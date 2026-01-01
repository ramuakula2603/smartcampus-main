# Fee Group API Documentation

## Overview

The Fee Group API provides comprehensive CRUD (Create, Read, Update, Delete) functionality for managing fee groups in the school management system. Fee groups are used to categorize different types of fees and organize fee structures.

---

## Base URL
```
http://{domain}/api/
```

## Important URL Structure

**For Fee Group APIs, use the controller/method pattern:**
- List fee groups: `http://{domain}/api/fee-groups/list`
- Get single fee group: `http://{domain}/api/fee-groups/get`
- Create fee group: `http://{domain}/api/fee-groups/create`
- Update fee group: `http://{domain}/api/fee-groups/update`
- Delete fee group: `http://{domain}/api/fee-groups/delete`

**Examples:**
- List all: `http://localhost/amt/api/fee-groups/list`
- Get specific: `http://localhost/amt/api/fee-groups/get`
- Create: `http://localhost/amt/api/fee-groups/create`
- Update: `http://localhost/amt/api/fee-groups/update`
- Delete: `http://localhost/amt/api/fee-groups/delete`

## Authentication Headers

All API requests require the following headers:
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

---

## Endpoints

### 1. List Fee Groups

**Endpoint:** `POST /fee-groups/list`
**Full URL:** `http://localhost/amt/api/fee-groups/list`

**Description:** Retrieve a list of all fee group records.

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
  "message": "Fee groups retrieved successfully",
  "total_records": 3,
  "data": [
    {
      "id": 1,
      "name": "Tuition Fees",
      "description": "Monthly tuition fees for all classes",
      "is_active": "yes",
      "created_at": "2024-01-15 10:30:00"
    },
    {
      "id": 2,
      "name": "Examination Fees",
      "description": "Fees for various examinations",
      "is_active": "yes",
      "created_at": "2024-01-16 11:45:00"
    }
  ],
  "timestamp": "2025-10-05 12:30:45"
}
```

#### cURL Example
```bash
curl -X POST "http://localhost/amt/api/fee-groups/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

### 2. Get Single Fee Group

**Endpoint:** `POST /fee-groups/get`
**Full URL:** `http://localhost/amt/api/fee-groups/get`

**Description:** Retrieve details of a specific fee group by ID.

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
  "message": "Fee group retrieved successfully",
  "data": {
    "id": 1,
    "name": "Tuition Fees",
    "description": "Monthly tuition fees for all classes",
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
  "message": "Fee group not found"
}
```

#### cURL Example
```bash
curl -X POST "http://localhost/amt/api/fee-groups/get" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "id": 1
  }'
```

---

### 3. Create Fee Group

**Endpoint:** `POST /fee-groups/create`
**Full URL:** `http://localhost/amt/api/fee-groups/create`

**Description:** Create a new fee group.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### Request Body
```json
{
  "name": "Library Fees",
  "description": "Annual library membership and book fees"
}
```

#### Success Response (HTTP 201)
```json
{
  "status": 1,
  "message": "Fee group created successfully",
  "data": {
    "name": "Library Fees",
    "description": "Annual library membership and book fees"
  },
  "timestamp": "2025-10-05 12:30:45"
}
```

#### Error Response (HTTP 400)
```json
{
  "status": 0,
  "message": "Fee group name is required"
}
```

#### Error Response (HTTP 409)
```json
{
  "status": 0,
  "message": "Fee group with this name already exists"
}
```

#### cURL Example
```bash
curl -X POST "http://localhost/amt/api/fee-groups/create" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "name": "Library Fees",
    "description": "Annual library membership and book fees"
  }'
```

---

### 4. Update Fee Group

**Endpoint:** `POST /fee-groups/update`
**Full URL:** `http://localhost/amt/api/fee-groups/update`

**Description:** Update an existing fee group.

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
  "name": "Updated Tuition Fees",
  "description": "Updated monthly tuition fees for all classes"
}
```

#### Success Response (HTTP 200)
```json
{
  "status": 1,
  "message": "Fee group updated successfully",
  "data": {
    "id": 1,
    "name": "Updated Tuition Fees",
    "description": "Updated monthly tuition fees for all classes"
  },
  "timestamp": "2025-10-05 12:30:45"
}
```

#### Error Response (HTTP 404)
```json
{
  "status": 0,
  "message": "Fee group not found"
}
```

#### cURL Example
```bash
curl -X POST "http://localhost/amt/api/fee-groups/update" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "id": 1,
    "name": "Updated Tuition Fees",
    "description": "Updated monthly tuition fees for all classes"
  }'
```

---

### 5. Delete Fee Group

**Endpoint:** `POST /fee-groups/delete`
**Full URL:** `http://localhost/amt/api/fee-groups/delete`

**Description:** Delete a fee group by ID.

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
  "message": "Fee group deleted successfully",
  "timestamp": "2025-10-05 12:30:45"
}
```

#### Error Response (HTTP 404)
```json
{
  "status": 0,
  "message": "Fee group not found"
}
```

#### cURL Example
```bash
curl -X POST "http://localhost/amt/api/fee-groups/delete" \
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
| name | string | Yes | Fee group name (unique) |
| description | string | No | Fee group description |

### Get/Delete Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | integer | Yes | Fee group ID |

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
- Fee group names must be unique
- Soft delete may be implemented (check with system administrator)
- Always validate input data before making API calls
