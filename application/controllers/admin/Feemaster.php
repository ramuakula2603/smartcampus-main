<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Feemaster extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->sch_setting_detail = $this->setting_model->getSetting();
    }

    public function index()
    {

        $this->session->set_userdata('top_menu', 'Fees Collection');
        $this->session->set_userdata('sub_menu', 'admin/feemaster');
		
        $data['title']        = $this->lang->line('fees_master_list');
        $feegroup             = $this->feegroup_model->get();
        $data['feegroupList'] = $feegroup;
        $feetype              = $this->feetype_model->get();
        $data['feetypeList']  = $feetype;
        $feegroup_result       = $this->feesessiongroup_model->getFeesByGroup(null,0);
        $data['feemasterList'] = $feegroup_result;
        $class                = $this->class_model->get();
        $data['classlist']    = $class;

        $this->form_validation->set_rules('feetype_id', $this->lang->line('fee_type'), 'required');
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'required|numeric');

        $this->form_validation->set_rules(
            'fee_groups_id', $this->lang->line('fee_group'), array(
                'required',
                array('check_exists', array($this->feesessiongroup_model, 'valid_check_exists')),
            )
        );

        if (isset($_POST['account_type']) && $_POST['account_type'] == 'fix') {
            $this->form_validation->set_rules('fine_amount', $this->lang->line('fix_amount'), 'required|numeric');
            $this->form_validation->set_rules('due_date', $this->lang->line('due_date'), 'trim|required|xss_clean');

        } elseif (isset($_POST['account_type']) && ($_POST['account_type'] == 'percentage')) {
            $this->form_validation->set_rules('fine_percentage', $this->lang->line('percentage'), 'required|numeric');
            $this->form_validation->set_rules('fine_amount', $this->lang->line('fix_amount'), 'required|numeric');
            $this->form_validation->set_rules('due_date', $this->lang->line('due_date'), 'trim|required|xss_clean');
        }

        if ($this->form_validation->run() == false) {

        } else {
            
            if($this->input->post('fine_amount')){
                $fine_amount    =   convertCurrencyFormatToBaseAmount($this->input->post('fine_amount'));
            }else{
                $fine_amount    = '';
            }
            
            $insert_array = array(
                'fee_groups_id'   => $this->input->post('fee_groups_id'),
                'feetype_id'      => $this->input->post('feetype_id'),
                'amount'          => convertCurrencyFormatToBaseAmount($this->input->post('amount')),
                'due_date'        => $this->customlib->dateFormatToYYYYMMDD($this->input->post('due_date')),
                'session_id'      => $this->setting_model->getCurrentSession(),
                'fine_type'       => $this->input->post('account_type'),
                'fine_percentage' => $this->input->post('fine_percentage'),
                'fine_amount'     => $fine_amount,
            );

            $feegroup_result = $this->feesessiongroup_model->add($insert_array);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
            redirect('admin/feemaster/index');
        }

        $this->load->view('layout/header', $data);
        $this->load->view('admin/feemaster/feemasterList', $data);
        $this->load->view('layout/footer', $data);
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('fees_master', 'can_delete')) {
            access_denied();
        }
        $data['title'] = $this->lang->line('fees_master_list');
        $this->feegrouptype_model->remove($id);
        redirect('admin/feemaster/index');
    }

    public function saveDefaultClassFee()
    {
        if (!$this->rbac->hasPrivilege('fees_master', 'can_add')) {
            echo json_encode(['status' => 'error', 'message' => 'Access denied']);
            return;
        }

        $class_id = $this->input->post('class_id');
        $fee_group_id = $this->input->post('fee_group_id');
        $edit_id = $this->input->post('edit_id');

        if (empty($class_id) || empty($fee_group_id)) {
            echo json_encode(['status' => 'error', 'message' => 'Class and Fee Group are required']);
            return;
        }

        // Check if combination already exists
        if (empty($edit_id)) {
            // For new records, check if combination exists
            $existing = $this->db->get_where('default_class_fees', [
                'class_id' => $class_id,
                'fee_group_id' => $fee_group_id
            ])->row();

            if ($existing) {
                echo json_encode(['status' => 'error', 'message' => 'This combination already exists']);
                return;
            }
        } else {
            // For updates, check if combination exists excluding current record
            $this->db->where('class_id', $class_id);
            $this->db->where('fee_group_id', $fee_group_id);
            $this->db->where('id !=', $edit_id);
            $existing = $this->db->get('default_class_fees')->row();

            if ($existing) {
                echo json_encode(['status' => 'error', 'message' => 'This combination already exists']);
                return;
            }
        }

        if (!empty($edit_id)) {
            // Update existing record
            $data = [
                'class_id' => $class_id,
                'fee_group_id' => $fee_group_id,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            $this->db->where('id', $edit_id);
            $result = $this->db->update('default_class_fees', $data);
        } else {
            // Insert new record
            $data = [
                'class_id' => $class_id,
                'fee_group_id' => $fee_group_id,
                'created_at' => date('Y-m-d H:i:s')
            ];
            $result = $this->db->insert('default_class_fees', $data);
        }

        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Default class fee saved successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to save default class fee']);
        }
    }

    public function getDefaultClassFeeList()
    {
        $this->db->select('dcf.*, c.class as class_name, fg.name as fee_group_name');
        $this->db->from('default_class_fees dcf');
        $this->db->join('classes c', 'c.id = dcf.class_id');
        $this->db->join('fee_groups fg', 'fg.id = dcf.fee_group_id');
        $this->db->order_by('c.class, fg.name');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            echo json_encode(['status' => 'success', 'data' => $query->result_array()]);
        } else {
            echo json_encode(['status' => 'success', 'data' => []]);
        }
    }

    public function getDefaultClassFee($id)
    {
        $this->db->select('dcf.*, c.class as class_name, fg.name as fee_group_name');
        $this->db->from('default_class_fees dcf');
        $this->db->join('classes c', 'c.id = dcf.class_id');
        $this->db->join('fee_groups fg', 'fg.id = dcf.fee_group_id');
        $this->db->where('dcf.id', $id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            echo json_encode(['status' => 'success', 'data' => $query->row_array()]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Record not found']);
        }
    }

    public function deleteDefaultClassFee($id)
    {
        if (!$this->rbac->hasPrivilege('fees_master', 'can_delete')) {
            echo json_encode(['status' => 'error', 'message' => 'Access denied']);
            return;
        }

        $this->db->where('id', $id);
        $result = $this->db->delete('default_class_fees');

        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Default class fee deleted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete default class fee']);
        }
    }

    public function getDefaultFeeGroupsByClass($class_id)
    {
        // Get default fee session groups for a specific class
        // We need to get fee_session_groups.id because that's what the student form uses
        $current_session = $this->setting_model->getCurrentSession();

        $this->db->select('fsg.id as fee_session_group_id, fg.name as fee_group_name');
        $this->db->from('default_class_fees dcf');
        $this->db->join('fee_groups fg', 'fg.id = dcf.fee_group_id');
        $this->db->join('fee_session_groups fsg', 'fsg.fee_groups_id = dcf.fee_group_id');
        $this->db->where('dcf.class_id', $class_id);
        $this->db->where('fsg.session_id', $current_session);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            echo json_encode(['status' => 'success', 'data' => $query->result_array()]);
        } else {
            echo json_encode(['status' => 'success', 'data' => []]);
        }
    }

    public function deletegrp($id)
    {
        $data['title'] = $this->lang->line('fees_master_list');
        $this->feesessiongroup_model->remove($id);
        redirect('admin/feemaster');
    }

    public function edit($id)
    {
        if (!$this->rbac->hasPrivilege('fees_master', 'can_edit')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Fees Collection');
        $this->session->set_userdata('sub_menu', 'admin/feemaster');
        $data['id']            = $id;
        $feegroup_type         = $this->feegrouptype_model->get($id);
        $data['feegroup_type'] = $feegroup_type;
        $feegroup              = $this->feegroup_model->get();
        $data['feegroupList']  = $feegroup;
        $feetype               = $this->feetype_model->get();
        $data['feetypeList']   = $feetype;
        $feegroup_result       = $this->feesessiongroup_model->getFeesByGroup(null,0);
        $data['feemasterList'] = $feegroup_result;
        $class                 = $this->class_model->get();
        $data['classlist']     = $class;
        $this->form_validation->set_rules('feetype_id', $this->lang->line('fee_type'), 'required');
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'required|numeric');
        $this->form_validation->set_rules(
            'fee_groups_id', $this->lang->line('fee_group'), array(
                'required',
                array('check_exists', array($this->feesessiongroup_model, 'valid_check_exists')),
            )
        );

        if (isset($_POST['account_type']) && $_POST['account_type'] == 'fix') {
            $this->form_validation->set_rules('fine_amount', $this->lang->line('fix_amount'), 'required|numeric');
            $this->form_validation->set_rules('due_date', $this->lang->line('due_date'), 'trim|required|xss_clean');
        } elseif (isset($_POST['account_type']) && ($_POST['account_type'] == 'percentage')) {
            $this->form_validation->set_rules('fine_percentage', $this->lang->line('percentage'), 'required|numeric');
            $this->form_validation->set_rules('fine_amount', $this->lang->line('fix_amount'), 'required|numeric');
            $this->form_validation->set_rules('due_date', $this->lang->line('due_date'), 'trim|required|xss_clean');
        }
        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/feemaster/feemasterEdit', $data);
            $this->load->view('layout/footer', $data);
        } else {
            
            if($this->input->post('fine_amount')){
                $fine_amount    =   convertCurrencyFormatToBaseAmount($this->input->post('fine_amount'));
            }else{
                $fine_amount    = '';
            }
            
            $insert_array = array(
                'id'              => $this->input->post('id'),
                'feetype_id'      => $this->input->post('feetype_id'),
                'due_date'        => $this->customlib->dateFormatToYYYYMMDD($this->input->post('due_date')),
                'amount'          => convertCurrencyFormatToBaseAmount($this->input->post('amount')),
                'fine_type'       => $this->input->post('account_type'),
                'fine_percentage' => $this->input->post('fine_percentage'),
                'fine_amount'     => $fine_amount,
            );

            $feegroup_result = $this->feegrouptype_model->add($insert_array);

            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('update_message') . '</div>');
            redirect('admin/feemaster/index');
        }
    }

    public function assign($id)
    {
        if (!$this->rbac->hasPrivilege('fees_group_assign', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Fees Collection');
        $this->session->set_userdata('sub_menu', 'admin/feemaster');
        $data['id']              = $id;
        $data['title']           = $this->lang->line('student_fees');
        $class                   = $this->class_model->get();
        $data['classlist']       = $class;
        $feegroup_result         = $this->feesessiongroup_model->getFeesByGroup($id);
        $data['feegroupList']    = $feegroup_result;
        $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;
        $data['sch_setting']     = $this->sch_setting_detail;
        $genderList            = $this->customlib->getGender();
        $data['genderList']    = $genderList;
        $RTEstatusList         = $this->customlib->getRteStatus();
        $data['RTEstatusList'] = $RTEstatusList;

        $category             = $this->category_model->get();
        $data['categorylist'] = $category;

        if ($this->input->server('REQUEST_METHOD') == 'POST') {

            $data['category_id'] = $this->input->post('category_id');
            $data['gender']      = $this->input->post('gender');
            $data['rte_status']  = $this->input->post('rte');
            $data['class_id']    = $this->input->post('class_id');
            $data['section_id']  = $this->input->post('section_id');

            $resultlist         = $this->studentfeemaster_model->searchAssignFeeByClassSection($data['class_id'], $data['section_id'], $id, $data['category_id'], $data['gender'], $data['rte_status']);
            $data['resultlist'] = $resultlist;
        }

        $this->load->view('layout/header', $data);
        $this->load->view('admin/feemaster/assign', $data);
        $this->load->view('layout/footer', $data);
    }

}
