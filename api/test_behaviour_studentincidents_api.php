<?php
// Test script for Behaviour Student Incidents API
// This script tests the basic functionality of the API endpoints

echo "Testing Behaviour Student Incidents API\n";
echo "========================================\n\n";

// Configuration
$base_url = 'http://localhost/amt/api';
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

// Test student ID (you may need to adjust this based on your database)
$test_student_id = 1;
$test_incident_id = 1;
$test_comment_id = 1;

echo "1. Testing get_by_student endpoint...\n";
$data = json_encode([
    'student_id' => $test_student_id,
    'session_value' => 'current_session'
]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . '/behaviour/studentincidents/get-by-student');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $http_code . "\n";
echo "Response: " . $response . "\n\n";

echo "2. Testing total_points endpoint...\n";
$data = json_encode([
    'student_id' => $test_student_id
]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . '/behaviour/studentincidents/total-points');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $http_code . "\n";
echo "Response: " . $response . "\n\n";

echo "3. Testing student_behavior endpoint...\n";
$data = json_encode([
    'student_id' => $test_student_id
]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . '/behaviour/studentincidents/student-behavior');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $http_code . "\n";
echo "Response: " . $response . "\n\n";

echo "4. Testing get_comments endpoint...\n";
$data = json_encode([
    'student_incident_id' => $test_incident_id
]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . '/behaviour/studentincidents/get-comments');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $http_code . "\n";
echo "Response: " . $response . "\n\n";

echo "API testing completed.\n";
?>
