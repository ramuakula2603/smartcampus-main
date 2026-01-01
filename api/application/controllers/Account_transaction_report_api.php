<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Account Transaction Report API Controller
 * 
 * This controller provides RESTful API endpoints for managing account transaction reports.
 * It handles listing transactions, getting accounts, financial years, and transaction summaries.
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Webservices
 * @author     School Management System
 * @version    1.0.0
 */
class Account_transaction_report_api extends CI_Controller
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
        $this->load->model('Account_transaction_report_model', 'report_model');
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
     * List account transactions
     * 
     * Retrieves a list of account transactions with optional date range and filter parameters.
     * 
     * @return void Outputs JSON response
     */
    public function list()
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

            // Extract filter parameters
            $start_date = isset($input['date_from']) && !empty($input['date_from']) ? $input['date_from'] : null;
            $end_date = isset($input['date_to']) && !empty($input['date_to']) ? $input['date_to'] : null;
            
            $filters = array();
            if (isset($input['from_account_id']) && !empty($input['from_account_id'])) {
                $filters['from_account_id'] = $input['from_account_id'];
            }
            if (isset($input['to_account_id']) && !empty($input['to_account_id'])) {
                $filters['to_account_id'] = $input['to_account_id'];
            }
            if (isset($input['is_active']) && $input['is_active'] !== '') {
                $filters['is_active'] = $input['is_active'];
            }

            // Validate date range if provided
            if (($start_date && !$end_date) || (!$start_date && $end_date)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Both date_from and date_to are required when filtering by date',
                    'data' => null
                ));
                return;
            }

            // Get transactions
            $transactions = $this->report_model->get_transactions_report($start_date, $end_date, $filters);

            // Format response data
            $formatted_data = array();
            foreach ($transactions as $transaction) {
                $formatted_data[] = array(
                    'id' => $transaction['id'],
                    'from_account_id' => $transaction['fromaccountid'],
                    'from_account_name' => isset($transaction['from_account_name']) ? $transaction['from_account_name'] : '',
                    'from_account_number' => isset($transaction['from_account_number']) ? $transaction['from_account_number'] : '',
                    'to_account_id' => $transaction['toaccountid'],
                    'to_account_name' => isset($transaction['to_account_name']) ? $transaction['to_account_name'] : '',
                    'to_account_number' => isset($transaction['to_account_number']) ? $transaction['to_account_number'] : '',
                    'amount' => $transaction['amount'],
                    'date' => $transaction['date'],
                    'is_active' => $transaction['is_active'],
                    'note' => isset($transaction['note']) ? $transaction['note'] : '',
                    'created_at' => isset($transaction['created_at']) ? $transaction['created_at'] : '',
                    'updated_at' => isset($transaction['updated_at']) ? $transaction['updated_at'] : ''
                );
            }

            json_output(200, array(
                'status' => 1,
                'message' => 'Account transactions retrieved successfully',
                'total_records' => count($formatted_data),
                'data' => $formatted_data
            ));

        } catch (Exception $e) {
            log_message('error', 'Account Transaction Report API List Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Get transaction summary
     * 
     * Retrieves summary statistics for account transactions within a date range.
     * 
     * @return void Outputs JSON response
     */
    public function summary()
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

            $start_date = isset($input['date_from']) && !empty($input['date_from']) ? $input['date_from'] : null;
            $end_date = isset($input['date_to']) && !empty($input['date_to']) ? $input['date_to'] : null;

            // Validate date range if provided
            if (($start_date && !$end_date) || (!$start_date && $end_date)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Both date_from and date_to are required when filtering by date',
                    'data' => null
                ));
                return;
            }

            // Get summary
            $summary = $this->report_model->get_transaction_summary($start_date, $end_date);

            json_output(200, array(
                'status' => 1,
                'message' => 'Transaction summary retrieved successfully',
                'data' => array(
                    'total_transactions' => (int)$summary['total_transactions'],
                    'total_amount' => (float)$summary['total_amount']
                )
            ));

        } catch (Exception $e) {
            log_message('error', 'Account Transaction Report API Summary Error: ' . $e->getMessage());
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
            log_message('error', 'Account Transaction Report API Accounts Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Get financial years list
     * 
     * Retrieves a list of all financial years.
     * 
     * @return void Outputs JSON response
     */
    public function financial_years()
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

            // Get financial years
            $financial_years = $this->report_model->get_financial_years();

            json_output(200, array(
                'status' => 1,
                'message' => 'Financial years retrieved successfully',
                'total_records' => count($financial_years),
                'data' => $financial_years
            ));

        } catch (Exception $e) {
            log_message('error', 'Account Transaction Report API Financial Years Error: ' . $e->getMessage());
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
            log_message('error', 'Account Transaction Report API Active Financial Year Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Get specific transaction
     * 
     * Retrieves details of a specific transaction by its ID.
     * 
     * @param int $id Transaction ID
     * @return void Outputs JSON response
     */
    public function get($id = null)
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

            // Validate ID
            if (empty($id) || !is_numeric($id) || $id <= 0) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or missing transaction ID',
                    'data' => null
                ));
                return;
            }

            $id = (int)$id;

            // Get transaction
            $transaction = $this->report_model->get_transaction($id);
            
            if (empty($transaction)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Transaction not found',
                    'data' => null
                ));
                return;
            }

            $formatted_data = array(
                'id' => $transaction['id'],
                'from_account_id' => $transaction['fromaccountid'],
                'from_account_name' => isset($transaction['from_account_name']) ? $transaction['from_account_name'] : '',
                'from_account_number' => isset($transaction['from_account_number']) ? $transaction['from_account_number'] : '',
                'to_account_id' => $transaction['toaccountid'],
                'to_account_name' => isset($transaction['to_account_name']) ? $transaction['to_account_name'] : '',
                'to_account_number' => isset($transaction['to_account_number']) ? $transaction['to_account_number'] : '',
                'amount' => $transaction['amount'],
                'date' => $transaction['date'],
                'is_active' => $transaction['is_active'],
                'note' => isset($transaction['note']) ? $transaction['note'] : '',
                'created_at' => isset($transaction['created_at']) ? $transaction['created_at'] : '',
                'updated_at' => isset($transaction['updated_at']) ? $transaction['updated_at'] : ''
            );

            json_output(200, array(
                'status' => 1,
                'message' => 'Transaction retrieved successfully',
                'data' => $formatted_data
            ));

        } catch (Exception $e) {
            log_message('error', 'Account Transaction Report API Get Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Delete transaction
     * 
     * Deletes a specific transaction by its ID.
     * 
     * @param int $id Transaction ID
     * @return void Outputs JSON response
     */
    public function delete($id = null)
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

            // Validate ID
            if (empty($id) || !is_numeric($id) || $id <= 0) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or missing transaction ID',
                    'data' => null
                ));
                return;
            }

            $id = (int)$id;

            // Check if transaction exists
            if (!$this->report_model->transaction_exists($id)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Transaction not found',
                    'data' => null
                ));
                return;
            }

            // Delete transaction
            $result = $this->report_model->delete_transaction($id);
            
            if ($result === false) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Failed to delete transaction',
                    'data' => null
                ));
                return;
            }

            json_output(200, array(
                'status' => 1,
                'message' => 'Transaction deleted successfully',
                'data' => null
            ));

        } catch (Exception $e) {
            log_message('error', 'Account Transaction Report API Delete Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }
}

