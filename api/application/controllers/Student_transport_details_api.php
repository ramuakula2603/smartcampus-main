<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Student Transport Details API Controller
 * 
 * This controller handles API requests for student transport details reports
 * showing students assigned to routes, vehicles, and pickup points.
 * 
 * @package    CodeIgniter
 * @subpackage Controllers
 * @category   API
 */
class Student_transport_details_api extends CI_Controller
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
     * POST /api/student-transport-details/list
     */
    public function list()
    {
        try {
            // Get classes
            $classes_query = $this->db->select('id, class')->from('classes')->order_by('class', 'asc')->get();
            $classes = $classes_query->result_array();

            // Get routes
            $routes_query = $this->db->select('id, route_title')->from('transport_route')->order_by('route_title', 'asc')->get();
            $routes = $routes_query->result_array();

            // Get vehicles
            $vehicles_query = $this->db->select('id, vehicle_no, vehicle_model, driver_name')->from('vehicles')->order_by('vehicle_no', 'asc')->get();
            $vehicles = $vehicles_query->result_array();

            $response = array(
                'status' => 1,
                'message' => 'Filter options retrieved successfully',
                'data' => array(
                    'classes' => $classes,
                    'routes' => $routes,
                    'vehicles' => $vehicles
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
     * Filter endpoint - Returns student transport details data
     * 
     * POST /api/student-transport-details/filter
     * 
     * Request body (all parameters optional):
     * {
     *   "class_id": 1,
     *   "section_id": 2,
     *   "transport_route_id": 3,
     *   "pickup_point_id": 4,
     *   "vehicle_id": 5
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
            $transport_route_id = isset($request_body['transport_route_id']) && $request_body['transport_route_id'] !== '' ? $request_body['transport_route_id'] : null;
            $pickup_point_id = isset($request_body['pickup_point_id']) && $request_body['pickup_point_id'] !== '' ? $request_body['pickup_point_id'] : null;
            $vehicle_id = isset($request_body['vehicle_id']) && $request_body['vehicle_id'] !== '' ? $request_body['vehicle_id'] : null;

            // Get student transport details
            $result = $this->get_student_transport_details($class_id, $section_id, $transport_route_id, $pickup_point_id, $vehicle_id);

            // Calculate summary
            $total_students = count($result);
            $routes_count = count(array_unique(array_column($result, 'route_title')));
            $vehicles_count = count(array_unique(array_column($result, 'vehicle_no')));
            $total_fees = array_sum(array_column($result, 'fees'));

            $response = array(
                'status' => 1,
                'message' => 'Student transport details retrieved successfully',
                'filters_applied' => array(
                    'class_id' => $class_id,
                    'section_id' => $section_id,
                    'transport_route_id' => $transport_route_id,
                    'pickup_point_id' => $pickup_point_id,
                    'vehicle_id' => $vehicle_id
                ),
                'summary' => array(
                    'total_students' => $total_students,
                    'total_routes' => $routes_count,
                    'total_vehicles' => $vehicles_count,
                    'total_transport_fees' => number_format($total_fees, 2, '.', '')
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
                    'message' => 'Error retrieving student transport details',
                    'error' => $e->getMessage()
                )));
        }
    }

    /**
     * Get student transport details using direct database queries
     */
    private function get_student_transport_details($class_id, $section_id, $transport_route_id, $pickup_point_id, $vehicle_id)
    {
        // Build the query
        $this->db->select('
            students.id,
            students.firstname,
            students.middlename,
            students.lastname,
            students.admission_no,
            students.father_name,
            students.mother_name,
            students.father_phone,
            students.mother_phone,
            students.mobileno,
            classes.class,
            sections.section,
            student_session.route_pickup_point_id,
            pickup_point.name as pickup_name,
            transport_route.route_title,
            route_pickup_point.fees,
            route_pickup_point.destination_distance,
            route_pickup_point.pickup_time,
            vehicles.id as vehicle_id,
            vehicles.vehicle_no,
            vehicles.vehicle_model,
            vehicles.driver_name,
            vehicles.driver_contact
        ');
        
        $this->db->from('students');
        $this->db->join('student_session', 'students.id = student_session.student_id');
        $this->db->join('sections', 'sections.id = student_session.section_id');
        $this->db->join('classes', 'classes.id = student_session.class_id');
        $this->db->join('route_pickup_point', 'student_session.route_pickup_point_id = route_pickup_point.id');
        $this->db->join('transport_route', 'transport_route.id = route_pickup_point.transport_route_id');
        $this->db->join('pickup_point', 'pickup_point.id = route_pickup_point.pickup_point_id');
        $this->db->join('vehicle_routes', 'student_session.vehroute_id = vehicle_routes.id');
        $this->db->join('vehicles', 'vehicle_routes.vehicle_id = vehicles.id');

        // Apply filters
        if ($class_id !== null) {
            $this->db->where('student_session.class_id', $class_id);
        }

        if ($section_id !== null) {
            $this->db->where('student_session.section_id', $section_id);
        }

        if ($transport_route_id !== null) {
            $this->db->where('route_pickup_point.transport_route_id', $transport_route_id);
        }

        if ($pickup_point_id !== null) {
            $this->db->where('route_pickup_point.pickup_point_id', $pickup_point_id);
        }

        if ($vehicle_id !== null) {
            $this->db->where('vehicles.id', $vehicle_id);
        }

        // Only active students in current session
        $this->db->where('students.is_active', 'yes');
        $this->db->where('student_session.session_id', $this->current_session);

        // Order by class and section
        $this->db->order_by('classes.class', 'asc');
        $this->db->order_by('sections.section', 'asc');

        $query = $this->db->get();
        $results = $query->result_array();

        // Format the results
        foreach ($results as &$row) {
            // Format student name
            $row['student_name'] = trim($row['firstname'] . ' ' . $row['middlename'] . ' ' . $row['lastname']);
            
            // Format class section
            $row['class_section'] = $row['class'] . ' - ' . $row['section'];
            
            // Format fees
            $row['fees'] = floatval($row['fees']);
            
            // Format distance
            $row['destination_distance'] = $row['destination_distance'] ? floatval($row['destination_distance']) : 0;
            
            // Format contact numbers
            $row['father_phone'] = $row['father_phone'] ? $row['father_phone'] : '';
            $row['mother_phone'] = $row['mother_phone'] ? $row['mother_phone'] : '';
            $row['mobileno'] = $row['mobileno'] ? $row['mobileno'] : '';
        }

        return $results;
    }
}

