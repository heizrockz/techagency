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
    <title><?= e($title) ?> — <?= APP_NAME ?></title>
    <?php require __DIR__ . '/partials/_head_assets.php'; ?>
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">

<div class="admin-layout flex w-full h-screen overflow-hidden">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>
    
    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-20 flex items-center justify-between px-8 bg-glass-bg border-b border-white/5 shrink-0 backdrop-blur-xl sticky top-0 z-[100]">
            <div class="flex flex-col">
                <div class="text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] mb-1 hidden sm:block">Invoices</div>
                <h1 class="text-xl font-black text-white tracking-tight flex items-center gap-3 group">
                    <span class="text-neon-cyan drop-shadow-[0_0_8px_rgba(6,182,212,0.4)]"><?= e($title) ?></span>
                </h1>
            </div>
            <div class="flex items-center gap-6">
                <div class="flex items-center gap-4">
                    <?php if ($invoice && isset($invoice['id']) && $invoice['id']): ?>
                        <a href="<?= baseUrl('admin/invoices?action=print&id=' . $invoice['id']) ?>" target="_blank" rel="noopener noreferrer" class="px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-neon-gold hover:bg-neon-gold/10 border border-white/5 hover:border-neon-gold/20 transition-all flex items-center gap-2">
                            <i class="ph ph-printer"></i> <span class="hidden sm:inline">Print</span>
                        </a>
                    <?php endif; ?>
                    <a href="<?= baseUrl('admin/invoices') ?>" class="px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-white hover:bg-white/5 border border-white/5 transition-all flex items-center gap-2">
                        <i class="ph ph-arrow-left"></i> <span class="hidden sm:inline">Back</span>
                    </a>
                </div>
                <?php require __DIR__ . '/partials/_topbar.php'; ?>
            </div>
        </header>

        <?php if(isset($_GET['saved'])): ?>
            <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid var(--theme-primary); color: var(--theme-primary); padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                ✅ Saved successfully!
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= baseUrl('admin/invoices') ?>" class="p-6 space-y-10">
            <input type="hidden" name="id" value="<?= $invoice['id'] ?? 0 ?>">
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 align-start">
                <!-- Document Info -->
                <div class="admin-stat-card !bg-glass-bg border border-white/5 !p-5 shadow-premium relative overflow-hidden group">
                    <div class="absolute -right-10 -top-10 w-32 h-32 bg-neon-cyan/5 rounded-full blur-3xl group-hover:bg-neon-cyan/10 transition-all"></div>
                    <div class="flex items-center gap-3 mb-5 border-b border-white/5 pb-4">
                        <div class="w-9 h-9 rounded-xl bg-neon-cyan/10 text-neon-cyan flex items-center justify-center text-xl shadow-lg border border-neon-cyan/20">
                            <i class="ph-duotone ph-file-text"></i>
                        </div>
                        <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-white">Document Parameters</h3>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="space-y-1.5">
                            <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest">Invoice Type</label>
                            <select name="type" class="w-full bg-black/40 border border-white/10 rounded-xl py-2 px-3 text-xs font-bold uppercase tracking-widest text-white focus:border-neon-cyan outline-none transition-all cursor-pointer">
                                <option value="invoice" <?= $type==='invoice'?'selected':'' ?>>Invoice</option>
                                <option value="quote" <?= $type==='quote'?'selected':'' ?>>Quote</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest">Currency</label>
                            <select name="invoice_currency" class="w-full bg-black/40 border border-white/10 rounded-xl py-2 px-3 text-xs font-bold uppercase tracking-widest text-white focus:border-neon-cyan outline-none transition-all cursor-pointer font-mono">
                                <?php foreach ($currencies as $code => $label): ?>
                                    <option value="<?= $code ?>" <?= $currency===$code?'selected':'' ?>><?= e($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="space-y-1.5">
                            <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest">Invoice Number *</label>
                            <input type="text" name="invoice_number" value="<?= e($invNum) ?>" class="w-full bg-black/40 border border-white/10 rounded-xl py-2 px-3 text-xs font-black tracking-[0.2em] text-neon-cyan focus:border-neon-cyan outline-none transition-all" required>
                            <p class="text-[8px] text-slate-600 font-bold uppercase tracking-tight">Auto-generated</p>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest">Status</label>
                            <select name="status" class="w-full bg-black/40 border border-white/10 rounded-xl py-2 px-3 text-xs font-bold uppercase tracking-widest text-white focus:border-neon-cyan outline-none transition-all cursor-pointer">
                                <option value="draft" <?= $status==='draft'?'selected':'' ?>>Draft</option>
                                <option value="sent" <?= $status==='sent'?'selected':'' ?>>Sent</option>
                                <option value="paid" <?= $status==='paid'?'selected':'' ?>>Paid</option>
                                <option value="cancelled" <?= $status==='cancelled'?'selected':'' ?>>Cancelled</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-4 pt-4 border-t border-white/5">
                        <div class="space-y-1.5">
                            <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest">Account Manager</label>
                            <select name="salesperson_id" class="w-full bg-black/40 border border-white/10 rounded-xl py-2 px-3 text-xs font-bold uppercase tracking-widest text-slate-400 focus:border-neon-cyan outline-none transition-all cursor-pointer">
                                <option value="0">-- Unassigned --</option>
                                <?php foreach ($salespersons as $s): ?>
                                    <option value="<?= $s['id'] ?>" <?= ($invoice && isset($invoice['salesperson_id']) && $invoice['salesperson_id'] == $s['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($s['full_name'] ?: $s['username']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest">Collaborators</label>
                            <div class="bg-black/40 border border-white/10 rounded-2xl p-3 max-h-[120px] overflow-y-auto crm-main-scroll grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <?php foreach ($salespersons as $s): ?>
                                    <label class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-white/5 transition-all cursor-pointer group/item">
                                        <div class="relative flex items-center justify-center">
                                            <input type="checkbox" name="extra_salesperson_ids[]" value="<?= $s['id'] ?>" 
                                                <?= (isset($extra_salesperson_ids) && in_array($s['id'], $extra_salesperson_ids)) ? 'checked' : '' ?>
                                                class="peer appearance-none w-4 h-4 rounded border border-white/20 checked:border-neon-cyan checked:bg-neon-cyan/20 transition-all cursor-pointer">
                                            <i class="ph ph-check absolute text-[9px] text-neon-cyan opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                                        </div>
                                        <span class="text-[9px] font-black text-slate-500 group-hover/item:text-slate-300 transition-colors uppercase tracking-widest"><?= htmlspecialchars($s['full_name'] ?: $s['username']) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Client Info -->
                <div class="admin-stat-card !bg-glass-bg border border-white/5 !p-5 shadow-premium relative overflow-hidden group">
                    <div class="absolute -right-10 -top-10 w-32 h-32 bg-neon-emerald/5 rounded-full blur-3xl group-hover:bg-neon-emerald/10 transition-all"></div>
                    <div class="flex items-center gap-3 mb-5 border-b border-white/5 pb-4">
                        <div class="w-9 h-9 rounded-xl bg-neon-emerald/10 text-neon-emerald flex items-center justify-center text-xl shadow-lg border border-neon-emerald/20">
                            <i class="ph-duotone ph-user-circle-plus"></i>
                        </div>
                        <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-white">Client Details</h3>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest">Contact</label>
                            <select name="contact_id" id="contact-select" class="w-full bg-black/40 border border-white/10 rounded-xl py-2 px-3 text-xs font-black uppercase tracking-widest text-neon-emerald focus:border-neon-emerald outline-none transition-all cursor-pointer" onchange="fillContactDetails(this.value)">
                                <option value="0">— Select Contact —</option>
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

                        <div class="space-y-1.5">
                            <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest">Client Name *</label>
                            <input type="text" name="client_name" id="client-name" value="<?= e($invoice['client_name'] ?? '') ?>" class="w-full bg-black/40 border border-white/10 rounded-xl py-2 px-3 text-sm font-black tracking-tight text-white focus:border-neon-emerald outline-none transition-all placeholder:text-slate-800" required placeholder="Client name">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest">Email</label>
                                <input type="email" name="client_email" id="client-email" value="<?= e($invoice['client_email'] ?? '') ?>" class="w-full bg-black/40 border border-white/10 rounded-xl py-2 px-3 text-xs font-bold text-white focus:border-neon-emerald outline-none transition-all placeholder:text-slate-800" placeholder="email@example.com">
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest">Phone</label>
                                <input type="text" name="client_phone" id="client-phone" value="<?= e($invoice['client_phone'] ?? '') ?>" class="w-full bg-black/40 border border-white/10 rounded-xl py-2 px-3 text-xs font-bold text-white focus:border-neon-emerald outline-none transition-all placeholder:text-slate-800" placeholder="+00 000 0000">
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest">Billing Address</label>
                            <textarea name="client_address" id="client-address" class="w-full bg-black/40 border border-white/10 rounded-xl py-2 px-3 text-xs font-medium text-slate-400 focus:border-neon-emerald outline-none transition-all min-h-[80px] placeholder:text-slate-800" placeholder="Billing address..."><?= e($invoice['client_address'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Line Items -->
            <div class="space-y-4">
                <div class="flex items-center justify-between border-b border-white/5 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-neon-gold/10 text-neon-gold flex items-center justify-center text-lg border border-neon-gold/20 shadow-lg shadow-neon-gold/5">
                            <i class="ph-duotone ph-list-numbers"></i>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-white">Line Items</h3>
                            <p class="text-[8px] text-slate-600 font-bold uppercase tracking-tight mt-0.5">Products / services for this invoice</p>
                        </div>
                    </div>
                    <button type="button" class="px-3 py-1.5 bg-neon-gold/10 hover:bg-neon-gold/20 text-neon-gold text-[9px] font-black uppercase tracking-widest rounded-lg transition-all border border-neon-gold/20 flex items-center gap-2 group" id="add-item-btn">
                        <i class="ph ph-plus-circle group-hover:rotate-90 transition-transform"></i> Add Item
                    </button>
                </div>

                <div class="admin-table-wrapper backdrop-blur-xl border border-white/5 rounded-2xl shadow-premium" style="overflow: visible;">
                    <table class="admin-table w-full text-left border-collapse" id="items-table" style="overflow: visible;">
                        <thead>
                            <tr class="text-slate-500 text-[8px] font-black uppercase tracking-[0.3em] bg-white/[0.01]">
                                <th class="py-3 px-4 w-[30%] text-neon-cyan">Product / Service</th>
                                <th class="py-3 px-4 w-[25%] opacity-70">Description</th>
                                <th class="py-3 px-4 w-[8%] text-center">Qty</th>
                                <th class="py-3 px-4 w-[12%] text-center">Unit Price</th>
                                <th class="py-3 px-4 w-[8%] text-center">Tax %</th>
                                <th class="py-3 px-4 w-[12%] text-right">Total</th>
                                <th class="py-3 px-4 w-[5%]"></th>
                            </tr>
                        </thead>
                        <tbody id="items-body" class="divide-y divide-white/[0.02]" style="overflow: visible;">
                            <?php if (empty($items)): ?>
                                <tr class="item-row hover:bg-white/[0.02] transition-colors group/row border-b border-white/[0.03] last:border-0 relative" style="overflow: visible;">
                                    <td class="py-3 px-4 relative" style="overflow: visible;">
                                        <input type="text" name="items[0][service_name]" class="w-full bg-black/40 border border-white/10 rounded-lg py-1.5 px-3 text-xs font-black tracking-tight text-white focus:border-neon-cyan outline-none transition-all placeholder:text-slate-800 product-search" autocomplete="off" placeholder="Search product..." required>
                                    </td>
                                    <td class="py-3 px-4">
                                        <input type="text" name="items[0][description]" class="w-full bg-black/20 border border-white/[0.07] rounded-lg py-1.5 px-3 text-xs text-slate-400 focus:outline-none focus:border-neon-cyan/30 placeholder:text-slate-700 item-desc transition-all" placeholder="Description...">
                                    </td>
                                    <td class="py-3 px-4">
                                        <input type="number" step="0.01" name="items[0][qty]" value="1.00" class="w-full bg-black/20 border border-white/5 rounded-lg py-1.5 px-2 text-xs font-black text-center text-white focus:border-neon-cyan outline-none transition-all item-qty" required>
                                    </td>
                                    <td class="py-3 px-4">
                                        <input type="number" step="0.01" name="items[0][unit_price]" value="0.00" class="w-full bg-black/20 border border-white/5 rounded-lg py-1.5 px-2 text-xs font-black text-center text-white focus:border-neon-cyan outline-none transition-all item-price" required>
                                    </td>
                                    <td class="py-3 px-4">
                                        <input type="number" step="0.01" name="items[0][vat_rate]" value="5.00" class="w-full bg-black/20 border border-white/5 rounded-lg py-1.5 px-2 text-xs font-bold text-center text-slate-500 focus:border-neon-cyan outline-none transition-all item-vat">
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <span class="text-xs font-black text-white tracking-tight item-line-total">0.00</span>
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <button type="button" class="w-7 h-7 rounded-lg bg-neon-rose/5 text-neon-rose hover:bg-neon-rose/20 transition-all flex items-center justify-center remove-item opacity-0 group-hover/row:opacity-100" title="Remove">
                                            <i class="ph ph-trash-simple text-sm"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($items as $i => $item): ?>
                                    <tr class="item-row hover:bg-white/[0.02] transition-colors group/row border-b border-white/[0.03] last:border-0 relative" style="overflow: visible;">
                                        <td class="py-3 px-4 relative" style="overflow: visible;">
                                            <input type="text" name="items[<?= $i ?>][service_name]" value="<?= e($item['service_name']) ?>" class="w-full bg-black/40 border border-white/10 rounded-lg py-1.5 px-3 text-xs font-black tracking-tight text-white focus:border-neon-cyan outline-none transition-all placeholder:text-slate-800 product-search" autocomplete="off" placeholder="Search product..." required>
                                        </td>
                                        <td class="py-3 px-4">
                                            <input type="text" name="items[<?= $i ?>][description]" value="<?= e($item['description'] ?? '') ?>" class="w-full bg-black/20 border border-white/[0.07] rounded-lg py-1.5 px-3 text-xs text-slate-400 focus:outline-none focus:border-neon-cyan/30 placeholder:text-slate-700 item-desc transition-all" placeholder="Description...">
                                        </td>
                                        <td class="py-3 px-4">
                                            <input type="number" step="0.01" name="items[<?= $i ?>][qty]" value="<?= e($item['qty']) ?>" class="w-full bg-black/20 border border-white/5 rounded-lg py-1.5 px-2 text-xs font-black text-center text-white focus:border-neon-cyan outline-none transition-all item-qty" required>
                                        </td>
                                        <td class="py-3 px-4">
                                            <input type="number" step="0.01" name="items[<?= $i ?>][unit_price]" value="<?= e($item['unit_price']) ?>" class="w-full bg-black/20 border border-white/5 rounded-lg py-1.5 px-2 text-xs font-black text-center text-white focus:border-neon-cyan outline-none transition-all item-price" required>
                                        </td>
                                        <td class="py-3 px-4">
                                            <input type="number" step="0.01" name="items[<?= $i ?>][vat_rate]" value="<?= e($item['vat_rate']) ?>" class="w-full bg-black/20 border border-white/5 rounded-lg py-1.5 px-2 text-xs font-bold text-center text-slate-500 focus:border-neon-cyan outline-none transition-all item-vat">
                                        </td>
                                        <td class="py-3 px-4 text-right">
                                            <span class="text-xs font-black text-white tracking-tight item-line-total">0.00</span>
                                        </td>
                                        <td class="py-3 px-4 text-right">
                                            <button type="button" class="w-7 h-7 rounded-lg bg-neon-rose/5 text-neon-rose hover:bg-neon-rose/20 transition-all flex items-center justify-center remove-item opacity-0 group-hover/row:opacity-100" title="Remove">
                                                <i class="ph ph-trash-simple text-sm"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Totals -->
            <div class="flex justify-end pt-6">
                <div class="w-full max-w-sm space-y-3 bg-white/[0.03] backdrop-blur-3xl border border-white/5 p-5 rounded-2xl shadow-premium relative group">
                    <div class="absolute -left-10 -bottom-10 w-24 h-24 bg-neon-cyan/5 rounded-full blur-2xl"></div>
                    
                    <div class="flex justify-between items-center text-[9px] font-black uppercase tracking-widest text-slate-500">
                        <span>Subtotal:</span>
                        <span class="text-white font-black tracking-tight text-sm" id="calc-subtotal">0.00</span>
                    </div>

                    <div class="flex justify-between items-center text-[9px] font-black uppercase tracking-widest text-slate-500">
                        <span>Discount:</span>
                        <div class="flex items-center gap-2">
                            <span class="text-neon-rose">-</span>
                            <input type="number" step="0.01" name="discount" id="global-discount" value="<?= e($invoice['discount'] ?? '0.00') ?>" class="w-24 bg-black/40 border border-white/10 rounded-xl py-1.5 px-3 text-xs font-black text-neon-rose text-right focus:border-neon-rose outline-none transition-all">
                        </div>
                    </div>

                    <div class="flex justify-between items-center text-[9px] font-black uppercase tracking-widest text-slate-500">
                        <span>Tax (VAT):</span>
                        <span class="text-white font-black tracking-tight text-sm" id="calc-vat">0.00</span>
                    </div>

                    <div class="pt-4 mt-2 border-t border-white/5 space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] font-black uppercase tracking-[0.3em] text-neon-cyan group-hover:drop-shadow-[0_0_8px_rgba(6,182,212,0.4)] transition-all">Total:</span>
                            <span class="text-xl font-black text-white tracking-tighter" id="calc-total">0.00</span>
                        </div>

                        <div class="flex justify-between items-center pt-3 border-t border-white/[0.02]">
                            <span class="text-[9px] font-black uppercase tracking-widest text-neon-emerald">Amount Paid:</span>
                            <input type="number" step="0.01" name="amount_paid" id="amount-paid" value="<?= e($invoice['amount_paid'] ?? '0.00') ?>" class="w-28 bg-neon-emerald/5 border border-neon-emerald/20 rounded-xl py-2 px-3 text-xs font-black text-neon-emerald text-right focus:border-neon-emerald outline-none transition-all shadow-[0_0_15px_rgba(16,185,129,0.05)]">
                        </div>

                        <div class="flex justify-between items-center pt-2">
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-neon-rose">Balance Due:</span>
                            <span class="text-lg font-black text-neon-rose drop-shadow-[0_0_10px_rgba(244,63,94,0.3)]" id="calc-balance">0.00</span>
                        </div>
                    </div>
                    
                    <?php if (isset($invoice['id']) && $invoice['id'] > 0 && ($invoice['amount_paid'] ?? 0) > 0): ?>
                        <div class="pt-6 text-right">
                            <a href="<?= baseUrl('admin/invoices?action=receipt&id=' . $invoice['id']) ?>" target="_blank" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-neon-emerald/10 text-neon-emerald text-[9px] font-black uppercase tracking-widest hover:bg-neon-emerald/20 border border-neon-emerald/20 transition-all">
                                <i class="ph ph-receipt"></i> View Receipt
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Notes, Terms & Payment Terms -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="admin-stat-card !bg-black/20 border border-white/5 !p-4 space-y-2">
                    <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest">Notes</label>
                    <textarea name="notes" class="w-full bg-transparent border border-white/5 rounded-lg py-2 px-3 text-xs text-slate-400 focus:border-neon-cyan outline-none transition-all min-h-[100px] placeholder:text-slate-800" placeholder="Internal notes..."><?= e($invoice['notes'] ?? '') ?></textarea>
                </div>
                <div class="admin-stat-card !bg-black/20 border border-white/5 !p-4 space-y-2">
                    <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest">Terms & Conditions</label>
                    <textarea name="terms" class="w-full bg-transparent border border-white/5 rounded-lg py-2 px-3 text-xs text-slate-400 focus:border-neon-cyan outline-none transition-all min-h-[100px] placeholder:text-slate-800" placeholder="Terms and conditions..."><?= e($invoice['terms'] ?? $defaultTerms ?? '') ?></textarea>
                </div>
                <div class="admin-stat-card !bg-black/20 border border-white/5 !p-4 space-y-2">
                    <label class="block text-[9px] font-black text-slate-500 uppercase tracking-widest">Payment Terms</label>
                    <textarea name="payment_terms" class="w-full bg-transparent border border-white/5 rounded-lg py-2 px-3 text-xs text-slate-400 focus:border-neon-cyan outline-none transition-all min-h-[100px] placeholder:text-slate-800" placeholder="e.g. 50% upfront, 50% on delivery."><?= e($paymentTerms) ?></textarea>
                </div>
            </div>

            <div class="flex justify-end pt-8 border-t border-white/5">
                <button type="submit" class="px-8 py-3 bg-neon-cyan hover:bg-cyan-400 text-black text-[10px] font-black uppercase tracking-[0.2em] rounded-xl transition-all shadow-[0_0_25px_rgba(6,182,212,0.3)] hover:shadow-[0_0_35px_rgba(6,182,212,0.5)] transform hover:-translate-y-1 flex items-center gap-2 active:scale-95">
                    <i class="ph ph-shield-check text-base"></i> Save Invoice
                </button>
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
        tr.className = 'item-row hover:bg-white/[0.02] transition-colors group/row border-b border-white/[0.03] last:border-0 relative';
        tr.style.overflow = 'visible';
        tr.innerHTML = `
            <td class="py-3 px-4 relative" style="overflow: visible;">
                <input type="text" name="items[${itemIdx}][service_name]" class="w-full bg-black/40 border border-white/10 rounded-lg py-1.5 px-3 text-xs font-black tracking-tight text-white focus:border-neon-cyan outline-none transition-all placeholder:text-slate-800 product-search" autocomplete="off" placeholder="Search product..." required>
            </td>
            <td class="py-3 px-4">
                <input type="text" name="items[${itemIdx}][description]" class="w-full bg-black/20 border border-white/[0.07] rounded-lg py-1.5 px-3 text-xs text-slate-400 focus:outline-none focus:border-neon-cyan/30 placeholder:text-slate-700 item-desc transition-all" placeholder="Description...">
            </td>
            <td class="py-3 px-4">
                <input type="number" step="0.01" name="items[${itemIdx}][qty]" value="1.00" class="w-full bg-black/20 border border-white/5 rounded-lg py-1.5 px-2 text-xs font-black text-center text-white focus:border-neon-cyan outline-none transition-all item-qty" required>
            </td>
            <td class="py-3 px-4">
                <input type="number" step="0.01" name="items[${itemIdx}][unit_price]" value="0.00" class="w-full bg-black/20 border border-white/5 rounded-lg py-1.5 px-2 text-xs font-black text-center text-white focus:border-neon-cyan outline-none transition-all item-price" required>
            </td>
            <td class="py-3 px-4">
                <input type="number" step="0.01" name="items[${itemIdx}][vat_rate]" value="5.00" class="w-full bg-black/20 border border-white/5 rounded-lg py-1.5 px-2 text-xs font-bold text-center text-slate-500 focus:border-neon-cyan outline-none transition-all item-vat">
            </td>
            <td class="py-3 px-4 text-right">
                <span class="text-xs font-black text-white tracking-tight item-line-total">0.00</span>
            </td>
            <td class="py-3 px-4 text-right">
                <button type="button" class="w-7 h-7 rounded-lg bg-neon-rose/5 text-neon-rose hover:bg-neon-rose/20 transition-all flex items-center justify-center remove-item opacity-0 group-hover/row:opacity-100" title="Remove">
                    <i class="ph ph-trash-simple text-sm"></i>
                </button>
            </td>
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
