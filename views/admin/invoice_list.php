<?php
$title = 'Invoices & Quotes';
$currentPage = 'invoices';
?>
<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?> — <?= APP_NAME ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= baseUrl('assets/css/style.css') ?>">
</head>
<body class="bg-[#0b0e14]" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">

<div class="admin-layout flex w-full">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>
    
    <div class="crm-main leading-relaxed text-slate-300">
        <header class="h-16 flex items-center justify-between px-6 bg-[#1a2333] border-b border-white/5 shrink-0">
            <h1 class="text-xl font-semibold text-white tracking-tight flex items-center gap-2">
                <i class="ph ph-receipt text-primary"></i>
                Invoices & Quotes
            </h1>
            <a href="<?= baseUrl('admin/invoices?action=new') ?>" class="btn-primary flex items-center gap-2">
                <i class="ph ph-plus"></i> Create New
            </a>
        </header>

        <main class="flex-1 overflow-y-auto p-6 bg-[#0b0e14] w-full h-full crm-main-scroll">
            <div class="bg-[#1a2333]/40 backdrop-blur-lg border border-white/5 rounded-3xl shadow-2xl overflow-hidden flex flex-col">
                <div class="p-6 border-b border-white/5 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="relative w-full sm:w-80">
                        <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-500"></i>
                        <input type="text" id="invoiceSearch" placeholder="Search records..." class="w-full bg-black/40 border border-white/10 rounded-xl py-2 pl-10 pr-4 text-sm text-white focus:border-primary outline-none transition-all">
                    </div>
                    <?php if (!empty($invoices)): ?>
                    <div class="text-[10px] font-bold uppercase tracking-widest text-slate-500">
                        Showing <?= count($invoices) ?> Documents
                    </div>
                    <?php endif; ?>
                </div>

                <div class="overflow-x-auto">
                    <?php if (empty($invoices)): ?>
                        <div class="py-20 text-center text-slate-500 italic">No invoices or quotes found. Create your first one!</div>
                    <?php else: ?>
                        <table class="w-full text-left border-collapse min-w-[900px]" id="invoicesTable">
                            <thead>
                                <tr class="bg-black/40 text-slate-500 text-[10px] font-bold uppercase tracking-[0.2em]">
                                    <th class="py-5 px-8">Number</th>
                                    <th class="py-5 px-4">Type</th>
                                    <th class="py-5 px-4">Client</th>
                                    <th class="py-5 px-4 text-center">Status</th>
                                    <th class="py-5 px-4 text-center">Date</th>
                                    <th class="py-5 px-8 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/[0.03] text-sm">
                                <?php foreach ($invoices as $inv): ?>
                                <tr class="hover:bg-white/[0.01] transition-colors group">
                                    <td class="py-5 px-8">
                                        <span class="text-white font-bold block"><?= e($inv['invoice_number']) ?></span>
                                    </td>
                                    <td class="py-5 px-4">
                                        <span class="px-3 py-1 rounded-lg <?= $inv['type'] === 'quote' ? 'bg-blue-500/10 text-blue-400 border-blue-500/20' : 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' ?> border text-[11px] font-bold uppercase tracking-tight">
                                            <?= $inv['type'] === 'quote' ? 'Quote' : 'Invoice' ?>
                                        </span>
                                    </td>
                                    <td class="py-5 px-4">
                                        <span class="text-white font-medium"><?= e($inv['client_name']) ?></span>
                                    </td>
                                    <td class="py-5 px-4 text-center">
                                        <?php
                                        $statusStyles = [
                                            'draft' => 'text-slate-500',
                                            'sent' => 'text-blue-400',
                                            'paid' => 'text-emerald-400',
                                            'cancelled' => 'text-red-400'
                                        ];
                                        $styleClass = $statusStyles[$inv['status']] ?? 'text-slate-500';
                                        ?>
                                        <span class="<?= $styleClass ?> text-[10px] font-extrabold uppercase tracking-widest bg-white/5 py-1 px-3 rounded-full">
                                            <?= e($inv['status']) ?>
                                        </span>
                                    </td>
                                    <td class="py-5 px-4 text-center">
                                        <span class="text-slate-500 text-xs"><?= date('M j, Y', strtotime($inv['created_at'])) ?></span>
                                    </td>
                                    <td class="py-5 px-8 text-right">
                                        <div class="flex justify-end gap-2">
                                            <a href="<?= baseUrl('admin/invoices?action=edit&id=' . $inv['id']) ?>" class="w-8 h-8 rounded-lg bg-primary/10 text-primary hover:bg-primary hover:text-white transition-all flex items-center justify-center" title="Edit">
                                                <i class="ph ph-pencil-simple"></i>
                                            </a>
                                            <a href="<?= baseUrl('admin/invoices?action=print&id=' . $inv['id']) ?>" target="_blank" class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-500 hover:bg-amber-500 hover:text-white transition-all flex items-center justify-center" title="Print/View">
                                                <i class="ph ph-printer"></i>
                                            </a>
                                            <button type="button" onclick="showDeleteModal('<?= e($inv['invoice_number']) ?>', '<?= baseUrl('admin/invoices?action=delete&id=' . $inv['id']) ?>')" class="w-8 h-8 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center" title="Delete">
                                                <i class="ph ph-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.7); backdrop-filter:blur(8px); align-items:center; justify-content:center;">
    <div style="background:#1a2333; border:1px solid rgba(255,255,255,0.1); border-radius:1.5rem; padding:2rem; max-width:420px; width:90%; box-shadow:0 25px 50px rgba(0,0,0,0.5); animation:modalIn 0.2s ease-out;">
        <div style="text-align:center; margin-bottom:1.5rem;">
            <div style="width:64px; height:64px; border-radius:50%; background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.2); display:flex; align-items:center; justify-content:center; margin:0 auto 1rem;">
                <i class="ph ph-warning" style="font-size:2rem; color:#f87171;"></i>
            </div>
            <h3 style="color:#fff; font-size:1.25rem; font-weight:700; margin-bottom:0.5rem;">Confirm Deletion</h3>
            <p style="color:#94a3b8; font-size:0.875rem; line-height:1.6;">Are you sure you want to delete <strong id="deleteItemName" style="color:#f87171;"></strong>? This action cannot be undone.</p>
        </div>
        <div style="display:flex; gap:0.75rem; justify-content:center;">
            <button onclick="closeDeleteModal()" style="padding:0.625rem 1.5rem; background:rgba(255,255,255,0.05); color:#94a3b8; font-weight:700; font-size:0.7rem; text-transform:uppercase; letter-spacing:0.1em; border-radius:0.75rem; border:1px solid rgba(255,255,255,0.1); cursor:pointer; transition:all 0.2s;">Cancel</button>
            <a id="deleteConfirmBtn" href="#" style="padding:0.625rem 1.5rem; background:#ef4444; color:#fff; font-weight:700; font-size:0.7rem; text-transform:uppercase; letter-spacing:0.1em; border-radius:0.75rem; border:none; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:0.5rem; transition:all 0.2s;">
                <i class="ph ph-trash"></i> Delete
            </a>
        </div>
    </div>
</div>
<style>
@keyframes modalIn { from { opacity:0; transform:scale(0.95) translateY(10px); } to { opacity:1; transform:scale(1) translateY(0); } }
#deleteModal button:hover { background:rgba(255,255,255,0.1) !important; color:#fff !important; }
#deleteConfirmBtn:hover { background:#dc2626 !important; box-shadow:0 0 20px rgba(239,68,68,0.3); }
</style>
                
<script>
document.getElementById('invoiceSearch').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('#invoicesTable tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});

function showDeleteModal(name, url) {
    document.getElementById('deleteItemName').textContent = name;
    document.getElementById('deleteConfirmBtn').href = url;
    document.getElementById('deleteModal').style.display = 'flex';
}
function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
}
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});
</script>
</body>
</html>
