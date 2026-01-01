<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Boys Girls Ratio Report API Controller
 * 
 * Provides API endpoints for retrieving boys/girls ratio statistics
 * with flexible filtering capabilities by class and section.
 * Returns aggregated counts of male and female students grouped by class and section.
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Report APIs
 * @author     SMS Development Team
 * @version    1.0.0
 */
class Boys_girls_ratio_report_api extends CI_Controller
{
    /**
     * Constructor
     * 
     * Loads required models and helpers for the API
     */
    public function __construct()
    {
        parent::__construct();
        
        // Load required models - IMPORTANT: Load setting_model FIRST
        $this->load->model('setting_model');
        $this->load->model('auth_model');
        $this->load->model('student_model');
        
        // Load helpers
        $this->load->helper('url');
        $this->load->helper('security');
    }

    /**
     * Filter Boys Girls Ratio Report
     * 
     * Retrieves boys/girls ratio statistics based on optional filter parameters.
     * Returns aggregated counts of male and female students grouped by class and section.
     * Handles null/empty parameters gracefully by returning all records when no filters are provided.
     * Supports both single values and arrays for multi-select functionality.
     * 
     * @method POST
     * @route  /api/boys-girls-ratio-report/filter
     * 
     * @param  int|array  $class_id    Optional. Class ID(s) to filter by
     * @param  int|array  $section_id  Optional. Section ID(s) to filter by
     * @param  int        $session_id  Optional. Session ID (defaults to current session)
     * 
     * @return JSON Response with status, message, filters_applied, total_records, summary, data, and timestamp
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
     *   "message": "Boys girls ratio report retrieved successfully",
     *   "filters_applied": {
     *     "class_id": [1],
     *     "section_id": [2],
     *     "session_id": 18
     *   },
     *   "total_records": 1,
     *   "summary": {
     *     "total_students": 45,
     *     "total_boys": 25,
     *     "total_girls": 20,
     *     "boys_girls_ratio": "1:0.8"
     *   },
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

            // Get boys girls ratio report data from model
            $data = $this->student_model->getBoysGirlsRatioReportByFilters($class_id, $section_id, $session_id);

            // Calculate summary statistics
            $total_students = 0;
            $total_boys = 0;
            $total_girls = 0;

            foreach ($data as $row) {
                $total_students += $row['total_student'];
                $total_boys += $row['male'];
                $total_girls += $row['female'];
            }

            // Calculate boys:girls ratio
            $boys_girls_ratio = $this->calculateRatio($total_boys, $total_girls);

            // Prepare response
            $response = [
                'status' => 1,
                'message' => 'Boys girls ratio report retrieved successfully',
                'filters_applied' => [
                    'class_id' => $class_id,
                    'section_id' => $section_id,
                    'session_id' => (int)$session_id
                ],
                'total_records' => count($data),
                'summary' => [
                    'total_students' => $total_students,
                    'total_boys' => $total_boys,
                    'total_girls' => $total_girls,
                    'boys_girls_ratio' => $boys_girls_ratio
                ],
                'data' => $data,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Boys Girls Ratio Report API Filter Error: ' . $e->getMessage());
            
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
     * List All Boys Girls Ratio Data
     * 
     * Retrieves all boys/girls ratio statistics for the current session.
     * No filter parameters required - returns aggregated data for all classes and sections.
     * 
     * @method POST
     * @route  /api/boys-girls-ratio-report/list
     * 
     * @return JSON Response with status, message, session_id, total_records, summary, data, and timestamp
     * 
     * @example
     * Request Body:
     * {}
     * 
     * Response:
     * {
     *   "status": 1,
     *   "message": "Boys girls ratio report retrieved successfully",
     *   "session_id": 18,
     *   "total_records": 15,
     *   "summary": {
     *     "total_students": 450,
     *     "total_boys": 250,
     *     "total_girls": 200,
     *     "boys_girls_ratio": "1:0.8"
     *   },
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

            // Get all boys girls ratio data for current session (no filters)
            $data = $this->student_model->getBoysGirlsRatioReportByFilters(null, null, $session_id);

            // Calculate summary statistics
            $total_students = 0;
            $total_boys = 0;
            $total_girls = 0;

            foreach ($data as $row) {
                $total_students += $row['total_student'];
                $total_boys += $row['male'];
                $total_girls += $row['female'];
            }

            // Calculate boys:girls ratio
            $boys_girls_ratio = $this->calculateRatio($total_boys, $total_girls);

            // Prepare response
            $response = [
                'status' => 1,
                'message' => 'Boys girls ratio report retrieved successfully',
                'session_id' => (int)$session_id,
                'total_records' => count($data),
                'summary' => [
                    'total_students' => $total_students,
                    'total_boys' => $total_boys,
                    'total_girls' => $total_girls,
                    'boys_girls_ratio' => $boys_girls_ratio
                ],
                'data' => $data,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Boys Girls Ratio Report API List Error: ' . $e->getMessage());
            
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
     * Calculate Ratio
     * 
     * Helper method to calculate ratio between two numbers
     * Returns ratio in format "1:X" where X is rounded to 2 decimal places
     * 
     * @param int $num1 First number (boys)
     * @param int $num2 Second number (girls)
     * @return string Ratio in format "1:X" or "0:0" if both are zero
     */
    private function calculateRatio($num1, $num2)
    {
        if ($num2 > 0 && $num1 > 0) {
            $ratio = round($num2 / $num1, 2);
            return "1:" . $ratio;
        } elseif ($num1 == 0 && $num2 > 0) {
            return "0:1";
        } elseif ($num1 > 0 && $num2 == 0) {
            return "1:0";
        } else {
            return "0:0";
        }
    }
}

