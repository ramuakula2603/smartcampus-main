<?php
/**
 * COMPREHENSIVE TEST SCRIPT - RUN THIS TO IDENTIFY THE ISSUE
 * 
 * This script will:
 * 1. Check if payment 945 exists in database
 * 2. Verify all filter values
 * 3. Test each filter combination
 * 4. Identify which filter is removing payment 945
 * 5. Provide detailed analysis and fix recommendations
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Other Collection Report API - Comprehensive Test Results</title>
    <style>
        body { font-family: monospace; background: #1e1e1e; color: #d4d4d4; padding: 20px; }
        h1 { color: #4ec9b0; }
        h2 { color: #569cd6; margin-top: 30px; }
        .success { color: #4ec9b0; }
        .error { color: #f48771; }
        .warning { color: #dcdcaa; }
        .info { color: #9cdcfe; }
        .section { background: #252526; border: 1px solid #3e3e42; padding: 15px; margin: 10px 0; }
        table { border-collapse: collapse; width: 100%; margin: 10px 0; }
        th, td { border: 1px solid #3e3e42; padding: 8px; text-align: left; }
        th { background: #2d2d30; }
        .code { background: #1e1e1e; padding: 10px; border-left: 3px solid #569cd6; margin: 10px 0; }
    </style>
</head>
<body>

<h1>🧪 Other Collection Report API - Comprehensive Test Results</h1>

<?php
// Load CodeIgniter
require_once(dirname(__FILE__) . '/index.php');

$CI =& get_instance();
$CI->load->database();
$CI->load->model('studentfeemasteradding_model');
$CI->load->model('staff_model');

// Test parameters
$target_payment_id = 945;
$session_id = "21";
$class_id = "16";
$section_id = "26";
$feetype_id = "4";  // EAMCET
$received_by = "6"; // MAHA LAKSHMI SALLA
$start_date = "2025-09-01";
$end_date = "2025-10-11";

echo "<div class='section'>";
echo "<h2>📋 Test Configuration</h2>";
echo "<table>";
echo "<tr><th>Parameter</th><th>Value</th></tr>";
echo "<tr><td>Target Payment ID</td><td>$target_payment_id</td></tr>";
echo "<tr><td>Session ID</td><td>$session_id</td></tr>";
echo "<tr><td>Class ID</td><td>$class_id</td></tr>";
echo "<tr><td>Section ID</td><td>$section_id</td></tr>";
echo "<tr><td>Fee Type ID</td><td>$feetype_id (EAMCET)</td></tr>";
echo "<tr><td>Collector ID</td><td>$received_by (MAHA LAKSHMI SALLA)</td></tr>";
echo "<tr><td>Date Range</td><td>$start_date to $end_date</td></tr>";
echo "</table>";
echo "</div>";

// STEP 1: Check if payment exists
echo "<div class='section'>";
echo "<h2>STEP 1: Database Check - Payment $target_payment_id</h2>";

$sql = "
SELECT 
    sfd.id,
    sfd.amount_detail,
    fgft.feetype_id,
    ft.type as fee_type_name,
    ss.class_id,
    ss.section_id,
    ss.session_id,
    s.firstname,
    s.lastname,
    s.admission_no,
    c.class as class_name,
    sec.section as section_name
FROM student_fees_depositeadding sfd
JOIN fee_groups_feetypeadding fgft ON fgft.id = sfd.fee_groups_feetype_id
JOIN feetypeadding ft ON ft.id = fgft.feetype_id
JOIN student_fees_masteradding sfm ON sfm.id = sfd.student_fees_master_id
JOIN student_session ss ON ss.id = sfm.student_session_id
JOIN students s ON s.id = ss.student_id
JOIN classes c ON c.id = ss.class_id
JOIN sections sec ON sec.id = ss.section_id
WHERE sfd.id = $target_payment_id
";

$query = $CI->db->query($sql);
$record = $query->row_array();

if (!$record) {
    echo "<p class='error'>✗ Payment $target_payment_id NOT FOUND in database!</p>";
    echo "<p>The payment doesn't exist. Please verify the payment ID.</p>";
    echo "</div></body></html>";
    exit;
}

echo "<p class='success'>✓ Payment $target_payment_id FOUND!</p>";
echo "<table>";
echo "<tr><th>Field</th><th>Value</th><th>Expected</th><th>Match?</th></tr>";
echo "<tr><td>Student</td><td>{$record['firstname']} {$record['lastname']}</td><td>JOREPALLI LAKSHMI DEVI</td><td>-</td></tr>";
echo "<tr><td>Admission No</td><td>{$record['admission_no']}</td><td>2023412</td><td>-</td></tr>";
echo "<tr><td>Class</td><td>{$record['class_name']} (ID: {$record['class_id']})</td><td>SR-BIPC (ID: 16)</td><td>" . ($record['class_id'] == 16 ? "<span class='success'>✓</span>" : "<span class='error'>✗</span>") . "</td></tr>";
echo "<tr><td>Section</td><td>{$record['section_name']} (ID: {$record['section_id']})</td><td>ID: 26</td><td>" . ($record['section_id'] == 26 ? "<span class='success'>✓</span>" : "<span class='error'>✗</span>") . "</td></tr>";
echo "<tr><td>Session ID</td><td>{$record['session_id']}</td><td>21</td><td>" . ($record['session_id'] == 21 ? "<span class='success'>✓</span>" : "<span class='error'>✗</span>") . "</td></tr>";
echo "<tr><td>Fee Type</td><td>{$record['fee_type_name']} (ID: {$record['feetype_id']})</td><td>EAMCET (ID: 4)</td><td>" . ($record['feetype_id'] == 4 ? "<span class='success'>✓</span>" : "<span class='error'>✗</span>") . "</td></tr>";
echo "</table>";

// Parse amount_detail JSON
$amount_detail = json_decode($record['amount_detail']);
if (!$amount_detail) {
    echo "<p class='error'>✗ Failed to parse amount_detail JSON!</p>";
    echo "</div></body></html>";
    exit;
}

echo "<h3>Amount Detail JSON Analysis</h3>";
echo "<p>Number of payments in JSON: " . count($amount_detail) . "</p>";

foreach ($amount_detail as $idx => $payment) {
    echo "<h4>Payment #" . ($idx + 1) . "</h4>";
    echo "<table>";
    echo "<tr><th>Field</th><th>Value</th><th>Check</th></tr>";
    
    if (isset($payment->date)) {
        $payment_ts = strtotime($payment->date);
        $start_ts = strtotime($start_date);
        $end_ts = strtotime($end_date);
        $in_range = ($payment_ts >= $start_ts && $payment_ts <= $end_ts);
        
        echo "<tr><td>Date</td><td>{$payment->date}</td><td>";
        echo "Timestamp: $payment_ts<br>";
        echo "Start: $start_ts ($start_date)<br>";
        echo "End: $end_ts ($end_date)<br>";
        echo ($in_range ? "<span class='success'>✓ In Range</span>" : "<span class='error'>✗ Out of Range</span>");
        echo "</td></tr>";
    } else {
        echo "<tr><td>Date</td><td class='error'>NOT SET</td><td>✗</td></tr>";
    }
    
    if (isset($payment->amount)) {
        echo "<tr><td>Amount</td><td>{$payment->amount}</td><td>-</td></tr>";
    }
    
    if (isset($payment->received_by)) {
        $match = ($payment->received_by == $received_by);
        echo "<tr><td>Received By</td><td>{$payment->received_by}</td><td>";
        echo "Expected: $received_by<br>";
        echo "Type: " . gettype($payment->received_by) . "<br>";
        echo ($match ? "<span class='success'>✓ Match</span>" : "<span class='error'>✗ No Match</span>");
        echo "</td></tr>";
        
        // Get staff name
        $staff = $CI->staff_model->get_StaffNameById($payment->received_by);
        if ($staff) {
            echo "<tr><td>Collector Name</td><td>{$staff['name']} ({$staff['employee_id']})</td><td>-</td></tr>";
        }
    } else {
        echo "<tr><td>Received By</td><td class='error'>NOT SET</td><td>✗</td></tr>";
    }
    
    if (isset($payment->inv_no)) {
        echo "<tr><td>Invoice No</td><td>{$payment->inv_no}</td><td>-</td></tr>";
    }
    
    echo "</table>";
}

echo "</div>";

// STEP 2: Test filter combinations
echo "<div class='section'>";
echo "<h2>STEP 2: Filter Combination Tests</h2>";
echo "<p>Testing incrementally to identify which filter removes payment $target_payment_id</p>";

$tests = [
    ['name' => 'Test 1: Date Range Only', 'params' => [$start_date, $end_date, null, null, null, null, null, null]],
    ['name' => 'Test 2: + Fee Type', 'params' => [$start_date, $end_date, $feetype_id, null, null, null, null, null]],
    ['name' => 'Test 3: + Class & Section', 'params' => [$start_date, $end_date, $feetype_id, null, null, $class_id, $section_id, null]],
    ['name' => 'Test 4: + Session', 'params' => [$start_date, $end_date, $feetype_id, null, null, $class_id, $section_id, $session_id]],
    ['name' => 'Test 5: + Collector', 'params' => [$start_date, $end_date, $feetype_id, $received_by, null, $class_id, $section_id, $session_id]],
];

echo "<table>";
echo "<tr><th>Test</th><th>Total Records</th><th>Payment $target_payment_id Found?</th><th>Status</th></tr>";

$first_failure = null;

foreach ($tests as $test) {
    $results = $CI->studentfeemasteradding_model->getFeeCollectionReport(...$test['params']);
    $found = false;
    
    foreach ($results as $r) {
        if ($r['id'] == $target_payment_id) {
            $found = true;
            break;
        }
    }
    
    $status_class = $found ? 'success' : 'error';
    $status_icon = $found ? '✓' : '✗';
    
    echo "<tr>";
    echo "<td>{$test['name']}</td>";
    echo "<td>" . count($results) . "</td>";
    echo "<td class='$status_class'>$status_icon " . ($found ? 'YES' : 'NO') . "</td>";
    echo "<td class='$status_class'>" . ($found ? 'PASS' : 'FAIL') . "</td>";
    echo "</tr>";
    
    if (!$found && $first_failure === null) {
        $first_failure = $test['name'];
    }
}

echo "</table>";

if ($first_failure) {
    echo "<div class='code'>";
    echo "<p class='error'><strong>⚠️ ISSUE IDENTIFIED:</strong></p>";
    echo "<p>Payment $target_payment_id first disappears at: <strong>$first_failure</strong></p>";
    echo "<p>This indicates the filter added in this test is removing the payment.</p>";
    echo "</div>";
}

echo "</div>";

// STEP 3: Root Cause Analysis
echo "<div class='section'>";
echo "<h2>STEP 3: Root Cause Analysis</h2>";

$issues = [];

// Check database filters
if ($record['class_id'] != $class_id) {
    $issues[] = "Class ID mismatch: Database has {$record['class_id']}, filter expects $class_id";
}
if ($record['section_id'] != $section_id) {
    $issues[] = "Section ID mismatch: Database has {$record['section_id']}, filter expects $section_id";
}
if ($record['session_id'] != $session_id) {
    $issues[] = "Session ID mismatch: Database has {$record['session_id']}, filter expects $session_id";
}
if ($record['feetype_id'] != $feetype_id) {
    $issues[] = "Fee Type ID mismatch: Database has {$record['feetype_id']}, filter expects $feetype_id";
}

// Check JSON filters
foreach ($amount_detail as $payment) {
    if (isset($payment->date)) {
        $payment_ts = strtotime($payment->date);
        $start_ts = strtotime($start_date);
        $end_ts = strtotime($end_date);
        
        if ($payment_ts < $start_ts || $payment_ts > $end_ts) {
            $issues[] = "Date out of range: Payment date {$payment->date} is outside $start_date to $end_date";
        }
    } else {
        $issues[] = "Date not set in JSON";
    }
    
    if (isset($payment->received_by)) {
        if ($payment->received_by != $received_by) {
            $issues[] = "Collector mismatch: JSON has {$payment->received_by}, filter expects $received_by";
        }
    } else {
        $issues[] = "Received_by not set in JSON";
    }
}

if (empty($issues)) {
    echo "<p class='success'>✓ ALL FILTERS MATCH! Payment should appear in results.</p>";
    echo "<p class='info'>If the API still doesn't return this payment, there may be an issue with:</p>";
    echo "<ul>";
    echo "<li>API controller parameter mapping</li>";
    echo "<li>Model method call</li>";
    echo "<li>Response formatting</li>";
    echo "</ul>";
} else {
    echo "<p class='error'>✗ Issues Found:</p>";
    echo "<ul>";
    foreach ($issues as $issue) {
        echo "<li class='error'>$issue</li>";
    }
    echo "</ul>";
    
    echo "<div class='code'>";
    echo "<p><strong>Recommended Actions:</strong></p>";
    echo "<ol>";
    foreach ($issues as $issue) {
        if (strpos($issue, 'Class ID') !== false || strpos($issue, 'Section ID') !== false || 
            strpos($issue, 'Session ID') !== false || strpos($issue, 'Fee Type ID') !== false) {
            echo "<li>Update your filter values to match the actual database values, OR update the database to match expected values</li>";
        } elseif (strpos($issue, 'Date') !== false) {
            echo "<li>Check the date format in amount_detail JSON and ensure it's parseable by strtotime()</li>";
        } elseif (strpos($issue, 'Collector') !== false) {
            echo "<li>Verify the received_by value in amount_detail JSON matches the filter value (check data type: string vs integer)</li>";
        }
    }
    echo "</ol>";
    echo "</div>";
}

echo "</div>";

echo "<div class='section'>";
echo "<h2>✅ Test Complete</h2>";
echo "<p>Review the results above to identify and fix the issue.</p>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ol>";
echo "<li>If all filters match but payment still doesn't appear, test the API endpoint directly</li>";
echo "<li>Compare API response with web interface response using same filters</li>";
echo "<li>Check API controller logs for any errors</li>";
echo "</ol>";
echo "</div>";

?>

</body>
</html>

