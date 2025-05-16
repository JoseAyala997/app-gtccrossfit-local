-- Add branch_id column to membership table
ALTER TABLE `membership` 
ADD COLUMN `branch_id` int DEFAULT NULL AFTER `membership_label`,
ADD CONSTRAINT `fk_membership_branch` 
FOREIGN KEY (`branch_id`) REFERENCES `gym_branch` (`id`) 
ON DELETE RESTRICT 
ON UPDATE CASCADE;

-- Update existing memberships to belong to the first branch
UPDATE `membership` SET `branch_id` = (SELECT `id` FROM `gym_branch` ORDER BY `id` LIMIT 1);

-- Add branch_id column to membership_history table
ALTER TABLE `membership_history` 
ADD COLUMN `branch_id` int DEFAULT NULL AFTER `membership_amount`,
ADD CONSTRAINT `fk_membership_history_branch` 
FOREIGN KEY (`branch_id`) REFERENCES `gym_branch` (`id`) 
ON DELETE RESTRICT 
ON UPDATE CASCADE;

-- Update existing membership history records to belong to the first branch
UPDATE `membership_history` SET `branch_id` = (SELECT `id` FROM `gym_branch` ORDER BY `id` LIMIT 1);
