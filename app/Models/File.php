<?php

namespace App\Models;

use App\Core\Model;

class File extends Model {
    protected $table = 'files';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'filename',
        'original_name',
        'file_path',
        'file_size',
        'mime_type',
        'file_hash',
        'uploaded_by',
        'project_id',
        'client_id',
        'proposal_id',
        'invoice_id',
        'category',
        'description',
        'tags',
        'is_public',
        'download_count',
        'last_accessed',
        'created_at',
        'updated_at'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function uploadFile($fileData, $uploadedFile) {
        // Check for duplicate file by hash
        if (!empty($fileData['file_hash'])) {
            $existingFile = $this->getByHash($fileData['file_hash']);
            if ($existingFile) {
                return ['error' => 'File already exists', 'existing_file' => $existingFile];
            }
        }

        $fileData['uploaded_by'] = $_SESSION['user_id'] ?? null;
        $fileData['download_count'] = 0;
        $fileData['tags'] = json_encode($fileData['tags'] ?? []);

        $fileId = $this->insert($fileData);
        
        if ($fileId) {
            // Log upload activity
            $this->logActivity($fileId, 'uploaded', $fileData);
            
            // Move file to permanent location
            if (!empty($uploadedFile['tmp_name'])) {
                $this->moveUploadedFile($uploadedFile['tmp_name'], $fileData['file_path']);
            }
        }

        return $fileId;
    }

