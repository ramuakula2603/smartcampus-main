-- Add permission_category entries for Result reports
-- Adds internal_result_report and external_result_report under Reports permission group (perm_group_id = 14)
-- Run this in your database (phpMyAdmin or mysql client). It is idempotent (uses INSERT ... SELECT WHERE NOT EXISTS)

SET @reports_group_id = (SELECT id FROM permission_group WHERE short_code = 'reports' LIMIT 1);

-- If reports group not found, fallback to 14 (legacy)
SET @reports_group_id = COALESCE(@reports_group_id, 14);

-- Insert internal_result_report if missing
INSERT INTO permission_category (perm_group_id, name, short_code, enable_view, enable_add, enable_edit, enable_delete, created_at)
SELECT @reports_group_id, 'Internal Result Report', 'internal_result_report', 1, 0, 0, 0, NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM permission_category WHERE short_code = 'internal_result_report');

-- Insert external_result_report if missing
INSERT INTO permission_category (perm_group_id, name, short_code, enable_view, enable_add, enable_edit, enable_delete, created_at)
SELECT @reports_group_id, 'External Result Report', 'external_result_report', 1, 0, 0, 0, NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM permission_category WHERE short_code = 'external_result_report');

-- Optional: show inserted rows
SELECT id, perm_group_id, name, short_code, enable_view FROM permission_category WHERE short_code IN ('internal_result_report', 'external_result_report');
