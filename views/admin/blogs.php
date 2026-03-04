<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <title>Blogs — <?= APP_NAME ?></title>
    <?php require __DIR__ . '/partials/_head_assets.php'; ?>
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<div class="admin-layout flex w-full h-screen overflow-hidden">
    <?php $currentPage = 'blogs'; require __DIR__ . '/partials/sidebar.php'; ?>
    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-auto lg:h-20 flex flex-col lg:flex-row items-center justify-between px-4 lg:px-8 bg-glass-bg border-b border-white/5 shrink-0 backdrop-blur-xl sticky top-0 z-[100] py-4 lg:py-0 gap-4 lg:gap-0">
            <div class="flex items-center justify-between w-full lg:w-auto">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center border border-primary/20 text-primary">
                        <i class="ph ph-books text-2xl animate-pulse"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-white tracking-tight">Knowledge Base</h1>
                        <p class="text-[10px] text-white/40 uppercase tracking-widest font-black hidden sm:block">Article Management</p>
                    </div>
                </div>
                <div class="lg:hidden">
                    <?php require __DIR__ . '/partials/_topbar.php'; ?>
                </div>
            </div>

            <div class="flex items-center justify-between lg:justify-end gap-6 w-full lg:w-auto">
                <a href="<?= baseUrl('admin/blogs?action=new') ?>" class="flex-1 lg:flex-none px-6 py-2 bg-primary/10 hover:bg-primary/20 border border-primary/20 hover:border-primary/40 rounded-xl transition-all duration-300 flex items-center justify-center gap-2">
                    <i class="ph ph-note-pencil text-lg text-primary"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest text-primary">New Article</span>
                </a>
                <div class="hidden lg:block h-8 w-px bg-white/10 mx-2"></div>
                <div class="hidden lg:block">
                    <?php require __DIR__ . '/partials/_topbar.php'; ?>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-8 crm-main-scroll bg-[#0b0e14]">
            <?php if ($saved): ?>
                <div class="mb-8 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl flex items-center gap-3 animate-in fade-in slide-in-from-top-4">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/20 flex items-center justify-center">
                        <i class="ph ph-broadcast text-emerald-500"></i>
                    </div>
                    <p class="text-emerald-500 font-medium">Article sequence successfully propagated to the global network.</p>
                </div>
            <?php endif; ?>

            <?php if ($action === 'edit' || $action === 'new'): ?>
                <div class="max-w-6xl mx-auto">
                    <div class="admin-card relative overflow-hidden group">
                        <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:opacity-10 transition-opacity">
                            <i class="ph ph-article text-8xl text-primary"></i>
                        </div>
                        
                        <div class="relative flex items-center gap-3 mb-8">
                            <i class="ph ph-pencil-line text-xl text-primary"></i>
                            <h2 class="text-lg font-bold text-white"><?= $action === 'edit' ? 'Edit Article Parameters' : 'Article Metadata' ?></h2>
                        </div>

                        <form method="POST" action="<?= baseUrl('admin/blogs') ?>" enctype="multipart/form-data" class="relative space-y-10">
                            <input type="hidden" name="id" value="<?= $editBlog['id'] ?? 0 ?>">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
                                <div class="space-y-3">
                                    <label class="text-sm font-bold text-white/50 uppercase tracking-widest ml-1">Article URI Path (Slug)</label>
                                    <div class="relative group/input">
                                        <i class="ph ph-globe absolute left-4 top-1/2 -translate-y-1/2 text-primary/50 group-focus-within/input:text-primary transition-colors"></i>
                                    <input type="text" name="slug" class="form-input !pl-12 !h-12 !rounded-xl" value="<?= e($editBlog['slug'] ?? '') ?>" placeholder="e.g. quantum-computing-trends">
                                    </div>
                                    <p class="text-[10px] text-white/20 uppercase tracking-widest font-black ml-1">Leave empty for automatic generation</p>
                                </div>

                                <div class="space-y-3">
                                    <label class="text-sm font-bold text-white/50 uppercase tracking-widest ml-1">Author Identity</label>
                                    <div class="relative group/input">
                                        <i class="ph ph-user-focus absolute left-4 top-1/2 -translate-y-1/2 text-primary/50 group-focus-within/input:text-primary transition-colors"></i>
                                        <input type="text" name="author_name" class="form-input !pl-12" value="<?= e($editBlog['author_name'] ?? '') ?>" placeholder="Project Lead Alpha">
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    <label class="text-sm font-bold text-white/50 uppercase tracking-widest ml-1">Priority Sequence</label>
                                    <div class="relative group/input">
                                        <i class="ph ph-sort-ascending absolute left-4 top-1/2 -translate-y-1/2 text-primary/50 group-focus-within/input:text-primary transition-colors"></i>
                                        <input type="number" name="sort_order" class="form-input !pl-12" value="<?= $editBlog['sort_order'] ?? 0 ?>">
                                    </div>
                                </div>

                                <div class="flex items-center gap-10 py-4 bg-white/[0.02] border border-white/5 rounded-2xl px-6 self-end">
                                    <label class="relative flex items-center gap-4 cursor-pointer group">
                                        <input type="checkbox" name="is_active" class="peer hidden" <?= (!isset($editBlog) || $editBlog['is_active']) ? 'checked' : '' ?>>
                                        <div class="w-14 h-7 bg-white/5 rounded-full border border-white/10 peer-checked:bg-primary/20 peer-checked:border-primary/40 transition-all duration-300"></div>
                                        <div class="absolute left-1.5 top-1.5 w-4 h-4 bg-white/20 rounded-full peer-checked:left-8.5 peer-checked:bg-primary transition-all duration-300 shadow-[0_0_10px_rgba(var(--primary-rgb),0.5)]"></div>
                                        <span class="text-xs font-black text-white/40 group-hover:text-white uppercase tracking-widest transition-colors">Propagation Status</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Media Gallery Section -->
                            <div class="p-8 rounded-3xl bg-primary/5 border border-primary/20 relative overflow-hidden group/gallery">
                                <div class="absolute top-0 right-0 p-6 opacity-5">
                                    <i class="ph ph-images-square text-7xl"></i>
                                </div>
                                <div class="flex items-center justify-between mb-8 relative">
                                    <div class="flex items-center gap-4">
                                        <i class="ph ph-camera text-2xl text-primary"></i>
                                        <div>
                                            <h3 class="text-base font-black text-white uppercase tracking-widest">Multimedia Gallery</h3>
                                            <p class="text-[10px] text-primary/50 font-black uppercase tracking-widest mt-1">Image & Video Assets</p>
                                        </div>
                                    </div>
                                    <button type="button" onclick="addMediaRow()" class="px-6 py-2 bg-primary/10 border border-primary/20 rounded-xl text-primary text-[10px] font-black uppercase tracking-widest hover:bg-primary/20 transition-all flex items-center gap-2">
                                        <i class="ph ph-plus-circle text-lg"></i> Attach Asset
                                    </button>
                                </div>

                                <div id="media-rows-container" class="space-y-6">
                                    <?php 
                                    $mediaItems = $editBlog['media'] ?? [];
                                    if (empty($mediaItems) && !empty($editBlog['media_url'])) {
                                        $mediaItems = [['media_type' => $editBlog['media_type'], 'media_url' => $editBlog['media_url'], 'sort_order' => 0]];
                                    }
                                    ?>
                                    <?php if (empty($mediaItems)): ?>
                                        <!-- Initial Row logic handled by JS or fallback -->
                                    <?php else: ?>
                                        <?php foreach ($mediaItems as $idx => $m): ?>
                                            <div class="media-row p-6 bg-white/[0.03] border border-white/5 rounded-2xl group/item relative hover:border-primary/30 transition-colors">
                                                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
                                                    <div class="space-y-2">
                                                        <label class="text-[10px] font-black text-white/30 uppercase tracking-widest ml-1">Stream Type</label>
                                                        <select name="media_items[<?= $idx ?>][type]" class="form-input text-xs">
                                                            <option value="image" <?= $m['media_type'] === 'image' ? 'selected' : '' ?>>Static Image</option>
                                                            <option value="video" <?= $m['media_type'] === 'video' ? 'selected' : '' ?>>Raw Video Stream</option>
                                                            <option value="video_link" <?= $m['media_type'] === 'video_link' ? 'selected' : '' ?>>External Video Link</option>
                                                        </select>
                                                    </div>
                                                    <div class="space-y-2">
                                                        <label class="text-[10px] font-black text-white/30 uppercase tracking-widest ml-1">Inject Physical File</label>
                                                        <input type="file" name="media_files[<?= $idx ?>]" class="form-input text-xs">
                                                    </div>
                                                    <div class="space-y-2">
                                                        <label class="text-[10px] font-black text-white/30 uppercase tracking-widest ml-1">Remote Link Endpoint</label>
                                                        <input type="text" name="media_items[<?= $idx ?>][url]" class="form-input text-xs" value="<?= e($m['media_url']) ?>" placeholder="https://...">
                                                    </div>
                                                    <div class="flex items-center gap-4">
                                                        <div class="flex-1 space-y-2">
                                                            <label class="text-[10px] font-black text-white/30 uppercase tracking-widest ml-1">Priority</label>
                                                            <input type="number" name="media_items[<?= $idx ?>][sort]" class="form-input text-xs" value="<?= $m['sort_order'] ?>">
                                                        </div>
                                                        <button type="button" onclick="this.closest('.media-row').remove()" class="w-10 h-10 rounded-xl bg-pink-500/10 flex items-center justify-center text-pink-500 border border-pink-500/10 hover:bg-pink-500 hover:text-black transition-all mt-6">
                                                            <i class="ph ph-x text-lg"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <script>
                                let mediaRowCount = <?= max(count($mediaItems), 1) ?>;
                                function addMediaRow() {
                                    const container = document.getElementById('media-rows-container');
                                    const row = document.createElement('div');
                                    row.className = 'media-row p-6 bg-white/[0.03] border border-white/5 rounded-2xl group/item relative hover:border-primary/30 transition-colors animate-in fade-in zoom-in-95 duration-300';
                                    row.innerHTML = `
                                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
                                            <div class="space-y-2">
                                                <label class="text-[10px] font-black text-white/30 uppercase tracking-widest ml-1">Stream Type</label>
                                                <select name="media_items[${mediaRowCount}][type]" class="form-input text-xs">
                                                    <option value="image">Static Image</option>
                                                    <option value="video">Raw Video Stream</option>
                                                    <option value="video_link">External Video Link</option>
                                                </select>
                                            </div>
                                            <div class="space-y-2">
                                                <label class="text-[10px] font-black text-white/30 uppercase tracking-widest ml-1">Inject Physical File</label>
                                                <input type="file" name="media_files[${mediaRowCount}]" class="form-input text-xs">
                                            </div>
                                            <div class="space-y-2">
                                                <label class="text-[10px] font-black text-white/30 uppercase tracking-widest ml-1">Remote Link Endpoint</label>
                                                <input type="text" name="media_items[${mediaRowCount}][url]" class="form-input text-xs" placeholder="https://...">
                                            </div>
                                            <div class="flex items-center gap-4">
                                                <div class="flex-1 space-y-2">
                                                    <label class="text-[10px] font-black text-white/30 uppercase tracking-widest ml-1">Priority</label>
                                                    <input type="number" name="media_items[${mediaRowCount}][sort]" class="form-input text-xs" value="${mediaRowCount}">
                                                </div>
                                                <button type="button" onclick="this.closest('.media-row').remove()" class="w-10 h-10 rounded-xl bg-pink-500/10 flex items-center justify-center text-pink-500 border border-pink-500/10 hover:bg-pink-500 hover:text-black transition-all mt-6">
                                                    <i class="ph ph-x text-lg"></i>
                                                </button>
                                            </div>
                                        </div>
                                    `;
                                    container.appendChild(row);
                                    mediaRowCount++;
                                }
                            </script>

                            <div class="space-y-12">
                                <?php foreach (SUPPORTED_LOCALES as $loc): ?>
                                    <div class="p-10 rounded-[2.5rem] bg-white/[0.02] border border-white/5 relative overflow-hidden group/loc">
                                        <div class="absolute top-0 right-0 p-10 opacity-5 font-black text-[5rem] select-none uppercase"><?= $loc ?></div>
                                        
                                        <div class="flex items-center gap-4 mb-10 relative">
                                            <div class="w-1 h-10 bg-gradient-to-b from-primary to-transparent rounded-full"></div>
                                            <div>
                                                <h4 class="text-sm font-black text-white uppercase tracking-[0.4em]"><?= strtoupper($loc) ?> Language</h4>
                                                <p class="text-[10px] text-white/20 font-black uppercase tracking-widest mt-1">Translation Module</p>
                                            </div>
                                        </div>

                                        <div class="space-y-10 relative">
                                            <div class="space-y-4">
                                                <label class="text-xs font-black text-white/30 uppercase tracking-[0.2em] ml-2">Article Headline</label>
                                                <input type="text" name="title_<?= $loc ?>" class="form-input !text-xl !font-black !h-14 !px-8 !rounded-2xl <?= $loc === 'ar' ? 'rtl-input' : '' ?>" value="<?= e($editBlog['translations'][$loc]['title'] ?? '') ?>" required>
                                            </div>
                                            
                                            <div class="space-y-4">
                                                <label class="text-xs font-black text-white/30 uppercase tracking-[0.2em] ml-2">Short Description</label>
                                                <textarea name="desc_<?= $loc ?>" class="form-input !px-8 !py-6 !rounded-2xl min-h-[100px] text-base font-medium opacity-70 leading-relaxed <?= $loc === 'ar' ? 'rtl-input' : '' ?>" rows="3"><?= e($editBlog['translations'][$loc]['description'] ?? '') ?></textarea>
                                            </div>

                                            <div class="space-y-4">
                                                <label class="text-xs font-black text-white/30 uppercase tracking-[0.2em] ml-2">Main Content</label>
                                                <textarea name="content_<?= $loc ?>" class="form-input !px-8 !py-8 !rounded-3xl min-h-[400px] text-base leading-loose font-light <?= $loc === 'ar' ? 'rtl-input' : '' ?>" rows="12"><?= e($editBlog['translations'][$loc]['content'] ?? '') ?></textarea>
                                                <div class="flex items-center gap-2 text-primary/40 text-[10px] font-black uppercase tracking-widest mt-2 ml-2">
                                                    <i class="ph ph-info"></i> Full HTML and Markdown support enabled for data formatting
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="flex items-center gap-6 pt-12 border-t border-white/5 justify-end">
                                <a href="<?= baseUrl('admin/blogs') ?>" class="px-10 py-5 bg-white/5 text-white/40 font-black uppercase tracking-[0.2em] text-[10px] rounded-2xl hover:bg-white/10 transition-all border border-white/5">
                                    Abort Operation
                                </a>
                                <button type="submit" class="group relative px-16 py-5 bg-primary text-black font-black uppercase tracking-[0.3em] text-[10px] rounded-2xl hover:scale-105 transition-all shadow-[0_0_50px_rgba(var(--primary-rgb),0.3)] overflow-hidden">
                                    <span class="relative z-10">Authorize Publication</span>
                                    <div class="absolute inset-0 bg-white translate-y-full group-hover:translate-y-0 transition-transform duration-500 -z-0"></div>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <div class="admin-card p-0 overflow-hidden border-white/5">
                    <div class="p-10 border-b border-white/5 flex items-center justify-between bg-white/[0.01] relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-r from-primary/5 via-transparent to-transparent"></div>
                        <div class="relative flex items-center gap-6">
                            <div class="w-16 h-16 rounded-2xl bg-primary/10 flex items-center justify-center border border-primary/20 text-primary">
                                <i class="ph ph-stack text-3xl animate-pulse"></i>
                            </div>
                            <div>
                                <h3 class="font-black text-white text-3xl tracking-tight leading-none">Article Repository</h3>
                                <p class="text-[10px] text-white/20 uppercase tracking-[0.5em] font-black mt-3">Global Content Management Matrix</p>
                            </div>
                        </div>
                        <div class="relative flex items-center gap-8">
                            <div class="flex flex-col items-end gap-1">
                                <span class="text-[10px] text-white/20 font-black uppercase tracking-widest">Active Threads</span>
                                <span class="text-3xl font-mono text-primary font-black"><?= str_pad(count($blogs), 2, '0', STR_PAD_LEFT) ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th class="!pl-10 h-16 uppercase tracking-[0.2em] text-[10px] font-black text-white/30">Article Details</th>
                                    <th class="h-16 uppercase tracking-[0.2em] text-[10px] font-black text-white/30">Translations</th>
                                    <th class="h-16 uppercase tracking-[0.2em] text-[10px] font-black text-white/30">Engagement</th>
                                    <th class="h-16 uppercase tracking-[0.2em] text-[10px] font-black text-white/30">Status</th>
                                    <th class="!pr-10 text-right h-16 uppercase tracking-[0.2em] text-[10px] font-black text-white/30">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                <?php if (empty($blogs)): ?>
                                    <tr>
                                        <td colspan="5" class="p-32 text-center bg-white/[0.01]">
                                            <div class="flex flex-col items-center gap-8 opacity-20">
                                                <i class="ph ph-article-medium text-9xl"></i>
                                                <p class="text-white font-black text-2xl uppercase tracking-[0.3em]">No Articles Found</p>
                                                <a href="<?= baseUrl('admin/blogs?action=new') ?>" class="px-10 py-4 bg-primary text-black font-black uppercase text-[10px] tracking-[0.2em] rounded-2xl shadow-xl">Create First Article</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>

                                <?php foreach ($blogs as $b): ?>
                                    <tr class="group hover:bg-white/[0.03] transition-all duration-500">
                                        <td class="!pl-10 py-8" data-label="Article">
                                            <div class="flex items-center gap-8">
                                                <div class="relative w-16 h-16 lg:w-20 lg:h-20 shrink-0 group-hover:scale-105 transition-all duration-700">
                                                    <?php 
                                                    $thumb = $b['image_thumbnail_url'] ?? null;
                                                    ?>
                                                    <div class="w-full h-full bg-white/5 rounded-2xl p-1 border border-white/10 group-hover:border-primary/40 transition-colors">
                                                        <?php if($thumb): ?>
                                                            <img src="<?= e($thumb) ?>" class="w-full h-full object-cover rounded-xl">
                                                        <?php else: ?>
                                                            <div class="w-full h-full bg-primary/5 rounded-xl flex items-center justify-center border border-primary/20">
                                                                <i class="ph ph-article text-2xl text-primary/40"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="text-white font-black text-lg tracking-tight group-hover:text-primary transition-colors truncate max-w-[300px] lg:max-w-[450px]">
                                                        <?= e($b['trans'] ?: 'Unnamed Article') ?>
                                                    </div>
                                                    <div class="flex items-center gap-4 mt-3">
                                                        <div class="flex items-center gap-2 text-[10px] text-white/40 font-black uppercase tracking-widest bg-white/5 px-3 py-1 rounded-xl border border-white/5">
                                                            <i class="ph ph-identification-badge text-sm"></i> By <?= e($b['author_name'] ?? 'CORE_SYS') ?>
                                                        </div>
                                                        <div class="text-[10px] text-primary/40 font-mono tracking-tighter border-l border-white/10 pl-4">/node:<?= e($b['slug'] ?? 'unknown') ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td data-label="Locales">
                                            <div class="flex flex-col gap-2">
                                                <div class="flex gap-2">
                                                    <?php foreach(SUPPORTED_LOCALES as $l): ?>
                                                        <div class="w-9 h-9 rounded-xl bg-white/5 border border-white/5 flex items-center justify-center text-[10px] font-black <?= rand(0,1)?'text-primary':'text-white/20' ?> uppercase transition-colors group-hover:border-primary/20"><?= $l ?></div>
                                                    <?php endforeach; ?>
                                                </div>
                                                <span class="text-[9px] text-white/20 font-black uppercase tracking-[0.2em] ml-1">Linguistic Status</span>
                                            </div>
                                        </td>
                                        <td data-label="Views">
                                            <div class="flex flex-col gap-1">
                                                <div class="text-white font-black text-lg font-mono tracking-tighter"><?= number_format($b['view_count'] ?? 0) ?></div>
                                                <div class="text-[9px] text-primary/40 font-black uppercase tracking-widest">Network Hits</div>
                                            </div>
                                        </td>
                                        <td data-label="Status">
                                            <?php if ($b['is_active']): ?>
                                                <div class="flex items-center gap-3 text-emerald-500">
                                                    <div class="relative flex h-3 w-3">
                                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500 shadow-[0_0_12px_rgba(16,185,129,0.7)]"></span>
                                                    </div>
                                                    <span class="text-[10px] font-black uppercase tracking-[0.3em]">Operational</span>
                                                </div>
                                            <?php else: ?>
                                                <div class="flex items-center gap-3 text-white/10">
                                                    <div class="h-3 w-3 rounded-full bg-white/10"></div>
                                                    <span class="text-[10px] font-black uppercase tracking-[0.3em]">Encrypted</span>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="!pr-10 text-right" data-label="Operations">
                                            <div class="flex items-center justify-end gap-3 lg:opacity-0 lg:group-hover:opacity-100 transition-all lg:translate-x-12 lg:group-hover:translate-x-0">
                                                <a href="<?= baseUrl('admin/blogs?action=edit&id='.$b['id']) ?>" class="w-12 h-12 lg:w-14 lg:h-14 rounded-xl lg:rounded-2xl bg-cyan-500/10 flex items-center justify-center border border-cyan-500/20 text-cyan-500 hover:bg-cyan-500 hover:text-black transition-all duration-400" title="Edit Article">
                                                    <i class="ph ph-pencil-simple text-xl lg:text-2xl"></i>
                                                </a>
                                                <button onclick="showDeleteModal('this article', '<?= baseUrl('admin/blogs?action=delete&id='.$b['id']) ?>')" class="w-12 h-12 lg:w-14 lg:h-14 rounded-xl lg:rounded-2xl bg-pink-500/10 flex items-center justify-center border border-pink-500/20 text-pink-500 hover:bg-pink-500 hover:text-black transition-all duration-400" title="Delete Article">
                                                    <i class="ph ph-trash text-xl lg:text-2xl"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <style>
<style>
    /* Desktop-first: ensure table looks good on large screens */
    @media screen and (min-width: 1025px) {
        .admin-table { min-width: 1200px; }
    }

    /* Mobile-responsive card transformation */
                                    @media (max-width: 1024px) {
                                        .admin-table-wrapper { border-radius: 1.5rem !important; margin: 0 !important; }
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
                                            border-radius: 1.25rem !important; 
                                            padding: 20px !important;
                                            border: 1px solid rgba(255,255,255,0.05) !important;
                                        }
                                        
                                        .admin-table td { 
                                            display: flex !important; 
                                            justify-content: space-between !important; 
                                            align-items: center !important; 
                                            padding: 12px 0 !important; 
                                            border-bottom: 1px solid rgba(255,255,255,0.05) !important;
                                            text-align: right !important;
                                            font-size: 0.85rem !important;
                                        }
                                        
                                        .admin-table td:last-child { border-bottom: none !important; padding-top: 15px !important; }
                                        
                                        .admin-table td::before { 
                                            content: attr(data-label) !important; 
                                            font-weight: 900 !important; 
                                            text-transform: uppercase !important; 
                                            font-size: 0.6rem !important; 
                                            color: var(--neon-cyan) !important;
                                            letter-spacing: 1.5px !important;
                                            opacity: 0.5 !important;
                                            text-align: left !important;
                                            margin-right: 15px !important;
                                        }
                                    }
</style>
                </div>
            <?php endif; ?>
        </main>
</div>
<?php require __DIR__ . '/partials/_delete_modal.php'; ?>
</body>
</html>
