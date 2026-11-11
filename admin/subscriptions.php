<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../middleware/admin.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Payment.php';

SessionManager::init();
require_admin();

$userModel = new User();
$paymentModel = new Payment();

// Get premium users
$premiumUsers = $userModel->getPremiumCount();
$totalUsers = $userModel->getTotalCount();
$conversionRate = $totalUsers > 0 ? ($premiumUsers / $totalUsers) * 100 : 0;

// Get all users with premium status
$db = Database::getInstance()->getConnection();
$stmt = $db->query("SELECT u.*, p.created_at as premium_date, p.amount as premium_amount 
                    FROM users u 
                    LEFT JOIN payments p ON u.id = p.user_id AND p.status = 'COMPLETE' AND p.payment_type = 'premium'
                    WHERE u.is_premium = 1 
                    ORDER BY u.premium_activated_at DESC");
$subscriptions = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html data-theme="forest" lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Subscriptions - Admin - IntaSend</title>
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

<div class="container mx-auto p-4">
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-4">
            <a href="dashboard.php" class="btn btn-circle btn-ghost">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-4xl font-bold gradient-text">Subscriptions</h1>
                <p class="text-base-content/70 mt-2">Manage premium subscriptions</p>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="stats shadow">
            <div class="stat">
                <div class="stat-figure text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                </div>
                <div class="stat-title">Premium Users</div>
                <div class="stat-value text-primary"><?php echo number_format($premiumUsers); ?></div>
                <div class="stat-desc">Out of <?php echo number_format($totalUsers); ?> total users</div>
            </div>
        </div>

        <div class="stats shadow">
            <div class="stat">
                <div class="stat-figure text-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
                <div class="stat-title">Conversion Rate</div>
                <div class="stat-value text-secondary"><?php echo number_format($conversionRate, 1); ?>%</div>
                <div class="stat-desc">Free to Premium</div>
            </div>
        </div>

        <div class="stats shadow">
            <div class="stat">
                <div class="stat-figure text-accent">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="stat-title">Total Revenue</div>
                <div class="stat-value text-accent">KES <?php echo number_format($paymentModel->getTotalRevenue(), 2); ?></div>
                <div class="stat-desc">All time</div>
            </div>
        </div>
    </div>

    <!-- Subscriptions Table -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title">Active Subscriptions</h2>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Activated</th>
                            <th>Amount Paid</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($subscriptions as $sub): ?>
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="avatar placeholder">
                                        <div class="bg-primary text-primary-content rounded-full w-10">
                                            <span class="text-xs"><?php echo strtoupper(substr($sub['first_name'], 0, 1) . substr($sub['last_name'], 0, 1)); ?></span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-bold"><?php echo htmlspecialchars($sub['first_name'] . ' ' . $sub['last_name']); ?></div>
                                        <div class="text-xs opacity-50">ID: <?php echo $sub['id']; ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-sm"><?php echo htmlspecialchars($sub['email']); ?></td>
                            <td class="text-sm"><?php echo htmlspecialchars($sub['phone'] ?? '-'); ?></td>
                            <td class="text-sm">
                                <?php if ($sub['premium_activated_at']): ?>
                                    <?php echo date('M j, Y g:i A', strtotime($sub['premium_activated_at'])); ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="font-mono text-sm">
                                <?php if ($sub['premium_amount']): ?>
                                    KES <?php echo number_format($sub['premium_amount'], 2); ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge badge-success gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Active
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if (empty($subscriptions)): ?>
                <div class="text-center py-12 opacity-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <p>No premium subscriptions yet</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