    public function getFileById($id) {
        $stmt = $this->pdo->prepare("
            SELECT f.*, 
                   u.full_name as uploaded_by_name,
                   p.title as project_title,
                   cl.full_name as client_name,
                   pr.title as proposal_title,
                   inv.invoice_number
            FROM {$this->table} f
            LEFT JOIN users u ON f.uploaded_by = u.id
            LEFT JOIN projects p ON f.project_id = p.id
            LEFT JOIN users cl ON f.client_id = cl.id
            LEFT JOIN proposals pr ON f.proposal_id = pr.id
            LEFT JOIN invoices inv ON f.invoice_id = inv.id
            WHERE f.id = :id
        ");
        $stmt->execute(['id' => $id]);
        $file = $stmt->fetch();
        
        if ($file) {
            $file['tags'] = json_decode($file['tags'], true) ?? [];
            $file['folders'] = $this->getFileFolders($id);
        }
        
        return $file;
    }

    public function getFilesByUser($userId, $filters = []) {
        $sql = "
            SELECT f.*, 
                   u.full_name as uploaded_by_name,
                   p.title as project_title,
                   cl.full_name as client_name
            FROM {$this->table} f
            LEFT JOIN users u ON f.uploaded_by = u.id
            LEFT JOIN projects p ON f.project_id = p.id
            LEFT JOIN users cl ON f.client_id = cl.id
            WHERE f.uploaded_by = :user_id
        ";
        
        $params = ['user_id' => $userId];
        
        if (!empty($filters['category'])) {
            $sql .= " AND f.category = :category";
            $params['category'] = $filters['category'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (f.original_name LIKE :search OR f.description LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }
        
        $sql .= " ORDER BY f.created_at DESC";
        
        if (!empty($filters['limit'])) {
            $sql .= " LIMIT :limit";
            $params['limit'] = $filters['limit'];
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $files = $stmt->fetchAll();
        
        foreach ($files as &$file) {
            $file['tags'] = json_decode($file['tags'], true) ?? [];
            $file['folders'] = $this->getFileFolders($file['id']);
        }
        
        return $files;
    }

    public function getFilesByProject($projectId) {
        $stmt = $this->pdo->prepare("
            SELECT f.*, u.full_name as uploaded_by_name
            FROM {$this->table} f
            LEFT JOIN users u ON f.uploaded_by = u.id
            WHERE f.project_id = :project_id
            ORDER BY f.created_at DESC
        ");
        $stmt->execute(['project_id' => $projectId]);
        $files = $stmt->fetchAll();
        
        foreach ($files as &$file) {
            $file['tags'] = json_decode($file['tags'], true) ?? [];
        }
        
        return $files;
    }

    public function getFilesByClient($clientId) {
        $stmt = $this->pdo->prepare("
            SELECT f.*, 
                   u.full_name as uploaded_by_name,
                   p.title as project_title
            FROM {$this->table} f
            LEFT JOIN users u ON f.uploaded_by = u.id
            LEFT JOIN projects p ON f.project_id = p.id
            WHERE f.client_id = :client_id OR f.is_public = TRUE
            ORDER BY f.created_at DESC
        ");
        $stmt->execute(['client_id' => $clientId]);
        $files = $stmt->fetchAll();
        
        foreach ($files as &$file) {
            $file['tags'] = json_decode($file['tags'], true) ?? [];
        }
        
        return $files;
    }

    public function getPublicFiles() {
        $stmt = $this->pdo->prepare("
            SELECT f.*, u.full_name as uploaded_by_name
            FROM {$this->table} f
            LEFT JOIN users u ON f.uploaded_by = u.id
            WHERE f.is_public = TRUE
            ORDER BY f.created_at DESC
        ");
        $stmt->execute();
        $files = $stmt->fetchAll();
        
        foreach ($files as &$file) {
            $file['tags'] = json_decode($file['tags'], true) ?? [];
        }
        
        return $files;
    }

    public function searchFiles($query, $filters = []) {
        $sql = "
            SELECT f.*, 
                   u.full_name as uploaded_by_name,
                   p.title as project_title,
                   cl.full_name as client_name
            FROM {$this->table} f
            LEFT JOIN users u ON f.uploaded_by = u.id
            LEFT JOIN projects p ON f.project_id = p.id
            LEFT JOIN users cl ON f.client_id = cl.id
            WHERE (f.original_name LIKE :query OR f.description LIKE :query OR f.filename LIKE :query)
        ";
        
        $params = ['query' => '%' . $query . '%'];
        
        if (!empty($filters['category'])) {
            $sql .= " AND f.category = :category";
            $params['category'] = $filters['category'];
        }
        
        if (!empty($filters['uploaded_by'])) {
            $sql .= " AND f.uploaded_by = :uploaded_by";
            $params['uploaded_by'] = $filters['uploaded_by'];
        }
        
        $sql .= " ORDER BY f.created_at DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $files = $stmt->fetchAll();
        
        foreach ($files as &$file) {
            $file['tags'] = json_decode($file['tags'], true) ?? [];
        }
        
        return $files;
    }

    public function updateFile($id, $data) {
        if (isset($data['tags'])) {
            $data['tags'] = json_encode($data['tags']);
        }
        
        $result = $this->update($id, $data);
        
        if ($result) {
            $this->logActivity($id, 'updated', $data);
        }
        
        return $result;
    }

    public function deleteFile($id) {
        $file = $this->getFileById($id);
        
        if (!$file) {
            return false;
        }

        // Log deletion
        $this->logActivity($id, 'deleted', $file);

        // Delete physical file
        if (file_exists($file['file_path'])) {
            unlink($file['file_path']);
        }

        // Delete from database
        return $this->delete($id);
    }

    public function downloadFile($id, $userId = null) {
        $file = $this->getFileById($id);
        
        if (!$file) {
            return ['error' => 'File not found'];
        }

        // Check permissions
        if (!$this->canAccessFile($file, $userId)) {
            return ['error' => 'Access denied'];
        }

        // Update download count
        $this->incrementDownloadCount($id);
        
        // Log download
        $this->logActivity($id, 'downloaded', ['user_id' => $userId]);

        // Update last accessed
        $this->update($id, ['last_accessed' => date('Y-m-d H:i:s')]);

        return $file;
    }

    public function shareFile($fileId, $sharedWith, $permission = 'view', $expiresAt = null) {
        $sql = "INSERT INTO file_shares (file_id, shared_with, shared_by, permission, expires_at) 
                VALUES (:file_id, :shared_with, :shared_by, :permission, :expires_at)";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'file_id' => $fileId,
            'shared_with' => $sharedWith,
            'shared_by' => $_SESSION['user_id'],
            'permission' => $permission,
            'expires_at' => $expiresAt
        ]);
    }

    public function getSharedFiles($userId) {
        $stmt = $this->pdo->prepare("
            SELECT fs.*, f.*, u.full_name as shared_by_name
            FROM file_shares fs
            JOIN files f ON fs.file_id = f.id
            JOIN users u ON fs.shared_by = u.id
            WHERE fs.shared_with = :user_id 
            AND (fs.expires_at IS NULL OR fs.expires_at > NOW())
            ORDER BY fs.created_at DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        
        $sharedFiles = $stmt->fetchAll();
        
        foreach ($sharedFiles as &$file) {
            $file['tags'] = json_decode($file['tags'], true) ?? [];
        }
        
        return $sharedFiles;
    }

    public function getFileStats($filters = []) {
        $sql = "
            SELECT 
                COUNT(*) as total_files,
                SUM(file_size) as total_size,
                AVG(file_size) as avg_size,
                COUNT(DISTINCT category) as categories_used,
                SUM(download_count) as total_downloads
            FROM {$this->table}
            WHERE 1=1
        ";
        
        $params = [];
        
        if (!empty($filters['uploaded_by'])) {
            $sql .= " AND uploaded_by = :uploaded_by";
            $params['uploaded_by'] = $filters['uploaded_by'];
        }
        
        if (!empty($filters['category'])) {
            $sql .= " AND category = :category";
            $params['category'] = $filters['category'];
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    public function getStorageUsage() {
        $stmt = $this->pdo->prepare("
            SELECT 
                category,
                COUNT(*) as file_count,
                SUM(file_size) as total_size,
                AVG(file_size) as avg_size
            FROM {$this->table}
            GROUP BY category
            ORDER BY total_size DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getRecentActivity($limit = 20) {
        $stmt = $this->pdo->prepare("
            SELECT fal.*, f.original_name, f.category,
                   u.full_name as user_name
            FROM file_activity_log fal
            JOIN files f ON fal.file_id = f.id
            JOIN users u ON fal.user_id = u.id
            ORDER BY fal.created_at DESC
            LIMIT :limit
        ");
        $stmt->execute(['limit' => $limit]);
        return $stmt->fetchAll();
    }

    public function createVersion($fileId, $uploadedFile, $changeDescription = '') {
        // Get current file info
        $currentFile = $this->getFileById($fileId);
        if (!$currentFile) {
            return false;
        }

        // Get current version number
        $stmt = $this->pdo->prepare("SELECT MAX(version_number) as max_version FROM file_versions WHERE file_id = :file_id");
        $stmt->execute(['file_id' => $fileId]);
        $result = $stmt->fetch();
        $newVersion = ($result['max_version'] ?? 0) + 1;

        // Create new version record
        $versionData = [
            'file_id' => $fileId,
            'version_number' => $newVersion,
            'filename' => $uploadedFile['name'],
            'file_path' => $this->generateVersionPath($currentFile['file_path'], $newVersion),
            'file_size' => $uploadedFile['size'],
            'uploaded_by' => $_SESSION['user_id'],
            'change_description' => $changeDescription
        ];

        $stmt = $this->pdo->prepare("
            INSERT INTO file_versions (file_id, version_number, filename, file_path, file_size, uploaded_by, change_description) 
            VALUES (:file_id, :version_number, :filename, :file_path, :file_size, :uploaded_by, :change_description)
        ");
        
        if ($stmt->execute($versionData)) {
            // Move new version file
            $this->moveUploadedFile($uploadedFile['tmp_name'], $versionData['file_path']);
            
            // Update main file record
            $this->update($fileId, [
                'file_path' => $versionData['file_path'],
                'file_size' => $uploadedFile['size'],
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            // Log version creation
            $this->logActivity($fileId, 'version_created', [
                'version_number' => $newVersion,
                'change_description' => $changeDescription
            ]);
            
            return $newVersion;
        }
        
        return false;
    }

    public function getFileVersions($fileId) {
        $stmt = $this->pdo->prepare("
            SELECT fv.*, u.full_name as uploaded_by_name
            FROM file_versions fv
            LEFT JOIN users u ON fv.uploaded_by = u.id
            WHERE fv.file_id = :file_id
            ORDER BY fv.version_number DESC
        ");
        $stmt->execute(['file_id' => $fileId]);
        return $stmt->fetchAll();
    }

    // Helper methods
    private function getByHash($hash) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE file_hash = :hash");
        $stmt->execute(['hash' => $hash]);
        return $stmt->fetch();
    }

    private function moveUploadedFile($tmpName, $destination) {
        $directory = dirname($destination);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        return move_uploaded_file($tmpName, $destination);
    }

    private function generateVersionPath($originalPath, $version) {
        $pathInfo = pathinfo($originalPath);
        return $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_v' . $version . '.' . $pathInfo['extension'];
    }

    private function canAccessFile($file, $userId) {
        // Owner can always access
        if ($file['uploaded_by'] == $userId) {
            return true;
        }

        // Public files can be accessed by anyone
        if ($file['is_public']) {
            return true;
        }

        // Check if file is shared with user
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) as count FROM file_shares 
            WHERE file_id = :file_id AND shared_with = :user_id 
            AND (expires_at IS NULL OR expires_at > NOW())
        ");
        $stmt->execute(['file_id' => $file['id'], 'user_id' => $userId]);
        $result = $stmt->fetch();
        
        return $result['count'] > 0;
    }

    private function incrementDownloadCount($fileId) {
        $stmt = $this->pdo->prepare("UPDATE {$this->table} SET download_count = download_count + 1 WHERE id = :id");
        return $stmt->execute(['id' => $fileId]);
    }

    private function logActivity($fileId, $action, $details = null) {
        $stmt = $this->pdo->prepare("
            INSERT INTO file_activity_log (file_id, user_id, action, details, ip_address, user_agent) 
            VALUES (:file_id, :user_id, :action, :details, :ip_address, :user_agent)
        ");
        
        return $stmt->execute([
            'file_id' => $fileId,
            'user_id' => $_SESSION['user_id'] ?? null,
            'action' => $action,
            'details' => json_encode($details),
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
        ]);
    }

    private function getFileFolders($fileId) {
        $stmt = $this->pdo->prepare("
            SELECT f.* FROM file_folders f
            JOIN file_folder_relations fr ON f.id = fr.folder_id
            WHERE fr.file_id = :file_id
        ");
        $stmt->execute(['file_id' => $fileId]);
        return $stmt->fetchAll();
    }

    public function addToFolder($fileId, $folderId) {
        $stmt = $this->pdo->prepare("
            INSERT IGNORE INTO file_folder_relations (file_id, folder_id) 
            VALUES (:file_id, :folder_id)
        ");
        return $stmt->execute(['file_id' => $fileId, 'folder_id' => $folderId]);
    }

    public function removeFromFolder($fileId, $folderId) {
        $stmt = $this->pdo->prepare("
            DELETE FROM file_folder_relations 
            WHERE file_id = :file_id AND folder_id = :folder_id
        ");
        return $stmt->execute(['file_id' => $fileId, 'folder_id' => $folderId]);
    }

    public function calculateFileHash($filePath) {
        return hash_file('sha256', $filePath);
    }

    public function formatFileSize($bytes) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
