<?php
/**
 * Comprehensive Debug Script for Other Collection Report API
 * Tests each filter incrementally to identify issues
 */

header('Content-Type: text/plain; charset=utf-8');

// Load CodeIgniter
require_once(dirname(__FILE__) . '/index.php');

$CI =& get_instance();
$CI->load->database();
$CI->load->model('studentfeemasteradding_model');
$CI->load->model('staff_model');

echo "=== COMPREHENSIVE OTHER COLLECTION REPORT DEBUG ===\n\n";
echo "Target Payment: ID 945/1, JOREPALLI LAKSHMI DEVI, EAMCET, 3000.00\n";
echo "Date: 02/09/2025 (September 2, 2025)\n\n";

// Test parameters
$session_id = "21";
$class_id = "16";
$section_id = "26";
$feetype_id = "4";  // CORRECTED: EAMCET = 4
$received_by = "6"; // MAHA LAKSHMI SALLA
$start_date = "2025-09-01";
$end_date = "2025-10-11";

echo "=== STEP 1: Check if Payment 945 Exists ===\n";
$CI->db->select('id, student_fees_master_id, fee_groups_feetype_id, amount_detail');
$CI->db->from('student_fees_depositeadding');
$CI->db->where('id', 945);
$deposit = $CI->db->get()->row();

if (!$deposit) {
    echo "✗ Payment ID 945 NOT FOUND!\n";
    exit;
}
echo "✓ Payment ID 945 found\n";
echo "  student_fees_master_id: {$deposit->student_fees_master_id}\n";
echo "  fee_groups_feetype_id: {$deposit->fee_groups_feetype_id}\n\n";

// Parse amount_detail
$amount_detail = json_decode($deposit->amount_detail);
if (!$amount_detail) {
    echo "✗ Failed to parse amount_detail JSON!\n";
    exit;
}
echo "✓ amount_detail parsed successfully\n";
echo "  Number of payments in JSON: " . count($amount_detail) . "\n\n";

foreach ($amount_detail as $idx => $payment) {
    echo "  Payment #" . ($idx + 1) . ":\n";
    echo "    Date: " . (isset($payment->date) ? $payment->date : 'N/A') . "\n";
    echo "    Amount: " . (isset($payment->amount) ? $payment->amount : 'N/A') . "\n";
    echo "    Received By: " . (isset($payment->received_by) ? $payment->received_by : 'N/A') . "\n";
    echo "    Invoice: " . (isset($payment->inv_no) ? $payment->inv_no : 'N/A') . "\n\n";
}

echo "=== STEP 2: Check Fee Type Mapping ===\n";
$CI->db->select('fee_groups_feetypeadding.*, feetypeadding.type, feetypeadding.id as feetype_id');
$CI->db->from('fee_groups_feetypeadding');
$CI->db->join('feetypeadding', 'feetypeadding.id = fee_groups_feetypeadding.feetype_id');
$CI->db->where('fee_groups_feetypeadding.id', $deposit->fee_groups_feetype_id);
$fee_mapping = $CI->db->get()->row();

if ($fee_mapping) {
    echo "✓ Fee type mapping found\n";
    echo "  fee_groups_feetypeadding.id: {$fee_mapping->id}\n";
    echo "  feetypeadding.id: {$fee_mapping->feetype_id}\n";
    echo "  Fee Type: {$fee_mapping->type}\n";
    echo "  Match filter (feetype_id=4)? " . ($fee_mapping->feetype_id == 4 ? "YES ✓" : "NO ✗") . "\n\n";
} else {
    echo "✗ Fee type mapping NOT found!\n\n";
}

echo "=== STEP 3: Check Student Session Info ===\n";
$CI->db->select('student_fees_masteradding.*, student_session.class_id, student_session.section_id, student_session.session_id, students.firstname, students.lastname, students.admission_no, classes.class, sections.section');
$CI->db->from('student_fees_masteradding');
$CI->db->join('student_session', 'student_session.id = student_fees_masteradding.student_session_id');
$CI->db->join('students', 'students.id = student_session.student_id');
$CI->db->join('classes', 'classes.id = student_session.class_id');
$CI->db->join('sections', 'sections.id = student_session.section_id');
$CI->db->where('student_fees_masteradding.id', $deposit->student_fees_master_id);
$student_info = $CI->db->get()->row();

if ($student_info) {
    echo "✓ Student info found\n";
    echo "  Student: {$student_info->firstname} {$student_info->lastname}\n";
    echo "  Admission No: {$student_info->admission_no}\n";
    echo "  Class: {$student_info->class} (ID: {$student_info->class_id})\n";
    echo "  Section: {$student_info->section} (ID: {$student_info->section_id})\n";
    echo "  Session ID: {$student_info->session_id}\n\n";
    
    echo "  Filter Matches:\n";
    echo "    Class ID (16): " . ($student_info->class_id == 16 ? "YES ✓" : "NO ✗ (actual: {$student_info->class_id})") . "\n";
    echo "    Section ID (26): " . ($student_info->section_id == 26 ? "YES ✓" : "NO ✗ (actual: {$student_info->section_id})") . "\n";
    echo "    Session ID (21): " . ($student_info->session_id == 21 ? "YES ✓" : "NO ✗ (actual: {$student_info->session_id})") . "\n\n";
} else {
    echo "✗ Student info NOT found!\n\n";
}

echo "=== STEP 4: Test Model Method with Different Filter Combinations ===\n\n";

