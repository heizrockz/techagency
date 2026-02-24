<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('admin_seo') ?> — <?= APP_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= baseUrl('assets/css/style.css') ?>">
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">

<div class="admin-layout">
    <?php $currentPage = 'seo'; require __DIR__ . '/partials/sidebar.php'; ?>

    <div class="admin-main">
        <div class="admin-header">
            <h1>🔍 <?= t('admin_seo') ?></h1>
        </div>

        <?php if ($saved): ?>
            <div class="alert alert-success"><?= t('admin_saved') ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= baseUrl('admin/seo') ?>">
            <?php foreach ($seoData as $page => $locales): ?>
                <div class="content-section">
                    <h3 style="text-transform: capitalize;">📄 <?= e($page) ?></h3>

                    <?php foreach (SUPPORTED_LOCALES as $loc): ?>
                        <div style="margin-bottom: 20px; padding: 16px; background: rgba(255,255,255,0.02); border-radius: var(--radius-sm);">
                            <p style="font-weight: 600; font-size: 0.85rem; margin-bottom: 12px; color: var(--neon-cyan);">
                                <?= $loc === 'en' ? '🇬🇧 English' : '🇸🇦 Arabic' ?>
                            </p>
                            <div class="admin-form-group">
                                <label>Title</label>
                                <input type="text"
                                       name="seo[<?= e($page) ?>][<?= $loc ?>][title]"
                                       value="<?= e($locales[$loc]['title'] ?? '') ?>"
                                       dir="<?= $loc === 'ar' ? 'rtl' : 'ltr' ?>">
                            </div>
                            <div class="admin-form-group">
                                <label>Description</label>
                                <textarea name="seo[<?= e($page) ?>][<?= $loc ?>][description]"
                                          rows="2"
                                          dir="<?= $loc === 'ar' ? 'rtl' : 'ltr' ?>"><?= e($locales[$loc]['description'] ?? '') ?></textarea>
                            </div>
                            <div class="admin-form-group">
                                <label>Keywords</label>
                                <input type="text"
                                       name="seo[<?= e($page) ?>][<?= $loc ?>][keywords]"
                                       value="<?= e($locales[$loc]['keywords'] ?? '') ?>"
                                       dir="<?= $loc === 'ar' ? 'rtl' : 'ltr' ?>">
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>

            <button type="submit" class="btn-admin-save"><?= t('admin_save') ?></button>
        </form>
    </div>
</div>

</body>
</html>
