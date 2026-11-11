<?php

// Security Functions

function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function validate_phone($phone) {
    // Kenyan phone format: 254XXXXXXXXX
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    if (strlen($phone) === 12 && substr($phone, 0, 3) === '254') {
        return $phone;
    }
    
    if (strlen($phone) === 10 && substr($phone, 0, 1) === '0') {
        return '254' . substr($phone, 1);
    }
    
    return false;
}

function validate_amount($amount) {
    $amount = filter_var($amount, FILTER_VALIDATE_FLOAT);
    return ($amount !== false && $amount > 0) ? $amount : false;
}

function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function redirect($url) {
    // If URL doesn't start with http/https or /, make it absolute from root
    if (!preg_match('/^(https?:\/\/|\/)/i', $url)) {
        $url = '/' . ltrim($url, '/');
    }
    header("Location: $url");
    exit();
}

function json_response($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

function get_base_url() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $script_path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
    return $protocol . '://' . $host . $script_path;
}

function log_activity($message, $data = []) {
    $logFile = __DIR__ . '/../logs/activity.log';
    $logDir = dirname($logFile);
    
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message";
    
    if (!empty($data)) {
        $logMessage .= ' | ' . json_encode($data);
    }
    
    file_put_contents($logFile, $logMessage . PHP_EOL, FILE_APPEND);
}

function csrf_field() {
    $token = SessionManager::generateCSRFToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

function verify_csrf() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        if (!SessionManager::validateCSRFToken($token)) {
            http_response_code(403);
            die('CSRF token validation failed');
        }
    }
}
