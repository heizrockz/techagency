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
                                <button type="button" onclick="showDeleteModal('Product #<?= $p['id'] ?> (<?= e($p['category']) ?>)', '<?= baseUrl('admin/products?action=delete&id='.$p['id']) ?>')" style="color: var(--neon-pink); background:none; border:none; cursor:pointer; font-size:inherit; font-family:inherit;">Delete</button>
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

<!-- Delete Confirmation Modal -->
<div id="deleteModal" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.7); backdrop-filter:blur(8px); align-items:center; justify-content:center;">
    <div style="background:#1a2333; border:1px solid rgba(255,255,255,0.1); border-radius:1.5rem; padding:2rem; max-width:420px; width:90%; box-shadow:0 25px 50px rgba(0,0,0,0.5); animation:modalIn 0.2s ease-out;">
        <div style="text-align:center; margin-bottom:1.5rem;">
            <div style="width:64px; height:64px; border-radius:50%; background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.2); display:flex; align-items:center; justify-content:center; margin:0 auto 1rem;">
                <i class="ph ph-warning" style="font-size:2rem; color:#f87171;"></i>
            </div>
            <h3 style="color:#fff; font-size:1.25rem; font-weight:700; margin-bottom:0.5rem;">Confirm Deletion</h3>
            <p style="color:#94a3b8; font-size:0.875rem; line-height:1.6;">Are you sure you want to delete <strong id="deleteItemName" style="color:#f87171;"></strong>? This action cannot be undone.</p>
        </div>
        <div style="display:flex; gap:0.75rem; justify-content:center;">
            <button onclick="closeDeleteModal()" style="padding:0.625rem 1.5rem; background:rgba(255,255,255,0.05); color:#94a3b8; font-weight:700; font-size:0.7rem; text-transform:uppercase; letter-spacing:0.1em; border-radius:0.75rem; border:1px solid rgba(255,255,255,0.1); cursor:pointer; transition:all 0.2s;">Cancel</button>
            <a id="deleteConfirmBtn" href="#" style="padding:0.625rem 1.5rem; background:#ef4444; color:#fff; font-weight:700; font-size:0.7rem; text-transform:uppercase; letter-spacing:0.1em; border-radius:0.75rem; border:none; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:0.5rem; transition:all 0.2s;">
                <i class="ph ph-trash"></i> Delete
            </a>
        </div>
    </div>
</div>
<style>
@keyframes modalIn { from { opacity:0; transform:scale(0.95) translateY(10px); } to { opacity:1; transform:scale(1) translateY(0); } }
#deleteModal button:hover { background:rgba(255,255,255,0.1) !important; color:#fff !important; }
#deleteConfirmBtn:hover { background:#dc2626 !important; box-shadow:0 0 20px rgba(239,68,68,0.3); }
</style>
<script>
function showDeleteModal(name, url) {
    document.getElementById('deleteItemName').textContent = name;
    document.getElementById('deleteConfirmBtn').href = url;
    document.getElementById('deleteModal').style.display = 'flex';
}
function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
}
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});
</script>
</body>
</html>
