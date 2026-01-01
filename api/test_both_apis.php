<?php
/**
 * Test both Sections API and Subjects API
 */

echo "=== TESTING BOTH APIS ===\n\n";

// Test Sections API
echo "1. Testing Sections API\n";
echo "=======================\n";

$sections_url = "http://localhost/amt/api/sections/list";
$headers = array(
    "Content-Type: application/json",
    "Client-Service: smartschool",
    "Auth-Key: schoolAdmin@"
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $sections_url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array()));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "URL: $sections_url\n";
echo "HTTP Code: $http_code\n";
echo "Response:\n";
echo $response . "\n\n";

// Test Subjects API
echo "2. Testing Subjects API\n";
echo "=======================\n";

$subjects_url = "http://localhost/amt/api/subjects/list";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $subjects_url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array()));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "URL: $subjects_url\n";
echo "HTTP Code: $http_code\n";
echo "Response:\n";
echo $response . "\n\n";

echo "=== END TEST ===\n";
?>

