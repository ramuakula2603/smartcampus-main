<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Add Item Report API Controller
 * 
 * This controller handles API requests for add item reports
 * showing items added to inventory within a date range.
 * 
 * @package    CodeIgniter
 * @subpackage Controllers
 * @category   API
 */
class Add_item_report_api extends CI_Controller
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
     * POST /api/add-item-report/list
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
     * Filter endpoint - Returns add item report data
     * 
     * POST /api/add-item-report/filter
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

            // Get add item data
            $result = $this->get_add_item_data($start_date, $end_date);

            // Calculate summary
            $total_quantity = 0;
            $total_purchase_price = 0;
            foreach ($result as $row) {
                $total_quantity += intval($row['quantity']);
                $total_purchase_price += floatval($row['purchase_price']);
            }

            $response = array(
                'status' => 1,
                'message' => 'Add item report retrieved successfully',
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
                    'total_items' => count($result),
                    'total_quantity' => $total_quantity,
                    'total_purchase_price' => number_format($total_purchase_price, 2, '.', '')
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
                    'message' => 'Error retrieving add item report',
                    'error' => $e->getMessage()
                )));
        }
    }

    /**
     * Get add item data using direct database queries
     */
    private function get_add_item_data($start_date, $end_date)
    {
        $sql = "SELECT 
                    item_stock.*,
                    item.name,
                    item.item_category_id,
                    item.description as des,
                    item_category.item_category,
                    item_supplier.item_supplier,
                    item_store.item_store
                FROM item_stock
                JOIN item ON item.id = item_stock.item_id
                JOIN item_category ON item.item_category_id = item_category.id
                JOIN item_supplier ON item_stock.supplier_id = item_supplier.id
                LEFT OUTER JOIN item_store ON item_store.id = item_stock.store_id
                WHERE DATE_FORMAT(item_stock.date, '%Y-%m-%d') BETWEEN " . 
                $this->db->escape($start_date) . " AND " . $this->db->escape($end_date) . "
                ORDER BY item_stock.id DESC";

        $query = $this->db->query($sql);
        $results = $query->result_array();

        // Format the results
        foreach ($results as &$row) {
            $row['quantity'] = intval($row['quantity']);
            $row['purchase_price'] = number_format(floatval($row['purchase_price']), 2, '.', '');
            $row['date'] = $row['date'];
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

