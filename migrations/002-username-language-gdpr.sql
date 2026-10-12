-- savmrl.it schema migration 002
-- Adds preferred_language, indexes for GDPR cleanup
-- Idempotent: safe to run multiple times

DROP PROCEDURE IF EXISTS `_savmrl_mig_002`;
DELIMITER //
CREATE PROCEDURE `_savmrl_mig_002`()
BEGIN
    -- Add preferred_language if not present
    IF NOT EXISTS (SELECT 1
                   FROM INFORMATION_SCHEMA.COLUMNS
                   WHERE TABLE_SCHEMA = DATABASE()
                     AND TABLE_NAME = 'users_savmrl'
                     AND COLUMN_NAME = 'preferred_language') THEN
        ALTER TABLE `users_savmrl`
            ADD COLUMN `preferred_language` VARCHAR(5) NOT NULL DEFAULT 'en' AFTER `two_fa_enabled`;
    END IF;

    -- Index for GDPR cleanup on opened tables
    IF NOT EXISTS (SELECT 1
                   FROM INFORMATION_SCHEMA.STATISTICS
                   WHERE TABLE_SCHEMA = DATABASE()
                     AND TABLE_NAME = 'opened_savmrl'
                     AND INDEX_NAME = 'idx_visited_timestamp') THEN
        ALTER TABLE `opened_savmrl`
            ADD INDEX `idx_visited_timestamp` (`visited_timestamp`);
    END IF;

    IF NOT EXISTS (SELECT 1
                   FROM INFORMATION_SCHEMA.STATISTICS
                   WHERE TABLE_SCHEMA = DATABASE()
                     AND TABLE_NAME = 'opened_alpha_savmrl'
                     AND INDEX_NAME = 'idx_alpha_visited_timestamp') THEN
        ALTER TABLE `opened_alpha_savmrl`
            ADD INDEX `idx_alpha_visited_timestamp` (`visited_timestamp`);
    END IF;
END //
DELIMITER ;
CALL `_savmrl_mig_002`();
DROP PROCEDURE IF EXISTS `_savmrl_mig_002`;
