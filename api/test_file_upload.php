<?php
/**
 * File Upload Test Script
 * 
 * This script tests the Student_file_upload library and helper functions.
 * It can be run from command line or browser.
 * 
 * Usage:
 *   php test_file_upload.php
 *   OR
 *   http://localhost/amt/api/test_file_upload.php
 */

// Check if running from command line or browser
$is_cli = (php_sapi_name() === 'cli');

if (!$is_cli) {
    header('Content-Type: text/plain');
}

echo "Student File Upload Test Script\n";
echo "================================\n\n";

// Bootstrap CodeIgniter
define('BASEPATH', dirname(__FILE__) . '/system/');
require_once(dirname(__FILE__) . '/index.php');

$CI = &get_instance();

// Load library and helper
$CI->load->library('student_file_upload');
$CI->load->helper('student_file_upload');

echo "Library and helper loaded successfully.\n\n";

// ============================================================================
// Test 1: Create Test Images
// ============================================================================
echo str_repeat("=", 80) . "\n";
echo "TEST 1: Create Test Images\n";
echo str_repeat("=", 80) . "\n";

function create_test_image($width = 100, $height = 100, $text = 'Test')
{
    $image = imagecreate($width, $height);
    $bg = imagecolorallocate($image, 255, 255, 255);
    $text_color = imagecolorallocate($image, 0, 0, 0);
    imagestring($image, 5, 10, 40, $text, $text_color);
    
    ob_start();
    imagepng($image);
    $image_data = ob_get_clean();
    imagedestroy($image);
    
    return $image_data;
}

// Create test images
$student_image_data = create_test_image(200, 200, 'Student');
$father_image_data = create_test_image(150, 150, 'Father');
$mother_image_data = create_test_image(150, 150, 'Mother');

echo "Created 3 test images\n";
echo "Student image size: " . strlen($student_image_data) . " bytes\n";
echo "Father image size: " . strlen($father_image_data) . " bytes\n";
echo "Mother image size: " . strlen($mother_image_data) . " bytes\n\n";

// ============================================================================
// Test 2: Convert to Base64
// ============================================================================
echo str_repeat("=", 80) . "\n";
echo "TEST 2: Convert to Base64\n";
echo str_repeat("=", 80) . "\n";

$student_base64 = 'data:image/png;base64,' . base64_encode($student_image_data);
$father_base64 = 'data:image/png;base64,' . base64_encode($father_image_data);
$mother_base64 = 'data:image/png;base64,' . base64_encode($mother_image_data);

echo "Student base64 length: " . strlen($student_base64) . " characters\n";
echo "Father base64 length: " . strlen($father_base64) . " characters\n";
echo "Mother base64 length: " . strlen($mother_base64) . " characters\n\n";

// ============================================================================
// Test 3: Validate Base64 Data
// ============================================================================
echo str_repeat("=", 80) . "\n";
echo "TEST 3: Validate Base64 Data\n";
echo str_repeat("=", 80) . "\n";

$valid_student = is_valid_base64_file($student_base64);
$valid_father = is_valid_base64_file($father_base64);
$valid_invalid = is_valid_base64_file('invalid-base64-data');

echo "Student base64 valid: " . ($valid_student ? "YES" : "NO") . "\n";
echo "Father base64 valid: " . ($valid_father ? "YES" : "NO") . "\n";
echo "Invalid data valid: " . ($valid_invalid ? "YES" : "NO") . "\n\n";

// ============================================================================
// Test 4: Get File Size from Base64
// ============================================================================
echo str_repeat("=", 80) . "\n";
echo "TEST 4: Get File Size from Base64\n";
echo str_repeat("=", 80) . "\n";

$student_size = get_base64_file_size($student_base64);
$father_size = get_base64_file_size($father_base64);

echo "Student file size: " . format_file_size($student_size) . "\n";
echo "Father file size: " . format_file_size($father_size) . "\n\n";

// ============================================================================
// Test 5: Upload Single File
// ============================================================================
echo str_repeat("=", 80) . "\n";
echo "TEST 5: Upload Single File\n";
echo str_repeat("=", 80) . "\n";

$result1 = $CI->student_file_upload->upload_base64_file(
    $student_base64,
    'student_image',
    999 // Test student ID
);

