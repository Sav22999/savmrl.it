-- savmrl.it schema migration 009
-- Fixes: user_report_reason column too short (VARCHAR(20) -> VARCHAR(500))
-- VARCHAR(20) truncated JSON with multiple reasons, losing report data
-- Idempotent: safe to run multiple times

ALTER TABLE `redirect_savmrl` MODIFY COLUMN `user_report_reason` VARCHAR(500) DEFAULT NULL;
