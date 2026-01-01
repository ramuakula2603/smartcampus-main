# Guardian Report API Documentation

## Table of Contents
- [Overview](#overview)
- [Base URL](#base-url)
- [Authentication](#authentication)
- [Endpoints](#endpoints)
  - [Filter Guardian Report](#1-filter-guardian-report)
  - [List All Guardians](#2-list-all-guardians)
- [Request Examples](#request-examples)
- [Response Format](#response-format)
- [Error Handling](#error-handling)
- [Testing Guide](#testing-guide)

---

## Overview

The **Guardian Report API** provides endpoints for retrieving student guardian information with flexible filtering capabilities. It supports filtering by class and section, with graceful handling of null/empty parameters.

### Key Features
- ✅ **Flexible Filtering** - Filter by class and/or section
- ✅ **Multi-Select Support** - Pass single values or arrays for filters
- ✅ **Graceful Null Handling** - Empty/null filters return all records
- ✅ **Session-Aware** - Automatically uses current session or accepts custom session
- ✅ **Consistent Response Format** - Standardized JSON responses
- ✅ **Comprehensive Guardian Data** - Includes guardian, father, and mother information

---

## Base URL

```
http://localhost/amt/api
```

**Note:** Replace `localhost/amt` with your actual domain and application path.

---

## Authentication

All API requests require the following headers:

| Header | Value | Required |
|--------|-------|----------|
| `Client-Service` | `smartschool` | Yes |
| `Auth-Key` | `schoolAdmin@` | Yes |
| `Content-Type` | `application/json` | Yes |

### Authentication Example

```bash
curl -X POST "http://localhost/amt/api/guardian-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

## Endpoints

### 1. Filter Guardian Report

Retrieves guardian report data based on optional filter parameters.

#### Endpoint Details

- **URL:** `/guardian-report/filter`
- **Method:** `POST`
- **Authentication:** Required

#### Request Parameters

All parameters are **optional**. When no filters are provided, returns all active students for the current session.

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `class_id` | integer or array | Filter by class ID(s) | `1` or `[1, 2, 3]` |
| `section_id` | integer or array | Filter by section ID(s) | `2` or `[1, 2]` |
| `session_id` | integer | Filter by session ID (defaults to current) | `18` |

#### Request Body Examples

**Example 1: No Filters (All Students)**
```json
{}
```

**Example 2: Single Class**
```json
{
  "class_id": 1
}
```

**Example 3: Multiple Classes**
```json
{
  "class_id": [1, 2, 3]
}
```

**Example 4: Class and Section**
```json
{
  "class_id": 1,
  "section_id": 2
}
```

**Example 5: Multiple Classes and Sections**
```json
{
  "class_id": [1, 2],
  "section_id": [1, 2, 3]
}
```

**Example 6: With Custom Session**
```json
{
  "class_id": 1,
  "section_id": 2,
  "session_id": 18
}
```

#### Success Response

**Status Code:** `200 OK`

```json
{
  "status": 1,
  "message": "Guardian report retrieved successfully",
  "filters_applied": {
    "class_id": [1],
    "section_id": [2],
    "session_id": 18
  },
  "total_records": 25,
  "data": [
    {
      "id": "123",
      "admission_no": "ADM001",
      "firstname": "John",
      "middlename": "Michael",
      "lastname": "Doe",
      "class_id": "1",
      "class": "Class 1",
      "section_id": "2",
      "section": "A",
      "mobileno": "9876543210",
      "guardian_name": "Robert Doe",
      "guardian_relation": "Father",
      "guardian_phone": "9876543210",
      "father_name": "Robert Doe",
      "father_phone": "9876543210",
      "mother_name": "Mary Doe",
      "mother_phone": "9876543211",
      "is_active": "yes"
    }
  ],
  "timestamp": "2025-10-07 10:30:45"
}
```

#### Response Fields

| Field | Type | Description |
|-------|------|-------------|
| `status` | integer | 1 for success, 0 for error |
| `message` | string | Response message |
| `filters_applied` | object | Filters that were applied to the query |
| `total_records` | integer | Number of records returned |
| `data` | array | Array of student guardian records |
| `timestamp` | string | Response timestamp |

#### Student Record Fields

| Field | Type | Description |
|-------|------|-------------|
| `id` | string | Student ID |
| `admission_no` | string | Student admission number |
| `firstname` | string | Student first name |
| `middlename` | string | Student middle name |
| `lastname` | string | Student last name |
| `class_id` | string | Class ID |
| `class` | string | Class name |
| `section_id` | string | Section ID |
| `section` | string | Section name |
| `mobileno` | string | Student mobile number |
| `guardian_name` | string | Guardian name |
| `guardian_relation` | string | Guardian relation |
| `guardian_phone` | string | Guardian phone number |
| `father_name` | string | Father name |
| `father_phone` | string | Father phone number |
| `mother_name` | string | Mother name |
| `mother_phone` | string | Mother phone number |
| `is_active` | string | Student active status (yes/no) |

---

### 2. List All Guardians

Retrieves all active students with guardian information for the current session.

#### Endpoint Details

- **URL:** `/guardian-report/list`
- **Method:** `POST`
- **Authentication:** Required

#### Request Parameters

No parameters required. Returns all active students for the current session.

#### Request Body

```json
{}
```

#### Success Response

**Status Code:** `200 OK`

```json
{
  "status": 1,
  "message": "Guardian report retrieved successfully",
  "session_id": 18,
  "total_records": 150,
  "data": [
    {
      "id": "123",
      "admission_no": "ADM001",
      "firstname": "John",
      "middlename": "Michael",
      "lastname": "Doe",
      "class_id": "1",
      "class": "Class 1",
      "section_id": "2",
      "section": "A",
      "mobileno": "9876543210",
      "guardian_name": "Robert Doe",
      "guardian_relation": "Father",
      "guardian_phone": "9876543210",
      "father_name": "Robert Doe",
      "father_phone": "9876543210",
      "mother_name": "Mary Doe",
      "mother_phone": "9876543211",
      "is_active": "yes"
    }
  ],
  "timestamp": "2025-10-07 10:30:45"
}
```

---

## Request Examples

### cURL Examples

**Example 1: All Students**
```bash
curl -X POST "http://localhost/amt/api/guardian-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Example 2: Filter by Class**
```bash
curl -X POST "http://localhost/amt/api/guardian-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": 1}'
```

**Example 3: Multiple Classes**
```bash
curl -X POST "http://localhost/amt/api/guardian-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": [1, 2, 3]}'
```

**Example 4: Class and Section**
```bash
curl -X POST "http://localhost/amt/api/guardian-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": 1, "section_id": 2}'
```

**Example 5: List All**
```bash
curl -X POST "http://localhost/amt/api/guardian-report/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### JavaScript/Fetch Example

```javascript
async function getGuardianReport(filters = {}) {
  try {
    const response = await fetch('http://localhost/amt/api/guardian-report/filter', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Client-Service': 'smartschool',
        'Auth-Key': 'schoolAdmin@'
      },
      body: JSON.stringify(filters)
    });
    
    const data = await response.json();
    
    if (data.status === 1) {
      console.log(`Found ${data.total_records} students`);
      return data.data;
    } else {
      console.error('Error:', data.message);
      return [];
    }
  } catch (error) {
    console.error('Request failed:', error);
    return [];
  }
}

// Usage examples
getGuardianReport(); // All students
getGuardianReport({ class_id: 1 }); // Students in class 1
getGuardianReport({ class_id: [1, 2], section_id: [1, 2] }); // Multiple filters
```

### PHP Example

```php
<?php
function getGuardianReport($filters = []) {
    $url = 'http://localhost/amt/api/guardian-report/filter';

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($filters));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Client-Service: smartschool',
        'Auth-Key: schoolAdmin@'
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

// Usage
$allStudents = getGuardianReport();
$classStudents = getGuardianReport(['class_id' => 1]);
$filteredStudents = getGuardianReport([
    'class_id' => [1, 2],
    'section_id' => [1, 2]
]);
?>
```

### Python Example

```python
import requests
import json

def get_guardian_report(filters=None):
    url = 'http://localhost/amt/api/guardian-report/filter'
    headers = {
        'Content-Type': 'application/json',
        'Client-Service': 'smartschool',
        'Auth-Key': 'schoolAdmin@'
    }

    if filters is None:
        filters = {}

    response = requests.post(url, headers=headers, json=filters)
    return response.json()

# Usage
all_students = get_guardian_report()
class_students = get_guardian_report({'class_id': 1})
filtered_students = get_guardian_report({
    'class_id': [1, 2],
    'section_id': [1, 2]
})
```

---

## Response Format

All API responses follow a consistent JSON format:

### Success Response Structure

```json
{
  "status": 1,
  "message": "Success message",
  "filters_applied": {},
  "total_records": 0,
  "data": [],
  "timestamp": "2025-10-07 10:30:45"
}
```

### Error Response Structure

```json
{
  "status": 0,
  "message": "Error message",
  "data": null
}
```

---

## Error Handling

### Common Error Responses

#### 1. Invalid Request Method

**Status Code:** `400 Bad Request`

```json
{
  "status": 0,
  "message": "Bad request. Only POST method allowed."
}
```

**Cause:** Using GET, PUT, DELETE, or other methods instead of POST.

**Solution:** Use POST method for all API requests.

---

#### 2. Authentication Failed

**Status Code:** `401 Unauthorized`

```json
{
  "status": 0,
  "message": "Unauthorized access"
}
```

**Cause:** Missing or invalid authentication headers.

**Solution:** Ensure you're sending the correct `Client-Service` and `Auth-Key` headers.

---

#### 3. Invalid JSON

**Status Code:** `400 Bad Request`

```json
{
  "status": 0,
  "message": "Invalid JSON format"
}
```

**Cause:** Malformed JSON in request body.

**Solution:** Validate your JSON before sending the request.

---

#### 4. Internal Server Error

**Status Code:** `500 Internal Server Error`

```json
{
  "status": 0,
  "message": "Internal server error occurred",
  "error": "Detailed error message",
  "data": null
}
```

**Cause:** Server-side error (database connection, model loading, etc.).

**Solution:** Check application logs at `api/application/logs/` for detailed error information.

---

## Testing Guide

### Test Scenarios

#### 1. Test All Students (No Filters)

**Request:**
```bash
curl -X POST "http://localhost/amt/api/guardian-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Expected:** Returns all active students for current session.

---

#### 2. Test Single Class Filter

**Request:**
```bash
curl -X POST "http://localhost/amt/api/guardian-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": 1}'
```

**Expected:** Returns students from class 1 only.

---

#### 3. Test Multiple Classes

**Request:**
```bash
curl -X POST "http://localhost/amt/api/guardian-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": [1, 2, 3]}'
```

**Expected:** Returns students from classes 1, 2, and 3.

---

#### 4. Test Class and Section

**Request:**
```bash
curl -X POST "http://localhost/amt/api/guardian-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": 1, "section_id": 2}'
```

**Expected:** Returns students from class 1, section 2.

---

#### 5. Test Null Values

**Request:**
```bash
curl -X POST "http://localhost/amt/api/guardian-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": null, "section_id": null}'
```

**Expected:** Returns all students (null values are ignored).

---

#### 6. Test Empty Arrays

**Request:**
```bash
curl -X POST "http://localhost/amt/api/guardian-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": [], "section_id": []}'
```

**Expected:** Returns all students (empty arrays are treated as no filter).

---

#### 7. Test List Endpoint

**Request:**
```bash
curl -X POST "http://localhost/amt/api/guardian-report/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Expected:** Returns all active students for current session.

---

#### 8. Test Invalid Method

**Request:**
```bash
curl -X GET "http://localhost/amt/api/guardian-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@"
```

**Expected:** Returns 400 error with message "Only POST method allowed."

---

#### 9. Test Invalid Authentication

**Request:**
```bash
curl -X POST "http://localhost/amt/api/guardian-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: invalid" \
  -H "Auth-Key: invalid" \
  -d '{}'
```

**Expected:** Returns 401 error with message "Unauthorized access."

---

### Interactive Testing

Use the provided HTML tester (`guardian_report_api_test.html`) for interactive testing with a user-friendly interface.

---

## Best Practices

1. **Always use POST method** - Even for read operations
2. **Include authentication headers** - Required for all requests
3. **Handle null/empty gracefully** - API returns all records when no filters provided
4. **Use arrays for multi-select** - Pass `[1, 2, 3]` for multiple values
5. **Check response status** - Always verify `status` field before processing data
6. **Log errors** - Check application logs for detailed error information
7. **Validate JSON** - Ensure request body is valid JSON
8. **Use HTTPS in production** - Secure your API endpoints

---

## Database Schema

The API queries the following tables:

- **students** - Main student information
- **student_session** - Student-session-class-section mapping
- **classes** - Class information
- **sections** - Section information

### Key Relationships

```
students (1) ←→ (N) student_session
student_session (N) ←→ (1) classes
student_session (N) ←→ (1) sections
```

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | 2025-10-07 | Initial release |

---

## Support

For issues or questions:
1. Check application logs at `api/application/logs/`
2. Review this documentation
3. Test with the interactive HTML tester
4. Verify database connectivity and data

---

## Related APIs

- **Student Report API** - Student information with additional filters
- **Disable Reason API** - CRUD operations for disable reasons
- **Fee Master API** - Fee management operations

All APIs follow the same authentication and response format patterns.


