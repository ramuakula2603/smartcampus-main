<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Student File Upload Helper
 * 
 * Provides convenient helper functions for handling file uploads in the Student API.
 * These functions wrap the Student_file_upload library for easy use.
 * 
 * @package    Student Management System
 * @subpackage API Helpers
 * @category   File Upload
 * @author     School Management System
 * @version    1.0.0
 */

/**
 * Process student files from API request
 * 
 * This function processes all file uploads from a student creation/update request.
 * It handles multiple file types and returns an array of file paths for database storage.
 * 
 * @param array $input_data Input data from API request
 * @param int $student_id Student ID (optional, for document folders)
 * @return array Result with status, file_paths, and errors
 * 
 * Example usage:
 * $result = process_student_files($input_data, $student_id);
 * if ($result['status']) {
 *     $file_paths = $result['file_paths'];
 *     // Update database with file paths
 * } else {
 *     // Handle errors
 *     $errors = $result['errors'];
 * }
 */
function process_student_files($input_data, $student_id = null)
{
    $CI = &get_instance();
    $CI->load->library('student_file_upload');

    $file_paths = array();
    $errors = array();
    $success_count = 0;

    // Define file fields and their types
    $file_fields = array(
        'student_image'    => 'student_image',
        'father_pic'       => 'father_pic',
        'mother_pic'       => 'mother_pic',
        'guardian_pic'     => 'guardian_pic',
        'application_file' => 'application_file',
        'first_doc'        => 'student_document',
        'second_doc'       => 'student_document',
        'fourth_doc'       => 'student_document',
        'fifth_doc'        => 'student_document',
    );

    // Process each file field
    foreach ($file_fields as $field_name => $file_type) {
        if (isset($input_data[$field_name]) && !empty($input_data[$field_name])) {
            $result = $CI->student_file_upload->upload_base64_file(
                $input_data[$field_name],
                $file_type,
                $student_id
            );

            if ($result['status']) {
                $file_paths[$field_name] = $result['file_path'];
                $success_count++;
            } else {
                $errors[$field_name] = $result['message'];
            }
        }
    }

    return array(
        'status' => (count($errors) === 0),
        'success_count' => $success_count,
        'file_paths' => $file_paths,
        'errors' => $errors
    );
}

/**
 * Upload single student file
 * 
 * Uploads a single file and returns the file path.
 * 
 * @param string $base64_data Base64 encoded file data
 * @param string $file_type File type (student_image, father_pic, etc.)
 * @param int $student_id Student ID (optional)
 * @return array Result with status, message, and file_path
 * 
 * Example usage:
 * $result = upload_student_file($base64_data, 'student_image');
 * if ($result['status']) {
 *     $file_path = $result['file_path'];
 * }
 */
function upload_student_file($base64_data, $file_type, $student_id = null)
{
    $CI = &get_instance();
    $CI->load->library('student_file_upload');

    return $CI->student_file_upload->upload_base64_file($base64_data, $file_type, $student_id);
}

/**
 * Delete student file
 * 
 * Deletes a file from the server.
 * 
 * @param string $file_path Relative file path
 * @return array Result with status and message
 * 
 * Example usage:
 * $result = delete_student_file('uploads/student_images/student-image-123.jpg');
 * if ($result['status']) {
 *     // File deleted successfully
 * }
 */
function delete_student_file($file_path)
{
    $CI = &get_instance();
    $CI->load->library('student_file_upload');

    return $CI->student_file_upload->delete_file($file_path);
}

/**
 * Validate base64 file data
 * 
 * Validates that the provided data is valid base64 encoded file data.
 * 
 * @param string $base64_data Base64 encoded data
 * @return bool True if valid, false otherwise
 */
function is_valid_base64_file($base64_data)
{
    if (empty($base64_data)) {
        return false;
    }

    // Check if data URI format
    if (preg_match('/^data:([a-zA-Z0-9]+\/[a-zA-Z0-9\-\+\.]+);base64,(.+)$/', $base64_data, $matches)) {
        $base64_string = $matches[2];
    } else {
        $base64_string = $base64_data;
    }

    // Try to decode
    $decoded = base64_decode($base64_string, true);
    
    return ($decoded !== false && base64_encode($decoded) === $base64_string);
}

