<?php
// Test script for Behaviour Setting API
// This script tests the basic functionality of the API endpoints for managing behaviour settings

echo "Testing Behaviour Setting API\n";
echo "=============================\n\n";

// Configuration
$base_url = 'http://localhost/amt/api';
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

echo "1. Testing get behaviour settings endpoint...\n";
$data = json_encode([]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . '/behaviour/setting/get');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $http_code . "\n";
echo "Response: " . $response . "\n\n";

// Extract the settings ID for update test
$settings_id = null;
if ($http_code == 200) {
    $response_data = json_decode($response, true);
    if (isset($response_data['data']['id'])) {
        $settings_id = $response_data['data']['id'];
        echo "Settings ID: " . $settings_id . "\n\n";
    }
}

echo "2. Testing update behaviour settings endpoint...\n";
if ($settings_id) {
    $data = json_encode([
        'id' => $settings_id,
        'comment_option' => ['student', 'parent']
    ]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $base_url . '/behaviour/setting/update');
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
    echo "Skipping update test - no settings ID available\n\n";
}

echo "3. Testing get behaviour settings after update...\n";
$data = json_encode([]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . '/behaviour/setting/get');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $http_code . "\n";
echo "Response: " . $response . "\n\n";

echo "Behaviour Setting API testing completed.\n";
?>
