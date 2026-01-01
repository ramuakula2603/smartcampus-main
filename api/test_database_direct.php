<?php
/**
 * Direct database query to check payment 945
 */

header('Content-Type: text/plain; charset=utf-8');

require_once(dirname(__FILE__) . '/index.php');
$CI =& get_instance();
$CI->load->database();

echo "=== Direct Database Query for Payment 945 ===\n\n";

// Get the full record with all joins
$sql = "
SELECT 
    sfd.id,
    sfd.student_fees_master_id,
    sfd.fee_groups_feetype_id,
    sfd.amount_detail,
    sfd.created_at,
    
    fgft.feetype_id as fee_type_id,
    ft.type as fee_type_name,
    ft.code as fee_type_code,
    
    sfm.student_session_id,
    
    ss.student_id,
    ss.class_id,
    ss.section_id,
    ss.session_id,
    
    s.firstname,
    s.middlename,
    s.lastname,
    s.admission_no,
    
    c.class as class_name,
    sec.section as section_name,
    ses.session as session_name

FROM student_fees_depositeadding sfd

JOIN fee_groups_feetypeadding fgft ON fgft.id = sfd.fee_groups_feetype_id
JOIN feetypeadding ft ON ft.id = fgft.feetype_id
JOIN student_fees_masteradding sfm ON sfm.id = sfd.student_fees_master_id
JOIN student_session ss ON ss.id = sfm.student_session_id
JOIN students s ON s.id = ss.student_id
JOIN classes c ON c.id = ss.class_id
JOIN sections sec ON sec.id = ss.section_id
JOIN sessions ses ON ses.id = ss.session_id

WHERE sfd.id = 945
";

$query = $CI->db->query($sql);
$record = $query->row_array();

if (!$record) {
    echo "✗ Payment 945 NOT FOUND in database!\n";
    
    // Check if table exists and has data
    $count = $CI->db->count_all('student_fees_depositeadding');
    echo "\nTotal records in student_fees_depositeadding: $count\n";
    
    if ($count > 0) {
        echo "\nSample IDs:\n";
        $CI->db->select('id');
        $CI->db->from('student_fees_depositeadding');
        $CI->db->limit(10);
        $sample = $CI->db->get()->result_array();
        foreach ($sample as $s) {
            echo "  - {$s['id']}\n";
        }
    }
    exit;
}

echo "✓ Payment 945 FOUND!\n\n";

echo "=== Basic Information ===\n";
echo "Payment ID: {$record['id']}\n";
echo "Created At: {$record['created_at']}\n\n";

echo "=== Student Information ===\n";
echo "Student ID: {$record['student_id']}\n";
echo "Name: {$record['firstname']} {$record['middlename']} {$record['lastname']}\n";
echo "Admission No: {$record['admission_no']}\n\n";

echo "=== Class/Section/Session ===\n";
echo "Class: {$record['class_name']} (ID: {$record['class_id']})\n";
echo "Section: {$record['section_name']} (ID: {$record['section_id']})\n";
echo "Session: {$record['session_name']} (ID: {$record['session_id']})\n\n";

echo "=== Fee Type ===\n";
echo "Fee Type: {$record['fee_type_name']} (ID: {$record['fee_type_id']})\n";
echo "Fee Type Code: {$record['fee_type_code']}\n";
echo "fee_groups_feetype_id: {$record['fee_groups_feetype_id']}\n\n";

echo "=== Filter Matches ===\n";
echo "Class ID matches 16? " . ($record['class_id'] == 16 ? "YES ✓" : "NO ✗ (actual: {$record['class_id']})") . "\n";
echo "Section ID matches 26? " . ($record['section_id'] == 26 ? "YES ✓" : "NO ✗ (actual: {$record['section_id']})") . "\n";
echo "Session ID matches 21? " . ($record['session_id'] == 21 ? "YES ✓" : "NO ✗ (actual: {$record['session_id']})") . "\n";
echo "Fee Type ID matches 4? " . ($record['fee_type_id'] == 4 ? "YES ✓" : "NO ✗ (actual: {$record['fee_type_id']})") . "\n\n";

echo "=== Amount Detail JSON ===\n";
echo "Raw JSON:\n";
echo $record['amount_detail'] . "\n\n";

