<?php
use IntaSend\IntaSendPHP\Transfer;

function handle_b2c_request()
{
    if (!isset($_POST['action'])) {
        return null;
    }

    include_once './credentials.php';
    if (empty($credentials['token'])) {
        return ['type' => 'error', 'message' => 'Please add your API Token to credentials.php'];
    }

    $transfer = new Transfer();
    $transfer->init($credentials);
    $action = $_POST['action'];

    try {
        if ($action === 'initiate') {
            $transactions = [
                [
                    'account' => $_POST['account'],
                    'amount' => $_POST['amount'],
                    'narrative' => $_POST['narrative']
                ],
            ];
            $response = $transfer->mpesa("KES", $transactions);
            return ['type' => 'success', 'message' => 'Transfer initiated.', 'response' => $response];
        }

        if ($action === 'approve') {
            $tracking_id = $_POST['tracking_id'];
            $response = json_decode($_POST['initiate_response']);
            $response = $transfer->approve($response);
             return ['type' => 'success', 'message' => 'Approval request sent.', 'response' => $response, 'initiate_response' => $_POST['initiate_response']];
        }

        if ($action === 'status') {
            $tracking_id = $_POST['tracking_id'];
            $response = $transfer->status($tracking_id);
            return ['type' => 'success', 'message' => 'Status checked.', 'response' => $response, 'initiate_response' => $_POST['initiate_response']];
        }
    } catch (Exception $e) {
        return ['type' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()];
    }
}

$result = handle_b2c_request();
$initiate_response = null;
if (isset($result['response'])) {
    $initiate_response = isset($result['initiate_response']) ? $result['initiate_response'] : json_encode($result['response']);
}


?>

<div class="max-w-6xl mx-auto animate-fade-in">
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold mb-2">B2C Transfer (Disbursements)</h2>
        <p class="opacity-70">Send funds directly to M-Pesa accounts from your IntaSend wallet.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">

        <!-- Form Section -->
        <div class="card bg-base-100 shadow-xl card-premium">
            <form class="card-body" method="POST" action="app.php?page=b2c">
                <h2 class="card-title text-2xl mb-4 text-primary">Initiate Transfer</h2>
                <input type="hidden" name="action" value="initiate">
                
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Phone Number</span>
                    </label>
                    <input type="text" placeholder="2547..." class="input input-bordered input-premium" name="account" required value="254740408496" />
                </div>
                
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Amount</span>
                    </label>
                    <div class="relative">
                         <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">KES</span>
                        <input type="number" placeholder="10" class="input input-bordered input-premium pl-12" name="amount" required value="10" />
                    </div>
                </div>
                
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Narrative</span>
                    </label>
                    <input type="text" placeholder="Salary Payment" class="input input-bordered input-premium" name="narrative" required value="Salary" />
                </div>
                
                <div class="form-control mt-8">
                    <button class="btn btn-primary-gradient" type="submit">
                        Initiate Transfer <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                    </button>
                </div>
            </form>
        </div>

        <!-- Response Section -->
        <div class="space-y-6">
            <?php if ($result) { ?>
                <div class="alert alert-<?php echo $result['type'] === 'error' ? 'error' : 'success'; ?> shadow-lg border border-white/5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span><?php echo $result['message']; ?></span>
                </div>

                <div class="mockup-code bg-[#1f2937] text-gray-300 shadow-xl border border-white/5 h-[400px] overflow-y-auto custom-scrollbar">
                    <pre class="px-5 py-2 text-warning"><code>// API Response</code></pre> 
                    <pre class="px-5"><code><?php print_r($result['response']); ?></code></pre>
                </div>
                
                <?php if (isset($result['response']->tracking_id)) { ?>
                    <div class="glass-panel p-6 rounded-xl flex flex-col sm:flex-row gap-4 justify-between items-center">
                        <div class="text-sm opacity-70">Actions for Tracking ID: <span class="font-mono text-primary"><?php echo $result['response']->tracking_id; ?></span></div>
                        <div class="flex gap-2">
                             <form method="POST" action="app.php?page=b2c">
                                <input type="hidden" name="action" value="approve">
                                <input type="hidden" name="initiate_response" value='<?php echo $initiate_response; ?>'>
                                <input type="hidden" name="tracking_id" value="<?php echo $result['response']->tracking_id; ?>">
                                <button class="btn btn-success btn-sm btn-outline gap-2" type="submit">
                                    ✓ Approve
                                </button>
                            </form>
                             <form method="POST" action="app.php?page=b2c">
                                <input type="hidden" name="action" value="status">
                                 <input type="hidden" name="initiate_response" value='<?php echo $initiate_response; ?>'>
                                <input type="hidden" name="tracking_id" value="<?php echo $result['response']->tracking_id; ?>">
                                <button class="btn btn-info btn-sm btn-outline gap-2" type="submit">
                                    ? Status
                                </button>
                            </form>
                        </div>
                    </div>
                <?php } ?>

            <?php } else { ?>
                <div class="glass-panel p-8 rounded-xl text-center h-full flex flex-col items-center justify-center opacity-70 border-dashed">
                    <div class="text-6xl mb-4">👋</div>
                    <p>Initiate a transfer to see the API response and approval options here.</p>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
