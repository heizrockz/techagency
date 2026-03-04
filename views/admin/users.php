<?php
$pageTitle = 'User Management';
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
                <div class="text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] mb-1 hidden sm:block">Personnel Oversight</div>
                <h1 class="text-xl font-black text-white tracking-tight flex items-center gap-3 group">
                    <span class="text-neon-cyan drop-shadow-[0_0_8px_rgba(6,182,212,0.4)]">Collective Intelligence</span>
                    <span class="opacity-20 translate-y-px hidden sm:inline">/</span>
                    <span class="text-sm tracking-widest text-slate-400 uppercase font-black hidden sm:inline">Identities</span>
                </h1>
            </div>
            <div class="flex items-center gap-6">
                <button onclick="document.getElementById('createUserModal').classList.remove('hidden')" class="px-4 sm:px-6 py-2.5 bg-neon-cyan hover:bg-cyan-400 text-black text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-lg active:scale-95 flex items-center gap-2">
                    <i class="ph-bold ph-user-plus text-lg"></i> <span class="hidden sm:inline">Provision New Identity</span>
                </button>
                <?php require __DIR__ . '/partials/_topbar.php'; ?>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-6 crm-main-scroll bg-[#0b0e14]">
            <div class="max-w-7xl mx-auto space-y-6">
                <!-- Flash Messages -->
                <?php if ($flash = getFlash()): ?>
                    <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-xl flex items-center gap-3">
                        <i class="ph ph-check-circle text-xl"></i>
                        <?= e($flash) ?>
                    </div>
                <?php endif; ?>
                
                <!-- Matrix Control Center -->
                <div class="flex items-center justify-between bg-glass-bg backdrop-blur-2xl p-6 rounded-[2rem] border border-white/5 shadow-premium mt-4">
                    <div class="flex items-center gap-3 bg-black/40 p-1.5 rounded-2xl border border-white/5 shadow-inner">
                        <button onclick="switchView('card')" id="view-card-btn" class="flex items-center gap-2 px-6 py-2.5 rounded-xl transition-all duration-300 text-[10px] font-black uppercase tracking-widest text-neon-cyan bg-neon-cyan/10 border border-neon-cyan/20 shadow-[0_0_20px_rgba(6,182,212,0.1)]">
                            <i class="ph-bold ph-squares-four text-sm"></i> Identity Matrix
                        </button>
                        <button onclick="switchView('list')" id="view-list-btn" class="flex items-center gap-2 px-6 py-2.5 rounded-xl transition-all duration-300 text-[10px] font-black uppercase tracking-widest text-slate-600 hover:text-white hover:bg-white/5 border border-transparent">
                            <i class="ph-bold ph-list text-sm"></i> Data Sequence
                        </button>
                    </div>
                    <div class="px-8 py-3 bg-white/5 rounded-2xl border border-white/5 flex items-center gap-4 group hover:border-neon-cyan/30 transition-all">
                        <div class="flex -space-x-3">
                            <?php foreach(array_slice($admins, 0, 3) as $a): ?>
                                <div class="w-8 h-8 rounded-full bg-black border-2 border-[#0b0e14] flex items-center justify-center text-xs shadow-lg ring-1 ring-white/10"><?= e($a['avatar_emoji'] ?? '👤') ?></div>
                            <?php endforeach; ?>
                        </div>
                        <span class="text-[10px] text-slate-500 font-extrabold uppercase tracking-[0.2em] flex items-center gap-3">
                            <i class="ph ph-cpu text-neon-cyan animate-pulse"></i>
                            Personnel Pool: <span class="text-white drop-shadow-[0_0_8px_rgba(255,255,255,0.3)]"><?= count($admins) ?></span>
                        </span>
                    </div>
                </div>

                <!-- Matrix Grid -->
                <div id="users-card-view" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php foreach ($admins as $admin): ?>
                        <div class="admin-stat-card !bg-glass-bg !p-10 flex flex-col h-full relative group hover:shadow-premium transition-all duration-500 border border-white/5 rounded-[2.5rem] !overflow-visible">
                            <!-- Background Atmosphere -->
                            <div class="absolute -right-16 -top-16 w-48 h-48 bg-neon-cyan/5 rounded-full blur-[80px] group-hover:bg-neon-cyan/10 transition-all duration-1000"></div>
                            
                            <div class="flex items-start justify-between mb-8 relative z-20">
                                <div class="flex items-center gap-6">
                                    <div class="w-20 h-20 rounded-3xl bg-black/40 border border-white/10 flex items-center justify-center text-4xl shadow-2xl ring-1 ring-white/5 group-hover:scale-110 group-hover:border-neon-cyan/40 transition-all duration-500 group-hover:rotate-3">
                                        <?= e($admin['avatar_emoji'] ?? '👤') ?>
                                    </div>
                                    <div class="flex flex-col">
                                        <h3 class="text-white font-black text-xl tracking-tight flex items-center gap-3">
                                            <?= e($admin['full_name'] ?: $admin['username']) ?>
                                            <?php if($admin['id'] === $_SESSION['admin_id']): ?>
                                                <div class="w-2 h-2 rounded-full bg-neon-cyan shadow-[0_0_8px_rgba(6,182,212,1)] animate-pulse"></div>
                                            <?php endif; ?>
                                        </h3>
                                        <div class="flex flex-wrap items-center gap-2.5 mt-2">
                                            <p class="text-slate-600 text-[10px] font-black uppercase tracking-widest font-mono"><?= e($admin['username']) ?></p>
                                            <?php if(($admin['is_salesperson'] ?? 0)): ?>
                                                <div class="flex items-center gap-1.5 px-3 py-1 bg-neon-emerald/10 border border-neon-emerald/20 rounded-lg">
                                                    <i class="ph-bold ph-lightning text-[10px] text-neon-emerald"></i>
                                                    <span class="text-[8px] text-neon-emerald font-black uppercase tracking-widest">Sales Core</span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="dropdown relative static-on-mobile">
                                    <button type="button" class="dropdown-trigger w-12 h-12 flex items-center justify-center text-slate-500 hover:text-white rounded-2xl hover:bg-white/5 border border-transparent hover:border-white/10 transition-all active:scale-90 relative z-[30]" onclick="toggleDropdown(this)">
                                        <i class="ph-bold ph-dots-three-vertical text-2xl"></i>
                                    </button>
                                    <div class="dropdown-menu hidden absolute right-0 mt-2 w-64 bg-slate-900/95 border border-white/10 rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.5)] overflow-hidden z-[999] backdrop-blur-3xl animate-scale-in">
                                        <?php if($admin['id'] !== 1 && $admin['id'] !== $_SESSION['admin_id']): ?>
                                            <div class="px-6 py-5 border-b border-white/5 bg-white/[0.01]">
                                                <p class="text-[9px] text-slate-600 font-bold uppercase tracking-[0.3em] mb-4">Override Permission</p>
                                                <form method="POST" action="<?= baseUrl('admin/users') ?>">
                                                    <input type="hidden" name="action" value="update_role">
                                                    <input type="hidden" name="id" value="<?= $admin['id'] ?>">
                                                    <div class="relative">
                                                        <select name="role" onchange="this.form.submit()" class="w-full bg-black/60 border border-white/10 rounded-xl text-[10px] font-black uppercase tracking-widest text-white px-5 py-3 focus:outline-none focus:border-neon-cyan cursor-pointer transition-all appearance-none">
                                                            <option value="standard" <?= $admin['role'] === 'standard' ? 'selected' : '' ?>>Tactical Admin</option>
                                                            <option value="super_admin" <?= $admin['role'] === 'super_admin' ? 'selected' : '' ?>>Strategic Overseer</option>
                                                        </select>
                                                        <i class="ph ph-caret-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-500 pointer-events-none"></i>
                                                    </div>
                                                </form>
                                            </div>
                                            
                                            <form method="POST" action="<?= baseUrl('admin/users') ?>" class="p-2 border-b border-white/5">
                                                <input type="hidden" name="action" value="toggle_ip_filter">
                                                <input type="hidden" name="id" value="<?= $admin['id'] ?>">
                                                <input type="hidden" name="ip_filter_enabled" value="<?= ($admin['ip_filter_enabled'] ?? 0) ?>">
                                                <button type="submit" class="w-full text-left px-4 py-4 text-[10px] font-black uppercase tracking-widest <?= ($admin['ip_filter_enabled'] ?? 0) ? 'text-neon-rose hover:bg-neon-rose/5' : 'text-neon-cyan hover:bg-neon-cyan/5' ?> rounded-xl transition-all flex items-center gap-4 active:scale-95 group/btn">
                                                    <i class="ph-bold <?= ($admin['ip_filter_enabled'] ?? 0) ? 'ph-lock-open' : 'ph-lock' ?> text-xl opacity-70 group-hover/btn:scale-110 transition-transform"></i>
                                                    <span><?= ($admin['ip_filter_enabled'] ?? 0) ? 'Unlock Security' : 'Lock Security (IP)' ?></span>
                                                </button>
                                            </form>

                                            <form method="POST" action="<?= baseUrl('admin/users') ?>" class="p-2 border-b border-white/5">
                                                <input type="hidden" name="action" value="toggle_salesperson">
                                                <input type="hidden" name="id" value="<?= $admin['id'] ?>">
                                                <input type="hidden" name="is_salesperson" value="<?= ($admin['is_salesperson'] ?? 0) ?>">
                                                <button type="submit" class="w-full text-left px-4 py-4 text-[10px] font-black uppercase tracking-widest <?= ($admin['is_salesperson'] ?? 0) ? 'text-neon-amber hover:bg-neon-amber/5' : 'text-neon-emerald hover:bg-neon-emerald/5' ?> rounded-xl transition-all flex items-center gap-4 active:scale-95 group/btn">
                                                    <i class="ph-bold ph-handshake text-xl opacity-70 group-hover/btn:scale-110 transition-transform"></i>
                                                    <span><?= ($admin['is_salesperson'] ?? 0) ? 'Decommission Sales' : 'Commission Sales' ?></span>
                                                </button>
                                            </form>
                                            
                                            <form method="POST" action="<?= baseUrl('admin/users') ?>" class="p-2" onsubmit="return confirm('CRITICAL: Permanent deletion?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= $admin['id'] ?>">
                                                <button type="submit" class="w-full text-left px-4 py-4 text-[10px] font-black uppercase tracking-widest text-neon-rose hover:bg-neon-rose/5 rounded-xl transition-all flex items-center gap-4 active:scale-95 group/btn">
                                                    <i class="ph-bold ph-trash text-xl opacity-70 group-hover/btn:rotate-12 transition-transform"></i>
                                                    <span>Purge Identity Node</span>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <div class="p-8 text-center space-y-4">
                                                <div class="w-16 h-16 rounded-full bg-neon-cyan/10 border border-neon-cyan/20 flex items-center justify-center text-neon-cyan mx-auto shadow-lg shadow-neon-cyan/10">
                                                    <i class="ph-bold ph-shield-star text-3xl animate-pulse"></i>
                                                </div>
                                                <p class="text-[10px] text-slate-500 font-black uppercase tracking-[0.2em] leading-relaxed">
                                                    Immutable Core System Identity
                                                </p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 gap-5 mb-10 relative z-10 pointer-events-none group-hover:pointer-events-auto">
                                <div class="flex items-center gap-5 p-4 rounded-2xl bg-black/20 border border-white/5 hover:border-white/10 transition-colors group/info">
                                    <div class="w-10 h-10 rounded-xl bg-black/40 flex items-center justify-center border border-white/5 shadow-inner group-hover/info:border-neon-cyan/30 transition-all">
                                        <i class="ph-bold ph-envelope-simple text-slate-600 group-hover/info:text-neon-cyan transition-colors"></i>
                                    </div>
                                    <div class="flex flex-col min-w-0">
                                        <span class="text-[8px] text-slate-700 font-extrabold uppercase tracking-widest mb-0.5">Digital Address</span>
                                        <span class="text-[11px] text-slate-400 font-bold truncate"><?= e($admin['recovery_email'] ?: 'NULL_VOICE') ?></span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-5 p-4 rounded-2xl bg-black/20 border border-white/5 hover:border-white/10 transition-colors group/info">
                                    <div class="w-10 h-10 rounded-xl bg-black/40 flex items-center justify-center border border-white/5 shadow-inner group-hover/info:border-neon-purple/30 transition-all">
                                        <i class="ph-bold ph-shield-check text-slate-600 group-hover/info:text-neon-purple transition-colors"></i>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[8px] text-slate-700 font-extrabold uppercase tracking-widest mb-0.5">Security Clearance</span>
                                        <span class="text-[10px] font-black uppercase tracking-widest <?= $admin['role'] === 'super_admin' ? 'text-neon-purple' : 'text-slate-500' ?>">
                                            <?= $admin['role'] === 'super_admin' ? 'Strategic Overseer' : 'Tactical Operator' ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- IP Protection System -->
                            <div class="mt-auto border-t border-white/5 pt-8 relative z-10">
                                <button onclick="toggleWhitelist(<?= $admin['id'] ?>)" class="w-full flex items-center justify-between group/guard">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-black/40 border border-white/10 flex items-center justify-center group-hover/guard:border-neon-cyan/30 transition-all">
                                            <i class="ph-bold ph-unite text-slate-700 group-hover/guard:text-neon-cyan transition-colors"></i>
                                        </div>
                                        <span class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-600 group-hover/guard:text-slate-400 transition-colors">
                                            Boundary Gates <span class="text-neon-cyan opacity-40 ml-1">(<?= count($admin['whitelisted_ips'] ?? []) ?>)</span>
                                        </span>
                                    </div>
                                    <i class="ph-bold ph-caret-down text-slate-800 transition-all duration-500 group-hover/guard:text-neon-cyan" id="whitelist-caret-<?= $admin['id'] ?>"></i>
                                </button>
                                
                                <div id="whitelist-panel-<?= $admin['id'] ?>" class="hidden space-y-4 mt-8 animate-slide-down">
                                    <?php if(empty($admin['whitelisted_ips'])): ?>
                                        <div class="px-6 py-6 bg-black/20 rounded-2xl border border-dashed border-white/5 text-center">
                                            <p class="text-[9px] text-slate-800 font-black uppercase tracking-widest leading-relaxed">Global access authorized. <br>No boundary restrictions.</p>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php foreach ($admin['whitelisted_ips'] ?? [] as $ipEntry): ?>
                                        <div class="flex items-center justify-between bg-black/60 rounded-2xl px-5 py-4 border border-white/5 shadow-inner group/ip hover:border-neon-cyan/20 transition-all">
                                            <div class="flex flex-col">
                                                <span class="text-xs text-white font-mono font-black tracking-tight flex items-center gap-2">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-neon-cyan shadow-[0_0_8px_rgba(6,182,212,0.5)]"></span>
                                                    <?= e($ipEntry['ip_address']) ?>
                                                </span>
                                                <?php if($ipEntry['expires_at']): ?>
                                                    <span class="text-[8px] text-neon-amber font-black uppercase tracking-widest mt-1.5 flex items-center gap-1.5">
                                                        <i class="ph ph-hourglass-high"></i> DECAY: <?= date('d M, Y', strtotime($ipEntry['expires_at'])) ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <form method="POST" action="<?= baseUrl('admin/users') ?>" class="inline">
                                                <input type="hidden" name="action" value="delete_ip">
                                                <input type="hidden" name="id" value="<?= $ipEntry['id'] ?>">
                                                <button type="submit" class="w-9 h-9 flex items-center justify-center text-slate-700 hover:text-neon-rose rounded-xl hover:bg-neon-rose/10 transition-all active:scale-90">
                                                    <i class="ph-bold ph-shield-slash text-xl"></i>
                                                </button>
                                            </form>
                                        </div>
                                    <?php endforeach; ?>
                                    
                                    <button onclick="showAddIp(<?= $admin['id'] ?>)" class="w-full py-3.5 border border-dashed border-white/10 rounded-2xl text-[9px] font-black uppercase tracking-[0.3em] text-slate-700 hover:border-neon-cyan hover:text-neon-cyan hover:bg-neon-cyan/5 transition-all flex items-center justify-center gap-3 mt-4 group/add-gate">
                                        <i class="ph-bold ph-plus-circle text-lg transition-transform group-hover/add-gate:rotate-90"></i> Inject Access Point
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Dynamic Data Sequence (List) -->
                <div id="users-list-view" class="hidden admin-table-wrapper backdrop-blur-xl border border-white/5 rounded-[2rem] overflow-hidden shadow-premium">
                    <table class="admin-table w-full text-left border-collapse">
                        <thead>
                            <tr class="text-slate-600 text-[8px] font-black uppercase tracking-[0.4em] bg-white/[0.01]">
                                <th class="py-6 px-10">Identity Node</th>
                                <th class="py-6 px-6">Classification</th>
                                <th class="py-6 px-6">Digital Address</th>
                                <th class="py-6 px-6">Specialization</th>
                                <th class="py-6 px-10 text-right">Navigation</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/[0.02]">
                            <?php foreach ($admins as $admin): ?>
                                <tr class="hover:bg-white/[0.03] transition-all group/row border-b border-white/[0.03] last:border-0 relative">
                                    <td class="py-6 px-10" data-label="Node">
                                        <div class="flex items-center gap-5">
                                            <div class="w-11 h-11 rounded-xl bg-black/40 border border-white/10 flex items-center justify-center text-2xl shadow-lg ring-1 ring-white/5 group-hover/row:border-neon-cyan/40 group-hover/row:bg-neon-cyan/5 transition-all">
                                                <?= e($admin['avatar_emoji'] ?? '👤') ?>
                                            </div>
                                            <div class="flex flex-col">
                                                <div class="text-white font-black text-[11px] uppercase tracking-wider group-hover/row:text-neon-cyan transition-colors"><?= e($admin['full_name'] ?: $admin['username']) ?></div>
                                                <div class="text-[9px] font-mono text-slate-600 uppercase tracking-widest mt-0.5"><?= e($admin['username']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-6 px-6" data-label="Class">
                                        <span class="text-[9px] font-black uppercase tracking-[0.2em] <?= $admin['role'] === 'super_admin' ? 'text-neon-purple' : 'text-slate-500' ?>">
                                            <?= $admin['role'] === 'super_admin' ? 'Super Overseer' : 'Tactical Admin' ?>
                                        </span>
                                    </td>
                                    <td class="py-6 px-6" data-label="Address">
                                        <span class="text-[10px] text-slate-500 font-bold font-mono tracking-tight lowercase"><?= e($admin['recovery_email'] ?: 'null_voice@tech.io') ?></span>
                                    </td>
                                    <td class="py-6 px-6" data-label="Spec">
                                        <?php if($admin['is_salesperson']): ?>
                                            <div class="inline-flex items-center gap-2 px-3 py-1 bg-neon-emerald/10 border border-neon-emerald/20 rounded-lg">
                                                <span class="w-1 h-1 rounded-full bg-neon-emerald shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                                                <span class="text-[8px] text-neon-emerald font-black uppercase tracking-widest">Sales Core</span>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-slate-800 text-[8px] font-black tracking-widest opacity-20">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-6 px-10 text-right" data-label="Nav">
                                        <div class="flex justify-end gap-3 opacity-0 group-hover/row:opacity-100 transition-all translate-x-4 group-hover/row:translate-x-0">
                                            <a href="<?= baseUrl('admin/profile?id='.$admin['id']) ?>" class="w-10 h-10 rounded-xl bg-neon-cyan/10 text-neon-cyan hover:bg-neon-cyan hover:text-black border border-neon-cyan/20 transition-all flex items-center justify-center shadow-lg active:scale-90">
                                                <i class="ph-bold ph-user-focus text-lg"></i>
                                            </a>
                                            <div class="dropdown relative inline-block">
                                                <button type="button" class="w-10 h-10 flex items-center justify-center text-slate-500 hover:text-white rounded-xl hover:bg-white/5 border border-white/5 transition-all" onclick="toggleDropdown(this)">
                                                    <i class="ph ph-dots-three-bold text-xl"></i>
                                                </button>
                                                <div class="dropdown-menu hidden absolute right-0 mt-3 w-56 bg-slate-900 border border-white/10 rounded-2xl shadow-premium overflow-hidden z-[60] backdrop-blur-2xl">
                                                    <?php if($admin['id'] !== 1 && $admin['id'] !== $_SESSION['admin_id']): ?>
                                                        <form method="POST" action="<?= baseUrl('admin/users') ?>" class="p-2 border-b border-white/5">
                                                            <input type="hidden" name="action" value="toggle_salesperson">
                                                            <input type="hidden" name="id" value="<?= $admin['id'] ?>">
                                                            <input type="hidden" name="is_salesperson" value="<?= ($admin['is_salesperson'] ?? 0) ?>">
                                                            <button type="submit" class="w-full text-left px-4 py-3 text-[9px] font-black uppercase tracking-widest text-slate-400 hover:text-white hover:bg-white/5 rounded-xl flex items-center gap-3 transition-colors">
                                                                <i class="ph ph-lightning text-lg"></i>
                                                                <?= ($admin['is_salesperson'] ?? 0) ? 'De-elevate Node' : 'Elevate to Sales' ?>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                    <div class="p-2">
                                                        <button class="w-full text-left px-4 py-3 text-[9px] font-black uppercase tracking-widest text-slate-400 hover:text-white hover:bg-white/5 rounded-xl flex items-center gap-3 transition-colors">
                                                            <i class="ph ph-shield-warning text-lg"></i> Audit Access
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Add IP Modal -->
<div id="addIpModal" class="hidden fixed inset-0 z-[150] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/80 backdrop-blur-md" onclick="closeIpModal()"></div>
    <div class="bg-glass-bg border border-white/10 p-10 rounded-[2.5rem] w-full max-w-md relative z-10 shadow-premium animate-scale-in">
        <h3 class="text-xl font-black text-white mb-8 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-neon-cyan/10 flex items-center justify-center text-neon-cyan border border-neon-cyan/20">
                <i class="ph ph-shield-check"></i>
            </div>
            <span class="uppercase text-sm tracking-[0.2em]">Authorize Access Node</span>
        </h3>
        <form method="POST" action="<?= baseUrl('admin/users') ?>" class="space-y-6">
            <input type="hidden" name="action" value="add_ip">
            <input type="hidden" name="admin_id" id="modal-admin-id">
            
            <div class="space-y-2">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">IPv4 Protocol Address</label>
                <input type="text" name="ip_address" required class="w-full bg-black/40 border border-white/10 rounded-2xl px-5 py-4 text-sm font-black text-white focus:outline-none focus:border-neon-cyan placeholder-slate-700 transition-all font-mono" placeholder="000.000.000.000">
            </div>
            
            <div class="space-y-2">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Temporal Decay (Optional)</label>
                <input type="datetime-local" name="expires_at" class="w-full bg-black/40 border border-white/10 rounded-2xl px-5 py-4 text-sm font-black text-white focus:outline-none focus:border-neon-cyan [color-scheme:dark] transition-all">
                <p class="text-[9px] text-slate-600 font-bold uppercase tracking-widest mt-3 flex items-center gap-2 ml-1">
                    <i class="ph ph-info text-neon-cyan opacity-70"></i> Omit for permanent clearance
                </p>
            </div>
            
            <button type="submit" class="w-full px-8 py-4 bg-neon-cyan hover:bg-cyan-400 text-black text-[11px] font-black uppercase tracking-[0.2em] rounded-2xl transition-all shadow-[0_0_20px_rgba(6,182,212,0.2)] hover:shadow-[0_0_30px_rgba(6,182,212,0.4)] transform hover:-translate-y-1 active:scale-95 mt-6">
                Commit Authorization
            </button>
        </form>
    </div>
</div>

<!-- Create User Modal -->
<div id="createUserModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/80 backdrop-blur-md" onclick="this.parentElement.classList.add('hidden')"></div>
    <div class="bg-glass-bg border border-white/10 p-10 rounded-[2.5rem] w-full max-w-xl relative z-10 shadow-premium animate-scale-in max-h-[90vh] overflow-y-auto crm-main-scroll">
        <div class="flex justify-between items-center mb-10">
            <h2 class="text-xl font-black text-white flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-neon-cyan/10 flex items-center justify-center text-neon-cyan border border-neon-cyan/20">
                    <i class="ph ph-user-plus"></i>
                </div>
                <span class="uppercase text-sm tracking-[0.2em]">Initialize Personnel Node</span>
            </h2>
            <button onclick="document.getElementById('createUserModal').classList.add('hidden')" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/5 text-slate-500 hover:text-white transition-all">
                <i class="ph ph-x text-xl"></i>
            </button>
        </div>
        
        <form method="POST" action="<?= baseUrl('admin/users') ?>" class="space-y-8">
            <input type="hidden" name="action" value="create">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Identity Tag *</label>
                    <div class="relative group">
                        <i class="ph ph-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-600 group-focus-within:text-neon-cyan transition-colors"></i>
                        <input type="text" name="username" required class="w-full bg-black/40 border border-white/10 rounded-2xl pl-12 pr-4 py-3.5 text-xs font-black text-white focus:outline-none focus:border-neon-cyan transition-all placeholder-slate-700" placeholder="e.g. neuro_admin">
                    </div>
                </div>
                
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Full Biological Name</label>
                    <div class="relative group">
                        <i class="ph ph-identification-card absolute left-4 top-1/2 -translate-y-1/2 text-slate-600 group-focus-within:text-neon-cyan transition-colors"></i>
                        <input type="text" name="full_name" class="w-full bg-black/40 border border-white/10 rounded-2xl pl-12 pr-4 py-3.5 text-xs font-black text-white focus:outline-none focus:border-neon-cyan transition-all placeholder-slate-700" placeholder="e.g. Alexander Vance">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Communication Frequency</label>
                    <div class="relative group">
                        <i class="ph ph-envelope-simple absolute left-4 top-1/2 -translate-y-1/2 text-slate-600 group-focus-within:text-neon-cyan transition-colors"></i>
                        <input type="email" name="email" class="w-full bg-black/40 border border-white/10 rounded-2xl pl-12 pr-4 py-3.5 text-xs font-black text-white focus:outline-none focus:border-neon-cyan transition-all placeholder-slate-700" placeholder="vance@techagency.io">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Access Protocol Key *</label>
                    <div class="relative group">
                        <i class="ph ph-lock-key absolute left-4 top-1/2 -translate-y-1/2 text-slate-600 group-focus-within:text-neon-cyan transition-colors"></i>
                        <input type="password" name="password" required class="w-full bg-black/40 border border-white/10 rounded-2xl pl-12 pr-4 py-3.5 text-xs font-black text-white focus:outline-none focus:border-neon-cyan transition-all placeholder-slate-700" placeholder="••••••••">
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Clearance Tier Configuration</label>
                <div class="relative">
                    <select name="role" id="roleSelect" onchange="togglePermissions()" class="w-full bg-black/40 border border-white/10 rounded-2xl px-5 py-4 text-xs font-black text-white focus:outline-none focus:border-neon-cyan transition-all appearance-none cursor-pointer">
                        <option value="standard">Tactical Admin (Localized Access)</option>
                        <option value="super_admin">Strategic Overseer (Omnipresent Access)</option>
                    </select>
                    <i class="ph ph-caret-down absolute right-5 top-1/2 -translate-y-1/2 text-slate-500 pointer-events-none"></i>
                </div>
                <p class="text-[9px] text-slate-600 font-bold uppercase tracking-widest mt-2 ml-1">Super Admins bypass all module restrictions and audit guards.</p>
            </div>

            <!-- Granular Permissions -->
            <div id="permissionsSection" class="bg-black/20 border border-white/5 rounded-[2rem] p-8 space-y-6">
                <label class="block text-[10px] font-black text-white uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                    <i class="ph ph-key text-neon-cyan"></i> Operational Modules
                </label>
                <div class="grid grid-cols-2 gap-6">
                    <?php 
                    $modules = [
                        'inbox' => 'Inbox & Chats',
                        'visitors' => 'Visitor Intel',
                        'bookings' => 'Reservation Node',
                        'crm' => 'CRM Protocol',
                        'content' => 'Static Assets',
                        'seo' => 'Search Vectors',
                        'blogs' => 'Data Journals',
                        'settings' => 'Core Systems'
                    ];
                    foreach($modules as $key => $label): ?>
                    <label class="flex items-center gap-3 cursor-pointer group/perm">
                        <input type="checkbox" name="permissions[]" value="<?= $key ?>" class="w-5 h-5 rounded-lg border-white/10 bg-black/40 text-neon-cyan cursor-pointer focus:ring-0 transition-all checked:bg-neon-cyan checked:border-neon-cyan" checked>
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-500 group-hover/perm:text-white transition-colors"><?= $label ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Sales Team Checkbox -->
            <div class="bg-neon-emerald/5 border border-neon-emerald/20 rounded-[1.5rem] p-6 flex items-start gap-4 transform transition-transform hover:scale-[1.01] duration-300">
                <input type="checkbox" name="is_salesperson" id="isSalesperson" value="1" class="mt-1 w-5 h-5 rounded-lg border-neon-emerald/30 bg-black/40 text-neon-emerald cursor-pointer focus:ring-0 checked:bg-neon-emerald">
                <div>
                    <label for="isSalesperson" class="block text-[11px] font-black text-neon-emerald uppercase tracking-widest cursor-pointer flex items-center gap-2">
                        <i class="ph ph-handshake"></i> Integrate with Sales Department
                    </label>
                    <p class="text-[9px] text-slate-500 font-bold uppercase tracking-widest mt-2 leading-relaxed">Identity becomes assignable within CRM Pipeline nodes and Lead funnels.</p>
                </div>
            </div>

            <div class="pt-6">
                <button type="submit" class="w-full px-8 py-5 bg-neon-cyan hover:bg-cyan-400 text-black text-[11px] font-black uppercase tracking-[0.2em] rounded-2xl transition-all shadow-[0_0_20px_rgba(6,182,212,0.2)] hover:shadow-[0_0_30px_rgba(6,182,212,0.4)] transform hover:-translate-y-1 active:scale-95">
                    Commit Identity Initialization
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function switchView(view) {
        const cardView = document.getElementById('users-card-view');
        const listView = document.getElementById('users-list-view');
        const cardBtn = document.getElementById('view-card-btn');
        const listBtn = document.getElementById('view-list-btn');
        
        if (view === 'card') {
            cardView.classList.remove('hidden');
            listView.classList.add('hidden');
            cardBtn.className = 'flex items-center gap-2 px-4 py-2 rounded-xl transition-all duration-300 text-[10px] font-black uppercase tracking-widest text-neon-cyan bg-neon-cyan/10 border border-neon-cyan/20 shadow-[0_0_15px_rgba(6,182,212,0.1)]';
            listBtn.className = 'flex items-center gap-2 px-4 py-2 rounded-xl transition-all duration-300 text-[10px] font-black uppercase tracking-widest text-slate-500 hover:text-white hover:bg-white/5 border border-transparent';
        } else {
            cardView.classList.add('hidden');
            listView.classList.remove('hidden');
            listBtn.className = 'flex items-center gap-2 px-4 py-2 rounded-xl transition-all duration-300 text-[10px] font-black uppercase tracking-widest text-neon-cyan bg-neon-cyan/10 border border-neon-cyan/20 shadow-[0_0_15px_rgba(6,182,212,0.1)]';
            cardBtn.className = 'flex items-center gap-2 px-4 py-2 rounded-xl transition-all duration-300 text-[10px] font-black uppercase tracking-widest text-slate-500 hover:text-white hover:bg-white/5 border border-transparent';
        }
        localStorage.setItem('user-management-view', view);
    }

    function toggleDropdown(btn) {
        // Close all other dropdowns first
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            if (menu !== btn.nextElementSibling) menu.classList.add('hidden');
        });
        btn.nextElementSibling.classList.toggle('hidden');
    }

    function toggleWhitelist(adminId) {
        const panel = document.getElementById(`whitelist-panel-${adminId}`);
        const caret = document.getElementById(`whitelist-caret-${adminId}`);
        panel.classList.toggle('hidden');
        if (!panel.classList.contains('hidden')) {
            caret.style.transform = 'rotate(180deg)';
        } else {
            caret.style.transform = 'rotate(0deg)';
        }
    }

    function showAddIp(adminId) {
        document.getElementById('modal-admin-id').value = adminId;
        document.getElementById('addIpModal').classList.remove('hidden');
    }

    function closeIpModal() {
        document.getElementById('addIpModal').classList.add('hidden');
    }

    // Load preferred view
    document.addEventListener('DOMContentLoaded', () => {
        const prefView = localStorage.getItem('user-management-view') || 'card';
        switchView(prefView);
    });

    // Close dropdowns when clicked outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown-menu').forEach(menu => menu.classList.add('hidden'));
        }
    });
</script>

<style>
    @keyframes scaleIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
    .animate-scale-in { animation: scaleIn 0.2s cubic-bezier(0, 0, 0.2, 1) forwards; }
    
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-slide-down { animation: slideDown 0.3s cubic-bezier(0, 0, 0.2, 1) forwards; }

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
