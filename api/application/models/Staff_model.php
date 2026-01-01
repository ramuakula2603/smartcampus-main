<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Staff_model extends CI_Model
{

    public function getAll($id = null, $is_active = null)
    {
        $this->db->select("staff.*,staff_designation.designation,department.department_name as department, roles.id as role_id, roles.name as role");
        $this->db->from('staff');
        $this->db->join('staff_designation', "staff_designation.id = staff.designation", "left");
        $this->db->join('staff_roles', "staff_roles.staff_id = staff.id", "left");
        $this->db->join('roles', "roles.id = staff_roles.role_id", "left");
        $this->db->join('department', "department.id = staff.department", "left");

        if ($id != null) {
            $this->db->where('staff.id', $id);
        } else {
            if ($is_active != null) {
                $this->db->where('staff.is_active', $is_active);
            }
            $this->db->order_by('staff.id');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    /**
     * Get staff report with filters
     * Simplified version for API - no role restrictions
     */
    public function staff_report($condition)
    {
        $this->load->model('customfield_model');

        $i = 1;
        $custom_fields = $this->customfield_model->get_custom_fields('staff', 1);

        $field_k_array = array();
        $join_array = "";
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($field_k_array, '`table_custom_' . $i . '`.`field_value` as `' . $custom_fields_value->name . '`');
                $join_array .= " LEFT JOIN `custom_field_values` as `" . $tb_counter . "` ON `staff`.`id` = `" . $tb_counter . "`.`belong_table_id` AND `" . $tb_counter . "`.`custom_field_id` = " . $custom_fields_value->id;

                $i++;
            }
        }

        $field_var = count($field_k_array) > 0 ? "," . implode(',', $field_k_array) : "";

        $query = "SELECT `staff`.*, `staff_designation`.`designation` as `designation`, `department`.`department_name` as `department`,`roles`.`name` as user_type " . $field_var . ",GROUP_CONCAT(leave_type_id,'@',alloted_leave) as leaves  FROM `staff` " . $join_array . " LEFT JOIN `staff_designation` ON `staff_designation`.`id` = `staff`.`designation` LEFT JOIN `staff_roles` ON `staff_roles`.`staff_id` = `staff`.`id` LEFT JOIN `roles` ON `staff_roles`.`role_id` = `roles`.`id` LEFT JOIN `department` ON `department`.`id` = `staff`.`department` left join staff_leave_details ON staff_leave_details.staff_id=staff.id WHERE 1  " . $condition . " group by staff.id";

        $query = $this->db->query($query);

        return $query->result_array();
    }

    /**
     * Get staff roles
     */
    public function getStaffRole()
    {
        $query = $this->db->select('*')->from('roles')->get();
        return $query->result_array();
    }

    /**
     * Get staff name by ID
     * Returns staff name, employee_id, and id
     */
    public function get_StaffNameById($id)
    {
        return $this->db->select("CONCAT_WS(' ',name,surname) as name,employee_id,id")->from('staff')->where('id', $id)->get()->row_array();
    }

    /**
     * Get employees by role
     * 
     * @param string|int $role Role name or ID ("select" for all roles, or specific role)
     * @param int $active 1 for active, 0 for inactive
     * @param int|null $class_id Optional class ID filter
     * @return array Array of staff members
     */
    public function getEmployee($role = "select", $active = 1, $class_id = null)
    {
        $this->db->select("staff.*, staff_designation.designation, department.department_name as department, roles.name as user_type, roles.id as role_id");
        $this->db->from('staff');
        $this->db->join('staff_designation', "staff_designation.id = staff.designation", "left");
        $this->db->join('staff_roles', "staff_roles.staff_id = staff.id", "left");
        $this->db->join('roles', "roles.id = staff_roles.role_id", "left");
        $this->db->join('department', "department.id = staff.department", "left");

        if ($class_id != "" && $class_id != null) {
            $this->db->join('class_teacher', 'staff.id = class_teacher.staff_id', 'left');
            $this->db->where('class_teacher.class_id', $class_id);
        }

        $this->db->where("staff.is_active", $active);

        // Filter by role if provided and not "select"
        if ($role != "" && $role != "select") {
            // Check if role is numeric (ID) or text (name)
            if (is_numeric($role)) {
                $this->db->where("roles.id", $role);
            } else {
                $this->db->where("roles.name", $role);
            }
        }

        $query = $this->db->get();
        return $query->result_array();
    }

}
