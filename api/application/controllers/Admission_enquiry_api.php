<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Admission Enquiry API Controller
 * 
 * This controller provides RESTful API endpoints for managing admission enquiries.
 * It handles CRUD operations for admission enquiries and their follow-ups.
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Webservices
 * @author     School Management System
 * @version    1.0.0
 */
class Admission_enquiry_api extends CI_Controller
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
        $this->load->model('Enquiry_model', 'enquiry_model');
        $this->load->model('class_model');
        $this->load->model('staff_model');
        $this->load->model('setting_model');
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
     * List all admission enquiries
     * 
     * Retrieves a list of admission enquiries with optional filtering and pagination.
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
            $class_id = isset($input['class_id']) && !empty($input['class_id']) ? (int)$input['class_id'] : null;
            $source = isset($input['source']) && !empty($input['source']) ? $this->db->escape_str($input['source']) : null;
            $enquiry_from_date = isset($input['enquiry_from_date']) && !empty($input['enquiry_from_date']) ? $input['enquiry_from_date'] : null;
            $enquiry_to_date = isset($input['enquiry_to_date']) && !empty($input['enquiry_to_date']) ? $input['enquiry_to_date'] : null;
            $status = isset($input['status']) && !empty($input['status']) ? $this->db->escape_str($input['status']) : 'active';
            $search = isset($input['search']) && !empty($input['search']) ? $this->db->escape_str($input['search']) : null;

            // Build query
            $this->db->select('enquiry.*, classes.class as class_name, staff.name as assigned_staff_name, staff.surname as assigned_staff_surname, staff.employee_id as assigned_staff_employee_id');
            $this->db->from('enquiry');
            $this->db->join('classes', 'enquiry.class_id = classes.id', 'left');
            $this->db->join('staff', 'enquiry.assigned = staff.id', 'left');

            // Apply filters
            if ($class_id !== null) {
                $this->db->where('enquiry.class_id', $class_id);
            }

            if ($source !== null) {
                $this->db->where('enquiry.source', $source);
            }

            if ($enquiry_from_date !== null) {
                $this->db->where('enquiry.date >=', $enquiry_from_date);
            }

            if ($enquiry_to_date !== null) {
                $this->db->where('enquiry.date <=', $enquiry_to_date);
            }

            if ($status !== null) {
                $this->db->where('enquiry.status', $status);
            }

            if ($search !== null) {
                $this->db->group_start();
                $this->db->like('enquiry.name', $search);
                $this->db->or_like('enquiry.contact', $search);
                $this->db->or_like('enquiry.email', $search);
                $this->db->group_end();
            }

            // Get follow-up information
            $enquiry_list = $this->db->order_by('enquiry.date', 'DESC')->get()->result_array();

            // Add follow-up information to each enquiry
            foreach ($enquiry_list as $key => $enquiry) {
                $follow_up = $this->enquiry_model->getFollowByEnquiry($enquiry['id']);
                $enquiry_list[$key]['last_follow_up_date'] = isset($follow_up['date']) ? $follow_up['date'] : null;
                $enquiry_list[$key]['next_follow_up_date'] = isset($follow_up['next_date']) ? $follow_up['next_date'] : null;
                $enquiry_list[$key]['last_follow_up_response'] = isset($follow_up['response']) ? $follow_up['response'] : null;
                $enquiry_list[$key]['last_follow_up_note'] = isset($follow_up['note']) ? $follow_up['note'] : null;
            }

            json_output(200, array(
                'status' => 1,
                'message' => 'Admission enquiries retrieved successfully',
                'total_records' => count($enquiry_list),
                'data' => $enquiry_list
            ));

        } catch (Exception $e) {
            log_message('error', 'Admission Enquiry API List Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Get a specific admission enquiry
     * 
     * Retrieves details of a specific admission enquiry by its ID.
     * 
     * @param int $id Admission enquiry ID
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
                    'message' => 'Invalid or missing admission enquiry ID',
                    'data' => null
                ));
                return;
            }

            $id = (int)$id;

            // Get enquiry details
            $enquiry = $this->enquiry_model->getenquiry_list($id, 'active');
            
            if (empty($enquiry)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Admission enquiry not found',
                    'data' => null
                ));
                return;
            }

            // Get follow-up information
            $follow_up = $this->enquiry_model->getFollowByEnquiry($id);
            $enquiry['last_follow_up_date'] = isset($follow_up['date']) ? $follow_up['date'] : null;
            $enquiry['next_follow_up_date'] = isset($follow_up['next_date']) ? $follow_up['next_date'] : null;
            $enquiry['last_follow_up_response'] = isset($follow_up['response']) ? $follow_up['response'] : null;
            $enquiry['last_follow_up_note'] = isset($follow_up['note']) ? $follow_up['note'] : null;

            json_output(200, array(
                'status' => 1,
                'message' => 'Admission enquiry retrieved successfully',
                'data' => $enquiry
            ));

        } catch (Exception $e) {
            log_message('error', 'Admission Enquiry API Get Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Create a new admission enquiry
     * 
     * Creates a new admission enquiry record.
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
                    'message' => 'Name is required',
                    'data' => null
                ));
                return;
            }

            if (empty($input['contact']) || trim($input['contact']) === '') {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Contact/Phone is required',
                    'data' => null
                ));
                return;
            }

            if (empty($input['source']) || trim($input['source']) === '') {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Source is required',
                    'data' => null
                ));
                return;
            }

            if (empty($input['date']) || trim($input['date']) === '') {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Enquiry date is required',
                    'data' => null
                ));
                return;
            }

            if (empty($input['follow_up_date']) || trim($input['follow_up_date']) === '') {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Follow-up date is required',
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

            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $input['follow_up_date'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid follow-up date format. Use YYYY-MM-DD format',
                    'data' => null
                ));
                return;
            }

            // Validate class_id if provided
            if (isset($input['class_id']) && !empty($input['class_id'])) {
                $class_id = (int)$input['class_id'];
                $class_exists = $this->class_model->getAll($class_id);
                if (empty($class_exists)) {
                    json_output(404, array(
                        'status' => 0,
                        'message' => 'Class not found',
                        'data' => null
                    ));
                    return;
                }
            }

            // Validate assigned staff if provided
            if (isset($input['assigned']) && !empty($input['assigned'])) {
                $assigned = (int)$input['assigned'];
                $staff_exists = $this->staff_model->getAll($assigned);
                if (empty($staff_exists)) {
                    json_output(404, array(
                        'status' => 0,
                        'message' => 'Assigned staff not found',
                        'data' => null
                    ));
                    return;
                }
            }

            // Prepare data for insertion
            $enquiry_data = array(
                'name' => $this->db->escape_str(trim($input['name'])),
                'contact' => $this->db->escape_str(trim($input['contact'])),
                'address' => isset($input['address']) ? $this->db->escape_str(trim($input['address'])) : '',
                'reference' => isset($input['reference']) ? $this->db->escape_str(trim($input['reference'])) : '',
                'date' => $input['date'],
                'description' => isset($input['description']) ? $this->db->escape_str(trim($input['description'])) : '',
                'follow_up_date' => $input['follow_up_date'],
                'note' => isset($input['note']) ? $this->db->escape_str(trim($input['note'])) : '',
                'source' => $this->db->escape_str(trim($input['source'])),
                'email' => isset($input['email']) ? $this->db->escape_str(trim($input['email'])) : null,
                'assigned' => isset($input['assigned']) && !empty($input['assigned']) ? (int)$input['assigned'] : null,
                'class_id' => isset($input['class_id']) && !empty($input['class_id']) ? (int)$input['class_id'] : null,
                'no_of_child' => isset($input['no_of_child']) ? $this->db->escape_str(trim($input['no_of_child'])) : null,
                'status' => isset($input['status']) ? $this->db->escape_str(trim($input['status'])) : 'active',
                'created_by' => isset($input['created_by']) ? (int)$input['created_by'] : 1
            );

            // Insert the enquiry
            $this->enquiry_model->add($enquiry_data);
            $enquiry_id = $this->db->insert_id();

            // Get the created enquiry
            $created_enquiry = $this->enquiry_model->getenquiry_list($enquiry_id, 'active');

            json_output(201, array(
                'status' => 1,
                'message' => 'Admission enquiry created successfully',
                'data' => $created_enquiry
            ));

        } catch (Exception $e) {
            log_message('error', 'Admission Enquiry API Create Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Update an admission enquiry
     * 
     * Updates an existing admission enquiry record.
     * 
     * @param int $id Admission enquiry ID
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
                    'message' => 'Invalid or missing admission enquiry ID',
                    'data' => null
                ));
                return;
            }

            $id = (int)$id;

            // Check if enquiry exists
            $existing_enquiry = $this->enquiry_model->getenquiry_list($id, 'active');
            if (empty($existing_enquiry)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Admission enquiry not found',
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
                    'message' => 'Name is required',
                    'data' => null
                ));
                return;
            }

            if (empty($input['contact']) || trim($input['contact']) === '') {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Contact/Phone is required',
                    'data' => null
                ));
                return;
            }

            if (empty($input['source']) || trim($input['source']) === '') {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Source is required',
                    'data' => null
                ));
                return;
            }

            if (empty($input['date']) || trim($input['date']) === '') {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Enquiry date is required',
                    'data' => null
                ));
                return;
            }

            if (empty($input['follow_up_date']) || trim($input['follow_up_date']) === '') {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Follow-up date is required',
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

            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $input['follow_up_date'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid follow-up date format. Use YYYY-MM-DD format',
                    'data' => null
                ));
                return;
            }

            // Validate class_id if provided
            if (isset($input['class_id']) && !empty($input['class_id'])) {
                $class_id = (int)$input['class_id'];
                $class_exists = $this->class_model->getAll($class_id);
                if (empty($class_exists)) {
                    json_output(404, array(
                        'status' => 0,
                        'message' => 'Class not found',
                        'data' => null
                    ));
                    return;
                }
            }

            // Validate assigned staff if provided
            if (isset($input['assigned']) && !empty($input['assigned'])) {
                $assigned = (int)$input['assigned'];
                $staff_exists = $this->staff_model->getAll($assigned);
                if (empty($staff_exists)) {
                    json_output(404, array(
                        'status' => 0,
                        'message' => 'Assigned staff not found',
                        'data' => null
                    ));
                    return;
                }
            }

            // Prepare data for update
            $enquiry_data = array(
                'name' => $this->db->escape_str(trim($input['name'])),
                'contact' => $this->db->escape_str(trim($input['contact'])),
                'address' => isset($input['address']) ? $this->db->escape_str(trim($input['address'])) : '',
                'reference' => isset($input['reference']) ? $this->db->escape_str(trim($input['reference'])) : '',
                'date' => $input['date'],
                'description' => isset($input['description']) ? $this->db->escape_str(trim($input['description'])) : '',
                'follow_up_date' => $input['follow_up_date'],
                'note' => isset($input['note']) ? $this->db->escape_str(trim($input['note'])) : '',
                'source' => $this->db->escape_str(trim($input['source'])),
                'email' => isset($input['email']) ? $this->db->escape_str(trim($input['email'])) : null,
                'assigned' => isset($input['assigned']) && !empty($input['assigned']) ? (int)$input['assigned'] : null,
                'class_id' => isset($input['class_id']) && !empty($input['class_id']) ? (int)$input['class_id'] : null,
                'no_of_child' => isset($input['no_of_child']) ? $this->db->escape_str(trim($input['no_of_child'])) : null
            );

            // Update the enquiry
            $this->enquiry_model->enquiry_update($id, $enquiry_data);

            // Get the updated enquiry
            $updated_enquiry = $this->enquiry_model->getenquiry_list($id, 'active');

            json_output(200, array(
                'status' => 1,
                'message' => 'Admission enquiry updated successfully',
                'data' => $updated_enquiry
            ));

        } catch (Exception $e) {
            log_message('error', 'Admission Enquiry API Update Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Delete an admission enquiry
     * 
     * Deletes an admission enquiry record.
     * 
     * @param int $id Admission enquiry ID
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
                    'message' => 'Invalid or missing admission enquiry ID',
                    'data' => null
                ));
                return;
            }

            $id = (int)$id;

            // Check if enquiry exists
            $existing_enquiry = $this->enquiry_model->getenquiry_list($id, 'active');
            if (empty($existing_enquiry)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Admission enquiry not found',
                    'data' => null
                ));
                return;
            }

            // Delete the enquiry
            $this->enquiry_model->enquiry_delete($id);

            json_output(200, array(
                'status' => 1,
                'message' => 'Admission enquiry deleted successfully',
                'data' => array('id' => $id)
            ));

        } catch (Exception $e) {
            log_message('error', 'Admission Enquiry API Delete Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }
}


