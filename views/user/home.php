<?php
// Prepare data for all sections
$services = getServices();
$products = getProducts();
$clients = getClients();
$teamMembers = getTeamMembers();
$testimonials = getTestimonials();
$bookingFields = getBookingFields();
$showClients = getSetting('show_clients_section', '1') === '1';
$showProducts = getSetting('show_products_section', '1') === '1';
$showStats = getSetting('show_stats_section', '1') === '1';
$showMarketing = getSetting('show_marketing_section', '1') === '1';
$showTeam = getSetting('show_team', '1') === '1';
$showTestimonials = getSetting('show_testimonials', '1') === '1';
$showBlogs = getSetting('show_blog_section', '1') === '1';
$siteName = getSetting('site_name', 'Mico Sage');
$blogs = getBlogs();
?>

<!-- ═══════════════════════════════════════════════════════════
     Hero Section
     ═══════════════════════════════════════════════════════════ -->
<section class="hero-section" id="hero">
    <canvas id="neural-bg" class="neural-bg-canvas"></canvas>
    
    <div class="hero-floating-elements">
        <div class="floating-code code-1">
            <span style="color: var(--neon-violet);">const</span> agency = <span style="color: var(--neon-emerald);">'<?= e($siteName) ?>'</span>;<br>
            <span style="color: var(--neon-violet);">await</span> agency.<span style="color: var(--neon-cobalt);">launch</span>();
        </div>
        <div class="floating-code code-2">
            &lt;<span style="color: var(--neon-pink);">Component</span> <span style="color: var(--neon-cyan);">design</span>=<span style="color: var(--neon-emerald);">"premium"</span> /&gt;
        </div>
        <div class="floating-code code-3">
            <span style="color: var(--neon-violet);">function</span> <span style="color: var(--neon-cobalt);">growBusiness</span>() {<br>
            &nbsp;&nbsp;<span style="color: var(--neon-violet);">return</span> <span style="color: var(--neon-emerald);">success</span>;<br>
            }
        </div>
    </div>

    <div class="section-container">
        <div class="hero-badge">
            <span class="pulse-dot"></span>
            <?= getCurrentLocale() === 'ar' ? 'متاح الآن للمشاريع الجديدة' : 'Now Available for New Projects' ?>
        </div>

        <h1 class="hero-title">
            <span class="gradient-text"><?= e(getContent('hero_title')) ?></span>
        </h1>

        <p class="hero-subtitle"><?= e(getContent('hero_subtitle')) ?></p>

        <div class="hero-cta-group">
            <a href="#booking" class="btn-primary">
                <?= t('hero_cta') ?>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
            <a href="#services" class="btn-ghost"><?= t('nav_services') ?></a>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════
     Tagline Section (Fast. Dynamic. Scalable.)
     ═══════════════════════════════════════════════════════════ -->
<?php if(getSetting('show_tagline_section', '1') === '1'): ?>
<section class="tagline-section" id="tagline">
    <div class="section-container">
        <div class="tagline-grid">
            <div class="tagline-card animate-on-scroll" style="animation-delay: 0s;">
                <div class="tagline-icon"><?= e(getContent('tagline1_icon')) ?></div>
                <h3><?= e(getContent('tagline1_title')) ?></h3>
                <p><?= e(getContent('tagline1_desc')) ?></p>
            </div>
            <div class="tagline-card animate-on-scroll" style="animation-delay: 0.15s;">
                <div class="tagline-icon"><?= e(getContent('tagline2_icon')) ?></div>
                <h3><?= e(getContent('tagline2_title')) ?></h3>
                <p><?= e(getContent('tagline2_desc')) ?></p>
            </div>
            <div class="tagline-card animate-on-scroll" style="animation-delay: 0.3s;">
                <div class="tagline-icon"><?= e(getContent('tagline3_icon')) ?></div>
                <h3><?= e(getContent('tagline3_title')) ?></h3>
                <p><?= e(getContent('tagline3_desc')) ?></p>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ═══════════════════════════════════════════════════════════
     Services Section (Dynamic from DB)
     ═══════════════════════════════════════════════════════════ -->
<section class="services-section" id="services">
    <div class="section-container">
        <div class="section-heading animate-on-scroll">
            <h2><?= t('services_title') ?></h2>
            <div class="heading-line"></div>
        </div>

        <div class="bento-grid">
            <?php foreach ($services as $i => $svc): ?>
            <div class="service-card animate-on-scroll" style="animation-delay: <?= $i * -4 ?>s;">
                <div class="holo-shine"></div>
                <div class="service-icon" style="background: linear-gradient(135deg, rgba(<?= getColorRgb($svc['color']) ?>, 0.15), transparent); box-shadow: 0 0 30px rgba(<?= getColorRgb($svc['color']) ?>, 0.1);">
                    <?= getIconSvg($svc['icon'], $svc['color']) ?>
                </div>
                <h3><?= e($svc['title'] ?? '') ?></h3>
                <p><?= e($svc['description'] ?? '') ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════
     Our Process Section
     ═══════════════════════════════════════════════════════════ -->
