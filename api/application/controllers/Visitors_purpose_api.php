<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Visitors Purpose API Controller
 * 
 * This controller provides RESTful API endpoints for managing visitor purpose records.
 * It handles CRUD operations for visitor purposes used in the front office module.
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Webservices
 * @author     School Management System
 * @version    1.0.0
 */
class Visitors_purpose_api extends CI_Controller
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
        $this->load->model('Visitors_purpose_model', 'visitors_purpose_model');
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
     * List all visitors purposes
     * 
     * Retrieves a list of all visitor purposes with optional search capabilities.
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

            // Extract search parameter
            $search = isset($input['search']) && !empty($input['search']) ? $this->db->escape_str($input['search']) : null;

            // Build query
            $this->db->select('*');
            $this->db->from('visitors_purpose');

            // Apply search filter if provided
            if ($search !== null) {
                $this->db->group_start();
                $this->db->like('visitors_purpose', $search);
                $this->db->or_like('description', $search);
                $this->db->group_end();
            }

            // Get purpose list
            $purpose_list = $this->db->order_by('id', 'ASC')->get()->result_array();

            json_output(200, array(
                'status' => 1,
                'message' => 'Visitor purposes retrieved successfully',
                'total_records' => count($purpose_list),
                'data' => $purpose_list
            ));

        } catch (Exception $e) {
            log_message('error', 'Visitors Purpose API List Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Get a specific visitors purpose
     * 
     * Retrieves details of a specific visitor purpose by its ID.
     * 
     * @param int $id Purpose ID
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
                    'message' => 'Invalid or missing purpose ID',
                    'data' => null
                ));
                return;
            }

            $id = (int)$id;

            // Get purpose details
            $purpose = $this->visitors_purpose_model->visitors_purpose_list($id);
            
            if (empty($purpose)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Visitor purpose not found',
                    'data' => null
                ));
                return;
            }

            json_output(200, array(
                'status' => 1,
                'message' => 'Visitor purpose retrieved successfully',
                'data' => $purpose
            ));

        } catch (Exception $e) {
            log_message('error', 'Visitors Purpose API Get Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Create a new visitors purpose
     * 
     * Creates a new visitor purpose record.
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
            if (empty($input['visitors_purpose']) || trim($input['visitors_purpose']) === '') {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Purpose is required',
                    'data' => null
                ));
                return;
            }

            // Prepare data for insertion
            $purpose_data = array(
                'visitors_purpose' => $this->db->escape_str(trim($input['visitors_purpose'])),
                'description' => isset($input['description']) ? $this->db->escape_str(trim($input['description'])) : ''
            );

            // Insert the purpose
            $purpose_id = $this->visitors_purpose_model->add($purpose_data);

            // Get the created purpose
            $created_purpose = $this->visitors_purpose_model->visitors_purpose_list($purpose_id);

            json_output(201, array(
                'status' => 1,
                'message' => 'Visitor purpose created successfully',
                'data' => $created_purpose
            ));

        } catch (Exception $e) {
            log_message('error', 'Visitors Purpose API Create Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Update a visitors purpose
     * 
     * Updates an existing visitor purpose record.
     * 
     * @param int $id Purpose ID
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
                    'message' => 'Invalid or missing purpose ID',
                    'data' => null
                ));
                return;
            }

            $id = (int)$id;

            // Check if purpose exists
            $existing_purpose = $this->visitors_purpose_model->visitors_purpose_list($id);
            if (empty($existing_purpose)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Visitor purpose not found',
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
            if (empty($input['visitors_purpose']) || trim($input['visitors_purpose']) === '') {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Purpose is required',
                    'data' => null
                ));
                return;
            }

            // Prepare data for update
            $purpose_data = array(
                'visitors_purpose' => $this->db->escape_str(trim($input['visitors_purpose'])),
                'description' => isset($input['description']) ? $this->db->escape_str(trim($input['description'])) : ''
            );

            // Update the purpose
            $this->visitors_purpose_model->update($id, $purpose_data);

            // Get the updated purpose
            $updated_purpose = $this->visitors_purpose_model->visitors_purpose_list($id);

            json_output(200, array(
                'status' => 1,
                'message' => 'Visitor purpose updated successfully',
                'data' => $updated_purpose
            ));

        } catch (Exception $e) {
            log_message('error', 'Visitors Purpose API Update Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Delete a visitors purpose
     * 
     * Deletes a visitor purpose record.
     * 
     * @param int $id Purpose ID
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
                    'message' => 'Invalid or missing purpose ID',
                    'data' => null
                ));
                return;
            }

            $id = (int)$id;

            // Check if purpose exists
            $existing_purpose = $this->visitors_purpose_model->visitors_purpose_list($id);
            if (empty($existing_purpose)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Visitor purpose not found',
                    'data' => null
                ));
                return;
            }

            // Delete the purpose
            $this->visitors_purpose_model->delete($id);

            json_output(200, array(
                'status' => 1,
                'message' => 'Visitor purpose deleted successfully',
                'data' => array('id' => $id)
            ));

        } catch (Exception $e) {
            log_message('error', 'Visitors Purpose API Delete Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }
}

