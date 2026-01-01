-- ============================================
-- Biometric API Test Environment Setup
-- ============================================
-- Run this script to prepare your system for testing

-- Step 1: Enable Biometric Feature
-- ============================================
UPDATE sch_settings 
SET biometric = 1, 
    biometric_device = 'TEST_DEVICE_001'
WHERE id = 1;

-- Verify settings
SELECT id, name, biometric, biometric_device 
FROM sch_settings 
WHERE id = 1;

-- Step 2: Create Test Staff (if not exists)
-- ============================================
-- Check if test staff exists
SELECT id, employee_id, name, surname 
FROM staff 
WHERE employee_id = '1001';

-- If not exists, insert test staff
INSERT INTO staff (
    employee_id, 
    name, 
    surname, 
    contact_no, 
    email, 
    dob, 
    gender, 
    date_of_joining, 
    is_active,
    lang_id,
    currency_id
)
SELECT 
    '1001',
    'Test',
    'Staff',
    '1234567890',
    'teststaff@example.com',
    '1990-01-01',
    'Male',
    CURDATE(),
    1,
    1,
    1
WHERE NOT EXISTS (
    SELECT 1 FROM staff WHERE employee_id = '1001'
);

-- Step 3: Create Test Student (if not exists)
-- ============================================
-- Check if test student exists
SELECT id, admission_no, firstname, lastname 
FROM students 
WHERE admission_no = '2001';

-- If not exists, insert test student
INSERT INTO students (
    admission_no,
    firstname,
    lastname,
    dob,
    gender,
    mobileno,
    email,
    is_active,
    parent_id
)
SELECT 
    '2001',
    'Test',
    'Student',
    '2010-01-01',
    'Male',
    '9876543210',
    'teststudent@example.com',
    'yes',
    0
WHERE NOT EXISTS (
    SELECT 1 FROM students WHERE admission_no = '2001'
);

-- Step 4: Verify Database Tables Exist
-- ============================================
-- Check if biometric tables exist
SELECT 
    TABLE_NAME,
    TABLE_ROWS,
    CREATE_TIME
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME IN ('biometric_device_logs', 'biometric_raw_attendance')
ORDER BY TABLE_NAME;

-- If tables don't exist, create them
-- ============================================

