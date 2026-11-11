<?php
include_once('header.php');

echo '<h2>Checkout Callback</h2>';
echo '<p>This is the callback URL for the checkout flow. The payment gateway redirects the user to this page after the payment is completed.</p>';

if (isset($_GET['tracking_id']) && isset($_GET['signature'])) {
    echo '<div class="alert alert-success">Payment successful!</div>';
    echo '<p>Here is the data returned by the payment gateway:</p>';
    echo '<pre class="bg-base-300 p-4 rounded-box mt-4"><code>';
    echo 'Tracking ID: ' . htmlspecialchars($_GET['tracking_id']) . "\n";
    echo 'Signature: ' . htmlspecialchars($_GET['signature']) . "\n";
    if (isset($_GET['invoice_id'])) {
        echo 'Invoice ID: ' . htmlspecialchars($_GET['invoice_id']) . "\n";
    }
    echo '</code></pre>';
} else {
    echo '<div class="alert alert-warning">Payment not completed or callback received without the expected parameters.</div>';
    echo '<pre class="bg-base-300 p-4 rounded-box mt-4"><code>';
    print_r($_GET);
    echo '</code></pre>';
}


include_once('footer.php');
?>
