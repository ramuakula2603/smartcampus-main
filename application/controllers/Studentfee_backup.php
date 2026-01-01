<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Studentfee extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('smsgateway');
        $this->load->library('mailsmsconf');
        $this->load->library('customlib');
        $this->load->library('media_storage');
         $this->load->model("module_model");
         $this->load->model('test_model');
         $this->load->model('addaccount_model');
         $this->load->model("studentfeemasteradding_model");
          $this->load->model("transportfee_model");
         $this->load->model("hostelfee_model");
         $this->load->model("studenthostelfee_model");
          $this->load->model("AdvancePayment_model");
        $this->search_type        = $this->config->item('search_type');
        $this->sch_setting_detail = $this->setting_model->getSetting();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('collect_fees', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', $this->lang->line('fees_collection'));
        $this->session->set_userdata('sub_menu', 'studentfee/index');
        $data['sch_setting'] = $this->sch_setting_detail;
        $data['title']       = 'student fees';
        $class               = $this->class_model->get();
        $data['classlist']   = $class;
        $this->load->view('layout/header', $data);
        $this->load->view('studentfee/studentfeeSearch', $data);
        $this->load->view('layout/footer', $data);
    }



    public function pdf()
    {
        $this->load->helper('pdf_helper');
    }

    public function search()
    {
        $search_type = $this->input->post('search_type');
        if ($search_type == "class_search") {
            // Remove required validation for flexible filtering - allow empty class selection
            $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|xss_clean');
        } elseif ($search_type == "keyword_search") {
            $this->form_validation->set_rules('search_text', $this->lang->line('keyword'), 'required|trim|xss_clean');
            $data = array('search_text' => 'dummy');
            $this->form_validation->set_data($data);
        }
        if ($this->form_validation->run() == false) {
            $error = array();
            if ($search_type == "class_search") {
                $error['class_id'] = form_error('class_id');
            } elseif ($search_type == "keyword_search") {
                $error['search_text'] = form_error('search_text');
            }

            $array = array('status' => 0, 'error' => $error);
            echo json_encode($array);
        } else {
            $search_type = $this->input->post('search_type');
            $search_text = $this->input->post('search_text');
            $class_id    = $this->input->post('class_id');
            $section_id  = $this->input->post('section_id');
            $params      = array('class_id' => $class_id, 'section_id' => $section_id, 'search_type' => $search_type, 'search_text' => $search_text);
            $array       = array('status' => 1, 'error' => '', 'params' => $params);
            echo json_encode($array);
        }
    }

    public function ajaxSearch()
    {
        $class       = $this->input->post('class_id');
        $section     = $this->input->post('section_id');
        $search_text = $this->input->post('search_text');
        $search_type = $this->input->post('search_type');
        if ($search_type == "class_search") {
            $students = $this->student_model->getDatatableByClassSection($class, $section);
        } elseif ($search_type == "keyword_search") {
            $students = $this->student_model->getDatatableByFullTextSearch($search_text);
        }
        $sch_setting = $this->sch_setting_detail;
        $students    = json_decode($students);
        $dt_data     = array();
        if (!empty($students->data)) {
            foreach ($students->data as $student_key => $student) {
                $row         = array();
                $row[]       = $student->class;
                $row[]       = $student->section;
                $row[]       = $student->admission_no;
                $row[]       = "<a href='" . base_url() . "student/view/" . $student->id . "'>" . $this->customlib->getFullName($student->firstname, $student->middlename, $student->lastname, $sch_setting->middlename, $sch_setting->lastname) . "</a>";
                $sch_setting = $this->sch_setting_detail;
                if ($sch_setting->father_name) {
                    $row[] = $student->father_name;
                }
                $row[] = $this->customlib->dateformat($student->dob);
                $row[] = $student->guardian_phone;
                $row[] = "<a href=" . site_url('studentfee/addfee/' . $student->student_session_id) . "  class='btn btn-info btn-xs'>" . $this->lang->line('collect_fees') . "</a>";

                $dt_data[] = $row;
            }

        }
        $json_data = array(
            "draw"            => intval($students->draw),
            "recordsTotal"    => intval($students->recordsTotal),
            "recordsFiltered" => intval($students->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);

    }

    public function feesearch()
    {
        if (!$this->rbac->hasPrivilege('search_due_fees', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Fees Collection');
        $this->session->set_userdata('sub_menu', 'studentfee/feesearch');
        $data['title']       = $this->lang->line('student_fees');
        $class               = $this->class_model->get();
        $data['classlist']   = $class;
        $data['sch_setting'] = $this->sch_setting_detail;
        $feesessiongroup     = $this->feesessiongroup_model->getFeesByGroup();
        $module=$this->module_model->getPermissionByModulename('transport');

        $currentsessiontransportfee = $this->transportfee_model->getSessionFees($this->current_session);
        if(!empty($currentsessiontransportfee)){
        if($module['is_active']){
        $month_list= $this->customlib->getMonthDropdown($this->sch_setting_detail->start_month);
        foreach($month_list as $key=>$value){
            $transportfesstype[]=$this->transportfee_model->transportfesstype($this->current_session,$value);
        }
        $feesessiongroup[count($feesessiongroup)]=(object)array('id'=>'Transport','group_name'=>'Transport Fees','is_system'=>0,'feetypes'=>$transportfesstype);
        }
        }

        // Add Hostel Fees
        $hostel_module = $this->module_model->getPermissionByModulename('hostel');
        $currentsessionhostelfee = $this->hostelfee_model->getSessionFees($this->current_session);
        if(!empty($currentsessionhostelfee)){
        if($hostel_module['is_active']){
        $month_list= $this->customlib->getMonthDropdown($this->sch_setting_detail->start_month);
        foreach($month_list as $key=>$value){
            $hostelfesstype[]=$this->hostelfee_model->hostelfesstype($this->current_session,$value);
        }
        $feesessiongroup[count($feesessiongroup)]=(object)array('id'=>'Hostel','group_name'=>'Hostel Fees','is_system'=>0,'feetypes'=>$hostelfesstype);
        }
        }

        $data['feesessiongrouplist'] = $feesessiongroup;
        $data['fees_group']          = "";
        if (isset($_POST['feegroup_id']) && $_POST['feegroup_id'] != '') {
            $data['fees_group'] = $_POST['feegroup_id'];
        }

        if (isset($_POST['select_all']) && $_POST['select_all'] != '') {
            $data['select_all'] = $_POST['select_all'];
        }

        $this->form_validation->set_rules('feegroup[]', $this->lang->line('fee_group'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('studentfee/studentSearchFee', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $feegroups = $this->input->post('feegroup');

            $fee_group_array          = array();
            $fee_groups_feetype_array = array();
            $transport_groups_feetype_array=array();
            $hostel_groups_feetype_array=array();
            foreach ($feegroups as $fee_grp_key => $fee_grp_value) {
                $feegroup                   = explode("-", $fee_grp_value);

                if($feegroup[0]=="Transport"){
                    $transport_groups_feetype_array[] = $feegroup[1];
                }elseif($feegroup[0]=="Hostel"){
                    $hostel_groups_feetype_array[] = $feegroup[1];
                }else{
                   $fee_group_array[]          = $feegroup[0];
                $fee_groups_feetype_array[] = $feegroup[1];
                }
            }

            $fee_group_comma = implode(', ', array_map(function ($val) {return sprintf("'%s'", $val);}, array_unique($fee_group_array)));
            $fee_groups_feetype_comma = implode(', ', array_map(function ($val) {return sprintf("'%s'", $val);}, array_unique($fee_groups_feetype_array)));

            $data['student_due_fee'] = array();

            $class_id   = $this->input->post('class_id');
            $section_id = $this->input->post('section_id');

            $student_due_fee = $this->studentfee_model->getMultipleDueFees($fee_group_comma, $fee_groups_feetype_comma,$transport_groups_feetype_array, $hostel_groups_feetype_array, $class_id, $section_id);
            $students = array();

            if (!empty($student_due_fee)) {
                foreach ($student_due_fee as $student_due_fee_key => $student_due_fee_value) {


                    $amt_due = ($student_due_fee_value['is_system']) ? $student_due_fee_value['fee_master_amount'] :$student_due_fee_value['amount'];

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

                        if (!array_key_exists($student_due_fee_value['student_session_id'], $students)) {
                            $students[$student_due_fee_value['student_session_id']] = $this->add_new_student($student_due_fee_value);
                        }
                        $students[$student_due_fee_value['student_session_id']]['fees'][] = array(
                             'is_system'=>$student_due_fee_value['is_system'],
                            'amount'          => $student_due_fee_value['amount'],
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

            $data['student_remain_fees'] = $students;

            $this->load->view('layout/header', $data);
            $this->load->view('studentfee/studentSearchFee', $data);
            $this->load->view('layout/footer', $data);
        }
    }



    public function reportbyclass()
    {
        $data['title']     = 'student fees';
        $data['title']     = 'student fees';
        $class             = $this->class_model->get();
        $data['classlist'] = $class;
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $this->load->view('layout/header', $data);
            $this->load->view('studentfee/reportByClass', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $student_fees_array      = array();
            $class_id                = $this->input->post('class_id');
            $section_id              = $this->input->post('section_id');
            $student_result          = $this->student_model->searchByClassSection($class_id, $section_id);
            $data['student_due_fee'] = array();
            if (!empty($student_result)) {
                foreach ($student_result as $key => $student) {
                    $student_array                      = array();
                    $student_array['student_detail']    = $student;
                    $student_session_id                 = $student['student_session_id'];
                    $student_id                         = $student['id'];
                    $student_due_fee                    = $this->studentfee_model->getDueFeeBystudentSection($class_id, $section_id, $student_session_id);
                    $student_array['fee_detail']        = $student_due_fee;
                    $student_fees_array[$student['id']] = $student_array;
                }
            }
            $data['class_id']           = $class_id;
            $data['section_id']         = $section_id;
            $data['student_fees_array'] = $student_fees_array;
            $this->load->view('layout/header', $data);
            $this->load->view('studentfee/reportByClass', $data);
            $this->load->view('layout/footer', $data);
        }
    }

    public function view($id)
    {
        if (!$this->rbac->hasPrivilege('collect_fees', 'can_view')) {
            access_denied();
        }
        $data['title']      = 'studentfee List';
        $studentfee         = $this->studentfee_model->get($id);
        $data['studentfee'] = $studentfee;
        $this->load->view('layout/header', $data);
        $this->load->view('studentfee/studentfeeShow', $data);
        $this->load->view('layout/footer', $data);
    }

    public function deleteFee()
    {
        if (!$this->rbac->hasPrivilege('collect_fees', 'can_delete')) {
            access_denied();
        }
        $invoice_id  = $this->input->post('main_invoice');
        $sub_invoice = $this->input->post('sub_invoice');
        if (!empty($invoice_id)) {
            $this->studentfee_model->remove($invoice_id, $sub_invoice);
            $this->addaccount_model->transcationremove($invoice_id . '/' . $sub_invoice,'fees');
        }
        $array = array('status' => 'success', 'result' => 'success');
        echo json_encode($array);
    }

    public function deleteStudentDiscount()
    {
        $discount_id = $this->input->post('discount_id');
        if (!empty($discount_id)) {
            $data = array('id' => $discount_id, 'status' => 'assigned', 'payment_id' => "");
            $this->feediscount_model->updateStudentDiscount($data);
        }
        $array = array('status' => 'success', 'result' => 'success');
        echo json_encode($array);
    }

    // public function getcollectfee()
    // {
    //     $setting_result      = $this->setting_model->get();
    //     $data['settinglist'] = $setting_result;
    //     $record              = $this->input->post('data');
    //     $record_array        = json_decode($record);

    //     $fees_array = array();
    //     foreach ($record_array as $key => $value) {
    //         $fee_groups_feetype_id = $value->fee_groups_feetype_id;
    //         $fee_master_id         = $value->fee_master_id;
    //         $fee_session_group_id  = $value->fee_session_group_id;
    //         $fee_category          = $value->fee_category;
    //         $trans_fee_id          = $value->trans_fee_id;

    //         if ($fee_category == "transport") {
    //             $feeList               = $this->studentfeemaster_model->getTransportFeeByID($trans_fee_id);
    //             $feeList->fee_category = $fee_category;
    //         } else {
    //             $feeList               = $this->studentfeemaster_model->getDueFeeByFeeSessionGroupFeetype($fee_session_group_id, $fee_master_id, $fee_groups_feetype_id);
    //             $feeList->fee_category = $fee_category;
    //         }

    //         $fees_array[] = $feeList;
    //     }

    //     $data['feearray'] = $fees_array;
    //     $result           = array(
    //         'view' => $this->load->view('studentfee/getcollectfee', $data, true),
    //     );

    //     $this->output->set_output(json_encode($result));
    // }

    public function getcollectfee()
    {
        $setting_result      = $this->setting_model->get();
        $data['settinglist'] = $setting_result;
        $record              = $this->input->post('data');
        $record_array        = json_decode($record);

        $fees_array = array();

        foreach ($record_array as $key => $value) {
            $fee_groups_feetype_id = $value->fee_groups_feetype_id;
            $fee_master_id         = $value->fee_master_id;
            $fee_session_group_id  = $value->fee_session_group_id;
            $fee_category          = $value->fee_category;
            $trans_fee_id          = $value->trans_fee_id;
            $otherfeecat           = $value->otherfeecat;

            if ($fee_category == "transport") {
                $feeList               = $this->studentfeemaster_model->getTransportFeeByID($trans_fee_id);
                $feeList->fee_category = $fee_category;

            } else if ($fee_category == "hostel") {
                $feeList               = $this->studentfeemaster_model->getHostelFeeByID($trans_fee_id);
                $feeList->fee_category = $fee_category;

            }else if($otherfeecat == "otherfee"){
                $feeList               = $this->studentfeemasteradding_model->getDueFeeByFeeSessionGroupFeetype($fee_session_group_id, $fee_master_id, $fee_groups_feetype_id,$value->student_session_id);
                $feeList->fee_category = $fee_category;

            }else {
                $feeList               = $this->studentfeemaster_model->getDueFeeByFeeSessionGroupFeetype($fee_session_group_id, $fee_master_id, $fee_groups_feetype_id);
                $feeList->fee_category = $fee_category;

            }
            $feeList->otherfeecat = $otherfeecat;
            $fees_array[] = $feeList;
        }

        $data['feearray'] = $fees_array;
        $data['json'] = $record_array;
        $result           = array(
            'view' => $this->load->view('studentfee/getcollectfee', $data, true),
        );

        $this->output->set_output(json_encode($result));
    }


    public function addfee($id)
    {

        if (!$this->rbac->hasPrivilege('collect_fees', 'can_view')) {
            access_denied();
        }

        $data['sch_setting']   = $this->sch_setting_detail;
        $data['title']         = 'Student Detail';
        $data['feesinbackdate'] = $this->customlib->getfeesinbackdate();
        $student               = $this->student_model->getByStudentSession($id);
        $route_pickup_point_id = $student['route_pickup_point_id'];
        $student_session_id    = $student['student_session_id'];
        $transport_fees=[];

        $module=$this->module_model->getPermissionByModulename('transport');
        if($module['is_active']){

        $transport_fees        = $this->studentfeemaster_model->getStudentTransportFees($student_session_id, $route_pickup_point_id);
        }

        // Add Hostel Fees
        $hostel_fees = [];
        $hostel_room_id = isset($student['hostel_room_id']) ? $student['hostel_room_id'] : null;

        // Check if hostel module is active
        $hostel_module = $this->module_model->getPermissionByModulename('hostel');

        if (isset($hostel_module['is_active']) && $hostel_module['is_active'] && !empty($hostel_room_id)) {
            try {
                $hostel_fees = $this->studentfeemaster_model->getStudentHostelFees($student_session_id, $hostel_room_id);

                // Ensure hostel_fees is an array
                if (!is_array($hostel_fees)) {
                    $hostel_fees = [];
                }

                // Debug log (remove in production)
                if (ENVIRONMENT === 'development') {
                    log_message('debug', 'Hostel fees loaded for student ' . $student_session_id . ': ' . count($hostel_fees) . ' fees found');
                }

            } catch (Exception $e) {
                // Log error and continue with empty array
                log_message('error', 'Error loading hostel fees for student ' . $student_session_id . ': ' . $e->getMessage());
                $hostel_fees = [];
            }
        } else {
            // Debug information for why hostel fees are not loaded
            if (ENVIRONMENT === 'development') {
                $debug_msg = 'Hostel fees not loaded - ';
                if (!isset($hostel_module['is_active']) || !$hostel_module['is_active']) {
                    $debug_msg .= 'hostel module inactive';
                } elseif (empty($hostel_room_id)) {
                    $debug_msg .= 'student not assigned to hostel room';
                }
                log_message('debug', $debug_msg . ' for student ' . $student_session_id);
            }
        }

        $data['student']       = $student;
        $student_due_fee       = $this->studentfeemaster_model->getStudentFees($id);

        // Group fees by session
        $fees_by_session = array();
        $additional_fees_by_session = array();
        $sessions = array();

        // Group regular fees by session
        if (!empty($student_due_fee)) {
            foreach ($student_due_fee as $fee) {
                if (isset($fee->session_id) && !empty($fee->session_id) && isset($fee->session) && !empty($fee->session)) {
                    $session_id = $fee->session_id;

                    // Store session information
                    if (!isset($sessions[$session_id])) {
                        $sessions[$session_id] = $fee->session;
                    }

                    // Group fees by session
                    if (!isset($fees_by_session[$session_id])) {
                        $fees_by_session[$session_id] = array();
                    }

                    $fees_by_session[$session_id][] = $fee;
                } else {
                    // For fees without session info, put in "Other Fees" category
                    if (!isset($fees_by_session[0])) {
                        $fees_by_session[0] = array();
                        $sessions[0] = "Other Fees";
                    }

                    $fees_by_session[0][] = $fee;
                }
            }
        }

        // Group additional fees by session
        $student_additional_fee = $this->studentfeemasteradding_model->getStudentFees($id);
        $additional_fees_by_session = array();

        if (!empty($student_additional_fee)) {
            foreach ($student_additional_fee as $fee) {
                if (isset($fee->session_id) && !empty($fee->session_id) && isset($fee->session) && !empty($fee->session)) {
                    $session_id = $fee->session_id;

                    // Store session information
                    if (!isset($sessions[$session_id])) {
                        $sessions[$session_id] = $fee->session;
                    }

                    // Group additional fees by session
                    if (!isset($additional_fees_by_session[$session_id])) {
                        $additional_fees_by_session[$session_id] = array();
                    }

                    $additional_fees_by_session[$session_id][] = $fee;
                } else {
                    // For fees without session info, put in "Other Fees" category
                    if (!isset($additional_fees_by_session[0])) {
                        $additional_fees_by_session[0] = array();
                        if (!isset($sessions[0])) {
                            $sessions[0] = "Other Fees";
                        }
                    }

                    $additional_fees_by_session[0][] = $fee;
                }
            }
        }

        // If no session information found in fees, try to get it from the student data
        if (empty($sessions) && isset($student['session_id']) && !empty($student['session_id'])) {
            $current_session_id = $student['session_id'];
            $session_info = $this->session_model->get($current_session_id);

            if (!empty($session_info) && isset($session_info['session'])) {
                $sessions[$session_info['id']] = $session_info['session'];
            }
        }

        // If still no session information, try to get it from the student data directly
        if (empty($sessions) && isset($student['session']) && !empty($student['session'])) {
            $sessions[0] = $student['session'];
        }

        // Store session information and grouped fees for the view
        $data['sessions'] = $sessions;
        $data['fees_by_session'] = $fees_by_session;
        $data['additional_fees_by_session'] = $additional_fees_by_session;
        $student_discount_fee  = $this->feediscount_model->getStudentFeesDiscount($id);

        // Check for pending discount requests for this student
        try {
            $pending_discounts = $this->feediscount_model->getStudentPendingDiscounts($student_session_id);
            $data['pending_discounts'] = $pending_discounts;

            // Create a lookup array for quick checking if a specific fee has pending discount
            $pending_discount_lookup = array();
            if (!empty($pending_discounts)) {
                foreach ($pending_discounts as $pending) {
                    $key = $pending['fee_groups_feetype_id'] . '_' . $pending['student_fees_master_id'];
                    $pending_discount_lookup[$key] = $pending;
                }
            }
            $data['pending_discount_lookup'] = $pending_discount_lookup;
        } catch (Exception $e) {
            // If there's an error with discount functionality, continue without it
            error_log('Discount functionality error: ' . $e->getMessage());
            $data['pending_discounts'] = array();
            $data['pending_discount_lookup'] = array();
        }

        $data['transport_fees']         = $transport_fees;
        $data['hostel_fees']            = $hostel_fees;
        $data['student_discount_fee']   = $student_discount_fee;
        $data['student_due_fee']        = $student_due_fee;
        // We're not passing the ungrouped additional fees to avoid duplication
        // Instead, we're only passing the grouped fees

        $category                       = $this->category_model->get();
        $data['categorylist']           = $category;
        $class_section                  = $this->student_model->getClassSection($student["class_id"]);
        $data["class_section"]          = $class_section;
        $session                        = $this->setting_model->getCurrentSession();
        $studentlistbysection           = $this->student_model->getStudentClassSection($student["class_id"], $session);
        $data["studentlistbysection"]   = $studentlistbysection;
        $student_processing_fee         = $this->studentfeemaster_model->getStudentProcessingFees($id);
        $data['student_processing_fee'] = false;

        foreach ($student_processing_fee as $key => $processing_value) {
            if (!empty($processing_value->fees)) {
                $data['student_processing_fee'] = true;
            }
        }

        // Get advance payment information for the student
        $advance_balance = $this->AdvancePayment_model->getAdvanceBalance($student_session_id);
        $advance_payments = $this->AdvancePayment_model->getStudentAdvancePayments($student_session_id);
        $advance_usage_history = $this->AdvancePayment_model->getAdvanceUsageHistory(null, $student_session_id);

        $data['advance_balance'] = $advance_balance;
        $data['advance_payments'] = $advance_payments;
        $data['advance_usage_history'] = $advance_usage_history;
        $data['student_session_id'] = $student_session_id;

        $this->load->view('layout/header', $data);
        $this->load->view('studentfee/studentAddfee', $data);
        $this->load->view('layout/footer', $data);
    }

    public function getProcessingfees($id)
    {
        if (!$this->rbac->hasPrivilege('collect_fees', 'can_add')) {
            access_denied();
        }

        $student               = $this->student_model->getByStudentSession($id);
        $route_pickup_point_id = $student['route_pickup_point_id'];
        $student_session_id    = $student['student_session_id'];
        $transport_fees        = $this->studentfeemaster_model->getStudentTransportFees($student_session_id, $route_pickup_point_id);
        $data['student']       = $student;
        $student_due_fee       = $this->studentfeemaster_model->getStudentProcessingFees($id);
        $data['transport_fees']  = $transport_fees;
        $data['student_due_fee'] = $student_due_fee;

        $result = array(
            'view' => $this->load->view('user/student/getProcessingfees', $data, true),
        );
        $this->output->set_output(json_encode($result));
    }

    public function deleteTransportFee()
    {
        $id = $this->input->post('feeid');
        $this->studenttransportfee_model->remove($id);
        $array = array('status' => 'success', 'result' => 'success');
        echo json_encode($array);
    }

    public function delete($id)
    {
        $data['title'] = 'studentfee List';
        $this->studentfee_model->remove($id);
        redirect('studentfee/index');
    }

    public function create()
    {
        if (!$this->rbac->hasPrivilege('collect_fees', 'can_view')) {
            access_denied();
        }
        $data['title'] = 'Add studentfee';
        $this->form_validation->set_rules('category', $this->lang->line('category'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('studentfee/studentfeeCreate', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $data = array(
                'category' => $this->input->post('category'),
            );
            $this->studentfee_model->add($data);
            $this->session->set_flashdata('msg', '<div studentfee="alert alert-success text-center">' . $this->lang->line('success_message') . '</div>');
            redirect('studentfee/index');
        }
    }

    public function edit($id)
    {
        if (!$this->rbac->hasPrivilege('collect_fees', 'can_edit')) {
            access_denied();
        }
        $data['title']      = 'Edit studentfees';
        $data['id']         = $id;
        $studentfee         = $this->studentfee_model->get($id);
        $data['studentfee'] = $studentfee;
        $this->form_validation->set_rules('category', $this->lang->line('category'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('studentfee/studentfeeEdit', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $data = array(
                'id'       => $id,
                'category' => $this->input->post('category'),
            );
            $this->studentfee_model->add($data);
            $this->session->set_flashdata('msg', '<div studentfee="alert alert-success text-center">' . $this->lang->line('update_message') . '</div>');
            redirect('studentfee/index');
        }
    }

    public function addstudentfee()
    {
        // Add error handling to catch fatal errors
        error_reporting(E_ALL);
        ini_set('display_errors', 0); // Don't display errors to browser, log them instead
        
        // Immediate response test - remove this after debugging
        error_log("DEBUGGING: addstudentfee method called at " . date('Y-m-d H:i:s'));
        
        // Test if we can respond immediately
        header('Content-Type: application/json');
        
        $fee_category = $this->input->post('fee_category');
        $transport_fees_id = $this->input->post('transport_fees_id');
        $hostel_fees_id = $this->input->post('hostel_fees_id');
        $collect_from_advance = $this->input->post('collect_from_advance');
        
        // TEMPORARY DEBUG RESPONSE - Remove after testing
        $debug_response = array(
            'status' => 'debug_success',
            'message' => 'Method is being called successfully',
            'post_data' => array(
                'fee_category' => $fee_category,
                'transport_fees_id' => $transport_fees_id,
                'hostel_fees_id' => $hostel_fees_id,
                'collect_from_advance' => $collect_from_advance
            ),
            'timestamp' => date('Y-m-d H:i:s')
        );
        echo json_encode($debug_response);
        return;
    }
        if ($fee_category == 'transport' && $transport_fees_id > 0) {
            // For transport fees, transport_fees_id is required
            $this->form_validation->set_rules('transport_fees_id', 'Transport Fee ID', 'required|trim|xss_clean|numeric');
        } elseif ($fee_category == 'hostel' && $hostel_fees_id > 0) {
            // For hostel fees, hostel_fees_id is required
            $this->form_validation->set_rules('hostel_fees_id', 'Hostel Fee ID', 'required|trim|xss_clean|numeric');
        } else {
            // For regular fees, these are required
            $this->form_validation->set_rules('student_fees_master_id', $this->lang->line('fee_master'), 'required|trim|xss_clean');
            $this->form_validation->set_rules('fee_groups_feetype_id', $this->lang->line('student'), 'required|trim|xss_clean');
        }
        
        // Common validation rules for all fee types
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'required|trim|xss_clean|numeric');
        $this->form_validation->set_rules('amount_discount', $this->lang->line('discount'), 'required|trim|numeric|xss_clean');
        $this->form_validation->set_rules('amount_fine', $this->lang->line('fine'), 'required|trim|numeric|xss_clean');
        $this->form_validation->set_rules('payment_mode', $this->lang->line('payment_mode'), 'required|trim|xss_clean');
        
        // Account name is only required if not collecting from advance payment
        if ($collect_from_advance != 1) {
            $this->form_validation->set_rules('accountname', $this->lang->line('accountname'), 'required|trim|xss_clean');
        }

        if ($this->form_validation->run() == false) {
            $data = array(
                'amount'                 => form_error('amount'),
                'student_fees_master_id' => form_error('student_fees_master_id'),
                'fee_groups_feetype_id'  => form_error('fee_groups_feetype_id'),
                'transport_fees_id'      => form_error('transport_fees_id'),
                'hostel_fees_id'         => form_error('hostel_fees_id'),
                'amount_discount'        => form_error('amount_discount'),
                'amount_fine'            => form_error('amount_fine'),
                'payment_mode'           => form_error('payment_mode'),
                'date'                   => form_error('date'),
                'accountname'            => form_error('accountname'),
            );
            $array = array('status' => 'fail', 'error' => $data);
            echo json_encode($array);
        } else {
            // DEBUGGING: Test if we reach here
            error_log("DEBUGGING: Validation passed, proceeding with fee collection");
            
            // Temporary response to check if validation is working - REMOVE AFTER DEBUGGING
            /*
            $debug_array = array(
                'status' => 'success', 
                'message' => 'Validation passed successfully',
                'debug' => 'Processing would start here'
            );
            echo json_encode($debug_array);
            return;
            */

            $staff_record = $this->staff_model->get($this->customlib->getStaffID());

            $collected_by             = $this->customlib->getAdminSessionUserName() . "(" . $staff_record['employee_id'] . ")";
            $student_fees_discount_id = $this->input->post('student_fees_discount_id');
            $student_session_id       = $this->input->post('student_session_id');
            $collect_from_advance     = $this->input->post('collect_from_advance');
            
            // Get the original amount first
            $original_amount = convertCurrencyFormatToBaseAmount($this->input->post('amount'));
            $advance_applied = 0;
            
            // Handle advance payment logic BEFORE creating json_array
            if ($collect_from_advance == 1) {
                $advance_balance = $this->AdvancePayment_model->getAdvanceBalance($student_session_id);

                if ($advance_balance > 0 && $original_amount > 0) {
                    // Validate that the amount doesn't exceed advance balance
                    if ($original_amount > $advance_balance) {
                        $array = array(
                            'status' => 'fail', 
                            'error' => array('amount' => 'Amount cannot exceed available advance balance')
                        );
                        echo json_encode($array);
                        return;
                    }
                    
                    $advance_applied = $original_amount; // Use the full entered amount from advance
                }
            }
            
            // Create json_array with the correct amount (always use original amount for display)
            $json_array = array(
                'amount'          => $original_amount, // Always store the full amount
                'amount_discount' => convertCurrencyFormatToBaseAmount($this->input->post('amount_discount')),
                'amount_fine'     => convertCurrencyFormatToBaseAmount($this->input->post('amount_fine')),
                'date'            => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
                'description'     => $this->input->post('description'),
                'collected_by'    => $collected_by,
                'payment_mode'    => $this->input->post('payment_mode'),
                'received_by'     => $staff_record['id'],
            );
            
            // Add advance payment tracking if applicable
            if ($collect_from_advance == 1 && $advance_applied > 0) {
                $json_array['advance_applied'] = $advance_applied;
                $json_array['cash_amount'] = 0;
                $json_array['payment_source'] = 'advance';
                
                // Add detailed transfer information
                // $advance_balance_before = $this->AdvancePayment_model->getStudentAdvanceBalance($student_session_id);
                $advance_balance_before = $this->AdvancePayment_model->getAdvanceBalance($student_session_id);
                $json_array['advance_transfer_details'] = array(
                    'transfer_amount' => $advance_applied,
                    'advance_balance_before' => $advance_balance_before,
                    'advance_balance_after' => $advance_balance_before - $advance_applied,
                    'transfer_type' => ($advance_applied == $advance_balance_before) ? 'Complete Balance Transfer' : 'Partial Balance Transfer',
                    'account_impact' => 'Zero Cash Entry - Direct Advance Utilization',
                    'transfer_timestamp' => date('Y-m-d H:i:s')
                );
            }

            $student_fees_master_id = $this->input->post('student_fees_master_id');
            $fee_groups_feetype_id  = $this->input->post('fee_groups_feetype_id');
            $transport_fees_id      = $this->input->post('transport_fees_id');
            $hostel_fees_id         = $this->input->post('hostel_fees_id');
            $fee_category           = $this->input->post('fee_category');
            
            // Debug logging for hostel fees
            if ($fee_category == 'hostel') {
                error_log("HOSTEL FEE DEBUG - Controller Input:");
                error_log("hostel_fees_id: " . $hostel_fees_id);
                error_log("student_session_id: " . $student_session_id);
                error_log("fee_category: " . $fee_category);
                error_log("POST data: " . print_r($_POST, true));
            }

            $data = array(
                'fee_category'           => $fee_category,
                'student_fees_master_id' => $this->input->post('student_fees_master_id'),
                'fee_groups_feetype_id'  => $this->input->post('fee_groups_feetype_id'),
                'student_session_id'     => $student_session_id,
                'amount_detail'          => $json_array,
            );

            if ($transport_fees_id != 0 && $fee_category == "transport") {
                $mailsms_array                    = new stdClass();
                $data['student_fees_master_id']   = null;
                $data['fee_groups_feetype_id']    = null;
                $data['student_transport_fee_id'] = $transport_fees_id;

                $mailsms_array                 = $this->studenttransportfee_model->getTransportFeeMasterByStudentTransportID($transport_fees_id);
                $mailsms_array->fee_group_name = $this->lang->line("transport_fees");
                $mailsms_array->type           = $mailsms_array->month;
                $mailsms_array->code           = "";
            } elseif ($hostel_fees_id != 0 && $fee_category == "hostel") {
                $mailsms_array                    = new stdClass();
                // Remove student_fees_master_id and fee_groups_feetype_id for hostel fees
                unset($data['student_fees_master_id']);
                unset($data['fee_groups_feetype_id']);
                $data['student_hostel_fee_id']    = $hostel_fees_id;
                // Ensure student_session_id is preserved for hostel fees
                $data['student_session_id']       = $student_session_id;
                
                // Debug logging for hostel fee data array
                error_log("HOSTEL FEE DEBUG - Data array before fee_deposit:");
                error_log(print_r($data, true));

                $mailsms_array                 = $this->studenthostelfee_model->getHostelFeeMasterByStudentHostelID($hostel_fees_id);
                $mailsms_array->fee_group_name = $this->lang->line("hostel_fees");
                $mailsms_array->type           = $mailsms_array->month;
                $mailsms_array->code           = "";
            } else {

                $mailsms_array = $this->feegrouptype_model->getFeeGroupByIDAndStudentSessionID($this->input->post('fee_groups_feetype_id'), $this->input->post('student_session_id'));

                if($mailsms_array->is_system){
                     $mailsms_array->amount=$mailsms_array->balance_fee_master_amount;
                }
            }

            $action             = $this->input->post('action');
            $send_to            = $this->input->post('guardian_phone');
            $email              = $this->input->post('guardian_email');
            $parent_app_key     = $this->input->post('parent_app_key');

            $inserted_id        = $this->studentfeemaster_model->fee_deposit($data, $send_to, $student_fees_discount_id);

            $receipt_data1           = json_decode($inserted_id);

            // Apply advance payment if any was calculated
            if ($advance_applied > 0 && $receipt_data1) {
                $available_advances = $this->AdvancePayment_model->getAvailableAdvancePayments($student_session_id);
                $remaining_to_apply = $advance_applied;
                $transfer_details = array();

                foreach ($available_advances as $advance) {
                    if ($remaining_to_apply <= 0) break;

                    $amount_to_use = min($advance->balance, $remaining_to_apply);
                    if ($amount_to_use > 0) {
                        // Record detailed transfer information
                        $transfer_details[] = array(
                            'advance_payment_id' => $advance->id,
                            'original_advance_date' => isset($advance->payment_date) ? $advance->payment_date : date('Y-m-d'),
                            'original_advance_amount' => $advance->amount,
                            'balance_before_transfer' => $advance->balance,
                            'amount_transferred' => $amount_to_use,
                            'balance_after_transfer' => $advance->balance - $amount_to_use,
                            'fee_invoice' => $receipt_data1->invoice_id . '/' . $receipt_data1->sub_invoice_id,
                            'transfer_timestamp' => date('Y-m-d H:i:s'),
                            'fee_category' => $fee_category,
                            'student_session_id' => $student_session_id
                        );
                        
                        $this->AdvancePayment_model->applyAdvanceToFee(
                            $advance->id,
                            $amount_to_use,
                            $receipt_data1->invoice_id,
                            null,
                            $fee_category,
                            'Applied to fee payment - Invoice: ' . $receipt_data1->invoice_id . '/' . $receipt_data1->sub_invoice_id
                        );
                        
                        // Store detailed transfer tracking with error handling
                        try {
                            $this->storeAdvanceTransferDetails($transfer_details[count($transfer_details) - 1]);
                        } catch (Exception $e) {
                            error_log("Error storing advance transfer details: " . $e->getMessage());
                        }
                        
                        $remaining_to_apply -= $amount_to_use;
                    }
                }
                
                // Log comprehensive transfer summary
                if (!empty($transfer_details)) {
                    $transfer_summary = array(
                        'total_transferred' => $advance_applied,
                        'transfer_count' => count($transfer_details),
                        'transfers' => $transfer_details,
                        'fee_receipt' => $receipt_data1->invoice_id . '/' . $receipt_data1->sub_invoice_id,
                        'student_session_id' => $student_session_id,
                        'processed_at' => date('Y-m-d H:i:s')
                    );
                    
                    // Store transfer summary in session for display
                    $this->session->set_userdata('last_advance_transfer', $transfer_summary);
                    
                    // Log for debugging
                    error_log("ADVANCE TRANSFER SUMMARY: " . json_encode($transfer_summary));
                }
            }

            $accounttranscationarray = array(
                'receiptid'=> $receipt_data1->invoice_id . '/' . $receipt_data1->sub_invoice_id,
                'accountid'=>$this->input->post('accountname'),
                'amount' => $collect_from_advance == 1 ? 0 : convertCurrencyFormatToBaseAmount($this->input->post('amount')),
                'date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
                'type' => 'fees',
                'description'     => $this->input->post('description') . ($advance_applied > 0 ? ' (Advance Applied: ' . amountFormat($advance_applied) . ')' : ''),
                'status' => 'credit',
            );

            // Only add account transaction if there's an actual cash payment (not from advance)
            if ($collect_from_advance != 1 && convertCurrencyFormatToBaseAmount($this->input->post('amount')) > 0) {
                $accounttranscation = $this->addaccount_model->addingtranscation($accounttranscationarray);
            }



            $print_record = array();
            if ($action == "print") {
                $receipt_data           = json_decode($inserted_id);
                $data['sch_setting']    = $this->sch_setting_detail;

                $student                = $this->studentsession_model->searchStudentsBySession($student_session_id);
                $data['student']        = $student;
                $data['sub_invoice_id'] = $receipt_data->sub_invoice_id;

                $setting_result         = $this->setting_model->get();
                $data['settinglist']    = $setting_result;

                if ($transport_fees_id != 0 && $fee_category == "transport") {

                    $fee_record = $this->studentfeemaster_model->getTransportFeeByInvoice($receipt_data->invoice_id, $receipt_data->sub_invoice_id);
                    $data['feeList']        = $fee_record;
                        $print_record = $this->load->view('print/printTransportFeesByName', $data, true);

                } elseif ($hostel_fees_id != 0 && $fee_category == "hostel") {

                    $fee_record = $this->studentfeemaster_model->getHostelFeeByInvoice($receipt_data->invoice_id, $receipt_data->sub_invoice_id);
                    $data['feeList']        = $fee_record;
                        $print_record = $this->load->view('print/printHostelFeesByName', $data, true);

                } else {

                    $fee_record             = $this->studentfeemaster_model->getFeeByInvoice($receipt_data->invoice_id, $receipt_data->sub_invoice_id);
                    $data['feeList']        = $fee_record;
                        $print_record = $this->load->view('print/printFeesByName', $data, true);
                }
            }

            // $mailsms_array->invoice            = $inserted_id;
            // $mailsms_array->student_session_id = $student_session_id;
            // $mailsms_array->contact_no         = $send_to;
            // $mailsms_array->email              = $email;
            // $mailsms_array->parent_app_key     = $parent_app_key;
            // $mailsms_array->fee_category       = $fee_category;

            // $this->mailsmsconf->mailsms('fee_submission', $mailsms_array);

            $array = array('status' => 'success', 'error' => '', 'print' => $print_record);
            
            // Add advance payment transfer information if applicable
            if ($advance_applied > 0 && $receipt_data1) {
                $array['advance_applied'] = $advance_applied;
                $array['fee_receipt'] = $receipt_data1->invoice_id . '/' . $receipt_data1->sub_invoice_id;
                $array['transfer_type'] = 'Direct advance payment utilization';
                $array['account_impact'] = 'Zero cash entry - funds transferred from advance balance';
                
                // Get transfer details from session if available
                $transfer_summary = $this->session->userdata('last_advance_transfer');
                if ($transfer_summary) {
                    $array['transfer_details'] = $transfer_summary;
                    // Clear the session data
                    $this->session->unset_userdata('last_advance_transfer');
                }
            }
            
            echo json_encode($array);
        }
    }

    // public function printFeesByName()
    // {
    //     $data                   = array('payment' => "0");
    //     $record                 = $this->input->post('data');
    //     $fee_category           = $this->input->post('fee_category');
    //     $invoice_id             = $this->input->post('main_invoice');
    //     $sub_invoice_id         = $this->input->post('sub_invoice');
    //     $student_session_id     = $this->input->post('student_session_id');
    //     $setting_result         = $this->setting_model->get();
    //     $data['settinglist']    = $setting_result;
    //     $student                = $this->studentsession_model->searchStudentsBySession($student_session_id);
    //     $data['student']        = $student;
    //     $data['sub_invoice_id'] = $sub_invoice_id;
    //     $data['sch_setting']    = $this->sch_setting_detail;

    //     $data['superadmin_rest'] = $this->customlib->superadmin_visible();

    //     if ($fee_category == "transport") {
    //         $fee_record      = $this->studentfeemaster_model->getTransportFeeByInvoice($invoice_id, $sub_invoice_id);
    //         $data['feeList'] = $fee_record;
    //         $page            = $this->load->view('print/printTransportFeesByName', $data, true);
    //     } else {
    //         $fee_record      = $this->studentfeemaster_model->getFeeByInvoice($invoice_id, $sub_invoice_id);
    //         $data['feeList'] = $fee_record;
    //         $page = $this->load->view('print/printFeesByName', $data, true);
    //     }

    //     echo json_encode(array('status' => 1, 'page' => $page));

    // }


    public function printFeesByName()
    {
        $data                   = array('payment' => "0");
        $record                 = $this->input->post('data');
        $fee_category           = $this->input->post('fee_category');
        $invoice_id             = $this->input->post('main_invoice');
        $sub_invoice_id         = $this->input->post('sub_invoice');
        $student_session_id     = $this->input->post('student_session_id');

        $fee_groups_feetype_id = $this->input->post('fee_groups_feetype_id');
        $fee_master_id         = $this->input->post('fee_master_id');
        $fee_session_group_id  = $this->input->post('fee_session_group_id');

        $setting_result         = $this->setting_model->get();
        $data['settinglist']    = $setting_result;
        $student                = $this->studentsession_model->searchStudentsBySession($student_session_id);
        $data['student']        = $student;
        $data['sub_invoice_id'] = $sub_invoice_id;
        $data['sch_setting']    = $this->sch_setting_detail;

        $data['superadmin_rest'] = $this->customlib->superadmin_visible();

        if ($fee_category == "transport") {
            $fee_record      = $this->studentfeemaster_model->getTransportFeeByInvoice($invoice_id, $sub_invoice_id);
            $data['feeList'] = $fee_record;
            $page            = $this->load->view('print/printTransportFeesByName', $data, true);
        } elseif ($fee_category == "hostel") {
            $fee_record      = $this->studentfeemaster_model->getHostelFeeByInvoice($invoice_id, $sub_invoice_id);
            $data['feeList'] = $fee_record;
            $page            = $this->load->view('print/printHostelFeesByName', $data, true);
        } else {
            $data['totalfeeList']       = $this->studentfeemaster_model->getDueFeeByFeeSessionGroupFeetype($fee_session_group_id, $fee_master_id, $fee_groups_feetype_id);

            $fee_record      = $this->studentfeemaster_model->getFeeByInvoice($invoice_id, $sub_invoice_id);
            $data['feeList'] = $fee_record;
            $page = $this->load->view('print/printFeesByName', $data, true);
        }

        echo json_encode(array('status' => 1, 'page' => $page));

    }




    public function printFeesByGroup()
    {
        $fee_category        = $this->input->post('fee_category');
        $trans_fee_id        = $this->input->post('trans_fee_id');
        $setting_result      = $this->setting_model->get();
        $data['settinglist'] = $setting_result;
        $data['sch_setting'] = $this->sch_setting_detail;

        if ($fee_category == "transport") {
            $data['feeList'] = $this->studentfeemaster_model->getTransportFeeByID($trans_fee_id);
            $page = $this->load->view('print/printTransportFeesByGroup', $data, true);

        } elseif ($fee_category == "hostel") {
            $data['feeList'] = $this->studentfeemaster_model->getHostelFeeByID($trans_fee_id);
            
            // Debug: Log the hostel fee data
            error_log("HOSTEL FEE PRINT DEBUG - trans_fee_id: " . $trans_fee_id);
            error_log("HOSTEL FEE PRINT DEBUG - feeList data: " . print_r($data['feeList'], true));
            
            $page = $this->load->view('print/printHostelFeesByGroup', $data, true);

        } else {

            $fee_groups_feetype_id = $this->input->post('fee_groups_feetype_id');
            $fee_master_id         = $this->input->post('fee_master_id');
            $fee_session_group_id  = $this->input->post('fee_session_group_id');
            $data['feeList']       = $this->studentfeemaster_model->getDueFeeByFeeSessionGroupFeetype($fee_session_group_id, $fee_master_id, $fee_groups_feetype_id);
            $page                  = $this->load->view('print/printFeesByGroup', $data, true);
        }

        echo json_encode(array('status' => 1, 'page' => $page));

    }

    // public function printFeesByGroupArray()
    // {
    //     $data['sch_setting'] = $this->sch_setting_detail;
    //     $record              = $this->input->post('data');
    //     $record_array        = json_decode($record);
    //     $fees_array          = array();
    //     foreach ($record_array as $key => $value) {
    //         $fee_groups_feetype_id = $value->fee_groups_feetype_id;
    //         $fee_master_id         = $value->fee_master_id;
    //         $fee_session_group_id  = $value->fee_session_group_id;
    //         $fee_category          = $value->fee_category;
    //         $trans_fee_id          = $value->trans_fee_id;

    //         if ($fee_category == "transport") {
    //             $feeList               = $this->studentfeemaster_model->getTransportFeeByID($trans_fee_id);
    //             $feeList->fee_category = $fee_category;
    //         } else {
    //             $feeList               = $this->studentfeemaster_model->getDueFeeByFeeSessionGroupFeetype($fee_session_group_id, $fee_master_id, $fee_groups_feetype_id);
    //             $feeList->fee_category = $fee_category;
    //         }

    //         $fees_array[] = $feeList;
    //     }

    //     $data['feearray'] = $fees_array;
    //     $this->load->view('print/printFeesByGroupArray', $data);
    // }

    public function printFeesByGroupArray()
    {
        $data['sch_setting'] = $this->sch_setting_detail;
        $record              = $this->input->post('data');
        $record_array        = json_decode($record);
        $fees_array          = array();
        
        foreach ($record_array as $key => $value) {
            $fee_groups_feetype_id = $value->fee_groups_feetype_id;
            $fee_master_id         = $value->fee_master_id;
            $fee_session_group_id  = $value->fee_session_group_id;
            $fee_category          = $value->fee_category;
            $trans_fee_id          = $value->trans_fee_id;
            $otherfeecat           = isset($value->otherfeecat) ? $value->otherfeecat : '';
            $student_session_id    = $value->student_session_id;
            $hostel_fee_id         = isset($value->hostel_fee_id) ? $value->hostel_fee_id : null;

            if ($fee_category == "transport") {
                $feeList = $this->studentfeemaster_model->getTransportFeeByID($trans_fee_id);
                if ($feeList) {
                    $feeList->fee_category = $fee_category;
                    $fees_array[] = $feeList;
                }
            } else if ($fee_category == "hostel") {
                // For hostel fees - use the proper hostel_fee_id from the JSON data
                if (!empty($hostel_fee_id)) {
                    $feeList = $this->studentfeemaster_model->getHostelFeeByID($hostel_fee_id);
                    if ($feeList) {
                        $feeList->fee_category = $fee_category;
                        $feeList->hostel_fee_id = $hostel_fee_id;
                        $fees_array[] = $feeList;
                    }
                } else {
                    // Fallback: If hostel_fee_id is not available, try using trans_fee_id
                    error_log("DEBUG: No hostel_fee_id found, trying trans_fee_id: " . $trans_fee_id);
                    $feeList = $this->studentfeemaster_model->getHostelFeeByID($trans_fee_id);
                    if ($feeList) {
                        $feeList->fee_category = $fee_category;
                        $feeList->hostel_fee_id = $trans_fee_id;
                        $fees_array[] = $feeList;
                    }
                }
            } else if ($otherfeecat == "otherfee") {
                $feeList = $this->studentfeemasteradding_model->getDueFeeByFeeSessionGroupFeetype(
                    $fee_session_group_id, 
                    $fee_master_id, 
                    $fee_groups_feetype_id,
                    $student_session_id
                );
                if ($feeList) {
                    $feeList->fee_category = $fee_category;
                    $fees_array[] = $feeList;
                }
            } else {
                $feeList = $this->studentfeemaster_model->getDueFeeByFeeSessionGroupFeetype(
                    $fee_session_group_id, 
                    $fee_master_id, 
                    $fee_groups_feetype_id
                );
                if ($feeList) {
                    $feeList->fee_category = $fee_category;
                    $fees_array[] = $feeList;
                }
            }
        }

        $data['feearray'] = $fees_array;
        $this->load->view('print/printFeesByGroupArray', $data);
    }

    public function searchpayment()
    {
        if (!$this->rbac->hasPrivilege('search_fees_payment', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Fees Collection');
        $this->session->set_userdata('sub_menu', 'studentfee/searchpayment');
        $data['title'] = $this->lang->line('fees_collection');

        $this->form_validation->set_rules('paymentid', $this->lang->line('payment_id'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {

        } else {
            $paymentid = $this->input->post('paymentid');
            $invoice   = explode("/", $paymentid);

            if (array_key_exists(0, $invoice) && array_key_exists(1, $invoice)) {
                $invoice_id             = $invoice[0];
                $sub_invoice_id         = $invoice[1];
                $feeList                = $this->studentfeemaster_model->getFeeByInvoice($invoice_id, $sub_invoice_id);
                $data['feeList']        = $feeList;
                $data['sub_invoice_id'] = $sub_invoice_id;
            } else {
                $data['feeList'] = array();
            }
        }
        $data['sch_setting'] = $this->sch_setting_detail;

        $this->load->view('layout/header', $data);
        $this->load->view('studentfee/searchpayment', $data);
        $this->load->view('layout/footer', $data);
    }

    public function addfeegroup()
    {
        $this->form_validation->set_rules('fee_session_groups', $this->lang->line('fee_group'), 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
            $data = array(
                'fee_session_groups' => form_error('fee_session_groups'),
            );
            $array = array('status' => 'fail', 'error' => $data);
            echo json_encode($array);
        } else {
            $student_session_id     = $this->input->post('student_session_id');
            $fee_session_groups     = $this->input->post('fee_session_groups');
            $student_sesssion_array = isset($student_session_id) ? $student_session_id : array();
            $student_ids            = $this->input->post('student_ids');
            $delete_student         = array_diff($student_ids, $student_sesssion_array);

            $preserve_record = array();
            if (!empty($student_sesssion_array)) {
                foreach ($student_sesssion_array as $key => $value) {
                    $insert_array = array(
                        'student_session_id'   => $value,
                        'fee_session_group_id' => $fee_session_groups,
                    );
                    $inserted_id = $this->studentfeemaster_model->add($insert_array);

                    $preserve_record[] = $inserted_id;
                }
            }
            if (!empty($delete_student)) {
                $this->studentfeemaster_model->delete($fee_session_groups, $delete_student);
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            echo json_encode($array);
        }
    }

    public function geBalanceFee()
    {
        $this->form_validation->set_rules('fee_groups_feetype_id', $this->lang->line('fee_groups_feetype_id'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('student_fees_master_id', $this->lang->line('student_fees_master_id'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('student_session_id', $this->lang->line('student_session_id'), 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
            $data = array(
                'fee_groups_feetype_id'  => form_error('fee_groups_feetype_id'),
                'student_fees_master_id' => form_error('student_fees_master_id'),
                'student_session_id'     => form_error('student_session_id'),
            );
            $array = array('status' => 'fail', 'error' => $data);
            echo json_encode($array);
        } else {
            $data                 = array();
            $student_session_id   = $this->input->post('student_session_id');
            $discount_not_applied = $this->getNotAppliedDiscount($student_session_id);

            $fee_category = $this->input->post('fee_category');
            if ($fee_category == "transport") {
                $trans_fee_id         = $this->input->post('trans_fee_id');
                $remain_amount_object = $this->getStudentTransportFeetypeBalance($trans_fee_id);
                $remain_amount        = (float) json_decode($remain_amount_object)->balance;
                $remain_amount_fine   = json_decode($remain_amount_object)->fine_amount;
            } elseif ($fee_category == "hostel") {
                $hostel_fee_id        = $this->input->post('hostel_fee_id') ? $this->input->post('hostel_fee_id') : $this->input->post('trans_fee_id');
                $remain_amount_object = $this->getStudentHostelFeetypeBalance($hostel_fee_id);
                $remain_amount        = (float) json_decode($remain_amount_object)->balance;
                $remain_amount_fine   = json_decode($remain_amount_object)->fine_amount;
            } else {
                $fee_groups_feetype_id  = $this->input->post('fee_groups_feetype_id');
                $student_fees_master_id = $this->input->post('student_fees_master_id');
                $remain_amount_object   = $this->getStuFeetypeBalance($fee_groups_feetype_id, $student_fees_master_id);
                $remain_amount          = json_decode($remain_amount_object)->balance;
                $remain_amount_fine     = json_decode($remain_amount_object)->fine_amount;
            }

            $remain_amount = number_format($remain_amount, 2, ".", "");

            $array = array('status' => 'success', 'error' => '', 'balance' => convertBaseAmountCurrencyFormat($remain_amount), 'discount_not_applied' => $discount_not_applied, 'remain_amount_fine' => convertBaseAmountCurrencyFormat($remain_amount_fine), 'student_fees' => convertBaseAmountCurrencyFormat(json_decode($remain_amount_object)->student_fees));
            echo json_encode($array);
        }
    }



    public function getStudentTransportFeetypeBalance($trans_fee_id)
    {
        $data = array();

        $result          = $this->studentfeemaster_model->studentTransportDeposit($trans_fee_id);
        $amount_balance  = 0;
        $amount          = 0;
        $amount_fine     = 0;
        $amount_discount = 0;
        $fine_amount     = 0;
        $fee_fine_amount = 0;

        $due_amt = $result->fees;
        if (strtotime($result->due_date) < strtotime(date('Y-m-d'))) {
            $fee_fine_amount = is_null($result->fine_percentage) ? $result->fine_amount : percentageAmount($result->fees, $result->fine_percentage);
        }

        $amount_detail = json_decode($result->amount_detail);
        if (is_object($amount_detail)) {

            foreach ($amount_detail as $amount_detail_key => $amount_detail_value) {
                $amount          = $amount + $amount_detail_value->amount;
                $amount_discount = $amount_discount + $amount_detail_value->amount_discount;
                $amount_fine     = $amount_fine + $amount_detail_value->amount_fine;
            }
        }

        $amount_balance = $due_amt - ($amount + $amount_discount);
        $fine_amount    = abs($amount_fine - $fee_fine_amount);
        $array          = array('status' => 'success', 'error' => '', 'student_fees' => $due_amt, 'balance' => $amount_balance, 'fine_amount' => $fine_amount);
        return json_encode($array);
    }

    public function getStudentHostelFeetypeBalance($hostel_fee_id)
    {
        $data = array();

        $result = $this->studentfeemaster_model->studentHostelDeposit($hostel_fee_id);
        $amount_balance  = 0;
        $amount          = 0;
        $amount_fine     = 0;
        $amount_discount = 0;
        $fine_amount     = 0;
        $fee_fine_amount = 0;

        $due_amt = $result->fees;
        if (strtotime($result->due_date) < strtotime(date('Y-m-d'))) {
            $fee_fine_amount = is_null($result->fine_percentage) ? $result->fine_amount : percentageAmount($result->fees, $result->fine_percentage);
        }

        $amount_detail = json_decode($result->amount_detail);
        if (is_object($amount_detail)) {
            foreach ($amount_detail as $amount_detail_key => $amount_detail_value) {
                $amount          = $amount + $amount_detail_value->amount;
                $amount_discount = $amount_discount + $amount_detail_value->amount_discount;
                $amount_fine     = $amount_fine + $amount_detail_value->amount_fine;
            }
        }

        $amount_balance = $due_amt - ($amount + $amount_discount);
        $fine_amount    = abs($amount_fine - $fee_fine_amount);
        $array          = array('status' => 'success', 'error' => '', 'student_fees' => $due_amt, 'balance' => $amount_balance, 'fine_amount' => $fine_amount);
        return json_encode($array);
    }

    public function getStuFeetypeBalance($fee_groups_feetype_id, $student_fees_master_id)
    {
        $data                           = array();
        $data['fee_groups_feetype_id']  = $fee_groups_feetype_id;
        $data['student_fees_master_id'] = $student_fees_master_id;
        $result                         = $this->studentfeemaster_model->studentDeposit($data);

        $amount_balance  = 0;
        $amount          = 0;
        $amount_fine     = 0;
        $amount_discount = 0;
        $fine_amount     = 0;
        $fee_fine_amount = 0;
        $due_amt         = $result->amount;
        if (strtotime($result->due_date) < strtotime(date('Y-m-d'))) {
            $fee_fine_amount = $result->fine_amount;
        }

        if ($result->is_system) {
            $due_amt = $result->student_fees_master_amount;
        }

        $amount_detail = json_decode($result->amount_detail);
        if (is_object($amount_detail)) {

            foreach ($amount_detail as $amount_detail_key => $amount_detail_value) {
                $amount          = $amount + $amount_detail_value->amount;
                $amount_discount = $amount_discount + $amount_detail_value->amount_discount;
                $amount_fine     = $amount_fine + $amount_detail_value->amount_fine;
            }
        }

        $amount_balance = $due_amt - ($amount + $amount_discount);
        $fine_amount    = ($fee_fine_amount > 0 ) ? ($fee_fine_amount - $amount_fine) : 0;

        $array          = array('status' => 'success', 'error' => '', 'student_fees' => $due_amt, 'balance' => $amount_balance, 'fine_amount' => $fine_amount);
        return json_encode($array);
    }

    public function check_deposit($amount)
    {
        if (is_numeric($this->input->post('amount')) && is_numeric($this->input->post('amount_discount'))) {
            if ($this->input->post('amount') != "" && $this->input->post('amount_discount') != "") {
                if ($this->input->post('amount') < 1) {
                    $this->form_validation->set_message('check_deposit', $this->lang->line('deposit_amount_can_not_be_less_than_zero'));
                    return false;
                } else {
                    $transport_fees_id      = $this->input->post('transport_fees_id');
                    $student_fees_master_id = $this->input->post('student_fees_master_id');
                    $fee_groups_feetype_id  = $this->input->post('fee_groups_feetype_id');
                    $deposit_amount         = $this->input->post('amount') + $this->input->post('amount_discount');
                    if ($transport_fees_id != 0) {
                        $remain_amount = $this->getStudentTransportFeetypeBalance($transport_fees_id);
                    } else {
                        $remain_amount = $this->getStuFeetypeBalance($fee_groups_feetype_id, $student_fees_master_id);
                    }
                    $remain_amount = json_decode($remain_amount)->balance;
                    if (convertBaseAmountCurrencyFormat($remain_amount) < $deposit_amount) {
                        $this->form_validation->set_message('check_deposit', $this->lang->line('deposit_amount_can_not_be_greater_than_remaining'));
                        return false;
                    } else {
                        return true;
                    }
                }
                return true;
            }
        } elseif (!is_numeric($this->input->post('amount'))) {
            $this->form_validation->set_message('check_deposit', $this->lang->line('amount_field_must_contain_only_numbers'));
            return false;
        } elseif (!is_numeric($this->input->post('amount_discount'))) {
            return true;
        }

        return true;
    }

    public function getNotAppliedDiscount($student_session_id)
    {
        $discounts_array= $this->feediscount_model->getDiscountNotApplied($student_session_id);
        foreach ($discounts_array as $discount_key => $discount_value) {
            $discounts_array[$discount_key]->{"amount"}=convertBaseAmountCurrencyFormat($discount_value->amount);
        }
        return $discounts_array;
    }

    // public function addfeegrp()
    // {
    //     $staff_record = $this->staff_model->get($this->customlib->getStaffID());
    //     $this->form_validation->set_error_delimiters('', '');
    //     $this->form_validation->set_rules('row_counter[]', $this->lang->line('fees_list'), 'required|trim|xss_clean');
    //     $this->form_validation->set_rules('collected_date', $this->lang->line('date'), 'required|trim|xss_clean');

    //     if ($this->form_validation->run() == false) {
    //         $data = array(
    //             'row_counter'    => form_error('row_counter'),
    //             'collected_date' => form_error('collected_date'),
    //         );
    //         $array = array('status' => 0, 'error' => $data);
    //         echo json_encode($array);
    //     } else {
    //         $collected_array = array();
    //         $collected_arr = array();
    //         $staff_record    = $this->staff_model->get($this->customlib->getStaffID());
    //         $collected_by    = $this->customlib->getAdminSessionUserName() . "(" . $staff_record['employee_id'] . ")";

    //         $send_to            = $this->input->post('guardian_phone');
    //         $email              = $this->input->post('guardian_email');
    //         $parent_app_key     = $this->input->post('parent_app_key');
    //         $student_session_id = $this->input->post('student_session_id');
    //        $student= $this->student_model->getByStudentSession($student_session_id);
    //         $total_row          = $this->input->post('row_counter');
    //         foreach ($total_row as $total_row_key => $total_row_value) {

    //             $fee_category             = $this->input->post('fee_category_' . $total_row_value);
    //             $student_transport_fee_id = $this->input->post('trans_fee_id_' . $total_row_value);

    //             $json_array = array(
    //                 'amount'          => $this->input->post('fee_amount_' . $total_row_value),
    //                 'date'            => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('collected_date'))),
    //                 'description'     => $this->input->post('fee_gupcollected_note'),
    //                 'amount_discount' => 0,
    //                 'collected_by'    => $collected_by,
    //                 'amount_fine'     => $this->input->post('fee_groups_feetype_fine_amount_' . $total_row_value),
    //                 'payment_mode'    => $this->input->post('payment_mode_fee'),
    //                 'received_by'     => $staff_record['id'],
    //             );
    //             $collected_array[] = array(
    //                 'fee_category'             => $fee_category,
    //                 'student_transport_fee_id' => $student_transport_fee_id,
    //                 'student_fees_master_id'   => $this->input->post('student_fees_master_id_' . $total_row_value),
    //                 'fee_groups_feetype_id'    => $this->input->post('fee_groups_feetype_id_' . $total_row_value),
    //                 'amount_detail'            => $json_array,
    //             );
    //         }

    //         $deposited_fees = $this->studentfeemaster_model->fee_deposit_collections($collected_array);




    //             if ($deposited_fees && is_array($deposited_fees)) {
    //                 foreach ($deposited_fees as $deposited_fees_key => $deposited_fees_value) {
    //                     $fee_category = $deposited_fees_value['fee_category'];
    //                        $invoice[]   = array(
    //                         'invoice_id'     => $deposited_fees_value['invoice_id'],
    //                         'sub_invoice_id' => $deposited_fees_value['sub_invoice_id'],
    //                         'fee_category' => $fee_category,
    //                     );


    //                     if ($deposited_fees_value['student_transport_fee_id'] != 0 && $deposited_fees_value['fee_category'] == "transport") {

    //                         $data['student_fees_master_id']   = null;
    //                         $data['fee_groups_feetype_id']    = null;
    //                         $data['student_transport_fee_id'] = $deposited_fees_value['student_transport_fee_id'];

    //                         $mailsms_array     = $this->studenttransportfee_model->getTransportFeeMasterByStudentTransportID($deposited_fees_value['student_transport_fee_id']);
    //                         $fee_group_name[]  = $this->lang->line("transport_fees");
    //                         $type[]            = $mailsms_array->month;
    //                         $code[]            = "-";
    //                         $fine_type[]       = $mailsms_array->fine_type;
    //                         $due_date[]        = $mailsms_array->due_date;
    //                         $fine_percentage[] = $mailsms_array->fine_percentage;
    //                         $fine_amount[]     = amountFormat($mailsms_array->fine_amount);
    //                         $amount[]          = amountFormat($mailsms_array->amount);



    //                     } else {

    //                         $mailsms_array = $this->feegrouptype_model->getFeeGroupByIDAndStudentSessionID($deposited_fees_value['fee_groups_feetype_id'], $student_session_id);

    //                         $fee_group_name[]  = $mailsms_array->fee_group_name;
    //                         $type[]            = $mailsms_array->type;
    //                         $code[]            = $mailsms_array->code;
    //                         $fine_type[]       = $mailsms_array->fine_type;
    //                         $due_date[]        = $mailsms_array->due_date;
    //                         $fine_percentage[] = $mailsms_array->fine_percentage;
    //                         $fine_amount[]     = amountFormat($mailsms_array->fine_amount);

    //                         if ($mailsms_array->is_system) {
    //                             $amount[] = amountFormat($mailsms_array->balance_fee_master_amount);
    //                         } else {
    //                             $amount[] = amountFormat($mailsms_array->amount);
    //                         }

    //                     }

    //                 }
    //                 $obj_mail                     = [];
    //                 $obj_mail['student_id']  = $student['id'];
    //                 $obj_mail['student_session_id'] = $student_session_id;

    //                 $obj_mail['invoice']         = $invoice;
    //                 $obj_mail['contact_no']      = $student['guardian_phone'];
    //                 $obj_mail['email']           = $student['email'];
    //                 $obj_mail['parent_app_key']  = $student['parent_app_key'];
    //                 $obj_mail['amount']          = "(".implode(',', $amount).")";
    //                 $obj_mail['fine_type']       = "(".implode(',', $fine_type).")";
    //                 $obj_mail['due_date']        = "(".implode(',', $due_date).")";
    //                 $obj_mail['fine_percentage'] = "(".implode(',', $fine_percentage).")";
    //                 $obj_mail['fine_amount']     = "(".implode(',', $fine_amount).")";
    //                 $obj_mail['fee_group_name']  = "(".implode(',', $fee_group_name).")";
    //                 $obj_mail['type']            = "(".implode(',', $type).")";
    //                 $obj_mail['code']            = "(".implode(',', $code).")";
    //                 $obj_mail['fee_category']    = $fee_category;
    //                 $obj_mail['send_type']    = 'group';


    //                 $this->mailsmsconf->mailsms('fee_submission', $obj_mail);

    //             }


    //         $array = array('status' => 1, 'error' => '');
    //         echo json_encode($array);
    //     }
    // }

    public function addfeegrp()
    {
        $staff_record = $this->staff_model->get($this->customlib->getStaffID());
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('row_counter[]', $this->lang->line('fees_list'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('collected_date', $this->lang->line('date'), 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
            $data = array(
                'row_counter'    => form_error('row_counter'),
                'collected_date' => form_error('collected_date'),
            );
            $array = array('status' => 0, 'error' => $data);
            echo json_encode($array);
        } else {
            $collected_array = array();
            $collected_arr = array();
            $staff_record    = $this->staff_model->get($this->customlib->getStaffID());
            $collected_by    = $this->customlib->getAdminSessionUserName() . "(" . $staff_record['employee_id'] . ")";

            $send_to            = $this->input->post('guardian_phone');
            $email              = $this->input->post('guardian_email');
            $parent_app_key     = $this->input->post('parent_app_key');
            $student_session_id = $this->input->post('student_session_id');
            $student= $this->student_model->getByStudentSession($student_session_id);
            $total_row          = $this->input->post('row_counter');
            foreach ($total_row as $total_row_key => $total_row_value) {

                $fee_category             = $this->input->post('fee_category_' . $total_row_value);
                $student_transport_fee_id = $this->input->post('trans_fee_id_' . $total_row_value);
                $otherfeecat              = $this->input->post('otherfeecat_' . $total_row_value);

                if($otherfeecat == "otherfee"){
                    $json_array = array(
                        'amount'          => $this->input->post('fee_amount_' . $total_row_value),
                        'date'            => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('collected_date'))),
                        'description'     => $this->input->post('fee_gupcollected_note'),
                        'amount_discount' => 0,
                        'collected_by'    => $collected_by,
                        'amount_fine'     => $this->input->post('fee_groups_feetype_fine_amount_' . $total_row_value),
                        'payment_mode'    => $this->input->post('payment_mode_fee'),
                        'received_by'     => $staff_record['id'],
                    );
                    $collected_arr[] = array(
                        'fee_category'             => $fee_category,
                        'student_transport_fee_id' => $student_transport_fee_id,
                        'student_fees_master_id'   => $this->input->post('student_fees_master_id_' . $total_row_value),
                        'fee_groups_feetype_id'    => $this->input->post('fee_groups_feetype_id_' . $total_row_value),
                        'amount_detail'            => $json_array,
                    );
                }else{
                    $json_array = array(
                        'amount'          => $this->input->post('fee_amount_' . $total_row_value),
                        'date'            => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('collected_date'))),
                        'description'     => $this->input->post('fee_gupcollected_note'),
                        'amount_discount' => 0,
                        'collected_by'    => $collected_by,
                        'amount_fine'     => $this->input->post('fee_groups_feetype_fine_amount_' . $total_row_value),
                        'payment_mode'    => $this->input->post('payment_mode_fee'),
                        'received_by'     => $staff_record['id'],
                    );
                    $collected_array[] = array(
                        'fee_category'             => $fee_category,
                        'student_transport_fee_id' => $student_transport_fee_id,
                        'student_fees_master_id'   => $this->input->post('student_fees_master_id_' . $total_row_value),
                        'fee_groups_feetype_id'    => $this->input->post('fee_groups_feetype_id_' . $total_row_value),
                        'amount_detail'            => $json_array,
                    );

                }



            }

            if($collected_array){
                $deposited_fees = $this->studentfeemaster_model->fee_deposit_collections($collected_array);
                if ($deposited_fees && is_array($deposited_fees)) {
                    foreach ($deposited_fees as $deposited_fees_key => $deposited_fees_value) {
                        $fee_category = $deposited_fees_value['fee_category'];
                            $invoice[]   = array(
                            'invoice_id'     => $deposited_fees_value['invoice_id'],
                            'sub_invoice_id' => $deposited_fees_value['sub_invoice_id'],
                            'fee_category' => $fee_category,
                        );


                        if ($deposited_fees_value['student_transport_fee_id'] != 0 && $deposited_fees_value['fee_category'] == "transport") {

                            $data['student_fees_master_id']   = null;
                            $data['fee_groups_feetype_id']    = null;
                            $data['student_transport_fee_id'] = $deposited_fees_value['student_transport_fee_id'];

                            $mailsms_array     = $this->studenttransportfee_model->getTransportFeeMasterByStudentTransportID($deposited_fees_value['student_transport_fee_id']);
                            $fee_group_name[]  = $this->lang->line("transport_fees");
                            $type[]            = $mailsms_array->month;
                            $code[]            = "-";
                            $fine_type[]       = $mailsms_array->fine_type;
                            $due_date[]        = $mailsms_array->due_date;
                            $fine_percentage[] = $mailsms_array->fine_percentage;
                            $fine_amount[]     = amountFormat($mailsms_array->fine_amount);
                            $amount[]          = amountFormat($mailsms_array->amount);



                        } else {

                            $mailsms_array = $this->feegrouptype_model->getFeeGroupByIDAndStudentSessionID($deposited_fees_value['fee_groups_feetype_id'], $student_session_id);

                            $fee_group_name[]  = $mailsms_array->fee_group_name;
                            $type[]            = $mailsms_array->type;
                            $code[]            = $mailsms_array->code;
                            $fine_type[]       = $mailsms_array->fine_type;
                            $due_date[]        = $mailsms_array->due_date;
                            $fine_percentage[] = $mailsms_array->fine_percentage;
                            $fine_amount[]     = amountFormat($mailsms_array->fine_amount);

                            if ($mailsms_array->is_system) {
                                $amount[] = amountFormat($mailsms_array->balance_fee_master_amount);
                            } else {
                                $amount[] = amountFormat($mailsms_array->amount);
                            }

                        }

                    }
                    $obj_mail                     = [];
                    $obj_mail['student_id']  = $student['id'];
                    $obj_mail['student_session_id'] = $student_session_id;

                    $obj_mail['invoice']         = $invoice;
                    $obj_mail['contact_no']      = $student['guardian_phone'];
                    $obj_mail['email']           = $student['email'];
                    $obj_mail['parent_app_key']  = $student['parent_app_key'];
                    $obj_mail['amount']          = "(".implode(',', $amount).")";
                    $obj_mail['fine_type']       = "(".implode(',', $fine_type).")";
                    $obj_mail['due_date']        = "(".implode(',', $due_date).")";
                    $obj_mail['fine_percentage'] = "(".implode(',', $fine_percentage).")";
                    $obj_mail['fine_amount']     = "(".implode(',', $fine_amount).")";
                    $obj_mail['fee_group_name']  = "(".implode(',', $fee_group_name).")";
                    $obj_mail['type']            = "(".implode(',', $type).")";
                    $obj_mail['code']            = "(".implode(',', $code).")";
                    $obj_mail['fee_category']    = $fee_category;
                    $obj_mail['send_type']    = 'group';


                    $this->mailsmsconf->mailsms('fee_submission', $obj_mail);

                }
            }

            if($collected_arr){
                $deposited_fees = $this->studentfeemasteradding_model->fee_deposit_collections($collected_arr);
                // if ($deposited_fees && is_array($deposited_fees)) {
                //     foreach ($deposited_fees as $deposited_fees_key => $deposited_fees_value) {
                //         $fee_category = $deposited_fees_value['fee_category'];
                //             $invoice[]   = array(
                //             'invoice_id'     => $deposited_fees_value['invoice_id'],
                //             'sub_invoice_id' => $deposited_fees_value['sub_invoice_id'],
                //             'fee_category' => $fee_category,
                //         );


                //         if ($deposited_fees_value['student_transport_fee_id'] != 0 && $deposited_fees_value['fee_category'] == "transport") {

                //             $data['student_fees_master_id']   = null;
                //             $data['fee_groups_feetype_id']    = null;
                //             $data['student_transport_fee_id'] = $deposited_fees_value['student_transport_fee_id'];

                //             $mailsms_array     = $this->studenttransportfee_model->getTransportFeeMasterByStudentTransportID($deposited_fees_value['student_transport_fee_id']);
                //             $fee_group_name[]  = $this->lang->line("transport_fees");
                //             $type[]            = $mailsms_array->month;
                //             $code[]            = "-";
                //             $fine_type[]       = $mailsms_array->fine_type;
                //             $due_date[]        = $mailsms_array->due_date;
                //             $fine_percentage[] = $mailsms_array->fine_percentage;
                //             $fine_amount[]     = amountFormat($mailsms_array->fine_amount);
                //             $amount[]          = amountFormat($mailsms_array->amount);



                //         } else {

                //             $mailsms_array = $this->feegrouptypeadding_model->getFeeGroupByIDAndStudentSessionID($deposited_fees_value['fee_groups_feetype_id'], $student_session_id);

                //             $fee_group_name[]  = $mailsms_array->fee_group_name;
                //             $type[]            = $mailsms_array->type;
                //             $code[]            = $mailsms_array->code;
                //             $fine_type[]       = $mailsms_array->fine_type;
                //             $due_date[]        = $mailsms_array->due_date;
                //             $fine_percentage[] = $mailsms_array->fine_percentage;
                //             $fine_amount[]     = amountFormat($mailsms_array->fine_amount);

                //             if ($mailsms_array->is_system) {
                //                 $amount[] = amountFormat($mailsms_array->balance_fee_master_amount);
                //             } else {
                //                 $amount[] = amountFormat($mailsms_array->amount);
                //             }

                //         }

                //     }
                //     $obj_mail                     = [];
                //     $obj_mail['student_id']  = $student['id'];
                //     $obj_mail['student_session_id'] = $student_session_id;

                //     $obj_mail['invoice']         = $invoice;
                //     $obj_mail['contact_no']      = $student['guardian_phone'];
                //     $obj_mail['email']           = $student['email'];
                //     $obj_mail['parent_app_key']  = $student['parent_app_key'];
                //     $obj_mail['amount']          = "(".implode(',', $amount).")";
                //     $obj_mail['fine_type']       = "(".implode(',', $fine_type).")";
                //     $obj_mail['due_date']        = "(".implode(',', $due_date).")";
                //     $obj_mail['fine_percentage'] = "(".implode(',', $fine_percentage).")";
                //     $obj_mail['fine_amount']     = "(".implode(',', $fine_amount).")";
                //     $obj_mail['fee_group_name']  = "(".implode(',', $fee_group_name).")";
                //     $obj_mail['type']            = "(".implode(',', $type).")";
                //     $obj_mail['code']            = "(".implode(',', $code).")";
                //     $obj_mail['fee_category']    = $fee_category;
                //     $obj_mail['send_type']    = 'group';


                //     $this->mailsmsconf->mailsms('fee_submission', $obj_mail);

                // }
            }

            $array = array('status' => 1, 'error' => '');
            echo json_encode($array);
        }
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




    // i am changing code

    public function discountaddfee($id,$amount,$dicountid)
    {

        if (!$this->rbac->hasPrivilege('collect_fees', 'can_view')) {
            access_denied();
        }

        $data['sch_setting']   = $this->sch_setting_detail;
        $data['title']         = 'Student Detail';
        $student               = $this->student_model->getByStudentSessionn($id,$dicountid);
        $route_pickup_point_id = $student['route_pickup_point_id'];
        $student_session_id    = $student['student_session_id'];
        $transport_fees=[];

        $module=$this->module_model->getPermissionByModulename('transport');
        if($module['is_active']){

        $transport_fees        = $this->studentfeemaster_model->getStudentTransportFees($student_session_id, $route_pickup_point_id);
        }

        $data['discountamt']=$amount;
        $data['discountid']=$dicountid;
        $data['stdid']=$student['id'];


        $data['student']       = $student;

        $student_due_fee       = $this->studentfeemaster_model->getStudentFees($id);
        $student_discount_fee  = $this->feediscount_model->getStudentFeesDiscount($id);

        $data['transport_fees']         = $transport_fees;
        $data['student_discount_fee']   = $student_discount_fee;
        $data['student_due_fee']        = $student_due_fee;
        $category                       = $this->category_model->get();
        $data['categorylist']           = $category;
        $class_section                  = $this->student_model->getClassSection($student["class_id"]);
        $data["class_section"]          = $class_section;
        $session                        = $this->setting_model->getCurrentSession();
        $studentlistbysection           = $this->student_model->getStudentClassSection($student["class_id"], $session);
        $data["studentlistbysection"]   = $studentlistbysection;
        $student_processing_fee         = $this->studentfeemaster_model->getStudentProcessingFees($id);
        $data['student_processing_fee'] = false;

        foreach ($student_processing_fee as $key => $processing_value) {
            if (!empty($processing_value->fees)) {
                $data['student_processing_fee'] = true;
            }
        }

        $this->load->view('layout/header', $data);
        $this->load->view('admin/feediscount/studentaddfeediscount', $data);
        $this->load->view('layout/footer', $data);
    }
    }





    public function addstudentfeee()
    {
        $this->form_validation->set_rules('student_fees_master_id', $this->lang->line('fee_master'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('fee_groups_feetype_id', $this->lang->line('student'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'required|trim|xss_clean|numeric|callback_check_deposit');
        $this->form_validation->set_rules('amount_discount', $this->lang->line('discount'), 'required|trim|numeric|xss_clean');
        $this->form_validation->set_rules('amount_fine', $this->lang->line('fine'), 'required|trim|numeric|xss_clean');
        $this->form_validation->set_rules('payment_mode', $this->lang->line('payment_mode'), 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
            $data = array(
                'amount'                 => form_error('amount'),
                'student_fees_master_id' => form_error('student_fees_master_id'),
                'fee_groups_feetype_id'  => form_error('fee_groups_feetype_id'),
                'amount_discount'        => form_error('amount_discount'),
                'amount_fine'            => form_error('amount_fine'),
                'payment_mode'           => form_error('payment_mode'),
                'date'           => form_error('date'),
            );
            $array = array('status' => 'fail', 'error' => $data);
            echo json_encode($array);
        } else {

            $staff_record = $this->staff_model->get($this->customlib->getStaffID());

            $collected_by             = $this->customlib->getAdminSessionUserName() . "(" . $staff_record['employee_id'] . ")";
            $student_fees_discount_id = $this->input->post('student_fees_discount_id');

            $studentid = $this->input->post('data');
            $certificate_id = $this->input->post('certificate_id');

            $json_array               = array(
                'amount'          => convertCurrencyFormatToBaseAmount($this->input->post('amount')),
                'amount_discount' => convertCurrencyFormatToBaseAmount($this->input->post('amount_discount')),
                'amount_fine'     => convertCurrencyFormatToBaseAmount($this->input->post('amount_fine')),
                'date'            => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
                'description'     => $this->input->post('description'),
                'collected_by'    => $collected_by,
                'payment_mode'    => $this->input->post('payment_mode'),
                'received_by'     => $staff_record['id'],
            );

            $student_fees_master_id = $this->input->post('student_fees_master_id');
            $fee_groups_feetype_id  = $this->input->post('fee_groups_feetype_id');
            $transport_fees_id      = $this->input->post('transport_fees_id');
            $fee_category           = $this->input->post('fee_category');

            $data = array(
                'fee_category'           => $fee_category,
                'student_fees_master_id' => $this->input->post('student_fees_master_id'),
                'fee_groups_feetype_id'  => $this->input->post('fee_groups_feetype_id'),
                'amount_detail'          => $json_array,
            );

            if ($transport_fees_id != 0 && $fee_category == "transport") {
                $mailsms_array                    = new stdClass();
                $data['student_fees_master_id']   = null;
                $data['fee_groups_feetype_id']    = null;
                $data['student_transport_fee_id'] = $transport_fees_id;

                $mailsms_array                 = $this->studenttransportfee_model->getTransportFeeMasterByStudentTransportID($transport_fees_id);
                $mailsms_array->fee_group_name = $this->lang->line("transport_fees");
                $mailsms_array->type           = $mailsms_array->month;
                $mailsms_array->code           = "";
            } else {

                $mailsms_array = $this->feegrouptype_model->getFeeGroupByIDAndStudentSessionID($this->input->post('fee_groups_feetype_id'), $this->input->post('student_session_id'));

                if($mailsms_array->is_system){
                     $mailsms_array->amount=$mailsms_array->balance_fee_master_amount;
                }

            }

            $action             = $this->input->post('action');
            $send_to            = $this->input->post('guardian_phone');
            $email              = $this->input->post('guardian_email');
            $parent_app_key     = $this->input->post('parent_app_key');
            $student_session_id = $this->input->post('student_session_id');
            $inserted_id        = $this->studentfeemaster_model->fee_deposit($data, $send_to, $student_fees_discount_id);
            $this->feediscount_model->updateapprovalstatus($certificate_id, $studentid, 1);

            $print_record = array();
            if ($action == "print") {
                $receipt_data           = json_decode($inserted_id);
                $data['sch_setting']    = $this->sch_setting_detail;

                $student                = $this->studentsession_model->searchStudentsBySession($student_session_id);
                $data['student']        = $student;
                $data['sub_invoice_id'] = $receipt_data->sub_invoice_id;

                $setting_result         = $this->setting_model->get();
                $data['settinglist']    = $setting_result;

        if ($transport_fees_id != 0 && $fee_category == "transport") {

            $fee_record = $this->studentfeemaster_model->getTransportFeeByInvoice($receipt_data->invoice_id, $receipt_data->sub_invoice_id);
             $data['feeList']        = $fee_record;
                $print_record = $this->load->view('print/printTransportFeesByName', $data, true);

        } else {

             $fee_record             = $this->studentfeemaster_model->getFeeByInvoice($receipt_data->invoice_id, $receipt_data->sub_invoice_id);
               $data['feeList']        = $fee_record;
                $print_record = $this->load->view('print/printFeesByName', $data, true);
        }
            }

            $mailsms_array->invoice            = $inserted_id;
            $mailsms_array->student_session_id = $student_session_id;
            $mailsms_array->contact_no         = $send_to;
            $mailsms_array->email              = $email;
            $mailsms_array->parent_app_key     = $parent_app_key;
            $mailsms_array->fee_category       = $fee_category;

            $this->mailsmsconf->mailsms('fee_submission', $mailsms_array);

            $array = array('status' => 'success', 'error' => '', 'print' => $print_record);
            echo json_encode($array);
        }
    }





    public function printaddingFeesByName()
    {
        $data                   = array('payment' => "0");
        $record                 = $this->input->post('data');
        $fee_category           = $this->input->post('fee_category');
        $invoice_id             = $this->input->post('main_invoice');
        $sub_invoice_id         = $this->input->post('sub_invoice');
        $student_session_id     = $this->input->post('student_session_id');
        $setting_result         = $this->setting_model->get();
        $data['settinglist']    = $setting_result;
        $student                = $this->studentsession_model->searchStudentsBySession($student_session_id);
        $data['student']        = $student;
        $data['sub_invoice_id'] = $sub_invoice_id;
        $data['sch_setting']    = $this->sch_setting_detail;

        $data['superadmin_rest'] = $this->customlib->superadmin_visible();

        if ($fee_category == "transport") {
            $fee_record      = $this->studentfeemasteradding_model->getTransportFeeByInvoice($invoice_id, $sub_invoice_id);
            $data['feeList'] = $fee_record;
            $page            = $this->load->view('print/printTransportFeesByName', $data, true);
        } else {
            $fee_record      = $this->studentfeemasteradding_model->getFeeByInvoice($invoice_id, $sub_invoice_id);
            $data['feeList'] = $fee_record;
            $page = $this->load->view('print/printFeesByName', $data, true);
        }

        echo json_encode(array('status' => 1, 'page' => $page));

    }


    public function getStuFeetypeAdditionalBalance($fee_groups_feetype_id, $student_fees_master_id,$student_session_id)
    {
        $data                           = array();
        $data['fee_groups_feetype_id']  = $fee_groups_feetype_id;
        $data['student_fees_master_id'] = $student_fees_master_id;
        $data['student_session_id'] = $student_session_id;
        $result                         = $this->studentfeemasteradding_model->studentDeposit($data);

        $amount_balance  = 0;
        $amount          = 0;
        $amount_fine     = 0;
        $amount_discount = 0;
        $fine_amount     = 0;
        $fee_fine_amount = 0;
        $due_amt         = $result->amount;
        if (strtotime($result->due_date) < strtotime(date('Y-m-d'))) {
            $fee_fine_amount = $result->fine_amount;
        }

        if ($result->is_system) {
            $due_amt = $result->student_fees_master_amount;
        }

        $amount_detail = json_decode($result->amount_detail);
        if (is_object($amount_detail)) {

            foreach ($amount_detail as $amount_detail_key => $amount_detail_value) {
                $amount          = $amount + $amount_detail_value->amount;
                $amount_discount = $amount_discount + $amount_detail_value->amount_discount;
                $amount_fine     = $amount_fine + $amount_detail_value->amount_fine;
            }
        }

        $amount_balance = $due_amt - ($amount + $amount_discount);
        $fine_amount    = ($fee_fine_amount > 0 ) ? ($fee_fine_amount - $amount_fine) : 0;

        $array          = array('status' => 'success', 'error' => '', 'student_fees' => $due_amt, 'balance' => $amount_balance, 'fine_amount' => $fine_amount);
        return json_encode($array);
    }



    public function geBalanceFeeadding()
    {
        $this->form_validation->set_rules('fee_groups_feetype_id', $this->lang->line('fee_groups_feetype_id'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('student_fees_master_id', $this->lang->line('student_fees_master_id'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('student_session_id', $this->lang->line('student_session_id'), 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
            $data = array(
                'fee_groups_feetype_id'  => form_error('fee_groups_feetype_id'),
                'student_fees_master_id' => form_error('student_fees_master_id'),
                'student_session_id'     => form_error('student_session_id'),
            );
            $array = array('status' => 'fail', 'error' => $data);
            echo json_encode($array);
        } else {
            $data                 = array();
            $student_session_id   = $this->input->post('student_session_id');
            $discount_not_applied = $this->getNotAppliedDiscount($student_session_id);

            $fee_category = $this->input->post('fee_category');
            if ($fee_category == "transport") {
                $trans_fee_id         = $this->input->post('trans_fee_id');
                $remain_amount_object = $this->getStudentTransportFeetypeBalance($trans_fee_id);
                $remain_amount        = (float) json_decode($remain_amount_object)->balance;
                $remain_amount_fine   = json_decode($remain_amount_object)->fine_amount;
            } else {
                $fee_groups_feetype_id  = $this->input->post('fee_groups_feetype_id');
                $student_fees_master_id = $this->input->post('student_fees_master_id');
                $remain_amount_object   = $this->getStuFeetypeAdditionalBalance($fee_groups_feetype_id, $student_fees_master_id,$student_session_id);
                $remain_amount          = json_decode($remain_amount_object)->balance;
                $remain_amount_fine     = json_decode($remain_amount_object)->fine_amount;
            }

            $remain_amount = number_format($remain_amount, 2, ".", "");

            $array = array('status' => 'success', 'error' => '', 'balance' => convertBaseAmountCurrencyFormat($remain_amount), 'discount_not_applied' => $discount_not_applied, 'remain_amount_fine' => convertBaseAmountCurrencyFormat($remain_amount_fine), 'student_fees' => convertBaseAmountCurrencyFormat(json_decode($remain_amount_object)->student_fees));
            echo json_encode($array);
        }
    }


    public function getStuFeetypeAddingBalance($fee_groups_feetype_id, $student_fees_master_id,$student_session_id)
    {
        $data                           = array();
        $data['fee_groups_feetype_id']  = $fee_groups_feetype_id;
        $data['student_fees_master_id'] = $student_fees_master_id;
        $data['student_session_id'] = $student_session_id;
        $result                         = $this->studentfeemasteradding_model->studentDeposit($data);

        if (!$result) {
            return json_encode(array('status' => 'error', 'message' => 'Invalid result returned'));
        }

        // Check if amount is zero
        if ($result->amount == 0) {
            return json_encode(array('status' => 'error', 'message' => 'Amount is zero'));
        }

        $amount_balance  = 0;
        $amount          = 0;
        $amount_fine     = 0;
        $amount_discount = 0;
        $fine_amount     = 0;
        $fee_fine_amount = 0;
        $due_amt         = $result->amount;
        if (strtotime($result->due_date) < strtotime(date('Y-m-d'))) {
            $fee_fine_amount = $result->fine_amount;
        }

        if ($result->is_system) {
            $due_amt = $result->student_fees_master_amount;
        }

        $amount_detail = json_decode($result->amount_detail);
        if (is_object($amount_detail)) {

            foreach ($amount_detail as $amount_detail_key => $amount_detail_value) {
                $amount          = $amount + $amount_detail_value->amount;
                $amount_discount = $amount_discount + $amount_detail_value->amount_discount;
                $amount_fine     = $amount_fine + $amount_detail_value->amount_fine;
            }
        }

        $amount_balance = $due_amt - ($amount + $amount_discount);
        $fine_amount    = ($fee_fine_amount > 0 ) ? ($fee_fine_amount - $amount_fine) : 0;

        $array          = array('status' => 'success', 'error' => '', 'student_fees' => $due_amt, 'balance' => $amount_balance, 'fine_amount' => $fine_amount);
        return json_encode($array);
    }

    public function check_adding_deposit($amount)
    {
        if (is_numeric($this->input->post('adding_amount')) && is_numeric($this->input->post('adding_amount_discount'))) {
            if ($this->input->post('adding_amount') != "" && $this->input->post('adding_amount_discount') != "") {
                if ($this->input->post('adding_amount') < 0) {
                    $this->form_validation->set_message('check_adding_deposit', $this->lang->line('deposit_amount_can_not_be_less_than_zero'));
                    return false;
                } else {
                    $transport_fees_id      = $this->input->post('adding_transport_fees_id');
                    $student_fees_master_id = $this->input->post('adding_student_fees_master_id');
                    $fee_groups_feetype_id  = $this->input->post('adding_fee_groups_feetype_id');
                    $student_session_id = $this->input->post('adding_student_session_id');
                    $deposit_amount         = $this->input->post('adding_amount') + $this->input->post('adding_amount_discount');
                    if ($transport_fees_id != 0) {
                        $remain_amount = $this->getStudentTransportFeetypeBalance($transport_fees_id);
                    } else {
                        $remain_amount = $this->getStuFeetypeAddingBalance($fee_groups_feetype_id, $student_fees_master_id,$student_session_id);
                    }
                    $remain_amount = json_decode($remain_amount)->balance;

                    if (convertBaseAmountCurrencyFormat($remain_amount) < $deposit_amount) {
                        $this->form_validation->set_message('check_adding_deposit', $this->lang->line('deposit_amount_can_not_be_greater_than_remaining'));
                        return false;
                    } else {
                        return true;
                    }
                }
                return true;
            }
        } elseif (!is_numeric($this->input->post('adding_amount'))) {
            $this->form_validation->set_message('check_adding_deposit', $this->lang->line('amount_field_must_contain_only_numbers'));
            return false;
        } elseif (!is_numeric($this->input->post('adding_amount_discount'))) {
            return true;
        }

        return true;
    }

    public function deleteaddingFee()
    {
        if (!$this->rbac->hasPrivilege('collect_fees', 'can_delete')) {
            access_denied();
        }
        $invoice_id  = $this->input->post('main_invoice');
        $sub_invoice = $this->input->post('sub_invoice');
        if (!empty($invoice_id)) {
            $this->studentfeemasteradding_model->remove($invoice_id, $sub_invoice);
            $this->addaccount_model->transcationremove($invoice_id . '/' . $sub_invoice,'otherfees');
        }
        $array = array('status' => 'success', 'result' => 'success');
        echo json_encode($array);
    }

    public function addstudentadditionalfee()
    {


        $this->form_validation->set_rules('adding_student_fees_master_id', $this->lang->line('fee_master'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('adding_date', $this->lang->line('date'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('adding_fee_groups_feetype_id', $this->lang->line('student'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('adding_amount', $this->lang->line('amount'), 'required|trim|xss_clean|numeric|callback_check_adding_deposit');
        $this->form_validation->set_rules('adding_amount_discount', $this->lang->line('discount'), 'required|trim|numeric|xss_clean');
        $this->form_validation->set_rules('adding_amount_fine', $this->lang->line('fine'), 'required|trim|numeric|xss_clean');
        $this->form_validation->set_rules('adding_payment_mode', $this->lang->line('payment_mode'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('addingaccountname', $this->lang->line('accountname'), 'required|trim|xss_clean');


        if ($this->form_validation->run() == false) {
            $data = array(
                'adding_amount'                 => form_error('adding_amount'),
                'adding_student_fees_master_id' => form_error('adding_student_fees_master_id'),
                'adding_fee_groups_feetype_id'  => form_error('adding_fee_groups_feetype_id'),
                'adding_amount_discount'        => form_error('adding_amount_discount'),
                'adding_amount_fine'            => form_error('adding_amount_fine'),
                'adding_payment_mode'           => form_error('adding_payment_mode'),
                'adding_date'                  => form_error('adding_date'),
                'addingaccountname'            =>  form_error('addingaccountname'),
            );
            $array = array('status' => 'fail', 'error' => $data);
            echo json_encode($array);
        } else {

            $staff_record = $this->staff_model->get($this->customlib->getStaffID());

            $collected_by             = $this->customlib->getAdminSessionUserName() . "(" . $staff_record['employee_id'] . ")";
            $student_fees_discount_id = $this->input->post('adding_student_fees_discount_id');
            $json_array               = array(
                'amount'          => convertCurrencyFormatToBaseAmount($this->input->post('adding_amount')),
                'amount_discount' => convertCurrencyFormatToBaseAmount($this->input->post('adding_amount_discount')),
                'amount_fine'     => convertCurrencyFormatToBaseAmount($this->input->post('adding_amount_fine')),
                'date'            => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('adding_date'))),
                'description'     => $this->input->post('adding_description'),
                'collected_by'    => $collected_by,
                'payment_mode'    => $this->input->post('adding_payment_mode'),
                'received_by'     => $staff_record['id'],
            );

            $student_fees_master_id = $this->input->post('adding_student_fees_master_id');
            $fee_groups_feetype_id  = $this->input->post('adding_fee_groups_feetype_id');
            $transport_fees_id      = $this->input->post('adding_transport_fees_id');
            $fee_category           = $this->input->post('adding_fee_category');

            $data = array(
                'fee_category'           => $fee_category,
                'student_fees_master_id' => $this->input->post('adding_student_fees_master_id'),
                'fee_groups_feetype_id'  => $this->input->post('adding_fee_groups_feetype_id'),
                'amount_detail'          => $json_array,
            );

            // if ($transport_fees_id != 0 && $fee_category == "transport") {
            //     $mailsms_array                    = new stdClass();
            //     $data['student_fees_master_id']   = null;
            //     $data['fee_groups_feetype_id']    = null;
            //     $data['student_transport_fee_id'] = $transport_fees_id;

            //     $mailsms_array                 = $this->studenttransportfee_model->getTransportFeeMasterByStudentTransportID($transport_fees_id);
            //     $mailsms_array->fee_group_name = $this->lang->line("transport_fees");
            //     $mailsms_array->type           = $mailsms_array->month;
            //     $mailsms_array->code           = "";
            // } else {

            //     $mailsms_array = $this->feegrouptypeadding_model->getFeeGroupByIDAndStudentSessionID($this->input->post('fee_groups_feetype_id'), $this->input->post('student_session_id'));

            //     if($mailsms_array->is_system){
            //          $mailsms_array->amount=$mailsms_array->balance_fee_master_amount;
            //     }

            // }

            $action             = $this->input->post('adding_action');
            $send_to            = $this->input->post('adding_guardian_phone');
            $email              = $this->input->post('adding_guardian_email');
            $parent_app_key     = $this->input->post('adding_parent_app_key');
            $student_session_id = $this->input->post('adding_student_session_id');
            $inserted_id        = $this->studentfeemasteradding_model->fee_deposit($data, $send_to, $student_fees_discount_id);

            $receipt_data1           = json_decode($inserted_id);

            $accounttranscationarray = array(
                'receiptid'=> $receipt_data1->invoice_id . '/' . $receipt_data1->sub_invoice_id,
                'accountid'=>$this->input->post('addingaccountname'),
                'amount' => convertCurrencyFormatToBaseAmount($this->input->post('adding_amount')),
                'date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('adding_date'))),
                'type' => 'otherfees',
                'description'     => $this->input->post('adding_description'),
                'status'   => 'credit',
            );


            $accounttranscation = $this->addaccount_model->addingtranscation($accounttranscationarray);



            $print_record = array();
            if ($action == "print") {
                $receipt_data           = json_decode($inserted_id);
                $data['sch_setting']    = $this->sch_setting_detail;

                $student                = $this->studentsession_model->searchStudentsBySession($student_session_id);
                $data['student']        = $student;
                $data['sub_invoice_id'] = $receipt_data->sub_invoice_id;

                $setting_result         = $this->setting_model->get();
                $data['settinglist']    = $setting_result;

                if ($transport_fees_id != 0 && $fee_category == "transport") {

                    $fee_record = $this->studentfeemaster_model->getTransportFeeByInvoice($receipt_data->invoice_id, $receipt_data->sub_invoice_id);
                    $data['feeList']        = $fee_record;
                        $print_record = $this->load->view('print/printTransportFeesByName', $data, true);

                } else {

                    $fee_record             = $this->studentfeemasteradding_model->getFeeByInvoice($receipt_data->invoice_id, $receipt_data->sub_invoice_id);
                    $data['feeList']        = $fee_record;
                        $print_record = $this->load->view('print/printFeesByName', $data, true);
                }
            }



            // $mailsms_array->invoice            = $inserted_id;
            // $mailsms_array->student_session_id = $student_session_id;
            // $mailsms_array->contact_no         = $send_to;
            // $mailsms_array->email              = $email;
            // $mailsms_array->parent_app_key     = $parent_app_key;
            // $mailsms_array->fee_category       = $fee_category;

            // $this->mailsmsconf->mailsms('fee_submission', $mailsms_array);

            $array = array('status' => 'success', 'error' => '', 'print' => $print_record);
            // $array = array('status' => 'success', 'error' => '');
            echo json_encode($array);
        }
    }


    public function printaddingFeesByGroup()
    {
        $fee_category        = $this->input->post('fee_category');
        $trans_fee_id        = $this->input->post('trans_fee_id');
        $setting_result      = $this->setting_model->get();
        $data['settinglist'] = $setting_result;
        $data['sch_setting'] = $this->sch_setting_detail;

        if ($fee_category == "transport") {
            $data['feeList'] = $this->studentfeemasteradding_model->getTransportFeeByID($trans_fee_id);
            $page = $this->load->view('print/printTransportFeesByGroup', $data, true);

        } else {

            $fee_groups_feetype_id = $this->input->post('fee_groups_feetype_id');
            $fee_master_id         = $this->input->post('fee_master_id');
            // $student_session_id    = $this->input->post('student_session_id');
            $fee_session_group_id  = $this->input->post('fee_session_group_id');
            $data['feeList']       = $this->studentfeemasteradding_model->getDueFeeByFeeSessionGroupFeetype($fee_session_group_id, $fee_master_id, $fee_groups_feetype_id,$student_session_id);
            $page                  = $this->load->view('print/printFeesByGroup', $data, true);
        }

        echo json_encode(array('status' => 1, 'page' => $page));

    }



    // public function addstudentadditionalfee()
    // {
    //     $this->form_validation->set_rules('student_fees_master_id', $this->lang->line('fee_master'), 'required|trim|xss_clean');
    //     $this->form_validation->set_rules('date', $this->lang->line('date'), 'required|trim|xss_clean');
    //     $this->form_validation->set_rules('fee_groups_feetype_id', $this->lang->line('student'), 'required|trim|xss_clean');
    //     $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'required|trim|xss_clean|numeric|callback_check_deposit');
    //     $this->form_validation->set_rules('amount_discount', $this->lang->line('discount'), 'required|trim|numeric|xss_clean');
    //     $this->form_validation->set_rules('amount_fine', $this->lang->line('fine'), 'required|trim|numeric|xss_clean');
    //     $this->form_validation->set_rules('payment_mode', $this->lang->line('payment_mode'), 'required|trim|xss_clean');

    //     if ($this->form_validation->run() == false) {
    //         $data = array(
    //             'amount'                 => form_error('amount'),
    //             'student_fees_master_id' => form_error('student_fees_master_id'),
    //             'fee_groups_feetype_id'  => form_error('fee_groups_feetype_id'),
    //             'amount_discount'        => form_error('amount_discount'),
    //             'amount_fine'            => form_error('amount_fine'),
    //             'payment_mode'           => form_error('payment_mode'),
    //             'date'                   => form_error('date'),
    //         );
    //         $array = array('status' => 'fail', 'error' => $data);
    //         echo json_encode($array);
    //     } else {
    //         // Process data from the second modal here

    //         // Get staff record
    //         $staff_record = $this->staff_model->get($this->customlib->getStaffID());

    //         // Prepare JSON data
    //         $json_array = array(
    //             'amount'          => convertCurrencyFormatToBaseAmount($this->input->post('amount')),
    //             'amount_discount' => convertCurrencyFormatToBaseAmount($this->input->post('amount_discount')),
    //             'amount_fine'     => convertCurrencyFormatToBaseAmount($this->input->post('amount_fine')),
    //             'date'            => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
    //             'description'     => $this->input->post('description'),
    //             'collected_by'    => $this->customlib->getAdminSessionUserName() . "(" . $staff_record['employee_id'] . ")",
    //             'payment_mode'    => $this->input->post('payment_mode'),
    //             'received_by'     => $staff_record['id'],
    //         );

    //         // Get other necessary data from the form
    //         $student_fees_master_id = $this->input->post('student_fees_master_id');
    //         $fee_groups_feetype_id  = $this->input->post('fee_groups_feetype_id');
    //         $transport_fees_id      = $this->input->post('transport_fees_id');
    //         $fee_category           = $this->input->post('fee_category');

    //         // Prepare data array
    //         $data = array(
    //             'fee_category'           => $fee_category,
    //             'student_fees_master_id' => $student_fees_master_id,
    //             'fee_groups_feetype_id'  => $fee_groups_feetype_id,
    //             'amount_detail'          => $json_array,
    //         );

    //         // Additional handling based on fee category or any other conditions can be added here

    //         // Process the data using your model method
    //         $inserted_id = $this->studentfeemasteradding_model->fee_deposit($data, $send_to, $student_fees_discount_id);

    //         // Prepare data for response
    //         $array = array('status' => 'success', 'error' => '');
    //         echo json_encode($array);
    //     }
    // }



    public function addstudentdiscountfee()
    {
        $this->form_validation->set_rules('student_fees_master_id', $this->lang->line('fee_master'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('fee_groups_feetype_id', $this->lang->line('student'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'required|trim|xss_clean|numeric');
        $this->form_validation->set_rules('amount_discount', $this->lang->line('discount'), 'required|trim|numeric|xss_clean');
        $this->form_validation->set_rules('amount_fine', $this->lang->line('fine'), 'required|trim|numeric|xss_clean');
        $this->form_validation->set_rules('payment_mode', $this->lang->line('payment_mode'), 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
            $data = array(
                'amount'                 => form_error('amount'),
                'student_fees_master_id' => form_error('student_fees_master_id'),
                'fee_groups_feetype_id'  => form_error('fee_groups_feetype_id'),
                'amount_discount'        => form_error('amount_discount'),
                'amount_fine'            => form_error('amount_fine'),
                'payment_mode'           => form_error('payment_mode'),
                'date'           => form_error('date'),
            );
            $array = array('status' => 'fail', 'error' => $data);
            echo json_encode($array);
        } else {

            $staff_record = $this->staff_model->get($this->customlib->getStaffID());

            $collected_by             = $this->customlib->getAdminSessionUserName() . "(" . $staff_record['employee_id'] . ")";
            $student_fees_discount_id = $this->input->post('student_fees_discount_id');
            $json_array               = array(
                'amount'          => convertCurrencyFormatToBaseAmount($this->input->post('amount')),
                'amount_discount' => convertCurrencyFormatToBaseAmount($this->input->post('amount_discount')),
                'amount_fine'     => convertCurrencyFormatToBaseAmount($this->input->post('amount_fine')),
                'date'            => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
                'description'     => $this->input->post('description'),
                'collected_by'    => $collected_by,
                'payment_mode'    => $this->input->post('payment_mode'),
                'received_by'     => $staff_record['id'],
            );

            $student_fees_master_id = $this->input->post('student_fees_master_id');
            $fee_groups_feetype_id  = $this->input->post('fee_groups_feetype_id');
            $transport_fees_id      = $this->input->post('transport_fees_id');
            $fee_category           = $this->input->post('fee_category');

            $data = array(
                'fee_category'           => $fee_category,
                'student_fees_master_id' => $this->input->post('student_fees_master_id'),
                'fee_groups_feetype_id'  => $this->input->post('fee_groups_feetype_id'),
                'amount_detail'          => $json_array,
            );

            if ($transport_fees_id != 0 && $fee_category == "transport") {
                $mailsms_array                    = new stdClass();
                $data['student_fees_master_id']   = null;
                $data['fee_groups_feetype_id']    = null;
                $data['student_transport_fee_id'] = $transport_fees_id;

                $mailsms_array                 = $this->studenttransportfee_model->getTransportFeeMasterByStudentTransportID($transport_fees_id);
                $mailsms_array->fee_group_name = $this->lang->line("transport_fees");
                $mailsms_array->type           = $mailsms_array->month;
                $mailsms_array->code           = "";
            } else {

                $mailsms_array = $this->feegrouptype_model->getFeeGroupByIDAndStudentSessionID($this->input->post('fee_groups_feetype_id'), $this->input->post('student_session_id'));

                if($mailsms_array->is_system){
                     $mailsms_array->amount=$mailsms_array->balance_fee_master_amount;
                }
            }

            $action             = $this->input->post('action');
            $send_to            = $this->input->post('guardian_phone');
            $email              = $this->input->post('guardian_email');
            $parent_app_key     = $this->input->post('parent_app_key');
            $student_session_id = $this->input->post('student_session_id');
            $inserted_id        = $this->studentfeemaster_model->fee_deposit($data, $send_to, $student_fees_discount_id);
            $array = array('status' => 'success', 'error' => '');
            echo json_encode($array);
        }
    }


    public function adddiscountstudentfee()
    {
        $this->form_validation->set_rules('student_fees_master_id', $this->lang->line('fee_master'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('fee_groups_feetype_id', $this->lang->line('student'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'required|trim|xss_clean|numeric|callback_check_deposit');

        if ($this->form_validation->run() == false) {
            $data = array(
                'amount'                 => form_error('amount'),
                'student_fees_master_id' => form_error('student_fees_master_id'),
                'fee_groups_feetype_id'  => form_error('fee_groups_feetype_id'),
                'date'           => form_error('date'),
            );
            $array = array('status' => 'fail', 'error' => $data);
            echo json_encode($array);
        } else {


            $data = array(
                'is_active'=>1,
                'approval_status' => 0,
                'student_session_id' =>$this->input->post('student_session_id'),
                'amount'          => convertCurrencyFormatToBaseAmount($this->input->post('amount')),
                'date'            => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
                'description'     => $this->input->post('description'),
                'student_fees_master_id' => $this->input->post('student_fees_master_id'),
                'fee_groups_feetype_id'  => $this->input->post('fee_groups_feetype_id'),
                'session_id' => $this->current_session,
            );

            $inserted_id        = $this->studentfeemaster_model->adddiscountstudentfee($data);


            $array = array('status' => 'success','data'=>$data);
            echo json_encode($array);
        }
    }





    public function printMiniFeesByGroupArray()
    {
        $data['sch_setting'] = $this->sch_setting_detail;
        $record              = $this->input->post('data');
        $record_array        = json_decode($record);
        $fees_array          = array();
        
        foreach ($record_array as $key => $value) {
            $fee_groups_feetype_id = $value->fee_groups_feetype_id;
            $fee_master_id         = $value->fee_master_id;
            $fee_session_group_id  = $value->fee_session_group_id;
            $fee_category          = $value->fee_category;
            $trans_fee_id          = $value->trans_fee_id;
            $otherfeecat           = $value->otherfeecat;
            $student_session_id    = $value->student_session_id;

            if ($fee_category == "transport") {
                // Get transport fee details
                if (!empty($trans_fee_id)) {
                    $feeList = $this->studentfeemaster_model->getTransportFeeByID($trans_fee_id);
                    if ($feeList) {
                        $feeList->fee_category = $fee_category;
                        $feeList->trans_fee_id = $trans_fee_id;
                        $feeList->student_session_id = $student_session_id;
                        
                        // Get payment history for transport fee
                        try {
                            $payment_history = $this->studentfeemaster_model->getTransportFeePaymentHistory($trans_fee_id);
                            if (!empty($payment_history)) {
                                $feeList->payment_history = $payment_history;
                            } else {
                                $feeList->payment_history = array(); // Initialize empty array if no payment history
                            }
                        } catch (Exception $e) {
                            // Log error and continue with empty payment history
                            error_log('Error getting transport fee payment history: ' . $e->getMessage());
                            $feeList->payment_history = array();
                        }
                        
                        $fees_array[] = $feeList;
                    }
                }
            } elseif ($fee_category == "hostel") {
                // Get hostel fee details
                $hostel_fee_id = !empty($value->hostel_fee_id) ? $value->hostel_fee_id : null;
                if (!empty($hostel_fee_id)) {
                    $feeList = $this->studenthostelfee_model->getHostelFeeByID($hostel_fee_id);
                    if ($feeList) {
                        $feeList->fee_category = $fee_category;
                        $feeList->hostel_fee_id = $hostel_fee_id;
                        $feeList->student_session_id = $student_session_id;
                        
                        // Get payment history for hostel fee
                        try {
                            $payment_history = $this->studenthostelfee_model->getHostelFeePaymentHistory($hostel_fee_id);
                            if (!empty($payment_history)) {
                                $feeList->payment_history = $payment_history;
                            } else {
                                $feeList->payment_history = array(); // Initialize empty array if no payment history
                            }
                        } catch (Exception $e) {
                            // Log error and continue with empty payment history
                            error_log('Error getting hostel fee payment history: ' . $e->getMessage());
                            $feeList->payment_history = array();
                        }
                        
                        $fees_array[] = $feeList;
                    }
                }
            } else if ($otherfeecat == "otherfee") {
                $feeList = $this->studentfeemasteradding_model->getDueFeeByFeeSessionGroupFeetype($fee_session_group_id, $fee_master_id, $fee_groups_feetype_id, $student_session_id);
                if ($feeList) {
                    $feeList->fee_category = $fee_category;
                    $fees_array[] = $feeList;
                }
            } else {
                $feeList = $this->studentfeemaster_model->getDueFeeByFeeSessionGroupFeetype($fee_session_group_id, $fee_master_id, $fee_groups_feetype_id);
                if ($feeList) {
                    $feeList->fee_category = $fee_category;
                    $fees_array[] = $feeList;
                }
            }
        }

        $data['feearray'] = $fees_array;
        $this->load->view('print/printMiniFeesByGroupArray', $data);
    }







    /**
     * Advance Payment Management Methods
     */

    /**
     * Show advance payment form
     */
    public function advancePayment()
    {
        if (!$this->rbac->hasPrivilege('collect_fees', 'can_add')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', $this->lang->line('fees_collection'));
        $this->session->set_userdata('sub_menu', 'studentfee/advancePayment');
        $data['title'] = 'Advance Payment';
        $data['sch_setting'] = $this->sch_setting_detail;
        $data['fields'] = $this->customfield_model->get_custom_fields('students', 1);
        $class = $this->class_model->get();
        $data['classlist'] = $class;
        $this->load->view('layout/header', $data);
        $this->load->view('studentfee/advancePaymentSearch', $data);
        $this->load->view('layout/footer', $data);
    }

    /**
     * Search for advance payment students
     */
    public function advanceSearch()
    {
        // Enhanced error logging and debugging - following student search pattern
        error_log('=== ADVANCE PAYMENT SEARCH VALIDATION STARTED ===');
        error_log('POST data: ' . print_r($_POST, true));
        error_log('Request method: ' . $_SERVER['REQUEST_METHOD']);

        // Handle multi-select values - convert to arrays if needed
        $class_id    = $this->input->post('class_id');
        $section_id  = $this->input->post('section_id');
        $search_type = $this->input->post('search_type');
        $search_text = $this->input->post('search_text');

        // Ensure arrays for multi-select values
        if (!is_array($class_id)) {
            $class_id = !empty($class_id) ? array($class_id) : array();
        }
        if (!is_array($section_id)) {
            $section_id = !empty($section_id) ? array($section_id) : array();
        }

        // Remove empty values from arrays
        $class_id = array_filter($class_id, function($value) { return !empty($value); });
        $section_id = array_filter($section_id, function($value) { return !empty($value); });

        error_log('Advance payment search validation - Processed: class_id=' . print_r($class_id, true) . ', section_id=' . print_r($section_id, true));
        log_message('debug', 'Advance payment search validation - Processed: class_id=' . print_r($class_id, true) . ', section_id=' . print_r($section_id, true));

        try {
            if ($search_type == 'search_filter') {
                // No mandatory validation - allow flexible report generation
                $params = array('search_type' => $search_type, 'class_id' => $class_id, 'section_id' => $section_id);
                $array  = array('status' => 1, 'error' => '', 'params' => $params);
                error_log('Advance payment search validation - Success response: ' . json_encode($array));
                log_message('debug', 'Advance payment search validation - Success response: ' . json_encode($array));
                echo json_encode($array);
            } else {
                // For full text search, no validation needed
                $params = array('search_type' => 'search_full', 'class_id' => $class_id, 'section_id' => $section_id, 'search_text' => $search_text);
                $array  = array('status' => 1, 'error' => '', 'params' => $params);
                error_log('Advance payment search validation - Full text search response: ' . json_encode($array));
                log_message('debug', 'Advance payment search validation - Full text search response: ' . json_encode($array));
                echo json_encode($array);
            }
        } catch (Exception $e) {
            error_log('Advance payment search validation - Exception: ' . $e->getMessage());
            log_message('error', 'Advance payment search validation - Exception: ' . $e->getMessage());
            $array = array('status' => 0, 'error' => array('general' => 'An error occurred during validation'));
            echo json_encode($array);
        }
    }

    /**
     * Search validation for advance payment - Following student search pattern exactly
     */
    public function searchvalidation()
    {
        // Enhanced error logging and debugging
        error_log('=== ADVANCE PAYMENT SEARCH VALIDATION STARTED ===');
        error_log('POST data: ' . print_r($_POST, true));
        error_log('Request method: ' . $_SERVER['REQUEST_METHOD']);
        error_log('Content type: ' . (isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : 'not set'));

        // Handle multi-select values - convert to arrays if needed
        $class_id    = $this->input->post('class_id');
        $section_id  = $this->input->post('section_id');
        $srch_type   = $this->input->post('search_type');
        $search_text = $this->input->post('search_text');

        // Ensure arrays for multi-select values
        if (!is_array($class_id)) {
            $class_id = !empty($class_id) ? array($class_id) : array();
        }
        if (!is_array($section_id)) {
            $section_id = !empty($section_id) ? array($section_id) : array();
        }

        error_log('Processed parameters:');
        error_log('- srch_type: ' . $srch_type);
        error_log('- class_id: ' . print_r($class_id, true));
        error_log('- section_id: ' . print_r($section_id, true));
        error_log('- search_text: ' . $search_text);

        try {
            if ($srch_type == 'search_filter') {
                // No mandatory validation - allow flexible report generation
                $params = array('srch_type' => $srch_type, 'class_id' => $class_id, 'section_id' => $section_id);
                $array  = array('status' => 1, 'error' => '', 'params' => $params);
                error_log('Advance payment search validation - Success response: ' . json_encode($array));
                log_message('debug', 'Advance payment search validation - Success response: ' . json_encode($array));
                echo json_encode($array);
            } else {
                // For full text search, no validation needed
                $params = array('srch_type' => 'search_full', 'class_id' => $class_id, 'section_id' => $section_id, 'search_text' => $search_text);
                $array  = array('status' => 1, 'error' => '', 'params' => $params);
                error_log('Advance payment search validation - Success response: ' . json_encode($array));
                log_message('debug', 'Advance payment search validation - Success response: ' . json_encode($array));
                echo json_encode($array);
            }
        } catch (Exception $e) {
            error_log('Advance payment search validation - Exception: ' . $e->getMessage());
            log_message('error', 'Advance payment search validation - Exception: ' . $e->getMessage());
            $array = array('status' => 0, 'error' => array('general' => 'An error occurred during validation'));
            echo json_encode($array);
        }
    }

    /**
     * Create advance payment
     */
    public function createAdvancePayment()
    {
        if (!$this->rbac->hasPrivilege('collect_fees', 'can_add')) {
            access_denied();
        }

        $this->form_validation->set_rules('student_session_id', $this->lang->line('student'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'required|trim|xss_clean|numeric|greater_than[0]');
        $this->form_validation->set_rules('payment_mode', $this->lang->line('payment_mode'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'required|trim|xss_clean');

        $action = $this->input->post('action'); // Get action (collect or print)

        if ($this->form_validation->run() == false) {
            $data = array(
                'student_session_id' => form_error('student_session_id'),
                'amount' => form_error('amount'),
                'payment_mode' => form_error('payment_mode'),
                'date' => form_error('date'),
            );
            $array = array('status' => 'fail', 'error' => $data);
            echo json_encode($array);
        } else {
            $staff_record = $this->staff_model->get($this->customlib->getStaffID());
            $collected_by = $this->customlib->getAdminSessionUserName() . "(" . $staff_record['employee_id'] . ")";

            $advance_data = array(
                'student_session_id' => $this->input->post('student_session_id'),
                'amount' => convertCurrencyFormatToBaseAmount($this->input->post('amount')),
                'balance' => convertCurrencyFormatToBaseAmount($this->input->post('amount')),
                'payment_date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
                'payment_mode' => $this->input->post('payment_mode'),
                'description' => $this->input->post('description'),
                'collected_by' => $collected_by,
                'received_by' => $staff_record['id'],
                'invoice_id' => $this->AdvancePayment_model->generateAdvanceInvoiceId(),
                'reference_no' => $this->input->post('reference_no'),
            );

            $inserted_id = $this->AdvancePayment_model->add($advance_data);

            if ($inserted_id) {
                // Note: Account transactions are not added for advance payments
                // They will be handled when the advance is actually used for fee payment

                if ($action === 'print') {
                    // Generate receipt for printing
                    $data['sch_setting'] = $this->sch_setting_detail;
                    $data['settinglist'] = $this->setting_model->get();
                    $data['advance_payment'] = $this->AdvancePayment_model->get($inserted_id);
                    
                    // Get student data from the advance payment record which already has joined student info
                    $data['student_data'] = $data['advance_payment'];

                    $page = $this->load->view('print/advancePaymentMiniReceipt', $data, true);
                    $array = array('status' => 'success', 'error' => '', 'message' => 'Advance payment created successfully', 'advance_id' => $inserted_id, 'print' => $page);
                } else {
                    $array = array('status' => 'success', 'error' => '', 'message' => 'Advance payment created successfully', 'advance_id' => $inserted_id);
                }
                
                echo json_encode($array);
            } else {
                $array = array('status' => 'fail', 'error' => 'Failed to create advance payment');
                echo json_encode($array);
            }
        }
    }

    /**
     * Get accounts for dropdown
     */
    public function getAccounts()
    {
        $accounts = $this->addaccount_model->get();
        echo json_encode($accounts);
    }

    /**
     * Get advance balance for a student
     */
    public function getAdvanceBalance()
    {
        $student_session_id = $this->input->post('student_session_id');

        if (!$student_session_id) {
            echo json_encode(array('status' => 'fail', 'error' => 'Student session ID required'));
            return;
        }

        $balance = $this->AdvancePayment_model->getAdvanceBalance($student_session_id);
        $advance_payments = $this->AdvancePayment_model->getStudentAdvancePayments($student_session_id);

        echo json_encode(array(
            'status' => 'success',
            'balance' => $balance,
            'formatted_balance' => amountFormat($balance),
            'advance_payments' => $advance_payments
        ));
    }

    /**
     * Print advance payment receipt
     */
    public function printAdvanceReceipt()
    {
        $advance_id = $this->input->post('advance_id');

        if (!$advance_id) {
            echo json_encode(array('status' => 0, 'error' => 'Advance payment ID required'));
            return;
        }

        $data['sch_setting'] = $this->sch_setting_detail;
        $data['settinglist'] = $this->setting_model->get();
        $data['advance_payment'] = $this->AdvancePayment_model->get($advance_id);

        if (!$data['advance_payment']) {
            echo json_encode(array('status' => 0, 'error' => 'Advance payment not found'));
            return;
        }

        // Get student data for the receipt
        $data['student_data'] = $data['advance_payment'];

        $page = $this->load->view('print/advancePaymentMiniReceipt', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    /**
     * Print advance payment mini receipt (similar to mini statement)
     */
    public function printAdvancePaymentMiniReceipt()
    {
        $advance_id = $this->input->post('advance_id');

        if (!$advance_id) {
            echo json_encode(array('status' => 'fail', 'error' => 'Advance payment ID required'));
            return;
        }

        $data['sch_setting'] = $this->sch_setting_detail;
        $data['settinglist'] = $this->setting_model->get();
        $data['advance_payment'] = $this->AdvancePayment_model->get($advance_id);

        if (!$data['advance_payment']) {
            echo json_encode(array('status' => 'fail', 'error' => 'Advance payment not found'));
            return;
        }

        // Get student data for the receipt
        $data['student_data'] = $data['advance_payment'];

        $page = $this->load->view('print/advancePaymentMiniReceipt', $data, true);
        echo json_encode(array('status' => 'success', 'page' => $page));
    }

    /**
     * Get advance payment history for a student
     */
    public function getAdvanceHistory()
    {
        $student_session_id = $this->input->post('student_session_id');

        if (!$student_session_id) {
            echo json_encode(array('status' => 'fail', 'error' => 'Student session ID required'));
            return;
        }

        $advance_payments = $this->AdvancePayment_model->getStudentAdvancePayments($student_session_id);
        $usage_history = $this->AdvancePayment_model->getAdvanceUsageHistory(null, $student_session_id);

        echo json_encode(array(
            'status' => 'success',
            'advance_payments' => $advance_payments,
            'usage_history' => $usage_history
        ));
    }

    /**
     * Revert advance payment usage
     */
    public function revertAdvancePayment()
    {
        // Set content type for JSON response
        header('Content-Type: application/json');

        // Log the request
        log_message('info', 'Revert advance payment request received. POST data: ' . json_encode($this->input->post()));

        if (!$this->rbac->hasPrivilege('collect_fees', 'can_add')) {
            log_message('error', 'Access denied for revert advance payment');
            echo json_encode(array('status' => 'fail', 'error' => 'Access denied'));
            return;
        }

        $usage_id = $this->input->post('usage_id');
        $reason = $this->input->post('reason');

        if (!$usage_id) {
            log_message('error', 'Usage ID not provided in revert request');
            echo json_encode(array('status' => 'fail', 'error' => 'Usage ID required'));
            return;
        }

        try {
            $this->load->model('AdvancePayment_model');

            // Validate that the usage record exists and belongs to the current session
            $student_session_id = $this->input->post('student_session_id');
            if ($student_session_id) {
                // Additional validation can be added here
                log_message('info', 'Reverting usage ID: ' . $usage_id . ' for student session: ' . $student_session_id);
            }

            $result = $this->AdvancePayment_model->revertAdvanceUsage($usage_id, $reason);

            if ($result) {
                log_message('info', 'Successfully reverted advance usage ID: ' . $usage_id);
                echo json_encode(array(
                    'status' => 'success',
                    'message' => 'Advance payment usage reverted successfully'
                ));
            } else {
                log_message('error', 'Failed to revert advance usage ID: ' . $usage_id);
                echo json_encode(array(
                    'status' => 'fail',
                    'error' => 'Failed to revert advance payment usage. Please check if the record exists and is not already reverted.'
                ));
            }
        } catch (Exception $e) {
            log_message('error', 'Exception in revert advance payment: ' . $e->getMessage());
            echo json_encode(array(
                'status' => 'fail',
                'error' => 'Error: ' . $e->getMessage()
            ));
        }
    }

    /**
     * Delete advance payment
     */
    public function deleteAdvancePayment()
    {
        // Set content type for JSON response
        header('Content-Type: application/json');

        // Check permissions
        if (!$this->rbac->hasPrivilege('collect_fees', 'can_delete')) {
            echo json_encode(array('status' => 'error', 'message' => 'Access denied'));
            return;
        }

        $advance_payment_id = $this->input->post('advance_payment_id');

        if (!$advance_payment_id) {
            echo json_encode(array('status' => 'error', 'message' => 'Advance payment ID required'));
            return;
        }

        try {
            $this->load->model('AdvancePayment_model');

            $result = $this->AdvancePayment_model->deleteAdvancePayment($advance_payment_id);

            echo json_encode($result);
        } catch (Exception $e) {
            log_message('error', 'Exception in delete advance payment: ' . $e->getMessage());
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ));
        }
    }

    /**
     * Alternative endpoint for revert advance usage (for compatibility)
     */
    public function revertAdvanceUsage()
    {
        // Call the main revert method
        $this->revertAdvancePayment();
    }

    /**
     * Debug endpoint to test revert functionality
     * Remove this method in production
     */
    public function testRevert($usage_id = null)
    {
        if (!$this->rbac->hasPrivilege('collect_fees', 'can_add')) {
            echo "Access denied";
            return;
        }

        echo "<h2>Advance Payment Revert Test</h2>";

        if (!$usage_id) {
            echo "<p>Usage: /studentfee/testRevert/[usage_id]</p>";

            // Show available usage records
            $this->load->model('AdvancePayment_model');
            $this->db->where('is_reverted !=', 'yes');
            $this->db->or_where('is_reverted IS NULL');
            $usage_records = $this->db->get('advance_payment_usage')->result();

            if ($usage_records) {
                echo "<h3>Available Usage Records for Testing:</h3>";
                echo "<ul>";
                foreach ($usage_records as $record) {
                    echo "<li><a href='" . site_url('studentfee/testRevert/' . $record->id) . "'>Usage ID: " . $record->id . " (Amount: " . $record->amount_used . ")</a></li>";
                }
                echo "</ul>";
            } else {
                echo "<p>No usage records available for testing.</p>";
            }
            return;
        }

        // Test the revert functionality
        echo "<h3>Testing Revert for Usage ID: $usage_id</h3>";

        try {
            $this->load->model('AdvancePayment_model');

            // Get usage record details before revert
            $usage_before = $this->db->get_where('advance_payment_usage', array('id' => $usage_id))->row();
            if ($usage_before) {
                echo "<h4>Before Revert:</h4>";
                echo "<pre>" . print_r($usage_before, true) . "</pre>";

                // Get advance payment details
                $advance_payment_before = $this->db->get_where('student_advance_payments', array('id' => $usage_before->advance_payment_id))->row();
                echo "<h4>Advance Payment Before:</h4>";
                echo "<pre>" . print_r($advance_payment_before, true) . "</pre>";

                // Perform revert
                $result = $this->AdvancePayment_model->revertAdvanceUsage($usage_id, 'Test revert from debug endpoint');

                if ($result) {
                    echo "<h4 style='color: green;'>✅ Revert Successful!</h4>";

                    // Get records after revert
                    $usage_after = $this->db->get_where('advance_payment_usage', array('id' => $usage_id))->row();
                    $advance_payment_after = $this->db->get_where('student_advance_payments', array('id' => $usage_before->advance_payment_id))->row();

                    echo "<h4>After Revert:</h4>";
                    echo "<h5>Usage Record:</h5>";
                    echo "<pre>" . print_r($usage_after, true) . "</pre>";
                    echo "<h5>Advance Payment:</h5>";
                    echo "<pre>" . print_r($advance_payment_after, true) . "</pre>";

                } else {
                    echo "<h4 style='color: red;'>❌ Revert Failed!</h4>";
                }

            } else {
                echo "<p style='color: red;'>Usage record not found!</p>";
            }

        } catch (Exception $e) {
            echo "<h4 style='color: red;'>❌ Exception: " . $e->getMessage() . "</h4>";
        }
    }

    /**
     * Get advance payment details for fee collection page
     */
    public function getAdvancePaymentDetails()
    {
        $student_session_id = $this->input->post('student_session_id');

        if (!$student_session_id) {
            echo json_encode(array('status' => 'fail', 'error' => 'Student session ID required'));
            return;
        }

        $advance_balance = $this->AdvancePayment_model->getAdvanceBalance($student_session_id);
        $advance_payments = $this->AdvancePayment_model->getStudentAdvancePayments($student_session_id);
        $usage_history = $this->AdvancePayment_model->getAdvanceUsageHistory(null, $student_session_id);

        echo json_encode(array(
            'status' => 'success',
            'balance' => $advance_balance,
            'formatted_balance' => amountFormat($advance_balance),
            'advance_payments' => $advance_payments,
            'usage_history' => $usage_history
        ));
    }

    /**
     * AJAX search for advance payment students - Following student search pattern
     */
    public function ajaxAdvanceSearch()
    {
        // Enhanced error logging and debugging - following student search pattern
        error_log('=== ADVANCE PAYMENT DATATABLE REQUEST STARTED ===');
        error_log('POST data: ' . print_r($_POST, true));
        error_log('Request method: ' . $_SERVER['REQUEST_METHOD']);

        $currency_symbol = $this->customlib->getSchoolCurrencyFormat();
        $class_id        = $this->input->post('class_id');
        $section_id      = $this->input->post('section_id');
        $search_text     = $this->input->post('search_text');
        $search_type     = $this->input->post('srch_type'); // Match the parameter name from searchvalidation

        // Enhanced debug logging
        error_log('Advance Payment DataTable - Raw input: class_id=' . print_r($class_id, true) . ', section_id=' . print_r($section_id, true));
        log_message('debug', 'Advance Payment DataTable - Raw input: class_id=' . print_r($class_id, true) . ', section_id=' . print_r($section_id, true));

        // Handle multi-select values - convert to arrays if needed (following student search pattern)
        if (!is_array($class_id)) {
            $class_id = !empty($class_id) ? array($class_id) : array();
        }
        if (!is_array($section_id)) {
            $section_id = !empty($section_id) ? array($section_id) : array();
        }

        // Remove empty values from arrays
        $class_id = array_filter($class_id, function($value) { return !empty($value); });
        $section_id = array_filter($section_id, function($value) { return !empty($value); });

        error_log('Advance Payment DataTable - Processed: class_id=' . print_r($class_id, true) . ', section_id=' . print_r($section_id, true));

        $sch_setting = $this->sch_setting_detail;

        try {
            $resultlist = '';
            // Use the same model methods as student search
            if ($search_type == "search_filter") {
                error_log('Advance Payment DataTable - Calling searchdtByClassSection with class_id=' . print_r($class_id, true) . ', section_id=' . print_r($section_id, true));
                $resultlist = $this->student_model->searchdtByClassSection($class_id, $section_id);
            } elseif ($search_type == "search_full") {
                error_log('Advance Payment DataTable - Calling searchFullText with search_text=' . $search_text);
                $resultlist = $this->student_model->searchFullText($search_text, array());
            } else {
                error_log('Advance Payment DataTable - Unknown search type: ' . $search_type);
                throw new Exception('Unknown search type: ' . $search_type);
            }

            error_log('Advance Payment DataTable - Model result length: ' . strlen($resultlist));
            error_log('Advance Payment DataTable - Model result preview: ' . substr($resultlist, 0, 300) . '...');

            // Handle empty result from model
            if (empty($resultlist) || trim($resultlist) === '') {
                error_log('Advance Payment DataTable - Empty result from model, creating default structure');
                $students = (object)array('data' => array(), 'draw' => 1, 'recordsTotal' => 0, 'recordsFiltered' => 0);
            } else {
                $students = json_decode($resultlist);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    error_log('Advance Payment DataTable - JSON decode error: ' . json_last_error_msg());
                    error_log('Advance Payment DataTable - Raw result causing error: ' . $resultlist);
                    throw new Exception('JSON decode failed: ' . json_last_error_msg());
                }
                error_log('Advance Payment DataTable - Decoded result: ' . print_r($students, true));
            }
        } catch (Exception $e) {
            error_log('Advance Payment DataTable - Exception: ' . $e->getMessage());
            $students = (object)array('data' => array(), 'draw' => 1, 'recordsTotal' => 0, 'recordsFiltered' => 0);
        }

        $dt_data = array();
        $fields  = $this->customfield_model->get_custom_fields('students', 1);

        if (!empty($students->data)) {
            foreach ($students->data as $student_key => $student) {
                try {
                    // Get advance balance for each student
                    $advance_balance = 0; // Default to 0 if model method fails
                    if (method_exists($this->AdvancePayment_model, 'getAdvanceBalance')) {
                        $advance_balance = $this->AdvancePayment_model->getAdvanceBalance($student->student_session_id);
                    } else {
                        error_log('Advance Payment DataTable - getAdvanceBalance method not found in AdvancePayment_model');
                    }

                    $row = array();
                    // Follow exact same structure as student search
                    $row[] = $student->admission_no;
                    $row[] = "<a href='" . base_url() . "student/view/" . $student->id . "'>" . $this->customlib->getFullName($student->firstname, $student->middlename, $student->lastname, $sch_setting->middlename, $sch_setting->lastname) . "</a>";
                    $row[] = $student->class . "(" . $student->section . ")";

                    if ($sch_setting->father_name) {
                        $row[] = $student->father_name;
                    }

                    $row[] = $this->customlib->dateformat($student->dob);

                    if (!empty($student->gender)) {
                        $row[] = $this->lang->line(strtolower($student->gender));
                    } else {
                        $row[] = '';
                    }

                    if ($sch_setting->category) {
                        $row[] = $student->category;
                    }

                    if ($sch_setting->mobile_no) {
                        $row[] = $student->mobileno;
                    }

                    // Add custom fields (same as student search)
                    foreach ($fields as $fields_key => $fields_value) {
                        $custom_name   = $fields_value->name;
                        $display_field = $student->$custom_name;
                        if ($fields_value->type == "link") {
                            $display_field = "<a href=" . $student->$custom_name . " target='_blank'>" . $student->$custom_name . "</a>";
                        }
                        $row[] = $display_field;
                    }

                    // Add advance balance column
                    $row[] = $currency_symbol . amountFormat($advance_balance);

                    // Action buttons
                    $action = '<div class="btn-group">';
                    $action .= '<button type="button" class="btn btn-primary btn-xs" onclick="openAdvancePaymentModal(\'' . $student->student_session_id . '\', \'' . addslashes($this->customlib->getFullName($student->firstname, $student->middlename, $student->lastname, $sch_setting->middlename, $sch_setting->lastname)) . '\', \'' . $student->admission_no . '\', \'' . $student->class . ' (' . $student->section . ')\', \'' . addslashes($student->father_name) . '\')" title="' . $this->lang->line('add_advance_payment') . '">';
                    $action .= '<i class="fa fa-plus"></i> ' . $this->lang->line('add_advance_payment');
                    $action .= '</button>';

                    if ($advance_balance > 0) {
                        $action .= '<button type="button" class="btn btn-info btn-xs" onclick="viewAdvanceHistory(\'' . $student->student_session_id . '\')" title="' . $this->lang->line('view_history') . '">';
                        $action .= '<i class="fa fa-history"></i>';
                        $action .= '</button>';
                    }

                    $action .= '</div>';
                    $row[] = $action;

                    $dt_data[] = $row;
                } catch (Exception $e) {
                    error_log('Advance Payment DataTable - Error processing student: ' . $e->getMessage());
                    continue;
                }
            }
        }

        // Follow the exact same JSON response pattern as student search
        $json_data = array(
            "draw"                => intval(isset($students->draw) ? $students->draw : 1),
            "recordsTotal"        => intval(isset($students->recordsTotal) ? $students->recordsTotal : 0),
            "recordsFiltered"     => intval(isset($students->recordsFiltered) ? $students->recordsFiltered : 0),
            "data"                => $dt_data,
        );

        error_log('Advance Payment DataTable - Final JSON data: ' . print_r($json_data, true));
        error_log('Advance Payment DataTable - Data rows count: ' . count($dt_data));

        // Set proper JSON header
        header('Content-Type: application/json');
        echo json_encode($json_data);

    }

    /**
     * Get fee payment history for individual fee items
     */
    public function getFeeHistory()
    {
        if (!$this->rbac->hasPrivilege('collect_fees', 'can_view')) {
            access_denied();
        }

        $student_session_id = $this->input->post('student_session_id');
        $student_fees_master_id = $this->input->post('student_fees_master_id');
        $fee_groups_feetype_id = $this->input->post('fee_groups_feetype_id');
        $fee_session_group_id = $this->input->post('fee_session_group_id');

        if (!$student_session_id || !$student_fees_master_id || !$fee_groups_feetype_id) {
            echo '<div class="alert alert-danger">Invalid parameters</div>';
            return;
        }

        // Get fee payment history
        $fee_history = $this->studentfeemaster_model->getFeePaymentHistory($student_fees_master_id, $fee_groups_feetype_id);

        $data['fee_history'] = $fee_history;
        $data['currency_symbol'] = $this->customlib->getSchoolCurrencyFormat();

        $this->load->view('studentfee/fee_history', $data);
    }

    /**
     * Get transport fee payment history
     */
    public function getTransportFeeHistory()
    {
        if (!$this->rbac->hasPrivilege('collect_fees', 'can_view')) {
            access_denied();
        }

        $student_session_id = $this->input->post('student_session_id');
        $trans_fee_id = $this->input->post('trans_fee_id');

        if (!$student_session_id || !$trans_fee_id) {
            echo '<div class="alert alert-danger">Invalid parameters</div>';
            return;
        }

        // Get transport fee payment history
        $transport_fee_history = $this->studentfeemaster_model->getTransportFeePaymentHistory($trans_fee_id);

        $data['transport_fee_history'] = $transport_fee_history;
        $data['currency_symbol'] = $this->customlib->getSchoolCurrencyFormat();

        $this->load->view('studentfee/transport_fee_history', $data);
    }

    /**
     * Get hostel fee payment history
     */
    public function getHostelFeeHistory()
    {
        if (!$this->rbac->hasPrivilege('collect_fees', 'can_view')) {
            access_denied();
        }

        $student_session_id = $this->input->post('student_session_id');
        $trans_fee_id = $this->input->post('trans_fee_id');

        if (!$student_session_id || !$trans_fee_id) {
            echo '<div class="alert alert-danger">Invalid parameters</div>';
            return;
        }

        // Get hostel fee payment history
        $hostel_fee_history = $this->studenthostelfee_model->getHostelFeePaymentHistory($trans_fee_id);

        $data['hostel_fee_history'] = $hostel_fee_history;
        $data['currency_symbol'] = $this->customlib->getSchoolCurrencyFormat();

        $this->load->view('studentfee/hostel_fee_history', $data);
    }

    /**
     * Get additional fee payment history
     */
    public function getAdditionalFeeHistory()
    {
        if (!$this->rbac->hasPrivilege('collect_fees', 'can_view')) {
            access_denied();
        }

        $student_session_id = $this->input->post('student_session_id');
        $student_fees_master_id = $this->input->post('student_fees_master_id');
        $fee_groups_feetype_id = $this->input->post('fee_groups_feetype_id');
        $fee_session_group_id = $this->input->post('fee_session_group_id');

        if (!$student_session_id || !$student_fees_master_id || !$fee_groups_feetype_id) {
            echo '<div class="alert alert-danger">Invalid parameters</div>';
            return;
        }

        // Get additional fee payment history
        $additional_fee_history = $this->studentfeemasteradding_model->getAdditionalFeePaymentHistory($student_fees_master_id, $fee_groups_feetype_id);

        $data['additional_fee_history'] = $additional_fee_history;
        $data['currency_symbol'] = $this->customlib->getSchoolCurrencyFormat();

        $this->load->view('studentfee/additional_fee_history', $data);
    }

    /**
     * Store detailed advance payment transfer information for tracking and reporting
     */
    private function storeAdvanceTransferDetails($transfer_detail) {
        try {
            $staff_record = $this->customlib->getStaffDetails();
            
            $transfer_data = array(
                'student_session_id' => $transfer_detail['student_session_id'] ?? 0,
                'advance_payment_id' => $transfer_detail['advance_payment_id'],
                'fee_receipt_id' => $transfer_detail['fee_invoice'],
                'fee_category' => $transfer_detail['fee_category'],
                'transfer_amount' => $transfer_detail['amount_transferred'],
                'advance_balance_before' => $transfer_detail['balance_before_transfer'],
                'advance_balance_after' => $transfer_detail['balance_after_transfer'],
                'original_advance_amount' => $transfer_detail['original_advance_amount'],
                'original_advance_date' => $transfer_detail['original_advance_date'],
                'transfer_type' => ($transfer_detail['amount_transferred'] == $transfer_detail['balance_before_transfer']) ? 'Complete' : 'Partial',
                'account_impact' => 'Zero Cash Entry - Direct Advance Utilization',
                'transfer_description' => 'Advance payment applied to ' . $transfer_detail['fee_category'] . ' fee - Invoice: ' . $transfer_detail['fee_invoice'],
                'created_by' => $staff_record['id'] ?? null,
                'created_at' => $transfer_detail['transfer_timestamp']
            );
            
            // Check if advance_payment_transfers table exists, create if not
            $this->db->query("CREATE TABLE IF NOT EXISTS `advance_payment_transfers` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `student_session_id` int(11) NOT NULL,
                `advance_payment_id` int(11) NOT NULL,
                `fee_receipt_id` varchar(50) NOT NULL,
                `fee_category` varchar(50) DEFAULT NULL,
                `transfer_amount` decimal(10,2) NOT NULL,
                `advance_balance_before` decimal(10,2) NOT NULL,
                `advance_balance_after` decimal(10,2) NOT NULL,
                `original_advance_amount` decimal(10,2) DEFAULT NULL,
                `original_advance_date` date DEFAULT NULL,
                `transfer_type` enum('Complete','Partial') DEFAULT 'Partial',
                `account_impact` varchar(100) DEFAULT 'Zero Cash Entry - Direct Advance Utilization',
                `transfer_description` text,
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `created_by` int(11) DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `idx_student_session` (`student_session_id`),
                KEY `idx_advance_payment` (`advance_payment_id`),
                KEY `idx_fee_receipt` (`fee_receipt_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
            
            // Insert the transfer record
            $this->db->insert('advance_payment_transfers', $transfer_data);
            
            error_log("ADVANCE TRANSFER STORED: " . json_encode($transfer_data));
            
        } catch (Exception $e) {
            error_log("ERROR STORING ADVANCE TRANSFER: " . $e->getMessage());
        }
    }

    /**
     * Get advance payment transfers history for a student
     */
    public function getAdvanceTransfersHistory() {
        if (!$this->rbac->hasPrivilege('collect_fees', 'can_view')) {
            echo json_encode(['status' => 'error', 'message' => 'Access denied']);
            return;
        }

        $student_session_id = $this->input->post('student_session_id');
        
        if (!$student_session_id) {
            echo json_encode(['status' => 'error', 'message' => 'Student session ID required']);
            return;
        }

        try {
            // Check if the advance_payment_transfers table exists
            $table_exists = $this->db->table_exists('advance_payment_transfers');
            
            if ($table_exists) {
                // Get transfers from the tracking table
                $this->db->select('apt.*, ap.amount as original_amount, ap.payment_date as original_date, ap.description as advance_description');
                $this->db->from('advance_payment_transfers apt');
                $this->db->join('student_advance_payments ap', 'apt.advance_payment_id = ap.id', 'left');
                $this->db->where('apt.student_session_id', $student_session_id);
                $this->db->order_by('apt.created_at', 'DESC');
                $query = $this->db->get();
                
                $transfers = $query->result();
            } else {
                $transfers = array();
            }
            
            if (empty($transfers)) {
                // Fallback: Get transfers from advance payment usage table if tracking table is empty
                $this->db->select('apu.*, ap.amount as original_amount, ap.payment_date as original_date, ap.description as advance_description, ap.student_session_id, apu.usage_date as created_at, apu.amount_used as transfer_amount, CONCAT("USAGE-", apu.id) as fee_receipt_id');
                $this->db->from('advance_payment_usage apu');
                $this->db->join('student_advance_payments ap', 'apu.advance_payment_id = ap.id', 'inner');
                $this->db->where('ap.student_session_id', $student_session_id);
                $this->db->where('apu.is_reverted', 'no');
                $this->db->order_by('apu.created_at', 'DESC');
                $query = $this->db->get();
                
                $transfers = $query->result();
            }

            // Get student information
            $student_info = $this->studentsession_model->searchStudentsBySession($student_session_id);
            
            $data['transfers'] = $transfers;
            $data['student_info'] = $student_info;
            $data['currency_symbol'] = $this->customlib->getSchoolCurrencyFormat();
            
            $html = $this->load->view('studentfee/advance_transfers_history', $data, true);
            
            if (empty($transfers)) {
                echo json_encode([
                    'status' => 'success',
                    'html' => $html,
                    'count' => 0,
                    'message' => 'No advance payment transfers found. Transfers will appear here when advance payments are used for fee collection.'
                ]);
            } else {
                echo json_encode([
                    'status' => 'success',
                    'html' => $html,
                    'count' => count($transfers)
                ]);
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Error loading transfer history: ' . $e->getMessage()
            ]);
        }
    }

    public function test_connection() {
        // Simple test method to check if controller loads properly
        $response = array(
            'status' => 'success',
            'message' => 'Controller loaded successfully',
            'timestamp' => date('Y-m-d H:i:s')
        );
        
        // Test if feediscount model can be loaded
        try {
            $this->load->model('feediscount_model');
            $response['feediscount_model'] = 'loaded successfully';
        } catch (Exception $e) {
            $response['feediscount_model'] = 'error: ' . $e->getMessage();
        }
        
        // Test if studentfeemaster model can be loaded
        try {
            $this->load->model('studentfeemaster_model');
            $response['studentfeemaster_model'] = 'loaded successfully';
        } catch (Exception $e) {
            $response['studentfeemaster_model'] = 'error: ' . $e->getMessage();
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
    }

}
