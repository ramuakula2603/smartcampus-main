# Student API - File Upload Documentation

## Overview

The Student API file upload system provides a modular, reusable solution for handling file uploads via base64-encoded data in JSON API requests. This system is designed to work seamlessly with the Student API without requiring modifications to the existing controller.

**Version:** 1.0.0  
**Created:** October 4, 2024

---

## Files Created

### 1. Student_file_upload Library
**Path:** `api/application/libraries/Student_file_upload.php`  
**Purpose:** Core file upload library handling base64 decoding, validation, and file storage

**Key Features:**
- Base64 file decoding (supports data URI and raw base64)
- File type validation (images and documents)
- File size validation (configurable limits)
- Automatic directory creation
- Unique file name generation
- File deletion support
- Comprehensive error handling

### 2. Student File Upload Helper
**Path:** `api/application/helpers/student_file_upload_helper.php`  
**Purpose:** Convenient helper functions for easy integration

**Key Features:**
- Batch file processing
- Single file upload
- File validation utilities
- Base64 conversion utilities
- File size formatting

---

## Supported File Types

### Images
**Used for:** Student photos, parent photos, guardian photos

**Allowed Extensions:** jpg, jpeg, png, gif  
**Maximum Size:** 2 MB (configurable)  
**Upload Directories:**
- Student images: `uploads/student_images/`
- Father photo: `uploads/student_images/`
- Mother photo: `uploads/student_images/`
- Guardian photo: `uploads/student_images/`

### Documents
**Used for:** Application files, student documents

**Allowed Extensions:** pdf, doc, docx  
**Maximum Size:** 5 MB (configurable)  
**Upload Directories:**
- Application files: `uploads/application_files/`
- Student documents: `uploads/student_documents/{student_id}/`

---

## Base64 Format Support

The library supports two base64 formats:

### 1. Data URI Format (Recommended)
```
data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAUA...
```

### 2. Raw Base64 Format
```
iVBORw0KGgoAAAANSUhEUgAAAAUA...
```

**Note:** Data URI format is preferred as it includes MIME type information.

---

## Usage Examples

### Example 1: Upload Single File

```php
// Load the library
$this->load->library('student_file_upload');

// Upload student image
$result = $this->student_file_upload->upload_base64_file(
    $base64_data,
    'student_image',
    $student_id
);

if ($result['status']) {
    $file_path = $result['file_path'];
    // Save $file_path to database
    echo "File uploaded: " . $file_path;
} else {
    echo "Error: " . $result['message'];
}
```

### Example 2: Process All Student Files

```php
// Load the helper
$this->load->helper('student_file_upload');

// Process all files from API request
$result = process_student_files($input_data, $student_id);

if ($result['status']) {
    // All files uploaded successfully
    $file_paths = $result['file_paths'];
    
    // Update database with file paths
    if (isset($file_paths['student_image'])) {
        $data['image'] = $file_paths['student_image'];
    }
    if (isset($file_paths['father_pic'])) {
        $data['father_pic'] = $file_paths['father_pic'];
    }
    // ... etc
} else {
    // Some files failed
    $errors = $result['errors'];
    foreach ($errors as $field => $error) {
        echo "$field: $error\n";
    }
}
```

### Example 3: Integration with Student_webservice

```php
// In Student_webservice::create_student_record() method
// After student record is created

// Process file uploads if any files are provided
$this->load->helper('student_file_upload');
$file_result = process_student_files($input_data, $insert_id);

if ($file_result['success_count'] > 0) {
    // Update student record with file paths
    $update_data = array('id' => $insert_id);
    
    if (isset($file_result['file_paths']['student_image'])) {
        $update_data['image'] = $file_result['file_paths']['student_image'];
    }
    if (isset($file_result['file_paths']['father_pic'])) {
        $update_data['father_pic'] = $file_result['file_paths']['father_pic'];
    }
    if (isset($file_result['file_paths']['mother_pic'])) {
        $update_data['mother_pic'] = $file_result['file_paths']['mother_pic'];
    }
    if (isset($file_result['file_paths']['guardian_pic'])) {
        $update_data['guardian_pic'] = $file_result['file_paths']['guardian_pic'];
    }
    
    // Update student record
    $this->student_model->add($update_data);
}

// Handle document uploads
if (isset($file_result['file_paths']['first_doc'])) {
    $doc_data = array(
        'student_id' => $insert_id,
        'title' => 'First Document',
        'doc' => $file_result['file_paths']['first_doc']
    );
    $this->student_model->adddoc($doc_data);
}
```

---

## API Request Format

### Request with File Uploads

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
  "mother_pic": "data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD...",
  "application_file": "data:application/pdf;base64,JVBERi0xLjQKJeLjz9...",
  "first_doc": "data:application/pdf;base64,JVBERi0xLjQKJeLjz9..."
}
```

### Response with File Upload Results

```json
{
  "status": 1,
  "message": "Student created successfully",
  "data": {
    "student_id": 123,
    "admission_no": "ADM001",
    "files_uploaded": {
      "student_image": "uploads/student_images/student-image-1696425600-abc123.png",
      "father_pic": "uploads/student_images/father-pic-1696425601-def456.jpg",
      "mother_pic": "uploads/student_images/mother-pic-1696425602-ghi789.jpg"
    },
    "files_failed": []
  },
  "timestamp": "2024-10-04 10:30:45"
}
```

---

## File Naming Convention

Generated file names follow this pattern:
```
{file-type}-{timestamp}-{random-id}.{extension}
```

**Examples:**
- `student-image-1696425600-abc123def456.png`
- `father-pic-1696425601-def456ghi789.jpg`
- `application-file-1696425602-ghi789jkl012.pdf`

This ensures:
- Unique file names (no collisions)
- Easy identification of file type
- Chronological ordering
- Original extension preservation

---

## Configuration

### Modify Maximum File Sizes

```php
$this->load->library('student_file_upload');

