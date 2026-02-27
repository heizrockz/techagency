<?php
/**
 * Custom 404 Error Page
 */
http_response_code(404);
$seo = [
    'title' => '404 - Page Not Found | ' . getSetting('site_name', APP_NAME),
    'description' => 'Sorry, the page you are looking for does not exist or has been moved.',
    'keywords' => '404, page not found'
];
$locale = getCurrentLocale();
$dir = isRTL() ? 'rtl' : 'ltr';
?>

<section class="error-404-section" style="padding: 120px 0; text-align: center; min-height: 70vh; display: flex; align-items: center; justify-content: center;">
    <div class="section-container">
        <div class="error-visual" style="margin-bottom: 40px;">
            <h1 style="font-size: 8rem; font-weight: 900; background: linear-gradient(135deg, var(--theme-primary), var(--theme-gold)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; line-height: 1;">404</h1>
            <div class="orb" style="width: 200px; height: 200px; background: radial-gradient(circle, var(--theme-primary), transparent); position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); opacity: 0.2; filter: blur(40px); z-index: -1;"></div>
        </div>
        
        <h2 style="font-size: 2rem; margin-bottom: 20px;"><?= $locale === 'ar' ? 'الصفحة غير موجودة' : 'Oops! Page Not Found' ?></h2>
        <p style="color: var(--text-secondary); max-width: 600px; margin: 0 auto 40px; font-size: 1.1rem;">
            <?= $locale === 'ar' ? 'عذراً، الصفحة التي تبحث عنها قد تم نقلها أو أنها لم تعد موجودة.' : 'The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.' ?>
        </p>
        
        <div class="error-actions" style="display: flex; gap: 20px; justify-content: center;">
            <a href="<?= baseUrl('/') ?>" class="btn-primary">
                <?= $locale === 'ar' ? 'العودة للرئيسية' : 'Back to Home' ?>
            </a>
            <a href="<?= baseUrl('/#booking') ?>" class="btn-secondary" style="background: var(--glass-bg); border: 1px solid var(--glass-border); padding: 12px 30px; border-radius: 999px; text-decoration: none; color: var(--text-primary);">
                <?= $locale === 'ar' ? 'تواصل معنا' : 'Contact Support' ?>
            </a>
        </div>
    </div>
</section>
