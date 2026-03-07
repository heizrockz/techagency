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
        }
        body { background: #0b0e14; color: #fff; font-family: 'Inter', sans-serif; overflow-x: hidden; }
        
        .store-hero {
            padding: 100px 0 60px;
            background: radial-gradient(circle at 50% 0%, rgba(139, 92, 246, 0.1) 0%, transparent 70%);
        }

        .search-bar-container {
            max-width: 600px;
            margin: 0 auto 60px;
            position: relative;
        }
        .search-bar {
            width: 100%;
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--glass-border);
            padding: 14px 24px 14px 50px;
            border-radius: 100px;
            color: white;
            outline: none;
            transition: all 0.3s;
        }
        .search-bar:focus { border-color: #8b5cf6; background: rgba(255,255,255,0.08); }
        .search-icon { position: absolute; left: 20px; top: 50%; transform: translateY(-50%); color: rgba(255,255,255,0.3); }

        .bento-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            grid-template-rows: repeat(2, 300px);
            gap: 24px;
            margin-bottom: 80px;
        }
        .bento-item {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 32px;
            overflow: hidden;
            position: relative;
            transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
            text-decoration: none;
            color: inherit;
        }
        .bento-item:hover { transform: scale(0.98); border-color: #8b5cf6; box-shadow: 0 0 40px rgba(139, 92, 246, 0.2); }
        .bento-item.featured { grid-column: span 2; grid-row: span 2; }
        .bento-item.medium { grid-column: span 2; }
        .item-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, transparent 40%, rgba(11, 14, 20, 0.9));
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 40px;
        }
        .item-bg { width: 100%; height: 100%; object-fit: cover; transition: transform 0.8s; }
        .bento-item:hover .item-bg { transform: scale(1.1); }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }
        .section-title { font-size: 20px; font-weight: 800; tracking-tight: -0.5px; display: flex; align-items: center; gap: 8px; }
        .section-title i { color: #8b5cf6; font-size: 14px; }

        .app-scroller { padding: 10px 0 40px; }
        
        .app-card-premium {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            transition: all 0.3s;
            text-decoration: none;
            color: inherit;
            height: 100%;
        }
        .app-card-premium:hover { background: rgba(255,255,255,0.06); border-color: #8b5cf6; transform: translateY(-4px); }
        
        .app-icon-wrapper {
            width: 100%;
            aspect-ratio: 1;
            border-radius: 16px;
            background: linear-gradient(135deg, rgba(255,255,255,0.05), rgba(255,255,255,0.01));
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.05);
        }
        .app-icon-wrapper img { width: 60%; height: 60%; object-fit: contain; }

        .price-tag { font-[10px] font-black uppercase tracking-widest text-[#8b5cf6]; background: rgba(139, 92, 246, 0.1); padding: 4px 8px; border-radius: 6px; }

        @media (max-width: 1024px) {
            .bento-grid { grid-template-columns: repeat(2, 1fr); grid-template-rows: auto; }
            .bento-item { height: 300px; }
            .bento-item.featured { grid-column: span 2; }
        }
    </style>
</head>
<body>

    <section class="store-hero">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h1 class="text-6xl font-black mb-4 tracking-tighter">MicoStore</h1>
                <p class="text-white/40 max-w-xl mx-auto font-medium italic">High performance apps & software curated for your ecosystem.</p>
            </div>
            
            <div class="search-bar-container">
                <i class="ph ph-magnifying-glass search-icon"></i>
                <input type="text" class="search-bar" placeholder="Search apps, software, and more">
            </div>

            <!-- Bento Highlights (Active/Newest) -->
            <div class="bento-grid">
                <?php foreach ($featured as $idx => $p): 
                    $class = ($idx === 0) ? 'featured' : (($idx === 1) ? 'medium' : '');
                    $headerImg = $p['header_image'] ?: 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&w=1200&q=80';
                    if (!empty($p['header_image']) && strpos($p['header_image'], 'http') !== 0) $headerImg = baseUrl($p['header_image']);
                ?>
                    <a href="<?= baseUrl('software/' . $p['slug']) ?>" class="bento-item <?= $class ?>">
                        <img src="<?= e($headerImg) ?>" class="item-bg">
                        <div class="item-overlay">
                            <div class="flex items-center gap-3 mb-4">
                                <span class="px-2 py-0.5 rounded text-[8px] font-black bg-white/10 backdrop-blur border border-white/10 uppercase tracking-widest text-white/60">Featured</span>
                                <span class="text-[8px] font-black uppercase tracking-widest text-[#8b5cf6]"><?= e($p['category_name']) ?></span>
                            </div>
                            <h2 class="text-3xl font-black mb-2 tracking-tight"><?= e($p['name']) ?></h2>
                            <p class="text-sm text-white/60 line-clamp-2"><?= e($p['description']) ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Dynamic Sections -->
            <?php foreach($sections as $sec): if(!empty($sec['products'])): ?>
                <div class="section-header">
                    <h2 class="section-title"><?= e($sec['title']) ?> <i class="ph ph-caret-right"></i></h2>
                </div>
                
                <div class="swiper app-scroller mb-12">
                    <div class="swiper-wrapper">
                        <?php foreach($sec['products'] as $p): 
                            $iconUrl = $p['icon_url'];
                            if (!empty($iconUrl) && strpos($iconUrl, 'http') !== 0) $iconUrl = baseUrl($iconUrl);
                        ?>
                            <div class="swiper-slide" style="width: 200px;">
                                <a href="<?= baseUrl('software/' . $p['slug']) ?>" class="app-card-premium">
                                    <div class="app-icon-wrapper">
                                        <?php if($iconUrl): ?>
                                            <img src="<?= e($iconUrl) ?>">
                                        <?php else: ?>
                                            <i class="ph ph-cube text-4xl text-white/10"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="font-bold text-sm text-white truncate"><?= e($p['name']) ?></h3>
                                        <p class="text-[10px] text-white/40 mb-3"><?= e($p['category_name']) ?></p>
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-1 text-[10px] text-amber-500">
                                                <i class="ph-fill ph-star"></i> <span>4.5</span>
                                            </div>
                                            <?php if($p['show_price']): ?>
                                                <span class="price-tag"><?= $p['pricing_model'] === 'free' ? 'Free' : '$'.number_format($p['price'], 0) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; endforeach; ?>

        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script>
        document.querySelectorAll('.app-scroller').forEach(el => {
            new Swiper(el, {
                slidesPerView: 'auto',
                spaceBetween: 24,
                freeMode: true,
            });
        });
    </script>
</body>
</html>
