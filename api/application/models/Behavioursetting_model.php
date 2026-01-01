<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Behavioursetting_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get behaviour settings
     */
    public function get_settings()
    {
        $this->db->select('*');
        $this->db->from('behaviour_settings');
        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * Update behaviour settings
     */
    public function update_settings($data)
    {
        $this->db->where('id', $data['id']);
        return $this->db->update('behaviour_settings', $data);
    }
}
