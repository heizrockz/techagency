<?php
$invoice = $invoice ?? [];
$isNew = empty($invoice);
$title = $isNew ? 'New Invoice / Quote' : 'Edit ' . ucfirst($invoice['type'] ?? 'invoice') . ' #' . ($invoice['invoice_number'] ?? '');
$currentPage = 'invoices';

$type = $invoice['type'] ?? 'invoice';
$invNum = $invoice['invoice_number'] ?? ($invoiceNumber ?? '');
$status = $invoice['status'] ?? 'draft';
$items = $items ?? [];
$currency = $invoice['invoice_currency'] ?? 'AED';
$paymentTerms = $invoice['payment_terms'] ?? '';
$contactId = $invoice['contact_id'] ?? 0;
$contacts = $contacts ?? [];
 
$currencies = ['AED' => 'AED (د.إ)', 'USD' => 'USD ($)', 'EUR' => 'EUR (€)', 'GBP' => 'GBP (£)', 'SAR' => 'SAR (﷼)', 'INR' => 'INR (₹)', 'QAR' => 'QAR', 'BHD' => 'BHD', 'OMR' => 'OMR', 'KWD' => 'KWD'];
?>
<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?> — <?= APP_NAME ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= baseUrl('assets/css/style.css') ?>">
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">

