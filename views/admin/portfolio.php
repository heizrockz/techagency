<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <title>Portfolio — <?= APP_NAME ?></title>
    <?php require __DIR__ . '/partials/_head_assets.php'; ?>
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<div class="admin-layout flex w-full h-screen overflow-hidden">
    <?php $currentPage = 'portfolio'; require __DIR__ . '/partials/sidebar.php'; ?>
    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-20 flex items-center justify-between px-8 bg-glass-bg border-b border-white/5 shrink-0 backdrop-blur-xl sticky top-0 z-[100]">
            <div class="absolute inset-0 bg-gradient-to-r from-primary/5 via-transparent to-transparent"></div>
            <div class="relative flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center border border-primary/20">
                    <i class="ph ph-palette text-2xl text-primary animate-pulse"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white tracking-tight">Project Nexus</h1>
                    <p class="text-xs text-white/40 uppercase tracking-widest font-medium hidden sm:block">Portfolio Architecture</p>
                </div>
            </div>
            <div class="relative flex items-center gap-4">
                <a href="<?= baseUrl('admin/portfolio?action=new') ?>" class="group flex items-center gap-2 px-3 sm:px-5 py-2.5 bg-primary/10 hover:bg-primary/20 border border-primary/20 hover:border-primary/40 rounded-xl transition-all duration-300">
                    <i class="ph ph-plus-circle text-lg text-primary group-hover:rotate-90 transition-transform duration-500"></i>
                    <span class="text-sm font-semibold text-primary hidden sm:inline">New Project</span>
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
                    <p class="text-emerald-500 font-medium">Project synchronized with the main sequence successfully.</p>
                </div>
            <?php endif; ?>

            <?php if ($action === 'edit' || $action === 'new'): ?>
                <div class="max-w-5xl mx-auto">
                    <div class="admin-card relative overflow-hidden group">
                        <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:opacity-10 transition-opacity">
                            <i class="ph ph-palette text-8xl text-primary"></i>
                        </div>
                        
                        <div class="relative flex items-center gap-3 mb-8">
                            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center border border-primary/20 text-primary">
                                <i class="ph ph-layout text-xl"></i>
                            </div>
                            <h2 class="text-xl font-bold text-white"><?= $action === 'edit' ? 'Modify Project Parameters' : 'Initialize New Project' ?></h2>
                        </div>

                        <form method="POST" action="<?= baseUrl('admin/portfolio') ?>" class="relative space-y-8">
                            <input type="hidden" name="id" value="<?= $editProject['id'] ?? 0 ?>">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-white/60 ml-1">Universal Slug</label>
                                    <div class="relative group/input">
                                        <i class="ph ph-link absolute left-4 top-1/2 -translate-y-1/2 text-primary/50 group-focus-within/input:text-primary transition-colors"></i>
                                        <input type="text" name="slug" class="form-input !pl-12" value="<?= e($editProject['slug'] ?? '') ?>" placeholder="e.g. quantum-interface" required>
                                    </div>
                                    <p class="text-[10px] text-white/30 uppercase tracking-tighter">URL-friendly unique identifier</p>
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-white/60 ml-1">Classification Category</label>
                                    <div class="relative group/input">
                                        <i class="ph ph-folders absolute left-4 top-1/2 -translate-y-1/2 text-primary/50 group-focus-within/input:text-primary transition-colors"></i>
                                        <select name="category" class="form-input !pl-12">
                                            <?php $cat = $editProject['category'] ?? 'website'; ?>
                                            <option value="website" <?= $cat==='website'?'selected':'' ?>>Web Architecture</option>
                                            <option value="app" <?= $cat==='app'?'selected':'' ?>>Mobile Subsystem</option>
                                            <option value="branding" <?= $cat==='branding'?'selected':'' ?>>Visual Identity</option>
                                            <option value="marketing" <?= $cat==='marketing'?'selected':'' ?>>Strategic Outreach</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-white/60 ml-1">Spectral Signature</label>
                                    <select name="color" class="form-input">
                                        <?php 
                                        $colors = ['cobalt', 'violet', 'emerald', 'pink', 'cyan', 'orange'];
                                        $selectedColor = $editProject['color'] ?? 'cobalt';
                                        foreach($colors as $c): ?>
                                            <option value="<?= $c ?>" <?= $selectedColor === $c ? 'selected' : '' ?>><?= ucfirst($c) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-white/60 ml-1">Priority Sequence</label>
                                    <input type="number" name="sort_order" class="form-input" value="<?= $editProject['sort_order'] ?? 0 ?>">
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-white/60 ml-1">Asset Endpoint (URL)</label>
                                    <input type="text" name="image_url" class="form-input" value="<?= e($editProject['image_url'] ?? '') ?>" placeholder="https://...">
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-white/60 ml-1">Live Demonstration Endpoint</label>
                                    <input type="text" name="demo_url" class="form-input" value="<?= e($editProject['demo_url'] ?? '') ?>" placeholder="https://...">
                                </div>

                                <div class="md:col-span-2 flex items-center gap-8 pt-4">
                                    <label class="relative flex items-center gap-3 cursor-pointer group">
                                        <input type="checkbox" name="is_active" class="peer hidden" <?= (!isset($editProject) || $editProject['is_active']) ? 'checked' : '' ?>>
                                        <div class="w-12 h-6 bg-white/5 rounded-full border border-white/10 peer-checked:bg-primary/20 peer-checked:border-primary/40 transition-all duration-300"></div>
                                        <div class="absolute left-1 top-1 w-4 h-4 bg-white/20 rounded-full peer-checked:left-7 peer-checked:bg-primary transition-all duration-300 shadow-lg"></div>
                                        <span class="text-sm font-medium text-white/60 group-hover:text-white transition-colors">Protocol Active</span>
                                    </label>

                                    <label class="relative flex items-center gap-3 cursor-pointer group">
                                        <input type="checkbox" name="is_featured" class="peer hidden" <?= (isset($editProject) && $editProject['is_featured']) ? 'checked' : '' ?>>
                                        <div class="w-12 h-6 bg-white/5 rounded-full border border-white/10 peer-checked:bg-amber-500/20 peer-checked:border-amber-500/40 transition-all duration-300"></div>
                                        <div class="absolute left-1 top-1 w-4 h-4 bg-white/20 rounded-full peer-checked:left-7 peer-checked:bg-amber-500 transition-all duration-300 shadow-lg"></div>
                                        <span class="text-sm font-medium text-white/60 group-hover:text-white transition-colors">Featured In Highlight Loop</span>
                                    </label>
                                </div>
                            </div>

                            <div class="space-y-8">
                                <?php foreach (SUPPORTED_LOCALES as $loc): ?>
                                    <div class="p-8 rounded-2xl bg-white/[0.03] border border-white/5 relative overflow-hidden group/loc">
                                        <div class="absolute top-0 right-0 p-4 opacity-5 pointer-events-none uppercase font-black text-6xl italic"><?= $loc ?></div>
                                        
                                        <div class="flex items-center justify-between mb-8 relative">
                                            <div class="flex items-center gap-3">
                                                <div class="w-2 h-8 bg-primary rounded-full"></div>
                                                <h4 class="text-lg font-bold text-white uppercase tracking-wider"><?= strtoupper($loc) ?> Multilingual Interface</h4>
                                            </div>
                                            <span class="px-3 py-1 rounded-md bg-white/5 text-xs font-bold text-white/40 border border-white/10"><?= $loc === 'ar' ? 'RTL' : 'LTR' ?></span>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 relative">
                                            <div class="space-y-3">
                                                <label class="text-xs font-bold text-white/40 uppercase tracking-widest ml-1">Project Title</label>
                                                <input type="text" name="title_<?= $loc ?>" class="form-input <?= $loc === 'ar' ? 'rtl-input' : '' ?>" value="<?= e($editProject['translations'][$loc]['title'] ?? '') ?>" required>
                                            </div>
                                            <div class="space-y-3">
                                                <label class="text-xs font-bold text-white/40 uppercase tracking-widest ml-1">Client Affiliation</label>
                                                <input type="text" name="client_<?= $loc ?>" class="form-input <?= $loc === 'ar' ? 'rtl-input' : '' ?>" value="<?= e($editProject['translations'][$loc]['client_name'] ?? '') ?>">
                                            </div>
                                            <div class="md:col-span-2 space-y-3">
                                                <label class="text-xs font-bold text-white/40 uppercase tracking-widest ml-1">Project Brief / Description</label>
                                                <textarea name="desc_<?= $loc ?>" class="form-input min-h-[120px] <?= $loc === 'ar' ? 'rtl-input' : '' ?>" rows="3"><?= e($editProject['translations'][$loc]['description'] ?? '') ?></textarea>
                                            </div>
                                            <div class="md:col-span-2 space-y-3">
                                                <label class="text-xs font-bold text-white/40 uppercase tracking-widest ml-1">Technology Tags (Comma Separated)</label>
                                                <div class="relative group/tag">
                                                    <i class="ph ph-terminal absolute left-4 top-1/2 -translate-y-1/2 text-primary/40"></i>
                                                    <input type="text" name="tags_<?= $loc ?>" class="form-input !pl-12" value="<?= e($editProject['translations'][$loc]['tags'] ?? '') ?>" placeholder="Laravel, Vue.js, TailwindCSS" dir="ltr">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="flex items-center gap-4 pt-6 justify-end">
                                <a href="<?= baseUrl('admin/portfolio') ?>" class="px-10 py-4 bg-white/5 text-white font-bold rounded-xl hover:bg-white/10 transition-all border border-white/10 uppercase tracking-widest text-xs">
                                    Abort Operation
                                </a>
                                <button type="submit" class="px-12 py-4 bg-primary text-black font-black rounded-xl hover:bg-primary/80 transition-all shadow-[0_0_20px_rgba(var(--primary-rgb),0.3)] uppercase tracking-widest text-xs">
                                    Sync Metadata
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <div class="admin-card p-0 overflow-hidden border-white/5">
                    <div class="p-8 border-b border-white/5 flex items-center justify-between bg-white/[0.01]">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center border border-primary/20 text-primary">
                                <i class="ph ph-briefcase text-2xl animate-pulse"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-white text-xl tracking-tight">Active Portfolio Matrix</h3>
                                <p class="text-[10px] text-white/30 uppercase tracking-[0.2em] font-bold">Operational Database</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-6">
                            <div class="flex flex-col items-end">
                                <span class="text-[10px] text-white/40 uppercase tracking-widest font-bold">Total Projects</span>
                                <span class="text-2xl font-black text-primary font-mono"><?= str_pad(count($projects), 2, '0', STR_PAD_LEFT) ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th class="!pl-8">Project Identity</th>
                                    <th>Classification</th>
                                    <th>Translation Status</th>
                                    <th>Priority</th>
                                    <th>System Status</th>
                                    <th class="!pr-8 text-right">Operations</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                <?php foreach ($projects as $p): ?>
                                    <tr class="group hover:bg-white/[0.02] transition-all duration-300">
                                        <td class="!pl-8" data-label="Identity">
                                            <div class="flex items-center gap-4 py-2">
                                                <div class="w-14 h-14 rounded-2xl bg-white/5 border border-white/10 p-1 group-hover:scale-110 transition-transform duration-500 overflow-hidden relative">
                                                    <?php if($p['image_url']): ?>
                                                        <img src="<?= e($p['image_url']) ?>" class="w-full h-full object-cover rounded-xl grayscale group-hover:grayscale-0 transition-all duration-700">
                                                    <?php else: ?>
                                                        <div class="w-full h-full flex items-center justify-center bg-primary/5 rounded-xl">
                                                            <i class="ph ph-image text-primary/20 text-xl"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                    <?php if($p['is_featured']): ?>
                                                        <div class="absolute top-0 right-0 w-6 h-6 bg-amber-500 flex items-center justify-center rounded-bl-xl shadow-lg">
                                                            <i class="ph-fill ph-star text-[10px] text-black"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div>
                                                    <div class="text-white font-bold tracking-tight text-lg"><?= e($p['trans'] ?? 'Unnamed Node') ?></div>
                                                    <div class="text-[10px] text-white/30 font-mono mt-1 italic"><?= e($p['slug']) ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td data-label="Class">
                                            <span class="px-3 py-1.5 rounded-lg bg-white/5 border border-white/10 text-[10px] font-black text-white/60 uppercase tracking-widest group-hover:border-primary/30 group-hover:text-primary transition-all">
                                                <?= e($p['category']) ?>
                                            </span>
                                        </td>
                                        <td data-label="Lang">
                                            <div class="flex gap-1">
                                                <?php foreach(SUPPORTED_LOCALES as $l): ?>
                                                    <div class="w-6 h-6 rounded-md bg-white/5 border border-white/5 flex items-center justify-center text-[9px] font-black text-white/30 uppercase"><?= $l ?></div>
                                                <?php endforeach; ?>
                                            </div>
                                        </td>
                                        <td data-label="Priority">
                                            <div class="text-white/60 font-mono text-xs">P-<?= str_pad($p['sort_order'], 3, '0', STR_PAD_LEFT) ?></div>
                                        </td>
                                        <td data-label="Status">
                                            <?php if ($p['is_active']): ?>
                                                <div class="flex items-center gap-3 text-emerald-500">
                                                    <div class="relative flex h-2 w-2">
                                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                                    </div>
                                                    <span class="text-[10px] font-black uppercase tracking-[0.2em]">Operational</span>
                                                </div>
                                            <?php else: ?>
                                                <div class="flex items-center gap-3 text-white/20">
                                                    <div class="h-2 w-2 rounded-full bg-white/20"></div>
                                                    <span class="text-[10px] font-black uppercase tracking-[0.2em]">Encrypted</span>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="!pr-8 text-right" data-label="Nav">
                                            <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition-all translate-x-4 group-hover:translate-x-0">
                                                <a href="<?= baseUrl('admin/portfolio?action=edit&id='.$p['id']) ?>" class="w-11 h-11 rounded-2xl bg-cyan-500/10 flex items-center justify-center border border-cyan-500/20 text-cyan-500 hover:bg-cyan-500 hover:text-black transition-all duration-300" title="Modify Architecture">
                                                    <i class="ph ph-gear-six text-xl"></i>
                                                </a>
                                                <button onclick="showDeleteModal('this project node', '<?= baseUrl('admin/portfolio?action=delete&id='.$p['id']) ?>')" class="w-11 h-11 rounded-2xl bg-pink-500/10 flex items-center justify-center border border-pink-500/20 text-pink-500 hover:bg-pink-500 hover:text-black transition-all duration-300" title="Terminate Node">
                                                    <i class="ph ph-x-circle text-xl"></i>
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
