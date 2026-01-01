<?php
/**
 * Simple test to check date range filtering
 */

header('Content-Type: text/plain; charset=utf-8');

echo "=== Date Range Test ===\n\n";

// Your search parameters
$start_date = "2025-09-01";
$end_date = "2025-10-11";

// Payment date from your report
$payment_date = "2025-09-02"; // Assuming 02/09/2025 is September 2, 2025

echo "Search Range:\n";
echo "  From: $start_date\n";
echo "  To: $end_date\n\n";

echo "Payment Date: $payment_date\n\n";

// Convert to timestamps
$st_date = strtotime($start_date);
$ed_date = strtotime($end_date);
$payment_timestamp = strtotime($payment_date);

echo "Timestamps:\n";
echo "  Start: $st_date (" . date('Y-m-d H:i:s', $st_date) . ")\n";
echo "  End: $ed_date (" . date('Y-m-d H:i:s', $ed_date) . ")\n";
echo "  Payment: $payment_timestamp (" . date('Y-m-d H:i:s', $payment_timestamp) . ")\n\n";

echo "Comparison:\n";
echo "  Payment >= Start? " . ($payment_timestamp >= $st_date ? "YES ✓" : "NO ✗") . "\n";
echo "  Payment <= End? " . ($payment_timestamp <= $ed_date ? "YES ✓" : "NO ✗") . "\n";
echo "  In Range? " . ($payment_timestamp >= $st_date && $payment_timestamp <= $ed_date ? "YES ✓" : "NO ✗") . "\n\n";

// Now let's check with end date including full day
$ed_date_full = strtotime($end_date . ' 23:59:59');
echo "With full day (23:59:59):\n";
echo "  End: $ed_date_full (" . date('Y-m-d H:i:s', $ed_date_full) . ")\n";
echo "  Payment <= End? " . ($payment_timestamp <= $ed_date_full ? "YES ✓" : "NO ✗") . "\n";
echo "  In Range? " . ($payment_timestamp >= $st_date && $payment_timestamp <= $ed_date_full ? "YES ✓" : "NO ✗") . "\n\n";

// Load CodeIgniter to test actual database
require_once(dirname(__FILE__) . '/index.php');
$CI =& get_instance();
$CI->load->database();

echo "=== Database Check ===\n\n";

// Check if payment 945 exists
$CI->db->select('id, amount_detail');
$CI->db->from('student_fees_depositeadding');
$CI->db->where('id', 945);
$deposit = $CI->db->get()->row();

if ($deposit) {
    echo "✓ Payment ID 945 found\n\n";
    echo "Amount Detail JSON:\n";
    $amount_detail = json_decode($deposit->amount_detail);
    if ($amount_detail && !empty($amount_detail)) {
        foreach ($amount_detail as $idx => $payment) {
            echo "\nPayment #" . ($idx + 1) . ":\n";
            echo "  Date: " . (isset($payment->date) ? $payment->date : 'N/A') . "\n";
            echo "  Amount: " . (isset($payment->amount) ? $payment->amount : 'N/A') . "\n";
            echo "  Received By: " . (isset($payment->received_by) ? $payment->received_by : 'N/A') . "\n";
            
            if (isset($payment->date)) {
                $pmt_ts = strtotime($payment->date);
                echo "  Timestamp: $pmt_ts (" . date('Y-m-d H:i:s', $pmt_ts) . ")\n";
                echo "  In Range (2025-09-01 to 2025-10-11)? " . 
                     ($pmt_ts >= $st_date && $pmt_ts <= $ed_date ? "YES ✓" : "NO ✗") . "\n";
            }
            
            if (isset($payment->received_by)) {
                echo "  Matches Collector 6? " . ($payment->received_by == 6 ? "YES ✓" : "NO ✗") . "\n";
            }
        }
    } else {
        echo "✗ Failed to parse amount_detail or it's empty\n";
    }
} else {
    echo "✗ Payment ID 945 NOT found\n";
}

echo "\n=== Test Complete ===\n";

