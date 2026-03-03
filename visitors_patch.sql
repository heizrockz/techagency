-- Database Patch: Visitor Analytics
-- Run this script on your live database to create the required table
-- Date: 2026-03-03

CREATE TABLE IF NOT EXISTS site_visitors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    country VARCHAR(100),
    country_code VARCHAR(5),
    city VARCHAR(100),
    region VARCHAR(100),
    isp VARCHAR(200),
    user_agent TEXT,
    page_url VARCHAR(500),
    visited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
