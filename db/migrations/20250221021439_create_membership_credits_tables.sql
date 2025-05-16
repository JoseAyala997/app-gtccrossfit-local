-- Create table for membership credits definition
CREATE TABLE `membership_credits` (
  `id` int NOT NULL AUTO_INCREMENT,
  `membership_id` int NOT NULL,
  `credits` int NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_membership_credits_membership` (`membership_id`),
  CONSTRAINT `fk_membership_credits_membership` 
    FOREIGN KEY (`membership_id`) REFERENCES `membership` (`id`) 
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create table for defining which branches credits can be used at
CREATE TABLE `membership_credit_branches` (
  `id` int NOT NULL AUTO_INCREMENT,
  `membership_credit_id` int NOT NULL,
  `branch_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_membership_credit_branch` (`membership_credit_id`, `branch_id`),
  KEY `fk_credit_branches_membership_credit` (`membership_credit_id`),
  KEY `fk_credit_branches_branch` (`branch_id`),
  CONSTRAINT `fk_credit_branches_membership_credit` 
    FOREIGN KEY (`membership_credit_id`) REFERENCES `membership_credits` (`id`) 
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_credit_branches_branch` 
    FOREIGN KEY (`branch_id`) REFERENCES `gym_branch` (`id`) 
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create table for tracking member credits
CREATE TABLE `gym_member_credits` (
  `id` int NOT NULL AUTO_INCREMENT,
  `gym_member_membership_id` int NOT NULL,
  `credits_remaining` int NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_member_credits_member_membership` (`gym_member_membership_id`),
  CONSTRAINT `fk_member_credits_member_membership` 
    FOREIGN KEY (`gym_member_membership_id`) REFERENCES `gym_member_memberships` (`id`) 
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create table for tracking credit usage
CREATE TABLE `credit_usage` (
  `id` int NOT NULL AUTO_INCREMENT,
  `gym_member_credit_id` int NOT NULL,
  `class_schedule_id` int NOT NULL,
  `branch_id` int NOT NULL,
  `used_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_credit_usage_member_credit` (`gym_member_credit_id`),
  KEY `fk_credit_usage_class` (`class_schedule_id`),
  KEY `fk_credit_usage_branch` (`branch_id`),
  CONSTRAINT `fk_credit_usage_member_credit` 
    FOREIGN KEY (`gym_member_credit_id`) REFERENCES `gym_member_credits` (`id`) 
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_credit_usage_class` 
    FOREIGN KEY (`class_schedule_id`) REFERENCES `class_schedule` (`id`) 
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_credit_usage_branch` 
    FOREIGN KEY (`branch_id`) REFERENCES `gym_branch` (`id`) 
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
