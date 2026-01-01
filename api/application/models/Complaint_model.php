<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Complaint_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get complaint list with optional filtering
     * 
     * @param int|null $id Complaint ID (optional)
     * @return array List of complaints or single complaint
     */
    public function complaint_list($id = null)
    {
        $this->db->select('*');
        $this->db->from('complaint');
        
        if ($id != null) {
            $this->db->where('id', $id);
        } else {
            $this->db->order_by('id', 'desc');
        }
        
        $query = $this->db->get();
        
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    /**
     * Add a new complaint
     * 
     * @param array $data Complaint data
     * @return int Inserted complaint ID
     */
    public function add($data)
    {
        $this->db->insert('complaint', $data);
        return $this->db->insert_id();
    }

    /**
     * Update an existing complaint
     * 
     * @param int $id Complaint ID
     * @param array $data Complaint data
     * @return bool True if update successful
     */
    public function update($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('complaint', $data);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Delete a complaint
     * 
     * @param int $id Complaint ID
     * @return bool True if delete successful
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('complaint');
        return $this->db->affected_rows() > 0;
    }

    /**
     * Get complaint types
     * 
     * @return array List of complaint types
     */
    public function getComplaintType()
    {
        $this->db->select('*');
        $this->db->from('complaint_type');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get complaint sources
     * 
     * @return array List of sources
     */
    public function getComplaintSource()
    {
        $this->db->select('*');
        $this->db->from('source');
        $query = $this->db->get();
        return $query->result_array();
    }
}

