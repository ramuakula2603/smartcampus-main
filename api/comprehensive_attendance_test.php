<?php
/**
 * Comprehensive Staff Attendance API Testing Script
 * Tests both endpoints with various scenarios and validates data accuracy
 */

echo "=== COMPREHENSIVE STAFF ATTENDANCE API TESTING ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

// Test configuration
$base_url = 'http://localhost/amt/api';
$auth_headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

// Test scenarios
$test_scenarios = [
    [
        'name' => 'Individual Staff - Current Year',
        'endpoint' => 'teacher/attendance-summary',
        'data' => ['staff_id' => 6],
        'authenticated' => true
    ],
    [
        'name' => 'Individual Staff - Specific Date Range',
        'endpoint' => 'teacher/attendance-summary',
        'data' => ['staff_id' => 6, 'from_date' => '2024-08-01', 'to_date' => '2024-08-31'],
        'authenticated' => true
    ],
    [
        'name' => 'All Staff - August 2024',
        'endpoint' => 'teacher/attendance-summary',
        'data' => ['from_date' => '2024-08-01', 'to_date' => '2024-08-31'],
        'authenticated' => true
    ],
    [
        'name' => 'All Staff - Current Year',
        'endpoint' => 'teacher/attendance-summary',
        'data' => [],
        'authenticated' => true
    ],
    [
        'name' => 'Alternative Endpoint - Individual Staff',
        'endpoint' => 'attendance/summary',
        'data' => ['staff_id' => 6, 'from_date' => '2024-08-01', 'to_date' => '2024-08-31'],
        'authenticated' => false
    ],
    [
        'name' => 'Alternative Endpoint - All Staff',
        'endpoint' => 'attendance/summary',
        'data' => ['from_date' => '2024-08-01', 'to_date' => '2024-08-31'],
        'authenticated' => false
    ]
];

// Error test scenarios
$error_scenarios = [
    [
        'name' => 'Invalid Staff ID',
        'endpoint' => 'teacher/attendance-summary',
        'data' => ['staff_id' => 99999],
        'authenticated' => true
    ],
    [
        'name' => 'Invalid Date Format',
        'endpoint' => 'teacher/attendance-summary',
        'data' => ['from_date' => '2024-13-01', 'to_date' => '2024-08-31'],
        'authenticated' => true
    ],
    [
        'name' => 'Missing Authentication',
        'endpoint' => 'teacher/attendance-summary',
        'data' => ['staff_id' => 6],
        'authenticated' => false
    ]
];

$test_results = [];

/**
 * Execute API test
 */
function executeTest($name, $endpoint, $data, $authenticated, $base_url, $auth_headers) {
    echo "Testing: $name\n";
    echo "Endpoint: $endpoint\n";
    echo "Data: " . json_encode($data) . "\n";
    
    $url = "$base_url/$endpoint";
    $headers = $authenticated ? $auth_headers : ['Content-Type: application/json'];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "❌ CURL Error: $error\n\n";
        return ['status' => 'error', 'message' => $error];
    }
    
    // Separate headers and body
    $header_size = strpos($response, "\r\n\r\n");
    $headers_text = substr($response, 0, $header_size);
    $body = substr($response, $header_size + 4);
    
    echo "HTTP Status: $http_code\n";
    
    if ($http_code == 200) {
        $json_data = json_decode($body, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            echo "✅ SUCCESS - Valid JSON response\n";
            
            // Analyze response data
            if (isset($json_data['data'])) {
                analyzeResponseData($json_data['data'], $name);
            }
            
            echo "\n";
            return ['status' => 'success', 'data' => $json_data, 'http_code' => $http_code];
        } else {
            echo "❌ FAILED - Invalid JSON response\n";
            echo "Response: " . substr($body, 0, 200) . "...\n\n";
            return ['status' => 'json_error', 'response' => $body];
        }
    } else {
        echo "❌ FAILED - HTTP $http_code\n";
        echo "Response: " . substr($body, 0, 200) . "...\n\n";
        return ['status' => 'http_error', 'http_code' => $http_code, 'response' => $body];
    }
}

