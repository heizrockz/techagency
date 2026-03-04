<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <title><?= t('admin_bookings') ?> — <?= APP_NAME ?></title>
    <?php require __DIR__ . '/partials/_head_assets.php'; ?>
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">

<div class="admin-layout flex w-full h-screen overflow-hidden">
    <?php $currentPage = 'bookings'; require __DIR__ . '/partials/sidebar.php'; ?>

    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-auto lg:h-20 flex flex-col lg:flex-row items-center justify-between px-4 lg:px-8 bg-glass-bg border-b border-white/5 shrink-0 backdrop-blur-xl sticky top-0 z-[100] py-4 lg:py-0 gap-4 lg:gap-0">
            <div class="flex items-center justify-between w-full lg:w-auto">
                <div class="flex flex-col">
                    <div class="text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] mb-1 hidden sm:block">Inbound Transmissions</div>
                    <h1 class="text-xl font-black text-white tracking-tight flex items-center gap-3 group">
                        <span class="text-neon-cyan drop-shadow-[0_0_8px_rgba(6,182,212,0.4)]">Registry Nexus</span>
                        <span class="opacity-20 translate-y-px hidden sm:inline">/</span>
                        <span class="text-sm tracking-widest text-slate-400 uppercase font-black hidden sm:inline">Engagement Feed</span>
                    </h1>
                </div>
                <div class="lg:hidden">
                    <?php require __DIR__ . '/partials/_topbar.php'; ?>
                </div>
            </div>
            
            <div class="flex items-center gap-6 w-full lg:w-auto overflow-hidden">
                <div class="flex-1 lg:flex-none overflow-hidden relative">
                    <div class="overflow-x-auto crm-main-scroll relative no-scrollbar mobile-scroll-mask">
                        <div class="flex bg-black/40 rounded-xl p-1 border border-white/10 shadow-inner w-max min-w-full">
                            <?php
                            $statuses = ['all' => 'ALL_STREAM', 'new' => 'NEW_SIG', 'viewed' => 'OBSERVED', 'contacted' => 'SYNCED', 'completed' => 'ARCHIVED', 'cancelled' => 'TERMINATED'];
                            $currentFilter = $_GET['status'] ?? 'all';
                            foreach ($statuses as $val => $label):
                            ?>
                                <a href="<?= baseUrl('admin/bookings?status=' . $val) ?>"
                                   class="px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all whitespace-nowrap <?= $currentFilter === $val ? 'bg-neon-cyan text-black shadow-[0_0_15px_rgba(6,182,212,0.4)]' : 'text-slate-500 hover:text-white hover:bg-white/5' ?>">
                                    <?= $label ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="hidden lg:block">
                    <?php require __DIR__ . '/partials/_topbar.php'; ?>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-8 bg-[#0b0e14]">
            <div class="admin-table-wrapper backdrop-blur-2xl border border-white/5 rounded-[2.5rem] overflow-hidden shadow-premium">
                <?php if (empty($bookings)): ?>
                    <div class="py-24 text-center">
                        <i class="ph-duotone ph-broadcast text-6xl text-slate-800 mb-4 block"></i>
                        <p class="text-slate-700 text-[10px] font-black uppercase tracking-[0.3em]">
                            <?= getCurrentLocale() === 'ar' ? 'لا توجد حجوزات.' : 'No active transmissions detected.' ?>
                        </p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto crm-main-scroll overflow-y-hidden">
                        <table class="admin-table w-full text-left border-collapse">
                            <thead>
                                <tr class="text-slate-600 text-[8px] font-black uppercase tracking-[0.4em] bg-white/[0.01]">
                                    <th class="py-6 px-10 w-24">ID</th>
                                    <th class="py-6 px-6">Entity Identity</th>
                                    <th class="py-6 px-6">Communication</th>
                                    <th class="py-6 px-6">Assigned Sector</th>
                                    <th class="py-6 px-6">Transmission Log</th>
                                    <th class="py-6 px-6 text-center">Protocol Cache</th>
                                    <th class="py-6 px-10 text-right">Sync Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/[0.02]">
                                <?php foreach ($bookings as $b): ?>
                                <tr class="hover:bg-white/[0.03] transition-all group/row border-b border-white/[0.03] last:border-0 relative">
                                    <td class="py-6 px-10 relative" data-label="Entry">
                                        <div class="flex items-center gap-2 group-hover/row:translate-x-1 transition-transform">
                                            <div class="w-1.5 h-1.5 rounded-full bg-neon-cyan/40 group-hover/row:bg-neon-cyan group-hover/row:animate-pulse transition-all"></div>
                                            <span class="text-[10px] font-black font-mono text-slate-700 uppercase">#<?= (int)$b['id'] ?></span>
                                        </div>
                                    </td>
                                    <td class="py-6 px-6" data-label="Identity">
                                        <div class="flex flex-col">
                                            <span class="text-[11px] font-black text-white uppercase tracking-tight group-hover/row:text-neon-cyan transition-colors"><?= e($b['name']) ?></span>
                                            <span class="text-[7px] text-slate-800 font-bold tracking-[0.2em] uppercase mt-0.5">Verified Identity</span>
                                        </div>
                                    </td>
                                    <td class="py-6 px-6" data-label="Comm">
                                        <div class="flex flex-col space-y-1">
                                            <div class="flex items-center gap-2 text-slate-400 group-hover/row:text-slate-300 transition-colors">
                                                <i class="ph ph-envelope-simple text-[10px] text-neon-cyan"></i>
                                                <span class="text-[9px] font-black font-mono tracking-tight"><?= e($b['email']) ?></span>
                                            </div>
                                            <?php if($b['phone']): ?>
                                            <div class="flex items-center gap-2 text-slate-500">
                                                <i class="ph ph-phone text-[10px] text-neon-emerald"></i>
                                                <span class="text-[9px] font-black font-mono tracking-tight"><?= e($b['phone']) ?></span>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="py-6 px-6" data-label="Sector">
                                        <span class="inline-flex px-3 py-1 rounded-lg bg-white/5 border border-white/5 text-[9px] font-black text-slate-500 uppercase tracking-widest group-hover/row:border-neon-cyan/20 group-hover/row:text-slate-300 transition-all">
                                            <?= e($b['service']) ?>
                                        </span>
                                    </td>
                                    <td class="py-6 px-6" data-label="Log">
                                        <div class="max-w-[200px] flex flex-col gap-1.5">
                                            <span class="text-[10px] text-slate-400 line-clamp-2 italic group-hover/row:text-slate-300 transition-colors"><?= e($b['message'] ?: '— No message node —') ?></span>
                                            <?php
                                            if (!empty($b['extra_fields'])) {
                                                $extra = json_decode($b['extra_fields'], true);
                                                if (is_array($extra)) {
                                                    echo '<div class="flex flex-wrap gap-1.5 mt-1">';
                                                    foreach ($extra as $k => $v) {
                                                        echo '<span class="text-[7px] font-black uppercase tracking-widest bg-neon-cyan/5 text-neon-cyan/60 px-2 py-0.5 rounded border border-neon-cyan/10">'.e($k).': '.e($v).'</span>';
                                                    }
                                                    echo '</div>';
                                                }
                                            }
                                            ?>
                                        </div>
                                    </td>
                                    <td class="py-6 px-6 text-center" data-label="Frequency">
                                        <div class="flex flex-col items-center">
                                            <span class="text-[10px] text-white font-black font-mono tracking-tight"><?= e(date('d.m.Y', strtotime($b['created_at']))) ?></span>
                                            <span class="text-[7px] text-slate-800 font-bold tracking-[0.2em] uppercase mt-0.5"><?= e($b['preferred_date'] ?: 'Async Time') ?></span>
                                        </div>
                                    </td>
                                    <td class="py-6 px-10 text-right" data-label="Status">
                                        <form method="POST" action="<?= baseUrl('admin/bookings/update-status') ?>" class="inline-block">
                                            <input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
                                            <div class="relative group/select">
                                                <?php
                                                    $statusMap = [
                                                        'new' => 'text-neon-cyan bg-neon-cyan/10 border-neon-cyan/30',
                                                        'viewed' => 'text-neon-blue bg-neon-blue/10 border-neon-blue/30',
                                                        'contacted' => 'text-neon-purple bg-neon-purple/10 border-neon-purple/30',
                                                        'completed' => 'text-neon-emerald bg-neon-emerald/10 border-neon-emerald/30',
                                                        'cancelled' => 'text-neon-rose bg-neon-rose/10 border-neon-rose/30'
                                                    ];
                                                    $currentClass = $statusMap[$b['status']] ?? 'text-slate-500 bg-white/10 border-white/20';
                                                ?>
                                                <select name="status" class="appearance-none bg-black/40 border <?= $currentClass ?> rounded-xl py-2 pl-4 pr-10 text-[9px] font-black uppercase tracking-widest cursor-pointer focus:outline-none focus:ring-1 focus:ring-neon-cyan transition-all" onchange="this.form.submit()">
                                                    <?php foreach ($statuses as $val => $label): if($val === 'all') continue; ?>
                                                        <option value="<?= $val ?>" <?= $b['status'] === $val ? 'selected' : '' ?>><?= $label ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <i class="ph-bold ph-caret-down absolute right-3 top-1/2 -translate-y-1/2 text-[10px] pointer-events-none opacity-40 group-hover/select:opacity-100 transition-opacity"></i>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
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
            color: #06b6d4 !important;
            letter-spacing: 2px !important;
            opacity: 0.6 !important;
            text-align: left !important;
            margin-right: 12px !important;
        }
        
        /* Adjust cell content for alignment */
        .admin-table td > * { 
            flex-shrink: 0 !important;
        }

        .mobile-scroll-mask {
            -webkit-mask-image: linear-gradient(to right, black 80%, transparent 100%);
            mask-image: linear-gradient(to right, black 80%, transparent 100%);
        }
    }
</style>
</body>
</html>
