<?php
$pageTitle = 'Visitor Analytics';
$currentPage = 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= htmlspecialchars($pageTitle . ' - ' . APP_NAME) ?></title>
    <?php require __DIR__ . '/partials/_head_assets.php'; ?>
    <script>
        function getCountryFlag(countryCode) {
            if (!countryCode || countryCode === 'UNKNOWN' || countryCode === 'LOCAL') return '🌐';
            const codePoints = countryCode.toUpperCase().split('').map(char => 127397 + char.charCodeAt(0));
            return String.fromCodePoint(...codePoints);
        }
    </script>
</head>
<body>
<div class="admin-layout flex w-full h-screen overflow-hidden">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>
    <div class="flex-1 flex flex-col min-w-0 bg-[#0f1115]">
        
        <!-- Header -->
        <!-- Header -->
        <header class="h-20 flex items-center justify-between px-8 bg-glass-bg border-b border-white/5 shrink-0 backdrop-blur-xl sticky top-0 z-[100]">
            <div class="flex flex-col">
                <div class="text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] mb-1 hidden sm:block">Intelligence Gathering</div>
                <h1 class="text-xl font-black text-white tracking-tight flex items-center gap-3 group">
                    <span class="text-neon-cyan drop-shadow-[0_0_8px_rgba(6,182,212,0.4)]">Visitor Intel</span>
                    <span class="opacity-20 translate-y-px hidden sm:inline">/</span>
                    <span class="text-[10px] tracking-widest text-slate-400 uppercase font-black hidden sm:inline-block">Telemetry Streams</span>
                </h1>
            </div>
            <div class="flex items-center gap-6">
                <button onclick="window.location.reload()" class="px-3 sm:px-6 py-2.5 bg-white/5 hover:bg-white/10 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all border border-white/10 flex items-center gap-2">
                    <i class="ph-bold ph-arrows-clockwise text-sm"></i> <span class="hidden sm:inline">Sync Feed</span>
                </button>
                <?php require __DIR__ . '/partials/_topbar.php'; ?>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-4 lg:p-6 crm-main-scroll bg-[#0b0e14]">
            <div class="max-w-7xl mx-auto space-y-8">
                
                <!-- Quick Stats -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="admin-stat-card bg-glass-bg border border-white/5 rounded-[2rem] p-8 relative overflow-hidden group shadow-premium">
                        <div class="absolute -right-8 -top-8 w-24 h-24 bg-neon-cyan/5 rounded-full blur-3xl group-hover:bg-neon-cyan/10 transition-all"></div>
                        <div class="flex justify-between items-start relative z-10">
                            <div>
                                <p class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-500 mb-3">Total Transmissions</p>
                                <h3 class="text-3xl font-black text-white tracking-tighter drop-shadow-sm"><?= number_format($totalVisits) ?></h3>
                                <div class="mt-3 inline-flex items-center gap-2 text-[8px] text-neon-emerald font-black bg-neon-emerald/10 border border-neon-emerald/20 px-3 py-1 rounded-lg uppercase tracking-widest">
                                    <i class="ph-bold ph-trend-up"></i> Lifetime_Sync
                                </div>
                            </div>
                            <div class="w-14 h-14 rounded-2xl bg-neon-cyan/10 text-neon-cyan flex items-center justify-center shadow-lg border border-neon-cyan/20 group-hover:scale-110 transition-all">
                                <i class="ph-duotone ph-users-three text-3xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="admin-stat-card bg-glass-bg border border-white/5 rounded-[2rem] p-8 relative overflow-hidden group shadow-premium">
                        <div class="absolute -right-8 -top-8 w-24 h-24 bg-neon-emerald/5 rounded-full blur-3xl group-hover:bg-neon-emerald/10 transition-all"></div>
                        <div class="flex justify-between items-start relative z-10">
                            <div>
                                <p class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-500 mb-3">Unique Signatures</p>
                                <h3 class="text-3xl font-black text-white tracking-tighter drop-shadow-sm"><?= number_format($uniqueIps) ?></h3>
                                <div class="mt-3 flex items-center gap-2">
                                    <span class="text-[8px] text-neon-cyan font-black bg-neon-cyan/10 border border-neon-cyan/20 px-2 py-1 rounded-lg uppercase tracking-widest">👤 <?= $humanIpCount ?> Human</span>
                                    <span class="text-[8px] text-neon-amber font-black bg-neon-amber/10 border border-neon-amber/20 px-2 py-1 rounded-lg uppercase tracking-widest">🤖 <?= $botIpCount ?> Bot</span>
                                </div>
                            </div>
                            <div class="w-14 h-14 rounded-2xl bg-neon-emerald/10 text-neon-emerald flex items-center justify-center shadow-lg border border-neon-emerald/20 group-hover:scale-110 transition-all">
                                <i class="ph-duotone ph-fingerprint text-3xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="admin-stat-card bg-glass-bg border border-white/5 rounded-[2rem] p-8 relative overflow-hidden group shadow-premium">
                        <div class="absolute -right-8 -top-8 w-24 h-24 bg-neon-purple/5 rounded-full blur-3xl group-hover:bg-neon-purple/10 transition-all"></div>
                        <div class="flex justify-between items-start relative z-10">
                            <div>
                                <p class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-500 mb-3">Temporal Activity</p>
                                <h3 class="text-3xl font-black text-white tracking-tighter drop-shadow-sm"><?= number_format($todayVisits) ?></h3>
                                <div class="mt-3 inline-flex items-center gap-2 text-[8px] text-neon-cyan font-black bg-neon-cyan/10 border border-neon-cyan/20 px-3 py-1 rounded-lg uppercase tracking-widest">
                                    <i class="ph-bold ph-lightning"></i> Real_Time_Flux
                                </div>
                            </div>
                            <div class="w-14 h-14 rounded-2xl bg-neon-purple/10 text-neon-purple flex items-center justify-center shadow-lg border border-neon-purple/20 group-hover:scale-110 transition-all">
                                <i class="ph-duotone ph-clock-countdown text-3xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="admin-stat-card bg-glass-bg border border-white/5 rounded-[2rem] p-8 relative overflow-hidden group shadow-premium">
                        <div class="absolute -right-8 -top-8 w-24 h-24 bg-neon-amber/5 rounded-full blur-3xl group-hover:bg-neon-amber/10 transition-all"></div>
                        <div class="flex justify-between items-start relative z-10">
                            <div>
                                <p class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-500 mb-3">Global Cluster</p>
                                <h3 class="text-3xl font-black text-white tracking-tighter drop-shadow-sm"><?= number_format($totalCountries) ?></h3>
                                <div class="mt-3 inline-flex items-center gap-2 text-[8px] text-neon-amber font-black bg-neon-amber/10 border border-neon-amber/20 px-3 py-1 rounded-lg uppercase tracking-widest">Regions_Identified</div>
                            </div>
                            <div class="w-14 h-14 rounded-2xl bg-neon-amber/10 text-neon-amber flex items-center justify-center shadow-lg border border-neon-amber/20 group-hover:scale-110 transition-all">
                                <i class="ph-duotone ph-globe-hemisphere-east text-3xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Geolocation Summaries -->
                <?php if (!empty($countriesData)): ?>
                <div class="admin-table-wrapper backdrop-blur-2xl border border-white/5 rounded-[2.5rem] overflow-hidden shadow-premium p-10 bg-white/[0.01]">
                    <div class="flex items-center gap-4 mb-8 border-b border-white/5 pb-6">
                        <div class="w-10 h-10 rounded-xl bg-neon-cyan/10 text-neon-cyan flex items-center justify-center text-xl shadow-lg border border-neon-cyan/20">
                            <i class="ph-duotone ph-map-trifold"></i>
                        </div>
                        <div class="flex flex-col">
                            <h2 class="text-[11px] font-black uppercase tracking-[0.3em] text-white">Geographic Distribution</h2>
                            <p class="text-[8px] text-slate-600 font-bold uppercase tracking-tight mt-1">Top performing regions by visit count</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
                        <?php foreach($countriesData as $c): ?>
                            <div class="bg-black/30 border border-white/5 rounded-2xl p-6 flex flex-col gap-4 group hover:bg-neon-cyan/5 hover:border-neon-cyan/30 transition-all duration-500 cursor-default shadow-sm relative overflow-hidden">
                                <div class="absolute -right-4 -top-4 w-12 h-12 bg-neon-cyan/5 rounded-full blur-xl group-hover:bg-neon-cyan/10 transition-all"></div>
                                <div class="flex justify-between items-start relative z-10">
                                    <span class="text-4xl filter saturate-150 group-hover:scale-110 group-hover:-rotate-6 transition-transform duration-500" id="flag-<?= htmlspecialchars($c['country_code']) ?>">
                                        <script>document.getElementById('flag-<?= htmlspecialchars($c['country_code']) ?>').innerText = getCountryFlag('<?= htmlspecialchars($c['country_code']) ?>');</script>
                                    </span>
                                    <span class="bg-neon-cyan/10 text-neon-cyan text-[9px] font-black px-3 py-1 rounded-lg border border-neon-cyan/20 shadow-lg">
                                        <?= number_format($c['visit_count']) ?>
                                    </span>
                                </div>
                                <div class="relative z-10">
                                    <h4 class="text-white font-black text-[11px] uppercase tracking-wider truncate" title="<?= htmlspecialchars($c['country']) ?>"><?= htmlspecialchars($c['country']) ?></h4>
                                    <p class="text-[8px] text-slate-500 font-black uppercase tracking-[0.2em] mt-1"><?= htmlspecialchars($c['country_code']) ?>_NODE</p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Raw Logs Table -->
                <div class="admin-table-wrapper backdrop-blur-2xl border border-white/5 rounded-[2.5rem] overflow-hidden shadow-premium mb-20">
                    <div class="p-8 border-b border-white/5 bg-white/[0.01] flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-neon-cyan/10 text-neon-cyan flex items-center justify-center text-xl shadow-lg border border-neon-cyan/20">
                                <i class="ph-duotone ph-identification-badge"></i>
                            </div>
                            <div class="flex flex-col">
                                <h3 class="text-[11px] font-black uppercase tracking-[0.3em] text-white m-0">Visitor Activity Log</h3>
                                <p class="text-[8px] text-slate-600 font-bold uppercase tracking-tight mt-1">Live telemetry feed — cryptographic footprint analysis</p>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-4">
                            <!-- Filter buttons -->
                            <div class="flex items-center bg-black/40 p-1 rounded-2xl border border-white/5 shadow-inner">
                                <a href="?filter=all" class="px-5 py-2 text-[9px] font-black uppercase tracking-[0.2em] transition-all rounded-xl <?= ($filter ?? 'all') === 'all' ? 'bg-neon-cyan/10 text-neon-cyan border border-neon-cyan/20' : 'text-slate-600 hover:text-white' ?>">All</a>
                                <a href="?filter=humans" class="px-5 py-2 text-[9px] font-black uppercase tracking-[0.2em] transition-all rounded-xl <?= ($filter ?? 'all') === 'humans' ? 'bg-neon-emerald/10 text-neon-emerald border border-neon-emerald/20' : 'text-slate-600 hover:text-white' ?>">👤 Humans</a>
                                <a href="?filter=bots" class="px-5 py-2 text-[9px] font-black uppercase tracking-[0.2em] transition-all rounded-xl <?= ($filter ?? 'all') === 'bots' ? 'bg-neon-amber/10 text-neon-amber border border-neon-amber/20' : 'text-slate-600 hover:text-white' ?>">🤖 Bots</a>
                            </div>
                             <div class="flex items-center gap-3 text-[9px] font-black uppercase tracking-widest text-neon-emerald bg-neon-emerald/5 px-4 py-2 rounded-xl border border-neon-emerald/20 shadow-[0_0_15px_rgba(16,185,129,0.1)]">
                                <span class="w-2 h-2 rounded-full bg-neon-emerald shadow-[0_0_8px_rgba(16,185,129,1)] animate-pulse"></span>
                                Live_Sync
                             </div>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto crm-main-scroll">
                        <table class="admin-table w-full text-left border-collapse min-w-[1100px]">
                            <thead>
                                <tr class="text-slate-600 text-[8px] font-black uppercase tracking-[0.4em] bg-white/[0.01]">
                                    <th class="py-6 px-10">Identity Cluster</th>
                                    <th class="py-6 px-6 text-center">Neural Frequency</th>
                                    <th class="py-6 px-6">Access Grid / ISP</th>
                                    <th class="py-6 px-6 text-center">Protocol</th>
                                    <th class="py-6 px-6">Terminal Node</th>
                                    <th class="py-6 px-10 text-right">Temporal Marker</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/[0.02]">
                                <?php if(empty($visitorLogs)): ?>
                                    <tr>
                                        <td colspan="6" class="py-32 text-center">
                                            <div class="flex flex-col items-center gap-5">
                                                <i class="ph-duotone ph-sneaker-move text-6xl text-slate-800 animate-bounce"></i>
                                                <p class="text-[10px] text-slate-700 font-black uppercase tracking-[0.3em]">No visitor footprints detected in this sector.</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach($visitorLogs as $log): ?>
                                    <?php 
                                        $logIsBot = isBot($log['user_agent']);
                                        // Apply filter
                                        if (($filter ?? 'all') === 'humans' && $logIsBot) continue;
                                        if (($filter ?? 'all') === 'bots' && !$logIsBot) continue;
                                    ?>
                                    <tr class="hover:bg-white/[0.03] transition-all group/row">
                                        <td class="py-6 px-10" data-label="Identity">
                                            <div class="flex items-center gap-5">
                                                <div class="w-12 h-12 rounded-xl bg-black/40 border border-white/10 flex items-center justify-center text-2xl shadow-lg ring-1 ring-white/5 group-hover/row:border-neon-cyan/40 transition-all" id="log-flag-<?= $log['id'] ?>">
                                                    <script>document.getElementById('log-flag-<?= $log['id'] ?>').innerText = getCountryFlag('<?= htmlspecialchars($log['country_code']) ?>');</script>
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-white font-black text-[11px] uppercase tracking-wider group-hover/row:text-neon-cyan transition-colors"><?= htmlspecialchars($log['country']) ?></span>
                                                    <span class="text-[9px] text-slate-600 font-bold uppercase tracking-widest mt-0.5"><?= htmlspecialchars($log['city'] . ($log['region'] && $log['region']!='Unknown' ? ', ' . $log['region'] : '')) ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-6 px-6 text-center" data-label="Frequency">
                                            <a href="<?= baseUrl('admin/visitors?action=ip_detail&ip=' . urlencode($log['ip_address'])) ?>" 
                                               class="px-4 py-2 rounded-xl bg-black/40 border border-white/10 font-mono text-[10px] font-black text-neon-cyan hover:bg-neon-cyan/10 hover:border-neon-cyan shadow-inner transition-all inline-flex items-center gap-2 group/ip">
                                                <?= htmlspecialchars($log['ip_address']) ?>
                                                <i class="ph-bold ph-magnifying-glass text-[8px] opacity-0 group-hover/ip:opacity-100 transition-opacity"></i>
                                            </a>
                                        </td>
                                        <td class="py-6 px-6" data-label="ISP">
                                            <div class="flex items-center gap-3 max-w-[200px]" title="<?= htmlspecialchars($log['isp']) ?>">
                                                <span class="text-[9px] text-slate-500 font-bold uppercase tracking-widest truncate"><?= htmlspecialchars($log['isp']) ?></span>
                                            </div>
                                        </td>
                                        <td class="py-6 px-6 text-center" data-label="Protocol">
                                            <?php if ($logIsBot): ?>
                                                <div class="inline-flex items-center gap-2 px-3 py-1 bg-neon-amber/10 border border-neon-amber/20 rounded-lg">
                                                    <span class="w-1 h-1 rounded-full bg-neon-amber shadow-[0_0_8px_rgba(245,158,11,0.5)]"></span>
                                                    <span class="text-[8px] text-neon-amber font-black uppercase tracking-widest">Bot_Node</span>
                                                </div>
                                            <?php else: ?>
                                                <div class="inline-flex items-center gap-2 px-3 py-1 bg-neon-emerald/10 border border-neon-emerald/20 rounded-lg">
                                                    <span class="w-1 h-1 rounded-full bg-neon-emerald shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                                                    <span class="text-[8px] text-neon-emerald font-black uppercase tracking-widest">Human_Ent</span>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-6 px-6" data-label="Terminal">
                                            <a href="<?= htmlspecialchars(BASE_URL . $log['page_url']) ?>" target="_blank" class="flex items-center gap-2 text-neon-cyan hover:text-white text-[10px] font-black uppercase tracking-widest truncate max-w-[180px] group/link">
                                                <i class="ph-bold ph-link-simple opacity-40 group-hover/link:opacity-100"></i>
                                                <?= htmlspecialchars($log['page_url'] == '/' ? '/ (Index)' : $log['page_url']) ?>
                                            </a>
                                        </td>
                                        <td class="py-6 px-10 text-right" data-label="Time">
                                            <div class="text-white font-black text-[11px] tracking-tighter uppercase"><?= date('M d', strtotime($log['visited_at'])) ?></div>
                                            <div class="text-[9px] text-slate-500 font-black uppercase tracking-widest mt-1 opacity-60"><?= date('h:i A', strtotime($log['visited_at'])) ?></div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if($totalPages > 1): ?>
                    <div class="p-8 border-t border-white/[0.03] bg-white/[0.01] flex flex-col sm:flex-row items-center justify-between gap-8">
                        <div class="text-slate-600 text-[9px] font-black uppercase tracking-[0.3em] bg-black/40 px-6 py-3 rounded-2xl border border-white/5 shadow-inner">
                            Sequence <span class="text-white"><?= $page ?></span> <span class="mx-3 opacity-20">/</span> <?= $totalPages ?> <span class="mx-3 opacity-20">|</span> <span class="text-neon-cyan"><?= number_format($totalLogs) ?></span> Total_Transmissions
                        </div>
                        <div class="flex gap-4">
                            <?php if($page > 1): ?>
                                <a href="?page=<?= $page - 1 ?>&filter=<?= $filter ?? 'all' ?>" class="px-8 py-3.5 bg-white/5 hover:bg-white/10 border border-white/5 rounded-2xl transition-all text-white text-[10px] font-black uppercase tracking-widest flex items-center gap-3 active:scale-95 shadow-lg">
                                    <i class="ph-bold ph-caret-left"></i> Previous_Block
                                </a>
                            <?php endif; ?>
                            <?php if($page < $totalPages): ?>
                                <a href="?page=<?= $page + 1 ?>&filter=<?= $filter ?? 'all' ?>" class="px-8 py-3.5 bg-neon-cyan text-black border border-neon-cyan/20 rounded-2xl transition-all text-[10px] font-black uppercase tracking-widest flex items-center gap-3 hover:bg-cyan-400 active:scale-95 shadow-[0_0_20px_rgba(6,182,212,0.2)]">
                                    Next_Block <i class="ph-bold ph-caret-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

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
            color: #06b6d4 !important;
            letter-spacing: 2px !important;
            opacity: 0.6 !important;
            text-align: left !important;
            margin-right: 12px !important;
        }
    }
</style>
</body>
</html>
