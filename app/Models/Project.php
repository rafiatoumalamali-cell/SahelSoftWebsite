<?php

namespace App\Models;

use App\Core\Model;

class Project extends Model {
    protected $table = 'projects';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'client_id', 'title', 'description', 'budget', 'progress', 
        'best_case_completion', 'worst_case_completion',
        'status', 'start_date', 'deadline', 'category', 'tags', 'live_url', 'demo_url', 'image_path',
        'dashboard_img', 'product_page_img', 'admin_panel_img',
        'problem', 'solution', 'results_impact',
        'created_at', 'updated_at'
    ];
    protected $useTimestamps = true;

    public function getProjectsByClient($clientId) {
        $stmt = $this->pdo->prepare("SELECT * FROM projects WHERE client_id = :client_id ORDER BY created_at DESC");
        $stmt->execute(['client_id' => $clientId]);
        return $stmt->fetchAll();
    }

    public function getProjectById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM projects WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function getAllProjects() {
        $stmt = $this->pdo->query("SELECT p.*, u.full_name as client_name FROM projects p JOIN users u ON p.client_id = u.id ORDER BY p.created_at DESC");
        return $stmt->fetchAll();
    }

    public function getPublicProjects() {
        $stmt = $this->pdo->query("SELECT p.*, u.full_name as client_name FROM projects p LEFT JOIN users u ON p.client_id = u.id ORDER BY p.created_at DESC");
        return $stmt->fetchAll();
    }

    public function update($id, $data) {
        $allowed = $this->allowedFields;
        $updates = [];
        $params = ['id' => $id];
        
        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) { // Use array_key_exists to allow setting null
                $updates[] = "$field = :$field";
                $params[$field] = $data[$field];
            }
        }
        
        if (empty($updates)) {
            return true; // Nothing to update
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $updates) . " WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
