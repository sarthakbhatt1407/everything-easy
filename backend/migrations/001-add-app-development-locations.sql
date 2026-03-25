-- ====================================================
-- Migration: Add Support for Application Development Locations
-- Description: Adds service_type column to locations table
-- Version: 1.1
-- ====================================================

-- Add service_type column to locations table if it doesn't exist
-- This allows maintaining separate service locations for website and app development

ALTER TABLE locations 
ADD COLUMN service_type VARCHAR(50) DEFAULT '' 
COMMENT 'Service type: website, application, or empty (both)' 
AFTER meta_description;

-- Create index on service_type for faster queries
CREATE INDEX idx_service_type ON locations(service_type);

-- Insert sample application development locations
-- These use the same structure as website development locations
INSERT INTO locations 
(location_name, city_name, state, slug, meta_title, meta_description, service_type) 
VALUES
('Dehradun Mobile App Development', 'Dehradun', 'Uttarakhand', 'dehradun-app-development', 
 'Professional Mobile App Development Services in Dehradun | EverythingEasy Technology', 
 'Custom mobile app development for iOS and Android in Dehradun. Expert developers delivering scalable applications with cutting-edge technology.', 
 'application'),

('Mumbai App Development', 'Mumbai', 'Maharashtra', 'mumbai-app-development', 
 'Mobile App Development Services in Mumbai | EverythingEasy Technology', 
 'Experienced app developers in Mumbai specializing in iOS, Android, and cross-platform development for startups and enterprises.', 
 'application'),

('Delhi Application Development', 'Delhi', 'Delhi', 'delhi-app-development', 
 'Application Development Company in Delhi | EverythingEasy Technology', 
 'Professional app development services in Delhi. From concept to deployment, we develop web and mobile applications that scale.', 
 'application'),

('Bangalore App Solutions', 'Bangalore', 'Karnataka', 'bangalore-app-development', 
 'Custom App Development in Bangalore | EverythingEasy Technology', 
 'Leading app development company in Bangalore. Specializing in mobile apps, web applications, and enterprise software solutions.', 
 'application');

-- To update existing locations to service both website and app development (set to both):
-- UPDATE locations SET service_type = '' WHERE service_type IS NULL OR service_type = '';

-- To view all application development locations:
-- SELECT * FROM locations WHERE service_type IN ('application', '');

-- To view application development locations by city:
-- SELECT * FROM locations WHERE service_type IN ('application', '') AND city_name = 'Dehradun';
