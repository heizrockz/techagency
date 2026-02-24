<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('admin_bookings') ?> — <?= APP_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= baseUrl('assets/css/style.css') ?>">
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">

<div class="admin-layout">
    <?php $currentPage = 'bookings'; require __DIR__ . '/partials/sidebar.php'; ?>

    <div class="admin-main">
        <div class="admin-header">
            <h1>📋 <?= t('admin_bookings') ?></h1>
            <div style="display: flex; gap: 8px;">
                <?php
                $statuses = ['all' => 'All', 'new' => 'New', 'viewed' => 'Viewed', 'contacted' => 'Contacted', 'completed' => 'Completed', 'cancelled' => 'Cancelled'];
                $currentFilter = $_GET['status'] ?? 'all';
                foreach ($statuses as $val => $label):
                ?>
                    <a href="<?= baseUrl('admin/bookings?status=' . $val) ?>"
                       style="padding: 6px 14px; border-radius: 999px; font-size: 0.8rem; font-weight: 500; text-decoration: none; transition: all 0.3s;
                              <?= $currentFilter === $val
                                  ? 'background: linear-gradient(135deg, var(--neon-cobalt), var(--neon-violet)); color: white;'
                                  : 'background: var(--glass-bg); border: 1px solid var(--glass-border); color: var(--text-secondary);' ?>">
                        <?= $label ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="admin-card">
            <?php if (empty($bookings)): ?>
                <p style="color: var(--text-muted); text-align: center; padding: 40px 0;">
                    <?= getCurrentLocale() === 'ar' ? 'لا توجد حجوزات.' : 'No bookings found.' ?>
                </p>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th><?= getCurrentLocale() === 'ar' ? 'الاسم' : 'Name' ?></th>
                                <th><?= getCurrentLocale() === 'ar' ? 'البريد' : 'Email' ?></th>
                                <th><?= getCurrentLocale() === 'ar' ? 'الهاتف' : 'Phone' ?></th>
                                <th><?= getCurrentLocale() === 'ar' ? 'الخدمة' : 'Service' ?></th>
                                <th><?= getCurrentLocale() === 'ar' ? 'الرسالة' : 'Message' ?></th>
                                <th><?= getCurrentLocale() === 'ar' ? 'التاريخ المفضل' : 'Preferred Date' ?></th>
                                <th><?= getCurrentLocale() === 'ar' ? 'الحالة' : 'Status' ?></th>
                                <th><?= getCurrentLocale() === 'ar' ? 'تم الإنشاء' : 'Created' ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bookings as $b): ?>
                            <tr>
                                <td><?= (int)$b['id'] ?></td>
                                <td style="font-weight: 500; color: var(--text-primary);"><?= e($b['name']) ?></td>
                                <td><?= e($b['email']) ?></td>
                                <td><?= e($b['phone'] ?: '—') ?></td>
                                <td><?= e($b['service']) ?></td>
                                <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= e($b['message']) ?>">
                                    <?= e($b['message'] ?: '—') ?>
                                    <?php
                                    if (!empty($b['extra_fields'])) {
                                        $extra = json_decode($b['extra_fields'], true);
                                        if (is_array($extra)) {
                                            echo '<div style="margin-top: 5px; font-size: 0.75rem; color: var(--text-muted);">';
                                            foreach ($extra as $k => $v) {
                                                echo e($k) . ': <strong>' . e($v) . '</strong><br>';
                                            }
                                            echo '</div>';
                                        }
                                    }
                                    ?>
                                </td>
                                <td><?= e($b['preferred_date'] ?: '—') ?></td>
                                <td>
                                    <form method="POST" action="<?= baseUrl('admin/bookings/update-status') ?>" class="inline-form">
                                        <input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
                                        <select name="status" class="inline-select" onchange="this.form.submit()">
                                            <?php foreach (['new','viewed','contacted','completed','cancelled'] as $s): ?>
                                                <option value="<?= $s ?>" <?= $b['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </form>
                                </td>
                                <td style="font-size: 0.8rem; color: var(--text-muted);"><?= e(date('M d, Y', strtotime($b['created_at']))) ?></td>
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