<?php if(getSetting('show_process_section', '1') === '1'): ?>
<section class="process-section" id="process">
    <div class="section-container">
        <div class="section-heading animate-on-scroll">
            <h2><?= e(getContent('process_title')) ?></h2>
            <p><?= e(getContent('process_subtitle')) ?></p>
            <div class="heading-line"></div>
        </div>

        <div class="process-timeline animate-on-scroll">
            <div class="process-line"></div>
            
            <div class="process-step" data-step="1">
                <div class="step-circle">1</div>
                <div class="step-content">
                    <h3><?= e(getContent('process_step1_title')) ?></h3>
                    <p><?= e(getContent('process_step1_desc')) ?></p>
                </div>
            </div>

            <div class="process-step" data-step="2">
                <div class="step-circle">2</div>
                <div class="step-content">
                    <h3><?= e(getContent('process_step2_title')) ?></h3>
                    <p><?= e(getContent('process_step2_desc')) ?></p>
                </div>
            </div>

            <div class="process-step" data-step="3">
                <div class="step-circle">3</div>
                <div class="step-content">
                    <h3><?= e(getContent('process_step3_title')) ?></h3>
                    <p><?= e(getContent('process_step3_desc')) ?></p>
                </div>
            </div>

            <div class="process-step" data-step="4">
                <div class="step-circle">4</div>
                <div class="step-content">
                    <h3><?= e(getContent('process_step4_title')) ?></h3>
                    <p><?= e(getContent('process_step4_desc')) ?></p>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ═══════════════════════════════════════════════════════════
     About Section (Dynamic Stats)
     ═══════════════════════════════════════════════════════════ -->
<section class="about-section" id="about">
    <div class="section-container">
        <div class="about-grid">
            <div class="about-text animate-on-scroll">
                <h2><?= e(getContent('about_title')) ?></h2>
                <p><?= e(getContent('about_text')) ?></p>
            </div>

            <?php if ($showStats): ?>
            <div class="about-stats animate-on-scroll">
                <div class="stat-card">
                    <div class="stat-num"><?= e(getSetting('stat_projects_num', '150+')) ?></div>
                    <div class="stat-label"><?= e(getLocaleSetting('stat_projects_label')) ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-num"><?= e(getSetting('stat_clients_num', '50+')) ?></div>
                    <div class="stat-label"><?= e(getLocaleSetting('stat_clients_label')) ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-num"><?= e(getSetting('stat_years_num', '8+')) ?></div>
                    <div class="stat-label"><?= e(getLocaleSetting('stat_years_label')) ?></div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════
     Digital Marketing Section (Enhanced)
     ═══════════════════════════════════════════════════════════ -->
<?php if ($showMarketing): ?>
<section class="marketing-section" id="marketing">
    <div class="section-container">
        <div class="section-heading animate-on-scroll">
            <h2><?= e(getContent('marketing_title')) ?></h2>
            <p><?= e(getContent('marketing_subtitle')) ?></p>
            <div class="heading-line"></div>
        </div>

        <div class="marketing-grid">
            <!-- SEO -->
            <div class="marketing-card animate-on-scroll">
                <div class="marketing-card-inner">
                    <div class="marketing-orb" style="background: radial-gradient(circle, rgba(59,130,246,0.3), transparent);"></div>
                    <div class="marketing-icon">
                        <?= getIconSvg('search', 'cobalt') ?>
                    </div>
                    <h3><?= e(getContent('marketing_seo_title')) ?></h3>
                    <p><?= e(getContent('marketing_seo_desc')) ?></p>
                </div>
            </div>
            <!-- Social Media -->
            <div class="marketing-card animate-on-scroll">
                <div class="marketing-card-inner">
                    <div class="marketing-orb" style="background: radial-gradient(circle, rgba(139,92,246,0.3), transparent);"></div>
                    <div class="marketing-icon">
                        <?= getIconSvg('share', 'violet') ?>
                    </div>
                    <h3><?= e(getContent('marketing_social_title')) ?></h3>
                    <p><?= e(getContent('marketing_social_desc')) ?></p>
                </div>
            </div>
            <!-- PPC -->
            <div class="marketing-card animate-on-scroll">
                <div class="marketing-card-inner">
                    <div class="marketing-orb" style="background: radial-gradient(circle, rgba(6,182,212,0.3), transparent);"></div>
                    <div class="marketing-icon">
                        <?= getIconSvg('megaphone', 'cyan') ?>
                    </div>
                    <h3><?= e(getContent('marketing_ppc_title')) ?></h3>
                    <p><?= e(getContent('marketing_ppc_desc')) ?></p>
                </div>
            </div>
            <!-- Brand Identity -->
            <div class="marketing-card animate-on-scroll">
                <div class="marketing-card-inner">
                    <div class="marketing-orb" style="background: radial-gradient(circle, rgba(236,72,153,0.3), transparent);"></div>
                    <div class="marketing-icon">
                        <?= getIconSvg('palette', 'pink') ?>
                    </div>
                    <h3><?= e(getContent('marketing_brand_title')) ?></h3>
                    <p><?= e(getContent('marketing_brand_desc')) ?></p>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ═══════════════════════════════════════════════════════════
     Products / Solutions Section
     ═══════════════════════════════════════════════════════════ -->
