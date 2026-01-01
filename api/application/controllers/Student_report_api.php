<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Student Report API Controller
 * 
 * This controller provides API endpoints for student report filtering and retrieval.
 * It handles filtering by class, section, session, and category with graceful handling
 * of null/empty parameters.
 * 
 * @package    Student Management System
 * @subpackage API Controllers
 * @category   Webservices
 * @author     School Management System
 * @version    1.0.0
 */
class Student_report_api extends CI_Controller
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
            $this->load->model('student_model');
            $this->load->model('setting_model');
            $this->load->model('auth_model');
        } catch (Exception $e) {
            log_message('error', 'Error loading models: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error: Failed to load required models',
                'data' => null
            ));
        }
    }

    /**
     * Filter student report
     * POST /student-report/filter
     * 
     * Retrieves student report data based on optional filter parameters.
     * Handles null/empty parameters gracefully by returning all records when filters are not provided.
     * 
     * @return void Outputs JSON response
     */
    public function filter()
    {
        // Check request method
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 0,
                'message' => 'Bad request. Only POST method allowed.'
            ));
        }

        // Check authentication
        $check_auth_client = $this->auth_model->check_auth_client();
        if ($check_auth_client == true) {
            try {
                // Get JSON input
                $_POST = json_decode(file_get_contents("php://input"), true);
                
                // Get filter parameters (all optional)
                $class_id = $this->input->post('class_id');
                $section_id = $this->input->post('section_id');
                $session_id = $this->input->post('session_id');
                $category_id = $this->input->post('category_id');

                // Handle both single values and arrays for multi-select
                // Convert single values to arrays for consistent processing
                if (!is_array($class_id)) {
                    $class_id = !empty($class_id) ? array($class_id) : array();
                }
                if (!is_array($section_id)) {
                    $section_id = !empty($section_id) ? array($section_id) : array();
                }
                if (!is_array($category_id)) {
                    $category_id = !empty($category_id) ? array($category_id) : array();
                }

                // Remove empty values from arrays
                $class_id = array_filter($class_id, function($value) { 
                    return !empty($value) && $value !== null && $value !== ''; 
                });
                $section_id = array_filter($section_id, function($value) { 
                    return !empty($value) && $value !== null && $value !== ''; 
                });
                $category_id = array_filter($category_id, function($value) { 
                    return !empty($value) && $value !== null && $value !== ''; 
                });

                // Convert empty arrays to null for the model
                $class_id = !empty($class_id) ? $class_id : null;
                $section_id = !empty($section_id) ? $section_id : null;
                $category_id = !empty($category_id) ? $category_id : null;

                // Handle session_id - if not provided, use current session
                if (empty($session_id)) {
                    $session_id = $this->setting_model->getCurrentSession();
                }

                // Get student report data using the model
                // Note: We're using a simplified approach that doesn't require gender and rte filters
                $result = $this->student_model->getStudentReportByFilters(
                    $class_id, 
                    $section_id, 
                    $category_id, 
                    $session_id
                );

                // Format the response data
                $formatted_data = array();
                if (!empty($result)) {
                    foreach ($result as $student) {
                        $formatted_data[] = array(
                            'id' => $student['id'],
                            'admission_no' => $student['admission_no'],
                            'roll_no' => $student['roll_no'],
                            'firstname' => $student['firstname'],
                            'middlename' => $student['middlename'],
                            'lastname' => $student['lastname'],
                            'class_id' => $student['class_id'],
                            'class' => $student['class'],
                            'section_id' => $student['section_id'],
                            'section' => $student['section'],
                            'category_id' => isset($student['category_id']) ? $student['category_id'] : null,
                            'category' => isset($student['category']) ? $student['category'] : null,
                            'father_name' => $student['father_name'],
                            'dob' => $student['dob'],
                            'gender' => $student['gender'],
                            'mobileno' => $student['mobileno'],
                            'email' => $student['email'],
                            'samagra_id' => isset($student['samagra_id']) ? $student['samagra_id'] : null,
                            'adhar_no' => isset($student['adhar_no']) ? $student['adhar_no'] : null,
                            'rte' => isset($student['rte']) ? $student['rte'] : null,
                            'guardian_name' => $student['guardian_name'],
                            'guardian_phone' => $student['guardian_phone'],
                            'guardian_relation' => isset($student['guardian_relation']) ? $student['guardian_relation'] : null,
                            'current_address' => isset($student['current_address']) ? $student['current_address'] : null,
                            'permanent_address' => isset($student['permanent_address']) ? $student['permanent_address'] : null,
                            'is_active' => $student['is_active']
                        );
                    }
                }

                // Prepare filters applied info
                $filters_applied = array();
                if ($class_id !== null) {
                    $filters_applied['class_id'] = is_array($class_id) ? $class_id : array($class_id);
                }
                if ($section_id !== null) {
                    $filters_applied['section_id'] = is_array($section_id) ? $section_id : array($section_id);
                }
                if ($category_id !== null) {
                    $filters_applied['category_id'] = is_array($category_id) ? $category_id : array($category_id);
                }
                $filters_applied['session_id'] = $session_id;

                // Build response
                $response = array(
                    'status' => 1,
                    'message' => 'Student report retrieved successfully',
                    'filters_applied' => $filters_applied,
                    'total_records' => count($formatted_data),
                    'data' => $formatted_data,
                    'timestamp' => date('Y-m-d H:i:s')
                );

                json_output(200, $response);

            } catch (Exception $e) {
                log_message('error', 'Student Report API Filter Error: ' . $e->getMessage());
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Internal server error occurred',
                    'error' => $e->getMessage(),
                    'data' => null
                ));
            }
        }
    }

    /**
     * List all students (no filters)
     * POST /student-report/list
     * 
     * Retrieves all active students for the current session.
     * 
     * @return void Outputs JSON response
     */
    public function list()
    {
        // Check request method
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 0,
                'message' => 'Bad request. Only POST method allowed.'
            ));
        }

        // Check authentication
        $check_auth_client = $this->auth_model->check_auth_client();
        if ($check_auth_client == true) {
            try {
                // Get current session
                $session_id = $this->setting_model->getCurrentSession();

                // Get all students for current session
                $result = $this->student_model->getStudentReportByFilters(null, null, null, $session_id);

                // Format the response data
                $formatted_data = array();
                if (!empty($result)) {
                    foreach ($result as $student) {
                        $formatted_data[] = array(
                            'id' => $student['id'],
                            'admission_no' => $student['admission_no'],
                            'roll_no' => $student['roll_no'],
                            'firstname' => $student['firstname'],
                            'middlename' => $student['middlename'],
                            'lastname' => $student['lastname'],
                            'class_id' => $student['class_id'],
                            'class' => $student['class'],
                            'section_id' => $student['section_id'],
                            'section' => $student['section'],
                            'category_id' => isset($student['category_id']) ? $student['category_id'] : null,
                            'category' => isset($student['category']) ? $student['category'] : null,
                            'father_name' => $student['father_name'],
                            'dob' => $student['dob'],
                            'gender' => $student['gender'],
                            'mobileno' => $student['mobileno'],
                            'email' => $student['email'],
                            'samagra_id' => isset($student['samagra_id']) ? $student['samagra_id'] : null,
                            'adhar_no' => isset($student['adhar_no']) ? $student['adhar_no'] : null,
                            'rte' => isset($student['rte']) ? $student['rte'] : null,
                            'guardian_name' => $student['guardian_name'],
                            'guardian_phone' => $student['guardian_phone'],
                            'guardian_relation' => isset($student['guardian_relation']) ? $student['guardian_relation'] : null,
                            'current_address' => isset($student['current_address']) ? $student['current_address'] : null,
                            'permanent_address' => isset($student['permanent_address']) ? $student['permanent_address'] : null,
                            'is_active' => $student['is_active']
                        );
                    }
                }

                // Build response
                $response = array(
                    'status' => 1,
                    'message' => 'Student report retrieved successfully',
                    'session_id' => $session_id,
                    'total_records' => count($formatted_data),
                    'data' => $formatted_data,
                    'timestamp' => date('Y-m-d H:i:s')
                );

                json_output(200, $response);

            } catch (Exception $e) {
                log_message('error', 'Student Report API List Error: ' . $e->getMessage());
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Internal server error occurred',
                    'error' => $e->getMessage(),
                    'data' => null
                ));
            }
        }
    }
}

