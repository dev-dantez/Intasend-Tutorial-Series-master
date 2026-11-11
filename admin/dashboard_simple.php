<?php
// Simplified dashboard to test
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><title>Admin Dashboard</title>";
echo '<link href="https://cdn.jsdelivr.net/npm/daisyui@4.10.2/dist/full.min.css" rel="stylesheet" />';
echo '<script src="https://cdn.tailwindcss.com"></script>';
echo "</head><body class='bg-base-300 p-8'>";

echo "<h1 class='text-4xl font-bold mb-4'>Admin Dashboard - Simple Version</h1>";

try {
    require_once __DIR__ . '/../config/config.php';
    echo "<p>✓ Config loaded</p>";
    
    require_once __DIR__ . '/../config/session.php';
    echo "<p>✓ Session loaded</p>";
    
    SessionManager::init();
    echo "<p>✓ Session initialized</p>";
    
    require_once __DIR__ . '/../models/User.php';
    echo "<p>✓ User model loaded</p>";
    
    require_once __DIR__ . '/../models/Payment.php';
    echo "<p>✓ Payment model loaded</p>";
    
    require_once __DIR__ . '/../models/Settings.php';
    echo "<p>✓ Settings model loaded</p>";
    
    $userModel = new User();
    $paymentModel = new Payment();
    $settingsModel = new Settings();
    echo "<p>✓ Models instantiated</p>";
    
    // Get data
    $totalUsers = $userModel->getTotalCount();
    echo "<p>✓ Total users: $totalUsers</p>";
    
    $premiumUsers = $userModel->getPremiumCount();
    echo "<p>✓ Premium users: $premiumUsers</p>";
    
    $totalRevenue = $paymentModel->getTotalRevenue();
    echo "<p>✓ Total revenue: $totalRevenue</p>";
    
    $premiumPrice = $settingsModel->get('premium_price', 500);
    echo "<p>✓ Premium price: $premiumPrice</p>";
    
    echo "<div class='mt-8'>";
    echo "<h2 class='text-2xl font-bold mb-4'>Quick Stats</h2>";
    echo "<div class='stats shadow'>";
    echo "<div class='stat'><div class='stat-title'>Total Users</div><div class='stat-value'>$totalUsers</div></div>";
    echo "<div class='stat'><div class='stat-title'>Premium Users</div><div class='stat-value'>$premiumUsers</div></div>";
    echo "<div class='stat'><div class='stat-title'>Revenue</div><div class='stat-value'>KES $totalRevenue</div></div>";
    echo "</div></div>";
    
    echo "<div class='mt-4'><a href='dashboard.php' class='btn btn-primary'>Try Full Dashboard</a></div>";
    
} catch (Exception $e) {
    echo "<div class='alert alert-error mt-4'>";
    echo "<h3>Error: " . htmlspecialchars($e->getMessage()) . "</h3>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}

echo "</body></html>";
