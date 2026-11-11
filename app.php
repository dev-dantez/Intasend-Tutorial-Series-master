<?php
// Main application file
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/middleware/auth.php';

// Initialize session
SessionManager::init();

// Require authentication for all pages
require_auth();

// Error reporting based on environment
if (Config::isProduction()) {
    error_reporting(0);
    ini_set('display_errors', 0);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

include_once(__DIR__ . '/vendor/autoload.php');
include_once('header.php');

$page = isset($_GET['page']) ? $_GET['page'] : 'home';

echo '<div>';

try {
    switch ($page) {
        case 'checkout':
            include_once('pages/checkout_new.php');
            break;
        case 'stk':
            include_once('pages/stk_new.php');
            break;
        case 'wallet':
            include_once('pages/wallet.php');
            break;
        case 'services':
            include_once('pages/services.php');
            break;
        case 'upgrade':
            include_once('pages/upgrade_new.php');
            break;
        case 'payments':
            include_once('pages/payment_history.php');
            break;
        case 'profile':
            include_once('pages/profile.php');
            break;
        case 'home':
        default:
            include_once('pages/home.php');
            break;
    }
} catch (Exception $e) {
    echo '<div class="alert alert-error">An error occurred: ' . $e->getMessage() . '</div>';
}

echo '</div>';

include_once('footer.php');
?>
