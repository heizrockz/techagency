-- ═══════════════════════════════════════════════════════════════
-- Mico Sage Tech Agency — Complete Database Migration
-- Run this single file to set up the entire database from scratch
-- ═══════════════════════════════════════════════════════════════

CREATE DATABASE IF NOT EXISTS tech_agency CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tech_agency;
SET NAMES utf8mb4;

-- ═══ Admin users ═══
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ═══ Client bookings ═══
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    email VARCHAR(200) NOT NULL,
    phone VARCHAR(50) DEFAULT '',
    service VARCHAR(100) NOT NULL,
    message TEXT,
    preferred_date DATE DEFAULT NULL,
    status ENUM('new','viewed','contacted','completed','cancelled') DEFAULT 'new',
    extra_fields JSON DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ═══ Dynamic page content (editable from admin) ═══
CREATE TABLE IF NOT EXISTS contents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_key VARCHAR(100) NOT NULL,
    locale VARCHAR(5) NOT NULL DEFAULT 'en',
    value TEXT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_section_locale (section_key, locale)
);

-- ═══ SEO meta tags per page ═══
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

-- ═══ Dynamic Services (admin-managed) ═══
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    icon VARCHAR(50) DEFAULT 'code',
    color VARCHAR(50) DEFAULT 'cobalt',
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS service_translations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_id INT NOT NULL,
    locale VARCHAR(5) NOT NULL DEFAULT 'en',
    title VARCHAR(200) NOT NULL,
    description TEXT,
    UNIQUE KEY uk_service_locale (service_id, locale),
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
);

-- ═══ Site Settings (key-value pairs) ═══
CREATE TABLE IF NOT EXISTS site_settings (
    setting_key VARCHAR(100) PRIMARY KEY,
    setting_value TEXT NOT NULL,
    setting_type VARCHAR(20) DEFAULT 'text',
    setting_group VARCHAR(50) DEFAULT 'general',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ═══ Our Clients ═══
CREATE TABLE IF NOT EXISTS clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    logo_url VARCHAR(500) DEFAULT '',
    website_url VARCHAR(500) DEFAULT '',
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ═══ Products / Ideas ═══
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    icon VARCHAR(50) DEFAULT 'globe',
    category ENUM('website','app','maintenance') DEFAULT 'website',
    color VARCHAR(50) DEFAULT 'cobalt',
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS product_translations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    locale VARCHAR(5) NOT NULL DEFAULT 'en',
    title VARCHAR(200) NOT NULL,
    description TEXT,
    UNIQUE KEY uk_product_locale (product_id, locale),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- ═══ Dynamic Booking Form Fields ═══
CREATE TABLE IF NOT EXISTS booking_fields (
    id INT AUTO_INCREMENT PRIMARY KEY,
    field_name VARCHAR(100) NOT NULL,
    field_type ENUM('text','email','tel','date','select','textarea','number') DEFAULT 'text',
    options TEXT DEFAULT NULL,
    is_required TINYINT(1) DEFAULT 0,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1
);

CREATE TABLE IF NOT EXISTS booking_field_translations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    field_id INT NOT NULL,
    locale VARCHAR(5) NOT NULL DEFAULT 'en',
    label VARCHAR(200) NOT NULL,
    placeholder VARCHAR(200) DEFAULT '',
    UNIQUE KEY uk_field_locale (field_id, locale),
    FOREIGN KEY (field_id) REFERENCES booking_fields(id) ON DELETE CASCADE
);

-- ═══ Admin-managed Translations ═══
CREATE TABLE IF NOT EXISTS translations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trans_key VARCHAR(200) NOT NULL,
    locale VARCHAR(5) NOT NULL DEFAULT 'en',
    trans_value TEXT NOT NULL,
    trans_group VARCHAR(50) DEFAULT 'general',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_trans_key_locale (trans_key, locale)
);

-- ═══ Portfolio Projects ═══
CREATE TABLE IF NOT EXISTS portfolio_projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(200) NOT NULL UNIQUE,
    image_url VARCHAR(500) DEFAULT '',
    demo_url VARCHAR(500) DEFAULT '',
    category ENUM('website','app','branding','marketing') DEFAULT 'website',
    color VARCHAR(50) DEFAULT 'cobalt',
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    is_featured TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS portfolio_project_translations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    locale VARCHAR(5) NOT NULL DEFAULT 'en',
    title VARCHAR(200) NOT NULL,
    description TEXT,
    client_name VARCHAR(200) DEFAULT '',
    tags VARCHAR(500) DEFAULT '',
    UNIQUE KEY uk_project_locale (project_id, locale),
    FOREIGN KEY (project_id) REFERENCES portfolio_projects(id) ON DELETE CASCADE
);

