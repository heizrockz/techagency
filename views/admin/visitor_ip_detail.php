<?php
$pageTitle = 'IP Activity — ' . htmlspecialchars($ip);
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
                <a href="<?= baseUrl('admin/visitors') ?>" class="text-slate-400 hover:text-white transition-colors p-2 -ml-2 rounded-lg hover:bg-white/5">
                    <i class="ph ph-arrow-left text-xl"></i>
                </a>
                <h1 class="text-xl font-semibold text-white tracking-tight flex items-center gap-2">
                    <i class="ph ph-detective text-primary"></i>
                    IP Activity Detail
                </h1>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-4 lg:p-6 crm-main-scroll bg-[#0b0e14]">
            <div class="max-w-5xl mx-auto space-y-8">

                <?php if (!$ipInfo): ?>
                    <div class="py-20 text-center text-slate-500 italic">No data found for this IP address.</div>
                <?php else: ?>

                <!-- IP Summary Card -->
                <div class="bg-[#1a2333]/40 backdrop-blur-lg border border-white/5 rounded-3xl p-8 shadow-2xl">
                    <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
                        <!-- Flag & Location -->
                        <div class="flex items-center gap-5">
                            <div class="w-20 h-20 rounded-2xl bg-black/40 border border-white/5 flex items-center justify-center text-4xl shadow-xl" id="ip-flag">
                                <script>document.getElementById('ip-flag').innerText = getCountryFlag('<?= htmlspecialchars($ipInfo['country_code']) ?>');</script>
                            </div>
                            <div>
                                <h2 class="text-2xl font-extrabold text-white tracking-tight"><?= htmlspecialchars($ip) ?></h2>
                                <p class="text-slate-400 mt-1"><?= htmlspecialchars($ipInfo['city']) ?>, <?= htmlspecialchars($ipInfo['region']) ?>, <?= htmlspecialchars($ipInfo['country']) ?></p>
                                <p class="text-slate-500 text-xs mt-1 flex items-center gap-2">
                                    <i class="ph ph-broadcast"></i> <?= htmlspecialchars($ipInfo['isp']) ?>
                                </p>
                            </div>
                        </div>

                        <!-- Stats -->
                        <div class="flex gap-4 ml-auto">
                            <div class="bg-black/30 border border-white/5 rounded-2xl px-6 py-4 text-center">
                                <div class="text-2xl font-extrabold text-white"><?= $ipPageCount ?></div>
                                <div class="text-[10px] text-slate-500 uppercase tracking-widest font-bold mt-1">Total Visits</div>
                            </div>
                            <div class="bg-black/30 border border-white/5 rounded-2xl px-6 py-4 text-center">
                                <div class="text-2xl font-extrabold text-white"><?= $ipUniquePages ?></div>
                                <div class="text-[10px] text-slate-500 uppercase tracking-widest font-bold mt-1">Unique Pages</div>
                            </div>
                            <div class="bg-black/30 border border-white/5 rounded-2xl px-6 py-4 text-center">
                                <?php if ($ipIsBot): ?>
                                    <div class="text-2xl">🤖</div>
                                    <div class="text-[10px] text-amber-400 uppercase tracking-widest font-bold mt-1">Bot</div>
                                <?php else: ?>
                                    <div class="text-2xl">👤</div>
                                    <div class="text-[10px] text-emerald-400 uppercase tracking-widest font-bold mt-1">Human</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- User Agent -->
                    <div class="mt-6 p-4 bg-black/30 border border-white/5 rounded-xl">
                        <div class="text-[10px] text-slate-500 uppercase tracking-widest font-bold mb-2">User Agent</div>
                        <code class="text-xs text-slate-300 break-all leading-relaxed"><?= htmlspecialchars($ipInfo['user_agent']) ?></code>
                    </div>
                </div>

                <!-- Activity Timeline -->
                <div class="bg-[#1a2333]/40 backdrop-blur-lg border border-white/5 rounded-3xl shadow-2xl overflow-hidden">
                    <div class="p-6 lg:p-8 border-b border-white/5">
                        <h2 class="text-xl font-bold text-white flex items-center gap-3">
                            <span class="w-10 h-10 rounded-xl bg-primary/20 flex items-center justify-center text-primary">
                                <i class="ph ph-path"></i>
                            </span>
                            Page Visit Timeline
                        </h2>
                        <p class="text-slate-500 text-sm mt-1">Complete browsing history for this visitor</p>
                    </div>

                    <div class="p-6 lg:p-8">
                        <div class="relative">
                            <!-- Timeline line -->
                            <div class="absolute left-[23px] top-0 bottom-0 w-px bg-white/5"></div>

                            <?php 
                            $prevDate = '';
                            foreach($ipVisits as $visit): 
                                $visitDate = date('M d, Y', strtotime($visit['visited_at']));
                                $showDate = ($visitDate !== $prevDate);
                                $prevDate = $visitDate;
                            ?>
                                <?php if ($showDate): ?>
                                    <div class="relative flex items-center gap-4 mb-4 <?= $visit !== $ipVisits[0] ? 'mt-8' : '' ?>">
                                        <div class="w-12 h-6 rounded-full bg-primary/10 border border-primary/20 flex items-center justify-center z-10">
                                            <i class="ph ph-calendar-blank text-primary text-xs"></i>
                                        </div>
                                        <span class="text-xs font-bold text-primary uppercase tracking-widest"><?= $visitDate ?></span>
                                    </div>
                                <?php endif; ?>

                                <div class="relative flex items-start gap-4 mb-3 group">
                                    <!-- Timeline dot -->
                                    <div class="w-12 flex items-center justify-center z-10 pt-1">
                                        <div class="w-3 h-3 rounded-full bg-slate-700 border-2 border-slate-600 group-hover:bg-primary group-hover:border-primary transition-all shadow-lg"></div>
                                    </div>
                                    <!-- Content -->
                                    <div class="flex-1 bg-black/20 border border-white/5 rounded-xl px-5 py-3 group-hover:border-primary/20 transition-all">
                                        <div class="flex items-center justify-between">
                                            <a href="<?= htmlspecialchars(BASE_URL . $visit['page_url']) ?>" target="_blank" class="text-primary hover:text-emerald-400 font-bold text-sm flex items-center gap-2 transition-colors">
                                                <i class="ph ph-link-simple"></i>
                                                <?= htmlspecialchars($visit['page_url'] == '/' ? '/ (Homepage)' : $visit['page_url']) ?>
                                            </a>
                                            <span class="text-[10px] text-slate-500 font-bold uppercase tracking-widest shrink-0 ml-4">
                                                <?= date('h:i:s A', strtotime($visit['visited_at'])) ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <?php endif; ?>

            </div>
        </main>
    </div>
</div>
</body>
</html>
