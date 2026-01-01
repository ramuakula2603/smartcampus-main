<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Staff Report API Controller
 * 
 * This controller handles API requests for staff reports
 * showing staff information with filtering by role, designation, status, and date range.
 * 
 * @package    CodeIgniter
 * @subpackage Controllers
 * @category   API
 */
class Staff_report_api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Load required models - IMPORTANT: setting_model MUST be loaded FIRST
        $this->load->model('setting_model');
        $this->load->model('auth_model');
        $this->load->model('staff_model');
        $this->load->model('role_model');
        $this->load->model('leavetypes_model');
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->database();
    }

    /**
     * Filter staff report
     * 
     * POST /api/staff-report/filter
     * 
     * Request body (all parameters optional):
     * {
     *   "role": 1,
     *   "designation": 2,
     *   "staff_status": "1",
     *   "search_type": "this_year",
     *   "from_date": "2025-01-01",
     *   "to_date": "2025-12-31"
     * }
     * 
     * Empty request body {} returns all staff data
     */
    public function filter()
    {
        try {
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

            // Check authentication
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

            // Get JSON input
            $json_input = json_decode($this->input->raw_input_stream, true);
            
            // Get filter parameters (all optional)
            $role = isset($json_input['role']) ? $json_input['role'] : null;
            $designation = isset($json_input['designation']) ? $json_input['designation'] : null;
            $staff_status = isset($json_input['staff_status']) ? $json_input['staff_status'] : null;
            $search_type = isset($json_input['search_type']) ? $json_input['search_type'] : null;
            $from_date = isset($json_input['from_date']) ? $json_input['from_date'] : null;
            $to_date = isset($json_input['to_date']) ? $json_input['to_date'] : null;

            // Build condition string
            $condition = "";
            
            // Handle date range filter
            if (!empty($from_date) && !empty($to_date)) {
                $condition .= " and date_format(date_of_joining,'%Y-%m-%d') between '" . $this->db->escape_str($from_date) . "' and '" . $this->db->escape_str($to_date) . "'";
            } else if (!empty($search_type)) {
                // Handle predefined search types (this_week, this_month, this_year, etc.)
                $dates = $this->getDateRange($search_type);
                if ($dates) {
                    $condition .= " and date_format(date_of_joining,'%Y-%m-%d') between '" . $this->db->escape_str($dates['from_date']) . "' and '" . $this->db->escape_str($dates['to_date']) . "'";
                }
            }
            
            // Handle staff status filter
            if (!empty($staff_status)) {
                if ($staff_status == 'both') {
                    $search_status = "1,2";
                } elseif ($staff_status == '2') {
                    $search_status = "0";
                } else {
                    $search_status = "1";
                }
                $condition .= " and `staff`.`is_active` in (" . $search_status . ")";
            }
            
            // Handle role filter
            if (!empty($role)) {
                $condition .= " and `staff_roles`.`role_id`=" . $this->db->escape($role);
            }
            
            // Handle designation filter
            if (!empty($designation)) {
                $condition .= " and `staff_designation`.`id`=" . $this->db->escape($designation);
            }

            // Get staff report data
            $result = $this->staff_model->staff_report($condition);
            
            // Get leave types for reference
            $leave_types = $this->leavetypes_model->getLeaveType();
            $leave_type_map = array();
            foreach ($leave_types as $leave) {
                $leave_type_map[$leave['id']] = $leave['type'];
            }
            
            // Process leave data for each staff member
            foreach ($result as $key => $staff) {
                if (!empty($staff['leaves'])) {
                    $leaves_array = explode(',', $staff['leaves']);
                    $processed_leaves = array();
                    foreach ($leaves_array as $leave) {
                        $leave_parts = explode('@', $leave);
                        if (count($leave_parts) == 2) {
                            $leave_type_id = $leave_parts[0];
                            $alloted_leave = $leave_parts[1];
                            $processed_leaves[] = array(
                                'leave_type_id' => $leave_type_id,
                                'leave_type' => isset($leave_type_map[$leave_type_id]) ? $leave_type_map[$leave_type_id] : 'Unknown',
                                'alloted_leave' => $alloted_leave
                            );
                        }
                    }
                    $result[$key]['processed_leaves'] = $processed_leaves;
                }
            }

            $response = [
                'status' => 1,
                'message' => 'Staff report retrieved successfully',
                'filters_applied' => [
                    'role' => $role,
                    'designation' => $designation,
                    'staff_status' => $staff_status,
                    'search_type' => $search_type,
                    'from_date' => $from_date,
                    'to_date' => $to_date
                ],
                'total_records' => count($result),
                'data' => $result,
                'leave_types' => $leave_type_map,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Staff Report API Error: ' . $e->getMessage());
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
     * List staff roles and designations
     * 
     * POST /api/staff-report/list
     * 
     * Returns available roles and designations for filtering
     */
    public function list()
    {
        try {
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

            // Check authentication
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

            // Get roles
            $roles = $this->role_model->get();
            
            // Get designations
            $this->db->select('*')->from('staff_designation');
            $query = $this->db->get();
            $designations = $query->result_array();
            
            // Get leave types
            $leave_types = $this->leavetypes_model->getLeaveType();
            
            // Staff status options
            $status_options = array(
                array('value' => '1', 'label' => 'Active'),
                array('value' => '2', 'label' => 'Inactive'),
                array('value' => 'both', 'label' => 'Both')
            );

            $response = [
                'status' => 1,
                'message' => 'Staff filter options retrieved successfully',
                'total_roles' => count($roles),
                'roles' => $roles,
                'total_designations' => count($designations),
                'designations' => $designations,
                'total_leave_types' => count($leave_types),
                'leave_types' => $leave_types,
                'status_options' => $status_options,
                'note' => 'Use the filter endpoint with role, designation, staff_status, or date range to get staff report',
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Staff Report API Error: ' . $e->getMessage());
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
     * Helper function to get date range based on search type
     */
    private function getDateRange($search_type)
    {
        $dates = null;
        
        switch ($search_type) {
            case 'today':
                $dates = array(
                    'from_date' => date('Y-m-d'),
                    'to_date' => date('Y-m-d')
                );
                break;
            case 'this_week':
                $dates = array(
                    'from_date' => date('Y-m-d', strtotime('monday this week')),
                    'to_date' => date('Y-m-d', strtotime('sunday this week'))
                );
                break;
            case 'this_month':
                $dates = array(
                    'from_date' => date('Y-m-01'),
                    'to_date' => date('Y-m-t')
                );
                break;
            case 'this_year':
                $dates = array(
                    'from_date' => date('Y-01-01'),
                    'to_date' => date('Y-12-31')
                );
                break;
        }
        
        return $dates;
    }
}

