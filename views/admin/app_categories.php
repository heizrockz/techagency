<?php
// Ensure this file is only accessed through the controller
if (!defined('APP_NAME')) die('Direct access prevented');
?>
<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <title>App Categories — <?= APP_NAME ?></title>
    <?php require __DIR__ . '/partials/_head_assets.php'; ?>
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<div class="admin-layout flex w-full h-screen overflow-hidden">
    <?php $currentPage = 'app-categories'; require __DIR__ . '/partials/sidebar.php'; ?>
    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-20 flex items-center justify-between px-8 bg-glass-bg border-b border-white/5 shrink-0 backdrop-blur-xl sticky top-0 z-[100]">
            <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/5 via-transparent to-transparent"></div>
            <div class="relative flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-cyan-500/10 flex items-center justify-center border border-cyan-500/20">
                    <i class="ph ph-cube text-2xl text-cyan-500 animate-pulse"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white tracking-tight">App Categories</h1>
                    <p class="text-[10px] text-white/40 uppercase tracking-widest font-black hidden sm:block">Product Taxonomy</p>
                </div>
            </div>
            <div class="relative flex items-center gap-4">
                <button onclick="openNewCategoryModal()" class="group flex items-center gap-2 px-3 sm:px-5 py-2.5 bg-cyan-500/10 hover:bg-cyan-500/20 border border-cyan-500/20 hover:border-cyan-500/40 rounded-xl transition-all duration-300">
                    <i class="ph ph-plus-circle text-lg text-cyan-500 group-hover:rotate-90 transition-transform duration-500"></i>
                    <span class="text-sm font-semibold text-cyan-500 hidden sm:inline">New Category</span>
                </button>
                <div class="h-8 w-px bg-white/10 mx-2"></div>
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

            <div class="admin-card p-0 overflow-hidden border-white/5">
                <div class="p-6 border-b border-white/5 flex items-center justify-between bg-white/[0.01]">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-cyan-500/10 flex items-center justify-center border border-cyan-500/20">
                            <i class="ph ph-folders text-cyan-500 text-xl"></i>
                        </div>
                        <h3 class="font-bold text-white tracking-tight text-lg">Category List</h3>
                    </div>
                    <div class="bg-white/5 px-4 py-1.5 rounded-full border border-white/10">
                        <span class="text-[10px] text-white/40 uppercase tracking-widest font-bold">Total Categories:</span>
                        <span class="text-sm font-mono text-cyan-500 font-bold ml-2"><?= count($categories) ?></span>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="admin-table min-w-[700px]">
                        <thead>
                            <tr>
                                <th class="w-16 text-center">Ord</th>
                                <th>Category Details</th>
                                <th>Color / Icon</th>
                                <th class="text-center">Products</th>
                                <th class="text-center">Status</th>
                                <th class="!pr-8 text-right">Operations</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            <?php if (empty($categories)): ?>
                                <tr><td colspan="6" class="text-center py-8 text-white/40">No categories found.</td></tr>
                            <?php else: ?>
                                <?php foreach ($categories as $c): ?>
                                    <tr class="group hover:bg-white/[0.02] transition-colors duration-300">
                                        <td class="text-center">
                                            <span class="text-xs font-mono text-white/40 bg-white/5 px-2 py-1 rounded-md border border-white/10"><?= $c['sort_order'] ?></span>
                                        </td>
                                        <td>
                                            <div class="flex items-center gap-4">
                                                <div class="w-10 h-10 rounded-xl bg-<?= $c['color'] ?: 'cyan' ?>-500/10 flex items-center justify-center border border-<?= $c['color'] ?: 'cyan' ?>-500/20 group-hover:scale-110 transition-transform duration-500">
                                                    <i class="ph <?= e($c['icon'] ?: 'ph-cube') ?> text-xl text-<?= $c['color'] ?: 'cyan' ?>-500"></i>
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-sm font-bold text-white group-hover:text-cyan-500 transition-colors"><?= e($c['name']) ?></span>
                                                    <span class="text-xs font-mono text-white/40">slug: <?= e($c['slug']) ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="flex items-center gap-2">
                                                <div class="w-3 h-3 rounded-full bg-<?= $c['color'] ?: 'cyan' ?>-500 shadow-[0_0_10px_rgba(var(--color-<?= $c['color'] ?: 'cyan' ?>-500),0.5)]"></div>
                                                <span class="text-xs font-mono text-white/60"><?= e($c['color']) ?></span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= baseUrl('admin/app-products?category='.$c['id']) ?>" class="inline-block bg-white/5 px-3 py-1 rounded-md border border-white/10 hover:bg-white/10 transition-colors">
                                                <span class="text-sm font-mono text-cyan-400"><?= $c['product_count'] ?></span>
                                                <span class="text-[10px] text-white/40 uppercase tracking-widest ml-1">Apps</span>
                                            </a>
                                        </td>
                                        <td class="text-center">
                                            <?php if($c['is_active']): ?>
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-500 text-[10px] font-bold uppercase tracking-widest border border-emerald-500/20">
                                                    Active
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/5 text-white/40 text-[10px] font-bold uppercase tracking-widest border border-white/10">
                                                    Inactive
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="!pr-8 text-right">
                                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <button type="button" 
                                                        onclick='editCategory(<?= json_encode([
                                                            "id" => $c["id"], 
                                                            "name" => $c["name"], 
                                                            "slug" => $c["slug"], 
                                                            "icon" => $c["icon"], 
                                                            "color" => $c["color"], 
                                                            "description" => $c["description"],
                                                            "sort_order" => $c["sort_order"],
                                                            "is_active" => $c["is_active"]
                                                        ]) ?>)' 
                                                        class="w-8 h-8 rounded-lg bg-cyan-500/10 flex items-center justify-center text-cyan-500 hover:bg-cyan-500 hover:text-black transition-all">
                                                    <i class="ph ph-pencil-simple"></i>
                                                </button>
                                                <?php if($c['product_count'] == 0): ?>
                                                    <button onclick="showDeleteModal('<?= e($c['name']) ?>', '<?= baseUrl('admin/app-categories?action=delete&id='.$c['id']) ?>')" class="w-8 h-8 rounded-lg bg-pink-500/10 flex items-center justify-center text-pink-500 hover:bg-pink-500 hover:text-white transition-all">
                                                        <i class="ph ph-trash"></i>
                                                    </button>
                                                <?php endif; ?>
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

