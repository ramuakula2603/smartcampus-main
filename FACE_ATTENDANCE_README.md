# Face Attendance Registration System - Implementation Guide

## Overview
This system implements face recognition-based student attendance registration using Face-API.js and CodeIgniter framework.

## Files Created/Modified

### 1. Database Tables (face_attendance_tables.sql)
- **face_attendance_students** - Stores registered students with face images
- **face_attendance_records** - Stores attendance records
- **face_attendance_logs** - Logs recognition sessions for audit

### 2. Model
- **application/models/Face_attendance_student_model.php**
  - Handles all database operations for face attendance
  - Methods: add_student, check_registration_exists, get_students, mark_attendance, etc.

### 3. Controller
- **application/controllers/admin/Face_attendance_register.php**
  - Main controller with methods:
    - `index()` - Main registration page
    - `register_student()` - Register new student with face images
    - `check_registration()` - Check if registration number exists
    - `get_students()` - Get all registered students
    - `delete_student()` - Delete student and face data

### 4. View
- **application/views/admin/face_attendance_register/index.php**
  - Complete registration interface
  - Camera capture functionality
  - Auto-capture 5 face images (minimum 3 required)
  - Real-time preview of captured images
  - Student list with thumbnails

### 5. Assets
- **assets/face_attendance_models/** - Face-API.js model files (18 files)
  - Copied from attend/models/
  - Used for face detection and recognition

### 6. Upload Directories
- **uploads/face_attendance_images/{registration_number}/** - Student face images
- **uploads/face_attendance_captures/{date}/** - Attendance capture images

## Installation Steps

### Step 1: Import Database Tables

Run the SQL file to create tables:
```sql
SOURCE c:/xampp/htdocs/amt/face_attendance_tables.sql;
```

Or manually execute in phpMyAdmin:
1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Select database: `amt`
3. Go to SQL tab
4. Paste contents of `face_attendance_tables.sql`
5. Click "Go"

### Step 2: Verify File Structure

Ensure all files are in place:
```
amt/
├── application/
│   ├── controllers/admin/
│   │   └── Face_attendance_register.php ✓
│   ├── models/
│   │   └── Face_attendance_student_model.php ✓
│   └── views/admin/face_attendance_register/
│       └── index.php ✓
├── assets/
│   └── face_attendance_models/ ✓ (18 model files)
├── uploads/
│   ├── face_attendance_images/ ✓
│   └── face_attendance_captures/ ✓
└── face_attendance_tables.sql ✓
```

### Step 3: Set Folder Permissions

Make upload directories writable:
```powershell
# On Windows with PowerShell (already created)
# On Linux/Mac:
chmod -R 777 uploads/face_attendance_images
chmod -R 777 uploads/face_attendance_captures
```

### Step 4: Access the System

Navigate to:
```
http://localhost/amt/admin/face_attendance_register
```

**Note:** You need proper permissions in the system. If you get an access denied error, add the permission in the admin panel first.

## How to Use

### Register a Student

1. **Fill Student Details:**
   - Registration Number (required, unique)
   - Admission Number (optional)
   - First Name (required)
   - Last Name (required)
   - Email (optional)
   - Phone (optional)

2. **Capture Face Images:**
   - Click "Start Face Capture" button
   - Allow camera access when prompted
   - System will automatically capture 5 images (1 per second)
   - Ensure good lighting and look directly at the camera
   - Minimum 3 images required to register

3. **Submit Registration:**
   - Click "Register Student" button
   - System will save student data and face images
   - Success message will appear
   - Page will reload with updated student list

### View Registered Students

- Right panel shows all registered students
- Each student card displays:
  - Name
  - Registration number
  - Face image thumbnails
  - Delete button

### Delete a Student

- Click trash icon on student card
- Confirm deletion
- Student data and all face images will be removed

## Database Schema

### face_attendance_students Table
```sql
- id (Primary Key)
- student_id (Link to existing student table)
- registration_number (Unique)
- admission_no
- first_name
- last_name
- class_id
- section_id
- email
- phone
- face_images (JSON - array of image filenames)
- face_descriptors (JSON - for future face recognition)
- is_active
- registered_by
- registration_date
- last_updated
```

### face_attendance_records Table
```sql
- id (Primary Key)
- face_student_id (Foreign Key)
- registration_number
- attendance_date
- attendance_time
- attendance_status (Present/Absent/Late)
- confidence_score
- captured_image
- session_id
- class_id
- section_id
- marked_by
- recognition_method (Auto/Manual/Verified)
- notes
- created_at
```

### face_attendance_logs Table
```sql
- id (Primary Key)
- session_date
- recognition_time
- detected_faces
- recognized_faces
- unknown_faces
- recognition_details (JSON)
- created_by
- created_at
```

## Features Implemented

✅ **Student Registration**
- Unique registration number validation
- Automatic face image capture (5 images)
- Real-time camera preview
- Image preview before submission
- AJAX-based submission

✅ **Data Management**
- View all registered students
- Delete student with face data
- Image storage in organized folders
- Database relationships maintained

✅ **User Interface**
- Responsive design
- Real-time validation
- Auto-capture countdown
- Success/error notifications
- Thumbnail preview

✅ **Security**
- RBAC permission checks
- SQL injection prevention (prepared statements)
- XSS protection
- Unique constraint on registration numbers

## Technical Details

### Camera Capture Process
1. Request camera permission via `navigator.mediaDevices.getUserMedia()`
2. Display video stream in canvas element
3. Auto-capture 5 frames at 1-second intervals
4. Convert to base64 PNG format
5. Display preview thumbnails
6. Submit to server on registration

### Image Storage
- Directory: `uploads/face_attendance_images/{registration_number}/`
- Format: PNG files named 1.png, 2.png, 3.png, etc.
- Base64 decoded and saved to disk
- File paths stored in database as JSON array

### Face Recognition Models
- Location: `assets/face_attendance_models/`
- Models included:
  - ssd_mobilenetv1 (face detection)
  - face_recognition_model
  - face_landmark_68
  - face_landmark_68_tiny
  - tiny_face_detector
  - mtcnn_model
  - face_expression_model
  - age_gender_model

## Next Steps (Future Implementation)

1. **Face Recognition Attendance Marking**
   - Load student face descriptors
   - Real-time face detection and matching
   - Auto-mark attendance based on recognition
   - Confidence score threshold configuration

2. **Attendance Reports**
   - Daily/Weekly/Monthly reports
   - Student-wise attendance history
   - Class-wise statistics
   - Export to Excel/PDF

3. **Integration**
   - Link to existing student database
   - Sync with class/section data
   - Session-based attendance
   - SMS/Email notifications

4. **Enhanced Features**
   - Face descriptor training
   - Multiple camera support
   - Attendance via mobile app
   - Anti-spoofing detection

## Troubleshooting

### Camera Not Working
- Ensure HTTPS connection (or localhost)
- Grant camera permissions in browser
- Check if camera is already in use
- Try different browser (Chrome recommended)

### Images Not Saving
- Check upload directory permissions (777)
- Verify directory exists
- Check disk space
- Review PHP upload limits in php.ini

### Registration Number Already Exists
- System checks for duplicates
- Clear the field and try different number
- Check registered students list

### Database Errors
- Ensure tables are created
- Check foreign key constraints
- Verify database connection
- Check error logs in application/logs/

## Browser Compatibility

**Recommended:**
- Chrome 80+
- Firefox 75+
- Safari 13+
- Edge 80+

**Requirements:**
- JavaScript enabled
- Camera access permission
- Modern browser with getUserMedia API support

## Security Notes

1. **Permissions:** System uses RBAC for access control
2. **Data Validation:** Server-side validation on all inputs
3. **SQL Injection:** Prepared statements used throughout
4. **File Upload:** Only base64 images accepted, validated format
5. **Directory Traversal:** Registration numbers sanitized

## Support

For issues or questions:
1. Check this documentation
2. Review browser console for errors
3. Check PHP error logs
4. Verify database structure
5. Test with different browser

## Version History

- **v1.0.0** (December 2, 2025)
  - Initial implementation
  - Student registration with face capture
  - Basic CRUD operations
  - Model files integration
  - Database schema creation

---

**System developed using:**
- CodeIgniter 3.x
- Face-API.js 0.22.2
- MySQL 5.7+
- jQuery 3.x
- Bootstrap 3.x (AdminLTE theme)
