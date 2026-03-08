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
            --glass-bg: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.08);
            --accent: #8b5cf6;
        }
        body { background: #0b0e14; color: #fff; font-family: 'Inter', sans-serif; overflow-x: hidden; }
        
        .store-hero {
            padding: 120px 0 80px;
            background: 
                radial-gradient(circle at 0% 0%, rgba(139, 92, 246, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 100% 100%, rgba(236, 72, 153, 0.05) 0%, transparent 50%);
        }

        .search-bar-container {
            max-width: 650px;
            margin: 0 auto 80px;
            position: relative;
        }
        .search-bar {
            width: 100%;
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--glass-border);
            padding: 18px 24px 18px 60px;
            border-radius: 24px;
            color: white;
            outline: none;
            transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
            font-size: 16px;
            backdrop-filter: blur(10px);
        }
        .search-bar:focus { 
            border-color: var(--accent); 
            background: rgba(255,255,255,0.06); 
            box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.1);
            transform: translateY(-2px);
        }
        .search-icon { position: absolute; left: 24px; top: 50%; transform: translateY(-50%); color: rgba(255,255,255,0.2); font-size: 20px; }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 32px;
            padding: 0 10px;
        }
        .section-title { 
            font-size: 24px; 
            font-weight: 900; 
            letter-spacing: -1px; 
            text-transform: uppercase;
            background: linear-gradient(to right, #fff, rgba(255,255,255,0.4));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* App Tile Grid */
        .app-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 24px;
            margin-bottom: 80px;
        }

        .app-tile {
            background: linear-gradient(145deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.01) 100%);
            border: 1px solid var(--glass-border);
            border-radius: 32px;
            padding: 24px;
            text-align: center;
            transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
            text-decoration: none;
            color: inherit;
            position: relative;
            overflow: hidden;
        }
        .app-tile::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(800px circle at var(--x) var(--y), rgba(255,255,255,0.06), transparent 40%);
            opacity: 0;
            transition: opacity 0.5s;
        }
        .app-tile:hover {
            transform: translateY(-8px) scale(1.02);
            border-color: rgba(139, 92, 246, 0.3);
            background: linear-gradient(145deg, rgba(255,255,255,0.08) 0%, rgba(255,255,255,0.02) 100%);
            box-shadow: 0 20px 40px -10px rgba(0,0,0,0.5);
        }
        .app-tile:hover::before { opacity: 1; }

        .app-tile-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            border-radius: 20px;
            background: #1a1d23;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
            border: 1px solid rgba(255,255,255,0.05);
            transition: transform 0.5s ease;
            position: relative;
            z-index: 1;
        }
        .app-tile:hover .app-tile-icon { transform: scale(1.1) rotate(5deg); }
        .app-tile-icon img { width: 70%; height: 70%; object-fit: contain; }
        .app-tile-icon i { font-size: 40px; color: var(--accent); }

        .app-tile-name { font-size: 15px; font-weight: 800; margin-bottom: 4px; color: #fff; z-index: 1; position: relative; }
        .app-tile-cat { font-size: 10px; font-weight: 700; color: rgba(255,255,255,0.3); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px; z-index: 1; position: relative; }
        
        .app-tile-footer {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            z-index: 1;
            position: relative;
        }
        .app-tile-rating { font-size: 11px; font-weight: 700; color: #f59e0b; display: flex; align-items: center; gap: 4px; }
        .app-tile-price { 
            font-size: 10px; 
            font-weight: 900; 
            background: rgba(255,255,255,0.05); 
            padding: 4px 10px; 
            border-radius: 8px; 
            color: rgba(255,255,255,0.6);
            border: 1px solid rgba(255,255,255,0.05);
        }

        .featured-banner {
            width: 100%;
            height: 400px;
            border-radius: 48px;
            margin-bottom: 80px;
            position: relative;
            overflow: hidden;
            border: 1px solid var(--glass-border);
        }
        .featured-banner img { width: 100%; height: 100%; object-fit: cover; }
        .featured-content {
            position: absolute;
            inset: 0;
            background: linear-gradient(to right, rgba(11, 14, 20, 0.95) 20%, transparent 80%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 80px;
        }

        @media (max-width: 768px) {
            .app-grid { grid-template-columns: repeat(2, 1fr); }
            .featured-content { padding: 40px; }
            .section-title { font-size: 20px; }
        }
    </style>
</head>
<body onmousemove="handleMouseMove(event)" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">

    <section class="store-hero">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-violet-500/10 border border-violet-500/20 text-violet-400 text-[10px] font-black uppercase tracking-widest mb-6">
                    <i class="ph-fill ph-sparkle"></i> Premium Ecosystem
                </div>
                <h1 class="text-7xl font-black mb-4 tracking-tighter">MicoStore</h1>
                <p class="text-white/40 max-w-xl mx-auto font-medium text-lg italic leading-relaxed">High performance apps & software curated for your ecosystem.</p>
            </div>
            
            <div class="search-bar-container">
                <i class="ph ph-magnifying-glass search-icon"></i>
                <input type="text" class="search-bar" placeholder="Search apps, software, and more">
            </div>

            <?php if(!empty($featured)): $f = $featured[0]; ?>
            <!-- Hero Featured -->
            <a href="<?= baseUrl('software/' . $f['slug']) ?>" class="featured-banner group block">
                <img src="<?= $f['header_image'] ? (strpos($f['header_image'], 'http') === 0 ? e($f['header_image']) : baseUrl($f['header_image'])) : 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&w=1600&q=80' ?>" class="group-hover:scale-105 transition-transform duration-1000">
                <div class="featured-content">
                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-violet-400 mb-4 block">Newest Release</span>
                    <h2 class="text-5xl font-black mb-6 tracking-tighter max-w-lg"><?= e($f['name']) ?></h2>
                    <p class="text-white/60 max-w-md text-lg mb-8 line-clamp-2"><?= e($f['description']) ?></p>
                    <div class="flex items-center gap-6">
                        <span class="px-6 py-3 bg-white text-black font-black uppercase tracking-widest rounded-2xl text-xs group-hover:bg-violet-400 group-hover:text-white transition-all">Explore App</span>
                    </div>
                </div>
            </a>
            <?php endif; ?>

            <!-- Dynamic Sections -->
            <?php foreach($sections as $sec): if(!empty($sec['products'])): ?>
                <div class="section-header">
                    <h2 class="section-title"><?= e($sec['title']) ?></h2>
                    <a href="#" class="text-[10px] font-black uppercase tracking-[0.2em] text-white/20 hover:text-white transition-colors">See All</a>
                </div>
                
                <div class="app-grid">
                    <?php foreach($sec['products'] as $p): 
                        $iconUrl = $p['icon_url'];
                        if (!empty($iconUrl) && strpos($iconUrl, 'http') !== 0) $iconUrl = baseUrl($iconUrl);
                    ?>
                        <a href="<?= baseUrl('software/' . $p['slug']) ?>" class="app-tile">
                            <div class="app-tile-icon">
                                <?php if($iconUrl): ?>
                                    <img src="<?= e($iconUrl) ?>">
                                <?php else: ?>
                                    <i class="ph ph-cube"></i>
                                <?php endif; ?>
                            </div>
                            <h3 class="app-tile-name"><?= e($p['name']) ?></h3>
                            <p class="app-tile-cat"><?= e($p['category_name']) ?></p>
                            
                            <div class="app-tile-footer">
                                <div class="app-tile-rating">
                                    <i class="ph-fill ph-star"></i> 4.7
                                </div>
                                <?php if($p['show_price']): ?>
                                    <span class="app-tile-price"><?= $p['pricing_model'] === 'free' ? 'Free' : '$'.number_format($p['price'], 0) ?></span>
                                <?php endif; ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; endforeach; ?>

        </div>
    </section>

    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script>
        function handleMouseMove(e) {
            for(const tile of document.getElementsByClassName("app-tile")) {
                const rect = tile.getBoundingClientRect(),
                x = e.clientX - rect.left,
                y = e.clientY - rect.top;

                tile.style.setProperty("--x", `${x}px`);
                tile.style.setProperty("--y", `${y}px`);
            }
        }
    </script>
</body>
</html>

