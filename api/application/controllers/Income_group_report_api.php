<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Income Group Report API Controller
 * 
 * Provides API endpoints for income group reports
 * 
 * @package    Smart School
 * @subpackage API
 * @category   Finance Reports
 * @author     Smart School Team
 */
class Income_group_report_api extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        
        // Suppress errors for clean JSON output
        ini_set('display_errors', 0);
        error_reporting(0);
        
        // Load required models in correct order
        $this->load->model('setting_model');
        $this->load->model('auth_model');
        $this->load->model('income_model');
        $this->load->model('incomehead_model');
        
        // Set JSON response header
        header('Content-Type: application/json');
    }

    /**
     * Filter endpoint - Get income group report with filters
     * 
     * POST /api/income-group-report/filter
     * 
     * Request Body (all optional):
     * {
     *   "search_type": "today|this_week|this_month|last_month|this_year|period",
     *   "date_from": "2025-01-01",
     *   "date_to": "2025-12-31",
     *   "head": "1"
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
            $head = (isset($input['head']) && $input['head'] !== '') ? $input['head'] : null;

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

            // Get income data
            $incomes = $this->income_model->searchincomegroup($start_date, $end_date, $head);

            // Calculate totals
            $total_amount = 0;
            $total_records = 0;
            $income_heads = array();
            
            foreach ($incomes as $income) {
                $total_amount += floatval($income['amount']);
                $total_records++;
                
                // Group by income head
                $head_id = $income['head_id'];
                if (!isset($income_heads[$head_id])) {
                    $income_heads[$head_id] = array(
                        'head_id' => $head_id,
                        'income_category' => $income['income_category'],
                        'count' => 0,
                        'total' => 0
                    );
                }
                $income_heads[$head_id]['count']++;
                $income_heads[$head_id]['total'] += floatval($income['amount']);
            }

            // Prepare response
            $response = array(
                'status' => 1,
                'message' => 'Income group report retrieved successfully',
                'filters_applied' => array(
                    'search_type' => $search_type,
                    'date_from' => $start_date,
                    'date_to' => $end_date,
                    'head' => $head
                ),
                'date_range' => array(
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'label' => $date_label
                ),
                'summary' => array(
                    'total_records' => $total_records,
                    'total_amount' => number_format($total_amount, 2, '.', ''),
                    'income_heads' => array_values($income_heads)
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
     * POST /api/income-group-report/list
     * 
     * Returns available income heads for filtering
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

            // Get all income heads
            $income_heads = $this->incomehead_model->get();

            // Prepare response
            $response = array(
                'status' => 1,
                'message' => 'Income heads retrieved successfully',
                'data' => array(
                    'income_heads' => $income_heads,
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
     * Get date range by search type
     * 
     * @param string $search_type
     * @return array
     */
    private function getDateRangeBySearchType($search_type)
    {
        $current_year = date('Y');
        $current_month = date('m');
        $current_day = date('d');

        switch ($search_type) {
            case 'today':
                $from_date = date('Y-m-d');
                $to_date = date('Y-m-d');
                $label = date('d/m/Y');
                break;
            case 'this_week':
                $from_date = date('Y-m-d', strtotime('monday this week'));
                $to_date = date('Y-m-d', strtotime('sunday this week'));
                $label = date('d/m/Y', strtotime($from_date)) . ' to ' . date('d/m/Y', strtotime($to_date));
                break;
            case 'this_month':
                $from_date = date('Y-m-01');
                $to_date = date('Y-m-t');
                $label = date('F Y');
                break;
            case 'last_month':
                $from_date = date('Y-m-01', strtotime('first day of last month'));
                $to_date = date('Y-m-t', strtotime('last day of last month'));
                $label = date('F Y', strtotime($from_date));
                break;
            case 'this_year':
            default:
                $from_date = $current_year . '-01-01';
                $to_date = $current_year . '-12-31';
                $label = '01/01/' . $current_year . ' to 31/12/' . $current_year;
                break;
        }

        return array(
            'from_date' => $from_date,
            'to_date' => $to_date,
            'label' => $label
        );
    }

}

