# Behavioral Module - Student Behaviour Types API Documentation

## Overview

The Behavioral Module - Student Behaviour Types API provides functionality for managing student behaviour types in the school management system. This API allows creating, retrieving, updating, and deleting behaviour types that can be assigned to students as incidents.

---

## Base URL
```
http://{domain}/api/
```

## Important URL Structure

**For Behavioral Module - Student Behaviour Types APIs, use the controller/method pattern:**
- List all behaviour types: `http://{domain}/api/studentbehaviour/list`
- Get behaviour type by ID: `http://{domain}/api/studentbehaviour/get`
- Create new behaviour type: `http://{domain}/api/studentbehaviour/create`
- Update behaviour type: `http://{domain}/api/studentbehaviour/update`
- Delete behaviour type: `http://{domain}/api/studentbehaviour/delete`

**Examples:**
- List behaviour types: `http://localhost/amt/api/studentbehaviour/list`
- Get behaviour type: `http://localhost/amt/api/studentbehaviour/get`

## Authentication Headers

All API requests require the following headers:
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

---

## Endpoints

### 1. List Student Behaviour Types

**Endpoint:** `POST /studentbehaviour/list`
**Full URL:** `http://localhost/amt/api/studentbehaviour/list`

**Description:** Retrieve all student behaviour types with optional pagination.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### Request Body
```json
{
    "limit": 10,
    "offset": 0
}
```

#### Success Response (HTTP 200)
```json
{
    "status": 1,
    "data": [
        {
            "id": "1",
            "title": "Good Behavior",
            "point": "5",
            "description": "Student showed excellent behavior in class",
            "created_at": "2025-11-15 10:30:00"
        }
    ],
    "total_count": 15,
    "message": "Student behaviour types retrieved successfully"
}
```

#### Error Response (HTTP 500)
```json
{
    "status": 0,
    "message": "Internal server error: [error details]"
}
```

#### cURL Example
```bash
curl -X POST "http://localhost/amt/api/studentbehaviour/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "limit": 10,
    "offset": 0
  }'
```

---

### 2. Get Student Behaviour Type by ID

**Endpoint:** `POST /studentbehaviour/get`
**Full URL:** `http://localhost/amt/api/studentbehaviour/get`

**Description:** Retrieve a specific student behaviour type by ID.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### Request Body
```json
{
    "id": 1
}
```

#### Success Response (HTTP 200)
```json
{
    "status": 1,
    "data": {
        "id": "1",
        "title": "Good Behavior",
        "point": "5",
        "description": "Student showed excellent behavior in class",
        "created_at": "2025-11-15 10:30:00"
    },
    "message": "Student behaviour type retrieved successfully"
}
```

#### Error Response (HTTP 400)
```json
{
    "status": 0,
    "message": "Behaviour ID is required"
}
```

#### Error Response (HTTP 404)
```json
{
    "status": 0,
    "message": "Student behaviour type not found"
}
```

#### cURL Example
```bash
curl -X POST "http://localhost/amt/api/studentbehaviour/get" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "id": 1
  }'
```

---

### 3. Create Student Behaviour Type

**Endpoint:** `POST /studentbehaviour/create`
**Full URL:** `http://localhost/amt/api/studentbehaviour/create`

**Description:** Create a new student behaviour type.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### Request Body
```json
{
    "title": "Good Behavior",
    "point": 5,
    "description": "Student showed excellent behavior in class"
}
```

#### Success Response (HTTP 200)
```json
{
    "status": 1,
    "data": {
        "id": 45
    },
    "message": "Student behaviour type created successfully"
}
```

#### Error Response (HTTP 400)
```json
{
    "status": 0,
    "message": "Title is required"
}
```

#### Error Response (HTTP 400)
```json
{
    "status": 0,
    "message": "Point must be a number"
}
```

#### cURL Example
```bash
curl -X POST "http://localhost/amt/api/studentbehaviour/create" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "title": "Good Behavior",
    "point": 5,
    "description": "Student showed excellent behavior in class"
  }'
```

---

### 4. Update Student Behaviour Type

**Endpoint:** `POST /studentbehaviour/update`
**Full URL:** `http://localhost/amt/api/studentbehaviour/update`

**Description:** Update an existing student behaviour type.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### Request Body
```json
{
    "id": 1,
    "title": "Excellent Behavior",
    "point": 10,
    "description": "Student showed outstanding behavior in class"
}
```

