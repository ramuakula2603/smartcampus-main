<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Examtype_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('setting_model');
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    /**
     * Get all academic sessions
     * @return array
     */
    public function sessions()
    {
        $query = $this->db->select()->from('sessions')->get();
        return $query->result_array();
    }

    /**
     * Get exam types
     * @param int $id
     * @return mixed
     */
    public function get($id = null)
    {
        if (!empty($id)) {
            $query = $this->db->where("id", $id)->get('examtype');
            return $query->row_array();
        } else {
            $query = $this->db->select()->from('examtype')->get();
            return $query->result_array();
        }
    }
}
