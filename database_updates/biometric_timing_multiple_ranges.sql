-- ============================================================================
-- Biometric Timing Setup - Multiple Time Ranges Enhancement
-- ============================================================================
-- This script modifies the biometric_timing_setup table to support multiple
-- check-in and check-out time ranges with late marking functionality
-- ============================================================================

-- Drop existing table if exists
DROP TABLE IF EXISTS `biometric_timing_setup`;

-- Create new table with multiple time range support
CREATE TABLE `biometric_timing_setup` (
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
  KEY `idx_priority` (`priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Biometric timing setup with multiple time ranges';

-- Insert default check-in time ranges
INSERT INTO `biometric_timing_setup` 
(`range_name`, `range_type`, `time_start`, `time_end`, `grace_period_minutes`, `attendance_type_id`, `is_active`, `priority`) 
VALUES
-- Morning Check-in (On Time)
('Morning Check-in (On Time)', 'checkin', '08:00:00', '09:00:00', 15, 1, 1, 1),
-- Morning Check-in (Late)
('Morning Check-in (Late)', 'checkin', '09:00:01', '10:00:00', 0, 2, 1, 2),
-- Afternoon Check-in (On Time)
('Afternoon Check-in (On Time)', 'checkin', '13:00:00', '14:00:00', 10, 1, 1, 3),
-- Afternoon Check-in (Late)
('Afternoon Check-in (Late)', 'checkin', '14:00:01', '15:00:00', 0, 2, 1, 4);

-- Insert default check-out time ranges
INSERT INTO `biometric_timing_setup` 
(`range_name`, `range_type`, `time_start`, `time_end`, `grace_period_minutes`, `attendance_type_id`, `is_active`, `priority`) 
VALUES
-- Evening Check-out
('Evening Check-out', 'checkout', '17:00:00', '19:00:00', 0, 1, 1, 1),
-- Late Evening Check-out
('Late Evening Check-out', 'checkout', '19:00:01', '22:00:00', 0, 1, 1, 2);

-- ============================================================================
-- Add indexes for performance
-- ============================================================================
ALTER TABLE `biometric_timing_setup` 
ADD INDEX `idx_time_range` (`time_start`, `time_end`),
ADD INDEX `idx_active_priority` (`is_active`, `priority`);

-- ============================================================================
-- Create view for easy querying of active time ranges
-- ============================================================================
CREATE OR REPLACE VIEW `v_active_biometric_timings` AS
SELECT 
    `id`,
    `range_name`,
    `range_type`,
    `time_start`,
    `time_end`,
    `grace_period_minutes`,
    `attendance_type_id`,
    `priority`,
    CASE 
        WHEN `range_type` = 'checkin' THEN 
            CONCAT('Check-in: ', TIME_FORMAT(`time_start`, '%h:%i %p'), ' - ', TIME_FORMAT(`time_end`, '%h:%i %p'))
        ELSE 
            CONCAT('Check-out: ', TIME_FORMAT(`time_start`, '%h:%i %p'), ' - ', TIME_FORMAT(`time_end`, '%h:%i %p'))
    END AS `display_range`
FROM `biometric_timing_setup`
WHERE `is_active` = 1
ORDER BY `range_type`, `priority`;

-- ============================================================================
-- Sample queries for testing
-- ============================================================================

-- Query to get all active check-in ranges
-- SELECT * FROM biometric_timing_setup WHERE range_type = 'checkin' AND is_active = 1 ORDER BY priority;

-- Query to get all active check-out ranges
-- SELECT * FROM biometric_timing_setup WHERE range_type = 'checkout' AND is_active = 1 ORDER BY priority;

-- Query to find matching time range for a specific time
-- SELECT * FROM biometric_timing_setup 
-- WHERE range_type = 'checkin' 
-- AND is_active = 1 
-- AND '09:30:00' BETWEEN time_start AND time_end 
-- ORDER BY priority LIMIT 1;

-- ============================================================================
-- Notes:
-- ============================================================================
-- 1. attendance_type_id values:
--    - 1 = Present
--    - 2 = Late
--    - 3 = Absent
--    - 4 = Half Day
--    - 5 = Holiday
--
-- 2. grace_period_minutes:
--    - Allows a grace period before marking as late
--    - Example: If time_start is 09:00:00 and grace_period_minutes is 15,
--      then punches between 09:00:00 and 09:15:00 will still be marked as Present
--
-- 3. priority:
--    - Lower number = higher priority
--    - Used when multiple ranges overlap
--    - System will use the first matching range based on priority
--
-- 4. Multiple shifts support:
--    - You can add multiple check-in/check-out ranges for different shifts
--    - Each shift can have its own grace period and attendance type
-- ============================================================================