-- ═══ Chatbot Nodes ═══
CREATE TABLE IF NOT EXISTS chatbot_nodes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    is_root TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS chatbot_node_translations (
    node_id INT NOT NULL,
    locale VARCHAR(5) NOT NULL DEFAULT 'en',
    message TEXT NOT NULL,
    PRIMARY KEY (node_id, locale),
    FOREIGN KEY (node_id) REFERENCES chatbot_nodes(id) ON DELETE CASCADE
);

-- ═══ Chatbot Options ═══
CREATE TABLE IF NOT EXISTS chatbot_options (
    id INT AUTO_INCREMENT PRIMARY KEY,
    node_id INT NOT NULL,
    next_node_id INT DEFAULT NULL,
    action_type ENUM('goto_node', 'link', 'call') DEFAULT 'goto_node',
    action_value VARCHAR(255) DEFAULT '',
    sort_order INT DEFAULT 0,
    FOREIGN KEY (node_id) REFERENCES chatbot_nodes(id) ON DELETE CASCADE,
    FOREIGN KEY (next_node_id) REFERENCES chatbot_nodes(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS chatbot_option_translations (
    option_id INT NOT NULL,
    locale VARCHAR(5) NOT NULL DEFAULT 'en',
    label VARCHAR(200) NOT NULL,
    PRIMARY KEY (option_id, locale),
    FOREIGN KEY (option_id) REFERENCES chatbot_options(id) ON DELETE CASCADE
);


-- ═══════════════════════════════════════════════════════════════
-- SEED DATA
-- ═══════════════════════════════════════════════════════════════

-- Default admin (username: admin, password: admin123)
INSERT INTO admins (username, password) VALUES
('admin', '$2y$10$aHK7b.PZcYoSqTQqDNe.huZzoJESswkvSJIxHlwwl0K7/pMmfDLPu')
ON DUPLICATE KEY UPDATE password = VALUES(password);

-- Default SEO
INSERT INTO seo_meta (page, locale, title, description, keywords) VALUES
('home', 'en', 'Mico Sage | Web Development, Windows Apps & Digital Marketing', 'Premium tech agency specializing in web engineering, Windows desktop applications, and digital marketing solutions.', 'web development, windows apps, digital marketing, tech agency'),
('home', 'ar', 'ميكو سيج | تطوير المواقع وتطبيقات ويندوز والتسويق الرقمي', 'وكالة تقنية متميزة متخصصة في هندسة الويب وتطبيقات سطح المكتب لنظام ويندوز وحلول التسويق الرقمي.', 'تطوير مواقع, تطبيقات ويندوز, تسويق رقمي, وكالة تقنية')
ON DUPLICATE KEY UPDATE title = VALUES(title), description = VALUES(description), keywords = VALUES(keywords);

-- Default content
INSERT INTO contents (section_key, locale, value) VALUES
('hero_title', 'en', 'We Build The Future'),
('hero_title', 'ar', 'نحن نبني المستقبل'),
('hero_subtitle', 'en', 'Premium Web Engineering · Windows Desktop Apps · Digital Growth · Creative Solutions'),
('hero_subtitle', 'ar', 'هندسة ويب متميزة · تطبيقات سطح المكتب · النمو الرقمي · حلول إبداعية'),
('about_title', 'en', 'Why Choose Mico Sage?'),
('about_title', 'ar', 'لماذا تختار ميكو سيج؟'),
('about_text', 'en', 'We are a forward-thinking tech agency that combines cutting-edge design with robust engineering. Our team delivers world-class digital products that propel your business into the future. From concept to launch, we handle every pixel and every line of code with precision.'),
('about_text', 'ar', 'نحن وكالة تقنية مبتكرة تجمع بين التصميم المتطور والهندسة القوية. يقدم فريقنا منتجات رقمية عالمية المستوى تدفع أعمالك نحو المستقبل. من الفكرة إلى الإطلاق، نتعامل مع كل بكسل وكل سطر كود بدقة.'),
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
('footer_text', 'ar', '© 2026 ميكو سيج. جميع الحقوق محفوظة.'),
('clients_title', 'en', 'Trusted By Industry Leaders'),
('clients_title', 'ar', 'موثوق من قبل قادة الصناعة'),
('products_title', 'en', 'Ready-Made Solutions'),
('products_title', 'ar', 'حلول جاهزة'),
('products_subtitle', 'en', 'Pre-built platforms customized to your brand. Launch faster, grow smarter.'),
('products_subtitle', 'ar', 'منصات جاهزة مخصصة لعلامتك التجارية. انطلق أسرع، انمُ بذكاء.'),
('marketing_title', 'en', 'Digital Marketing That Delivers Results'),
('marketing_title', 'ar', 'تسويق رقمي يحقق النتائج'),
('marketing_subtitle', 'en', 'We don''t just market — we engineer growth. Data-driven strategies that turn clicks into customers.'),
('marketing_subtitle', 'ar', 'نحن لا نسوق فقط — بل نهندس النمو. استراتيجيات مبنية على البيانات تحول النقرات إلى عملاء.'),
('marketing_seo_title', 'en', 'SEO Optimization'),
('marketing_seo_title', 'ar', 'تحسين محركات البحث'),
('marketing_seo_desc', 'en', 'Dominate search rankings with white-hat SEO strategies, technical audits, and content optimization.'),
('marketing_seo_desc', 'ar', 'تصدر نتائج البحث باستراتيجيات SEO أخلاقية وتدقيق تقني وتحسين المحتوى.'),
('marketing_social_title', 'en', 'Social Media Marketing'),
('marketing_social_title', 'ar', 'التسويق عبر التواصل الاجتماعي'),
('marketing_social_desc', 'en', 'Build a loyal community with engaging content, strategic campaigns, and influencer partnerships.'),
('marketing_social_desc', 'ar', 'ابنِ مجتمعاً مخلصاً بمحتوى جذاب وحملات استراتيجية وشراكات مع المؤثرين.'),
('marketing_ppc_title', 'en', 'PPC & Paid Ads'),
('marketing_ppc_title', 'ar', 'الإعلانات المدفوعة'),
('marketing_ppc_desc', 'en', 'Maximize ROI with precision-targeted Google Ads, Meta Ads, and programmatic advertising campaigns.'),
('marketing_ppc_desc', 'ar', 'حقق أقصى عائد استثمار بإعلانات جوجل وميتا المستهدفة بدقة والحملات الإعلانية البرمجية.'),
('marketing_brand_title', 'en', 'Brand Identity'),
('marketing_brand_title', 'ar', 'هوية العلامة التجارية'),
('marketing_brand_desc', 'en', 'Craft a premium brand identity with logo design, brand guidelines, and visual storytelling that resonates.'),
('marketing_brand_desc', 'ar', 'اصنع هوية علامة تجارية متميزة بتصميم شعار وإرشادات العلامة والسرد البصري المؤثر.')
ON DUPLICATE KEY UPDATE value = VALUES(value);

-- Default Settings
INSERT INTO site_settings (setting_key, setting_value, setting_type, setting_group) VALUES
('site_name', 'Mico Sage', 'text', 'branding'),
('site_tagline_en', 'Defying Digital Limits', 'text', 'branding'),
('site_tagline_ar', 'نتحدى الحدود الرقمية', 'text', 'branding'),
('stat_projects_num', '150+', 'text', 'stats'),
('stat_projects_label_en', 'Projects Delivered', 'text', 'stats'),
('stat_projects_label_ar', 'مشروع منجز', 'text', 'stats'),
('stat_clients_num', '50+', 'text', 'stats'),
('stat_clients_label_en', 'Happy Clients', 'text', 'stats'),
('stat_clients_label_ar', 'عميل سعيد', 'text', 'stats'),
('stat_years_num', '8+', 'text', 'stats'),
('stat_years_label_en', 'Years Experience', 'text', 'stats'),
('stat_years_label_ar', 'سنوات خبرة', 'text', 'stats'),
('show_clients_section', '1', 'boolean', 'sections'),
('show_products_section', '1', 'boolean', 'sections'),
('show_stats_section', '1', 'boolean', 'sections'),
('show_marketing_section', '1', 'boolean', 'sections'),
('contact_phone', '+971 50 123 4567', 'text', 'contact'),
('contact_email', 'hello@micosage.com', 'text', 'contact'),
('primary_color', '#3b82f6', 'color', 'branding'),
('secondary_color', '#8b5cf6', 'color', 'branding')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

-- Default Services
INSERT INTO services (id, icon, color, sort_order) VALUES
(1, 'code', 'cobalt', 1),
(2, 'monitor', 'violet', 2),
(3, 'chart', 'emerald', 3)
ON DUPLICATE KEY UPDATE icon = VALUES(icon), color = VALUES(color), sort_order = VALUES(sort_order);

INSERT INTO service_translations (service_id, locale, title, description) VALUES
(1, 'en', 'Web Engineering', 'Full-stack web applications built with modern frameworks, responsive designs, and pixel-perfect interfaces that convert visitors into customers.'),
(1, 'ar', 'هندسة الويب', 'تطبيقات ويب متكاملة مبنية بأحدث الأطر والتصاميم المتجاوبة وواجهات مثالية تحول الزوار إلى عملاء.'),
(2, 'en', 'Windows Desktop Apps', 'Native Windows applications with sleek UIs, powerful performance, and seamless integration with your business workflows.'),
(2, 'ar', 'تطبيقات سطح المكتب', 'تطبيقات ويندوز أصلية بواجهات أنيقة وأداء قوي وتكامل سلس مع سير عمل شركتك.'),
(3, 'en', 'Digital Growth', 'Data-driven digital marketing strategies including SEO, social media, PPC, and brand identity that amplify your online presence.'),
(3, 'ar', 'النمو الرقمي', 'استراتيجيات تسويق رقمي مبنية على البيانات تشمل تحسين محركات البحث والتواصل الاجتماعي والإعلانات المدفوعة وهوية العلامة التجارية.')
ON DUPLICATE KEY UPDATE title = VALUES(title), description = VALUES(description);

-- Default Products
INSERT INTO products (id, icon, category, color, sort_order) VALUES
(1, 'car', 'website', 'cobalt', 1),
(2, 'cart', 'website', 'violet', 2),
(3, 'hotel', 'website', 'cyan', 3),
(4, 'billing', 'app', 'emerald', 4),
(5, 'crm', 'app', 'pink', 5),
(6, 'wrench', 'maintenance', 'orange', 6)
ON DUPLICATE KEY UPDATE icon = VALUES(icon), category = VALUES(category), color = VALUES(color), sort_order = VALUES(sort_order);

INSERT INTO product_translations (product_id, locale, title, description) VALUES
(1, 'en', 'Car Rental Platform', 'Complete car rental management system with booking engine, fleet management, and payment integration.'),
(1, 'ar', 'منصة تأجير السيارات', 'نظام متكامل لإدارة تأجير السيارات مع محرك حجوزات وإدارة أسطول وتكامل مدفوعات.'),
(2, 'en', 'E-Commerce Store', 'Feature-rich online store with product management, shopping cart, payment gateways, and analytics dashboard.'),
(2, 'ar', 'متجر إلكتروني', 'متجر إلكتروني غني بالميزات مع إدارة المنتجات وعربة التسوق وبوابات الدفع ولوحة التحليلات.'),
(3, 'en', 'Hotel Booking System', 'Modern hotel reservation platform with room management, dynamic pricing, and guest portal.'),
(3, 'ar', 'نظام حجز فنادق', 'منصة حجز فنادق حديثة مع إدارة الغرف والتسعير الديناميكي وبوابة الضيوف.'),
(4, 'en', 'Billing & Invoice App', 'Streamlined billing application with invoice generation, recurring payments, and financial reporting.'),
(4, 'ar', 'تطبيق الفواتير', 'تطبيق فوترة متطور مع إصدار الفواتير والمدفوعات المتكررة والتقارير المالية.'),
(5, 'en', 'CRM System', 'Customer relationship management with lead tracking, pipeline management, and automated workflows.'),
(5, 'ar', 'نظام إدارة العملاء', 'إدارة علاقات العملاء مع تتبع العملاء المحتملين وإدارة المبيعات وسير العمل الآلي.'),
(6, 'en', 'Website Maintenance', 'Ongoing website maintenance, security updates, performance optimization, and content management support.'),
(6, 'ar', 'صيانة المواقع', 'صيانة مستمرة للمواقع وتحديثات الأمان وتحسين الأداء ودعم إدارة المحتوى.')
ON DUPLICATE KEY UPDATE title = VALUES(title), description = VALUES(description);

-- Default Booking Fields
INSERT INTO booking_fields (id, field_name, field_type, is_required, sort_order, is_active) VALUES
(1, 'name', 'text', 1, 1, 1),
(2, 'email', 'email', 1, 2, 1),
(3, 'phone', 'tel', 0, 3, 1),
(4, 'service', 'select', 1, 4, 1),
(5, 'preferred_date', 'date', 0, 5, 1),
(6, 'message', 'textarea', 0, 6, 1)
ON DUPLICATE KEY UPDATE field_name = VALUES(field_name), field_type = VALUES(field_type);

INSERT INTO booking_field_translations (field_id, locale, label, placeholder) VALUES
(1, 'en', 'Full Name', 'Enter your full name'),
(1, 'ar', 'الاسم الكامل', 'أدخل اسمك الكامل'),
(2, 'en', 'Email Address', 'Enter your email'),
(2, 'ar', 'البريد الإلكتروني', 'أدخل بريدك الإلكتروني'),
(3, 'en', 'Phone Number', 'Enter your phone number'),
(3, 'ar', 'رقم الهاتف', 'أدخل رقم هاتفك'),
(4, 'en', 'Select Service', 'Choose a service'),
(4, 'ar', 'اختر الخدمة', 'اختر خدمة'),
(5, 'en', 'Preferred Date', 'Select a date'),
(5, 'ar', 'التاريخ المفضل', 'اختر تاريخاً'),
(6, 'en', 'Project Details', 'Tell us about your project'),
(6, 'ar', 'تفاصيل المشروع', 'أخبرنا عن مشروعك')
ON DUPLICATE KEY UPDATE label = VALUES(label), placeholder = VALUES(placeholder);

-- Default Clients
INSERT INTO clients (id, name, logo_url, sort_order, is_active) VALUES
(1, 'TechCorp International', '', 1, 1),
(2, 'Global Solutions Ltd', '', 2, 1),
(3, 'Innovation Hub', '', 3, 1),
(4, 'Digital Ventures', '', 4, 1),
(5, 'SmartBiz Group', '', 5, 1)
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Default Portfolio Projects
INSERT INTO portfolio_projects (id, slug, image_url, demo_url, category, color, sort_order, is_active, is_featured) VALUES
(1, 'al-riyada-ecommerce', '', '', 'website', 'cobalt', 1, 1, 1),
(2, 'noir-perfumes', '', '', 'website', 'violet', 2, 1, 0),
(3, 'vogue-models-agency', '', '', 'website', 'pink', 3, 1, 0),
(4, 'fleettrack-pro', '', '', 'app', 'emerald', 4, 1, 0),
(5, 'al-maskan-hotels', '', '', 'website', 'cyan', 5, 1, 0),
(6, 'digital-bloom-campaign', '', '', 'marketing', 'orange', 6, 1, 0)
ON DUPLICATE KEY UPDATE slug = VALUES(slug), category = VALUES(category), color = VALUES(color), sort_order = VALUES(sort_order);

INSERT INTO portfolio_project_translations (project_id, locale, title, description, client_name, tags) VALUES
(1, 'en', 'Al-Riyada E-Commerce', 'A full-featured e-commerce platform with real-time inventory management, multi-currency checkout, AI-powered product recommendations, and a sleek responsive storefront that boosted conversions by 40%.', 'Al-Riyada Trading Co.', 'Laravel,Vue.js,Stripe,MySQL,Redis'),
(1, 'ar', 'منصة الريادة للتجارة الإلكترونية', 'منصة تجارة إلكترونية متكاملة مع إدارة مخزون فورية ودفع متعدد العملات وتوصيات منتجات بالذكاء الاصطناعي وواجهة متجاوبة أنيقة رفعت التحويلات بنسبة 40%.', 'شركة الريادة التجارية', 'Laravel,Vue.js,Stripe,MySQL,Redis'),
(2, 'en', 'Noir Perfumes', 'Luxury perfume brand website with immersive 3D product visualization, scent-matching quiz, subscription box builder, and an elegant dark-themed UI that mirrors the brand''s premium identity.', 'Noir Fragrances LLC', 'Next.js,Three.js,Tailwind CSS,PostgreSQL'),
(2, 'ar', 'عطور نوار', 'موقع علامة عطور فاخرة مع عرض ثلاثي الأبعاد للمنتجات واختبار مطابقة العطور ومنشئ صناديق الاشتراك وواجهة أنيقة داكنة تعكس هوية العلامة المتميزة.', 'نوار للعطور', 'Next.js,Three.js,Tailwind CSS,PostgreSQL'),
(3, 'en', 'Vogue Models Agency', 'Model management platform with digital portfolios, casting call boards, availability calendars, and a dynamic gallery showcasing talent with smooth animations and video integration.', 'Vogue Agency International', 'React,Node.js,MongoDB,AWS S3,FFmpeg'),
(3, 'ar', 'وكالة فوغ للعارضات', 'منصة إدارة عارضات مع ملفات رقمية ولوحات اختيار ممثلين وتقويم التوفر ومعرض ديناميكي يعرض المواهب بحركات سلسة وتكامل الفيديو.', 'وكالة فوغ الدولية', 'React,Node.js,MongoDB,AWS S3,FFmpeg'),
(4, 'en', 'FleetTrack Pro', 'Enterprise Windows desktop application for real-time fleet tracking with GPS integration, driver behavior analytics, maintenance scheduling, and comprehensive reporting dashboards.', 'Gulf Logistics Group', 'C#,WPF,.NET 8,SQLite,Google Maps API'),
(4, 'ar', 'فليت تراك برو', 'تطبيق سطح مكتب ويندوز لتتبع الأساطيل في الوقت الفعلي مع تكامل GPS وتحليلات سلوك السائقين وجدولة الصيانة ولوحات تقارير شاملة.', 'مجموعة الخليج اللوجستية', 'C#,WPF,.NET 8,SQLite,Google Maps API'),
(5, 'en', 'Al-Maskan Hotels', 'Modern hotel booking platform with dynamic room pricing, interactive floor plans, virtual tours, guest portal with loyalty points, and integration with major OTA channels.', 'Al-Maskan Hospitality', 'PHP,Alpine.js,MySQL,Stripe,Mapbox'),
(5, 'ar', 'فنادق المسكن', 'منصة حجز فنادق حديثة مع تسعير ديناميكي للغرف ومخططات طوابق تفاعلية وجولات افتراضية وبوابة ضيوف مع نقاط ولاء وتكامل مع قنوات الحجز الكبرى.', 'ضيافة المسكن', 'PHP,Alpine.js,MySQL,Stripe,Mapbox'),
(6, 'en', 'Digital Bloom Campaign', 'Comprehensive digital marketing campaign that tripled social media engagement, achieved #1 Google rankings for 15 target keywords, and generated 200% ROI through strategic PPC and content marketing.', 'Bloom Beauty', 'Google Ads,Meta Ads,SEO,Analytics,Figma')  ,
(6, 'ar', 'حملة ديجيتال بلوم', 'حملة تسويق رقمي شاملة ضاعفت التفاعل على وسائل التواصل ثلاث مرات وحققت المركز الأول في جوجل لـ15 كلمة مفتاحية مستهدفة وحققت عائد استثمار 200% من خلال الإعلانات المدفوعة وتسويق المحتوى.', 'بلوم بيوتي', 'Google Ads,Meta Ads,SEO,Analytics,Figma')
ON DUPLICATE KEY UPDATE title = VALUES(title), description = VALUES(description), client_name = VALUES(client_name), tags = VALUES(tags);

-- Portfolio page SEO
INSERT INTO seo_meta (page, locale, title, description, keywords) VALUES
('portfolio', 'en', 'Our Portfolio | Mico Sage — Projects & Case Studies', 'Explore our portfolio of web applications, desktop software, and digital marketing campaigns. See how Mico Sage delivers premium digital solutions.', 'portfolio, case studies, web projects, app development, digital marketing'),
('portfolio', 'ar', 'أعمالنا | ميكو سيج — مشاريعنا ودراسات الحالة', 'استكشف أعمالنا من تطبيقات الويب وبرامج سطح المكتب وحملات التسويق الرقمي. شاهد كيف تقدم ميكو سيج حلول رقمية متميزة.', 'أعمال, دراسات حالة, مشاريع ويب, تطوير تطبيقات, تسويق رقمي')
ON DUPLICATE KEY UPDATE title = VALUES(title), description = VALUES(description), keywords = VALUES(keywords);

-- Portfolio content entries
INSERT INTO contents (section_key, locale, value) VALUES
('portfolio_title', 'en', 'Our Portfolio'),
('portfolio_title', 'ar', 'أعمالنا'),
('portfolio_subtitle', 'en', 'Showcasing our finest work — from concept to launch, every project tells a story of innovation.'),
('portfolio_subtitle', 'ar', 'نعرض أفضل أعمالنا — من الفكرة إلى الإطلاق، كل مشروع يروي قصة ابتكار.')
ON DUPLICATE KEY UPDATE value = VALUES(value);

-- Chatbot Seed Data
INSERT INTO chatbot_nodes (id, name, is_root) VALUES
(1, 'Welcome', 1),
(2, 'Services Info', 0),
(3, 'Support', 0)
ON DUPLICATE KEY UPDATE name = VALUES(name), is_root = VALUES(is_root);

INSERT INTO chatbot_node_translations (node_id, locale, message) VALUES
(1, 'en', 'Hi there! 👋 Welcome to Mico Sage. How can we help you today?'),
(1, 'ar', 'مرحباً بك في ميكو سيج 👋 كيف يمكننا مساعدتك اليوم؟'),
(2, 'en', 'We offer premium Web Engineering, Windows Desktop Applications, and Digital Marketing services. Would you like to view our portfolio or book a consultation?'),
(2, 'ar', 'نقدم خدمات هندسة الويب المتميزة، وتطبيقات سطح المكتب، والتسويق الرقمي. هل تود رؤية أعمالنا أو حجز استشارة؟'),
(3, 'en', 'Our support team is ready to assist you. You can give us a call directly.'),
(3, 'ar', 'فريق الدعم الفني جاهز لمساعدتك. يمكنك الاتصال بنا مباشرة.')
ON DUPLICATE KEY UPDATE message = VALUES(message);

INSERT INTO chatbot_options (id, node_id, next_node_id, action_type, action_value, sort_order) VALUES
(1, 1, 2, 'goto_node', '', 1),
(2, 1, 3, 'goto_node', '', 2),
(3, 2, NULL, 'link', '/portfolio', 1),
(4, 2, NULL, 'link', '/#booking', 2),
(5, 3, NULL, 'call', '', 1)
ON DUPLICATE KEY UPDATE next_node_id = VALUES(next_node_id), action_type = VALUES(action_type), action_value = VALUES(action_value);

INSERT INTO chatbot_option_translations (option_id, locale, label) VALUES
(1, 'en', 'Our Services'),
(1, 'ar', 'خدماتنا'),
(2, 'en', 'Contact Support'),
(2, 'ar', 'الدعم الفني'),
(3, 'en', 'View Portfolio'),
(3, 'ar', 'رؤية الأعمال'),
(4, 'en', 'Book Consultation'),
(4, 'ar', 'حجز استشارة'),
(5, 'en', 'Call Us Now'),
(5, 'ar', 'اتصل بنا الآن')
ON DUPLICATE KEY UPDATE label = VALUES(label);

-- ═══════════════════════════════════════════════════════════
-- Team Members
-- ═══════════════════════════════════════════════════════════
CREATE TABLE IF NOT EXISTS team_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_url VARCHAR(255) DEFAULT '',
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS team_member_translations (
    member_id INT,
    locale VARCHAR(10),
    name VARCHAR(255) NOT NULL,
    role VARCHAR(255) NOT NULL,
    bio TEXT,
    PRIMARY KEY (member_id, locale),
    FOREIGN KEY (member_id) REFERENCES team_members(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ═══════════════════════════════════════════════════════════
-- Testimonials
-- ═══════════════════════════════════════════════════════════
CREATE TABLE IF NOT EXISTS testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_image_url VARCHAR(255) DEFAULT '',
    rating INT DEFAULT 5, /* 1-5 stars */
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS testimonial_translations (
    testimonial_id INT,
    locale VARCHAR(10),
    client_name VARCHAR(255) NOT NULL,
    client_company VARCHAR(255) DEFAULT '',
    content TEXT NOT NULL,
    PRIMARY KEY (testimonial_id, locale),
    FOREIGN KEY (testimonial_id) REFERENCES testimonials(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ═══════════════════════════════════════════════════════════
-- Adding settings for toggling Team and Testimonials sections
-- ═══════════════════════════════════════════════════════════
INSERT INTO site_settings (setting_key, setting_value, setting_type, setting_group) VALUES
('show_team', '1', 'boolean', 'general'),
('show_testimonials', '1', 'boolean', 'general')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

-- ═══════════════════════════════════════════════════════════
-- Seeding Team Members
-- ═══════════════════════════════════════════════════════════
INSERT INTO team_members (id, sort_order, is_active) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 3, 1)
ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order);

INSERT INTO team_member_translations (member_id, locale, name, role, bio) VALUES
(1, 'en', 'Sarah Jenkins', 'CEO & Founder', 'Visionary leader with 15+ years of experience in digital transformation and enterprise architecture.'),
(1, 'ar', 'سارة جنكينز', 'الرئيسة التنفيذية والمؤسسة', 'قائدة ذات رؤية مستقبلية بخبرة تتجاوز 15 عاماً في التحول الرقمي وهندسة المشاريع الإستراتيجية.'),
(2, 'en', 'Ahmad Al-Fahad', 'Chief Technology Officer', 'Expert in scalable cloud solutions, AI integration, and leading high-performance engineering teams.'),
(2, 'ar', 'أحمد الفهد', 'الرئيس التنفيذي للتكنولوجيا', 'خبير في الحلول السحابية القابلة للتطوير، ودمج الذكاء الاصطناعي، وقيادة فرق هندسية عالية الأداء.'),
(3, 'en', 'Elena Rodriguez', 'Creative Director', 'Award-winning designer passionate about building intuitive, beautiful, and accessible user experiences.'),
(3, 'ar', 'إيلينا رودريغيز', 'المديرة الإبداعية', 'مصممة حائزة على جوائز شغوفة ببناء تجارب مستخدم بديهية، وجميلة، ومتاحة للجميع.')
ON DUPLICATE KEY UPDATE name = VALUES(name), role = VALUES(role), bio = VALUES(bio);

-- ═══════════════════════════════════════════════════════════
-- Seeding Testimonials
-- ═══════════════════════════════════════════════════════════
INSERT INTO testimonials (id, rating, sort_order, is_active) VALUES
(1, 5, 1, 1),
(2, 5, 2, 1),
(3, 4, 3, 1)
ON DUPLICATE KEY UPDATE rating = VALUES(rating);

INSERT INTO testimonial_translations (testimonial_id, locale, client_name, client_company, content) VALUES
(1, 'en', 'Michael Chang', 'TechCorp International', 'Mico Sage entirely revamped our legacy systems. Their Web Engineering team is top-tier, delivering a product that exceeded all our performance expectations.'),
(1, 'ar', 'مايكل تشانغ', 'تيك كورب الدولية', 'قامت ميكو سيج بتجديد أنظمتنا القديمة بالكامل. فريق هندسة الويب لديهم من الطراز الأول، وقدموا منتجاً فاق كل توقعاتنا من ناحية الأداء.'),
(2, 'en', 'Fatima Al-Sayed', 'Bloom Beauty', 'The digital marketing campaign they designed for us tripled our online sales in just three months. Their creative solutions are unmatched.'),
(2, 'ar', 'فاطمة السيد', 'بلوم بيوتي', 'حملة التسويق الرقمي التي صمموها لنا ضاعفت مبيعاتنا عبر الإنترنت ثلاث مرات في ثلاثة أشهر فقط. حلولهم الإبداعية لا مثيل لها.'),
(3, 'en', 'David Reynolds', 'Gulf Logistics Group', 'Their custom Windows desktop application streamlined our entire fleet tracking process. Highly professional and responsive team.'),
(3, 'ar', 'ديفيد رينولدز', 'مجموعة الخليج اللوجستية', 'تطبيق سطح المكتب المخصص الذي صمموه لنا سهّل عملية تتبع الأسطول بالكامل. فريق عالي الاحترافية وسريع الاستجابة.')
ON DUPLICATE KEY UPDATE client_name = VALUES(client_name), client_company = VALUES(client_company), content = VALUES(content);

-- Also seed homepage content titles for these sections
INSERT INTO contents (section_key, locale, value) VALUES
('team_title', 'en', 'Meet Our Team'),
('team_title', 'ar', 'فريق العمل'),
('team_subtitle', 'en', 'The brilliant minds behind our innovative solutions.'),
('team_subtitle', 'ar', 'العقول المبتكرة وراء حلولنا الإبداعية.'),
('testimonials_title', 'en', 'Client Success Stories'),
('testimonials_title', 'ar', 'قصص نجاح عملائنا'),
('testimonials_subtitle', 'ar', 'لا تأخذ بكلامنا فقط. شاهد ما يقوله شركاؤنا ومزودينا.')
ON DUPLICATE KEY UPDATE value = VALUES(value);

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
