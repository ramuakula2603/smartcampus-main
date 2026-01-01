<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Studentbehaviour_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get all student behaviour records with pagination
     */
    public function get_all($limit = null, $offset = null)
    {
        $this->db->select('id, title, point, description, created_at');
        $this->db->from('student_behaviour');
        $this->db->order_by('id', 'DESC');
        
        if ($limit !== null) {
            $this->db->limit($limit, $offset);
        }
        
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get student behaviour by ID
     */
    public function get_by_id($id)
    {
        $this->db->select('id, title, point, description, created_at');
        $this->db->from('student_behaviour');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * Create new student behaviour
     */
    public function create($data)
    {
        $this->db->insert('student_behaviour', $data);
        return $this->db->insert_id();
    }

    /**
     * Update student behaviour by ID
     */
    public function update($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('student_behaviour', $data);
        return $this->db->affected_rows();
    }

    /**
     * Delete student behaviour by ID
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('student_behaviour');
    }

    /**
     * Get total count of student behaviour records
     */
    public function get_count()
    {
        return $this->db->count_all('student_behaviour');
    }
}
