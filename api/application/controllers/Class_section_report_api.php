<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Section Report API Controller
 * 
 * Provides API endpoints for retrieving class and section information with student counts.
 * This API mirrors the functionality of the web interface at /report/classsectionreport
 * and provides flexible filtering capabilities by class, section, and session.
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Report APIs
 * @author     SMS Development Team
 * @version    1.0.0
 */
class Class_section_report_api extends CI_Controller
{
    /**
     * Constructor
     *
     * Loads required models and helpers for the API
     */
    public function __construct()
    {
        parent::__construct();

        // Load required models - IMPORTANT: Load setting_model BEFORE others
        $this->load->model('setting_model');
        $this->load->model('auth_model');
        $this->load->model('classsection_model');
        $this->load->model('class_model');
        $this->load->model('section_model');
        
        // Load helpers
        $this->load->helper('url');
        $this->load->helper('security');
    }

    /**
     * Filter Class Section Report
     * 
     * @method POST
     * @route  /api/class-section-report/filter
     * 
     * @param  int|array $class_id    Optional. Filter by specific class ID(s)
     * @param  int|array $section_id  Optional. Filter by specific section ID(s)
     * @param  int       $session_id  Optional. Filter by session (defaults to current session)
     * 
     * @return JSON Response with status, message, filters_applied, total_records, summary, data, and timestamp
     */
    public function filter()
    {
        try {
            // Check request method
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

            // Check authentication
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
            $_POST = json_decode(file_get_contents("php://input"), true);
            if ($_POST === null) {
                $_POST = array();
            }

            // Get filter parameters (all optional)
            $class_id = $this->input->post('class_id');
            $section_id = $this->input->post('section_id');
            $session_id = $this->input->post('session_id');

            // Use current session if not provided
            if (empty($session_id)) {
                $session_id = $this->setting_model->getCurrentSession();
            }

            // Handle both single values and arrays for multi-select
            if (!is_array($class_id) && !empty($class_id)) {
                $class_id = array($class_id);
            }
            if (!is_array($section_id) && !empty($section_id)) {
                $section_id = array($section_id);
            }

            // Get class section report data directly (avoiding user restrictions)
            $data = $this->getClassSectionStudentCountForAPI();

            // Apply filters if provided
            if (!empty($data)) {
                $filtered_data = array();
                
                foreach ($data as $record) {
                    $include_record = true;
                    
                    // Filter by class_id if provided
                    if (!empty($class_id) && !in_array($record->class_id, $class_id)) {
                        $include_record = false;
                    }
                    
                    // Filter by section_id if provided
                    if (!empty($section_id) && !in_array($record->section_id, $section_id)) {
                        $include_record = false;
                    }
                    
                    if ($include_record) {
                        $filtered_data[] = $record;
                    }
                }
                
                $data = $filtered_data;
            }

            // Calculate summary statistics
            $summary = [
                'total_classes' => 0,
                'total_sections' => count($data),
                'total_students' => 0,
                'active_classes' => 0,
                'active_sections' => 0
            ];

            $unique_classes = array();
            if (!empty($data)) {
                foreach ($data as $record) {
                    $summary['total_students'] += (int)$record->student_count;
                    
                    // Track unique classes
                    if (!in_array($record->class_id, $unique_classes)) {
                        $unique_classes[] = $record->class_id;
                        $summary['total_classes']++;
                        $summary['active_classes']++;
                    }
                    
                    $summary['active_sections']++;
                }
            }

            // Prepare response
            $response = [
                'status' => 1,
                'message' => 'Class section report retrieved successfully',
                'filters_applied' => [
                    'class_id' => $class_id,
                    'section_id' => $section_id,
                    'session_id' => (int)$session_id
                ],
                'total_records' => count($data),
                'summary' => $summary,
                'data' => $data,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Class Section Report API Filter Error: ' . $e->getMessage());
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 0,
                    'message' => 'Internal server error occurred',
                    'error' => $e->getMessage(),
                    'timestamp' => date('Y-m-d H:i:s')
                ]));
        }
    }

    /**
     * List All Class Section Data
     * 
     * @method POST
     * @route  /api/class-section-report/list
     * 
     * @return JSON Response with status, message, session_id, total_records, summary, data, and timestamp
     */
    public function list()
    {
        try {
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

            // Get all class section data for current session (no filters)
            $data = $this->getClassSectionStudentCountForAPI();

            // Calculate summary statistics
            $summary = [
                'total_classes' => 0,
                'total_sections' => count($data),
                'total_students' => 0,
                'active_classes' => 0,
                'active_sections' => 0
            ];

            $unique_classes = array();
            if (!empty($data)) {
                foreach ($data as $record) {
                    $summary['total_students'] += (int)$record->student_count;
                    
                    // Track unique classes
                    if (!in_array($record->class_id, $unique_classes)) {
                        $unique_classes[] = $record->class_id;
                        $summary['total_classes']++;
                        $summary['active_classes']++;
                    }
                    
                    $summary['active_sections']++;
                }
            }

            // Prepare response
            $response = [
                'status' => 1,
                'message' => 'Class section report retrieved successfully',
                'session_id' => (int)$session_id,
                'total_records' => count($data),
                'summary' => $summary,
                'data' => $data,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Class Section Report API List Error: ' . $e->getMessage());
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 0,
                    'message' => 'Internal server error occurred',
                    'error' => $e->getMessage(),
                    'timestamp' => date('Y-m-d H:i:s')
                ]));
        }
    }

    /**
     * Get Class Section Student Count for API
     *
     * This method provides a simplified version of getClassSectionStudentCount()
     * without user restrictions, suitable for API usage.
     *
     * @return array Array of class section records with student counts
     */
    private function getClassSectionStudentCountForAPI()
    {
        $session_id = $this->setting_model->getCurrentSession();

        $query = "SELECT
                    class_sections.id,
                    class_sections.class_id,
                    class_sections.section_id,
                    classes.class,
                    sections.section,
                    (SELECT COUNT(*)
                     FROM student_session
                     INNER JOIN students ON students.id = student_session.student_id
                     WHERE student_session.class_id = classes.id
                       AND student_session.section_id = sections.id
                       AND students.is_active = 'yes'
                       AND student_session.session_id = ?) as student_count
                  FROM class_sections
                  INNER JOIN classes ON classes.id = class_sections.class_id
                  INNER JOIN sections ON sections.id = class_sections.section_id
                  ORDER BY classes.class ASC, sections.section ASC";

        $result = $this->db->query($query, array($session_id));
        return $result->result();
    }
}
