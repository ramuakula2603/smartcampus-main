<?php
/**
 * Debug script to check specific payment ID 945
 * Based on your report showing:
 * Payment ID: 945/1
 * Date: 02/09/2025
 * Student: JOREPALLI LAKSHMI DEVI
 * Class: SR-BIPC
 * Fee Type: EAMCET
 * Collector: MAHA LAKSHMI SALLA (200226)
 */

header('Content-Type: text/plain; charset=utf-8');

// Load CodeIgniter
require_once(dirname(__FILE__) . '/index.php');

// Get CI instance
$CI =& get_instance();

// Load required models
$CI->load->database();
$CI->load->model('studentfeemasteradding_model');
$CI->load->model('staff_model');

echo "=== Checking Payment ID 945 ===\n\n";

// Get the specific deposit record
$CI->db->select('student_fees_depositeadding.*, feetypeadding.type, feetypeadding.id as feetype_id, students.firstname, students.lastname, students.admission_no, classes.class, sections.section, student_session.class_id, student_session.section_id, student_session.session_id');
$CI->db->from('student_fees_depositeadding');
$CI->db->join('fee_groups_feetypeadding', 'fee_groups_feetypeadding.id = student_fees_depositeadding.fee_groups_feetype_id');
$CI->db->join('feetypeadding', 'feetypeadding.id = fee_groups_feetypeadding.feetype_id');
$CI->db->join('student_fees_masteradding', 'student_fees_masteradding.id = student_fees_depositeadding.student_fees_master_id');
$CI->db->join('student_session', 'student_session.id = student_fees_masteradding.student_session_id');
$CI->db->join('students', 'students.id = student_session.student_id');
$CI->db->join('classes', 'classes.id = student_session.class_id');
$CI->db->join('sections', 'sections.id = student_session.section_id');
$CI->db->where('student_fees_depositeadding.id', 945);
$deposit = $CI->db->get()->row_array();

if (!$deposit) {
    echo "✗ Payment ID 945 NOT FOUND in database!\n";
    echo "\nChecking if any deposits exist...\n";
    $CI->db->select('id, student_fees_master_id, fee_groups_feetype_id');
    $CI->db->from('student_fees_depositeadding');
    $CI->db->limit(5);
    $sample_deposits = $CI->db->get()->result_array();
    if (!empty($sample_deposits)) {
        echo "Sample deposit IDs found:\n";
        foreach ($sample_deposits as $sd) {
            echo "  - ID: {$sd['id']}\n";
        }
    } else {
        echo "No deposits found in database!\n";
    }
    exit;
}

echo "✓ Payment Record Found!\n\n";
echo "=== Basic Information ===\n";
echo "Payment ID: {$deposit['id']}\n";
echo "Student: {$deposit['firstname']} {$deposit['lastname']}\n";
echo "Admission No: {$deposit['admission_no']}\n";
echo "Class: {$deposit['class']}\n";
echo "Section: {$deposit['section']}\n";
echo "Fee Type: {$deposit['type']}\n";
echo "Fee Type ID: {$deposit['feetype_id']}\n";
echo "Class ID: {$deposit['class_id']}\n";
echo "Section ID: {$deposit['section_id']}\n";
echo "Session ID: {$deposit['session_id']}\n\n";

echo "=== Amount Detail JSON ===\n";
echo "Raw JSON:\n";
echo $deposit['amount_detail'] . "\n\n";

