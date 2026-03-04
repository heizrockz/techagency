<?php
$title = 'Invoices & Quotes';
$currentPage = 'invoices';
?>
<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <title><?= e($title) ?> — <?= APP_NAME ?></title>
    <?php require __DIR__ . '/partials/_head_assets.php'; ?>
</head>
<body class="bg-[#0b0e14]" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">

<div class="admin-layout flex w-full h-screen overflow-hidden">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>
    <div class="flex-1 flex flex-col min-w-0 bg-[#0b0e14]">
        <header class="h-20 flex items-center justify-between px-8 bg-glass-bg border-b border-white/5 shrink-0 backdrop-blur-xl sticky top-0 z-[100]">
            <div class="flex flex-col">
                <div class="text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] mb-1 hidden sm:block">Financial Ledger</div>
                <h1 class="text-xl font-black text-white tracking-tight flex items-center gap-3 group">
                    <span class="text-neon-cyan drop-shadow-[0_0_8px_rgba(6,182,212,0.4)]">Neural Ledger</span>
                    <span class="opacity-20 translate-y-px hidden sm:inline">/</span>
                    <span class="text-sm tracking-widest text-slate-400 uppercase font-black hidden sm:inline">Invoices & Quotes</span>
                </h1>
            </div>
            <div class="flex items-center gap-6">
                <a href="<?= baseUrl('admin/invoices?action=new') ?>" class="px-4 sm:px-6 py-2.5 bg-neon-cyan hover:bg-cyan-400 text-black text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-lg active:scale-95 flex items-center gap-2">
                    <i class="ph-bold ph-plus text-lg"></i> <span class="hidden sm:inline">Provision New Protocol</span>
                </a>
                <?php require __DIR__ . '/partials/_topbar.php'; ?>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-6 bg-[#0b0e14] w-full h-full crm-main-scroll">
            <div class="admin-table-wrapper backdrop-blur-2xl border border-white/5 rounded-[2.5rem] overflow-hidden shadow-premium">
                <div class="p-10 border-b border-white/5 flex flex-col lg:flex-row items-center justify-between gap-8 bg-white/[0.01]">
                    <div class="relative w-full lg:w-[480px] group">
                        <i class="ph-bold ph-magnifying-glass absolute left-6 top-1/2 -translate-y-1/2 text-slate-600 group-focus-within:text-neon-cyan transition-colors"></i>
                        <input type="text" id="invoiceSearch" placeholder="Filter ledger records..." class="w-full bg-black/40 border border-white/10 rounded-2xl py-4.5 pl-14 pr-6 text-[10px] font-black text-white focus:outline-none focus:border-neon-cyan transition-all placeholder-slate-800 uppercase tracking-[0.3em] shadow-inner">
                    </div>
                    <?php if (!empty($invoices)): ?>
                    <div class="px-8 py-3 bg-white/5 rounded-[1.25rem] border border-white/5 flex items-center gap-4 group hover:border-neon-cyan/30 transition-all">
                        <div class="w-2 h-2 rounded-full bg-neon-cyan animate-pulse shadow-[0_0_8px_rgba(6,182,212,1)]"></div>
                        <span class="text-[10px] text-slate-500 font-black uppercase tracking-[0.2em]">
                            Active Protocols: <span class="text-white ml-2"><?= count($invoices) ?> Clusters</span>
                        </span>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="overflow-x-auto crm-main-scroll">
                    <?php if (empty($invoices)): ?>
                        <div class="py-24 text-center text-slate-500 font-bold uppercase tracking-[0.3em] italic opacity-30">No transaction records found.</div>
                    <?php else: ?>
                        <table class="admin-table w-full text-left border-collapse min-w-[1100px]" id="invoicesTable">
                            <thead>
                                <tr class="text-slate-600 text-[8px] font-black uppercase tracking-[0.4em] bg-white/[0.01]">
                                    <th class="py-6 px-10">Sequence ID</th>
                                    <th class="py-6 px-6">Entity Architecture</th>
                                    <th class="py-6 px-6">Target Identity</th>
                                    <th class="py-6 px-6 text-center">Status Cluster</th>
                                    <th class="py-6 px-6 text-center">Temporal Marker</th>
                                    <th class="py-6 px-10 text-right">Directives</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/[0.03] text-sm">
                                <?php foreach ($invoices as $inv): ?>
                                <tr class="hover:bg-white/[0.03] transition-all group/row border-b border-white/[0.03] last:border-0 relative">
                                    <td class="py-6 px-10 relative" data-label="Sequence">
                                        <div class="flex items-center gap-4 relative z-10 transition-transform group-hover/row:translate-x-1">
                                            <div class="w-11 h-11 rounded-xl bg-black/40 border border-white/10 flex items-center justify-center shadow-inner group-hover/row:border-neon-cyan/40 group-hover/row:bg-neon-cyan/5 transition-all duration-500">
                                                <i class="ph-bold ph-barcode text-neon-cyan group-hover/row:scale-110 transition-transform"></i>
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-white font-black text-sm tracking-tight group-hover/row:text-neon-cyan transition-colors uppercase"><?= e($inv['invoice_number']) ?></span>
                                                <span class="text-[7px] text-slate-700 font-black tracking-[0.3em] uppercase">System Trace ID</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-6 px-6" data-label="Architecture">
                                        <div class="inline-flex px-3 py-1 rounded-lg border <?= $inv['type'] === 'quote' ? 'text-neon-blue bg-neon-blue/10 border-neon-blue/20 shadow-[0_0_10px_rgba(59,130,246,0.1)]' : 'text-neon-cyan bg-neon-cyan/10 border-neon-cyan/20 shadow-[0_0_10px_rgba(6,182,212,0.1)]' ?> text-[8px] font-black uppercase tracking-[0.2em]">
                                            <?= $inv['type'] === 'quote' ? 'RE_QUOTE' : 'FX_INVOICE' ?>
                                        </div>
                                    </td>
                                    <td class="py-6 px-6" data-label="Identity">
                                        <div class="flex items-center gap-4">
                                            <div class="w-8 h-8 rounded-xl bg-white/5 flex items-center justify-center border border-white/5 shadow-inner group-hover/row:border-neon-cyan/30 transition-all">
                                                <i class="ph-bold ph-identification-badge text-slate-600 text-xs group-hover/row:text-neon-cyan transition-colors"></i>
                                            </div>
                                            <span class="text-white font-black text-[11px] tracking-widest uppercase group-hover/row:text-neon-cyan transition-colors"><?= e($inv['client_name']) ?></span>
                                        </div>
                                    </td>
                                    <td class="py-6 px-6 text-center" data-label="Status">
                                        <?php
                                        $statusMap = [
                                            'draft' => ['class' => 'text-slate-500 bg-white/5 border-white/10', 'label' => 'DRAFT_MODE'],
                                            'sent' => ['class' => 'text-neon-purple bg-neon-purple/10 border-neon-purple/20', 'label' => 'DISPATCHED'],
                                            'paid' => ['class' => 'text-neon-emerald bg-neon-emerald/10 border-neon-emerald/20 shadow-[0_0_10px_rgba(16,185,129,0.1)]', 'label' => 'SETTLED'],
                                            'cancelled' => ['class' => 'text-neon-rose bg-neon-rose/10 border-neon-rose/20', 'label' => 'TERMINATED']
                                        ];
                                        $meta = $statusMap[$inv['status']] ?? ['class' => 'text-slate-600 bg-white/5 border-white/5', 'label' => 'UNTRACKED'];
                                        ?>
                                        <span class="inline-flex px-3 py-1 rounded-lg border <?= $meta['class'] ?> text-[8px] font-black uppercase tracking-[0.2em]">
                                            <?= $meta['label'] ?>
                                        </span>
                                    </td>
                                    <td class="py-6 px-6 text-center" data-label="Marker">
                                        <div class="flex flex-col items-center">
                                            <span class="text-white text-[11px] font-black tracking-widest font-mono"><?= date('d.m.y', strtotime($inv['created_at'])) ?></span>
                                            <span class="text-[7px] text-slate-700 font-black tracking-[0.3em] uppercase mt-1">Record Sync</span>
                                        </div>
                                    </td>
                                    <td class="py-6 px-10 text-right" data-label="Directives">
                                        <div class="flex justify-end gap-3 opacity-0 group-hover/row:opacity-100 transition-all translate-x-4 group-hover/row:translate-x-0">
                                            <a href="<?= baseUrl('admin/invoices?action=edit&id=' . $inv['id']) ?>" class="w-10 h-10 rounded-xl bg-white/5 text-slate-500 hover:text-neon-cyan hover:bg-neon-cyan/5 border border-white/10 hover:border-neon-cyan/20 transition-all flex items-center justify-center shadow-lg group/btn" title="Refine Protocol">
                                                <i class="ph-bold ph-pencil-simple text-lg group-hover/btn:scale-110 transition-transform"></i>
                                            </a>
                                            <a href="<?= baseUrl('admin/invoices?action=print&id=' . $inv['id']) ?>" target="_blank" class="w-10 h-10 rounded-xl bg-white/5 text-slate-500 hover:text-neon-amber hover:bg-neon-amber/5 border border-white/10 hover:border-neon-amber/20 transition-all flex items-center justify-center shadow-lg group/btn" title="Execute Transmission">
                                                <i class="ph-bold ph-printer text-lg group-hover/btn:scale-110 transition-transform"></i>
                                            </a>
                                            <button type="button" onclick="showDeleteModal('<?= e($inv['invoice_number']) ?>', '<?= baseUrl('admin/invoices?action=delete&id=' . $inv['id']) ?>')" class="w-10 h-10 rounded-xl bg-white/5 text-slate-600 hover:text-neon-rose hover:bg-neon-rose/5 border border-white/10 hover:border-neon-rose/20 transition-all flex items-center justify-center shadow-lg group/btn" title="Purge Record">
                                                <i class="ph-bold ph-trash text-lg group-hover/btn:rotate-6 transition-transform"></i>
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


<?php require __DIR__ . '/partials/_delete_modal.php'; ?>
<script>
document.getElementById('invoiceSearch').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('#invoicesTable tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});
</script>

<style>
    @media screen and (max-width: 1024px) {
        .admin-table thead { display: none; }
        .admin-table, .admin-table tbody, .admin-table tr, .admin-table td { 
            display: block; 
            width: 100%; 
            min-width: auto !important;
        }
        .admin-table tr { 
            margin-bottom: 20px; 
            background: rgba(255,255,255,0.01); 
            border-radius: 1.5rem; 
            padding: 20px;
            border: 1px solid rgba(255,255,255,0.05);
        }
        .admin-table td { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            padding: 12px 0; 
            border-bottom: 1px solid rgba(255,255,255,0.03);
            text-align: right;
        }
        .admin-table td:last-child { border-bottom: none; }
        .admin-table td::before { 
            content: attr(data-label); 
            font-weight: 900; 
            text-transform: uppercase; 
            font-size: 0.6rem; 
            color: #06b6d4;
            letter-spacing: 1px;
            opacity: 0.5;
        }
    }
</style>
<style>
    /* Desktop-first: ensure table looks good on large screens */
    @media screen and (min-width: 1025px) {
        .admin-table { min-width: 1100px; }
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
        
        .admin-card { padding: 1.5rem !important; }
    }
</style>
</body>
</html>
