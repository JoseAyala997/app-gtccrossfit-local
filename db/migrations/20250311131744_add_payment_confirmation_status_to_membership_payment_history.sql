-- Add payment_confirmation_status column to membership_payment_history table
ALTER TABLE `membership_payment_history` 
ADD COLUMN `payment_confirmation_status` ENUM('Pending', 'Confirmed', 'Rejected') NOT NULL DEFAULT 'Pending' COMMENT 'Status of payment confirmation by admin',
ADD COLUMN `confirmed_by` INT NULL COMMENT 'Admin user ID who confirmed the payment',
ADD COLUMN `confirmed_date` DATETIME NULL COMMENT 'Date and time when payment was confirmed',
ADD COLUMN `confirmation_note` TEXT NULL COMMENT 'Admin note about the payment confirmation';
