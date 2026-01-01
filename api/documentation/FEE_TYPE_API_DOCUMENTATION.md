# Fee Type API Documentation

## Overview

The Fee Type API provides comprehensive CRUD (Create, Read, Update, Delete) functionality for managing fee types in the school management system. Fee types define specific categories of fees such as tuition, examination, library, sports, etc.

---

## Base URL
```
http://{domain}/api/
```

## Important URL Structure

**For Fee Type APIs, use the controller/method pattern:**
- List fee types: `http://{domain}/api/fee-types/list`
- Get single fee type: `http://{domain}/api/fee-types/get`
- Create fee type: `http://{domain}/api/fee-types/create`
- Update fee type: `http://{domain}/api/fee-types/update`
- Delete fee type: `http://{domain}/api/fee-types/delete`

**Examples:**
- List all: `http://localhost/amt/api/fee-types/list`
- Get specific: `http://localhost/amt/api/fee-types/get`
- Create: `http://localhost/amt/api/fee-types/create`
- Update: `http://localhost/amt/api/fee-types/update`
- Delete: `http://localhost/amt/api/fee-types/delete`

## Authentication Headers

All API requests require the following headers:
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

---

## Endpoints

### 1. List Fee Types

**Endpoint:** `POST /fee-types/list`
**Full URL:** `http://localhost/amt/api/fee-types/list`

**Description:** Retrieve a list of all fee type records.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### Request Body
```json
{}
```

#### Success Response (HTTP 200)
```json
{
  "status": 1,
  "message": "Fee types retrieved successfully",
  "total_records": 4,
  "data": [
    {
      "id": 1,
      "type": "Tuition Fee",
      "code": "TF001",
      "description": "Monthly tuition fees",
      "is_active": "yes",
      "created_at": "2024-01-15 10:30:00"
    },
    {
      "id": 2,
      "type": "Examination Fee",
      "code": "EF001",
      "description": "Semester examination fees",
      "is_active": "yes",
      "created_at": "2024-01-16 11:45:00"
    }
  ],
  "timestamp": "2025-10-05 12:30:45"
}
```

#### cURL Example
```bash
curl -X POST "http://localhost/amt/api/fee-types/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

### 2. Get Single Fee Type

**Endpoint:** `POST /fee-types/get`
**Full URL:** `http://localhost/amt/api/fee-types/get`

**Description:** Retrieve details of a specific fee type by ID.

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
  "message": "Fee type retrieved successfully",
  "data": {
    "id": 1,
    "type": "Tuition Fee",
    "code": "TF001",
    "description": "Monthly tuition fees",
    "is_active": "yes",
    "created_at": "2024-01-15 10:30:00"
  },
  "timestamp": "2025-10-05 12:30:45"
}
```

#### Error Response (HTTP 404)
```json
{
  "status": 0,
  "message": "Fee type not found"
}
```

#### cURL Example
```bash
curl -X POST "http://localhost/amt/api/fee-types/get" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "id": 1
  }'
```

---

### 3. Create Fee Type

**Endpoint:** `POST /fee-types/create`
**Full URL:** `http://localhost/amt/api/fee-types/create`

**Description:** Create a new fee type.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### Request Body
```json
{
  "type": "Library Fee",
  "code": "LF001",
  "description": "Annual library membership fee"
}
```

#### Success Response (HTTP 201)
```json
{
  "status": 1,
  "message": "Fee type created successfully",
  "data": {
    "type": "Library Fee",
    "code": "LF001",
    "description": "Annual library membership fee"
  },
  "timestamp": "2025-10-05 12:30:45"
}
```

#### Error Response (HTTP 400)
```json
{
  "status": 0,
  "message": "Fee type name is required"
}
```

#### Error Response (HTTP 409)
```json
{
  "status": 0,
  "message": "Fee type with this code already exists"
}
```

#### cURL Example
```bash
curl -X POST "http://localhost/amt/api/fee-types/create" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "type": "Library Fee",
    "code": "LF001",
    "description": "Annual library membership fee"
  }'
```

---

### 4. Update Fee Type

**Endpoint:** `POST /fee-types/update`
**Full URL:** `http://localhost/amt/api/fee-types/update`

**Description:** Update an existing fee type.

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
  "type": "Updated Tuition Fee",
  "code": "TF001",
  "description": "Updated monthly tuition fees"
}
```

#### Success Response (HTTP 200)
```json
{
  "status": 1,
  "message": "Fee type updated successfully",
  "data": {
    "id": 1,
    "type": "Updated Tuition Fee",
    "code": "TF001",
    "description": "Updated monthly tuition fees"
  },
  "timestamp": "2025-10-05 12:30:45"
}
```

#### Error Response (HTTP 404)
```json
{
  "status": 0,
  "message": "Fee type not found"
}
```

#### cURL Example
```bash
curl -X POST "http://localhost/amt/api/fee-types/update" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "id": 1,
    "type": "Updated Tuition Fee",
    "code": "TF001",
    "description": "Updated monthly tuition fees"
  }'
```

---

### 5. Delete Fee Type

**Endpoint:** `POST /fee-types/delete`
**Full URL:** `http://localhost/amt/api/fee-types/delete`

**Description:** Delete a fee type by ID.

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
  "message": "Fee type deleted successfully",
  "timestamp": "2025-10-05 12:30:45"
}
```

#### Error Response (HTTP 404)
```json
{
  "status": 0,
  "message": "Fee type not found"
}
```

#### cURL Example
```bash
curl -X POST "http://localhost/amt/api/fee-types/delete" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "id": 1
  }'
```

---

## Request Parameters

### Create/Update Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| type | string | Yes | Fee type name |
| code | string | Yes | Fee type code (unique) |
| description | string | No | Fee type description |

### Get/Delete Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | integer | Yes | Fee type ID |

---

## Response Fields

| Field | Type | Description |
|-------|------|-------------|
| status | integer | 1 for success, 0 for failure |
| message | string | Human-readable message |
| data | object/array | Response data |
| timestamp | string | Server timestamp |
| total_records | integer | Total number of records (list endpoint) |

---

## Error Codes

| HTTP Code | Description |
|-----------|-------------|
| 200 | Success |
| 201 | Created successfully |
| 400 | Bad request (validation error) |
| 404 | Resource not found |
| 409 | Conflict (duplicate entry) |
| 500 | Internal server error |

---

## Testing

Use the provided cURL examples to test each endpoint. Make sure to:

1. Replace `localhost/amt` with your actual domain
2. Ensure the authentication headers are correct
3. Verify the database contains test data for GET/UPDATE/DELETE operations

---

## Notes

- All endpoints use POST method as per application standards
- Authentication is required for all endpoints
- Fee type codes must be unique
- Both type name and code are required for creation
- Soft delete may be implemented (check with system administrator)
- Always validate input data before making API calls
