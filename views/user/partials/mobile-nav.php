<!-- Mobile Navigation -->
<div class="mobile-nav" id="mobileNav">
    <a href="#hero"><?= t('nav_home') ?></a>
    <a href="#services"><?= t('nav_services') ?></a>
    <a href="#about"><?= t('nav_about') ?></a>
    <a href="<?= baseUrl('portfolio') ?>"><?= t('nav_portfolio') ?></a>
    <a href="#booking"><?= t('nav_booking') ?></a>
    <a href="<?= baseUrl('?lang=' . (getCurrentLocale() === 'en' ? 'ar' : 'en')) ?>" class="nav-lang-btn" style="text-align:center; margin-top: 8px;">
        <?= t('nav_lang') ?>
    </a>
</div>
