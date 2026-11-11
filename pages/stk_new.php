<?php
require_once __DIR__ . '/../services/PaymentService.php';

function process_stk_push_secure() {
    if (isset($_POST['deposit'])) {
        verify_csrf();
        
        $phoneNumber = sanitize_input($_POST['phonenumber'] ?? '');
        $amount = sanitize_input($_POST['amount'] ?? '');
        
        // Validate inputs
        $validPhone = validate_phone($phoneNumber);
        $validAmount = validate_amount($amount);
        
        if (!$validPhone) {
            return ['class' => 'alert-error', 'message' => 'Invalid phone number format. Use 254XXXXXXXXX or 07XXXXXXXX'];
        }
        
        if (!$validAmount || $validAmount < 10) {
            return ['class' => 'alert-error', 'message' => 'Invalid amount. Minimum is KES 10'];
        }
        
        try {
            $userId = SessionManager::get('user_id');
            $paymentService = new PaymentService();
            
            $result = $paymentService->initiateSTKPush(
                $userId,
                $validPhone,
                $validAmount,
                'Customer payment'
            );
            
            if ($result['success'] && isset($result['response']->invoice)) {
                $invoice = $result['response']->invoice;
                
                if ($invoice->state == 'PROCESSING' || $invoice->state == 'PENDING') {
                    return [
                        'class' => 'alert-info',
                        'message' => 'STK Push sent! Check your phone to complete payment. Reference: ' . $result['reference']
                    ];
                } elseif ($invoice->state == 'COMPLETE') {
                    return [
                        'class' => 'alert-success',
                        'message' => 'Payment successful!'
                    ];
                } else {
                    return [
                        'class' => 'alert-error',
                        'message' => 'Payment failed: ' . ($invoice->note ?? 'Unknown error')
                    ];
                }
            } else {
                return ['class' => 'alert-error', 'message' => 'Failed to initiate STK push'];
            }
            
        } catch (Exception $e) {
            log_activity("STK Push error", [
                'user_id' => SessionManager::get('user_id'),
                'error' => $e->getMessage()
            ]);
            return ['class' => 'alert-error', 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    return null;
}

$status_alert = process_stk_push_secure();
?>

<div class="max-w-4xl mx-auto animate-fade-in">
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold mb-2">M-Pesa STK Push</h2>
        <p class="opacity-70">Trigger a real-time payment prompt to a customer's phone.</p>
    </div>

    <div class="hero rounded-2xl overflow-hidden glass-panel p-0">
        <div class="hero-content flex-col lg:flex-row-reverse w-full p-8 lg:p-12 gap-12">
            <div class="text-center lg:text-left flex-1">
                <?php if ($status_alert): ?>
                    <div class="alert <?php echo $status_alert['class']; ?> shadow-lg mb-6 border-none text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span><?php echo $status_alert['message']; ?></span>
                    </div>
                <?php endif; ?>
                
                <div class="relative">
                    <div class="absolute -inset-1 rounded-full bg-primary/20 blur-xl opacity-50"></div>
                    <img src="./pay.svg" alt="Mobile Payment Illustration" class="relative max-w-sm w-full mx-auto lg:mx-0 drop-shadow-2xl">
                </div>
                
                <div class="mt-8 space-y-2 opacity-70 hidden lg:block text-sm">
                    <p>✓ Instant notification to customer's phone.</p>
                    <p>✓ Secure and seamless transaction.</p>
                    <p>✓ Real-time status tracking.</p>
                </div>
            </div>
            
            <div class="card shrink-0 w-full max-w-md shadow-2xl bg-base-100/50 backdrop-blur border border-white/5">
                <form class="card-body" method="POST" action="app.php?page=stk">
                    <?php echo csrf_field(); ?>
                    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Phone Number</span>
                        </label>
                        <input type="text" placeholder="254700000000 or 0700000000" 
                               class="input input-bordered input-premium" 
                               name="phonenumber" required 
                               pattern="[0-9]+" 
                               value="<?php echo htmlspecialchars($_POST['phonenumber'] ?? ''); ?>" />
                        <label class="label">
                            <span class="label-text-alt">Format: 254XXXXXXXXX or 07XXXXXXXX</span>
                        </label>
                    </div>
                    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Amount (KES)</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">KES</span>
                            <input type="number" placeholder="10" min="10" step="1"
                                   class="input input-bordered input-premium pl-12" 
                                   name="amount" required 
                                   value="<?php echo htmlspecialchars($_POST['amount'] ?? ''); ?>" />
                        </div>
                        <label class="label">
                            <span class="label-text-alt">Minimum: KES 10</span>
                        </label>
                    </div>
                    
                    <div class="form-control mt-8">
                        <button class="btn btn-primary-gradient w-full h-12 text-lg font-semibold" type="submit" name="deposit">
                            Send Request
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
