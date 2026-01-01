# Student Categories API - Quick Reference

## Headers (All Endpoints)

```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

---

## 1. Get All Categories

```
POST /teacher/student-categories
```

**Request:**
```json
{}
```

**Response:**
```json
{
  "status": 1,
  "total_categories": 86,
  "data": [
    {
      "category_id": 1,
      "category_name": "General",
      "is_active": "no"
    }
  ]
}
```

---

## 2. Get Single Category

```
POST /teacher/student-category/get
```

**Request:**
```json
{
  "category_id": 5
}
```

**Response:**
```json
{
  "status": 1,
  "data": {
    "category_id": 5,
    "category_name": "OBC",
    "is_active": "no"
  }
}
```

---

## 3. Create Category

```
POST /teacher/student-category/create
```

**Request:**
```json
{
  "category_name": "New Category",
  "is_active": "yes"  // Optional, defaults to "no"
}
```

**Response (HTTP 201):**
```json
{
  "status": 1,
  "message": "Student category created successfully",
  "data": {
    "category_id": 92,
    "category_name": "New Category",
    "is_active": "yes"
  }
}
```

---

## 4. Update Category

```
POST /teacher/student-category/update
```

**Request:**
```json
{
  "category_id": 5,
  "category_name": "Updated Name",  // Optional
  "is_active": "yes"                // Optional
}
```

**Response:**
```json
{
  "status": 1,
  "message": "Student category updated successfully",
  "data": {
    "category_id": 5,
    "category_name": "Updated Name",
    "is_active": "yes",
    "updated_at": "2025-10-05"
  }
}
```

---

## 5. Delete Category

```
POST /teacher/student-category/delete
```

**Request:**
```json
{
  "category_id": 5
}
```

**Response:**
```json
{
  "status": 1,
  "message": "Student category deleted successfully",
  "category_id": 5
}
```

**Error (In Use - HTTP 409):**
```json
{
  "status": 0,
  "message": "Cannot delete category. It is being used by 25 student(s)",
  "students_count": 25
}
```

---

## cURL Examples

### Get All
```bash
curl -X POST http://localhost/amt/api/teacher/student-categories \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Get Single
```bash
curl -X POST http://localhost/amt/api/teacher/student-category/get \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"category_id": 5}'
```

### Create
```bash
curl -X POST http://localhost/amt/api/teacher/student-category/create \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"category_name": "New Category", "is_active": "yes"}'
```

### Update
```bash
curl -X POST http://localhost/amt/api/teacher/student-category/update \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"category_id": 5, "category_name": "Updated", "is_active": "yes"}'
```

### Delete
```bash
curl -X POST http://localhost/amt/api/teacher/student-category/delete \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"category_id": 5}'
```

---

## HTTP Status Codes

| Code | Meaning | When |
|------|---------|------|
| 200 | OK | Success (GET, UPDATE, DELETE) |
| 201 | Created | Success (CREATE) |
| 400 | Bad Request | Validation error |
| 404 | Not Found | Category doesn't exist |
| 409 | Conflict | Duplicate or in use |
| 500 | Server Error | Database/exception error |

---

## Validation Rules

### Create
- ✅ `category_name` required, non-empty
- ✅ `category_name` must be unique
- ✅ `is_active` must be "yes" or "no"

### Update
- ✅ `category_id` required
- ✅ At least one field to update
- ✅ New `category_name` must be unique
- ✅ `is_active` must be "yes" or "no"

### Delete
- ✅ `category_id` required
- ✅ Category must not be assigned to students

---

## Common Errors

**Missing Required Field:**
```json
{
  "status": 0,
  "message": "category_id is required"
}
```

**Duplicate Name:**
```json
{
  "status": 0,
  "message": "Category with this name already exists"
}
```

**Not Found:**
```json
{
  "status": 0,
  "message": "Student category not found"
}
```

**In Use:**
```json
{
  "status": 0,
  "message": "Cannot delete category. It is being used by 25 student(s)",
  "students_count": 25
}
```

---

## Test Script

```bash
C:\xampp\php\php.exe test_student_categories_api.php
```

**Tests:**
- ✅ Get all categories
- ✅ Create category
- ✅ Get single category
- ✅ Update category
- ✅ Delete category
- ✅ Duplicate detection
- ✅ Invalid ID handling
- ✅ Required field validation

---

## Quick Tips

1. **Category Names** are case-sensitive
2. **is_active** defaults to "no" if not specified
3. **Whitespace** is automatically trimmed from names
4. **Deletion** is prevented if category is in use
5. **updated_at** is automatically set on updates

---

**Version:** 1.0  
**Last Updated:** October 5, 2025

