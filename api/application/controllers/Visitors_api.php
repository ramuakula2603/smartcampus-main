<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Visitors API Controller
 * 
 * This controller provides RESTful API endpoints for managing visitor records.
 * It handles CRUD operations for visitors including staff and student visitors.
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Webservices
 * @author     School Management System
 * @version    1.0.0
 */
class Visitors_api extends CI_Controller
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
        $this->load->model('Visitors_model', 'visitors_model');
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
     * List all visitors
     * 
     * Retrieves a list of all visitors with optional filtering.
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
            $staff_id = isset($input['staff_id']) && !empty($input['staff_id']) ? (int)$input['staff_id'] : null;
            $student_session_id = isset($input['student_session_id']) && !empty($input['student_session_id']) ? (int)$input['student_session_id'] : null;
            $meeting_with = isset($input['meeting_with']) && !empty($input['meeting_with']) ? $this->db->escape_str($input['meeting_with']) : null;
            $date_from = isset($input['date_from']) && !empty($input['date_from']) ? $input['date_from'] : null;
            $date_to = isset($input['date_to']) && !empty($input['date_to']) ? $input['date_to'] : null;
            $search = isset($input['search']) && !empty($input['search']) ? $this->db->escape_str($input['search']) : null;

            // Build query
            $this->db->select('visitors_book.*,classes.class,sections.section,staff.name as staff_name,staff.surname as staff_surname,staff.employee_id as staff_employee_id,student_session.class_id,student_session.section_id,students.id as students_id,students.admission_no,students.firstname as student_firstname,students.middlename as student_middlename,students.lastname as student_lastname')
                ->from('visitors_book');

            // Apply filters
            if ($staff_id !== null) {
                $this->db->where('visitors_book.staff_id', $staff_id);
            }

            if ($student_session_id !== null) {
                $this->db->where('visitors_book.student_session_id', $student_session_id);
            }

            if ($meeting_with !== null) {
                $this->db->where('visitors_book.meeting_with', $meeting_with);
            }

            if ($date_from !== null) {
                $this->db->where('visitors_book.date >=', $date_from);
            }

            if ($date_to !== null) {
                $this->db->where('visitors_book.date <=', $date_to);
            }

            if ($search !== null) {
                $this->db->group_start();
                $this->db->like('visitors_book.name', $search);
                $this->db->or_like('visitors_book.contact', $search);
                $this->db->or_like('visitors_book.email', $search);
                $this->db->or_like('visitors_book.id_proof', $search);
                $this->db->group_end();
            }

            // Join tables
            $this->db->join('student_session', 'student_session.id=visitors_book.student_session_id', 'left');
            $this->db->join('students', 'students.id=student_session.student_id', 'left');
            $this->db->join('classes', 'student_session.class_id=classes.id', 'left');
            $this->db->join('sections', 'sections.id=student_session.section_id', 'left');
            $this->db->join('staff', 'staff.id=visitors_book.staff_id', 'left');

            // Get visitors list
            $visitor_list = $this->db->order_by('visitors_book.id', 'DESC')->get()->result_array();

            json_output(200, array(
                'status' => 1,
                'message' => 'Visitors retrieved successfully',
                'total_records' => count($visitor_list),
                'data' => $visitor_list
            ));

        } catch (Exception $e) {
            log_message('error', 'Visitors API List Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Get a specific visitor
     * 
     * Retrieves details of a specific visitor by its ID.
     * 
     * @param int $id Visitor ID
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
                    'message' => 'Invalid or missing visitor ID',
                    'data' => null
                ));
                return;
            }

            $id = (int)$id;

            // Get visitor details
            $visitor = $this->visitors_model->visitors_list($id);
            
            if (empty($visitor)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Visitor not found',
                    'data' => null
                ));
                return;
            }

            json_output(200, array(
                'status' => 1,
                'message' => 'Visitor retrieved successfully',
                'data' => $visitor
            ));

        } catch (Exception $e) {
            log_message('error', 'Visitors API Get Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Create a new visitor
     * 
     * Creates a new visitor record.
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
            if (empty($input['meeting_with']) || !in_array($input['meeting_with'], array('staff', 'student'))) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Meeting with is required and must be either "staff" or "student"',
                    'data' => null
                ));
                return;
            }

            if (empty($input['purpose']) || trim($input['purpose']) === '') {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Purpose is required',
                    'data' => null
                ));
                return;
            }

            if (empty($input['name']) || trim($input['name']) === '') {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Visitor name is required',
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

            if (empty($input['contact']) || trim($input['contact']) === '') {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Contact is required',
                    'data' => null
                ));
                return;
            }

            // Validate date format
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $input['date'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid date format. Use YYYY-MM-DD format',
                    'data' => null
                ));
                return;
            }

            $meeting_with = $input['meeting_with'];
            $staff_id = null;
            $student_session_id = null;

            // Validate meeting_with specific fields
            if ($meeting_with == 'staff') {
                if (empty($input['staff_id']) || !is_numeric($input['staff_id']) || $input['staff_id'] <= 0) {
                    json_output(400, array(
                        'status' => 0,
                        'message' => 'Staff ID is required when meeting_with is "staff"',
                        'data' => null
                    ));
                    return;
                }
                $staff_id = (int)$input['staff_id'];
                $staff_exists = $this->staff_model->getAll($staff_id);
                if (empty($staff_exists)) {
                    json_output(404, array(
                        'status' => 0,
                        'message' => 'Staff not found',
                        'data' => null
                    ));
                    return;
                }
            } else if ($meeting_with == 'student') {
                if (empty($input['student_session_id']) || !is_numeric($input['student_session_id']) || $input['student_session_id'] <= 0) {
                    json_output(400, array(
                        'status' => 0,
                        'message' => 'Student session ID is required when meeting_with is "student"',
                        'data' => null
                    ));
                    return;
                }
                $student_session_id = (int)$input['student_session_id'];
            }

            // Prepare data for insertion
            $visitor_data = array(
                'meeting_with' => $this->db->escape_str(trim($meeting_with)),
                'purpose' => $this->db->escape_str(trim($input['purpose'])),
                'name' => $this->db->escape_str(trim($input['name'])),
                'contact' => $this->db->escape_str(trim($input['contact'])),
                'email' => isset($input['email']) ? $this->db->escape_str(trim($input['email'])) : null,
                'id_proof' => isset($input['id_proof']) ? $this->db->escape_str(trim($input['id_proof'])) : '',
                'no_of_people' => isset($input['no_of_people']) ? (int)$input['no_of_people'] : 1,
                'date' => $input['date'],
                'in_time' => isset($input['in_time']) ? $this->db->escape_str(trim($input['in_time'])) : '',
                'out_time' => isset($input['out_time']) ? $this->db->escape_str(trim($input['out_time'])) : '',
                'note' => isset($input['note']) ? $this->db->escape_str(trim($input['note'])) : '',
                'source' => isset($input['source']) ? $this->db->escape_str(trim($input['source'])) : null,
                'staff_id' => $staff_id,
                'student_session_id' => $student_session_id,
                'image' => isset($input['image']) ? $this->db->escape_str(trim($input['image'])) : null
            );

            // Insert the visitor
            $visitor_id = $this->visitors_model->add($visitor_data);

            // Get the created visitor
            $created_visitor = $this->visitors_model->visitors_list($visitor_id);

            json_output(201, array(
                'status' => 1,
                'message' => 'Visitor created successfully',
                'data' => $created_visitor
            ));

        } catch (Exception $e) {
            log_message('error', 'Visitors API Create Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Update a visitor
     * 
     * Updates an existing visitor record.
     * 
     * @param int $id Visitor ID
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
                    'message' => 'Invalid or missing visitor ID',
                    'data' => null
                ));
                return;
            }

            $id = (int)$id;

            // Check if visitor exists
            $existing_visitor = $this->visitors_model->visitors_list($id);
            if (empty($existing_visitor)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Visitor not found',
                    'data' => null
                ));
                return;
            }

            // Get input data
            $input = json_decode($this->input->raw_input_stream, true);

            // Validate required fields
            if (empty($input['meeting_with']) || !in_array($input['meeting_with'], array('staff', 'student'))) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Meeting with is required and must be either "staff" or "student"',
                    'data' => null
                ));
                return;
            }

            if (empty($input['purpose']) || trim($input['purpose']) === '') {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Purpose is required',
                    'data' => null
                ));
                return;
            }

            if (empty($input['name']) || trim($input['name']) === '') {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Visitor name is required',
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

            if (empty($input['contact']) || trim($input['contact']) === '') {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Contact is required',
                    'data' => null
                ));
                return;
            }

            // Validate date format
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $input['date'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid date format. Use YYYY-MM-DD format',
                    'data' => null
                ));
                return;
            }

            $meeting_with = $input['meeting_with'];
            $staff_id = null;
            $student_session_id = null;

            // Validate meeting_with specific fields
            if ($meeting_with == 'staff') {
                if (empty($input['staff_id']) || !is_numeric($input['staff_id']) || $input['staff_id'] <= 0) {
                    json_output(400, array(
                        'status' => 0,
                        'message' => 'Staff ID is required when meeting_with is "staff"',
                        'data' => null
                    ));
                    return;
                }
                $staff_id = (int)$input['staff_id'];
                $staff_exists = $this->staff_model->getAll($staff_id);
                if (empty($staff_exists)) {
                    json_output(404, array(
                        'status' => 0,
                        'message' => 'Staff not found',
                        'data' => null
                    ));
                    return;
                }
            } else if ($meeting_with == 'student') {
                if (empty($input['student_session_id']) || !is_numeric($input['student_session_id']) || $input['student_session_id'] <= 0) {
                    json_output(400, array(
                        'status' => 0,
                        'message' => 'Student session ID is required when meeting_with is "student"',
                        'data' => null
                    ));
                    return;
                }
                $student_session_id = (int)$input['student_session_id'];
            }

            // Prepare data for update
            $visitor_data = array(
                'meeting_with' => $this->db->escape_str(trim($meeting_with)),
                'purpose' => $this->db->escape_str(trim($input['purpose'])),
                'name' => $this->db->escape_str(trim($input['name'])),
                'contact' => $this->db->escape_str(trim($input['contact'])),
                'email' => isset($input['email']) ? $this->db->escape_str(trim($input['email'])) : null,
                'id_proof' => isset($input['id_proof']) ? $this->db->escape_str(trim($input['id_proof'])) : '',
                'no_of_people' => isset($input['no_of_people']) ? (int)$input['no_of_people'] : 1,
                'date' => $input['date'],
                'in_time' => isset($input['in_time']) ? $this->db->escape_str(trim($input['in_time'])) : '',
                'out_time' => isset($input['out_time']) ? $this->db->escape_str(trim($input['out_time'])) : '',
                'note' => isset($input['note']) ? $this->db->escape_str(trim($input['note'])) : '',
                'source' => isset($input['source']) ? $this->db->escape_str(trim($input['source'])) : null,
                'staff_id' => $staff_id,
                'student_session_id' => $student_session_id
            );

            // Update image if provided
            if (isset($input['image']) && !empty($input['image'])) {
                $visitor_data['image'] = $this->db->escape_str(trim($input['image']));
            }

            // Update the visitor
            $this->visitors_model->update($id, $visitor_data);

            // Get the updated visitor
            $updated_visitor = $this->visitors_model->visitors_list($id);

            json_output(200, array(
                'status' => 1,
                'message' => 'Visitor updated successfully',
                'data' => $updated_visitor
            ));

        } catch (Exception $e) {
            log_message('error', 'Visitors API Update Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Delete a visitor
     * 
     * Deletes a visitor record.
     * 
     * @param int $id Visitor ID
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
                    'message' => 'Invalid or missing visitor ID',
                    'data' => null
                ));
                return;
            }

            $id = (int)$id;

            // Check if visitor exists
            $existing_visitor = $this->visitors_model->visitors_list($id);
            if (empty($existing_visitor)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Visitor not found',
                    'data' => null
                ));
                return;
            }

            // Delete the visitor
            $this->visitors_model->delete($id);

            json_output(200, array(
                'status' => 1,
                'message' => 'Visitor deleted successfully',
                'data' => array('id' => $id)
            ));

        } catch (Exception $e) {
            log_message('error', 'Visitors API Delete Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }
}

