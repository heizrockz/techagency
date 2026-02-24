-- Mico Sage Tech Agency — Database Setup
-- Run this in phpMyAdmin or MySQL CLI

CREATE DATABASE IF NOT EXISTS tech_agency CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tech_agency;

-- Admin users
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bookings from clients
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    email VARCHAR(200) NOT NULL,
    phone VARCHAR(50) DEFAULT '',
    service VARCHAR(100) NOT NULL,
    message TEXT,
    preferred_date DATE DEFAULT NULL,
    status ENUM('new','viewed','contacted','completed','cancelled') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Dynamic page content (editable from admin)
CREATE TABLE IF NOT EXISTS contents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_key VARCHAR(100) NOT NULL,
    locale VARCHAR(5) NOT NULL DEFAULT 'en',
    value TEXT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_section_locale (section_key, locale)
);

-- SEO meta tags per page
CREATE TABLE IF NOT EXISTS seo_meta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page VARCHAR(50) NOT NULL,
    locale VARCHAR(5) NOT NULL DEFAULT 'en',
    title VARCHAR(255) DEFAULT '',
    description TEXT DEFAULT NULL,
    keywords VARCHAR(500) DEFAULT '',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_page_locale (page, locale)
);

-- Insert default admin (password: admin123)
INSERT INTO admins (username, password) VALUES
('admin', '$2y$10$ruXfiawURcZYOnjzKnve6Ob2sVNfqnIN2LHHOIWwwt9bwXIbFVsZq');

-- Insert default SEO
INSERT INTO seo_meta (page, locale, title, description, keywords) VALUES
('home', 'en', 'Mico Sage | Web Development, Windows Apps & Digital Marketing', 'Premium tech agency specializing in web engineering, Windows desktop applications, and digital marketing solutions.', 'web development, windows apps, digital marketing, tech agency'),
('home', 'ar', 'ميكو سيج | تطوير المواقع وتطبيقات ويندوز والتسويق الرقمي', 'وكالة تقنية متميزة متخصصة في هندسة الويب وتطبيقات سطح المكتب لنظام ويندوز وحلول التسويق الرقمي.', 'تطوير مواقع, تطبيقات ويندوز, تسويق رقمي, وكالة تقنية');

-- Insert default content
INSERT INTO contents (section_key, locale, value) VALUES
('hero_title', 'en', 'We Build The Future'),
('hero_title', 'ar', 'نحن نبني المستقبل'),
('hero_subtitle', 'en', 'Premium Web Engineering · Windows Desktop Apps · Digital Growth'),
('hero_subtitle', 'ar', 'هندسة ويب متميزة · تطبيقات سطح المكتب · النمو الرقمي'),
('about_title', 'en', 'Why Choose Mico Sage?'),
('about_title', 'ar', 'لماذا تختار ميكو سيج؟'),
('about_text', 'en', 'We are a forward-thinking tech agency that combines cutting-edge design with robust engineering. Our team delivers world-class digital products that propel your business into the future.'),
('about_text', 'ar', 'نحن وكالة تقنية مبتكرة تجمع بين التصميم المتطور والهندسة القوية. يقدم فريقنا منتجات رقمية عالمية المستوى تدفع أعمالك نحو المستقبل.'),
('service_web_title', 'en', 'Web Engineering'),
('service_web_title', 'ar', 'هندسة الويب'),
('service_web_desc', 'en', 'Full-stack web applications built with modern frameworks, responsive designs, and pixel-perfect interfaces that convert visitors into customers.'),
('service_web_desc', 'ar', 'تطبيقات ويب متكاملة مبنية بأحدث الأطر والتصاميم المتجاوبة وواجهات مثالية تحول الزوار إلى عملاء.'),
('service_windows_title', 'en', 'Windows Desktop Apps'),
('service_windows_title', 'ar', 'تطبيقات سطح المكتب'),
('service_windows_desc', 'en', 'Native Windows applications with sleek UIs, powerful performance, and seamless integration with your business workflows.'),
('service_windows_desc', 'ar', 'تطبيقات ويندوز أصلية بواجهات أنيقة وأداء قوي وتكامل سلس مع سير عمل شركتك.'),
('service_marketing_title', 'en', 'Digital Growth'),
('service_marketing_title', 'ar', 'النمو الرقمي'),
('service_marketing_desc', 'en', 'Data-driven digital marketing strategies including SEO, social media, PPC, and brand identity that amplify your online presence.'),
('service_marketing_desc', 'ar', 'استراتيجيات تسويق رقمي مبنية على البيانات تشمل تحسين محركات البحث والتواصل الاجتماعي والإعلانات المدفوعة وهوية العلامة التجارية.'),
('booking_title', 'en', 'Book a Consultation'),
('booking_title', 'ar', 'احجز استشارة'),
('booking_subtitle', 'en', 'Let us bring your vision to life. No login required.'),
('booking_subtitle', 'ar', 'دعنا نحول رؤيتك إلى واقع. لا حاجة لتسجيل الدخول.'),
('footer_text', 'en', '© 2026 Mico Sage. All rights reserved.'),
('footer_text', 'ar', '© 2026 ميكو سيج. جميع الحقوق محفوظة.');

-- ═══════════════════════════════════════════════════════════
-- Invoices & Quotes
-- ═══════════════════════════════════════════════════════════
CREATE TABLE IF NOT EXISTS invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('invoice', 'quote') DEFAULT 'invoice',
    invoice_number VARCHAR(50) NOT NULL UNIQUE,
    client_name VARCHAR(200) NOT NULL,
    client_email VARCHAR(200) DEFAULT '',
    client_phone VARCHAR(50) DEFAULT '',
    client_address VARCHAR(500) DEFAULT '',
    discount DECIMAL(10,2) DEFAULT 0.00,
    vat_rate DECIMAL(5,2) DEFAULT 0.00,
    status ENUM('draft', 'sent', 'paid', 'cancelled') DEFAULT 'draft',
    notes TEXT,
    terms TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS invoice_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_id INT NOT NULL,
    service_name VARCHAR(200) NOT NULL,
    description TEXT,
    qty DECIMAL(10,2) DEFAULT 1.00,
    unit_price DECIMAL(10,2) DEFAULT 0.00,
    vat_rate DECIMAL(5,2) DEFAULT 0.00,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE
);

-- ═══════════════════════════════════════════════════════════
-- Chatbot Inbox
-- ═══════════════════════════════════════════════════════════
CREATE TABLE IF NOT EXISTS chatbot_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_uuid VARCHAR(100) NOT NULL UNIQUE,
    user_ip VARCHAR(50) DEFAULT '',
    user_agent VARCHAR(500) DEFAULT '',
    status ENUM('active', 'closed') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS chatbot_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id INT NOT NULL,
    sender ENUM('bot', 'user') NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (session_id) REFERENCES chatbot_sessions(id) ON DELETE CASCADE
);
