<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Halltickectgeneration extends Admin_Controller
{

    public function __construct()
    {
       
        parent::__construct();
        $this->sch_setting_detail = $this->setting_model->getSetting();
        $this->load->library('customlib');
        $this->load->library('media_storage');
        $this->load->model('halltickectgeneration_model');
        $this->load->model('setting_model');


        $this->load->model('feegroup_model'); 
        $this->load->model('feetype_model'); 
        $this->load->model('feesessiongroup_model');
        
    }

    public function index()
    {
        // if (!$this->rbac->hasPrivilege('generate_certificate', 'can_view')) {
        //     access_denied();
        // }
        // $this->session->set_userdata('top_menu', 'Certificate');
        // $this->session->set_userdata('sub_menu', 'admin/generatecertificate');

        $certificateList         = $this->halltickectgeneration_model->getstudentcertificate();
        $data['certificateList'] = $certificateList;
        $class                   = $this->class_model->get();
        $data['classlist']       = $class;
        $getsubjectgroups = $this->halltickectgeneration_model->getsubjectgroups();
        $data['getsubjectgroups'] = $getsubjectgroups;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/halltickectgeneration/generatecertificate', $data);
        $this->load->view('layout/footer', $data);
    }

    public function search()
    {
        // $this->session->set_userdata('top_menu', 'Certificate');
        // $this->session->set_userdata('sub_menu', 'admin/generatecertificate');

        $class                   = $this->class_model->get();
        $data['classlist']       = $class;
        $certificateList         = $this->halltickectgeneration_model->getstudentcertificate();
        $data['certificateList'] = $certificateList;
        $getsubjectgroups = $this->halltickectgeneration_model->getsubjectgroups();
        $data['getsubjectgroups'] = $getsubjectgroups;

        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/halltickectgeneration/generatecertificate', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $class       = $this->input->post('class_id');
            $section     = $this->input->post('section_id');
            $search      = $this->input->post('search');
            $certificate = $this->input->post('certificate_id');
            if (isset($search)) {
                $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
                $this->form_validation->set_rules('certificate_id', $this->lang->line('halltickect_name'), 'trim|required|xss_clean');
                $this->form_validation->set_rules('subjectgrp_id', $this->lang->line('subject_group'), 'trim|required|xss_clean');

                if ($this->form_validation->run() == false) {

                } else {
                    $data['searchby']          = "filter";
                    $data['class_id']          = $this->input->post('class_id');
                    $data['section_id']        = $this->input->post('section_id');
                    $certificate               = $this->input->post('certificate_id');
                    $certificateResult         = $this->halltickectgeneration_model->getcertificatebyid($certificate);
                    $data['certificateResult'] = $certificateResult;
                    $resultlist                = $this->student_model->searchByClassSection($class, $section);
                    $data['resultlist']        = $resultlist;
                    $title                     = $this->classsection_model->getDetailbyClassSection($data['class_id'], $data['section_id']);
                    // $data['title']             = $this->lang->line('std_dtl_for') . ' ' . $title['class'] . "(" . $title['section'] . ")";
                }
            }
            $data['sch_setting'] = $this->sch_setting_detail;
            $this->load->view('layout/header', $data);
            $this->load->view('admin/halltickectgeneration/generatecertificate', $data);
            $this->load->view('layout/footer', $data);
        }

    }

    public function createtc()
    {

        // if (!$this->rbac->hasPrivilege('student_id_card', 'can_view')) {
        //     access_denied();
        // }

        // $this->session->set_userdata('top_menu', 'Certificate');
        // $this->session->set_userdata('sub_menu', 'admin/studentidcard');
        
        $this->data['idcardlist'] = $this->halltickectgeneration_model->idcardlist();
        // $this->data['classlist'] = $this->halltickectgeneration_model->getsubjects();

        
        $this->load->view('layout/header');
        $this->load->view('admin/halltickectgeneration/createidcard', $this->data);
        $this->load->view('layout/footer');
    }


    public function handle_upload($var)
    {
        $image_validate = $this->config->item('image_validate');
        $result         = $this->filetype_model->get();
        if (isset($_FILES[$var]) && !empty($_FILES[$var]['name'])) {

            $file_type = $_FILES[$var]['type'];
            $file_size = $_FILES[$var]["size"];
            $file_name = $_FILES[$var]["name"];

            $allowed_extension = array_map('trim', array_map('strtolower', explode(',', $result->image_extension)));
            $allowed_mime_type = array_map('trim', array_map('strtolower', explode(',', $result->image_mime)));
            $ext               = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if ($files = @getimagesize($_FILES[$var]['tmp_name'])) {

                if (!in_array($files['mime'], $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }

                if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('extension_not_allowed'));
                    return false;
                }

                if ($file_size > $result->image_size) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($result->image_size / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed_or_extension_not_allowed'));
                return false;
            }

            return true;
        }
        return true;
    }



    public function create()
    {

        // if (!$this->rbac->hasPrivilege('student_id_card', 'can_add')) {
        //     access_denied();
        // }

        // $data['title'] = 'Student ID Card';

        $this->form_validation->set_rules('halltickect_name', $this->lang->line('halltickect_name'), 'trim|required|xss_clean');
        
        $this->form_validation->set_rules('hall_exam_heading', $this->lang->line('hall_exam_heading'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('school_name', $this->lang->line('school_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('hall_address', $this->lang->line('hall_address'), 'trim|required|xss_clean');
        
        $this->form_validation->set_rules('hall_email', $this->lang->line('hall_email'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('hall_phone', $this->lang->line('hall_phone'), 'trim|required|xss_clean');
        
        $this->form_validation->set_rules('logo_img', $this->lang->line('logo'), 'callback_handle_upload');
        


        if ($this->form_validation->run() == false) {
            $this->data['idcardlist'] = $this->halltickectgeneration_model->idcardlist();
            // $this->data['classlist'] = $this->halltickectgeneration_model->getsubjects();
            

            $this->load->view('layout/header');
            $this->load->view('admin/halltickectgeneration/createidcard', $this->data);
            $this->load->view('layout/footer');
        } else {
            
           
            $data = array(
                'halltickect_name '                => $this->input->post('halltickect_name'),
                'schoolname '            => $this->input->post('school_name'),
                'address '         => $this->input->post('hall_address'),
                'email'         => $this->input->post('hall_email'),
                'phone'             => $this->input->post('hall_phone'),
                'toplefttext'                => $this->input->post('top_left_text'),
                'topmiddletext'           => $this->input->post('top_middle_text'),
                'toprighttext'         => $this->input->post('top_right_text'),
                'bottomlefttext'  => $this->input->post('bottom_left_text'),
                'bottommiddletext'  => $this->input->post('bottom_middle_text'),
                'bottomrighttext' => $this->input->post('bottom_right_text'),
                'sessionid'  => $this->setting_model->getCurrentSession(),
                'examheading'  => $this->input->post('hall_exam_heading'),
                
            );

            if (!empty($_FILES['logo_img']['name'])) {
                
                // $logo_img_name = $this->media_storage->applicationfileupload("logo_img", "./uploads/tcgeneration/logo/");

                $logo_img_name = $this->media_storage->fileupload("logo_img", "./uploads/halltickectgeneration/logo/");
            } else {
                $logo_img_name = '';
            }
            $data['logo_path '] = $logo_img_name;

            $insert_id = $this->halltickectgeneration_model->addtcgenerate($data);

            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
            redirect('admin/halltickectgeneration/create');
        }
    }


    public function edit($id)
    {
        // if (!$this->rbac->hasPrivilege('student_id_card', 'can_edit')) {
        //     access_denied();
        // }

        $data['title']            = 'Edit ID Card';
        $editidcard               = $this->halltickectgeneration_model->get($id);
        $this->data['editidcard'] = $editidcard;


        $this->form_validation->set_rules('halltickect_name', $this->lang->line('halltickect_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('hall_exam_heading', $this->lang->line('hall_exam_heading'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('school_name', $this->lang->line('school_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('hall_address', $this->lang->line('hall_address'), 'trim|required|xss_clean');
        
        $this->form_validation->set_rules('hall_email', $this->lang->line('hall_email'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('hall_phone', $this->lang->line('hall_phone'), 'trim|required|xss_clean');
        
        $this->form_validation->set_rules('logo_img', $this->lang->line('logo'), 'callback_handle_upload');
        


        
        
        if ($this->form_validation->run() == false) {

            $this->data['idcardlist'] = $this->halltickectgeneration_model->idcardlist();
            // $this->data['classlist'] = $this->halltickectgeneration_model->getsubjects();
            
            $this->load->view('layout/header');
            $this->load->view('admin/halltickectgeneration/studentidcardedit', $this->data);
            $this->load->view('layout/footer');
        } else {
           

            $data = array(
                'id'=>$this->input->post('id'),
                'halltickect_name '                => $this->input->post('halltickect_name'),
                'schoolname '            => $this->input->post('school_name'),
                'address '         => $this->input->post('hall_address'),
                'email'         => $this->input->post('hall_email'),
                'phone'             => $this->input->post('hall_phone'),
                'toplefttext'                => $this->input->post('top_left_text'),
                'topmiddletext'           => $this->input->post('top_middle_text'),
                'toprighttext'         => $this->input->post('top_right_text'),
                'bottomlefttext'  => $this->input->post('bottom_left_text'),
                'bottommiddletext'  => $this->input->post('bottom_middle_text'),
                'bottomrighttext' => $this->input->post('bottom_right_text'),
                'sessionid'  => $this->setting_model->getCurrentSession(),
                'examheading'  => $this->input->post('hall_exam_heading'),
                
            );


            $removelogo_image       = $this->input->post('removelogo_image');
            // $removesign_image       = $this->input->post('removesign_image');

            
            if ($removelogo_image != '') {
                $data['logo_path'] = '';
            }

            // if ($removesign_image != '') {
            //     $data['sign_image'] = '';
            // }

            

            // if (isset($_FILES["logo_img"]) && $_FILES['logo_img']['name'] != '' && (!empty($_FILES['logo_img']['name']))) {
            //     $logo_img     = $this->media_storage->fileupload("logo_img", "./uploads/student_id_card/logo/");
            //     $data['logo'] = $logo_img;
            // }

            // if (isset($_FILES["logo_img"]) && $_FILES['logo_img']['name'] != '' && (!empty($_FILES['logo_img']['name']))) {
            //     $this->media_storage->filedelete($editidcard[0]->logo, "uploads/student_id_card/logo");
            // }
            
            if (isset($_FILES["logo_img"]) && $_FILES['logo_img']['name'] != '' && (!empty($_FILES['logo_img']['name']))) {
                $this->media_storage->filedelete($editidcard[0]->logo, "uploads/halltickectgeneration/logo");
            }


            if (isset($_FILES["logo_img"]) && $_FILES['logo_img']['name'] != '' && (!empty($_FILES['logo_img']['name']))) {
                $logo_img     = $this->media_storage->fileupload("logo_img", "./uploads/halltickectgeneration/logo/");
                $data['logo_path'] = $logo_img;
            }


            
            // $this->Student_id_card_model->addidcard($data);
            $insert_id = $this->halltickectgeneration_model->addtcgenerate($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('update_message') . '</div>');
            redirect('admin/halltickectgeneration/createtc');
        }
    }


    public function delete($id)
    {
        $data['title'] = 'Certificate List';
        $row           = $this->halltickectgeneration_model->get($id);
        
        if ($row[0]->logo != '') {
            $this->media_storage->filedelete($row[0]->logo, "uploads/halltickectgeneration/logo/");
        }

        $this->halltickectgeneration_model->remove($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('delete_message') . '</div>');
        redirect('admin/halltickectgeneration/createtc');
    }


    public function view()
    {
        $id             = $this->input->post('certificateid');
        $output         = '';
        $data['idcard'] = $this->halltickectgeneration_model->idcardbyid($id);
        // $certificate         = $this->halltickectgeneration_model->getcertificatebyid($id);
        // $data['certificate'] = $certificate;
        $this->load->view('admin/halltickectgeneration/studentidcardpreview', $data);
    }


    public function generate($student, $class, $certificate)
    {
        $certificateResult         = $this->halltickectgeneration_model->getcertificatebyid($certificate);
        $data['certificateResult'] = $certificateResult;
        $resultlist                = $this->student_model->searchByClassStudent($class, $student);
        $data['resultlist']        = $resultlist;

        $this->load->view('admin/halltickectgeneration/transfercertificate', $data);
    }


    public function generatemultiple()
    {

        $studentid           = $this->input->post('data');
        $student_array       = json_decode($studentid);
        $certificate_id      = $this->input->post('certificate_id');
        $subjectgrpId        = $this->input->post('subjectgrp_id');
        $class               = $this->input->post('class_id');
        $data                = array();
        $results             = array();
        $std_arr             = array();
        
        $data['sch_setting'] = $this->setting_model->get();
        $certificate         = $this->halltickectgeneration_model->getcertificatebyid($certificate_id);
        $data['certificate'] = $certificate;
        $data['subid'] = $subjectgrpId;
        $hallsubgrp          = $this->halltickectgeneration_model->halltickectsubjects($subjectgrpId);
        $data['hallsubgrp']  = $hallsubgrp;

        foreach ($student_array as $key => $value) {
            $std_arr[] = $value->student_id;
        }
        $data['students'] = $this->student_model->getStudentsByArray($std_arr);
        foreach ($data['students'] as $key => $value) {
            $data['students'][$key]->name = $this->customlib->getFullName($value->firstname, $value->middlename, $value->lastname, $this->sch_setting_detail->middlename, $this->sch_setting_detail->lastname);
        }

        $data['sch_setting'] = $this->sch_setting_detail;
        $certificates        = $this->load->view('admin/halltickectgeneration/printcertificate', $data, true);
        echo $certificates;

    }
    


    // subject
    public function check_subjectcode($str){
        $this->load->database();

        $subcode = $this->security->xss_clean($str);
        
        // Replace 'your_table_name' with the actual name of your database table
        $query = $this->db->get_where('halltickectsubjects', array('subject_code' => $subcode));
        
        if ($query->num_rows() > 0) {
            $this->form_validation->set_message('check_subjectcode', $this->lang->line('record_already_exist'));
            return false;
        }
        return true;
    }

    public function subcreate()
    {
        // if (!$this->rbac->hasPrivilege('results_subject_branch', 'can_add')) {
        //     access_denied();
        // }
        $data['title']        = $this->lang->line('add_category');
        $category_result      = $this->halltickectgeneration_model->getsubjects();
        $data['categorylist'] = $category_result;
        $this->form_validation->set_rules('category', $this->lang->line('category'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('subjectcode', $this->lang->line('subjectcode'), 'trim|required|xss_clean|callback_check_subjectcode');
        
        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/halltickectgeneration/subjects', $data);
            $this->load->view('layout/footer', $data);
        } else {

            $data = array(
                'name' => $this->input->post('category'),
                'subject_code' => strtoupper($this->input->post('subjectcode'))
            );
            $this->halltickectgeneration_model->subjectadd($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
            redirect('admin/halltickectgeneration/subcreate/');

        }


    }


    public function subedit($id)
    {
        // if (!$this->rbac->hasPrivilege('results_subject_branch', 'can_add')) {
        //     access_denied();
        // }
        $data['title']        = $this->lang->line('add_category');
        $category_result      = $this->halltickectgeneration_model->getsubjects();
        $data['categorylist'] = $category_result;
        $data['id'] = $id;

       
        $category = $this->halltickectgeneration_model->getsub($id);
        $data['category'] = $category;

        $this->form_validation->set_rules('category', $this->lang->line('category'), 'trim|required|xss_clean');
        // $this->form_validation->set_rules('subjectcode', $this->lang->line('subjectcode'), 'trim|required|xss_clean|callback_check_subjectcode');
        $this->form_validation->set_rules('subjectcode', $this->lang->line('subjectcode'), 'trim|required|xss_clean');
        
        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/halltickectgeneration/subjectsedit', $data);
            $this->load->view('layout/footer', $data);
        } else {

            $data = array(
                'id' => $id,
                'name' => $this->input->post('category'),
                'subject_code' => strtoupper($this->input->post('subjectcode'))
            );
            $this->halltickectgeneration_model->subjectadd($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
            redirect('admin/halltickectgeneration/subcreate/');
            
        }


    }

    public function subdel($id){
        $this->halltickectgeneration_model->subremove($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('delete_message') . '</div>');
        redirect('admin/halltickectgeneration/subcreate');
    }




    // subject group
    public function subgrpcreate()
    {
        // if (!$this->rbac->hasPrivilege('results_subject_branch', 'can_add')) {
        //     access_denied();
        // }
        $data['title']        = $this->lang->line('add_category');
        $category_result      = $this->halltickectgeneration_model->getsubjectgrp();
        $data['categorylist'] = $category_result;
        $this->form_validation->set_rules('category', $this->lang->line('category'), 'trim|required|xss_clean');
        
        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/halltickectgeneration/subjectgroup', $data);
            $this->load->view('layout/footer', $data);
        } else {

            $data = array(
                'name' => $this->input->post('category'),
            );
            $this->halltickectgeneration_model->subjectgrpadd($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
            redirect('admin/halltickectgeneration/subgrpcreate/');

        }


    }

    public function subgrpedit($id)
    {
        // if (!$this->rbac->hasPrivilege('results_subject_branch', 'can_add')) {
        //     access_denied();
        // }
        $data['title']        = $this->lang->line('add_category');
        $category_result      = $this->halltickectgeneration_model->getsubjectgrp();
        $data['categorylist'] = $category_result;
        $data['id'] = $id;

       
        $category = $this->halltickectgeneration_model->getgrpsub($id);
        $data['category'] = $category;

        $this->form_validation->set_rules('category', $this->lang->line('category'), 'trim|required|xss_clean');
        
        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/halltickectgeneration/subjectgroupedit', $data);
            $this->load->view('layout/footer', $data);
        } else {

            $data = array(
                'id' => $id,
                'name' => $this->input->post('category'),
            );
            $this->halltickectgeneration_model->subjectgrpadd($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
            redirect('admin/halltickectgeneration/subgrpcreate/');
            
        }


    }

    public function subgrpdel($id){
        $this->halltickectgeneration_model->subgrpremove($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('delete_message') . '</div>');
        redirect('admin/halltickectgeneration/subgrpcreate');
    }









    // subject combo
    public function subgroupcombo(){

        $data['title']        = $this->lang->line('fees_master_list');
        $feegroup             = $this->halltickectgeneration_model->getsubjectgrp();
        $data['feegroupList'] = $feegroup;
        $feetype              = $this->halltickectgeneration_model->getsubjects();
        $data['feetypeList']  = $feetype;

        $feegroup_result       = $this->halltickectgeneration_model->get_subject_groups();
        $data['feemasterList'] = $feegroup_result;

        $this->form_validation->set_rules(
            'fee_groups_id', $this->lang->line('subject_group'), array(
                'required',
                array('check_exists', array($this->halltickectgeneration_model, 'valid_check_exists')),
            )
        );

        $this->form_validation->set_rules('feetype_id', $this->lang->line('subjects'), 'required');
        $this->form_validation->set_rules('due_date', $this->lang->line('due_date'), 'required');
        $this->form_validation->set_rules('start_time', $this->lang->line('start_time'), 'required');
        $this->form_validation->set_rules('end_time', $this->lang->line('end_time'), 'required');
        $this->form_validation->set_rules('max_marks', $this->lang->line('max_marks'), 'required');
        $this->form_validation->set_rules('min_marks', $this->lang->line('min_marks'), 'required');

        if ($this->form_validation->run() == false) {

        } else {
            
            $insert_array = array(
                'subjectgrp_id'   => $this->input->post('fee_groups_id'),
                'subject_id'      => $this->input->post('feetype_id'),
                'date'        => $this->customlib->dateFormatToYYYYMMDD($this->input->post('due_date')),
                'starttime'      => $this->input->post('start_time'),
                'endtime'       => $this->input->post('end_time'),
                'maxmark' => $this->input->post('max_marks'),
                'minmark'     => $this->input->post('min_marks'),
                'is_active' => 1,
            );

            $feegroup_result = $this->halltickectgeneration_model->addsubcombo($insert_array);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
            redirect('admin/halltickectgeneration/subgroupcombo');
        }

        $this->load->view('layout/header', $data);
        $this->load->view('admin/halltickectgeneration/feemasterList', $data);
        $this->load->view('layout/footer', $data);

    }



    public function subgroupcomboedit($id){

        $data['title']        = $this->lang->line('fees_master_list');

        $feegroup             = $this->halltickectgeneration_model->getsubjectgrp();
        $data['feegroupList'] = $feegroup;

        $feetype              = $this->halltickectgeneration_model->getsubjects();
        $data['feetypeList']  = $feetype;

        $feegroup_result       = $this->halltickectgeneration_model->get_subject_groups();
        $data['feemasterList'] = $feegroup_result;

        $feegroup_type = $this->halltickectgeneration_model->getsingle($id);
        $data['feegroup_type'] = $feegroup_type;

        $this->form_validation->set_rules('fee_groups_id', $this->lang->line('subject_group'), 'required');

        $this->form_validation->set_rules('feetype_id', $this->lang->line('subjects'), 'required');
        $this->form_validation->set_rules('due_date', $this->lang->line('due_date'), 'required');
        $this->form_validation->set_rules('start_time', $this->lang->line('start_time'), 'required');
        $this->form_validation->set_rules('end_time', $this->lang->line('end_time'), 'required');
        $this->form_validation->set_rules('max_marks', $this->lang->line('max_marks'), 'required');
        $this->form_validation->set_rules('min_marks', $this->lang->line('min_marks'), 'required');

        if ($this->form_validation->run() == false) {
            
        } else {

             $insert_array = array(
                'id' => $id,
                'subjectgrp_id'   => $this->input->post('fee_groups_id'),
                'subject_id'      => $this->input->post('feetype_id'),
                'date'        => $this->customlib->dateFormatToYYYYMMDD($this->input->post('due_date')),
                'starttime'      => $this->input->post('start_time'),
                'endtime'       => $this->input->post('end_time'),
                'maxmark' => $this->input->post('max_marks'),
                'minmark'     => $this->input->post('min_marks'),
                'is_active' => 1,
            );

            $feegroup_result = $this->halltickectgeneration_model->addsubcombo($insert_array);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
            redirect('admin/halltickectgeneration/subgroupcombo');
        }

        $this->load->view('layout/header', $data);
        $this->load->view('admin/halltickectgeneration/feemasterEdit', $data);
        $this->load->view('layout/footer', $data);

    }

    public function subgroupcombodel($id){

        $data['title'] = $this->lang->line('');
        $this->halltickectgeneration_model->subjectgrpadd($data);
        $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
        redirect('admin/halltickectgeneration/subgroupcombo');

    }


    public function deletecombogrp($id)
    {
        $data['title'] = $this->lang->line('fees_master_list');
        $this->halltickectgeneration_model->delcomgrp($id);
        redirect('admin/halltickectgeneration/subgroupcombo');
    }

    public function deletecomboitem($id)
    {
        // if (!$this->rbac->hasPrivilege('fees_master', 'can_delete')) {
        //     access_denied();
        // }

        $data['title'] = $this->lang->line('fees_master_list');
        $this->halltickectgeneration_model->comboremove($id);
        redirect('admin/halltickectgeneration/subgroupcombo');

    }





}



