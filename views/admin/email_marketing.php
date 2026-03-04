<?php
$title = "Email Marketing";
$currentPage = 'marketing';
?>
<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <title><?= e($title) ?> — <?= APP_NAME ?></title>
    <?php require __DIR__ . '/partials/_head_assets.php'; ?>
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">

<div class="admin-layout flex w-full h-screen overflow-hidden">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>
    
    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-auto lg:h-20 flex flex-col lg:flex-row items-center justify-between px-4 lg:px-8 bg-glass-bg border-b border-white/5 shrink-0 backdrop-blur-xl sticky top-0 z-[100] py-4 lg:py-0 gap-4 lg:gap-0">
            <div class="flex items-center justify-between w-full lg:w-auto">
                <div class="flex flex-col">
                    <div class="text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] mb-1 hidden sm:block">Communication Matrix</div>
                    <h1 class="text-xl font-black text-white tracking-tight flex items-center gap-3 group">
                        <span class="text-neon-cyan drop-shadow-[0_0_8px_rgba(6,182,212,0.4)]">Email Marketing</span>
                        <span class="opacity-20 translate-y-px hidden sm:inline">/</span>
                        <span class="text-[10px] tracking-widest text-slate-400 uppercase font-black hidden sm:inline-block">Broadcast Engine</span>
                    </h1>
                </div>
                <div class="lg:hidden">
                    <?php require __DIR__ . '/partials/_topbar.php'; ?>
                </div>
            </div>
            
            <div class="hidden lg:block">
                <?php require __DIR__ . '/partials/_topbar.php'; ?>
            </div>
        </header>

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
            <div class="card-header" style="cursor:pointer; display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; gap:10px;" onclick="toggleConfig()">
                <h3 class="card-title" style="font-size:1rem; font-weight:600; margin:0;">⚙️ SMTP / IMAP Configuration</h3>
                <span id="config-toggle-icon" style="font-size:1.2rem; transition: transform 0.3s;">▼</span>
            </div>
            <div id="config-content" style="display:none; padding-top:20px; border-top:1px solid rgba(255,255,255,0.05); margin-top:15px;">
                <form method="POST">
                    <input type="hidden" name="action" value="save_settings">
                    <div class="admin-grid-2" style="margin-bottom:15px; gap:15px;">
                        <div class="form-group">
                            <label style="display:block; font-size:0.8rem; color:var(--text-muted); margin-bottom:5px;">SMTP Host</label>
                            <input type="text" name="smtp_host" value="<?= e($settings['smtp_host']) ?>" class="form-control" placeholder="smtp.example.com" style="width:100%; padding:10px; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); border-radius:8px; color:white;">
                        </div>
                        <div class="form-group">
                            <label style="display:block; font-size:0.8rem; color:var(--text-muted); margin-bottom:5px;">SMTP Port</label>
                            <input type="number" name="smtp_port" value="<?= e($settings['smtp_port']) ?>" class="form-control" placeholder="587" style="width:100%; padding:10px; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); border-radius:8px; color:white;">
                        </div>
                    </div>
                    <div class="admin-grid-2" style="margin-bottom:15px; gap:15px;">
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
                        <button type="submit" class="btn-primary">Save & Verify Configuration</button>
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
                <div class="card-body p-4 sm:p-8">
                    <form id="campaignForm" enctype="multipart/form-data">
                        <div id="campError" style="display:none; background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.2); color:#f43f5e; padding:12px 16px; border-radius:8px; margin-bottom:15px; font-size:0.85rem;"></div>
                        <div class="flex flex-col lg:flex-row gap-8 lg:gap-12">
                            <div class="flex-1 min-w-0">
                                <div class="form-group" style="margin-bottom:15px;">
                                    <label style="display:block; font-size:0.8rem; color:var(--text-muted); margin-bottom:5px;">Campaign Subject</label>
                                    <input type="text" name="subject" id="campSubject" class="form-control" placeholder="Our Latest AI Insights" style="width:100%; padding:10px; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); border-radius:8px; color:white;">
                                </div>
                                <div class="form-group" style="margin-bottom:15px;">
                                    <label style="display:block; font-size:0.8rem; color:var(--text-muted); margin-bottom:5px;">Email Body (HTML)</label>
                                    <textarea name="body" id="campBody" class="form-control" rows="12" placeholder="Hello, we are excited to share..." style="width:100%; padding:10px; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); border-radius:8px; color:white; font-family:inherit;"></textarea>
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

                            <div class="w-full lg:w-80 shrink-0">
                                <div class="form-group" style="margin-bottom:20px;">
                                    <label style="display:block; font-size:0.8rem; color:var(--text-muted); margin-bottom:10px;">Recipient Type</label>
                                    <div style="display:flex; flex-wrap:wrap; gap:12px 20px;">
                                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:0.85rem; color:var(--text-secondary);">
                                            <input type="radio" name="send_type" value="single" checked onclick="toggleRecipientType('single')"> Single / Manual
                                        </label>
                                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:0.85rem; color:var(--text-secondary);">
                                            <input type="radio" name="send_type" value="bulk" onclick="toggleRecipientType('bulk')"> Bulk (CSV)
                                        </label>
                                    </div>
                                </div>

                                <div id="recipient-single" class="form-group" style="margin-bottom:20px;">
                                    <label style="display:block; font-size:0.8rem; color:var(--text-muted); margin-bottom:5px;">Recipient Emails</label>
                                    <textarea name="single_recipients" id="campRecipients" class="form-control" rows="4" placeholder="email1@example.com, email2@example.com" style="width:100%; padding:10px; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); border-radius:8px; color:white;"></textarea>
                                    <small style="color:var(--text-muted); font-size:0.7rem;">Enter one or more emails separated by commas.</small>
                                </div>

                                <div id="recipient-bulk" class="form-group" style="margin-bottom:20px; display:none;">
                                    <label style="display:block; font-size:0.8rem; color:var(--text-muted); margin-bottom:5px;">Recipient List (CSV)</label>
                                    <input type="file" name="email_list" id="campCSV" class="form-control" accept=".csv" style="width:100%; padding:8px; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); border-radius:8px; color:white;">
                                    <small style="color:var(--text-muted); font-size:0.7rem;">Upload a CSV file. The first column must contain the email addresses.</small>
                                </div>

                                <div style="margin-top: 40px; text-align:center;">
                                    <button type="button" id="launchBtn" class="btn btn-neon" style="width:100%; padding:15px;" onclick="launchCampaign()">
                                        <span style="font-size:1.2rem;">🚀</span> Launch Campaign Now
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Progress Modal -->
        <div id="progressModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:9999; backdrop-filter:blur(6px);">
            <div style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); width:90%; max-width:600px; max-height:80vh; background:#0f1117; border:1px solid rgba(255,255,255,0.1); border-radius:16px; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,0.5);">
                <div style="padding:20px 24px; border-bottom:1px solid rgba(255,255,255,0.06); display:flex; align-items:center; justify-content:space-between;">
                    <h3 style="margin:0; color:white; font-size:1.1rem;">📧 Sending Campaign</h3>
                    <span id="progressCounter" style="color:var(--neon-cyan); font-size:0.9rem; font-weight:600;">0 / 0</span>
                </div>
                <div style="padding:16px 24px;">
                    <!-- Progress Bar -->
                    <div style="background:rgba(255,255,255,0.06); border-radius:10px; height:8px; overflow:hidden; margin-bottom:16px;">
                        <div id="progressBar" style="width:0%; height:100%; background:linear-gradient(90deg, var(--neon-emerald), var(--neon-cyan)); border-radius:10px; transition:width 0.3s ease;"></div>
                    </div>
                    <div style="display:flex; gap:16px; margin-bottom:16px;">
                        <span style="font-size:0.8rem; color:var(--text-muted);">✅ Sent: <strong id="sentCount" style="color:var(--neon-emerald);">0</strong></span>
                        <span style="font-size:0.8rem; color:var(--text-muted);">❌ Failed: <strong id="failCount" style="color:var(--neon-pink);">0</strong></span>
                        <span style="font-size:0.8rem; color:var(--text-muted);">⏳ Pending: <strong id="pendingCount" style="color:var(--neon-cyan);">0</strong></span>
                    </div>
                </div>
                <!-- Log area -->
                <div id="progressLog" style="max-height:300px; overflow-y:auto; padding:0 24px 16px; font-family:'Inter',monospace; font-size:0.78rem; line-height:1.8;"></div>
                <div id="progressFooter" style="display:none; padding:16px 24px; border-top:1px solid rgba(255,255,255,0.06); text-align:center;">
                    <button onclick="closeProgress()" class="btn-primary">✓ Done — Close</button>
                </div>
            </div>
        </div>

        <!-- History Section -->
        <div class="admin-card" style="margin-top: 30px;">
            <div class="card-header" style="margin-bottom:20px;">
                <h3 class="card-title" style="font-size:1.1rem; font-weight:600;">📜 Campaign History</h3>
            </div>
            <div class="card-body">
                <div style="overflow-x: auto;">
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
</div>