/**
 * Analyze response data for completeness and accuracy
 */
function analyzeResponseData($data, $test_name) {
    echo "📊 Data Analysis:\n";
    
    if (isset($data['staff_id'])) {
        // Individual staff response
        echo "   - Staff ID: {$data['staff_id']}\n";
        echo "   - Staff Name: {$data['staff_info']['name']} {$data['staff_info']['surname']}\n";
        
        if (isset($data['attendance_summary'])) {
            $summary = $data['attendance_summary'];
            echo "   - Present: {$summary['Present']['count']} days\n";
            echo "   - Absent: {$summary['Absent']['count']} days\n";
            echo "   - Late: {$summary['Late']['count']} days\n";
            echo "   - Half Day: {$summary['Half Day']['count']} days\n";
            echo "   - Holiday: {$summary['Holiday']['count']} days\n";
            
            $total_attendance_records = count($data['attendance_dates']);
            echo "   - Total attendance records: $total_attendance_records\n";
        }
    } elseif (isset($data['staff_attendance_data'])) {
        // All staff response
        $total_staff = count($data['staff_attendance_data']);
        echo "   - Total staff members: $total_staff\n";
        
        $staff_with_attendance = 0;
        $total_present = 0;
        $total_absent = 0;
        
        foreach ($data['staff_attendance_data'] as $staff) {
            if (isset($staff['attendance_summary'])) {
                $present = $staff['attendance_summary']['Present']['count'];
                $absent = $staff['attendance_summary']['Absent']['count'];
                
                if ($present > 0 || $absent > 0) {
                    $staff_with_attendance++;
                }
                
                $total_present += $present;
                $total_absent += $absent;
            }
        }
        
        echo "   - Staff with attendance records: $staff_with_attendance\n";
        echo "   - Total present days (all staff): $total_present\n";
        echo "   - Total absent days (all staff): $total_absent\n";
    }
    
    if (isset($data['date_range'])) {
        echo "   - Date range: {$data['date_range']['from_date']} to {$data['date_range']['to_date']}\n";
    }
}

// Execute main tests
echo "🧪 EXECUTING MAIN TEST SCENARIOS\n";
echo "================================\n\n";

foreach ($test_scenarios as $scenario) {
    $result = executeTest(
        $scenario['name'],
        $scenario['endpoint'],
        $scenario['data'],
        $scenario['authenticated'],
        $base_url,
        $auth_headers
    );
    $test_results[] = array_merge($scenario, ['result' => $result]);
}

// Execute error tests
echo "🚨 EXECUTING ERROR TEST SCENARIOS\n";
echo "=================================\n\n";

foreach ($error_scenarios as $scenario) {
    $result = executeTest(
        $scenario['name'],
        $scenario['endpoint'],
        $scenario['data'],
        $scenario['authenticated'],
        $base_url,
        $auth_headers
    );
    $test_results[] = array_merge($scenario, ['result' => $result]);
}

// Generate summary report
echo "📋 TEST SUMMARY REPORT\n";
echo "=====================\n\n";

$successful_tests = 0;
$failed_tests = 0;

foreach ($test_results as $test) {
    $status = $test['result']['status'];
    if ($status === 'success') {
        $successful_tests++;
        echo "✅ {$test['name']}: PASSED\n";
    } else {
        $failed_tests++;
        echo "❌ {$test['name']}: FAILED ($status)\n";
    }
}

echo "\n📊 OVERALL RESULTS:\n";
echo "Total Tests: " . count($test_results) . "\n";
echo "Successful: $successful_tests\n";
echo "Failed: $failed_tests\n";
echo "Success Rate: " . round(($successful_tests / count($test_results)) * 100, 2) . "%\n\n";

echo "=== TESTING COMPLETED ===\n";
?>
