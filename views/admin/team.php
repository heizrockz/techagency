<?php
/**
 * Admin: Our Team Members CRUD
 */
?>
<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Team — <?= APP_NAME ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= baseUrl('assets/css/style.css') ?>">
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<div class="admin-layout">
    <?php $currentPage = 'team'; require __DIR__ . '/partials/sidebar.php'; ?>
    <div class="admin-main">
        <div class="admin-header">
            <h1>👥 Our Team</h1>
            <a href="<?= baseUrl('admin/team?action=new') ?>" class="btn-primary" style="padding: 8px 16px; font-size: 0.9rem;">+ Add Member</a>
        </div>

        <?php if ($saved): ?>
            <div class="alert alert-success">Team member saved successfully.</div>
        <?php endif; ?>

        <?php if ($action === 'edit' || $action === 'new'): ?>
            <div class="admin-card" style="margin-bottom: 30px;">
                <h3 style="margin-bottom: 24px; font-size: 1.1rem;"><?= $editMember ? 'Edit Member' : 'Add New Member' ?></h3>
                <form method="POST" action="<?= baseUrl('admin/team') ?>">
                    <input type="hidden" name="id" value="<?= $editMember['id'] ?? 0 ?>">

                    <div class="admin-grid-2" style="margin-bottom: 20px;">
                        <div class="admin-form-group">
                            <label>Photo / Avatar URL</label>
                            <input type="text" name="image_url" class="form-input" placeholder="https://..." value="<?= e($editMember['image_url'] ?? '') ?>">
                        </div>
                        <div class="admin-form-group">
                            <label>Sort Order</label>
                            <input type="number" name="sort_order" class="form-input" value="<?= $editMember['sort_order'] ?? 0 ?>">
                        </div>
                        <div class="admin-form-group" style="display: flex; align-items: center; gap: 10px; margin-top: 10px;">
                            <label style="cursor: pointer; display: flex; align-items: center; gap: 10px;">
                                <input type="checkbox" name="is_active" <?= (!isset($editMember) || $editMember['is_active']) ? 'checked' : '' ?>>
                                Active (visible on site)
                            </label>
                        </div>
                    </div>

                    <?php foreach (SUPPORTED_LOCALES as $loc): ?>
                    <div class="content-section" style="margin-bottom: 16px;">
                        <h3><?= strtoupper($loc) ?> Translation</h3>
                        <div class="content-locale-grid">
                            <div class="admin-form-group">
                                <label>Full Name (<?= $loc ?>)</label>
                                <input type="text" name="name_<?= $loc ?>" class="form-input <?= $loc === 'ar' ? 'rtl-input' : '' ?>"
                                    value="<?= e($editMember['translations'][$loc]['name'] ?? '') ?>" required>
                            </div>
                            <div class="admin-form-group">
                                <label>Job Title / Role (<?= $loc ?>)</label>
                                <input type="text" name="role_<?= $loc ?>" class="form-input <?= $loc === 'ar' ? 'rtl-input' : '' ?>"
                                    value="<?= e($editMember['translations'][$loc]['role'] ?? '') ?>" required>
                            </div>
                            <div class="admin-form-group" style="grid-column: span 2;">
                                <label>Short Bio (<?= $loc ?>)</label>
                                <textarea name="bio_<?= $loc ?>" class="form-input <?= $loc === 'ar' ? 'rtl-input' : '' ?>" rows="3"><?= e($editMember['translations'][$loc]['bio'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <div style="margin-top: 20px; display: flex; gap: 10px;">
                        <button type="submit" class="btn-admin-save">Save Member</button>
                        <a href="<?= baseUrl('admin/team') ?>" class="btn-ghost">Cancel</a>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div class="admin-card">
                <div style="overflow-x: auto;">
                    <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th>Name (EN)</th>
                            <th>Role (EN)</th>
                            <th>Sort</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($members)): ?>
                        <tr><td colspan="6" style="text-align:center; color: var(--text-muted); padding: 40px;">No team members yet. <a href="<?= baseUrl('admin/team?action=new') ?>" style="color: var(--theme-primary);">Add the first one.</a></td></tr>
                        <?php endif; ?>
                        <?php foreach ($members as $m): ?>
                        <?php
                            $transArr = [];
                            foreach (explode('|', $m['trans'] ?? '') as $part) {
                                [$l, $n] = array_pad(explode(':', $part, 2), 2, '');
                                $transArr[$l] = $n;
                            }
                        ?>
                        <tr>
                            <td>
                                <?php if ($m['image_url']): ?>
                                    <img src="<?= e($m['image_url']) ?>" style="width:44px; height:44px; border-radius:50%; object-fit:cover;">
                                <?php else: ?>
                                    <div style="width:44px; height:44px; border-radius:50%; background: linear-gradient(135deg, var(--theme-primary), var(--theme-gold)); display:inline-flex; align-items:center; justify-content:center; font-weight:700; font-size:1rem; color:white;">
                                        <?= strtoupper(substr($transArr['en'] ?? 'M', 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><?= e($transArr['en'] ?? '—') ?></td>
                            <td style="color: var(--text-muted); font-size: 0.88rem;"><?= e($transArr['ar'] ?? '') ?></td>
                            <td><?= $m['sort_order'] ?></td>
                            <td><?= $m['is_active'] ? '<span style="color:var(--neon-emerald)">Active</span>' : '<span style="color:var(--text-muted)">Inactive</span>' ?></td>
                            <td>
                                <a href="<?= baseUrl('admin/team?action=edit&id='.$m['id']) ?>" style="color: var(--neon-cyan); margin-right: 10px;">Edit</a>
                                <a href="<?= baseUrl('admin/team?action=delete&id='.$m['id']) ?>" style="color: #ef4444;" onclick="return confirm('Delete this team member?');">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
