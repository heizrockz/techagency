<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Translations — <?= APP_NAME ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= baseUrl('assets/css/style.css') ?>">
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<div class="admin-layout">
    <?php $currentPage = 'translations'; require __DIR__ . '/partials/sidebar.php'; ?>
    <div class="admin-main">
        <div class="admin-header">
            <h1>🌐 DB Translations</h1>
            <p style="color:var(--text-muted); font-size:0.9rem; margin-top:5px;">Override default language file translations or add new ones without editing PHP.</p>
        </div>
        
        <?php if ($saved): ?>
            <div class="alert alert-success">Translations saved successfully.</div>
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
        <form method="POST" action="<?= baseUrl('admin/translations') ?>">
            <input type="hidden" name="action" value="save">
            <div class="admin-card">
                <h3>Existing Database Translations</h3>
                
                <?php foreach ($translations as $key => $locales): ?>
                    <div style="margin-top: 20px; padding: 15px; background: rgba(255,255,255,0.02); border-radius: 8px; border-left: 3px solid var(--neon-cobalt);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <h4 style="font-family: monospace; color: var(--neon-cyan); margin: 0;"><?= e($key) ?></h4>
                            <div style="display: flex; gap: 10px; align-items: center;">
                                <input type="text" name="groups[<?= e($key) ?>]" value="<?= e($locales['en']['trans_group'] ?? 'general') ?>" style="background:transparent; border:1px solid var(--glass-border); color:var(--text-muted); padding:2px 8px; border-radius:4px; font-size:0.75rem; width:100px;">
                                <button type="submit" name="action" value="delete" onclick="document.getElementById('del_<?= e($key) ?>').name='trans_key'; document.getElementById('del_<?= e($key) ?>').value='<?= e($key) ?>'; return confirm('Delete this translation key?');" style="background:none; border:none; color:var(--neon-pink); cursor:pointer; font-size:0.8rem;">Delete</button>
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
    </div>
</div>
</body>
</html>
