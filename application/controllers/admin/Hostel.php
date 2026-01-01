<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Hostel extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->library('Customlib');
        $this->load->model(array("hostelfee_model", "hostelroom_model", "studenthostelfee_model"));
        $this->sch_setting_detail = $this->setting_model->getSetting();
        $this->load->library("datatables");
    }

    public function index()
    {

        if (!$this->rbac->hasPrivilege('hostel', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Hostel');
        $this->session->set_userdata('sub_menu', 'hostel/index');
        $listhostel         = $this->hostel_model->listhostel();
        $data['listhostel'] = $listhostel;
        $ght                = $this->customlib->getHostaltype();
        $data['ght']        = $ght;
        $this->load->view('layout/header');
        $this->load->view('admin/hostel/createhostel', $data);
        $this->load->view('layout/footer');
    }

    public function create()
    {
        if (!$this->rbac->hasPrivilege('hostel', 'can_add')) {
            access_denied();
        }
        $data['title'] = 'Add Library';
        $this->form_validation->set_rules('hostel_name', $this->lang->line('hostel_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('type', $this->lang->line('type'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $listhostel         = $this->hostel_model->listhostel();
            $data['listhostel'] = $listhostel;
            $ght                = $this->customlib->getHostaltype();
            $data['ght']        = $ght;
            $this->load->view('layout/header');
            $this->load->view('admin/hostel/createhostel', $data);
            $this->load->view('layout/footer');
        } else {
            $data = array(
                'hostel_name' => $this->input->post('hostel_name'),
                'type'        => $this->input->post('type'),
                'address'     => $this->input->post('address'),
                'intake'      => $this->input->post('intake'),
                'description' => $this->input->post('description'),
            );
            $this->hostel_model->addhostel($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
            redirect('admin/hostel/index');
        }
    }

    public function edit($id)
    {
        if (!$this->rbac->hasPrivilege('hostel', 'can_edit')) {
            access_denied();
        }
        $data['title']      = 'Add Hostel';
        $data['id']         = $id;
        $edithostel         = $this->hostel_model->get($id);
        $data['edithostel'] = $edithostel;
        $ght                = $this->customlib->getHostaltype();
        $data['ght']        = $ght;
        $this->form_validation->set_rules('hostel_name', $this->lang->line('hostel_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('type', $this->lang->line('type'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $listhostel         = $this->hostel_model->listhostel();
            $data['listhostel'] = $listhostel;
            $this->load->view('layout/header');
            $this->load->view('admin/hostel/edithostel', $data);
            $this->load->view('layout/footer');
        } else {
            $data = array(
                'id'          => $this->input->post('id'),
                'hostel_name' => $this->input->post('hostel_name'),
                'type'        => $this->input->post('type'),
                'address'     => $this->input->post('address'),
                'intake'      => $this->input->post('intake'),
                'description' => $this->input->post('description'),
            );
            $this->hostel_model->addhostel($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('update_message') . '</div>');
            redirect('admin/hostel/index');
        }
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('hostel', 'can_delete')) {
            access_denied();
        }
        $data['title'] = 'Fees Master List';
        $this->hostel_model->remove($id);
        redirect('admin/hostel/index');
    }

    public function feemaster()
    {
        if (!($this->rbac->hasPrivilege('hostel_fees_master', 'can_view'))) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Hostel');
        $this->session->set_userdata('sub_menu', 'hostel/feemaster');
        $current_session               = $this->setting_model->getCurrentSession();
        $data                          = array();
        $month_list                    = $this->customlib->getMonthDropdown($this->sch_setting_detail->start_month);

        $data['title']                 = 'hostel fees';
        $data['month_list']            = $month_list;

         $month_list= $this->customlib->getMonthDropdown($this->sch_setting_detail->start_month);
        $data['hostelfees'] = array();
        foreach($month_list as $key=>$value){
            $hostel_fee_data = $this->hostelfee_model->hostelfesstype($current_session,$value);
            if($hostel_fee_data && isset($hostel_fee_data->id)) {
                $data['hostelfees'][] = (array)$hostel_fee_data;
            }
        }

        $hostel_room_id         = $this->input->post('hostel_room_id');
        $data['hostel_room_id'] = $hostel_room_id;
        $hostel_room            = $this->hostelroom_model->get($hostel_room_id);
        $data['hostel_room']    = $hostel_room;

        if ($this->input->server('REQUEST_METHOD') == "POST") {

            $rows = $this->input->post('rows[]');

            $insert_data = array();
            $update_data = array();

            foreach ($rows as $row_key => $row_value) {

                $fine_amount = 0;
                if ($this->input->post('fine_type_' . $row_value) == "fix") {
                    $fine_amount = convertCurrencyFormatToBaseAmount($this->input->post('fine_amount_' . $row_value));
                }

                if ($this->input->post('prev_id_' . $row_value) != 0) {

                    $update_array                    = array();
                    $update_array['id']              = $this->input->post('prev_id_' . $row_value);
                    $update_array['month']           = $this->input->post('month_' . $row_value);
                    $update_array['due_date']        = $this->customlib->dateFormatToYYYYMMDD($this->input->post('due_date_' . $row_value));
                    $update_array['fine_type']       = $this->input->post('fine_type_' . $row_value);
                    $update_array['fine_percentage'] = empty2null($this->input->post('percentage_' . $row_value));
                    $update_array['fine_amount']     = $fine_amount;
                    $update_array['session_id']      = $current_session;
                    $update_data[]                   = $update_array;

                } else {

                    $new_insert                    = array();
                    $new_insert['month']           = $this->input->post('month_' . $row_value);
                    $new_insert['due_date']        = $this->customlib->dateFormatToYYYYMMDD($this->input->post('due_date_' . $row_value));
                    $new_insert['fine_type']       = $this->input->post('fine_type_' . $row_value);
                    $new_insert['fine_percentage'] = empty2null($this->input->post('percentage_' . $row_value));
                    $new_insert['fine_amount']     = $fine_amount;
                    $new_insert['session_id']      = $current_session;
                    $insert_data[]                 = $new_insert;
                }

            }

            $this->hostelfee_model->add($insert_data, $update_data);
            $this->session->set_flashdata('msg', $this->lang->line('success_message'));
            redirect('admin/hostel/feemaster');
        }

        $this->load->view('layout/header');
        $this->load->view('admin/hostel/feemaster', $data);
        $this->load->view('layout/footer');
    }

    public function assignhostelfee()
    {
        if (!($this->rbac->hasPrivilege('assign_hostel_fees', 'can_view'))) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Hostel');
        $this->session->set_userdata('sub_menu', 'hostel/assignhostelfee');

        $data['title'] = 'Assign Hostel Fees';
        $class = $this->class_model->get();
        $data['classlist'] = $class;

        // FIXED: Get all sessions for selection
        $this->load->model('session_model');
        $sessions = $this->session_model->get();
        $data['sessionlist'] = $sessions;
        $data['current_session'] = $this->setting_model->getCurrentSession();

        $userdata = $this->customlib->getUserData();
        $carray = array();

        if (!empty($data["classlist"])) {
            foreach ($data["classlist"] as $ckey => $cvalue) {
                $carray[] = $cvalue["id"];
            }
        }

        $this->load->view('layout/header', $data);
        $this->load->view('admin/hostel/assignhostelfee', $data);
        $this->load->view('layout/footer');
    }

    public function assignhostelfeestudent()
    {
        $class_id = $this->input->post('class_id');
        $section_id = $this->input->post('section_id');
        $hostel_room_id = $this->input->post('hostel_room_id');
        $session_id = $this->input->post('session_id'); // FIXED: Get selected session

        $data = array();
        $resultlist = $this->student_model->searchByClassSection($class_id, $section_id);
        $data['resultlist'] = $resultlist;
        $data['class_id'] = $class_id;
        $data['section_id'] = $section_id;
        $data['hostel_room_id'] = $hostel_room_id;

        // FIXED: Use selected session or default to current session
        if (empty($session_id)) {
            $session_id = $this->setting_model->getCurrentSession();
        }

        // Get hostel fees for the selected session
        $hostelfees = $this->hostelfee_model->getBySession($session_id);

        $data['hostelfees'] = $hostelfees;
        $data['selected_session'] = $session_id;

        $this->load->view('admin/hostel/_assignhostelfeestudent', $data);
    }

    public function assignhostelfeepost()
    {
        $student_session_id = $this->input->post('student_session_id');
        $hostel_room_id = $this->input->post('hostel_room_id');
        $hostel_fees = $this->input->post('hostel_fees');
        $remove_fees = $this->input->post('remove_fees');
        $session_id = $this->input->post('session_id'); // FIXED: Get selected session

        $data_insert = array();
        $remove_ids = array();

        if (!empty($hostel_fees)) {
            foreach ($hostel_fees as $key => $value) {
                $data_insert[] = array(
                    'hostel_feemaster_id' => $value,
                    'student_session_id' => $student_session_id,
                    'hostel_room_id' => $hostel_room_id,
                    'generated_by' => $this->customlib->getStaffID()
                );
            }
        }

        if (!empty($remove_fees)) {
            $remove_ids = $remove_fees;
        }

        // FIXED: Use selected session or default to current session
        if (empty($session_id)) {
            $session_id = $this->setting_model->getCurrentSession();
        }

        $this->load->model('studenthostelfee_model');
        $result = $this->studenthostelfee_model->add($data_insert, $student_session_id, $remove_ids, $hostel_room_id, $session_id);

        if ($result) {
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        } else {
            $array = array('status' => 'fail', 'error' => '', 'message' => $this->lang->line('something_went_wrong'));
        }

        echo json_encode($array);
    }

    public function getStudentHostelFees()
    {
        $student_session_id = $this->input->post('student_session_id');
        $hostel_room_id = $this->input->post('hostel_room_id');
        $session_id = $this->input->post('session_id'); // FIXED: Get selected session

        $assigned_fees = array();

        if ($student_session_id && $hostel_room_id) {
            // FIXED: Pass session_id to get fees for specific session
            $hostel_fees = $this->studenthostelfee_model->getStudentHostelFeesBySession($student_session_id, $hostel_room_id, $session_id);

            foreach ($hostel_fees as $fee) {
                $assigned_fees[] = $fee->hostel_feemaster_id;
            }
        }

        $response = array(
            'status' => 'success',
            'assigned_fees' => $assigned_fees
        );

        echo json_encode($response);
    }

}
