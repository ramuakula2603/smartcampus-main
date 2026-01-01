<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Student_referral_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get student referral list with filtering
     * 
     * @param array $filters Filter parameters (class_id, section_id, reference_id/staff_id)
     * @return array List of student referrals
     */
    public function get_student_referrals($filters = array())
    {
        $this->db->select('student_reference.id as referral_id, student_reference.student_id, student_reference.staff_id as reference_id, student_reference.session_id, student_reference.created_at, students.id as student_id, students.admission_no, students.firstname, students.middlename, students.lastname, students.father_name, students.dob, students.gender, students.guardian_phone, students.mobileno, classes.id as class_id, classes.class, sections.id as section_id, sections.section, staff.name as staff_firstname, staff.surname as staff_lastname, staff.employee_id');
        $this->db->from('student_reference');
        $this->db->join('students', 'students.id = student_reference.student_id');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'classes.id = student_session.class_id');
        $this->db->join('sections', 'sections.id = student_session.section_id');
        $this->db->join('staff', 'staff.id = student_reference.staff_id', 'left');
        
        $this->db->where('students.is_active', 'yes');

        // Apply filters
        if (isset($filters['class_id']) && !empty($filters['class_id'])) {
            if (is_array($filters['class_id'])) {
                $this->db->where_in('student_session.class_id', $filters['class_id']);
            } else {
                $this->db->where('student_session.class_id', $filters['class_id']);
            }
        }

        if (isset($filters['section_id']) && !empty($filters['section_id'])) {
            if (is_array($filters['section_id'])) {
                $this->db->where_in('student_session.section_id', $filters['section_id']);
            } else {
                $this->db->where('student_session.section_id', $filters['section_id']);
            }
        }

        if (isset($filters['reference_id']) && !empty($filters['reference_id'])) {
            if (is_array($filters['reference_id'])) {
                $this->db->where_in('student_reference.staff_id', $filters['reference_id']);
            } else {
                $this->db->where('student_reference.staff_id', $filters['reference_id']);
            }
        }

        if (isset($filters['session_id']) && !empty($filters['session_id'])) {
            if (is_array($filters['session_id'])) {
                $this->db->where_in('student_reference.session_id', $filters['session_id']);
            } else {
                $this->db->where('student_reference.session_id', $filters['session_id']);
            }
        }

        $this->db->order_by('student_reference.id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get a specific student referral by ID
     * 
     * @param int $id Referral ID
     * @return array|null Student referral data
     */
    public function get_referral($id)
    {
        $this->db->select('student_reference.*, students.admission_no, students.firstname, students.middlename, students.lastname, students.father_name, students.dob, students.gender, students.guardian_phone, students.mobileno, classes.class, sections.section, staff.name as staff_firstname, staff.surname as staff_lastname, staff.employee_id');
        $this->db->from('student_reference');
        $this->db->join('students', 'students.id = student_reference.student_id');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'classes.id = student_session.class_id');
        $this->db->join('sections', 'sections.id = student_session.section_id');
        $this->db->join('staff', 'staff.id = student_reference.staff_id', 'left');
        $this->db->where('student_reference.id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * Check if referral exists
     * 
     * @param int $id Referral ID
     * @return bool True if referral exists
     */
    public function referral_exists($id)
    {
        $this->db->select('id');
        $this->db->from('student_reference');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }

    /**
     * Add a new student referral
     * 
     * @param array $data Referral data
     * @return int Inserted referral ID
     */
    public function add($data)
    {
        $this->db->insert('student_reference', $data);
        return $this->db->insert_id();
    }

    /**
     * Update an existing student referral
     * 
     * @param int $id Referral ID
     * @param array $data Referral data
     * @return bool True if update successful
     */
    public function update($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('student_reference', $data);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Delete a student referral
     * 
     * @param int $id Referral ID
     * @return bool True if delete successful
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('student_reference');
        return $this->db->affected_rows() > 0;
    }
}