$amount_detail = json_decode($record['amount_detail']);

if (!$amount_detail) {
    echo "✗ Failed to parse JSON!\n";
    exit;
}

echo "✓ JSON parsed successfully\n";
echo "Number of payments: " . count($amount_detail) . "\n\n";

foreach ($amount_detail as $idx => $payment) {
    echo "--- Payment #" . ($idx + 1) . " ---\n";
    
    $fields = ['date', 'amount', 'amount_discount', 'amount_fine', 'payment_mode', 'received_by', 'inv_no', 'description'];
    
    foreach ($fields as $field) {
        if (isset($payment->$field)) {
            echo "  $field: {$payment->$field}\n";
        } else {
            echo "  $field: NOT SET\n";
        }
    }
    
    // Check date range
    if (isset($payment->date)) {
        $payment_ts = strtotime($payment->date);
        $start_ts = strtotime('2025-09-01');
        $end_ts = strtotime('2025-10-11');
        
        echo "\n  Date Range Check:\n";
        echo "    Payment date: {$payment->date}\n";
        echo "    Payment timestamp: $payment_ts\n";
        echo "    Start (2025-09-01): $start_ts\n";
        echo "    End (2025-10-11): $end_ts\n";
        echo "    >= Start? " . ($payment_ts >= $start_ts ? "YES ✓" : "NO ✗") . "\n";
        echo "    <= End? " . ($payment_ts <= $end_ts ? "YES ✓" : "NO ✗") . "\n";
        echo "    In Range? " . ($payment_ts >= $start_ts && $payment_ts <= $end_ts ? "YES ✓" : "NO ✗") . "\n";
    }
    
    // Check collector
    if (isset($payment->received_by)) {
        echo "\n  Collector Check:\n";
        echo "    received_by in JSON: {$payment->received_by}\n";
        echo "    Filter value: 6\n";
        echo "    Type of received_by: " . gettype($payment->received_by) . "\n";
        echo "    Match (==)? " . ($payment->received_by == 6 ? "YES ✓" : "NO ✗") . "\n";
        echo "    Match (===)? " . ($payment->received_by === 6 ? "YES ✓" : "NO ✗") . "\n";
        echo "    Match (=== '6')? " . ($payment->received_by === '6' ? "YES ✓" : "NO ✗") . "\n";
        
        // Get staff info
        $CI->load->model('staff_model');
        $staff = $CI->staff_model->get_StaffNameById($payment->received_by);
        if ($staff) {
            echo "    Collector Name: {$staff['name']} ({$staff['employee_id']})\n";
        }
    }
    
    echo "\n";
}

echo "=== Analysis ===\n";

$all_filters_match = true;
$issues = [];

if ($record['class_id'] != 16) {
    $all_filters_match = false;
    $issues[] = "Class ID mismatch (expected 16, got {$record['class_id']})";
}

if ($record['section_id'] != 26) {
    $all_filters_match = false;
    $issues[] = "Section ID mismatch (expected 26, got {$record['section_id']})";
}

if ($record['session_id'] != 21) {
    $all_filters_match = false;
    $issues[] = "Session ID mismatch (expected 21, got {$record['session_id']})";
}

if ($record['fee_type_id'] != 4) {
    $all_filters_match = false;
    $issues[] = "Fee Type ID mismatch (expected 4, got {$record['fee_type_id']})";
}

// Check JSON filters
foreach ($amount_detail as $payment) {
    if (isset($payment->date)) {
        $payment_ts = strtotime($payment->date);
        $start_ts = strtotime('2025-09-01');
        $end_ts = strtotime('2025-10-11');
        
        if ($payment_ts < $start_ts || $payment_ts > $end_ts) {
            $all_filters_match = false;
            $issues[] = "Date out of range ({$payment->date})";
        }
    }
    
    if (isset($payment->received_by) && $payment->received_by != 6) {
        $all_filters_match = false;
        $issues[] = "Collector mismatch (expected 6, got {$payment->received_by})";
    }
}

if ($all_filters_match) {
    echo "✓ ALL FILTERS MATCH! This payment should appear in the API results.\n";
} else {
    echo "✗ Some filters don't match:\n";
    foreach ($issues as $issue) {
        echo "  - $issue\n";
    }
}

echo "\n=== Query Complete ===\n";

