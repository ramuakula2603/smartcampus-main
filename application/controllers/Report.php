<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Report extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->time               = strtotime(date('d-m-Y H:i:s'));
        $this->payment_mode       = $this->customlib->payment_mode();
        $this->search_type        = $this->customlib->get_searchtype();
        $this->sch_setting_detail = $this->setting_model->getSetting();
        $this->load->library('media_storage');
    }

    public function pdfStudentFeeRecord()
    {
        $data                    = [];
        $class_id                = $this->uri->segment(3);
        $section_id              = $this->uri->segment(4);
        $student_id              = $this->uri->segment(5);
        $student                 = $this->student_model->get($student_id);
        $setting_result          = $this->setting_model->get();
        $data['settinglist']     = $setting_result;
        $data['student']         = $student;
        $student_due_fee         = $this->studentfee_model->getDueFeeBystudent($class_id, $section_id, $student_id);
        $data['student_due_fee'] = $student_due_fee;
        $html                    = $this->load->view('reports/students_detail', $data, true);
        $pdfFilePath             = $this->time . ".pdf";
        $this->fontdata          = array(
            "opensans" => array(
                'R'  => "OpenSans-Regular.ttf",
                'B'  => "OpenSans-Bold.ttf",
                'I'  => "OpenSans-Italic.ttf",
                'BI' => "OpenSans-BoldItalic.ttf",
            ),
        );
        $this->load->library('m_pdf');
        $this->m_pdf->pdf->WriteHTML($html);
        $this->m_pdf->pdf->Output($pdfFilePath, "D");
    }

    public function pdfByInvoiceNo()
    {
        $data                    = [];
        $invoice_id              = $this->uri->segment(3);
        $setting_result          = $this->setting_model->get();
        $data['settinglist']     = $setting_result;
        $student_due_fee         = $this->studentfee_model->getFeeByInvoice($invoice_id);
        $data['student_due_fee'] = $student_due_fee;
        $html                    = $this->load->view('reports/pdfinvoiceno', $data, true);
        $pdfFilePath             = $this->time . ".pdf";
        $this->load->library('m_pdf');
        $this->m_pdf->pdf->WriteHTML($html);
        $this->m_pdf->pdf->Output($pdfFilePath, "D");
    }

    public function pdfDepositeFeeByStudent($id)
    {
        $data                        = [];
        $data['title']               = 'Student Detail';
        $student                     = $this->student_model->get($id);
        $setting_result              = $this->setting_model->get();
        $data['settinglist']         = $setting_result;
        $student_fee_history         = $this->studentfee_model->getStudentFees($id);
        $data['student_fee_history'] = $student_fee_history;
        $data['student']             = $student;
        $array                       = array();
        $feecategory                 = $this->feecategory_model->get();
        foreach ($feecategory as $key => $value) {
            $dataarray            = array();
            $value_id             = $value['id'];
            $dataarray[$value_id] = $value['category'];
            $category             = $value['category'];
            $datatype             = array();
            $data_fee_type        = array();
            $feetype              = $this->feetype_model->getFeetypeByCategory($value['id']);
            foreach ($feetype as $feekey => $feevalue) {
                $ftype            = $feevalue['id'];
                $datatype[$ftype] = $feevalue['type'];
            }
            $data_fee_type[]      = $datatype;
            $dataarray[$category] = $datatype;
            $array[]              = $dataarray;
        }
        $data['category_array'] = $array;
        $data['feecategory']    = $feecategory;
        $html                   = $this->load->view('reports/pdfStudentDeposite', $data, true);
        $pdfFilePath            = $this->time . ".pdf";
        $this->load->library('m_pdf');
        $this->m_pdf->pdf->WriteHTML($html);
        $this->m_pdf->pdf->Output($pdfFilePath, "D");
    }

    public function pdfStudentListByText()
    {
        $data                = [];
        $search_text         = $this->uri->segment(3);
        $setting_result      = $this->setting_model->get();
        $data['settinglist'] = $setting_result;
        $resultlist          = $this->student_model->searchFullText($search_text);
        $data['resultlist']  = $resultlist;
        $html                = $this->load->view('reports/pdfStudentListByText', $data, true);
        $pdfFilePath         = $this->time . ".pdf";
        $this->load->library('m_pdf');
        $this->m_pdf->pdf->WriteHTML($html);
        $this->m_pdf->pdf->Output($pdfFilePath, "D");
    }

    public function marksreport()
    {
        $setting_result        = $this->setting_model->get();
        $data['settinglist']   = $setting_result;
        $exam_id               = $this->uri->segment(3);
        $class_id              = $this->uri->segment(4);
        $section_id            = $this->uri->segment(5);
        $data['exam_id']       = $exam_id;
        $data['class_id']      = $class_id;
        $data['section_id']    = $section_id;
        $exam_arrylist         = $this->exam_model->get($exam_id);
        $data['exam_arrylist'] = $exam_arrylist;
        $section               = $this->section_model->getClassNameBySection($class_id, $section_id);
        $data['class']         = $section;
        $examSchedule          = $this->examschedule_model->getDetailbyClsandSection($class_id, $section_id, $exam_id);
        $studentList           = $this->student_model->searchByClassSection($class_id, $section_id);
        $data['examSchedule']  = array();
        if (!empty($examSchedule)) {
            $new_array                      = array();
            $data['examSchedule']['status'] = "yes";
            foreach ($studentList as $stu_key => $stu_value) {
                $array                 = array();
                $array['student_id']   = $stu_value['id'];
                $array['roll_no']      = $stu_value['roll_no'];
                $array['firstname']    = $stu_value['firstname'];
                $array['lastname']     = $stu_value['lastname'];
                $array['admission_no'] = $stu_value['admission_no'];
                $array['dob']          = $stu_value['dob'];
                $array['father_name']  = $stu_value['father_name'];
                $x                     = array();
                foreach ($examSchedule as $ex_key => $ex_value) {
                    $exam_array                     = array();
                    $exam_array['exam_schedule_id'] = $ex_value['id'];
                    $exam_array['exam_id']          = $ex_value['exam_id'];
                    $exam_array['full_marks']       = $ex_value['full_marks'];
                    $exam_array['passing_marks']    = $ex_value['passing_marks'];
                    $exam_array['exam_name']        = $ex_value['name'];
                    $exam_array['exam_type']        = $ex_value['type'];
                    $student_exam_result            = $this->examresult_model->get_result($ex_value['id'], $stu_value['id']);
                    if (empty($student_exam_result)) {
                        $data['examSchedule']['status'] = "no";
                    } else {
                        $exam_array['attendence'] = $student_exam_result->attendence;
                        $exam_array['get_marks']  = $student_exam_result->get_marks;
                    }
                    $x[] = $exam_array;
                }
                $array['exam_array'] = $x;
                $new_array[]         = $array;
            }
            $data['examSchedule']['result'] = $new_array;
        } else {
            $s                    = array('status' => 'no');
            $data['examSchedule'] = $s;
        }
        $html        = $this->load->view('reports/marksreport', $data, true);
        $pdfFilePath = $this->time . ".pdf";
        $this->load->library('m_pdf');
        $this->m_pdf->pdf->WriteHTML($html);
        $this->m_pdf->pdf->Output($pdfFilePath, "D");
        $this->load->view('reports/marksreport', $data);
    }

    public function pdfStudentListByClassSection()
    {
        $data                = [];
        $class_id            = $this->uri->segment(3);
        $section_id          = $this->uri->segment(4);
        $setting_result      = $this->setting_model->get();
        $section             = $this->section_model->getClassNameBySection($class_id, $section_id);
        $data['class']       = $section;
        $data['settinglist'] = $setting_result;
        $resultlist          = $this->student_model->searchByClassSection($class_id, $section_id);
        $data['resultlist']  = $resultlist;
        $html                = $this->load->view('reports/pdfStudentListByClassSection', $data, true);
        $pdfFilePath         = $this->time . ".pdf";
        $this->load->library('m_pdf');
        $this->m_pdf->pdf->WriteHTML($html);
        $this->m_pdf->pdf->Output($pdfFilePath, "D");
    }

    public function pdfStudentListDifferentCriteria()
    {
        $data           = [];
        $class_id       = $this->input->get('class_id');
        $section_id     = $this->input->get('section_id');
        $category_id    = $this->input->get('category_id');
        $gender         = $this->input->get('gender');
        $rte            = $this->input->get('rte');
        $setting_result = $this->setting_model->get();
        $class          = $this->class_model->get($class_id);
        $data['class']  = $class;
        if ($section_id != "") {
            $section         = $this->section_model->getClassNameBySection($class_id, $section_id);
            $data['section'] = $section;
        }
        if ($gender != "") {
            $data['gender'] = $gender;
        }
        if ($rte != "") {
            $data['rte'] = $rte;
        }
        if ($category_id != "") {
            $category         = $this->category_model->get($category_id);
            $data['category'] = $category;
        }
        $data['settinglist'] = $setting_result;
        $resultlist          = $this->student_model->searchByClassSectionCategoryGenderRte($class_id, $section_id, $category_id, $gender, $rte);
        $data['resultlist']  = $resultlist;
        $html                = $this->load->view('reports/pdfStudentListDifferentCriteria', $data, true);
        $pdfFilePath         = $this->time . ".pdf";
        $this->load->library('m_pdf');
        $this->m_pdf->pdf->WriteHTML($html);
        $this->m_pdf->pdf->Output($pdfFilePath, "D");
    }

    public function pdfStudentListByClass()
    {
        $data                = [];
        $class_id            = $this->uri->segment(3);
        $section_id          = "";
        $setting_result      = $this->setting_model->get();
        $section             = $this->class_model->get($class_id);
        $data['class']       = $section;
        $data['settinglist'] = $setting_result;
        $resultlist          = $this->student_model->searchByClassSection($class_id, $section_id);
        $data['resultlist']  = $resultlist;
        $html                = $this->load->view('reports/pdfStudentListByClass', $data, true);
        $pdfFilePath         = $this->time . ".pdf";
        $this->load->library('m_pdf');
        $this->m_pdf->pdf->WriteHTML($html);
        $this->m_pdf->pdf->Output($pdfFilePath, "D");
    }

    public function transactionSearch()
    {
        $data                = [];
        $date_from           = $this->input->get('datefrom');
        $date_to             = $this->input->get('dateto');
        $setting_result      = $this->setting_model->get();
        $data['exp_title']   = 'Transaction From ' . $date_from . " To " . $date_to;
        $date_from           = date('Y-m-d', $this->customlib->datetostrtotime($date_from));
        $date_to             = date('Y-m-d', $this->customlib->datetostrtotime($date_to));
        $expenseList         = $this->expense_model->search("", $date_from, $date_to);
        $feeList             = $this->studentfee_model->getFeeBetweenDate($date_from, $date_to);
        $data['expenseList'] = $expenseList;
        $data['feeList']     = $feeList;
        $data['settinglist'] = $setting_result;
        $html                = $this->load->view('reports/transactionSearch', $data, true);
        $pdfFilePath         = $this->time . ".pdf";
        $this->load->library('m_pdf');
        $this->m_pdf->pdf->WriteHTML($html);
        $this->m_pdf->pdf->Output($pdfFilePath, "D");
    }

    public function pdfExamschdule()
    {
        $data                 = [];
        $setting_result       = $this->setting_model->get();
        $data['settinglist']  = $setting_result;
        $exam_id              = $this->uri->segment(3);
        $section_id           = $this->uri->segment(4);
        $class_id             = $this->uri->segment(5);
        $class                = $this->class_model->get($class_id);
        $data['class']        = $class;
        $examSchedule         = $this->examschedule_model->getDetailbyClsandSection($class_id, $section_id, $exam_id);
        $section              = $this->section_model->getClassNameBySection($class_id, $section_id);
        $data['section']      = $section;
        $data['examSchedule'] = $examSchedule;
        $exam                 = $this->exam_model->get($exam_id);
        $data['exam']         = $exam;
        $html                 = $this->load->view('reports/examSchedule', $data, true);
        $pdfFilePath          = $this->time . ".pdf";
        $this->load->library('m_pdf');
        $this->m_pdf->pdf->WriteHTML($html);
        $this->m_pdf->pdf->Output($pdfFilePath, "D");
    }

    public function get_betweendate($type)
    {
        $this->load->view('reports/betweenDate');
    }

    public function class_subject()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/student_information');
        $this->session->set_userdata('subsub_menu', 'Reports/student_information/class_subject_report');
        $data['title']       = 'Add Fees Type';
        $data['searchlist']  = $this->search_type;
        $class               = $this->class_model->get('', $classteacher = 'yes');
        $data['classlist']   = $class;
        $data['search_type'] = '';
        $data['class_id']    = $class_id    = $this->input->post('class_id');
        $data['section_id']  = $section_id  = $this->input->post('section_id');

        // Initialize section list - get all sections for initial load
        $data['section_list'] = array();

        // For initial page load, get all sections from all classes
        if (empty($class_id)) {
            // Get all sections for all classes
            $all_classes = $data['classlist'];
            foreach ($all_classes as $class) {
                $sections = $this->section_model->getClassBySection($class['id']);
                if (!empty($sections)) {
                    $data['section_list'] = array_merge($data['section_list'], $sections);
                }
            }
            // Remove duplicates based on section_id
            $unique_sections = array();
            $added_section_ids = array();
            foreach ($data['section_list'] as $section) {
                if (!in_array($section['section_id'], $added_section_ids)) {
                    $unique_sections[] = $section;
                    $added_section_ids[] = $section['section_id'];
                }
            }
            $data['section_list'] = $unique_sections;
        } else {
            if (is_array($class_id)) {
                // For multi-select, get sections for all selected classes
                foreach ($class_id as $single_class_id) {
                    $sections = $this->section_model->getClassBySection($single_class_id);
                    if (!empty($sections)) {
                        $data['section_list'] = array_merge($data['section_list'], $sections);
                    }
                }
                // Remove duplicates based on section_id
                $unique_sections = array();
                $added_section_ids = array();
                foreach ($data['section_list'] as $section) {
                    if (!in_array($section['section_id'], $added_section_ids)) {
                        $unique_sections[] = $section;
                        $added_section_ids[] = $section['section_id'];
                    }
                }
                $data['section_list'] = $unique_sections;
            } else {
                $data['section_list'] = $this->section_model->getClassBySection($class_id);
            }
        }

        // Only process form data if this is a POST request
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            // Remove required validation for flexible filtering - allow empty class/section selection
            $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|xss_clean');
            $this->form_validation->set_rules('section_id', $this->lang->line('section'), 'trim|xss_clean');

            if ($this->form_validation->run() == false) {
                $data['subjects'] = array();
            } else {
                // Handle multi-select arrays for class_id and section_id
                $class_ids = $this->input->post('class_id');
                $section_ids = $this->input->post('section_id');

                // Get subjects for all selected class/section combinations
                $data['resultlist'] = $this->subjecttimetable_model->getSubjectByClassandSection($class_ids, $section_ids);

                $subject = array();
                foreach ($data['resultlist'] as $value) {
                    $subject[$value->subject_id][] = $value;
                }

                $data['subjects'] = $subject;
            }
        } else {
            // For GET requests (initial page load), show empty results
            $data['subjects'] = array();
        }

        $this->load->view('layout/header', $data);
        $this->load->view('reports/class_subject', $data);
        $this->load->view('layout/footer', $data);

    }

    public function admission_report()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/student_information');
        $this->session->set_userdata('subsub_menu', 'Reports/student_information/admission_report');
        $data['title']           = 'Add Fees Type';
        $data['searchlist']      = $this->search_type;
        $data['sch_setting']     = $this->sch_setting_detail;
        $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;
        $searchterm              = '';
        $class                   = $this->class_model->get();
        $data['classlist']       = $class;
        $this->load->view('layout/header', $data);
        $this->load->view('reports/admission_report', $data);
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

    public function sibling_report()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/student_information');
        $this->session->set_userdata('subsub_menu', 'Reports/student_information/sibling_report');
        $data['title']           = 'Sibling Report';
        $data['searchlist']      = $this->search_type;
        $data['sch_setting']     = $this->sch_setting_detail;
        $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;
        $userdata                = $this->customlib->getUserData();
        $carray                  = array();

        $class                   = $this->class_model->get();
        $data['classlist']       = $class;

        if (!empty($data["classlist"])) {
            foreach ($data["classlist"] as $ckey => $cvalue) {
                $carray[] = $cvalue["id"];
            }
        }

        // Handle traditional POST form submission (for backward compatibility)
        $searchterm              = '';
        $condition               = array();
        $data['class_id']     = $class_id     = $this->input->post('class_id');
        $data['section_id']   = $section_id   = $this->input->post('section_id');
        $data['section_list'] = $this->section_model->getClassBySection($this->input->post('class_id'));

        // Convert single values to arrays for new multi-select support
        $class_id_array = null;
        $section_id_array = null;

        if (isset($_POST['class_id']) && $_POST['class_id'] != '') {
            $class_id_array = is_array($_POST['class_id']) ? $_POST['class_id'] : array($_POST['class_id']);
            $condition['classes.id'] = $_POST['class_id']; // Keep for legacy support
        }

        if (isset($_POST['section_id']) && $_POST['section_id'] != '') {
            $section_id_array = is_array($_POST['section_id']) ? $_POST['section_id'] : array($_POST['section_id']);
            $condition['sections.id'] = $_POST['section_id']; // Keep for legacy support
        }

        // Check if this is a form submission
        if ($this->input->post('search')) {
            // Use updated model methods with proper parameter structure
            // Pass class_id_array as $carray for class teacher restrictions and as $class_id for filtering
            $data['sibling_list'] = $this->student_model->sibling_reportsearch($searchterm, $class_id_array, $condition, $class_id_array, $section_id_array);

            $sibling_parent = array();
            foreach ($data['sibling_list'] as $value) {
                $sibling_parent[] = $value['parent_id'];
            }

            $data['resultlist'] = $this->student_model->sibling_report($searchterm, $class_id_array, $condition, $class_id_array, $section_id_array);
            $sibling = array();

            foreach ($data['resultlist'] as $value) {
                if (in_array($value['parent_id'], $sibling_parent)) {
                    $sibling[$value['parent_id']][] = $value;
                }
            }
            $data['resultlist'] = $sibling;
        } else {
            // Initialize empty data for initial page load
            $data['resultlist'] = array();
        }

        $this->load->view('layout/header', $data);
        $this->load->view('reports/sibling_report', $data);
        $this->load->view('layout/footer', $data);
    }

    public function siblingsearchvalidation()
    {
        // Enhanced error logging and debugging
        error_log('=== SIBLING SEARCH VALIDATION STARTED ===');
        error_log('POST data: ' . print_r($_POST, true));
        error_log('Request method: ' . $_SERVER['REQUEST_METHOD']);
        error_log('Content type: ' . (isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : 'not set'));

        // Handle multi-select values - convert to arrays if needed
        $class_id    = $this->input->post('class_id');
        $section_id  = $this->input->post('section_id');
        $srch_type = $this->input->post('search_type');

        // Enhanced debug logging
        error_log('Sibling search validation - Raw input: class_id=' . print_r($class_id, true) . ', section_id=' . print_r($section_id, true) . ', search_type=' . $srch_type);
        log_message('debug', 'Sibling search validation - Raw input: class_id=' . print_r($class_id, true) . ', section_id=' . print_r($section_id, true) . ', search_type=' . $srch_type);

        // Convert single values to arrays for consistency
        if (!is_array($class_id) && !empty($class_id)) {
            $class_id = array($class_id);
        }
        if (!is_array($section_id) && !empty($section_id)) {
            $section_id = array($section_id);
        }

        error_log('Sibling search validation - Processed: class_id=' . print_r($class_id, true) . ', section_id=' . print_r($section_id, true));
        log_message('debug', 'Sibling search validation - Processed: class_id=' . print_r($class_id, true) . ', section_id=' . print_r($section_id, true));

        try {
            if ($srch_type == 'search_filter') {
                // No mandatory validation - allow flexible report generation
                $params = array('srch_type' => $srch_type, 'class_id' => $class_id, 'section_id' => $section_id);
                $array  = array('status' => 1, 'error' => '', 'params' => $params);
                error_log('Sibling search validation - Success response: ' . json_encode($array));
                log_message('debug', 'Sibling search validation - Success response: ' . json_encode($array));

                // Set proper JSON header
                header('Content-Type: application/json');
                echo json_encode($array);
            } else {
                // Handle other search types like the Guardian Report does
                $params = array('srch_type' => 'search_full', 'class_id' => $class_id, 'section_id' => $section_id);
                $array  = array('status' => 1, 'error' => '', 'params' => $params);
                error_log('Sibling search validation - Full search response: ' . json_encode($array));
                log_message('debug', 'Sibling search validation - Full search response: ' . json_encode($array));

                // Set proper JSON header
                header('Content-Type: application/json');
                echo json_encode($array);
            }
        } catch (Exception $e) {
            error_log('Sibling search validation - Exception: ' . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(array('status' => 0, 'error' => array('general' => 'Server error occurred')));
        }
    }

    public function dtsiblingreportlist()
    {
        // Enhanced error logging and debugging
        error_log('=== SIBLING DATATABLE REQUEST STARTED ===');
        error_log('POST data: ' . print_r($_POST, true));
        error_log('Request method: ' . $_SERVER['REQUEST_METHOD']);

        $class_id   = $this->input->post('class_id');
        $section_id = $this->input->post('section_id');
        $sch_setting = $this->sch_setting_detail;

        // Enhanced debug logging
        error_log('Sibling DataTable - Raw input: class_id=' . print_r($class_id, true) . ', section_id=' . print_r($section_id, true));
        log_message('debug', 'Sibling DataTable - Raw input: class_id=' . print_r($class_id, true) . ', section_id=' . print_r($section_id, true));

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

        error_log('Sibling DataTable - Processed: class_id=' . print_r($class_id, true) . ', section_id=' . print_r($section_id, true));
        log_message('debug', 'Sibling DataTable - Processed: class_id=' . print_r($class_id, true) . ', section_id=' . print_r($section_id, true));

        try {
            error_log('Sibling DataTable - Calling existing model methods...');

            // Prepare condition array for section filtering (legacy support)
            $condition = array();
            if (!empty($section_id)) {
                if (is_array($section_id) && count($section_id) == 1) {
                    $condition['sections.id'] = $section_id[0];
                } elseif (!is_array($section_id)) {
                    $condition['sections.id'] = $section_id;
                }
                // For multiple sections, we'll rely on the new section_id parameter
            }

            // Use existing sibling_reportsearch method with proper parameters
            // $carray should contain class IDs for class teacher restrictions
            $sibling_list = $this->student_model->sibling_reportsearch('', $class_id, $condition, $class_id, $section_id);
            $sibling_parent = array();

            foreach ($sibling_list as $value) {
                $sibling_parent[] = $value['parent_id'];
            }

            error_log('Found sibling parent IDs: ' . print_r($sibling_parent, true));

            // Use existing sibling_report method with proper parameters
            $resultlist = $this->student_model->sibling_report('', $class_id, $condition, $class_id, $section_id);
            $sibling = array();

            // Group students by parent_id (only those with siblings)
            foreach ($resultlist as $value) {
                if (in_array($value['parent_id'], $sibling_parent)) {
                    $sibling[$value['parent_id']][] = $value;
                }
            }

            // Convert grouped data to DataTable format (array of arrays like Guardian Report)
            $dt_data = array();
            foreach ($sibling as $parent_id => $students) {
                if (count($students) > 1) { // Only include families with multiple children
                    $row = array();

                    // Use first student's parent info
                    $first_student = $students[0];

                    if ($sch_setting->father_name) {
                        $row[] = $first_student['father_name'];
                    }
                    if ($sch_setting->mother_name) {
                        $row[] = $first_student['mother_name'];
                    }
                    if ($sch_setting->guardian_name) {
                        $row[] = $first_student['guardian_name'];
                    }
                    if ($sch_setting->guardian_phone) {
                        $row[] = $first_student['guardian_phone'];
                    }

                    // Build student names and classes
                    $student_names = array();
                    $student_classes = array();
                    $admission_dates = array();

                    foreach ($students as $student) {
                        $full_name = $this->customlib->getFullName($student['firstname'], $student['middlename'], $student['lastname'], $sch_setting->middlename, $sch_setting->lastname);
                        $student_names[] = '<a href="' . base_url() . 'student/view/' . $student['id'] . '">' . $full_name . ' (' . $student['admission_no'] . ')</a>';
                        $student_classes[] = $student['class'] . ' (' . $student['section'] . ')';

                        if ($sch_setting->admission_date && !empty($student['admission_date']) && $student['admission_date'] != '0000-00-00') {
                            $admission_dates[] = date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($student['admission_date']));
                        } else {
                            $admission_dates[] = '';
                        }
                    }

                    $row[] = implode('<br/>', $student_names);
                    $row[] = implode('<br/>', $student_classes);

                    if ($sch_setting->admission_date) {
                        $row[] = implode('<br/>', $admission_dates);
                    }

                    $dt_data[] = $row;
                }
            }

            // Return DataTable JSON response in the same format as Guardian Report
            $json_data = array(
                "draw"            => intval($this->input->post('draw')),
                "recordsTotal"    => count($dt_data),
                "recordsFiltered" => count($dt_data),
                "data"            => $dt_data,
            );

            error_log('Sibling DataTable - Final JSON data: ' . print_r($json_data, true));
            error_log('Sibling DataTable - Data rows count: ' . count($dt_data));

            // Set proper JSON header and output result
            header('Content-Type: application/json');
            echo json_encode($json_data);

        } catch (Exception $e) {
            error_log('Sibling DataTable - Exception: ' . $e->getMessage());
            error_log('Sibling DataTable - Stack trace: ' . $e->getTraceAsString());

            // Return empty DataTable response on error
            header('Content-Type: application/json');
            echo json_encode(array(
                'draw' => intval($this->input->post('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => array(),
                'error' => 'Server error occurred'
            ));
        }
    }

    public function studentbookissuereport()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/library');
        $this->session->set_userdata('subsub_menu', 'Reports/library/book_issue_report');
        $data['searchlist'] = $this->customlib->get_searchtype();
        $data['members']    = array('' => $this->lang->line('all'), 'student' => $this->lang->line('student'), 'teacher' => $this->lang->line('teacher'));
        $this->load->view('layout/header', $data);
        $this->load->view('reports/studentBookIssueReport', $data);
        $this->load->view('layout/footer', $data);
    }

    public function bookduereport()
    {

        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/library');
        $this->session->set_userdata('subsub_menu', 'Reports/library/bookduereport');
        $data['searchlist']  = $this->customlib->get_searchtype();
        $data['sch_setting'] = $this->sch_setting_detail;
        $data['members']     = array('' => $this->lang->line('all'), 'student' => $this->lang->line('student'), 'teacher' => $this->lang->line('teacher'));
        $this->load->view('layout/header', $data);
        $this->load->view('reports/bookduereport', $data);
        $this->load->view('layout/footer', $data);
    }

    public function bookinventory()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/library');
        $this->session->set_userdata('subsub_menu', 'Reports/library/bookinventory');
        $data['searchlist'] = $this->customlib->get_searchtype();
        $this->load->view('layout/header', $data);
        $this->load->view('reports/bookinventory', $data);
        $this->load->view('layout/footer', $data);
    }

    public function feescollectionreport()
    {

        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/fees_collection');
        $this->session->set_userdata('subsub_menu', '');
        $this->load->view('layout/header');
        $this->load->view('reports/feescollectionreport');
        $this->load->view('layout/footer');
    }

    public function gerenalincomereport()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'reports/bookinventory');
        $data['searchlist'] = $this->customlib->get_searchtype();
        if (isset($_POST['search_type']) && $_POST['search_type'] != '') {

            $dates               = $this->customlib->get_betweendate($_POST['search_type']);
            $data['search_type'] = $_POST['search_type'];

        } else {

            $dates               = $this->customlib->get_betweendate('this_year');
            $data['search_type'] = '';

        }

        $start_date       = date('Y-m-d', strtotime($dates['from_date']));
        $end_date         = date('Y-m-d', strtotime($dates['to_date']));
        $data['label']    = date($this->customlib->getSchoolDateFormat(), strtotime($start_date)) . " " . $this->lang->line('to') . " " . date($this->customlib->getSchoolDateFormat(), strtotime($end_date));
        $listbook         = $this->book_model->bookinventory($start_date, $end_date);
        $data['listbook'] = $listbook;
        $this->load->view('layout/header', $data);
        $this->load->view('reports/gerenalincomereport', $data);
        $this->load->view('layout/footer', $data);
    }

    public function studentinformation()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/student_information');
        $this->session->set_userdata('subsub_menu', '');
        $this->load->view('layout/header');
        $this->load->view('reports/studentinformation');
        $this->load->view('layout/footer');
    }

    public function human_resource()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/human_resource');
        $this->session->set_userdata('subsub_menu', '');
        $this->load->view('layout/header');
        $this->load->view('reports/human_resource');
        $this->load->view('layout/footer');
    }

        

    public function library()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/library');
        $this->session->set_userdata('subsub_menu', '');
        $this->load->view('layout/header');
        $this->load->view('reports/library');
        $this->load->view('layout/footer');
    }

    public function inventory()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/inventory');
        $this->session->set_userdata('subsub_menu', '');
        $this->load->view('layout/header');
        $this->load->view('reports/inventory');
        $this->load->view('layout/footer');
    }

    public function result()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/result');
        $this->session->set_userdata('subsub_menu', '');
        $this->load->view('layout/header');
        $this->load->view('reports/result');
        $this->load->view('layout/footer');
    }

    public function internal_result()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/result');
        $this->session->set_userdata('subsub_menu', 'Reports/result/internal_result');
        
        // Load the internal result model
        $this->load->model('internalresult_model');
        
        // Load necessary data for dropdowns
        $data['title'] = 'Internal Result Report';
        $session_result = $this->session_model->get();
        $data['sessionlist'] = $session_result;
        $data['classlist'] = $this->class_model->get();
        $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;
        $data['sch_setting'] = $this->sch_setting_detail;
        
        // Initialize exam type list (will be populated based on selected session)
        $data['examtypelist'] = array();
        
        // Status options
        $data['statuslist'] = array(
            'all' => 'All',
            'pass' => 'Pass',
            'fail' => 'Fail',
            'absent' => 'Absent'
        );
        
        // Initialize form validation data
        $data['selected_session'] = '';
        $data['selected_exam_type'] = '';
        $data['selected_class'] = '';
        $data['selected_section'] = '';
        $data['selected_status'] = 'all';
        $data['results'] = array();
        
        // Handle form submission
        if ($this->input->post('search')) {
            $data['selected_session'] = $this->input->post('session_id');
            $data['selected_exam_type'] = $this->input->post('exam_type_id');
            $data['selected_class'] = $this->input->post('class_id');
            $data['selected_section'] = $this->input->post('section_id');
            $data['selected_status'] = $this->input->post('status');
            
            // Get exam types for selected session
            if (!empty($data['selected_session'])) {
                $data['examtypelist'] = $this->internalresult_model->getExamTypes($data['selected_session']);
            }
            
            // Build filters array - convert single values to arrays for the model
            $filters = array(
                'session_id' => !empty($data['selected_session']) ? array($data['selected_session']) : null,
                'exam_type_id' => !empty($data['selected_exam_type']) ? array($data['selected_exam_type']) : null,
                'class_id' => !empty($data['selected_class']) ? array($data['selected_class']) : null,
                'section_id' => !empty($data['selected_section']) ? array($data['selected_section']) : null,
                'status' => $data['selected_status']
            );
            
            // Fetch results
            $data['results'] = $this->internalresult_model->getInternalResultsReport($filters);
        } else {
            // On initial load, get exam types for current session
            $data['examtypelist'] = $this->internalresult_model->getExamTypes($this->current_session);
            $data['selected_session'] = $this->current_session;
        }
        
        $this->load->view('layout/header', $data);
        $this->load->view('reports/internal_result', $data);
        $this->load->view('layout/footer', $data);
    }

    /**
     * AJAX method to get exam types based on selected session
     */
    public function getExamTypesBySession()
    {
        $this->load->model('internalresult_model');
        $session_id = $this->input->post('session_id');
        
        if (!empty($session_id)) {
            $exam_types = $this->internalresult_model->getExamTypes($session_id);
            echo json_encode($exam_types);
        } else {
            echo json_encode(array());
        }
    }

    public function external_result()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/result');
        $this->session->set_userdata('subsub_menu', 'Reports/result/external_result');
        
        // Load the external result model
        $this->load->model('externalresult_model');
        
        // Load necessary data for dropdowns
        $data['title'] = 'External Result Report';
        $session_result = $this->session_model->get();
        $data['sessionlist'] = $session_result;
        $data['classlist'] = $this->class_model->get();
        $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;
        $data['sch_setting'] = $this->sch_setting_detail;
        
        // Initialize exam type list (will be populated based on selected session)
        $data['examtypelist'] = array();
        
        // Status options
        $data['statuslist'] = array(
            'all' => 'All',
            'pass' => 'Pass',
            'fail' => 'Fail',
            'absent' => 'Absent'
        );
        
        // Initialize form validation data
        $data['selected_session'] = '';
        $data['selected_exam_type'] = '';
        $data['selected_class'] = '';
        $data['selected_section'] = '';
        $data['selected_status'] = 'all';
        $data['results'] = array();
        
        // Handle form submission
        if ($this->input->post('search')) {
            $data['selected_session'] = $this->input->post('session_id');
            $data['selected_exam_type'] = $this->input->post('exam_type_id');
            $data['selected_class'] = $this->input->post('class_id');
            $data['selected_section'] = $this->input->post('section_id');
            $data['selected_status'] = $this->input->post('status');
            
            // Get exam types for selected session
            if (!empty($data['selected_session'])) {
                $data['examtypelist'] = $this->externalresult_model->getExamTypes($data['selected_session']);
            }
            
            // Build filters array - convert single values to arrays for the model
            $filters = array(
                'session_id' => !empty($data['selected_session']) ? array($data['selected_session']) : null,
                'exam_type_id' => !empty($data['selected_exam_type']) ? array($data['selected_exam_type']) : null,
                'class_id' => !empty($data['selected_class']) ? array($data['selected_class']) : null,
                'section_id' => !empty($data['selected_section']) ? array($data['selected_section']) : null,
                'status' => $data['selected_status']
            );
            
            // Fetch results
            $data['results'] = $this->externalresult_model->getExternalResultsReport($filters);
        } else {
            // On initial load, get exam types for current session
            $data['examtypelist'] = $this->externalresult_model->getExamTypes($this->current_session);
            $data['selected_session'] = $this->current_session;
        }
        
        $this->load->view('layout/header', $data);
        $this->load->view('reports/external_result', $data);
        $this->load->view('layout/footer', $data);
    }

    /**
     * AJAX method to get external exam types based on selected session
     */
    public function getExternalExamTypesBySession()
    {
        $this->load->model('externalresult_model');
        $session_id = $this->input->post('session_id');
        
        if (!empty($session_id)) {
            $exam_types = $this->externalresult_model->getExamTypes($session_id);
            echo json_encode($exam_types);
        } else {
            echo json_encode(array());
        }
    }

    public function onlineexams()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/online_examinations');
        $this->session->set_userdata('subsub_menu', 'Reports/online_examinations/onlineexams');
        $condition          = "";
        $data['searchlist'] = $this->customlib->get_searchtype();
        $data['date_type']  = $this->customlib->date_type();

        $this->load->view('layout/header', $data);
        $this->load->view('reports/onlineexams', $data);
        $this->load->view('layout/footer', $data);

    }

    public function onlineexamsresult()
    {

        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/examinations');
        $this->session->set_userdata('subsub_menu', 'Reports/examinations/onlineexamsresult');
        $condition           = "";
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

        $data['label'] = date($this->customlib->getSchoolDateFormat(), strtotime($start_date)) . " " . $this->lang->line('to') . " " . date($this->customlib->getSchoolDateFormat(), strtotime($end_date));

        if (isset($_POST['date_type']) && $_POST['date_type'] != '') {

            $data['date_typeid'] = $_POST['date_type'];

            if ($_POST['date_type'] == 'exam_from_date') {

                $condition = " date_format(exam_from,'%Y-%m-%d') between '" . $start_date . "' and '" . $end_date . "'";

            } elseif ($_POST['date_type'] == 'exam_to_date') {

                $condition = " date_format(exam_to,'%Y-%m-%d') between '" . $start_date . "' and '" . $end_date . "'";

            }

        } else {

            $condition = " date_format(created_at,'%Y-%m-%d') between '" . $start_date . "' and '" . $end_date . "'";

        }

        $data['resultlist'] = $this->onlineexam_model->onlineexamReport($condition);
        $this->load->view('layout/header', $data);
        $this->load->view('reports/onlineexamsresult', $data);
        $this->load->view('layout/footer', $data);
    }

    public function onlineexamattend()
    {

        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/online_examinations');
        $this->session->set_userdata('subsub_menu', 'Reports/online_examinations/onlineexamattend');
        $data['searchlist'] = $this->customlib->get_searchtype();
        $data['date_type']  = $this->customlib->date_type();
        $this->load->view('layout/header', $data);
        $this->load->view('reports/onlineexamattend', $data);
        $this->load->view('layout/footer', $data);
    }

    public function onlineexamrank()
    {

        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/online_examinations');
        $this->session->set_userdata('subsub_menu', 'Reports/online_examinations/onlineexamrank');

        $exam_id             = $class_id             = $section_id             = $condition             = '';
        $studentrecord       = array();
        $getResultByStudent1 = array();

        $examList          = $this->onlineexam_model->get();
        $data['examList']  = $examList;
        $class             = $this->class_model->get();
        $data['classlist'] = $class;
        $this->form_validation->set_rules('exam_id', $this->lang->line('exam'), 'required');

        if ($this->form_validation->run() == false) {

        } else {
            if (isset($_POST['class_id']) && $_POST['class_id'] != '') {
                $class_id = $_POST['class_id'];
            }

            if (isset($_POST['section_id']) && $_POST['section_id'] != '') {
                $section_id = $_POST['section_id'];
            }

            if (isset($_POST['exam_id']) && $_POST['exam_id'] != '') {
                $exam_id = $_POST['exam_id'];
            }

            $exam = $this->onlineexam_model->get($exam_id);

            if (!empty($exam)) {

                $student_data = $this->onlineexam_model->searchAllOnlineExamStudents($exam_id, $class_id, $section_id, 1);

                if (!empty($student_data)) {
                    foreach ($student_data as $student_key => $student_value) {
                        $student_data[$student_key]['questions_results'] = $this->onlineexamresult_model->getResultByStudent($student_value['onlineexam_student_id'], $exam_id);
                    }
                }

                $data['exam']         = $exam;
                $data['student_data'] = $student_data;
            }

        }
        $data['sch_setting'] = $this->sch_setting_detail;
        $this->load->view('layout/header', $data);
        $this->load->view('reports/onlineexamrank', $data);
        $this->load->view('layout/footer', $data);

    }

    public function inventorystock()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/inventory');
        $this->session->set_userdata('subsub_menu', 'Reports/inventory/inventorystock');
        $data['searchlist'] = $this->customlib->get_searchtype();
        $this->load->view('layout/header');
        $this->load->view('reports/inventorystock', $data);
        $this->load->view('layout/footer');
    }

    public function additem()
    {

        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/inventory');
        $this->session->set_userdata('subsub_menu', 'Reports/inventory/additem');
        $data['searchlist']  = $this->customlib->get_searchtype();
        $data['date_type']   = $this->customlib->date_type();
        $data['date_typeid'] = '';
        $this->load->view('layout/header', $data);
        $this->load->view('reports/additem', $data);
        $this->load->view('layout/footer', $data);
    }

    public function getadditemlistbydt()
    {

        if (isset($_POST['search_type']) && $_POST['search_type'] != '') {
            $dates               = $this->customlib->get_betweendate($_POST['search_type']);
            $data['search_type'] = $_POST['search_type'];
        } else {
            $dates               = $this->customlib->get_betweendate('this_year');
            $data['search_type'] = '';
        }

        $start_date = date('Y-m-d', strtotime($dates['from_date']));
        $end_date   = date('Y-m-d', strtotime($dates['to_date']));

        $data['label'] = date($this->customlib->getSchoolDateFormat(), strtotime($start_date)) . " " . $this->lang->line('to') . " " . date($this->customlib->getSchoolDateFormat(), strtotime($end_date));
        $itemresult    = $this->itemstock_model->get_ItemByBetweenDate($start_date, $end_date);

        $resultlist      = json_decode($itemresult);
        $currency_symbol = $this->customlib->getSchoolCurrencyFormat();
        $dt_data         = array();
        if (!empty($resultlist->data)) {
            foreach ($resultlist->data as $key => $value) {

                $row       = array();
                $row[]     = $value->name;
                $row[]     = $value->item_category;
                $row[]     = $value->item_supplier;
                $row[]     = $value->item_store;
                $row[]     = $value->quantity;
                $row[]     = $currency_symbol . amountFormat($value->purchase_price);
                $row[]     = date($this->customlib->getSchoolDateFormat(), strtotime($value->date));
                $dt_data[] = $row;
            }
        }

        $json_data = array(
            "draw"            => intval($resultlist->draw),
            "recordsTotal"    => intval($resultlist->recordsTotal),
            "recordsFiltered" => intval($resultlist->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function issueinventory()
    {

        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/inventory');
        $this->session->set_userdata('subsub_menu', 'Reports/inventory/issueinventory');
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

        $data['label']         = date($this->customlib->getSchoolDateFormat(), strtotime($start_date)) . " " . $this->lang->line('to') . " " . date($this->customlib->getSchoolDateFormat(), strtotime($end_date));
        // $data['itemissueList'] = $this->itemissue_model->get_IssueInventoryReport($start_date, $end_date);

        $this->load->view('layout/header', $data);
        $this->load->view('reports/issueinventory', $data);
        $this->load->view('layout/footer', $data);
    }

    public function getissueinventorylistbydt()
    {
        if (isset($_POST['search_type']) && $_POST['search_type'] != '') {
            $dates               = $this->customlib->get_betweendate($_POST['search_type']);
            $data['search_type'] = $_POST['search_type'];
        } else {
            $dates               = $this->customlib->get_betweendate('this_year');
            $data['search_type'] = '';
        }

        $start_date = date('Y-m-d', strtotime($dates['from_date']));
        $end_date   = date('Y-m-d', strtotime($dates['to_date']));

        $data['label']   = date($this->customlib->getSchoolDateFormat(), strtotime($start_date)) . " " . $this->lang->line('to') . " " . date($this->customlib->getSchoolDateFormat(), strtotime($end_date));
        $itemresult      = $this->itemissue_model->get_IssueInventoryReport($start_date, $end_date);
        $resultlist      = json_decode($itemresult);
        $currency_symbol = $this->customlib->getSchoolCurrencyFormat();
        $dt_data         = array();
        if (!empty($resultlist->data)) {
            foreach ($resultlist->data as $key => $value) {                

                $row   = array();
                $row[] = $value->item_name;
                $row[] = $value->note;                
                
                $row[] = $value->item_category;
                if ($value->return_date == "0000-00-00") {
                    $return_date = "";
                } else {
                    $return_date = date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($value->return_date));
                }
                
                $row[] = date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($value->issue_date)) . " - " . $return_date;
                $row[]     = $value->staff_name . " " . $value->surname . "(" . $value->employee_id . ")";
                $row[]     = $value->issued_by_name . " " . $value->issued_by_surname . "(" . $value->issued_by_employee_id . ")";        
                
                $row[]     = $value->quantity;
                $dt_data[] = $row;
            }
        }

        $json_data = array(
            "draw"            => intval($resultlist->draw),
            "recordsTotal"    => intval($resultlist->recordsTotal),
            "recordsFiltered" => intval($resultlist->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function finance()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/finance');
        $this->session->set_userdata('subsub_menu', '');
        $this->load->view('layout/header');
        $this->load->view('reports/finance');
        $this->load->view('layout/footer');
    }    

    public function student_profile()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/student_information');
        $this->session->set_userdata('subsub_menu', 'Reports/student_information/student_profile');
        $data['title']           = 'Add Fees Type';
        $data['searchlist']      = $this->search_type;
        $data['sch_setting']     = $this->sch_setting_detail;
        $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;
        $searchterm              = '';
        $class                   = $this->class_model->get();
        $data['classlist']       = $class;
        $data['class_id']        = $class_id        = $this->input->post('class_id');
        $data['section_id']      = $section_id      = $this->input->post('section_id');
        $condition1              = "";
        $condition2              = "";

        // Initialize section list - get all sections for initial load
        $data['section_list'] = array();

        // For initial page load, get all sections from all classes
        if (empty($class_id)) {
            // Get all sections for all classes
            $all_classes = $data['classlist'];
            foreach ($all_classes as $class) {
                $sections = $this->section_model->getClassBySection($class['id']);
                if (!empty($sections)) {
                    $data['section_list'] = array_merge($data['section_list'], $sections);
                }
            }
            // Remove duplicates based on section_id
            $unique_sections = array();
            $added_section_ids = array();
            foreach ($data['section_list'] as $section) {
                if (!in_array($section['section_id'], $added_section_ids)) {
                    $unique_sections[] = $section;
                    $added_section_ids[] = $section['section_id'];
                }
            }
            $data['section_list'] = $unique_sections;
        } else {
            if (is_array($class_id)) {
                // For multi-select, get sections for all selected classes
                foreach ($class_id as $single_class_id) {
                    $sections = $this->section_model->getClassBySection($single_class_id);
                    if (!empty($sections)) {
                        $data['section_list'] = array_merge($data['section_list'], $sections);
                    }
                }
                // Remove duplicates based on section_id
                $unique_sections = array();
                $added_section_ids = array();
                foreach ($data['section_list'] as $section) {
                    if (!in_array($section['section_id'], $added_section_ids)) {
                        $unique_sections[] = $section;
                        $added_section_ids[] = $section['section_id'];
                    }
                }
                $data['section_list'] = $unique_sections;
            } else {
                $data['section_list'] = $this->section_model->getClassBySection($class_id);
            }
        }

        $data['search_type']  = '';
        $data['filter_label'] = '';
        if (isset($_POST['search_type']) && $_POST['search_type'] != '') {

            $between_date        = $this->customlib->get_betweendate($_POST['search_type']);
            $data['search_type'] = $search_type = $_POST['search_type'];

            $from_date            = date('Y-m-d', strtotime($between_date['from_date']));
            $to_date              = date('Y-m-d', strtotime($between_date['to_date']));
            $condition2           = " date_format(admission_date,'%Y-%m-%d') between  '" . $from_date . "' and '" . $to_date . "'";
            $data['filter_label'] = date($this->customlib->getSchoolDateFormat(), strtotime($from_date)) . " To " . date($this->customlib->getSchoolDateFormat(), strtotime($to_date));
        }

        $data['sch_setting']     = $this->sch_setting_detail;
        $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;

        // Only process form data if this is a POST request
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            // Remove required validation for flexible filtering - allow empty class/section selection
            $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|xss_clean');
            $this->form_validation->set_rules('section_id', $this->lang->line('section'), 'trim|xss_clean');

            if ($this->form_validation->run() == false) {
                $data['resultlist'] = array();
            } else {
                // Handle multi-select arrays for class_id and section_id
                $class_ids = $this->input->post('class_id');
                $section_ids = $this->input->post('section_id');

                $condition1 = '';

                // Build condition for class_id (handle both arrays and single values)
                if (!empty($class_ids)) {
                    if (is_array($class_ids)) {
                        $class_ids_clean = array_map('intval', $class_ids);
                        $condition1 .= " classes.id IN (" . implode(',', $class_ids_clean) . ")";
                    } else {
                        $condition1 .= " classes.id = " . intval($class_ids);
                    }
                }

                // Build condition for section_id (handle both arrays and single values)
                if (!empty($section_ids)) {
                    if (!empty($condition1)) {
                        $condition1 .= " AND ";
                    }
                    if (is_array($section_ids)) {
                        $section_ids_clean = array_map('intval', $section_ids);
                        $condition1 .= " sections.id IN (" . implode(',', $section_ids_clean) . ")";
                    } else {
                        $condition1 .= " sections.id = " . intval($section_ids);
                    }
                }

                $data['resultlist'] = $this->student_model->student_profile($condition1, $condition2);
            }
        } else {
            // For GET requests (initial page load), show empty results
            $data['resultlist'] = array();
        }

        $this->load->view('layout/header', $data);
        $this->load->view('reports/student_profile', $data);
        $this->load->view('layout/footer', $data);
    }

    public function searchstudentprofilevalidation()
    {
        // Enhanced debugging for validation method
        error_log('=== STUDENT PROFILE VALIDATION STARTED ===');
        error_log('POST data: ' . print_r($_POST, true));
        error_log('Request method: ' . $_SERVER['REQUEST_METHOD']);

        // Handle multi-select values - convert to arrays if needed
        $class_id   = $this->input->post('class_id');
        $section_id = $this->input->post('section_id');
        $search_type = $this->input->post('search_type');

        error_log('Raw input - class_id: ' . print_r($class_id, true));
        error_log('Raw input - section_id: ' . print_r($section_id, true));
        error_log('Raw input - search_type: ' . print_r($search_type, true));

        // Convert single values to arrays for consistency
        if (!is_array($class_id) && !empty($class_id)) {
            $class_id = array($class_id);
        }
        if (!is_array($section_id) && !empty($section_id)) {
            $section_id = array($section_id);
        }

        error_log('Processed input - class_id: ' . print_r($class_id, true));
        error_log('Processed input - section_id: ' . print_r($section_id, true));

        // Remove required validation for flexible filtering - allow empty class and section selection
        // Skip form validation for array inputs as CodeIgniter doesn't handle them well
        $validation_needed = false;

        // Skip form validation for multi-select arrays and proceed directly
        $params = array('class_id' => $class_id, 'section_id' => $section_id, 'search_type' => $search_type);
        error_log('Validation success - params: ' . print_r($params, true));
        $array  = array('status' => 1, 'error' => '', 'params' => $params);
        echo json_encode($array);
    }

    public function dtstudentprofilereportlist()
    {
        // Enhanced error logging and debugging
        error_log('=== STUDENT PROFILE DATATABLE REQUEST STARTED ===');
        error_log('POST data: ' . print_r($_POST, true));
        error_log('Request method: ' . $_SERVER['REQUEST_METHOD']);

        $class_id   = $this->input->post('class_id');
        $section_id = $this->input->post('section_id');
        $search_type = $this->input->post('search_type');

        // Enhanced debug logging
        error_log('Student Profile DataTable - Raw input: class_id=' . print_r($class_id, true) . ', section_id=' . print_r($section_id, true) . ', search_type=' . print_r($search_type, true));
        log_message('debug', 'Student Profile DataTable - Raw input: class_id=' . print_r($class_id, true) . ', section_id=' . print_r($section_id, true) . ', search_type=' . print_r($search_type, true));

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

        error_log('Student Profile DataTable - Processed: class_id=' . print_r($class_id, true) . ', section_id=' . print_r($section_id, true) . ', search_type=' . print_r($search_type, true));
        log_message('debug', 'Student Profile DataTable - Processed: class_id=' . print_r($class_id, true) . ', section_id=' . print_r($section_id, true) . ', search_type=' . print_r($search_type, true));

        try {
            error_log('Student Profile DataTable - Calling model method...');
            $result = $this->student_model->searchdatatablebyStudentProfileDetails($class_id, $section_id, $search_type);
            error_log('Student Profile DataTable - Model result length: ' . strlen($result));
            error_log('Student Profile DataTable - Model result preview: ' . substr($result, 0, 200) . '...');
            log_message('debug', 'Student Profile DataTable - Model result: ' . $result);

            $resultlist = json_decode($result);
            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log('Student Profile DataTable - JSON decode error: ' . json_last_error_msg());
                throw new Exception('JSON decode failed: ' . json_last_error_msg());
            }

            $sch_setting = $this->sch_setting_detail;
            $adm_auto_insert = $this->sch_setting_detail->adm_auto_insert;

            // Process data to match table structure like Guardian Report does
            $dt_data = array();
            if (!empty($resultlist->data)) {
                foreach ($resultlist->data as $student) {
                    $row = array();

                    // Add columns in the same order as the table headers
                    if (!$adm_auto_insert) {
                        $row[] = $student->admission_no;
                    }
                    if ($sch_setting->roll_no) {
                        $row[] = $student->roll_no;
                    }
                    $row[] = $student->class;
                    $row[] = $student->section;
                    $row[] = $student->firstname;
                    if ($sch_setting->middlename) {
                        $row[] = $student->middlename;
                    }
                    if ($sch_setting->lastname) {
                        $row[] = $student->lastname;
                    }
                    $row[] = $student->gender;
                    $row[] = $student->dob ? date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($student->dob)) : '';

                    // Add more columns based on school settings
                    if ($sch_setting->category) {
                        $row[] = $student->category;
                    }
                    if ($sch_setting->religion) {
                        $row[] = $student->religion;
                    }
                    if ($sch_setting->cast) {
                        $row[] = $student->cast;
                    }
                    if ($sch_setting->mobile_no) {
                        $row[] = $student->mobileno;
                    }
                    if ($sch_setting->student_email) {
                        $row[] = $student->email;
                    }
                    if ($sch_setting->admission_date) {
                        $row[] = $student->admission_date ? date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($student->admission_date)) : '';
                    }
                    if ($sch_setting->is_blood_group) {
                        $row[] = $student->blood_group;
                    }
                    if ($sch_setting->is_student_house) {
                        $row[] = $student->house_name;
                    }
                    if ($sch_setting->student_height) {
                        $row[] = $student->height;
                    }
                    if ($sch_setting->student_weight) {
                        $row[] = $student->weight;
                    }
                    if ($sch_setting->measurement_date) {
                        $row[] = $student->measurement_date ? date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($student->measurement_date)) : '';
                    }

                    $row[] = $student->fees_discount;

                    if ($sch_setting->father_name) {
                        $row[] = $student->father_name;
                    }
                    if ($sch_setting->father_phone) {
                        $row[] = $student->father_phone;
                    }
                    if ($sch_setting->father_occupation) {
                        $row[] = $student->father_occupation;
                    }
                    if ($sch_setting->mother_name) {
                        $row[] = $student->mother_name;
                    }
                    if ($sch_setting->mother_phone) {
                        $row[] = $student->mother_phone;
                    }
                    if ($sch_setting->mother_occupation) {
                        $row[] = $student->mother_occupation;
                    }

                    if ($sch_setting->guardian_name) {
                        $row[] = $student->guardian_is;
                        $row[] = $student->guardian_name;
                    }
                    if ($sch_setting->guardian_relation) {
                        $row[] = $student->guardian_relation;
                    }
                    if ($sch_setting->guardian_phone) {
                        $row[] = $student->guardian_phone;
                    }
                    if ($sch_setting->guardian_occupation) {
                        $row[] = $student->guardian_occupation;
                    }
                    if ($sch_setting->guardian_email) {
                        $row[] = $student->guardian_email;
                    }
                    if ($sch_setting->guardian_address) {
                        $row[] = $student->guardian_address;
                    }
                    if ($sch_setting->current_address) {
                        $row[] = $student->current_address;
                    }
                    if ($sch_setting->permanent_address) {
                        $row[] = $student->permanent_address;
                    }
                    if ($sch_setting->route_list) {
                        $row[] = ''; // transport route not in current select
                    }
                    if ($sch_setting->hostel_id) {
                        $row[] = ''; // hostel details not in current select
                    }
                    $row[] = ''; // room_no not in current select

                    if ($sch_setting->bank_account_no) {
                        $row[] = $student->bank_account_no;
                    }
                    if ($sch_setting->bank_name) {
                        $row[] = $student->bank_name;
                    }
                    if ($sch_setting->ifsc_code) {
                        $row[] = $student->ifsc_code;
                    }
                    if ($sch_setting->national_identification_no) {
                        $row[] = $student->adhar_no;
                    }
                    if ($sch_setting->local_identification_no) {
                        $row[] = $student->samagra_id;
                    }
                    if ($sch_setting->rte) {
                        $row[] = $student->rte;
                    }
                    if ($sch_setting->previous_school_details) {
                        $row[] = $student->previous_school;
                    }
                    if ($sch_setting->student_note) {
                        $row[] = $student->note;
                    }

                    $dt_data[] = $row;
                }
            }

            $json_data = array(
                "draw"            => intval($resultlist->draw),
                "recordsTotal"    => intval($resultlist->recordsTotal),
                "recordsFiltered" => intval($resultlist->recordsFiltered),
                "data"            => $dt_data,
            );

            error_log('Student Profile DataTable - Final JSON data rows count: ' . count($dt_data));

            // Set proper JSON header
            header('Content-Type: application/json');
            echo json_encode($json_data);
        } catch (Exception $e) {
            error_log('Student Profile DataTable - Exception: ' . $e->getMessage());
            // Return empty DataTable response on error
            $empty_response = array(
                "draw" => intval($this->input->post('draw')),
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => array()
            );
            echo json_encode($empty_response);
        }
    }

    public function staff_report()
    {

        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/human_resource');
        $this->session->set_userdata('subsub_menu', 'Reports/human_resource/staff_report');
        $data['title']           = 'Add Fees Type';
        $data['searchlist']      = $this->search_type;
        $data['sch_setting']     = $this->sch_setting_detail;
        $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;
        $searchterm              = '';
        $condition               = "";
        if (isset($_POST['search_type']) && $_POST['search_type'] != '') {

            $between_date        = $this->customlib->get_betweendate($_POST['search_type']);
            $data['search_type'] = $search_type = $_POST['search_type'];

            $from_date = date('Y-m-d', strtotime($between_date['from_date']));

            $to_date = date('Y-m-d', strtotime($between_date['to_date']));

            $condition .= " and date_format(date_of_joining,'%Y-%m-%d') between  '" . $from_date . "' and '" . $to_date . "'";
            $data['filter_label'] = date($this->customlib->getSchoolDateFormat(), strtotime($from_date)) . " To " . date($this->customlib->getSchoolDateFormat(), strtotime($to_date));
        }

        if (isset($_POST['staff_status']) && $_POST['staff_status'] != '') {
            if ($_POST['staff_status'] == 'both') {

                $search_status = "1,2";

            } elseif ($_POST['staff_status'] == '2') {

                $search_status = "0";

            } else {

                $search_status = "1";

            }
            $condition .= " and `staff`.`is_active` in (" . $search_status . ")";
            $data['status_val'] = $_POST['staff_status'];
        } else {
            $data['status_val'] = 1;
        }

        if (isset($_POST['role']) && $_POST['role'] != '') {
            $condition .= " and `staff_roles`.`role_id`=" . $_POST['role'];
            $data['role_val'] = $_POST['role'];
        }

        if (isset($_POST['designation']) && $_POST['designation'] != '') {
            $condition .= " and `staff_designation`.`id`=" . $_POST['designation'];
            $data['designation_val'] = $_POST['designation'];
        }

        $data['resultlist'] = $this->staff_model->staff_report($condition);
        $leave_type         = $this->leavetypes_model->getLeaveType();
        foreach ($leave_type as $key => $leave_value) {
            $data['leave_type'][$leave_value['id']] = $leave_value['type'];
        }
        $data['status']      = $this->customlib->staff_status();
        $data['roles']       = $this->role_model->get();
        $data['designation'] = $this->designation_model->get();

        $data['fields']          = $this->customfield_model->get_custom_fields('staff', 1);
        $data['sch_setting']     = $this->sch_setting_detail;
        $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;

        $this->load->view('layout/header', $data);
        $this->load->view('reports/staff_report', $data);
        $this->load->view('layout/footer', $data);
    }   

    public function lesson_plan()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/lesson_plan');
        $this->session->set_userdata('subsub_menu', 'Reports/lesson_plan/lesson_plan');
        $data                     = array();
        $data['subjects_data']    = array();
        $class                    = $this->class_model->get();
        $data['classlist']        = $class;
        $data['class_id']         = "";
        $data['section_id']       = "";
        $data['subject_group_id'] = "";
        $data['subject_id']       = "";
        $data['lessons']          = array();
        $lebel                    = "";

        $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('section_id', $this->lang->line('section'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('subject_group_id', $this->lang->line('subject_group'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {

        } else {

            $data['class_id']               = $_POST['class_id'];
            $data['section_id']             = $_POST['section_id'];
            $data['subject_group_id']       = $_POST['subject_group_id'];
            $subjects                       = $this->subjectgroup_model->getGroupsubjects($_POST['subject_group_id']);
            $subject_group_class_sectionsId = $this->lessonplan_model->getsubject_group_class_sectionsId($_POST['class_id'], $_POST['section_id'], $_POST['subject_group_id']);

            foreach ($subjects as $key => $value) {
                $show_status     = 0;
                $teacher_summary = array();
                $lesson_result   = array();
                $complete        = 0;
                $incomplete      = 0;
                $array[]         = $value;
                $lebel           = ($value->code == '') ? $value->name : $value->name . ' (' . $value->code . ')';

                $subject_details = $this->syllabus_model->get_subjectstatus($value->id, $subject_group_class_sectionsId['id']);
                if ($subject_details[0]->total != 0) {

                    $complete   = ($subject_details[0]->complete / $subject_details[0]->total) * 100;
                    $incomplete = ($subject_details[0]->incomplete / $subject_details[0]->total) * 100;

                    $data['subjects_data'][$value->id] = array(
                        'lebel'      => $lebel,
                        'complete'   => round($complete),
                        'incomplete' => round($incomplete),
                        'id'         => $value->id . '_' . $value->code,
                        'total'      => $subject_details[0]->total,
                        'name'       => $value->name,
                    );

                } else {

                    $data['subjects_data'][$value->id] = array(
                        'lebel'      => $lebel,
                        'complete'   => 0,
                        'incomplete' => 0,
                        'id'         => $value->id . '_' . $value->code,
                        'total'      => 0,
                        'name'       => $value->name,

                    );
                }

                $syllabus_report = $this->syllabus_model->get_subjectsyllabussreport($value->id, $subject_group_class_sectionsId['id']);
                $lesson_result   = array();
                foreach ($syllabus_report as $syllabus_reportkey => $syllabus_reportvalue) {

                    $topic_data     = array();
                    $topic_result   = $this->syllabus_model->get_topicbylessonid($syllabus_reportvalue['id']);
                    $topic_complete = 0;
                    foreach ($topic_result as $topic_resultkey => $topic_resultvalue) {
                        if ($topic_resultvalue['status'] == 1) {
                            $topic_complete++;
                        }

                        $topic_data[] = array('name' => $topic_resultvalue['name'], 'status' => $topic_resultvalue['status'], 'complete_date' => $topic_resultvalue['complete_date']);
                    }
                    $total_topic = count($topic_data);
                    if ($total_topic > 0) {
                        $incomplete_percent = round((($total_topic - $topic_complete) / $total_topic) * 100);
                        $complete_percent   = round(($topic_complete / $total_topic) * 100);
                    } else {
                        $incomplete_percent = 0;
                        $complete_percent   = 0;
                    }

                    $show_status     = 1;
                    $lesson_result[] = array('name' => $syllabus_reportvalue['name'], 'topics' => $topic_data, 'incomplete_percent' => $incomplete_percent, 'complete_percent' => $complete_percent);

                }

                $data['subjects_data'][$value->id]['lesson_summary'] = $lesson_result;

            }
        }

        $data['status'] = array('1' => $this->lang->line('complete'), '0' => $this->lang->line('incomplete'));
        $this->load->view('layout/header', $data);
        $this->load->view('reports/syllabus', $data);
        $this->load->view('layout/footer', $data);
    }

    public function teachersyllabusstatus()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/lesson_plan');
        $this->session->set_userdata('subsub_menu', 'Reports/lesson_plan/teachersyllabusstatus');
        $data                     = array();
        $data['subjects_data']    = array();
        $class                    = $this->class_model->get();
        $data['classlist']        = $class;
        $data['class_id']         = "";
        $data['section_id']       = "";
        $data['subject_group_id'] = "";
        $data['subject_id']       = "";
        $data['lessons']          = array();

        $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('section_id', $this->lang->line('section'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('subject_group_id', $this->lang->line('subject_group'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('subject_id', $this->lang->line('subject'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {

        } else {
            $lebel = "";

            $data['class_id']         = $_POST['class_id'];
            $data['section_id']       = $_POST['section_id'];
            $data['subject_group_id'] = $_POST['subject_group_id'];
            $data['subject_id']       = $_POST['subject_id'];

            $subject_group_class_sectionsId = $this->lessonplan_model->getsubject_group_class_sectionsId($_POST['class_id'], $_POST['section_id'], $_POST['subject_group_id']);

            $teacher_summary          = array();
            $complete                 = 0;
            $incomplete               = 0;
            $data['subject_name']     = "";
            $data['subject_complete'] = 0;
            $subjectdata              = $this->subject_model->get($_POST['subject_id']);

            $subject_details = $this->syllabus_model->get_subjectstatus($_POST['subject_id'], $subject_group_class_sectionsId['id']);
            if ($subject_details[0]->total != 0) {

                $complete   = ($subject_details[0]->complete / $subject_details[0]->total) * 100;
                $incomplete = ($subject_details[0]->incomplete / $subject_details[0]->total) * 100;
                if ($subjectdata['code'] == '') {
                    $lebel = $subjectdata['name'];
                } else {
                    $lebel = $subjectdata['name'] . ' (' . $subjectdata['code'] . ')';
                }
                $data['subjects_data'][$subjectdata['id']] = array(
                    'lebel'      => $lebel,
                    'complete'   => round($complete),
                    'incomplete' => round($incomplete),
                    'id'         => $subjectdata['id'] . '_' . $subjectdata['code'],
                );
                $data['subject_complete'] = round($complete);

            } else {

                $data['subjects_data'][$subjectdata['id']] = array(
                    'lebel'      => $lebel,
                    'complete'   => 0,
                    'incomplete' => 0,
                    'id'         => $subjectdata['id'] . '_' . $subjectdata['code'],
                );
                $data['subject_complete'] = 0;
            }

            $teachers_report = $this->syllabus_model->get_subjectteachersreport($_POST['subject_id'], $subject_group_class_sectionsId['id']);

            foreach ($teachers_report as $teachers_reportkey => $teachers_reportvalue) {
                if ($teachers_reportvalue['code'] == '') {
                    $data['subject_name'] = $teachers_reportvalue['subject_name'];

                } else {
                    $data['subject_name'] = $teachers_reportvalue['subject_name'] . " (" . $teachers_reportvalue['code'] . ")";

                }
                $syllabus_id       = explode(',', $teachers_reportvalue['subject_syllabus_id']);
                $staff_periodsdata = array();
                foreach ($syllabus_id as $syllabus_idkey => $syllabus_idvalue) {

                    $staff_periods       = $this->syllabus_model->get_subjectsyllabusbyid($syllabus_idvalue);
                    $staff_periodsdata[] = $staff_periods;

                }

                $teacher_summary[] = array(
                    'name'           => $teachers_reportvalue['name'],
                    'total_periods'  => $teachers_reportvalue['total_priodes'],
                    'summary_report' => $staff_periodsdata,
                );

            }

            $data['subjects_data'][$subjectdata['id']]['teachers_summary'] = $teacher_summary;

        }

        $this->load->view('layout/header', $data);
        $this->load->view('reports/teacherSyllabusStatus', $data);
        $this->load->view('layout/footer', $data);
    }

    public function alumnireport()
    {
        if (!$this->rbac->hasPrivilege('alumni_report', 'can_view')) {
            access_denied();
        }
        $data                = array();
        $data['sessionlist'] = $this->session_model->get();
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/alumni_report');
        $class                   = $this->class_model->get();
        $data['classlist']       = $class;
        $data['title']           = $this->lang->line('alumni_student_for_passout_session');
        $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;
        $data['sch_setting']     = $this->sch_setting_detail;
        $data['fields']          = $this->customfield_model->get_custom_fields('students', 1);
        $class                   = $this->class_model->get();
        $data['classlist']       = $class;
        $data['session_id']      = $session_id      = "";
        $userdata                = $this->customlib->getUserData();
        $carray                  = array();
        $alumni_student          = $this->alumni_model->get();
        $alumni_studets          = array();
        foreach ($alumni_student as $key => $value) {
            $alumni_studets[$value['student_id']] = $value;
        }
        $data['alumni_studets'] = $alumni_studets;
        if (!empty($data["classlist"])) {
            foreach ($data["classlist"] as $ckey => $cvalue) {

                $carray[] = $cvalue["id"];
            }
        }

        $button = $this->input->post('search');
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $this->load->view('layout/header', $data);
            $this->load->view('reports/alumnireport', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $class              = $this->input->post('class_id');
            $section            = $this->input->post('section_id');
            $search             = $this->input->post('search');
            $search_text        = $this->input->post('search_text');
            $data['session_id'] = $session_id = $this->input->post('session_id');
            if (isset($search)) {
                if ($search == 'search_filter') {
                    $this->form_validation->set_rules('session_id', $this->lang->line('session'), 'trim|required|xss_clean');
                    $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
                    if ($this->form_validation->run() == false) {

                    } else {
                        $data['searchby']    = "filter";
                        $data['class_id']    = $this->input->post('class_id');
                        $data['section_id']  = $this->input->post('section_id');
                        $data['search_text'] = $this->input->post('search_text');
                        $resultlist          = $this->student_model->search_alumniStudentReport($class, $section, $session_id);
                        $data['resultlist']  = $resultlist;

                    }
                } else if ($search == 'search_full') {
                    $data['searchby'] = "text";

                    $data['search_text'] = trim($this->input->post('search_text'));
                    $resultlist          = $this->student_model->search_alumniStudentbyAdmissionNoReport($search_text, $carray);
                    $data['resultlist']  = $resultlist;

                }
            }

            $this->load->view('layout/header');
            $this->load->view('reports/alumnireport', $data);
            $this->load->view('layout/footer');
        }

    }

    public function boys_girls_ratio()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/student_information');
        $this->session->set_userdata('subsub_menu', 'Reports/student_information/boys_girls_ratio');
        $data['title']           = 'Add Fees Type';
        $data['searchlist']      = $this->search_type;
        $data['sch_setting']     = $this->sch_setting_detail;
        $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;
        $searchterm              = '';
        $class                   = $this->class_model->get();
        $data['classlist']       = $class;
        foreach ($data['classlist'] as $key => $value) {
            $carray[] = $value['id'];
        }

        $data['resultlist'] = $this->student_model->student_ratio();
        $total_boys         = $total_girls         = 0;
        foreach ($data['resultlist'] as $key => $value) {

            $total_boys += $value['male'];
            $total_girls += $value['female'];

            $data['result'][] = array('total_student' => $value['total_student'], 'male' => $value['male'], 'female' => $value['female'], 'class' => $value['class'], 'section' => $value['section'], 'class_id' => $value['class_id'], 'section_id' => $value['section_id'], 'boys_girls_ratio' => $this->getRatio($value['male'], $value['female']));
        }

        $data['all_boys_girls_ratio']      = $this->getRatio($total_boys, $total_girls);
        $data['all_student_teacher_ratio'] = $this->getRatio($total_boys, $total_girls);

        $this->load->view('layout/header', $data);
        $this->load->view('reports/student_ratio_report', $data);
        $this->load->view('layout/footer', $data);
    }

    public function student_teacher_ratio()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/student_information');
        $this->session->set_userdata('subsub_menu', 'Reports/student_information/student_teacher_ratio');
        $data['title']           = 'Add Fees Type';
        $data['searchlist']      = $this->search_type;
        $data['sch_setting']     = $this->sch_setting_detail;
        $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;
        $searchterm              = '';    

        $data['resultlist'] = $this->student_model->student_ratio();
        $total_boys         = $total_girls         = $all_teacher         = $all_student         = 0;
        foreach ($data['resultlist'] as $key => $value) {

            $all_student += $value['total_student'];
            $count_classteachers = array();
            $count_classteachers = $this->student_model->count_classteachers($value['class_id'], $value['section_id']);

            if (!empty($count_classteachers)) {
                $total_teacher = $count_classteachers;
            } else {
                $total_teacher = 0;
            }

            $data['result'][] = array('total_student' => $value['total_student'], 'male' => $value['male'], 'female' => $value['female'], 'class' => $value['class'], 'section' => $value['section'], 'class_id' => $value['class_id'], 'section_id' => $value['section_id'], 'total_teacher' => $total_teacher, 'boys_girls_ratio' => $this->getRatio($value['male'], $value['female']), 'teacher_ratio' => $this->getRatio($value['total_student'], $total_teacher));

            $all_teacher += $total_teacher;
        }

        $data['all_student_teacher_ratio'] = $this->getRatio($all_student, $all_teacher);
        $this->load->view('layout/header', $data);
        $this->load->view('reports/teacher_ratio_report', $data);
        $this->load->view('layout/footer', $data);
    }

    public function getRatio($num1, $num2)
    {
        if ($num2 > 0 && $num1 > 0) {
            $num = round($num2 / $num1, 2);

        } else {
            $num = 0;
        }

        if ($num1 == '0') {
            $by = 0;
            return "$by:$num2";
        } else {
            $by = 1;
            return "$by:$num";
        }

    }    

    public function getAvailQuantity($item_id)
    {
        $data      = $this->item_model->getItemAvailable($item_id);
        $available = ($data['added_stock'] - $data['issued']);
        if ($available >= 0) {
            return $available;
        } else {
            return 0;
        }
    }

    public function getinventorylist()
    {
        if (isset($_POST['search_type']) && $_POST['search_type'] != '') {
            $dates               = $this->customlib->get_betweendate($_POST['search_type']);
            $data['search_type'] = $_POST['search_type'];
        } else {
            $dates               = $this->customlib->get_betweendate('this_year');
            $data['search_type'] = '';
        }

        $start_date = date('Y-m-d', strtotime($dates['from_date']));
        $end_date   = date('Y-m-d', strtotime($dates['to_date']));

        $data['label'] = date($this->customlib->getSchoolDateFormat(), strtotime($start_date)) . " " . $this->lang->line('to') . " " . date($this->customlib->getSchoolDateFormat(), strtotime($end_date));

        $dstockresult1 = $this->itemstock_model->get_currentstock($start_date, $end_date);
        $m             = json_decode($dstockresult1);
        $dt_data       = array();
        if (!empty($m->data)) {
            foreach ($m->data as $key => $value) {
                $available_stock = $this->getAvailQuantity($value->id);
                $row             = array();
                $row[]           = $value->name;
                 
                $row[]           = $value->item_category;
                $row[]           = $value->item_supplier;
                $row[]           = $value->item_store;
                $row[]           = $available_stock;
                $row[]           = $value->available_stock;
                $row[]           = $value->total_not_returned;
                $dt_data[]       = $row;
            }
        }

        $json_data = array(
            "draw"            => intval($m->draw),
            "recordsTotal"    => intval($m->recordsTotal),
            "recordsFiltered" => intval($m->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

   

    public function dtadmissionreport()
    {
        $sch_setting = $this->sch_setting_detail;
        $searchterm  = '';
        $class       = $this->class_model->get();
        $classlist   = $class;
        $count       = 0;

        foreach ($classlist as $key => $value) {
            $carray[] = $value['id'];
        }
        if (isset($_POST['search_type']) && $_POST['search_type'] != '') {

            $between_date        = $this->customlib->get_betweendate($_POST['search_type']);
            $data['search_type'] = $search_type = $_POST['search_type'];
        } else {

            $between_date        = $this->customlib->get_betweendate('this_year');
            $data['search_type'] = $search_type = '';
        }

        $from_date    = date('Y-m-d', strtotime($between_date['from_date']));
        $to_date      = date('Y-m-d', strtotime($between_date['to_date']));
        $condition    = " date_format(admission_date,'%Y-%m-%d') between  '" . $from_date . "' and '" . $to_date . "'";
        $filter_label = date($this->customlib->getSchoolDateFormat(), strtotime($from_date)) . " To " . date($this->customlib->getSchoolDateFormat(), strtotime($to_date));

        $result = $this->student_model->admission_report($searchterm, $carray, $condition);

        $resultlist = json_decode($result);
        $dt_data    = array();
        if (!empty($resultlist->data)) {
            foreach ($resultlist->data as $resultlist_key => $student) {

                $count++;
                $viewbtn = "<a  href='" . base_url() . "student/view/" . $student->id . "'>" . $this->customlib->getFullName($student->firstname, $student->middlename, $student->lastname, $sch_setting->middlename, $sch_setting->lastname) . "</a>";

                $row = array();

                $row[] = $student->admission_no;
                $row[] = $viewbtn;
                $row[] = $student->class . " (" . $student->section . ")";
                if ($sch_setting->father_name) {
                    $row[] = $student->father_name;
                }
                if ($student->dob != null && $student->dob != '0000-00-00') {
                    $row[] = date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($student->dob));
                } else {
                    $row[] = "";
                }

                if ($sch_setting->admission_date) {
                    if ($student->admission_date != null && $student->admission_date != '0000-00-00') {

                        $row[] = date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($student->admission_date));
                    } else {
                        $row[] = "";
                    }
                }

                $row[] = $this->lang->line(strtolower($student->gender));

                if ($sch_setting->category) {
                    $row[] = $student->category;
                }
                if ($sch_setting->mobile_no) {
                    $row[] = $student->mobileno;
                }

                $dt_data[] = $row;
            }

            $footer_row   = array();
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = $this->lang->line('total_admission_in_this_duration');
            $footer_row[] = $filter_label;
            $footer_row[] = $count;
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $dt_data[]    = $footer_row;

        }
        $json_data = array(
            "draw"            => intval($resultlist->draw),
            "recordsTotal"    => intval($resultlist->recordsTotal),
            "recordsFiltered" => intval($resultlist->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    /*
    function to get formparateter */
    public function getformparameter()
    {
        $search_type = $this->input->post('search_type');
        $date_type   = $this->input->post("date_type");
        $date_from   = "";
        $date_to     = "";
        if ($search_type == 'period') {
            $date_from = $this->input->post('date_from');
            $date_to   = $this->input->post('date_to');
        }

        $params = array('search_type' => $search_type, 'date_type' => $date_type, 'date_from' => $date_from, 'date_to' => $date_to);
        $array  = array('status' => 1, 'error' => '', 'params' => $params);
        echo json_encode($array);
    }

    public function dtexamreportlist()
    {
        $search_type = $this->input->post('search_type');
        $date_type   = $this->input->post('date_type');
        $date_from   = $this->input->post('date_from');
        $date_to     = $this->input->post('date_to');

        $data['date_typeid'] = '';
        if (isset($search_type) && $search_type != '') {

            $dates               = $this->customlib->get_betweendate($search_type);
            $data['search_type'] = $search_type;

        } else {

            $dates               = $this->customlib->get_betweendate('this_year');
            $data['search_type'] = '';

        }

        $start_date = date('Y-m-d', strtotime($dates['from_date']));
        $end_date   = date('Y-m-d', strtotime($dates['to_date']));

        $data['label'] = date($this->customlib->getSchoolDateFormat(), strtotime($start_date)) . " " . $this->lang->line('to') . " " . date($this->customlib->getSchoolDateFormat(), strtotime($end_date));

        if (isset($date_type) && $date_type != '') {

            $data['date_typeid'] = $date_type;

            if ($date_type == 'exam_from_date') {
                $condition = " date_format(exam_from,'%Y-%m-%d') between '" . $start_date . "' and '" . $end_date . "'";
            } elseif ($date_type == 'exam_to_date') {
                $condition = " date_format(exam_to,'%Y-%m-%d') between '" . $start_date . "' and '" . $end_date . "'";
            }

        } else {
            $condition = " date_format(onlineexam.created_at,'%Y-%m-%d') between '" . $start_date . "' and '" . $end_date . "'";
        }

        $sch_setting = $this->sch_setting_detail;
        $results     = $this->onlineexam_model->onlineexamReport($condition);

        $resultlist = json_decode($results);
        $dt_data    = array();

        if (!empty($resultlist->data)) {
            foreach ($resultlist->data as $resultlist_key => $subject_value) {

                if ($subject_value->is_active == 1) {
                    $publish_btn = " <i class='fa fa-check-square-o'></i><span style='display:none'>" . $this->lang->line('yes') . "</span>";
                } else {
                    $publish_btn = " <i class='fa fa-exclamation-circle'></i><span style='display:none'>" . $this->lang->line('no') . "</span>";
                }

                if ($subject_value->is_active == 1) {
                    $result_publish = " <i class='fa fa-check-square-o'></i><span style='display:none'>" . $this->lang->line('yes') . "</span>";
                } else {
                    $result_publish = "<i class='fa fa-exclamation-circle'></i><span style='display:none'>" . $this->lang->line('no') . "</span>";
                }

                $row   = array();
                $row[] = $subject_value->exam;
                $row[] = $subject_value->attempt;
                $row[] = $this->customlib->dateyyyymmddToDateTimeformat($subject_value->exam_from, false);
                $row[] = $this->customlib->dateyyyymmddToDateTimeformat($subject_value->exam_to, false);
                $row[] = $subject_value->duration;
                $row[] = $subject_value->assign;
                $row[] = $subject_value->questions;
                $row[] = $publish_btn;
                $row[] = $result_publish;

                $dt_data[] = $row;
            }

        }
        $json_data = array(
            "draw"            => intval($resultlist->draw),
            "recordsTotal"    => intval($resultlist->recordsTotal),
            "recordsFiltered" => intval($resultlist->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    /* function to get exam attempt report using datatable*/

    public function dtexamattemptreport()
    {
        $condition   = "";
        $search_type = $this->input->post('search_type');
        $date_type   = $this->input->post('date_type');
        $date_from   = $this->input->post('date_from');
        $date_to     = $this->input->post('date_to');
        $data['searchlist'] = $this->customlib->get_searchtype();
        $data['date_type']  = $this->customlib->date_type();

        $data['date_typeid'] = '';
        if (isset($search_type) && $search_type != '') {
            $dates               = $this->customlib->get_betweendate($search_type);
            $data['search_type'] = $search_type;
        } else {
            $dates               = $this->customlib->get_betweendate('this_year');
            $data['search_type'] = '';
        }

        $start_date = date('Y-m-d', strtotime($dates['from_date']));
        $end_date   = date('Y-m-d', strtotime($dates['to_date']));

        $data['label'] = date($this->customlib->getSchoolDateFormat(), strtotime($start_date)) . " " . $this->lang->line('to') . " " . date($this->customlib->getSchoolDateFormat(), strtotime($end_date));

        if (isset($date_type) && $date_type != '') {

            $data['date_typeid'] = $_POST['date_type'];

            if ($date_type == 'exam_from_date') {

                $condition .= " and date_format(onlineexam.exam_from,'%Y-%m-%d') between '" . $start_date . "' and '" . $end_date . "'";

            } elseif ($date_type == 'exam_to_date') {

                $condition .= " and date_format(onlineexam.exam_to,'%Y-%m-%d') between '" . $start_date . "' and '" . $end_date . "'";
            }

        } else {

            $condition .= " and  date_format(onlineexam.created_at,'%Y-%m-%d') between '" . $start_date . "' and '" . $end_date . "'";

        }

        $result      = $this->onlineexam_model->onlineexamatteptreport($condition);
        $sch_setting = $this->sch_setting_detail;
        $resultlist  = json_decode($result);
        $dt_data     = array();

        if (!empty($resultlist->data)) {
            foreach ($resultlist->data as $resultlist_key => $student_value) {

                $exams = explode(',', $student_value->exams);

                $exam_name               = "";
                $exam_from               = "";
                $exam_to                 = "";
                $exam_duration           = "";
                $exam_publish            = "";
                $exam_resultpublish      = "";
                $exam_publishprint       = "";
                $exam_resultpublishprint = "";
                foreach ($exams as $exams_key => $exams_value) {
                    $exam_details = explode('@', $exams_value);

                    if (count($exam_details) == 9) {

                        $exam_name .= $exam_details[1];
                        $exam_from .= date($this->customlib->getSchoolDateFormat(), $this->customlib->dateYYYYMMDDtoStrtotime($exam_details[3]));
                        $exam_to .= date($this->customlib->getSchoolDateFormat(), $this->customlib->dateYYYYMMDDtoStrtotime($exam_details[4]));
                        $exam_duration .= $exam_details[5];
                        $exam_publish .= ($exam_details[7] == 1) ? "<i class='fa fa-check-square-o' aria-hidden='true'></i>" : "<i class='fa fa-exclamation-circle' aria-hidden='true'></i>";
                        $exam_resultpublish .= ($exam_details[8] == 1) ? "<i class='fa fa-check-square-o' aria-hidden='true'></i>" : "<i class='fa fa-exclamation-circle' aria-hidden='true'></i>";

                        $exam_publishprint .= ($exam_details[7] == 1) ? "<span style='display:none'>" . $this->lang->line('yes') . "</span>" : "<span style='display:none'>" . $this->lang->line('no') . "</span>";
                        $exam_resultpublishprint .= ($exam_details[8] == 1) ? "<span style='display:none'>" . $this->lang->line('yes') . "</span>" : "<span style='display:none'>" . $this->lang->line('no') . "</span>";

                        $exam_name .= '<br>';
                        $exam_from .= "<br>";
                        $exam_to .= "<br>";
                        $exam_duration .= "<br>";
                        $exam_publish .= "<br>";
                        $exam_resultpublish .= "<br>";
                        $exam_publishprint .= "<br>";
                        $exam_resultpublishprint .= "<br>";
                    }
                }

                $row   = array();
                $row[] = $student_value->admission_no;
                $row[] = $this->customlib->getFullName($student_value->firstname, $student_value->middlename, $student_value->lastname, $sch_setting->middlename, $sch_setting->lastname);
                $row[] = $student_value->class;
                $row[] = $student_value->section;
                $row[] = $exam_name;
                $row[] = $exam_from;
                $row[] = $exam_to;
                $row[] = $exam_duration;
                $row[] = $exam_publish . $exam_publishprint;
                $row[] = $exam_resultpublish . $exam_resultpublishprint;

                $dt_data[] = $row;
            }

        }
        $json_data = array(
            "draw"            => intval($resultlist->draw),
            "recordsTotal"    => intval($resultlist->recordsTotal),
            "recordsFiltered" => intval($resultlist->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    /*
    function to get formparateter */
    public function getbookissueparameter()
    {
        $search_type  = $this->input->post('search_type');
        $members_type = $this->input->post("members_type");
        $date_from    = "";
        $date_to      = "";
        if ($search_type == 'period') {
            $date_from = $this->input->post('date_from');
            $date_to   = $this->input->post('date_to');
        }

        $params = array('search_type' => $search_type, 'members_type' => $members_type, 'date_from' => $date_from, 'date_to' => $date_to);
        $array  = array('status' => 1, 'error' => '', 'params' => $params);
        echo json_encode($array);

    }

    /* function to get book issue report by using datatable */
    public function dtbookissuereportlist()
    {
        $superadmin_visible = $this->customlib->superadmin_visible();
        $getStaffRole       = $this->customlib->getStaffRole();
        $staffrole          = json_decode($getStaffRole);
        $search_type = $this->input->post('search_type');
        $member_type = $this->input->post('date_type');
        $date_from   = $this->input->post('date_from');
        $date_to     = $this->input->post('date_to');

        $data['searchlist'] = $this->customlib->get_searchtype();
        if (isset($_POST['search_type']) && $_POST['search_type'] != '') {

            $dates               = $this->customlib->get_betweendate($_POST['search_type']);
            $data['search_type'] = $_POST['search_type'];

        } else {

            $dates               = $this->customlib->get_betweendate('this_year');
            $data['search_type'] = '';

        }

        $data['members'] = array('' => $this->lang->line('all'), 'student' => $this->lang->line('student'), 'teacher' => $this->lang->line('teacher'));
        $start_date      = date('Y-m-d', strtotime($dates['from_date']));
        $end_date        = date('Y-m-d', strtotime($dates['to_date']));
        $data['label']   = date($this->customlib->getSchoolDateFormat(), strtotime($start_date)) . " " . $this->lang->line('to') . " " . date($this->customlib->getSchoolDateFormat(), strtotime($end_date));

        $result = $this->bookissue_model->studentBookIssue_report($start_date, $end_date);
        $sch_setting = $this->sch_setting_detail;
        $resultlist = json_decode($result);
        $dt_data    = array();

        if (!empty($resultlist->data)) {
            foreach ($resultlist->data as $resultlist_key => $value) {

                $row   = array();
                $row[] = $value->book_title;
                $row[] = $value->book_no;
                $row[] = date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($value->issue_date));
                $row[] = date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($value->duereturn_date));
                $row[] = $value->members_id;
                $row[] = $value->library_card_no;
                
                if ($value->admission) {
                    $admission = ' (' . $value->admission . ')';
                    $row[]     = $value->admission;
                } else {
                    $admission = '';
                    $row[]     = "";
                }

                if ($value->employee_id) {
                    $staff_employee_id = ' (' . $value->employee_id . ')';
                } else {
                    $staff_employee_id = '';
                }

                if ($value->member_type == 'student') {
                    $row[] = $this->customlib->getFullName($value->firstname, $value->middlename, $value->lastname, $sch_setting->middlename, $sch_setting->lastname) . $admission;
                } else {

                    if ($superadmin_visible == 'disabled') {
                        if ($staffrole->id != 7) {
                            $staffresult = $this->staff_model->getAll($value->staff_id);

                            if (isset($staffresult['role_id']) && $staffresult['role_id'] == 7) {
                                $row[] = '';
                            } else {
                                $row[] = ucwords($value->staff_name) . $staff_employee_id;
                            }

                        } else {
                            $row[] = ucwords($value->staff_name) . $staff_employee_id;
                        }
                    } else {
                        $row[] = ucwords($value->staff_name) . $staff_employee_id;
                    }

                }
                $row[] = ucwords($value->member_type);

                $dt_data[] = $row;
            }

        }
        $json_data = array(
            "draw"            => intval($resultlist->draw),
            "recordsTotal"    => intval($resultlist->recordsTotal),
            "recordsFiltered" => intval($resultlist->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    /* function to get book due report by using datatable */
    public function dtbookduereportlist()
    {
        $search_type = $this->input->post('search_type');
        $member_type = $this->input->post('date_type');
        $date_from   = $this->input->post('date_from');
        $date_to     = $this->input->post('date_to');

        $superadmin_visible = $this->customlib->superadmin_visible();
        $getStaffRole       = $this->customlib->getStaffRole();
        $staffrole          = json_decode($getStaffRole);

        if (isset($_POST['search_type']) && $_POST['search_type'] != '') {

            $dates               = $this->customlib->get_betweendate($_POST['search_type']);
            $data['search_type'] = $_POST['search_type'];

        } else {

            $dates               = $this->customlib->get_betweendate('this_year');
            $data['search_type'] = '';

        }

        if (isset($_POST['members_type']) && $_POST['members_type'] != '') {
            $data['member_id'] = $_POST['members_type'];
        } else {
            $data['member_id'] = '';
        }

        $data['members'] = array('' => $this->lang->line('all'), 'student' => $this->lang->line('student'), 'teacher' => $this->lang->line('teacher'));

        $start_date    = date('Y-m-d', strtotime($dates['from_date']));
        $end_date      = date('Y-m-d', strtotime($dates['to_date']));
        $data['label'] = date($this->customlib->getSchoolDateFormat(), strtotime($start_date)) . " " . $this->lang->line('to') . " " . date($this->customlib->getSchoolDateFormat(), strtotime($end_date));
        $issued_books  = $this->bookissue_model->bookduereport($start_date, $end_date);
        $sch_setting = $this->sch_setting_detail;
        $resultlist = json_decode($issued_books);
        $dt_data    = array();
        if (!empty($resultlist->data)) {
            foreach ($resultlist->data as $resultlist_key => $value) {

                $row   = array();
                $row[] = $value->book_title;
                $row[] = $value->book_no;
                $row[] = date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($value->issue_date));
                $row[] = date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($value->duereturn_date));
                $row[] = $value->members_id;
                $row[] = $value->library_card_no;
                if ($value->admission != 0) {
                    $row[] = $value->admission;
                } else {
                    $row[] = "";
                }
                if ($value->member_type == 'student') {
                    $row[] = $this->customlib->getFullName($value->firstname, $value->middlename, $value->lastname, $sch_setting->middlename, $sch_setting->lastname) . ' (' . $value->admission . ')';
                } else {

                    if (!empty($value->employee_id)) {

                        if ($superadmin_visible == 'disabled') {
                            if ($staffrole->id != 7) {
                                $staffresult = $this->staff_model->getAll($value->staff_id);
                                if ($staffresult['role_id'] == 7) {
                                    $row[] = '';
                                } else {
                                    $row[] = ucwords($value->fname) . " " . ucwords($value->lname) . ' (' . $value->employee_id . ')';
                                }

                            } else {
                                $row[] = ucwords($value->fname) . " " . ucwords($value->lname) . ' (' . $value->employee_id . ')';
                            }
                        } else {
                            $row[] = ucwords($value->fname) . " " . ucwords($value->lname) . ' (' . $value->employee_id . ')';
                        }

                    } else {
                        $row[] = '';
                    }

                }
                $row[] = ucwords($value->member_type);

                $dt_data[] = $row;
            }

        }
        $json_data = array(
            "draw"            => intval($resultlist->draw),
            "recordsTotal"    => intval($resultlist->recordsTotal),
            "recordsFiltered" => intval($resultlist->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    /* function to get book issue return report by using datatable */
    public function dtbookinventoryreportlist()
    {
        $search_type = $this->input->post('search_type');
        $date_from = $this->input->post('date_from');
        $date_to   = $this->input->post('date_to');

        if (isset($_POST['search_type']) && $_POST['search_type'] != '') {

            $dates               = $this->customlib->get_betweendate($_POST['search_type']);
            $data['search_type'] = $_POST['search_type'];

        } else {

            $dates               = $this->customlib->get_betweendate('this_year');
            $data['search_type'] = '';

        }
        $currency_symbol = $this->customlib->getSchoolCurrencyFormat();

        $start_date    = date('Y-m-d', strtotime($dates['from_date']));
        $end_date      = date('Y-m-d', strtotime($dates['to_date']));
        $data['label'] = date($this->customlib->getSchoolDateFormat(), strtotime($start_date)) . " " . $this->lang->line('to') . " " . date($this->customlib->getSchoolDateFormat(), strtotime($end_date));

        $listbook = $this->book_model->bookinventory($start_date, $end_date);

        $resultlist = json_decode($listbook);
        $dt_data    = array();

        if (!empty($resultlist->data)) {

            $editbtn   = "";
            $deletebtn = "";
            foreach ($resultlist->data as $resultlist_key => $value) {

                
                $condition = "<p class='text text-info no-print' >" . $value->description . "</p>";
                

                $title = "<a href='#' data-toggle='popover' class='detail_popover'>" . $value->book_title . "</a> <div class='fee_detail_popover' style='display: none'> " . $condition . " </div> ";

                $row   = array();
                $row[] = $title;
                $row[] = $value->book_no;
                $row[] = $value->isbn_no;
                $row[] = $value->publish;
                $row[] = $value->author;
                $row[] = $value->subject;
                $row[] = $value->rack_no;
                $row[] = $value->qty;
                $row[] = $value->qty - $value->total_issue;
                $row[]     = $value->qty - ($value->qty - $value->total_issue);
                $row[]     = ($currency_symbol . amountFormat($value->perunitcost));
                $row[]     = date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($value->postdate));
                $row[]     = $editbtn . " " . $deletebtn;
                $dt_data[] = $row;
            }

        }
        $json_data = array(
            "draw"            => intval($resultlist->draw),
            "recordsTotal"    => intval($resultlist->recordsTotal),
            "recordsFiltered" => intval($resultlist->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    /*
    this function is used to get and return  form parameter without applying any validation  */
    public function getsearchtypeparam()
    {
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

    public function online_admission_report()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/online_admission');
        $this->session->set_userdata('subsub_menu', 'Reports/online_admission');
        $class             = $this->class_model->get();
        $data['classlist'] = $class;
        $this->load->view('layout/header', $data);
        $this->load->view('reports/online_admission_report', $data);
        $this->load->view('layout/footer', $data);

    }

    public function checkvalidation()
    {
        $class_id   = $this->input->post('class_id');
        $section_id = $this->input->post('section_id');
        $status     = $this->input->post('status');
        $params     = array('class_id' => $class_id, 'section_id' => $section_id, 'status' => $status);
        $array      = array('status' => 1, 'error' => '', 'params' => $params);
        echo json_encode($array);
    }

    public function dtonlineadmissionreportlist()
    {
        $class_id   = $this->input->post("class_id");
        $section_id = $this->input->post("section_id");
        $status     = $this->input->post("status");
        $sch_setting = $this->sch_setting_detail;
        $result          = $this->student_model->getonlineadmissionreport($class_id, $section_id, $status);
        $resultlist      = json_decode($result);
        $dt_data         = array();
        $currency_symbol = $this->customlib->getSchoolCurrencyFormat();
        if (!empty($resultlist->data)) {
            foreach ($resultlist->data as $resultlist_key => $online_admission) {

                $dob                      = "";
                $category                 = "";
                $online_admission_payment = '';

                if ($online_admission->form_status == 1) {
                    $form_status = '<span class="label label-success">' . $this->lang->line('submitted') . '</span>';
                } else if ($online_admission->form_status == 0) {
                    $form_status = '<span class="label label-danger">' . $this->lang->line('not_submitted') . '</span>';
                }

                if ($online_admission->dob != null) {
                    $dob = date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($online_admission->dob));
                }

                if ($sch_setting->online_admission_payment == 'yes') {
                    if ($online_admission->paid_status == 1) {
                        $online_admission_payment = '<span class="label label-success">' . $this->lang->line('paid') . '</span>';
                    } elseif ($online_admission->paid_status == 2) {
                        $online_admission_payment = '<span class="label label-info">' . $this->lang->line('processing') . '</span>';
                    } else {
                        $online_admission_payment = '<span class="label label-danger">' . $this->lang->line('unpaid') . '</span>';
                    }
                }

                if (($online_admission->is_enroll)) {
                    $enroll = "<i class='fa fa-check'></i><span style='display:none'>" . $this->lang->line('yes') . "</span>";
                } else {
                    $enroll = "<i class='fa fa-minus-circle'></i><span style='display:none'>" . $this->lang->line('no') . "</span>";
                }

                $row   = array();
                $row[] = $online_admission->reference_no;
                $row[] = $online_admission->admission_no;
                $row[] = $this->customlib->getFullName($online_admission->firstname, $online_admission->middlename, $online_admission->lastname, $this->sch_setting_detail->middlename, $this->sch_setting_detail->lastname);

                $row[] = $online_admission->class . "(" . $online_admission->section . ")";
                $row[] = $online_admission->mobileno;
                $row[] = $dob;
                $row[] = $this->lang->line(strtolower($online_admission->gender));
                $row[] = $form_status;
                $row[] = $online_admission_payment;
                $row[] = $enroll;
                $row[] = $currency_symbol . amountFormat($online_admission->paid_amount);

                $dt_data[] = $row;
            }

        }
        $json_data = array(
            "draw"            => intval($resultlist->draw),
            "recordsTotal"    => intval($resultlist->recordsTotal),
            "recordsFiltered" => intval($resultlist->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }  
        
    public function studentreport()
    {
        if (!$this->rbac->hasPrivilege('student_report', 'can_view')) {
            access_denied();
        }    
   
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/student_information');
        $this->session->set_userdata('subsub_menu', 'Reports/student_information/student_report');
        $data['title']           = 'student fee';
        $genderList              = $this->customlib->getGender();
        $data['genderList']      = $genderList;
        $RTEstatusList           = $this->customlib->getRteStatus();
        $data['RTEstatusList']   = $RTEstatusList;
        $class                   = $this->class_model->get();
        $data['classlist']       = $class;
        $category                = $this->category_model->get();
        $data['categorylist']    = $category;
        $data['sch_setting']     = $this->sch_setting_detail;
        $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;
        $userdata                = $this->customlib->getUserData();
        $category                = $this->category_model->get();
        $data['categorylist']    = $category;
        $this->load->view('layout/header', $data);
        $this->load->view('reports/studentReport', $data);
        $this->load->view('layout/footer', $data);

    }    
    
    public function studentreportvalidation()
    {
        // Handle multi-select values - convert to arrays if needed
        $class_id    = $this->input->post('class_id');
        $section_id  = $this->input->post('section_id');
        $category_id = $this->input->post('category_id');
        $gender      = $this->input->post('gender');
        $rte         = $this->input->post('rte');

        // Convert single values to arrays for consistency
        if (!is_array($class_id) && !empty($class_id)) {
            $class_id = array($class_id);
        }
        if (!is_array($section_id) && !empty($section_id)) {
            $section_id = array($section_id);
        }
        if (!is_array($category_id) && !empty($category_id)) {
            $category_id = array($category_id);
        }
        if (!is_array($gender) && !empty($gender)) {
            $gender = array($gender);
        }
        if (!is_array($rte) && !empty($rte)) {
            $rte = array($rte);
        }

        $srch_type = $this->input->post('search_type');

        if ($srch_type == 'search_filter') {
            // Allow flexible report generation - no mandatory fields required
            // Users can generate reports with any combination of filters

            $params = array('srch_type' => $srch_type, 'class_id' => $class_id, 'section_id' => $section_id, 'category_id' => $category_id, 'gender' => $gender, 'rte' => $rte);
            $array  = array('status' => 1, 'error' => '', 'params' => $params);
            echo json_encode($array);
        } else {
            $params = array('srch_type' => 'search_full', 'class_id' => $class_id, 'section_id' => $section_id);
            $array  = array('status' => 1, 'error' => '', 'params' => $params);
            echo json_encode($array);
        }
    }
    
    public function dtstudentreportlist()
    {
        $currency_symbol = $this->customlib->getSchoolCurrencyFormat();
        $class           = $this->input->post('class_id');
        $section         = $this->input->post('section_id');
        $category_id     = $this->input->post('category_id');
        $gender          = $this->input->post('gender');
        $rte             = $this->input->post('rte');
        $sch_setting     = $this->sch_setting_detail;

        // Handle both single and multi-select values properly
        // When multiple values are selected, they come as arrays
        // When single values are selected, they come as strings
        // Convert single values to arrays for consistent processing
        if (!is_array($class)) {
            $class = !empty($class) ? array($class) : array();
        }
        if (!is_array($section)) {
            $section = !empty($section) ? array($section) : array();
        }
        if (!is_array($category_id)) {
            $category_id = !empty($category_id) ? array($category_id) : array();
        }
        if (!is_array($gender)) {
            $gender = !empty($gender) ? array($gender) : array();
        }
        if (!is_array($rte)) {
            $rte = !empty($rte) ? array($rte) : array();
        }

        // Remove empty values from arrays
        $class = array_filter($class, function($value) { return !empty($value); });
        $section = array_filter($section, function($value) { return !empty($value); });
        $category_id = array_filter($category_id, function($value) { return !empty($value); });
        $gender = array_filter($gender, function($value) { return !empty($value); });
        $rte = array_filter($rte, function($value) { return !empty($value); });

        $result     = $this->student_model->searchdatatableByClassSectionCategoryGenderRte($class, $section, $category_id, $gender, $rte);
        $resultlist = json_decode($result);
        $dt_data    = array();
        if (!empty($resultlist->data)) {
            foreach ($resultlist->data as $resultlist_key => $student) {

                $viewbtn = "<a  href='" . base_url() . "student/view/" . $student->id . "'>" . $this->customlib->getFullName($student->firstname, $student->middlename, $student->lastname, $sch_setting->middlename, $sch_setting->lastname) . "</a>";

                $row   = array();
                $row[] = $student->section;
                $row[] = $student->admission_no;
                $row[] = $viewbtn;
                if ($sch_setting->father_name) {
                    $row[] = $student->father_name;
                }
                if ($student->dob != null && $student->dob != '0000-00-00') {
                    $row[] = date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($student->dob));
                } else {
                    $row[] = "";
                }
                $row[] = $this->lang->line(strtolower($student->gender));

                if ($sch_setting->category) {
                    $row[] = $student->category;
                }
                if ($sch_setting->mobile_no) {
                    $row[] = $student->mobileno;
                }
                if ($sch_setting->national_identification_no) {
                    $row[] = $student->samagra_id;
                }
                if ($sch_setting->local_identification_no) {
                    $row[] = $student->adhar_no;
                }
                if ($sch_setting->rte) {
                    $row[] = $student->rte;
                }

                $dt_data[] = $row;
            }

        }
        $json_data = array(
            "draw"            => intval($resultlist->draw),
            "recordsTotal"    => intval($resultlist->recordsTotal),
            "recordsFiltered" => intval($resultlist->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }
    
     public function classsectionreport()
    {
        if (!$this->rbac->hasPrivilege('student_report', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/student_information');
        $this->session->set_userdata('subsub_menu', 'Reports/student_information/classsectionreport');
        $data['title']              = 'Class & Section Report';
        $data['class_section_list'] = $this->classsection_model->getClassSectionStudentCount();
        $this->load->view('layout/header', $data);
        $this->load->view('reports/classsectionreport', $data);
        $this->load->view('layout/footer', $data);
    }
    
    public function guardianreport()
    {
        if (!$this->rbac->hasPrivilege('guardian_report', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/student_information');
        $this->session->set_userdata('subsub_menu', 'Reports/student_information/guardian_report');
        $data['title']           = 'Student Guardian Report';
        $class                   = $this->class_model->get();
        $data['classlist']       = $class;
        $data['sch_setting']     = $this->sch_setting_detail;
        $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;
        $userdata                = $this->customlib->getUserData();
        $carray                  = array();

        if (!empty($data["classlist"])) {
            foreach ($data["classlist"] as $ckey => $cvalue) {

                $carray[] = $cvalue["id"];
            }
        }

        $resultlist         = $this->student_model->studentGuardianDetails($carray);
        $data["resultlist"] = "";

        $this->load->view("layout/header", $data);
        $this->load->view("reports/guardianReport", $data);
        $this->load->view("layout/footer", $data);
    }

    public function guardiansearchvalidation()
    {
        // Enhanced error logging and debugging
        error_log('=== GUARDIAN SEARCH VALIDATION STARTED ===');
        error_log('POST data: ' . print_r($_POST, true));
        error_log('Request method: ' . $_SERVER['REQUEST_METHOD']);
        error_log('Content type: ' . (isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : 'not set'));

        // Handle multi-select values - convert to arrays if needed
        $class_id    = $this->input->post('class_id');
        $section_id  = $this->input->post('section_id');
        $srch_type = $this->input->post('search_type');

        // Enhanced debug logging
        error_log('Guardian search validation - Raw input: class_id=' . print_r($class_id, true) . ', section_id=' . print_r($section_id, true) . ', search_type=' . $srch_type);
        log_message('debug', 'Guardian search validation - Raw input: class_id=' . print_r($class_id, true) . ', section_id=' . print_r($section_id, true) . ', search_type=' . $srch_type);

        // Convert single values to arrays for consistency
        if (!is_array($class_id) && !empty($class_id)) {
            $class_id = array($class_id);
        }
        if (!is_array($section_id) && !empty($section_id)) {
            $section_id = array($section_id);
        }

        error_log('Guardian search validation - Processed: class_id=' . print_r($class_id, true) . ', section_id=' . print_r($section_id, true));
        log_message('debug', 'Guardian search validation - Processed: class_id=' . print_r($class_id, true) . ', section_id=' . print_r($section_id, true));

        try {
            if ($srch_type == 'search_filter') {
                // No mandatory validation - allow flexible report generation
                $params = array('srch_type' => $srch_type, 'class_id' => $class_id, 'section_id' => $section_id);
                $array  = array('status' => 1, 'error' => '', 'params' => $params);
                error_log('Guardian search validation - Success response: ' . json_encode($array));
                log_message('debug', 'Guardian search validation - Success response: ' . json_encode($array));

                // Set proper JSON header
                header('Content-Type: application/json');
                echo json_encode($array);
            } else {
                // Handle other search types like the Student Report does
                $params = array('srch_type' => 'search_full', 'class_id' => $class_id, 'section_id' => $section_id);
                $array  = array('status' => 1, 'error' => '', 'params' => $params);
                error_log('Guardian search validation - Full search response: ' . json_encode($array));
                log_message('debug', 'Guardian search validation - Full search response: ' . json_encode($array));

                // Set proper JSON header
                header('Content-Type: application/json');
                echo json_encode($array);
            }
        } catch (Exception $e) {
            error_log('Guardian search validation - Exception: ' . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(array('status' => 0, 'error' => array('general' => 'Server error occurred')));
        }
    }

    public function dtguardianreportlist()
    {
        // Enhanced error logging and debugging
        error_log('=== GUARDIAN DATATABLE REQUEST STARTED ===');
        error_log('POST data: ' . print_r($_POST, true));
        error_log('Request method: ' . $_SERVER['REQUEST_METHOD']);

        $class_id   = $this->input->post('class_id');
        $section_id = $this->input->post('section_id');
        $sch_setting = $this->sch_setting_detail;

        // Enhanced debug logging
        error_log('Guardian DataTable - Raw input: class_id=' . print_r($class_id, true) . ', section_id=' . print_r($section_id, true));
        log_message('debug', 'Guardian DataTable - Raw input: class_id=' . print_r($class_id, true) . ', section_id=' . print_r($section_id, true));

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

        error_log('Guardian DataTable - Processed: class_id=' . print_r($class_id, true) . ', section_id=' . print_r($section_id, true));
        log_message('debug', 'Guardian DataTable - Processed: class_id=' . print_r($class_id, true) . ', section_id=' . print_r($section_id, true));

        try {
            error_log('Guardian DataTable - Calling model method...');
            $result = $this->student_model->searchdatatablebyGuardianDetails($class_id, $section_id);
            error_log('Guardian DataTable - Model result length: ' . strlen($result));
            error_log('Guardian DataTable - Model result preview: ' . substr($result, 0, 200) . '...');
            log_message('debug', 'Guardian DataTable - Model result: ' . $result);

            $resultlist = json_decode($result);
            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log('Guardian DataTable - JSON decode error: ' . json_last_error_msg());
                throw new Exception('JSON decode failed: ' . json_last_error_msg());
            }
            error_log('Guardian DataTable - Decoded result: ' . print_r($resultlist, true));
        } catch (Exception $e) {
            error_log('Guardian DataTable - Exception: ' . $e->getMessage());
            $resultlist = (object)array('data' => array(), 'draw' => 1, 'recordsTotal' => 0, 'recordsFiltered' => 0);
        }

        $dt_data = array();
        if (!empty($resultlist->data)) {
            foreach ($resultlist->data as $student) {
                $row = array();
                $row[] = $student->class . " (" . $student->section . ")";
                $row[] = $student->admission_no;
                $row[] = '<a href="' . base_url() . 'student/view/' . $student->id . '">' .
                         $this->customlib->getFullName($student->firstname, $student->middlename, $student->lastname, $sch_setting->middlename, $sch_setting->lastname) . '</a>';

                if ($sch_setting->mobile_no) {
                    $row[] = $student->mobileno;
                }
                if ($sch_setting->guardian_name) {
                    $row[] = $student->guardian_name;
                }
                if ($sch_setting->guardian_relation) {
                    $row[] = $student->guardian_relation;
                }
                if ($sch_setting->guardian_phone) {
                    $row[] = $student->guardian_phone;
                }
                if ($sch_setting->father_name) {
                    $row[] = $student->father_name;
                }
                if ($sch_setting->father_phone) {
                    $row[] = $student->father_phone;
                }
                if ($sch_setting->mother_name) {
                    $row[] = $student->mother_name;
                }
                if ($sch_setting->mother_phone) {
                    $row[] = $student->mother_phone;
                }

                $dt_data[] = $row;
            }
        }

        $json_data = array(
            "draw"            => intval($resultlist->draw),
            "recordsTotal"    => intval($resultlist->recordsTotal),
            "recordsFiltered" => intval($resultlist->recordsFiltered),
            "data"            => $dt_data,
        );

        error_log('Guardian DataTable - Final JSON data: ' . print_r($json_data, true));
        error_log('Guardian DataTable - Data rows count: ' . count($dt_data));

        // Set proper JSON header
        header('Content-Type: application/json');
        echo json_encode($json_data);
    }

    public function admissionreport()
    {
        if (!$this->rbac->hasPrivilege('student_history', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/student_information');
        $this->session->set_userdata('subsub_menu', 'Reports/student_information/student_history');
        $data['title'] = 'Admission Report';

        $class                   = $this->class_model->get();
        $data['classlist']       = $class;
        $userdata                = $this->customlib->getUserData();
        $data['sch_setting']     = $this->sch_setting_detail;
        $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;
        $carray                  = array();

        if (!empty($data["classlist"])) {
            foreach ($data["classlist"] as $ckey => $cvalue) {

                $carray[] = $cvalue["id"];
            }
        }

        $admission_year         = $this->student_model->admissionYear();
        $data["admission_year"] = $admission_year;
        $this->load->view("layout/header", $data);
        $this->load->view("reports/admissionReport", $data);
        $this->load->view("layout/footer", $data);
    }
    
    public function admissionsearchvalidation()
    {
        $class_id = $this->input->post('class_id');
        $year     = $this->input->post('year');

        // Remove required validation for flexible filtering - allow empty class selection
        $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|xss_clean');
        if ($this->form_validation->run() == false) {
            $error = array();

            $error['class_id'] = form_error('class_id');
            $array             = array('status' => 0, 'error' => $error);
            echo json_encode($array);
        } else {

            $params = array('class_id' => $class_id, 'year' => $year);
            $array  = array('status' => 1, 'error' => '', 'params' => $params);
            echo json_encode($array);
        }
    }
    
    public function dtadmissionreportlist()
    {
        $currency_symbol = $this->customlib->getSchoolCurrencyFormat();
        $class_id        = $this->input->post('class_id');
        $year            = $this->input->post('year');

        // Handle multi-select arrays - convert to comma-separated strings for model
        if (is_array($class_id)) {
            $class_id = implode(',', array_map('intval', $class_id));
        }

        $sch_setting = $this->sch_setting_detail;
        $result      = $this->student_model->searchdatatablebyAdmissionDetails($class_id, $year);
        $resultlist  = json_decode($result);

        $dt_data = array();
        if (!empty($resultlist->data)) {
            foreach ($resultlist->data as $resultlist_key => $student) {

                $id            = $student->sid;
                $sessionlist   = $this->student_model->studentSessionDetails($id);
                $startsession  = $sessionlist['start'];
                $findstartyear = explode("-", $startsession);
                $startyear     = $findstartyear[0];
                $endsession    = $sessionlist['end'];
                $findendyear   = explode("-", $endsession);
                $endyear       = $findendyear[0];

                $viewbtn = "<a  href='" . base_url() . "student/view/" . $student->sid . "'>" . $this->customlib->getFullName($student->firstname, $student->middlename, $student->lastname, $sch_setting->middlename, $sch_setting->lastname) . "</a>";

                $row   = array();
                $row[] = $student->admission_no;
                $row[] = $viewbtn;

                if ($student->admission_date != null && $student->admission_date != '0000-00-00') {
                    $row[] = date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($student->admission_date));
                } else {
                    $row[] = "";
                }
                $row[] = $sessionlist['startclass'] . "  -  " . $sessionlist['endclass'];
                $row[] = $sessionlist['start'] . "  -  " . $sessionlist['end'];
                $row[] = ($endyear - $startyear) + 1;

                if ($sch_setting->mobile_no) {
                    $row[] = $student->mobileno;
                }

                if ($sch_setting->guardian_name) {
                    $row[] = $student->guardian_name;
                }

                if ($sch_setting->guardian_phone) {
                    $row[] = $student->guardian_phone;
                }

                $dt_data[] = $row;
            }

        }
        $json_data = array(
            "draw"            => intval($resultlist->draw),
            "recordsTotal"    => intval($resultlist->recordsTotal),
            "recordsFiltered" => intval($resultlist->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }
    
    public function logindetailreport()
    {
        if (!$this->rbac->hasPrivilege('student_login_credential_report', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/student_information');
        $this->session->set_userdata('subsub_menu', 'Reports/student_information/student_login_credential');
        $class                   = $this->class_model->get();
        $data['classlist']       = $class;
        $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;

        $this->load->view("layout/header");
        $this->load->view("reports/logindetailreport", $data);
        $this->load->view("layout/footer");
    }
    
     public function searchloginvalidation()
    {
        // Handle multi-select values - convert to arrays if needed
        $class_id   = $this->input->post('class_id');
        $section_id = $this->input->post('section_id');

        // Convert single values to arrays for consistency
        if (!is_array($class_id) && !empty($class_id)) {
            $class_id = array($class_id);
        }
        if (!is_array($section_id) && !empty($section_id)) {
            $section_id = array($section_id);
        }

        // Remove required validation for flexible filtering - allow empty class and section selection
        $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|xss_clean');
        $this->form_validation->set_rules('section_id', $this->lang->line('section'), 'trim|xss_clean');

        if ($this->form_validation->run() == false) {
            $error = array();

            $error['class_id']   = form_error('class_id');
            $error['section_id'] = form_error('section_id');

            $array = array('status' => 0, 'error' => $error);
            echo json_encode($array);
        } else {

            $params = array('class_id' => $class_id, 'section_id' => $section_id);
            $array  = array('status' => 1, 'error' => '', 'params' => $params);
            echo json_encode($array);
        }
    }
    
    public function dtcredentialreportlist()
    {
        $sch_setting = $this->sch_setting_detail;
        $class_id    = $this->input->post("class_id");
        $section_id  = $this->input->post("section_id");

        // Handle multi-select arrays - convert to comma-separated strings for model
        if (is_array($class_id)) {
            $class_id = implode(',', array_map('intval', $class_id));
        }
        if (is_array($section_id)) {
            $section_id = implode(',', array_map('intval', $section_id));
        }

        $result      = $this->student_model->getdtforlogincredential($class_id, $section_id);
        $resultlist  = json_decode($result);
        $dt_data     = array();

        if (!empty($resultlist->data)) {
            foreach ($resultlist->data as $resultlist_key => $student) {
                $studentlist      = $this->user_model->getUserLoginDetails($student->id);
                $student_username = $studentlist["username"];
                $student_password = $studentlist["password"];

                $viewbtn = "<a  href='" . base_url() . "student/view/" . $student->id . "'>" . $this->customlib->getFullName($student->firstname, $student->middlename, $student->lastname, $sch_setting->middlename, $sch_setting->lastname) . "</a>";

                $row   = array();
                $row[] = $student->admission_no;
                $row[] = $viewbtn;

                if (isset($student_username)) {
                    $row[] = $student_username;
                } else {
                    $row[] = "";
                }

                if (isset($student_password)) {
                    $row[] = $student_password;
                } else {
                    $row[] = "";
                }

                $dt_data[] = $row;
            }

        }
        $json_data = array(
            "draw"            => intval($resultlist->draw),
            "recordsTotal"    => intval($resultlist->recordsTotal),
            "recordsFiltered" => intval($resultlist->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }
    
    public function parentlogindetailreport()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/student_information');
        $this->session->set_userdata('subsub_menu', 'Reports/student_information/parent_login_credential');
        $class                   = $this->class_model->get();
        $data['classlist']       = $class;
        $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;

        $this->load->view("layout/header");
        $this->load->view("reports/parentlogindetailreport", $data);
        $this->load->view("layout/footer");
    }

    public function searchparentloginvalidation()
    {
        // Handle multi-select values - convert to arrays if needed
        $class_id   = $this->input->post('class_id');
        $section_id = $this->input->post('section_id');

        // Convert single values to arrays for consistency
        if (!is_array($class_id) && !empty($class_id)) {
            $class_id = array($class_id);
        }
        if (!is_array($section_id) && !empty($section_id)) {
            $section_id = array($section_id);
        }

        // Remove required validation for flexible filtering - allow empty class and section selection
        $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|xss_clean');
        $this->form_validation->set_rules('section_id', $this->lang->line('section'), 'trim|xss_clean');

        if ($this->form_validation->run() == false) {
            $error = array();

            $error['class_id']   = form_error('class_id');
            $error['section_id'] = form_error('section_id');

            $array = array('status' => 0, 'error' => $error);
            echo json_encode($array);
        } else {

            $params = array('class_id' => $class_id, 'section_id' => $section_id);
            $array  = array('status' => 1, 'error' => '', 'params' => $params);
            echo json_encode($array);
        }
    }

    public function dtparentcredentialreportlist()
    {
        $sch_setting = $this->sch_setting_detail;
        $class_id    = $this->input->post("class_id");
        $section_id  = $this->input->post("section_id");

        // Handle multi-select arrays - convert to comma-separated strings for model
        if (is_array($class_id)) {
            $class_id = implode(',', array_map('intval', $class_id));
        }
        if (is_array($section_id)) {
            $section_id = implode(',', array_map('intval', $section_id));
        }

        $result      = $this->student_model->getdtforlogincredential($class_id, $section_id);
        $resultlist  = json_decode($result);
        $dt_data     = array();

        if (!empty($resultlist->data)) {
            foreach ($resultlist->data as $resultlist_key => $student) {
                $parentlist      = $this->user_model->getParentLoginDetails($student->id);
                $parent_username = $parentlist["username"];
                $parent_password = $parentlist["password"];

                $viewbtn = "<a  href='" . base_url() . "student/view/" . $student->id . "'>" . $this->customlib->getFullName($student->firstname, $student->middlename, $student->lastname, $sch_setting->middlename, $sch_setting->lastname) . "</a>";

                $row   = array();
                $row[] = $student->admission_no;
                $row[] = $viewbtn;

                if (isset($parent_username)) {
                    $row[] = $parent_username;
                } else {
                    $row[] = "";
                }

                if (isset($parent_password)) {
                    $row[] = $parent_password;
                } else {
                    $row[] = "";
                }

                $dt_data[] = $row;
            }

        }
        $json_data = array(
            "draw"            => intval($resultlist->draw),
            "recordsTotal"    => intval($resultlist->recordsTotal),
            "recordsFiltered" => intval($resultlist->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    public function dtclasssubjectreport()
    {
        // Enhanced error logging and debugging
        error_log('=== CLASS SUBJECT DATATABLE REQUEST STARTED ===');
        error_log('POST data: ' . print_r($_POST, true));
        error_log('Request method: ' . $_SERVER['REQUEST_METHOD']);

        $sch_setting = $this->sch_setting_detail;
        $class_id    = $this->input->post("class_id");
        $section_id  = $this->input->post("section_id");

        // Enhanced debug logging
        error_log('Class Subject DataTable - Raw input: class_id=' . print_r($class_id, true) . ', section_id=' . print_r($section_id, true));

        // Handle multi-select arrays - ensure they are arrays
        if (!is_array($class_id)) {
            $class_id = !empty($class_id) ? array($class_id) : array();
        }
        if (!is_array($section_id)) {
            $section_id = !empty($section_id) ? array($section_id) : array();
        }

        // Filter out empty values
        $class_id = array_filter($class_id, function($value) { return !empty($value); });
        $section_id = array_filter($section_id, function($value) { return !empty($value); });

        error_log('Class Subject DataTable - Processed: class_id=' . print_r($class_id, true) . ', section_id=' . print_r($section_id, true));

        try {
            error_log('Class Subject DataTable - Calling model method...');
            // Get subjects for all selected class/section combinations
            $resultlist = $this->subjecttimetable_model->getSubjectByClassandSection($class_id, $section_id);
            error_log('Class Subject DataTable - Model result count: ' . count($resultlist));
            error_log('Class Subject DataTable - Model result preview: ' . print_r(array_slice($resultlist, 0, 2), true));
        } catch (Exception $e) {
            error_log('Class Subject DataTable - Exception: ' . $e->getMessage());
            error_log('Class Subject DataTable - Exception trace: ' . $e->getTraceAsString());
            $resultlist = array();
        }

        $dt_data = array();
        if (!empty($resultlist)) {
            // Group subjects by subject_id to match the original display logic
            $subjects = array();
            foreach ($resultlist as $value) {
                $subjects[$value->subject_id][] = $value;
            }

            foreach ($subjects as $subject_group) {
                $row = array();
                $first_subject = $subject_group[0];

                // Class
                $row[] = $first_subject->class_name;

                // Section
                $row[] = $first_subject->section_name;

                // Subject (with code if available)
                $subject_display = $first_subject->subject_name;
                if (!empty($first_subject->code)) {
                    $subject_display .= ' (' . $first_subject->code . ')';
                }
                $row[] = $subject_display;

                // Teachers (with class teacher indication)
                $teachers_html = '';
                foreach ($subject_group as $teacher) {
                    $class_teacher = '';
                    if ($teacher->class_teacher == $teacher->staff_id) {
                        $class_teacher = ' <span class="label label-success">' . $this->lang->line('class_teacher') . '</span>';
                    }
                    $teachers_html .= $teacher->name . " " . $teacher->surname . " (" . $teacher->employee_id . ")" . $class_teacher . "<br>";
                }
                $row[] = $teachers_html;

                // Time schedules
                $time_html = '';
                foreach ($subject_group as $teacher) {
                    $time_html .= $this->lang->line(strtolower($teacher->day)) . " " . $teacher->time_from . " To " . $teacher->time_to . "<br>";
                }
                $row[] = $time_html;

                // Room numbers
                $room_html = '';
                foreach ($subject_group as $teacher) {
                    $room_html .= $teacher->room_no . "<br>";
                }
                $row[] = $room_html;

                $dt_data[] = $row;
            }
        }

        // For DataTable server-side processing, we need to handle pagination parameters
        $draw = intval($this->input->post('draw'));
        $start = intval($this->input->post('start'));
        $length = intval($this->input->post('length'));

        $total_records = count($dt_data);

        // Apply pagination
        if ($length > 0) {
            $dt_data = array_slice($dt_data, $start, $length);
        }

        $json_data = array(
            "draw"            => $draw,
            "recordsTotal"    => $total_records,
            "recordsFiltered" => $total_records,
            "data"            => $dt_data,
        );

        error_log('Class Subject DataTable - Final JSON data rows count: ' . count($dt_data));

        // Set proper JSON header
        header('Content-Type: application/json');
        echo json_encode($json_data);
    }

}
