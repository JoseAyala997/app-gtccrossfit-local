ALTER TABLE membership_payment_history ADD COLUMN is_maintenance TINYINT(1) DEFAULT 0;
 ALTER TABLE membership_history ADD COLUMN is_maintenance TINYINT(1) DEFAULT 0;
  ALTER TABLE membership_payment ADD COLUMN is_maintenance TINYINT(1) DEFAULT 0;

