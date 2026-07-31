-- ============================================================
-- Lead Management CRM - Database Schema
-- Import this file in phpMyAdmin (cPanel) before using the app.
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================
-- TABLE: users (login)
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default login: username "admin", password "ChangeMe123!"
-- IMPORTANT: login karte hi is password ko Settings page se turant badal do.
INSERT INTO users (username, password_hash) VALUES
('admin', '$2y$12$B48yJKQuv9jfbhxsDZHp.eGe9JsgA6QnJ/m5YepLgHLqo.OBt6fRC')
ON DUPLICATE KEY UPDATE username = username;

-- ============================================
-- TABLE: leads (main company/lead data)
-- ============================================
CREATE TABLE IF NOT EXISTS leads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    place_id VARCHAR(255) UNIQUE,
    company_name VARCHAR(255) NOT NULL,
    address VARCHAR(500),
    phone VARCHAR(50),
    whatsapp_number VARCHAR(50),
    website VARCHAR(500),
    email VARCHAR(255),
    facebook_url VARCHAR(500),
    linkedin_url VARCHAR(500),
    instagram_url VARCHAR(500),
    google_profile_url VARCHAR(500),
    reviews_count INT DEFAULT 0,
    rating DECIMAL(2,1),
    search_query VARCHAR(255),
    industry VARCHAR(100) DEFAULT 'Real Estate',
    source VARCHAR(30) NOT NULL DEFAULT 'google_places',
    urgency_score INT DEFAULT 0,
    status ENUM('new','analyzed','contacted','replied','follow_up','closed_won','closed_lost') DEFAULT 'new',
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_urgency (urgency_score)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABLE: lead_gaps
-- ============================================
CREATE TABLE IF NOT EXISTS lead_gaps (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lead_id INT NOT NULL,
    gap_type VARCHAR(100),
    gap_detail VARCHAR(255),
    FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABLE: email_templates
-- ============================================
CREATE TABLE IF NOT EXISTS email_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    subject VARCHAR(255),
    body TEXT,
    gap_trigger VARCHAR(100),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO email_templates (name, subject, body, gap_trigger) VALUES
('No Website Pitch', 'Quick idea for {{company_name}}',
'Hi {{company_name}} team,\n\nI noticed you don''t have a website listed on Google yet. Most of your customers are searching online before they call - without a site, you could be losing leads to competitors who show up first.\n\nI help local businesses get a fast, professional website live in days. Want me to send a quick mockup, free of cost?\n\nThanks,\n', 'no_website'),
('Low Reviews Pitch', 'Noticed something about {{company_name}} on Google',
'Hi {{company_name}} team,\n\nI came across your Google Business listing - great work so far! I noticed you currently have a relatively small number of reviews, which can make it harder for new customers to trust and find you.\n\nI help businesses like yours set up a simple system to get more genuine reviews consistently. Interested in a quick chat?\n\nThanks,\n', 'low_reviews'),
('No Social Media Pitch', 'Growing {{company_name}} online', 'Hi {{company_name}} team,\n\nI checked your online presence and noticed you don''t have active social media profiles linked. This is a missed opportunity to reach more local customers and build trust.\n\nI can help set up and manage your social presence. Would you be open to a short call?\n\nThanks,\n', 'no_social')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- ============================================
-- TABLE: email_log
-- ============================================
CREATE TABLE IF NOT EXISTS email_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lead_id INT NOT NULL,
    template_id INT,
    subject VARCHAR(255),
    body TEXT,
    sent_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    reply_status ENUM('pending','replied','no_reply','bounced') DEFAULT 'pending',
    reply_received_at DATETIME NULL,
    reply_notes TEXT,
    FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE,
    FOREIGN KEY (template_id) REFERENCES email_templates(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABLE: search_history
-- ============================================
CREATE TABLE IF NOT EXISTS search_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    query VARCHAR(255),
    results_count INT,
    searched_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABLE: app_settings (key-value store: API key, gap thresholds)
-- ============================================
CREATE TABLE IF NOT EXISTS app_settings (
    setting_key VARCHAR(100) PRIMARY KEY,
    setting_value VARCHAR(1000)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO app_settings (setting_key, setting_value) VALUES
('google_places_api_key', ''),
('google_places_api_enabled', '1'),
('max_reviews_threshold', '10'),
('min_rating_threshold', '4.0'),
('max_search_results', '10'),
('my_name', ''),
('my_company', ''),
('my_email', ''),
('my_phone', ''),
('my_portfolio', ''),
('apollo_api_key', ''),
('apollo_api_enabled', '0'),
('hunter_api_key', ''),
('hunter_api_enabled', '0')
ON DUPLICATE KEY UPDATE setting_key = setting_key;

-- ============================================
-- TABLE: lead_contacts (owner/CEO/manager contacts found via Apollo/Hunter)
-- ============================================
CREATE TABLE IF NOT EXISTS lead_contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lead_id INT NOT NULL,
    contact_name VARCHAR(255),
    title VARCHAR(255),
    email VARCHAR(255),
    phone VARCHAR(50),
    linkedin_url VARCHAR(500),
    source VARCHAR(50),
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE,
    UNIQUE KEY unique_contact_per_lead (lead_id, email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;
