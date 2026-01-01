<?php

class Staffattendancemodel extends MY_Model {

    public function __construct() {
        parent::__construct();

        // Load required models
        $this->load->model('setting_model');

        $this->current_session = $this->setting_model->getCurrentSession();
        $this->current_date = $this->setting_model->getDateYmd();
    }

    public function get($id = null) {
        $this->db->select()->join("staff", "staff.id = staff_attendance.staff_id")->from('staff_attendance');
        $this->db->where("staff.is_active", 1);
        if ($id != null) {
            $this->db->where('staff_attendance.id', $id);
        } else {
            $this->db->order_by('staff_attendance.id');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    public function getUserType() {

        $query = $this->db->query("select distinct user_type from staff where is_active = 1");

        return $query->result_array();
    }

    public function searchAttendenceUserType($user_type, $date) {
        $condition = '';
        if ($this->session->has_userdata('admin')) {
            $getStaffRole     = $this->customlib->getStaffRole();
            $staffrole   =   json_decode($getStaffRole);       
            $superadmin_visible = $this->customlib->superadmin_visible(); 
            if ($superadmin_visible == 'disabled' && $staffrole->id != 7) {                 
                $condition = " and roles.id != 7";
            } 
        }
        
        if ($user_type == "select") {   

            $query = $this->db->query("select staff_attendance.id,staff_attendance.created_at as attendence_dt, staff_attendance.staff_attendance_type_id,staff_attendance.biometric_attendence,staff_attendance.remark,staff.name,staff.surname,staff.employee_id,staff.contact_no,staff.email,roles.name as user_type,IFNULL(staff_attendance.date, 'xxx') as date,staff.id as staff_id from staff left join staff_roles on staff_roles.staff_id = staff.id left join roles on staff_roles.role_id = roles.id left join staff_attendance on (staff.id = staff_attendance.staff_id) and staff_attendance.date = " . $this->db->escape($date) . " where staff.is_active = 1 $condition");
        } else {

            $query = $this->db->query("select staff_attendance.staff_attendance_type_id,staff_attendance.created_at as attendence_dt,staff_attendance.biometric_attendence,staff_attendance.remark,staff.name,staff.surname,staff.employee_id,staff.contact_no,staff.email,roles.name as user_type,IFNULL(staff_attendance.date, 'xxx') as date, IFNULL(staff_attendance.id, 0) as id, staff.id as staff_id from staff left join staff_roles on (staff.id = staff_roles.staff_id) left join roles on (roles.id = staff_roles.role_id) left join staff_attendance on (staff.id = staff_attendance.staff_id) and staff_attendance.date = " . $this->db->escape($date) . " where roles.name = " . $this->db->escape($user_type) . " and staff.is_active = 1 $condition");
        }
        return $query->result_array();
    }

    public function add($data) {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('staff_attendance', $data);
            $message = UPDATE_RECORD_CONSTANT . " On staff attendance id " . $data['id'];
            $action = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);
        } else {
            $this->db->insert('staff_attendance', $data);
            $id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On staff attendance id " . $id;
            $action = "Insert";
            $record_id = $id;
            $this->log($message, $record_id, $action);
        }
        //======================Code End==============================
        $this->db->trans_complete(); # Completing transaction
        /* Optional */
        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            //return $return_value;
        }
    }

    public function getStaffAttendanceType() {

        $query = $this->db->select('*')->where("is_active", 'yes')->get("staff_attendance_type");

        return $query->result_array();
    }

    public function searchAttendanceReport($user_type, $date) {

        // Initialize condition variable
        $condition = '';
        
        if ($this->session->has_userdata('admin')) {
            $getStaffRole     = $this->customlib->getStaffRole();
            $staffrole   =   json_decode($getStaffRole);       
             
            $superadmin_visible = $this->customlib->superadmin_visible(); 
            if ($superadmin_visible == 'disabled' && $staffrole->id != 7) {
                $condition = "and staff_roles.role_id != 7";       
            } 
        }
        
        if ($user_type == "select") {

            $query = $this->db->query("select staff_attendance.staff_attendance_type_id,staff_attendance_type.type as `att_type`,staff_attendance_type.key_value as `key`,staff_attendance.remark,staff.name,staff.surname,staff.employee_id,staff.contact_no,staff.email,roles.name as user_type,IFNULL(staff_attendance.date, 'xxx') as date, IFNULL(staff_attendance.id, 0) as attendence_id, staff.id as id from staff left join staff_attendance on (staff.id = staff_attendance.staff_id) and staff_attendance.date = " . $this->db->escape($date) . " left join staff_attendance_type on staff_attendance_type.id = staff_attendance.staff_attendance_type_id left join staff_roles on staff_roles.staff_id = staff.id left join roles on staff_roles.role_id = roles.id where staff.is_active = 1 $condition");
        } else {

            $query = $this->db->query("select staff_attendance.staff_attendance_type_id,staff_attendance_type.type as `att_type`,staff_attendance_type.key_value as `key`,staff_attendance.remark,staff.name,staff.surname,staff.employee_id,staff.contact_no,staff.email,roles.name as user_type,IFNULL(staff_attendance.date, 'xxx') as date, IFNULL(staff_attendance.id, 0) as attendence_id, staff.id as id from staff  left join staff_roles on (staff.id = staff_roles.staff_id) left join roles on (roles.id = staff_roles.role_id) left join staff_attendance on (staff.id = staff_attendance.staff_id) and staff_attendance.date = " . $this->db->escape($date) . " left join staff_attendance_type on staff_attendance_type.id = staff_attendance.staff_attendance_type_id  where roles.name = '" . $user_type . "' and staff.is_active = 1 $condition");
        }

        return $query->result_array();
    }

    public function attendanceYearCount() {

        $query = $this->db->select("distinct year(date) as year")->get("staff_attendance");

        return $query->result_array();
    }

    /**
     * Get Staff Attendance Report by Filters
     *
     * Retrieves staff attendance records with optional filtering by role, date range, and staff ID.
     *
     * @param mixed  $role_id    Role ID (single value, array, or null)
     * @param string $from_date  Start date for date range filter (or null)
     * @param string $to_date    End date for date range filter (or null)
     * @param mixed  $staff_id   Staff ID (single value, array, or null)
     * @param int    $session_id Session ID (defaults to current session if not provided)
     * @return array Array of staff attendance records
     */
    public function getStaffAttendanceReportByFilters($role_id = null, $from_date = null, $to_date = null, $staff_id = null, $session_id = null)
    {
        // If session_id is not provided, use current session
        if (empty($session_id)) {
            $session_id = $this->current_session;
        }

        $this->db->select('staff_attendance.*,
                          staff.name,
                          staff.surname,
                          staff.employee_id,
                          staff.contact_no,
                          staff.email,
                          roles.name as role_name,
                          staff_attendance_type.type as attendance_type,
                          staff_attendance_type.key_value as attendance_key');
        $this->db->from('staff_attendance');
        $this->db->join('staff', 'staff.id = staff_attendance.staff_id', 'inner');
        $this->db->join('staff_roles', 'staff_roles.staff_id = staff.id', 'left');
        $this->db->join('roles', 'roles.id = staff_roles.role_id', 'left');
        $this->db->join('staff_attendance_type', 'staff_attendance_type.id = staff_attendance.staff_attendance_type_id', 'left');
        $this->db->where('staff.is_active', 1);

        // Apply role filter if provided
        if ($role_id !== null && !empty($role_id)) {
            if (is_array($role_id) && count($role_id) > 0) {
                $this->db->where_in('roles.id', $role_id);
            } else {
                $this->db->where('roles.id', $role_id);
            }
        }

        // Apply date range filter if provided
        if (!empty($from_date) && !empty($to_date)) {
            $this->db->where('staff_attendance.date >=', $from_date);
            $this->db->where('staff_attendance.date <=', $to_date);
        } elseif (!empty($from_date)) {
            $this->db->where('staff_attendance.date >=', $from_date);
        } elseif (!empty($to_date)) {
            $this->db->where('staff_attendance.date <=', $to_date);
        }

        // Apply staff filter if provided
        if ($staff_id !== null && !empty($staff_id)) {
            if (is_array($staff_id) && count($staff_id) > 0) {
                $this->db->where_in('staff.id', $staff_id);
            } else {
                $this->db->where('staff.id', $staff_id);
            }
        }

        $this->db->order_by('staff_attendance.date', 'DESC');
        $this->db->order_by('staff.name', 'ASC');

        $query = $this->db->get();
        return $query->result_array();
    }

    public function searchStaffattendance($date, $staff_id, $active_staff = true) {

        $sql = "select staff_attendance.staff_attendance_type_id,staff_attendance_type.type as `att_type`,staff_attendance_type.key_value as `key`,staff_attendance.remark,staff.name,staff.surname,staff.contact_no,staff.email,roles.name as user_type,IFNULL(staff_attendance.date, 'xxx') as date, IFNULL(staff_attendance.id, 0) as attendence_id, staff.id as id from staff left join staff_attendance on (staff.id = staff_attendance.staff_id) and staff_attendance.date = " . $this->db->escape($date) . " left join staff_roles on staff_roles.staff_id = staff.id left join roles on staff_roles.role_id = roles.id left join staff_attendance_type on staff_attendance_type.id = staff_attendance.staff_attendance_type_id where staff.id = " . $this->db->escape($staff_id);
        if ($active_staff || !isset($active_staff)) {
            $sql .= " and staff.is_active = 1";
        }
        $query = $this->db->query($sql);
        return $query->row_array();
    }


        public function onlineattendence($data) {

        $this->db->where('staff_id', $data['staff_id']);
        $this->db->where('date', $data['date']);
        $q = $this->db->get('staff_attendance');

        if ($q->num_rows() == 0) {
            $this->db->insert('staff_attendance', $data);
            return ($this->db->affected_rows() != 1) ? false : true;
        }
        return false;
    }

    /**
     * Get comprehensive attendance summary for staff members
     * @param int|null $staff_id - Specific staff ID or null for all staff
     * @param string $from_date - Start date (Y-m-d format)
     * @param string $to_date - End date (Y-m-d format)
     * @return array - Comprehensive attendance data with statistics and dates
     */
    public function getAttendanceSummary($staff_id = null, $from_date = null, $to_date = null) {
        // Set default date range if not provided - use actual data range instead of current year
        if (empty($from_date) || empty($to_date)) {
            $date_range = $this->getAttendanceDateRange($staff_id);

            if (empty($from_date)) {
                $from_date = $date_range['min_date'] ?: date('Y-01-01');
            }
            if (empty($to_date)) {
                $to_date = $date_range['max_date'] ?: date('Y-12-31');
            }
        }

        // Validate date format
        if (!$this->isValidDate($from_date) || !$this->isValidDate($to_date)) {
            return array('error' => 'Invalid date format. Use Y-m-d format.');
        }

        // Ensure from_date is not greater than to_date
        if (strtotime($from_date) > strtotime($to_date)) {
            return array('error' => 'From date cannot be greater than to date.');
        }

        $result = array();

        if ($staff_id) {
            // Get data for specific staff member
            $staff_data = $this->getStaffAttendanceData($staff_id, $from_date, $to_date);
            if ($staff_data) {
                $result = array(
                    'staff_id' => $staff_id,
                    'staff_info' => $staff_data['staff_info'],
                    'attendance_summary' => $staff_data['attendance_summary'],
                    'attendance_dates' => $staff_data['attendance_dates'],
                    'leave_summary' => $staff_data['leave_summary'],
                    'date_range' => array(
                        'from_date' => $from_date,
                        'to_date' => $to_date
                    )
                );
            } else {
                $result = array('error' => 'Staff member not found or no data available.');
            }
        } else {
            // Get data for all active staff members
            $all_staff = $this->getAllStaffAttendanceData($from_date, $to_date);
            $result = array(
                'staff_attendance_data' => $all_staff,
                'total_staff' => count($all_staff),
                'date_range' => array(
                    'from_date' => $from_date,
                    'to_date' => $to_date
                )
            );
        }

        return $result;
    }

    /**
     * Get attendance data for a specific staff member
     */
    private function getStaffAttendanceData($staff_id, $from_date, $to_date) {
        // Get staff basic information
        $this->db->select('staff.id, staff.name, staff.surname, staff.employee_id, staff.email, staff.contact_no,
                          staff_designation.designation, department.department_name, roles.name as role_name');
        $this->db->from('staff');
        $this->db->join('staff_designation', 'staff_designation.id = staff.designation', 'left');
        $this->db->join('department', 'department.id = staff.department', 'left');
        $this->db->join('staff_roles', 'staff_roles.staff_id = staff.id', 'left');
        $this->db->join('roles', 'roles.id = staff_roles.role_id', 'left');
        $this->db->where('staff.id', $staff_id);
        $this->db->where('staff.is_active', 1);
        $staff_info = $this->db->get()->row_array();

        if (!$staff_info) {
            return null;
        }

        // Get attendance types
        $attendance_types = $this->getStaffAttendanceType();

        // Initialize attendance summary
        $attendance_summary = array();
        $attendance_dates = array();

        foreach ($attendance_types as $type) {
            $attendance_summary[$type['type']] = array(
                'count' => 0,
                'dates' => array()
            );
        }

        // Get attendance records within date range
        $this->db->select('staff_attendance.date, staff_attendance.remark, staff_attendance.created_at,
                          staff_attendance_type.type, staff_attendance_type.key_value');
        $this->db->from('staff_attendance');
        $this->db->join('staff_attendance_type', 'staff_attendance_type.id = staff_attendance.staff_attendance_type_id');
        $this->db->where('staff_attendance.staff_id', $staff_id);
        $this->db->where('staff_attendance.date >=', $from_date);
        $this->db->where('staff_attendance.date <=', $to_date);
        $this->db->order_by('staff_attendance.date', 'DESC');
        $attendance_records = $this->db->get()->result_array();

        // Process attendance records
        foreach ($attendance_records as $record) {
            $type = $record['type'];
            if (isset($attendance_summary[$type])) {
                $attendance_summary[$type]['count']++;
                $attendance_summary[$type]['dates'][] = array(
                    'date' => $record['date'],
                    'remark' => $record['remark'],
                    'recorded_at' => $record['created_at']
                );
            }

            $attendance_dates[] = array(
                'date' => $record['date'],
                'type' => $type,
                'key_value' => $record['key_value'],
                'remark' => $record['remark'],
                'recorded_at' => $record['created_at']
            );
        }

        // Get leave data
        $leave_summary = $this->getStaffLeaveData($staff_id, $from_date, $to_date);

        return array(
            'staff_info' => $staff_info,
            'attendance_summary' => $attendance_summary,
            'attendance_dates' => $attendance_dates,
            'leave_summary' => $leave_summary
        );
    }

    /**
     * Get attendance data for all active staff members
     */
    private function getAllStaffAttendanceData($from_date, $to_date) {
        // Get all active staff
        $this->db->select('staff.id');
        $this->db->from('staff');
        $this->db->where('staff.is_active', 1);
        $staff_list = $this->db->get()->result_array();

        $all_staff_data = array();

        foreach ($staff_list as $staff) {
            $staff_data = $this->getStaffAttendanceData($staff['id'], $from_date, $to_date);
            if ($staff_data) {
                $all_staff_data[] = array(
                    'staff_id' => $staff['id'],
                    'staff_info' => $staff_data['staff_info'],
                    'attendance_summary' => $staff_data['attendance_summary'],
                    'attendance_dates' => $staff_data['attendance_dates'],
                    'leave_summary' => $staff_data['leave_summary']
                );
            }
        }

        return $all_staff_data;
    }

    /**
     * Get staff leave data within date range
     */
    private function getStaffLeaveData($staff_id, $from_date, $to_date) {
        $this->db->select('staff_leave_request.*, leave_types.type as leave_type_name');
        $this->db->from('staff_leave_request');
        $this->db->join('leave_types', 'leave_types.id = staff_leave_request.leave_type_id');
        $this->db->where('staff_leave_request.staff_id', $staff_id);
        $this->db->where('staff_leave_request.status', 'approve');
        $this->db->where('staff_leave_request.leave_from >=', $from_date);
        $this->db->where('staff_leave_request.leave_to <=', $to_date);
        $this->db->order_by('staff_leave_request.leave_from', 'DESC');
        $leave_records = $this->db->get()->result_array();

        $leave_summary = array();
        $leave_dates = array();

        foreach ($leave_records as $leave) {
            $leave_type = $leave['leave_type_name'];

            if (!isset($leave_summary[$leave_type])) {
                $leave_summary[$leave_type] = array(
                    'count' => 0,
                    'total_days' => 0,
                    'requests' => array()
                );
            }

            $leave_summary[$leave_type]['count']++;
            $leave_summary[$leave_type]['total_days'] += $leave['leave_days'];
            $leave_summary[$leave_type]['requests'][] = array(
                'leave_from' => $leave['leave_from'],
                'leave_to' => $leave['leave_to'],
                'leave_days' => $leave['leave_days'],
                'employee_remark' => $leave['employee_remark'],
                'admin_remark' => $leave['admin_remark'],
                'applied_date' => $leave['date']
            );

            // Generate individual leave dates
            $current_date = strtotime($leave['leave_from']);
            $end_date = strtotime($leave['leave_to']);

            while ($current_date <= $end_date) {
                $leave_dates[] = array(
                    'date' => date('Y-m-d', $current_date),
                    'leave_type' => $leave_type,
                    'remark' => $leave['employee_remark']
                );
                $current_date = strtotime('+1 day', $current_date);
            }
        }

        return array(
            'leave_summary' => $leave_summary,
            'leave_dates' => $leave_dates
        );
    }

    /**
     * Get the actual date range of attendance data for a staff member or all staff
     */
    private function getAttendanceDateRange($staff_id = null) {
        $this->db->select('MIN(date) as min_date, MAX(date) as max_date');
        $this->db->from('staff_attendance');

        if ($staff_id) {
            $this->db->where('staff_id', $staff_id);
        }

        $query = $this->db->get();
        $result = $query->row();

        return array(
            'min_date' => $result->min_date,
            'max_date' => $result->max_date
        );
    }

    /**
     * Validate date format
     */
    private function isValidDate($date) {
        if (empty($date)) {
            return false;
        }
        $d = date_create_from_format('Y-m-d', $date);
        return $d && date_format($d, 'Y-m-d') === $date;
    }

    /**
     * Get Available Staff Attendance Years
     * 
     * Retrieves distinct years that have staff attendance records in the system.
     * Returns years in descending order (newest first).
     * 
     * @return array Array of years with staff attendance records
     * 
     * @example Return format:
     * [
     *   {"year": "2025"},
     *   {"year": "2024"},
     *   {"year": "2023"}
     * ]
     */
    public function getStaffAttendanceYears()
    {
        $this->db->select('DISTINCT YEAR(date) as year');
        $this->db->from('staff_attendance');
        $this->db->where('date IS NOT NULL');
        $this->db->order_by('year', 'DESC');
        
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get Available Staff Attendance Years with Details
     * 
     * Retrieves distinct years with additional statistics including:
     * - Total staff attendance records for the year
     * - Total unique staff members with attendance in the year
     * - Earliest attendance date in the year
     * - Latest attendance date in the year
     * 
     * Returns years in descending order (newest first).
     * 
     * @return array Array of years with staff attendance statistics
     * 
     * @example Return format:
     * [
     *   {
     *     "year": "2025",
     *     "total_records": 8520,
     *     "total_staff": 45,
     *     "earliest_date": "2025-01-01",
     *     "latest_date": "2025-10-13"
     *   }
     * ]
     */
    public function getStaffAttendanceYearsWithDetails()
    {
        $sql = "SELECT 
                    YEAR(date) as year,
                    COUNT(*) as total_records,
                    COUNT(DISTINCT staff_id) as total_staff,
                    MIN(date) as earliest_date,
                    MAX(date) as latest_date
                FROM staff_attendance
                WHERE date IS NOT NULL
                GROUP BY YEAR(date)
                ORDER BY year DESC";
        
        $query = $this->db->query($sql);
        return $query->result_array();
    }

}
