<?php
$blogs = getBlogs();
$seo = getSeoMeta('blogs');
$title = $seo['title'] ?? 'Our Projects | Mico Sage';
?>

<section class="page-hero" style="padding: 140px 0 80px; position: relative; overflow: hidden; background: var(--bg-primary);">
    <div class="blog-background-effects" style="position:absolute; inset:0; pointer-events:none; z-index:0;">
        <div style="position:absolute; top:-10%; left:-10%; width:40%; height:40%; background:radial-gradient(circle, rgba(var(--neon-violet-rgb), 0.1) 0%, transparent 70%); filter:blur(100px);"></div>
        <div style="position:absolute; bottom:-10%; right:-10%; width:40%; height:40%; background:radial-gradient(circle, rgba(var(--neon-emerald-rgb), 0.1) 0%, transparent 70%); filter:blur(100px);"></div>
    </div>
    
    <div class="section-container" style="position: relative; z-index: 1;">
        <div class="section-heading animate-on-scroll">
            <h1 class="gradient-text" style="font-size: 3rem; margin-bottom: 20px;"><?= e(getContent('blog_title')) ?></h1>
            <p style="color: var(--text-secondary); font-size: 1.1rem; max-width: 600px; margin: 0 auto;"><?= e(getContent('blog_subtitle')) ?></p>
            <div class="heading-line" style="margin: 30px auto; width: 80px; height: 4px; background: linear-gradient(90deg, var(--neon-cyan), var(--neon-violet)); border-radius: 2px;"></div>
        </div>
    </div>
</section>

