<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Audit Log API Controller
 * 
 * This controller handles API requests for audit trail logs
 * showing system actions performed by staff members.
 * 
 * @package    CodeIgniter
 * @subpackage Controllers
 * @category   API
 */
class Audit_log_api extends CI_Controller
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
     * POST /api/audit-log/list
     */
    public function list()
    {
        try {
            // Get unique actions
            $actions_query = $this->db->query("SELECT DISTINCT action FROM logs WHERE action IS NOT NULL AND action != '' ORDER BY action ASC");
            $actions = array_column($actions_query->result_array(), 'action');

            // Get unique platforms
            $platforms_query = $this->db->query("SELECT DISTINCT platform FROM logs WHERE platform IS NOT NULL AND platform != '' ORDER BY platform ASC");
            $platforms = array_column($platforms_query->result_array(), 'platform');

            // Get staff users who have audit logs
            $users_query = $this->db->query("
                SELECT DISTINCT staff.id, staff.name, staff.surname, staff.employee_id
                FROM logs
                JOIN staff ON staff.id = logs.user_id
                ORDER BY staff.name ASC
            ");
            $users_raw = $users_query->result_array();

            // Format user names
            $users = array();
            foreach ($users_raw as $user) {
                $users[] = array(
                    'id' => $user['id'],
                    'staff_name' => trim($user['name'] . ' ' . $user['surname']) . ' (' . $user['employee_id'] . ')'
                );
            }

            $response = array(
                'status' => 1,
                'message' => 'Filter options retrieved successfully',
                'data' => array(
                    'actions' => $actions,
                    'platforms' => $platforms,
                    'users' => $users
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
     * Filter endpoint - Returns audit log data
     * 
     * POST /api/audit-log/filter
     * 
     * Request body (all parameters optional):
     * {
     *   "user_id": 1,
     *   "action": "login",
     *   "platform": "Windows",
     *   "from_date": "2025-01-01",
     *   "to_date": "2025-12-31",
     *   "ip_address": "192.168.1.1",
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
            $user_id = isset($request_body['user_id']) && $request_body['user_id'] !== '' ? $request_body['user_id'] : null;
            $action = isset($request_body['action']) && $request_body['action'] !== '' ? $request_body['action'] : null;
            $platform = isset($request_body['platform']) && $request_body['platform'] !== '' ? $request_body['platform'] : null;
            $from_date = isset($request_body['from_date']) && $request_body['from_date'] !== '' ? $request_body['from_date'] : null;
            $to_date = isset($request_body['to_date']) && $request_body['to_date'] !== '' ? $request_body['to_date'] : null;
            $ip_address = isset($request_body['ip_address']) && $request_body['ip_address'] !== '' ? $request_body['ip_address'] : null;
            $limit = isset($request_body['limit']) && $request_body['limit'] !== '' ? intval($request_body['limit']) : 100;

            // Get audit log data
            $result = $this->get_audit_logs($user_id, $action, $platform, $from_date, $to_date, $ip_address, $limit);

            // Calculate summary
            $total_logs = count($result);
            $actions_count = count(array_unique(array_column($result, 'action')));
            $users_count = count(array_unique(array_column($result, 'user_id')));
            
            // Group by action for distribution
            $action_distribution = array();
            foreach ($result as $log) {
                $action_name = $log['action'] ? $log['action'] : 'Unknown';
                if (!isset($action_distribution[$action_name])) {
                    $action_distribution[$action_name] = 0;
                }
                $action_distribution[$action_name]++;
            }

            $response = array(
                'status' => 1,
                'message' => 'Audit log data retrieved successfully',
                'filters_applied' => array(
                    'user_id' => $user_id,
                    'action' => $action,
                    'platform' => $platform,
                    'from_date' => $from_date,
                    'to_date' => $to_date,
                    'ip_address' => $ip_address,
                    'limit' => $limit
                ),
                'summary' => array(
                    'total_logs' => $total_logs,
                    'total_actions' => $actions_count,
                    'total_users' => $users_count,
                    'action_distribution' => $action_distribution
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
                    'message' => 'Error retrieving audit log data',
                    'error' => $e->getMessage()
                )));
        }
    }

    /**
     * Get audit logs using direct database queries
     */
    private function get_audit_logs($user_id, $action, $platform, $from_date, $to_date, $ip_address, $limit)
    {
        // Build the query
        $this->db->select('
            logs.id,
            logs.message,
            logs.record_id,
            logs.user_id,
            logs.action,
            logs.ip_address,
            logs.platform,
            logs.agent,
            logs.time,
            logs.created_at,
            CONCAT_WS(" ", staff.name, staff.surname, " (", staff.employee_id, ")") as staff_name,
            staff.employee_id,
            staff.name as staff_first_name,
            staff.surname as staff_last_name
        ');
        
        $this->db->from('logs');
        $this->db->join('staff', 'staff.id = logs.user_id', 'left');

        // Apply filters
        if ($user_id !== null) {
            $this->db->where('logs.user_id', $user_id);
        }

        if ($action !== null) {
            $this->db->where('logs.action', $action);
        }

        if ($platform !== null) {
            $this->db->where('logs.platform', $platform);
        }

        if ($ip_address !== null) {
            $this->db->like('logs.ip_address', $ip_address);
        }

        if ($from_date !== null) {
            $this->db->where('DATE(logs.time) >=', $from_date);
        }

        if ($to_date !== null) {
            $this->db->where('DATE(logs.time) <=', $to_date);
        }

        // Order by most recent first
        $this->db->order_by('logs.time', 'desc');

        // Apply limit
        $this->db->limit($limit);

        $query = $this->db->get();
        $results = $query->result_array();

        // Format the results
        foreach ($results as &$row) {
            // Format timestamp
            $row['formatted_time'] = date('Y-m-d H:i:s', strtotime($row['time']));
            $row['formatted_date'] = date('Y-m-d', strtotime($row['time']));
            $row['formatted_time_only'] = date('H:i:s', strtotime($row['time']));
            
            // Ensure staff_name is not null
            if (!$row['staff_name']) {
                $row['staff_name'] = 'Unknown User';
            }
            
            // Format message
            $row['message'] = $row['message'] ? $row['message'] : '';
            $row['record_id'] = $row['record_id'] ? $row['record_id'] : '';
            $row['action'] = $row['action'] ? $row['action'] : '';
            $row['ip_address'] = $row['ip_address'] ? $row['ip_address'] : '';
            $row['platform'] = $row['platform'] ? $row['platform'] : '';
            $row['agent'] = $row['agent'] ? $row['agent'] : '';
        }

        return $results;
    }
}

