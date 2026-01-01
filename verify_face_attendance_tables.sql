-- Verification Script for Face Attendance Tables
-- Run this to check if tables are properly created

-- Check if face_attendance_students table exists
SELECT 
    'face_attendance_students' as table_name,
    CASE 
        WHEN COUNT(*) > 0 THEN 'EXISTS ✓'
        ELSE 'NOT FOUND ✗'
    END as status
FROM information_schema.tables 
WHERE table_schema = 'amt' 
  AND table_name = 'face_attendance_students'

UNION ALL

-- Check if face_attendance_records table exists
SELECT 
    'face_attendance_records' as table_name,
    CASE 
        WHEN COUNT(*) > 0 THEN 'EXISTS ✓'
        ELSE 'NOT FOUND ✗'
    END as status
FROM information_schema.tables 
WHERE table_schema = 'amt' 
  AND table_name = 'face_attendance_records'

UNION ALL

-- Check if face_attendance_logs table exists
SELECT 
    'face_attendance_logs' as table_name,
    CASE 
        WHEN COUNT(*) > 0 THEN 'EXISTS ✓'
        ELSE 'NOT FOUND ✗'
    END as status
FROM information_schema.tables 
WHERE table_schema = 'amt' 
  AND table_name = 'face_attendance_logs';

-- Show structure of face_attendance_students table
SHOW COLUMNS FROM face_attendance_students;

-- Show structure of face_attendance_records table
SHOW COLUMNS FROM face_attendance_records;

-- Show structure of face_attendance_logs table
SHOW COLUMNS FROM face_attendance_logs;

-- Count existing records
SELECT 'face_attendance_students' as table_name, COUNT(*) as record_count FROM face_attendance_students
UNION ALL
SELECT 'face_attendance_records' as table_name, COUNT(*) as record_count FROM face_attendance_records
UNION ALL
SELECT 'face_attendance_logs' as table_name, COUNT(*) as record_count FROM face_attendance_logs;
