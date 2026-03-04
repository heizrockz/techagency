<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('admin_login_title') ?> — <?= APP_NAME ?></title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons (Standard Bold Phosphor) -->
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/bold/style.css">
    
    <!-- Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --bg-deep: #05070a;
            --card-surface: #0f1218;
            --accent-brand: #10b981;
            --accent-soft: rgba(16, 185, 129, 0.1);
            --border-muted: rgba(255, 255, 255, 0.05);
            --input-field-bg: #161b22;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-deep);
            color: #ececec;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            overflow: hidden;
        }

        /* Subtler mesh background for professional feel */
        .mesh-canvas {
            position: fixed;
            inset: 0;
            background: 
                radial-gradient(circle at 10% 10%, rgba(16, 185, 129, 0.04) 0%, transparent 35%),
                radial-gradient(circle at 90% 90%, rgba(6, 182, 212, 0.04) 0%, transparent 35%);
            z-index: -1;
        }

        .login-card {
            width: 100%;
            max-width: 440px;
            background: var(--card-surface);
            border: 1px solid var(--border-muted);
            border-radius: 32px;
            padding: 64px 48px;
            box-shadow: 0 40px 100px -20px rgba(0, 0, 0, 0.8);
            position: relative;
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 40px 24px;
                border-radius: 24px;
                max-width: 90%;
            }
            .app-title { font-size: 24px; }
            .logo-box { width: 64px; height: 64px; margin-bottom: 20px; }
            .input-stack { margin-top: 32px; }
            .submit-trigger { padding: 16px; margin-top: 32px; }
        }

        /* Refined border gradient */
        .login-card::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 32px;
            padding: 1px;
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.1), transparent 50%, rgba(255, 255, 255, 0.03));
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
        }

        .logo-box {
            width: 80px;
            height: 80px;
            margin: 0 auto 28px;
            background: #1c2128;
            border: 1px solid var(--border-muted);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .app-title {
            font-family: 'Outfit', sans-serif;
            font-size: 32px;
            font-weight: 800;
            letter-spacing: -0.015em;
            text-align: center;
            margin: 0;
            color: #fff;
            background: linear-gradient(to bottom, #fff, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .app-tagline {
            text-align: center;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4em;
            color: #64748b;
            margin-top: 10px;
            opacity: 0.6;
        }

        .input-stack {
            margin-top: 48px;
        }

        .label-text {
            display: block;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: #475569;
            margin-bottom: 12px;
            margin-left: 2px;
        }

        .field-container {
            position: relative;
        }

        .field-container i {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #334155;
            font-size: 20px;
            transition: all 0.3s;
        }

        .input-box {
            width: 100%;
            background: var(--input-field-bg);
            border: 1px solid var(--border-muted);
            border-radius: 18px;
            padding: 18px 20px 18px 56px;
            color: #fff;
            font-size: 15px;
            font-weight: 500;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .input-box:focus {
            outline: none;
            border-color: rgba(16, 185, 129, 0.4);
            background: #1e242c;
            box-shadow: 0 0 0 4px var(--accent-soft);
        }

        .input-box:focus + i {
            color: var(--accent-brand);
        }

        .action-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 32px;
            padding: 0 4px;
        }

        .toggle-group {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            color: #64748b;
            transition: color 0.3s;
        }

        .toggle-group:hover {
            color: #94a3b8;
        }

        .switch-ui {
            position: relative;
            width: 40px;
            height: 22px;
            background: #232a35;
            border-radius: 12px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .switch-ui::after {
            content: '';
            position: absolute;
            left: 4px;
            top: 4px;
            width: 14px;
            height: 14px;
            background: #475569;
            border-radius: 50%;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        input[type="checkbox"]:checked + .switch-ui {
            background: var(--accent-brand);
        }

        input[type="checkbox"]:checked + .switch-ui::after {
            left: 22px;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }

        .submit-trigger {
            width: 100%;
            background: #fff;
            color: #000;
            border: none;
            border-radius: 18px;
            padding: 20px;
            font-size: 15px;
            font-weight: 800;
            margin-top: 40px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            letter-spacing: 0.01em;
        }

        .submit-trigger:hover {
            background: #e2e8f0;
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
        }

        .submit-trigger:active {
            transform: translateY(0);
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.08);
            border: 1px solid rgba(239, 68, 68, 0.15);
            color: #f87171;
            padding: 16px 20px;
            border-radius: 18px;
            font-size: 13px;
            font-weight: 600;
            margin-top: 32px;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: bounceIn 0.5s;
        }

        .copyright-tag {
            text-align: center;
            font-size: 11px;
            font-weight: 600;
            color: #334155;
            margin-top: 48px;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }

        @keyframes bounceIn {
            0% { opacity: 0; transform: scale(0.95); }
            50% { transform: scale(1.02); }
            100% { opacity: 1; transform: scale(1); }
        }
    </style>
</head>
<body>
    <div class="mesh-canvas"></div>

    <div class="login-card">
        <div class="logo-box">
            <?php $logo = getSetting('site_logo'); if(!empty($logo)): ?>
                <img src="<?= baseUrl($logo) ?>" alt="<?= APP_NAME ?>" class="w-12 h-12 object-contain filter drop-shadow-lg">
            <?php else: ?>
                <i class="ph-bold ph-lightning text-white text-3xl"></i>
            <?php endif; ?>
        </div>
        <h1 class="app-title"><?= APP_NAME ?></h1>
        <p class="app-tagline">Strategic Administrator Gateway</p>

        <?php if (!empty($error)): ?>
            <div class="alert-error">
                <i class="ph-bold ph-warning-circle text-xl"></i>
                <span><?= e($error) ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= baseUrl('admin/login') ?>">
            <div class="input-stack">
                <div class="form-unit">
                    <label for="username" class="label-text"><?= t('admin_username') ?></label>
                    <div class="field-container">
                        <input type="text" id="username" name="username" class="input-box" placeholder="Username" required autofocus>
                        <i class="ph-bold ph-user-circle"></i>
                    </div>
                </div>

                <div class="form-unit" style="margin-top: 28px;">
                    <label for="password" class="label-text"><?= t('admin_password') ?></label>
                    <div class="field-container">
                        <input type="password" id="password" name="password" class="input-box" placeholder="Password" required>
                        <i class="ph-bold ph-lock-key"></i>
                    </div>
                </div>
            </div>

            <div class="action-options">
                <label class="toggle-group">
                    <input type="checkbox" name="remember" value="1" style="display: none;">
                    <div class="switch-ui"></div>
                    <span>Keep me signed in</span>
                </label>
            </div>

            <button type="submit" class="submit-trigger">
                <span>Sign in to Terminal</span>
                <i class="ph-bold ph-arrow-right text-lg"></i>
            </button>
        </form>

        <div class="copyright-tag">
            &copy; <?= date('Y') ?> &bull; System Nexus Core
        </div>
    </div>
</body>
</html>
