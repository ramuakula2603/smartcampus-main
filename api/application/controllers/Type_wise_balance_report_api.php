<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Type Wise Balance Report API Controller
 * 
 * This controller handles API requests for type-wise balance reports
 * showing fee balances categorized by fee type.
 * 
 * @package    CodeIgniter
 * @subpackage Controllers
 * @category   API
 */
class Type_wise_balance_report_api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Load required models - IMPORTANT: setting_model MUST be loaded FIRST
        $this->load->model('setting_model');
        $this->load->model('auth_model');
        $this->load->model('studentfeemaster_model');
        $this->load->model('session_model');
        $this->load->model('feegroup_model');
        $this->load->model('feetype_model');
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->database();
    }

    /**
     * Filter type wise balance report
     * 
     * POST /api/type-wise-balance-report/filter
     * 
     * Request body:
     * {
     *   "session_id": "1",
     *   "feetype_ids": ["1", "2"],
     *   "feegroup_ids": ["1"],
     *   "class_id": "1",
     *   "section_id": "2"
     * }
     * 
     * session_id and feetype_ids are required
     * Empty feetype_ids [] returns all fee types
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
            $json_input = json_decode($this->input->raw_input_stream, true);
            
            // Get filter parameters
            $session_id = isset($json_input['session_id']) ? $json_input['session_id'] : null;
            $feetype_ids = isset($json_input['feetype_ids']) ? $json_input['feetype_ids'] : [];
            $feegroup_ids = isset($json_input['feegroup_ids']) ? $json_input['feegroup_ids'] : null;
            $class_id = isset($json_input['class_id']) ? $json_input['class_id'] : null;
            $section_id = isset($json_input['section_id']) ? $json_input['section_id'] : null;

            // Validate required parameters
            if ($session_id === null) {
                $this->output
                    ->set_status_header(400)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 0,
                        'message' => 'session_id is required'
                    ]));
                return;
            }

            // Get type wise balance report data
            $results = $this->studentfeemaster_model->gettypewisereportt(
                $session_id,
                $feetype_ids,
                $feegroup_ids,
                $class_id,
                $section_id
            );

            $response = [
                'status' => 1,
                'message' => 'Type wise balance report retrieved successfully',
                'filters_applied' => [
                    'session_id' => $session_id,
                    'feetype_ids' => $feetype_ids,
                    'feegroup_ids' => $feegroup_ids,
                    'class_id' => $class_id,
                    'section_id' => $section_id
                ],
                'total_records' => count($results),
                'data' => $results,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Type Wise Balance Report API Error: ' . $e->getMessage());
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 0,
                    'message' => 'Internal server error',
                    'error' => $e->getMessage()
                ]));
        }
    }

    /**
     * List type wise balance report filter options
     * 
     * POST /api/type-wise-balance-report/list
     * 
     * Returns available sessions, fee groups, and fee types for filtering
     */
    public function list()
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

            // Get filter options
            $sessions = $this->session_model->get();
            $feegroups = $this->feegroup_model->get();
            $feetypes = $this->feetype_model->get();
            
            // Get classes
            $this->load->model('class_model');
            $classes = $this->class_model->get();

            $response = [
                'status' => 1,
                'message' => 'Type wise balance report filter options retrieved successfully',
                'sessions' => $sessions,
                'feegroups' => $feegroups,
                'feetypes' => $feetypes,
                'classes' => $classes,
                'note' => 'Use the filter endpoint with session_id (required) and feetype_ids (required) to get type wise balance report',
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Type Wise Balance Report API Error: ' . $e->getMessage());
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 0,
                    'message' => 'Internal server error',
                    'error' => $e->getMessage()
                ]));
        }
    }
}

