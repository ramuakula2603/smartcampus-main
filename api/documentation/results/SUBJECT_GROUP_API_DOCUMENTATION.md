# Subject Group API Documentation

## Overview
The Subject Group API provides endpoints for managing result subject groups with subjects and min/max marks configuration. It allows retrieving result types, subjects, subject groups, and performing CRUD operations.

## Base URL
`http://localhost/amt/api/Subjectgroup_api/`

*Note: Replace `localhost/amt` with your actual domain when deploying to production.*

## Authentication
All requests must include the following headers:
- `Client-Service`: `smartschool`
- `Auth-Key`: `schoolAdmin@`

## Endpoints

### 1. Get Result Types
**Method:** `GET`  
**URL:** `get_result_types`

**Description:**  
Retrieves all available result types (exam types).

**Request Example:**
```bash
curl -X GET "http://localhost/amt/api/Subjectgroup_api/get_result_types" \
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

### 2. Get Subjects
**Method:** `GET`  
**URL:** `get_subjects`

**Description:**  
Retrieves all available subjects.

**Request Example:**
```bash
curl -X GET "http://localhost/amt/api/Subjectgroup_api/get_subjects" \
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
            "name": "Mathematics",
            "code": "MATH",
            "type": "Theory"
        },
        {
            "id": "2",
            "name": "Physics",
            "code": "PHY",
            "type": "Theory"
        }
    ]
}
```

---

### 3. Get All Subject Groups
**Method:** `GET`  
**URL:** `get_subject_groups`

**Description:**  
Retrieves all subject groups.

**Request Example:**
```bash
curl -X GET "http://localhost/amt/api/Subjectgroup_api/get_subject_groups" \
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
            "group_subject": [
                {
                    "subject_id": "1",
                    "subject_name": "Mathematics",
                    "minmarks": "0",
                    "maxmarks": "100"
                }
            ]
        }
    ]
}
```

---

### 4. Get Specific Subject Group
**Method:** `GET`  
**URL:** `get_subject_group/:id`

**Description:**  
Retrieves a specific subject group by ID with all subjects and marks.

**Request Example:**
```bash
curl -X GET "http://localhost/amt/api/Subjectgroup_api/get_subject_group/2" \
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
            "group_subject": [
                {
                    "id": "1",
                    "subject_id": "1",
                    "subject_name": "Mathematics",
                    "subject_code": "MATH",
                    "minmarks": "0",
                    "maxmarks": "100"
                },
                {
                    "id": "2",
                    "subject_id": "2",
                    "subject_name": "Physics",
                    "subject_code": "PHY",
                    "minmarks": "0",
                    "maxmarks": "100"
                }
            ]
        }
    ]
}
```

**Response (Error - Not Found):**
```json
{
    "status": 404,
    "message": "Subject group not found."
}
```

---

### 5. Create Subject Group
**Method:** `POST`  
**URL:** `create_subject_group`

**Description:**  
Creates a new subject group with subjects and min/max marks.

**Request Body:**
```json
{
    "result_type_id": "2",
    "subjects": [
        {
            "subject_id": "1",
            "minmarks": "0",
            "maxmarks": "100"
        },
        {
            "subject_id": "2",
            "minmarks": "0",
            "maxmarks": "100"
        }
    ]
}
```

**Request Body Parameters:**
- `result_type_id` (required): Result type/exam type ID
- `subjects` (required): Array of subject objects
  - `subject_id` (required): Subject ID
  - `minmarks` (optional): Minimum marks (default: 0)
  - `maxmarks` (optional): Maximum marks (default: 100)

**Request Example:**
```bash
curl -X POST "http://localhost/amt/api/Subjectgroup_api/create_subject_group" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -H "Content-Type: application/json" \
  -d '{
    "result_type_id": "2",
    "subjects": [
        {"subject_id": "1", "minmarks": "0", "maxmarks": "100"},
        {"subject_id": "2", "minmarks": "0", "maxmarks": "100"}
    ]
  }'
```

**Response (Success):**
```json
{
    "status": 201,
    "message": "Subject group created successfully.",
    "data": {
        "result_type_id": "2",
        "subjects_count": 2
    }
}
```

**Response (Error - Missing Parameters):**
```json
{
    "status": 400,
    "message": "Result type ID is required."
}
```

---

### 6. Update Subject Group
**Method:** `PUT` or `POST`  
**URL:** `update_subject_group/:id`

**Description:**  
Updates an existing subject group by adding/removing subjects and updating marks.

**Request Body:**
```json
{
    "subjects": [
        {
            "subject_id": "1",
            "minmarks": "0",
            "maxmarks": "100"
        },
        {
            "subject_id": "3",
            "minmarks": "0",
            "maxmarks": "50"
        }
    ]
}
```

**Request Example:**
```bash
curl -X PUT "http://localhost/amt/api/Subjectgroup_api/update_subject_group/2" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -H "Content-Type: application/json" \
  -d '{
    "subjects": [
        {"subject_id": "1", "minmarks": "0", "maxmarks": "100"},
        {"subject_id": "3", "minmarks": "0", "maxmarks": "50"}
    ]
  }'
