<?php
/**
 * Debug script to test Other Collection Report API with specific parameters
 * 
 * Test with your parameters:
 * {
 *     "session_id": "21",
 *     "class_id": "16",
 *     "section_id": "26",
 *     "feetype_id": "46",
 *     "collect_by_id": "6",
 *     "search_type": "all",
 *     "from_date": "2025-09-01",
 *     "to_date": "2025-10-11"
 * }
 */

// Set headers
header('Content-Type: application/json');

// Load CodeIgniter
require_once(dirname(__FILE__) . '/index.php');

// Get CI instance
$CI =& get_instance();

// Load required models
$CI->load->database();
$CI->load->model('studentfeemasteradding_model');
$CI->load->model('staff_model');

// Your test parameters
$session_id = "21";
$class_id = "16";
$section_id = "26";
$feetype_id = "46";
$received_by = "6"; // collect_by_id
$start_date = "2024-09-01"; // Changed to 2024 (past date)
$end_date = "2024-10-11";   // Changed to 2024 (past date)

echo "=== Testing Other Collection Report ===\n\n";
echo "Parameters:\n";
echo "- Session ID: $session_id\n";
echo "- Class ID: $class_id\n";
echo "- Section ID: $section_id\n";
echo "- Fee Type ID: $feetype_id\n";
echo "- Received By: $received_by\n";
echo "- Date Range: $start_date to $end_date\n\n";

// Test 1: Check if fee type exists
echo "=== Test 1: Check Fee Type ===\n";
$CI->db->select('*');
$CI->db->from('feetypeadding');
$CI->db->where('id', $feetype_id);
$feetype_result = $CI->db->get()->row_array();
if ($feetype_result) {
    echo "✓ Fee Type Found: " . $feetype_result['type'] . " (ID: " . $feetype_result['id'] . ")\n\n";
} else {
    echo "✗ Fee Type NOT Found with ID: $feetype_id\n";
    echo "Available Fee Types:\n";
    $all_feetypes = $CI->db->get('feetypeadding')->result_array();
    foreach ($all_feetypes as $ft) {
        echo "  - ID: {$ft['id']}, Type: {$ft['type']}\n";
    }
    echo "\n";
}

// Test 2: Check if class exists
echo "=== Test 2: Check Class ===\n";
$CI->db->select('*');
$CI->db->from('classes');
$CI->db->where('id', $class_id);
$class_result = $CI->db->get()->row_array();
if ($class_result) {
    echo "✓ Class Found: " . $class_result['class'] . " (ID: " . $class_result['id'] . ")\n\n";
} else {
    echo "✗ Class NOT Found with ID: $class_id\n\n";
}

// Test 3: Check if section exists
echo "=== Test 3: Check Section ===\n";
$CI->db->select('*');
$CI->db->from('sections');
$CI->db->where('id', $section_id);
$section_result = $CI->db->get()->row_array();
if ($section_result) {
    echo "✓ Section Found: " . $section_result['section'] . " (ID: " . $section_result['id'] . ")\n\n";
} else {
    echo "✗ Section NOT Found with ID: $section_id\n\n";
}

// Test 4: Check if session exists
echo "=== Test 4: Check Session ===\n";
$CI->db->select('*');
$CI->db->from('sessions');
$CI->db->where('id', $session_id);
$session_result = $CI->db->get()->row_array();
if ($session_result) {
    echo "✓ Session Found: " . $session_result['session'] . " (ID: " . $session_result['id'] . ")\n\n";
} else {
    echo "✗ Session NOT Found with ID: $session_id\n\n";
}

// Test 5: Check if staff/collector exists
echo "=== Test 5: Check Collector ===\n";
$CI->db->select('*');
$CI->db->from('staff');
$CI->db->where('id', $received_by);
$staff_result = $CI->db->get()->row_array();
if ($staff_result) {
    echo "✓ Collector Found: " . $staff_result['name'] . " (ID: " . $staff_result['id'] . ")\n\n";
} else {
    echo "✗ Collector NOT Found with ID: $received_by\n\n";
}

// Test 6: Check raw deposits without filters
echo "=== Test 6: Check Raw Deposits (No Filters) ===\n";
$CI->db->select('COUNT(*) as total');
$CI->db->from('student_fees_depositeadding');
$total_deposits = $CI->db->get()->row()->total;
echo "Total deposits in database: $total_deposits\n\n";

