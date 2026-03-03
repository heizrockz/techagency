<?php
$pageTitle = 'Visitor Analytics';
$currentPage = 'dashboard';
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
    <script>
        function getCountryFlag(countryCode) {
            if (!countryCode || countryCode === 'UNKNOWN' || countryCode === 'LOCAL') return '🌐';
            const codePoints = countryCode.toUpperCase().split('').map(char => 127397 + char.charCodeAt(0));
            return String.fromCodePoint(...codePoints);
        }
    </script>
</head>
<body>
<div class="admin-layout flex w-full">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>
    <div class="crm-main leading-relaxed text-slate-300 bg-[#0f1115]">
        
        <!-- Header -->
        <header class="h-16 flex items-center justify-between px-6 bg-[#1a2333] border-b border-white/5 shrink-0">
            <div class="flex items-center gap-4">
                <a href="<?= htmlspecialchars(BASE_URL) ?>/admin/dashboard" class="text-slate-400 hover:text-white transition-colors p-2 -ml-2 rounded-lg hover:bg-white/5">
                    <i class="ph ph-arrow-left text-xl"></i>
                </a>
                <h1 class="text-xl font-semibold text-white tracking-tight flex items-center gap-2">
                    <i class="ph ph-chart-line-up text-primary"></i>
                    Visitor Analytics
                </h1>
            </div>
            <div class="flex gap-2">
                <button onclick="window.location.reload()" class="btn-ghost flex items-center gap-2">
                    <i class="ph ph-arrows-clockwise"></i> Refresh
                </button>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-4 lg:p-6 crm-main-scroll bg-[#0b0e14]">
            <div class="max-w-7xl mx-auto space-y-8">
                
                <!-- Quick Stats -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-[#1a2333]/40 backdrop-blur-md border border-white/5 rounded-2xl p-6 shadow-2xl relative overflow-hidden group transition-all duration-300 hover:border-primary/20">
                        <div class="absolute -right-4 -top-4 w-20 h-20 bg-primary/5 rounded-full blur-2xl group-hover:bg-primary/10 transition-all"></div>
                        <div class="flex justify-between items-start relative z-10">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Total Visits</p>
                                <h3 class="text-3xl font-extrabold text-white tracking-tight"><?= number_format($totalVisits) ?></h3>
                                <div class="mt-2 flex items-center gap-1 text-[10px] text-emerald-400 font-bold bg-emerald-400/10 px-2 py-0.5 rounded-full w-fit">
                                    <i class="ph ph-trend-up"></i> Lifetime
                                </div>
                            </div>
                            <div class="w-12 h-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center shadow-inner group-hover:scale-110 transition-transform">
                                <i class="ph ph-users-three text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-[#1a2333]/40 backdrop-blur-md border border-white/5 rounded-2xl p-6 shadow-2xl relative overflow-hidden group transition-all duration-300 hover:border-emerald-500/20">
                        <div class="absolute -right-4 -top-4 w-20 h-20 bg-emerald-500/5 rounded-full blur-2xl group-hover:bg-emerald-500/10 transition-all"></div>
                        <div class="flex justify-between items-start relative z-10">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Unique IPs</p>
                                <h3 class="text-3xl font-extrabold text-white tracking-tight"><?= number_format($uniqueIps) ?></h3>
                                <div class="mt-2 flex items-center gap-2">
                                    <span class="text-[10px] text-emerald-400 font-bold bg-emerald-400/10 px-2 py-0.5 rounded-full">👤 <?= $humanIpCount ?> Human</span>
                                    <span class="text-[10px] text-amber-400 font-bold bg-amber-400/10 px-2 py-0.5 rounded-full">🤖 <?= $botIpCount ?> Bot</span>
                                </div>
                            </div>
                            <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center shadow-inner group-hover:scale-110 transition-transform">
                                <i class="ph ph-fingerprint text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-[#1a2333]/40 backdrop-blur-md border border-white/5 rounded-2xl p-6 shadow-2xl relative overflow-hidden group transition-all duration-300 hover:border-blue-500/20">
                        <div class="absolute -right-4 -top-4 w-20 h-20 bg-blue-500/5 rounded-full blur-2xl group-hover:bg-blue-500/10 transition-all"></div>
                        <div class="flex justify-between items-start relative z-10">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Active Today</p>
                                <h3 class="text-3xl font-extrabold text-white tracking-tight"><?= number_format($todayVisits) ?></h3>
                                <div class="mt-2 flex items-center gap-1 text-[10px] text-blue-400 font-bold bg-blue-400/10 px-2 py-0.5 rounded-full w-fit">
                                    <i class="ph ph-lightning"></i> Real-time
                                </div>
                            </div>
                            <div class="w-12 h-12 rounded-2xl bg-blue-500/10 text-blue-400 flex items-center justify-center shadow-inner group-hover:scale-110 transition-transform">
                                <i class="ph ph-clock-countdown text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-[#1a2333]/40 backdrop-blur-md border border-white/5 rounded-2xl p-6 shadow-2xl relative overflow-hidden group transition-all duration-300 hover:border-purple-500/20">
                        <div class="absolute -right-4 -top-4 w-20 h-20 bg-purple-500/5 rounded-full blur-2xl group-hover:bg-purple-500/10 transition-all"></div>
                        <div class="flex justify-between items-start relative z-10">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Global Coverage</p>
                                <h3 class="text-3xl font-extrabold text-white tracking-tight"><?= number_format($totalCountries) ?></h3>
                                <div class="mt-2 text-[10px] text-purple-400 font-bold bg-purple-400/10 px-2 py-0.5 rounded-full w-fit">Countries</div>
                            </div>
                            <div class="w-12 h-12 rounded-2xl bg-purple-500/10 text-purple-400 flex items-center justify-center shadow-inner group-hover:scale-110 transition-transform">
                                <i class="ph ph-globe-hemisphere-east text-2xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Geolocation Summaries -->
                <?php if (!empty($countriesData)): ?>
                <div class="bg-[#1a2333]/40 backdrop-blur-lg border border-white/5 rounded-3xl p-6 lg:p-8 shadow-2xl">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h2 class="text-xl font-bold text-white flex items-center gap-3">
                                <span class="w-10 h-10 rounded-xl bg-primary/20 flex items-center justify-center text-primary">
                                    <i class="ph ph-map-trifold"></i>
                                </span>
                                Geographic Distribution
                            </h2>
                            <p class="text-slate-500 text-sm mt-1">Top performing regions by visit count</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                        <?php foreach($countriesData as $c): ?>
                            <div class="bg-black/30 border border-white/5 rounded-2xl p-4 flex flex-col gap-3 group hover:bg-primary/5 hover:border-primary/20 transition-all duration-300 cursor-default shadow-sm hover:shadow-primary/5">
                                <div class="flex justify-between items-start">
                                    <span class="text-3xl filter saturate-150 group-hover:scale-110 transition-transform duration-300" id="flag-<?= htmlspecialchars($c['country_code']) ?>">
                                        <script>document.getElementById('flag-<?= htmlspecialchars($c['country_code']) ?>').innerText = getCountryFlag('<?= htmlspecialchars($c['country_code']) ?>');</script>
                                    </span>
                                    <span class="bg-primary/10 text-primary text-[10px] font-bold px-2 py-1 rounded-lg">
                                        <?= number_format($c['visit_count']) ?>
                                    </span>
                                </div>
                                <div>
                                    <h4 class="text-white font-bold text-sm truncate" title="<?= htmlspecialchars($c['country']) ?>"><?= htmlspecialchars($c['country']) ?></h4>
                                    <p class="text-xs text-slate-500 uppercase tracking-widest mt-0.5"><?= htmlspecialchars($c['country_code']) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Raw Logs Table -->
                <div class="bg-[#1a2333]/40 backdrop-blur-lg border border-white/5 rounded-3xl shadow-2xl overflow-hidden flex flex-col">
                    <div class="p-6 lg:p-8 border-b border-white/5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-bold text-white flex items-center gap-3">
                                <span class="w-10 h-10 rounded-xl bg-primary/20 flex items-center justify-center text-primary">
                                    <i class="ph ph-identification-badge"></i>
                                </span>
                                Visitor Activity Log
                            </h2>
                            <p class="text-slate-500 text-sm mt-1">Live feed — click any IP to see full activity</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <!-- Filter buttons -->
                            <div class="flex items-center bg-black/30 rounded-xl border border-white/5 overflow-hidden">
                                <a href="?filter=all" class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-widest transition-all <?= ($filter ?? 'all') === 'all' ? 'bg-primary/20 text-primary' : 'text-slate-500 hover:text-white' ?>">All</a>
                                <a href="?filter=humans" class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-widest transition-all <?= ($filter ?? 'all') === 'humans' ? 'bg-emerald-500/20 text-emerald-400' : 'text-slate-500 hover:text-white' ?>">👤 Humans</a>
                                <a href="?filter=bots" class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-widest transition-all <?= ($filter ?? 'all') === 'bots' ? 'bg-amber-500/20 text-amber-400' : 'text-slate-500 hover:text-white' ?>">🤖 Bots</a>
                            </div>
                             <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-[#10b981] bg-[#10b981]/10 px-3 py-1.5 rounded-full border border-[#10b981]/20">
                                <span class="w-2 h-2 rounded-full bg-[#10b981] animate-ping"></span>
                                Live Feed
                             </div>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto w-full">
                        <table class="w-full text-left border-collapse min-w-[1100px]">
                            <thead>
                                <tr class="bg-black/40 text-slate-500 text-[10px] font-bold uppercase tracking-[0.2em]">
                                    <th class="py-5 px-8">Location & Identity</th>
                                    <th class="py-5 px-4 text-center">Protocol (IP)</th>
                                    <th class="py-5 px-4">ISP / Network</th>
                                    <th class="py-5 px-4 text-center">Type</th>
                                    <th class="py-5 px-4">Resource Accessed</th>
                                    <th class="py-5 px-8 text-right">Timestamp</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/[0.03] text-sm">
                                <?php if(empty($visitorLogs)): ?>
                                    <tr>
                                        <td colspan="6" class="py-20 text-center text-slate-500">
                                            <div class="flex flex-col items-center gap-4">
                                                <i class="ph ph-sneaker-move text-5xl opacity-20"></i>
                                                <p class="italic">No visitor footprints detected yet.</p>
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
                                    <tr class="hover:bg-white/[0.01] transition-colors group">
                                        <td class="py-5 px-8">
                                            <div class="flex items-center gap-4">
                                                <div class="w-12 h-12 rounded-full bg-black/40 border border-white/5 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform duration-300 shadow-xl" id="log-flag-<?= $log['id'] ?>">
                                                    <script>document.getElementById('log-flag-<?= $log['id'] ?>').innerText = getCountryFlag('<?= htmlspecialchars($log['country_code']) ?>');</script>
                                                </div>
                                                <div>
                                                    <span class="text-white font-bold block leading-none mb-1 group-hover:text-primary transition-colors"><?= htmlspecialchars($log['country']) ?></span>
                                                    <span class="text-xs text-slate-500 tracking-tight font-medium"><?= htmlspecialchars($log['city'] . ($log['region'] && $log['region']!='Unknown' ? ', ' . $log['region'] : '')) ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-5 px-4 text-center">
                                            <a href="<?= baseUrl('admin/visitors?action=ip_detail&ip=' . urlencode($log['ip_address'])) ?>" 
                                               class="px-3 py-1.5 rounded-lg bg-white/5 border border-white/5 font-mono text-[11px] text-primary hover:bg-primary/10 hover:border-primary/30 transition-all cursor-pointer inline-flex items-center gap-1.5"
                                               title="Click to view all activity from this IP">
                                                <i class="ph ph-magnifying-glass-plus text-xs opacity-60"></i>
                                                <?= htmlspecialchars($log['ip_address']) ?>
                                            </a>
                                        </td>
                                        <td class="py-5 px-4">
                                            <div class="flex items-center gap-2 max-w-[200px]" title="<?= htmlspecialchars($log['isp']) ?>">
                                                <i class="ph ph-broadcast text-slate-600"></i>
                                                <span class="text-xs text-slate-400 truncate"><?= htmlspecialchars($log['isp']) ?></span>
                                            </div>
                                        </td>
                                        <td class="py-5 px-4 text-center">
                                            <?php if ($logIsBot): ?>
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-400 text-[10px] font-bold uppercase tracking-widest" title="<?= htmlspecialchars($log['user_agent']) ?>">
                                                    <i class="ph ph-robot"></i> Bot
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-bold uppercase tracking-widest" title="<?= htmlspecialchars($log['user_agent']) ?>">
                                                    <i class="ph ph-user"></i> Human
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-5 px-4">
                                            <a href="<?= htmlspecialchars(BASE_URL . $log['page_url']) ?>" target="_blank" class="flex items-center gap-2 text-primary hover:text-emerald-400 font-medium transition-all group-hover:translate-x-1 duration-300">
                                                <i class="ph ph-link-simple text-lg"></i>
                                                <span class="truncate max-w-[200px]" title="<?= htmlspecialchars($log['page_url']) ?>">
                                                    <?= htmlspecialchars($log['page_url'] == '/' ? '/ (Homepage)' : $log['page_url']) ?>
                                                </span>
                                            </a>
                                        </td>
                                        <td class="py-5 px-8 text-right">
                                            <div class="text-white font-bold text-xs"><?= date('M d, Y', strtotime($log['visited_at'])) ?></div>
                                            <div class="text-[10px] text-slate-500 font-medium uppercase tracking-widest mt-1"><?= date('h:i A', strtotime($log['visited_at'])) ?></div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if($totalPages > 1): ?>
                    <div class="p-8 border-t border-white/[0.03] flex flex-col sm:flex-row items-center justify-between gap-6">
                        <div class="text-slate-500 text-xs font-semibold uppercase tracking-[0.2em] bg-white/5 px-4 py-2 rounded-full border border-white/5">
                            Page <span class="text-white"><?= $page ?></span> <span class="mx-2 opacity-30">/</span> <?= $totalPages ?> <span class="mx-2 opacity-30">|</span> <span class="text-primary"><?= number_format($totalLogs) ?></span> Total Records
                        </div>
                        <div class="flex gap-2">
                            <?php if($page > 1): ?>
                                <a href="?page=<?= $page - 1 ?>&filter=<?= $filter ?? 'all' ?>" class="px-6 py-2.5 bg-white/5 hover:bg-white/10 border border-white/5 rounded-2xl transition-all text-white text-xs font-bold uppercase tracking-widest flex items-center gap-2 shadow-xl hover:shadow-primary/5">
                                    <i class="ph ph-caret-left"></i> Previous
                                </a>
                            <?php endif; ?>
                            <?php if($page < $totalPages): ?>
                                <a href="?page=<?= $page + 1 ?>&filter=<?= $filter ?? 'all' ?>" class="px-6 py-2.5 bg-primary/10 hover:bg-primary border border-primary/20 hover:border-primary rounded-2xl transition-all text-primary hover:text-white text-xs font-bold uppercase tracking-widest flex items-center gap-2 shadow-xl hover:shadow-primary/20">
                                    Next <i class="ph ph-caret-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
        </main>
    </div>
</div>
</body>
</html>
