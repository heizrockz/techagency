<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <title>Booking Form Fields — <?= APP_NAME ?></title>
    <?php require __DIR__ . '/partials/_head_assets.php'; ?>
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<div class="admin-layout flex w-full h-screen overflow-hidden">
    <?php $currentPage = 'booking_fields'; require __DIR__ . '/partials/sidebar.php'; ?>
    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-20 flex items-center justify-between px-8 bg-glass-bg border-b border-white/5 shrink-0 backdrop-blur-xl sticky top-0 z-[100]">
            <div class="flex flex-col">
                <div class="text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] mb-1 hidden sm:block">Interactive Interfaces</div>
                <h1 class="text-xl font-black text-white tracking-tight flex items-center gap-3 group">
                    <span class="text-neon-cyan drop-shadow-[0_0_8px_rgba(6,182,212,0.4)]">Portal Architect</span>
                    <span class="opacity-20 translate-y-px hidden sm:inline">/</span>
                    <span class="text-sm tracking-widest text-slate-400 uppercase font-black hidden sm:inline">Booking Fields</span>
                </h1>
            </div>
            <div class="flex items-center gap-6">
                <a href="<?= baseUrl('admin/booking-fields?action=new') ?>" class="px-4 sm:px-6 py-2.5 bg-neon-cyan hover:bg-cyan-400 text-black text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-lg active:scale-95 flex items-center gap-2">
                    <i class="ph-bold ph-plus-circle text-lg"></i> <span class="hidden sm:inline">Inject New Vector</span>
                </a>
                <?php require __DIR__ . '/partials/_topbar.php'; ?>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-8 crm-main-scroll bg-[#0b0e14]">
            <?php if ($saved): ?>
                <div class="mb-8 p-4 bg-neon-emerald/10 border border-neon-emerald/20 rounded-2xl text-neon-emerald text-[10px] font-black uppercase tracking-widest flex items-center gap-3">
                    <i class="ph-bold ph-check-circle text-lg"></i> Parameters Committed Successfully
                </div>
            <?php endif; ?>

        <?php if ($action === 'edit' || $action === 'new'): ?>
            <div class="admin-card" style="margin-bottom: 30px;">
                <form method="POST" action="<?= baseUrl('admin/booking-fields') ?>">
                    <input type="hidden" name="id" value="<?= $editField['id'] ?? 0 ?>">
                    <div class="admin-grid-2">
                        <div class="form-group">
                            <label>Field Name (code handle) *</label>
                            <input type="text" name="field_name" class="form-input" value="<?= e($editField['field_name'] ?? '') ?>" required <?= isset($editField) && in_array($editField['field_name'], ['name','email','phone','service','date','message']) ? 'readonly' : '' ?>>
                            <small style="color:var(--text-muted)">Must be unique, lowercase, no spaces.</small>
                        </div>
                        <div class="form-group">
                            <label>Field Type</label>
                            <select name="field_type" class="form-input">
                                <?php $ft = $editField['field_type'] ?? 'text'; ?>
                                <option value="text" <?= $ft==='text'?'selected':'' ?>>Text Line</option>
                                <option value="email" <?= $ft==='email'?'selected':'' ?>>Email</option>
                                <option value="tel" <?= $ft==='tel'?'selected':'' ?>>Phone</option>
                                <option value="number" <?= $ft==='number'?'selected':'' ?>>Number</option>
                                <option value="select" <?= $ft==='select'?'selected':'' ?>>Select Dropdown</option>
                                <option value="date" <?= $ft==='date'?'selected':'' ?>>Date Picker</option>
                                <option value="textarea" <?= $ft==='textarea'?'selected':'' ?>>Multiline Textarea</option>
                            </select>
                        </div>
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label>Dropdown Options (comma separated, only if type is Select)</label>
                            <input type="text" name="options" class="form-input" value="<?= e($editField['options'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Sort Order</label>
                            <input type="number" name="sort_order" class="form-input" value="<?= $editField['sort_order'] ?? 0 ?>">
                        </div>
                        <div class="form-group" style="display: flex; align-items: center; gap: 20px; margin-top: 25px;">
                            <label style="cursor: pointer; display: flex; align-items: center; gap: 10px;">
                                <input type="checkbox" name="is_required" <?= (!isset($editField) || $editField['is_required']) ? 'checked' : '' ?>> Required
                            </label>
                            <label style="cursor: pointer; display: flex; align-items: center; gap: 10px;">
                                <input type="checkbox" name="is_active" <?= (!isset($editField) || $editField['is_active']) ? 'checked' : '' ?>> Active
                            </label>
                        </div>
                    </div>

                    <?php foreach (SUPPORTED_LOCALES as $loc): ?>
                        <div style="margin-top: 20px; padding: 15px; background: rgba(255,255,255,0.02); border-radius: 8px;">
                            <h4 style="margin-bottom: 10px;"><?= strtoupper($loc) ?> Label & Placeholder</h4>
                            <div class="form-group">
                                <label>Label</label>
                                <input type="text" name="label_<?= $loc ?>" class="form-input" value="<?= e($editField['translations'][$loc]['label'] ?? '') ?>" dir="<?= $loc === 'ar' ? 'rtl' : 'ltr' ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Placeholder</label>
                                <input type="text" name="placeholder_<?= $loc ?>" class="form-input" value="<?= e($editField['translations'][$loc]['placeholder'] ?? '') ?>" dir="<?= $loc === 'ar' ? 'rtl' : 'ltr' ?>">
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div style="margin-top: 20px;">
                        <button type="submit" class="btn-primary">Save Field</button>
                        <a href="<?= baseUrl('admin/booking-fields') ?>" class="btn-ghost" style="margin-left: 10px;">Cancel</a>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div class="admin-card">
                <div style="overflow-x: auto;">
                    <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Field Handle</th>
                            <th>Type</th>
                            <th>Translations</th>
                            <th>Required</th>
                            <th>Sort</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($fields as $f): ?>
                        <tr class="hover:bg-white/[0.03] transition-all group/row border-b border-white/[0.03] last:border-0 relative">
                            <td class="py-4" data-label="Handle"><strong><?= e($f['field_name']) ?></strong></td>
                            <td class="py-4" data-label="Type"><span style="padding:4px 8px;border-radius:4px;background:var(--glass-bg);font-size:0.75rem;"><?= e($f['field_type']) ?></span></td>
                            <td class="py-4" data-label="Trans"><?= e($f['trans']) ?></td>
                            <td class="py-4" data-label="Required"><?= $f['is_required'] ? 'Yes' : 'No' ?></td>
                            <td class="py-4" data-label="Sort"><?= $f['sort_order'] ?></td>
                            <td class="py-4" data-label="Status"><?= $f['is_active'] ? '<span style="color:var(--neon-emerald)">Active</span>' : '<span style="color:var(--text-muted)">Inactive</span>' ?></td>
                            <td class="py-4 text-right" data-label="Actions">
                                <div class="flex justify-end gap-3">
                                    <a href="<?= baseUrl('admin/booking-fields?action=edit&id='.$f['id']) ?>" style="color: var(--neon-cyan);">Edit</a>
                                    <?php if (!in_array($f['field_name'], ['name','email','phone','service','date','message'])): ?>
                                    <a href="javascript:void(0)" style="color: var(--neon-pink);" onclick="showDeleteModal('this field', '<?= baseUrl('admin/booking-fields?action=delete&id='.$f['id']) ?>')">Delete</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                </div>
            </div>
        <?php endif; ?>
        </main>
    </div>
</div>
<?php require __DIR__ . '/partials/_delete_modal.php'; ?>
<style>
<style>
    /* Desktop-first: ensure table looks good on large screens */
    @media screen and (min-width: 1025px) {
        .admin-table { min-width: 1000px; }
    }

    /* Mobile-responsive card transformation */
    @media (max-width: 1024px) {
        .admin-table-wrapper { border-radius: 1.5rem !important; margin: -1rem !important; }
        .admin-table thead { display: none !important; }
        
        .admin-table, 
        .admin-table tbody, 
        .admin-table tr, 
        .admin-table td { 
            display: block !important; 
            width: 100% !important; 
            min-width: 0 !important;
        }
        
        .admin-table tr { 
            margin-bottom: 20px !important; 
            background: rgba(255,255,255,0.02) !important; 
            border-radius: 1.5rem !important; 
            padding: 20px !important;
            border: 1px solid rgba(255,255,255,0.05) !important;
        }
        
        .admin-table td { 
            display: flex !important; 
            justify-content: space-between !important; 
            align-items: center !important; 
            padding: 12px 0 !important; 
            border-bottom: 1px solid rgba(255,255,255,0.03) !important;
            text-align: right !important;
            min-height: 44px !important;
        }
        
        .admin-table td:last-child { border-bottom: none !important; }
        
        .admin-table td::before { 
            content: attr(data-label) !important; 
            font-weight: 900 !important; 
            text-transform: uppercase !important; 
            font-size: 0.65rem !important; 
            color: #06b6d4 !important;
            letter-spacing: 2px !important;
            opacity: 0.6 !important;
            text-align: left !important;
            margin-right: 12px !important;
        }
        
        .admin-card { padding: 1.5rem !important; }
        .admin-grid-2 { grid-template-columns: 1fr !important; }
    }
</style>
</body>
</html>
