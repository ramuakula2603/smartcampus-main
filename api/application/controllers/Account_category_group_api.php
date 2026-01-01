<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Account Category Group API Controller
 * 
 * This controller provides RESTful API endpoints for managing account category groups.
 * It handles creating, reading, updating, and deleting relationships between account categories and account types.
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Webservices
 * @author     School Management System
 * @version    1.0.0
 */
class Account_category_group_api extends CI_Controller
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
        $this->load->model('Account_category_group_model', 'category_group_model');
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
     * List category groups (grouped by category)
     * 
     * Retrieves a list of category groups grouped by account category.
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
            
            if (isset($input['account_category_id']) && !empty($input['account_category_id'])) {
                $filters['account_category_id'] = $input['account_category_id'];
            }

            // Get category groups
            $category_groups = $this->category_group_model->get_category_groups($filters);

            json_output(200, array(
                'status' => 1,
                'message' => 'Category groups retrieved successfully',
                'total_records' => count($category_groups),
                'data' => $category_groups
            ));

        } catch (Exception $e) {
            log_message('error', 'Account Category Group API List Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * List category groups (flat list)
     * 
     * Retrieves a flat list of all category groups (not grouped).
     * 
     * @return void Outputs JSON response
     */
    public function list_flat()
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
            
            if (isset($input['account_category_id']) && !empty($input['account_category_id'])) {
                $filters['account_category_id'] = $input['account_category_id'];
            }
            
            if (isset($input['account_type_id']) && !empty($input['account_type_id'])) {
                $filters['account_type_id'] = $input['account_type_id'];
            }
            
            if (isset($input['is_active']) && $input['is_active'] !== '') {
                $filters['is_active'] = $input['is_active'];
            }

            // Get category groups
            $category_groups = $this->category_group_model->list_category_groups($filters);

            json_output(200, array(
                'status' => 1,
                'message' => 'Category groups retrieved successfully',
                'total_records' => count($category_groups),
                'data' => $category_groups
            ));

        } catch (Exception $e) {
            log_message('error', 'Account Category Group API List Flat Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Get specific category group
     * 
     * Retrieves detailed information about a specific category group by its ID.
     * 
     * @param int $id Category group ID
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
                    'message' => 'Invalid or missing category group ID',
                    'data' => null
                ));
                return;
            }

            $id = (int)$id;

            // Get category group
            $category_group = $this->category_group_model->get_category_group($id);
            
            if (empty($category_group)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Category group not found',
                    'data' => null
                ));
                return;
            }

            $formatted_data = array(
                'id' => $category_group['id'],
                'account_category_id' => $category_group['accountcategory_id'],
                'account_category_name' => isset($category_group['account_category_name']) ? $category_group['account_category_name'] : '',
                'account_type_id' => $category_group['accounttype_id'],
                'account_type_name' => isset($category_group['account_type_name']) ? $category_group['account_type_name'] : '',
                'account_type_code' => isset($category_group['account_type_code']) ? $category_group['account_type_code'] : '',
                'is_active' => $category_group['is_active'],
                'created_at' => isset($category_group['created_at']) ? $category_group['created_at'] : ''
            );

            json_output(200, array(
                'status' => 1,
                'message' => 'Category group retrieved successfully',
                'data' => $formatted_data
            ));

        } catch (Exception $e) {
            log_message('error', 'Account Category Group API Get Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Create category group
     * 
     * Creates a new relationship between an account category and an account type.
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
            if (empty($input['account_category_id']) || !is_numeric($input['account_category_id'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or missing account_category_id',
                    'data' => null
                ));
                return;
            }

            if (empty($input['account_type_id']) || !is_numeric($input['account_type_id'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or missing account_type_id',
                    'data' => null
                ));
                return;
            }

            $account_category_id = (int)$input['account_category_id'];
            $account_type_id = (int)$input['account_type_id'];

            // Check if combination already exists
            if ($this->category_group_model->combination_exists($account_category_id, $account_type_id)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'This account category and account type combination already exists',
                    'data' => null
                ));
                return;
            }

            // Prepare category group data
            $group_data = array(
                'account_category_id' => $account_category_id,
                'account_type_id' => $account_type_id,
                'is_active' => isset($input['is_active']) ? $input['is_active'] : 'yes'
            );

            // Create category group
            $group_id = $this->category_group_model->create_category_group($group_data);

            if ($group_id === false) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Failed to create category group',
                    'data' => null
                ));
                return;
            }

            // Get created category group
            $category_group = $this->category_group_model->get_category_group($group_id);

            $formatted_data = array(
                'id' => $category_group['id'],
                'account_category_id' => $category_group['accountcategory_id'],
                'account_category_name' => isset($category_group['account_category_name']) ? $category_group['account_category_name'] : '',
                'account_type_id' => $category_group['accounttype_id'],
                'account_type_name' => isset($category_group['account_type_name']) ? $category_group['account_type_name'] : '',
                'account_type_code' => isset($category_group['account_type_code']) ? $category_group['account_type_code'] : '',
                'is_active' => $category_group['is_active'],
                'created_at' => isset($category_group['created_at']) ? $category_group['created_at'] : ''
            );

            json_output(201, array(
                'status' => 1,
                'message' => 'Category group created successfully',
                'data' => $formatted_data
            ));

        } catch (Exception $e) {
            log_message('error', 'Account Category Group API Create Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Update category group
     * 
     * Updates an existing category group.
     * 
     * @param int $id Category group ID
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
                    'message' => 'Invalid or missing category group ID',
                    'data' => null
                ));
                return;
            }

            $id = (int)$id;

            // Check if category group exists
            if (!$this->category_group_model->category_group_exists($id)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Category group not found',
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
            if (isset($input['account_category_id']) && (!is_numeric($input['account_category_id']) || $input['account_category_id'] <= 0)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid account_category_id',
                    'data' => null
                ));
                return;
            }

            if (isset($input['account_type_id']) && (!is_numeric($input['account_type_id']) || $input['account_type_id'] <= 0)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid account_type_id',
                    'data' => null
                ));
                return;
            }

            // Get current category group to check for duplicate combinations
            $current_group = $this->category_group_model->get_category_group($id);
            $account_category_id = isset($input['account_category_id']) ? (int)$input['account_category_id'] : $current_group['accountcategory_id'];
            $account_type_id = isset($input['account_type_id']) ? (int)$input['account_type_id'] : $current_group['accounttype_id'];

            // Check if new combination already exists (excluding current record)
            if ($this->category_group_model->combination_exists($account_category_id, $account_type_id, $id)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'This account category and account type combination already exists',
                    'data' => null
                ));
                return;
            }

            // Prepare update data
            $update_data = array();
            if (isset($input['account_category_id'])) {
                $update_data['account_category_id'] = (int)$input['account_category_id'];
            }
            if (isset($input['account_type_id'])) {
                $update_data['account_type_id'] = (int)$input['account_type_id'];
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

            // Update category group
            $result = $this->category_group_model->update_category_group($id, $update_data);

            if ($result === false) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Failed to update category group',
                    'data' => null
                ));
                return;
            }

            // Get updated category group
            $category_group = $this->category_group_model->get_category_group($id);

            $formatted_data = array(
                'id' => $category_group['id'],
                'account_category_id' => $category_group['accountcategory_id'],
                'account_category_name' => isset($category_group['account_category_name']) ? $category_group['account_category_name'] : '',
                'account_type_id' => $category_group['accounttype_id'],
                'account_type_name' => isset($category_group['account_type_name']) ? $category_group['account_type_name'] : '',
                'account_type_code' => isset($category_group['account_type_code']) ? $category_group['account_type_code'] : '',
                'is_active' => $category_group['is_active'],
                'created_at' => isset($category_group['created_at']) ? $category_group['created_at'] : ''
            );

            json_output(200, array(
                'status' => 1,
                'message' => 'Category group updated successfully',
                'data' => $formatted_data
            ));

        } catch (Exception $e) {
            log_message('error', 'Account Category Group API Update Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Delete category group
     * 
     * Deletes a category group.
     * 
     * @param int $id Category group ID
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
                    'message' => 'Invalid or missing category group ID',
                    'data' => null
                ));
                return;
            }

            $id = (int)$id;

            // Check if category group exists
            if (!$this->category_group_model->category_group_exists($id)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Category group not found',
                    'data' => null
                ));
                return;
            }

            // Delete category group
            $result = $this->category_group_model->delete_category_group($id);

            if ($result === false) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Failed to delete category group',
                    'data' => null
                ));
                return;
            }

            json_output(200, array(
                'status' => 1,
                'message' => 'Category group deleted successfully',
                'data' => null
            ));

        } catch (Exception $e) {
            log_message('error', 'Account Category Group API Delete Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }
}
