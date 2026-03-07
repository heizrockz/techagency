<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <title><?= t('admin_dashboard') ?> — <?= APP_NAME ?></title>
    <?php require __DIR__ . '/partials/_head_assets.php'; ?>
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">

<div class="admin-layout flex w-full h-screen overflow-hidden">
    <!-- Sidebar -->
    <?php $currentPage = 'dashboard'; require __DIR__ . '/partials/sidebar.php'; ?>

    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-20 flex items-center justify-between px-8 bg-glass-bg border-b border-white/5 shrink-0 backdrop-blur-xl sticky top-0 z-[100]">
            <div class="flex flex-col">
                <div class="text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] mb-1 hidden sm:block">Command Center</div>
                <h1 class="text-xl font-black text-white tracking-tight flex items-center gap-3 group">
                    <span class="text-neon-cyan drop-shadow-[0_0_8px_rgba(6,182,212,0.4)]"><?= t('admin_welcome') ?></span>
                    <span class="opacity-20 translate-y-px hidden sm:inline">/</span>
                    <span class="text-sm tracking-widest text-slate-400 uppercase font-black hidden sm:inline-block"><?= e(getAdminUser()) ?></span>
                </h1>
            </div>
            <?php require __DIR__ . '/partials/_topbar.php'; ?>
        </header>

        <!-- Futuristic Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 p-8">
            <!-- Total Traffic Card -->
            <a href="<?= baseUrl('/admin/visitors') ?>" class="admin-stat-card !bg-glass-bg border border-white/5 p-8 rounded-3xl relative overflow-hidden group hover:border-neon-cyan/40 transition-all hover:-translate-y-1 shadow-premium">
                <div class="absolute top-0 right-0 w-32 h-32 bg-neon-cyan/5 blur-[60px] rounded-full -mr-16 -mt-16 group-hover:bg-neon-cyan/10 transition-colors"></div>
                <div class="flex items-center justify-between mb-6">
                    <div class="w-14 h-14 rounded-2xl bg-neon-cyan/10 flex items-center justify-center border border-neon-cyan/20 group-hover:scale-110 transition-transform">
                        <i class="ph-bold ph-chart-line-up text-3xl text-neon-cyan"></i>
                    </div>
                    <div class="text-right">
                        <div class="text-[9px] font-black text-slate-600 uppercase tracking-[0.2em] mb-1">Global Reach</div>
                        <div class="text-3xl font-black text-white tracking-tighter"><?= number_format((int)$visitCount) ?></div>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-500 group-hover:text-neon-cyan transition-colors">Digital Pulse Analytics</span>
                    <i class="ph ph-arrow-right text-neon-cyan opacity-0 group-hover:opacity-100 transition-all translate-x-2 group-hover:translate-x-0"></i>
                </div>
            </a>

            <!-- Total Bookers -->
            <div class="admin-stat-card !bg-glass-bg border border-white/5 p-8 rounded-3xl relative overflow-hidden group hover:border-neon-emerald/40 transition-all hover:-translate-y-1 shadow-premium">
                <div class="absolute top-0 right-0 w-32 h-32 bg-neon-emerald/5 blur-[60px] rounded-full -mr-16 -mt-16 group-hover:bg-neon-emerald/10 transition-colors"></div>
                <div class="flex items-center justify-between mb-6">
                    <div class="w-14 h-14 rounded-2xl bg-neon-emerald/10 flex items-center justify-center border border-neon-emerald/20 group-hover:scale-110 transition-transform">
                        <i class="ph-bold ph-calendar-check text-3xl text-neon-emerald"></i>
                    </div>
                    <div class="text-right">
                        <div class="text-[9px] font-black text-slate-600 uppercase tracking-[0.2em] mb-1">Registry Vol.</div>
                        <div class="text-3xl font-black text-white tracking-tighter"><?= (int)$totalBookings ?></div>
                    </div>
                </div>
                <div class="flex items-center">
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">Confirmed Engagements</span>
                </div>
            </div>

            <!-- Active Alerts -->
            <div class="admin-stat-card !bg-glass-bg border border-white/5 p-8 rounded-3xl relative overflow-hidden group hover:border-neon-rose/40 transition-all hover:-translate-y-1 shadow-premium">
                <div class="absolute top-0 right-0 w-32 h-32 bg-neon-rose/5 blur-[60px] rounded-full -mr-16 -mt-16 group-hover:bg-neon-rose/10 transition-colors"></div>
                <div class="flex items-center justify-between mb-6">
                    <div class="w-14 h-14 rounded-2xl bg-neon-rose/10 flex items-center justify-center border border-neon-rose/20 group-hover:scale-110 transition-transform">
                        <i class="ph-bold ph-bell-ringing text-3xl text-neon-rose"></i>
                    </div>
                    <div class="text-right">
                        <div class="text-[9px] font-black text-slate-600 uppercase tracking-[0.2em] mb-1">Urgent Signal</div>
                        <div class="text-3xl font-black text-white tracking-tighter"><?= (int)$newBookings ?></div>
                    </div>
                </div>
                <div class="flex items-center">
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">Awaiting Verification</span>
                </div>
            </div>
        </div>

        <!-- Traffic Analytics Graph -->
        <div class="px-8 pb-8">
            <div class="admin-table-wrapper backdrop-blur-xl border border-white/5 rounded-3xl overflow-hidden shadow-premium p-6">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-[11px] font-black text-white uppercase tracking-[0.3em] flex items-center gap-3">
                        <i class="ph ph-chart-line-up text-neon-emerald"></i>
                        Traffic Analytics Pipeline
                    </h2>
                    <a href="<?= baseUrl('/admin/visitors') ?>" class="text-[9px] font-black text-slate-500 hover:text-neon-cyan uppercase tracking-widest transition-all">
                        Advanced Options →
                    </a>
                </div>
                <div id="trafficChart" class="w-full h-80 -ml-2"></div>
            </div>
        </div>

        <!-- Recent Registrations Activity -->
        <div class="px-8 pb-8">
            <div class="admin-table-wrapper backdrop-blur-xl border border-white/5 rounded-3xl overflow-hidden shadow-premium flex flex-col">
                <div class="px-8 py-6 border-b border-white/5 bg-white/[0.01] flex items-center justify-between">
                    <h2 class="text-[11px] font-black text-white uppercase tracking-[0.3em] flex items-center gap-3">
                        <i class="ph ph-broadcast text-neon-cyan animate-pulse"></i>
                        Live Engagement Registry
                    </h2>
                    <a href="<?= baseUrl('/admin/bookings') ?>" class="text-[9px] font-black text-slate-500 hover:text-neon-cyan uppercase tracking-widest transition-all">
                        Full Data Sequence →
                    </a>
                </div>

                <?php if (empty($recentBookings)): ?>
                    <div class="p-12 text-center">
                        <div class="text-slate-700 text-[10px] font-black uppercase tracking-widest">
                            No Transmission Detected Yet.
                        </div>
                    </div>
                <?php else: ?>
                    <table class="admin-table w-full text-left border-collapse">
                        <thead>
                            <tr class="text-slate-600 text-[8px] font-black uppercase tracking-[0.4em] bg-white/[0.01]">
                                <th class="py-5 px-8 w-32">Vector ID</th>
                                <th class="py-5 px-6">Identity</th>
                                <th class="py-5 px-6">Assigned Sector</th>
                                <th class="py-5 px-6 text-center">Protocol Status</th>
                                <th class="py-5 px-8 text-right font-mono tracking-normal">Temporal Record</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/[0.02]">
                            <?php foreach ($recentBookings as $b): ?>
                            <tr class="hover:bg-white/[0.03] transition-all group/row border-b border-white/[0.03] last:border-0 relative">
                                <td class="py-5 px-8 relative">
                                    <div class="flex items-center gap-2 group-hover/row:translate-x-1 transition-transform">
                                        <div class="w-1.5 h-1.5 rounded-full bg-neon-cyan/40 group-hover/row:bg-neon-cyan group-hover/row:animate-pulse transition-all"></div>
                                        <span class="text-[9px] font-black font-mono text-slate-700 tracking-tighter uppercase whitespace-nowrap">REC_<?= str_pad($b['id'], 5, '0', STR_PAD_LEFT) ?></span>
                                    </div>
                                </td>
                                <td class="py-5 px-6">
                                    <div class="flex flex-col">
                                        <span class="text-[11px] font-black text-white uppercase tracking-[0.1em] group-hover/row:text-neon-cyan transition-colors"><?= e($b['name']) ?></span>
                                        <span class="text-[7px] text-slate-800 font-bold tracking-[0.2em] uppercase">Verified Entity</span>
                                    </div>
                                </td>
                                <td class="py-5 px-6">
                                    <span class="text-[10px] text-slate-500 font-black uppercase tracking-widest bg-white/5 px-2.5 py-1 rounded-lg border border-white/5"><?= e($b['service']) ?></span>
                                </td>
                                <td class="py-5 px-6 text-center">
                                    <?php 
                                        $meta = match($b['status']) {
                                            'pending' => ['class' => 'text-neon-amber bg-neon-amber/10 border-neon-amber/20', 'label' => 'AWAITING_SIGNAL'],
                                            'confirmed' => ['class' => 'text-neon-emerald bg-neon-emerald/10 border-neon-emerald/20', 'label' => 'ACTIVE_SYNC'],
                                            'cancelled' => ['class' => 'text-neon-rose bg-neon-rose/10 border-neon-rose/20', 'label' => 'TERMINATED'],
                                            default => ['class' => 'text-slate-600 bg-white/10 border-white/20', 'label' => 'UNKNOWN']
                                        };
                                    ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg border <?= $meta['class'] ?> text-[8px] font-black uppercase tracking-[0.2em]">
                                        <?= $meta['label'] ?>
                                    </span>
                                </td>
                                <td class="py-5 px-8 text-right">
                                    <div class="flex flex-col items-end">
                                        <span class="text-[10px] text-white font-black font-mono tracking-tighter truncate"><?= date('H:i • d.m.y', strtotime($b['created_at'])) ?></span>
                                        <span class="text-[7px] text-slate-800 font-bold tracking-[0.3em] uppercase mt-0.5">Transmission Sync</span>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var options = {
        series: [{
            name: 'Unique Visitors',
            data: <?= json_encode($trafficData ?? []) ?>
        }],
        chart: {
            type: 'area',
            height: 320,
            fontFamily: 'Inter, sans-serif',
            toolbar: { show: false },
            background: 'transparent',
            parentHeightOffset: 0
        },
        colors: ['#10b981'],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.4,
                opacityTo: 0.05,
                stops: [0, 100]
            }
        },
        dataLabels: { enabled: false },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        xaxis: {
            categories: <?= json_encode($trafficLabels ?? []) ?>,
            axisBorder: { show: false },
            axisTicks: { show: false },
            labels: {
                style: { colors: '#64748b', fontSize: '9px', fontWeight: 600, cssClass: 'uppercase tracking-widest' },
                offsetY: 5
            }
        },
        yaxis: {
            labels: {
                style: { colors: '#64748b', fontSize: '10px', fontWeight: 600 },
                formatter: (value) => { return Math.floor(value) }
            }
        },
        grid: {
            borderColor: 'rgba(255,255,255,0.05)',
            strokeDashArray: 4,
            yaxis: { lines: { show: true } },
            padding: { top: 0, right: 0, bottom: 0, left: 10 }
        },
        theme: { mode: 'dark' },
        tooltip: {
            theme: 'dark',
            x: { show: true },
            y: { formatter: function (val) { return val + " visits" } }
        }
    };

    var chart = new ApexCharts(document.querySelector("#trafficChart"), options);
    chart.render();
});
</script>
</body>
</html>
