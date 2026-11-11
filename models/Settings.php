<?php
require_once __DIR__ . '/../config/database.php';

class Settings {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function get($key, $default = null) {
        $stmt = $this->db->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ? $result['setting_value'] : $default;
    }
    
    public function set($key, $value, $description = null) {
        $stmt = $this->db->prepare("
            INSERT INTO settings (setting_key, setting_value, description) 
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE setting_value = ?, description = COALESCE(?, description)
        ");
        
        return $stmt->execute([$key, $value, $description, $value, $description]);
    }
    
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM settings ORDER BY setting_key");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function delete($key) {
        $stmt = $this->db->prepare("DELETE FROM settings WHERE setting_key = ?");
        return $stmt->execute([$key]);
    }
}
