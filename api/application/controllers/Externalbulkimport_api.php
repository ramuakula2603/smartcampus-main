<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Externalbulkimport_api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('addpublicresult_model');
        $this->load->model('examtype_model');
        $this->load->model('setting_model');
        $this->load->helper('json_output');
    }

    private function validate_headers()
    {
        $client_service = $this->input->get_request_header('Client-Service', TRUE);
        $auth_key = $this->input->get_request_header('Auth-Key', TRUE);
        
        if ($client_service == 'smartschool' && $auth_key == 'schoolAdmin@') {
            return true;
        }
        return false;
    }

    /**
     * Download sample CSV file for import
     * 
     * @return void Outputs CSV file download
     */
    public function download_sample_file()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'GET') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            if ($this->validate_headers()) {
                // Define headers
                // Note: The sample file in the original controller uses 'import_student_admission_no.csv'
                // but the logic uses 'hall_no'. We will stick to 'hall_no' as per the analysis.
                // Also, we'll add placeholder subject codes as headers.
                $headers = array(
                    'hall_no',
                    'subject_code_1',
                    'subject_code_2',
                    'subject_code_3'
                );

                // Set headers for file download
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="sample_external_results_import.csv"');

                // Open output stream
                $fp = fopen('php://output', 'w');

                // Write headers
                fputcsv($fp, $headers);

                // Write sample data
                $sample_data = array(
                    array('HT1001', '85', '90', '75'),
                    array('HT1002', '90', '88', '92'),
                    array('HT1003', 'AB', '70', '80')
                );

                foreach ($sample_data as $row) {
                    fputcsv($fp, $row);
                }

                // Close output stream
                fclose($fp);
                exit;
            } else {
                json_output(401, array('status' => 401, 'message' => 'Client Service or Auth Key is invalid.'));
            }
        }
    }

    /**
     * Import external results from CSV file
     * 
     * @return void Outputs JSON response with import status
     */
    public function import_external_results()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            if ($this->validate_headers()) {
                // Check if file is uploaded
                if (!isset($_FILES['file']) || $_FILES['file']['error'] != UPLOAD_ERR_OK) {
                    json_output(400, array('status' => 400, 'message' => 'CSV file is required.'));
                    return;
                }

                // Check required parameters
                $exam_id = $this->input->post('exam_id');
                $academic_id = $this->input->post('academic_id');

                if (empty($exam_id)) {
                    json_output(400, array('status' => 400, 'message' => 'Exam ID (Result Type) is required.'));
                    return;
                }
                if (empty($academic_id)) {
                    json_output(400, array('status' => 400, 'message' => 'Academic Year ID is required.'));
                    return;
                }

                // Get subjects for this exam type and session
                $subjectsdata = $this->addpublicresult_model->subjectsgroupp($exam_id, $academic_id);
                
                if (empty($subjectsdata)) {
                    json_output(400, array('status' => 400, 'message' => 'No subjects found for this Exam Type and Academic Year.'));
                    return;
                }

                // Process CSV file
                $file_path = $_FILES['file']['tmp_name'];
                
                // Use str_getcsv to parse the file
                $csv_lines = file($file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                
                if (empty($csv_lines)) {
                    json_output(400, array('status' => 400, 'message' => 'CSV file is empty.'));
                    return;
                }

                // Parse header
                $header = str_getcsv(array_shift($csv_lines));
                
                // Normalize header: trim spaces and convert to lowercase for easier matching
                $header_map = array();
                foreach ($header as $index => $col_name) {
                    $header_map[trim($col_name)] = $index;
                }

                // Check for 'hall_no' column
                if (!isset($header_map['hall_no'])) {
                    json_output(400, array('status' => 400, 'message' => 'Invalid CSV format. "hall_no" column is required.'));
                    return;
                }

                $success_count = 0;
                $error_count = 0;
                $errors = array();
                $row_number = 1; // Starting after header

                foreach ($csv_lines as $line) {
                    $row_number++;
                    $row = str_getcsv($line);
                    
                    // Skip empty rows
                    if (empty($row)) {
                        continue;
                    }

                    // Get Hall Ticket No
                    $hall_no_index = $header_map['hall_no'];
                    $hall_no = isset($row[$hall_no_index]) ? trim($row[$hall_no_index]) : '';

                    if (empty($hall_no)) {
                        $errors[] = "Row $row_number: Missing Hall Ticket Number.";
                        $error_count++;
                        continue;
                    }

                    // Get Student ID from Hall Ticket No
                    $student_id = $this->addpublicresult_model->getstudentid($hall_no);

                    if (!$student_id) {
                        $errors[] = "Row $row_number: Student with Hall Ticket '$hall_no' not found.";
                        $error_count++;
                        continue;
                    }

                    $student_processed = false;

                    // Iterate through available subjects for this exam
                    foreach ($subjectsdata as $subject) {
                        $subject_code = $subject['subject_code'];
                        $sub_id = $subject['subid'];
                        
                        // Find if this subject exists in CSV header
                        // Strategy 1: Match by subject_code (e.g., "MATHS-1A")
                        // Strategy 2: Match by sub_id (e.g., "31")
                        
                        $marks = null;
                        $found = false;

                        if (isset($header_map[$subject_code]) && isset($row[$header_map[$subject_code]])) {
                            $marks = trim($row[$header_map[$subject_code]]);
                            $found = true;
                        } elseif (isset($header_map[$sub_id]) && isset($row[$header_map[$sub_id]])) {
                            $marks = trim($row[$header_map[$sub_id]]);
                            $found = true;
                        }

                        if ($found && $marks !== '') {
                            // Get marks ID (validates if subject is linked to exam)
                            $marks_id = $this->addpublicresult_model->getmarksidd($exam_id, $sub_id, $academic_id);

                            if ($marks_id) {
                                // Insert or Update Result
                                $data = array(
                                    'public_exam_id' => $exam_id,
                                    'student_id'     => $student_id,
                                    'subject_id'     => $sub_id,
                                    'marks'          => $marks,
                                    'session_id'     => $academic_id,
                                );
                                
                                $this->addpublicresult_model->add($data);
                                $student_processed = true;
                            }
                        }
                    }

                    if ($student_processed) {
                        $success_count++;
                    } else {
                        // No marks found for any valid subject for this student
                        // Not necessarily an error, but maybe a warning? 
                        // For now, we won't count it as an error unless we want strict validation.
                        // $errors[] = "Row $row_number: No valid marks found for student '$hall_no'.";
                    }
                }

                json_output(200, array(
                    'status' => 200,
                    'message' => 'Import completed.',
                    'data' => array(
                        'total_processed' => $success_count + $error_count,
                        'success_count' => $success_count,
                        'error_count' => $error_count,
                        'errors' => $errors
                    )
                ));
            } else {
                json_output(401, array('status' => 401, 'message' => 'Client Service or Auth Key is invalid.'));
            }
        }
    }
}
