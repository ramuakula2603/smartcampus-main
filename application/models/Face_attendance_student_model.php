<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Face_attendance_student_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Check if registration number already exists
     */
    public function check_registration_exists($registration_number) {
        $this->db->where('registration_number', $registration_number);
        $query = $this->db->get('face_attendance_students');
        return $query->num_rows() > 0;
    }

    /**
     * Get all registered students
     */
    public function get_all_students($is_active = null) {
        if ($is_active !== null) {
            $this->db->where('is_active', $is_active);
        }
        $this->db->order_by('first_name', 'ASC');
        $this->db->order_by('last_name', 'ASC');
        $query = $this->db->get('face_attendance_students');
        return $query->result();
    }

    /**
     * Get student by ID
     */
    public function get_student($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('face_attendance_students');
        return $query->row();
    }

    /**
     * Get student by registration number
     */
    public function get_student_by_registration($registration_number) {
        $this->db->where('registration_number', $registration_number);
        $query = $this->db->get('face_attendance_students');
        return $query->row();
    }

    /**
     * Add new student
     */
    public function add_student($data) {
        $insert_data = array(
            'student_id'          => isset($data['student_id']) ? $data['student_id'] : null,
            'registration_number' => $data['registration_number'],
            'admission_no'        => isset($data['admission_no']) ? $data['admission_no'] : null,
            'first_name'          => $data['first_name'],
            'last_name'           => $data['last_name'],
            'class_id'            => isset($data['class_id']) ? $data['class_id'] : null,
            'section_id'          => isset($data['section_id']) ? $data['section_id'] : null,
            'email'               => isset($data['email']) ? $data['email'] : null,
            'phone'               => isset($data['phone']) ? $data['phone'] : null,
            'face_images'         => isset($data['face_images']) ? json_encode($data['face_images']) : null,
            'face_descriptors'    => isset($data['face_descriptors']) ? json_encode($data['face_descriptors']) : null,
            'is_active'           => 1,
            'registered_by'       => $this->session->userdata('id'),
            'registration_date'   => date('Y-m-d H:i:s')
        );

        if ($this->db->insert('face_attendance_students', $insert_data)) {
            return $this->db->insert_id();
        }
        return false;
    }

    /**
     * Update student information
     */
    public function update_student($id, $data) {
        $update_data = array();
        
        $allowed_fields = array(
            'student_id', 'admission_no', 'first_name', 'last_name',
            'class_id', 'section_id', 'email', 'phone', 'is_active'
        );

        foreach ($allowed_fields as $field) {
            if (isset($data[$field])) {
                $update_data[$field] = $data[$field];
            }
        }

        // Handle JSON fields separately
        if (isset($data['face_images'])) {
            $update_data['face_images'] = json_encode($data['face_images']);
        }
        if (isset($data['face_descriptors'])) {
            $update_data['face_descriptors'] = json_encode($data['face_descriptors']);
        }

        if (!empty($update_data)) {
            $this->db->where('id', $id);
            return $this->db->update('face_attendance_students', $update_data);
        }
        return false;
    }

    /**
     * Delete student
     */
    public function delete_student($id) {
        $this->db->where('id', $id);
        return $this->db->delete('face_attendance_students');
    }

    /**
     * Get students with filters
     */
    public function get_students_filtered($filters = array()) {
        if (isset($filters['class_id']) && !empty($filters['class_id'])) {
            $this->db->where('class_id', $filters['class_id']);
        }
        if (isset($filters['section_id']) && !empty($filters['section_id'])) {
            $this->db->where('section_id', $filters['section_id']);
        }
        if (isset($filters['is_active'])) {
            $this->db->where('is_active', $filters['is_active']);
        }
        if (isset($filters['search']) && !empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('first_name', $filters['search']);
            $this->db->or_like('last_name', $filters['search']);
            $this->db->or_like('registration_number', $filters['search']);
            $this->db->group_end();
        }

        $this->db->order_by('first_name', 'ASC');
        $query = $this->db->get('face_attendance_students');
        return $query->result();
    }

    /**
     * Mark attendance
     */
    public function mark_attendance($data) {
        $insert_data = array(
            'face_student_id'      => $data['face_student_id'],
            'registration_number'  => $data['registration_number'],
            'attendance_date'      => $data['attendance_date'],
            'attendance_time'      => $data['attendance_time'],
            'attendance_status'    => isset($data['attendance_status']) ? $data['attendance_status'] : 'Present',
            'confidence_score'     => isset($data['confidence_score']) ? $data['confidence_score'] : null,
            'captured_image'       => isset($data['captured_image']) ? $data['captured_image'] : null,
            'session_id'           => isset($data['session_id']) ? $data['session_id'] : null,
            'class_id'             => isset($data['class_id']) ? $data['class_id'] : null,
            'section_id'           => isset($data['section_id']) ? $data['section_id'] : null,
            'marked_by'            => $this->session->userdata('id'),
            'recognition_method'   => isset($data['recognition_method']) ? $data['recognition_method'] : 'Auto',
            'notes'                => isset($data['notes']) ? $data['notes'] : null
        );

        return $this->db->insert('face_attendance_records', $insert_data);
    }

    /**
     * Check if attendance already marked for today
     */
    public function is_attendance_marked_today($face_student_id, $date = null) {
        if ($date === null) {
            $date = date('Y-m-d');
        }
        
        $this->db->where('face_student_id', $face_student_id);
        $this->db->where('attendance_date', $date);
        $query = $this->db->get('face_attendance_records');
        return $query->num_rows() > 0;
    }

    /**
     * Get attendance records with filters
     */
    public function get_attendance_records($filters = array()) {
        $this->db->select('far.*, fas.registration_number, fas.first_name, fas.last_name');
        $this->db->from('face_attendance_records far');
        $this->db->join('face_attendance_students fas', 'far.face_student_id = fas.id', 'left');

        if (isset($filters['date_from']) && !empty($filters['date_from'])) {
            $this->db->where('far.attendance_date >=', $filters['date_from']);
        }
        if (isset($filters['date_to']) && !empty($filters['date_to'])) {
            $this->db->where('far.attendance_date <=', $filters['date_to']);
        }
        if (isset($filters['class_id']) && !empty($filters['class_id'])) {
            $this->db->where('far.class_id', $filters['class_id']);
        }
        if (isset($filters['section_id']) && !empty($filters['section_id'])) {
            $this->db->where('far.section_id', $filters['section_id']);
        }
        if (isset($filters['status']) && !empty($filters['status'])) {
            $this->db->where('far.attendance_status', $filters['status']);
        }

        $this->db->order_by('far.attendance_date', 'DESC');
        $this->db->order_by('far.attendance_time', 'DESC');
        
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Log face recognition session
     */
    public function log_recognition_session($data) {
        $insert_data = array(
            'session_date'        => $data['session_date'],
            'recognition_time'    => date('Y-m-d H:i:s'),
            'detected_faces'      => isset($data['detected_faces']) ? $data['detected_faces'] : 0,
            'recognized_faces'    => isset($data['recognized_faces']) ? $data['recognized_faces'] : 0,
            'unknown_faces'       => isset($data['unknown_faces']) ? $data['unknown_faces'] : 0,
            'recognition_details' => isset($data['recognition_details']) ? json_encode($data['recognition_details']) : null,
            'created_by'          => $this->session->userdata('id')
        );

        return $this->db->insert('face_attendance_logs', $insert_data);
    }

    /**
     * Get total registered students count
     */
    public function get_total_students($is_active = 1) {
        if ($is_active !== null) {
            $this->db->where('is_active', $is_active);
        }
        return $this->db->count_all_results('face_attendance_students');
    }

    /**
     * Get attendance statistics for a date
     */
    public function get_attendance_stats($date = null) {
        if ($date === null) {
            $date = date('Y-m-d');
        }

        $this->db->select('attendance_status, COUNT(*) as count');
        $this->db->where('attendance_date', $date);
        $this->db->group_by('attendance_status');
        $query = $this->db->get('face_attendance_records');
        
        $stats = array(
            'Present' => 0,
            'Absent'  => 0,
            'Late'    => 0
        );

        foreach ($query->result() as $row) {
            $stats[$row->attendance_status] = $row->count;
        }

        return $stats;
    }

    /**
     * Get attendance by date
     */
    public function get_attendance_by_date($date) {
        $this->db->select('far.*, fas.registration_number, fas.first_name, fas.last_name, fas.admission_no');
        $this->db->from('face_attendance_records far');
        $this->db->join('face_attendance_students fas', 'far.face_student_id = fas.id', 'left');
        $this->db->where('far.attendance_date', $date);
        $this->db->order_by('far.attendance_time', 'DESC');
        
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Log attendance session
     */
    public function log_attendance_session($data) {
        $insert_data = array(
            'session_date'        => isset($data['session_date']) ? $data['session_date'] : date('Y-m-d H:i:s'),
            'recognition_time'    => date('Y-m-d H:i:s'),
            'detected_faces'      => isset($data['detected_faces']) ? $data['detected_faces'] : 0,
            'recognized_faces'    => isset($data['recognized_faces']) ? $data['recognized_faces'] : 0,
            'unknown_faces'       => isset($data['unknown_faces']) ? $data['unknown_faces'] : 0,
            'recognition_details' => isset($data['recognition_details']) ? $data['recognition_details'] : null,
            'created_by'          => isset($data['created_by']) ? $data['created_by'] : $this->session->userdata('id')
        );

        return $this->db->insert('face_attendance_logs', $insert_data);
    }
}
