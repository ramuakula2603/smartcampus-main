<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Face_attendance_register extends Admin_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Face_attendance_student_model');
    }

    function index() {
        if (!$this->rbac->hasPrivilege('face_attendance_register', 'can_view')) {
            access_denied();
        }
        
        $this->session->set_userdata('top_menu', 'Attendance');
        $this->session->set_userdata('sub_menu', 'admin/face_attendance_register');
        
        $data['title'] = 'Face Attendance Registration';
        $data['students'] = $this->Face_attendance_student_model->get_all_students(1);
        
        $this->load->view('layout/header', $data);
        $this->load->view('admin/face_attendance_register/index', $data);
        $this->load->view('layout/footer', $data);
    }

    /**
     * Check if registration number exists (AJAX)
     */
    public function check_registration() {
        $registration_number = $this->input->post('registration_number');
        
        if (empty($registration_number)) {
            echo json_encode(array('status' => 'error', 'message' => 'Registration number is required'));
            return;
        }

        $exists = $this->Face_attendance_student_model->check_registration_exists($registration_number);
        
        echo json_encode(array(
            'status' => 'success',
            'exists' => $exists,
            'message' => $exists ? 'Registration number already exists' : 'Registration number is available'
        ));
    }

    /**
     * Register new student with face images
     */
    public function register_student() {
        if (!$this->rbac->hasPrivilege('face_attendance_register', 'can_add')) {
            echo json_encode(array('status' => 'error', 'message' => 'Access denied'));
            return;
        }

        $registration_number = $this->input->post('registration_number');
        $first_name = $this->input->post('first_name');
        $last_name = $this->input->post('last_name');
        $email = $this->input->post('email');
        $phone = $this->input->post('phone');
        $admission_no = $this->input->post('admission_no');
        $class_id = $this->input->post('class_id');
        $section_id = $this->input->post('section_id');

        // Validation
        if (empty($registration_number) || empty($first_name) || empty($last_name)) {
            echo json_encode(array('status' => 'error', 'message' => 'Required fields are missing'));
            return;
        }

        // Check if registration number already exists
        if ($this->Face_attendance_student_model->check_registration_exists($registration_number)) {
            echo json_encode(array('status' => 'error', 'message' => 'Registration number already exists'));
            return;
        }

        // Check if at least 3 images are captured
        $captured_images = array();
        for ($i = 1; $i <= 5; $i++) {
            $image_data = $this->input->post("captured_image_$i");
            if (!empty($image_data)) {
                $captured_images[] = $image_data;
            }
        }

        if (count($captured_images) < 3) {
            echo json_encode(array('status' => 'error', 'message' => 'Please capture at least 3 face images'));
            return;
        }

        // Create directory for student images
        $upload_path = './uploads/face_attendance_images/' . $registration_number . '/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true);
        }

        // Save images
        $saved_images = array();
        foreach ($captured_images as $index => $image_data) {
            // Extract base64 data
            $image_parts = explode(';base64,', $image_data);
            if (count($image_parts) == 2) {
                $image_base64 = base64_decode($image_parts[1]);
                $file_name = ($index + 1) . '.png';
                $file_path = $upload_path . $file_name;
                
                if (file_put_contents($file_path, $image_base64)) {
                    $saved_images[] = $file_name;
                }
            }
        }

        if (count($saved_images) < 3) {
            echo json_encode(array('status' => 'error', 'message' => 'Error saving images. Please try again.'));
            return;
        }

        // Prepare student data
        $student_data = array(
            'registration_number' => $registration_number,
            'first_name'          => $first_name,
            'last_name'           => $last_name,
            'email'               => $email,
            'phone'               => $phone,
            'admission_no'        => $admission_no,
            'class_id'            => $class_id,
            'section_id'          => $section_id,
            'face_images'         => $saved_images
        );

        // Insert into database
        $insert_id = $this->Face_attendance_student_model->add_student($student_data);

        if ($insert_id) {
            echo json_encode(array(
                'status' => 'success',
                'message' => 'Student registered successfully!',
                'student_id' => $insert_id
            ));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Error saving student data'));
        }
    }

    /**
     * Get all registered students (AJAX)
     */
    public function get_students() {
        $students = $this->Face_attendance_student_model->get_all_students(1);
        
        $student_list = array();
        foreach ($students as $student) {
            $student_list[] = array(
                'id' => $student->id,
                'registration_number' => $student->registration_number,
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'email' => $student->email,
                'face_images' => json_decode($student->face_images),
                'registration_date' => $student->registration_date
            );
        }

        echo json_encode(array(
            'status' => 'success',
            'students' => $student_list
        ));
    }

    /**
     * Get student face images for recognition
     */
    public function get_student_images($registration_number) {
        $student = $this->Face_attendance_student_model->get_student_by_registration($registration_number);
        
        if (!$student) {
            echo json_encode(array('status' => 'error', 'message' => 'Student not found'));
            return;
        }

        $images = json_decode($student->face_images);
        $image_urls = array();
        
        foreach ($images as $image) {
            $image_urls[] = base_url('uploads/face_attendance_images/' . $registration_number . '/' . $image);
        }

        echo json_encode(array(
            'status' => 'success',
            'images' => $image_urls,
            'student' => array(
                'registration_number' => $student->registration_number,
                'first_name' => $student->first_name,
                'last_name' => $student->last_name
            )
        ));
    }

    /**
     * Delete student
     */
    public function delete_student() {
        if (!$this->rbac->hasPrivilege('face_attendance_register', 'can_delete')) {
            echo json_encode(array('status' => 'error', 'message' => 'Access denied'));
            return;
        }

        $student_id = $this->input->post('student_id');
        
        if (empty($student_id)) {
            echo json_encode(array('status' => 'error', 'message' => 'Student ID is required'));
            return;
        }

        // Get student data before deletion
        $student = $this->Face_attendance_student_model->get_student($student_id);
        
        if (!$student) {
            echo json_encode(array('status' => 'error', 'message' => 'Student not found'));
            return;
        }

        // Delete images directory
        $upload_path = './uploads/face_attendance_images/' . $student->registration_number . '/';
        if (is_dir($upload_path)) {
            $this->delete_directory($upload_path);
        }

        // Delete from database
        if ($this->Face_attendance_student_model->delete_student($student_id)) {
            echo json_encode(array('status' => 'success', 'message' => 'Student deleted successfully'));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Error deleting student'));
        }
    }

    /**
     * Mark Attendance - Face Recognition Page
     */
    public function mark_attendance() {
        if (!$this->rbac->hasPrivilege('face_attendance_register', 'can_view')) {
            access_denied();
        }
        
        $this->session->set_userdata('top_menu', 'Attendance');
        $this->session->set_userdata('sub_menu', 'admin/face_attendance_register/mark_attendance');
        
        $data['title'] = 'Mark Face Attendance';
        $data['students'] = $this->Face_attendance_student_model->get_all_students(1);
        
        $this->load->view('layout/header', $data);
        $this->load->view('admin/face_attendance_register/mark_attendance', $data);
        $this->load->view('layout/footer', $data);
    }

    /**
     * Get all registered students for face recognition (AJAX)
     */
    public function get_registered_students() {
        $students = $this->Face_attendance_student_model->get_all_students(1);
        
        $student_list = array();
        foreach ($students as $student) {
            $face_images = json_decode($student->face_images, true);
            $image_urls = array();
            
            if (is_array($face_images)) {
                foreach ($face_images as $image) {
                    $image_urls[] = base_url('uploads/face_attendance_images/' . $student->registration_number . '/' . $image);
                }
            }
            
            $student_list[] = array(
                'id' => $student->id,
                'registration_number' => $student->registration_number,
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'admission_no' => $student->admission_no,
                'class_id' => $student->class_id,
                'section_id' => $student->section_id,
                'email' => $student->email,
                'face_images' => $image_urls
            );
        }

        echo json_encode(array(
            'status' => 'success',
            'students' => $student_list
        ));
    }

    /**
     * Save attendance records (AJAX)
     */
    public function save_attendance() {
        if (!$this->rbac->hasPrivilege('face_attendance_register', 'can_add')) {
            echo json_encode(array('status' => 'error', 'message' => 'Access denied'));
            return;
        }

        $attendance_data = $this->input->post('attendance_data');
        
        if (empty($attendance_data)) {
            echo json_encode(array('status' => 'error', 'message' => 'No attendance data provided'));
            return;
        }

        // Decode JSON data
        $attendance_records = json_decode($attendance_data, true);
        
        if (!is_array($attendance_records) || count($attendance_records) == 0) {
            echo json_encode(array('status' => 'error', 'message' => 'Invalid attendance data'));
            return;
        }

        $success_count = 0;
        $error_count = 0;
        $session_id = uniqid('session_', true);
        $marked_by = $this->customlib->getStaffID();

        foreach ($attendance_records as $record) {
            $attendance_entry = array(
                'face_student_id'     => isset($record['student_id']) ? $record['student_id'] : null,
                'registration_number' => $record['registration_number'],
                'attendance_date'     => date('Y-m-d'),
                'attendance_time'     => date('H:i:s'),
                'attendance_status'   => $record['status'],
                'confidence_score'    => isset($record['confidence']) ? $record['confidence'] : null,
                'session_id'          => $session_id,
                'class_id'            => isset($record['class_id']) ? $record['class_id'] : null,
                'section_id'          => isset($record['section_id']) ? $record['section_id'] : null,
                'marked_by'           => $marked_by,
                'recognition_method'  => 'face_recognition',
                'notes'               => isset($record['notes']) ? $record['notes'] : null
            );

            if ($this->Face_attendance_student_model->mark_attendance($attendance_entry)) {
                $success_count++;
            } else {
                $error_count++;
            }
        }

        // Log the attendance session
        $detected_faces = $this->input->post('detected_faces');
        $log_entry = array(
            'session_date'       => date('Y-m-d H:i:s'),
            'detected_faces'     => $detected_faces ? $detected_faces : count($attendance_records),
            'recognized_faces'   => $success_count,
            'unknown_faces'      => $error_count,
            'recognition_details' => json_encode(array(
                'session_id' => $session_id,
                'total_records' => count($attendance_records),
                'success' => $success_count,
                'errors' => $error_count
            )),
            'created_by'         => $marked_by
        );

        $this->Face_attendance_student_model->log_attendance_session($log_entry);

        echo json_encode(array(
            'status' => 'success',
            'message' => "Attendance saved successfully! Present: $success_count, Errors: $error_count",
            'session_id' => $session_id,
            'success_count' => $success_count,
            'error_count' => $error_count
        ));
    }

    /**
     * Get attendance records for today (AJAX)
     */
    public function get_attendance_records() {
        $date = $this->input->get('date') ? $this->input->get('date') : date('Y-m-d');
        
        $records = $this->Face_attendance_student_model->get_attendance_by_date($date);
        
        echo json_encode(array(
            'status' => 'success',
            'date' => $date,
            'records' => $records
        ));
    }

    /**
     * Helper function to delete directory recursively
     */
    private function delete_directory($dir) {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!$this->delete_directory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }

        return rmdir($dir);
    }
}
