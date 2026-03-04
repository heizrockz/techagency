<?php
$pageTitle = 'CRM Payments';
$currentPage = 'crm_payments';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= htmlspecialchars($pageTitle . ' - ' . APP_NAME) ?></title>
    <?php require __DIR__ . '/partials/_head_assets.php'; ?>
    <style>
        .progress-bar-container { transition: all 0.3s ease; opacity: 0; pointer-events: none; }
        .progress-bar-container.active { opacity: 1; pointer-events: auto; }
        .file-list-item { animation: slideIn 0.2s ease-out; }
        @keyframes slideIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="bg-[#0b0e14]">
<div class="admin-layout flex w-full h-screen overflow-hidden">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>
    <div class="flex-1 flex flex-col min-w-0 bg-[#0b0e14]">
        <header class="h-20 flex items-center justify-between px-8 bg-glass-bg border-b border-white/5 shrink-0 backdrop-blur-xl sticky top-0 z-[100]">
            <div class="flex flex-col">
                <div class="text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] mb-1 hidden sm:block">Fiscal Intelligence</div>
                <h1 class="text-xl font-black text-white tracking-tight flex items-center gap-3 group">
                    <span class="text-neon-cyan drop-shadow-[0_0_8px_rgba(6,182,212,0.4)]">Payments</span>
                    <span class="opacity-20 translate-y-px hidden sm:inline">/</span>
                    <span class="text-[10px] tracking-widest text-slate-400 uppercase font-black hidden sm:inline-block">Expenditures</span>
                </h1>
            </div>
            <div class="flex items-center gap-6">
                <button onclick="openPaymentModal()" class="px-3 sm:px-6 py-2.5 bg-neon-cyan hover:bg-cyan-400 text-black text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-lg active:scale-95 flex items-center gap-2">
                    <i class="ph-bold ph-plus-circle text-lg"></i> <span class="hidden sm:inline">Record</span>
                </button>
                <?php require __DIR__ . '/partials/_topbar.php'; ?>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-6 bg-[#0b0e14] w-full h-full crm-main-scroll">
            <?php if ($flash = getFlash()): ?>
                <div class="mb-6 p-4 rounded-xl border border-emerald-500/20 bg-emerald-500/10 text-emerald-400 text-sm flex items-center gap-2">
                    <i class="ph ph-check-circle text-lg"></i>
                    <?= htmlspecialchars($flash) ?>
                </div>
            <?php endif; ?>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
                <div class="admin-stat-card !bg-glass-bg border border-white/5 p-8 rounded-3xl relative overflow-hidden group hover:border-neon-cyan/40 transition-all shadow-premium">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-neon-cyan/5 blur-[60px] rounded-full -mr-16 -mt-16 group-hover:bg-neon-cyan/10 transition-colors"></div>
                    <div class="flex items-center justify-between mb-6">
                        <div class="w-14 h-14 rounded-2xl bg-neon-cyan/10 flex items-center justify-center border border-neon-cyan/20 group-hover:scale-110 transition-transform">
                            <i class="ph-bold ph-chart-pie text-3xl text-neon-cyan"></i>
                        </div>
                        <div class="text-right">
                            <div class="text-[9px] font-black text-slate-600 uppercase tracking-[0.2em] mb-1">Total Outflow</div>
                            <div class="text-3xl font-black text-white tracking-tighter">$<?= number_format($totalSpend, 2) ?></div>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">Global Portfolio Drain</span>
                    </div>
                </div>

                <div class="admin-stat-card !bg-glass-bg border border-white/5 p-8 rounded-3xl relative overflow-hidden group hover:border-neon-purple/40 transition-all shadow-premium">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-neon-purple/5 blur-[60px] rounded-full -mr-16 -mt-16 group-hover:bg-neon-purple/10 transition-colors"></div>
                    <div class="flex items-center justify-between mb-6">
                        <div class="w-14 h-14 rounded-2xl bg-neon-purple/10 flex items-center justify-center border border-neon-purple/20 group-hover:scale-110 transition-transform">
                            <i class="ph-bold ph-calendar text-3xl text-neon-purple"></i>
                        </div>
                        <div class="text-right">
                            <div class="text-[9px] font-black text-slate-600 uppercase tracking-[0.2em] mb-1">Monthly Burn</div>
                            <div class="text-3xl font-black text-white tracking-tighter">$<?= number_format($monthlySpend, 2) ?></div>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">Active Operational Speed</span>
                    </div>
                </div>

                <div class="admin-stat-card !bg-glass-bg border border-white/5 p-8 rounded-3xl relative overflow-hidden group hover:border-neon-emerald/40 transition-all shadow-premium">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-neon-emerald/5 blur-[60px] rounded-full -mr-16 -mt-16 group-hover:bg-neon-emerald/10 transition-colors"></div>
                    <div class="flex items-center justify-between mb-6">
                        <div class="w-14 h-14 rounded-2xl bg-neon-emerald/10 flex items-center justify-center border border-neon-emerald/20 group-hover:scale-110 transition-transform">
                            <i class="ph-bold ph-receipt text-3xl text-neon-emerald"></i>
                        </div>
                        <div class="text-right">
                            <div class="text-[9px] font-black text-slate-600 uppercase tracking-[0.2em] mb-1">Last Transmission</div>
                            <div class="text-lg font-black text-white tracking-tighter truncate max-w-[150px] uppercase">
                                <?= !empty($payments) ? htmlspecialchars($payments[0]['title']) : 'NULL' ?>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">Live Transaction Entry</span>
                    </div>
                </div>
            </div>

            <!-- Ledger Activity -->
            <div class="admin-table-wrapper backdrop-blur-xl border border-white/5 rounded-3xl overflow-hidden flex flex-col shadow-premium">
                <div class="px-8 py-6 border-b border-white/5 flex flex-col sm:flex-row items-center justify-between gap-6 bg-white/[0.01]">
                    <form class="flex items-center gap-4 w-full sm:w-auto" method="GET" action="<?= BASE_URL ?>/admin/crm_payments">
                        <div class="relative w-full sm:w-96 group">
                            <i class="ph ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-600 group-focus-within:text-neon-cyan transition-colors"></i>
                            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Filter historical data sequences..." class="w-full bg-black/40 border border-white/10 rounded-2xl py-3 pl-12 pr-4 text-[11px] font-black uppercase tracking-widest text-white focus:border-neon-cyan outline-none transition-all placeholder:text-slate-800">
                        </div>
                        <select name="category" onchange="this.form.submit()" class="bg-black/40 border border-white/10 rounded-2xl py-3 px-6 text-[10px] font-black uppercase tracking-widest text-slate-400 focus:border-neon-cyan outline-none transition-all cursor-pointer hover:bg-black/60 appearance-none min-w-[180px]">
                            <option value="">Classification Filter</option>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?= $cat ?>" <?= $categoryFilter === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                    <div class="text-[10px] font-black uppercase tracking-[0.3em] text-white flex items-center gap-3">
                        <i class="ph ph-activity text-neon-cyan animate-pulse"></i>
                        <span class="opacity-50"><?= count($payments) ?> Recorded Clusters</span>
                    </div>
                </div>

                <div class="overflow-x-auto crm-main-scroll">
                    <table class="admin-table w-full text-left border-collapse min-w-[1000px]">
                        <thead>
                            <tr class="text-slate-600 text-[8px] font-black uppercase tracking-[0.3em] bg-white/[0.01]">
                                <th class="py-6 px-8">Transaction Origin</th>
                                <th class="py-6 px-6 text-center">Protocol Class</th>
                                <th class="py-6 px-6">Operational Link</th>
                                <th class="py-6 px-6 text-right font-mono tracking-normal">Valuation (AED)</th>
                                <th class="py-6 px-8 text-right tracking-[0.2em]">Navigation</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/[0.02]">
                            <?php if(empty($payments)): ?>
                                <tr>
                                    <td colspan="5" class="py-20 text-center">
                                        <div class="text-slate-700 text-[10px] font-black uppercase tracking-widest">No historical sequences detected.</div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($payments as $p): ?>
                                <tr class="hover:bg-white/[0.03] transition-all group/row border-b border-white/[0.03] last:border-0 relative">
                                    <td class="py-6 px-8" data-label="Title">
                                        <div class="flex items-center gap-5">
                                            <div class="w-12 h-12 rounded-2xl bg-black/40 border border-white/10 flex items-center justify-center text-xl shadow-inner group-hover/row:border-neon-cyan/40 group-hover/row:bg-neon-cyan/5 transition-all duration-300">
                                                <i class="ph-bold ph-receipt text-neon-cyan group-hover/row:scale-110 transition-transform"></i>
                                            </div>
                                            <div>
                                                <span class="text-white font-black text-[11px] uppercase tracking-wider block mb-1 group-hover/row:text-neon-cyan transition-colors"><?= htmlspecialchars($p['title']) ?></span>
                                                <div class="flex items-center gap-3">
                                                    <span class="text-[9px] text-slate-600 uppercase tracking-widest font-black flex items-center gap-1.5">
                                                        <i class="ph ph-calendar-blank text-[11px]"></i>
                                                        <?= date('d M Y', strtotime($p['payment_date'])) ?>
                                                    </span>
                                                    <?php if(!empty($p['attachments'])): ?>
                                                        <div class="flex items-center gap-1 px-2 py-0.5 rounded-md bg-neon-cyan/5 border border-neon-cyan/20">
                                                            <i class="ph-bold ph-paperclip text-neon-cyan text-[8px]"></i>
                                                            <span class="text-[8px] text-neon-cyan font-black uppercase tracking-widest"><?= count($p['attachments']) ?> UNIT</span>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-6 px-6 text-center" data-label="Protocol">
                                        <span class="inline-flex px-3 py-1 rounded-lg bg-white/5 border border-white/10 text-[8px] text-slate-400 font-black uppercase tracking-widest hover:border-neon-cyan/30 transition-colors">
                                            <?= htmlspecialchars($p['category']) ?>
                                        </span>
                                    </td>
                                    <td class="py-6 px-6" data-label="Link">
                                        <?php if($p['project_name']): ?>
                                            <a href="<?= BASE_URL ?>/admin/crm_opportunity?id=<?= $p['opportunity_id'] ?>" class="group/link flex items-center gap-3 transition-all">
                                                <div class="w-8 h-8 rounded-xl bg-white/5 flex items-center justify-center group-hover/link:bg-neon-cyan/10 border border-white/5 group-hover/link:border-neon-cyan/20 transition-all">
                                                    <i class="ph ph-rocket text-slate-500 group-hover/link:text-neon-cyan text-sm"></i>
                                                </div>
                                                <span class="text-[10px] font-black text-slate-500 group-hover/link:text-white uppercase tracking-widest truncate max-w-[180px]"><?= htmlspecialchars($p['project_name']) ?></span>
                                            </a>
                                        <?php else: ?>
                                            <div class="flex items-center gap-3 text-slate-800 text-[9px] font-black uppercase tracking-widest">
                                                <div class="w-1 h-1 rounded-full bg-slate-800"></div>
                                                GLOBAL OBJECTIVE
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-6 px-6 text-right" data-label="Valuation">
                                        <div class="flex flex-col items-end">
                                            <span class="text-white font-black text-lg tracking-tighter">$<?= number_format($p['amount'], 2) ?></span>
                                            <span class="text-[8px] text-slate-700 font-bold uppercase tracking-widest mt-1">DRAIN QUANTUM</span>
                                        </div>
                                    </td>
                                    <td class="py-6 px-8 text-right" data-label="Navigation">
                                        <div class="flex justify-end gap-3 opacity-0 group-hover/row:opacity-100 transition-all translate-x-4 group-hover/row:translate-x-0">
                                            <button onclick='viewPayment(<?= json_encode($p) ?>)' class="w-10 h-10 rounded-xl bg-neon-cyan/10 text-neon-cyan hover:bg-neon-cyan hover:text-black border border-neon-cyan/20 transition-all flex items-center justify-center shadow-lg active:scale-90" title="Audit Sequence">
                                                <i class="ph-bold ph-eye text-lg"></i>
                                            </button>
                                            <button onclick='editPayment(<?= json_encode($p) ?>)' class="w-10 h-10 rounded-xl bg-white/5 text-slate-400 hover:text-white border border-white/10 transition-all flex items-center justify-center shadow-lg active:scale-90" title="Modify Intel">
                                                <i class="ph-bold ph-pencil-simple text-lg"></i>
                                            </button>
                                            <form action="<?= BASE_URL ?>/admin/crm_payments" method="POST" id="deletePayment_<?= $p['id'] ?>" class="inline-block">
                                                <input type="hidden" name="action" value="delete_payment">
                                                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                                <button type="button" onclick="showDeleteModal('CLUSTER-<?= $p['id'] ?>', 'deletePayment_<?= $p['id'] ?>')" class="w-10 h-10 rounded-xl bg-neon-rose/5 text-neon-rose hover:bg-neon-rose hover:text-white border border-neon-rose/20 transition-all flex items-center justify-center shadow-lg active:scale-90">
                                                    <i class="ph-bold ph-trash text-lg"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Add/Edit Expense Modal -->
<div id="paymentModal" class="hidden fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-[#1a2333] border border-white/10 rounded-3xl w-full max-w-lg shadow-2xl relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-transparent pointer-events-none"></div>
        
        <div class="p-6 border-b border-white/5 flex items-center justify-between relative z-10">
            <h3 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="ph ph-file-plus text-primary" id="modalIcon"></i> <span id="modalTitle">Record Expenditure</span>
            </h3>
            <button onclick="closePaymentModal()" class="w-8 h-8 rounded-full bg-white/5 text-slate-400 hover:text-white transition-all flex items-center justify-center">
                <i class="ph ph-x text-xl"></i>
            </button>
        </div>

        <form id="paymentForm" action="<?= BASE_URL ?>/admin/crm_payments" method="POST" enctype="multipart/form-data" class="p-6 space-y-4 relative z-10 font-medium">
            <input type="hidden" name="action" value="add_payment" id="formAction">
            <input type="hidden" name="id" value="" id="paymentId">
            
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Short Description / Title</label>
                    <input type="text" name="title" id="pTitle" required class="w-full bg-black/40 border border-white/10 rounded-xl py-3 px-4 text-white focus:border-primary outline-none transition-all" placeholder="e.g. Office Rent Feb 2026">
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Amount ($)</label>
                    <input type="number" step="0.01" name="amount" id="pAmount" required class="w-full bg-black/40 border border-white/10 rounded-xl py-3 px-4 text-white focus:border-primary outline-none transition-all" placeholder="0.00">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Category</label>
                    <select name="category" id="pCategory" class="w-full bg-black/40 border border-white/10 rounded-xl py-3 px-4 text-white focus:border-primary outline-none transition-all">
                        <?php foreach($categories as $cat): ?>
                            <option value="<?= $cat ?>"><?= $cat ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Payment Date</label>
                    <input type="date" name="payment_date" id="pDate" value="<?= date('Y-m-d') ?>" class="w-full bg-black/40 border border-white/10 rounded-xl py-3 px-4 text-white focus:border-primary outline-none transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Project Link</label>
                    <select name="opportunity_id" id="pOpp" class="w-full bg-black/40 border border-white/10 rounded-xl py-3 px-4 text-white focus:border-primary outline-none transition-all">
                        <option value="">None / General</option>
                        <?php foreach($opportunities as $opp): ?>
                            <option value="<?= $opp['id'] ?>"><?= htmlspecialchars($opp['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-span-2">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Internal Notes</label>
                    <textarea name="notes" id="pNotes" rows="2" class="w-full bg-black/40 border border-white/10 rounded-xl py-2 px-4 text-white focus:border-primary outline-none transition-all placeholder:text-slate-700" placeholder="Optional details..."></textarea>
                </div>

                <div class="col-span-2">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Upload Attachments</label>
                    <div class="relative group">
                        <input type="file" name="attachments[]" id="fileInput" multiple class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <div class="w-full bg-black/40 border border-dashed border-white/10 rounded-xl py-4 flex flex-col items-center justify-center group-hover:border-primary/50 transition-all">
                            <i class="ph ph-cloud-arrow-up text-2xl text-slate-500 group-hover:text-primary mb-1"></i>
                            <span class="text-xs text-slate-500 group-hover:text-slate-300">Click or drag files to upload</span>
                        </div>
                    </div>
                    <div id="fileList" class="mt-3 space-y-2"></div>
                </div>
            </div>

            <div class="progress-bar-container bg-black/40 border border-white/5 rounded-full h-1.5 mt-4 overflow-hidden" id="uploadProgressContainer">
                <div class="bg-primary h-full w-0 transition-all duration-300 shadow-[0_0_10px_rgba(var(--primary-rgb),0.5)]" id="uploadProgressBar"></div>
            </div>

            <div class="pt-2 flex justify-end gap-3">
                <button type="button" onclick="closePaymentModal()" class="px-6 py-3 bg-white/5 text-slate-400 font-bold uppercase tracking-widest text-[11px] rounded-xl hover:bg-white/10 transition-all">Cancel</button>
                <button type="submit" id="submitBtn" class="px-8 py-3 bg-primary text-white font-bold uppercase tracking-widest text-xs rounded-xl hover:shadow-[0_0_20px_rgba(var(--primary-rgb),0.3)] transition-all min-w-[140px]">Save Record</button>
            </div>
        </form>
    </div>
</div>

<!-- View Detail Modal -->
<div id="viewModal" class="hidden fixed inset-0 z-[60] bg-black/90 backdrop-blur-md flex items-center justify-center p-4">
    <div class="bg-[#1a2333] border border-white/10 rounded-[2.5rem] w-full max-w-2xl shadow-2xl relative overflow-hidden">
        <div class="p-8 space-y-8">
            <div class="flex justify-between items-start">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-primary/10 text-primary flex items-center justify-center text-3xl shadow-inner">
                        <i class="ph ph-receipt"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-white" id="vTitle">Expenditure Title</h2>
                        <span class="text-xs text-slate-500 font-bold uppercase tracking-widest" id="vCategory">Category</span>
                    </div>
                </div>
                <button onclick="closeViewModal()" class="w-10 h-10 rounded-full bg-white/5 text-slate-400 hover:text-white transition-all flex items-center justify-center">
                    <i class="ph ph-x text-xl"></i>
                </button>
            </div>

            <div class="grid grid-cols-2 gap-8 py-8 border-y border-white/5">
                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Amount</label>
                    <span class="text-3xl font-black text-white" id="vAmount">$0.00</span>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Payment Date</label>
                    <span class="text-xl font-bold text-slate-300" id="vDate">Jan 01, 2026</span>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Linked Project</label>
                    <span class="text-sm font-medium text-primary" id="vProject">General Expenditure</span>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Recorded By</label>
                    <span class="text-sm font-medium text-slate-400" id="vAdmin">Administrator</span>
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3">Internal Notes</label>
                <p class="text-slate-400 text-sm leading-relaxed italic bg-black/20 p-4 rounded-2xl border border-white/5" id="vNotes">No notes available.</p>
            </div>

            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-4">Attached Documents</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" id="vAttachments">
                    <!-- Files injected here -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById('paymentModal');
    const viewModal = document.getElementById('viewModal');
    const pForm = document.getElementById('paymentForm');
    const fileInput = document.getElementById('fileInput');
    const fileList = document.getElementById('fileList');
    const progressBar = document.getElementById('uploadProgressBar');
    const progressContainer = document.getElementById('uploadProgressContainer');

    function openPaymentModal() {
        pForm.reset();
        document.getElementById('formAction').value = 'add_payment';
        document.getElementById('paymentId').value = '';
        document.getElementById('modalTitle').innerText = 'Record Expenditure';
        document.getElementById('modalIcon').className = 'ph ph-file-plus text-primary';
        fileList.innerHTML = '';
        modal.classList.remove('hidden');
    }

    function closePaymentModal() {
        modal.classList.add('hidden');
    }

    function editPayment(data) {
        openPaymentModal();
        document.getElementById('formAction').value = 'edit_payment';
        document.getElementById('paymentId').value = data.id;
        document.getElementById('modalTitle').innerText = 'Edit Expenditure';
        document.getElementById('modalIcon').className = 'ph ph-pencil-simple text-primary';
        
        document.getElementById('pTitle').value = data.title;
        document.getElementById('pAmount').value = data.amount;
        document.getElementById('pCategory').value = data.category;
        document.getElementById('pDate').value = data.payment_date;
        document.getElementById('pOpp').value = data.opportunity_id || '';
        document.getElementById('pNotes').value = data.notes || '';
    }

    function viewPayment(data) {
        document.getElementById('vTitle').innerText = data.title;
        document.getElementById('vCategory').innerText = data.category;
        document.getElementById('vAmount').innerText = '$' + parseFloat(data.amount).toLocaleString(undefined, {minimumFractionDigits: 2});
        document.getElementById('vDate').innerText = new Date(data.payment_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
        document.getElementById('vProject').innerText = data.project_name || 'General Expenditure';
        document.getElementById('vAdmin').innerText = data.admin_name || 'System';
        document.getElementById('vNotes').innerText = data.notes || 'No notes available.';
        
        const attContainer = document.getElementById('vAttachments');
        attContainer.innerHTML = '';
        if (data.attachments && data.attachments.length > 0) {
            data.attachments.forEach(att => {
                attContainer.innerHTML += `
                    <a href="<?= BASE_URL ?>${att.file_path}" target="_blank" class="flex items-center gap-3 p-3 bg-white/5 border border-white/10 rounded-xl hover:bg-white/10 transition-all group">
                        <i class="ph ph-file-pdf text-red-400 text-xl"></i>
                        <span class="text-xs text-slate-300 font-medium truncate">${att.file_name}</span>
                        <i class="ph ph-download-simple ml-auto text-slate-500 group-hover:text-white"></i>
                    </a>
                `;
            });
        } else {
            attContainer.innerHTML = '<span class="text-xs text-slate-600 italic">No attachments found.</span>';
        }
        
        viewModal.classList.remove('hidden');
    }

    function closeViewModal() {
        viewModal.classList.add('hidden');
    }

    // File selection listing
    fileInput.addEventListener('change', () => {
        fileList.innerHTML = '';
        Array.from(fileInput.files).forEach((file, index) => {
            const size = (file.size / 1024).toFixed(1) + ' KB';
            fileList.innerHTML += `
                <div class="file-list-item flex items-center justify-between p-2 bg-black/20 rounded-lg border border-white/5 text-[10px]">
                    <div class="flex items-center gap-2">
                        <i class="ph ph-file text-primary"></i>
                        <span class="text-slate-300 truncate max-w-[200px]">${file.name}</span>
                        <span class="text-slate-600">${size}</span>
                    </div>
                    <i class="ph ph-check-circle text-emerald-500"></i>
                </div>
            `;
        });
    });

    // Handle Form Submission with Progress Bar simulation
    pForm.onsubmit = function(e) {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="ph ph-circle-notch animate-spin mr-2"></i> Processing...';
        
        progressContainer.classList.add('active');
        let progress = 0;
        const interval = setInterval(() => {
            progress += Math.random() * 30;
            if (progress > 95) progress = 95;
            progressBar.style.width = progress + '%';
        }, 200);

        // Actual form submission will happen normally, the interval is just to show progress 
        // until the page reloads from the POST redirect.
    };

    // Close modals on escape key
    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closePaymentModal();
            closeViewModal();
        }
    });

    // Close on click outside
    window.onclick = function(event) {
        if (event.target == modal) closePaymentModal();
        if (event.target == viewModal) closeViewModal();
    }
</script>

<style>
    /* Desktop-first: ensure table looks good on large screens */
    @media screen and (min-width: 1025px) {
        .admin-table { min-width: 1000px; }
    }

    /* Mobile-responsive card transformation */
    @media (max-width: 1024px) {
        .admin-table-wrapper { border-radius: 1.5rem !important; margin: -1rem !important; }
        .admin-table thead { display: none !important; }
        
        .admin-table, 
        .admin-table tbody, 
        .admin-table tr, 
        .admin-table td { 
            display: block !important; 
            width: 100% !important; 
            min-width: 0 !important;
        }
        
        .admin-table tr { 
            margin-bottom: 20px !important; 
            background: rgba(255,255,255,0.02) !important; 
            border-radius: 1.5rem !important; 
            padding: 20px !important;
            border: 1px solid rgba(255,255,255,0.05) !important;
        }
        
        .admin-table td { 
            display: flex !important; 
            justify-content: space-between !important; 
            align-items: center !important; 
            padding: 12px 0 !important; 
            border-bottom: 1px solid rgba(255,255,255,0.03) !important;
            text-align: right !important;
            min-height: 44px !important;
        }
        
        .admin-table td:last-child { border-bottom: none !important; }
        
        .admin-table td::before { 
            content: attr(data-label) !important; 
            font-weight: 900 !important; 
            text-transform: uppercase !important; 
            font-size: 0.65rem !important; 
            color: #06b6d4 !important;
            letter-spacing: 2px !important;
            opacity: 0.6 !important;
            text-align: left !important;
            margin-right: 12px !important;
        }
    }
</style>

<?php require __DIR__ . '/partials/_delete_modal.php'; ?>
</body>
</html>
