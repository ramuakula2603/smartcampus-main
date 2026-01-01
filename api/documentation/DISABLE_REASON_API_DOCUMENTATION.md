# Disable Reason API Documentation

## Overview

The Disable Reason API provides comprehensive CRUD (Create, Read, Update, Delete) functionality for managing student disable reasons. This API allows you to manage the reasons used when disabling student accounts in the school management system.

---

## Base URL
```
http://{domain}/api/
```

## Important URL Structure

**For Disable Reason APIs, use the controller/method pattern:**
- List reasons: `http://{domain}/api/disable-reason/list`
- Get single reason: `http://{domain}/api/disable-reason/get/{id}`
- Create reason: `http://{domain}/api/disable-reason/create`
- Update reason: `http://{domain}/api/disable-reason/update/{id}`
- Delete reason: `http://{domain}/api/disable-reason/delete/{id}`

**Examples:**
- List all: `http://localhost/amt/api/disable-reason/list`
- Get reason: `http://localhost/amt/api/disable-reason/get/5`
- Create: `http://localhost/amt/api/disable-reason/create`
- Update: `http://localhost/amt/api/disable-reason/update/5`
- Delete: `http://localhost/amt/api/disable-reason/delete/5`

## Authentication Headers

All API requests require the following headers:
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

---

## Endpoints

### 1. List Disable Reasons

**Endpoint:** `POST /disable-reason/list`
**Full URL:** `http://localhost/amt/api/disable-reason/list`

**Description:** Retrieve a list of all disable reason records.

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
  "message": "Disable reasons retrieved successfully",
  "total_records": 5,
  "data": [
    {
      "id": 1,
      "reason": "Academic Performance",
      "created_at": "2024-01-15 10:30:00",
      "updated_at": "2024-01-15 10:30:00"
    },
    {
      "id": 2,
      "reason": "Disciplinary Issues",
      "created_at": "2024-01-16 11:45:00",
      "updated_at": "2024-01-16 11:45:00"
    },
    {
      "id": 3,
      "reason": "Fee Payment Issues",
      "created_at": "2024-01-17 09:15:00",
      "updated_at": "2024-01-17 09:15:00"
    }
  ]
}
```

---

### 2. Get Single Disable Reason

**Endpoint:** `POST /disable-reason/get/{id}`
**Full URL:** `http://localhost/amt/api/disable-reason/get/5`

**Description:** Retrieve detailed information for a specific disable reason record.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### URL Parameters
- `id` (required): Disable reason record ID

#### Request Body
```json
{}
```

#### Success Response (HTTP 200)
```json
{
  "status": 1,
  "message": "Disable reason record retrieved successfully",
  "data": {
    "id": 5,
    "reason": "Academic Performance",
    "created_at": "2024-01-15 10:30:00",
    "updated_at": "2024-01-15 10:30:00"
  }
}
```

---

### 3. Create Disable Reason

**Endpoint:** `POST /disable-reason/create`
**Full URL:** `http://localhost/amt/api/disable-reason/create`

**Description:** Create a new disable reason record.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### Request Body
```json
{
  "reason": "Medical Issues"
}
```

#### Request Parameters
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| reason | string | Yes | The disable reason text (cannot be empty) |

#### Success Response (HTTP 201)
```json
{
  "status": 1,
  "message": "Disable reason created successfully",
  "data": {
    "id": 6,
    "reason": "Medical Issues",
    "created_at": "2024-01-20 14:30:00"
  }
}
```

---

### 4. Update Disable Reason

**Endpoint:** `POST /disable-reason/update/{id}`
**Full URL:** `http://localhost/amt/api/disable-reason/update/5`

**Description:** Update an existing disable reason record.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### URL Parameters
- `id` (required): Disable reason record ID to update

#### Request Body
```json
{
  "reason": "Updated Academic Performance Issues"
}
```

#### Request Parameters
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| reason | string | Yes | The updated disable reason text (cannot be empty) |

#### Success Response (HTTP 200)
```json
{
  "status": 1,
  "message": "Disable reason updated successfully",
  "data": {
    "id": 5,
    "reason": "Updated Academic Performance Issues",
    "updated_at": "2024-01-20 15:45:00"
  }
}
```

---

### 5. Delete Disable Reason

**Endpoint:** `POST /disable-reason/delete/{id}`
**Full URL:** `http://localhost/amt/api/disable-reason/delete/5`

**Description:** Delete an existing disable reason record.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### URL Parameters
- `id` (required): Disable reason record ID to delete

#### Request Body
```json
{}
```

#### Success Response (HTTP 200)
```json
{
  "status": 1,
  "message": "Disable reason deleted successfully",
  "data": {
    "id": 5,
    "reason": "Academic Performance Issues"
  }
}
```

---

## Error Responses

### 400 Bad Request
```json
{
  "status": 0,
  "message": "Invalid or missing disable reason ID",
  "data": null
}
```

```json
{
  "status": 0,
  "message": "Disable reason is required and cannot be empty",
  "data": null
}
```

### 401 Unauthorized
```json
{
  "status": 0,
  "message": "Unauthorized access. Invalid headers.",
  "data": null
}
```

### 404 Not Found
```json
{
  "status": 0,
  "message": "Disable reason record not found",
  "data": null
}
```

### 405 Method Not Allowed
```json
{
  "status": 0,
  "message": "Method not allowed. Use POST method.",
  "data": null
}
```

### 500 Internal Server Error
```json
{
  "status": 0,
  "message": "Internal server error occurred",
  "data": null
}
```

---

## Usage Examples

### Example 1: Get All Disable Reasons
```bash
curl -X POST "http://localhost/amt/api/disable-reason/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 2: Create New Disable Reason
```bash
curl -X POST "http://localhost/amt/api/disable-reason/create" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "reason": "Transfer to Another School"
  }'
```

### Example 3: Update Disable Reason
```bash
curl -X POST "http://localhost/amt/api/disable-reason/update/3" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "reason": "Updated Fee Payment Issues"
  }'
```

### Example 4: Delete Disable Reason
```bash
curl -X POST "http://localhost/amt/api/disable-reason/delete/3" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 5: Get Specific Disable Reason
```bash
curl -X POST "http://localhost/amt/api/disable-reason/get/2" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

## Database Tables Used

- `disable_reason` - Main disable reason records table

### Table Structure
```sql
CREATE TABLE `disable_reason` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reason` varchar(255) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);
```

---

## Validation Rules

1. **Reason Field:**
   - Required for create and update operations
   - Cannot be empty or contain only whitespace
   - String type
   - Maximum length: 255 characters

2. **ID Parameter:**
   - Must be a positive integer
   - Required for get, update, and delete operations
   - Must exist in the database for update and delete operations

---

## Notes

1. All endpoints require POST method
2. Authentication headers are mandatory for all requests
3. The reason field is trimmed of leading/trailing whitespace
4. All responses include status, message, and data fields
5. Error responses follow consistent format
6. Successful creation returns HTTP 201, others return HTTP 200
7. Database transactions are used for data integrity
8. Audit logging is implemented for all operations

---

## Common Use Cases

1. **List all reasons** - For populating dropdown menus in admin interfaces
2. **Create reason** - Adding new disable reasons as needed
3. **Update reason** - Modifying existing reason text for clarity
4. **Delete reason** - Removing obsolete or duplicate reasons
5. **Get specific reason** - Retrieving details for editing forms

---

## Support

For API support and questions, contact the development team or refer to the main project documentation.
