# Behavioral Module - Student Incidents API Documentation

## Overview

The Behavioral Module - Student Incidents API provides functionality for managing student behavioral incidents in the school management system. This API allows retrieving student incidents, managing comments, and tracking behavioral points.

---

## Base URL
```
http://{domain}/api/
```

## Important URL Structure

**For Behavioral Module - Student Incidents APIs, use the controller/method pattern:**
- Get student incidents by student ID: `http://{domain}/api/behaviour/studentincidents/get-by-student`
- Get student total points: `http://{domain}/api/behaviour/studentincidents/total-points`
- Get student behavior records: `http://{domain}/api/behaviour/studentincidents/student-behavior`
- Delete student incident: `http://{domain}/api/behaviour/studentincidents/delete`
- Add student incident comment: `http://{domain}/api/behaviour/studentincidents/add-comment`
- Get student incident comments: `http://{domain}/api/behaviour/studentincidents/get-comments`
- Delete student incident comment: `http://{domain}/api/behaviour/studentincidents/delete-comment`

**Examples:**
- Get by student: `http://localhost/amt/api/behaviour/studentincidents/get-by-student`
- Total points: `http://localhost/amt/api/behaviour/studentincidents/total-points`
- Behavior report: `http://localhost/amt/api/behaviour/studentincidents/report`

## Authentication Headers

All API requests require the following headers:
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

---

## Endpoints

### 1. Get Student Incidents by Student ID

**Endpoint:** `POST /behaviour/studentincidents/get-by-student`
**Full URL:** `http://localhost/amt/api/behaviour/studentincidents/get-by-student`

**Description:** Retrieve all incidents for a specific student.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### Request Body
```json
{
    "student_id": 123,
    "session_value": "current_session"
}
```

#### Success Response (HTTP 200)
```json
{
    "status": 1,
    "data": {
        // Datatable response with student incidents
    },
    "message": "Student incidents retrieved successfully"
}
```

#### Error Response (HTTP 400)
```json
{
    "status": 0,
    "message": "Student ID is required"
}
```

#### cURL Example
```bash
curl -X POST "http://localhost/amt/api/behaviour/studentincidents/get-by-student" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "student_id": 123,
    "session_value": "current_session"
  }'
```

---

### 2. Get Student Total Points

**Endpoint:** `POST /behaviour/studentincidents/total-points`
**Full URL:** `http://localhost/amt/api/behaviour/studentincidents/total-points`

**Description:** Retrieve the total behavioral points for a specific student.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### Request Body
```json
{
    "student_id": 123
}
```

#### Success Response (HTTP 200)
```json
{
    "status": 1,
    "data": {
        "totalpoints": "25"
    },
    "message": "Student total points retrieved successfully"
}
```

#### Error Response (HTTP 400)
```json
{
    "status": 0,
    "message": "Student ID is required"
}
```

#### cURL Example
```bash
curl -X POST "http://localhost/amt/api/behaviour/studentincidents/total-points" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "student_id": 123
  }'
```

---

### 3. Get Student Behavior Records

**Endpoint:** `POST /behaviour/studentincidents/student-behavior`
**Full URL:** `http://localhost/amt/api/behaviour/studentincidents/student-behavior`

**Description:** Retrieve detailed behavior records for a specific student.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### Request Body
```json
{
    "student_id": 123
}
```

#### Success Response (HTTP 200)
```json
{
    "status": 1,
    "data": [
        {
            "title": "Good Behavior",
            "point": "5",
            "description": "Student showed excellent behavior in class",
            "id": "45",
            "created_at": "2025-11-15 10:30:00",
            "student_id": "123",
            "firstname": "John",
            "middlename": "",
            "lastname": "Doe",
            "admission_no": "ADM00123",
            "session": "2025-2026",
            "staff_name": "Jane Smith",
            "staff_surname": "Smith",
            "staff_employee_id": "EMP001",
            "role_id": "2"
        }
    ],
    "message": "Student behavior records retrieved successfully"
}
```

#### Error Response (HTTP 400)
```json
{
    "status": 0,
    "message": "Student ID is required"
}
```

#### cURL Example
```bash
curl -X POST "http://localhost/amt/api/behaviour/studentincidents/student-behavior" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "student_id": 123
  }'
```

---

### 4. Delete Student Incident

**Endpoint:** `POST /behaviour/studentincidents/delete`
**Full URL:** `http://localhost/amt/api/behaviour/studentincidents/delete`

**Description:** Delete a specific student incident by ID.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### Request Body
```json
{
    "id": 45
}
```

#### Success Response (HTTP 200)
```json
{
    "status": 1,
    "message": "Student incident deleted successfully"
}
```

#### Error Response (HTTP 400)
```json
{
    "status": 0,
    "message": "Incident ID is required"
}
```

#### cURL Example
```bash
curl -X POST "http://localhost/amt/api/behaviour/studentincidents/delete" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "id": 45
  }'
```

---

### 5. Add Student Incident Comment

**Endpoint:** `POST /behaviour/studentincidents/add-comment`
**Full URL:** `http://localhost/amt/api/behaviour/studentincidents/add-comment`

