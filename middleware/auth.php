<?php
// Authentication Middleware

function require_auth() {
    if (!SessionManager::has('user_id')) {
        redirect('/auth/login.php');
    }
}

function require_premium() {
    require_auth();
    
    if (!SessionManager::get('is_premium', false)) {
        redirect('/app.php?page=upgrade');
    }
}

function get_logged_in_user() {
    if (!SessionManager::has('user_id')) {
        return null;
    }
    
    return [
        'id' => SessionManager::get('user_id'),
        'email' => SessionManager::get('user_email'),
        'name' => SessionManager::get('user_name'),
        'phone' => SessionManager::get('user_phone', ''),
        'is_premium' => SessionManager::get('is_premium', false),
    ];
}
