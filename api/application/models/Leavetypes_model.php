<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Leave Types Model for API
 * Simplified version for API usage
 */
class Leavetypes_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get leave types
     * @param int $id
     * @return mixed
     */
    public function getLeaveType($id = null)
    {
        $this->db->select('*')->from('leave_types');

        if ($id != null) {
            $this->db->where('id', $id);
        }

        $query = $this->db->get();
        
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    /**
     * Get all leave types
     * @return array
     */
    public function get($id = null)
    {
        return $this->getLeaveType($id);
    }
}

