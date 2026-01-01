<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Time Range Assignment Model
 * Manages assignment of time ranges to staff and students
 */
class Time_range_assignment_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // ========================================================================
    // STAFF TIME RANGE ASSIGNMENTS
    // ========================================================================

    /**
     * Get all time range assignments for a specific staff member
     * @param int $staff_id
     * @param bool $active_only Return only active assignments
     * @return array
     */
    public function getStaffAssignments($staff_id, $active_only = true)
    {
        $this->db->select('stra.*, bts.range_name, bts.range_type, bts.time_start, bts.time_end, bts.is_active as range_is_active');
        $this->db->from('staff_time_range_assignments stra');
        $this->db->join('biometric_timing_setup bts', 'stra.time_range_id = bts.id', 'left');
        $this->db->where('stra.staff_id', $staff_id);
        
        if ($active_only) {
            $this->db->where('stra.is_active', 1);
            $this->db->where('bts.is_active', 1);
        }
        
        $this->db->order_by('bts.range_type', 'ASC');
        $this->db->order_by('bts.priority', 'ASC');
        
        return $this->db->get()->result_array();
    }

    /**
     * Check if a staff member is assigned to a specific time range
     * @param int $staff_id
     * @param int $time_range_id
     * @return bool
     */
    public function isStaffAssignedToRange($staff_id, $time_range_id)
    {
        $this->db->select('COUNT(*) as count');
        $this->db->from('staff_time_range_assignments');
        $this->db->where('staff_id', $staff_id);
        $this->db->where('time_range_id', $time_range_id);
        $this->db->where('is_active', 1);
        
        $result = $this->db->get()->row();
        return ($result && $result->count > 0);
    }

    /**
     * Add time range assignment for staff
     * @param int $staff_id
     * @param int $time_range_id
     * @param int $created_by Admin user ID
     * @return bool
     */
    public function addStaffAssignment($staff_id, $time_range_id, $created_by = null)
    {
        $data = array(
            'staff_id' => $staff_id,
            'time_range_id' => $time_range_id,
            'is_active' => 1,
            'created_by' => $created_by,
            'created_at' => date('Y-m-d H:i:s')
        );
        
        // Use INSERT IGNORE to avoid duplicate key errors
        $this->db->insert('staff_time_range_assignments', $data);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Remove time range assignment for staff
     * @param int $staff_id
     * @param int $time_range_id
     * @return bool
     */
    public function removeStaffAssignment($staff_id, $time_range_id)
    {
        $this->db->where('staff_id', $staff_id);
        $this->db->where('time_range_id', $time_range_id);
        $this->db->delete('staff_time_range_assignments');
        
        return $this->db->affected_rows() > 0;
    }

    /**
     * Bulk assign time ranges to staff
     * @param int $staff_id
     * @param array $time_range_ids Array of time range IDs
     * @param int $created_by Admin user ID
     * @return bool
     */
    public function bulkAssignStaff($staff_id, $time_range_ids, $created_by = null)
    {
        // First, remove all existing assignments
        $this->db->where('staff_id', $staff_id);
        $this->db->delete('staff_time_range_assignments');
        
        // Then add new assignments
        if (!empty($time_range_ids)) {
            $data = array();
            foreach ($time_range_ids as $time_range_id) {
                $data[] = array(
                    'staff_id' => $staff_id,
                    'time_range_id' => $time_range_id,
                    'is_active' => 1,
                    'created_by' => $created_by,
                    'created_at' => date('Y-m-d H:i:s')
                );
            }
            
            $this->db->insert_batch('staff_time_range_assignments', $data);
        }
        
        return true;
    }

    // ========================================================================
    // STUDENT TIME RANGE ASSIGNMENTS
    // ========================================================================

    /**
     * Get all time range assignments for a specific student
     * @param int $student_session_id
     * @param bool $active_only Return only active assignments
     * @return array
     */
    public function getStudentAssignments($student_session_id, $active_only = true)
    {
        $this->db->select('stra.*, bts.range_name, bts.range_type, bts.time_start, bts.time_end, bts.is_active as range_is_active');
        $this->db->from('student_time_range_assignments stra');
        $this->db->join('biometric_timing_setup bts', 'stra.time_range_id = bts.id', 'left');
        $this->db->where('stra.student_session_id', $student_session_id);
        
        if ($active_only) {
            $this->db->where('stra.is_active', 1);
            $this->db->where('bts.is_active', 1);
        }
        
        $this->db->order_by('bts.range_type', 'ASC');
        $this->db->order_by('bts.priority', 'ASC');
        
        return $this->db->get()->result_array();
    }

    /**
     * Check if a student is assigned to a specific time range
     * @param int $student_session_id
     * @param int $time_range_id
     * @return bool
     */
    public function isStudentAssignedToRange($student_session_id, $time_range_id)
    {
        $this->db->select('COUNT(*) as count');
        $this->db->from('student_time_range_assignments');
        $this->db->where('student_session_id', $student_session_id);
        $this->db->where('time_range_id', $time_range_id);
        $this->db->where('is_active', 1);
        
        $result = $this->db->get()->row();
        return ($result && $result->count > 0);
    }

    /**
     * Add time range assignment for student
     * @param int $student_session_id
     * @param int $time_range_id
     * @param int $created_by Admin user ID
     * @return bool
     */
    public function addStudentAssignment($student_session_id, $time_range_id, $created_by = null)
    {
        $data = array(
            'student_session_id' => $student_session_id,
            'time_range_id' => $time_range_id,
            'is_active' => 1,
            'created_by' => $created_by,
            'created_at' => date('Y-m-d H:i:s')
        );
        
        // Use INSERT IGNORE to avoid duplicate key errors
        $this->db->insert('student_time_range_assignments', $data);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Remove time range assignment for student
     * @param int $student_session_id
     * @param int $time_range_id
     * @return bool
     */
    public function removeStudentAssignment($student_session_id, $time_range_id)
    {
        $this->db->where('student_session_id', $student_session_id);
        $this->db->where('time_range_id', $time_range_id);
        $this->db->delete('student_time_range_assignments');
        
        return $this->db->affected_rows() > 0;
    }

    /**
     * Bulk assign time ranges to student
     * @param int $student_session_id
     * @param array $time_range_ids Array of time range IDs
     * @param int $created_by Admin user ID
     * @return bool
     */
    public function bulkAssignStudent($student_session_id, $time_range_ids, $created_by = null)
    {
        // First, remove all existing assignments
        $this->db->where('student_session_id', $student_session_id);
        $this->db->delete('student_time_range_assignments');
        
        // Then add new assignments
        if (!empty($time_range_ids)) {
            $data = array();
            foreach ($time_range_ids as $time_range_id) {
                $data[] = array(
                    'student_session_id' => $student_session_id,
                    'time_range_id' => $time_range_id,
                    'is_active' => 1,
                    'created_by' => $created_by,
                    'created_at' => date('Y-m-d H:i:s')
                );
            }
            
            $this->db->insert_batch('student_time_range_assignments', $data);
        }
        
        return true;
    }

    // ========================================================================
    // UTILITY METHODS
    // ========================================================================

    /**
     * Get count of staff members assigned to a specific time range
     * @param int $time_range_id
     * @return int
     */
    public function getStaffCountByRange($time_range_id)
    {
        $this->db->select('COUNT(DISTINCT staff_id) as count');
        $this->db->from('staff_time_range_assignments');
        $this->db->where('time_range_id', $time_range_id);
        $this->db->where('is_active', 1);
        
        $result = $this->db->get()->row();
        return $result ? $result->count : 0;
    }

    /**
     * Get count of students assigned to a specific time range
     * @param int $time_range_id
     * @return int
     */
    public function getStudentCountByRange($time_range_id)
    {
        $this->db->select('COUNT(DISTINCT student_session_id) as count');
        $this->db->from('student_time_range_assignments');
        $this->db->where('time_range_id', $time_range_id);
        $this->db->where('is_active', 1);
        
        $result = $this->db->get()->row();
        return $result ? $result->count : 0;
    }
}

