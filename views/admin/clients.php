<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clients — <?= APP_NAME ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= baseUrl('assets/css/style.css') ?>">
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<div class="admin-layout">
    <?php $currentPage = 'clients'; require __DIR__ . '/partials/sidebar.php'; ?>
    <div class="admin-main">
        <div class="admin-header">
            <h1>🤝 Our Clients</h1>
            <a href="<?= baseUrl('admin/clients?action=new') ?>" class="btn-primary" style="padding: 8px 16px; font-size: 0.9rem;">+ Add Client</a>
        </div>
        
        <?php if ($saved): ?>
            <div class="alert alert-success">Saved successfully.</div>
        <?php endif; ?>

        <?php if ($action === 'edit' || $action === 'new'): ?>
            <div class="admin-card" style="margin-bottom: 30px;">
                <form method="POST" action="<?= baseUrl('admin/clients') ?>">
                    <input type="hidden" name="id" value="<?= $editClient['id'] ?? 0 ?>">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label>Client Name</label>
                            <input type="text" name="name" class="form-input" value="<?= e($editClient['name'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Logo URL (optional)</label>
                            <input type="text" name="logo_url" class="form-input" value="<?= e($editClient['logo_url'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Website URL (optional)</label>
                            <input type="text" name="website_url" class="form-input" value="<?= e($editClient['website_url'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Sort Order</label>
                            <input type="number" name="sort_order" class="form-input" value="<?= $editClient['sort_order'] ?? 0 ?>">
                        </div>
                        <div class="form-group" style="display: flex; align-items: center; margin-top: 25px;">
                            <label style="cursor: pointer; display: flex; align-items: center; gap: 10px;">
                                <input type="checkbox" name="is_active" <?= (!isset($editClient) || $editClient['is_active']) ? 'checked' : '' ?>>
                                Active
                            </label>
                        </div>
                    </div>

                    <div style="margin-top: 20px;">
                        <button type="submit" class="btn-primary">Save Client</button>
                        <a href="<?= baseUrl('admin/clients') ?>" class="btn-ghost" style="margin-left: 10px;">Cancel</a>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div class="admin-card">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Logo</th>
                            <th>Sort</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clients as $c): ?>
                        <tr>
                            <td><?= e($c['name']) ?></td>
                            <td>
                                <?php if($c['logo_url']): ?>
                                    <img src="<?= e($c['logo_url']) ?>" style="height:30px; border-radius:4px; max-width:100px; object-fit:contain;">
                                <?php else: ?>
                                    <span style="color:var(--text-muted)">No Logo</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $c['sort_order'] ?></td>
                            <td><?= $c['is_active'] ? '<span style="color:var(--neon-emerald)">Active</span>' : '<span style="color:var(--text-muted)">Inactive</span>' ?></td>
                            <td>
                                <a href="<?= baseUrl('admin/clients?action=edit&id='.$c['id']) ?>" style="color: var(--neon-cyan); margin-right: 10px;">Edit</a>
                                <a href="<?= baseUrl('admin/clients?action=delete&id='.$c['id']) ?>" style="color: var(--neon-pink);" onclick="return confirm('Delete?');">Delete</a>
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
