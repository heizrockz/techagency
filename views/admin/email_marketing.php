<?php
$title = "Email Marketing";
$currentPage = 'marketing';
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
            <div class="header-left">
                <h1 style="color: var(--neon-cobalt); margin:0; font-size:1.8rem;">✉️ Email Marketing</h1>
                <p style="color: var(--text-muted); font-size: 0.85rem;">Configure SMTP and send bulk or single email campaigns.</p>
            </div>
        </div>

        <?php if ($saved): ?>
            <div class="alert alert-success" style="background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.2); color:var(--neon-emerald); padding:15px; border-radius:10px; margin-bottom:20px;">Email settings saved and verified successfully.</div>
        <?php endif; ?>

        <?php if ($sent): ?>
            <div class="alert alert-success" style="background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.2); color:var(--neon-emerald); padding:15px; border-radius:10px; margin-bottom:20px;">Campaign executed. Check the history below.</div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error" style="background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.2); color:var(--neon-pink); padding:15px; border-radius:10px; margin-bottom:20px;"><?= e($error) ?></div>
        <?php endif; ?>

        <!-- Collapsible Configuration Section -->
        <div class="admin-card collapsible-card" style="margin-bottom: 30px;">
            <div class="card-header" style="cursor:pointer; display:flex; justify-content:space-between; align-items:center;" onclick="toggleConfig()">
                <h3 class="card-title" style="font-size:1.1rem; font-weight:600; margin:0;">⚙️ SMTP / IMAP Configuration</h3>
                <span id="config-toggle-icon" style="font-size:1.2rem; transition: transform 0.3s;">▼</span>
            </div>
            <div id="config-content" style="display:none; padding-top:20px; border-top:1px solid rgba(255,255,255,0.05); margin-top:15px;">
                <form method="POST">
                    <input type="hidden" name="action" value="save_settings">
                    <div class="form-row" style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
                        <div class="form-group">
                            <label style="display:block; font-size:0.8rem; color:var(--text-muted); margin-bottom:5px;">SMTP Host</label>
                            <input type="text" name="smtp_host" value="<?= e($settings['smtp_host']) ?>" class="form-control" placeholder="smtp.example.com" style="width:100%; padding:10px; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); border-radius:8px; color:white;">
                        </div>
                        <div class="form-group">
                            <label style="display:block; font-size:0.8rem; color:var(--text-muted); margin-bottom:5px;">SMTP Port</label>
                            <input type="number" name="smtp_port" value="<?= e($settings['smtp_port']) ?>" class="form-control" placeholder="587" style="width:100%; padding:10px; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); border-radius:8px; color:white;">
                        </div>
                    </div>
                    <div class="form-row" style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
                        <div class="form-group">
                            <label style="display:block; font-size:0.8rem; color:var(--text-muted); margin-bottom:5px;">SMTP User</label>
                            <input type="text" name="smtp_user" value="<?= e($settings['smtp_user']) ?>" class="form-control" placeholder="user@example.com" style="width:100%; padding:10px; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); border-radius:8px; color:white;">
                        </div>
                        <div class="form-group">
                            <label style="display:block; font-size:0.8rem; color:var(--text-muted); margin-bottom:5px;">SMTP Password</label>
                            <input type="password" name="smtp_pass" value="<?= e($settings['smtp_pass']) ?>" class="form-control" placeholder="••••••••" style="width:100%; padding:10px; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); border-radius:8px; color:white;">
                        </div>
                    </div>
                    <div class="form-row" style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
                        <div class="form-group">
                            <label style="display:block; font-size:0.8rem; color:var(--text-muted); margin-bottom:5px;">Encryption</label>
                            <select name="smtp_encryption" class="form-control" style="width:100%; padding:10px; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); border-radius:8px; color:white;">
                                <option value="none" <?= $settings['smtp_encryption'] === 'none' ? 'selected' : '' ?>>None</option>
                                <option value="tls" <?= $settings['smtp_encryption'] === 'tls' ? 'selected' : '' ?>>TLS (Recommended)</option>
                                <option value="ssl" <?= $settings['smtp_encryption'] === 'ssl' ? 'selected' : '' ?>>SSL</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label style="display:block; font-size:0.8rem; color:var(--text-muted); margin-bottom:5px;">From Name</label>
                            <input type="text" name="from_name" value="<?= e($settings['from_name']) ?>" class="form-control" placeholder="Mico Sage Support" style="width:100%; padding:10px; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); border-radius:8px; color:white;">
                        </div>
                    </div>
                    <div class="form-group" style="margin-bottom:15px;">
                        <label style="display:block; font-size:0.8rem; color:var(--text-muted); margin-bottom:5px;">From Email</label>
                        <input type="email" name="from_email" value="<?= e($settings['from_email']) ?>" class="form-control" placeholder="no-reply@micosage.com" style="width:100%; padding:10px; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); border-radius:8px; color:white;">
                    </div>
                    
                    <hr style="border:0; border-top:1px solid rgba(255,255,255,0.05); margin: 20px 0;">
                    
                    <div class="form-group">
                        <label style="display:block; font-size:0.8rem; color:var(--text-muted); margin-bottom:5px;">Email Signature (HTML)</label>
                        <textarea name="signature_html" class="form-control" rows="4" style="width:100%; padding:10px; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); border-radius:8px; color:white; font-family:monospace;"><?= e($settings['signature_html']) ?></textarea>
                        <small style="color:var(--text-muted); font-size:0.7rem;">This signature will be appended to all outgoing emails.</small>
                    </div>

                    <div style="margin-top: 20px;">
                        <button type="submit" class="btn btn-primary" style="background:var(--theme-primary); border:none; padding:10px 20px; border-radius:8px; color:white; font-weight:600; cursor:pointer;">Save & Verify Configuration</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="marketing-grid" style="display: grid; grid-template-columns: 1fr; gap: 30px;">
            <!-- Launch Campaign Section -->
            <div class="admin-card">
                <div class="card-header" style="margin-bottom:20px;">
                    <h3 class="card-title" style="font-size:1.1rem; font-weight:600;">🚀 Launch New Campaign</h3>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="send_campaign">
                        
                        <div class="form-row" style="display:grid; grid-template-columns:1fr 1fr; gap:30px;">
                            <div class="col-left">
                                <div class="form-group" style="margin-bottom:15px;">
                                    <label style="display:block; font-size:0.8rem; color:var(--text-muted); margin-bottom:5px;">Campaign Subject</label>
                                    <input type="text" name="subject" class="form-control" required placeholder="Our Latest AI Insights" style="width:100%; padding:10px; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); border-radius:8px; color:white;">
                                </div>
                                <div class="form-group" style="margin-bottom:15px;">
                                    <label style="display:block; font-size:0.8rem; color:var(--text-muted); margin-bottom:5px;">Email Body (HTML)</label>
                                    <textarea name="body" class="form-control" rows="12" required placeholder="Hello, we are excited to share..." style="width:100%; padding:10px; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); border-radius:8px; color:white; font-family:inherit;"></textarea>
                                </div>

                                <!-- Signature Preview -->
                                <?php if (!empty($settings['signature_html'])): ?>
                                    <div class="signature-preview" style="margin-top:10px; padding:15px; background:rgba(255,255,255,0.02); border:1px dashed var(--glass-border); border-radius:8px;">
                                        <div style="font-size:0.75rem; color:var(--text-muted); margin-bottom:10px; text-transform:uppercase; letter-spacing:1px;">Signature Preview</div>
                                        <div style="color:rgba(255,255,255,0.8); font-size:0.9rem;">
                                            <div style="margin-bottom:10px;">--</div>
                                            <?= $settings['signature_html'] ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="col-right">
                                <div class="form-group" style="margin-bottom:20px;">
                                    <label style="display:block; font-size:0.8rem; color:var(--text-muted); margin-bottom:10px;">Recipient Type</label>
                                    <div style="display:flex; gap:20px;">
                                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                                            <input type="radio" name="send_type" value="single" checked onclick="toggleRecipientType('single')"> Single / Manual
                                        </label>
                                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                                            <input type="radio" name="send_type" value="bulk" onclick="toggleRecipientType('bulk')"> Bulk (CSV)
                                        </label>
                                    </div>
                                </div>

                                <div id="recipient-single" class="form-group" style="margin-bottom:20px;">
                                    <label style="display:block; font-size:0.8rem; color:var(--text-muted); margin-bottom:5px;">Recipient Emails</label>
                                    <textarea name="single_recipients" class="form-control" rows="4" placeholder="email1@example.com, email2@example.com" style="width:100%; padding:10px; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); border-radius:8px; color:white;"></textarea>
                                    <small style="color:var(--text-muted); font-size:0.7rem;">Enter one or more emails separated by commas.</small>
                                </div>

                                <div id="recipient-bulk" class="form-group" style="margin-bottom:20px; display:none;">
                                    <label style="display:block; font-size:0.8rem; color:var(--text-muted); margin-bottom:5px;">Recipient List (CSV)</label>
                                    <input type="file" name="email_list" class="form-control" accept=".csv" style="width:100%; padding:8px; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); border-radius:8px; color:white;">
                                    <small style="color:var(--text-muted); font-size:0.7rem;">Upload a CSV file. The first column must contain the email addresses.</small>
                                </div>

                                <div style="margin-top: 40px; text-align:center;">
                                    <button type="submit" class="btn btn-neon" style="width:100%; padding:15px;" onclick="return confirm('Launch campaign to all recipients?')">
                                        <span style="font-size:1.2rem;">🚀</span> Launch Campaign Now
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- History Section -->
        <div class="admin-card" style="margin-top: 30px;">
            <div class="card-header" style="margin-bottom:20px;">
                <h3 class="card-title" style="font-size:1.1rem; font-weight:600;">📜 Campaign History</h3>
            </div>
            <div class="card-body">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Sent/Total</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($campaigns as $camp): ?>
                        <tr>
                            <td>#<?= $camp['id'] ?></td>
                            <td><strong><?= e($camp['subject']) ?></strong></td>
                            <td>
                                <span class="badge" style="padding:4px 8px; border-radius:4px; font-size:0.75rem; background: <?= $camp['status'] === 'completed' ? 'rgba(16,185,129,0.1)' : 'rgba(59,130,246,0.1)' ?>; color: <?= $camp['status'] === 'completed' ? 'var(--neon-emerald)' : 'var(--neon-cyan)' ?>;">
                                    <?= ucfirst($camp['status']) ?>
                                </span>
                            </td>
                            <td><?= $camp['sent_count'] ?> / <?= $camp['total_emails'] ?></td>
                            <td><?= date('Y-m-d H:i', strtotime($camp['created_at'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($campaigns)): ?>
                        <tr><td colspan="5" style="text-align:center; padding:30px; color:var(--text-muted);">No campaigns launched yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function toggleConfig() {
    const content = document.getElementById('config-content');
    const icon = document.getElementById('config-toggle-icon');
    if (content.style.display === 'none') {
        content.style.display = 'block';
        icon.style.transform = 'rotate(180deg)';
    } else {
        content.style.display = 'none';
        icon.style.transform = 'rotate(0deg)';
    }
}

function toggleRecipientType(type) {
    const single = document.getElementById('recipient-single');
    const bulk = document.getElementById('recipient-bulk');
    if (type === 'single') {
        single.style.display = 'block';
        bulk.style.display = 'none';
    } else {
        single.style.display = 'none';
        bulk.style.display = 'block';
    }
}
</script>

<style>
.btn-neon {
    background: linear-gradient(135deg, var(--neon-emerald), var(--neon-cyan));
    color: #000;
    font-weight: 700;
    border: none;
    padding: 12px 25px;
    border-radius: 8px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    transition: all 0.3s ease;
}
.btn-neon:hover {
    filter: brightness(1.1);
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(var(--neon-cyan-rgb), 0.4);
}
.admin-card {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid var(--glass-border);
    border-radius: 12px;
    padding: 25px;
}
.collapsible-card:hover {
    background: rgba(255, 255, 255, 0.04);
}
</style>

</body>
</html>
