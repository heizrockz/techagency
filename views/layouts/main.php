<!DOCTYPE html>
<html lang="<?= e($locale) ?>" dir="<?= $dir ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($seo['title'] ?? APP_NAME) ?></title>
    <meta name="description" content="<?= e($seo['description'] ?? '') ?>">
    <meta name="keywords" content="<?= e($seo['keywords'] ?? '') ?>">
    
    <?php if(!empty($seo['canonical_link'])): ?>
        <link rel="canonical" href="<?= e($seo['canonical_link']) ?>">
    <?php elseif($canonical = getSetting('seo_canonical')): ?>
        <link rel="canonical" href="<?= e($canonical) ?>">
    <?php endif; ?>
    
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
    <meta property="og:url"         content="<?= e($ogUrl) ?>">
    <?php if ($ogImage): ?>
    <meta property="og:image"       content="<?= e($ogImage) ?>">
    <meta property="og:image:width"  content="1200">
    <meta property="og:image:height" content="630">
    <?php endif; ?>
    <?php if ($ogSiteName = getSetting('seo_og_sitename', APP_NAME)): ?>
    <meta property="og:site_name"   content="<?= e($ogSiteName) ?>">
    <?php endif; ?>
    <!-- Twitter Card -->
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="<?= e($ogTitle) ?>">
    <meta name="twitter:description" content="<?= e($ogDesc) ?>">
    <?php if ($ogImage): ?>
    <meta name="twitter:image"       content="<?= e($ogImage) ?>">
    <?php endif; ?>

    <?php if($favicon = getSetting('seo_favicon')): ?>
        <link rel="icon" type="image/x-icon" href="<?= baseUrl($favicon) ?>">
    <?php endif; ?>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
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

    <!-- Scripts -->
    <script src="<?= baseUrl('assets/js/app.js') ?>"></script>
</body>
</html>
