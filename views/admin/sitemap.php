<?php
$title = "Sitemap Settings";
$currentPage = 'sitemap';
?>
<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <title><?= e($title) ?> — <?= APP_NAME ?></title>
    <?php require __DIR__ . '/partials/_head_assets.php'; ?>
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">

<div class="admin-layout flex w-full h-screen overflow-hidden">
    <!-- Sidebar -->
    <?php require __DIR__ . '/partials/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-20 flex items-center justify-between px-8 bg-glass-bg border-b border-white/5 shrink-0 backdrop-blur-xl sticky top-0 z-[100]">
            <div class="flex flex-col">
                <div class="text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] mb-1 hidden sm:block">Index Orchestration</div>
                <h1 class="text-xl font-black text-white tracking-tight flex items-center gap-3 group">
                    <span class="text-neon-cyan drop-shadow-[0_0_8px_rgba(6,182,212,0.4)]">Neural Wayfinder</span>
                    <span class="opacity-20 translate-y-px hidden sm:inline">/</span>
                    <span class="text-sm tracking-widest text-slate-400 uppercase font-black hidden sm:inline">Sitemap XML</span>
                </h1>
            </div>
            <div class="flex items-center gap-6">
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="action" value="regenerate">
                    <button type="submit" class="px-4 sm:px-6 py-2.5 bg-white/5 hover:bg-white/10 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all border border-white/10 active:scale-95 flex items-center gap-2" onclick="return confirm('This will reload the sitemap from the current database records. Any manual changes will be lost. Proceed?')">
                        <i class="ph ph-arrows-clockwise text-lg"></i> <span class="hidden sm:inline">Force Regeneration</span>
                    </button>
                </form>
                <?php require __DIR__ . '/partials/_topbar.php'; ?>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-8 crm-main-scroll bg-[#0b0e14]">
            <?php if ($saved): ?>
                <div class="mb-8 p-4 bg-neon-emerald/10 border border-neon-emerald/20 rounded-2xl text-neon-emerald text-[10px] font-black uppercase tracking-widest flex items-center gap-3">
                    <i class="ph-bold ph-check-circle text-lg"></i> Sitemap updated successfully.
                </div>
            <?php endif; ?>

        <div class="admin-card" style="background:rgba(255,255,255,0.03); border:1px solid var(--glass-border); border-radius:12px; padding:25px;">
            <div class="card-header" style="margin-bottom:20px;">
                <h3 class="card-title" style="font-size:1.1rem; font-weight:600;">sitemap.xml Editor</h3>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="save">
                    <div class="form-group">
                        <label for="sitemap_content" style="display:block; font-size:0.8rem; color:var(--text-muted); margin-bottom:10px;">Raw XML Content</label>
                        <textarea name="sitemap_content" id="sitemap_content" class="form-control" 
                                  style="width:100%; min-height: 500px; font-family: monospace; background: #1e1e1e; color: #d4d4d4; line-height: 1.5; padding: 20px; border:1px solid rgba(255,255,255,0.1); border-radius:8px; outline:none;"
                        ><?= htmlspecialchars($currentContent) ?></textarea>
                    </div>
                    
                    <div class="form-actions" style="margin-top: 20px; display:flex; gap:15px;">
                        <button type="submit" class="btn-primary">Save Manual Changes</button>
                        <a href="<?= baseUrl('sitemap.xml') ?>" target="_blank" rel="noopener noreferrer" class="btn-secondary">View Live XML</a>
                    </div>
                </form>
            </div>
        </div>
        </main>
    </div>
</div>

<style>
#sitemap_content:focus {
    border-color: var(--neon-emerald);
}
</style>

</body>
</html>
