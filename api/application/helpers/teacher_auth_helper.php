<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Teacher Authentication Helper Functions
 * Role-based access control and utility functions for teacher authentication
 */

if (!function_exists('is_teacher_authenticated')) {
    /**
     * Check if current user is an authenticated teacher
     */
    function is_teacher_authenticated()
    {
        $CI = &get_instance();
        $CI->load->model('teacher_auth_model');
        
        $auth_check = $CI->teacher_auth_model->auth();
        return $auth_check['status'] == 200;
    }
}

if (!function_exists('get_authenticated_teacher')) {
    /**
     * Get authenticated teacher information
     */
    function get_authenticated_teacher()
    {
        $CI = &get_instance();
        $CI->load->model('teacher_auth_model');
        
        $auth_check = $CI->teacher_auth_model->auth();
        if ($auth_check['status'] == 200) {
            return array(
                'staff_id' => $auth_check['staff_id'],
                'user_id' => isset($auth_check['user_id']) ? $auth_check['user_id'] : null,
                'auth_type' => isset($auth_check['auth_type']) ? $auth_check['auth_type'] : 'token'
            );
        }
        return false;
    }
}

if (!function_exists('require_teacher_auth')) {
    /**
     * Require teacher authentication - returns error response if not authenticated
     */
    function require_teacher_auth()
    {
        if (!is_teacher_authenticated()) {
            json_output(401, array('status' => 401, 'message' => 'Teacher authentication required.'));
        }
    }
}

if (!function_exists('check_teacher_permission')) {
    /**
     * Check if teacher has specific permission
     * This can be extended based on role hierarchy
     */
    function check_teacher_permission($permission)
    {
        $teacher = get_authenticated_teacher();
        if (!$teacher) {
            return false;
        }
        
        $CI = &get_instance();
        $CI->load->model('teacher_auth_model');
        
        // Get teacher information
        $teacher_info = $CI->teacher_auth_model->getTeacherInformation($teacher['staff_id']);
        
        if (!$teacher_info) {
            return false;
        }
        
        // Basic permission checks - can be extended
        switch ($permission) {
            case 'view_profile':
            case 'edit_profile':
            case 'view_dashboard':
                return true; // All teachers have these permissions
                
            case 'manage_students':
                // Only class teachers or subject teachers
                return $teacher_info->designation != null;
                
            case 'view_reports':
                // Teachers with specific designations
                return in_array($teacher_info->designation, [1, 2, 3]); // Adjust based on your designation IDs
                
            default:
                return false;
        }
    }
}

if (!function_exists('get_teacher_role_name')) {
    /**
     * Get teacher role name based on designation
     */
    function get_teacher_role_name($designation_id)
    {
        $CI = &get_instance();
        $CI->db->select('designation');
        $CI->db->from('staff_designation');
        $CI->db->where('id', $designation_id);
        $query = $CI->db->get();
        
        if ($query->num_rows() > 0) {
            return $query->row()->designation;
        }
        
        return 'Teacher';
    }
}

if (!function_exists('is_class_teacher')) {
    /**
     * Check if teacher is assigned as class teacher
     */
    function is_class_teacher($staff_id = null)
    {
        if (!$staff_id) {
            $teacher = get_authenticated_teacher();
            if (!$teacher) {
                return false;
            }
            $staff_id = $teacher['staff_id'];
        }
        
        $CI = &get_instance();
        $CI->db->select('id');
        $CI->db->from('class_teacher');
        $CI->db->where('staff_id', $staff_id);
        $query = $CI->db->get();
        
        return $query->num_rows() > 0;
    }
}

if (!function_exists('is_subject_teacher')) {
    /**
     * Check if teacher is assigned to teach subjects
     */
    function is_subject_teacher($staff_id = null)
    {
        if (!$staff_id) {
            $teacher = get_authenticated_teacher();
            if (!$teacher) {
                return false;
            }
            $staff_id = $teacher['staff_id'];
        }
        
        $CI = &get_instance();
        $CI->db->select('id');
        $CI->db->from('teacher_subject');
        $CI->db->where('teacher_id', $staff_id);
        $query = $CI->db->get();
        
        return $query->num_rows() > 0;
    }
}

if (!function_exists('get_teacher_classes')) {
    /**
     * Get classes assigned to teacher
     */
    function get_teacher_classes($staff_id = null)
    {
        if (!$staff_id) {
            $teacher = get_authenticated_teacher();
            if (!$teacher) {
                return array();
            }
            $staff_id = $teacher['staff_id'];
        }
        
        $CI = &get_instance();
        $CI->db->select('classes.id, classes.class, sections.id as section_id, sections.section');
        $CI->db->from('class_teacher');
        $CI->db->join('classes', 'classes.id = class_teacher.class_id');
        $CI->db->join('sections', 'sections.id = class_teacher.section_id');
        $CI->db->where('class_teacher.staff_id', $staff_id);
        $query = $CI->db->get();
        
        return $query->result_array();
    }
}

if (!function_exists('get_teacher_subjects')) {
    /**
     * Get subjects assigned to teacher
     */
    function get_teacher_subjects($staff_id = null)
    {
        if (!$staff_id) {
            $teacher = get_authenticated_teacher();
            if (!$teacher) {
                return array();
            }
            $staff_id = $teacher['staff_id'];
        }
        
        $CI = &get_instance();
        $CI->db->select('subjects.id, subjects.name, subjects.code, subjects.type');
        $CI->db->from('teacher_subject');
        $CI->db->join('subjects', 'subjects.id = teacher_subject.subject_id');
        $CI->db->where('teacher_subject.teacher_id', $staff_id);
        $query = $CI->db->get();
        
        return $query->result_array();
    }
}

if (!function_exists('validate_teacher_access')) {
    /**
     * Validate teacher access to specific resource
     */
    function validate_teacher_access($resource_type, $resource_id)
    {
        $teacher = get_authenticated_teacher();
        if (!$teacher) {
            return false;
        }
        
        switch ($resource_type) {
            case 'class':
                $classes = get_teacher_classes($teacher['staff_id']);
                return in_array($resource_id, array_column($classes, 'id'));
                
            case 'subject':
                $subjects = get_teacher_subjects($teacher['staff_id']);
                return in_array($resource_id, array_column($subjects, 'id'));
                
            case 'student':
                // Check if teacher has access to student's class
                $CI = &get_instance();
                $CI->db->select('class_id, section_id');
                $CI->db->from('students');
                $CI->db->where('id', $resource_id);
                $student = $CI->db->get()->row();
                
                if ($student) {
                    $classes = get_teacher_classes($teacher['staff_id']);
                    foreach ($classes as $class) {
                        if ($class['id'] == $student->class_id && $class['section_id'] == $student->section_id) {
                            return true;
                        }
                    }
                }
                return false;
                
            default:
                return false;
        }
    }
}

if (!function_exists('teacher_access_denied')) {
    /**
     * Return access denied response for teachers
     */
    function teacher_access_denied($message = 'Access denied. Insufficient permissions.')
    {
        json_output(403, array('status' => 403, 'message' => $message));
    }
}
