<?php
/**
 * Admin: Customer Testimonials CRUD
 */
?>
<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testimonials — <?= APP_NAME ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= baseUrl('assets/css/style.css') ?>">
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<div class="admin-layout">
    <?php $currentPage = 'testimonials'; require __DIR__ . '/partials/sidebar.php'; ?>
    <div class="admin-main">
        <div class="admin-header">
            <h1>💬 Client Testimonials</h1>
            <a href="<?= baseUrl('admin/testimonials?action=new') ?>" class="btn-primary" style="padding: 8px 16px; font-size: 0.9rem;">+ Add Testimonial</a>
        </div>

        <?php if ($saved): ?>
            <div class="alert alert-success">Testimonial saved successfully.</div>
        <?php endif; ?>

        <?php if ($action === 'edit' || $action === 'new'): ?>
            <div class="admin-card" style="margin-bottom: 30px;">
                <h3 style="margin-bottom: 24px; font-size: 1.1rem;"><?= $editTestimonial ? 'Edit Testimonial' : 'Add New Testimonial' ?></h3>
                <form method="POST" action="<?= baseUrl('admin/testimonials') ?>">
                    <input type="hidden" name="id" value="<?= $editTestimonial['id'] ?? 0 ?>">

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 24px;">
                        <div class="admin-form-group">
                            <label>Client Photo URL (optional)</label>
                            <input type="text" name="client_image_url" class="form-input" placeholder="https://..."
                                value="<?= e($editTestimonial['client_image_url'] ?? '') ?>">
                        </div>
                        <div class="admin-form-group">
                            <label>Star Rating (1–5)</label>
                            <select name="rating" class="form-input">
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                <option value="<?= $i ?>" <?= ($editTestimonial['rating'] ?? 5) == $i ? 'selected' : '' ?>>
                                    <?= str_repeat('★', $i) ?> <?= $i ?> Star<?= $i > 1 ? 's' : '' ?>
                                </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="admin-form-group">
                            <label>Sort Order</label>
                            <input type="number" name="sort_order" class="form-input" value="<?= $editTestimonial['sort_order'] ?? 0 ?>">
                        </div>
                        <div class="admin-form-group" style="display: flex; align-items: center; gap: 10px; margin-top: 10px;">
                            <label style="cursor: pointer; display: flex; align-items: center; gap: 10px;">
                                <input type="checkbox" name="is_active" <?= (!isset($editTestimonial) || $editTestimonial['is_active']) ? 'checked' : '' ?>>
                                Active (visible on site)
                            </label>
                        </div>
                    </div>

                    <?php foreach (SUPPORTED_LOCALES as $loc): ?>
                    <div class="content-section" style="margin-bottom: 16px;">
                        <h3><?= strtoupper($loc) ?> Translation</h3>
                        <div class="content-locale-grid">
                            <div class="admin-form-group">
                                <label>Client Name (<?= $loc ?>)</label>
                                <input type="text" name="client_name_<?= $loc ?>" class="form-input <?= $loc === 'ar' ? 'rtl-input' : '' ?>"
                                    value="<?= e($editTestimonial['translations'][$loc]['client_name'] ?? '') ?>" required>
                            </div>
                            <div class="admin-form-group">
                                <label>Company / Position (<?= $loc ?>)</label>
                                <input type="text" name="client_company_<?= $loc ?>" class="form-input <?= $loc === 'ar' ? 'rtl-input' : '' ?>"
                                    value="<?= e($editTestimonial['translations'][$loc]['client_company'] ?? '') ?>">
                            </div>
                            <div class="admin-form-group" style="grid-column: span 2;">
                                <label>Quote / Review (<?= $loc ?>)</label>
                                <textarea name="content_<?= $loc ?>" class="form-input <?= $loc === 'ar' ? 'rtl-input' : '' ?>"
                                    rows="3" required><?= e($editTestimonial['translations'][$loc]['content'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <div style="margin-top: 20px; display: flex; gap: 10px;">
                        <button type="submit" class="btn-admin-save">Save Testimonial</button>
                        <a href="<?= baseUrl('admin/testimonials') ?>" class="btn-ghost">Cancel</a>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div class="admin-card">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Rating</th>
                            <th>Company (EN)</th>
                            <th>Sort</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($testimonials)): ?>
                        <tr><td colspan="6" style="text-align:center; color: var(--text-muted); padding: 40px;">No testimonials yet. <a href="<?= baseUrl('admin/testimonials?action=new') ?>" style="color: var(--theme-primary);">Add the first one.</a></td></tr>
                        <?php endif; ?>
                        <?php foreach ($testimonials as $t): ?>
                        <?php
                            $transArr = [];
                            foreach (explode('|', $t['trans'] ?? '') as $part) {
                                [$l, $n] = array_pad(explode(':', $part, 2), 2, '');
                                $transArr[$l] = $n;
                            }
                        ?>
                        <tr>
                            <td>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <?php if ($t['client_image_url']): ?>
                                        <img src="<?= e($t['client_image_url']) ?>" style="width:36px; height:36px; border-radius:50%; object-fit:cover;">
                                    <?php else: ?>
                                        <div style="width:36px; height:36px; border-radius:50%; background: linear-gradient(135deg, var(--theme-primary), var(--theme-gold)); display:inline-flex; align-items:center; justify-content:center; font-weight:700; font-size:0.9rem; color:white;">
                                            <?= strtoupper(substr($transArr['en'] ?? 'C', 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                    <span><?= e($transArr['en'] ?? '—') ?></span>
                                </div>
                            </td>
                            <td style="color: var(--theme-gold);"><?= str_repeat('★', intval($t['rating'])) ?></td>
                            <td style="color: var(--text-muted); font-size: 0.88rem;"><?= e($transArr['ar'] ?? '') ?></td>
                            <td><?= $t['sort_order'] ?></td>
                            <td><?= $t['is_active'] ? '<span style="color:var(--neon-emerald)">Active</span>' : '<span style="color:var(--text-muted)">Inactive</span>' ?></td>
                            <td>
                                <a href="<?= baseUrl('admin/testimonials?action=edit&id='.$t['id']) ?>" style="color: var(--neon-cyan); margin-right: 10px;">Edit</a>
                                <a href="<?= baseUrl('admin/testimonials?action=delete&id='.$t['id']) ?>" style="color: #ef4444;" onclick="return confirm('Delete this testimonial?');">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
