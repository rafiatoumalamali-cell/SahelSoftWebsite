<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class User extends Model {
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'full_name', 
        'email', 
        'password_hash', 
        'password_salt',
        'phone', 
        'company_name', 
        'role',
        'created_at',
        'updated_at'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function findByEmail($email) {
        return $this->where('email', $email)->first();
    }

    // Overriding create to use the Model's save/insert method if preferred, 
    // but sustaining manual method for now to match controller usage OR refactoring controller later.
    // Let's keep it compatible but use the Model features.
    public function create($data) {
        // Generate a random salt for the password
        $passwordSalt = bin2hex(random_bytes(32));
        
        $userData = [
            'full_name'     => $data['full_name'],
            'email'         => $data['email'],
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'password_salt' => $passwordSalt,
            'role'          => $data['role'] ?? 'client',
            'phone'         => $data['phone'] ?? null,
            'company_name'  => $data['company_name'] ?? null
        ];
        
        return $this->insert($userData);
    }

    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    public function getAllUsers() {
        return $this->orderBy('created_at', 'DESC')->findAll();
    }
    public function update($id, $data) {
        // Filter out fields that shouldn't be updated directly or handle password separately if needed
        $allowed = ['full_name', 'email', 'phone', 'company_name', 'role'];
        $updates = [];
        $params = ['id' => $id];
        
        foreach ($allowed as $field) {
            if (isset($data[$field])) {
                $updates[] = "$field = :$field";
                $params[$field] = $data[$field];
            }
        }
        
        // Handle password update separately if provided
        if (!empty($data['password'])) {
            $passwordSalt = bin2hex(random_bytes(32));
            $updates[] = "password_hash = :password_hash";
            $updates[] = "password_salt = :password_salt";
            $params['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            $params['password_salt'] = $passwordSalt;
        }
        
        if (empty($updates)) {
            return false;
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $updates) . " WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($id) {
        // Delete associated projects first to avoid FK constraint violation
        $sqlProjects = "DELETE FROM projects WHERE client_id = :id";
        $stmtProjects = $this->pdo->prepare($sqlProjects);
        $stmtProjects->execute(['id' => $id]);

        // Now delete the user
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function getVisibleContacts($userId, $role) {
        if ($role === 'admin') {
            // Admins see everyone
            $stmt = $this->pdo->prepare("SELECT id, full_name, role, avatar FROM users WHERE id != :id AND is_active = 1 ORDER BY full_name ASC");
            $stmt->execute(['id' => $userId]);
            return $stmt->fetchAll();
        }

        if ($role === 'developer' || $role === 'project_manager') {
            // Developers see:
            // 1. All other internal staff (admins, managers, developers)
            // 2. Clients of projects they are assigned to (via tasks)
            $sql = "SELECT DISTINCT u.id, u.full_name, u.role, u.avatar 
                    FROM users u 
                    WHERE u.id != :id AND u.is_active = 1 AND (
                        u.role IN ('admin', 'project_manager', 'developer')
                        OR u.id IN (
                            SELECT p.client_id 
                            FROM projects p 
                            JOIN project_tasks pt ON p.id = pt.project_id 
                            WHERE pt.assigned_to = :id
                            OR p.project_manager_id = :id
                        )
                    )
                    ORDER BY u.full_name ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $userId]);
            return $stmt->fetchAll();
        }

        if ($role === 'client') {
            // Clients see:
            // 1. All Admins
            // 2. The Project Manager of their projects
            // 3. Developers assigned to their projects
            $sql = "SELECT DISTINCT u.id, u.full_name, u.role, u.avatar 
                    FROM users u 
                    WHERE u.id != :id AND u.is_active = 1 AND (
                        u.role = 'admin'
                        OR u.id IN (
                            SELECT p.project_manager_id FROM projects p WHERE p.client_id = :id
                        )
                        OR u.id IN (
                            SELECT pt.assigned_to 
                            FROM project_tasks pt 
                            JOIN projects p ON pt.project_id = p.id 
                            WHERE p.client_id = :id
                        )
                    )
                    ORDER BY u.full_name ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $userId]);
            return $stmt->fetchAll();
        }

        return [];
    }
}
