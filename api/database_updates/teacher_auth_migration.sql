-- Teacher Authentication System Database Updates
-- Add app_key field to staff table for mobile device token management

-- Add app_key column to staff table if it doesn't exist
ALTER TABLE `staff` ADD COLUMN `app_key` VARCHAR(255) NULL DEFAULT NULL AFTER `disable_at`;

-- Add index for app_key for better performance
ALTER TABLE `staff` ADD INDEX `idx_staff_app_key` (`app_key`);

-- Update users_authentication table to ensure staff_id field exists and has proper constraints
-- (This field already exists in the schema, but adding for completeness)
-- ALTER TABLE `users_authentication` ADD COLUMN `staff_id` INT(11) NULL DEFAULT NULL AFTER `token`;

-- Add index for staff_id in users_authentication table for better performance
ALTER TABLE `users_authentication` ADD INDEX `idx_users_auth_staff_id` (`staff_id`);

-- Add foreign key constraint for staff_id (optional, for data integrity)
-- ALTER TABLE `users_authentication` ADD CONSTRAINT `fk_users_auth_staff` 
-- FOREIGN KEY (`staff_id`) REFERENCES `staff`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Create a view for teacher authentication (optional, for easier queries)
CREATE OR REPLACE VIEW `teacher_auth_view` AS
SELECT 
    s.id as staff_id,
    s.employee_id,
    s.name,
    s.surname,
    s.email,
    s.contact_no,
    s.designation,
    s.department,
    s.is_active,
    s.app_key,
    ua.token,
    ua.expired_at,
    sd.designation as designation_name,
    d.department_name
FROM staff s
LEFT JOIN users_authentication ua ON s.id = ua.staff_id
LEFT JOIN staff_designation sd ON s.designation = sd.id
LEFT JOIN department d ON s.department = d.id
WHERE s.is_active = 1;
