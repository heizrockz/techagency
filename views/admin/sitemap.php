<?php
$title = "Sitemap Settings";
$currentPage = 'sitemap';
?>
<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?> — <?= APP_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= baseUrl('assets/css/style.css') ?>">
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">

<div class="admin-layout">
    <!-- Sidebar -->
    <?php require __DIR__ . '/partials/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="admin-main">
        <div class="admin-header">
            <div class="header-left">
                <h1 style="color: var(--neon-cobalt); margin:0; font-size:1.8rem;">🗺️ Sitemap Manager</h1>
                <p style="color: var(--text-muted); font-size: 0.85rem;">Generate and edit your search engine structure.</p>
            </div>
            <div class="header-actions">
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="action" value="regenerate">
                    <button type="submit" class="btn btn-secondary" style="background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); padding:8px 15px; border-radius:8px; color:white; cursor:pointer;" onclick="return confirm('This will reload the sitemap from the current database records. Any manual changes will be lost. Proceed?')">
                        🔄 Regenerate Now
                    </button>
                </form>
            </div>
        </div>

        <?php if ($saved): ?>
            <div class="alert alert-success" style="background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.2); color:var(--neon-emerald); padding:15px; border-radius:10px; margin-bottom:20px;">Sitemap has been updated successfully.</div>
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
                        <button type="submit" class="btn btn-primary" style="background:var(--theme-primary); border:none; padding:10px 20px; border-radius:8px; color:white; font-weight:600; cursor:pointer;">Save Manual Changes</button>
                        <a href="<?= baseUrl('sitemap.xml') ?>" target="_blank" class="btn btn-secondary" style="background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); padding:10px 20px; border-radius:8px; color:white; text-decoration:none; display:inline-block;">View Live XML</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
#sitemap_content:focus {
    border-color: var(--neon-emerald);
}
</style>

</body>
</html>
