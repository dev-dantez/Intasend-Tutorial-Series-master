<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../services/PaymentService.php';

SessionManager::init();

$signature = $_GET['signature'] ?? null;
$checkoutId = $_GET['checkout_id'] ?? null;
$invoiceId = $_GET['invoice_id'] ?? null;

if (!$signature) {
    include_once __DIR__ . '/../header.php';
    echo '<div class="alert alert-error max-w-xl mx-auto mt-12">Invalid callback - no signature provided</div>';
    include_once __DIR__ . '/../footer.php';
    exit();
}

try {
    $paymentService = new PaymentService();
    $response = $paymentService->verifyPayment($signature, $checkoutId, $invoiceId);
    
    if (isset($response->invoice) && $response->invoice->state === 'COMPLETE') {
        // Payment successful
        $invoice = $response->invoice;
        
        // Check if this is a premium upgrade
        if (isset($invoice->api_ref) && strpos($invoice->api_ref, 'PREM-') === 0) {
            // Upgrade user to premium
            $userId = SessionManager::get('user_id');
            if ($userId) {
                require_once __DIR__ . '/../models/User.php';
                $userModel = new User();
                $userModel->updatePremiumStatus($userId, true);
                
                SessionManager::set('is_premium', true);
                SessionManager::regenerate();
                
                log_activity("Premium upgrade successful", [
                    'user_id' => $userId,
                    'reference' => $invoice->api_ref
                ]);
            }
        }
        
        include_once __DIR__ . '/../header.php';
        ?>
        <div class="max-w-2xl mx-auto mt-12 animate-fade-in">
            <div class="card bg-base-100 shadow-xl card-premium border-success/50">
                <div class="card-body text-center">
                    <div class="flex justify-center mb-4">
                        <div class="p-4 bg-success/20 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </div>
                    
                    <h2 class="card-title text-3xl font-bold justify-center text-success mb-4">
                        Payment Successful!
                    </h2>
                    
                    <p class="text-lg mb-6">Your payment has been processed successfully.</p>
                    
                    <div class="bg-base-200 p-4 rounded-lg text-left mb-6">
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <span class="opacity-70">Amount:</span>
                            <span class="font-bold"><?php echo number_format($invoice->value, 2); ?> <?php echo $invoice->currency; ?></span>
                            
                            <span class="opacity-70">Reference:</span>
                            <span class="font-mono text-xs"><?php echo htmlspecialchars($invoice->api_ref ?? 'N/A'); ?></span>
                            
                            <span class="opacity-70">Invoice ID:</span>
                            <span class="font-mono text-xs"><?php echo htmlspecialchars($invoice->invoice_id ?? 'N/A'); ?></span>
                        </div>
                    </div>
                    
                    <div class="flex gap-4 justify-center">
                        <a href="../app.php" class="btn btn-primary">Go to Dashboard</a>
                        <?php if (strpos($invoice->api_ref ?? '', 'PREM-') === 0): ?>
                            <a href="../app.php?page=services" class="btn btn-success">Access Premium Features</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
        include_once __DIR__ . '/../footer.php';
        
    } else {
        // Payment failed or pending
        $status = $response->invoice->state ?? 'UNKNOWN';
        
        include_once __DIR__ . '/../header.php';
        ?>
        <div class="max-w-xl mx-auto mt-12 animate-fade-in">
            <div class="card bg-base-100 shadow-xl card-premium border-warning/50">
                <div class="card-body text-center">
                    <h2 class="card-title text-2xl font-bold justify-center text-warning mb-4">
                        Payment <?php echo ucfirst(strtolower($status)); ?>
                    </h2>
                    
                    <p class="mb-6">Your payment status: <strong><?php echo htmlspecialchars($status); ?></strong></p>
                    
                    <div class="alert alert-warning text-sm">
                        <?php if ($status === 'PENDING'): ?>
                            Your payment is being processed. Please check back later.
                        <?php else: ?>
                            The payment could not be completed. Please try again.
                        <?php endif; ?>
                    </div>
                    
                    <div class="mt-6">
                        <a href="../app.php" class="btn btn-outline">Return to Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
        <?php
        include_once __DIR__ . '/../footer.php';
    }
    
} catch (Exception $e) {
    log_activity("Callback processing failed", [
        'signature' => $signature,
        'error' => $e->getMessage()
    ]);
    
    include_once __DIR__ . '/../header.php';
    ?>
    <div class="max-w-xl mx-auto mt-12 animate-fade-in">
        <div class="card bg-base-100 shadow-xl card-premium border-error/50">
            <div class="card-body text-center">
                <h2 class="card-title text-2xl font-bold justify-center text-error mb-4">
                    Verification Failed
                </h2>
                
                <p class="mb-6">We could not verify your payment.</p>
                
                <div class="alert alert-error text-sm text-left">
                    <?php echo htmlspecialchars($e->getMessage()); ?>
                </div>
                
                <div class="mt-6">
                    <a href="../app.php" class="btn btn-outline">Return to Dashboard</a>
                </div>
            </div>
        </div>
    </div>
    <?php
    include_once __DIR__ . '/../footer.php';
}
