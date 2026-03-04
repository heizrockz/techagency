<?php
// views/admin/partials/_topbar.php
require_once __DIR__ . '/../../../includes/db.php';
$db = getDB();

// Fetch unread notifications
$notifications = getRecentNotifications(10);
$unreadCount = getUnreadNotificationCount();

$emoji = '👤';
$displayName = getAdminUser() ?? 'Admin';
$roleName = 'Administrator';
try {
    $adminStmt = $db->prepare('SELECT avatar_emoji, full_name, role, recovery_email, permissions FROM admins WHERE id = ?');
    $adminStmt->execute([$_SESSION['admin_id'] ?? 0]);
    $adminData = $adminStmt->fetch();
    if ($adminData) {
        $emoji = $adminData['avatar_emoji'] ?? '👤';
        $displayName = $adminData['full_name'] ?? $displayName;
        $roleName = $adminData['role'] === 'super_admin' ? 'Super Admin' : 'Administrator';
        
        // Sync role, email and permissions to session
        $_SESSION['admin_role'] = $adminData['role'] ?? 'standard';
        $_SESSION['admin_email'] = $adminData['recovery_email'] ?? '';
        $_SESSION['admin_permissions'] = json_decode($adminData['permissions'] ?? '[]', true);
    }
} catch (Exception $e) {}
?>

