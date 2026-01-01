-- ============================================================================
-- Time Range Assignment System - Test & Verification Script
-- ============================================================================
-- This script tests and verifies the Time Range Assignment System installation
-- Run this after installing the system to ensure everything works correctly
-- ============================================================================

-- ============================================================================
-- TEST 1: Verify Tables Exist
-- ============================================================================
SELECT 'TEST 1: Checking if tables exist...' as test_name;

SELECT 
    CASE 
        WHEN COUNT(*) = 2 THEN '✅ PASS: Both assignment tables exist'
        ELSE '❌ FAIL: Assignment tables missing'
    END as result
FROM information_schema.tables 
WHERE table_schema = 'amt' 
  AND table_name IN ('staff_time_range_assignments', 'student_time_range_assignments');

-- ============================================================================
-- TEST 2: Verify Columns Added
-- ============================================================================
SELECT 'TEST 2: Checking if columns were added...' as test_name;

SELECT 
    CASE 
        WHEN COUNT(*) = 2 THEN '✅ PASS: is_authorized_range columns exist'
        ELSE '❌ FAIL: is_authorized_range columns missing'
    END as result
FROM information_schema.columns 
WHERE table_schema = 'amt' 
  AND column_name = 'is_authorized_range'
  AND table_name IN ('staff_attendance', 'student_attendences');

-- ============================================================================
-- TEST 3: Verify Foreign Keys
-- ============================================================================
SELECT 'TEST 3: Checking foreign key constraints...' as test_name;

SELECT 
    CASE 
        WHEN COUNT(*) >= 4 THEN '✅ PASS: Foreign keys exist'
        ELSE '❌ FAIL: Foreign keys missing'
    END as result
FROM information_schema.key_column_usage 
WHERE table_schema = 'amt' 
  AND table_name IN ('staff_time_range_assignments', 'student_time_range_assignments')
  AND referenced_table_name IS NOT NULL;

-- ============================================================================
-- TEST 4: Verify Permissions
-- ============================================================================
SELECT 'TEST 4: Checking permissions...' as test_name;

SELECT 
    CASE 
        WHEN COUNT(*) > 0 THEN '✅ PASS: Permission category exists'
        ELSE '❌ FAIL: Permission category missing'
    END as result
FROM permission_category 
WHERE short_code = 'time_range_assignments';

-- ============================================================================
-- TEST 5: Verify Time Ranges Exist
-- ============================================================================
SELECT 'TEST 5: Checking if time ranges exist...' as test_name;

SELECT 
    CASE 
        WHEN COUNT(*) > 0 THEN CONCAT('✅ PASS: ', COUNT(*), ' time ranges available')
        ELSE '❌ FAIL: No time ranges found'
    END as result
FROM biometric_timing_setup 
WHERE is_active = 1;

-- ============================================================================
-- TEST 6: Test Staff Assignment (Sample)
-- ============================================================================
SELECT 'TEST 6: Testing staff assignment functionality...' as test_name;

-- Get first active staff member
SET @test_staff_id = (SELECT id FROM staff WHERE is_active = 1 LIMIT 1);

-- Get first active time range
SET @test_time_range_id = (SELECT id FROM biometric_timing_setup WHERE is_active = 1 LIMIT 1);

-- Try to insert a test assignment
INSERT IGNORE INTO staff_time_range_assignments (staff_id, time_range_id, is_active, created_at)
VALUES (@test_staff_id, @test_time_range_id, 1, NOW());

-- Check if insert worked
SELECT 
    CASE 
        WHEN COUNT(*) > 0 THEN '✅ PASS: Staff assignment insert works'
        ELSE '❌ FAIL: Staff assignment insert failed'
    END as result
FROM staff_time_range_assignments 
WHERE staff_id = @test_staff_id AND time_range_id = @test_time_range_id;

-- Clean up test data
DELETE FROM staff_time_range_assignments 
WHERE staff_id = @test_staff_id AND time_range_id = @test_time_range_id;

