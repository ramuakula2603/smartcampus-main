<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Fee Collection Filters Model
 * 
 * This model provides methods to retrieve filter options for fee collection reports.
 * It handles hierarchical relationships between sessions, classes, and sections.
 * 
 * @package    School Management System
 * @subpackage Models
 * @category   Database
 * @author     School Management System
 * @version    1.0.0
 */
class Fee_collection_filters_model extends CI_Model
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all sessions
     * 
     * Returns all academic sessions with their IDs and names.
     * 
     * @return array Array of session objects with id and session fields
     */
    public function get_sessions()
    {
        $this->db->select('id, session');
        $this->db->from('sessions');
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        
        $sessions = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $sessions[] = array(
                    'id' => $row['id'],
                    'name' => $row['session']
                );
            }
        }
        
        return $sessions;
    }

    /**
     * Get classes filtered by session
     *
     * If session_id is provided, returns classes for that specific session.
     * If session_id is null or empty, returns all classes across all sessions.
     *
     * @param int|null $session_id Optional session ID to filter classes
     * @return array Array of class objects with id and name fields
     */
    public function get_classes($session_id = null)
    {
        // Check if session_id is provided and is a valid numeric value
        if ($session_id !== null && $session_id !== '' && is_numeric($session_id) && $session_id > 0) {
            // Log for debugging
            log_message('debug', 'Fee Collection Filters: Filtering classes by session_id = ' . $session_id);

            // Get classes for specific session from student_session table
            $this->db->select('DISTINCT classes.id, classes.class as name');
            $this->db->from('student_session');
            $this->db->join('classes', 'student_session.class_id = classes.id');
            $this->db->where('student_session.session_id', $session_id);
            $this->db->order_by('classes.id', 'ASC');
        } else {
            // Log for debugging
            log_message('debug', 'Fee Collection Filters: Getting all classes (no session filter)');

            // Get all classes
            $this->db->select('id, class as name');
            $this->db->from('classes');
            $this->db->order_by('id', 'ASC');
        }

        $query = $this->db->get();

        $classes = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $classes[] = array(
                    'id' => $row['id'],
                    'name' => $row['name']
                );
            }
        }

        // Log result count
        log_message('debug', 'Fee Collection Filters: Found ' . count($classes) . ' classes');

        return $classes;
    }

    /**
     * Get sections filtered by class
     *
     * If class_id is provided, returns sections for that specific class.
     * If class_id is null or empty, returns all sections across all classes.
     *
     * @param int|null $class_id Optional class ID to filter sections
     * @return array Array of section objects with id and name fields
     */
    public function get_sections($class_id = null)
    {
        // Check if class_id is provided and is a valid numeric value
        if ($class_id !== null && $class_id !== '' && is_numeric($class_id) && $class_id > 0) {
            // Log for debugging
            log_message('debug', 'Fee Collection Filters: Filtering sections by class_id = ' . $class_id);

            // Get sections for specific class from class_sections table
            $this->db->select('sections.id, sections.section as name');
            $this->db->from('class_sections');
            $this->db->join('sections', 'class_sections.section_id = sections.id');
            $this->db->where('class_sections.class_id', $class_id);
            $this->db->order_by('sections.id', 'ASC');
        } else {
            // Log for debugging
            log_message('debug', 'Fee Collection Filters: Getting all sections (no class filter)');

            // Get all sections
            $this->db->select('id, section as name');
            $this->db->from('sections');
            $this->db->order_by('id', 'ASC');
        }

        $query = $this->db->get();

        $sections = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $sections[] = array(
                    'id' => $row['id'],
                    'name' => $row['name']
                );
            }
        }

        // Log result count
        log_message('debug', 'Fee Collection Filters: Found ' . count($sections) . ' sections');

        return $sections;
    }

    /**
     * Get all fee groups
     * 
     * Returns all fee groups with their IDs and names.
     * Excludes system fee groups (is_system = 1).
     * 
     * @return array Array of fee group objects with id and name fields
     */
    public function get_fee_groups()
    {
        $this->db->select('id, name');
        $this->db->from('fee_groups');
        $this->db->where('is_system', 0);
        $this->db->order_by('id', 'ASC');
        $query = $this->db->get();
        
        $fee_groups = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $fee_groups[] = array(
                    'id' => $row['id'],
                    'name' => $row['name']
                );
            }
        }
        
        return $fee_groups;
    }

    /**
     * Get all fee types
     * 
     * Returns all fee types with their IDs and names.
     * Excludes system fee types (is_system = 1).
     * Returns all fee types regardless of session.
     * 
     * @return array Array of fee type objects with id and name fields
     */
    public function get_fee_types()
    {
        $this->db->select('id, type as name, code');
        $this->db->from('feetype');
        $this->db->where('is_system', 0);
        $this->db->order_by('id', 'ASC');
        $query = $this->db->get();
        
        $fee_types = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $fee_types[] = array(
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'code' => $row['code']
                );
            }
        }
        
        return $fee_types;
    }

    /**
     * Get staff members who can collect fees
     *
     * Returns all active staff members with their IDs and names.
     * These staff members can be used as fee collectors.
     *
     * @return array Array of staff objects with id and name fields
     */
    public function get_staff_collectors()
    {
        $this->db->select('staff.id, staff.name, staff.surname, staff.employee_id');
        $this->db->from('staff');
        $this->db->where('staff.is_active', 1);
        $this->db->order_by('staff.id', 'ASC');
        $query = $this->db->get();

        $staff = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $full_name = trim($row['name'] . ' ' . $row['surname']);
                $staff[] = array(
                    'id' => $row['id'],
                    'name' => $full_name,
                    'employee_id' => $row['employee_id']
                );
            }
        }

        return $staff;
    }

    /**
     * Get hierarchical academic data structure
     *
     * Returns sessions with nested classes, sections, and optionally students.
     * Supports filtering by session_id, class_id, and section_id.
     *
     * @param int|null $session_id Optional session ID to filter
     * @param int|null $class_id Optional class ID to filter
     * @param int|null $section_id Optional section ID to filter
     * @param bool $include_students Whether to include student details
     * @return array Hierarchical array of sessions with nested data
     */
    public function get_hierarchical_data($session_id = null, $class_id = null, $section_id = null, $include_students = false)
    {
        $result = array();

        // Get sessions
        $sessions_query = $this->db->select('id, session')
            ->from('sessions')
            ->order_by('id', 'DESC');

        if ($session_id !== null && $session_id !== '' && is_numeric($session_id) && $session_id > 0) {
            $sessions_query->where('id', $session_id);
        }

        $sessions = $sessions_query->get()->result_array();

        foreach ($sessions as $session) {
            $session_data = array(
                'id' => $session['id'],
                'name' => $session['session'],
                'classes' => array()
            );

            // Get classes for this session
            $this->db->distinct();
            $this->db->select('classes.id, classes.class as name');
            $this->db->from('student_session');
            $this->db->join('classes', 'student_session.class_id = classes.id');
            $this->db->where('student_session.session_id', $session['id']);

            if ($class_id !== null && $class_id !== '' && is_numeric($class_id) && $class_id > 0) {
                $this->db->where('classes.id', $class_id);
            }

            $this->db->order_by('classes.id', 'ASC');
            $classes = $this->db->get()->result_array();

            foreach ($classes as $class) {
                $class_data = array(
                    'id' => $class['id'],
                    'name' => $class['name'],
                    'sections' => array()
                );

                // Get sections for this class
                $this->db->distinct();
                $this->db->select('sections.id, sections.section as name');
                $this->db->from('student_session');
                $this->db->join('sections', 'student_session.section_id = sections.id');
                $this->db->where('student_session.session_id', $session['id']);
                $this->db->where('student_session.class_id', $class['id']);

                if ($section_id !== null && $section_id !== '' && is_numeric($section_id) && $section_id > 0) {
                    $this->db->where('sections.id', $section_id);
                }

                $this->db->order_by('sections.id', 'ASC');
                $sections = $this->db->get()->result_array();

                foreach ($sections as $section) {
                    $section_data = array(
                        'id' => $section['id'],
                        'name' => $section['name']
                    );

                    // Include students if requested
                    if ($include_students) {
                        $section_data['students'] = $this->get_students_by_section(
                            $session['id'],
                            $class['id'],
                            $section['id']
                        );
                    }

                    $class_data['sections'][] = $section_data;
                }

                $session_data['classes'][] = $class_data;
            }

            $result[] = $session_data;
        }

        return $result;
    }

    /**
     * Get students enrolled in a specific section
     *
     * @param int $session_id Session ID
     * @param int $class_id Class ID
     * @param int $section_id Section ID
     * @return array Array of student objects
     */
    public function get_students_by_section($session_id, $class_id, $section_id)
    {
        $this->db->select('
            students.id,
            students.admission_no,
            students.roll_no,
            students.firstname,
            students.middlename,
            students.lastname,
            students.father_name,
            students.dob,
            students.gender,
            students.mobileno,
            students.email,
            students.guardian_name,
            students.guardian_phone,
            students.is_active
        ');
        $this->db->from('student_session');
        $this->db->join('students', 'student_session.student_id = students.id');
        $this->db->where('student_session.session_id', $session_id);
        $this->db->where('student_session.class_id', $class_id);
        $this->db->where('student_session.section_id', $section_id);
        $this->db->where('students.is_active', 'yes');
        $this->db->order_by('students.admission_no', 'ASC');

        $query = $this->db->get();

        $students = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $full_name = trim($row['firstname'] . ' ' . $row['middlename'] . ' ' . $row['lastname']);
                $students[] = array(
                    'id' => $row['id'],
                    'admission_no' => $row['admission_no'],
                    'roll_no' => $row['roll_no'],
                    'full_name' => $full_name,
                    'firstname' => $row['firstname'],
                    'middlename' => $row['middlename'],
                    'lastname' => $row['lastname'],
                    'father_name' => $row['father_name'],
                    'dob' => $row['dob'],
                    'gender' => $row['gender'],
                    'mobileno' => $row['mobileno'],
                    'email' => $row['email'],
                    'guardian_name' => $row['guardian_name'],
                    'guardian_phone' => $row['guardian_phone'],
                    'is_active' => $row['is_active']
                );
            }
        }

        return $students;
    }
}

