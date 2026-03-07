<?php
// Ensure this file is only accessed through the controller
if (!defined('APP_NAME')) die('Direct access prevented');
?>
<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <title>App Manager Dashboard — <?= APP_NAME ?></title>
    <?php require __DIR__ . '/partials/_head_assets.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<div class="admin-layout flex w-full h-screen overflow-hidden">
    <?php $currentPage = 'app-manager'; require __DIR__ . '/partials/sidebar.php'; ?>
    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-20 flex items-center justify-between px-8 bg-glass-bg border-b border-white/5 shrink-0 backdrop-blur-xl sticky top-0 z-[100]">
            <div class="absolute inset-0 bg-gradient-to-r from-violet-500/5 via-transparent to-transparent"></div>
            <div class="relative flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-violet-500/10 flex items-center justify-center border border-violet-500/20">
                    <i class="ph ph-squares-four text-2xl text-violet-500 animate-pulse"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white tracking-tight">App Ecosystem</h1>
                    <p class="text-[10px] text-white/40 uppercase tracking-widest font-black hidden sm:block">Telemetry & Licensing Dashboard</p>
                </div>
            </div>
            <div class="relative flex items-center gap-4">
                <?php require __DIR__ . '/partials/_topbar.php'; ?>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-8 crm-main-scroll bg-[#0b0e14]">
            
            <!-- Quick Stats Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
                <!-- Products -->
                <div class="p-6 rounded-2xl bg-white/[0.02] border border-white/5 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-cyan-500/5 rounded-full blur-2xl -mr-16 -mt-16 group-hover:bg-cyan-500/10 transition-colors"></div>
                    <div class="flex items-center justify-between mb-4 relative z-10">
                        <div class="w-10 h-10 rounded-xl bg-cyan-500/10 flex items-center justify-center border border-cyan-500/20">
                            <i class="ph ph-cube text-cyan-500 text-xl"></i>
                        </div>
                        <span class="text-xs font-bold text-white/40 uppercase tracking-widest">Products</span>
                    </div>
                    <h3 class="text-3xl font-black text-white font-mono relative z-10"><?= number_format($totalProducts) ?></h3>
                    <p class="text-xs text-white/40 mt-1 relative z-10">Total App Offerings</p>
                </div>

                <!-- Active Licenses -->
                <div class="p-6 rounded-2xl bg-white/[0.02] border border-white/5 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-orange-500/5 rounded-full blur-2xl -mr-16 -mt-16 group-hover:bg-orange-500/10 transition-colors"></div>
                    <div class="flex items-center justify-between mb-4 relative z-10">
                        <div class="w-10 h-10 rounded-xl bg-orange-500/10 flex items-center justify-center border border-orange-500/20">
                            <i class="ph ph-key text-orange-500 text-xl"></i>
                        </div>
                        <span class="text-xs font-bold text-white/40 uppercase tracking-widest">Active Keys</span>
                    </div>
                    <h3 class="text-3xl font-black text-white font-mono relative z-10"><?= number_format($activeLicenses) ?></h3>
                    <p class="text-[10px] text-white/40 mt-1 relative z-10">Out of <?= number_format($totalLicenses) ?> total licenses</p>
                </div>

                <!-- Online Devices -->
                <div class="p-6 rounded-2xl bg-white/[0.02] border border-white/5 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-500/5 rounded-full blur-2xl -mr-16 -mt-16 group-hover:bg-emerald-500/10 transition-colors"></div>
                    <div class="flex items-center justify-between mb-4 relative z-10">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center border border-emerald-500/20">
                            <i class="ph ph-desktop text-emerald-500 text-xl"></i>
                        </div>
                        <span class="text-xs font-bold text-white/40 uppercase tracking-widest">Online</span>
                    </div>
                    <h3 class="text-3xl font-black text-emerald-400 font-mono relative z-10"><?= number_format($onlineDevices) ?></h3>
                    <p class="text-[10px] text-white/40 mt-1 relative z-10">Nodes Connected Now</p>
                </div>

                <!-- Total Installs -->
                <div class="p-6 rounded-2xl bg-white/[0.02] border border-white/5 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-pink-500/5 rounded-full blur-2xl -mr-16 -mt-16 group-hover:bg-pink-500/10 transition-colors"></div>
                    <div class="flex items-center justify-between mb-4 relative z-10">
                        <div class="w-10 h-10 rounded-xl bg-pink-500/10 flex items-center justify-center border border-pink-500/20">
                            <i class="ph ph-download-simple text-pink-500 text-xl"></i>
                        </div>
                        <span class="text-xs font-bold text-white/40 uppercase tracking-widest">Installs</span>
                    </div>
                    <h3 class="text-3xl font-black text-white font-mono relative z-10"><?= number_format($totalInstalls) ?></h3>
                    <p class="text-[10px] text-white/40 mt-1 relative z-10">Total Validations Recorded</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Data Charts / Breakdown -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="p-6 rounded-2xl bg-white/[0.02] border border-white/5">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center gap-2">
                                <i class="ph ph-chart-pie-slice text-white/40 text-lg"></i>
                                <h3 class="text-sm font-bold text-white uppercase tracking-widest">Category Distribution</h3>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <?php foreach($categoryStats as $cs): ?>
                                <?php 
                                    $pct = $totalProducts > 0 ? round(($cs['product_count'] / $totalProducts) * 100) : 0;
                                ?>
                                <div class="p-4 rounded-xl bg-white/5 border border-white/5 hover:border-<?= $cs['color'] ?>-500/50 transition-colors group">
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="w-8 h-8 rounded-lg bg-<?= $cs['color'] ?>-500/10 flex items-center justify-center border border-<?= $cs['color'] ?>-500/20 text-<?= $cs['color'] ?>-500">
                                            <i class="ph <?= e($cs['icon']) ?> text-lg"></i>
                                        </div>
                                        <span class="text-xl font-black text-white font-mono group-hover:text-<?= $cs['color'] ?>-500 transition-colors"><?= $cs['product_count'] ?></span>
                                    </div>
                                    <p class="text-xs text-white/60 font-medium truncate mb-2"><?= e($cs['name']) ?></p>
                                    <div class="w-full h-1 bg-white/10 rounded-full overflow-hidden">
                                        <div class="h-full bg-<?= $cs['color'] ?>-500 rounded-full" style="width: <?= $pct ?>%"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <?php if(empty($categoryStats)): ?>
                                <div class="col-span-4 text-center py-8 text-white/40 text-sm">No category data available.</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="p-6 rounded-2xl bg-white/[0.02] border border-white/5">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center gap-2">
                                <i class="ph ph-pulse text-white/40 text-lg"></i>
                                <h3 class="text-sm font-bold text-white uppercase tracking-widest">License Status Map</h3>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-6 flex-wrap">
                            <?php 
                                $statusColors = ['active' => 'emerald', 'suspended' => 'orange', 'expired' => 'white', 'revoked' => 'pink'];
                                foreach($licenseStatusDist as $ls): 
                                    $col = $statusColors[$ls['status']] ?? 'white';
                            ?>
                                <div class="flex-1 min-w-[120px] p-4 rounded-xl bg-<?= $col ?>-500/5 border border-<?= $col ?>-500/10">
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="w-2 h-2 rounded-full bg-<?= $col ?>-500"></div>
                                        <span class="text-[10px] font-bold text-white/60 uppercase tracking-widest"><?= $ls['status'] ?></span>
                                    </div>
                                    <div class="text-2xl font-mono text-white font-bold"><?= number_format($ls['cnt']) ?></div>
                                </div>
                            <?php endforeach; ?>
                            <?php if(empty($licenseStatusDist)): ?>
                                <div class="w-full text-center py-4 text-white/40 text-sm">No license status data available.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Recent Telemetry -->
                <div class="p-6 rounded-2xl bg-white/[0.02] border border-white/5 flex flex-col h-[600px]">
                    <div class="flex items-center justify-between mb-6 shrink-0">
                        <div class="flex items-center gap-2">
                            <i class="ph ph-activity text-white/40 text-lg"></i>
                            <h3 class="text-sm font-bold text-white uppercase tracking-widest">System Feed</h3>
                        </div>
                        <a href="<?= baseUrl('admin/app-devices') ?>" class="text-[10px] text-violet-400 hover:text-violet-300 uppercase tracking-widest font-bold">View Devices</a>
                    </div>
                    
                    <div class="flex-1 overflow-y-auto pr-2 crm-main-scroll space-y-4">
                        <?php if (empty($recentConnections)): ?>
                            <div class="text-center py-8 text-white/40 text-sm border border-dashed border-white/10 rounded-xl">No recent telemetry.</div>
                        <?php else: ?>
                            <?php foreach ($recentConnections as $conn): ?>
                                <?php 
                                    $isConnect = $conn['event_type'] === 'connect';
                                    $isDownload = $conn['event_type'] === 'download';
                                    $bCol = $isConnect ? 'emerald' : ($isDownload ? 'pink' : 'orange');
                                    $icon = $isConnect ? 'ph-pulse' : ($isDownload ? 'ph-download-simple' : 'ph-activity');
                                ?>
                                <div class="p-4 rounded-xl bg-white/5 border border-white/5 hover:border-<?= $bCol ?>-500/30 transition-colors">
                                    <div class="flex justify-between items-start mb-3">
                                        <div class="flex items-center gap-2">
                                            <div class="w-2 h-2 rounded-full bg-<?= $bCol ?>-500 <?= $isConnect ? 'animate-pulse' : '' ?>"></div>
                                            <span class="text-xs font-bold text-white uppercase tracking-widest"><?= e($conn['event_type']) ?></span>
                                        </div>
                                        <span class="text-[10px] font-mono text-white/40"><?= date('H:i:s M j', strtotime($conn['created_at'])) ?></span>
                                    </div>
                                    
                                    <div class="space-y-1">
                                        <?php if($isDownload): ?>
                                            <div class="text-sm text-white font-medium"><?= e($conn['details']) ?></div>
                                            <div class="text-[10px] text-pink-400 font-bold uppercase tracking-widest mt-1">Store Event</div>
                                        <?php else: ?>
                                            <div class="text-sm text-white font-medium">Node: <?= !empty($conn['hostname']) ? e($conn['hostname']) : e($conn['ip_address']) ?></div>
                                            <div class="text-xs text-white/30 font-mono flex items-center gap-2">
                                                <i class="ph ph-key text-[10px]"></i> <?= e($conn['license_key'] ?? 'No Key') ?>
                                            </div>
                                            <div class="text-[10px] text-violet-400 font-bold uppercase tracking-widest mt-1">
                                                <?= e($conn['product_name'] ?? 'Unknown App') ?> <span class="text-white/40 lowercase ml-1">v<?= e($conn['app_version'] ?? '?.?') ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>
</body>
</html>
