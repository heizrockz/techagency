<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products — <?= APP_NAME ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= baseUrl('assets/css/style.css') ?>">
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<div class="admin-layout">
    <?php $currentPage = 'products'; require __DIR__ . '/partials/sidebar.php'; ?>
    <div class="admin-main">
        <div class="admin-header">
            <h1>📦 Products / Ideas</h1>
            <a href="<?= baseUrl('admin/products?action=new') ?>" class="btn-primary" style="padding: 8px 16px; font-size: 0.9rem;">+ Add Product</a>
        </div>
        
        <?php if ($saved): ?>
            <div class="alert alert-success">Saved successfully.</div>
        <?php endif; ?>

        <?php if ($action === 'edit' || $action === 'new'): ?>
            <div class="admin-card" style="margin-bottom: 30px;">
                <form method="POST" action="<?= baseUrl('admin/products') ?>">
                    <input type="hidden" name="id" value="<?= $editProduct['id'] ?? 0 ?>">
                    <div class="admin-grid-2">
                        <div class="form-group">
                            <label>Category</label>
                            <select name="category" class="form-input">
                                <?php $cat = $editProduct['category'] ?? 'website'; ?>
                                <option value="website" <?= $cat==='website'?'selected':'' ?>>Website</option>
                                <option value="app" <?= $cat==='app'?'selected':'' ?>>App</option>
                                <option value="maintenance" <?= $cat==='maintenance'?'selected':'' ?>>Maintenance</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Icon (globe, car, cart, hotel, billing, crm, wrench)</label>
                            <input type="text" name="icon" class="form-input" value="<?= e($editProduct['icon'] ?? 'globe') ?>">
                        </div>
                        <div class="form-group">
                            <label>Color (cobalt, violet, emerald, pink, cyan, orange)</label>
                            <input type="text" name="color" class="form-input" value="<?= e($editProduct['color'] ?? 'cobalt') ?>">
                        </div>
                        <div class="form-group">
                            <label>Sort Order</label>
                            <input type="number" name="sort_order" class="form-input" value="<?= $editProduct['sort_order'] ?? 0 ?>">
                        </div>
                        <div class="form-group" style="display: flex; align-items: center; margin-top: 25px;">
                            <label style="cursor: pointer; display: flex; align-items: center; gap: 10px;">
                                <input type="checkbox" name="is_active" <?= (!isset($editProduct) || $editProduct['is_active']) ? 'checked' : '' ?>>
                                Active
                            </label>
                        </div>
                    </div>

                    <?php foreach (SUPPORTED_LOCALES as $loc): ?>
                        <div style="margin-top: 20px; padding: 15px; background: rgba(255,255,255,0.02); border-radius: 8px;">
                            <h4 style="margin-bottom: 10px;"><?= strtoupper($loc) ?> Translation</h4>
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" name="title_<?= $loc ?>" class="form-input" value="<?= e($editProduct['translations'][$loc]['title'] ?? '') ?>" dir="<?= $loc === 'ar' ? 'rtl' : 'ltr' ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="desc_<?= $loc ?>" class="form-input" rows="3" dir="<?= $loc === 'ar' ? 'rtl' : 'ltr' ?>"><?= e($editProduct['translations'][$loc]['description'] ?? '') ?></textarea>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div style="margin-top: 20px;">
                        <button type="submit" class="btn-primary">Save Product</button>
                        <a href="<?= baseUrl('admin/products') ?>" class="btn-ghost" style="margin-left: 10px;">Cancel</a>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div class="admin-card">
                <div style="overflow-x: auto;">
                    <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Translations</th>
                            <th>Sort</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $p): ?>
                        <tr>
                            <td><span style="padding:4px 8px;border-radius:4px;background:var(--neon-<?= $p['color'] ?>);color:#fff;font-size:0.7rem;"><?= e($p['category']) ?></span></td>
                            <td><?= e($p['trans']) ?></td>
                            <td><?= $p['sort_order'] ?></td>
                            <td><?= $p['is_active'] ? '<span style="color:var(--neon-emerald)">Active</span>' : '<span style="color:var(--text-muted)">Inactive</span>' ?></td>
                            <td>
                                <a href="<?= baseUrl('admin/products?action=edit&id='.$p['id']) ?>" style="color: var(--neon-cyan); margin-right: 10px;">Edit</a>
                                <a href="<?= baseUrl('admin/products?action=delete&id='.$p['id']) ?>" style="color: var(--neon-pink);" onclick="return confirm('Delete?');">Delete</a>
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
