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
            <a href="<?= baseUrl('/admin/visitors') ?>" class="admin-stat-card group relative overflow-hidden block transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_20px_50px_rgba(59,130,246,0.15)] border-white/5 hover:border-primary/20 bg-[#1a2333]">
                <!-- Premium Background Effect -->
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-primary/10 rounded-full blur-2xl group-hover:bg-primary/20 transition-all duration-500"></div>
                
                <div class="flex justify-between items-end relative z-10">
                    <div>
                        <div class="stat-title text-slate-400 text-sm font-medium mb-1 flex items-center gap-2">
                            <i class="ph ph-chart-line-up text-primary animate-pulse"></i>
                            <?= getCurrentLocale() === 'ar' ? 'إجمالي الزيارات' : 'Total Traffic' ?>
                        </div>
                        <div class="stat-value text-3xl font-bold text-white tracking-tight leading-none"><?= number_format((int)$visitCount) ?></div>
                    </div>
                    <div class="flex flex-col items-end gap-2">
                        <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center transition-all duration-300 group-hover:scale-110 group-hover:bg-primary group-hover:text-white shadow-lg shadow-primary/10">
                            <i class="ph ph-users text-xl"></i>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-primary opacity-0 group-hover:opacity-100 transition-all duration-300 translate-y-2 group-hover:translate-y-0">
                            <?= getCurrentLocale() === 'ar' ? 'عرض التفاصيل' : 'View Details' ?>
                        </span>
                    </div>
                </div>
            </a>
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
