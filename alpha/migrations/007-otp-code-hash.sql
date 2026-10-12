-- Widen OTP code column to accommodate bcrypt hashes
ALTER TABLE `otp_codes_savmrl`
    MODIFY COLUMN `code` VARCHAR(255) NOT NULL;
