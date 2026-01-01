<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Session Fee Structure API Controller
 * 
 * Provides API endpoints for retrieving session-wise fee structure data including:
 * - Sessions with classes and sections
 * - Fee groups with fee types and amounts
 * - Hierarchical nested structure for easy consumption
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Fee Management
 * @author     SMS Development Team
 * @version    1.0.0
 */
class Session_fee_structure_api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        // Disable error display - API should only return JSON
        ini_set('display_errors', 0);
        error_reporting(0);

        // Load required models in correct order
        $this->load->model('setting_model');
        $this->load->model('auth_model');
        $this->load->model('session_model');
        $this->load->model('class_model');
        $this->load->model('feesessiongroup_model');
        $this->load->model('feegroup_model');
        $this->load->model('feetype_model');

        // Load database
        $this->load->database();
    }

    /**
     * Filter endpoint - Get session fee structure with filters
     * POST /api/session-fee-structure/filter
     * 
     * Request body (all parameters optional):
     * {
     *   "session_id": 1,
     *   "class_id": 2,
     *   "section_id": 3,
     *   "fee_group_id": 4,
     *   "fee_type_id": 5
     * }
     * 
     * Empty request body {} returns all session-wise fee structures
     */
    public function filter()
    {
        // Check request method
        if ($this->input->method() !== 'post') {
            $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 0,
                    'message' => 'Bad request. Only POST method allowed.'
                ]));
            return;
        }

        // Authenticate request
        if (!$this->auth_model->check_auth_client()) {
            $this->output
                ->set_status_header(401)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 0,
                    'message' => 'Unauthorized access'
                ]));
            return;
        }

        try {
            // Get JSON input
            $json_input = json_decode($this->input->raw_input_stream, true);
            
            // Get filter parameters (all optional)
            // Treat empty strings as null for graceful handling
            $session_id = (isset($json_input['session_id']) && $json_input['session_id'] !== '') ? $json_input['session_id'] : null;
            $class_id = (isset($json_input['class_id']) && $json_input['class_id'] !== '') ? $json_input['class_id'] : null;
            $section_id = (isset($json_input['section_id']) && $json_input['section_id'] !== '') ? $json_input['section_id'] : null;
            $fee_group_id = (isset($json_input['fee_group_id']) && $json_input['fee_group_id'] !== '') ? $json_input['fee_group_id'] : null;
            $fee_type_id = (isset($json_input['fee_type_id']) && $json_input['fee_type_id'] !== '') ? $json_input['fee_type_id'] : null;

            // Get session fee structure data
            $data = $this->get_session_fee_structure($session_id, $class_id, $section_id, $fee_group_id, $fee_type_id);

            $response = [
                'status' => 1,
                'message' => 'Session fee structure retrieved successfully',
                'filters_applied' => [
                    'session_id' => $session_id,
                    'class_id' => $class_id,
                    'section_id' => $section_id,
                    'fee_group_id' => $fee_group_id,
                    'fee_type_id' => $fee_type_id
                ],
                'total_sessions' => count($data),
                'data' => $data,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Session Fee Structure API Error: ' . $e->getMessage());
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 0,
                    'message' => 'Internal server error',
                    'error' => $e->getMessage()
                ]));
        }
    }

    /**
     * List endpoint - Get filter options for session fee structure
     * POST /api/session-fee-structure/list
     * 
     * Returns available sessions, classes, sections, fee groups, and fee types
     */
    public function list()
    {
        // Check request method
        if ($this->input->method() !== 'post') {
            $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 0,
                    'message' => 'Bad request. Only POST method allowed.'
                ]));
            return;
        }

        // Authenticate request
        if (!$this->auth_model->check_auth_client()) {
            $this->output
                ->set_status_header(401)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 0,
                    'message' => 'Unauthorized access'
                ]));
            return;
        }

        try {
            // Get all sessions
            $sessions = $this->session_model->getAllSession();
            
            // Get all classes
            $classes = $this->class_model->get();
            
            // Get all fee groups
            $fee_groups = $this->feegroup_model->get();
            
            // Get all fee types
            $fee_types = $this->feetype_model->get();

            $response = [
                'status' => 1,
                'message' => 'Session fee structure filter options retrieved successfully',
                'sessions' => $sessions,
                'classes' => $classes,
                'fee_groups' => $fee_groups,
                'fee_types' => $fee_types,
                'note' => 'Use the filter endpoint with parameters to get session fee structure data',
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Session Fee Structure API Error: ' . $e->getMessage());
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 0,
                    'message' => 'Internal server error',
                    'error' => $e->getMessage()
                ]));
        }
    }

    /**
     * Get session fee structure with nested hierarchy
     *
     * @param int|null $session_id
     * @param int|null $class_id
     * @param int|null $section_id
     * @param int|null $fee_group_id
     * @param int|null $fee_type_id
     * @return array
     */
    private function get_session_fee_structure($session_id = null, $class_id = null, $section_id = null, $fee_group_id = null, $fee_type_id = null)
    {
        // First, get all sessions (with optional session filter)
        $this->db->select('id as session_id, session as session_name, is_active as session_is_active');
        $this->db->from('sessions');

        if ($session_id !== null) {
            $this->db->where('id', $session_id);
        }

        $this->db->order_by('id DESC');
        $sessions_data = $this->db->get()->result_array();

        $sessions = [];

        foreach ($sessions_data as $session_row) {
            $sess_id = $session_row['session_id'];

            // Initialize session
            $sessions[$sess_id] = [
                'session_id' => $session_row['session_id'],
                'session_name' => $session_row['session_name'],
                'session_is_active' => $session_row['session_is_active'],
                'classes' => []
            ];

            // Get classes and sections for this session
            $this->db->select('
                classes.id as class_id,
                classes.class as class_name,
                classes.is_active as class_is_active,
                sections.id as section_id,
                sections.section as section_name,
                sections.is_active as section_is_active
            ');
            $this->db->from('student_session');
            $this->db->join('classes', 'classes.id = student_session.class_id');
            $this->db->join('sections', 'sections.id = student_session.section_id');
            $this->db->where('student_session.session_id', $sess_id);

            // Apply class and section filters
            if ($class_id !== null) {
                $this->db->where('classes.id', $class_id);
            }
            if ($section_id !== null) {
                $this->db->where('sections.id', $section_id);
            }

            $this->db->group_by('classes.id, sections.id');
            $this->db->order_by('classes.class ASC, sections.section ASC');

            $class_section_data = $this->db->get()->result_array();

            // Build classes and sections structure
            $classes = [];
            foreach ($class_section_data as $row) {
                $cls_id = $row['class_id'];

                // Initialize class if not exists
                if (!isset($classes[$cls_id])) {
                    $classes[$cls_id] = [
                        'class_id' => $row['class_id'],
                        'class_name' => $row['class_name'],
                        'class_is_active' => $row['class_is_active'],
                        'sections' => []
                    ];
                }

                // Add section
                $classes[$cls_id]['sections'][] = [
                    'section_id' => $row['section_id'],
                    'section_name' => $row['section_name'],
                    'section_is_active' => $row['section_is_active']
                ];
            }

            $sessions[$sess_id]['classes'] = array_values($classes);

            // Get fee groups with fee types for this session
            $sessions[$sess_id]['fee_groups'] = $this->get_fee_groups_for_session($sess_id, $fee_group_id, $fee_type_id);
        }

        return array_values($sessions);
    }

    /**
     * Get fee groups with fee types for a specific session
     * 
     * @param int $session_id
     * @param int|null $fee_group_id
     * @param int|null $fee_type_id
     * @return array
     */
    private function get_fee_groups_for_session($session_id, $fee_group_id = null, $fee_type_id = null)
    {
        // Get fee groups for this session
        $this->db->select('
            fee_session_groups.id as fee_session_group_id,
            fee_groups.id as fee_group_id,
            fee_groups.name as fee_group_name,
            fee_groups.description as fee_group_description,
            fee_groups.is_system as fee_group_is_system,
            fee_groups.is_active as fee_group_is_active
        ');
        $this->db->from('fee_session_groups');
        $this->db->join('fee_groups', 'fee_groups.id = fee_session_groups.fee_groups_id');
        $this->db->where('fee_session_groups.session_id', $session_id);
        
        if ($fee_group_id !== null) {
            $this->db->where('fee_groups.id', $fee_group_id);
        }
        
        $this->db->order_by('fee_groups.name ASC');
        
        $fee_groups_data = $this->db->get()->result_array();
        
        $fee_groups = [];
        foreach ($fee_groups_data as $fg) {
            $fee_groups[] = [
                'fee_session_group_id' => $fg['fee_session_group_id'],
                'fee_group_id' => $fg['fee_group_id'],
                'fee_group_name' => $fg['fee_group_name'],
                'fee_group_description' => $fg['fee_group_description'],
                'fee_group_is_system' => $fg['fee_group_is_system'],
                'fee_group_is_active' => $fg['fee_group_is_active'],
                'fee_types' => $this->get_fee_types_for_group($fg['fee_session_group_id'], $fg['fee_group_id'], $fee_type_id)
            ];
        }
        
        return $fee_groups;
    }

    /**
     * Get fee types for a specific fee group
     * 
     * @param int $fee_session_group_id
     * @param int $fee_group_id
     * @param int|null $fee_type_id
     * @return array
     */
    private function get_fee_types_for_group($fee_session_group_id, $fee_group_id, $fee_type_id = null)
    {
        $this->db->select('
            fee_groups_feetype.id as fee_groups_feetype_id,
            feetype.id as fee_type_id,
            feetype.type as fee_type_name,
            feetype.code as fee_type_code,
            feetype.description as fee_type_description,
            feetype.is_system as fee_type_is_system,
            feetype.is_active as fee_type_is_active,
            fee_groups_feetype.amount,
            fee_groups_feetype.due_date,
            fee_groups_feetype.fine_type,
            fee_groups_feetype.fine_percentage,
            fee_groups_feetype.fine_amount
        ');
        $this->db->from('fee_groups_feetype');
        $this->db->join('feetype', 'feetype.id = fee_groups_feetype.feetype_id');
        $this->db->where('fee_groups_feetype.fee_session_group_id', $fee_session_group_id);
        $this->db->where('fee_groups_feetype.fee_groups_id', $fee_group_id);
        
        if ($fee_type_id !== null) {
            $this->db->where('feetype.id', $fee_type_id);
        }
        
        $this->db->order_by('feetype.type ASC');
        
        return $this->db->get()->result_array();
    }
}

