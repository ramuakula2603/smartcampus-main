-- Add Missing Income and Expense Modules for Dashboard Charts
-- This script adds the required modules to enable dashboard chart functionality

USE amt;

-- ========================================
-- STEP 1: Add Income Module
-- ========================================

-- Check if income module exists, if not add it
INSERT IGNORE INTO `permission_student` (`name`, `short_code`, `system`, `student`, `parent`, `group_id`, `created_at`) 
VALUES ('Income', 'income', 0, 1, 1, 22, NOW());

-- ========================================
-- STEP 2: Add Expense Module  
-- ========================================

-- Check if expense module exists, if not add it
INSERT IGNORE INTO `permission_student` (`name`, `short_code`, `system`, `student`, `parent`, `group_id`, `created_at`) 
VALUES ('Expense', 'expense', 0, 1, 1, 22, NOW());

-- ========================================
-- STEP 3: Add Fees Collection Module (if not exists)
-- ========================================

-- Check if fees_collection module exists, if not add it
INSERT IGNORE INTO `permission_student` (`name`, `short_code`, `system`, `student`, `parent`, `group_id`, `created_at`) 
VALUES ('Fees Collection', 'fees_collection', 0, 1, 1, 2, NOW());

-- ========================================
-- STEP 4: Verification
-- ========================================

SELECT '=== MODULE SETUP VERIFICATION ===' as Info;

-- Show all income/expense related modules
SELECT 'Income and Expense Modules:' as description;
SELECT id, name, short_code, parent, student, group_id 
FROM permission_student 
WHERE short_code IN ('income', 'expense', 'fees_collection', 'fees')
ORDER BY short_code;

-- Check if modules are active (parent = 1 means active)
SELECT 'Module Status Summary:' as description;
SELECT 
    short_code,
    name,
    CASE 
        WHEN parent = 1 THEN '✅ Active'
        ELSE '❌ Inactive'
    END as status
FROM permission_student 
WHERE short_code IN ('income', 'expense', 'fees_collection', 'fees')
ORDER BY short_code;

SELECT '=== MODULES SETUP COMPLETED ===' as Info;
SELECT 'Dashboard charts should now have proper module access!' as Message;
