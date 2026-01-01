# Student Report API - Quick Reference

## Base URL
```
http://localhost/amt/api/student-report/
```

## Authentication Headers (Required for all requests)
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

---

## Endpoints

### 1. Filter Student Report
**URL:** `POST /student-report/filter`

**Quick Examples:**

**All students (no filters):**
```json
{}
```

**Filter by class:**
```json
{
  "class_id": 1
}
```

**Filter by multiple classes:**
```json
{
  "class_id": [1, 2, 3]
}
```

**Filter by class and section:**
```json
{
  "class_id": 1,
  "section_id": 2
}
```

**Filter by class, section, and category:**
```json
{
  "class_id": 1,
  "section_id": 2,
  "category_id": 3
}
```

**Filter by specific session:**
```json
{
  "class_id": 1,
  "session_id": 18
}
```

---

### 2. List All Students
**URL:** `POST /student-report/list`

**Request:**
```json
{}
```

---

## Response Format

**Success Response:**
```json
{
  "status": 1,
  "message": "Student report retrieved successfully",
  "filters_applied": {
    "class_id": [1],
    "section_id": [2],
    "session_id": 18
  },
  "total_records": 25,
  "data": [
    {
      "id": 1,
      "admission_no": "2024001",
      "roll_no": "101",
      "firstname": "John",
      "middlename": "Michael",
      "lastname": "Doe",
      "class_id": 1,
      "class": "Class 10",
      "section_id": 2,
      "section": "A",
      "category_id": 3,
      "category": "General",
      "father_name": "Robert Doe",
      "dob": "2010-05-15",
      "gender": "Male",
      "mobileno": "9876543210",
      "email": "john.doe@example.com",
      "samagra_id": "123456789",
      "adhar_no": "123412341234",
      "rte": "No",
      "guardian_name": "Robert Doe",
      "guardian_phone": "9876543210",
      "guardian_relation": "Father",
      "current_address": "123 Main Street, City",
      "permanent_address": "123 Main Street, City",
      "is_active": "yes"
    }
  ],
  "timestamp": "2025-10-07 10:30:45"
}
```

---

## cURL Commands

**Filter by class:**
```bash
curl -X POST "http://localhost/amt/api/student-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": 1}'
```

**All students:**
```bash
curl -X POST "http://localhost/amt/api/student-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**List endpoint:**
```bash
curl -X POST "http://localhost/amt/api/student-report/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

## Key Features

✅ **Graceful null/empty handling** - Empty filters return all records
✅ **Multi-select support** - Pass arrays for multiple values
✅ **Session handling** - Auto-uses current session if not specified
✅ **Consistent with existing APIs** - Same patterns as Disable Reason and Fee Master APIs
✅ **Comprehensive error handling** - Clear error messages

---

## Common Use Cases

| Use Case | Request Body |
|----------|--------------|
| All students | `{}` |
| Students in a class | `{"class_id": 1}` |
| Students in multiple classes | `{"class_id": [1, 2, 3]}` |
| Students in a class and section | `{"class_id": 1, "section_id": 2}` |
| Students by category | `{"category_id": 3}` |
| Students in specific session | `{"session_id": 18}` |
| Complex filter | `{"class_id": [1, 2], "section_id": [1, 2], "category_id": 3}` |

---

## Testing Checklist

- [ ] Test with no filters (should return all students)
- [ ] Test with single class filter
- [ ] Test with multiple class filters (array)
- [ ] Test with class and section filters
- [ ] Test with category filter
- [ ] Test with session_id filter
- [ ] Test with null values (should return all students)
- [ ] Test with empty arrays (should return all students)
- [ ] Test authentication (wrong headers should fail)
- [ ] Test wrong HTTP method (GET should fail)
- [ ] Verify response format matches documentation
- [ ] Verify all fields are present in response
- [ ] Test list endpoint

---

## Response Fields

| Field | Type | Description |
|-------|------|-------------|
| id | integer | Student ID |
| admission_no | string | Admission number |
| roll_no | string | Roll number |
| firstname | string | First name |
| middlename | string | Middle name |
| lastname | string | Last name |
| class_id | integer | Class ID |
| class | string | Class name |
| section_id | integer | Section ID |
| section | string | Section name |
| category_id | integer | Category ID |
| category | string | Category name |
| father_name | string | Father's name |
| dob | date | Date of birth |
| gender | string | Gender |
| mobileno | string | Mobile number |
| email | string | Email |
| samagra_id | string | Samagra ID |
| adhar_no | string | Aadhar number |
| rte | string | RTE status |
| guardian_name | string | Guardian name |
| guardian_phone | string | Guardian phone |
| guardian_relation | string | Guardian relation |
| current_address | string | Current address |
| permanent_address | string | Permanent address |
| is_active | string | Active status |

---

## Error Codes

| Code | Message | Cause |
|------|---------|-------|
| 400 | Bad request. Only POST method allowed. | Wrong HTTP method |
| 401 | Unauthorized. | Invalid authentication headers |
| 500 | Internal server error occurred | Server/database error |

