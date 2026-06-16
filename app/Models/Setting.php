<?php

namespace App\Models;

use App\Core\Model;

class Setting extends Model {
    protected $table = 'system_settings';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'setting_key', 
        'setting_value',
        'created_at',
        'updated_at'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function get($key, $default = null) {
        $result = $this->where('setting_key', $key)->first();
        return $result ? $result['setting_value'] : $default;
    }

    public function set($key, $value) {
        $existing = $this->where('setting_key', $key)->first();
        
        if ($existing) {
            $sql = "UPDATE {$this->table} SET setting_value = :value, updated_at = NOW() WHERE setting_key = :key";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute(['value' => $value, 'key' => $key]);
        } else {
            return $this->insert([
                'setting_key' => $key,
                'setting_value' => $value
            ]);
        }
    }
    
    public function getAllSettings() {
        $settings = $this->findAll();
        $mapped = [];
        foreach ($settings as $s) {
            $mapped[$s['setting_key']] = $s['setting_value'];
        }
        return $mapped;
    }
}
