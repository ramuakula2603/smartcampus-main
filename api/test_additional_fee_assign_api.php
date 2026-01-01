<?php
// Test script for Additional Fee Assign API
// This script tests the update functionality of the additional fee assign API

// Configuration
$api_url = 'http://localhost/amt/api/additional-fee-assign/update';
$auth_headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

// Test data - Replace with valid IDs from your database
$test_data = [
    'id' => 1, // Replace with a valid ID from student_fees_amountadding table
    'amount' => 2500.00
];

// Initialize cURL
$ch = curl_init();

// Set cURL options
curl_setopt_array($ch, [
    CURLOPT_URL => $api_url,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($test_data),
    CURLOPT_HTTPHEADER => $auth_headers,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_TIMEOUT => 30
]);

// Execute request
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

// Close cURL
curl_close($ch);

// Output results
echo "HTTP Code: " . $http_code . "\n";
echo "Response: " . $response . "\n";

if (!empty($error)) {
    echo "cURL Error: " . $error . "\n";
}

// Parse and display JSON response
if ($response) {
    $json_response = json_decode($response, true);
    if ($json_response) {
        echo "Parsed Response:\n";
        print_r($json_response);
    } else {
        echo "Failed to parse JSON response\n";
    }
}
