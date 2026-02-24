<!-- Floating Island Navbar -->
<nav class="navbar-island" id="mainNavbar">
    <?php $navLogo = getSetting('site_logo'); ?>
    <?php if(!empty($navLogo)): ?>
        <a href="<?= baseUrl('/') ?>" class="nav-logo"><img src="<?= baseUrl($navLogo) ?>" alt="<?= e(getSetting('site_name', APP_NAME)) ?>" style="max-height:32px;"></a>
    <?php else: ?>
        <a href="<?= baseUrl('/') ?>" class="nav-logo">⚡ <?= e(getSetting('site_name', APP_NAME)) ?></a>
    <?php endif; ?>

    <ul class="nav-links">
        <li><a href="<?= baseUrl('/') ?>#hero"><?= t('nav_home') ?></a></li>
        <li><a href="<?= baseUrl('/') ?>#services"><?= t('nav_services') ?></a></li>
        <li><a href="<?= baseUrl('/') ?>#about"><?= t('nav_about') ?></a></li>
        <li><a href="<?= baseUrl('portfolio') ?>"><?= t('nav_portfolio') ?></a></li>
        <?php if(getSetting('show_products_section', '1') === '1'): ?>
        <li><a href="<?= baseUrl('/') ?>#products"><?= getCurrentLocale() === 'en' ? 'Products' : 'منتجات' ?></a></li>
        <?php endif; ?>
        <?php if(getSetting('show_clients_section', '1') === '1'): ?>
        <li><a href="<?= baseUrl('/') ?>#clients"><?= getCurrentLocale() === 'en' ? 'Clients' : 'عملاء' ?></a></li>
        <?php endif; ?>
        <li><a href="<?= baseUrl('/') ?>#booking" class="nav-cta-btn"><?= t('nav_booking') ?></a></li>
    </ul>

    <a href="<?= baseUrl('?lang=' . (getCurrentLocale() === 'en' ? 'ar' : 'en')) ?>" class="nav-lang-btn">
        <?= t('nav_lang') ?>
    </a>

    <div class="nav-hamburger" id="navHamburger">
        <span></span><span></span><span></span>
    </div>
</nav>
