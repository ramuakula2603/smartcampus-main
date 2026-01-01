# Students API Documentation

## Overview

The Students API endpoint provides comprehensive student information with optional filtering by class, section, and academic session. This endpoint is designed for retrieving student lists for various purposes such as attendance management, grade reporting, and student directory displays.

---

## Endpoint Information

### Get Students List

Retrieve a list of students with optional filtering by class, section, and session.

**Endpoint:** `/teacher/students`  
**Method:** `POST`  
**Content-Type:** `application/json`  
**Authentication:** Required (Client-Service and Auth-Key headers)

---

## Request

### Headers

| Header | Value | Required | Description |
|--------|-------|----------|-------------|
| Content-Type | application/json | Yes | Request content type |
| Client-Service | smartschool | Yes | Client service identifier |
| Auth-Key | schoolAdmin@ | Yes | Authentication key |

### Request Body

All parameters are **optional**. If no parameters are provided, all active students will be returned.

```json
{
  "class_id": 19,      // Optional: Filter by class ID
  "section_id": 47,    // Optional: Filter by section ID
  "session_id": 21     // Optional: Filter by session ID
}
```

#### Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| class_id | integer | No | Filter students by class ID |
| section_id | integer | No | Filter students by section ID |
| session_id | integer | No | Filter students by academic session ID |

**Filter Behavior:**
- All filters are optional and can be used independently or in combination
- Null or missing parameters are ignored
- Only students with `is_active = 'yes'` are returned
- Results are sorted by firstname in ascending order

---

## Response

### Success Response (HTTP 200)

```json
{
  "status": 1,
  "message": "Students retrieved successfully",
  "filters_applied": {
    "class_id": 19,
    "section_id": 47,
    "session_id": 21
  },
  "total_students": 125,
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
      "email": "john.smith@example.com",
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
        "current_address": "123 Main Street, City, State",
        "permanent_address": "456 Oak Avenue, Town, State"
      },
      "category_id": "15",
      "is_active": "yes"
    }
  ],
  "timestamp": "2025-10-05 00:04:37"
}
```

### Response Fields

#### Root Level

| Field | Type | Description |
|-------|------|-------------|
| status | integer | 1 for success, 0 for failure |
| message | string | Human-readable status message |
| filters_applied | object | Shows which filters were applied |
| total_students | integer | Total number of students returned |
| data | array | Array of student objects |
| timestamp | string | Server timestamp (YYYY-MM-DD HH:MM:SS) |

#### Student Object

| Field | Type | Description |
|-------|------|-------------|
| student_id | integer | Unique student identifier |
| student_session_id | integer | Student session record ID |
| admission_no | string | Student admission number |
| roll_no | string | Student roll number |
| full_name | string | Complete name (computed) |
| firstname | string | First name |
| middlename | string | Middle name (nullable) |
| lastname | string | Last name |
| dob | string | Date of birth (YYYY-MM-DD) |
| gender | string | Gender (Male/Female) |
| email | string | Email address |
| mobileno | string | Mobile phone number |
| blood_group | string | Blood group |
| profile_image | string | Full URL to profile image with timestamp |
| class_info | object | Class and section information |
| guardian_info | object | Parent/guardian information |
| address_info | object | Address information |
| category_id | string | Student category ID |
| is_active | string | Active status (yes/no) |

#### class_info Object

| Field | Type | Description |
|-------|------|-------------|
| class_id | integer | Class ID |
| class_name | string | Class name |
| section_id | integer | Section ID |
| section_name | string | Section name |
| session_id | integer | Academic session ID |
| session_name | string | Academic session name |

#### guardian_info Object

| Field | Type | Description |
|-------|------|-------------|
| father_name | string | Father's name |
| father_phone | string | Father's phone number |
| mother_name | string | Mother's name |
| mother_phone | string | Mother's phone number |
| guardian_name | string | Primary guardian name |
| guardian_phone | string | Primary guardian phone |
| guardian_relation | string | Guardian relationship (Father/Mother/Other) |

#### address_info Object

| Field | Type | Description |
|-------|------|-------------|
| current_address | string | Current residential address |
| permanent_address | string | Permanent address |

### Error Response (HTTP 200)

```json
{
  "status": 0,
  "message": "Error message describing the issue",
  "error": {
    "type": "Error Type",
    "details": "Detailed error information"
  },
  "timestamp": "2025-10-05 00:04:37"
}
```

---

## Usage Examples

### Example 1: Get All Students

**Request:**
```bash
curl -X POST http://localhost/amt/api/teacher/students \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Response:**
```json
{
  "status": 1,
  "message": "Students retrieved successfully",
  "filters_applied": {
    "class_id": null,
    "section_id": null,
    "session_id": null
  },
  "total_students": 2490,
  "data": [ /* array of all active students */ ]
}
```

### Example 2: Filter by Class Only

**Request:**
```bash
curl -X POST http://localhost/amt/api/teacher/students \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": 19}'
```

**Response:**
```json
{
  "status": 1,
  "message": "Students retrieved successfully",
  "filters_applied": {
    "class_id": 19,
    "section_id": null,
    "session_id": null
  },
  "total_students": 450,
  "data": [ /* array of students in class 19 */ ]
}
```

### Example 3: Filter by Class and Section

**Request:**
```bash
curl -X POST http://localhost/amt/api/teacher/students \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": 19, "section_id": 47}'
```

**Response:**
```json
{
  "status": 1,
  "message": "Students retrieved successfully",
  "filters_applied": {
    "class_id": 19,
    "section_id": 47,
    "session_id": null
  },
  "total_students": 45,
  "data": [ /* array of students in class 19, section 47 */ ]
}
```

### Example 4: Filter by All Parameters

**Request:**
```bash
curl -X POST http://localhost/amt/api/teacher/students \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": 19, "section_id": 47, "session_id": 21}'
```

**Response:**
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
  "data": [ /* array of students matching all filters */ ]
}
```

---

## Error Handling

### Common Error Scenarios

1. **Invalid JSON Format**
```json
{
  "status": 0,
  "message": "Invalid JSON format in request body",
  "error": {
    "type": "JSON Parse Error"
  }
}
```

2. **Database Connection Error**
```json
{
  "status": 0,
  "message": "Database connection failed",
  "error": {
    "type": "Database Error",
    "details": "Connection timeout"
  }
}
```

3. **Query Execution Error**
```json
{
  "status": 0,
  "message": "Failed to retrieve students",
  "error": {
    "type": "Query Error",
    "details": "SQL syntax error"
  }
}
```

---

## Notes

1. **Profile Images:**
   - Returns actual student image if exists
   - Falls back to gender-based default images (default_male.jpg / default_female.jpg)
   - Includes timestamp parameter for cache busting

2. **Performance:**
   - Efficient database joins minimize query overhead
   - Results are sorted by firstname for consistent ordering
   - Consider pagination for large result sets (future enhancement)

3. **Data Privacy:**
   - Ensure proper authentication before accessing student data
   - Sensitive information should be handled according to privacy policies

4. **Active Students Only:**
   - Only students with `is_active = 'yes'` are returned
   - Inactive students are automatically excluded

---

## Testing

Use the provided test script to verify the endpoint:

```bash
C:\xampp\php\php.exe test_students_api.php
```

The test script includes:
- Test with no filters (all students)
- Test with class_id filter only
- Test with class_id and section_id filters
- Test with all filters (class_id, section_id, session_id)

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0 | 2025-10-05 | Initial implementation |

---

## Support

For issues or questions regarding this API endpoint, please contact the development team.

