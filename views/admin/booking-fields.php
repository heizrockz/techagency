<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Form Fields — <?= APP_NAME ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= baseUrl('assets/css/style.css') ?>">
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<div class="admin-layout">
    <?php $currentPage = 'booking_fields'; require __DIR__ . '/partials/sidebar.php'; ?>
    <div class="admin-main">
        <div class="admin-header">
            <h1>📝 Booking Form Builder</h1>
            <a href="<?= baseUrl('admin/booking-fields?action=new') ?>" class="btn-primary" style="padding: 8px 16px; font-size: 0.9rem;">+ Add Field</a>
        </div>
        
        <?php if ($saved): ?>
            <div class="alert alert-success">Saved successfully.</div>
        <?php endif; ?>

        <?php if ($action === 'edit' || $action === 'new'): ?>
            <div class="admin-card" style="margin-bottom: 30px;">
                <form method="POST" action="<?= baseUrl('admin/booking-fields') ?>">
                    <input type="hidden" name="id" value="<?= $editField['id'] ?? 0 ?>">
                    <div class="admin-grid-2">
                        <div class="form-group">
                            <label>Field Name (code handle) *</label>
                            <input type="text" name="field_name" class="form-input" value="<?= e($editField['field_name'] ?? '') ?>" required <?= isset($editField) && in_array($editField['field_name'], ['name','email','phone','service','date','message']) ? 'readonly' : '' ?>>
                            <small style="color:var(--text-muted)">Must be unique, lowercase, no spaces.</small>
                        </div>
                        <div class="form-group">
                            <label>Field Type</label>
                            <select name="field_type" class="form-input">
                                <?php $ft = $editField['field_type'] ?? 'text'; ?>
                                <option value="text" <?= $ft==='text'?'selected':'' ?>>Text Line</option>
                                <option value="email" <?= $ft==='email'?'selected':'' ?>>Email</option>
                                <option value="tel" <?= $ft==='tel'?'selected':'' ?>>Phone</option>
                                <option value="number" <?= $ft==='number'?'selected':'' ?>>Number</option>
                                <option value="select" <?= $ft==='select'?'selected':'' ?>>Select Dropdown</option>
                                <option value="date" <?= $ft==='date'?'selected':'' ?>>Date Picker</option>
                                <option value="textarea" <?= $ft==='textarea'?'selected':'' ?>>Multiline Textarea</option>
                            </select>
                        </div>
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label>Dropdown Options (comma separated, only if type is Select)</label>
                            <input type="text" name="options" class="form-input" value="<?= e($editField['options'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Sort Order</label>
                            <input type="number" name="sort_order" class="form-input" value="<?= $editField['sort_order'] ?? 0 ?>">
                        </div>
                        <div class="form-group" style="display: flex; align-items: center; gap: 20px; margin-top: 25px;">
                            <label style="cursor: pointer; display: flex; align-items: center; gap: 10px;">
                                <input type="checkbox" name="is_required" <?= (!isset($editField) || $editField['is_required']) ? 'checked' : '' ?>> Required
                            </label>
                            <label style="cursor: pointer; display: flex; align-items: center; gap: 10px;">
                                <input type="checkbox" name="is_active" <?= (!isset($editField) || $editField['is_active']) ? 'checked' : '' ?>> Active
                            </label>
                        </div>
                    </div>

                    <?php foreach (SUPPORTED_LOCALES as $loc): ?>
                        <div style="margin-top: 20px; padding: 15px; background: rgba(255,255,255,0.02); border-radius: 8px;">
                            <h4 style="margin-bottom: 10px;"><?= strtoupper($loc) ?> Label & Placeholder</h4>
                            <div class="form-group">
                                <label>Label</label>
                                <input type="text" name="label_<?= $loc ?>" class="form-input" value="<?= e($editField['translations'][$loc]['label'] ?? '') ?>" dir="<?= $loc === 'ar' ? 'rtl' : 'ltr' ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Placeholder</label>
                                <input type="text" name="placeholder_<?= $loc ?>" class="form-input" value="<?= e($editField['translations'][$loc]['placeholder'] ?? '') ?>" dir="<?= $loc === 'ar' ? 'rtl' : 'ltr' ?>">
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div style="margin-top: 20px;">
                        <button type="submit" class="btn-primary">Save Field</button>
                        <a href="<?= baseUrl('admin/booking-fields') ?>" class="btn-ghost" style="margin-left: 10px;">Cancel</a>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div class="admin-card">
                <div style="overflow-x: auto;">
                    <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Field Handle</th>
                            <th>Type</th>
                            <th>Translations</th>
                            <th>Required</th>
                            <th>Sort</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($fields as $f): ?>
                        <tr>
                            <td><strong><?= e($f['field_name']) ?></strong></td>
                            <td><span style="padding:4px 8px;border-radius:4px;background:var(--glass-bg);font-size:0.75rem;"><?= e($f['field_type']) ?></span></td>
                            <td><?= e($f['trans']) ?></td>
                            <td><?= $f['is_required'] ? 'Yes' : 'No' ?></td>
                            <td><?= $f['sort_order'] ?></td>
                            <td><?= $f['is_active'] ? '<span style="color:var(--neon-emerald)">Active</span>' : '<span style="color:var(--text-muted)">Inactive</span>' ?></td>
                            <td>
                                <a href="<?= baseUrl('admin/booking-fields?action=edit&id='.$f['id']) ?>" style="color: var(--neon-cyan); margin-right: 10px;">Edit</a>
                                <?php if (!in_array($f['field_name'], ['name','email','phone','service','date','message'])): ?>
                                <a href="<?= baseUrl('admin/booking-fields?action=delete&id='.$f['id']) ?>" style="color: var(--neon-pink);" onclick="return confirm('Delete field? Data in bookings will be preserved in JSON but the field will be removed from form.');">Delete</a>
                                <?php endif; ?>
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
