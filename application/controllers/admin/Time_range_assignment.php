<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Time Range Assignment Controller
 * Manages assignment of time ranges to staff and students
 */
class Time_range_assignment extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('json_output');
        $this->load->model('time_range_assignment_model');
        $this->load->model('biometric_timing_model');
        $this->load->model('staff_model');
        $this->load->model('student_model');
    }

    /**
     * Main page - Time Range Assignment Management
     */
    public function index()
    {
        if (!$this->rbac->hasPrivilege('time_range_assignments', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'System Settings');
        $this->session->set_userdata('sub_menu', 'schsettings/index');
        $this->session->set_userdata('subsub_menu', 'admin/time_range_assignment');
        
        $data['title'] = 'Time Range Assignments';
        $data['time_ranges'] = $this->biometric_timing_model->getActiveTimeRanges();
        
        $this->load->view('layout/header', $data);
        $this->load->view('admin/time_range_assignment/index', $data);
        $this->load->view('layout/footer', $data);
    }

    // ========================================================================
    // STAFF ASSIGNMENT METHODS
    // ========================================================================

    /**
     * Get staff list for assignment (AJAX)
     */
    public function getStaffList()
    {
        if (!$this->rbac->hasPrivilege('time_range_assignments', 'can_view')) {
            json_output(403, array('status' => 403, 'message' => 'Access denied'));
            return;
        }

        $search = $this->input->post('search');
        $staff_list = $this->staff_model->get(); // Get all active staff
        
        // Filter by search term if provided
        if ($search) {
            $staff_list = array_filter($staff_list, function($staff) use ($search) {
                $name = strtolower($staff['name'] . ' ' . $staff['surname']);
                $employee_id = strtolower($staff['employee_id']);
                $search_lower = strtolower($search);
                return (strpos($name, $search_lower) !== false || strpos($employee_id, $search_lower) !== false);
            });
        }
        
        json_output(200, array(
            'status' => 200,
            'data' => array_values($staff_list)
        ));
    }

    /**
     * Get staff assignments (AJAX)
     */
    public function getStaffAssignments()
    {
        if (!$this->rbac->hasPrivilege('time_range_assignments', 'can_view')) {
            json_output(403, array('status' => 403, 'message' => 'Access denied'));
            return;
        }

        $staff_id = $this->input->post('staff_id');
        
        if (!$staff_id) {
            json_output(400, array('status' => 400, 'message' => 'Staff ID is required'));
            return;
        }

        $assignments = $this->time_range_assignment_model->getStaffAssignments($staff_id);
        $all_ranges = $this->biometric_timing_model->getActiveTimeRanges();
        
        // Mark which ranges are assigned
        $assigned_ids = array_column($assignments, 'time_range_id');
        foreach ($all_ranges as &$range) {
            $range['is_assigned'] = in_array($range['id'], $assigned_ids);
        }
        
        json_output(200, array(
            'status' => 200,
            'data' => array(
                'assignments' => $assignments,
                'all_ranges' => $all_ranges
            )
        ));
    }

    /**
     * Save staff assignments (AJAX)
     */
    public function saveStaffAssignments()
    {
        if (!$this->rbac->hasPrivilege('time_range_assignments', 'can_edit')) {
            json_output(403, array('status' => 403, 'message' => 'Access denied'));
            return;
        }

        $staff_id = $this->input->post('staff_id');
        $time_range_ids = $this->input->post('time_range_ids');
        
        if (!$staff_id) {
            json_output(400, array('status' => 400, 'message' => 'Staff ID is required'));
            return;
        }

        // time_range_ids can be empty array (remove all assignments)
        if ($time_range_ids === null) {
            $time_range_ids = array();
        }

        $created_by = $this->session->userdata('admin')['id'];
        $result = $this->time_range_assignment_model->bulkAssignStaff($staff_id, $time_range_ids, $created_by);
        
        if ($result) {
            json_output(200, array(
                'status' => 200,
                'message' => 'Staff assignments saved successfully'
            ));
        } else {
            json_output(500, array(
                'status' => 500,
                'message' => 'Failed to save staff assignments'
            ));
        }
    }

    // ========================================================================
    // STUDENT ASSIGNMENT METHODS
    // ========================================================================

    /**
     * Get student list for assignment (AJAX)
     */
    public function getStudentList()
    {
        if (!$this->rbac->hasPrivilege('time_range_assignments', 'can_view')) {
            json_output(403, array('status' => 403, 'message' => 'Access denied'));
            return;
        }

        $class_id = $this->input->post('class_id');
        $section_id = $this->input->post('section_id');
        $search = $this->input->post('search');
        
        if (!$class_id || !$section_id) {
            json_output(400, array('status' => 400, 'message' => 'Class and Section are required'));
            return;
        }

        $current_session = $this->setting_model->getCurrentSession();
        $student_list = $this->student_model->searchByClassSection($class_id, $section_id, $current_session);
        
        // Filter by search term if provided
        if ($search) {
            $student_list = array_filter($student_list, function($student) use ($search) {
                $name = strtolower($student['firstname'] . ' ' . $student['lastname']);
                $admission_no = strtolower($student['admission_no']);
                $search_lower = strtolower($search);
                return (strpos($name, $search_lower) !== false || strpos($admission_no, $search_lower) !== false);
            });
        }
        
        json_output(200, array(
            'status' => 200,
            'data' => array_values($student_list)
        ));
    }

    /**
     * Get student assignments (AJAX)
     */
    public function getStudentAssignments()
    {
        if (!$this->rbac->hasPrivilege('time_range_assignments', 'can_view')) {
            json_output(403, array('status' => 403, 'message' => 'Access denied'));
            return;
        }

        $student_session_id = $this->input->post('student_session_id');
        
        if (!$student_session_id) {
            json_output(400, array('status' => 400, 'message' => 'Student Session ID is required'));
            return;
        }

        $assignments = $this->time_range_assignment_model->getStudentAssignments($student_session_id);
        $all_ranges = $this->biometric_timing_model->getActiveTimeRanges();
        
        // Mark which ranges are assigned
        $assigned_ids = array_column($assignments, 'time_range_id');
        foreach ($all_ranges as &$range) {
            $range['is_assigned'] = in_array($range['id'], $assigned_ids);
        }
        
        json_output(200, array(
            'status' => 200,
            'data' => array(
                'assignments' => $assignments,
                'all_ranges' => $all_ranges
            )
        ));
    }

    /**
     * Save student assignments (AJAX)
     */
    public function saveStudentAssignments()
    {
        if (!$this->rbac->hasPrivilege('time_range_assignments', 'can_edit')) {
            json_output(403, array('status' => 403, 'message' => 'Access denied'));
            return;
        }

        $student_session_id = $this->input->post('student_session_id');
        $time_range_ids = $this->input->post('time_range_ids');
        
        if (!$student_session_id) {
            json_output(400, array('status' => 400, 'message' => 'Student Session ID is required'));
            return;
        }

        // time_range_ids can be empty array (remove all assignments)
        if ($time_range_ids === null) {
            $time_range_ids = array();
        }

        $created_by = $this->session->userdata('admin')['id'];
        $result = $this->time_range_assignment_model->bulkAssignStudent($student_session_id, $time_range_ids, $created_by);
        
        if ($result) {
            json_output(200, array(
                'status' => 200,
                'message' => 'Student assignments saved successfully'
            ));
        } else {
            json_output(500, array(
                'status' => 500,
                'message' => 'Failed to save student assignments'
            ));
        }
    }

    // ========================================================================
    // UTILITY METHODS
    // ========================================================================

    /**
     * Get time range statistics (AJAX)
     */
    public function getTimeRangeStats()
    {
        if (!$this->rbac->hasPrivilege('time_range_assignments', 'can_view')) {
            json_output(403, array('status' => 403, 'message' => 'Access denied'));
            return;
        }

        $time_range_id = $this->input->post('time_range_id');
        
        if (!$time_range_id) {
            json_output(400, array('status' => 400, 'message' => 'Time Range ID is required'));
            return;
        }

        $staff_count = $this->time_range_assignment_model->getStaffCountByRange($time_range_id);
        $student_count = $this->time_range_assignment_model->getStudentCountByRange($time_range_id);
        
        json_output(200, array(
            'status' => 200,
            'data' => array(
                'staff_count' => $staff_count,
                'student_count' => $student_count
            )
        ));
    }
}

