-- ========================================================
-- Supabase PostgreSQL Setup & Data Seed (Auto-generated)
-- Copy and paste this directly into your Supabase SQL Editor
-- ========================================================

-- DROP TABLE IF EXISTS admin_users CASCADE;
DROP TABLE IF EXISTS admin_users CASCADE;
-- DROP TABLE IF EXISTS site_settings CASCADE;
DROP TABLE IF EXISTS site_settings CASCADE;
-- DROP TABLE IF EXISTS services CASCADE;
DROP TABLE IF EXISTS services CASCADE;
-- DROP TABLE IF EXISTS completed_jobs CASCADE;
DROP TABLE IF EXISTS completed_jobs CASCADE;
-- DROP TABLE IF EXISTS reviews CASCADE;
DROP TABLE IF EXISTS reviews CASCADE;
-- DROP TABLE IF EXISTS teams CASCADE;
DROP TABLE IF EXISTS teams CASCADE;
-- DROP TABLE IF EXISTS custom_sections CASCADE;
DROP TABLE IF EXISTS custom_sections CASCADE;
-- DROP TABLE IF EXISTS messages CASCADE;
DROP TABLE IF EXISTS messages CASCADE;
-- DROP TABLE IF EXISTS blogs CASCADE;
DROP TABLE IF EXISTS blogs CASCADE;
-- DROP TABLE IF EXISTS pages CASCADE;
DROP TABLE IF EXISTS pages CASCADE;
-- DROP TABLE IF EXISTS menus CASCADE;
DROP TABLE IF EXISTS menus CASCADE;
-- DROP TABLE IF EXISTS page_visits CASCADE;
DROP TABLE IF EXISTS page_visits CASCADE;
-- DROP TABLE IF EXISTS page_sections CASCADE;
DROP TABLE IF EXISTS page_sections CASCADE;

-- Create Tables
-- Schema for admin_users
CREATE TABLE admin_users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Schema for site_settings
CREATE TABLE site_settings (
    id SERIAL PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL,
    setting_value TEXT
);

