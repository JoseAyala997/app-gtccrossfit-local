ALTER TABLE `gym_member` 
ADD COLUMN `is_maintenance_mode` TINYINT(1) NOT NULL DEFAULT 0,
ADD COLUMN `maintenance_start_date` DATE NULL,
ADD COLUMN `maintenance_end_date` DATE NULL,
ADD COLUMN `maintenance_notes` TEXT NULL;