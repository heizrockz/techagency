<?php
if (!defined('APP_NAME')) die('Direct access prevented');
?>
<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <title>Software Store — <?= APP_NAME ?></title>
    <?php require __DIR__ . '/admin/partials/_head_assets.php'; ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <style>
        :root {
            --glass-bg: rgba(255, 255, 255, 0.02);
            --glass-border: rgba(255, 255, 255, 0.06);
            --accent: #8b5cf6;
            --font-main: 'Inter', sans-serif;
        }
        body { background: #0b0e14; color: #fff; font-family: var(--font-main); overflow-x: hidden; -webkit-font-smoothing: antialiased; }
        
        .store-hero {
            padding: 140px 0 100px;
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

        /* Copilot-Style App Tile Grid */
        .app-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 40px;
            margin-bottom: 100px;
            padding: 0 12px;
        }

        .app-card {
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            gap: 16px;
            transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .app-card:hover { transform: translateY(-10px); }

        .app-visual {
            width: 100%;
            aspect-ratio: 1;
            border-radius: 36px;
            overflow: hidden;
            position: relative;
            background: #1c212b;
            box-shadow: 
                0 10px 30px -10px rgba(0,0,0,0.5),
                inset 0 0 0 1px rgba(255,255,255,0.03);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.4s;
        }
        .app-card:hover .app-visual {
            box-shadow: 
                0 30px 60px -12px rgba(0,0,0,0.6),
                inset 0 0 0 1px rgba(255,255,255,0.08);
        }

        /* Dynamic Gradients for Backgrounds */
        .bg-gradient-1 { background: linear-gradient(135deg, #FF6B6B 0%, #FEC260 100%); }
        .bg-gradient-2 { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .bg-gradient-3 { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .bg-gradient-4 { background: linear-gradient(135deg, #2af598 0%, #009efd 100%); }
        .bg-gradient-5 { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .bg-auto { background: linear-gradient(135deg, #1c212b 0%, #2a313d 100%); }

        .app-icon {
            width: 55%;
            height: 55%;
            object-fit: contain;
            filter: drop-shadow(0 8px 16px rgba(0,0,0,0.2));
            transition: transform 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .app-card:hover .app-icon { transform: scale(1.1) rotate(5deg); }
        .app-icon-fallback { font-size: 56px; color: rgba(255,255,255,0.8); transition: transform 0.5s; }
        .app-card:hover .app-icon-fallback { transform: scale(1.1); }

        .app-info { padding: 0 4px; }
        .app-name { 
            font-size: 16px; 
            font-weight: 700; 
            margin-bottom: 6px; 
            color: rgba(255,255,255,0.9);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .app-meta {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        .app-rating { display: flex; align-items: center; gap: 4px; color: rgba(255,255,255,0.3); }
        .app-rating i { color: #f59e0b; font-size: 10px; }
        .app-status {
            background: rgba(255,255,255,0.05);
            padding: 4px 10px;
            border-radius: 8px;
            color: rgba(255,255,255,0.4);
            border: 1px solid rgba(255,255,255,0.05);
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
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

    <section class="store-hero">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h1 class="text-6xl font-black mb-4 tracking-tighter">MicoStore</h1>
                <p class="text-white/30 max-w-xl mx-auto font-medium text-lg italic leading-relaxed">High performance apps curated for your forensic & data needs.</p>
            </div>
            
            <div class="search-bar-container">
                <i class="ph ph-magnifying-glass search-icon"></i>
                <input type="text" class="search-bar" placeholder="Search apps, tools, and platforms">
            </div>

            <?php if(!empty($featured)): $f = $featured[0]; ?>
            <!-- Hero Release -->
            <div class="featured-hero">
                <div class="hero-inner group">
                    <img src="<?= $f['header_image'] ? (strpos($f['header_image'], 'http') === 0 ? e($f['header_image']) : baseUrl($f['header_image'])) : 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&w=1600&q=80' ?>" class="hero-bg group-hover:scale-105 transition-transform duration-1000">
                    <div class="hero-overlay">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-violet-500/20 border border-violet-500/30 text-violet-400 text-[10px] font-black uppercase tracking-widest mb-6">
                            <i class="ph-fill ph-sparkle"></i> Featured Release
                        </div>
                        <h2 class="text-5xl font-black mb-4 tracking-tighter max-w-lg"><?= e($f['name']) ?></h2>
                        <p class="text-white/50 max-w-md text-lg mb-8 line-clamp-2"><?= e($f['description']) ?></p>
                        <a href="<?= baseUrl('software/' . $f['slug']) ?>" class="inline-flex items-center justify-center px-8 py-4 bg-white text-black font-black uppercase tracking-widest rounded-2xl text-xs hover:bg-violet-400 hover:text-white transition-all">Explore App</a>
                    </div>
                </div>
            </div>
            <?php endif; ?>

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
                                <?php if($iconUrl): ?>
                                    <img src="<?= e($iconUrl) ?>" class="app-icon shadow-2xl">
                                <?php else: ?>
                                    <i class="ph ph-cube app-icon-fallback"></i>
                                <?php endif; ?>
                            </div>
                            <div class="app-info">
                                <h3 class="app-name"><?= e($p['name']) ?></h3>
                                <div class="app-meta">
                                    <div class="app-rating">
                                        <i class="ph-fill ph-star"></i> 4.7
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

