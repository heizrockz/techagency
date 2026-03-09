<?php
if (!defined('APP_NAME')) die('Direct access prevented');
?>
<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <title><?= e($product['name']) ?> — Software Ecosystem</title>
    <meta name="description" content="<?= e($product['meta_description'] ?: $product['description']) ?>">
    <meta name="keywords" content="<?= e($product['meta_keywords']) ?>">
    
    <!-- Open Graph / Social Media -->
    <?php
        $siteDomain = 'https://' . ($_SERVER['HTTP_HOST'] ?? 'micosage.com');
        $ogTitle = e($product['name']) . ' — ' . APP_NAME;
        $ogDesc = trim(e($product['meta_description'] ?: $product['description']));
        if (empty($ogDesc)) $ogDesc = 'Download ' . e($product['name']) . ' from ' . APP_NAME;
        
        $rawImage = !empty($product['icon_url']) ? $product['icon_url'] : (!empty($product['header_image']) ? $product['header_image'] : '');
        $ogImage = '';
        if (!empty($rawImage)) {
            $ogImage = strpos($rawImage, 'http') === 0 ? $rawImage : $siteDomain . baseUrl($rawImage);
        } else {
            $siteLogo = getSetting('site_logo');
            if ($siteLogo) $ogImage = $siteDomain . baseUrl($siteLogo);
        }
        
        $ogUrl = $siteDomain . baseUrl('software/' . e($product['slug']));
    ?>
    <meta property="og:type" content="product">
    <meta property="og:title" content="<?= $ogTitle ?>">
    <meta property="og:description" content="<?= $ogDesc ?>">
    <meta property="og:url" content="<?= $ogUrl ?>">
    <?php if (!empty($ogImage)): ?>
    <meta property="og:image" content="<?= $ogImage ?>">
    <meta property="og:image:width" content="512">
    <meta property="og:image:height" content="512">
    <?php endif; ?>
    <meta property="og:site_name" content="<?= APP_NAME ?>">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= $ogTitle ?>">
    <meta name="twitter:description" content="<?= $ogDesc ?>">
    <?php if (!empty($ogImage)): ?>
    <meta name="twitter:image" content="<?= $ogImage ?>">
    <?php endif; ?>
    <?php require __DIR__ . '/admin/partials/_head_assets.php'; ?>
    <!-- Required for Navbar -->
    <link rel="stylesheet" href="<?= baseUrl('assets/css/style.css') ?>">

    <style>
        :root {
            --glass-bg: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.08);
        }
        body { background: #0b0e14; color: #fff; font-family: 'Inter', sans-serif; overflow-x: hidden; }
        
        /* Support navbar island */
        .navbar-island { z-index: 1000 !important; }
        
        .product-nav {
            height: 70px;
            background: rgba(11, 14, 20, 0.8);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--glass-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 40px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .details-container {
            max-width: 1200px;
            margin: 180px auto 60px; /* Increased top margin for navbar */
            padding: 0 20px;
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 60px;
        }

        .gallery-main {
            aspect-ratio: 16/9;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 32px;
            overflow: hidden;
            margin-bottom: 24px;
        }
        .gallery-main img { width: 100%; height: 100%; object-fit: cover; }

        .thumbnail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 16px;
        }
        .thumb-item {
            aspect-ratio: 16/9;
            border-radius: 12px;
            border: 1px solid var(--glass-border);
            overflow: hidden;
            cursor: pointer;
            transition: all 0.3s;
        }
        .thumb-item:hover { border-color: #8b5cf6; transform: scale(1.05); }

        .review-card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 24px;
            margin-bottom: 20px;
        }
        .admin-reply {
            margin-top: 20px;
            padding: 20px;
            background: rgba(139, 92, 246, 0.05);
            border-left: 3px solid #8b5cf6;
            border-radius: 0 16px 16px 0;
        }

        .rating-input {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
            gap: 8px;
        }
        .rating-input input { display: none; }
        .rating-input label { font-size: 24px; color: rgba(255,255,255,0.1); cursor: pointer; transition: color 0.2s; }
        .rating-input label:hover,
        .rating-input label:hover ~ label,
        .rating-input input:checked ~ label { color: #f59e0b; }

        @media (max-width: 1024px) {
            .details-container { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <!-- Main Navigation Bar -->
    <?php require __DIR__ . '/user/partials/navbar.php'; ?>

    <nav class="product-nav mt-8"> <!-- Added margin top to separate from main nav slightly if needed, or could just rest below -->
        <a href="<?= baseUrl('software') ?>" class="flex items-center gap-2 text-white/60 hover:text-white transition-colors">
            <i class="ph ph-arrow-left"></i>
            <span class="text-sm font-bold uppercase tracking-widest"><?= getCurrentLocale() === 'ar' ? 'الرجوع للمتجر' : 'Store' ?></span>
        </a>
        <div class="flex items-center gap-4">
            <span class="text-[10px] font-black uppercase tracking-widest text-white/20"><?= getCurrentLocale() === 'ar' ? 'الإصدار' : 'Version' ?> <?= e($product['version']) ?></span>
            <div class="h-4 w-px bg-white/10"></div>
            <span class="text-[10px] font-black uppercase tracking-widest text-violet-400"><?= e($product['category_name']) ?></span>
        </div>
    </nav>

    <div class="details-container">
        <div class="main-content">
            <!-- Gallery -->
            <div class="gallery-section mb-12">
                <div class="gallery-main">
                    <?php 
                    $mainImage = $product['header_image'];
                    if (empty($mainImage) && !empty($gallery)) {
                        $mainImage = $gallery[0]['image_path'];
                    }
                    if (empty($mainImage)) {
                        $mainImage = 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&w=1200&q=80';
                    }
                    ?>
                    <img src="<?= strpos($mainImage, 'http') === 0 ? e($mainImage) : baseUrl($mainImage) ?>" id="galleryMain">
                </div>
                <div class="thumbnail-grid">
                    <?php foreach ($gallery as $img): ?>
                        <div class="thumb-item" onclick="document.getElementById('galleryMain').src = '<?= baseUrl($img['image_path']) ?>'">
                            <img src="<?= baseUrl($img['image_path']) ?>" class="w-full h-full object-cover">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Content -->
            <div class="space-y-6 mb-12">
                <h1 class="text-5xl font-black tracking-tighter"><?= e($product['name']) ?></h1>
                
                <!-- Short Description (Lead) -->
                <?php if (!empty($product['short_description'])): ?>
                    <p class="text-2xl text-violet-300/90 font-medium leading-relaxed"><?= e($product['short_description']) ?></p>
                <?php endif; ?>
                
                <!-- Long Description -->
                <?php if (!empty($product['long_description'])): ?>
                    <div class="text-lg text-white/70 leading-loose prose prose-invert max-w-none text-justify whitespace-pre-line">
                        <?= e($product['long_description']) ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Features List -->
            <?php if (!empty(trim($product['features']))): ?>
                <div class="mb-20">
                    <h2 class="text-2xl font-bold mb-6"><?= getCurrentLocale() === 'ar' ? 'أبرز المميزات' : 'Key Features & Specialties' ?></h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php 
                        $featureLines = array_filter(array_map('trim', explode("\n", $product['features'])));
                        foreach($featureLines as $feat): 
                            if(empty($feat)) continue;
                        ?>
                            <div class="flex items-start gap-4 p-4 rounded-2xl bg-white/5 border border-white/10">
                                <i class="ph-fill ph-check-circle text-emerald-400 text-xl mt-1 shrink-0"></i>
                                <span class="text-white/80 leading-relaxed font-medium"><?= e($feat) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Reviews Section -->
            <div id="reviews" class="space-y-8">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold">Ratings and reviews</h2>
                    <button onclick="document.getElementById('reviewForm').classList.toggle('hidden')" class="text-sm font-bold text-violet-400 hover:text-white transition-colors">Write a review</button>
                </div>

                <!-- Review Form -->
                <div id="reviewForm" class="hidden admin-card p-8 mb-12 border-violet-500/20">
                    <form action="<?= baseUrl('software/review') ?>" method="POST" class="space-y-6">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <div class="grid grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-widest text-white/40">Your Name</label>
                                <input type="text" name="name" class="form-input" placeholder="Display name" required>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-widest text-white/40">Rating</label>
                                <div class="rating-input">
                                    <input type="radio" id="star5" name="rating" value="5" /><label for="star5" class="ph-fill ph-star"></label>
                                    <input type="radio" id="star4" name="rating" value="4" /><label for="star4" class="ph-fill ph-star"></label>
                                    <input type="radio" id="star3" name="rating" value="3" /><label for="star3" class="ph-fill ph-star"></label>
                                    <input type="radio" id="star2" name="rating" value="2" /><label for="star2" class="ph-fill ph-star"></label>
                                    <input type="radio" id="star1" name="rating" value="1" /><label for="star1" class="ph-fill ph-star"></label>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-white/40">Your Feedback</label>
                            <textarea name="comment" rows="4" class="form-input" placeholder="What do you think of this software?" required></textarea>
                        </div>
                        <button type="submit" class="w-full py-4 bg-violet-600 text-white font-black uppercase tracking-widest rounded-2xl hover:bg-violet-500 transition-all">Submit Review</button>
                    </form>
                </div>

                <!-- Display Reviews -->
                <?php foreach($reviews as $rev): ?>
                    <div class="review-card">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center font-bold text-white/40"><?= substr($rev['name'], 0, 1) ?></div>
                                <div>
                                    <h4 class="font-bold text-white"><?= e($rev['name']) ?></h4>
                                    <div class="flex items-center gap-1 text-amber-500 text-xs">
                                        <?php for($i=1; $i<=5; $i++): ?>
                                            <i class="ph-fill ph-star <?= $i <= $rev['rating'] ? '' : 'opacity-20' ?>"></i>
                                        <?php endfor; ?>
                                        <span class="ml-2 text-white/20 font-medium"><?= date('M Y', strtotime($rev['created_at'])) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="text-white/60 leading-relaxed italic">"<?= e($rev['comment']) ?>"</p>
                        
                        <?php if(!empty($rev['admin_reply'])): ?>
                            <div class="admin-reply">
                                <p class="text-[10px] font-black uppercase tracking-widest text-violet-400 mb-2">Developer Response</p>
                                <p class="text-sm text-white/80"><?= e($rev['admin_reply']) ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; if(empty($reviews)): ?>
                    <div class="text-center py-20 bg-white/5 rounded-3xl border border-dashed border-white/10 text-white/20">
                        <p>No reviews yet. Be the first to share your experience!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="sidebar">
            <div class="sticky top-[110px] space-y-6">
                <!-- Purchase Card (Existing) -->
                <div class="admin-card p-8 bg-violet-600/5 border-violet-500/20 rounded-[32px] space-y-6">
                     <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-white/40 mb-2">Price</p>
                        <?php if($product['show_price']): ?>
                            <h2 class="text-4xl font-black"><?= $product['pricing_model'] === 'free' ? 'Free' : '$'.number_format($product['price'], 2) ?></h2>
                        <?php else: ?>
                            <h2 class="text-2xl font-black uppercase tracking-widest">Contact Us</h2>
                        <?php endif; ?>
                    </div>

                    <div class="space-y-4">
                        <?php if($product['download_url']): ?>
                            <a href="<?= baseUrl('api/download-track.php?id='.$product['id']) ?>" class="block w-full py-4 bg-white text-black text-center font-black uppercase tracking-widest rounded-2xl hover:bg-violet-400 hover:text-white transition-all">Get It Now</a>
                        <?php endif; ?>
                        <?php if($product['show_buy_button'] && $product['buy_url']): ?>
                            <a href="<?= e($product['buy_url']) ?>" target="_blank" class="block w-full py-4 bg-violet-600 text-white text-center font-black uppercase tracking-widest rounded-2xl hover:bg-violet-700 transition-all">Buy License</a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Features/Spec List -->
                <div class="admin-card p-8 rounded-[32px] space-y-4">
                    <h3 class="text-sm font-black uppercase tracking-widest text-white/40"><?= getCurrentLocale() === 'ar' ? 'المتطلبات الأساسية' : 'Requirements' ?></h3>
                    <div class="space-y-3">
                        <?php 
                        $reqs = array_filter(array_map('trim', explode(',', $product['os_requirements'] ?? '')));
                        if (empty($reqs)) {
                            $reqs = ['Windows 10+', '64-bit Architecture'];
                        }
                        foreach($reqs as $req): 
                        ?>
                            <div class="flex items-center gap-3 text-sm font-medium text-white/80">
                                <i class="ph ph-monitor text-violet-400"></i> <?= e($req) ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating CTA Button -->
    <?php $phone = getSetting('contact_phone', ''); $wa = getSetting('whatsapp_number', ''); ?>
    <?php if((!empty($phone) || !empty($wa))): ?>
    <div class="floating-cta-wrapper">
        <button class="floating-cta-btn" aria-label="Talk to our expert" id="floatingCtaBtn">
            <div class="fcb-pulse"></div>
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
            </svg>
            <span class="cta-text"><?= getCurrentLocale() === 'ar' ? 'تحدث إلى الخبراء' : 'Talk to our expert' ?></span>
        </button>
        <div class="floating-cta-popup" id="floatingCtaPopup">
            <?php if(!empty($wa)): ?>
            <a href="https://wa.me/<?= e(preg_replace('/[^0-9]/', '', $wa)) ?>?text=Hello%20<?= urlencode(APP_NAME) ?>!" target="_blank" rel="noopener noreferrer" class="cta-popup-item wa">
               <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12.031 2.01c-5.518 0-9.998 4.48-9.998 9.998 0 1.763.46 3.486 1.332 5.006L2 22l5.12-1.341c1.472.825 3.149 1.26 4.908 1.26h.005c5.517 0 9.995-4.478 9.995-9.995 0-5.517-4.478-9.996-9.997-9.996zm5.498 14.414c-.22.62-1.28 1.189-1.789 1.246-.464.053-1.056.126-3.32-.813-2.887-1.196-4.735-4.14-4.877-4.329-.142-.189-1.163-1.547-1.163-2.95 0-1.403.734-2.095.992-2.383.258-.288.563-.36.75-.36s.374-.005.541.002c.181.01.425-.07.662.502.247.596.598 1.458.649 1.562.052.104.086.225.015.367-.07.142-.104.231-.208.354-.104.122-.218.261-.31.365-.104.116-.214.244-.092.455.122.21 5.4 5.4 5.611 5.722z"/></svg>
               <?= getCurrentLocale() === 'ar' ? 'واتساب' : 'WhatsApp Us' ?>
            </a>
            <?php endif; ?>
            <?php if(!empty($phone)): ?>
            <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', $phone)) ?>" class="cta-popup-item call">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                <?= getCurrentLocale() === 'ar' ? 'إتصل بنا' : 'Call Us' ?>
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
    <?php $chatbotData = getChatbotData(getCurrentLocale()); if($chatbotData['start_node_id']): ?>
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

    <script src="<?= baseUrl('assets/js/app.js') ?>" defer></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</body>
</html>
