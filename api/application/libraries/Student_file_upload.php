<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Student File Upload Library
 * 
 * This library handles file uploads for the Student API, specifically designed
 * to work with base64-encoded files from JSON API requests.
 * 
 * Supports:
 * - Student images (jpg, jpeg, png)
 * - Parent photos (father, mother, guardian)
 * - Application documents (PDF)
 * - Additional student documents (PDF, jpg, jpeg, png)
 * 
 * @package    Student Management System
 * @subpackage API Libraries
 * @category   File Upload
 * @author     School Management System
 * @version    1.0.0
 */
class Student_file_upload
{
    /**
     * CodeIgniter instance
     * @var object
     */
    private $_CI;

    /**
     * Base upload path
     * @var string
     */
    private $base_path;

    /**
     * Allowed image extensions
     * @var array
     */
    private $allowed_image_extensions = array('jpg', 'jpeg', 'png', 'gif');

    /**
     * Allowed document extensions
     * @var array
     */
    private $allowed_document_extensions = array('pdf', 'doc', 'docx');

    /**
     * Maximum file size in bytes (5MB default)
     * @var int
     */
    private $max_file_size = 5242880; // 5MB

    /**
     * Maximum image size in bytes (2MB default)
     * @var int
     */
    private $max_image_size = 2097152; // 2MB

    /**
     * Upload directories configuration
     * @var array
     */
    private $upload_directories = array(
        'student_image'      => 'uploads/student_images/',
        'father_pic'         => 'uploads/student_images/',
        'mother_pic'         => 'uploads/student_images/',
        'guardian_pic'       => 'uploads/student_images/',
        'application_file'   => 'uploads/application_files/',
        'student_document'   => 'uploads/student_documents/',
    );

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_CI = &get_instance();
        $this->_CI->load->library('customlib');
        
