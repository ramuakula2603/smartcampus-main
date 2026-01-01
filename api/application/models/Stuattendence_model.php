<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Stuattendence_model extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
        $this->current_date = $this->setting_model->getDateYmd();
    }

    /**
     * Get Daily Attendance Report by Filters
     * 
     * Retrieves daily attendance statistics grouped by class and section.
     * Returns aggregated counts for different attendance types (present, absent, late, excuse, half_day).
     * Handles null/empty parameters gracefully.
     * 
     * @param string $date       Specific date for attendance (or null)
     * @param string $from_date  Start date for date range filter (or null)
     * @param string $to_date    End date for date range filter (or null)
     * @param int    $session_id Session ID (defaults to current session if not provided)
     * @return array Array of attendance records grouped by class and section
     */
    public function getDailyAttendanceReportByFilters($date = null, $from_date = null, $to_date = null, $session_id = null)
    {
        // If session_id is not provided, use current session
        if (empty($session_id)) {
            $session_id = $this->current_session;
        }

        // Build date condition
        $date_condition = '';
        if (!empty($date)) {
            $date_condition = " AND `student_attendences`.`date` = " . $this->db->escape($date);
        } elseif (!empty($from_date) && !empty($to_date)) {
            $date_condition = " AND `student_attendences`.`date` BETWEEN " . $this->db->escape($from_date) . " AND " . $this->db->escape($to_date);
        } elseif (!empty($from_date)) {
            $date_condition = " AND `student_attendences`.`date` >= " . $this->db->escape($from_date);
        } elseif (!empty($to_date)) {
            $date_condition = " AND `student_attendences`.`date` <= " . $this->db->escape($to_date);
        }

        // Build SQL query
        $sql = 'SELECT 
                    classes.id as class_id,
                    classes.class as class_name,
                    sections.id as section_id,
                    sections.section as section_name, 
                    SUM(CASE WHEN `attendence_type_id` = 1 THEN 1 ELSE 0 END) AS "present",
                    SUM(CASE WHEN `attendence_type_id` = 2 THEN 1 ELSE 0 END) AS "excuse",
                    SUM(CASE WHEN `attendence_type_id` = 4 THEN 1 ELSE 0 END) AS "absent",
                    SUM(CASE WHEN `attendence_type_id` = 3 THEN 1 ELSE 0 END) AS "late",
                    SUM(CASE WHEN `attendence_type_id` = 6 THEN 1 ELSE 0 END) AS "half_day" 
                FROM `student_attendences` 
                JOIN student_session ON student_attendences.student_session_id = student_session.id 
                INNER JOIN class_sections ON (student_session.class_id = class_sections.class_id AND student_session.section_id = class_sections.section_id) 
                INNER JOIN classes ON classes.id = class_sections.class_id 
                INNER JOIN sections ON sections.id = class_sections.section_id 
                WHERE `student_session`.`session_id` = ' . intval($session_id) . ' ' . $date_condition . ' 
                GROUP BY class_sections.id
                ORDER BY classes.id, sections.id';

        $query = $this->db->query($sql);
        $results = $query->result_array();

        // Calculate percentages for each record
        foreach ($results as $key => $row) {
            $total_present = $row['present'] + $row['excuse'] + $row['late'] + $row['half_day'];
            $total_student = $total_present + $row['absent'];
            
            if ($total_present > 0 && $total_student > 0) {
                $present_percent = round(($total_present / $total_student) * 100);
            } else {
                $present_percent = 0;
            }
            
            if ($row['absent'] > 0 && $total_student > 0) {
                $absent_percent = round(($row['absent'] / $total_student) * 100);
            } else {
                $absent_percent = 0;
            }

            $results[$key]['total_student'] = $total_student;
            $results[$key]['total_present'] = $total_present;
            $results[$key]['present_percent'] = $present_percent . '%';
            $results[$key]['absent_percent'] = $absent_percent . '%';
        }

        return $results;
    }

    /**
     * Get Biometric Attendance Log by Filters
     * 
     * Retrieves biometric attendance log records with student details.
     * Supports pagination and filtering.
     * 
     * @param string $from_date  Start date for date range filter (or null)
     * @param string $to_date    End date for date range filter (or null)
     * @param int    $student_id Student ID filter (or null)
     * @param int    $limit      Limit for pagination (or null)
     * @param int    $offset     Offset for pagination (or null)
     * @return array Array of biometric attendance log records
     */
    public function getBiometricAttlogReportByFilters($from_date = null, $to_date = null, $student_id = null, $limit = null, $offset = null)
    {
        $this->db->select('student_attendences.*, 
                          CONCAT_WS(" ", students.firstname, students.lastname) as name,
                          students.firstname,
                          students.middlename,
                          students.lastname,
                          students.roll_no,
                          students.admission_no,
                          classes.class,
                          sections.section');
        $this->db->from('student_attendences');
        $this->db->join('student_session', 'student_session.id = student_attendences.student_session_id', 'left');
        $this->db->join('students', 'student_session.student_id = students.id', 'left');
        $this->db->join('classes', 'student_session.class_id = classes.id', 'left');
        $this->db->join('sections', 'student_session.section_id = sections.id', 'left');
        $this->db->where('biometric_attendence', 1);

        // Apply date range filter if provided
        if (!empty($from_date) && !empty($to_date)) {
            $this->db->where('student_attendences.date >=', $from_date);
            $this->db->where('student_attendences.date <=', $to_date);
        } elseif (!empty($from_date)) {
            $this->db->where('student_attendences.date >=', $from_date);
        } elseif (!empty($to_date)) {
            $this->db->where('student_attendences.date <=', $to_date);
        }

        // Apply student filter if provided
        if (!empty($student_id)) {
            if (is_array($student_id) && count($student_id) > 0) {
                $this->db->where_in('students.id', $student_id);
            } else {
                $this->db->where('students.id', $student_id);
            }
        }

        // Apply pagination if provided
        if ($limit !== null) {
            $this->db->limit($limit, $offset);
        }

        $this->db->order_by('student_attendences.date', 'DESC');
        $this->db->order_by('student_attendences.id', 'DESC');

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Count Biometric Attendance Log Records
     * 
     * Counts total biometric attendance log records with optional filters.
     * 
     * @param string $from_date  Start date for date range filter (or null)
     * @param string $to_date    End date for date range filter (or null)
     * @param int    $student_id Student ID filter (or null)
     * @return int Total count of records
     */
    public function countBiometricAttlogReportByFilters($from_date = null, $to_date = null, $student_id = null)
    {
        $this->db->select('count(*) as total');
        $this->db->from('student_attendences');
        $this->db->join('student_session', 'student_session.id = student_attendences.student_session_id', 'left');
        $this->db->join('students', 'student_session.student_id = students.id', 'left');
        $this->db->where('biometric_attendence', 1);

        // Apply date range filter if provided
        if (!empty($from_date) && !empty($to_date)) {
            $this->db->where('student_attendences.date >=', $from_date);
            $this->db->where('student_attendences.date <=', $to_date);
        } elseif (!empty($from_date)) {
            $this->db->where('student_attendences.date >=', $from_date);
        } elseif (!empty($to_date)) {
            $this->db->where('student_attendences.date <=', $to_date);
        }

        // Apply student filter if provided
        if (!empty($student_id)) {
            if (is_array($student_id) && count($student_id) > 0) {
                $this->db->where_in('students.id', $student_id);
            } else {
                $this->db->where('students.id', $student_id);
            }
        }

        $query = $this->db->get();
        $count = $query->row_array();
        return $count['total'];
    }

    /**
     * Get Attendance Report by Filters
     * 
     * Retrieves detailed student attendance records with student information.
     * Supports filtering by class, section, and date range.
     * 
     * @param mixed  $class_id   Class ID (single value, array, or null)
     * @param mixed  $section_id Section ID (single value, array, or null)
     * @param string $from_date  Start date for date range filter (or null)
     * @param string $to_date    End date for date range filter (or null)
     * @param int    $session_id Session ID (defaults to current session if not provided)
     * @return array Array of student attendance records
     */
    public function getAttendanceReportByFilters($class_id = null, $section_id = null, $from_date = null, $to_date = null, $session_id = null)
    {
        // If session_id is not provided, use current session
        if (empty($session_id)) {
            $session_id = $this->current_session;
        }

        // Build condition for class and section
        $condition = '';
        if ($class_id !== null && !empty($class_id)) {
            if (is_array($class_id) && count($class_id) > 0) {
                $class_ids = implode(',', array_map('intval', $class_id));
                $condition .= " AND `student_session`.`class_id` IN (" . $class_ids . ")";
            } elseif (!is_array($class_id)) {
                $condition .= " AND `student_session`.`class_id` = " . intval($class_id);
            }
        }

        if ($section_id !== null && !empty($section_id)) {
            if (is_array($section_id) && count($section_id) > 0) {
                $section_ids = implode(',', array_map('intval', $section_id));
                $condition .= " AND `student_session`.`section_id` IN (" . $section_ids . ")";
            } elseif (!is_array($section_id)) {
                $condition .= " AND `student_session`.`section_id` = " . intval($section_id);
            }
        }

        // Build date condition
        $date_condition = '';
        if (!empty($from_date) && !empty($to_date)) {
            $date_condition = " AND `student_attendences`.`date` BETWEEN " . $this->db->escape($from_date) . " AND " . $this->db->escape($to_date);
        } elseif (!empty($from_date)) {
            $date_condition = " AND `student_attendences`.`date` >= " . $this->db->escape($from_date);
        } elseif (!empty($to_date)) {
            $date_condition = " AND `student_attendences`.`date` <= " . $this->db->escape($to_date);
        }

        // Build SQL query
        $sql = "SELECT 
                    `classes`.`id` AS `class_id`, 
                    `students`.`id`, 
                    `classes`.`class`, 
                    `sections`.`id` AS `section_id`, 
                    `sections`.`section`, 
                    `students`.`admission_no`, 
                    `students`.`roll_no`, 
                    `students`.`firstname`,
                    students.middlename, 
                    `students`.`lastname`, 
                    `students`.`gender`, 
                    `student_session`.`session_id`,
                    `date`, 
                    count(student_attendences.id) as total_attendance 
                FROM `student_attendences` 
                INNER JOIN `student_session` ON `student_session`.`id` = `student_attendences`.`student_session_id` 
                INNER JOIN `students` ON `student_session`.`student_id` = `students`.`id` 
                JOIN `classes` ON `student_session`.`class_id` = `classes`.`id` 
                JOIN `sections` ON `sections`.`id` = `student_session`.`section_id` 
                WHERE `student_session`.`session_id` = " . intval($session_id) . " 
                AND `students`.`is_active` = 'yes' " . $condition . $date_condition . " 
                GROUP BY students.id  
                ORDER BY `students`.`id`";

        $query = $this->db->query($sql);
        return $query->result_array();
    }

    /**
     * Get Class Attendance Report by Filters
     *
     * Retrieves monthly class attendance statistics with detailed attendance breakdown.
     * Returns student-wise attendance data for a specific month and year.
     *
     * @param mixed  $class_id   Class ID (single value, array, or null)
     * @param mixed  $section_id Section ID (single value, array, or null)
     * @param int    $month      Month number (1-12)
     * @param int    $year       Year (YYYY format)
     * @param int    $session_id Session ID (defaults to current session if not provided)
     * @return array Array of class attendance records with monthly statistics
     */
    public function getClassAttendanceReportByFilters($class_id = null, $section_id = null, $month = null, $year = null, $session_id = null)
    {
        // If session_id is not provided, use current session
        if (empty($session_id)) {
            $session_id = $this->current_session;
        }

        // If month and year not provided, use current month and year
        if (empty($month)) {
            $month = date('m');
        }
        if (empty($year)) {
            $year = date('Y');
        }

        // Build condition for class and section
        $condition = '';
        if ($class_id !== null && !empty($class_id)) {
            if (is_array($class_id) && count($class_id) > 0) {
                $class_ids = implode(',', array_map('intval', $class_id));
                $condition .= " AND `student_session`.`class_id` IN (" . $class_ids . ")";
            } elseif (!is_array($class_id)) {
                $condition .= " AND `student_session`.`class_id` = " . intval($class_id);
            }
        }

        if ($section_id !== null && !empty($section_id)) {
            if (is_array($section_id) && count($section_id) > 0) {
                $section_ids = implode(',', array_map('intval', $section_id));
                $condition .= " AND `student_session`.`section_id` IN (" . $section_ids . ")";
            } elseif (!is_array($section_id)) {
                $condition .= " AND `student_session`.`section_id` = " . intval($section_id);
            }
        }

        // Build SQL query for monthly attendance
        $sql = "SELECT
                    `students`.`id` as student_id,
                    `students`.`admission_no`,
                    `students`.`roll_no`,
                    `students`.`firstname`,
                    `students`.`middlename`,
                    `students`.`lastname`,
                    `students`.`gender`,
                    `classes`.`id` AS `class_id`,
                    `classes`.`class`,
                    `sections`.`id` AS `section_id`,
                    `sections`.`section`,
                    COUNT(CASE WHEN `student_attendences`.`attendence_type_id` = 1 THEN 1 END) as present_count,
                    COUNT(CASE WHEN `student_attendences`.`attendence_type_id` = 2 THEN 1 END) as excuse_count,
                    COUNT(CASE WHEN `student_attendences`.`attendence_type_id` = 3 THEN 1 END) as late_count,
                    COUNT(CASE WHEN `student_attendences`.`attendence_type_id` = 4 THEN 1 END) as absent_count,
                    COUNT(CASE WHEN `student_attendences`.`attendence_type_id` = 6 THEN 1 END) as half_day_count,
                    COUNT(`student_attendences`.`id`) as total_days
                FROM `student_session`
                INNER JOIN `students` ON `student_session`.`student_id` = `students`.`id`
                INNER JOIN `classes` ON `student_session`.`class_id` = `classes`.`id`
                INNER JOIN `sections` ON `student_session`.`section_id` = `sections`.`id`
                LEFT JOIN `student_attendences` ON `student_attendences`.`student_session_id` = `student_session`.`id`
                    AND MONTH(`student_attendences`.`date`) = " . intval($month) . "
                    AND YEAR(`student_attendences`.`date`) = " . intval($year) . "
                WHERE `student_session`.`session_id` = " . intval($session_id) . "
                AND `students`.`is_active` = 'yes' " . $condition . "
                GROUP BY `students`.`id`
                ORDER BY `classes`.`id`, `sections`.`id`, `students`.`id`";

        $query = $this->db->query($sql);
        $results = $query->result_array();

        // Calculate attendance percentage for each student
        foreach ($results as $key => $row) {
            $total_present = $row['present_count'] + $row['excuse_count'] + $row['late_count'] + $row['half_day_count'];
            $total_days = $row['total_days'];

            if ($total_days > 0) {
                $attendance_percentage = round(($total_present / $total_days) * 100, 2);
            } else {
                $attendance_percentage = 0;
            }

            $results[$key]['total_present'] = $total_present;
            $results[$key]['attendance_percentage'] = $attendance_percentage . '%';
        }

        return $results;
    }

    /**
     * Get Available Attendance Years
     * 
     * Retrieves distinct years that have attendance records in the system.
     * Returns years in descending order (newest first).
     * 
     * @return array Array of years with attendance records
     * 
     * @example Return format:
     * [
     *   {"year": "2025"},
     *   {"year": "2024"},
     *   {"year": "2023"}
     * ]
     */
    public function getAttendanceYears()
    {
        $this->db->select('DISTINCT YEAR(date) as year');
        $this->db->from('student_attendences');
        $this->db->where('date IS NOT NULL');
        $this->db->order_by('year', 'DESC');
        
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get Available Attendance Years with Details
     * 
     * Retrieves distinct years with additional statistics including:
     * - Total attendance records for the year
     * - Earliest attendance date in the year
     * - Latest attendance date in the year
     * 
     * Returns years in descending order (newest first).
     * 
     * @return array Array of years with attendance statistics
     * 
     * @example Return format:
     * [
     *   {
     *     "year": "2025",
     *     "total_records": 15420,
     *     "earliest_date": "2025-01-01",
     *     "latest_date": "2025-10-13"
     *   }
     * ]
     */
    public function getAttendanceYearsWithDetails()
    {
        $sql = "SELECT 
                    YEAR(date) as year,
                    COUNT(*) as total_records,
                    MIN(date) as earliest_date,
                    MAX(date) as latest_date
                FROM student_attendences
                WHERE date IS NOT NULL
                GROUP BY YEAR(date)
                ORDER BY year DESC";
        
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    /**
     * Get Student Attendance Type Report
     *
     * Retrieves students who have a specific attendance type within the given date range and filters.
     * This method is used by the Student Attendance Type Report API to get students with specific
     * attendance types (Present, Absent, Late, Excuse, Half Day, Holiday) during a date range.
     *
     * @param string $condition SQL condition string with attendance type, class, section, and date filters
     * @param int    $session_id Session ID (if null, returns data from all sessions)
     * @return array Array of student records with attendance type count
     */
    public function getStudentAttendanceTypeReport($condition = '', $session_id = null)
    {
        // Build session condition - if session_id is provided, filter by it; otherwise get all sessions
        $session_condition = '';
        if (!empty($session_id)) {
            $session_condition = "AND `student_session`.`session_id` = " . intval($session_id) . " ";
        }

        // Build SQL query to get students with specific attendance type
        $sql = "SELECT 
                    `classes`.`id` AS `class_id`, 
                    `students`.`id`, 
                    `classes`.`class`, 
                    `sections`.`id` AS `section_id`, 
                    `sections`.`section`, 
                    `students`.`admission_no`, 
                    `students`.`roll_no`, 
                    `students`.`admission_date`,
                    `students`.`firstname`,
                    students.middlename, 
                    `students`.`lastname`, 
                    `students`.`mobileno`, 
                    `students`.`email`, 
                    `students`.`dob`, 
                    `students`.`father_name`, 
                    `students`.`gender`, 
                    `student_session`.`session_id`,
                    COUNT(student_attendences.id) as total_type 
                FROM `student_attendences` 
                INNER JOIN `student_session` ON `student_session`.`id` = `student_attendences`.`student_session_id` 
                INNER JOIN `students` ON `student_session`.`student_id` = `students`.`id` 
                JOIN `classes` ON `student_session`.`class_id` = `classes`.`id` 
                JOIN `sections` ON `sections`.`id` = `student_session`.`section_id` 
                WHERE `students`.`is_active` = 'yes' " . $session_condition . $condition . " 
                GROUP BY students.id  
                ORDER BY `students`.`id`";

        $query = $this->db->query($sql);
        return $query->result_array();
    }
}

