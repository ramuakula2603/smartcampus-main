<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Dispatch_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function dispatch_list()
    {
        $this->db->select('*');
        $this->db->where('type', 'dispatch');
        $this->db->from('dispatch_receive');
        $this->db->order_by('id', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function dis_rec_data($id, $type)
    {
        $this->db->select('*');
        $this->db->where('id', $id);
        $this->db->where('type', $type);
        $this->db->from('dispatch_receive');
        $query = $this->db->get();
        return $query->row_array();
    }

    public function insert($table, $data)
    {
        $this->db->insert($table, $data);
        return $this->db->insert_id();
    }

    public function update_dispatch($table, $id, $type, $data)
    {
        $this->db->where('id', $id);
        $this->db->where('type', $type);
        $this->db->update($table, $data);
        return $this->db->affected_rows() > 0;
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('dispatch_receive');
        return $this->db->affected_rows() > 0;
    }
}

