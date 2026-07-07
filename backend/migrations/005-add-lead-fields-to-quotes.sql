-- ====================================================
-- Migration: Add lead qualification fields to quotes
-- Description: Lets the admin tag where a lead came from, whether it's a
--              genuine lead vs spam/fake/internship/job inquiry, and keep
--              follow-up remarks/notes on each lead.
-- ====================================================

ALTER TABLE quotes
    ADD COLUMN lead_source VARCHAR(50) DEFAULT NULL COMMENT 'google_ads, seo, referral, social_media, direct, other' AFTER timeline,
    ADD COLUMN lead_category VARCHAR(20) NOT NULL DEFAULT 'genuine' COMMENT 'genuine, spam, fake, internship, job' AFTER status,
    ADD COLUMN remarks TEXT DEFAULT NULL COMMENT 'Internal follow-up notes, not shown to client' AFTER lead_category;

CREATE INDEX idx_lead_category ON quotes(lead_category);
