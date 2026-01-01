<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Add Account API Controller
 * 
 * This controller provides RESTful API endpoints for managing accounts.
 * It handles creating, reading, updating, and deleting accounts.
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Webservices
 * @author     School Management System
 * @version    1.0.0
 */
class Add_account_api extends CI_Controller
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
        $this->load->model('Add_account_model', 'account_model');
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
     * List accounts
     * 
     * Retrieves a list of accounts with optional filters.
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
            
            if (isset($input['account_category']) && !empty($input['account_category'])) {
                $filters['account_category'] = $input['account_category'];
            }
            
            if (isset($input['account_type']) && !empty($input['account_type'])) {
                $filters['account_type'] = $input['account_type'];
            }
            
            if (isset($input['account_role']) && !empty($input['account_role'])) {
                $filters['account_role'] = $input['account_role'];
            }
            
            if (isset($input['is_active']) && $input['is_active'] !== '') {
                $filters['is_active'] = $input['is_active'];
            }

            // Get accounts
            $accounts = $this->account_model->get_accounts($filters);

            // Format payment modes
            $formatted_data = array();
            foreach ($accounts as $account) {
                $payment_modes = array();
                if (isset($account['cash']) && $account['cash'] == 1) {
                    $payment_modes[] = 'cash';
                }
                if (isset($account['cheque']) && $account['cheque'] == 1) {
                    $payment_modes[] = 'cheque';
                }
                if (isset($account['dd']) && $account['dd'] == 1) {
                    $payment_modes[] = 'dd';
                }
                if (isset($account['bank_transfer']) && $account['bank_transfer'] == 1) {
                    $payment_modes[] = 'bank_transfer';
                }
                if (isset($account['upi']) && $account['upi'] == 1) {
                    $payment_modes[] = 'upi';
                }
                if (isset($account['card']) && $account['card'] == 1) {
                    $payment_modes[] = 'card';
                }

                $formatted_data[] = array(
                    'id' => $account['id'],
                    'name' => $account['name'],
                    'code' => $account['code'],
                    'account_category' => $account['account_category'],
                    'account_category_name' => isset($account['account_category_name']) ? $account['account_category_name'] : '',
                    'account_type' => $account['account_type'],
                    'account_type_name' => isset($account['account_type_name']) ? $account['account_type_name'] : '',
                    'account_role' => $account['account_role'],
                    'is_active' => $account['is_active'],
                    'description' => isset($account['description']) ? $account['description'] : '',
                    'payment_modes' => $payment_modes,
                    'cash' => isset($account['cash']) ? (int)$account['cash'] : 0,
                    'cheque' => isset($account['cheque']) ? (int)$account['cheque'] : 0,
                    'dd' => isset($account['dd']) ? (int)$account['dd'] : 0,
                    'bank_transfer' => isset($account['bank_transfer']) ? (int)$account['bank_transfer'] : 0,
                    'upi' => isset($account['upi']) ? (int)$account['upi'] : 0,
                    'card' => isset($account['card']) ? (int)$account['card'] : 0,
                    'created_at' => isset($account['created_at']) ? $account['created_at'] : '',
                    'updated_at' => isset($account['updated_at']) ? $account['updated_at'] : ''
                );
            }

            json_output(200, array(
                'status' => 1,
                'message' => 'Accounts retrieved successfully',
                'total_records' => count($formatted_data),
                'data' => $formatted_data
            ));

        } catch (Exception $e) {
            log_message('error', 'Add Account API List Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Get specific account
     * 
     * Retrieves detailed information about a specific account by its ID.
     * 
     * @param int $id Account ID
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
                    'message' => 'Invalid or missing account ID',
                    'data' => null
                ));
                return;
            }

            $id = (int)$id;

            // Get account
            $account = $this->account_model->get_account($id);
            
            if (empty($account)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Account not found',
                    'data' => null
                ));
                return;
            }

            // Format payment modes
            $payment_modes = array();
            if (isset($account['cash']) && $account['cash'] == 1) {
                $payment_modes[] = 'cash';
            }
            if (isset($account['cheque']) && $account['cheque'] == 1) {
                $payment_modes[] = 'cheque';
            }
            if (isset($account['dd']) && $account['dd'] == 1) {
                $payment_modes[] = 'dd';
            }
            if (isset($account['bank_transfer']) && $account['bank_transfer'] == 1) {
                $payment_modes[] = 'bank_transfer';
            }
            if (isset($account['upi']) && $account['upi'] == 1) {
                $payment_modes[] = 'upi';
            }
            if (isset($account['card']) && $account['card'] == 1) {
                $payment_modes[] = 'card';
            }

            $formatted_data = array(
                'id' => $account['id'],
                'name' => $account['name'],
                'code' => $account['code'],
                'account_category' => $account['account_category'],
                'account_category_name' => isset($account['account_category_name']) ? $account['account_category_name'] : '',
                'account_type' => $account['account_type'],
                'account_type_name' => isset($account['account_type_name']) ? $account['account_type_name'] : '',
                'account_role' => $account['account_role'],
                'is_active' => $account['is_active'],
                'description' => isset($account['description']) ? $account['description'] : '',
                'payment_modes' => $payment_modes,
                'cash' => isset($account['cash']) ? (int)$account['cash'] : 0,
                'cheque' => isset($account['cheque']) ? (int)$account['cheque'] : 0,
                'dd' => isset($account['dd']) ? (int)$account['dd'] : 0,
                'bank_transfer' => isset($account['bank_transfer']) ? (int)$account['bank_transfer'] : 0,
                'upi' => isset($account['upi']) ? (int)$account['upi'] : 0,
                'card' => isset($account['card']) ? (int)$account['card'] : 0,
                'created_at' => isset($account['created_at']) ? $account['created_at'] : '',
                'updated_at' => isset($account['updated_at']) ? $account['updated_at'] : ''
            );

            json_output(200, array(
                'status' => 1,
                'message' => 'Account retrieved successfully',
                'data' => $formatted_data
            ));

        } catch (Exception $e) {
            log_message('error', 'Add Account API Get Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Create account
     * 
     * Creates a new account.
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
            if (empty($input['name']) || !is_string($input['name'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or missing name',
                    'data' => null
                ));
                return;
            }

            if (empty($input['code']) || !is_string($input['code'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or missing code',
                    'data' => null
                ));
                return;
            }

            if (empty($input['account_category']) || !is_numeric($input['account_category'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or missing account_category',
                    'data' => null
                ));
                return;
            }

            if (empty($input['account_type']) || !is_numeric($input['account_type'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or missing account_type',
                    'data' => null
                ));
                return;
            }

            if (empty($input['account_role']) || !in_array($input['account_role'], array('both', 'debitor', 'creditor'))) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or missing account_role. Must be one of: both, debitor, creditor',
                    'data' => null
                ));
                return;
            }

            // Validate payment modes
            if (empty($input['payment_modes']) || !is_array($input['payment_modes']) || empty($input['payment_modes'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or missing payment_modes. Must be a non-empty array',
                    'data' => null
                ));
                return;
            }

            $valid_payment_modes = array('cash', 'cheque', 'dd', 'bank_transfer', 'upi', 'card');
            foreach ($input['payment_modes'] as $mode) {
                if (!in_array($mode, $valid_payment_modes)) {
                    json_output(400, array(
                        'status' => 0,
                        'message' => 'Invalid payment mode: ' . $mode . '. Valid modes are: cash, cheque, dd, bank_transfer, upi, card',
                        'data' => null
                    ));
                    return;
                }
            }

            // Check if name already exists
            if ($this->account_model->name_exists($input['name'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Account name already exists',
                    'data' => null
                ));
                return;
            }

            // Check if code already exists
            if ($this->account_model->code_exists($input['code'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Account code already exists',
                    'data' => null
                ));
                return;
            }

            // Prepare account data
            $account_data = array(
                'name' => trim($input['name']),
                'code' => trim($input['code']),
                'account_category' => (int)$input['account_category'],
                'account_type' => (int)$input['account_type'],
                'account_role' => $input['account_role'],
                'description' => isset($input['description']) ? $input['description'] : '',
                'is_active' => isset($input['is_active']) ? $input['is_active'] : 'yes',
                'cash' => in_array('cash', $input['payment_modes']) ? 1 : 0,
                'cheque' => in_array('cheque', $input['payment_modes']) ? 1 : 0,
                'dd' => in_array('dd', $input['payment_modes']) ? 1 : 0,
                'bank_transfer' => in_array('bank_transfer', $input['payment_modes']) ? 1 : 0,
                'upi' => in_array('upi', $input['payment_modes']) ? 1 : 0,
                'card' => in_array('card', $input['payment_modes']) ? 1 : 0
            );

            // Create account
            $account_id = $this->account_model->create_account($account_data);

            if ($account_id === false) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Failed to create account',
                    'data' => null
                ));
                return;
            }

            // Get created account
            $account = $this->account_model->get_account($account_id);

            // Format payment modes
            $payment_modes = array();
            if (isset($account['cash']) && $account['cash'] == 1) {
                $payment_modes[] = 'cash';
            }
            if (isset($account['cheque']) && $account['cheque'] == 1) {
                $payment_modes[] = 'cheque';
            }
            if (isset($account['dd']) && $account['dd'] == 1) {
                $payment_modes[] = 'dd';
            }
            if (isset($account['bank_transfer']) && $account['bank_transfer'] == 1) {
                $payment_modes[] = 'bank_transfer';
            }
            if (isset($account['upi']) && $account['upi'] == 1) {
                $payment_modes[] = 'upi';
            }
            if (isset($account['card']) && $account['card'] == 1) {
                $payment_modes[] = 'card';
            }

            $formatted_data = array(
                'id' => $account['id'],
                'name' => $account['name'],
                'code' => $account['code'],
                'account_category' => $account['account_category'],
                'account_category_name' => isset($account['account_category_name']) ? $account['account_category_name'] : '',
                'account_type' => $account['account_type'],
                'account_type_name' => isset($account['account_type_name']) ? $account['account_type_name'] : '',
                'account_role' => $account['account_role'],
                'is_active' => $account['is_active'],
                'description' => isset($account['description']) ? $account['description'] : '',
                'payment_modes' => $payment_modes,
                'cash' => isset($account['cash']) ? (int)$account['cash'] : 0,
                'cheque' => isset($account['cheque']) ? (int)$account['cheque'] : 0,
                'dd' => isset($account['dd']) ? (int)$account['dd'] : 0,
                'bank_transfer' => isset($account['bank_transfer']) ? (int)$account['bank_transfer'] : 0,
                'upi' => isset($account['upi']) ? (int)$account['upi'] : 0,
                'card' => isset($account['card']) ? (int)$account['card'] : 0,
                'created_at' => isset($account['created_at']) ? $account['created_at'] : '',
                'updated_at' => isset($account['updated_at']) ? $account['updated_at'] : ''
            );

            json_output(201, array(
                'status' => 1,
                'message' => 'Account created successfully',
                'data' => $formatted_data
            ));

        } catch (Exception $e) {
            log_message('error', 'Add Account API Create Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Update account
     * 
     * Updates an existing account.
     * 
     * @param int $id Account ID
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
                    'message' => 'Invalid or missing account ID',
                    'data' => null
                ));
                return;
            }

            $id = (int)$id;

            // Check if account exists
            if (!$this->account_model->account_exists($id)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Account not found',
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
            if (isset($input['name']) && (empty($input['name']) || !is_string($input['name']))) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid name',
                    'data' => null
                ));
                return;
            }

            if (isset($input['code']) && (empty($input['code']) || !is_string($input['code']))) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid code',
                    'data' => null
                ));
                return;
            }

            if (isset($input['account_role']) && !in_array($input['account_role'], array('both', 'debitor', 'creditor'))) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid account_role. Must be one of: both, debitor, creditor',
                    'data' => null
                ));
                return;
            }

            // Check if name already exists (excluding current account)
            if (isset($input['name']) && $this->account_model->name_exists($input['name'], $id)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Account name already exists',
                    'data' => null
                ));
                return;
            }

            // Check if code already exists (excluding current account)
            if (isset($input['code']) && $this->account_model->code_exists($input['code'], $id)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Account code already exists',
                    'data' => null
                ));
                return;
            }

            // Validate payment modes if provided
            if (isset($input['payment_modes'])) {
                if (!is_array($input['payment_modes']) || empty($input['payment_modes'])) {
                    json_output(400, array(
                        'status' => 0,
                        'message' => 'Invalid payment_modes. Must be a non-empty array',
                        'data' => null
                    ));
                    return;
                }

                $valid_payment_modes = array('cash', 'cheque', 'dd', 'bank_transfer', 'upi', 'card');
                foreach ($input['payment_modes'] as $mode) {
                    if (!in_array($mode, $valid_payment_modes)) {
                        json_output(400, array(
                            'status' => 0,
                            'message' => 'Invalid payment mode: ' . $mode . '. Valid modes are: cash, cheque, dd, bank_transfer, upi, card',
                            'data' => null
                        ));
                        return;
                    }
                }
            }

            // Prepare update data
            $update_data = array();
            
            if (isset($input['name'])) {
                $update_data['name'] = trim($input['name']);
            }
            if (isset($input['code'])) {
                $update_data['code'] = trim($input['code']);
            }
            if (isset($input['account_category'])) {
                $update_data['account_category'] = (int)$input['account_category'];
            }
            if (isset($input['account_type'])) {
                $update_data['account_type'] = (int)$input['account_type'];
            }
            if (isset($input['account_role'])) {
                $update_data['account_role'] = $input['account_role'];
            }
            if (isset($input['description'])) {
                $update_data['description'] = $input['description'];
            }
            if (isset($input['is_active'])) {
                $update_data['is_active'] = $input['is_active'];
            }
            if (isset($input['payment_modes'])) {
                $update_data['cash'] = in_array('cash', $input['payment_modes']) ? 1 : 0;
                $update_data['cheque'] = in_array('cheque', $input['payment_modes']) ? 1 : 0;
                $update_data['dd'] = in_array('dd', $input['payment_modes']) ? 1 : 0;
                $update_data['bank_transfer'] = in_array('bank_transfer', $input['payment_modes']) ? 1 : 0;
                $update_data['upi'] = in_array('upi', $input['payment_modes']) ? 1 : 0;
                $update_data['card'] = in_array('card', $input['payment_modes']) ? 1 : 0;
            }

            if (empty($update_data)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'No data provided for update',
                    'data' => null
                ));
                return;
            }

            // Update account
            $result = $this->account_model->update_account($id, $update_data);

            if ($result === false) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Failed to update account',
                    'data' => null
                ));
                return;
            }

            // Get updated account
            $account = $this->account_model->get_account($id);

            // Format payment modes
            $payment_modes = array();
            if (isset($account['cash']) && $account['cash'] == 1) {
                $payment_modes[] = 'cash';
            }
            if (isset($account['cheque']) && $account['cheque'] == 1) {
                $payment_modes[] = 'cheque';
            }
            if (isset($account['dd']) && $account['dd'] == 1) {
                $payment_modes[] = 'dd';
            }
            if (isset($account['bank_transfer']) && $account['bank_transfer'] == 1) {
                $payment_modes[] = 'bank_transfer';
            }
            if (isset($account['upi']) && $account['upi'] == 1) {
                $payment_modes[] = 'upi';
            }
            if (isset($account['card']) && $account['card'] == 1) {
                $payment_modes[] = 'card';
            }

            $formatted_data = array(
                'id' => $account['id'],
                'name' => $account['name'],
                'code' => $account['code'],
                'account_category' => $account['account_category'],
                'account_category_name' => isset($account['account_category_name']) ? $account['account_category_name'] : '',
                'account_type' => $account['account_type'],
                'account_type_name' => isset($account['account_type_name']) ? $account['account_type_name'] : '',
                'account_role' => $account['account_role'],
                'is_active' => $account['is_active'],
                'description' => isset($account['description']) ? $account['description'] : '',
                'payment_modes' => $payment_modes,
                'cash' => isset($account['cash']) ? (int)$account['cash'] : 0,
                'cheque' => isset($account['cheque']) ? (int)$account['cheque'] : 0,
                'dd' => isset($account['dd']) ? (int)$account['dd'] : 0,
                'bank_transfer' => isset($account['bank_transfer']) ? (int)$account['bank_transfer'] : 0,
                'upi' => isset($account['upi']) ? (int)$account['upi'] : 0,
                'card' => isset($account['card']) ? (int)$account['card'] : 0,
                'created_at' => isset($account['created_at']) ? $account['created_at'] : '',
                'updated_at' => isset($account['updated_at']) ? $account['updated_at'] : ''
            );

            json_output(200, array(
                'status' => 1,
                'message' => 'Account updated successfully',
                'data' => $formatted_data
            ));

        } catch (Exception $e) {
            log_message('error', 'Add Account API Update Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Delete account
     * 
     * Deletes an account.
     * 
     * @param int $id Account ID
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
                    'message' => 'Invalid or missing account ID',
                    'data' => null
                ));
                return;
            }

            $id = (int)$id;

            // Check if account exists
            if (!$this->account_model->account_exists($id)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Account not found',
                    'data' => null
                ));
                return;
            }

            // Delete account
            $result = $this->account_model->delete_account($id);

            if ($result === false) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Failed to delete account',
                    'data' => null
                ));
                return;
            }

            json_output(200, array(
                'status' => 1,
                'message' => 'Account deleted successfully',
                'data' => null
            ));

        } catch (Exception $e) {
            log_message('error', 'Add Account API Delete Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Get account categories
     * 
     * Retrieves a list of account categories for dropdown selection.
     * 
     * @return void Outputs JSON response
     */
    public function categories()
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

            // Get categories
            $categories = $this->account_model->get_account_categories();

            json_output(200, array(
                'status' => 1,
                'message' => 'Account categories retrieved successfully',
                'total_records' => count($categories),
                'data' => $categories
            ));

        } catch (Exception $e) {
            log_message('error', 'Add Account API Categories Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Get account types
     * 
     * Retrieves a list of account types, optionally filtered by category.
     * 
     * @return void Outputs JSON response
     */
    public function types()
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

            // Get account types
            if (isset($input['account_category_id']) && !empty($input['account_category_id'])) {
                $types = $this->account_model->get_account_types_by_category($input['account_category_id']);
            } else {
                $types = $this->account_model->get_account_types();
            }

            json_output(200, array(
                'status' => 1,
                'message' => 'Account types retrieved successfully',
                'total_records' => count($types),
                'data' => $types
            ));

        } catch (Exception $e) {
            log_message('error', 'Add Account API Types Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Get account roles
     * 
     * Retrieves a list of account roles for dropdown selection.
     * 
     * @return void Outputs JSON response
     */
    public function roles()
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

            // Get roles
            $roles = $this->account_model->get_account_roles();

            json_output(200, array(
                'status' => 1,
                'message' => 'Account roles retrieved successfully',
                'total_records' => count($roles),
                'data' => $roles
            ));

        } catch (Exception $e) {
            log_message('error', 'Add Account API Roles Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }
}

