<!-- Floating Island Navbar -->
<nav class="navbar-island" id="mainNavbar">
    <?php 
        $navLogo = getSetting('site_logo'); 
        $companyName = getLocaleSetting('company_name');
        if (empty($companyName)) $companyName = getSetting('site_name', APP_NAME);
    ?>
    <?php if(!empty($navLogo)): ?>
        <a href="<?= baseUrl('/') ?>" class="nav-logo">
            <img src="<?= baseUrl($navLogo) ?>" alt="<?= e($companyName) ?>" title="<?= e($companyName) ?>" style="max-height:24px;">
            <span class="company-name-text"><?= e($companyName) ?></span>
        </a>
    <?php else: ?>
        <a href="<?= baseUrl('/') ?>" class="nav-logo">⚡ <?= e($companyName) ?></a>
    <?php endif; ?>

    <ul class="nav-links">
        <li><a href="<?= baseUrl('/') ?>#hero"><?= t('nav_home') ?></a></li>
        <li><a href="<?= baseUrl('/') ?>#services"><?= t('nav_services') ?></a></li>
        <li><a href="<?= baseUrl('/') ?>#about"><?= t('nav_about') ?></a></li>
        <li><a href="<?= baseUrl('portfolio') ?>"><?= t('nav_portfolio') ?></a></li>
        <?php if(getSetting('show_software_store', '1') === '1'): ?>
        <li><a href="<?= baseUrl('software') ?>"><?= getCurrentLocale() === 'en' ? 'Software' : 'برامج' ?></a></li>
        <?php endif; ?>
        <?php if(getSetting('show_products_section', '1') === '1'): ?>
        <li><a href="<?= baseUrl('/') ?>#products"><?= getCurrentLocale() === 'en' ? 'Products' : 'منتجات' ?></a></li>
        <?php endif; ?>
        <?php if(getSetting('show_clients_section', '1') === '1'): ?>
        <li><a href="<?= baseUrl('/') ?>#clients"><?= getCurrentLocale() === 'en' ? 'Clients' : 'عملاء' ?></a></li>
        <?php endif; ?>
        <li><a href="<?= baseUrl('/') ?>#booking" class="nav-cta-btn"><?= t('nav_booking') ?></a></li>
    </ul>

    <a href="<?= getCurrentUrlWithLang(getCurrentLocale() === 'en' ? 'ar' : 'en') ?>" class="nav-lang-btn">
        <?= t('nav_lang') ?>
    </a>

    <button class="theme-toggle" id="themeToggle" aria-label="Toggle theme" title="Toggle Light/Dark Mode">
        <svg class="theme-icon moon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
        <svg class="theme-icon sun" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
    </button>

    <div class="nav-hamburger" id="navHamburger">
        <span></span><span></span><span></span>
    </div>
</nav>