echo "Upload result:\n";
echo "Status: " . ($result1['status'] ? "SUCCESS" : "FAILED") . "\n";
echo "Message: " . $result1['message'] . "\n";
if ($result1['status']) {
    echo "File path: " . $result1['file_path'] . "\n";
    echo "File name: " . $result1['file_name'] . "\n";
    echo "File size: " . format_file_size($result1['file_size']) . "\n";
    echo "File type: " . $result1['file_type'] . "\n";
}
echo "\n";

// ============================================================================
// Test 6: Upload Multiple Files Using Helper
// ============================================================================
echo str_repeat("=", 80) . "\n";
echo "TEST 6: Upload Multiple Files Using Helper\n";
echo str_repeat("=", 80) . "\n";

$input_data = array(
    'student_image' => $student_base64,
    'father_pic' => $father_base64,
    'mother_pic' => $mother_base64,
);

$result2 = process_student_files($input_data, 999);

echo "Batch upload result:\n";
echo "Status: " . ($result2['status'] ? "SUCCESS" : "FAILED") . "\n";
echo "Success count: " . $result2['success_count'] . "\n";
echo "Files uploaded:\n";
foreach ($result2['file_paths'] as $field => $path) {
    echo "  - $field: $path\n";
}
if (count($result2['errors']) > 0) {
    echo "Errors:\n";
    foreach ($result2['errors'] as $field => $error) {
        echo "  - $field: $error\n";
    }
}
echo "\n";

// ============================================================================
// Test 7: Test Invalid File Type
// ============================================================================
echo str_repeat("=", 80) . "\n";
echo "TEST 7: Test Invalid File Type\n";
echo str_repeat("=", 80) . "\n";

$invalid_base64 = 'data:application/exe;base64,' . base64_encode('invalid file content');
$result3 = $CI->student_file_upload->upload_base64_file(
    $invalid_base64,
    'student_image',
    999
);

echo "Invalid file upload result:\n";
echo "Status: " . ($result3['status'] ? "SUCCESS" : "FAILED") . "\n";
echo "Message: " . $result3['message'] . "\n\n";

// ============================================================================
// Test 8: Test File Size Limit
// ============================================================================
echo str_repeat("=", 80) . "\n";
echo "TEST 8: Test File Size Limit\n";
echo str_repeat("=", 80) . "\n";

// Create a large image (should exceed 2MB limit)
$large_image_data = create_test_image(3000, 3000, 'Large');
$large_base64 = 'data:image/png;base64,' . base64_encode($large_image_data);

echo "Large image size: " . format_file_size(strlen($large_image_data)) . "\n";

$result4 = $CI->student_file_upload->upload_base64_file(
    $large_base64,
    'student_image',
    999
);

echo "Large file upload result:\n";
echo "Status: " . ($result4['status'] ? "SUCCESS" : "FAILED") . "\n";
echo "Message: " . $result4['message'] . "\n\n";

// ============================================================================
// Test 9: Test PDF Document Upload
// ============================================================================
echo str_repeat("=", 80) . "\n";
echo "TEST 9: Test PDF Document Upload\n";
echo str_repeat("=", 80) . "\n";

// Create a minimal PDF
$pdf_content = "%PDF-1.4\n1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n3 0 obj\n<< /Type /Page /Parent 2 0 R /Resources << /Font << /F1 << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> >> >> /MediaBox [0 0 612 792] /Contents 4 0 R >>\nendobj\n4 0 obj\n<< /Length 44 >>\nstream\nBT\n/F1 12 Tf\n100 700 Td\n(Test PDF) Tj\nET\nendstream\nendobj\nxref\n0 5\n0000000000 65535 f\n0000000009 00000 n\n0000000058 00000 n\n0000000115 00000 n\n0000000317 00000 n\ntrailer\n<< /Size 5 /Root 1 0 R >>\nstartxref\n408\n%%EOF";
$pdf_base64 = 'data:application/pdf;base64,' . base64_encode($pdf_content);

$result5 = $CI->student_file_upload->upload_base64_file(
    $pdf_base64,
    'application_file',
    999
);

