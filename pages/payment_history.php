<?php
require_once __DIR__ . '/../models/Payment.php';

$user = get_logged_in_user();
$paymentModel = new Payment();
$payments = $paymentModel->findByUser($user['id'], 20);
?>

<div class="max-w-6xl mx-auto animate-fade-in">
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold mb-2">Payment History</h2>
        <p class="opacity-70">View all your payment transactions</p>
    </div>
    
    <?php if (empty($payments)): ?>
        <div class="text-center p-12 glass-panel rounded-xl">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="text-lg opacity-50">No payment history yet</p>
            <a href="app.php?page=checkout" class="btn btn-primary mt-4">Make Your First Payment</a>
        </div>
    <?php else: ?>
        <div class="glass-panel rounded-2xl overflow-hidden shadow-2xl">
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead class="bg-base-300/50">
                        <tr>
                            <th>Date</th>
                            <th>Reference</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $payment): ?>
                            <tr class="hover:bg-base-200/50 transition-colors border-white/5">
                                <td class="text-sm">
                                    <?php echo date('M d, Y H:i', strtotime($payment['created_at'])); ?>
                                </td>
                                <td class="font-mono text-xs opacity-70">
                                    <?php echo htmlspecialchars(substr($payment['reference_number'], 0, 20)); ?>...
                                </td>
                                <td>
                                    <span class="badge badge-sm badge-outline">
                                        <?php echo htmlspecialchars($payment['payment_type']); ?>
                                    </span>
                                </td>
                                <td class="font-bold">
                                    <?php echo number_format($payment['amount'], 2); ?> 
                                    <span class="text-xs opacity-50"><?php echo htmlspecialchars($payment['currency']); ?></span>
                                </td>
                                <td>
                                    <?php
                                    $statusClass = 'badge-ghost';
                                    if ($payment['status'] === 'COMPLETE') $statusClass = 'badge-success';
                                    elseif ($payment['status'] === 'FAILED') $statusClass = 'badge-error';
                                    elseif ($payment['status'] === 'PENDING' || $payment['status'] === 'PROCESSING') $statusClass = 'badge-warning';
                                    ?>
                                    <div class="badge badge-sm <?php echo $statusClass; ?>">
                                        <?php echo htmlspecialchars($payment['status']); ?>
                                    </div>
                                </td>
                                <td>
                                    <button class="btn btn-ghost btn-xs" onclick="viewPayment<?php echo $payment['id']; ?>.showModal()">
                                        View
                                    </button>
                                    
                                    <!-- Modal -->
                                    <dialog id="viewPayment<?php echo $payment['id']; ?>" class="modal">
                                        <div class="modal-box">
                                            <h3 class="font-bold text-lg mb-4">Payment Details</h3>
                                            <div class="space-y-2 text-sm">
                                                <div class="flex justify-between">
                                                    <span class="opacity-70">Reference:</span>
                                                    <span class="font-mono"><?php echo htmlspecialchars($payment['reference_number']); ?></span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="opacity-70">Amount:</span>
                                                    <span class="font-bold"><?php echo number_format($payment['amount'], 2); ?> <?php echo htmlspecialchars($payment['currency']); ?></span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="opacity-70">Type:</span>
                                                    <span><?php echo htmlspecialchars($payment['payment_type']); ?></span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="opacity-70">Status:</span>
                                                    <span><?php echo htmlspecialchars($payment['status']); ?></span>
                                                </div>
                                                <?php if ($payment['transaction_id']): ?>
                                                    <div class="flex justify-between">
                                                        <span class="opacity-70">Transaction ID:</span>
                                                        <span class="font-mono text-xs"><?php echo htmlspecialchars($payment['transaction_id']); ?></span>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="flex justify-between">
                                                    <span class="opacity-70">Created:</span>
                                                    <span><?php echo date('M d, Y H:i:s', strtotime($payment['created_at'])); ?></span>
                                                </div>
                                                <?php if ($payment['updated_at'] !== $payment['created_at']): ?>
                                                    <div class="flex justify-between">
                                                        <span class="opacity-70">Updated:</span>
                                                        <span><?php echo date('M d, Y H:i:s', strtotime($payment['updated_at'])); ?></span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="modal-action">
                                                <form method="dialog">
                                                    <button class="btn">Close</button>
                                                </form>
                                            </div>
                                        </div>
                                        <form method="dialog" class="modal-backdrop">
                                            <button>close</button>
                                        </form>
                                    </dialog>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>
