-- Add member_id to class_booking table
ALTER TABLE `class_booking`
ADD COLUMN `member_id` int DEFAULT NULL AFTER `booking_id`,
ADD KEY `fk_class_booking_member_id` (`member_id`),
ADD CONSTRAINT `fk_class_booking_member_id` FOREIGN KEY (`member_id`) REFERENCES `gym_member` (`id`) ON DELETE SET NULL;

-- Try to match existing bookings with members based on email
UPDATE `class_booking` cb
INNER JOIN `gym_member` gm ON cb.email = gm.email
SET cb.member_id = gm.id
WHERE cb.member_id IS NULL;
