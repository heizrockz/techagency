<?php
$currentPage = 'profile';
$emojiList = ['👤','😎','🧑‍💻','👩‍💼','🦁','🐺','🦅','🔥','⚡','💎','🎯','🚀','🧠','🎨','🌟','👑','🤖','🦊','🐱','🎵'];
?>
<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile — <?= APP_NAME ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= baseUrl('assets/css/style.css') ?>">
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<div class="admin-layout">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>
    
    <div class="admin-main">
        <div class="admin-header">
            <h1 style="color: var(--neon-cyan); margin:0;">⚙️ My Profile</h1>
        </div>

        <?php if ($saved): ?>
            <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid var(--theme-primary); color: var(--theme-primary); padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                ✅ Profile updated successfully!
            </div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div style="background: rgba(244, 63, 94, 0.1); border: 1px solid #f43f5e; color: #f43f5e; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                ❌ <?= e($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= baseUrl('admin/profile') ?>">
            <div class="admin-card" style="margin-bottom: 25px;">
                <h3 style="color: var(--neon-cyan); margin-bottom: 20px;">Personal Information</h3>
                
                <div class="profile-main-grid">
                    <!-- Avatar Picker -->
                    <div style="text-align: center;">
                        <div style="width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, var(--neon-cyan), var(--neon-violet)); display: flex; align-items: center; justify-content: center; font-size: 2.5rem; margin-bottom: 10px;">
                            <?= $admin['avatar_emoji'] ?? '👤' ?>
                        </div>
                        <label style="font-size: 0.8rem; color: var(--text-muted);">Choose Avatar</label>
                        <div class="admin-grid-2" style="max-width: 200px; margin-top: 8px;">
                            <?php foreach ($emojiList as $em): ?>
                                <label style="cursor: pointer;">
                                    <input type="radio" name="avatar_emoji" value="<?= $em ?>" style="display:none;" <?= ($admin['avatar_emoji'] ?? '👤') === $em ? 'checked' : '' ?>>
                                    <span style="font-size: 1.3rem; padding: 4px; border-radius: 6px; display: inline-block; border: 2px solid transparent; transition: all 0.2s;" class="emoji-pick"><?= $em ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Info Fields -->
                    <div>
                        <div class="admin-grid-2">
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text" name="full_name" value="<?= e($admin['full_name'] ?? '') ?>" class="form-input" placeholder="Your full name">
                            </div>
                            <div class="form-group">
                                <label>Username</label>
                                <input type="text" value="<?= e($admin['username'] ?? '') ?>" class="form-input" readonly style="opacity: 0.6;">
                                <small style="color: var(--text-muted);">Cannot be changed</small>
                            </div>
                        </div>

                        <div class="admin-grid-2" style="margin-top: 15px;">
                            <div class="form-group">
                                <label>Recovery Email</label>
                                <input type="email" name="recovery_email" value="<?= e($admin['recovery_email'] ?? '') ?>" class="form-input" placeholder="backup@email.com">
                            </div>
                            <div class="form-group">
                                <label>Recovery Phone</label>
                                <input type="text" name="recovery_phone" value="<?= e($admin['recovery_phone'] ?? '') ?>" class="form-input" placeholder="+971 50 xxx xxxx">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="admin-card" style="margin-bottom: 25px;">
                <h3 style="color: var(--theme-gold); margin-bottom: 20px;">🔐 Change Password</h3>
                <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 15px;">Leave blank to keep your current password.</p>
                <div class="admin-grid-2">
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="new_password" class="form-input" placeholder="••••••••" autocomplete="new-password">
                    </div>
                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <input type="password" name="confirm_password" class="form-input" placeholder="••••••••" autocomplete="new-password">
                    </div>
                </div>
            </div>

            <div style="text-align: right;">
                <button type="submit" class="btn-primary">💾 Update Profile</button>
            </div>
        </form>
    </div>
</div>

<style>
    input[type="radio"]:checked + .emoji-pick {
        border-color: var(--neon-cyan) !important;
        background: rgba(0, 255, 200, 0.1);
        box-shadow: 0 0 10px rgba(0, 255, 200, 0.2);
    }
</style>
</body>
</html>
