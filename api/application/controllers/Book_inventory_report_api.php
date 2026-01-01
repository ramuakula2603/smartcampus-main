<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Book Inventory Report API Controller
 * 
 * This controller handles API requests for book inventory reports
 * showing book stock information with filtering by date range.
 * 
 * @package    CodeIgniter
 * @subpackage Controllers
 * @category   API
 */
class Book_inventory_report_api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Load required models - IMPORTANT: setting_model MUST be loaded FIRST
        $this->load->model('setting_model');
        $this->load->model('auth_model');
        $this->load->model('book_model');
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->database();
    }

    /**
     * Filter book inventory report
     * 
     * POST /api/book-inventory-report/filter
     * 
     * Request body (all parameters optional):
     * {
     *   "search_type": "this_year",
     *   "from_date": "2025-01-01",
     *   "to_date": "2025-12-31"
     * }
     * 
     * Empty request body {} returns all book inventory data for current year
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

            // Get book inventory data
            $result = $this->book_model->getBookInventoryReport($start_date, $end_date);

            $response = [
                'status' => 1,
                'message' => 'Book inventory report retrieved successfully',
                'filters_applied' => [
                    'search_type' => $search_type,
                    'from_date' => $from_date,
                    'to_date' => $to_date,
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
            log_message('error', 'Book Inventory Report API Error: ' . $e->getMessage());
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
     * List book inventory filter options
     * 
     * POST /api/book-inventory-report/list
     * 
     * Returns available search types for filtering
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

            $response = [
                'status' => 1,
                'message' => 'Book inventory filter options retrieved successfully',
                'search_types' => $search_types,
                'note' => 'Use the filter endpoint with search_type or custom date range (from_date, to_date) to get book inventory report',
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Book Inventory Report API Error: ' . $e->getMessage());
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

