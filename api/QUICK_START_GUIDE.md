# Student Admission API - Quick Start Guide

## 🚀 Getting Started in 5 Minutes

### Step 1: Verify Installation

Make sure all files are in place:
```
api/
├── application/
│   └── controllers/
│       └── Student_admission_api.php       ✓ Main API controller
├── STUDENT_API_DOCUMENTATION.md            ✓ Full documentation
├── STUDENT_API_IMPLEMENTATION_SUMMARY.md   ✓ Implementation details
├── QUICK_START_GUIDE.md                    ✓ This file
├── test_student_api.php                    ✓ Test script
└── Student_API_Postman_Collection.json     ✓ Postman collection
```

### Step 2: Test the API

#### Option A: Using the Test Script (Easiest)
```bash
cd api
php test_student_api.php
```

#### Option B: Using Browser
Navigate to:
```
http://localhost/amt/api/test_student_api.php
```

#### Option C: Using cURL (Quick Test)
```bash
curl -X POST http://localhost/amt/api/student_admission_api/create \
  -H "Content-Type: application/json" \
  -d '{
    "firstname": "Test",
    "lastname": "Student",
    "gender": "Male",
    "dob": "2010-01-15",
    "class_id": 1,
    "section_id": 1,
    "reference_id": 1,
    "guardian_name": "Test Guardian",
    "guardian_is": "Father",
    "guardian_phone": "9876543210",
    "guardian_email": "testguardian@example.com",
    "email": "teststudent@example.com",
    "mobileno": "9123456789"
  }'
```

#### Option D: Using Postman
1. Open Postman
2. Import `Student_API_Postman_Collection.json`
3. Select "Create Student - Minimal Required Fields"
4. Click "Send"

### Step 3: Verify Success

**Expected Response (HTTP 201):**
```json
{
  "status": 1,
  "message": "Student created successfully",
  "data": {
    "student_id": 123,
    "admission_no": "ADM001",
    "roll_no": "R001",
    "student_session_id": 456,
    "student_username": "std123",
    "student_password": "abc123",
    "parent_username": "par123",
    "parent_password": "xyz789",
    "firstname": "Test",
    "lastname": "Student",
    "gender": "Male",
    "class_id": 1,
    "section_id": 1
  },
  "timestamp": "2024-10-04 10:30:45"
}
```

### Step 4: Verify in Database

Check the following tables:
```sql
-- Check student record
SELECT * FROM students WHERE id = 123;

-- Check student session
SELECT * FROM student_session WHERE student_id = 123;

-- Check login credentials
SELECT * FROM users WHERE user_id = 123 AND role = 'student';
SELECT * FROM users WHERE role = 'parent' AND childs LIKE '%123%';
```

---

## 📋 Minimal Request Example

The absolute minimum data needed to create a student:

```json
{
  "firstname": "John",
  "gender": "Male",
  "dob": "2010-01-15",
  "class_id": 1,
  "section_id": 1,
  "reference_id": 1,
  "guardian_name": "Jane Doe",
  "guardian_is": "Mother",
  "guardian_phone": "9876543210",
  "guardian_email": "jane@example.com"
}
```

**Note:** Additional fields may be required based on your school settings.

---

## 🔍 Common Issues & Solutions

### Issue 1: "Validation failed" Error
**Cause:** Missing required fields or invalid data format

**Solution:** Check the error response for specific field errors:
```json
{
  "status": 0,
  "message": "Validation failed",
  "errors": {
    "firstname": "First Name is required",
    "mobileno": "Mobile Number must be exactly 10 characters"
  }
}
```

### Issue 2: "Email already exists"
**Cause:** Student or guardian email is already in the database

**Solution:** Use a unique email address or check existing records

### Issue 3: "Admission number already exists"
**Cause:** Duplicate admission number

**Solution:** 
- If auto-generation is enabled, check school settings
- If manual, provide a unique admission number

### Issue 4: "Database connection failed"
**Cause:** Database configuration issue

**Solution:** Check `api/application/config/database.php`

### Issue 5: 404 Not Found
**Cause:** Incorrect URL or routing issue

**Solution:** Verify URL is exactly:
```
http://localhost/amt/api/student_webservice/create
```

---

## 📊 Field Reference

### Required Fields (Always)
| Field | Example | Notes |
|-------|---------|-------|
| firstname | "John" | Student's first name |
| gender | "Male" or "Female" | Must be exact |
| dob | "2010-01-15" | YYYY-MM-DD format |
| class_id | 1 | Must exist in classes table |
| section_id | 1 | Must exist in sections table |
| reference_id | 1 | Must exist in staff table |