/**
 * Get file size from base64 data
 * 
 * Calculates the file size from base64 encoded data.
 * 
 * @param string $base64_data Base64 encoded data
 * @return int|false File size in bytes or false on error
 */
function get_base64_file_size($base64_data)
{
    if (empty($base64_data)) {
        return false;
    }

    // Check if data URI format
    if (preg_match('/^data:([a-zA-Z0-9]+\/[a-zA-Z0-9\-\+\.]+);base64,(.+)$/', $base64_data, $matches)) {
        $base64_string = $matches[2];
    } else {
        $base64_string = $base64_data;
    }

    // Decode and get size
    $decoded = base64_decode($base64_string, true);
    
    if ($decoded === false) {
        return false;
    }

    return strlen($decoded);
}

/**
 * Format file size to human readable format
 * 
 * @param int $bytes File size in bytes
 * @return string Formatted file size
 */
function format_file_size($bytes)
{
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    }
    return $bytes . ' bytes';
}

/**
 * Get allowed file extensions for student files
 * 
 * @param string $type Type of file ('image' or 'document')
 * @return array Array of allowed extensions
 */
function get_allowed_student_file_extensions($type = 'image')
{
    $CI = &get_instance();
    $CI->load->library('student_file_upload');

    if ($type === 'image') {
        return $CI->student_file_upload->get_allowed_image_extensions();
    } elseif ($type === 'document') {
        return $CI->student_file_upload->get_allowed_document_extensions();
    }

    return array();
}

/**
 * Validate file extension
 * 
 * @param string $extension File extension
 * @param string $type Type of file ('image' or 'document')
 * @return bool True if valid, false otherwise
 */
function is_valid_file_extension($extension, $type = 'image')
{
    $allowed = get_allowed_student_file_extensions($type);
    return in_array(strtolower($extension), $allowed);
}

/**
 * Extract file extension from base64 data
 * 
 * @param string $base64_data Base64 encoded data
 * @return string|false File extension or false on error
 */
function get_extension_from_base64($base64_data)
{
    if (empty($base64_data)) {
        return false;
    }

    // Check if data URI format
    if (preg_match('/^data:([a-zA-Z0-9]+\/[a-zA-Z0-9\-\+\.]+);base64,(.+)$/', $base64_data, $matches)) {
        $mime_type = $matches[1];
        
        $mime_map = array(
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        );

        return isset($mime_map[$mime_type]) ? $mime_map[$mime_type] : false;
    }

    return false;
}

/**
 * Create base64 data URI from file
 * 
 * Useful for testing or converting existing files to base64 format.
 * 
 * @param string $file_path Path to file
 * @return string|false Base64 data URI or false on error
 */
function file_to_base64($file_path)
{
    if (!file_exists($file_path)) {
        return false;
    }

    $file_data = file_get_contents($file_path);
    if ($file_data === false) {
        return false;
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime_type = $finfo->file($file_path);

    return 'data:' . $mime_type . ';base64,' . base64_encode($file_data);
}

/**
 * Batch process multiple files
 * 
 * Processes multiple files and returns results for each.
 * 
 * @param array $files Array of files with 'data', 'type', and optional 'student_id'
 * @return array Results for each file
 * 
 * Example usage:
 * $files = array(
 *     array('data' => $base64_data1, 'type' => 'student_image'),
 *     array('data' => $base64_data2, 'type' => 'father_pic'),
 * );
 * $results = batch_upload_student_files($files);
 */
function batch_upload_student_files($files)
{
    $results = array();
    $success_count = 0;
    $error_count = 0;

    foreach ($files as $index => $file) {
        if (!isset($file['data']) || !isset($file['type'])) {
            $results[$index] = array(
                'status' => false,
                'message' => 'Missing data or type'
            );
            $error_count++;
            continue;
        }

        $student_id = isset($file['student_id']) ? $file['student_id'] : null;
        $result = upload_student_file($file['data'], $file['type'], $student_id);
        
        $results[$index] = $result;
        
        if ($result['status']) {
            $success_count++;
        } else {
            $error_count++;
        }
    }

    return array(
        'results' => $results,
        'success_count' => $success_count,
        'error_count' => $error_count,
        'total' => count($files)
    );
}

