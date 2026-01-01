# Student Categories API Documentation

## Overview

The Student Categories API provides complete CRUD (Create, Read, Update, Delete) operations for managing student categories. Categories are used to classify students based on various criteria such as caste, special needs, or other institutional classifications.

---

## Authentication

All endpoints require authentication headers:

| Header | Value | Required |
|--------|-------|----------|
| Content-Type | application/json | Yes |
| Client-Service | smartschool | Yes |
| Auth-Key | schoolAdmin@ | Yes |

---

## Endpoints Summary

| Endpoint | Method | Purpose | HTTP Status |
|----------|--------|---------|-------------|
| `/teacher/student-categories` | POST | Get all categories | 200 |
| `/teacher/student-category/get` | POST | Get single category | 200, 404 |
| `/teacher/student-category/create` | POST | Create new category | 201, 400, 409 |
| `/teacher/student-category/update` | POST | Update category | 200, 400, 404, 409 |
| `/teacher/student-category/delete` | POST | Delete category | 200, 404, 409 |

---

## 1. Get All Categories

Retrieve a list of all student categories.

### Request

**Endpoint:** `POST /teacher/student-categories`  
**URL:** `http://localhost/amt/api/teacher/student-categories`

**Request Body:**
```json
{}
```

### Response

**Success (HTTP 200):**
```json
{
  "status": 1,
  "message": "Student categories retrieved successfully",
  "total_categories": 86,
  "data": [
    {
      "category_id": 1,
      "category_name": "General",
      "is_active": "no",
      "created_at": "2023-08-01 17:30:49",
      "updated_at": null
    },
    {
      "category_id": 2,
      "category_name": "OBC",
      "is_active": "no",
      "created_at": "2023-08-12 00:20:13",
      "updated_at": null
    }
  ],
  "timestamp": "2025-10-05 00:10:18"
}
```

### Response Fields

| Field | Type | Description |
|-------|------|-------------|
| status | integer | 1 for success, 0 for failure |
| message | string | Human-readable message |
| total_categories | integer | Total number of categories |
| data | array | Array of category objects |
| timestamp | string | Server timestamp |

**Category Object:**
| Field | Type | Description |
|-------|------|-------------|
| category_id | integer | Unique category identifier |
| category_name | string | Category name |
| is_active | string | Active status (yes/no) |
| created_at | string | Creation timestamp |
| updated_at | string | Last update date (nullable) |

---

## 2. Get Single Category

Retrieve details of a specific category by ID.

### Request

**Endpoint:** `POST /teacher/student-category/get`  
**URL:** `http://localhost/amt/api/teacher/student-category/get`

**Request Body:**
```json
{
  "category_id": 5
}
```

**Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| category_id | integer | Yes | Category ID to retrieve |

### Response

**Success (HTTP 200):**
```json
{
  "status": 1,
  "message": "Student category retrieved successfully",
  "data": {
    "category_id": 5,
    "category_name": "OBC",
    "is_active": "no",
    "created_at": "2023-08-12 00:20:13",
    "updated_at": null
  },
  "timestamp": "2025-10-05 00:10:18"
}
```

**Error - Not Found (HTTP 404):**
```json
{
  "status": 0,
  "message": "Student category not found",
  "timestamp": "2025-10-05 00:10:18"
}
```

**Error - Missing Parameter (HTTP 400):**
```json
{
  "status": 0,
  "message": "category_id is required",
  "timestamp": "2025-10-05 00:10:18"
}
```

---

## 3. Create Category

Create a new student category.

### Request

**Endpoint:** `POST /teacher/student-category/create`  
**URL:** `http://localhost/amt/api/teacher/student-category/create`

**Request Body:**
```json
{
  "category_name": "New Category",
  "is_active": "yes"
}
```

**Parameters:**
| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| category_name | string | Yes | - | Category name (unique) |
| is_active | string | No | "no" | Active status (yes/no) |

### Response

**Success (HTTP 201):**
```json
{
  "status": 1,
  "message": "Student category created successfully",
  "data": {
    "category_id": 92,
    "category_name": "New Category",
    "is_active": "yes",
    "created_at": "2025-10-05 00:10:18"
  },
  "timestamp": "2025-10-05 00:10:18"
}
```

**Error - Duplicate Name (HTTP 409):**
```json
{
  "status": 0,
  "message": "Category with this name already exists",
  "timestamp": "2025-10-05 00:10:18"
}
```

