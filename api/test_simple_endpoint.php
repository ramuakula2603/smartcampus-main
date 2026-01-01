<?php
/**
 * Test if API endpoints are accessible
 */

$endpoints = array(
    'http://localhost/amt/api/classes/list',
    'http://localhost/amt/api/sections/list',
    'http://localhost/amt/api/class-sections/list',
    'http://localhost/amt/api/class-sections/link'
);

$headers = array(
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
);

echo "Testing API Endpoints Accessibility\n";
echo "====================================\n\n";

foreach ($endpoints as $url) {
    echo "Testing: " . $url . "\n";
    
    $options = array(
        'http' => array(
            'header' => $headers,
            'method' => 'POST',
            'content' => json_encode(array())
        )
    );
    
    $context = stream_context_create($options);
    
    try {
        $response = @file_get_contents($url, false, $context);
        
        if ($response === false) {
            echo "  Status: FAILED (No response)\n";
            if (isset($http_response_header)) {
                echo "  HTTP Status: " . $http_response_header[0] . "\n";
            }
        } else {
            echo "  Status: SUCCESS\n";
            echo "  Response Length: " . strlen($response) . " bytes\n";
            if (isset($http_response_header)) {
                echo "  HTTP Status: " . $http_response_header[0] . "\n";
            }
        }
    } catch (Exception $e) {
        echo "  Status: ERROR - " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}
?>

