<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
ob_start();
class Feesdiscountapproval extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->sch_setting_detail = $this->setting_model->getSetting();
        $this->load->model('staff_model');
        $this->load->model('addaccount_model');
        $this->load->model('studentfee_model');
        $this->load->model('session_model');
        $this->load->model('feediscount_model');
        $this->load->model('student_model');
        $this->load->model('class_model');
        $this->load->model('section_model');
        $this->load->model('studentfeemaster_model');
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('generate_certificate', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Certificate');
        $this->session->set_userdata('sub_menu', 'admin/generatecertificate');

        $certificateList         = $this->feediscount_model->get();
        $data['certificateList'] = $certificateList;

        $progresslist            = $this->customlib->getProgress();
        $data['progresslist']    = $progresslist;

        $class                   = $this->class_model->get();
        $data['classlist']       = $class;

        // Load session data for dropdown
        $sessionlist             = $this->session_model->get();
        $data['sessionlist']     = $sessionlist;

        $this->load->view('layout/header', $data);
        $this->load->view('admin/feediscount/feesdiscountapproval', $data);
        $this->load->view('layout/footer', $data);

    }


    public function search()
    {
        $this->session->set_userdata('top_menu', 'Certificate');
        $this->session->set_userdata('sub_menu', 'admin/generatecertificate');

        $class                   = $this->class_model->get();
        $data['classlist']       = $class;

        $progresslist            = $this->customlib->getProgress();
        $data['progresslist']    = $progresslist;

        $certificateList         = $this->feediscount_model->get();
        $data['certificateList'] = $certificateList;

        // Load session data for dropdown
        $sessionlist             = $this->session_model->get();
        $data['sessionlist']     = $sessionlist;

        // Always show the search form - AJAX will handle data loading
        $this->load->view('layout/header', $data);
        $this->load->view('admin/feediscount/feesdiscountapproval', $data);
        $this->load->view('layout/footer', $data);
    }

    public function dtfeesdiscountlist()
    {
        // Enhanced error logging and debugging
        error_log('=== FEES DISCOUNT DATATABLE REQUEST STARTED ===');
        error_log('POST data: ' . print_r($_POST, true));
        error_log('Request method: ' . $_SERVER['REQUEST_METHOD']);

        $currency_symbol = $this->customlib->getSchoolCurrencyFormat();
        $class_id        = $this->input->post('class_id');
        $section_id      = $this->input->post('section_id');
        $session_id      = $this->input->post('session_id');
        $progress_id     = $this->input->post('progress_id');

        // Enhanced debug logging
        error_log('Fees Discount DataTable - Raw input: class_id=' . print_r($class_id, true) . ', section_id=' . print_r($section_id, true));
        log_message('debug', 'Fees Discount DataTable - Raw input: class_id=' . print_r($class_id, true) . ', section_id=' . print_r($section_id, true));

        // Handle both single and multi-select values properly
        if (!is_array($class_id)) {
            $class_id = !empty($class_id) ? array($class_id) : array();
        }
        if (!is_array($section_id)) {
            $section_id = !empty($section_id) ? array($section_id) : array();
        }

        // Remove empty values from arrays
        $class_id = array_filter($class_id, function($value) { return !empty($value); });
        $section_id = array_filter($section_id, function($value) { return !empty($value); });

        error_log('Fees Discount DataTable - Processed: class_id=' . print_r($class_id, true) . ', section_id=' . print_r($section_id, true));
        log_message('debug', 'Fees Discount DataTable - Processed: class_id=' . print_r($class_id, true) . ', section_id=' . print_r($section_id, true));

        $sch_setting = $this->sch_setting_detail;

        try {
            // Get the discount approval data (pass null for certificate_id since we removed it)
            $resultlist = $this->student_model->searchByClassSectionAnddiscountStatus($class_id, null, $section_id, $progress_id, $session_id);

            error_log('Fees Discount DataTable - Model result count: ' . count($resultlist));
            error_log('Fees Discount DataTable - Model result preview: ' . print_r(array_slice($resultlist, 0, 2), true));

        } catch (Exception $e) {
            error_log('Fees Discount DataTable - Exception: ' . $e->getMessage());
            $resultlist = array();
        }

        $dt_data = array();

        if (!empty($resultlist)) {
            foreach ($resultlist as $student) {
                $viewbtn = '';
                $approvebtn = '';
                $disapprovebtn = '';
                $revertbtn = '';

                // View button (explore button)
                $viewbtn = "<a href='" . base_url() . "student/view/" . $student['id'] . "' class='btn btn-default btn-xs' data-toggle='tooltip' title='" . $this->lang->line('view') . "'><i class='fa fa-reorder'></i></a>";

                // Action buttons based on approval status
                if ($student['approval_status'] == 0) { // Pending
                    $approvebtn = "<span style='margin-right:3px; cursor:pointer;' class='label label-success approve-btn' data-toggle='modal' data-target='#confirm-approved' data-studentid='" . $student['fdaid'] . "'>Approve</span>";
                    $disapprovebtn = "<span style='cursor:pointer;' class='label label-danger disapprove-btn' data-studentid='" . $student['fdaid'] . "' data-toggle='modal' data-target='#confirm-delete'>Disapprove</span>";
                } elseif ($student['approval_status'] == 1) { // Approved
                    $revertbtn = "<button class='btn btn-default btn-xs' data-toggle='modal' data-target='#confirm-retrive' title='" . $this->lang->line('revert') . "' data-studentid='" . $student['fdaid'] . "' data-paymentid='" . $student['payment_id'] . "'><i class='fa fa-undo'></i></button>";
                }

                $row = array();

                // Checkbox for bulk operations (only for pending status)
                $checkbox = '';
                if ($student['approval_status'] == 0) {
                    $checkbox = "<input type='checkbox' class='checkbox center-block' name='check' data-student_id='" . $student['id'] . "' value='" . $student['id'] . "'>";
                }
                $row[] = $checkbox;

                $row[] = $student['admission_no'];
                $row[] = "<a href='" . base_url() . "student/view/" . $student['id'] . "'>" . $this->customlib->getFullName($student['firstname'], $student['middlename'], $student['lastname'], $sch_setting->middlename, $sch_setting->lastname) . "</a>";
                $row[] = $student['class'] . "(" . $student['section'] . ")";
                $row[] = $student['father_name'];

                // Format date of birth
                $dob = '';
                if ($student['dob'] != '' && $student['dob'] != '0000-00-00') {
                    $dob = date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($student['dob']));
                }
                $row[] = $dob;

                $row[] = $this->lang->line(strtolower($student['gender']));
                $row[] = $student['category'];
                $row[] = $student['mobileno'];
                $row[] = $student['fgrname']; // Fee group name
                $row[] = $student['amount']; // Discount amount

                // Discount note column
                $discount_note = !empty($student['discount_note']) ? $student['discount_note'] : '-';
                $row[] = $discount_note;

                // Status column
                $status = '';
                if ($student['approval_status'] == 0) {
                    $status = "<span class='label label-warning'>" . $this->lang->line('pending') . "</span>";
                } elseif ($student['approval_status'] == 1) {
                    $status = "<span class='label label-success'>" . $this->lang->line('approved') . "</span>";
                } elseif ($student['approval_status'] == 2) {
                    $status = "<span class='label label-danger'>" . $this->lang->line('rejected') . "</span>";
                }
                $row[] = $status;

                // Action column
                $actions = $viewbtn . ' ' . $approvebtn . ' ' . $disapprovebtn . ' ' . $revertbtn;
                $row[] = $actions;

                $dt_data[] = $row;
            }
        }

        $json_data = array(
            "draw"            => intval($this->input->post('draw')),
            "recordsTotal"    => count($resultlist),
            "recordsFiltered" => count($resultlist),
            "data"            => $dt_data,
        );

        error_log('Fees Discount DataTable - Final JSON data count: ' . count($dt_data));

        // Set proper JSON header
        header('Content-Type: application/json');
        echo json_encode($json_data);
    }

    public function searchvalidation()
    {
        $search_type = $this->input->post('search_type');

        if ($search_type == "search_filter") {
            // No mandatory validation for flexible search
            $array = array('status' => 1, 'error' => '');
            echo json_encode($array);
        } else {
            $class_id    = $this->input->post('class_id');
            $section_id  = $this->input->post('section_id');
            $session_id  = $this->input->post('session_id');
            $progress_id = $this->input->post('progress_id');

            $params = array(
                'class_id' => $class_id,
                'section_id' => $section_id,
                'session_id' => $session_id,
                'progress_id' => $progress_id
            );

            $array = array('status' => 1, 'error' => '', 'params' => $params);
            echo json_encode($array);
        }
    }

    public function generate($student, $class, $certificate)
    {
        $certificateResult         = $this->Generatecertificate_model->getcertificatebyid($certificate);
        $data['certificateResult'] = $certificateResult;
        $resultlist                = $this->student_model->searchByClassStudent($class, $student);
        $data['resultlist']        = $resultlist;

        $this->load->view('admin/certificate/transfercertificate', $data);
    }

    public function generatemultiple()
    {

        $studentid           = $this->input->post('data');
        $student_array       = json_decode($studentid);
        $certificate_id      = $this->input->post('certificate_id');
        $class               = $this->input->post('class_id');
        foreach ($student_array as $key => $value) {
            $item['student_session_id']=$value->student_id;
            $item['fees_discount_id']=$certificate_id;
            $temp=$this->feediscount_model->allotdiscount($item);
            $this->feediscount_model->updateapprovalstatus($certificate_id,$value->student_id,1);
        }
        
        redirect('admin/feesdiscountapproval/index');

    }


    public function dismissapprovalgeneratemultiple()
    {

        $studentid           = $this->input->post('data');
        $student_array       = json_decode($studentid);
        $certificate_id      = $this->input->post('certificate_id');
        $class               = $this->input->post('class_id');
        foreach ($student_array as $key => $value) {
            $this->feediscount_model->updateapprovalstatus($certificate_id,$value->student_id,2);
        }
        
        redirect('admin/feesdiscountapproval/index');
        
    }


    public function dismissapprovalsingle()
    {
        try {
            $studentid = $this->input->post('dataa');

            // Validate input
            if (empty($studentid)) {
                $response = array('status' => 'fail', 'message' => 'Student ID is required');
                echo json_encode($response);
                return;
            }

            // Log the request
            error_log("Dismissing approval for student ID: " . $studentid);

            // Update approval status to rejected (2)
            $update_result = $this->feediscount_model->updateapprovalstatus($studentid, 2);

            if ($update_result) {
                error_log("Successfully dismissed approval for student ID: " . $studentid);
                $response = array('status' => 'success', 'message' => 'Discount request rejected successfully');
            } else {
                error_log("Failed to dismiss approval for student ID: " . $studentid);
                $response = array('status' => 'fail', 'message' => 'Failed to update approval status');
            }

        } catch (Exception $e) {
            error_log("Error in dismissapprovalsingle: " . $e->getMessage());
            $response = array('status' => 'fail', 'message' => 'An error occurred while processing the request');
        }

        // Send the response
        echo json_encode($response);
    }


    public function retrive()
    {
        try {
            $studentid = $this->input->post('dataa');
            $paymentid = $this->input->post('certificate_id');

            // Validate input
            if (empty($studentid)) {
                $response = array('status' => 'fail', 'message' => 'Student ID is required');
                echo json_encode($response);
                return;
            }

            // Log the request
            error_log("Reverting approval for student ID: " . $studentid . ", Payment ID: " . $paymentid);

            // Get the discount approval record before reverting
            $discount_record = $this->feediscount_model->getapproval($studentid);
            if (empty($discount_record)) {
                error_log("Discount record not found for student ID: " . $studentid);
                $response = array('status' => 'fail', 'message' => 'Discount record not found');
                echo json_encode($response);
                return;
            }

            // Delete fee record if payment ID exists (this removes the discount from student's payment history)
            if (!empty($paymentid)) {
                $parts = explode('/', $paymentid);
                if (count($parts) >= 2) {
                    $this->deleteFee($parts[0], $parts[1]);
                    error_log("Deleted fee record for invoice: " . $parts[0] . "/" . $parts[1]);
                } else {
                    error_log("Invalid payment ID format: " . $paymentid);
                }
            }

            // Clear payment ID (set to NULL) and update approval status to pending (0)
            $dataa = array(
                'id' => $studentid,
                'payment_id' => null, // Clear the payment ID
                'approval_status' => 0 // Set back to pending
            );
            $update_result = $this->feediscount_model->updatepaymentid($dataa);

            if (!$update_result) {
                error_log("Failed to update discount record for student ID: " . $studentid);
                $response = array('status' => 'fail', 'message' => 'Failed to update discount record');
                echo json_encode($response);
                return;
            }

            error_log("Successfully reverted approval for student ID: " . $studentid);
            $response = array('status' => 'success', 'message' => 'Discount approval reverted successfully. The discount has been removed from student fees and status changed to pending.');

        } catch (Exception $e) {
            error_log("Error in retrive: " . $e->getMessage());
            $response = array('status' => 'fail', 'message' => 'An error occurred while reverting the approval');
        }

        // Send the response
        echo json_encode($response);
    }

    public function deleteFee($invoice_id,$sub_invoice)
    {
        
        if (!empty($invoice_id)) {
            $this->studentfee_model->remove($invoice_id, $sub_invoice);
            $this->addaccount_model->transcationremove($invoice_id . '/' . $sub_invoice,'fees');
        }
        
    }

  

    
    public function approvalsingle()
    {
        try {
            $studentid = $this->input->post('dataa');

            // Validate input
            if (empty($studentid)) {
                $response = array('status' => 'fail', 'message' => 'Student ID is required');
                echo json_encode($response);
                return;
            }

            // Log the request
            error_log("Approving discount for student ID: " . $studentid);

            // Update the approval status in the database using your model
            $update_result = $this->feediscount_model->updateapprovalstatus($studentid, 1);

            if (!$update_result) {
                error_log("Failed to update approval status for student ID: " . $studentid);
                $response = array('status' => 'fail', 'message' => 'Failed to update approval status');
                echo json_encode($response);
                return;
            }

            $approval_data = $this->feediscount_model->getapproval($studentid);

            if (empty($approval_data)) {
                error_log("No approval data found for student ID: " . $studentid);
                $response = array('status' => 'fail', 'message' => 'Approval data not found');
                echo json_encode($response);
                return;
            }

            $staff_record = $this->staff_model->get($this->customlib->getStaffID());

            $collected_by = $this->customlib->getAdminSessionUserName() . "(" . $staff_record['employee_id'] . ")";
            $json_array = array(
                'amount'          => convertCurrencyFormatToBaseAmount(0),
                'amount_discount' => convertCurrencyFormatToBaseAmount($approval_data['amount']),
                'amount_fine'     => convertCurrencyFormatToBaseAmount(0),
                'date'            => $approval_data['date'],
                'description'     => $approval_data['description'],
                'collected_by'    => $collected_by,
                'payment_mode'    => 'Cash',
                'received_by'     => $staff_record['id'],
            );

            $data = array(
                'fee_category'           => 'fees',
                'student_fees_master_id' => $approval_data['student_fees_master_id'],
                'fee_groups_feetype_id'  => $approval_data['fee_groups_feetype_id'],
                'amount_detail'          => $json_array,
            );

            $inserted_id = $this->studentfeemaster_model->fee_deposit($data,'','');
            $receipt_data1 = json_decode($inserted_id);

            if ($receipt_data1 && isset($receipt_data1->invoice_id)) {
                $dataa = array(
                    'id' => $studentid,
                    'payment_id' => $receipt_data1->invoice_id . '/' . $receipt_data1->sub_invoice_id,
                );
                $update_resultt = $this->feediscount_model->updatepaymentid($dataa);

                error_log("Successfully approved discount for student ID: " . $studentid);
                $response = array('status' => 'success', 'message' => 'Discount approved successfully');
            } else {
                error_log("Failed to create fee deposit for student ID: " . $studentid);
                $response = array('status' => 'fail', 'message' => 'Failed to create fee deposit');
            }

        } catch (Exception $e) {
            error_log("Error in approvalsingle: " . $e->getMessage());
            $response = array('status' => 'fail', 'message' => 'An error occurred while processing the approval');
        }

        // Send the response
        echo json_encode($response);
    }




    


    public function addstudentfee()
    {

        $studentid=$this->input->post('student_session_id');
        $fee_groups_feetype_id  = $this->input->post('fee_groups_feetype_id');

        $temp =$this->feediscount_model->getfeetypeid($studentid,$fee_groups_feetype_id);


        $staff_record = $this->staff_model->get($this->customlib->getStaffID());
        $collected_by             = $this->customlib->getAdminSessionUserName() . "(" . $staff_record['employee_id'] . ")";
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


        
        
        
        $student_fees_master_id = $temp['id'];
        $transport_fees_id      = $this->input->post('transport_fees_id');


        $data = array(
            
            'student_fees_master_id' => $student_fees_master_id,
            'fee_groups_feetype_id'  => $fee_groups_feetype_id,
            'amount_detail'          => $json_array,
        );

    
        
        $send_to            = $this->input->post('guardian_phone');
        // $email              = $this->input->post('guardian_email');
        // $parent_app_key     = $this->input->post('parent_app_key');
        // $student_session_id = $this->input->post('student_session_id');
        $inserted_id        = $this->studentfeemaster_model->discount_fee_deposit($data, $send_to, $student_fees_discount_id);

        
        echo json_encode(['status' => 'success', 'message' => $inserted_id]);
        exit();
    }

    /**
     * AJAX method to get sections for multiple selected classes
     */
    public function getClassSections()
    {
        $class_ids = $this->input->post('class_ids');

        if (empty($class_ids)) {
            echo json_encode(array());
            return;
        }

        // Ensure class_ids is an array
        if (!is_array($class_ids)) {
            $class_ids = array($class_ids);
        }

        $result = array();

        foreach ($class_ids as $class_id) {
            if (!empty($class_id)) {
                $sections = $this->section_model->getClassBySection($class_id);
                $result[$class_id] = $sections;
            }
        }

        echo json_encode($result);
    }
}


?>






