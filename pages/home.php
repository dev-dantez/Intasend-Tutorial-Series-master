<?php
$isPremium  = SessionManager::get('is_premium', false);
$userName   = SessionManager::get('user_name', 'there');
$firstName  = explode(' ', trim($userName))[0];
?>

<!-- ─── Hero ─── -->
<section class="relative mb-16 pt-4">

  <!-- Background glow blobs -->
  <div class="pointer-events-none absolute -top-20 -left-20 w-96 h-96 rounded-full bg-primary/10 blur-3xl"></div>
  <div class="pointer-events-none absolute top-10 right-0 w-72 h-72 rounded-full bg-emerald-700/10 blur-3xl"></div>

  <div class="relative text-center max-w-3xl mx-auto animate-fade-in">

    <!-- Badge -->
    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-primary/10 border border-primary/20 text-primary text-xs font-semibold mb-6 tracking-wide uppercase">
      <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
      Powered by IntaSend Payments
    </div>

    <h1 class="text-5xl sm:text-6xl font-extrabold leading-tight tracking-tight mb-5">
      Welcome back,<br>
      <span class="gradient-text"><?php echo htmlspecialchars($firstName); ?></span>
    </h1>

    <p class="text-lg text-white/55 max-w-xl mx-auto leading-relaxed mb-8">
      Generate realistic Kenyan identity profiles, test M-Pesa payments, and explore the IntaSend PHP SDK — all in one place.
    </p>

    <div class="flex flex-wrap gap-3 justify-center">
      <?php if ($isPremium): ?>
        <a href="app.php?page=services" class="btn btn-primary-gradient px-6 gap-2 shadow-lg shadow-primary/20">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
          </svg>
          Open Mock Platform
        </a>
      <?php else: ?>
        <a href="app.php?page=upgrade" class="btn btn-primary-gradient px-6 gap-2 shadow-lg shadow-primary/20">
          Upgrade to Premium — KES 500
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
          </svg>
        </a>
      <?php endif; ?>
      <a href="app.php?page=stk" class="btn bg-white/5 border border-white/10 hover:bg-white/10 text-white px-6">
        Try STK Push
      </a>
    </div>

    <!-- Stats row -->
    <div class="mt-10 flex flex-wrap justify-center gap-8 text-center">
      <?php
      $stats = [
        ['label' => 'Payment Methods', 'value' => '3+'],
        ['label' => 'SDK Integrations', 'value' => '5'],
        ['label' => 'API Response', 'value' => '<1s'],
      ];
      foreach ($stats as $s): ?>
        <div>
          <div class="text-2xl font-bold text-white"><?php echo $s['value']; ?></div>
          <div class="text-xs text-white/40 mt-0.5"><?php echo $s['label']; ?></div>
        </div>
      <?php endforeach; ?>
    </div>

  </div>
</section>

<!-- ─── Feature Cards ─── -->
<section class="mb-14">

  <h2 class="text-xs font-semibold uppercase tracking-widest text-white/30 mb-6 text-center">Platform Features</h2>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

    <?php
    $features = [
      [
        'href'  => '?page=services',
        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>',
        'accent'=> 'primary',
        'title' => 'Mock Platform',
        'desc'  => 'Generate realistic Kenyan identity profiles with names, IDs, phone numbers and more.',
        'badge' => $isPremium ? null : ['label' => 'Premium', 'color' => 'warning'],
        'cta'   => $isPremium ? 'Open →' : 'Upgrade →',
      ],
      [
        'href'  => '?page=checkout',
        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>',
        'accent'=> 'blue-400',
        'title' => 'Checkout Link',
        'desc'  => 'Create hosted payment pages with one API call. Redirect customers to a secure checkout.',
        'badge' => null,
        'cta'   => 'Try it →',
      ],
      [
        'href'  => '?page=stk',
        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>',
        'accent'=> 'green-400',
        'title' => 'M-Pesa STK Push',
        'desc'  => 'Trigger real-time M-Pesa prompts on a customer\'s phone. Instant mobile money collection.',
        'badge' => null,
        'cta'   => 'Try it →',
      ],
      [
        'href'  => '?page=wallet',
        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>',
        'accent'=> 'yellow-400',
        'title' => 'Wallets',
        'desc'  => 'View your IntaSend wallet balances and monitor transaction flows in real time.',
        'badge' => null,
        'cta'   => 'View →',
      ],
      [
        'href'  => '?page=payments',
        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
        'accent'=> 'purple-400',
        'title' => 'Payment History',
        'desc'  => 'Full audit trail of every transaction you\'ve made — status, amount, and timestamps.',
        'badge' => null,
        'cta'   => 'View →',
      ],
      [
        'href'  => '?page=profile',
        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>',
        'accent'=> 'pink-400',
        'title' => 'My Profile',
        'desc'  => 'Update your personal details, phone number, and account preferences.',
        'badge' => null,
        'cta'   => 'Edit →',
      ],
    ];
    foreach ($features as $f): ?>

    <a href="<?php echo $f['href']; ?>" class="group relative flex flex-col p-5 rounded-2xl bg-white/[0.03] border border-white/[0.07] hover:border-primary/30 hover:bg-white/[0.06] transition-all duration-200">

      <!-- Icon -->
      <div class="mb-4 w-10 h-10 rounded-xl flex items-center justify-center bg-<?php echo $f['accent']; ?>/10 group-hover:bg-<?php echo $f['accent']; ?>/20 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-<?php echo $f['accent']; ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <?php echo $f['icon']; ?>
        </svg>
      </div>

      <!-- Title + badge -->
      <div class="flex items-center gap-2 mb-1.5">
        <h3 class="font-semibold text-white text-sm"><?php echo $f['title']; ?></h3>
        <?php if ($f['badge']): ?>
          <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-md bg-<?php echo $f['badge']['color']; ?>/15 text-<?php echo $f['badge']['color']; ?> border border-<?php echo $f['badge']['color']; ?>/20"><?php echo $f['badge']['label']; ?></span>
        <?php endif; ?>
      </div>

      <p class="text-xs text-white/40 leading-relaxed mb-4"><?php echo $f['desc']; ?></p>

      <!-- CTA -->
      <div class="mt-auto text-xs font-semibold text-primary/60 group-hover:text-primary transition-colors"><?php echo $f['cta']; ?></div>

    </a>
    <?php endforeach; ?>

  </div>
</section>

<!-- ─── Upgrade Banner (non-premium only) ─── -->
<?php if (!$isPremium): ?>
<section class="mb-6">
  <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-primary/20 via-emerald-700/15 to-primary/10 border border-primary/20 p-8">

    <!-- Decorative blob -->
    <div class="pointer-events-none absolute -right-10 -top-10 w-52 h-52 rounded-full bg-primary/15 blur-3xl"></div>

    <div class="relative flex flex-col sm:flex-row items-center gap-6">
      <div class="w-14 h-14 rounded-2xl bg-primary/20 flex items-center justify-center shrink-0">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-primary" viewBox="0 0 20 20" fill="currentColor">
          <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
        </svg>
      </div>
      <div class="flex-1 text-center sm:text-left">
        <h3 class="text-xl font-bold text-white mb-1">Unlock the Mock Platform</h3>
        <p class="text-sm text-white/50">Get unlimited access to the Kenyan identity generator, downloadable profiles, and all future features.</p>
      </div>
      <a href="app.php?page=upgrade" class="btn btn-primary-gradient shrink-0 px-6 gap-2 shadow-lg shadow-primary/20">
        Get Premium — KES 500
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
        </svg>
      </a>
    </div>
  </div>
</section>
<?php endif; ?>
