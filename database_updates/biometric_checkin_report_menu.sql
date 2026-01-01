-- ============================================================================
-- Biometric Check-in Report - Menu and Permission Setup
-- ============================================================================
-- This script adds the Biometric Check-in Report to the system menu
-- and creates the necessary permissions
-- ============================================================================

-- Step 1: Add permission for Biometric Check-in Report
-- ============================================================================

-- Check if permission already exists
SET @permission_exists = (SELECT COUNT(*) FROM `permission_category` WHERE `perm_group_id` = 3 AND `name` = 'biometric_checkin_report');

-- Insert permission if it doesn't exist
INSERT INTO `permission_category` (`perm_group_id`, `name`, `short_code`, `enable_view`, `enable_add`, `enable_edit`, `enable_delete`, `created_at`)
SELECT 3, 'biometric_checkin_report', 'biometric_checkin_report', 1, 0, 0, 0, NOW()
WHERE @permission_exists = 0;

-- Get the permission category ID
SET @permission_id = (SELECT `id` FROM `permission_category` WHERE `short_code` = 'biometric_checkin_report' LIMIT 1);

-- Step 2: Grant permission to Super Admin role (role_id = 7)
-- ============================================================================

-- Check if permission is already granted
SET @role_permission_exists = (SELECT COUNT(*) FROM `roles_permissions` WHERE `role_id` = 7 AND `perm_cat_id` = @permission_id);

-- Grant permission if not already granted
INSERT INTO `roles_permissions` (`role_id`, `perm_cat_id`, `can_view`, `can_add`, `can_edit`, `can_delete`, `created_at`)
SELECT 7, @permission_id, 1, 0, 0, 0, NOW()
WHERE @role_permission_exists = 0;

-- Step 3: Add menu item to sidebar
-- ============================================================================
-- Note: This is typically done in the view files, but we document it here

-- The menu item should be added to: application/views/attendencereports/_attendance.php
-- Add this code block:

/*
<?php
if ($this->customlib->is_biometricAttendence()) {
    if ($this->rbac->hasPrivilege('biometric_checkin_report', 'can_view')) {
        ?>
        <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('Reports/biometric_checkin_report'); ?>">
            <a href="<?php echo site_url('biometric_checkin_report'); ?>">
                <i class="fa fa-clock-o"></i> <?php echo $this->lang->line('biometric_checkin_report'); ?>
            </a>
        </li>
        <?php
    }
}
?>
*/

-- ============================================================================
-- Verification Queries
-- ============================================================================

-- Verify permission was created
SELECT 'Permission Created:' as Status, 
       id, name, short_code 
FROM permission_category 
WHERE short_code = 'biometric_checkin_report';

-- Verify role permission was granted
SELECT 'Role Permission Granted:' as Status,
       rp.role_id, r.name as role_name, pc.name as permission_name, rp.can_view
FROM roles_permissions rp
JOIN roles r ON rp.role_id = r.id
JOIN permission_category pc ON rp.perm_cat_id = pc.id
WHERE pc.short_code = 'biometric_checkin_report';

-- ============================================================================
-- Language Strings to Add
-- ============================================================================

-- Add these to your language files (application/language/english/english_lang.php):
/*
$lang['biometric_checkin_report'] = 'Biometric Check-in Report';
$lang['staff_checkin_report'] = 'Staff Check-in Report';
$lang['student_checkin_report'] = 'Student Check-in Report';
$lang['checked_in'] = 'Checked In';
$lang['not_checked_in'] = 'Not Checked In';
$lang['checkin_count'] = 'Check-in Count';
$lang['first_checkin'] = 'First Check-in';
$lang['last_checkin'] = 'Last Check-in';
$lang['checkin_details'] = 'Check-in Details';
$lang['attendance_rate'] = 'Attendance Rate';
*/

-- ============================================================================
-- Complete!
-- ============================================================================

SELECT 'Biometric Check-in Report menu and permissions have been set up successfully!' as Message;

