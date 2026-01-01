# Bulk Delete API Documentation

## Overview

The Bulk Delete API provides functionality for safely performing bulk deletion operations on student records. This API includes validation, confirmation mechanisms, and detailed reporting to ensure safe and controlled bulk deletion operations.

---

## Base URL
```
http://{domain}/api/
```

## Important URL Structure

**For Bulk Delete APIs, use the controller/method pattern:**
- Bulk delete students: `http://{domain}/api/bulk-delete/students`
- Validate deletion: `http://{domain}/api/bulk-delete/validate`

**Examples:**
- Delete students: `http://localhost/amt/api/bulk-delete/students`
- Validate: `http://localhost/amt/api/bulk-delete/validate`

## Authentication Headers

All API requests require the following headers:
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

---

## Endpoints

### 1. Bulk Delete Students

**Endpoint:** `POST /bulk-delete/students`
**Full URL:** `http://localhost/amt/api/bulk-delete/students`

**Description:** Performs bulk deletion of student records with safety checks and confirmation requirements.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### Request Body
```json
{
  "student_ids": [123, 456, 789],
  "confirmed": true
}
```

#### Request Parameters
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| student_ids | array | Yes | Array of student IDs to delete |
| confirmed | boolean | No | Confirmation flag (default: false) |

#### Success Response - Without Confirmation (HTTP 200)
```json
{
  "status": 0,
  "message": "Confirmation required for bulk deletion",
  "data": {
    "students_to_delete": [
      {
        "id": 123,
        "admission_no": "ADM2024001",
        "firstname": "John",
        "lastname": "Doe",
        "class_id": 10,
        "section_id": 5
      },
      {
        "id": 456,
        "admission_no": "ADM2024002",
        "firstname": "Jane",
        "lastname": "Smith",
        "class_id": 10,
        "section_id": 5
      }
    ],
    "total_count": 2,
    "non_existing_ids": [789],
    "confirmation_required": true,
    "warning": "This action will permanently delete the selected students and all their related data. Please confirm by setting \"confirmed\": true in your request."
  }
}
```

#### Success Response - With Confirmation (HTTP 200)
```json
{
  "status": 1,
  "message": "Bulk deletion completed successfully",
  "data": {
    "deleted_students": [
      {
        "id": 123,
        "admission_no": "ADM2024001",
        "firstname": "John",
        "lastname": "Doe",
        "class_id": 10,
        "section_id": 5
      },
      {
        "id": 456,
        "admission_no": "ADM2024002",
        "firstname": "Jane",
        "lastname": "Smith",
        "class_id": 10,
        "section_id": 5
      }
    ],
    "total_deleted": 2,
    "deletion_timestamp": "2024-01-20 15:30:45",
    "non_existing_ids": [789],
    "warning": "Some student IDs were not found and were skipped"
  }
}
```

---

### 2. Validate Bulk Delete

**Endpoint:** `POST /bulk-delete/validate`
**Full URL:** `http://localhost/amt/api/bulk-delete/validate`

**Description:** Validates student IDs and provides detailed information about what will be deleted without performing the actual deletion.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### Request Body
```json
{
  "student_ids": [123, 456, 789, "invalid", -1]
}
```

#### Request Parameters
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| student_ids | array | Yes | Array of student IDs to validate |

#### Success Response (HTTP 200)
```json
{
  "status": 1,
  "message": "Validation completed successfully",
  "data": {
    "validation_summary": {
      "total_requested": 5,
      "valid_students": 2,
      "invalid_ids": 2,
      "non_existing_ids": 1
    },
    "students_found": [
      {
        "id": 123,
        "admission_no": "ADM2024001",
        "full_name": "John Doe Smith",
        "firstname": "John",
        "middlename": "Doe",
        "lastname": "Smith",
        "class_info": {
          "class_id": 10,
          "class_name": "Class 10",
          "section_id": 5,
          "section_name": "Section A"
        },
        "email": "john.doe@example.com",
        "mobileno": "9876543210",
        "father_name": "Robert Doe",
        "is_active": "yes"
      },
      {
        "id": 456,
        "admission_no": "ADM2024002",
        "full_name": "Jane Smith",
        "firstname": "Jane",
        "middlename": "",
        "lastname": "Smith",
        "class_info": {
          "class_id": 10,
          "class_name": "Class 10",
          "section_id": 5,
          "section_name": "Section A"
        },
        "email": "jane.smith@example.com",
        "mobileno": "9876543211",
        "father_name": "Michael Smith",
        "is_active": "yes"
      }
    ],
    "invalid_ids": ["invalid", -1],
    "non_existing_ids": [789],
    "warnings": [
      "Some IDs are invalid (not numeric or negative)",
      "Some student IDs do not exist in the database"
    ]
  }
}
```

