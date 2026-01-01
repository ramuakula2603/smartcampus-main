-- ============================================
-- VERIFICATION SCRIPT
-- Check if Result submenu was added successfully
-- ============================================

-- 1. Check if the Result submenu exists
SELECT '=== CHECKING RESULT SUBMENU ===' as info;
SELECT 
    ssm.id,
    ssm.sidebar_menu_id,
    sm.menu as parent_menu,
    ssm.menu as submenu_name,
    ssm.lang_key,
    ssm.url,
    ssm.level,
    ssm.is_active,
    ssm.created_at
FROM sidebar_sub_menus ssm
JOIN sidebar_menus sm ON sm.id = ssm.sidebar_menu_id
WHERE ssm.sidebar_menu_id = 26 
  AND ssm.lang_key = 'result';

-- 2. Compare with Finance submenu structure
SELECT '=== COMPARING WITH FINANCE SUBMENU ===' as info;
SELECT 
    ssm.menu as submenu_name,
    ssm.lang_key,
    ssm.url,
    ssm.level,
    ssm.is_active
FROM sidebar_sub_menus ssm
WHERE ssm.sidebar_menu_id = 26 
  AND ssm.lang_key IN ('finance', 'result')
ORDER BY ssm.lang_key;

-- 3. List all Report submenus to verify position
SELECT '=== ALL REPORT SUBMENUS (ordered by level) ===' as info;
SELECT 
    ssm.id,
    ssm.menu,
    ssm.lang_key,
    ssm.url,
    ssm.level,
    ssm.is_active
FROM sidebar_sub_menus ssm
WHERE ssm.sidebar_menu_id = 26
ORDER BY ssm.level;

-- 4. Count total submenus under Reports
SELECT '=== SUBMENU COUNT ===' as info;
SELECT COUNT(*) as total_report_submenus
FROM sidebar_sub_menus
WHERE sidebar_menu_id = 26;

-- 5. Verify the Result submenu is active
SELECT '=== RESULT SUBMENU STATUS ===' as info;
SELECT 
    CASE 
        WHEN is_active = 1 THEN 'ACTIVE ✓'
        ELSE 'INACTIVE ✗'
    END as status,
    menu,
    url
FROM sidebar_sub_menus
WHERE sidebar_menu_id = 26 
  AND lang_key = 'result';

-- ============================================
-- EXPECTED RESULTS:
-- ============================================
-- 1. Result submenu should exist with:
--    - sidebar_menu_id = 26 (Reports)
--    - menu = 'result'
--    - url = 'report/result'
--    - level = 16
--    - is_active = 1
--
-- 2. It should appear in the list alongside Finance
--
-- 3. Total Report submenus should be 15 or more
--
-- 4. Status should show ACTIVE ✓
-- ============================================
