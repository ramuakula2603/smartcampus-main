<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Visitors_purpose_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get visitors purpose list with optional filtering
     * 
     * @param int|null $id Purpose ID (optional)
     * @return array List of purposes or single purpose
     */
    public function visitors_purpose_list($id = null)
    {
        $this->db->select('*');
        $this->db->from('visitors_purpose');
        
        if ($id != null) {
            $this->db->where('id', $id);
        } else {
            $this->db->order_by('id', 'asc');
        }
        
        $query = $this->db->get();
        
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    /**
     * Add a new visitors purpose
     * 
     * @param array $data Purpose data
     * @return int Inserted purpose ID
     */
    public function add($data)
    {
        $this->db->insert('visitors_purpose', $data);
        return $this->db->insert_id();
    }

    /**
     * Update an existing visitors purpose
     * 
     * @param int $id Purpose ID
     * @param array $data Purpose data
     * @return bool True if update successful
     */
    public function update($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('visitors_purpose', $data);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Delete a visitors purpose
     * 
     * @param int $id Purpose ID
     * @return bool True if delete successful
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('visitors_purpose');
        return $this->db->affected_rows() > 0;
    }
}

