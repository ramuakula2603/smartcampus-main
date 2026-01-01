# Subject API Documentation

## Overview
The Subject API provides endpoints for managing individual subjects with name, code, and type (Theory/Practical). It allows retrieving subjects list, specific subject details, and performing CRUD operations.

## Base URL
`http://localhost/amt/api/Subject_api/`

*Note: Replace `localhost/amt` with your actual domain when deploying to production.*

## Authentication
All requests must include the following headers:
- `Client-Service`: `smartschool`
- `Auth-Key`: `schoolAdmin@`

## Endpoints

### 1. Get All Subjects
**Method:** `GET`  
**URL:** `get_subjects`

**Description:**  
Retrieves all subjects in the system.

**Request Example:**
```bash
curl -X GET "http://localhost/amt/api/Subject_api/get_subjects" \
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
            "name": "MATHS-IA",
            "code": "JR-MATHS-1A",
            "type": "Theory"
        },
        {
            "id": "2",
            "name": "PHYSICS",
            "code": "JR-PHYSICS",
            "type": "Theory"
        },
        {
            "id": "3",
            "name": "CHEMISTRY LAB",
            "code": "JR-CHEM-LAB",
            "type": "Practical"
        }
    ]
}
```

---

### 2. Get Specific Subject
**Method:** `GET`  
**URL:** `get_subject/:id`

**Description:**  
Retrieves a specific subject by ID.

**Request Example:**
```bash
curl -X GET "http://localhost/amt/api/Subject_api/get_subject/1" \
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
        "name": "MATHS-IA",
        "code": "JR-MATHS-1A",
        "type": "Theory"
    }
}
```

**Response (Error - Not Found):**
```json
{
    "status": 404,
    "message": "Subject not found."
}
```

---

### 3. Create Subject
**Method:** `POST`  
**URL:** `create_subject`

**Description:**  
Creates a new subject with name, code, and type.

**Request Body:**
```json
{
    "name": "BIOLOGY",
    "code": "JR-BIO",
    "type": "Theory"
}
```

**Request Body Parameters:**
- `name` (required): Subject name (must be unique)
- `code` (optional): Subject code (must be unique if provided)
- `type` (required): Subject type - "Theory" or "Practical"

**Request Example:**
```bash
curl -X POST "http://localhost/amt/api/Subject_api/create_subject" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "BIOLOGY",
    "code": "JR-BIO",
    "type": "Theory"
  }'
```

**Response (Success):**
```json
{
    "status": 201,
    "message": "Subject created successfully.",
    "data": {
        "id": "4",
        "name": "BIOLOGY",
        "code": "JR-BIO",
        "type": "Theory"
    }
}
```

**Response (Error - Duplicate Name):**
```json
{
    "status": 400,
    "message": "Subject name already exists."
}
```

**Response (Error - Invalid Type):**
```json
{
    "status": 400,
    "message": "Subject type must be either \"Theory\" or \"Practical\"."
}
```

---

### 4. Update Subject
**Method:** `PUT` or `POST`  
**URL:** `update_subject/:id`

**Description:**  
Updates an existing subject's name, code, and type.

**Request Body:**
```json
{
    "name": "BIOLOGY - UPDATED",
    "code": "JR-BIO-NEW",
    "type": "Practical"
}
```

**Request Example:**
```bash
curl -X PUT "http://localhost/amt/api/Subject_api/update_subject/4" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "BIOLOGY - UPDATED",
    "code": "JR-BIO-NEW",
    "type": "Practical"
  }'
```

**Response (Success):**
```json
{
    "status": 200,
    "message": "Subject updated successfully.",
    "data": {
        "id": "4",
        "name": "BIOLOGY - UPDATED",
        "code": "JR-BIO-NEW",
        "type": "Practical"
    }
}
```

**Response (Error - Not Found):**
```json
{
    "status": 404,
    "message": "Subject not found."
}
```

---

### 5. Delete Subject
**Method:** `DELETE` or `POST`  
**URL:** `delete_subject/:id`

**Description:**  
Deletes a subject from the system.

**Request Example:**
```bash
curl -X DELETE "http://localhost/amt/api/Subject_api/delete_subject/4" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@"
```

**Response (Success):**
```json
{
    "status": 200,
    "message": "Subject deleted successfully.",
    "data": {
        "id": "4"
    }
}
```

**Response (Error - Not Found):**
```json
{
    "status": 404,
    "message": "Subject not found."
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
$base_url = 'http://localhost/amt/api/Subject_api/';
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

// Get all subjects
$subjects = makeApiRequest('get_subjects');

// Get specific subject
$subject = makeApiRequest('get_subject/1');

// Create subject
$create_data = array(
    'name' => 'BIOLOGY',
    'code' => 'JR-BIO',
    'type' => 'Theory'
);
$created = makeApiRequest('create_subject', 'POST', $create_data);

// Update subject
$update_data = array(
    'name' => 'BIOLOGY - UPDATED',
    'code' => 'JR-BIO-NEW',
    'type' => 'Practical'
);
$updated = makeApiRequest('update_subject/4', 'PUT', $update_data);

// Delete subject
$deleted = makeApiRequest('delete_subject/4', 'DELETE');
?>
```

---

## Integration Example (JavaScript)

```javascript
const baseUrl = 'http://localhost/amt/api/Subject_api/';
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

// Get all subjects
const subjects = await makeApiRequest('get_subjects');

// Create subject
const createData = {
    name: 'BIOLOGY',
    code: 'JR-BIO',
    type: 'Theory'
};
const created = await makeApiRequest('create_subject', 'POST', createData);

// Update subject
const updateData = {
    name: 'BIOLOGY - UPDATED',
    code: 'JR-BIO-NEW',
    type: 'Practical'
};
const updated = await makeApiRequest('update_subject/4', 'PUT', updateData);

// Delete subject
const deleted = await makeApiRequest('delete_subject/4', 'DELETE');
```

---

## Database Table

### `subjects`
- `id`: Primary key
- `name`: Subject name (unique)
- `code`: Subject code (unique, optional)
- `type`: Subject type ("Theory" or "Practical")

---

## Validation Rules

1. **Subject Name**: Required, must be unique
2. **Subject Code**: Optional, must be unique if provided
3. **Subject Type**: Required, must be either "Theory" or "Practical"

---

## Notes

1. **Authentication**: All endpoints require valid headers
2. **HTTP Methods**: Use correct method (GET, POST, PUT, DELETE)
3. **JSON Format**: Ensure valid JSON for POST/PUT requests
4. **Uniqueness**: Subject names and codes must be unique
5. **Type Values**: Only "Theory" and "Practical" are accepted
