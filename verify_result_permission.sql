-- Verification Script: Check Result Permission Implementation
-- Run this after executing add_result_permissions.sql

SELECT '=== STEP 1: Check Permission Group (Reports) ===' AS info;
SELECT id, name, short_code, is_active 
FROM permission_group 
WHERE short_code = 'reports';

SELECT '=== STEP 2: Check Permission Categories Exist ===' AS info;
SELECT id, perm_group_id, name, short_code, enable_view, enable_add, enable_edit, enable_delete, created_at
FROM permission_category 
WHERE short_code IN ('internal_result_report', 'external_result_report');

SELECT '=== STEP 3: Check Result Submenu Configuration ===' AS info;
SELECT id, sidebar_menu_id, menu, lang_key, url, level, access_permissions, is_active
FROM sidebar_sub_menus 
WHERE lang_key = 'result' AND sidebar_menu_id = 26;

SELECT '=== STEP 4: Check Existing Role Permissions for Role 3 ===' AS info;
SELECT rp.id, rp.role_id, pc.name AS permission_name, pc.short_code, rp.can_view, rp.can_add, rp.can_edit, rp.can_delete
FROM roles_permissions rp
JOIN permission_category pc ON pc.id = rp.perm_cat_id
WHERE rp.role_id = 3 
  AND pc.perm_group_id = 14  -- Reports group
ORDER BY pc.name;

SELECT '=== STEP 5: Check Role 3 Details ===' AS info;
SELECT id, name 
FROM roles 
WHERE id = 3;

SELECT '=== STEP 6: Count Total Permission Categories in Reports Group ===' AS info;
SELECT COUNT(*) AS total_report_permissions
FROM permission_category 
WHERE perm_group_id = 14;

SELECT '=== STEP 7: List ALL Report Permission Categories ===' AS info;
SELECT id, name, short_code, enable_view, enable_add, enable_edit, enable_delete
FROM permission_category 
WHERE perm_group_id = 14
ORDER BY name;

-- Expected Results:
-- STEP 1: Should show Reports group (id=14, short_code='reports')
-- STEP 2: Should show 2 rows (internal_result_report, external_result_report)
-- STEP 3: Should show Result submenu with access_permissions containing these permissions
-- STEP 4: Will show existing permissions for role 3 in Reports group (may be empty if not yet assigned)
-- STEP 5: Should show role name (e.g., "Teacher", "Staff", etc.)
-- STEP 6: Should show count (likely 15-20+ permission categories)
-- STEP 7: Should list all report permissions including the new ones