<section class="blog-grid-section" style="padding: 60px 0 100px; background: var(--bg-primary);">
    <div class="section-container">
        <?php if (empty($blogs)): ?>
            <div style="text-align: center; padding: 100px 0;">
                <h3 style="color: var(--text-muted);">No blog posts found yet.</h3>
            </div>
        <?php else: ?>
            <div id="blog-grid-main" class="blog-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 40px;">
                <?php foreach ($blogs as $i => $blog): 
                    $isHidden = $i >= 10 ? 'style="display:none;" class="blog-card-item-main hidden-blog-main"' : 'class="blog-card-item-main"';
                ?>
                <div <?= $isHidden ?> data-index="<?= $i ?>">
                    <div class="blog-card animate-on-scroll" style="animation-delay: <?= ($i % 10) * 0.05 ?>s; background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: 24px; overflow: hidden; backdrop-filter: blur(12px); transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1); height: 100%; display: flex; flex-direction: column;">
                        
                        <div class="blog-media" style="position: relative; height: 240px; overflow: hidden; border-bottom: 1px solid var(--glass-border); flex-shrink: 0;">
                            <?php if ($blog['media_type'] === 'video' && !empty($blog['media_url'])): ?>
                                <video autoplay loop muted playsinline style="width: 100%; height: 100%; object-fit: cover; opacity: 0.8; transition: transform 0.8s ease;">
                                    <source src="<?= baseUrl($blog['media_url']) ?>" type="video/<?= pathinfo($blog['media_url'], PATHINFO_EXTENSION) ?>">
                                </video>
                            <?php elseif ($blog['media_type'] === 'video_link' && !empty($blog['media_url'])): ?>
                                <?php
                                    // Auto-generate YouTube thumbnail
                                    $ytThumb = '';
                                    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/|youtube\.com\/shorts\/)([a-zA-Z0-9_-]{11})/', $blog['media_url'], $ytMatch)) {
                                        $ytThumb = 'https://img.youtube.com/vi/' . $ytMatch[1] . '/hqdefault.jpg';
                                    }
                                ?>
                                <?php if ($ytThumb): ?>
                                    <img src="<?= e($ytThumb) ?>" alt="<?= e($blog['title'] ?? '') ?>" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.7; transition: transform 0.8s ease;">
                                    <div style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); width:60px; height:60px; background:rgba(255,0,0,0.85); border-radius:12px; display:flex; align-items:center; justify-content:center; pointer-events:none;">
                                        <svg width="28" height="28" viewBox="0 0 24 24" fill="#fff"><polygon points="9.5,7.5 16.5,12 9.5,16.5"/></svg>
                                    </div>
                                <?php else: ?>
                                    <div style="width: 100%; height: 100%; background: #0a0f18; display:flex; align-items:center; justify-content:center;">
                                        <svg width="60" height="60" viewBox="0 0 24 24" fill="var(--neon-pink)" style="opacity:0.8;"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/></svg>
                                    </div>
                                <?php endif; ?>
                            <?php elseif (!empty($blog['media_url'])): ?>
                                <img src="<?= baseUrl($blog['media_url']) ?>" alt="<?= e($blog['title'] ?? '') ?>" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.8s ease; opacity: 0.9;">
                            <?php else: ?>
                                <div style="width:100%; height:100%; background:linear-gradient(135deg, #0a0f18, #141a2a); display:flex; align-items:center; justify-content:center;">
                                    <svg width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="var(--glass-border)" stroke-width="1"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                </div>
                            <?php endif; ?>
                            <div style="position:absolute; top:0; left:0; width:100%; height:100%; background:linear-gradient(to bottom, transparent 40%, rgba(0,0,0,0.6));"></div>
                            <div style="position:absolute; top:20px; right:20px; padding:6px 14px; background:var(--bg-primary); border:1px solid var(--glass-border); border-radius:10px; backdrop-filter:blur(8px); color:var(--text-primary); font-size:0.75rem; font-weight:600; display:flex; align-items:center; gap:8px;">
                                <span><?= date('M d, Y', strtotime($blog['created_at'])) ?></span>
                                <?php if(isset($blog['view_count']) && $blog['view_count'] > 0): ?>
                                    <span style="border-left:1px solid var(--glass-border); padding-left:8px; display:flex; align-items:center; gap:4px; color:var(--neon-cyan);">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        <?= number_format($blog['view_count']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="blog-content" style="padding: 30px; flex-grow: 1; display: flex; flex-direction: column;">
                            <h3 style="font-size: 1.4rem; margin: 0 0 15px 0; color: var(--text-primary); line-height: 1.4; font-weight: 600;"><?= e($blog['title'] ?? '') ?></h3>
                            <p style="color: var(--text-secondary); font-size: 0.95rem; line-height: 1.7; margin-bottom: 30px; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; flex-grow: 1;"><?= e($blog['description'] ?? '') ?></p>
                            
                            <a href="<?= baseUrl('blog/' . e($blog['slug'])) ?>" class="blog-nav-link" style="color: var(--neon-emerald); font-size: 0.85rem; font-weight: 800; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; letter-spacing: 1px; text-transform: uppercase; margin-top: auto;">
                                READ ARTICLE
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <?php if (count($blogs) > 10): ?>
            <div style="text-align: center; margin-top: 60px;">
                <button id="load-more-main" class="btn-ghost" style="border: 2px solid var(--glass-border); padding: 14px 40px; border-radius: 40px; color: var(--text-primary); cursor: pointer; background: transparent; transition: 0.3s; font-weight: 600;">
                    VIEW MORE NEWS
                </button>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<!-- Trending / Most Viewed Section -->
<?php
    $allBlogsForTrending = getBlogs();
    if (!empty($allBlogsForTrending)):
        usort($allBlogsForTrending, function($a, $b) {
            return ($b['view_count'] ?? 0) - ($a['view_count'] ?? 0);
        });
        $topTrending = array_slice($allBlogsForTrending, 0, 3);
?>
<section class="trending-section" style="padding: 100px 0; background: var(--bg-primary); border-top: 1px solid var(--glass-border);">
    <div class="section-container">
        <div class="section-heading" style="text-align:left; margin-bottom: 50px;">
            <h2 style="font-size: 2rem; color: var(--text-primary); display: flex; align-items: center; gap: 12px;">
                <span style="width: 4px; height: 30px; background: var(--neon-pink); border-radius: 4px;"></span>
                🔥 <?= getCurrentLocale() === 'en' ? 'Trending Stories' : 'أشهر المقالات' ?>
            </h2>
            <p style="color: var(--text-secondary); margin-top: 10px;"><?= getCurrentLocale() === 'en' ? 'Most read articles from our community.' : 'المقالات الأكثر قراءة من قبل روادنا.' ?></p>
        </div>
        
        <div class="trending-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 30px;">
            <?php foreach ($topTrending as $t): ?>
            <a href="<?= baseUrl('blog/' . $t['slug']) ?>" class="trending-card-link" style="text-decoration:none; display:block;">
                <div class="trending-card" style="background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: 20px; padding: 24px; transition: all 0.4s ease; display: flex; gap: 20px; align-items: center; backdrop-filter: blur(100px);">
                    <div style="width: 80px; height: 80px; border-radius: 12px; overflow: hidden; flex-shrink: 0; background: var(--bg-secondary);">
                        <?php 
                            $tThumb = baseUrl($t['media_url'] ?: 'assets/images/placeholder.webp');
                            if ($t['media_type'] === 'video_link' && preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/', $t['media_url'], $m)) {
                                $tThumb = 'https://img.youtube.com/vi/' . $m[1] . '/hqdefault.jpg';
                            }
                        ?>
                        <img src="<?= $tThumb ?>" style="width:100%; height:100%; object-fit:cover;">
                    </div>
                    <div style="flex-grow: 1; overflow: hidden;">
                        <span style="font-size: 0.75rem; color: var(--neon-cyan); font-weight: 600; text-transform: uppercase;"><?= date('M d', strtotime($t['created_at'])) ?></span>
                        <h4 style="color: var(--text-primary); margin: 6px 0 8px 0; font-size: 1.05rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?= e($t['title'] ?? '') ?></h4>
                        <div style="font-size: 0.75rem; color: var(--text-muted); display: flex; align-items: center; gap: 5px;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <?= number_format($t['view_count'] ?? 0) ?> views
                        </div>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<style>
    .trending-card:hover { 
        background: var(--glass-hover); 
        transform: translateX(10px); 
        border-color: var(--neon-pink); 
        box-shadow: 0 10px 30px -10px rgba(var(--neon-pink-rgb), 0.2);
    }
    @media (max-width: 768px) {
        .trending-card:hover { transform: translateY(-5px); }
    }
</style>
<?php endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loadBtn = document.getElementById('load-more-main');
        if (loadBtn) {
            loadBtn.addEventListener('click', function() {
                const hiddenBatch = document.querySelectorAll('.hidden-blog-main');
                const nextItems = Array.from(hiddenBatch).slice(0, 10);
                
                nextItems.forEach(item => {
                    item.style.display = 'block';
                    item.classList.remove('hidden-blog-main');
                });
                
                if (document.querySelectorAll('.hidden-blog-main').length === 0) {
                    loadBtn.style.display = 'none';
                }
            });
        }
    });
</script>

<style>
    .blog-card:hover { transform: translateY(-15px); background: var(--glass-hover); border-color: var(--theme-primary); box-shadow: 0 30px 60px -15px rgba(0,0,0,0.3); }
    .blog-card:hover img, .blog-card:hover video { transform: scale(1.1); }
    .blog-nav-link:hover { color: var(--theme-gold); gap: 12px; }
</style>
