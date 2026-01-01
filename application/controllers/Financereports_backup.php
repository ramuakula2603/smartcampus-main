<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Financereports extends Admin_Controller
{
    /**
     * Simple error logging method that doesn't depend on CodeIgniter's log_message
     * This prevents the "Unable to load the requested class: Log" error
     */
    private function safe_log($level, $message)
    {
        // Only log errors to prevent excessive logging
        if ($level === 'error') {
            $log_file = APPPATH . 'logs/fee_debug.log';
            $timestamp = date('Y-m-d H:i:s');
            $log_entry = "[$timestamp] [$level] $message" . PHP_EOL;
            @file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
        }
    }

    public function __construct()
    {
        parent::__construct();

        $this->time               = strtotime(date('d-m-Y H:i:s'));
        $this->payment_mode       = $this->customlib->payment_mode();
        $this->search_type        = $this->customlib->get_searchtype();
        $this->sch_setting_detail = $this->setting_model->getSetting();
        $this->load->library('media_storage');
        $this->load->model("module_model");


        $this->load->model("student_model");
        $this->load->model("studentfeemaster_model");
        $this->load->model("feetype_model");
        $this->load->model("feetypeadding_model");
        $this->load->model("studentfeemasteradding_model");

    }

    public function finance()
    {
        $this->session->set_userdata('top_menu', 'Financereports');
        $this->session->set_userdata('sub_menu', 'Financereports/finance');
        $this->session->set_userdata('subsub_menu', '');
        $this->load->view('layout/header');
        $this->load->view('financereports/finance');
        $this->load->view('layout/footer');
    }

    public function reportduefees()
    {
        if (!$this->rbac->hasPrivilege('balance_fees_statement', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/finance');
        $this->session->set_userdata('subsub_menu', 'Reports/finance/reportduefees');
        $data                = array();
        $data['title']       = 'student fees';
        $class               = $this->class_model->get();
        $data['classlist']   = $class;
        $data['sch_setting'] = $this->sch_setting_detail;
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $date               = date('Y-m-d');
            $class_id           = $this->input->post('class_id');
            $section_id         = $this->input->post('section_id');
            $data['class_id']   = $class_id;
            $data['section_id'] = $section_id;
            // $fees_dues          = $this->studentfeemaster_model->getStudentDueFeeTypesByDate($date, $class_id, $section_id);
            $fees_dues          = $this->studentfeemaster_model->getStudentDueFeeTypesByDatee($date, $class_id, $section_id);

            $students_list      = array();

            if (!empty($fees_dues)) {
                foreach ($fees_dues as $fee_due_key => $fee_due_value) {
                    $amount_paid = 0;

                    if (isJSON($fee_due_value->amount_detail)) {
                        $student_fees_array = json_decode($fee_due_value->amount_detail);
                        foreach ($student_fees_array as $fee_paid_key => $fee_paid_value) {
                            $amount_paid += ($fee_paid_value->amount + $fee_paid_value->amount_discount);
                        }
                    }
                    if ($amount_paid < $fee_due_value->fee_amount || ($amount_paid < $fee_due_value->amount && $fee_due_value->is_system)) {

                        $students_list[$fee_due_value->student_session_id]['admission_no']             = $fee_due_value->admission_no;
                        $students_list[$fee_due_value->student_session_id]['class_id']             = $fee_due_value->class_id;
                        $students_list[$fee_due_value->student_session_id]['section_id']             = $fee_due_value->section_id;
                        $students_list[$fee_due_value->student_session_id]['student_id']             = $fee_due_value->student_id;
                        $students_list[$fee_due_value->student_session_id]['roll_no']                  = $fee_due_value->roll_no;
                        $students_list[$fee_due_value->student_session_id]['admission_date']           = $fee_due_value->admission_date;
                        $students_list[$fee_due_value->student_session_id]['firstname']                = $fee_due_value->firstname;
                        $students_list[$fee_due_value->student_session_id]['middlename']               = $fee_due_value->middlename;
                        $students_list[$fee_due_value->student_session_id]['lastname']                 = $fee_due_value->lastname;
                        $students_list[$fee_due_value->student_session_id]['father_name']              = $fee_due_value->father_name;
                        $students_list[$fee_due_value->student_session_id]['image']                    = $fee_due_value->image;
                        $students_list[$fee_due_value->student_session_id]['mobileno']                 = $fee_due_value->mobileno;
                        $students_list[$fee_due_value->student_session_id]['email']                    = $fee_due_value->email;
                        $students_list[$fee_due_value->student_session_id]['state']                    = $fee_due_value->state;
                        $students_list[$fee_due_value->student_session_id]['city']                     = $fee_due_value->city;
                        $students_list[$fee_due_value->student_session_id]['pincode']                  = $fee_due_value->pincode;
                        $students_list[$fee_due_value->student_session_id]['class']                    = $fee_due_value->class;
                        $students_list[$fee_due_value->student_session_id]['section']                  = $fee_due_value->section;
                        $students_list[$fee_due_value->student_session_id]['fee_groups_feetype_ids'][] = $fee_due_value->fee_groups_feetype_id;
                    }

                }

            }

            if (!empty($students_list)) {
                foreach ($students_list as $student_key => $student_value) {
                    $students_list[$student_key]['fees_list'] = $this->studentfeemaster_model->studentDepositByFeeGroupFeeTypeArray($student_key, $student_value['fee_groups_feetype_ids']);
                   $students_list[$student_key]['transport_fees']       = array();
                $student               = $this->student_model->getByStudentSession($student_value['student_id']);

        $route_pickup_point_id = $student['route_pickup_point_id'];
        $student_session_id    = $student['student_session_id'];
        $transport_fees=[];
                $module=$this->module_model->getPermissionByModulename('transport');

        if($module['is_active']){

        $transport_fees        = $this->studentfeemaster_model->getStudentTransportFees($student_session_id, $route_pickup_point_id);

        }
        $students_list[$student_key]['transport_fees']       = $transport_fees;

                }
            }

            $data['student_due_fee'] = $students_list;
        }

        $this->load->view('layout/header', $data);
        $this->load->view('financereports/reportduefees', $data);
        $this->load->view('layout/footer', $data);
    }

    public function printreportduefees()
    {
        $data                = array();
        $data['title']       = 'student fees';
        $class               = $this->class_model->get();
        $data['classlist']   = $class;
        $data['sch_setting'] = $this->sch_setting_detail;
        $date                = date('Y-m-d');
        $class_id            = $this->input->post('class_id');
        $section_id          = $this->input->post('section_id');
        $data['class_id']    = $class_id;
        $data['section_id']  = $section_id;
        $fees_dues           = $this->studentfeemaster_model->getStudentDueFeeTypesByDate($date, $class_id, $section_id);
        $students_list       = array();

        if (!empty($fees_dues)) {
            foreach ($fees_dues as $fee_due_key => $fee_due_value) {
                $amount_paid = 0;

                if (isJSON($fee_due_value->amount_detail)) {
                    $student_fees_array = json_decode($fee_due_value->amount_detail);
                    foreach ($student_fees_array as $fee_paid_key => $fee_paid_value) {
                        $amount_paid += ($fee_paid_value->amount + $fee_paid_value->amount_discount);
                    }
                }
                // if ($amount_paid < $fee_due_value->fee_amount) {
                if ($amount_paid < $fee_due_value->fee_amount || ($amount_paid < $fee_due_value->amount && $fee_due_value->is_system)) {
                    $students_list[$fee_due_value->student_session_id]['admission_no']             = $fee_due_value->admission_no;
                     $students_list[$fee_due_value->student_session_id]['class_id']             = $fee_due_value->class_id;
                        $students_list[$fee_due_value->student_session_id]['section_id']             = $fee_due_value->section_id;
                        $students_list[$fee_due_value->student_session_id]['student_id']             = $fee_due_value->student_id;
                    $students_list[$fee_due_value->student_session_id]['roll_no']                  = $fee_due_value->roll_no;
                    $students_list[$fee_due_value->student_session_id]['admission_date']           = $fee_due_value->admission_date;
                    $students_list[$fee_due_value->student_session_id]['firstname']                = $fee_due_value->firstname;
                    $students_list[$fee_due_value->student_session_id]['middlename']               = $fee_due_value->middlename;
                    $students_list[$fee_due_value->student_session_id]['lastname']                 = $fee_due_value->lastname;
                    $students_list[$fee_due_value->student_session_id]['father_name']              = $fee_due_value->father_name;
                    $students_list[$fee_due_value->student_session_id]['image']                    = $fee_due_value->image;
                    $students_list[$fee_due_value->student_session_id]['mobileno']                 = $fee_due_value->mobileno;
                    $students_list[$fee_due_value->student_session_id]['email']                    = $fee_due_value->email;
                    $students_list[$fee_due_value->student_session_id]['state']                    = $fee_due_value->state;
                    $students_list[$fee_due_value->student_session_id]['city']                     = $fee_due_value->city;
                    $students_list[$fee_due_value->student_session_id]['pincode']                  = $fee_due_value->pincode;
                    $students_list[$fee_due_value->student_session_id]['class']                    = $fee_due_value->class;
                    $students_list[$fee_due_value->student_session_id]['section']                  = $fee_due_value->section;
                    $students_list[$fee_due_value->student_session_id]['fee_groups_feetype_ids'][] = $fee_due_value->fee_groups_feetype_id;
                }
            }
        }

        if (!empty($students_list)) {
            foreach ($students_list as $student_key => $student_value) {
                $students_list[$student_key]['fees_list'] = $this->studentfeemaster_model->studentDepositByFeeGroupFeeTypeArray($student_key, $student_value['fee_groups_feetype_ids']);
                  $students_list[$student_key]['transport_fees']       = array();
                $student               = $this->student_model->getByStudentSession($student_value['student_id']);

        $route_pickup_point_id = $student['route_pickup_point_id'];
        $student_session_id    = $student['student_session_id'];
        $transport_fees=[];
                $module=$this->module_model->getPermissionByModulename('transport');

        if($module['is_active']){

        $transport_fees        = $this->studentfeemaster_model->getStudentTransportFees($student_session_id, $route_pickup_point_id);

        }
        $students_list[$student_key]['transport_fees']       = $transport_fees;
            }
        }
        $data['student_due_fee'] = $students_list;
        $page                    = $this->load->view('financereports/_printreportduefees', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    // public function reportdailycollection()
    // {
    //     if (!$this->rbac->hasPrivilege('daily_collection_report', 'can_view')) {
    //         access_denied();
    //     }
    //     $this->session->set_userdata('top_menu', 'Reports');
    //     $this->session->set_userdata('sub_menu', 'Reports/finance');
    //     $this->session->set_userdata('subsub_menu', 'Reports/finance/reportdailycollection');
    //     $data          = array();
    //     $data['title'] = 'Daily Collection Report';
    //     $this->form_validation->set_rules('date_from', $this->lang->line('date_from'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('date_to', $this->lang->line('date_to'), 'trim|required|xss_clean');

    //     if ($this->form_validation->run() == true) {

    //         $date_from          = $this->input->post('date_from');
    //         $date_to            = $this->input->post('date_to');
    //         $formated_date_from = strtotime($this->customlib->dateFormatToYYYYMMDD($date_from));
    //         $formated_date_to   = strtotime($this->customlib->dateFormatToYYYYMMDD($date_to));
    //         $st_fees            = $this->studentfeemaster_model->getCurrentSessionStudentFees();
    //         $fees_data          = array();

    //         for ($i = $formated_date_from; $i <= $formated_date_to; $i += 86400) {
    //             $fees_data[$i]['amt']                       = 0;
    //             $fees_data[$i]['count']                     = 0;
    //             $fees_data[$i]['student_fees_deposite_ids'] = array();
    //         }

    //         if (!empty($st_fees)) {
    //             foreach ($st_fees as $fee_key => $fee_value) {
    //                 if (isJSON($fee_value->amount_detail)) {
    //                     $fees_details = (json_decode($fee_value->amount_detail));
    //                     if (!empty($fees_details)) {
    //                         foreach ($fees_details as $fees_detail_key => $fees_detail_value) {
    //                             $date = strtotime($fees_detail_value->date);
    //                             if ($date >= $formated_date_from && $date <= $formated_date_to) {
    //                                 if (array_key_exists($date, $fees_data)) {
    //                                     $fees_data[$date]['amt'] += $fees_detail_value->amount + $fees_detail_value->amount_fine;
    //                                     $fees_data[$date]['count'] += 1;
    //                                     $fees_data[$date]['student_fees_deposite_ids'][] = $fee_value->student_fees_deposite_id;
    //                                 } else {
    //                                     $fees_data[$date]['amt']                         = $fees_detail_value->amount + $fees_detail_value->amount_fine;
    //                                     $fees_data[$date]['count']                       = 1;
    //                                     $fees_data[$date]['student_fees_deposite_ids'][] = $fee_value->student_fees_deposite_id;
    //                                 }
    //                             }
    //                         }
    //                     }
    //                 }
    //             }
    //         }
    //         $data['fees_data'] = $fees_data;

    //     }

    //     $this->load->view('layout/header', $data);
    //     $this->load->view('financereports/reportdailycollection', $data);
    //     $this->load->view('layout/footer', $data);
    // }

    // public function feeCollectionStudentDeposit()
    // {
    //     $data                 = array();
    //     $date                 = $this->input->post('date');
    //     $fees_id              = $this->input->post('fees_id');
    //     $fees_id_array        = explode(',', $fees_id);
    //     $fees_list            = $this->studentfeemaster_model->getFeesDepositeByIdArray($fees_id_array);
    //     $data['student_list'] = $fees_list;
    //     $data['date']         = $date;
    //     $data['sch_setting']  = $this->sch_setting_detail;
    //     $page                 = $this->load->view('financereports/_feeCollectionStudentDeposit', $data, true);
    //     echo json_encode(array('status' => 1, 'page' => $page));
    // }




    // public function reportdailycollection()
    // {
    //     if (!$this->rbac->hasPrivilege('daily_collection_report', 'can_view')) {
    //         access_denied();
    //     }
    //     $this->session->set_userdata('top_menu', 'Reports');
    //     $this->session->set_userdata('sub_menu', 'Reports/finance');
    //     $this->session->set_userdata('subsub_menu', 'Reports/finance/reportdailycollection');
    //     $data          = array();
    //     $data['title'] = 'Daily Collection Report';
    //     $this->form_validation->set_rules('date_from', $this->lang->line('date_from'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('date_to', $this->lang->line('date_to'), 'trim|required|xss_clean');

    //     if ($this->form_validation->run() == true) {

    //         $date_from          = $this->input->post('date_from');
    //         $date_to            = $this->input->post('date_to');
    //         $formated_date_from = strtotime($this->customlib->dateFormatToYYYYMMDD($date_from));
    //         $formated_date_to   = strtotime($this->customlib->dateFormatToYYYYMMDD($date_to));
    //         $st_fees            = $this->studentfeemaster_model->getCurrentSessionStudentFeess();
    //         $fees_data          = array();

    //         for ($i = $formated_date_from; $i <= $formated_date_to; $i += 86400) {
    //             $fees_data[$i]['amt']                       = 0;
    //             $fees_data[$i]['count']                     = 0;
    //             $fees_data[$i]['student_fees_deposite_ids'] = array();
    //         }

    //         if (!empty($st_fees)) {
    //             foreach ($st_fees as $fee_key => $fee_value) {
    //                 if (isJSON($fee_value->amount_detail)) {
    //                     $fees_details = (json_decode($fee_value->amount_detail));
    //                     if (!empty($fees_details)) {
    //                         foreach ($fees_details as $fees_detail_key => $fees_detail_value) {
    //                             $date = strtotime($fees_detail_value->date);
    //                             if ($date >= $formated_date_from && $date <= $formated_date_to) {
    //                                 if (array_key_exists($date, $fees_data)) {
    //                                     $fees_data[$date]['amt'] += $fees_detail_value->amount + $fees_detail_value->amount_fine;
    //                                     $fees_data[$date]['count'] += 1;
    //                                     $fees_data[$date]['student_fees_deposite_ids'][] = $fee_value->student_fees_deposite_id;
    //                                 } else {
    //                                     $fees_data[$date]['amt']                         = $fees_detail_value->amount + $fees_detail_value->amount_fine;
    //                                     $fees_data[$date]['count']                       = 1;
    //                                     $fees_data[$date]['student_fees_deposite_ids'][] = $fee_value->student_fees_deposite_id;
    //                                 }
    //                             }
    //                         }
    //                     }
    //                 }
    //             }
    //         }
    //         $data['fees_data'] = $fees_data;
    //     }

    //     $this->load->view('layout/header', $data);
    //     $this->load->view('financereports/reportdailycollection', $data);
    //     $this->load->view('layout/footer', $data);
    // }



    public function feeCollectionStudentDeposit()
    {
        $data                 = array();
        $date                 = $this->input->post('date');
        $fees_id              = $this->input->post('fees_id');
        $fees_id_array        = explode(',', $fees_id);
        $fees_list            = $this->studentfeemaster_model->getFeesDepositeByIdArrayy($fees_id_array);
        // $fees_list            = array();
        $data['student_list'] = $fees_list;
        $data['date']         = $date;
        $data['sch_setting']  = $this->sch_setting_detail;
        $page                 = $this->load->view('financereports/_feeCollectionStudentDeposit', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }







    public function reportbyname()
    {
        if (!$this->rbac->hasPrivilege('fees_statement', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/finance');
        $this->session->set_userdata('subsub_menu', 'Reports/finance/reportbyname');
        $data['title']       = 'student fees';
        $data['title']       = 'student fees';
        $class               = $this->class_model->get();
        $data['classlist']   = $class;
        $data['sch_setting'] = $this->sch_setting_detail;

        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $this->load->view('layout/header', $data);
            $this->load->view('financereports/reportByName', $data);
            $this->load->view('layout/footer', $data);
        } else {
            {
                $data['student_due_fee'] = array();
                $class_id                = $this->input->post('class_id');
                $section_id              = $this->input->post('section_id');
                $student_id              = $this->input->post('student_id');
                $student_due_fee         = $this->studentfeemaster_model->getStudentFeesByClassSectionStudent($class_id, $section_id, $student_id);
                foreach($student_due_fee as $key=>$value){
                    $transport_fees=array();
                  $student               = $this->student_model->getByStudentSession($value['student_id']);

        $route_pickup_point_id = $student['route_pickup_point_id'];
        $student_session_id    = $student['student_session_id'];
        $transport_fees=[];
                $module=$this->module_model->getPermissionByModulename('transport');

        if($module['is_active']){

        $transport_fees        = $this->studentfeemaster_model->getStudentTransportFees($student_session_id, $route_pickup_point_id);

        }
        $student_due_fee[$key]['transport_fees']         = $transport_fees;
                }


                $data['student_due_fee'] = $student_due_fee;
                $data['class_id']        = $class_id;
                $data['section_id']      = $section_id;
                $data['student_id']      = $student_id;
                $category                = $this->category_model->get();
                $data['categorylist']    = $category;
                $this->load->view('layout/header', $data);
                $this->load->view('financereports/reportByName', $data);
                $this->load->view('layout/footer', $data);
            }
        }
    }

    public function studentacademicreport()
    {
        if (!$this->rbac->hasPrivilege('balance_fees_report', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/finance');
        $this->session->set_userdata('subsub_menu', 'Reports/finance/studentacademicreport');
        $data['title']           = 'student fee';
        $data['payment_type']    = $this->customlib->getPaymenttype();
        $class                   = $this->class_model->get();
        $data['classlist']       = $class;
        $data['sch_setting']     = $this->sch_setting_detail;
        $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;
        $this->form_validation->set_rules('search_type', $this->lang->line('search_type'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $data['student_due_fee'] = array();
            $data['resultarray']     = array();
            $data['feetype']     = "";
            $data['feetype_arr'] = array();
        } else {
            $student_Array = array();
            $search_type   = $this->input->post('search_type');
            $class_id   = $this->input->post('class_id');
            $section_id = $this->input->post('section_id');

            if (isset($class_id)) {
                $studentlist = $this->student_model->searchByClassSectionWithSession($class_id, $section_id);
            } else {
                $studentlist = $this->student_model->getStudents();
            }

            $student_Array = array();
            if (!empty($studentlist)) {
                foreach ($studentlist as $key => $eachstudent) {
                    $obj                = new stdClass();
                    $obj->name          = $this->customlib->getFullName($eachstudent['firstname'], $eachstudent['middlename'], $eachstudent['lastname'], $this->sch_setting_detail->middlename, $this->sch_setting_detail->lastname);
                    $obj->class         = $eachstudent['class'];
                    $obj->section       = $eachstudent['section'];
                    $obj->admission_no  = $eachstudent['admission_no'];
                    $obj->roll_no       = $eachstudent['roll_no'];
                    $obj->father_name   = $eachstudent['father_name'];
                    $student_session_id = $eachstudent['student_session_id'];
                    $student_total_fees = $this->studentfeemaster_model->getTransStudentFees($student_session_id);

                    if (!empty($student_total_fees)) {
                        $totalfee = 0;
                        $deposit  = 0;
                        $discount = 0;
                        $balance  = 0;
                        $fine     = 0;
                          //print_r($student_total_fees);die;
                        foreach ($student_total_fees as $student_total_fees_key => $student_total_fees_value) {

                            if (!empty($student_total_fees_value->fees)) {
                                foreach ($student_total_fees_value->fees as $each_fee_key => $each_fee_value) {

                                        $totalfee = $totalfee +$each_fee_value->amount;


                                        $amount_detail =json_decode($each_fee_value->amount_detail);



                                    if (is_object($amount_detail) && !empty($amount_detail)) {
                                        foreach ($amount_detail as $amount_detail_key => $amount_detail_value) {
                                            $deposit  = $deposit + $amount_detail_value->amount;
                                            $fine     = $fine + $amount_detail_value->amount_fine;
                                            $discount = $discount + $amount_detail_value->amount_discount;
                                        }
                                    }
                                }
                            }
                        }

                        $obj->totalfee     = $totalfee;
                        $obj->payment_mode = "N/A";
                        $obj->deposit      = $deposit;
                        $obj->fine         = $fine;
                        $obj->discount     = $discount;
                        $obj->balance      = $totalfee - ($deposit + $discount);
                    } else {

                        $obj->totalfee     = 0;
                        $obj->payment_mode = 0;
                        $obj->deposit      = 0;
                        $obj->fine         = 0;
                        $obj->balance      = 0;
                        $obj->discount     = 0;
                    }

                    if ($search_type == 'all') {
                        $student_Array[] = $obj;
                    } elseif ($search_type == 'balance') {
                        if ($obj->balance > 0) {
                            $student_Array[] = $obj;
                        }
                    } elseif ($search_type == 'paid') {
                        if ($obj->balance <= 0) {
                            $student_Array[] = $obj;
                        }
                    }
                }
            }

            $classlistdata[]         = array('result' => $student_Array);
            $data['student_due_fee'] = $student_Array;
            $data['resultarray']     = $classlistdata;

        }

        $this->load->view('layout/header', $data);
        $this->load->view('financereports/studentAcademicReport', $data);
        $this->load->view('layout/footer', $data);
    }

    // public function collection_report()
    // {
    //     if (!$this->rbac->hasPrivilege('collect_fees', 'can_view')) {
    //         access_denied();
    //     }

    //     $data['collect_by']  = $this->studentfeemaster_model->get_feesreceived_by();
    //     $data['searchlist']  = $this->customlib->get_searchtype();
    //     $data['group_by']    = $this->customlib->get_groupby();
    //     $feetype             = $this->feetype_model->get();
    //     $tnumber=count($feetype);
    //     $feetype[$tnumber]=array('id'=>'transport_fees','type'=>'Transport Fees');

    //     $data['feetypeList'] = $feetype;
    //     $this->session->set_userdata('top_menu', 'Reports');
    //     $this->session->set_userdata('sub_menu', 'Reports/finance');
    //     $this->session->set_userdata('subsub_menu', 'Reports/finance/collection_report');
    //     $subtotal = false;

    //     if (isset($_POST['search_type']) && $_POST['search_type'] != '') {
    //         $dates               = $this->customlib->get_betweendate($_POST['search_type']);
    //         $data['search_type'] = $_POST['search_type'];
    //     } else {
    //         $dates               = $this->customlib->get_betweendate('this_year');
    //         $data['search_type'] = '';
    //     }

    //     if (isset($_POST['collect_by']) && $_POST['collect_by'] != '') {
    //         $data['received_by'] = $received_by = $_POST['collect_by'];
    //     } else {
    //         $data['received_by'] = $received_by = '';
    //     }

    //     if (isset($_POST['feetype_id']) && $_POST['feetype_id'] != '') {
    //         $feetype_id = $_POST['feetype_id'];
    //     } else {
    //         $feetype_id = "";
    //     }

    //     if (isset($_POST['group']) && $_POST['group'] != '') {
    //         $data['group_byid'] = $group = $_POST['group'];
    //         $subtotal           = true;
    //     } else {
    //         $data['group_byid'] = $group = '';
    //     }

    //     $collect_by = array();
    //     $collection = array();
    //     $start_date = date('Y-m-d', strtotime($dates['from_date']));
    //     $end_date   = date('Y-m-d', strtotime($dates['to_date']));

    //     $this->form_validation->set_rules('search_type', $this->lang->line('search_duration'), 'trim|required|xss_clean');

    //     $data['classlist']        = $this->class_model->get();
    //     $data['selected_section'] = '';

    //     if ($this->form_validation->run() == false) {
    //         $data['results'] = array();
    //     } else {

    //         $class_id   = $this->input->post('class_id');
    //         $section_id = $this->input->post('section_id');

    //         $data['selected_section'] = $section_id;

    //         $data['results'] = $this->studentfeemaster_model->getFeeCollectionReport($start_date, $end_date, $feetype_id, $received_by, $group, $class_id, $section_id);


    //         if ($group != '') {

    //             if ($group == 'class') {
    //                 $group_by = 'class_id';
    //             } elseif ($group == 'collection') {
    //                 $group_by = 'received_by';
    //             } elseif ($group == 'mode') {
    //                 $group_by = 'payment_mode';
    //             }

    //             foreach ($data['results'] as $key => $value) {
    //                 $collection[$value[$group_by]][] = $value;
    //             }
    //         } else {

    //             $s = 0;
    //             foreach ($data['results'] as $key => $value) {
    //                 $collection[$s++] = array($value);
    //             }
    //         }

    //         $data['results'] = $collection;
    //     }
    //     $data['subtotal']    = $subtotal;

    //     $data['sch_setting'] = $this->sch_setting_detail;
    //     $this->load->view('layout/header', $data);
    //     $this->load->view('financereports/collection_report', $data);
    //     $this->load->view('layout/footer', $data);
    // }



    public function collection_report()
    {
        if (!$this->rbac->hasPrivilege('collect_fees', 'can_view')) {
            access_denied();
        }

        $data['collect_by']  = $this->studentfeemaster_model->get_feesreceived_by();
        $data['searchlist']  = $this->customlib->get_searchtype();
        $data['group_by']    = $this->customlib->get_groupby();
        $session_result           = $this->session_model->get();
        $data['sessionlist']      = $session_result;
        $feetype             = $this->feetype_model->get();
        $tnumber=count($feetype);
        $feetype[$tnumber]=array('id'=>'transport_fees','type'=>'Transport Fees');

        $data['feetypeList'] = $feetype;
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/finance');
        $this->session->set_userdata('subsub_menu', 'Reports/finance/collection_report');
        $subtotal = false;

        if (isset($_POST['search_type']) && $_POST['search_type'] != '') {
            $dates               = $this->customlib->get_betweendate($_POST['search_type']);
            $data['search_type'] = $_POST['search_type'];
        } else {
            $dates               = $this->customlib->get_betweendate('this_year');
            $data['search_type'] = '';
        }

        if (isset($_POST['collect_by']) && $_POST['collect_by'] != '') {
            $data['received_by'] = $received_by = $_POST['collect_by'];
        } else {
            $data['received_by'] = $received_by = '';
        }

        if (isset($_POST['feetype_id']) && $_POST['feetype_id'] != '') {
            $feetype_id = $_POST['feetype_id'];
        } else {
            $feetype_id = "";
        }

        if (isset($_POST['group']) && $_POST['group'] != '') {
            $data['group_byid'] = $group = $_POST['group'];
            $subtotal           = true;
        } else {
            $data['group_byid'] = $group = '';
        }

        $collect_by = array();
        $collection = array();
        $start_date = date('Y-m-d', strtotime($dates['from_date']));
        $end_date   = date('Y-m-d', strtotime($dates['to_date']));

        $this->form_validation->set_rules('search_type', $this->lang->line('search_duration'), 'trim|required|xss_clean');

        $data['classlist']        = $this->class_model->get();
        $data['selected_section'] = '';

        if ($this->form_validation->run() == false) {
            $data['results'] = array();
        } else {

            $class_id   = $this->input->post('class_id');
            $section_id = $this->input->post('section_id');
            $session_id = $this->input->post('sch_session_id');

            $data['selected_section'] = $section_id;

            $data['results'] = $this->studentfeemaster_model->getFeeCollectionReport($start_date, $end_date, $feetype_id, $received_by, $group, $class_id, $section_id,$session_id);


            if ($group != '') {

                if ($group == 'class') {
                    $group_by = 'class_id';
                } elseif ($group == 'collection') {
                    $group_by = 'received_by';
                } elseif ($group == 'mode') {
                    $group_by = 'payment_mode';
                }

                foreach ($data['results'] as $key => $value) {
                    $collection[$value[$group_by]][] = $value;
                }
            } else {

                $s = 0;
                foreach ($data['results'] as $key => $value) {
                    $collection[$s++] = array($value);
                }
            }

            $data['results'] = $collection;
        }
        $data['subtotal']    = $subtotal;

        $data['sch_setting'] = $this->sch_setting_detail;
        $this->load->view('layout/header', $data);
        $this->load->view('financereports/collection_report', $data);
        $this->load->view('layout/footer', $data);
    }














    public function other_collection_report()
    {
        if (!$this->rbac->hasPrivilege('collect_fees', 'can_view')) {
            access_denied();
        }

        $data['collect_by']  = $this->studentfeemaster_model->get_feesreceived_by();

        $data['searchlist']  = $this->customlib->get_searchtype();
        $data['group_by']    = $this->customlib->get_groupby();
        $session_result           = $this->session_model->get();
        $data['sessionlist']      = $session_result;

        $feetype             = $this->feetypeadding_model->get();


        // $tnumber=count($feetype);
        // $feetype[$tnumber]=array('id'=>'transport_fees','type'=>'Transport Fees');

        $data['feetypeList'] = $feetype;
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/finance');
        $this->session->set_userdata('subsub_menu', 'Reports/finance/other_collection_report');

        $subtotal = false;

        if (isset($_POST['search_type']) && $_POST['search_type'] != '') {
            $dates               = $this->customlib->get_betweendate($_POST['search_type']);
            $data['search_type'] = $_POST['search_type'];
        } else {
            $dates               = $this->customlib->get_betweendate('this_year');
            $data['search_type'] = '';
        }

        if (isset($_POST['collect_by']) && $_POST['collect_by'] != '') {
            $data['received_by'] = $received_by = $_POST['collect_by'];
        } else {
            $data['received_by'] = $received_by = '';
        }

        if (isset($_POST['feetype_id']) && $_POST['feetype_id'] != '') {
            $feetype_id = $_POST['feetype_id'];
        } else {
            $feetype_id = "";
        }

        if (isset($_POST['group']) && $_POST['group'] != '') {
            $data['group_byid'] = $group = $_POST['group'];
            $subtotal           = true;
        } else {
            $data['group_byid'] = $group = '';
        }

        $collect_by = array();
        $collection = array();
        $start_date = date('Y-m-d', strtotime($dates['from_date']));
        $end_date   = date('Y-m-d', strtotime($dates['to_date']));

        $this->form_validation->set_rules('search_type', $this->lang->line('search_duration'), 'trim|required|xss_clean');

        $data['classlist']        = $this->class_model->get();
        $data['selected_section'] = '';

        if ($this->form_validation->run() == false) {
            $data['results'] = array();
        } else {

            $class_id   = $this->input->post('class_id');
            $section_id = $this->input->post('section_id');
            $session_id = $this->input->post('sch_session_id');

            $data['selected_section'] = $section_id;

            $data['results'] = $this->studentfeemasteradding_model->getFeeCollectionReport($start_date, $end_date, $feetype_id, $received_by, $group, $class_id, $section_id, $session_id);

            if ($group != '') {
                if ($group == 'class') {
                    $group_by = 'class_id';
                } elseif ($group == 'collection') {
                    $group_by = 'received_by';
                } elseif ($group == 'mode') {
                    $group_by = 'payment_mode';
                }

                $collection = array();
                if (!empty($data['results'])) {
                    foreach ($data['results'] as $key => $value) {
                        $collection[$value[$group_by]][] = $value;
                    }
                }
            } else {
                $collection = array();
                if (!empty($data['results'])) {
                    $s = 0;
                    foreach ($data['results'] as $key => $value) {
                        $collection[$s++] = array($value);
                    }
                }
            }

            $data['results'] = $collection;

        }
        $data['subtotal']    = $subtotal;

        $data['sch_setting'] = $this->sch_setting_detail;
        $this->load->view('layout/header', $data);
        $this->load->view('financereports/other_collection_report', $data);
        $this->load->view('layout/footer', $data);
    }







    public function total_fee_collection_report()
    {
        if (!$this->rbac->hasPrivilege('collect_fees', 'can_view')) {
            access_denied();
        }

        $data['collect_by']  = $this->studentfeemaster_model->get_feesreceived_by();
        $data['searchlist']  = $this->customlib->get_searchtype();
        $data['group_by']    = $this->customlib->get_groupby();
        $session_result      = $this->session_model->get();
        $data['sessionlist'] = $session_result;

        // Get regular fee types
        $feetype             = $this->feetype_model->get();
        $tnumber=count($feetype);
        $feetype[$tnumber]=array('id'=>'transport_fees','type'=>'Transport Fees');

        // Get other fee types
        $other_feetype       = $this->feetypeadding_model->get();

        // Combine both fee types
        $combined_feetype = array_merge($feetype, $other_feetype);

        $data['feetypeList'] = $combined_feetype;
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/finance');
        $this->session->set_userdata('subsub_menu', 'Reports/finance/total_fee_collection_report');
        $subtotal = false;

        if (isset($_POST['search_type']) && $_POST['search_type'] != '') {
            $dates               = $this->customlib->get_betweendate($_POST['search_type']);
            $data['search_type'] = $_POST['search_type'];
        } else {
            $dates               = $this->customlib->get_betweendate('this_year');
            $data['search_type'] = '';
        }

        if (isset($_POST['collect_by']) && $_POST['collect_by'] != '') {
            $data['received_by'] = $received_by = $_POST['collect_by'];
        } else {
            $data['received_by'] = $received_by = '';
        }

        if (isset($_POST['feetype_id']) && $_POST['feetype_id'] != '') {
            $feetype_id = $_POST['feetype_id'];
        } else {
            $feetype_id = "";
        }

        if (isset($_POST['group']) && $_POST['group'] != '') {
            $data['group_byid'] = $group = $_POST['group'];
            $subtotal           = true;
        } else {
            $data['group_byid'] = $group = '';
        }

        $collect_by = array();
        $collection = array();
        $start_date = date('Y-m-d', strtotime($dates['from_date']));
        $end_date   = date('Y-m-d', strtotime($dates['to_date']));

        $this->form_validation->set_rules('search_type', $this->lang->line('search_duration'), 'trim|required|xss_clean');

        $data['classlist']        = $this->class_model->get();
        $data['selected_section'] = '';

        if ($this->form_validation->run() == false) {
            $data['results'] = array();
        } else {

            $class_id   = $this->input->post('class_id');
            $section_id = $this->input->post('section_id');
            $session_id = $this->input->post('sch_session_id');

            $data['selected_section'] = $section_id;

            // Get regular fee collection data
            $regular_fees = $this->studentfeemaster_model->getFeeCollectionReport($start_date, $end_date, $feetype_id, $received_by, $group, $class_id, $section_id, $session_id);

            // Get other fee collection data
            $other_fees = $this->studentfeemasteradding_model->getFeeCollectionReport($start_date, $end_date, $feetype_id, $received_by, $group, $class_id, $section_id, $session_id);

            // Combine both results
            $combined_results = array_merge($regular_fees, $other_fees);

            if ($group != '') {
                if ($group == 'class') {
                    $group_by = 'class_id';
                } elseif ($group == 'collection') {
                    $group_by = 'received_by';
                } elseif ($group == 'mode') {
                    $group_by = 'payment_mode';
                }

                foreach ($combined_results as $key => $value) {
                    $collection[$value[$group_by]][] = $value;
                }
            } else {
                $s = 0;
                foreach ($combined_results as $key => $value) {
                    $collection[$s++] = array($value);
                }
            }

            $data['results'] = $collection;
        }
        $data['subtotal']    = $subtotal;

        $data['sch_setting'] = $this->sch_setting_detail;
        $this->load->view('layout/header', $data);
        $this->load->view('financereports/total_fee_collection_report', $data);
        $this->load->view('layout/footer', $data);
    }

    // Helper method to log both to server and browser console
    private function console_log($message, $data = null) {
        // Log to server error log
        $this->safe_log('debug', $message . ($data ? ' - Data: ' . json_encode($data) : ''));
        
        // Also output to browser console via JavaScript (only if not AJAX)
        if (!$this->input->is_ajax_request()) {
            $js_message = addslashes($message);
            $js_data = $data ? json_encode($data) : 'null';
            echo "<script>console.log('CONTROLLER: {$js_message}', {$js_data});</script>";
        }
    }

    public function fee_collection_report_columnwise()
    {
        $this->console_log('=== CONTROLLER: Method fee_collection_report_columnwise started ===');
        
        if (!$this->rbac->hasPrivilege('collect_fees', 'can_view')) {
            $this->console_log('CONTROLLER: Access denied - no privileges');
            access_denied();
        }

        $this->console_log('CONTROLLER: Access granted, processing request');
        $this->console_log('CONTROLLER: HTTP Method', $_SERVER['REQUEST_METHOD']);
        $this->console_log('CONTROLLER: Raw POST data', $_POST);

        try {
            $this->console_log('CONTROLLER: Initializing data structure');
            // Initialize default data structure to prevent view errors
            $data = array(
                'results' => array(),
                'fee_types' => array(),
                'error_message' => null,
                'sch_setting' => $this->sch_setting_detail
            );

            $this->console_log('CONTROLLER: Getting POST data');
            // Get POST data with error handling
            $class_id = $this->input->post('class_id');

            $this->console_log('CONTROLLER: Raw class_id from POST', $class_id);

            // Check if class_id is coming through as JSON (fallback for AJAX requests)
            if (empty($class_id)) {
                $this->console_log('CONTROLLER: class_id empty, checking raw input');
                $raw_post = @file_get_contents('php://input');
                if ($raw_post && strpos($raw_post, 'class_id') !== false) {
                    $this->console_log('CONTROLLER: Found class_id in raw input', $raw_post);
                    $json_data = json_decode($raw_post, true);
                    if (json_last_error() === JSON_ERROR_NONE && isset($json_data['class_id'])) {
                        $class_id = $json_data['class_id'];
                        $this->console_log('CONTROLLER: Extracted class_id from JSON', $class_id);
                    }
                }
            }

            $this->console_log('CONTROLLER: Loading required models');
            // Load required models with error handling
            try {
                $this->load->model('studentfeemaster_model');
                $this->load->model('feetype_model');
                $this->load->model('session_model');
            } catch (Exception $model_error) {
                throw new Exception('Failed to load required models: ' . $model_error->getMessage());
            }

            // Get data with error handling
            $data['collect_by']  = $this->studentfeemaster_model->get_feesreceived_by();
            $data['searchlist']  = $this->customlib->get_searchtype();
            $data['group_by']    = $this->customlib->get_groupby();

            // Get session data with error handling
            $session_result = $this->session_model->get();
            $data['sessionlist'] = is_array($session_result) ? $session_result : array();

            // Get regular fee types with error handling
            $feetype = $this->feetype_model->get();
            if (!is_array($feetype)) {
                $feetype = array();
            }
            $tnumber = count($feetype);
            $feetype[$tnumber] = array('id'=>'transport_fees','type'=>'Transport Fees');

            // Get other fee types with enhanced error handling
            $other_feetype = array();
            if (file_exists(APPPATH . 'models/Feetypeadding_model.php')) {
                try {
                    $this->load->model('feetypeadding_model');
                    $other_feetype = $this->feetypeadding_model->get();
                    if (!is_array($other_feetype)) {
                        $other_feetype = array();
                    }
                } catch (Exception $e) {
                    // If other fee types can't be loaded, continue with empty array
                    $other_feetype = array();
                }
            }

            // Combine both fee types safely
            $combined_feetype = array_merge($feetype, $other_feetype);

            $data['feetypeList'] = $combined_feetype;
            $this->session->set_userdata('top_menu', 'Reports');
            $this->session->set_userdata('sub_menu', 'Reports/finance');
            $this->session->set_userdata('subsub_menu', 'Reports/finance/fee_collection_report_columnwise');
            $subtotal = false;

            // Handle search_type
            $search_type = $this->input->post('search_type');
            if (!empty($search_type)) {
                $dates               = $this->customlib->get_betweendate($search_type);
                $data['search_type'] = $search_type;
            } else {
                $dates               = $this->customlib->get_betweendate('this_year');
                $data['search_type'] = '';
            }

            // Handle collect_by (array input) - SIMPLIFIED
            $collect_by_input = $this->input->post('collect_by');
            if (!empty($collect_by_input) && is_array($collect_by_input) && count(array_filter($collect_by_input)) > 0) {
                // Use only the first value to avoid array issues
                $filtered = array_filter($collect_by_input);
                $data['received_by'] = $received_by = reset($filtered); // Get first value only
            } else {
                $data['received_by'] = $received_by = '';
            }

            // Handle feetype_id (array input) - SIMPLIFIED
            $feetype_input = $this->input->post('feetype_id');
            if (!empty($feetype_input) && is_array($feetype_input) && count(array_filter($feetype_input)) > 0) {
                // Use only the first value to avoid array issues
                $filtered = array_filter($feetype_input);
                $feetype_id = reset($filtered); // Get first value only
            } else {
                $feetype_id = "";
            }

            // Handle group
            $group_input = $this->input->post('group');
            if (!empty($group_input)) {
                $data['group_byid'] = $group = $group_input;
                $subtotal           = true;
            } else {
                $data['group_byid'] = $group = '';
            }

            $this->console_log('CONTROLLER: Processing date range', $dates);
            $collect_by = array();
            $collection = array();
            $start_date = date('Y-m-d', strtotime($dates['from_date']));
            $end_date   = date('Y-m-d', strtotime($dates['to_date']));
            $this->console_log('CONTROLLER: Date range processed', array('start_date' => $start_date, 'end_date' => $end_date));

            $this->console_log('CONTROLLER: Processing form arrays');

            // Handle class_id array input properly
            $class_input = $this->input->post('class_id');
            $this->console_log('CONTROLLER: Raw class_input', array('value' => $class_input, 'type' => gettype($class_input), 'is_array' => is_array($class_input)));
            
            if (!empty($class_input) && is_array($class_input) && count(array_filter($class_input)) > 0) {
                $class_id = array_filter($class_input); // Keep as array for filtering
                $data['selected_class'] = $class_id;
                $this->console_log('CONTROLLER: class_id processed as array', $class_id);
            } else {
                $class_id = null; // null means show all classes
                $data['selected_class'] = '';
                $this->console_log('CONTROLLER: class_id set to null (show all)');
            }

            // Handle section_id array input properly  
            $section_input = $this->input->post('section_id');
            $this->console_log('CONTROLLER: Raw section_input', array('value' => $section_input, 'type' => gettype($section_input), 'is_array' => is_array($section_input)));
            
            if (!empty($section_input) && is_array($section_input) && count(array_filter($section_input)) > 0) {
                $section_id = array_filter($section_input); // Keep as array for filtering
                $data['selected_section'] = $section_id;
                $this->console_log('CONTROLLER: section_id processed as array', $section_id);
            } else {
                $section_id = null; // null means show all sections
                $data['selected_section'] = '';
                $this->console_log('CONTROLLER: section_id set to null (show all)');
            }

            $this->console_log('CONTROLLER: Setting form validation rules');
            $this->form_validation->set_rules('search_type', $this->lang->line('search_duration'), 'trim|required|xss_clean');

            $data['classlist'] = $this->class_model->get();

            // Debug POST data
            $this->safe_log('debug', 'POST data received: ' . print_r($_POST, true));
            
            // Debug converted values
            $this->safe_log('debug', 'Converted values - class_id: ' . (is_array($class_id) ? json_encode($class_id) : ($class_id ?? 'null')) . ', section_id: ' . (is_array($section_id) ? json_encode($section_id) : ($section_id ?? 'null')));

            $this->console_log('CONTROLLER: Running form validation');
            // Enhanced form validation with error handling
            try {
                $form_valid = $this->form_validation->run();
                $this->safe_log('debug', 'Form validation result: ' . ($form_valid ? 'PASSED' : 'FAILED'));
                $this->console_log('CONTROLLER: Form validation result', $form_valid ? 'PASSED' : 'FAILED');
            } catch (Exception $validation_error) {
                $this->safe_log('error', 'Form validation error: ' . $validation_error->getMessage());
                $this->console_log('CONTROLLER: Form validation error', $validation_error->getMessage());
                $form_valid = false;
            }

            if ($form_valid == false) {
                $this->console_log('CONTROLLER: Form validation failed, returning empty results');
                $data['results'] = array();
                $data['fee_types'] = array();
                $this->safe_log('debug', 'Form validation failed: ' . validation_errors());
            } else {
                $this->console_log('CONTROLLER: Form validation passed, processing request');
                
                // Handle session_id array input properly  
                $session_input = $this->input->post('sch_session_id');
                $this->console_log('CONTROLLER: Raw session_input', array('value' => $session_input, 'type' => gettype($session_input)));
                
                if (!empty($session_input) && is_array($session_input) && count(array_filter($session_input)) > 0) {
                    $session_id = array_filter($session_input); // Keep as array for filtering
                    $this->console_log('CONTROLLER: session_id processed as array', $session_id);
                } else {
                    $session_id = null; // null means show all sessions
                    $this->console_log('CONTROLLER: session_id set to null (show all)');
                }

                $this->console_log('CONTROLLER: Preparing to call model methods');
                // Get fee collection data for column-wise display with enhanced error handling
                try {
                    // Validate required parameters
                    if (empty($start_date) || empty($end_date)) {
                        throw new Exception('Invalid date range provided');
                    }

                    $this->console_log('CONTROLLER: Final parameters for model call', array(
                        'start_date' => $start_date,
                        'end_date' => $end_date,
                        'feetype_id' => $feetype_id,
                        'received_by' => $received_by,
                        'group' => $group,
                        'class_id' => $class_id,
                        'section_id' => $section_id,
                        'session_id' => $session_id
                    ));

                    // Log the parameters being passed to the model
                    $this->safe_log('debug', 'Calling getFeeCollectionReportColumnwise with: start_date=' . $start_date . ', end_date=' . $end_date . ', class_id=' . (is_array($class_id) ? json_encode($class_id) : ($class_id ?? 'null')) . ', section_id=' . (is_array($section_id) ? json_encode($section_id) : ($section_id ?? 'null')) . ', session_id=' . (is_array($session_id) ? json_encode($session_id) : ($session_id ?? 'null')));

                    $this->console_log('CONTROLLER: Calling getFeeCollectionReport (test)');
                    // Try the main query first to isolate the error
                    try {
                        $this->safe_log('debug', 'Testing getFeeCollectionReport call...');
                        $test_result = $this->studentfeemaster_model->getFeeCollectionReport($start_date, $end_date, $feetype_id, $received_by, $group, $class_id, $section_id, $session_id);
                        $this->safe_log('debug', 'getFeeCollectionReport succeeded with ' . count($test_result) . ' results');
                        $this->console_log('CONTROLLER: getFeeCollectionReport test successful', array('result_count' => count($test_result)));
                    } catch (Exception $test_error) {
                        $this->safe_log('error', 'getFeeCollectionReport failed: ' . $test_error->getMessage());
                        $this->console_log('CONTROLLER: getFeeCollectionReport test failed', $test_error->getMessage());
                        throw $test_error;
                    }

                    $this->console_log('CONTROLLER: Calling getFeeCollectionReportColumnwise');
                    $data['results'] = $this->studentfeemaster_model->getFeeCollectionReportColumnwise($start_date, $end_date, $feetype_id, $received_by, $group, $class_id, $section_id, $session_id);
                    $this->console_log('CONTROLLER: getFeeCollectionReportColumnwise completed', array('result_count' => count($data['results'])));

                    $this->console_log('CONTROLLER: Calling getFeeTypesForColumnwise');
                    // Get all fee types for column headers
                    $data['fee_types'] = $this->studentfeemaster_model->getFeeTypesForColumnwise($start_date, $end_date, $feetype_id, $class_id, $section_id, $session_id);
                    $this->console_log('CONTROLLER: getFeeTypesForColumnwise completed', array('fee_types_count' => count($data['fee_types'])));

                    // Ensure we have valid arrays
                    if (!is_array($data['results'])) {
                        $data['results'] = array();
                    }
                    if (!is_array($data['fee_types'])) {
                        $data['fee_types'] = array();
                    }

                    // Validate data structure
                    foreach ($data['results'] as $index => $result) {
                        if (!is_array($result)) {
                            unset($data['results'][$index]);
                        }
                    }
                    $data['results'] = array_values($data['results']); // Re-index

                } catch (Exception $e) {
                    $this->safe_log('error', 'Fee collection report error: ' . $e->getMessage());
                    $data['results'] = array();
                    $data['fee_types'] = array();
                    $data['error_message'] = 'An error occurred while generating the report. Please try again.';
                }
            }
            $data['subtotal']    = $subtotal;

            $data['sch_setting'] = $this->sch_setting_detail;
            
            $this->console_log('CONTROLLER: Preparing to load views');
            $this->console_log('CONTROLLER: Data summary', array(
                'results_count' => count($data['results']),
                'fee_types_count' => count($data['fee_types']),
                'has_error' => isset($data['error_message']) ? $data['error_message'] : 'none'
            ));
            
            $this->load->view('layout/header', $data);
            $this->console_log('CONTROLLER: Header view loaded');
            
            $this->load->view('financereports/fee_collection_report_columnwise', $data);
            $this->console_log('CONTROLLER: Main view loaded');
            
            $this->load->view('layout/footer', $data);
            $this->console_log('CONTROLLER: Footer view loaded');
            $this->console_log('=== CONTROLLER: Method completed successfully ===');
        } catch (Exception $e) {
            // Log the error safely
            $this->safe_log('error', 'Fee collection report main error: ' . $e->getMessage());

            // Set default data to prevent view errors
            $data = array(
                'results' => array(),
                'fee_types' => array(),
                'error_message' => 'An error occurred while loading the report. Please try again.',
                'sch_setting' => $this->sch_setting_detail,
                'classlist' => array(),
                'sessionlist' => array(),
                'feetypeList' => array(),
                'searchlist' => array(),
                'group_by' => array(),
                'collect_by' => array(),
                'subtotal' => false,
                'selected_section' => ''
            );

            $this->load->view('layout/header', $data);
            $this->load->view('financereports/fee_collection_report_columnwise', $data);
            $this->load->view('layout/footer', $data);
        }
    }

    public function export_fee_collection_columnwise()
    {
        try {
            // Get form data - same as main report method
            $search_type = $this->input->post('search_type');
            $date_from = $this->input->post('date_from');
            $date_to = $this->input->post('date_to');
            $feetype_id = $this->input->post('feetype_id');
            $received_by = $this->input->post('received_by');
            $group = $this->input->post('group');
            $class_id = $this->input->post('class_id');
            $section_id = $this->input->post('section_id');
            $session_id = $this->input->post('sch_session_id');
            $export_format = $this->input->post('export_format');

            // Process dates same as main report method
            $dates = $this->customlib->get_betweendate($search_type, $date_from, $date_to);
            $start_date = date('Y-m-d', strtotime($dates['from_date']));
            $end_date = date('Y-m-d', strtotime($dates['to_date']));

            // Process received_by parameter same as main method
            if (isset($received_by) && $received_by != '') {
                $received_by = $received_by;
            } else {
                $received_by = "";
            }

            // Process feetype_id parameter same as main method
            if (isset($feetype_id) && $feetype_id != '') {
                $feetype_id = $feetype_id;
            } else {
                $feetype_id = "";
            }

            // Process group parameter same as main method
            if (isset($group) && $group != '') {
                $group = $group;
            } else {
                $group = '';
            }

            // Handle session_id - use current session if not provided
            if (empty($session_id)) {
                $session_id = $this->setting_model->getCurrentSession();
            }

            // Validate export format
            if (!in_array($export_format, ['pdf', 'excel', 'csv'])) {
                show_error('Invalid export format');
                return;
            }

            // Get the same data as the main report
            $data['results'] = $this->studentfeemaster_model->getFeeCollectionReportColumnwise($start_date, $end_date, $feetype_id, $received_by, $group, $class_id, $section_id, $session_id);
            $data['fee_types'] = $this->studentfeemaster_model->getFeeTypesForColumnwise($start_date, $end_date, $feetype_id, $class_id, $section_id, $session_id);

            // Get school settings
            $data['sch_setting'] = $this->sch_setting_detail;
            $data['currency_symbol'] = $this->customlib->getSchoolCurrencyFormat();

            // Set report parameters
            $data['start_date'] = $start_date;
            $data['end_date'] = $end_date;
            $data['export_format'] = $export_format;

            // If no data found, create a simple export with message
            if (empty($data['results']) && empty($data['fee_types'])) {
                $data['results'] = array();
                $data['fee_types'] = array(array('type' => 'No Data Available', 'id' => 'no_data'));
            }

            // Validate and fix data structure before export
            $data = $this->validate_export_data($data);

            // Only support CSV export
            if ($export_format == 'csv') {
                $this->export_csv_columnwise($data);
            } elseif ($export_format == 'excel') {
                $this->export_excel_columnwise($data);
            } else {
                show_error('Only CSV and Excel export are supported');
            }

        } catch (Exception $e) {
            show_error('An error occurred while exporting the report: ' . $e->getMessage());
        }
    }

    /**
     * Validate and fix export data structure to ensure CSV export works correctly
     */
    private function validate_export_data($data)
    {
        // Ensure results is an array
        if (!isset($data['results']) || !is_array($data['results'])) {
            $data['results'] = array();
        }

        // Ensure fee_types is an array
        if (!isset($data['fee_types']) || !is_array($data['fee_types'])) {
            $data['fee_types'] = array();
        }

        // Validate each student record
        foreach ($data['results'] as $index => $student) {
            if (!is_array($student)) {
                unset($data['results'][$index]);
                continue;
            }

            // Ensure required fields exist
            $required_fields = array('admission_no', 'firstname', 'lastname', 'class', 'section');
            foreach ($required_fields as $field) {
                if (!isset($student[$field])) {
                    $data['results'][$index][$field] = '';
                }
            }

            // Ensure fee_types exists and is an array
            if (!isset($student['fee_types']) || !is_array($student['fee_types'])) {
                $data['results'][$index]['fee_types'] = array();
            }
        }

        // Re-index results array to remove gaps
        $data['results'] = array_values($data['results']);

        return $data;
    }

    private function export_excel_columnwise($data)
    {
        // Generate filename
        $filename = 'Fee_Collection_Report_Columnwise_' . date('Y-m-d_H-i-s') . '.xls';

        // Set headers for Excel download
        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Build Excel HTML content
        echo $this->build_excel_content($data);
        exit;
    }

    private function build_excel_content($data)
    {
        $currency_symbol = $this->customlib->getSchoolCurrencyFormat();
        
        $html = '<html>';
        $html .= '<head><meta charset="UTF-8"></head>';
        $html .= '<body>';
        $html .= '<table border="1" style="border-collapse: collapse; width: 100%; font-family: Arial, sans-serif;">';
        
        // Title row
        $html .= '<tr><td colspan="20" style="text-align: center; font-weight: bold; font-size: 16px; padding: 10px;">Fee Collection Report Column Wise</td></tr>';
        $html .= '<tr><td colspan="20"></td></tr>'; // Empty row

        // Date range
        if (!empty($data['start_date']) && !empty($data['end_date'])) {
            $html .= '<tr><td colspan="20" style="text-align: center; font-weight: bold;">Period: ' . date('d-M-Y', strtotime($data['start_date'])) . ' to ' . date('d-M-Y', strtotime($data['end_date'])) . '</td></tr>';
            $html .= '<tr><td colspan="20"></td></tr>'; // Empty row
        }

        // Headers
        $html .= '<tr style="background-color: #f8f9fa; font-weight: bold;">';
        $html .= '<td style="border: 1px solid #333; padding: 8px; text-align: center;">S.No</td>';
        $html .= '<td style="border: 1px solid #333; padding: 8px; text-align: center;">Admission No</td>';
        $html .= '<td style="border: 1px solid #333; padding: 8px; text-align: center;">Student Name</td>';
        $html .= '<td style="border: 1px solid #333; padding: 8px; text-align: center;">Phone</td>';
        $html .= '<td style="border: 1px solid #333; padding: 8px; text-align: center;">Class</td>';
        $html .= '<td style="border: 1px solid #333; padding: 8px; text-align: center;">Section</td>';

        // Dynamic fee type headers
        $fee_types = isset($data['fee_types']) ? $data['fee_types'] : array();
        foreach ($fee_types as $fee_type) {
            $html .= '<td style="border: 1px solid #333; padding: 8px; text-align: center; background-color: #e8f4fd;">' . htmlspecialchars($fee_type['type']) . '</td>';
        }
        $html .= '<td style="border: 1px solid #333; padding: 8px; text-align: center; background-color: #d4edda;">Grand Total</td>';
        $html .= '</tr>';

        // Data rows
        $results = isset($data['results']) ? $data['results'] : array();
        $grand_total = 0;
        $grand_totals_by_type = array();
        
        // Initialize grand totals for each fee type
        foreach ($fee_types as $fee_type) {
            $grand_totals_by_type[$fee_type['type']] = 0;
        }

        if (isset($results) && is_array($results)) {
            $row_count = 0;
            foreach ($results as $student) {
                $row_count++;
                $student_total = 0;

                $html .= '<tr>';
                $html .= '<td style="border: 1px solid #333; padding: 4px; text-align: center;">' . $row_count . '</td>';
                $html .= '<td style="border: 1px solid #333; padding: 4px;">' . htmlspecialchars(isset($student['admission_no']) ? $student['admission_no'] : '') . '</td>';
                
                $student_name = $this->customlib->getFullName(
                    isset($student['firstname']) ? $student['firstname'] : '',
                    isset($student['middlename']) ? $student['middlename'] : '',
                    isset($student['lastname']) ? $student['lastname'] : '',
                    $data['sch_setting']->middlename,
                    $data['sch_setting']->lastname
                );
                $html .= '<td style="border: 1px solid #333; padding: 4px;">' . htmlspecialchars($student_name) . '</td>';
                $html .= '<td style="border: 1px solid #333; padding: 4px;">' . htmlspecialchars(isset($student['guardian_phone']) ? $student['guardian_phone'] : '') . '</td>';
                $html .= '<td style="border: 1px solid #333; padding: 4px;">' . htmlspecialchars(isset($student['class']) ? $student['class'] : '') . '</td>';
                $html .= '<td style="border: 1px solid #333; padding: 4px;">' . htmlspecialchars(isset($student['section']) ? $student['section'] : '') . '</td>';

                // Dynamic fee type columns
                foreach ($fee_types as $fee_type) {
                    $fee_type_name = $fee_type['type'];
                    $fee_data = isset($student['fee_types'][$fee_type_name]) ? $student['fee_types'][$fee_type_name] : array(
                        'total_amount' => 0,
                        'paid_amount' => 0,
                        'remaining_amount' => 0
                    );

                    if (is_numeric($fee_data)) {
                        $paid_amount = $fee_data;
                    } else {
                        $paid_amount = isset($fee_data['paid_amount']) ? $fee_data['paid_amount'] : 0;
                    }

                    $html .= '<td style="border: 1px solid #333; padding: 4px; text-align: right;">' . $currency_symbol . number_format($paid_amount, 0) . '</td>';
                    $student_total += $paid_amount;
                    $grand_totals_by_type[$fee_type_name] += $paid_amount;
                }

                $html .= '<td style="border: 1px solid #333; padding: 4px; text-align: right; font-weight: bold; background-color: #fff3cd;">' . $currency_symbol . number_format($student_total, 0) . '</td>';
                $html .= '</tr>';
                
                $grand_total += $student_total;
            }
        }

        // Grand totals row
        $html .= '<tr style="background-color: #e8f4fd; font-weight: bold;">';
        $html .= '<td colspan="6" style="border: 1px solid #333; padding: 8px; text-align: center;">Grand Total</td>';
        foreach ($fee_types as $fee_type) {
            $html .= '<td style="border: 1px solid #333; padding: 4px; text-align: right;">' . $currency_symbol . number_format($grand_totals_by_type[$fee_type['type']], 0) . '</td>';
        }
        $html .= '<td style="border: 1px solid #333; padding: 4px; text-align: right; background-color: #d4edda;">' . $currency_symbol . number_format($grand_total, 0) . '</td>';
        $html .= '</tr>';

        $html .= '</table>';
        $html .= '</body></html>';

        return $html;
    }

    private function export_csv_columnwise($data)
    {
        // Generate filename
        $filename = 'Fee_Collection_Report_Columnwise_' . date('Y-m-d_H-i-s') . '.csv';

        // Set headers for download with UTF-8 encoding
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Open output stream
        $output = fopen('php://output', 'w');

        // Add UTF-8 BOM to handle special characters properly
        fwrite($output, "\xEF\xBB\xBF");

        // Build CSV content
        $this->build_csv_content($output, $data);

        fclose($output);
        exit;
    }





    private function build_csv_content($output, $data)
    {
        // Use Rs. instead of currency symbol to avoid encoding issues
        $currency_symbol = 'Rs.';



        // Title row
        fputcsv($output, array('Fee Collection Report Column Wise'));
        fputcsv($output, array()); // Empty row

        // Date range
        if (!empty($data['start_date']) && !empty($data['end_date'])) {
            fputcsv($output, array('Period: ' . date('d-M-Y', strtotime($data['start_date'])) . ' to ' . date('d-M-Y', strtotime($data['end_date']))));
            fputcsv($output, array()); // Empty row
        }

        // Headers - Use EXACT same logic as frontend view
        $headers = array('S.No', 'Admission No', 'Student Name', 'Phone', 'Class', 'Section');

        // Mirror the frontend logic: foreach ($fee_types as $fee_type) with 5 sub-columns each
        $fee_types = isset($data['fee_types']) ? $data['fee_types'] : array();

        foreach ($fee_types as $fee_type) {
            $fee_type_name = $fee_type['type'];
            $headers[] = $fee_type_name . ' - Total';
            $headers[] = $fee_type_name . ' - Fine';
            $headers[] = $fee_type_name . ' - Discount';
            $headers[] = $fee_type_name . ' - Paid';
            $headers[] = $fee_type_name . ' - Balance';
        }
        $headers[] = 'Grand Total';
        fputcsv($output, $headers);



        // Data rows - Use EXACT same logic as frontend view
        $results = isset($data['results']) ? $data['results'] : array();


        if (isset($results) && is_array($results)) {
            $row_count = 0;
            foreach ($results as $student) {
                $row_data = array();
                $student_total = 0;
                $row_count++;

                // Basic student information - same as frontend
                $row_data[] = $row_count; // S.No
                $row_data[] = isset($student['admission_no']) ? $student['admission_no'] : '';
                $row_data[] = $this->customlib->getFullName(
                    isset($student['firstname']) ? $student['firstname'] : '',
                    isset($student['middlename']) ? $student['middlename'] : '',
                    isset($student['lastname']) ? $student['lastname'] : '',
                    $data['sch_setting']->middlename,
                    $data['sch_setting']->lastname
                );
                $row_data[] = isset($student['mobileno']) ? $student['mobileno'] : (isset($student['guardian_phone']) ? $student['guardian_phone'] : '');
                $row_data[] = isset($student['class']) ? $student['class'] : '';
                $row_data[] = isset($student['section']) ? $student['section'] : '';

                // Mirror frontend logic: foreach ($fee_types as $fee_type) with 5 columns each
                foreach ($fee_types as $fee_type) {
                    $fee_type_name = $fee_type['type'];
                    $fee_data = isset($student['fee_types'][$fee_type_name]) ? $student['fee_types'][$fee_type_name] : array(
                        'total_amount' => 0,
                        'fine_amount' => 0,
                        'discount_amount' => 0,
                        'paid_amount' => 0,
                        'remaining_amount' => 0
                    );

                    // Handle old format (just amount) vs new format (detailed data) - EXACT same logic as frontend
                    if (is_numeric($fee_data)) {
                        $total_amount = $fee_data;
                        $fine_amount = 0;
                        $discount_amount = 0;
                        $paid_amount = $fee_data;
                        $remaining_amount = 0;
                    } else {
                        $total_amount = isset($fee_data['total_amount']) ? $fee_data['total_amount'] : 0;
                        $fine_amount = isset($fee_data['fine_amount']) ? $fee_data['fine_amount'] : 0;
                        $discount_amount = isset($fee_data['discount_amount']) ? $fee_data['discount_amount'] : 0;
                        $paid_amount = isset($fee_data['paid_amount']) ? $fee_data['paid_amount'] : 0;
                        $remaining_amount = isset($fee_data['remaining_amount']) ? $fee_data['remaining_amount'] : 0;
                    }

                    // Add the 5 columns for each fee type (matching table UI exactly)
                    $row_data[] = $total_amount > 0 ? $currency_symbol . number_format($total_amount, 2) : '-';
                    $row_data[] = $fine_amount > 0 ? $currency_symbol . number_format($fine_amount, 2) : '-';
                    $row_data[] = $discount_amount > 0 ? $currency_symbol . number_format($discount_amount, 2) : '-';
                    $row_data[] = $paid_amount > 0 ? $currency_symbol . number_format($paid_amount, 2) : '-';
                    $row_data[] = $remaining_amount != 0 ? $currency_symbol . number_format($remaining_amount, 2) : ($total_amount > 0 ? $currency_symbol . '0.00' : '-');
                    
                    $student_total += $paid_amount;


                }

                // Grand total for student
                $row_data[] = $currency_symbol . ' ' . number_format($student_total, 0);



                fputcsv($output, $row_data);
            }

        }

        // Add grand totals
        fputcsv($output, array()); // Empty row

        // Calculate totals - EXACT same logic as frontend view
        $total_by_type = array();
        foreach ($fee_types as $fee_type) {
            $total_by_type[$fee_type['type']] = array(
                'total_amount' => 0,
                'paid_amount' => 0,
                'remaining_amount' => 0
            );
        }

        if (isset($results) && is_array($results)) {
            foreach ($results as $student) {
                foreach ($fee_types as $fee_type) {
                    $fee_data = isset($student['fee_types'][$fee_type['type']]) ? $student['fee_types'][$fee_type['type']] : array(
                        'total_amount' => 0,
                        'paid_amount' => 0,
                        'remaining_amount' => 0,
                        'payments' => array()
                    );

                    // Handle old format (just amount) vs new format (detailed data) - EXACT same as frontend
                    if (is_numeric($fee_data)) {
                        $paid_amount = $fee_data;
                        $total_amount = $fee_data;
                        $remaining_amount = 0;
                    } else {
                        $paid_amount = $fee_data['paid_amount'];
                        $total_amount = $fee_data['total_amount'];
                        $remaining_amount = $fee_data['remaining_amount'];
                    }

                    $total_by_type[$fee_type['type']]['total_amount'] += $total_amount;
                    $total_by_type[$fee_type['type']]['paid_amount'] += $paid_amount;
                    $total_by_type[$fee_type['type']]['remaining_amount'] += $remaining_amount;
                }
            }
        }

        // Grand totals rows - EXACT same logic as frontend view
        $grand_total_amount = 0;
        $grand_paid_amount = 0;
        $grand_remaining_amount = 0;

        foreach ($fee_types as $fee_type) {
            $type_totals = $total_by_type[$fee_type['type']];
            $grand_total_amount += $type_totals['total_amount'];
            $grand_paid_amount += $type_totals['paid_amount'];
            $grand_remaining_amount += $type_totals['remaining_amount'];
        }

        // Grand Total (Total Assigned) Row - same as frontend
        $grand_total_assigned = array('Grand Total (Assigned)', '', '', '');
        foreach ($fee_types as $fee_type) {
            $type_totals = $total_by_type[$fee_type['type']];
            $grand_total_assigned[] = $currency_symbol . number_format($type_totals['total_amount'], 0);
        }
        $grand_total_assigned[] = $currency_symbol . number_format($grand_total_amount, 0);
        fputcsv($output, $grand_total_assigned);

        // Grand Paid (Total Collected) Row - same as frontend
        $grand_total_paid = array('Grand Paid (Collected)', '', '', '');
        foreach ($fee_types as $fee_type) {
            $type_totals = $total_by_type[$fee_type['type']];
            $grand_total_paid[] = $currency_symbol . number_format($type_totals['paid_amount'], 0);
        }
        $grand_total_paid[] = $currency_symbol . number_format($grand_paid_amount, 0);
        fputcsv($output, $grand_total_paid);

        // Grand Remaining (Total Pending) Row - same as frontend
        $grand_total_remaining = array('Grand Remaining (Pending)', '', '', '');
        foreach ($fee_types as $fee_type) {
            $type_totals = $total_by_type[$fee_type['type']];
            $grand_total_remaining[] = $currency_symbol . number_format($type_totals['remaining_amount'], 0);
        }
        $grand_total_remaining[] = $currency_symbol . number_format($grand_remaining_amount, 0);
        fputcsv($output, $grand_total_remaining);

        // Add summary information
        fputcsv($output, array()); // Empty row
        fputcsv($output, array('Export Summary:'));
        fputcsv($output, array('Total Students: ' . count($results)));
        fputcsv($output, array('Total Fee Types: ' . count($fee_types)));
        fputcsv($output, array('Export Date: ' . date('Y-m-d H:i:s')));


    }

    /**
     * Export fee collection report in Excel format matching table UI exactly
     */
    private function export_excel_columnwise($data)
    {
        // Generate filename
        $filename = 'Fee_Collection_Report_Columnwise_' . date('Y-m-d_H-i-s') . '.xls';

        // Set headers for Excel download
        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Generate Excel content
        echo $this->build_excel_content($data);
        exit;
    }

    /**
     * Build Excel content with exact table structure matching UI
     */
    private function build_excel_content($data)
    {
        $currency_symbol = 'Rs.';
        
        // Start HTML for Excel
        $html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        $html .= '<head><meta charset="utf-8"><meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
        $html .= '<style>';
        $html .= 'table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; }';
        $html .= 'th, td { border: 1px solid #333; padding: 4px; text-align: center; font-size: 10px; }';
        $html .= 'th { background-color: #f8f9fa; font-weight: bold; }';
        $html .= '.student-info { background-color: #f9f9f9; }';
        $html .= '.fee-header { background-color: #3498db; color: white; }';
        $html .= '.fee-subheader { background-color: #ecf0f1; color: #2c3e50; }';
        $html .= '.grand-total { background-color: #e74c3c; color: white; }';
        $html .= '.total-row { background-color: #e8f4fd; font-weight: bold; }';
        $html .= '</style></head><body>';

        // Title
        $html .= '<h3 style="text-align: center;">Fee Collection Report - Column Wise</h3>';
        
        // Date range
        if (!empty($data['start_date']) && !empty($data['end_date'])) {
            $html .= '<p style="text-align: center;">Period: ' . date('d-M-Y', strtotime($data['start_date'])) . ' to ' . date('d-M-Y', strtotime($data['end_date'])) . '</p>';
        }

        $html .= '<table>';

        // Headers - First row (Fee Type Groups)
        $html .= '<tr>';
        $html .= '<th rowspan="2" class="student-info">S.No</th>';
        $html .= '<th rowspan="2" class="student-info">Admission No</th>';
        $html .= '<th rowspan="2" class="student-info">Student Name</th>';
        $html .= '<th rowspan="2" class="student-info">Phone</th>';
        $html .= '<th rowspan="2" class="student-info">Class</th>';
        $html .= '<th rowspan="2" class="student-info">Section</th>';

        // Dynamic fee type headers
        $fee_types = isset($data['fee_types']) ? $data['fee_types'] : array();
        foreach ($fee_types as $fee_type) {
            $html .= '<th colspan="5" class="fee-header">' . htmlspecialchars($fee_type['type']) . '</th>';
        }
        $html .= '<th rowspan="2" class="grand-total">Grand Total</th>';
        $html .= '</tr>';

        // Second header row (Sub-columns)
        $html .= '<tr>';
        foreach ($fee_types as $fee_type) {
            $html .= '<th class="fee-subheader">Total</th>';
            $html .= '<th class="fee-subheader">Fine</th>';
            $html .= '<th class="fee-subheader">Discount</th>';
            $html .= '<th class="fee-subheader">Paid</th>';
            $html .= '<th class="fee-subheader">Balance</th>';
        }
        $html .= '</tr>';

        // Data rows
        $results = isset($data['results']) ? $data['results'] : array();
        $grand_totals_by_type = array();
        $overall_grand_total = 0;
        
        // Initialize grand totals
        foreach ($fee_types as $fee_type) {
            $grand_totals_by_type[$fee_type['type']] = array(
                'total' => 0, 'fine' => 0, 'discount' => 0, 'paid' => 0, 'balance' => 0
            );
        }

        if (isset($results) && is_array($results)) {
            $row_count = 0;
            foreach ($results as $student) {
                $row_count++;
                $student_grand_total = 0;

                $html .= '<tr>';
                $html .= '<td>' . $row_count . '</td>';
                $html .= '<td>' . htmlspecialchars(isset($student['admission_no']) ? $student['admission_no'] : '') . '</td>';
                
                $student_name = $this->customlib->getFullName(
                    isset($student['firstname']) ? $student['firstname'] : '',
                    isset($student['middlename']) ? $student['middlename'] : '',
                    isset($student['lastname']) ? $student['lastname'] : '',
                    $data['sch_setting']->middlename,
                    $data['sch_setting']->lastname
                );
                $html .= '<td style="text-align: left;">' . htmlspecialchars($student_name) . '</td>';
                $html .= '<td>' . htmlspecialchars(isset($student['mobileno']) ? $student['mobileno'] : (isset($student['guardian_phone']) ? $student['guardian_phone'] : '')) . '</td>';
                $html .= '<td>' . htmlspecialchars(isset($student['class']) ? $student['class'] : '') . '</td>';
                $html .= '<td>' . htmlspecialchars(isset($student['section']) ? $student['section'] : '') . '</td>';

                // Dynamic fee type columns (5 per fee type)
                foreach ($fee_types as $fee_type) {
                    $fee_type_name = $fee_type['type'];
                    $fee_data = isset($student['fee_types'][$fee_type_name]) ? $student['fee_types'][$fee_type_name] : array(
                        'total_amount' => 0,
                        'fine_amount' => 0,
                        'discount_amount' => 0,
                        'paid_amount' => 0,
                        'remaining_amount' => 0
                    );

                    // Handle different data formats
                    if (is_numeric($fee_data)) {
                        $total_amount = $fee_data;
                        $fine_amount = 0;
                        $discount_amount = 0;
                        $paid_amount = $fee_data;
                        $remaining_amount = 0;
                    } else {
                        $total_amount = isset($fee_data['total_amount']) ? $fee_data['total_amount'] : 0;
                        $fine_amount = isset($fee_data['fine_amount']) ? $fee_data['fine_amount'] : 0;
                        $discount_amount = isset($fee_data['discount_amount']) ? $fee_data['discount_amount'] : 0;
                        $paid_amount = isset($fee_data['paid_amount']) ? $fee_data['paid_amount'] : 0;
                        $remaining_amount = isset($fee_data['remaining_amount']) ? $fee_data['remaining_amount'] : 0;
                    }

                    // Add 5 columns for each fee type
                    $html .= '<td style="text-align: right;">' . ($total_amount > 0 ? $currency_symbol . number_format($total_amount, 2) : '-') . '</td>';
                    $html .= '<td style="text-align: right;">' . ($fine_amount > 0 ? $currency_symbol . number_format($fine_amount, 2) : '-') . '</td>';
                    $html .= '<td style="text-align: right;">' . ($discount_amount > 0 ? $currency_symbol . number_format($discount_amount, 2) : '-') . '</td>';
                    $html .= '<td style="text-align: right;">' . ($paid_amount > 0 ? $currency_symbol . number_format($paid_amount, 2) : '-') . '</td>';
                    $html .= '<td style="text-align: right;">' . ($remaining_amount != 0 ? $currency_symbol . number_format($remaining_amount, 2) : ($total_amount > 0 ? $currency_symbol . '0.00' : '-')) . '</td>';

                    // Add to grand totals
                    $grand_totals_by_type[$fee_type_name]['total'] += $total_amount;
                    $grand_totals_by_type[$fee_type_name]['fine'] += $fine_amount;
                    $grand_totals_by_type[$fee_type_name]['discount'] += $discount_amount;
                    $grand_totals_by_type[$fee_type_name]['paid'] += $paid_amount;
                    $grand_totals_by_type[$fee_type_name]['balance'] += $remaining_amount;
                    
                    $student_grand_total += $paid_amount;
                }

                $html .= '<td style="text-align: right; font-weight: bold;">' . $currency_symbol . number_format($student_grand_total, 2) . '</td>';
                $html .= '</tr>';
                
                $overall_grand_total += $student_grand_total;
            }
        }

        // Grand totals row
        $html .= '<tr class="total-row">';
        $html .= '<td colspan="6" style="text-align: center; font-weight: bold;">Grand Total</td>';
        $total_paid_all_types = 0;
        foreach ($fee_types as $fee_type) {
            $totals = $grand_totals_by_type[$fee_type['type']];
            $html .= '<td style="text-align: right;">' . $currency_symbol . number_format($totals['total'], 2) . '</td>';
            $html .= '<td style="text-align: right;">' . $currency_symbol . number_format($totals['fine'], 2) . '</td>';
            $html .= '<td style="text-align: right;">' . $currency_symbol . number_format($totals['discount'], 2) . '</td>';
            $html .= '<td style="text-align: right;">' . $currency_symbol . number_format($totals['paid'], 2) . '</td>';
            $html .= '<td style="text-align: right;">' . $currency_symbol . number_format($totals['balance'], 2) . '</td>';
            $total_paid_all_types += $totals['paid'];
        }
        $html .= '<td style="text-align: right; font-weight: bold; background-color: #d4edda;">' . $currency_symbol . number_format($total_paid_all_types, 2) . '</td>';
        $html .= '</tr>';

        $html .= '</table>';
        $html .= '</body></html>';

        return $html;
    }

























    public function onlinefees_report()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/finance');
        $this->session->set_userdata('subsub_menu', 'Reports/finance/onlinefees_report');
        $data['searchlist'] = $this->customlib->get_searchtype();
        $data['group_by']   = $this->customlib->get_groupby();

        if (isset($_POST['search_type']) && $_POST['search_type'] != '') {

            $dates               = $this->customlib->get_betweendate($_POST['search_type']);
            $data['search_type'] = $_POST['search_type'];
        } else {

            $dates               = $this->customlib->get_betweendate('this_year');
            $data['search_type'] = '';
        }

        $collection = array();
        $start_date = date('Y-m-d', strtotime($dates['from_date']));
        $end_date   = date('Y-m-d', strtotime($dates['to_date']));
        $this->form_validation->set_rules('search_type', $this->lang->line('search_type'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $data['collectlist'] = array();
        } else {
            $data['collectlist'] = $this->studentfeemaster_model->getOnlineFeeCollectionReport($start_date, $end_date);
        }

        $data['sch_setting'] = $this->sch_setting_detail;
        $this->load->view('layout/header', $data);
        $this->load->view('financereports/onlineFeesReport', $data);
        $this->load->view('layout/footer', $data);
    }

    public function duefeesremark()
    {
        if (!$this->rbac->hasPrivilege('balance_fees_report_with_remark', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/finance');
        $this->session->set_userdata('subsub_menu', 'Reports/finance/duefeesremark');
        $data                = array();
        $data['title']       = 'student fees';
        $class               = $this->class_model->get();
        $data['classlist']   = $class;
        $data['sch_setting'] = $this->sch_setting_detail;
        $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('section_id', $this->lang->line('section'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == true) {
            $date               = date('Y-m-d');
            $class_id           = $this->input->post('class_id');
            $section_id         = $this->input->post('section_id');
            $data['class_id']   = $class_id;
            $data['section_id'] = $section_id;
            $date               = date('Y-m-d');
            $student_due_fee    = $this->studentfee_model->getDueStudentFeesByDateClassSection($class_id, $section_id, $date);
            $students = array();
            if (!empty($student_due_fee)) {
                foreach ($student_due_fee as $student_due_fee_key => $student_due_fee_value) {

                    $amt_due = ($student_due_fee_value['is_system']) ? $student_due_fee_value['previous_balance_amount'] : $student_due_fee_value['amount'];

                       $a = json_decode($student_due_fee_value['amount_detail']);


                    if (!empty($a)) {
                        $amount          = 0;
                        $amount_discount = 0;
                        $amount_fine     = 0;

                        foreach ($a as $a_key => $a_value) {
                            $amount          = $amount + $a_value->amount;
                            $amount_discount = $amount_discount + $a_value->amount_discount;
                            $amount_fine     = $amount_fine + $a_value->amount_fine;
                        }
                        if ($amt_due <= ($amount + $amount_discount)) {
                            unset($student_due_fee[$student_due_fee_key]);
                        } else {

                            if (!array_key_exists($student_due_fee_value['student_session_id'], $students)) {
                                $students[$student_due_fee_value['student_session_id']] = $this->add_new_student($student_due_fee_value);
                            }

                            $students[$student_due_fee_value['student_session_id']]['fees'][] = array(
                                'is_system'=>$student_due_fee_value['is_system'],
                                'amount'          => $amt_due,
                                'amount_deposite' => $amount,
                                'amount_discount' => $amount_discount,
                                'amount_fine'     => $amount_fine,
                                'fee_group'       => $student_due_fee_value['fee_group'],
                                'fee_type'        => $student_due_fee_value['fee_type'],
                                'fee_code'        => $student_due_fee_value['fee_code'],

                            );

                        }
                    } else {
                        $amount          = 0;
                        $amount_discount = 0;

                        if ($amt_due <= ($amount + $amount_discount)) {
                            unset($student_due_fee[$student_due_fee_key]);
                        } else {
                            if (!array_key_exists($student_due_fee_value['student_session_id'], $students)) {

                                $students[$student_due_fee_value['student_session_id']] = $this->add_new_student($student_due_fee_value);
                            }
                            $students[$student_due_fee_value['student_session_id']]['fees'][] = array(
                                'is_system'=>$student_due_fee_value['is_system'],
                                'amount'          => $amt_due,
                                'amount_deposite' => 0,
                                'amount_discount' => 0,
                                'amount_fine'     => 0,
                                'fee_group'       => $student_due_fee_value['fee_group'],
                                'fee_type'        => $student_due_fee_value['fee_type'],
                                'fee_code'        => $student_due_fee_value['fee_code'],
                            );

                        }
                    }


                }

            }

            $data['student_remain_fees'] = $students;

        }
  $data['start_month'] = $this->sch_setting_detail->start_month;
        $this->load->view('layout/header', $data);
        $this->load->view('financereports/duefeesremark', $data);
        $this->load->view('layout/footer', $data);
    }

    public function add_new_student($student)
    {
        $new_student = array(
            'id'                 => $student['id'],
            'student_session_id' => $student['student_session_id'],
            'class'              => $student['class'],
            'section_id'         => $student['section_id'],
            'section'            => $student['section'],
            'admission_no'       => $student['admission_no'],
            'roll_no'            => $student['roll_no'],
            'admission_date'     => $student['admission_date'],
            'firstname'          => $student['firstname'],
            'middlename'         => $student['middlename'],
            'lastname'           => $student['lastname'],
            'image'              => $student['image'],
            'mobileno'           => $student['mobileno'],
            'email'              => $student['email'],
            'state'              => $student['state'],
            'city'               => $student['city'],
            'pincode'            => $student['pincode'],
            'religion'           => $student['religion'],
            'dob'                => $student['dob'],
            'current_address'    => $student['current_address'],
            'permanent_address'  => $student['permanent_address'],
            'category_id'        => $student['category_id'],
            'category'           => $student['category'],
            'adhar_no'           => $student['adhar_no'],
            'samagra_id'         => $student['samagra_id'],
            'bank_account_no'    => $student['bank_account_no'],
            'bank_name'          => $student['bank_name'],
            'ifsc_code'          => $student['ifsc_code'],
            'guardian_name'      => $student['guardian_name'],
            'guardian_relation'  => $student['guardian_relation'],
            'guardian_phone'     => $student['guardian_phone'],
            'guardian_address'   => $student['guardian_address'],
            'is_active'          => $student['is_active'],
            'father_name'        => $student['father_name'],
            'rte'                => $student['rte'],
            'gender'             => $student['gender'],

        );
        return $new_student;
    }

    public function printduefeesremark()
    {
        if (!$this->rbac->hasPrivilege('fees_statement', 'can_view')) {
            access_denied();
        }

        $date                = date('Y-m-d');
        $class_id            = $this->input->post('class_id');
        $section_id          = $this->input->post('section_id');
        $data['class_id']    = $class_id;
        $data['section_id']  = $section_id;
        $data['class']       = $this->class_model->get($class_id);
        $data['section']     = $this->section_model->get($section_id);
        $date                = date('Y-m-d');
        $data['sch_setting'] = $this->sch_setting_detail;
        $student_due_fee     = $this->studentfee_model->getDueStudentFeesByDateClassSection($class_id, $section_id, $date);

        $students = array();

        if (!empty($student_due_fee)) {
            foreach ($student_due_fee as $student_due_fee_key => $student_due_fee_value) {



                $amt_due = ($student_due_fee_value['is_system']) ? $student_due_fee_value['previous_balance_amount'] : $student_due_fee_value['amount'];

                $a = json_decode($student_due_fee_value['amount_detail']);
                if (!empty($a)) {
                    $amount          = 0;
                    $amount_discount = 0;
                    $amount_fine     = 0;

                    foreach ($a as $a_key => $a_value) {
                        $amount          = $amount + $a_value->amount;
                        $amount_discount = $amount_discount + $a_value->amount_discount;
                        $amount_fine     = $amount_fine + $a_value->amount_fine;
                    }
                    if ($amt_due <= ($amount + $amount_discount)) {
                        unset($student_due_fee[$student_due_fee_key]);
                    } else {

                        if (!array_key_exists($student_due_fee_value['student_session_id'], $students)) {
                            $students[$student_due_fee_value['student_session_id']] = $this->add_new_student($student_due_fee_value);
                        }

                        $students[$student_due_fee_value['student_session_id']]['fees'][] = array(
                            'is_system'=>$student_due_fee_value['is_system'],
                            'amount'          => $amt_due,
                            'amount_deposite' => $amount,
                            'amount_discount' => $amount_discount,
                            'amount_fine'     => $amount_fine,
                            'fee_group'       => $student_due_fee_value['fee_group'],
                            'fee_type'        => $student_due_fee_value['fee_type'],
                            'fee_code'        => $student_due_fee_value['fee_code'],
                        );

                    }
                } else {
                    $amount          = 0;
                    $amount_discount = 0;

                    if ($amt_due <= ($amount + $amount_discount)) {
                        unset($student_due_fee[$student_due_fee_key]);
                    } else {
                        if (!array_key_exists($student_due_fee_value['student_session_id'], $students)) {
                            $students[$student_due_fee_value['student_session_id']] = $this->add_new_student($student_due_fee_value);
                        }
                        $students[$student_due_fee_value['student_session_id']]['fees'][] = array(
                            'is_system'=>$student_due_fee_value['is_system'],
                            'amount'          => $amt_due,
                            'amount_deposite' => 0,
                            'amount_discount' => 0,
                            'amount_fine'     => 0,
                            'fee_group'       => $student_due_fee_value['fee_group'],
                            'fee_type'        => $student_due_fee_value['fee_type'],
                            'fee_code'        => $student_due_fee_value['fee_code'],
                        );
                    }
                }
            }
        }

        $data['student_remain_fees'] = $students;
        $page = $this->load->view('financereports/_printduefeesremark', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));

    }

    public function income()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/finance');
        $this->session->set_userdata('subsub_menu', 'Reports/finance/income');
        $data['searchlist'] = $this->customlib->get_searchtype();
        $this->load->view('layout/header', $data);
        $this->load->view('financereports/income', $data);
        $this->load->view('layout/footer', $data);
    }

    public function searchreportvalidation()
    {

        $this->form_validation->set_rules('search_type', $this->lang->line('search_type'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $error = array();

            $error['search_type'] = form_error('search_type');

            $array = array('status' => 0, 'error' => $error);
            echo json_encode($array);
        } else {
            $search_type = $this->input->post('search_type');
            $date_from   = "";
            $date_to     = "";
            if ($search_type == 'period') {

                $date_from = $this->input->post('date_from');
                $date_to   = $this->input->post('date_to');
            }

            $params = array('search_type' => $search_type, 'date_from' => $date_from, 'date_to' => $date_to);
            $array  = array('status' => 1, 'error' => '', 'params' => $params);
            echo json_encode($array);
        }
    }

    public function getincomelistbydt()
    {
        $search_type = $this->input->post('search_type');
        $date_from   = $this->input->post('date_from');
        $date_to     = $this->input->post('date_to');

        if ($search_type == "") {
            $dates               = $this->customlib->get_betweendate('this_year');
            $data['search_type'] = '';
        } else {
            $dates               = $this->customlib->get_betweendate($_POST['search_type']);
            $data['search_type'] = $_POST['search_type'];
        }

        $start_date = date('Y-m-d', strtotime($dates['from_date']));
        $end_date   = date('Y-m-d', strtotime($dates['to_date']));

        $data['label'] = date($this->customlib->getSchoolDateFormat(), strtotime($start_date)) . " " . $this->lang->line('to') . " " . date($this->customlib->getSchoolDateFormat(), strtotime($end_date));

        $incomeList = $this->income_model->search("", $start_date, $end_date);

        $incomeList      = json_decode($incomeList);
        $currency_symbol = $this->customlib->getSchoolCurrencyFormat();
        $dt_data         = array();
        $grand_total     = 0;
        if (!empty($incomeList->data)) {
            foreach ($incomeList->data as $key => $value) {
                $grand_total += $value->amount;

                $row   = array();
                $row[] = $value->name;
                $row[] = $value->invoice_no;
                $row[] = $value->income_category;
                $row[] = date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($value->date));
                $row[] = $currency_symbol . amountFormat($value->amount);
                $dt_data[] = $row;
            }
            $footer_row   = array();
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "<b>" . $this->lang->line('grand_total') . "</b>";
            $footer_row[] = $currency_symbol . amountFormat($grand_total);
            $dt_data[]    = $footer_row;
        }

        $json_data = array(
            "draw"            => intval($incomeList->draw),
            "recordsTotal"    => intval($incomeList->recordsTotal),
            "recordsFiltered" => intval($incomeList->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);

    }

    public function expense()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/finance');
        $this->session->set_userdata('subsub_menu', 'Reports/finance/expense');
        $data['searchlist']  = $this->customlib->get_searchtype();
        $data['date_type']   = $this->customlib->date_type();
        $data['date_typeid'] = '';

        $this->form_validation->set_rules('search_type', $this->lang->line('search_type'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $dates               = $this->customlib->get_betweendate('this_year');
            $data['search_type'] = '';
        } else {
            $dates               = $this->customlib->get_betweendate($_POST['search_type']);
            $data['search_type'] = $_POST['search_type'];
        }

        $start_date = date('Y-m-d', strtotime($dates['from_date']));
        $end_date   = date('Y-m-d', strtotime($dates['to_date']));

        $data['label'] = date($this->customlib->getSchoolDateFormat(), strtotime($start_date)) . " " . $this->lang->line('to') . " " . date($this->customlib->getSchoolDateFormat(), strtotime($end_date));
        $this->load->view('layout/header', $data);
        $this->load->view('financereports/expense', $data);
        $this->load->view('layout/footer', $data);
    }

    public function getexpenselistbydt()
    {
        $search_type = $this->input->post('search_type');
        $date_from   = $this->input->post('date_from');
        $date_to     = $this->input->post('date_to');

        if ($search_type == "") {
            $dates               = $this->customlib->get_betweendate('this_year');
            $data['search_type'] = '';
        } else {
            $dates               = $this->customlib->get_betweendate($_POST['search_type']);
            $data['search_type'] = $_POST['search_type'];
        }

        $start_date = date('Y-m-d', strtotime($dates['from_date']));
        $end_date   = date('Y-m-d', strtotime($dates['to_date']));

        $data['label'] = date($this->customlib->getSchoolDateFormat(), strtotime($start_date)) . " " . $this->lang->line('to') . " " . date($this->customlib->getSchoolDateFormat(), strtotime($end_date));
        $expenseList   = $this->expense_model->search('', $start_date, $end_date);

        $m               = json_decode($expenseList);
        $currency_symbol = $this->customlib->getSchoolCurrencyFormat();
        $dt_data         = array();
        $grand_total     = 0;
        if (!empty($m->data)) {
            foreach ($m->data as $key => $value) {
                $grand_total += $value->amount;

                $row       = array();
                $row[]     = date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($value->date));
                $row[]     = $value->exp_category;
                $row[]     = $value->name;
                $row[]     = $value->invoice_no;
                $row[]     = $currency_symbol . amountFormat($value->amount);
                $dt_data[] = $row;
            }
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "<b>" . $this->lang->line('grand_total'). "</b>";
            $footer_row[] = "<b>" . $currency_symbol . amountFormat($grand_total). "</b>";
            $dt_data[]    = $footer_row;
        }

        $json_data = array(
            "draw"            => intval($m->draw),
            "recordsTotal"    => intval($m->recordsTotal),
            "recordsFiltered" => intval($m->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function payroll()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/finance');
        $this->session->set_userdata('subsub_menu', 'Reports/finance/payroll');
        $data['searchlist']  = $this->customlib->get_searchtype();
        $data['date_type']   = $this->customlib->date_type();
        $data['date_typeid'] = '';

        if (isset($_POST['search_type']) && $_POST['search_type'] != '') {

            $dates               = $this->customlib->get_betweendate($_POST['search_type']);
            $data['search_type'] = $_POST['search_type'];

        } else {

            $dates               = $this->customlib->get_betweendate('this_year');
            $data['search_type'] = '';

        }

        $start_date = date('Y-m-d', strtotime($dates['from_date']));
        $end_date   = date('Y-m-d', strtotime($dates['to_date']));

        $data['label']        = date($this->customlib->getSchoolDateFormat(), strtotime($start_date)) . " " . $this->lang->line('to') . " " . date($this->customlib->getSchoolDateFormat(), strtotime($end_date));
        $data['payment_mode'] = $this->payment_mode;

        $result              = $this->payroll_model->getbetweenpayrollReport($start_date, $end_date);
        $data['payrollList'] = $result;
        $this->load->view('layout/header', $data);
        $this->load->view('financereports/payroll', $data);
        $this->load->view('layout/footer', $data);
    }

    public function incomegroup()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/finance');
        $this->session->set_userdata('subsub_menu', 'Reports/finance/incomegroup');
        $data['searchlist']  = $this->customlib->get_searchtype();
        $data['date_type']   = $this->customlib->date_type();
        $data['date_typeid'] = '';
        $data['headlist']    = $this->incomehead_model->get();
        $this->load->view('layout/header', $data);
        $this->load->view('financereports/incomegroup', $data);
        $this->load->view('layout/footer', $data);
    }

    public function dtincomegroupreport()
    {
        $search_type = $this->input->post('search_type');
        $date_from   = $this->input->post('date_from');
        $date_to     = $this->input->post('date_to');
        $head        = $this->input->post('head');

        if (isset($search_type) && $search_type != '') {

            $dates               = $this->customlib->get_betweendate($search_type);
            $data['search_type'] = $_POST['search_type'];

        } else {

            $dates               = $this->customlib->get_betweendate('this_year');
            $data['search_type'] = '';

        }
        $data['head_id'] = $head_id = "";
        if (isset($_POST['head']) && $_POST['head'] != '') {
            $data['head_id'] = $head_id = $_POST['head'];
        }

        $start_date = date('Y-m-d', strtotime($dates['from_date']));
        $end_date   = date('Y-m-d', strtotime($dates['to_date']));

        $data['label']   = date($this->customlib->getSchoolDateFormat(), strtotime($start_date)) . " " . $this->lang->line('to') . " " . date($this->customlib->getSchoolDateFormat(), strtotime($end_date));
        $incomeList      = $this->income_model->searchincomegroup($start_date, $end_date, $head_id);
        $m               = json_decode($incomeList);
        $currency_symbol = $this->customlib->getSchoolCurrencyFormat();
        $dt_data         = array();
        $grand_total     = 0;

        if (!empty($m->data)) {
            $grd_total  = 0;
            $inchead_id = 0;
            $count      = 0;
            foreach ($m->data as $key => $value) {
                $income_head[$value->head_id][] = $value;
            }

            foreach ($m->data as $key => $value) {
                $inc_head_id  = $value->head_id;
                $total_amount = "<b>" . $value->amount . "</b>";
                $grd_total += $value->amount;
                $row = array();
                if ($inchead_id == $inc_head_id) {
                    $row[] = "";
                    $count++;
                } else {
                    $row[] = $value->income_category;
                    $count = 0;
                }
                $row[]      = $value->id;
                $row[]      = $value->name;
                $row[]      = date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($value->date));
                $row[]      = $value->invoice_no;
                $row[]      = amountFormat($value->amount);
                $dt_data[]  = $row;
                $inchead_id = $value->head_id;
                $sub_total  = 0;
                if ($count == (count($income_head[$value->head_id]) - 1)) {
                    foreach ($income_head[$value->head_id] as $inc_headkey => $inc_headvalue) {
                        $sub_total += $inc_headvalue->amount;
                    }
                    $amount_row   = array();
                    $amount_row[] = "";
                    $amount_row[] = "";
                    $amount_row[] = "";
                    $amount_row[] = "";
                    $amount_row[] = "<b>" . $this->lang->line('sub_total') . "</b>";
                    $amount_row[] = "<b>" . $currency_symbol . amountFormat($sub_total) . "</b>";
                    $dt_data[]    = $amount_row;
                }
            }

            $grand_total  = "<b>" . $currency_symbol . amountFormat($grd_total) . "</b>";
            $footer_row   = array();
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "<b>" . $this->lang->line('total') . "</b>";
            $footer_row[] = $grand_total;
            $dt_data[]    = $footer_row;
        }

        $json_data = array(
            "draw"            => intval($m->draw),
            "recordsTotal"    => intval($m->recordsTotal),
            "recordsFiltered" => intval($m->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function getgroupreportparam()
    {
        $search_type = $this->input->post('search_type');
        $head        = $this->input->post('head');
        $date_from = "";
        $date_to   = "";
        if ($search_type == 'period') {

            $date_from = $this->input->post('date_from');
            $date_to   = $this->input->post('date_to');
        }

        $params = array('search_type' => $search_type, 'head' => $head, 'date_from' => $date_from, 'date_to' => $date_to);
        $array  = array('status' => 1, 'error' => '', 'params' => $params);
        echo json_encode($array);
    }

    public function expensegroup()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/finance');
        $this->session->set_userdata('subsub_menu', 'Reports/finance/expensegroup');
        $data['searchlist']  = $this->customlib->get_searchtype();
        $data['date_type']   = $this->customlib->date_type();
        $data['date_typeid'] = '';
        $data['headlist']    = $this->expensehead_model->get();

        $this->load->view('layout/header', $data);
        $this->load->view('financereports/expensegroup', $data);
        $this->load->view('layout/footer', $data);
    }

    public function dtexpensegroupreport()
    {
        $search_type = $this->input->post('search_type');
        $date_from   = $this->input->post('date_from');
        $date_to     = $this->input->post('date_to');
        $head        = $this->input->post('head');

        $data['date_type']   = $this->customlib->date_type();
        $data['date_typeid'] = '';

        if (isset($_POST['search_type']) && $_POST['search_type'] != '') {

            $dates               = $this->customlib->get_betweendate($_POST['search_type']);
            $data['search_type'] = $_POST['search_type'];

        } else {

            $dates               = $this->customlib->get_betweendate('this_year');
            $data['search_type'] = '';

        }

        $data['head_id'] = $head_id = "";
        if (isset($_POST['head']) && $_POST['head'] != '') {
            $data['head_id'] = $head_id = $_POST['head'];
        }

        $start_date = date('Y-m-d', strtotime($dates['from_date']));
        $end_date   = date('Y-m-d', strtotime($dates['to_date']));

        $data['label'] = date($this->customlib->getSchoolDateFormat(), strtotime($start_date)) . " " . $this->lang->line('to') . " " . date($this->customlib->getSchoolDateFormat(), strtotime($end_date));
        $result        = $this->expensehead_model->searchexpensegroup($start_date, $end_date, $head_id);

        $m               = json_decode($result);
        $currency_symbol = $this->customlib->getSchoolCurrencyFormat();
        $dt_data         = array();
        $grand_total     = 0;
        if (!empty($m->data)) {
            foreach ($m->data as $key => $value) {
                $expense_head[$value->exp_head_id][] = $value;
            }

            $grd_total  = 0;
            $exphead_id = 0;
            $count      = 0;
            foreach ($m->data as $key => $value) {

                $exp_head_id  = $value->exp_head_id;
                $total_amount = "<b>" . $value->total_amount . "</b>";
                $grd_total += $value->total_amount;
                $row = array();

                if ($exphead_id == $exp_head_id) {
                    $row[] = "";
                    $count++;
                } else {
                    $row[] = $value->exp_category;
                    $count = 0;
                }

                $row[]      = $value->id;
                $row[]      = $value->name;
                $row[]      = date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($value->date));
                $row[]      = $value->invoice_no;
                $row[]      = amountFormat($value->amount);
                $dt_data[]  = $row;
                $exphead_id = $value->exp_head_id;
                $sub_total  = 0;
                if ($count == (count($expense_head[$value->exp_head_id]) - 1)) {
                    foreach ($expense_head[$value->exp_head_id] as $exp_headkey => $exp_headvalue) {
                        $sub_total += $exp_headvalue->amount;
                    }
                    $amount_row   = array();
                    $amount_row[] = "";
                    $amount_row[] = "";
                    $amount_row[] = "";
                    $amount_row[] = "";
                    $amount_row[] = "<b>" . $this->lang->line('sub_total') . "</b>";
                    $amount_row[] = "<b>" . $currency_symbol . amountFormat($sub_total) . "</b>";
                    $dt_data[]    = $amount_row;
                }

            }

            $grand_total  = "<b>" . $currency_symbol . amountFormat($grd_total) . "</b>";
            $footer_row   = array();
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "<b>" . $this->lang->line('total') . "</b>";
            $footer_row[] = $grand_total;
            $dt_data[]    = $footer_row;
        }

        $json_data = array(
            "draw"            => intval($m->draw),
            "recordsTotal"    => intval($m->recordsTotal),
            "recordsFiltered" => intval($m->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function onlineadmission()
    {
        if (!$this->rbac->hasPrivilege('online_admission', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/finance');
        $this->session->set_userdata('subsub_menu', 'Reports/finance/onlineadmission');
        $data['searchlist'] = $this->customlib->get_searchtype();
        $data['group_by']   = $this->customlib->get_groupby();

        if (isset($_POST['search_type']) && $_POST['search_type'] != '') {

            $dates               = $this->customlib->get_betweendate($_POST['search_type']);
            $data['search_type'] = $_POST['search_type'];
        } else {

            $dates               = $this->customlib->get_betweendate('this_year');
            $data['search_type'] = '';
        }

        $collection = array();
        $start_date = date('Y-m-d', strtotime($dates['from_date']));
        $end_date   = date('Y-m-d', strtotime($dates['to_date']));
        $this->form_validation->set_rules('search_type', $this->lang->line('search_type'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {

            $data['collectlist'] = array();

        } else {

            $data['collectlist'] = $this->onlinestudent_model->getOnlineAdmissionFeeCollectionReport($start_date, $end_date);

        }
        $data['sch_setting'] = $this->sch_setting_detail;
        $this->load->view('layout/header', $data);
        $this->load->view('financereports/onlineadmission', $data);
        $this->load->view('layout/footer', $data);
    }


    // public function totalstudentacademicreport()
    // {
    //     if (!$this->rbac->hasPrivilege('balance_fees_report', 'can_view')) {
    //         access_denied();
    //     }

    //     $this->session->set_userdata('top_menu', 'Reports');
    //     $this->session->set_userdata('sub_menu', 'Reports/finance');
    //     $this->session->set_userdata('subsub_menu', 'Reports/finance/totalstudentacademicreport');
    //     $data['title']           = 'student fee';
    //     $data['payment_type']    = $this->customlib->getPaymenttype();
    //     $class                   = $this->class_model->get();
    //     $data['classlist']       = $class;
    //     $data['sch_setting']     = $this->sch_setting_detail;
    //     $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;
    //     $this->form_validation->set_rules('search_type', $this->lang->line('search_type'), 'trim|required|xss_clean');

    //     if ($this->form_validation->run() == false) {
    //         $data['student_due_fee'] = array();
    //         $data['resultarray']     = array();
    //         $data['feetype']     = "";
    //         $data['feetype_arr'] = array();
    //     } else {
    //         $student_Array = array();
    //         $search_type   = $this->input->post('search_type');
    //         $class_id   = $this->input->post('class_id');
    //         $section_id = $this->input->post('section_id');

    //         if (isset($class_id)) {
    //             $studentlist = $this->student_model->totalsearchByClassSectionWithSession($class_id, $section_id);
    //         } else {
    //             $studentlist = $this->student_model->gettotalStudents();
    //         }

    //         $student_Array = array();
    //         if (!empty($studentlist)) {
    //             foreach ($studentlist as $key => $eachstudent) {
    //                 $obj                = new stdClass();
    //                 $obj->name          = $this->customlib->getFullName($eachstudent['firstname'], $eachstudent['middlename'], $eachstudent['lastname'], $this->sch_setting_detail->middlename, $this->sch_setting_detail->lastname);
    //                 $obj->class         = $eachstudent['class'];
    //                 $obj->section       = $eachstudent['section'];
    //                 $obj->admission_no  = $eachstudent['admission_no'];
    //                 $obj->roll_no       = $eachstudent['roll_no'];
    //                 $obj->father_name   = $eachstudent['father_name'];
    //                 $student_session_id = $eachstudent['student_session_id'];
    //                 $student_total_fees = $this->studentfeemaster_model->getTransStudentFees($student_session_id);

    //                 if (!empty($student_total_fees)) {
    //                     $totalfee = 0;
    //                     $deposit  = 0;
    //                     $discount = 0;
    //                     $balance  = 0;
    //                     $fine     = 0;
    //                       //print_r($student_total_fees);die;
    //                     foreach ($student_total_fees as $student_total_fees_key => $student_total_fees_value) {

    //                         if (!empty($student_total_fees_value->fees)) {
    //                             foreach ($student_total_fees_value->fees as $each_fee_key => $each_fee_value) {

    //                                     $totalfee = $totalfee +$each_fee_value->amount;


    //                                     $amount_detail =json_decode($each_fee_value->amount_detail);



    //                                 if (is_object($amount_detail) && !empty($amount_detail)) {
    //                                     foreach ($amount_detail as $amount_detail_key => $amount_detail_value) {
    //                                         $deposit  = $deposit + $amount_detail_value->amount;
    //                                         $fine     = $fine + $amount_detail_value->amount_fine;
    //                                         $discount = $discount + $amount_detail_value->amount_discount;
    //                                     }
    //                                 }
    //                             }
    //                         }
    //                     }

    //                     $obj->totalfee     = $totalfee;
    //                     $obj->payment_mode = "N/A";
    //                     $obj->deposit      = $deposit;
    //                     $obj->fine         = $fine;
    //                     $obj->discount     = $discount;
    //                     $obj->balance      = $totalfee - ($deposit + $discount);
    //                 } else {

    //                     $obj->totalfee     = 0;
    //                     $obj->payment_mode = 0;
    //                     $obj->deposit      = 0;
    //                     $obj->fine         = 0;
    //                     $obj->balance      = 0;
    //                     $obj->discount     = 0;
    //                 }

    //                 if ($search_type == 'all') {
    //                     $student_Array[] = $obj;
    //                 } elseif ($search_type == 'balance') {
    //                     if ($obj->balance > 0) {
    //                         $student_Array[] = $obj;
    //                     }
    //                 } elseif ($search_type == 'paid') {
    //                     if ($obj->balance <= 0) {
    //                         $student_Array[] = $obj;
    //                     }
    //                 }
    //             }
    //         }

    //         $classlistdata[]         = array('result' => $student_Array);
    //         $data['student_due_fee'] = $student_Array;
    //         $data['resultarray']     = $classlistdata;

    //     }

    //     $this->load->view('layout/header', $data);
    //     $this->load->view('financereports/totalstudentAcademicReport', $data);
    //     $this->load->view('layout/footer', $data);
    // }



    public function totalstudentacademicreport()
    {
        if (!$this->rbac->hasPrivilege('balance_fees_report', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/finance');
        $this->session->set_userdata('subsub_menu', 'Reports/finance/totalstudentacademicreport');
        $data['title']           = 'student fee';
        $data['payment_type']    = $this->customlib->getPaymenttype();
        $class                   = $this->class_model->get();
        $data['classlist']       = $class;
        $session_result           = $this->session_model->get();
        $data['sessionlist']      = $session_result;
        $data['sch_setting']     = $this->sch_setting_detail;
        $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;
        $this->form_validation->set_rules('search_type', $this->lang->line('search_type'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $data['student_due_fee'] = array();
            $data['resultarray']     = array();
            $data['feetype']     = "";
            $data['feetype_arr'] = array();
        } else {
            $student_Array = array();
            $search_type   = $this->input->post('search_type');
            $class_id   = $this->input->post('class_id');
            $section_id = $this->input->post('section_id');
            $session_idd =$this->input->post('sch_session_id');

            if (isset($class_id)) {
                $studentlist = $this->student_model->totalsearchByClassSectionWithSession($session_idd,$class_id, $section_id);
            } else {
                $studentlist = $this->student_model->gettotalStudents($session_idd);
            }

            // $session_idd =$this->input->post('sch_session_id');

            // if (isset($class_id)) {
            //     $studentlist = $this->student_model->totalsearchByClassSectionWithSession($class_id, $section_id);
            // } else {
            //     $studentlist = $this->student_model->gettotalStudents();
            // }

            $student_Array = array();
            if (!empty($studentlist)) {
                foreach ($studentlist as $key => $eachstudent) {
                    $obj                = new stdClass();
                    $obj->name          = $this->customlib->getFullName($eachstudent['firstname'], $eachstudent['middlename'], $eachstudent['lastname'], $this->sch_setting_detail->middlename, $this->sch_setting_detail->lastname);
                    $obj->class         = $eachstudent['class'];
                    $obj->section       = $eachstudent['section'];
                    $obj->admission_no  = $eachstudent['admission_no'];
                    $obj->roll_no       = $eachstudent['roll_no'];
                    $obj->father_name   = $eachstudent['father_name'];
                    $student_session_id = $eachstudent['student_session_id'];
                    $student_total_fees = $this->studentfeemaster_model->getTransStudentFees($student_session_id);

                    if (!empty($student_total_fees)) {
                        $totalfee = 0;
                        $deposit  = 0;
                        $discount = 0;
                        $balance  = 0;
                        $fine     = 0;
                          //print_r($student_total_fees);die;
                        foreach ($student_total_fees as $student_total_fees_key => $student_total_fees_value) {

                            if (!empty($student_total_fees_value->fees)) {
                                foreach ($student_total_fees_value->fees as $each_fee_key => $each_fee_value) {

                                        $totalfee = $totalfee +$each_fee_value->amount;


                                        $amount_detail =json_decode($each_fee_value->amount_detail);



                                    if (is_object($amount_detail) && !empty($amount_detail)) {
                                        foreach ($amount_detail as $amount_detail_key => $amount_detail_value) {
                                            $deposit  = $deposit + $amount_detail_value->amount;
                                            $fine     = $fine + $amount_detail_value->amount_fine;
                                            $discount = $discount + $amount_detail_value->amount_discount;
                                        }
                                    }
                                }
                            }
                        }

                        $obj->totalfee     = $totalfee;
                        $obj->payment_mode = "N/A";
                        $obj->deposit      = $deposit;
                        $obj->fine         = $fine;
                        $obj->discount     = $discount;
                        $obj->balance      = $totalfee - ($deposit + $discount);
                    } else {

                        $obj->totalfee     = 0;
                        $obj->payment_mode = 0;
                        $obj->deposit      = 0;
                        $obj->fine         = 0;
                        $obj->balance      = 0;
                        $obj->discount     = 0;
                    }

                    if ($search_type == 'all') {
                        $student_Array[] = $obj;
                    } elseif ($search_type == 'balance') {
                        if ($obj->balance > 0) {
                            $student_Array[] = $obj;
                        }
                    } elseif ($search_type == 'paid') {
                        if ($obj->balance <= 0) {
                            $student_Array[] = $obj;
                        }
                    }
                }
            }

            $classlistdata[]         = array('result' => $student_Array);
            $data['student_due_fee'] = $student_Array;
            $data['resultarray']     = $classlistdata;

        }

        $this->load->view('layout/header', $data);
        $this->load->view('financereports/totalstudentAcademicReport', $data);
        $this->load->view('layout/footer', $data);
    }



    public function yearreportduefees()
    {
        if (!$this->rbac->hasPrivilege('balance_fees_statement', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/finance');
        $this->session->set_userdata('subsub_menu', 'Reports/finance/yearreportduefees');
        $data                = array();
        $data['title']       = 'student fees';
        $class               = $this->class_model->get();
        $data['classlist']   = $class;
        $data['sch_setting'] = $this->sch_setting_detail;
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $date               = date('Y-m-d');
            $class_id           = $this->input->post('class_id');
            $section_id         = $this->input->post('section_id');
            $data['class_id']   = $class_id;
            $data['section_id'] = $section_id;
            $fees_dues          = $this->studentfeemaster_model->getStudentDueFeeTypesByDateee($date, $class_id, $section_id);
            $students_list      = array();

            if (!empty($fees_dues)) {
                foreach ($fees_dues as $fee_due_key => $fee_due_value) {
                    $amount_paid = 0;

                    if (isJSON($fee_due_value->amount_detail)) {
                        $student_fees_array = json_decode($fee_due_value->amount_detail);
                        foreach ($student_fees_array as $fee_paid_key => $fee_paid_value) {
                            $amount_paid += ($fee_paid_value->amount + $fee_paid_value->amount_discount);
                        }
                    }
                    if ($amount_paid < $fee_due_value->fee_amount || ($amount_paid < $fee_due_value->amount && $fee_due_value->is_system)) {

                        $students_list[$fee_due_value->student_session_id]['admission_no']             = $fee_due_value->admission_no;
                        $students_list[$fee_due_value->student_session_id]['class_id']             = $fee_due_value->class_id;
                        $students_list[$fee_due_value->student_session_id]['section_id']             = $fee_due_value->section_id;
                        $students_list[$fee_due_value->student_session_id]['student_id']             = $fee_due_value->student_id;
                        $students_list[$fee_due_value->student_session_id]['roll_no']                  = $fee_due_value->roll_no;
                        $students_list[$fee_due_value->student_session_id]['admission_date']           = $fee_due_value->admission_date;
                        $students_list[$fee_due_value->student_session_id]['firstname']                = $fee_due_value->firstname;
                        $students_list[$fee_due_value->student_session_id]['middlename']               = $fee_due_value->middlename;
                        $students_list[$fee_due_value->student_session_id]['lastname']                 = $fee_due_value->lastname;
                        $students_list[$fee_due_value->student_session_id]['father_name']              = $fee_due_value->father_name;
                        $students_list[$fee_due_value->student_session_id]['image']                    = $fee_due_value->image;
                        $students_list[$fee_due_value->student_session_id]['mobileno']                 = $fee_due_value->mobileno;
                        $students_list[$fee_due_value->student_session_id]['email']                    = $fee_due_value->email;
                        $students_list[$fee_due_value->student_session_id]['state']                    = $fee_due_value->state;
                        $students_list[$fee_due_value->student_session_id]['city']                     = $fee_due_value->city;
                        $students_list[$fee_due_value->student_session_id]['pincode']                  = $fee_due_value->pincode;
                        $students_list[$fee_due_value->student_session_id]['class']                    = $fee_due_value->class;
                        $students_list[$fee_due_value->student_session_id]['section']                  = $fee_due_value->section;
                        $students_list[$fee_due_value->student_session_id]['fee_groups_feetype_ids'][] = $fee_due_value->fee_groups_feetype_id;
                    }

                }

            }

            if (!empty($students_list)) {
                foreach ($students_list as $student_key => $student_value) {
                    $students_list[$student_key]['fees_list'] = $this->studentfeemaster_model->studentDepositByFeeGroupFeeTypeArray($student_key, $student_value['fee_groups_feetype_ids']);
                   $students_list[$student_key]['transport_fees']       = array();
                $student               = $this->student_model->getByStudentSession($student_value['student_id']);

                $route_pickup_point_id = $student['route_pickup_point_id'];
                $student_session_id    = $student['student_session_id'];
                $transport_fees=[];
                $module=$this->module_model->getPermissionByModulename('transport');

        if($module['is_active']){

        $transport_fees        = $this->studentfeemaster_model->getStudentTransportFees($student_session_id, $route_pickup_point_id);

        }
        $students_list[$student_key]['transport_fees']       = $transport_fees;

                }
            }

            $data['student_due_fee'] = $students_list;
        }

        $this->load->view('layout/header', $data);
        $this->load->view('financereports/yearreportduefees', $data);
        $this->load->view('layout/footer', $data);
    }


    public function otherfeeCollectionStudentDeposit()
    {
        $data                 = array();
        $date                 = $this->input->post('date');
        $fees_id              = $this->input->post('fees_id');
        $fees_id_array        = explode(',', $fees_id);
        $fees_list            = $this->studentfeemaster_model->getotherFeesDepositeByIdArrayy($fees_id_array);
        // $fees_list            = array();
        $data['student_list'] = $fees_list;
        $data['date']         = $date;
        $data['sch_setting']  = $this->sch_setting_detail;
        $page                 = $this->load->view('financereports/_feeCollectionStudentDeposit', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }


    public function reportdailycollection()
    {
        if (!$this->rbac->hasPrivilege('daily_collection_report', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/finance');
        $this->session->set_userdata('subsub_menu', 'Reports/finance/reportdailycollection');
        $data          = array();
        $data['title'] = 'Daily Collection Report';
        $this->form_validation->set_rules('date_from', $this->lang->line('date_from'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('date_to', $this->lang->line('date_to'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == true) {

            $date_from          = $this->input->post('date_from');
            $date_to            = $this->input->post('date_to');
            $formated_date_from = strtotime($this->customlib->dateFormatToYYYYMMDD($date_from));
            $formated_date_to   = strtotime($this->customlib->dateFormatToYYYYMMDD($date_to));
            $st_fees            = $this->studentfeemaster_model->getCurrentSessionStudentFeess();
            $st_other_fees            = $this->studentfeemaster_model->getOtherfeesCurrentSessionStudentFeess();
            $fees_data          = array();
            $other_fees_data   = array();

            for ($i = $formated_date_from; $i <= $formated_date_to; $i += 86400) {
                $fees_data[$i]['amt']                       = 0;
                $fees_data[$i]['count']                     = 0;
                $fees_data[$i]['student_fees_deposite_ids'] = array();
            }

            if (!empty($st_fees)) {
                foreach ($st_fees as $fee_key => $fee_value) {
                    if (isJSON($fee_value->amount_detail)) {
                        $fees_details = (json_decode($fee_value->amount_detail));
                        if (!empty($fees_details)) {
                            foreach ($fees_details as $fees_detail_key => $fees_detail_value) {
                                $date = strtotime($fees_detail_value->date);
                                if ($date >= $formated_date_from && $date <= $formated_date_to) {
                                    if (array_key_exists($date, $fees_data)) {
                                        $fees_data[$date]['amt'] += $fees_detail_value->amount + $fees_detail_value->amount_fine;
                                        $fees_data[$date]['count'] += 1;
                                        $fees_data[$date]['student_fees_deposite_ids'][] = $fee_value->student_fees_deposite_id;
                                    } else {
                                        $fees_data[$date]['amt']                         = $fees_detail_value->amount + $fees_detail_value->amount_fine;
                                        $fees_data[$date]['count']                       = 1;
                                        $fees_data[$date]['student_fees_deposite_ids'][] = $fee_value->student_fees_deposite_id;
                                    }
                                }
                            }
                        }
                    }
                }
            }


            if (!empty($st_other_fees)) {
                foreach ($st_other_fees as $fee_key => $fee_value) {
                    if (isJSON($fee_value->amount_detail)) {
                        $fees_details = (json_decode($fee_value->amount_detail));
                        if (!empty($fees_details)) {
                            foreach ($fees_details as $fees_detail_key => $fees_detail_value) {
                                $date = strtotime($fees_detail_value->date);
                                if ($date >= $formated_date_from && $date <= $formated_date_to) {
                                    if (array_key_exists($date, $other_fees_data)) {
                                        $other_fees_data[$date]['amt'] += $fees_detail_value->amount + $fees_detail_value->amount_fine;
                                        $other_fees_data[$date]['count'] += 1;
                                        $other_fees_data[$date]['student_fees_deposite_ids'][] = $fee_value->student_fees_deposite_id;
                                    } else {
                                        $other_fees_data[$date]['amt']                         = $fees_detail_value->amount + $fees_detail_value->amount_fine;
                                        $other_fees_data[$date]['count']                       = 1;
                                        $other_fees_data[$date]['student_fees_deposite_ids'][] = $fee_value->student_fees_deposite_id;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $data['fees_data'] = $fees_data;
            $data['other_fees_data'] = $other_fees_data;
        }

        $this->load->view('layout/header', $data);
        $this->load->view('financereports/reportdailycollection', $data);
        $this->load->view('layout/footer', $data);
    }



    public function typewisebalancereport()
    {
        // Load data for dropdowns
        $session_result = $this->session_model->get();
        $data['sessionlist'] = $session_result;
        $class = $this->class_model->get();
        $data['classlist'] = $class;

        $feegroup = $this->feegroup_model->get();
        $data['feegroupList'] = $feegroup;

        $feetype = $this->feetype_model->get();
        $data['feetypeList'] = $feetype;

        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/finance');
        $this->session->set_userdata('subsub_menu', 'Reports/finance/typewisebalacereport');

        // Define validation rules
        $this->form_validation->set_rules('sch_session_id', 'Session', 'trim|required|xss_clean');
        $this->form_validation->set_rules('feetype_ids[]', 'Fee Type', 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            // Handle validation errors if necessary
            $this->load->view('layout/header', $data);
            $this->load->view('financereports/typewisereport', $data);
            $this->load->view('layout/footer', $data);
        } else {
            // Retrieve form inputs
            $session_id = $this->input->post('sch_session_id');
            $class_id = $this->input->post('class_id');
            $section_id = $this->input->post('section_id');
            $group_ids = $this->input->post('feegroup_ids'); // Retrieve array of selected fee group IDs
            $feetype_ids = $this->input->post('feetype_ids'); // Retrieve array of selected fee type IDs

            // Process the selected fee type IDs
            $data['results'] = $this->studentfeemaster_model->gettypewisereportt($session_id, $feetype_ids, $group_ids, $class_id, $section_id);

            // Load the view with results
            $this->load->view('layout/header', $data);
            $this->load->view('financereports/typewisereport', $data);
            $this->load->view('layout/footer', $data);
        }
    }



}

