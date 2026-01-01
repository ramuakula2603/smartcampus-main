<?php
/**
 * Comprehensive Student House API Test
 * Tests all endpoints including error scenarios
 */

echo "=== Comprehensive Student House API Test ===\n\n";

$base_url = 'http://localhost/amt/api/';
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

$test_results = [];

/**
 * Make HTTP request
 */
function makeRequest($url, $data = null, $headers = []) {
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => implode("\r\n", $headers),
            'content' => $data ? json_encode($data) : '{}',
            'timeout' => 30,
            'ignore_errors' => true
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    $http_code = 200;
    
    if (isset($http_response_header)) {
        foreach ($http_response_header as $header) {
            if (preg_match('/HTTP\/\d\.\d\s+(\d+)/', $header, $matches)) {
                $http_code = intval($matches[1]);
                break;
            }
        }
    }
    
    return [
        'response' => $response,
        'http_code' => $http_code,
        'success' => $response !== false,
        'json' => $response ? json_decode($response, true) : null
    ];
}

/**
 * Run test and record result
 */
function runTest($name, $url, $data = null, $headers = [], $expected_status = 1, $expected_http = 200) {
    global $test_results;
    
    echo "Testing: $name\n";
    echo str_repeat("-", 50) . "\n";
    
    $result = makeRequest($url, $data, $headers);
    
    $success = true;
    $issues = [];
    
    // Check HTTP status
    if ($result['http_code'] != $expected_http) {
        $success = false;
        $issues[] = "Expected HTTP $expected_http, got {$result['http_code']}";
    }
    
    // Check JSON response
    if (!$result['json']) {
        $success = false;
        $issues[] = "Invalid JSON response";
    } else {
        // Check status field
        if (isset($result['json']['status']) && $result['json']['status'] != $expected_status) {
            $success = false;
            $issues[] = "Expected status $expected_status, got {$result['json']['status']}";
        }
    }
    
    if ($success) {
        echo "✅ PASS\n";
        if ($result['json']) {
            echo "Message: " . $result['json']['message'] . "\n";
        }
    } else {
        echo "❌ FAIL\n";
        foreach ($issues as $issue) {
            echo "  - $issue\n";
        }
        if ($result['response']) {
            echo "Response: " . substr($result['response'], 0, 200) . "...\n";
        }
    }
    
    $test_results[] = [
        'name' => $name,
        'success' => $success,
        'issues' => $issues,
        'response' => $result['json']
    ];
    
    echo "\n";
    return $result;
}

// Test 1: Valid List Request
runTest(
    "List All Houses (Valid)",
    $base_url . 'student-house/list',
    [],
    $headers,
    1,
    200
);

// Test 2: Invalid Headers
runTest(
    "List Houses (Invalid Headers)",
    $base_url . 'student-house/list',
    [],
    ['Content-Type: application/json'],
    0,
    401
);

// Test 3: Get Valid House
runTest(
    "Get House ID 1 (Valid)",
    $base_url . 'student-house/get/1',
    [],
    $headers,
    1,
    200
);

// Test 4: Get Invalid House ID
runTest(
    "Get House ID 999 (Not Found)",
    $base_url . 'student-house/get/999',
    [],
    $headers,
    0,
    404
);

// Test 5: Get Invalid House ID Format
runTest(
    "Get House ID 'abc' (Invalid Format)",
    $base_url . 'student-house/get/abc',
    [],
    $headers,
    0,
    400
);

// Test 6: Create Valid House
$create_result = runTest(
    "Create House (Valid)",
    $base_url . 'student-house/create',
    [
        'house_name' => 'Test House ' . time(),
        'description' => 'Test house for API validation'
    ],
    $headers,
    1,
    201
);

$created_id = null;
if ($create_result['json'] && isset($create_result['json']['data']['id'])) {
    $created_id = $create_result['json']['data']['id'];
}

// Test 7: Create House (Missing Name)
runTest(
    "Create House (Missing Name)",
    $base_url . 'student-house/create',
    [
        'description' => 'House without name'
    ],
    $headers,
    0,
    400
);

// Test 8: Create House (Empty Name)
runTest(
    "Create House (Empty Name)",
    $base_url . 'student-house/create',
    [
        'house_name' => '',
        'description' => 'House with empty name'
    ],
    $headers,
    0,
    400
);

// Test 9: Update Valid House
if ($created_id) {
    runTest(
        "Update House (Valid)",
        $base_url . 'student-house/update/' . $created_id,
        [
            'house_name' => 'Updated Test House',
            'description' => 'Updated description',
            'is_active' => 'yes'
        ],
        $headers,
        1,
        200
    );
}

// Test 10: Update Non-existent House
runTest(
    "Update House (Not Found)",
    $base_url . 'student-house/update/999',
    [
        'house_name' => 'Non-existent House',
        'description' => 'This should fail'
    ],
    $headers,
    0,
    404
);

// Test 11: Delete Valid House
if ($created_id) {
    runTest(
        "Delete House (Valid)",
        $base_url . 'student-house/delete/' . $created_id,
        [],
        $headers,
        1,
        200
    );
}

// Test 12: Delete Non-existent House
runTest(
    "Delete House (Not Found)",
    $base_url . 'student-house/delete/999',
    [],
    $headers,
    0,
    404
);

// Summary
echo "=== TEST SUMMARY ===\n";
$total_tests = count($test_results);
$passed_tests = array_filter($test_results, function($test) { return $test['success']; });
$failed_tests = array_filter($test_results, function($test) { return !$test['success']; });

echo "Total Tests: $total_tests\n";
echo "Passed: " . count($passed_tests) . "\n";
echo "Failed: " . count($failed_tests) . "\n";
echo "Success Rate: " . round((count($passed_tests) / $total_tests) * 100, 2) . "%\n\n";

if (!empty($failed_tests)) {
    echo "FAILED TESTS:\n";
    foreach ($failed_tests as $test) {
        echo "- {$test['name']}\n";
        foreach ($test['issues'] as $issue) {
            echo "  * $issue\n";
        }
    }
} else {
    echo "🎉 ALL TESTS PASSED! Student House API is working perfectly!\n";
}

echo "\n=== Test Complete ===\n";
?>
