# External Results API Documentation

## Overview
The External Results API provides endpoints for managing and searching external (public) exam results. It allows retrieving classes, sections, public exam types, result statuses, and searching for students based on various filters.

## Base URL
`http://localhost/amt/api/Publicresult_api/`

*Note: Replace `localhost/amt` with your actual domain when deploying to production.*

## Authentication
All requests must include the following headers:
- `Client-Service`: `smartschool`
- `Auth-Key`: `schoolAdmin@`

## Endpoints

### 1. Get All Classes
**Method:** `GET`  
**URL:** `get_classes`

**Description:**  
Retrieves all available classes in the system.

**Request Example:**
```bash
curl -X GET "http://localhost/amt/api/Publicresult_api/get_classes" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@"
```

**Response (Success):**
```json
{
    "status": 200,
    "message": "Success",
    "data": [
        {
            "id": "1",
            "class": "Class 1",
            "is_active": "yes",
            "created_at": "2023-01-01 10:00:00",
            "updated_at": "2023-01-01 10:00:00"
        }
    ]
}
```

---

### 2. Get Sections
**Method:** `GET`  
**URL:** `get_sections`

**Description:**  
Retrieves sections. Can optionally filter by class ID.

**Query Parameters:**
- `class_id` (optional): Filter sections by class ID

**Request Example (All Sections):**
```bash
curl -X GET "http://localhost/amt/api/Publicresult_api/get_sections" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@"
```

**Request Example (Sections for Specific Class):**
```bash
curl -X GET "http://localhost/amt/api/Publicresult_api/get_sections?class_id=1" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@"
```

**Response (Success):**
```json
{
    "status": 200,
    "message": "Success",
    "data": [
        {
            "id": "1",
            "section": "A",
            "is_active": "yes"
        }
    ]
}
```

---

### 3. Get Academic Sessions
**Method:** `GET`  
**URL:** `get_sessions`

**Description:**  
Retrieves all available academic sessions.

**Request Example:**
```bash
curl -X GET "http://localhost/amt/api/Publicresult_api/get_sessions" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@"
```

**Response (Success):**
```json
{
    "status": 200,
    "message": "Success",
    "data": [
        {
            "id": "20",
            "session": "2024-25",
            "is_active": "no"
        }
    ]
}
```

---

### 4. Get External Result Types (Public Exam Types)
**Method:** `GET`  
**URL:** `get_external_result_types`

**Description:**  
Retrieves all available external/public exam types. Can optionally filter by session ID.

**Query Parameters:**
- `session_id` (optional): Filter exam types by session ID

**Request Example:**
```bash
curl -X GET "http://localhost/amt/api/Publicresult_api/get_external_result_types" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@"
```

**Response (Success):**
```json
{
    "status": 200,
    "message": "Success",
    "data": [
        {
            "id": "2",
            "examtype": "1 year 1sem mech",
            "session_id": "18",
            "is_active": "no"
        }
    ]
}
```

---

### 5. Get Result Adding Statuses
**Method:** `GET`  
**URL:** `get_result_statuses`

**Description:**  
Retrieves predefined result adding status options.

**Request Example:**
```bash
curl -X GET "http://localhost/amt/api/Publicresult_api/get_result_statuses" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@"
```

**Response (Success):**
```json
{
    "status": 200,
    "message": "Success",
    "data": [
        {
            "id": "all",
            "name": "All"
        },
        {
            "id": "not_added",
            "name": "Not Added"
        },
        {
            "id": "added",
            "name": "Added"
        }
    ]
}
```

---

### 6. Search Students by Filters
**Method:** `POST`  
**URL:** `search_students`

**Description:**  
Searches for students based on class, section, external result type, and result adding status filters.

**Request Body:**
```json
{
    "class_id": "1",
    "section_id": "1",
    "external_result_type_id": "2",
    "result_adding_status": "all",
    "session_id": "20"
}
```

**Request Body Parameters:**
- `class_id` (required): Class ID to filter students
- `section_id` (required): Section ID to filter students
- `external_result_type_id` (required): External exam type ID
- `result_adding_status` (optional): Filter by status - `all`, `not_added`, or `added` (default: `all`)
- `session_id` (optional): Academic session ID

**Request Example:**
```bash
curl -X POST "http://localhost/amt/api/Publicresult_api/search_students" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -H "Content-Type: application/json" \
  -d '{
    "class_id": "1",
    "section_id": "1",
    "external_result_type_id": "2",
    "result_adding_status": "all",
    "session_id": "20"
  }'
```

**Response (Success):**
```json
{
    "status": 200,
    "message": "Success",
    "data": {
        "students": [
            {
                "id": "1",
                "admission_no": "1001",
                "firstname": "John",
                "middlename": "",
                "lastname": "Doe",
                "roll_no": "1",
                "mobileno": "1234567890",
                "email": "john.doe@example.com",
                "class": "Class 1",
                "section": "A",
                "assign_status": "1",
                "full_name": "John Doe",
                "result_status": "Added"
            }
        ],
        "total_count": 1
    }
}
```

