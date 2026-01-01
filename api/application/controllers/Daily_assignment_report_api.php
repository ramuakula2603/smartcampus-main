<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Daily Assignment Report API Controller
 * 
 * This controller handles API requests for daily assignment reports
 * showing student assignment information with filtering by class, section, subject group, subject, and date range.
 * 
 * @package    CodeIgniter
 * @subpackage Controllers
 * @category   API
 */
class Daily_assignment_report_api extends CI_Controller
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
     * Filter daily assignment report
     * 
     * POST /api/daily-assignment-report/filter
     * 
     * Request body (all parameters optional):
     * {
     *   "class_id": 10,
     *   "section_id": 5,
     *   "subject_group_id": 3,
     *   "subject_id": 15,
     *   "search_type": "this_year",
     *   "from_date": "2025-01-01",
     *   "to_date": "2025-12-31",
     *   "session_id": 21
     * }
     * 
     * Empty request body {} returns all daily assignment data
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
            $search_type = isset($json_input['search_type']) ? $json_input['search_type'] : null;
            $from_date = isset($json_input['from_date']) ? $json_input['from_date'] : null;
            $to_date = isset($json_input['to_date']) ? $json_input['to_date'] : null;
            $session_id = isset($json_input['session_id']) ? $json_input['session_id'] : $this->setting_model->getCurrentSession();

            // Build date condition
            $condition = "";
            if (!empty($from_date) && !empty($to_date)) {
                $condition = " date_format(daily_assignment.date,'%Y-%m-%d') between '" . $this->db->escape_str($from_date) . "' and '" . $this->db->escape_str($to_date) . "'";
            } else if (!empty($search_type)) {
                $dates = $this->getDateRange($search_type);
                if ($dates) {
                    $condition = " date_format(daily_assignment.date,'%Y-%m-%d') between '" . $this->db->escape_str($dates['from_date']) . "' and '" . $this->db->escape_str($dates['to_date']) . "'";
                }
            } else {
                // Default to this year if no date filter provided
                $dates = $this->getDateRange('this_year');
                $condition = " date_format(daily_assignment.date,'%Y-%m-%d') between '" . $this->db->escape_str($dates['from_date']) . "' and '" . $this->db->escape_str($dates['to_date']) . "'";
            }

            // Get daily assignment data
            $result = $this->homework_model->getDailyAssignmentReport($class_id, $section_id, $subject_group_id, $subject_id, $condition, $session_id);

            $response = [
                'status' => 1,
                'message' => 'Daily assignment report retrieved successfully',
                'filters_applied' => [
                    'class_id' => $class_id,
                    'section_id' => $section_id,
                    'subject_group_id' => $subject_group_id,
                    'subject_id' => $subject_id,
                    'search_type' => $search_type,
                    'from_date' => $from_date,
                    'to_date' => $to_date,
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
            log_message('error', 'Daily Assignment Report API Error: ' . $e->getMessage());
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
     * List daily assignment filter options
     * 
     * POST /api/daily-assignment-report/list
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
            
            // Search type options
            $search_types = array(
                array('value' => 'today', 'label' => 'Today'),
                array('value' => 'this_week', 'label' => 'This Week'),
                array('value' => 'this_month', 'label' => 'This Month'),
                array('value' => 'this_year', 'label' => 'This Year')
            );

            $response = [
                'status' => 1,
                'message' => 'Daily assignment filter options retrieved successfully',
                'total_classes' => count($classes),
                'classes' => $classes,
                'search_types' => $search_types,
                'current_session_id' => (int)$session_id,
                'note' => 'Use the filter endpoint with class_id, section_id, subject_group_id, subject_id, or date range to get daily assignment report',
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Daily Assignment Report API Error: ' . $e->getMessage());
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
     * Helper function to get date range based on search type
     */
    private function getDateRange($search_type)
    {
        $dates = null;
        
        switch ($search_type) {
            case 'today':
                $dates = array(
                    'from_date' => date('Y-m-d'),
                    'to_date' => date('Y-m-d')
                );
                break;
            case 'this_week':
                $dates = array(
                    'from_date' => date('Y-m-d', strtotime('monday this week')),
                    'to_date' => date('Y-m-d', strtotime('sunday this week'))
                );
                break;
            case 'this_month':
                $dates = array(
                    'from_date' => date('Y-m-01'),
                    'to_date' => date('Y-m-t')
                );
                break;
            case 'this_year':
                $dates = array(
                    'from_date' => date('Y-01-01'),
                    'to_date' => date('Y-12-31')
                );
                break;
        }
        
        return $dates;
    }
}

