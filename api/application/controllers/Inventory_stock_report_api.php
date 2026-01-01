<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Inventory Stock Report API Controller
 * 
 * This controller handles API requests for inventory stock reports
 * showing available quantities, total quantities, and issued quantities.
 * 
 * @package    CodeIgniter
 * @subpackage Controllers
 * @category   API
 */
class Inventory_stock_report_api extends CI_Controller
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
     * POST /api/inventory-stock-report/list
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
     * Filter endpoint - Returns inventory stock report data
     * 
     * POST /api/inventory-stock-report/filter
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

            // Get inventory stock data
            $result = $this->get_inventory_stock($start_date, $end_date);

            $response = array(
                'status' => 1,
                'message' => 'Inventory stock report retrieved successfully',
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
                    'total_stock_quantity' => array_sum(array_column($result, 'total_quantity')),
                    'total_available_quantity' => array_sum(array_column($result, 'available_quantity')),
                    'total_issued_quantity' => array_sum(array_column($result, 'total_issued'))
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
                    'message' => 'Error retrieving inventory stock report',
                    'error' => $e->getMessage()
                )));
        }
    }

    /**
     * Get inventory stock data using direct database queries
     */
    private function get_inventory_stock($start_date, $end_date)
    {
        // Build the main query
        $sql = "SELECT 
                    item.id,
                    item.name,
                    item.item_category_id,
                    item.description,
                    item_category.item_category,
                    item_supplier.item_supplier,
                    item_store.item_store,
                    SUM(item_stock.quantity) as total_quantity,
                    (SELECT SUM(quantity) FROM item_issue WHERE item.id = item_issue.item_id) as total_issued,
                    (SELECT SUM(quantity) FROM item_issue WHERE item.id = item_issue.item_id AND is_returned = 1) as total_not_returned
                FROM item_stock
                JOIN item ON item.id = item_stock.item_id
                JOIN item_category ON item.item_category_id = item_category.id
                JOIN item_supplier ON item_stock.supplier_id = item_supplier.id
                LEFT OUTER JOIN item_store ON item_store.id = item_stock.store_id
                WHERE 1=1";

        // Add date filter if provided
        if ($start_date && $end_date) {
            $sql .= " AND DATE_FORMAT(item_stock.date, '%Y-%m-%d') BETWEEN " . 
                    $this->db->escape($start_date) . " AND " . $this->db->escape($end_date);
        }

        $sql .= " GROUP BY item.id
                  ORDER BY item.name ASC";

        $query = $this->db->query($sql);
        $results = $query->result_array();

        // Calculate available quantity for each item
        foreach ($results as &$row) {
            $available = $this->get_available_quantity($row['id']);
            $row['available_quantity'] = $available;
            $row['total_issued'] = $row['total_issued'] ? intval($row['total_issued']) : 0;
            $row['total_not_returned'] = $row['total_not_returned'] ? intval($row['total_not_returned']) : 0;
            $row['total_quantity'] = $row['total_quantity'] ? intval($row['total_quantity']) : 0;
        }

        return $results;
    }

    /**
     * Get available quantity for an item
     */
    private function get_available_quantity($item_id)
    {
        $sql = "SELECT 
                    IFNULL(item_stock.item_stock_quantity, 0) as added_stock,
                    IFNULL(item_issues.issued, 0) as issued
                FROM item
                LEFT JOIN (
                    SELECT item_id, SUM(quantity) as item_stock_quantity 
                    FROM item_stock 
                    GROUP BY item_id
                ) as item_stock ON item_stock.item_id = item.id
                LEFT JOIN (
                    SELECT item_id, 
                           IFNULL(SUM(CASE WHEN is_returned = 1 THEN quantity ELSE 0 END), 0) as issued
                    FROM item_issue 
                    GROUP BY item_id
                ) as item_issues ON item_issues.item_id = item.id
                WHERE item.id = " . intval($item_id);

        $query = $this->db->query($sql);
        $data = $query->row_array();

        if ($data) {
            $available = ($data['added_stock'] - $data['issued']);
            return $available >= 0 ? $available : 0;
        }

        return 0;
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

