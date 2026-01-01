<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Alumni Report API Controller
 * 
 * This controller handles API requests for alumni student reports
 * showing students who have passed out from the school.
 * 
 * @package    CodeIgniter
 * @subpackage Controllers
 * @category   API
 */
class Alumni_report_api extends CI_Controller
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
     * POST /api/alumni-report/list
     */
    public function list()
    {
        try {
            // Get classes
            $classes_query = $this->db->select('id, class')->from('classes')->order_by('class', 'asc')->get();
            $classes = $classes_query->result_array();

            // Get sessions (pass-out years)
            $sessions_query = $this->db->select('id, session')->from('sessions')->order_by('session', 'desc')->get();
            $sessions = $sessions_query->result_array();

            // Get categories
            $categories_query = $this->db->select('id, category')->from('categories')->order_by('category', 'asc')->get();
            $categories = $categories_query->result_array();

            $response = array(
                'status' => 1,
                'message' => 'Filter options retrieved successfully',
                'data' => array(
                    'classes' => $classes,
                    'sessions' => $sessions,
                    'categories' => $categories
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
     * Filter endpoint - Returns alumni student data
     * 
     * POST /api/alumni-report/filter
     * 
     * Request body (all parameters optional):
     * {
     *   "class_id": 1,
     *   "section_id": 2,
     *   "session_id": 3,
     *   "category_id": 4,
     *   "admission_no": "ADM001"
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
            $session_id = isset($request_body['session_id']) && $request_body['session_id'] !== '' ? $request_body['session_id'] : null;
            $category_id = isset($request_body['category_id']) && $request_body['category_id'] !== '' ? $request_body['category_id'] : null;
            $admission_no = isset($request_body['admission_no']) && $request_body['admission_no'] !== '' ? $request_body['admission_no'] : null;

            // Get alumni student data
            $result = $this->get_alumni_students($class_id, $section_id, $session_id, $category_id, $admission_no);

            // Calculate summary
            $total_alumni = count($result);
            $classes_count = count(array_unique(array_column($result, 'class_id')));
            $sessions_count = count(array_unique(array_column($result, 'session_id')));
            
            // Group by session for pass-out year distribution
            $session_distribution = array();
            foreach ($result as $student) {
                $session = $student['session'];
                if (!isset($session_distribution[$session])) {
                    $session_distribution[$session] = 0;
                }
                $session_distribution[$session]++;
            }

            $response = array(
                'status' => 1,
                'message' => 'Alumni report data retrieved successfully',
                'filters_applied' => array(
                    'class_id' => $class_id,
                    'section_id' => $section_id,
                    'session_id' => $session_id,
                    'category_id' => $category_id,
                    'admission_no' => $admission_no
                ),
                'summary' => array(
                    'total_alumni' => $total_alumni,
                    'total_classes' => $classes_count,
                    'total_sessions' => $sessions_count,
                    'session_distribution' => $session_distribution
                ),
                'total_records' => $total_alumni,
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
                    'message' => 'Error retrieving alumni report data',
                    'error' => $e->getMessage()
                )));
        }
    }

    /**
     * Get alumni students using direct database queries
     * 
     * IMPORTANT: This query now matches the exact logic used by the web page
     * It starts from alumni_students table (if it has records) OR
     * it can work from student_session table with is_alumni flag
     */
    private function get_alumni_students($class_id, $section_id, $session_id, $category_id, $admission_no)
    {
        // First, check if alumni_students table has any records
        $alumni_table_count = $this->db->count_all('alumni_students');
        
        if ($alumni_table_count > 0) {
            // Use the web page approach: Start from alumni_students table
            $this->db->select('
                students.id,
                students.admission_no,
                students.roll_no,
                students.firstname,
                students.middlename,
                students.lastname,
                students.father_name,
                students.guardian_name,
                students.guardian_phone,
                students.mobileno,
                students.email,
                students.dob,
                students.gender,
                students.current_address,
                students.permanent_address,
                students.city,
                students.state,
                students.pincode,
                students.religion,
                students.admission_date,
                classes.id as class_id,
                classes.class,
                sections.id as section_id,
                sections.section,
                IFNULL(categories.id, 0) as category_id,
                IFNULL(categories.category, "") as category,
                student_session.session_id,
                sessions.session,
                IFNULL(alumni_students.current_email, "") as current_email,
                IFNULL(alumni_students.current_phone, "") as current_phone,
                IFNULL(alumni_students.occupation, "") as occupation,
                IFNULL(alumni_students.address, "") as current_address_alumni
            ');

            $this->db->from('alumni_students');
            $this->db->join('students', 'students.id = alumni_students.student_id');
            $this->db->join('student_session', 'student_session.student_id = students.id');
            $this->db->join('classes', 'student_session.class_id = classes.id');
            $this->db->join('sections', 'sections.id = student_session.section_id');
            $this->db->join('sessions', 'sessions.id = student_session.session_id');
            $this->db->join('categories', 'students.category_id = categories.id', 'left');
            
            // Apply is_alumni filter
            $this->db->where('student_session.is_alumni', 1);
            
        } else {
            // Fallback approach: Start from student_session with is_alumni flag
            $this->db->select('
                students.id,
                students.admission_no,
                students.roll_no,
                students.firstname,
                students.middlename,
                students.lastname,
                students.father_name,
                students.guardian_name,
                students.guardian_phone,
                students.mobileno,
                students.email,
                students.dob,
                students.gender,
                students.current_address,
                students.permanent_address,
                students.city,
                students.state,
                students.pincode,
                students.religion,
                students.admission_date,
                classes.id as class_id,
                classes.class,
                sections.id as section_id,
                sections.section,
                IFNULL(categories.id, 0) as category_id,
                IFNULL(categories.category, "") as category,
                student_session.session_id,
                sessions.session,
                "" as current_email,
                "" as current_phone,
                "" as occupation,
                "" as current_address_alumni
            ');

            $this->db->from('student_session');
            $this->db->join('students', 'students.id = student_session.student_id');
            $this->db->join('classes', 'student_session.class_id = classes.id');
            $this->db->join('sections', 'sections.id = student_session.section_id');
            $this->db->join('sessions', 'sessions.id = student_session.session_id');
            $this->db->join('categories', 'students.category_id = categories.id', 'left');
            
            // Must have is_alumni flag set
            $this->db->where('student_session.is_alumni', 1);
        }

        // Apply common filters
        if ($class_id !== null) {
            $this->db->where('student_session.class_id', $class_id);
        }

        if ($section_id !== null) {
            $this->db->where('student_session.section_id', $section_id);
        }

        if ($session_id !== null) {
            $this->db->where('student_session.session_id', $session_id);
        }

        if ($category_id !== null) {
            $this->db->where('students.category_id', $category_id);
        }

        if ($admission_no !== null) {
            $this->db->like('students.admission_no', $admission_no);
        }

        // Only active students
        $this->db->where('students.is_active', 'yes');

        // Group by student ID to avoid duplicates
        $this->db->group_by('students.id');

        // Order by admission number
        $this->db->order_by('students.admission_no', 'asc');

        $query = $this->db->get();
        $results = $query->result_array();

        // Format the results
        foreach ($results as &$row) {
            // Format student name
            $row['student_name'] = trim($row['firstname'] . ' ' . $row['middlename'] . ' ' . $row['lastname']);
            
            // Format class section
            $row['class_section'] = $row['class'] . ' - ' . $row['section'];
            
            // Format pass out year (session)
            $row['pass_out_year'] = $row['session'];
            
            // Format contact information
            $row['mobileno'] = $row['mobileno'] ? $row['mobileno'] : '';
            $row['email'] = $row['email'] ? $row['email'] : '';
            $row['current_email'] = $row['current_email'] ? $row['current_email'] : '';
            $row['current_phone'] = $row['current_phone'] ? $row['current_phone'] : '';
            $row['occupation'] = $row['occupation'] ? $row['occupation'] : '';
            
            // Format addresses
            $row['current_address'] = $row['current_address'] ? $row['current_address'] : '';
            $row['permanent_address'] = $row['permanent_address'] ? $row['permanent_address'] : '';
            $row['current_address_alumni'] = $row['current_address_alumni'] ? $row['current_address_alumni'] : '';
        }

        return $results;
    }
}

