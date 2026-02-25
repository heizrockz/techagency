<?php
/**
 * BlogController — handles blog detail page
 */
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';

function showBlogDetail(string $slug): void {
    $blog = getBlogBySlug($slug);

    if (!$blog) {
        http_response_code(404);
        die("Blog post not found.");
    }

    $locale = getCurrentLocale();
    $dir = isRTL() ? 'rtl' : 'ltr';
    
    // SEO setup for the specific blog
    $seo = [
        'title' => $blog['title'] . ' | ' . APP_NAME,
        'description' => $blog['description'] ?? '',
        'keywords' => $blog['keywords'] ?? '',
        'og_image' => $blog['image_url'] ?? '',
        'canonical_link' => baseUrl('blog/' . $blog['slug'])
    ];

    $viewFile = 'blog-detail';
    require __DIR__ . '/../views/layouts/main.php';
}

function showAll(): void {
    $locale = getCurrentLocale();
    $seo = getSeoMeta('blogs');
    
    $seo = [
        'title' => ($seo['title'] ?? 'Our Projects') . ' | ' . APP_NAME,
        'description' => $seo['description'] ?? '',
        'keywords' => $seo['keywords'] ?? '',
        'canonical_link' => baseUrl('blogs')
    ];

    $viewFile = 'blogs';
    require __DIR__ . '/../views/layouts/main.php';
}
