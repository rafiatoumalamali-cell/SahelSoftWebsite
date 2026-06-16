<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class ContentPage extends Model {
    protected $table = 'content_pages';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'page_key',
        'title',
        'content',
        'meta_description',
        'meta_keywords',
        'status',
        'last_edited_by',
        'created_at',
        'updated_at'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getPageByKey($key) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE page_key = :key");
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $stmt->execute(['key' => $key]);
        return $stmt->fetch();
    }

    public function getPublishedPageByKey($key) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE page_key = :key AND status = 'published'");
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $stmt->execute(['key' => $key]);
        return $stmt->fetch();
    }

    public function getAllPages() {
        $stmt = $this->pdo->query("SELECT cp.*, u.full_name as editor_name FROM {$this->table} cp LEFT JOIN users u ON cp.last_edited_by = u.id ORDER BY cp.updated_at DESC");
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $result = $stmt->fetchAll();
        return is_array($result) ? $result : [];
    }

    public function getPublishedPages() {
        $stmt = $this->pdo->query("SELECT * FROM {$this->table} WHERE status = 'published' ORDER BY page_key ASC");
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $result = $stmt->fetchAll();
        return is_array($result) ? $result : [];
    }

    public function updatePage($key, $data, $userId) {
        $data['last_edited_by'] = $userId;
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        return $this->updateWhere('page_key', $key, $data);
    }

    public function createPage($data, $userId) {
        $data['last_edited_by'] = $userId;
        
        return $this->insert($data);
    }

    public function deletePage($key) {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE page_key = :key");
        return $stmt->execute(['key' => $key]);
    }

    public function publishPage($key, $userId) {
        $sql = "UPDATE {$this->table} SET status = 'published', last_edited_by = :user_id, updated_at = NOW() WHERE page_key = :key";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['key' => $key, 'user_id' => $userId]);
    }

    public function unpublishPage($key, $userId) {
        $sql = "UPDATE {$this->table} SET status = 'draft', last_edited_by = :user_id, updated_at = NOW() WHERE page_key = :key";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['key' => $key, 'user_id' => $userId]);
    }

    public function getRecentChanges($limit = 10) {
        $stmt = $this->pdo->prepare("SELECT cp.*, u.full_name as editor_name FROM {$this->table} cp LEFT JOIN users u ON cp.last_edited_by = u.id ORDER BY cp.updated_at DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return is_array($result) ? $result : [];
    }

    public function searchPages($query) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE title LIKE :query OR content LIKE :query OR meta_description LIKE :query ORDER BY title ASC");
        $searchTerm = '%' . $query . '%';
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $stmt->execute(['query' => $searchTerm]);
        $result = $stmt->fetchAll();
        return is_array($result) ? $result : [];
    }

    // Helper method to update where condition
    private function updateWhere($field, $value, $data) {
        $allowed = $this->allowedFields;
        $updates = [];
        $params = [$field => $value];
        
        foreach ($allowed as $col) {
            if (array_key_exists($col, $data)) {
                $updates[] = "$col = :$col";
                $params[$col] = $data[$col];
            }
        }
        
        if (empty($updates)) {
            return true;
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $updates) . " WHERE $field = :$field";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }
}
