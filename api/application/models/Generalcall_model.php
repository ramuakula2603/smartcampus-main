<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Generalcall_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('setting_model');
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    public function get($id = null)
    {
        $this->db->select('*');
        $this->db->from('general_calls');
        
        if ($id != null) {
            $this->db->where('id', $id);
            return $this->db->get()->row_array();
        } else {
            $this->db->order_by('date', 'DESC');
            return $this->db->get()->result_array();
        }
    }

    public function add($data)
    {
        $this->db->insert('general_calls', $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('general_calls', $data);
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('general_calls');
    }

    public function searchByDate($date)
    {
        $this->db->select('*');
        $this->db->from('general_calls');
        $this->db->where('date', $date);
        $this->db->order_by('date', 'DESC');
        return $this->db->get()->result_array();
    }

    public function getCallsByDateRange($start_date, $end_date)
    {
        $this->db->select('*');
        $this->db->from('general_calls');
        $this->db->where('date >=', $start_date);
        $this->db->where('date <=', $end_date);
        $this->db->order_by('date', 'DESC');
        return $this->db->get()->result_array();
    }
}
