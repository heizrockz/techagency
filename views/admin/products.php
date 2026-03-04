<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <title>Product Protocol — <?= APP_NAME ?></title>
    <?php require __DIR__ . '/partials/_head_assets.php'; ?>
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<div class="admin-layout flex w-full h-screen overflow-hidden">
    <?php $currentPage = 'products'; require __DIR__ . '/partials/sidebar.php'; ?>
    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-20 flex items-center justify-between px-8 bg-glass-bg border-b border-white/5 shrink-0 backdrop-blur-xl sticky top-0 z-[100]">
            <div class="absolute inset-0 bg-gradient-to-r from-violet-500/5 via-transparent to-transparent"></div>
            <div class="relative flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-violet-500/10 flex items-center justify-center border border-violet-500/20">
                    <i class="ph ph-cube text-2xl text-violet-500 animate-pulse"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white tracking-tight">Products</h1>
                    <p class="text-[10px] text-white/40 uppercase tracking-widest font-black hidden sm:block">Innovation Registry</p>
                </div>
            </div>
            <div class="relative flex items-center gap-4">
                <a href="<?= baseUrl('admin/products?action=new') ?>" class="group flex items-center gap-2 px-3 sm:px-5 py-2.5 bg-violet-500/10 hover:bg-violet-500/20 border border-violet-500/20 hover:border-violet-500/40 rounded-xl transition-all duration-300">
                    <i class="ph ph-plus-circle text-lg text-violet-500 group-hover:rotate-90 transition-transform duration-500"></i>
                    <span class="text-sm font-semibold text-violet-500 hidden sm:inline">Deploy</span>
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
                    <p class="text-emerald-500 font-medium">Asset parameters synchronized successfully.</p>
                </div>
            <?php endif; ?>

            <?php if ($action === 'edit' || $action === 'new'): ?>
                <div class="max-w-4xl mx-auto">
                    <div class="admin-card relative overflow-hidden group">
                        <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:opacity-10 transition-opacity">
                            <i class="ph ph-package text-8xl text-violet-500"></i>
                        </div>
                        
                        <div class="relative flex items-center gap-3 mb-8">
                            <div class="w-10 h-10 rounded-lg bg-violet-500/10 flex items-center justify-center border border-violet-500/20 text-violet-500">
                                <i class="ph ph-pencil-line text-xl"></i>
                            </div>
                            <h2 class="text-xl font-bold text-white"><?= $action === 'edit' ? 'Modify Asset Specifications' : 'Initialize New Product Idea' ?></h2>
                        </div>

                        <form method="POST" action="<?= baseUrl('admin/products') ?>" class="relative space-y-8">
                            <input type="hidden" name="id" value="<?= $editProduct['id'] ?? 0 ?>">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-white/60 ml-1">Classification Category</label>
                                    <select name="category" class="form-input">
                                        <?php $cat = $editProduct['category'] ?? 'website'; ?>
                                        <option value="website" <?= $cat==='website'?'selected':'' ?>>Web Architecture</option>
                                        <option value="app" <?= $cat==='app'?'selected':'' ?>>Mobile Subsystem</option>
                                        <option value="maintenance" <?= $cat==='maintenance'?'selected':'' ?>>Operational Maintenance</option>
                                    </select>
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-white/60 ml-1">Icon Identifier</label>
                                    <div class="relative group/input">
                                        <i class="ph ph-tag absolute left-4 top-1/2 -translate-y-1/2 text-violet-500/50 group-focus-within/input:text-violet-500 transition-colors"></i>
                                        <input type="text" name="icon" class="form-input !pl-12" placeholder="globe, monitor, etc." value="<?= e($editProduct['icon'] ?? 'globe') ?>">
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-white/60 ml-1">Spectral Signature</label>
                                    <select name="color" class="form-input">
                                        <?php 
                                        $colors = ['cobalt', 'violet', 'emerald', 'pink', 'cyan', 'orange'];
                                        $selectedColor = $editProduct['color'] ?? 'cobalt';
                                        foreach($colors as $c): ?>
                                            <option value="<?= $c ?>" <?= $selectedColor === $c ? 'selected' : '' ?>><?= ucfirst($c) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-white/60 ml-1">Sequence Priority</label>
                                    <input type="number" name="sort_order" class="form-input" value="<?= $editProduct['sort_order'] ?? 0 ?>">
                                </div>

                                <div class="flex items-center pt-8">
                                    <label class="relative flex items-center gap-3 cursor-pointer group">
                                        <input type="checkbox" name="is_active" class="peer hidden" <?= (!isset($editProduct) || $editProduct['is_active']) ? 'checked' : '' ?>>
                                        <div class="w-12 h-6 bg-white/5 rounded-full border border-white/10 peer-checked:bg-violet-500/20 peer-checked:border-violet-500/40 transition-all duration-300"></div>
                                        <div class="absolute left-1 top-1 w-4 h-4 bg-white/20 rounded-full peer-checked:left-7 peer-checked:bg-violet-500 transition-all duration-300 shadow-lg"></div>
                                        <span class="text-sm font-medium text-white/60 group-hover:text-white transition-colors">Asset Operational</span>
                                    </label>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <?php foreach (SUPPORTED_LOCALES as $loc): ?>
                                    <div class="p-6 rounded-2xl bg-white/[0.03] border border-white/5 relative overflow-hidden group/loc">
                                        <div class="absolute top-0 right-0 w-24 h-24 bg-violet-500/5 rounded-full -mr-12 -mt-12 blur-2xl group-hover/loc:bg-violet-500/10 transition-colors"></div>
                                        
                                        <div class="flex items-center justify-between mb-6">
                                            <div class="flex items-center gap-2">
                                                <div class="w-2 h-2 rounded-full bg-violet-500 animate-pulse"></div>
                                                <h4 class="text-sm font-bold text-white uppercase tracking-widest"><?= strtoupper($loc) ?> Signal Channel</h4>
                                            </div>
                                            <span class="px-2 py-0.5 rounded-md bg-white/5 text-[10px] font-bold text-white/40 border border-white/10"><?= $loc === 'ar' ? 'RTL' : 'LTR' ?></span>
                                        </div>

                                        <div class="space-y-4">
                                            <div class="space-y-2">
                                                <label class="text-xs font-semibold text-white/40 ml-1">Title</label>
                                                <input type="text" name="title_<?= $loc ?>" class="form-input <?= $loc === 'ar' ? 'rtl-input' : '' ?>" value="<?= e($editProduct['translations'][$loc]['title'] ?? '') ?>" required>
                                            </div>
                                            <div class="space-y-2">
                                                <label class="text-xs font-semibold text-white/40 ml-1">Description</label>
                                                <textarea name="desc_<?= $loc ?>" class="form-input min-h-[100px] <?= $loc === 'ar' ? 'rtl-input' : '' ?>" rows="3"><?= e($editProduct['translations'][$loc]['description'] ?? '') ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="flex items-center gap-4 pt-6 border-t border-white/5">
                                <button type="submit" class="group relative px-8 py-3 bg-violet-500 text-black font-bold rounded-xl hover:bg-violet-400 transition-all shadow-lg shadow-violet-500/20 overflow-hidden">
                                    <span class="relative z-10">Commit Asset</span>
                                    <div class="absolute inset-0 bg-white/20 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-1000"></div>
                                </button>
                                <a href="<?= baseUrl('admin/products') ?>" class="px-8 py-3 bg-white/5 text-white font-semibold rounded-xl hover:bg-white/10 transition-all border border-white/10">
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
                            <div class="w-10 h-10 rounded-xl bg-violet-500/10 flex items-center justify-center border border-violet-500/20">
                                <i class="ph ph-package text-violet-500 text-xl"></i>
                            </div>
                            <h3 class="font-bold text-white tracking-tight text-lg">Innovation Asset Matrix</h3>
                        </div>
                        <div class="bg-white/5 px-4 py-1.5 rounded-full border border-white/10">
                            <span class="text-[10px] text-white/40 uppercase tracking-widest font-bold">Total Assets:</span>
                            <span class="text-sm font-mono text-violet-500 font-bold ml-2"><?= count($products) ?></span>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th class="!pl-8">Asset Entity</th>
                                    <th>Translation Data</th>
                                    <th class="text-center">Seq Index</th>
                                    <th class="text-center">Status</th>
                                    <th class="!pr-8 text-right">Operations</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                <?php foreach ($products as $p): ?>
                                    <tr class="group hover:bg-white/[0.02] transition-colors duration-300">
                                        <td class="!pl-8">
                                            <div class="flex items-center gap-4">
                                                <div class="w-12 h-12 rounded-2xl bg-<?= $p['color'] ?>/10 flex items-center justify-center border border-<?= $p['color'] ?>/20 group-hover:scale-110 transition-transform duration-500">
                                                    <i class="ph ph-<?= e($p['icon']) ?> text-2xl text-<?= $p['color'] ?>"></i>
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-xs font-bold text-white/40 uppercase tracking-widest"><?= e($p['category']) ?></span>
                                                    <span class="text-sm font-bold text-white group-hover:text-violet-500 transition-colors uppercase"><?= e($p['trans']) ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="flex gap-1.5">
                                                <?php foreach (SUPPORTED_LOCALES as $loc): ?>
                                                    <span class="px-1.5 py-0.5 rounded bg-white/5 text-[9px] font-bold text-white/30 border border-white/5 uppercase"><?= $loc ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="text-xs font-mono text-white/40 bg-white/5 px-2 py-1 rounded-md border border-white/10"><?= $p['sort_order'] ?></span>
                                        </td>
                                        <td class="text-center">
                                            <?php if($p['is_active']): ?>
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-500 text-[10px] font-bold uppercase tracking-widest border border-emerald-500/20">
                                                    Operational
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/5 text-white/40 text-[10px] font-bold uppercase tracking-widest border border-white/10">
                                                    Standby
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="!pr-8 text-right">
                                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <a href="<?= baseUrl('admin/products?action=edit&id='.$p['id']) ?>" class="w-8 h-8 rounded-lg bg-violet-500/10 flex items-center justify-center text-violet-500 hover:bg-violet-500 hover:text-black transition-all">
                                                    <i class="ph ph-pencil-simple"></i>
                                                </a>
                                                <button onclick="showDeleteModal('<?= e($p['trans']) ?>', '<?= baseUrl('admin/products?action=delete&id='.$p['id']) ?>')" class="w-8 h-8 rounded-lg bg-pink-500/10 flex items-center justify-center text-pink-500 hover:bg-pink-500 hover:text-white transition-all">
                                                    <i class="ph ph-trash"></i>
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
</div>

<?php require __DIR__ . '/partials/_delete_modal.php'; ?>
</body>
</html>
