<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Complaint API Controller
 * 
 * This controller provides RESTful API endpoints for managing complaint records.
 * It handles CRUD operations for complaints including document attachments.
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Webservices
 * @author     School Management System
 * @version    1.0.0
 */
class Complaint_api extends CI_Controller
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
        $this->load->model('Complaint_model', 'complaint_model');
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
     * List all complaints
     * 
     * Retrieves a list of all complaints with optional filtering and search capabilities.
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
            $complaint_type = isset($input['complaint_type']) && !empty($input['complaint_type']) ? $this->db->escape_str($input['complaint_type']) : null;
            $source = isset($input['source']) && !empty($input['source']) ? $this->db->escape_str($input['source']) : null;
            $name = isset($input['name']) && !empty($input['name']) ? $this->db->escape_str($input['name']) : null;
            $contact = isset($input['contact']) && !empty($input['contact']) ? $this->db->escape_str($input['contact']) : null;

            // Build query
            $this->db->select('*');
            $this->db->from('complaint');

            // Apply filters
            if ($date_from !== null) {
                $this->db->where('date >=', $date_from);
            }

            if ($date_to !== null) {
                $this->db->where('date <=', $date_to);
            }

            if ($complaint_type !== null) {
                $this->db->like('complaint_type', $complaint_type);
            }

            if ($source !== null) {
                $this->db->like('source', $source);
            }

            if ($name !== null) {
                $this->db->like('name', $name);
            }

            if ($contact !== null) {
                $this->db->like('contact', $contact);
            }

            if ($search !== null) {
                $this->db->group_start();
                $this->db->like('complaint_type', $search);
                $this->db->or_like('source', $search);
                $this->db->or_like('name', $search);
                $this->db->or_like('contact', $search);
                $this->db->or_like('description', $search);
                $this->db->or_like('action_taken', $search);
                $this->db->or_like('assigned', $search);
                $this->db->or_like('note', $search);
                $this->db->group_end();
            }

            // Get complaint list
            $complaint_list = $this->db->order_by('id', 'DESC')->get()->result_array();

            json_output(200, array(
                'status' => 1,
                'message' => 'Complaints retrieved successfully',
                'total_records' => count($complaint_list),
                'data' => $complaint_list
            ));

        } catch (Exception $e) {
            log_message('error', 'Complaint API List Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Get a specific complaint
     * 
     * Retrieves details of a specific complaint by its ID.
     * 
     * @param int $id Complaint ID
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
                    'message' => 'Invalid or missing complaint ID',
                    'data' => null
                ));
                return;
            }

            $id = (int)$id;

            // Get complaint details
            $complaint = $this->complaint_model->complaint_list($id);
            
            if (empty($complaint)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Complaint not found',
                    'data' => null
                ));
                return;
            }

            json_output(200, array(
                'status' => 1,
                'message' => 'Complaint retrieved successfully',
                'data' => $complaint
            ));

        } catch (Exception $e) {
            log_message('error', 'Complaint API Get Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Create a new complaint
     * 
     * Creates a new complaint record.
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
            if (empty($input['name']) || trim($input['name']) === '') {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Name (Complain By) is required',
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
            $complaint_data = array(
                'complaint_type' => isset($input['complaint_type']) ? $this->db->escape_str(trim($input['complaint_type'])) : '',
                'source' => isset($input['source']) ? $this->db->escape_str(trim($input['source'])) : '',
                'name' => $this->db->escape_str(trim($input['name'])),
                'contact' => isset($input['contact']) ? $this->db->escape_str(trim($input['contact'])) : '',
                'date' => $input['date'],
                'description' => isset($input['description']) ? $this->db->escape_str(trim($input['description'])) : '',
                'action_taken' => isset($input['action_taken']) ? $this->db->escape_str(trim($input['action_taken'])) : '',
                'assigned' => isset($input['assigned']) ? $this->db->escape_str(trim($input['assigned'])) : '',
                'note' => isset($input['note']) ? $this->db->escape_str(trim($input['note'])) : '',
                'image' => isset($input['image']) ? $this->db->escape_str(trim($input['image'])) : null
            );

            // Insert the complaint
            $complaint_id = $this->complaint_model->add($complaint_data);

            // Get the created complaint
            $created_complaint = $this->complaint_model->complaint_list($complaint_id);

            json_output(201, array(
                'status' => 1,
                'message' => 'Complaint created successfully',
                'data' => $created_complaint
            ));

        } catch (Exception $e) {
            log_message('error', 'Complaint API Create Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Update a complaint
     * 
     * Updates an existing complaint record.
     * 
     * @param int $id Complaint ID
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
                    'message' => 'Invalid or missing complaint ID',
                    'data' => null
                ));
                return;
            }

            $id = (int)$id;

            // Check if complaint exists
            $existing_complaint = $this->complaint_model->complaint_list($id);
            if (empty($existing_complaint)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Complaint not found',
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
            if (empty($input['name']) || trim($input['name']) === '') {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Name (Complain By) is required',
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
            $complaint_data = array(
                'complaint_type' => isset($input['complaint_type']) ? $this->db->escape_str(trim($input['complaint_type'])) : '',
                'source' => isset($input['source']) ? $this->db->escape_str(trim($input['source'])) : '',
                'name' => $this->db->escape_str(trim($input['name'])),
                'contact' => isset($input['contact']) ? $this->db->escape_str(trim($input['contact'])) : '',
                'date' => $input['date'],
                'description' => isset($input['description']) ? $this->db->escape_str(trim($input['description'])) : '',
                'action_taken' => isset($input['action_taken']) ? $this->db->escape_str(trim($input['action_taken'])) : '',
                'assigned' => isset($input['assigned']) ? $this->db->escape_str(trim($input['assigned'])) : '',
                'note' => isset($input['note']) ? $this->db->escape_str(trim($input['note'])) : ''
            );

            // Update image if provided
            if (isset($input['image']) && !empty($input['image'])) {
                $complaint_data['image'] = $this->db->escape_str(trim($input['image']));
            } else {
                $complaint_data['image'] = $existing_complaint['image'];
            }

            // Update the complaint
            $this->complaint_model->update($id, $complaint_data);

            // Get the updated complaint
            $updated_complaint = $this->complaint_model->complaint_list($id);

            json_output(200, array(
                'status' => 1,
                'message' => 'Complaint updated successfully',
                'data' => $updated_complaint
            ));

        } catch (Exception $e) {
            log_message('error', 'Complaint API Update Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Delete a complaint
     * 
     * Deletes a complaint record.
     * 
     * @param int $id Complaint ID
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
                    'message' => 'Invalid or missing complaint ID',
                    'data' => null
                ));
                return;
            }

            $id = (int)$id;

            // Check if complaint exists
            $existing_complaint = $this->complaint_model->complaint_list($id);
            if (empty($existing_complaint)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Complaint not found',
                    'data' => null
                ));
                return;
            }

            // Delete the complaint
            $this->complaint_model->delete($id);

            json_output(200, array(
                'status' => 1,
                'message' => 'Complaint deleted successfully',
                'data' => array('id' => $id)
            ));

        } catch (Exception $e) {
            log_message('error', 'Complaint API Delete Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Get complaint types
     * 
     * Retrieves a list of all available complaint types.
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

            // Get complaint types
            $types = $this->complaint_model->getComplaintType();

            json_output(200, array(
                'status' => 1,
                'message' => 'Complaint types retrieved successfully',
                'total_records' => count($types),
                'data' => $types
            ));

        } catch (Exception $e) {
            log_message('error', 'Complaint API Types Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Get complaint sources
     * 
     * Retrieves a list of all available complaint sources.
     * 
     * @return void Outputs JSON response
     */
    public function sources()
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

            // Get complaint sources
            $sources = $this->complaint_model->getComplaintSource();

            json_output(200, array(
                'status' => 1,
                'message' => 'Complaint sources retrieved successfully',
                'total_records' => count($sources),
                'data' => $sources
            ));

        } catch (Exception $e) {
            log_message('error', 'Complaint API Sources Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }
}

