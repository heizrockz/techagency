<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Settings — <?= APP_NAME ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= baseUrl('assets/css/style.css') ?>">
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<div class="admin-layout">
    <?php $currentPage = 'settings'; require __DIR__ . '/partials/sidebar.php'; ?>
    <div class="admin-main">
        <div class="admin-header">
            <h1>⚙️ Site Settings</h1>
        </div>
        
        <?php if ($saved): ?>
            <div class="alert alert-success">Settings saved successfully.</div>
        <?php endif; ?>

        <form method="POST" action="<?= baseUrl('admin/settings') ?>" enctype="multipart/form-data">
            <div class="admin-card" style="margin-bottom: 20px;">
                <h3>Section Toggles</h3>
                <p style="color:var(--text-muted); font-size:0.85rem; margin-bottom:15px;">Show or hide entire sections on the user-facing home page.</p>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <label style="cursor: pointer; display: flex; align-items: center; gap: 10px;">
                        <input type="checkbox" name="settings[show_clients_section]" <?= ($settings['show_clients_section']['setting_value'] ?? '1') === '1' ? 'checked' : '' ?>> Show "Our Clients" Section
                    </label>
                    <label style="cursor: pointer; display: flex; align-items: center; gap: 10px;">
                        <input type="checkbox" name="settings[show_products_section]" <?= ($settings['show_products_section']['setting_value'] ?? '1') === '1' ? 'checked' : '' ?>> Show "Products / Ideas" Section
                    </label>
                    <label style="cursor: pointer; display: flex; align-items: center; gap: 10px;">
                        <input type="checkbox" name="settings[show_stats_section]" <?= ($settings['show_stats_section']['setting_value'] ?? '1') === '1' ? 'checked' : '' ?>> Show "Stats" in About Section
                    </label>
                    <label style="cursor: pointer; display: flex; align-items: center; gap: 10px;">
                        <input type="checkbox" name="settings[show_marketing_section]" <?= ($settings['show_marketing_section']['setting_value'] ?? '1') === '1' ? 'checked' : '' ?>> Show "Digital Marketing" Section
                    </label>
                    <label style="cursor: pointer; display: flex; align-items: center; gap: 10px;">
                        <input type="checkbox" name="settings[show_team]" <?= ($settings['show_team']['setting_value'] ?? '1') === '1' ? 'checked' : '' ?>> Show "Our Team" Section
                    </label>
                    <label style="cursor: pointer; display: flex; align-items: center; gap: 10px;">
                        <input type="checkbox" name="settings[show_testimonials]" <?= ($settings['show_testimonials']['setting_value'] ?? '1') === '1' ? 'checked' : '' ?>> Show "Testimonials" Section
                    </label>
                </div>
            </div>

            <div class="admin-card" style="margin-bottom: 20px;">
                <h3>Branding</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 15px;">
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Site Name</label>
                        <input type="text" name="settings[site_name]" class="form-input" value="<?= e($settings['site_name']['setting_value'] ?? 'Mico Sage') ?>">
                    </div>
                    <div class="form-group" style="grid-column: 1 / -1; display: flex; align-items: center; gap: 20px;">
                        <div>
                            <label>Company Logo</label>
                            <input type="file" name="site_logo" class="form-input" accept="image/*">
                            <small style="color:var(--text-muted); display:block; margin-top:5px;">Upload a new logo (PNG, JPG, SVG). It will replace the text Site Name in the navigation bar.</small>
                        </div>
                        <?php if(!empty($settings['site_logo']['setting_value'])): ?>
                            <div style="background: rgba(255,255,255,0.05); padding: 10px; border-radius: 8px; border: 1px solid var(--glass-border);">
                                <img src="<?= baseUrl($settings['site_logo']['setting_value']) ?>" alt="Current Logo" style="max-height: 50px;">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="admin-card" style="margin-bottom: 20px;">
                <h3>Company Stats</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-top: 15px;">
                    <div class="form-group">
                        <label>Projects Number</label>
                        <input type="text" name="settings[stat_projects_num]" class="form-input" value="<?= e($settings['stat_projects_num']['setting_value'] ?? '150+') ?>">
                        <input type="text" name="settings[stat_projects_label_en]" class="form-input" style="margin-top:5px;" value="<?= e($settings['stat_projects_label_en']['setting_value'] ?? '') ?>" placeholder="Label (EN)">
                        <input type="text" name="settings[stat_projects_label_ar]" class="form-input" style="margin-top:5px;" value="<?= e($settings['stat_projects_label_ar']['setting_value'] ?? '') ?>" placeholder="Label (AR)" dir="rtl">
                    </div>
                    <div class="form-group">
                        <label>Clients Number</label>
                        <input type="text" name="settings[stat_clients_num]" class="form-input" value="<?= e($settings['stat_clients_num']['setting_value'] ?? '50+') ?>">
                        <input type="text" name="settings[stat_clients_label_en]" class="form-input" style="margin-top:5px;" value="<?= e($settings['stat_clients_label_en']['setting_value'] ?? '') ?>" placeholder="Label (EN)">
                        <input type="text" name="settings[stat_clients_label_ar]" class="form-input" style="margin-top:5px;" value="<?= e($settings['stat_clients_label_ar']['setting_value'] ?? '') ?>" placeholder="Label (AR)" dir="rtl">
                    </div>
                    <div class="form-group">
                        <label>Years Number</label>
                        <input type="text" name="settings[stat_years_num]" class="form-input" value="<?= e($settings['stat_years_num']['setting_value'] ?? '8+') ?>">
                        <input type="text" name="settings[stat_years_label_en]" class="form-input" style="margin-top:5px;" value="<?= e($settings['stat_years_label_en']['setting_value'] ?? '') ?>" placeholder="Label (EN)">
                        <input type="text" name="settings[stat_years_label_ar]" class="form-input" style="margin-top:5px;" value="<?= e($settings['stat_years_label_ar']['setting_value'] ?? '') ?>" placeholder="Label (AR)" dir="rtl">
                    </div>
                </div>
            </div>

        <!-- Contact Group -->
        <div class="admin-card" style="margin-bottom: 20px;">
            <h3 style="color: var(--neon-cyan);">📞 Contact Information</h3>
            <p style="color:var(--text-muted); font-size:0.85rem; margin-bottom:15px;">Used for the floating call button and contact page.</p>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label>Contact Phone (for Call Button)</label>
                    <input type="text" name="settings[contact_phone]" class="form-input" value="<?= e($settings['contact_phone']['setting_value'] ?? '') ?>" placeholder="+1 234 567 8900" dir="ltr">
                </div>
                <div class="form-group">
                    <label>Contact Email</label>
                    <input type="email" name="settings[contact_email]" class="form-input" value="<?= e($settings['contact_email']['setting_value'] ?? '') ?>" placeholder="hello@company.com" dir="ltr">
                </div>
                <div class="form-group">
                    <label>WhatsApp Number</label>
                    <input type="text" name="settings[whatsapp_number]" class="form-input" value="<?= e($settings['whatsapp_number']['setting_value'] ?? '') ?>" placeholder="+971501234567" dir="ltr">
                </div>
                <div class="form-group">
                    <label>Contact Location / Address</label>
                    <input type="text" name="settings[contact_location]" class="form-input" value="<?= e($settings['contact_location']['setting_value'] ?? '') ?>" placeholder="Dubai, UAE">
                </div>
            </div>
        </div>

            <!-- Social Media Group -->
            <div class="admin-card" style="margin-bottom: 20px;">
                <h3 style="color: var(--neon-cyan);">🌐 Social Media Links</h3>
                <p style="color:var(--text-muted); font-size:0.85rem; margin-bottom:15px;">Displayed in the website footer.</p>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label>Facebook URL</label>
                        <input type="url" name="settings[social_facebook]" class="form-input" value="<?= e($settings['social_facebook']['setting_value'] ?? '') ?>" placeholder="https://facebook.com/..." dir="ltr">
                    </div>
                    <div class="form-group">
                        <label>Twitter / X URL</label>
                        <input type="url" name="settings[social_twitter]" class="form-input" value="<?= e($settings['social_twitter']['setting_value'] ?? '') ?>" placeholder="https://twitter.com/..." dir="ltr">
                    </div>
                    <div class="form-group">
                        <label>Instagram URL</label>
                        <input type="url" name="settings[social_instagram]" class="form-input" value="<?= e($settings['social_instagram']['setting_value'] ?? '') ?>" placeholder="https://instagram.com/..." dir="ltr">
                    </div>
                    <div class="form-group">
                        <label>LinkedIn URL</label>
                        <input type="url" name="settings[social_linkedin]" class="form-input" value="<?= e($settings['social_linkedin']['setting_value'] ?? '') ?>" placeholder="https://linkedin.com/..." dir="ltr">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-primary" style="margin-bottom: 40px; font-size: 1.1rem; padding: 12px 30px;">Save All Settings</button>
        </form>
    </div>
</div>
</body>
</html>
