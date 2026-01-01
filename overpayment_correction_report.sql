-- OVERPAYMENT ANALYSIS AND CORRECTION QUERIES
-- Run these queries to identify and analyze overpayment issues

-- 1. IDENTIFY ALL OVERPAYMENTS IN SESSION 2025-26
SELECT 
    'OVERPAYMENT ANALYSIS FOR SESSION 2025-26' as report_title;

-- 2. FEE GROUPS WITH HIGHEST OVERPAYMENTS
SELECT 
    fg.name as fee_group_name,
    COUNT(DISTINCT sfm.student_session_id) as total_students,
    SUM(sfm.amount) as total_fee_amount,
    -- Calculate collected amount (this would need to be done in application)
    'MANUAL_CALCULATION_NEEDED' as total_collected,
    'MANUAL_CALCULATION_NEEDED' as overpayment_amount
FROM fee_groups fg
INNER JOIN fee_session_groups fsg ON fsg.fee_groups_id = fg.id AND fsg.session_id = 21
INNER JOIN student_fees_master sfm ON sfm.fee_session_group_id = fg.id
INNER JOIN student_session ss ON ss.id = sfm.student_session_id AND ss.session_id = 21
WHERE fg.is_system = 0
GROUP BY fg.id, fg.name
HAVING SUM(sfm.amount) > 0
ORDER BY SUM(sfm.amount) DESC;

-- 3. STUDENTS WITH HIGHEST INDIVIDUAL OVERPAYMENTS
SELECT 
    s.admission_no,
    CONCAT(s.firstname, ' ', IFNULL(s.lastname, '')) as student_name,
    c.class as class_name,
    sec.section as section_name,
    fg.name as fee_group_name,
    sfm.amount as fee_amount,
    sfm.id as master_id,
    -- Payment details would need to be calculated in application
    'CHECK_PAYMENTS' as payment_status
FROM students s
INNER JOIN student_session ss ON ss.student_id = s.id AND ss.session_id = 21
INNER JOIN classes c ON c.id = ss.class_id
INNER JOIN sections sec ON sec.id = ss.section_id
INNER JOIN student_fees_master sfm ON sfm.student_session_id = ss.id
INNER JOIN fee_groups fg ON fg.id = sfm.fee_session_group_id
WHERE fg.is_system = 0
  AND fg.name IN (
    '2025-2026 JR-BIPC(BOOKS FEE)',
    '2025-2026 JR-MPC(BOOKS FEE)', 
    '2025-2026 SR-BIPC BOOKS FEE',
    '2025-2026 TC FEE',
    '2025-2026 SR-CEC ONTC FEE'
  )
ORDER BY fg.name, s.admission_no;

-- 4. PAYMENT RECORDS FOR PROBLEMATIC CASES
SELECT 
    'SAMPLE PAYMENT RECORDS FOR ANALYSIS' as section_title;

SELECT 
    sfd.student_fees_master_id,
    sfm.amount as fee_amount,
    fg.name as fee_group_name,
    s.admission_no,
    CONCAT(s.firstname, ' ', IFNULL(s.lastname, '')) as student_name,
    sfd.amount_detail,
    LENGTH(sfd.amount_detail) as json_length
FROM student_fees_deposite sfd
INNER JOIN student_fees_master sfm ON sfm.id = sfd.student_fees_master_id
INNER JOIN student_session ss ON ss.id = sfm.student_session_id AND ss.session_id = 21
INNER JOIN students s ON s.id = ss.student_id
INNER JOIN fee_groups fg ON fg.id = sfm.fee_session_group_id
WHERE fg.name IN (
    '2025-2026 JR-BIPC(BOOKS FEE)',
    '2025-2026 TC FEE'
  )
  AND LENGTH(sfd.amount_detail) > 200  -- Large payment records
ORDER BY LENGTH(sfd.amount_detail) DESC
LIMIT 10;

-- 5. SUMMARY STATISTICS
SELECT 
    'SUMMARY STATISTICS' as section_title;

SELECT 
    COUNT(DISTINCT fg.id) as total_fee_groups,
    COUNT(DISTINCT sfm.student_session_id) as total_students,
    SUM(sfm.amount) as total_fee_amount,
    AVG(sfm.amount) as avg_fee_per_student
FROM fee_groups fg
INNER JOIN fee_session_groups fsg ON fsg.fee_groups_id = fg.id AND fsg.session_id = 21
INNER JOIN student_fees_master sfm ON sfm.fee_session_group_id = fg.id
INNER JOIN student_session ss ON ss.id = sfm.student_session_id AND ss.session_id = 21
WHERE fg.is_system = 0;

-- 6. POTENTIAL DATA CORRECTION STRATEGIES
SELECT 'DATA CORRECTION RECOMMENDATIONS' as recommendations;

/*
RECOMMENDED CORRECTION APPROACH:

1. IMMEDIATE ACTIONS:
   - Export all payment records for manual review
   - Identify payments > 2x fee amount for investigation
   - Contact students with large overpayments for verification

2. SYSTEMATIC CORRECTIONS:
   - For obvious data entry errors (e.g., ₹13000 instead of ₹1300):
     UPDATE student_fees_deposite 
     SET amount_detail = REPLACE(amount_detail, '"amount":13000', '"amount":1300')
     WHERE student_fees_master_id = [specific_id];
   
   - For advance payments that should be allocated across multiple fee groups:
     Create new payment records for other fee groups
     Adjust original payment amount

3. PREVENTION MEASURES:
   - Add validation in payment entry forms
   - Implement overpayment alerts
   - Create daily overpayment monitoring reports
   - Train staff on proper payment allocation

4. RECONCILIATION PROCESS:
   - Create monthly reconciliation reports
   - Compare total collections vs total fees
   - Investigate discrepancies > 5%
   - Maintain audit trail of all corrections

CAUTION: 
- Always backup data before making corrections
- Document all changes with reasons
- Get approval for corrections > ₹1000
- Verify with students before making adjustments
*/
