# Internal Results API Documentation

## Overview
The Internal Results API provides endpoints for managing and searching internal exam results. It allows retrieving classes, sections, exam types, result statuses, and searching for students based on various filters.

## Base URL
`http://localhost/amt/api/Internalresult_api/`

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
curl -X GET "http://localhost/amt/api/Internalresult_api/get_classes" \
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
        },
        {
            "id": "2",
            "class": "Class 2",
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
curl -X GET "http://localhost/amt/api/Internalresult_api/get_sections" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@"
```

**Request Example (Sections for Specific Class):**
```bash
curl -X GET "http://localhost/amt/api/Internalresult_api/get_sections?class_id=1" \
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
            "is_active": "yes",
            "created_at": "2023-01-01 10:00:00",
            "updated_at": "2023-01-01 10:00:00"
        },
        {
            "id": "2",
            "section": "B",
            "is_active": "yes",
            "created_at": "2023-01-01 10:00:00",
            "updated_at": "2023-01-01 10:00:00"
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
curl -X GET "http://localhost/amt/api/Internalresult_api/get_sessions" \
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
            "is_active": "no",
            "created_at": "2016-10-01 10:59:18",
            "updated_at": "0000-00-00"
        },
        {
            "id": "21",
            "session": "2025-26",
            "is_active": "yes",
            "created_at": "2025-01-01 10:00:00",
            "updated_at": "2025-01-01 10:00:00"
        }
    ]
}
```

---

### 4. Get Internal Result Types (Exam Types)
**Method:** `GET`  
**URL:** `get_internal_result_types`

**Description:**  
Retrieves all available internal exam types. Can optionally filter by session ID.

**Query Parameters:**
- `session_id` (optional): Filter exam types by session ID

**Request Example (All Exam Types):**
```bash
curl -X GET "http://localhost/amt/api/Internalresult_api/get_internal_result_types" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@"
```

**Request Example (Exam Types for Specific Session):**
```bash
curl -X GET "http://localhost/amt/api/Internalresult_api/get_internal_result_types?session_id=20" \
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
            "examtype": "First Internal",
            "session_id": "20",
            "is_active": "yes",
            "created_at": "2023-10-28 01:43:17",
            "updated_at": null
        },
        {
            "id": "2",
            "examtype": "Second Internal",
            "session_id": "20",
            "is_active": "yes",
            "created_at": "2023-10-28 01:43:17",
            "updated_at": null
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
curl -X GET "http://localhost/amt/api/Internalresult_api/get_result_statuses" \
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
Searches for students based on class, section, internal result type, and result adding status filters.

**Request Body:**
```json
{
    "class_id": "1",
    "section_id": "1",
    "internal_result_type_id": "1",
    "result_adding_status": "all",
    "session_id": "20"
}
```

**Request Body Parameters:**
- `class_id` (required): Class ID to filter students
- `section_id` (required): Section ID to filter students
- `internal_result_type_id` (required): Internal exam type ID
- `result_adding_status` (optional): Filter by status - `all`, `not_added`, or `added` (default: `all`)
- `session_id` (optional): Academic session ID

**Request Example:**
```bash
curl -X POST "http://localhost/amt/api/Internalresult_api/search_students" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -H "Content-Type: application/json" \
  -d '{
    "class_id": "1",
    "section_id": "1",
    "internal_result_type_id": "1",
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
            },
            {
                "id": "2",
                "admission_no": "1002",
                "firstname": "Jane",
                "middlename": "M",
                "lastname": "Smith",
                "roll_no": "2",
                "mobileno": "0987654321",
                "email": "jane.smith@example.com",
                "class": "Class 1",
                "section": "A",
                "assign_status": null,
                "full_name": "Jane M Smith",
                "result_status": "Not Added"
            }
        ],
        "total_count": 2
    }
}
```

**Response (Error - Missing Parameters):**
```json
{
    "status": 400,
    "message": "Class, Section, and Internal Result Type are required."
}
```

---

### 7. Search Student Results by Admission Number
**Method:** `POST`  
**URL:** `search`

**Description:**  
Searches for a student's internal exam results by admission number, academic year, and exam type.

**Request Body:**
```json
{
    "admission_no": "1001",
    "academic_id": "20",
    "exam_id": "1"
}
```

**Request Body Parameters:**
- `admission_no` (required): Student admission number
- `academic_id` (required): Academic session ID
- `exam_id` (required): Internal exam type ID

**Request Example:**
```bash
curl -X POST "http://localhost/amt/api/Internalresult_api/search" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -H "Content-Type: application/json" \
  -d '{
    "admission_no": "1001",
    "academic_id": "20",
    "exam_id": "1"
  }'
```

**Response (Success):**
```json
{
    "status": 200,
    "message": "Success",
    "data": {
        "result_name": {
            "id": "1",
            "examtype": "First Internal",
            "session_id": "20",
            "is_active": "yes",
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
            "mobileno": "1234567890",
            "email": "john.doe@example.com"
        },
        "result_status": {
            "id": "1",
            "stid": "1",
            "resultype_id": "1",
            "session_id": "20",
            "assign_status": "1"
        },
        "results": [
            {
                "id": "1",
                "stid": "1",
                "subjectid": "1",
                "resulgroup_id": "1",
                "session_id": "20",
                "minmarks": "0",
                "maxmarks": "100",
                "actualmarks": "85",
                "examtype": "Mathematics"
            },
            {
                "id": "2",
                "stid": "1",
                "subjectid": "2",
                "resulgroup_id": "1",
                "session_id": "20",
                "minmarks": "0",
                "maxmarks": "100",
                "actualmarks": "90",
                "examtype": "Science"
            }
        ],
        "admission_no": "1001",
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
    "message": "Admission Number, Academic Year, and Exam are required."
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

### Bad Request (Wrong HTTP Method)
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

// API Configuration
$base_url = 'http://localhost/amt/api/Internalresult_api/';
$headers = array(
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@',
    'Content-Type: application/json'
);

// Function to make API requests
function makeApiRequest($endpoint, $method = 'GET', $data = null) {
    global $base_url, $headers;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $base_url . $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    }
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return json_decode($response, true);
}

// Example 1: Get all classes
$classes = makeApiRequest('get_classes');
print_r($classes);

// Example 2: Get sections for a specific class
$sections = makeApiRequest('get_sections?class_id=1');
print_r($sections);

// Example 3: Get internal result types
$exam_types = makeApiRequest('get_internal_result_types');
print_r($exam_types);

// Example 4: Search students
$search_data = array(
    'class_id' => '1',
    'section_id' => '1',
    'internal_result_type_id' => '1',
    'result_adding_status' => 'all',
    'session_id' => '20'
);
$students = makeApiRequest('search_students', 'POST', $search_data);
print_r($students);

// Example 5: Get student results by admission number
$result_data = array(
    'admission_no' => '1001',
    'academic_id' => '20',
    'exam_id' => '1'
);
$results = makeApiRequest('search', 'POST', $result_data);
print_r($results);

?>
```

---

## Integration Example (JavaScript)

```javascript
// API Configuration
const baseUrl = 'http://localhost/amt/api/Internalresult_api/';
const headers = {
    'Client-Service': 'smartschool',
    'Auth-Key': 'schoolAdmin@',
    'Content-Type': 'application/json'
};

// Function to make API requests
async function makeApiRequest(endpoint, method = 'GET', data = null) {
    const options = {
        method: method,
        headers: headers
    };
    
    if (method === 'POST' && data) {
        options.body = JSON.stringify(data);
    }
    
    try {
        const response = await fetch(baseUrl + endpoint, options);
        const result = await response.json();
        return result;
    } catch (error) {
        console.error('API Error:', error);
        return null;
    }
}

// Example 1: Get all classes
makeApiRequest('get_classes').then(data => {
    console.log('Classes:', data);
});

// Example 2: Get sections for a specific class
makeApiRequest('get_sections?class_id=1').then(data => {
    console.log('Sections:', data);
});

// Example 3: Get internal result types
makeApiRequest('get_internal_result_types').then(data => {
    console.log('Exam Types:', data);
});

// Example 4: Search students
const searchData = {
    class_id: '1',
    section_id: '1',
    internal_result_type_id: '1',
    result_adding_status: 'all',
    session_id: '20'
};
makeApiRequest('search_students', 'POST', searchData).then(data => {
    console.log('Students:', data);
});

// Example 5: Get student results by admission number
const resultData = {
    admission_no: '1001',
    academic_id: '20',
    exam_id: '1'
};
makeApiRequest('search', 'POST', resultData).then(data => {
    console.log('Results:', data);
});
```

---

## Database Schema Reference

### Tables Used

#### `classes`
- `id`: Primary key
- `class`: Class name
- `is_active`: Active status
- `created_at`: Creation timestamp
- `updated_at`: Update timestamp

#### `sections`
- `id`: Primary key
- `section`: Section name
- `is_active`: Active status
- `created_at`: Creation timestamp
- `updated_at`: Update timestamp

#### `sessions`
- `id`: Primary key
- `session`: Session name (e.g., "2024-25")
- `is_active`: Active status
- `created_at`: Creation timestamp
- `updated_at`: Update timestamp

#### `examtype`
- `id`: Primary key
- `examtype`: Exam type name
- `session_id`: Foreign key to sessions table
- `is_active`: Active status
- `created_at`: Creation timestamp
- `updated_at`: Update timestamp

#### `students`
- `id`: Primary key
- `admission_no`: Student admission number
- `firstname`: First name
- `middlename`: Middle name
- `lastname`: Last name
- `roll_no`: Roll number
- `mobileno`: Mobile number
- `email`: Email address
- `is_active`: Active status

#### `student_session`
- `id`: Primary key
- `student_id`: Foreign key to students table
- `class_id`: Foreign key to classes table
- `section_id`: Foreign key to sections table
- `session_id`: Foreign key to sessions table

#### `resultaddingstatus`
- `id`: Primary key
- `stid`: Student ID (foreign key to students table)
- `resultype_id`: Result type ID (foreign key to examtype table)
- `session_id`: Session ID (foreign key to sessions table)
- `assign_status`: Status (0 = Not Added, 1 = Added)

#### `internalresulttable`
- `id`: Primary key
- `stid`: Student ID
- `subjectid`: Subject ID
- `resulgroup_id`: Result group ID (exam type)
- `session_id`: Session ID
- `minmarks`: Minimum marks
- `maxmarks`: Maximum marks
- `actualmarks`: Actual marks obtained

---

## Notes

1. **Authentication**: All endpoints require valid authentication headers. Requests without proper headers will receive a 401 error.

2. **HTTP Methods**: Ensure you use the correct HTTP method (GET or POST) for each endpoint.

3. **JSON Format**: For POST requests, ensure the request body is valid JSON format.

4. **Session Filtering**: When filtering by session, make sure to use the current or appropriate session ID.

5. **Result Status**: The `result_adding_status` parameter accepts three values:
   - `all`: Returns all students
   - `not_added`: Returns only students whose results have not been added
   - `added`: Returns only students whose results have been added

6. **Active Students**: The `search_students` endpoint only returns active students (`is_active = 'yes'`).