-- ============================================================================
-- TEST 7: Test Student Assignment (Sample)
-- ============================================================================
SELECT 'TEST 7: Testing student assignment functionality...' as test_name;

-- Get first active student session
SET @test_student_session_id = (SELECT id FROM student_session WHERE is_active = 'yes' LIMIT 1);

-- Try to insert a test assignment
INSERT IGNORE INTO student_time_range_assignments (student_session_id, time_range_id, is_active, created_at)
VALUES (@test_student_session_id, @test_time_range_id, 1, NOW());

-- Check if insert worked
SELECT 
    CASE 
        WHEN COUNT(*) > 0 THEN '✅ PASS: Student assignment insert works'
        ELSE '❌ FAIL: Student assignment insert failed'
    END as result
FROM student_time_range_assignments 
WHERE student_session_id = @test_student_session_id AND time_range_id = @test_time_range_id;

-- Clean up test data
DELETE FROM student_time_range_assignments 
WHERE student_session_id = @test_student_session_id AND time_range_id = @test_time_range_id;

-- ============================================================================
-- TEST 8: Test Unique Constraint
-- ============================================================================
SELECT 'TEST 8: Testing unique constraint...' as test_name;

-- Try to insert duplicate (should fail silently with INSERT IGNORE)
INSERT IGNORE INTO staff_time_range_assignments (staff_id, time_range_id, is_active, created_at)
VALUES (@test_staff_id, @test_time_range_id, 1, NOW());

INSERT IGNORE INTO staff_time_range_assignments (staff_id, time_range_id, is_active, created_at)
VALUES (@test_staff_id, @test_time_range_id, 1, NOW());

-- Check if only one record exists
SELECT 
    CASE 
        WHEN COUNT(*) = 1 THEN '✅ PASS: Unique constraint works'
        ELSE '❌ FAIL: Unique constraint not working'
    END as result
FROM staff_time_range_assignments 
WHERE staff_id = @test_staff_id AND time_range_id = @test_time_range_id;

-- Clean up test data
DELETE FROM staff_time_range_assignments 
WHERE staff_id = @test_staff_id AND time_range_id = @test_time_range_id;

-- ============================================================================
-- SUMMARY: Show Current State
-- ============================================================================
SELECT '============================================' as separator;
SELECT 'SUMMARY: Current System State' as summary_title;
SELECT '============================================' as separator;

-- Count time ranges
SELECT 
    'Time Ranges' as item,
    COUNT(*) as total,
    SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active,
    SUM(CASE WHEN range_type = 'checkin' THEN 1 ELSE 0 END) as checkin_ranges,
    SUM(CASE WHEN range_type = 'checkout' THEN 1 ELSE 0 END) as checkout_ranges
FROM biometric_timing_setup;

-- Count staff assignments
SELECT 
    'Staff Assignments' as item,
    COUNT(*) as total,
    COUNT(DISTINCT staff_id) as unique_staff,
    SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_assignments
FROM staff_time_range_assignments;

-- Count student assignments
SELECT 
    'Student Assignments' as item,
    COUNT(*) as total,
    COUNT(DISTINCT student_session_id) as unique_students,
    SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_assignments
FROM student_time_range_assignments;

-- Count staff with assignments
SELECT 
    'Staff with Assignments' as item,
    COUNT(DISTINCT stra.staff_id) as count,
    CONCAT(ROUND((COUNT(DISTINCT stra.staff_id) / (SELECT COUNT(*) FROM staff WHERE is_active = 1)) * 100, 2), '%') as percentage
FROM staff_time_range_assignments stra
WHERE stra.is_active = 1;

-- Count students with assignments
SELECT 
    'Students with Assignments' as item,
    COUNT(DISTINCT stra.student_session_id) as count,
    CONCAT(ROUND((COUNT(DISTINCT stra.student_session_id) / (SELECT COUNT(*) FROM student_session WHERE is_active = 'yes')) * 100, 2), '%') as percentage