**Description:** Add a comment to a student incident.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### Request Body
```json
{
    "data": {
        "student_incident_id": 45,
        "comment": "This is a follow-up comment",
        "type": "followup",
        "staff_id": 5,
        "student_id": 123
    }
}
```

#### Success Response (HTTP 200)
```json
{
    "status": 1,
    "data": {
        "id": 78
    },
    "message": "Student incident comment added successfully"
}
```

#### Error Response (HTTP 400)
```json
{
    "status": 0,
    "message": "Comment data is required"
}
```

#### cURL Example
```bash
curl -X POST "http://localhost/amt/api/behaviour/studentincidents/add-comment" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "data": {
        "student_incident_id": 45,
        "comment": "This is a follow-up comment",
        "type": "followup",
        "staff_id": 5,
        "student_id": 123
    }
  }'
```

---

### 6. Get Student Incident Comments

**Endpoint:** `POST /behaviour/studentincidents/get-comments`
**Full URL:** `http://localhost/amt/api/behaviour/studentincidents/get-comments`

**Description:** Retrieve all comments for a specific student incident.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### Request Body
```json
{
    "student_incident_id": 45
}
```

#### Success Response (HTTP 200)
```json
{
    "status": 1,
    "data": [
        {
            "comment": "This is a follow-up comment",
            "type": "followup",
            "created_date": "2025-11-15 14:30:00",
            "staff_name": "Jane",
            "staff_surname": "Smith",
            "staff_employee_id": "EMP001",
            "staff_image": null,
            "staff_gender": "Female",
            "firstname": "John",
            "middlename": "",
            "lastname": "Doe",
            "admission_no": "ADM00123",
            "student_image": null,
            "id": "78",
            "staff_id": "5",
            "student_id": "123",
            "role_name": "Teacher",
            "stud_gender": "Male"
        }
    ],
    "message": "Student incident comments retrieved successfully"
}
```

#### Error Response (HTTP 400)
```json
{
    "status": 0,
    "message": "Student incident ID is required"
}
```

#### cURL Example
```bash
curl -X POST "http://localhost/amt/api/behaviour/studentincidents/get-comments" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "student_incident_id": 45
  }'
```

---

### 7. Delete Student Incident Comment

**Endpoint:** `POST /behaviour/studentincidents/delete-comment`
**Full URL:** `http://localhost/amt/api/behaviour/studentincidents/delete-comment`

**Description:** Delete a specific student incident comment by ID.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### Request Body
```json
{
    "id": 78
}
```

#### Success Response (HTTP 200)
```json
{
    "status": 1,
    "message": "Student incident comment deleted successfully"
}
```

#### Error Response (HTTP 400)
```json
{
    "status": 0,
    "message": "Comment ID is required"
}
```

#### cURL Example
```bash
curl -X POST "http://localhost/amt/api/behaviour/studentincidents/delete-comment" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "id": 78
  }'
```

---

### 8. Get Behavior Report

**Endpoint:** `POST /behaviour/studentincidents/report`
**Full URL:** `http://localhost/amt/api/behaviour/studentincidents/report`

**Description:** Retrieve a comprehensive behavior report with optional filters for class, section, student, date range, and incident type.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### Request Body
```json
{
    "class_id": 5,
    "section_id": 3,
    "student_id": 123,
    "from_date": "2025-11-01",
    "to_date": "2025-11-30",
    "incident_id": 7
}
```

#### Success Response (HTTP 200)
```json
{
    "status": 1,
    "data": [
        {
            "id": "45",
            "created_at": "2025-11-15 10:30:00",
            "title": "Good Behavior",
            "point": "5",
            "description": "Student showed excellent behavior in class",
            "firstname": "John",
            "middlename": "",
            "lastname": "Doe",
            "admission_no": "ADM00123",
            "class": "Grade 5",
            "section": "A",
            "staff_name": "Jane",
            "staff_surname": "Smith"
        }
    ],
    "message": "Behavior report retrieved successfully"
}
```

#### Error Response (HTTP 500)
```json
{
    "status": 0,
    "message": "Failed to retrieve behavior report"
}
```

#### cURL Example
```bash
curl -X POST "http://localhost/amt/api/behaviour/studentincidents/report" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "class_id": 5,
    "section_id": 3,
    "from_date": "2025-11-01",
    "to_date": "2025-11-30"
  }'
```

---

## Request Parameters

### Get Student Incidents
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| student_id | integer | Yes | Student ID |
| session_value | string | No | Session value (current_session or all) |

### Get Student Total Points
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| student_id | integer | Yes | Student ID |

### Get Student Behavior Records
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| student_id | integer | Yes | Student ID |

### Delete Student Incident
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | integer | Yes | Incident ID |

### Add Student Incident Comment
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| data | object | Yes | Comment data object |

### Get Student Incident Comments
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| student_incident_id | integer | Yes | Student incident ID |

### Delete Student Incident Comment
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | integer | Yes | Comment ID |