// Test 1: No filters (only date range)
echo "TEST 1: Only Date Range\n";
$results1 = $CI->studentfeemasteradding_model->getFeeCollectionReport(
    $start_date, $end_date, null, null, null, null, null, null
);
echo "  Results: " . count($results1) . " records\n";
$found1 = false;
foreach ($results1 as $r) {
    if ($r['id'] == 945) {
        $found1 = true;
        echo "  ✓ Payment 945 FOUND in results!\n";
        break;
    }
}
if (!$found1 && count($results1) > 0) {
    echo "  ✗ Payment 945 NOT in results\n";
    echo "  Sample IDs: ";
    foreach (array_slice($results1, 0, 5) as $r) {
        echo $r['id'] . ", ";
    }
    echo "\n";
}
echo "\n";

// Test 2: With fee type filter only
echo "TEST 2: Date Range + Fee Type (4)\n";
$results2 = $CI->studentfeemasteradding_model->getFeeCollectionReport(
    $start_date, $end_date, $feetype_id, null, null, null, null, null
);
echo "  Results: " . count($results2) . " records\n";
$found2 = false;
foreach ($results2 as $r) {
    if ($r['id'] == 945) {
        $found2 = true;
        echo "  ✓ Payment 945 FOUND in results!\n";
        break;
    }
}
if (!$found2 && count($results2) > 0) {
    echo "  ✗ Payment 945 NOT in results\n";
}
echo "\n";

// Test 3: With class and section filters
echo "TEST 3: Date Range + Fee Type + Class (16) + Section (26)\n";
$results3 = $CI->studentfeemasteradding_model->getFeeCollectionReport(
    $start_date, $end_date, $feetype_id, null, null, $class_id, $section_id, null
);
echo "  Results: " . count($results3) . " records\n";
$found3 = false;
foreach ($results3 as $r) {
    if ($r['id'] == 945) {
        $found3 = true;
        echo "  ✓ Payment 945 FOUND in results!\n";
        break;
    }
}
if (!$found3 && count($results3) > 0) {
    echo "  ✗ Payment 945 NOT in results\n";
}
echo "\n";

// Test 4: With session filter
echo "TEST 4: Date Range + Fee Type + Class + Section + Session (21)\n";
$results4 = $CI->studentfeemasteradding_model->getFeeCollectionReport(
    $start_date, $end_date, $feetype_id, null, null, $class_id, $section_id, $session_id
);
echo "  Results: " . count($results4) . " records\n";
$found4 = false;
foreach ($results4 as $r) {
    if ($r['id'] == 945) {
        $found4 = true;
        echo "  ✓ Payment 945 FOUND in results!\n";
        echo "  Payment Details:\n";
        echo "    Date: {$r['date']}\n";
        echo "    Student: {$r['firstname']} {$r['lastname']}\n";
        echo "    Amount: {$r['amount']}\n";
        echo "    Fee Type: {$r['type']}\n";
        echo "    Received By: {$r['received_by']}\n";
        break;
    }
}
if (!$found4 && count($results4) > 0) {
    echo "  ✗ Payment 945 NOT in results\n";
}
echo "\n";

// Test 5: With collector filter
echo "TEST 5: All Filters Including Collector (6)\n";
$results5 = $CI->studentfeemasteradding_model->getFeeCollectionReport(
    $start_date, $end_date, $feetype_id, $received_by, null, $class_id, $section_id, $session_id
);
echo "  Results: " . count($results5) . " records\n";
$found5 = false;
foreach ($results5 as $r) {
    if ($r['id'] == 945) {
        $found5 = true;
        echo "  ✓ Payment 945 FOUND in results!\n";
        echo "  Payment Details:\n";
        echo "    Date: {$r['date']}\n";
        echo "    Student: {$r['firstname']} {$r['lastname']}\n";
        echo "    Amount: {$r['amount']}\n";
        echo "    Fee Type: {$r['type']}\n";
        echo "    Received By: {$r['received_by']}\n";
        if (isset($r['received_byname'])) {
            echo "    Collector: {$r['received_byname']['name']} ({$r['received_byname']['employee_id']})\n";
        }
        break;
    }
}
if (!$found5 && count($results5) > 0) {
    echo "  ✗ Payment 945 NOT in results\n";
    echo "  This means the collector filter is removing it!\n";
    echo "  Check if received_by in JSON matches filter value (6)\n";
}
echo "\n";

echo "=== STEP 5: Summary ===\n";
echo "Payment 945 found in:\n";
echo "  Test 1 (Date only): " . ($found1 ? "YES ✓" : "NO ✗") . "\n";
echo "  Test 2 (+ Fee Type): " . ($found2 ? "YES ✓" : "NO ✗") . "\n";
echo "  Test 3 (+ Class/Section): " . ($found3 ? "YES ✓" : "NO ✗") . "\n";
echo "  Test 4 (+ Session): " . ($found4 ? "YES ✓" : "NO ✗") . "\n";
echo "  Test 5 (+ Collector): " . ($found5 ? "YES ✓" : "NO ✗") . "\n\n";

if (!$found5 && $found4) {
    echo "⚠️ ISSUE IDENTIFIED: Collector filter is removing the payment!\n";
    echo "The payment exists and matches all other filters, but the collector filter fails.\n";
    echo "Check the received_by value in the amount_detail JSON.\n";
}

echo "\n=== DEBUG COMPLETE ===\n";

