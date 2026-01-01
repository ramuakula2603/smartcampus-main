<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Admission Report API Controller
 * 
 * This controller provides API endpoints for admission report filtering and retrieval.
 * It handles filtering by class and admission year with graceful handling of null/empty parameters.
 * 
 * @package    Student Management System
 * @subpackage API Controllers
 * @category   Webservices
 * @author     School Management System
 * @version    1.0.0
 */
class Admission_report_api extends CI_Controller
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
     * Filter admission report
     * POST /admission-report/filter
     * 
     * Retrieves admission report data based on optional filter parameters.
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
                $year = $this->input->post('year');
                $session_id = $this->input->post('session_id');

                // Handle both single values and arrays for multi-select
                // Convert single values to arrays for consistent processing
                if (!is_array($class_id)) {
                    $class_id = !empty($class_id) ? array($class_id) : array();
                }
                if (!is_array($year)) {
                    $year = !empty($year) ? array($year) : array();
                }

                // Remove empty values from arrays
                $class_id = array_filter($class_id, function($value) { 
                    return !empty($value) && $value !== null && $value !== ''; 
                });
                $year = array_filter($year, function($value) { 
                    return !empty($value) && $value !== null && $value !== ''; 
                });

                // Convert empty arrays to null for the model
                $class_id = !empty($class_id) ? $class_id : null;
                $year = !empty($year) ? $year : null;

                // Handle session_id - if not provided, use current session
                if (empty($session_id)) {
                    $session_id = $this->setting_model->getCurrentSession();
                }

                // Get admission report data using the model
                $result = $this->student_model->getAdmissionReportByFilters(
                    $class_id, 
                    $year, 
                    $session_id
                );

                // Format the response data
                $formatted_data = array();
                if (!empty($result)) {
                    foreach ($result as $student) {
                        $formatted_data[] = array(
                            'id' => $student['id'],
                            'admission_no' => $student['admission_no'],
                            'admission_date' => $student['admission_date'],
                            'firstname' => $student['firstname'],
                            'middlename' => $student['middlename'],
                            'lastname' => $student['lastname'],
                            'class_id' => $student['class_id'],
                            'class' => $student['class'],
                            'section_id' => $student['section_id'],
                            'section' => $student['section'],
                            'session_id' => $student['session_id'],
                            'session' => isset($student['session']) ? $student['session'] : null,
                            'mobileno' => isset($student['mobileno']) ? $student['mobileno'] : null,
                            'guardian_name' => isset($student['guardian_name']) ? $student['guardian_name'] : null,
                            'guardian_relation' => isset($student['guardian_relation']) ? $student['guardian_relation'] : null,
                            'guardian_phone' => isset($student['guardian_phone']) ? $student['guardian_phone'] : null,
                            'is_active' => $student['is_active']
                        );
                    }
                }

                // Prepare filters applied info
                $filters_applied = array();
                if ($class_id !== null) {
                    $filters_applied['class_id'] = is_array($class_id) ? $class_id : array($class_id);
                }
                if ($year !== null) {
                    $filters_applied['year'] = is_array($year) ? $year : array($year);
                }
                $filters_applied['session_id'] = $session_id;

                // Build response
                $response = array(
                    'status' => 1,
                    'message' => 'Admission report retrieved successfully',
                    'filters_applied' => $filters_applied,
                    'total_records' => count($formatted_data),
                    'data' => $formatted_data,
                    'timestamp' => date('Y-m-d H:i:s')
                );

                json_output(200, $response);

            } catch (Exception $e) {
                log_message('error', 'Admission Report API Filter Error: ' . $e->getMessage());
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
     * List all admissions (no filters)
     * POST /admission-report/list
     * 
     * Retrieves all active students with admission information for the current session.
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

                // Get all students with admission info for current session
                $result = $this->student_model->getAdmissionReportByFilters(null, null, $session_id);

                // Format the response data
                $formatted_data = array();
                if (!empty($result)) {
                    foreach ($result as $student) {
                        $formatted_data[] = array(
                            'id' => $student['id'],
                            'admission_no' => $student['admission_no'],
                            'admission_date' => $student['admission_date'],
                            'firstname' => $student['firstname'],
                            'middlename' => $student['middlename'],
                            'lastname' => $student['lastname'],
                            'class_id' => $student['class_id'],
                            'class' => $student['class'],
                            'section_id' => $student['section_id'],
                            'section' => $student['section'],
                            'session_id' => $student['session_id'],
                            'session' => isset($student['session']) ? $student['session'] : null,
                            'mobileno' => isset($student['mobileno']) ? $student['mobileno'] : null,
                            'guardian_name' => isset($student['guardian_name']) ? $student['guardian_name'] : null,
                            'guardian_relation' => isset($student['guardian_relation']) ? $student['guardian_relation'] : null,
                            'guardian_phone' => isset($student['guardian_phone']) ? $student['guardian_phone'] : null,
                            'is_active' => $student['is_active']
                        );
                    }
                }

                // Build response
                $response = array(
                    'status' => 1,
                    'message' => 'Admission report retrieved successfully',
                    'session_id' => $session_id,
                    'total_records' => count($formatted_data),
                    'data' => $formatted_data,
                    'timestamp' => date('Y-m-d H:i:s')
                );

                json_output(200, $response);

            } catch (Exception $e) {
                log_message('error', 'Admission Report API List Error: ' . $e->getMessage());
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

