<?php
if (!defined('APP_NAME')) die('Direct access prevented');
?>
<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <title>Software Store — <?= APP_NAME ?></title>
    
    <!-- Open Graph / Social Media -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Software Store — <?= APP_NAME ?>">
    <meta property="og:description" content="<?= getCurrentLocale() === 'ar' ? 'تطبيقات عالية الأداء وأدوات احترافية' : 'High performance apps & professional tools' ?>">
    <meta property="og:url" content="<?= baseUrl('software') ?>">
    <meta property="og:site_name" content="<?= APP_NAME ?>">
    <?php $siteLogo = getSetting('site_logo'); if (!empty($siteLogo)): ?>
    <meta property="og:image" content="<?= baseUrl($siteLogo) ?>">
    <?php endif; ?>
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="Software Store — <?= APP_NAME ?>">
    <meta name="twitter:description" content="<?= getCurrentLocale() === 'ar' ? 'تطبيقات عالية الأداء وأدوات احترافية' : 'High performance apps & professional tools' ?>">

    <?php require __DIR__ . '/admin/partials/_head_assets.php'; ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <!-- Required for Navbar -->
    <link rel="stylesheet" href="<?= baseUrl('assets/css/style.css') ?>">
    
    <style>
        :root {
            --glass-bg: rgba(255, 255, 255, 0.02);
            --glass-border: rgba(255, 255, 255, 0.06);
            --accent: #8b5cf6;
            --font-main: 'Inter', sans-serif;
        }
        body { background: #0b0e14; color: #fff; font-family: var(--font-main); overflow-x: hidden; -webkit-font-smoothing: antialiased; }

        /* Support navbar island */
        .navbar-island { z-index: 1000 !important; }
        
        .store-hero {
            padding: 180px 0 100px; /* Increased top padding to accommodate fixed navbar */
            background: 
                radial-gradient(circle at 50% -20%, rgba(139, 92, 246, 0.1) 0%, transparent 60%),
                radial-gradient(circle at 100% 100%, rgba(236, 72, 153, 0.02) 0%, transparent 40%);
        }

        .search-bar-container {
            max-width: 600px;
            margin: 0 auto 100px;
            position: relative;
        }
        .search-bar {
            width: 100%;
            background: rgba(255,255,255,0.02);
            border: 1px solid var(--glass-border);
            padding: 20px 24px 20px 64px;
            border-radius: 20px;
            color: white;
            outline: none;
            transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
            font-size: 16px;
            backdrop-filter: blur(20px);
        }
        .search-bar:focus { 
            background: rgba(255,255,255,0.04); 
            border-color: rgba(255,255,255,0.1);
            box-shadow: 0 20px 40px -10px rgba(0,0,0,0.4);
            transform: translateY(-2px);
        }
        .search-icon { position: absolute; left: 24px; top: 50%; transform: translateY(-50%); color: rgba(255,255,255,0.2); font-size: 20px; }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 40px;
            padding: 0 12px;
        }
        .section-title { 
            font-size: 20px; 
            font-weight: 800; 
            letter-spacing: -0.5px;
            color: #fff;
        }

        /* Store App Tile Grid */
        .app-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 100px;
            padding: 0 12px;
        }

        .app-card {
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            background: #202024;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s ease, background 0.3s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        .app-card:hover { 
            transform: translateY(-4px); 
            background: #2d2d33;
        }

        .app-visual {
            width: 100%;
            aspect-ratio: 16 / 13;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        /* Dynamic Gradients for Backgrounds */
        .bg-gradient-1 { background: linear-gradient(135deg, #2b1f41 0%, #aa2f6e 100%); }
        .bg-gradient-2 { background: linear-gradient(135deg, #184145 0%, #2b4556 100%); }
        .bg-gradient-3 { background: linear-gradient(135deg, #266cf1 0%, #153c9f 100%); }
        .bg-gradient-4 { background: linear-gradient(135deg, #d31e13 0%, #901610 100%); }
        .bg-gradient-5 { background: linear-gradient(135deg, #1c72f7 0%, #0d3eb8 100%); }
        .bg-auto { background: linear-gradient(135deg, #1e2638 0%, #2f3a52 100%); }

        .app-icon-container {
            width: 100px;
            height: 100px;
            background: #ffffff;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 16px rgba(0,0,0,0.15);
            transition: transform 0.4s ease;
        }

        .app-icon {
            width: 65%;
            height: 65%;
            object-fit: contain;
        }
        .app-card:hover .app-icon-container { transform: scale(1.05); }
        .app-icon-fallback { font-size: 50px; color: rgba(0,0,0,0.4); }

        .app-info { 
            padding: 16px; 
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            flex-grow: 1;
        }
        .app-name { 
            font-size: 15px; 
            font-weight: 500; 
            margin-bottom: 16px; 
            color: #ffffff;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.3;
        }
        
        .app-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12px;
            color: #a0a0a0;
            margin-top: auto;
        }
        .app-rating { 
            display: flex; 
            align-items: center; 
            gap: 4px; 
            font-weight: 500;
        }
        .app-rating i { color: #a0a0a0; font-size: 11px; }
        .app-status {
            background: #141414;
            padding: 4px 12px;
            border-radius: 4px;
            color: #ffffff;
            font-size: 12px;
            font-weight: 600;
        }

        .featured-hero {
            width: 100%;
            height: 480px;
            border-radius: 48px;
            margin-bottom: 100px;
            position: relative;
            overflow: hidden;
            border: 1px solid var(--glass-border);
            padding: 0 12px;
        }
        .hero-inner {
            width: 100%;
            height: 100%;
            border-radius: 40px;
            overflow: hidden;
            position: relative;
        }
        .hero-bg { width: 100%; height: 100%; object-fit: cover; }
        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to right, rgba(11, 14, 20, 0.9) 20%, transparent 80%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 80px;
        }

        @media (max-width: 768px) {
            .app-grid { grid-template-columns: repeat(2, 1fr); gap: 24px; }
            .hero-overlay { padding: 40px; }
            .store-hero { padding-top: 100px; }
            .section-title { font-size: 18px; }
        }
    </style>
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">

    <!-- Main Navigation Bar -->
    <?php require __DIR__ . '/user/partials/navbar.php'; ?>

    <section class="store-hero pt-[180px]">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <!-- Translated main title and subtitle -->
                <h1 class="text-6xl font-black mb-4 tracking-tighter"><?= getCurrentLocale() === 'ar' ? 'متجر البرمجيات' : 'MicoStore' ?></h1>
                <p class="text-white/30 max-w-xl mx-auto font-medium text-lg italic leading-relaxed">
                    <?= getCurrentLocale() === 'ar' ? 'تطبيقات عالية الأداء لاحتياجاتك.' : 'High performance apps curated for your forensic & data needs.' ?>
                </p>
            </div>
            
            <div class="search-bar-container">
                <i class="ph ph-magnifying-glass search-icon"></i>
                <input type="text" class="search-bar" placeholder="<?= getCurrentLocale() === 'ar' ? 'البحث عن التطبيقات، والأدوات، والمنصات' : 'Search apps, tools, and platforms' ?>">
            </div>

            <!-- Dynamic Sections -->
            <?php foreach($sections as $secId => $sec): if(!empty($sec['products'])): ?>
                <div class="section-header">
                    <h2 class="section-title"><?= e($sec['title']) ?></h2>
                    <a href="#" class="text-[10px] font-black uppercase tracking-[0.2em] text-white/20 hover:text-white transition-colors">Browse Category</a>
                </div>
                
                <div class="app-grid">
                    <?php foreach($sec['products'] as $pIdx => $p): 
                        $iconUrl = $p['icon_url'];
                        if (!empty($iconUrl) && strpos($iconUrl, 'http') !== 0) $iconUrl = baseUrl($iconUrl);
                        
                        // Assign a random-ish gradient class for variety
                        $gradClass = 'bg-gradient-' . (($pIdx % 5) + 1);
                    ?>
                        <a href="<?= baseUrl('software/' . $p['slug']) ?>" class="app-card">
                            <div class="app-visual <?= $gradClass ?>">
                                <div class="app-icon-container">
                                    <?php if($iconUrl): ?>
                                        <img src="<?= e($iconUrl) ?>" class="app-icon">
                                    <?php else: ?>
                                        <i class="ph ph-cube app-icon-fallback"></i>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="app-info">
                                <h3 class="app-name"><?= e($p['name']) ?></h3>
                                <div class="app-meta">
                                    <div class="app-rating">
                                        4.7 <i class="ph-fill ph-star"></i>
                                    </div>
                                    <div class="app-status">
                                        <?= $p['pricing_model'] === 'free' ? 'Free' : '$'.number_format($p['price'], 0) ?>
                                    </div>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; endforeach; ?>

        </div>
    </section>

    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</body>
</html>

