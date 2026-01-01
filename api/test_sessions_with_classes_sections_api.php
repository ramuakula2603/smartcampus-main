<?php

/**
 * Test script for Sessions with Classes and Sections API
 * 
 * This script tests the new sessions-with-classes-sections endpoint
 * to ensure it returns the correct hierarchical structure.
 * 
 * Usage: php test_sessions_with_classes_sections_api.php
 */

// Configuration
$base_url = 'http://localhost/amt/api';
$endpoint = '/teacher/sessions-with-classes-sections';
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

/**
 * Make HTTP POST request
 */
function makeRequest($url, $data = [], $headers = []) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    
    curl_close($ch);
    
    if ($error) {
        return [
            'success' => false,
            'error' => $error,
            'http_code' => $http_code
        ];
    }
    
    return [
        'success' => true,
        'data' => json_decode($response, true),
        'http_code' => $http_code,
        'raw_response' => $response
    ];
}

/**
 * Print test results
 */
function printTestResult($test_name, $result, $expected_structure = null) {
    echo "\n" . str_repeat("=", 80) . "\n";
    echo "TEST: $test_name\n";
    echo str_repeat("=", 80) . "\n";
    
    if (!$result['success']) {
        echo "❌ FAILED - cURL Error: " . $result['error'] . "\n";
        echo "HTTP Code: " . $result['http_code'] . "\n";
        return false;
    }
    
    echo "HTTP Code: " . $result['http_code'] . "\n";
    
    if ($result['http_code'] !== 200) {
        echo "❌ FAILED - HTTP Error\n";
        echo "Response: " . $result['raw_response'] . "\n";
        return false;
    }
    
    $data = $result['data'];
    
    if (!$data) {
        echo "❌ FAILED - Invalid JSON response\n";
        echo "Raw Response: " . $result['raw_response'] . "\n";
        return false;
    }
    
    // Check basic response structure
    if (!isset($data['status']) || !isset($data['message']) || !isset($data['data'])) {
        echo "❌ FAILED - Missing required response fields\n";
        echo "Response: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
        return false;
    }
    
    if ($data['status'] !== 1) {
        echo "❌ FAILED - API returned error status\n";
        echo "Message: " . $data['message'] . "\n";
        if (isset($data['error'])) {
            echo "Error: " . json_encode($data['error'], JSON_PRETTY_PRINT) . "\n";
        }
        return false;
    }
    
    echo "✅ SUCCESS\n";
    echo "Message: " . $data['message'] . "\n";
    echo "Total Sessions: " . $data['total_sessions'] . "\n";
    
    // Validate hierarchical structure
    if ($expected_structure) {
        echo "\nValidating hierarchical structure...\n";
        
        foreach ($data['data'] as $session_index => $session) {
            // Check session structure
            $required_session_fields = ['session_id', 'session_name', 'is_active', 'classes_count', 'classes'];
            foreach ($required_session_fields as $field) {
                if (!isset($session[$field])) {
                    echo "❌ Missing session field: $field in session $session_index\n";
                    return false;
                }
            }
            
            echo "Session {$session['session_id']}: {$session['session_name']} ({$session['classes_count']} classes)\n";
            
            // Check classes structure
            foreach ($session['classes'] as $class_index => $class) {
                $required_class_fields = ['class_id', 'class_name', 'is_active', 'sections_count', 'sections'];
                foreach ($required_class_fields as $field) {
                    if (!isset($class[$field])) {
                        echo "❌ Missing class field: $field in class $class_index\n";
                        return false;
                    }
                }
                
                echo "  Class {$class['class_id']}: {$class['class_name']} ({$class['sections_count']} sections)\n";
                
                // Check sections structure
                foreach ($class['sections'] as $section_index => $section) {
                    $required_section_fields = ['section_id', 'section_name', 'is_active'];
                    foreach ($required_section_fields as $field) {
                        if (!isset($section[$field])) {
                            echo "❌ Missing section field: $field in section $section_index\n";
                            return false;
                        }
                    }
                    
                    echo "    Section {$section['section_id']}: {$section['section_name']}\n";
                }
            }
        }
        
        echo "✅ Hierarchical structure validation passed\n";
    }
    
    return true;
}

// Run tests
echo "Sessions with Classes and Sections API Test Suite\n";
echo "================================================\n";
echo "Base URL: $base_url\n";
echo "Endpoint: $endpoint\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n";

// Test 1: Get all sessions with default parameters
$test1_result = makeRequest($base_url . $endpoint, [], $headers);
$test1_success = printTestResult(
    "Get All Sessions with Classes and Sections (Default Parameters)", 
    $test1_result, 
    true
);

// Test 2: Get all sessions including inactive
$test2_result = makeRequest($base_url . $endpoint, ['include_inactive' => true], $headers);
$test2_success = printTestResult(
    "Get All Sessions Including Inactive Records", 
    $test2_result, 
    true
);

// Test 3: Test with empty request body
$test3_result = makeRequest($base_url . $endpoint, [], $headers);
$test3_success = printTestResult(
    "Empty Request Body Test", 
    $test3_result, 
    true
);

// Summary
echo "\n" . str_repeat("=", 80) . "\n";
echo "TEST SUMMARY\n";
echo str_repeat("=", 80) . "\n";

$total_tests = 3;
$passed_tests = ($test1_success ? 1 : 0) + ($test2_success ? 1 : 0) + ($test3_success ? 1 : 0);

echo "Total Tests: $total_tests\n";
echo "Passed: $passed_tests\n";
echo "Failed: " . ($total_tests - $passed_tests) . "\n";

if ($passed_tests === $total_tests) {
    echo "🎉 ALL TESTS PASSED!\n";
    echo "The Sessions with Classes and Sections API is working correctly.\n";
} else {
    echo "❌ SOME TESTS FAILED\n";
    echo "Please check the API implementation and database configuration.\n";
}

echo "\nTest completed at: " . date('Y-m-d H:i:s') . "\n";

?>
