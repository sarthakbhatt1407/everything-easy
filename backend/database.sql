-- Everything Easy Database Setup
-- Create database
CREATE DATABASE IF NOT EXISTS everything_easy;
USE everything_easy;

-- Create quotes table
CREATE TABLE IF NOT EXISTS quotes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    company_name VARCHAR(255),
    service VARCHAR(100) NOT NULL,
    budget VARCHAR(50),
    timeline VARCHAR(50),
    project_details TEXT NOT NULL,
    newsletter TINYINT(1) DEFAULT 0,
    status ENUM('pending', 'in-progress', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample data for testing
INSERT INTO quotes (first_name, last_name, email, phone, company_name, service, budget, timeline, project_details, status, created_at) VALUES
('John', 'Doe', 'john.doe@example.com', '+1 (555) 123-4567', 'Tech Solutions Inc.', 'web-development', '5k-10k', '2-3-months', 'We need a responsive website for our IT services company with modern design, contact forms, and service showcase pages.', 'pending', '2025-12-05 10:30:00'),
('Sarah', 'Miller', 'sarah.m@example.com', '+1 (555) 987-6543', 'Digital Agency', 'app-development', '10k-25k', '3-6-months', 'Looking for a mobile app with user authentication, payment integration, and real-time notifications.', 'in-progress', '2025-12-04 14:20:00'),
('Robert', 'Johnson', 'robert.j@example.com', '+1 (555) 456-7890', 'StartUp Co.', 'seo', 'under-5k', '1-month', 'Need SEO optimization for our existing website to improve search rankings and organic traffic.', 'completed', '2025-12-03 09:15:00'),
('Emily', 'White', 'emily.white@example.com', '+1 (555) 234-5678', 'E-Commerce Ltd.', 'web-development', '25k-50k', '3-6-months', 'Complete e-commerce platform with inventory management, payment gateway, and admin dashboard.', 'pending', '2025-12-02 16:45:00'),
('Michael', 'Brown', 'michael.b@example.com', '+1 (555) 345-6789', 'Creative Studio', 'seo', '5k-10k', '2-3-months', 'Comprehensive SEO and digital marketing campaign for brand awareness and lead generation.', 'in-progress', '2025-12-01 11:30:00');

-- Create blogs table
CREATE TABLE IF NOT EXISTS blogs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    excerpt TEXT NOT NULL,
    content LONGTEXT NOT NULL,
    image_url VARCHAR(500) NOT NULL,
    category VARCHAR(100) NOT NULL,
    author VARCHAR(100) NOT NULL,
    status ENUM('published', 'draft') DEFAULT 'draft',
    tags TEXT,
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_category (category),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample blog posts
INSERT INTO blogs (title, excerpt, content, image_url, category, author, status, tags, views, created_at) VALUES
('Top Digital Marketing Trends to Watch in 2025', 
 'Discover the latest digital marketing strategies that will dominate 2025. From AI-powered campaigns to personalized customer experiences...', 
 '<p class="lead">Digital marketing is evolving faster than ever, and 2025 promises to bring exciting new opportunities for businesses to connect with their audiences.</p><h3 class="fw-bold mt-4 mb-3">1. AI-Powered Personalization</h3><p>Artificial Intelligence is revolutionizing how businesses interact with customers. AI-powered tools can now analyze customer behavior, predict preferences, and deliver personalized content at scale.</p><h3 class="fw-bold mt-4 mb-3">2. Voice Search Optimization</h3><p>With the rise of smart speakers and voice assistants, optimizing for voice search is no longer optional. Businesses need to focus on conversational keywords and natural language.</p><h3 class="fw-bold mt-4 mb-3">3. Video Marketing Dominance</h3><p>Video content continues to reign supreme in digital marketing. Short-form videos on platforms like TikTok, Instagram Reels, and YouTube Shorts are capturing attention like never before.</p>',
 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&h=600&q=80',
 'Digital Marketing',
 'Admin',
 'published',
 'digital marketing, trends, ai, video marketing',
 156,
 '2025-12-05 10:00:00'),

('Building a Successful E-commerce Platform',
 'Learn the essential steps to create a profitable online store. From choosing the right platform to optimizing user experience...',
 '<p class="lead">Creating a successful e-commerce platform requires careful planning, the right technology stack, and a focus on user experience.</p><h3 class="fw-bold mt-4 mb-3">Choose the Right Platform</h3><p>Selecting the right e-commerce platform is crucial. Options like Shopify, WooCommerce, Magento, and custom solutions each have their advantages.</p><h3 class="fw-bold mt-4 mb-3">User Experience is King</h3><p>Your website should be intuitive, fast, and mobile-responsive. Implement clear navigation, high-quality product images, detailed descriptions, and an easy checkout process.</p>',
 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&h=600&q=80',
 'Web Development',
 'Admin',
 'published',
 'ecommerce, web development, online store',
 89,
 '2025-12-03 14:30:00'),

('Why Your Business Needs a Modern Website',
 'Explore how a modern, responsive website can transform your business and attract more customers in today''s digital age...',
 '<p class="lead">In today''s digital-first world, your website is often the first interaction potential customers have with your business.</p><h3 class="fw-bold mt-4 mb-3">First Impressions Matter</h3><p>Research shows that users form an opinion about your website in just 0.05 seconds. A modern, professional website builds credibility and trust instantly.</p><h3 class="fw-bold mt-4 mb-3">Mobile Responsiveness is Critical</h3><p>Over 60% of web traffic now comes from mobile devices. A modern website must provide an excellent experience across all screen sizes.</p>',
 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&h=600&q=80',
 'Web Development',
 'Admin',
 'published',
 'website, responsive design, business',
 124,
 '2025-12-01 09:15:00');
