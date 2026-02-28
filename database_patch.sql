-- Table structure for `crm_attachments`
DROP TABLE IF EXISTS `crm_attachments`;
CREATE TABLE `crm_attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `linked_type` enum('opportunity','log_note') NOT NULL,
  `linked_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_type` varchar(100) DEFAULT '',
  `file_size` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for `crm_items`
DROP TABLE IF EXISTS `crm_items`;
CREATE TABLE `crm_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(15,2) DEFAULT 0.00,
  `cost` decimal(15,2) DEFAULT 0.00,
  `category` varchar(100) DEFAULT '',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for `crm_log_notes`
DROP TABLE IF EXISTS `crm_log_notes`;
CREATE TABLE `crm_log_notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `opportunity_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `note_type` enum('note','email','call','meeting') DEFAULT 'note',
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `opportunity_id` (`opportunity_id`),
  KEY `admin_id` (`admin_id`),
  CONSTRAINT `crm_log_notes_ibfk_1` FOREIGN KEY (`opportunity_id`) REFERENCES `crm_opportunities` (`id`) ON DELETE CASCADE,
  CONSTRAINT `crm_log_notes_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for `crm_opportunities`
DROP TABLE IF EXISTS `crm_opportunities`;
CREATE TABLE `crm_opportunities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `contact_id` int(11) DEFAULT NULL,
  `email` varchar(255) DEFAULT '',
  `phone` varchar(50) DEFAULT '',
  `expected_revenue` decimal(15,2) DEFAULT 0.00,
  `revenue_type` varchar(50) DEFAULT 'Total',
  `stage` enum('New Lead','Know Your Client','Post Casting','Quote & Proposal','LPO','Casting & Production','Won','Lost') DEFAULT 'New Lead',
  `probability` decimal(5,2) DEFAULT 0.00,
  `color_code` varchar(20) DEFAULT NULL,
  `expected_closing` date DEFAULT NULL,
  `tags` varchar(500) DEFAULT '',
  `priority` int(11) DEFAULT 0,
  `salesperson_id` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `contact_id` (`contact_id`),
  KEY `salesperson_id` (`salesperson_id`),
  CONSTRAINT `crm_opportunities_ibfk_1` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `crm_opportunities_ibfk_2` FOREIGN KEY (`salesperson_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for `crm_opportunity_items`
DROP TABLE IF EXISTS `crm_opportunity_items`;
CREATE TABLE `crm_opportunity_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `opportunity_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `qty` int(11) DEFAULT 1,
  `price` decimal(10,2) DEFAULT 0.00,
  `subtotal` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for `crm_stages`
DROP TABLE IF EXISTS `crm_stages`;
CREATE TABLE `crm_stages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_collapsed` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ALTER statements for existing tables
ALTER TABLE `admins` ADD COLUMN IF NOT EXISTS `avatar_emoji` VARCHAR(10) DEFAULT '👤';
ALTER TABLE `invoices` ADD COLUMN IF NOT EXISTS `opportunity_id` INT DEFAULT NULL;
