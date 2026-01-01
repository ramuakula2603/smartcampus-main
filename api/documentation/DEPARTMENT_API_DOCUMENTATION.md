# Department API Documentation

## Overview

The Department API provides comprehensive CRUD (Create, Read, Update, Delete) functionality for managing staff department records. This API allows you to manage the department system used for organizing staff members into different departments within the school.

---

## Base URL
```
http://{domain}/api/
```

## Important URL Structure

**For Department APIs, use the controller/method pattern:**
- List departments: `http://{domain}/api/department/list`
- Get single department: `http://{domain}/api/department/get/{id}`
- Create department: `http://{domain}/api/department/create`
- Update department: `http://{domain}/api/department/update/{id}`
- Delete department: `http://{domain}/api/department/delete/{id}`

**Examples:**
- List all: `http://localhost/amt/api/department/list`
- Get department: `http://localhost/amt/api/department/get/5`
- Create: `http://localhost/amt/api/department/create`
- Update: `http://localhost/amt/api/department/update/5`
- Delete: `http://localhost/amt/api/department/delete/5`

## Authentication Headers

All API requests require the following headers:
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

---

## Endpoints

### 1. List Departments

**Endpoint:** `POST /department/list`
**Full URL:** `http://localhost/amt/api/department/list`

**Description:** Retrieve a list of all department records.

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
  "message": "Departments retrieved successfully",
  "total_records": 5,
  "data": [
    {
      "id": 1,
      "department_name": "Computer Science",
      "is_active": "yes"
    },
    {
      "id": 2,
      "department_name": "Mathematics",
      "is_active": "yes"
    },
    {
      "id": 3,
      "department_name": "English",
      "is_active": "yes"
    },
    {
      "id": 4,
      "department_name": "Science",
      "is_active": "yes"
    },
    {
      "id": 5,
      "department_name": "Administration",
      "is_active": "yes"
    }
  ]
}
```

---

### 2. Get Single Department

**Endpoint:** `POST /department/get/{id}`
**Full URL:** `http://localhost/amt/api/department/get/5`

**Description:** Retrieve detailed information for a specific department record.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### URL Parameters
- `id` (required): Department record ID

#### Request Body
```json
{}
```

#### Success Response (HTTP 200)
```json
{
  "status": 1,
  "message": "Department record retrieved successfully",
  "data": {
    "id": 5,
    "department_name": "Computer Science",
    "is_active": "yes"
  }
}
```

---

### 3. Create Department

**Endpoint:** `POST /department/create`
**Full URL:** `http://localhost/amt/api/department/create`

**Description:** Create a new department record.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### Request Body
```json
{
  "department_name": "Physical Education",
  "is_active": "yes"
}
```

#### Request Parameters
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| department_name | string | Yes | The department name (cannot be empty, must be unique) |
| is_active | string | No | Active status ("yes" or "no"), defaults to "yes" |

#### Success Response (HTTP 201)
```json
{
  "status": 1,
  "message": "Department created successfully",
  "data": {
    "id": 6,
    "department_name": "Physical Education",
    "is_active": "yes"
  }
}
```

---

### 4. Update Department

**Endpoint:** `POST /department/update/{id}`
**Full URL:** `http://localhost/amt/api/department/update/5`

**Description:** Update an existing department record.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### URL Parameters
- `id` (required): Department record ID to update

#### Request Body
```json
{
  "department_name": "Updated Computer Science",
  "is_active": "yes"
}
```

#### Request Parameters
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| department_name | string | Yes | The updated department name (cannot be empty, must be unique) |
| is_active | string | No | Active status ("yes" or "no") |

#### Success Response (HTTP 200)
```json
{
  "status": 1,
  "message": "Department updated successfully",
  "data": {
    "id": 5,
    "department_name": "Updated Computer Science",
    "is_active": "yes"
  }
}
```

---

### 5. Delete Department

**Endpoint:** `POST /department/delete/{id}`
**Full URL:** `http://localhost/amt/api/department/delete/5`

**Description:** Delete an existing department record.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### URL Parameters
- `id` (required): Department record ID to delete

#### Request Body
```json
{}
```

#### Success Response (HTTP 200)
```json
{
  "status": 1,
  "message": "Department deleted successfully",
  "data": {
    "id": 5,
    "department_name": "Computer Science"
  }
}
```

---

## Error Responses

### 400 Bad Request
```json
{
  "status": 0,
  "message": "Invalid or missing department ID",
  "data": null
}
```

```json
{
  "status": 0,
  "message": "Department name is required and cannot be empty",
  "data": null
}
```

```json
{
  "status": 0,
  "message": "Department name already exists",
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
  "message": "Department record not found",
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

### Example 1: Get All Departments
```bash
curl -X POST "http://localhost/amt/api/department/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 2: Create New Department
```bash
curl -X POST "http://localhost/amt/api/department/create" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "department_name": "Art and Craft",
    "is_active": "yes"
  }'
```

### Example 3: Update Department
```bash
curl -X POST "http://localhost/amt/api/department/update/3" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "department_name": "Updated English Department",
    "is_active": "yes"
  }'
```

### Example 4: Delete Department
```bash
curl -X POST "http://localhost/amt/api/department/delete/3" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 5: Get Specific Department
```bash
curl -X POST "http://localhost/amt/api/department/get/2" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

## Database Tables Used

- `department` - Main department records table

### Table Structure
```sql
CREATE TABLE `department` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `department_name` varchar(200) NOT NULL,
  `is_active` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
);
```

---

## Validation Rules

1. **Department Name Field:**
   - Required for create and update operations
   - Cannot be empty or contain only whitespace
   - String type
   - Maximum length: 200 characters
   - Must be unique across all departments

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
3. Department name field is trimmed of leading/trailing whitespace
4. All responses include status, message, and data fields
5. Error responses follow consistent format
6. Successful creation returns HTTP 201, others return HTTP 200
7. Database transactions are used for data integrity
8. Audit logging is implemented for all operations
9. The `is_active` field defaults to "yes" for new departments
10. Department names must be unique - duplicates are not allowed

---

## Common Use Cases

1. **List all departments** - For populating dropdown menus and department selection interfaces
2. **Create department** - Adding new departments to the school system
3. **Update department** - Modifying department names or active status
4. **Delete department** - Removing departments that are no longer needed
5. **Get specific department** - Retrieving details for editing forms or display

---

## Department System Integration

The Department API integrates with:
- **Staff Management** - Staff members are assigned to departments
- **Designation Management** - Designations can be department-specific
- **Payroll** - Department-wise salary and payroll management
- **Reports** - Department-wise staff reports and statistics
- **Human Resources** - Department-based staff organization

---

## Best Practices

1. **Descriptive Names** - Use clear, descriptive department names
2. **Unique Names** - Ensure department names are unique within the school
3. **Active Status** - Use the is_active field to temporarily disable departments
4. **Validation** - Always validate staff assignments before deletion
5. **Backup** - Maintain backups before bulk operations
6. **Dependencies** - Check for dependent records (staff, designations) before deletion

---

## Support

For API support and questions, contact the development team or refer to the main project documentation.
