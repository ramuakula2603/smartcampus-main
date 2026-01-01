# Admission Report API Documentation

## Table of Contents
- [Overview](#overview)
- [Base URL](#base-url)
- [Authentication](#authentication)
- [Endpoints](#endpoints)
  - [Filter Admission Report](#1-filter-admission-report)
  - [List All Admissions](#2-list-all-admissions)
- [Request Examples](#request-examples)
- [Response Format](#response-format)
- [Error Handling](#error-handling)
- [Testing Guide](#testing-guide)

---

## Overview

The **Admission Report API** provides endpoints for retrieving student admission information with flexible filtering capabilities. It supports filtering by class and admission year, with graceful handling of null/empty parameters.

### Key Features
- ✅ **Flexible Filtering** - Filter by class and/or admission year
- ✅ **Multi-Select Support** - Pass single values or arrays for filters
- ✅ **Graceful Null Handling** - Empty/null filters return all records
- ✅ **Session-Aware** - Automatically uses current session or accepts custom session
- ✅ **Consistent Response Format** - Standardized JSON responses
- ✅ **Comprehensive Admission Data** - Includes admission date, class, section, and guardian information

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
curl -X POST "http://localhost/amt/api/admission-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

## Endpoints

### 1. Filter Admission Report

Retrieves admission report data based on optional filter parameters.

#### Endpoint Details

- **URL:** `/admission-report/filter`
- **Method:** `POST`
- **Authentication:** Required

#### Request Parameters

All parameters are **optional**. When no filters are provided, returns all active students for the current session.

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `class_id` | integer or array | Filter by class ID(s) | `1` or `[1, 2, 3]` |
| `year` | integer or array | Filter by admission year(s) | `2024` or `[2023, 2024]` |
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

**Example 4: Single Admission Year**
```json
{
  "year": 2024
}
```

**Example 5: Multiple Admission Years**
```json
{
  "year": [2023, 2024]
}
```

**Example 6: Class and Year**
```json
{
  "class_id": 1,
  "year": 2024
}
```

**Example 7: Multiple Classes and Years**
```json
{
  "class_id": [1, 2],
  "year": [2023, 2024]
}
```

**Example 8: With Custom Session**
```json
{
  "class_id": 1,
  "year": 2024,
  "session_id": 18
}
```

#### Success Response

**Status Code:** `200 OK`

```json
{
  "status": 1,
  "message": "Admission report retrieved successfully",
  "filters_applied": {
    "class_id": [1],
    "year": [2024],
    "session_id": 18
  },
  "total_records": 25,
  "data": [
    {
      "id": "123",
      "admission_no": "ADM001",
      "admission_date": "2024-04-15",
      "firstname": "John",
      "middlename": "Michael",
      "lastname": "Doe",
      "class_id": "1",
      "class": "Class 1",
      "section_id": "2",
      "section": "A",
      "session_id": "18",
      "session": "2024-2025",
      "mobileno": "9876543210",
      "guardian_name": "Robert Doe",
      "guardian_relation": "Father",
      "guardian_phone": "9876543210",
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
| `data` | array | Array of student admission records |
| `timestamp` | string | Response timestamp |

#### Student Record Fields

| Field | Type | Description |
|-------|------|-------------|
| `id` | string | Student ID |
| `admission_no` | string | Student admission number |
| `admission_date` | string | Date of admission (YYYY-MM-DD) |
| `firstname` | string | Student first name |
| `middlename` | string | Student middle name |
| `lastname` | string | Student last name |
| `class_id` | string | Class ID |
| `class` | string | Class name |
| `section_id` | string | Section ID |
| `section` | string | Section name |
| `session_id` | string | Session ID |
| `session` | string | Session name |
| `mobileno` | string | Student mobile number |
| `guardian_name` | string | Guardian name |
| `guardian_relation` | string | Guardian relation |
| `guardian_phone` | string | Guardian phone number |
| `is_active` | string | Student active status (yes/no) |

---

### 2. List All Admissions

Retrieves all active students with admission information for the current session.

#### Endpoint Details

- **URL:** `/admission-report/list`
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
  "message": "Admission report retrieved successfully",
  "session_id": 18,
  "total_records": 150,
  "data": [
    {
      "id": "123",
      "admission_no": "ADM001",
      "admission_date": "2024-04-15",
      "firstname": "John",
      "middlename": "Michael",
      "lastname": "Doe",
      "class_id": "1",
      "class": "Class 1",
      "section_id": "2",
      "section": "A",
      "session_id": "18",
      "session": "2024-2025",
      "mobileno": "9876543210",
      "guardian_name": "Robert Doe",
      "guardian_relation": "Father",
      "guardian_phone": "9876543210",
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
curl -X POST "http://localhost/amt/api/admission-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Example 2: Filter by Class**
```bash
curl -X POST "http://localhost/amt/api/admission-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": 1}'
```

**Example 3: Filter by Year**
```bash
curl -X POST "http://localhost/amt/api/admission-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"year": 2024}'
```

**Example 4: Multiple Classes and Years**
```bash
curl -X POST "http://localhost/amt/api/admission-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": [1, 2, 3], "year": [2023, 2024]}'
```

**Example 5: Class and Year**
```bash
curl -X POST "http://localhost/amt/api/admission-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": 1, "year": 2024}'
```

**Example 6: List All**
```bash
curl -X POST "http://localhost/amt/api/admission-report/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### JavaScript/Fetch Example

```javascript
async function getAdmissionReport(filters = {}) {
  try {
    const response = await fetch('http://localhost/amt/api/admission-report/filter', {
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
getAdmissionReport(); // All students
getAdmissionReport({ class_id: 1 }); // Students in class 1
getAdmissionReport({ year: 2024 }); // Students admitted in 2024
getAdmissionReport({ class_id: [1, 2], year: [2023, 2024] }); // Multiple filters
```

### PHP Example

```php
<?php
function getAdmissionReport($filters = []) {
    $url = 'http://localhost/amt/api/admission-report/filter';

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
$allStudents = getAdmissionReport();
$classStudents = getAdmissionReport(['class_id' => 1]);
$yearStudents = getAdmissionReport(['year' => 2024]);
$filteredStudents = getAdmissionReport([
    'class_id' => [1, 2],
    'year' => [2023, 2024]
]);
?>
```

### Python Example

```python
import requests
import json

def get_admission_report(filters=None):
    url = 'http://localhost/amt/api/admission-report/filter'
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
all_students = get_admission_report()
class_students = get_admission_report({'class_id': 1})
year_students = get_admission_report({'year': 2024})
filtered_students = get_admission_report({
    'class_id': [1, 2],
    'year': [2023, 2024]
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
curl -X POST "http://localhost/amt/api/admission-report/filter" \
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
curl -X POST "http://localhost/amt/api/admission-report/filter" \
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
curl -X POST "http://localhost/amt/api/admission-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": [1, 2, 3]}'
```

**Expected:** Returns students from classes 1, 2, and 3.

---

#### 4. Test Single Year Filter

**Request:**
```bash
curl -X POST "http://localhost/amt/api/admission-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"year": 2024}'
```

**Expected:** Returns students admitted in 2024.

---

#### 5. Test Multiple Years

**Request:**
```bash
curl -X POST "http://localhost/amt/api/admission-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"year": [2023, 2024]}'
```

**Expected:** Returns students admitted in 2023 or 2024.

---

#### 6. Test Class and Year

**Request:**
```bash
curl -X POST "http://localhost/amt/api/admission-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": 1, "year": 2024}'
```

**Expected:** Returns students from class 1 admitted in 2024.

---

#### 7. Test Null Values

**Request:**
```bash
curl -X POST "http://localhost/amt/api/admission-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": null, "year": null}'
```

**Expected:** Returns all students (null values are ignored).

---

#### 8. Test Empty Arrays

**Request:**
```bash
curl -X POST "http://localhost/amt/api/admission-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": [], "year": []}'
```

**Expected:** Returns all students (empty arrays are treated as no filter).

---

#### 9. Test List Endpoint

**Request:**
```bash
curl -X POST "http://localhost/amt/api/admission-report/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Expected:** Returns all active students for current session.

---

#### 10. Test Invalid Method

**Request:**
```bash
curl -X GET "http://localhost/amt/api/admission-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@"
```

**Expected:** Returns 400 error with message "Only POST method allowed."

---

#### 11. Test Invalid Authentication

**Request:**
```bash
curl -X POST "http://localhost/amt/api/admission-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: invalid" \
  -H "Auth-Key: invalid" \
  -d '{}'
```

**Expected:** Returns 401 error with message "Unauthorized access."

---

### Interactive Testing

Use the provided HTML tester (`admission_report_api_test.html`) for interactive testing with a user-friendly interface.

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

- **students** - Main student information including admission_date
- **student_session** - Student-session-class-section mapping
- **classes** - Class information
- **sections** - Section information
- **sessions** - Session information

### Key Relationships

```
students (1) ←→ (N) student_session
student_session (N) ←→ (1) classes
student_session (N) ←→ (1) sections
student_session (N) ←→ (1) sessions
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
- **Guardian Report API** - Guardian information filtering
- **Disable Reason API** - CRUD operations for disable reasons
- **Fee Master API** - Fee management operations

All APIs follow the same authentication and response format patterns.


