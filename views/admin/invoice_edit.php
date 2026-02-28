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
            <div class="admin-actions">
                <?php if ($invoice['id']): ?>
                    <a href="<?= baseUrl('admin/invoices?action=print&id=' . $invoice['id']) ?>" target="_blank" rel="noopener noreferrer" class="btn-secondary">🖨️ Print / PDF</a>
                <?php endif; ?>
                <a href="<?= baseUrl('admin/invoices') ?>" class="btn-ghost">← Back to List</a>
            </div>
        </div>

        <?php if(isset($_GET['saved'])): ?>
            <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid var(--theme-primary); color: var(--theme-primary); padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                ✅ Saved successfully!
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= baseUrl('admin/invoices') ?>" class="admin-card invoice-form">
            <input type="hidden" name="id" value="<?= $invoice['id'] ?? 0 ?>">
            
            <div class="admin-grid-2" style="margin-bottom: 30px; align-items: start;">
                <!-- Document Info -->
                <div class="admin-card" style="padding: 20px; background: rgba(0,0,0,0.15);">
                    <h3 style="color: var(--neon-cyan); margin-bottom: 15px; border-bottom: 1px solid var(--glass-border); padding-bottom: 10px;">Document Details</h3>
                    
                    <div class="admin-grid-2" style="gap: 15px;">
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

                    <div class="admin-grid-2" style="gap: 15px;">
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
                <div class="admin-card" style="padding: 20px; background: rgba(0,0,0,0.15);">
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

                    <div class="admin-grid-2" style="gap: 15px;">
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
                <span>Line Items <span style="cursor:help; color:var(--text-muted); font-size:0.75rem;" title="Add products by typing in the Service Name field — matching CRM products will appear as suggestions. You can also add custom items.">ⓘ</span></span>
                <div>
                    <button type="button" class="admin-btn" id="add-item-btn" style="padding: 4px 10px; font-size: 0.8rem;">+ Add a line</button>
                </div>
            </h3>

            <div style="overflow-x: visible; margin-bottom: 20px; padding-bottom: 15px;">
                <table class="admin-table" id="items-table">
                    <thead>
                        <tr>
                            <th style="width: 28%">Product <span style="cursor:help; color:var(--text-muted); font-size:0.7rem;" title="Type to search CRM products or enter a custom name">ⓘ</span></th>
                            <th style="width: 25%">Description</th>
                            <th style="width: 8%">Qty</th>
                            <th style="width: 12%">Unit Price</th>
                            <th style="width: 8%">VAT % <span style="cursor:help; color:var(--text-muted); font-size:0.7rem;" title="Value Added Tax percentage applied to this line item">ⓘ</span></th>
                            <th style="width: 12%">Total</th>
                            <th style="width: 5%"></th>
                        </tr>
                    </thead>
                    <tbody id="items-body">
                        <?php if (empty($items)): ?>
                            <tr class="item-row">
                                <td style="position:relative;"><input type="text" name="items[0][service_name]" class="form-input product-search" autocomplete="off" placeholder="Type to find product..." required></td>
                                <td><input type="text" name="items[0][description]" class="form-input item-desc"></td>
                                <td><input type="number" step="0.01" name="items[0][qty]" value="1.00" class="form-input item-qty" required></td>
                                <td><input type="number" step="0.01" name="items[0][unit_price]" value="0.00" class="form-input item-price" required></td>
                                <td><input type="number" step="0.01" name="items[0][vat_rate]" value="5.00" class="form-input item-vat"></td>
                                <td class="item-line-total">0.00</td>
                                <td><button type="button" class="admin-btn remove-item" style="color: #f43f5e; border-color: transparent;" title="Remove this line">🗑</button></td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($items as $i => $item): ?>
                                <tr class="item-row">
                                    <td style="position:relative;"><input type="text" name="items[<?= $i ?>][service_name]" value="<?= e($item['service_name']) ?>" class="form-input product-search" autocomplete="off" placeholder="Type to find product..." required></td>
                                    <td><input type="text" name="items[<?= $i ?>][description]" value="<?= e($item['description']) ?>" class="form-input item-desc"></td>
                                    <td><input type="number" step="0.01" name="items[<?= $i ?>][qty]" value="<?= e($item['qty']) ?>" class="form-input item-qty" required></td>
                                    <td><input type="number" step="0.01" name="items[<?= $i ?>][unit_price]" value="<?= e($item['unit_price']) ?>" class="form-input item-price" required></td>
                                    <td><input type="number" step="0.01" name="items[<?= $i ?>][vat_rate]" value="<?= e($item['vat_rate']) ?>" class="form-input item-vat"></td>
                                    <td class="item-line-total">0.00</td>
                                    <td><button type="button" class="admin-btn remove-item" style="color: #f43f5e; border-color: transparent;" title="Remove this line">🗑</button></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Totals -->
            <div style="display: flex; justify-content: flex-end; margin-bottom: 30px;">
                <div style="width: 100%; max-width: 350px; background: rgba(0,0,0,0.2); padding: 20px; border-radius: 8px; border: 1px solid var(--glass-border);">
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
                    <div style="display: flex; justify-content: space-between; margin-top: 15px; align-items: center;">
                        <span style="color: var(--text-secondary);">Amount Paid (Split):</span>
                        <input type="number" step="0.01" name="amount_paid" id="amount-paid" value="<?= e($invoice['amount_paid'] ?? '0.00') ?>" class="form-input" style="width: 100px; padding: 4px 8px; border-color: var(--neon-emerald);">
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-top: 10px; font-size: 1.1rem;">
                        <span style="color: #f43f5e; font-weight: 600;">Balance Due:</span>
                        <strong id="calc-balance" style="color: #f43f5e;">0.00</strong>
                    </div>
                    
                    <?php if (($invoice['id'] ?? 0) > 0 && ($invoice['amount_paid'] ?? 0) > 0): ?>
                        <div style="margin-top: 15px; text-align: right;">
                            <a href="<?= baseUrl('admin/invoices?action=receipt&id=' . $invoice['id']) ?>" target="_blank" class="admin-btn" style="background: var(--neon-emerald); color: #fff; border:none; padding: 5px 10px; font-size: 0.85rem;">
                                🧾 View CRM Receipt
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Notes, Terms & Payment Terms -->
            <div class="admin-grid-3" style="gap: 20px; margin-bottom: 30px;">
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

