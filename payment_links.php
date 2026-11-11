<?php
use IntaSend\IntaSendPHP\PaymentLink;

include_once './credentials.php';

$link_result = null;

if (isset($_POST['generate_link'])) {
    if (empty($credentials['publishable_key'])) {
         $link_result = ['type' => 'error', 'message' => 'Please update credentials.php with your API keys.'];
    } else {
        try {
            $paymentLink = new PaymentLink();
            $paymentLink->init($credentials);
            
            $title = $_POST['title'];
            $currency = $_POST['currency'];
            $amount = (float) $_POST['amount'];
            
            // Explicitly passing defaults to ensure correct values
            // Note: A 500 Server Error usually indicates an issue with the IntaSend account (e.g., verification pending) 
            // or the API service itself, rather than the code.
            $response = $paymentLink->create($title, $currency, $amount, "BUSINESS-PAYS", "BUSINESS-PAYS");
            
            if ($response && isset($response->url)) {
                 $link_result = ['type' => 'success', 'message' => 'Link Generated Successfully', 'data' => $response];
            } else {
                 $link_result = ['type' => 'error', 'message' => 'Failed to generate link.'];
            }

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            // Get full response body if available
            $error_msg = $e->getMessage();
            if ($e->hasResponse()) {
                $responseBody = $e->getResponse()->getBody()->getContents();
                $error_msg .= ' <br><strong>Response:</strong> ' . htmlspecialchars($responseBody);
                
                if ($e->getResponse()->getStatusCode() == 500) {
                     $error_msg .= '<br><br><strong>Note:</strong> A 500 Error indicates the IntaSend server could not process the request. Please ensure your account is fully verified and enabled for Payment Links.';
                }
            }
            
            $link_result = ['type' => 'error', 'message' => 'API Error: ' . $error_msg];
        } catch (Exception $e) {
            $link_result = ['type' => 'error', 'message' => 'Error: ' . $e->getMessage()];
        }
    }
}
?>

<div class="max-w-4xl mx-auto animate-fade-in">
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold mb-2">Payment Links</h2>
        <p class="opacity-70">Generate simple, reusable payment links for your customers.</p>
    </div>

    <div class="flex flex-col md:flex-row gap-8 items-start">
        
        <!-- Generator Form -->
        <div class="card bg-base-100 shadow-xl card-premium flex-1">
            <form class="card-body" method="POST" action="app.php?page=payment_links">
                <input type="hidden" name="page" value="payment_links">
                <h3 class="card-title text-xl mb-4">Create New Link</h3>
                
                <div class="form-control">
                    <label class="label"><span class="label-text">Service/Product Title</span></label>
                    <input type="text" name="title" placeholder="e.g. Consultation Fee" class="input input-bordered input-premium" required />
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text">Currency</span></label>
                        <select name="currency" class="select select-bordered input-premium w-full">
                            <option value="KES">KES</option>
                            <option value="USD">USD</option>
                            <option value="EUR">EUR</option>
                            <option value="GBP">GBP</option>
                        </select>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Amount</span></label>
                        <input type="number" name="amount" placeholder="1000" class="input input-bordered input-premium" required />
                    </div>
                </div>
                
                <div class="form-control mt-6">
                    <button type="submit" name="generate_link" class="btn btn-primary-gradient">Generate Link</button>
                </div>
            </form>
        </div>

        <!-- Result Section -->
        <div class="flex-1 w-full">
            <?php if ($link_result) { ?>
                <div class="glass-panel p-6 rounded-2xl animate-fade-in">
                    <?php if ($link_result['type'] == 'success') { ?>
                        <div class="text-center">
                            <div class="inline-flex p-3 bg-success/20 rounded-full text-success mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>
                            </div>
                            <h3 class="text-lg font-bold mb-2">Link Ready!</h3>
                            <p class="text-xs opacity-70 mb-4 break-all"><?php echo $link_result['data']->url; ?></p>
                            
                            <a href="<?php echo $link_result['data']->url; ?>" target="_blank" class="btn btn-outline btn-sm btn-wide mb-2">Open Link</a>
                            
                            <div class="divider text-xs opacity-50">Details</div>
                            <div class="text-left text-sm space-y-2">
                                <div class="flex justify-between"><span>Title:</span> <span class="font-medium"><?php echo htmlspecialchars($link_result['data']->title); ?></span></div>
                                <div class="flex justify-between"><span>Ref:</span> <span class="font-mono opacity-70"><?php echo htmlspecialchars($link_result['data']->id); ?></span></div>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="alert alert-error shadow-lg">
                            <span><?php echo $link_result['message']; ?></span>
                        </div>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <div class="h-full flex flex-col items-center justify-center p-8 glass-panel rounded-2xl opacity-50 border-2 border-dashed border-base-300">
                    <div class="text-4xl mb-2">🔗</div>
                    <p class="text-center text-sm">Generate a link to see details here.</p>
                </div>
            <?php } ?>
        </div>

    </div>
</div>
