# Student Academic Report API Documentation

## 🎉 **UPDATED - October 8, 2025**
**The API now:**
- ✅ Handles empty/null parameters gracefully (no validation errors)
- ✅ Returns complete detailed fee information including:
  - Fee groups with fee types
  - Payment history with dates and payment modes
  - Student discount information
  - Transport fees

**See:** `STUDENT_ACADEMIC_REPORT_GRACEFUL_HANDLING_FIX.md` for complete details.

---

## Overview
The Student Academic Report API provides endpoints to retrieve individual student academic fee reports with detailed fee structure, payment history, and flexible filtering options (student ID, admission number, class/section, or all students).

**Base URL:** `/api/student-academic-report`

**Authentication Required:** Yes
- Header: `Client-Service: smartschool`
- Header: `Auth-Key: schoolAdmin@`

**HTTP Method:** POST (for all endpoints)

---

## Endpoints

### 1. Filter Endpoint
**URL:** `/api/student-academic-report/filter`

**Purpose:** Retrieve student academic fee report with filters

**Request Body Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| student_id | string/int | No* | Student ID to search for |
| admission_no | string | No* | Admission number to search for |
| class_id | string/int | No* | Class ID to filter by |
| section_id | string/int | No | Section ID to filter by (used with class_id) |
| session_id | string/int | No | Academic session ID to filter by |

**Note:** At least ONE of `student_id`, `admission_no`, or `class_id` is required.

**Example Requests:**

1. **Search by Student ID:**
```json
{
    "student_id": "100"
}
```

2. **Search by Admission Number:**
```json
{
    "admission_no": "ADM001"
}
```

3. **Get All Students from Class:**
```json
{
    "class_id": "1"
}
```

4. **Get Students from Class and Section:**
```json
{
    "class_id": "1",
    "section_id": "1"
}
```

5. **Filter by Session:**
```json
{
    "class_id": "1",
    "session_id": "1"
}
```

**Success Response (200 OK) - Single Student:**
```json
{
    "status": 1,
    "message": "Student academic report retrieved successfully",
    "filters_applied": {
        "student_id": "100",
        "admission_no": null
    },
    "data": {
        "id": "100",
        "admission_no": "ADM001",
        "firstname": "John",
        "middlename": "M",
        "lastname": "Doe",
        "class": "Class 1",
        "section": "A",
        "roll_no": "001",
        "father_name": "Mr. Doe",
        "fees": [
            {
                "id": "1",
                "name": "Tuition Fee",
                "amount": "5000.00",
                "amount_paid": "3000.00",
                "amount_discount": "200.00",
                "amount_fine": "50.00"
            }
        ]
    },
    "timestamp": "2025-10-08 14:30:00"
}
```

**Success Response (200 OK) - Multiple Students:**
```json
{
    "status": 1,
    "message": "Student academic report retrieved successfully",
    "filters_applied": {
        "class_id": "1",
        "section_id": "1",
        "session_id": null
    },
    "total_records": 25,
    "data": [
        {
            "id": "100",
            "admission_no": "ADM001",
            "firstname": "John",
            "middlename": "M",
            "lastname": "Doe",
            "class": "Class 1",
            "section": "A",
            "roll_no": "001",
            "father_name": "Mr. Doe",
            "fees": [...]
        }
    ],
    "timestamp": "2025-10-08 14:30:00"
}
```

**Error Response (400 Bad Request):**
```json
{
    "status": 0,
    "message": "Please provide at least one filter parameter (student_id, admission_no, or class_id)"
}
```

---

### 2. List Endpoint
**URL:** `/api/student-academic-report/list`

**Purpose:** Retrieve filter options

**Request Body:** Empty `{}` or no body required

**Success Response (200 OK):**
```json
{
    "status": 1,
    "message": "Filter options retrieved successfully",
    "data": {
        "classes": [
            {
                "id": "1",
                "class": "Class 1",
                "sections": [
                    {
                        "id": "1",
                        "section": "A"
                    }
                ]
            }
        ],
        "sessions": [
            {
                "id": "1",
                "session": "2024-2025"
            }
        ]
    },
    "timestamp": "2025-10-08 14:30:00"
}
```

---

## Implementation Details

### Controller
**File:** `api/application/controllers/Student_academic_report_api.php`

**Key Features:**
- Authentication check
- Multiple search options (student_id, admission_no, class_id)
- Returns single student or list based on filter
- Requires at least one filter parameter

### Model Methods
**File:** `api/application/models/Student_model.php`

**Methods:**
1. `get($student_id)` - Get student by ID
2. `getByAdmissionNo($admission_no)` - Get student by admission number
3. `searchByClassSection($class_id, $section_id, $session_id)` - Get students by class/section

**File:** `api/application/models/Studentfeemaster_model.php`

**Methods:**
1. `getStudentFees($student_session_id)` - Get student fees

### Routes
**File:** `api/application/config/routes.php`

```php
$route['student-academic-report/filter']['POST'] = 'student_academic_report_api/filter';
$route['student-academic-report/list']['POST'] = 'student_academic_report_api/list';
```

---

## Usage Examples

### cURL Examples

1. **Search by Student ID:**
```bash
curl -X POST "http://localhost/amt/api/student-academic-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"student_id":"100"}'
```

2. **Search by Admission Number:**
```bash
curl -X POST "http://localhost/amt/api/student-academic-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"admission_no":"ADM001"}'
```

3. **Get All Students from Class:**
```bash
curl -X POST "http://localhost/amt/api/student-academic-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id":"1"}'
```

4. **Get Filter Options:**
```bash
curl -X POST "http://localhost/amt/api/student-academic-report/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d "{}"
```

---

## Notes

1. **Search Priority:**
   - If `student_id` is provided, searches by student ID
   - If `admission_no` is provided (and no student_id), searches by admission number
   - If `class_id` is provided (and no student_id/admission_no), returns all students from that class

2. **Response Format:**
   - Single student: Returns student object directly in `data` field
   - Multiple students: Returns array of students in `data` field with `total_records` count

3. **Validation:**
   - At least one filter parameter is required
   - Returns 400 error if no filter provided

---

## API Version
**Version:** 1.0.0  
**Last Updated:** October 8, 2025  
**Status:** Production Ready

