-- ============================================================================
-- INTERNAL RESULTS MODULE - DATABASE VERIFICATION SCRIPT
-- ============================================================================
-- Purpose: Verify database structure and test data integrity
-- Usage: Run this script in phpMyAdmin or MySQL command line
-- ============================================================================

-- ----------------------------------------------------------------------------
-- SECTION 1: VERIFY TABLE STRUCTURES
-- ----------------------------------------------------------------------------

-- Check if all required tables exist
SELECT 
    'Table Existence Check' AS verification_type,
    CASE 
        WHEN COUNT(*) = 5 THEN '✓ PASS - All tables exist'
        ELSE '✗ FAIL - Missing tables'
    END AS result,
    COUNT(*) AS tables_found,
    5 AS tables_expected
FROM information_schema.tables 
WHERE table_schema = DATABASE()
  AND table_name IN (
      'resultaddingstatus',
      'internalresulttable',
      'students',
      'student_session',
      'examtype'
  );

-- ----------------------------------------------------------------------------
-- SECTION 2: VERIFY resultaddingstatus TABLE STRUCTURE
-- ----------------------------------------------------------------------------

SELECT 
    '--- resultaddingstatus Table Structure ---' AS info;

DESCRIBE resultaddingstatus;

-- Check indexes on resultaddingstatus
SELECT 
    'Index Check - resultaddingstatus' AS verification_type,
    CASE 
        WHEN COUNT(*) >= 4 THEN '✓ PASS - All indexes exist'
        ELSE '✗ FAIL - Missing indexes'
    END AS result,
    COUNT(*) AS indexes_found
FROM information_schema.statistics
WHERE table_schema = DATABASE()
  AND table_name = 'resultaddingstatus'
  AND index_name IN ('PRIMARY', 'resultype_id', 'session_id', 'stid');

-- ----------------------------------------------------------------------------
-- SECTION 3: VERIFY internalresulttable TABLE STRUCTURE
-- ----------------------------------------------------------------------------

SELECT 
    '--- internalresulttable Table Structure ---' AS info;

DESCRIBE internalresulttable;

-- Check indexes on internalresulttable
SELECT 
    'Index Check - internalresulttable' AS verification_type,
    CASE 
        WHEN COUNT(*) >= 5 THEN '✓ PASS - All indexes exist'
        ELSE '✗ FAIL - Missing indexes'
    END AS result,
    COUNT(*) AS indexes_found
FROM information_schema.statistics
WHERE table_schema = DATABASE()
  AND table_name = 'internalresulttable'
  AND index_name IN ('PRIMARY', 'resulgroup_id', 'subjectid', 'session_id', 'stid');

-- ----------------------------------------------------------------------------
-- SECTION 4: CHECK CURRENT SESSION
-- ----------------------------------------------------------------------------

SELECT 
    '--- Current Active Session ---' AS info;

SELECT 
    id AS session_id,
    session AS session_name,
    is_active,
    created_at
FROM sessions
WHERE is_active = 'yes'
LIMIT 1;

-- ----------------------------------------------------------------------------
-- SECTION 5: CHECK RESULT TYPES (EXAM TYPES)
-- ----------------------------------------------------------------------------

SELECT 
    '--- Available Result Types ---' AS info;

SELECT 
    id AS resultype_id,
    examtype AS exam_name,
    session_id,
    is_active,
    created_at
FROM examtype
WHERE is_active = 'yes'
ORDER BY id;

-- ----------------------------------------------------------------------------
-- SECTION 6: ASSIGNMENT STATUS SUMMARY
-- ----------------------------------------------------------------------------

SELECT 
    '--- Assignment Status Summary ---' AS info;

SELECT 
    e.id AS resultype_id,
    e.examtype AS exam_name,
    COUNT(CASE WHEN r.assign_status = 1 THEN 1 END) AS assigned_students,
    COUNT(CASE WHEN r.assign_status = 0 THEN 1 END) AS unassigned_students,
    COUNT(*) AS total_records
FROM resultaddingstatus r
JOIN examtype e ON r.resultype_id = e.id
WHERE r.session_id = (SELECT id FROM sessions WHERE is_active = 'yes' LIMIT 1)
GROUP BY e.id, e.examtype
ORDER BY e.id;

-- ----------------------------------------------------------------------------
-- SECTION 7: DATA INTEGRITY CHECKS
-- ----------------------------------------------------------------------------

