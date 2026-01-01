# Student Fee Search API Documentation

## Overview

The Student Fee Search API provides comprehensive search functionality for finding students and their fee information. This API supports multiple search methods including class-based search, keyword search, and fee category-based search, mirroring the functionality of the `studentfee/feesearch` page.

---

## Base URL
```
http://{domain}/api/
```

## Important URL Structure

**For Student Fee Search APIs, use the controller/method pattern:**
- Search by class: `http://{domain}/api/student-fee-search/by-class`
- Search by keyword: `http://{domain}/api/student-fee-search/by-keyword`
- Search by fee category: `http://{domain}/api/student-fee-search/by-category`
- Get classes: `http://{domain}/api/student-fee-search/classes`
- Get sections: `http://{domain}/api/student-fee-search/sections`
- Get fee categories: `http://{domain}/api/student-fee-search/fee-categories`
- Get student fees: `http://{domain}/api/student-fee-search/student-fees`

**Examples:**
- Search by class: `http://localhost/amt/api/student-fee-search/by-class`
- Search by keyword: `http://localhost/amt/api/student-fee-search/by-keyword`
- Get classes: `http://localhost/amt/api/student-fee-search/classes`

## Authentication Headers

All API requests require the following headers:
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

---

## Endpoints

### 1. Search Students by Class

**Endpoint:** `POST /student-fee-search/by-class`
**Full URL:** `http://localhost/amt/api/student-fee-search/by-class`

**Description:** Search students by class and optionally by section.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### Request Body
```json
{
  "class_id": 1,
  "section_id": 2
}
```

#### Success Response (HTTP 200)
```json
{
  "status": 1,
  "message": "Students retrieved successfully",
  "total_records": 25,
  "search_criteria": {
    "class_id": 1,
    "section_id": 2
  },
  "data": [
    {
      "id": 1,
      "student_session_id": 101,
      "admission_no": "ADM001",
      "firstname": "John",
      "lastname": "Doe",
      "father_name": "Robert Doe",
      "class": "Class 1",
      "section": "A",
      "roll_no": "001",
      "is_active": "yes"
    }
  ],
  "timestamp": "2025-10-05 12:30:45"
}
```

#### cURL Example
```bash
curl -X POST "http://localhost/amt/api/student-fee-search/by-class" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "class_id": 1,
    "section_id": 2
  }'
```

---

### 2. Search Students by Keyword

**Endpoint:** `POST /student-fee-search/by-keyword`
**Full URL:** `http://localhost/amt/api/student-fee-search/by-keyword`

**Description:** Search students using full-text search on names, admission numbers, etc.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### Request Body
```json
{
  "search_text": "John Doe"
}
```

#### Success Response (HTTP 200)
```json
{
  "status": 1,
  "message": "Students retrieved successfully",
  "total_records": 3,
  "search_criteria": {
    "search_text": "John Doe"
  },
  "data": [
    {
      "id": 1,
      "student_session_id": 101,
      "admission_no": "ADM001",
      "firstname": "John",
      "lastname": "Doe",
      "father_name": "Robert Doe",
      "class": "Class 1",
      "section": "A",
      "roll_no": "001",
      "is_active": "yes"
    }
  ],
  "timestamp": "2025-10-05 12:30:45"
}
```

#### cURL Example
```bash
curl -X POST "http://localhost/amt/api/student-fee-search/by-keyword" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "search_text": "John Doe"
  }'
```

---

### 3. Search by Fee Category

**Endpoint:** `POST /student-fee-search/by-category`
**Full URL:** `http://localhost/amt/api/student-fee-search/by-category`

**Description:** Search students and their fee details by fee category.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### Request Body
```json
{
  "feecategory_id": 1,
  "class_id": 1,
  "section_id": 2
}
```

#### Success Response (HTTP 200)
```json
{
  "status": 1,
  "message": "Student fees retrieved successfully",
  "total_records": 15,
  "search_criteria": {
    "feecategory_id": 1,
    "class_id": 1,
    "section_id": 2
  },
  "data": [
    {
      "student_info": {
        "id": 1,
        "admission_no": "ADM001",
        "firstname": "John",
        "lastname": "Doe",
        "class": "Class 1",
        "section": "A"
      },
      "fee_details": [
        {
          "id": 1,
          "fee_group_name": "Tuition Fees",
          "amount": "5000.00",
          "paid_amount": "3000.00",
          "balance": "2000.00",
          "due_date": "2024-01-15"
        }
      ]
    }
  ],
  "timestamp": "2025-10-05 12:30:45"
}
```

#### cURL Example
```bash
curl -X POST "http://localhost/amt/api/student-fee-search/by-category" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "feecategory_id": 1,
    "class_id": 1,
    "section_id": 2
  }'
```

---

### 4. Get Classes

**Endpoint:** `POST /student-fee-search/classes`
**Full URL:** `http://localhost/amt/api/student-fee-search/classes`