<?php if ($showProducts && !empty($products)): ?>
<section class="products-section" id="products">
    <div class="section-container">
        <div class="section-heading animate-on-scroll">
            <h2><?= e(getContent('products_title')) ?></h2>
            <p><?= e(getContent('products_subtitle')) ?></p>
            <div class="heading-line"></div>
        </div>

        <?php
        $uniqueCategories = [];
        foreach ($products as $p) {
            $cat = strtolower(trim($p['category']));
            if (!empty($cat) && !in_array($cat, $uniqueCategories)) {
                $uniqueCategories[] = $cat;
            }
        }
        ?>
        <!-- Category tabs -->
        <div class="product-tabs animate-on-scroll">
            <button class="product-tab active" data-category="all"><?= getCurrentLocale() === 'ar' ? 'الكل' : 'All' ?></button>
            <?php foreach($uniqueCategories as $cat): ?>
                <button class="product-tab" data-category="<?= e($cat) ?>">
                    <?php
                        // Optional translation fallback mapping
                        $catLabels = [
                            'website' => ['en' => 'Websites', 'ar' => 'مواقع'],
                            'app' => ['en' => 'Apps', 'ar' => 'تطبيقات'],
                            'maintenance' => ['en' => 'Maintenance', 'ar' => 'صيانة']
                        ];
                        $locale = getCurrentLocale() === 'ar' ? 'ar' : 'en';
                        echo isset($catLabels[$cat]) ? $catLabels[$cat][$locale] : e(ucfirst($cat));
                    ?>
                </button>
            <?php endforeach; ?>
        </div>

        <div class="products-grid">
            <?php foreach ($products as $prod): ?>
            <div class="product-card animate-on-scroll" data-category="<?= e($prod['category']) ?>">
                <div class="product-icon" style="background: linear-gradient(135deg, rgba(<?= getColorRgb($prod['color']) ?>, 0.15), transparent);">
                    <?= getIconSvg($prod['icon'], $prod['color']) ?>
                </div>
                <span class="product-category-badge"><?= e(ucfirst($prod['category'])) ?></span>
                <h3><?= e($prod['title'] ?? '') ?></h3>
                <p><?= e($prod['description'] ?? '') ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ═══════════════════════════════════════════════════════════
     Clients Section
     ═══════════════════════════════════════════════════════════ -->
<?php if ($showClients && !empty($clients)): ?>
<section class="clients-section" id="clients">
    <div class="section-container">
        <div class="section-heading animate-on-scroll">
            <h2><?= e(getContent('clients_title')) ?></h2>
            <div class="heading-line"></div>
        </div>

        <div class="clients-marquee animate-on-scroll">
            <div class="clients-track">
                <?php foreach ($clients as $client): ?>
                <div class="client-card">
                    <?php if (!empty($client['logo_url'])): ?>
                        <img src="<?= e($client['logo_url']) ?>" alt="<?= e($client['name']) ?>" class="client-logo">
                    <?php else: ?>
                        <div class="client-initials"><?= e(mb_substr($client['name'], 0, 2)) ?></div>
                    <?php endif; ?>
                    <span class="client-name"><?= e($client['name']) ?></span>
                </div>
                <?php endforeach; ?>
                <!-- Duplicate for infinite scroll effect -->
                <?php foreach ($clients as $client): ?>
                <div class="client-card">
                    <?php if (!empty($client['logo_url'])): ?>
                        <img src="<?= e($client['logo_url']) ?>" alt="<?= e($client['name']) ?>" class="client-logo">
                    <?php else: ?>
                        <div class="client-initials"><?= e(mb_substr($client['name'], 0, 2)) ?></div>
                    <?php endif; ?>
                    <span class="client-name"><?= e($client['name']) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ═══════════════════════════════════════════════════════════
     Team Members Section
     ═══════════════════════════════════════════════════════════ -->
