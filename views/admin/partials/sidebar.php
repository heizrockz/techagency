<!-- Mobile Toggle Button -->
<button class="admin-mobile-toggle" id="adminMobileToggle" aria-label="Toggle Menu">
    <span class="toggle-icon">☰</span>
</button>

<div class="admin-sidebar" id="adminSidebar">
    <div class="admin-brand" style="display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; padding: 1.5rem 1rem;">
        <?php $logo = getSetting('site_logo'); if(!empty($logo)): ?>
            <img src="<?= baseUrl($logo) ?>" alt="<?= APP_NAME ?>" style="max-height: 48px; margin-bottom: 0.5rem; border-radius: 4px;">
            <div style="font-size: 0.9rem; font-weight: 700; color: #fff; letter-spacing: 0.5px;"><?= APP_NAME ?></div>
        <?php else: ?>
            <div style="font-size: 1.25rem; font-weight: 800; color: var(--theme-gold);"><?= APP_NAME ?></div>
        <?php endif; ?>
    </div>
    <nav class="admin-nav">
        <a href="<?= baseUrl('admin/dashboard') ?>" class="<?= $currentPage === 'dashboard' ? 'active' : '' ?>">📊 <?= t('admin_dashboard') ?></a>
        <a href="<?= baseUrl('admin/inbox') ?>" class="<?= $currentPage === 'inbox' ? 'active' : '' ?>">📥 Inbox</a>
        <a href="<?= baseUrl('admin/visitors') ?>" class="<?= $currentPage === 'visitors' ? 'active' : '' ?>">🌍 Visitors</a>
        <a href="<?= baseUrl('admin/bookings') ?>" class="<?= $currentPage === 'bookings' ? 'active' : '' ?>">📋 <?= t('admin_bookings') ?></a>

        <!-- CRM Group -->
        <?php $crmActive = in_array($currentPage, ['contacts', 'marketing', 'invoices', 'crm_pipeline', 'crm_products']); ?>
        <div class="sidebar-group <?= $crmActive ? 'open' : '' ?>">
            <div class="sidebar-group-toggle" onclick="this.parentElement.classList.toggle('open')">
                <span>💼 CRM</span>
                <span class="sidebar-arrow">▸</span>
            </div>
            <div class="sidebar-group-items">
                <a href="<?= baseUrl('admin/crm_pipeline') ?>" class="<?= $currentPage === 'crm_pipeline' ? 'active' : '' ?>">📈 Pipeline (Leads)</a>
                <a href="<?= baseUrl('admin/crm_products') ?>" class="<?= $currentPage === 'crm_products' ? 'active' : '' ?>">📦 Products / Items</a>
                <a href="<?= baseUrl('admin/contacts') ?>" class="<?= $currentPage === 'contacts' ? 'active' : '' ?>">📇 Contacts</a>
                <a href="<?= baseUrl('admin/marketing') ?>" class="<?= $currentPage === 'marketing' ? 'active' : '' ?>">✉️ Email Marketing</a>
                <a href="<?= baseUrl('admin/invoices') ?>" class="<?= $currentPage === 'invoices' ? 'active' : '' ?>">🧾 Invoices / Quotes</a>
            </div>
        </div>

        <!-- Content Group -->
        <?php $contentActive = in_array($currentPage, ['services', 'products', 'clients', 'portfolio', 'team', 'testimonials', 'content', 'seo']); ?>
        <div class="sidebar-group <?= $contentActive ? 'open' : '' ?>">
            <div class="sidebar-group-toggle" onclick="this.parentElement.classList.toggle('open')">
                <span>📄 Content</span>
                <span class="sidebar-arrow">▸</span>
            </div>
            <div class="sidebar-group-items">
                <a href="<?= baseUrl('admin/services') ?>" class="<?= $currentPage === 'services' ? 'active' : '' ?>">✨ Services</a>
                <a href="<?= baseUrl('admin/products') ?>" class="<?= $currentPage === 'products' ? 'active' : '' ?>">📦 Products/Ideas</a>
                <a href="<?= baseUrl('admin/clients') ?>" class="<?= $currentPage === 'clients' ? 'active' : '' ?>">🤝 Our Clients</a>
                <a href="<?= baseUrl('admin/portfolio') ?>" class="<?= $currentPage === 'portfolio' ? 'active' : '' ?>">🎨 Portfolio</a>
                <a href="<?= baseUrl('admin/team') ?>" class="<?= $currentPage === 'team' ? 'active' : '' ?>">👥 Our Team</a>
                <a href="<?= baseUrl('admin/testimonials') ?>" class="<?= $currentPage === 'testimonials' ? 'active' : '' ?>">💬 Testimonials</a>
                <a href="<?= baseUrl('admin/content') ?>" class="<?= $currentPage === 'content' ? 'active' : '' ?>">✏️ <?= t('admin_content') ?></a>
                <a href="<?= baseUrl('admin/seo') ?>" class="<?= $currentPage === 'seo' ? 'active' : '' ?>">🔍 <?= t('admin_seo') ?></a>
                <a href="<?= baseUrl('admin/blogs') ?>" class="<?= $currentPage === 'blogs' ? 'active' : '' ?>">📰 Blogs</a>
            </div>
        </div>

        <!-- Configuration Group -->
        <?php $configActive = in_array($currentPage, ['booking_fields', 'settings', 'chatbot', 'translations']); ?>
        <div class="sidebar-group <?= $configActive ? 'open' : '' ?>">
            <div class="sidebar-group-toggle" onclick="this.parentElement.classList.toggle('open')">
                <span>⚙️ Configuration</span>
                <span class="sidebar-arrow">▸</span>
            </div>
            <div class="sidebar-group-items">
                <a href="<?= baseUrl('admin/booking-fields') ?>" class="<?= $currentPage === 'booking_fields' ? 'active' : '' ?>">📝 Form Builder</a>
                <a href="<?= baseUrl('admin/settings') ?>" class="<?= $currentPage === 'settings' ? 'active' : '' ?>">⚙️ Site Settings</a>
                <a href="<?= baseUrl('admin/chatbot') ?>" class="<?= $currentPage === 'chatbot' ? 'active' : '' ?>">🤖 Chatbot Builder</a>
                <a href="<?= baseUrl('admin/translations') ?>" class="<?= $currentPage === 'translations' ? 'active' : '' ?>">🌐 Translations</a>
                <a href="<?= baseUrl('admin/sitemap') ?>" class="<?= $currentPage === 'sitemap' ? 'active' : '' ?>">🗺️ Sitemap</a>
            </div>
        </div>
        
        <div style="margin-top: 30px; padding-top: 15px; border-top: 1px solid rgba(255,255,255,0.05);">
            <a href="<?= baseUrl('/') ?>" target="_blank" rel="noopener noreferrer" style="color: var(--theme-gold); display: flex; align-items: center; justify-content: space-between;">
                <span>🌐 View Live Site</span>
                <span style="font-size: 0.8rem;">↗</span>
            </a>
        </div>

        <!-- User Profile Section -->
        <?php
            $emoji = '👤';
            $displayName = getAdminUser() ?? 'Admin';
            try {
                $db = getDB();
                $adminStmt = $db->prepare('SELECT avatar_emoji, full_name FROM admins WHERE id = ?');
                $adminStmt->execute([$_SESSION['admin_id'] ?? 0]);
                $adminData = $adminStmt->fetch();
                if ($adminData) {
                    $emoji = $adminData['avatar_emoji'] ?? '👤';
                    $displayName = $adminData['full_name'] ?? $displayName;
                }
            } catch (Exception $e) {
                // Columns may not exist yet — use defaults
            }
        ?>
        <div style="margin-top:15px; padding:12px 15px; background: rgba(255,255,255,0.03); border-radius: 10px; border: 1px solid var(--glass-border);">
            <div style="display:flex; align-items:center; gap:10px; cursor:pointer;" onclick="document.getElementById('profile-menu').classList.toggle('show')">
                <span style="font-size:1.5rem;"><?= $emoji ?></span>
                <div style="flex:1; min-width:0;">
                    <div style="font-weight:600; font-size:0.85rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?= e($displayName) ?></div>
                    <div style="font-size:0.7rem; color:var(--text-muted);">Administrator</div>
                </div>
                <span style="font-size:0.7rem; color:var(--text-muted);">▼</span>
            </div>
            <div id="profile-menu" style="display:none; margin-top:10px; padding-top:10px; border-top:1px solid var(--glass-border);">
                <a href="<?= baseUrl('admin/profile') ?>" style="display:block; padding:6px 0; font-size:0.85rem; color:var(--neon-cyan);">⚙️ Edit Profile</a>
                <a href="<?= baseUrl('admin/logout') ?>" style="display:block; padding:6px 0; font-size:0.85rem; color:var(--neon-pink);">🚪 Logout</a>
            </div>
        </div>
        <style>
            #profile-menu.show { display: block !important; }
        </style>
    </nav>
</div>

<!-- Mobile Overlay -->
<div class="admin-mobile-overlay" id="adminOverlay"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.getElementById('adminMobileToggle');
    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.getElementById('adminOverlay');
    
    if (toggle && sidebar && overlay) {
        const toggleMenu = () => {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
            document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
        };

        toggle.addEventListener('click', toggleMenu);
        overlay.addEventListener('click', toggleMenu);

        // Close sidebar when clicking nav links on mobile
        const navLinks = sidebar.querySelectorAll('nav a');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 768) {
                    toggleMenu();
                }
            });
        });
    }
});
</script>
