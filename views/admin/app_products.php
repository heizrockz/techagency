<?php
// Ensure this file is only accessed through the controller
if (!defined('APP_NAME')) die('Direct access prevented');
?>
<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <title>App Products — <?= APP_NAME ?></title>
    <?php require __DIR__ . '/partials/_head_assets.php'; ?>
    <style>
        .form-tab { display: none; }
        .form-tab.active { display: block; }
        .tab-btn.active { 
            background: rgba(139, 92, 246, 0.1);
            color: #8b5cf6;
            border-color: rgba(139, 92, 246, 0.4);
        }
    </style>
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<div class="admin-layout flex w-full h-screen overflow-hidden">
    <?php $currentPage = 'app-products'; require __DIR__ . '/partials/sidebar.php'; ?>
    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-20 flex items-center justify-between px-8 bg-glass-bg border-b border-white/5 shrink-0 backdrop-blur-xl sticky top-0 z-[100]">
            <div class="absolute inset-0 bg-gradient-to-r from-violet-500/5 via-transparent to-transparent"></div>
            <div class="relative flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-violet-500/10 flex items-center justify-center border border-violet-500/20">
                    <i class="ph ph-app-window text-2xl text-violet-500 animate-pulse"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white tracking-tight">App Products</h1>
                    <p class="text-[10px] text-white/40 uppercase tracking-widest font-black hidden sm:block">Product Management</p>
                </div>
            </div>
            <div class="relative flex items-center gap-4">
                <a href="<?= baseUrl('admin/api-docs') ?>" class="group flex items-center gap-2 px-3 sm:px-5 py-2.5 bg-blue-500/10 hover:bg-blue-500/20 border border-blue-500/20 hover:border-blue-500/40 rounded-xl transition-all duration-300">
                    <i class="ph ph-code text-lg text-blue-500 group-hover:-rotate-12 transition-transform duration-500"></i>
                    <span class="text-sm font-semibold text-blue-500 hidden sm:inline">API Docs</span>
                </a>
                <a href="<?= baseUrl('admin/app-products?action=new') ?>" class="group flex items-center gap-2 px-3 sm:px-5 py-2.5 bg-violet-500/10 hover:bg-violet-500/20 border border-violet-500/20 hover:border-violet-500/40 rounded-xl transition-all duration-300">
                    <i class="ph ph-plus-circle text-lg text-violet-500 group-hover:rotate-90 transition-transform duration-500"></i>
                    <span class="text-sm font-semibold text-violet-500 hidden sm:inline">New Product</span>
                </a>
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

            <?php if ($action === 'edit' || $action === 'new'): ?>
                <?php 
                $editProduct = null;
                $gallery = [];
                if ($action === 'edit' && isset($_GET['id'])) {
                    $db = getDB();
                    $stmt = $db->prepare("SELECT * FROM app_products WHERE id = ?");
                    $stmt->execute([intval($_GET['id'])]);
                    $editProduct = $stmt->fetch();
                    
                    $gallery = $db->query("SELECT * FROM app_product_images WHERE product_id = " . intval($_GET['id']) . " ORDER BY sort_order")->fetchAll();
                }
                ?>
                <div class="max-w-4xl mx-auto pb-20">
                    <div class="admin-card relative overflow-hidden group">
                        <!-- Header & Tabs -->
                        <div class="flex items-center justify-between p-6 border-b border-white/5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-violet-500/10 flex items-center justify-center border border-violet-500/20 text-violet-500">
                                    <i class="ph ph-pencil-line text-xl"></i>
                                </div>
                                <h2 class="text-xl font-bold text-white"><?= $action === 'edit' ? 'Edit Product' : 'Add New Product' ?></h2>
                            </div>
                            
                            <div class="flex items-center bg-white/5 p-1 rounded-xl">
                                <button type="button" onclick="switchTab('general')" class="tab-btn active px-4 py-1.5 rounded-lg text-xs font-bold uppercase tracking-widest transition-all">General</button>
                                <button type="button" onclick="switchTab('arabic')" class="tab-btn px-4 py-1.5 rounded-lg text-xs font-bold uppercase tracking-widest transition-all text-violet-300">عربي</button>
                                <button type="button" onclick="switchTab('seo')" class="tab-btn px-4 py-1.5 rounded-lg text-xs font-bold uppercase tracking-widest transition-all">SEO & Vis</button>
                                <button type="button" onclick="switchTab('gallery')" class="tab-btn px-4 py-1.5 rounded-lg text-xs font-bold uppercase tracking-widest transition-all">Gallery</button>
                            </div>
                        </div>

                        <form method="POST" action="<?= baseUrl('admin/app-products') ?>" class="p-6" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?= $editProduct['id'] ?? 0 ?>">
                            
                            <!-- General Tab -->
                            <div id="tab-general" class="form-tab active space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <label class="text-[11px] font-bold text-white/40 uppercase tracking-widest ml-1">Product Name</label>
                                        <input type="text" name="name" class="form-input" required value="<?= e($editProduct['name'] ?? '') ?>" placeholder="e.g. Acme POS Pro">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-[11px] font-bold text-white/40 uppercase tracking-widest ml-1">Slug Identifier</label>
                                        <input type="text" name="slug" class="form-input" value="<?= e($editProduct['slug'] ?? '') ?>" placeholder="Auto-generated if empty">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-[11px] font-bold text-white/40 uppercase tracking-widest ml-1">Category</label>
                                        <select name="category_id" class="form-input">
                                            <option value="">Select Category...</option>
                                            <?php foreach ($categories as $cat): ?>
                                                <option value="<?= $cat['id'] ?>" <?= ($editProduct['category_id'] ?? 0) == $cat['id'] ? 'selected' : '' ?>>
                                                    <?= e($cat['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-[11px] font-bold text-white/40 uppercase tracking-widest ml-1">Version</label>
                                        <input type="text" name="version" class="form-input" value="<?= e($editProduct['version'] ?? '1.0.0') ?>">
                                    </div>
                                </div>

                                <div class="p-4 rounded-xl bg-violet-500/5 border border-violet-500/10 space-y-4">
                                    <h3 class="text-[10px] font-black uppercase tracking-widest text-violet-500">Visual Assets</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="space-y-2">
                                            <label class="text-[11px] font-bold text-white/40 uppercase tracking-widest ml-1">Icon Upload</label>
                                            <input type="file" name="icon_file" class="form-input !py-1.5 text-[10px]">
                                            <input type="text" name="icon_url" class="form-input text-xs" value="<?= e($editProduct['icon_url'] ?? '') ?>" placeholder="Or paste icon URL">
                                        </div>
                                        <div class="space-y-2">
                                            <label class="text-[11px] font-bold text-white/40 uppercase tracking-widest ml-1">Header/Cover Upload</label>
                                            <input type="file" name="header_file" class="form-input !py-1.5 text-[10px]">
                                            <input type="text" name="header_image" class="form-input text-xs" value="<?= e($editProduct['header_image'] ?? '') ?>" placeholder="Or paste header URL">
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="text-[11px] font-bold text-white/40 uppercase tracking-widest ml-1">Short Description</label>
                                    <textarea name="description" rows="2" class="form-input" placeholder="Brief summary for list view..."><?= e($editProduct['description'] ?? '') ?></textarea>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[11px] font-bold text-white/40 uppercase tracking-widest ml-1">Long Description (About Text)</label>
                                    <textarea name="long_description" rows="5" class="form-input" placeholder="Full descriptive text of the software..."><?= e($editProduct['long_description'] ?? '') ?></textarea>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[11px] font-bold text-white/40 uppercase tracking-widest ml-1">Requirements (Comma Separated)</label>
                                    <input type="text" name="os_requirements" class="form-input" placeholder="Windows 10+, 64-bit Architecture" value="<?= e($editProduct['os_requirements'] ?? 'Windows 10+, 64-bit Architecture') ?>">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[11px] font-bold text-white/40 uppercase tracking-widest ml-1">Full Specifications (One per line)</label>
                                    <textarea name="features" rows="4" class="form-input" placeholder="Feature 1\nFeature 2..."><?= e($editProduct['features'] ?? '') ?></textarea>
                                </div>

                                <div class="p-4 rounded-xl bg-emerald-500/5 border border-emerald-500/10 space-y-4">
                                    <h3 class="text-[10px] font-black uppercase tracking-widest text-emerald-500">Pricing & Distribution</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="space-y-2">
                                            <label class="text-[11px] font-bold text-white/40 uppercase tracking-widest ml-1">Pricing Model</label>
                                            <select name="pricing_model" class="form-input">
                                                <?php $models = ['free' => 'Free', 'one_time' => 'One-Time Purchase', 'monthly' => 'Monthly Subscription', 'yearly' => 'Yearly Subscription']; 
                                                foreach($models as $k => $v): ?>
                                                    <option value="<?= $k ?>" <?= ($editProduct['pricing_model'] ?? 'free') === $k ? 'selected' : '' ?>><?= $v ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="space-y-2">
                                            <label class="text-[11px] font-bold text-white/40 uppercase tracking-widest ml-1">Base Price ($)</label>
                                            <input type="number" step="0.01" name="price" class="form-input" value="<?= e($editProduct['price'] ?? '0.00') ?>">
                                        </div>
                                    </div>
                                    <div class="space-y-4">
                                        <label class="text-[11px] font-bold text-white/40 uppercase tracking-widest ml-1">Download URL / Build Upload</label>
                                        
                                        <!-- Advanced Uploader UI -->
                                        <div id="advancedUploader" class="border-2 border-dashed border-violet-500/30 rounded-xl p-6 text-center hover:bg-violet-500/5 transition-all relative overflow-hidden">
                                            <input type="file" id="softwareFileInput" name="software_file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="startChunkUpload(this)">
                                            
                                            <div id="uploaderStateIdle" class="flex flex-col items-center gap-2 pointer-events-none">
                                                <i class="ph ph-cloud-arrow-up text-3xl text-violet-500"></i>
                                                <span class="text-sm font-bold text-violet-400">Click or Drag to Upload App/Zip</span>
                                                <span class="text-[10px] text-white/40">Advanced chunked upload supports unlimited file sizes</span>
                                            </div>
                                            
                                            <div id="uploaderStateProgress" class="hidden flex-col items-center gap-3 w-full pointer-events-none">
                                                <div class="w-full bg-white/5 rounded-full h-3 overflow-hidden relative shadow-inner">
                                                    <div id="uploadProgressBar" class="bg-gradient-to-r from-violet-600 to-fuchsia-500 h-full w-0 transition-all duration-300 relative shadow-[0_0_10px_rgba(139,92,246,0.5)]">
                                                        <div class="absolute inset-0 bg-white/20 w-full animate-pulse"></div>
                                                    </div>
                                                </div>
                                                <div class="flex justify-between w-full text-[11px] font-bold uppercase tracking-widest text-violet-400">
                                                    <span id="uploadProgressText">0%</span>
                                                    <span id="uploadETA">Calculating...</span>
                                                </div>
                                            </div>
                                            
                                            <div id="uploaderStateDone" class="hidden flex-col items-center gap-2 pointer-events-none">
                                                <i class="ph ph-check-circle text-3xl text-emerald-500 drop-shadow-[0_0_10px_rgba(16,185,129,0.5)]"></i>
                                                <span class="text-sm font-bold text-emerald-500">Upload Complete!</span>
                                                <span id="uploadedFileName" class="text-[11px] text-white/60 font-mono"></span>
                                            </div>
                                            
                                            <div id="uploaderStateError" class="hidden flex-col items-center gap-2 pointer-events-none">
                                                <i class="ph ph-warning-circle text-3xl text-pink-500 drop-shadow-[0_0_10px_rgba(236,72,153,0.5)]"></i>
                                                <span class="text-sm font-bold text-pink-500" id="uploadErrorText">Upload Failed</span>
                                                <button type="button" onclick="resetUploader()" class="text-[10px] bg-pink-500/20 px-4 py-1.5 rounded-lg text-pink-400 font-bold uppercase tracking-widest pointer-events-auto hover:bg-pink-500/40 transition-colors mt-2">Retry Upload</button>
                                            </div>
                                        </div>
                                        
                                        <input type="text" id="downloadUrlInput" name="download_url" class="form-input text-xs" value="<?= e($editProduct['download_url'] ?? '') ?>" placeholder="Or paste direct download link">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-[11px] font-bold text-white/40 uppercase tracking-widest ml-1">Buy / Checkout URL</label>
                                        <input type="text" name="buy_url" class="form-input" value="<?= e($editProduct['buy_url'] ?? '') ?>" placeholder="Stripe/PayPal link">
                                    </div>
                                </div>
                            </div>

                            <!-- Arabic Tab -->
                            <div id="tab-arabic" class="form-tab hidden space-y-6" dir="rtl">
                                <div class="p-4 rounded-xl bg-violet-600/10 border border-violet-500/20 mb-4">
                                    <p class="text-sm text-violet-300 font-medium">الترجمة العربية. اترك الحقول فارغة للرجوع إلى اللغة الإنجليزية.</p>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[11px] font-bold text-white/40 uppercase tracking-widest ml-1">اسم المنتج</label>
                                    <input type="text" name="name_ar" class="form-input text-right" value="<?= e($editProduct['name_ar'] ?? '') ?>" placeholder="مثال: برنامج المبيعات">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[11px] font-bold text-white/40 uppercase tracking-widest ml-1">النبذة القصيرة</label>
                                    <textarea name="short_description_ar" rows="2" class="form-input text-right" placeholder="ملخص قصير..."><?= e($editProduct['short_description_ar'] ?? '') ?></textarea>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[11px] font-bold text-white/40 uppercase tracking-widest ml-1">الوصف الكامل</label>
                                    <textarea name="long_description_ar" rows="5" class="form-input text-right" placeholder="وصف مفصل للمنتج..."><?= e($editProduct['long_description_ar'] ?? '') ?></textarea>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[11px] font-bold text-white/40 uppercase tracking-widest ml-1">متطلبات التشغيل (مفصول بفواصل)</label>
                                    <input type="text" name="os_requirements_ar" class="form-input text-right" placeholder="ويندوز 10 فأحدث، معمارية 64 بت" value="<?= e($editProduct['os_requirements_ar'] ?? 'ويندوز 10 فأحدث، معمارية 64 بت') ?>">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[11px] font-bold text-white/40 uppercase tracking-widest ml-1">المميزات (ميزة واحدة في كل سطر)</label>
                                    <textarea name="features_ar" rows="4" class="form-input text-right" placeholder="ميزة 1\nميزة 2..."><?= e($editProduct['features_ar'] ?? '') ?></textarea>
                                </div>
                            </div>

                            <!-- SEO Tab -->
                            <div id="tab-seo" class="form-tab space-y-6">
                                <div class="p-6 rounded-2xl bg-white/5 border border-white/10 space-y-6">
                                    <h3 class="text-[10px] font-black uppercase tracking-widest text-white/60">Search Engine Optimization</h3>
                                    <div class="space-y-2">
                                        <label class="text-[11px] font-bold text-white/40 uppercase tracking-widest ml-1">Meta Description</label>
                                        <textarea name="meta_description" rows="3" class="form-input" placeholder="Enter specific description for Google search..."><?= e($editProduct['meta_description'] ?? '') ?></textarea>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-[11px] font-bold text-white/40 uppercase tracking-widest ml-1">Meta Keywords (comma separated)</label>
                                        <input type="text" name="meta_keywords" class="form-input" value="<?= e($editProduct['meta_keywords'] ?? '') ?>" placeholder="software, tool, efficiency, recovery">
                                    </div>
                                </div>

                                <div class="p-6 rounded-2xl bg-white/5 border border-white/10 space-y-4">
                                    <h3 class="text-[10px] font-black uppercase tracking-widest text-white/60">Visibility Settings</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <label class="flex items-center gap-3 p-4 rounded-xl bg-white/5 border border-white/5 hover:border-white/20 transition-all cursor-pointer">
                                            <input type="checkbox" name="is_public" class="w-4 h-4 rounded border-white/10" <?= ($editProduct['is_public'] ?? 1) ? 'checked' : '' ?>>
                                            <div>
                                                <p class="text-sm font-bold text-white">Publicly Listed</p>
                                                <p class="text-[10px] text-white/40">Visible in software store page</p>
                                            </div>
                                        </label>
                                        <label class="flex items-center gap-3 p-4 rounded-xl bg-white/5 border border-white/5 hover:border-white/20 transition-all cursor-pointer">
                                            <input type="checkbox" name="show_price" class="w-4 h-4 rounded border-white/10" <?= ($editProduct['show_price'] ?? 1) ? 'checked' : '' ?>>
                                            <div>
                                                <p class="text-sm font-bold text-white">Show Price</p>
                                                <p class="text-[10px] text-white/40">Display price tag on products</p>
                                            </div>
                                        </label>
                                        <label class="flex items-center gap-3 p-4 rounded-xl bg-white/5 border border-white/5 hover:border-white/20 transition-all cursor-pointer">
                                            <input type="checkbox" name="show_buy_button" class="w-4 h-4 rounded border-white/10" <?= ($editProduct['show_buy_button'] ?? 1) ? 'checked' : '' ?>>
                                            <div>
                                                <p class="text-sm font-bold text-white">Show Buy Button</p>
                                                <p class="text-[10px] text-white/40">Enable checkout redirect</p>
                                            </div>
                                        </label>
                                        <label class="flex items-center gap-3 p-4 rounded-xl bg-white/5 border border-white/5 hover:border-white/20 transition-all cursor-pointer">
                                            <input type="checkbox" name="is_active" class="w-4 h-4 rounded border-white/10" <?= ($editProduct['is_active'] ?? 1) ? 'checked' : '' ?>>
                                            <div>
                                                <p class="text-sm font-bold text-white">Internal Activation</p>
                                                <p class="text-[10px] text-white/40">Enable licensing system access</p>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Gallery Tab -->
                            <div id="tab-gallery" class="form-tab space-y-6">
                                <div class="p-6 rounded-2xl bg-white/5 border border-white/10 space-y-6">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-[10px] font-black uppercase tracking-widest text-white/60">Product Image Gallery</h3>
                                        <span class="text-[10px] text-white/20 px-2 py-0.5 rounded-full border border-white/10">Max 10 images</span>
                                    </div>
                                    
                                    <div id="galleryPreviewContainer" class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                        <?php foreach ($gallery as $img): ?>
                                            <div class="relative group aspect-video rounded-xl overflow-hidden bg-white/5 border border-white/10">
                                                <img src="<?= baseUrl($img['image_path']) ?>" class="w-full h-full object-cover">
                                                <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                                    <a href="#" onclick="deleteImage(<?= $img['id'] ?>)" class="w-8 h-8 rounded-lg bg-pink-500/20 hover:bg-pink-500/40 border border-pink-500/20 flex items-center justify-center text-pink-500">
                                                        <i class="ph ph-trash"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                        <label class="aspect-video rounded-xl border-2 border-dashed border-white/10 hover:border-violet-500/40 hover:bg-violet-500/5 transition-all flex flex-col items-center justify-center gap-2 cursor-pointer">
                                            <i class="ph ph-plus-circle text-2xl text-white/20"></i>
                                            <span class="text-[10px] font-bold text-white/20 uppercase">Add More</span>
                                            <input type="file" name="gallery_files[]" multiple class="hidden" onchange="previewGallery(this)">
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-4 mt-8 pt-8 border-t border-white/5">
                                <button type="submit" class="flex-1 bg-violet-500 hover:bg-violet-600 text-white font-bold py-3 rounded-xl transition-all shadow-lg shadow-violet-500/20">
                                    Save Product Details
                                </button>
                                <a href="<?= baseUrl('admin/app-products') ?>" class="px-8 py-3 bg-white/5 hover:bg-white/10 text-white font-bold rounded-xl transition-all border border-white/5">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <script>
                function switchTab(tabId) {
                    document.querySelectorAll('.form-tab').forEach(t => t.classList.remove('active'));
                    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                    document.getElementById('tab-' + tabId).classList.add('active');
                    event.currentTarget.classList.add('active');
                }

                function previewGallery(input) {
                    const container = document.getElementById('galleryPreviewContainer');
                    if (input.files) {
                        Array.from(input.files).forEach(file => {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                const div = document.createElement('div');
                                div.className = 'relative group aspect-video rounded-xl overflow-hidden bg-white/5 border border-white/10 opacity-60';
                                div.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <span class="text-[8px] font-black uppercase tracking-widest bg-black/40 px-2 py-1 rounded">Pending</span>
                                    </div>`;
                                container.insertBefore(div, container.lastElementChild);
                            }
                            reader.readAsDataURL(file);
                        });
                    }
                }

                function deleteImage(id) {
                    if (confirm('Delete this image from gallery?')) {
                        window.location.href = `<?= baseUrl('admin/app-products?action=delete_image&id=') ?>${id}&product_id=<?= $editProduct['id'] ?? 0 ?>`;
                    }
                    event.preventDefault();
                }

                // --- Chunked Uploader Logic ---
                function resetUploader() {
                    document.getElementById('uploaderStateIdle').classList.remove('hidden');
                    document.getElementById('uploaderStateProgress').classList.add('hidden');
                    document.getElementById('uploaderStateDone').classList.add('hidden');
                    document.getElementById('uploaderStateError').classList.add('hidden');
                    document.getElementById('softwareFileInput').value = '';
                    document.getElementById('softwareFileInput').classList.remove('hidden');
                }

                async function startChunkUpload(input) {
                    if (!input.files || input.files.length === 0) return;
                    const file = input.files[0];
                    
                    // Switch UI
                    document.getElementById('uploaderStateIdle').classList.add('hidden');
                    document.getElementById('uploaderStateProgress').classList.remove('hidden');
                    document.getElementById('uploaderStateDone').classList.add('hidden');
                    document.getElementById('uploaderStateError').classList.add('hidden');
                    document.getElementById('softwareFileInput').classList.add('hidden');
                    
                    // Check if file is really large; if so, bigger chunks, else 2MB
                    const chunkSize = 2 * 1024 * 1024; // 2MB
                    const totalChunks = Math.ceil(file.size / chunkSize);
                    const uploadId = Math.random().toString(36).substring(2) + Date.now().toString(36);
                    const fileName = file.name;
                    
                    let uploadedBytes = 0;
                    const startTime = Date.now();
                    
                    for (let chunkIndex = 0; chunkIndex < totalChunks; chunkIndex++) {
                        const start = chunkIndex * chunkSize;
                        const end = Math.min(start + chunkSize, file.size);
                        const chunk = file.slice(start, end);
                        
                        const formData = new FormData();
                        formData.append('chunk', chunk);
                        formData.append('chunkIndex', chunkIndex);
                        formData.append('totalChunks', totalChunks);
                        formData.append('uploadId', uploadId);
                        formData.append('fileName', fileName);
                        
                        try {
                            const response = await uploadChunk(formData);
                            
                            uploadedBytes += chunk.size;
                            const percent = Math.round((uploadedBytes / file.size) * 100);
                            document.getElementById('uploadProgressBar').style.width = percent + '%';
                            document.getElementById('uploadProgressText').innerText = percent + '%';
                            
                            // ETA
                            const elapsed = (Date.now() - startTime) / 1000;
                            const speed = uploadedBytes / elapsed; // bytes per sec
                            const remainingBytes = file.size - uploadedBytes;
                            const etaSec = Math.round(remainingBytes / speed);
                            
                            if (etaSec > 60) {
                                document.getElementById('uploadETA').innerText = `ETA: ${Math.round(etaSec/60)}m ${etaSec%60}s`;
                            } else {
                                document.getElementById('uploadETA').innerText = etaSec > 0 ? `ETA: ${etaSec}s` : 'Finalizing...';
                            }
                            
                            if (chunkIndex === totalChunks - 1) {
                                // Done!
                                const resData = JSON.parse(response);
                                if (resData.success) {
                                    document.getElementById('uploaderStateProgress').classList.add('hidden');
                                    document.getElementById('uploaderStateDone').classList.remove('hidden');
                                    document.getElementById('uploaderStateDone').classList.add('flex');
                                    document.getElementById('downloadUrlInput').value = resData.file_path;
                                    document.getElementById('uploadedFileName').innerText = fileName;
                                    // Remove the file from the original input so it doesn't upload again on form submit
                                    input.value = '';
                                } else {
                                    throw new Error(resData.error || 'Assembly failed');
                                }
                            }
                        } catch (e) {
                            console.error(e);
                            document.getElementById('uploaderStateProgress').classList.add('hidden');
                            document.getElementById('uploaderStateError').classList.remove('hidden');
                            document.getElementById('uploaderStateError').classList.add('flex');
                            document.getElementById('uploadErrorText').innerText = 'Upload Failed: ' + e.message;
                            input.value = ''; // Clear it so they have to try again
                            break;
                        }
                    }
                }

                function uploadChunk(formData) {
                    return new Promise((resolve, reject) => {
                        const xhr = new XMLHttpRequest();
                        xhr.open('POST', '<?= baseUrl('admin/app-products?action=upload_chunk') ?>', true);
                        
                        xhr.onload = function() {
                            if (xhr.status >= 200 && xhr.status < 300) {
                                resolve(xhr.responseText);
                            } else {
                                reject(new Error(xhr.responseText || 'Server error ' + xhr.status));
                            }
                        };
                        xhr.onerror = function() { reject(new Error('Network connection error')); };
                        xhr.send(formData);
                    });
                }
                </script>

            <?php else: ?>
                <!-- Product List View -->
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    <?php foreach ($products as $p): ?>
                        <div class="admin-card group hover:scale-[1.01] transition-transform duration-300">
                            <div class="p-6">
                                <div class="flex items-start justify-between mb-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-14 h-14 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center overflow-hidden">
                                            <?php if($p['icon_url']): ?>
                                                <img src="<?= strpos($p['icon_url'], 'http') === 0 ? e($p['icon_url']) : baseUrl($p['icon_url']) ?>" class="w-full h-full object-contain p-2">
                                            <?php else: ?>
                                                <i class="ph ph-cube text-3xl text-violet-500 group-hover:rotate-12 transition-transform"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-bold text-white group-hover:text-violet-400 transition-colors"><?= e($p['name']) ?></h3>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-widest" style="background: <?= $p['category_color'] ?>20; color: <?= $p['category_color'] ?>; border: 1px solid <?= $p['category_color'] ?>30;">
                                                    <?= e($p['category_name']) ?>
                                                </span>
                                                <span class="text-[10px] text-white/40 font-mono">v<?= e($p['version']) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex gap-1.5">
                                        <a href="<?= baseUrl('admin/app-products?action=edit&id=' . $p['id']) ?>" class="w-8 h-8 rounded-lg bg-white/5 hover:bg-violet-500/20 border border-white/5 flex items-center justify-center text-white/60 hover:text-violet-500 transition-all">
                                            <i class="ph ph-pencil-simple"></i>
                                        </a>
                                        <a href="<?= baseUrl('admin/app-products?action=delete&id=' . $p['id']) ?>" onclick="return confirm('Full delete?')" class="w-8 h-8 rounded-lg bg-white/5 hover:bg-pink-500/20 border border-white/5 flex items-center justify-center text-white/60 hover:text-pink-500 transition-all">
                                            <i class="ph ph-trash"></i>
                                        </a>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-3 gap-4 p-4 rounded-xl bg-white/[0.02] border border-white/5">
                                    <div class="text-center">
                                        <p class="text-[10px] font-black uppercase tracking-widest text-white/20 mb-1">Licenses</p>
                                        <p class="text-lg font-bold text-white"><?= $p['license_count'] ?></p>
                                    </div>
                                    <div class="text-center border-x border-white/5">
                                        <p class="text-[10px] font-black uppercase tracking-widest text-white/20 mb-1">Active</p>
                                        <p class="text-lg font-bold text-emerald-500"><?= $p['active_license_count'] ?></p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-[10px] font-black uppercase tracking-widest text-white/20 mb-1">Price</p>
                                        <p class="text-lg font-bold text-violet-500">$<?= number_format($p['price'], 0) ?></p>
                                    </div>
                                </div>

                                <div class="mt-6 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full <?= $p['is_active'] ? 'bg-emerald-500' : 'bg-pink-500' ?> animate-pulse"></div>
                                        <span class="text-[10px] font-black uppercase tracking-widest text-white/40"><?= $p['is_active'] ? 'Active' : 'Draft' ?></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="ph ph-globe text-white/20"></i>
                                        <span class="text-[10px] font-black uppercase tracking-widest <?= $p['is_public'] ? 'text-violet-400' : 'text-white/20' ?>"><?= $p['is_public'] ? 'Public' : 'Hidden' ?></span>
                                    </div>
                                </div>
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
