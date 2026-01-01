<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Bulk Delete API Controller
 * 
 * This controller provides RESTful API endpoints for bulk delete operations.
 * It handles bulk deletion of student records with proper validation and safety checks.
 * 
 * @package    Student Management System
 * @subpackage API Controllers
 * @category   Webservices
 * @author     School Management System
 * @version    1.0.0
 */
class Bulk_delete_api extends CI_Controller
{
    /**
     * Constructor
     * 
     * Initializes the controller, loads required models, libraries, and helpers.
     */
    public function __construct()
    {
        parent::__construct();

        // Start output buffering if not already started
        if (!ob_get_level()) {
            ob_start();
        }

        // Set JSON content type early
        $this->output->set_content_type('application/json');

        // Load essential helpers
        $this->load->helper('json_output');

        // Load required models
        try {
            $this->load->model(array(
                'student_model',
                'setting_model',
                'class_model',
                'section_model'
            ));
        } catch (Exception $e) {
            log_message('error', 'Error loading models: ' . $e->getMessage());
        }

        // Load form validation library
        $this->load->library('form_validation');

        // Get school settings
        try {
            $this->sch_setting_detail = $this->setting_model->getSetting();
            
            // Set timezone
            if (isset($this->sch_setting_detail->timezone) && $this->sch_setting_detail->timezone != "") {
                date_default_timezone_set($this->sch_setting_detail->timezone);
            } else {
                date_default_timezone_set('UTC');
            }
        } catch (Exception $e) {
            log_message('error', 'Error loading school settings: ' . $e->getMessage());
            date_default_timezone_set('UTC');
        }
    }

    /**
     * Validate request headers
     * 
     * @return bool True if headers are valid, false otherwise
     */
    private function validate_headers()
    {
        $client_service = $this->input->get_request_header('Client-Service', TRUE);
        $auth_key = $this->input->get_request_header('Auth-Key', TRUE);

        return ($client_service === 'smartschool' && $auth_key === 'schoolAdmin@');
    }

    /**
     * Bulk delete students
     * 
     * Performs bulk deletion of student records with validation and safety checks.
     * 
     * @return void Outputs JSON response
     */
    public function students()
    {
        try {
            // Validate request method
            if ($this->input->method() !== 'post') {
                json_output(405, array(
                    'status' => 0,
                    'message' => 'Method not allowed. Use POST method.',
                    'data' => null
                ));
                return;
            }

            // Validate required headers
            if (!$this->validate_headers()) {
                json_output(401, array(
                    'status' => 0,
                    'message' => 'Unauthorized access. Invalid headers.',
                    'data' => null
                ));
                return;
            }

            // Get input data
            $input = json_decode($this->input->raw_input_stream, true);

            // Validate student IDs
            if (empty($input['student_ids']) || !is_array($input['student_ids'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Student IDs are required and must be an array',
                    'data' => null
                ));
                return;
            }

            $student_ids = $input['student_ids'];

            // Validate each student ID
            $valid_ids = array();
            $invalid_ids = array();

            foreach ($student_ids as $id) {
                if (is_numeric($id) && $id > 0) {
                    $valid_ids[] = (int)$id;
                } else {
                    $invalid_ids[] = $id;
                }
            }

            if (!empty($invalid_ids)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid student IDs found',
                    'data' => array(
                        'invalid_ids' => $invalid_ids
                    )
                ));
                return;
            }

            // Check if students exist
            $existing_students = array();
            $non_existing_ids = array();

            foreach ($valid_ids as $id) {
                $student = $this->student_model->get($id);
                if (!empty($student)) {
                    $existing_students[] = array(
                        'id' => $student['id'],
                        'admission_no' => $student['admission_no'],
                        'firstname' => $student['firstname'],
                        'lastname' => $student['lastname'],
                        'class_id' => $student['class_id'],
                        'section_id' => $student['section_id']
                    );
                } else {
                    $non_existing_ids[] = $id;
                }
            }

