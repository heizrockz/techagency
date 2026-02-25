<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('admin_seo') ?> — <?= APP_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= baseUrl('assets/css/style.css') ?>">
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">

<div class="admin-layout">
    <?php $currentPage = 'seo'; require __DIR__ . '/partials/sidebar.php'; ?>

    <div class="admin-main">
        <div class="admin-header">
            <h1>🔍 <?= t('admin_seo') ?></h1>
        </div>

        <?php if ($saved): ?>
            <div class="alert alert-success"><?= t('admin_saved') ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= baseUrl('admin/seo') ?>" enctype="multipart/form-data">
            <div class="content-section">
                <h3>🌍 Global SEO Settings</h3>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 20px;">Settings that apply site-wide, such as the browser tab icon and link sharing images.</p>
                
                <div class="admin-grid-2">
                    <div class="admin-form-group" style="display: flex; align-items: center; gap: 20px;">
                        <div style="flex:1;">
                            <label>Favicon (Browser Tab Icon)</label>
                            <input type="file" name="seo_favicon" class="form-input" accept="image/*,.ico">
                            <small style="color:var(--text-muted); display:block; margin-top:5px;">Recommended format: ICO or completely square PNG.</small>
                        </div>
                        <?php if(!empty($globalSeo['seo_favicon'])): ?>
                            <div style="background: rgba(255,255,255,0.05); padding: 10px; border-radius: 8px; border: 1px solid var(--glass-border);">
                                <img src="<?= baseUrl($globalSeo['seo_favicon']) ?>" alt="Current Favicon" style="max-height: 48px; max-width: 48px;">
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="admin-form-group" style="display: flex; align-items: center; gap: 20px;">
                        <div style="flex:1;">
                            <label>OpenGraph Image (Link Sharing Preview)</label>
                            <input type="file" name="seo_og_image" class="form-input" accept="image/*">
                            <small style="color:var(--text-muted); display:block; margin-top:5px;">Image that shows when your site is shared on WhatsApp, Facebook, etc.</small>
                        </div>
                        <?php if(!empty($globalSeo['seo_og_image'])): ?>
                            <div style="background: rgba(255,255,255,0.05); padding: 10px; border-radius: 8px; border: 1px solid var(--glass-border);">
                                <img src="<?= baseUrl($globalSeo['seo_og_image']) ?>" alt="Current OG Image" style="max-height: 48px;">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php foreach ($seoData as $page => $locales): ?>
                <div class="content-section">
                    <h3 style="text-transform: capitalize;">📄 <?= e($page) ?></h3>

                    <?php foreach (SUPPORTED_LOCALES as $loc): ?>
                        <div style="margin-bottom: 20px; padding: 16px; background: rgba(255,255,255,0.02); border-radius: var(--radius-sm);">
                            <p style="font-weight: 600; font-size: 0.85rem; margin-bottom: 12px; color: var(--neon-cyan);">
                                <?= $loc === 'en' ? '🇬🇧 English' : '🇸🇦 Arabic' ?>
                            </p>
                            <div class="admin-form-group">
                                <label>SEO Title</label>
                                <input type="text"
                                       name="seo[<?= e($page) ?>][<?= $loc ?>][title]"
                                       value="<?= e($locales[$loc]['title'] ?? '') ?>"
                                       class="form-input"
                                       dir="<?= $loc === 'ar' ? 'rtl' : 'ltr' ?>">
                            </div>
                            <div class="admin-form-group">
                                <label>Canonical Link (Optional)</label>
                                <input type="url"
                                       name="seo[<?= e($page) ?>][<?= $loc ?>][canonical_link]"
                                       value="<?= e($locales[$loc]['canonical_link'] ?? '') ?>"
                                       class="form-input"
                                       dir="ltr"
                                       placeholder="https://example.com/...">
                                <small style="color:var(--text-muted);">Leave blank unless you are resolving duplicate content issues on another domain.</small>
                            </div>
                            <div class="admin-form-group">
                                <label>Meta Description</label>
                                <textarea name="seo[<?= e($page) ?>][<?= $loc ?>][description]"
                                          rows="2"
                                          dir="<?= $loc === 'ar' ? 'rtl' : 'ltr' ?>"><?= e($locales[$loc]['description'] ?? '') ?></textarea>
                            </div>
                            <div class="admin-form-group">
                                <label>Meta Keywords</label>
                                <input type="text"
                                       name="seo[<?= e($page) ?>][<?= $loc ?>][keywords]"
                                       value="<?= e($locales[$loc]['keywords'] ?? '') ?>"
                                       dir="<?= $loc === 'ar' ? 'rtl' : 'ltr' ?>">
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>

            <button type="submit" class="btn-admin-save"><?= t('admin_save') ?></button>
        </form>
    </div>
</div>

</body>
</html>
