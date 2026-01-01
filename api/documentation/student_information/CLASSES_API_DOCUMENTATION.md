# Classes API Documentation

## Overview

The Classes API provides comprehensive CRUD (Create, Read, Update, Delete) functionality for managing school class records. This API allows you to manage the class system used for organizing students into different academic classes within the school.

---

## Base URL
```
http://{domain}/api/
```

## Important URL Structure

**For Classes APIs, use the controller/method pattern:**
- List classes: `http://{domain}/api/classes/list`
- Get single class: `http://{domain}/api/classes/get/{id}`
- Create class: `http://{domain}/api/classes/create`
- Update class: `http://{domain}/api/classes/update/{id}`
- Delete class: `http://{domain}/api/classes/delete/{id}`

**Examples:**
- List all: `http://localhost/amt/api/classes/list`
- Get class: `http://localhost/amt/api/classes/get/5`
- Create: `http://localhost/amt/api/classes/create`
- Update: `http://localhost/amt/api/classes/update/5`
- Delete: `http://localhost/amt/api/classes/delete/5`

## Authentication Headers

All API requests require the following headers:
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

---

## Endpoints

### 1. List Classes

**Endpoint:** `POST /classes/list`
**Full URL:** `http://localhost/amt/api/classes/list`

**Description:** Retrieve a list of all class records.

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
  "message": "Classes retrieved successfully",
  "total_records": 5,
  "data": [
    {
      "id": 1,
      "class": "Class 1",
      "is_active": "yes",
      "created_at": "2024-01-15 10:30:00",
      "updated_at": "2024-01-15 10:30:00"
    },
    {
      "id": 2,
      "class": "Class 2",
      "is_active": "yes",
      "created_at": "2024-01-16 11:45:00",
      "updated_at": "2024-01-16 11:45:00"
    },
    {
      "id": 3,
      "class": "Class 3",
      "is_active": "yes",
      "created_at": "2024-01-17 09:15:00",
      "updated_at": "2024-01-17 09:15:00"
    }
  ]
}
```

---

### 2. Get Single Class

**Endpoint:** `POST /classes/get/{id}`
**Full URL:** `http://localhost/amt/api/classes/get/5`

**Description:** Retrieve detailed information for a specific class record.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### URL Parameters
- `id` (required): Class record ID

#### Request Body
```json
{}
```

#### Success Response (HTTP 200)
```json
{
  "status": 1,
  "message": "Class record retrieved successfully",
  "data": {
    "id": 5,
    "class": "Class 5",
    "is_active": "yes",
    "created_at": "2024-01-15 10:30:00",
    "updated_at": "2024-01-15 10:30:00"
  }
}
```

---

### 3. Create Class

**Endpoint:** `POST /classes/create`
**Full URL:** `http://localhost/amt/api/classes/create`

**Description:** Create a new class record.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### Request Body
```json
{
  "class": "Class 6",
  "is_active": "yes"
}
```

#### Request Parameters
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| class | string | Yes | The class name (cannot be empty) |
| is_active | string | No | Active status ("yes" or "no"), defaults to "yes" |

#### Success Response (HTTP 201)
```json
{
  "status": 1,
  "message": "Class created successfully",
  "data": {
    "id": 6,
    "class": "Class 6",
    "is_active": "yes",
    "created_at": "2024-01-20 14:30:00"
  }
}
```

---

### 4. Update Class

**Endpoint:** `POST /classes/update/{id}`
**Full URL:** `http://localhost/amt/api/classes/update/5`

**Description:** Update an existing class record.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### URL Parameters
- `id` (required): Class record ID to update

#### Request Body
```json
{
  "class": "Updated Class 5",
  "is_active": "yes"
}
```

#### Request Parameters
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| class | string | Yes | The updated class name (cannot be empty) |
| is_active | string | No | Active status ("yes" or "no") |