            if (empty($existing_students)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'No valid students found for deletion',
                    'data' => array(
                        'non_existing_ids' => $non_existing_ids
                    )
                ));
                return;
            }

            // Optional: Check for confirmation flag
            $confirmed = isset($input['confirmed']) ? (bool)$input['confirmed'] : false;

            if (!$confirmed) {
                json_output(200, array(
                    'status' => 0,
                    'message' => 'Confirmation required for bulk deletion',
                    'data' => array(
                        'students_to_delete' => $existing_students,
                        'total_count' => count($existing_students),
                        'non_existing_ids' => $non_existing_ids,
                        'confirmation_required' => true,
                        'warning' => 'This action will permanently delete the selected students and all their related data. Please confirm by setting "confirmed": true in your request.'
                    )
                ));
                return;
            }

            // Perform bulk deletion
            $deleted_ids = array_column($existing_students, 'id');
            
            // Log the deletion attempt
            log_message('info', 'Bulk delete attempt for student IDs: ' . implode(', ', $deleted_ids));

            // Perform the bulk deletion
            $deletion_result = $this->student_model->bulkdelete($deleted_ids);

            // Prepare response
            $response_data = array(
                'deleted_students' => $existing_students,
                'total_deleted' => count($existing_students),
                'deletion_timestamp' => date('Y-m-d H:i:s')
            );

            if (!empty($non_existing_ids)) {
                $response_data['non_existing_ids'] = $non_existing_ids;
                $response_data['warning'] = 'Some student IDs were not found and were skipped';
            }

            json_output(200, array(
                'status' => 1,
                'message' => 'Bulk deletion completed successfully',
                'data' => $response_data
            ));

        } catch (Exception $e) {
            log_message('error', 'Bulk Delete API Students Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred during bulk deletion',
                'data' => null
            ));
        }
    }

    /**
     * Validate bulk delete request
     * 
     * Validates student IDs and provides information about what will be deleted
     * without actually performing the deletion.
     * 
     * @return void Outputs JSON response
     */
    public function validate()
    {
        try {
            // Validate request method
            if ($this->input->method() !== 'post') {
                json_output(405, array(
                    'status' => 0,
                    'message' => 'Method not allowed. Use POST method.',
                    'data' => null
                ));
                return;
            }

            // Validate required headers
            if (!$this->validate_headers()) {
                json_output(401, array(
                    'status' => 0,
                    'message' => 'Unauthorized access. Invalid headers.',
                    'data' => null
                ));
                return;
            }

            // Get input data
            $input = json_decode($this->input->raw_input_stream, true);

            // Validate student IDs
            if (empty($input['student_ids']) || !is_array($input['student_ids'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Student IDs are required and must be an array',
                    'data' => null
                ));
                return;
            }

            $student_ids = $input['student_ids'];

            // Validate each student ID and get student details
            $valid_students = array();
            $invalid_ids = array();
            $non_existing_ids = array();

            foreach ($student_ids as $id) {
                if (!is_numeric($id) || $id <= 0) {
                    $invalid_ids[] = $id;
                    continue;
                }

                $student = $this->student_model->get((int)$id);
                if (!empty($student)) {
                    // Get class and section names
                    $class_info = $this->class_model->get($student['class_id']);
                    $section_info = $this->section_model->get($student['section_id']);

                    $valid_students[] = array(
                        'id' => $student['id'],
                        'admission_no' => $student['admission_no'],
                        'full_name' => trim($student['firstname'] . ' ' . $student['middlename'] . ' ' . $student['lastname']),
                        'firstname' => $student['firstname'],
                        'middlename' => $student['middlename'],
                        'lastname' => $student['lastname'],
                        'class_info' => array(
                            'class_id' => $student['class_id'],
                            'class_name' => !empty($class_info) ? $class_info['class'] : 'Unknown',
                            'section_id' => $student['section_id'],
                            'section_name' => !empty($section_info) ? $section_info['section'] : 'Unknown'
                        ),
                        'email' => $student['email'],
                        'mobileno' => $student['mobileno'],
                        'father_name' => $student['father_name'],
                        'is_active' => $student['is_active']
                    );
                } else {
                    $non_existing_ids[] = (int)$id;
                }
            }

            // Prepare validation summary
            $validation_summary = array(
                'total_requested' => count($student_ids),
                'valid_students' => count($valid_students),
                'invalid_ids' => count($invalid_ids),
                'non_existing_ids' => count($non_existing_ids)
            );

            $response_data = array(
                'validation_summary' => $validation_summary,
                'students_found' => $valid_students
            );

            if (!empty($invalid_ids)) {
                $response_data['invalid_ids'] = $invalid_ids;
            }

            if (!empty($non_existing_ids)) {
                $response_data['non_existing_ids'] = $non_existing_ids;
            }

            // Add warnings if any
            $warnings = array();
            if (!empty($invalid_ids)) {
                $warnings[] = 'Some IDs are invalid (not numeric or negative)';
            }
            if (!empty($non_existing_ids)) {
                $warnings[] = 'Some student IDs do not exist in the database';
            }
            if (empty($valid_students)) {
                $warnings[] = 'No valid students found for deletion';
            }

            if (!empty($warnings)) {
                $response_data['warnings'] = $warnings;
            }

            $status = empty($valid_students) ? 0 : 1;
            $message = empty($valid_students) ? 
                'Validation failed - no valid students found' : 
                'Validation completed successfully';

            json_output(200, array(
                'status' => $status,
                'message' => $message,
                'data' => $response_data
            ));

        } catch (Exception $e) {
            log_message('error', 'Bulk Delete API Validate Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred during validation',
                'data' => null
            ));
        }
    }
}
