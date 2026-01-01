<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Postal Dispatch API Controller
 * 
 * This controller provides RESTful API endpoints for managing postal dispatch records.
 * It handles CRUD operations for postal dispatches including document attachments.
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Webservices
 * @author     School Management System
 * @version    1.0.0
 */
class Postal_dispatch_api extends CI_Controller
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
        $this->load->model('Dispatch_model', 'dispatch_model');
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
     * List all postal dispatches
     * 
     * Retrieves a list of all postal dispatches with optional filtering.
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
            $date_from = isset($input['date_from']) && !empty($input['date_from']) ? $input['date_from'] : null;
            $date_to = isset($input['date_to']) && !empty($input['date_to']) ? $input['date_to'] : null;
            $search = isset($input['search']) && !empty($input['search']) ? $this->db->escape_str($input['search']) : null;
            $to_title = isset($input['to_title']) && !empty($input['to_title']) ? $this->db->escape_str($input['to_title']) : null;
            $from_title = isset($input['from_title']) && !empty($input['from_title']) ? $this->db->escape_str($input['from_title']) : null;
            $reference_no = isset($input['reference_no']) && !empty($input['reference_no']) ? $this->db->escape_str($input['reference_no']) : null;

            // Build query
            $this->db->select('*');
            $this->db->from('dispatch_receive');
            $this->db->where('type', 'dispatch');

            // Apply filters
            if ($date_from !== null) {
                $this->db->where('date >=', $date_from);
            }

            if ($date_to !== null) {
                $this->db->where('date <=', $date_to);
            }

            if ($to_title !== null) {
                $this->db->like('to_title', $to_title);
            }

            if ($from_title !== null) {
                $this->db->like('from_title', $from_title);
            }

            if ($reference_no !== null) {
                $this->db->like('reference_no', $reference_no);
            }

            if ($search !== null) {
                $this->db->group_start();
                $this->db->like('to_title', $search);
                $this->db->or_like('from_title', $search);
                $this->db->or_like('reference_no', $search);
                $this->db->or_like('address', $search);
                $this->db->or_like('note', $search);
                $this->db->group_end();
            }

            // Get dispatch list
            $dispatch_list = $this->db->order_by('id', 'DESC')->get()->result_array();

            json_output(200, array(
                'status' => 1,
                'message' => 'Postal dispatches retrieved successfully',
                'total_records' => count($dispatch_list),
                'data' => $dispatch_list
            ));

        } catch (Exception $e) {
            log_message('error', 'Postal Dispatch API List Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Get a specific postal dispatch
     * 
     * Retrieves details of a specific postal dispatch by its ID.
     * 
     * @param int $id Postal dispatch ID
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
                    'message' => 'Invalid or missing postal dispatch ID',
                    'data' => null
                ));
                return;
            }

            $id = (int)$id;

            // Get dispatch details
            $dispatch = $this->dispatch_model->dis_rec_data($id, 'dispatch');
            
            if (empty($dispatch)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Postal dispatch not found',
                    'data' => null
                ));
                return;
            }

            json_output(200, array(
                'status' => 1,
                'message' => 'Postal dispatch retrieved successfully',
                'data' => $dispatch
            ));

        } catch (Exception $e) {
            log_message('error', 'Postal Dispatch API Get Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Create a new postal dispatch
     * 
     * Creates a new postal dispatch record.
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
            if (empty($input['to_title']) || trim($input['to_title']) === '') {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'To Title is required',
                    'data' => null
                ));
                return;
            }

            if (empty($input['date']) || trim($input['date']) === '') {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Date is required',
                    'data' => null
                ));
                return;
            }

            // Validate date format (YYYY-MM-DD)
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $input['date'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid date format. Use YYYY-MM-DD format',
                    'data' => null
                ));
                return;
            }

            // Prepare data for insertion
            $dispatch_data = array(
                'reference_no' => isset($input['reference_no']) ? $this->db->escape_str(trim($input['reference_no'])) : '',
                'to_title' => $this->db->escape_str(trim($input['to_title'])),
                'address' => isset($input['address']) ? $this->db->escape_str(trim($input['address'])) : '',
                'note' => isset($input['note']) ? $this->db->escape_str(trim($input['note'])) : '',
                'from_title' => isset($input['from_title']) ? $this->db->escape_str(trim($input['from_title'])) : '',
                'date' => $input['date'],
                'type' => 'dispatch',
                'image' => isset($input['image']) ? $this->db->escape_str(trim($input['image'])) : null
            );

            // Insert the dispatch
            $dispatch_id = $this->dispatch_model->insert('dispatch_receive', $dispatch_data);

            // Get the created dispatch
            $created_dispatch = $this->dispatch_model->dis_rec_data($dispatch_id, 'dispatch');

            json_output(201, array(
                'status' => 1,
                'message' => 'Postal dispatch created successfully',
                'data' => $created_dispatch
            ));

        } catch (Exception $e) {
            log_message('error', 'Postal Dispatch API Create Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Update a postal dispatch
     * 
     * Updates an existing postal dispatch record.
     * 
     * @param int $id Postal dispatch ID
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
                    'message' => 'Invalid or missing postal dispatch ID',
                    'data' => null
                ));
                return;
            }

            $id = (int)$id;

            // Check if dispatch exists
            $existing_dispatch = $this->dispatch_model->dis_rec_data($id, 'dispatch');
            if (empty($existing_dispatch)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Postal dispatch not found',
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
            if (empty($input['to_title']) || trim($input['to_title']) === '') {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'To Title is required',
                    'data' => null
                ));
                return;
            }

            if (empty($input['date']) || trim($input['date']) === '') {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Date is required',
                    'data' => null
                ));
                return;
            }

            // Validate date format (YYYY-MM-DD)
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $input['date'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid date format. Use YYYY-MM-DD format',
                    'data' => null
                ));
                return;
            }

            // Prepare data for update
            $dispatch_data = array(
                'reference_no' => isset($input['reference_no']) ? $this->db->escape_str(trim($input['reference_no'])) : '',
                'to_title' => $this->db->escape_str(trim($input['to_title'])),
                'address' => isset($input['address']) ? $this->db->escape_str(trim($input['address'])) : '',
                'note' => isset($input['note']) ? $this->db->escape_str(trim($input['note'])) : '',
                'from_title' => isset($input['from_title']) ? $this->db->escape_str(trim($input['from_title'])) : '',
                'date' => $input['date'],
                'type' => 'dispatch'
            );

            // Update image if provided
            if (isset($input['image']) && !empty($input['image'])) {
                $dispatch_data['image'] = $this->db->escape_str(trim($input['image']));
            } else {
                $dispatch_data['image'] = $existing_dispatch['image'];
            }

            // Update the dispatch
            $this->dispatch_model->update_dispatch('dispatch_receive', $id, 'dispatch', $dispatch_data);

            // Get the updated dispatch
            $updated_dispatch = $this->dispatch_model->dis_rec_data($id, 'dispatch');

            json_output(200, array(
                'status' => 1,
                'message' => 'Postal dispatch updated successfully',
                'data' => $updated_dispatch
            ));

        } catch (Exception $e) {
            log_message('error', 'Postal Dispatch API Update Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Delete a postal dispatch
     * 
     * Deletes a postal dispatch record.
     * 
     * @param int $id Postal dispatch ID
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
                    'message' => 'Invalid or missing postal dispatch ID',
                    'data' => null
                ));
                return;
            }

            $id = (int)$id;

            // Check if dispatch exists
            $existing_dispatch = $this->dispatch_model->dis_rec_data($id, 'dispatch');
            if (empty($existing_dispatch)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Postal dispatch not found',
                    'data' => null
                ));
                return;
            }

            // Delete the dispatch
            $this->dispatch_model->delete($id);

            json_output(200, array(
                'status' => 1,
                'message' => 'Postal dispatch deleted successfully',
                'data' => array('id' => $id)
            ));

        } catch (Exception $e) {
            log_message('error', 'Postal Dispatch API Delete Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }
}

