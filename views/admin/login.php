<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('admin_login_title') ?> — <?= APP_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= baseUrl('assets/css/style.css') ?>">
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
    <div class="nebula-bg"></div>
    <div class="nebula-orb nebula-orb-1"></div>
    <div class="nebula-orb nebula-orb-2"></div>

    <div class="admin-login-wrapper">
        <div class="admin-login-card">
            <div style="font-size: 2.5rem; margin-bottom: 16px;">⚡</div>
            <h1><?= t('admin_login_title') ?></h1>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error" style="text-align:start; margin-top: 16px;"><?= e($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="<?= baseUrl('admin/login') ?>" style="margin-top: 24px; text-align: start;">
                <div class="admin-form-group">
                    <label for="username"><?= t('admin_username') ?></label>
                    <input type="text" id="username" name="username" required autofocus>
                </div>
                <div class="admin-form-group">
                    <label for="password"><?= t('admin_password') ?></label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn-neon" style="margin-top: 8px;"><?= t('admin_login_btn') ?></button>
            </form>
        </div>
    </div>
</body>
</html>
