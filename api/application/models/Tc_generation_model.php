<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Tc_generation_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get TC certificate templates list
     * 
     * @param int|null $id Certificate template ID (optional)
     * @return array List of TC certificate templates or single template
     */
    public function get_tc_certificates($id = null)
    {
        $this->db->select('*');
        $this->db->from('tc_generation');
        
        if ($id != null) {
            $this->db->where('id', $id);
            $this->db->where('status', 1);
        } else {
            $this->db->where('status', 1);
        }
        
        $this->db->order_by('id', 'ASC');
        $query = $this->db->get();
        
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    /**
     * Get students for TC generation with filtering
     * 
     * @param array $filters Filter parameters (class_id, section_id)
     * @return array List of students eligible for TC generation
     */
    public function get_students_for_tc($filters = array())
    {
        $this->db->select('students.id as student_id, students.admission_no, students.firstname, students.middlename, students.lastname, students.father_name, students.mother_name, students.dob, students.gender, students.guardian_phone, students.mobileno, students.cast, students.religion, students.admission_date, classes.id as class_id, classes.class, sections.id as section_id, sections.section, categories.category');
        $this->db->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'classes.id = student_session.class_id');
        $this->db->join('sections', 'sections.id = student_session.section_id');
        $this->db->join('categories', 'categories.id = students.category_id', 'left');
        
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

        if (isset($filters['session_id']) && !empty($filters['session_id'])) {
            if (is_array($filters['session_id'])) {
                $this->db->where_in('student_session.session_id', $filters['session_id']);
            } else {
                $this->db->where('student_session.session_id', $filters['session_id']);
            }
        }

        $this->db->order_by('students.admission_no', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get a specific student for TC generation
     * 
     * @param int $student_id Student ID
     * @return array|null Student data
     */
    public function get_student_for_tc($student_id)
    {
        $this->db->select('students.*, classes.class, sections.section, categories.category');
        $this->db->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'classes.id = student_session.class_id');
        $this->db->join('sections', 'sections.id = student_session.section_id');
        $this->db->join('categories', 'categories.id = students.category_id', 'left');
        $this->db->where('students.id', $student_id);
        $this->db->where('students.is_active', 'yes');
        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * Get all TC templates (including inactive) for management
     * 
     * @param array $filters Filter parameters (search, status)
     * @return array List of TC templates
     */
    public function get_all_templates($filters = array())
    {
        $this->db->select('*');
        $this->db->from('tc_generation');
        
        // Apply filters
        if (isset($filters['search']) && !empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('tc_name', $filters['search']);
            $this->db->or_like('school_name', $filters['search']);
            $this->db->or_like('tc_head_tittle', $filters['search']);
            $this->db->group_end();
        }
        
        if (isset($filters['status']) && $filters['status'] !== '') {
            $this->db->where('status', $filters['status']);
        }
        
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Check if TC template exists
     * 
     * @param int $id Template ID
     * @return bool True if exists, false otherwise
     */
    public function template_exists($id)
    {
        $this->db->select('id');
        $this->db->from('tc_generation');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }

    /**
     * Add new TC template
     * 
     * @param array $data Template data
     * @return int|false Insert ID on success, false on failure
     */
    public function add_template($data)
    {
        $this->db->insert('tc_generation', $data);
        return $this->db->insert_id();
    }

    /**
     * Update TC template
     * 
     * @param int $id Template ID
     * @param array $data Template data
     * @return bool True on success, false on failure
     */
    public function update_template($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('tc_generation', $data);
    }

    /**
     * Delete TC template
     * 
     * @param int $id Template ID
     * @return bool True on success, false on failure
     */
    public function delete_template($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('tc_generation');
    }

    /**
     * Get subjects list (for language selection)
     * 
     * @return array List of subjects
     */
    public function get_subjects()
    {
        $this->db->select('id, name');
        $this->db->from('subjects');
        $this->db->order_by('name', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }
}

