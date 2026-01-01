-- SQL script to add Hostel Fee Master menu items and permissions
-- This script adds the missing hostel fee-related menu items to the sidebar navigation
-- and ensures proper permissions are in place

-- ========================================
-- STEP 1: Add Permission Categories for Hostel Fees
-- ========================================

-- First, get the hostel permission group ID
SET @hostel_perm_group_id = (SELECT id FROM permission_group WHERE short_code = 'hostel' LIMIT 1);

-- If hostel permission group doesn't exist, create it
INSERT IGNORE INTO `permission_group` (`name`, `short_code`, `is_active`, `system`)
VALUES ('Hostel', 'hostel', 1, 0);

-- Update the variable in case we just created it
SET @hostel_perm_group_id = (SELECT id FROM permission_group WHERE short_code = 'hostel' LIMIT 1);

-- Add permission categories for hostel fee management
INSERT IGNORE INTO `permission_category` (`perm_group_id`, `name`, `short_code`, `enable_view`, `enable_add`, `enable_edit`, `enable_delete`)
VALUES
(@hostel_perm_group_id, 'Hostel Fees Master', 'hostel_fees_master', 1, 1, 1, 1),
(@hostel_perm_group_id, 'Assign Hostel Fees', 'assign_hostel_fees', 1, 1, 1, 1);

-- ========================================
-- STEP 2: Create/Update Main Hostel Menu
-- ========================================

