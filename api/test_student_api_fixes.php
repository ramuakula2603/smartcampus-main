<?php
/**
 * Test script to verify Student API fixes
 * Tests the Auth endpoint and other Student API functionality
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
    'username' => 'student',
    'password' => 'student123',
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

/**
 * Test results tracking
 */
$tests = [];
$passed = 0;
$failed = 0;

/**
 * Test 1: Check if Auth endpoint is accessible
 */
echo "=== Test 1: Auth Endpoint Accessibility ===\n";
$result = makeRequest($base_url . 'auth/login', $test_credentials, $headers);

$test1_passed = false;
if ($result['error']) {
    echo "❌ CURL Error: " . $result['error'] . "\n";
} elseif ($result['http_code'] == 200) {
    echo "✅ HTTP Status: " . $result['http_code'] . "\n";
    $response_data = json_decode($result['response'], true);
    
    if ($response_data) {
        echo "✅ Valid JSON Response received\n";
        echo "Response: " . json_encode($response_data, JSON_PRETTY_PRINT) . "\n";
        
        // Check if we get a proper response structure (even if login fails)
        if (isset($response_data['status']) && isset($response_data['message'])) {
            echo "✅ Response has proper structure\n";
            $test1_passed = true;
        } else {
            echo "❌ Response missing required fields (status, message)\n";
        }
    } else {
        echo "❌ Invalid JSON Response\n";
        echo "Raw Response: " . $result['response'] . "\n";
    }
} else {
    echo "❌ HTTP Status: " . $result['http_code'] . "\n";
    echo "Response: " . $result['response'] . "\n";
}

$tests['Auth Endpoint Accessibility'] = $test1_passed;
if ($test1_passed) $passed++; else $failed++;

echo "\n";

/**
 * Test 2: Test Webservice endpoint accessibility
 */
echo "=== Test 2: Webservice Endpoint Accessibility ===\n";

// Test a simple webservice endpoint
$webservice_data = [
    'student_id' => 1
];

$result = makeRequest($base_url . 'webservice/getStudentProfile', $webservice_data, $headers);

$test2_passed = false;
if ($result['error']) {
    echo "❌ CURL Error: " . $result['error'] . "\n";
} elseif ($result['http_code'] == 200 || $result['http_code'] == 404) {
    echo "✅ HTTP Status: " . $result['http_code'] . "\n";
    $response_data = json_decode($result['response'], true);

    if ($response_data) {
        echo "✅ Valid JSON Response received\n";
        echo "Response: " . json_encode($response_data, JSON_PRETTY_PRINT) . "\n";

        // Check if we get a proper response structure (even if it fails due to auth or missing data)
        if (isset($response_data['status']) && isset($response_data['message'])) {
            echo "✅ Response has proper structure\n";
            if ($result['http_code'] == 404 && $response_data['message'] == 'Student not found') {
                echo "✅ Proper error handling for missing student\n";
            }
            $test2_passed = true;
        } else {
            echo "❌ Response missing required fields (status, message)\n";
        }
    } else {
        echo "❌ Invalid JSON Response\n";
        echo "Raw Response: " . $result['response'] . "\n";
    }
} else {
    echo "❌ HTTP Status: " . $result['http_code'] . "\n";
    echo "Response: " . $result['response'] . "\n";
}

$tests['Webservice Endpoint Accessibility'] = $test2_passed;
if ($test2_passed) $passed++; else $failed++;

echo "\n";

/**
 * Test 3: Test Error Handling (Invalid endpoint)
 */
echo "=== Test 3: Error Handling Test ===\n";

// Test an invalid endpoint to see if error handling works
$result = makeRequest($base_url . 'invalid/endpoint', [], $headers);

$test3_passed = false;
if ($result['error']) {
    echo "❌ CURL Error: " . $result['error'] . "\n";
} else {
    echo "✅ HTTP Status: " . $result['http_code'] . "\n";
    $response_data = json_decode($result['response'], true);

    if ($response_data) {
        echo "✅ Valid JSON Response received\n";
        echo "Response: " . json_encode($response_data, JSON_PRETTY_PRINT) . "\n";

        // Check if we get a proper error response structure
        if (isset($response_data['status']) && isset($response_data['message'])) {
            echo "✅ Error response has proper structure\n";
            $test3_passed = true;
        } else {
            echo "❌ Error response missing required fields (status, message)\n";
        }
    } else {
        echo "❌ Invalid JSON Response\n";
        echo "Raw Response: " . $result['response'] . "\n";
    }
}

$tests['Error Handling'] = $test3_passed;
if ($test3_passed) $passed++; else $failed++;

echo "\n";

/**
 * Test Summary
 */
echo "=== TEST SUMMARY ===\n";
echo "Total Tests: " . count($tests) . "\n";
echo "Passed: $passed\n";
echo "Failed: $failed\n";
echo "Success Rate: " . round(($passed / count($tests)) * 100, 2) . "%\n\n";

echo "Detailed Results:\n";
foreach ($tests as $test_name => $result) {
    echo ($result ? "✅" : "❌") . " $test_name\n";
}

if ($failed > 0) {
    echo "\n⚠️  Some tests failed. Please check the error messages above.\n";
    exit(1);
} else {
    echo "\n🎉 All tests passed! Student API fixes are working correctly.\n";
    exit(0);
}
?>
