<?php
$type = ucfirst($invoice['type'] ?? 'invoice');
$invNum = $invoice['invoice_number'] ?? '';
$date = date('F j, Y', strtotime($invoice['created_at']));
$siteName = getSetting('site_name', 'Mico Sage');
$sitePhone = getSetting('contact_phone', '');
$siteEmail = getSetting('contact_email', '');
$siteAddress = getSetting('contact_address', '');
$cur = $invoice['invoice_currency'] ?? 'AED';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $type ?> - <?= e($invNum) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            color: #1a1d24;
            background: #fff;
            margin: 0;
            padding: 40px;
            font-size: 14px;
            line-height: 1.6;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            background: #fff;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 40px;
            border-bottom: 2px solid #10b981;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #10b981;
            margin: 0 0 10px 0;
            font-size: 2.5rem;
            text-transform: uppercase;
            letter-spacing: -1px;
        }
        .company-info {
            text-align: left;
            margin-top: 10px;
            color: #4b5563;
        }
        .invoice-details {
            text-align: right;
        }
        .invoice-details h2 {
            margin: 0 0 5px 0;
            font-size: 1.5rem;
            color: #1f2937;
        }
        .client-info {
            margin-bottom: 40px;
            background: #f9fafb;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #10b981;
        }
        .client-info h3 {
            margin: 0 0 10px 0;
            color: #1f2937;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        th {
            background: #f3f4f6;
            color: #374151;
            font-weight: 600;
            text-align: left;
            padding: 12px;
            border-bottom: 2px solid #e5e7eb;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
            color: #4b5563;
        }
        .totals {
            width: 300px;
            float: right;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            color: #4b5563;
        }
        .totals-row.grand-total {
            border-top: 2px solid #10b981;
            padding-top: 12px;
            font-weight: 700;
            font-size: 1.2rem;
            color: #1f2937;
        }
        .clear {
            clear: both;
        }
        .notes-terms {
            margin-top: 60px;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
            font-size: 0.9rem;
            color: #6b7280;
        }
        .notes-terms h4 {
            margin: 0 0 5px 0;
            color: #374151;
        }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
        .print-btn {
            display: inline-block;
            background: #10b981;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 600;
            margin-bottom: 20px;
            cursor: pointer;
            border: none;
        }
    </style>
</head>
<body>

<div class="no-print" style="text-align: center;">
    <button class="print-btn" onclick="window.print()">🖨️ Print / Save as PDF</button>
</div>

<div class="invoice-box">
    <div class="header">
        <div>
            <?php $logo = getSetting('site_logo'); if(!empty($logo)): ?>
                <img src="<?= baseUrl($logo) ?>" alt="<?= e($siteName) ?>" style="max-height: 60px; margin-bottom: 10px;">
            <?php else: ?>
                <h1><?= e($siteName) ?></h1>
            <?php endif; ?>
            <div class="company-info">
                <?php if($siteAddress) echo e($siteAddress) . '<br>'; ?>
                <?php if($siteEmail) echo e($siteEmail) . '<br>'; ?>
                <?php if($sitePhone) echo e($sitePhone); ?>
            </div>
        </div>
        <div class="invoice-details">
            <h2><?= $type ?></h2>
            <div><strong>No:</strong> <?= e($invNum) ?></div>
            <div><strong>Date:</strong> <?= $date ?></div>
            <div><strong>Status:</strong> <?= ucfirst($invoice['status']) ?></div>
        </div>
    </div>

    <div class="client-info">
        <h3>Billed To</h3>
        <strong><?= e($invoice['client_name']) ?></strong><br>
        <?php if($invoice['client_address']) echo nl2br(e($invoice['client_address'])) . '<br>'; ?>
        <?php if($invoice['client_email']) echo e($invoice['client_email']) . '<br>'; ?>
        <?php if($invoice['client_phone']) echo e($invoice['client_phone']); ?>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 40%">Description</th>
                <th style="text-align: center">Qty</th>
                <th style="text-align: right">Unit Price</th>
                <th style="text-align: right">VAT / Tax</th>
                <th style="text-align: right">Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $subtotal = 0;
            $totalVat = 0;
            foreach ($items as $item): 
                $lineAmount = $item['qty'] * $item['unit_price'];
                $lineVat = $lineAmount * ($item['vat_rate'] / 100);
                $subtotal += $lineAmount;
                $totalVat += $lineVat;
            ?>
                <tr>
                    <td>
                        <strong style="color: #1f2937;"><?= e($item['service_name']) ?></strong>
                        <?php if($item['description']): ?>
                            <div style="font-size: 0.85rem; margin-top: 4px;"><?= nl2br(e($item['description'])) ?></div>
                        <?php endif; ?>
                    </td>
                    <td style="text-align: center"><?= (float)$item['qty'] ?></td>
                    <td style="text-align: right"><?= $cur ?> <?= number_format($item['unit_price'], 2) ?></td>
                    <td style="text-align: right"><?= (float)$item['vat_rate'] ?>%</td>
                    <td style="text-align: right"><?= $cur ?> <?= number_format($lineAmount + $lineVat, 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="totals">
        <div class="totals-row">
            <span>Subtotal:</span>
            <span><?= $cur ?> <?= number_format($subtotal, 2) ?></span>
        </div>
        <?php if ($invoice['discount'] > 0): ?>
            <div class="totals-row" style="color: #f43f5e;">
                <span>Discount:</span>
                <span>-<?= $cur ?> <?= number_format($invoice['discount'], 2) ?></span>
            </div>
        <?php endif; ?>
        <div class="totals-row">
            <span>Tax / VAT:</span>
            <span><?= $cur ?> <?= number_format($totalVat, 2) ?></span>
        </div>
        <div class="totals-row grand-total">
            <span>Total:</span>
            <span><?= $cur ?> <?= number_format(($subtotal - $invoice['discount'] + $totalVat), 2) ?></span>
        </div>
    </div>
    <div class="clear"></div>

    <div class="notes-terms">
        <?php if ($invoice['notes']): ?>
            <div style="margin-bottom: 20px;">
                <h4>Notes</h4>
                <div><?= nl2br(e($invoice['notes'])) ?></div>
            </div>
        <?php endif; ?>
        
        <?php if ($invoice['terms']): ?>
            <div>
                <h4>Terms & Conditions</h4>
                <div><?= nl2br(e($invoice['terms'])) ?></div>
            </div>
        <?php endif; ?>

        <?php if (!empty($invoice['payment_terms'])): ?>
            <div style="margin-top: 20px;">
                <h4>Payment Terms</h4>
                <div><?= nl2br(e($invoice['payment_terms'])) ?></div>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