<div class="admin-layout">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>
    
    <div class="admin-main">
        <div class="admin-header">
            <div>
                <h1 style="color: var(--theme-gold); margin:0;">🧾 <?= e($title) ?></h1>
                <p style="color: var(--text-muted); font-size: 0.9rem;">Fill in the details below to generate.</p>
            </div>
            <div>
                <?php if (!$isNew): ?>
                    <a href="<?= baseUrl('admin/invoices?action=print&id=' . $invoice['id']) ?>" target="_blank" class="admin-btn" style="border-color: var(--theme-gold); color: var(--theme-gold);">🖨️ Print / PDF</a>
                <?php endif; ?>
                <a href="<?= baseUrl('admin/invoices') ?>" class="admin-btn">← Back to List</a>
            </div>
        </div>

        <?php if(isset($_GET['saved'])): ?>
            <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid var(--theme-primary); color: var(--theme-primary); padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                ✅ Saved successfully!
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= baseUrl('admin/invoices') ?>" class="admin-card invoice-form">
            <input type="hidden" name="id" value="<?= $invoice['id'] ?? 0 ?>">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px;">
                <!-- Document Info -->
                <div>
                    <h3 style="color: var(--neon-cyan); margin-bottom: 15px; border-bottom: 1px solid var(--glass-border); padding-bottom: 10px;">Document Details</h3>
                    
                    <div class="form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div>
                            <label>Document Type</label>
                            <select name="type" class="form-input">
                                <option value="invoice" <?= $type==='invoice'?'selected':'' ?>>Invoice</option>
                                <option value="quote" <?= $type==='quote'?'selected':'' ?>>Quote</option>
                            </select>
                        </div>
                        <div>
                            <label>Currency</label>
                            <select name="invoice_currency" class="form-input">
                                <?php foreach ($currencies as $code => $label): ?>
                                    <option value="<?= $code ?>" <?= $currency===$code?'selected':'' ?>><?= e($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div>
                            <label>Invoice/Quote Number *</label>
                            <input type="text" name="invoice_number" value="<?= e($invNum) ?>" class="form-input" required>
                            <small style="color:var(--text-muted);">Auto-generated. Edit only if needed.</small>
                        </div>
                        <div>
                            <label>Status</label>
                            <select name="status" class="form-input">
                                <option value="draft" <?= $status==='draft'?'selected':'' ?>>Draft</option>
                                <option value="sent" <?= $status==='sent'?'selected':'' ?>>Sent</option>
                                <option value="paid" <?= $status==='paid'?'selected':'' ?>>Paid / Accepted</option>
                                <option value="cancelled" <?= $status==='cancelled'?'selected':'' ?>>Cancelled</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Client Info -->
                <div>
                    <h3 style="color: var(--neon-emerald); margin-bottom: 15px; border-bottom: 1px solid var(--glass-border); padding-bottom: 10px;">Client Details</h3>
                    
                    <div class="form-group">
                        <label>Select Contact</label>
        <select name="contact_id" id="contact-select" class="form-input" onchange="fillContactDetails(this.value)">
            <option value="0">— Manual Entry —</option>
            <?php foreach ($contacts as $c): ?>
                <option value="<?= $c['id'] ?>" 
                    data-name="<?= e($c['name']) ?>"
                    data-email="<?= e($c['email'] ?? '') ?>"
                    data-phone="<?= e($c['phone'] ?? '') ?>"
                    data-address="<?= e(($c['location'] ?? '') . ($c['country'] ? ', ' . $c['country'] : '')) ?>"
                    data-vat="<?= e($c['vat_number'] ?? '') ?>"
                    data-website="<?= e($c['website'] ?? '') ?>"
                    <?= (int)$contactId === (int)$c['id'] ? 'selected' : '' ?>
                ><?= e($c['name']) ?> <?= $c['type'] === 'company' ? '🏢' : '👤' ?></option>
            <?php endforeach; ?>
        </select>
    </div>

                    <div class="form-group">
                        <label>Client Name / Company *</label>
                        <input type="text" name="client_name" id="client-name" value="<?= e($invoice['client_name'] ?? '') ?>" class="form-input" required>
                    </div>

                    <div class="form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div>
                            <label>Email Address</label>
                            <input type="email" name="client_email" id="client-email" value="<?= e($invoice['client_email'] ?? '') ?>" class="form-input">
                        </div>
                        <div>
                            <label>Phone Number</label>
                            <input type="text" name="client_phone" id="client-phone" value="<?= e($invoice['client_phone'] ?? '') ?>" class="form-input">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Billing Address</label>
                        <textarea name="client_address" id="client-address" class="form-input" style="min-height: 50px;"><?= e($invoice['client_address'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Line Items -->
            <h3 style="color: var(--theme-gold); margin-bottom: 15px; border-bottom: 1px solid var(--glass-border); padding-bottom: 10px; display: flex; justify-content: space-between; align-items: center;">
                <span>Line Items</span>
                <button type="button" class="admin-btn" id="add-item-btn" style="padding: 4px 10px; font-size: 0.8rem;">+ Add Item</button>
            </h3>

            <div style="overflow-x: auto; margin-bottom: 20px;">
                <table class="admin-table" id="items-table">
                    <thead>
                        <tr>
                            <th style="width: 30%">Service Name</th>
                            <th style="width: 30%">Description</th>
                            <th style="width: 10%">Qty</th>
                            <th style="width: 10%">Unit Price</th>
                            <th style="width: 10%">VAT %</th>
                            <th style="width: 10%">Total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="items-body">
                        <?php if (empty($items)): ?>
                            <tr class="item-row">
                                <td><input type="text" name="items[0][service_name]" class="form-input" required></td>
                                <td><input type="text" name="items[0][description]" class="form-input"></td>
                                <td><input type="number" step="0.01" name="items[0][qty]" value="1" class="form-input item-qty" required></td>
                                <td><input type="number" step="0.01" name="items[0][unit_price]" value="0.00" class="form-input item-price" required></td>
                                <td><input type="number" step="0.01" name="items[0][vat_rate]" value="0.00" class="form-input item-vat"></td>
                                <td class="item-line-total">0.00</td>
                                <td><button type="button" class="admin-btn remove-item" style="color: #f43f5e; border-color: transparent;">X</button></td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($items as $i => $item): ?>
                                <tr class="item-row">
                                    <td><input type="text" name="items[<?= $i ?>][service_name]" value="<?= e($item['service_name']) ?>" class="form-input" required></td>
                                    <td><input type="text" name="items[<?= $i ?>][description]" value="<?= e($item['description']) ?>" class="form-input"></td>
                                    <td><input type="number" step="0.01" name="items[<?= $i ?>][qty]" value="<?= e($item['qty']) ?>" class="form-input item-qty" required></td>
                                    <td><input type="number" step="0.01" name="items[<?= $i ?>][unit_price]" value="<?= e($item['unit_price']) ?>" class="form-input item-price" required></td>
                                    <td><input type="number" step="0.01" name="items[<?= $i ?>][vat_rate]" value="<?= e($item['vat_rate']) ?>" class="form-input item-vat"></td>
                                    <td class="item-line-total">0.00</td>
                                    <td><button type="button" class="admin-btn remove-item" style="color: #f43f5e; border-color: transparent;">X</button></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Totals -->
            <div style="display: flex; justify-content: flex-end; margin-bottom: 30px;">
                <div style="width: 300px; background: rgba(0,0,0,0.2); padding: 20px; border-radius: 8px; border: 1px solid var(--glass-border);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <span style="color: var(--text-secondary);">Subtotal:</span>
                        <strong id="calc-subtotal">0.00</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px; align-items: center;">
                        <span style="color: var(--text-secondary);">Global Discount:</span>
                        <input type="number" step="0.01" name="discount" id="global-discount" value="<?= e($invoice['discount'] ?? '0.00') ?>" class="form-input" style="width: 80px; padding: 4px 8px;">
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <span style="color: var(--text-secondary);">Total VAT:</span>
                        <strong id="calc-vat">0.00</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-top: 15px; padding-top: 15px; border-top: 1px double var(--glass-border); font-size: 1.2rem;">
                        <span style="color: var(--theme-gold); font-weight: 800;">Total:</span>
                        <strong id="calc-total" style="color: var(--theme-primary);">0.00</strong>
                    </div>
                </div>
            </div>

            <!-- Notes, Terms & Payment Terms -->
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                <div class="form-group">
                    <label>Client Notes (Printed on invoice)</label>
                    <textarea name="notes" class="form-input" style="min-height: 100px;"><?= e($invoice['notes'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>Terms & Conditions</label>
                    <textarea name="terms" class="form-input" style="min-height: 100px;"><?= e($invoice['terms'] ?? $defaultTerms ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>Payment Terms</label>
                    <textarea name="payment_terms" class="form-input" style="min-height: 100px;" placeholder="e.g. 50% upfront, 50% on delivery. Net 30 days."><?= e($paymentTerms) ?></textarea>
                </div>
            </div>

            <div style="text-align: right; margin-top: 30px; border-top: 1px solid var(--glass-border); padding-top: 20px;">
                <button type="submit" class="btn-primary">💾 Save Document</button>
            </div>
        </form>
    </div>
</div>

<script>
// Auto-fill contact details
function fillContactDetails(contactId) {
    const sel = document.getElementById('contact-select');
    const opt = sel.options[sel.selectedIndex];
    if (contactId > 0) {
        document.getElementById('client-name').value = opt.dataset.name || '';
        document.getElementById('client-email').value = opt.dataset.email || '';
        document.getElementById('client-phone').value = opt.dataset.phone || '';
        
        // Build comprehensive billing address
        let addressParts = [];
        if (opt.dataset.address) addressParts.push(opt.dataset.address);
        if (opt.dataset.vat) addressParts.push('VAT Number: ' + opt.dataset.vat);
        if (opt.dataset.website) addressParts.push('Website: ' + opt.dataset.website);
        
        document.getElementById('client-address').value = addressParts.join('\n');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    let itemIdx = <?= empty($items) ? 1 : count($items) ?>;
    const itemsBody = document.getElementById('items-body');
    const btnAdd = document.getElementById('add-item-btn');
    const globalDiscount = document.getElementById('global-discount');

    function calculateTotals() {
        let subtotal = 0;
        let totalVat = 0;
        
        document.querySelectorAll('.item-row').forEach(row => {
            const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
            const price = parseFloat(row.querySelector('.item-price').value) || 0;
            const vatRate = parseFloat(row.querySelector('.item-vat').value) || 0;
            
            const lineTotal = qty * price;
            const lineVat = lineTotal * (vatRate / 100);
            
            row.querySelector('.item-line-total').textContent = (lineTotal + lineVat).toFixed(2);
            
            subtotal += lineTotal;
            totalVat += lineVat;
        });

        const discount = parseFloat(globalDiscount.value) || 0;
        const finalTotal = subtotal - discount + totalVat;

        document.getElementById('calc-subtotal').textContent = subtotal.toFixed(2);
        document.getElementById('calc-vat').textContent = totalVat.toFixed(2);
        document.getElementById('calc-total').textContent = finalTotal.toFixed(2);
    }

    btnAdd.addEventListener('click', () => {
        const tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.innerHTML = `
            <td><input type="text" name="items[${itemIdx}][service_name]" class="form-input" required></td>
            <td><input type="text" name="items[${itemIdx}][description]" class="form-input"></td>
            <td><input type="number" step="0.01" name="items[${itemIdx}][qty]" value="1" class="form-input item-qty" required></td>
            <td><input type="number" step="0.01" name="items[${itemIdx}][unit_price]" value="0.00" class="form-input item-price" required></td>
            <td><input type="number" step="0.01" name="items[${itemIdx}][vat_rate]" value="0.00" class="form-input item-vat"></td>
            <td class="item-line-total">0.00</td>
            <td><button type="button" class="admin-btn remove-item" style="color: #f43f5e; border-color: transparent;">X</button></td>
        `;
        itemsBody.appendChild(tr);
        itemIdx++;
        bindRowEvents(tr);
        calculateTotals();
    });

    function bindRowEvents(row) {
        row.querySelectorAll('input').forEach(inp => {
            inp.addEventListener('input', calculateTotals);
        });
        row.querySelector('.remove-item').addEventListener('click', (e) => {
            e.target.closest('tr').remove();
            calculateTotals();
        });
    }

    document.querySelectorAll('.item-row').forEach(bindRowEvents);
    globalDiscount.addEventListener('input', calculateTotals);
    calculateTotals();
});
</script>

</body>
</html>
