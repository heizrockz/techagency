-- Migration script: Add canonical links and Blogs schema

-- 1. Add canonical_link to seo_meta if it doesn't exist
ALTER TABLE seo_meta ADD COLUMN IF NOT EXISTS canonical_link VARCHAR(500) DEFAULT '';

-- 2. Create blogs table
CREATE TABLE IF NOT EXISTS blogs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(200) NOT NULL UNIQUE,
    media_type ENUM('image', 'video', 'video_link') DEFAULT 'image',
    media_url VARCHAR(500) DEFAULT '',
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Create blog translations
CREATE TABLE IF NOT EXISTS blog_translations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    blog_id INT NOT NULL,
    locale VARCHAR(5) NOT NULL DEFAULT 'en',
    title VARCHAR(255) NOT NULL,
    description TEXT,
    UNIQUE KEY uk_blog_locale (blog_id, locale),
    FOREIGN KEY (blog_id) REFERENCES blogs(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Insert blog Settings
INSERT INTO site_settings (setting_key, setting_value, setting_type, setting_group) VALUES
('show_blog_section', '1', 'boolean', 'sections')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

-- 5. Insert content defaults for Blog Section
INSERT INTO contents (section_key, locale, value) VALUES
('blog_title', 'en', 'Latest Insights'),
('blog_title', 'ar', 'أحدث المقالات'),
('blog_subtitle', 'en', 'Stay updated with our latest news and futuristic visions.'),
('blog_subtitle', 'ar', 'ابق على اطلاع بآخر أخبارنا ورؤانا المستقبلية.')
ON DUPLICATE KEY UPDATE value = VALUES(value);

-- 6. Insert content defaults for "Our Process" Section
INSERT INTO contents (section_key, locale, value) VALUES
('process_title', 'en', 'Our Engineering Process'),
('process_title', 'ar', 'عملية الهندسة لدينا'),
('process_subtitle', 'en', 'From concept to deployment, we follow a rigorous futuristic workflow.'),
('process_subtitle', 'ar', 'من المفهوم إلى النشر، نتبع سير عمل مستقبلي صارم.'),
('process_step1_title', 'en', 'Discovery & Analysis'),
('process_step1_title', 'ar', 'الاستكشاف والتحليل'),
('process_step1_desc', 'en', 'We dive deep into your requirements to define the technical roadmap.'),
('process_step1_desc', 'ar', 'نغوص في متطلباتك لتحديد خارطة الطريق التقنية.'),
('process_step2_title', 'en', 'Futuristic Design'),
('process_step2_title', 'ar', 'التصميم المستقبلي'),
('process_step2_desc', 'en', 'Crafting UI/UX that feels alive, interactive, and premium.'),
('process_step2_desc', 'ar', 'صياغة تجربة مستخدم تشعر بالحيوية والتفاعل والفخامة.'),
('process_step3_title', 'en', 'Agile Development'),
('process_step3_title', 'ar', 'التطوير الرشيق'),
('process_step3_desc', 'en', 'Building with scalable architectures and clean, modular code.'),
('process_step3_desc', 'ar', 'البناء بهندسة قابلة للتوسع وكود نظيف ونمطي.'),
('process_step4_title', 'en', 'Testing & Launch'),
('process_step4_title', 'ar', 'الاختبار والإطلاق'),
('process_step4_desc', 'en', 'Rigorous QA followed by a smooth global deployment.'),
('process_step4_desc', 'ar', 'ضمان جودة صارم يليه نشر عالمي سلس.')
ON DUPLICATE KEY UPDATE value = VALUES(value);
