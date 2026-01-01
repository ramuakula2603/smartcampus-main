<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Biometric Check-in Model
 * Tracks and counts daily check-ins/check-outs for students and staff
 */
class Biometric_checkin_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get daily check-in summary for staff
     * @param string $date Date in Y-m-d format
     * @return array
     */
    public function getStaffCheckinSummary($date = null)
    {
        if ($date === null) {
            $date = date('Y-m-d');
        }

        // Get all active staff
        $this->db->select('staff.id, staff.name, staff.surname, staff.employee_id, roles.name as role_name, staff.department');
        $this->db->from('staff');
        $this->db->join('staff_roles', 'staff_roles.staff_id = staff.id', 'left');
        $this->db->join('roles', 'staff_roles.role_id = roles.id', 'left');
        $this->db->where('staff.is_active', 1);
        $this->db->order_by('staff.name', 'ASC');
        $all_staff = $this->db->get()->result_array();

        // Get check-ins/check-outs for the date with time range info
        $this->db->select('sa.staff_id, sa.created_at, sa.check_in_time, sa.check_out_time, sa.staff_attendance_type_id, sat.type as attendance_type, sa.remark, sa.biometric_attendence, bts.range_type, bts.range_name');
        $this->db->from('staff_attendance sa');
        $this->db->join('staff_attendance_type sat', 'sa.staff_attendance_type_id = sat.id', 'left');
        $this->db->join('biometric_timing_setup bts', 'sa.time_range_id = bts.id', 'left');
        $this->db->where('sa.date', $date);
        $this->db->where('sa.biometric_attendence', 1);
        $this->db->order_by('sa.created_at', 'ASC');
        $checkins = $this->db->get()->result_array();

        // Create a map of staff check-ins/check-outs
        $checkin_map = array();
        foreach ($checkins as $checkin) {
            if (!isset($checkin_map[$checkin['staff_id']])) {
                $checkin_map[$checkin['staff_id']] = array(
                    'all_records' => array(),
                    'checkins' => array(),
                    'checkouts' => array()
                );
            }
            $checkin_map[$checkin['staff_id']]['all_records'][] = $checkin;
            
            // Separate by type
            if ($checkin['range_type'] === 'checkout') {
                $checkin_map[$checkin['staff_id']]['checkouts'][] = $checkin;
            } else {
                $checkin_map[$checkin['staff_id']]['checkins'][] = $checkin;
            }
        }

        // Combine data
        $result = array();
        foreach ($all_staff as $staff) {
            $has_records = isset($checkin_map[$staff['id']]);
            $all_records = $has_records ? $checkin_map[$staff['id']]['all_records'] : array();
            $checkin_records = $has_records ? $checkin_map[$staff['id']]['checkins'] : array();
            $checkout_records = $has_records ? $checkin_map[$staff['id']]['checkouts'] : array();
            
            $staff_data = array(
                'id' => $staff['id'],
                'name' => $staff['name'] . ' ' . $staff['surname'],
                'employee_id' => $staff['employee_id'],
                'role' => $staff['role_name'],
                'department' => $staff['department'],
                'has_checked_in' => count($checkin_records) > 0,
                'has_checked_out' => count($checkout_records) > 0,
                'checkin_count' => count($checkin_records),
                'checkout_count' => count($checkout_records),
                'total_punch_count' => count($all_records),
                'checkins' => $all_records,
                'first_checkin_time' => null,
                'last_checkin_time' => null,
                'first_checkout_time' => null,
                'last_checkout_time' => null,
                'status' => 'Not Checked In'
            );

            // Get first and last check-in times
            if (count($checkin_records) > 0) {
                $first_checkin = $checkin_records[0];
                $last_checkin = end($checkin_records);
                
                $staff_data['first_checkin_time'] = date('h:i A', strtotime($first_checkin['created_at']));
                $staff_data['last_checkin_time'] = date('h:i A', strtotime($last_checkin['created_at']));
                $staff_data['status'] = $first_checkin['attendance_type'];
                $staff_data['remark'] = $first_checkin['remark'];
            }
            
            // Get first and last check-out times
            if (count($checkout_records) > 0) {
                $first_checkout = $checkout_records[0];
                $last_checkout = end($checkout_records);
                
                $staff_data['first_checkout_time'] = date('h:i A', strtotime($first_checkout['created_at']));
                $staff_data['last_checkout_time'] = date('h:i A', strtotime($last_checkout['created_at']));
            }

            $result[] = $staff_data;
        }

        // Sort results: Checked-in staff first, then not checked-in staff
        usort($result, function($a, $b) {
            // First sort by check-in status (checked in first)
            if ($a['has_checked_in'] != $b['has_checked_in']) {
                return $b['has_checked_in'] - $a['has_checked_in']; // true (1) before false (0)
            }
            
            // For checked-in staff, sort by check-out status (not checked out first)
            if ($a['has_checked_in'] && $b['has_checked_in']) {
                if ($a['has_checked_out'] != $b['has_checked_out']) {
                    return $a['has_checked_out'] - $b['has_checked_out']; // false (0) before true (1)
                }
            }
            
            // Finally, sort by name alphabetically
            return strcasecmp($a['name'], $b['name']);
        });

        return $result;
    }

    /**
     * Get daily check-in summary for students
     * @param string $date Date in Y-m-d format
     * @param int $class_id Optional class filter
     * @param int $section_id Optional section filter
     * @return array
     */
    public function getStudentCheckinSummary($date = null, $class_id = null, $section_id = null)
    {
        if ($date === null) {
            $date = date('Y-m-d');
        }

        $session_id = $this->setting_model->getCurrentSession();

        // Get all active students
        $this->db->select('students.id, students.firstname, students.middlename, students.lastname, students.admission_no, 
                          classes.class as class_name, sections.section as section_name, 
                          student_session.id as student_session_id, student_session.class_id, student_session.section_id');
        $this->db->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id', 'inner');
        $this->db->join('classes', 'student_session.class_id = classes.id', 'left');
        $this->db->join('sections', 'student_session.section_id = sections.id', 'left');
        $this->db->where('student_session.session_id', $session_id);
        $this->db->where('students.is_active', 'yes');
        
        if ($class_id !== null) {
            $this->db->where('student_session.class_id', $class_id);
        }
        
        if ($section_id !== null) {
            $this->db->where('student_session.section_id', $section_id);
        }
        
        $this->db->order_by('classes.class', 'ASC');
        $this->db->order_by('sections.section', 'ASC');
        $this->db->order_by('students.firstname', 'ASC');
        $all_students = $this->db->get()->result_array();

        // Get check-ins for the date
        $this->db->select('sa.student_session_id, sa.created_at, sa.attendence_type_id, at.type as attendance_type, sa.remark, sa.biometric_attendence');
        $this->db->from('student_attendences sa');
        $this->db->join('attendence_type at', 'sa.attendence_type_id = at.id', 'left');
        $this->db->where('sa.date', $date);
        $this->db->where('sa.biometric_attendence', 1);
        $this->db->order_by('sa.created_at', 'ASC');
        $checkins = $this->db->get()->result_array();

        // Create a map of student check-ins
        $checkin_map = array();
        foreach ($checkins as $checkin) {
            if (!isset($checkin_map[$checkin['student_session_id']])) {
                $checkin_map[$checkin['student_session_id']] = array();
            }
            $checkin_map[$checkin['student_session_id']][] = $checkin;
        }

        // Combine data
        $result = array();
        foreach ($all_students as $student) {
            $student_data = array(
                'id' => $student['id'],
                'student_session_id' => $student['student_session_id'],
                'name' => trim($student['firstname'] . ' ' . $student['middlename'] . ' ' . $student['lastname']),
                'admission_no' => $student['admission_no'],
                'class' => $student['class_name'],
                'section' => $student['section_name'],
                'class_id' => $student['class_id'],
                'section_id' => $student['section_id'],
                'has_checked_in' => isset($checkin_map[$student['student_session_id']]),
                'checkin_count' => isset($checkin_map[$student['student_session_id']]) ? count($checkin_map[$student['student_session_id']]) : 0,
                'checkins' => isset($checkin_map[$student['student_session_id']]) ? $checkin_map[$student['student_session_id']] : array(),
                'first_checkin_time' => null,
                'last_checkin_time' => null,
                'status' => 'Not Checked In'
            );

            if ($student_data['has_checked_in']) {
                $first_checkin = $checkin_map[$student['student_session_id']][0];
                $last_checkin = end($checkin_map[$student['student_session_id']]);
                
                $student_data['first_checkin_time'] = date('h:i A', strtotime($first_checkin['created_at']));
                $student_data['last_checkin_time'] = date('h:i A', strtotime($last_checkin['created_at']));
                $student_data['status'] = $first_checkin['attendance_type'];
                $student_data['remark'] = $first_checkin['remark'];
            }

            $result[] = $student_data;
        }

        return $result;
    }

    /**
     * Get check-in statistics for a date
     * @param string $date Date in Y-m-d format
     * @return array
     */
    public function getCheckinStatistics($date = null)
    {
        if ($date === null) {
            $date = date('Y-m-d');
        }

        $session_id = $this->setting_model->getCurrentSession();

        // Staff statistics
        $this->db->select('COUNT(DISTINCT staff.id) as total_staff');
        $this->db->from('staff');
        $this->db->where('staff.is_active', 1);
        $total_staff = $this->db->get()->row()->total_staff;

        // Get checked in staff (checkin type)
        $this->db->select('COUNT(DISTINCT sa.staff_id) as checked_in_staff');
        $this->db->from('staff_attendance sa');
        $this->db->join('biometric_timing_setup bts', 'sa.time_range_id = bts.id', 'left');
        $this->db->where('sa.date', $date);
        $this->db->where('sa.biometric_attendence', 1);
        $this->db->where('(bts.range_type = "checkin" OR bts.range_type IS NULL)');
        $checked_in_staff = $this->db->get()->row()->checked_in_staff;

        // Get checked out staff (checkout type)
        $this->db->select('COUNT(DISTINCT sa.staff_id) as checked_out_staff');
        $this->db->from('staff_attendance sa');
        $this->db->join('biometric_timing_setup bts', 'sa.time_range_id = bts.id', 'inner');
        $this->db->where('sa.date', $date);
        $this->db->where('sa.biometric_attendence', 1);
        $this->db->where('bts.range_type', 'checkout');
        $checked_out_staff = $this->db->get()->row()->checked_out_staff;

        // Get total punch count
        $this->db->select('COUNT(*) as total_punches');
        $this->db->from('staff_attendance');
        $this->db->where('date', $date);
        $this->db->where('biometric_attendence', 1);
        $total_punches = $this->db->get()->row()->total_punches;

        // Student statistics
        $this->db->select('COUNT(DISTINCT student_session.id) as total_students');
        $this->db->from('student_session');
        $this->db->join('students', 'student_session.student_id = students.id', 'inner');
        $this->db->where('student_session.session_id', $session_id);
        $this->db->where('students.is_active', 'yes');
        $total_students = $this->db->get()->row()->total_students;

        $this->db->select('COUNT(DISTINCT sa.student_session_id) as checked_in_students');
        $this->db->from('student_attendences sa');
        $this->db->where('sa.date', $date);
        $this->db->where('sa.biometric_attendence', 1);
        $checked_in_students = $this->db->get()->row()->checked_in_students;

        return array(
            'date' => $date,
            'staff' => array(
                'total' => $total_staff,
                'checked_in' => $checked_in_staff,
                'checked_out' => $checked_out_staff,
                'not_checked_in' => $total_staff - $checked_in_staff,
                'total_punches' => $total_punches,
                'percentage' => $total_staff > 0 ? round(($checked_in_staff / $total_staff) * 100, 2) : 0
            ),
            'students' => array(
                'total' => $total_students,
                'checked_in' => $checked_in_students,
                'not_checked_in' => $total_students - $checked_in_students,
                'percentage' => $total_students > 0 ? round(($checked_in_students / $total_students) * 100, 2) : 0
            )
        );
    }

    /**
     * Get check-in/check-out details for a specific person
     * @param int $id Person ID
     * @param string $type 'staff' or 'student'
     * @param string $date Date in Y-m-d format
     * @return array
     */
    public function getPersonCheckinDetails($id, $type, $date = null)
    {
        if ($date === null) {
            $date = date('Y-m-d');
        }

        if ($type === 'staff') {
            $this->db->select('sa.*, sat.type as attendance_type, bts.range_name, bts.range_type, bts.time_start, bts.time_end');
            $this->db->from('staff_attendance sa');
            $this->db->join('staff_attendance_type sat', 'sa.staff_attendance_type_id = sat.id', 'left');
            $this->db->join('biometric_timing_setup bts', 'sa.remark LIKE CONCAT("%", bts.range_name, "%")', 'left');
            $this->db->where('sa.staff_id', $id);
            $this->db->where('sa.date', $date);
            $this->db->where('sa.biometric_attendence', 1);
            $this->db->order_by('sa.created_at', 'ASC');
        } else {
            $this->db->select('sa.*, at.type as attendance_type, bts.range_name, bts.range_type, bts.time_start, bts.time_end');
            $this->db->from('student_attendences sa');
            $this->db->join('attendence_type at', 'sa.attendence_type_id = at.id', 'left');
            $this->db->join('biometric_timing_setup bts', 'sa.remark LIKE CONCAT("%", bts.range_name, "%")', 'left');
            $this->db->where('sa.student_session_id', $id);
            $this->db->where('sa.date', $date);
            $this->db->where('sa.biometric_attendence', 1);
            $this->db->order_by('sa.created_at', 'ASC');
        }

        $records = $this->db->get()->result_array();

        // Group by time range and add summary
        return $this->groupCheckinsByTimeRange($records);
    }

    /**
     * Group check-ins by time range and provide summary
     * @param array $records Raw check-in records
     * @return array
     */
    private function groupCheckinsByTimeRange($records)
    {
        $grouped = array();
        $summary = array();
        $all_records = array();

        foreach ($records as $record) {
            // Extract time range info
            $range_key = $record['range_name'] ?: 'Unknown Range';
            $range_type = $record['range_type'] ?: 'unknown';

            // Initialize group if not exists
            if (!isset($grouped[$range_key])) {
                $grouped[$range_key] = array(
                    'range_name' => $record['range_name'],
                    'range_type' => $range_type,
                    'range_type_label' => ucfirst($range_type),
                    'time_start' => $record['time_start'],
                    'time_end' => $record['time_end'],
                    'time_range_display' => $this->formatTimeRange($record['time_start'], $record['time_end']),
                    'count' => 0,
                    'records' => array()
                );
            }

            // Add record to group
            $grouped[$range_key]['count']++;
            $grouped[$range_key]['records'][] = array(
                'created_at' => $record['created_at'],
                'time_display' => date('h:i A', strtotime($record['created_at'])),
                'attendance_type' => $record['attendance_type'],
                'remark' => $record['remark']
            );

            // Add to all records list
            $all_records[] = array(
                'created_at' => $record['created_at'],
                'time_display' => date('h:i A', strtotime($record['created_at'])),
                'attendance_type' => $record['attendance_type'],
                'remark' => $record['remark'],
                'range_name' => $record['range_name'],
                'range_type' => $range_type,
                'range_type_label' => ucfirst($range_type)
            );

            // Update summary counts
            if (!isset($summary[$range_type])) {
                $summary[$range_type] = 0;
            }
            $summary[$range_type]++;
        }

        return array(
            'grouped' => $grouped,
            'summary' => $summary,
            'all_records' => $all_records,
            'total_count' => count($records)
        );
    }

    /**
     * Format time range for display
     * @param string $start_time
     * @param string $end_time
     * @return string
     */
    private function formatTimeRange($start_time, $end_time)
    {
        if (!$start_time || !$end_time) {
            return '';
        }

        $start = date('g:i A', strtotime($start_time));
        $end = date('g:i A', strtotime($end_time));

        return $start . ' - ' . $end;
    }
}

