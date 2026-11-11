<?php
// Comprehensive diagnostic script
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

$output = [];
$errors = [];

try {
    $output[] = "Step 1: Starting diagnostic...";
    
    // Test each require
    $output[] = "Step 2: Loading config...";
    require_once __DIR__ . '/../config/config.php';
    $output[] = "✓ Config loaded";
    
    $output[] = "Step 3: Loading session...";
    require_once __DIR__ . '/../config/session.php';
    $output[] = "✓ Session loaded";
    
    $output[] = "Step 4: Loading functions...";
    require_once __DIR__ . '/../includes/functions.php';
    $output[] = "✓ Functions loaded";
    
    $output[] = "Step 5: Loading auth middleware...";
    require_once __DIR__ . '/../middleware/auth.php';
    $output[] = "✓ Auth middleware loaded";
    
    $output[] = "Step 6: Loading admin middleware...";
    require_once __DIR__ . '/../middleware/admin.php';
    $output[] = "✓ Admin middleware loaded";
    
    $output[] = "Step 7: Loading User model...";
    require_once __DIR__ . '/../models/User.php';
    $output[] = "✓ User model loaded";
    
    $output[] = "Step 8: Loading Payment model...";
    require_once __DIR__ . '/../models/Payment.php';
    $output[] = "✓ Payment model loaded";
    
    $output[] = "Step 9: Loading Settings model...";
    require_once __DIR__ . '/../models/Settings.php';
    $output[] = "✓ Settings model loaded";
    
    $output[] = "Step 10: Initializing session...";
    SessionManager::init();
    $output[] = "✓ Session initialized";
    
    $output[] = "Step 11: Checking authentication...";
    if (!SessionManager::has('user_id')) {
        $errors[] = "Not logged in - would redirect to /auth/login.php";
    } else {
        $output[] = "✓ User logged in (ID: " . SessionManager::get('user_id') . ")";
    }
    
    $output[] = "Step 12: Checking admin status...";
    if (!SessionManager::get('is_admin', false)) {
        $errors[] = "Not admin - would show access denied page";
    } else {
        $output[] = "✓ User is admin";
    }
    
    $output[] = "Step 13: Creating model instances...";
    $userModel = new User();
    $paymentModel = new Payment();
    $settingsModel = new Settings();
    $output[] = "✓ Models instantiated";
    
    $output[] = "Step 14: Fetching data...";
    $totalUsers = $userModel->getTotalCount();
    $output[] = "✓ Total users: $totalUsers";
    
    $premiumUsers = $userModel->getPremiumCount();
    $output[] = "✓ Premium users: $premiumUsers";
    
    $totalRevenue = $paymentModel->getTotalRevenue();
    $output[] = "✓ Total revenue: $totalRevenue";
    
    $recentPayments = $paymentModel->getRecent(10);
    $output[] = "✓ Recent payments: " . count($recentPayments);
    
    $recentUsers = $userModel->getRecent(10);
    $output[] = "✓ Recent users: " . count($recentUsers);
    
    $premiumPrice = $settingsModel->get('premium_price', 500);
    $output[] = "✓ Premium price: $premiumPrice";
    
    $currency = $settingsModel->get('currency', 'KES');
    $output[] = "✓ Currency: $currency";
    
    $output[] = "Step 15: Testing date functions...";
    $todaySignups = $userModel->getCountByDate(date('Y-m-d'));
    $output[] = "✓ Today's signups: $todaySignups";
    
    $todayRevenue = $paymentModel->getRevenueByDate(date('Y-m-d'));
    $output[] = "✓ Today's revenue: $todayRevenue";
    
    $output[] = "\n=== ALL CHECKS PASSED ===";
    
} catch (Exception $e) {
    $errors[] = "EXCEPTION: " . $e->getMessage();
    $errors[] = "File: " . $e->getFile() . " Line: " . $e->getLine();
    $errors[] = "Trace: " . $e->getTraceAsString();
} catch (Error $e) {
    $errors[] = "ERROR: " . $e->getMessage();
    $errors[] = "File: " . $e->getFile() . " Line: " . $e->getLine();
    $errors[] = "Trace: " . $e->getTraceAsString();
}

$buffer = ob_get_clean();
if ($buffer) {
    $errors[] = "Output buffer: " . $buffer;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard Diagnostic</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.10.2/dist/full.min.css" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-base-300 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-4xl font-bold mb-8">Admin Dashboard Diagnostic</h1>
        
        <?php if (empty($errors)): ?>
            <div class="alert alert-success mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>All checks passed! Dashboard should work.</span>
            </div>
            <a href="dashboard.php" class="btn btn-primary btn-lg">Open Dashboard</a>
        <?php else: ?>
            <div class="alert alert-error mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Errors detected - see below</span>
            </div>
        <?php endif; ?>
        
        <div class="card bg-base-100 shadow-xl mb-6">
            <div class="card-body">
                <h2 class="card-title">Diagnostic Output</h2>
                <div class="mockup-code">
                    <?php foreach ($output as $line): ?>
                        <pre><code><?php echo htmlspecialchars($line); ?></code></pre>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <?php if (!empty($errors)): ?>
            <div class="card bg-base-100 shadow-xl border-2 border-error">
                <div class="card-body">
                    <h2 class="card-title text-error">Errors</h2>
                    <div class="mockup-code">
                        <?php foreach ($errors as $error): ?>
                            <pre class="text-error"><code><?php echo htmlspecialchars($error); ?></code></pre>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="mt-6">
            <a href="../app.php" class="btn btn-ghost">Back to Site</a>
        </div>
    </div>
</body>
</html>
