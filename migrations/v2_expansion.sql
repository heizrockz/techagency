-- Mico Sage Tech Agency — V2 Schema Expansion
USE tech_agency;

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

-- ═══ Update bookings to store dynamic field data ═══
ALTER TABLE bookings ADD COLUMN IF NOT EXISTS extra_fields JSON DEFAULT NULL;

-- ═══════════════════════════════════════════════════════════
-- SEED DATA
-- ═══════════════════════════════════════════════════════════

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
('primary_color', '#3b82f6', 'color', 'branding'),
('secondary_color', '#8b5cf6', 'color', 'branding');

-- Default Services
INSERT INTO services (icon, color, sort_order) VALUES
('code', 'cobalt', 1),
('monitor', 'violet', 2),
('chart', 'emerald', 3);

INSERT INTO service_translations (service_id, locale, title, description) VALUES
(1, 'en', 'Web Engineering', 'Full-stack web applications built with modern frameworks, responsive designs, and pixel-perfect interfaces that convert visitors into customers.'),
(1, 'ar', 'هندسة الويب', 'تطبيقات ويب متكاملة مبنية بأحدث الأطر والتصاميم المتجاوبة وواجهات مثالية تحول الزوار إلى عملاء.'),
(2, 'en', 'Windows Desktop Apps', 'Native Windows applications with sleek UIs, powerful performance, and seamless integration with your business workflows.'),
(2, 'ar', 'تطبيقات سطح المكتب', 'تطبيقات ويندوز أصلية بواجهات أنيقة وأداء قوي وتكامل سلس مع سير عمل شركتك.'),
(3, 'en', 'Digital Growth', 'Data-driven digital marketing strategies including SEO, social media, PPC, and brand identity that amplify your online presence.'),
(3, 'ar', 'النمو الرقمي', 'استراتيجيات تسويق رقمي مبنية على البيانات تشمل تحسين محركات البحث والتواصل الاجتماعي والإعلانات المدفوعة وهوية العلامة التجارية.');

-- Default Products
INSERT INTO products (icon, category, color, sort_order) VALUES
('car', 'website', 'cobalt', 1),
('cart', 'website', 'violet', 2),
('hotel', 'website', 'cyan', 3),
('billing', 'app', 'emerald', 4),
('crm', 'app', 'pink', 5),
('wrench', 'maintenance', 'orange', 6);

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
(6, 'ar', 'صيانة المواقع', 'صيانة مستمرة للمواقع وتحديثات الأمان وتحسين الأداء ودعم إدارة المحتوى.');

-- Default Booking Fields
INSERT INTO booking_fields (field_name, field_type, is_required, sort_order, is_active) VALUES
('name', 'text', 1, 1, 1),
('email', 'email', 1, 2, 1),
('phone', 'tel', 0, 3, 1),
('service', 'select', 1, 4, 1),
('preferred_date', 'date', 0, 5, 1),
('message', 'textarea', 0, 6, 1);

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
(6, 'ar', 'تفاصيل المشروع', 'أخبرنا عن مشروعك');

-- Default Clients
INSERT INTO clients (name, logo_url, sort_order, is_active) VALUES
('TechCorp International', '', 1, 1),
('Global Solutions Ltd', '', 2, 1),
('Innovation Hub', '', 3, 1),
('Digital Ventures', '', 4, 1),
('SmartBiz Group', '', 5, 1);

-- Update existing content for Mico Sage branding
UPDATE contents SET value = 'We Build The Future' WHERE section_key = 'hero_title' AND locale = 'en';
UPDATE contents SET value = 'نحن نبني المستقبل' WHERE section_key = 'hero_title' AND locale = 'ar';
UPDATE contents SET value = 'Premium Web Engineering · Windows Desktop Apps · Digital Growth · Creative Solutions' WHERE section_key = 'hero_subtitle' AND locale = 'en';
UPDATE contents SET value = 'هندسة ويب متميزة · تطبيقات سطح المكتب · النمو الرقمي · حلول إبداعية' WHERE section_key = 'hero_subtitle' AND locale = 'ar';
UPDATE contents SET value = 'Why Choose Mico Sage?' WHERE section_key = 'about_title' AND locale = 'en';
UPDATE contents SET value = 'لماذا تختار ميكو سيج؟' WHERE section_key = 'about_title' AND locale = 'ar';
UPDATE contents SET value = 'We are a forward-thinking tech agency that combines cutting-edge design with robust engineering. Our team delivers world-class digital products that propel your business into the future. From concept to launch, we handle every pixel and every line of code with precision.' WHERE section_key = 'about_text' AND locale = 'en';
UPDATE contents SET value = 'نحن وكالة تقنية مبتكرة تجمع بين التصميم المتطور والهندسة القوية. يقدم فريقنا منتجات رقمية عالمية المستوى تدفع أعمالك نحو المستقبل. من الفكرة إلى الإطلاق، نتعامل مع كل بكسل وكل سطر كود بدقة.' WHERE section_key = 'about_text' AND locale = 'ar';
UPDATE contents SET value = '© 2026 Mico Sage. All rights reserved.' WHERE section_key = 'footer_text' AND locale = 'en';
UPDATE contents SET value = '© 2026 ميكو سيج. جميع الحقوق محفوظة.' WHERE section_key = 'footer_text' AND locale = 'ar';

-- Add new content entries
INSERT INTO contents (section_key, locale, value) VALUES
('clients_title', 'en', 'Trusted By Industry Leaders'),
('clients_title', 'ar', 'موثوق من قبل قادة الصناعة'),
('products_title', 'en', 'Ready-Made Solutions'),
('products_title', 'ar', 'حلول جاهزة'),
('products_subtitle', 'en', 'Pre-built platforms customized to your brand. Launch faster, grow smarter.'),
('products_subtitle', 'ar', 'منصات جاهزة مخصصة لعلامتك التجارية. انطلق أسرع، انمُ بذكاء.'),
('marketing_title', 'en', 'Digital Marketing That Delivers Results'),
('marketing_title', 'ar', 'تسويق رقمي يحقق النتائج'),
('marketing_subtitle', 'en', 'We don\'t just market — we engineer growth. Data-driven strategies that turn clicks into customers.'),
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

-- Update SEO for Mico Sage
UPDATE seo_meta SET title = 'Mico Sage | Web Development, Windows Apps & Digital Marketing' WHERE page = 'home' AND locale = 'en';
UPDATE seo_meta SET title = 'ميكو سيج | تطوير المواقع وتطبيقات ويندوز والتسويق الرقمي' WHERE page = 'home' AND locale = 'ar';
