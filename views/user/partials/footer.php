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
                <?php $shareLinks = getSocialShareLinks(); ?>
                <a href="<?= e($shareLinks['facebook']) ?>" target="_blank" aria-label="Share on Facebook"><svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg></a>
                <a href="<?= e($shareLinks['twitter']) ?>" target="_blank" aria-label="Share on Twitter"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"></path></svg></a>
                <a href="<?= e($shareLinks['linkedin']) ?>" target="_blank" aria-label="Share on LinkedIn"><svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect x="2" y="9" width="4" height="12"></rect><circle cx="4" cy="4" r="2"></circle></svg></a>
                <a href="<?= e($shareLinks['whatsapp']) ?>" target="_blank" aria-label="Share on WhatsApp"><svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12.031 2.01c-5.518 0-9.998 4.48-9.998 9.998 0 1.763.46 3.486 1.332 5.006L2 22l5.12-1.341c1.472.825 3.149 1.26 4.908 1.26h.005c5.517 0 9.995-4.478 9.995-9.995 0-5.517-4.478-9.996-9.997-9.996zm5.498 14.414c-.22.62-1.28 1.189-1.789 1.246-.464.053-1.056.126-3.32-.813-2.887-1.196-4.735-4.14-4.877-4.329-.142-.189-1.163-1.547-1.163-2.95 0-1.403.734-2.095.992-2.383.258-.288.563-.36.75-.36s.374-.005.541.002c.181.01.425-.07.662.502.247.596.598 1.458.649 1.562.052.104.086.225.015.367-.07.142-.104.231-.208.354-.104.122-.218.261-.31.365-.104.116-.214.244-.092.455.122.21 5.4 5.4 5.611 5.722z"/></svg></a>
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
