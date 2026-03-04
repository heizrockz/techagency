<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <title>Services — <?= APP_NAME ?></title>
    <?php require __DIR__ . '/partials/_head_assets.php'; ?>
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<div class="admin-layout flex w-full h-screen overflow-hidden">
    <?php $currentPage = 'services'; require __DIR__ . '/partials/sidebar.php'; ?>
    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-20 flex items-center justify-between px-8 bg-glass-bg border-b border-white/5 shrink-0 backdrop-blur-xl sticky top-0 z-[100]">
            <div class="absolute inset-0 bg-gradient-to-r from-primary/5 via-transparent to-transparent"></div>
            <div class="relative flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center border border-primary/20">
                    <i class="ph ph-sparkle text-2xl text-primary animate-pulse"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white tracking-tight">Service Protocol</h1>
                    <p class="text-xs text-white/40 uppercase tracking-widest font-medium hidden sm:block">Content Architecture</p>
                </div>
            </div>
            <div class="relative flex items-center gap-4">
                <a href="<?= baseUrl('admin/services?action=new') ?>" class="group flex items-center gap-2 px-3 sm:px-5 py-2.5 bg-primary/10 hover:bg-primary/20 border border-primary/20 hover:border-primary/40 rounded-xl transition-all duration-300">
                    <i class="ph ph-plus-circle text-lg text-primary group-hover:rotate-90 transition-transform duration-500"></i>
                    <span class="text-sm font-semibold text-primary hidden sm:inline">New Service</span>
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
                    <p class="text-emerald-500 font-medium">Protocol update synchronized successfully.</p>
                </div>
            <?php endif; ?>

            <?php if ($action === 'edit' || $action === 'new'): ?>
                <div class="max-w-4xl mx-auto">
                    <div class="admin-card relative overflow-hidden group">
                        <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:opacity-10 transition-opacity">
                            <i class="ph ph-sparkle text-8xl text-primary"></i>
                        </div>
                        
                        <div class="relative flex items-center gap-3 mb-8">
                            <i class="ph ph-gear-six text-primary text-xl"></i>
                            <h2 class="text-xl font-bold text-white"><?= $action === 'edit' ? 'Modify Service Node' : 'Initialize New Service' ?></h2>
                        </div>

                        <form method="POST" action="<?= baseUrl('admin/services') ?>" class="relative space-y-8">
                            <input type="hidden" name="id" value="<?= $editService['id'] ?? 0 ?>">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-white/60 ml-1">Icon Identifier</label>
                                    <div class="relative">
                                        <i class="ph ph-tag absolute left-4 top-1/2 -translate-y-1/2 text-primary/50"></i>
                                        <input type="text" name="icon" class="form-input !pl-12" placeholder="code, monitor, chart..." value="<?= e($editService['icon'] ?? 'code') ?>">
                                    </div>
                                    <p class="text-[10px] text-white/30 uppercase tracking-tighter">Phosphor icon identifier</p>
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-white/60 ml-1">Spectral Signature</label>
                                    <select name="color" class="form-input">
                                        <?php 
                                        $colors = ['cobalt', 'violet', 'emerald', 'pink', 'cyan', 'orange'];
                                        $selectedColor = $editService['color'] ?? 'cobalt';
                                        foreach($colors as $c): ?>
                                            <option value="<?= $c ?>" <?= $selectedColor === $c ? 'selected' : '' ?>><?= ucfirst($c) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-white/60 ml-1">Sequence Priority</label>
                                    <input type="number" name="sort_order" class="form-input" value="<?= $editService['sort_order'] ?? 0 ?>">
                                </div>

                                <div class="flex items-center pt-8">
                                    <label class="relative flex items-center gap-3 cursor-pointer group">
                                        <input type="checkbox" name="is_active" class="peer hidden" <?= (!isset($editService) || $editService['is_active']) ? 'checked' : '' ?>>
                                        <div class="w-12 h-6 bg-white/5 rounded-full border border-white/10 peer-checked:bg-primary/20 peer-checked:border-primary/40 transition-all duration-300"></div>
                                        <div class="absolute left-1 top-1 w-4 h-4 bg-white/20 rounded-full peer-checked:left-7 peer-checked:bg-primary transition-all duration-300 shadow-lg"></div>
                                        <span class="text-sm font-medium text-white/60 group-hover:text-white transition-colors">Protocol Active</span>
                                    </label>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                <?php foreach (SUPPORTED_LOCALES as $loc): ?>
                                    <div class="p-6 rounded-2xl bg-white/[0.03] border border-white/5 space-y-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <h4 class="text-sm font-bold text-primary uppercase tracking-widest"><?= strtoupper($loc) ?> Interface</h4>
                                            <span class="px-2 py-0.5 rounded-md bg-white/5 text-[10px] font-bold text-white/40"><?= $loc === 'ar' ? 'RTL' : 'LTR' ?></span>
                                        </div>
                                        <div class="space-y-2">
                                            <label class="text-xs font-semibold text-white/40">Title</label>
                                            <input type="text" name="title_<?= $loc ?>" class="form-input" value="<?= e($editService['translations'][$loc]['title'] ?? '') ?>" dir="<?= $loc === 'ar' ? 'rtl' : 'ltr' ?>" required>
                                        </div>
                                        <div class="space-y-2">
                                            <label class="text-xs font-semibold text-white/40">Description</label>
                                            <textarea name="desc_<?= $loc ?>" class="form-input min-h-[100px]" dir="<?= $loc === 'ar' ? 'rtl' : 'ltr' ?>"><?= e($editService['translations'][$loc]['description'] ?? '') ?></textarea>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="flex items-center gap-4 pt-6 border-t border-white/5">
                                <button type="submit" class="px-8 py-3 bg-primary text-black font-bold rounded-xl hover:bg-primary/80 transition-all shadow-lg shadow-primary/20">
                                    Synchronize Node
                                </button>
                                <a href="<?= baseUrl('admin/services') ?>" class="px-8 py-3 bg-white/5 text-white font-semibold rounded-xl hover:bg-white/10 transition-all border border-white/10">
                                    Abort Operation
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <div class="admin-card p-0 overflow-hidden border-white/5">
                    <div class="p-6 border-b border-white/5 flex items-center justify-between bg-white/[0.01]">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center">
                                <i class="ph ph-list-bullets text-primary"></i>
                            </div>
                            <h3 class="font-bold text-white">Active Services Matrix</h3>
                        </div>
                        <div class="text-xs text-white/40 font-mono uppercase tracking-widest">
                            Nodes: <?= count($services) ?>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th class="!pl-8">Identity</th>
                                    <th>Translation Data</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th class="!pr-8 text-right">Operations</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                <?php foreach ($services as $s): ?>
                                    <tr class="group hover:bg-white/[0.02] transition-colors duration-300">
                                        <td class="!pl-8" data-label="Node">
                                            <div class="flex items-center gap-4">
                                                <div class="w-12 h-12 rounded-2xl bg-<?= $s['color'] ?>/10 flex items-center justify-center border border-<?= $s['color'] ?>/20 group-hover:scale-110 transition-transform duration-500">
                                                    <i class="ph ph-<?= e($s['icon']) ?> text-2xl text-<?= $s['color'] ?>"></i>
                                                </div>
                                                <div>
                                                    <div class="text-white font-bold tracking-tight">Node: <?= e($s['icon']) ?></div>
                                                    <div class="text-[10px] text-<?= $s['color'] ?> font-bold uppercase tracking-widest"><?= e($s['color']) ?> spectral</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td data-label="Trans">
                                            <div class="max-w-[300px]">
                                                <div class="text-white/80 font-medium truncate"><?= e($s['trans']) ?></div>
                                                <div class="text-[10px] text-white/20 uppercase mt-1">Multi-locale dataset</div>
                                            </div>
                                        </td>
                                        <td data-label="Priority">
                                            <span class="px-3 py-1 rounded-full bg-white/5 border border-white/10 text-white/60 text-xs font-mono">
                                                ORD-<?= str_pad($s['sort_order'], 3, '0', STR_PAD_LEFT) ?>
                                            </span>
                                        </td>
                                        <td data-label="Status">
                                            <?php if ($s['is_active']): ?>
                                                <div class="flex items-center gap-2 text-emerald-500">
                                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                                    <span class="text-xs font-bold uppercase tracking-wider">Active</span>
                                                </div>
                                            <?php else: ?>
                                                <div class="flex items-center gap-2 text-white/30">
                                                    <span class="w-2 h-2 rounded-full bg-white/20"></span>
                                                    <span class="text-xs font-bold uppercase tracking-wider">Offline</span>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="!pr-8 text-right" data-label="Operations">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="<?= baseUrl('admin/services?action=edit&id='.$s['id']) ?>" class="w-10 h-10 rounded-xl bg-cyan-500/10 flex items-center justify-center border border-cyan-500/20 text-cyan-500 hover:bg-cyan-500 hover:text-black transition-all duration-300" title="Edit Protocol">
                                                    <i class="ph ph-pencil-simple text-lg"></i>
                                                </a>
                                                <button onclick="showDeleteModal('this service node', '<?= baseUrl('admin/services?action=delete&id='.$s['id']) ?>')" class="w-10 h-10 rounded-xl bg-pink-500/10 flex items-center justify-center border border-pink-500/20 text-pink-500 hover:bg-pink-500 hover:text-black transition-all duration-300" title="Purge Node">
                                                    <i class="ph ph-trash text-lg"></i>
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