<!-- Typeahead Dropdown Styles -->
<style>
.product-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: var(--theme-darker, #0d1117);
    border: 1px solid var(--glass-border, rgba(255,255,255,0.1));
    border-radius: 0 0 8px 8px;
    max-height: 220px;
    overflow-y: auto;
    z-index: 999;
    box-shadow: 0 8px 24px rgba(0,0,0,0.5);
    display: none;
}
.product-dropdown .pd-item {
    padding: 8px 12px;
    cursor: pointer;
    border-bottom: 1px solid rgba(255,255,255,0.03);
    transition: background 0.15s;
}
.product-dropdown .pd-item:hover,
.product-dropdown .pd-item.active {
    background: rgba(99, 102, 241, 0.15);
}
.product-dropdown .pd-item .pd-name {
    font-weight: 600;
    color: var(--text-primary, #fff);
    font-size: 0.85rem;
}
.product-dropdown .pd-item .pd-meta {
    font-size: 0.7rem;
    color: var(--text-muted, #64748b);
    display: flex;
}
.product-dropdown .pd-empty {
    padding: 8px 12px;
    font-size: 0.8rem;
    color: var(--text-muted, #64748b);
    font-style: italic;
}
.product-dropdown .pd-create-btn {
    padding: 8px 12px;
    cursor: pointer;
    border-top: 1px solid rgba(255,255,255,0.05);
    background: rgba(99, 102, 241, 0.1);
    color: #818cf8;
    font-size: 0.8rem;
    font-weight: 500;
    transition: background 0.15s;
    display: flex;
    align-items: center;
    gap: 6px;
}
.product-dropdown .pd-create-btn:hover {
    background: rgba(99, 102, 241, 0.25);
}
    gap: 12px;
    margin-top: 2px;
}
.product-dropdown .pd-item .pd-meta .pd-price {
    color: var(--neon-emerald, #10b981);
    font-weight: 600;
}
.product-dropdown .pd-empty {
    padding: 12px;
    color: var(--text-muted, #64748b);
    text-align: center;
    font-size: 0.8rem;
    font-style: italic;
}
.product-dropdown::-webkit-scrollbar { width: 4px; }
.product-dropdown::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 4px; }
</style>

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

// Global AJAX create product from invoice typeahead
function createProductFromTypeahead(e, btn) {
    e.preventDefault();
    const name = btn.querySelector('span').textContent;
    const td = btn.closest('td');
    const input = td.querySelector('.product-search');
    const dropdown = td.querySelector('.product-dropdown');
    
    let price = 0;
    
    const formData = new FormData();
    formData.append('action', 'ajax_create_product');
    formData.append('name', name);
    formData.append('price', price);
    
    fetch('<?= htmlspecialchars(BASE_URL) ?>/admin/crm_pipeline', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    }).then(r => r.json()).then(data => {
        if (data.success && data.product) {
            // Wait for DOMContentLoaded listener to define crmProducts, wait,
            // we dispatch a custom event or let the row update itself manually.
            // But we can just dispatch an event on the input.
            const customEv = new CustomEvent('product-created', { detail: data.product });
            input.dispatchEvent(customEv);
            dropdown.style.display = 'none';
        } else {
            alert('Failed to create product: ' + (data.error || 'Unknown error'));
        }
    }).catch(err => {
        console.error(err);
        alert('Server error creating product.');
    });
}

document.addEventListener('DOMContentLoaded', () => {
    let itemIdx = <?= empty($items) ? 1 : count($items) ?>;
    const itemsBody = document.getElementById('items-body');
    const btnAdd = document.getElementById('add-item-btn');
    const globalDiscount = document.getElementById('global-discount');

    // CRM Products data for typeahead
    const crmProducts = <?php
        $crmProducts = getDB()->query("SELECT * FROM crm_items ORDER BY name ASC")->fetchAll();
        echo json_encode($crmProducts ?: []);
    ?>;

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
        const amountPaid = parseFloat(document.getElementById('amount-paid').value) || 0;
        const balanceDue = finalTotal - amountPaid;

        document.getElementById('calc-subtotal').textContent = subtotal.toFixed(2);
        document.getElementById('calc-vat').textContent = totalVat.toFixed(2);
        document.getElementById('calc-total').textContent = finalTotal.toFixed(2);
        document.getElementById('calc-balance').textContent = (balanceDue > 0 ? balanceDue : 0).toFixed(2);
    }

    // Typeahead for product search
    function initProductSearch(input) {
        const td = input.closest('td');
        td.style.position = 'relative';
        
        let dropdown = td.querySelector('.product-dropdown');
        if (!dropdown) {
            dropdown = document.createElement('div');
            dropdown.className = 'product-dropdown';
            td.appendChild(dropdown);
        }

        let activeIndex = -1;

        input.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            activeIndex = -1;
            
            if (query.length < 1) {
                dropdown.style.display = 'none';
                return;
            }

            const matches = crmProducts.filter(p => 
                p.name.toLowerCase().includes(query) || 
                (p.category && p.category.toLowerCase().includes(query)) ||
                (p.description && p.description.toLowerCase().includes(query))
            ).slice(0, 8);

            if (matches.length === 0) {
                dropdown.innerHTML = '<div class="pd-empty">No products found.</div>' +
                                     '<div class="pd-create-btn" onmousedown="createProductFromTypeahead(event, this)"><i class="ph ph-plus"></i> Create "<span>' + escapeHtml(query) + '</span>"</div>';
            } else {
                dropdown.innerHTML = matches.map((p, i) => `
                    <div class="pd-item" data-index="${i}">
                        <div class="pd-name">${escapeHtml(p.name)}</div>
                        <div class="pd-meta">
                            <span class="pd-price">${parseFloat(p.price).toFixed(2)}</span>
                            ${p.category ? '<span>' + escapeHtml(p.category) + '</span>' : ''}
                        </div>
                    </div>
                `).join('') + '<div class="pd-create-btn" onmousedown="createProductFromTypeahead(event, this)"><i class="ph ph-plus"></i> Create "<span>' + escapeHtml(query) + '</span>"</div>';

                dropdown.querySelectorAll('.pd-item').forEach((item, idx) => {
                    item.addEventListener('mousedown', (e) => {
                        e.preventDefault();
                        selectProduct(input, matches[idx]);
                        dropdown.style.display = 'none';
                    });
                });
            }
            dropdown.style.display = 'block';
        });
        
        // Listen for new product creation
        input.addEventListener('product-created', function(e) {
            const product = e.detail;
            crmProducts.push(product); // Add to local array
            selectProduct(this, product); // Automatically select it
        });

        input.addEventListener('keydown', function(e) {
            const items = dropdown.querySelectorAll('.pd-item');
            if (!items.length || dropdown.style.display === 'none') return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                activeIndex = Math.min(activeIndex + 1, items.length - 1);
                items.forEach((it, i) => it.classList.toggle('active', i === activeIndex));
                items[activeIndex].scrollIntoView({block: 'nearest'});
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                activeIndex = Math.max(activeIndex - 1, 0);
                items.forEach((it, i) => it.classList.toggle('active', i === activeIndex));
                items[activeIndex].scrollIntoView({block: 'nearest'});
            } else if (e.key === 'Enter' && activeIndex >= 0) {
                e.preventDefault();
                const query = input.value.toLowerCase().trim();
                const matches = crmProducts.filter(p => 
                    p.name.toLowerCase().includes(query) || 
                    (p.category && p.category.toLowerCase().includes(query))
                ).slice(0, 8);
                if (matches[activeIndex]) {
                    selectProduct(input, matches[activeIndex]);
                }
                dropdown.style.display = 'none';
            } else if (e.key === 'Escape') {
                dropdown.style.display = 'none';
            }
        });

        input.addEventListener('blur', () => {
            setTimeout(() => dropdown.style.display = 'none', 150);
        });

        input.addEventListener('focus', function() {
            if (this.value.trim().length >= 1) {
                this.dispatchEvent(new Event('input'));
            }
        });
    }

    function selectProduct(input, product) {
        const row = input.closest('.item-row');
        input.value = product.name;
        
        const descInput = row.querySelector('.item-desc');
        const priceInput = row.querySelector('.item-price');
        
        if (descInput) descInput.value = product.description || '';
        if (priceInput) priceInput.value = parseFloat(product.price).toFixed(2);
        
        // Flash feedback
        row.style.backgroundColor = 'rgba(16, 185, 129, 0.15)';
        setTimeout(() => row.style.backgroundColor = '', 600);
        
        calculateTotals();
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    btnAdd.addEventListener('click', () => {
        const tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.innerHTML = `
            <td style="position:relative;"><input type="text" name="items[${itemIdx}][service_name]" class="form-input product-search" autocomplete="off" placeholder="Type to find product..." required></td>
            <td><input type="text" name="items[${itemIdx}][description]" class="form-input item-desc"></td>
            <td><input type="number" step="0.01" name="items[${itemIdx}][qty]" value="1.00" class="form-input item-qty" required></td>
            <td><input type="number" step="0.01" name="items[${itemIdx}][unit_price]" value="0.00" class="form-input item-price" required></td>
            <td><input type="number" step="0.01" name="items[${itemIdx}][vat_rate]" value="5.00" class="form-input item-vat"></td>
            <td class="item-line-total">0.00</td>
            <td><button type="button" class="admin-btn remove-item" style="color: #f43f5e; border-color: transparent;" title="Remove this line">🗑</button></td>
        `;
        itemsBody.appendChild(tr);
        itemIdx++;
        bindRowEvents(tr);
        calculateTotals();
        // Focus the new product search field
        const newInput = tr.querySelector('.product-search');
        if (newInput) newInput.focus();
    });

    function bindRowEvents(row) {
        row.querySelectorAll('input').forEach(inp => {
            inp.addEventListener('input', calculateTotals);
        });
        row.querySelector('.remove-item').addEventListener('click', (e) => {
            e.target.closest('tr').remove();
            calculateTotals();
        });
        // Init typeahead on product-search inputs
        const searchInput = row.querySelector('.product-search');
        if (searchInput) initProductSearch(searchInput);
    }

    document.querySelectorAll('.item-row').forEach(bindRowEvents);
    globalDiscount.addEventListener('input', calculateTotals);
    document.getElementById('amount-paid').addEventListener('input', calculateTotals);
    calculateTotals();
});
</script>

</body>
</html>
