<?php
if (!defined('APP_NAME')) die('Direct access prevented');
?>
<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <title>Store Sections — <?= APP_NAME ?></title>
    <?php require __DIR__ . '/partials/_head_assets.php'; ?>
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<div class="admin-layout flex w-full h-screen overflow-hidden">
    <?php $currentPage = 'app-sections'; require __DIR__ . '/partials/sidebar.php'; ?>
    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-20 flex items-center justify-between px-8 bg-glass-bg border-b border-white/5 shrink-0 backdrop-blur-xl sticky top-0 z-[100]">
            <div class="relative flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-violet-500/10 flex items-center justify-center border border-violet-500/20">
                    <i class="ph ph-layout text-2xl text-violet-500"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white tracking-tight">Store Sections</h1>
                    <p class="text-[10px] text-white/40 uppercase tracking-widest font-black">Frontend Layout Management</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                 <a href="<?= baseUrl('admin/app-sections?action=new') ?>" class="group flex items-center gap-2 px-5 py-2.5 bg-violet-500/10 hover:bg-violet-500/20 border border-violet-500/20 rounded-xl transition-all">
                    <i class="ph ph-plus-circle text-lg text-violet-500"></i>
                    <span class="text-sm font-semibold text-violet-500">New Section</span>
                </a>
                <?php require __DIR__ . '/partials/_topbar.php'; ?>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-8 crm-main-scroll bg-[#0b0e14]">
             <?php if ($flash = getFlash()): ?>
                <div class="mb-8 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl flex items-center gap-3">
                    <i class="ph ph-check-circle text-emerald-500"></i>
                    <p class="text-emerald-500 font-medium"><?= e($flash) ?></p>
                </div>
            <?php endif; ?>

            <?php if ($action === 'edit' || $action === 'new'): ?>
                <?php 
                $editSection = null;
                $activeProducts = [];
                if ($action === 'edit' && isset($_GET['id'])) {
                    foreach($sections as $s) if($s['id'] == $_GET['id']) $editSection = $s;
                    foreach($editSection['products'] as $p) $activeProducts[] = $p['id'];
                }
                ?>
                <div class="max-w-2xl mx-auto">
                    <form method="POST" action="<?= baseUrl('admin/app-sections') ?>" class="admin-card p-8 space-y-6">
                        <input type="hidden" name="id" value="<?= $editSection['id'] ?? 0 ?>">
                        <div class="space-y-2">
                            <label class="text-[11px] font-bold text-white/40 uppercase tracking-widest">Section Title</label>
                            <input type="text" name="title" class="form-input" required value="<?= e($editSection['title'] ?? '') ?>" placeholder="e.g. Featured Software Tools">
                        </div>
                        <div class="grid grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[11px] font-bold text-white/40 uppercase tracking-widest">Sort Order</label>
                                <input type="number" name="sort_order" class="form-input" value="<?= $editSection['sort_order'] ?? 0 ?>">
                            </div>
                            <div class="flex items-center pt-6">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" name="is_active" class="w-4 h-4 rounded border-white/10" <?= ($editSection['is_active'] ?? 1) ? 'checked' : '' ?>>
                                    <span class="text-sm font-bold text-white">Active</span>
                                </label>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[11px] font-bold text-white/40 uppercase tracking-widest">Select Products</label>
                            <div class="grid grid-cols-2 gap-2 max-h-60 overflow-y-auto p-4 bg-white/5 rounded-xl border border-white/10 scrollbar-thin">
                                <?php foreach($allProducts as $p): ?>
                                    <label class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/5 cursor-pointer">
                                        <input type="checkbox" name="product_ids[]" value="<?= $p['id'] ?>" <?= in_array($p['id'], $activeProducts) ? 'checked' : '' ?> class="w-4 h-4 rounded">
                                        <span class="text-xs font-semibold text-white/80"><?= e($p['name']) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 pt-4">
                            <button type="submit" class="flex-1 bg-violet-500 hover:bg-violet-600 text-white font-bold py-3 rounded-xl transition-all">Save Section</button>
                            <a href="<?= baseUrl('admin/app-sections') ?>" class="px-8 py-3 bg-white/5 text-white font-bold rounded-xl border border-white/10">Cancel</a>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 gap-4">
                    <?php foreach($sections as $s): ?>
                        <div class="admin-card p-6 flex items-center justify-between">
                            <div class="flex items-center gap-6">
                                <div class="w-12 h-12 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center font-bold text-white/20"><?= $s['sort_order'] ?></div>
                                <div>
                                    <h3 class="text-lg font-bold text-white"><?= e($s['title']) ?></h3>
                                    <p class="text-xs text-white/40"><?= count($s['products']) ?> Products assigned • <?= $s['is_active'] ? '<span class="text-emerald-500">Visible</span>' : '<span class="text-pink-500">Hidden</span>' ?></p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <a href="<?= baseUrl('admin/app-sections?action=edit&id='.$s['id']) ?>" class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center text-white/60 hover:text-violet-500 transition-all border border-white/5"><i class="ph ph-pencil"></i></a>
                                <a href="<?= baseUrl('admin/app-sections?action=delete&id='.$s['id']) ?>" onclick="return confirm('Delete section?')" class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center text-white/60 hover:text-pink-500 transition-all border border-white/5"><i class="ph ph-trash"></i></a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>
<?php require __DIR__ . '/partials/_footer_assets.php'; ?>
</body>
</html>
