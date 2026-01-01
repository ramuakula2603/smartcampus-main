<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Hall Ticket Model
 * 
 * This model handles all database operations for hall ticket management.
 * It manages templates, subjects, subject groups, and subject combinations.
 * 
 * @package    School Management System
 * @subpackage Models
 * @category   Webservices
 * @author     School Management System
 * @version    1.0.0
 */
class Hall_ticket_model extends CI_Model
{
    /**
     * Constructor
     * 
     * Initializes the model and loads the database.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get hall ticket templates with optional filters
     * 
     * @param array $filters Optional filters
     * @return array Templates data
     */
    public function get_templates($filters = array())
    {
        $this->db->select('*');
        $this->db->from('halltickect_generation');
        
        // Apply filters if provided
        if (!empty($filters)) {
            foreach ($filters as $key => $value) {
                $this->db->where($key, $value);
            }
        }
        
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get specific hall ticket template by ID
     * 
     * @param int $id Template ID
     * @return array Template data or empty array if not found
     */
    public function get_template($id)
    {
        $this->db->select('*');
        $this->db->from('halltickect_generation');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * Check if template exists
     * 
     * @param int $id Template ID
     * @return bool True if exists, false otherwise
     */
    public function template_exists($id)
    {
        $this->db->select('id');
        $this->db->from('halltickect_generation');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }

    /**
     * Create hall ticket template
     * 
     * @param array $data Template data
     * @return int|bool Template ID on success, false on failure
     */
    public function create_template($data)
    {
        $this->db->trans_start();
        
        // Insert template data
        $this->db->insert('halltickect_generation', $data);
        $template_id = $this->db->insert_id();
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {
            return false;
        }
        
        return $template_id;
    }

    /**
     * Update hall ticket template
     * 
     * @param int $id Template ID
     * @param array $data Update data
     * @return bool True on success, false on failure
     */
    public function update_template($id, $data)
    {
        $this->db->trans_start();
        
        // Update template data
        $this->db->where('id', $id);
        $result = $this->db->update('halltickect_generation', $data);
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {
            return false;
        }
        
        return $result;
    }

    /**
     * Delete hall ticket template
     * 
     * @param int $id Template ID
     * @return bool True on success, false on failure
     */
    public function delete_template($id)
    {
        $this->db->trans_start();
        
        // Delete template
        $this->db->where('id', $id);
        $result = $this->db->delete('halltickect_generation');
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {
            return false;
        }
        
        return $result;
    }

    /**
     * Get subjects
     * 
     * @return array Subjects data
     */
    public function get_subjects()
    {
        $this->db->select('*');
        $this->db->from('halltickectsubjects');
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get specific subject by ID
     * 
     * @param int $id Subject ID
     * @return array Subject data or empty array if not found
     */
    public function get_subject($id)
    {
        $this->db->select('*');
        $this->db->from('halltickectsubjects');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * Check if subject exists
     * 
     * @param int $id Subject ID
     * @return bool True if exists, false otherwise
     */
    public function subject_exists($id)
    {
        $this->db->select('id');
        $this->db->from('halltickectsubjects');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }

    /**
     * Check if subject code exists
     * 
     * @param string $subject_code Subject code
     * @param int $exclude_id ID to exclude from check (for updates)
     * @return bool True if exists, false otherwise
     */
    public function subject_code_exists($subject_code, $exclude_id = 0)
    {
        $this->db->select('id');
        $this->db->from('halltickectsubjects');
        $this->db->where('subject_code', $subject_code);
        if ($exclude_id > 0) {
            $this->db->where('id !=', $exclude_id);
        }
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }

    /**
     * Create subject
     * 
     * @param array $data Subject data
     * @return int|bool Subject ID on success, false on failure
     */
    public function create_subject($data)
    {
        $this->db->trans_start();
        
        // Insert subject data
        $this->db->insert('halltickectsubjects', $data);
        $subject_id = $this->db->insert_id();
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {
            return false;
        }
        
        return $subject_id;
    }

    /**
     * Update subject
     * 
     * @param int $id Subject ID
     * @param array $data Update data
     * @return bool True on success, false on failure
     */
    public function update_subject($id, $data)
    {
        $this->db->trans_start();
        
        // Update subject data
        $this->db->where('id', $id);
        $result = $this->db->update('halltickectsubjects', $data);
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {
            return false;
        }
        
        return $result;
    }

    /**
     * Delete subject
     * 
     * @param int $id Subject ID
     * @return bool True on success, false on failure
     */
    public function delete_subject($id)
    {
        $this->db->trans_start();
        
        // Delete subject
        $this->db->where('id', $id);
        $result = $this->db->delete('halltickectsubjects');
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {
            return false;
        }
        
        return $result;
    }

    /**
     * Get subject groups
     * 
     * @return array Subject groups data
     */
    public function get_subject_groups()
    {
        $this->db->select('*');
        $this->db->from('halltickectsubgrp');
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get specific subject group by ID
     * 
     * @param int $id Subject group ID
     * @return array Subject group data or empty array if not found
     */
    public function get_subject_group($id)
    {
        $this->db->select('*');
        $this->db->from('halltickectsubgrp');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * Check if subject group exists
     * 
     * @param int $id Subject group ID
     * @return bool True if exists, false otherwise
     */
    public function subject_group_exists($id)
    {
        $this->db->select('id');
        $this->db->from('halltickectsubgrp');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }

    /**
     * Create subject group
     * 
     * @param array $data Subject group data
     * @return int|bool Subject group ID on success, false on failure
     */
    public function create_subject_group($data)
    {
        $this->db->trans_start();
        
        // Insert subject group data
        $this->db->insert('halltickectsubgrp', $data);
        $group_id = $this->db->insert_id();
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {
            return false;
        }
        
        return $group_id;
    }

    /**
     * Update subject group
     * 
     * @param int $id Subject group ID
     * @param array $data Update data
     * @return bool True on success, false on failure
     */
    public function update_subject_group($id, $data)
    {
        $this->db->trans_start();
        
        // Update subject group data
        $this->db->where('id', $id);
        $result = $this->db->update('halltickectsubgrp', $data);
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {
            return false;
        }
        
        return $result;
    }

    /**
     * Delete subject group
     * 
     * @param int $id Subject group ID
     * @return bool True on success, false on failure
     */
    public function delete_subject_group($id)
    {
        $this->db->trans_start();
        
        // Delete subject group
        $this->db->where('id', $id);
        $result = $this->db->delete('halltickectsubgrp');
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {
            return false;
        }
        
        return $result;
    }

    /**
     * Get subject combinations with optional filters
     * 
     * @param array $filters Optional filters
     * @return array Subject combinations data
     */
    public function get_subject_combinations($filters = array())
    {
        $this->db->select('hsc.*, hs.name as subject_name, hsg.name as group_name');
        $this->db->from('halltickectsubjectcombo hsc');
        $this->db->join('halltickectsubjects hs', 'hsc.subject_id = hs.id', 'left');
        $this->db->join('halltickectsubgrp hsg', 'hsc.subjectgrp_id = hsg.id', 'left');
        
        // Apply filters if provided
        if (!empty($filters)) {
            foreach ($filters as $key => $value) {
                $this->db->where($key, $value);
            }
        }
        
        $this->db->order_by('hsc.id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get specific subject combination by ID
     * 
     * @param int $id Subject combination ID
     * @return array Subject combination data or empty array if not found
     */
    public function get_subject_combination($id)
    {
        $this->db->select('hsc.*, hs.name as subject_name, hsg.name as group_name');
        $this->db->from('halltickectsubjectcombo hsc');
        $this->db->join('halltickectsubjects hs', 'hsc.subject_id = hs.id', 'left');
        $this->db->join('halltickectsubgrp hsg', 'hsc.subjectgrp_id = hsg.id', 'left');
        $this->db->where('hsc.id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * Check if subject combination exists
     * 
     * @param int $subjectgrp_id Subject group ID
     * @param int $subject_id Subject ID
     * @param int $exclude_id ID to exclude from check (for updates)
     * @return bool True if exists, false otherwise
     */
    public function combination_exists($subjectgrp_id, $subject_id, $exclude_id = 0)
    {
        $this->db->select('id');
        $this->db->from('halltickectsubjectcombo');
        $this->db->where('subjectgrp_id', $subjectgrp_id);
        $this->db->where('subject_id', $subject_id);
        if ($exclude_id > 0) {
            $this->db->where('id !=', $exclude_id);
        }
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }

    /**
     * Check if subject combination exists by ID
     * 
     * @param int $id Subject combination ID
     * @return bool True if exists, false otherwise
     */
    public function subject_combination_exists($id)
    {
        $this->db->select('id');
        $this->db->from('halltickectsubjectcombo');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }

    /**
     * Create subject combination
     * 
     * @param array $data Subject combination data
     * @return int|bool Subject combination ID on success, false on failure
     */
    public function create_subject_combination($data)
    {
        $this->db->trans_start();
        
        // Insert subject combination data
        $this->db->insert('halltickectsubjectcombo', $data);
        $combination_id = $this->db->insert_id();
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {
            return false;
        }
        
        return $combination_id;
    }

    /**
     * Update subject combination
     * 
     * @param int $id Subject combination ID
     * @param array $data Update data
     * @return bool True on success, false on failure
     */
    public function update_subject_combination($id, $data)
    {
        $this->db->trans_start();
        
        // Update subject combination data
        $this->db->where('id', $id);
        $result = $this->db->update('halltickectsubjectcombo', $data);
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {
            return false;
        }
        
        return $result;
    }

    /**
     * Delete subject combination
     * 
     * @param int $id Subject combination ID
     * @return bool True on success, false on failure
     */
    public function delete_subject_combination($id)
    {
        $this->db->trans_start();
        
        // Delete subject combination
        $this->db->where('id', $id);
        $result = $this->db->delete('halltickectsubjectcombo');
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {
            return false;
        }
        
        return $result;
    }

    /**
     * Get students by IDs
     * 
     * @param array $student_ids Student IDs
     * @return array Students data
     */
    public function get_students_by_ids($student_ids)
    {
        if (empty($student_ids) || !is_array($student_ids)) {
            return array();
        }
        
        // Sanitize student IDs
        $student_ids = array_map('intval', $student_ids);
        $student_ids = array_filter($student_ids, function($id) { return $id > 0; });
        
        if (empty($student_ids)) {
            return array();
        }
        
        $this->db->select('
            s.id,
            s.admission_no,
            s.firstname,
            s.middlename,
            s.lastname,
            s.dob,
            s.gender,
            s.mobileno,
            c.class,
            sec.section
        ');
        $this->db->from('students s');
        $this->db->join('student_session ss', 's.id = ss.student_id', 'left');
        $this->db->join('classes c', 'ss.class_id = c.id', 'left');
        $this->db->join('sections sec', 'ss.section_id = sec.id', 'left');
        $this->db->where_in('s.id', $student_ids);
        $this->db->order_by('s.id', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }
}
