<?php
require_once __DIR__ . '/../config/database.php';

class Payment {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function create($userId, $amount, $currency, $type, $referenceNumber, $status = 'PENDING') {
        $sql = "INSERT INTO payments (user_id, amount, currency, payment_type, reference_number, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId, $amount, $currency, $type, $referenceNumber, $status]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Payment creation failed: " . $e->getMessage());
            return false;
        }
    }
    
    public function updateStatus($referenceNumber, $status, $transactionId = null, $metadata = null) {
        $sql = "UPDATE payments SET status = ?, transaction_id = ?, metadata = ?, updated_at = NOW() 
                WHERE reference_number = ?";
        
        $metadataJson = $metadata ? json_encode($metadata) : null;
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$status, $transactionId, $metadataJson, $referenceNumber]);
        } catch (PDOException $e) {
            error_log("Payment update failed: " . $e->getMessage());
            return false;
        }
    }
    
    public function findByReference($referenceNumber) {
        $sql = "SELECT * FROM payments WHERE reference_number = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$referenceNumber]);
        return $stmt->fetch();
    }
    
    public function findByUser($userId, $limit = 10) {
        $sql = "SELECT * FROM payments WHERE user_id = ? ORDER BY created_at DESC LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    }
    
    public function getSuccessfulPayments($userId) {
        $sql = "SELECT * FROM payments WHERE user_id = ? AND status = 'COMPLETE' ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    // Admin analytics methods
    public function getTotalRevenue() {
        $stmt = $this->db->query("SELECT SUM(amount) as total FROM payments WHERE status = 'COMPLETE'");
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }
    
    public function getRevenueByDate($date) {
        $stmt = $this->db->prepare("SELECT SUM(amount) as total FROM payments WHERE status = 'COMPLETE' AND DATE(created_at) = ?");
        $stmt->execute([$date]);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }
    
    public function getRevenueByDateRange($startDate, $endDate) {
        $stmt = $this->db->prepare("SELECT SUM(amount) as total FROM payments WHERE status = 'COMPLETE' AND DATE(created_at) BETWEEN ? AND ?");
        $stmt->execute([$startDate, $endDate]);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }
    
    public function getRecent($limit = 10) {
        $sql = "SELECT p.*, u.email as user_email, u.first_name, u.last_name 
                FROM payments p 
                LEFT JOIN users u ON p.user_id = u.id 
                ORDER BY p.created_at DESC LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    public function getAll($limit = 100, $offset = 0) {
        $sql = "SELECT p.*, u.email as user_email, u.first_name, u.last_name 
                FROM payments p 
                LEFT JOIN users u ON p.user_id = u.id 
                ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }
}
