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
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    
    $email = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $firstName = sanitize_input($_POST['first_name'] ?? '');
    $lastName = sanitize_input($_POST['last_name'] ?? '');
    $phone = sanitize_input($_POST['phone'] ?? '');
    
    // Validation
    if (empty($email) || empty($password) || empty($firstName) || empty($lastName)) {
        $error = 'Please fill in all required fields';
    } elseif (!validate_email($email)) {
        $error = 'Invalid email format';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match';
    } else {
        $userModel = new User();
        
        // Check if email exists
        if ($userModel->findByEmail($email)) {
            $error = 'Email already registered';
        } else {
            $userId = $userModel->create($email, $password, $firstName, $lastName, $phone);
            
            if ($userId) {
                $success = 'Account created successfully! Please login.';
                log_activity("User registered", ['user_id' => $userId, 'email' => $email]);
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}

include_once __DIR__ . '/../header.php';
?>

<div class="min-h-[calc(100vh-200px)] flex items-center justify-center py-8">
    <div class="card w-full max-w-md shadow-2xl bg-base-100 card-premium">
        <div class="card-body">
            <h2 class="card-title text-3xl font-bold text-center justify-center mb-6">
                <span class="gradient-text">Create Account</span>
            </h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error shadow-lg mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success shadow-lg mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span><?php echo htmlspecialchars($success); ?></span>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <?php echo csrf_field(); ?>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">First Name</span>
                        </label>
                        <input type="text" name="first_name" placeholder="John" 
                               class="input input-bordered input-premium" required
                               value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Last Name</span>
                        </label>
                        <input type="text" name="last_name" placeholder="Doe" 
                               class="input input-bordered input-premium" required
                               value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>">
                    </div>
                </div>
                
                <div class="form-control mt-4">
                    <label class="label">
                        <span class="label-text font-medium">Email</span>
                    </label>
                    <input type="email" name="email" placeholder="your@email.com" 
                           class="input input-bordered input-premium" required
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
                
                <div class="form-control mt-4">
                    <label class="label">
                        <span class="label-text font-medium">Phone (Optional)</span>
                    </label>
                    <input type="tel" name="phone" placeholder="254700000000" 
                           class="input input-bordered input-premium"
                           value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                </div>
                
                <div class="form-control mt-4">
                    <label class="label">
                        <span class="label-text font-medium">Password</span>
                    </label>
                    <input type="password" name="password" placeholder="••••••••" 
                           class="input input-bordered input-premium" required>
                </div>
                
                <div class="form-control mt-4">
                    <label class="label">
                        <span class="label-text font-medium">Confirm Password</span>
                    </label>
                    <input type="password" name="confirm_password" placeholder="••••••••" 
                           class="input input-bordered input-premium" required>
                </div>
                
                <div class="form-control mt-6">
                    <button type="submit" class="btn btn-primary-gradient w-full">
                        Create Account
                    </button>
                </div>
            </form>
            
            <div class="divider">OR</div>
            
            <div class="text-center">
                <p class="text-sm opacity-70">Already have an account?</p>
                <a href="login.php" class="link link-primary font-medium">Login</a>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../footer.php'; ?>