<script>
const API_URL = '<?= baseUrl("api/campaign_send.php") ?>';

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
    document.getElementById('recipient-single').style.display = type === 'single' ? 'block' : 'none';
    document.getElementById('recipient-bulk').style.display = type === 'bulk' ? 'block' : 'none';
}

async function launchCampaign() {
    const errEl = document.getElementById('campError');
    errEl.style.display = 'none';

    const subject = document.getElementById('campSubject').value.trim();
    const body = document.getElementById('campBody').value.trim();
    const sendType = document.querySelector('input[name="send_type"]:checked').value;

    // Visible validation
    if (!subject) { showCampError('Please enter a campaign subject.'); return; }
    if (!body) { showCampError('Please enter the email body.'); return; }

    const btn = document.getElementById('launchBtn');

    // Inline confirm to avoid browser popup blockers
    if (btn.dataset.confirmed !== 'true') {
        btn.innerHTML = '<span style="font-size:1.2rem;">⚠️</span> Click again to confirm launch';
        btn.dataset.confirmed = 'true';
        btn.style.background = '#f59e0b'; // warning color
        btn.style.color = '#000';
        
        // Reset after 4 seconds
        setTimeout(() => {
            if (!btn.disabled) {
                btn.dataset.confirmed = 'false';
                btn.innerHTML = '<span style="font-size:1.2rem;">🚀</span> Launch Campaign Now';
                btn.style.background = '';
                btn.style.color = '';
            }
        }, 4000);
        return;
    }

    btn.dataset.confirmed = 'false';
    btn.style.background = '';
    btn.style.color = '';
    btn.disabled = true;
    btn.innerHTML = '⏳ Preparing...';

    try {
        // Step 1: Create campaign
        const fd = new FormData();
        fd.append('action', 'create_campaign');
        fd.append('subject', subject);
        fd.append('body', body);
        fd.append('send_type', sendType);
        
        if (sendType === 'single') {
            fd.append('recipients', document.getElementById('campRecipients').value);
        } else {
            const csvFile = document.getElementById('campCSV').files[0];
            if (csvFile) fd.append('email_list', csvFile);
        }

        const createRes = await fetch(API_URL, { method: 'POST', body: fd });
        const createData = await createRes.json();

        if (createData.error) {
            showCampError(createData.error);
            btn.disabled = false;
            btn.innerHTML = '<span style="font-size:1.2rem;">🚀</span> Launch Campaign Now';
            return;
        }

        const { campaign_id, emails, total } = createData;

        // Show progress modal
        showProgress(total);

        // Step 2: Send emails one by one
        let sent = 0, failed = 0;
        for (let i = 0; i < emails.length; i++) {
            const email = emails[i];
            updatePending(total - i - 1);
            addLog(`Sending to ${email}...`, 'pending');

            try {
                const sendFd = new FormData();
                sendFd.append('action', 'send_one');
                sendFd.append('campaign_id', campaign_id);
                sendFd.append('email', email);

                const sendRes = await fetch(API_URL, { method: 'POST', body: sendFd });
                const sendData = await sendRes.json();

                if (sendData.status === 'sent') {
                    sent++;
                    updateLastLog(`✅ ${email} — Sent`, 'success');
                } else {
                    failed++;
                    updateLastLog(`❌ ${email} — Failed`, 'error');
                }
            } catch (err) {
                failed++;
                updateLastLog(`❌ ${email} — Network Error`, 'error');
            }

            updateProgress(i + 1, total, sent, failed);
        }

        // Step 3: Finalize
        const finFd = new FormData();
        finFd.append('action', 'finalize');
        finFd.append('campaign_id', campaign_id);
        await fetch(API_URL, { method: 'POST', body: finFd });

        addLog(`\n✓ Campaign complete: ${sent} sent, ${failed} failed.`, 'done');
        document.getElementById('progressFooter').style.display = 'block';

    } catch (err) {
        showCampError('Unexpected error: ' + err.message);
    }

    btn.disabled = false;
    btn.innerHTML = '<span style="font-size:1.2rem;">🚀</span> Launch Campaign Now';
}

