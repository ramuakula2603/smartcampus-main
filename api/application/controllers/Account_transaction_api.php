<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Account Transaction API Controller
 * 
 * This controller provides RESTful API endpoints for managing account transactions.
 * It handles creating, reading, updating, and deleting account transactions.
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Webservices
 * @author     School Management System
 * @version    1.0.0
 */
class Account_transaction_api extends CI_Controller
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
        $this->load->model('Account_transaction_model', 'transaction_model');
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
     * Get debit accounts
     * 
     * Retrieves a list of accounts that can be used as debit accounts.
     * 
     * @return void Outputs JSON response
     */
    public function debit_accounts()
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

            // Get debit accounts
            $accounts = $this->transaction_model->get_debit_accounts();

            json_output(200, array(
                'status' => 1,
                'message' => 'Debit accounts retrieved successfully',
                'total_records' => count($accounts),
                'data' => $accounts
            ));

        } catch (Exception $e) {
            log_message('error', 'Account Transaction API Debit Accounts Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Get credit accounts
     * 
     * Retrieves a list of accounts that can be used as credit accounts.
     * 
     * @return void Outputs JSON response
     */
    public function credit_accounts()
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

            // Get credit accounts
            $accounts = $this->transaction_model->get_credit_accounts();

            json_output(200, array(
                'status' => 1,
                'message' => 'Credit accounts retrieved successfully',
                'total_records' => count($accounts),
                'data' => $accounts
            ));

        } catch (Exception $e) {
            log_message('error', 'Account Transaction API Credit Accounts Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Create account transaction
     * 
     * Creates a new account transaction between a debit account and a credit account.
     * 
     * @return void Outputs JSON response
     */
    public function create()
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
            if ($input === null || empty($input)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or empty request data',
                    'data' => null
                ));
                return;
            }

            // Validate required fields
            if (empty($input['from_account_id']) || !is_numeric($input['from_account_id'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or missing from_account_id (debit account)',
                    'data' => null
                ));
                return;
            }

            if (empty($input['to_account_id']) || !is_numeric($input['to_account_id'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or missing to_account_id (credit account)',
                    'data' => null
                ));
                return;
            }

            if (empty($input['amount']) || !is_numeric($input['amount']) || $input['amount'] <= 0) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or missing amount. Amount must be greater than 0',
                    'data' => null
                ));
                return;
            }

            if (empty($input['date'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or missing date',
                    'data' => null
                ));
                return;
            }

            // Validate date format
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $input['date'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid date format. Use Y-m-d format (e.g., 2024-01-15)',
                    'data' => null
                ));
                return;
            }

            $from_account_id = (int)$input['from_account_id'];
            $to_account_id = (int)$input['to_account_id'];

            // Check if accounts exist
            if (!$this->transaction_model->account_exists($from_account_id)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Debit account not found',
                    'data' => null
                ));
                return;
            }

            if (!$this->transaction_model->account_exists($to_account_id)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Credit account not found',
                    'data' => null
                ));
                return;
            }

            // Check if accounts are different
            if ($from_account_id === $to_account_id) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Debit account and credit account cannot be the same',
                    'data' => null
                ));
                return;
            }

            // Prepare transaction data
            $transaction_data = array(
                'from_account_id' => $from_account_id,
                'to_account_id' => $to_account_id,
                'amount' => (float)$input['amount'],
                'date' => $input['date'],
                'note' => isset($input['note']) ? $input['note'] : '',
                'is_active' => isset($input['is_active']) ? $input['is_active'] : 'yes'
            );

            // Create transaction
            $transaction_id = $this->transaction_model->create_transaction($transaction_data);

            if ($transaction_id === false) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Failed to create account transaction',
                    'data' => null
                ));
                return;
            }

            // Get created transaction
            $transaction = $this->transaction_model->get_transaction($transaction_id);

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

            json_output(201, array(
                'status' => 1,
                'message' => 'Account transaction created successfully',
                'data' => $formatted_data
            ));

        } catch (Exception $e) {
            log_message('error', 'Account Transaction API Create Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * List account transactions
     * 
     * Retrieves a list of account transactions with optional filters.
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
            $filters = array();
            
            if (isset($input['from_account_id']) && !empty($input['from_account_id'])) {
                $filters['from_account_id'] = $input['from_account_id'];
            }
            
            if (isset($input['to_account_id']) && !empty($input['to_account_id'])) {
                $filters['to_account_id'] = $input['to_account_id'];
            }
            
            if (isset($input['date_from']) && !empty($input['date_from'])) {
                $filters['date_from'] = $input['date_from'];
            }
            
            if (isset($input['date_to']) && !empty($input['date_to'])) {
                $filters['date_to'] = $input['date_to'];
            }
            
            if (isset($input['is_active']) && $input['is_active'] !== '') {
                $filters['is_active'] = $input['is_active'];
            }

            // Get transactions
            $transactions = $this->transaction_model->list_transactions($filters);

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
            log_message('error', 'Account Transaction API List Error: ' . $e->getMessage());
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
     * Retrieves details of a specific account transaction by its ID.
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
            $transaction = $this->transaction_model->get_transaction($id);
            
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
            log_message('error', 'Account Transaction API Get Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Update transaction
     * 
     * Updates an existing account transaction.
     * 
     * @param int $id Transaction ID
     * @return void Outputs JSON response
     */
    public function update($id = null)
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
            if (!$this->transaction_model->transaction_exists($id)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Transaction not found',
                    'data' => null
                ));
                return;
            }

            // Get input data
            $input = json_decode($this->input->raw_input_stream, true);
            if ($input === null || empty($input)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or empty request data',
                    'data' => null
                ));
                return;
            }

            // Validate fields if provided
            if (isset($input['from_account_id']) && (!is_numeric($input['from_account_id']) || $input['from_account_id'] <= 0)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid from_account_id',
                    'data' => null
                ));
                return;
            }

            if (isset($input['to_account_id']) && (!is_numeric($input['to_account_id']) || $input['to_account_id'] <= 0)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid to_account_id',
                    'data' => null
                ));
                return;
            }

            if (isset($input['amount']) && (!is_numeric($input['amount']) || $input['amount'] <= 0)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid amount. Amount must be greater than 0',
                    'data' => null
                ));
                return;
            }

            if (isset($input['date']) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $input['date'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid date format. Use Y-m-d format (e.g., 2024-01-15)',
                    'data' => null
                ));
                return;
            }

            // Check if accounts exist if provided
            if (isset($input['from_account_id']) && !$this->transaction_model->account_exists($input['from_account_id'])) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Debit account not found',
                    'data' => null
                ));
                return;
            }

            if (isset($input['to_account_id']) && !$this->transaction_model->account_exists($input['to_account_id'])) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Credit account not found',
                    'data' => null
                ));
                return;
            }

            // Prepare update data
            $update_data = array();
            if (isset($input['from_account_id'])) {
                $update_data['from_account_id'] = (int)$input['from_account_id'];
            }
            if (isset($input['to_account_id'])) {
                $update_data['to_account_id'] = (int)$input['to_account_id'];
            }
            if (isset($input['amount'])) {
                $update_data['amount'] = (float)$input['amount'];
            }
            if (isset($input['date'])) {
                $update_data['date'] = $input['date'];
            }
            if (isset($input['note'])) {
                $update_data['note'] = $input['note'];
            }
            if (isset($input['is_active'])) {
                $update_data['is_active'] = $input['is_active'];
            }

            if (empty($update_data)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'No data provided for update',
                    'data' => null
                ));
                return;
            }

            // Check if accounts are different if both are provided
            if (isset($update_data['from_account_id']) && isset($update_data['to_account_id'])) {
                if ($update_data['from_account_id'] === $update_data['to_account_id']) {
                    json_output(400, array(
                        'status' => 0,
                        'message' => 'Debit account and credit account cannot be the same',
                        'data' => null
                    ));
                    return;
                }
            }

            // Update transaction
            $result = $this->transaction_model->update_transaction($id, $update_data);

            if ($result === false) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Failed to update transaction',
                    'data' => null
                ));
                return;
            }

            // Get updated transaction
            $transaction = $this->transaction_model->get_transaction($id);

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
                'message' => 'Transaction updated successfully',
                'data' => $formatted_data
            ));

        } catch (Exception $e) {
            log_message('error', 'Account Transaction API Update Error: ' . $e->getMessage());
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
     * Deletes an account transaction.
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
            if (!$this->transaction_model->transaction_exists($id)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Transaction not found',
                    'data' => null
                ));
                return;
            }

            // Delete transaction
            $result = $this->transaction_model->delete_transaction($id);

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
            log_message('error', 'Account Transaction API Delete Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }
}

