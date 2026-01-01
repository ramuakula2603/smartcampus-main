-- ============================================
-- Add NEW Result Submenu under Reports Menu
-- This adds a COMPLETELY NEW submenu - does NOT modify any existing submenus
-- ============================================

-- Step 1: Check if Result submenu already exists (to avoid duplicates)
SELECT 'Checking for existing Result submenu...' as info;
SELECT COUNT(*) as existing_count 
FROM sidebar_sub_menus 
WHERE sidebar_menu_id = 26 AND lang_key = 'result';

-- Step 2: Show current Reports submenus before insertion
SELECT '=== BEFORE: Current Reports Submenus ===' as info;
SELECT id, menu, lang_key, url, level 
FROM sidebar_sub_menus 
WHERE sidebar_menu_id = 26 
ORDER BY level;

-- Step 3: Add NEW Result submenu under Reports (sidebar_menu_id = 26)
-- This will ONLY INSERT if the record does NOT exist
-- Existing submenus: 
--   131 = Student Information (level 1)
--   132 = Finance (level 2)
--   133 = Attendance (level 3)
--   134 = Examinations (level 4)  ← This stays unchanged
--   143 = Online Examinations (level 5)
--   135 = Lesson Plan (level 6)
--   136 = Human Resource (level 7)
--   144 = Homework (level 8)
--   137 = Library (level 9)
--   138 = Inventory (level 10)
--   145 = Transport (level 11)
--   139 = Hostel (level 12)
--   140 = Alumni (level 13)
--   141 = User Log (level 14)
--   142 = Audit Trail Report (level 15)
--   NEW = Result (level 16)  ← Adding this NEW submenu

INSERT INTO `sidebar_sub_menus` 
(`sidebar_menu_id`, `menu`, `key`, `lang_key`, `url`, `level`, `access_permissions`, `permission_group_id`, `activate_controller`, `activate_methods`, `addon_permission`, `is_active`, `created_at`) 
SELECT 26, 'result', NULL, 'result', 'report/result', 16, 
'(\'internal_result_report\', \'can_view\') || (\'external_result_report\', \'can_view\')', 
NULL, 'report', 'result,internal_result,external_result', '', 1, NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM sidebar_sub_menus 
    WHERE sidebar_menu_id = 26 AND lang_key = 'result'
);

-- Step 4: Verify the NEW Result submenu was added
SELECT '=== NEW Result Submenu Details ===' as info;
SELECT ssm.id, ssm.sidebar_menu_id, sm.menu as parent_menu, ssm.menu as submenu, 
       ssm.lang_key, ssm.url, ssm.level, ssm.is_active
FROM sidebar_sub_menus ssm
JOIN sidebar_menus sm ON sm.id = ssm.sidebar_menu_id
WHERE ssm.sidebar_menu_id = 26 AND ssm.lang_key = 'result';

-- Step 5: Display ALL Reports submenus after insertion (to confirm nothing was modified)
SELECT '=== AFTER: All Reports Submenus (Ordered by Level) ===' as info;
SELECT ssm.id, ssm.menu, ssm.lang_key, ssm.url, ssm.level, ssm.is_active
FROM sidebar_sub_menus ssm
WHERE ssm.sidebar_menu_id = 26
ORDER BY ssm.level;

-- Step 6: Count total submenus (should increase by 1)
SELECT 'Total Reports Submenus:' as info, COUNT(*) as total_count
FROM sidebar_sub_menus
WHERE sidebar_menu_id = 26;

-- ============================================
-- IMPORTANT NOTES:
-- ============================================
-- ✓ This script ONLY ADDS a NEW "Result" submenu
-- ✓ NO EXISTING submenus are modified or deleted
-- ✓ The "Examinations" submenu (id=134) remains unchanged
-- ✓ The new "Result" submenu is completely separate
-- ✓ Both can coexist - they serve different purposes
--
-- INSTRUCTIONS:
-- 1. Run this SQL file in your database (phpMyAdmin or MySQL client)
-- 2. Verify in the output that Result submenu was added with level=16
-- 3. Refresh your application/browser (Ctrl+Shift+R)
-- 4. Check Reports menu - you should see the new "Result" submenu
-- 5. The submenu links to: report/result
-- ============================================
