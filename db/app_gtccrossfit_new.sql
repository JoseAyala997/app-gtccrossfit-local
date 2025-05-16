-- -------------------------------------------------------------
-- TablePlus 6.0.0(550)
--
-- https://tableplus.com/
--
-- Database: app_gtccrossfit
-- Generation Time: 2025-02-13 15:38:10.7140
-- -------------------------------------------------------------


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


DROP TABLE IF EXISTS `activity`;
CREATE TABLE `activity` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cat_id` int DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `assigned_to` int DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=506 DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `activity_video`;
CREATE TABLE `activity_video` (
  `id` int NOT NULL AUTO_INCREMENT,
  `activity_id` int DEFAULT NULL,
  `video` text,
  `created_at` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `class_booking`;
CREATE TABLE `class_booking` (
  `booking_id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `mobile_no` varchar(100) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `zipcode` int DEFAULT NULL,
  `class_id` varchar(10) DEFAULT NULL,
  `booking_date` date DEFAULT NULL,
  `booking_type` varchar(20) DEFAULT NULL,
  `booking_amount` varchar(50) DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `payment_by` varchar(10) DEFAULT NULL,
  `status` varchar(10) DEFAULT NULL,
  `created_at` date DEFAULT NULL,
  PRIMARY KEY (`booking_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `class_schedule`;
CREATE TABLE `class_schedule` (
  `id` int NOT NULL AUTO_INCREMENT,
  `class_name` varchar(100) DEFAULT NULL,
  `assign_staff_mem` int DEFAULT NULL,
  `assistant_staff_member` int DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `class_fees` int DEFAULT NULL,
  `days` varchar(200) DEFAULT NULL,
  `start_time` varchar(30) DEFAULT NULL,
  `end_time` varchar(30) DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `class_schedule_list`;
CREATE TABLE `class_schedule_list` (
  `id` int NOT NULL AUTO_INCREMENT,
  `class_id` int DEFAULT NULL,
  `days` varchar(255) DEFAULT NULL,
  `start_time` varchar(20) DEFAULT NULL,
  `end_time` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `general_setting`;
CREATE TABLE `general_setting` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `start_year` varchar(50) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `office_number` varchar(20) DEFAULT NULL,
  `country` text,
  `email` varchar(100) DEFAULT NULL,
  `date_format` varchar(15) DEFAULT NULL,
  `calendar_lang` text,
  `gym_logo` varchar(200) DEFAULT NULL,
  `cover_image` varchar(200) DEFAULT NULL,
  `weight` varchar(100) DEFAULT NULL,
  `height` varchar(100) DEFAULT NULL,
  `chest` varchar(100) DEFAULT NULL,
  `waist` varchar(100) DEFAULT NULL,
  `thing` varchar(100) DEFAULT NULL,
  `arms` varchar(100) DEFAULT NULL,
  `fat` varchar(100) DEFAULT NULL,
  `member_can_view_other` int DEFAULT NULL,
  `staff_can_view_own_member` int DEFAULT NULL,
  `enable_sandbox` int DEFAULT NULL,
  `paypal_email` varchar(50) DEFAULT NULL,
  `currency` varchar(20) DEFAULT NULL,
  `enable_alert` int DEFAULT NULL,
  `reminder_days` varchar(100) DEFAULT NULL,
  `reminder_message` varchar(255) DEFAULT NULL,
  `enable_message` int DEFAULT NULL,
  `left_header` varchar(100) DEFAULT NULL,
  `footer` varchar(100) DEFAULT NULL,
  `system_installed` int DEFAULT NULL,
  `enable_rtl` int DEFAULT '0',
  `datepicker_lang` text,
  `time_zone` varchar(20) NOT NULL DEFAULT 'UTC',
  `system_version` text,
  `sys_language` varchar(20) NOT NULL DEFAULT 'en',
  `header_color` varchar(10) DEFAULT NULL,
  `sidemenu_color` varchar(10) DEFAULT NULL,
  `stripe_secret_key` text,
  `stripe_publishable_key` text,
  `stripe_product_created` tinyint NOT NULL DEFAULT '0',
  `stripe_product_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `gym_accessright`;
CREATE TABLE `gym_accessright` (
  `id` int NOT NULL AUTO_INCREMENT,
  `controller` text,
  `action` text,
  `menu` text,
  `menu_icon` text,
  `menu_title` text,
  `member` int DEFAULT NULL,
  `staff_member` int DEFAULT NULL,
  `accountant` int DEFAULT NULL,
  `page_link` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `gym_assign_workout`;
CREATE TABLE `gym_assign_workout` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `level_id` int DEFAULT NULL,
  `description` text,
  `direct_assign` tinyint(1) DEFAULT NULL,
  `created_date` date DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `gym_attendance`;
CREATE TABLE `gym_attendance` (
  `attendance_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `class_id` int DEFAULT NULL,
  `attendance_date` date DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `attendance_by` int DEFAULT NULL,
  `role_name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`attendance_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `gym_branch`;
CREATE TABLE `gym_branch` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `notes` text,
  `created_date` date DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `gym_daily_workout`;
CREATE TABLE `gym_daily_workout` (
  `id` int NOT NULL AUTO_INCREMENT,
  `workout_id` int DEFAULT NULL,
  `member_id` int DEFAULT NULL,
  `record_date` date DEFAULT NULL,
  `result_measurment` varchar(50) DEFAULT NULL,
  `result` varchar(100) DEFAULT NULL,
  `duration` varchar(100) DEFAULT NULL,
  `assigned_by` int DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `time_of_workout` varchar(50) DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL,
  `reminder_status` tinyint DEFAULT '0',
  `note` text,
  `created_by` int DEFAULT NULL,
  `created_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `gym_event_place`;
CREATE TABLE `gym_event_place` (
  `id` int NOT NULL AUTO_INCREMENT,
  `place` varchar(100) DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `gym_group`;
CREATE TABLE `gym_group` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(200) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `gym_income_expense`;
CREATE TABLE `gym_income_expense` (
  `id` int NOT NULL AUTO_INCREMENT,
  `invoice_type` varchar(100) DEFAULT NULL,
  `invoice_label` varchar(100) DEFAULT NULL,
  `supplier_name` varchar(100) DEFAULT NULL,
  `entry` text,
  `payment_status` varchar(50) DEFAULT NULL,
  `total_amount` double DEFAULT NULL,
  `receiver_id` int DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `gym_interest_area`;
CREATE TABLE `gym_interest_area` (
  `id` int NOT NULL AUTO_INCREMENT,
  `interest` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `gym_levels`;
CREATE TABLE `gym_levels` (
  `id` int NOT NULL AUTO_INCREMENT,
  `level` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `gym_measurement`;
CREATE TABLE `gym_measurement` (
  `id` int NOT NULL AUTO_INCREMENT,
  `result_measurment` varchar(100) DEFAULT NULL,
  `result` float DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `result_date` date DEFAULT NULL,
  `image` varchar(50) DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `gym_member`;
CREATE TABLE `gym_member` (
  `id` int NOT NULL AUTO_INCREMENT,
  `activated` int DEFAULT NULL,
  `role_name` text,
  `member_id` text,
  `token` varchar(300) DEFAULT NULL,
  `stripe_customer_id` varchar(255) DEFAULT NULL,
  `is_exist` tinyint NOT NULL DEFAULT '0',
  `first_name` varchar(100) DEFAULT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `member_type` text,
  `role` int DEFAULT NULL,
  `branch_id` int DEFAULT NULL,
  `s_specialization` varchar(255) DEFAULT NULL,
  `gender` text,
  `birth_date` date DEFAULT NULL,
  `assign_class` int DEFAULT NULL,
  `assign_group` varchar(150) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `zipcode` varchar(100) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `weight` varchar(10) DEFAULT NULL,
  `height` varchar(10) DEFAULT NULL,
  `chest` varchar(10) DEFAULT NULL,
  `waist` varchar(10) DEFAULT NULL,
  `thing` varchar(10) DEFAULT NULL,
  `arms` varchar(10) DEFAULT NULL,
  `fat` varchar(10) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `image` varchar(200) DEFAULT NULL,
  `assign_staff_mem` int DEFAULT NULL,
  `intrested_area` int DEFAULT NULL,
  `g_source` int DEFAULT NULL,
  `referrer_by` int DEFAULT NULL,
  `inquiry_date` date DEFAULT NULL,
  `trial_end_date` date DEFAULT NULL,
  `selected_membership` varchar(100) DEFAULT NULL,
  `membership_status` text,
  `membership_valid_from` date DEFAULT NULL,
  `membership_valid_to` date DEFAULT NULL,
  `first_pay_date` date DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_date` date DEFAULT NULL,
  `alert_sent` int DEFAULT NULL,
  `admin_alert` int DEFAULT '0',
  `alert_send_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_gym_members_branch` (`branch_id`),
  CONSTRAINT `fk_gym_members_branch` FOREIGN KEY (`branch_id`) REFERENCES `gym_branch` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `gym_member_class`;
CREATE TABLE `gym_member_class` (
  `id` int NOT NULL AUTO_INCREMENT,
  `member_id` int DEFAULT NULL,
  `assign_class` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=129 DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `gym_message`;
CREATE TABLE `gym_message` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sender` int DEFAULT NULL,
  `receiver` int DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `subject` varchar(150) DEFAULT NULL,
  `message_body` text,
  `status` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `gym_newsletter`;
CREATE TABLE `gym_newsletter` (
  `id` int NOT NULL AUTO_INCREMENT,
  `api_key` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `gym_notice`;
CREATE TABLE `gym_notice` (
  `id` int NOT NULL AUTO_INCREMENT,
  `notice_title` varchar(100) DEFAULT NULL,
  `notice_for` text,
  `class_id` int DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `comment` varchar(200) DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `gym_nutrition`;
CREATE TABLE `gym_nutrition` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `day` varchar(50) DEFAULT NULL,
  `breakfast` text,
  `midmorning_snack` text,
  `lunch` text,
  `afternoon_snack` text,
  `dinner` text,
  `afterdinner_snack` text,
  `start_date` varchar(20) DEFAULT NULL,
  `expire_date` varchar(20) DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `gym_nutrition_data`;
CREATE TABLE `gym_nutrition_data` (
  `id` int NOT NULL AUTO_INCREMENT,
  `day_name` varchar(30) DEFAULT NULL,
  `nutrition_time` varchar(30) DEFAULT NULL,
  `nutrition_value` text,
  `nutrition_id` int DEFAULT NULL,
  `created_date` date DEFAULT NULL,
  `create_by` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `gym_product`;
CREATE TABLE `gym_product` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_name` varchar(100) DEFAULT NULL,
  `price` double DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `gym_reservation`;
CREATE TABLE `gym_reservation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `event_name` varchar(100) DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `start_time` varchar(20) DEFAULT NULL,
  `end_time` varchar(20) DEFAULT NULL,
  `place_id` int DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `gym_roles`;
CREATE TABLE `gym_roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `gym_source`;
CREATE TABLE `gym_source` (
  `id` int NOT NULL AUTO_INCREMENT,
  `source_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `gym_store`;
CREATE TABLE `gym_store` (
  `id` int NOT NULL AUTO_INCREMENT,
  `member_id` int DEFAULT NULL,
  `sell_date` date DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `price` double DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `sell_by` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `gym_user_workout`;
CREATE TABLE `gym_user_workout` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_workout_id` int DEFAULT NULL,
  `workout_name` int DEFAULT NULL,
  `sets` int DEFAULT NULL,
  `reps` int DEFAULT NULL,
  `kg` float DEFAULT NULL,
  `rest_time` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `gym_workout_data`;
CREATE TABLE `gym_workout_data` (
  `id` int NOT NULL AUTO_INCREMENT,
  `day_name` varchar(15) DEFAULT NULL,
  `workout_name` varchar(100) DEFAULT NULL,
  `sets` int DEFAULT NULL,
  `reps` int DEFAULT NULL,
  `kg` float DEFAULT NULL,
  `time` int DEFAULT NULL,
  `workout_id` int DEFAULT NULL,
  `created_date` date DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `installment_plan`;
CREATE TABLE `installment_plan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `number` int DEFAULT NULL,
  `duration` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `membership`;
CREATE TABLE `membership` (
  `id` int NOT NULL AUTO_INCREMENT,
  `membership_label` varchar(100) DEFAULT NULL,
  `membership_cat_id` int DEFAULT NULL,
  `membership_length` int DEFAULT NULL,
  `membership_class_limit` varchar(20) DEFAULT NULL,
  `limit_days` int DEFAULT NULL,
  `limitation` varchar(20) DEFAULT NULL,
  `install_plan_id` int DEFAULT NULL,
  `membership_amount` double DEFAULT NULL,
  `membership_class` varchar(255) DEFAULT NULL,
  `installment_amount` double DEFAULT NULL,
  `signup_fee` double DEFAULT NULL,
  `gmgt_membershipimage` varchar(255) DEFAULT NULL,
  `created_date` date DEFAULT NULL,
  `created_by_id` int DEFAULT NULL,
  `membership_description` text,
  `activated_on_stripe` tinyint NOT NULL DEFAULT '0',
  `stripe_plan_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `membership_activity`;
CREATE TABLE `membership_activity` (
  `id` int NOT NULL AUTO_INCREMENT,
  `activity_id` int DEFAULT NULL,
  `membership_id` int DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `membership_history`;
CREATE TABLE `membership_history` (
  `id` int NOT NULL AUTO_INCREMENT,
  `member_id` int DEFAULT NULL,
  `selected_membership` int DEFAULT NULL,
  `assign_staff_mem` int DEFAULT NULL,
  `intrested_area` int DEFAULT NULL,
  `g_source` int DEFAULT NULL,
  `referrer_by` int DEFAULT NULL,
  `inquiry_date` date DEFAULT NULL,
  `trial_end_date` date DEFAULT NULL,
  `membership_valid_from` date DEFAULT NULL,
  `membership_valid_to` date DEFAULT NULL,
  `first_pay_date` date DEFAULT NULL,
  `created_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `membership_payment`;
CREATE TABLE `membership_payment` (
  `mp_id` int NOT NULL AUTO_INCREMENT,
  `member_id` int DEFAULT NULL,
  `membership_id` int DEFAULT NULL,
  `membership_amount` double DEFAULT NULL,
  `paid_amount` double DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `membership_status` varchar(50) DEFAULT NULL,
  `payment_status` varchar(20) DEFAULT NULL,
  `created_date` date DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  PRIMARY KEY (`mp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `membership_payment_history`;
CREATE TABLE `membership_payment_history` (
  `payment_history_id` bigint NOT NULL AUTO_INCREMENT,
  `mp_id` int DEFAULT NULL,
  `amount` int DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `paid_by_date` date DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `trasaction_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`payment_history_id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `specialization`;
CREATE TABLE `specialization` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `staff_branches`;
CREATE TABLE `staff_branches` (
  `id` int NOT NULL AUTO_INCREMENT,
  `staff_id` int NOT NULL,
  `branch_id` int NOT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_staff_branches_staff_id` (`staff_id`),
  KEY `fk_staff_branches_branch_id` (`branch_id`),
  CONSTRAINT `fk_staff_branches_branch_id` FOREIGN KEY (`branch_id`) REFERENCES `gym_branch` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_staff_branches_staff_id` FOREIGN KEY (`staff_id`) REFERENCES `gym_member` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

INSERT INTO `activity` (`id`, `cat_id`, `title`, `assigned_to`, `created_by`, `created_date`) VALUES
(1, 5, 'Kelly', 2, 1, '2024-10-29'),
(2, 5, 'Kendrick', 2, 1, '2024-10-29'),
(3, 5, 'Lane', 2, 1, '2024-10-29'),
(4, 5, 'Legion 8', 2, 1, '2024-10-29'),
(5, 5, 'Liam', 2, 1, '2024-10-29'),
(6, 5, 'Linda', 2, 1, '2024-10-29'),
(7, 5, 'Lola', 2, 1, '2024-10-29'),
(8, 5, 'Loredo', 2, 1, '2024-10-29'),
(9, 5, 'Lucy', 2, 1, '2024-10-29'),
(10, 5, 'Lumberjack 20', 2, 1, '2024-10-29'),
(11, 5, 'Lurong Champion Test', 2, 1, '2024-10-29'),
(12, 3, 'Atlas Stone to Shoulder', 2, 1, '2024-10-29'),
(13, 3, 'Axle Clean', 2, 1, '2024-10-29'),
(14, 3, 'Axle Deadlift', 2, 1, '2024-10-29'),
(15, 3, 'Axle Hang Power Clean', 2, 1, '2024-10-29'),
(16, 3, 'Axle Power Clean', 2, 1, '2024-10-29'),
(17, 3, 'Axle Push Jerk', 2, 1, '2024-10-29'),
(18, 3, 'Axle Push Press', 2, 1, '2024-10-29'),
(19, 3, 'Axle Shoulder Press', 2, 1, '2024-10-29'),
(20, 3, 'Back Lunge', 2, 1, '2024-10-29'),
(21, 3, 'Back Squat', 2, 1, '2024-10-29'),
(22, 3, 'Banded Deadlift', 2, 1, '2024-10-29'),
(23, 3, 'Banded Sumo Deadlift', 2, 1, '2024-10-29'),
(24, 3, 'Barbell Box Step Up', 2, 1, '2024-10-29'),
(25, 3, 'Bear Complex', 2, 1, '2024-10-29'),
(26, 3, 'Bench Press', 2, 1, '2024-10-29'),
(27, 3, 'Bent Over Row', 2, 1, '2024-10-29'),
(28, 3, 'Bottom Up Back Squat', 2, 1, '2024-10-29'),
(29, 3, 'Bottom Up Front Squat', 2, 1, '2024-10-29'),
(30, 3, 'Bottom Up Overhead Squat', 2, 1, '2024-10-29'),
(31, 3, 'Box Squat', 2, 1, '2024-10-29'),
(32, 3, 'Clean', 2, 1, '2024-10-29'),
(33, 3, 'Clean Extension', 2, 1, '2024-10-29'),
(34, 3, 'Clean Pull', 2, 1, '2024-10-29'),
(35, 3, 'Clean and Jerk', 2, 1, '2024-10-29'),
(36, 3, 'Cluster', 2, 1, '2024-10-29'),
(37, 3, 'Curtis P', 2, 1, '2024-10-29'),
(38, 3, 'Deadlift', 2, 1, '2024-10-29'),
(39, 3, 'Dumbbell Box Step Up', 2, 1, '2024-10-29'),
(40, 3, 'Farmer Walk', 2, 1, '2024-10-29'),
(41, 3, 'Farmers Deadlift ', 2, 1, '2024-10-29'),
(42, 3, 'Floor Press', 2, 1, '2024-10-29'),
(43, 3, 'Frankenstein Squat', 2, 1, '2024-10-29'),
(44, 3, 'Front Lunge', 2, 1, '2024-10-29'),
(45, 3, 'Front Squat', 2, 1, '2024-10-29'),
(46, 3, 'Good Morning', 2, 1, '2024-10-29'),
(47, 3, 'Hang Cluster', 2, 1, '2024-10-29'),
(48, 3, 'Hang Power Clean', 2, 1, '2024-10-29'),
(49, 3, 'Hang Power Snatch', 2, 1, '2024-10-29'),
(50, 3, 'Hang Squat Clean', 2, 1, '2024-10-29'),
(51, 3, 'Hang Squat Snatch', 2, 1, '2024-10-29'),
(52, 3, 'High Hang Power Clean', 2, 1, '2024-10-29'),
(53, 3, 'High Hang Power Snatch', 2, 1, '2024-10-29'),
(54, 3, 'High Hang Squat Clean', 2, 1, '2024-10-29'),
(55, 3, 'High Hang Squat Snatch', 2, 1, '2024-10-29'),
(56, 3, 'Jerk Drive', 2, 1, '2024-10-29'),
(57, 3, 'Keg Carry', 2, 1, '2024-10-29'),
(58, 3, 'Keg Ground to Overhead', 2, 1, '2024-10-29'),
(59, 3, 'Keg Ground to Shoulder', 2, 1, '2024-10-29'),
(60, 3, 'Kettlebel Box Step Up', 2, 1, '2024-10-29'),
(61, 3, 'Kettlebel Clean', 2, 1, '2024-10-29'),
(62, 3, 'Kettlebel Shoulder Press', 2, 1, '2024-10-29'),
(63, 3, 'Kettlebel Snatch', 2, 1, '2024-10-29'),
(64, 3, 'Kettlebell Swing', 2, 1, '2024-10-29'),
(65, 3, 'Medball Clean', 2, 1, '2024-10-29'),
(66, 3, 'Muscle Clean', 2, 1, '2024-10-29'),
(67, 3, 'Muscle Snatch', 2, 1, '2024-10-29'),
(68, 3, 'Overhead Lunge', 2, 1, '2024-10-29'),
(69, 3, 'Overhead Squat', 2, 1, '2024-10-29'),
(70, 3, 'Power Clean', 2, 1, '2024-10-29'),
(71, 3, 'Power Snatch', 2, 1, '2024-10-29'),
(72, 3, 'Press', 2, 1, '2024-10-29'),
(73, 3, 'Pull Sled', 2, 1, '2024-10-29'),
(74, 3, 'Push Jerk', 2, 1, '2024-10-29'),
(75, 3, 'Push Press', 2, 1, '2024-10-29'),
(76, 3, 'Push Sled', 2, 1, '2024-10-29'),
(77, 3, 'Romanian Deadlift', 2, 1, '2024-10-29'),
(78, 3, 'Seated Press', 2, 1, '2024-10-29'),
(79, 3, 'Shoulder Press', 2, 1, '2024-10-29'),
(80, 3, 'Snatch', 2, 1, '2024-10-29'),
(81, 3, 'Snatch Balance', 2, 1, '2024-10-29'),
(82, 3, 'Snatch Deadlift', 2, 1, '2024-10-29'),
(83, 3, 'Snatch Extension', 2, 1, '2024-10-29'),
(84, 3, 'Snatch Pull', 2, 1, '2024-10-29'),
(85, 3, 'Snatch Pull Under', 2, 1, '2024-10-29'),
(86, 3, 'Snatch Push Press', 2, 1, '2024-10-29'),
(87, 3, 'Sotts Press', 2, 1, '2024-10-29'),
(88, 3, 'Split Jerk', 2, 1, '2024-10-29'),
(89, 3, 'Split Snatch', 2, 1, '2024-10-29'),
(90, 3, 'Squat Jerk', 2, 1, '2024-10-29'),
(91, 3, 'Stiff-Legged Deadlift', 2, 1, '2024-10-29'),
(92, 3, 'Sumo Deadlift', 2, 1, '2024-10-29'),
(93, 3, 'Sumo Deadlift High Pull', 2, 1, '2024-10-29'),
(94, 3, 'Thruster', 2, 1, '2024-10-29'),
(95, 3, 'Tire Flip', 2, 1, '2024-10-29'),
(96, 3, 'Turkish Get Up', 2, 1, '2024-10-29'),
(97, 3, 'Wall ball', 2, 1, '2024-10-29'),
(98, 3, 'Weighted Dip', 2, 1, '2024-10-29'),
(99, 3, 'Weighted Pull-up', 2, 1, '2024-10-29'),
(100, 3, 'Weighted Push-up', 2, 1, '2024-10-29'),
(101, 3, 'Yoke Carry', 2, 1, '2024-10-29'),
(102, 3, 'Zercher Squat', 2, 1, '2024-10-29'),
(103, 4, 'Air Squat', 2, 1, '2024-10-29'),
(104, 4, 'Bar Muscle-up', 2, 1, '2024-10-29'),
(105, 4, 'Box Jump', 2, 1, '2024-10-29'),
(106, 4, 'Burpee', 2, 1, '2024-10-29'),
(107, 4, 'Chest-To-Bar Pull-up', 2, 1, '2024-10-29'),
(108, 4, 'Chin-up', 2, 1, '2024-10-29'),
(109, 4, 'GHD back extension', 2, 1, '2024-10-29'),
(110, 4, 'GHD hip extension', 2, 1, '2024-10-29'),
(111, 4, 'GHD sit ups', 2, 1, '2024-10-29'),
(112, 4, 'Handstand Push-up', 2, 1, '2024-10-29'),
(113, 4, 'Handstand Walk', 2, 1, '2024-10-29'),
(114, 4, 'Hollow Rock', 2, 1, '2024-10-29'),
(115, 4, 'Kipping', 2, 1, '2024-10-29'),
(116, 4, 'L-Sit', 2, 1, '2024-10-29'),
(117, 4, 'Muscle-up', 2, 1, '2024-10-29'),
(118, 4, 'Pistol', 2, 1, '2024-10-29'),
(119, 4, 'Plank', 2, 1, '2024-10-29'),
(120, 4, 'Pull-up', 2, 1, '2024-10-29'),
(121, 4, 'Push-up', 2, 1, '2024-10-29'),
(122, 4, 'Ring Dip', 2, 1, '2024-10-29'),
(123, 4, 'Ring Push-up', 2, 1, '2024-10-29'),
(124, 4, 'Ring Row', 2, 1, '2024-10-29'),
(125, 4, 'Rope Climb', 2, 1, '2024-10-29'),
(126, 4, 'Sit-up', 2, 1, '2024-10-29'),
(127, 4, 'Strict Pull-up', 2, 1, '2024-10-29'),
(128, 4, 'Toes-to-Bar', 2, 1, '2024-10-29'),
(129, 5, '1 mile run', 2, 1, '2024-10-29'),
(130, 5, '100 meters run', 2, 1, '2024-10-29'),
(131, 5, '1000 meters row', 2, 1, '2024-10-29'),
(132, 5, '1000 meters run', 2, 1, '2024-10-29'),
(133, 5, '10000 meters run', 2, 1, '2024-10-29'),
(134, 5, '1775', 2, 1, '2024-10-29'),
(135, 5, '18.Zero', 2, 1, '2024-10-29'),
(136, 5, '200 meters run', 2, 1, '2024-10-29'),
(137, 5, '2000 meters row', 2, 1, '2024-10-29'),
(138, 5, '2000 meters run', 2, 1, '2024-10-29'),
(139, 5, '21 Guns', 2, 1, '2024-10-29'),
(140, 5, '30 Burpees for Time', 2, 1, '2024-10-29'),
(141, 5, '30 Muscle-ups', 2, 1, '2024-10-29'),
(142, 5, '300 meters run', 2, 1, '2024-10-29'),
(143, 5, '31 Heroes', 2, 1, '2024-10-29'),
(144, 5, '400 meters run', 2, 1, '2024-10-29'),
(145, 5, '50 Burpees for Time', 2, 1, '2024-10-29'),
(146, 5, '50 meters run', 2, 1, '2024-10-29'),
(147, 5, '500 meters run', 2, 1, '2024-10-29'),
(148, 5, '5000 meters row', 2, 1, '2024-10-29'),
(149, 5, '5000 meters run', 2, 1, '2024-10-29'),
(150, 5, '9/11 Tribute', 2, 1, '2024-10-29'),
(151, 5, 'Abbate', 2, 1, '2024-10-29'),
(152, 5, 'Adam Brown', 2, 1, '2024-10-29'),
(153, 5, 'Adderall', 2, 1, '2024-10-29'),
(154, 5, 'Adrian', 2, 1, '2024-10-29'),
(155, 5, 'Air Force', 2, 1, '2024-10-29'),
(156, 5, 'Alexander', 2, 1, '2024-10-29'),
(157, 5, 'Amanda', 2, 1, '2024-10-29'),
(158, 5, 'Andi', 2, 1, '2024-10-29'),
(159, 5, 'Andy', 2, 1, '2024-10-29'),
(160, 5, 'Angie', 2, 1, '2024-10-29'),
(161, 5, 'Annie', 2, 1, '2024-10-29'),
(162, 5, 'Anthony Raspa', 2, 1, '2024-10-29'),
(163, 5, 'Arnie', 2, 1, '2024-10-29'),
(164, 5, 'B-1', 2, 1, '2024-10-29'),
(165, 5, 'Bad Karma', 2, 1, '2024-10-29'),
(166, 5, 'Badger', 2, 1, '2024-10-29'),
(167, 5, 'Barbara', 2, 1, '2024-10-29'),
(168, 5, 'Barbara Ann', 2, 1, '2024-10-29'),
(169, 5, 'Barraza', 2, 1, '2024-10-29'),
(170, 5, 'Baz', 2, 1, '2024-10-29'),
(171, 5, 'Bear Complex', 2, 1, '2024-10-29'),
(172, 5, 'Beck Soldo', 2, 1, '2024-10-29'),
(173, 5, 'Bert', 2, 1, '2024-10-29'),
(174, 5, 'Betty', 2, 1, '2024-10-29'),
(175, 5, 'Bex', 2, 1, '2024-10-29'),
(176, 5, 'Big Sex', 2, 1, '2024-10-29'),
(177, 5, 'Black and Blue', 2, 1, '2024-10-29'),
(178, 5, 'Blake', 2, 1, '2024-10-29'),
(179, 5, 'Blanchard (Sapper)', 2, 1, '2024-10-29'),
(180, 5, 'Bowen', 2, 1, '2024-10-29'),
(181, 5, 'Bradley', 2, 1, '2024-10-29'),
(182, 5, 'Bradshaw', 2, 1, '2024-10-29'),
(183, 5, 'Brehm', 2, 1, '2024-10-29'),
(184, 5, 'Brenton', 2, 1, '2024-10-29'),
(185, 5, 'Brian', 2, 1, '2024-10-29'),
(186, 5, 'Broad Jump', 2, 1, '2024-10-29'),
(187, 5, 'Broomstick mile', 2, 1, '2024-10-29'),
(188, 5, 'Brownwood Dallas 5', 2, 1, '2024-10-29'),
(189, 5, 'Bulger', 2, 1, '2024-10-29'),
(190, 5, 'Bull', 2, 1, '2024-10-29'),
(191, 5, 'CHAD', 2, 1, '2024-10-29'),
(192, 5, 'Cameron', 2, 1, '2024-10-29'),
(193, 5, 'Candy', 2, 1, '2024-10-29'),
(194, 5, 'Capoot', 2, 1, '2024-10-29'),
(195, 5, 'Carse', 2, 1, '2024-10-29'),
(196, 5, 'Charles Kasper', 2, 1, '2024-10-29'),
(197, 5, 'Charlotte', 2, 1, '2024-10-29'),
(198, 5, 'Chelsea', 2, 1, '2024-10-29'),
(199, 5, 'Christine', 2, 1, '2024-10-29'),
(200, 5, 'Cindy', 2, 1, '2024-10-29'),
(201, 5, 'Cindy XXX', 2, 1, '2024-10-29'),
(202, 5, 'Claudia', 2, 1, '2024-10-29'),
(203, 5, 'Clovis', 2, 1, '2024-10-29'),
(204, 5, 'Coe', 2, 1, '2024-10-29'),
(205, 5, 'Coffey', 2, 1, '2024-10-29'),
(206, 5, 'Coffland', 2, 1, '2024-10-29'),
(207, 5, 'Collin', 2, 1, '2024-10-29'),
(208, 5, 'Complex Fran', 2, 1, '2024-10-29'),
(209, 5, 'Crain', 2, 1, '2024-10-29'),
(210, 5, 'CrossFit Total', 2, 1, '2024-10-29'),
(211, 5, 'CrossFit Total II', 2, 1, '2024-10-29'),
(212, 5, 'Cyrus, Eli, Janie, Liam and Mary', 2, 1, '2024-10-29'),
(213, 5, 'DG', 2, 1, '2024-10-29'),
(214, 5, 'DT', 2, 1, '2024-10-29'),
(215, 5, 'Dae Han', 2, 1, '2024-10-29'),
(216, 5, 'Daniel', 2, 1, '2024-10-29'),
(217, 5, 'Danny', 2, 1, '2024-10-29'),
(218, 5, 'Death by Assault Bike', 2, 1, '2024-10-29'),
(219, 5, 'Del', 2, 1, '2024-10-29'),
(220, 5, 'Dennis McHugh', 2, 1, '2024-10-29'),
(221, 5, 'Desforges', 2, 1, '2024-10-29'),
(222, 5, 'Diane', 2, 1, '2024-10-29'),
(223, 5, 'Dobogai', 2, 1, '2024-10-29'),
(224, 5, 'Donny', 2, 1, '2024-10-29'),
(225, 5, 'Dork', 2, 1, '2024-10-29'),
(226, 5, 'Double DT', 2, 1, '2024-10-29'),
(227, 5, 'Double Grace', 2, 1, '2024-10-29'),
(228, 5, 'Dowell-876', 2, 1, '2024-10-29'),
(229, 5, 'Dragon', 2, 1, '2024-10-29'),
(230, 5, 'Easy Mary', 2, 1, '2024-10-29'),
(231, 5, 'Elizabeth', 2, 1, '2024-10-29'),
(232, 5, 'Erin', 2, 1, '2024-10-29'),
(233, 5, 'Especial Mary', 2, 1, '2024-10-29'),
(234, 5, 'Eva', 2, 1, '2024-10-29'),
(235, 5, 'Falkel', 2, 1, '2024-10-29'),
(236, 5, 'Fat Amy', 2, 1, '2024-10-29'),
(237, 5, 'Feeks', 2, 1, '2024-10-29'),
(238, 5, 'Fight Gone Bad', 2, 1, '2024-10-29'),
(239, 5, 'Filthy Fifty', 2, 1, '2024-10-29'),
(240, 5, 'FitCops Dallas 5', 2, 1, '2024-10-29'),
(241, 5, 'Flight Simulator', 2, 1, '2024-10-29'),
(242, 5, 'Foo', 2, 1, '2024-10-29'),
(243, 5, 'Fractured Fran', 2, 1, '2024-10-29'),
(244, 5, 'Fran', 2, 1, '2024-10-29'),
(245, 5, 'Frank Bonomo', 2, 1, '2024-10-29'),
(246, 5, 'Frantasy Land', 2, 1, '2024-10-29'),
(247, 5, 'Franzilla', 2, 1, '2024-10-29'),
(248, 5, 'Frelen', 2, 1, '2024-10-29'),
(249, 5, 'Gallant', 2, 1, '2024-10-29'),
(250, 5, 'Gas Pedal', 2, 1, '2024-10-29'),
(251, 5, 'Gator', 2, 1, '2024-10-29'),
(252, 5, 'Gaza', 2, 1, '2024-10-29'),
(253, 5, 'Giff', 2, 1, '2024-10-29'),
(254, 5, 'Glen', 2, 1, '2024-10-29'),
(255, 5, 'Godzilla', 2, 1, '2024-10-29'),
(256, 5, 'Grace', 2, 1, '2024-10-29'),
(257, 5, 'Greg Alia', 2, 1, '2024-10-29'),
(258, 5, 'Grettel', 2, 1, '2024-10-29'),
(259, 5, 'Griff', 2, 1, '2024-10-29'),
(260, 5, 'Guido', 2, 1, '2024-10-29'),
(261, 5, 'Gunny', 2, 1, '2024-10-29'),
(262, 5, 'Gwen', 2, 1, '2024-10-29'),
(263, 5, 'Hall', 2, 1, '2024-10-29'),
(264, 5, 'Hamilton', 2, 1, '2024-10-29'),
(265, 5, 'Hammer', 2, 1, '2024-10-29'),
(266, 5, 'Hansen', 2, 1, '2024-10-29'),
(267, 5, 'Hard Cindy', 2, 1, '2024-10-29'),
(268, 5, 'Harper', 2, 1, '2024-10-29'),
(269, 5, 'Harvell', 2, 1, '2024-10-29'),
(270, 5, 'Heather', 2, 1, '2024-10-29'),
(271, 5, 'Heather (Heyer)', 2, 1, '2024-10-29'),
(272, 5, 'Heavy DT', 2, 1, '2024-10-29'),
(273, 5, 'Heavy Helen', 2, 1, '2024-10-29'),
(274, 5, 'Helen', 2, 1, '2024-10-29'),
(275, 5, 'Helton', 2, 1, '2024-10-29'),
(276, 5, 'Hidalgo', 2, 1, '2024-10-29'),
(277, 5, 'High Jump', 2, 1, '2024-10-29'),
(278, 5, 'Holbrook', 2, 1, '2024-10-29'),
(279, 5, 'Holleyman', 2, 1, '2024-10-29'),
(280, 5, 'Hope for kenya', 2, 1, '2024-10-29'),
(281, 5, 'Hortman', 2, 1, '2024-10-29'),
(282, 5, 'Horton', 2, 1, '2024-10-29'),
(283, 5, 'Hotshots 19', 2, 1, '2024-10-29'),
(284, 5, 'I plank, you plank', 2, 1, '2024-10-29'),
(285, 5, 'Ingrid', 2, 1, '2024-10-29'),
(286, 5, 'Irvine', 2, 1, '2024-10-29'),
(287, 5, 'Isabel', 2, 1, '2024-10-29'),
(288, 5, 'JT', 2, 1, '2024-10-29'),
(289, 5, 'Jackie', 2, 1, '2024-10-29'),
(290, 5, 'Jake’s WOD', 2, 1, '2024-10-29'),
(291, 5, 'James Wright', 2, 1, '2024-10-29'),
(292, 5, 'Jenny', 2, 1, '2024-10-29'),
(293, 5, 'Job’s challenge', 2, 1, '2024-10-29'),
(294, 5, 'Jonesworthy', 2, 1, '2024-10-29'),
(295, 5, 'Jordan', 2, 1, '2024-10-29'),
(296, 5, 'Joseph Maffeo', 2, 1, '2024-10-29'),
(297, 5, 'Judah Maccabee', 2, 1, '2024-10-29'),
(298, 5, 'Kalsu', 2, 1, '2024-10-29'),
(299, 5, 'Karabel', 2, 1, '2024-10-29'),
(300, 5, 'Karen', 2, 1, '2024-10-29'),
(312, 5, 'Lyla', 2, 1, '2024-10-29'),
(313, 5, 'Lynne', 2, 1, '2024-10-29'),
(314, 5, 'Maggie', 2, 1, '2024-10-29'),
(315, 5, 'Marathon Monday', 2, 1, '2024-10-29'),
(316, 5, 'Marguerita', 2, 1, '2024-10-29'),
(317, 5, 'Mark 35', 2, 1, '2024-10-29'),
(318, 5, 'Martin', 2, 1, '2024-10-29'),
(319, 5, 'Mary', 2, 1, '2024-10-29'),
(320, 5, 'Mary XXX', 2, 1, '2024-10-29'),
(321, 5, 'McDonald &amp; Galagher', 2, 1, '2024-10-29'),
(322, 5, 'Miagi', 2, 1, '2024-10-29'),
(323, 5, 'Molly Ann', 2, 1, '2024-10-29'),
(324, 5, 'Monti', 2, 1, '2024-10-29'),
(325, 5, 'Moon', 2, 1, '2024-10-29'),
(326, 5, 'Morrison', 2, 1, '2024-10-29'),
(327, 5, 'Moszer', 2, 1, '2024-10-29'),
(328, 5, 'Mr. Joshua', 2, 1, '2024-10-29'),
(329, 5, 'Murph', 2, 1, '2024-10-29'),
(330, 5, 'Nancy', 2, 1, '2024-10-29'),
(331, 5, 'Nasty Girls', 2, 1, '2024-10-29'),
(332, 5, 'Nasty Girls V2', 2, 1, '2024-10-29'),
(333, 5, 'Nate', 2, 1, '2024-10-29'),
(334, 5, 'Naughty Nancy', 2, 1, '2024-10-29'),
(335, 5, 'Nautical Nancy', 2, 1, '2024-10-29'),
(336, 5, 'Nicole', 2, 1, '2024-10-29'),
(337, 5, 'Nukes', 2, 1, '2024-10-29'),
(338, 5, 'Nutts', 2, 1, '2024-10-29'),
(339, 5, 'Oh No Curtis P', 2, 1, '2024-10-29'),
(340, 5, 'Open 11.1', 2, 1, '2024-10-29'),
(341, 5, 'Open 11.2', 2, 1, '2024-10-29'),
(342, 5, 'Open 11.3', 2, 1, '2024-10-29'),
(343, 5, 'Open 11.4', 2, 1, '2024-10-29'),
(344, 5, 'Open 11.5', 2, 1, '2024-10-29'),
(345, 5, 'Open 11.6', 2, 1, '2024-10-29'),
(346, 5, 'Open 12.1', 2, 1, '2024-10-29'),
(347, 5, 'Open 12.2', 2, 1, '2024-10-29'),
(348, 5, 'Open 12.3', 2, 1, '2024-10-29'),
(349, 5, 'Open 12.4', 2, 1, '2024-10-29'),
(350, 5, 'Open 12.5', 2, 1, '2024-10-29'),
(351, 5, 'Open 13.1', 2, 1, '2024-10-29'),
(352, 5, 'Open 13.2', 2, 1, '2024-10-29'),
(353, 5, 'Open 13.3', 2, 1, '2024-10-29'),
(354, 5, 'Open 13.4', 2, 1, '2024-10-29'),
(355, 5, 'Open 13.5', 2, 1, '2024-10-29'),
(356, 5, 'Open 14.1', 2, 1, '2024-10-29'),
(357, 5, 'Open 14.2', 2, 1, '2024-10-29'),
(358, 5, 'Open 14.3', 2, 1, '2024-10-29'),
(359, 5, 'Open 14.4', 2, 1, '2024-10-29'),
(360, 5, 'Open 14.5', 2, 1, '2024-10-29'),
(361, 5, 'Open 15.1', 2, 1, '2024-10-29'),
(362, 5, 'Open 15.1A', 2, 1, '2024-10-29'),
(363, 5, 'Open 15.2', 2, 1, '2024-10-29'),
(364, 5, 'Open 15.3', 2, 1, '2024-10-29'),
(365, 5, 'Open 15.4', 2, 1, '2024-10-29'),
(366, 5, 'Open 15.5', 2, 1, '2024-10-29'),
(367, 5, 'Open 16.1', 2, 1, '2024-10-29'),
(368, 5, 'Open 16.2', 2, 1, '2024-10-29'),
(369, 5, 'Open 16.2 Scaled', 2, 1, '2024-10-29'),
(370, 5, 'Open 16.3', 2, 1, '2024-10-29'),
(371, 5, 'Open 16.3 Scaled', 2, 1, '2024-10-29'),
(372, 5, 'Open 16.4', 2, 1, '2024-10-29'),
(373, 5, 'Open 16.4 Scaled', 2, 1, '2024-10-29'),
(374, 5, 'Open 16.5', 2, 1, '2024-10-29'),
(375, 5, 'Open 16.5 Scaled', 2, 1, '2024-10-29'),
(376, 5, 'Open 17.1', 2, 1, '2024-10-29'),
(377, 5, 'Open 17.2', 2, 1, '2024-10-29'),
(378, 5, 'Open 17.3', 2, 1, '2024-10-29'),
(379, 5, 'Open 17.4', 2, 1, '2024-10-29'),
(380, 5, 'Open 17.5', 2, 1, '2024-10-29'),
(381, 5, 'Open 18.1', 2, 1, '2024-10-29'),
(382, 5, 'Open 18.1 Scaled', 2, 1, '2024-10-29'),
(383, 5, 'Open 18.2', 2, 1, '2024-10-29'),
(384, 5, 'Open 18.2 Scaled', 2, 1, '2024-10-29'),
(385, 5, 'Open 18.2a', 2, 1, '2024-10-29'),
(386, 5, 'Open 18.3', 2, 1, '2024-10-29'),
(387, 5, 'Open 18.3 Scaled', 2, 1, '2024-10-29'),
(388, 5, 'Open 18.4', 2, 1, '2024-10-29'),
(389, 5, 'Open 18.4 Scaled', 2, 1, '2024-10-29'),
(390, 5, 'Open 18.5', 2, 1, '2024-10-29'),
(391, 5, 'Open 18.5 Scaled', 2, 1, '2024-10-29'),
(392, 5, 'Open 19.1', 2, 1, '2024-10-29'),
(393, 5, 'Open 19.1 Scaled', 2, 1, '2024-10-29'),
(394, 5, 'Open 19.2', 2, 1, '2024-10-29'),
(395, 5, 'Open 19.2 Scaled', 2, 1, '2024-10-29'),
(396, 5, 'Open 19.3', 2, 1, '2024-10-29'),
(397, 5, 'Open 19.3 Scaled', 2, 1, '2024-10-29'),
(398, 5, 'Open 19.4', 2, 1, '2024-10-29'),
(399, 5, 'Open 19.4 Scaled', 2, 1, '2024-10-29'),
(400, 5, 'Open 19.5', 2, 1, '2024-10-29'),
(401, 5, 'Open 19.5 Scaled', 2, 1, '2024-10-29'),
(402, 5, 'Open 20.1', 2, 1, '2024-10-29'),
(403, 5, 'Open 20.1 Scaled', 2, 1, '2024-10-29'),
(404, 5, 'Open 20.2', 2, 1, '2024-10-29'),
(405, 5, 'Open 20.2 Scaled', 2, 1, '2024-10-29'),
(406, 5, 'Open 20.3', 2, 1, '2024-10-29'),
(407, 5, 'Open 20.3 Scaled', 2, 1, '2024-10-29'),
(408, 5, 'Open 20.4', 2, 1, '2024-10-29'),
(409, 5, 'Open 20.4 Scaled', 2, 1, '2024-10-29'),
(410, 5, 'Open 20.5', 2, 1, '2024-10-29'),
(411, 5, 'Open 20.5 Scaled', 2, 1, '2024-10-29'),
(412, 5, 'Open 21.1 Equipment Free', 2, 1, '2024-10-29'),
(413, 5, 'Open 21.1 Foundations', 2, 1, '2024-10-29'),
(414, 5, 'Open 21.1 Rx´d', 2, 1, '2024-10-29'),
(415, 5, 'Open 21.1 Scaled', 2, 1, '2024-10-29'),
(416, 5, 'Open 21.2 Equipment Free', 2, 1, '2024-10-29'),
(417, 5, 'Open 21.2 Foundations', 2, 1, '2024-10-29'),
(418, 5, 'Open 21.2 Rx´d', 2, 1, '2024-10-29'),
(419, 5, 'Open 21.2 Scaled', 2, 1, '2024-10-29'),
(420, 5, 'Open 21.3 Equipment Free', 2, 1, '2024-10-29'),
(421, 5, 'Open 21.3 Foundations', 2, 1, '2024-10-29'),
(422, 5, 'Open 21.3 Rx´d', 2, 1, '2024-10-29'),
(423, 5, 'Open 21.3 Scaled', 2, 1, '2024-10-29'),
(424, 5, 'Open 21.4 Equipment Free', 2, 1, '2024-10-29'),
(425, 5, 'Open 21.4 Foundations', 2, 1, '2024-10-29'),
(426, 5, 'Open 21.4 Rx´d', 2, 1, '2024-10-29'),
(427, 5, 'Open 21.4 Scaled', 2, 1, '2024-10-29'),
(428, 5, 'Open 22.1', 2, 1, '2024-10-29'),
(429, 5, 'Open 22.2 for reps', 2, 1, '2024-10-29'),
(430, 5, 'Open 22.2 for time', 2, 1, '2024-10-29'),
(431, 5, 'Open 22.3 for reps', 2, 1, '2024-10-29'),
(432, 5, 'Open 22.3 for time', 2, 1, '2024-10-29'),
(433, 5, 'Open 23.1', 2, 1, '2024-10-29'),
(434, 5, 'Open 23.2 A', 2, 1, '2024-10-29'),
(435, 5, 'Open 23.2 B', 2, 1, '2024-10-29'),
(436, 5, 'Open 23.3', 2, 1, '2024-10-29'),
(437, 5, 'Open 24.1', 2, 1, '2024-10-29'),
(438, 5, 'Open 24.2', 2, 1, '2024-10-29'),
(439, 5, 'Open 24.3', 2, 1, '2024-10-29'),
(440, 5, 'Ozzy', 2, 1, '2024-10-29'),
(441, 5, 'Painstorm X', 2, 1, '2024-10-29'),
(442, 5, 'Painstorm XI', 2, 1, '2024-10-29'),
(443, 5, 'Painstorm XII', 2, 1, '2024-10-29'),
(444, 5, 'Painstorm XIX', 2, 1, '2024-10-29'),
(445, 5, 'Painstorm XV', 2, 1, '2024-10-29'),
(446, 5, 'Painstorm XXI', 2, 1, '2024-10-29'),
(447, 5, 'Painstorm XXIV', 2, 1, '2024-10-29'),
(448, 5, 'Painstorm XXVI', 2, 1, '2024-10-29'),
(449, 5, 'Patton', 2, 1, '2024-10-29'),
(450, 5, 'Paul Keating', 2, 1, '2024-10-29'),
(451, 5, 'Paul Martini', 2, 1, '2024-10-29'),
(452, 5, 'Pidgeon', 2, 1, '2024-10-29'),
(453, 5, 'Ralph', 2, 1, '2024-10-29'),
(454, 5, 'Randy', 2, 1, '2024-10-29'),
(455, 5, 'Regionals 18.1', 2, 1, '2024-10-29'),
(456, 5, 'Regionals 18.2', 2, 1, '2024-10-29'),
(457, 5, 'Regionals 18.3', 2, 1, '2024-10-29'),
(458, 5, 'Regionals 18.4', 2, 1, '2024-10-29'),
(459, 5, 'Regionals 18.5', 2, 1, '2024-10-29'),
(460, 5, 'Rhodri', 2, 1, '2024-10-29'),
(461, 5, 'Rich', 2, 1, '2024-10-29'),
(462, 5, 'Rosa', 2, 1, '2024-10-29'),
(463, 5, 'Run and Get Fran', 2, 1, '2024-10-29'),
(464, 5, 'SSG Atkins', 2, 1, '2024-10-29'),
(465, 5, 'Saman', 2, 1, '2024-10-29'),
(466, 5, 'Saved by the Barbell', 2, 1, '2024-10-29'),
(467, 5, 'Schmalls', 2, 1, '2024-10-29'),
(468, 5, 'Scottie', 2, 1, '2024-10-29'),
(469, 5, 'Sgt. Michael Smith', 2, 1, '2024-10-29'),
(470, 5, 'Slob', 2, 1, '2024-10-29'),
(471, 5, 'Spehar', 2, 1, '2024-10-29'),
(472, 5, 'Sphinx', 2, 1, '2024-10-29'),
(473, 5, 'Spotts', 2, 1, '2024-10-29'),
(474, 5, 'Strung-Out Backwards and Upside-Down Fran', 2, 1, '2024-10-29'),
(475, 5, 'Sugar Daddy', 2, 1, '2024-10-29'),
(476, 5, 'T.J.', 2, 1, '2024-10-29'),
(477, 5, 'T.U.P', 2, 1, '2024-10-29'),
(478, 5, 'Tabata Something Else', 2, 1, '2024-10-29'),
(479, 5, 'Tabata This', 2, 1, '2024-10-29'),
(480, 5, 'Tama', 2, 1, '2024-10-29'),
(481, 5, 'Thacker', 2, 1, '2024-10-29'),
(482, 5, 'The 540', 2, 1, '2024-10-29'),
(483, 5, 'The Chief', 2, 1, '2024-10-29'),
(484, 5, 'The Chief is Dead', 2, 1, '2024-10-29'),
(485, 5, 'The Don', 2, 1, '2024-10-29'),
(486, 5, 'The Fuhrmannator', 2, 1, '2024-10-29'),
(487, 5, 'The Juicy', 2, 1, '2024-10-29'),
(488, 5, 'The Seven', 2, 1, '2024-10-29'),
(489, 5, 'The Triplet', 2, 1, '2024-10-29'),
(490, 5, 'Tommy Mac', 2, 1, '2024-10-29'),
(491, 5, 'Top Gun', 2, 1, '2024-10-29'),
(492, 5, 'Triple 3', 2, 1, '2024-10-29'),
(493, 5, 'Triple Broad Jump', 2, 1, '2024-10-29'),
(494, 5, 'Victoria Martens', 2, 1, '2024-10-29'),
(495, 5, 'Wade', 2, 1, '2024-10-29'),
(496, 5, 'Weaver', 2, 1, '2024-10-29'),
(497, 5, 'Wesley’s Wod', 2, 1, '2024-10-29'),
(498, 5, 'White I', 2, 1, '2024-10-29'),
(499, 5, 'Whitten', 2, 1, '2024-10-29'),
(500, 5, 'Will Lindsay', 2, 1, '2024-10-29'),
(501, 5, 'William Krukowski', 2, 1, '2024-10-29'),
(502, 5, 'WyK', 2, 1, '2024-10-29'),
(503, 5, 'Yeti', 2, 1, '2024-10-29'),
(504, 5, 'Zach (Clouser)', 2, 1, '2024-10-29'),
(505, 5, 'Zimmerman', 2, 1, '2024-10-29');

INSERT INTO `category` (`id`, `name`) VALUES
(1, 'Regular'),
(2, 'Limitado'),
(3, 'Weightlifting'),
(4, 'Gymnastics'),
(5, 'Benchmark Metcon'),
(6, 'Ejercicios varios');

INSERT INTO `class_schedule` (`id`, `class_name`, `assign_staff_mem`, `assistant_staff_member`, `location`, `class_fees`, `days`, `start_time`, `end_time`, `created_by`, `created_date`) VALUES
(1, 'Yoga', 2, 0, 'Gym', 5, '[\"Sunday\",\"Saturday\"]', '8:00', '10:00', 1, '2016-08-22'),
(2, 'Aeróbicos', 2, 0, 'Gym', 5, '[\"Sunday\",\"Friday\",\"Saturday\"]', '17:15', '18:15', 1, '2016-08-22'),
(3, 'HIT', 2, 2, 'Gym', 5, '[\"Sunday\",\"Tuesday\",\"Thursday\"]', '18:30', '19:45', 1, '2016-08-22'),
(4, 'Cardio', 2, 0, 'Gym', 5, '[\"Friday\",\"Saturday\"]', '15:30', '16:30', 1, '2016-08-22'),
(5, 'Pilates', 2, 0, 'Old location', 5, '[\"Sunday\"]', '12:00', '15:15', 1, '2016-08-22'),
(6, 'Zumba', 2, 0, 'Gym', 5, '[\"Saturday\"]', '20:30', '22:30', 1, '2016-08-22'),
(7, 'Funcional', 2, 0, 'Gym', 5, '[\"Monday\",\"Wednesday\",\"Thursday\",\"Friday\",\"Saturday\"]', '9:15', '11:45', 1, '2016-08-22');

INSERT INTO `class_schedule_list` (`id`, `class_id`, `days`, `start_time`, `end_time`) VALUES
(1, 1, '[\"Sunday\",\"Saturday\"]', '8:00', '10:00'),
(2, 2, '[\"Sunday\",\"Friday\",\"Saturday\"]', '17:15', '18:15'),
(3, 3, '[\"Sunday\",\"Tuesday\",\"Thursday\"]', '18:30', '19:45'),
(4, 4, '[\"Friday\",\"Saturday\"]', '15:30', '16:30'),
(5, 5, '[\"Sunday\"]', '12:00', '15:15'),
(6, 6, '[\"Saturday\"]', '20:30', '22:30'),
(7, 7, '[\"Monday\",\"Wednesday\",\"Thursday\",\"Friday\",\"Saturday\"]', '9:15', '11:45');

INSERT INTO `general_setting` (`id`, `name`, `start_year`, `address`, `office_number`, `country`, `email`, `date_format`, `calendar_lang`, `gym_logo`, `cover_image`, `weight`, `height`, `chest`, `waist`, `thing`, `arms`, `fat`, `member_can_view_other`, `staff_can_view_own_member`, `enable_sandbox`, `paypal_email`, `currency`, `enable_alert`, `reminder_days`, `reminder_message`, `enable_message`, `left_header`, `footer`, `system_installed`, `enable_rtl`, `datepicker_lang`, `time_zone`, `system_version`, `sys_language`, `header_color`, `sidemenu_color`, `stripe_secret_key`, `stripe_publishable_key`, `stripe_product_created`, `stripe_product_id`) VALUES
(1, 'GTC Crossfit Management', '2024', 'address', '8899665544', 'py', 'juanfer@lienzo.co', 'm/d/Y', 'es', '1727272880_578719.png', '1727289941_948049.jpg', 'KG', 'Centimetros', 'Inches', 'Inches', 'Inches', 'Inches', 'Percentage', 0, 1, 0, 'your_id@paypal.com', 'PYG', 1, '5', 'Hello GYM_MEMBERNAME,\r\n      Your Membership  GYM_MEMBERSHIP  started at GYM_STARTDATE and it will expire on GYM_ENDDATE.\r\nThank You.', 1, 'Gym Management by lienzo.co', 'Copyright 2022-2024. Todos los derechos reservados.', 1, 0, 'es', 'America/Asuncion', '24', 'es', '#1db198', '#000000', 'YOUR SECRET KEY', 'YOUR PUBLISHABLE KEY', 0, NULL);

INSERT INTO `gym_accessright` (`id`, `controller`, `action`, `menu`, `menu_icon`, `menu_title`, `member`, `staff_member`, `accountant`, `page_link`) VALUES
(1, 'StaffMembers', '', 'staff_member', 'staff-member.png', 'Staff Member', 1, 1, 1, '/staff-members/staff-list'),
(2, 'Membership', '', 'membership', 'membership-type.png', 'Membership Type', 1, 1, 1, '/membership/membership-list'),
(3, 'GymGroup', '', 'group', 'group.png', 'Group', 1, 1, 0, '/gym-group/group-list'),
(4, 'GymMember', '', 'member', 'member.png', 'Member', 1, 1, 1, '/gym-member/member-list'),
(5, 'Activity', '', 'activity', 'activity.png', 'Activity', 1, 1, 0, '/activity/activity-list'),
(6, 'ClassSchedule', '', 'class-schedule', 'class-schedule.png', 'Class Schedule', 1, 1, 0, '/class-schedule/class-list'),
(7, 'ClassBooking', '', 'class-booking', 'class-schedule.png', 'Class Booking', 0, 1, 0, '/class-booking/booking-list'),
(8, 'GymAttendance', '', 'attendance', 'attendance.png', 'Attendance', 0, 1, 1, '/gym-attendance/attendance'),
(9, 'GymAssignWorkout', '', 'assign-workout', 'assigne-workout.png', 'Assigned Workouts', 1, 1, 0, '/gym-assign-workout/workout-log'),
(10, 'GymDailyWorkout', '', 'workouts', 'workout.png', 'Workouts', 1, 1, 0, '/gym-daily-workout/workout-list'),
(11, 'GymAccountant', '', 'accountant', 'accountant.png', 'Accountant', 1, 1, 0, '/gym-accountant/accountant-list'),
(12, 'MembershipPayment', '', 'membership_payment', 'fee.png', 'Fee Payment', 1, 0, 1, '/membership-payment/payment-list'),
(13, 'MembershipPayment', '', 'income', 'payment.png', 'Income', 0, 0, 1, '/membership-payment/income-list'),
(14, 'MembershipPayment', '', 'expense', 'payment.png', 'Expense', 0, 0, 1, '/membership-payment/expense-list'),
(15, 'GymProduct', '', 'product', 'products.png', 'Product', 0, 1, 0, '/gym-product/product-list'),
(16, 'GymStore', '', 'store', 'store.png', 'Store', 0, 1, 0, '/gym-store/sell-record'),
(17, 'GymNewsletter', '', 'news_letter', 'newsletter.png', 'Newsletter', 0, 0, 0, '/gym-newsletter/setting'),
(18, 'GymMessage', '', 'message', 'message.png', 'Message', 1, 1, 0, '/gym-message/compose-message'),
(19, 'GymNotice', '', 'notice', 'notice.png', 'Notice', 1, 1, 1, '/gym-notice/notice-list'),
(20, 'Report', '', 'report', 'report.png', 'Report', 1, 1, 1, '/reports/membership-report'),
(21, 'GymNutrition', '', 'nutrition', 'nutrition-schedule.png', 'Nutrition Schedule', 1, 1, 0, '/gym-nutrition/nutrition-list'),
(22, 'GymReservation', '', 'reservation', 'reservation.png', 'Event', 1, 1, 0, '/gym-reservation/reservation-list'),
(23, 'GymProfile', '', 'account', 'account.png', 'Account', 1, 1, 1, '/GymProfile/view_profile'),
(24, 'GymSubscriptionHistory', '', 'subscription_history', 'subscription_history.png', 'Subscription History', 1, 0, 0, '/GymSubscriptionHistory/');

INSERT INTO `gym_attendance` (`attendance_id`, `user_id`, `class_id`, `attendance_date`, `status`, `attendance_by`, `role_name`) VALUES
(1, 5, 1, '2024-10-23', 'Absent', 1, 'member'),
(2, 5, 3, '2024-10-23', 'Present', 1, 'member'),
(3, 33, 7, '2024-11-13', 'Present', 1, 'member'),
(4, 35, 7, '2024-11-13', 'Present', 1, 'member'),
(5, 36, 7, '2024-11-13', 'Present', 1, 'member'),
(6, 37, 7, '2024-11-13', 'Present', 1, 'member'),
(7, 38, 7, '2024-11-13', 'Present', 1, 'member'),
(8, 34, 7, '2024-11-13', 'Absent', 1, 'member'),
(9, 41, 7, '2024-11-13', 'Absent', 1, 'member'),
(10, 40, 7, '2024-11-13', 'Absent', 1, 'member');

INSERT INTO `gym_branch` (`id`, `name`, `address`, `phone`, `email`, `notes`, `created_date`, `is_active`) VALUES
(1, 'Central Branch', 'Main Street 123', '123456789', 'central@gtccrossfit.com', 'Main branch of GTC Crossfit', '2025-02-07', 1);

INSERT INTO `gym_group` (`id`, `name`, `image`, `created_by`, `created_date`) VALUES
(1, 'Sin asignar', '1729180182_101448.jpg', NULL, '2024-10-17');

INSERT INTO `gym_levels` (`id`, `level`) VALUES
(1, 'Nivel 1'),
(2, 'Nivel 2'),
(3, 'Nivel 3');

INSERT INTO `gym_member` (`id`, `activated`, `role_name`, `member_id`, `token`, `stripe_customer_id`, `is_exist`, `first_name`, `middle_name`, `last_name`, `member_type`, `role`, `branch_id`, `s_specialization`, `gender`, `birth_date`, `assign_class`, `assign_group`, `address`, `city`, `state`, `zipcode`, `mobile`, `phone`, `email`, `weight`, `height`, `chest`, `waist`, `thing`, `arms`, `fat`, `username`, `password`, `image`, `assign_staff_mem`, `intrested_area`, `g_source`, `referrer_by`, `inquiry_date`, `trial_end_date`, `selected_membership`, `membership_status`, `membership_valid_from`, `membership_valid_to`, `first_pay_date`, `created_by`, `created_date`, `alert_sent`, `admin_alert`, `alert_send_date`) VALUES
(1, NULL, 'administrator', NULL, NULL, NULL, 0, 'Admin', '', '', NULL, NULL, NULL, NULL, 'male', '2016-07-01', NULL, NULL, 'null', 'null', 't', '123123', '123123123', '', 'admin@admin.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'admin', '$2y$10$iC3HJKi83n9UsPVUdBsq1uoOT3jPJIk0BwvbZU6dUX8l8YMglUjfO', 'Thumbnail-img2.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2020-06-04', NULL, 0, NULL),
(2, 1, 'staff_member', '', NULL, NULL, 0, 'Angel', '', 'Black', '', 4, NULL, '[\"1\"]', 'male', '2016-08-10', NULL, '', 'Address line', 'City', '', '', '2288774455', '', 'angel.black@mail.com', '', '', '', '', '', '', '', 'sergio', '$2y$10$nUY.i0NfrUX8aS0M5NDlb.w3FPBnW15gqTl274LtLqgTqTASxdDiy', '1727272978_723195.png', 0, 0, 0, 0, NULL, NULL, '', '', NULL, NULL, NULL, 0, '2016-08-22', 0, 0, NULL),
(3, NULL, 'accountant', NULL, NULL, NULL, 0, 'Jose', 'a', 'Aguero', NULL, NULL, NULL, NULL, 'male', '1995-09-01', NULL, NULL, 'Mi direccion', 'Asuncion', NULL, NULL, '981 232164', '', 'joseau93@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'jose', '$2y$10$2coGZpWs4yhitmhyFeW1huGEPqZr0vTFC2qsuNu0pYDLk.EmobKXC', 'Thumbnail-img.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-09-24', NULL, 0, NULL),
(4, NULL, 'accountant', NULL, NULL, NULL, 0, 'Test', '', 'Test', NULL, NULL, NULL, NULL, 'male', '2004-10-08', NULL, NULL, 'Address', 'Asuncion', NULL, NULL, '984584854', '', 'test@lienzo.co', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'testacc', '$2y$10$5fAt0zKLC7.lFibxASr9p.qddY7UY/ezJNSaTrR007TV8UueuuETi', 'Thumbnail-img.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-10-03', NULL, 0, NULL),
(5, 1, 'member', 'M51724', NULL, NULL, 0, 'Marcos', 'Anibal', 'Godoy', 'Member', NULL, NULL, NULL, 'male', '1991-10-15', NULL, '[\"1\"]', 'Avda. Brasilia', 'Asunción', 'Capital', '001204', '984152563', '', 'marcos@lienzo.co', '', '', '', '', '', '', '', 'marcos', '$2y$10$hdeZTF4/WUjVZBQTFf/Dp.PcCEEOGh90zoCzSNUpE7djR9j.huX52', 'Thumbnail-img.png', 2, NULL, NULL, 2, '1969-12-31', '1969-12-31', '2', 'Continue', '2024-11-11', '2025-09-06', '2024-10-17', NULL, '2024-10-17', 0, 0, NULL),
(6, 1, 'member', 'M61124', NULL, NULL, 0, 'Arami', '', 'Cañiza', 'Member', NULL, NULL, NULL, 'female', '1997-11-11', NULL, '[\"1\"]', 'Avda. Brasilia', 'Asunción', '', '001204', '961456789', '', 'aramicaniza1@gmail.com', '', '', '', '', '', '', '', 'aramicaniza', '$2y$10$Uzgs5sWICEMMSU8muSamw.5Q5DIPxbNn6GrpWyme2NwzcD9kIWyG2', 'Thumbnail-img.png', 2, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2', 'Continue', '2024-11-11', '2025-05-09', NULL, NULL, '2024-11-11', 0, 0, NULL),
(7, 1, 'staff_member', NULL, NULL, NULL, 0, 'Ariel', '', 'Quiñonez', NULL, 7, NULL, '[\"1\"]', 'male', '2024-11-06', NULL, NULL, 'Dirección', 'Ciudad', NULL, NULL, '987362333', '', 'ariel.quinhonez@mail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ariel.quinhonez', '$2y$10$jrRqIQgrWGkihALuXCzQNe1K0a3mXdL.2ni0JevEE8hFKu1qPEOxC', 'Thumbnail-img.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-11-12', NULL, 0, NULL),
(8, 1, 'staff_member', NULL, NULL, NULL, 0, 'Belen', '', 'Delgado', NULL, 4, NULL, '[\"1\"]', 'female', '2024-11-07', NULL, NULL, 'Dirección', 'Ciudad', NULL, NULL, '984748565', '', 'belen.delgado@mail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'belen.delgado', '$2y$10$8l7DstIb6E4cPX0RSfKoGOiclEr1wI5DJtZAtSoVro2V3hYxWpMhi', 'Thumbnail-img.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-11-12', NULL, 0, NULL),
(9, 1, 'staff_member', NULL, NULL, NULL, 0, 'Belinda', '', 'Martínez', NULL, 6, NULL, '[\"1\"]', 'female', '2024-11-07', NULL, NULL, 'Dirección', 'Ciudad', NULL, NULL, '984857424', '', 'belinda.martinez@mail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'belinda.martinez', '$2y$10$Wbf3HjPP3GG75gPBKspX5u5vZNy/K4sfDUBbdMwUM6wk/3RWKumTq', 'Thumbnail-img.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-11-12', NULL, 0, NULL),
(10, 1, 'staff_member', NULL, NULL, NULL, 0, 'Charlie', '', 'López', NULL, 5, NULL, '[\"1\"]', 'male', '2024-11-07', NULL, NULL, 'Dirección', 'Ciudad', NULL, NULL, '984857424', '', 'charlie.lopez@mail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'charlie.lopez', '$2y$10$uqFVppQRfEDYkV042C6fy.pCpO9/EFb02Dn/.s.BXaoDWht2iQzX.', 'Thumbnail-img.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-11-12', NULL, 0, NULL),
(11, 1, 'staff_member', NULL, NULL, NULL, 0, 'Edu', '', 'Colina', NULL, 3, NULL, '[\"1\"]', 'male', '2024-11-07', NULL, NULL, 'Dirección', 'Ciudad', NULL, NULL, '984857424', '', 'edu.colina@mail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'edu.colina', '$2y$10$2W1qKH7EAY/2ovXunEu33uQRDVTAaqU/lL0obYnFNy5DXnTyfl7mW', 'Thumbnail-img.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-11-12', NULL, 0, NULL),
(12, 1, 'staff_member', NULL, NULL, NULL, 0, 'Francisco', '', 'Troche', NULL, 2, NULL, '[\"1\"]', 'male', '2024-11-07', NULL, NULL, 'Dirección', 'Ciudad', NULL, NULL, '984857424', '', 'francisto.troche@mail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'francisto.troche', '$2y$10$TArDQIKfJ0.8oa8rgI0fPeg8Oo.MsT8RH6kDn2mp2UrNCSKWXeQLq', 'Thumbnail-img.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-11-12', NULL, 0, NULL),
(13, 1, 'staff_member', NULL, NULL, NULL, 0, 'Gisselle', '', 'Musmeci', NULL, 4, NULL, '[\"1\"]', 'female', '2024-11-07', NULL, NULL, 'Dirección', 'Ciudad', NULL, NULL, '984857424', '', 'gisselle.musmeci@mail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'gisselle.musmeci', '$2y$10$wSoL2eRUVbp0hE0QNKZ/9uuPQ56GxXGTQvV2ngu2GNzH81H5O5VKu', 'Thumbnail-img.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-11-12', NULL, 0, NULL),
(14, 1, 'staff_member', NULL, NULL, NULL, 0, 'Gabi', '', 'Orantes', NULL, 3, NULL, '[\"1\"]', 'female', '2024-11-07', NULL, NULL, 'Dirección', 'Ciudad', NULL, NULL, '984857424', '', 'gabi.orantes@mail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'gabi.orantes', '$2y$10$ZiSOJad2k.5gB9PbiyR8WufnSik1o0hz.lvl5gAEpEdpJEJpe5n0q', 'Thumbnail-img.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-11-12', NULL, 0, NULL),
(15, 1, 'staff_member', NULL, NULL, NULL, 0, 'Guille', '', 'Palacios', NULL, 7, NULL, '[\"1\"]', 'male', '2024-11-07', NULL, NULL, 'Dirección', 'Ciudad', NULL, NULL, '984857424', '', 'guille.palacios@mail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'guille.palacios', '$2y$10$DpX6QFzqDYI.tgYOYZh06O4C6bkwYKdNCWyxJYPbVGs5uV/nV2Gyy', 'Thumbnail-img.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-11-12', NULL, 0, NULL),
(16, 1, 'staff_member', NULL, NULL, NULL, 0, 'Guillermo', '', 'Duarte', NULL, 5, NULL, '[\"1\"]', 'male', '2024-11-07', NULL, NULL, 'Dirección', 'Ciudad', NULL, NULL, '984857424', '', 'guillermo.duarte@mail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'guillermo.duarte', '$2y$10$M3ErwZWLDypg/lNOQ6tFQeCi1By4YYEXgh5gm8nMW7jXJoSTJ.qmm', 'Thumbnail-img.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-11-12', NULL, 0, NULL),
(17, 1, 'staff_member', NULL, NULL, NULL, 0, 'Gustavo', '', 'Paredes', NULL, 3, NULL, '[\"1\"]', 'male', '2024-11-07', NULL, NULL, 'Dirección', 'Ciudad', NULL, NULL, '984857424', '', 'gustavo.paredes@mail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'gustavo.paredes', '$2y$10$rOzBfQTue8uR0SYyqYY7M.xTrCtzWiD5mCQztikys9yv8t/ryQvzq', 'Thumbnail-img.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-11-12', NULL, 0, NULL),
(18, 1, 'staff_member', NULL, NULL, NULL, 0, 'Ivan', '', 'Greco', NULL, 3, NULL, '[\"1\"]', 'male', '2024-11-07', NULL, NULL, 'Dirección', 'Ciudad', NULL, NULL, '984857424', '', 'ivan.greco@mail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ivan.greco', '$2y$10$qecfZfMJTQMOA.7APTlKceXJEVei.npBQJ3Bj8E26tDBVuAdygMMu', 'Thumbnail-img.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-11-12', NULL, 0, NULL),
(19, 1, 'staff_member', NULL, NULL, NULL, 0, 'Jorge', '', 'Volta', NULL, 5, NULL, '[\"1\"]', 'male', '2024-11-07', NULL, NULL, 'Dirección', 'Ciudad', NULL, NULL, '984857424', '', 'jorge.volta@mail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'jorge.volta', '$2y$10$Z58HfFBTR3MyxkKyoO2NV.TSabIW8xlkagVK1aEFosei7lBDib03K', 'Thumbnail-img.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-11-12', NULL, 0, NULL),
(20, 1, 'staff_member', NULL, NULL, NULL, 0, 'Jose', '', 'Aguero', NULL, 7, NULL, '[\"1\"]', 'male', '2024-11-07', NULL, NULL, 'Dirección', 'Ciudad', NULL, NULL, '984857424', '', 'jose.aguero@mail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'jose.aguero', '$2y$10$pm6VSogYoFDTpMHEZrV/f.0z/gsGqT84T8PWD..hd0r2GwvvfYs6e', 'Thumbnail-img.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-11-12', NULL, 0, NULL),
(21, 1, 'staff_member', NULL, NULL, NULL, 0, 'Jose', '', 'González', NULL, 5, NULL, '[\"1\"]', 'male', '2024-11-07', NULL, NULL, 'Dirección', 'Ciudad', NULL, NULL, '984857424', '', 'jose.gonzalez@mail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'jose.gonzalez', '$2y$10$1ReIiguJKnfdTk5De1Bs6ubrz7wNVY0nOCUjWpsHtdHdOWFFt.UKC', 'Thumbnail-img.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-11-12', NULL, 0, NULL),
(22, 1, 'staff_member', NULL, NULL, NULL, 0, 'Luis', '', 'Delgado', NULL, 7, NULL, '[\"1\"]', 'male', '2024-11-07', NULL, NULL, 'Dirección', 'Ciudad', NULL, NULL, '984857424', '', 'luis.delgado@mail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'luis.delgado', '$2y$10$fClQMViQT0WlQgDMtbvYMON46EC1u9YOZf9e62cTIksH8ZiGtefwm', 'Thumbnail-img.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-11-12', NULL, 0, NULL),
(23, 1, 'staff_member', NULL, NULL, NULL, 0, 'Mara', '', 'Román', NULL, 5, NULL, '[\"1\"]', 'female', '2024-11-07', NULL, NULL, 'Dirección', 'Ciudad', NULL, NULL, '984857424', '', 'mara.roman@mail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'mara.roman', '$2y$10$eILl2YkqmyA8Z90uotCHmOS7c6mA833Ejq2j2q/8bGAsCkOtNoqhO', 'Thumbnail-img.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-11-12', NULL, 0, NULL),
(24, 1, 'staff_member', NULL, NULL, NULL, 0, 'Madeleine', '', 'Arguello', NULL, 7, NULL, '[\"1\"]', 'female', '2024-11-07', NULL, NULL, 'Dirección', 'Ciudad', NULL, NULL, '984857424', '', 'madeleine.arguello@mail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'madeleine.arguello', '$2y$10$Om08v2T5aiLbDiOx5t9HBu.ezdToXcmlOdeXCUBbzWj.n3b6DqisC', 'Thumbnail-img.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-11-12', NULL, 0, NULL),
(25, 1, 'staff_member', NULL, NULL, NULL, 0, 'Mario', '', 'Ortiz', NULL, 3, NULL, '[\"1\"]', 'male', '2024-11-07', NULL, NULL, 'Dirección', 'Ciudad', NULL, NULL, '984857424', '', 'mario.ortiz@mail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'mario.ortiz', '$2y$10$M81u5TnKN0vAHtcCLV2rVuxeFQn0k05QYX.W5OyFl8s1gTM.Jke9K', 'Thumbnail-img.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-11-12', NULL, 0, NULL),
(26, 1, 'staff_member', NULL, NULL, NULL, 0, 'Mathias', '', 'Ozuna', NULL, 4, NULL, '[\"1\"]', 'male', '2024-11-07', NULL, NULL, 'Dirección', 'Ciudad', NULL, NULL, '984857424', '', 'mathias.ozuna@mail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'mathias.ozuna', '$2y$10$KmM0EkIUIQf6oEinDQ1lfOX9hjE7DMhzOmhBDLDxUjhLenNnwQ9Lu', 'Thumbnail-img.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-11-12', NULL, 0, NULL),
(27, 1, 'staff_member', NULL, NULL, NULL, 0, 'Paul', '', 'González', NULL, 4, NULL, '[\"1\"]', 'male', '2024-11-07', NULL, NULL, 'Dirección', 'Ciudad', NULL, NULL, '984857424', '', 'paul.gonzalez@mail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'paul.gonzalez', '$2y$10$Ekz0njFT.cL4/XVHR04k9eldvrt8QHKBJ6bG.mcfdwmWIG/bKcH2i', 'Thumbnail-img.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-11-12', NULL, 0, NULL),
(28, 1, 'staff_member', NULL, NULL, NULL, 0, 'Sebas', '', 'Ramírez', NULL, 3, NULL, '[\"1\"]', 'male', '2024-11-07', NULL, NULL, 'Dirección', 'Ciudad', NULL, NULL, '984857424', '', 'sebas.ramirez@mail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'sebas.ramirez', '$2y$10$NjN2KVsBjdyH2M2IJ4/91el/n1sqW8Exw0V92II6bIBCWGRLQX.XS', 'Thumbnail-img.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-11-12', NULL, 0, NULL),
(29, 1, 'staff_member', NULL, NULL, NULL, 0, 'Sol', '', 'Figueredo', NULL, 6, NULL, '[\"1\"]', 'female', '2024-11-07', NULL, NULL, 'Dirección', 'Ciudad', NULL, NULL, '984857424', '', 'sol.figueredo@mail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'sol.figueredo', '$2y$10$KltAh5N4nJqwFzT3ITQLpeb6kGJhNxhaMWFevm4iv2UV0/JMxxj2G', 'Thumbnail-img.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-11-12', NULL, 0, NULL),
(30, 1, 'staff_member', NULL, NULL, NULL, 0, 'Tadeo', '', 'Corbalan', NULL, 6, NULL, '[\"1\"]', 'male', '2024-11-07', NULL, NULL, 'Dirección', 'Ciudad', NULL, NULL, '984857424', '', 'tadeo.corbalan@mail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'tadeo.corbalan', '$2y$10$UEne2m0mPH.YnzZF1/onMuvGCkfWam4blKsKp5dHS2iBFvKtJyDXm', 'Thumbnail-img.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-11-12', NULL, 0, NULL),
(31, 1, 'staff_member', NULL, NULL, NULL, 0, 'Tamara', '', 'Lobo', NULL, 7, NULL, '[\"1\"]', 'female', '2024-11-07', NULL, NULL, 'Dirección', 'Ciudad', NULL, NULL, '984857424', '', 'tamara.lobo@mail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'tamara.lobo', '$2y$10$SiG/VaoQHQsdld1dI3oXP.XNWWSGRTAxrDgEKn2ad843WW8deFWRG', 'Thumbnail-img.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-11-12', NULL, 0, NULL),
(32, 1, 'staff_member', NULL, NULL, NULL, 0, 'Víctor', 'Paulo', 'Came Carvallo', NULL, 3, NULL, '[\"1\"]', 'male', '2024-11-07', NULL, NULL, 'Dirección', 'Ciudad', NULL, NULL, '984857424', '', 'victor.came@mail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'victor.came', '$2y$10$qN4qbLhsbxYEYUxL7y9BoOoJdVbNt5DkYxc0rEoVCL7O5a.bwlCPG', 'Thumbnail-img.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-11-12', NULL, 0, NULL),
(33, 1, 'member', 'M331224', NULL, NULL, 0, 'Abdiel', '', 'Contrera', 'Member', NULL, NULL, NULL, 'male', '2004-11-08', NULL, '\"\"', 'Dirección', 'Ciudad', 'Central', '11457', '984758496', '', 'abdiel.contrera@mail.com', '', '', '', '', '', '', '', 'abdiel.contrera', '$2y$10$bRGzvx1b8gjqAA2VlT20T.3P7lBR6Yn5vtOJ3roEBYWuKHN4ceN82', 'Thumbnail-img.png', 7, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2', 'Continue', '2024-11-12', '2025-05-10', NULL, NULL, '2024-11-12', 0, 0, NULL),
(34, 1, 'member', 'M341224', NULL, NULL, 0, 'Abigail', '', 'González', 'Member', NULL, NULL, NULL, 'female', '2004-11-08', NULL, '\"\"', 'Dirección', 'Ciudad', 'Central', '11457', '984758496', '', 'abigail.gonzalez@mail.com', '', '', '', '', '', '', '', 'abigail.gonzalez', '$2y$10$dUx4wMVvJ62i9XY9gTh5TelJ9SkZcydF6g0lvzc1PqOXZBXr7HHQK', 'Thumbnail-img.png', 13, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2', 'Continue', '2024-11-12', '2025-05-10', NULL, NULL, '2024-11-12', 0, 0, NULL),
(35, 1, 'member', 'M351224', NULL, NULL, 0, 'Adan', '', 'Leiva', 'Member', NULL, NULL, NULL, 'male', '2004-11-08', NULL, '\"\"', 'Dirección', 'Ciudad', 'Central', '11457', '984758496', '', 'adan.leiva@mail.com', '', '', '', '', '', '', '', 'adan.leiva', '$2y$10$WlhHaLcVaoGrcYHp5NYcaeqfQ77RKQv7B2GNxmmdkbfZ8pYNqZXxW', 'Thumbnail-img.png', 27, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2', 'Continue', '2024-11-12', '2025-05-10', NULL, NULL, '2024-11-12', NULL, 0, NULL),
(36, 1, 'member', 'M361224', NULL, NULL, 0, 'Adrian', '', 'Arévalos', 'Member', NULL, NULL, NULL, 'male', '2004-11-08', NULL, '\"\"', 'Dirección', 'Ciudad', 'Central', '11457', '984758496', '', 'adrian.arevalos@mail.com', '', '', '', '', '', '', '', 'adrian.arevalos', '$2y$10$5JJkOmyipfcM8n7gth0sK.hCcyjWk0BFJg1TutRIi7dZ/Vb4jFryu', 'Thumbnail-img.png', 24, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2', 'Continue', '2024-11-12', '2025-05-10', NULL, NULL, '2024-11-12', NULL, 0, NULL),
(37, 1, 'member', 'M371224', NULL, NULL, 0, 'Adrian', '', 'Noguera', 'Member', NULL, NULL, NULL, 'male', '2004-11-08', NULL, '\"\"', 'Dirección', 'Ciudad', 'Central', '11457', '984758496', '', 'adrian.noguera@mail.com', '', '', '', '', '', '', '', 'adrian.noguera', '$2y$10$jptkRVM6.sNNzkUviqlIreaZ3UYm4dMkFlht9G4P7uHZdUE6AWDXy', 'Thumbnail-img.png', 23, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2', 'Continue', '2024-11-12', '2025-05-10', NULL, NULL, '2024-11-12', NULL, 0, NULL),
(38, 1, 'member', 'M381224', NULL, NULL, 0, 'Adriana', '', 'Lombardo', 'Member', NULL, NULL, NULL, 'female', '2004-11-08', NULL, '[\"1\"]', 'Dirección', 'Ciudad', 'Central', '11457', '984758496', '', 'adriana.lombardo@mail.com', '', '', '', '', '', '', '', 'adriana.lombardo', '$2y$10$j3Vuas4xEa5chpoiYvxj4.lx6UeSKBAHzWNwbrKyfcxOVwMxR/OVK', 'Thumbnail-img.png', 12, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2', 'Continue', '2024-11-12', '2025-05-10', NULL, NULL, '2024-11-12', NULL, 0, NULL),
(39, 1, 'member', 'M391224', NULL, NULL, 0, 'Adriana', '', 'Piris', 'Member', NULL, NULL, NULL, 'female', '2004-11-08', NULL, '\"\"', 'Dirección', 'Ciudad', 'Central', '11457', '984758496', '', 'adriana.piris@mail.com', '', '', '', '', '', '', '', 'adriana.piris', '$2y$10$N12HgaV2z4/wfTb03i7QFuksDmoCptCGvz0mA12fkcAlahcH.dyui', 'Thumbnail-img.png', 20, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2', 'Continue', '2024-11-12', '2025-05-10', NULL, NULL, '2024-11-12', 0, 0, NULL),
(40, 1, 'member', 'M401224', NULL, NULL, 0, 'Adriana', '', 'Velazquez', 'Member', NULL, NULL, NULL, 'female', '2004-11-08', NULL, '\"\"', 'Dirección', 'Ciudad', 'Central', '11457', '984758496', '', 'adriana.velazquez@mail.com', '', '', '', '', '', '', '', 'adriana.velazquez', '$2y$10$OygCgC9ATwZnNb.PLmT5OuT11Q/fQIuBHgCstyh7VjFDQUsID.jDy', 'Thumbnail-img.png', 24, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2', 'Continue', '2024-11-12', '2025-05-10', NULL, NULL, '2024-11-12', 0, 0, NULL),
(41, 1, 'member', 'M411224', NULL, NULL, 0, 'Agustina', '', 'Ojeda', 'Member', NULL, NULL, NULL, 'female', '2004-11-08', NULL, '\"\"', 'Dirección', 'Ciudad', 'Central', '11457', '984758496', '', 'agustina.ojeda@mail.com', '', '', '', '', '', '', '', 'agustina.ojeda', '$2y$10$pIeCt0e5.Fh3UPWUb9UomOqKWFJcRWYgLTBhPeXwNZ.ncKaLRdjb6', 'Thumbnail-img.png', 25, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2', 'Continue', '2024-11-12', '2025-05-10', NULL, NULL, '2024-11-12', NULL, 0, NULL),
(42, 1, 'member', 'M421224', NULL, NULL, 0, 'Alan', '', 'Invernizzi', 'Member', NULL, NULL, NULL, 'male', '2004-11-08', NULL, '\"\"', 'Dirección', 'Ciudad', 'Central', '11457', '984758496', '', 'alan.invernizzi@mail.com', '', '', '', '', '', '', '', 'alan.invernizzi', '$2y$10$M3Yl8R5QoUpqS3cHzhhfCu6k.EvfRTdd58NuKig5IEs432QKiYIlC', 'Thumbnail-img.png', 17, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2', 'Continue', '2024-11-12', '2025-05-10', NULL, NULL, '2024-11-12', NULL, 0, NULL),
(43, 1, 'member', 'M431224', NULL, NULL, 0, 'Alberto', '', 'Dávalos', 'Member', NULL, NULL, NULL, 'male', '2004-11-08', NULL, '[\"1\"]', 'Dirección', 'Ciudad', 'Central', '11457', '984758496', '', 'alberto.davalos@mail.com', '', '', '', '', '', '', '', 'alberto.davalos', '$2y$10$V8Rw8Nno2/DRk8JQ0I2.5OebNblwIDLLH/rXvtUrRmM5Nd7h8KLT2', 'Thumbnail-img.png', 27, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2', 'Continue', '2024-11-12', '2025-05-10', NULL, NULL, '2024-11-12', NULL, 0, NULL),
(44, 1, 'member', 'M441224', NULL, NULL, 0, 'Alberto', '', 'López', 'Member', NULL, NULL, NULL, 'male', '2004-11-08', NULL, '[\"1\"]', 'Dirección', 'Ciudad', 'Central', '11457', '984758496', '', 'alberto.lopez@mail.com', '', '', '', '', '', '', '', 'alberto.lopez', '$2y$10$M5bfLLE8KBTn46EEehwgL.Kiw2i9nAv9gAAdSUG9aT/kjsa0J0upm', 'Thumbnail-img.png', 20, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2', 'Continue', '2024-11-12', '2025-05-10', NULL, NULL, '2024-11-12', NULL, 0, NULL),
(45, 1, 'member', 'M451224', NULL, NULL, 0, 'Aldana', '', 'Bonvehi', 'Member', NULL, NULL, NULL, 'female', '2004-11-08', NULL, '\"\"', 'Dirección', 'Ciudad', 'Central', '11457', '984758496', '', 'aldana.bonvehi@mail.com', '', '', '', '', '', '', '', 'aldana.bonvehi', '$2y$10$8v0zo4QT6rI4vxtQZT1CueEXdJ6.XOJJ.NtdDA0LpYnx.to0oPMhC', 'Thumbnail-img.png', 7, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2', 'Continue', '2024-11-12', '2025-05-10', NULL, NULL, '2024-11-12', NULL, 0, NULL),
(46, 1, 'member', 'M461224', NULL, NULL, 0, 'Aldo', '', 'Pusineri', 'Member', NULL, NULL, NULL, 'male', '2004-11-08', NULL, '[\"1\"]', 'Dirección', 'Ciudad', 'Central', '11457', '984758496', '', 'aldo.pusineri@mail.com', '', '', '', '', '', '', '', 'aldo.pusineri', '$2y$10$cVIc8pZHXW6FoLpbCpZAAe2I6n0MkHWcBiFtof/gjILRwPQHeWrYe', 'Thumbnail-img.png', 17, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2', 'Continue', '2024-11-12', '2025-05-10', NULL, NULL, '2024-11-12', NULL, 0, NULL),
(47, 1, 'member', 'M471224', NULL, NULL, 0, 'Aldo', '', 'Rotela', 'Member', NULL, NULL, NULL, 'male', '2004-11-08', NULL, '[\"1\"]', 'Dirección', 'Ciudad', 'Central', '11457', '984758496', '', 'aldo.rotela@mail.com', '', '', '', '', '', '', '', 'aldo.rotela', '$2y$10$zouky14dBlRi1KqXjKqZ5.pesDRTf9PBlhCbEkyw05e2nXbV2BK1S', 'Thumbnail-img.png', 19, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2', 'Continue', '2024-11-12', '2025-05-10', NULL, NULL, '2024-11-12', NULL, 0, NULL),
(48, 1, 'member', 'M481224', NULL, NULL, 0, 'Ale', '', 'Cantero', 'Member', NULL, NULL, NULL, 'female', '2004-11-08', NULL, '\"\"', 'Dirección', 'Ciudad', 'Central', '11457', '984758496', '', 'ale.cantero@mail.com', '', '', '', '', '', '', '', 'ale.cantero', '$2y$10$DpNSllEux454mURn8S3tD.7jNBNquNiW1cZP.Zzlc.yAGuYHPdlbK', 'Thumbnail-img.png', 15, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2', 'Continue', '2024-11-12', '2025-05-10', NULL, NULL, '2024-11-12', NULL, 0, NULL),
(49, 1, 'member', 'M491224', NULL, NULL, 0, 'Ale', '', 'Florentín', 'Member', NULL, NULL, NULL, 'male', '2004-11-08', NULL, '\"\"', 'Dirección', 'Ciudad', 'Central', '11457', '984758496', '', 'ale.florentin@mail.com', '', '', '', '', '', '', '', 'ale.florentin', '$2y$10$bGgIA/SM3e2CPF.HaQIcWeqjXYyLehOhs65viqOQ5manvZ4RYvYEa', 'Thumbnail-img.png', 23, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2', 'Continue', '2024-11-12', '2025-05-10', NULL, NULL, '2024-11-12', NULL, 0, NULL),
(50, 1, 'member', 'M501224', NULL, NULL, 0, 'Alejandro', '', 'García', 'Member', NULL, NULL, NULL, 'male', '2004-11-08', NULL, '\"\"', 'Dirección', 'Ciudad', 'Central', '11457', '984758496', '', 'alejandro.garcia@mail.com', '', '', '', '', '', '', '', 'alejandro.garcia', '$2y$10$bxFGbz/Mz9.J4j/sd3XJl.WAAeWnfgcGp7Up7tjIHkMktIEr7cEhe', 'Thumbnail-img.png', 15, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2', 'Continue', '2024-11-12', '2025-05-10', NULL, NULL, '2024-11-12', NULL, 0, NULL),
(51, 1, 'member', 'M511224', NULL, NULL, 0, 'Alejandro', '', 'Gimenez', 'Member', NULL, NULL, NULL, 'male', '2004-11-08', NULL, '\"\"', 'Dirección', 'Ciudad', 'Central', '11457', '984758496', '', 'alejandro.gimenez@mail.com', '', '', '', '', '', '', '', 'alejandro.gimenez', '$2y$10$jZ7o6swdQ06jPm.xJm5.beEyBwLAHEI25z722ciBW0UeOwvogJALi', 'Thumbnail-img.png', 12, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2', 'Continue', '2024-11-12', '2025-05-10', NULL, NULL, '2024-11-12', NULL, 0, NULL),
(52, 1, 'member', 'M521224', NULL, NULL, 0, 'Alejandro', '', 'González', 'Member', NULL, NULL, NULL, 'male', '2004-11-08', NULL, '\"\"', 'Dirección', 'Ciudad', 'Central', '11457', '984758496', '', 'alejandro.gonzalez@mail.com', '', '', '', '', '', '', '', 'alejandro.gonzalez', '$2y$10$nqDashlXPvJrPyhu1.mgSOb4cdinO88MwVNgi47UGFHNh3LWXl9u.', 'Thumbnail-img.png', 7, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2', 'Continue', '2024-11-12', '2025-05-10', NULL, NULL, '2024-11-12', NULL, 0, NULL),
(53, 1, 'member', 'M531224', NULL, NULL, 0, 'Alex', '', 'Aguayo', 'Member', NULL, NULL, NULL, 'male', '2004-11-08', NULL, '\"\"', 'Dirección', 'Ciudad', 'Central', '11457', '984758496', '', 'alex.aguayo@mail.com', '', '', '', '', '', '', '', 'alex.aguayo', '$2y$10$kaZ/pEsNqhNtYtGTv4ZzGulnE.aufYdQF3XmTTL6BtuwuomiHhu0i', 'Thumbnail-img.png', 20, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2', 'Continue', '2024-11-12', '2025-05-10', NULL, NULL, '2024-11-12', NULL, 0, NULL),
(54, 1, 'member', 'M541224', NULL, NULL, 0, 'Alex', '', 'Ojeda', 'Member', NULL, NULL, NULL, 'male', '2004-11-08', NULL, '\"\"', 'Dirección', 'Ciudad', 'Central', '11457', '984758496', '', 'alex.ojeda@mail.com', '', '', '', '', '', '', '', 'alex.ojeda', '$2y$10$Xl696tFBkoZchKFwMpISOOaF7PxzdqfrYNQvuLu/1e69Pe9C8M2eG', 'Thumbnail-img.png', 10, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2', 'Continue', '2024-11-12', '2025-05-10', NULL, NULL, '2024-11-12', NULL, 0, NULL),
(55, 1, 'member', 'M551224', NULL, NULL, 0, 'Alexandra', '', 'Cabral', 'Member', NULL, NULL, NULL, 'female', '2004-11-08', NULL, '\"\"', 'Dirección', 'Ciudad', 'Central', '11457', '984758496', '', 'alexandra.cabral@mail.com', '', '', '', '', '', '', '', 'alexandra.cabral', '$2y$10$qR6osuv3KgenU2GBAMyB1Or40w.QsSV9e8cQrpXQEll8/O66NTYCa', 'Thumbnail-img.png', 13, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2', 'Continue', '2024-11-11', '2025-05-09', NULL, NULL, '2024-11-12', 0, 0, NULL),
(56, 1, 'member', 'M561224', NULL, NULL, 0, 'Alexandra', '', 'Carreras', 'Member', NULL, NULL, NULL, 'female', '2004-11-08', NULL, '\"\"', 'Dirección', 'Ciudad', 'Central', '11457', '984758496', '', 'alexandra.carreras@mail.com', '', '', '', '', '', '', '', 'alexandra.carreras', '$2y$10$sEfNAFlbLonAbXPYoH2xP.UnGwOx5pH0kyYC.dyie6cB7EAccSnJy', 'Thumbnail-img.png', 13, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2', 'Continue', '2024-11-11', '2025-05-09', NULL, NULL, '2024-11-12', NULL, 0, NULL),
(57, 1, 'member', 'M571224', NULL, NULL, 0, 'Alexis', '', 'Villalba', 'Member', NULL, NULL, NULL, 'male', '2004-11-08', NULL, '\"\"', 'Dirección', 'Ciudad', 'Central', '11457', '984758496', '', 'alexis.villalba@mail.com', '', '', '', '', '', '', '', 'alexis.villalba', '$2y$10$bxqm13r9pltEnXTegxu4Te7xVJ70yGCRRMG2qMqjRQjb8n8qb0kii', 'Thumbnail-img.png', 16, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2', 'Continue', '2024-11-11', '2025-05-09', NULL, NULL, '2024-11-12', NULL, 0, NULL),
(58, 1, 'member', 'M580725', NULL, NULL, 0, 'Marcos', 'Anibal', 'Godoy', 'Member', NULL, NULL, NULL, 'male', '1990-02-23', NULL, '[\"1\"]', 'Enrique Solano López 1024', 'Asunción', 'Capital', '001204', '981406939', '', 'marcosanibalgg@gmail.com', '', '', '', '', '', '', '', 'mgodoy', '$2y$10$.Z.I/qA9OIMWuWNlRCNTF.p8t8HIA52sK3PxHraabRwDLMw7wTkjW', '1738919306_631181.jpg', 13, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '3', 'Continue', '2025-02-07', '2025-08-05', NULL, NULL, '2025-02-07', NULL, 0, NULL);

INSERT INTO `gym_member_class` (`id`, `member_id`, `assign_class`) VALUES
(17, 35, 4),
(18, 35, 6),
(19, 35, 7),
(20, 36, 4),
(21, 36, 6),
(22, 36, 7),
(23, 37, 4),
(24, 37, 6),
(25, 37, 7),
(26, 38, 4),
(27, 38, 6),
(28, 38, 7),
(29, 34, 4),
(30, 34, 6),
(31, 34, 7),
(38, 41, 4),
(39, 41, 6),
(40, 41, 7),
(41, 40, 4),
(42, 40, 6),
(43, 40, 7),
(44, 39, 4),
(45, 39, 6),
(46, 39, 7),
(47, 42, 4),
(48, 42, 6),
(49, 42, 7),
(50, 43, 4),
(51, 43, 6),
(52, 43, 7),
(53, 44, 4),
(54, 44, 6),
(55, 44, 7),
(56, 45, 4),
(57, 45, 6),
(58, 45, 7),
(59, 46, 4),
(60, 46, 6),
(61, 46, 7),
(62, 47, 4),
(63, 47, 6),
(64, 47, 7),
(65, 48, 4),
(66, 48, 6),
(67, 48, 7),
(68, 49, 4),
(69, 49, 6),
(70, 49, 7),
(71, 50, 4),
(72, 50, 6),
(73, 50, 7),
(74, 51, 4),
(75, 51, 6),
(76, 51, 7),
(77, 52, 4),
(78, 52, 6),
(79, 52, 7),
(80, 53, 4),
(81, 53, 6),
(82, 53, 7),
(83, 54, 4),
(84, 54, 6),
(85, 54, 7),
(89, 56, 4),
(90, 56, 6),
(91, 56, 7),
(92, 55, 4),
(93, 55, 6),
(94, 55, 7),
(95, 57, 4),
(96, 57, 6),
(97, 57, 7),
(98, 5, 1),
(99, 5, 2),
(100, 5, 3),
(101, 5, 4),
(102, 5, 5),
(103, 6, 4),
(104, 6, 6),
(105, 6, 7),
(121, 58, 4),
(122, 58, 6),
(123, 58, 7),
(124, 33, 1),
(125, 33, 2),
(126, 33, 3),
(127, 33, 4),
(128, 33, 5);

INSERT INTO `gym_roles` (`id`, `name`) VALUES
(2, 'Benchmark Metcon'),
(3, 'Custom Metcon'),
(4, 'Gymnastics'),
(5, 'Skill'),
(6, 'Warmup'),
(7, 'Weightlifting');

INSERT INTO `installment_plan` (`id`, `number`, `duration`) VALUES
(1, 1, 'Month'),
(2, 1, 'Week'),
(3, 1, 'Year');

INSERT INTO `membership` (`id`, `membership_label`, `membership_cat_id`, `membership_length`, `membership_class_limit`, `limit_days`, `limitation`, `install_plan_id`, `membership_amount`, `membership_class`, `installment_amount`, `signup_fee`, `gmgt_membershipimage`, `created_date`, `created_by_id`, `membership_description`, `activated_on_stripe`, `stripe_plan_id`) VALUES
(1, 'Membresia Platino', 1, 360, 'Unlimited', NULL, NULL, 1, 200000, '[\"1\",\"2\",\"3\",\"4\",\"5\",\"6\",\"7\"]', 42, 50000, '1727274469_614222.png', '2016-08-22', 1, '<p>Membresia Platino<br></p>', 0, NULL),
(2, 'Membresia Gold', 1, 300, 'Unlimited', NULL, NULL, 1, 250000, '[\"1\",\"2\",\"3\",\"4\",\"5\"]', 37, 50000, '1727274336_723119.png', '2016-08-22', 1, '<p>Membresía Gold<br></p>', 0, NULL),
(3, 'Membresia Plata', 2, 180, 'Limited', 0, 'per_week', 2, 150000, '[\"4\",\"6\",\"7\"]', 5, 50000, '1727274479_376769.png', '2016-08-22', 1, '<p>Membresia Plata<br></p>', 0, NULL);

INSERT INTO `membership_activity` (`id`, `activity_id`, `membership_id`, `created_by`, `created_date`) VALUES
(1, 1, 2, NULL, '2024-10-09'),
(2, 2, 2, NULL, '2024-10-09'),
(3, 3, 2, NULL, '2024-10-09'),
(4, 4, 2, NULL, '2024-10-09'),
(5, 5, 2, NULL, '2024-10-09'),
(6, 6, 2, NULL, '2024-10-09'),
(7, 7, 2, NULL, '2024-10-09'),
(8, 8, 2, NULL, '2024-10-09'),
(9, 9, 2, NULL, '2024-10-09'),
(10, 10, 2, NULL, '2024-10-09'),
(11, 11, 2, NULL, '2024-10-09');

INSERT INTO `membership_history` (`id`, `member_id`, `selected_membership`, `assign_staff_mem`, `intrested_area`, `g_source`, `referrer_by`, `inquiry_date`, `trial_end_date`, `membership_valid_from`, `membership_valid_to`, `first_pay_date`, `created_date`) VALUES
(1, 5, 1, 2, NULL, NULL, 2, '1969-12-31', '1969-12-31', '2024-10-17', '2025-10-11', '2024-10-17', '2024-10-17'),
(2, 5, 2, NULL, NULL, NULL, NULL, NULL, NULL, '2024-11-11', '2025-09-06', NULL, '2024-11-11'),
(3, 6, 3, 2, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2024-11-11', '2025-05-09', NULL, '2024-11-11'),
(4, 33, 3, 7, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2024-11-12', '2025-05-10', NULL, '2024-11-12'),
(5, 34, 3, 13, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2024-11-12', '2025-05-10', NULL, '2024-11-12'),
(6, 35, 3, 27, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2024-11-12', '2025-05-10', NULL, '2024-11-12'),
(7, 36, 3, 24, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2024-11-12', '2025-05-10', NULL, '2024-11-12'),
(8, 37, 3, 23, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2024-11-12', '2025-05-10', NULL, '2024-11-12'),
(9, 38, 3, 12, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2024-11-12', '2025-05-10', NULL, '2024-11-12'),
(10, 39, 3, 20, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2024-11-12', '2025-05-10', NULL, '2024-11-12'),
(11, 40, 3, 24, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2024-11-12', '2025-05-10', NULL, '2024-11-12'),
(12, 41, 3, 25, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2024-11-12', '2025-05-10', NULL, '2024-11-12'),
(13, 42, 3, 17, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2024-11-12', '2025-05-10', NULL, '2024-11-12'),
(14, 43, 3, 27, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2024-11-12', '2025-05-10', NULL, '2024-11-12'),
(15, 44, 3, 20, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2024-11-12', '2025-05-10', NULL, '2024-11-12'),
(16, 45, 3, 7, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2024-11-12', '2025-05-10', NULL, '2024-11-12'),
(17, 46, 3, 17, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2024-11-12', '2025-05-10', NULL, '2024-11-12'),
(18, 47, 3, 19, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2024-11-12', '2025-05-10', NULL, '2024-11-12'),
(19, 48, 3, 15, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2024-11-12', '2025-05-10', NULL, '2024-11-12'),
(20, 49, 3, 23, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2024-11-12', '2025-05-10', NULL, '2024-11-12'),
(21, 50, 3, 15, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2024-11-12', '2025-05-10', NULL, '2024-11-12'),
(22, 51, 3, 12, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2024-11-12', '2025-05-10', NULL, '2024-11-12'),
(23, 52, 3, 7, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2024-11-12', '2025-05-10', NULL, '2024-11-12'),
(24, 53, 3, 20, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2024-11-12', '2025-05-10', NULL, '2024-11-12'),
(25, 54, 3, 10, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2024-11-12', '2025-05-10', NULL, '2024-11-12'),
(26, 55, 3, 22, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2024-11-11', '2025-05-09', NULL, '2024-11-12'),
(27, 56, 3, 13, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2024-11-11', '2025-05-09', NULL, '2024-11-12'),
(28, 57, 3, 16, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2024-11-11', '2025-05-09', NULL, '2024-11-12'),
(29, 58, 3, 13, NULL, NULL, NULL, '1969-12-31', '1969-12-31', '2025-02-07', '2025-08-05', NULL, '2025-02-07');

INSERT INTO `membership_payment` (`mp_id`, `member_id`, `membership_id`, `membership_amount`, `paid_amount`, `start_date`, `end_date`, `membership_status`, `payment_status`, `created_date`, `created_by`) VALUES
(1, 5, 1, 200000, 0, '2024-10-17', '2025-10-11', 'Continue', '0', '2024-10-17', 1),
(2, 5, 2, 250000, 0, '2024-11-11', '2025-09-06', 'Continue', '0', '2024-11-11', NULL),
(3, 6, 3, 150000, 0, '2024-11-11', '2025-05-09', 'Continue', '0', '2024-11-11', 1),
(4, 33, 3, 150000, 0, '2024-11-12', '2025-05-10', 'Continue', '0', '2024-11-12', 1),
(5, 34, 3, 150000, 0, '2024-11-12', '2025-05-10', 'Continue', '0', '2024-11-12', 1),
(6, 35, 3, 150000, 0, '2024-11-12', '2025-05-10', 'Continue', '0', '2024-11-12', 1),
(7, 36, 3, 150000, 0, '2024-11-12', '2025-05-10', 'Continue', '0', '2024-11-12', 1),
(8, 37, 3, 150000, 0, '2024-11-12', '2025-05-10', 'Continue', '0', '2024-11-12', 1),
(9, 38, 3, 150000, 0, '2024-11-12', '2025-05-10', 'Continue', '0', '2024-11-12', 1),
(10, 39, 3, 150000, 0, '2024-11-12', '2025-05-10', 'Continue', '0', '2024-11-12', 1),
(11, 40, 3, 150000, 0, '2024-11-12', '2025-05-10', 'Continue', '0', '2024-11-12', 1),
(12, 41, 3, 150000, 0, '2024-11-12', '2025-05-10', 'Continue', '0', '2024-11-12', 1),
(13, 42, 3, 150000, 0, '2024-11-12', '2025-05-10', 'Continue', '0', '2024-11-12', 1),
(14, 43, 3, 150000, 0, '2024-11-12', '2025-05-10', 'Continue', '0', '2024-11-12', 1),
(15, 44, 3, 150000, 0, '2024-11-12', '2025-05-10', 'Continue', '0', '2024-11-12', 1),
(16, 45, 3, 150000, 0, '2024-11-12', '2025-05-10', 'Continue', '0', '2024-11-12', 1),
(17, 46, 3, 150000, 0, '2024-11-12', '2025-05-10', 'Continue', '0', '2024-11-12', 1),
(18, 47, 3, 150000, 0, '2024-11-12', '2025-05-10', 'Continue', '0', '2024-11-12', 1),
(19, 48, 3, 150000, 0, '2024-11-12', '2025-05-10', 'Continue', '0', '2024-11-12', 1),
(20, 49, 3, 150000, 0, '2024-11-12', '2025-05-10', 'Continue', '0', '2024-11-12', 1),
(21, 50, 3, 150000, 0, '2024-11-12', '2025-05-10', 'Continue', '0', '2024-11-12', 1),
(22, 51, 3, 150000, 0, '2024-11-12', '2025-05-10', 'Continue', '0', '2024-11-12', 1),
(23, 52, 3, 150000, 0, '2024-11-12', '2025-05-10', 'Continue', '0', '2024-11-12', 1),
(24, 53, 3, 150000, 0, '2024-11-12', '2025-05-10', 'Continue', '0', '2024-11-12', 1),
(25, 54, 3, 150000, 0, '2024-11-12', '2025-05-10', 'Continue', '0', '2024-11-12', 1),
(26, 55, 3, 150000, 0, '2024-11-11', '2025-05-09', 'Continue', '0', '2024-11-12', 1),
(27, 56, 3, 150000, 0, '2024-11-11', '2025-05-09', 'Continue', '0', '2024-11-12', 1),
(28, 57, 3, 150000, 0, '2024-11-11', '2025-05-09', 'Continue', '0', '2024-11-12', 1),
(29, 58, 3, 150000, 0, '2025-02-07', '2025-08-05', 'Continue', '0', '2025-02-07', 1);

INSERT INTO `membership_payment_history` (`payment_history_id`, `mp_id`, `amount`, `payment_method`, `paid_by_date`, `created_by`, `trasaction_id`) VALUES
(1, 1, 200000, 'Cash', '2024-10-17', 1, '123456789'),
(2, 2, 250000, 'Cash', '2024-11-11', 1, '987654321'),
(3, 3, 150000, 'Cash', '2024-11-11', 1, '123456789'),
(4, 4, 150000, 'Cash', '2024-11-12', 1, '987654321'),
(5, 5, 150000, 'Cash', '2024-11-12', 1, '123456789'),
(6, 6, 150000, 'Cash', '2024-11-12', 1, '987654321'),
(7, 7, 150000, 'Cash', '2024-11-12', 1, '123456789'),
(8, 8, 150000, 'Cash', '2024-11-12', 1, '987654321'),
(9, 9, 150000, 'Cash', '2024-11-12', 1, '123456789'),
(10, 10, 150000, 'Cash', '2024-11-12', 1, '987654321'),
(11, 11, 150000, 'Cash', '2024-11-12', 1, '123456789'),
(12, 12, 150000, 'Cash', '2024-11-12', 1, '987654321'),
(13, 13, 150000, 'Cash', '2024-11-12', 1, '123456789'),
(14, 14, 150000, 'Cash', '2024-11-12', 1, '987654321'),
(15, 15, 150000, 'Cash', '2024-11-12', 1, '123456789'),
(16, 16, 150000, 'Cash', '2024-11-12', 1, '987654321'),
(17, 17, 150000, 'Cash', '2024-11-12', 1, '123456789'),
(18, 18, 150000, 'Cash', '2024-11-12', 1, '987654321'),
(19, 19, 150000, 'Cash', '2024-11-12', 1, '123456789'),
(20, 20, 150000, 'Cash', '2024-11-12', 1, '987654321'),
(21, 21, 150000, 'Cash', '2024-11-12', 1, '123456789'),
(22, 22, 150000, 'Cash', '2024-11-12', 1, '987654321'),
(23, 23, 150000, 'Cash', '2024-11-12', 1, '123456789'),
(24, 24, 150000, 'Cash', '2024-11-12', 1, '987654321'),
(25, 25, 150000, 'Cash', '2024-11-12', 1, '123456789'),
(26, 26, 150000, 'Cash', '2024-11-11', 1, '987654321'),
(27, 27, 150000, 'Cash', '2024-11-11', 1, '123456789'),
(28, 28, 150000, 'Cash', '2024-11-11', 1, '987654321');

INSERT INTO `specialization` (`id`, `name`) VALUES
(1, 'Training');

INSERT INTO `staff_branches` (`id`, `staff_id`, `branch_id`, `created`) VALUES
(1, 2, 1, '2025-02-07 08:04:03'),
(2, 7, 1, '2025-02-07 08:04:14');



/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;