<!-- Add/Edit Modal -->
<div id="add-category-modal" class="fixed inset-0 z-[200] hidden items-center justify-center">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeCategoryModal()"></div>
    <div class="relative w-full max-w-lg bg-[#0b0e14] border border-white/10 rounded-2xl shadow-2xl overflow-hidden transform transition-all p-6 scale-95 opacity-0 inline-block align-bottom sm:my-8 sm:align-middle" id="add-category-content">
        <div class="absolute top-0 right-0 p-8 opacity-5">
            <i class="ph ph-folders text-8xl text-cyan-500"></i>
        </div>
        
        <div class="relative flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-lg bg-cyan-500/10 flex items-center justify-center border border-cyan-500/20 text-cyan-500">
                <i class="ph ph-folder-plus text-xl"></i>
            </div>
            <h2 id="modal-title" class="text-xl font-bold text-white tracking-tight">Add New Category</h2>
        </div>

        <form method="POST" action="<?= baseUrl('admin/app-categories') ?>" class="relative space-y-6">
            <input type="hidden" name="id" id="cat-id" value="0">
            
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2 col-span-2">
                        <label class="text-xs font-semibold text-white/60 uppercase tracking-widest ml-1">Category Name</label>
                        <input type="text" name="name" id="cat-name" class="form-input bg-white/5 border-white/10 focus:border-cyan-500/50" required>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-white/60 uppercase tracking-widest ml-1">Slug (optional)</label>
                        <input type="text" name="slug" id="cat-slug" class="form-input bg-white/5 border-white/10 focus:border-cyan-500/50">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-white/60 uppercase tracking-widest ml-1">Sort Order</label>
                        <input type="number" name="sort_order" id="cat-sort" class="form-input bg-white/5 border-white/10 focus:border-cyan-500/50" value="0">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-white/60 uppercase tracking-widest ml-1">Phosphor Icon</label>
                        <input type="text" name="icon" id="cat-icon" class="form-input bg-white/5 border-white/10 focus:border-cyan-500/50" value="ph-cube" placeholder="e.g. ph-cube">
                    </div>
                    
                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-white/60 uppercase tracking-widest ml-1">Color Theme</label>
                        <select name="color" id="cat-color" class="form-input bg-white/5 border-white/10 focus:border-cyan-500/50">
                            <option value="cyan">Cyan</option>
                            <option value="violet">Violet</option>
                            <option value="emerald">Emerald</option>
                            <option value="pink">Pink</option>
                            <option value="orange">Orange</option>
                            <option value="cobalt">Cobalt</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-semibold text-white/60 uppercase tracking-widest ml-1">Description</label>
                    <textarea name="description" id="cat-desc" rows="2" class="form-input bg-white/5 border-white/10 focus:border-cyan-500/50"></textarea>
                </div>

                <div class="flex items-center pt-2">
                    <label class="relative flex items-center gap-3 cursor-pointer group">
                        <input type="checkbox" name="is_active" id="cat-active" class="peer hidden" checked>
                        <div class="w-10 h-5 bg-white/5 rounded-full border border-white/10 peer-checked:bg-cyan-500/20 peer-checked:border-cyan-500/40 transition-all duration-300"></div>
                        <div class="absolute left-1 top-1 w-3 h-3 bg-white/20 rounded-full peer-checked:left-6 peer-checked:bg-cyan-500 transition-all duration-300 shadow-lg"></div>
                        <span class="text-sm font-medium text-white/60 group-hover:text-white transition-colors">Category is Active</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-6 mt-6 border-t border-white/10">
                <button type="button" onclick="closeCategoryModal()" class="px-6 py-2.5 rounded-xl border border-white/10 text-white/60 hover:text-white hover:bg-white/5 transition-colors font-medium text-sm">Cancel</button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-500 hover:bg-cyan-500 hover:text-black transition-colors font-semibold shadow-[0_0_20px_rgba(236,72,153,0)] hover:shadow-[0_0_20px_rgba(6,182,212,0.2)] text-sm">Save Category</button>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/partials/_delete_modal.php'; ?>

