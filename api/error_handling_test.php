<?php
/**
 * Error Handling Test for Staff Attendance API
 * Test various error scenarios and edge cases
 */

echo "=== ERROR HANDLING & EDGE CASES TEST ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

// Test configuration
$base_url = 'http://localhost/amt/api';
$auth_headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

/**
 * Execute API call and return full response details
 */
function testAPICall($test_name, $endpoint, $data, $authenticated = true, $expected_status = null) {
    global $base_url, $auth_headers;
    
    echo "🧪 TEST: $test_name\n";
    echo "Endpoint: $endpoint\n";
    echo "Data: " . json_encode($data) . "\n";
    echo "Authenticated: " . ($authenticated ? 'Yes' : 'No') . "\n";
    
    $url = "$base_url/$endpoint";
    $headers = $authenticated ? $auth_headers : ['Content-Type: application/json'];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    echo "HTTP Status: $http_code\n";
    
    if ($error) {
        echo "❌ CURL Error: $error\n";
        return false;
    }
    
    if ($expected_status && $http_code == $expected_status) {
        echo "✅ Expected status code received\n";
    } elseif ($expected_status && $http_code != $expected_status) {
        echo "❌ Unexpected status code (expected $expected_status)\n";
    }
    
    $json_data = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "Response: " . json_encode($json_data, JSON_PRETTY_PRINT) . "\n";
        
        if (isset($json_data['status']) && isset($json_data['message'])) {
            echo "✅ Proper error response structure\n";
        } else {
            echo "⚠️  Non-standard response structure\n";
        }
    } else {
        echo "❌ Invalid JSON response\n";
        echo "Raw response: " . substr($response, 0, 200) . "...\n";
    }
    
    echo str_repeat("-", 50) . "\n\n";
    return true;
}

echo "🚨 TESTING ERROR SCENARIOS\n";
echo "==========================\n\n";

// Test 1: Invalid Staff ID
testAPICall(
    "Invalid Staff ID (99999)",
    "teacher/attendance-summary",
    ['staff_id' => 99999],
    true,
    400
);

// Test 2: Non-existent Staff ID
testAPICall(
    "Non-existent Staff ID (0)",
    "teacher/attendance-summary",
    ['staff_id' => 0],
    true,
    400
);

// Test 3: Invalid date format
testAPICall(
    "Invalid Date Format",
    "teacher/attendance-summary",
    ['from_date' => '2024-13-01', 'to_date' => '2024-08-31'],
    true,
    400
);

// Test 4: Invalid date format 2
testAPICall(
    "Invalid Date Format 2",
    "teacher/attendance-summary",
    ['from_date' => '2024/08/01', 'to_date' => '2024/08/31'],
    true,
    400
);

// Test 5: Future dates
testAPICall(
    "Future Date Range",
    "teacher/attendance-summary",
    ['from_date' => '2030-01-01', 'to_date' => '2030-12-31'],
    true,
    200
);

// Test 6: Reversed date range
testAPICall(
    "Reversed Date Range (to_date before from_date)",
    "teacher/attendance-summary",
    ['from_date' => '2024-08-31', 'to_date' => '2024-08-01'],
    true,
    400
);

// Test 7: Missing authentication
testAPICall(
    "Missing Authentication Headers",
    "teacher/attendance-summary",
    ['staff_id' => 6],
    false,
    401
);

// Test 8: Wrong authentication
echo "🧪 TEST: Wrong Authentication Headers\n";
echo "Endpoint: teacher/attendance-summary\n";
echo "Data: " . json_encode(['staff_id' => 6]) . "\n";

$wrong_headers = [
    'Content-Type: application/json',
    'Client-Service: wrongservice',
    'Auth-Key: wrongkey'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$base_url/teacher/attendance-summary");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['staff_id' => 6]));
curl_setopt($ch, CURLOPT_HTTPHEADER, $wrong_headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLOPT_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: $http_code\n";
$json_data = json_decode($response, true);
if ($json_data) {
    echo "Response: " . json_encode($json_data, JSON_PRETTY_PRINT) . "\n";
}
echo str_repeat("-", 50) . "\n\n";

// Test 9: Invalid JSON payload
echo "🧪 TEST: Invalid JSON Payload\n";
echo "Endpoint: teacher/attendance-summary\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$base_url/teacher/attendance-summary");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, '{invalid json}');
curl_setopt($ch, CURLOPT_HTTPHEADER, $auth_headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: $http_code\n";
$json_data = json_decode($response, true);
if ($json_data) {
    echo "Response: " . json_encode($json_data, JSON_PRETTY_PRINT) . "\n";
}
echo str_repeat("-", 50) . "\n\n";

// Test 10: GET method instead of POST
echo "🧪 TEST: Wrong HTTP Method (GET instead of POST)\n";
echo "Endpoint: teacher/attendance-summary\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$base_url/teacher/attendance-summary?staff_id=6");
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $auth_headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: $http_code\n";
$json_data = json_decode($response, true);
if ($json_data) {
    echo "Response: " . json_encode($json_data, JSON_PRETTY_PRINT) . "\n";
}
echo str_repeat("-", 50) . "\n\n";

// Test 11: Empty payload
testAPICall(
    "Empty JSON Payload",
    "teacher/attendance-summary",
    [],
    true,
    200
);

// Test 12: Null values
testAPICall(
    "Null Values in Payload",
    "teacher/attendance-summary",
    ['staff_id' => null, 'from_date' => null, 'to_date' => null],
    true,
    200
);

echo "📊 EDGE CASE TESTS\n";
echo "==================\n\n";

// Test 13: Very old date range
testAPICall(
    "Very Old Date Range (2020)",
    "teacher/attendance-summary",
    ['from_date' => '2020-01-01', 'to_date' => '2020-12-31'],
    true,
    200
);

// Test 14: Single day range
testAPICall(
    "Single Day Range",
    "teacher/attendance-summary",
    ['staff_id' => 6, 'from_date' => '2024-08-15', 'to_date' => '2024-08-15'],
    true,
    200
);

// Test 15: Large staff ID
testAPICall(
    "Large Staff ID",
    "teacher/attendance-summary",
    ['staff_id' => 999999999],
    true,
    400
);

echo "=== ERROR HANDLING TEST COMPLETED ===\n";
?>
