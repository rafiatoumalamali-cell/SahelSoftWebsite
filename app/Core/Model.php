<?php

namespace App\Core;

abstract class Model {
    protected $db;
    protected $pdo;
    protected $table;
    protected $primaryKey = 'id';
    protected $allowedFields = [];
    protected $useTimestamps = false;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    // Query Builder parts
    protected $wheres = [];
    protected $orders = [];
    protected $limit = null;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->pdo = $this->db->getConnection();
    }

    // Basic Query Builder
    public function where($column, $value) {
        $this->wheres[] = [$column, $value];
        return $this;
    }

    public function orderBy($column, $direction = 'ASC') {
        $this->orders[] = "$column $direction";
        return $this;
    }

    public function find($id) {
        return $this->where($this->primaryKey, $id)->first();
    }

    public function first() {
        $this->limit = 1;
        $sql = $this->buildSelect();
        $stmt = $this->pdo->prepare($sql);
        $this->bindValues($stmt);
        $stmt->execute();
        $this->resetQuery();
        return $stmt->fetch();
    }

    public function count() {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        if (!empty($this->wheres)) {
            $clauses = [];
            foreach ($this->wheres as $where) {
                $clauses[] = "{$where[0]} = :{$where[0]}";
            }
            $sql .= " WHERE " . implode(' AND ', $clauses);
        }
        $stmt = $this->pdo->prepare($sql);
        $this->bindValues($stmt);
        $stmt->execute();
        $this->resetQuery();
        $result = $stmt->fetch();
        return $result ? (int)$result['total'] : 0;
    }

    public function findAll() {
        $sql = $this->buildSelect();
        $stmt = $this->pdo->prepare($sql);
        $this->bindValues($stmt);
        $stmt->execute();
        $this->resetQuery();
        return $stmt->fetchAll();
    }

    public function insert($data) {
        // Filter data based on allowedFields
        $data = array_intersect_key($data, array_flip($this->allowedFields));
        
        if ($this->useTimestamps) {
            $now = date('Y-m-d H:i:s');
            $data[$this->createdField] = $now;
            $data[$this->updatedField] = $now;
        }

        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        
        return $stmt->execute();
    }

    public function update($id, $data) {
        // Filter data based on allowedFields
        $allowed = $this->allowedFields;
        $updates = [];
        $params = ['id' => $id];
        
        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $updates[] = "$field = :$field";
                $params[$field] = $data[$field];
            }
        }
        
        if (empty($updates)) {
            return true; // Nothing to update
        }

        if ($this->useTimestamps) {
            $updates[] = "{$this->updatedField} = :{$this->updatedField}";
            $params[$this->updatedField] = date('Y-m-d H:i:s');
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $updates) . " WHERE {$this->primaryKey} = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    // Helpers
    protected function buildSelect() {
        $sql = "SELECT * FROM {$this->table}";
        
        if (!empty($this->wheres)) {
            $clauses = [];
            foreach ($this->wheres as $where) {
                $clauses[] = "{$where[0]} = :{$where[0]}";
            }
            $sql .= " WHERE " . implode(' AND ', $clauses);
        }
        
        if (!empty($this->orders)) {
            $sql .= " ORDER BY " . implode(', ', $this->orders);
        }
        
        if ($this->limit) {
            $sql .= " LIMIT {$this->limit}";
        }
        
        return $sql;
    }

    protected function bindValues($stmt) {
        foreach ($this->wheres as $where) {
            $stmt->bindValue(":{$where[0]}", $where[1]);
        }
    }

    protected function resetQuery() {
        $this->wheres = [];
        $this->orders = [];
        $this->limit = null;
    }
}