echo "PDF upload result:\n";
echo "Status: " . ($result5['status'] ? "SUCCESS" : "FAILED") . "\n";
echo "Message: " . $result5['message'] . "\n";
if ($result5['status']) {
    echo "File path: " . $result5['file_path'] . "\n";
}
echo "\n";

// ============================================================================
// Test 10: Test File Deletion
// ============================================================================
echo str_repeat("=", 80) . "\n";
echo "TEST 10: Test File Deletion\n";
echo str_repeat("=", 80) . "\n";

if ($result1['status']) {
    $delete_result = delete_student_file($result1['file_path']);
    echo "Delete result:\n";
    echo "Status: " . ($delete_result['status'] ? "SUCCESS" : "FAILED") . "\n";
    echo "Message: " . $delete_result['message'] . "\n";
} else {
    echo "Skipping deletion test (no file to delete)\n";
}
echo "\n";

// ============================================================================
// Test 11: Get Allowed Extensions
// ============================================================================
echo str_repeat("=", 80) . "\n";
echo "TEST 11: Get Allowed Extensions\n";
echo str_repeat("=", 80) . "\n";

$image_exts = get_allowed_student_file_extensions('image');
$doc_exts = get_allowed_student_file_extensions('document');

echo "Allowed image extensions: " . implode(', ', $image_exts) . "\n";
echo "Allowed document extensions: " . implode(', ', $doc_exts) . "\n\n";

// ============================================================================
// Test 12: Batch Upload with Mixed Results
// ============================================================================
echo str_repeat("=", 80) . "\n";
echo "TEST 12: Batch Upload with Mixed Results\n";
echo str_repeat("=", 80) . "\n";

$files = array(
    array('data' => $student_base64, 'type' => 'student_image', 'student_id' => 999),
    array('data' => $father_base64, 'type' => 'father_pic', 'student_id' => 999),
    array('data' => 'invalid-data', 'type' => 'mother_pic', 'student_id' => 999),
);

$result6 = batch_upload_student_files($files);

echo "Batch upload result:\n";
echo "Total files: " . $result6['total'] . "\n";
echo "Success count: " . $result6['success_count'] . "\n";
echo "Error count: " . $result6['error_count'] . "\n";
echo "\nIndividual results:\n";
foreach ($result6['results'] as $index => $result) {
    echo "File $index: " . ($result['status'] ? "SUCCESS" : "FAILED") . " - " . $result['message'] . "\n";
}
echo "\n";

// ============================================================================
// Summary
// ============================================================================
echo str_repeat("=", 80) . "\n";
echo "TEST SUMMARY\n";
echo str_repeat("=", 80) . "\n";

$tests = array(
    'Test 1: Create Test Images' => true,
    'Test 2: Convert to Base64' => true,
    'Test 3: Validate Base64 Data' => ($valid_student && $valid_father && !$valid_invalid),
    'Test 4: Get File Size' => ($student_size > 0 && $father_size > 0),
    'Test 5: Upload Single File' => $result1['status'],
    'Test 6: Upload Multiple Files' => $result2['status'],
    'Test 7: Invalid File Type' => (!$result3['status']), // Should fail
    'Test 8: File Size Limit' => (!$result4['status']), // Should fail
    'Test 9: PDF Document Upload' => $result5['status'],
    'Test 10: File Deletion' => (isset($delete_result) ? $delete_result['status'] : true),
    'Test 11: Get Allowed Extensions' => (count($image_exts) > 0 && count($doc_exts) > 0),
    'Test 12: Batch Upload' => ($result6['success_count'] > 0),
);

$passed = 0;
$failed = 0;

foreach ($tests as $test_name => $result) {
    $status = $result ? "PASSED" : "FAILED";
    echo "$test_name: $status\n";
    if ($result) {
        $passed++;
    } else {
        $failed++;
    }
}

echo "\n";
echo "Total Tests: " . count($tests) . "\n";
echo "Passed: $passed\n";
echo "Failed: $failed\n";
echo "\n";

if ($failed === 0) {
    echo "✓ All tests passed!\n";
} else {
    echo "✗ Some tests failed. Please review the output above.\n";
}

echo "\n";
echo "Note: Check the uploads directory to verify files were created:\n";
echo "  - uploads/student_images/\n";
echo "  - uploads/application_files/\n";
echo "  - uploads/student_documents/999/\n";