// Parse the JSON
$amount_detail = json_decode($deposit['amount_detail']);
if ($amount_detail) {
    echo "Parsed Payments:\n";
    foreach ($amount_detail as $idx => $payment) {
        echo "\n--- Payment #" . ($idx + 1) . " ---\n";
        echo "Date: " . (isset($payment->date) ? $payment->date : 'N/A') . "\n";
        echo "Amount: " . (isset($payment->amount) ? $payment->amount : 'N/A') . "\n";
        echo "Discount: " . (isset($payment->amount_discount) ? $payment->amount_discount : 'N/A') . "\n";
        echo "Fine: " . (isset($payment->amount_fine) ? $payment->amount_fine : 'N/A') . "\n";
        echo "Payment Mode: " . (isset($payment->payment_mode) ? $payment->payment_mode : 'N/A') . "\n";
        echo "Received By: " . (isset($payment->received_by) ? $payment->received_by : 'N/A') . "\n";
        echo "Invoice No: " . (isset($payment->inv_no) ? $payment->inv_no : 'N/A') . "\n";
        
        // Check date range
        if (isset($payment->date)) {
            $payment_timestamp = strtotime($payment->date);
            $start_timestamp = strtotime('2025-09-01');
            $end_timestamp = strtotime('2025-10-11');
            
            echo "\nDate Check:\n";
            echo "  Payment Date: {$payment->date}\n";
            echo "  Payment Timestamp: $payment_timestamp (" . date('Y-m-d H:i:s', $payment_timestamp) . ")\n";
            echo "  Start Date: 2025-09-01\n";
            echo "  Start Timestamp: $start_timestamp (" . date('Y-m-d H:i:s', $start_timestamp) . ")\n";
            echo "  End Date: 2025-10-11\n";
            echo "  End Timestamp: $end_timestamp (" . date('Y-m-d H:i:s', $end_timestamp) . ")\n";
            echo "  In Range? " . ($payment_timestamp >= $start_timestamp && $payment_timestamp <= $end_timestamp ? "YES ✓" : "NO ✗") . "\n";
        }
        
        // Check collector
        if (isset($payment->received_by)) {
            echo "\nCollector Check:\n";
            echo "  Payment Received By: {$payment->received_by}\n";
            echo "  Filter Received By: 6\n";
            echo "  Match? " . ($payment->received_by == 6 ? "YES ✓" : "NO ✗") . "\n";
            
            // Get staff name
            $staff = $CI->staff_model->get_StaffNameById($payment->received_by);
            if ($staff) {
                echo "  Collector Name: {$staff['name']} ({$staff['employee_id']})\n";
            }
        }
    }
} else {
    echo "✗ Failed to parse amount_detail JSON!\n";
}

echo "\n\n=== Testing Model Method ===\n";

// Test with your exact parameters
$results = $CI->studentfeemasteradding_model->getFeeCollectionReport(
    '2025-09-01',
    '2025-10-11',
    '46',      // feetype_id
    '6',       // received_by
    null,      // group
    '16',      // class_id
    '26',      // section_id
    '21'       // session_id
);

echo "Results from getFeeCollectionReport():\n";
echo "Total Records: " . count($results) . "\n\n";

if (!empty($results)) {
    echo "✓ Data Found!\n";
    foreach ($results as $idx => $record) {
        echo "\n--- Record #" . ($idx + 1) . " ---\n";
        echo "Payment ID: {$record['id']}/{$record['inv_no']}\n";
        echo "Date: {$record['date']}\n";
        echo "Student: {$record['firstname']} {$record['lastname']}\n";
        echo "Amount: {$record['amount']}\n";
        echo "Fee Type: {$record['type']}\n";
    }
} else {
    echo "✗ No records returned!\n\n";
    
    // Test without filters
    echo "Testing without filters...\n";
    $results_no_filter = $CI->studentfeemasteradding_model->getFeeCollectionReport(
        '2025-09-01',
        '2025-10-11',
        null,  // no feetype_id
        null,  // no received_by
        null,  // no group
        null,  // no class_id
        null,  // no section_id
        null   // no session_id
    );
    echo "Records without filters: " . count($results_no_filter) . "\n";
    
    if (!empty($results_no_filter)) {
        echo "\nSample record:\n";
        $sample = $results_no_filter[0];
        echo "Payment ID: {$sample['id']}/{$sample['inv_no']}\n";
        echo "Date: {$sample['date']}\n";
        echo "Student: {$sample['firstname']} {$sample['lastname']}\n";
        echo "Class ID: {$sample['class_id']}\n";
        echo "Section ID: {$sample['section_id']}\n";
        echo "Fee Type: {$sample['type']}\n";
        echo "Received By: {$sample['received_by']}\n";
    }
}

echo "\n\n=== Debug Complete ===\n";

