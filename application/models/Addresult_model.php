<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Addresult_model extends MY_Model
{

    protected $current_session;

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
        $this->current_date    = $this->setting_model->getDateYmd();
    }



    // public function admissionnostatusgetDatatableByClassSection($class_id, $section_id = null,$status)
    // {
    //     $this->db
    //         ->select('student_admi.admi_status,classes.id as `class_id`,student_session.id as student_session_id,students.id,classes.class,sections.id as `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.admission_date,students.firstname,students.middlename,  students.lastname,students.image,students.mobileno,students.email,students.state,students.city, students.pincode,students.religion,students.dob,students.current_address,    students.permanent_address,IFNULL(students.category_id, 0) as `category_id`,IFNULL(categories.category, "") as `category`,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code ,students.guardian_name, students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.app_key,students.parent_app_key,students.rte,students.gender')
    //         ->join('student_session', 'student_session.student_id = students.id')
    //         ->join('classes', 'student_session.class_id = classes.id')
    //         ->join('sections', 'sections.id = student_session.section_id')
    //         ->join('categories', 'students.category_id = categories.id', 'left')
    //         ->join('student_admi','students.id=student_admi.student_id')
    //         ->where('student_admi.admi_status',1)
    //         ->where('student_session.session_id', $this->current_session)
    //         ->where('students.is_active', "yes");
        
    //     $this->db->where('student_session.class_id', $class_id);
    //     if ($section_id != null) {
    //         $this->db->where('student_session.section_id', $section_id);
    //     }

    //     $this->db->from('students');

    //     $query = $this->db->get();
    //     $result = $query->result_array(); // Fetch the result as an array

    //     // Return the result as an array
    //     return $result;

    //     // $query = $this->datatables->get('students');
    //     // return $query->row_array();
    //     //$this->datatables->from('students');
    //     // return $this->datatables->generate('json');
    // }

    // public function admissionnostatusgetDatatableByClassSection($class_id, $section_id = null, $status,$resulttype)
    // {
    //     $this->db
    //         ->select('resultaddingstatus.adding_status,classes.id as `class_id`, student_session.id as student_session_id, students.id, classes.class, sections.id as `section_id`, sections.section, students.id, students.admission_no, students.roll_no, students.admission_date, students.firstname, students.middlename, students.lastname, students.image, students.mobileno, students.email, students.state, students.city, students.pincode, students.religion, students.dob, students.current_address, students.permanent_address, IFNULL(students.category_id, 0) as `category_id`, IFNULL(categories.category, "") as `category`, students.adhar_no, students.samagra_id, students.bank_account_no, students.bank_name, students.ifsc_code, students.guardian_name, students.guardian_relation, students.guardian_phone, students.guardian_address, students.is_active, students.created_at, students.updated_at, students.father_name, students.app_key, students.parent_app_key, students.rte, students.gender')
    //         ->join('student_session', 'student_session.student_id = students.id')
    //         ->join('classes', 'student_session.class_id = classes.id')
    //         ->join('sections', 'sections.id = student_session.section_id')
    //         ->join('categories', 'students.category_id = categories.id', 'left')
    //         ->join('student_admi', 'students.id = student_admi.student_id')
    //         ->join('resultaddingstatus','resultaddingstatus.stid=students.id','left')
    //         ->where('student_session.session_id', $this->current_session)
    //         ->where('students.is_active', 'yes');
    


    //     if ($status == 0) {
    //         $this->db->where("resultaddingstatus.resultype_id'!=$resulttype OR resultaddingstatus.adding_status IS NULL OR resultaddingstatus.adding_status = 0)");
    //     }else{
    //         $this->db->where("(resultaddingstatus.adding_status IS NOT NULL OR resultaddingstatus.adding_status = 1)");
    //         $this->db->where('resultaddingstatus.resultype_id',$resulttype);
    //     }
    

    //     $this->db->where('student_session.class_id', $class_id);
    //     if ($section_id != null) {
    //         $this->db->where('student_session.section_id', $section_id);
    //     }
    
    //     $this->db->from('students');
    
    //     $query = $this->db->get();
    //     $result = $query->result_array(); // Fetch the result as an array
    
    //     // Return the result as an array
    //     return $result;
    // }
    

    public function admissionnostatusgetDatatableByClassSection($class_id, $section_id = null, $status, $resulttype)
    {
        $this->db
            ->select('classes.id as `class_id`, student_session.id as student_session_id, students.id, classes.class, sections.id as `section_id`, sections.section, students.id, students.admission_no, students.roll_no, students.admission_date, students.firstname, students.middlename, students.lastname, students.image, students.mobileno, students.email, students.state, students.city, students.pincode, students.religion, students.dob, students.current_address, students.permanent_address, IFNULL(students.category_id, 0) as `category_id`, IFNULL(categories.category, "") as `category`, students.adhar_no, students.samagra_id, students.bank_account_no, students.bank_name, students.ifsc_code, students.guardian_name, students.guardian_relation, students.guardian_phone, students.guardian_address, students.is_active, students.created_at, students.updated_at, students.father_name, students.app_key, students.parent_app_key, students.rte, students.gender')
            ->join('student_session', 'student_session.student_id = students.id')
            ->join('classes', 'student_session.class_id = classes.id')
            ->join('sections', 'sections.id = student_session.section_id')
            ->join('categories', 'students.category_id = categories.id', 'left')
            ->join('student_admi', 'students.id = student_admi.student_id')
            ->where('student_session.session_id', $this->current_session)
            ->where('students.is_active', 'yes');

            if ($status == 0) {
                $this->db->where('students.id NOT IN (SELECT stid FROM resultaddingstatus WHERE resultype_id = ' . $resulttype . ' AND session_id = ' . $this->current_session . ')');
            }

            if($status==1){

                $this->db->join('resultaddingstatus', 'students.id = resultaddingstatus.stid');
                $this->db->where('resultaddingstatus.resultype_id',$resulttype);
                $this->db->where('resultaddingstatus.session_id',$this->current_session);
                $this->db->where('resultaddingstatus.assign_status',1);

            }

        $this->db->where('student_session.class_id', $class_id);
        if ($section_id != null) {
            $this->db->where('student_session.section_id', $section_id);
        }

        $this->db->from('students');

        $query = $this->db->get();
        $result = $query->result_array(); // Fetch the result as an array

        // Return the result as an array
        return $result;
    }

    public function admi_no_update($data, $id)
    {
        $this->db->where('student_id', $id);
        $this->db->update('student_admi', $data);
    
        if ($this->db->affected_rows() > 0) {
            // Update successful
            return true;
        } else {
            // Update failed
            return false;
        }
    }

    public function getAdmissionNumber($studentId)
    {
        // Query the database to fetch the admission number
        $query = $this->db->select('admi_no')
                          ->from('student_admi')
                          ->where('student_id', $studentId)
                          ->where('admi_status',1)
                          ->get();

        if ($query->num_rows() > 0) {
            // Admission number found, return it
            return $query->row()->admi_no;
        } else {
            // Admission number not found
            return false;
        }
    }

    public function subjectsgroup($resulttype){

        // Explicitly select required fields including minmarks and maxmarks
        $this->db->select('resultsubject_group_subjects.subject_id, resultsubject_group_subjects.minmarks, resultsubject_group_subjects.maxmarks, resultsubjects.examtype, resultsubjects.subject_code')
                ->where('resultsubject_group_subjects.session_id', $this->current_session)
                ->where('resultsubject_group_subjects.resultsubjects_id',$resulttype)
                ->join('resultsubjects','resultsubjects.id=resultsubject_group_subjects.subject_id')
                ->from('resultsubject_group_subjects');


        $query = $this->db->get();

        // Debug: Log the SQL query
        log_message('debug', 'subjectsgroup SQL: ' . $this->db->last_query());

        $result = $query->result_array(); // Fetch the result as an array

        // Debug: Log the result
        log_message('debug', 'subjectsgroup result: ' . print_r($result, true));

        // Return the result as an array
        return $result;

    }

    public function add($data)
    {

        $this->db->insert('internalresulttable', $data);
        $insert_id = $this->db->insert_id();
        $message   = INSERT_RECORD_CONSTANT . " On transport route id " . $insert_id;
        $action    = "Insert";
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


    public function addresult($data){
        $this->db->insert('resultaddingstatus', $data);
        $insert_id = $this->db->insert_id();
        $message   = INSERT_RECORD_CONSTANT . " On transport route id " . $insert_id;
        $action    = "Insert";
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


    public function get($id = null)
    {
        $this->db->select()->from('pickup_point');
        if ($id != null) {
            $this->db->where('pickup_point.id', $id);
        } else {
            $this->db->order_by('pickup_point.id');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    public function remove($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('transport_route_id', $id);
        $this->db->delete('route_pickup_point');
        $message   = DELETE_RECORD_CONSTANT . " On transport route id " . $id;
        $action    = "Delete";
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
    }

    public function remove_point($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('pickup_point');
        $message   = DELETE_RECORD_CONSTANT . " On transport route id " . $id;
        $action    = "Delete";
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
    }

    public function remove_pickupfromroute($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('route_pickup_point');
        $message   = DELETE_RECORD_CONSTANT . " On transport route id " . $id;
        $action    = "Delete";
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
    }

    // public function add($data)
    // {
    //     $this->db->trans_start(); # Starting Transaction
    //     $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
    //     //=======================Code Start===========================
    //     if (isset($data['id']) && !empty($data['id'])) {
    //         $this->db->where('id', $data['id']);
    //         $this->db->update('route_pickup_point', $data);
    //         $message   = UPDATE_RECORD_CONSTANT . " On  transport route id " . $data['id'];
    //         $action    = "Update";
    //         $record_id = $data['id'];
    //         $insert_id = $data['id'];
    //         $this->log($message, $record_id, $action);
    //         //======================Code End==============================

    //         $this->db->trans_complete(); # Completing transaction
    //         /* Optional */

    //         if ($this->db->trans_status() === false) {
    //             # Something went wrong.
    //             $this->db->trans_rollback();
    //             return false;
    //         } else {
    //             //return $return_value;
    //         }
    //     } else {
    //         $this->db->insert('route_pickup_point', $data);
    //         $insert_id = $this->db->insert_id();
    //         $message   = INSERT_RECORD_CONSTANT . " On transport route id " . $insert_id;
    //         $action    = "Insert";
    //         $record_id = $insert_id;
    //         $this->log($message, $record_id, $action);
    //         //======================Code End==============================

    //         $this->db->trans_complete(); # Completing transaction
    //         /* Optional */

    //         if ($this->db->trans_status() === false) {
    //             # Something went wrong.
    //             $this->db->trans_rollback();
    //             return false;
    //         } else {
    //             //return $return_value;
    //         }
    //         return $insert_id;
    //     }
    // }

    public function listpickup_point()
    {
        $this->datatables->select('*')->from('pickup_point')->searchable('name,latitude,longitude,id')
            ->orderable('name,latitude,longitude,id');
        return $this->datatables->generate('json');
    }

    public function dropdownpickup_point()
    {
        return $this->db->select('*')->from('pickup_point')->get()->result_array();
    }

    public function getpickup_pointbyid($id)
    {
        $this->db->select('*')->from('route_pickup_point')->where('route_pickup_point.id', $id);
        $getpickup_pointbyid = $this->db->get();
        return $getpickup_pointbyid->row_array();
    }

    public function route_pickup_point()
    {
        $this->db->select('route_pickup_point.transport_route_id,pickup_point.name as pickup_point,transport_route.route_title')->from('route_pickup_point')->join('transport_route', 'route_pickup_point.transport_route_id=transport_route.id')->join('pickup_point', 'pickup_point.id=route_pickup_point.pickup_point_id')->group_by('route_pickup_point.transport_route_id');
        $route_pickup_point = $this->db->get();

        $result = $route_pickup_point->result_array();
        foreach ($result as $key => $value) {
            $result[$key]['point_list'] = $this->getPickupPointByRouteID($value['transport_route_id']);
        }

        return $result;
    }

    public function get_routelist()
    {
        $route_list = $this->db->select('transport_route.id as routes_id,transport_route.route_title')->from('transport_route')->get()->result_array();

        return $route_list;
    } 

    public function getPickupPointByRouteID($id)
    {
        $this->db->select('route_pickup_point.*,pickup_point.name as pickup_point,transport_route.route_title')->from('route_pickup_point')->join('transport_route', 'route_pickup_point.transport_route_id=transport_route.id')->join('pickup_point', 'pickup_point.id=route_pickup_point.pickup_point_id')->where('route_pickup_point.transport_route_id', $id)->order_by('order_number', 'asc');
        $route_pickup_point = $this->db->get();

        return $route_pickup_point->result_array();
    }

    public function add_pickup_point($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id']) && !empty($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('pickup_point', $data);
            $message   = UPDATE_RECORD_CONSTANT . " On  transport route id " . $data['id'];
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
            $this->db->insert('pickup_point', $data);
            $insert_id = $this->db->insert_id();
            $message   = INSERT_RECORD_CONSTANT . " On transport route id " . $insert_id;
            $action    = "Insert";
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
 
    public function getPickupPointsByvehrouteId($vehroute_id)
    {
        $sql   = "SELECT vehicle_routes.*,transport_route.route_title,transport_route.id as `transport_route_id`,route_pickup_point.id as `route_pickup_point_id`,route_pickup_point.fees,route_pickup_point.destination_distance,pickup_point.name FROM `vehicle_routes` INNER JOIN transport_route on transport_route.id=vehicle_routes.route_id INNER JOIN route_pickup_point on route_pickup_point.transport_route_id=transport_route.id  INNER JOIN pickup_point on pickup_point.id= route_pickup_point.pickup_point_id WHERE vehicle_routes.id=" . $this->db->escape($vehroute_id) . " ORDER by route_pickup_point.order_number asc";
        $query = $this->db->query($sql);

        return $query->result();
    }


    public function getmarks($resid,$subid){
        $this->db->select()->from('resultsubject_group_subjects')
                           ->where('resultsubject_group_subjects.resultsubjects_id',$resid)
                           ->where('resultsubject_group_subjects.subject_id',$subid)
                           ->where('resultsubject_group_subjects.session_id',$this->current_session);

        $result = $this->db->get();
        return $result->row_array();
    }


    public function getmarksid($id,$yearid){
        $this->db->select()->from('resultsubject_group_subjects')
                           ->where('resultsubject_group_subjects.id',$id)
                           ->where('resultsubject_group_subjects.session_id',$yearid);

        $result = $this->db->get();
        return $result->row_array();
    }


    public function subjectsgroupp($resulttype,$session){

        // CORRECT: Join to resultsubjects table (the subject_id refers to resultsubjects.id)
        // resultsubjects.subject_code contains the actual subject code that should match CSV column headers
        $this->db->select('resultsubject_group_subjects.subject_id as subid, resultsubjects.subject_code, resultsubjects.examtype as subject_name, resultsubject_group_subjects.*')
                ->where('resultsubject_group_subjects.session_id', $session)
                ->where('resultsubject_group_subjects.resultsubjects_id',$resulttype)
                ->join('resultsubjects','resultsubjects.id=resultsubject_group_subjects.subject_id')
                ->from('resultsubject_group_subjects');


        $query = $this->db->get();
        $result = $query->result_array(); // Fetch the result as an array

        // Return the result as an array
        return $result;

    }


    public function resultupdate($stid,$resid,$subid,$data){
        $this->db->where('stid', $stid);
        $this->db->where('resulgroup_id', $resid);
        $this->db->where('subjectid', $subid);
        $this->db->update('internalresulttable', $data);
        
        $this->db->trans_complete(); # Completing transaction

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            //return $return_value;
        }
    }


    public function getmarkforsubject($stid,$resid,$subid,$sessionid){
        $query = $this->db->select('actualmarks')
                        ->from('internalresulttable')
                        ->where('stid', $stid)
                        ->where('resulgroup_id', $resid)
                        ->where('subjectid', $subid)
                        ->where('session_id', $sessionid)
                        ->get();
        if ($query->num_rows() > 0) {
            // Admission number found, return it
            return $query->row()->actualmarks;
        } else {
            // Admission number not found
            return false;
        }
    }


    public function searchstudentresultassign($class=null,$section_id=null,$catagoryid=null,$gender=null,$id,$sessionid){

        // Initialize custom fields handling
        $i                   = 1;
        $custom_fields       = $this->customfield_model->get_custom_fields('students', 1);
        $field_var_array     = array();

        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($field_var_array, 'table_custom_' . $i . '.field_value as ' . $custom_fields_value->name);
                $i++;
            }
        }

        $field_variable = implode(',', $field_var_array);

        $this->db->select('students.id AS `stid`,resultaddingstatus.assign_status,classes.id AS `class_id`,student_session.id as student_session_id,students.id,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.admission_date,students.firstname,students.middlename,students.lastname,students.image,  students.mobileno,students.email,students.state,students.city,students.pincode,students.religion,students.dob ,students.current_address,students.permanent_address,IFNULL(students.category_id, 0) as `category_id`,IFNULL(categories.category, "") as `category`,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code, students.guardian_name, students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.app_key,students.parent_app_key,students.rte,students.gender,vehicles.vehicle_no,transport_route.route_title,route_pickup_point.id as `route_pickup_point_id`,pickup_point.name as `pickup_point`' . ($field_variable ? ',' . $field_variable : ''))->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id');
        $this->db->join('resultaddingstatus','students.id = resultaddingstatus.stid AND resultaddingstatus.resultype_id = ' . $this->db->escape($id), 'left');
        $this->db->join('categories', 'students.category_id = categories.id', 'left');

        // Add custom field joins
        if (!empty($custom_fields)) {
            $j = 1;
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $j;
                $this->db->join('custom_field_values as ' . $tb_counter, 'students.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, 'left');
                $j++;
            }
        }

        $this->db->join('route_pickup_point', 'student_session.route_pickup_point_id = route_pickup_point.id', 'left');
        $this->db->join('pickup_point', 'pickup_point.id = route_pickup_point.pickup_point_id', 'left');
        $this->db->join('vehicle_routes', 'student_session.vehroute_id = vehicle_routes.id', 'left');
        $this->db->join('transport_route', 'vehicle_routes.route_id = transport_route.id', 'left');
        $this->db->join('vehicles', 'vehicle_routes.vehicle_id = vehicles.id', 'left');
        $this->db->where('student_session.session_id', $this->current_session);
        $this->db->where('students.is_active', "yes");
        if ($class != null) {
            $this->db->where('student_session.class_id', $class);
        }
        if ($section_id != null) {
            $this->db->where('student_session.section_id', $section_id);
        }
        if($catagoryid != null){
            $this->db->where('students.category_id', $catagoryid);
        }
        if($gender != null ){
            $this->db->where('students.gender',$gender);
        }
        
        
        $query = $this->db->get();
        $result = $query->result_array(); // Fetch the result as an array

        // Return the result as an array
        return $result;
    }

    public function updatingresult($stid,$resid,$data,$sessionid){
        // Check if record exists
        $this->db->where('stid', $stid);
        $this->db->where('resultype_id', $resid);
        $this->db->where('session_id', $sessionid);
        $query = $this->db->get('resultaddingstatus');

        if ($query->num_rows() > 0) {
            // Record exists, update it
            $this->db->where('stid', $stid);
            $this->db->where('resultype_id', $resid);
            $this->db->where('session_id', $sessionid);
            $this->db->update('resultaddingstatus', $data);
        } else {
            // Record doesn't exist, insert it
            $insert_data = array_merge($data, array(
                'stid' => $stid,
                'resultype_id' => $resid,
                'session_id' => $sessionid
            ));
            $this->db->insert('resultaddingstatus', $insert_data);
        }

        $this->db->trans_complete(); # Completing transaction

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return true;
        }
    }

    public function check_result_status($stid, $resultype_id, $session_id){
        $this->db->select('*');
        $this->db->from('resultaddingstatus');
        $this->db->where('stid', $stid);
        $this->db->where('resultype_id', $resultype_id);
        $this->db->where('session_id', $session_id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row_array();
        } else {
            return false;
        }
    }

}
