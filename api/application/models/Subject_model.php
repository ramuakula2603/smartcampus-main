<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Subject_model extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Get subject by ID or all subjects
     * 
     * @param int $id Subject ID (optional)
     * @return mixed Array of subjects or single subject
     */
    public function get($id = null) {
        if ($id != null) {
            $this->db->select()->from('subjects');
            $this->db->where('id', $id);
            $query = $this->db->get();
            return $query->row_array();
        } else {
            $this->db->select()->from('subjects');
            $this->db->order_by('id');
            $query = $this->db->get();
            return $query->result_array();
        }
    }

    /**
     * Add or update subject
     * 
     * @param array $data Subject data
     * @return int Insert ID or boolean
     */
    public function add($data) {
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('subjects', $data);
            return true;
        } else {
            $this->db->insert('subjects', $data);
            $id = $this->db->insert_id();
            return $id;
        }
    }

    /**
     * Remove subject
     * 
     * @param int $id Subject ID
     * @return boolean
     */
    public function remove($id) {
        $this->db->where('id', $id);
        $this->db->delete('subjects');
        return true;
    }

    /**
     * Check if subject name exists
     * 
     * @param array $data Subject data
     * @return boolean
     */
    function check_data_exists($data) {
        $this->db->where('name', $data['name']);
        $query = $this->db->get('subjects');
        if ($query->num_rows() > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Check if subject code exists
     * 
     * @param array $data Subject data
     * @return boolean
     */
    function check_code_exists($data) {
        $this->db->where('code', $data['code']);
        $query = $this->db->get('subjects');
        if ($query->num_rows() > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}