```

**Response (Success):**
```json
{
    "status": 200,
    "message": "Subject group updated successfully.",
    "data": {
        "id": "2",
        "subjects_added": 1,
        "subjects_removed": 1
    }
}
```

**Response (Error - Not Found):**
```json
{
    "status": 404,
    "message": "Subject group not found."
}
```

---

### 7. Delete Subject Group
**Method:** `DELETE` or `POST`  
**URL:** `delete_subject_group/:id`

**Description:**  
Deletes a subject group and all associated subject mappings.

**Request Example:**
```bash
curl -X DELETE "http://localhost/amt/api/Subjectgroup_api/delete_subject_group/2" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@"
```

**Response (Success):**
```json
{
    "status": 200,
    "message": "Subject group deleted successfully.",
    "data": {
        "id": "2"
    }
}
```

**Response (Error - Not Found):**
```json
{
    "status": 404,
    "message": "Subject group not found."
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
$base_url = 'http://localhost/amt/api/Subjectgroup_api/';
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
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    if (($method === 'POST' || $method === 'PUT') && $data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

// Get result types
$result_types = makeApiRequest('get_result_types');

// Get subjects
$subjects = makeApiRequest('get_subjects');

// Get all subject groups
$subject_groups = makeApiRequest('get_subject_groups');

// Get specific subject group
$subject_group = makeApiRequest('get_subject_group/2');

// Create subject group
$create_data = array(
    'result_type_id' => '2',
    'subjects' => array(
        array('subject_id' => '1', 'minmarks' => '0', 'maxmarks' => '100'),
        array('subject_id' => '2', 'minmarks' => '0', 'maxmarks' => '100')
    )
);
$created = makeApiRequest('create_subject_group', 'POST', $create_data);

// Update subject group
$update_data = array(
    'subjects' => array(
        array('subject_id' => '1', 'minmarks' => '0', 'maxmarks' => '100'),
        array('subject_id' => '3', 'minmarks' => '0', 'maxmarks' => '50')
    )
);
$updated = makeApiRequest('update_subject_group/2', 'PUT', $update_data);

// Delete subject group
$deleted = makeApiRequest('delete_subject_group/2', 'DELETE');
?>
```

---

## Integration Example (JavaScript)

```javascript
const baseUrl = 'http://localhost/amt/api/Subjectgroup_api/';
const headers = {
    'Client-Service': 'smartschool',
    'Auth-Key': 'schoolAdmin@',
    'Content-Type': 'application/json'
};

async function makeApiRequest(endpoint, method = 'GET', data = null) {
    const options = {
        method: method,
        headers: headers
    };
    
    if ((method === 'POST' || method === 'PUT') && data) {
        options.body = JSON.stringify(data);
    }
    
    try {
        const response = await fetch(baseUrl + endpoint, options);
        return await response.json();
    } catch (error) {
        console.error('API Error:', error);
        return null;
    }
}

// Get result types
const resultTypes = await makeApiRequest('get_result_types');

// Create subject group
const createData = {
    result_type_id: '2',
    subjects: [
        { subject_id: '1', minmarks: '0', maxmarks: '100' },
        { subject_id: '2', minmarks: '0', maxmarks: '100' }
    ]
};
const created = await makeApiRequest('create_subject_group', 'POST', createData);

// Update subject group
const updateData = {
    subjects: [
        { subject_id: '1', minmarks: '0', maxmarks: '100' },
        { subject_id: '3', minmarks: '0', maxmarks: '50' }
    ]
};
const updated = await makeApiRequest('update_subject_group/2', 'PUT', updateData);

// Delete subject group
const deleted = await makeApiRequest('delete_subject_group/2', 'DELETE');
```

---

## Database Tables

- `publicexamtype` - Result types (exam types)
- `resultsubjects` - Subjects
- `publicresultsubject_group_subjects` - Subject group mapping with min/max marks

---

## Notes

1. **Authentication**: All endpoints require valid headers
2. **HTTP Methods**: Use correct method (GET, POST, PUT, DELETE)
3. **JSON Format**: Ensure valid JSON for POST/PUT requests
4. **Subject Validation**: Subject IDs must exist in the database
5. **Marks Range**: Min marks should be less than max marks
