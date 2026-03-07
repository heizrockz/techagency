<?php
// Ensure this file is only accessed through the controller
if (!defined('APP_NAME')) die('Direct access prevented');
?>
<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <title>App Devices — <?= APP_NAME ?></title>
    <?php require __DIR__ . '/partials/_head_assets.php'; ?>
    <script>
        function toggleLogs(deviceId) {
            const logsRow = document.getElementById('logs-' + deviceId);
            const icon = document.getElementById('icon-' + deviceId);
            if (logsRow.classList.contains('hidden')) {
                logsRow.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
            } else {
                logsRow.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        }
    </script>
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<div class="admin-layout flex w-full h-screen overflow-hidden">
    <?php $currentPage = 'app-devices'; require __DIR__ . '/partials/sidebar.php'; ?>
    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-20 flex items-center justify-between px-8 bg-glass-bg border-b border-white/5 shrink-0 backdrop-blur-xl sticky top-0 z-[100]">
            <div class="absolute inset-0 bg-gradient-to-r from-emerald-500/5 via-transparent to-transparent"></div>
            <div class="relative flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center border border-emerald-500/20">
                    <i class="ph ph-desktop text-2xl text-emerald-500 animate-pulse"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white tracking-tight">App Devices</h1>
                    <p class="text-[10px] text-white/40 uppercase tracking-widest font-black hidden sm:block">Hardware Telemetry</p>
                </div>
            </div>
            <div class="relative flex items-center gap-4">
                <?php require __DIR__ . '/partials/_topbar.php'; ?>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-8 crm-main-scroll bg-[#0b0e14]">
            <?php if ($flash = getFlash()): ?>
                <div class="mb-8 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl flex items-center gap-3 animate-in fade-in slide-in-from-top-4">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/20 flex items-center justify-center border border-emerald-500/20">
                        <i class="ph ph-check-circle text-emerald-500"></i>
                    </div>
                    <p class="text-emerald-500 font-medium"><?= e($flash) ?></p>
                </div>
            <?php endif; ?>

            <!-- Filters -->
            <div class="mb-6 flex flex-col sm:flex-row gap-4 items-center justify-between">
                <form method="GET" action="<?= baseUrl('admin/app-devices') ?>" class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                    <div class="relative w-full sm:w-64">
                        <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-white/40"></i>
                        <input type="text" name="search" value="<?= e($_GET['search'] ?? '') ?>" placeholder="Search ID, IP, or Hostname..." class="form-input !pl-10 !py-2 w-full text-sm">
                    </div>
                    
                    <select name="product" class="form-input !py-2 text-sm w-full sm:w-auto">
                        <option value="">All Products</option>
                        <?php foreach ($allProducts as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= (isset($_GET['product']) && $_GET['product'] == $p['id']) ? 'selected' : '' ?>><?= e($p['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    
                    <select name="license" class="form-input !py-2 text-sm w-full sm:w-auto">
                        <option value="">All Licenses</option>
                        <?php foreach ($allLicenses as $l): ?>
                            <option value="<?= $l['id'] ?>" <?= (isset($_GET['license']) && $_GET['license'] == $l['id']) ? 'selected' : '' ?>><?= e($l['license_key']) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <select name="online" class="form-input !py-2 text-sm w-full sm:w-auto">
                        <option value="">Status: All</option>
                        <option value="1" <?= (isset($_GET['online']) && $_GET['online'] === '1') ? 'selected' : '' ?>>Online Only</option>
                        <option value="0" <?= (isset($_GET['online']) && $_GET['online'] === '0') ? 'selected' : '' ?>>Offline Only</option>
                    </select>
                    
                    <button type="submit" class="px-4 py-2 bg-white/5 text-white/60 hover:text-white hover:bg-white/10 rounded-lg border border-white/10 transition-colors text-sm font-semibold">
                        Filter
                    </button>
                    
                    <?php if(!empty($_GET['search']) || !empty($_GET['product']) || !empty($_GET['license']) || isset($_GET['online'])): ?>
                        <a href="<?= baseUrl('admin/app-devices') ?>" class="px-4 py-2 text-pink-500 hover:text-pink-400 text-sm font-semibold transition-colors">Clear</a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="admin-card p-0 overflow-hidden border-white/5">
                <div class="p-6 border-b border-white/5 flex items-center justify-between bg-white/[0.01]">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center border border-emerald-500/20">
                            <i class="ph ph-hard-drives text-emerald-500 text-xl"></i>
                        </div>
                        <h3 class="font-bold text-white tracking-tight text-lg">Connected Endpoints</h3>
                    </div>
                    <div class="bg-white/5 px-4 py-1.5 rounded-full border border-white/10">
                        <span class="text-[10px] text-white/40 uppercase tracking-widest font-bold">Total Devices:</span>
                        <span class="text-sm font-mono text-emerald-500 font-bold ml-2"><?= count($devices) ?></span>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="admin-table min-w-[1000px] w-full border-collapse">
                        <thead>
                            <tr>
                                <th class="!pl-6 w-10 text-center"></th>
                                <th>Hostname / IP</th>
                                <th>Hardware ID</th>
                                <th>License Link</th>
                                <th>Telemetry Activity</th>
                                <th class="text-center">Connection</th>
                                <th class="!pr-8 text-right">Operations</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            <?php if (empty($devices)): ?>
                                <tr><td colspan="7" class="text-center py-8 text-white/40">No devices found.</td></tr>
                            <?php else: ?>
                                <?php foreach ($devices as $d): ?>
                                    <tr class="group hover:bg-white/[0.02] transition-colors duration-300">
                                        <td class="!pl-6 text-center">
                                            <button onclick="toggleLogs(<?= $d['id'] ?>)" class="w-8 h-8 rounded flex items-center justify-center text-white/40 hover:bg-white/5 hover:text-white transition-all">
                                                <i id="icon-<?= $d['id'] ?>" class="ph ph-caret-down transition-transform duration-300"></i>
                                            </button>
                                        </td>
                                        <td>
                                            <div class="flex flex-col">
                                                <div class="flex items-center gap-2">
                                                    <?php if($d['is_online']): ?>
                                                        <div class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.8)] animate-pulse"></div>
                                                    <?php else: ?>
                                                        <div class="w-2 h-2 rounded-full bg-white/20"></div>
                                                    <?php endif; ?>
                                                    <span class="text-sm font-bold text-white"><?= !empty($d['hostname']) ? e($d['hostname']) : 'Unknown-Host' ?></span>
                                                </div>
                                                <span class="text-xs font-mono text-white/40 mt-0.5 ml-4"><?= e($d['ip_address'] ?: '0.0.0.0') ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="flex flex-col">
                                                <span class="text-xs font-mono text-white/80" title="<?= e($d['os_info']) ?>"><?= e($d['hardware_id']) ?></span>
                                                <span class="text-[10px] text-emerald-500/70 uppercase font-mono tracking-widest mt-0.5"><?= e($d['os_info'] ?: 'Unknown OS') ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="flex flex-col">
                                                <a href="<?= baseUrl('admin/app-licenses?search='.urlencode($d['license_key'])) ?>" class="text-xs font-mono text-white/80 hover:text-orange-400 transition-colors">
                                                    <?= e($d['license_key']) ?>
                                                </a>
                                                <span class="text-[10px] text-white/40 uppercase font-bold tracking-widest mt-0.5"><?= e($d['product_name']) ?> v<?= e($d['app_version']) ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="flex flex-col">
                                                <span class="text-xs text-white/60">Heartbeat: <span class="text-white"><?= !empty($d['last_heartbeat']) ? date('M j, H:i:s', strtotime($d['last_heartbeat'])) : 'Never' ?></span></span>
                                                <span class="text-xs text-white/40 mt-0.5">First Seen: <span class="font-mono text-white/30"><?= date('M j, Y', strtotime($d['first_seen'])) ?></span></span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <?php if($d['is_online']): ?>
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-500 text-[10px] font-bold uppercase tracking-widest border border-emerald-500/20">
                                                    Online
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-500/10 text-slate-400 text-[10px] font-bold uppercase tracking-widest border border-slate-500/20">
                                                    Offline
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="!pr-8 text-right">
                                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <?php if($d['is_online']): ?>
                                                    <a href="<?= baseUrl('admin/app-devices?action=disconnect&id='.$d['id']) ?>" class="w-8 h-8 rounded-lg bg-orange-500/10 flex items-center justify-center text-orange-500 hover:bg-orange-500 hover:text-black transition-all" title="Force Disconnect">
                                                        <i class="ph ph-plugs"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <button onclick="showDeleteModal('<?= e($d['hostname'] ?: $d['hardware_id']) ?>', '<?= baseUrl('admin/app-devices?action=delete&id='.$d['id']) ?>')" class="w-8 h-8 rounded-lg bg-pink-500/10 flex items-center justify-center text-pink-500 hover:bg-pink-500 hover:text-white transition-all" title="Remove Device">
                                                    <i class="ph ph-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    
                                    <!-- Logs Drawer -->
                                    <tr id="logs-<?= $d['id'] ?>" class="hidden bg-black/20">
                                        <td colspan="7" class="px-8 flex-col pb-4 !border-white/5 py-4">
                                            <div class="border border-white/10 rounded-xl bg-[#0b0e14] overflow-hidden">
                                                <div class="px-4 py-2 bg-white/5 border-b border-white/10 flex items-center gap-2">
                                                    <i class="ph ph-list-dashes text-white/40 text-sm"></i>
                                                    <span class="text-xs font-bold text-white/60 uppercase tracking-widest">Recent Telemetry Logs</span>
                                                </div>
                                                <ul class="divide-y divide-white/5">
                                                    <?php $logs = $deviceLogs[$d['id']] ?? []; ?>
                                                    <?php if(empty($logs)): ?>
                                                        <li class="p-4 text-xs text-white/30 text-center font-mono">No logs available for this device.</li>
                                                    <?php else: ?>
                                                        <?php foreach($logs as $log): ?>
                                                            <?php 
                                                                $logCol = 'white';
                                                                if($log['event_type'] === 'connect') $logCol = 'emerald';
                                                                if($log['event_type'] === 'disconnect') $logCol = 'orange';
                                                                if($log['event_type'] === 'error') $logCol = 'pink';
                                                            ?>
                                                            <li class="px-4 py-2.5 flex items-start gap-4 hover:bg-white/[0.02] transition-colors">
                                                                <span class="text-xs font-mono text-white/40 shrink-0 w-[140px]"><?= date('M j, H:i:s', strtotime($log['created_at'])) ?></span>
                                                                <span class="text-[10px] uppercase font-bold text-<?= $logCol ?>-500 tracking-widest shrink-0 w-[90px]"><?= e($log['event_type']) ?></span>
                                                                <span class="text-xs text-white/70 font-mono"><?= !empty($log['details']) ? e($log['details']) : '—' ?></span>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

<?php require __DIR__ . '/partials/_delete_modal.php'; ?>
</body>
</html>
