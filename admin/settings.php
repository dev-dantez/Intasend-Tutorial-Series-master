<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../middleware/admin.php';
require_once __DIR__ . '/../models/Settings.php';

SessionManager::init();
require_admin();

$settingsModel = new Settings();
$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    
    $premiumPrice = filter_var($_POST['premium_price'], FILTER_VALIDATE_FLOAT);
    $currency = sanitize_input($_POST['currency']);
    $siteName = sanitize_input($_POST['site_name']);
    $maintenanceMode = isset($_POST['maintenance_mode']) ? 1 : 0;
    
    if ($premiumPrice === false || $premiumPrice < 0) {
        $error = 'Invalid premium price';
    } else {
        $settingsModel->set('premium_price', $premiumPrice, 'Premium subscription price');
        $settingsModel->set('currency', $currency, 'Default currency');
        $settingsModel->set('site_name', $siteName, 'Site name');
        $settingsModel->set('maintenance_mode', $maintenanceMode, 'Maintenance mode');
        
        $success = 'Settings updated successfully!';
        log_activity('Admin updated settings', ['user_id' => SessionManager::get('user_id')]);
    }
}

// Get current settings
$settings = $settingsModel->getAll();
$settingsArray = [];
foreach ($settings as $setting) {
    $settingsArray[$setting['setting_key']] = $setting['setting_value'];
}
?>
<!DOCTYPE html>
<html data-theme="forest" lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Settings - Admin - IntaSend</title>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@4.10.2/dist/full.min.css" rel="stylesheet" type="text/css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-base-300 text-base-content">

<div class="navbar glass-nav sticky top-0 z-50 px-4 mb-4" style="background: rgba(0,0,0,0.8); backdrop-filter: blur(10px);">
    <div class="flex-1">
      <a class="btn btn-ghost text-xl normal-case gap-2" href="dashboard.php">
        <span style="background: linear-gradient(to right, #3b82f6, #8b5cf6); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Kenyan Fullz Generator Pro</span> 
        <span class="font-light opacity-70 text-sm hidden sm:inline-block tracking-widest">ADMIN</span>
      </a>
    </div>
    <div class="flex-none gap-2">
      <a href="../app.php" class="btn btn-ghost btn-sm">Back to Site</a>
    </div>
</div>

<div class="container mx-auto p-4 max-w-4xl">
    <div class="flex items-center gap-4 mb-8">
        <a href="dashboard.php" class="btn btn-circle btn-ghost">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-4xl font-bold gradient-text">Settings</h1>
            <p class="text-base-content/70 mt-2">Configure your platform settings</p>
        </div>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success shadow-lg mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span><?php echo htmlspecialchars($success); ?></span>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-error shadow-lg mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span><?php echo htmlspecialchars($error); ?></span>
        </div>
    <?php endif; ?>

    <form method="POST" class="space-y-6">
        <?php echo csrf_field(); ?>

        <!-- General Settings -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">General Settings</h2>
                
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Site Name</span>
                    </label>
                    <input type="text" name="site_name" value="<?php echo htmlspecialchars($settingsArray['site_name'] ?? ''); ?>" 
                           class="input input-bordered" required>
                </div>

                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-4">
                        <input type="checkbox" name="maintenance_mode" value="1" 
                               <?php echo ($settingsArray['maintenance_mode'] ?? 0) ? 'checked' : ''; ?> 
                               class="checkbox checkbox-primary">
                        <div>
                            <span class="label-text font-medium">Maintenance Mode</span>
                            <p class="text-xs text-base-content/60">Temporarily disable site access for non-admin users</p>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Pricing Settings -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">Pricing Settings</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Premium Price</span>
                        </label>
                        <input type="number" name="premium_price" step="0.01" min="0" 
                               value="<?php echo htmlspecialchars($settingsArray['premium_price'] ?? '500'); ?>" 
                               class="input input-bordered" required>
                        <label class="label">
                            <span class="label-text-alt">Price for premium subscription</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Currency</span>
                        </label>
                        <select name="currency" class="select select-bordered" required>
                            <option value="KES" <?php echo ($settingsArray['currency'] ?? 'KES') === 'KES' ? 'selected' : ''; ?>>KES - Kenyan Shilling</option>
                            <option value="USD" <?php echo ($settingsArray['currency'] ?? 'KES') === 'USD' ? 'selected' : ''; ?>>USD - US Dollar</option>
                            <option value="EUR" <?php echo ($settingsArray['currency'] ?? 'KES') === 'EUR' ? 'selected' : ''; ?>>EUR - Euro</option>
                            <option value="GBP" <?php echo ($settingsArray['currency'] ?? 'KES') === 'GBP' ? 'selected' : ''; ?>>GBP - British Pound</option>
                        </select>
                    </div>
                </div>

                <div class="alert alert-info mt-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm">Price changes will apply to new subscriptions immediately. Existing subscriptions are not affected.</span>
                </div>
            </div>
        </div>

        <!-- All Settings Table -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">All Settings</h2>
                <div class="overflow-x-auto">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Key</th>
                                <th>Value</th>
                                <th>Description</th>
                                <th>Last Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($settings as $setting): ?>
                            <tr>
                                <td class="font-mono text-xs"><?php echo htmlspecialchars($setting['setting_key']); ?></td>
                                <td class="font-semibold"><?php echo htmlspecialchars($setting['setting_value']); ?></td>
                                <td class="text-xs"><?php echo htmlspecialchars($setting['description'] ?? '-'); ?></td>
                                <td class="text-xs"><?php echo date('M j, Y g:i A', strtotime($setting['updated_at'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-4">
            <a href="dashboard.php" class="btn btn-ghost">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Save Settings
            </button>
        </div>
    </form>
</div>

</body>
</html>
