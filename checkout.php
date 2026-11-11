<?php
// checkout.php

// This file is included by app.php
// The autoloader is already included in the files that need it.

use IntaSend\IntaSendPHP\Checkout;
use IntaSend\IntaSendPHP\Customer;

// It's better to include credentials securely, for example from a file outside the web root.
// For this example, we'll keep it simple.
include_once('credentials.php');

if (empty($credentials['publishable_key'])) {
    echo '<div class="alert alert-error">Please add your Publishable Key to credentials.php</div>';
} else {
    ?>
    <div class="max-w-4xl mx-auto mb-8 text-center animate-fade-in">
        <h2 class="text-3xl font-bold mb-2">Checkout Link</h2>
        <p class="opacity-70">Redirect your customers to a hosted, secure payment page.</p>
    </div>
    <?php
    //create app

    $customer = new Customer();
    $customer->first_name = "Joe";
    $customer->last_name = "Doe";
    $customer->email = "joe@doe.com";
    $customer->country = "KE";
    $customer->city = "Nairobi";
    $customer->address = "Apt 123, Westland road";
    $customer->zipcode = "0100";
    $customer->state = "Nairobi";

    $amount = 10;
    $currency = "KES";

    // Add your website and redirect url where the user will be redirected on success
    // For this demo, we'll redirect back to our app
    $host = "http://$_SERVER[HTTP_HOST]";
    $redirect_url = $host . str_replace('app.php', 'callback.php', $_SERVER['REQUEST_URI']);

    $ref_order_number = "test-order-" . time();

    try {
        $checkout = new Checkout();
        $checkout->init($credentials);
        $resp = $checkout->create(
            $amount,
            $currency,
            $customer,
            $host,
            $redirect_url,
            $ref_order_number,
            null, // comment
            null  // method
        );

        if (isset($resp->url) && !empty($resp->url)) {
            echo '<div class="glass-panel p-8 rounded-2xl text-center max-w-lg mx-auto animate-fade-in">';
            echo '<div class="flex justify-center mb-4"><div class="p-3 bg-success/20 rounded-full text-success"><svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg></div></div>';
            echo '<h3 class="text-2xl font-bold mb-2">Checkout Session Created</h3>';
            echo '<p class="opacity-70 mb-6">Your payment link is ready. Proceed to the secure payment page.</p>';
            echo '<a href="' . htmlspecialchars($resp->url) . '" class="btn btn-primary-gradient w-full max-w-xs gap-2">Proceed to Payment <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg></a>';
            echo '</div>';
        } else {
            echo '<div class="alert alert-error shadow-lg mb-6">Could not create checkout.</div>';
            echo '<div class="mockup-code bg-base-100 shadow-xl border border-white/5 text-sm">';
            echo '<pre class="text-warning"><code>';
            print_r($resp);
            echo '</code></pre>';
            echo '</div>';
        }
    } catch (Exception $e) {
        echo '<div class="alert alert-error">An error occurred: ' . $e->getMessage() . '</div>';
    }
}

?>
