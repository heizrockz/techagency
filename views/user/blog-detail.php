<?php
/**
 * Blog Detail View
 */
?>

<!-- futuristic Blog Detail UI -->
<article class="blog-detail-section" style="padding: 120px 0 80px; position: relative; overflow: hidden;">
    <!-- Background Accents -->
    <div class="bg-accents" style="position:absolute; inset:0; pointer-events:none; z-index:0;">
        <div style="position:absolute; top:10%; right:-5%; width:30%; height:40%; background:radial-gradient(circle, rgba(var(--neon-violet-rgb), 0.1) 0%, transparent 70%); filter:blur(100px);"></div>
        <div style="position:absolute; bottom:10%; left:-5%; width:30%; height:40%; background:radial-gradient(circle, rgba(var(--neon-emerald-rgb), 0.1) 0%, transparent 70%); filter:blur(100px);"></div>
    </div>

    <div class="section-container" style="position: relative; z-index: 1;">
        <!-- Back Link -->
        <a href="<?= baseUrl('/') ?>#blogs" class="back-link animate-on-scroll" style="display:inline-flex; align-items:center; gap:8px; color:rgba(255,255,255,0.6); text-decoration:none; margin-bottom:32px; font-weight:500; transition:all 0.3s ease;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            <?= getCurrentLocale() === 'en' ? 'Back to Insights' : 'العودة للمقالات' ?>
        </a>

        <!-- Blog Header -->
        <header class="blog-header animate-on-scroll" style="margin-bottom: 48px;">
            <div class="blog-meta" style="margin-bottom:16px; display:flex; align-items:center; gap:16px;">
                <span class="blog-date" style="font-size:0.9rem; color:var(--neon-emerald); font-weight:600; text-transform:uppercase; letter-spacing:1px;">
                    <?= date('M d, Y', strtotime($blog['created_at'])) ?>
                </span>
                <div style="width:4px; height:4px; border-radius:50%; background:rgba(255,255,255,0.2);"></div>
                <span class="blog-category" style="font-size:0.9rem; color:rgba(255,255,255,0.5);">
                    <?= e($blog['category'] ?? (getCurrentLocale()==='en'?'Technology':'تكنولوجيا')) ?>
                </span>
            </div>
            <h1 class="gradient-text" style="font-size: clamp(2.5rem, 5vw, 4rem); line-height: 1.1; margin-bottom: 24px;">
                <?= e($blog['title']) ?>
            </h1>
            <p class="blog-lead" style="font-size: 1.25rem; color: rgba(255,255,255,0.7); max-width: 800px; line-height: 1.6;">
                <?= e($blog['description']) ?>
            </p>
        </header>

        <!-- Main Content Area -->
        <div class="blog-content-wrapper" style="display: grid; grid-template-columns: 1fr; gap: 48px;">
            
            <!-- Blog Featured Media -->
            <div class="blog-featured-media animate-on-scroll" style="border-radius:24px; overflow:hidden; border:1px solid rgba(255,255,255,0.1); background:rgba(255,255,255,0.03); box-shadow:0 30px 60px -12px rgba(0,0,0,0.5);">
                <?php
                    $mediaType = $blog['media_type'] ?? 'image';
                    $mediaUrl  = $blog['media_url'] ?? '';

                    // Helper: extract YouTube video ID from various URL formats
                    function extractYouTubeId($url) {
                        $patterns = [
                            '/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/|youtube\.com\/shorts\/)([a-zA-Z0-9_-]{11})/',
                        ];
                        foreach ($patterns as $p) {
                            if (preg_match($p, $url, $m)) return $m[1];
                        }
                        return null;
                    }

                    // Helper: extract Vimeo video ID
                    function extractVimeoId($url) {
                        if (preg_match('/vimeo\.com\/(\d+)/', $url, $m)) return $m[1];
                        return null;
                    }
                ?>

                <?php if ($mediaType === 'video' && !empty($mediaUrl)): ?>
                    <!-- Uploaded video file — native HTML5 player -->
                    <video controls playsinline style="width:100%; display:block; background:#000;">
                        <source src="<?= baseUrl($mediaUrl) ?>" type="video/<?= pathinfo($mediaUrl, PATHINFO_EXTENSION) ?>">
                        Your browser does not support the video tag.
                    </video>

                <?php elseif ($mediaType === 'video_link' && !empty($mediaUrl)): ?>
                    <?php $ytId = extractYouTubeId($mediaUrl); $vmId = extractVimeoId($mediaUrl); ?>

                    <?php if ($ytId): ?>
                        <!-- YouTube embed -->
                        <div style="position:relative; padding-bottom:56.25%; height:0; overflow:hidden;">
                            <iframe style="position:absolute; top:0; left:0; width:100%; height:100%; border:0;"
                                    src="https://www.youtube.com/embed/<?= e($ytId) ?>?rel=0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen></iframe>
                        </div>
                    <?php elseif ($vmId): ?>
                        <!-- Vimeo embed -->
                        <div style="position:relative; padding-bottom:56.25%; height:0; overflow:hidden;">
                            <iframe style="position:absolute; top:0; left:0; width:100%; height:100%; border:0;"
                                    src="https://player.vimeo.com/video/<?= e($vmId) ?>"
                                    allow="autoplay; fullscreen; picture-in-picture"
                                    allowfullscreen></iframe>
                        </div>
                    <?php else: ?>
                        <!-- Fallback: generic external video link -->
                        <div style="position:relative; padding-bottom:56.25%; height:0; overflow:hidden;">
                            <iframe style="position:absolute; top:0; left:0; width:100%; height:100%; border:0;"
                                    src="<?= e($mediaUrl) ?>"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen></iframe>
                        </div>
                    <?php endif; ?>

                <?php elseif (!empty($mediaUrl)): ?>
                    <!-- Image -->
                    <img src="<?= baseUrl($mediaUrl) ?>" alt="<?= e($blog['title']) ?>" style="width:100%; height:auto; display:block;">

                <?php else: ?>
                    <!-- No media placeholder -->
                    <div style="width:100%; aspect-ratio:16/9; background:linear-gradient(45deg, #1e1e1e, #2a2a2a); display:flex; align-items:center; justify-content:center; color:rgba(255,255,255,0.1);">
                        <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Blog Rich Text Content -->
            <div class="blog-rich-content animate-on-scroll" style="font-size:1.15rem; line-height:1.8; color:rgba(255,255,255,0.85); max-width:850px; margin:0 auto;">
                <?= ($blog['content']) ?> <!-- Already sanitized or assuming HTML from admin -->
            </div>

        </div>

        <!-- Share Section -->
        <div class="blog-footer animate-on-scroll" style="margin-top:80px; padding-top:40px; border-top:1px solid rgba(255,255,255,0.1); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:24px;">
            <div class="share-title" style="font-weight:600; color:#fff;"><?= getCurrentLocale() === 'en' ? 'Share this Insight' : 'شارك هذا المقال' ?></div>
            <div class="social-share-links" style="display:flex; gap:16px;">
                <a href="#" class="share-btn" style="width:40px; height:40px; border-radius:50%; background:rgba(255,255,255,0.05); display:flex; align-items:center; justify-content:center; color:#fff; transition:0.3s;"><svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg></a>
                <a href="#" class="share-btn" style="width:40px; height:40px; border-radius:50%; background:rgba(255,255,255,0.05); display:flex; align-items:center; justify-content:center; color:#fff; transition:0.3s;"><svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path></svg></a>
                <a href="#" class="share-btn" style="width:40px; height:40px; border-radius:50%; background:rgba(255,255,255,0.05); display:flex; align-items:center; justify-content:center; color:#fff; transition:0.3s;"><svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect x="2" y="9" width="4" height="12"></rect><circle cx="4" cy="4" r="2"></circle></svg></a>
            </div>
        </div>
    </div>
</article>

<style>
    .back-link:hover { color: var(--neon-emerald) !important; transform: translateX(-5px); }
    .blog-rich-content h2, .blog-rich-content h3 { color: #fff; margin-top: 2rem; margin-bottom: 1rem; }
    .blog-rich-content p { margin-bottom: 1.5rem; }
    .blog-rich-content strong { color: var(--neon-emerald); }
    .blog-rich-content ul { padding-left: 1.5rem; margin-bottom: 1.5rem; }
    .blog-rich-content li { margin-bottom: 0.5rem; list-style-type: square; color: rgba(255,255,255,0.7); }
    .share-btn:hover { background: var(--neon-emerald) !important; transform: translateY(-3px); box-shadow:0 10px 20px -5px rgba(16,185,129,0.3); }
</style>
