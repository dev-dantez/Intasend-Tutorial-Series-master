<?php
use IntaSend\IntaSendPHP\Checkout;
use IntaSend\IntaSendPHP\Customer;

include_once './credentials.php';

try {
    $checkout = new Checkout();
    $checkout->init($credentials);

    // Define a simplistic dummy customer for this digital product example
    // In a real app, you'd get this from the logged-in user session
    $customer = new Customer();
    $customer->first_name = "Guest";
    $customer->last_name = "User";
    $customer->email = "user@example.com";
    $customer->phone_number = "254700000000";
    $customer->country = "KE";

    $amount = 500;
    $currency = "KES";
    
    // Construct the absolute callback URL
    // Assuming app is running on localhost or getting host from server
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $script_path = str_replace('\\', '/', dirname($_SERVER['PHP_SELF'])); // Fix Windows backslashes
    $host_url = $protocol . "://" . $_SERVER['HTTP_HOST'] . $script_path;
    
    // Ensure no trailing slash to avoid double slash
    $host_url = rtrim($host_url, '/');
    
    $redirect_url = $host_url . "/app.php?page=verify_payment";

    $ref_order_number = "PREM-" . uniqid();

    // Create Checkout Session
    // amount, currency, customer, host, redirect_url, api_ref, comment, method
    $resp = $checkout->create(
        $amount,
        $currency,
        $customer,
        $host_url,
        $redirect_url,
        $ref_order_number,
        "Premium Data Plan",
        null
    );

    if (isset($resp->url) && !empty($resp->url)) {
        // Redirect user to IntaSend
        header("Location: " . $resp->url);
        exit();
    } else {
        throw new Exception("Could not generate checkout URL. Response: " . json_encode($resp));
    }

} catch (Exception $e) {
    echo '<div class="max-w-xl mx-auto mt-12 alert alert-error shadow-lg">
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span>Error initiating upgrade: ' . $e->getMessage() . '</span>
            </div>
          </div>';
}
