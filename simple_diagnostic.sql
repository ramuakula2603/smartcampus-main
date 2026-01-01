-- Simple diagnostic query to understand the negative balance issue

-- Query 1: Check a specific fee group with negative balance
SELECT 
    fg.id as fee_group_id,
    fg.name as fee_group_name,
    sfm.id as student_fees_master_id,
    sfm.amount as assigned_amount,
    s.admission_no,
    CONCAT(s.firstname, ' ', IFNULL(s.lastname, '')) as student_name
FROM fee_groups fg
INNER JOIN student_fees_master sfm ON sfm.fee_session_group_id = fg.id
INNER JOIN student_session ss ON ss.id = sfm.student_session_id
INNER JOIN students s ON s.id = ss.student_id
WHERE fg.name = '2025-2026 -SR- 0NTC'
  AND fg.is_system = 0
LIMIT 10;

-- Query 2: Check payment records for this fee group
SELECT 
    sfd.id as deposit_id,
    sfd.student_fees_master_id,
    sfd.amount_detail,
    sfd.created_at
FROM student_fees_deposite sfd
INNER JOIN student_fees_master sfm ON sfm.id = sfd.student_fees_master_id
INNER JOIN fee_groups fg ON fg.id = sfm.fee_session_group_id
WHERE fg.name = '2025-2026 -SR- 0NTC'
LIMIT 5;

-- Query 3: Check fee_groups_feetype to see if there are fee types defined
SELECT 
    fg.name as fee_group_name,
    ft.type as fee_type,
    fgft.amount as fee_type_amount,
    fgft.due_date
FROM fee_groups fg
INNER JOIN fee_groups_feetype fgft ON fgft.fee_groups_id = fg.id
INNER JOIN feetype ft ON ft.id = fgft.feetype_id
WHERE fg.name = '2025-2026 -SR- 0NTC';

-- Query 4: Check another problematic fee group
SELECT 
    fg.name as fee_group_name,
    ft.type as fee_type,
    fgft.amount as fee_type_amount,
    fgft.due_date
FROM fee_groups fg
INNER JOIN fee_groups_feetype fgft ON fgft.fee_groups_id = fg.id
INNER JOIN feetype ft ON ft.id = fgft.feetype_id
WHERE fg.name = '2025-2026 JR-BIPC(BOOKS FEE)';

