<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Halltickectgeneration_model extends MY_Model
{

    public function getsubjectname($subid){
        $this->db->select('name')->from('subjects');
        $this->db->where('id', $subid);
        $query = $this->db->get();
        $result = $query->row();
        if ($result) {
            return $result->name;
        } else {
            return null; // or any default value you prefer if the subject is not found
        }
    }
    
    // public function getsubjectname($subid){
    //     $this->db->select('name')->from('subjects');
    //     $this->db->where('id', $subid);
    //     $query = $this->db->get();
    //     return $query->row();
    // }

    public function addtcgenerate($data) {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('halltickect_generation', $data);
            $message = UPDATE_RECORD_CONSTANT . " On  id tc generation id " . $data['id'];
            $action = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);
            //======================Code End==============================

            $this->db->trans_complete(); # Completing transaction
            /* Optional */

            if ($this->db->trans_status() === false) {
                # Something went wrong.
                $this->db->trans_rollback();
                return false;
            } else {
                //return $return_value;
            }
        } else {
            $this->db->insert('halltickect_generation', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On tc generation id " . $insert_id;
            $action = "Insert";
            $record_id = $insert_id;
            $this->log($message, $record_id, $action);
            //======================Code End==============================

            $this->db->trans_complete(); # Completing transaction
            /* Optional */

            if ($this->db->trans_status() === false) {
                # Something went wrong.
                $this->db->trans_rollback();
                return false;
            } else {
                //return $return_value;
            }
            return $insert_id;
        }
    }

    public function idcardlist() {
        $this->db->select('*');
        $this->db->from('halltickect_generation');
        $query = $this->db->get();
        return $query->result();
    }

    public function idcardbyid($id) {
        $this->db->select('*');
        $this->db->from('halltickect_generation');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row();
    }

    public function get($id) {
        $this->db->select('*');
        $this->db->from('halltickect_generation');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->result();
    }


    public function remove($id) {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('halltickect_generation');
        $message = DELETE_RECORD_CONSTANT . " On id tc generation id " . $id;
        $action = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================
        $this->db->trans_complete(); # Completing transaction
        /* Optional */
        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            // return $return_value;
        }
    }


    public function getcertificatebyid($certificate)
    {
        $this->db->select('*');
        $this->db->from('halltickect_generation');
        $this->db->where('id', $certificate);
        $query = $this->db->get();
        return $query->result();
    }


    public function getstudentcertificate()
    {
        $this->db->select('*');
        $this->db->from('halltickect_generation');
        $query = $this->db->get();
        return $query->result();
    }

    public function getStudentsByArray($array)
    {
        $i             = 1;
        $custom_fields = $this->customfield_model->get_custom_fields('students');

        $field_var_array = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($field_var_array, 'table_custom_' . $i . '.field_value as ' . $custom_fields_value->name);
                $this->db->join('custom_field_values as ' . $tb_counter, 'students.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, 'left');
                $i++;
            }
        }

        $field_variable = implode(',', $field_var_array);

        $this->db->select('classes.id AS `class_id`,student_session.id as student_session_id,students.id,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.admission_date,students.firstname, students.middlename,students.lastname,students.image,   students.mobileno,students.email,students.state,students.city,students.pincode,students.religion,students.dob ,students.current_address,students.blood_group,students.permanent_address,IFNULL(students.category_id, 0) as `category_id`,IFNULL(categories.category, "") as `category`,students.adhar_no,students.samagra_id,students.bank_account_no,students.cast,students.bank_name, students.ifsc_code,students.guardian_name, students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.mother_name,students.updated_at,students.father_name,students.rte,students.gender,users.id as `user_tbl_id`,users.username,users.password as `user_tbl_password`,users.is_active as `user_tbl_active`,' . $field_variable)->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id');
        $this->db->join('categories', 'students.category_id = categories.id', 'left');
        $this->db->join('users', 'users.user_id = students.id', 'left');
        $this->db->where('student_session.session_id', $this->current_session);
        $this->db->where('users.role', 'student');
        $this->db->where_in('students.id', $array);
        $this->db->order_by('students.id');
        $this->db->group_by('students.id');
        $query = $this->db->get();
        return $query->result();
    }




    // subjects
    
    public function getsubjects(){
        $this->db->select('*');
        $this->db->from('halltickectsubjects');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getsub($id) {
        $this->db->select('*');
        $this->db->from('halltickectsubjects');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }
    
    public function subjectadd($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('halltickectsubjects', $data);
            $message   = UPDATE_RECORD_CONSTANT . " On  halltickectsubjects id " . $data['id'];
            $action    = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);
            //======================Code End==============================

            $this->db->trans_complete(); # Completing transaction
            /* Optional */

            if ($this->db->trans_status() === false) {
                # Something went wrong.
                $this->db->trans_rollback();
                return false;
            } else {
                //return $return_value;
            }
        } else {
            $this->db->insert('halltickectsubjects', $data);
            $id        = $this->db->insert_id();
            $message   = INSERT_RECORD_CONSTANT . " On  halltickectsubjects id " . $id;
            $action    = "Insert";
            $record_id = $id;
            $this->log($message, $record_id, $action);
            //======================Code End==============================

            $this->db->trans_complete(); # Completing transaction
            /* Optional */

            if ($this->db->trans_status() === false) {
                # Something went wrong.
                $this->db->trans_rollback();
                return false;
            } else {
                //return $return_value;
            }
            return $id;
        }
    }

    public function subremove($id){
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('halltickectsubjects');
        $message = DELETE_RECORD_CONSTANT . " On id tc generation id " . $id;
        $action = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================
        $this->db->trans_complete(); # Completing transaction
        /* Optional */
        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            // return $return_value;
        }
    }




    // subject group

    
    public function getsubjectgrp(){
        $this->db->select('*');
        $this->db->from('halltickectsubgrp');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getgrpsub($id) {
        $this->db->select('*');
        $this->db->from('halltickectsubgrp');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function subjectgrpadd($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('halltickectsubgrp', $data);
            $message   = UPDATE_RECORD_CONSTANT . " On  halltickectsubgrp id " . $data['id'];
            $action    = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);
            //======================Code End==============================

            $this->db->trans_complete(); # Completing transaction
            /* Optional */

            if ($this->db->trans_status() === false) {
                # Something went wrong.
                $this->db->trans_rollback();
                return false;
            } else {
                //return $return_value;
            }
        } else {
            $this->db->insert('halltickectsubgrp', $data);
            $id        = $this->db->insert_id();
            $message   = INSERT_RECORD_CONSTANT . " On  halltickectsubgrp id " . $id;
            $action    = "Insert";
            $record_id = $id;
            $this->log($message, $record_id, $action);
            //======================Code End==============================

            $this->db->trans_complete(); # Completing transaction
            /* Optional */

            if ($this->db->trans_status() === false) {
                # Something went wrong.
                $this->db->trans_rollback();
                return false;
            } else {
                //return $return_value;
            }
            return $id;
        }
    }

    public function subgrpremove($id){
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('halltickectsubgrp');
        $message = DELETE_RECORD_CONSTANT . " On id tc generation id " . $id;
        $action = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================
        $this->db->trans_complete(); # Completing transaction
        /* Optional */
        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            // return $return_value;
        }
    }


   



    // subjectcombo

    public function addsubcombo($data) {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('halltickectsubjectcombo', $data);
            $message = UPDATE_RECORD_CONSTANT . " On  id tc generation id " . $data['id'];
            $action = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);
            //======================Code End==============================

            $this->db->trans_complete(); # Completing transaction
            /* Optional */

            if ($this->db->trans_status() === false) {
                # Something went wrong.
                $this->db->trans_rollback();
                return false;
            } else {
                //return $return_value;
            }
        } else {
            $this->db->insert('halltickectsubjectcombo', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On tc generation id " . $insert_id;
            $action = "Insert";
            $record_id = $insert_id;
            $this->log($message, $record_id, $action);
            //======================Code End==============================

            $this->db->trans_complete(); # Completing transaction
            /* Optional */

            if ($this->db->trans_status() === false) {
                # Something went wrong.
                $this->db->trans_rollback();
                return false;
            } else {
                //return $return_value;
            }
            return $insert_id;
        }
    }


    public function getsubjectgroups() {
        $subquery = $this->db->select('DISTINCT(subjectgrp_id)')
                             ->from('halltickectsubjectcombo')
                             ->get_compiled_select();

        $this->db->select('h.subjectgrp_id, sg.name as subjectgrp_name, sg.id as sgid');
        $this->db->from("($subquery) as h");
        $this->db->join('halltickectsubgrp sg', 'h.subjectgrp_id = sg.id', 'left');
        
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result;
        }
        
        return array();
    }

    public function get_subject_groups() {
        $subquery = $this->db->select('DISTINCT(subjectgrp_id)')
                             ->from('halltickectsubjectcombo')
                             ->get_compiled_select();

        $this->db->select('h.subjectgrp_id, sg.name as subjectgrp_name, sg.id as sgid');
        $this->db->from("($subquery) as h");
        $this->db->join('halltickectsubgrp sg', 'h.subjectgrp_id = sg.id', 'left');
        
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            foreach ($result as &$value) {
                $value['subjects'] = $this->getsubjectsbygroupid($value['subjectgrp_id']);
            }
            return $result;
        }
        
        return array();
    }

    public function getsubjectsbygroupid($group_id) {
        $this->db->select('h.*, s.name as subject_name');
        $this->db->from('halltickectsubjectcombo h');
        $this->db->join('halltickectsubjects s', 'h.subject_id = s.id');
        $this->db->where('h.subjectgrp_id', $group_id);

        $query = $this->db->get();
        return $query->result_array();
    }





    public function valid_check_exists($str)
    {
        $fee_groups_id = $this->input->post('fee_groups_id');
        $feetype_id    = $this->input->post('feetype_id');
        $id            = $this->input->post('id');

        if (!isset($id)) {
            $id = 0;
        }

        if ($this->check_data_exists($fee_groups_id, $feetype_id, $id)) {
            $this->form_validation->set_message('check_exists', $this->lang->line('feegroup_combination_already_exists'));
            return false;
        } else {
            return true;
        }
    }

    public function check_data_exists($fee_groups_id, $feetype_id, $id)
    {
        $this->db->where('subjectgrp_id', $fee_groups_id);
        $query = $this->db->get('halltickectsubjectcombo');

        if ($query->num_rows() > 0) {
            $this->db->where('subjectgrp_id', $fee_groups_id);
            $this->db->where('subject_id', $feetype_id);
            $this->db->where('id !=', $id);
            $query = $this->db->get('halltickectsubjectcombo');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


    public function comboremove($id){
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('halltickectsubjectcombo');
        $message = DELETE_RECORD_CONSTANT . " On id tc generation id " . $id;
        $action = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================
        $this->db->trans_complete(); # Completing transaction
        /* Optional */
        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            // return $return_value;
        }
    }


    public function delcomgrp($id){
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('subjectgrp_id', $id);
        $this->db->delete('halltickectsubjectcombo');
        $message = DELETE_RECORD_CONSTANT . " On id tc generation id " . $id;
        $action = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================
        $this->db->trans_complete(); # Completing transaction
        /* Optional */
        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            // return $return_value;
        }
    }

    public function getsingle($id) {
        $this->db->select('halltickectsubjectcombo.*, 
                           halltickectsubgrp.id as grpid, 
                           halltickectsubgrp.name as grpname, 
                           halltickectsubjects.id as subid, 
                           halltickectsubjects.name as subjname');
        $this->db->from('halltickectsubjectcombo');
        $this->db->where('halltickectsubjectcombo.id', $id);
        $this->db->join('halltickectsubgrp', 'halltickectsubgrp.id = halltickectsubjectcombo.subjectgrp_id', 'left');
        $this->db->join('halltickectsubjects', 'halltickectsubjects.id = halltickectsubjectcombo.subject_id', 'left');
        
        $query = $this->db->get();
        
        return $query->row_array();
    }


    


    public function halltickectsubjects($subject_id) {
        $this->db->select('halltickectsubjects.name,halltickectsubjectcombo.*');
        $this->db->from('halltickectsubjectcombo');
        $this->db->where('halltickectsubjectcombo.subjectgrp_id', $subject_id);
        $this->db->join('halltickectsubjects', 'halltickectsubjects.id=halltickectsubjectcombo.subject_id');
        $this->db->order_by('date', 'ASC');
        
        $query = $this->db->get();
        
        return $query->result_array();
    }

}


