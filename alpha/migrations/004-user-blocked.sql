-- savmrl.it schema migration 004
-- Adds blocked column to users table
-- Idempotent: safe to run multiple times

DROP PROCEDURE IF EXISTS `_savmrl_mig_004`;
DELIMITER //
CREATE PROCEDURE `_savmrl_mig_004`()
BEGIN
    IF NOT EXISTS (SELECT 1
                   FROM INFORMATION_SCHEMA.COLUMNS
                   WHERE TABLE_SCHEMA = DATABASE()
                     AND TABLE_NAME = 'users_savmrl'
                     AND COLUMN_NAME = 'blocked') THEN
        ALTER TABLE `users_savmrl`
            ADD COLUMN `blocked` TINYINT(1) NOT NULL DEFAULT 0 AFTER `two_fa_enabled`;
    END IF;
END //
DELIMITER ;
CALL `_savmrl_mig_004`();
DROP PROCEDURE IF EXISTS `_savmrl_mig_004`;
