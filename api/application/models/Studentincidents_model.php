<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Studentincidents_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get student incidents by student ID
     */
    public function assignstudent($student_id, $session_value = 'current_session')
    {
        $this->db->select('student_incidents.id,student_behaviour.title,student_behaviour.point,student_behaviour.description,student_incidents.created_at,student_incidents.student_id,students.firstname,students.middlename,students.lastname,students.admission_no,staff.name as staff_name,staff.surname as staff_surname,staff.employee_id as staff_employee_id,roles.name as role_id');
        $this->db->from('student_incidents');
        $this->db->join('student_behaviour', 'student_behaviour.id = student_incidents.incident_id');
        $this->db->join('students', 'students.id = student_incidents.student_id');
        $this->db->join('staff', 'staff.id = student_incidents.assign_by');
        $this->db->join('staff_roles', 'staff_roles.staff_id = staff.id', 'left');
        $this->db->join('roles', 'roles.id = staff_roles.role_id', 'left');
        
        // Remove the session check since the session table doesn't exist
        // if ($session_value == 'current_session') {
        //     $this->db->where('student_incidents.session_id', $this->current_session());
        // }
        
        $this->db->where('student_incidents.student_id', $student_id);
        // Remove school_id check since we don't have that column
        // $this->db->where('student_incidents.school_id', $this->schoolId());
        $this->db->order_by('student_incidents.created_at', 'DESC');
        $query = $this->db->get();
        $result = $query->result_array();
        
        // Format the result as a JSON object similar to datatables
        $data = array(
            'data' => $result
        );
        
        return json_encode($data);
    }

    /**
     * Get total points for a student
     */
    public function totalpoints($student_id)
    {
        $this->db->select('SUM(student_behaviour.point) as totalpoints');
        $this->db->from('student_incidents');
        $this->db->join('student_behaviour', 'student_behaviour.id = student_incidents.incident_id');
        $this->db->where('student_incidents.student_id', $student_id);
        // Remove school_id check since we don't have that column
        // $this->db->where('student_incidents.school_id', $this->schoolId());
        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * Get student behavior records
     */
    public function studentbehaviour($student_id)
    {
        $this->db->select('student_behaviour.title,student_behaviour.point,student_behaviour.description,student_incidents.id,student_incidents.created_at,student_incidents.student_id,students.firstname,students.middlename,students.lastname,students.admission_no,staff.name as staff_name,staff.surname as staff_surname,staff.employee_id as staff_employee_id,roles.name as role_id');
        $this->db->from('student_incidents');
        $this->db->join('student_behaviour', 'student_behaviour.id = student_incidents.incident_id');
        $this->db->join('students', 'students.id = student_incidents.student_id');
        $this->db->join('staff', 'staff.id = student_incidents.assign_by');
        $this->db->join('staff_roles', 'staff_roles.staff_id = staff.id', 'left');
        $this->db->join('roles', 'roles.id = staff_roles.role_id', 'left');
        $this->db->where('student_incidents.student_id', $student_id);
        // Remove school_id check since we don't have that column
        // $this->db->where('student_incidents.school_id', $this->schoolId());
        $this->db->order_by('student_incidents.created_at', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Delete student incident by ID
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        // Remove school_id check since we don't have that column
        // $this->db->where('school_id', $this->schoolId());
        return $this->db->delete('student_incidents');
    }

    /**
     * Add or update student incident comment
     */
    public function addmessage($data)
    {
        if (isset($data['id']) && !empty($data['id'])) {
            // Update existing comment
            $this->db->where('id', $data['id']);
            // Remove school_id check since we don't have that column
            // $this->db->where('school_id', $this->schoolId());
            $this->db->update('student_incident_comments', $data);
            return $data['id'];
        } else {
            // Insert new comment
            // Remove school_id since we don't have that column
            // $data['school_id'] = $this->schoolId();
            $this->db->insert('student_incident_comments', $data);
            return $this->db->insert_id();
        }
    }

    /**
     * Get student incident comments by incident ID
     */
    public function getmessage($student_incident_id)
    {
        $this->db->select('student_incident_comments.comment,student_incident_comments.type,student_incident_comments.created_date,staff.name as staff_name,staff.surname as staff_surname,staff.employee_id as staff_employee_id,staff.image as staff_image,staff.gender as staff_gender,students.firstname,students.middlename,students.lastname,students.admission_no,students.image as student_image,student_incident_comments.id,student_incident_comments.staff_id,student_incident_comments.student_id,roles.name as role_name,students.gender as stud_gender');
        $this->db->from('student_incident_comments');
        $this->db->join('staff', 'staff.id = student_incident_comments.staff_id');
        $this->db->join('students', 'students.id = student_incident_comments.student_id');
        $this->db->join('staff_roles', 'staff_roles.staff_id = staff.id', 'left');
        $this->db->join('roles', 'roles.id = staff_roles.role_id', 'left');
        $this->db->where('student_incident_comments.student_incident_id', $student_incident_id);
        // Remove school_id check since we don't have that column
        // $this->db->where('student_incident_comments.school_id', $this->schoolId());
        $this->db->order_by('student_incident_comments.created_date', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Delete student incident comment by ID
     */
    public function delete_comment($id)
    {
        $this->db->where('id', $id);
        // Remove school_id check since we don't have that column
        // $this->db->where('school_id', $this->schoolId());
        return $this->db->delete('student_incident_comments');
    }

    /**
     * Get current session ID
     */
    private function current_session()
    {
        // This would typically retrieve the current session from settings
        // For now, we'll return a default value or you can implement your logic
        // Since the session table doesn't exist, we'll return a default value
        return 1;
    }

    /**
     * Get school ID from session or settings
     */
    private function schoolId()
    {
        // This would typically retrieve the school ID from session or settings
        // For now, we'll return a default value
        return 1;
    }

    /**
     * Get behavior report data with filters
     */
    public function get_report_data($class_id = null, $section_id = null, $student_id = null, $from_date = null, $to_date = null, $incident_id = null)
    {
        $this->db->select('student_incidents.id,student_incidents.created_at,student_behaviour.title,student_behaviour.point,student_behaviour.description,students.firstname,students.middlename,students.lastname,students.admission_no,classes.class,sections.section,staff.name as staff_name,staff.surname as staff_surname');
        $this->db->from('student_incidents');
        $this->db->join('student_behaviour', 'student_behaviour.id = student_incidents.incident_id');
        $this->db->join('students', 'students.id = student_incidents.student_id');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'classes.id = student_session.class_id');
        $this->db->join('sections', 'sections.id = student_session.section_id');
        $this->db->join('staff', 'staff.id = student_incidents.assign_by');
        
        // Apply filters
        if (!empty($class_id)) {
            $this->db->where('classes.id', $class_id);
        }
        
        if (!empty($section_id)) {
            $this->db->where('sections.id', $section_id);
        }
        
        if (!empty($student_id)) {
            $this->db->where('students.id', $student_id);
        }
        
        if (!empty($from_date)) {
            $this->db->where('DATE(student_incidents.created_at) >=', $from_date);
        }
        
        if (!empty($to_date)) {
            $this->db->where('DATE(student_incidents.created_at) <=', $to_date);
        }
        
        if (!empty($incident_id)) {
            $this->db->where('student_behaviour.id', $incident_id);
        }
        
        $this->db->order_by('student_incidents.created_at', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }
}
