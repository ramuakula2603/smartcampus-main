<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Admino extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->library('Customlib');
        $this->load->library('media_storage');
        $this->load->model('student_model');
        $this->sch_setting_detail = $this->setting_model->getSetting();
    }


    public function index()
    {
        if (!$this->rbac->hasPrivilege('add_admisno', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Certificate');
        $this->session->set_userdata('sub_menu', 'admin/generateidcard');

        $class                   = $this->class_model->get();
        $data['classlist']       = $class;
        $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;
        $data['sch_setting']     = $this->sch_setting_detail;

        // $idcardlist              = $this->Generateidcard_model->getstudentidcard();
        // $data['idcardlist']      = $idcardlist;

        $progresslist            = $this->customlib->checkadminstatus();
        $data['progresslist']    = $progresslist;

        $button                  = $this->input->post('search');
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/hallticket/admissionnocheck', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $class   = $this->input->post('class_id');
            $section = $this->input->post('section_id');
            $search  = $this->input->post('search');

            $admistatus   = $this->input->post('progress_id');
            if($admistatus=="withadmissionno"){
                $vall=1;
            }
            if($admistatus=="noadmissionno"){
                $vall=0;
            }
            // $id_card = $this->input->post('id_card');
            if (isset($search)) {
                $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
                $this->form_validation->set_rules('progress_id', $this->lang->line('admi_status'), 'trim|required|xss_clean');

                //$this->form_validation->set_rules('id_card', $this->lang->line('id_card_template'), 'trim|required|xss_clean');
                if ($this->form_validation->run() == false) {

                } else {
                    $data['searchby']     = "filter";
                    $data['class_id']     = $this->input->post('class_id');
                    $data['section_id']   = $this->input->post('section_id');
                    // $id_card              = $this->input->post('id_card');
                    // $idcardResult         = $this->Generateidcard_model->getidcardbyid($id_card);
                    // $data['idcardResult'] = $idcardResult;
                    $resultlist           = $this->student_model->admissionnostatusgetDatatableByClassSection($class, $section,$vall);
                    $data['resultlist']   = $resultlist;

                }
            }

            $this->load->view('layout/header', $data);
            $this->load->view('admin/hallticket/admissionnocheck', $data);
            $this->load->view('layout/footer', $data);
        }
    }

    public function search()
    {
        if (!$this->rbac->hasPrivilege('add_admisno', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Certificate');
        $this->session->set_userdata('sub_menu', 'admin/generateidcard');

        $class                   = $this->class_model->get();
        $data['classlist']       = $class;
        $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;
        $data['sch_setting']     = $this->sch_setting_detail;

        // $idcardlist              = $this->Generateidcard_model->getstudentidcard();
        // $data['idcardlist']      = $idcardlist;

        $progresslist            = $this->customlib->checkadminstatus();
        $data['progresslist']    = $progresslist;

        $button                  = $this->input->post('search');
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/hallticket/admissionnocheck', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $class   = $this->input->post('class_id');
            $section = $this->input->post('section_id');
            $search  = $this->input->post('search');
            $admistatus   = $this->input->post('progress_id');
            if($admistatus=="withadmissionno"){
                $vall=1;
            }
            if($admistatus=="noadmissionno"){
                $vall=0;
            }
            // $id_card = $this->input->post('id_card');
            if (isset($search)) {
                $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
                $this->form_validation->set_rules('progress_id', $this->lang->line('admi_status'), 'trim|required|xss_clean');

                //$this->form_validation->set_rules('id_card', $this->lang->line('id_card_template'), 'trim|required|xss_clean');
                if ($this->form_validation->run() == false) {

                } else {
                    $data['searchby']     = "filter";
                    $data['class_id']     = $this->input->post('class_id');
                    $data['section_id']   = $this->input->post('section_id');
                    // $id_card              = $this->input->post('id_card');
                    // $idcardResult         = $this->Generateidcard_model->getidcardbyid($id_card);
                    // $data['idcardResult'] = $idcardResult;
                    $resultlist           = $this->student_model->admissionnostatusgetDatatableByClassSection($class, $section,$vall);
                    $data['resultlist']   = $resultlist;

                }
            }

            $this->load->view('layout/header', $data);
            $this->load->view('admin/hallticket/admissionnocheck', $data);
            $this->load->view('layout/footer', $data);
        }
    }





    public function addadmino()
    {

        $studentid = $this->input->post('studentid');
        $this->form_validation->set_rules('admi_no', $this->lang->line('admi_no'), 'required|trim|xss_clean|callback_check_student_admi_no_exists');

        if ($this->form_validation->run() == false) {

            $data = array(
                'admi_no' => form_error('admi_no'),
            );
            $array = array('status' => 'fail', 'error' => $data);
            echo json_encode($array);

        } else {
            // Validation passed, now check if admi_no exists in the database
            $admi_no = $this->input->post('admi_no');
            $data = array(
                'admi_no' => $admi_no,
                'admi_status'=>1,
                'student_id'=>$studentid,
            );

            $s = $this->student_model->admi_no_update($data, $studentid);
            if($s){
                $array = array('status' => 'success');
                echo json_encode($array);
            }else{
                $array = array('status' => 'fail');
                echo json_encode($array);
            }



        }
    }




    public function check_student_admi_no_exists($str)
    {
        // Load the database library if it's not already loaded
        $this->load->database();

        $admi_no = $this->security->xss_clean($str);

        // Replace 'your_table_name' with the actual name of your database table
        $query = $this->db->get_where('student_admi', array('admi_no' => $admi_no));

        if ($query->num_rows() > 0) {
            $this->form_validation->set_message('check_student_admi_no_exists', $this->lang->line('record_already_exist'));
            return false;
        }

        return true;
    }


    // public function addadmino()
    // {
    //     $this->form_validation->set_rules('admi_no', $this->lang->line('admi_no'), 'required|trim|xss_clean');

    //     if ($this->form_validation->run() == false) {
    //         $data = array(
    //             'admi_no' => form_error('admi_no'),
    //         );
    //         $array = array('status' => 'fail', 'error' => $data);
    //         echo json_encode($array);
    //     } else {
    //         // Validation passed, perform your desired action here

    //         $array = array('status' => 'success');
    //         echo json_encode($array);
    //     }
    // }



    // public function addadmino()
    // {
    //     $this->form_validation->set_rules('admi_no', $this->lang->line('fee_master'), 'required|trim|xss_clean');

    //     if ($this->form_validation->run() == false) {
    //         $data = array(
    //             'admi_no'                 => form_error('admi_no'),
    //         );
    //         $array = array('status' => 'fail', 'error' => $data);
    //         echo json_encode($array);

    //     }else{

    //     }
    // }





    public function getadmino()
    {
        $studentId = $this->input->post('studentid');

        // Call a function in the student_model to fetch the admission number
        $admissionNumber = $this->student_model->getAdmissionNumber($studentId);

        if ($admissionNumber !== false) {
            // If admission number is found, send it as JSON response
            $response = array('status' => 'success', 'admi_no' => $admissionNumber);
            echo json_encode($response);
        } else {
            // If admission number is not found, send an error response
            $response = array('status' => 'fail', 'error_message' => 'Admission number not found.');
            echo json_encode($response);
        }
    }




    public function searching()
    {
        if (!$this->rbac->hasPrivilege('add_admisno', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Admission No');
        $this->session->set_userdata('sub_menu', 'admin/hallticket/searching');
        $data['title'] = $this->lang->line('fees_collection');

        $this->form_validation->set_rules('admission_no', $this->lang->line('admission_no'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {

        } else {
            $admission_no = $this->input->post('admission_no');

            // Get student details by admission number
            $student_id = $this->student_model->getuseridfromadminotable($admission_no);

            // Add debug information
            $data['debug_info'] = array(
                'admission_no' => $admission_no,
                'student_id' => $student_id
            );

            // Check if student exists in student_admi table
            $this->db->select('*');
            $this->db->from('student_admi');
            $this->db->where('admi_no', $admission_no);
            $query_check = $this->db->get();
            $data['debug_info']['admi_records'] = $query_check->result_array();

            // If not found in student_admi, try to find in students table
            if (!$student_id) {
                $this->db->select('id');
                $this->db->from('students');
                $this->db->where('admission_no', $admission_no);
                $query_students = $this->db->get();
                $data['debug_info']['students_table'] = $query_students->result_array();

                if ($query_students->num_rows() > 0) {
                    $student_id = $query_students->row()->id;
                    $data['debug_info']['student_id_from_students_table'] = $student_id;
                }
            }

            if ($student_id) {
                // Get student details
                $studentDetails = $this->student_model->get($student_id);

                // For student session details, we need to get the current class and section
                $this->db->select('student_session.*, classes.class, sections.section');
                $this->db->from('student_session');
                $this->db->join('classes', 'classes.id = student_session.class_id');
                $this->db->join('sections', 'sections.id = student_session.section_id');
                $this->db->where('student_session.student_id', $student_id);
                $this->db->where('student_session.session_id', $this->current_session);
                $query = $this->db->get();
                $student_session = $query->result_array();

                $data['studentDetails'] = $studentDetails;
                $data['student_session'] = $student_session;
                $data['admission_no'] = $admission_no;

                // Add more debug info
                $data['debug_info']['student_details'] = $studentDetails ? 'Found' : 'Not Found';
                $data['debug_info']['student_session_count'] = count($student_session);
            } else {
                $data['studentDetails'] = array();
                $data['student_session'] = array();
            }
        }
        $data['sch_setting'] = $this->sch_setting_detail;

        $this->load->view('layout/header', $data);
        $this->load->view('admin/hallticket/searching', $data);
        $this->load->view('layout/footer', $data);
    }






}
