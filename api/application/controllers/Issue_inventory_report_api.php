<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Issue Inventory Report API Controller
 * 
 * This controller handles API requests for issue inventory reports
 * showing items issued to staff members within a date range.
 * 
 * @package    CodeIgniter
 * @subpackage Controllers
 * @category   API
 */
class Issue_inventory_report_api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Suppress PHP 8.2 deprecation warnings
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
        
        // Database connection error handling
        try {
            // Load required models and libraries
            $this->load->model('setting_model');
            $this->load->model('auth_model');
            $this->load->database();
            $this->load->library('session');
            $this->load->helper('url');
            
            // Check database connection
            if (!$this->db->conn_id) {
                throw new Exception('Database connection failed');
            }
        } catch (Exception $e) {
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'status' => 0,
                    'message' => 'Database connection error. Please ensure MySQL is running in XAMPP.',
                    'error' => $e->getMessage()
                )));
            return;
        }
        
        // Authenticate API request
        $client_service = $this->input->get_request_header('Client-Service', TRUE);
        $auth_key = $this->input->get_request_header('Auth-Key', TRUE);
        
        if ($client_service !== 'smartschool' || $auth_key !== 'schoolAdmin@') {
            $this->output
                ->set_status_header(401)
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'status' => 0,
                    'message' => 'Unauthorized access'
                )));
            return;
        }
    }

    /**
     * List endpoint - Returns filter options
     * 
     * POST /api/issue-inventory-report/list
     */
    public function list()
    {
        try {
            $search_types = array(
                'today' => 'Today',
                'this_week' => 'This Week',
                'this_month' => 'This Month',
                'last_month' => 'Last Month',
                'this_year' => 'This Year',
                'period' => 'Period'
            );

            $response = array(
                'status' => 1,
                'message' => 'Filter options retrieved successfully',
                'data' => array(
                    'search_types' => $search_types
                ),
                'timestamp' => date('Y-m-d H:i:s')
            );

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'status' => 0,
                    'message' => 'Error retrieving filter options',
                    'error' => $e->getMessage()
                )));
        }
    }

    /**
     * Filter endpoint - Returns issue inventory report data
     * 
     * POST /api/issue-inventory-report/filter
     * 
     * Request body (all parameters optional):
     * {
     *   "search_type": "this_year",
     *   "date_from": "2025-01-01",
     *   "date_to": "2025-12-31"
     * }
     */
    public function filter()
    {
        try {
            // Check request method
            if ($this->input->method() !== 'post') {
                $this->output
                    ->set_status_header(400)
                    ->set_content_type('application/json')
                    ->set_output(json_encode(array(
                        'status' => 0,
                        'message' => 'Bad request. Only POST method allowed.'
                    )));
                return;
            }

            // Get request body
            $request_body = json_decode($this->input->raw_input_stream, true);
            if ($request_body === null) {
                $request_body = array();
            }

            // Extract parameters (all optional)
            $search_type = isset($request_body['search_type']) ? $request_body['search_type'] : null;
            $date_from = isset($request_body['date_from']) ? $request_body['date_from'] : null;
            $date_to = isset($request_body['date_to']) ? $request_body['date_to'] : null;

            // Calculate date range
            $dates = $this->get_date_range($search_type, $date_from, $date_to);
            $start_date = $dates['from_date'];
            $end_date = $dates['to_date'];

            // Get issue inventory data
            $result = $this->get_issue_inventory_data($start_date, $end_date);

            // Calculate summary
            $total_quantity = 0;
            $total_returned = 0;
            $total_not_returned = 0;
            
            foreach ($result as $row) {
                $total_quantity += intval($row['quantity']);
                if ($row['is_returned'] == 1) {
                    $total_returned++;
                } else {
                    $total_not_returned++;
                }
            }

            $response = array(
                'status' => 1,
                'message' => 'Issue inventory report retrieved successfully',
                'filters_applied' => array(
                    'search_type' => $search_type,
                    'date_from' => $date_from,
                    'date_to' => $date_to,
                    'date_range_used' => array(
                        'start_date' => $start_date,
                        'end_date' => $end_date
                    )
                ),
                'summary' => array(
                    'total_issues' => count($result),
                    'total_quantity' => $total_quantity,
                    'total_returned' => $total_returned,
                    'total_not_returned' => $total_not_returned
                ),
                'total_records' => count($result),
                'data' => $result,
                'timestamp' => date('Y-m-d H:i:s')
            );

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'status' => 0,
                    'message' => 'Error retrieving issue inventory report',
                    'error' => $e->getMessage()
                )));
        }
    }

    /**
     * Get issue inventory data using direct database queries
     */
    private function get_issue_inventory_data($start_date, $end_date)
    {
        $sql = "SELECT 
                    item_issue.*,
                    item.name as item_name,
                    item.item_category_id,
                    item_category.item_category,
                    staff.employee_id,
                    staff.name as staff_name,
                    staff.surname,
                    issued_by.employee_id as issued_by_employee_id,
                    issued_by.name as issued_by_name,
                    issued_by.surname as issued_by_surname,
                    roles.name as role_name
                FROM item_issue
                INNER JOIN item ON item.id = item_issue.item_id
                INNER JOIN item_category ON item_category.id = item.item_category_id
                INNER JOIN staff ON staff.id = item_issue.issue_to
                INNER JOIN staff as issued_by ON issued_by.id = item_issue.issue_by
                INNER JOIN staff_roles ON staff_roles.staff_id = staff.id
                INNER JOIN roles ON roles.id = staff_roles.role_id
                WHERE DATE_FORMAT(item_issue.issue_date, '%Y-%m-%d') BETWEEN " . 
                $this->db->escape($start_date) . " AND " . $this->db->escape($end_date) . "
                ORDER BY item_issue.issue_date DESC";

        $query = $this->db->query($sql);
        $results = $query->result_array();

        // Format the results
        foreach ($results as &$row) {
            $row['quantity'] = intval($row['quantity']);
            $row['is_returned'] = intval($row['is_returned']);
            
            // Format issue_to information
            $row['issue_to_info'] = array(
                'employee_id' => $row['employee_id'],
                'name' => $row['staff_name'] . ' ' . $row['surname'],
                'role' => $row['role_name']
            );
            
            // Format issued_by information
            $row['issued_by_info'] = array(
                'employee_id' => $row['issued_by_employee_id'],
                'name' => $row['issued_by_name'] . ' ' . $row['issued_by_surname']
            );
            
            // Format return date
            if ($row['return_date'] == '0000-00-00' || empty($row['return_date'])) {
                $row['return_date'] = null;
                $row['return_status'] = 'Not Returned';
            } else {
                $row['return_status'] = 'Returned';
            }
        }

        return $results;
    }

    /**
     * Calculate date range based on search type
     */
    private function get_date_range($search_type, $date_from, $date_to)
    {
        // If specific dates provided, use them
        if ($date_from && $date_to) {
            return array('from_date' => $date_from, 'to_date' => $date_to);
        }

        // Otherwise calculate based on search_type
        $today = date('Y-m-d');
        
        switch ($search_type) {
            case 'today':
                return array('from_date' => $today, 'to_date' => $today);
            
            case 'this_week':
                return array(
                    'from_date' => date('Y-m-d', strtotime('monday this week')),
                    'to_date' => $today
                );
            
            case 'this_month':
                return array('from_date' => date('Y-m-01'), 'to_date' => $today);
            
            case 'last_month':
                return array(
                    'from_date' => date('Y-m-01', strtotime('last month')),
                    'to_date' => date('Y-m-t', strtotime('last month'))
                );
            
            case 'this_year':
            default:
                return array('from_date' => date('Y-01-01'), 'to_date' => date('Y-12-31'));
        }
    }
}