#### Success Response (HTTP 200)
```json
{
  "status": 1,
  "message": "Class updated successfully",
  "data": {
    "id": 5,
    "class": "Updated Class 5",
    "is_active": "yes",
    "updated_at": "2024-01-20 15:45:00"
  }
}
```

---

### 5. Delete Class

**Endpoint:** `POST /classes/delete/{id}`
**Full URL:** `http://localhost/amt/api/classes/delete/5`

**Description:** Delete an existing class record.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### URL Parameters
- `id` (required): Class record ID to delete

#### Request Body
```json
{}
```

#### Success Response (HTTP 200)
```json
{
  "status": 1,
  "message": "Class deleted successfully",
  "data": {
    "id": 5,
    "class": "Class 5"
  }
}
```

---

## Error Responses

### 400 Bad Request
```json
{
  "status": 0,
  "message": "Invalid or missing class ID",
  "data": null
}
```

```json
{
  "status": 0,
  "message": "Class name is required and cannot be empty",
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
  "message": "Class record not found",
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

### Example 1: Get All Classes
```bash
curl -X POST "http://localhost/amt/api/classes/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 2: Create New Class
```bash
curl -X POST "http://localhost/amt/api/classes/create" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "class": "Class 7",
    "is_active": "yes"
  }'
```

### Example 3: Update Class
```bash
curl -X POST "http://localhost/amt/api/classes/update/3" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "class": "Updated Class 3",
    "is_active": "yes"
  }'
```

### Example 4: Delete Class
```bash
curl -X POST "http://localhost/amt/api/classes/delete/3" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 5: Get Specific Class
```bash
curl -X POST "http://localhost/amt/api/classes/get/2" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

## Database Tables Used

- `classes` - Main class records table

### Table Structure
```sql
CREATE TABLE `classes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `class` varchar(60) DEFAULT NULL,
  `is_active` varchar(255) DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL,
  PRIMARY KEY (`id`)
);
```

---

## Validation Rules

1. **Class Field:**
   - Required for create and update operations
   - Cannot be empty or contain only whitespace
   - String type
   - Maximum length: 60 characters

2. **Is Active Field:**
   - Optional for all operations
   - Must be "yes" or "no"
   - Defaults to "yes" for new records

3. **ID Parameter:**
   - Must be a positive integer
   - Required for get, update, and delete operations
   - Must exist in the database for update and delete operations

---

## Notes

1. All endpoints require POST method
2. Authentication headers are mandatory for all requests
3. Class name field is trimmed of leading/trailing whitespace
4. All responses include status, message, and data fields
5. Error responses follow consistent format
6. Successful creation returns HTTP 201, others return HTTP 200
7. Database transactions are used for data integrity
8. Audit logging is implemented for all operations
9. The `is_active` field defaults to "yes" for new classes
10. Deleting a class also removes associated class_sections records

---

## Common Use Cases

1. **List all classes** - For populating dropdown menus and class selection interfaces
2. **Create class** - Adding new classes to the school system
3. **Update class** - Modifying class names or active status
4. **Delete class** - Removing classes that are no longer needed
5. **Get specific class** - Retrieving details for editing forms or display

---

## Class System Integration

The Classes API integrates with:
- **Sections Management** - Classes are linked to sections via class_sections table
- **Student Management** - Students are assigned to classes
- **Teacher Management** - Teachers are assigned to classes
- **Timetable** - Classes are used in scheduling
- **Examinations** - Classes are used for exam organization
- **Reports** - Class-wise reports and statistics

---

## Best Practices

1. **Unique Names** - Ensure class names are unique within the school
2. **Meaningful Names** - Use clear, descriptive class names (e.g., "Grade 1", "Class 10-A")
3. **Active Status** - Use the is_active field to temporarily disable classes
4. **Validation** - Always validate class assignments before deletion
5. **Backup** - Maintain backups before bulk operations
6. **Dependencies** - Check for dependent records (students, sections) before deletion

---

## Support

For API support and questions, contact the development team or refer to the main project documentation.