<?php if ($showTeam && !empty($teamMembers)): ?>
<section class="team-section" id="team">
    <div class="section-container">
        <div class="section-heading animate-on-scroll">
            <h2><?= e(getContent('team_title')) ?></h2>
            <p><?= e(getContent('team_subtitle')) ?></p>
            <div class="heading-line"></div>
        </div>

        <div class="team-grid">
            <?php foreach ($teamMembers as $i => $member): ?>
            <div class="team-card animate-on-scroll" style="animation-delay: <?= $i * 0.1 ?>s;">
                <div class="team-avatar">
                    <?php if (!empty($member['image_url'])): ?>
                        <img src="<?= e($member['image_url']) ?>" alt="<?= e($member['name'] ?? '') ?>">
                    <?php else: ?>
                        <div class="team-avatar-initials">
                            <?= strtoupper(mb_substr($member['name'] ?? 'M', 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                    <div class="team-avatar-ring"></div>
                </div>
                <div class="team-card-body">
                    <h3 class="team-name"><?= e($member['name'] ?? '') ?></h3>
                    <div class="team-role"><?= e($member['role'] ?? '') ?></div>
                    <?php if (!empty($member['bio'])): ?>
                    <p class="team-bio"><?= e($member['bio']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ═══════════════════════════════════════════════════════════
     Testimonials Section
     ═══════════════════════════════════════════════════════════ -->
<?php if ($showTestimonials && !empty($testimonials)): ?>
<section class="testimonials-section" id="testimonials">
    <div class="section-container">
        <div class="section-heading animate-on-scroll">
            <h2><?= e(getContent('testimonials_title')) ?></h2>
            <p><?= e(getContent('testimonials_subtitle')) ?></p>
            <div class="heading-line"></div>
        </div>

        <div class="testimonials-grid">
            <?php foreach ($testimonials as $i => $t): ?>
            <div class="testimonial-card animate-on-scroll" style="animation-delay: <?= $i * 0.1 ?>s;">
                <div class="testimonial-stars">
                    <?php for ($s = 1; $s <= 5; $s++): ?>
                        <span class="star <?= $s <= intval($t['rating']) ? 'filled' : '' ?>">★</span>
                    <?php endfor; ?>
                </div>
                <p class="testimonial-content">"<?= e($t['content'] ?? '') ?>"</p>
                <div class="testimonial-author">
                    <?php if (!empty($t['client_image_url'])): ?>
                        <img src="<?= e($t['client_image_url']) ?>" alt="<?= e($t['client_name'] ?? '') ?>" class="testimonial-avatar">
                    <?php else: ?>
                        <div class="testimonial-avatar-initials">
                            <?= strtoupper(mb_substr($t['client_name'] ?? 'C', 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                    <div class="testimonial-author-info">
                        <div class="testimonial-name"><?= e($t['client_name'] ?? '') ?></div>
                        <?php if (!empty($t['client_company'])): ?>
                        <div class="testimonial-company"><?= e($t['client_company']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ═══════════════════════════════════════════════════════════
     Blog Section (Futuristic Glassmorphism UI)
     ═══════════════════════════════════════════════════════════ -->
<?php if ($showBlogs && !empty($blogs)): ?>
<section class="blog-section" id="blogs" style="position: relative; overflow: hidden; padding: 100px 0;">
    <!-- Abstract neural particles for background -->
    <div class="blog-background-effects" style="position:absolute; inset:0; pointer-events:none; z-index:0;">
        <div style="position:absolute; top:-10%; left:-10%; width:40%; height:40%; background:radial-gradient(circle, rgba(var(--neon-violet-rgb), 0.1) 0%, transparent 70%); filter:blur(100px);"></div>
        <div style="position:absolute; bottom:-10%; right:-10%; width:40%; height:40%; background:radial-gradient(circle, rgba(var(--neon-emerald-rgb), 0.1) 0%, transparent 70%); filter:blur(100px);"></div>
    </div>
    
    <div class="section-container" style="position: relative; z-index: 1;">
        <div class="section-heading animate-on-scroll">
            <h2><?= e(getContent('blog_title')) ?></h2>
            <p><?= e(getContent('blog_subtitle')) ?></p>
            <div class="heading-line" style="margin: 20px auto; width: 60px; height: 3px; background: linear-gradient(90deg, var(--neon-cyan), var(--neon-violet));"></div>
        </div>

        <div class="blog-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; margin-top: 60px;">
            <?php 
            $latestBlogs = array_slice($blogs, 0, 3);
            foreach ($latestBlogs as $i => $blog): 
            ?>
            <div class="blog-card animate-on-scroll" style="animation-delay: <?= $i * 0.1 ?>s; background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255,255,255,0.08); border-radius: 20px; overflow: hidden; backdrop-filter: blur(12px); transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);">
                
                <div class="blog-media" style="position: relative; height: 200px; overflow: hidden; border-bottom: 1px solid rgba(255,255,255,0.05);">
                    <?php if ($blog['media_type'] === 'video' && !empty($blog['media_url'])): ?>
                        <video autoplay loop muted playsinline style="width: 100%; height: 100%; object-fit: cover; opacity: 0.8; transition: transform 0.8s ease;">
                            <source src="<?= baseUrl($blog['media_url']) ?>" type="video/<?= pathinfo($blog['media_url'], PATHINFO_EXTENSION) ?>">
                        </video>
                    <?php elseif ($blog['media_type'] === 'video_link' && !empty($blog['media_url'])): ?>
                        <div style="width: 100%; height: 100%; background: #0a0f18; display:flex; align-items:center; justify-content:center;">
                            <a href="<?= e($blog['media_url']) ?>" target="_blank" style="color:var(--neon-pink); opacity:0.8; transition:transform 0.3s ease;" onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform='scale(1)'">
                                <svg width="50" height="50" viewBox="0 0 24 24" fill="currentColor"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/></svg>
                            </a>
                        </div>
                    <?php else: ?>
                        <img src="<?= !empty($blog['media_url']) ? baseUrl($blog['media_url']) : baseUrl('assets/images/placeholder.webp') ?>" alt="<?= e($blog['title'] ?? '') ?>" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.8s ease; opacity: 0.9;">
                    <?php endif; ?>
                    <div style="position:absolute; top:0; left:0; width:100%; height:100%; background:linear-gradient(to bottom, transparent 60%, rgba(0,0,0,0.4));"></div>
                </div>

                <div class="blog-content" style="padding: 25px;">
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                        <span style="padding: 4px 10px; background: rgba(var(--neon-cyan-rgb),0.1); color: var(--neon-cyan); font-size: 0.65rem; border-radius: 4px; font-weight: 700; letter-spacing: 1px;"><?= strtoupper(date('M d', strtotime($blog['created_at']))) ?></span>
                    </div>
                    <h3 style="font-size: 1.2rem; margin: 0 0 12px 0; color: #fff; line-height: 1.4; font-weight: 600;"><?= e($blog['title'] ?? '') ?></h3>
                    <p style="color: rgba(255,255,255,0.5); font-size: 0.85rem; line-height: 1.6; margin-bottom: 25px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?= e($blog['description'] ?? '') ?></p>
                    
                    <a href="<?= baseUrl('blog/' . e($blog['slug'])) ?>" class="blog-nav-link" style="color: var(--neon-emerald); font-size: 0.8rem; font-weight: 800; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; letter-spacing: 1px;">
                        READ PROJECT
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if (count($blogs) > 3): ?>
        <div style="text-align: center; margin-top: 50px;">
            <a href="<?= baseUrl('blogs') ?>" class="btn-ghost" style="border: 1px solid rgba(var(--neon-cyan-rgb), 0.3); padding: 12px 30px; border-radius: 30px; color: var(--neon-cyan); text-decoration: none; font-weight: 600; transition: all 0.3s ease;">
                SEE MORE PROJECTS
                <svg style="margin-left: 8px; vertical-align: middle;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
        </div>
        <?php endif; ?>
    </div>
    
    <style>
        .blog-card:hover { transform: translateY(-12px); background: rgba(255,255,255,0.05); border-color: rgba(var(--neon-emerald-rgb), 0.4); box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); }
        .blog-card:hover img, .blog-card:hover video { transform: scale(1.1); }
        .blog-nav-link:hover { color: #fff; gap: 10px; }
    </style>
</section>
<?php endif; ?>

<!-- ═══════════════════════════════════════════════════════════
     Booking Section (Dynamic Fields)
     ═══════════════════════════════════════════════════════════ -->
<?php if (getSetting('show_booking_section', '1') === '1'): ?>
<section class="booking-section" id="booking">
    <div class="section-container">
        <div class="section-heading animate-on-scroll">
            <h2><?= e(getContent('booking_title')) ?></h2>
            <p><?= e(getContent('booking_subtitle')) ?></p>
            <div class="heading-line"></div>
        </div>

        <div class="booking-card animate-on-scroll">
            <?php if (!empty($_SESSION['booking_error'])): ?>
                <div class="alert alert-error"><?= e($_SESSION['booking_error']) ?></div>
                <?php unset($_SESSION['booking_error']); ?>
            <?php endif; ?>

            <form action="<?= baseUrl('booking/submit') ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

                <?php
                $fields = $bookingFields;
                $totalFields = count($fields);
                $i = 0;
                while ($i < $totalFields):
                    $field = $fields[$i];
                    $nextField = ($i + 1 < $totalFields) ? $fields[$i + 1] : null;

                    // Pair short fields side by side
                    $isPairable = in_array($field['field_type'], ['text','email','tel','date','number','select']);
                    $nextIsPairable = $nextField && in_array($nextField['field_type'], ['text','email','tel','date','number','select']);

                    if ($isPairable && $nextIsPairable):
                ?>
                <div class="form-row">
                    <?php renderBookingField($field, $services); ?>
                    <?php renderBookingField($nextField, $services); $i++; ?>
                </div>
                <?php else: ?>
                    <?php renderBookingField($field, $services); ?>
                <?php
                    endif;
                    $i++;
                endwhile;
                ?>

                <button type="submit" class="btn-neon">
                    <?= t('booking_submit') ?>
                    <svg style="display:inline; vertical-align:middle; margin-<?= isRTL() ? 'right' : 'left' ?>: 8px;" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2L11 13"/><path d="M22 2L15 22 11 13 2 9z"/></svg>
                </button>
            </form>
        </div>
    </div>
</section>
<?php endif; ?>

<?php
/* ── Helper: Render a single booking form field ─────────── */
function renderBookingField(array $field, array $services): void {
    $name = e($field['field_name']);
    $label = e($field['label'] ?? $field['field_name']);
    $placeholder = e($field['placeholder'] ?? '');
    $req = $field['is_required'] ? 'required' : '';
    $type = $field['field_type'];
    ?>
    <div class="form-group">
        <label for="field_<?= $name ?>"><?= $label ?></label>
        <?php if ($type === 'textarea'): ?>
            <textarea id="field_<?= $name ?>" name="fields[<?= $name ?>]" class="form-input" rows="4" placeholder="<?= $placeholder ?>" <?= $req ?>></textarea>
        <?php elseif ($type === 'select'): ?>
            <select id="field_<?= $name ?>" name="fields[<?= $name ?>]" class="form-input modern-select" <?= $req ?>>
                <option value=""><?= $placeholder ?></option>
                <?php if ($field['field_name'] === 'service'): ?>
                    <?php foreach ($services as $s): ?>
                        <option value="<?= e($s['title'] ?? '') ?>"><?= e($s['title'] ?? '') ?></option>
                    <?php endforeach; ?>
                <?php elseif (!empty($field['options'])): ?>
                    <?php foreach (explode(',', $field['options']) as $opt): ?>
                        <option value="<?= e(trim($opt)) ?>"><?= e(trim($opt)) ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        <?php elseif ($type === 'tel'): ?>
            <div style="display: flex; gap: 8px;">
                <select name="country_code" class="form-input" style="width: 105px; min-width: 105px; flex-shrink:0; padding-left: 10px; padding-right: 10px; font-size: 0.9rem;">
<option value="+93">🇦🇫 +93</option>
<option value="+358">🇦🇽 +358</option>
<option value="+355">🇦🇱 +355</option>
<option value="+213">🇩🇿 +213</option>
<option value="+1-684">🇦🇸 +1</option>
<option value="+376">🇦🇩 +376</option>
<option value="+244">🇦🇴 +244</option>
<option value="+1-264">🇦🇮 +1</option>
<option value="+672">🇦🇶 +672</option>
<option value="+1-268">🇦🇬 +1</option>
<option value="+54">🇦🇷 +54</option>
<option value="+374">🇦🇲 +374</option>
<option value="+297">🇦🇼 +297</option>
<option value="+61">🇦🇺 +61</option>
<option value="+43">🇦🇹 +43</option>
<option value="+994">🇦🇿 +994</option>
<option value="+1-242">🇧🇸 +1</option>
<option value="+973">🇧🇭 +973</option>
<option value="+880">🇧🇩 +880</option>
<option value="+1-246">🇧🇧 +1</option>
<option value="+375">🇧🇾 +375</option>
<option value="+32">🇧🇪 +32</option>
<option value="+501">🇧🇿 +501</option>
<option value="+229">🇧🇯 +229</option>
<option value="+1-441">🇧🇲 +1</option>
<option value="+975">🇧🇹 +975</option>
<option value="+591">🇧🇴 +591</option>
<option value="+387">🇧🇦 +387</option>
<option value="+267">🇧🇼 +267</option>
<option value="+55">🇧🇷 +55</option>
<option value="+246">🇮🇴 +246</option>
<option value="+1-284">🇻🇬 +1</option>
<option value="+673">🇧🇳 +673</option>
<option value="+359">🇧🇬 +359</option>
<option value="+226">🇧🇫 +226</option>
<option value="+257">🇧🇮 +257</option>
<option value="+855">🇰🇭 +855</option>
<option value="+237">🇨🇲 +237</option>
<option value="+1">🇨🇦 +1</option>
<option value="+238">🇨🇻 +238</option>
<option value="+1-345">🇰🇾 +1</option>
<option value="+236">🇨🇫 +236</option>
<option value="+235">🇹🇩 +235</option>
<option value="+56">🇨🇱 +56</option>
<option value="+86">🇨🇳 +86</option>
<option value="+61">🇨🇽 +61</option>
<option value="+61">🇨🇨 +61</option>
<option value="+57">🇨🇴 +57</option>
<option value="+269">🇰🇲 +269</option>
<option value="+243">🇨🇩 +243</option>
<option value="+242">🇨🇬 +242</option>
<option value="+682">🇨🇰 +682</option>
<option value="+506">🇨🇷 +506</option>
<option value="+225">🇨🇮 +225</option>
<option value="+385">🇭🇷 +385</option>
<option value="+53">🇨🇺 +53</option>
<option value="+599">🇨🇼 +599</option>
<option value="+357">🇨🇾 +357</option>
<option value="+420">🇨🇿 +420</option>
<option value="+45">🇩🇰 +45</option>
<option value="+253">🇩🇯 +253</option>
<option value="+1-767">🇩🇲 +1</option>
<option value="+1-809">🇩🇴 +1</option>
<option value="+593">🇪🇨 +593</option>
<option value="+20">🇪🇬 +20</option>
<option value="+503">🇸🇻 +503</option>
<option value="+240">🇬🇶 +240</option>
<option value="+291">🇪🇷 +291</option>
<option value="+372">🇪🇪 +372</option>
<option value="+251">🇪🇹 +251</option>
<option value="+500">🇫🇰 +500</option>
<option value="+298">🇫🇴 +298</option>
<option value="+679">🇫🇯 +679</option>
<option value="+358">🇫🇮 +358</option>
<option value="+33">🇫🇷 +33</option>
<option value="+594">🇬🇫 +594</option>
<option value="+689">🇵🇫 +689</option>
<option value="+241">🇬🇦 +241</option>
<option value="+220">🇬🇲 +220</option>
<option value="+995">🇬🇪 +995</option>
<option value="+49">🇩🇪 +49</option>
<option value="+233">🇬🇭 +233</option>
<option value="+350">🇬🇮 +350</option>
<option value="+30">🇬🇷 +30</option>
<option value="+299">🇬🇱 +299</option>
<option value="+1-473">🇬🇩 +1</option>
<option value="+590">🇬🇵 +590</option>
<option value="+1-671">🇬🇺 +1</option>
<option value="+502">🇬🇹 +502</option>
<option value="+44-1481">🇬🇬 +44</option>
<option value="+224">🇬🇳 +224</option>
<option value="+245">🇬🇼 +245</option>
<option value="+592">🇬🇾 +592</option>
<option value="+509">🇭🇹 +509</option>
<option value="+504">🇭🇳 +504</option>
<option value="+852">🇭🇰 +852</option>
<option value="+36">🇭🇺 +36</option>
<option value="+354">🇮🇸 +354</option>
<option value="+91">🇮🇳 +91</option>
<option value="+62">🇮🇩 +62</option>
<option value="+98">🇮🇷 +98</option>
<option value="+964">🇮🇶 +964</option>
<option value="+353">🇮🇪 +353</option>
<option value="+44-1624">🇮🇲 +44</option>
<option value="+972">🇮🇱 +972</option>
<option value="+39">🇮🇹 +39</option>
<option value="+1-876">🇯🇲 +1</option>
<option value="+81">🇯🇵 +81</option>
<option value="+44-1534">🇯🇪 +44</option>
<option value="+962">🇯🇴 +962</option>
<option value="+7">🇰🇿 +7</option>
<option value="+254">🇰🇪 +254</option>
<option value="+686">🇰🇮 +686</option>
<option value="+383">🇽🇰 +383</option>
<option value="+965">🇰🇼 +965</option>
<option value="+996">🇰🇬 +996</option>
<option value="+856">🇱🇦 +856</option>
<option value="+371">🇱🇻 +371</option>
<option value="+961">🇱🇧 +961</option>
<option value="+266">🇱🇸 +266</option>
<option value="+231">🇱🇷 +231</option>
<option value="+218">🇱🇾 +218</option>
<option value="+423">🇱🇮 +423</option>
<option value="+370">🇱🇹 +370</option>
<option value="+352">🇱🇺 +352</option>
<option value="+853">🇲🇴 +853</option>
<option value="+389">🇲🇰 +389</option>
<option value="+261">🇲🇬 +261</option>
<option value="+265">🇲🇼 +265</option>
<option value="+60">🇲🇾 +60</option>
<option value="+960">🇲🇻 +960</option>
<option value="+223">🇲🇱 +223</option>
<option value="+356">🇲🇹 +356</option>
<option value="+692">🇲🇭 +692</option>
<option value="+596">🇲🇶 +596</option>
<option value="+222">🇲🇷 +222</option>
<option value="+230">🇲🇺 +230</option>
<option value="+262">🇾🇹 +262</option>
<option value="+52">🇲🇽 +52</option>
<option value="+691">🇫🇲 +691</option>
<option value="+373">🇲🇩 +373</option>
<option value="+377">🇲🇨 +377</option>
<option value="+976">🇲🇳 +976</option>
<option value="+382">🇲🇪 +382</option>
<option value="+1-664">🇲🇸 +1</option>
<option value="+212">🇲🇦 +212</option>
<option value="+258">🇲🇿 +258</option>
<option value="+95">🇲🇲 +95</option>
<option value="+264">🇳🇦 +264</option>
<option value="+674">🇳🇷 +674</option>
<option value="+977">🇳🇵 +977</option>
<option value="+31">🇳🇱 +31</option>
<option value="+687">🇳🇨 +687</option>
<option value="+64">🇳🇿 +64</option>
<option value="+505">🇳🇮 +505</option>
<option value="+227">🇳🇪 +227</option>
<option value="+234">🇳🇬 +234</option>
<option value="+683">🇳🇺 +683</option>
<option value="+672">🇳🇫 +672</option>
<option value="+850">🇰🇵 +850</option>
<option value="+1-670">🇲🇵 +1</option>
<option value="+47">🇳🇴 +47</option>
<option value="+968">🇴🇲 +968</option>
<option value="+92">🇵🇰 +92</option>
<option value="+680">🇵🇼 +680</option>
<option value="+970">🇵🇸 +970</option>
<option value="+507">🇵🇦 +507</option>
<option value="+675">🇵🇬 +675</option>
<option value="+595">🇵🇾 +595</option>
<option value="+51">🇵🇪 +51</option>
<option value="+63">🇵🇭 +63</option>
<option value="+64">🇵🇳 +64</option>
<option value="+48">🇵🇱 +48</option>
<option value="+351">🇵🇹 +351</option>
<option value="+1-787">🇵🇷 +1</option>
<option value="+974">🇶🇦 +974</option>
<option value="+242">🇨🇬 +242</option>
<option value="+262">🇷🇪 +262</option>
<option value="+40">🇷🇴 +40</option>
<option value="+7">🇷🇺 +7</option>
<option value="+250">🇷🇼 +250</option>
<option value="+590">🇧🇱 +590</option>
<option value="+290">🇸🇭 +290</option>
<option value="+1-869">🇰🇳 +1</option>
<option value="+1-758">🇱🇨 +1</option>
<option value="+590">🇲🇫 +590</option>
<option value="+508">🇵🇲 +508</option>
<option value="+1-784">🇻🇨 +1</option>
<option value="+685">🇼🇸 +685</option>
<option value="+378">🇸🇲 +378</option>
<option value="+239">🇸🇹 +239</option>
<option value="+966">🇸🇦 +966</option>
<option value="+221">🇸🇳 +221</option>
<option value="+381">🇷🇸 +381</option>
<option value="+248">🇸🇨 +248</option>
<option value="+232">🇸🇱 +232</option>
<option value="+65">🇸🇬 +65</option>
<option value="+1-721">🇸🇽 +1</option>
<option value="+421">🇸🇰 +421</option>
<option value="+386">🇸🇮 +386</option>
<option value="+677">🇸🇧 +677</option>
<option value="+252">🇸🇴 +252</option>
<option value="+27">🇿🇦 +27</option>
<option value="+82">🇰🇷 +82</option>
<option value="+211">🇸🇸 +211</option>
<option value="+34">🇪🇸 +34</option>
<option value="+94">🇱🇰 +94</option>
<option value="+249">🇸🇩 +249</option>
<option value="+597">🇸🇷 +597</option>
<option value="+47">🇸🇯 +47</option>
<option value="+268">🇸🇿 +268</option>
<option value="+46">🇸🇪 +46</option>
<option value="+41">🇨🇭 +41</option>
<option value="+963">🇸🇾 +963</option>
<option value="+886">🇹🇼 +886</option>
<option value="+992">🇹🇯 +992</option>
<option value="+255">🇹🇿 +255</option>
<option value="+66">🇹🇭 +66</option>
<option value="+228">🇹🇬 +228</option>
<option value="+690">🇹🇰 +690</option>
<option value="+676">🇹🇴 +676</option>
<option value="+1-868">🇹🇹 +1</option>
<option value="+216">🇹🇳 +216</option>
<option value="+90">🇹🇷 +90</option>
<option value="+993">🇹🇲 +993</option>
<option value="+1-649">🇹🇨 +1</option>
<option value="+688">🇹🇻 +688</option>
<option value="+1-340">🇻🇮 +1</option>
<option value="+256">🇺🇬 +256</option>
<option value="+380">🇺🇦 +380</option>
<option value="+971" selected>🇦🇪 +971</option>
<option value="+44">🇬🇧 +44</option>
<option value="+1">🇺🇸 +1</option>
<option value="+598">🇺🇾 +598</option>
<option value="+998">🇺🇿 +998</option>
<option value="+678">🇻🇺 +678</option>
<option value="+379">🇻🇦 +379</option>
<option value="+58">🇻🇪 +58</option>
<option value="+84">🇻🇳 +84</option>
<option value="+681">🇼🇫 +681</option>
<option value="+212">🇪🇭 +212</option>
<option value="+967">🇾🇪 +967</option>
<option value="+260">🇿🇲 +260</option>
<option value="+263">🇿🇼 +263</option>
                </select>
                <input type="tel" id="field_<?= $name ?>" name="fields[<?= $name ?>]" class="form-input" placeholder="<?= $placeholder ?>" <?= $req ?> style="flex:1;">
            </div>
        <?php else: ?>
            <input type="<?= e($type) ?>" id="field_<?= $name ?>" name="fields[<?= $name ?>]" class="form-input" placeholder="<?= $placeholder ?>" <?= $req ?>>
        <?php endif; ?>
    </div>
    <?php
}
?>
