ALTER TABLE `otp_codes_savmrl`
    MODIFY COLUMN `purpose` ENUM('login_2fa', 'email_verification', 'password_reset', 'password_change', 'account_deletion') NOT NULL DEFAULT 'login_2fa';