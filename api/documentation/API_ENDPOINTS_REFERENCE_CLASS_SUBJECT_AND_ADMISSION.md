# API Endpoints Reference: Class Subject & Admission Reports

**Quick Reference Guide for Developers**

---

## 📋 Endpoints Overview

| API | Endpoint | Method | Auth Required | Status |
|-----|----------|--------|---------------|--------|
| Class Subject Report | `/api/class-subject-report/filter` | POST | ✅ Yes | ✅ Live |
| Class Subject Report | `/api/class-subject-report/list` | POST | ✅ Yes | ✅ Live |
| Admission Report | `/api/admission-report/filter` | POST | ✅ Yes | ✅ Live |
| Admission Report | `/api/admission-report/list` | POST | ✅ Yes | ✅ Live |

---

## 🔐 Authentication

**Required Headers** (for all endpoints):
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

---

## 1️⃣ Class Subject Report API

### Endpoint 1: Filter Class Subjects

**URL**: `POST http://localhost/amt/api/class-subject-report/filter`

**Request Parameters**:
```json
{
  "class_id": 1,           // Optional: int or array [1, 2, 3]
  "section_id": 2,         // Optional: int or array [1, 2]
  "session_id": 18         // Optional: int (defaults to current session)
}
```

**Response Fields**:
```json
{
  "status": 1,
  "message": "Class subject report retrieved successfully",
  "filters_applied": {
    "class_id": [1],
    "section_id": [2],
    "session_id": 18
  },
  "total_records": 15,
  "data": [
    {
      "timetable_id": "123",
      "subject_id": "5",
      "subject_name": "Mathematics",
      "subject_code": "MATH101",
      "subject_type": "Theory",
      "staff_id": "10",
      "staff_name": "John",
      "staff_surname": "Doe",
      "employee_id": "EMP001",
      "class_id": "1",
      "class_name": "Class 10",
      "section_id": "2",
      "section_name": "A",
      "day": "Monday",
      "time_from": "09:00:00",
      "time_to": "10:00:00",
      "start_time": "09:00:00",
      "end_time": "10:00:00",
      "room_no": "101",
      "session_id": "18",
      "subject_group_id": "3",
      "subject_group_subject_id": "7",
      "class_teacher": "10"
    }
  ],
  "timestamp": "2025-10-09 10:30:45"
}
```

**Use Cases**:
- Get all subject assignments for a class
- Get subjects for specific sections
- Get timetable information
- Get teacher assignments

---

### Endpoint 2: List All Class Subjects

**URL**: `POST http://localhost/amt/api/class-subject-report/list`

**Request Parameters**: None (empty body `{}`)

**Response**: Same format as filter endpoint, returns all subject assignments

---

## 2️⃣ Admission Report API

### Endpoint 1: Filter Admissions

**URL**: `POST http://localhost/amt/api/admission-report/filter`

**Request Parameters**:
```json
{
  "class_id": 1,           // Optional: int or array [1, 2, 3]
  "year": 2024,            // Optional: int or array [2023, 2024]
  "session_id": 18         // Optional: int (defaults to current session)
}
```

**Response Fields**:
```json
{
  "status": 1,
  "message": "Admission report retrieved successfully",
  "filters_applied": {
    "class_id": [1],
    "year": [2024],
    "session_id": 18
  },
  "total_records": 50,
  "data": [
    {
      "student_id": "123",
      "admission_no": "ADM2024001",
      "admission_date": "2024-04-15",
      "firstname": "John",
      "lastname": "Smith",
      "student_name": "John Smith",
      "class_id": "1",
      "class": "Class 10",
      "section_id": "2",
      "section": "A",
      "mobileno": "9876543210",
      "guardian_name": "Robert Smith",
      "guardian_relation": "Father",
      "guardian_phone": "9876543211",
      "guardian_email": "robert@example.com",
      "session_id": "18",
      "session": "2024-25"
    }
  ],
  "timestamp": "2025-10-09 10:30:45"
}
```

