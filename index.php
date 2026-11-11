<?php
// Redirect to the main application app.php
header("Location: app.php");
exit();

/*
// Original test code below:
include_once(__DIR__ . '/vendor/autoload.php');

use IntaSend\IntaSendPHP\Checkout;
use IntaSend\IntaSendPHP\Customer;

$credentials = [
    'publishable_key' =>  'ISPubKey_live_fbd8e0b7-cbbf-4176-81d6-55a1d7fd681a'
];

// ... (rest of the original code)
*/
?>
