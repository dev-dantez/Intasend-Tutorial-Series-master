<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/functions.php';

SessionManager::init();

$userId = SessionManager::get('user_id');
if ($userId) {
    log_activity("User logged out", ['user_id' => $userId]);
}

SessionManager::destroy();
redirect('login.php');
