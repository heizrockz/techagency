<?php
/**
 * BlogController — handles blog detail page
 */
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';

function showBlogDetail(string $slug): void
{
    $blog = getBlogBySlug($slug);

    if (!$blog) {
        http_response_code(404);
        die("Blog post not found.");
    }

    // Increment view count
    try {
        $db = getDB();
        $db->prepare('UPDATE blogs SET view_count = COALESCE(view_count,0) + 1 WHERE slug = ?')->execute([$slug]);
    } catch (Exception $e) { /* silently fail */
    }

    $locale = getCurrentLocale();
    $dir = isRTL() ? 'rtl' : 'ltr';

    // Build best OG image
    $ogImage = '';
    if (($blog['media_type'] ?? 'image') === 'image' && !empty($blog['media_url'])) {
        $ogImage = baseUrl($blog['media_url']);
    } elseif (($blog['media_type'] ?? '') === 'video_link' && !empty($blog['media_url'])) {
        // Try YouTube thumbnail
        $ytPatterns = [
            '/youtube\.com\/watch\?.*v=([a-zA-Z0-9_-]{11})/',
            '/youtu\.be\/([a-zA-Z0-9_-]{11})/',
        ];
        foreach ($ytPatterns as $p) {
            if (preg_match($p, $blog['media_url'], $m)) {
                $ogImage = "https://img.youtube.com/vi/{$m[1]}/maxresdefault.jpg";
                break;
            }
        }
    }

    // SEO setup for the specific blog
    $seo = [
        'title' => $blog['title'] . ' | ' . APP_NAME,
        'description' => $blog['description'] ?? '',
        'keywords' => $blog['keywords'] ?? '',
        'og_image' => $ogImage,
        'canonical_link' => baseUrl('blog/' . $blog['slug'])
    ];

    $viewFile = 'blog-detail';
    require __DIR__ . '/../views/layouts/main.php';
}

function showAll(): void
{
    $locale = getCurrentLocale();
    $seo = getSeoMeta('blogs');

    $seo = [
        'title' => ($seo['title'] ?? 'Our Insights') . ' | ' . APP_NAME,
        'description' => $seo['description'] ?? '',
        'keywords' => $seo['keywords'] ?? '',
        'canonical_link' => baseUrl('blogs')
    ];

    $viewFile = 'blogs';
    require __DIR__ . '/../views/layouts/main.php';
}
