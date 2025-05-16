-- Add max_quota and branch_id to class_schedule table
ALTER TABLE `class_schedule`
ADD COLUMN `max_quota` int DEFAULT NULL COMMENT 'Maximum number of students allowed in the class' AFTER `end_time`,
ADD COLUMN `branch_id` int DEFAULT NULL AFTER `max_quota`,
ADD KEY `fk_class_schedule_branch_id` (`branch_id`),
ADD CONSTRAINT `fk_class_schedule_branch_id` FOREIGN KEY (`branch_id`) REFERENCES `gym_branch` (`id`) ON DELETE SET NULL;

-- Update existing records to assign them to the default branch (id = 1)
UPDATE `class_schedule` SET `branch_id` = 1 WHERE `branch_id` IS NULL;

-- Add branch_id to class_schedule_list table for consistency
ALTER TABLE `class_schedule_list`
ADD COLUMN `branch_id` int DEFAULT NULL AFTER `end_time`,
ADD KEY `fk_class_schedule_list_branch_id` (`branch_id`),
ADD CONSTRAINT `fk_class_schedule_list_branch_id` FOREIGN KEY (`branch_id`) REFERENCES `gym_branch` (`id`) ON DELETE SET NULL;

-- Update existing class_schedule_list records to match their parent class_schedule branch
UPDATE `class_schedule_list` csl
INNER JOIN `class_schedule` cs ON csl.class_id = cs.id
SET csl.branch_id = cs.branch_id
WHERE csl.branch_id IS NULL;
