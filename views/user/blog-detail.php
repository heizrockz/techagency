<?php
/**
 * Blog Detail View
 */
?>

<!-- futuristic Blog Detail UI -->
<article class="blog-detail-section" style="padding: 160px 0 80px; position: relative; overflow-wrap: break-word;">
    <!-- Background Accents -->
    <div class="bg-accents" style="position:absolute; inset:0; pointer-events:none; z-index:0;">
        <div style="position:absolute; top:10%; right:-5%; width:30%; height:40%; background:radial-gradient(circle, rgba(var(--neon-violet-rgb), 0.1) 0%, transparent 70%); filter:blur(100px);"></div>
        <div style="position:absolute; bottom:10%; left:-5%; width:30%; height:40%; background:radial-gradient(circle, rgba(var(--neon-emerald-rgb), 0.1) 0%, transparent 70%); filter:blur(100px);"></div>
    </div>

    <div class="section-container" style="position: relative; z-index: 1; max-width: 1200px; margin: 0 auto; padding: 0 20px; width: 100%; box-sizing: border-box;">
        <!-- Back Link -->
        <a href="<?= baseUrl('/') ?>#blogs" class="back-link animate-on-scroll" style="display:inline-flex; align-items:center; gap:8px; color:var(--text-secondary); text-decoration:none; margin-bottom:32px; font-weight:500; transition:all 0.3s ease;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            <?= getCurrentLocale() === 'en' ? 'Back to Insights' : 'العودة للمقالات' ?>
        </a>
        
        <!-- Swiper CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

        <!-- Blog Header -->
        <header class="blog-header animate-on-scroll" style="margin-bottom: 32px;">
            <div class="blog-meta" style="margin-bottom:16px; display:flex; align-items:center; gap:16px;">
                <span class="blog-date" style="font-size:0.9rem; color:var(--neon-emerald); font-weight:600; text-transform:uppercase; letter-spacing:1px;">
                    <?= date('M d, Y', strtotime($blog['created_at'])) ?>
                </span>
                <div style="width:4px; height:4px; border-radius:50%; background:rgba(255,255,255,0.2);"></div>
                <span class="blog-category" style="font-size:0.9rem; color:var(--text-muted);">
                    <?= e($blog['category'] ?? (getCurrentLocale()==='en'?'Technology':'تكنولوجيا')) ?>
                </span>
            </div>
            <h1 class="gradient-text" style="font-size: clamp(2.5rem, 5vw, 4rem); line-height: 1.1; margin-bottom: 24px;">
                <?= e($blog['title']) ?>
            </h1>
            <p class="blog-lead" style="font-size: 1.25rem; color: var(--text-secondary); max-width: 800px; line-height: 1.6; font-weight: 400; overflow-wrap: break-word; word-wrap: break-word;">
                <?= e($blog['description']) ?>
            </p>
        </header>

        <!-- Main Content Area -->
        <div class="blog-container" style="display: grid; grid-template-columns: 1fr 350px; gap: 48px;">
            
            <div class="blog-main-column">
                <!-- Blog Featured Media — Now under title -->
                <!-- Blog Multi-Media Gallery -->
                <div class="blog-media-container animate-on-scroll" style="margin-bottom: 32px; max-width: 1000px; margin-left: auto; margin-right: auto;">
                    <?php
                        $mediaGallery = $blog['media'] ?? [];
                        if (empty($mediaGallery) && !empty($blog['media_url'])) {
                            $mediaGallery = [['media_type' => $blog['media_type'], 'media_url' => $blog['media_url']]];
                        }
                        $isSlider = count($mediaGallery) > 1;
                    ?>

                    <?php if ($isSlider): ?>
                        <!-- Swiper Slider -->
                        <div class="swiper blog-media-slider" style="border-radius:24px; overflow:hidden; border:1px solid var(--glass-border); background:var(--glass-bg); backdrop-filter: blur(10px); box-shadow:0 15px 35px -5px rgba(0,0,0,0.15); height: 400px;">
                            <div class="swiper-wrapper" style="height: 100%;">
                                <?php foreach ($mediaGallery as $mIdx => $media): 
                                    $mediaType = $media['media_type'] ?? 'image';
                                    $mediaUrl  = $media['media_url'] ?? '';
                                    if (empty($mediaUrl)) continue;
                                ?>
                                    <div class="swiper-slide" style="height: 100%;">
                                        <?php if ($mediaType === 'video'): ?>
                                            <video controls playsinline style="width:100%; height:100%; display:block; background:#000; object-fit: cover;">
                                                <source src="<?= baseUrl($mediaUrl) ?>" type="video/<?= pathinfo($mediaUrl, PATHINFO_EXTENSION) ?>">
                                            </video>
                                        <?php elseif ($mediaType === 'video_link'): ?>
                                            <?php $ytId = extractYouTubeIdDetail($mediaUrl); $vmId = extractVimeoIdDetail($mediaUrl); ?>
                                            <div class="video-container" style="position:relative; width:100%; height:100%; overflow:hidden;">
                                                <?php if ($ytId): ?>
                                                    <iframe style="position:absolute; top:0; left:0; width:100%; height:100%; border:0;" src="https://www.youtube.com/embed/<?= e($ytId) ?>?rel=0" allowfullscreen></iframe>
                                                <?php elseif ($vmId): ?>
                                                    <iframe style="position:absolute; top:0; left:0; width:100%; height:100%; border:0;" src="https://player.vimeo.com/video/<?= e($vmId) ?>" allowfullscreen></iframe>
                                                <?php else: ?>
                                                    <iframe style="position:absolute; top:0; left:0; width:100%; height:100%; border:0;" src="<?= e($mediaUrl) ?>" allowfullscreen></iframe>
                                                <?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <img src="<?= baseUrl($mediaUrl) ?>" alt="<?= e($blog['title']) ?>" style="width:100%; height:100%; object-fit: cover; display:block;">
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <!-- Swiper Navigation -->
                            <div class="swiper-button-next"></div>
                            <div class="swiper-button-prev"></div>
                            <!-- Swiper Pagination -->
                            <div class="swiper-pagination"></div>
                        </div>
                    <?php else: ?>
                        <!-- Single Media Item -->
                        <?php if (!empty($mediaGallery)): 
                            $media = $mediaGallery[0];
                            $mediaType = $media['media_type'] ?? 'image';
                            $mediaUrl  = $media['media_url'] ?? '';
                        ?>
                            <div class="single-gallery-item" style="border-radius:24px; overflow:hidden; border:1px solid var(--glass-border); background:var(--glass-bg); backdrop-filter: blur(10px); box-shadow:0 15px 35px -5px rgba(0,0,0,0.15); height: 400px;">
                                <?php if ($mediaType === 'video'): ?>
                                    <video controls playsinline style="width:100%; height:100%; display:block; background:#000; object-fit: cover;">
                                        <source src="<?= baseUrl($mediaUrl) ?>" type="video/<?= pathinfo($mediaUrl, PATHINFO_EXTENSION) ?>">
                                    </video>
                                <?php elseif ($mediaType === 'video_link'): ?>
                                    <?php $ytId = extractYouTubeIdDetail($mediaUrl); $vmId = extractVimeoIdDetail($mediaUrl); ?>
                                    <div style="position:relative; width:100%; height:100%; overflow:hidden;">
                                        <?php if ($ytId): ?>
                                            <iframe style="position:absolute; top:0; left:0; width:100%; height:100%; border:0;" src="https://www.youtube.com/embed/<?= e($ytId) ?>?rel=0" allowfullscreen></iframe>
                                        <?php elseif ($vmId): ?>
                                            <iframe style="position:absolute; top:0; left:0; width:100%; height:100%; border:0;" src="https://player.vimeo.com/video/<?= e($vmId) ?>" allowfullscreen></iframe>
                                        <?php else: ?>
                                            <iframe style="position:absolute; top:0; left:0; width:100%; height:100%; border:0;" src="<?= e($mediaUrl) ?>" allowfullscreen></iframe>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <img src="<?= baseUrl($mediaUrl) ?>" alt="<?= e($blog['title']) ?>" style="width:100%; height:100%; object-fit: cover; display:block;">
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <!-- Blog Rich Text Content -->
                <div class="blog-rich-content animate-on-scroll" style="font-size:1.15rem; line-height:1.9; color:var(--text-primary); font-weight: 400; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word;">
                    <?= ($blog['content']) ?>
                </div>
            </div>

            <!-- Blog Sidebar: Latest Blogs -->
            <aside class="blog-sidebar">
                <div class="sidebar-widget animate-on-scroll" style="background:var(--glass-bg); border:1px solid var(--glass-border); border-radius:20px; padding:24px; backdrop-filter:blur(10px); position: sticky; top: 120px;">
                    <h3 style="font-size:1.2rem; color:var(--text-primary); margin-bottom:20px; display:flex; align-items:center; gap:10px;">
                        <span style="width:3px; height:15px; background:var(--neon-emerald); border-radius:3px;"></span>
                        <?= getCurrentLocale() === 'en' ? 'Latest Insights' : 'أحدث المقالات' ?>
                    </h3>
                    <div class="recent-posts" style="display:flex; flex-direction:column; gap:20px;">
                        <?php 
                        $allBlogs = getBlogs();
                        $recentBlogs = array_slice(array_filter($allBlogs, function($b) use ($blog) { return $b['slug'] !== $blog['slug']; }), 0, 5);
                        foreach ($recentBlogs as $rb): 
                        ?>
                        <a href="<?= baseUrl('blog/' . $rb['slug']) ?>" class="recent-post-item" style="display:flex; gap:12px; text-decoration:none; group;">
                            <div class="recent-thumb" style="width:70px; height:70px; border-radius:10px; overflow:hidden; flex-shrink:0; background:var(--bg-secondary);">
                                <?php if ($rb['media_type'] === 'video_link' && preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $rb['media_url'], $m)): ?>
                                    <img src="https://img.youtube.com/vi/<?= $m[1] ?>/hqdefault.jpg" style="width:100%; height:100%; object-fit:cover;">
                                <?php else: ?>
                                    <img src="<?= baseUrl($rb['media_url'] ?: 'assets/images/placeholder.webp') ?>" alt="" style="width:100%; height:100%; object-fit:cover;">
                                <?php endif; ?>
                            </div>
                            <div class="recent-info" style="flex: 1; overflow: hidden;">
                                <span style="font-size:0.75rem; color:var(--neon-emerald);"><?= date('M d, Y', strtotime($rb['created_at'])) ?></span>
                                <h4 style="font-size:0.95rem; color:var(--text-primary); margin:4px 0 0 0; line-height:1.4; transition:0.3s; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" class="recent-title"><?= e($rb['title']) ?></h4>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </aside>

        </div>

        <!-- Share Section -->
        <div class="blog-footer animate-on-scroll" style="margin-top:60px; padding:30px 0; border-top:1px solid var(--glass-border); border-bottom:1px solid var(--glass-border); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:24px;">
            <div class="share-title" style="font-weight:600; color:var(--text-primary);"><?= getCurrentLocale() === 'en' ? 'Share this Insight' : 'شارك هذا المقال' ?></div>
            <div class="social-share-links" style="display:flex; gap:16px;">
                <a href="#" class="share-btn" style="width:40px; height:40px; border-radius:50%; background:var(--glass-bg); border:1px solid var(--glass-border); display:flex; align-items:center; justify-content:center; color:var(--text-primary); transition:0.3s;"><svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg></a>
                <a href="#" class="share-btn" style="width:40px; height:40px; border-radius:50%; background:var(--glass-bg); border:1px solid var(--glass-border); display:flex; align-items:center; justify-content:center; color:var(--text-primary); transition:0.3s;"><svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path></svg></a>
                <a href="#" class="share-btn" style="width:40px; height:40px; border-radius:50%; background:var(--glass-bg); border:1px solid var(--glass-border); display:flex; align-items:center; justify-content:center; color:var(--text-primary); transition:0.3s;"><svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect x="2" y="9" width="4" height="12"></rect><circle cx="4" cy="4" r="2"></circle></svg></a>
            </div>
        </div>

        <!-- More Insights / Most Viewed Bottom Section -->
        <section class="more-insights" style="margin-top: 80px;">
            <h3 style="font-size:1.8rem; color:var(--text-primary); margin-bottom:40px; display:flex; align-items:center; gap:12px;">
                <span style="width:4px; height:24px; background:var(--neon-emerald); border-radius:4px;"></span>
                <?= getCurrentLocale() === 'en' ? 'More Insights' : 'مقالات إضافية' ?>
            </h3>
            <div class="recent-posts-grid" style="display:grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap:30px;">
                <?php 
                $allBlogs = getBlogs();
                // Get most viewed blogs excluding current one
                $trending = array_filter($allBlogs, function($b) use ($blog) { return $b['slug'] !== $blog['slug']; });
                usort($trending, function($a, $b) { return ($b['view_count'] ?? 0) - ($a['view_count'] ?? 0); });
                $bottomBlogs = array_slice($trending, 0, 3);
                
                foreach ($bottomBlogs as $rb): 
                ?>
                <a href="<?= baseUrl('blog/' . $rb['slug']) ?>" class="bottom-insight-card" style="text-decoration:none; display:block; background:var(--glass-bg); border:1px solid var(--glass-border); border-radius:20px; overflow:hidden; transition:0.3s;">
                    <div style="height:180px; position:relative; overflow:hidden;">
                        <?php if ($rb['media_type'] === 'video_link' && preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $rb['media_url'], $m)): ?>
                            <img src="https://img.youtube.com/vi/<?= $m[1] ?>/hqdefault.jpg" style="width:100%; height:100%; object-fit:cover;">
                        <?php else: ?>
                            <img src="<?= baseUrl($rb['media_url'] ?: 'assets/images/placeholder.webp') ?>" style="width:100%; height:100%; object-fit:cover;">
                        <?php endif; ?>
                    </div>
                    <div style="padding:20px;">
                        <span style="font-size:0.75rem; color:var(--neon-emerald); font-weight:600;"><?= date('M d, Y', strtotime($rb['created_at'])) ?></span>
                        <h4 style="color:var(--text-primary); margin:8px 0 0 0; line-height:1.4; font-size:1.1rem;"><?= e($rb['title']) ?></h4>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</article>

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (document.querySelector('.blog-media-slider')) {
            new Swiper('.blog-media-slider', {
                loop: true,
                speed: 800,
                autoHeight: false,
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
            });
        }
    });
