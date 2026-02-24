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
