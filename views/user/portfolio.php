<?php
/**
 * Portfolio Page — Showcase of projects
 */
$projects = getPortfolioProjects();
$siteName = getSetting('site_name', 'Mico Sage');
$isAr = getCurrentLocale() === 'ar';
?>

<!-- ═══════════════════════════════════════════════════════════
     Portfolio Hero
     ═══════════════════════════════════════════════════════════ -->
<section class="portfolio-hero" id="portfolio-top">
    <div class="section-container">
        <a href="<?= baseUrl('/') ?>" class="portfolio-back-link">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            <?= t('portfolio_back') ?>
        </a>
        <h1 class="hero-title" style="margin-top: 24px;">
            <span class="gradient-text"><?= e(getContent('portfolio_title')) ?></span>
        </h1>
        <p class="hero-subtitle"><?= e(getContent('portfolio_subtitle')) ?></p>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════
     Category Filters
     ═══════════════════════════════════════════════════════════ -->
<section class="portfolio-filters-section">
    <div class="section-container">
        <div class="portfolio-filters" id="portfolioFilters">
            <button class="pf-tab active" data-cat="all"><?= t('portfolio_all') ?></button>
            <button class="pf-tab" data-cat="website"><?= t('portfolio_websites') ?></button>
            <button class="pf-tab" data-cat="app"><?= t('portfolio_apps') ?></button>
            <button class="pf-tab" data-cat="marketing"><?= t('portfolio_marketing') ?></button>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════
     Project Grid
     ═══════════════════════════════════════════════════════════ -->
<section class="portfolio-grid-section">
    <div class="section-container">
        <?php if (empty($projects)): ?>
            <p style="text-align:center; color: var(--text-muted); padding: 60px 0;">
                <?= $isAr ? 'لا توجد مشاريع حالياً' : 'No projects yet' ?>
            </p>
        <?php else: ?>
            <div class="portfolio-grid" id="portfolioGrid">
                <?php foreach ($projects as $i => $proj):
                    $tags = !empty($proj['tags']) ? explode(',', $proj['tags']) : [];
                    $isFeatured = $proj['is_featured'] == 1;
                    $colorRgb = getColorRgb($proj['color']);
                    $catLabel = ucfirst($proj['category']);
                ?>
                <div class="portfolio-card<?= $isFeatured ? ' portfolio-card--featured' : '' ?> animate-on-scroll"
                     data-category="<?= e($proj['category']) ?>"
                     style="animation-delay: <?= $i * 0.1 ?>s;">
                    <!-- Gradient header bar -->
                    <div class="portfolio-card__header" style="background: linear-gradient(135deg, rgba(<?= $colorRgb ?>, 0.25), rgba(<?= $colorRgb ?>, 0.05));">
                        <div class="portfolio-card__icon" style="color: rgba(<?= $colorRgb ?>, 1);">
                            <?= getIconSvg($proj['category'] === 'app' ? 'monitor' : ($proj['category'] === 'marketing' ? 'chart' : 'code'), $proj['color']) ?>
                        </div>
                        <span class="portfolio-card__badge" style="background: rgba(<?= $colorRgb ?>, 0.15); color: rgba(<?= $colorRgb ?>, 1);">
                            <?= e($catLabel) ?>
                        </span>
                    </div>

                    <!-- Content -->
                    <div class="portfolio-card__body">
                        <h3 class="portfolio-card__title"><?= e($proj['title'] ?? '') ?></h3>

                        <?php if (!empty($proj['client_name'])): ?>
                        <p class="portfolio-card__client">
                            <span class="portfolio-card__client-label"><?= t('portfolio_client') ?>:</span>
                            <?= e($proj['client_name']) ?>
                        </p>
                        <?php endif; ?>

                        <p class="portfolio-card__desc"><?= e($proj['description'] ?? '') ?></p>

                        <?php if (!empty($tags)): ?>
                        <div class="portfolio-card__tags">
                            <?php foreach ($tags as $tag): ?>
                                <span class="portfolio-tag"><?= e(trim($tag)) ?></span>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($proj['demo_url'])): ?>
                        <a href="<?= e($proj['demo_url']) ?>" target="_blank" class="portfolio-card__cta" style="color: rgba(<?= $colorRgb ?>, 1);">
                            <?= t('portfolio_view') ?>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- CTA -->
        <div class="portfolio-cta" style="text-align: center; margin-top: 60px;">
            <a href="<?= baseUrl('/') ?>#booking" class="btn-primary">
                <?= t('hero_cta') ?>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>
</section>

<script>
// Portfolio filter tabs
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.pf-tab');
    const cards = document.querySelectorAll('.portfolio-card');

    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            const cat = this.dataset.cat;

            cards.forEach(card => {
                if (cat === 'all' || card.dataset.category === cat) {
                    card.style.display = '';
                    card.style.animation = 'fadeInUp 0.5s ease forwards';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
});
</script>
