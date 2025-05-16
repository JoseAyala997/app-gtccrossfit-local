-- Add receipt_photo column to membership_payment_history table
ALTER TABLE `membership_payment_history` ADD COLUMN `receipt_photo` VARCHAR(255) NULL COMMENT 'Path to uploaded receipt photo';
