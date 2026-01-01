# Student Report API Documentation

## Overview

The Student Report API provides endpoints for retrieving student report data with flexible filtering capabilities. This API allows you to filter students by class, section, session, and category. It handles null/empty filter parameters gracefully by returning all records when filters are not provided, treating empty filters the same as a list endpoint.

---

## Base URL
```
http://{domain}/api/
```

## Important URL Structure

**For Student Report APIs, use the controller/method pattern:**
- Filter students: `http://{domain}/api/student-report/filter`
- List all students: `http://{domain}/api/student-report/list`

**Examples:**
- Filter: `http://localhost/amt/api/student-report/filter`
- List all: `http://localhost/amt/api/student-report/list`

## Authentication Headers

All API requests require the following headers:
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

---

## Endpoints

### 1. Filter Student Report

**Endpoint:** `POST /student-report/filter`
**Full URL:** `http://localhost/amt/api/student-report/filter`

**Description:** Retrieve student report data with optional filtering by class, section, session, and category. All filter parameters are optional. If no filters are provided or filters are empty, the API returns all active students for the current session.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### Request Body (All parameters are optional)
```json
{
  "class_id": 1,
  "section_id": 2,
  "session_id": 18,
  "category_id": 3
}
```

**Note:** All filter parameters support both single values and arrays for multi-select functionality:

**Single value example:**
```json
{
  "class_id": 1,
  "section_id": 2
}
```

**Multiple values example:**
```json
{
  "class_id": [1, 2, 3],
  "section_id": [1, 2],
  "category_id": [1, 2]
}
```

**Empty/No filters example (returns all students):**
```json
{}
```

or

```json
{
  "class_id": null,
  "section_id": null,
  "category_id": null
}
```

#### Request Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| class_id | integer or array | No | Filter by class ID. Can be a single ID or array of IDs. If empty/null, returns all classes. |
| section_id | integer or array | No | Filter by section ID. Can be a single ID or array of IDs. If empty/null, returns all sections. |
| session_id | integer | No | Filter by session ID. If not provided, uses current session. |
| category_id | integer or array | No | Filter by category ID. Can be a single ID or array of IDs. If empty/null, returns all categories. |

#### Success Response (HTTP 200)
```json
{
  "status": 1,
  "message": "Student report retrieved successfully",
  "filters_applied": {
    "class_id": [1],
    "section_id": [2],
    "session_id": 18,
    "category_id": [3]
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

#### Success Response with No Filters (Returns all students)
```json
{
  "status": 1,
  "message": "Student report retrieved successfully",
  "filters_applied": {
    "session_id": 18
  },
  "total_records": 150,
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

#### Error Response (HTTP 400)
```json
{
  "status": 0,
  "message": "Bad request. Only POST method allowed."
}
```

#### Error Response (HTTP 401)
```json
{
  "status": 0,
  "message": "Unauthorized."
}
```

#### Error Response (HTTP 500)
```json
{
  "status": 0,
  "message": "Internal server error occurred",
  "error": "Error details here",
  "data": null
}
```

#### cURL Example - With Filters
```bash
curl -X POST "http://localhost/amt/api/student-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "class_id": 1,
    "section_id": 2,
    "session_id": 18,
    "category_id": 3
  }'
```


#### cURL Example - Multiple Values
```bash
curl -X POST "http://localhost/amt/api/student-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "class_id": [1, 2, 3],
    "section_id": [1, 2]
  }'
```

#### cURL Example - No Filters (Returns all students)
```bash
curl -X POST "http://localhost/amt/api/student-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

#### JavaScript/Fetch Example
```javascript
fetch('http://localhost/amt/api/student-report/filter', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Client-Service': 'smartschool',
    'Auth-Key': 'schoolAdmin@'
  },
  body: JSON.stringify({
    class_id: 1,
    section_id: 2,
    session_id: 18,
    category_id: 3
  })
})
.then(response => response.json())
.then(data => console.log(data))
.catch(error => console.error('Error:', error));
```

---

### 2. List All Students

**Endpoint:** `POST /student-report/list`
**Full URL:** `http://localhost/amt/api/student-report/list`

