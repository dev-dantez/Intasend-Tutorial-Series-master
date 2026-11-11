<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Step 1: Starting...<br>";

try {
    echo "Step 2: Loading config...<br>";
    require_once __DIR__ . '/../config/config.php';
    echo "Step 3: Config loaded<br>";
    
    echo "Step 4: Loading session...<br>";
    require_once __DIR__ . '/../config/session.php';
    echo "Step 5: Session loaded<br>";
    
    echo "Step 6: Loading functions...<br>";
    require_once __DIR__ . '/../includes/functions.php';
    echo "Step 7: Functions loaded<br>";
    
    echo "Step 8: Loading auth middleware...<br>";
    require_once __DIR__ . '/../middleware/auth.php';
    echo "Step 9: Auth middleware loaded<br>";
    
    echo "Step 10: Loading admin middleware...<br>";
    require_once __DIR__ . '/../middleware/admin.php';
    echo "Step 11: Admin middleware loaded<br>";
    
    echo "Step 12: Initializing session...<br>";
    SessionManager::init();
    echo "Step 13: Session initialized<br>";
    
    echo "<h2>Session Data:</h2>";
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";
    
    echo "<h2>Checking admin status...</h2>";
    if (SessionManager::has('user_id')) {
        echo "User ID: " . SessionManager::get('user_id') . "<br>";
        echo "Is Admin: " . (SessionManager::get('is_admin', false) ? 'Yes' : 'No') . "<br>";
    } else {
        echo "Not logged in<br>";
    }
    
    echo "<br><h2>All checks passed!</h2>";
    echo '<a href="dashboard.php">Try Dashboard</a>';
    
} catch (Exception $e) {
    echo "<h2 style='color:red'>Error: " . $e->getMessage() . "</h2>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
