<?php
session_start();

echo "<h1>Debug Information</h1>";

echo "<h2>Session Data:</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h2>User Status:</h2>";
if (isset($_SESSION['user_id'])) {
    echo "Logged in as User ID: " . $_SESSION['user_id'] . "<br>";
    echo "Is Admin: " . (isset($_SESSION['is_admin']) ? ($_SESSION['is_admin'] ? 'Yes' : 'No') : 'Not set') . "<br>";
    echo "Is Premium: " . (isset($_SESSION['is_premium']) ? ($_SESSION['is_premium'] ? 'Yes' : 'No') : 'Not set') . "<br>";
    
    // Check database
    require_once __DIR__ . '/../config/database.php';
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT id, email, is_admin, is_premium FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    echo "<h2>Database User Data:</h2>";
    echo "<pre>";
    print_r($user);
    echo "</pre>";
} else {
    echo "Not logged in<br>";
    echo '<a href="../auth/login.php">Go to Login</a>';
}

echo "<h2>Next Steps:</h2>";
if (!isset($_SESSION['user_id'])) {
    echo "1. <a href='../auth/login.php'>Login first</a><br>";
} elseif (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    echo "1. <a href='make_admin.php'>Make yourself admin</a><br>";
    echo "2. Then logout and login again<br>";
} else {
    echo "✓ You're an admin! <a href='dashboard.php'>Go to Dashboard</a>";
}
