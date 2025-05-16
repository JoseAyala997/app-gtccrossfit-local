CREATE TABLE `staff_branches` (
  `id` int NOT NULL AUTO_INCREMENT,
  `staff_id` int NOT NULL,
  `branch_id` int NOT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_staff_branches_staff_id` (`staff_id`),
  KEY `fk_staff_branches_branch_id` (`branch_id`),
  CONSTRAINT `fk_staff_branches_staff_id` FOREIGN KEY (`staff_id`) REFERENCES `gym_member` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_staff_branches_branch_id` FOREIGN KEY (`branch_id`) REFERENCES `gym_branch` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
