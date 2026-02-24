<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('admin_content') ?> — <?= APP_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= baseUrl('assets/css/style.css') ?>">
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">

<div class="admin-layout">
    <?php $currentPage = 'content'; require __DIR__ . '/partials/sidebar.php'; ?>

    <div class="admin-main">
        <div class="admin-header">
            <h1>✏️ <?= t('admin_content') ?></h1>
        </div>

        <?php if ($saved): ?>
            <div class="alert alert-success"><?= t('admin_saved') ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= baseUrl('admin/content') ?>">
            <?php foreach ($contents as $key => $locales): ?>
                <div class="content-section">
                    <h3><?= e($key) ?></h3>
                    <div class="content-locale-grid">
                        <?php foreach (SUPPORTED_LOCALES as $loc): ?>
                            <div class="admin-form-group">
                                <label>
                                    <?= $loc === 'en' ? '🇬🇧 English' : '🇸🇦 Arabic' ?>
                                </label>
                                <?php
                                    $val = $locales[$loc] ?? '';
                                    $isLong = strlen($val) > 100;
                                ?>
                                <?php if ($isLong): ?>
                                    <textarea name="content[<?= e($key) ?>][<?= $loc ?>]" rows="3" dir="<?= $loc === 'ar' ? 'rtl' : 'ltr' ?>"><?= e($val) ?></textarea>
                                <?php else: ?>
                                    <input type="text" name="content[<?= e($key) ?>][<?= $loc ?>]" value="<?= e($val) ?>" dir="<?= $loc === 'ar' ? 'rtl' : 'ltr' ?>">
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <button type="submit" class="btn-admin-save"><?= t('admin_save') ?></button>
        </form>
    </div>
</div>

</body>
</html>
