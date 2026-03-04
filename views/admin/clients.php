<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <title>Clients — <?= APP_NAME ?></title>
    <?php require __DIR__ . '/partials/_head_assets.php'; ?>
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<div class="admin-layout flex w-full h-screen overflow-hidden">
    <?php $currentPage = 'clients'; require __DIR__ . '/partials/sidebar.php'; ?>
    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-20 flex items-center justify-between px-8 bg-glass-bg border-b border-white/5 shrink-0 backdrop-blur-xl sticky top-0 z-[100]">
            <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/5 via-transparent to-transparent"></div>
            <div class="relative flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-cyan-500/10 flex items-center justify-center border border-cyan-500/20">
                    <i class="ph ph-handshake text-2xl text-cyan-500 animate-pulse"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white tracking-tight">Client Nexus</h1>
                    <p class="text-xs text-white/40 uppercase tracking-widest font-medium hidden sm:block">Partnership Ecosystem</p>
                </div>
            </div>
            <div class="relative flex items-center gap-4">
                <a href="<?= baseUrl('admin/clients?action=new') ?>" class="group flex items-center gap-2 px-5 py-2.5 bg-cyan-500/10 hover:bg-cyan-500/20 border border-cyan-500/20 hover:border-cyan-500/40 rounded-xl transition-all duration-300">
                    <i class="ph ph-plus-circle text-lg text-cyan-500 group-hover:rotate-90 transition-transform duration-500"></i>
                    <span class="text-sm font-semibold text-cyan-500 hidden sm:inline">Register Entity</span>
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
                    <p class="text-emerald-500 font-medium">Entity data synchronized successfully.</p>
                </div>
            <?php endif; ?>

            <?php if ($action === 'edit' || $action === 'new'): ?>
                <div class="max-w-4xl mx-auto">
                    <div class="admin-card relative overflow-hidden group">
                        <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:opacity-10 transition-opacity">
                            <i class="ph ph-identification-card text-8xl text-cyan-500"></i>
                        </div>
                        
                        <div class="relative flex items-center gap-3 mb-8">
                            <div class="w-10 h-10 rounded-lg bg-cyan-500/10 flex items-center justify-center border border-cyan-500/20 text-cyan-500">
                                <i class="ph ph-fingerprint text-xl"></i>
                            </div>
                            <h2 class="text-xl font-bold text-white"><?= $action === 'edit' ? 'Modify Entity Parameters' : 'Initialize New Partnership' ?></h2>
                        </div>

                        <form method="POST" action="<?= baseUrl('admin/clients') ?>" class="relative space-y-8">
                            <input type="hidden" name="id" value="<?= $editClient['id'] ?? 0 ?>">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-white/60 ml-1">Entity Name</label>
                                    <div class="relative group/input">
                                        <i class="ph ph-buildings absolute left-4 top-1/2 -translate-y-1/2 text-cyan-500/50 group-focus-within/input:text-cyan-500 transition-colors"></i>
                                        <input type="text" name="name" class="form-input !pl-12" placeholder="e.g. Neural Systems Core" value="<?= e($editClient['name'] ?? '') ?>" required>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-white/60 ml-1">Visual Signifier (Logo URL)</label>
                                    <div class="relative group/input">
                                        <i class="ph ph-image absolute left-4 top-1/2 -translate-y-1/2 text-cyan-500/50 group-focus-within/input:text-cyan-500 transition-colors"></i>
                                        <input type="text" name="logo_url" class="form-input !pl-12" placeholder="https://..." value="<?= e($editClient['logo_url'] ?? '') ?>">
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-white/60 ml-1">Digital Domain URL</label>
                                    <div class="relative group/input">
                                        <i class="ph ph-globe absolute left-4 top-1/2 -translate-y-1/2 text-cyan-500/50 group-focus-within/input:text-cyan-500 transition-colors"></i>
                                        <input type="text" name="website_url" class="form-input !pl-12" placeholder="https://..." value="<?= e($editClient['website_url'] ?? '') ?>">
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-white/60 ml-1">Sequence Priority</label>
                                    <div class="relative group/input">
                                        <i class="ph ph-sort-ascending absolute left-4 top-1/2 -translate-y-1/2 text-cyan-500/50 group-focus-within/input:text-cyan-500 transition-colors"></i>
                                        <input type="number" name="sort_order" class="form-input !pl-12" value="<?= $editClient['sort_order'] ?? 0 ?>">
                                    </div>
                                </div>

                                <div class="flex items-center pt-8">
                                    <label class="relative flex items-center gap-3 cursor-pointer group">
                                        <input type="checkbox" name="is_active" class="peer hidden" <?= (!isset($editClient) || $editClient['is_active']) ? 'checked' : '' ?>>
                                        <div class="w-12 h-6 bg-white/5 rounded-full border border-white/10 peer-checked:bg-cyan-500/20 peer-checked:border-cyan-500/40 transition-all duration-300"></div>
                                        <div class="absolute left-1 top-1 w-4 h-4 bg-white/20 rounded-full peer-checked:left-7 peer-checked:bg-cyan-500 transition-all duration-300 shadow-lg"></div>
                                        <span class="text-sm font-medium text-white/60 group-hover:text-white transition-colors">Active Protocol Status</span>
                                    </label>
                                </div>
                            </div>

                            <div class="flex items-center gap-4 pt-6 border-t border-white/5">
                                <button type="submit" class="group relative px-8 py-3 bg-cyan-500 text-black font-bold rounded-xl hover:bg-cyan-400 transition-all shadow-lg shadow-cyan-500/20 overflow-hidden">
                                    <span class="relative z-10">Commit Entity</span>
                                    <div class="absolute inset-0 bg-white/20 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-1000"></div>
                                </button>
                                <a href="<?= baseUrl('admin/clients') ?>" class="px-8 py-3 bg-white/5 text-white font-semibold rounded-xl hover:bg-white/10 transition-all border border-white/10">
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
                            <div class="w-10 h-10 rounded-xl bg-cyan-500/10 flex items-center justify-center border border-cyan-500/20">
                                <i class="ph ph-address-book text-cyan-500 text-xl"></i>
                            </div>
                            <h3 class="font-bold text-white tracking-tight text-lg">Entity Visibility Matrix</h3>
                        </div>
                        <div class="bg-white/5 px-4 py-1.5 rounded-full border border-white/10">
                            <span class="text-[10px] text-white/40 uppercase tracking-widest font-bold">Total Partners:</span>
                            <span class="text-sm font-mono text-cyan-500 font-bold ml-2"><?= count($clients) ?></span>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th class="!pl-8">Entity Identity</th>
                                    <th class="text-center">Visual Signifier</th>
                                    <th class="text-center">Sequence</th>
                                    <th class="text-center">Status</th>
                                    <th class="!pr-8 text-right">Operations</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                <?php foreach ($clients as $c): ?>
                                    <tr class="group hover:bg-white/[0.02] transition-colors duration-300">
                                        <td class="!pl-8" data-label="Identity">
                                            <div class="flex flex-col">
                                                <span class="text-sm font-bold text-white group-hover:text-cyan-500 transition-colors uppercase tracking-wider"><?= e($c['name']) ?></span>
                                                <span class="text-[10px] text-white/30 font-mono"><?= e($c['website_url'] ?: 'NO_DIGITAL_DOMAIN') ?></span>
                                            </div>
                                        </td>
                                        <td class="text-center" data-label="Signifier">
                                            <?php if($c['logo_url']): ?>
                                                <div class="relative inline-block group/logo">
                                                    <div class="absolute inset-0 bg-cyan-500/20 blur-md opacity-0 group-hover/logo:opacity-100 transition-opacity rounded-full"></div>
                                                    <img src="<?= e($c['logo_url']) ?>" alt="" class="h-8 w-auto relative grayscale opacity-40 group-hover:grayscale-0 group-hover:opacity-100 transition-all duration-500 mx-auto">
                                                </div>
                                            <?php else: ?>
                                                <i class="ph ph-image-square text-xl text-white/10"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center" data-label="Sequence">
                                            <span class="text-xs font-mono text-white/40 bg-white/5 px-2 py-1 rounded-md border border-white/10"><?= $c['sort_order'] ?></span>
                                        </td>
                                        <td class="text-center" data-label="Status">
                                            <?php if($c['is_active']): ?>
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-500 text-[10px] font-bold uppercase tracking-widest border border-emerald-500/20">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                    Operational
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/5 text-white/40 text-[10px] font-bold uppercase tracking-widest border border-white/10">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-white/20"></span>
                                                    Standby
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="!pr-8 text-right" data-label="Operations">
                                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <a href="<?= baseUrl('admin/clients?action=edit&id='.$c['id']) ?>" class="w-8 h-8 rounded-lg bg-cyan-500/10 flex items-center justify-center text-cyan-500 hover:bg-cyan-500 hover:text-black transition-all">
                                                    <i class="ph ph-pencil-simple"></i>
                                                </a>
                                                <button onclick="showDeleteModal('<?= e($c['name']) ?>', '<?= baseUrl('admin/clients?action=delete&id='.$c['id']) ?>')" class="w-8 h-8 rounded-lg bg-pink-500/10 flex items-center justify-center text-pink-500 hover:bg-pink-500 hover:text-white transition-all">
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

<style>
<style>
    /* Desktop-first: ensure table looks good on large screens */
    @media screen and (min-width: 1025px) {
        .admin-table { min-width: 1200px; }
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
<?php require __DIR__ . '/partials/_delete_modal.php'; ?>
</body>
</html>