        // Set base path
        $this->base_path = FCPATH; // Front controller path (index.php directory)
    }

    /**
     * Upload base64 encoded file
     * 
     * @param string $base64_data Base64 encoded file data
     * @param string $file_type Type of file (student_image, father_pic, etc.)
     * @param int $student_id Student ID (optional, for document folder)
     * @return array Result array with status, message, and file_path
     */
    public function upload_base64_file($base64_data, $file_type, $student_id = null)
    {
        try {
            // Validate input
            if (empty($base64_data)) {
                return $this->error_response('No file data provided');
            }

            if (!isset($this->upload_directories[$file_type])) {
                return $this->error_response('Invalid file type: ' . $file_type);
            }

            // Parse base64 data
            $file_info = $this->parse_base64_data($base64_data);
            if (!$file_info['status']) {
                return $file_info;
            }

            // Validate file type
            $validation = $this->validate_file($file_info, $file_type);
            if (!$validation['status']) {
                return $validation;
            }

            // Generate file name
            $file_name = $this->generate_file_name($file_info['extension'], $file_type);

            // Determine upload path
            $upload_path = $this->get_upload_path($file_type, $student_id);

            // Create directory if not exists
            $dir_result = $this->ensure_directory_exists($upload_path);
            if (!$dir_result['status']) {
                return $dir_result;
            }

            // Save file
            $full_path = $upload_path . $file_name;
            $save_result = $this->save_file($file_info['decoded_data'], $full_path);
            
            if (!$save_result['status']) {
                return $save_result;
            }

            // Return success with relative path for database storage
            $relative_path = str_replace($this->base_path, '', $full_path);
            $relative_path = str_replace('\\', '/', $relative_path); // Normalize path separators

            return array(
                'status' => true,
                'message' => 'File uploaded successfully',
                'file_path' => $relative_path,
                'file_name' => $file_name,
                'file_size' => strlen($file_info['decoded_data']),
                'file_type' => $file_info['mime_type']
            );

        } catch (Exception $e) {
            return $this->error_response('Exception: ' . $e->getMessage());
        }
    }

    /**
     * Parse base64 encoded data
     * 
     * Supports formats:
     * - data:image/png;base64,iVBORw0KGgo...
     * - iVBORw0KGgo... (raw base64)
     * 
     * @param string $base64_data Base64 encoded data
     * @return array Result with decoded data and metadata
     */
    private function parse_base64_data($base64_data)
    {
        // Check if data URI format
        if (preg_match('/^data:([a-zA-Z0-9]+\/[a-zA-Z0-9\-\+\.]+);base64,(.+)$/', $base64_data, $matches)) {
            $mime_type = $matches[1];
            $base64_string = $matches[2];
        } else {
            // Assume raw base64 string
            $base64_string = $base64_data;
            $mime_type = null;
        }

        // Decode base64
        $decoded_data = base64_decode($base64_string, true);

        if ($decoded_data === false) {
            return $this->error_response('Invalid base64 data');
        }

        // Detect mime type if not provided
        if (!$mime_type) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime_type = $finfo->buffer($decoded_data);
        }

        // Get extension from mime type
        $extension = $this->get_extension_from_mime($mime_type);

        if (!$extension) {
            return $this->error_response('Unable to determine file type from mime: ' . $mime_type);
        }

        return array(
            'status' => true,
            'decoded_data' => $decoded_data,
            'mime_type' => $mime_type,
            'extension' => $extension,
            'size' => strlen($decoded_data)
        );
    }

    /**
     * Get file extension from MIME type
     * 
     * @param string $mime_type MIME type
     * @return string|false Extension or false
     */
    private function get_extension_from_mime($mime_type)
    {
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

    /**
     * Validate file based on type
     * 
     * @param array $file_info File information
     * @param string $file_type File type category
     * @return array Validation result
     */
    private function validate_file($file_info, $file_type)
    {
        $extension = $file_info['extension'];
        $size = $file_info['size'];

        // Determine if this is an image or document
        $is_image = in_array($file_type, array('student_image', 'father_pic', 'mother_pic', 'guardian_pic'));
        $is_document = in_array($file_type, array('application_file', 'student_document'));

        // Validate extension
        if ($is_image) {
            if (!in_array($extension, $this->allowed_image_extensions)) {
                return $this->error_response(
                    'Invalid image format. Allowed: ' . implode(', ', $this->allowed_image_extensions)
                );
            }

            // Validate image size
            if ($size > $this->max_image_size) {
                return $this->error_response(
                    'Image size exceeds maximum allowed size of ' . $this->format_bytes($this->max_image_size)
                );
            }
        } elseif ($is_document) {
            if (!in_array($extension, $this->allowed_document_extensions)) {
                return $this->error_response(
                    'Invalid document format. Allowed: ' . implode(', ', $this->allowed_document_extensions)
                );
            }

            // Validate document size
            if ($size > $this->max_file_size) {
                return $this->error_response(
                    'Document size exceeds maximum allowed size of ' . $this->format_bytes($this->max_file_size)
                );
            }
        }

        return array('status' => true);
    }

    /**
     * Generate unique file name
     * 
     * @param string $extension File extension
     * @param string $file_type File type
     * @return string Generated file name
     */
    private function generate_file_name($extension, $file_type)
    {
        $timestamp = time();
        $random = uniqid(rand());
        $prefix = str_replace('_', '-', $file_type);
        
        return $prefix . '-' . $timestamp . '-' . $random . '.' . $extension;
    }

    /**
     * Get upload path for file type
     * 
     * @param string $file_type File type
     * @param int $student_id Student ID (for document folders)
     * @return string Full upload path
     */
    private function get_upload_path($file_type, $student_id = null)
    {
        $relative_path = $this->upload_directories[$file_type];

        // For student documents, create student-specific folder
        if ($file_type === 'student_document' && $student_id) {
            $relative_path .= $student_id . '/';
        }

        return $this->base_path . $relative_path;
    }

    /**
     * Ensure directory exists, create if not
     * 
     * @param string $directory Directory path
     * @return array Result
     */
    private function ensure_directory_exists($directory)
    {
        if (!is_dir($directory)) {
            if (!mkdir($directory, 0755, true)) {
                return $this->error_response('Failed to create directory: ' . $directory);
            }
        }

        if (!is_writable($directory)) {
            return $this->error_response('Directory is not writable: ' . $directory);
        }

        return array('status' => true);
    }

    /**
     * Save file to disk
     * 
     * @param string $data File data
     * @param string $path Full file path
     * @return array Result
     */
    private function save_file($data, $path)
    {
        $bytes_written = file_put_contents($path, $data);

        if ($bytes_written === false) {
            return $this->error_response('Failed to save file to: ' . $path);
        }

        return array(
            'status' => true,
            'bytes_written' => $bytes_written
        );
    }

    /**
     * Delete uploaded file
     * 
     * @param string $file_path Relative file path
     * @return array Result
     */
    public function delete_file($file_path)
    {
        if (empty($file_path)) {
            return $this->error_response('No file path provided');
        }

        $full_path = $this->base_path . $file_path;

        if (!file_exists($full_path)) {
            return $this->error_response('File does not exist: ' . $file_path);
        }

        if (unlink($full_path)) {
            return array(
                'status' => true,
                'message' => 'File deleted successfully'
            );
        }

        return $this->error_response('Failed to delete file: ' . $file_path);
    }

    /**
     * Format bytes to human readable format
     * 
     * @param int $bytes Bytes
     * @return string Formatted string
     */
    private function format_bytes($bytes)
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
     * Create error response
     * 
     * @param string $message Error message
     * @return array Error response
     */
    private function error_response($message)
    {
        return array(
            'status' => false,
            'message' => $message
        );
    }

    /**
     * Set maximum file size
     * 
     * @param int $bytes Size in bytes
     * @return void
     */
    public function set_max_file_size($bytes)
    {
        $this->max_file_size = $bytes;
    }

    /**
     * Set maximum image size
     * 
     * @param int $bytes Size in bytes
     * @return void
     */
    public function set_max_image_size($bytes)
    {
        $this->max_image_size = $bytes;
    }

    /**
     * Get allowed image extensions
     * 
     * @return array
     */
    public function get_allowed_image_extensions()
    {
        return $this->allowed_image_extensions;
    }

    /**
     * Get allowed document extensions
     * 
     * @return array
     */
    public function get_allowed_document_extensions()
    {
        return $this->allowed_document_extensions;
    }
}

