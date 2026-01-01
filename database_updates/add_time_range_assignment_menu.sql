-- ============================================================================
-- Add Time Range Assignment Menu Item to Sidebar
-- ============================================================================
-- This script adds the Time Range Assignment submenu under System Settings
-- ============================================================================

-- Insert submenu item for Time Range Assignments under System Settings (menu_id = 27)
INSERT INTO `sidebar_sub_menus` (
    `sidebar_menu_id`,
    `permission_group_id`,
    `url`,
    `lang_key`,
    `menu`,
    `activate_controller`,
    `activate_methods`,
    `access_permissions`,
    `level`,
    `is_active`,
    `created_at`
) VALUES (
    27,  -- System Settings menu ID
    1,   -- Student Information permission group (same as other system settings)
    'admin/time_range_assignment',
    'time_range_assignments',
    'Time Range Assignments',
    'time_range_assignment',
    'index,getStaffList,getStaffAssignments,saveStaffAssignments,getStudentList,getStudentAssignments,saveStudentAssignments',
    'time_range_assignments,can_view',
    1,
    1,
    NOW()
) ON DUPLICATE KEY UPDATE 
    `menu` = 'Time Range Assignments',
    `url` = 'admin/time_range_assignment',
    `activate_controller` = 'time_range_assignment',
    `is_active` = 1;

-- Verify the menu was added
SELECT 'Menu item added successfully!' as status;

SELECT 
    ssm.id,
    ssm.sidebar_menu_id,
    ssm.menu,
    ssm.lang_key,
    ssm.url,
    ssm.activate_controller,
    ssm.is_active
FROM sidebar_sub_menus ssm
WHERE ssm.lang_key = 'time_range_assignments';

