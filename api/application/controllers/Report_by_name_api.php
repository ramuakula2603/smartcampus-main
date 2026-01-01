<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Report By Name API Controller
 * 
 * Provides API endpoints for searching students by name and retrieving their fee reports
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Finance Reports
 * @author     SMS Development Team
 * @version    1.0.0
 */
class Report_by_name_api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        // Disable error display - API should only return JSON
        ini_set('display_errors', 0);
        error_reporting(0);

        // Load required models in correct order
        $this->load->model('setting_model');
        $this->load->model('auth_model');
        $this->load->model('studentfeemaster_model');
        $this->load->model('student_model');
        $this->load->model('class_model');
        $this->load->model('session_model');
        $this->load->model('module_model');

        // Load library
        $this->load->library('customlib');

        // Load helper
        $this->load->helper('custom');
    }

    /**
     * Filter endpoint - Search students by name and get their fee reports
     * POST /api/report-by-name/filter
     */
    public function filter()
    {
        // Authenticate request
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

        try {
            // Get JSON input
            $json_input = json_decode($this->input->raw_input_stream, true);

            // Get filter parameters
            $search_text = (isset($json_input['search_text']) && $json_input['search_text'] !== '') ? $json_input['search_text'] : null;
            $class_id = (isset($json_input['class_id']) && $json_input['class_id'] !== '') ? $json_input['class_id'] : null;
            $section_id = (isset($json_input['section_id']) && $json_input['section_id'] !== '') ? $json_input['section_id'] : null;
            $student_id = (isset($json_input['student_id']) && $json_input['student_id'] !== '') ? $json_input['student_id'] : null;
            $session_id = (isset($json_input['session_id']) && $json_input['session_id'] !== '') ? $json_input['session_id'] : null;

            // If session_id is provided, use it to filter; otherwise use current session
            if ($session_id === null) {
                $session_id = $this->setting_model->getCurrentSession();
            }

            // Get detailed student fees using the same method as web page
            $student_due_fee = $this->studentfeemaster_model->getStudentFeesByClassSectionStudent($class_id, $section_id, $student_id, $session_id);

            // Add transport fees for each student
            if (!empty($student_due_fee)) {
                foreach ($student_due_fee as $key => $value) {
                    $transport_fees = array();

                    // Get student info to check for transport
                    $student_info = $this->student_model->get($value['student_id']);

                    if (!empty($student_info)) {
                        // Convert to array if it's an object
                        if (is_object($student_info)) {
                            $student_info = (array) $student_info;
                        }

                        $route_pickup_point_id = isset($student_info['route_pickup_point_id']) ? $student_info['route_pickup_point_id'] : null;
                        $student_session_id = $value['student_session_id'];

                        // Check if transport module is active
                        $module = $this->module_model->getPermissionByModulename('transport');

                        if (!empty($module) && isset($module['is_active']) && $module['is_active'] && $route_pickup_point_id) {
                            $transport_fees = $this->studentfeemaster_model->getStudentTransportFees($student_session_id, $route_pickup_point_id);
                        }
                    }

                    $student_due_fee[$key]['transport_fees'] = $transport_fees;
                }
            }

            // Convert to array for JSON output
            $student_array = array_values($student_due_fee);

            $response = [
                'status' => 1,
                'message' => 'Report by name retrieved successfully',
                'filters_applied' => [
                    'search_text' => $search_text,
                    'class_id' => $class_id,
                    'section_id' => $section_id,
                    'session_id' => $session_id
                ],
                'total_records' => count($student_array),
                'data' => $student_array,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 0,
                    'message' => 'Error retrieving report by name: ' . $e->getMessage()
                ]));
        }
    }

    /**
     * List endpoint - Get filter options
     * POST /api/report-by-name/list
     */
    public function list()
    {
        // Authenticate request
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

        try {
            // Get classes
            $classes = $this->class_model->get();
            
            // Get sessions
            $sessions = $this->session_model->get();

            $response = [
                'status' => 1,
                'message' => 'Filter options retrieved successfully',
                'data' => [
                    'classes' => $classes,
                    'sessions' => $sessions
                ],
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 0,
                    'message' => 'Error retrieving filter options: ' . $e->getMessage()
                ]));
        }
    }
}

