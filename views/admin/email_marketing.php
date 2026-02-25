<?php
$title = "Email Marketing";
ob_start();
?>

<div class="admin-header">
    <div class="header-left">
        <h1>Email Marketing</h1>
        <p>Configure SMTP and send bulk email campaigns.</p>
    </div>
</div>

<?php if ($saved): ?>
    <div class="alert alert-success">Email settings saved successfully.</div>
<?php endif; ?>

<?php if ($sent): ?>
    <div class="alert alert-success">Campaign executed. Emails are being sent. Check the history below.</div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-error"><?= e($error) ?></div>
<?php endif; ?>

<div class="marketing-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
    <!-- Column 1: Config & Send -->
    <div class="col-left">
        <div class="card" style="margin-bottom: 30px;">
            <div class="card-header">
                <h3 class="card-title">SMTP / IMAP Configuration</h3>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="save_settings">
                    <div class="form-row">
                        <div class="form-group">
                            <label>SMTP Host</label>
                            <input type="text" name="smtp_host" value="<?= e($settings['smtp_host']) ?>" class="form-control" placeholder="smtp.example.com">
                        </div>
                        <div class="form-group">
                            <label>SMTP Port</label>
                            <input type="number" name="smtp_port" value="<?= e($settings['smtp_port']) ?>" class="form-control" placeholder="587">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>SMTP User</label>
                            <input type="text" name="smtp_user" value="<?= e($settings['smtp_user']) ?>" class="form-control" placeholder="user@example.com">
                        </div>
                        <div class="form-group">
                            <label>SMTP Password</label>
                            <input type="password" name="smtp_pass" value="<?= e($settings['smtp_pass']) ?>" class="form-control" placeholder="••••••••">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Encryption</label>
                            <select name="smtp_encryption" class="form-control">
                                <option value="none" <?= $settings['smtp_encryption'] === 'none' ? 'selected' : '' ?>>None</option>
                                <option value="tls" <?= $settings['smtp_encryption'] === 'tls' ? 'selected' : '' ?>>TLS (Recommended)</option>
                                <option value="ssl" <?= $settings['smtp_encryption'] === 'ssl' ? 'selected' : '' ?>>SSL</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>From Name</label>
                            <input type="text" name="from_name" value="<?= e($settings['from_name']) ?>" class="form-control" placeholder="Mico Sage Support">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>From Email</label>
                        <input type="email" name="from_email" value="<?= e($settings['from_email']) ?>" class="form-control" placeholder="no-reply@micosage.com">
                    </div>
                    
                    <hr style="border:0; border-top:1px solid rgba(255,255,255,0.05); margin: 20px 0;">
                    
                    <div class="form-group">
                        <label>Email Signature (HTML)</label>
                        <textarea name="signature_html" class="form-control" rows="4"><?= e($settings['signature_html']) ?></textarea>
                        <small style="color:var(--text-muted);">This signature will be appended to all outgoing emails.</small>
                    </div>

                    <div style="margin-top: 20px;">
                        <button type="submit" class="btn btn-primary">Save Configuration</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Column 2: New Campaign -->
    <div class="col-right">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Launch New Campaign</h3>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="send_campaign">
                    <div class="form-group">
                        <label>Campaign Subject</label>
                        <input type="text" name="subject" class="form-control" required placeholder="Our Latest AI Insights">
                    </div>
                    <div class="form-group">
                        <label>Email Body (HTML)</label>
                        <textarea name="body" class="form-control" rows="10" required placeholder="Hello, we are excited to share..."></textarea>
                    </div>
                    <div class="form-group">
                        <label>recipient List (CSV)</label>
                        <input type="file" name="email_list" class="form-control" accept=".csv" required>
                        <small style="color:var(--text-muted);">Upload a CSV file. The first column must contain the email addresses.</small>
                    </div>
                    
                    <div style="margin-top: 25px;">
                        <button type="submit" class="btn btn-neon" onclick="return confirm('Launch campaign to all recipients in the list?')">
                            <i class="lucide-send"></i> Launch Campaign Now
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- History Section -->
<div class="card" style="margin-top: 30px;">
    <div class="card-header">
        <h3 class="card-title">Campaign History</h3>
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
                        <span class="badge badge-<?= $camp['status'] === 'completed' ? 'success' : ($camp['status'] === 'sending' ? 'info' : 'secondary') ?>">
                            <?= ucfirst($camp['status']) ?>
                        </span>
                    </td>
                    <td><?= $camp['sent_count'] ?> / <?= $camp['total_emails'] ?></td>
                    <td><?= date('Y-m-d H:i', strtotime($camp['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($campaigns)): ?>
                <tr><td colspan="5" style="text-align:center;">No campaigns launched yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

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
    gap: 8px;
}
.btn-neon:hover {
    filter: brightness(1.1);
    transform: translateY(-2px);
}
.badge-info { background: var(--neon-cyan); color: #000; }
</style>

<?php
$content = ob_get_clean();
require __DIR__ . '/layouts/admin_layout.php';
?>