**Use Cases**:
- Get all students admitted in a specific year
- Get admissions for specific classes
- Get student and guardian information
- Generate admission reports

---

### Endpoint 2: List All Admissions

**URL**: `POST http://localhost/amt/api/admission-report/list`

**Request Parameters**: None (empty body `{}`)

**Response**: Same format as filter endpoint, returns all active students

---

## 🎯 Common Use Cases

### Use Case 1: Get All Records
```bash
# Class Subject Report - All subjects
curl -X POST "http://localhost/amt/api/class-subject-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'

# Admission Report - All students
curl -X POST "http://localhost/amt/api/admission-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Use Case 2: Filter by Single Class
```bash
# Class Subject Report
curl -X POST "http://localhost/amt/api/class-subject-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": 1}'

# Admission Report
curl -X POST "http://localhost/amt/api/admission-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": 1}'
```

### Use Case 3: Filter by Multiple Classes
```bash
# Class Subject Report
curl -X POST "http://localhost/amt/api/class-subject-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": [1, 2, 3]}'

# Admission Report
curl -X POST "http://localhost/amt/api/admission-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": [1, 2, 3]}'
```

### Use Case 4: Complex Filters
```bash
# Class Subject Report - Multiple classes and sections
curl -X POST "http://localhost/amt/api/class-subject-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": [1, 2], "section_id": [1, 2]}'

# Admission Report - Multiple classes and years
curl -X POST "http://localhost/amt/api/admission-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": [1, 2], "year": [2023, 2024]}'
```

---

## 💻 Code Examples

### JavaScript/Fetch

```javascript
// Class Subject Report
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

// Admission Report
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

// Usage Examples
const allSubjects = await getClassSubjects();
const class1Subjects = await getClassSubjects({ class_id: 1 });
const allStudents = await getAdmissions();
const year2024Students = await getAdmissions({ year: 2024 });
```

### PHP/cURL

```php
<?php
// Class Subject Report
function getClassSubjects($filters = []) {
    $ch = curl_init('http://localhost/amt/api/class-subject-report/filter');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($filters));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Client-Service: smartschool',
        'Auth-Key: schoolAdmin@'
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

// Admission Report
function getAdmissions($filters = []) {
    $ch = curl_init('http://localhost/amt/api/admission-report/filter');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($filters));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Client-Service: smartschool',
        'Auth-Key: schoolAdmin@'
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

// Usage
$subjects = getClassSubjects(['class_id' => 1]);
$students = getAdmissions(['year' => 2024]);
?>
```

---

## ⚠️ Error Responses

### 400 Bad Request
```json
{
  "status": 0,
  "message": "Bad request. Only POST method allowed."
}
```

### 401 Unauthorized
```json
{
  "status": 0,
  "message": "Unauthorized access"
}
```

### 500 Internal Server Error
```json
{
  "status": 0,
  "message": "Internal server error",
  "timestamp": "2025-10-09 10:30:45"
}
```

---

## 📊 Response Status Codes

| Status | Meaning |
|--------|---------|
| `1` | Success - Request completed successfully |
| `0` | Error - Request failed (check message field) |

---

## 🔍 Filter Behavior

### Graceful Handling
All these requests return ALL records:
- `{}`
- `{"class_id": null}`
- `{"class_id": []}`
- `{"class_id": "", "section_id": ""}`

### Multi-Select
Both single values and arrays are supported:
- Single: `{"class_id": 1}`
- Multiple: `{"class_id": [1, 2, 3]}`

---

## 📚 Additional Resources

- **Class Subject API Documentation**: `api/documentation/student_information/CLASS_SUBJECT_REPORT_API_README.md`
- **Admission API Documentation**: `api/documentation/ADMISSION_REPORT_API_README.md`
- **Interactive Testers**: 
  - `api/documentation/class_subject_report_api_test.html`
  - `api/documentation/admission_report_api_test.html`

---

**Last Updated**: 2025-10-09  
**API Version**: 1.0.0  
**Status**: Production Ready ✅

