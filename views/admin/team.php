<?php
/**
 * Admin: Our Team Members CRUD
 */
?>
<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <title>Our Team — <?= APP_NAME ?></title>
    <?php require __DIR__ . '/partials/_head_assets.php'; ?>
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<div class="admin-layout flex w-full h-screen overflow-hidden">
    <?php $currentPage = 'team'; require __DIR__ . '/partials/sidebar.php'; ?>
    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-20 flex items-center justify-between px-8 bg-glass-bg border-b border-white/5 shrink-0 backdrop-blur-xl sticky top-0 z-[100]">
            <div class="absolute inset-0 bg-gradient-to-r from-primary/5 via-transparent to-transparent"></div>
            <div class="relative flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center border border-primary/20">
                    <i class="ph ph-users text-2xl text-primary animate-pulse"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white tracking-tight">Personnel Registry</h1>
                    <p class="text-xs text-white/40 uppercase tracking-widest font-medium hidden sm:block">Human Capital Collective</p>
                </div>
            </div>
            <div class="relative flex items-center gap-4">
                <a href="<?= baseUrl('admin/team?action=new') ?>" class="group flex items-center gap-2 px-3 sm:px-5 py-2.5 bg-primary/10 hover:bg-primary/20 border border-primary/20 hover:border-primary/40 rounded-xl transition-all duration-300">
                    <i class="ph ph-user-plus text-lg text-primary group-hover:scale-110 transition-transform duration-300"></i>
                    <span class="text-sm font-semibold text-primary hidden sm:inline">Enlist</span>
                </a>
                <div class="h-8 w-px bg-white/10 mx-2"></div>
                <?php require __DIR__ . '/partials/_topbar.php'; ?>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-8 crm-main-scroll bg-[#0b0e14]">
            <?php if ($saved): ?>
                <div class="mb-8 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl flex items-center gap-3 animate-in fade-in slide-in-from-top-4">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/20 flex items-center justify-center">
                        <i class="ph ph-check-circle text-emerald-500"></i>
                    </div>
                    <p class="text-emerald-500 font-medium">Registry profile synchronized successfully.</p>
                </div>
            <?php endif; ?>

            <?php if ($action === 'edit' || $action === 'new'): ?>
                <div class="max-w-4xl mx-auto">
                    <div class="admin-card relative overflow-hidden group">
                        <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:opacity-10 transition-opacity rotate-12">
                            <i class="ph ph-identification-card text-8xl text-primary"></i>
                        </div>
                        
                        <div class="relative flex items-center gap-3 mb-8">
                            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center border border-primary/20 text-primary">
                                <i class="ph ph-user-focus text-xl"></i>
                            </div>
                            <h2 class="text-xl font-bold text-white"><?= $action === 'edit' ? 'Modify Personnel Profile' : 'Initialize New Identity' ?></h2>
                        </div>

                        <form method="POST" action="<?= baseUrl('admin/team') ?>" class="relative space-y-8">
                            <input type="hidden" name="id" value="<?= $editMember['id'] ?? 0 ?>">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-white/60 ml-1">Visual Identity (URL)</label>
                                    <div class="relative group/input">
                                        <i class="ph ph-image absolute left-4 top-1/2 -translate-y-1/2 text-primary/50 group-focus-within/input:text-primary transition-colors"></i>
                                        <input type="text" name="image_url" class="form-input !pl-12" placeholder="https://..." value="<?= e($editMember['image_url'] ?? '') ?>">
                                    </div>
                                    <p class="text-[10px] text-white/30 uppercase tracking-tighter">Avatar or profile photograph endpoint</p>
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-white/60 ml-1">Sequence Priority</label>
                                    <div class="relative group/input">
                                        <i class="ph ph-list-numbers absolute left-4 top-1/2 -translate-y-1/2 text-primary/50 group-focus-within/input:text-primary transition-colors"></i>
                                        <input type="number" name="sort_order" class="form-input !pl-12" value="<?= $editMember['sort_order'] ?? 0 ?>">
                                    </div>
                                </div>

                                <div class="flex items-center pt-8">
                                    <label class="relative flex items-center gap-3 cursor-pointer group">
                                        <input type="checkbox" name="is_active" class="peer hidden" <?= (!isset($editMember) || $editMember['is_active']) ? 'checked' : '' ?>>
                                        <div class="w-12 h-6 bg-white/5 rounded-full border border-white/10 peer-checked:bg-primary/20 peer-checked:border-primary/40 transition-all duration-300"></div>
                                        <div class="absolute left-1 top-1 w-4 h-4 bg-white/20 rounded-full peer-checked:left-7 peer-checked:bg-primary transition-all duration-300 shadow-lg"></div>
                                        <span class="text-sm font-medium text-white/60 group-hover:text-white transition-colors">Registry Visibility Active</span>
                                    </label>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <?php foreach (SUPPORTED_LOCALES as $loc): ?>
                                    <div class="p-6 rounded-2xl bg-white/[0.03] border border-white/5 relative overflow-hidden group/loc">
                                        <div class="absolute top-0 right-0 w-24 h-24 bg-primary/5 rounded-full -mr-12 -mt-12 blur-2xl group-hover/loc:bg-primary/10 transition-colors"></div>
                                        
                                        <div class="flex items-center justify-between mb-6">
                                            <div class="flex items-center gap-2">
                                                <div class="w-2 h-2 rounded-full bg-primary animate-pulse"></div>
                                                <h4 class="text-sm font-bold text-white uppercase tracking-widest"><?= strtoupper($loc) ?> Data Stream</h4>
                                            </div>
                                            <span class="px-2 py-0.5 rounded-md bg-white/5 text-[10px] font-bold text-white/40 border border-white/10"><?= $loc === 'ar' ? 'RTL' : 'LTR' ?></span>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div class="space-y-2">
                                                <label class="text-xs font-semibold text-white/40 ml-1">Full Legal Identity</label>
                                                <input type="text" name="name_<?= $loc ?>" class="form-input <?= $loc === 'ar' ? 'rtl-input' : '' ?>" value="<?= e($editMember['translations'][$loc]['name'] ?? '') ?>" required>
                                            </div>
                                            <div class="space-y-2">
                                                <label class="text-xs font-semibold text-white/40 ml-1">Designated Role / Title</label>
                                                <input type="text" name="role_<?= $loc ?>" class="form-input <?= $loc === 'ar' ? 'rtl-input' : '' ?>" value="<?= e($editMember['translations'][$loc]['role'] ?? '') ?>" required>
                                            </div>
                                            <div class="md:col-span-2 space-y-2">
                                                <label class="text-xs font-semibold text-white/40 ml-1">Operational Biography</label>
                                                <textarea name="bio_<?= $loc ?>" class="form-input min-h-[100px] <?= $loc === 'ar' ? 'rtl-input' : '' ?>" rows="3"><?= e($editMember['translations'][$loc]['bio'] ?? '') ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="flex items-center gap-4 pt-6 border-t border-white/5">
                                <button type="submit" class="group relative px-8 py-3 bg-primary text-black font-bold rounded-xl hover:bg-primary/90 transition-all shadow-lg shadow-primary/20 overflow-hidden">
                                    <span class="relative z-10">Commit Profile</span>
                                    <div class="absolute inset-0 bg-white/20 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-1000"></div>
                                </button>
                                <a href="<?= baseUrl('admin/team') ?>" class="px-8 py-3 bg-white/5 text-white font-semibold rounded-xl hover:bg-white/10 transition-all border border-white/10">
                                    Abort
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <div class="admin-card p-0 overflow-hidden border-white/5">
                    <div class="p-6 border-b border-white/5 flex items-center justify-between bg-white/[0.01]">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center border border-primary/20">
                                <i class="ph ph-address-book text-primary text-xl"></i>
                            </div>
                            <h3 class="font-bold text-white tracking-tight text-lg">Operational Personnel Matrix</h3>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="bg-white/5 px-4 py-1.5 rounded-full border border-white/10">
                                <span class="text-[10px] text-white/40 uppercase tracking-widest font-bold">Total Personnel:</span>
                                <span class="text-sm font-mono text-primary font-bold ml-2"><?= count($members) ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th class="!pl-8">Personnel Entity</th>
                                    <th>Identity Stream</th>
                                    <th>Title / Designation</th>
                                    <th>Registry Sort</th>
                                    <th>Status</th>
                                    <th class="!pr-8 text-right">Operations</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                <?php if (empty($members)): ?>
                                    <tr>
                                        <td colspan="6" class="p-20 text-center">
                                            <div class="flex flex-col items-center gap-4 opacity-30">
                                                <i class="ph ph-users-four text-6xl"></i>
                                                <p class="text-white font-medium">No personnel detected in the registry.</p>
                                                <a href="<?= baseUrl('admin/team?action=new') ?>" class="text-primary hover:underline text-sm font-bold uppercase tracking-widest">Enlist First Member</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>

                                <?php foreach ($members as $m): ?>
                                    <?php
                                        $transArr = [];
                                        foreach (explode('|', $m['trans'] ?? '') as $part) {
                                            $pieces = explode(':', $part, 2);
                                            if (count($pieces) === 2) {
                                                $transArr[$pieces[0]] = $pieces[1];
                                            }
                                        }
                                    ?>
                                    <tr class="group hover:bg-white/[0.02] transition-all duration-300">
                                        <td class="!pl-8" data-label="Identity">
                                            <div class="flex items-center gap-4">
                                                <div class="relative">
                                                    <?php if ($m['image_url']): ?>
                                                        <img src="<?= e($m['image_url']) ?>" class="w-12 h-12 rounded-2xl object-cover border border-white/10 group-hover:scale-110 group-hover:border-primary/40 transition-all duration-500 shadow-xl">
                                                    <?php else: ?>
                                                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center border border-primary/20 group-hover:scale-110 transition-all duration-500">
                                                            <span class="text-primary font-black text-xl"><?= strtoupper(substr($transArr['en'] ?? 'M', 0, 1)) ?></span>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full border-2 border-[#0b0e14] <?= $m['is_active'] ? 'bg-emerald-500' : 'bg-white/20' ?>"></div>
                                                </div>
                                                <div>
                                                    <div class="text-white font-bold tracking-tight text-lg"><?= e($transArr['en'] ?? '—') ?></div>
                                                    <div class="text-[10px] text-white/30 font-bold uppercase tracking-widest mt-0.5">Primary Localized Name</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td data-label="Locales">
                                            <div class="flex gap-1 items-center">
                                                <?php foreach(SUPPORTED_LOCALES as $l): ?>
                                                    <span class="px-1.5 py-0.5 rounded-md bg-white/5 border border-white/5 text-[9px] font-bold text-white/40 uppercase"><?= $l ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                        </td>
                                        <td data-label="Logic">
                                            <div class="text-white/70 font-medium"><?= e($transArr['en_role'] ?? ($transArr['en'] ? 'Member' : '—')) ?></div>
                                            <div class="text-[10px] text-primary/40 font-bold uppercase tracking-widest mt-0.5">Operational Logic</div>
                                        </td>
                                        <td data-label="Priority">
                                            <div class="w-fit px-3 py-1 rounded-lg bg-white/5 border border-white/10 text-white/60 text-xs font-mono group-hover:border-primary/20 transition-colors">
                                                #<?= str_pad($m['sort_order'], 3, '0', STR_PAD_LEFT) ?>
                                            </div>
                                        </td>
                                        <td data-label="Status">
                                            <?php if ($m['is_active']): ?>
                                                <div class="flex items-center gap-2 text-emerald-500">
                                                    <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.6)] animate-pulse"></div>
                                                    <span class="text-[10px] font-black uppercase tracking-widest">Active Duty</span>
                                                </div>
                                            <?php else: ?>
                                                <div class="flex items-center gap-2 text-white/20">
                                                    <div class="w-1.5 h-1.5 rounded-full bg-white/20"></div>
                                                    <span class="text-[10px] font-black uppercase tracking-widest">Deactivated</span>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="!pr-8 text-right" data-label="Operations">
                                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all translate-x-4 group-hover:translate-x-0">
                                                <a href="<?= baseUrl('admin/team?action=edit&id='.$m['id']) ?>" class="w-10 h-10 rounded-xl bg-cyan-500/10 flex items-center justify-center border border-cyan-500/20 text-cyan-500 hover:bg-cyan-500 hover:text-black transition-all duration-300" title="Edit Identity">
                                                    <i class="ph ph-fingerprint text-lg"></i>
                                                </a>
                                                <button onclick="showDeleteModal('this personnel entity', '<?= baseUrl('admin/team?action=delete&id='.$m['id']) ?>')" class="w-10 h-10 rounded-xl bg-pink-500/10 flex items-center justify-center border border-pink-500/20 text-pink-500 hover:bg-pink-500 hover:text-black transition-all duration-300" title="Revoke Clearance">
                                                    <i class="ph ph-skull text-lg"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </main>
</div>
<style>
<style>
    /* Desktop-first: ensure table looks good on large screens */
    @media screen and (min-width: 1025px) {
        .admin-table { min-width: 1200px; }
    }

    /* Mobile-responsive card transformation */
    @media (max-width: 1024px) {
        .admin-table-wrapper { border-radius: 1.75rem !important; margin: -1.25rem !important; }
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
            margin-bottom: 25px !important; 
            background: rgba(255,255,255,0.02) !important; 
            border-radius: 1.75rem !important; 
            padding: 24px !important;
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
<?php require __DIR__ . '/partials/_delete_modal.php'; ?>
</body>
</html>
