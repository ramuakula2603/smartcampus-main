# Student House API Documentation

## Overview

The Student House API provides comprehensive CRUD (Create, Read, Update, Delete) functionality for managing school house records. This API allows you to manage the house system used for organizing students into different houses within the school.

---

## Base URL
```
http://{domain}/api/
```

## Important URL Structure

**For Student House APIs, use the controller/method pattern:**
- List houses: `http://{domain}/api/student-house/list`
- Get single house: `http://{domain}/api/student-house/get/{id}`
- Create house: `http://{domain}/api/student-house/create`
- Update house: `http://{domain}/api/student-house/update/{id}`
- Delete house: `http://{domain}/api/student-house/delete/{id}`

**Examples:**
- List all: `http://localhost/amt/api/student-house/list`
- Get house: `http://localhost/amt/api/student-house/get/5`
- Create: `http://localhost/amt/api/student-house/create`
- Update: `http://localhost/amt/api/student-house/update/5`
- Delete: `http://localhost/amt/api/student-house/delete/5`

## Authentication Headers

All API requests require the following headers:
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

---

## Endpoints

### 1. List Student Houses

**Endpoint:** `POST /student-house/list`
**Full URL:** `http://localhost/amt/api/student-house/list`

**Description:** Retrieve a list of all student house records.

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
  "message": "Student houses retrieved successfully",
  "total_records": 4,
  "data": [
    {
      "id": 1,
      "house_name": "Red House",
      "description": "The Red House represents courage and strength",
      "is_active": "yes",
      "created_at": "2024-01-15 10:30:00",
      "updated_at": "2024-01-15 10:30:00"
    },
    {
      "id": 2,
      "house_name": "Blue House",
      "description": "The Blue House represents wisdom and knowledge",
      "is_active": "yes",
      "created_at": "2024-01-16 11:45:00",
      "updated_at": "2024-01-16 11:45:00"
    },
    {
      "id": 3,
      "house_name": "Green House",
      "description": "The Green House represents growth and harmony",
      "is_active": "yes",
      "created_at": "2024-01-17 09:15:00",
      "updated_at": "2024-01-17 09:15:00"
    },
    {
      "id": 4,
      "house_name": "Yellow House",
      "description": "The Yellow House represents energy and creativity",
      "is_active": "yes",
      "created_at": "2024-01-18 14:20:00",
      "updated_at": "2024-01-18 14:20:00"
    }
  ]
}
```

---

### 2. Get Single Student House

**Endpoint:** `POST /student-house/get/{id}`
**Full URL:** `http://localhost/amt/api/student-house/get/5`

**Description:** Retrieve detailed information for a specific student house record.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### URL Parameters
- `id` (required): Student house record ID

#### Request Body
```json
{}
```

#### Success Response (HTTP 200)
```json
{
  "status": 1,
  "message": "Student house record retrieved successfully",
  "data": {
    "id": 5,
    "house_name": "Red House",
    "description": "The Red House represents courage and strength",
    "is_active": "yes",
    "created_at": "2024-01-15 10:30:00",
    "updated_at": "2024-01-15 10:30:00"
  }
}
```

---

### 3. Create Student House

**Endpoint:** `POST /student-house/create`
**Full URL:** `http://localhost/amt/api/student-house/create`

**Description:** Create a new student house record.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### Request Body
```json
{
  "house_name": "Purple House",
  "description": "The Purple House represents leadership and innovation"
}
```

#### Request Parameters
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| house_name | string | Yes | The house name (cannot be empty) |
| description | string | No | Description of the house |

#### Success Response (HTTP 201)
```json
{
  "status": 1,
  "message": "Student house created successfully",
  "data": {
    "id": 6,
    "house_name": "Purple House",
    "description": "The Purple House represents leadership and innovation",
    "is_active": "yes",
    "created_at": "2024-01-20 14:30:00"
  }
}
```

---

### 4. Update Student House

**Endpoint:** `POST /student-house/update/{id}`
**Full URL:** `http://localhost/amt/api/student-house/update/5`

**Description:** Update an existing student house record.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### URL Parameters
- `id` (required): Student house record ID to update

#### Request Body
```json
{
  "house_name": "Updated Red House",
  "description": "The Red House represents courage, strength, and determination",
  "is_active": "yes"
}
```

