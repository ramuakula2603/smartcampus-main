<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Income Report API Controller
 * 
 * Provides API endpoints for income reports (different from Income Group Report)
 * This shows all income records without grouping
 * 
 * @package    Smart School
 * @subpackage API
 * @category   Finance Reports
 * @author     Smart School Team
 */
class Income_report_api extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        // Suppress errors for clean JSON output
        ini_set('display_errors', 0);
        error_reporting(0);

        // Set JSON response header
        header('Content-Type: application/json');

        // Try to load database with error handling
        try {
            $this->load->database();

            // Test database connection
            if (!$this->db->conn_id) {
                throw new Exception('Database connection failed');
            }

            // Load required models in correct order
            $this->load->model('setting_model');
            $this->load->model('auth_model');

        } catch (Exception $e) {
            // Return JSON error response
            echo json_encode(array(
                'status' => 0,
                'message' => 'Database connection error. Please ensure MySQL is running in XAMPP.',
                'error' => 'Unable to connect to database server',
                'timestamp' => date('Y-m-d H:i:s')
            ));
            exit;
        }
    }

    /**
     * Filter endpoint - Get income report with filters
     * 
     * POST /api/income-report/filter
     * 
     * Request Body (all optional):
     * {
     *   "search_type": "today|this_week|this_month|last_month|this_year|period",
     *   "date_from": "2025-01-01",
     *   "date_to": "2025-12-31"
     * }
     * 
     * Empty request {} returns all income for current year
     */
    public function filter()
    {
        try {
            // Authenticate request
            if (!$this->auth_model->check_auth_client()) {
                echo json_encode(array(
                    'status' => 0,
                    'message' => 'Unauthorized access',
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            // Get input
            $input = json_decode(file_get_contents('php://input'), true);
            if ($input === null) {
                $input = array();
            }

            // Extract parameters with graceful null handling
            $search_type = (isset($input['search_type']) && $input['search_type'] !== '') ? $input['search_type'] : null;
            $date_from = (isset($input['date_from']) && $input['date_from'] !== '') ? $input['date_from'] : null;
            $date_to = (isset($input['date_to']) && $input['date_to'] !== '') ? $input['date_to'] : null;

            // Determine date range
            if ($search_type !== null) {
                // Use search_type to determine dates
                $dates = $this->getDateRangeBySearchType($search_type);
                $start_date = $dates['from_date'];
                $end_date = $dates['to_date'];
                $date_label = $dates['label'];
            } elseif ($date_from !== null && $date_to !== null) {
                // Use custom date range
                $start_date = $date_from;
                $end_date = $date_to;
                $date_label = date('d/m/Y', strtotime($start_date)) . ' to ' . date('d/m/Y', strtotime($end_date));
            } else {
                // Default to current year
                $dates = $this->getDateRangeBySearchType('this_year');
                $start_date = $dates['from_date'];
                $end_date = $dates['to_date'];
                $date_label = $dates['label'];
            }

            // Get income data directly from database
            $this->db->select('income.id, income.date, income.name, income.invoice_no, income.amount, income.documents, income.note, income_head.income_category, income.income_head_id');
            $this->db->from('income');
            $this->db->join('income_head', 'income.income_head_id = income_head.id');
            $this->db->where('income.date >=', $start_date);
            $this->db->where('income.date <=', $end_date);
            $this->db->order_by('income.date', 'desc');
            $query = $this->db->get();
            $incomeList = $query->result_array();

            // Calculate totals
            $total_amount = 0;
            $total_records = 0;
            $incomes = array();

            if (!empty($incomeList)) {
                foreach ($incomeList as $income) {
                    $total_amount += floatval($income['amount']);
                    $total_records++;

                    // Format for response
                    $incomes[] = array(
                        'id' => $income['id'],
                        'name' => $income['name'],
                        'invoice_no' => $income['invoice_no'],
                        'income_category' => $income['income_category'],
                        'date' => $income['date'],
                        'amount' => number_format(floatval($income['amount']), 2, '.', ''),
                        'note' => isset($income['note']) ? $income['note'] : '',
                        'documents' => isset($income['documents']) ? $income['documents'] : ''
                    );
                }
            }

            // Prepare response
            $response = array(
                'status' => 1,
                'message' => 'Income report retrieved successfully',
                'filters_applied' => array(
                    'search_type' => $search_type,
                    'date_from' => $start_date,
                    'date_to' => $end_date
                ),
                'date_range' => array(
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'label' => $date_label
                ),
                'summary' => array(
                    'total_records' => $total_records,
                    'total_amount' => number_format($total_amount, 2, '.', '')
                ),
                'total_records' => $total_records,
                'data' => $incomes,
                'timestamp' => date('Y-m-d H:i:s')
            );

            echo json_encode($response);

        } catch (Exception $e) {
            echo json_encode(array(
                'status' => 0,
                'message' => 'An error occurred while processing the request',
                'error' => $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s')
            ));
        }
    }

    /**
     * List endpoint - Get filter options
     * 
     * POST /api/income-report/list
     * 
     * Returns available search types for filtering
     */
    public function list()
    {
        try {
            // Authenticate request
            if (!$this->auth_model->check_auth_client()) {
                echo json_encode(array(
                    'status' => 0,
                    'message' => 'Unauthorized access',
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            // Prepare response
            $response = array(
                'status' => 1,
                'message' => 'Filter options retrieved successfully',
                'data' => array(
                    'search_types' => array(
                        array('key' => 'today', 'label' => 'Today'),
                        array('key' => 'this_week', 'label' => 'This Week'),
                        array('key' => 'this_month', 'label' => 'This Month'),
                        array('key' => 'last_month', 'label' => 'Last Month'),
                        array('key' => 'this_year', 'label' => 'This Year'),
                        array('key' => 'period', 'label' => 'Custom Period')
                    )
                ),
                'timestamp' => date('Y-m-d H:i:s')
            );

            echo json_encode($response);

        } catch (Exception $e) {
            echo json_encode(array(
                'status' => 0,
                'message' => 'An error occurred while processing the request',
                'error' => $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s')
            ));
        }
    }

    /**
     * Private helper method to get date range by search type
     */
    private function getDateRangeBySearchType($search_type)
    {
        $today = date('Y-m-d');
        
        switch ($search_type) {
            case 'today':
                return array(
                    'from_date' => $today,
                    'to_date' => $today,
                    'label' => date('d/m/Y')
                );
            
            case 'this_week':
                $start_of_week = date('Y-m-d', strtotime('monday this week'));
                $end_of_week = date('Y-m-d', strtotime('sunday this week'));
                return array(
                    'from_date' => $start_of_week,
                    'to_date' => $end_of_week,
                    'label' => date('d/m/Y', strtotime($start_of_week)) . ' to ' . date('d/m/Y', strtotime($end_of_week))
                );
            
            case 'this_month':
                $start_of_month = date('Y-m-01');
                $end_of_month = date('Y-m-t');
                return array(
                    'from_date' => $start_of_month,
                    'to_date' => $end_of_month,
                    'label' => date('F Y')
                );
            
            case 'last_month':
                $start_of_last_month = date('Y-m-01', strtotime('first day of last month'));
                $end_of_last_month = date('Y-m-t', strtotime('last day of last month'));
                return array(
                    'from_date' => $start_of_last_month,
                    'to_date' => $end_of_last_month,
                    'label' => date('F Y', strtotime($start_of_last_month))
                );
            
            case 'this_year':
            default:
                $start_of_year = date('Y-01-01');
                $end_of_year = date('Y-12-31');
                return array(
                    'from_date' => $start_of_year,
                    'to_date' => $end_of_year,
                    'label' => date('01/01/Y') . ' to ' . date('31/12/Y')
                );
        }
    }

}

