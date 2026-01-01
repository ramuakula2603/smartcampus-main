<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Biometric Timing Model
 * Manages multiple check-in and check-out time ranges with late marking functionality
 */
class Biometric_timing_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all active time ranges
     * @param string $range_type Optional: 'checkin' or 'checkout'
     * @return array
     */
    public function getActiveTimeRanges($range_type = null)
    {
        $this->db->select('*');
        $this->db->from('biometric_timing_setup');
        $this->db->where('is_active', 1);
        
        if ($range_type !== null) {
            $this->db->where('range_type', $range_type);
        }
        
        $this->db->order_by('range_type', 'ASC');
        $this->db->order_by('priority', 'ASC');
        
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get all time ranges (active and inactive)
     * @return array
     */
    public function getAllTimeRanges()
    {
        $this->db->select('*');
        $this->db->from('biometric_timing_setup');
        $this->db->order_by('range_type', 'ASC');
        $this->db->order_by('priority', 'ASC');
        
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get a specific time range by ID
     * @param int $id
     * @return array|null
     */
    public function getTimeRangeById($id)
    {
        $this->db->select('*');
        $this->db->from('biometric_timing_setup');
        $this->db->where('id', $id);
        
        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * Find matching time range for a given time
     * @param string $time Time in HH:MM:SS format
     * @param string $range_type 'checkin' or 'checkout'
     * @return array|null Returns the matching time range or null
     */
    public function findMatchingTimeRange($time, $range_type)
    {
        // Convert time to comparable format
        $check_time = date('H:i:s', strtotime($time));
        
        $this->db->select('*');
        $this->db->from('biometric_timing_setup');
        $this->db->where('range_type', $range_type);
        $this->db->where('is_active', 1);
        $this->db->where("TIME('$check_time') BETWEEN time_start AND time_end");
        $this->db->order_by('priority', 'ASC');
        $this->db->limit(1);
        
        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * Determine attendance type based on punch time
     * This is the core logic for late marking
     *
     * @param string $punch_time Punch time in datetime format
     * @param string $range_type 'checkin' or 'checkout'
     * @param int $person_id Optional: staff_id or student_session_id for assignment validation
     * @param string $person_type Optional: 'staff' or 'student' for assignment validation
     * @return array Returns ['attendance_type_id' => int, 'range_name' => string, 'is_late' => bool, 'is_authorized' => bool]
     */
    public function determineAttendanceType($punch_time, $range_type = 'checkin', $person_id = null, $person_type = null)
    {
        $time_only = date('H:i:s', strtotime($punch_time));

        // Find matching time range
        $matching_range = $this->findMatchingTimeRange($time_only, $range_type);

        if ($matching_range) {
            // Check if person is authorized for this time range (if assignment checking is enabled)
            $is_authorized = true;
            if ($person_id !== null && $person_type !== null) {
                $is_authorized = $this->checkTimeRangeAuthorization($person_id, $person_type, $matching_range['id']);
            }

            // Check if within grace period
            $grace_end_time = date('H:i:s', strtotime($matching_range['time_start'] . ' + ' . $matching_range['grace_period_minutes'] . ' minutes'));

            $is_within_grace = ($time_only <= $grace_end_time);

            // If within grace period and the range is for "on time", mark as present
            // Otherwise, use the attendance type defined in the range
            if ($is_within_grace && $matching_range['attendance_type_id'] == 1) {
                $attendance_type_id = 1; // Present
                $is_late = false;
            } else {
                $attendance_type_id = $matching_range['attendance_type_id'];
                $is_late = ($attendance_type_id == 2); // 2 = Late
            }

            return [
                'attendance_type_id' => $attendance_type_id,
                'range_name' => $matching_range['range_name'],
                'is_late' => $is_late,
                'is_authorized' => $is_authorized,
                'time_range_id' => $matching_range['id'],
                'matched_range' => $matching_range
            ];
        }

        // No matching range found - default to present but log this
        return [
            'attendance_type_id' => 1, // Default to Present
            'range_name' => 'No matching range',
            'is_late' => false,
            'is_authorized' => true, // No range to check authorization against
            'time_range_id' => null,
            'matched_range' => null
        ];
    }

    /**
     * Check if a person is authorized to use a specific time range
     * @param int $person_id staff_id or student_session_id
     * @param string $person_type 'staff' or 'student'
     * @param int $time_range_id
     * @return bool True if authorized or no assignments exist, False if unauthorized
     */
    public function checkTimeRangeAuthorization($person_id, $person_type, $time_range_id)
    {
        if ($person_type === 'staff') {
            // Check if there are ANY assignments for this staff member
            $this->db->select('COUNT(*) as total_assignments');
            $this->db->from('staff_time_range_assignments');
            $this->db->where('staff_id', $person_id);
            $this->db->where('is_active', 1);
            $total_result = $this->db->get()->row();

            // If no assignments exist, allow all time ranges (backward compatibility)
            if ($total_result->total_assignments == 0) {
                return true;
            }

            // Check if assigned to this specific time range
            $this->db->select('COUNT(*) as count');
            $this->db->from('staff_time_range_assignments');
            $this->db->where('staff_id', $person_id);
            $this->db->where('time_range_id', $time_range_id);
            $this->db->where('is_active', 1);
            $result = $this->db->get()->row();

            return ($result && $result->count > 0);

        } elseif ($person_type === 'student') {
            // Check if there are ANY assignments for this student
            $this->db->select('COUNT(*) as total_assignments');
            $this->db->from('student_time_range_assignments');
            $this->db->where('student_session_id', $person_id);
            $this->db->where('is_active', 1);
            $total_result = $this->db->get()->row();

            // If no assignments exist, allow all time ranges (backward compatibility)
            if ($total_result->total_assignments == 0) {
                return true;
            }

            // Check if assigned to this specific time range
            $this->db->select('COUNT(*) as count');
            $this->db->from('student_time_range_assignments');
            $this->db->where('student_session_id', $person_id);
            $this->db->where('time_range_id', $time_range_id);
            $this->db->where('is_active', 1);
            $result = $this->db->get()->row();

            return ($result && $result->count > 0);
        }

        // Unknown person type - default to authorized
        return true;
    }

    /**
     * Add a new time range
     * @param array $data
     * @return int|bool Insert ID or false
     */
    public function addTimeRange($data)
    {
        $insert_data = [
            'range_name' => $data['range_name'],
            'range_type' => $data['range_type'],
            'time_start' => $data['time_start'],
            'time_end' => $data['time_end'],
            'grace_period_minutes' => isset($data['grace_period_minutes']) ? $data['grace_period_minutes'] : 0,
            'attendance_type_id' => $data['attendance_type_id'],
            'is_active' => isset($data['is_active']) ? $data['is_active'] : 1,
            'priority' => isset($data['priority']) ? $data['priority'] : 0,
        ];
        
        $this->db->insert('biometric_timing_setup', $insert_data);
        
        if ($this->db->affected_rows() > 0) {
            return $this->db->insert_id();
        }
        
        return false;
    }

    /**
     * Update an existing time range
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateTimeRange($id, $data)
    {
        $update_data = [];
        
        if (isset($data['range_name'])) {
            $update_data['range_name'] = $data['range_name'];
        }
        if (isset($data['range_type'])) {
            $update_data['range_type'] = $data['range_type'];
        }
        if (isset($data['time_start'])) {
            $update_data['time_start'] = $data['time_start'];
        }
        if (isset($data['time_end'])) {
            $update_data['time_end'] = $data['time_end'];
        }
        if (isset($data['grace_period_minutes'])) {
            $update_data['grace_period_minutes'] = $data['grace_period_minutes'];
        }
        if (isset($data['attendance_type_id'])) {
            $update_data['attendance_type_id'] = $data['attendance_type_id'];
        }
        if (isset($data['is_active'])) {
            $update_data['is_active'] = $data['is_active'];
        }
        if (isset($data['priority'])) {
            $update_data['priority'] = $data['priority'];
        }
        
        if (empty($update_data)) {
            return false;
        }
        
        $this->db->where('id', $id);
        $this->db->update('biometric_timing_setup', $update_data);
        
        return ($this->db->affected_rows() >= 0);
    }

    /**
     * Delete a time range
     * @param int $id
     * @return bool
     */
    public function deleteTimeRange($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('biometric_timing_setup');
        
        return ($this->db->affected_rows() > 0);
    }

    /**
     * Toggle active status of a time range
     * @param int $id
     * @return bool
     */
    public function toggleActiveStatus($id)
    {
        $range = $this->getTimeRangeById($id);
        
        if (!$range) {
            return false;
        }
        
        $new_status = ($range['is_active'] == 1) ? 0 : 1;
        
        $this->db->where('id', $id);
        $this->db->update('biometric_timing_setup', ['is_active' => $new_status]);
        
        return ($this->db->affected_rows() > 0);
    }

    /**
     * Batch update time ranges
     * @param array $ranges Array of time range data
     * @return bool
     */
    public function batchUpdateTimeRanges($ranges)
    {
        $this->db->trans_start();
        
        foreach ($ranges as $range) {
            if (isset($range['id']) && $range['id'] > 0) {
                // Update existing
                $this->updateTimeRange($range['id'], $range);
            } else {
                // Insert new
                $this->addTimeRange($range);
            }
        }
        
        $this->db->trans_complete();
        
        return $this->db->trans_status();
    }

    /**
     * Get time ranges grouped by type
     * @return array
     */
    public function getTimeRangesGrouped()
    {
        $ranges = $this->getAllTimeRanges();
        
        $grouped = [
            'checkin' => [],
            'checkout' => []
        ];
        
        foreach ($ranges as $range) {
            $grouped[$range['range_type']][] = $range;
        }
        
        return $grouped;
    }
}

