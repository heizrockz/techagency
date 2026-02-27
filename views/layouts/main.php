<!DOCTYPE html>
<html lang="<?= e($locale) ?>" dir="<?= $dir ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(mb_strimwidth($seo['title'] ?? APP_NAME, 0, 60, "...")) ?></title>
    <meta name="description" content="<?= e($seo['description'] ?? '') ?>">
    <meta name="keywords" content="<?= e($seo['keywords'] ?? '') ?>">
    
    <?php 
        $currentUrl = fullUrl($_SERVER['REQUEST_URI']);
        $canonicalUrl = !empty($seo['canonical_link']) ? (preg_match('~^https?://~', $seo['canonical_link']) ? $seo['canonical_link'] : fullUrl($seo['canonical_link'])) : $currentUrl;
        // Strip query params for a cleaner canonical if they aren't part of the core content
        $canonicalUrl = strtok($canonicalUrl, '?');
    ?>
    <link rel="canonical" href="<?= e($canonicalUrl) ?>">
    
    <!-- Google Analytics Placeholder -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-XXXXXXXXXX');
    </script>

    <!-- Structured Data (JSON-LD) -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Organization",
      "name": "<?= e(getSetting('site_name', APP_NAME)) ?>",
      "url": "<?= fullUrl() ?>",
      "logo": "<?= fullUrl(getSetting('site_logo')) ?>",
      "description": "<?= e($seo['description'] ?? '') ?>",
      "sameAs": [
        "https://facebook.com/micosage",
        "https://twitter.com/micosage",
        "https://linkedin.com/company/micosage"
      ]
    }
    </script>
    
    <!-- Open Graph / Social Sharing Meta -->
    <?php
        $ogTitle       = $seo['title'] ?? APP_NAME;
        $ogDesc        = $seo['description'] ?? getSetting('seo_og_desc', '');
        $ogUrl         = $seo['canonical_link'] ?? getSetting('seo_og_url', baseUrl());
        $ogType        = isset($blog) ? 'article' : 'website';
        // Determine best OG image: blog image > blog YT thumbnail > site default
        $ogImage = '';
        if (!empty($seo['og_image'])) {
            $ogImage = $seo['og_image'];
        } elseif (!empty($seo['og_yt_thumbnail'])) {
            $ogImage = $seo['og_yt_thumbnail'];
        } elseif ($siteOg = getSetting('seo_og_image')) {
            $ogImage = baseUrl($siteOg);
        }
    ?>
    <meta property="og:type"        content="<?= e($ogType) ?>">
    <meta property="og:title"       content="<?= e($ogTitle) ?>">
    <meta property="og:description" content="<?= e($ogDesc) ?>">
    <meta property="og:url"         content="<?= e(preg_match('~^https?://~', $ogUrl) ? $ogUrl : fullUrl($ogUrl)) ?>">
    <?php if ($ogImage): ?>
    <?php $fullOgImage = preg_match('~^https?://~', $ogImage) ? $ogImage : fullUrl($ogImage); ?>
    <meta property="og:image"       content="<?= e($fullOgImage) ?>">
    <meta property="og:image:width"  content="600">
    <meta property="og:image:height" content="360">
    <?php endif; ?>
    <?php if ($ogSiteName = getSetting('seo_og_sitename', APP_NAME)): ?>
    <meta property="og:site_name"   content="<?= e($ogSiteName) ?>">
    <?php endif; ?>
    <!-- Twitter Card -->
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="<?= e($ogTitle) ?>">
    <meta name="twitter:description" content="<?= e($ogDesc) ?>">
    <?php if ($ogImage): ?>
    <meta name="twitter:image"       content="<?= e($fullOgImage) ?>">
    <?php endif; ?>

    <?php if($favicon = getSetting('seo_favicon')): ?>
        <link rel="icon" type="image/x-icon" href="<?= fullUrl($favicon) ?>">
        <link rel="apple-touch-icon" href="<?= fullUrl($favicon) ?>">
    <?php endif; ?>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400&display=swap" rel="stylesheet" media="print" onload="this.media='all'">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com" defer></script>
    <script>
        window.addEventListener('load', function() {
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['Inter', 'IBM Plex Sans Arabic', 'system-ui', 'sans-serif'],
                            mono: ['JetBrains Mono', 'monospace'],
                        }
                    }
                }
            }
        });
    </script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= baseUrl('assets/css/style.css') ?>">
