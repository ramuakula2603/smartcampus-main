-- ============================================================================
-- Time Range Assignment System for Biometric Attendance
-- ============================================================================
-- This script creates tables to assign specific time ranges to staff and students
-- allowing administrators to control which time ranges each person can use
-- ============================================================================

-- ============================================================================
-- Table: staff_time_range_assignments
-- Purpose: Links staff members to specific time ranges they are authorized to use
-- ============================================================================
CREATE TABLE IF NOT EXISTS `staff_time_range_assignments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `staff_id` int(11) NOT NULL COMMENT 'Foreign key to staff table',
  `time_range_id` int(11) NOT NULL COMMENT 'Foreign key to biometric_timing_setup table',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=Active, 0=Inactive',
  `created_by` int(11) DEFAULT NULL COMMENT 'Admin user who created this assignment',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_staff_range` (`staff_id`, `time_range_id`),
  KEY `idx_staff_id` (`staff_id`),
  KEY `idx_time_range_id` (`time_range_id`),
  KEY `idx_is_active` (`is_active`),
  CONSTRAINT `fk_staff_time_range_staff` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_staff_time_range_setup` FOREIGN KEY (`time_range_id`) REFERENCES `biometric_timing_setup` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Assigns specific time ranges to staff members';

-- ============================================================================
-- Table: student_time_range_assignments
-- Purpose: Links students to specific time ranges they are authorized to use
-- ============================================================================
CREATE TABLE IF NOT EXISTS `student_time_range_assignments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_session_id` int(11) NOT NULL COMMENT 'Foreign key to student_session table',
  `time_range_id` int(11) NOT NULL COMMENT 'Foreign key to biometric_timing_setup table',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=Active, 0=Inactive',
  `created_by` int(11) DEFAULT NULL COMMENT 'Admin user who created this assignment',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_student_range` (`student_session_id`, `time_range_id`),
  KEY `idx_student_session_id` (`student_session_id`),
  KEY `idx_time_range_id` (`time_range_id`),
  KEY `idx_is_active` (`is_active`),
  CONSTRAINT `fk_student_time_range_student` FOREIGN KEY (`student_session_id`) REFERENCES `student_session` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_student_time_range_setup` FOREIGN KEY (`time_range_id`) REFERENCES `biometric_timing_setup` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Assigns specific time ranges to students';

-- ============================================================================
-- Add new column to staff_attendance for unauthorized punches
-- ============================================================================
ALTER TABLE `staff_attendance` 
ADD COLUMN `is_authorized_range` tinyint(1) DEFAULT 1 COMMENT '1=Authorized, 0=Unauthorized time range' AFTER `biometric_attendence`;

-- ============================================================================
-- Add new column to student_attendences for unauthorized punches
-- ============================================================================
ALTER TABLE `student_attendences` 
ADD COLUMN `is_authorized_range` tinyint(1) DEFAULT 1 COMMENT '1=Authorized, 0=Unauthorized time range' AFTER `biometric_attendence`;

-- ============================================================================
-- Insert menu permission for Time Range Assignments
-- ============================================================================
INSERT INTO `permission_category` (`id`, `perm_group_id`, `name`, `short_code`, `enable_view`, `enable_add`, `enable_edit`, `enable_delete`, `created_at`) 
VALUES (NULL, 1, 'Time Range Assignments', 'time_range_assignments', 1, 1, 1, 1, NOW())
ON DUPLICATE KEY UPDATE `name` = 'Time Range Assignments';

-- Get the permission_category id
SET @perm_cat_id = (SELECT id FROM permission_category WHERE short_code = 'time_range_assignments' LIMIT 1);

-- Insert permissions for all roles (adjust role IDs as needed)
-- Role 1 = Super Admin, Role 2 = Admin, etc.
INSERT INTO `roles_permissions` (`role_id`, `perm_cat_id`, `can_view`, `can_add`, `can_edit`, `can_delete`, `created_at`)
SELECT r.id, @perm_cat_id, 1, 1, 1, 1, NOW()
FROM `roles` r
WHERE r.id IN (1, 2, 7) -- Super Admin, Admin, and other admin roles
ON DUPLICATE KEY UPDATE `can_view` = 1, `can_add` = 1, `can_edit` = 1, `can_delete` = 1;

-- ============================================================================
-- Sample Data (Optional - for testing)
-- ============================================================================
-- Uncomment the following lines to insert sample assignments

-- Example: Assign all time ranges to staff_id 1
-- INSERT INTO `staff_time_range_assignments` (`staff_id`, `time_range_id`, `is_active`, `created_at`)
-- SELECT 1, id, 1, NOW()
-- FROM `biometric_timing_setup`
-- WHERE `is_active` = 1;

-- Example: Assign morning check-in to student_session_id 1
-- INSERT INTO `student_time_range_assignments` (`student_session_id`, `time_range_id`, `is_active`, `created_at`)
-- SELECT 1, id, 1, NOW()
-- FROM `biometric_timing_setup`
-- WHERE `range_name` LIKE '%Morning%' AND `is_active` = 1;

-- ============================================================================
-- Verification Queries
-- ============================================================================
-- Run these queries to verify the tables were created successfully

-- Check staff_time_range_assignments table
-- SELECT COUNT(*) as staff_assignments FROM staff_time_range_assignments;

-- Check student_time_range_assignments table
-- SELECT COUNT(*) as student_assignments FROM student_time_range_assignments;

-- Check if columns were added
-- DESCRIBE staff_attendance;
-- DESCRIBE student_attendences;

-- Check permissions
-- SELECT * FROM permission_category WHERE short_code = 'time_range_assignments';
-- SELECT * FROM roles_permissions WHERE perm_cat_id = @perm_cat_id;

-- ============================================================================
-- Rollback Script (Use with caution!)
-- ============================================================================
-- Uncomment the following lines to remove all changes

-- DROP TABLE IF EXISTS `student_time_range_assignments`;
-- DROP TABLE IF EXISTS `staff_time_range_assignments`;
-- ALTER TABLE `staff_attendance` DROP COLUMN IF EXISTS `is_authorized_range`;
-- ALTER TABLE `student_attendences` DROP COLUMN IF EXISTS `is_authorized_range`;
-- DELETE FROM `roles_permissions` WHERE `perm_cat_id` = (SELECT id FROM permission_category WHERE short_code = 'time_range_assignments');
-- DELETE FROM `permission_category` WHERE `short_code` = 'time_range_assignments';

-- ============================================================================
-- End of Script
-- ============================================================================

