<?php
$user = get_logged_in_user();
require_once __DIR__ . '/../models/User.php';
$userModel = new User();
$userDetails = $userModel->findById($user['id']);
?>

<div class="max-w-4xl mx-auto animate-fade-in">
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold mb-2">My Profile</h2>
        <p class="opacity-70">Manage your account settings</p>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Profile Card -->
        <div class="card bg-base-100 shadow-xl card-premium md:col-span-1">
            <div class="card-body items-center text-center">
                <div class="avatar placeholder mb-4">
                    <div class="bg-primary text-primary-content rounded-full w-24">
                        <span class="text-3xl"><?php echo strtoupper(substr($user['name'], 0, 2)); ?></span>
                    </div>
                </div>
                <h3 class="card-title"><?php echo htmlspecialchars($user['name']); ?></h3>
                <p class="text-sm opacity-70"><?php echo htmlspecialchars($user['email']); ?></p>
                
                <?php if ($user['is_premium']): ?>
                    <div class="badge badge-success gap-2 mt-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 011.397 1.398l-.8 1.6V12a1 1 0 01-.293.707l-1 1a1 1 0 00-.293.707V15a1 1 0 01-1 1h-4a1 1 0 01-1-1v-1.586A1 1 0 019 12V8.404l-.8-1.6a1 1 0 011.397-1.398l1.599.8L15.152 4.677 11 3.016V2a1 1 0 01-1-1z" clip-rule="evenodd" />
                        </svg>
                        Premium Member
                    </div>
                <?php else: ?>
                    <a href="app.php?page=upgrade" class="btn btn-warning btn-sm gap-2 mt-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 011.397 1.398l-.8 1.6V12a1 1 0 01-.293.707l-1 1a1 1 0 00-.293.707V15a1 1 0 01-1 1h-4a1 1 0 01-1-1v-1.586A1 1 0 019 12V8.404l-.8-1.6a1 1 0 011.397-1.398l1.599.8L15.152 4.677 11 3.016V2a1 1 0 01-1-1z" clip-rule="evenodd" />
                        </svg>
                        Upgrade to Premium
                    </a>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Account Details -->
        <div class="card bg-base-100 shadow-xl card-premium md:col-span-2">
            <div class="card-body">
                <h3 class="card-title mb-4">Account Information</h3>
                
                <div class="space-y-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Full Name</span>
                        </label>
                        <input type="text" value="<?php echo htmlspecialchars($userDetails['first_name'] . ' ' . $userDetails['last_name']); ?>" 
                               class="input input-bordered input-premium" disabled>
                    </div>
                    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Email Address</span>
                        </label>
                        <input type="email" value="<?php echo htmlspecialchars($userDetails['email']); ?>" 
                               class="input input-bordered input-premium" disabled>
                    </div>
                    
                    <?php if ($userDetails['phone']): ?>
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Phone Number</span>
                            </label>
                            <input type="tel" value="<?php echo htmlspecialchars($userDetails['phone']); ?>" 
                                   class="input input-bordered input-premium" disabled>
                        </div>
                    <?php endif; ?>
                    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Member Since</span>
                        </label>
                        <input type="text" value="<?php echo date('F j, Y', strtotime($userDetails['created_at'])); ?>" 
                               class="input input-bordered input-premium" disabled>
                    </div>
                    
                    <?php if ($userDetails['is_premium'] && $userDetails['premium_activated_at']): ?>
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Premium Since</span>
                            </label>
                            <input type="text" value="<?php echo date('F j, Y', strtotime($userDetails['premium_activated_at'])); ?>" 
                                   class="input input-bordered input-premium" disabled>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="card-actions justify-end mt-6">
                    <a href="app.php?page=payments" class="btn btn-outline btn-sm">View Payment History</a>
                </div>
            </div>
        </div>
    </div>
</div>
