<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Fee Collection Filters API Controller
 * 
 * This controller provides API endpoints for retrieving filter options
 * for fee collection reports. It returns sessions, classes, sections,
 * fee groups, fee types, staff collectors, and grouping options.
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Webservices
 * @author     School Management System
 * @version    1.0.0
 */
class Fee_collection_filters_api extends CI_Controller
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
                'fee_collection_filters_model',
                'setting_model'
            ));
        } catch (Exception $e) {
            log_message('error', 'Error loading models: ' . $e->getMessage());
            $this->setting_model = null;
            $this->fee_collection_filters_model = null;
        }

        // Get school settings
        try {
            if ($this->setting_model !== null) {
                $this->sch_setting_detail = $this->setting_model->getSetting();

                // Set timezone
                if (isset($this->sch_setting_detail->timezone) && $this->sch_setting_detail->timezone != "") {
                    date_default_timezone_set($this->sch_setting_detail->timezone);
                } else {
                    date_default_timezone_set('UTC');
                }
            } else {
                log_message('warning', 'Setting model not loaded, using default timezone');
                date_default_timezone_set('UTC');
                $this->sch_setting_detail = null;
            }
        } catch (Exception $e) {
            log_message('error', 'Error loading school settings: ' . $e->getMessage());
            date_default_timezone_set('UTC');
            $this->sch_setting_detail = null;
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
     * Get filter options for fee collection reports (HIERARCHICAL)
     *
     * Returns hierarchical structure: Sessions → Classes → Sections
     * Each session contains its classes, each class contains its sections.
     * Supports filtering by session_id, class_id, and section_id.
     *
     * @return void Outputs JSON response
     */
    public function get()
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

            // Get input data (may be empty for getting all options)
            $input = json_decode($this->input->raw_input_stream, true);

            // Extract filter parameters (all optional)
            $session_id = isset($input['session_id']) ? $input['session_id'] : null;
            $class_id = isset($input['class_id']) ? $input['class_id'] : null;
            $section_id = isset($input['section_id']) ? $input['section_id'] : null;

            // Log the received parameters for debugging
            log_message('debug', 'Fee Collection Filters API: Received session_id = ' . var_export($session_id, true));
            log_message('debug', 'Fee Collection Filters API: Received class_id = ' . var_export($class_id, true));
            log_message('debug', 'Fee Collection Filters API: Received section_id = ' . var_export($section_id, true));

            // Get hierarchical data (without students)
            $hierarchical_data = $this->fee_collection_filters_model->get_hierarchical_data(
                $session_id,
                $class_id,
                $section_id,
                false // Don't include students in this endpoint
            );

            // Get other filter options
            $fee_groups = $this->fee_collection_filters_model->get_fee_groups();
            $fee_types = $this->fee_collection_filters_model->get_fee_types();
            $collect_by = $this->fee_collection_filters_model->get_staff_collectors();
            $group_by_options = array('class', 'collect', 'mode');

            // Format response data
            $response_data = array(
                'sessions' => $hierarchical_data,
                'fee_groups' => $fee_groups,
                'fee_types' => $fee_types,
                'collect_by' => $collect_by,
                'group_by_options' => $group_by_options
            );

            json_output(200, array(
                'status' => 1,
                'message' => 'Filter options retrieved successfully',
                'data' => $response_data
            ));

        } catch (Exception $e) {
            log_message('error', 'Fee Collection Filters API Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Get hierarchical academic data with students
     *
     * Returns complete hierarchical structure: Sessions → Classes → Sections → Students
     * Each session contains its classes, each class contains its sections,
     * and each section contains its enrolled students.
     * Supports filtering by session_id, class_id, and section_id.
     *
     * @return void Outputs JSON response
     */
    public function get_hierarchy()
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

            // Get input data (may be empty for getting all data)
            $input = json_decode($this->input->raw_input_stream, true);

            // Extract filter parameters (all optional)
            $session_id = isset($input['session_id']) ? $input['session_id'] : null;
            $class_id = isset($input['class_id']) ? $input['class_id'] : null;
            $section_id = isset($input['section_id']) ? $input['section_id'] : null;

            // Log the received parameters for debugging
            log_message('debug', 'Fee Collection Hierarchy API: Received session_id = ' . var_export($session_id, true));
            log_message('debug', 'Fee Collection Hierarchy API: Received class_id = ' . var_export($class_id, true));
            log_message('debug', 'Fee Collection Hierarchy API: Received section_id = ' . var_export($section_id, true));

            // Get hierarchical data with students
            $hierarchical_data = $this->fee_collection_filters_model->get_hierarchical_data(
                $session_id,
                $class_id,
                $section_id,
                true // Include students in this endpoint
            );

            // Calculate statistics
            $total_sessions = count($hierarchical_data);
            $total_classes = 0;
            $total_sections = 0;
            $total_students = 0;

            foreach ($hierarchical_data as $session) {
                $total_classes += count($session['classes']);
                foreach ($session['classes'] as $class) {
                    $total_sections += count($class['sections']);
                    foreach ($class['sections'] as $section) {
                        $total_students += count($section['students']);
                    }
                }
            }

            json_output(200, array(
                'status' => 1,
                'message' => 'Hierarchical academic data retrieved successfully',
                'filters_applied' => array(
                    'session_id' => $session_id,
                    'class_id' => $class_id,
                    'section_id' => $section_id
                ),
                'statistics' => array(
                    'total_sessions' => $total_sessions,
                    'total_classes' => $total_classes,
                    'total_sections' => $total_sections,
                    'total_students' => $total_students
                ),
                'data' => $hierarchical_data,
                'timestamp' => date('Y-m-d H:i:s')
            ));

        } catch (Exception $e) {
            log_message('error', 'Fee Collection Hierarchy API Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }
}