-- Insert or update the main Hostel menu (if it doesn't exist)
INSERT IGNORE INTO `sidebar_menus` (`permission_group_id`, `icon`, `menu`, `activate_menu`, `lang_key`, `system_level`, `level`, `sidebar_display`, `access_permissions`, `is_active`)
VALUES
(@hostel_perm_group_id, 'fa fa-building', 'Hostel', 'hostel', 'hostel', 0, 0, 1, 'hostel|can_view,hostel_rooms|can_view,room_type|can_view,hostel_fees_master|can_view,assign_hostel_fees|can_view', 1);

-- ========================================
-- STEP 3: Create Hostel Submenu Items
-- ========================================

-- Get the Hostel menu ID (assuming it's the one we just inserted or already exists)
SET @hostel_menu_id = (SELECT id FROM sidebar_menus WHERE lang_key = 'hostel' LIMIT 1);

-- Insert Hostel submenu items (using INSERT IGNORE to avoid duplicates)
INSERT IGNORE INTO `sidebar_sub_menus` (`sidebar_menu_id`, `menu`, `key`, `lang_key`, `url`, `level`, `access_permissions`, `permission_group_id`, `activate_controller`, `activate_methods`, `addon_permission`, `is_active`)
VALUES
-- Hostel Rooms
(@hostel_menu_id, 'Hostel Rooms', 'hostel_rooms', 'hostel_rooms', 'admin/hostelroom', 1, 'hostel_rooms|can_view', @hostel_perm_group_id, 'hostelroom', 'index,edit', NULL, 1),

-- Room Type
(@hostel_menu_id, 'Room Type', 'room_type', 'room_type', 'admin/roomtype', 1, 'room_type|can_view', @hostel_perm_group_id, 'roomtype', 'index,edit', NULL, 1),

-- Hostel
(@hostel_menu_id, 'Hostel', 'hostel', 'hostel', 'admin/hostel', 1, 'hostel|can_view', @hostel_perm_group_id, 'hostel', 'index,edit', NULL, 1),

-- Hostel Fees Master (NEW)
(@hostel_menu_id, 'Hostel Fees Master', 'hostel_fees_master', 'hostel_fees_master', 'admin/hostel/feemaster', 1, 'hostel_fees_master|can_view', @hostel_perm_group_id, 'hostel', 'feemaster', NULL, 1),

-- Assign Hostel Fees (NEW)
(@hostel_menu_id, 'Assign Hostel Fees', 'assign_hostel_fees', 'assign_hostel_fees', 'admin/hostel/assignhostelfee', 1, 'assign_hostel_fees|can_view', @hostel_perm_group_id, 'hostel', 'assignhostelfee,assignhostelfeestudent,assignhostelfeepost', NULL, 1);

-- ========================================
-- STEP 4: Grant Permissions to Super Admin Role
-- ========================================

-- Get Super Admin role ID (usually role ID 1, but let's be safe)
SET @super_admin_role_id = (SELECT id FROM roles WHERE name = 'Super Admin' OR name = 'SuperAdmin' OR id = 1 LIMIT 1);

-- Get permission category IDs for hostel fees
SET @hostel_fees_master_perm_id = (SELECT id FROM permission_category WHERE short_code = 'hostel_fees_master' LIMIT 1);
SET @assign_hostel_fees_perm_id = (SELECT id FROM permission_category WHERE short_code = 'assign_hostel_fees' LIMIT 1);

-- Grant all permissions to Super Admin for hostel fee management
INSERT IGNORE INTO `roles_permissions` (`role_id`, `perm_cat_id`, `can_view`, `can_add`, `can_edit`, `can_delete`)
VALUES
(@super_admin_role_id, @hostel_fees_master_perm_id, 1, 1, 1, 1),
(@super_admin_role_id, @assign_hostel_fees_perm_id, 1, 1, 1, 1);

-- ========================================
-- STEP 5: Clean up duplicates and ensure consistency
-- ========================================

-- Remove any duplicate hostel submenu entries that might exist
DELETE s1 FROM sidebar_sub_menus s1
INNER JOIN sidebar_sub_menus s2
WHERE s1.id > s2.id
AND s1.sidebar_menu_id = s2.sidebar_menu_id
AND s1.lang_key = s2.lang_key
AND s1.sidebar_menu_id = @hostel_menu_id;

-- ========================================
-- STEP 6: Ensure Transport menu consistency (for reference)
-- ========================================

-- Get transport permission group ID
SET @transport_perm_group_id = (SELECT id FROM permission_group WHERE short_code = 'transport' LIMIT 1);
SET @transport_menu_id = (SELECT id FROM sidebar_menus WHERE lang_key = 'transport' LIMIT 1);

-- Add Transport Fees Master permission if it doesn't exist
INSERT IGNORE INTO `permission_category` (`perm_group_id`, `name`, `short_code`, `enable_view`, `enable_add`, `enable_edit`, `enable_delete`)
VALUES
(@transport_perm_group_id, 'Transport Fees Master', 'transport_fees_master', 1, 1, 1, 1);

-- Add Transport Fees Master submenu if it doesn't exist
INSERT IGNORE INTO `sidebar_sub_menus` (`sidebar_menu_id`, `menu`, `key`, `lang_key`, `url`, `level`, `access_permissions`, `permission_group_id`, `activate_controller`, `activate_methods`, `addon_permission`, `is_active`)
VALUES
(@transport_menu_id, 'Transport Fees Master', 'transport_fees_master', 'transport_fees_master', 'admin/transport/feemaster', 1, 'transport_fees_master|can_view', @transport_perm_group_id, 'transport', 'feemaster', NULL, 1);

-- ========================================
-- STEP 7: Verification Queries
-- ========================================

-- Display the results for verification
SELECT '=== HOSTEL MENU STRUCTURE ===' as Info;
SELECT
    sm.id as menu_id,
    sm.menu as main_menu,
    sm.lang_key as main_menu_key,
    sm.permission_group_id,
    ssm.id as submenu_id,
    ssm.menu as submenu,
    ssm.lang_key as submenu_key,
    ssm.url as submenu_url,
    ssm.activate_controller,
    ssm.activate_methods,
    ssm.access_permissions
FROM sidebar_menus sm
LEFT JOIN sidebar_sub_menus ssm ON sm.id = ssm.sidebar_menu_id
WHERE sm.lang_key = 'hostel'
ORDER BY sm.id, ssm.id;

-- Display hostel permissions
SELECT '=== HOSTEL PERMISSIONS ===' as Info;
SELECT
    pg.name as permission_group,
    pg.short_code as group_code,
    pc.name as permission_name,
    pc.short_code as permission_code,
    pc.enable_view,
    pc.enable_add,
    pc.enable_edit,
    pc.enable_delete
FROM permission_group pg
JOIN permission_category pc ON pg.id = pc.perm_group_id
WHERE pg.short_code = 'hostel'
ORDER BY pc.id;

-- Display role permissions for hostel
SELECT '=== HOSTEL ROLE PERMISSIONS ===' as Info;
SELECT
    r.name as role_name,
    pc.name as permission_name,
    pc.short_code as permission_code,
    rp.can_view,
    rp.can_add,
    rp.can_edit,
    rp.can_delete
FROM roles r
JOIN roles_permissions rp ON r.id = rp.role_id
JOIN permission_category pc ON pc.id = rp.perm_cat_id
JOIN permission_group pg ON pg.id = pc.perm_group_id
WHERE pg.short_code = 'hostel'
ORDER BY r.id, pc.id;

-- Display Transport menu for comparison
SELECT '=== TRANSPORT MENU STRUCTURE (for reference) ===' as Info;
SELECT
    sm.id as menu_id,
    sm.menu as main_menu,
    sm.lang_key as main_menu_key,
    ssm.id as submenu_id,
    ssm.menu as submenu,
    ssm.lang_key as submenu_key,
    ssm.url as submenu_url,
    ssm.activate_controller,
    ssm.activate_methods
FROM sidebar_menus sm
LEFT JOIN sidebar_sub_menus ssm ON sm.id = ssm.sidebar_menu_id
WHERE sm.lang_key = 'transport'
ORDER BY sm.id, ssm.id;

-- Final success message
SELECT '=== SETUP COMPLETE ===' as Info;
SELECT 'Hostel Fee Master menu items have been successfully added to the navigation system!' as Message;
