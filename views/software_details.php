<?php
if (!defined('APP_NAME')) die('Direct access prevented');
?>
<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <title><?= e($product['name']) ?> — Software Ecosystem</title>
    <meta name="description" content="<?= e($product['meta_description'] ?: $product['description']) ?>">
    <meta name="keywords" content="<?= e($product['meta_keywords']) ?>">
    <?php require __DIR__ . '/admin/partials/_head_assets.php'; ?>
    <style>
        :root {
            --glass-bg: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.08);
        }
        body { background: #0b0e14; color: #fff; font-family: 'Inter', sans-serif; overflow-x: hidden; }
        
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
            margin: 60px auto;
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
            .details-container { grid-template-cols: 1fr; }
        }
    </style>
</head>
<body>

    <nav class="product-nav">
        <a href="<?= baseUrl('software') ?>" class="flex items-center gap-2 text-white/60 hover:text-white transition-colors">
            <i class="ph ph-arrow-left"></i>
            <span class="text-sm font-bold uppercase tracking-widest">Store</span>
        </a>
        <div class="flex items-center gap-4">
            <span class="text-[10px] font-black uppercase tracking-widest text-white/20">Version <?= e($product['version']) ?></span>
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
            <div class="space-y-6 mb-20">
                <h1 class="text-5xl font-black tracking-tighter"><?= e($product['name']) ?></h1>
                <p class="text-xl text-white/60 leading-relaxed"><?= e($product['description']) ?></p>
            </div>

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
                    <h3 class="text-sm font-black uppercase tracking-widest text-white/40">Requirements</h3>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3 text-sm font-medium text-white/80">
                            <i class="ph ph-monitor text-violet-400"></i> Windows 10+
                        </div>
                        <div class="flex items-center gap-3 text-sm font-medium text-white/80">
                            <i class="ph ph-intersect text-violet-400"></i> 64-bit Architecture
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</body>
</html>