<script>
    function editCategory(cat) {
        document.getElementById('modal-title').innerText = 'Edit Category';
        document.getElementById('cat-id').value = cat.id;
        document.getElementById('cat-name').value = cat.name;
        document.getElementById('cat-slug').value = cat.slug;
        document.getElementById('cat-icon').value = cat.icon;
        document.getElementById('cat-color').value = cat.color;
        document.getElementById('cat-sort').value = cat.sort_order;
        document.getElementById('cat-desc').value = cat.description;
        document.getElementById('cat-active').checked = cat.is_active == 1;

        const modal = document.getElementById('add-category-modal');
        const content = document.getElementById('add-category-content');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function openNewCategoryModal() {
        document.getElementById('modal-title').innerText = 'Add New Category';
        document.getElementById('cat-id').value = '0';
        document.getElementById('cat-name').value = '';
        document.getElementById('cat-slug').value = '';
        document.getElementById('cat-icon').value = 'ph-cube';
        document.getElementById('cat-color').value = 'cyan';
        document.getElementById('cat-sort').value = '0';
        document.getElementById('cat-desc').value = '';
        document.getElementById('cat-active').checked = true;

        const modal = document.getElementById('add-category-modal');
        const content = document.getElementById('add-category-content');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeCategoryModal() {
        const modal = document.getElementById('add-category-modal');
        const content = document.getElementById('add-category-content');
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.getElementById('modal-title').innerText = 'Add New Category';
            document.getElementById('cat-id').value = '0';
            document.getElementById('cat-name').value = '';
            document.getElementById('cat-slug').value = '';
            document.getElementById('cat-icon').value = 'ph-cube';
            document.getElementById('cat-color').value = 'cyan';
            document.getElementById('cat-sort').value = '0';
            document.getElementById('cat-desc').value = '';
            document.getElementById('cat-active').checked = true;
        }, 200);
    }
</script>
</body>
</html>