</script>

<style>
    .back-link:hover { color: var(--neon-emerald) !important; transform: translateX(-5px); }
    .blog-rich-content h2, .blog-rich-content h3 { color: var(--text-primary); margin-top: 2.5rem; margin-bottom: 1.25rem; font-weight: 700; }
    .blog-rich-content p { margin-bottom: 1.5rem; }
    .blog-rich-content strong { color: var(--neon-emerald); font-weight: 600; }
    .blog-rich-content ul { padding-left: 1.5rem; margin-bottom: 1.5rem; }
    .blog-rich-content li { margin-bottom: 0.75rem; list-style-type: square; color: var(--text-secondary); }
    .share-btn:hover { background: var(--neon-emerald) !important; color: #fff !important; transform: translateY(-3px); box-shadow:0 10px 20px -5px rgba(16,185,129,0.3); border-color: var(--neon-emerald) !important; }
    
    @media (max-width: 991px) {
        .blog-container { display: flex !important; flex-direction: column !important; gap: 30px !important; }
        .blog-sidebar { order: 2; width: 100% !important; }
        .blog-main-column { order: 1; width: 100% !important; min-width: 0 !important; }
        .blog-media-slider, .single-gallery-item { height: 300px !important; width: 100% !important; max-width: 100vw !important; }
    }
    @media (max-width: 768px) {
        .blog-detail-section { padding-top: 200px !important; }
        .blog-header h1 { font-size: 1.8rem !important; line-height: 1.2 !important; overflow-wrap: anywhere !important; }
        .blog-header { margin-bottom: 24px !important; }
        .blog-meta { flex-wrap: wrap; gap: 10px !important; }
        .blog-media-container { margin-bottom: 24px !important; max-width: 100% !important; padding: 0 !important; }
        .section-container { padding: 0 16px !important; overflow-x: hidden !important; }
    }
    @media (max-width: 576px) {
        .blog-detail-section { padding-top: 180px !important; }
        .blog-header h1 { font-size: 1.5rem !important; }
        .blog-media-slider, .single-gallery-item { height: 180px !important; }
        .blog-rich-content { font-size: 1.05rem !important; overflow-wrap: anywhere !important; }
    }
    .recent-post-item:hover .recent-title { color: var(--neon-emerald) !important; transform: translateX(5px); }
    .bottom-insight-card:hover { transform: translateY(-10px); border-color: var(--neon-emerald); box-shadow: 0 20px 40px -10px rgba(0,0,0,0.3); }

    /* Swiper Styling Overrides */
    .blog-media-slider .swiper-button-next,
    .blog-media-slider .swiper-button-prev {
        color: var(--neon-emerald);
        background: rgba(var(--bg-secondary-rgb), 0.5);
        backdrop-filter: blur(10px);
        width: 45px;
        height: 45px;
        border-radius: 50%;
        border: 1px solid var(--glass-border);
        transition: all 0.3s ease;
    }
    .blog-media-slider .swiper-button-next:after,
    .blog-media-slider .swiper-button-prev:after {
        font-size: 1.2rem;
        font-weight: bold;
    }
    .blog-media-slider .swiper-button-next:hover,
    .blog-media-slider .swiper-button-prev:hover {
        background: var(--neon-emerald);
        color: #fff;
    }
    .blog-media-slider .swiper-pagination-bullet {
        background: var(--text-muted);
        opacity: 0.5;
    }
    .blog-media-slider .swiper-pagination-bullet-active {
        background: var(--neon-emerald);
        opacity: 1;
        width: 25px;
        border-radius: 5px;
        transition: width 0.3s ease;
    }
</style>
