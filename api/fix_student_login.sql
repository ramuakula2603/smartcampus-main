-- Fix Student Login Settings
-- This script ensures that student panel login is enabled

-- First, check current values
SELECT id, student_panel_login, parent_panel_login, student_login, parent_login 
FROM sch_settings 
LIMIT 5;

-- Update student_panel_login to 'yes' to enable student API access
UPDATE sch_settings 
SET student_panel_login = 'yes' 
WHERE student_panel_login != 'yes' OR student_panel_login IS NULL;

-- Also ensure parent_panel_login is enabled if needed
UPDATE sch_settings 
SET parent_panel_login = 'yes' 
WHERE parent_panel_login != 'yes' OR parent_panel_login IS NULL;

-- Verify the changes
SELECT id, student_panel_login, parent_panel_login, student_login, parent_login 
FROM sch_settings 
LIMIT 5;
