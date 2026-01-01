<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Postal Receive API Controller
 * 
 * This controller provides RESTful API endpoints for managing postal receive records.
 * It handles CRUD operations for postal receives including document attachments.
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Webservices
 * @author     School Management System
 * @version    1.0.0
 */
class Postal_receive_api extends CI_Controller
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
     * List all postal receives
     * 
     * Retrieves a list of all postal receives with optional filtering.
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
            $this->db->where('type', 'receive');

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

            // Get receive list
            $receive_list = $this->db->order_by('id', 'DESC')->get()->result_array();

            json_output(200, array(
                'status' => 1,
                'message' => 'Postal receives retrieved successfully',
                'total_records' => count($receive_list),
                'data' => $receive_list
            ));

        } catch (Exception $e) {
            log_message('error', 'Postal Receive API List Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Get a specific postal receive
     * 
     * Retrieves details of a specific postal receive by its ID.
     * 
     * @param int $id Postal receive ID
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
                    'message' => 'Invalid or missing postal receive ID',
                    'data' => null
                ));
                return;
            }

            $id = (int)$id;

            // Get receive details
            $receive = $this->dispatch_model->dis_rec_data($id, 'receive');
            
            if (empty($receive)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Postal receive not found',
                    'data' => null
                ));
                return;
            }

            json_output(200, array(
                'status' => 1,
                'message' => 'Postal receive retrieved successfully',
                'data' => $receive
            ));

        } catch (Exception $e) {
            log_message('error', 'Postal Receive API Get Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Create a new postal receive
     * 
     * Creates a new postal receive record.
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
            if (empty($input['from_title']) || trim($input['from_title']) === '') {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'From Title is required',
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
            $receive_data = array(
                'reference_no' => isset($input['reference_no']) ? $this->db->escape_str(trim($input['reference_no'])) : '',
                'from_title' => $this->db->escape_str(trim($input['from_title'])),
                'address' => isset($input['address']) ? $this->db->escape_str(trim($input['address'])) : '',
                'note' => isset($input['note']) ? $this->db->escape_str(trim($input['note'])) : '',
                'to_title' => isset($input['to_title']) ? $this->db->escape_str(trim($input['to_title'])) : '',
                'date' => $input['date'],
                'type' => 'receive',
                'image' => isset($input['image']) ? $this->db->escape_str(trim($input['image'])) : null
            );

            // Insert the receive
            $receive_id = $this->dispatch_model->insert('dispatch_receive', $receive_data);

            // Get the created receive
            $created_receive = $this->dispatch_model->dis_rec_data($receive_id, 'receive');

            json_output(201, array(
                'status' => 1,
                'message' => 'Postal receive created successfully',
                'data' => $created_receive
            ));

        } catch (Exception $e) {
            log_message('error', 'Postal Receive API Create Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Update a postal receive
     * 
     * Updates an existing postal receive record.
     * 
     * @param int $id Postal receive ID
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
                    'message' => 'Invalid or missing postal receive ID',
                    'data' => null
                ));
                return;
            }

            $id = (int)$id;

            // Check if receive exists
            $existing_receive = $this->dispatch_model->dis_rec_data($id, 'receive');
            if (empty($existing_receive)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Postal receive not found',
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
            if (empty($input['from_title']) || trim($input['from_title']) === '') {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'From Title is required',
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
            $receive_data = array(
                'reference_no' => isset($input['reference_no']) ? $this->db->escape_str(trim($input['reference_no'])) : '',
                'from_title' => $this->db->escape_str(trim($input['from_title'])),
                'address' => isset($input['address']) ? $this->db->escape_str(trim($input['address'])) : '',
                'note' => isset($input['note']) ? $this->db->escape_str(trim($input['note'])) : '',
                'to_title' => isset($input['to_title']) ? $this->db->escape_str(trim($input['to_title'])) : '',
                'date' => $input['date'],
                'type' => 'receive'
            );

            // Update image if provided
            if (isset($input['image']) && !empty($input['image'])) {
                $receive_data['image'] = $this->db->escape_str(trim($input['image']));
            } else {
                $receive_data['image'] = $existing_receive['image'];
            }

            // Update the receive
            $this->dispatch_model->update_dispatch('dispatch_receive', $id, 'receive', $receive_data);

            // Get the updated receive
            $updated_receive = $this->dispatch_model->dis_rec_data($id, 'receive');

            json_output(200, array(
                'status' => 1,
                'message' => 'Postal receive updated successfully',
                'data' => $updated_receive
            ));

        } catch (Exception $e) {
            log_message('error', 'Postal Receive API Update Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Delete a postal receive
     * 
     * Deletes a postal receive record.
     * 
     * @param int $id Postal receive ID
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
                    'message' => 'Invalid or missing postal receive ID',
                    'data' => null
                ));
                return;
            }

            $id = (int)$id;

            // Check if receive exists
            $existing_receive = $this->dispatch_model->dis_rec_data($id, 'receive');
            if (empty($existing_receive)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Postal receive not found',
                    'data' => null
                ));
                return;
            }

            // Delete the receive
            $this->dispatch_model->delete($id);

            json_output(200, array(
                'status' => 1,
                'message' => 'Postal receive deleted successfully',
                'data' => array('id' => $id)
            ));

        } catch (Exception $e) {
            log_message('error', 'Postal Receive API Delete Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }
}
