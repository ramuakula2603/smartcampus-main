<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Student Book Issue Report API Controller
 * 
 * This controller handles API requests for student book issue reports
 * showing book issue information with filtering by date range and member type.
 * 
 * @package    CodeIgniter
 * @subpackage Controllers
 * @category   API
 */
class Student_book_issue_report_api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Load required models - IMPORTANT: setting_model MUST be loaded FIRST
        $this->load->model('setting_model');
        $this->load->model('auth_model');
        $this->load->model('bookissue_model');
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->database();
    }

    /**
     * Filter student book issue report
     * 
     * POST /api/student-book-issue-report/filter
     * 
     * Request body (all parameters optional):
     * {
     *   "search_type": "this_year",
     *   "from_date": "2025-01-01",
     *   "to_date": "2025-12-31",
     *   "member_type": "student"
     * }
     * 
     * Empty request body {} returns all book issue data for current year
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
            $search_type = isset($json_input['search_type']) ? $json_input['search_type'] : null;
            $from_date = isset($json_input['from_date']) ? $json_input['from_date'] : null;
            $to_date = isset($json_input['to_date']) ? $json_input['to_date'] : null;
            $member_type = isset($json_input['member_type']) ? $json_input['member_type'] : null;

            // Build date range
            if (!empty($from_date) && !empty($to_date)) {
                $start_date = $from_date;
                $end_date = $to_date;
            } else if (!empty($search_type)) {
                $dates = $this->getDateRange($search_type);
                $start_date = $dates['from_date'];
                $end_date = $dates['to_date'];
            } else {
                // Default to this year if no date filter provided
                $dates = $this->getDateRange('this_year');
                $start_date = $dates['from_date'];
                $end_date = $dates['to_date'];
            }

            // Get student book issue data
            $result = $this->bookissue_model->getStudentBookIssueReport($start_date, $end_date, $member_type);

            $response = [
                'status' => 1,
                'message' => 'Student book issue report retrieved successfully',
                'filters_applied' => [
                    'search_type' => $search_type,
                    'from_date' => $from_date,
                    'to_date' => $to_date,
                    'member_type' => $member_type,
                    'date_range_used' => [
                        'start_date' => $start_date,
                        'end_date' => $end_date
                    ]
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
            log_message('error', 'Student Book Issue Report API Error: ' . $e->getMessage());
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
     * List student book issue filter options
     * 
     * POST /api/student-book-issue-report/list
     * 
     * Returns available search types and member types for filtering
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

            // Search type options
            $search_types = array(
                array('value' => 'today', 'label' => 'Today'),
                array('value' => 'this_week', 'label' => 'This Week'),
                array('value' => 'this_month', 'label' => 'This Month'),
                array('value' => 'this_year', 'label' => 'This Year')
            );

            // Member type options
            $member_types = array(
                array('value' => '', 'label' => 'All'),
                array('value' => 'student', 'label' => 'Student'),
                array('value' => 'teacher', 'label' => 'Teacher')
            );

            $response = [
                'status' => 1,
                'message' => 'Student book issue filter options retrieved successfully',
                'search_types' => $search_types,
                'member_types' => $member_types,
                'note' => 'Use the filter endpoint with search_type, member_type, or custom date range (from_date, to_date) to get student book issue report',
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Student Book Issue Report API Error: ' . $e->getMessage());
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
            default:
                $dates = array(
                    'from_date' => date('Y-01-01'),
                    'to_date' => date('Y-12-31')
                );
        }
        
        return $dates;
    }
}

