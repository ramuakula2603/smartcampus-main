<?php
// Test script for Behaviour Report API
// This script tests the report functionality of the behavioral module

echo "Testing Behaviour Report API\n";
echo "============================\n\n";

// Configuration
$base_url = 'http://localhost/amt/api';
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

echo "1. Testing report endpoint with no filters...\n";
$data = json_encode([]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . '/behaviour/studentincidents/report');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $http_code . "\n";
echo "Response: " . $response . "\n\n";

echo "2. Testing report endpoint with class filter...\n";
$data = json_encode([
    'class_id' => 1
]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . '/behaviour/studentincidents/report');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $http_code . "\n";
echo "Response: " . $response . "\n\n";

echo "3. Testing report endpoint with section filter...\n";
$data = json_encode([
    'section_id' => 1
]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . '/behaviour/studentincidents/report');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $http_code . "\n";
echo "Response: " . $response . "\n\n";

echo "4. Testing report endpoint with student filter...\n";
$data = json_encode([
    'student_id' => 1
]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . '/behaviour/studentincidents/report');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $http_code . "\n";
echo "Response: " . $response . "\n\n";

echo "5. Testing report endpoint with date range filter...\n";
$data = json_encode([
    'from_date' => '2020-01-01',
    'to_date' => '2025-12-31'
]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . '/behaviour/studentincidents/report');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $http_code . "\n";
echo "Response: " . $response . "\n\n";

echo "6. Testing report endpoint with incident type filter...\n";
$data = json_encode([
    'incident_id' => 1
]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . '/behaviour/studentincidents/report');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $http_code . "\n";
echo "Response: " . $response . "\n\n";

echo "7. Testing report endpoint with multiple filters...\n";
$data = json_encode([
    'class_id' => 1,
    'section_id' => 1,
    'from_date' => '2020-01-01',
    'to_date' => '2025-12-31'
]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . '/behaviour/studentincidents/report');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $http_code . "\n";
echo "Response: " . $response . "\n\n";

echo "8. Testing report endpoint with all filters...\n";
$data = json_encode([
    'class_id' => 1,
    'section_id' => 1,
    'student_id' => 1,
    'from_date' => '2020-01-01',
    'to_date' => '2025-12-31',
    'incident_id' => 1
]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . '/behaviour/studentincidents/report');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $http_code . "\n";
echo "Response: " . $response . "\n\n";

echo "Behaviour Report API testing completed.\n";
?>
