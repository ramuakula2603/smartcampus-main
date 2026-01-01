<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Enquiry_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getenquiry_list($id = null, $status = 'active')
    {
        if (!empty($id) && !empty($status)) {
            $this->db->where("enquiry.id", $id);
        }

        $query = $this->db->select('enquiry.*,classes.class as classname,staff.id as staff_id,staff.name as staff_name,staff.surname as staff_surname,staff.employee_id')
            ->join("classes", "enquiry.class_id = classes.id", "left")
            ->join("staff", "staff.id = enquiry.assigned", "left")
            ->where('enquiry.status', $status)
            ->order_by("enquiry.id", "desc")
            ->get("enquiry");

        if (!empty($id) && !empty($status)) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    public function getFollowByEnquiry($id)
    {
        $query = $this->db->select("*")
            ->where("enquiry_id", $id)
            ->order_by("id", "desc")
            ->get("follow_up");
        return $query->row_array();
    }

    public function add($data)
    {
        $this->db->insert('enquiry', $data);
        return $this->db->insert_id();
    }

    public function enquiry_update($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('enquiry', $data);
        return $this->db->affected_rows() > 0;
    }

    public function enquiry_delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('enquiry');
        return $this->db->affected_rows() > 0;
    }

    public function searchEnquiry($class, $source, $date_from, $date_to, $status = 'active')
    {
        $condition = 0;

        if (!empty($class)) {
            $condition = 1;
            $this->db->where("enquiry.class_id", $class);
        }

        if (!empty($source)) {
            $condition = 1;
            $this->db->where("source", $source);
        }

        if (!empty($status)) {
            if ($status != 'all') {
                $condition = 1;
                $this->db->where("status", $status);
            } else {
                $condition = 1;
            }
        }

        if ((!empty($date_from)) && (!empty($date_to))) {
            $condition = 1;
            $this->db->where("date >= ", $date_from);
            $this->db->where("date <= ", $date_to);
        }

        if ($condition == 0) {
            $this->db->where("enquiry.status", "active");
        }

        $query = $this->db->select('enquiry.*,classes.class as classname')
            ->join("classes", "classes.id = enquiry.class_id", "left")
            ->get("enquiry");
        return $query->result_array();
    }

    public function check_number($phone_number)
    {
        $this->db->select('contact,name');
        $this->db->from('enquiry');
        $this->db->where("contact", $phone_number);
        $result = $this->db->get();
        return $result->row_array();
    }

    public function getComplaintSource()
    {
        $this->db->select('*');
        $this->db->from('source');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_reference()
    {
        $this->db->select('*');
        $this->db->from('reference');
        $query = $this->db->get();
        return $query->result_array();
    }
}

