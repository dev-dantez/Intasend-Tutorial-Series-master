<?php
require_once __DIR__ . '/../services/PaymentService.php';

use IntaSend\IntaSendPHP\Customer;

$error = null;
$checkoutUrl = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_checkout'])) {
    verify_csrf();
    
    $amount = validate_amount($_POST['amount'] ?? 0);
    $currency = sanitize_input($_POST['currency'] ?? 'KES');
    
    if (!$amount || $amount < 10) {
        $error = 'Invalid amount. Minimum is 10';
    } else {
        try {
            $user = get_logged_in_user();
            
            $customer = new Customer();
            $customer->first_name = explode(' ', $user['name'])[0] ?? 'Guest';
            $customer->last_name = explode(' ', $user['name'])[1] ?? 'User';
            $customer->email = $user['email'];
            $customer->country = "KE";
            $customer->city = "Nairobi";
            $customer->address = "Online";
            $customer->zipcode = "00100";
            $customer->state = "Nairobi";
            
            $paymentService = new PaymentService();
            $result = $paymentService->createCheckout(
                $user['id'],
                $amount,
                $currency,
                $customer,
                'CHECKOUT'
            );
            
            if ($result['success'] && isset($result['checkout_url'])) {
                $checkoutUrl = $result['checkout_url'];
            } else {
                $error = 'Failed to create checkout session';
            }
            
        } catch (Exception $e) {
            $error = 'Error: ' . $e->getMessage();
            log_activity("Checkout creation failed", [
                'user_id' => $user['id'],
                'error' => $e->getMessage()
            ]);
        }
    }
}
?>

<div class="max-w-4xl mx-auto mb-8 animate-fade-in">
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold mb-2">Checkout Link</h2>
        <p class="opacity-70">Redirect your customers to a hosted, secure payment page.</p>
    </div>
    
    <?php if ($error): ?>
        <div class="alert alert-error shadow-lg mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span><?php echo htmlspecialchars($error); ?></span>
        </div>
    <?php endif; ?>
    
    <?php if ($checkoutUrl): ?>
        <div class="glass-panel p-8 rounded-2xl text-center max-w-lg mx-auto">
            <div class="flex justify-center mb-4">
                <div class="p-3 bg-success/20 rounded-full text-success">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold mb-2">Checkout Session Created</h3>
            <p class="opacity-70 mb-6">Your payment link is ready. Proceed to the secure payment page.</p>
            <a href="<?php echo htmlspecialchars($checkoutUrl); ?>" class="btn btn-primary-gradient w-full max-w-xs gap-2">
                Proceed to Payment 
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </a>
        </div>
    <?php else: ?>
        <div class="glass-panel p-8 rounded-2xl max-w-lg mx-auto">
            <form method="POST" action="">
                <?php echo csrf_field(); ?>
                
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text font-medium">Amount</span>
                    </label>
                    <input type="number" name="amount" min="10" step="1" placeholder="100" 
                           class="input input-bordered input-premium" required
                           value="<?php echo htmlspecialchars($_POST['amount'] ?? '100'); ?>">
                    <label class="label">
                        <span class="label-text-alt">Minimum: 10</span>
                    </label>
                </div>
                
                <div class="form-control mb-6">
                    <label class="label">
                        <span class="label-text font-medium">Currency</span>
                    </label>
                    <select name="currency" class="select select-bordered input-premium">
                        <option value="KES">KES - Kenyan Shilling</option>
                        <option value="USD">USD - US Dollar</option>
                        <option value="EUR">EUR - Euro</option>
                        <option value="GBP">GBP - British Pound</option>
                    </select>
                </div>
                
                <button type="submit" name="create_checkout" class="btn btn-primary-gradient w-full">
                    Create Checkout Session
                </button>
            </form>
        </div>
    <?php endif; ?>
</div>
