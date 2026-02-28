<?php
$invoice = $invoice ?? [];
if (!$invoice) {
    die('Invoice data missing.');
}

$items = $items ?? [];

// Calculate totals
$subtotal = 0;
$totalVat = 0;
foreach ($items as $item) {
    $lineTotal = $item['qty'] * $item['unit_price'];
    $lineVat = $lineTotal * ($item['vat_rate'] / 100);
    $subtotal += $lineTotal;
    $totalVat += $lineVat;
}
$discount = floatval($invoice['discount']);
$finalTotal = $subtotal - $discount + $totalVat;
$amountPaid = floatval($invoice['amount_paid'] ?? 0);
$balanceDue = max(0, $finalTotal - $amountPaid);

$currency = $invoice['invoice_currency'] ?? 'AED';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt - <?= htmlspecialchars($invoice['invoice_number']) ?></title>
    <!-- Tailwind CSS (CDN for quick print styling) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f1f5f9; }
        .receipt-card { max-width: 450px; margin: 40px auto; background: #fff; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); overflow: hidden; }
        .receipt-header { background-color: #0f172a; color: #fff; padding: 24px; text-align: center; }
        @media print {
            body { background: #fff; }
            .receipt-card { box-shadow: none; margin: 0; max-width: 100%; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

<div class="no-print text-center pt-6">
    <button onclick="window.print()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-6 rounded-lg shadow transition">
        Print Receipt
    </button>
    <div class="mt-4 text-sm text-slate-500">Press Esc to return</div>
</div>

<div class="receipt-card">
    <div class="receipt-header relative">
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at center, #ffffff 1px, transparent 1px); background-size: 10px 10px;"></div>
        <div class="relative z-10">
            <h1 class="text-2xl font-bold tracking-tight mb-1">PAYMENT RECEIPT</h1>
            <p class="text-indigo-300 text-sm opacity-90"><?= htmlspecialchars(APP_NAME) ?></p>
            <div class="mt-6">
                <p class="text-xs text-slate-400 uppercase tracking-wider mb-1">Amount Paid</p>
                <p class="text-4xl font-extrabold text-emerald-400"><?= number_format($amountPaid, 2) ?> <?= $currency ?></p>
            </div>
        </div>
    </div>

    <div class="p-6">
        <div class="flex justify-between items-end border-b border-slate-100 pb-4 mb-4">
            <div>
                <p class="text-xs text-slate-400 uppercase tracking-wider">Receipt For</p>
                <p class="font-semibold text-slate-800"><?= htmlspecialchars($invoice['client_name']) ?></p>
                <?php if ($invoice['client_email']): ?>
                    <p class="text-xs text-slate-500"><?= htmlspecialchars($invoice['client_email']) ?></p>
                <?php endif; ?>
            </div>
            <div class="text-right">
                <p class="text-xs text-slate-400 uppercase tracking-wider">Date</p>
                <p class="text-sm font-medium text-slate-800"><?= date('M d, Y') ?></p>
            </div>
        </div>

        <div class="flex justify-between items-center mb-6">
            <div>
                <p class="text-xs text-slate-400 uppercase tracking-wider">Invoice / Ref</p>
                <p class="font-semibold text-indigo-600"><?= htmlspecialchars($invoice['invoice_number']) ?></p>
            </div>
            <div class="text-right">
                <p class="text-xs text-slate-400 uppercase tracking-wider">Payment Method</p>
                <p class="font-medium text-slate-800">Bank Transfer / Card</p> 
            </div>
        </div>

        <div class="bg-slate-50 rounded-lg p-4 mb-6 text-sm border border-slate-100">
            <div class="flex justify-between mb-2">
                <span class="text-slate-500">Invoice Total:</span>
                <span class="font-medium text-slate-800"><?= number_format($finalTotal, 2) ?> <?= $currency ?></span>
            </div>
            <div class="flex justify-between mb-2 pb-2 border-b border-slate-200">
                <span class="text-slate-500">Amount Paid:</span>
                <span class="font-medium text-emerald-600">- <?= number_format($amountPaid, 2) ?> <?= $currency ?></span>
            </div>
            <div class="flex justify-between mt-2 pt-1 font-semibold">
                <span class="text-slate-800">Remaining Balance:</span>
                <span class="text-slate-800"><?= number_format($balanceDue, 2) ?> <?= $currency ?></span>
            </div>
        </div>

        <?php if ($invoice['payment_terms']): ?>
        <div class="text-xs text-slate-500 mb-6 bg-slate-50 p-3 rounded">
            <strong>Notes / Terms:</strong><br>
            <?= nl2br(htmlspecialchars($invoice['payment_terms'])) ?>
        </div>
        <?php endif; ?>

        <div class="text-center mt-8">
            <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <p class="text-emerald-600 font-semibold">Payment Successful</p>
            <p class="text-xs text-slate-400 mt-1">Thank you for your business!</p>
        </div>
    </div>
</div>

</body>
</html>
