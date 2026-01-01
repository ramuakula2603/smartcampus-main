<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Expense Group Report API Controller
 * 
 * Provides API endpoints for expense group reports
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Finance Reports
 * @author     SMS Development Team
 * @version    1.0.0
 */
class Expense_group_report_api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        // Disable error display - API should only return JSON
        ini_set('display_errors', 0);
        error_reporting(0);

        // Load required models in correct order
        $this->load->model('setting_model');
        $this->load->model('auth_model');
        $this->load->model('expensehead_model');

        // Load library
        $this->load->library('customlib');

        // Load helper
        $this->load->helper('custom');
    }

    /**
     * Filter endpoint - Get expense group report with filters
     * POST /api/expense-group-report/filter
     */
    public function filter()
    {
        // Authenticate request
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

        try {
            // Get JSON input
            $json_input = json_decode($this->input->raw_input_stream, true);
            
            // Get filter parameters - gracefully handle null/empty values
            $search_type = (isset($json_input['search_type']) && $json_input['search_type'] !== '') ? $json_input['search_type'] : null;
            $date_from = (isset($json_input['date_from']) && $json_input['date_from'] !== '') ? $json_input['date_from'] : null;
            $date_to = (isset($json_input['date_to']) && $json_input['date_to'] !== '') ? $json_input['date_to'] : null;
            $head_id = (isset($json_input['head_id']) && $json_input['head_id'] !== '') ? $json_input['head_id'] : null;

            // Determine date range
            if ($search_type !== null && $search_type !== '') {
                $dates = $this->customlib->get_betweendate($search_type);
            } elseif ($date_from !== null && $date_to !== null && $date_from !== '' && $date_to !== '') {
                $dates = array(
                    'from_date' => $date_from,
                    'to_date' => $date_to
                );
            } else {
                // Default to current year if no dates provided
                $search_year = date('Y');
                $dates = array(
                    'from_date' => $search_year . '-01-01',
                    'to_date' => $search_year . '-12-31'
                );
            }

            $start_date = date('Y-m-d', strtotime($dates['from_date']));
            $end_date = date('Y-m-d', strtotime($dates['to_date']));

            // Get expense data
            $expenses = $this->expensehead_model->searchexpensegroup($start_date, $end_date, $head_id);

            // Calculate totals
            $total_amount = 0;
            $total_count = 0;
            $expense_by_head = array();

            foreach ($expenses as $expense) {
                $total_amount += floatval($expense['amount']);
                $total_count++;

                // Group by expense head
                $exp_head_id = $expense['exp_head_id'];
                if (!isset($expense_by_head[$exp_head_id])) {
                    $expense_by_head[$exp_head_id] = array(
                        'head_id' => $exp_head_id,
                        'exp_category' => $expense['exp_category'],
                        'expense_count' => 0,
                        'total_amount' => 0
                    );
                }
                $expense_by_head[$exp_head_id]['expense_count']++;
                $expense_by_head[$exp_head_id]['total_amount'] += floatval($expense['amount']);
            }

            // Convert to indexed array
            $summary = array_values($expense_by_head);

            $response = [
                'status' => 1,
                'message' => 'Expense group report retrieved successfully',
                'filters_applied' => [
                    'search_type' => $search_type,
                    'date_from' => $start_date,
                    'date_to' => $end_date,
                    'head_id' => $head_id
                ],
                'date_range' => [
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'label' => date($this->customlib->getSchoolDateFormat(), strtotime($start_date)) . " to " . date($this->customlib->getSchoolDateFormat(), strtotime($end_date))
                ],
                'summary' => [
                    'total_expenses' => $total_count,
                    'total_amount' => number_format($total_amount, 2),
                    'by_head' => $summary
                ],
                'total_records' => count($expenses),
                'data' => $expenses,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 0,
                    'message' => 'Error retrieving expense group report: ' . $e->getMessage()
                ]));
        }
    }

    /**
     * List endpoint - Get filter options
     * POST /api/expense-group-report/list
     */
    public function list()
    {
        // Authenticate request
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

        try {
            // Get expense heads
            $expense_heads = $this->expensehead_model->get();

            // Get search types (date ranges)
            $search_types = $this->customlib->get_searchtype();

            // Get date types
            $date_types = $this->customlib->date_type();

            $response = [
                'status' => 1,
                'message' => 'Filter options retrieved successfully',
                'data' => [
                    'expense_heads' => $expense_heads,
                    'search_types' => $search_types,
                    'date_types' => $date_types
                ],
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 0,
                    'message' => 'Error retrieving filter options: ' . $e->getMessage()
                ]));
        }
    }
}

