-- ====================================================
-- Migration: Create blog_post table
-- Description: Adds the blog_post table used by api/blog-post-api.php
-- ====================================================

CREATE TABLE IF NOT EXISTS blog_post (
    id INT(11) NOT NULL AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    meta_title VARCHAR(255) DEFAULT NULL,
    meta_description TEXT DEFAULT NULL,
    description TEXT DEFAULT NULL,
    content TEXT DEFAULT NULL,
    publish_date DATE DEFAULT NULL,
    author VARCHAR(255) DEFAULT NULL,
    category VARCHAR(255) DEFAULT NULL,
    tags VARCHAR(255) DEFAULT NULL,
    image_url VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
