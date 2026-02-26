<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blogs — <?= APP_NAME ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= baseUrl('assets/css/style.css') ?>">
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<div class="admin-layout">
    <?php $currentPage = 'blogs'; require __DIR__ . '/partials/sidebar.php'; ?>
    <div class="admin-main">
        <div class="admin-header">
            <h1>📰 Blogs</h1>
            <a href="<?= baseUrl('admin/blogs?action=new') ?>" class="btn-primary" style="padding: 8px 16px; font-size: 0.9rem;">+ Add Blog</a>
        </div>
        
        <?php if ($saved): ?>
            <div class="alert alert-success">Saved successfully.</div>
        <?php endif; ?>

        <?php if ($action === 'edit' || $action === 'new'): ?>
            <div class="admin-card" style="margin-bottom: 30px;">
                <form method="POST" action="<?= baseUrl('admin/blogs') ?>" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $editBlog['id'] ?? 0 ?>">
                    <div class="admin-grid-2">
                        <div class="form-group">
                            <label>Slug (URL-friendly name, auto-generated if empty)</label>
                            <input type="text" name="slug" class="form-input" value="<?= e($editBlog['slug'] ?? '') ?>" placeholder="e.g. my-first-blog">
                        </div>
                        <div class="form-group">
                            <label>Media Type</label>
                            <select name="media_type" class="form-input">
                                <?php $mt = $editBlog['media_type'] ?? 'image'; ?>
                                <option value="image" <?= $mt==='image'?'selected':'' ?>>Image (.jpg, .png)</option>
                                <option value="video" <?= $mt==='video'?'selected':'' ?>>Video File (.mp4, .webm)</option>
                                <option value="video_link" <?= $mt==='video_link'?'selected':'' ?>>External Video Link</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Media File Upload</label>
                            <input type="file" name="media_file" class="form-input" accept="image/*,video/mp4,video/webm">
                            <?php if(!empty($editBlog['media_url']) && !str_starts_with($editBlog['media_url'], 'http')): ?>
                                <small style="display:block; margin-top:5px; color:var(--text-muted)">Current: <a href="<?= baseUrl($editBlog['media_url']) ?>" target="_blank" style="color:var(--neon-cyan)">View Media</a></small>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label>Media URL (External Link)</label>
                            <input type="text" name="media_url" class="form-input" value="<?= e($editBlog['media_url'] ?? '') ?>" placeholder="https://youtube.com/...">
                        </div>
                        <div class="form-group">
                            <label>Sort Order</label>
                            <input type="number" name="sort_order" class="form-input" value="<?= $editBlog['sort_order'] ?? 0 ?>">
                        </div>
                        <div class="form-group" style="display: flex; align-items: center; margin-top: 25px; gap: 20px;">
                            <label style="cursor: pointer; display: flex; align-items: center; gap: 10px;">
                                <input type="checkbox" name="is_active" <?= (!isset($editBlog) || $editBlog['is_active']) ? 'checked' : '' ?>>
                                Active
                            </label>
                        </div>
                    </div>

                    <?php foreach (SUPPORTED_LOCALES as $loc): ?>
                        <div style="margin-top: 20px; padding: 15px; background: rgba(255,255,255,0.02); border-radius: 8px;">
                            <h4 style="margin-bottom: 10px;"><?= strtoupper($loc) ?> Translation</h4>
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" name="title_<?= $loc ?>" class="form-input" value="<?= e($editBlog['translations'][$loc]['title'] ?? '') ?>" dir="<?= $loc === 'ar' ? 'rtl' : 'ltr' ?>" required>
                            </div>
                            <div class="form-group" style="margin-top: 10px;">
                                <label>Short Description (Under Title)</label>
                                <textarea name="desc_<?= $loc ?>" class="form-input" rows="3" dir="<?= $loc === 'ar' ? 'rtl' : 'ltr' ?>"><?= e($editBlog['translations'][$loc]['description'] ?? '') ?></textarea>
                            </div>
                            <div class="form-group" style="margin-top: 10px;">
                                <label>Main Content (After Image - HTML allowed)</label>
                                <textarea name="content_<?= $loc ?>" class="form-input" rows="12" dir="<?= $loc === 'ar' ? 'rtl' : 'ltr' ?>"><?= e($editBlog['translations'][$loc]['content'] ?? '') ?></textarea>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div style="margin-top: 20px;">
                        <button type="submit" class="btn-primary">Save Blog</button>
                        <a href="<?= baseUrl('admin/blogs') ?>" class="btn-ghost" style="margin-left: 10px;">Cancel</a>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div class="admin-card">
                <div class="mobile-table-wrapper" style="overflow-x: auto;">
                    <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Slug</th>
                            <th>Translations</th>
                            <th>Media Type</th>
                            <th>Views</th>
                            <th>Sort</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($blogs as $b): ?>
                        <tr>
                            <td data-label="Slug"><?= e($b['slug']) ?></td>
                            <td data-label="Translations" style="max-width:300px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?= e($b['trans'] ?? '') ?></td>
                            <td data-label="Media Type"><span style="padding:4px 8px;border-radius:4px;background:rgba(255,255,255,0.1);font-size:0.7rem;"><?= e($b['media_type']) ?></span></td>
                            <td data-label="Views"><?= number_format($b['view_count'] ?? 0) ?></td>
                            <td data-label="Sort"><?= $b['sort_order'] ?></td>
                            <td data-label="Status"><?= $b['is_active'] ? '<span style="color:var(--neon-emerald)">Active</span>' : '<span style="color:var(--text-muted)">Inactive</span>' ?></td>
                            <td data-label="Actions">
                                <a href="<?= baseUrl('admin/blogs?action=edit&id='.$b['id']) ?>" style="color: var(--neon-cyan); margin-right: 10px;">Edit</a>
                                <a href="<?= baseUrl('admin/blogs?action=delete&id='.$b['id']) ?>" style="color: var(--neon-pink);" onclick="return confirm('Delete this blog?');">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($blogs)): ?>
                        <tr>
                            <td colspan="7" style="text-align:center; padding: 20px;">No blogs found.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                    </table>
                </div>

    <style>
        @media screen and (max-width: 768px) {
            .admin-table thead { display: none; }
            .admin-table tr { 
                display: block; 
                margin-bottom: 20px; 
                background: rgba(255,255,255,0.03); 
                border-radius: 12px; 
                padding: 10px;
                border: 1px solid var(--glass-border);
            }
            .admin-table td { 
                display: flex; 
                justify-content: space-between; 
                align-items: center; 
                text-align: right; 
                padding: 10px 5px; 
                border-bottom: 1px solid rgba(255,255,255,0.05);
            }
            .admin-table td:last-child { border-bottom: none; }
            .admin-table td::before { 
                content: attr(data-label); 
                font-weight: 700; 
                text-transform: uppercase; 
                font-size: 0.75rem; 
                color: var(--neon-cyan);
                margin-right: 15px;
            }
        }
    </style>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
