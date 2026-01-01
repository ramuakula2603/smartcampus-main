<?php
/**
 * Test script for Staff Attendance API
 * This script tests various scenarios of the attendance summary API
 */

// API Configuration
$base_url = 'http://localhost/amt/api/teacher/attendance-summary';
$headers = array(
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
);

/**
 * Make API request
 */
function makeApiRequest($url, $data, $headers) {
    $options = array(
        'http' => array(
            'header' => implode("\r\n", $headers) . "\r\n",
            'method' => 'POST',
            'content' => json_encode($data)
        )
    );

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    
    if ($result === FALSE) {
        return array('error' => 'Request failed');
    }
    
    return json_decode($result, true);
}

/**
 * Test scenarios
 */
echo "=== Staff Attendance API Test Suite ===\n\n";

// Test 1: Get attendance for specific staff member
echo "Test 1: Get attendance for specific staff member (ID: 6)\n";
echo "Request: POST /teacher/attendance-summary\n";
$test1_data = array(
    'staff_id' => 6,
    'from_date' => '2024-01-01',
    'to_date' => '2024-12-31'
);
echo "Data: " . json_encode($test1_data, JSON_PRETTY_PRINT) . "\n";

$response1 = makeApiRequest($base_url, $test1_data, $headers);
echo "Response Status: " . ($response1['status'] ?? 'Error') . "\n";
echo "Response Message: " . ($response1['message'] ?? 'No message') . "\n";

if (isset($response1['data']) && isset($response1['data']['staff_info'])) {
    $staff_info = $response1['data']['staff_info'];
    echo "Staff: " . $staff_info['name'] . " " . $staff_info['surname'] . " (ID: " . $staff_info['employee_id'] . ")\n";
    
    if (isset($response1['data']['attendance_summary'])) {
        echo "Attendance Summary:\n";
        foreach ($response1['data']['attendance_summary'] as $type => $data) {
            echo "  - $type: " . $data['count'] . " days\n";
        }
    }
}
echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 2: Get attendance for all staff (current year)
echo "Test 2: Get attendance for all staff (current year)\n";
echo "Request: POST /teacher/attendance-summary\n";
$test2_data = array();
echo "Data: " . json_encode($test2_data, JSON_PRETTY_PRINT) . "\n";

$response2 = makeApiRequest($base_url, $test2_data, $headers);
echo "Response Status: " . ($response2['status'] ?? 'Error') . "\n";
echo "Response Message: " . ($response2['message'] ?? 'No message') . "\n";

if (isset($response2['data']) && isset($response2['data']['staff_attendance_data'])) {
    echo "Total Staff: " . $response2['data']['total_staff'] . "\n";
    echo "Date Range: " . $response2['data']['date_range']['from_date'] . " to " . $response2['data']['date_range']['to_date'] . "\n";
    
    foreach ($response2['data']['staff_attendance_data'] as $index => $staff_data) {
        if ($index < 3) { // Show first 3 staff members
            $staff_info = $staff_data['staff_info'];
            echo "Staff " . ($index + 1) . ": " . $staff_info['name'] . " " . $staff_info['surname'] . "\n";
        }
    }
    if (count($response2['data']['staff_attendance_data']) > 3) {
        echo "... and " . (count($response2['data']['staff_attendance_data']) - 3) . " more staff members\n";
    }
}
echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 3: Get attendance for specific date range
echo "Test 3: Get attendance for specific date range (June 2024)\n";
echo "Request: POST /teacher/attendance-summary\n";
$test3_data = array(
    'from_date' => '2024-06-01',
    'to_date' => '2024-06-30'
);
echo "Data: " . json_encode($test3_data, JSON_PRETTY_PRINT) . "\n";

$response3 = makeApiRequest($base_url, $test3_data, $headers);
echo "Response Status: " . ($response3['status'] ?? 'Error') . "\n";
echo "Response Message: " . ($response3['message'] ?? 'No message') . "\n";

if (isset($response3['data'])) {
    if (isset($response3['data']['total_staff'])) {
        echo "Total Staff: " . $response3['data']['total_staff'] . "\n";
    }
    if (isset($response3['data']['date_range'])) {
        echo "Date Range: " . $response3['data']['date_range']['from_date'] . " to " . $response3['data']['date_range']['to_date'] . "\n";
    }
}
echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 4: Invalid staff ID
echo "Test 4: Invalid staff ID (negative number)\n";
echo "Request: POST /teacher/attendance-summary\n";
$test4_data = array(
    'staff_id' => -1
);
echo "Data: " . json_encode($test4_data, JSON_PRETTY_PRINT) . "\n";

$response4 = makeApiRequest($base_url, $test4_data, $headers);
echo "Response Status: " . ($response4['status'] ?? 'Error') . "\n";
echo "Response Message: " . ($response4['message'] ?? 'No message') . "\n";
echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 5: Invalid date format
echo "Test 5: Invalid date format\n";
echo "Request: POST /teacher/attendance-summary\n";
$test5_data = array(
    'from_date' => '2024/01/01',  // Wrong format
    'to_date' => '2024-12-31'
);
echo "Data: " . json_encode($test5_data, JSON_PRETTY_PRINT) . "\n";

$response5 = makeApiRequest($base_url, $test5_data, $headers);
echo "Response Status: " . ($response5['status'] ?? 'Error') . "\n";
echo "Response Message: " . ($response5['message'] ?? 'No message') . "\n";
echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 6: Invalid date range (from_date > to_date)
echo "Test 6: Invalid date range (from_date > to_date)\n";
echo "Request: POST /teacher/attendance-summary\n";
$test6_data = array(
    'from_date' => '2024-12-31',
    'to_date' => '2024-01-01'
);
echo "Data: " . json_encode($test6_data, JSON_PRETTY_PRINT) . "\n";

$response6 = makeApiRequest($base_url, $test6_data, $headers);
echo "Response Status: " . ($response6['status'] ?? 'Error') . "\n";
echo "Response Message: " . ($response6['message'] ?? 'No message') . "\n";
echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 7: Invalid JSON
echo "Test 7: Invalid JSON format\n";
echo "Request: POST /teacher/attendance-summary\n";
echo "Data: Invalid JSON string\n";

$options = array(
    'http' => array(
        'header' => implode("\r\n", $headers) . "\r\n",
        'method' => 'POST',
        'content' => '{"staff_id": 6, "invalid_json"}'  // Invalid JSON
    )
);

$context = stream_context_create($options);
$result = file_get_contents($base_url, false, $context);
$response7 = json_decode($result, true);

echo "Response Status: " . ($response7['status'] ?? 'Error') . "\n";
echo "Response Message: " . ($response7['message'] ?? 'No message') . "\n";
echo "\n" . str_repeat("-", 50) . "\n\n";

echo "=== Test Suite Completed ===\n";
echo "All tests have been executed. Check the responses above for API functionality.\n";
echo "Expected results:\n";
echo "- Test 1: Should return attendance data for staff ID 6\n";
echo "- Test 2: Should return attendance data for all active staff\n";
echo "- Test 3: Should return attendance data for June 2024 date range\n";
echo "- Test 4: Should return error for invalid staff ID\n";
echo "- Test 5: Should return error for invalid date format\n";
echo "- Test 6: Should return error for invalid date range\n";
echo "- Test 7: Should return error for invalid JSON\n";
?>
