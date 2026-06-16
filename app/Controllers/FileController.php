<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\File;
use App\Services\AuditService;

class FileController extends Controller {
    private $fileModel;
    private $auditService;

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }
        
        $this->fileModel = new File();
        $this->auditService = AuditService::getInstance();
    }

    public function index() {
        $filters = $this->getFiltersFromRequest();
        $files = $this->fileModel->getFilesByUser($_SESSION['user_id'], $filters);
        $stats = $this->fileModel->getFileStats(['uploaded_by' => $_SESSION['user_id']]);
        
        $this->auditService->logView('file_management');
        
        return $this->view('admin/files/index', [
            'title' => 'File Management',
            'files' => $files,
            'stats' => $stats,
            'filters' => $filters
        ]);
    }

    public function upload() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->view('admin/files/upload', [
                'title' => 'Upload Files'
            ]);
        }

        // Verify CSRF token
        if (!csrf_verify()) {
            $_SESSION['error'] = 'Invalid security token. Please try again.';
            return $this->redirect('/admin/files');
        }

        if (!isset($_FILES['files'])) {
            $_SESSION['error'] = 'No files selected for upload.';
            return $this->redirect('/admin/files/upload');
        }

        $uploadedFiles = [];
        $errors = [];

        foreach ($_FILES['files']['name'] as $key => $filename) {
            if ($_FILES['files']['error'][$key] === UPLOAD_ERR_OK) {
                $fileData = [
                    'original_name' => $filename,
                    'filename' => $this->generateUniqueFilename($filename),
                    'category' => $_POST['category'] ?? 'other',
                    'description' => $_POST['description'] ?? '',
                    'project_id' => $_POST['project_id'] ?? null,
                    'client_id' => $_POST['client_id'] ?? null,
                    'is_public' => isset($_POST['is_public']),
                    'tags' => $this->parseTags($_POST['tags'] ?? '')
                ];

                // Set file path
                $uploadDir = APP_ROOT . '/public/uploads/files/' . date('Y/m');
                $fileData['file_path'] = $uploadDir . '/' . $fileData['filename'];
                $fileData['file_size'] = $_FILES['files']['size'][$key];
                $fileData['mime_type'] = $_FILES['files']['type'][$key];

                // Calculate file hash
                $tempPath = $_FILES['files']['tmp_name'][$key];
                $fileData['file_hash'] = $this->fileModel->calculateFileHash($tempPath);

                $uploadedFile = [
                    'name' => $fileData['filename'],
                    'tmp_name' => $tempPath,
                    'size' => $fileData['file_size']
                ];

                $result = $this->fileModel->uploadFile($fileData, $uploadedFile);

                if (is_numeric($result)) {
                    $uploadedFiles[] = $result;
                    
                    // Add to folder if specified
                    if (!empty($_POST['folder_id'])) {
                        $this->fileModel->addToFolder($result, $_POST['folder_id']);
                    }
                } else {
                    $errors[] = $result['error'] ?? 'Failed to upload ' . $filename;
                }
            } else {
                $errors[] = 'Error uploading ' . $filename . ': ' . $this->getUploadErrorMessage($_FILES['files']['error'][$key]);
            }
        }

        if (!empty($uploadedFiles)) {
            $_SESSION['success'] = count($uploadedFiles) . ' file(s) uploaded successfully.';
        }

        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
        }

        return $this->redirect('/admin/files');
    }

    public function view() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $_SESSION['error'] = 'File ID not provided.';
            return $this->redirect('/admin/files');
        }

        $file = $this->fileModel->getFileById($id);
        
        if (!$file) {
            $_SESSION['error'] = 'File not found.';
            return $this->redirect('/admin/files');
        }

        // Check permissions
        if (!$this->canAccessFile($file)) {
            $_SESSION['error'] = 'Access denied.';
            return $this->redirect('/admin/files');
        }

        $this->auditService->logView('file_details', 'file', $id);

        return $this->view('admin/files/view', [
            'title' => 'File Details - ' . $file['original_name'],
            'file' => $file,
            'versions' => $this->fileModel->getFileVersions($id),
            'sharedUsers' => $this->getSharedUsers($id)
        ]);
    }

    public function download() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $_SESSION['error'] = 'File ID not provided.';
            return $this->redirect('/admin/files');
        }

        $result = $this->fileModel->downloadFile($id, $_SESSION['user_id']);
        
        if (isset($result['error'])) {
            $_SESSION['error'] = $result['error'];
            return $this->redirect('/admin/files');
        }

        $file = $result;
        $filePath = $file['file_path'];

        if (!file_exists($filePath)) {
            $_SESSION['error'] = 'File not found on server.';
            return $this->redirect('/admin/files');
        }

        // Set headers for download
        header('Content-Type: ' . $file['mime_type']);
        header('Content-Disposition: attachment; filename="' . $file['original_name'] . '"');
        header('Content-Length: ' . $file['file_size']);
        header('Cache-Control: private, no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        readfile($filePath);
        exit;
    }

    public function edit() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $_SESSION['error'] = 'File ID not provided.';
            return $this->redirect('/admin/files');
        }

        $file = $this->fileModel->getFileById($id);
        
        if (!$file) {
            $_SESSION['error'] = 'File not found.';
            return $this->redirect('/admin/files');
        }

        // Check permissions (only owner can edit)
        if ($file['uploaded_by'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Access denied.';
            return $this->redirect('/admin/files');
        }

        return $this->view('admin/files/edit', [
            'title' => 'Edit File - ' . $file['original_name'],
            'file' => $file
        ]);
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirect('/admin/files');
        }

        $id = $_POST['id'] ?? null;
        if (!$id) {
            $_SESSION['error'] = 'File ID not provided.';
            return $this->redirect('/admin/files');
        }

        // Verify CSRF token
        if (!csrf_verify()) {
            $_SESSION['error'] = 'Invalid security token. Please try again.';
            return $this->redirect('/admin/files/edit?id=' . $id);
        }

        $file = $this->fileModel->getFileById($id);
        if (!$file || $file['uploaded_by'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Access denied.';
            return $this->redirect('/admin/files');
        }

        $updateData = [
            'description' => $_POST['description'] ?? '',
            'category' => $_POST['category'] ?? 'other',
            'is_public' => isset($_POST['is_public']),
            'tags' => $this->parseTags($_POST['tags'] ?? ''),
            'project_id' => $_POST['project_id'] ?? null,
            'client_id' => $_POST['client_id'] ?? null
        ];

        if ($this->fileModel->updateFile($id, $updateData)) {
            $_SESSION['success'] = 'File updated successfully.';
        } else {
            $_SESSION['error'] = 'Failed to update file.';
        }

        return $this->redirect('/admin/files/view?id=' . $id);
    }

    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirect('/admin/files');
        }

        $id = $_POST['id'] ?? null;
        if (!$id) {
            $_SESSION['error'] = 'File ID not provided.';
            return $this->redirect('/admin/files');
        }

        // Verify CSRF token
        if (!csrf_verify()) {
            $_SESSION['error'] = 'Invalid security token. Please try again.';
            return $this->redirect('/admin/files');
        }

        $file = $this->fileModel->getFileById($id);
        if (!$file || $file['uploaded_by'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Access denied.';
            return $this->redirect('/admin/files');
        }

        if ($this->fileModel->deleteFile($id)) {
            $_SESSION['success'] = 'File deleted successfully.';
        } else {
            $_SESSION['error'] = 'Failed to delete file.';
        }

        return $this->redirect('/admin/files');
    }

    public function share() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirect('/admin/files');
        }

        $fileId = $_POST['file_id'] ?? null;
        $sharedWith = $_POST['shared_with'] ?? null;
        $permission = $_POST['permission'] ?? 'view';
        $expiresAt = $_POST['expires_at'] ?? null;

        if (!$fileId || !$sharedWith) {
            $_SESSION['error'] = 'File ID and user to share with are required.';
            return $this->redirect('/admin/files');
        }

        // Verify CSRF token
        if (!csrf_verify()) {
            $_SESSION['error'] = 'Invalid security token. Please try again.';
            return $this->redirect('/admin/files');
        }

        $file = $this->fileModel->getFileById($fileId);
        if (!$file || $file['uploaded_by'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Access denied.';
            return $this->redirect('/admin/files');
        }

        if ($this->fileModel->shareFile($fileId, $sharedWith, $permission, $expiresAt)) {
            $_SESSION['success'] = 'File shared successfully.';
        } else {
            $_SESSION['error'] = 'Failed to share file.';
        }

        return $this->redirect('/admin/files/view?id=' . $fileId);
    }

    public function shared() {
        $sharedFiles = $this->fileModel->getSharedFiles($_SESSION['user_id']);
        
        $this->auditService->logView('shared_files');
        
        return $this->view('admin/files/shared', [
            'title' => 'Shared Files',
            'files' => $sharedFiles
        ]);
    }

    public function search() {
        $query = $_GET['q'] ?? '';
        $filters = $this->getFiltersFromRequest();
        
        if (empty($query)) {
            return $this->redirect('/admin/files');
        }

        $files = $this->fileModel->searchFiles($query, $filters);
        
        $this->auditService->logView('file_search');
        
        return $this->view('admin/files/search', [
            'title' => 'Search Results',
            'files' => $files,
            'query' => $query,
            'filters' => $filters
        ]);
    }

    public function createVersion() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirect('/admin/files');
        }

        $fileId = $_POST['file_id'] ?? null;
        $changeDescription = $_POST['change_description'] ?? '';

        if (!$fileId) {
            $_SESSION['error'] = 'File ID not provided.';
            return $this->redirect('/admin/files');
        }

        // Verify CSRF token
        if (!csrf_verify()) {
            $_SESSION['error'] = 'Invalid security token. Please try again.';
            return $this->redirect('/admin/files');
        }

        $file = $this->fileModel->getFileById($fileId);
        if (!$file || $file['uploaded_by'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Access denied.';
            return $this->redirect('/admin/files');
        }

        if (!isset($_FILES['version_file'])) {
            $_SESSION['error'] = 'No file selected for new version.';
            return $this->redirect('/admin/files/view?id=' . $fileId);
        }

        $uploadedFile = $_FILES['version_file'];
        if ($uploadedFile['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Error uploading file: ' . $this->getUploadErrorMessage($uploadedFile['error']);
            return $this->redirect('/admin/files/view?id=' . $fileId);
        }

        $version = $this->fileModel->createVersion($fileId, $uploadedFile, $changeDescription);
        
        if ($version) {
            $_SESSION['success'] = 'New version created successfully (Version ' . $version . ').';
        } else {
            $_SESSION['error'] = 'Failed to create new version.';
        }

        return $this->redirect('/admin/files/view?id=' . $fileId);
    }

    public function stats() {
        $stats = $this->fileModel->getFileStats();
        $storageUsage = $this->fileModel->getStorageUsage();
        $recentActivity = $this->fileModel->getRecentActivity();
        
        $this->auditService->logView('file_stats');
        
        return $this->view('admin/files/stats', [
            'title' => 'File Statistics',
            'stats' => $stats,
            'storageUsage' => $storageUsage,
            'recentActivity' => $recentActivity
        ]);
    }

    // Helper methods
    private function generateUniqueFilename($filename) {
        $pathInfo = pathinfo($filename);
        $extension = $pathInfo['extension'] ?? '';
        $basename = $pathInfo['filename'];
        
        // Clean the filename
        $basename = preg_replace('/[^a-zA-Z0-9._-]/', '', $basename);
        
        // Generate unique filename
        $uniqueFilename = $basename . '_' . time() . '_' . uniqid();
        
        return $uniqueFilename . ($extension ? '.' . $extension : '');
    }

    private function parseTags($tagsString) {
        if (empty($tagsString)) {
            return [];
        }
        
        $tags = array_map('trim', explode(',', $tagsString));
        return array_filter($tags, function($tag) {
            return !empty($tag);
        });
    }

    private function getFiltersFromRequest() {
        return [
            'category' => $_GET['category'] ?? null,
            'search' => $_GET['search'] ?? null,
            'limit' => $_GET['limit'] ?? null
        ];
    }

    private function canAccessFile($file) {
        // Owner can always access
        if ($file['uploaded_by'] == $_SESSION['user_id']) {
            return true;
        }

        // Admin can access all files
        if ($_SESSION['role'] === 'admin') {
            return true;
        }

        // Public files can be accessed by anyone
        if ($file['is_public']) {
            return true;
        }

        // Check if file is shared with user
        return $this->fileModel->canAccessFile($file, $_SESSION['user_id']);
    }

    private function getSharedUsers($fileId) {
        $stmt = $this->pdo->prepare("
            SELECT fs.*, u.full_name, u.email
            FROM file_shares fs
            JOIN users u ON fs.shared_with = u.id
            WHERE fs.file_id = :file_id
            AND (fs.expires_at IS NULL OR fs.expires_at > NOW())
        ");
        $stmt->execute(['file_id' => $fileId]);
        return $stmt->fetchAll();
    }

    private function getUploadErrorMessage($errorCode) {
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
            UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
            UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload'
        ];
        
        return $errors[$errorCode] ?? 'Unknown upload error';
    }
}
