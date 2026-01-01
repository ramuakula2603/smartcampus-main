<?php
// Test script for Student Behaviour Types API
// This script tests the basic functionality of the API endpoints for managing behaviour types

echo "Testing Student Behaviour Types API\n";
echo "====================================\n\n";

// Configuration
$base_url = 'http://localhost/amt/api';
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

// Test data
$test_behaviour_id = null;

echo "1. Testing create behaviour type endpoint...\n";
$data = json_encode([
    'title' => 'Test Behaviour Type',
    'point' => 5,
    'description' => 'This is a test behaviour type for API testing'
]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . '/studentbehaviour/create');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $http_code . "\n";
echo "Response: " . $response . "\n\n";

// Extract the created behaviour ID for further tests
if ($http_code == 200) {
    $response_data = json_decode($response, true);
    if (isset($response_data['data']['id'])) {
        $test_behaviour_id = $response_data['data']['id'];
        echo "Created behaviour ID: " . $test_behaviour_id . "\n\n";
    }
}

echo "2. Testing list behaviour types endpoint...\n";
$data = json_encode([]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . '/studentbehaviour/list');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $http_code . "\n";
echo "Response: " . $response . "\n\n";

echo "3. Testing get behaviour type endpoint...\n";
if ($test_behaviour_id) {
    $data = json_encode([
        'id' => $test_behaviour_id
    ]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $base_url . '/studentbehaviour/get');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "HTTP Code: " . $http_code . "\n";
    echo "Response: " . $response . "\n\n";
} else {
    echo "Skipping get test - no behaviour ID available\n\n";
}

echo "4. Testing update behaviour type endpoint...\n";
if ($test_behaviour_id) {
    $data = json_encode([
        'id' => $test_behaviour_id,
        'title' => 'Updated Test Behaviour Type',
        'point' => 10,
        'description' => 'This is an updated test behaviour type for API testing'
    ]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $base_url . '/studentbehaviour/update');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "HTTP Code: " . $http_code . "\n";
    echo "Response: " . $response . "\n\n";
} else {
    echo "Skipping update test - no behaviour ID available\n\n";
}

echo "5. Testing delete behaviour type endpoint...\n";
if ($test_behaviour_id) {
    $data = json_encode([
        'id' => $test_behaviour_id
    ]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $base_url . '/studentbehaviour/delete');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "HTTP Code: " . $http_code . "\n";
    echo "Response: " . $response . "\n\n";
} else {
    echo "Skipping delete test - no behaviour ID available\n\n";
}

echo "Student Behaviour Types API testing completed.\n";
?>
