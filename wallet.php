<?php
use IntaSend\IntaSendPHP\Wallet;

include_once './credentials.php';

$wallet = new Wallet();
$wallet->init($credentials);

$wallets = [];
$transactions = [];
$selected_wallet_id = isset($_GET['wallet_id']) ? $_GET['wallet_id'] : null;

try {
    // Get all wallets
    $response = $wallet->retrieve();
    // The response structure usually depends on API. Assuming it returns an array or object with results.
    // Based on library code: return $this->send_request('GET','/wallets/');
    
    // For safety, let's assume raw response might be paginated or just a list.
    // We'll inspect it blindly or trust it's a list.
    if(isset($response->results)){
        $wallets = $response->results;
    } else {
        // Fallback if it's a direct array
        $wallets = $response; 
    }

    if ($selected_wallet_id) {
        $transactions_resp = $wallet->transactions($selected_wallet_id);
         if(isset($transactions_resp->results)){
            $transactions = $transactions_resp->results;
        } else {
            $transactions = $transactions_resp; 
        }
    }

} catch (Exception $e) {
    echo '<div class="alert alert-error">Error loading wallets: ' . $e->getMessage() . '</div>';
    $wallets = [];
}
?>

<div class="max-w-6xl mx-auto animate-fade-in">
    <div class="text-center mb-12">
        <h2 class="text-3xl font-bold mb-2">My Wallets</h2>
        <p class="opacity-70">View your account balances and recent transactions.</p>
    </div>

    <!-- Wallet Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
        <?php if (!empty($wallets)) { ?>
            <?php foreach ($wallets as $w) { 
                $is_selected = ($selected_wallet_id == $w->wallet_id);
                $card_bg = $is_selected ? 'ring-2 ring-primary bg-primary/10' : 'bg-base-100/50';
            ?>
                <a href="app.php?page=wallet&wallet_id=<?php echo $w->wallet_id; ?>" class="card bg-base-100 shadow-xl card-premium <?php echo $card_bg; ?> group transition-all">
                    <div class="card-body">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-3 rounded-xl bg-base-300 w-fit group-hover:scale-110 transition-transform">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                            </div>
                            <?php if ($w->can_disburse) { ?>
                                <span class="badge badge-success badge-sm gap-1">
                                    Disburse 
                                </span>
                            <?php } ?>
                        </div>
                        
                        <h3 class="font-medium opacity-70 uppercase tracking-wider text-xs mb-1"><?php echo htmlspecialchars($w->label); ?></h3>
                        <div class="text-3xl font-bold mb-1">
                            <?php echo number_format($w->current_balance, 2); ?> <span class="text-lg opacity-50"><?php echo htmlspecialchars($w->currency); ?></span>
                        </div>
                        <div class="text-xs opacity-50 truncate font-mono">ID: <?php echo htmlspecialchars($w->wallet_id); ?></div>
                    </div>
                </a>
            <?php } ?>
        <?php } else { ?>
            <div class="col-span-full text-center p-12 glass-panel rounded-xl">
                <p class="opacity-50">No wallets found or unable to fetch details.</p>
            </div>
        <?php } ?>
    </div>

    <!-- Transactions Section -->
    <?php if ($selected_wallet_id && !empty($wallets)) { ?>
        <div class="glass-panel rounded-2xl overflow-hidden animate-fade-in shadow-2xl">
            <div class="p-6 border-b border-white/5 flex justify-between items-center">
                <h3 class="text-xl font-bold">Recent Transactions</h3>
                <span class="text-xs opacity-50 bg-base-300 px-2 py-1 rounded">Last 10 entries</span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <!-- head -->
                    <thead class="bg-base-300/50 text-base-content/70">
                        <tr>
                            <th>Status</th>
                            <th>Reference</th>
                            <th>Type</th>
                            <th>Narrative</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($transactions) > 0) { ?>
                            <?php foreach ($transactions as $t) { ?>
                                <tr class="hover:bg-base-200/50 transition-colors border-white/5">
                                    <td>
                                        <div class="badge badge-sm <?php echo ($t->status ?? '') === 'Processing' ? 'badge-success' : (($t->status ?? '') === 'Failed' ? 'badge-error' : 'badge-ghost'); ?>">
                                            <?php echo htmlspecialchars($t->status ?? 'Unknown'); ?>
                                        </div>
                                    </td>
                                    <td class="font-mono text-xs opacity-70"><?php echo htmlspecialchars($t->invoice_id ?? $t->id ?? 'N/A'); ?></td>
                                    <td class="text-sm"><?php echo htmlspecialchars($t->category ?? $t->type ?? 'General'); ?></td>
                                    <td class="text-sm max-w-xs truncate"><?php echo htmlspecialchars($t->narrative ?? '-'); ?></td>
                                    <td class="text-right font-mono font-bold <?php echo ($t->value ?? 0) > 0 ? 'text-success' : 'text-error'; ?>">
                                        <?php echo number_format($t->value ?? 0, 2); ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="5" class="text-center py-8 opacity-50">No transactions found for this wallet.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php } else if (!empty($wallets)) { ?>
        <div class="text-center p-12 opacity-50 border-2 border-dashed border-base-300 rounded-xl">
            <p>Select a wallet above to view its transactions.</p>
        </div>
    <?php } ?>
</div>