#### Validation Failed Response (HTTP 200)
```json
{
  "status": 0,
  "message": "Validation failed - no valid students found",
  "data": {
    "validation_summary": {
      "total_requested": 3,
      "valid_students": 0,
      "invalid_ids": 2,
      "non_existing_ids": 1
    },
    "students_found": [],
    "invalid_ids": ["invalid", -1],
    "non_existing_ids": [999],
    "warnings": [
      "Some IDs are invalid (not numeric or negative)",
      "Some student IDs do not exist in the database",
      "No valid students found for deletion"
    ]
  }
}
```

---

## Error Responses

### 400 Bad Request
```json
{
  "status": 0,
  "message": "Student IDs are required and must be an array",
  "data": null
}
```

```json
{
  "status": 0,
  "message": "Invalid student IDs found",
  "data": {
    "invalid_ids": ["invalid", -1, ""]
  }
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
  "message": "No valid students found for deletion",
  "data": {
    "non_existing_ids": [123, 456, 789]
  }
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
  "message": "Internal server error occurred during bulk deletion",
  "data": null
}
```

---

## Usage Examples

### Example 1: Validate Before Deletion
```bash
curl -X POST "http://localhost/amt/api/bulk-delete/validate" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "student_ids": [123, 456, 789]
  }'
```

### Example 2: Bulk Delete Without Confirmation (Preview)
```bash
curl -X POST "http://localhost/amt/api/bulk-delete/students" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "student_ids": [123, 456, 789]
  }'
```

### Example 3: Bulk Delete With Confirmation
```bash
curl -X POST "http://localhost/amt/api/bulk-delete/students" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "student_ids": [123, 456, 789],
    "confirmed": true
  }'
```

---

## Safety Features

### 1. Two-Step Confirmation Process
- First call without `confirmed: true` shows what will be deleted
- Second call with `confirmed: true` performs the actual deletion

### 2. Validation Checks
- Validates student ID format (must be positive integers)
- Checks if students exist in the database
- Provides detailed information about each student

### 3. Detailed Reporting
- Lists all students that will be/were deleted
- Reports invalid IDs and non-existing IDs
- Provides warnings for potential issues

### 4. Transaction Safety
- Uses database transactions for data integrity
- Rolls back on errors to maintain consistency

### 5. Audit Logging
- Logs all deletion attempts for audit purposes
- Records timestamps and user information

---

## Database Operations

### Tables Affected by Bulk Delete
- `students` - Main student records
- `student_session` - Student class/section assignments
- `student_fees_master` - Fee assignments
- `student_transport_fees` - Transport fee records
- `student_attendance` - Attendance records
- `student_documents` - Document records
- Related files and media are also deleted

### Cascade Deletions
The bulk delete operation handles cascade deletions for:
- Student session records
- Fee assignments
- Transport assignments
- Attendance records
- Document files
- Profile images

---

## Best Practices

1. **Always validate first** - Use the validate endpoint before deletion
2. **Review confirmation data** - Check the preview before confirming
3. **Handle partial failures** - Some IDs may be invalid or non-existing
4. **Monitor logs** - Check application logs for detailed operation records
5. **Backup data** - Ensure database backups before bulk operations
6. **Test with small batches** - Start with small sets for testing

---

## Limitations

1. **Maximum batch size** - Recommended maximum of 100 students per request
2. **Timeout considerations** - Large batches may timeout
3. **Memory usage** - Large operations consume more server memory
4. **File cleanup** - Associated files are deleted but may require cleanup

---

## Support

For API support and questions, contact the development team or refer to the main project documentation.
