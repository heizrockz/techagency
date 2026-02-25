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
    <link rel="stylesheet" href="<?= baseUrl('assets/css/style.css') ?>">
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<div class="admin-layout">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>
    
    <div class="admin-main">
        <div class="admin-header">
            <div>
                <h1 style="color: var(--neon-cyan); margin:0;">📇 <?= ($action === 'edit' || $action === 'new') ? ($isNew ? 'New Contact' : 'Edit Contact') : 'Contacts (CRM)' ?></h1>
                <p style="color: var(--text-muted); font-size: 0.9rem;">Manage your clients and leads</p>
            </div>
            <div>
                <?php if ($action === 'list'): ?>
                    <a href="<?= baseUrl('admin/contacts?action=new') ?>" class="btn-primary" style="padding: 8px 16px; font-size: 0.9rem;">+ New Contact</a>
                <?php else: ?>
                    <a href="<?= baseUrl('admin/contacts') ?>" class="btn-ghost">← Back to List</a>
                <?php endif; ?>
            </div>
        </div>

        <?php if(isset($_GET['saved'])): ?>
            <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid var(--theme-primary); color: var(--theme-primary); padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                ✅ Contact saved successfully!
            </div>
        <?php endif; ?>

        <?php if ($action === 'edit' || $action === 'new'): ?>
            <form method="POST" action="<?= baseUrl('admin/contacts') ?>" class="admin-card">
                <input type="hidden" name="id" value="<?= $contact['id'] ?? 0 ?>">
                
                <div class="admin-grid-2" style="margin-bottom: 25px;">
                    <div class="form-group">
                        <label>Contact Name *</label>
                        <input type="text" name="name" value="<?= e($contact['name'] ?? '') ?>" class="form-input" required placeholder="Company or Individual Name">
                    </div>
                    <div class="form-group">
                        <label>Type</label>
                        <select name="type" class="form-input">
                            <option value="company" <?= ($contact['type'] ?? 'company') === 'company' ? 'selected' : '' ?>>🏢 Company</option>
                            <option value="individual" <?= ($contact['type'] ?? '') === 'individual' ? 'selected' : '' ?>>👤 Individual</option>
                        </select>
                    </div>
                </div>

                <div class="admin-grid-2" style="margin-bottom: 25px;">
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone" value="<?= e($contact['phone'] ?? '') ?>" class="form-input" placeholder="+971 50 123 4567">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="<?= e($contact['email'] ?? '') ?>" class="form-input" placeholder="info@example.com">
                    </div>
                </div>

                <div class="admin-grid-2" style="margin-bottom: 25px;">
                    <div class="form-group">
                        <label>TRN / VAT Number</label>
                        <input type="text" name="vat_number" value="<?= e($contact['vat_number'] ?? '') ?>" class="form-input" placeholder="Tax Registration Number">
                    </div>
                    <div class="form-group">
                        <label>Website</label>
                        <input type="url" name="website" value="<?= e($contact['website'] ?? '') ?>" class="form-input" placeholder="https://example.com">
                    </div>
                </div>

                <div class="admin-grid-2" style="margin-bottom: 25px;">
                    <div class="form-group">
                        <label>Location / Address</label>
                        <input type="text" name="location" value="<?= e($contact['location'] ?? '') ?>" class="form-input" placeholder="City, Street">
                    </div>
                    <div class="form-group">
                        <label>Country</label>
                        <input type="text" name="country" value="<?= e($contact['country'] ?? '') ?>" class="form-input" placeholder="United Arab Emirates">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                    <div class="form-group">
                        <label>POC Details (Point of Contact)</label>
                        <textarea name="poc_details" class="form-input" style="min-height: 80px;" placeholder="Name, Role, Direct Phone, Direct Email..."><?= e($contact['poc_details'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Connected Source</label>
                        <select name="source" class="form-input">
                            <?php $src = $contact['source'] ?? ''; ?>
                            <option value="" <?= empty($src) ? 'selected' : '' ?>>— Select —</option>
                            <option value="direct_enquiry" <?= $src === 'direct_enquiry' ? 'selected' : '' ?>>Direct Enquiry</option>
                            <option value="website" <?= $src === 'website' ? 'selected' : '' ?>>Website</option>
                            <option value="referral" <?= $src === 'referral' ? 'selected' : '' ?>>Referral</option>
                            <option value="social_media" <?= $src === 'social_media' ? 'selected' : '' ?>>Social Media</option>
                            <option value="cold_call" <?= $src === 'cold_call' ? 'selected' : '' ?>>Cold Call</option>
                            <option value="exhibition" <?= $src === 'exhibition' ? 'selected' : '' ?>>Exhibition / Event</option>
                            <option value="linkedin" <?= $src === 'linkedin' ? 'selected' : '' ?>>LinkedIn</option>
                            <option value="other" <?= $src === 'other' ? 'selected' : '' ?>>Other</option>
                        </select>
                    </div>
                </div>

                <div style="text-align: right; margin-top: 25px; border-top: 1px solid var(--glass-border); padding-top: 20px;">
                    <button type="submit" class="btn-primary">💾 Save Contact</button>
                </div>
            </form>

        <?php else: ?>
            <!-- Contact List -->
            <div class="admin-card">
                <?php if (empty($contacts)): ?>
                    <p style="color: var(--text-muted); text-align: center; padding: 40px;">No contacts yet. Create your first contact to get started.</p>
                <?php else: ?>
                    <div style="display: flex; justify-content: flex-end; margin-bottom: 15px;">
                        <input type="text" id="contactSearch" class="form-input" placeholder="Search contacts..." style="max-width: 300px;">
                    </div>
                    <div style="overflow-x: auto;">
                        <table class="admin-table" id="contactsTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Country</th>
                                    <th>Source</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($contacts as $c): ?>
                                <tr>
                                    <td><?= (int)$c['id'] ?></td>
                                    <td><strong><?= e($c['name']) ?></strong></td>
                                    <td><span style="padding:3px 8px;border-radius:4px;background:<?= $c['type']==='company'?'rgba(16,185,129,0.1)':'rgba(139,92,246,0.1)' ?>;font-size:0.8rem;"><?= $c['type'] === 'company' ? '🏢 Company' : '👤 Individual' ?></span></td>
                                    <td><?= e($c['phone'] ?? '—') ?></td>
                                    <td><?= e($c['email'] ?? '—') ?></td>
                                    <td><?= e($c['country'] ?? '—') ?></td>
                                    <td><span style="font-size:0.8rem;color:var(--text-muted);"><?= e(str_replace('_',' ',ucfirst($c['source'] ?? '—'))) ?></span></td>
                                    <td style="font-size: 0.85rem; color: var(--text-muted);"><?= date('M d, Y', strtotime($c['created_at'])) ?></td>
                                    <td>
                                        <a href="<?= baseUrl('admin/contacts?action=edit&id='.$c['id']) ?>" style="color: var(--neon-cyan); margin-right: 10px;">Edit</a>
                                        <a href="<?= baseUrl('admin/contacts?action=delete&id='.$c['id']) ?>" style="color: var(--neon-pink);" onclick="return confirm('Delete this contact? This cannot be undone.');">Delete</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
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
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
