# Result Subject API Documentation

## Overview
The Result Subject API provides endpoints for managing subjects specifically for the results module (e.g., MATHS-1A, PHYSICS, etc.). It allows retrieving subject list, specific subject details, and performing CRUD operations.

## Base URL
`http://localhost/amt/api/Resultsubject_api/`

*Note: Replace `localhost/amt` with your actual domain when deploying to production.*

## Authentication
All requests must include the following headers:
- `Client-Service`: `smartschool`
- `Auth-Key`: `schoolAdmin@`

## Endpoints

### 1. Get All Result Subjects
**Method:** `GET`  
**URL:** `get_result_subjects`

**Description:**  
Retrieves all result subjects in the system.

**Request Example:**
```bash
curl -X GET "http://localhost/amt/api/Resultsubject_api/get_result_subjects" \
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
            "subject_name": "MATHS-1A",
            "subject_code": "31"
        },
        {
            "id": "2",
            "subject_name": "PHYSICS",
            "subject_code": "41"
        }
    ]
}
```

---

### 2. Get Specific Result Subject
**Method:** `GET`  
**URL:** `get_result_subject/:id`

**Description:**  
Retrieves a specific result subject by ID.

**Request Example:**
```bash
curl -X GET "http://localhost/amt/api/Resultsubject_api/get_result_subject/1" \
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
        "subject_name": "MATHS-1A",
        "subject_code": "31"
    }
}
```

**Response (Error - Not Found):**
```json
{
    "status": 404,
    "message": "Result subject not found."
}
```

---

### 3. Create Result Subject
**Method:** `POST`  
**URL:** `create_result_subject`

**Description:**  
Creates a new result subject.

**Request Body:**
```json
{
    "subject_name": "CHEMISTRY",
    "subject_code": "42"
}
```

**Request Body Parameters:**
- `subject_name` (required): Subject name
- `subject_code` (required): Subject code (must be unique)

**Request Example:**
```bash
curl -X POST "http://localhost/amt/api/Resultsubject_api/create_result_subject" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -H "Content-Type: application/json" \
  -d '{
    "subject_name": "CHEMISTRY",
    "subject_code": "42"
  }'
```

**Response (Success):**
```json
{
    "status": 201,
    "message": "Result subject created successfully.",
    "data": {
        "id": "3",
        "subject_name": "CHEMISTRY",
        "subject_code": "42"
    }
}
```

**Response (Error - Duplicate Code):**
```json
{
    "status": 400,
    "message": "Subject code already exists."
}
```

---

### 4. Update Result Subject
**Method:** `PUT` or `POST`  
**URL:** `update_result_subject/:id`

**Description:**  
Updates an existing result subject's name and code.

**Request Body:**
```json
{
    "subject_name": "CHEMISTRY - Updated",
    "subject_code": "42"
}
```

**Request Body Parameters:**
- `subject_name` (required): Subject name
- `subject_code` (required): Subject code

**Request Example:**
```bash
curl -X PUT "http://localhost/amt/api/Resultsubject_api/update_result_subject/3" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -H "Content-Type: application/json" \
  -d '{
    "subject_name": "CHEMISTRY - Updated",
    "subject_code": "42"
  }'
```

**Response (Success):**
```json
{
    "status": 200,
    "message": "Result subject updated successfully.",
    "data": {
        "id": "3",
        "subject_name": "CHEMISTRY - Updated",
        "subject_code": "42"
    }
}
```

**Response (Error - Not Found):**
```json
{
    "status": 404,
    "message": "Result subject not found."
}
```

---

### 5. Delete Result Subject
**Method:** `DELETE` or `POST`  
**URL:** `delete_result_subject/:id`

**Description:**  
Deletes a result subject from the system.

**Request Example:**
```bash
curl -X DELETE "http://localhost/amt/api/Resultsubject_api/delete_result_subject/3" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@"
```

**Response (Success):**
```json
{
    "status": 200,
    "message": "Result subject deleted successfully.",
    "data": {
        "id": "3"
    }
}
```

**Response (Error - Not Found):**
```json
{
    "status": 404,
    "message": "Result subject not found."
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
$base_url = 'http://localhost/amt/api/Resultsubject_api/';
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

// Get all result subjects
$subjects = makeApiRequest('get_result_subjects');

// Get specific result subject
$subject = makeApiRequest('get_result_subject/1');

// Create result subject
$create_data = array(
    'subject_name' => 'CHEMISTRY',
    'subject_code' => '42'
);
$created = makeApiRequest('create_result_subject', 'POST', $create_data);

// Update result subject
$update_data = array(
    'subject_name' => 'CHEMISTRY - Updated',
    'subject_code' => '42'
);
$updated = makeApiRequest('update_result_subject/3', 'PUT', $update_data);

// Delete result subject
$deleted = makeApiRequest('delete_result_subject/3', 'DELETE');
?>
```

---

## Integration Example (JavaScript)

```javascript
const baseUrl = 'http://localhost/amt/api/Resultsubject_api/';
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

// Get all result subjects
const subjects = await makeApiRequest('get_result_subjects');

// Create result subject
const createData = {
    subject_name: 'CHEMISTRY',
    subject_code: '42'
};
const created = await makeApiRequest('create_result_subject', 'POST', createData);

// Update result subject
const updateData = {
    subject_name: 'CHEMISTRY - Updated',
    subject_code: '42'
};
const updated = await makeApiRequest('update_result_subject/3', 'PUT', updateData);

// Delete result subject
const deleted = await makeApiRequest('delete_result_subject/3', 'DELETE');
```

---

## Database Table

### `resultsubjects`
- `id`: Primary key
- `examtype`: Subject name (mapped to `subject_name` in API)
- `subject_code`: Subject code

---

## Notes

1. **Authentication**: All endpoints require valid headers
2. **HTTP Methods**: Use correct method (GET, POST, PUT, DELETE)
3. **JSON Format**: Ensure valid JSON for POST/PUT requests
4. **Field Mapping**: API uses `subject_name` which maps to `examtype` column in database
5. **Validation**: Subject code must be unique
