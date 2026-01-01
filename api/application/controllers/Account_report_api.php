<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Account Report API Controller
 * 
 * This controller provides RESTful API endpoints for generating account reports.
 * It handles account balance calculations, transaction listings, and daily breakdowns.
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Webservices
 * @author     School Management System
 * @version    1.0.0
 */
class Account_report_api extends CI_Controller
{
    /**
     * Constructor
     * 
     * Initializes the controller, loads required models, libraries, and helpers.
     */
    public function __construct()
    {
        parent::__construct();

        // Start output buffering if not already started
        if (!ob_get_level()) {
            ob_start();
        }

        // Set JSON content type
        $this->output->set_content_type('application/json');

        // Load essential helpers
        $this->load->helper('json_output');

        // Load required models
        $this->load->model('Account_report_model', 'report_model');
    }

    /**
     * Validate required headers
     * 
     * @return bool True if headers are valid, false otherwise
     */
    private function validate_headers()
    {
        $client_service = $this->input->get_request_header('Client-Service', TRUE);
        $auth_key = $this->input->get_request_header('Auth-Key', TRUE);
        
        return ($client_service === 'smartschool' && $auth_key === 'schoolAdmin@');
    }

    /**
     * Generate account report
     * 
     * Generates a comprehensive account report with opening/closing balances,
     * transactions, and daily breakdown.
     * 
     * @return void Outputs JSON response
     */
    public function generate()
    {
        try {
            // Validate request method
            if ($this->input->method() !== 'post') {
                json_output(405, array(
                    'status' => 0,
                    'message' => 'Method not allowed. Use POST method.',
                    'data' => null
                ));
                return;
            }

            // Validate required headers
            if (!$this->validate_headers()) {
                json_output(401, array(
                    'status' => 0,
                    'message' => 'Unauthorized access. Invalid headers.',
                    'data' => null
                ));
                return;
            }

            // Get input data
            $input = json_decode($this->input->raw_input_stream, true);
            if ($input === null) {
                $input = array();
            }

            // Validate required fields
            if (empty($input['account_id']) || !is_numeric($input['account_id'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or missing account_id',
                    'data' => null
                ));
                return;
            }

            if (empty($input['date_from']) || empty($input['date_to'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Both date_from and date_to are required',
                    'data' => null
                ));
                return;
            }

            $account_id = (int)$input['account_id'];
            $start_date = $input['date_from'];
            $end_date = $input['date_to'];

            // Validate date format
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $start_date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $end_date)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid date format. Use Y-m-d format (e.g., 2024-01-15)',
                    'data' => null
                ));
                return;
            }

            // Validate date range
            if (strtotime($start_date) > strtotime($end_date)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'date_from cannot be greater than date_to',
                    'data' => null
                ));
                return;
            }

            // Check if account exists
            if (!$this->report_model->account_exists($account_id)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Account not found',
                    'data' => null
                ));
                return;
            }

            // Generate report
            $report = $this->report_model->get_account_report($account_id, $start_date, $end_date);

            if ($report === null) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'No active financial year found',
                    'data' => null
                ));
                return;
            }

            json_output(200, array(
                'status' => 1,
                'message' => 'Account report generated successfully',
                'data' => $report
            ));

        } catch (Exception $e) {
            log_message('error', 'Account Report API Generate Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Get accounts list
     * 
     * Retrieves a list of all accounts.
     * 
     * @return void Outputs JSON response
     */
    public function accounts()
    {
        try {
            // Validate request method
            if ($this->input->method() !== 'post') {
                json_output(405, array(
                    'status' => 0,
                    'message' => 'Method not allowed. Use POST method.',
                    'data' => null
                ));
                return;
            }

            // Validate required headers
            if (!$this->validate_headers()) {
                json_output(401, array(
                    'status' => 0,
                    'message' => 'Unauthorized access. Invalid headers.',
                    'data' => null
                ));
                return;
            }

            // Get accounts
            $accounts = $this->report_model->get_accounts();

            json_output(200, array(
                'status' => 1,
                'message' => 'Accounts retrieved successfully',
                'total_records' => count($accounts),
                'data' => $accounts
            ));

        } catch (Exception $e) {
            log_message('error', 'Account Report API Accounts Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Get account transactions
     * 
     * Retrieves transactions for a specific account within a date range.
     * 
     * @return void Outputs JSON response
     */
    public function transactions()
    {
        try {
            // Validate request method
            if ($this->input->method() !== 'post') {
                json_output(405, array(
                    'status' => 0,
                    'message' => 'Method not allowed. Use POST method.',
                    'data' => null
                ));
                return;
            }

            // Validate required headers
            if (!$this->validate_headers()) {
                json_output(401, array(
                    'status' => 0,
                    'message' => 'Unauthorized access. Invalid headers.',
                    'data' => null
                ));
                return;
            }

            // Get input data
            $input = json_decode($this->input->raw_input_stream, true);
            if ($input === null) {
                $input = array();
            }

            // Validate required fields
            if (empty($input['account_id']) || !is_numeric($input['account_id'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or missing account_id',
                    'data' => null
                ));
                return;
            }

            $account_id = (int)$input['account_id'];
            $start_date = isset($input['date_from']) && !empty($input['date_from']) ? $input['date_from'] : null;
            $end_date = isset($input['date_to']) && !empty($input['date_to']) ? $input['date_to'] : null;
            $status = isset($input['status']) && !empty($input['status']) ? $input['status'] : null;

            // Validate date range if provided
            if (($start_date && !$end_date) || (!$start_date && $end_date)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Both date_from and date_to are required when filtering by date',
                    'data' => null
                ));
                return;
            }

            // Check if account exists
            if (!$this->report_model->account_exists($account_id)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Account not found',
                    'data' => null
                ));
                return;
            }

            // Get transactions
            $transactions = $this->report_model->get_account_transactions($account_id, $start_date, $end_date, $status);

            json_output(200, array(
                'status' => 1,
                'message' => 'Account transactions retrieved successfully',
                'total_records' => count($transactions),
                'data' => $transactions
            ));

        } catch (Exception $e) {
            log_message('error', 'Account Report API Transactions Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Get active financial year
     * 
     * Retrieves the currently active financial year.
     * 
     * @return void Outputs JSON response
     */
    public function active_financial_year()
    {
        try {
            // Validate request method
            if ($this->input->method() !== 'post') {
                json_output(405, array(
                    'status' => 0,
                    'message' => 'Method not allowed. Use POST method.',
                    'data' => null
                ));
                return;
            }

            // Validate required headers
            if (!$this->validate_headers()) {
                json_output(401, array(
                    'status' => 0,
                    'message' => 'Unauthorized access. Invalid headers.',
                    'data' => null
                ));
                return;
            }

            // Get active financial year
            $financial_year = $this->report_model->get_active_financial_year();

            if (empty($financial_year)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'No active financial year found',
                    'data' => null
                ));
                return;
            }

            json_output(200, array(
                'status' => 1,
                'message' => 'Active financial year retrieved successfully',
                'data' => $financial_year
            ));

        } catch (Exception $e) {
            log_message('error', 'Account Report API Active Financial Year Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }
}

