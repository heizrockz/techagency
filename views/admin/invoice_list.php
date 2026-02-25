<?php
$title = 'Invoices & Quotes';
$currentPage = 'invoices';
?>
<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?> — <?= APP_NAME ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= baseUrl('assets/css/style.css') ?>">
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">


<div class="admin-layout">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>
    
    <div class="admin-main">
        <div class="admin-header">
            <div>
                <h1 style="color: var(--theme-gold); margin:0;">🧾 Invoices & Quotes</h1>
                <p style="color: var(--text-muted); font-size: 0.9rem;">Manage and generate client invoices and quotes.</p>
            </div>
            <a href="<?= baseUrl('admin/invoices?action=new') ?>" class="btn-primary" style="text-decoration:none;">+ Create New</a>
        </div>

        <div class="admin-card">
            <?php if (empty($invoices)): ?>
                <p style="color: var(--text-muted); text-align: center; padding: 40px 0;">No invoices or quotes found. Create your first one!</p>
            <?php else: ?>
                <div style="display: flex; justify-content: flex-end; margin-bottom: 15px;">
                    <input type="text" id="invoiceSearch" class="form-input" placeholder="Search invoices..." style="max-width: 300px;">
                </div>
                <div style="overflow-x: auto;">
                    <table class="admin-table" id="invoicesTable">
                    <thead>
                        <tr>
                            <th>Number</th>
                            <th>Type</th>
                            <th>Client</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($invoices as $inv): ?>
                            <tr>
                                <td><strong style="color: var(--text-primary);"><?= e($inv['invoice_number']) ?></strong></td>
                                <td>
                                    <?php if($inv['type'] === 'quote'): ?>
                                        <span class="status-badge" style="background: rgba(14, 165, 233, 0.1); color: var(--neon-cobalt);">Quote</span>
                                    <?php else: ?>
                                        <span class="status-badge" style="background: rgba(16, 185, 129, 0.1); color: var(--theme-primary);">Invoice</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= e($inv['client_name']) ?></td>
                                <td>
                                    <?php
                                    $statusColors = [
                                        'draft' => 'rgba(255,255,255,0.2)',
                                        'sent' => 'var(--neon-cobalt)',
                                        'paid' => 'var(--theme-primary)',
                                        'cancelled' => 'var(--neon-pink)'
                                    ];
                                    $color = $statusColors[$inv['status']] ?? 'var(--text-muted)';
                                    ?>
                                    <span style="color: <?= $color ?>; text-transform: uppercase; font-size: 0.8rem; font-weight: 700; letter-spacing: 1px;">
                                        <?= e($inv['status']) ?>
                                    </span>
                                </td>
                                <td><?= date('M j, Y', strtotime($inv['created_at'])) ?></td>
                                <td>
                                    <a href="<?= baseUrl('admin/invoices?action=edit&id=' . $inv['id']) ?>" class="admin-btn" style="padding: 6px 12px; font-size: 0.8rem;">Edit</a>
                                    <a href="<?= baseUrl('admin/invoices?action=print&id=' . $inv['id']) ?>" target="_blank" class="admin-btn" style="padding: 6px 12px; font-size: 0.8rem; border-color: var(--theme-gold); color: var(--theme-gold);">Print</a>
                                    <a href="<?= baseUrl('admin/invoices?action=delete&id=' . $inv['id']) ?>" class="admin-btn" style="padding: 6px 12px; font-size: 0.8rem; color: #f43f5e; border-color: rgba(244, 63, 94, 0.3);" onclick="return confirm('Delete this record? This cannot be undone.')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                </div>
                
                <script>
                document.getElementById('invoiceSearch').addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const rows = document.querySelectorAll('#invoicesTable tbody tr');
                    
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(searchTerm) ? '' : 'none';
                    });
                });
                </script>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
