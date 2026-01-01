-- ============================================================================
-- BIOMETRIC ATTENDANCE SYSTEM - COMPLETE DATABASE SETUP
-- ============================================================================
-- This script ensures all required tables exist for the biometric attendance
-- system supporting both staff and student attendance tracking.
-- 
-- Features:
-- - ZKTeco device communication
-- - Staff attendance with multiple punches per day
-- - Student attendance with multiple punches per day
-- - Device logging and raw data storage
-- - Advanced timing rules with grace periods
-- - Time range assignments for authorization
-- ============================================================================

-- ============================================================================
-- 1. BIOMETRIC DEVICES TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS `biometric_devices` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sn` varchar(64) NOT NULL COMMENT 'Device Serial Number (unique identifier)',
  `name` varchar(128) DEFAULT NULL COMMENT 'Device name/label for identification',
  `timezone` varchar(64) DEFAULT NULL COMMENT 'Device timezone (e.g., Asia/Kolkata)',
  `ip` varchar(64) DEFAULT NULL COMMENT 'Device IP address',
  `is_allowed` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Allow/disallow device (1=allowed, 0=blocked)',
  `note` text DEFAULT NULL COMMENT 'Additional notes about the device',
  `created_at` datetime DEFAULT current_timestamp() COMMENT 'Record creation timestamp',
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Record last update timestamp',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_sn` (`sn`),
  KEY `idx_is_allowed` (`is_allowed`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cloud-based biometric device management and allowlisting';

-- ============================================================================
-- 2. BIOMETRIC DEVICE LOGS TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS `biometric_device_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `request_method` varchar(10) DEFAULT NULL COMMENT 'GET or POST',
  `request_uri` varchar(255) DEFAULT NULL COMMENT 'Request URI',
  `query_string` text DEFAULT NULL COMMENT 'URL query parameters as JSON',
  `raw_body` text DEFAULT NULL COMMENT 'Raw POST body data',
  `parsed_data` text DEFAULT NULL COMMENT 'Parsed data as JSON',
  `device_sn` varchar(100) DEFAULT NULL COMMENT 'Device serial number',
  `ip_address` varchar(50) DEFAULT NULL COMMENT 'Device IP address',
  `user_agent` varchar(255) DEFAULT NULL COMMENT 'Device user agent',
  `processing_status` enum('pending','success','error') DEFAULT 'pending' COMMENT 'Processing status',
  `error_message` text DEFAULT NULL COMMENT 'Error message if processing failed',
  `records_processed` int(11) DEFAULT 0 COMMENT 'Number of attendance records processed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_device_sn` (`device_sn`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_processing_status` (`processing_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci COMMENT='Biometric device communication logs';

-- ============================================================================
-- 3. BIOMETRIC RAW ATTENDANCE TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS `biometric_raw_attendance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `device_log_id` int(11) DEFAULT NULL COMMENT 'Reference to biometric_device_logs',
  `device_sn` varchar(100) DEFAULT NULL COMMENT 'Device serial number',
  `table_type` varchar(50) DEFAULT NULL COMMENT 'ATTLOG or OPERLOG',
  `stamp` varchar(50) DEFAULT NULL COMMENT 'Device stamp',
  `employee_id` varchar(50) DEFAULT NULL COMMENT 'Employee/Student PIN',
  `punch_time` datetime DEFAULT NULL COMMENT 'Punch timestamp',
  `status1` int(11) DEFAULT NULL COMMENT 'Status field 1',
  `status2` int(11) DEFAULT NULL COMMENT 'Status field 2',
  `status3` int(11) DEFAULT NULL COMMENT 'Status field 3',
  `status4` int(11) DEFAULT NULL COMMENT 'Status field 4',
  `status5` int(11) DEFAULT NULL COMMENT 'Status field 5',
  `processed` tinyint(1) DEFAULT 0 COMMENT 'Whether this record has been processed',
  `processed_at` datetime DEFAULT NULL COMMENT 'When this record was processed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_device_log_id` (`device_log_id`),
  KEY `idx_device_sn` (`device_sn`),
  KEY `idx_employee_id` (`employee_id`),
  KEY `idx_processed` (`processed`),
  KEY `idx_punch_time` (`punch_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci COMMENT='Raw biometric attendance data';

-- ============================================================================
-- 4. BIOMETRIC TIMING SETUP TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS `biometric_timing_setup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `range_name` varchar(100) NOT NULL COMMENT 'Name of the time range (e.g., Morning Shift, Afternoon Shift)',
  `range_type` enum('checkin','checkout') NOT NULL COMMENT 'Type of time range',
  `time_start` time NOT NULL COMMENT 'Start time of the range',
  `time_end` time NOT NULL COMMENT 'End time of the range',
  `grace_period_minutes` int(11) DEFAULT 0 COMMENT 'Grace period in minutes before marking late',
  `attendance_type_id` int(11) NOT NULL COMMENT 'Attendance type ID to assign (1=Present, 2=Late, etc.)',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Active status',
  `priority` int(11) DEFAULT 0 COMMENT 'Priority order for matching (lower number = higher priority)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_range_type` (`range_type`),
  KEY `idx_is_active` (`is_active`),
  KEY `idx_priority` (`priority`),
  KEY `idx_time_range` (`time_start`,`time_end`),
  KEY `idx_attendance_type` (`attendance_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Biometric timing setup with multiple time ranges';

-- ============================================================================
-- 5. STAFF TIME RANGE ASSIGNMENTS TABLE
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
  KEY `idx_staff_id` (`staff_id`),
  KEY `idx_time_range_id` (`time_range_id`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci COMMENT='Assigns specific time ranges to staff members';

-- ============================================================================
-- 6. STUDENT TIME RANGE ASSIGNMENTS TABLE
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
  KEY `idx_student_session_id` (`student_session_id`),
  KEY `idx_time_range_id` (`time_range_id`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci COMMENT='Assigns specific time ranges to students';

-- ============================================================================
-- 7. UPDATE STAFF TABLE - ADD BIOMETRIC FIELDS IF NOT EXISTS
-- ============================================================================
ALTER TABLE `staff` 
ADD COLUMN IF NOT EXISTS `biometric_id` varchar(50) DEFAULT NULL COMMENT 'Biometric device ID' AFTER `employee_id`,
ADD COLUMN IF NOT EXISTS `biometric_device_pin` varchar(50) DEFAULT NULL COMMENT 'PIN used on biometric device' AFTER `biometric_id`;

-- ============================================================================
-- 8. UPDATE STUDENTS TABLE - ADD BIOMETRIC FIELDS IF NOT EXISTS
-- ============================================================================
ALTER TABLE `students` 
ADD COLUMN IF NOT EXISTS `biometric_id` varchar(50) DEFAULT NULL COMMENT 'Biometric device ID' AFTER `admission_no`,
ADD COLUMN IF NOT EXISTS `biometric_device_pin` varchar(50) DEFAULT NULL COMMENT 'PIN used on biometric device' AFTER `biometric_id`;

-- ============================================================================
-- 9. UPDATE STAFF_ATTENDANCE TABLE - ADD BIOMETRIC FIELDS IF NOT EXISTS
-- ============================================================================
ALTER TABLE `staff_attendance` 
ADD COLUMN IF NOT EXISTS `biometric_attendence` int(1) DEFAULT 0 COMMENT '1=From biometric device, 0=Manual entry' AFTER `staff_attendance_type_id`,
ADD COLUMN IF NOT EXISTS `is_authorized_range` tinyint(1) DEFAULT 1 COMMENT '1=Authorized time range, 0=Unauthorized' AFTER `biometric_attendence`,
ADD COLUMN IF NOT EXISTS `biometric_device_data` text DEFAULT NULL COMMENT 'JSON data from biometric device' AFTER `is_authorized_range`;

-- ============================================================================
-- 10. UPDATE STUDENT_ATTENDENCES TABLE - ADD BIOMETRIC FIELDS IF NOT EXISTS
-- ============================================================================
ALTER TABLE `student_attendences` 
ADD COLUMN IF NOT EXISTS `biometric_attendence` int(1) NOT NULL DEFAULT 0 COMMENT '1=From biometric device, 0=Manual entry' AFTER `student_session_id`,
ADD COLUMN IF NOT EXISTS `is_authorized_range` tinyint(1) DEFAULT 1 COMMENT '1=Authorized time range, 0=Unauthorized' AFTER `biometric_attendence`,
ADD COLUMN IF NOT EXISTS `biometric_device_data` text DEFAULT NULL COMMENT 'JSON data from biometric device' AFTER `remark`;

-- ============================================================================
-- 11. UPDATE SCH_SETTINGS TABLE - ADD BIOMETRIC SETTINGS IF NOT EXISTS
-- ============================================================================
ALTER TABLE `sch_settings` 
ADD COLUMN IF NOT EXISTS `biometric` int(11) DEFAULT 0 COMMENT '1=Enabled, 0=Disabled' AFTER `name`,
ADD COLUMN IF NOT EXISTS `biometric_device` text DEFAULT NULL COMMENT 'Comma-separated list of authorized device serial numbers' AFTER `biometric`;

-- ============================================================================
-- 12. INSERT DEFAULT TIMING RANGES (IF NOT EXISTS)
-- ============================================================================
INSERT IGNORE INTO `biometric_timing_setup` (`id`, `range_name`, `range_type`, `time_start`, `time_end`, `grace_period_minutes`, `attendance_type_id`, `is_active`, `priority`) VALUES
(1, 'Morning Check-in', 'checkin', '06:00:00', '10:00:00', 15, 1, 1, 1),
(2, 'Late Morning', 'checkin', '10:00:01', '12:00:00', 0, 2, 1, 2),
(3, 'Afternoon Check-in', 'checkin', '12:00:01', '18:00:00', 15, 1, 1, 3),
(4, 'Evening Check-out', 'checkout', '15:00:00', '23:59:59', 0, 1, 1, 1);

-- ============================================================================
-- 13. CREATE VIEWS FOR EASY QUERYING
-- ============================================================================

-- View for active biometric devices
CREATE OR REPLACE VIEW `v_active_biometric_devices` AS
SELECT 
    `id`, `sn`, `name`, `timezone`, `ip`, `is_allowed`, `note`, 
    `created_at`, `updated_at`,
    CASE WHEN `is_allowed` = 1 THEN 'Active' ELSE 'Blocked' END AS `status`
FROM `biometric_devices`
ORDER BY `created_at` DESC;

-- View for active biometric timings
CREATE OR REPLACE VIEW `v_active_biometric_timings` AS
SELECT 
    `id`, `range_name`, `range_type`, `time_start`, `time_end`, 
    `grace_period_minutes`, `attendance_type_id`, `priority`,
    CASE 
        WHEN `range_type` = 'checkin' THEN CONCAT('Check-in: ', TIME_FORMAT(`time_start`, '%h:%i %p'), ' - ', TIME_FORMAT(`time_end`, '%h:%i %p'))
        ELSE CONCAT('Check-out: ', TIME_FORMAT(`time_start`, '%h:%i %p'), ' - ', TIME_FORMAT(`time_end`, '%h:%i %p'))
    END AS `display_range`
FROM `biometric_timing_setup`
WHERE `is_active` = 1
ORDER BY `range_type` ASC, `priority` ASC;

-- ============================================================================
-- SETUP COMPLETE
-- ============================================================================
-- All tables, indexes, and views have been created/updated.
-- Next steps:
-- 1. Configure biometric settings in admin panel (sch_settings table)
-- 2. Add device serial numbers to authorized list
-- 3. Configure time ranges as needed
-- 4. Assign staff/students to time ranges (optional)
-- 5. Configure device to point to: http://your-server/biometric/index
-- ============================================================================

