-- savmrl.it schema migration 003
-- Adds redirect_seconds to redirect tables (per-link redirect delay)
-- Idempotent: safe to run multiple times

DROP PROCEDURE IF EXISTS `_savmrl_mig_003`;
DELIMITER //
CREATE PROCEDURE `_savmrl_mig_003`()
BEGIN
    IF NOT EXISTS (SELECT 1
                   FROM INFORMATION_SCHEMA.COLUMNS
                   WHERE TABLE_SCHEMA = DATABASE()
                     AND TABLE_NAME = 'redirect_savmrl'
                     AND COLUMN_NAME = 'redirect_seconds') THEN
        ALTER TABLE `redirect_savmrl`
            ADD COLUMN `redirect_seconds` TINYINT UNSIGNED DEFAULT NULL AFTER `expiry_date`;
    END IF;

    IF NOT EXISTS (SELECT 1
                   FROM INFORMATION_SCHEMA.COLUMNS
                   WHERE TABLE_SCHEMA = DATABASE()
                     AND TABLE_NAME = 'redirect_alpha_savmrl'
                     AND COLUMN_NAME = 'redirect_seconds') THEN
        ALTER TABLE `redirect_alpha_savmrl`
            ADD COLUMN `redirect_seconds` TINYINT UNSIGNED DEFAULT NULL AFTER `expiry_date`;
    END IF;
END //
DELIMITER ;
CALL `_savmrl_mig_003`();
DROP PROCEDURE IF EXISTS `_savmrl_mig_003`;
