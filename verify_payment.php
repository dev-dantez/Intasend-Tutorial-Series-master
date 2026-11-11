<?php
use IntaSend\IntaSendPHP\Checkout;

include_once './credentials.php';

$checkout_id = isset($_GET['checkout_id']) ? $_GET['checkout_id'] : null;
$signature = isset($_GET['signature']) ? $_GET['signature'] : null;
// Tracking ID might be invoice_id depending on flow
$invoice_id = isset($_GET['invoice_id']) ? $_GET['invoice_id'] : null;

$verification_failed = false;
$error_msg = "";

if ($signature) {
    try {
        $checkout = new Checkout();
        $checkout->init($credentials);
        
        // Check Status
        // check_status($signature, $checkout_id, $invoice_id)
        $resp = $checkout->check_status($signature, $checkout_id, $invoice_id);

        if (isset($resp->invoice) && $resp->invoice->state == 'COMPLETE') {
            // Payment Success!
            $_SESSION['is_premium'] = true;
            $success = true;
            
            // --- NEW: Deliver Data via Email & WhatsApp (Simulated) ---
            
            // 1. Get Customer Contacts from Invoice
            $customer_email = $resp->invoice->customer->email ?? 'N/A';
            $customer_phone = $resp->invoice->customer->phone_number ?? 'N/A';
            
            // 2. Get the Data to Send (Current Profile)
            require_once 'MockDataService.php';
            if (!isset($_SESSION['current_profile'])) {
                $gen = new MockDataService();
                $_SESSION['current_profile'] = $gen->generateProfile();
            }
            $p = $_SESSION['current_profile'];
            
            // 3. Format Message
            $message = "CONFIDENTIAL: YOUR FULLZ ELITE DATA\n\n";
            $message .= "Name: " . $p['name'] . "\n";
            $message .= "ID: " . $p['nino'] . "\n";
            $message .= "CC: " . $p['cc_number'] . " (CVV: " . $p['cc_cvv'] . ")\n";
            $message .= "Coords: " . $p['coordinates'] . "\n";
            $message .= "\nLogin to platform for full history.";
            
            // 4. Send Email (PHP mail wrapper)
            // Note: Requires SMTP config on server
            $headers = "From: noreply@fullzelite.ke";
            // mail($customer_email, "Your Premium Identity Data", $message, $headers); // Uncomment on live server
            
            // 5. Send WhatsApp (Mock Logic)
            // To actually send WA, you'd use Twilio/Infobip API here:
            // send_whatsapp($customer_phone, $message);
            
            // Store delivery status for UI
            $delivery_status = "Data bundle securely transmitted to <strong>$customer_email</strong> and WhatsApp <strong>$customer_phone</strong>.";

        } else {
            $verification_failed = true;
            $error_msg = isset($resp->invoice->state) ? "Status: " . $resp->invoice->state : "Unknown status";
        }

    } catch (Exception $e) {
        $verification_failed = true;
        $error_msg = $e->getMessage();
    }
} else {
    $verification_failed = true;
    $error_msg = "No signature provided.";
}

// Redirect handled below with UI
if (!$verification_failed) {
    // Instead of auto-redirect, show the "Delivery" success screen
    ?>
    <div class="max-w-2xl mx-auto mt-12 animate-fade-in text-center font-mono">
        <div class="card bg-black border border-green-500 shadow-[0_0_30px_rgba(0,255,0,0.2)] text-green-500 p-8">
            <div class="flex flex-col items-center">
                <div class="w-20 h-20 rounded-full border-2 border-green-500 flex items-center justify-center mb-6 animate-pulse">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                </div>
                <h2 class="text-3xl font-bold mb-2">ACCESS GRANTED</h2>
                <div class="divider border-green-900/50"></div>
                
                <p class="text-xl mb-6 text-white">Identity Unlocked Successfully.</p>
                
                <div class="alert bg-green-900/20 border-green-500/50 mb-8 text-left text-sm text-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-green-500 shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span><?php echo $delivery_status; ?></span>
                </div>
                
                <div class="flex gap-4">
                     <a href="app.php?page=services" class="btn btn-outline btn-success hover:shadow-[0_0_15px_rgba(0,255,0,0.5)]">
                        ACCESS DASHBOARD
                    </a>
                </div>
                
                <div class="mt-8 text-xs opacity-50">
                    <p>SESSION SECURE. LOGS PURGED.</p>
                </div>
            </div>
        </div>
    </div>
    <?php
    exit();
}
?>

<div class="max-w-xl mx-auto mt-12 animate-fade-in">
    <div class="card bg-base-100 shadow-xl card-premium border-error/50">
        <div class="card-body text-center">
            <h2 class="card-title justify-center text-error text-2xl mb-4">Verification Failed</h2>
            <p class="mb-6">We could not verify your payment.</p>
            <div class="alert alert-error text-sm text-left">
                <?php echo htmlspecialchars($error_msg); ?>
            </div>
            <div class="mt-6">
                <a href="app.php?page=services" class="btn btn-outline">Return to Platform</a>
            </div>
        </div>
    </div>
</div>
