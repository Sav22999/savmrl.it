-- savmrl.it schema migration 008
-- Adds: user_reports column to redirect table for tracking user-submitted reports
-- The existing `reported` column remains for admin-level flags
-- Idempotent: safe to run multiple times

SET @tbl = (SELECT DATABASE());

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @tbl AND TABLE_NAME = 'redirect_savmrl' AND COLUMN_NAME = 'user_reports');

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `redirect_savmrl` ADD COLUMN `user_reports` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `reported`',
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @col_exists2 = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @tbl AND TABLE_NAME = 'redirect_savmrl' AND COLUMN_NAME = 'user_report_reason');

SET @sql2 = IF(@col_exists2 = 0,
    'ALTER TABLE `redirect_savmrl` ADD COLUMN `user_report_reason` VARCHAR(500) DEFAULT NULL AFTER `user_reports`',
    'SELECT 1');
PREPARE stmt2 FROM @sql2;
EXECUTE stmt2;
DEALLOCATE PREPARE stmt2;
