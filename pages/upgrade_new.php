<?php
require_once __DIR__ . '/../services/PaymentService.php';

use IntaSend\IntaSendPHP\Customer;

try {
    $user = get_logged_in_user();
    
    // Check if already premium
    if ($user['is_premium']) {
        ?>
        <div class="max-w-xl mx-auto mt-12 animate-fade-in">
            <div class="card bg-base-100 shadow-xl card-premium border-success/50">
                <div class="card-body text-center">
                    <div class="flex justify-center mb-4">
                        <div class="p-4 bg-success/20 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <h2 class="card-title text-3xl font-bold justify-center text-success mb-4">
                        You're Premium!
                    </h2>
                    <p class="text-lg mb-6">You already have premium access to all features.</p>
                    <a href="app.php?page=services" class="btn btn-primary">Access Premium Features</a>
                </div>
            </div>
        </div>
        <?php
        return;
    }
    
    $customer = new Customer();
    $nameParts = explode(' ', trim($user['name'] ?? ''));
    $customer->first_name = $nameParts[0] ?? 'Guest';
    $customer->last_name = $nameParts[1] ?? 'User';
    $customer->email = $user['email'];
    // Use the user's real phone if available, normalised to 254XXXXXXXXX
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
    $customer->country = "KE";
    $customer->city = "Nairobi";
    $customer->address = "N/A";
    $customer->state = "Nairobi";
    $customer->zipcode = "00100";
    
    $amount = 500;
    $currency = "KES";
    
    $paymentService = new PaymentService();
    $result = $paymentService->createCheckout(
        $user['id'],
        $amount,
        $currency,
        $customer,
        'PREMIUM'
    );
    
    if ($result['success'] && isset($result['checkout_url'])) {
        // Redirect to IntaSend
        header("Location: " . $result['checkout_url']);
        exit();
    } else {
        throw new Exception("Could not generate checkout URL");
    }
    
} catch (Exception $e) {
    log_activity("Premium upgrade initiation failed", [
        'user_id' => SessionManager::get('user_id'),
        'error' => $e->getMessage()
    ]);
    ?>
    <div class="max-w-xl mx-auto mt-12 alert alert-error shadow-lg">
        <div>
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Error initiating upgrade: <?php echo htmlspecialchars($e->getMessage()); ?></span>
        </div>
    </div>
    <?php
}
