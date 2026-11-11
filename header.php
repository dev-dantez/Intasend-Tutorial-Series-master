<!DOCTYPE html>
<html data-theme="forest" lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kenyan ID Generator | Secure Identity Platform</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Tailwind & DaisyUI -->
  <link href="https://cdn.jsdelivr.net/npm/daisyui@4.10.2/dist/full.min.css" rel="stylesheet" type="text/css" />
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Custom Styles -->
  <link rel="stylesheet" href="/assets/css/style.css">

  <style>
    /* Nav active indicator */
    .nav-link {
      position: relative;
      padding: 0.4rem 0.75rem;
      border-radius: 0.5rem;
      font-size: 0.875rem;
      font-weight: 500;
      color: rgba(255,255,255,0.65);
      transition: color 0.2s, background 0.2s;
      white-space: nowrap;
    }
    .nav-link:hover { color: #fff; background: rgba(255,255,255,0.07); }
    .nav-link.active { color: #fff; background: rgba(16,185,129,0.15); }
    .nav-link.active::after {
      content: '';
      position: absolute;
      bottom: -2px; left: 50%; transform: translateX(-50%);
      width: 16px; height: 2px;
      background: #10b981;
      border-radius: 999px;
    }
    .nav-admin { color: #f87171 !important; }
    .nav-admin:hover { background: rgba(248,113,113,0.1) !important; }
  </style>
</head>

<body class="bg-base-300 text-base-content">

<?php
// Determine current page for active nav state
$currentPage = $_GET['page'] ?? 'home';
$currentScript = basename($_SERVER['PHP_SELF']);
function navActive($page, $current) {
  return $page === $current ? ' active' : '';
}
?>

<!-- ═══════════════════════ TOP NAV ═══════════════════════ -->
<header class="glass-nav sticky top-0 z-50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">

    <!-- Logo -->
    <a href="/app.php" class="flex items-center gap-2.5 shrink-0">
      <div class="w-8 h-8 rounded-lg bg-primary/20 flex items-center justify-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
      </div>
      <span class="font-bold text-base tracking-tight text-white">ID<span class="gradient-text">Gen</span></span>
    </a>

    <!-- Desktop Nav -->
    <nav class="hidden md:flex items-center gap-1">
      <?php if (SessionManager::get('is_admin', false)): ?>
        <a href="/admin/dashboard.php" class="nav-link nav-admin">⚡ Admin</a>
      <?php endif; ?>
      <a href="/app.php?page=home"     class="nav-link<?= navActive('home',     $currentPage) ?>">Home</a>
      <a href="/app.php?page=services" class="nav-link<?= navActive('services', $currentPage) ?>">Mock Platform</a>
      <a href="/app.php?page=checkout" class="nav-link<?= navActive('checkout', $currentPage) ?>">Checkout</a>
      <a href="/app.php?page=stk"      class="nav-link<?= navActive('stk',      $currentPage) ?>">STK Push</a>
      <a href="/app.php?page=wallet"   class="nav-link<?= navActive('wallet',   $currentPage) ?>">Wallets</a>
      <a href="/app.php?page=payments" class="nav-link<?= navActive('payments', $currentPage) ?>">History</a>
    </nav>

    <!-- Right: premium badge + avatar menu -->
    <div class="flex items-center gap-3 shrink-0">
      <?php if (SessionManager::get('is_premium')): ?>
        <span class="hidden sm:inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full bg-success/15 text-success border border-success/25">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
          </svg>
          Premium
        </span>
      <?php else: ?>
        <a href="/app.php?page=upgrade" class="hidden sm:inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full bg-primary/15 text-primary border border-primary/25 hover:bg-primary/25 transition-colors">
          Upgrade
        </a>
      <?php endif; ?>

      <!-- Avatar dropdown -->
      <div class="dropdown dropdown-end">
        <label tabindex="0" class="cursor-pointer flex items-center gap-2 group">
          <div class="w-9 h-9 rounded-full bg-gradient-to-br from-primary to-emerald-700 flex items-center justify-center text-white text-xs font-bold ring-2 ring-white/10 group-hover:ring-primary/50 transition-all">
            <?php echo strtoupper(substr(SessionManager::get('user_name', 'U'), 0, 2)); ?>
          </div>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-white/40 group-hover:text-white/70 transition-colors hidden sm:block" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
          </svg>
        </label>
        <ul tabindex="0" class="dropdown-content mt-2 z-50 w-56 rounded-xl shadow-2xl bg-base-100 border border-white/10 overflow-hidden">
          <li class="px-4 py-3 border-b border-white/10">
            <p class="text-sm font-semibold text-white truncate"><?php echo htmlspecialchars(SessionManager::get('user_name', 'User')); ?></p>
            <p class="text-xs text-white/40 truncate"><?php echo htmlspecialchars(SessionManager::get('user_email', '')); ?></p>
          </li>

          <!-- Mobile nav links -->
          <li class="md:hidden border-b border-white/10 py-1">
            <?php if (SessionManager::get('is_admin', false)): ?>
              <a href="/admin/dashboard.php" class="flex items-center gap-2 px-4 py-2 text-sm text-red-400 hover:bg-white/5">⚡ Admin Dashboard</a>
            <?php endif; ?>
            <a href="/app.php?page=home"     class="flex items-center gap-2 px-4 py-2 text-sm text-white/70 hover:bg-white/5">Home</a>
            <a href="/app.php?page=services" class="flex items-center gap-2 px-4 py-2 text-sm text-white/70 hover:bg-white/5">Mock Platform</a>
            <a href="/app.php?page=checkout" class="flex items-center gap-2 px-4 py-2 text-sm text-white/70 hover:bg-white/5">Checkout</a>
            <a href="/app.php?page=stk"      class="flex items-center gap-2 px-4 py-2 text-sm text-white/70 hover:bg-white/5">STK Push</a>
            <a href="/app.php?page=wallet"   class="flex items-center gap-2 px-4 py-2 text-sm text-white/70 hover:bg-white/5">Wallets</a>
            <a href="/app.php?page=payments" class="flex items-center gap-2 px-4 py-2 text-sm text-white/70 hover:bg-white/5">History</a>
          </li>

          <li class="py-1">
            <a href="/app.php?page=profile" class="flex items-center gap-2 px-4 py-2 text-sm text-white/70 hover:bg-white/5 hover:text-white transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
              Profile
            </a>
            <a href="/app.php?page=payments" class="flex items-center gap-2 px-4 py-2 text-sm text-white/70 hover:bg-white/5 hover:text-white transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
              Payment History
            </a>
          </li>
          <li class="border-t border-white/10 py-1">
            <a href="/auth/logout.php" class="flex items-center gap-2 px-4 py-2 text-sm text-red-400 hover:bg-red-500/10 transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
              Sign Out
            </a>
          </li>
        </ul>
      </div>
    </div>

  </div>
</header>
<!-- ═══════════════════════ /TOP NAV ═══════════════════════ -->

<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 min-h-[calc(100vh-160px)]">
