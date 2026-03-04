<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <title>Translations — <?= APP_NAME ?></title>
    <?php require __DIR__ . '/partials/_head_assets.php'; ?>
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<div class="admin-layout flex w-full h-screen overflow-hidden">
    <?php $currentPage = 'translations'; require __DIR__ . '/partials/sidebar.php'; ?>
    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-20 flex items-center justify-between px-8 bg-glass-bg border-b border-white/5 shrink-0 backdrop-blur-xl sticky top-0 z-[100]">
            <div class="flex flex-col">
                <div class="text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] mb-1 hidden sm:block">Language Logic</div>
                <h1 class="text-xl font-black text-white tracking-tight flex items-center gap-3 group">
                    <span class="text-neon-cyan drop-shadow-[0_0_8px_rgba(6,182,212,0.4)]">Semantic Bridge</span>
                    <span class="opacity-20 translate-y-px hidden sm:inline">/</span>
                    <span class="text-sm tracking-widest text-slate-400 uppercase font-black hidden sm:inline">Translation Nodes</span>
                </h1>
            </div>
            <div class="flex items-center gap-6">
                <?php require __DIR__ . '/partials/_topbar.php'; ?>
            </div>
        </header>
        
        <main class="flex-1 overflow-y-auto p-8 crm-main-scroll bg-[#0b0e14]">
        <?php if ($saved): ?>
            <div class="mb-8 p-4 bg-neon-emerald/10 border border-neon-emerald/20 rounded-2xl text-neon-emerald text-[10px] font-black uppercase tracking-widest flex items-center gap-3">
                <i class="ph-bold ph-check-circle text-lg"></i> Parameters Committed Successfully
            </div>
        <?php endif; ?>

        <div class="admin-card" style="margin-bottom: 30px;">
            <h3>Add New Translation Key</h3>
            <form method="POST" action="<?= baseUrl('admin/translations') ?>" style="margin-top: 15px;">
                <input type="hidden" name="action" value="save">
                <div class="admin-grid-2">
                    <div class="form-group">
                        <label>Translation Key (e.g., hero_cta)</label>
                        <input type="text" name="new_key" class="form-input" placeholder="hero_cta">
                    </div>
                    <div class="form-group">
                        <label>Group (optional, for organizing)</label>
                        <input type="text" name="new_group" class="form-input" value="general">
                    </div>
                </div>
                <div class="admin-grid-2" style="margin-top: 15px;">
                    <div class="form-group">
                        <label>English Value</label>
                        <input type="text" name="new_value_en" class="form-input" placeholder="English text">
                    </div>
                    <div class="form-group">
                        <label>Arabic Value</label>
                        <input type="text" name="new_value_ar" class="form-input" placeholder="النص العربي" dir="rtl">
                    </div>
                </div>
                <button type="submit" class="btn-primary" style="margin-top: 15px;">+ Add Key</button>
            </form>
        </div>

        <?php if (!empty($translations)): ?>
        <form method="POST" action="<?= baseUrl('admin/translations') ?>" id="translations_form">
            <input type="hidden" name="action" value="save" id="trans_action">
            <div class="admin-card">
                <h3>Existing Database Translations</h3>
                
                <?php foreach ($translations as $key => $locales): ?>
                    <div style="margin-top: 20px; padding: 15px; background: rgba(255,255,255,0.02); border-radius: 8px; border-left: 3px solid var(--neon-cobalt);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <h4 style="font-family: monospace; color: var(--neon-cyan); margin: 0;"><?= e($key) ?></h4>
                            <div style="display: flex; gap: 10px; align-items: center;">
                                <input type="text" name="groups[<?= e($key) ?>]" value="<?= e($locales['en']['trans_group'] ?? 'general') ?>" style="background:transparent; border:1px solid var(--glass-border); color:var(--text-muted); padding:2px 8px; border-radius:4px; font-size:0.75rem; width:100px;">
                                <button type="button" onclick="showDeleteModal('<?= e($key) ?>', function() { document.getElementById('trans_action').value='delete'; document.getElementById('del_<?= e($key) ?>').name='trans_key'; document.getElementById('del_<?= e($key) ?>').value='<?= e($key) ?>'; document.getElementById('translations_form').submit(); })" style="background:none; border:none; color:var(--neon-pink); cursor:pointer; font-size:0.8rem;">Delete</button>
                                <input type="hidden" id="del_<?= e($key) ?>">
                            </div>
                        </div>
                        <div class="admin-grid-2">
                            <?php foreach (SUPPORTED_LOCALES as $loc): ?>
                                <div class="form-group" style="margin: 0;">
                                    <label style="font-size: 0.75rem; opacity: 0.7;"><?= strtoupper($loc) ?></label>
                                    <input type="text" name="trans[<?= e($key) ?>][<?= $loc ?>]" class="form-input" value="<?= e($locales[$loc]['trans_value'] ?? '') ?>" dir="<?= $loc === 'ar' ? 'rtl' : 'ltr' ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <button type="submit" class="btn-primary" style="margin-top: 20px; font-size: 1.1rem; padding: 12px 30px;">Save All Changes</button>
        </form>
        <?php endif; ?>
        </main>
    </div>
</div>

<?php require __DIR__ . '/partials/_delete_modal.php'; ?>
<style>
    @media screen and (max-width: 1024px) {
        .admin-card { padding: 1.5rem !important; }
        .admin-grid-2 { grid-template-columns: 1fr; gap: 1rem; }
        header { padding: 0 1.5rem !important; h-24 !important; flex-direction: column; justify-content: center; gap: 0.5rem; }
    }
</style>
</body>
</html>
