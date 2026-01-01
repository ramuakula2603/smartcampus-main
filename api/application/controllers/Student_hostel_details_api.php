<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Student Hostel Details API Controller
 * 
 * This controller handles API requests for student hostel details reports
 * showing students assigned to hostels, rooms, and room types.
 * 
 * @package    CodeIgniter
 * @subpackage Controllers
 * @category   API
 */
class Student_hostel_details_api extends CI_Controller
{
    private $current_session;

    public function __construct()
    {
        parent::__construct();
        
        // Suppress PHP 8.2 deprecation warnings
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
        
        // Database connection error handling
        try {
            // Load required models and libraries
            $this->load->model('setting_model');
            $this->load->model('auth_model');
            $this->load->database();
            $this->load->library('session');
            $this->load->helper('url');
            
            // Check database connection
            if (!$this->db->conn_id) {
                throw new Exception('Database connection failed');
            }
            
            // Get current session
            $this->current_session = $this->setting_model->getCurrentSession();
            
        } catch (Exception $e) {
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'status' => 0,
                    'message' => 'Database connection error. Please ensure MySQL is running in XAMPP.',
                    'error' => $e->getMessage()
                )));
            return;
        }
        
        // Authenticate API request
        $client_service = $this->input->get_request_header('Client-Service', TRUE);
        $auth_key = $this->input->get_request_header('Auth-Key', TRUE);
        
        if ($client_service !== 'smartschool' || $auth_key !== 'schoolAdmin@') {
            $this->output
                ->set_status_header(401)
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'status' => 0,
                    'message' => 'Unauthorized access'
                )));
            return;
        }
    }

    /**
     * List endpoint - Returns filter options
     * 
     * POST /api/student-hostel-details/list
     */
    public function list()
    {
        try {
            // Get classes
            $classes_query = $this->db->select('id, class')->from('classes')->order_by('class', 'asc')->get();
            $classes = $classes_query->result_array();

            // Get hostels
            $hostels_query = $this->db->select('id, hostel_name, type, address, intake')->from('hostel')->order_by('hostel_name', 'asc')->get();
            $hostels = $hostels_query->result_array();

            // Get room types
            $room_types_query = $this->db->select('id, room_type, description')->from('room_types')->order_by('room_type', 'asc')->get();
            $room_types = $room_types_query->result_array();

            $response = array(
                'status' => 1,
                'message' => 'Filter options retrieved successfully',
                'data' => array(
                    'classes' => $classes,
                    'hostels' => $hostels,
                    'room_types' => $room_types
                ),
                'timestamp' => date('Y-m-d H:i:s')
            );

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'status' => 0,
                    'message' => 'Error retrieving filter options',
                    'error' => $e->getMessage()
                )));
        }
    }

    /**
     * Filter endpoint - Returns student hostel details data
     * 
     * POST /api/student-hostel-details/filter
     * 
     * Request body (all parameters optional):
     * {
     *   "class_id": 1,
     *   "section_id": 2,
     *   "hostel_id": 3,
     *   "hostel_name": "Boys Hostel",
     *   "room_type_id": 4,
     *   "room_no": "101"
     * }
     */
    public function filter()
    {
        try {
            // Check request method
            if ($this->input->method() !== 'post') {
                $this->output
                    ->set_status_header(400)
                    ->set_content_type('application/json')
                    ->set_output(json_encode(array(
                        'status' => 0,
                        'message' => 'Bad request. Only POST method allowed.'
                    )));
                return;
            }

            // Get request body
            $request_body = json_decode($this->input->raw_input_stream, true);
            if ($request_body === null) {
                $request_body = array();
            }

            // Extract parameters (all optional)
            $class_id = isset($request_body['class_id']) && $request_body['class_id'] !== '' ? $request_body['class_id'] : null;
            $section_id = isset($request_body['section_id']) && $request_body['section_id'] !== '' ? $request_body['section_id'] : null;
            $hostel_id = isset($request_body['hostel_id']) && $request_body['hostel_id'] !== '' ? $request_body['hostel_id'] : null;
            $hostel_name = isset($request_body['hostel_name']) && $request_body['hostel_name'] !== '' ? $request_body['hostel_name'] : null;
            $room_type_id = isset($request_body['room_type_id']) && $request_body['room_type_id'] !== '' ? $request_body['room_type_id'] : null;
            $room_no = isset($request_body['room_no']) && $request_body['room_no'] !== '' ? $request_body['room_no'] : null;

            // Get student hostel details
            $result = $this->get_student_hostel_details($class_id, $section_id, $hostel_id, $hostel_name, $room_type_id, $room_no);

            // Calculate summary
            $total_students = count($result);
            $hostels_count = count(array_unique(array_column($result, 'hostel_name')));
            $rooms_count = count(array_unique(array_column($result, 'room_no')));
            $total_cost = array_sum(array_column($result, 'cost_per_bed'));

            $response = array(
                'status' => 1,
                'message' => 'Student hostel details retrieved successfully',
                'filters_applied' => array(
                    'class_id' => $class_id,
                    'section_id' => $section_id,
                    'hostel_id' => $hostel_id,
                    'hostel_name' => $hostel_name,
                    'room_type_id' => $room_type_id,
                    'room_no' => $room_no
                ),
                'summary' => array(
                    'total_students' => $total_students,
                    'total_hostels' => $hostels_count,
                    'total_rooms' => $rooms_count,
                    'total_hostel_cost' => number_format($total_cost, 2, '.', '')
                ),
                'total_records' => $total_students,
                'data' => $result,
                'timestamp' => date('Y-m-d H:i:s')
            );

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'status' => 0,
                    'message' => 'Error retrieving student hostel details',
                    'error' => $e->getMessage()
                )));
        }
    }

    /**
     * Get student hostel details using direct database queries
     */
    private function get_student_hostel_details($class_id, $section_id, $hostel_id, $hostel_name, $room_type_id, $room_no)
    {
        // Build the query
        $this->db->select('
            students.id,
            students.firstname,
            students.middlename,
            students.lastname,
            students.admission_no,
            students.mobileno,
            students.guardian_phone,
            students.hostel_room_id,
            classes.class,
            sections.section,
            hostel_rooms.id as room_id,
            hostel_rooms.room_no,
            hostel_rooms.no_of_bed,
            hostel_rooms.cost_per_bed,
            hostel_rooms.title as room_title,
            hostel_rooms.description as room_description,
            hostel.id as hostel_id,
            hostel.hostel_name,
            hostel.type as hostel_type,
            hostel.address as hostel_address,
            room_types.id as room_type_id,
            room_types.room_type,
            room_types.description as room_type_description
        ');
        
        $this->db->from('students');
        $this->db->join('student_session', 'students.id = student_session.student_id');
        $this->db->join('sections', 'sections.id = student_session.section_id');
        $this->db->join('classes', 'classes.id = student_session.class_id');
        $this->db->join('hostel_rooms', 'hostel_rooms.id = students.hostel_room_id');
        $this->db->join('hostel', 'hostel.id = hostel_rooms.hostel_id');
        $this->db->join('room_types', 'room_types.id = hostel_rooms.room_type_id');

        // Apply filters
        if ($class_id !== null) {
            $this->db->where('student_session.class_id', $class_id);
        }

        if ($section_id !== null) {
            $this->db->where('student_session.section_id', $section_id);
        }

        if ($hostel_id !== null) {
            $this->db->where('hostel.id', $hostel_id);
        }

        if ($hostel_name !== null) {
            $this->db->where('hostel.hostel_name', $hostel_name);
        }

        if ($room_type_id !== null) {
            $this->db->where('room_types.id', $room_type_id);
        }

        if ($room_no !== null) {
            $this->db->where('hostel_rooms.room_no', $room_no);
        }

        // Only active students in current session
        $this->db->where('students.is_active', 'yes');
        $this->db->where('student_session.session_id', $this->current_session);

        // Order by class, section, and student name
        $this->db->order_by('classes.class', 'asc');
        $this->db->order_by('sections.section', 'asc');
        $this->db->order_by('students.firstname', 'asc');

        $query = $this->db->get();
        $results = $query->result_array();

        // Format the results
        foreach ($results as &$row) {
            // Format student name
            $row['student_name'] = trim($row['firstname'] . ' ' . $row['middlename'] . ' ' . $row['lastname']);
            
            // Format class section
            $row['class_section'] = $row['class'] . ' - ' . $row['section'];
            
            // Format cost
            $row['cost_per_bed'] = floatval($row['cost_per_bed']);
            
            // Format number of beds
            $row['no_of_bed'] = intval($row['no_of_bed']);
            
            // Format contact numbers
            $row['mobileno'] = $row['mobileno'] ? $row['mobileno'] : '';
            $row['guardian_phone'] = $row['guardian_phone'] ? $row['guardian_phone'] : '';
        }

        return $results;
    }
}

