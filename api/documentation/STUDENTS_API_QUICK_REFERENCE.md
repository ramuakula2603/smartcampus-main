# Students API - Quick Reference

## Endpoint

```
POST /teacher/students
```

## Headers

```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

## Request Examples

### Get All Students
```json
{}
```

### Filter by Class
```json
{
  "class_id": 19
}
```

### Filter by Class and Section
```json
{
  "class_id": 19,
  "section_id": 47
}
```

### Filter by All Parameters
```json
{
  "class_id": 19,
  "section_id": 47,
  "session_id": 21
}
```

## Response Structure

```json
{
  "status": 1,
  "message": "Students retrieved successfully",
  "filters_applied": {
    "class_id": 19,
    "section_id": 47,
    "session_id": 21
  },
  "total_students": 42,
  "data": [
    {
      "student_id": 1552,
      "student_session_id": 1555,
      "admission_no": "202488",
      "roll_no": "12345",
      "full_name": "John Doe Smith",
      "firstname": "John",
      "middlename": "Doe",
      "lastname": "Smith",
      "dob": "2009-06-13",
      "gender": "Male",
      "email": "john@example.com",
      "mobileno": "6302585701",
      "blood_group": "O+",
      "profile_image": "http://localhost/amt/api/uploads/student_images/1552.jpg?1759602877",
      "class_info": {
        "class_id": 19,
        "class_name": "SR-MPC",
        "section_id": 47,
        "section_name": "2025-26 SR SPARK",
        "session_id": 21,
        "session_name": "2025-26"
      },
      "guardian_info": {
        "father_name": "Robert Smith",
        "father_phone": "6302585701",
        "mother_name": "Mary Smith",
        "mother_phone": "6302585702",
        "guardian_name": "Robert Smith",
        "guardian_phone": "6302585701",
        "guardian_relation": "Father"
      },
      "address_info": {
        "current_address": "123 Main Street",
        "permanent_address": "456 Oak Avenue"
      },
      "category_id": "15",
      "is_active": "yes"
    }
  ],
  "timestamp": "2025-10-05 00:04:37"
}
```

## Key Features

✅ **Optional Filtering** - All parameters are optional  
✅ **Flexible Combinations** - Use any combination of filters  
✅ **Complete Data** - Student, class, guardian, and address info  
✅ **Profile Images** - With cache-busting timestamps  
✅ **Active Students Only** - Automatically filters inactive students  
✅ **Sorted Results** - Ordered by firstname (ASC)

## cURL Example

```bash
curl -X POST http://localhost/amt/api/teacher/students \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": 19, "section_id": 47, "session_id": 21}'
```

## Test Script

```bash
C:\xampp\php\php.exe test_students_api.php
```

## Common Use Cases

| Use Case | Request Body |
|----------|--------------|
| All students | `{}` |
| Students in a class | `{"class_id": 19}` |
| Students in a section | `{"class_id": 19, "section_id": 47}` |
| Students in current session | `{"session_id": 21}` |
| Specific class/section/session | `{"class_id": 19, "section_id": 47, "session_id": 21}` |

## Response Fields Summary

### Student Data
- student_id, admission_no, roll_no
- full_name, firstname, middlename, lastname
- dob, gender, email, mobileno, blood_group
- profile_image, category_id, is_active

### Class Info
- class_id, class_name
- section_id, section_name
- session_id, session_name

### Guardian Info
- father_name, father_phone
- mother_name, mother_phone
- guardian_name, guardian_phone, guardian_relation

### Address Info
- current_address
- permanent_address

## Error Response

```json
{
  "status": 0,
  "message": "Error message",
  "error": {
    "type": "Error Type",
    "details": "Error details"
  },
  "timestamp": "2025-10-05 00:04:37"
}
```

## Status Codes

| Status | Meaning |
|--------|---------|
| 1 | Success |
| 0 | Error/Failure |

---

**Version:** 1.0  
**Last Updated:** October 5, 2025

