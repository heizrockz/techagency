<?php
if (!defined('APP_NAME')) die('Direct access prevented');
?>
<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <title>Product Reviews — <?= APP_NAME ?></title>
    <?php require __DIR__ . '/partials/_head_assets.php'; ?>
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<div class="admin-layout flex w-full h-screen overflow-hidden">
    <?php $currentPage = 'app-reviews'; require __DIR__ . '/partials/sidebar.php'; ?>
    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-20 flex items-center justify-between px-8 bg-glass-bg border-b border-white/5 shrink-0 backdrop-blur-xl sticky top-0 z-[100]">
            <div class="relative flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center border border-amber-500/20">
                    <i class="ph ph-star text-2xl text-amber-500"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white tracking-tight">Product Reviews</h1>
                    <p class="text-[10px] text-white/40 uppercase tracking-widest font-black">User Feedback Moderation</p>
                </div>
            </div>
            <?php require __DIR__ . '/partials/_topbar.php'; ?>
        </header>

        <main class="flex-1 overflow-y-auto p-8 crm-main-scroll bg-[#0b0e14]">
             <?php if ($flash = getFlash()): ?>
                <div class="mb-8 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl flex items-center gap-3">
                    <i class="ph ph-check-circle text-emerald-500"></i>
                    <p class="text-emerald-500 font-medium"><?= e($flash) ?></p>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 gap-6">
                <?php foreach($reviews as $r): ?>
                    <div class="admin-card overflow-hidden">
                        <div class="p-6 border-b border-white/5 flex items-start justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-full bg-white/5 flex items-center justify-center font-bold text-white/20"><?= substr($r['name'], 0, 1) ?></div>
                                <div>
                                    <h3 class="font-bold text-white"><?= e($r['name']) ?> <span class="text-xs font-normal text-white/40">on <?= e($r['product_name']) ?></span></h3>
                                    <div class="flex items-center gap-1 text-amber-500 mt-1">
                                        <?php for($i=1; $i<=5; $i++): ?>
                                            <i class="ph-fill ph-star <?= $i <= $r['rating'] ? '' : 'opacity-20' ?>"></i>
                                        <?php endfor; ?>
                                        <span class="ml-2 text-[10px] text-white/40 uppercase tracking-widest"><?= date('M d, Y', strtotime($r['created_at'])) ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest <?= $r['status'] === 'approved' ? 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20' : ($r['status'] === 'pending' ? 'bg-amber-500/10 text-amber-500 border border-amber-500/20' : 'bg-pink-500/10 text-pink-500 border border-pink-500/20') ?>">
                                    <?= $r['status'] ?>
                                </span>
                                <a href="<?= baseUrl('admin/app-reviews?action=delete&id='.$r['id']) ?>" onclick="return confirm('Delete review?')" class="w-8 h-8 rounded-lg bg-pink-500/10 text-pink-500 flex items-center justify-center border border-pink-500/20 hover:bg-pink-500/20"><i class="ph ph-trash"></i></a>
                            </div>
                        </div>
                        <div class="p-6 bg-white/[0.02]">
                            <p class="text-sm text-white/60 leading-relaxed italic mb-6">"<?= e($r['comment']) ?>"</p>
                            
                            <form method="POST" action="<?= baseUrl('admin/app-reviews') ?>" class="space-y-4">
                                <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black uppercase tracking-widest text-white/40">Admin Reply</label>
                                    <textarea name="admin_reply" rows="2" class="form-input text-xs" placeholder="Write your response..."><?= e($r['admin_reply'] ?? '') ?></textarea>
                                </div>
                                <div class="flex items-center justify-between">
                                    <select name="status" class="form-input !w-auto text-xs py-1.5 px-3">
                                        <option value="pending" <?= $r['status'] === 'pending' ? 'selected' : '' ?>>Pending Review</option>
                                        <option value="approved" <?= $r['status'] === 'approved' ? 'selected' : '' ?>>Approve Review</option>
                                        <option value="rejected" <?= $r['status'] === 'rejected' ? 'selected' : '' ?>>Reject Review</option>
                                    </select>
                                    <button type="submit" class="px-6 py-2 bg-violet-500 text-white text-xs font-bold rounded-lg hover:bg-violet-600 transition-all">Update Review</button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endforeach; if(empty($reviews)): ?>
                    <div class="text-center py-20 bg-white/5 rounded-3xl border border-dashed border-white/10">
                        <i class="ph ph-star-half text-6xl text-white/10 mb-4"></i>
                        <p class="text-white/40">No reviews to moderate yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>
<?php require __DIR__ . '/partials/_footer_assets.php'; ?>
</body>
</html>
