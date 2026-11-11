<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../middleware/admin.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../models/Settings.php';

SessionManager::init();
require_admin();

$userModel = new User();
$paymentModel = new Payment();
$settingsModel = new Settings();

// Get analytics data
$totalUsers = $userModel->getTotalCount();
$premiumUsers = $userModel->getPremiumCount();
$totalRevenue = $paymentModel->getTotalRevenue();
$recentPayments = $paymentModel->getRecent(10);
$recentUsers = $userModel->getRecent(10);

// Get current settings
$premiumPrice = $settingsModel->get('premium_price', 500);
$currency = $settingsModel->get('currency', 'KES');

// Get statistics
$stats = [
    'today_signups' => $userModel->getCountByDate(date('Y-m-d')),
    'today_revenue' => $paymentModel->getRevenueByDate(date('Y-m-d')),
    'week_signups' => $userModel->getCountByDateRange(date('Y-m-d', strtotime('-7 days')), date('Y-m-d')),
    'week_revenue' => $paymentModel->getRevenueByDateRange(date('Y-m-d', strtotime('-7 days')), date('Y-m-d')),
    'month_signups' => $userModel->getCountByDateRange(date('Y-m-01'), date('Y-m-d')),
    'month_revenue' => $paymentModel->getRevenueByDateRange(date('Y-m-01'), date('Y-m-d')),
];
?>
<!DOCTYPE html>
<html data-theme="forest" lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - IntaSend</title>
  <link href="https://cdn.jsdelivr.net/npm/daisyui@4.10.2/dist/full.min.css" rel="stylesheet" type="text/css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .gradient-text {
      background: linear-gradient(to right, #3b82f6, #8b5cf6);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
  </style>
</head>
<body class="bg-base-300 text-base-content">

<!-- Navbar -->
<div class="navbar bg-base-100 shadow-lg sticky top-0 z-50 px-4 mb-4">
    <div class="flex-1">
      <a class="btn btn-ghost text-xl normal-case gap-2" href="dashboard.php">
        <span class="gradient-text">Kenyan Fullz Generator Pro</span> 
        <span class="font-light opacity-70 text-sm">ADMIN</span>
      </a>
    </div>
    <div class="flex-none gap-2">
      <a href="../app.php" class="btn btn-ghost btn-sm">Back to Site</a>
      <a href="../auth/logout.php" class="btn btn-error btn-sm">Logout</a>
    </div>
</div>

<div class="container mx-auto p-4">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-4xl font-bold gradient-text">Admin Dashboard</h1>
            <p class="text-base-content/70 mt-2">Manage your platform, users, and settings</p>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Users -->
        <div class="card bg-gradient-to-br from-blue-500 to-blue-700 text-white shadow-xl">
            <div class="card-body">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-blue-100 text-sm">Total Users</p>
                        <h2 class="text-4xl font-bold mt-2"><?php echo number_format($totalUsers); ?></h2>
                        <p class="text-blue-100 text-xs mt-2">+<?php echo $stats['today_signups']; ?> today</p>
                    </div>
                    <div class="bg-white/20 p-3 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Premium Users -->
        <div class="card bg-gradient-to-br from-green-500 to-green-700 text-white shadow-xl">
            <div class="card-body">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-green-100 text-sm">Premium Users</p>
                        <h2 class="text-4xl font-bold mt-2"><?php echo number_format($premiumUsers); ?></h2>
                        <p class="text-green-100 text-xs mt-2"><?php echo $totalUsers > 0 ? round(($premiumUsers/$totalUsers)*100, 1) : 0; ?>% conversion</p>
                    </div>
                    <div class="bg-white/20 p-3 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="card bg-gradient-to-br from-purple-500 to-purple-700 text-white shadow-xl">
            <div class="card-body">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-purple-100 text-sm">Total Revenue</p>
                        <h2 class="text-4xl font-bold mt-2"><?php echo $currency; ?> <?php echo number_format($totalRevenue, 2); ?></h2>
                        <p class="text-purple-100 text-xs mt-2">+<?php echo $currency; ?> <?php echo number_format($stats['today_revenue'], 2); ?> today</p>
                    </div>
                    <div class="bg-white/20 p-3 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Price -->
        <div class="card bg-gradient-to-br from-orange-500 to-orange-700 text-white shadow-xl">
            <div class="card-body">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-orange-100 text-sm">Premium Price</p>
                        <h2 class="text-4xl font-bold mt-2"><?php echo $currency; ?> <?php echo number_format($premiumPrice); ?></h2>
                        <a href="settings.php" class="text-orange-100 text-xs mt-2 underline">Change price</a>
                    </div>
                    <div class="bg-white/20 p-3 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Period Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h3 class="card-title text-lg">This Week</h3>
                <div class="stats stats-vertical shadow-none bg-transparent">
                    <div class="stat px-0">
                        <div class="stat-title">New Users</div>
                        <div class="stat-value text-primary"><?php echo $stats['week_signups']; ?></div>
                    </div>
                    <div class="stat px-0">
                        <div class="stat-title">Revenue</div>
                        <div class="stat-value text-secondary text-2xl"><?php echo $currency; ?> <?php echo number_format($stats['week_revenue'], 2); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h3 class="card-title text-lg">This Month</h3>
                <div class="stats stats-vertical shadow-none bg-transparent">
                    <div class="stat px-0">
                        <div class="stat-title">New Users</div>
                        <div class="stat-value text-primary"><?php echo $stats['month_signups']; ?></div>
                    </div>
                    <div class="stat px-0">
                        <div class="stat-title">Revenue</div>
                        <div class="stat-value text-secondary text-2xl"><?php echo $currency; ?> <?php echo number_format($stats['month_revenue'], 2); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h3 class="card-title text-lg">Quick Actions</h3>
                <div class="flex flex-col gap-2 mt-4">
                    <a href="settings.php" class="btn btn-sm btn-outline">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Settings
                    </a>
                    <a href="users.php" class="btn btn-sm btn-outline">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Manage Users
                    </a>
                    <a href="subscriptions.php" class="btn btn-sm btn-outline">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                        Subscriptions
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Payments -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h3 class="card-title">Recent Payments</h3>
                <div class="overflow-x-auto">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentPayments as $payment): ?>
                            <tr>
                                <td class="text-xs"><?php echo htmlspecialchars($payment['user_email'] ?? 'N/A'); ?></td>
                                <td class="font-mono text-xs"><?php echo $currency; ?> <?php echo number_format($payment['amount'], 2); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $payment['status'] === 'COMPLETE' ? 'success' : ($payment['status'] === 'PENDING' ? 'warning' : 'error'); ?> badge-sm">
                                        <?php echo $payment['status']; ?>
                                    </span>
                                </td>
                                <td class="text-xs"><?php echo date('M j, g:i A', strtotime($payment['created_at'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Users -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h3 class="card-title">Recent Users</h3>
                <div class="overflow-x-auto">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentUsers as $user): ?>
                            <tr>
                                <td class="text-xs"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                                <td class="text-xs"><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <?php if ($user['is_premium']): ?>
                                        <span class="badge badge-success badge-sm">Premium</span>
                                    <?php else: ?>
                                        <span class="badge badge-ghost badge-sm">Free</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-xs"><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
