<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Student_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $CI = &get_instance();
        $CI->load->model('setting_model');
        $this->current_session = $this->setting_model->getCurrentSession();
        $this->current_date    = $this->setting_model->getDateYmd();
    }

  public function getStudentByClassSectionID($class_id = null, $section_id = null, $id = null, $session_id=null)
    {
        if($session_id != ""){
           $session_id= $session_id;
        }else{
            $session_id=$this->current_session;
        }

        $this->db->select('pickup_point.name as pickup_point_name,student_session.route_pickup_point_id,student_session.vehroute_id,vehicle_routes.route_id,vehicle_routes.vehicle_id,transport_route.route_title,vehicles.vehicle_no,hostel_rooms.room_no,vehicles.driver_name,vehicles.driver_contact,hostel.id as `hostel_id`,hostel.hostel_name,room_types.id as `room_type_id`,room_types.room_type ,students.hostel_room_id,student_session.id as `student_session_id`,student_session.fees_discount,classes.id AS `class_id`,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.admission_date,students.firstname, students.middlename, students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode , students.note, students.religion, students.cast, school_houses.house_name,   students.dob ,students.current_address, students.previous_school,
            students.guardian_is,students.parent_id,
            students.permanent_address,students.category_id,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.father_pic ,students.height ,students.weight,students.measurement_date, students.mother_pic , students.guardian_pic , students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.father_phone,students.blood_group,students.school_house_id,students.father_occupation,students.mother_name,students.mother_phone,students.mother_occupation,students.guardian_occupation,students.gender,students.guardian_is,students.rte,students.guardian_email,sessions.session, users.username,users.password,students.dis_reason,students.dis_note,students.app_key,students.parent_app_key')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('sessions', 'sessions.id = student_session.session_id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id');
        $this->db->join('hostel_rooms', 'hostel_rooms.id = students.hostel_room_id', 'left');
        $this->db->join('hostel', 'hostel.id = hostel_rooms.hostel_id', 'left');
        $this->db->join('room_types', 'room_types.id = hostel_rooms.room_type_id', 'left');       
        $this->db->join('school_houses', 'school_houses.id = students.school_house_id', 'left');
        $this->db->join('users', 'users.user_id = students.id', 'left');
        $this->db->join('route_pickup_point', 'route_pickup_point.id = student_session.route_pickup_point_id', 'left');
        $this->db->join('pickup_point', 'route_pickup_point.pickup_point_id = pickup_point.id', 'left');
        $this->db->join('transport_route', 'route_pickup_point.transport_route_id = transport_route.id', 'left');
        $this->db->join('vehicle_routes', 'vehicle_routes.id = student_session.vehroute_id', 'left');
        $this->db->join('vehicles', 'vehicles.id = vehicle_routes.vehicle_id', 'left');
        $this->db->where('student_session.class_id', $class_id);
        $this->db->where('student_session.section_id', $section_id);
        $this->db->where('student_session.session_id', $session_id);
        $this->db->where('users.role', 'student');

        if ($id != null) {
            $this->db->where('students.id', $id);
        } else {
            $this->db->where('students.is_active', 'yes');
            $this->db->order_by('students.id', 'desc');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    public function read_siblings_students($parent_id)
    {
        $this->db->select('students.*,classes.id AS `class_id`,classes.class,sections.id AS `section_id`,sections.section')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id');
        $this->db->where('student_session.session_id', $this->current_session);
        $this->db->where('parent_id', $parent_id);
        $this->db->where('students.is_active', 'yes');
        $this->db->group_by('students.id');
        $query = $this->db->get();
        return $query->result();
    }

    public function get($id = null)
    {  
        $this->db->select('pickup_point.name as pickup_point_name,IFNULL(student_session.route_pickup_point_id,0) as `route_pickup_point_id`,student_session.transport_fees,students.app_key,students.parent_app_key,student_session.vehroute_id,vehicle_routes.route_id,vehicle_routes.vehicle_id,transport_route.route_title,vehicles.vehicle_no,hostel_rooms.room_no,vehicles.driver_name,vehicles.driver_contact,vehicles.vehicle_model,vehicles.manufacture_year,vehicles.driver_licence,vehicles.vehicle_photo,hostel.id as `hostel_id`,hostel.hostel_name,room_types.id as `room_type_id`,room_types.room_type ,students.hostel_room_id,student_session.id as `student_session_id`,student_session.fees_discount,classes.id AS `class_id`,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.admission_no,students.admission_date,students.firstname,students.middlename,  students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode , students.note, students.religion, students.cast, school_houses.house_name,   students.dob ,students.current_address, students.previous_school,
            students.guardian_is,students.parent_id,
            students.permanent_address,students.category_id,categories.category,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.father_pic ,students.height ,students.weight,students.measurement_date, students.mother_pic , students.guardian_pic , students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.father_phone,students.blood_group,students.school_house_id,students.father_occupation,students.mother_name,students.mother_phone,students.mother_occupation,students.guardian_occupation,students.gender,students.guardian_is,students.rte,students.guardian_email, users.username,users.password,students.dis_reason,students.dis_note,students.disable_at,IFNULL(currencies.short_name,0) as currency_name,IFNULL(currencies.symbol,0) as symbol,IFNULL(currencies.base_price,0) as base_price,IFNULL(currencies.id,0) as `currency_id`, student_session.session_id,sessions.session')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id');
        $this->db->join('hostel_rooms', 'hostel_rooms.id = students.hostel_room_id', 'left');
        $this->db->join('hostel', 'hostel.id = hostel_rooms.hostel_id', 'left');
        $this->db->join('room_types', 'room_types.id = hostel_rooms.room_type_id', 'left');
        $this->db->join('route_pickup_point', 'route_pickup_point.id = student_session.route_pickup_point_id', 'left');
        $this->db->join('pickup_point', 'route_pickup_point.pickup_point_id = pickup_point.id', 'left');
        $this->db->join('vehicle_routes', 'vehicle_routes.id = student_session.vehroute_id', 'left');
        $this->db->join('transport_route', 'vehicle_routes.route_id = transport_route.id', 'left');
        $this->db->join('vehicles', 'vehicles.id = vehicle_routes.vehicle_id', 'left');
        $this->db->join('school_houses', 'school_houses.id = students.school_house_id', 'left');
        $this->db->join('users', 'users.user_id = students.id', 'left');
         $this->db->join('currencies', 'currencies.id=users.currency_id', 'left');
        $this->db->join('categories', 'categories.id = students.category_id', 'left');
        $this->db->join('sessions', 'sessions.id = student_session.session_id', 'left');
        
        $this->db->where('student_session.session_id', $this->current_session);
        $this->db->where('users.role', 'student');
        if ($id != null) {
            $this->db->where('students.id', $id);
        } else {
            $this->db->where('students.is_active', 'yes');
            $this->db->order_by('students.id', 'desc');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_object();
        } else {
            return $query->result_array();
        }
    }

    public function getStudentSession($id)
    {
        $query = $this->db->query("SELECT  max(sessions.id) as student_session_id, max(sessions.session) as session from sessions join student_session on (sessions.id = student_session.session_id)  where student_session.student_id = " . $id);
        return $query->row_array();
    }

    public function getRecentRecord($id = null)
    {
        $this->db->select('classes.id AS `class_id`,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.admission_date,students.firstname,  students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode ,     students.religion,     students.dob ,students.current_address,    students.permanent_address,students.category_id,    students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.father_phone,students.father_occupation,students.mother_name,students.mother_phone,students.mother_occupation,students.guardian_occupation,students.gender,students.guardian_is')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id');
        $this->db->where('student_session.session_id', $this->current_session);
        if ($id != null) {
            $this->db->where('students.id', $id);
        } else {

        }
        $this->db->order_by('students.id', 'desc');
        $this->db->limit(5);
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    public function getstudentdoc($id)
    {
        $this->db->select()->from('student_doc');
        $this->db->where('student_id', $id);
        $query = $this->db->get();
        return $query->result();
    }

    public function add($data)
    {
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('students', $data);
        }
    }

    public function adddoc($data)
    {
        $this->db->insert('student_doc', $data);
        return $this->db->insert_id();
    }

    public function updatestudentlanguage($data)
    {
        if (isset($data['user_id'])) {
            $this->db->where('user_id', $data['user_id']);
            $this->db->update('users', $data);
        }
    }

    /**
     * Get student report data by filters
     *
     * Retrieves student report data based on optional filter parameters.
     * Handles null/empty parameters gracefully by returning all records when filters are not provided.
     *
     * @param mixed $class_id Class ID or array of class IDs (optional)
     * @param mixed $section_id Section ID or array of section IDs (optional)
     * @param mixed $category_id Category ID or array of category IDs (optional)
     * @param int $session_id Session ID (optional, defaults to current session)
     * @return array Array of student records
     */
    public function getStudentReportByFilters($class_id = null, $section_id = null, $category_id = null, $session_id = null)
    {
        // Use current session if not provided
        if (empty($session_id)) {
            $session_id = $this->current_session;
        }

        // Build the query
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
            students.samagra_id,
            students.adhar_no,
            students.rte,
            students.guardian_name,
            students.guardian_phone,
            students.guardian_relation,
            students.current_address,
            students.permanent_address,
            students.is_active,
            classes.id AS class_id,
            classes.class,
            sections.id AS section_id,
            sections.section,
            students.category_id,
            categories.category
        ');
        $this->db->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id');
        $this->db->join('categories', 'students.category_id = categories.id', 'left');

        // Apply session filter
        $this->db->where('student_session.session_id', $session_id);

        // Apply active status filter
        $this->db->where('students.is_active', 'yes');

        // Apply class filter if provided
        if ($class_id !== null && !empty($class_id)) {
            if (is_array($class_id) && count($class_id) > 0) {
                $this->db->where_in('student_session.class_id', $class_id);
            } else {
                $this->db->where('student_session.class_id', $class_id);
            }
        }

        // Apply section filter if provided
        if ($section_id !== null && !empty($section_id)) {
            if (is_array($section_id) && count($section_id) > 0) {
                $this->db->where_in('student_session.section_id', $section_id);
            } else {
                $this->db->where('student_session.section_id', $section_id);
            }
        }

        // Apply category filter if provided
        if ($category_id !== null && !empty($category_id)) {
            if (is_array($category_id) && count($category_id) > 0) {
                $this->db->where_in('students.category_id', $category_id);
            } else {
                $this->db->where('students.category_id', $category_id);
            }
        }

        // Order by admission number
        $this->db->order_by('students.admission_no', 'asc');

        // Execute query
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get Guardian Report by Filters
     *
     * Retrieves guardian report data with optional filtering by class, section, and session.
     * Handles null/empty parameters gracefully by returning all records when filters are not provided.
     * Supports both single values and arrays for multi-select functionality.
     *
     * @param mixed $class_id    Class ID (single value, array, or null)
     * @param mixed $section_id  Section ID (single value, array, or null)
     * @param int   $session_id  Session ID (defaults to current session if not provided)
     * @return array Array of student records with guardian information
     */
    public function getGuardianReportByFilters($class_id = null, $section_id = null, $session_id = null)
    {
        // If session_id is not provided, use current session
        if (empty($session_id)) {
            $session_id = $this->setting_model->getCurrentSession();
        }

        // Start building the query
        $this->db->select('
            students.id,
            students.admission_no,
            students.firstname,
            students.middlename,
            students.lastname,
            students.mobileno,
            students.guardian_name,
            students.guardian_relation,
            students.guardian_phone,
            students.father_name,
            students.father_phone,
            students.mother_name,
            students.mother_phone,
            students.is_active,
            student_session.class_id,
            student_session.section_id,
            classes.class,
            sections.section
        ');

        // Join with student_session table
        $this->db->join('student_session', 'students.id = student_session.student_id', 'inner');

        // Join with classes table
        $this->db->join('classes', 'student_session.class_id = classes.id', 'inner');

        // Join with sections table
        $this->db->join('sections', 'student_session.section_id = sections.id', 'inner');

        // Apply session filter
        $this->db->where('student_session.session_id', $session_id);

        // Apply active status filter
        $this->db->where('students.is_active', 'yes');

        // Apply class filter if provided
        if ($class_id !== null && !empty($class_id)) {
            if (is_array($class_id) && count($class_id) > 0) {
                $this->db->where_in('student_session.class_id', $class_id);
            } else {
                $this->db->where('student_session.class_id', $class_id);
            }
        }

        // Apply section filter if provided
        if ($section_id !== null && !empty($section_id)) {
            if (is_array($section_id) && count($section_id) > 0) {
                $this->db->where_in('student_session.section_id', $section_id);
            } else {
                $this->db->where('student_session.section_id', $section_id);
            }
        }

        // Group by student ID to avoid duplicates
        $this->db->group_by('students.id');

        // Order by admission number
        $this->db->order_by('students.admission_no', 'asc');

        // Execute query
        $query = $this->db->get('students');
        return $query->result_array();
    }

    /**
     * Get Admission Report by Filters
     *
     * Retrieves admission report data with optional filtering by class, year, and session.
     * Handles null/empty parameters gracefully by returning all records when filters are not provided.
     * Supports both single values and arrays for multi-select functionality.
     *
     * @param mixed $class_id    Class ID (single value, array, or null)
     * @param mixed $year        Admission year (single value, array, or null)
     * @param int   $session_id  Session ID (defaults to current session if not provided)
     * @return array Array of student records with admission information
     */
    public function getAdmissionReportByFilters($class_id = null, $year = null, $session_id = null)
    {
        // If session_id is not provided, use current session
        if (empty($session_id)) {
            $session_id = $this->setting_model->getCurrentSession();
        }

        // Start building the query
        $this->db->select('
            students.id,
            students.admission_no,
            students.admission_date,
            students.firstname,
            students.middlename,
            students.lastname,
            students.mobileno,
            students.guardian_name,
            students.guardian_relation,
            students.guardian_phone,
            students.is_active,
            student_session.class_id,
            student_session.section_id,
            student_session.session_id,
            classes.class,
            sections.section,
            sessions.session
        ');

        // Join with student_session table
        $this->db->join('student_session', 'students.id = student_session.student_id', 'inner');

        // Join with classes table
        $this->db->join('classes', 'student_session.class_id = classes.id', 'inner');

        // Join with sections table
        $this->db->join('sections', 'student_session.section_id = sections.id', 'inner');

        // Join with sessions table
        $this->db->join('sessions', 'student_session.session_id = sessions.id', 'inner');

        // Apply session filter
        $this->db->where('student_session.session_id', $session_id);

        // Apply active status filter
        $this->db->where('students.is_active', 'yes');

        // Apply class filter if provided
        if ($class_id !== null && !empty($class_id)) {
            if (is_array($class_id) && count($class_id) > 0) {
                $this->db->where_in('student_session.class_id', $class_id);
            } else {
                $this->db->where('student_session.class_id', $class_id);
            }
        }

        // Apply year filter if provided (filter by admission_date year)
        if ($year !== null && !empty($year)) {
            if (is_array($year) && count($year) > 0) {
                // For multiple years, use group_start/group_end with OR conditions
                $this->db->group_start();
                foreach ($year as $index => $y) {
                    if ($index === 0) {
                        $this->db->where('YEAR(students.admission_date)', $y);
                    } else {
                        $this->db->or_where('YEAR(students.admission_date)', $y);
                    }
                }
                $this->db->group_end();
            } else {
                $this->db->where('YEAR(students.admission_date)', $year);
            }
        }

        // Group by student ID to avoid duplicates
        $this->db->group_by('students.id');

        // Order by admission number
        $this->db->order_by('students.admission_no', 'asc');

        // Execute query
        $query = $this->db->get('students');
        return $query->result_array();
    }

    /**
     * Get Login Detail Report by Filters
     *
     * Retrieves student login credential information with optional filtering by class, section, and session.
     * Handles null/empty parameters gracefully by returning all records when filters are not provided.
     * Supports both single values and arrays for multi-select functionality.
     * Includes username and password from users table.
     *
     * @param mixed $class_id    Class ID (single value, array, or null)
     * @param mixed $section_id  Section ID (single value, array, or null)
     * @param int   $session_id  Session ID (defaults to current session if not provided)
     * @return array Array of student records with login credential information
     */
    public function getLoginDetailReportByFilters($class_id = null, $section_id = null, $session_id = null)
    {
        // If session_id is not provided, use current session
        if (empty($session_id)) {
            $session_id = $this->setting_model->getCurrentSession();
        }

        // Start building the query
        $this->db->select('
            students.id,
            students.admission_no,
            students.firstname,
            students.middlename,
            students.lastname,
            students.mobileno,
            students.email,
            students.is_active,
            student_session.class_id,
            student_session.section_id,
            student_session.session_id,
            classes.class,
            sections.section,
            sessions.session,
            users.username,
            users.password
        ');

        // Join with student_session table
        $this->db->join('student_session', 'students.id = student_session.student_id', 'inner');

        // Join with classes table
        $this->db->join('classes', 'student_session.class_id = classes.id', 'inner');

        // Join with sections table
        $this->db->join('sections', 'sections.id = student_session.section_id', 'inner');

        // Join with sessions table
        $this->db->join('sessions', 'student_session.session_id = sessions.id', 'inner');

        // Join with users table to get login credentials
        $this->db->join('users', 'students.id = users.user_id AND users.role = "student"', 'left');

        // Apply session filter
        $this->db->where('student_session.session_id', $session_id);

        // Apply active status filter
        $this->db->where('students.is_active', 'yes');

        // Apply class filter if provided
        if ($class_id !== null && !empty($class_id)) {
            if (is_array($class_id) && count($class_id) > 0) {
                $this->db->where_in('student_session.class_id', $class_id);
            } else {
                $this->db->where('student_session.class_id', $class_id);
            }
        }

        // Apply section filter if provided
        if ($section_id !== null && !empty($section_id)) {
            if (is_array($section_id) && count($section_id) > 0) {
                $this->db->where_in('student_session.section_id', $section_id);
            } else {
                $this->db->where('student_session.section_id', $section_id);
            }
        }

        // Group by student ID to avoid duplicates
        $this->db->group_by('students.id');

        // Order by admission number
        $this->db->order_by('students.admission_no', 'asc');

        // Execute query
        $query = $this->db->get('students');
        return $query->result_array();
    }

    /**
     * Get Parent Login Detail Report by Filters
     *
     * Retrieves parent login credential information with optional filtering by class, section, and session.
     * Handles null/empty parameters gracefully by returning all records when filters are not provided.
     * Supports both single values and arrays for multi-select functionality.
     * Includes parent username and password from users table.
     *
     * @param mixed $class_id    Class ID (single value, array, or null)
     * @param mixed $section_id  Section ID (single value, array, or null)
     * @param int   $session_id  Session ID (defaults to current session if not provided)
     * @return array Array of student records with parent login credential information
     */
    public function getParentLoginDetailReportByFilters($class_id = null, $section_id = null, $session_id = null)
    {
        // If session_id is not provided, use current session
        if (empty($session_id)) {
            $session_id = $this->setting_model->getCurrentSession();
        }

        // Start building the query
        $this->db->select('
            students.id,
            students.admission_no,
            students.firstname,
            students.middlename,
            students.lastname,
            students.mobileno,
            students.email,
            students.guardian_name,
            students.guardian_phone,
            students.parent_id,
            students.is_active,
            student_session.class_id,
            student_session.section_id,
            student_session.session_id,
            classes.class,
            sections.section,
            sessions.session,
            parent_users.username as parent_username,
            parent_users.password as parent_password
        ');

        // Join with student_session table
        $this->db->join('student_session', 'students.id = student_session.student_id', 'inner');

        // Join with classes table
        $this->db->join('classes', 'student_session.class_id = classes.id', 'inner');

        // Join with sections table
        $this->db->join('sections', 'sections.id = student_session.section_id', 'inner');

        // Join with sessions table
        $this->db->join('sessions', 'student_session.session_id = sessions.id', 'inner');

        // Join with users table to get parent login credentials
        // Using LEFT JOIN to handle students without parent accounts
        $this->db->join('users as parent_users', 'students.parent_id = parent_users.id AND parent_users.role = "parent"', 'left');

        // Apply session filter
        $this->db->where('student_session.session_id', $session_id);

        // Apply active status filter
        $this->db->where('students.is_active', 'yes');

        // Apply class filter if provided
        if ($class_id !== null && !empty($class_id)) {
            if (is_array($class_id) && count($class_id) > 0) {
                $this->db->where_in('student_session.class_id', $class_id);
            } else {
                $this->db->where('student_session.class_id', $class_id);
            }
        }

        // Apply section filter if provided
        if ($section_id !== null && !empty($section_id)) {
            if (is_array($section_id) && count($section_id) > 0) {
                $this->db->where_in('student_session.section_id', $section_id);
            } else {
                $this->db->where('student_session.section_id', $section_id);
            }
        }

        // Group by student ID to avoid duplicates
        $this->db->group_by('students.id');

        // Order by admission number
        $this->db->order_by('students.admission_no', 'asc');

        // Execute query
        $query = $this->db->get('students');
        return $query->result_array();
    }

    /**
     * Get Student Profile Report by Filters
     *
     * Retrieves comprehensive student profile information with optional filtering by class, section, and session.
     * Handles null/empty parameters gracefully by returning all records when filters are not provided.
     * Supports both single values and arrays for multi-select functionality.
     * Includes extensive student data with hostel, transport, category, and login information.
     *
     * @param mixed $class_id    Class ID (single value, array, or null)
     * @param mixed $section_id  Section ID (single value, array, or null)
     * @param int   $session_id  Session ID (defaults to current session if not provided)
     * @return array Array of student profile records with comprehensive information
     */
    public function getStudentProfileReportByFilters($class_id = null, $section_id = null, $session_id = null)
    {
        // If session_id is not provided, use current session
        if (empty($session_id)) {
            $session_id = $this->setting_model->getCurrentSession();
        }

        // Start building the query with comprehensive student profile fields
        $this->db->select('
            student_session.transport_fees,
            student_session.vehroute_id,
            vehicle_routes.route_id,
            vehicle_routes.vehicle_id,
            transport_route.route_title,
            vehicles.vehicle_no,
            hostel_rooms.room_no,
            vehicles.driver_name,
            vehicles.driver_contact,
            hostel.id as hostel_id,
            hostel.hostel_name,
            room_types.id as room_type_id,
            room_types.room_type,
            students.hostel_room_id,
            student_session.id as student_session_id,
            student_session.fees_discount,
            classes.id AS class_id,
            classes.class,
            sections.id AS section_id,
            sections.section,
            students.id,
            students.admission_no,
            students.roll_no,
            students.admission_date,
            students.firstname,
            students.middlename,
            students.lastname,
            students.image,
            students.mobileno,
            students.email,
            students.state,
            students.city,
            students.pincode,
            students.note,
            students.religion,
            students.cast,
            school_houses.house_name,
            students.dob,
            students.current_address,
            students.previous_school,
            students.guardian_is,
            students.parent_id,
            students.permanent_address,
            students.category_id,
            students.adhar_no,
            students.samagra_id,
            students.bank_account_no,
            students.bank_name,
            students.ifsc_code,
            students.guardian_name,
            students.father_pic,
            students.height,
            students.weight,
            students.measurement_date,
            students.mother_pic,
            students.guardian_pic,
            students.guardian_relation,
            students.guardian_phone,
            students.guardian_address,
            students.is_active,
            students.created_at,
            students.updated_at,
            students.father_name,
            students.father_phone,
            students.blood_group,
            students.school_house_id,
            students.father_occupation,
            students.mother_name,
            students.mother_phone,
            students.mother_occupation,
            students.guardian_occupation,
            students.gender,
            students.rte,
            students.guardian_email,
            users.username,
            users.password,
            students.dis_reason,
            students.dis_note,
            categories.category
        ');

        // Join with required tables
        $this->db->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id', 'inner');
        $this->db->join('classes', 'student_session.class_id = classes.id', 'inner');
        $this->db->join('sections', 'sections.id = student_session.section_id', 'inner');

        // Left joins for optional data
        $this->db->join('hostel_rooms', 'hostel_rooms.id = students.hostel_room_id', 'left');
        $this->db->join('hostel', 'hostel.id = hostel_rooms.hostel_id', 'left');
        $this->db->join('room_types', 'room_types.id = hostel_rooms.room_type_id', 'left');
        $this->db->join('vehicle_routes', 'vehicle_routes.id = student_session.vehroute_id', 'left');
        $this->db->join('transport_route', 'vehicle_routes.route_id = transport_route.id', 'left');
        $this->db->join('vehicles', 'vehicles.id = vehicle_routes.vehicle_id', 'left');
        $this->db->join('school_houses', 'school_houses.id = students.school_house_id', 'left');
        $this->db->join('users', 'users.user_id = students.id AND users.role = "student"', 'left');
        $this->db->join('categories', 'categories.id = students.category_id', 'left');

        // Apply session filter
        $this->db->where('student_session.session_id', $session_id);

        // Apply active status filter
        $this->db->where('students.is_active', 'yes');

        // Apply class filter if provided
        if ($class_id !== null && !empty($class_id)) {
            if (is_array($class_id) && count($class_id) > 0) {
                $this->db->where_in('classes.id', $class_id);
            } else {
                $this->db->where('classes.id', $class_id);
            }
        }

        // Apply section filter if provided
        if ($section_id !== null && !empty($section_id)) {
            if (is_array($section_id) && count($section_id) > 0) {
                $this->db->where_in('sections.id', $section_id);
            } else {
                $this->db->where('sections.id', $section_id);
            }
        }

        // Order by student ID descending
        $this->db->order_by('students.id', 'desc');

        // Execute query
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get Boys Girls Ratio Report by Filters
     *
     * Retrieves aggregated boys/girls ratio statistics with optional filtering by class, section, and session.
     * Returns counts of male and female students grouped by class and section.
     * Handles null/empty parameters gracefully by returning all records when filters are not provided.
     * Supports both single values and arrays for multi-select functionality.
     *
     * @param mixed $class_id    Class ID (single value, array, or null)
     * @param mixed $section_id  Section ID (single value, array, or null)
     * @param int   $session_id  Session ID (defaults to current session if not provided)
     * @return array Array of aggregated records with total_student, male, female counts by class and section
     */
    public function getBoysGirlsRatioReportByFilters($class_id = null, $section_id = null, $session_id = null)
    {
        // If session_id is not provided, use current session
        if (empty($session_id)) {
            $session_id = $this->setting_model->getCurrentSession();
        }

        // Build the query with aggregated counts
        $this->db->select('
            COUNT(*) as total_student,
            SUM(CASE WHEN students.gender = "Male" THEN 1 ELSE 0 END) AS male,
            SUM(CASE WHEN students.gender = "Female" THEN 1 ELSE 0 END) AS female,
            classes.class,
            sections.section,
            classes.id as class_id,
            sections.id as section_id
        ');

        // Join with required tables
        $this->db->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id', 'inner');
        $this->db->join('classes', 'student_session.class_id = classes.id', 'inner');
        $this->db->join('sections', 'sections.id = student_session.section_id', 'inner');
        $this->db->join('categories', 'students.category_id = categories.id', 'left');
        $this->db->join('class_sections', 'class_sections.class_id = classes.id AND class_sections.section_id = sections.id', 'inner');

        // Apply session filter
        $this->db->where('student_session.session_id', $session_id);

        // Apply active status filter
        $this->db->where('students.is_active', 'yes');

        // Apply class filter if provided
        if ($class_id !== null && !empty($class_id)) {
            if (is_array($class_id) && count($class_id) > 0) {
                $this->db->where_in('classes.id', $class_id);
            } else {
                $this->db->where('classes.id', $class_id);
            }
        }

        // Apply section filter if provided
        if ($section_id !== null && !empty($section_id)) {
            if (is_array($section_id) && count($section_id) > 0) {
                $this->db->where_in('sections.id', $section_id);
            } else {
                $this->db->where('sections.id', $section_id);
            }
        }

        // Group by class_sections to get aggregated counts per class-section combination
        $this->db->group_by('class_sections.id');

        // Order by student ID
        $this->db->order_by('students.id');

        // Execute query
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get Online Admission Report by Filters
     *
     * Retrieves online admission data with optional filtering by class, section, admission status, and date range.
     * Handles null/empty parameters gracefully by returning all records when filters are not provided.
     * Supports both single values and arrays for multi-select functionality.
     *
     * @param mixed  $class_id          Class ID (single value, array, or null)
     * @param mixed  $section_id        Section ID (single value, array, or null)
     * @param mixed  $admission_status  Admission status (0=pending, 1=admitted, array, or null)
     * @param string $from_date         Start date for date range filter (or null)
     * @param string $to_date           End date for date range filter (or null)
     * @return array Array of online admission records
     */
    public function getOnlineAdmissionReportByFilters($class_id = null, $section_id = null, $admission_status = null, $from_date = null, $to_date = null)
    {
        // Build the query with comprehensive online admission fields
        $this->db->select('
            online_admissions.id,
            online_admissions.reference_no,
            online_admissions.admission_no,
            online_admissions.firstname,
            online_admissions.middlename,
            online_admissions.lastname,
            online_admissions.mobileno,
            online_admissions.email,
            online_admissions.dob,
            online_admissions.gender,
            online_admissions.form_status,
            online_admissions.paid_status,
            online_admissions.is_enroll,
            online_admissions.created_at,
            classes.id as class_id,
            classes.class,
            sections.id as section_id,
            sections.section,
            students.id as student_id,
            students.admission_date,
            (SELECT IFNULL(SUM(online_admission_payment.paid_amount), 0)
             FROM online_admission_payment
             WHERE online_admission_payment.online_admission_id = online_admissions.id) as paid_amount
        ');

        // Join with required tables
        $this->db->from('online_admissions');
        $this->db->join('students', 'students.admission_no = online_admissions.admission_no', 'left');
        $this->db->join('student_session', 'student_session.student_id = students.id', 'left');
        $this->db->join('class_sections', 'class_sections.id = online_admissions.class_section_id', 'left');
        $this->db->join('classes', 'class_sections.class_id = classes.id', 'left');
        $this->db->join('sections', 'sections.id = class_sections.section_id', 'left');

        // Apply class filter if provided
        if ($class_id !== null && !empty($class_id)) {
            if (is_array($class_id) && count($class_id) > 0) {
                $this->db->where_in('classes.id', $class_id);
            } else {
                $this->db->where('classes.id', $class_id);
            }
        }

        // Apply section filter if provided
        if ($section_id !== null && !empty($section_id)) {
            if (is_array($section_id) && count($section_id) > 0) {
                $this->db->where_in('sections.id', $section_id);
            } else {
                $this->db->where('sections.id', $section_id);
            }
        }

        // Apply admission status filter if provided
        if ($admission_status !== null && !empty($admission_status)) {
            if (is_array($admission_status) && count($admission_status) > 0) {
                $this->db->where_in('online_admissions.is_enroll', $admission_status);
            } else {
                $this->db->where('online_admissions.is_enroll', $admission_status);
            }
        }

        // Apply date range filter if provided
        if (!empty($from_date) && !empty($to_date)) {
            $this->db->where('DATE(online_admissions.created_at) >=', $from_date);
            $this->db->where('DATE(online_admissions.created_at) <=', $to_date);
        } elseif (!empty($from_date)) {
            $this->db->where('DATE(online_admissions.created_at) >=', $from_date);
        } elseif (!empty($to_date)) {
            $this->db->where('DATE(online_admissions.created_at) <=', $to_date);
        }

        // Order by admission number descending
        $this->db->order_by('online_admissions.admission_no', 'desc');

        // Execute query
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get Student Teacher Ratio Report by Filters
     *
     * Retrieves student-teacher ratio statistics with optional filtering by class and section.
     * Returns aggregated counts of students (total, male, female) and teachers grouped by class and section.
     * Calculates boys:girls ratio and student:teacher ratio for each class-section combination.
     * Handles null/empty parameters gracefully by returning all records when filters are not provided.
     * Supports both single values and arrays for multi-select functionality.
     *
     * @param mixed  $class_id    Class ID (single value, array, or null)
     * @param mixed  $section_id  Section ID (single value, array, or null)
     * @param int    $session_id  Session ID (defaults to current session if not provided)
     * @return array Array of student-teacher ratio records with calculated ratios
     */
    public function getStudentTeacherRatioReportByFilters($class_id = null, $section_id = null, $session_id = null)
    {
        // If session_id is not provided, use current session
        if (empty($session_id)) {
            $session_id = $this->setting_model->getCurrentSession();
        }

        // Build the query with aggregated student counts
        $this->db->select('
            COUNT(*) as total_student,
            SUM(CASE WHEN students.gender = "Male" THEN 1 ELSE 0 END) AS male,
            SUM(CASE WHEN students.gender = "Female" THEN 1 ELSE 0 END) AS female,
            classes.class,
            sections.section,
            classes.id as class_id,
            sections.id as section_id
        ');

        // Join with required tables
        $this->db->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id', 'inner');
        $this->db->join('classes', 'student_session.class_id = classes.id', 'inner');
        $this->db->join('sections', 'sections.id = student_session.section_id', 'inner');
        $this->db->join('class_sections', 'class_sections.class_id = classes.id AND class_sections.section_id = sections.id', 'inner');

        // Apply session filter
        $this->db->where('student_session.session_id', $session_id);

        // Apply active status filter
        $this->db->where('students.is_active', 'yes');

        // Apply class filter if provided
        if ($class_id !== null && !empty($class_id)) {
            if (is_array($class_id) && count($class_id) > 0) {
                $this->db->where_in('classes.id', $class_id);
            } else {
                $this->db->where('classes.id', $class_id);
            }
        }

        // Apply section filter if provided
        if ($section_id !== null && !empty($section_id)) {
            if (is_array($section_id) && count($section_id) > 0) {
                $this->db->where_in('sections.id', $section_id);
            } else {
                $this->db->where('sections.id', $section_id);
            }
        }

        // Group by class_sections to get aggregated counts per class-section combination
        $this->db->group_by('class_sections.id');

        // Order by class and section
        $this->db->order_by('classes.id, sections.id');

        // Execute query
        $query = $this->db->get();
        $results = $query->result_array();

        // For each class-section, get teacher count and calculate ratios
        foreach ($results as $key => $row) {
            // Get teacher count for this class-section
            $teacher_count = $this->count_classteachers($row['class_id'], $row['section_id']);
            $total_teacher = !empty($teacher_count) ? $teacher_count : 0;

            // Add teacher count to result
            $results[$key]['total_teacher'] = $total_teacher;

            // Calculate boys:girls ratio
            $results[$key]['boys_girls_ratio'] = $this->calculateRatio($row['male'], $row['female']);

            // Calculate student:teacher ratio
            $results[$key]['teacher_ratio'] = $this->calculateRatio($row['total_student'], $total_teacher);
        }

        return $results;
    }

    /**
     * Count Class Teachers
     *
     * Counts the number of unique teachers assigned to a specific class and section.
     * Teachers are counted from the subject_timetable table where they are assigned to teach subjects.
     * Only active teachers are counted.
     *
     * @param int $class_id   Class ID
     * @param int $section_id Section ID
     * @return int Number of unique teachers assigned to the class-section
     */
    public function count_classteachers($class_id, $section_id)
    {
        $sql = "SELECT staff.id
                FROM `subject_timetable`
                JOIN `subject_group_subjects` ON `subject_timetable`.`subject_group_subject_id` = `subject_group_subjects`.`id`
                INNER JOIN subjects ON subject_group_subjects.subject_id = subjects.id
                INNER JOIN staff ON staff.id = subject_timetable.staff_id
                WHERE staff.is_active = '1'
                AND `subject_timetable`.`class_id` = " . intval($class_id) . "
                AND `subject_timetable`.`section_id` = " . intval($section_id) . "
                AND `subject_timetable`.`session_id` = " . intval($this->current_session);

        $query = $this->db->query($sql);
        $count = $query->result();
        $teacher = array();

        if (!empty($count)) {
            foreach ($count as $key => $value) {
                $teacher[$value->id] = $value->id;
            }
        }

        return count($teacher);
    }

    /**
     * Calculate Ratio
     *
     * Helper method to calculate ratio between two numbers.
     * Returns ratio in format "1:X" where X is rounded to 2 decimal places.
     *
     * @param int $num1 First number
     * @param int $num2 Second number
     * @return string Ratio in format "1:X" or "0:0" if both are zero
     */
    private function calculateRatio($num1, $num2)
    {
        if ($num2 > 0 && $num1 > 0) {
            $ratio = round($num2 / $num1, 2);
            return "1:" . $ratio;
        } elseif ($num1 == 0 && $num2 > 0) {
            return "0:1";
        } elseif ($num1 > 0 && $num2 == 0) {
            return "1:0";
        } else {
            return "0:0";
        }
    }

    /**
     * Search students by class and section with session
     * Used for Total Student Academic Report API
     */
    public function totalsearchByClassSectionWithSession($session_id = null, $class_id = null, $section_id = null)
    {
        $this->db->select('classes.id AS `class_id`,student_session.id as student_session_id,students.id,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.admission_date,students.firstname,students.middlename,  students.lastname,students.image,  students.mobileno, students.email ,students.state,students.city , students.pincode,students.religion,students.dob ,students.current_address,students.permanent_address,students.father_name,students.rte,students.gender')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id');

        if ($session_id != null) {
            $this->db->where('student_session.session_id', $session_id);
        }

        $this->db->where('students.is_active', 'yes');

        if ($class_id != null) {
            $this->db->where('student_session.class_id', $class_id);
        }

        if ($section_id != null) {
            $this->db->where('student_session.section_id', $section_id);
        }

        $this->db->order_by('students.id');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get all students with session filter
     * Used for Total Student Academic Report API
     */
    public function gettotalStudents($session_id = null)
    {
        $this->db->select('classes.id AS `class_id`,student_session.id as student_session_id,students.id,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.admission_date,students.firstname,students.middlename,  students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode ,     students.religion,     students.dob ,students.current_address,    students.permanent_address,students.father_name,students.rte,students.gender')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id');

        if ($session_id != null) {
            $this->db->where('student_session.session_id', $session_id);
        }

        $this->db->where('students.is_active', 'yes');
        $this->db->order_by('students.id');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Search students by name
     * Used for Report By Name API
     */
    public function searchByName($search_text, $class_id = null, $section_id = null, $session_id = null)
    {
        $this->db->select('students.id,students.admission_no,students.firstname,students.middlename,students.lastname,students.roll_no,students.father_name,classes.class,sections.section,student_session.id as student_session_id')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id');

        $this->db->where('students.is_active', 'yes');

        // Search in firstname, middlename, lastname, or admission_no
        $this->db->group_start();
        $this->db->like('students.firstname', $search_text);
        $this->db->or_like('students.middlename', $search_text);
        $this->db->or_like('students.lastname', $search_text);
        $this->db->or_like('students.admission_no', $search_text);
        $this->db->group_end();

        if ($class_id != null) {
            $this->db->where('student_session.class_id', $class_id);
        }

        if ($section_id != null) {
            $this->db->where('student_session.section_id', $section_id);
        }

        if ($session_id != null) {
            $this->db->where('student_session.session_id', $session_id);
        }

        $this->db->order_by('students.firstname');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Search students by class and section
     * Used for Student Academic Report API
     */
    public function searchByClassSection($class_id, $section_id = null, $session_id = null)
    {
        $this->db->select('students.id,students.admission_no,students.firstname,students.middlename,students.lastname,students.roll_no,students.father_name,classes.class,sections.section,student_session.id as student_session_id')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id');

        $this->db->where('students.is_active', 'yes');
        $this->db->where('student_session.class_id', $class_id);

        if ($section_id != null) {
            $this->db->where('student_session.section_id', $section_id);
        }

        if ($session_id != null) {
            $this->db->where('student_session.session_id', $session_id);
        }

        $this->db->order_by('students.firstname');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get student by admission number
     * Used for Student Academic Report API
     */
    public function getByAdmissionNo($admission_no)
    {
        $this->db->select('students.id,students.admission_no,students.firstname,students.middlename,students.lastname,students.roll_no,students.father_name,classes.class,sections.section,student_session.id as student_session_id')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id');

        $this->db->where('students.admission_no', $admission_no);
        $this->db->where('students.is_active', 'yes');

        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * Get all students with session filter
     * Used for Report By Name API
     */
    public function getAll($session_id = null)
    {
        $this->db->select('students.id,students.admission_no,students.firstname,students.middlename,students.lastname,students.roll_no,students.father_name,classes.class,sections.section,student_session.id as student_session_id')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id');

        $this->db->where('students.is_active', 'yes');

        if ($session_id != null) {
            $this->db->where('student_session.session_id', $session_id);
        }

        $this->db->order_by('students.firstname');
        $this->db->limit(100); // Limit to prevent large result sets
        $query = $this->db->get();
        return $query->result_array();
    }

    public function admissionnostatusgetDatatableByClassSection($class_id, $section_id = null,$status)
    {
        $this->db
            ->select('student_admi.admi_status,classes.id as `class_id`,student_session.id as student_session_id,students.id,classes.class,sections.id as `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.admission_date,students.firstname,students.middlename,  students.lastname,students.image,students.mobileno,students.email,students.state,students.city, students.pincode,students.religion,students.dob,students.current_address,    students.permanent_address,IFNULL(students.category_id, 0) as `category_id`,IFNULL(categories.category, "") as `category`,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code ,students.guardian_name, students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.app_key,students.parent_app_key,students.rte,students.gender')
            ->join('student_session', 'student_session.student_id = students.id')
            ->join('classes', 'student_session.class_id = classes.id')
            ->join('sections', 'sections.id = student_session.section_id')
            ->join('categories', 'students.category_id = categories.id', 'left')
            ->join('student_admi','students.id=student_admi.student_id')
            ->where('student_admi.admi_status',$status)
            ->where('student_session.session_id', $this->current_session)
            ->where('students.is_active', "yes");

        $this->db->where('student_session.class_id', $class_id);
        if ($section_id != null) {
            $this->db->where('student_session.section_id', $section_id);
        }

        $this->db->from('students');

        $query = $this->db->get();
        $result = $query->result_array(); // Fetch the result as an array

        // Return the result as an array
        return $result;
    }

    public function admi_no_update($data, $student_id)
    {
        $this->db->where('student_id', $student_id);
        $q = $this->db->get('student_admi');
        if ($q->num_rows() > 0) {
            $this->db->where('student_id', $student_id);
            $this->db->update('student_admi', $data);
        } else {
            $this->db->insert('student_admi', $data);
        }
        return true;
    }

    public function check_admi_no_data_exists($admi_no)
    {
        $this->db->where('admi_no', $admi_no);
        $query = $this->db->get('student_admi');
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getuserid($admission_no){
        $query = $this->db->select('id')
                          ->from('students')
                          ->where('admission_no',$admission_no)
                          ->get();

        if ($query->num_rows() > 0) {
            return $query->row()->id;
        } else {
            return false;
        }
    }

    public function getuseridfromadminotable($admission_no){
        $query = $this->db->select('student_id')
                          ->from('student_admi')
                          ->where('admi_no',$admission_no)
                          ->where('admi_status', 1)
                          ->get();

        if ($query->num_rows() > 0) {
            return $query->row()->student_id;
        } else {
            // If not found with admi_status=1, try without status check
            $query2 = $this->db->select('student_id')
                              ->from('student_admi')
                              ->where('admi_no',$admission_no)
                              ->get();

            if ($query2->num_rows() > 0) {
                return $query2->row()->student_id;
            } else {
                return false;
            }
        }
    }

    public function hallticketnostatusgetDatatableByClassSection($class_id, $section_id = null,$status)
    {
        $this->db
            ->select('student_hallticket.hallticket_status,student_admi.admi_status,classes.id as `class_id`,student_session.id as student_session_id,students.id,classes.class,sections.id as `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.admission_date,students.firstname,students.middlename,  students.lastname,students.image,students.mobileno,students.email,students.state,students.city, students.pincode,students.religion,students.dob,students.current_address,    students.permanent_address,IFNULL(students.category_id, 0) as `category_id`,IFNULL(categories.category, "") as `category`,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code ,students.guardian_name, students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.app_key,students.parent_app_key,students.rte,students.gender')
            ->join('student_session', 'student_session.student_id = students.id')
            ->join('classes', 'student_session.class_id = classes.id')
            ->join('sections', 'sections.id = student_session.section_id')
            ->join('categories', 'students.category_id = categories.id', 'left')
            ->join('student_admi','students.id=student_admi.student_id')
            ->where('student_admi.admi_status',1)
            ->where('student_session.session_id', $this->current_session)
            ->where('students.is_active', "yes");

            if ($status == 0) {
                // Left join with student_hallticket
                $this->db->join('student_hallticket', 'student_admi.id = student_hallticket.admi_no_id', 'left');

                // Filter for rows where there's no match in student_hallticket
                $this->db->where('student_hallticket.id IS NULL');
            } elseif ($status == 1) {
                $this->db->join('student_hallticket', 'student_hallticket.admi_no_id = student_admi.id');
                $this->db->where('student_hallticket.hallticket_status', 1);
            }

        $this->db->where('student_session.class_id', $class_id);
        if ($section_id != null) {
            $this->db->where('student_session.section_id', $section_id);
        }

        $this->db->from('students');

        $query = $this->db->get();
        $result = $query->result_array();

        return $result;
    }

    public function getadmi_no_id($studentid){
        $query = $this->db->select('id')
                          ->from('student_admi')
                          ->where('student_id', $studentid)
                          ->where('admi_status',1)
                          ->get();

        if ($query->num_rows() > 0) {
            return $query->row()->id;
        } else {
            return false;
        }
    }

    public function hallticket_no_add($data)
    {
        $this->db->insert('student_hallticket', $data);

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            return true;
        }
    }

    public function gethallticket_no($admi_no_id){
        $query = $this->db->select('std_hallticket')
                          ->from('student_hallticket')
                          ->where('admi_no_id', $admi_no_id)
                          ->where('hallticket_status',1)
                          ->get();

        if ($query->num_rows() > 0) {
            return $query->row()->std_hallticket;
        } else {
            return false;
        }
    }

    public function hallticket_no_update($data, $id)
    {
        $this->db->where('admi_no_id', $id);
        $this->db->update('student_hallticket', $data);

        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function gethallticket_noo($admi_no_id){
        $query = $this->db->select('admi_no_id')
                          ->from('student_hallticket')
                          ->where('admi_no_id', $admi_no_id)
                          ->where('hallticket_status',1)
                          ->get();

        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

}