#### Request Parameters
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| house_name | string | Yes | The updated house name (cannot be empty) |
| description | string | No | Updated description of the house |
| is_active | string | No | Active status ("yes" or "no") |

#### Success Response (HTTP 200)
```json
{
  "status": 1,
  "message": "Student house updated successfully",
  "data": {
    "id": 5,
    "house_name": "Updated Red House",
    "description": "The Red House represents courage, strength, and determination",
    "is_active": "yes",
    "updated_at": "2024-01-20 15:45:00"
  }
}
```

---

### 5. Delete Student House

**Endpoint:** `POST /student-house/delete/{id}`
**Full URL:** `http://localhost/amt/api/student-house/delete/5`

**Description:** Delete an existing student house record.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### URL Parameters
- `id` (required): Student house record ID to delete

#### Request Body
```json
{}
```

#### Success Response (HTTP 200)
```json
{
  "status": 1,
  "message": "Student house deleted successfully",
  "data": {
    "id": 5,
    "house_name": "Red House",
    "description": "The Red House represents courage and strength"
  }
}
```

---

## Error Responses

### 400 Bad Request
```json
{
  "status": 0,
  "message": "Invalid or missing student house ID",
  "data": null
}
```

```json
{
  "status": 0,
  "message": "House name is required and cannot be empty",
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
  "message": "Student house record not found",
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

### Example 1: Get All Student Houses
```bash
curl -X POST "http://localhost/amt/api/student-house/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 2: Create New Student House
```bash
curl -X POST "http://localhost/amt/api/student-house/create" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "house_name": "Orange House",
    "description": "The Orange House represents enthusiasm and teamwork"
  }'
```

### Example 3: Update Student House
```bash
curl -X POST "http://localhost/amt/api/student-house/update/3" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "house_name": "Updated Green House",
    "description": "The Green House represents growth, harmony, and environmental consciousness",
    "is_active": "yes"
  }'
```

### Example 4: Delete Student House
```bash
curl -X POST "http://localhost/amt/api/student-house/delete/3" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 5: Get Specific Student House
```bash
curl -X POST "http://localhost/amt/api/student-house/get/2" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

## Database Tables Used

- `school_houses` - Main student house records table

### Table Structure
```sql
CREATE TABLE `school_houses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `house_name` varchar(255) NOT NULL,
  `description` text,
  `is_active` enum('yes','no') DEFAULT 'yes',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);
```

---

## Validation Rules

1. **House Name Field:**
   - Required for create and update operations
   - Cannot be empty or contain only whitespace
   - String type
   - Should be unique (recommended)

2. **Description Field:**
   - Optional for all operations
   - Text type
   - Can be empty

3. **Is Active Field:**
   - Optional for update operations
   - Must be "yes" or "no"
   - Defaults to "yes" for new records

4. **ID Parameter:**
   - Must be a positive integer
   - Required for get, update, and delete operations
   - Must exist in the database for update and delete operations

---

## Notes

1. All endpoints require POST method
2. Authentication headers are mandatory for all requests
3. House name and description fields are trimmed of leading/trailing whitespace
4. All responses include status, message, and data fields
5. Error responses follow consistent format
6. Successful creation returns HTTP 201, others return HTTP 200
7. Database transactions are used for data integrity
8. Audit logging is implemented for all operations
9. The `is_active` field defaults to "yes" for new houses

---

## Common Use Cases

1. **List all houses** - For populating dropdown menus and house selection interfaces
2. **Create house** - Adding new houses to the school house system
3. **Update house** - Modifying house names, descriptions, or active status
4. **Delete house** - Removing houses that are no longer needed
5. **Get specific house** - Retrieving details for editing forms or display

---

## House System Integration

The Student House API integrates with:
- **Student Management** - Students can be assigned to houses
- **Online Admissions** - Houses can be selected during admission
- **Reports** - House-wise student reports and statistics
- **Events** - House-based competitions and activities

---

## Best Practices

1. **Unique Names** - Ensure house names are unique within the school
2. **Meaningful Descriptions** - Provide clear descriptions for each house
3. **Active Status** - Use the is_active field to temporarily disable houses
4. **Validation** - Always validate house assignments before deletion
5. **Backup** - Maintain backups before bulk operations

---

## Support

For API support and questions, contact the development team or refer to the main project documentation.