**Description:** Retrieve all active students for the current session without any filters.

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

**Note:** This endpoint does not accept any parameters. It returns all active students for the current session.

#### Success Response (HTTP 200)
```json
{
  "status": 1,
  "message": "Student report retrieved successfully",
  "session_id": 18,
  "total_records": 150,
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

#### cURL Example
```bash
curl -X POST "http://localhost/amt/api/student-report/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

## Response Field Descriptions

| Field | Type | Description |
|-------|------|-------------|
| id | integer | Unique student ID |
| admission_no | string | Student admission number |
| roll_no | string | Student roll number |
| firstname | string | Student's first name |
| middlename | string | Student's middle name |
| lastname | string | Student's last name |
| class_id | integer | Class ID |
| class | string | Class name |
| section_id | integer | Section ID |
| section | string | Section name |
| category_id | integer | Category ID (nullable) |
| category | string | Category name (nullable) |
| father_name | string | Father's name |
| dob | date | Date of birth (YYYY-MM-DD) |
| gender | string | Gender (Male/Female) |
| mobileno | string | Mobile number |
| email | string | Email address |
| samagra_id | string | Samagra ID (nullable) |
| adhar_no | string | Aadhar number (nullable) |
| rte | string | RTE status (nullable) |
| guardian_name | string | Guardian's name |
| guardian_phone | string | Guardian's phone |
| guardian_relation | string | Guardian's relation (nullable) |
| current_address | string | Current address (nullable) |
| permanent_address | string | Permanent address (nullable) |
| is_active | string | Active status (yes/no) |

---

## Important Notes

### Graceful Handling of Empty/Null Parameters

This API is designed to handle null/empty filter parameters gracefully:

1. **Empty Request Body**: If you send an empty request body `{}`, the API returns all active students for the current session.

2. **Null Parameters**: If you send `null` values for filter parameters, they are ignored:
   ```json
   {
     "class_id": null,
     "section_id": null
   }
   ```
   This behaves the same as sending an empty request body.

3. **Empty Arrays**: If you send empty arrays, they are treated as no filter:
   ```json
   {
     "class_id": [],
     "section_id": []
   }
   ```

4. **Mixed Filters**: You can provide some filters and leave others empty:
   ```json
   {
     "class_id": 1,
     "section_id": null
   }
   ```
   This returns all students in class 1, regardless of section.

### Session Handling

- If `session_id` is not provided, the API automatically uses the current active session from the system settings.
- You can explicitly provide a `session_id` to query students from a specific session.

### Multi-Select Support

All filter parameters (except `session_id`) support both single values and arrays:
- Single value: `"class_id": 1`
- Multiple values: `"class_id": [1, 2, 3]`

---

## Testing Guide

### Test Case 1: Filter by Single Class
```bash
curl -X POST "http://localhost/amt/api/student-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": 1}'
```

### Test Case 2: Filter by Multiple Classes
```bash
curl -X POST "http://localhost/amt/api/student-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": [1, 2, 3]}'
```

### Test Case 3: Filter by Class and Section
```bash
curl -X POST "http://localhost/amt/api/student-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": 1, "section_id": 2}'
```

### Test Case 4: No Filters (All Students)
```bash
curl -X POST "http://localhost/amt/api/student-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Test Case 5: Null Filters (Should return all students)
```bash
curl -X POST "http://localhost/amt/api/student-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": null, "section_id": null}'
```

---

## Error Handling

The API implements comprehensive error handling:

1. **Method Validation**: Only POST requests are accepted
2. **Authentication**: Validates Client-Service and Auth-Key headers
3. **Database Errors**: Catches and logs database exceptions
4. **Invalid Data**: Returns appropriate error messages for invalid input

---

## Consistency with Existing APIs

This API follows the same patterns as:
- Disable Reason API
- Fee Master API
- Other school management system APIs

**Common patterns:**
- POST method for all operations
- Same authentication headers
- Consistent response format with status, message, and data fields
- Timestamp in responses
- Comprehensive error handling

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | 2025-10-07 | Initial release with filter and list endpoints |

---

## Support

For issues or questions regarding this API, please contact the development team or refer to the main API documentation.

