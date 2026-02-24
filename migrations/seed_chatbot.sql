-- Final Chatbot Seeder
-- 1. Root Node
INSERT INTO chatbot_nodes (id, name, is_root, pos_x, pos_y, reply_type) VALUES 
(1, 'Welcome Greeting', 1, 150, 200, 'preset')
ON DUPLICATE KEY UPDATE name=VALUES(name), is_root=VALUES(is_root), pos_x=VALUES(pos_x), pos_y=VALUES(pos_y), reply_type=VALUES(reply_type);

-- 2. Nodes
INSERT INTO chatbot_nodes (id, name, is_root, pos_x, pos_y, reply_type) VALUES 
(2, 'Service Inquiry', 0, 450, 200, 'preset'),
(3, 'Lead Collection', 0, 750, 200, 'user_input')
ON DUPLICATE KEY UPDATE name=VALUES(name), is_root=VALUES(is_root), pos_x=VALUES(pos_x), pos_y=VALUES(pos_y), reply_type=VALUES(reply_type);

-- 3. Translations (Node)
INSERT INTO chatbot_node_translations (node_id, locale, message) VALUES 
(1, 'en', 'Hello! Thank you for visiting Mico Sage. How may we assist you today?'),
(1, 'ar', 'مرحباً بك! شكراً لزيارتك ميكو سيج. كيف يمكننا مساعدتك اليوم؟'),
(2, 'en', 'We specialize in high-end Web Engineering, Windows Desktop Applications, and Data-Driven Marketing. Which area interests you?'),
(2, 'ar', 'نحن متخصصون في هندسة الويب وتطبيقات ويندوز والتسويق الرقمي. أي مجال يهمك؟'),
(3, 'en', 'Excellent choice. Please leave your email or phone number, and our experts will reach out with a custom proposal.'),
(3, 'ar', 'اختيار ممتاز. يرجى ترك بريدك الإلكتروني أو رقم هاتفك، وسيتواصل خبراؤنا معك لعرض مخصص.')
ON DUPLICATE KEY UPDATE message=VALUES(message);

-- 4. Options
INSERT INTO chatbot_options (id, node_id, next_node_id, action_type, sort_order) VALUES
(1, 1, 2, 'goto_node', 1),
(2, 2, 3, 'goto_node', 1)
ON DUPLICATE KEY UPDATE node_id=VALUES(node_id), next_node_id=VALUES(next_node_id), action_type=VALUES(action_type), sort_order=VALUES(sort_order);

-- 5. Translations (Option)
INSERT INTO chatbot_option_translations (option_id, locale, label) VALUES
(1, 'en', 'Explore Services'),
(1, 'ar', 'استكشاف الخدمات'),
(2, 'en', 'Get a Quote'),
(2, 'ar', 'طلب عرض سعر')
ON DUPLICATE KEY UPDATE label=VALUES(label);