**Response (Error - Missing Parameters):**
```json
{
    "status": 400,
    "message": "Class, Section, and External Result Type are required."
}
```

---

### 7. Search Student Results by Hall Ticket
**Method:** `POST`  
**URL:** `search`

**Description:**  
Searches for a student's external exam results by hall ticket number, academic year, and exam type.

**Request Body:**
```json
{
    "hallticket_no": "HT2025001",
    "academic_id": "20",
    "exam_id": "2"
}
```

**Request Body Parameters:**
- `hallticket_no` (required): The student's hall ticket number.
- `academic_id` (required): The ID of the academic session.
- `exam_id` (required): The ID of the external exam type.

**Request Example:**
```bash
curl -X POST "http://localhost/amt/api/Publicresult_api/search" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -H "Content-Type: application/json" \
  -d '{
    "hallticket_no": "HT2025001",
    "academic_id": "20",
    "exam_id": "2"
  }'
```

**Response (Success):**
```json
{
    "status": 200,
    "message": "Success",
    "data": {
        "result_name": {
            "id": "2",
            "examtype": "1 year 1sem mech",
            "session_id": "18",
            "is_active": "no",
            "created_at": "2023-10-28 01:43:17",
            "updated_at": null
        },
        "student_data": {
            "id": "1",
            "firstname": "John",
            "lastname": "Doe",
            "admission_no": "1001",
            "class": "Class 1",
            "section": "A",
            "dob": "2005-05-15",
            "gender": "Male",
            "mobileno": "1234567890",
            "email": "john.doe@example.com",
            "guardian_name": "Jane Doe",
            "guardian_phone": "0987654321"
        },
        "result_status": {
            "id": "1",
            "stid": "1",
            "resultype_id": "2",
            "session_id": "20",
            "assign_status": "1"
        },
        "results": [
            {
                "id": "1",
                "stid": "1",
                "subjectid": "1",
                "resulgroup_id": "2",
                "session_id": "20",
                "minmarks": "0",
                "maxmarks": "100",
                "actualmarks": "85",
                "subject_name": "Mathematics"
            },
            {
                "id": "2",
                "stid": "1",
                "subjectid": "2",
                "resulgroup_id": "2",
                "session_id": "20",
                "minmarks": "0",
                "maxmarks": "100",
                "actualmarks": "92",
                "subject_name": "Physics"
            }
        ],
        "hallticket_no": "HT2025001",
        "academic_id": "20"
    }
}
```

**Response (Error - Student Not Found):**
```json
{
    "status": 404,
    "message": "Student not found."
}
```

**Response (Error - Missing Parameters):**
```json
{
    "status": 400,
    "message": "Hall Ticket Number, Academic Year, and Exam are required."
}
```

---

## Error Responses

### Authentication Error
```json
{
    "status": 401,
    "message": "Client Service or Auth Key is invalid."
}
```

### Bad Request
```json
{
    "status": 400,
    "message": "Bad request."
}
```

### Invalid JSON
```json
{
    "status": 400,
    "message": "Invalid JSON format or empty body."
}
```

---

## Integration Example (PHP)

```php
<?php
$base_url = 'http://localhost/amt/api/Publicresult_api/';
$headers = array(
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@',
    'Content-Type: application/json'
);

function makeApiRequest($endpoint, $method = 'GET', $data = null) {
    global $base_url, $headers;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $base_url . $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

// Get classes
$classes = makeApiRequest('get_classes');
// print_r($classes);

// Get academic sessions
$sessions = makeApiRequest('get_sessions');
// print_r($sessions);

// Get external result types
$exam_types = makeApiRequest('get_external_result_types');
// print_r($exam_types);

// Search students
$search_data = array(
    'class_id' => '1',
    'section_id' => '1',
    'external_result_type_id' => '2',
    'result_adding_status' => 'all',
    'session_id' => '20'
);
$students = makeApiRequest('search_students', 'POST', $search_data);
// print_r($students);

// Search student results by hall ticket
$hallticket_search_data = array(
    'hallticket_no' => 'HT2025001',
    'academic_id' => '20',
    'exam_id' => '2'
);
$student_results = makeApiRequest('search', 'POST', $hallticket_search_data);
// print_r($student_results);
?>
```

---

## Database Tables

- `classes` - Class information
- `sections` - Section information
- `sessions` - Academic sessions
- `publicexamtype` - External exam types
- `students` - Student information
- `student_session` - Student-class-section relationships
- `publicresultaddingstatus` - Result adding status tracking
- `publicresulttable` - External exam results

---

## Notes

1. **Authentication**: All endpoints require valid headers
2. **HTTP Methods**: Use correct method (GET or POST)
3. **JSON Format**: Ensure valid JSON for POST requests
4. **Active Students**: Only active students are returned
