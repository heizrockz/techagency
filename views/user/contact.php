<!-- Hero Section -->
<section class="hero-section" style="padding-top: 15vh; min-height: 40vh; padding-bottom: 50px;">
    <div class="container text-center text-reveal">
        <h1 class="hero-title"><span class="gradient-text"><?= t('contact_title', 'Contact Us') ?></span></h1>
        <p class="hero-subtitle" style="max-width: 600px; margin: 0 auto;"><?= t('contact_subtitle', 'We would love to hear from you. Get in touch with our team.') ?></p>
    </div>
</section>

<!-- Contact Form Section -->
<section class="section">
    <div class="container" style="max-width: 900px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 40px; align-items: start;">
            
            <!-- Contact Info -->
            <div class="contact-info-card glass-card fade-up">
                <h3 style="font-size: 1.5rem; margin-bottom: 20px; color: var(--neon-cyan);"><?= t('contact_info', 'Contact Information') ?></h3>
                
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    <?php if($phone = getSetting('contact_phone', '')): ?>
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div style="width: 50px; height: 50px; background: rgba(59,130,246,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--neon-cobalt);">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        </div>
                        <div>
                            <div style="font-size: 0.9rem; color: var(--text-muted);"><?= t('contact_phone', 'Phone') ?></div>
                            <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', $phone)) ?>" style="color: #fff; text-decoration: none; font-weight: 500; font-size: 1.1rem; direction: ltr; display: inline-block;"><?= e($phone) ?></a>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if($email = getSetting('contact_email', '')): ?>
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div style="width: 50px; height: 50px; background: rgba(236,72,153,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--neon-pink);">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        </div>
                        <div>
                            <div style="font-size: 0.9rem; color: var(--text-muted);"><?= t('contact_email', 'Email') ?></div>
                            <a href="mailto:<?= e($email) ?>" style="color: #fff; text-decoration: none; font-weight: 500; font-size: 1.1rem;"><?= e($email) ?></a>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div style="width: 50px; height: 50px; background: rgba(16,185,129,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--neon-emerald);">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        </div>
                        <div>
                            <div style="font-size: 0.9rem; color: var(--text-muted);"><?= t('contact_location', 'Location') ?></div>
                            <div style="color: #fff; font-weight: 500; font-size: 1.1rem;"><?= e(getSetting('contact_location', 'Dubai, UAE')) ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="glass-card fade-up" style="animation-delay: 0.2s;">
                <h3 style="font-size: 1.5rem; margin-bottom: 20px; color: var(--neon-violet);"><?= t('contact_send_msg', 'Send a Message') ?></h3>
                
                <form action="#" method="POST" onsubmit="event.preventDefault(); alert('Message feature coming soon!');" style="display: flex; flex-direction: column; gap: 15px;">
                    <div>
                        <input type="text" name="name" class="form-input" placeholder="<?= t('contact_form_name', 'Your Name') ?>" required style="background: rgba(255,255,255,0.02); height: 50px;">
                    </div>
                    <div>
                        <input type="email" name="email" class="form-input" placeholder="<?= t('contact_form_email', 'Your Email') ?>" required style="background: rgba(255,255,255,0.02); height: 50px;">
                    </div>
                    <div>
                        <textarea name="message" class="form-input" placeholder="<?= t('contact_form_message', 'Your Message') ?>" rows="5" required style="background: rgba(255,255,255,0.02); resize: vertical;"></textarea>
                    </div>
                    <button type="submit" class="btn-primary" style="padding: 15px; font-size: 1.1rem; width: 100%; display: flex; justify-content: center; gap: 10px; align-items: center;">
                        <?= t('contact_btn_send', 'Send Message') ?>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                    </button>
                </form>
            </div>

        </div>
    </div>
</section>

<style>
.contact-info-card {
    background: linear-gradient(145deg, rgba(255,255,255,0.03) 0%, rgba(255,255,255,0.01) 100%);
    border-top: 2px solid rgba(59, 130, 246, 0.3);
}
</style>
