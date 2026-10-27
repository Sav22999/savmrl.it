-- Migration 010: Enlarge redirect_link column for encrypted URLs
-- Encrypted URLs (AES-256-GCM + base64) can exceed VARCHAR(255).
-- Change to TEXT to support any URL length.

ALTER TABLE `redirect_savmrl` MODIFY COLUMN `redirect_link` TEXT NOT NULL;
