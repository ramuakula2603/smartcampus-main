# File Upload Integration Guide

## Overview

This guide shows how to integrate the file upload functionality with the `Student_admission_api.php` controller. The file upload system is designed to be modular and can be added as an optional enhancement.

---

## Quick Integration (3 Steps)

### Step 1: Load the Helper

Add this line at the beginning of the `create_student_record()` method:

```php
$this->load->helper('student_file_upload');
```

### Step 2: Process Files After Student Creation

Add this code after the student record is successfully created (after `$insert_id` is available):

```php
// Process file uploads if any files are provided
$file_result = process_student_files($input_data, $insert_id);

if ($file_result['success_count'] > 0) {
    // Files were uploaded, update student record
    $update_data = array('id' => $insert_id);
    
    // Add file paths to update data
    foreach ($file_result['file_paths'] as $field => $path) {
        $update_data[$field] = $path;
    }
    
    // Update student record with file paths
    $this->student_model->add($update_data);
}
```

### Step 3: Include File Info in Response

Add file upload results to the success response:

```php
$response_data['files_uploaded'] = $file_result['file_paths'];
$response_data['files_failed'] = $file_result['errors'];
```

---

## Complete Integration Example

Here's the complete code snippet to add to `Student_admission_api.php`:

### Location: In `create_student_record()` method, after student creation

```php
// ============================================================================
// FILE UPLOAD INTEGRATION (Optional Enhancement)
// ============================================================================
// This section handles file uploads for student images, parent photos, and documents.
// It uses the Student_file_upload library and helper functions.
// Files are uploaded from base64-encoded data in the JSON request.
// ============================================================================

// Load file upload helper
$this->load->helper('student_file_upload');

// Process all file uploads from the request
$file_result = process_student_files($input_data, $insert_id);

// If any files were successfully uploaded, update the student record
if ($file_result['success_count'] > 0) {
    $update_data = array('id' => $insert_id);
    
    // Map uploaded files to database fields
    $file_field_mapping = array(
        'student_image'    => 'image',
        'father_pic'       => 'father_pic',
        'mother_pic'       => 'mother_pic',
        'guardian_pic'     => 'guardian_pic',
    );
    
    // Add file paths to update data
    foreach ($file_field_mapping as $input_field => $db_field) {
        if (isset($file_result['file_paths'][$input_field])) {
            $update_data[$db_field] = $file_result['file_paths'][$input_field];
        }
    }
    
    // Update student record with file paths
    if (count($update_data) > 1) { // More than just 'id'
        $this->student_model->add($update_data);
    }
}

// Handle document uploads separately (stored in student_doc table)
$document_fields = array('first_doc', 'second_doc', 'fourth_doc', 'fifth_doc');
foreach ($document_fields as $doc_field) {
    if (isset($file_result['file_paths'][$doc_field])) {
        $doc_data = array(
            'student_id' => $insert_id,
            'title'      => ucfirst(str_replace('_', ' ', $doc_field)),
            'doc'        => $file_result['file_paths'][$doc_field]
        );
        $this->student_model->adddoc($doc_data);
    }
}

// Handle application file upload (stored in student_application table)
if (isset($file_result['file_paths']['application_file'])) {
    $app_data = array(
        'student_id'       => $insert_id,
        'application_file' => $file_result['file_paths']['application_file']
    );
    $this->student_model->uploadapplicationfiledata($app_data);
}

// Log any file upload errors (optional)
if (count($file_result['errors']) > 0) {
    log_message('info', 'Student ' . $insert_id . ' file upload errors: ' . json_encode($file_result['errors']));
}

// ============================================================================
// END FILE UPLOAD INTEGRATION
// ============================================================================
```

### Location: In the success response section

```php
// Add file upload information to response
if (isset($file_result)) {
    $response_data['files_uploaded'] = $file_result['file_paths'];
    $response_data['files_upload_count'] = $file_result['success_count'];
    
    if (count($file_result['errors']) > 0) {
        $response_data['files_failed'] = $file_result['errors'];
    }
}
```

---

## Alternative: Minimal Integration

If you want the absolute minimal integration, just add this single line after student creation:

```php
// Process and save file uploads
$this->load->helper('student_file_upload');
$file_result = process_student_files($input_data, $insert_id);
if ($file_result['success_count'] > 0) {
    $this->student_model->add(array_merge(array('id' => $insert_id), $file_result['file_paths']));
}
```

---

## Testing the Integration

### Test Request with Files

```json
{
  "firstname": "John",
  "lastname": "Doe",
  "gender": "Male",
  "dob": "2010-01-15",
  "class_id": 1,
  "section_id": 1,
  "reference_id": 1,
  "guardian_name": "Jane Doe",
  "guardian_phone": "9876543210",
  "guardian_email": "jane@example.com",
  "student_image": "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAUA...",
  "father_pic": "data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD...",
  "mother_pic": "data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD..."
}
```

### Expected Response

```json
{
  "status": 1,
  "message": "Student created successfully",
  "data": {
    "student_id": 123,
    "admission_no": "ADM001",
    "student_username": "std123",
    "student_password": "abc123",
    "files_uploaded": {
      "student_image": "uploads/student_images/student-image-1696425600-abc123.png",
      "father_pic": "uploads/student_images/father-pic-1696425601-def456.jpg",
      "mother_pic": "uploads/student_images/mother-pic-1696425602-ghi789.jpg"
    },
    "files_upload_count": 3,
    "files_failed": {}
  },
  "timestamp": "2024-10-04 10:30:45"
}
```

