<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Role Model for API
 * Simplified version without session-based restrictions
 */
class Role_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get roles
     * @param int $id
     * @return mixed
     */
    public function get($id = null)
    {
        $this->db->select()->from('roles');

        if ($id != null) {
            $this->db->where('roles.id', $id);
        } else {
            $this->db->order_by('roles.id');
        }

        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }
}

