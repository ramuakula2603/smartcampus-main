<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Internalbulkimport_api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('resultsubjects_model');
        $this->load->model('student_model');
        $this->load->model('examtype_model');
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
                $headers = array(
                    'admission_no',
                    'subject_code',
                    'marks',
                    'is_absent'
                );

                // Set headers for file download
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="sample_internal_results_import.csv"');

                // Open output stream
                $fp = fopen('php://output', 'w');

                // Write headers
                fputcsv($fp, $headers);

                // Write sample data
                $sample_data = array(
                    array('1001', '31', '85', '0'),
                    array('1002', '41', '90', '0'),
                    array('1003', '42', '0', '1')
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
     * Import internal results from CSV file
     * 
     * @return void Outputs JSON response with import status
     */
    public function import_internal_results()
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
                $result_type_id = $this->input->post('result_type_id');
                $session_id = $this->input->post('session_id');

                if (empty($result_type_id)) {
                    json_output(400, array('status' => 400, 'message' => 'Result Type ID is required.'));
                    return;
                }
                if (empty($session_id)) {
                    json_output(400, array('status' => 400, 'message' => 'Session ID is required.'));
                    return;
                }

                // Process CSV file
                $file_path = $_FILES['file']['tmp_name'];
                $csv_data = array_map('str_getcsv', file($file_path));
                
                // Remove header row
                $header = array_shift($csv_data);

                // Validate header structure (basic check)
                if (count($header) < 3) {
                    json_output(400, array('status' => 400, 'message' => 'Invalid CSV format. Required columns: admission_no, subject_code, marks.'));
                    return;
                }

                $success_count = 0;
                $error_count = 0;
                $errors = array();

                foreach ($csv_data as $row_index => $row) {
                    // Skip empty rows
                    if (empty($row) || empty($row[0])) {
                        continue;
                    }

                    $admission_no = trim($row[0]);
                    $subject_code = trim($row[1]);
                    $marks = trim($row[2]);
                    $is_absent = isset($row[3]) ? trim($row[3]) : '0';

                    // 1. Get Student ID from Admission No
                    $student = $this->student_model->findByAdmission($admission_no);
                    if (!$student) {
                        $errors[] = "Row " . ($row_index + 2) . ": Student with Admission No '$admission_no' not found.";
                        $error_count++;
                        continue;
                    }
                    $student_id = $student['id'];

                    // 2. Get Subject ID from Subject Code
                    $this->db->where('subject_code', $subject_code);
                    $subject_query = $this->db->get('resultsubjects');
                    if ($subject_query->num_rows() == 0) {
                        $errors[] = "Row " . ($row_index + 2) . ": Subject with Code '$subject_code' not found.";
                        $error_count++;
                        continue;
                    }
                    $subject = $subject_query->row_array();
                    $subject_id = $subject['id'];

                    // 3. Prepare Data for Insertion
                    $data = array(
                        'session_id' => $session_id,
                        'result_type_id' => $result_type_id,
                        'student_id' => $student_id,
                        'subject_id' => $subject_id,
                        'marks' => $marks,
                        'is_absent' => $is_absent
                    );

                    // 4. Check if result already exists (Update or Insert)
                    $this->db->where('session_id', $session_id);
                    $this->db->where('result_type_id', $result_type_id);
                    $this->db->where('student_id', $student_id);
                    $this->db->where('subject_id', $subject_id);
                    $existing_result = $this->db->get('internalresulttable');

                    if ($existing_result->num_rows() > 0) {
                        // Update
                        $result_row = $existing_result->row_array();
                        $this->db->where('id', $result_row['id']);
                        $this->db->update('internalresulttable', $data);
                    } else {
                        // Insert
                        $this->db->insert('internalresulttable', $data);
                    }

                    $success_count++;
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
