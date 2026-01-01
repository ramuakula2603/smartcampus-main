# Generate Paper Implementation Guide

## Overview
This document describes the implementation of the "Generate Paper" feature for the student portal.

## Files Created/Modified

### 1. Controller
**File:** `application/controllers/user/Generatepaper.php`
- Created a new controller for handling Generate Paper functionality
- Includes `index()` method to display the form
- Includes `create()` method to handle form submission

### 2. Model
**File:** `application/models/Generatepaper_model.php`
- Created model to handle database operations for papers
- Methods:
  - `add($data)` - Insert or update paper records
  - `get($id)` - Retrieve paper records
  - `remove($id)` - Delete paper records

### 3. View
**File:** `application/views/user/generatepaper/index.php`
- Created view with a form to generate papers
- Form fields:
  - Title (required)
  - Total Questions (required, number)
  - Instructions (optional, textarea)

### 4. Student Header (Sidebar Menu)
**File:** `application/views/layout/student/header.php`
- Added "Generate Paper" menu item to the student sidebar
- Menu appears after "Hostel Rooms" menu item
- Uses icon: `fa fa-file-text-o`
- Links to: `user/generatepaper`

### 5. Language File
**File:** `application/language/English/app_files/system_lang.php`
- Added language key: `$lang['generate_paper'] = "Generate Paper";`
- Existing keys used:
  - `total_questions` - Already exists
  - `instructions` - Already exists
  - `generate` - Already exists
  - `title` - Already exists

### 6. Database Table
**File:** `generate_paper_table.sql`
- SQL script to create the `papers` table
- Table structure:
  - `id` - Primary key (auto increment)
  - `title` - Paper title (varchar 255)
  - `total_questions` - Number of questions (int)
  - `instructions` - Paper instructions (text)
  - `created_at` - Timestamp (auto)
  - `updated_at` - Timestamp (auto on update)

## Installation Steps

### Step 1: Import Database Table
Run the SQL script to create the `papers` table:

```bash
# Using MySQL command line
mysql -u your_username -p amt < generate_paper_table.sql

# Or using phpMyAdmin
# 1. Open phpMyAdmin
# 2. Select 'amt' database
# 3. Go to 'Import' tab
# 4. Choose 'generate_paper_table.sql' file
# 5. Click 'Go'
```

### Step 2: Verify Files
Ensure all the following files are in place:
- ✅ `application/controllers/user/Generatepaper.php`
- ✅ `application/models/Generatepaper_model.php`
- ✅ `application/views/user/generatepaper/index.php`
- ✅ `application/views/layout/student/header.php` (modified)
- ✅ `application/language/English/app_files/system_lang.php` (modified)

### Step 3: Test the Feature
1. Login as a student
2. Look for "Generate Paper" in the sidebar menu
3. Click on "Generate Paper"
4. Fill in the form:
   - Title: e.g., "Mathematics Final Exam"
   - Total Questions: e.g., 50
   - Instructions: e.g., "Answer all questions. Time allowed: 2 hours."
5. Click "Generate" button
6. Verify success message appears

## URL Structure
- **Generate Paper Page:** `http://your-domain/user/generatepaper`
- **Create Paper Action:** `http://your-domain/user/generatepaper/create`

## Menu Location
The "Generate Paper" menu item appears in the student sidebar:
- Position: After "Hostel Rooms" menu item
- Icon: File text icon (fa fa-file-text-o)
- Label: "Generate Paper"

## Database Entry
You mentioned you created a "generate paper" entry in the AMT database. Please note:
- The student sidebar is **hardcoded** in the header.php file
- It does NOT use the `sidebar_menus` or `sidebar_sub_menus` tables
- The admin sidebar uses those tables, but the student sidebar does not
- Therefore, any database entries you created for student menus won't be used

## Permissions
Currently, the feature is available to all students without additional permission checks. If you need to add permission controls:

1. Add a check in the controller:
```php
if (!$this->studentmodule_lib->hasActive('generate_paper')) {
    redirect('user/user/dashboard');
}
```

2. Wrap the menu item in the header.php:
```php
<?php if ($this->studentmodule_lib->hasActive('generate_paper')) {?>
    <li class="<?php echo set_Topmenu('Generatepaper'); ?>">
        <a href="<?php echo base_url(); ?>user/generatepaper">
            <i class="fa fa-file-text-o ftlayer"></i> 
            <span><?php echo $this->lang->line('generate_paper'); ?></span>
        </a>
    </li>
<?php }?>
```

## Troubleshooting

### Menu Not Showing
- Clear browser cache
- Check if the header.php file was modified correctly
- Verify you're logged in as a student (not parent or guest)

### Language Key Not Working
- Verify the language file was modified correctly
- Check the current language setting in the system
- If using a language other than English, add the translation to that language file

### Database Errors
- Ensure the `papers` table was created successfully
- Check database connection settings
- Verify table permissions

## Future Enhancements
Consider adding:
- List of created papers
- Edit/Delete functionality
- Paper preview
- Export to PDF
- Question bank integration
- Paper templates
- Sharing papers with teachers

## Support
If you encounter any issues, check:
1. PHP error logs
2. Browser console for JavaScript errors
3. Database query logs
4. CodeIgniter error logs in `application/logs/`