**Error - Missing Parameter (HTTP 400):**
```json
{
  "status": 0,
  "message": "category_name is required",
  "timestamp": "2025-10-05 00:10:18"
}
```

### Validation Rules

- `category_name` must not be empty
- `category_name` must be unique (case-sensitive)
- `is_active` must be "yes" or "no"
- Leading/trailing whitespace is automatically trimmed

---

## 4. Update Category

Update an existing student category.

### Request

**Endpoint:** `POST /teacher/student-category/update`  
**URL:** `http://localhost/amt/api/teacher/student-category/update`

**Request Body:**
```json
{
  "category_id": 5,
  "category_name": "Updated Name",
  "is_active": "yes"
}
```

**Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| category_id | integer | Yes | Category ID to update |
| category_name | string | No | New category name |
| is_active | string | No | New active status (yes/no) |

**Note:** At least one of `category_name` or `is_active` must be provided.

### Response

**Success (HTTP 200):**
```json
{
  "status": 1,
  "message": "Student category updated successfully",
  "data": {
    "category_id": 5,
    "category_name": "Updated Name",
    "is_active": "yes",
    "created_at": "2023-08-12 00:20:13",
    "updated_at": "2025-10-05"
  },
  "timestamp": "2025-10-05 00:10:18"
}
```

**Error - Not Found (HTTP 404):**
```json
{
  "status": 0,
  "message": "Student category not found",
  "timestamp": "2025-10-05 00:10:18"
}
```

**Error - Duplicate Name (HTTP 409):**
```json
{
  "status": 0,
  "message": "Category with this name already exists",
  "timestamp": "2025-10-05 00:10:18"
}
```

**Error - No Fields to Update (HTTP 400):**
```json
{
  "status": 0,
  "message": "No valid fields to update",
  "timestamp": "2025-10-05 00:10:18"
}
```

### Validation Rules

- `category_id` must exist
- If updating `category_name`, it must be unique (excluding current category)
- `is_active` must be "yes" or "no"
- `updated_at` is automatically set to current date

---

## 5. Delete Category

Delete a student category.

### Request

**Endpoint:** `POST /teacher/student-category/delete`  
**URL:** `http://localhost/amt/api/teacher/student-category/delete`

**Request Body:**
```json
{
  "category_id": 5
}
```

**Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| category_id | integer | Yes | Category ID to delete |

### Response

**Success (HTTP 200):**
```json
{
  "status": 1,
  "message": "Student category deleted successfully",
  "category_id": 5,
  "timestamp": "2025-10-05 00:10:18"
}
```

**Error - Not Found (HTTP 404):**
```json
{
  "status": 0,
  "message": "Student category not found",
  "timestamp": "2025-10-05 00:10:18"
}
```

**Error - Category In Use (HTTP 409):**
```json
{
  "status": 0,
  "message": "Cannot delete category. It is being used by 25 student(s)",
  "students_count": 25,
  "timestamp": "2025-10-05 00:10:18"
}
```

### Validation Rules

- `category_id` must exist
- Category cannot be deleted if assigned to any students
- Returns count of students using the category if deletion fails

---

## Error Handling

### Common Error Responses

**Invalid JSON (HTTP 400):**
```json
{
  "status": 0,
  "message": "Invalid JSON format in request body",
  "error": {
    "type": "JSON Parse Error",
    "details": "Syntax error"
  },
  "timestamp": "2025-10-05 00:10:18"
}
```

**Internal Server Error (HTTP 500):**
```json
{
  "status": 0,
  "message": "An error occurred while processing request",
  "error": {
    "type": "Exception",
    "message": "Error details",
    "file": "filename.php",
    "line": 123
  },
  "timestamp": "2025-10-05 00:10:18"
}
```

### HTTP Status Codes

| Code | Meaning | When Used |
|------|---------|-----------|
| 200 | OK | Successful GET, UPDATE, DELETE |
| 201 | Created | Successful CREATE |
| 400 | Bad Request | Validation errors, missing fields |
| 404 | Not Found | Category ID doesn't exist |
| 409 | Conflict | Duplicate name, category in use |
| 500 | Internal Server Error | Database errors, exceptions |

---

## Testing

Use the provided test script to verify all endpoints:

```bash
C:\xampp\php\php.exe test_student_categories_api.php
```

The test script covers:
- All CRUD operations
- Error handling scenarios
- Validation rules
- Duplicate detection
- Relationship checking

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0 | 2025-10-05 | Initial implementation with full CRUD |

---

## Support

For issues or questions regarding this API, please contact the development team.

