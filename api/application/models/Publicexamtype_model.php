<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Publicexamtype_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all academic sessions
     * @return array
     */
    public function sessions()
    {
        $this->db->select()->from('sessions');
        $query = $this->db->get();
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
            $query = $this->db->where("id", $id)->get('publicexamtype');
            return $query->row_array();
        } else {
            $query = $this->db->select()->from('publicexamtype')->get();
            return $query->result_array();
        }
    }

    /**
     * Get student ID by hall ticket number
     * @param string $hallno
     * @return mixed
     */
    public function getstudentid($hallno){
        $this->db->select('student_id')->from('student_hallticket')
                 ->join('student_admi','student_admi.id=student_hallticket.admi_no_id')
                 ->where('student_hallticket.std_hallticket',$hallno);
                 
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            return $query->row()->student_id;
        } else {
            return false;
        }
    }

    /**
     * Get exam type/result name by ID
     * @param int $id
     * @return array
     */
    public function getresultname($id){
        $this->db->select()->from('publicexamtype')->where('publicexamtype.id',$id);

        $query = $this->db->get();
        
        return $query->row_array();
    }

    /**
     * Get comprehensive student data
     * @param int $id
     * @return array
     */
    public function gtstudentdata($id){

        $this->db->select('students.app_key,students.parent_app_key,student_session.id as `student_session_id`,classes.id AS `class_id`,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no,students.roll_no,students.admission_no,students.admission_date,students.firstname,students.middlename,  students.lastname,students.image,students.mobileno, students.email ,students.state,students.city,students.pincode,students.note,students.religion,students.cast,students.dob,students.current_address,students.previous_school,students.guardian_is,students.parent_id,  students.permanent_address,students.category_id,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.father_pic ,students.height ,students.weight,students.measurement_date, students.mother_pic , students.guardian_pic , students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.father_phone,students.blood_group,students.school_house_id,students.father_occupation,students.mother_name,students.mother_phone,students.mother_occupation,students.guardian_occupation,students.gender,students.guardian_is,students.rte,students.guardian_email, users.username,users.password,users.id as user_id,students.dis_reason,students.dis_note,students.disable_at')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id');
        $this->db->join('hostel_rooms', 'hostel_rooms.id = students.hostel_room_id', 'left');
        $this->db->join('hostel', 'hostel.id = hostel_rooms.hostel_id', 'left');
        $this->db->join('room_types', 'room_types.id = hostel_rooms.room_type_id', 'left');
        $this->db->join('school_houses', 'school_houses.id = students.school_house_id', 'left');
        $this->db->join('users', 'users.user_id = students.id', 'left');
        $this->db->where('users.role', 'student');
        
        $this->db->where('students.id', $id);
        
        $query = $this->db->get();
        
        return $query->row_array();
    }

    /**
     * Get result status for student/exam/session
     * @param int $stid
     * @param int $resid
     * @param int $acadid
     * @return array
     */
    public function getresultstatus($stid,$resid,$acadid){
        $this->db->select()->from('publicresultaddingstatus')
                ->where('publicresultaddingstatus.stid',$stid)
                ->where('publicresultaddingstatus.resultype_id',$resid)
                ->where('publicresultaddingstatus.session_id',$acadid);

        $query = $this->db->get();

        return $query->row_array();
    }

    /**
     * Get student results by hall ticket number
     * @param string $hallno
     * @param int $resultgrp
     * @param int $sessionid
     * @return array
     */
    public function getstudentresults($hallno, $resultgrp, $sessionid){
        $this->db->select('publicresulttable.*, resultsubjects.*')->from('publicresulttable');
        $this->db->join('student_admi', 'student_admi.student_id = publicresulttable.stid');
        $this->db->join('resultsubjects', 'resultsubjects.id = publicresulttable.subjectid');
        $this->db->join('student_hallticket', 'student_hallticket.admi_no_id = student_admi.id');
        $this->db->where('student_hallticket.std_hallticket', $hallno);
        $this->db->where('publicresulttable.session_id', $sessionid);
        $this->db->where('publicresulttable.resulgroup_id', $resultgrp);

        $query = $this->db->get();
        $result = $query->result_array();

        return $result;
    }
}
