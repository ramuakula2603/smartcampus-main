<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Visitors_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function visitors_list($id = null)
    {
        $this->db->select('visitors_book.*,classes.class,sections.section,staff.name as staff_name,staff.surname as staff_surname,staff.employee_id as staff_employee_id,student_session.class_id,student_session.section_id,students.id as students_id,students.admission_no,students.firstname as student_firstname,students.middlename as student_middlename,students.lastname as student_lastname')
            ->from('visitors_book');
        
        if ($id != null) {
            $this->db->where('visitors_book.id', $id);
        } else {
            $this->db->order_by('visitors_book.id', 'desc');
        }
        
        $this->db->join('student_session', 'student_session.id=visitors_book.student_session_id', 'left');
        $this->db->join('students', 'students.id=student_session.student_id', 'left');
        $this->db->join('classes', 'student_session.class_id=classes.id', 'left');
        $this->db->join('sections', 'sections.id=student_session.section_id', 'left');
        $this->db->join('staff', 'staff.id=visitors_book.staff_id', 'left');

        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    public function add($data)
    {
        $this->db->insert('visitors_book', $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('visitors_book', $data);
        return $this->db->affected_rows() > 0;
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('visitors_book');
        return $this->db->affected_rows() > 0;
    }

    public function getPurpose()
    {
        $this->db->select('*');
        $this->db->from('visitors_purpose');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function visitorbystaffid($staff_id)
    {
        $this->db->select('visitors_book.*')
            ->from('visitors_book')
            ->where('visitors_book.staff_id', $staff_id)
            ->order_by('visitors_book.id', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function visitorbystudentid($student_session_id)
    {
        $this->db->select('visitors_book.*')
            ->from('visitors_book')
            ->where('visitors_book.student_session_id', $student_session_id)
            ->order_by('visitors_book.id', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }
}
