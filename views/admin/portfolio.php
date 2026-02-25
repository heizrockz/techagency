<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio — <?= APP_NAME ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= baseUrl('assets/css/style.css') ?>">
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<div class="admin-layout">
    <?php $currentPage = 'portfolio'; require __DIR__ . '/partials/sidebar.php'; ?>
    <div class="admin-main">
        <div class="admin-header">
            <h1>🎨 Portfolio Projects</h1>
            <a href="<?= baseUrl('admin/portfolio?action=new') ?>" class="btn-primary" style="padding: 8px 16px; font-size: 0.9rem;">+ Add Project</a>
        </div>
        
        <?php if ($saved): ?>
            <div class="alert alert-success">Saved successfully.</div>
        <?php endif; ?>

        <?php if ($action === 'edit' || $action === 'new'): ?>
            <div class="admin-card" style="margin-bottom: 30px;">
                <form method="POST" action="<?= baseUrl('admin/portfolio') ?>">
                    <input type="hidden" name="id" value="<?= $editProject['id'] ?? 0 ?>">
                    <div class="admin-grid-2">
                        <div class="form-group">
                            <label>Slug (URL-friendly name)</label>
                            <input type="text" name="slug" class="form-input" value="<?= e($editProject['slug'] ?? '') ?>" placeholder="e.g. my-project" required>
                        </div>
                        <div class="form-group">
                            <label>Category</label>
                            <select name="category" class="form-input">
                                <?php $cat = $editProject['category'] ?? 'website'; ?>
                                <option value="website" <?= $cat==='website'?'selected':'' ?>>Website</option>
                                <option value="app" <?= $cat==='app'?'selected':'' ?>>App</option>
                                <option value="branding" <?= $cat==='branding'?'selected':'' ?>>Branding</option>
                                <option value="marketing" <?= $cat==='marketing'?'selected':'' ?>>Marketing</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Color (cobalt, violet, emerald, pink, cyan, orange)</label>
                            <input type="text" name="color" class="form-input" value="<?= e($editProject['color'] ?? 'cobalt') ?>">
                        </div>
                        <div class="form-group">
                            <label>Sort Order</label>
                            <input type="number" name="sort_order" class="form-input" value="<?= $editProject['sort_order'] ?? 0 ?>">
                        </div>
                        <div class="form-group">
                            <label>Image URL (optional)</label>
                            <input type="text" name="image_url" class="form-input" value="<?= e($editProject['image_url'] ?? '') ?>" placeholder="https://...">
                        </div>
                        <div class="form-group">
                            <label>Demo URL (optional)</label>
                            <input type="text" name="demo_url" class="form-input" value="<?= e($editProject['demo_url'] ?? '') ?>" placeholder="https://...">
                        </div>
                        <div class="form-group" style="display: flex; align-items: center; margin-top: 25px; gap: 20px;">
                            <label style="cursor: pointer; display: flex; align-items: center; gap: 10px;">
                                <input type="checkbox" name="is_active" <?= (!isset($editProject) || $editProject['is_active']) ? 'checked' : '' ?>>
                                Active
                            </label>
                            <label style="cursor: pointer; display: flex; align-items: center; gap: 10px;">
                                <input type="checkbox" name="is_featured" <?= (isset($editProject) && $editProject['is_featured']) ? 'checked' : '' ?>>
                                Featured (larger card)
                            </label>
                        </div>
                    </div>

                    <?php foreach (SUPPORTED_LOCALES as $loc): ?>
                        <div style="margin-top: 20px; padding: 15px; background: rgba(255,255,255,0.02); border-radius: 8px;">
                            <h4 style="margin-bottom: 10px;"><?= strtoupper($loc) ?> Translation</h4>
                            <div class="admin-grid-2">
                                <div class="form-group">
                                    <label>Title</label>
                                    <input type="text" name="title_<?= $loc ?>" class="form-input" value="<?= e($editProject['translations'][$loc]['title'] ?? '') ?>" dir="<?= $loc === 'ar' ? 'rtl' : 'ltr' ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Client Name</label>
                                    <input type="text" name="client_<?= $loc ?>" class="form-input" value="<?= e($editProject['translations'][$loc]['client_name'] ?? '') ?>" dir="<?= $loc === 'ar' ? 'rtl' : 'ltr' ?>">
                                </div>
                            </div>
                            <div class="form-group" style="margin-top: 10px;">
                                <label>Description</label>
                                <textarea name="desc_<?= $loc ?>" class="form-input" rows="3" dir="<?= $loc === 'ar' ? 'rtl' : 'ltr' ?>"><?= e($editProject['translations'][$loc]['description'] ?? '') ?></textarea>
                            </div>
                            <div class="form-group" style="margin-top: 10px;">
                                <label>Tags (comma-separated, e.g. Laravel,Vue.js,MySQL)</label>
                                <input type="text" name="tags_<?= $loc ?>" class="form-input" value="<?= e($editProject['translations'][$loc]['tags'] ?? '') ?>" dir="ltr">
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div style="margin-top: 20px;">
                        <button type="submit" class="btn-primary">Save Project</button>
                        <a href="<?= baseUrl('admin/portfolio') ?>" class="btn-ghost" style="margin-left: 10px;">Cancel</a>
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
                            <th>Featured</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($projects as $p): ?>
                        <tr>
                            <td><span style="padding:4px 8px;border-radius:4px;background:rgba(255,255,255,0.1);font-size:0.7rem;"><?= e($p['category']) ?></span></td>
                            <td><?= e($p['trans'] ?? '') ?></td>
                            <td><?= $p['sort_order'] ?></td>
                            <td><?= $p['is_active'] ? '<span style="color:var(--neon-emerald)">Active</span>' : '<span style="color:var(--text-muted)">Inactive</span>' ?></td>
                            <td><?= $p['is_featured'] ? '⭐' : '' ?></td>
                            <td>
                                <a href="<?= baseUrl('admin/portfolio?action=edit&id='.$p['id']) ?>" style="color: var(--neon-cyan); margin-right: 10px;">Edit</a>
                                <a href="<?= baseUrl('admin/portfolio?action=delete&id='.$p['id']) ?>" style="color: var(--neon-pink);" onclick="return confirm('Delete this project?');">Delete</a>
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