#### Success Response (HTTP 200)
```json
{
    "status": 1,
    "message": "Student behaviour type updated successfully"
}
```

#### Error Response (HTTP 400)
```json
{
    "status": 0,
    "message": "Behaviour ID is required"
}
```

#### Error Response (HTTP 400)
```json
{
    "status": 0,
    "message": "Title is required"
}
```

#### Error Response (HTTP 400)
```json
{
    "status": 0,
    "message": "Point must be a number"
}
```

#### cURL Example
```bash
curl -X POST "http://localhost/amt/api/studentbehaviour/update" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "id": 1,
    "title": "Excellent Behavior",
    "point": 10,
    "description": "Student showed outstanding behavior in class"
  }'
```

---

### 5. Delete Student Behaviour Type

**Endpoint:** `POST /studentbehaviour/delete`
**Full URL:** `http://localhost/amt/api/studentbehaviour/delete`

**Description:** Delete a specific student behaviour type by ID. Note that behaviour types that are already assigned to incidents cannot be deleted.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### Request Body
```json
{
    "id": 1
}
```

#### Success Response (HTTP 200)
```json
{
    "status": 1,
    "message": "Student behaviour type deleted successfully"
}
```

#### Error Response (HTTP 400)
```json
{
    "status": 0,
    "message": "Behaviour ID is required"
}
```

#### Error Response (HTTP 400)
```json
{
    "status": 0,
    "message": "Cannot delete behaviour type. It is used in [count] incident(s)."
}
```

#### cURL Example
```bash
curl -X POST "http://localhost/amt/api/studentbehaviour/delete" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "id": 1
  }'
```

---

## Request Parameters

### List Behaviour Types
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| limit | integer | No | Number of records to retrieve (for pagination) |
| offset | integer | No | Offset for pagination |

### Get Behaviour Type
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | integer | Yes | Behaviour type ID |

### Create Behaviour Type
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| title | string | Yes | Behaviour type title |
| point | integer | Yes | Points value (can be positive or negative) |
| description | string | No | Behaviour type description |

### Update Behaviour Type
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | integer | Yes | Behaviour type ID |
| title | string | Yes | Behaviour type title |
| point | integer | Yes | Points value (can be positive or negative) |
| description | string | No | Behaviour type description |

### Delete Behaviour Type
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | integer | Yes | Behaviour type ID |

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
| 404 | Not found |
| 500 | Internal server error |

---

## Testing

Use the provided cURL examples to test the endpoints. Make sure to:

1. Replace `localhost/amt` with your actual domain
2. Ensure the authentication headers are correct
3. Verify that the IDs exist in the respective tables
4. Check that you have proper permissions to access the data

### Test Cases

#### Test Case 1: Create Behaviour Type
This test case demonstrates creating a new behaviour type.

**Request:**
```bash
curl -X POST "http://localhost/amt/api/studentbehaviour/create" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "title": "Test Behaviour",
    "point": 5,
    "description": "This is a test behaviour type"
  }'
```

**Expected Response (HTTP 200):**
```json
{
    "status": 1,
    "data": {
        "id": 45
    },
    "message": "Student behaviour type created successfully"
}
```

#### Test Case 2: List Behaviour Types
This test case demonstrates retrieving all behaviour types.

**Request:**
```bash
curl -X POST "http://localhost/amt/api/studentbehaviour/list" \
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
            "title": "Good Behavior",
            "point": "5",
            "description": "Student showed excellent behavior in class",
            "created_at": "2025-11-15 10:30:00"
        }
    ],
    "total_count": 15,
    "message": "Student behaviour types retrieved successfully"
}
```

#### Test Case 3: Missing Required Fields
This test case demonstrates the validation error when required fields are missing.

**Request:**
```bash
curl -X POST "http://localhost/amt/api/studentbehaviour/create" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Expected Response (HTTP 400):**
```json
{
    "status": 0,
    "message": "Title is required"
}
```

---

## Notes

- All endpoints use POST method as per application standards
- Authentication is required for all endpoints
- This API specifically manages student behaviour types
- Points can be positive (for good behavior) or negative (for bad behavior)
- Behaviour types that are already assigned to incidents cannot be deleted for data integrity
- The functionality complements the existing student incidents API
