# Quick Start Guide: Class Subject & Admission Report APIs

## 🎯 Overview

This guide provides everything you need to start using the **Class Subject Report API** and **Admission Report API** immediately.

---

## ✅ Status: READY TO USE

Both APIs are **fully implemented, documented, and tested**. No additional development work is required.

---

## 🚀 Quick Start

### 1. Class Subject Report API

#### Get All Subject Assignments
```bash
curl -X POST "http://localhost/amt/api/class-subject-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

#### Filter by Class
```bash
curl -X POST "http://localhost/amt/api/class-subject-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": 1}'
```

#### Filter by Multiple Classes and Sections
```bash
curl -X POST "http://localhost/amt/api/class-subject-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": [1, 2, 3], "section_id": [1, 2]}'
```

---

### 2. Admission Report API

#### Get All Students
```bash
curl -X POST "http://localhost/amt/api/admission-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

#### Filter by Class
```bash
curl -X POST "http://localhost/amt/api/admission-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": 1}'
```

#### Filter by Admission Year
```bash
curl -X POST "http://localhost/amt/api/admission-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"year": 2024}'
```

#### Filter by Multiple Classes and Years
```bash
curl -X POST "http://localhost/amt/api/admission-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": [1, 2], "year": [2023, 2024]}'
```

---

## 📋 API Endpoints Summary

| API | Endpoint | Method | Purpose |
|-----|----------|--------|---------|
| Class Subject | `/api/class-subject-report/filter` | POST | Filter subject assignments |
| Class Subject | `/api/class-subject-report/list` | POST | List all subject assignments |
| Admission | `/api/admission-report/filter` | POST | Filter student admissions |
| Admission | `/api/admission-report/list` | POST | List all student admissions |

---

## 🔑 Required Headers

Both APIs require these headers:

```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

---

## 📊 Request Parameters

### Class Subject Report API

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `class_id` | int or array | No | Filter by class ID(s) |
| `section_id` | int or array | No | Filter by section ID(s) |
| `session_id` | int | No | Session ID (defaults to current) |

### Admission Report API

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `class_id` | int or array | No | Filter by class ID(s) |
| `year` | int or array | No | Filter by admission year(s) |
| `session_id` | int | No | Session ID (defaults to current) |

**Note**: All parameters are optional. Empty request `{}` returns all records.

---

## 💡 JavaScript Examples

### Class Subject Report
```javascript
async function getClassSubjects(filters = {}) {
  const response = await fetch('http://localhost/amt/api/class-subject-report/filter', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Client-Service': 'smartschool',
      'Auth-Key': 'schoolAdmin@'
    },
    body: JSON.stringify(filters)
  });
  return await response.json();
}

// Usage
const allSubjects = await getClassSubjects();
const class1Subjects = await getClassSubjects({ class_id: 1 });
const multipleClasses = await getClassSubjects({ class_id: [1, 2, 3] });
```

### Admission Report
```javascript
async function getAdmissions(filters = {}) {
  const response = await fetch('http://localhost/amt/api/admission-report/filter', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Client-Service': 'smartschool',
      'Auth-Key': 'schoolAdmin@'
    },
    body: JSON.stringify(filters)
  });
  return await response.json();
}

// Usage
const allStudents = await getAdmissions();
const class1Students = await getAdmissions({ class_id: 1 });
const year2024 = await getAdmissions({ year: 2024 });
const filtered = await getAdmissions({ class_id: [1, 2], year: [2023, 2024] });
```

---

## 📱 Response Format

Both APIs return consistent JSON responses:

### Success Response
```json
{
  "status": 1,
  "message": "Report retrieved successfully",
  "filters_applied": {
    "class_id": [1],
    "session_id": 18
  },
  "total_records": 25,
  "data": [...],
  "timestamp": "2025-10-09 10:30:45"
}
```

### Error Response
```json
{
  "status": 0,
  "message": "Error description",
  "timestamp": "2025-10-09 10:30:45"
}
```

---

## 🧪 Interactive Testing

Both APIs have interactive HTML test files for easy testing:

1. **Class Subject Report**: 
   - Open `api/documentation/class_subject_report_api_test.html` in your browser
   - Test various filter combinations
   - View real-time responses

2. **Admission Report**: 
   - Open `api/documentation/admission_report_api_test.html` in your browser
   - Test various filter combinations
   - View real-time responses

---

## 🎯 Key Features

### 1. Graceful Null/Empty Handling
All these requests return ALL records:
```json
{}
{"class_id": null}
{"class_id": []}
{"class_id": null, "section_id": null}
```

### 2. Multi-Select Support
Single or multiple values:
```json
// Single
{"class_id": 1}

// Multiple
{"class_id": [1, 2, 3]}
```

### 3. Session Awareness
Automatically uses current session or accepts custom:
```json
{"session_id": 18}
```

---

## 📂 File Locations

### Class Subject Report API
- **Controller**: `api/application/controllers/Class_subject_report_api.php`
- **Documentation**: `api/documentation/student_information/CLASS_SUBJECT_REPORT_API_README.md`
- **Test File**: `api/documentation/class_subject_report_api_test.html`
- **Routes**: Lines 243-244 in `api/application/config/routes.php`

### Admission Report API
- **Controller**: `api/application/controllers/Admission_report_api.php`
- **Documentation**: `api/documentation/ADMISSION_REPORT_API_README.md`
- **Test File**: `api/documentation/admission_report_api_test.html`
- **Routes**: Lines 231-232 in `api/application/config/routes.php`

---

## 🔧 Troubleshooting

### 401 Unauthorized
**Problem**: Missing or incorrect authentication headers

**Solution**: Ensure headers are correct:
```
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

### 400 Bad Request
**Problem**: Using GET instead of POST

**Solution**: Use POST method:
```bash
curl -X POST "http://localhost/amt/api/..." ...
```

### Empty Response
**Problem**: No data in database or wrong session

**Solution**:
- Check if data exists in database
- Verify session_id is correct
- Check application logs at `api/application/logs/`

---

## 📚 Complete Documentation

For detailed information, see:

### Class Subject Report API
- **Main README**: `api/documentation/student_information/CLASS_SUBJECT_REPORT_API_README.md`

### Admission Report API
- **Main README**: `api/documentation/ADMISSION_REPORT_API_README.md`
- **Quick Reference**: `api/documentation/ADMISSION_REPORT_API_QUICK_REFERENCE.md`
- **Implementation Summary**: `api/documentation/ADMISSION_REPORT_API_IMPLEMENTATION_SUMMARY.md`
- **Detailed Docs**: `api/documentation/student_information/ADMISSION_REPORT_API_DOCUMENTATION.md`

---

## ✅ Verification

Both APIs are:
- ✅ Fully implemented
- ✅ Thoroughly documented
- ✅ Routes configured
- ✅ Authentication working
- ✅ Test files available
- ✅ Separate from web pages
- ✅ Production ready

---

## 🎉 You're Ready!

Both APIs are ready to use immediately. Start with the interactive test files or use the cURL examples above.

**Happy coding!** 🚀