### Get Behavior Report
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| class_id | integer | No | Filter by class ID |
| section_id | integer | No | Filter by section ID |
| student_id | integer | No | Filter by student ID |
| from_date | date | No | Filter from date (YYYY-MM-DD) |
| to_date | date | No | Filter to date (YYYY-MM-DD) |
| incident_id | integer | No | Filter by incident type ID |

---

## Response Fields

| Field | Type | Description |
|-------|------|-------------|
| status | integer | 1 for success, 0 for failure |
| data | object/array | Response data |
| message | string | Response message |

---

## Error Codes

| HTTP Code | Description |
|-----------|-------------|
| 200 | Success |
| 400 | Bad request (validation error) |
| 500 | Internal server error |

---

## Testing

Use the provided cURL examples to test the endpoints. Make sure to:

1. Replace `localhost/amt` with your actual domain
2. Ensure the authentication headers are correct
3. Verify that the IDs exist in the respective tables
4. Check that you have proper permissions to access the data

### Test Cases

#### Test Case 1: Get Student Incidents
This test case demonstrates retrieving incidents for a student.

**Request:**
```bash
curl -X POST "http://localhost/amt/api/behaviour/studentincidents/get-by-student" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "student_id": 1,
    "session_value": "current_session"
  }'
```

**Expected Response (HTTP 200):**
```json
{
    "status": 1,
    "data": {
        // Datatable response
    },
    "message": "Student incidents retrieved successfully"
}
```

#### Test Case 2: Get Student Total Points
This test case demonstrates retrieving total points for a student.

**Request:**
```bash
curl -X POST "http://localhost/amt/api/behaviour/studentincidents/total-points" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "student_id": 1
  }'
```

**Expected Response (HTTP 200):**
```json
{
    "status": 1,
    "data": {
        "totalpoints": "15"
    },
    "message": "Student total points retrieved successfully"
}
```

#### Test Case 3: Missing Required Fields
This test case demonstrates the validation error when required fields are missing.

**Request:**
```bash
curl -X POST "http://localhost/amt/api/behaviour/studentincidents/get-by-student" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Expected Response (HTTP 400):**
```json
{
    "status": 0,
    "message": "Student ID is required"
}
```

### Behavior Report Test Cases

#### Test Case 4: Get Behavior Report with No Filters
This test case demonstrates retrieving all behavior incidents without any filters.

**Request:**
```bash
curl -X POST "http://localhost/amt/api/behaviour/studentincidents/report" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Expected Response (HTTP 200):**
```json
{
    "status": 1,
    "data": [
        {
            "id": "1",
            "created_at": "2025-11-15 10:30:00",
            "title": "Good Behavior",
            "point": "5",
            "description": "Student showed excellent behavior in class",
            "firstname": "John",
            "middlename": "",
            "lastname": "Doe",
            "admission_no": "ADM00123",
            "class": "Grade 5",
            "section": "A",
            "staff_name": "Jane",
            "staff_surname": "Smith"
        }
    ],
    "message": "Behavior report retrieved successfully"
}
```

#### Test Case 5: Get Behavior Report with Date Range Filter
This test case demonstrates retrieving behavior incidents within a specific date range.

**Request:**
```bash
curl -X POST "http://localhost/amt/api/behaviour/studentincidents/report" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "from_date": "2025-11-01",
    "to_date": "2025-11-30"
  }'
```

**Expected Response (HTTP 200):**
```json
{
    "status": 1,
    "data": [
        {
            "id": "1",
            "created_at": "2025-11-15 10:30:00",
            "title": "Good Behavior",
            "point": "5",
            "description": "Student showed excellent behavior in class",
            "firstname": "John",
            "middlename": "",
            "lastname": "Doe",
            "admission_no": "ADM00123",
            "class": "Grade 5",
            "section": "A",
            "staff_name": "Jane",
            "staff_surname": "Smith"
        }
    ],
    "message": "Behavior report retrieved successfully"
}
```

#### Test Case 6: Get Behavior Report with Multiple Filters
This test case demonstrates retrieving behavior incidents with multiple filters applied.

**Request:**
```bash
curl -X POST "http://localhost/amt/api/behaviour/studentincidents/report" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "class_id": 5,
    "section_id": 3,
    "from_date": "2025-11-01",
    "to_date": "2025-11-30"
  }'
```

**Expected Response (HTTP 200):**
```json
{
    "status": 1,
    "data": [
        {
            "id": "1",
            "created_at": "2025-11-15 10:30:00",
            "title": "Good Behavior",
            "point": "5",
            "description": "Student showed excellent behavior in class",
            "firstname": "John",
            "middlename": "",
            "lastname": "Doe",
            "admission_no": "ADM00123",
            "class": "Grade 5",
            "section": "A",
            "staff_name": "Jane",
            "staff_surname": "Smith"
        }
    ],
    "message": "Behavior report retrieved successfully"
}
```

---

## Notes

- All endpoints use POST method as per application standards
- Authentication is required for all endpoints
- This API specifically manages student behavioral incidents
- The functionality mirrors the web interface's student incidents management
- Points are stored as integers and can be positive or negative