-- Create biometric_device_logs table
CREATE TABLE IF NOT EXISTS `biometric_device_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `request_method` varchar(10) NOT NULL,
  `request_uri` varchar(255) DEFAULT NULL,
  `query_string` text DEFAULT NULL,
  `raw_body` text DEFAULT NULL,
  `parsed_data` text DEFAULT NULL,
  `device_sn` varchar(100) DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `processing_status` enum('pending','success','error') DEFAULT 'pending',
  `error_message` text DEFAULT NULL,
  `records_processed` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_device_sn` (`device_sn`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_processing_status` (`processing_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create biometric_raw_attendance table
CREATE TABLE IF NOT EXISTS `biometric_raw_attendance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `device_log_id` int(11) DEFAULT NULL,
  `device_sn` varchar(100) DEFAULT NULL,
  `table_type` varchar(50) DEFAULT NULL,
  `stamp` varchar(50) DEFAULT NULL,
  `employee_id` varchar(100) NOT NULL,
  `punch_time` datetime NOT NULL,
  `status1` int(11) DEFAULT NULL,
  `status2` int(11) DEFAULT NULL,
  `status3` int(11) DEFAULT NULL,
  `status4` int(11) DEFAULT NULL,
  `status5` int(11) DEFAULT NULL,
  `processed` tinyint(1) DEFAULT 0,
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_device_log_id` (`device_log_id`),
  KEY `idx_employee_id` (`employee_id`),
  KEY `idx_punch_time` (`punch_time`),
  KEY `idx_processed` (`processed`),
  KEY `idx_device_sn` (`device_sn`),
  CONSTRAINT `fk_device_log` FOREIGN KEY (`device_log_id`) REFERENCES `biometric_device_logs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Step 5: Clean Up Old Test Data (Optional)
-- ============================================
-- Uncomment these lines if you want to clean up old test data

-- DELETE FROM biometric_device_logs WHERE device_sn = 'TEST_DEVICE_001';
-- DELETE FROM biometric_raw_attendance WHERE device_sn = 'TEST_DEVICE_001';
-- DELETE FROM staff_attendance WHERE staff_id IN (SELECT id FROM staff WHERE employee_id = '1001');
-- DELETE FROM student_attendences WHERE student_session_id IN (
--     SELECT id FROM student_session WHERE student_id IN (
--         SELECT id FROM students WHERE admission_no = '2001'
--     )
-- );

-- Step 6: Verify Setup
-- ============================================

-- Check biometric settings
SELECT 
    'Biometric Settings' as check_type,
    CASE 
        WHEN biometric = 1 THEN '✓ Enabled'
        ELSE '✗ Disabled'
    END as status,
    biometric_device as device_sn
FROM sch_settings
WHERE id = 1;

-- Check test users
SELECT 
    'Test Staff' as check_type,
    CASE 
        WHEN COUNT(*) > 0 THEN CONCAT('✓ Found (ID: ', GROUP_CONCAT(id), ')')
        ELSE '✗ Not Found'
    END as status,
    GROUP_CONCAT(CONCAT(name, ' ', surname)) as name
FROM staff
WHERE employee_id = '1001';

SELECT 
    'Test Student' as check_type,
    CASE 
        WHEN COUNT(*) > 0 THEN CONCAT('✓ Found (ID: ', GROUP_CONCAT(id), ')')
        ELSE '✗ Not Found'
    END as status,
    GROUP_CONCAT(CONCAT(firstname, ' ', lastname)) as name
FROM students
WHERE admission_no = '2001';

-- Check tables
SELECT 
    'Biometric Tables' as check_type,
    CASE 
        WHEN COUNT(*) = 2 THEN '✓ Both tables exist'
        WHEN COUNT(*) = 1 THEN '⚠ Only 1 table exists'
        ELSE '✗ Tables missing'
    END as status,
    GROUP_CONCAT(TABLE_NAME) as tables
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME IN ('biometric_device_logs', 'biometric_raw_attendance');

-- Step 7: Test Data Summary
-- ============================================

SELECT '============================================' as '';
SELECT 'SETUP COMPLETE - READY FOR TESTING' as '';
SELECT '============================================' as '';
SELECT '' as '';
SELECT 'Test Configuration:' as '';
SELECT '-------------------' as '';
SELECT CONCAT('Device SN: ', biometric_device) as config FROM sch_settings WHERE id = 1;
SELECT CONCAT('Test Staff ID: 1001 (', name, ' ', surname, ')') as config 
FROM staff WHERE employee_id = '1001' LIMIT 1;
SELECT CONCAT('Test Student ID: 2001 (', firstname, ' ', lastname, ')') as config 
FROM students WHERE admission_no = '2001' LIMIT 1;
SELECT '' as '';
SELECT 'Postman Test URL:' as '';
SELECT '----------------' as '';
SELECT 'POST http://your-domain.com/iclock/cdata?SN=TEST_DEVICE_001&table=ATTLOG&Stamp=1' as url;
SELECT '' as '';
SELECT 'Test Body (Raw):' as '';
SELECT '---------------' as '';
SELECT '1001\t2025-10-24 08:30:00\t0\t0\t0\t0\t0' as body;
SELECT '' as '';
SELECT 'Expected Response:' as '';
SELECT '-----------------' as '';
SELECT 'OK: 1' as response;
SELECT '' as '';
SELECT 'Verification URLs:' as '';
SELECT '-----------------' as '';
SELECT 'Device Logs: http://your-domain.com/biometric/device_logs' as url;
SELECT 'Raw Attendance: http://your-domain.com/biometric/raw_attendance' as url;
SELECT '' as '';
SELECT '============================================' as '';

-- Step 8: Quick Test Queries
-- ============================================

-- After running Postman tests, use these queries to verify:

-- View recent device logs
-- SELECT * FROM biometric_device_logs ORDER BY created_at DESC LIMIT 5;

-- View recent raw attendance
-- SELECT * FROM biometric_raw_attendance ORDER BY created_at DESC LIMIT 5;

-- View today's staff attendance
-- SELECT * FROM staff_attendance WHERE DATE(date) = CURDATE();

-- View today's student attendance
-- SELECT * FROM student_attendences WHERE DATE(date) = CURDATE();

-- Count logs by status
-- SELECT processing_status, COUNT(*) as count 
-- FROM biometric_device_logs 
-- GROUP BY processing_status;

-- Count processed vs unprocessed
-- SELECT 
--     SUM(CASE WHEN processed = 1 THEN 1 ELSE 0 END) as processed,
--     SUM(CASE WHEN processed = 0 THEN 1 ELSE 0 END) as unprocessed
-- FROM biometric_raw_attendance;

