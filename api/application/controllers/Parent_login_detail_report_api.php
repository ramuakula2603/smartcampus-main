<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Parent Login Detail Report API Controller
 * 
 * Provides API endpoints for retrieving parent login credential information
 * with flexible filtering capabilities by class and section.
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Report APIs
 * @author     SMS Development Team
 * @version    1.0.0
 */
class Parent_login_detail_report_api extends CI_Controller
{
    /**
     * Constructor
     * 
     * Loads required models and helpers for the API
     */
    public function __construct()
    {
        parent::__construct();
        
        // Load required models
        $this->load->model('student_model');
        $this->load->model('user_model');
        $this->load->model('setting_model');
        $this->load->model('auth_model');
        
        // Load helpers
        $this->load->helper('url');
        $this->load->helper('security');
    }

    /**
     * Filter Parent Login Detail Report
     * 
     * Retrieves parent login credentials based on optional filter parameters.
     * Handles null/empty parameters gracefully by returning all records when no filters are provided.
     * Supports both single values and arrays for multi-select functionality.
     * 
     * @method POST
     * @route  /api/parent-login-detail-report/filter
     * 
     * @param  int|array  $class_id    Optional. Class ID(s) to filter by
     * @param  int|array  $section_id  Optional. Section ID(s) to filter by
     * @param  int        $session_id  Optional. Session ID (defaults to current session)
     * 
     * @return JSON Response with status, message, filters_applied, total_records, data, and timestamp
     * 
     * @example
     * Request Body:
     * {
     *   "class_id": 1,
     *   "section_id": 2
     * }
     * 
     * Response:
     * {
     *   "status": 1,
     *   "message": "Parent login detail report retrieved successfully",
     *   "filters_applied": {
     *     "class_id": [1],
     *     "section_id": [2],
     *     "session_id": 18
     *   },
     *   "total_records": 25,
     *   "data": [...],
     *   "timestamp": "2025-10-07 10:30:45"
     * }
     */
    public function filter()
    {
        try {
            // Validate request method
            if ($this->input->method() !== 'post') {
                $this->output
                    ->set_status_header(400)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 0,
                        'message' => 'Bad request. Only POST method allowed.'
                    ]));
                return;
            }

            // Validate authentication
            if (!$this->auth_model->check_auth_client()) {
                $this->output
                    ->set_status_header(401)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 0,
                        'message' => 'Unauthorized access'
                    ]));
                return;
            }

            // Get JSON input
            $json_input = json_decode(file_get_contents('php://input'), true);
            
            // Extract filter parameters
            $class_id = isset($json_input['class_id']) ? $json_input['class_id'] : null;
            $section_id = isset($json_input['section_id']) ? $json_input['section_id'] : null;
            $session_id = isset($json_input['session_id']) ? $json_input['session_id'] : null;

            // Convert single values to arrays for consistent handling
            if (!is_array($class_id)) {
                $class_id = !empty($class_id) ? array($class_id) : array();
            }
            if (!is_array($section_id)) {
                $section_id = !empty($section_id) ? array($section_id) : array();
            }

            // Filter out empty values from arrays
            $class_id = array_filter($class_id, function($value) { 
                return !empty($value) && $value !== null && $value !== ''; 
            });
            $section_id = array_filter($section_id, function($value) { 
                return !empty($value) && $value !== null && $value !== ''; 
            });

            // Convert empty arrays to null for graceful handling
            $class_id = !empty($class_id) ? $class_id : null;
            $section_id = !empty($section_id) ? $section_id : null;

            // If session_id is not provided, use current session
            if (empty($session_id)) {
                $session_id = $this->setting_model->getCurrentSession();
            }

            // Get parent login detail report data from model
            $data = $this->student_model->getParentLoginDetailReportByFilters($class_id, $section_id, $session_id);

            // Prepare response
            $response = [
                'status' => 1,
                'message' => 'Parent login detail report retrieved successfully',
                'filters_applied' => [
                    'class_id' => $class_id,
                    'section_id' => $section_id,
                    'session_id' => (int)$session_id
                ],
                'total_records' => count($data),
                'data' => $data,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Parent Login Detail Report API Filter Error: ' . $e->getMessage());
            
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 0,
                    'message' => 'Internal server error occurred',
                    'error' => $e->getMessage(),
                    'data' => null
                ]));
        }
    }

    /**
     * List All Parent Login Details
     * 
     * Retrieves all parent login credentials for the current session.
     * No filter parameters required - returns all active students with parent credentials.
     * 
     * @method POST
     * @route  /api/parent-login-detail-report/list
     * 
     * @return JSON Response with status, message, session_id, total_records, data, and timestamp
     * 
     * @example
     * Request Body:
     * {}
     * 
     * Response:
     * {
     *   "status": 1,
     *   "message": "Parent login detail report retrieved successfully",
     *   "session_id": 18,
     *   "total_records": 150,
     *   "data": [...],
     *   "timestamp": "2025-10-07 10:30:45"
     * }
     */
    public function list()
    {
        try {
            // Validate request method
            if ($this->input->method() !== 'post') {
                $this->output
                    ->set_status_header(400)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 0,
                        'message' => 'Bad request. Only POST method allowed.'
                    ]));
                return;
            }

            // Validate authentication
            if (!$this->auth_model->check_auth_client()) {
                $this->output
                    ->set_status_header(401)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 0,
                        'message' => 'Unauthorized access'
                    ]));
                return;
            }

            // Get current session
            $session_id = $this->setting_model->getCurrentSession();

            // Get all parent login details for current session (no filters)
            $data = $this->student_model->getParentLoginDetailReportByFilters(null, null, $session_id);

            // Prepare response
            $response = [
                'status' => 1,
                'message' => 'Parent login detail report retrieved successfully',
                'session_id' => (int)$session_id,
                'total_records' => count($data),
                'data' => $data,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Parent Login Detail Report API List Error: ' . $e->getMessage());
            
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 0,
                    'message' => 'Internal server error occurred',
                    'error' => $e->getMessage(),
                    'data' => null
                ]));
        }
    }
}