-- Check 1: Students with marks but no assignment (SHOULD BE ZERO)
SELECT 
    'Data Integrity Check 1' AS check_name,
    'Students with marks but not assigned' AS description,
    COUNT(DISTINCT i.stid) AS issue_count,
    CASE 
        WHEN COUNT(DISTINCT i.stid) = 0 THEN '✓ PASS - No issues found'
        ELSE '✗ FAIL - Found students with marks but not assigned'
    END AS result
FROM internalresulttable i
LEFT JOIN resultaddingstatus r ON i.stid = r.stid 
    AND i.resulgroup_id = r.resultype_id
    AND i.session_id = r.session_id
WHERE r.assign_status IS NULL OR r.assign_status = 0;

-- Check 2: Students with marks but assign_status = 0 (SHOULD BE ZERO)
SELECT 
    'Data Integrity Check 2' AS check_name,
    'Students with marks but assign_status = 0' AS description,
    COUNT(DISTINCT i.stid) AS issue_count,
    CASE 
        WHEN COUNT(DISTINCT i.stid) = 0 THEN '✓ PASS - No issues found'
        ELSE '✗ FAIL - Found students with marks but unassigned'
    END AS result
FROM internalresulttable i
JOIN resultaddingstatus r ON i.stid = r.stid 
    AND i.resulgroup_id = r.resultype_id
    AND i.session_id = r.session_id
WHERE r.assign_status = 0;

