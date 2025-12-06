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
