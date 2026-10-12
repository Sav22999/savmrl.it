-- savmrl.it schema migration 001
-- Adds: users, sessions, OTP codes, admins, admin sessions
-- Alters: redirect tables (add user_id, admin_blocked)
-- Idempotent: safe to run multiple times

-- 1. Users table
CREATE TABLE IF NOT EXISTS `users_savmrl`
(
    `id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `username`           VARCHAR(100) NOT NULL,
    `email`              VARCHAR(255) NOT NULL,
    `password_hash`      VARCHAR(255) NOT NULL,
    `email_verified`     TINYINT(1)   NOT NULL DEFAULT 0,
    `pro_features`       TINYINT(1)   NOT NULL DEFAULT 0,
    `two_fa_enabled`     TINYINT(1)   NOT NULL DEFAULT 0,
    `preferred_language` VARCHAR(5)   NOT NULL DEFAULT 'en',
    `created_at`         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_username` (`username`),
    UNIQUE KEY `uq_email` (`email`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_520_ci;

-- 2. User sessions table
CREATE TABLE IF NOT EXISTS `sessions_savmrl`
(
    `id`            VARCHAR(64)  NOT NULL,
    `user_id`       INT UNSIGNED NOT NULL,
    `csrf_token`    VARCHAR(64)  NOT NULL,
    `ip_address`    VARCHAR(45)           DEFAULT NULL,
    `user_agent`    VARCHAR(512)          DEFAULT NULL,
    `created_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `expires_at`    DATETIME     NOT NULL,
    `last_activity` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_expires` (`expires_at`),
    CONSTRAINT `fk_sessions_user` FOREIGN KEY (`user_id`) REFERENCES `users_savmrl` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_520_ci;

-- 3. OTP codes table
CREATE TABLE IF NOT EXISTS `otp_codes_savmrl`
(
    `id`         INT UNSIGNED                             NOT NULL AUTO_INCREMENT,
    `user_id`    INT UNSIGNED                             NOT NULL,
    `code`       VARCHAR(6)                               NOT NULL,
    `purpose`    ENUM ('login_2fa', 'email_verification') NOT NULL DEFAULT 'login_2fa',
    `temp_token` VARCHAR(64)                                       DEFAULT NULL,
    `attempts`   TINYINT UNSIGNED                         NOT NULL DEFAULT 0,
    `created_at` DATETIME                                 NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `expires_at` DATETIME                                 NOT NULL,
    `used`       TINYINT(1)                               NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `idx_user_purpose` (`user_id`, `purpose`),
    KEY `idx_temp_token` (`temp_token`),
    CONSTRAINT `fk_otp_user` FOREIGN KEY (`user_id`) REFERENCES `users_savmrl` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_520_ci;

-- 4. Admins table (separate from users)
CREATE TABLE IF NOT EXISTS `admins_savmrl`
(
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `username`      VARCHAR(100) NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `email`         VARCHAR(255) NOT NULL,
    `created_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `last_login`    DATETIME              DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_admin_username` (`username`),
    UNIQUE KEY `uq_admin_email` (`email`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_520_ci;

-- 5. Admin sessions table (isolated from user sessions)
CREATE TABLE IF NOT EXISTS `admin_sessions_savmrl`
(
    `id`            VARCHAR(64)  NOT NULL,
    `admin_id`      INT UNSIGNED NOT NULL,
    `csrf_token`    VARCHAR(64)  NOT NULL,
    `ip_address`    VARCHAR(45)           DEFAULT NULL,
    `user_agent`    VARCHAR(512)          DEFAULT NULL,
    `created_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `expires_at`    DATETIME     NOT NULL,
    `last_activity` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_admin_id` (`admin_id`),
    KEY `idx_admin_expires` (`expires_at`),
    CONSTRAINT `fk_admin_sessions` FOREIGN KEY (`admin_id`) REFERENCES `admins_savmrl` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_520_ci;

-- 6. Add user_id and admin_blocked to production redirect table (idempotent)
DROP PROCEDURE IF EXISTS `_savmrl_mig_001_redirect`;
DELIMITER //
CREATE PROCEDURE `_savmrl_mig_001_redirect`()
BEGIN
    IF NOT EXISTS (SELECT 1
                   FROM INFORMATION_SCHEMA.COLUMNS
                   WHERE TABLE_SCHEMA = DATABASE()
                     AND TABLE_NAME = 'redirect_savmrl'
                     AND COLUMN_NAME = 'user_id') THEN
        ALTER TABLE `redirect_savmrl`
            ADD COLUMN `user_id` INT UNSIGNED DEFAULT NULL;
    END IF;
    IF NOT EXISTS (SELECT 1
                   FROM INFORMATION_SCHEMA.COLUMNS
                   WHERE TABLE_SCHEMA = DATABASE()
                     AND TABLE_NAME = 'redirect_savmrl'
                     AND COLUMN_NAME = 'admin_blocked') THEN
        ALTER TABLE `redirect_savmrl`
            ADD COLUMN `admin_blocked` TINYINT(1) DEFAULT NULL;
    END IF;
    IF NOT EXISTS (SELECT 1
                   FROM INFORMATION_SCHEMA.STATISTICS
                   WHERE TABLE_SCHEMA = DATABASE()
                     AND TABLE_NAME = 'redirect_savmrl'
                     AND INDEX_NAME = 'idx_user_id') THEN
        ALTER TABLE `redirect_savmrl`
            ADD INDEX `idx_user_id` (`user_id`);
    END IF;
END //
DELIMITER ;
CALL `_savmrl_mig_001_redirect`();
DROP PROCEDURE IF EXISTS `_savmrl_mig_001_redirect`;

-- 7. Add user_id and admin_blocked to alpha redirect table (idempotent)
DROP PROCEDURE IF EXISTS `_savmrl_mig_001_alpha`;
DELIMITER //
CREATE PROCEDURE `_savmrl_mig_001_alpha`()
BEGIN
    IF NOT EXISTS (SELECT 1
                   FROM INFORMATION_SCHEMA.COLUMNS
                   WHERE TABLE_SCHEMA = DATABASE()
                     AND TABLE_NAME = 'redirect_alpha_savmrl'
                     AND COLUMN_NAME = 'user_id') THEN
        ALTER TABLE `redirect_alpha_savmrl`
            ADD COLUMN `user_id` INT UNSIGNED DEFAULT NULL;
    END IF;
    IF NOT EXISTS (SELECT 1
                   FROM INFORMATION_SCHEMA.COLUMNS
                   WHERE TABLE_SCHEMA = DATABASE()
                     AND TABLE_NAME = 'redirect_alpha_savmrl'
                     AND COLUMN_NAME = 'admin_blocked') THEN
        ALTER TABLE `redirect_alpha_savmrl`
            ADD COLUMN `admin_blocked` TINYINT(1) DEFAULT NULL;
    END IF;
    IF NOT EXISTS (SELECT 1
                   FROM INFORMATION_SCHEMA.STATISTICS
                   WHERE TABLE_SCHEMA = DATABASE()
                     AND TABLE_NAME = 'redirect_alpha_savmrl'
                     AND INDEX_NAME = 'idx_alpha_user_id') THEN
        ALTER TABLE `redirect_alpha_savmrl`
            ADD INDEX `idx_alpha_user_id` (`user_id`);
    END IF;
END //
DELIMITER ;
CALL `_savmrl_mig_001_alpha`();
DROP PROCEDURE IF EXISTS `_savmrl_mig_001_alpha`;
