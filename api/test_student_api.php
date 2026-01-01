<?php
/**
 * Student API Test Script
 * 
 * This script tests the Student Webservice API endpoints.
 * Run this file from command line or browser to test the API.
 * 
 * Usage:
 *   php test_student_api.php
 *   OR
 *   http://localhost/amt/api/test_student_api.php
 */

// Configuration
$base_url = 'http://localhost/amt/api/';
$endpoint = 'student_admission_api/create';

// Test data - Minimal required fields
$test_data_minimal = array(
    'firstname' => 'Test',
    'lastname' => 'Student',
    'gender' => 'Male',
    'dob' => '2010-01-15',
    'class_id' => 1,
    'section_id' => 1,
    'reference_id' => 1,
    'guardian_name' => 'Test Guardian',
    'guardian_is' => 'Father',
    'guardian_phone' => '9876543210',
    'guardian_email' => 'testguardian' . time() . '@example.com',
    'email' => 'teststudent' . time() . '@example.com',
    'mobileno' => '9123456789',
);

// Test data - Complete with all optional fields
$test_data_complete = array(
    'firstname' => 'John',
    'middlename' => 'Michael',
    'lastname' => 'Doe',
    'gender' => 'Male',
    'dob' => '2010-01-15',
    'class_id' => 1,
    'section_id' => 1,
    'reference_id' => 1,
    'guardian_name' => 'Jane Doe',
    'guardian_is' => 'Mother',
    'guardian_phone' => '9876543210',
    'guardian_email' => 'janedoe' . time() . '@example.com',
    'guardian_relation' => 'Mother',
    'guardian_occupation' => 'Teacher',
    'guardian_address' => '123 Main Street, City',
    'email' => 'johndoe' . time() . '@example.com',
    'mobileno' => '9123456789',
    'blood_group' => 'O+',
    'religion' => 'Hindu',
    'cast' => 'General',
    'category_id' => 1,
    'current_address' => '123 Main Street, City',
    'permanent_address' => '123 Main Street, City',
    'father_name' => 'Robert Doe',
    'father_phone' => '9876543211',
    'father_occupation' => 'Engineer',
    'mother_name' => 'Jane Doe',
    'mother_phone' => '9876543210',
    'mother_occupation' => 'Teacher',
    'admission_date' => '2024-04-01',
    'admi_no' => 'TEST' . time(),
    'state' => 'Maharashtra',
    'city' => 'Mumbai',
    'pincode' => '400001',
    'height' => '150',
    'weight' => '45',
    'adhar_no' => '123456789012',
    'note' => 'Test student created via API',
);

// Test data - Invalid (for testing validation)
$test_data_invalid = array(
    'firstname' => '', // Missing required field
    'gender' => 'Invalid', // Invalid value
    'dob' => 'invalid-date', // Invalid date format
    'email' => 'invalid-email', // Invalid email format
    'mobileno' => '123', // Invalid mobile number
);

/**
 * Make API request
 */
function make_api_request($url, $data) {
    $ch = curl_init($url);
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen(json_encode($data))
    ));
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    
    curl_close($ch);
    
    return array(
        'http_code' => $http_code,
        'response' => $response,
        'error' => $error
    );
}

/**
 * Print test result
 */
function print_test_result($test_name, $result) {
    echo "\n" . str_repeat("=", 80) . "\n";
    echo "TEST: " . $test_name . "\n";
    echo str_repeat("=", 80) . "\n";
    
    echo "HTTP Code: " . $result['http_code'] . "\n";
    
    if (!empty($result['error'])) {
        echo "cURL Error: " . $result['error'] . "\n";
    }
    
    echo "\nResponse:\n";
    $response_data = json_decode($result['response'], true);
    if ($response_data) {
        echo json_encode($response_data, JSON_PRETTY_PRINT) . "\n";
    } else {
        echo $result['response'] . "\n";
    }
    
    echo "\n";
}

// Check if running from command line or browser
$is_cli = (php_sapi_name() === 'cli');

if (!$is_cli) {
    header('Content-Type: text/plain');
}

echo "Student API Test Script\n";
echo "=======================\n\n";
echo "Base URL: " . $base_url . "\n";
echo "Endpoint: " . $endpoint . "\n\n";

// Test 1: Create student with minimal required fields
echo "Running Test 1: Create student with minimal required fields...\n";
$result1 = make_api_request($base_url . $endpoint, $test_data_minimal);
print_test_result("Minimal Required Fields", $result1);

// Test 2: Create student with complete data
echo "Running Test 2: Create student with complete data...\n";
$result2 = make_api_request($base_url . $endpoint, $test_data_complete);
print_test_result("Complete Data", $result2);

// Test 3: Test validation with invalid data
echo "Running Test 3: Test validation with invalid data...\n";
$result3 = make_api_request($base_url . $endpoint, $test_data_invalid);
print_test_result("Invalid Data (Validation Test)", $result3);

// Test 4: Test duplicate email
echo "Running Test 4: Test duplicate email validation...\n";
$duplicate_data = $test_data_minimal;
$duplicate_data['email'] = $test_data_minimal['email']; // Use same email as Test 1
$duplicate_data['guardian_email'] = 'newguardian' . time() . '@example.com'; // Different guardian email
$result4 = make_api_request($base_url . $endpoint, $duplicate_data);
print_test_result("Duplicate Email", $result4);

// Test 5: Test with sibling
if (isset($result1['response'])) {
    $response1_data = json_decode($result1['response'], true);
    if (isset($response1_data['data']['student_id'])) {
        echo "Running Test 5: Create student with sibling...\n";
        $sibling_data = array(
            'firstname' => 'Sibling',
            'lastname' => 'Student',
            'gender' => 'Female',
            'dob' => '2012-05-20',
            'class_id' => 1,
            'section_id' => 1,
            'reference_id' => 1,
            'guardian_name' => 'Test Guardian',
            'guardian_is' => 'Father',
            'guardian_phone' => '9876543210',
            'email' => 'sibling' . time() . '@example.com',
            'mobileno' => '9123456788',
            'sibling_id' => $response1_data['data']['student_id'],
        );
        $result5 = make_api_request($base_url . $endpoint, $sibling_data);
        print_test_result("Sibling Student", $result5);
    }
}

echo "\n" . str_repeat("=", 80) . "\n";
echo "All tests completed!\n";
echo str_repeat("=", 80) . "\n";

// Summary
echo "\nTest Summary:\n";
echo "-------------\n";
echo "Test 1 (Minimal): HTTP " . $result1['http_code'] . " - " . 
     (in_array($result1['http_code'], [200, 201]) ? "PASSED" : "FAILED") . "\n";
echo "Test 2 (Complete): HTTP " . $result2['http_code'] . " - " . 
     (in_array($result2['http_code'], [200, 201]) ? "PASSED" : "FAILED") . "\n";
echo "Test 3 (Invalid): HTTP " . $result3['http_code'] . " - " . 
     ($result3['http_code'] == 400 ? "PASSED (Expected validation error)" : "FAILED") . "\n";
echo "Test 4 (Duplicate): HTTP " . $result4['http_code'] . " - " . 
     ($result4['http_code'] == 400 ? "PASSED (Expected validation error)" : "FAILED") . "\n";

if (isset($result5)) {
    echo "Test 5 (Sibling): HTTP " . $result5['http_code'] . " - " . 
         (in_array($result5['http_code'], [200, 201]) ? "PASSED" : "FAILED") . "\n";
}

echo "\nNote: Check the database to verify that records were created correctly.\n";
echo "Student credentials are returned in the API response for successful creations.\n";

