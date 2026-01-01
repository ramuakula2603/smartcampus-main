<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Incomehead_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all income heads
     * @param int $id Optional income head ID
     * @return array
     */
    public function get($id = null)
    {
        $this->db->select()->from('income_head');
        if ($id != null) {
            $this->db->where('id', $id);
        } else {
            $this->db->order_by('id');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

}

