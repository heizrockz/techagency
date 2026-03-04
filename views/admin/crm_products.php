<?php
$pageTitle = 'CRM Products';
$currentPage = 'crm_products';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= htmlspecialchars($pageTitle . ' - ' . APP_NAME) ?></title>
    <?php require __DIR__ . '/partials/_head_assets.php'; ?>
</head>
<body class="bg-[#0b0e14]">
<div class="admin-layout flex w-full h-screen overflow-hidden">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>
    <div class="flex-1 flex flex-col min-w-0 bg-[#0b0e14]">
    <header class="h-20 flex items-center justify-between px-8 bg-glass-bg border-b border-white/5 shrink-0 backdrop-blur-xl sticky top-0 z-[100]">
        <div class="flex flex-col">
            <div class="text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] mb-1 hidden sm:block">Asset Inventory</div>
            <h1 class="text-xl font-black text-white tracking-tight flex items-center gap-3 group">
                <span class="text-neon-cyan drop-shadow-[0_0_8px_rgba(6,182,212,0.4)]">Registry</span>
                <span class="opacity-20 translate-y-px hidden sm:inline">/</span>
                <span class="text-[10px] tracking-widest text-slate-400 uppercase font-black hidden sm:inline-block">Item Ledger</span>
            </h1>
        </div>
        <div class="flex items-center gap-6">
            <button onclick="openProductModal()" class="px-3 sm:px-6 py-2.5 bg-neon-cyan hover:bg-cyan-400 text-black text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-lg active:scale-95 flex items-center gap-2">
                <i class="ph-bold ph-plus-circle text-lg"></i> <span class="hidden sm:inline">Provision</span>
            </button>
            <?php require __DIR__ . '/partials/_topbar.php'; ?>
        </div>
    </header>

    <div class="flex-1 overflow-y-auto p-6 bg-[#0b0e14] w-full h-full crm-main-scroll">
        <?php if ($flash = getFlash()): ?>
            <div class="mb-6 p-4 rounded-xl border border-emerald-500/20 bg-emerald-500/10 text-emerald-400 text-sm flex items-center gap-2">
                <i class="ph ph-check-circle text-lg"></i>
                <?= htmlspecialchars($flash) ?>
            </div>
        <?php endif; ?>

        <div class="admin-table-wrapper backdrop-blur-xl border border-white/5 rounded-3xl overflow-hidden shadow-premium">
            <div class="overflow-x-auto crm-main-scroll w-full">
                <table class="admin-table w-full text-left border-collapse min-w-[800px]">
                    <thead>
                        <tr class="text-slate-600 text-[8px] font-black uppercase tracking-[0.4em] bg-white/[0.01]">
                            <th class="py-6 px-10">Entity Identification</th>
                            <th class="py-6 px-6 text-center">Protocol Class</th>
                            <th class="py-6 px-6 text-right tracking-[0.2em]">Standard Valuation</th>
                            <th class="py-6 px-10 text-right tracking-[0.3em]">Navigation</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/[0.02]">
                        <?php if (empty($items)): ?>
                            <tr>
                                <td colspan="4" class="py-32 text-center">
                                    <div class="text-slate-700 text-[10px] font-black uppercase tracking-widest animate-pulse italic">No active entities detected in sector.</div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($items as $item): ?>
                                <tr class="hover:bg-white/[0.03] transition-all group/row border-b border-white/[0.03] last:border-0 relative">
                                    <td class="py-6 px-10" data-label="Asset">
                                        <div class="flex items-center gap-6">
                                            <div class="w-12 h-12 rounded-2xl bg-black/40 border border-white/10 flex items-center justify-center text-xl shadow-inner group-hover/row:border-neon-cyan/40 group-hover/row:bg-neon-cyan/5 transition-all duration-300">
                                                <i class="ph-bold ph-package text-neon-cyan group-hover/row:scale-110 transition-transform"></i>
                                            </div>
                                            <div>
                                                <div class="font-black text-white text-[11px] uppercase tracking-wider group-hover/row:text-neon-cyan transition-colors mb-1"><?= htmlspecialchars($item['name']) ?></div>
                                                <?php if ($item['description']): ?>
                                                    <div class="text-[9px] text-slate-600 font-bold tracking-wide line-clamp-1 max-w-[320px]"><?= htmlspecialchars($item['description']) ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-6 px-6 text-center" data-label="Protocol">
                                        <?php if ($item['category']): ?>
                                            <span class="inline-flex px-3 py-1 rounded-lg bg-white/5 border border-white/10 text-[8px] text-slate-400 font-black uppercase tracking-widest hover:border-neon-cyan/30 transition-colors">
                                                <?= htmlspecialchars($item['category']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-slate-800 text-[8px] font-black uppercase tracking-[0.2em] italic">UNCLASSIFIED</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-6 px-6 text-right" data-label="Valuation">
                                        <div class="flex flex-col items-end">
                                            <span class="text-white font-black text-xl tracking-tighter group-hover/row:text-neon-emerald transition-colors font-mono">$<?= number_format($item['price'], 2) ?></span>
                                            <span class="text-[8px] text-slate-700 font-bold uppercase tracking-widest mt-1">RATE PER UNIT (USD)</span>
                                        </div>
                                    </td>
                                    <td class="py-6 px-10 text-right" data-label="Navigation">
                                        <div class="flex justify-end gap-3 opacity-0 group-hover/row:opacity-100 transition-all translate-x-4 group-hover/row:translate-x-0">
                                            <button onclick='editProduct(<?= json_encode($item) ?>)' class="w-10 h-10 rounded-xl bg-neon-cyan/10 text-neon-cyan hover:bg-neon-cyan hover:text-black border border-neon-cyan/20 transition-all flex items-center justify-center shadow-lg active:scale-90" title="Modify Intel">
                                                <i class="ph-bold ph-pencil-simple text-lg"></i>
                                            </button>
                                            <form action="<?= htmlspecialchars(BASE_URL) ?>/admin/crm_products" method="POST" class="inline" id="deleteForm_<?= $item['id'] ?>">
                                                <input type="hidden" name="action" value="delete_item">
                                                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                                <button type="button" onclick="showDeleteModal('ENTITY-<?= $item['id'] ?>', 'deleteForm_<?= $item['id'] ?>')" class="w-10 h-10 rounded-xl bg-neon-rose/5 text-neon-rose hover:bg-neon-rose hover:text-white border border-neon-rose/20 transition-all flex items-center justify-center shadow-lg active:scale-90">
                                                    <i class="ph-bold ph-trash text-lg"></i>
                                                </button>
                                            </form>
                                        </div>
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
<style>
    /* Desktop-first: ensure table looks good on large screens */
    @media screen and (min-width: 1025px) {
        .admin-table { min-width: 1000px; }
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
    </div>
</div>

<?php require __DIR__ . '/partials/_delete_modal.php'; ?>
</body>
</html>
