<!-- Footer -->
<footer class="footer">
    <div class="footer-grid section-container">
        <!-- Brand & Tagline -->
        <div class="footer-col branding">
            <?php 
                $footerLogo = getSetting('site_logo'); 
                $companyName = getLocaleSetting('company_name');
                if (empty($companyName)) $companyName = getSetting('site_name', APP_NAME);
            ?>
            <?php if(!empty($footerLogo)): ?>
            <div class="footer-logo">
                <img src="<?= baseUrl($footerLogo) ?>" alt="<?= e($companyName) ?>" style="max-height:30px;">
                <span class="footer-company-name"><?= e($companyName) ?></span>
            </div>
            <?php else: ?>
            <h3 class="footer-logo">
                <span class="footer-company-name"><?= e($companyName) ?></span>
            </h3>
            <?php endif; ?>
            <p class="footer-tagline">⚡ <?= getContent('footer_tagline', getCurrentLocale()) !== 'footer_tagline' ? e(getContent('footer_tagline', getCurrentLocale())) : e(t('footer_tagline')) ?></p>
            
            <div class="footer-socials">
                <?php if($fb = getSetting('social_facebook')): ?>
                    <a href="<?= e($fb) ?>" target="_blank" aria-label="Facebook"><svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg></a>
                <?php endif; ?>
                <?php if($tw = getSetting('social_twitter')): ?>
                    <a href="<?= e($tw) ?>" target="_blank" aria-label="Twitter"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"></path></svg></a>
                <?php endif; ?>
                <?php if($ig = getSetting('social_instagram')): ?>
                    <a href="<?= e($ig) ?>" target="_blank" aria-label="Instagram"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg></a>
                <?php endif; ?>
                <?php if($li = getSetting('social_linkedin')): ?>
                    <a href="<?= e($li) ?>" target="_blank" aria-label="LinkedIn"><svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect x="2" y="9" width="4" height="12"></rect><circle cx="4" cy="4" r="2"></circle></svg></a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Contact Info -->
        <?php if (getSetting('show_contact_section', '1') === '1'): ?>
        <div class="footer-col">
            <h4><?= t('contact_us') ?></h4>
            <ul class="footer-links">
                <?php if($email = getSetting('contact_email')): ?>
                    <li><a href="mailto:<?= e($email) ?>"><?= e($email) ?></a></li>
                <?php endif; ?>
                <?php if($phone = getSetting('contact_phone')): ?>
                    <li><a href="tel:<?= e(preg_replace('/[^0-9+]/', '', $phone)) ?>"><?= e($phone) ?></a></li>
                <?php endif; ?>
                <?php if($address = getSetting('contact_address')): ?>
                    <li><span class="text-muted"><?= e($address) ?></span></li>
                <?php endif; ?>
            </ul>
        </div>
        <?php endif; ?>

        <!-- Quick Links -->
        <div class="footer-col">
            <h4>Quick Links</h4>
            <ul class="footer-links">
                <li><a href="<?= baseUrl() ?>#services"><?= t('services') ?></a></li>
                <li><a href="<?= baseUrl() ?>#about"><?= t('about_us') ?></a></li>
                <li><a href="<?= baseUrl() ?>#products"><?= t('products') ?></a></li>
                <li><a href="<?= baseUrl('portfolio') ?>"><?= t('portfolio') ?></a></li>
                <li><a href="<?= baseUrl() ?>#booking"><?= t('book_meeting') ?></a></li>
            </ul>
        </div>
        
        <!-- Legal -->
        <div class="footer-col">
            <h4>Legal</h4>
            <ul class="footer-links">
                <li><a href="#">Privacy Policy</a></li>
                <li><a href="#">Terms of Service</a></li>
                <li><a href="#">Cookie Policy</a></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <p class="footer-copy"><?= e(getContent('footer_text')) ?></p>
    </div>
</footer>
