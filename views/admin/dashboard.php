<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('admin_dashboard') ?> — <?= APP_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= baseUrl('assets/css/style.css') ?>">
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">

<div class="admin-layout">
    <!-- Sidebar -->
    <?php $currentPage = 'dashboard'; require __DIR__ . '/partials/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="admin-main">
        <div class="admin-header">
            <h1><?= t('admin_welcome') ?>, <?= e(getAdminUser()) ?> 👋</h1>
        </div>

        <!-- Stats -->
        <div class="admin-stats">
            <div class="admin-stat-card">
                <div class="stat-value"><?= (int)$visitCount ?></div>
                <div class="stat-title"><?= getCurrentLocale() === 'ar' ? 'إجمالي الزيارات' : 'Total Traffic' ?></div>
            </div>
            <div class="admin-stat-card">
                <div class="stat-value"><?= (int)$totalBookings ?></div>
                <div class="stat-title"><?= t('admin_total_bookings') ?></div>
            </div>
            <div class="admin-stat-card">
                <div class="stat-value"><?= (int)$newBookings ?></div>
                <div class="stat-title"><?= t('admin_new_bookings') ?></div>
            </div>
        </div>

        <!-- Recent Bookings -->
        <div class="admin-card">
            <h2 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 16px;">
                <?= getCurrentLocale() === 'ar' ? 'أحدث الحجوزات' : 'Recent Bookings' ?>
            </h2>

            <?php if (empty($recentBookings)): ?>
                <p style="color: var(--text-muted);">
                    <?= getCurrentLocale() === 'ar' ? 'لا توجد حجوزات بعد.' : 'No bookings yet.' ?>
                </p>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th><?= getCurrentLocale() === 'ar' ? 'الاسم' : 'Name' ?></th>
                                <th><?= getCurrentLocale() === 'ar' ? 'الخدمة' : 'Service' ?></th>
                                <th><?= getCurrentLocale() === 'ar' ? 'الحالة' : 'Status' ?></th>
                                <th><?= getCurrentLocale() === 'ar' ? 'التاريخ' : 'Date' ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentBookings as $b): ?>
                            <tr>
                                <td><?= (int)$b['id'] ?></td>
                                <td><?= e($b['name']) ?></td>
                                <td><?= e($b['service']) ?></td>
                                <td><span class="status-badge status-<?= e($b['status']) ?>"><?= e($b['status']) ?></span></td>
                                <td><?= e($b['created_at']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
