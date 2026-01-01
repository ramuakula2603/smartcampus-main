# Subjects API - Quick Reference Guide

## Base URL
```
http://localhost/amt/api/
```

## Authentication Headers (Required for all requests)
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

---

## Endpoints Summary

| Operation | Method | Endpoint | URL |
|-----------|--------|----------|-----|
| List All | POST | `/subjects/list` | `http://localhost/amt/api/subjects/list` |
| Get One | POST | `/subjects/get/{id}` | `http://localhost/amt/api/subjects/get/1` |
| Create | POST | `/subjects/create` | `http://localhost/amt/api/subjects/create` |
| Update | POST | `/subjects/update/{id}` | `http://localhost/amt/api/subjects/update/1` |
| Delete | POST | `/subjects/delete/{id}` | `http://localhost/amt/api/subjects/delete/1` |

---

## Quick Examples

### 1. List All Subjects
```bash
curl -X POST "http://localhost/amt/api/subjects/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Response:**
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
    }
  ]
}
```

---

### 2. Get Single Subject
```bash
curl -X POST "http://localhost/amt/api/subjects/get/1" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Response:**
```json
{
  "status": 1,
  "message": "Subject record retrieved successfully",
  "data": {
    "id": 1,
    "name": "Mathematics",
    "code": "MATH",
    "is_active": "yes",
    "created_at": "2024-01-15 10:30:00",
    "updated_at": "2024-01-15 10:30:00"
  }
}
```

---

### 3. Create Subject
```bash
curl -X POST "http://localhost/amt/api/subjects/create" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "name": "Physics",
    "code": "PHY",
    "is_active": "yes"
  }'
```

**Response (HTTP 201):**
```json
{
  "status": 1,
  "message": "Subject created successfully",
  "data": {
    "id": 6,
    "name": "Physics",
    "code": "PHY",
    "is_active": "yes",
    "created_at": "2024-01-20 14:30:00"
  }
}
```

---

### 4. Update Subject
```bash
curl -X POST "http://localhost/amt/api/subjects/update/1" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "name": "Advanced Mathematics",
    "code": "MATH-ADV",
    "is_active": "yes"
  }'
```

**Response:**
```json
{
  "status": 1,
  "message": "Subject updated successfully",
  "data": {
    "id": 1,
    "name": "Advanced Mathematics",
    "code": "MATH-ADV",
    "is_active": "yes",
    "updated_at": "2024-01-20 15:45:00"
  }
}
```

---

### 5. Delete Subject
```bash
curl -X POST "http://localhost/amt/api/subjects/delete/1" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Response:**
```json
{
  "status": 1,
  "message": "Subject deleted successfully",
  "data": {
    "id": 1,
    "name": "Mathematics",
    "code": "MATH"
  }
}
```

---

## Request/Response Fields

### Create/Update Request Body
```json
{
  "name": "Subject Name",        // Required: Subject name
  "code": "SUBJ",                // Optional: Subject code
  "is_active": "yes"             // Optional: Active status (default: "yes")
}
```

### Response Data Fields
```json
{
  "id": 1,                       // Subject ID
  "name": "Subject Name",        // Subject name
  "code": "SUBJ",                // Subject code
  "is_active": "yes",            // Active status
  "created_at": "timestamp",     // Creation timestamp
  "updated_at": "timestamp"      // Last update timestamp
}
```

---

## HTTP Status Codes

| Code | Meaning | Scenario |
|------|---------|----------|
| 200 | OK | Successful GET, UPDATE, DELETE |
| 201 | Created | Successful CREATE |
| 400 | Bad Request | Invalid input or missing required fields |
| 401 | Unauthorized | Invalid authentication headers |
| 404 | Not Found | Subject record not found |
| 405 | Method Not Allowed | Wrong HTTP method (must be POST) |
| 500 | Server Error | Internal server error |

---

## Error Response Examples

### Missing Required Field
```json
{
  "status": 0,
  "message": "Subject name is required and cannot be empty",
  "data": null
}
```

### Invalid Authentication
```json
{
  "status": 0,
  "message": "Unauthorized access. Invalid headers.",
  "data": null
}
```

### Record Not Found
```json
{
  "status": 0,
  "message": "Subject record not found",
  "data": null
}
```

### Wrong HTTP Method
```json
{
  "status": 0,
  "message": "Method not allowed. Use POST method.",
  "data": null
}
```

---

## Common Use Cases

### Populate Subject Dropdown
```bash
# Get all subjects for dropdown menu
curl -X POST "http://localhost/amt/api/subjects/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Add New Subject
```bash
# Create a new subject
curl -X POST "http://localhost/amt/api/subjects/create" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"name": "Chemistry", "code": "CHEM"}'
```

### Edit Subject
```bash
# Update subject details
curl -X POST "http://localhost/amt/api/subjects/update/1" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"name": "Organic Chemistry", "code": "CHEM-ORG"}'
```

### Deactivate Subject
```bash
# Disable a subject without deleting
curl -X POST "http://localhost/amt/api/subjects/update/1" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"name": "Mathematics", "is_active": "no"}'
```

---

## Implementation Files

- **Controller:** `api/application/controllers/Subjects_api.php`
- **Routes:** `api/application/config/routes.php` (lines 190-195)
- **Documentation:** `api/documentation/SUBJECTS_API_DOCUMENTATION.md`
- **Summary:** `api/documentation/SUBJECTS_API_IMPLEMENTATION_SUMMARY.md`

---

## Key Points

✅ All endpoints require POST method
✅ All endpoints require authentication headers
✅ Subject name is required for create/update
✅ Subject code is optional but recommended
✅ is_active defaults to "yes" for new subjects
✅ Consistent response format across all endpoints
✅ Comprehensive error handling and validation
✅ Audit logging for all operations

---

## Support

For detailed documentation, see: `api/documentation/SUBJECTS_API_DOCUMENTATION.md`

