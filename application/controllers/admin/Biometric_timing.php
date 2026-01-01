<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Biometric Timing Controller
 * Manages multiple check-in and check-out time ranges
 */
class Biometric_timing extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('json_output');
        $this->load->model('biometric_timing_model');
        $this->load->model('attendencetype_model');
    }

    /**
     * Get all time ranges (AJAX)
     */
    public function getTimeRanges()
    {
        if (!$this->rbac->hasPrivilege('general_setting', 'can_view')) {
            json_output(403, array('status' => 403, 'message' => 'Access denied'));
            return;
        }

        $ranges = $this->biometric_timing_model->getTimeRangesGrouped();
        
        json_output(200, array(
            'status' => 200,
            'data' => $ranges
        ));
    }

    /**
     * Add a new time range (AJAX)
     */
    public function addTimeRange()
    {
        if (!$this->rbac->hasPrivilege('general_setting', 'can_add')) {
            json_output(403, array('status' => 403, 'message' => 'Access denied'));
            return;
        }

        try {
            // Validate required fields
            $range_name = $this->input->post('range_name');
            $range_type = $this->input->post('range_type');
            $time_start = $this->input->post('time_start');
            $time_end = $this->input->post('time_end');
            $attendance_type_id = $this->input->post('attendance_type_id');

            if (empty($range_name) || empty($range_type) || empty($time_start) || empty($time_end) || empty($attendance_type_id)) {
                json_output(400, array(
                    'status' => 400,
                    'message' => 'Missing required fields'
                ));
                return;
            }

            $data = array(
                'range_name' => $range_name,
                'range_type' => $range_type,
                'time_start' => $time_start,
                'time_end' => $time_end,
                'grace_period_minutes' => $this->input->post('grace_period_minutes') ?: 0,
                'attendance_type_id' => $attendance_type_id,
                'is_active' => $this->input->post('is_active') ? 1 : 0,
                'priority' => $this->input->post('priority') ?: 0,
            );

            $result = $this->biometric_timing_model->addTimeRange($data);

            if ($result) {
                json_output(200, array(
                    'status' => 200,
                    'message' => 'Time range added successfully',
                    'id' => $result
                ));
            } else {
                // Get database error if any
                $db_error = $this->db->error();
                json_output(500, array(
                    'status' => 500,
                    'message' => 'Failed to add time range',
                    'error' => $db_error['message']
                ));
            }
        } catch (Exception $e) {
            json_output(500, array(
                'status' => 500,
                'message' => 'Server error: ' . $e->getMessage()
            ));
        }
    }

    /**
     * Update a time range (AJAX)
     */
    public function updateTimeRange()
    {
        if (!$this->rbac->hasPrivilege('general_setting', 'can_edit')) {
            json_output(403, array('status' => 403, 'message' => 'Access denied'));
            return;
        }

        try {
            $id = $this->input->post('id');

            if (empty($id)) {
                json_output(400, array(
                    'status' => 400,
                    'message' => 'Missing time range ID'
                ));
                return;
            }

            $data = array(
                'range_name' => $this->input->post('range_name'),
                'range_type' => $this->input->post('range_type'),
                'time_start' => $this->input->post('time_start'),
                'time_end' => $this->input->post('time_end'),
                'grace_period_minutes' => $this->input->post('grace_period_minutes') ?: 0,
                'attendance_type_id' => $this->input->post('attendance_type_id'),
                'is_active' => $this->input->post('is_active') ? 1 : 0,
                'priority' => $this->input->post('priority') ?: 0,
            );

            $result = $this->biometric_timing_model->updateTimeRange($id, $data);

            if ($result) {
                json_output(200, array(
                    'status' => 200,
                    'message' => 'Time range updated successfully'
                ));
            } else {
                $db_error = $this->db->error();
                json_output(500, array(
                    'status' => 500,
                    'message' => 'Failed to update time range',
                    'error' => $db_error['message']
                ));
            }
        } catch (Exception $e) {
            json_output(500, array(
                'status' => 500,
                'message' => 'Server error: ' . $e->getMessage()
            ));
        }
    }

    /**
     * Delete a time range (AJAX)
     */
    public function deleteTimeRange()
    {
        if (!$this->rbac->hasPrivilege('general_setting', 'can_delete')) {
            json_output(403, array('status' => 403, 'message' => 'Access denied'));
            return;
        }

        $id = $this->input->post('id');
        $result = $this->biometric_timing_model->deleteTimeRange($id);

        if ($result) {
            json_output(200, array(
                'status' => 200,
                'message' => 'Time range deleted successfully'
            ));
        } else {
            json_output(500, array(
                'status' => 500,
                'message' => 'Failed to delete time range'
            ));
        }
    }

    /**
     * Toggle active status (AJAX)
     */
    public function toggleStatus()
    {
        if (!$this->rbac->hasPrivilege('general_setting', 'can_edit')) {
            json_output(403, array('status' => 403, 'message' => 'Access denied'));
            return;
        }

        $id = $this->input->post('id');
        $result = $this->biometric_timing_model->toggleActiveStatus($id);

        if ($result) {
            json_output(200, array(
                'status' => 200,
                'message' => 'Status updated successfully'
            ));
        } else {
            json_output(500, array(
                'status' => 500,
                'message' => 'Failed to update status'
            ));
        }
    }

    /**
     * Batch save time ranges (AJAX)
     */
    public function batchSaveTimeRanges()
    {
        if (!$this->rbac->hasPrivilege('general_setting', 'can_edit')) {
            json_output(403, array('status' => 403, 'message' => 'Access denied'));
            return;
        }

        $ranges = $this->input->post('ranges');
        
        if (!$ranges || !is_array($ranges)) {
            json_output(400, array(
                'status' => 400,
                'message' => 'Invalid data format'
            ));
            return;
        }

        $result = $this->biometric_timing_model->batchUpdateTimeRanges($ranges);

        if ($result) {
            json_output(200, array(
                'status' => 200,
                'message' => 'Time ranges saved successfully'
            ));
        } else {
            json_output(500, array(
                'status' => 500,
                'message' => 'Failed to save time ranges'
            ));
        }
    }

    /**
     * Get attendance types for dropdown
     */
    public function getAttendanceTypes()
    {
        $staff_types = $this->attendencetype_model->getStaffAttendanceType();

        json_output(200, array(
            'status' => 200,
            'data' => $staff_types
        ));
    }

    /**
     * Test endpoint to verify controller is accessible
     */
    public function test()
    {
        json_output(200, array(
            'status' => 200,
            'message' => 'Biometric timing controller is working',
            'timestamp' => date('Y-m-d H:i:s')
        ));
    }
}

