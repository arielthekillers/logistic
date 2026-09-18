<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class SettingsModel extends Model {
    protected $table = 'settings';
    
    // We don't have an auto-incrementing 'id', primary key is 'key_name'
    protected $primaryKey = 'key_name';

    public function getAllSettings() {
        $sql = "SELECT key_name, key_value FROM settings";
        $stmt = $this->db->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['key_name']] = $row['key_value'];
        }
        return $settings;
    }

    public function set($key, $value) {
        $sql = "INSERT INTO settings (key_name, key_value) VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE key_value = VALUES(key_value)";
        // For sqlite/mysql compatibility we might need standard upsert or check first
        // Let's do a safe cross-db upsert
        $existing = $this->where('key_name', $key);
        if (count($existing) > 0) {
            $sql = "UPDATE settings SET key_value = ? WHERE key_name = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$value, $key]);
        } else {
            $sql = "INSERT INTO settings (key_name, key_value) VALUES (?, ?)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$key, $value]);
        }
    }
}
