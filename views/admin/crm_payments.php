<?php
$pageTitle = 'CRM Payments';
$currentPage = 'crm_payments';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle . ' - ' . APP_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= htmlspecialchars(BASE_URL) ?>/assets/css/style.css">
    <style>
        .progress-bar-container { transition: all 0.3s ease; opacity: 0; pointer-events: none; }
        .progress-bar-container.active { opacity: 1; pointer-events: auto; }
        .file-list-item { animation: slideIn 0.2s ease-out; }
        @keyframes slideIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="bg-[#0b0e14]">
<div class="admin-layout flex w-full">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>
    <div class="crm-main leading-relaxed text-slate-300">
        <header class="h-16 flex items-center justify-between px-6 bg-[#1a2333] border-b border-white/5 shrink-0">
            <h1 class="text-xl font-semibold text-white tracking-tight flex items-center gap-2">
                <i class="ph ph-hand-coins text-primary"></i>
                Spendings & Expenditures
            </h1>
            <button onclick="openPaymentModal()" class="btn-primary">
                <i class="ph ph-plus mr-2"></i> Record Expense
            </button>
        </header>

        <main class="flex-1 overflow-y-auto p-6 bg-[#0b0e14] w-full h-full crm-main-scroll">
            <?php if ($flash = getFlash()): ?>
                <div class="mb-6 p-4 rounded-xl border border-emerald-500/20 bg-emerald-500/10 text-emerald-400 text-sm flex items-center gap-2">
                    <i class="ph ph-check-circle text-lg"></i>
                    <?= htmlspecialchars($flash) ?>
                </div>
            <?php endif; ?>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <div class="bg-[#1a2333]/40 backdrop-blur-md border border-white/5 rounded-2xl p-6 shadow-2xl relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 w-20 h-20 bg-primary/5 rounded-full blur-2xl group-hover:bg-primary/10 transition-all"></div>
                    <div class="flex justify-between items-start relative z-10">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Total Spendings</p>
                            <h3 class="text-3xl font-extrabold text-white tracking-tight">$<?= number_format($totalSpend, 2) ?></h3>
                            <div class="mt-2 text-[10px] text-slate-500 bg-white/5 px-2 py-0.5 rounded-full w-fit">Lifetime Total</div>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center shadow-inner group-hover:scale-110 transition-transform">
                            <i class="ph ph-chart-pie-slice text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-[#1a2333]/40 backdrop-blur-md border border-white/5 rounded-2xl p-6 shadow-2xl relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 w-20 h-20 bg-blue-500/5 rounded-full blur-2xl group-hover:bg-blue-500/10 transition-all"></div>
                    <div class="flex justify-between items-start relative z-10">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Current Month</p>
                            <h3 class="text-3xl font-extrabold text-white tracking-tight">$<?= number_format($monthlySpend, 2) ?></h3>
                            <div class="mt-2 text-[10px] text-blue-400 bg-blue-400/10 px-2 py-0.5 rounded-full w-fit">MTD Expenditure</div>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-blue-500/10 text-blue-400 flex items-center justify-center shadow-inner group-hover:scale-110 transition-transform">
                            <i class="ph ph-calendar-check text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-[#1a2333]/40 backdrop-blur-md border border-white/5 rounded-2xl p-6 shadow-2xl relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 w-20 h-20 bg-emerald-500/5 rounded-full blur-2xl group-hover:bg-emerald-500/10 transition-all"></div>
                    <div class="flex justify-between items-start relative z-10">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Recent Record</p>
                            <h3 class="text-lg font-bold text-white tracking-tight truncate max-w-[180px]">
                                <?= !empty($payments) ? htmlspecialchars($payments[0]['title']) : 'No records' ?>
                            </h3>
                            <div class="mt-2 text-[10px] text-emerald-400 bg-emerald-400/10 px-2 py-0.5 rounded-full w-fit">Latest Transaction</div>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center shadow-inner group-hover:scale-110 transition-transform">
                            <i class="ph ph-receipt text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters & Table -->
            <div class="bg-[#1a2333]/40 backdrop-blur-lg border border-white/5 rounded-3xl shadow-2xl overflow-hidden flex flex-col">
                <div class="p-6 border-b border-white/5 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <form class="flex items-center gap-3 w-full sm:w-auto" method="GET" action="<?= BASE_URL ?>/admin/crm_payments">
                        <div class="relative w-full sm:w-64">
                            <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-500"></i>
                            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search records..." class="w-full bg-black/40 border border-white/10 rounded-xl py-2 pl-10 pr-4 text-sm focus:border-primary outline-none transition-all">
                        </div>
                        <select name="category" onchange="this.form.submit()" class="bg-black/40 border border-white/10 rounded-xl py-2 px-4 text-sm focus:border-primary outline-none transition-all">
                            <option value="">All Categories</option>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?= $cat ?>" <?= $categoryFilter === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                    <div class="text-xs font-bold uppercase tracking-widest text-slate-500">
                        Showing <?= count($payments) ?> Records
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[950px]">
                        <thead>
                            <tr class="bg-black/40 text-slate-500 text-[10px] font-bold uppercase tracking-[0.2em]">
                                <th class="py-5 px-8">Expenditure Details</th>
                                <th class="py-5 px-4">Category</th>
                                <th class="py-5 px-4">Project / Link</th>
                                <th class="py-5 px-4 text-right">Amount</th>
                                <th class="py-5 px-8 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/[0.03] text-sm">
                            <?php if(empty($payments)): ?>
                                <tr>
                                    <td colspan="5" class="py-20 text-center text-slate-500 italic">No expenditures recorded yet.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($payments as $p): ?>
                                <tr class="hover:bg-white/[0.01] transition-colors group">
                                    <td class="py-5 px-8">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-xl bg-black/40 border border-white/5 flex items-center justify-center text-lg shadow-inner">
                                                <i class="ph-duotone ph-currency-circle-dollar text-primary"></i>
                                            </div>
                                            <div>
                                                <span class="text-white font-bold block mb-0.5"><?= htmlspecialchars($p['title']) ?></span>
                                                <div class="flex flex-wrap gap-2 mt-1">
                                                    <span class="text-[10px] text-slate-500 uppercase tracking-widest font-bold font-mono"><?= date('M d, Y', strtotime($p['payment_date'])) ?></span>
                                                    <?php if(!empty($p['attachments'])): ?>
                                                        <div class="flex gap-1.5 hover:scale-105 transition-transform">
                                                            <i class="ph ph-paperclip text-primary text-xs"></i>
                                                            <span class="text-[9px] text-primary font-black uppercase"><?= count($p['attachments']) ?> Files</span>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-5 px-4">
                                        <span class="px-3 py-1 rounded-lg bg-white/5 border border-white/5 text-[11px] text-slate-400 font-bold uppercase tracking-tight">
                                            <?= htmlspecialchars($p['category']) ?>
                                        </span>
                                    </td>
                                    <td class="py-5 px-4">
                                        <?php if($p['project_name']): ?>
                                            <a href="<?= BASE_URL ?>/admin/crm_opportunity?id=<?= $p['opportunity_id'] ?>" class="text-primary hover:text-emerald-400 flex items-center gap-2 transition-colors">
                                                <i class="ph ph-briefcase"></i>
                                                <span class="truncate max-w-[150px] font-medium"><?= htmlspecialchars($p['project_name']) ?></span>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-slate-600 italic">General Expenditure</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-5 px-4 text-right">
                                        <span class="text-white font-extrabold text-base">$<?= number_format($p['amount'], 2) ?></span>
                                    </td>
                                    <td class="py-5 px-8 text-right">
                                        <div class="flex justify-end gap-2">
                                            <button onclick='viewPayment(<?= json_encode($p) ?>)' class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-400 hover:bg-blue-500 hover:text-white transition-all flex items-center justify-center" title="View">
                                                <i class="ph ph-eye"></i>
                                            </button>
                                            <button onclick='editPayment(<?= json_encode($p) ?>)' class="w-8 h-8 rounded-lg bg-primary/10 text-primary hover:bg-primary hover:text-white transition-all flex items-center justify-center" title="Edit">
                                                <i class="ph ph-pencil-simple"></i>
                                            </button>
                                            <form action="<?= BASE_URL ?>/admin/crm_payments" method="POST" onsubmit="return confirm('Delete this record?')" class="inline-block">
                                                <input type="hidden" name="action" value="delete_payment">
                                                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                                <button type="submit" class="w-8 h-8 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center">
                                                    <i class="ph ph-trash"></i>
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

</body>
</html>
