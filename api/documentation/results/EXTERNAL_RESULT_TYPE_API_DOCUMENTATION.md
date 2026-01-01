# External Result Type API Documentation

## Overview
The External Result Type API provides endpoints for managing external result types (e.g., Public Exam, Board Exam, etc.). It allows retrieving external result types list, specific result type details, and performing CRUD operations.

## Base URL
`http://localhost/amt/api/Publicexamtype_api/`

*Note: Replace `localhost/amt` with your actual domain when deploying to production.*

## Authentication
All requests must include the following headers:
- `Client-Service`: `smartschool`
- `Auth-Key`: `schoolAdmin@`

## Endpoints

### 1. Get All External Result Types
**Method:** `GET`  
**URL:** `get_public_exam_types`

**Description:**  
Retrieves all external result types in the system.

**Request Example:**
```bash
curl -X GET "http://localhost/amt/api/Publicexamtype_api/get_public_exam_types" \
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
            "examtype": "IPE March 2024",
            "session_id": "18",
            "is_active": "no"
        },
        {
            "id": "2",
            "examtype": "JEE Mains 2024",
            "session_id": "20",
            "is_active": "yes"
        }
    ]
}
```

---

### 2. Get Specific External Result Type
**Method:** `GET`  
**URL:** `get_public_exam_type/:id`

**Description:**  
Retrieves a specific external result type by ID.

**Request Example:**
```bash
curl -X GET "http://localhost/amt/api/Publicexamtype_api/get_public_exam_type/1" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@"
```

**Response (Success):**
```json
{
    "status": 200,
    "message": "Success",
    "data": {
        "id": "1",
        "examtype": "IPE March 2024",
        "session_id": "18",
        "is_active": "no"
    }
}
```

**Response (Error - Not Found):**
```json
{
    "status": 404,
    "message": "External result type not found."
}
```

---

### 3. Create External Result Type
**Method:** `POST`  
**URL:** `create_public_exam_type`

**Description:**  
Creates a new external result type.

**Request Body:**
```json
{
    "examtype": "EAMCET 2024",
    "session_id": "20"
}
```

**Request Body Parameters:**
- `examtype` (required): External result type name
- `session_id` (optional): Session ID (defaults to current session if not provided)

**Request Example:**
```bash
curl -X POST "http://localhost/amt/api/Publicexamtype_api/create_public_exam_type" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -H "Content-Type: application/json" \
  -d '{
    "examtype": "EAMCET 2024",
    "session_id": "20"
  }'
```

**Response (Success):**
```json
{
    "status": 201,
    "message": "External result type created successfully.",
    "data": {
        "id": "3",
        "examtype": "EAMCET 2024",
        "session_id": "20"
    }
}
```

**Response (Error - Missing Name):**
```json
{
    "status": 400,
    "message": "External result type name is required."
}
```

---

### 4. Update External Result Type
**Method:** `PUT` or `POST`  
**URL:** `update_public_exam_type/:id`

**Description:**  
Updates an existing external result type's name, session, and active status.

**Request Body:**
```json
{
    "examtype": "EAMCET 2024 - Updated",
    "session_id": "20",
    "is_active": "yes"
}
```

**Request Body Parameters:**
- `examtype` (required): External result type name
- `session_id` (optional): Session ID
- `is_active` (optional): Active status ("yes" or "no")

**Request Example:**
```bash
curl -X PUT "http://localhost/amt/api/Publicexamtype_api/update_public_exam_type/3" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -H "Content-Type: application/json" \
  -d '{
    "examtype": "EAMCET 2024 - Updated",
    "session_id": "20",
    "is_active": "yes"
  }'
```

**Response (Success):**
```json
{
    "status": 200,
    "message": "External result type updated successfully.",
    "data": {
        "id": "3",
        "examtype": "EAMCET 2024 - Updated",
        "session_id": "20",
        "is_active": "yes"
    }
}
```

**Response (Error - Not Found):**
```json
{
    "status": 404,
    "message": "External result type not found."
}
```

---

### 5. Delete External Result Type
**Method:** `DELETE` or `POST`  
**URL:** `delete_public_exam_type/:id`

**Description:**  
Deletes an external result type from the system.

**Request Example:**
```bash
curl -X DELETE "http://localhost/amt/api/Publicexamtype_api/delete_public_exam_type/3" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@"
```

**Response (Success):**
```json
{
    "status": 200,
    "message": "External result type deleted successfully.",
    "data": {
        "id": "3"
    }
}
```

**Response (Error - Not Found):**
```json
{
    "status": 404,
    "message": "External result type not found."
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
$base_url = 'http://localhost/amt/api/Publicexamtype_api/';
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

// Get all external result types
$exam_types = makeApiRequest('get_public_exam_types');

// Get specific external result type
$exam_type = makeApiRequest('get_public_exam_type/1');

// Create external result type
$create_data = array(
    'examtype' => 'EAMCET 2024',
    'session_id' => '20'
);
$created = makeApiRequest('create_public_exam_type', 'POST', $create_data);

// Update external result type
$update_data = array(
    'examtype' => 'EAMCET 2024 - Updated',
    'session_id' => '20',
    'is_active' => 'yes'
);
$updated = makeApiRequest('update_public_exam_type/3', 'PUT', $update_data);

// Delete external result type
$deleted = makeApiRequest('delete_public_exam_type/3', 'DELETE');
?>
```

---

## Integration Example (JavaScript)

```javascript
const baseUrl = 'http://localhost/amt/api/Publicexamtype_api/';
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

// Get all external result types
const examTypes = await makeApiRequest('get_public_exam_types');

// Create external result type
const createData = {
    examtype: 'EAMCET 2024',
    session_id: '20'
};
const created = await makeApiRequest('create_public_exam_type', 'POST', createData);

// Update external result type
const updateData = {
    examtype: 'EAMCET 2024 - Updated',
    session_id: '20',
    is_active: 'yes'
};
const updated = await makeApiRequest('update_public_exam_type/3', 'PUT', updateData);

// Delete external result type
const deleted = await makeApiRequest('delete_public_exam_type/3', 'DELETE');
```

---

## Database Table

### `publicexamtype`
- `id`: Primary key
- `examtype`: External result type name
- `session_id`: Academic session ID
- `is_active`: Active status ("yes" or "no")

---

## Notes

1. **Authentication**: All endpoints require valid headers
2. **HTTP Methods**: Use correct method (GET, POST, PUT, DELETE)
3. **JSON Format**: Ensure valid JSON for POST/PUT requests
4. **Session ID**: Defaults to current session if not provided
5. **Active Status**: New external result types are created with `is_active = "no"` by default