**Description:** Get all available classes for dropdown selection.

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

#### Success Response (HTTP 200)
```json
{
  "status": 1,
  "message": "Classes retrieved successfully",
  "total_records": 10,
  "data": [
    {
      "id": 1,
      "class": "Class 1",
      "is_active": "yes"
    },
    {
      "id": 2,
      "class": "Class 2",
      "is_active": "yes"
    }
  ],
  "timestamp": "2025-10-05 12:30:45"
}
```

#### cURL Example
```bash
curl -X POST "http://localhost/amt/api/student-fee-search/classes" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

### 5. Get Sections by Class

**Endpoint:** `POST /student-fee-search/sections`
**Full URL:** `http://localhost/amt/api/student-fee-search/sections`

**Description:** Get sections for a specific class.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### Request Body
```json
{
  "class_id": 1
}
```

#### Success Response (HTTP 200)
```json
{
  "status": 1,
  "message": "Sections retrieved successfully",
  "total_records": 3,
  "class_id": 1,
  "data": [
    {
      "id": 1,
      "section": "A",
      "is_active": "yes"
    },
    {
      "id": 2,
      "section": "B",
      "is_active": "yes"
    }
  ],
  "timestamp": "2025-10-05 12:30:45"
}
```

#### cURL Example
```bash
curl -X POST "http://localhost/amt/api/student-fee-search/sections" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "class_id": 1
  }'
```

---

### 6. Get Fee Categories

**Endpoint:** `POST /student-fee-search/fee-categories`
**Full URL:** `http://localhost/amt/api/student-fee-search/fee-categories`

**Description:** Get all available fee categories.

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

#### Success Response (HTTP 200)
```json
{
  "status": 1,
  "message": "Fee categories retrieved successfully",
  "total_records": 5,
  "data": [
    {
      "id": 1,
      "category": "Academic Fees",
      "is_active": "yes"
    },
    {
      "id": 2,
      "category": "Transport Fees",
      "is_active": "yes"
    }
  ],
  "timestamp": "2025-10-05 12:30:45"
}
```

#### cURL Example
```bash
curl -X POST "http://localhost/amt/api/student-fee-search/fee-categories" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

### 7. Get Student Fees

**Endpoint:** `POST /student-fee-search/student-fees`
**Full URL:** `http://localhost/amt/api/student-fee-search/student-fees`

**Description:** Get detailed fee information for a specific student.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### Request Body
```json
{
  "student_session_id": 101
}
```

#### Success Response (HTTP 200)
```json
{
  "status": 1,
  "message": "Student fees retrieved successfully",
  "student_session_id": 101,
  "data": [
    {
      "id": 1,
      "fee_group_name": "Tuition Fees",
      "amount": "5000.00",
      "paid_amount": "3000.00",
      "balance": "2000.00",
      "due_date": "2024-01-15",
      "fees": [
        {
          "feetype_id": 1,
          "type": "Monthly Fee",
          "amount": "5000.00",
          "fine_amount": "0.00"
        }
      ]
    }
  ],
  "timestamp": "2025-10-05 12:30:45"
}
```

#### cURL Example
```bash
curl -X POST "http://localhost/amt/api/student-fee-search/student-fees" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "student_session_id": 101
  }'
```

---

## Request Parameters

### Search by Class Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| class_id | integer | Yes | Class ID |
| section_id | integer | No | Section ID (optional) |

### Search by Keyword Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| search_text | string | Yes | Search keyword |

### Search by Category Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| feecategory_id | integer | Yes | Fee category ID |
| class_id | integer | No | Class ID (optional) |
| section_id | integer | No | Section ID (optional) |

### Get Sections Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| class_id | integer | Yes | Class ID |

### Get Student Fees Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| student_session_id | integer | Yes | Student session ID |

---

## Response Fields

| Field | Type | Description |
|-------|------|-------------|
| status | integer | 1 for success, 0 for failure |
| message | string | Human-readable message |
| data | array | Response data |
| timestamp | string | Server timestamp |
| total_records | integer | Total number of records |
| search_criteria | object | Applied search criteria |

---

## Error Codes

| HTTP Code | Description |
|-----------|-------------|
| 200 | Success |
| 400 | Bad request (validation error) |
| 404 | Resource not found |
| 500 | Internal server error |

---

## Testing

Use the provided cURL examples to test each endpoint. Make sure to:

1. Replace `localhost/amt` with your actual domain
2. Ensure the authentication headers are correct
3. Test with valid class IDs, section IDs, and student session IDs
4. Verify search functionality with different keywords

---

## Notes

- All endpoints use POST method as per application standards
- Authentication is required for all endpoints
- Search results are filtered by current session and active students
- Fee information includes both assigned and paid amounts
- Use the classes and sections endpoints to populate dropdown menus
- Student session IDs are required for detailed fee information
