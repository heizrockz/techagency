<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <title><?= t('admin_content') ?> — <?= APP_NAME ?></title>
    <?php require __DIR__ . '/partials/_head_assets.php'; ?>
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">

<div class="admin-layout flex w-full h-screen overflow-hidden">
    <?php $currentPage = 'content'; require __DIR__ . '/partials/sidebar.php'; ?>

    <div class="flex-1 flex flex-col min-w-0 bg-[#0b0e14]">
        <header class="h-20 flex items-center justify-between px-8 bg-glass-bg border-b border-white/5 shrink-0 backdrop-blur-xl sticky top-0 z-[100]">
            <div class="flex flex-col">
                <div class="text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] mb-1 hidden sm:block">Content Architecture</div>
                <h1 class="text-xl font-black text-white tracking-tight flex items-center gap-3 group">
                    <span class="text-neon-cyan drop-shadow-[0_0_8px_rgba(6,182,212,0.4)]"><?= t('admin_content') ?></span>
                    <span class="opacity-20 translate-y-px hidden sm:inline">/</span>
                    <span class="text-sm tracking-widest text-slate-400 uppercase font-black hidden sm:inline">Matrix Nodes</span>
                </h1>
            </div>
            <?php require __DIR__ . '/partials/_topbar.php'; ?>
        </header>

        <?php if ($saved): ?>
            <div class="alert alert-success"><?= t('admin_saved') ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= baseUrl('admin/content') ?>">
            <?php foreach ($contents as $key => $locales): ?>
                <div class="content-section">
                    <h3><?= e($key) ?></h3>
                    <div class="content-locale-grid">
                        <?php foreach (SUPPORTED_LOCALES as $loc): ?>
                            <div class="admin-form-group">
                                <label>
                                    <?= $loc === 'en' ? '🇬🇧 English' : '🇸🇦 Arabic' ?>
                                </label>
                                <?php
                                    $val = $locales[$loc] ?? '';
                                    $isLong = strlen($val) > 100;
                                ?>
                                <?php if ($isLong): ?>
                                    <textarea name="content[<?= e($key) ?>][<?= $loc ?>]" rows="3" dir="<?= $loc === 'ar' ? 'rtl' : 'ltr' ?>"><?= e($val) ?></textarea>
                                <?php else: ?>
                                    <input type="text" name="content[<?= e($key) ?>][<?= $loc ?>]" value="<?= e($val) ?>" dir="<?= $loc === 'ar' ? 'rtl' : 'ltr' ?>">
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <button type="submit" class="btn-admin-save"><?= t('admin_save') ?></button>
        </form>
    </div>
</div>

<style>
    .content-locale-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    .content-section {
        background: rgba(255,255,255,0.02);
        border: 1px solid rgba(255,255,255,0.05);
        border-radius: 1rem;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }
    .content-section h3 {
        color: #06b6d4;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 1rem;
        opacity: 0.5;
    }
    .admin-form-group label {
        display: block;
        font-size: 0.7rem;
        color: rgba(255,255,255,0.4);
        margin-bottom: 0.5rem;
    }
    .admin-form-group input, .admin-form-group textarea {
        width: 100%;
        background: rgba(0,0,0,0.2);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 0.75rem;
        padding: 0.75rem 1rem;
        color: white;
        font-size: 0.85rem;
        transition: all 0.3s;
    }
    .admin-form-group input:focus, .admin-form-group textarea:focus {
        border-color: #06b6d4;
        box-shadow: 0 0 0 1px rgba(6,182,212,0.2);
        outline: none;
    }
    .btn-admin-save {
        background: #06b6d4;
        color: black;
        font-weight: 700;
        padding: 0.75rem 2rem;
        border-radius: 0.75rem;
        margin: 2rem;
        cursor: pointer;
        transition: all 0.3s;
    }
    .btn-admin-save:hover { background: #22d3ee; transform: translateY(-1px); }

    @media screen and (max-width: 1024px) {
        .content-locale-grid { grid-template-columns: 1fr; }
        header { padding: 0 1.5rem !important; }
        form { padding: 1.5rem !important; }
    }
</style>
</body>
</html>
