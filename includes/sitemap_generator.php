<?php
/**
 * Mico Sage — Sitemap Generator Helper
 */

function generateDynamicSitemap(): string {
    $db = getDB();
    $baseUrl = 'https://micosage.com/'; // Final production URL

    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . PHP_EOL;
    $xml .= '<!--  created with Free Online Sitemap Generator www.xml-sitemaps.com  -->' . PHP_EOL;

    // Helper for date formatting
    $formatDate = function($dateStr = null) {
        $timestamp = $dateStr ? strtotime($dateStr) : time();
        return date('Y-m-d\TH:i:s+00:00', $timestamp);
    };

    // 1. Static Pages
    $staticPages = [
        '' => ['prio' => 1.00],
        'portfolio' => ['prio' => 0.80],
        'contact' => ['prio' => 0.50],
    ];

    foreach ($staticPages as $path => $meta) {
        $xml .= '<url>' . PHP_EOL;
        $xml .= '<loc>' . $baseUrl . $path . '</loc>' . PHP_EOL;
        $xml .= '<lastmod>' . $formatDate() . '</lastmod>' . PHP_EOL;
        $xml .= '<priority>' . number_format($meta['prio'], 2) . '</priority>' . PHP_EOL;
        $xml .= '</url>' . PHP_EOL;
    }

    // 2. Dynamic Blogs
    try {
        $blogs = $db->query("SELECT slug, updated_at FROM blogs WHERE is_active = 1")->fetchAll();
        foreach ($blogs as $blog) {
            $xml .= '<url>' . PHP_EOL;
            $xml .= '<loc>' . $baseUrl . 'blog/' . $blog['slug'] . '</loc>' . PHP_EOL;
            $xml .= '<lastmod>' . $formatDate($blog['updated_at']) . '</lastmod>' . PHP_EOL;
            $xml .= '<priority>0.80</priority>' . PHP_EOL;
            $xml .= '</url>' . PHP_EOL;
        }
    } catch (Exception $e) {}

    // 3. Dynamic Services
    try {
        $services = $db->query("SELECT id, updated_at FROM services WHERE is_active = 1")->fetchAll();
        foreach ($services as $service) {
            // Service link is dynamic by ID in this app: e.g. /#service-ID
            $xml .= '<url>' . PHP_EOL;
            $xml .= '<loc>' . $baseUrl . '#service-' . $service['id'] . '</loc>' . PHP_EOL;
            $xml .= '<lastmod>' . $formatDate($service['updated_at'] ?? null) . '</lastmod>' . PHP_EOL;
            $xml .= '<priority>0.70</priority>' . PHP_EOL;
            $xml .= '</url>' . PHP_EOL;
        }
    } catch (Exception $e) {}

    // 4. Dynamic Portfolio Projects
    try {
        $projects = $db->query("SELECT slug, created_at FROM portfolio_projects WHERE is_active = 1")->fetchAll();
        foreach ($projects as $proj) {
            $xml .= '<url>' . PHP_EOL;
            $xml .= '<loc>' . $baseUrl . 'portfolio/' . $proj['slug'] . '</loc>' . PHP_EOL;
            $xml .= '<lastmod>' . $formatDate($proj['created_at']) . '</lastmod>' . PHP_EOL;
            $xml .= '<priority>0.70</priority>' . PHP_EOL;
            $xml .= '</url>' . PHP_EOL;
        }
    } catch (Exception $e) {}

    $xml .= '</urlset>';
    return $xml;
}