<div class="flex items-center gap-2 lg:gap-4 relative z-[60]">
    <!-- Global Search Terminal -->
    <?php if(isset($showTopbarSearch) && $showTopbarSearch): ?>
    <div class="hidden lg:flex items-center gap-4 bg-black/40 border border-white/5 rounded-2xl px-5 py-2.5 w-72 group focus-within:w-96 focus-within:border-neon-cyan/40 transition-all duration-500 shadow-inner">
        <i class="ph ph-magnifying-glass text-slate-600 group-focus-within:text-neon-cyan transition-colors"></i>
        <form action="" method="GET" class="flex-1">
            <?php if(isset($topbarSearchHiddenFields)): foreach($topbarSearchHiddenFields as $name => $val): ?>
                <input type="hidden" name="<?= e($name) ?>" value="<?= e($val) ?>">
            <?php endforeach; endif; ?>
            <input type="text" name="search" id="<?= $topbarSearchId ?? 'globalSearch' ?>" value="<?= e($search ?? '') ?>" 
                   placeholder="<?= $topbarSearchPlaceholder ?? 'SCAN DATA VECTORS...' ?>" 
                   class="bg-transparent border-none text-[10px] font-black text-white placeholder-slate-800 outline-none w-full uppercase tracking-[0.2em]">
        </form>
    </div>
    <?php endif; ?>

    <!-- Notifications Dropdown -->
    <div class="relative group" id="notificationDropdown">
        <button type="button" class="w-10 h-10 rounded-full bg-white/5 border border-white/10 text-slate-400 hover:text-white flex items-center justify-center transition-colors relative" onclick="toggleNotifMenu(event)">
            <i class="ph ph-bell"></i>
            <?php if($unreadCount > 0): ?>
            <span class="absolute top-0 right-0 w-3 h-3 bg-red-500 rounded-full border-2 border-[#1a2333]"></span>
            <?php endif; ?>
        </button>
        <div id="notifMenu" class="hidden absolute right-0 top-12 w-80 bg-[#1e293b] border border-white/10 rounded-xl shadow-2xl overflow-hidden shadow-[0_10px_40px_rgba(0,0,0,0.5)]">
            <div class="p-3 border-b border-white/5 flex justify-between items-center bg-[#182130]">
                <span class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-2"><i class="ph ph-bell-ringing text-primary"></i> Notifications</span>
                <?php if($unreadCount > 0): ?>
                <form action="<?= baseUrl('admin/notifications') ?>" method="POST" class="inline">
                    <input type="hidden" name="action" value="mark_all_read">
                    <button type="submit" class="text-[10px] text-primary hover:text-white transition-colors">Mark all read</button>
                </form>
                <?php endif; ?>
            </div>
            <div class="max-h-80 overflow-y-auto">
                <?php if(empty($notifications)): ?>
                    <div class="p-8 text-center text-slate-500 text-xs flex flex-col items-center gap-2">
                        <i class="ph ph-bell-slash text-2xl opacity-50"></i>
                        No new notifications
                    </div>
                <?php else: ?>
                    <?php foreach($notifications as $n): ?>
                        <a href="<?= htmlspecialchars(baseUrl($n['link'] ?? '#')) ?>" class="block p-3 border-b border-white/5 hover:bg-white/5 transition-colors <?= $n['is_read'] ? 'opacity-60' : 'bg-white/[0.02]' ?>">
                            <div class="text-white text-sm font-semibold mb-1 flex items-center justify-between gap-2">
                                <span class="flex items-center gap-1.5">
                                    <?php if($n['type'] === 'booking'): ?><i class="ph-fill ph-calendar-check text-emerald-400"></i>
                                    <?php elseif($n['type'] === 'chat'): ?><i class="ph-fill ph-chat-circle-dots text-blue-400"></i>
                                    <?php elseif($n['type'] === 'login'): ?><i class="ph-fill ph-shield-check text-amber-400"></i>
                                    <?php elseif($n['type'] === 'visit'): ?><i class="ph-fill ph-users-three text-purple-400"></i>
                                    <?php else: ?><i class="ph-fill ph-info text-slate-400"></i><?php endif; ?>
                                    <?= htmlspecialchars($n['title']) ?>
                                </span>
                                <?php if(!$n['is_read']): ?><span class="w-1.5 h-1.5 rounded-full bg-primary flex-shrink-0"></span><?php endif; ?>
                            </div>
                            <div class="text-[11px] text-slate-400 line-clamp-2 leading-relaxed"><?= htmlspecialchars($n['content'] ?? $n['message'] ?? '') ?></div>
                            <div class="text-[9px] text-slate-500 font-medium uppercase tracking-widest mt-2"><?= date('M d, H:i', strtotime($n['created_at'])) ?></div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <a href="<?= baseUrl('admin/notifications') ?>" class="block w-full p-2.5 text-center text-[11px] text-primary bg-black/20 hover:bg-black/40 transition-colors font-bold uppercase tracking-widest">Show All Notifications</a>
        </div>
    </div>

    <!-- User Profile Dropdown -->
    <div class="relative group" id="userProfileDropdown">
        <div class="flex items-center gap-2.5 cursor-pointer bg-white/5 hover:bg-white/10 border border-white/10 rounded-full pl-1.5 pr-2 lg:pr-4 py-1.5 transition-all" onclick="toggleProfileMenu(event)">
            <span class="w-8 h-8 rounded-full bg-gradient-to-br from-primary/30 to-primary/10 border border-primary/20 flex items-center justify-center text-sm shadow-inner"><?= $emoji ?></span>
            <div class="hidden lg:flex flex-col">
                <span class="text-[11px] font-bold text-white leading-none mb-0.5"><?= e($displayName) ?></span>
                <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest leading-none"><?= e($roleName) ?></span>
            </div>
            <i class="ph ph-caret-down text-slate-400 text-[10px] ml-1"></i>
        </div>
        <div id="topProfileMenu" class="hidden absolute right-0 top-14 w-56 bg-[#1e293b] border border-white/10 rounded-xl shadow-2xl overflow-hidden shadow-[0_10px_40px_rgba(0,0,0,0.5)]">
            <div class="p-4 border-b border-white/5 bg-[#182130]">
                <div class="text-sm font-bold text-white"><?= e($displayName) ?></div>
                <div class="text-xs text-primary"><?= e($_SESSION['admin_email'] ?? '') ?></div>
            </div>
            <div class="p-2">
                <a href="<?= baseUrl('admin/profile') ?>" class="px-3 py-2 text-xs text-slate-300 hover:text-white hover:bg-white/5 flex items-center gap-3 rounded-lg transition-colors">
                    <i class="ph ph-user text-lg text-slate-400"></i> Edit Profile
                </a>
                <?php if(($adminData['role'] ?? '') === 'super_admin'): ?>
                <a href="<?= baseUrl('admin/users') ?>" class="px-3 py-2 text-xs text-slate-300 hover:text-white hover:bg-white/5 flex items-center gap-3 rounded-lg transition-colors mt-1">
                    <i class="ph ph-users-three text-lg text-emerald-400"></i> User Management
                </a>
                <a href="<?= baseUrl('admin/activity_logs') ?>" class="px-3 py-2 text-xs text-slate-300 hover:text-white hover:bg-white/5 flex items-center gap-3 rounded-lg transition-colors mt-1">
                    <i class="ph ph-shield-check text-lg text-purple-400"></i> Activity Logs
                </a>
                <?php endif; ?>
                <hr class="border-white/5 my-2">
                <a href="<?= baseUrl('admin/logout') ?>" class="px-3 py-2 text-xs text-red-400 hover:text-red-300 hover:bg-red-400/10 flex items-center gap-3 rounded-lg transition-colors">
                    <i class="ph ph-sign-out text-lg text-red-400/70"></i> Logout
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleNotifMenu(e) {
        e.stopPropagation();
        document.getElementById('notifMenu').classList.toggle('hidden');
        document.getElementById('topProfileMenu').classList.add('hidden');
    }
    
    function toggleProfileMenu(e) {
        e.stopPropagation();
        document.getElementById('topProfileMenu').classList.toggle('hidden');
        document.getElementById('notifMenu').classList.add('hidden');
    }

    // Close dropdowns on outside click
    document.addEventListener('click', (e) => {
        const notifMenu = document.getElementById('notifMenu');
        const profileMenu = document.getElementById('topProfileMenu');
        if (notifMenu && !e.target.closest('#notificationDropdown')) {
            notifMenu.classList.add('hidden');
        }
        if (profileMenu && !e.target.closest('#userProfileDropdown')) {
            profileMenu.classList.add('hidden');
        }
    });
</script>
