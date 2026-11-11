<?php
require_once __DIR__ . '/../config/database.php';

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function create($email, $password, $first_name, $last_name, $phone = null) {
        $sql = "INSERT INTO users (email, password, first_name, last_name, phone, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())";
        
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$email, $hashedPassword, $first_name, $last_name, $phone]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("User creation failed: " . $e->getMessage());
            return false;
        }
    }
    
    public function findByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
    
    public function findById($id) {
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function verifyPassword($email, $password) {
        $user = $this->findByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
    
    public function updatePremiumStatus($userId, $isPremium) {
        $sql = "UPDATE users SET is_premium = ?, premium_activated_at = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$isPremium ? 1 : 0, $userId]);
    }
    
    public function isPremium($userId) {
        $user = $this->findById($userId);
        return $user && $user['is_premium'] == 1;
    }
    
    // Admin analytics methods
    public function getTotalCount() {
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch();
        return $result['count'];
    }
    
    public function getPremiumCount() {
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM users WHERE is_premium = 1");
        $result = $stmt->fetch();
        return $result['count'];
    }
    
    public function getCountByDate($date) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM users WHERE DATE(created_at) = ?");
        $stmt->execute([$date]);
        $result = $stmt->fetch();
        return $result['count'];
    }
    
    public function getCountByDateRange($startDate, $endDate) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM users WHERE DATE(created_at) BETWEEN ? AND ?");
        $stmt->execute([$startDate, $endDate]);
        $result = $stmt->fetch();
        return $result['count'];
    }
    
    public function getRecent($limit = 10) {
        $stmt = $this->db->prepare("SELECT * FROM users ORDER BY created_at DESC LIMIT ?");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    public function getAll($limit = 100, $offset = 0) {
        $stmt = $this->db->prepare("SELECT * FROM users ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }
    
    public function updateAdmin($userId, $isAdmin) {
        $stmt = $this->db->prepare("UPDATE users SET is_admin = ? WHERE id = ?");
        return $stmt->execute([$isAdmin ? 1 : 0, $userId]);
    }
}
