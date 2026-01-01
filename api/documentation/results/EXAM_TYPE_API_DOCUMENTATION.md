# Exam Type API Documentation

## Overview
The Exam Type API provides endpoints for managing exam types (e.g., Mid-1, Final Exam, etc.). It allows retrieving exam types list, specific exam type details, and performing CRUD operations.

## Base URL
`http://localhost/amt/api/Examtype_api/`

*Note: Replace `localhost/amt` with your actual domain when deploying to production.*

## Authentication
All requests must include the following headers:
- `Client-Service`: `smartschool`
- `Auth-Key`: `schoolAdmin@`

## Endpoints

### 1. Get All Exam Types
**Method:** `GET`  
**URL:** `get_exam_types`

**Description:**  
Retrieves all exam types in the system.

**Request Example:**
```bash
curl -X GET "http://localhost/amt/api/Examtype_api/get_exam_types" \
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
            "id": "3",
            "examtype": "mid-1",
            "session_id": "18",
            "is_active": "no",
            "created_at": "2023-10-28 01:44:40",
            "updated_at": null
        },
        {
            "id": "4",
            "examtype": "Final Exam",
            "session_id": "20",
            "is_active": "yes",
            "created_at": "2024-01-15 10:00:00",
            "updated_at": null
        }
    ]
}
```

---

### 2. Get Specific Exam Type
**Method:** `GET`  
**URL:** `get_exam_type/:id`

**Description:**  
Retrieves a specific exam type by ID.

**Request Example:**
```bash
curl -X GET "http://localhost/amt/api/Examtype_api/get_exam_type/3" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@"
```

**Response (Success):**
```json
{
    "status": 200,
    "message": "Success",
    "data": {
        "id": "3",
        "examtype": "mid-1",
        "session_id": "18",
        "is_active": "no",
        "created_at": "2023-10-28 01:44:40",
        "updated_at": null
    }
}
```

**Response (Error - Not Found):**
```json
{
    "status": 404,
    "message": "Exam type not found."
}
```

---

### 3. Create Exam Type
**Method:** `POST`  
**URL:** `create_exam_type`

**Description:**  
Creates a new exam type.

**Request Body:**
```json
{
    "examtype": "Quarterly Exam",
    "session_id": "20"
}
```

**Request Body Parameters:**
- `examtype` (required): Exam type name
- `session_id` (optional): Session ID (defaults to current session if not provided)

**Request Example:**
```bash
curl -X POST "http://localhost/amt/api/Examtype_api/create_exam_type" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -H "Content-Type: application/json" \
  -d '{
    "examtype": "Quarterly Exam",
    "session_id": "20"
  }'
```

**Response (Success):**
```json
{
    "status": 201,
    "message": "Exam type created successfully.",
    "data": {
        "examtype": "Quarterly Exam",
        "session_id": "20"
    }
}
```

**Response (Error - Missing Name):**
```json
{
    "status": 400,
    "message": "Exam type name is required."
}
```

---

### 4. Update Exam Type
**Method:** `PUT` or `POST`  
**URL:** `update_exam_type/:id`

**Description:**  
Updates an existing exam type's name, session, and active status.

**Request Body:**
```json
{
    "examtype": "Quarterly Exam - Updated",
    "session_id": "20",
    "is_active": "yes"
}
```

**Request Body Parameters:**
- `examtype` (required): Exam type name
- `session_id` (optional): Session ID
- `is_active` (optional): Active status ("yes" or "no")

**Request Example:**
```bash
curl -X PUT "http://localhost/amt/api/Examtype_api/update_exam_type/5" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -H "Content-Type: application/json" \
  -d '{
    "examtype": "Quarterly Exam - Updated",
    "session_id": "20",
    "is_active": "yes"
  }'
```

**Response (Success):**
```json
{
    "status": 200,
    "message": "Exam type updated successfully.",
    "data": {
        "id": "5",
        "examtype": "Quarterly Exam - Updated",
        "session_id": "20",
        "is_active": "yes"
    }
}
```

**Response (Error - Not Found):**
```json
{
    "status": 404,
    "message": "Exam type not found."
}
```

---

### 5. Delete Exam Type
**Method:** `DELETE` or `POST`  
**URL:** `delete_exam_type/:id`

**Description:**  
Deletes an exam type from the system.

**Request Example:**
```bash
curl -X DELETE "http://localhost/amt/api/Examtype_api/delete_exam_type/5" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@"
```

**Response (Success):**
```json
{
    "status": 200,
    "message": "Exam type deleted successfully.",
    "data": {
        "id": "5"
    }
}
```

**Response (Error - Not Found):**
```json
{
    "status": 404,
    "message": "Exam type not found."
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
$base_url = 'http://localhost/amt/api/Examtype_api/';
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

// Get all exam types
$exam_types = makeApiRequest('get_exam_types');

// Get specific exam type
$exam_type = makeApiRequest('get_exam_type/3');

// Create exam type
$create_data = array(
    'examtype' => 'Quarterly Exam',
    'session_id' => '20'
);
$created = makeApiRequest('create_exam_type', 'POST', $create_data);

// Update exam type
$update_data = array(
    'examtype' => 'Quarterly Exam - Updated',
    'session_id' => '20',
    'is_active' => 'yes'
);
$updated = makeApiRequest('update_exam_type/5', 'PUT', $update_data);

// Delete exam type
$deleted = makeApiRequest('delete_exam_type/5', 'DELETE');
?>
```

---

## Integration Example (JavaScript)

```javascript
const baseUrl = 'http://localhost/amt/api/Examtype_api/';
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

// Get all exam types
const examTypes = await makeApiRequest('get_exam_types');

// Create exam type
const createData = {
    examtype: 'Quarterly Exam',
    session_id: '20'
};
const created = await makeApiRequest('create_exam_type', 'POST', createData);

// Update exam type
const updateData = {
    examtype: 'Quarterly Exam - Updated',
    session_id: '20',
    is_active: 'yes'
};
const updated = await makeApiRequest('update_exam_type/5', 'PUT', updateData);

// Delete exam type
const deleted = await makeApiRequest('delete_exam_type/5', 'DELETE');
```

---

## Database Table

### `examtype`
- `id`: Primary key
- `examtype`: Exam type name
- `session_id`: Academic session ID
- `is_active`: Active status ("yes" or "no")
- `created_at`: Creation timestamp
- `updated_at`: Update timestamp

---

## Notes

1. **Authentication**: All endpoints require valid headers
2. **HTTP Methods**: Use correct method (GET, POST, PUT, DELETE)
3. **JSON Format**: Ensure valid JSON for POST/PUT requests
4. **Session ID**: Defaults to current session if not provided
5. **Active Status**: New exam types are created with `is_active = "no"` by default
