-- ====================================================
-- Migration: Add status, views, meta_keywords to blog_post
-- Description: Restores draft/publish workflow, view counts, and meta
--               keywords that the old `blogs` table had, now on blog_post
--               (the `blogs` table has been dropped and blog_post is the
--               single source of truth going forward).
-- ====================================================

ALTER TABLE blog_post
    ADD COLUMN status VARCHAR(20) NOT NULL DEFAULT 'published' AFTER tags,
    ADD COLUMN views INT(11) NOT NULL DEFAULT 0 AFTER status,
    ADD COLUMN meta_keywords VARCHAR(255) DEFAULT NULL AFTER meta_description;

CREATE INDEX idx_blog_post_status ON blog_post(status);
