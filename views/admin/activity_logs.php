<?php
$pageTitle = 'Activity Logs';
$currentPage = '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= e($pageTitle) ?> — <?= APP_NAME ?></title>
    <?php require __DIR__ . '/partials/_head_assets.php'; ?>
</head>
<body class="bg-[#0b0e14]">
<div class="admin-layout flex w-full h-screen overflow-hidden">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>
    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-20 flex items-center justify-between px-8 bg-glass-bg border-b border-white/5 shrink-0 backdrop-blur-xl sticky top-0 z-[100]">
            <div class="flex flex-col">
                <div class="text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] mb-1 hidden sm:block">System Audit</div>
                <h1 class="text-xl font-black text-white tracking-tight flex items-center gap-3 group">
                    <span class="text-neon-purple drop-shadow-[0_0_8px_rgba(168,85,247,0.4)]">Audit Trail</span>
                    <span class="opacity-20 translate-y-px hidden sm:inline">/</span>
                    <span class="text-sm tracking-widest text-slate-400 uppercase font-black hidden sm:inline">Security Protocol</span>
                </h1>
            </div>
            <div class="flex items-center gap-6">
                <form method="GET" action="<?= baseUrl('admin/activity_logs') ?>" class="relative group hidden sm:block">
                    <i class="ph-bold ph-magnifying-glass absolute left-5 top-1/2 -translate-y-1/2 text-slate-600 group-focus-within:text-neon-purple transition-colors"></i>
                    <input type="text" name="search" value="<?= e($_GET['search'] ?? '') ?>" placeholder="Filter audit trail..." class="bg-black/40 border border-white/10 rounded-2xl pl-12 pr-6 py-3 text-[10px] font-black uppercase tracking-widest text-white focus:border-neon-purple outline-none transition-all w-64 lg:w-80 placeholder:text-slate-700 shadow-inner">
                </form>
                <?php require __DIR__ . '/partials/_topbar.php'; ?>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-6 crm-main-scroll bg-[#0b0e14]">
            <div class="max-w-7xl mx-auto space-y-6">
                <!-- Activity History Table -->
                <div class="admin-table-wrapper backdrop-blur-2xl border border-white/5 rounded-[2rem] overflow-hidden shadow-premium">
                    <div class="overflow-x-auto crm-main-scroll">
                        <table class="admin-table w-full text-left border-collapse">
                            <thead>
                                <tr class="text-slate-600 text-[8px] font-black uppercase tracking-[0.4em] bg-white/[0.01]">
                                    <th class="py-6 px-10">Temporal Vector</th>
                                    <th class="py-6 px-6 text-center">Protocol Subject</th>
                                    <th class="py-6 px-6 text-center">Operation Hash</th>
                                    <th class="py-6 px-6 w-1/3">Administrative Context</th>
                                    <th class="py-6 px-10 text-right">Access Point</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/[0.02]">
                                <?php if(empty($logs)): ?>
                                    <tr>
                                        <td colspan="5" class="py-32 text-center">
                                            <div class="text-slate-700 text-[10px] font-black uppercase tracking-widest animate-pulse italic">No active audit markers detected in database.</div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($logs as $log): ?>
                                        <tr class="hover:bg-white/[0.03] transition-all group/row border-b border-white/[0.03] last:border-0 relative">
                                            <td class="py-6 px-10" data-label="Vector">
                                                <div class="flex flex-col">
                                                    <span class="text-white font-black text-[11px] uppercase tracking-wider group-hover/row:text-neon-purple transition-colors"><?= date('M d, Y', strtotime($log['created_at'])) ?></span>
                                                    <span class="text-[9px] text-slate-600 font-bold uppercase tracking-widest mt-0.5 font-mono"><?= date('H:i:s', strtotime($log['created_at'])) ?> <span class="text-[7px] opacity-40">UTC</span></span>
                                                </div>
                                            </td>
                                            <td class="py-6 px-6 text-center" data-label="Subject">
                                                <div class="flex items-center justify-center gap-3">
                                                    <div class="w-9 h-9 rounded-xl bg-black/40 border border-white/5 flex items-center justify-center text-lg group-hover/row:border-neon-purple/40 group-hover/row:bg-neon-purple/5 transition-all duration-300 shadow-inner">
                                                        <i class="ph-bold ph-identification-badge text-neon-purple group-hover/row:scale-110 transition-transform"></i>
                                                    </div>
                                                    <span class="text-white font-black text-[10px] tracking-widest uppercase"><?= e($log['username'] ?? 'SYSTEM_CORE') ?></span>
                                                </div>
                                            </td>
                                            <td class="py-6 px-6 text-center" data-label="Hash">
                                                <?php
                                                $actionClass = [
                                                    'login' => 'text-neon-emerald bg-neon-emerald/10 border-neon-emerald/20',
                                                    'delete_user' => 'text-neon-rose bg-neon-rose/10 border-neon-rose/20',
                                                    'default' => 'text-neon-purple bg-neon-purple/10 border-neon-purple/20'
                                                ];
                                                $cls = $actionClass[$log['action_type']] ?? $actionClass['default'];
                                                ?>
                                                <span class="inline-flex px-3 py-1 rounded-lg border <?= $cls ?> text-[8px] font-black uppercase tracking-[0.2em] shadow-lg">
                                                    <?= e(str_replace('_', ' ', $log['action_type'])) ?>
                                                </span>
                                            </td>
                                            <td class="py-6 px-6" data-label="Context">
                                                <div class="text-[10px] text-slate-500 font-bold leading-relaxed max-w-xl line-clamp-2 italic" title="<?= e($log['details']) ?>">
                                                    "<?= e($log['details']) ?>"
                                                </div>
                                            </td>
                                            <td class="py-6 px-10 text-right relative" data-label="Access">
                                                <div class="flex items-center justify-end gap-3">
                                                    <span class="text-[10px] text-slate-600 font-black tracking-[0.2em] font-mono group-hover/row:text-neon-purple transition-colors"><?= e($log['ip_address']) ?></span>
                                                    <div class="w-1.5 h-1.5 rounded-full bg-neon-purple/30 group-hover/row:bg-neon-purple group-hover/row:shadow-[0_0_8px_rgba(168,85,247,1)] transition-all"></div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
<style>
    /* Desktop-first: ensure table looks good on large screens */
    @media screen and (min-width: 1025px) {
        .admin-table { min-width: 1200px; }
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
            color: #a855f7 !important;
            letter-spacing: 2px !important;
            opacity: 0.6 !important;
            text-align: left !important;
            margin-right: 12px !important;
        }
    }
</style>
</body>
</html>
