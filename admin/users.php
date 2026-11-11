<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../middleware/admin.php';
require_once __DIR__ . '/../models/User.php';

SessionManager::init();
require_admin();

$userModel = new User();
$success = '';
$error = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    
    $action = $_POST['action'] ?? '';
    $userId = (int)($_POST['user_id'] ?? 0);
    
    if ($action === 'toggle_premium' && $userId) {
        $user = $userModel->findById($userId);
        if ($user) {
            $newStatus = !$user['is_premium'];
            $userModel->updatePremiumStatus($userId, $newStatus);
            $success = 'Premium status updated successfully!';
            log_activity('Admin toggled premium status', ['user_id' => $userId, 'new_status' => $newStatus]);
        }
    } elseif ($action === 'toggle_admin' && $userId) {
        $user = $userModel->findById($userId);
        if ($user && $user['id'] != SessionManager::get('user_id')) {
            $newStatus = !$user['is_admin'];
            $userModel->updateAdmin($userId, $newStatus);
            $success = 'Admin status updated successfully!';
            log_activity('Admin toggled admin status', ['user_id' => $userId, 'new_status' => $newStatus]);
        } else {
            $error = 'Cannot modify your own admin status';
        }
    }
}

// Pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

$users = $userModel->getAll($perPage, $offset);
$totalUsers = $userModel->getTotalCount();
$totalPages = ceil($totalUsers / $perPage);
?>
<!DOCTYPE html>
<html data-theme="forest" lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Users - Admin - IntaSend</title>
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
                <h1 class="text-4xl font-bold gradient-text">User Management</h1>
                <p class="text-base-content/70 mt-2">Manage users and subscriptions</p>
            </div>
        </div>
        <div class="stats shadow">
            <div class="stat">
                <div class="stat-title">Total Users</div>
                <div class="stat-value text-primary"><?php echo number_format($totalUsers); ?></div>
            </div>
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

    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="avatar placeholder">
                                        <div class="bg-neutral text-neutral-content rounded-full w-10">
                                            <span class="text-xs"><?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?></span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-bold"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></div>
                                        <?php if ($user['is_admin']): ?>
                                            <div class="badge badge-sm badge-error">Admin</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="text-sm"><?php echo htmlspecialchars($user['email']); ?></td>
                            <td class="text-sm"><?php echo htmlspecialchars($user['phone'] ?? '-'); ?></td>
                            <td>
                                <?php if ($user['is_premium']): ?>
                                    <span class="badge badge-success gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                        </svg>
                                        Premium
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-ghost">Free</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-sm"><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                            <td>
                                <div class="dropdown dropdown-end">
                                    <label tabindex="0" class="btn btn-ghost btn-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                        </svg>
                                    </label>
                                    <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                                        <li>
                                            <form method="POST" class="w-full">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="action" value="toggle_premium">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <button type="submit" class="w-full text-left">
                                                    <?php echo $user['is_premium'] ? 'Remove Premium' : 'Grant Premium'; ?>
                                                </button>
                                            </form>
                                        </li>
                                        <?php if ($user['id'] != SessionManager::get('user_id')): ?>
                                        <li>
                                            <form method="POST" class="w-full">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="action" value="toggle_admin">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <button type="submit" class="w-full text-left">
                                                    <?php echo $user['is_admin'] ? 'Remove Admin' : 'Make Admin'; ?>
                                                </button>
                                            </form>
                                        </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <div class="flex justify-center mt-6">
                <div class="btn-group">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>" class="btn btn-sm">«</a>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                        <a href="?page=<?php echo $i; ?>" class="btn btn-sm <?php echo $i === $page ? 'btn-active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?php echo $page + 1; ?>" class="btn btn-sm">»</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
