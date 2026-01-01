# Subjects API Documentation

## Overview

The Subjects API provides comprehensive CRUD (Create, Read, Update, Delete) functionality for managing school subject records. This API allows you to manage the subject system used for organizing academic subjects within the school.

---

## Base URL
```
http://{domain}/api/
```

## Important URL Structure

**For Subjects APIs, use the controller/method pattern:**
- List subjects: `http://{domain}/api/subjects/list`
- Get single subject: `http://{domain}/api/subjects/get/{id}`
- Create subject: `http://{domain}/api/subjects/create`
- Update subject: `http://{domain}/api/subjects/update/{id}`
- Delete subject: `http://{domain}/api/subjects/delete/{id}`

**Examples:**
- List all: `http://localhost/amt/api/subjects/list`
- Get subject: `http://localhost/amt/api/subjects/get/5`
- Create: `http://localhost/amt/api/subjects/create`
- Update: `http://localhost/amt/api/subjects/update/5`
- Delete: `http://localhost/amt/api/subjects/delete/5`

## Authentication Headers

All API requests require the following headers:
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

---

## Endpoints

### 1. List Subjects

**Endpoint:** `POST /subjects/list`
**Full URL:** `http://localhost/amt/api/subjects/list`

**Description:** Retrieve a list of all subject records.

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
  "message": "Subjects retrieved successfully",
  "total_records": 5,
  "data": [
    {
      "id": 1,
      "name": "Mathematics",
      "code": "MATH",
      "is_active": "yes",
      "created_at": "2024-01-15 10:30:00",
      "updated_at": "2024-01-15 10:30:00"
    },
    {
      "id": 2,
      "name": "English",
      "code": "ENG",
      "is_active": "yes",
      "created_at": "2024-01-16 11:45:00",
      "updated_at": "2024-01-16 11:45:00"
    }
  ]
}
```

---

### 2. Get Single Subject

**Endpoint:** `POST /subjects/get/{id}`
**Full URL:** `http://localhost/amt/api/subjects/get/5`

**Description:** Retrieve detailed information for a specific subject record.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### URL Parameters
- `id` (required): Subject record ID

#### Request Body
```json
{}
```

#### Success Response (HTTP 200)
```json
{
  "status": 1,
  "message": "Subject record retrieved successfully",
  "data": {
    "id": 5,
    "name": "Science",
    "code": "SCI",
    "is_active": "yes",
    "created_at": "2024-01-15 10:30:00",
    "updated_at": "2024-01-15 10:30:00"
  }
}
```

---

### 3. Create Subject

**Endpoint:** `POST /subjects/create`
**Full URL:** `http://localhost/amt/api/subjects/create`

**Description:** Create a new subject record.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### Request Body
```json
{
  "name": "History",
  "code": "HIST",
  "is_active": "yes"
}
```

#### Required Fields
- `name` (string): Subject name

#### Optional Fields
- `code` (string): Subject code
- `is_active` (string): Active status (default: "yes")

#### Success Response (HTTP 201)
```json
{
  "status": 1,
  "message": "Subject created successfully",
  "data": {
    "id": 6,
    "name": "History",
    "code": "HIST",
    "is_active": "yes",
    "created_at": "2024-01-20 14:30:00"
  }
}
```

---

### 4. Update Subject

**Endpoint:** `POST /subjects/update/{id}`
**Full URL:** `http://localhost/amt/api/subjects/update/5`

**Description:** Update an existing subject record.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### URL Parameters
- `id` (required): Subject record ID to update

#### Request Body
```json
{
  "name": "Updated Science",
  "code": "SCI-UPD",
  "is_active": "yes"
}
```

#### Required Fields
- `name` (string): Subject name

#### Optional Fields
- `code` (string): Subject code
- `is_active` (string): Active status

#### Success Response (HTTP 200)
```json
{
  "status": 1,
  "message": "Subject updated successfully",
  "data": {
    "id": 5,
    "name": "Updated Science",
    "code": "SCI-UPD",
    "is_active": "yes",
    "updated_at": "2024-01-20 15:45:00"
  }
}
```

---

### 5. Delete Subject

**Endpoint:** `POST /subjects/delete/{id}`
**Full URL:** `http://localhost/amt/api/subjects/delete/5`

**Description:** Delete an existing subject record.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### URL Parameters
- `id` (required): Subject record ID to delete

#### Request Body
```json
{}
```

#### Success Response (HTTP 200)
```json
{
  "status": 1,
  "message": "Subject deleted successfully",
  "data": {
    "id": 5,
    "name": "Science",
    "code": "SCI"
  }
}
```

---

## Error Responses

### 400 Bad Request
```json
{
  "status": 0,
  "message": "Subject name is required and cannot be empty",
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
  "message": "Subject record not found",
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

### Example 1: Get All Subjects
```bash
curl -X POST "http://localhost/amt/api/subjects/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 2: Create New Subject
```bash
curl -X POST "http://localhost/amt/api/subjects/create" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "name": "Geography",
    "code": "GEO",
    "is_active": "yes"
  }'
```

### Example 3: Update Subject
```bash
curl -X POST "http://localhost/amt/api/subjects/update/3" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "name": "Updated Geography",
    "code": "GEO-UPD",
    "is_active": "yes"
  }'
```

### Example 4: Delete Subject
```bash
curl -X POST "http://localhost/amt/api/subjects/delete/3" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 5: Get Specific Subject
```bash
curl -X POST "http://localhost/amt/api/subjects/get/2" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

## Database Tables Used

- `subjects` - Main subject records table

### Table Structure
```sql
CREATE TABLE `subjects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `code` varchar(60) DEFAULT NULL,
  `is_active` varchar(255) DEFAULT 'yes',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL,
  PRIMARY KEY (`id`)
);
```

---

## Notes

1. All endpoints require POST method
2. Authentication headers are mandatory for all requests
3. Subject name field is trimmed of leading/trailing whitespace
4. All responses include status, message, and data fields
5. Error responses follow consistent format
6. Successful creation returns HTTP 201, others return HTTP 200
7. Database transactions are used for data integrity
8. Audit logging is implemented for all operations
9. The `is_active` field defaults to "yes" for new subjects
10. Subject code is optional but recommended for identification

---

## Common Use Cases

1. **List all subjects** - For populating dropdown menus and subject selection interfaces
2. **Create subject** - Adding new subjects to the school system
3. **Update subject** - Modifying subject names, codes, or active status
4. **Delete subject** - Removing subjects that are no longer needed
5. **Get specific subject** - Retrieving details for editing forms or display

---

## Subject System Integration

The Subjects API integrates with:
- **Classes Management** - Subjects are assigned to classes
- **Teacher Management** - Teachers are assigned to subjects
- **Student Management** - Students study assigned subjects
- **Timetable** - Subjects are used in scheduling
- **Examinations** - Subjects are used for exam organization
- **Reports** - Subject-wise reports and statistics

---

## Best Practices

1. **Standard Names** - Use clear, descriptive subject names
2. **Consistent Coding** - Maintain consistent subject code conventions
3. **Active Status** - Use the is_active field to temporarily disable subjects
4. **Validation** - Always validate subject assignments before deletion
5. **Backup** - Maintain backups before bulk operations
6. **Dependencies** - Check for dependent records before deletion

---

## Support

For API support and questions, contact the development team or refer to the main project documentation.

