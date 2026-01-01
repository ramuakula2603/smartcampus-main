<?php
/**
 * Direct test of Class Sections API Link endpoint
 */

// Test the link endpoint directly
$url = 'http://localhost/amt/api/class-sections/link';

// Prepare the request
$data = array(
    'class_id' => 1,
    'section_id' => 1
);

$options = array(
    'http' => array(
        'header' => array(
            'Content-Type: application/json',
            'Client-Service: smartschool',
            'Auth-Key: schoolAdmin@'
        ),
        'method' => 'POST',
        'content' => json_encode($data)
    )
);

$context = stream_context_create($options);

echo "Testing Class Sections API Link Endpoint\n";
echo "=========================================\n\n";
echo "URL: " . $url . "\n";
echo "Method: POST\n";
echo "Headers:\n";
echo "  - Content-Type: application/json\n";
echo "  - Client-Service: smartschool\n";
echo "  - Auth-Key: schoolAdmin@\n";
echo "Request Body:\n";
echo json_encode($data, JSON_PRETTY_PRINT) . "\n\n";

try {
    $response = file_get_contents($url, false, $context);
    
    echo "Response:\n";
    echo $response . "\n";
    
    // Check HTTP response code
    if (isset($http_response_header)) {
        echo "\nHTTP Headers:\n";
        foreach ($http_response_header as $header) {
            echo "  " . $header . "\n";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>

