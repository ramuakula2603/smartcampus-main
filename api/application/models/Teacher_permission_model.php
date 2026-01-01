<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Teacher_permission_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('staff_model', 'role_model', 'rolepermission_model', 'sidebarmenu_model'));
    }

    /**
     * Get teacher's role information
     */
    public function getTeacherRole($staff_id)
    {
        $this->db->select('roles.id, roles.name, roles.slug, roles.is_superadmin');
        $this->db->from('staff_roles');
        $this->db->join('roles', 'roles.id = staff_roles.role_id');
        $this->db->where('staff_roles.staff_id', $staff_id);
        $this->db->where('staff_roles.is_active', 1);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row();
        }
        return false;
    }

    /**
     * Get teacher's permissions based on role
     */
    public function getTeacherPermissions($staff_id)
    {
        $role = $this->getTeacherRole($staff_id);
        if (!$role) {
            return array();
        }

        // If super admin, return all permissions
        if ($role->is_superadmin == 1) {
            return $this->getAllPermissions();
        }

        // Get role-specific permissions
        $this->db->select('
            rp.can_view, rp.can_add, rp.can_edit, rp.can_delete,
            pc.id as permission_id, pc.name as permission_name, pc.short_code,
            pg.id as group_id, pg.name as group_name, pg.short_code as group_short_code
        ');
        $this->db->from('roles_permissions rp');
        $this->db->join('permission_category pc', 'pc.id = rp.perm_cat_id');
        $this->db->join('permission_group pg', 'pg.id = pc.perm_group_id');
        $this->db->where('rp.role_id', $role->id);
        $this->db->where('pg.is_active', 1);
        $this->db->order_by('pg.id, pc.id');
        $query = $this->db->get();

        $permissions = array();
        foreach ($query->result() as $perm) {
            if (!isset($permissions[$perm->group_short_code])) {
                $permissions[$perm->group_short_code] = array(
                    'group_id' => $perm->group_id,
                    'group_name' => $perm->group_name,
                    'permissions' => array()
                );
            }

            $permissions[$perm->group_short_code]['permissions'][$perm->short_code] = array(
                'permission_id' => $perm->permission_id,
                'permission_name' => $perm->permission_name,
                'can_view' => (bool)$perm->can_view,
                'can_add' => (bool)$perm->can_add,
                'can_edit' => (bool)$perm->can_edit,
                'can_delete' => (bool)$perm->can_delete
            );
        }

        return $permissions;
    }

    /**
     * Get all permissions (for super admin)
     */
    private function getAllPermissions()
    {
        $this->db->select('
            pc.id as permission_id, pc.name as permission_name, pc.short_code,
            pg.id as group_id, pg.name as group_name, pg.short_code as group_short_code
        ');
        $this->db->from('permission_category pc');
        $this->db->join('permission_group pg', 'pg.id = pc.perm_group_id');
        $this->db->where('pg.is_active', 1);
        $this->db->order_by('pg.id, pc.id');
        $query = $this->db->get();

        $permissions = array();
        foreach ($query->result() as $perm) {
            if (!isset($permissions[$perm->group_short_code])) {
                $permissions[$perm->group_short_code] = array(
                    'group_id' => $perm->group_id,
                    'group_name' => $perm->group_name,
                    'permissions' => array()
                );
            }

            $permissions[$perm->group_short_code]['permissions'][$perm->short_code] = array(
                'permission_id' => $perm->permission_id,
                'permission_name' => $perm->permission_name,
                'can_view' => true,
                'can_add' => true,
                'can_edit' => true,
                'can_delete' => true
            );
        }

        return $permissions;
    }

    /**
     * Get teacher's menu items based on permissions
     */
    public function getTeacherMenus($staff_id)
    {
        $role = $this->getTeacherRole($staff_id);
        if (!$role) {
            return array();
        }

        // Get main menu items
        $this->db->select('
            sm.id, sm.menu, sm.icon, sm.activate_menu, sm.lang_key, sm.level,
            sm.access_permissions, pg.short_code as permission_group
        ');
        $this->db->from('sidebar_menus sm');
        $this->db->join('permission_group pg', 'pg.id = sm.permission_group_id', 'left');
        $this->db->where('sm.is_active', 1);
        $this->db->where('sm.sidebar_display', 1);
        $this->db->order_by('sm.level');
        $query = $this->db->get();

        $menus = array();
        foreach ($query->result() as $menu) {
            // Check if teacher has access to this menu
            if ($this->hasMenuAccess($staff_id, $menu, $role)) {
                $menu_item = array(
                    'id' => $menu->id,
                    'menu' => $menu->menu,
                    'icon' => $menu->icon,
                    'activate_menu' => $menu->activate_menu,
                    'lang_key' => $menu->lang_key,
                    'level' => $menu->level,
                    'permission_group' => $menu->permission_group,
                    'submenus' => $this->getSubMenus($menu->id, $staff_id, $role)
                );
                $menus[] = $menu_item;
            }
        }

        return $menus;
    }

    /**
     * Get sub-menu items for a main menu
     */
    private function getSubMenus($menu_id, $staff_id, $role)
    {
        $this->db->select('
            ssm.id, ssm.menu, ssm.key, ssm.lang_key, ssm.url, ssm.level,
            ssm.access_permissions, ssm.activate_controller, ssm.activate_methods,
            pg.short_code as permission_group
        ');
        $this->db->from('sidebar_sub_menus ssm');
        $this->db->join('permission_group pg', 'pg.id = ssm.permission_group_id', 'left');
        $this->db->where('ssm.sidebar_menu_id', $menu_id);
        $this->db->where('ssm.is_active', 1);
        $this->db->order_by('ssm.level');
        $query = $this->db->get();

        $submenus = array();
        foreach ($query->result() as $submenu) {
            // Check if teacher has access to this submenu
            if ($this->hasSubmenuAccess($staff_id, $submenu, $role)) {
                $submenus[] = array(
                    'id' => $submenu->id,
                    'menu' => $submenu->menu,
                    'key' => $submenu->key,
                    'lang_key' => $submenu->lang_key,
                    'url' => $submenu->url,
                    'level' => $submenu->level,
                    'permission_group' => $submenu->permission_group,
                    'activate_controller' => $submenu->activate_controller,
                    'activate_methods' => $submenu->activate_methods
                );
            }
        }

        return $submenus;
    }

    /**
     * Check if teacher has access to main menu
     */
    private function hasMenuAccess($staff_id, $menu, $role)
    {
        // Super admin has access to everything
        if ($role->is_superadmin == 1) {
            return true;
        }

        // If no permission group specified, allow access
        if (empty($menu->permission_group)) {
            return true;
        }

        // Check if teacher has any permission in this group
        return $this->hasPermissionInGroup($staff_id, $menu->permission_group);
    }

    /**
     * Check if teacher has access to submenu
     */
    private function hasSubmenuAccess($staff_id, $submenu, $role)
    {
        // Super admin has access to everything
        if ($role->is_superadmin == 1) {
            return true;
        }

        // If no permission group specified, allow access
        if (empty($submenu->permission_group)) {
            return true;
        }

        // Parse access permissions if specified
        if (!empty($submenu->access_permissions)) {
            $permissions = explode(',', $submenu->access_permissions);
            foreach ($permissions as $permission) {
                $permission = trim($permission);
                if ($this->hasSpecificPermission($staff_id, $submenu->permission_group, $permission)) {
                    return true;
                }
            }
            return false;
        }

        // Check if teacher has any permission in this group
        return $this->hasPermissionInGroup($staff_id, $submenu->permission_group);
    }

    /**
     * Check if teacher has any permission in a permission group
     */
    private function hasPermissionInGroup($staff_id, $group_short_code)
    {
        $role = $this->getTeacherRole($staff_id);
        if (!$role) {
            return false;
        }

        $this->db->select('COUNT(*) as count');
        $this->db->from('roles_permissions rp');
        $this->db->join('permission_category pc', 'pc.id = rp.perm_cat_id');
        $this->db->join('permission_group pg', 'pg.id = pc.perm_group_id');
        $this->db->where('rp.role_id', $role->id);
        $this->db->where('pg.short_code', $group_short_code);
        $this->db->where('(rp.can_view = 1 OR rp.can_add = 1 OR rp.can_edit = 1 OR rp.can_delete = 1)');
        $query = $this->db->get();

        $result = $query->row();
        return $result->count > 0;
    }

    /**
     * Check if teacher has specific permission
     */
    private function hasSpecificPermission($staff_id, $group_short_code, $permission_type)
    {
        $role = $this->getTeacherRole($staff_id);
        if (!$role) {
            return false;
        }

        $permission_field = 'can_' . $permission_type;
        if (!in_array($permission_field, ['can_view', 'can_add', 'can_edit', 'can_delete'])) {
            return false;
        }

        $this->db->select('COUNT(*) as count');
        $this->db->from('roles_permissions rp');
        $this->db->join('permission_category pc', 'pc.id = rp.perm_cat_id');
        $this->db->join('permission_group pg', 'pg.id = pc.perm_group_id');
        $this->db->where('rp.role_id', $role->id);
        $this->db->where('pg.short_code', $group_short_code);
        $this->db->where("rp.{$permission_field}", 1);
        $query = $this->db->get();

        $result = $query->row();
        return $result->count > 0;
    }

    /**
     * Get accessible modules for teacher
     */
    public function getTeacherModules($staff_id)
    {
        $permissions = $this->getTeacherPermissions($staff_id);
        
        $modules = array();
        foreach ($permissions as $group_code => $group_data) {
            $has_access = false;
            foreach ($group_data['permissions'] as $perm_code => $perm_data) {
                if ($perm_data['can_view'] || $perm_data['can_add'] || 
                    $perm_data['can_edit'] || $perm_data['can_delete']) {
                    $has_access = true;
                    break;
                }
            }

            if ($has_access) {
                $modules[] = array(
                    'group_id' => $group_data['group_id'],
                    'group_name' => $group_data['group_name'],
                    'group_code' => $group_code,
                    'status' => 'active',
                    'permissions_count' => count($group_data['permissions'])
                );
            }
        }

        return $modules;
    }

    /**
     * Check if teacher has specific privilege (similar to RBAC)
     */
    public function hasPrivilege($staff_id, $category, $permission)
    {
        $role = $this->getTeacherRole($staff_id);
        if (!$role) {
            return false;
        }

        // Super admin has all privileges
        if ($role->is_superadmin == 1) {
            return true;
        }

        $permission_field = 'can_' . $permission;
        if (!in_array($permission_field, ['can_view', 'can_add', 'can_edit', 'can_delete'])) {
            return false;
        }

        $this->db->select("rp.{$permission_field}");
        $this->db->from('roles_permissions rp');
        $this->db->join('permission_category pc', 'pc.id = rp.perm_cat_id');
        $this->db->where('rp.role_id', $role->id);
        $this->db->where('pc.short_code', $category);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $result = $query->row();
            return (bool)$result->{$permission_field};
        }

        return false;
    }
}
