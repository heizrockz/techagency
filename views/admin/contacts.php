<?php
$contact = $contact ?? [];
$isNew = empty($contact);
$currentPage = 'contacts';
$action = $action ?? 'list';
?>
<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacts — <?= APP_NAME ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= baseUrl('assets/css/style.css') ?>">
</head>
<body class="bg-[#0b0e14]" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<div class="admin-layout flex w-full">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>
    
    <div class="crm-main leading-relaxed text-slate-300">
        <header class="h-16 flex items-center justify-between px-6 bg-[#1a2333] border-b border-white/5 shrink-0">
            <h1 class="text-xl font-semibold text-white tracking-tight flex items-center gap-2">
                <i class="ph ph-users-three text-primary"></i>
                <?= ($action === 'edit' || $action === 'new') ? ($isNew ? 'New Contact' : 'Edit Contact') : 'Contacts' ?>
            </h1>
            <div>
                <?php if ($action === 'list'): ?>
                    <a href="<?= baseUrl('admin/contacts?action=new') ?>" class="btn-primary flex items-center gap-2">
                        <i class="ph ph-plus"></i> New Contact
                    </a>
                <?php else: ?>
                    <a href="<?= baseUrl('admin/contacts') ?>" class="btn-secondary flex items-center gap-2">
                        <i class="ph ph-arrow-left"></i> Back to List
                    </a>
                <?php endif; ?>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-6 bg-[#0b0e14] w-full h-full crm-main-scroll">
            <?php if(isset($_GET['saved']) || ($flash = getFlash())): ?>
                <div class="mb-6 p-4 rounded-xl border border-emerald-500/20 bg-emerald-500/10 text-emerald-400 text-sm flex items-center gap-2">
                    <i class="ph ph-check-circle text-lg"></i>
                    <?= htmlspecialchars($flash ?? 'Contact saved successfully!') ?>
                </div>
            <?php endif; ?>

        <?php if ($action === 'edit' || $action === 'new'): ?>
            <form method="POST" action="<?= baseUrl('admin/contacts') ?>" class="bg-[#1a2333]/40 backdrop-blur-lg border border-white/5 rounded-3xl p-8 shadow-2xl space-y-6">
                <input type="hidden" name="id" value="<?= $contact['id'] ?? 0 ?>">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest">Contact Name *</label>
                        <input type="text" name="name" value="<?= e($contact['name'] ?? '') ?>" class="w-full bg-black/40 border border-white/10 rounded-xl py-3 px-4 text-white focus:border-primary outline-none transition-all" required placeholder="Company or Individual Name">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest">Type</label>
                        <select name="type" class="w-full bg-black/40 border border-white/10 rounded-xl py-3 px-4 text-white focus:border-primary outline-none transition-all appearance-none">
                            <option value="company" <?= ($contact['type'] ?? 'company') === 'company' ? 'selected' : '' ?>>🏢 Company</option>
                            <option value="individual" <?= ($contact['type'] ?? '') === 'individual' ? 'selected' : '' ?>>👤 Individual</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest">Phone Number</label>
                        <input type="text" name="phone" value="<?= e($contact['phone'] ?? '') ?>" class="w-full bg-black/40 border border-white/10 rounded-xl py-3 px-4 text-white focus:border-primary outline-none transition-all" placeholder="+971 50 123 4567">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest">Email</label>
                        <input type="email" name="email" value="<?= e($contact['email'] ?? '') ?>" class="w-full bg-black/40 border border-white/10 rounded-xl py-3 px-4 text-white focus:border-primary outline-none transition-all" placeholder="info@example.com">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest">TRN / VAT Number</label>
                        <input type="text" name="vat_number" value="<?= e($contact['vat_number'] ?? '') ?>" class="w-full bg-black/40 border border-white/10 rounded-xl py-3 px-4 text-white focus:border-primary outline-none transition-all" placeholder="Tax Registration Number">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest">Website</label>
                        <input type="url" name="website" value="<?= e($contact['website'] ?? '') ?>" class="w-full bg-black/40 border border-white/10 rounded-xl py-3 px-4 text-white focus:border-primary outline-none transition-all" placeholder="https://example.com">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest">Location / Address</label>
                        <input type="text" name="location" value="<?= e($contact['location'] ?? '') ?>" class="w-full bg-black/40 border border-white/10 rounded-xl py-3 px-4 text-white focus:border-primary outline-none transition-all" placeholder="City, Street">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest">Country</label>
                        <input type="text" name="country" value="<?= e($contact['country'] ?? '') ?>" class="w-full bg-black/40 border border-white/10 rounded-xl py-3 px-4 text-white focus:border-primary outline-none transition-all" placeholder="United Arab Emirates">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest">POC Details (Point of Contact)</label>
                        <textarea name="poc_details" class="w-full bg-black/40 border border-white/10 rounded-xl py-3 px-4 text-white focus:border-primary outline-none transition-all min-h-[100px]" placeholder="Name, Role, Direct Phone, Direct Email..."><?= e($contact['poc_details'] ?? '') ?></textarea>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest">Connected Source</label>
                        <select name="source" class="w-full bg-black/40 border border-white/10 rounded-xl py-3 px-4 text-white focus:border-primary outline-none transition-all appearance-none">
                            <?php $src = $contact['source'] ?? ''; ?>
                            <option value="" <?= empty($src) ? 'selected' : '' ?>>— Select —</option>
                            <option value="direct_enquiry" <?= $src === 'direct_enquiry' ? 'selected' : '' ?>>Direct Enquiry</option>
                            <option value="website" <?= $src === 'website' ? 'selected' : '' ?>>Website</option>
                            <option value="referral" <?= $src === 'referral' ? 'selected' : '' ?>>Referral</option>
                            <option value="social_media" <?= $src === 'social_media' ? 'selected' : '' ?>>Social Media</option>
                            <option value="cold_call" <?= $src === 'cold_call' ? 'selected' : '' ?>>Cold Call</option>
                            <option value="exhibition" <?= $src === 'exhibition' ? 'selected' : '' ?>>Exhibitone / Event</option>
                            <option value="linkedin" <?= $src === 'linkedin' ? 'selected' : '' ?>>LinkedIn</option>
                            <option value="other" <?= $src === 'other' ? 'selected' : '' ?>>Other</option>
                        </select>
                    </div>
                </div>

                <div class="pt-6 flex justify-end gap-4 border-t border-white/5">
                    <a href="<?= baseUrl('admin/contacts') ?>" class="px-6 py-3 bg-white/5 text-slate-400 font-bold uppercase tracking-widest text-[11px] rounded-xl hover:bg-white/10 transition-all">Cancel</a>
                    <button type="submit" class="px-8 py-3 bg-primary text-white font-bold uppercase tracking-widest text-xs rounded-xl hover:shadow-[0_0_20px_rgba(var(--primary-rgb),0.3)] transition-all min-w-[140px]">💾 Save Contact</button>
                </div>
            </form>

        <?php else: ?>
            <!-- Contact List -->
            <div class="bg-[#1a2333]/40 backdrop-blur-lg border border-white/5 rounded-3xl shadow-2xl overflow-hidden flex flex-col">
                <div class="p-6 border-b border-white/5 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="relative w-full sm:w-80">
                        <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-500"></i>
                        <input type="text" id="contactSearch" placeholder="Search contacts..." class="w-full bg-black/40 border border-white/10 rounded-xl py-2 pl-10 pr-4 text-sm text-white focus:border-primary outline-none transition-all">
                    </div>
                    <?php if (!empty($contacts)): ?>
                    <div class="text-[10px] font-bold uppercase tracking-widest text-slate-500">
                        Showing <?= count($contacts) ?> Contacts
                    </div>
                    <?php endif; ?>
                </div>

                <div class="overflow-x-auto">
                    <?php if (empty($contacts)): ?>
                        <div class="py-20 text-center text-slate-500 italic">No contacts yet. Create your first contact to get started.</div>
                    <?php else: ?>
                        <table class="w-full text-left border-collapse min-w-[900px]" id="contactsTable">
                            <thead>
                                <tr class="bg-black/40 text-slate-500 text-[10px] font-bold uppercase tracking-[0.2em]">
                                    <th class="py-5 px-8">Client Info</th>
                                    <th class="py-5 px-4">Type</th>
                                    <th class="py-5 px-4 text-center">Contact</th>
                                    <th class="py-5 px-4 text-center">Location</th>
                                    <th class="py-5 px-8 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/[0.03] text-sm">
                                <?php foreach ($contacts as $c): ?>
                                <tr class="hover:bg-white/[0.01] transition-colors group">
                                    <td class="py-5 px-8">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-xl bg-black/40 border border-white/5 flex items-center justify-center text-lg shadow-inner">
                                                <i class="<?= $c['type']==='company'?'ph ph-buildings':'ph ph-user' ?> text-primary"></i>
                                            </div>
                                            <div>
                                                <span class="text-white font-bold block mb-0.5"><?= e($c['name']) ?></span>
                                                <span class="text-[10px] text-slate-500 uppercase tracking-widest font-bold font-mono">Added <?= date('M d, Y', strtotime($c['created_at'])) ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-5 px-4">
                                        <span class="px-3 py-1 rounded-lg bg-white/5 border border-white/5 text-[11px] text-slate-400 font-bold uppercase tracking-tight">
                                            <?= $c['type'] === 'company' ? 'Company' : 'Individual' ?>
                                        </span>
                                    </td>
                                    <td class="py-5 px-4 text-center space-y-1">
                                        <?php if ($c['phone']): ?>
                                            <div class="flex items-center justify-center gap-2 text-slate-400 hover:text-white transition-colors">
                                                <i class="ph ph-phone text-primary"></i>
                                                <span class="text-xs"><?= e($c['phone']) ?></span>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($c['email']): ?>
                                            <div class="flex items-center justify-center gap-2 text-slate-400 hover:text-white transition-colors">
                                                <i class="ph ph-envelope text-primary"></i>
                                                <span class="text-xs"><?= e($c['email']) ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-5 px-4 text-center">
                                        <div class="flex flex-col items-center">
                                            <span class="text-white font-medium block"><?= e($c['country'] ?? '—') ?></span>
                                            <span class="text-[10px] text-slate-500"><?= e($c['location'] ?? '') ?></span>
                                        </div>
                                    </td>
                                    <td class="py-5 px-8 text-right">
                                        <div class="flex justify-end gap-2">
                                            <a href="<?= baseUrl('admin/contacts?action=edit&id='.$c['id']) ?>" class="w-8 h-8 rounded-lg bg-primary/10 text-primary hover:bg-primary hover:text-white transition-all flex items-center justify-center" title="Edit">
                                                <i class="ph ph-pencil-simple"></i>
                                            </a>
                                            <button type="button" onclick="showDeleteModal('<?= e($c['name']) ?>', '<?= baseUrl('admin/contacts?action=delete&id='.$c['id']) ?>')" class="w-8 h-8 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center" title="Delete">
                                                <i class="ph ph-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
                    
                    <script>
                    document.getElementById('contactSearch').addEventListener('input', function() {
                        const searchTerm = this.value.toLowerCase();
                        const rows = document.querySelectorAll('#contactsTable tbody tr');
                        
                        rows.forEach(row => {
                            const text = row.textContent.toLowerCase();
                            row.style.display = text.includes(searchTerm) ? '' : 'none';
                        });
                    });
                    </script>
                <?php endif; ?>
            </main>
    </div>
</div>

<?php require __DIR__ . '/partials/_delete_modal.php'; ?>
</body>
</html>
