-- ============================================
-- Database Schema for Business Website
-- Import ei file cPanel > phpMyAdmin theke
-- ============================================

CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default admin: username = admin , password = admin123
-- (password hashed, login korar por change kore nao)
INSERT INTO admin_users (username, password) VALUES
('admin', '$2y$10$td9UuP4jFNkM/02Nu70Qd.kDMoxPCbc14bFJReFb1e9EA7UtLL/1C');

CREATE TABLE IF NOT EXISTS site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT
);

INSERT INTO site_settings (setting_key, setting_value) VALUES
('site_name', 'My Business'),
('site_tagline', 'Quality Work, Trusted Service'),
('phone', '+880 1XXXXXXXXX'),
('email', 'info@example.com'),
('address', 'Dhaka, Bangladesh'),
('facebook_url', ''),
('footer_text', '© 2026 My Business. All rights reserved.');

CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    icon VARCHAR(100) DEFAULT 'fa-cog',
    image VARCHAR(255),
    sort_order INT DEFAULT 0,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS completed_jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    job_date DATE,
    sort_order INT DEFAULT 0,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_name VARCHAR(150) NOT NULL,
    message TEXT,
    rating INT DEFAULT 5,
    image VARCHAR(255),
    sort_order INT DEFAULT 0,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS teams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    designation VARCHAR(150),
    bio TEXT,
    image VARCHAR(255),
    facebook_url VARCHAR(255),
    sort_order INT DEFAULT 0,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS custom_sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_key VARCHAR(100) NOT NULL UNIQUE,
    title VARCHAR(150),
    content TEXT,
    image VARCHAR(255),
    page VARCHAR(50) DEFAULT 'home',
    sort_order INT DEFAULT 0,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150),
    email VARCHAR(150),
    phone VARCHAR(50),
    message TEXT,
    is_read TINYINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS blogs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content TEXT,
    image VARCHAR(255),
    author VARCHAR(100) DEFAULT 'Admin',
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    subtitle VARCHAR(255),
    slug VARCHAR(255) NOT NULL UNIQUE,
    content TEXT,
    image VARCHAR(255),
    parent_menu VARCHAR(50) DEFAULT NULL,
    meta_description TEXT,
    meta_keywords VARCHAR(255),
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS menus (
    id INT AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(150) NOT NULL,
    link VARCHAR(255) NOT NULL,
    parent_id INT DEFAULT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS page_sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_id INT NOT NULL,
    section_type VARCHAR(50) NOT NULL,
    title VARCHAR(255) DEFAULT NULL,
    content TEXT DEFAULT NULL,
    image VARCHAR(255) DEFAULT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS page_visits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_url VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    country VARCHAR(100) DEFAULT 'Unknown',
    city VARCHAR(100) DEFAULT 'Unknown',
    user_agent TEXT DEFAULT NULL,
    session_id VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