function showCampError(msg) {
    const el = document.getElementById('campError');
    el.textContent = msg;
    el.style.display = 'block';
    el.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function showProgress(total) {
    document.getElementById('progressModal').style.display = 'block';
    document.getElementById('progressCounter').textContent = `0 / ${total}`;
    document.getElementById('progressBar').style.width = '0%';
    document.getElementById('sentCount').textContent = '0';
    document.getElementById('failCount').textContent = '0';
    document.getElementById('pendingCount').textContent = total;
    document.getElementById('progressLog').innerHTML = '';
    document.getElementById('progressFooter').style.display = 'none';
}

function updateProgress(current, total, sent, failed) {
    document.getElementById('progressCounter').textContent = `${current} / ${total}`;
    document.getElementById('progressBar').style.width = `${(current / total * 100)}%`;
    document.getElementById('sentCount').textContent = sent;
    document.getElementById('failCount').textContent = failed;
}

function updatePending(n) {
    document.getElementById('pendingCount').textContent = n;
}

function addLog(msg, type) {
    const log = document.getElementById('progressLog');
    const div = document.createElement('div');
    div.className = 'log-' + type;
    const colors = { pending: '#64748b', success: '#10b981', error: '#f43f5e', done: '#22d3ee' };
    div.style.color = colors[type] || '#94a3b8';
    div.textContent = msg;
    log.appendChild(div);
    log.scrollTop = log.scrollHeight;
}

function updateLastLog(msg, type) {
    const log = document.getElementById('progressLog');
    const last = log.lastElementChild;
    if (last) {
        const colors = { success: '#10b981', error: '#f43f5e' };
        last.style.color = colors[type] || '#94a3b8';
        last.textContent = msg;
    }
}

function closeProgress() {
    document.getElementById('progressModal').style.display = 'none';
    window.location.reload();
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
.btn-neon:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
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
#progressLog::-webkit-scrollbar { width: 4px; }
#progressLog::-webkit-scrollbar-track { background: transparent; }
#progressLog::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 4px; }
</style>

</body>
</html>
