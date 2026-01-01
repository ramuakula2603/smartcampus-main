<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Teacher Middleware Library
 * Provides middleware functionality for teacher authentication and authorization
 */
class Teacher_middleware
{
    private $CI;
    private $protected_methods = array();
    private $role_permissions = array();

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->helper('teacher_auth');
        $this->CI->load->model('teacher_auth_model');
        
        // Define default protected methods
        $this->protected_methods = array(
            'profile', 'update_profile', 'change_password', 
            'dashboard', 'logout'
        );
        
        // Define role-based permissions
        $this->role_permissions = array(
            'teacher' => array(
                'view_profile', 'edit_profile', 'view_dashboard',
                'manage_students', 'view_attendance', 'manage_homework'
            ),
            'head_teacher' => array(
                'view_profile', 'edit_profile', 'view_dashboard',
                'manage_students', 'view_attendance', 'manage_homework',
                'view_reports', 'manage_teachers'
            ),
            'admin' => array('*') // All permissions
        );
    }

    /**
     * Check authentication for protected methods
     */
    public function check_auth($controller = null, $method = null)
    {
        if (!$controller) {
            $controller = $this->CI->router->fetch_class();
        }
        
        if (!$method) {
            $method = $this->CI->router->fetch_method();
        }
        
        // Check if method requires authentication
        if ($this->is_protected_method($method)) {
            $auth_check = $this->CI->teacher_auth_model->auth();
            
            if ($auth_check['status'] != 200) {
                json_output(401, $auth_check);
                return false;
            }
            
            // Store authenticated teacher info for later use
            $this->CI->authenticated_teacher = array(
                'staff_id' => $auth_check['staff_id'],
                'user_id' => isset($auth_check['user_id']) ? $auth_check['user_id'] : null,
                'auth_type' => isset($auth_check['auth_type']) ? $auth_check['auth_type'] : 'token'
            );
            
            return true;
        }
        
        return true; // Method doesn't require authentication
    }

    /**
     * Check if method is protected
     */
    public function is_protected_method($method)
    {
        return in_array($method, $this->protected_methods);
    }

    /**
     * Add protected method
     */
    public function add_protected_method($method)
    {
        if (!in_array($method, $this->protected_methods)) {
            $this->protected_methods[] = $method;
        }
    }

    /**
     * Remove protected method
     */
    public function remove_protected_method($method)
    {
        $key = array_search($method, $this->protected_methods);
        if ($key !== false) {
            unset($this->protected_methods[$key]);
        }
    }

    /**
     * Check permission for specific action
     */
    public function check_permission($permission, $staff_id = null)
    {
        if (!$staff_id) {
            if (!isset($this->CI->authenticated_teacher)) {
                return false;
            }
            $staff_id = $this->CI->authenticated_teacher['staff_id'];
        }
        
        // Get teacher role
        $teacher_role = $this->get_teacher_role($staff_id);
        
        if (!$teacher_role) {
            return false;
        }
        
        // Check if role has permission
        if (isset($this->role_permissions[$teacher_role])) {
            $permissions = $this->role_permissions[$teacher_role];
            return in_array('*', $permissions) || in_array($permission, $permissions);
        }
        
        return false;
    }

    /**
     * Get teacher role based on designation and other factors
     */
    private function get_teacher_role($staff_id)
    {
        $this->CI->db->select('staff.*, staff_designation.designation, staff_roles.role_id, roles.name as role_name');
        $this->CI->db->from('staff');
        $this->CI->db->join('staff_designation', 'staff_designation.id = staff.designation', 'left');
        $this->CI->db->join('staff_roles', 'staff_roles.staff_id = staff.id', 'left');
        $this->CI->db->join('roles', 'roles.id = staff_roles.role_id', 'left');
        $this->CI->db->where('staff.id', $staff_id);
        $query = $this->CI->db->get();
        
        if ($query->num_rows() > 0) {
            $staff = $query->row();
            
            // Determine role based on designation or role assignment
            if ($staff->role_name) {
                return strtolower($staff->role_name);
            }
            
            // Default role mapping based on designation
            $designation_roles = array(
                'Principal' => 'admin',
                'Vice Principal' => 'head_teacher',
                'Head Teacher' => 'head_teacher',
                'Senior Teacher' => 'head_teacher'
            );
            
            if (isset($designation_roles[$staff->designation])) {
                return $designation_roles[$staff->designation];
            }
        }
        
        return 'teacher'; // Default role
    }

    /**
     * Require specific permission
     */
    public function require_permission($permission, $error_message = null)
    {
        if (!$this->check_permission($permission)) {
            $message = $error_message ?: "Access denied. Permission '{$permission}' required.";
            json_output(403, array('status' => 403, 'message' => $message));
            return false;
        }
        return true;
    }

    /**
     * Check if teacher can access specific resource
     */
    public function can_access_resource($resource_type, $resource_id)
    {
        if (!isset($this->CI->authenticated_teacher)) {
            return false;
        }
        
        return validate_teacher_access($resource_type, $resource_id);
    }

    /**
     * Require resource access
     */
    public function require_resource_access($resource_type, $resource_id, $error_message = null)
    {
        if (!$this->can_access_resource($resource_type, $resource_id)) {
            $message = $error_message ?: "Access denied. You don't have permission to access this {$resource_type}.";
            json_output(403, array('status' => 403, 'message' => $message));
            return false;
        }
        return true;
    }

    /**
     * Get authenticated teacher information
     */
    public function get_authenticated_teacher()
    {
        return isset($this->CI->authenticated_teacher) ? $this->CI->authenticated_teacher : null;
    }

    /**
     * Log teacher activity
     */
    public function log_activity($action, $details = null)
    {
        $teacher = $this->get_authenticated_teacher();
        if (!$teacher) {
            return false;
        }
        
        $log_data = array(
            'staff_id' => $teacher['staff_id'],
            'action' => $action,
            'details' => $details ? json_encode($details) : null,
            'ip_address' => $this->CI->input->ip_address(),
            'user_agent' => $this->CI->input->user_agent(),
            'created_at' => date('Y-m-d H:i:s')
        );
        
        // Insert into activity log table (create this table if needed)
        // $this->CI->db->insert('teacher_activity_log', $log_data);
        
        return true;
    }

    /**
     * Rate limiting for API calls
     */
    public function check_rate_limit($max_requests = 100, $time_window = 3600)
    {
        $teacher = $this->get_authenticated_teacher();
        if (!$teacher) {
            return true; // No rate limiting for unauthenticated requests
        }
        
        $cache_key = "rate_limit_teacher_{$teacher['staff_id']}";
        
        // Simple rate limiting using session or cache
        // This is a basic implementation - use Redis or Memcached for production
        $current_requests = $this->CI->session->userdata($cache_key) ?: 0;
        
        if ($current_requests >= $max_requests) {
            json_output(429, array(
                'status' => 429, 
                'message' => 'Rate limit exceeded. Please try again later.',
                'retry_after' => $time_window
            ));
            return false;
        }
        
        $this->CI->session->set_userdata($cache_key, $current_requests + 1);
        return true;
    }
}