// Test 7: Check deposits with class filter
echo "=== Test 7: Check Deposits with Class Filter ===\n";
$CI->db->select('COUNT(*) as total');
$CI->db->from('student_fees_depositeadding');
$CI->db->join('student_fees_masteradding', 'student_fees_masteradding.id = student_fees_depositeadding.student_fees_master_id');
$CI->db->join('student_session', 'student_session.id = student_fees_masteradding.student_session_id');
$CI->db->where('student_session.class_id', $class_id);
$CI->db->where('student_session.section_id', $section_id);
$class_deposits = $CI->db->get()->row()->total;
echo "Deposits for Class ID $class_id, Section ID $section_id: $class_deposits\n\n";

// Test 8: Check deposits with fee type filter
echo "=== Test 8: Check Deposits with Fee Type Filter ===\n";
$CI->db->select('COUNT(*) as total');
$CI->db->from('student_fees_depositeadding');
$CI->db->join('fee_groups_feetypeadding', 'fee_groups_feetypeadding.id = student_fees_depositeadding.fee_groups_feetype_id');
$CI->db->where('fee_groups_feetypeadding.feetype_id', $feetype_id);
$feetype_deposits = $CI->db->get()->row()->total;
echo "Deposits for Fee Type ID $feetype_id: $feetype_deposits\n\n";

// Test 9: Check deposits with all filters (before date filtering)
echo "=== Test 9: Check Deposits with All Filters (Before Date) ===\n";
$CI->db->select('student_fees_depositeadding.*, feetypeadding.type');
$CI->db->from('student_fees_depositeadding');
$CI->db->join('fee_groups_feetypeadding', 'fee_groups_feetypeadding.id = student_fees_depositeadding.fee_groups_feetype_id');
$CI->db->join('feetypeadding', 'feetypeadding.id = fee_groups_feetypeadding.feetype_id');
$CI->db->join('student_fees_masteradding', 'student_fees_masteradding.id = student_fees_depositeadding.student_fees_master_id');
$CI->db->join('student_session', 'student_session.id = student_fees_masteradding.student_session_id');
$CI->db->where('student_session.class_id', $class_id);
$CI->db->where('student_session.section_id', $section_id);
$CI->db->where('student_session.session_id', $session_id);
$CI->db->where('fee_groups_feetypeadding.feetype_id', $feetype_id);
$CI->db->limit(5);
$all_filters_deposits = $CI->db->get()->result_array();
echo "Sample deposits with all filters (first 5):\n";
if (!empty($all_filters_deposits)) {
    foreach ($all_filters_deposits as $deposit) {
        echo "  - ID: {$deposit['id']}, Fee Type: {$deposit['type']}, Amount Detail: " . substr($deposit['amount_detail'], 0, 100) . "...\n";
    }
} else {
    echo "  No deposits found with these filters!\n";
}
echo "\n";

// Test 10: Call the actual model method
echo "=== Test 10: Call Model Method ===\n";
$results = $CI->studentfeemasteradding_model->getFeeCollectionReport(
    $start_date,
    $end_date,
    $feetype_id,
    $received_by,
    null, // group
    $class_id,
    $section_id,
    $session_id
);

echo "Results from getFeeCollectionReport(): " . count($results) . " records\n";
if (!empty($results)) {
    echo "\nFirst 3 records:\n";
    foreach (array_slice($results, 0, 3) as $record) {
        echo "  - Payment ID: {$record['id']}/{$record['inv_no']}\n";
        echo "    Date: {$record['date']}\n";
        echo "    Student: {$record['firstname']} {$record['lastname']}\n";
        echo "    Amount: {$record['amount']}\n";
        echo "    Fee Type: {$record['type']}\n\n";
    }
} else {
    echo "\n✗ No records returned!\n";
    echo "\nPossible reasons:\n";
    echo "1. No payments in the date range ($start_date to $end_date)\n";
    echo "2. The amount_detail JSON doesn't contain payments from collector ID $received_by\n";
    echo "3. The amount_detail JSON doesn't contain payments in the date range\n";
}

echo "\n=== Debug Complete ===\n";

