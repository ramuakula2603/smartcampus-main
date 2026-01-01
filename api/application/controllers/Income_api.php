<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Income API Controller
 * 
 * This controller provides RESTful API endpoints for income management.
 * It handles CRUD operations for income records.
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Webservices
 * @author     School Management System
 * @version    1.0.0
 */
class Income_api extends CI_Controller
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

        // Set JSON content type early
        $this->output->set_content_type('application/json');

        // Load essential helpers
        $this->load->helper('json_output');

        // Load required models
        try {
            $this->load->model(array(
                'income_model',
                'incomehead_model',
                'setting_model'
            ));
        } catch (Exception $e) {
            log_message('error', 'Error loading models: ' . $e->getMessage());
        }

        // Load libraries
        try {
            $this->load->library(array('customlib'));
        } catch (Exception $e) {
            log_message('error', 'Error loading libraries: ' . $e->getMessage());
        }
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
     * List all income records
     * 
     * Retrieves a list of all income records.
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

            // Get all income records
            $income_records = $this->income_model->get();

            // Format response data
            $formatted_records = array();
            if (!empty($income_records)) {
                foreach ($income_records as $record) {
                    $formatted_records[] = array(
                        'id' => $record['id'],
                        'name' => $record['name'],
                        'invoice_no' => $record['invoice_no'],
                        'date' => $record['date'],
                        'amount' => $record['amount'],
                        'income_category' => $record['income_category'],
                        'income_head_id' => $record['income_head_id'],
                        'note' => isset($record['note']) ? $record['note'] : null,
                        'documents' => isset($record['documents']) ? $record['documents'] : null
                    );
                }
            }

            json_output(200, array(
                'status' => 1,
                'message' => 'Income records retrieved successfully',
                'total_records' => count($formatted_records),
                'data' => $formatted_records
            ));

        } catch (Exception $e) {
            log_message('error', 'Income API List Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Get single income record
     * 
     * Retrieves detailed information for a specific income record.
     * 
     * @param int $id Income ID
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

            // Validate ID parameter
            if ($id === null || !is_numeric($id) || $id <= 0) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or missing income ID',
                    'data' => null
                ));
                return;
            }

            // Get income record
            $income = $this->income_model->get($id);

            if (empty($income)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Income record not found',
                    'data' => null
                ));
                return;
            }

            // Format response data
            $formatted_income = array(
                'id' => $income['id'],
                'name' => $income['name'],
                'invoice_no' => $income['invoice_no'],
                'date' => $income['date'],
                'amount' => $income['amount'],
                'income_category' => $income['income_category'],
                'income_head_id' => $income['income_head_id'],
                'note' => isset($income['note']) ? $income['note'] : null,
                'documents' => isset($income['documents']) ? $income['documents'] : null
            );

            json_output(200, array(
                'status' => 1,
                'message' => 'Income record retrieved successfully',
                'data' => $formatted_income
            ));

        } catch (Exception $e) {
            log_message('error', 'Income API Get Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Create new income record
     * 
     * Creates a new income record.
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

            // Validate required fields
            $validation_errors = $this->validate_income_data($input);
            if (!empty($validation_errors)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Validation failed',
                    'errors' => $validation_errors,
                    'data' => null
                ));
                return;
            }

            // Prepare data for insertion
            $data = array(
                'name' => trim($input['name']),
                'invoice_no' => isset($input['invoice_no']) ? trim($input['invoice_no']) : null,
                'date' => $input['date'],
                'amount' => $input['amount'],
                'income_head_id' => $input['income_head_id'],
                'note' => isset($input['note']) ? trim($input['note']) : null,
                'documents' => isset($input['documents']) ? $input['documents'] : null
            );

            // Create the record
            $new_id = $this->income_model->add($data);

            // Get the created record
            $created_income = $this->income_model->get($new_id);

            json_output(201, array(
                'status' => 1,
                'message' => 'Income record created successfully',
                'data' => array(
                    'id' => $created_income['id'],
                    'name' => $created_income['name'],
                    'invoice_no' => $created_income['invoice_no'],
                    'date' => $created_income['date'],
                    'amount' => $created_income['amount'],
                    'income_category' => $created_income['income_category'],
                    'income_head_id' => $created_income['income_head_id'],
                    'note' => $created_income['note'],
                    'documents' => $created_income['documents']
                )
            ));

        } catch (Exception $e) {
            log_message('error', 'Income API Create Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Validate income data
     * 
     * @param array $data Input data to validate
     * @return array Validation errors
     */
    private function validate_income_data($data)
    {
        $errors = array();

        // Validate name
        if (empty($data['name']) || trim($data['name']) === '') {
            $errors[] = 'Income name is required and cannot be empty';
        }

        // Validate date
        if (empty($data['date'])) {
            $errors[] = 'Date is required';
        } elseif (!$this->validate_date($data['date'])) {
            $errors[] = 'Invalid date format. Use YYYY-MM-DD format';
        }

        // Validate amount
        if (!isset($data['amount']) || !is_numeric($data['amount']) || $data['amount'] <= 0) {
            $errors[] = 'Amount is required and must be a positive number';
        }

        // Validate income head ID
        if (empty($data['income_head_id']) || !is_numeric($data['income_head_id'])) {
            $errors[] = 'Income head ID is required and must be a valid number';
        } else {
            // Check if income head exists
            $income_head = $this->incomehead_model->get($data['income_head_id']);
            if (empty($income_head)) {
                $errors[] = 'Invalid income head ID';
            }
        }

        return $errors;
    }

    /**
     * Update existing income record
     *
     * Updates an existing income record.
     *
     * @param int $id Income ID to update
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

            // Validate ID parameter
            if ($id === null || !is_numeric($id) || $id <= 0) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or missing income ID',
                    'data' => null
                ));
                return;
            }

            // Check if record exists
            $existing_income = $this->income_model->get($id);
            if (empty($existing_income)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Income record not found',
                    'data' => null
                ));
                return;
            }

            // Get input data
            $input = json_decode($this->input->raw_input_stream, true);

            // Validate required fields
            $validation_errors = $this->validate_income_data($input);
            if (!empty($validation_errors)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Validation failed',
                    'errors' => $validation_errors,
                    'data' => null
                ));
                return;
            }

            // Prepare data for update
            $data = array(
                'id' => $id,
                'name' => trim($input['name']),
                'invoice_no' => isset($input['invoice_no']) ? trim($input['invoice_no']) : $existing_income['invoice_no'],
                'date' => $input['date'],
                'amount' => $input['amount'],
                'income_head_id' => $input['income_head_id'],
                'note' => isset($input['note']) ? trim($input['note']) : $existing_income['note'],
                'documents' => isset($input['documents']) ? $input['documents'] : $existing_income['documents']
            );

            // Update the record
            $this->income_model->add($data);

            // Get the updated record
            $updated_income = $this->income_model->get($id);

            json_output(200, array(
                'status' => 1,
                'message' => 'Income record updated successfully',
                'data' => array(
                    'id' => $updated_income['id'],
                    'name' => $updated_income['name'],
                    'invoice_no' => $updated_income['invoice_no'],
                    'date' => $updated_income['date'],
                    'amount' => $updated_income['amount'],
                    'income_category' => $updated_income['income_category'],
                    'income_head_id' => $updated_income['income_head_id'],
                    'note' => $updated_income['note'],
                    'documents' => $updated_income['documents']
                )
            ));

        } catch (Exception $e) {
            log_message('error', 'Income API Update Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Delete income record
     *
     * Deletes an existing income record.
     *
     * @param int $id Income ID to delete
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

            // Validate ID parameter
            if ($id === null || !is_numeric($id) || $id <= 0) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or missing income ID',
                    'data' => null
                ));
                return;
            }

            // Check if record exists
            $existing_income = $this->income_model->get($id);
            if (empty($existing_income)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Income record not found',
                    'data' => null
                ));
                return;
            }

            // Store income info before deletion
            $deleted_income_info = array(
                'id' => $existing_income['id'],
                'name' => $existing_income['name'],
                'invoice_no' => $existing_income['invoice_no'],
                'amount' => $existing_income['amount']
            );

            // Delete the record
            $this->income_model->remove($id);

            json_output(200, array(
                'status' => 1,
                'message' => 'Income record deleted successfully',
                'data' => $deleted_income_info
            ));

        } catch (Exception $e) {
            log_message('error', 'Income API Delete Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Validate date format
     *
     * @param string $date Date string to validate
     * @return bool True if valid, false otherwise
     */
    private function validate_date($date)
    {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
}
