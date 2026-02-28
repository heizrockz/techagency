<?php
$pageTitle = 'CRM Products';
$currentPage = 'crm_products';
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
</head>
<body>
<div class="admin-layout flex w-full">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>
    <div class="crm-main leading-relaxed text-slate-300">
    <header class="h-16 flex items-center justify-between px-6 bg-[#1a2333] border-b border-white/5 shrink-0">
        <h1 class="text-xl font-semibold text-white tracking-tight flex items-center gap-2">
            <i class="ph ph-package text-primary"></i>
            Products & Items
        </h1>
        <button onclick="openProductModal()" class="btn-primary">
            <i class="ph ph-plus mr-2"></i> New Product
        </button>
    </header>

    <div class="flex-1 overflow-y-auto p-6 bg-slate-900/50 w-full h-full">
        <?php if ($flash = getFlash()): ?>
            <div class="mb-6 p-4 rounded-xl border border-emerald-500/20 bg-emerald-500/10 text-emerald-400 text-sm flex items-center gap-2">
                <i class="ph ph-check-circle text-lg"></i>
                <?= htmlspecialchars($flash) ?>
            </div>
        <?php endif; ?>

        <div class="bg-[#1a2333] border border-white/5 rounded-2xl p-6 shadow-xl w-full">
            <div class="w-full">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-white/10 text-slate-400 text-sm">
                            <th class="py-3 px-4 font-semibold">Product Name</th>
                            <th class="py-3 px-4 font-semibold">Category</th>
                            <th class="py-3 px-4 font-semibold text-right">Selling Price</th>
                            <th class="py-3 px-4 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <?php if (empty($items)): ?>
                            <tr>
                                <td colspan="4" class="py-8 text-center text-slate-500 italic">No products found. Add your first item.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($items as $item): ?>
                                <tr class="hover:bg-white/[0.02] transition-colors group">
                                    <td class="py-3 px-4">
                                        <div class="font-medium text-white"><?= htmlspecialchars($item['name']) ?></div>
                                        <?php if ($item['description']): ?>
                                            <div class="text-xs text-slate-500 mt-1 line-clamp-1"><?= htmlspecialchars($item['description']) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 px-4">
                                        <?php if ($item['category']): ?>
                                            <span class="bg-slate-800 text-xs px-2 py-1 rounded-full border border-white/5"><?= htmlspecialchars($item['category']) ?></span>
                                        <?php else: ?>
                                            <span class="text-slate-600 text-xs">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <span class="text-emerald-400 font-medium">$<?= number_format($item['price'], 2) ?></span>
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <button onclick='editProduct(<?= json_encode($item) ?>)' class="text-slate-400 hover:text-blue-400 mr-3 transition-colors">
                                            <i class="ph ph-pencil-simple text-lg"></i>
                                        </button>
                                        <form action="<?= htmlspecialchars(BASE_URL) ?>/admin/crm_products" method="POST" class="inline" onsubmit="return confirm('Delete this product?');">
                                            <input type="hidden" name="action" value="delete_item">
                                            <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                            <button type="submit" class="text-slate-400 hover:text-red-400 transition-colors">
                                                <i class="ph ph-trash text-lg"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Product Form Modal -->
<div id="productModal" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div class="bg-[#1a2333] border border-white/10 rounded-2xl w-full max-w-lg shadow-2xl scale-95 transition-transform duration-300 transform translate-y-0 relative">
        <div class="p-6 border-b border-white/10 flex items-center justify-between">
            <h3 id="modalTitle" class="text-lg font-bold text-white">Add Product</h3>
            <button type="button" onclick="closeProductModal()" class="text-slate-400 hover:text-white transition-colors">
                <i class="ph ph-x text-xl"></i>
            </button>
        </div>
        <form action="<?= htmlspecialchars(BASE_URL) ?>/admin/crm_products" method="POST" class="p-6">
            <input type="hidden" name="action" value="save_item">
            <input type="hidden" name="id" id="productId" value="">
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-slate-300 mb-1">Product Name *</label>
                    <input type="text" name="name" id="productName" required class="form-input" placeholder="e.g. Standard CMS Package">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Selling Price</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-slate-500">$</span>
                        <input type="number" step="0.01" name="price" id="productPrice" class="form-input pl-8" value="0.00">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Category</label>
                    <input type="text" name="category" id="productCategory" class="form-input" placeholder="e.g. Service">
                </div>
                
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-slate-300 mb-1">Internal Description</label>
                    <textarea name="description" id="productDescription" rows="3" class="form-input" placeholder="Notes for internal team..."></textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeProductModal()" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Save Product</button>
            </div>
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById('productModal');

    function openProductModal() {
        document.getElementById('modalTitle').innerText = 'Add Product';
        document.getElementById('productId').value = '';
        document.getElementById('productName').value = '';
        document.getElementById('productPrice').value = '0.00';
        document.getElementById('productCategory').value = '';
        document.getElementById('productDescription').value = '';
        
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.remove('opacity-0'), 10);
    }

    function editProduct(item) {
        document.getElementById('modalTitle').innerText = 'Edit Product: ' + item.name;
        document.getElementById('productId').value = item.id;
        document.getElementById('productName').value = item.name;
        document.getElementById('productPrice').value = item.price;
        document.getElementById('productCategory').value = item.category || '';
        document.getElementById('productDescription').value = item.description || '';
        
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.remove('opacity-0'), 10);
    }

    function closeProductModal() {
        modal.classList.add('opacity-0');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }
</script>
    </div>
</div>
</body>
</html>
