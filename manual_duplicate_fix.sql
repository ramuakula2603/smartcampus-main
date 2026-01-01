-- MANUAL QUICK FIX for duplicate hostel menus
-- Run these commands one by one if the comprehensive script doesn't work

-- 1. Delete all hostel fee permission categories except the highest ID ones
DELETE FROM permission_category WHERE short_code = 'hostel_fees_master' AND id < 11046;
DELETE FROM permission_category WHERE short_code = 'assign_hostel_fees' AND id < 11047;

-- 2. Delete all hostel fee submenus except the highest ID ones  
DELETE FROM sidebar_sub_menus WHERE lang_key = 'hostel_fees_master' AND id < 502;
DELETE FROM sidebar_sub_menus WHERE lang_key = 'assign_hostel_fees' AND id < 503;

-- 3. Clean up orphaned roles_permissions
DELETE FROM roles_permissions WHERE perm_cat_id NOT IN (SELECT id FROM permission_category);

-- 4. Ensure Super Admin has the correct permissions
INSERT IGNORE INTO roles_permissions (role_id, perm_cat_id, can_view, can_add, can_edit, can_delete)
VALUES 
(1, 11046, 1, 1, 1, 1),
(1, 11047, 1, 1, 1, 1);

-- 5. Verify final state
SELECT 'Final check - should show only 2 entries:' as info;
SELECT id, short_code, name FROM permission_category WHERE short_code IN ('hostel_fees_master', 'assign_hostel_fees');

SELECT 'Final submenus - should show only 2 entries:' as info;
SELECT ssm.id, ssm.lang_key, ssm.menu, ssm.url 
FROM sidebar_sub_menus ssm
JOIN sidebar_menus sm ON sm.id = ssm.sidebar_menu_id
WHERE sm.lang_key = 'hostel' AND ssm.lang_key IN ('hostel_fees_master', 'assign_hostel_fees');
