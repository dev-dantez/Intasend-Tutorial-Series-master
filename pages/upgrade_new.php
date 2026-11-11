<?php
require_once __DIR__ . '/../services/PaymentService.php';

use IntaSend\IntaSendPHP\Customer;

try {
    $user = get_logged_in_user();

    // ── Already premium ──────────────────────────────────────────────────────
    if ($user['is_premium']) {
        ?>
        <div style="max-width:520px;margin:60px auto;animation:fadeIn .55s cubic-bezier(.16,1,.3,1) forwards;opacity:0;transform:translateY(12px);">
            <div style="background:#0e1a18;border:1px solid rgba(20,184,166,0.35);border-radius:16px;padding:40px 32px;text-align:center;box-shadow:0 8px 40px rgba(0,0,0,0.4);">
                <div style="display:inline-flex;align-items:center;justify-content:center;width:72px;height:72px;border-radius:50%;background:rgba(20,184,166,0.15);margin-bottom:20px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="none" viewBox="0 0 24 24" stroke="#14b8a6" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2 style="font-size:1.75rem;font-weight:800;color:#f0fdf9;margin:0 0 12px;">You&rsquo;re Premium!</h2>
                <p style="color:rgba(240,253,249,0.6);margin:0 0 28px;line-height:1.6;">
                    You already have full access to every feature in the ID Generator.
                </p>
                <a href="/app.php?page=services"
                   style="display:inline-block;background:linear-gradient(135deg,#10b981 0%,#059669 100%);color:#fff;font-weight:700;font-size:.95rem;padding:14px 32px;border-radius:10px;text-decoration:none;transition:filter .2s,transform .2s;letter-spacing:-.01em;">
                    Open ID Generator
                </a>
            </div>
        </div>
        <?php
        return;
    }

    // ── Confirmed → run checkout ─────────────────────────────────────────────
    if (isset($_GET['confirm'])) {
        $customer = new Customer();
        $nameParts = explode(' ', trim($user['name'] ?? ''));
        $customer->first_name = $nameParts[0] ?? 'Guest';
        $customer->last_name   = $nameParts[1] ?? 'User';
        $customer->email       = $user['email'];

        $rawPhone = $user['phone'] ?? '';
        if (!empty($rawPhone)) {
            $rawPhone = preg_replace('/\D/', '', $rawPhone);
            if (strlen($rawPhone) === 9) {
                $rawPhone = '254' . $rawPhone;
            } elseif (strlen($rawPhone) === 10 && $rawPhone[0] === '0') {
                $rawPhone = '254' . substr($rawPhone, 1);
            }
            $customer->phone_number = $rawPhone;
        } else {
            $customer->phone_number = null;
        }

        $customer->country = 'KE';
        $customer->city    = 'Nairobi';
        $customer->address = 'N/A';
        $customer->state   = 'Nairobi';
        $customer->zipcode = '00100';

        $paymentService = new PaymentService();
        $result = $paymentService->createCheckout(
            $user['id'],
            500,
            'KES',
            $customer,
            'PREMIUM'
        );

        if ($result['success'] && isset($result['checkout_url'])) {
            header('Location: ' . $result['checkout_url']);
            exit();
        } else {
            throw new Exception('Could not generate checkout URL');
        }
    }

    // ── Default → pricing / sales page ──────────────────────────────────────
    ?>

    <style>
        @keyframes upgradeFadeIn {
            to { opacity: 1; transform: translateY(0); }
        }
        .upgrade-fade { animation: upgradeFadeIn .6s cubic-bezier(.16,1,.3,1) forwards; opacity: 0; transform: translateY(16px); }
        .upgrade-fade-2 { animation: upgradeFadeIn .6s .1s cubic-bezier(.16,1,.3,1) forwards; opacity: 0; transform: translateY(16px); }
        .upgrade-fade-3 { animation: upgradeFadeIn .6s .2s cubic-bezier(.16,1,.3,1) forwards; opacity: 0; transform: translateY(16px); }
        .pay-btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 10px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #fff; font-weight: 700; font-size: 1.1rem;
            padding: 16px 40px; border-radius: 12px; text-decoration: none;
            border: none; cursor: pointer; letter-spacing: -.015em;
            transition: filter .2s, transform .2s, box-shadow .2s;
            box-shadow: 0 4px 24px rgba(16,185,129,0.3);
            position: relative; overflow: hidden;
        }
        .pay-btn::after {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(to bottom, rgba(255,255,255,0.1), transparent);
            pointer-events: none;
        }
        .pay-btn:hover { filter: brightness(1.1); transform: translateY(-2px); box-shadow: 0 8px 32px rgba(16,185,129,0.45); }
        .pay-btn:active { transform: translateY(0); }
        .tier-col {
            flex: 1 1 0;
            min-width: 240px;
            background: #0e1a18;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 16px;
            padding: 28px 24px;
        }
        .tier-col.premium-col {
            border-color: rgba(20,184,166,0.4);
            background: rgba(20,184,166,0.05);
            box-shadow: 0 0 0 1px rgba(20,184,166,0.15), 0 12px 40px rgba(20,184,166,0.08);
        }
        .tier-row {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            font-size: .88rem;
            color: rgba(240,253,249,0.75);
        }
        .tier-row:last-child { border-bottom: none; }
        .tier-row .icon-check { color: #14b8a6; flex-shrink: 0; margin-top: 2px; }
        .tier-row .icon-lock  { color: rgba(240,253,249,0.25); flex-shrink: 0; margin-top: 2px; }
        .tier-row.locked-row  { color: rgba(240,253,249,0.3); }
    </style>

    <div style="max-width:860px;margin:48px auto;padding:0 16px;">

        <!-- Headline -->
        <div class="upgrade-fade" style="text-align:center;margin-bottom:48px;">
            <div style="display:inline-block;background:rgba(20,184,166,0.1);border:1px solid rgba(20,184,166,0.25);border-radius:999px;padding:6px 18px;font-size:.8rem;font-weight:600;color:#14b8a6;letter-spacing:.06em;text-transform:uppercase;margin-bottom:20px;">
                One-Time Upgrade
            </div>
            <h1 style="font-size:clamp(1.9rem,5vw,2.9rem);font-weight:800;color:#f0fdf9;margin:0 0 16px;letter-spacing:-.03em;line-height:1.1;">
                Unlock the full<br>
                <span style="background:linear-gradient(135deg,#10b981 0%,#059669 100%);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;">ID Generator</span>
            </h1>
            <p style="color:rgba(240,253,249,0.5);font-size:1.05rem;max-width:460px;margin:0 auto;line-height:1.65;">
                Free gives you a glimpse. Premium gives you the whole identity — email, phone, address, National ID, financial data, and a full professional CV.
            </p>
        </div>

        <!-- Price callout -->
        <div class="upgrade-fade-2" style="text-align:center;margin-bottom:44px;">
            <div style="display:inline-block;background:#0e1a18;border:1px solid rgba(20,184,166,0.3);border-radius:20px;padding:28px 48px;">
                <div style="color:rgba(240,253,249,0.4);font-size:.8rem;font-weight:600;letter-spacing:.08em;text-transform:uppercase;margin-bottom:6px;">One-time price</div>
                <div style="font-size:3.5rem;font-weight:800;color:#f0fdf9;letter-spacing:-.04em;line-height:1;">
                    KES&nbsp;<span style="background:linear-gradient(135deg,#10b981 0%,#059669 100%);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;">500</span>
                </div>
                <div style="color:rgba(240,253,249,0.35);font-size:.82rem;margin-top:8px;">One-time payment &bull; No subscription &bull; Access never expires</div>
            </div>
        </div>

        <!-- Comparison columns -->
        <div class="upgrade-fade-2" style="display:flex;gap:20px;flex-wrap:wrap;margin-bottom:44px;">

            <!-- Free column -->
            <div class="tier-col">
                <div style="font-size:.7rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:rgba(240,253,249,0.35);margin-bottom:4px;">Free</div>
                <div style="font-size:1.15rem;font-weight:700;color:#f0fdf9;margin-bottom:20px;">Basic Preview</div>

                <div class="tier-row">
                    <svg class="icon-check" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Name, nationality, age, height, blood type
                </div>
                <div class="tier-row">
                    <svg class="icon-check" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Username, city, zip, coordinates
                </div>
                <div class="tier-row">
                    <svg class="icon-check" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Card type &amp; expiry (no full number)
                </div>
                <div class="tier-row">
                    <svg class="icon-check" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Wallet balance
                </div>

                <div style="margin-top:12px;border-top:1px solid rgba(255,255,255,0.06);padding-top:12px;">
                    <div class="tier-row locked-row">
                        <svg class="icon-lock" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Email masked &mdash; <code style="font-size:.78rem;">abc****@example.com</code>
                    </div>
                    <div class="tier-row locked-row">
                        <svg class="icon-lock" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Phone truncated &mdash; first 8 digits only
                    </div>
                    <div class="tier-row locked-row">
                        <svg class="icon-lock" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        National ID / NINO &mdash; blurred
                    </div>
                    <div class="tier-row locked-row">
                        <svg class="icon-lock" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Street address &mdash; hidden
                    </div>
                    <div class="tier-row locked-row">
                        <svg class="icon-lock" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Card number masked &mdash; <code style="font-size:.78rem;">4xxx **** **** xxxx</code>
                    </div>
                    <div class="tier-row locked-row">
                        <svg class="icon-lock" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        CVV &mdash; shown as <code style="font-size:.78rem;">***</code>
                    </div>
                    <div class="tier-row locked-row">
                        <svg class="icon-lock" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Professional profile &amp; CV
                    </div>
                </div>
            </div>

            <!-- Premium column -->
            <div class="tier-col premium-col">
                <div style="font-size:.7rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#14b8a6;margin-bottom:4px;">Premium</div>
                <div style="font-size:1.15rem;font-weight:700;color:#f0fdf9;margin-bottom:20px;">Full Identity</div>

                <div class="tier-row">
                    <svg class="icon-check" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#14b8a6" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Everything in Free
                </div>
                <div class="tier-row">
                    <svg class="icon-check" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#14b8a6" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Full email address revealed
                </div>
                <div class="tier-row">
                    <svg class="icon-check" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#14b8a6" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Full phone number
                </div>
                <div class="tier-row">
                    <svg class="icon-check" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#14b8a6" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    National ID &amp; NINO number
                </div>
                <div class="tier-row">
                    <svg class="icon-check" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#14b8a6" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Full street address
                </div>
                <div class="tier-row">
                    <svg class="icon-check" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#14b8a6" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Full credit card number
                </div>
                <div class="tier-row">
                    <svg class="icon-check" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#14b8a6" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    CVV code
                </div>
                <div class="tier-row">
                    <svg class="icon-check" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#14b8a6" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Job title &amp; 2–4 work experiences with descriptions
                </div>
                <div class="tier-row">
                    <svg class="icon-check" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#14b8a6" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Education: degree, major, university
                </div>
                <div class="tier-row">
                    <svg class="icon-check" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#14b8a6" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Skills list &amp; downloadable CV
                </div>
            </div>

        </div>

        <!-- CTA -->
        <div class="upgrade-fade-3" style="text-align:center;">
            <a href="/app.php?page=upgrade&confirm=1" class="pay-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Pay KES 500 &mdash; Unlock Now
            </a>
            <p style="color:rgba(240,253,249,0.3);font-size:.8rem;margin-top:14px;">
                One-time payment &bull; No subscription &bull; Access never expires
            </p>
            <p style="color:rgba(240,253,249,0.2);font-size:.75rem;margin-top:6px;">
                Secure checkout powered by IntaSend &bull; M-Pesa, card &amp; more accepted
            </p>
        </div>

    </div>

    <?php

} catch (Exception $e) {
    log_activity('Premium upgrade initiation failed', [
        'user_id' => SessionManager::get('user_id'),
        'error'   => $e->getMessage()
    ]);
    ?>
    <div style="max-width:600px;margin:48px auto;background:#0e1a18;border:1px solid rgba(239,68,68,0.3);border-radius:16px;padding:28px 24px;display:flex;gap:14px;align-items:flex-start;">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="#ef4444" stroke-width="2" style="flex-shrink:0;margin-top:2px;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <div style="color:#f0fdf9;font-weight:600;margin-bottom:4px;">Could not initiate upgrade</div>
            <div style="color:rgba(240,253,249,0.5);font-size:.9rem;"><?php echo htmlspecialchars($e->getMessage()); ?></div>
            <a href="/app.php?page=upgrade" style="display:inline-block;margin-top:14px;color:#14b8a6;font-size:.88rem;text-decoration:none;">← Try again</a>
        </div>
    </div>
    <?php
}
