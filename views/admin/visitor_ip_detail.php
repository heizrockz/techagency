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
<div class="admin-layout flex w-full h-screen overflow-hidden">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>
    <div class="crm-main leading-relaxed text-slate-300 bg-[#0f1115]">
        
        <!-- Header -->
        <!-- Header -->
        <header class="h-20 flex items-center justify-between px-8 bg-glass-bg border-b border-white/5 shrink-0 backdrop-blur-xl sticky top-0 z-[100]">
            <div class="flex items-center gap-4">
                <a href="<?= baseUrl('admin/visitors') ?>" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white/5 text-slate-500 hover:text-neon-cyan hover:bg-neon-cyan/5 border border-white/10 hover:border-neon-cyan/20 transition-all active:scale-95 shadow-lg group">
                    <i class="ph-bold ph-arrow-left text-xl transition-transform group-hover:-translate-x-1"></i>
                </a>
                <div class="flex flex-col">
                    <div class="text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] mb-1 hidden sm:block">Deep Intelligence</div>
                    <h1 class="text-xl font-black text-white tracking-tight flex items-center gap-3 group">
                        <span class="text-neon-cyan drop-shadow-[0_0_8px_rgba(6,182,212,0.4)]">IP Detail</span>
                        <span class="opacity-20 translate-y-px hidden sm:inline">/</span>
                        <span class="text-[10px] tracking-widest text-slate-400 uppercase font-black hidden sm:inline-block"><?= htmlspecialchars($ip) ?></span>
                    </h1>
                </div>
            </div>
            <?php require __DIR__ . '/partials/_topbar.php'; ?>
        </header>

        <main class="flex-1 overflow-y-auto p-4 lg:p-6 crm-main-scroll bg-[#0b0e14]">
            <div class="max-w-5xl mx-auto space-y-8">

                <?php if (!$ipInfo): ?>
                    <div class="py-20 text-center text-slate-500 italic">No data found for this IP address.</div>
                <?php else: ?>

                <!-- IP Summary Card -->
                <div class="admin-table-wrapper backdrop-blur-2xl border border-white/5 rounded-[2.5rem] overflow-hidden shadow-premium p-10 bg-white/[0.01] relative group">
                    <div class="absolute -right-20 -top-20 w-64 h-64 bg-neon-cyan/5 rounded-full blur-[100px] transition-all duration-700 group-hover:bg-neon-cyan/10"></div>
                    
                    <div class="relative z-10 flex flex-col items-start gap-6 md:gap-10">
                        <!-- Flag & Location -->
                        <div class="flex items-center gap-8">
                            <div class="w-24 h-24 rounded-3xl bg-black/40 border border-white/10 flex items-center justify-center text-5xl shadow-2xl ring-1 ring-white/5 group-hover:scale-110 group-hover:rotate-3 transition-all duration-500" id="ip-flag">
                                <script>document.getElementById('ip-flag').innerText = getCountryFlag('<?= htmlspecialchars($ipInfo['country_code']) ?>');</script>
                            </div>
                            <div>
                                <h2 class="text-3xl font-black text-white tracking-tighter uppercase drop-shadow-sm"><?= htmlspecialchars($ip) ?></h2>
                                <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mt-2 flex items-center gap-3">
                                    <span class="text-white"><?= htmlspecialchars($ipInfo['city']) ?></span>
                                    <span class="opacity-20">/</span>
                                    <span><?= htmlspecialchars($ipInfo['region']) ?></span>
                                    <span class="opacity-20">/</span>
                                    <span class="text-neon-cyan"><?= htmlspecialchars($ipInfo['country']) ?></span>
                                </p>
                                <div class="mt-4 flex items-center gap-3 px-4 py-2 bg-black/40 rounded-xl border border-white/5 w-fit">
                                    <i class="ph-duotone ph-broadcast text-neon-cyan text-lg"></i>
                                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-500"><?= htmlspecialchars($ipInfo['isp']) ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Stats -->
                        <div class="flex flex-wrap gap-4 w-full md:w-auto md:ml-auto">
                            <div class="flex-1 min-w-[120px] bg-black/40 border border-white/10 rounded-3xl px-6 py-4 text-center shadow-inner group-hover:border-neon-cyan/30 transition-all">
                                <div class="text-2xl font-black text-white tracking-tighter"><?= $ipPageCount ?></div>
                                <div class="text-[8px] text-slate-600 font-extrabold uppercase tracking-[0.2em] mt-1">Total_Flux</div>
                            </div>
                            <div class="flex-1 min-w-[120px] bg-black/40 border border-white/10 rounded-3xl px-6 py-4 text-center shadow-inner group-hover:border-neon-purple/30 transition-all">
                                <div class="text-2xl font-black text-white tracking-tighter"><?= $ipUniquePages ?></div>
                                <div class="text-[8px] text-slate-600 font-extrabold uppercase tracking-[0.2em] mt-1">Unique_Nodes</div>
                            </div>
                            <div class="flex-1 min-w-[120px] bg-black/40 border border-white/10 rounded-3xl px-6 py-4 text-center shadow-inner group-hover:border-neon-amber/30 transition-all">
                                <?php if ($ipIsBot): ?>
                                    <div class="text-2xl filter drop-shadow-[0_0_8px_rgba(245,158,11,0.4)]">🤖</div>
                                    <div class="text-[8px] text-neon-amber font-extrabold uppercase tracking-[0.2em] mt-1">Neural_Bot</div>
                                <?php else: ?>
                                    <div class="text-2xl filter drop-shadow-[0_0_8px_rgba(16,185,129,0.4)]">👤</div>
                                    <div class="text-[8px] text-neon-emerald font-extrabold uppercase tracking-[0.2em] mt-1">Human_Ent</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- User Agent -->
                    <div class="mt-10 p-6 bg-black/40 border border-white/5 rounded-2xl relative overflow-hidden group/agent">
                        <div class="absolute inset-0 bg-gradient-to-r from-neon-cyan/5 to-transparent opacity-0 group-hover/agent:opacity-100 transition-opacity"></div>
                        <div class="text-[9px] font-black text-slate-600 uppercase tracking-[0.3em] mb-3 relative z-10 flex items-center gap-3">
                            <i class="ph-bold ph-browser text-neon-cyan"></i> Client Agent Signature
                        </div>
                        <code class="text-[10px] text-slate-400 font-mono font-bold break-all leading-relaxed relative z-10 block"><?= htmlspecialchars($ipInfo['user_agent']) ?></code>
                    </div>
                </div>

                <!-- Activity Timeline -->
                <div class="admin-table-wrapper backdrop-blur-2xl border border-white/5 rounded-[2.5rem] overflow-hidden shadow-premium mb-20">
                    <div class="p-10 border-b border-white/5 bg-white/[0.01]">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-neon-cyan/10 text-neon-cyan flex items-center justify-center text-xl shadow-lg border border-neon-cyan/20">
                                <i class="ph-duotone ph-path"></i>
                            </div>
                            <div class="flex flex-col">
                                <h2 class="text-[11px] font-black uppercase tracking-[0.3em] text-white m-0">Temporal Access Chain</h2>
                                <p class="text-[8px] text-slate-600 font-bold uppercase tracking-tight mt-1">Sequence of node interactions mapped by chronos-markers</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-10">
                        <div class="relative">
                            <!-- Timeline line -->
                            <div class="absolute left-[27px] top-0 bottom-0 w-px bg-white/5 border-l border-white/5"></div>

                            <?php 
                            $prevDate = '';
                            foreach($ipVisits as $visit): 
                                $visitDate = date('M d, Y', strtotime($visit['visited_at']));
                                $showDate = ($visitDate !== $prevDate);
                                $prevDate = $visitDate;
                            ?>
                                <?php if ($showDate): ?>
                                    <div class="relative flex items-center gap-6 mb-8 <?= $visit !== $ipVisits[0] ? 'mt-12' : '' ?>">
                                        <div class="w-14 h-8 rounded-xl bg-neon-cyan/10 border border-neon-cyan/20 flex items-center justify-center z-10 shadow-lg">
                                            <i class="ph-bold ph-calendar-blank text-neon-cyan text-xs"></i>
                                        </div>
                                        <span class="text-[10px] font-black text-neon-cyan uppercase tracking-[0.3em] shadow-sm"><?= $visitDate ?></span>
                                    </div>
                                <?php endif; ?>

                                <div class="relative flex items-start gap-6 mb-4 group/item">
                                    <!-- Timeline dot -->
                                    <div class="w-14 flex items-center justify-center z-10 pt-2.5">
                                        <div class="w-2.5 h-2.5 rounded-full bg-slate-900 border-2 border-slate-700 group-hover/item:bg-neon-cyan group-hover/item:border-neon-cyan group-hover/item:shadow-[0_0_10px_rgba(6,182,212,0.8)] transition-all duration-300"></div>
                                    </div>
                                    <!-- Content -->
                                    <div class="flex-1 bg-black/40 border border-white/5 rounded-2xl px-6 py-4 group-hover/item:border-neon-cyan/20 transition-all duration-300 shadow-inner group-hover/item:bg-white/[0.02]">
                                        <div class="flex items-center justify-between">
                                            <a href="<?= htmlspecialchars(BASE_URL . $visit['page_url']) ?>" target="_blank" class="text-white hover:text-neon-cyan font-black text-[11px] uppercase tracking-wider flex items-center gap-3 transition-colors group/link">
                                                <i class="ph-bold ph-link-simple opacity-40 group-hover/link:opacity-100"></i>
                                                <?= htmlspecialchars($visit['page_url'] == '/' ? '/ (Index_Nexus)' : $visit['page_url']) ?>
                                            </a>
                                            <span class="text-[9px] text-slate-600 font-black uppercase tracking-[0.2em] shrink-0 ml-6 flex items-center gap-2">
                                                <i class="ph ph-clock text-xs"></i>
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
