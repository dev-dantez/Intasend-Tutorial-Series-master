<?php
/**
 * Quick script to make a user an admin
 * Usage: Visit this page and enter your email
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email)) {
        $error = 'Please enter an email address';
    } else {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("UPDATE users SET is_admin = 1 WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() > 0) {
                $success = "User $email is now an admin! You can now login and access the admin dashboard.";
            } else {
                $error = "No user found with email: $email";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html data-theme="forest" lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make Admin - IntaSend</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.10.2/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-base-300">
    <div class="container mx-auto p-8 max-w-md mt-20">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-2xl mb-4">Make User Admin</h2>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span><?php echo htmlspecialchars($success); ?></span>
                    </div>
                    <div class="mt-4">
                        <a href="../auth/login.php" class="btn btn-primary w-full">Go to Login</a>
                    </div>
                <?php else: ?>
                    <?php if ($error): ?>
                        <div class="alert alert-error mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span><?php echo htmlspecialchars($error); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">User Email</span>
                            </label>
                            <input type="email" name="email" placeholder="user@example.com" 
                                   class="input input-bordered" required>
                            <label class="label">
                                <span class="label-text-alt">Enter the email of the user you want to make admin</span>
                            </label>
                        </div>
                        
                        <div class="form-control mt-6">
                            <button type="submit" class="btn btn-primary">Make Admin</button>
                        </div>
                    </form>
                    
                    <div class="divider">OR</div>
                    
                    <div class="text-sm opacity-70">
                        <p class="mb-2">Don't have an account yet?</p>
                        <a href="../auth/register.php" class="link link-primary">Register here</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="alert alert-warning mt-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span class="text-xs">Delete this file after creating your admin account for security!</span>
        </div>
    </div>
</body>
</html>
