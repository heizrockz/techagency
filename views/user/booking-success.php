<!DOCTYPE html>
<html lang="<?= e($locale) ?>" dir="<?= $dir ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($seo['title'] ?? APP_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= baseUrl('assets/css/style.css') ?>">
    <style>
        .success-wrapper {
            position: relative;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            animation: scaleUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .success-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(16, 185, 129, 0.2);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 60px 50px;
            text-align: center;
            box-shadow: 0 30px 80px rgba(0,0,0,0.5), 0 0 60px rgba(16, 185, 129, 0.15);
            max-width: 480px;
            width: 100%;
        }

        .success-icon-container {
            width: 120px;
            height: 120px;
            margin: 0 auto 30px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .success-icon-bg {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(251, 191, 36, 0.2));
            border-radius: 50%;
            animation: pulseBg 2s infinite alternate;
            z-index: 0;
        }

        .checkmark {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: block;
            stroke-width: 3;
            stroke: #fff;
            stroke-miterlimit: 10;
            box-shadow: inset 0px 0px 0px var(--theme-primary, #10b981);
            animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both;
            position: relative;
            z-index: 1;
        }

        .checkmark-circle {
            stroke-dasharray: 166;
            stroke-dashoffset: 166;
            stroke-width: 2;
            stroke-miterlimit: 10;
            stroke: var(--theme-primary, #10b981);
            fill: none;
            animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
        }

        .checkmark-check {
            transform-origin: 50% 50%;
            stroke-dasharray: 48;
            stroke-dashoffset: 48;
            animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
        }

        @keyframes stroke {
            100% { stroke-dashoffset: 0; }
        }

        @keyframes scale {
            0%, 100% { transform: none; }
            50% { transform: scale3d(1.1, 1.1, 1); }
        }

        @keyframes fill {
            100% { box-shadow: inset 0px 0px 0px 60px var(--theme-primary, #10b981); }
        }

        @keyframes scaleUp {
            0% { opacity: 0; transform: scale(0.8) translateY(30px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }

        @keyframes pulseBg {
            0% { transform: scale(0.9); opacity: 0.6; }
            100% { transform: scale(1.1); opacity: 1; filter: blur(10px); }
        }

        .success-card h1 {
            font-size: 2.2rem;
            font-weight: 800;
            margin-bottom: 15px;
            background: linear-gradient(135deg, #10b981, #fBBF24);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: fadeInUp 0.8s ease backwards 0.3s;
        }

        .success-card p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 40px;
            animation: fadeInUp 0.8s ease backwards 0.4s;
        }

        .btn-home {
            display: inline-block;
            padding: 16px 40px;
            background: linear-gradient(135deg, #10b981, #fBBF24);
            border-radius: 12px;
            color: white;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            animation: fadeInUp 0.8s ease backwards 0.5s;
            box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);
        }

        .btn-home:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(16, 185, 129, 0.5);
            color: white;
        }

        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body dir="<?= $dir ?>">
    <div class="nebula-bg"></div>
    <div class="nebula-orb nebula-orb-1"></div>
    <div class="nebula-orb nebula-orb-2"></div>

    <div class="success-wrapper">
        <div class="success-card">
            <div class="success-icon-container">
                <div class="success-icon-bg"></div>
                <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                    <circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none"/>
                    <path class="checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                </svg>
            </div>
            <h1><?= getCurrentLocale() === 'ar' ? 'تم بنجاح!' : 'Success!' ?></h1>
            <p><?= t('booking_success') ?></p>
            <a href="<?= baseUrl('/') ?>" class="btn-home">
                <?= t('nav_home') ?>
            </a>
        </div>
    </div>
</body>
</html>
