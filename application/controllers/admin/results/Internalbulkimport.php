<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Internalbulkimport extends Admin_Controller
{

    public function __construct()
    {

        parent::__construct();
        $this->load->library('media_storage');
        $this->config->load('app-config');
        $this->load->library('mailsmsconf');
        $this->load->library('encoding_lib');
        $this->load->model('student_model');
        $this->load->model('examtype_model');
        $this->load->model('addresult_model');
        $this->current_session = $this->setting_model->getCurrentSession();
        $this->sch_setting_detail = $this->setting_model->getSetting();
        
    }


    public function import()
    {
        if (!$this->rbac->hasPrivilege('internal_bulk_import', 'can_view')) {
            access_denied();
        }
        $data['title']      = $this->lang->line('import_student');
        $data['title_list'] = $this->lang->line('recently_added_student');

        $userdata           = $this->customlib->getUserData();

        $category                   = $this->examtype_model->get();
        $data['categorylist']       = $category;

        $session                    = $this->examtype_model->sessions();
        $data['sessions']           = $session;

        $fields = array('admission_no','subject_code','subject_code','subject_code','subject_code');


        $data["fields"]       = $fields;

        $this->form_validation->set_rules('file', $this->lang->line('select_csv_file'), 'callback_handle_csv_upload');
        $this->form_validation->set_rules('academic_id', $this->lang->line('academic_year'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('exam_id', $this->lang->line('resut_type'), 'trim|required|xss_clean');


        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/results/internalresultbulkimport', $data);
            $this->load->view('layout/footer', $data);
        } else {

            $session = $this->setting_model->getCurrentSession();
            $examtype_id = $this->input->post('exam_id');
            $sessionid = $this->input->post('academic_id');

            $subjectsdata = $this->addresult_model->subjectsgroupp($examtype_id,$sessionid);

            // Debug: Log configured subjects with full details (remove after testing)
            $debug_subjects = array();
            $debug_full = array();
            foreach ($subjectsdata as $subj) {
                $debug_subjects[] = $subj['subject_code'];
                $debug_full[] = 'ID:' . $subj['subid'] . ' Code:' . $subj['subject_code'] . ' Name:' . (isset($subj['subject_name']) ? $subj['subject_name'] : 'N/A');
            }
            log_message('debug', 'Configured subject codes: ' . implode(', ', $debug_subjects));
            log_message('debug', 'Full subject details: ' . implode(' | ', $debug_full));


            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                if ($ext == 'csv') {
                    $file = $_FILES['file']['tmp_name'];
                    $this->load->library('CSVReader');
                    $result = $this->csvreader->parse_file($file);

                    // Debug: Log CSV column headers (remove after testing)
                    if (!empty($result) && isset($result[1])) {
                        $csv_columns = array_keys($result[1]);
                        log_message('debug', 'CSV column headers: ' . implode(', ', $csv_columns));
                    }

                    if (!empty($result)) {
                        $rowcount = 0;
                        $success_count = 0;
                        $failed_students = array();

                        // Start transaction for data integrity
                        $this->db->trans_start();

                        for ($i = 1; $i <= count($result); $i++) {

                            // Skip if row doesn't exist (array index issue)
                            if (!isset($result[$i])) {
                                continue;
                            }

                            // Support multiple column names: 'application_no', 'admission_no', 'admi_no'
                            $admi_no = '';
                            if (isset($result[$i]["application_no"]) && !empty($result[$i]["application_no"])) {
                                $admi_no = $result[$i]["application_no"];
                            } elseif (isset($result[$i]["admission_no"]) && !empty($result[$i]["admission_no"])) {
                                $admi_no = $result[$i]["admission_no"];
                            } elseif (isset($result[$i]["admi_no"]) && !empty($result[$i]["admi_no"])) {
                                $admi_no = $result[$i]["admi_no"];
                            }

                            // Debug: Log first row to see what columns are available
                            if ($i == 1 && empty($admi_no)) {
                                log_message('debug', 'Row 1 columns: ' . print_r(array_keys($result[$i]), true));
                                log_message('debug', 'Row 1 data: ' . print_r($result[$i], true));
                            }

                            // Skip if admission number is empty
                            if (empty($admi_no)) {
                                $failed_students[] = 'Row ' . $i . ' (Missing admission/application number)';
                                continue;
                            }

                            // Get student ID from admission number
                            $stidd = $this->examtype_model->getstudentid($admi_no);

                            // Skip if student not found
                            if (!$stidd || $stidd === false) {
                                $failed_students[] = $admi_no . ' (Student not found in database)';
                                log_message('debug', 'Student not found: ' . $admi_no);
                                continue;
                            }

                            // Track if at least one subject was imported for this student
                            $student_has_marks = false;
                            $subjects_processed = 0;
                            $subjects_with_marks = 0;
                            $subjects_not_found_in_csv = array();

                            // Process each subject for this student
                            foreach ($subjectsdata as $subject_code) {
                                $subjects_processed++;
                                $marksid = $this->examtype_model->getmarksid($examtype_id, $subject_code['subid'], $sessionid);

                                // Skip if marks ID not found (subject not configured for this result type)
                                if (!$marksid || $marksid === false) {
                                    continue;
                                }

                                // Try to find marks in CSV using multiple strategies:
                                // 1. Try subject_code (e.g., "JR-MATHS-1B")
                                // 2. Try subject_id (e.g., "32") - for CSV files using numeric IDs as column headers
                                $marks = null;
                                $found_column = null;

                                // Strategy 1: Try subject_code
                                if(isset($result[$i][$subject_code['subject_code']]) && $result[$i][$subject_code['subject_code']] !== ''){
                                    $marks = $result[$i][$subject_code['subject_code']];
                                    $found_column = $subject_code['subject_code'];
                                }
                                // Strategy 2: Try subject_id (for CSV files using numeric IDs as column headers)
                                elseif(isset($result[$i][$subject_code['subid']]) && $result[$i][$subject_code['subid']] !== ''){
                                    $marks = $result[$i][$subject_code['subid']];
                                    $found_column = $subject_code['subid'];
                                }

                                // If marks found, insert/update the record
                                if($marks !== null){
                                    // Normalize marks - convert "ab" or "Ab" to "AB"
                                    $marks_normalized = trim($marks);
                                    if (strtoupper($marks_normalized) === 'AB') {
                                        $marks_normalized = 'AB';
                                    }

                                    $dataa = array(
                                        "stid" => $stidd,
                                        "resulgroup_id" => $examtype_id,
                                        "subjectid" => $subject_code['subid'],
                                        "actualmarks" => $marks_normalized,
                                        "markstableid" => $marksid,
                                        "session_id" => $sessionid
                                    );
                                    $this->examtype_model->updateresult($dataa);
                                    $student_has_marks = true;
                                    $subjects_with_marks++;
                                } else {
                                    // Track which subject codes/IDs are not found in CSV
                                    $subjects_not_found_in_csv[] = $subject_code['subject_code'];
                                }
                            }

                            // Debug logging for first student only
                            if ($i == 1) {
                                log_message('debug', 'First student (' . $admi_no . ') - Subjects processed: ' . $subjects_processed . ', Subjects with marks: ' . $subjects_with_marks);
                                if (!empty($subjects_not_found_in_csv)) {
                                    log_message('debug', 'Subject codes not found in CSV: ' . implode(', ', $subjects_not_found_in_csv));
                                }
                            }

                            // Only add to resultaddingstatus if student had at least one mark imported
                            if ($student_has_marks) {
                                // Check if student is already in resultaddingstatus table
                                $existing_status = $this->addresult_model->check_result_status($stidd, $examtype_id, $sessionid);

                                if (!$existing_status) {
                                    // Only insert if not exists, and set assign_status to 1 since we're importing results
                                    $data1=array(
                                        'stid' => $stidd,
                                        'resultype_id' => $examtype_id,
                                        'session_id' => $sessionid,
                                        'assign_status' => 1,
                                    );

                                    $in = $this->addresult_model->addresult($data1);
                                }

                                $success_count++;
                            } else {
                                // Provide more detailed failure reason
                                if ($subjects_processed == 0) {
                                    $failed_students[] = $admi_no . ' (No subjects configured for this result type)';
                                } else {
                                    // Show which subject codes were expected but not found in CSV
                                    if (!empty($subjects_not_found_in_csv) && count($failed_students) < 5) {
                                        $failed_students[] = $admi_no . ' (CSV missing columns: ' . implode(', ', array_slice($subjects_not_found_in_csv, 0, 3)) . ')';
                                    } else {
                                        $failed_students[] = $admi_no . ' (No marks found in CSV for any subject)';
                                    }
                                }
                            }
                        }

                        // Complete transaction
                        $this->db->trans_complete();

                        // Check transaction status
                        if ($this->db->trans_status() === FALSE) {
                            // Transaction failed, rollback happened automatically
                            $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">Import failed due to database error. Please try again.</div>');
                        } else {
                            // Transaction successful
                            $total_rows = count($result);
                            $failed_count = count($failed_students);

                            $message = '<div class="alert alert-success text-center">';
                            $message .= 'Import completed successfully!<br>';
                            $message .= 'Total rows in file: ' . $total_rows . '<br>';
                            $message .= 'Successfully imported: ' . $success_count . ' students<br>';

                            if ($failed_count > 0) {
                                $message .= 'Failed: ' . $failed_count . ' students<br>';
                                $message .= '<small>Failed students: ' . implode(', ', array_slice($failed_students, 0, 10));
                                if ($failed_count > 10) {
                                    $message .= ' and ' . ($failed_count - 10) . ' more...';
                                }
                                $message .= '</small>';
                            }

                            $message .= '</div>';
                            $this->session->set_flashdata('msg', $message);
                        }


                    } else {
                        $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">' . $this->lang->line('no_record_found') . '</div>');
                    }
                } else {
                    $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">' . $this->lang->line('please_upload_csv_file_only') . '</div>');
                }
            }
        
        redirect('admin/results/internalbulkimport/import');
    }
}





    public function handle_csv_upload()
    {
        $error = "";
        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
            $allowedExts = array('csv');
            $mimes       = array('text/csv',
                'text/plain',
                'application/csv',
                'text/comma-separated-values',
                'application/excel',
                'application/vnd.ms-excel',
                'application/vnd.msexcel',
                'text/anytext',
                'application/octet-stream',
                'application/txt');
            $temp      = explode(".", $_FILES["file"]["name"]);
            $extension = end($temp);
            if ($_FILES["file"]["error"] > 0) {
                $error .= "Error opening the file<br />";
            }
            if (!in_array($_FILES['file']['type'], $mimes)) {
                $error .= "Error opening the file<br />";
                $this->form_validation->set_message('handle_csv_upload', $this->lang->line('file_type_not_allowed'));
                return false;
            }
            if (!in_array($extension, $allowedExts)) {
                $error .= "Error opening the file<br />";
                $this->form_validation->set_message('handle_csv_upload', $this->lang->line('extension_not_allowed'));
                return false;
            }
            if ($error == "") {
                return true;
            }
        } else {
            $this->form_validation->set_message('handle_csv_upload', $this->lang->line('please_select_file'));
            return false;
        }
    }


    public function exportformat()
    {
        $this->load->helper('download');
        $filepath = "./backend/import/import_student_admission_no.csv";
        $data     = file_get_contents($filepath);
        $name     = 'import_student_admission_no.csv';

        force_download($name, $data);
    }



}



?>