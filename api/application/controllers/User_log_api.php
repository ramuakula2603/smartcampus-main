<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * User Log API Controller
 * 
 * This controller handles API requests for user login logs
 * showing login activities of students, parents, and staff.
 * 
 * @package    CodeIgniter
 * @subpackage Controllers
 * @category   API
 */
class User_log_api extends CI_Controller
{
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
     * POST /api/user-log/list
     */
    public function list()
    {
        try {
            // Get unique roles
            $roles_query = $this->db->query("SELECT DISTINCT role FROM userlog WHERE role IS NOT NULL AND role != '' ORDER BY role ASC");
            $roles = array_column($roles_query->result_array(), 'role');

            // Get classes with sections
            $classes_query = $this->db->query("SELECT id, class FROM classes ORDER BY class ASC");
            $classes = $classes_query->result_array();

            $response = array(
                'status' => 1,
                'message' => 'Filter options retrieved successfully',
                'data' => array(
                    'roles' => $roles,
                    'classes' => $classes
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
     * Filter endpoint - Returns user log data
     * 
     * POST /api/user-log/filter
     * 
     * Request body (all parameters optional):
     * {
     *   "role": "student",
     *   "class_id": 1,
     *   "section_id": 2,
     *   "from_date": "2025-01-01",
     *   "to_date": "2025-12-31",
     *   "ip_address": "192.168.1.1",
     *   "user": "john",
     *   "limit": 100
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
            $role = isset($request_body['role']) && $request_body['role'] !== '' ? $request_body['role'] : null;
            $class_id = isset($request_body['class_id']) && $request_body['class_id'] !== '' ? $request_body['class_id'] : null;
            $section_id = isset($request_body['section_id']) && $request_body['section_id'] !== '' ? $request_body['section_id'] : null;
            $from_date = isset($request_body['from_date']) && $request_body['from_date'] !== '' ? $request_body['from_date'] : null;
            $to_date = isset($request_body['to_date']) && $request_body['to_date'] !== '' ? $request_body['to_date'] : null;
            $ip_address = isset($request_body['ip_address']) && $request_body['ip_address'] !== '' ? $request_body['ip_address'] : null;
            $user = isset($request_body['user']) && $request_body['user'] !== '' ? $request_body['user'] : null;
            $limit = isset($request_body['limit']) && $request_body['limit'] !== '' ? intval($request_body['limit']) : 100;

            // Get user log data
            $result = $this->get_user_logs($role, $class_id, $section_id, $from_date, $to_date, $ip_address, $user, $limit);

            // Calculate summary
            $total_logs = count($result);
            $roles_count = count(array_unique(array_column($result, 'role')));
            $users_count = count(array_unique(array_column($result, 'user')));
            
            // Group by role for distribution
            $role_distribution = array();
            foreach ($result as $log) {
                $role_name = $log['role'] ? ucfirst($log['role']) : 'Unknown';
                if (!isset($role_distribution[$role_name])) {
                    $role_distribution[$role_name] = 0;
                }
                $role_distribution[$role_name]++;
            }

            $response = array(
                'status' => 1,
                'message' => 'User log data retrieved successfully',
                'filters_applied' => array(
                    'role' => $role,
                    'class_id' => $class_id,
                    'section_id' => $section_id,
                    'from_date' => $from_date,
                    'to_date' => $to_date,
                    'ip_address' => $ip_address,
                    'user' => $user,
                    'limit' => $limit
                ),
                'summary' => array(
                    'total_logs' => $total_logs,
                    'total_roles' => $roles_count,
                    'total_users' => $users_count,
                    'role_distribution' => $role_distribution
                ),
                'total_records' => $total_logs,
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
                    'message' => 'Error retrieving user log data',
                    'error' => $e->getMessage()
                )));
        }
    }

    /**
     * Get user logs using direct database queries
     */
    private function get_user_logs($role, $class_id, $section_id, $from_date, $to_date, $ip_address, $user, $limit)
    {
        // Build the query
        $this->db->select('
            userlog.id,
            userlog.user,
            userlog.role,
            userlog.class_section_id,
            userlog.ipaddress,
            userlog.user_agent,
            userlog.login_datetime,
            IFNULL(classes.id, 0) as class_id,
            IFNULL(classes.class, "") as class_name,
            IFNULL(sections.id, 0) as section_id,
            IFNULL(sections.section, "") as section_name
        ');
        
        $this->db->from('userlog');
        $this->db->join('class_sections', 'class_sections.id = userlog.class_section_id', 'left');
        $this->db->join('classes', 'classes.id = class_sections.class_id', 'left');
        $this->db->join('sections', 'sections.id = class_sections.section_id', 'left');

        // Apply filters
        if ($role !== null) {
            $this->db->where('userlog.role', $role);
        }

        if ($class_id !== null) {
            $this->db->where('classes.id', $class_id);
        }

        if ($section_id !== null) {
            $this->db->where('sections.id', $section_id);
        }

        if ($ip_address !== null) {
            $this->db->like('userlog.ipaddress', $ip_address);
        }

        if ($user !== null) {
            $this->db->like('userlog.user', $user);
        }

        if ($from_date !== null) {
            $this->db->where('DATE(userlog.login_datetime) >=', $from_date);
        }

        if ($to_date !== null) {
            $this->db->where('DATE(userlog.login_datetime) <=', $to_date);
        }

        // Order by most recent first
        $this->db->order_by('userlog.login_datetime', 'desc');

        // Apply limit
        $this->db->limit($limit);

        $query = $this->db->get();
        $results = $query->result_array();

        // Format the results
        foreach ($results as &$row) {
            // Format timestamp
            $row['formatted_datetime'] = date('Y-m-d H:i:s', strtotime($row['login_datetime']));
            $row['formatted_date'] = date('Y-m-d', strtotime($row['login_datetime']));
            $row['formatted_time'] = date('H:i:s', strtotime($row['login_datetime']));
            
            // Format class section
            if ($row['class_name'] && $row['section_name']) {
                $row['class_section'] = $row['class_name'] . ' (' . $row['section_name'] . ')';
            } else {
                $row['class_section'] = '';
            }
            
            // Format role
            $row['role_formatted'] = $row['role'] ? ucfirst($row['role']) : '';
            
            // Ensure fields are not null
            $row['user'] = $row['user'] ? $row['user'] : '';
            $row['role'] = $row['role'] ? $row['role'] : '';
            $row['ipaddress'] = $row['ipaddress'] ? $row['ipaddress'] : '';
            $row['user_agent'] = $row['user_agent'] ? $row['user_agent'] : '';
        }

        return $results;
    }
}