---

## Creating Test Base64 Images

### Using PHP

```php
<?php
// Create a simple test image
$image = imagecreate(100, 100);
$bg = imagecolorallocate($image, 255, 255, 255);
$text_color = imagecolorallocate($image, 0, 0, 0);
imagestring($image, 5, 10, 40, 'Test', $text_color);

// Save to buffer
ob_start();
imagepng($image);
$image_data = ob_get_clean();
imagedestroy($image);

// Convert to base64
$base64 = 'data:image/png;base64,' . base64_encode($image_data);
echo $base64;
```

### Using Command Line (Linux/Mac)

```bash
# Convert existing image to base64
base64 -w 0 student_photo.jpg > student_photo_base64.txt

# Create data URI
echo "data:image/jpeg;base64,$(base64 -w 0 student_photo.jpg)"
```

### Using Online Tools

1. Visit: https://www.base64-image.de/
2. Upload your image
3. Copy the generated base64 string
4. Use in API request

---

## Validation Before Upload

You can add validation to check if files are provided before processing:

```php
// Check if any file fields are present in the request
$file_fields = array('student_image', 'father_pic', 'mother_pic', 'guardian_pic', 
                     'application_file', 'first_doc', 'second_doc', 'fourth_doc', 'fifth_doc');

$has_files = false;
foreach ($file_fields as $field) {
    if (isset($input_data[$field]) && !empty($input_data[$field])) {
        $has_files = true;
        break;
    }
}

// Only process files if any are present
if ($has_files) {
    $this->load->helper('student_file_upload');
    $file_result = process_student_files($input_data, $insert_id);
    // ... rest of file processing code
}
```

---

## Error Handling

### Handle File Upload Errors Gracefully

```php
$file_result = process_student_files($input_data, $insert_id);

if ($file_result['success_count'] > 0) {
    // Some or all files uploaded successfully
    // Update student record
    // ...
}

if (count($file_result['errors']) > 0) {
    // Some files failed to upload
    // Log errors but don't fail the entire request
    log_message('warning', 'Student ' . $insert_id . ' file upload errors: ' . json_encode($file_result['errors']));
    
    // Optionally add to response
    $response_data['file_upload_warnings'] = $file_result['errors'];
}
```

---

## Database Schema Updates

If you want to store file metadata, you may need to add columns to the `students` table:

```sql
ALTER TABLE `students` 
ADD COLUMN `image` VARCHAR(255) NULL AFTER `is_active`,
ADD COLUMN `father_pic` VARCHAR(255) NULL AFTER `image`,
ADD COLUMN `mother_pic` VARCHAR(255) NULL AFTER `father_pic`,
ADD COLUMN `guardian_pic` VARCHAR(255) NULL AFTER `mother_pic`;
```

**Note:** Check if these columns already exist before running the ALTER statements.

---

## Performance Optimization

### For Large Files

```php
// Increase PHP memory limit if needed
ini_set('memory_limit', '256M');

// Increase max execution time
ini_set('max_execution_time', 300);

// Process files asynchronously (advanced)
// Use a queue system like Redis or RabbitMQ
```

### For Multiple Files

```php
// Process files in parallel (if PHP 7.4+)
// Use parallel processing libraries
// Or implement async processing with queues
```

---

## Rollback on Error

If you want to delete uploaded files if the transaction fails:

```php
// Store uploaded file paths
$uploaded_files = array();

// After file upload
if ($file_result['success_count'] > 0) {
    $uploaded_files = $file_result['file_paths'];
}

// If transaction fails
if ($this->db->trans_status() === FALSE) {
    // Delete uploaded files
    $this->load->helper('student_file_upload');
    foreach ($uploaded_files as $file_path) {
        delete_student_file($file_path);
    }
    
    $this->db->trans_rollback();
    return array(
        'status' => 0,
        'message' => 'Transaction failed, files cleaned up',
        'timestamp' => date('Y-m-d H:i:s')
    );
}
```

---

## Complete Integration Checklist

- [ ] Load `student_file_upload` helper in controller
- [ ] Add file processing code after student creation
- [ ] Update student record with file paths
- [ ] Handle document uploads to `student_doc` table
- [ ] Handle application file to `student_application` table
- [ ] Add file info to API response
- [ ] Test with sample base64 images
- [ ] Verify files are saved to correct directories
- [ ] Check database records have correct file paths
- [ ] Test error handling (invalid files, large files)
- [ ] Verify file permissions on upload directories
- [ ] Test rollback scenario
- [ ] Add logging for file upload errors
- [ ] Update API documentation with file upload examples

---

## Summary

The file upload system is designed to be:
- **Modular:** Can be added without modifying existing code
- **Optional:** Works whether files are provided or not
- **Robust:** Handles errors gracefully
- **Flexible:** Easy to customize and extend
- **Documented:** Comprehensive documentation provided

You can integrate it in as little as 3 lines of code, or use the complete integration for full functionality.

---

**Next Steps:**
1. Review the integration code above
2. Decide on minimal or complete integration
3. Add the code to `Student_admission_api.php`
4. Test with sample requests
5. Verify files are uploaded correctly
6. Update API documentation

---

**Created by:** Augment Agent  
**Date:** October 4, 2024  
**Version:** 1.0.0