-- Check 3: Orphaned assignment records (students don't exist)
SELECT 
    'Data Integrity Check 3' AS check_name,
    'Orphaned assignment records' AS description,
    COUNT(*) AS issue_count,
    CASE 
        WHEN COUNT(*) = 0 THEN '✓ PASS - No orphaned records'
        ELSE '✗ FAIL - Found orphaned assignment records'
    END AS result
FROM resultaddingstatus r
LEFT JOIN students s ON r.stid = s.id
WHERE s.id IS NULL;

-- Check 4: Orphaned result records (students don't exist)
SELECT 
    'Data Integrity Check 4' AS check_name,
    'Orphaned result records' AS description,
    COUNT(*) AS issue_count,
    CASE 
        WHEN COUNT(*) = 0 THEN '✓ PASS - No orphaned records'
        ELSE '✗ FAIL - Found orphaned result records'
    END AS result
FROM internalresulttable i
LEFT JOIN students s ON i.stid = s.id
WHERE s.id IS NULL;

-- ----------------------------------------------------------------------------
-- SECTION 8: SAMPLE DATA - ASSIGNED STUDENTS
-- ----------------------------------------------------------------------------

SELECT 
    '--- Sample: Assigned Students (First 10) ---' AS info;

SELECT 
    s.id AS student_id,
    s.admission_no,
    CONCAT(s.firstname, ' ', COALESCE(s.lastname, '')) AS student_name,
    e.examtype AS exam_name,
    r.assign_status,
    r.created_at AS assignment_date,
    COUNT(i.id) AS subjects_with_marks
FROM students s
JOIN resultaddingstatus r ON s.id = r.stid
JOIN examtype e ON r.resultype_id = e.id
LEFT JOIN internalresulttable i ON s.id = i.stid 
    AND i.resulgroup_id = r.resultype_id
    AND i.session_id = r.session_id
WHERE r.assign_status = 1
  AND r.session_id = (SELECT id FROM sessions WHERE is_active = 'yes' LIMIT 1)
GROUP BY s.id, s.admission_no, s.firstname, s.lastname, e.examtype, r.assign_status, r.created_at
ORDER BY r.created_at DESC
LIMIT 10;

-- ----------------------------------------------------------------------------
-- SECTION 9: DETAILED RESULT DATA FOR SPECIFIC RESULT TYPE
-- ----------------------------------------------------------------------------

SELECT 
    '--- Detailed Results for Result Type ID 6 ---' AS info;

SELECT 
    s.admission_no,
    CONCAT(s.firstname, ' ', COALESCE(s.lastname, '')) AS student_name,
    sub.name AS subject_name,
    i.actualmarks AS marks,
    i.created_at AS marks_entry_date
FROM internalresulttable i
JOIN students s ON i.stid = s.id
JOIN subjects sub ON i.subjectid = sub.id
WHERE i.resulgroup_id = 6
  AND i.session_id = (SELECT id FROM sessions WHERE is_active = 'yes' LIMIT 1)
ORDER BY s.admission_no, sub.name
LIMIT 20;

-- ----------------------------------------------------------------------------
-- SECTION 10: ASSIGNMENT STATUS BREAKDOWN BY CLASS
-- ----------------------------------------------------------------------------

SELECT 
    '--- Assignment Status by Class ---' AS info;

SELECT 
    c.class AS class_name,
    sec.section AS section_name,
    e.examtype AS exam_name,
    COUNT(CASE WHEN r.assign_status = 1 THEN 1 END) AS assigned,
    COUNT(CASE WHEN r.assign_status = 0 THEN 1 END) AS unassigned,
    COUNT(*) AS total
FROM resultaddingstatus r
JOIN students s ON r.stid = s.id
JOIN student_session ss ON s.id = ss.student_id
JOIN classes c ON ss.class_id = c.id
JOIN sections sec ON ss.section_id = sec.id
JOIN examtype e ON r.resultype_id = e.id
WHERE r.session_id = (SELECT id FROM sessions WHERE is_active = 'yes' LIMIT 1)
  AND ss.session_id = r.session_id
GROUP BY c.class, sec.section, e.examtype
ORDER BY c.class, sec.section, e.examtype;

-- ----------------------------------------------------------------------------
-- SECTION 11: QUICK VERIFICATION QUERIES
-- ----------------------------------------------------------------------------

-- Query 1: Total assigned students across all result types
SELECT 
    'Total Assigned Students' AS metric,
    COUNT(DISTINCT stid) AS count
FROM resultaddingstatus
WHERE assign_status = 1
  AND session_id = (SELECT id FROM sessions WHERE is_active = 'yes' LIMIT 1);

-- Query 2: Total students with marks entered
SELECT 
    'Total Students with Marks' AS metric,
    COUNT(DISTINCT stid) AS count
FROM internalresulttable
WHERE session_id = (SELECT id FROM sessions WHERE is_active = 'yes' LIMIT 1);

-- Query 3: Total marks records
SELECT 
    'Total Marks Records' AS metric,
    COUNT(*) AS count
FROM internalresulttable
WHERE session_id = (SELECT id FROM sessions WHERE is_active = 'yes' LIMIT 1);

-- Query 4: Average marks by result type
SELECT 
    e.examtype AS exam_name,
    COUNT(DISTINCT i.stid) AS students_count,
    COUNT(i.id) AS total_marks_entries,
    ROUND(AVG(i.actualmarks), 2) AS average_marks,
    MIN(i.actualmarks) AS min_marks,
    MAX(i.actualmarks) AS max_marks
FROM internalresulttable i
JOIN examtype e ON i.resulgroup_id = e.id
WHERE i.session_id = (SELECT id FROM sessions WHERE is_active = 'yes' LIMIT 1)
GROUP BY e.examtype
ORDER BY e.examtype;

-- ----------------------------------------------------------------------------
-- SECTION 12: SPECIFIC CHECKS FOR RESULT TYPE ID 6
-- ----------------------------------------------------------------------------

SELECT 
    '--- Specific Checks for Result Type ID 6 ---' AS info;

-- Check assignment status distribution
SELECT 
    'Assignment Status Distribution' AS metric,
    CASE 
        WHEN assign_status = 0 THEN 'Unassigned (0)'
        WHEN assign_status = 1 THEN 'Assigned (1)'
        ELSE 'Unknown'
    END AS status,
    COUNT(*) AS count
FROM resultaddingstatus
WHERE resultype_id = 6
  AND session_id = (SELECT id FROM sessions WHERE is_active = 'yes' LIMIT 1)
GROUP BY assign_status;

-- Check marks entry status
SELECT 
    'Marks Entry Status' AS metric,
    COUNT(DISTINCT i.stid) AS students_with_marks,
    COUNT(DISTINCT r.stid) AS students_assigned,
    COUNT(DISTINCT r.stid) - COUNT(DISTINCT i.stid) AS students_without_marks
FROM resultaddingstatus r
LEFT JOIN internalresulttable i ON r.stid = i.stid 
    AND i.resulgroup_id = r.resultype_id
    AND i.session_id = r.session_id
WHERE r.resultype_id = 6
  AND r.assign_status = 1
  AND r.session_id = (SELECT id FROM sessions WHERE is_active = 'yes' LIMIT 1);

-- ============================================================================
-- END OF VERIFICATION SCRIPT
-- ============================================================================

SELECT 
    '============================================' AS separator,
    'VERIFICATION COMPLETE' AS status,
    NOW() AS timestamp,
    '============================================' AS separator2;