// Set max document size to 10MB
$this->student_file_upload->set_max_file_size(10485760);

// Set max image size to 3MB
$this->student_file_upload->set_max_image_size(3145728);
```

### Add Custom File Types

Edit `Student_file_upload.php`:

```php
// Add to allowed extensions
private $allowed_image_extensions = array('jpg', 'jpeg', 'png', 'gif', 'webp');
private $allowed_document_extensions = array('pdf', 'doc', 'docx', 'txt');

// Add to MIME type mapping
private function get_extension_from_mime($mime_type)
{
    $mime_map = array(
        // ... existing mappings
        'image/webp' => 'webp',
        'text/plain' => 'txt',
    );
    // ...
}
```

---

## Error Handling

### Common Errors and Solutions

#### Error: "Invalid base64 data"
**Cause:** Malformed base64 string  
**Solution:** Ensure base64 data is properly encoded

#### Error: "Invalid image format"
**Cause:** File extension not allowed  
**Solution:** Use jpg, jpeg, png, or gif for images

#### Error: "Image size exceeds maximum"
**Cause:** File too large  
**Solution:** Compress image or increase max size limit

#### Error: "Failed to create directory"
**Cause:** Permission issues  
**Solution:** Ensure web server has write permissions to uploads directory

#### Error: "Directory is not writable"
**Cause:** Permission issues  
**Solution:** Set directory permissions to 755 or 777

---

## Testing

### Test Script for File Upload

```php
<?php
// test_file_upload.php

require_once('path/to/codeigniter/index.php');

$CI = &get_instance();
$CI->load->library('student_file_upload');

// Read test image
$image_path = 'path/to/test/image.jpg';
$image_data = file_get_contents($image_path);
$base64_data = 'data:image/jpeg;base64,' . base64_encode($image_data);

// Test upload
$result = $CI->student_file_upload->upload_base64_file(
    $base64_data,
    'student_image',
    123
);

print_r($result);
```

### cURL Test with File Upload

```bash
# Create base64 encoded image
BASE64_IMAGE=$(base64 -w 0 test_image.jpg)

# Send API request
curl -X POST http://localhost/amt/api/student_webservice/create \
  -H "Content-Type: application/json" \
  -d "{
    \"firstname\": \"Test\",
    \"lastname\": \"Student\",
    \"gender\": \"Male\",
    \"dob\": \"2010-01-15\",
    \"class_id\": 1,
    \"section_id\": 1,
    \"reference_id\": 1,
    \"guardian_name\": \"Guardian\",
    \"guardian_phone\": \"9876543210\",
    \"guardian_email\": \"test@example.com\",
    \"student_image\": \"data:image/jpeg;base64,$BASE64_IMAGE\"
  }"
```

---

## Helper Functions Reference

### process_student_files($input_data, $student_id)
Processes all file uploads from API request

**Returns:** Array with status, file_paths, and errors

### upload_student_file($base64_data, $file_type, $student_id)
Uploads a single file

**Returns:** Array with status, message, and file_path

### delete_student_file($file_path)
Deletes a file from server

**Returns:** Array with status and message

### is_valid_base64_file($base64_data)
Validates base64 file data

**Returns:** Boolean

### get_base64_file_size($base64_data)
Gets file size from base64 data

**Returns:** Integer (bytes) or false

### format_file_size($bytes)
Formats bytes to human readable format

**Returns:** String (e.g., "2.5 MB")

### get_allowed_student_file_extensions($type)
Gets allowed extensions for file type

**Returns:** Array of extensions

### file_to_base64($file_path)
Converts file to base64 data URI

**Returns:** String or false

---

## Security Considerations

1. **File Type Validation:** Only allowed extensions are accepted
2. **File Size Limits:** Prevents large file uploads
3. **MIME Type Checking:** Validates actual file content
4. **Unique File Names:** Prevents file overwrites
5. **Directory Isolation:** Student documents in separate folders
6. **Error Logging:** All errors are logged for monitoring

---

## Performance Considerations

1. **Base64 Overhead:** Base64 encoding increases file size by ~33%
2. **Memory Usage:** Large files decoded in memory
3. **Disk I/O:** File writes are synchronous
4. **Recommendation:** For production, consider:
   - Async file processing
   - Cloud storage integration (S3, etc.)
   - CDN for file delivery
   - Image optimization/compression

---

## Future Enhancements

- [ ] Image resizing/thumbnail generation
- [ ] Cloud storage support (AWS S3, Azure, etc.)
- [ ] Async file processing with queue
- [ ] Image optimization/compression
- [ ] Virus scanning integration
- [ ] File metadata extraction
- [ ] Watermarking support
- [ ] Multiple file format conversions

---

## Troubleshooting

### Files not uploading
1. Check directory permissions (755 or 777)
2. Verify base64 data is valid
3. Check file size limits
4. Review error logs

### Files uploaded but not visible
1. Check file path in database
2. Verify web server can serve from uploads directory
3. Check .htaccess rules

### Performance issues
1. Reduce max file sizes
2. Implement async processing
3. Use cloud storage
4. Enable caching

---

## Support

For issues or questions:
1. Check error logs: `application/logs/`
2. Review this documentation
3. Test with provided examples
4. Check file permissions

---

**Created by:** Augment Agent  
**Date:** October 4, 2024  
**Version:** 1.0.0

