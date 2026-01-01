<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Homework Report API Controller
 * 
 * This controller handles API requests for homework reports
 * showing homework information with filtering by class, section, subject group, and subject.
 * 
 * @package    CodeIgniter
 * @subpackage Controllers
 * @category   API
 */
class Homework_report_api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Load required models - IMPORTANT: setting_model MUST be loaded FIRST
        $this->load->model('setting_model');
        $this->load->model('auth_model');
        $this->load->model('homework_model');
        $this->load->model('class_model');
        $this->load->model('subjectgroup_model');
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->database();
    }

    /**
     * Filter homework report
     * 
     * POST /api/homework-report/filter
     * 
     * Request body (all parameters optional):
     * {
     *   "class_id": 10,
     *   "section_id": 5,
     *   "subject_group_id": 3,
     *   "subject_id": 15,
     *   "session_id": 21
     * }
     * 
     * Empty request body {} returns all homework data
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
            
            // Get filter parameters (all optional)
            $class_id = isset($json_input['class_id']) ? $json_input['class_id'] : null;
            $section_id = isset($json_input['section_id']) ? $json_input['section_id'] : null;
            $subject_group_id = isset($json_input['subject_group_id']) ? $json_input['subject_group_id'] : null;
            $subject_id = isset($json_input['subject_id']) ? $json_input['subject_id'] : null;
            $session_id = isset($json_input['session_id']) ? $json_input['session_id'] : $this->setting_model->getCurrentSession();

            // Get homework report data
            $result = $this->homework_model->search_dthomeworkreport($class_id, $section_id, $subject_group_id, $subject_id, $session_id);

            $response = [
                'status' => 1,
                'message' => 'Homework report retrieved successfully',
                'filters_applied' => [
                    'class_id' => $class_id,
                    'section_id' => $section_id,
                    'subject_group_id' => $subject_group_id,
                    'subject_id' => $subject_id,
                    'session_id' => (int)$session_id
                ],
                'total_records' => count($result),
                'data' => $result,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Homework Report API Error: ' . $e->getMessage());
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
     * List homework report filter options
     * 
     * POST /api/homework-report/list
     * 
     * Returns available classes, sections, subject groups, and subjects for filtering
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

            // Get classes
            $classes = $this->class_model->get();
            
            // Get session
            $session_id = $this->setting_model->getCurrentSession();

            $response = [
                'status' => 1,
                'message' => 'Homework report filter options retrieved successfully',
                'total_classes' => count($classes),
                'classes' => $classes,
                'current_session_id' => (int)$session_id,
                'note' => 'Use the filter endpoint with class_id, section_id, subject_group_id, and subject_id to get homework report',
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Homework Report API Error: ' . $e->getMessage());
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

