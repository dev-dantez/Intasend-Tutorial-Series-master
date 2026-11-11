<?php

use IntaSend\IntaSendPHP\Collection;

function process_stk_push()
{
    if (isset($_POST['deposit'])) {
        include_once './credentials.php';

        if (empty($credentials['publishable_key'])) {
             return ['class' => 'alert-error', 'message' => 'Please add your Publishable Key to credentials.php'];
        }

        $phone_number = $_POST['phonenumber'];
        $amount = $_POST['amount'];

        try {
            $collection = new Collection();
            $collection->init($credentials);

            $response = $collection->mpesa_stk_push($amount, $phone_number, 'Customer pay for item');
            
            // We are not polling for status here to avoid long loading times.
            // The callback infrastructure should be used for production applications.
            // For this example, we will just show the initial response.

            if ($response && ($response->invoice->state == 'PROCESSING' || $response->invoice->state == 'PENDING')) {
                 return ['class' => 'alert-info', 'message' => 'STK Push initiated. Please check your phone to complete the payment. Invoice ID: '.$response->invoice->invoice_id];
            } else if($response && $response->invoice->state == 'COMPLETE') {
                 return ['class' => 'alert-success', 'message' => 'Payment Successful.'];
            } else {
                 if ($response) {
                    $debug_info = htmlspecialchars(json_encode($response));
                    return ['class' => 'alert-error', 'message' => 'STK push failed. Reason: ' . (isset($response->invoice->note) ? $response->invoice->note : 'Unknown') . ' <br><small>Debug: ' . $debug_info . '</small>'];
                 } else {
                     return ['class' => 'alert-error', 'message' => 'STK push failed. No response from server.'];
                 }
            }

        } catch (Exception $e) {
            return ['class' => 'alert-error', 'message' => 'An error occurred: ' . $e->getMessage()];
        }
    }
    if (isset($_GET['status'])) {
        $status = $_GET['status'];
        if ($status === 'COMPLETE') {
            return ['class' => 'alert-success', 'message' => 'Payment Successful'];
        } elseif ($status === 'FAILED') {
            return ['class' => 'alert-error', 'message' => 'Payment Cancelled'];
        } else {
            return ['class' => 'alert-warning', 'message' => 'Payment Pending'];
        }
    }
    return null;
}

$status_alert = process_stk_push();

?>

?>

<div class="max-w-4xl mx-auto animate-fade-in">
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold mb-2">M-Pesa STK Push</h2>
        <p class="opacity-70">Trigger a real-time payment prompt to a customer's phone.</p>
    </div>

    <div class="hero rounded-2xl overflow-hidden glass-panel p-0">
        <div class="hero-content flex-col lg:flex-row-reverse w-full p-8 lg:p-12 gap-12">
            <div class="text-center lg:text-left flex-1">
                <?php if ($status_alert) { ?>
                    <div class="alert <?php echo $status_alert['class']; ?> shadow-lg mb-6 border-none text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span><?php echo $status_alert['message']; ?></span>
                    </div>
                <?php } ?>
                
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
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Phone Number</span>
                        </label>
                        <input type="text" placeholder="254700000000" class="input input-bordered input-premium" name="phonenumber" required />
                    </div>
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Amount</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">KES</span>
                            <input type="number" placeholder="10" class="input input-bordered input-premium pl-12" name="amount" required />
                        </div>
                    </div>
                    <div class="form-control mt-8">
                        <button class="btn btn-primary-gradient w-full h-12 text-lg font-semibold" type="submit" name="deposit">
                            Send Request
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>