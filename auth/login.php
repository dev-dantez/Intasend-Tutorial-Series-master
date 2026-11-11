<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../models/User.php';

SessionManager::init();

// Redirect if already logged in
if (SessionManager::has('user_id')) {
    redirect('app.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    
    $email = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        $userModel = new User();
        $user = $userModel->verifyPassword($email, $password);
        
        if ($user) {
            SessionManager::regenerate();
            SessionManager::set('user_id', $user['id']);
            SessionManager::set('user_email', $user['email']);
            SessionManager::set('user_name', $user['first_name'] . ' ' . $user['last_name']);
            SessionManager::set('user_phone', $user['phone'] ?? '');
            SessionManager::set('is_premium', $user['is_premium']);
            SessionManager::set('is_admin', $user['is_admin']);
            
            log_activity("User logged in", ['user_id' => $user['id'], 'email' => $email]);
            
            // Redirect to admin dashboard if admin
            if ($user['is_admin']) {
                redirect('admin/dashboard.php');
            } else {
                redirect('app.php');
            }
        } else {
            $error = 'Invalid email or password';
            log_activity("Failed login attempt", ['email' => $email]);
        }
    }
}

include_once __DIR__ . '/../header.php';
?>

<div class="min-h-[calc(100vh-200px)] flex items-center justify-center">
    <div class="card w-full max-w-md shadow-2xl bg-base-100 card-premium">
        <div class="card-body">
            <h2 class="card-title text-3xl font-bold text-center justify-center mb-6">
                <span class="gradient-text">Login</span>
            </h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error shadow-lg mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <?php echo csrf_field(); ?>
                
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Email</span>
                    </label>
                    <input type="email" name="email" placeholder="your@email.com" 
                           class="input input-bordered input-premium" required 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
                
                <div class="form-control mt-4">
                    <label class="label">
                        <span class="label-text font-medium">Password</span>
                    </label>
                    <input type="password" name="password" placeholder="••••••••" 
                           class="input input-bordered input-premium" required>
                </div>
                
                <div class="form-control mt-6">
                    <button type="submit" class="btn btn-primary-gradient w-full">
                        Login
                    </button>
                </div>
            </form>
            
            <div class="divider">OR</div>
            
            <div class="text-center">
                <p class="text-sm opacity-70">Don't have an account?</p>
                <a href="register.php" class="link link-primary font-medium">Create Account</a>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../footer.php'; ?>
