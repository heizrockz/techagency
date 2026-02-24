-- Missing Taglines and Success Page Entries
INSERT IGNORE INTO `site_settings` (`setting_key`, `setting_value`, `setting_type`, `setting_group`) VALUES
('show_tagline_section', '1', 'boolean', 'sections');

-- Tagline Content
INSERT IGNORE INTO `contents` (`section_key`, `locale`, `value`) VALUES 
('tagline1_icon', 'en', '⚡'),
('tagline1_icon', 'ar', '⚡'),
('tagline1_title', 'en', 'Fast'),
('tagline1_title', 'ar', 'سريع'),
('tagline1_desc', 'en', 'Optimized performance for lightning-fast load times and smooth UX.'),
('tagline1_desc', 'ar', 'أداء محسّن لسرعات تحميل قصوى وتجربة مستخدم سلسة.'),
('tagline2_icon', 'en', '🔄'),
('tagline2_icon', 'ar', '🔄'),
('tagline2_title', 'en', 'Dynamic'),
('tagline2_title', 'ar', 'ديناميكي'),
('tagline2_desc', 'en', 'Interactive solutions that adapt to your evolving business needs in real-time.'),
('tagline2_desc', 'ar', 'حلول تفاعلية تتكيف مع احتياجات عملك المتطورة في الوقت الفعلي.'),
('tagline3_icon', 'en', '📈'),
('tagline3_icon', 'ar', '📈'),
('tagline3_title', 'en', 'Scalable'),
('tagline3_title', 'ar', 'قابل للتوسع'),
('tagline3_desc', 'en', 'Architecture built to grow with your business, supporting millions of users.'),
('tagline3_desc', 'ar', 'بُنية تحتية مصممة للنمو مع أعمالك، تدعم ملايين المستخدمين.');

-- Success Page Content
INSERT IGNORE INTO `contents` (`section_key`, `locale`, `value`) VALUES
('success_page_title', 'en', 'Success!'),
('success_page_title', 'ar', 'تم بنجاح!'),
('success_page_message', 'en', 'Thank you! Your booking request has been submitted. Our team will contact you shortly.'),
('success_page_message', 'ar', 'شكراً لك! تم استلام الطلب وسنتواصل معك قريباً.'),
('success_page_button', 'en', 'Return to Home'),
('success_page_button', 'ar', 'العودة للرئيسية');
