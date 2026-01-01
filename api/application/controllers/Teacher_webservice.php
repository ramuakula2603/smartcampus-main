<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Teacher_webservice extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        // Start output buffering if not already started
        if (!ob_get_level()) {
            ob_start();
        }

        // Set JSON content type early
        $this->output->set_content_type('application/json');

        // Load essential models first
        $this->load->model('teacher_auth_model');
        $this->load->helper('json_output');

        // Load other models with error handling
        try {
            $this->load->model(array(
                'teacher_permission_model', 'staff_model', 'setting_model', 'rolepermission_model'
            ));
        } catch (Exception $e) {
            log_message('error', 'Error loading models: ' . $e->getMessage());
        }

        // Load libraries with error handling
        try {
            $this->load->library('teacher_middleware');
        } catch (Exception $e) {
            log_message('error', 'Teacher middleware not available: ' . $e->getMessage());
        }

        try {
            $this->load->library('customlib');
        } catch (Exception $e) {
            log_message('error', 'Customlib not available: ' . $e->getMessage());
        }

        try {
            $this->load->helper('teacher_auth');
        } catch (Exception $e) {
            log_message('error', 'Teacher auth helper not available: ' . $e->getMessage());
        }

        // Set timezone with error handling
        try {
            if (isset($this->setting_model)) {
                $setting = $this->setting_model->getSchoolDetail();
                if ($setting && isset($setting->timezone) && $setting->timezone != "") {
                    date_default_timezone_set($setting->timezone);
                } else {
                    date_default_timezone_set('UTC');
                }
            } else {
                date_default_timezone_set('UTC');
            }
        } catch (Exception $e) {
            log_message('error', 'Error setting timezone: ' . $e->getMessage());
            date_default_timezone_set('UTC');
        }

        // Set custom error handling for JSON responses
        set_error_handler(array($this, 'custom_error_handler'));
        set_exception_handler(array($this, 'custom_exception_handler'));
    }

    /**
     * Custom error handler for JSON responses
     */
    public function custom_error_handler($severity, $message, $file, $line)
    {
        if (!(error_reporting() & $severity)) {
            return false;
        }

        $error_response = array(
            'status' => 0,
            'message' => 'PHP Error occurred',
            'error' => array(
                'type' => 'PHP Error',
                'severity' => $severity,
                'message' => $message,
                'file' => basename($file),
                'line' => $line
            ),
            'timestamp' => date('Y-m-d H:i:s')
        );

        // Log the error
        log_message('error', "PHP Error: $message in $file on line $line");

        // Only send JSON error for database or critical errors
        if (stripos($message, 'database') !== false || 
            stripos($message, 'fatal') !== false ||
            stripos($message, 'call to') !== false) {
            
            if (ob_get_level()) ob_clean();
            header('Content-Type: application/json');
            echo json_encode($error_response);
            exit;
        }

        return false;
    }

    /**
     * Custom exception handler for JSON responses
     */
    public function custom_exception_handler($exception)
    {
        $error_response = array(
            'status' => 0,
            'message' => 'Exception occurred',
            'error' => array(
                'type' => get_class($exception),
                'message' => $exception->getMessage(),
                'file' => basename($exception->getFile()),
                'line' => $exception->getLine()
            ),
            'timestamp' => date('Y-m-d H:i:s')
        );

        // Log the exception
        log_message('error', "Exception: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine());

        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode($error_response);
        exit;
    }

    /**
     * Get Teacher Menu Items
     * POST /teacher/menu
     * Body: {"staff_id": 123}
     */
    public function menu()
    {
        try {
            $method = $this->input->server('REQUEST_METHOD');

            if ($method != 'POST') {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Bad request. Only POST method allowed.',
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            // Get JSON input
                $json_input = json_decode($this->input->raw_input_stream, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid JSON format in request body',
                    'error' => json_last_error_msg(),
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }
            
            if (empty($json_input) || !isset($json_input['staff_id'])) {
                json_output(400, array(
                    'status' => 0, 
                    'message' => 'staff_id is required in request body',
                    'example' => array('staff_id' => 123),
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            $staff_id = intval($json_input['staff_id']);

            if ($staff_id <= 0) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'staff_id must be a valid positive integer',
                    'provided' => $json_input['staff_id'],
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            // Check database connection
            if (!$this->db->conn_id) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Database connection failed',
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            // Get staff info directly from database (same logic as simple_menu)
            $this->db->select('s.*, r.name as role_name, r.is_superadmin, r.id as role_id');
            $this->db->from('staff s');
            $this->db->join('staff_roles sr', 'sr.staff_id = s.id', 'left');
            $this->db->join('roles r', 'r.id = sr.role_id', 'left');
            $this->db->where('s.id', $staff_id);
            $this->db->where('s.is_active', 1);
            
            $query = $this->db->get();
            
            if (!$query) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Database query failed',
                    'error' => $this->db->error(),
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }
            
            $staff_info = $query->row();

            if (!$staff_info) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Staff member not found or inactive',
                    'staff_id' => $staff_id,
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            // Check if superadmin
            $is_superadmin = ($staff_info->role_id == 7 || $staff_info->is_superadmin == 1);

            // Get ALL menus (we'll filter by access_permissions)
            $this->db->select('*');
            $this->db->from('sidebar_menus');
            $this->db->where('is_active', 1);
            $this->db->where('sidebar_display', 1);
            $this->db->order_by('level');
            $menu_query = $this->db->get();

            if (!$menu_query) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Failed to fetch menus',
                    'error' => $this->db->error(),
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            $all_menus = $menu_query->result_array();

            // Get ALL submenus
            $this->db->select('*');
            $this->db->from('sidebar_sub_menus');
            $this->db->where('is_active', 1);
            $this->db->order_by('sidebar_menu_id, level');
            $submenu_query = $this->db->get();
            $all_submenus = $submenu_query ? $submenu_query->result_array() : array();

            // Group submenus by menu_id
            $submenus_by_menu = array();
            foreach ($all_submenus as $submenu) {
                $submenus_by_menu[$submenu['sidebar_menu_id']][] = $submenu;
            }

            // Filter menus and submenus using access_permissions (like admin dashboard)
            $menus = array();
            foreach ($all_menus as $menu) {
                // Check menu permission using access_permissions field
                $module_permission = $this->access_permission_sidebar_remove_pipe($menu['access_permissions']);
                $module_access = false;

                if ($is_superadmin) {
                    $module_access = true;
                } elseif (!empty($module_permission)) {
                    foreach ($module_permission as $m_permission_value) {
                        $cat_permission = $this->access_permission_remove_comma($m_permission_value);

                        if (count($cat_permission) >= 2) {
                            if ($this->hasPrivilege($staff_info->role_id, $staff_info->role_name, $cat_permission[0], $cat_permission[1])) {
                                $module_access = true;
                                break;
                            }
                        }
                    }
                }

                if ($module_access) {
                    // Filter submenus for this menu
                    $menu['submenus'] = array();

                    if (isset($submenus_by_menu[$menu['id']])) {
                        foreach ($submenus_by_menu[$menu['id']] as $submenu) {
                            $sidebar_permission = $this->access_permission_sidebar_remove_pipe($submenu['access_permissions']);
                            $sidebar_access = false;

                            if ($is_superadmin) {
                                $sidebar_access = true;
                            } elseif (!empty($sidebar_permission)) {
                                foreach ($sidebar_permission as $sidebar_permission_value) {
                                    $sidebar_cat_permission = $this->access_permission_remove_comma($sidebar_permission_value);

                                    if (count($sidebar_cat_permission) >= 2) {
                                        if ($this->hasPrivilege($staff_info->role_id, $staff_info->role_name, $sidebar_cat_permission[0], $sidebar_cat_permission[1])) {
                                            $sidebar_access = true;
                                            break;
                                        }
                                    }
                                }
                            }

                            if ($sidebar_access) {
                                $menu['submenus'][] = $submenu;
                            }
                        }
                    }

                    $menus[] = $menu;
                }
            }

            // Enhanced response (keeping original format for compatibility)
            $response = array(
                'status' => 1,
                'message' => 'Menu items retrieved successfully.',
                'data' => array(
                    'staff_id' => $staff_id,
                    'staff_info' => array(
                        'id' => (int)$staff_info->id,
                        'name' => $staff_info->name,
                        'surname' => $staff_info->surname,
                        'employee_id' => $staff_info->employee_id,
                        'full_name' => trim($staff_info->name . ' ' . $staff_info->surname)
                    ),
                    'role' => array(
                        'id' => $staff_info->role_id ? (int)$staff_info->role_id : null,
                        'name' => $staff_info->role_name ? $staff_info->role_name : 'No Role Assigned',
                        'slug' => null, // Keeping original format
                        'is_superadmin' => $is_superadmin
                    ),
                    'menus' => $menus,
                    'total_menus' => count($menus),
                    'timestamp' => date('Y-m-d H:i:s')
                )
            );
            
            json_output(200, $response);
            
        } catch (Exception $e) {
            $error_response = array(
                'status' => 0,
                'message' => 'Exception occurred while retrieving menu items',
                'error' => array(
                    'type' => get_class($e),
                    'message' => $e->getMessage(),
                    'file' => basename($e->getFile()),
                    'line' => $e->getLine()
                ),
                'staff_id' => isset($staff_id) ? $staff_id : null,
                'timestamp' => date('Y-m-d H:i:s')
            );
            
            log_message('error', 'Menu Exception: ' . $e->getMessage());
            json_output(500, $error_response);
        }
    }

    /**
     * Get Teacher Permissions
     * GET /teacher/permissions
     */
    public function permissions()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'GET') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->teacher_auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $auth_check = $this->teacher_auth_model->auth();
                if ($auth_check['status'] == 200) {
                    $staff_id = $auth_check['staff_id'];
                    
                    $permissions = $this->teacher_permission_model->getTeacherPermissions($staff_id);
                    $role = $this->teacher_permission_model->getTeacherRole($staff_id);
                    
                    // Count total permissions
                    $total_permissions = 0;
                    $active_permissions = 0;
                    foreach ($permissions as $group) {
                        foreach ($group['permissions'] as $perm) {
                            $total_permissions++;
                            if ($perm['can_view'] || $perm['can_add'] || $perm['can_edit'] || $perm['can_delete']) {
                                $active_permissions++;
                            }
                        }
                    }
                    
                    $response = array(
                        'status' => 1,
                        'message' => 'Permissions retrieved successfully.',
                        'data' => array(
                            'role' => array(
                                'id' => $role ? $role->id : null,
                                'name' => $role ? $role->name : 'Unknown',
                                'slug' => $role ? $role->slug : null,
                                'is_superadmin' => $role ? (bool)$role->is_superadmin : false
                            ),
                            'permissions' => $permissions,
                            'summary' => array(
                                'total_permission_groups' => count($permissions),
                                'total_permissions' => $total_permissions,
                                'active_permissions' => $active_permissions
                            )
                        )
                    );
                    
                    json_output(200, $response);
                } else {
                    json_output(401, $auth_check);
                }
            }
        }
    }

    /**
     * Get Teacher Accessible Modules
     * GET /teacher/modules
     */
    public function modules()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'GET') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->teacher_auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $auth_check = $this->teacher_auth_model->auth();
                if ($auth_check['status'] == 200) {
                    $staff_id = $auth_check['staff_id'];
                    
                    $modules = $this->teacher_permission_model->getTeacherModules($staff_id);
                    $role = $this->teacher_permission_model->getTeacherRole($staff_id);
                    
                    $response = array(
                        'status' => 1,
                        'message' => 'Accessible modules retrieved successfully.',
                        'data' => array(
                            'role' => array(
                                'id' => $role ? $role->id : null,
                                'name' => $role ? $role->name : 'Unknown',
                                'slug' => $role ? $role->slug : null,
                                'is_superadmin' => $role ? (bool)$role->is_superadmin : false
                            ),
                            'modules' => $modules,
                            'total_modules' => count($modules)
                        )
                    );
                    
                    json_output(200, $response);
                } else {
                    json_output(401, $auth_check);
                }
            }
        }
    }

    /**
     * Check Specific Permission
     * POST /teacher/check-permission
     */
    public function check_permission()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->teacher_auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $auth_check = $this->teacher_auth_model->auth();
                if ($auth_check['status'] == 200) {
                    $staff_id = $auth_check['staff_id'];
                    $params = json_decode(file_get_contents('php://input'), true);
                    
                    // Validate required parameters
                    if (!isset($params['category']) || !isset($params['permission'])) {
                        json_output(400, array(
                            'status' => 400, 
                            'message' => 'Category and permission parameters are required.'
                        ));
                        return;
                    }
                    
                    $category = $params['category'];
                    $permission = $params['permission'];
                    
                    $has_permission = $this->teacher_permission_model->hasPrivilege($staff_id, $category, $permission);
                    $role = $this->teacher_permission_model->getTeacherRole($staff_id);
                    
                    $response = array(
                        'status' => 1,
                        'message' => 'Permission check completed.',
                        'data' => array(
                            'category' => $category,
                            'permission' => $permission,
                            'has_permission' => $has_permission,
                            'role' => array(
                                'id' => $role ? $role->id : null,
                                'name' => $role ? $role->name : 'Unknown',
                                'is_superadmin' => $role ? (bool)$role->is_superadmin : false
                            )
                        )
                    );
                    
                    json_output(200, $response);
                } else {
                    json_output(401, $auth_check);
                }
            }
        }
    }

    /**
     * Get Teacher Role Information
     * GET /teacher/role
     */
    public function role()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'GET') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->teacher_auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $auth_check = $this->teacher_auth_model->auth();
                if ($auth_check['status'] == 200) {
                    $staff_id = $auth_check['staff_id'];
                    
                    $role = $this->teacher_permission_model->getTeacherRole($staff_id);
                    $staff_info = $this->staff_model->get($staff_id);
                    
                    if ($role) {
                        $response = array(
                            'status' => 1,
                            'message' => 'Role information retrieved successfully.',
                            'data' => array(
                                'role' => array(
                                    'id' => $role->id,
                                    'name' => $role->name,
                                    'slug' => $role->slug,
                                    'is_superadmin' => (bool)$role->is_superadmin
                                ),
                                'staff_info' => array(
                                    'id' => $staff_info['id'],
                                    'employee_id' => $staff_info['employee_id'],
                                    'name' => $staff_info['name'] . ' ' . $staff_info['surname'],
                                    'designation' => $staff_info['designation'],
                                    'department' => $staff_info['department_name']
                                )
                            )
                        );
                    } else {
                        $response = array(
                            'status' => 0,
                            'message' => 'No role assigned to this teacher.',
                            'data' => array(
                                'role' => null,
                                'staff_info' => array(
                                    'id' => $staff_info['id'],
                                    'employee_id' => $staff_info['employee_id'],
                                    'name' => $staff_info['name'] . ' ' . $staff_info['surname'],
                                    'designation' => $staff_info['designation'],
                                    'department' => $staff_info['department_name']
                                )
                            )
                        );
                    }
                    
                    json_output(200, $response);
                } else {
                    json_output(401, $auth_check);
                }
            }
        }
    }

    /**
     * Get System Settings for Teacher
     * GET /teacher/settings
     */
    public function settings()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'GET') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->teacher_auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $auth_check = $this->teacher_auth_model->auth();
                if ($auth_check['status'] == 200) {
                    $setting = $this->setting_model->get();

                    // Filter settings relevant to teachers
                    $teacher_settings = array(
                        'school_name' => $setting[0]['name'],
                        'school_code' => $setting[0]['dise_code'],
                        'session_id' => $setting[0]['session_id'],
                        'currency_symbol' => $setting[0]['currency_symbol'],
                        'currency' => $setting[0]['currency'],
                        'date_format' => $setting[0]['date_format'],
                        'time_format' => $setting[0]['time_format'],
                        'timezone' => $setting[0]['timezone'],
                        'language' => $setting[0]['language'],
                        'is_rtl' => $setting[0]['is_rtl'],
                        'theme' => $setting[0]['theme'],
                        'start_week' => $setting[0]['start_week'],
                        'student_login' => $setting[0]['student_login'],
                        'parent_login' => $setting[0]['parent_login']
                    );

                    $response = array(
                        'status' => 1,
                        'message' => 'System settings retrieved successfully.',
                        'data' => $teacher_settings
                    );

                    json_output(200, $response);
                } else {
                    json_output(401, $auth_check);
                }
            }
        }
    }

    /**
     * Get Sidebar Menu Structure
     * GET /teacher/sidebar-menu
     */
    public function sidebar_menu()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'GET') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->teacher_auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $auth_check = $this->teacher_auth_model->auth();
                if ($auth_check['status'] == 200) {
                    $staff_id = $auth_check['staff_id'];

                    $menus = $this->teacher_permission_model->getTeacherMenus($staff_id);

                    // Format for sidebar display
                    $sidebar_structure = array();
                    foreach ($menus as $menu) {
                        $sidebar_item = array(
                            'id' => $menu['id'],
                            'title' => $menu['menu'],
                            'icon' => $menu['icon'],
                            'key' => $menu['lang_key'],
                            'level' => $menu['level'],
                            'has_submenu' => count($menu['submenus']) > 0,
                            'submenu_count' => count($menu['submenus']),
                            'children' => array()
                        );

                        foreach ($menu['submenus'] as $submenu) {
                            $sidebar_item['children'][] = array(
                                'id' => $submenu['id'],
                                'title' => $submenu['menu'],
                                'key' => $submenu['key'],
                                'url' => $submenu['url'],
                                'controller' => $submenu['activate_controller'],
                                'methods' => explode(',', $submenu['activate_methods'])
                            );
                        }

                        $sidebar_structure[] = $sidebar_item;
                    }

                    $response = array(
                        'status' => 1,
                        'message' => 'Sidebar menu structure retrieved successfully.',
                        'data' => array(
                            'sidebar_menu' => $sidebar_structure,
                            'total_main_menus' => count($sidebar_structure),
                            'total_submenus' => array_sum(array_column($sidebar_structure, 'submenu_count'))
                        )
                    );

                    json_output(200, $response);
                } else {
                    json_output(401, $auth_check);
                }
            }
        }
    }

    /**
     * Get Navigation Breadcrumb
     * POST /teacher/breadcrumb
     */
    public function breadcrumb()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->teacher_auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $auth_check = $this->teacher_auth_model->auth();
                if ($auth_check['status'] == 200) {
                    $staff_id = $auth_check['staff_id'];
                    $params = json_decode(file_get_contents('php://input'), true);

                    if (!isset($params['controller']) || !isset($params['method'])) {
                        json_output(400, array(
                            'status' => 400,
                            'message' => 'Controller and method parameters are required.'
                        ));
                        return;
                    }

                    $controller = $params['controller'];
                    $method_name = $params['method'];

                    // Find the menu item that matches the controller and method
                    $menus = $this->teacher_permission_model->getTeacherMenus($staff_id);
                    $breadcrumb = array();

                    foreach ($menus as $menu) {
                        foreach ($menu['submenus'] as $submenu) {
                            if ($submenu['activate_controller'] == $controller) {
                                $methods = explode(',', $submenu['activate_methods']);
                                if (in_array($method_name, $methods)) {
                                    $breadcrumb = array(
                                        'main_menu' => array(
                                            'id' => $menu['id'],
                                            'title' => $menu['menu'],
                                            'icon' => $menu['icon']
                                        ),
                                        'submenu' => array(
                                            'id' => $submenu['id'],
                                            'title' => $submenu['menu'],
                                            'url' => $submenu['url']
                                        ),
                                        'current' => array(
                                            'controller' => $controller,
                                            'method' => $method_name
                                        )
                                    );
                                    break 2;
                                }
                            }
                        }
                    }

                    $response = array(
                        'status' => 1,
                        'message' => 'Breadcrumb information retrieved.',
                        'data' => array(
                            'breadcrumb' => $breadcrumb,
                            'found' => !empty($breadcrumb)
                        )
                    );

                    json_output(200, $response);
                } else {
                    json_output(401, $auth_check);
                }
            }
        }
    }

    /**
     * Get Permission Groups
     * GET /teacher/permission-groups
     */
    public function permission_groups()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'GET') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->teacher_auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $auth_check = $this->teacher_auth_model->auth();
                if ($auth_check['status'] == 200) {
                    $staff_id = $auth_check['staff_id'];

                    $permissions = $this->teacher_permission_model->getTeacherPermissions($staff_id);

                    $permission_groups = array();
                    foreach ($permissions as $group_code => $group_data) {
                        $active_permissions = 0;
                        $total_permissions = count($group_data['permissions']);

                        foreach ($group_data['permissions'] as $perm) {
                            if ($perm['can_view'] || $perm['can_add'] || $perm['can_edit'] || $perm['can_delete']) {
                                $active_permissions++;
                            }
                        }

                        $permission_groups[] = array(
                            'group_id' => $group_data['group_id'],
                            'group_name' => $group_data['group_name'],
                            'group_code' => $group_code,
                            'total_permissions' => $total_permissions,
                            'active_permissions' => $active_permissions,
                            'access_level' => $active_permissions > 0 ? 'granted' : 'denied'
                        );
                    }

                    $response = array(
                        'status' => 1,
                        'message' => 'Permission groups retrieved successfully.',
                        'data' => array(
                            'permission_groups' => $permission_groups,
                            'total_groups' => count($permission_groups)
                        )
                    );

                    json_output(200, $response);
                } else {
                    json_output(401, $auth_check);
                }
            }
        }
    }

    /**
     * Get Detailed Permissions for a Group
     * POST /teacher/group-permissions
     */
    public function group_permissions()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->teacher_auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $auth_check = $this->teacher_auth_model->auth();
                if ($auth_check['status'] == 200) {
                    $staff_id = $auth_check['staff_id'];
                    $params = json_decode(file_get_contents('php://input'), true);

                    if (!isset($params['group_code'])) {
                        json_output(400, array(
                            'status' => 400,
                            'message' => 'Group code parameter is required.'
                        ));
                        return;
                    }

                    $group_code = $params['group_code'];
                    $permissions = $this->teacher_permission_model->getTeacherPermissions($staff_id);

                    if (isset($permissions[$group_code])) {
                        $group_data = $permissions[$group_code];

                        $detailed_permissions = array();
                        foreach ($group_data['permissions'] as $perm_code => $perm_data) {
                            $detailed_permissions[] = array(
                                'permission_id' => $perm_data['permission_id'],
                                'permission_name' => $perm_data['permission_name'],
                                'permission_code' => $perm_code,
                                'can_view' => $perm_data['can_view'],
                                'can_add' => $perm_data['can_add'],
                                'can_edit' => $perm_data['can_edit'],
                                'can_delete' => $perm_data['can_delete'],
                                'has_any_access' => $perm_data['can_view'] || $perm_data['can_add'] ||
                                                   $perm_data['can_edit'] || $perm_data['can_delete']
                            );
                        }

                        $response = array(
                            'status' => 1,
                            'message' => 'Group permissions retrieved successfully.',
                            'data' => array(
                                'group_info' => array(
                                    'group_id' => $group_data['group_id'],
                                    'group_name' => $group_data['group_name'],
                                    'group_code' => $group_code
                                ),
                                'permissions' => $detailed_permissions,
                                'total_permissions' => count($detailed_permissions)
                            )
                        );
                    } else {
                        $response = array(
                            'status' => 0,
                            'message' => 'Permission group not found or access denied.',
                            'data' => array(
                                'group_code' => $group_code,
                                'permissions' => array()
                            )
                        );
                    }

                    json_output(200, $response);
                } else {
                    json_output(401, $auth_check);
                }
            }
        }
    }

    /**
     * Bulk Permission Check
     * POST /teacher/bulk-permission-check
     */
    public function bulk_permission_check()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->teacher_auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $auth_check = $this->teacher_auth_model->auth();
                if ($auth_check['status'] == 200) {
                    $staff_id = $auth_check['staff_id'];
                    $params = json_decode(file_get_contents('php://input'), true);

                    if (!isset($params['permissions']) || !is_array($params['permissions'])) {
                        json_output(400, array(
                            'status' => 400,
                            'message' => 'Permissions array is required.'
                        ));
                        return;
                    }

                    $permission_checks = array();
                    foreach ($params['permissions'] as $perm_check) {
                        if (isset($perm_check['category']) && isset($perm_check['permission'])) {
                            $has_permission = $this->teacher_permission_model->hasPrivilege(
                                $staff_id,
                                $perm_check['category'],
                                $perm_check['permission']
                            );

                            $permission_checks[] = array(
                                'category' => $perm_check['category'],
                                'permission' => $perm_check['permission'],
                                'has_permission' => $has_permission,
                                'identifier' => isset($perm_check['identifier']) ? $perm_check['identifier'] : null
                            );
                        }
                    }

                    $response = array(
                        'status' => 1,
                        'message' => 'Bulk permission check completed.',
                        'data' => array(
                            'permission_checks' => $permission_checks,
                            'total_checks' => count($permission_checks),
                            'granted_count' => count(array_filter($permission_checks, function($check) {
                                return $check['has_permission'];
                            }))
                        )
                    );

                    json_output(200, $response);
                } else {
                    json_output(401, $auth_check);
                }
            }
        }
    }

    /**
     * Get Module Status
     * POST /teacher/module-status
     */
    public function module_status()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->teacher_auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $auth_check = $this->teacher_auth_model->auth();
                if ($auth_check['status'] == 200) {
                    $staff_id = $auth_check['staff_id'];
                    $params = json_decode(file_get_contents('php://input'), true);

                    if (!isset($params['module_code'])) {
                        json_output(400, array(
                            'status' => 400,
                            'message' => 'Module code parameter is required.'
                        ));
                        return;
                    }

                    $module_code = $params['module_code'];
                    $modules = $this->teacher_permission_model->getTeacherModules($staff_id);

                    $module_found = false;
                    $module_info = null;

                    foreach ($modules as $module) {
                        if ($module['group_code'] == $module_code) {
                            $module_found = true;
                            $module_info = $module;
                            break;
                        }
                    }

                    $response = array(
                        'status' => 1,
                        'message' => 'Module status retrieved successfully.',
                        'data' => array(
                            'module_code' => $module_code,
                            'is_accessible' => $module_found,
                            'module_info' => $module_info
                        )
                    );

                    json_output(200, $response);
                } else {
                    json_output(401, $auth_check);
                }
            }
        }
    }

    /**
     * Get Teacher Features Access
     * GET /teacher/features
     */
    public function features()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'GET') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->teacher_auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $auth_check = $this->teacher_auth_model->auth();
                if ($auth_check['status'] == 200) {
                    $staff_id = $auth_check['staff_id'];

                    // Define common teacher features and check access
                    $features = array(
                        'student_management' => array(
                            'name' => 'Student Management',
                            'permissions' => array(
                                array('category' => 'student_information', 'permission' => 'view'),
                                array('category' => 'student_information', 'permission' => 'edit')
                            )
                        ),
                        'attendance' => array(
                            'name' => 'Attendance Management',
                            'permissions' => array(
                                array('category' => 'attendance', 'permission' => 'view'),
                                array('category' => 'attendance', 'permission' => 'add')
                            )
                        ),
                        'examinations' => array(
                            'name' => 'Examinations',
                            'permissions' => array(
                                array('category' => 'examinations', 'permission' => 'view'),
                                array('category' => 'examinations', 'permission' => 'add')
                            )
                        ),
                        'homework' => array(
                            'name' => 'Homework Management',
                            'permissions' => array(
                                array('category' => 'homework', 'permission' => 'view'),
                                array('category' => 'homework', 'permission' => 'add')
                            )
                        ),
                        'lesson_plan' => array(
                            'name' => 'Lesson Planning',
                            'permissions' => array(
                                array('category' => 'lesson_plan', 'permission' => 'view'),
                                array('category' => 'lesson_plan', 'permission' => 'add')
                            )
                        ),
                        'communicate' => array(
                            'name' => 'Communication',
                            'permissions' => array(
                                array('category' => 'communicate', 'permission' => 'view'),
                                array('category' => 'communicate', 'permission' => 'add')
                            )
                        ),
                        'reports' => array(
                            'name' => 'Reports',
                            'permissions' => array(
                                array('category' => 'reports', 'permission' => 'view')
                            )
                        )
                    );

                    $feature_access = array();
                    foreach ($features as $feature_code => $feature_data) {
                        $has_access = false;
                        $granted_permissions = array();

                        foreach ($feature_data['permissions'] as $perm) {
                            $has_perm = $this->teacher_permission_model->hasPrivilege(
                                $staff_id,
                                $perm['category'],
                                $perm['permission']
                            );

                            if ($has_perm) {
                                $has_access = true;
                                $granted_permissions[] = $perm['permission'];
                            }
                        }

                        $feature_access[] = array(
                            'feature_code' => $feature_code,
                            'feature_name' => $feature_data['name'],
                            'has_access' => $has_access,
                            'granted_permissions' => $granted_permissions,
                            'total_permissions' => count($feature_data['permissions'])
                        );
                    }

                    $response = array(
                        'status' => 1,
                        'message' => 'Teacher features access retrieved successfully.',
                        'data' => array(
                            'features' => $feature_access,
                            'total_features' => count($feature_access),
                            'accessible_features' => count(array_filter($feature_access, function($f) {
                                return $f['has_access'];
                            }))
                        )
                    );

                    json_output(200, $response);
                } else {
                    json_output(401, $auth_check);
                }
            }
        }
    }

    /**
     * Get Teacher Dashboard Summary
     * GET /teacher/dashboard-summary
     */
    public function dashboard_summary()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'GET') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->teacher_auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $auth_check = $this->teacher_auth_model->auth();
                if ($auth_check['status'] == 200) {
                    $staff_id = $auth_check['staff_id'];

                    // Get comprehensive summary
                    $role = $this->teacher_permission_model->getTeacherRole($staff_id);
                    $permissions = $this->teacher_permission_model->getTeacherPermissions($staff_id);
                    $modules = $this->teacher_permission_model->getTeacherModules($staff_id);
                    $menus = $this->teacher_permission_model->getTeacherMenus($staff_id);

                    // Calculate statistics
                    $total_permissions = 0;
                    $active_permissions = 0;
                    foreach ($permissions as $group) {
                        foreach ($group['permissions'] as $perm) {
                            $total_permissions++;
                            if ($perm['can_view'] || $perm['can_add'] || $perm['can_edit'] || $perm['can_delete']) {
                                $active_permissions++;
                            }
                        }
                    }

                    $total_submenus = 0;
                    foreach ($menus as $menu) {
                        $total_submenus += count($menu['submenus']);
                    }

                    $response = array(
                        'status' => 1,
                        'message' => 'Dashboard summary retrieved successfully.',
                        'data' => array(
                            'role_info' => array(
                                'id' => $role ? $role->id : null,
                                'name' => $role ? $role->name : 'Unknown',
                                'is_superadmin' => $role ? (bool)$role->is_superadmin : false
                            ),
                            'access_summary' => array(
                                'total_permission_groups' => count($permissions),
                                'total_permissions' => $total_permissions,
                                'active_permissions' => $active_permissions,
                                'permission_percentage' => $total_permissions > 0 ?
                                    round(($active_permissions / $total_permissions) * 100, 2) : 0,
                                'accessible_modules' => count($modules),
                                'main_menus' => count($menus),
                                'submenus' => $total_submenus
                            ),
                            'quick_stats' => array(
                                'has_student_access' => $this->teacher_permission_model->hasPrivilege($staff_id, 'student_information', 'view'),
                                'has_attendance_access' => $this->teacher_permission_model->hasPrivilege($staff_id, 'attendance', 'view'),
                                'has_exam_access' => $this->teacher_permission_model->hasPrivilege($staff_id, 'examinations', 'view'),
                                'has_homework_access' => $this->teacher_permission_model->hasPrivilege($staff_id, 'homework', 'view'),
                                'has_report_access' => $this->teacher_permission_model->hasPrivilege($staff_id, 'reports', 'view')
                            )
                        )
                    );

                    json_output(200, $response);
                } else {
                    json_output(401, $auth_check);
                }
            }
        }
    }

    /**
     * Simple test method - no dependencies
     */
    public function test()
    {
        try {
            $response = array(
                'status' => 1,
                'message' => 'Teacher webservice test successful',
                'timestamp' => date('Y-m-d H:i:s'),
                'controller' => 'Teacher_webservice',
                'method' => 'test',
                'environment' => ENVIRONMENT,
                'php_version' => PHP_VERSION,
                'codeigniter_version' => CI_VERSION
            );
            
            json_output(200, $response);
        } catch (Exception $e) {
            $error_response = array(
                'status' => 0,
                'message' => 'Test method failed',
                'error' => array(
                    'type' => get_class($e),
                    'message' => $e->getMessage()
                ),
                'timestamp' => date('Y-m-d H:i:s')
            );
            
            json_output(500, $error_response);
        }
    }

    /**
     * Simple menu test - minimal dependencies using actual database structure
     */
    public function simple_menu()
    {
        try {
            $method = $this->input->server('REQUEST_METHOD');

            if ($method != 'POST') {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Bad request. Only POST method allowed.',
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            // Get JSON input
            $json_input = json_decode($this->input->raw_input_stream, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid JSON format in request body',
                    'error' => json_last_error_msg(),
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }
            
            if (empty($json_input) || !isset($json_input['staff_id'])) {
                json_output(400, array(
                    'status' => 0, 
                    'message' => 'staff_id is required in request body',
                    'example' => array('staff_id' => 123),
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            $staff_id = intval($json_input['staff_id']);

            if ($staff_id <= 0) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'staff_id must be a valid positive integer',
                    'provided' => $json_input['staff_id'],
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            // Check database connection
            if (!$this->db->conn_id) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Database connection failed',
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            // Get staff info directly from database
            $this->db->select('s.*, r.name as role_name, r.is_superadmin, r.id as role_id');
            $this->db->from('staff s');
            $this->db->join('staff_roles sr', 'sr.staff_id = s.id', 'left');
            $this->db->join('roles r', 'r.id = sr.role_id', 'left');
            $this->db->where('s.id', $staff_id);
            $this->db->where('s.is_active', 1);
            
            $query = $this->db->get();
            
            if (!$query) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Database query failed',
                    'error' => $this->db->error(),
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }
            
            $staff_info = $query->row();

            if (!$staff_info) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Staff member not found or inactive',
                    'staff_id' => $staff_id,
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            // Check if superadmin
            $is_superadmin = ($staff_info->role_id == 7 || $staff_info->is_superadmin == 1);

            // Get ALL menus (we'll filter by access_permissions)
            $this->db->select('*');
            $this->db->from('sidebar_menus');
            $this->db->where('is_active', 1);
            $this->db->where('sidebar_display', 1);
            $this->db->order_by('level');
            $menu_query = $this->db->get();

            if (!$menu_query) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Failed to fetch menus',
                    'error' => $this->db->error(),
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            $all_menus = $menu_query->result_array();

            // Get ALL submenus
            $this->db->select('*');
            $this->db->from('sidebar_sub_menus');
            $this->db->where('is_active', 1);
            $this->db->order_by('sidebar_menu_id, level');
            $submenu_query = $this->db->get();
            $all_submenus = $submenu_query ? $submenu_query->result_array() : array();

            // Group submenus by menu_id
            $submenus_by_menu = array();
            foreach ($all_submenus as $submenu) {
                $submenus_by_menu[$submenu['sidebar_menu_id']][] = $submenu;
            }

            // Filter menus and submenus using access_permissions (like admin dashboard)
            $menus = array();
            foreach ($all_menus as $menu) {
                // Check menu permission using access_permissions field
                $module_permission = $this->access_permission_sidebar_remove_pipe($menu['access_permissions']);
                $module_access = false;

                if ($is_superadmin) {
                    $module_access = true;
                } elseif (!empty($module_permission)) {
                    foreach ($module_permission as $m_permission_value) {
                        $cat_permission = $this->access_permission_remove_comma($m_permission_value);

                        if (count($cat_permission) >= 2) {
                            if ($this->hasPrivilege($staff_info->role_id, $staff_info->role_name, $cat_permission[0], $cat_permission[1])) {
                                $module_access = true;
                                break;
                            }
                        }
                    }
                }

                if ($module_access) {
                    // Filter submenus for this menu
                    $menu['submenus'] = array();

                    if (isset($submenus_by_menu[$menu['id']])) {
                        foreach ($submenus_by_menu[$menu['id']] as $submenu) {
                            $sidebar_permission = $this->access_permission_sidebar_remove_pipe($submenu['access_permissions']);
                            $sidebar_access = false;

                            if ($is_superadmin) {
                                $sidebar_access = true;
                            } elseif (!empty($sidebar_permission)) {
                                foreach ($sidebar_permission as $sidebar_permission_value) {
                                    $sidebar_cat_permission = $this->access_permission_remove_comma($sidebar_permission_value);

                                    if (count($sidebar_cat_permission) >= 2) {
                                        if ($this->hasPrivilege($staff_info->role_id, $staff_info->role_name, $sidebar_cat_permission[0], $sidebar_cat_permission[1])) {
                                            $sidebar_access = true;
                                            break;
                                        }
                                    }
                                }
                            }

                            if ($sidebar_access) {
                                $menu['submenus'][] = $submenu;
                            }
                        }
                    }

                    $menus[] = $menu;
                }
            }

            $response = array(
                'status' => 1,
                'message' => 'Menu items retrieved successfully',
                'data' => array(
                    'staff_id' => $staff_id,
                    'staff_info' => array(
                        'id' => (int)$staff_info->id,
                        'name' => $staff_info->name,
                        'surname' => $staff_info->surname,
                        'employee_id' => $staff_info->employee_id,
                        'full_name' => trim($staff_info->name . ' ' . $staff_info->surname)
                    ),
                    'role' => array(
                        'id' => $staff_info->role_id ? (int)$staff_info->role_id : null,
                        'name' => $staff_info->role_name ? $staff_info->role_name : 'No Role Assigned',
                        'is_superadmin' => $is_superadmin
                    ),
                    'menus' => $menus,
                    'total_menus' => count($menus),
                    'timestamp' => date('Y-m-d H:i:s')
                )
            );
            
            json_output(200, $response);
            
        } catch (Exception $e) {
            $error_response = array(
                'status' => 0,
                'message' => 'Exception occurred while retrieving menu items',
                'error' => array(
                    'type' => get_class($e),
                    'message' => $e->getMessage(),
                    'file' => basename($e->getFile()),
                    'line' => $e->getLine()
                ),
                'staff_id' => isset($staff_id) ? $staff_id : null,
                'timestamp' => date('Y-m-d H:i:s')
            );
            
            log_message('error', 'Simple Menu Exception: ' . $e->getMessage());
            json_output(500, $error_response);
        }
    }

    /**
     * Simple test method
     */
    public function debug_test()
    {
        json_output(200, array(
            'status' => 1,
            'message' => 'Teacher webservice debug test successful',
            'timestamp' => date('Y-m-d H:i:s'),
            'controller' => 'Teacher_webservice'
        ));
    }

    /**
     * Get Staff Attendance Summary
     * POST /teacher/attendance-summary
     *
     * Comprehensive attendance API that returns detailed attendance statistics
     * for staff members including dates, leave information, and summaries.
     */
    public function attendance_summary()
    {
        // Enable error reporting for debugging
        error_reporting(E_ALL);
        ini_set('display_errors', 0); // Don't display errors in output

        // Log that method was called
        log_message('info', 'attendance_summary method called');

        try {
            $method = $this->input->server('REQUEST_METHOD');

            if ($method != 'POST') {
                json_output(400, array('status' => 400, 'message' => 'Bad request. Only POST method allowed.'));
                return;
            }

            $check_auth_client = $this->teacher_auth_model->check_auth_client();
            if (!$check_auth_client) {
                json_output(401, array('status' => 401, 'message' => 'Unauthorized. Please check Client-Service and Auth-Key headers.'));
                return;
            }

            // For attendance summary, we only require client authentication
            // This allows administrative access to attendance data without user-specific tokens

            // Load required models
            $this->load->model('staffattendancemodel');
            $this->load->model('leaverequest_model');

            // Get request parameters
            $params = json_decode(file_get_contents('php://input'), true);

            // Validate JSON input
            if (json_last_error() !== JSON_ERROR_NONE) {
                json_output(400, array(
                    'status' => 400,
                    'message' => 'Invalid JSON format in request body.'
                ));
                return;
            }

            // Extract parameters with defaults
            $staff_id = isset($params['staff_id']) ? (int)$params['staff_id'] : null;
            $from_date = isset($params['from_date']) ? trim($params['from_date']) : null;
            $to_date = isset($params['to_date']) ? trim($params['to_date']) : null;

            // Validate staff_id if provided
            if ($staff_id !== null && $staff_id <= 0) {
                json_output(400, array(
                    'status' => 400,
                    'message' => 'Invalid staff_id. Must be a positive integer.'
                ));
                return;
            }

            // Validate date formats if provided
            if ($from_date && !$this->isValidDate($from_date)) {
                json_output(400, array(
                    'status' => 400,
                    'message' => 'Invalid from_date format. Use YYYY-MM-DD format.'
                ));
                return;
            }

            if ($to_date && !$this->isValidDate($to_date)) {
                json_output(400, array(
                    'status' => 400,
                    'message' => 'Invalid to_date format. Use YYYY-MM-DD format.'
                ));
                return;
            }

            // Check date range validity
            if ($from_date && $to_date && strtotime($from_date) > strtotime($to_date)) {
                json_output(400, array(
                    'status' => 400,
                    'message' => 'from_date cannot be greater than to_date.'
                ));
                return;
            }

            // Get attendance summary data
            $attendance_data = $this->staffattendancemodel->getAttendanceSummary($staff_id, $from_date, $to_date);

            // Check for errors in the model response
            if (isset($attendance_data['error'])) {
                json_output(400, array(
                    'status' => 400,
                    'message' => $attendance_data['error']
                ));
                return;
            }

            // Prepare successful response
            $response = array(
                'status' => 1,
                'message' => 'Attendance summary retrieved successfully.',
                'data' => $attendance_data,
                'request_info' => array(
                    'staff_id' => $staff_id,
                    'from_date' => $from_date ?: date('Y-01-01'),
                    'to_date' => $to_date ?: date('Y-12-31'),
                    'generated_at' => date('Y-m-d H:i:s')
                )
            );

            json_output(200, $response);

        } catch (Exception $e) {
            // Log the error for debugging
            log_message('error', 'Staff Attendance Summary API Error: ' . $e->getMessage());

            json_output(500, array(
                'status' => 500,
                'message' => 'Internal server error: ' . $e->getMessage()
            ));
        } catch (Error $e) {
            // Log PHP errors
            log_message('error', 'Staff Attendance Summary PHP Error: ' . $e->getMessage());

            json_output(500, array(
                'status' => 500,
                'message' => 'PHP Error: ' . $e->getMessage()
            ));
        }
    }

    /**
     * Get Staff Attendance - Simplified endpoint that automatically finds all attendance data
     * POST /teacher/staff-attendance
     *
     * This endpoint automatically determines the date range based on available data
     * and returns all attendance records for the specified staff member.
     */
    public function staff_attendance()
    {
        try {
            $method = $this->input->server('REQUEST_METHOD');

            if ($method != 'POST') {
                json_output(400, array('status' => 400, 'message' => 'Bad request. Only POST method allowed.'));
                return;
            }

            $check_auth_client = $this->teacher_auth_model->check_auth_client();
            if (!$check_auth_client) {
                json_output(401, array('status' => 401, 'message' => 'Unauthorized. Please check Client-Service and Auth-Key headers.'));
                return;
            }

            // Load required models
            $this->load->model('staffattendancemodel');

            // Get request parameters
            $params = json_decode(file_get_contents('php://input'), true);

            // Validate JSON input
            if (json_last_error() !== JSON_ERROR_NONE) {
                json_output(400, array(
                    'status' => 400,
                    'message' => 'Invalid JSON format in request body.'
                ));
                return;
            }

            // Extract staff_id (required for this endpoint)
            $staff_id = isset($params['staff_id']) ? (int)$params['staff_id'] : null;

            if (empty($staff_id)) {
                json_output(400, array(
                    'status' => 400,
                    'message' => 'staff_id parameter is required.'
                ));
                return;
            }

            // Get attendance data without specifying dates (will auto-detect range)
            $attendance_data = $this->staffattendancemodel->getAttendanceSummary($staff_id);

            // Check for errors in the model response
            if (isset($attendance_data['error'])) {
                json_output(400, array(
                    'status' => 400,
                    'message' => $attendance_data['error']
                ));
                return;
            }

            // Prepare successful response
            $response = array(
                'status' => 1,
                'message' => 'Staff attendance retrieved successfully.',
                'data' => $attendance_data,
                'note' => 'This endpoint automatically detects the date range based on available attendance data.',
                'generated_at' => date('Y-m-d H:i:s')
            );

            json_output(200, $response);

        } catch (Exception $e) {
            log_message('error', 'Staff Attendance API Error: ' . $e->getMessage());

            json_output(500, array(
                'status' => 500,
                'message' => 'Internal server error: ' . $e->getMessage()
            ));
        } catch (Error $e) {
            log_message('error', 'Staff Attendance PHP Error: ' . $e->getMessage());

            json_output(500, array(
                'status' => 500,
                'message' => 'PHP Error: ' . $e->getMessage()
            ));
        }
    }

    /**
     * Validate date format (YYYY-MM-DD)
     */
    private function isValidDate($date)
    {
        if (empty($date)) {
            return false;
        }
        $d = date_create_from_format('Y-m-d', $date);
        return $d && date_format($d, 'Y-m-d') === $date;
    }

    /**
     * Debug Menu - Test menu retrieval without authentication
     * GET /teacher/debug-menu?staff_id=1
     */
    public function debug_menu()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'GET') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
            return;
        }

        $staff_id = $this->input->get('staff_id');
        if (empty($staff_id)) {
            json_output(400, array(
                'status' => 400,
                'message' => 'staff_id parameter is required.'
            ));
            return;
        }

        try {
            // Load the teacher permission model
            $this->load->model('teacher_permission_model');
            
            // Get role information
            $role = $this->teacher_permission_model->getTeacherRole($staff_id);
            
            // Get menus
            $menus = $this->teacher_permission_model->getTeacherMenus($staff_id);
            
            // Get permissions
            $permissions = $this->teacher_permission_model->getTeacherPermissions($staff_id);
            
            // Get staff info from database
            $this->db->select('s.*, r.name as role_name, r.is_superadmin');
            $this->db->from('staff s');
            $this->db->join('staff_roles sr', 'sr.staff_id = s.id', 'left');
            $this->db->join('roles r', 'r.id = sr.role_id', 'left');
            $this->db->where('s.id', $staff_id);
            $staff_info = $this->db->get()->row();
            
            $response = array(
                'status' => 1,
                'message' => 'Debug menu data retrieved successfully.',
                'data' => array(
                    'staff_id' => $staff_id,
                    'staff_info' => $staff_info,
                    'role' => $role,
                    'menus' => $menus,
                    'total_menus' => count($menus),
                    'permissions' => $permissions,
                    'debug_info' => array(
                        'timestamp' => date('Y-m-d H:i:s'),
                        'staff_exists' => !empty($staff_info),
                        'role_found' => !empty($role),
                        'menu_count' => count($menus),
                        'permission_groups' => count($permissions)
                    )
                )
            );
            
            json_output(200, $response);
            
        } catch (Exception $e) {
            $error_response = array(
                'status' => 0,
                'message' => 'Error in debug menu retrieval',
                'error' => $e->getMessage(),
                'debug_info' => array(
                    'staff_id' => $staff_id,
                    'timestamp' => date('Y-m-d H:i:s'),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                )
            );
            json_output(500, $error_response);
        }
    }

    /**
     * Parse access_permissions field (replicate menu_helper.php logic)
     * Removes pipe signs and parentheses
     */
    private function access_permission_sidebar_remove_pipe($access_permissions)
    {
        if (empty($access_permissions)) {
            return array();
        }
        // remove pipe sign ||
        $module_permission = array_map('trim', explode('||', preg_replace('/\(\'|\'|\)/', '', $access_permissions)));
        return $module_permission;
    }

    /**
     * Parse comma-separated permission values
     */
    private function access_permission_remove_comma($m_permission_value)
    {
        if (empty($m_permission_value)) {
            return array();
        }
        // remove comma
        $module_permission_seprated = array_map('trim', explode(',', preg_replace('/\s+/', '', $m_permission_value)));
        return $module_permission_seprated;
    }

    /**
     * Check if staff has privilege (replicate RBAC logic)
     */
    private function hasPrivilege($role_id, $role_name, $category, $permission)
    {
        // Super Admin has all privileges
        if ($role_name == 'Super Admin') {
            return true;
        }

        // Check if rolepermission_model is loaded
        if (!isset($this->rolepermission_model)) {
            // Try to load it
            try {
                $this->load->model('rolepermission_model');
            } catch (Exception $e) {
                log_message('error', 'Failed to load rolepermission_model: ' . $e->getMessage());
                return false;
            }
        }

        // Verify model is loaded
        if (!isset($this->rolepermission_model) || !is_object($this->rolepermission_model)) {
            log_message('error', 'rolepermission_model is not available');
            return false;
        }

        try {
            // Get permission from database
            $role_perm = $this->rolepermission_model->getPermissionByRoleandCategory($role_id, trim($category));

            if ($role_perm && isset($role_perm[$permission])) {
                return ($role_perm[$permission] == 1);
            }
        } catch (Exception $e) {
            log_message('error', 'Error checking privilege: ' . $e->getMessage());
            return false;
        }

        return false;
    }

    /**
     * Get Comprehensive Staff Profile
     * POST /teacher/profile
     * Body: {"staff_id": 2}
     *
     * Returns complete staff profile including:
     * - Personal information
     * - Payroll records
     * - Leave records
     * - Attendance summary
     * - File paths (documents, QR code, profile image)
     */
    public function profile()
    {
        try {
            $method = $this->input->server('REQUEST_METHOD');

            if ($method != 'POST') {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Bad request. Only POST method allowed.',
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            // Get JSON input
            $json_input = json_decode($this->input->raw_input_stream, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid JSON format in request body',
                    'error' => json_last_error_msg(),
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            if (empty($json_input) || !isset($json_input['staff_id'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'staff_id is required in request body',
                    'example' => array('staff_id' => 2),
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            $staff_id = intval($json_input['staff_id']);

            if ($staff_id <= 0) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'staff_id must be a valid positive integer',
                    'provided' => $json_input['staff_id'],
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            // Check database connection
            if (!$this->db->conn_id) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Database connection failed',
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            // Load required models
            $this->load->model('staffattendancemodel');
            $this->load->model('leaverequest_model');

            // Get staff personal information with all joins
            $this->db->select('s.*,
                sd.designation as designation_name,
                d.department_name,
                r.name as role_name,
                r.id as role_id,
                r.is_superadmin');
            $this->db->from('staff s');
            $this->db->join('staff_designation sd', 'sd.id = s.designation', 'left');
            $this->db->join('department d', 'd.id = s.department', 'left');
            $this->db->join('staff_roles sr', 'sr.staff_id = s.id', 'left');
            $this->db->join('roles r', 'r.id = sr.role_id', 'left');
            $this->db->where('s.id', $staff_id);

            $query = $this->db->get();

            if (!$query) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Database query failed',
                    'error' => $this->db->error(),
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            $staff_info = $query->row();

            if (!$staff_info) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Staff member not found',
                    'staff_id' => $staff_id,
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            // Build personal information
            $personal_info = array(
                'id' => (int)$staff_info->id,
                'employee_id' => $staff_info->employee_id,
                'name' => $staff_info->name,
                'surname' => $staff_info->surname,
                'full_name' => trim($staff_info->name . ' ' . $staff_info->surname),
                'designation' => $staff_info->designation_name,
                'department' => $staff_info->department_name,
                'phone' => $staff_info->contact_no,
                'email' => $staff_info->email,
                'emergency_contact' => $staff_info->emergency_contact_no,
                'qualification' => $staff_info->qualification,
                'work_experience' => $staff_info->work_exp,
                'date_of_joining' => $staff_info->date_of_joining,
                'date_of_birth' => $staff_info->dob,
                'marital_status' => $staff_info->marital_status,
                'gender' => $staff_info->gender,
                'father_name' => $staff_info->father_name,
                'mother_name' => $staff_info->mother_name,
                'local_address' => $staff_info->local_address,
                'permanent_address' => $staff_info->permanent_address,
                'note' => $staff_info->note,
                'is_active' => (int)$staff_info->is_active,
                'role' => array(
                    'id' => $staff_info->role_id ? (int)$staff_info->role_id : null,
                    'name' => $staff_info->role_name ? $staff_info->role_name : 'No Role Assigned',
                    'is_superadmin' => $staff_info->is_superadmin ? (bool)$staff_info->is_superadmin : false
                ),
                'bank_details' => array(
                    'account_title' => $staff_info->account_title,
                    'bank_account_no' => $staff_info->bank_account_no,
                    'bank_name' => $staff_info->bank_name,
                    'ifsc_code' => $staff_info->ifsc_code,
                    'bank_branch' => $staff_info->bank_branch
                ),
                'employment_details' => array(
                    'epf_no' => $staff_info->epf_no,
                    'basic_salary' => $staff_info->basic_salary ? (float)$staff_info->basic_salary : null,
                    'contract_type' => $staff_info->contract_type,
                    'payscale' => $staff_info->payscale,
                    'shift' => $staff_info->shift,
                    'location' => $staff_info->location,
                    'date_of_leaving' => $staff_info->date_of_leaving
                ),
                'social_media' => array(
                    'facebook' => $staff_info->facebook,
                    'twitter' => $staff_info->twitter,
                    'linkedin' => $staff_info->linkedin,
                    'instagram' => $staff_info->instagram
                )
            );

            // Get payroll information
            $this->db->select('*');
            $this->db->from('staff_payslip');
            $this->db->where('staff_id', $staff_id);
            $this->db->order_by('year DESC, month DESC');
            $payroll_records = $this->db->get()->result_array();

            // Format payroll records
            $payroll_info = array(
                'records' => array(),
                'summary' => array(
                    'total_records' => count($payroll_records),
                    'total_net_salary' => 0,
                    'total_allowances' => 0,
                    'total_deductions' => 0,
                    'total_tax' => 0
                )
            );

            foreach ($payroll_records as $payroll) {
                $payroll_info['records'][] = array(
                    'id' => (int)$payroll['id'],
                    'month' => $payroll['month'],
                    'year' => $payroll['year'],
                    'basic_salary' => (float)$payroll['basic'],
                    'total_allowance' => (float)$payroll['total_allowance'],
                    'total_deduction' => (float)$payroll['total_deduction'],
                    'leave_deduction' => (int)$payroll['leave_deduction'],
                    'tax' => $payroll['tax'],
                    'net_salary' => (float)$payroll['net_salary'],
                    'status' => $payroll['status'],
                    'payment_mode' => $payroll['payment_mode'],
                    'payment_date' => $payroll['payment_date'],
                    'remark' => $payroll['remark'],
                    'created_at' => $payroll['created_at']
                );

                // Calculate summary
                if ($payroll['status'] == 'paid') {
                    $payroll_info['summary']['total_net_salary'] += (float)$payroll['net_salary'];
                    $payroll_info['summary']['total_allowances'] += (float)$payroll['total_allowance'];
                    $payroll_info['summary']['total_deductions'] += (float)$payroll['total_deduction'];
                    $payroll_info['summary']['total_tax'] += (float)$payroll['tax'];
                }
            }

            // Get leave information
            $this->db->select('slr.*, lt.type as leave_type_name, s.name as applied_by_name, s.surname as applied_by_surname, s.employee_id as applied_by_employee_id');
            $this->db->from('staff_leave_request slr');
            $this->db->join('leave_types lt', 'lt.id = slr.leave_type_id', 'left');
            $this->db->join('staff s', 's.id = slr.applied_by', 'left');
            $this->db->where('slr.staff_id', $staff_id);
            $this->db->order_by('slr.created_at DESC');
            $leave_records = $this->db->get()->result_array();

            // Format leave records
            $leave_info = array(
                'records' => array(),
                'summary' => array(
                    'total_requests' => count($leave_records),
                    'approved_count' => 0,
                    'pending_count' => 0,
                    'disapproved_count' => 0,
                    'total_leave_days' => 0,
                    'approved_leave_days' => 0
                )
            );

            foreach ($leave_records as $leave) {
                $applied_by_full = '';
                if (!empty($leave['applied_by_name'])) {
                    $applied_by_full = trim($leave['applied_by_name'] . ' ' . $leave['applied_by_surname']);
                    if (!empty($leave['applied_by_employee_id'])) {
                        $applied_by_full .= ' (' . $leave['applied_by_employee_id'] . ')';
                    }
                }

                $leave_info['records'][] = array(
                    'id' => (int)$leave['id'],
                    'leave_type' => $leave['leave_type_name'],
                    'leave_type_id' => (int)$leave['leave_type_id'],
                    'leave_from' => $leave['leave_from'],
                    'leave_to' => $leave['leave_to'],
                    'leave_days' => (int)$leave['leave_days'],
                    'employee_remark' => $leave['employee_remark'],
                    'admin_remark' => $leave['admin_remark'],
                    'status' => $leave['status'],
                    'applied_by' => $applied_by_full,
                    'applied_by_id' => $leave['applied_by'] ? (int)$leave['applied_by'] : null,
                    'document_file' => $leave['document_file'],
                    'apply_date' => $leave['date'],
                    'created_at' => $leave['created_at']
                );

                // Calculate summary
                $leave_info['summary']['total_leave_days'] += (int)$leave['leave_days'];

                if ($leave['status'] == 'approve' || $leave['status'] == 'approved') {
                    $leave_info['summary']['approved_count']++;
                    $leave_info['summary']['approved_leave_days'] += (int)$leave['leave_days'];
                } elseif ($leave['status'] == 'pending') {
                    $leave_info['summary']['pending_count']++;
                } elseif ($leave['status'] == 'disapprove' || $leave['status'] == 'disapproved') {
                    $leave_info['summary']['disapproved_count']++;
                }
            }

            // Get attendance information
            $attendance_info = $this->getStaffAttendanceInfo($staff_id);

            // Get file paths
            $file_paths = $this->getStaffFilePaths($staff_info, $staff_id);

            // Build final response
            $response = array(
                'status' => 1,
                'message' => 'Staff profile retrieved successfully',
                'data' => array(
                    'personal_information' => $personal_info,
                    'payroll_information' => $payroll_info,
                    'leave_information' => $leave_info,
                    'attendance_information' => $attendance_info,
                    'file_paths' => $file_paths
                ),
                'timestamp' => date('Y-m-d H:i:s')
            );

            json_output(200, $response);

        } catch (Exception $e) {
            $error_response = array(
                'status' => 0,
                'message' => 'Exception occurred while retrieving staff profile',
                'error' => array(
                    'type' => get_class($e),
                    'message' => $e->getMessage(),
                    'file' => basename($e->getFile()),
                    'line' => $e->getLine()
                ),
                'staff_id' => isset($staff_id) ? $staff_id : null,
                'timestamp' => date('Y-m-d H:i:s')
            );

            log_message('error', 'Staff Profile Exception: ' . $e->getMessage());
            json_output(500, $error_response);
        }
    }

    /**
     * Helper method to get staff attendance information with detailed monthly breakdown
     */
    private function getStaffAttendanceInfo($staff_id)
    {
        // Get attendance types with color coding
        $this->db->select('id, type, key_value');
        $this->db->from('staff_attendance_type');
        $this->db->where('is_active', 1);
        $this->db->order_by('id');
        $attendance_types_raw = $this->db->get()->result_array();

        // Define color mapping for attendance types
        $color_map = array(
            'P' => '#4CAF50',  // Green for Present
            'L' => '#FF9800',  // Orange for Late
            'A' => '#F44336',  // Red for Absent
            'H' => '#2196F3',  // Blue for Half Day
            'F' => '#9C27B0',  // Purple for Holiday
        );

        // Format attendance types with colors
        $attendance_types = array();
        foreach ($attendance_types_raw as $type) {
            $key = strtoupper($type['key_value']);
            $attendance_types[] = array(
                'id' => (int)$type['id'],
                'type' => $type['type'],
                'key_value' => $type['key_value'],
                'color' => isset($color_map[$key]) ? $color_map[$key] : '#9E9E9E'
            );
        }

        // Get all attendance records ordered by date DESC (most recent first)
        $this->db->select('sa.*, sat.type as attendance_type, sat.key_value');
        $this->db->from('staff_attendance sa');
        $this->db->join('staff_attendance_type sat', 'sat.id = sa.staff_attendance_type_id', 'left');
        $this->db->where('sa.staff_id', $staff_id);
        $this->db->order_by('sa.date', 'DESC');
        $attendance_records = $this->db->get()->result_array();

        // Initialize counters
        $present_count = 0;
        $late_count = 0;
        $absent_count = 0;
        $half_day_count = 0;
        $holiday_count = 0;

        // Group records by month and year
        $monthly_data = array();

        foreach ($attendance_records as $record) {
            $date = $record['date'];
            $key_value = strtoupper($record['key_value']);

            // Extract month and year
            $date_obj = new DateTime($date);
            $month = $date_obj->format('F');  // Full month name
            $year = $date_obj->format('Y');
            $day_name = $date_obj->format('l');  // Full day name (Monday, Tuesday, etc.)
            $month_year_key = $year . '-' . $date_obj->format('m');

            // Initialize month array if not exists
            if (!isset($monthly_data[$month_year_key])) {
                $monthly_data[$month_year_key] = array(
                    'month' => $month,
                    'year' => $year,
                    'month_number' => (int)$date_obj->format('m'),
                    'days' => array(),
                    'month_summary' => array(
                        'present' => 0,
                        'absent' => 0,
                        'late' => 0,
                        'half_day' => 0,
                        'holiday' => 0
                    )
                );
            }

            // Determine status label
            $status = 'unknown';
            if ($key_value == 'P') {
                $status = 'present';
                $present_count++;
                $monthly_data[$month_year_key]['month_summary']['present']++;
            } elseif ($key_value == 'L') {
                $status = 'late';
                $late_count++;
                $monthly_data[$month_year_key]['month_summary']['late']++;
            } elseif ($key_value == 'A') {
                $status = 'absent';
                $absent_count++;
                $monthly_data[$month_year_key]['month_summary']['absent']++;
            } elseif ($key_value == 'H') {
                $status = 'half_day';
                $half_day_count++;
                $monthly_data[$month_year_key]['month_summary']['half_day']++;
            } elseif ($key_value == 'F') {
                $status = 'holiday';
                $holiday_count++;
                $monthly_data[$month_year_key]['month_summary']['holiday']++;
            }

            // Add day record
            $monthly_data[$month_year_key]['days'][] = array(
                'date' => $date,
                'day_name' => $day_name,
                'status' => $status,
                'status_key' => $record['key_value'],
                'remark' => $record['remark'] ? $record['remark'] : ''
            );
        }

        // Convert monthly data to indexed array and sort by year-month DESC
        $monthly_breakdown = array_values($monthly_data);

        // Sort by year and month (most recent first)
        usort($monthly_breakdown, function($a, $b) {
            if ($a['year'] != $b['year']) {
                return $b['year'] - $a['year'];
            }
            return $b['month_number'] - $a['month_number'];
        });

        // Remove month_number from final output (it was only for sorting)
        foreach ($monthly_breakdown as &$month_data) {
            unset($month_data['month_number']);
        }

        // Calculate total records
        $total_records = count($attendance_records);

        // Calculate attendance percentage (present + half_day considered as attendance)
        $attendance_percentage = 0;
        if ($total_records > 0) {
            $attended = $present_count + ($half_day_count * 0.5);
            $attendance_percentage = round(($attended / $total_records) * 100, 2);
        }

        // Build final response
        return array(
            'summary' => array(
                'total_present' => $present_count,
                'total_absent' => $absent_count,
                'total_late' => $late_count,
                'total_half_day' => $half_day_count,
                'total_holiday' => $holiday_count,
                'total_records' => $total_records,
                'attendance_percentage' => $attendance_percentage
            ),
            'monthly_breakdown' => $monthly_breakdown,
            'attendance_types' => $attendance_types
        );
    }

    /**
     * Helper method to get staff file paths
     */
    private function getStaffFilePaths($staff_info, $staff_id)
    {
        $base_url = base_url();

        // Get timestamp for cache busting (similar to img_time() helper)
        $timestamp = '?' . time();

        // Profile image path with timestamp
        $profile_image = '';
        if (!empty($staff_info->image)) {
            $profile_image = $base_url . 'uploads/staff_images/' . $staff_info->image . $timestamp;
        } else {
            if ($staff_info->gender == 'Male') {
                $profile_image = $base_url . 'uploads/staff_images/default_male.jpg' . $timestamp;
            } else {
                $profile_image = $base_url . 'uploads/staff_images/default_female.jpg' . $timestamp;
            }
        }

        // QR code and barcode paths with timestamp
        $qr_code_path = '';
        $barcode_path = '';

        if (!empty($staff_info->employee_id)) {
            // Check if QR code file exists
            $qr_file = './uploads/staff_id_card/qrcode/' . $staff_info->employee_id . '.png';
            if (file_exists($qr_file)) {
                $qr_code_path = $base_url . 'uploads/staff_id_card/qrcode/' . $staff_info->employee_id . '.png' . $timestamp;
            }

            // Check if barcode file exists
            $barcode_file = './uploads/staff_id_card/barcodes/' . $staff_info->employee_id . '.png';
            if (file_exists($barcode_file)) {
                $barcode_path = $base_url . 'uploads/staff_id_card/barcodes/' . $staff_info->employee_id . '.png' . $timestamp;
            }
        }

        // Document paths
        $documents = array();

        if (!empty($staff_info->resume)) {
            $documents['resume'] = array(
                'filename' => $staff_info->resume,
                'path' => $base_url . 'uploads/staff_documents/' . $staff_id . '/' . $staff_info->resume,
                'type' => 'resume'
            );
        }

        if (!empty($staff_info->joining_letter)) {
            $documents['joining_letter'] = array(
                'filename' => $staff_info->joining_letter,
                'path' => $base_url . 'uploads/staff_documents/' . $staff_id . '/' . $staff_info->joining_letter,
                'type' => 'joining_letter'
            );
        }

        if (!empty($staff_info->resignation_letter)) {
            $documents['resignation_letter'] = array(
                'filename' => $staff_info->resignation_letter,
                'path' => $base_url . 'uploads/staff_documents/' . $staff_id . '/' . $staff_info->resignation_letter,
                'type' => 'resignation_letter'
            );
        }

        if (!empty($staff_info->other_document_file)) {
            $documents['other_document'] = array(
                'filename' => $staff_info->other_document_file,
                'name' => $staff_info->other_document_name,
                'path' => $base_url . 'uploads/staff_documents/' . $staff_id . '/' . $staff_info->other_document_file,
                'type' => 'other_document'
            );
        }

        return array(
            'profile_image' => $profile_image,
            'qr_code' => $qr_code_path,
            'barcode' => $barcode_path,
            'documents' => $documents
        );
    }

    /**
     * Handle 404 errors with JSON response
     */

    /**
     * Get classes with their associated sections
     * POST /teacher/classes-with-sections
     *
     * Request body (optional):
     * {
     *   "session_id": 21  // Optional: filter by session
     * }
     *
     * Returns: Hierarchical structure of classes with sections
     */
    public function classes_with_sections()
    {
        try {
            // Get JSON input
            $json_input = json_decode(file_get_contents('php://input'), true);

            // Check for JSON decode errors
            if (json_last_error() !== JSON_ERROR_NONE && file_get_contents('php://input') !== '') {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid JSON format in request body',
                    'error' => array(
                        'type' => 'JSON Parse Error',
                        'details' => json_last_error_msg()
                    ),
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            // Extract optional session_id filter
            $session_id = isset($json_input['session_id']) && !empty($json_input['session_id']) ? intval($json_input['session_id']) : null;

            // Extract optional include_inactive filter (default: false)
            $include_inactive = isset($json_input['include_inactive']) && $json_input['include_inactive'] === true;

            // Query to get classes
            $this->db->select('id, class as class_name, is_active');
            $this->db->from('classes');

            // Only filter by is_active if include_inactive is false
            if (!$include_inactive) {
                // Since all classes have is_active='no', we'll return all classes
                // Comment out the is_active filter to return all classes
                // $this->db->where('is_active', 'yes');
            }

            $this->db->order_by('class', 'ASC');

            $classes_query = $this->db->get();

            if (!$classes_query) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Failed to retrieve classes',
                    'error' => array(
                        'type' => 'Database Error',
                        'details' => $this->db->error()
                    ),
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            $classes = $classes_query->result();

            // Build hierarchical structure
            $result = array();

            foreach ($classes as $class) {
                // Query to get sections for this class
                $this->db->select('s.id, s.section as section_name, cs.is_active');
                $this->db->from('class_sections cs');
                $this->db->join('sections s', 's.id = cs.section_id', 'inner');
                $this->db->where('cs.class_id', $class->id);

                // Only filter by is_active if include_inactive is false
                if (!$include_inactive) {
                    // Comment out is_active filters to return all sections
                    // $this->db->where('cs.is_active', 'yes');
                    // $this->db->where('s.is_active', 'yes');
                }

                $this->db->order_by('s.section', 'ASC');

                $sections_query = $this->db->get();

                $sections = array();
                if ($sections_query && $sections_query->num_rows() > 0) {
                    foreach ($sections_query->result() as $section) {
                        $sections[] = array(
                            'section_id' => intval($section->id),
                            'section_name' => $section->section_name,
                            'is_active' => $section->is_active
                        );
                    }
                }

                // Add class with its sections to result
                $result[] = array(
                    'class_id' => intval($class->id),
                    'class_name' => $class->class_name,
                    'is_active' => $class->is_active,
                    'sections_count' => count($sections),
                    'sections' => $sections
                );
            }

            // Return success response
            json_output(200, array(
                'status' => 1,
                'message' => 'Classes with sections retrieved successfully',
                'filters_applied' => array(
                    'session_id' => $session_id
                ),
                'total_classes' => count($result),
                'data' => $result,
                'timestamp' => date('Y-m-d H:i:s')
            ));

        } catch (Exception $e) {
            json_output(500, array(
                'status' => 0,
                'message' => 'An error occurred while retrieving classes with sections',
                'error' => array(
                    'type' => 'Exception',
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ),
                'timestamp' => date('Y-m-d H:i:s')
            ));
        }
    }

    /**
     * Get sessions with their associated classes and sections in hierarchical structure
     * POST /teacher/sessions-with-classes-sections
     *
     * Request body (optional):
     * {
     *   "include_inactive": false  // Optional: include inactive sessions/classes/sections (default: false)
     * }
     *
     * Returns: Hierarchical structure of sessions → classes → sections
     */
    public function sessions_with_classes_sections()
    {
        try {
            // Get JSON input
            $json_input = json_decode(file_get_contents('php://input'), true);

            // Check for JSON decode errors
            if (json_last_error() !== JSON_ERROR_NONE && file_get_contents('php://input') !== '') {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid JSON format in request body',
                    'error' => array(
                        'type' => 'JSON Parse Error',
                        'details' => json_last_error_msg()
                    ),
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            // Extract optional include_inactive filter (default: false)
            $include_inactive = isset($json_input['include_inactive']) && $json_input['include_inactive'] === true;

            // Query to get sessions
            $this->db->select('id, session as session_name, is_active');
            $this->db->from('sessions');

            // Only filter by is_active if include_inactive is false
            if (!$include_inactive) {
                // Since all sessions might have is_active='no', we'll return all sessions
                // Comment out the is_active filter to return all sessions
                // $this->db->where('is_active', 'yes');
            }

            $this->db->order_by('session', 'ASC');

            $sessions_query = $this->db->get();

            if (!$sessions_query) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Failed to retrieve sessions',
                    'error' => array(
                        'type' => 'Database Error',
                        'details' => $this->db->error()
                    ),
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            $sessions = $sessions_query->result();

            // Build hierarchical structure
            $result = array();

            foreach ($sessions as $session) {
                // Query to get classes for this session through student_session table
                $this->db->distinct();
                $this->db->select('c.id, c.class as class_name, c.is_active');
                $this->db->from('student_session ss');
                $this->db->join('classes c', 'c.id = ss.class_id', 'inner');
                $this->db->where('ss.session_id', $session->id);

                // Only filter by is_active if include_inactive is false
                if (!$include_inactive) {
                    // Comment out is_active filters to return all classes
                    // $this->db->where('c.is_active', 'yes');
                }

                $this->db->order_by('c.class', 'ASC');

                $classes_query = $this->db->get();

                $classes = array();
                if ($classes_query && $classes_query->num_rows() > 0) {
                    foreach ($classes_query->result() as $class) {
                        // Query to get sections for this class and session
                        $this->db->distinct();
                        $this->db->select('s.id, s.section as section_name, s.is_active');
                        $this->db->from('student_session ss');
                        $this->db->join('sections s', 's.id = ss.section_id', 'inner');
                        $this->db->where('ss.session_id', $session->id);
                        $this->db->where('ss.class_id', $class->id);

                        // Only filter by is_active if include_inactive is false
                        if (!$include_inactive) {
                            // Comment out is_active filters to return all sections
                            // $this->db->where('s.is_active', 'yes');
                        }

                        $this->db->order_by('s.section', 'ASC');

                        $sections_query = $this->db->get();

                        $sections = array();
                        if ($sections_query && $sections_query->num_rows() > 0) {
                            foreach ($sections_query->result() as $section) {
                                $sections[] = array(
                                    'section_id' => intval($section->id),
                                    'section_name' => $section->section_name,
                                    'is_active' => $section->is_active
                                );
                            }
                        }

                        // Add class with its sections to classes array
                        $classes[] = array(
                            'class_id' => intval($class->id),
                            'class_name' => $class->class_name,
                            'is_active' => $class->is_active,
                            'sections_count' => count($sections),
                            'sections' => $sections
                        );
                    }
                }

                // Add session with its classes to result
                $result[] = array(
                    'session_id' => intval($session->id),
                    'session_name' => $session->session_name,
                    'is_active' => $session->is_active,
                    'classes_count' => count($classes),
                    'classes' => $classes
                );
            }

            // Return success response
            json_output(200, array(
                'status' => 1,
                'message' => 'Sessions with classes and sections retrieved successfully',
                'filters_applied' => array(
                    'include_inactive' => $include_inactive
                ),
                'total_sessions' => count($result),
                'data' => $result,
                'timestamp' => date('Y-m-d H:i:s')
            ));

        } catch (Exception $e) {
            json_output(500, array(
                'status' => 0,
                'message' => 'An error occurred while retrieving sessions with classes and sections',
                'error' => array(
                    'type' => 'Exception',
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ),
                'timestamp' => date('Y-m-d H:i:s')
            ));
        }
    }

    /**
     * Get students by class, section, and session
     * POST /teacher/students
     *
     * Request body:
     * {
     *   "class_id": 5,      // Optional - filter by class
     *   "section_id": 3,    // Optional - filter by section
     *   "session_id": 2     // Optional - filter by session
     * }
     *
     * If parameters are null or not provided, returns all active students
     */
    public function students()
    {
        try {
            $method = $this->input->server('REQUEST_METHOD');

            if ($method != 'POST') {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Bad request. Only POST method allowed.',
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            // Get JSON input
            $json_input = json_decode($this->input->raw_input_stream, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid JSON format in request body',
                    'error' => json_last_error_msg(),
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            // Extract filter parameters (all optional)
            $class_id = isset($json_input['class_id']) && !empty($json_input['class_id']) ? intval($json_input['class_id']) : null;
            $section_id = isset($json_input['section_id']) && !empty($json_input['section_id']) ? intval($json_input['section_id']) : null;
            $session_id = isset($json_input['session_id']) && !empty($json_input['session_id']) ? intval($json_input['session_id']) : null;

            // Check database connection
            if (!$this->db->conn_id) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Database connection failed',
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            // Build query with joins
            $this->db->select('s.id as student_id,
                s.admission_no,
                s.roll_no,
                s.firstname,
                s.middlename,
                s.lastname,
                s.dob,
                s.gender,
                s.email,
                s.mobileno,
                s.image,
                s.blood_group,
                s.father_name,
                s.father_phone,
                s.mother_name,
                s.mother_phone,
                s.guardian_name,
                s.guardian_phone,
                s.guardian_relation,
                s.current_address,
                s.permanent_address,
                s.category_id,
                s.is_active,
                ss.id as student_session_id,
                ss.session_id,
                ss.class_id,
                ss.section_id,
                c.class as class_name,
                sec.section as section_name,
                ses.session as session_name');

            $this->db->from('students s');
            $this->db->join('student_session ss', 'ss.student_id = s.id', 'left');
            $this->db->join('classes c', 'c.id = ss.class_id', 'left');
            $this->db->join('sections sec', 'sec.id = ss.section_id', 'left');
            $this->db->join('sessions ses', 'ses.id = ss.session_id', 'left');

            // Apply filters if provided
            if ($class_id !== null) {
                $this->db->where('ss.class_id', $class_id);
            }

            if ($section_id !== null) {
                $this->db->where('ss.section_id', $section_id);
            }

            if ($session_id !== null) {
                $this->db->where('ss.session_id', $session_id);
            }

            // Only get active students
            $this->db->where('s.is_active', 'yes');
            $this->db->order_by('s.firstname', 'ASC');

            $query = $this->db->get();

            if (!$query) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Database query failed',
                    'error' => $this->db->error(),
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            $students = $query->result_array();

            // Format student data
            $formatted_students = array();
            $base_url = base_url();
            $timestamp = '?' . time();

            foreach ($students as $student) {
                // Build full name
                $full_name = trim($student['firstname'] . ' ' . $student['middlename'] . ' ' . $student['lastname']);

                // Profile image URL
                $profile_image = '';
                if (!empty($student['image'])) {
                    $profile_image = $base_url . 'uploads/student_images/' . $student['image'] . $timestamp;
                } else {
                    // Default image based on gender
                    if ($student['gender'] == 'Male') {
                        $profile_image = $base_url . 'uploads/student_images/default_male.jpg' . $timestamp;
                    } else {
                        $profile_image = $base_url . 'uploads/student_images/default_female.jpg' . $timestamp;
                    }
                }

                $formatted_students[] = array(
                    'student_id' => (int)$student['student_id'],
                    'student_session_id' => (int)$student['student_session_id'],
                    'admission_no' => $student['admission_no'],
                    'roll_no' => $student['roll_no'],
                    'full_name' => $full_name,
                    'firstname' => $student['firstname'],
                    'middlename' => $student['middlename'],
                    'lastname' => $student['lastname'],
                    'dob' => $student['dob'],
                    'gender' => $student['gender'],
                    'email' => $student['email'],
                    'mobileno' => $student['mobileno'],
                    'blood_group' => $student['blood_group'],
                    'profile_image' => $profile_image,
                    'class_info' => array(
                        'class_id' => (int)$student['class_id'],
                        'class_name' => $student['class_name'],
                        'section_id' => (int)$student['section_id'],
                        'section_name' => $student['section_name'],
                        'session_id' => (int)$student['session_id'],
                        'session_name' => $student['session_name']
                    ),
                    'guardian_info' => array(
                        'father_name' => $student['father_name'],
                        'father_phone' => $student['father_phone'],
                        'mother_name' => $student['mother_name'],
                        'mother_phone' => $student['mother_phone'],
                        'guardian_name' => $student['guardian_name'],
                        'guardian_phone' => $student['guardian_phone'],
                        'guardian_relation' => $student['guardian_relation']
                    ),
                    'address_info' => array(
                        'current_address' => $student['current_address'],
                        'permanent_address' => $student['permanent_address']
                    ),
                    'category_id' => $student['category_id'],
                    'is_active' => $student['is_active']
                );
            }

            // Build response
            $response = array(
                'status' => 1,
                'message' => 'Students retrieved successfully',
                'filters_applied' => array(
                    'class_id' => $class_id,
                    'section_id' => $section_id,
                    'session_id' => $session_id
                ),
                'total_students' => count($formatted_students),
                'data' => $formatted_students,
                'timestamp' => date('Y-m-d H:i:s')
            );

            json_output(200, $response);

        } catch (Exception $e) {
            log_message('error', 'Students API error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error',
                'error' => $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s')
            ));
        }
    }

    /**
     * Create student category
     * POST /teacher/student-category/create
     *
     * Request body:
     * {
     *   "category_name": "Category Name",
     *   "is_active": "yes"  // Optional, defaults to "no"
     * }
     *
     * Returns: Created category details
     */
    public function student_category_create()
    {
        try {
            // Get JSON input
            $json_input = json_decode(file_get_contents('php://input'), true);

            // Check for JSON decode errors
            if (json_last_error() !== JSON_ERROR_NONE) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid JSON format in request body',
                    'error' => array(
                        'type' => 'JSON Parse Error',
                        'details' => json_last_error_msg()
                    ),
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            // Validate category_name
            if (!isset($json_input['category_name']) || empty(trim($json_input['category_name']))) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'category_name is required',
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            $category_name = trim($json_input['category_name']);
            $is_active = isset($json_input['is_active']) ? $json_input['is_active'] : 'no';

            // Validate is_active value
            if (!in_array($is_active, array('yes', 'no'))) {
                $is_active = 'no';
            }

            // Check if category already exists
            $this->db->where('category', $category_name);
            $existing = $this->db->get('categories');

            if ($existing && $existing->num_rows() > 0) {
                json_output(409, array(
                    'status' => 0,
                    'message' => 'Category with this name already exists',
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            // Insert new category
            $data = array(
                'category' => $category_name,
                'is_active' => $is_active,
                'created_at' => date('Y-m-d H:i:s')
            );

            $this->db->insert('categories', $data);

            if ($this->db->affected_rows() > 0) {
                $insert_id = $this->db->insert_id();

                json_output(201, array(
                    'status' => 1,
                    'message' => 'Student category created successfully',
                    'data' => array(
                        'category_id' => $insert_id,
                        'category_name' => $category_name,
                        'is_active' => $is_active,
                        'created_at' => date('Y-m-d H:i:s')
                    ),
                    'timestamp' => date('Y-m-d H:i:s')
                ));
            } else {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Failed to create student category',
                    'timestamp' => date('Y-m-d H:i:s')
                ));
            }

        } catch (Exception $e) {
            json_output(500, array(
                'status' => 0,
                'message' => 'An error occurred while creating student category',
                'error' => array(
                    'type' => 'Exception',
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ),
                'timestamp' => date('Y-m-d H:i:s')
            ));
        }
    }

    /**
     * Update student category
     * POST /teacher/student-category/update
     *
     * Request body:
     * {
     *   "category_id": 5,
     *   "category_name": "Updated Name",  // Optional
     *   "is_active": "yes"                // Optional
     * }
     *
     * Returns: Updated category details
     */
    public function student_category_update()
    {
        try {
            // Get JSON input
            $json_input = json_decode(file_get_contents('php://input'), true);

            // Check for JSON decode errors
            if (json_last_error() !== JSON_ERROR_NONE) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid JSON format in request body',
                    'error' => array(
                        'type' => 'JSON Parse Error',
                        'details' => json_last_error_msg()
                    ),
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            // Validate category_id
            if (!isset($json_input['category_id']) || empty($json_input['category_id'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'category_id is required',
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            $category_id = intval($json_input['category_id']);

            // Check if category exists
            $this->db->where('id', $category_id);
            $existing = $this->db->get('categories');

            if (!$existing || $existing->num_rows() == 0) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Student category not found',
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            // Build update data
            $update_data = array();

            if (isset($json_input['category_name']) && !empty(trim($json_input['category_name']))) {
                $category_name = trim($json_input['category_name']);

                // Check if new name already exists (excluding current category)
                $this->db->where('category', $category_name);
                $this->db->where('id !=', $category_id);
                $duplicate = $this->db->get('categories');

                if ($duplicate && $duplicate->num_rows() > 0) {
                    json_output(409, array(
                        'status' => 0,
                        'message' => 'Category with this name already exists',
                        'timestamp' => date('Y-m-d H:i:s')
                    ));
                    return;
                }

                $update_data['category'] = $category_name;
            }

            if (isset($json_input['is_active'])) {
                $is_active = $json_input['is_active'];
                if (in_array($is_active, array('yes', 'no'))) {
                    $update_data['is_active'] = $is_active;
                }
            }

            if (empty($update_data)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'No valid fields to update',
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            $update_data['updated_at'] = date('Y-m-d');

            // Update category
            $this->db->where('id', $category_id);
            $this->db->update('categories', $update_data);

            if ($this->db->affected_rows() >= 0) {
                // Get updated category
                $this->db->where('id', $category_id);
                $query = $this->db->get('categories');
                $row = $query->row();

                json_output(200, array(
                    'status' => 1,
                    'message' => 'Student category updated successfully',
                    'data' => array(
                        'category_id' => intval($row->id),
                        'category_name' => $row->category,
                        'is_active' => $row->is_active,
                        'created_at' => $row->created_at,
                        'updated_at' => $row->updated_at
                    ),
                    'timestamp' => date('Y-m-d H:i:s')
                ));
            } else {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Failed to update student category',
                    'timestamp' => date('Y-m-d H:i:s')
                ));
            }

        } catch (Exception $e) {
            json_output(500, array(
                'status' => 0,
                'message' => 'An error occurred while updating student category',
                'error' => array(
                    'type' => 'Exception',
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ),
                'timestamp' => date('Y-m-d H:i:s')
            ));
        }
    }

    /**
     * Delete student category
     * POST /teacher/student-category/delete
     *
     * Request body:
     * {
     *   "category_id": 5
     * }
     *
     * Returns: Success message
     */
    public function student_category_delete()
    {
        try {
            // Get JSON input
            $json_input = json_decode(file_get_contents('php://input'), true);

            // Check for JSON decode errors
            if (json_last_error() !== JSON_ERROR_NONE) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid JSON format in request body',
                    'error' => array(
                        'type' => 'JSON Parse Error',
                        'details' => json_last_error_msg()
                    ),
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            // Validate category_id
            if (!isset($json_input['category_id']) || empty($json_input['category_id'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'category_id is required',
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            $category_id = intval($json_input['category_id']);

            // Check if category exists
            $this->db->where('id', $category_id);
            $existing = $this->db->get('categories');

            if (!$existing || $existing->num_rows() == 0) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Student category not found',
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            // Check if category is being used by any students
            $this->db->where('category_id', $category_id);
            $students_using = $this->db->get('students');

            if ($students_using && $students_using->num_rows() > 0) {
                json_output(409, array(
                    'status' => 0,
                    'message' => 'Cannot delete category. It is being used by ' . $students_using->num_rows() . ' student(s)',
                    'students_count' => $students_using->num_rows(),
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            // Delete category
            $this->db->where('id', $category_id);
            $this->db->delete('categories');

            if ($this->db->affected_rows() > 0) {
                json_output(200, array(
                    'status' => 1,
                    'message' => 'Student category deleted successfully',
                    'category_id' => $category_id,
                    'timestamp' => date('Y-m-d H:i:s')
                ));
            } else {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Failed to delete student category',
                    'timestamp' => date('Y-m-d H:i:s')
                ));
            }

        } catch (Exception $e) {
            json_output(500, array(
                'status' => 0,
                'message' => 'An error occurred while deleting student category',
                'error' => array(
                    'type' => 'Exception',
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ),
                'timestamp' => date('Y-m-d H:i:s')
            ));
        }
    }

    /**
     * Get all student categories
     * POST /teacher/student-categories
     *
     * Returns: List of all student categories
     */
    public function student_categories()
    {
        try {
            // Query to get all categories
            $this->db->select('id, category as category_name, is_active, created_at, updated_at');
            $this->db->from('categories');
            $this->db->order_by('category', 'ASC');

            $query = $this->db->get();

            if (!$query) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Failed to retrieve student categories',
                    'error' => array(
                        'type' => 'Database Error',
                        'details' => $this->db->error()
                    ),
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            $categories = array();
            foreach ($query->result() as $row) {
                $categories[] = array(
                    'category_id' => intval($row->id),
                    'category_name' => $row->category_name,
                    'is_active' => $row->is_active,
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at
                );
            }

            json_output(200, array(
                'status' => 1,
                'message' => 'Student categories retrieved successfully',
                'total_categories' => count($categories),
                'data' => $categories,
                'timestamp' => date('Y-m-d H:i:s')
            ));

        } catch (Exception $e) {
            json_output(500, array(
                'status' => 0,
                'message' => 'An error occurred while retrieving student categories',
                'error' => array(
                    'type' => 'Exception',
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ),
                'timestamp' => date('Y-m-d H:i:s')
            ));
        }
    }

    /**
     * Get single student category
     * POST /teacher/student-category/get
     *
     * Request body:
     * {
     *   "category_id": 5
     * }
     *
     * Returns: Single category details
     */
    public function student_category_get()
    {
        try {
            // Get JSON input
            $json_input = json_decode(file_get_contents('php://input'), true);

            // Check for JSON decode errors
            if (json_last_error() !== JSON_ERROR_NONE) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid JSON format in request body',
                    'error' => array(
                        'type' => 'JSON Parse Error',
                        'details' => json_last_error_msg()
                    ),
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            // Validate category_id
            if (!isset($json_input['category_id']) || empty($json_input['category_id'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'category_id is required',
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            $category_id = intval($json_input['category_id']);

            // Query to get category
            $this->db->select('id, category as category_name, is_active, created_at, updated_at');
            $this->db->from('categories');
            $this->db->where('id', $category_id);

            $query = $this->db->get();

            if (!$query || $query->num_rows() == 0) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Student category not found',
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            $row = $query->row();
            $category = array(
                'category_id' => intval($row->id),
                'category_name' => $row->category_name,
                'is_active' => $row->is_active,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at
            );

            json_output(200, array(
                'status' => 1,
                'message' => 'Student category retrieved successfully',
                'data' => $category,
                'timestamp' => date('Y-m-d H:i:s')
            ));

        } catch (Exception $e) {
            json_output(500, array(
                'status' => 0,
                'message' => 'An error occurred while retrieving student category',
                'error' => array(
                    'type' => 'Exception',
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ),
                'timestamp' => date('Y-m-d H:i:s')
            ));
        }
    }

    public function not_found()
    {
        $response = array(
            'status' => 0,
            'message' => 'API endpoint not found',
            'error' => array(
                'type' => 'Not Found',
                'code' => 404,
                'uri' => $this->uri->uri_string(),
                'method' => $this->input->server('REQUEST_METHOD')
            ),
            'available_endpoints' => array(
                'POST /teacher/simple_menu' => 'Get menu items for staff',
                'POST /teacher/menu' => 'Get menu items (original)',
                'POST /teacher/profile' => 'Get comprehensive staff profile',
                'POST /teacher/students' => 'Get students by class/section/session',
                'POST /teacher/classes-with-sections' => 'Get classes with sections',
                'POST /teacher/sessions-with-classes-sections' => 'Get sessions with classes and sections',
                'POST /teacher/student-categories' => 'Get all student categories',
                'POST /teacher/student-category/get' => 'Get single student category',
                'POST /teacher/student-category/create' => 'Create student category',
                'POST /teacher/student-category/update' => 'Update student category',
                'POST /teacher/student-category/delete' => 'Delete student category',
                'GET /teacher/test' => 'Test endpoint',
                'GET /teacher/debug-menu' => 'Debug menu endpoint'
            ),
            'timestamp' => date('Y-m-d H:i:s')
        );

        json_output(404, $response);
    }
}