</head>
<body dir="<?= $dir ?>" data-baseurl="<?= baseUrl() ?>">

    <!-- Nebula Background -->
    <div class="nebula-bg"></div>
    <div class="nebula-orb nebula-orb-1"></div>
    <div class="nebula-orb nebula-orb-2"></div>
    <div class="nebula-orb nebula-orb-3"></div>

    <!-- Floating Island Navbar -->
    <?php require __DIR__ . '/../user/partials/navbar.php'; ?>

    <!-- Mobile Nav -->
    <?php require __DIR__ . '/../user/partials/mobile-nav.php'; ?>

    <!-- Dynamic Island Announcement Logic -->
    <?php
    $announcementActive = getSetting('announcement_active', '0') === '1';
    if ($announcementActive) {
        $msgEn = getSetting('announcement_message', '');
        $msgAr = getSetting('announcement_message_ar', '');
        $announcementMsg = getCurrentLocale() === 'ar' && !empty($msgAr) ? $msgAr : $msgEn;
        
        $announcementDur = (int)getSetting('announcement_duration', '5');
        $announcementEnd = getSetting('announcement_end_date', '');
        $showAnnouncement = !empty($announcementMsg);

        if ($showAnnouncement && !empty($announcementEnd)) {
            if (strtotime($announcementEnd) < time()) {
                $showAnnouncement = false;
            }
        }
        
        if ($showAnnouncement):
    ?>
    <style>
    /* Dynamic Island Navbar Styles */
    .navbar-island {
        transition: width 0.6s cubic-bezier(0.34, 1.56, 0.64, 1), 
                    height 0.6s cubic-bezier(0.34, 1.56, 0.64, 1), 
                    padding 0.6s cubic-bezier(0.34, 1.56, 0.64, 1),
                    background 0.6s ease,
                    border-radius 0.6s ease,
                    top 0.6s ease;
        overflow: hidden;
        border: 1px solid rgba(255,255,255,0.08); /* Default sub-border */
    }
    
    /* When island mode is active */
    .navbar-island.is-island {
        width: 100%;
        max-width: 600px;
        height: 60px; /* Taller island height for emphasis */
        padding: 0 25px;
        background: rgba(10, 10, 15, 0.95);
        border: 1px solid var(--neon-cyan);
        border-radius: 30px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.8), inset 0 0 0 1px rgba(255,255,255,0.05);
        top: 20px;
        justify-content: center;
    }
    
    /* Standard navbar children transitions */
    .nav-links, .nav-logo, .nav-lang-btn, .theme-toggle, .nav-hamburger {
        transition: opacity 0.4s ease, transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        opacity: 1;
        transform: scale(1);
    }
    
    /* Hide standard children when island is active */
    .navbar-island.is-island > *:not(.island-content) {
        opacity: 0;
        pointer-events: none;
        transform: scale(0.95);
        position: absolute; 
    }
    
    /* New dynamic island content */
    .island-content {
        display: none;
        align-items: center;
        gap: 12px;
        color: #fff;
        font-size: 1rem;
        font-weight: 500;
        white-space: nowrap;
        opacity: 0;
        transform: scale(0.9);
        transition: opacity 0.5s ease 0.3s, transform 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) 0.3s;
    }
    .navbar-island.is-island .island-content {
        display: flex;
        opacity: 1;
        transform: scale(1);
    }
    
    @media (max-width: 768px) {
        .navbar-island.is-island {
            width: 95%;
            height: 55px;
        }
        .island-content { font-size: 0.85rem; }
    }
    </style>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const nav = document.getElementById('mainNavbar');
        const msg = <?= json_encode($announcementMsg) ?>;
        const dur = <?= $announcementDur ?>;
        
        if (nav && msg) {
            // Create island content container
            const islandContent = document.createElement('div');
            islandContent.className = 'island-content';
            islandContent.innerHTML = `<span style="font-size:1.2rem;">📣</span> <span>${msg}</span>`;
            nav.appendChild(islandContent);

            // Trigger island effect slightly after load
            setTimeout(() => {
                // Record original width/height if needed, but CSS handles standard view
                nav.classList.add('is-island');
                
                // If it's not permanent, schedule the return
                if (dur > 0) {
                    setTimeout(() => {
                        nav.classList.remove('is-island');
                        // Clean up island content after animation
                        setTimeout(() => nav.removeChild(islandContent), 600);
                    }, dur * 1000);
                }
            }, 800); // Wait 800ms before triggering
        }
    });
    </script>
    <?php endif; } ?>

    <!-- Main Content -->
    <main>
        <?php
        $page = $viewFile ?? 'home';
        require __DIR__ . '/../user/' . $page . '.php';
        ?>
    </main>

    <!-- Footer -->
    <?php require __DIR__ . '/../user/partials/footer.php'; ?>

    <!-- Floating CTA Button -->
    <?php $phone = getSetting('contact_phone', ''); $wa = getSetting('whatsapp_number', ''); ?>
    <?php if((!empty($phone) || !empty($wa)) && $page !== 'contact'): ?>
    <div class="floating-cta-wrapper">
        <button class="floating-cta-btn" aria-label="Talk to our expert" id="floatingCtaBtn">
            <div class="fcb-pulse"></div>
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
            </svg>
            <span class="cta-text">Talk to our expert</span>
        </button>
        <div class="floating-cta-popup" id="floatingCtaPopup">
            <?php if(!empty($wa)): ?>
            <a href="https://wa.me/<?= e(preg_replace('/[^0-9]/', '', $wa)) ?>?text=Hello%20<?= urlencode(APP_NAME) ?>!" target="_blank" class="cta-popup-item wa">
               <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12.031 2.01c-5.518 0-9.998 4.48-9.998 9.998 0 1.763.46 3.486 1.332 5.006L2 22l5.12-1.341c1.472.825 3.149 1.26 4.908 1.26h.005c5.517 0 9.995-4.478 9.995-9.995 0-5.517-4.478-9.996-9.997-9.996zm5.498 14.414c-.22.62-1.28 1.189-1.789 1.246-.464.053-1.056.126-3.32-.813-2.887-1.196-4.735-4.14-4.877-4.329-.142-.189-1.163-1.547-1.163-2.95 0-1.403.734-2.095.992-2.383.258-.288.563-.36.75-.36s.374-.005.541.002c.181.01.425-.07.662.502.247.596.598 1.458.649 1.562.052.104.086.225.015.367-.07.142-.104.231-.208.354-.104.122-.218.261-.31.365-.104.116-.214.244-.092.455.122.21 5.4 5.4 5.611 5.722z"/></svg>
               WhatsApp Us
            </a>
            <?php endif; ?>
            <?php if(!empty($phone)): ?>
            <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', $phone)) ?>" class="cta-popup-item call">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                Call Us
            </a>
            <?php endif; ?>
            <?php if(getSetting('show_phone_in_cta', '0') === '1' && !empty($phone)): ?>
            <div style="padding:8px 16px; font-size:0.82rem; color:rgba(255,255,255,0.6); text-align:center; border-top:1px solid rgba(255,255,255,0.06); direction:ltr;">
                <?= e($phone) ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Chatbot Widget -->
    <?php $chatbotData = getChatbotData($locale); if($chatbotData['start_node_id']): ?>
    <div class="chatbot-widget" id="chatbotWidget">
        <button class="chatbot-toggle" id="chatbotToggle" aria-label="Open Chat">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
            </svg>
        </button>
        <div class="chatbot-panel" id="chatbotPanel">
            <div class="chatbot-header">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 32px; height: 32px; background: linear-gradient(135deg, var(--neon-cyan), var(--neon-violet)); border-radius: 50%; display: flex; align-items: center; justify-content: center;">🤖</div>
                    <div>
                        <strong style="display: block; font-size: 0.95rem;"><?= APP_NAME ?></strong>
                        <div style="font-size: 0.75rem; color: rgba(255,255,255,0.7); display: flex; align-items: center; gap: 5px;">
                            <span style="display:inline-block; width:8px; height:8px; background:var(--neon-emerald); border-radius:50%; box-shadow: 0 0 5px var(--neon-emerald);"></span> Online
                        </div>
                    </div>
                </div>
                <div style="display:flex; gap:6px; align-items:center;">
                    <button class="chatbot-action-btn" id="chatbotNewChat" aria-label="New Chat" title="New Chat">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>
                    </button>
                    <button class="chatbot-close" id="chatbotClose" aria-label="Close Chat">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
            </div>
            <div class="chatbot-messages" id="chatbotMessages">
                <!-- Messages will be injected here automatically -->
            </div>
            <div class="chatbot-options" id="chatbotOptions">
                <!-- Buttons will be injected here automatically -->
            </div>
            <div class="chatbot-input-area" id="chatbotInputArea" style="display:none;">
                <input type="text" id="chatbotInput" placeholder="Type your message..." autocomplete="off">
                <button id="chatbotSendBtn" aria-label="Send Message">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                </button>
            </div>
            <div class="chatbot-footer">
                <button id="chatbotEndChat" class="chatbot-end-btn">End Chat</button>
            </div>
        </div>
    </div>
    <script>
        window.chatbotData = <?= json_encode($chatbotData) ?>;
    </script>
    <?php endif; ?>

    <!-- Global Broken Image Fallback -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // A clean, standalone SVG placeholder representing a missing image in our brand style
        const fallbackSvgUrl = "data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='800' height='600' viewBox='0 0 800 600'%3E%3Crect width='800' height='600' fill='%231a1d24'/%3E%3Ctext x='50%25' y='50%25' fill='%23475569' font-family='sans-serif' font-size='32' text-anchor='middle' dy='.3em'%3EImage Unavailable%3C/text%3E%3C/svg%3E";
        
        document.querySelectorAll('img').forEach(img => {
            // Handle dynamically loaded / erroring images robustly
            img.addEventListener('error', function() {
                if(this.src !== fallbackSvgUrl && !this.dataset.fallbackApplied) {
                    this.dataset.fallbackApplied = 'true'; // Anti-loop
                    this.src = fallbackSvgUrl;
                    this.style.objectFit = 'cover';
                    this.alt = 'Image unavailable';
                }
            });
            // If the image is ALREADY broken by the time this script runs
            if(img.complete && img.naturalHeight === 0) {
                if(img.src !== fallbackSvgUrl && !img.dataset.fallbackApplied) {
                    img.dataset.fallbackApplied = 'true';
                    img.src = fallbackSvgUrl;
                    img.style.objectFit = 'cover';
                }
            }
        });
    });
    </script>

    <!-- Scripts -->
    <script src="<?= baseUrl('assets/js/app.js') ?>" defer></script>
</body>
</html>
