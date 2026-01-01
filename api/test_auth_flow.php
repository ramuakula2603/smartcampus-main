<?php
/**
 * Test the exact authentication flow to identify the issue
 */

// Configuration
$base_url = 'http://localhost/amt/api/';
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

// Test data
$test_credentials = [
    'username' => 'test',
    'password' => 'test',
    'deviceToken' => 'test-device-token'
];

/**
 * Make HTTP request
 */
function makeRequest($url, $data = null, $headers = []) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    
    if ($data !== null) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    return [
        'response' => $response,
        'http_code' => $httpCode,
        'error' => $error
    ];
}

echo "=== Authentication Flow Debug ===\n\n";

echo "1. Testing Auth endpoint with debug info:\n";
echo "==========================================\n";

$result = makeRequest($base_url . 'auth/login', $test_credentials, $headers);

echo "HTTP Status: " . $result['http_code'] . "\n";
if ($result['error']) {
    echo "CURL Error: " . $result['error'] . "\n";
}

echo "Response:\n";
echo $result['response'] . "\n\n";

// Parse the response
$response_data = json_decode($result['response'], true);
if ($response_data) {
    echo "2. Response Analysis:\n";
    echo "=====================\n";
    echo "Status: " . $response_data['status'] . "\n";
    echo "Message: " . $response_data['message'] . "\n";
    
    if ($response_data['message'] == 'Your account is suspended') {
        echo "\n🔍 ISSUE CONFIRMED: Getting 'Your account is suspended' message\n";
        echo "This means student_panel_login is NOT equal to 'yes'\n";
        echo "\nPossible causes:\n";
        echo "1. Database query in getSetting() is failing\n";
        echo "2. student_panel_login field has wrong value in database\n";
        echo "3. Comparison logic issue in Auth_model\n";
    }
}

echo "\n3. Let's test a simple endpoint to check if basic API works:\n";
echo "============================================================\n";

// Test a simple endpoint that doesn't require authentication
$simple_result = makeRequest($base_url . 'invalid/test', [], $headers);
echo "Simple test HTTP Status: " . $simple_result['http_code'] . "\n";
echo "Simple test Response: " . substr($simple_result['response'], 0, 200) . "...\n";

echo "\n=== Debug Complete ===\n";
echo "\nNext steps:\n";
echo "1. Check if sch_settings table exists and has data\n";
echo "2. Verify student_panel_login field value\n";
echo "3. Check if JOIN queries in getSetting() are working\n";
echo "4. Test with a direct database update\n";
?>