FROM student_time_range_assignments stra
WHERE stra.is_active = 1;

-- ============================================================================
-- DETAILED INFORMATION
-- ============================================================================
SELECT '============================================' as separator;
SELECT 'DETAILED INFORMATION' as detail_title;
SELECT '============================================' as separator;

-- Show all time ranges
SELECT 
    'Available Time Ranges:' as info;

SELECT 
    id,
    range_name,
    range_type,
    CONCAT(time_start, ' - ', time_end) as time_window,
    grace_period_minutes,
    CASE WHEN is_active = 1 THEN 'Active' ELSE 'Inactive' END as status,
    priority
FROM biometric_timing_setup
ORDER BY range_type, priority;

-- Show staff with assignments (if any)
SELECT 
    'Staff with Time Range Assignments:' as info;

SELECT 
    s.employee_id,
    s.name,
    COUNT(stra.id) as assigned_ranges,
    GROUP_CONCAT(bts.range_name ORDER BY bts.priority SEPARATOR ', ') as assigned_range_names
FROM staff s
JOIN staff_time_range_assignments stra ON s.id = stra.staff_id
JOIN biometric_timing_setup bts ON stra.time_range_id = bts.id
WHERE stra.is_active = 1 AND bts.is_active = 1
GROUP BY s.id
ORDER BY s.employee_id;

-- Show students with assignments (if any)
SELECT 
    'Students with Time Range Assignments:' as info;

SELECT 
    st.admission_no,
    st.firstname,
    st.lastname,
    COUNT(stra.id) as assigned_ranges,
    GROUP_CONCAT(bts.range_name ORDER BY bts.priority SEPARATOR ', ') as assigned_range_names
FROM students st
JOIN student_session ss ON st.id = ss.student_id
JOIN student_time_range_assignments stra ON ss.id = stra.student_session_id
JOIN biometric_timing_setup bts ON stra.time_range_id = bts.id
WHERE stra.is_active = 1 AND bts.is_active = 1 AND ss.is_active = 'yes'
GROUP BY st.id
ORDER BY st.admission_no;

-- ============================================================================
-- RECOMMENDATIONS
-- ============================================================================
SELECT '============================================' as separator;
SELECT 'RECOMMENDATIONS' as recommendations_title;
SELECT '============================================' as separator;

SELECT 
    CASE 
        WHEN (SELECT COUNT(*) FROM biometric_timing_setup WHERE is_active = 1) = 0 
        THEN '⚠️  WARNING: No active time ranges found. Please create time ranges first.'
        WHEN (SELECT COUNT(*) FROM staff_time_range_assignments) = 0 
        THEN '✅ INFO: No staff assignments yet. All staff can use all time ranges (backward compatible).'
        WHEN (SELECT COUNT(*) FROM student_time_range_assignments) = 0 
        THEN '✅ INFO: No student assignments yet. All students can use all time ranges (backward compatible).'
        ELSE '✅ READY: System is configured and ready to use!'
    END as recommendation;

-- ============================================================================
-- NEXT STEPS
-- ============================================================================
SELECT '============================================' as separator;
SELECT 'NEXT STEPS' as next_steps_title;
SELECT '============================================' as separator;

SELECT 
    '1. Access the Time Range Assignment interface at: http://localhost/amt/admin/time_range_assignment' as step
UNION ALL
SELECT 
    '2. Assign time ranges to staff members who need restrictions'
UNION ALL
SELECT 
    '3. Assign time ranges to students who need restrictions'
UNION ALL
SELECT 
    '4. Test biometric punches to verify authorization works'
UNION ALL
SELECT 
    '5. Monitor unauthorized punches using the queries in the documentation';

-- ============================================================================
-- END OF TEST SCRIPT
-- ============================================================================
SELECT '============================================' as separator;
SELECT '✅ TEST SCRIPT COMPLETED' as completion_message;
SELECT '============================================' as separator;