-- Schema for services
CREATE TABLE services (
    id SERIAL PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    icon VARCHAR(100) DEFAULT 'fa-cog',
    image VARCHAR(255),
    sort_order INT DEFAULT 0,
    status VARCHAR(50) DEFAULT 'active',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Schema for completed_jobs
CREATE TABLE completed_jobs (
    id SERIAL PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    job_date DATE,
    sort_order INT DEFAULT 0,
    status VARCHAR(50) DEFAULT 'active',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Schema for reviews
CREATE TABLE reviews (
    id SERIAL PRIMARY KEY,
    client_name VARCHAR(150) NOT NULL,
    message TEXT,
    rating INT DEFAULT 5,
    image VARCHAR(255),
    sort_order INT DEFAULT 0,
    status VARCHAR(50) DEFAULT 'active',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Schema for teams
CREATE TABLE teams (
    id SERIAL PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    designation VARCHAR(150),
    bio TEXT,
    image VARCHAR(255),
    facebook_url VARCHAR(255),
    sort_order INT DEFAULT 0,
    status VARCHAR(50) DEFAULT 'active',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Schema for custom_sections
CREATE TABLE custom_sections (
    id SERIAL PRIMARY KEY,
    section_key VARCHAR(100) NOT NULL,
    title VARCHAR(150),
    content TEXT,
    image VARCHAR(255),
    page VARCHAR(50) DEFAULT 'home',
    sort_order INT DEFAULT 0,
    status VARCHAR(50) DEFAULT 'active',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Schema for messages
CREATE TABLE messages (
    id SERIAL PRIMARY KEY,
    name VARCHAR(150),
    email VARCHAR(150),
    phone VARCHAR(50),
    message TEXT,
    is_read INT DEFAULT 0,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Schema for blogs
CREATE TABLE blogs (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    content TEXT,
    image VARCHAR(255),
    author VARCHAR(100) DEFAULT 'Admin',
    status VARCHAR(50) DEFAULT 'active',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Schema for pages
CREATE TABLE pages (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    subtitle VARCHAR(255),
    slug VARCHAR(255) NOT NULL,
    content TEXT,
    image VARCHAR(255),
    parent_menu VARCHAR(50) DEFAULT NULL,
    meta_description TEXT,
    meta_keywords VARCHAR(255),
    status VARCHAR(50) DEFAULT 'active',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Schema for menus
CREATE TABLE menus (
    id SERIAL PRIMARY KEY,
    label VARCHAR(150) NOT NULL,
    link VARCHAR(255) NOT NULL,
    parent_id INT DEFAULT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Schema for page_visits
CREATE TABLE page_visits (
    id SERIAL PRIMARY KEY,
    page_url VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    country VARCHAR(100) DEFAULT 'Unknown',
    city VARCHAR(100) DEFAULT 'Unknown',
    user_agent TEXT DEFAULT NULL,
    session_id VARCHAR(255) NOT NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    last_activity TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Schema for page_sections
CREATE TABLE page_sections (
    id SERIAL PRIMARY KEY,
    page_id INT NOT NULL,
    section_type VARCHAR(50) NOT NULL,
    title VARCHAR(255) DEFAULT NULL,
    content TEXT DEFAULT NULL,
    image VARCHAR(255) DEFAULT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE
);

-- Seed Data
-- Data for admin_users
INSERT INTO admin_users (id, username, password, created_at) VALUES
    ('1', 'admin', '$2y$10$td9UuP4jFNkM/02Nu70Qd.kDMoxPCbc14bFJReFb1e9EA7UtLL/1C', '2026-07-05 07:03:43'),
    ('2', 'rockybiswas133@gamil.com', '$2y$10$v8S9UUsaDdDP1onXoaL7ueJhlsM0I/mU.1D5pCJmO.4hAZ39Zf6M6', '2026-07-05 11:29:43');

-- Data for site_settings
INSERT INTO site_settings (id, setting_key, setting_value) VALUES
    ('8', 'site_name', 'Apex Digital Solutions'),
    ('9', 'site_tagline', 'Innovative CRM & Web Solutions for Modern Enterprises'),
    ('10', 'phone', '+880 1712-345678'),
    ('11', 'email', 'contact@apexdigital.com'),
    ('12', 'address', 'Banani, Dhaka, Bangladesh'),
    ('13', 'facebook_url', 'https://facebook.com/apexdigital'),
    ('14', 'footer_text', '© 2026 Apex Digital Solutions. All rights reserved.');

-- Data for services
INSERT INTO services (id, title, description, icon, image, sort_order, status, created_at) VALUES
    ('1', 'Web Design & Development', 'We design and build modern, fast, and fully responsive business websites tailored to your brand identity, optimized for search engines and user experience.', 'fa-laptop-code', NULL, '1', 'active', '2026-07-05 07:03:43'),
    ('2', 'Custom CRM & CRM Integration', 'Streamline sales pipelines, automate workflows, track leads, and store customer interactions securely in a tailored CRM designed for your workflow.', 'fa-users-cog', NULL, '2', 'active', '2026-07-05 07:03:43'),
    ('3', 'Digital Marketing & SEO', 'Expand your online presence and scale organic leads using our data-driven SEO strategies, local SEO, content writing, and digital ad campaigns.', 'fa-bullhorn', NULL, '3', 'active', '2026-07-05 07:03:43'),
    ('4', 'Cloud Hosting & Infrastructure', 'Reliable, high-performance cloud hosting setup, system maintenance, email server configuration, and daily backups with 99.9% uptime.', 'fa-cloud', NULL, '4', 'active', '2026-07-05 07:03:43'),
    ('5', 'What is Lorem Ipsum?', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet. Etiam ultricies nisi vel augue. Curabitur ullamcorper ultricies nisi. Nam eget dui. Etiam rhoncus. Maecenas tempus, tellus eget condimentum rhoncus, sem quam semper libero, sit amet adipiscing sem neque sed ipsum. Nam quam nunc, blandit vel, luctus pulvinar, hendrerit id, lorem. Maecenas nec odio et ante tincidunt tempus. Donec vitae sapien ut libero venenatis faucibus. Nullam quis ante. Etiam sit amet orci eget eros faucibus tincidunt. Duis leo. Sed fringilla mauris sit amet nibh. Donec sodales sagittis magna. Sed consequat, leo eget bibendum sodales, augue velit cursus nunc', 'fa-cog', 'img_6a4a03a296bdd.jpg', '0', 'active', '2026-07-05 07:11:30'),
    ('6', 'agsag', 'gasdgasdgasdg', 'fa-cog', NULL, '1', 'active', '2026-07-05 07:45:49');

-- No data for completed_jobs

-- Data for reviews
INSERT INTO reviews (id, client_name, message, rating, image, sort_order, status, created_at) VALUES
    ('1', 'Mahbubur Rahman', 'Apex Digital built a customized CRM for our real estate firm in Dhaka. It boosted our sales team''s productivity by 40% and organized our lead pipeline beautifully. Extremely professional work!', '5', NULL, '1', 'active', '2026-07-05 07:03:43'),
    ('2', 'Farhana Yasmin', 'Their web development team is top-notch. Our new agency website is lightning fast, mobile responsive, and has already doubled our organic inquiries. Highly recommended!', '5', NULL, '2', 'active', '2026-07-05 07:03:43');

-- Data for teams
INSERT INTO teams (id, name, designation, bio, image, facebook_url, sort_order, status, created_at) VALUES
    ('1', 'Rahat Chowdhury', 'Co-Founder & Lead Engineer', 'Full-stack architect specializing in PHP, SQLite/MySQL, and scalable web apps with 8+ years of engineering experience.', NULL, NULL, '1', 'active', '2026-07-05 07:03:43'),
    ('2', 'Nusrat Jahan', 'CRM Implementation Lead', 'Database expert focused on designing automated workflows, system integrations, and secure customer databases.', NULL, NULL, '2', 'active', '2026-07-05 07:03:43');

-- Data for custom_sections
INSERT INTO custom_sections (id, section_key, title, content, image, page, sort_order, status, created_at) VALUES
    ('1', 'about_mission', 'Our Mission & Vision', 'At Apex Digital, our mission is to empower enterprises and startups with state-of-the-art software solutions. We bridge the gap between complex database systems and daily operational needs by building intuitive CRM platforms and responsive websites.', NULL, 'about', '1', 'active', '2026-07-05 07:03:43'),
    ('2', 'about_values', 'Why Choose Apex Digital?', 'We believe in absolute transparency, high-performance software engineering, and dedicated support. Our developers work directly with your team to customize modules, build automated report systems, and optimize lead conversion rates.', NULL, 'about', '2', 'active', '2026-07-05 07:03:43');

-- No data for messages

-- Data for blogs
INSERT INTO blogs (id, title, slug, content, image, author, status, created_at) VALUES
    ('1', '5 Ways Custom CRM Software Can Double Your Sales Efficiency', '5-ways-custom-crm-software-can-double-sales-efficiency', 'Managing client interactions using spreadsheets is slow and prone to errors. Custom CRM software organizes all lead pipelines, automates followup tasks, and stores contact notes dynamically.

Here are 5 core benefits of transitioning to a CRM:

1. Centralized Customer Data: Never lose a lead''s contact details or interaction history again.
2. Automated Follow-ups: Set automatic reminders for calls, emails, and meetings.
3. Sales Pipeline Visualization: Track deals from initial contact to closing in real-time.
4. Advanced Reporting: Analyze team performance and monthly revenues automatically.
5. Secure Collaboration: Allow your sales, support, and marketing teams to work in harmony.

By leveraging these automations, businesses typically see sales efficiency double within the first quarter.', NULL, 'Rahat Chowdhury', 'active', '2026-07-05 07:03:43'),
    ('2', 'The Importance of Local SEO for Businesses in Bangladesh', 'importance-local-seo-businesses-bangladesh', 'With millions of searches happening daily on Google, optimizing your business website for local search terms (like ''web design in dhaka'') is crucial. Local SEO ensures you stand out on Google Maps, drive organic phone calls, and attract high-converting local leads directly to your office.

To make your website local SEO friendly, focus on:

- Google Business Profile: Create and verify your business map location.
- Local Keywords: Include target city names in your titles, headings, and meta descriptions.
- Mobile Optimization: Ensure your site load times are fast, as most local searches happen on mobile devices.
- Consistent NAP: Make sure your Name, Address, and Phone number are identical across all directories and your website footer.', NULL, 'Admin', 'active', '2026-07-05 07:03:43'),
    ('3', 'Why SQLite is the Perfect Database Choice for Small to Mid-Sized Apps', 'why-sqlite-is-perfect-database-small-mid-apps', 'For many business websites and lightweight CRM platforms, running a separate database server like MySQL adds unnecessary deployment overhead, security risks, and costs. SQLite is serverless, zero-configuration, and incredibly fast, offering full relational features right out-of-the-box.

SQLite compiles all database tables and indexes into a single local file. This simplifies migrations, makes local testing extremely easy (like our setup here), and provides sub-millisecond query performance since database reads are direct file operations.', NULL, 'Rahat Chowdhury', 'active', '2026-07-05 07:03:43');

-- Data for pages
INSERT INTO pages (id, title, subtitle, slug, content, image, parent_menu, meta_description, meta_keywords, status, created_at) VALUES
    ('3', 'About Us', 'Innovative CRM & Web Solutions for Modern Enterprises', 'about', '', NULL, NULL, 'Learn about our mission, vision, and core values.', 'about apex digital, software company profile', 'active', '2026-07-05 07:36:40'),
    ('4', 'Our Services', 'Explore what we offer', 'services', NULL, NULL, NULL, 'High-performance web development, custom CRM design, SEO, and cloud hosting.', 'it services, custom software packages', 'active', '2026-07-05 07:36:40'),
    ('5', 'Recently Completed Jobs', 'See our latest work', 'completed-jobs', NULL, NULL, NULL, 'Browse our successful CRM integration and web development case studies.', 'portfolio, software projects, web design cases', 'active', '2026-07-05 07:36:40'),
    ('6', 'Client Reviews', 'What our clients say', 'reviews', NULL, NULL, NULL, 'Read testimonials from our satisfied business clients in Bangladesh and abroad.', 'client testimonials, software reviews, rating', 'active', '2026-07-05 07:36:40'),
    ('7', 'Our Team', 'Meet our experts', 'team', NULL, NULL, NULL, 'Meet the developers, databases leads, and founders behind Apex Digital Solutions.', 'software development team, engineers', 'active', '2026-07-05 07:36:40'),
    ('8', 'Contact Us', 'We''d love to hear from you', 'contact', NULL, NULL, NULL, 'Send us a message or request a quote for your next custom software project.', 'contact software team, support address', 'active', '2026-07-05 07:36:40'),
    ('9', 'Enterprise CRM Service', 'Scalable, High-Performance Customer Portals & CRM Software', 'enterprise-crm-service', NULL, NULL, NULL, 'Custom CRM development for large teams.', 'enterprise crm, crm developer', 'active', '2026-07-05 07:36:40'),
    ('10', 'Home', 'Innovative CRM & Web Solutions for Modern Enterprises', 'home', NULL, NULL, NULL, 'Custom CRM software and business website developers.', 'crm, web design, apex digital', 'active', '2026-07-05 11:16:39');

-- Data for menus
INSERT INTO menus (id, label, link, parent_id, sort_order, created_at) VALUES
    ('1', 'Home', 'index.php', NULL, '1', '2026-07-05 07:33:28'),
    ('2', 'About', 'page.php?slug=about', NULL, '2', '2026-07-05 07:33:28'),
    ('3', 'Services', 'page.php?slug=services', NULL, '3', '2026-07-05 07:33:28'),
    ('4', 'Work', '#', NULL, '4', '2026-07-05 07:33:28'),
    ('5', 'Resources', '#', NULL, '5', '2026-07-05 07:33:28'),
    ('6', 'Contact', 'page.php?slug=contact', NULL, '6', '2026-07-05 07:33:28'),
    ('7', 'All Services', 'services.php', '3', '1', '2026-07-05 07:33:28'),
    ('8', 'Web Development', 'services.php#web-dev', '3', '2', '2026-07-05 07:33:28'),
    ('9', 'CRM Solutions', 'services.php#crm-sol', '3', '3', '2026-07-05 07:33:28'),
    ('10', 'Digital Marketing', 'services.php#marketing', '3', '4', '2026-07-05 07:33:28'),
    ('11', 'Enterprise CRM Service', 'page.php?slug=enterprise-crm-service', '3', '5', '2026-07-05 07:33:28'),
    ('12', 'Completed Jobs', 'page.php?slug=completed-jobs', '4', '1', '2026-07-05 07:33:28'),
    ('13', 'Client Reviews', 'page.php?slug=reviews', '4', '2', '2026-07-05 07:33:28'),
    ('14', 'Our Team', 'page.php?slug=team', '4', '3', '2026-07-05 07:33:28'),
    ('15', 'Blog / Articles', 'blog.php', '5', '1', '2026-07-05 07:33:28'),
    ('16', 'Pricing Plans', 'pricing.php', '5', '2', '2026-07-05 07:33:28'),
    ('17', 'FAQs', 'faq.php', '5', '3', '2026-07-05 07:33:28');

-- Data for page_visits
INSERT INTO page_visits (id, page_url, ip_address, country, city, user_agent, session_id, created_at, last_activity) VALUES
    ('1', '/index.php', '::1', 'Bangladesh', 'Sylhet', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'fsridpimg1273n0j3vscr3ph86', '2026-07-05 11:25:11', '2026-07-05 11:34:01'),
    ('2', '/page.php?slug=services', '::1', 'Germany', 'Berlin', 'Mozilla/5.0 (Linux; Android 15; Pixel 9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', 'fsridpimg1273n0j3vscr3ph86', '2026-07-05 11:26:47', '2026-07-05 11:26:47'),
    ('3', '/index.php', '::1', 'United States', 'San Francisco', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', 'kj6ovmc6gk7tmbv2atq66i1936', '2026-07-05 11:33:07', '2026-07-05 11:33:07'),
    ('4', '/index.php/admin', '::1', 'Japan', 'Tokyo', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', 'kj6ovmc6gk7tmbv2atq66i1936', '2026-07-05 11:33:13', '2026-07-05 11:33:13'),
    ('5', '/index.php/css/style.css', '::1', 'Germany', 'Berlin', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', 'kj6ovmc6gk7tmbv2atq66i1936', '2026-07-05 11:33:13', '2026-07-05 11:33:13'),
    ('6', '/index.php/uploads/img_6a4a03a296bdd.jpg', '::1', 'Bangladesh', 'Chittagong', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', 'kj6ovmc6gk7tmbv2atq66i1936', '2026-07-05 11:33:13', '2026-07-05 11:33:13'),
    ('7', '/index.php/js/script.js', '::1', 'Japan', 'Tokyo', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', 'kj6ovmc6gk7tmbv2atq66i1936', '2026-07-05 11:33:13', '2026-07-05 11:33:14'),
    ('8', '/index.php', '::1', 'United States', 'New York', 'StaticExporter', 'nkvajb7fli609mam3u5t4gum80', '2026-07-05 11:38:37', '2026-07-05 11:38:37'),
    ('9', '/index.php', '::1', 'Japan', 'Tokyo', 'StaticExporter', 'fn15l69usehgljl3veg49uj8ui', '2026-07-05 11:38:37', '2026-07-05 11:38:37'),
    ('10', '/blog.php', '::1', 'Bangladesh', 'Dhaka', 'StaticExporter', '3ig37d8vg14uhs9b0o464ihvu7', '2026-07-05 11:38:37', '2026-07-05 11:38:37'),
    ('11', '/page.php?slug=about', '::1', 'Germany', 'Berlin', 'StaticExporter', '1ub4onuce58slm6870op0od0ck', '2026-07-05 11:38:37', '2026-07-05 11:38:37'),
    ('12', '/page.php?slug=about', '::1', 'Japan', 'Tokyo', 'StaticExporter', '41lu1ag3faehsg0l1ociqrakro', '2026-07-05 11:38:37', '2026-07-05 11:38:37'),
    ('13', '/page.php?slug=services', '::1', 'Bangladesh', 'Dhaka', 'StaticExporter', 'v2dfgo3qd9t2btpgqlvj3cg7jl', '2026-07-05 11:38:37', '2026-07-05 11:38:37'),
    ('14', '/page.php?slug=services', '::1', 'United States', 'San Francisco', 'StaticExporter', 'lgm2njcih8e51duhm45etsvfap', '2026-07-05 11:38:37', '2026-07-05 11:38:37'),
    ('15', '/page.php?slug=completed-jobs', '::1', 'Australia', 'Sydney', 'StaticExporter', '98ajha34el5vqofjqdah42a7pt', '2026-07-05 11:38:37', '2026-07-05 11:38:37'),
    ('16', '/page.php?slug=completed-jobs', '::1', 'United States', 'San Francisco', 'StaticExporter', 'qf7fa08vpr52p19q9rb2lnbkj9', '2026-07-05 11:38:37', '2026-07-05 11:38:37'),
    ('17', '/page.php?slug=reviews', '::1', 'United States', 'San Francisco', 'StaticExporter', '2p8eicqig3gi67tm021kobqg4g', '2026-07-05 11:38:37', '2026-07-05 11:38:37'),
    ('18', '/page.php?slug=reviews', '::1', 'United States', 'New York', 'StaticExporter', 'orflkt4es1jetuasee0jftihdh', '2026-07-05 11:38:37', '2026-07-05 11:38:37'),
    ('19', '/page.php?slug=team', '::1', 'Bangladesh', 'Chittagong', 'StaticExporter', '7vt89aa5bpa8r8jq7t2dt13r8r', '2026-07-05 11:38:37', '2026-07-05 11:38:37'),
    ('20', '/page.php?slug=team', '::1', 'United States', 'New York', 'StaticExporter', '3doiepe7tpv5r8sd3lecbg0hrs', '2026-07-05 11:38:37', '2026-07-05 11:38:37'),
    ('21', '/page.php?slug=contact', '::1', 'Germany', 'Berlin', 'StaticExporter', 'noo9qjvr05d2762rq4ioij7j1f', '2026-07-05 11:38:37', '2026-07-05 11:38:37'),
    ('22', '/page.php?slug=contact', '::1', 'Japan', 'Tokyo', 'StaticExporter', 'adhfgfs77tpcl94cifarsg7858', '2026-07-05 11:38:37', '2026-07-05 11:38:37'),
    ('23', '/page.php?slug=enterprise-crm-service', '::1', 'United Kingdom', 'London', 'StaticExporter', 'c4qeclqe6m4cvj7ep96k9h8om8', '2026-07-05 11:38:37', '2026-07-05 11:38:37'),
    ('24', '/page.php?slug=home', '::1', 'Germany', 'Berlin', 'StaticExporter', 'genda4lvhe9loqicrkd5ek04fn', '2026-07-05 11:38:37', '2026-07-05 11:38:37'),
    ('25', '/blog-detail.php?slug=5-ways-custom-crm-software-can-double-sales-efficiency', '::1', 'Bangladesh', 'Dhaka', 'StaticExporter', '8b3p2g58c1ekc3a1bns1mj59th', '2026-07-05 11:38:37', '2026-07-05 11:38:37'),
    ('26', '/blog-detail.php?slug=importance-local-seo-businesses-bangladesh', '::1', 'Japan', 'Tokyo', 'StaticExporter', '8tn9uflhhoa37odqcg18d56ru1', '2026-07-05 11:38:37', '2026-07-05 11:38:37'),
    ('27', '/blog-detail.php?slug=why-sqlite-is-perfect-database-small-mid-apps', '::1', 'Bangladesh', 'Dhaka', 'StaticExporter', 'tgn807d17ugnl7lfekfhuh2lde', '2026-07-05 11:38:37', '2026-07-05 11:38:37'),
    ('28', '/review-detail.php?id=1', '::1', 'United Kingdom', 'London', 'StaticExporter', 'ilh00ktthunutj7d1v7cju51o5', '2026-07-05 11:38:37', '2026-07-05 11:38:37'),
    ('29', '/review-detail.php?id=2', '::1', 'Australia', 'Sydney', 'StaticExporter', 'ho5tps4hp3c9gstou9vagkpl6c', '2026-07-05 11:38:37', '2026-07-05 11:38:37');

-- Data for page_sections
INSERT INTO page_sections (id, page_id, section_type, title, content, image, sort_order, created_at) VALUES
    ('1', '3', 'team', 'Our Mission & Vision', 'At Apex Digital, our mission is to empower enterprises and startups with state-of-the-art software solutions. We bridge the gap between complex database systems and daily operational needs by building intuitive CRM platforms and responsive websites.', NULL, '1', '2026-07-05 07:36:40'),
    ('2', '3', 'content', 'Why Choose Apex Digital?', 'We believe in absolute transparency, high-performance software engineering, and dedicated support. Our developers work directly with your team to customize modules, build automated report systems, and optimize lead conversion rates.', NULL, '2', '2026-07-05 07:36:40'),
    ('3', '4', 'services', '', 'We provide data-driven web design, secure database integrations, and high-converting marketing campaigns.', NULL, '1', '2026-07-05 07:36:40'),
    ('4', '5', 'jobs', '', 'Explore our recently shipped projects.', NULL, '1', '2026-07-05 07:36:40'),
    ('5', '6', 'reviews', '', 'Read verified customer testimonials.', NULL, '1', '2026-07-05 07:36:40'),
    ('6', '7', 'team', '', 'Meet the engineers who build our CRM platforms.', NULL, '1', '2026-07-05 07:36:40'),
    ('7', '8', 'contact', 'Get a Free Consultation', 'Enter your requirements below, and our lead engineer will get back to you within 24 hours.', NULL, '1', '2026-07-05 07:36:40'),
    ('8', '9', 'content', 'High-Performance CRM Solutions', 'Our Enterprise CRM Service delivers specialized customer portals, team pipeline management, advanced invoice tracking, and direct developer API integrations.

<div style="background:#eff6ff; border:1px solid #bfdbfe; padding:25px; border-radius:10px; margin:30px 0; box-shadow: 0 4px 12px rgba(29, 78, 216, 0.05);">
  <h3 style="color:#1d4ed8; margin-top:0; margin-bottom:12px;">💡 Premium Enterprise Modules</h3>
  <p style="color:#1e40af; margin-bottom:15px; font-weight:500;">Take your operations to the next level with our customized modules:</p>
  <ul style="list-style:disc; margin-left:20px; margin-bottom:20px; color:#1e40af; line-height:1.8;">
    <li><strong>Invoicing Engine:</strong> Generate PDFs and payment links automatically.</li>
    <li><strong>Live Analytics:</strong> Real-time charts for lead conversions and monthly revenues.</li>
    <li><strong>API Integration:</strong> Sync with Slack, Mailchimp, or custom databases.</li>
  </ul>
  <a href="page.php?slug=contact" class="btn" style="display:inline-block; background:#1d4ed8; color:#fff; padding:10px 20px; border-radius:8px; font-weight:600;">Request Custom Setup</a>
</div>', NULL, '1', '2026-07-05 07:36:40'),
    ('9', '3', 'services', 'Our Mission & Vision', 'wgwrhawrhW', NULL, '0', '2026-07-05 07:43:42'),
    ('10', '3', 'content', '', '', NULL, '0', '2026-07-05 07:44:07'),
    ('11', '8', 'team', 'Our Team', '', NULL, '1', '2026-07-05 07:44:55'),
    ('12', '10', 'services', 'Our Services', 'What we offer', NULL, '1', '2026-07-05 11:16:39'),
    ('13', '10', 'jobs', 'Recently Completed Jobs', 'See our latest work', NULL, '2', '2026-07-05 11:16:39'),
    ('14', '10', 'reviews', 'Client Reviews', 'What our clients say', NULL, '3', '2026-07-05 11:16:39');

-- Reset ID Sequences for PostgreSQL Serial Types
SELECT setval(pg_get_serial_sequence('admin_users', 'id'), coalesce(max(id), 1), max(id) IS NOT NULL) FROM admin_users;
SELECT setval(pg_get_serial_sequence('site_settings', 'id'), coalesce(max(id), 1), max(id) IS NOT NULL) FROM site_settings;
SELECT setval(pg_get_serial_sequence('services', 'id'), coalesce(max(id), 1), max(id) IS NOT NULL) FROM services;
SELECT setval(pg_get_serial_sequence('completed_jobs', 'id'), coalesce(max(id), 1), max(id) IS NOT NULL) FROM completed_jobs;
SELECT setval(pg_get_serial_sequence('reviews', 'id'), coalesce(max(id), 1), max(id) IS NOT NULL) FROM reviews;
SELECT setval(pg_get_serial_sequence('teams', 'id'), coalesce(max(id), 1), max(id) IS NOT NULL) FROM teams;
SELECT setval(pg_get_serial_sequence('custom_sections', 'id'), coalesce(max(id), 1), max(id) IS NOT NULL) FROM custom_sections;
SELECT setval(pg_get_serial_sequence('messages', 'id'), coalesce(max(id), 1), max(id) IS NOT NULL) FROM messages;
SELECT setval(pg_get_serial_sequence('blogs', 'id'), coalesce(max(id), 1), max(id) IS NOT NULL) FROM blogs;
SELECT setval(pg_get_serial_sequence('pages', 'id'), coalesce(max(id), 1), max(id) IS NOT NULL) FROM pages;
SELECT setval(pg_get_serial_sequence('menus', 'id'), coalesce(max(id), 1), max(id) IS NOT NULL) FROM menus;
SELECT setval(pg_get_serial_sequence('page_visits', 'id'), coalesce(max(id), 1), max(id) IS NOT NULL) FROM page_visits;
SELECT setval(pg_get_serial_sequence('page_sections', 'id'), coalesce(max(id), 1), max(id) IS NOT NULL) FROM page_sections;