-- ═══ Email Settings (SMTP/IMAP) ═══
CREATE TABLE IF NOT EXISTS email_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    provider_name VARCHAR(100) DEFAULT 'Primary SMTP',
    smtp_host VARCHAR(255) DEFAULT '',
    smtp_port INT DEFAULT 587,
    smtp_user VARCHAR(255) DEFAULT '',
    smtp_pass VARCHAR(255) DEFAULT '',
    smtp_encryption ENUM('none', 'ssl', 'tls') DEFAULT 'tls',
    from_email VARCHAR(255) DEFAULT '',
    from_name VARCHAR(255) DEFAULT '',
    imap_host VARCHAR(255) DEFAULT '',
    imap_port INT DEFAULT 993,
    signature_html TEXT DEFAULT '',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ═══ Marketing Campaigns / Mailing Lists ═══
CREATE TABLE IF NOT EXISTS marketing_campaigns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    status ENUM('draft', 'sending', 'completed', 'failed') DEFAULT 'draft',
    total_emails INT DEFAULT 0,
    sent_count INT DEFAULT 0,
    failed_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS marketing_recipients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    campaign_id INT,
    email VARCHAR(255) NOT NULL,
    name VARCHAR(255) DEFAULT '',
    status ENUM('pending', 'sent', 'failed') DEFAULT 'pending',
    error_message TEXT DEFAULT '',
    sent_at DATETIME DEFAULT NULL,
    FOREIGN KEY (campaign_id) REFERENCES marketing_campaigns(id) ON DELETE CASCADE
);

-- Seed default empty settings if not exists
INSERT INTO email_settings (id, from_name) VALUES (1, 'Mico Sage Team') 
ON DUPLICATE KEY UPDATE from_name = from_name;
