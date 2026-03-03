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

        <main class="flex-1 overflow-y-auto p-4 lg:p-6 crm-main-scroll">
            <div class="max-w-7xl mx-auto space-y-6">
                
                <!-- Quick Stats -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
                    <div class="bg-[#1a2333] border border-white/5 rounded-2xl p-5 shadow-xl relative overflow-hidden group">
                        <div class="absolute inset-0 bg-primary/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="flex justify-between items-start relative z-10">
                            <div>
                                <p class="text-sm text-slate-400 mb-1">Total Visits</p>
                                <h3 class="text-3xl font-bold text-white"><?= number_format($totalVisits) ?></h3>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                                <i class="ph ph-users text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-[#1a2333] border border-white/5 rounded-2xl p-5 shadow-xl relative overflow-hidden group">
                        <div class="absolute inset-0 bg-emerald-500/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="flex justify-between items-start relative z-10">
                            <div>
                                <p class="text-sm text-slate-400 mb-1">Unique IPs</p>
                                <h3 class="text-3xl font-bold text-white"><?= number_format($uniqueIps) ?></h3>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center">
                                <i class="ph ph-fingerprint text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-[#1a2333] border border-white/5 rounded-2xl p-5 shadow-xl relative overflow-hidden group">
                        <div class="absolute inset-0 bg-blue-500/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="flex justify-between items-start relative z-10">
                            <div>
                                <p class="text-sm text-slate-400 mb-1">Today's Active</p>
                                <h3 class="text-3xl font-bold text-white"><?= number_format($todayVisits) ?></h3>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center">
                                <i class="ph ph-calendar-star text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-[#1a2333] border border-white/5 rounded-2xl p-5 shadow-xl relative overflow-hidden group">
                        <div class="absolute inset-0 bg-purple-500/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="flex justify-between items-start relative z-10">
                            <div>
                                <p class="text-sm text-slate-400 mb-1">Countries Reached</p>
                                <h3 class="text-3xl font-bold text-white"><?= number_format($totalCountries) ?></h3>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center">
                                <i class="ph ph-globe-hemisphere-west text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Geolocation Summaries -->
                <?php if (!empty($countriesData)): ?>
                <div class="bg-[#1a2333] border border-white/5 rounded-2xl p-4 sm:p-6 shadow-xl">
                    <h2 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                        <i class="ph ph-map-pin text-primary"></i>
                        Top Countries
                    </h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-3">
                        <?php foreach($countriesData as $c): ?>
                            <div class="bg-black/20 border border-white/5 rounded-xl p-3 flex items-center justify-between hover:bg-white/5 transition-colors">
                                <div class="flex items-center gap-2 truncate">
                                    <span class="text-xl" id="flag-<?= htmlspecialchars($c['country_code']) ?>">
                                        <script>document.getElementById('flag-<?= htmlspecialchars($c['country_code']) ?>').innerText = getCountryFlag('<?= htmlspecialchars($c['country_code']) ?>');</script>
                                    </span>
                                    <span class="text-sm font-medium text-slate-300 truncate" title="<?= htmlspecialchars($c['country']) ?>">
                                        <?= htmlspecialchars($c['country']) ?>
                                    </span>
                                </div>
                                <span class="bg-white/10 text-xs px-2 py-0.5 rounded-full text-white font-medium">
                                    <?= number_format($c['visit_count']) ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Raw Logs Table -->
                <div class="bg-[#1a2333] border border-white/5 rounded-2xl shadow-xl overflow-hidden flex flex-col">
                    <div class="p-4 sm:p-6 border-b border-white/5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                            <i class="ph ph-list-dashes text-primary"></i>
                            Recent Visitor Log
                        </h2>
                    </div>
                    
                    <div class="overflow-x-auto w-full">
                        <table class="w-full text-left border-collapse whitespace-nowrap lg:whitespace-normal min-w-[800px]">
                            <thead>
                                <tr class="bg-black/20 text-slate-400 text-xs uppercase tracking-wider">
                                    <th class="py-3 px-4 font-semibold">Location</th>
                                    <th class="py-3 px-4 font-semibold">IP Address</th>
                                    <th class="py-3 px-4 font-semibold">ISP</th>
                                    <th class="py-3 px-4 font-semibold">Page URL</th>
                                    <th class="py-3 px-4 font-semibold text-right">Time</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5 text-sm">
                                <?php if(empty($visitorLogs)): ?>
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-slate-500 italic">No detailed visitor logs recorded yet.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach($visitorLogs as $log): ?>
                                    <tr class="hover:bg-white/[0.02] transition-colors">
                                        <td class="py-3 px-4">
                                            <div class="flex items-center gap-2">
                                                <span id="log-flag-<?= $log['id'] ?>">
                                                    <script>document.getElementById('log-flag-<?= $log['id'] ?>').innerText = getCountryFlag('<?= htmlspecialchars($log['country_code']) ?>');</script>
                                                </span>
                                                <div>
                                                    <span class="text-white block"><?= htmlspecialchars($log['country']) ?></span>
                                                    <span class="text-xs text-slate-500"><?= htmlspecialchars($log['city'] . ($log['region'] && $log['region']!='Unknown' ? ', ' . $log['region'] : '')) ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3 px-4 font-mono text-slate-400 text-xs">
                                            <?= htmlspecialchars($log['ip_address']) ?>
                                        </td>
                                        <td class="py-3 px-4 text-slate-400 text-xs truncate max-w-[150px]" title="<?= htmlspecialchars($log['isp']) ?>">
                                            <?= htmlspecialchars($log['isp']) ?>
                                        </td>
                                        <td class="py-3 px-4">
                                            <a href="<?= htmlspecialchars(BASE_URL . $log['page_url']) ?>" target="_blank" class="text-primary hover:text-emerald-400 transition-colors truncate max-w-[200px] inline-block" title="<?= htmlspecialchars($log['page_url']) ?>">
                                                <?= htmlspecialchars($log['page_url']) ?>
                                            </a>
                                        </td>
                                        <td class="py-3 px-4 text-right text-slate-500 whitespace-nowrap">
                                            <div class="text-slate-300"><?= date('M d, Y', strtotime($log['visited_at'])) ?></div>
                                            <div class="text-xs"><?= date('h:i A', strtotime($log['visited_at'])) ?></div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if($totalPages > 1): ?>
                    <div class="p-4 border-t border-white/5 flex items-center justify-between text-sm">
                        <span class="text-slate-500">Showing page <?= $page ?> of <?= $totalPages ?> (Total: <?= $totalLogs ?>)</span>
                        <div class="flex gap-1">
                            <?php if($page > 1): ?>
                                <a href="?page=<?= $page - 1 ?>" class="px-3 py-1 bg-white/5 hover:bg-white/10 rounded transition-colors text-white">Previous</a>
                            <?php endif; ?>
                            <?php if($page < $totalPages): ?>
                                <a href="?page=<?= $page + 1 ?>" class="px-3 py-1 bg-white/5 hover:bg-white/10 rounded transition-colors text-white">Next</a>
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
