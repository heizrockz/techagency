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
                    <div class="admin-grid-3">
                        <div class="form-group">
                            <label>Slug (URL-friendly name, auto-generated if empty)</label>
                            <input type="text" name="slug" class="form-input" value="<?= e($editBlog['slug'] ?? '') ?>" placeholder="e.g. my-first-blog">
                        </div>
                        <div class="form-group">
                            <label>Sort Order (Higher = first)</label>
                            <input type="number" name="sort_order" class="form-input" value="<?= $editBlog['sort_order'] ?? 0 ?>">
                        </div>
                        <div class="form-group" style="display: flex; align-items: center; margin-top: 25px; gap: 20px;">
                            <label style="cursor: pointer; display: flex; align-items: center; gap: 10px;">
                                <input type="checkbox" name="is_active" <?= (!isset($editBlog) || $editBlog['is_active']) ? 'checked' : '' ?>>
                                Active
                            </label>
                        </div>
                    </div>

                    <div class="admin-card" style="margin-top:20px; border-color:var(--neon-cyan);">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                            <h4 style="margin:0;">🖼️ Media Gallery</h4>
                            <button type="button" class="btn-primary" style="padding:4px 12px; font-size:0.8rem;" onclick="addMediaRow()">+ Add Media</button>
                        </div>
                        <div id="media-rows-container">
                            <?php 
                            $mediaItems = $editBlog['media'] ?? [];
                            if (empty($mediaItems) && !empty($editBlog['media_url'])) {
                                // Default row if editing old blog
                                $mediaItems = [['media_type' => $editBlog['media_type'], 'media_url' => $editBlog['media_url'], 'sort_order' => 0]];
                            }
                            ?>
                            <?php if (empty($mediaItems)): ?>
                                <!-- Initialize at least one empty row -->
                                <div class="media-row admin-grid-4" style="align-items:end; padding-bottom:15px; border-bottom:1px solid rgba(255,255,255,0.05); margin-bottom:15px;">
                                    <div class="form-group">
                                        <label>Type</label>
                                        <select name="media_items[0][type]" class="form-input">
                                            <option value="image">Image</option>
                                            <option value="video">Video File</option>
                                            <option value="video_link">Video Link</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Upload File</label>
                                        <input type="file" name="media_files[0]" class="form-input">
                                    </div>
                                    <div class="form-group">
                                        <label>OR Media URL</label>
                                        <input type="text" name="media_items[0][url]" class="form-input" placeholder="https://...">
                                    </div>
                                    <div class="form-group" style="display:flex; gap:10px; align-items:center;">
                                        <div style="flex:1;">
                                            <label>Sort</label>
                                            <input type="number" name="media_items[0][sort]" class="form-input" value="0">
                                        </div>
                                        <button type="button" class="btn-ghost" style="color:var(--neon-pink); padding:8px; margin-top:25px;" onclick="this.parentElement.parentElement.remove()">✕</button>
                                    </div>
                                </div>
                            <?php else: ?>
                                <?php foreach ($mediaItems as $idx => $m): ?>
                                    <div class="media-row admin-grid-4" style="align-items:end; padding-bottom:15px; border-bottom:1px solid rgba(255,255,255,0.05); margin-bottom:15px;">
                                        <div class="form-group">
                                            <label>Type</label>
                                            <select name="media_items[<?= $idx ?>][type]" class="form-input">
                                                <option value="image" <?= $m['media_type'] === 'image' ? 'selected' : '' ?>>Image</option>
                                                <option value="video" <?= $m['media_type'] === 'video' ? 'selected' : '' ?>>Video File</option>
                                                <option value="video_link" <?= $m['media_type'] === 'video_link' ? 'selected' : '' ?>>Video Link</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Upload File (Replace)</label>
                                            <input type="file" name="media_files[<?= $idx ?>]" class="form-input">
                                        </div>
                                        <div class="form-group">
                                            <label>OR Media URL</label>
                                            <input type="text" name="media_items[<?= $idx ?>][url]" class="form-input" value="<?= e($m['media_url']) ?>">
                                        </div>
                                        <div class="form-group" style="display:flex; gap:10px; align-items:center;">
                                            <div style="flex:1;">
                                                <label>Sort</label>
                                                <input type="number" name="media_items[<?= $idx ?>][sort]" class="form-input" value="<?= $m['sort_order'] ?>">
                                            </div>
                                            <button type="button" class="btn-ghost" style="color:var(--neon-pink); padding:8px; margin-top:25px;" onclick="this.parentElement.parentElement.remove()">✕</button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <script>
                        let mediaRowCount = <?= max(count($mediaItems), 1) ?>;
                        function addMediaRow() {
                            const container = document.getElementById('media-rows-container');
                            const row = document.createElement('div');
                            row.className = 'media-row admin-grid-4';
                            row.style = 'align-items:end; padding-bottom:15px; border-bottom:1px solid rgba(255,255,255,0.05); margin-bottom:15px;';
                            row.innerHTML = `
                                <div class="form-group">
                                    <label>Type</label>
                                    <select name="media_items[${mediaRowCount}][type]" class="form-input">
                                        <option value="image">Image</option>
                                        <option value="video">Video File</option>
                                        <option value="video_link">Video Link</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Upload File</label>
                                    <input type="file" name="media_files[${mediaRowCount}]" class="form-input">
                                </div>
                                <div class="form-group">
                                    <label>OR Media URL</label>
                                    <input type="text" name="media_items[${mediaRowCount}][url]" class="form-input" placeholder="https://...">
                                </div>
                                <div class="form-group" style="display:flex; gap:10px; align-items:center;">
                                    <div style="flex:1;">
                                        <label>Sort</label>
                                        <input type="number" name="media_items[${mediaRowCount}][sort]" class="form-input" value="${mediaRowCount}">
                                    </div>
                                    <button type="button" class="btn-ghost" style="color:var(--neon-pink); padding:8px; margin-top:25px;" onclick="this.parentElement.parentElement.remove()">✕</button>
                                </div>
                            `;
                            container.appendChild(row);
                            mediaRowCount++;
                        }
                    </script>

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
        @media screen and (max-width: 991px) {
            .admin-table thead { display: none; }
            .admin-table, .admin-table tbody, .admin-table tr, .admin-table td { 
                display: block; 
                width: 100%; 
                min-width: auto !important;
            }
            .admin-table tr { 
                margin-bottom: 25px; 
                background: rgba(255,255,255,0.02); 
                border-radius: 16px; 
                padding: 15px;
                border: 1px solid var(--glass-border);
                box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            }
            .admin-table td { 
                display: flex; 
                justify-content: space-between; 
                align-items: center; 
                text-align: right; 
                padding: 12px 0; 
                border-bottom: 1px solid rgba(255,255,255,0.05);
                font-size: 0.9rem;
                overflow-wrap: anywhere;
                word-break: break-word;
            }
            .admin-table td:last-child { border-bottom: none; padding-top: 15px; }
            .admin-table td::before { 
                content: attr(data-label); 
                font-weight: 700; 
                text-transform: uppercase; 
                font-size: 0.7rem; 
                color: var(--neon-cyan);
                letter-spacing: 1px;
                text-align: left;
                min-width: 100px;
                flex-shrink: 0;
            }
            .admin-table td .badge {
                margin: 0;
            }
            .admin-table td[data-label="Translations"] {
                flex-direction: column;
                align-items: flex-start;
                text-align: left;
                gap: 5px;
            }
        }
    </style>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
