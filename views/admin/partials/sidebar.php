<!-- Mobile Toggle Button -->
<button class="admin-mobile-toggle" id="adminMobileToggle" aria-label="Toggle Menu">
    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
</button>

<div class="admin-sidebar" id="adminSidebar">
    <!-- Brand -->
    <div class="admin-brand">
        <?php $logo = getSetting('site_logo'); if(!empty($logo)): ?>
            <img src="<?= baseUrl($logo) ?>" alt="<?= APP_NAME ?>" class="admin-brand-logo">
        <?php endif; ?>
        <span class="admin-brand-name"><?= APP_NAME ?></span>
    </div>

    <!-- Navigation -->
    <nav class="admin-nav">
        <!-- Main Section Label -->
        <div class="sidebar-section-label">Main</div>

        <a href="<?= baseUrl('admin/dashboard') ?>" class="<?= $currentPage === 'dashboard' ? 'active' : '' ?>">
            <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg></span>
            <span><?= t('admin_dashboard') ?></span>
        </a>
        <a href="<?= baseUrl('admin/inbox') ?>" class="<?= $currentPage === 'inbox' ? 'active' : '' ?>">
            <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"/><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/></svg></span>
            <span>Inbox</span>
        </a>
        <a href="<?= baseUrl('admin/visitors') ?>" class="<?= $currentPage === 'visitors' ? 'active' : '' ?>">
            <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
            <span>Visitors</span>
        </a>
        <a href="<?= baseUrl('admin/bookings') ?>" class="<?= $currentPage === 'bookings' ? 'active' : '' ?>">
            <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><path d="M8 14h.01"/><path d="M12 14h.01"/><path d="M16 14h.01"/><path d="M8 18h.01"/><path d="M12 18h.01"/></svg></span>
            <span><?= t('admin_bookings') ?></span>
        </a>

        <!-- CRM Group -->
        <div class="sidebar-section-label">CRM</div>
        <?php $crmActive = in_array($currentPage, ['contacts', 'marketing', 'invoices', 'crm_pipeline', 'crm_products']); ?>
        <div class="sidebar-group <?= $crmActive ? 'open' : '' ?>">
            <div class="sidebar-group-toggle" onclick="this.parentElement.classList.toggle('open')">
                <div class="sidebar-group-toggle-left">
                    <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg></span>
                    <span>CRM</span>
                </div>
                <span class="sidebar-chevron"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></span>
            </div>
            <div class="sidebar-group-items">
                <a href="<?= baseUrl('admin/crm_pipeline') ?>" class="<?= $currentPage === 'crm_pipeline' ? 'active' : '' ?>">
                    <span class="nav-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg></span>
                    <span>Pipeline</span>
                </a>
                <a href="<?= baseUrl('admin/crm_products') ?>" class="<?= $currentPage === 'crm_products' ? 'active' : '' ?>">
                    <span class="nav-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg></span>
                    <span>Products</span>
                </a>
                <a href="<?= baseUrl('admin/contacts') ?>" class="<?= $currentPage === 'contacts' ? 'active' : '' ?>">
                    <span class="nav-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
                    <span>Contacts</span>
                </a>
                <a href="<?= baseUrl('admin/marketing') ?>" class="<?= $currentPage === 'marketing' ? 'active' : '' ?>">
                    <span class="nav-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></span>
                    <span>Email Marketing</span>
                </a>
                <a href="<?= baseUrl('admin/invoices') ?>" class="<?= $currentPage === 'invoices' ? 'active' : '' ?>">
                    <span class="nav-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg></span>
                    <span>Invoices</span>
                </a>
            </div>
        </div>

        <!-- Content Group -->
        <div class="sidebar-section-label">Content</div>
        <?php $contentActive = in_array($currentPage, ['services', 'products', 'clients', 'portfolio', 'team', 'testimonials', 'content', 'seo', 'blogs']); ?>
        <div class="sidebar-group <?= $contentActive ? 'open' : '' ?>">
            <div class="sidebar-group-toggle" onclick="this.parentElement.classList.toggle('open')">
                <div class="sidebar-group-toggle-left">
                    <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg></span>
                    <span>Content</span>
                </div>
                <span class="sidebar-chevron"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></span>
            </div>
            <div class="sidebar-group-items">
                <a href="<?= baseUrl('admin/services') ?>" class="<?= $currentPage === 'services' ? 'active' : '' ?>">
                    <span class="nav-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></span>
                    <span>Services</span>
                </a>
                <a href="<?= baseUrl('admin/products') ?>" class="<?= $currentPage === 'products' ? 'active' : '' ?>">
                    <span class="nav-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg></span>
                    <span>Products/Ideas</span>
                </a>
                <a href="<?= baseUrl('admin/clients') ?>" class="<?= $currentPage === 'clients' ? 'active' : '' ?>">
                    <span class="nav-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg></span>
                    <span>Our Clients</span>
                </a>
                <a href="<?= baseUrl('admin/portfolio') ?>" class="<?= $currentPage === 'portfolio' ? 'active' : '' ?>">
                    <span class="nav-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="2.18" ry="2.18"/><line x1="7" y1="2" x2="7" y2="22"/><line x1="17" y1="2" x2="17" y2="22"/><line x1="2" y1="12" x2="22" y2="12"/><line x1="2" y1="7" x2="7" y2="7"/><line x1="2" y1="17" x2="7" y2="17"/><line x1="17" y1="7" x2="22" y2="7"/><line x1="17" y1="17" x2="22" y2="17"/></svg></span>
                    <span>Portfolio</span>
                </a>
                <a href="<?= baseUrl('admin/team') ?>" class="<?= $currentPage === 'team' ? 'active' : '' ?>">
                    <span class="nav-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
                    <span>Our Team</span>
                </a>
                <a href="<?= baseUrl('admin/testimonials') ?>" class="<?= $currentPage === 'testimonials' ? 'active' : '' ?>">
                    <span class="nav-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></span>
                    <span>Testimonials</span>
                </a>
                <a href="<?= baseUrl('admin/content') ?>" class="<?= $currentPage === 'content' ? 'active' : '' ?>">
                    <span class="nav-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg></span>
                    <span><?= t('admin_content') ?></span>
                </a>
                <a href="<?= baseUrl('admin/seo') ?>" class="<?= $currentPage === 'seo' ? 'active' : '' ?>">
                    <span class="nav-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></span>
                    <span><?= t('admin_seo') ?></span>
                </a>
                <a href="<?= baseUrl('admin/blogs') ?>" class="<?= $currentPage === 'blogs' ? 'active' : '' ?>">
                    <span class="nav-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg></span>
                    <span>Blogs</span>
                </a>
            </div>
        </div>

        <!-- Configuration Group -->
        <div class="sidebar-section-label">System</div>
        <?php $configActive = in_array($currentPage, ['booking_fields', 'settings', 'chatbot', 'translations', 'sitemap']); ?>
        <div class="sidebar-group <?= $configActive ? 'open' : '' ?>">
            <div class="sidebar-group-toggle" onclick="this.parentElement.classList.toggle('open')">
                <div class="sidebar-group-toggle-left">
                    <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg></span>
                    <span>Configuration</span>
                </div>
                <span class="sidebar-chevron"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></span>
            </div>
            <div class="sidebar-group-items">
                <a href="<?= baseUrl('admin/booking-fields') ?>" class="<?= $currentPage === 'booking_fields' ? 'active' : '' ?>">
                    <span class="nav-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg></span>
                    <span>Form Builder</span>
                </a>
                <a href="<?= baseUrl('admin/settings') ?>" class="<?= $currentPage === 'settings' ? 'active' : '' ?>">
                    <span class="nav-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg></span>
                    <span>Site Settings</span>
                </a>
                <a href="<?= baseUrl('admin/chatbot') ?>" class="<?= $currentPage === 'chatbot' ? 'active' : '' ?>">
                    <span class="nav-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="10" rx="2"/><circle cx="12" cy="5" r="2"/><path d="M12 7v4"/><line x1="8" y1="16" x2="8" y2="16"/><line x1="16" y1="16" x2="16" y2="16"/></svg></span>
                    <span>Chatbot Builder</span>
                </a>
                <a href="<?= baseUrl('admin/translations') ?>" class="<?= $currentPage === 'translations' ? 'active' : '' ?>">
                    <span class="nav-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg></span>
                    <span>Translations</span>
                </a>
                <a href="<?= baseUrl('admin/sitemap') ?>" class="<?= $currentPage === 'sitemap' ? 'active' : '' ?>">
                    <span class="nav-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/><line x1="8" y1="2" x2="8" y2="18"/><line x1="16" y1="6" x2="16" y2="22"/></svg></span>
                    <span>Sitemap</span>
                </a>
            </div>
        </div>

        <!-- View Live Site -->
        <div class="sidebar-divider"></div>
        <a href="<?= baseUrl('/') ?>" target="_blank" rel="noopener noreferrer" class="sidebar-live-link">
            <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg></span>
            <span>View Live Site</span>
        </a>

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
        <div class="sidebar-user-card">
            <div class="sidebar-user-info" onclick="document.getElementById('profile-menu').classList.toggle('show')">
                <span class="sidebar-user-avatar"><?= $emoji ?></span>
                <div class="sidebar-user-details">
                    <div class="sidebar-user-name"><?= e($displayName) ?></div>
                    <div class="sidebar-user-role">Administrator</div>
                </div>
                <span class="sidebar-user-chevron"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg></span>
            </div>
            <div id="profile-menu" class="sidebar-user-menu">
                <a href="<?= baseUrl('admin/profile') ?>">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Edit Profile
                </a>
                <a href="<?= baseUrl('admin/logout') ?>" class="sidebar-logout-link">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Logout
                </a>
            </div>
        </div>
    </nav>
</div>

<!-- Mobile Overlay -->
<div class="admin-mobile-overlay" id="adminOverlay"></div>

<style>
    #profile-menu.show { display: flex !important; }
</style>

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