### Conditional Required Fields
| Field | When Required | Example |
|-------|---------------|---------|
| guardian_name | If setting enabled | "Jane Doe" |
| guardian_is | If setting enabled | "Mother" |
| guardian_phone | If setting enabled | "9876543210" |
| lastname | If setting enabled | "Doe" |
| mobileno | If setting enabled | "9123456789" |

### Commonly Used Optional Fields
| Field | Example | Notes |
|-------|---------|-------|
| email | "john@example.com" | Must be unique |
| middlename | "Michael" | |
| blood_group | "O+" | O+, A+, B+, AB+, O-, A-, B-, AB- |
| religion | "Hindu" | |
| category_id | 1 | Student category |
| current_address | "123 Main St" | |
| father_name | "Robert Doe" | |
| mother_name | "Jane Doe" | |
| admission_date | "2024-04-01" | |

---

## 🎯 Testing Scenarios

### Scenario 1: Basic Student
```json
{
  "firstname": "Basic",
  "lastname": "Student",
  "gender": "Male",
  "dob": "2010-01-15",
  "class_id": 1,
  "section_id": 1,
  "reference_id": 1,
  "guardian_name": "Guardian",
  "guardian_is": "Father",
  "guardian_phone": "9876543210",
  "guardian_email": "basic@example.com"
}
```

### Scenario 2: Student with Sibling
```json
{
  "firstname": "Sibling",
  "lastname": "Student",
  "gender": "Female",
  "dob": "2012-05-20",
  "class_id": 1,
  "section_id": 1,
  "reference_id": 1,
  "guardian_name": "Guardian",
  "guardian_is": "Father",
  "guardian_phone": "9876543210",
  "email": "sibling@example.com",
  "sibling_id": 123
}
```
**Note:** Replace `123` with actual student ID

### Scenario 3: Student with Transport
```json
{
  "firstname": "Transport",
  "lastname": "Student",
  "gender": "Male",
  "dob": "2010-01-15",
  "class_id": 1,
  "section_id": 1,
  "reference_id": 1,
  "guardian_name": "Guardian",
  "guardian_is": "Father",
  "guardian_phone": "9876543210",
  "guardian_email": "transport@example.com",
  "vehroute_id": 1,
  "route_pickup_point_id": 1,
  "transport_feemaster_id": [1, 2, 3]
}
```

---

## 📚 Next Steps

1. **Read Full Documentation:** `STUDENT_API_DOCUMENTATION.md`
2. **Review Implementation:** `STUDENT_API_IMPLEMENTATION_SUMMARY.md`
3. **Run All Tests:** `php test_student_api.php`
4. **Import Postman Collection:** For interactive testing
5. **Check Database:** Verify records are created correctly

---

## 🆘 Need Help?

### Check Logs
```
application/logs/log-{date}.php
```

### Enable Debug Mode
In `api/index.php`:
```php
define('ENVIRONMENT', 'development');
```

### Common Commands

**Test API:**
```bash
php test_student_api.php
```

**Check Database:**
```sql
SELECT * FROM students ORDER BY id DESC LIMIT 5;
SELECT * FROM users WHERE role IN ('student', 'parent') ORDER BY id DESC LIMIT 5;
```

**View Logs:**
```bash
tail -f application/logs/log-$(date +%Y-%m-%d).php
```

---

## ✅ Checklist

Before going to production:

- [ ] Test with minimal required fields
- [ ] Test with complete data
- [ ] Test validation errors
- [ ] Test duplicate email/admission number
- [ ] Test sibling creation
- [ ] Test with transport fees
- [ ] Test with fee assignments
- [ ] Verify database records
- [ ] Verify login credentials work
- [ ] Check error logs
- [ ] Review security settings
- [ ] Add authentication (recommended)
- [ ] Set up monitoring/logging

---

**Quick Links:**
- Full Documentation: `STUDENT_API_DOCUMENTATION.md`
- Implementation Summary: `STUDENT_API_IMPLEMENTATION_SUMMARY.md`
- Test Script: `test_student_api.php`
- Postman Collection: `Student_API_Postman_Collection.json`

**API Endpoint:**
```
POST http://localhost/amt/api/student_admission_api/create
```

**Content-Type:**
```
application/json
```

---

**Happy Coding! 🎉**

