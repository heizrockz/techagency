<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <title><?= t('admin_seo') ?> — <?= APP_NAME ?></title>
    <?php require __DIR__ . '/partials/_head_assets.php'; ?>
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">

<div class="admin-layout flex w-full h-screen overflow-hidden">
    <?php $currentPage = 'seo'; require __DIR__ . '/partials/sidebar.php'; ?>

    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-20 flex items-center justify-between px-8 bg-glass-bg border-b border-white/5 shrink-0 backdrop-blur-xl sticky top-0 z-[100]">
            <div class="flex flex-col">
                <div class="text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] mb-1 hidden sm:block">Search Optimization</div>
                <h1 class="text-xl font-black text-white tracking-tight flex items-center gap-3 group">
                    <span class="text-neon-cyan drop-shadow-[0_0_8px_rgba(6,182,212,0.4)]">Vector Alignment</span>
                    <span class="opacity-20 translate-y-px hidden sm:inline">/</span>
                    <span class="text-sm tracking-widest text-slate-400 uppercase font-black hidden sm:inline">Meta Protocols</span>
                </h1>
            </div>
            <div class="flex items-center gap-6">
                <?php require __DIR__ . '/partials/_topbar.php'; ?>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-8 crm-main-scroll bg-[#0b0e14]">
            <?php if ($saved): ?>
                <div class="mb-8 p-4 bg-neon-emerald/10 border border-neon-emerald/20 rounded-2xl text-neon-emerald text-[10px] font-black uppercase tracking-widest flex items-center gap-3">
                    <i class="ph-bold ph-check-circle text-lg"></i> <?= t('admin_saved') ?>
                </div>
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
        </main>
    </div>
</div>

</body>
</html>
