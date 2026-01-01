# TC Generation API Documentation

## Overview

The **TC Generation API** provides RESTful endpoints for managing Transfer Certificate (TC) generation in the school management system. This API enables you to retrieve TC certificate templates and list students eligible for TC generation based on class and section filters.

**Base URL:** `http://localhost/amt/api`

**Authentication:** All endpoints require the following headers:
- `Client-Service: smartschool`
- `Auth-Key: schoolAdmin@`
- `Content-Type: application/json`

---

## ⚠️ Important Notes

**All endpoints use POST method** - Even for retrieving data, you must use POST (not GET).

**URL Format** - Make sure your URL is correct:
- ❌ Wrong: `hhttp://localhost/amt/api/tc-generation-api/templates` (typo: "hhttp")
- ✅ Correct: `http://localhost/amt/api/tc-generation-api/templates`
- Always use `http://` (not `https://` or `hhttp://`)

**URL Placeholders** - When you see `{id}` in the documentation, replace it with an actual number:
- ❌ Wrong: `http://localhost/amt/api/tc-generation-api/template/{id}`
- ✅ Correct: `http://localhost/amt/api/tc-generation-api/template/1`
- Example: For ID 5, use `/template/5` not `/template/{id}`

**Request Body** - Even if the endpoint doesn't require data, you must send an empty JSON object `{}` in the request body.

---

## Endpoints

### 1. List TC Certificate Templates

**Endpoint:** `POST /tc-generation-api/templates`

**Description:** Retrieves a list of all active TC certificate templates available for generating transfer certificates.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Request Body:** `{}` (empty JSON object)

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "TC certificate templates retrieved successfully",
  "total_records": 2,
  "data": [
    {
      "id": 1,
      "tc_name": "Standard TC",
      "school_name": "Amaravathi Junior College",
      "tc_head_tittle": "TRANSFER CERTIFICATE",
      "tc_description": "Standard transfer certificate template",
      "status": 1
    },
    {
      "id": 2,
      "tc_name": "Custom TC",
      "school_name": "Amaravathi Junior College",
      "tc_head_tittle": "SCHOOL LEAVING CERTIFICATE",
      "tc_description": "Custom transfer certificate template",
      "status": 1
    }
  ]
}
```

**Error Responses:**

**401 Unauthorized - Invalid Headers:**
```json
{
  "status": 0,
  "message": "Unauthorized access. Invalid headers.",
  "data": null
}
```

**405 Method Not Allowed:**
```json
{
  "status": 0,
  "message": "Method not allowed. Use POST method.",
  "data": null
}
```

**500 Internal Server Error:**
```json
{
  "status": 0,
  "message": "Internal server error occurred",
  "data": null
}
```

---

### 2. Get Specific TC Certificate Template

**Endpoint:** `POST /tc-generation-api/template/{id}`

**Description:** Retrieves detailed information about a specific TC certificate template by its ID.

**⚠️ Important:** 
- **Method must be POST** (not GET)
- Replace `{id}` with an actual template ID number (e.g., use `1` instead of `{id}`)
- Example URL: `http://localhost/amt/api/tc-generation-api/template/1`

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**URL Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `id` | integer | Yes | ID of the TC certificate template | `1`, `2`, `5` |

**Request Body:** `{}` (empty JSON object)

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "TC certificate template retrieved successfully",
  "data": {
    "id": 1,
    "tc_name": "Standard TC",
    "school_name": "Amaravathi Junior College",
    "tc_head_tittle": "TRANSFER CERTIFICATE",
    "tc_description": "Standard transfer certificate template",
    "tc_address": "School Address",
    "tc_body": "Certificate body text",
    "tc_footer": "Certificate footer",
    "tc_conduct": "Good",
    "tc_mother_tongue": "Telugu",
    "tc_first_lang": "1",
    "tc_second_lang": "2",
    "tc_date_left": "Date format",
    "tc_nationality": "Indian",
    "tc_second_year_course": "Course details",
    "tc_eligible_university_course": "University course details",
    "tc_receipt_scholarship": "Scholarship details",
    "tc_receipt_concession": "Concession details",
    "tc_punishment_during_period": "Punishment details",
    "tc_optional_lang": "Optional language",
    "logo": "uploads/tcgeneration/logo/logo.png",
    "enable_student_name": 1,
    "enable_admission_date": 1,
    "enable_parents_name": 1,
    "enable_dob": 1,
    "enable_mother_tongue": 1,
    "enable_date_tc": 1,
    "enable_caste": 1,
    "status": 1
  }
}
```

**Error Responses:**

**400 Bad Request - Invalid ID:**
```json
{
  "status": 0,
  "message": "Invalid or missing template ID",
  "data": null
}
```

**404 Not Found:**
```json
{
  "status": 0,
  "message": "TC certificate template not found",
  "data": null
}
```

---

### 3. List Students for TC Generation

**Endpoint:** `POST /tc-generation-api/students`

**Description:** Retrieves a list of students eligible for TC generation with optional filtering by class, section, and session.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Request Body:**
```json
{
  "class_id": [1, 2],
  "section_id": 1,
  "session_id": 1
}
```

**Request Parameters:**
| Parameter | Type | Required | Description | Example Values |
|-----------|------|----------|-------------|----------------|
| `class_id` | integer or array | No | Filter by class ID(s). Can be a single ID or array of IDs | `1`, `[1, 2, 3]` |
| `section_id` | integer or array | No | Filter by section ID(s). Can be a single ID or array of IDs | `1`, `[1, 3]` |
| `session_id` | integer or array | No | Filter by session ID(s). Can be a single ID or array of IDs | `1`, `[1, 2]` |

**Important Notes:**
- All parameters are optional. If no parameters are provided, all active students will be returned.
- Multiple filters can be combined. All filters are applied with AND logic.
- Results are ordered by admission number in ascending order.
- Only active students (`is_active = 'yes'`) are returned.

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Students for TC generation retrieved successfully",
  "total_records": 2,
  "data": [
    {
      "student_id": 135,
      "admission_no": "ADM001",
      "student_name": "John Doe",
      "father_name": "Robert Doe",
      "mother_name": "Mary Doe",
      "class": "Class 10",
      "class_id": 10,
      "section": "A",
      "section_id": 1,
      "date_of_birth": "2010-05-15",
      "gender": "Male",
      "phone": "1234567890",
      "cast": "OC",
      "religion": "Hindu",
      "category": "General",
      "admission_date": "2020-04-01"
    },
    {
      "student_id": 136,
      "admission_no": "ADM002",
      "student_name": "Jane Smith",
      "father_name": "Michael Smith",
      "mother_name": "Sarah Smith",
      "class": "Class 10",
      "class_id": 10,
      "section": "A",
      "section_id": 1,
      "date_of_birth": "2010-08-20",
      "gender": "Female",
      "phone": "9876543210",
      "cast": "BC",
      "religion": "Christian",
      "category": "OBC",
      "admission_date": "2020-04-02"
    }
  ]
}
```

**Error Responses:**

**401 Unauthorized - Invalid Headers:**
```json
{
  "status": 0,
  "message": "Unauthorized access. Invalid headers.",
  "data": null
}
```

**405 Method Not Allowed:**
```json
{
  "status": 0,
  "message": "Method not allowed. Use POST method.",
  "data": null
}
```

**500 Internal Server Error:**
```json
{
  "status": 0,
  "message": "Internal server error occurred",
  "data": null
}
```

---

### 4. Get Specific Student for TC Generation

**Endpoint:** `POST /tc-generation-api/student/{student_id}`

**Description:** Retrieves detailed information about a specific student eligible for TC generation.

**⚠️ Important:** 
- **Method must be POST** (not GET)
- Replace `{student_id}` with an actual student ID number (e.g., use `135` instead of `{student_id}`)
- Example URL: `http://localhost/amt/api/tc-generation-api/student/135`

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**URL Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `student_id` | integer | Yes | ID of the student | `135`, `136`, `140` |

**Request Body:** `{}` (empty JSON object)

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Student details retrieved successfully",
  "data": {
    "student_id": 135,
    "admission_no": "ADM001",
    "student_name": "John Doe",
    "father_name": "Robert Doe",
    "mother_name": "Mary Doe",
    "class": "Class 10",
    "section": "A",
    "date_of_birth": "2010-05-15",
    "gender": "Male",
    "phone": "1234567890",
    "cast": "OC",
    "religion": "Hindu",
    "category": "General",
    "admission_date": "2020-04-01"
  }
}
```

**Error Responses:**

**400 Bad Request - Invalid ID:**
```json
{
  "status": 0,
  "message": "Invalid or missing student ID",
  "data": null
}
```

**404 Not Found:**
```json
{
  "status": 0,
  "message": "Student not found or not eligible for TC generation",
  "data": null
}
```

---

### 5. List All TC Templates (Management)

**Endpoint:** `POST /tc-generation-api/list`

**Description:** Retrieves a list of all TC templates including inactive ones, with optional filtering. This endpoint is used for template management purposes.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Request Body:**
```json
{
  "search": "Standard",
  "status": 1
}
```

**Request Parameters:**
| Parameter | Type | Required | Description | Example Values |
|-----------|------|----------|-------------|----------------|
| `search` | string | No | Search term to filter templates by name, school name, or header title | `"Standard"`, `"TC"` |
| `status` | integer | No | Filter by status (0=inactive, 1=active). Leave empty to get all | `0`, `1` |

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "TC templates retrieved successfully",
  "total_records": 6,
  "data": [
    {
      "id": 1,
      "tc_name": "Standard TC",
      "school_name": "Amaravathi Junior College",
      "tc_head_tittle": "TRANSFER CERTIFICATE",
      "tc_description": "Standard transfer certificate template",
      "tc_address": "School Address",
      "tc_body": "Certificate body text",
      "status": 1
    }
  ]
}
```

**Error Responses:**

**401 Unauthorized - Invalid Headers:**
```json
{
  "status": 0,
  "message": "Unauthorized access. Invalid headers.",
  "data": null
}
```

---

### 6. Create TC Template

**Endpoint:** `POST /tc-generation-api/create`

**Description:** Creates a new TC certificate template.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Request Body:**
```json
{
  "tc_name": "Standard TC",
  "school_name": "Amaravathi Junior College",
  "tc_head_tittle": "TRANSFER CERTIFICATE",
  "tc_description": "Standard transfer certificate template",
  "tc_address": "School Address",
  "tc_body": "Certificate body text",
  "tc_footer": "Certificate footer",
  "tc_conduct": "Good",
  "tc_mother_tongue": "Telugu",
  "firstlang_id": "1",
  "secondlang_id": "2",
  "tc_date_left": "Date format",
  "tc_nationality": "Indian",
  "tc_second_year_course": "Course details",
  "tc_eligible_university_course": "University course details",
  "tc_receipt_scholarship": "Scholarship details",
  "tc_receipt_concession": "Concession details",
  "tc_punishment_during_period": "Punishment details",
  "tc_optional_lang": "Optional language",
  "logo": "uploads/tcgeneration/logo/logo.png",
  "enable_student_name": 1,
  "enable_admission_date": 1,
  "enable_parents_name": 1,
  "enable_dob": 1,
  "enable_mother_tongue": 1,
  "enable_date_tc": 1,
  "enable_caste": 1,
  "status": 1
}
```

**Required Fields:**
| Field | Type | Description |
|-------|------|-------------|
| `tc_name` | string | Name of the TC template |
| `school_name` | string | School name |
| `tc_head_tittle` | string | Certificate header title |
| `tc_description` | string | Certificate description |
| `tc_body` | string | Certificate body text |
| `tc_address` | string | School address |
| `tc_footer` | string | Certificate footer |
| `tc_conduct` | string | Conduct information |
| `tc_mother_tongue` | string | Mother tongue field |
| `firstlang_id` | string/integer | First language subject ID |
| `secondlang_id` | string/integer | Second language subject ID |
| `tc_date_left` | string | Date format |
| `tc_nationality` | string | Nationality field |
| `tc_second_year_course` | string | Second year course details |
| `tc_eligible_university_course` | string | Eligible university course details |
| `tc_receipt_scholarship` | string | Scholarship receipt details |
| `tc_receipt_concession` | string | Concession receipt details |
| `tc_punishment_during_period` | string | Punishment details |
| `tc_optional_lang` | string | Optional language details |

**Optional Fields:**
| Field | Type | Default | Description |
|-------|------|---------|-------------|
| `logo` | string | `""` | Logo file path |
| `enable_student_name` | integer | `0` | Enable student name field (0=disable, 1=enable) |
| `enable_admission_date` | integer | `0` | Enable admission date field (0=disable, 1=enable) |
| `enable_parents_name` | integer | `0` | Enable parents name field (0=disable, 1=enable) |
| `enable_dob` | integer | `0` | Enable date of birth field (0=disable, 1=enable) |
| `enable_mother_tongue` | integer | `0` | Enable mother tongue field (0=disable, 1=enable) |
| `enable_date_tc` | integer | `0` | Enable TC date field (0=disable, 1=enable) |
| `enable_caste` | integer | `0` | Enable caste field (0=disable, 1=enable) |
| `status` | integer | `1` | Template status (0=inactive, 1=active) |

**Success Response (201 Created):**
```json
{
  "status": 1,
  "message": "TC template created successfully",
  "data": {
    "id": 7,
    "tc_name": "Standard TC",
    "school_name": "Amaravathi Junior College",
    "tc_head_tittle": "TRANSFER CERTIFICATE",
    "tc_description": "Standard transfer certificate template",
    "tc_address": "School Address",
    "tc_body": "Certificate body text",
    "tc_footer": "Certificate footer",
    "tc_conduct": "Good",
    "tc_mother_tongue": "Telugu",
    "tc_first_lang": "1",
    "tc_second_lang": "2",
    "logo": "uploads/tcgeneration/logo/logo.png",
    "enable_student_name": 1,
    "enable_admission_date": 1,
    "enable_parents_name": 1,
    "enable_dob": 1,
    "enable_mother_tongue": 1,
    "enable_date_tc": 1,
    "enable_caste": 1,
    "status": 1
  }
}
```

**Error Responses:**

**400 Bad Request - Missing Required Field:**
```json
{
  "status": 0,
  "message": "Required field missing: tc_name",
  "data": null
}
```

**401 Unauthorized - Invalid Headers:**
```json
{
  "status": 0,
  "message": "Unauthorized access. Invalid headers.",
  "data": null
}
```

**500 Internal Server Error:**
```json
{
  "status": 0,
  "message": "Failed to create TC template",
  "data": null
}
```

---

### 7. Update TC Template

**Endpoint:** `POST /tc-generation-api/update/{id}`

**Description:** Updates an existing TC certificate template. Only provided fields will be updated.

**⚠️ Important:** 
- **Method must be POST** (not PUT)
- Replace `{id}` with an actual template ID number (e.g., use `1` instead of `{id}`)
- Example URL: `http://localhost/amt/api/tc-generation-api/update/1`

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**URL Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `id` | integer | Yes | ID of the TC template to update | `1`, `2`, `5` |

**Request Body:**
```json
{
  "tc_name": "Updated TC Name",
  "tc_description": "Updated description",
  "status": 1
}
```

**Note:** You can provide any combination of fields to update. Only the fields provided in the request body will be updated.

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "TC template updated successfully",
  "data": {
    "id": 1,
    "tc_name": "Updated TC Name",
    "school_name": "Amaravathi Junior College",
    "tc_description": "Updated description",
    "status": 1
  }
}
```

**Error Responses:**

**400 Bad Request - Invalid ID:**
```json
{
  "status": 0,
  "message": "Invalid or missing template ID",
  "data": null
}
```

**400 Bad Request - No Data Provided:**
```json
{
  "status": 0,
  "message": "No data provided for update",
  "data": null
}
```

**404 Not Found:**
```json
{
  "status": 0,
  "message": "TC template not found",
  "data": null
}
```

---

### 8. Delete TC Template

**Endpoint:** `POST /tc-generation-api/delete/{id}`

**Description:** Deletes a TC certificate template.

**⚠️ Important:** 
- **Method must be POST** (not DELETE)
- Replace `{id}` with an actual template ID number (e.g., use `1` instead of `{id}`)
- Example URL: `http://localhost/amt/api/tc-generation-api/delete/1`

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**URL Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `id` | integer | Yes | ID of the TC template to delete | `1`, `2`, `5` |

**Request Body:** `{}` (empty JSON object)

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "TC template deleted successfully",
  "data": null
}
```

**Error Responses:**

**400 Bad Request - Invalid ID:**
```json
{
  "status": 0,
  "message": "Invalid or missing template ID",
  "data": null
}
```

**404 Not Found:**
```json
{
  "status": 0,
  "message": "TC template not found",
  "data": null
}
```

**500 Internal Server Error:**
```json
{
  "status": 0,
  "message": "Failed to delete TC template",
  "data": null
}
```

---

### 9. Get Subjects List (for Language Selection)

**Endpoint:** `POST /tc-generation-api/subjects`

**Description:** Retrieves a list of subjects that can be used for first and second language selection in TC templates.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Request Body:** `{}` (empty JSON object)

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Subjects retrieved successfully",
  "total_records": 10,
  "data": [
    {
      "id": 1,
      "name": "English"
    },
    {
      "id": 2,
      "name": "Telugu"
    },
    {
      "id": 3,
      "name": "Hindi"
    }
  ]
}
```

**Error Responses:**

**401 Unauthorized - Invalid Headers:**
```json
{
  "status": 0,
  "message": "Unauthorized access. Invalid headers.",
  "data": null
}
```

---

## HTTP Status Codes

| Code | Meaning | Description |
|------|---------|-------------|
| 200 | OK | Request successful (GET, LIST operations) |
| 400 | Bad Request | Invalid input or missing required fields |
| 401 | Unauthorized | Invalid or missing authentication headers |
| 404 | Not Found | Resource not found (template or student) |
| 405 | Method Not Allowed | Wrong HTTP method used (must be POST) |
| 500 | Internal Server Error | Server error occurred |

---

## cURL Examples

### Example 1: List TC Certificate Templates

```bash
curl -X POST "http://localhost/amt/api/tc-generation-api/templates" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 2: Get Specific TC Template

```bash
curl -X POST "http://localhost/amt/api/tc-generation-api/template/1" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 3: List Students for TC Generation

```bash
curl -X POST "http://localhost/amt/api/tc-generation-api/students" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 4: List Students with Filters

```bash
curl -X POST "http://localhost/amt/api/tc-generation-api/students" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "class_id": [1, 2],
    "section_id": 1
  }'
```

### Example 5: Get Specific Student

```bash
curl -X POST "http://localhost/amt/api/tc-generation-api/student/135" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 6: List All TC Templates (Management)

```bash
curl -X POST "http://localhost/amt/api/tc-generation-api/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "search": "Standard",
    "status": 1
  }'
```

### Example 7: Create TC Template

```bash
curl -X POST "http://localhost/amt/api/tc-generation-api/create" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "tc_name": "Standard TC",
    "school_name": "Amaravathi Junior College",
    "tc_head_tittle": "TRANSFER CERTIFICATE",
    "tc_description": "Standard transfer certificate template",
    "tc_address": "School Address",
    "tc_body": "Certificate body text",
    "tc_footer": "Certificate footer",
    "tc_conduct": "Good",
    "tc_mother_tongue": "Telugu",
    "firstlang_id": "1",
    "secondlang_id": "2",
    "tc_date_left": "Date format",
    "tc_nationality": "Indian",
    "tc_second_year_course": "Course details",
    "tc_eligible_university_course": "University course details",
    "tc_receipt_scholarship": "Scholarship details",
    "tc_receipt_concession": "Concession details",
    "tc_punishment_during_period": "Punishment details",
    "tc_optional_lang": "Optional language",
    "status": 1
  }'
```

### Example 8: Update TC Template

```bash
curl -X POST "http://localhost/amt/api/tc-generation-api/update/1" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "tc_name": "Updated TC Name",
    "tc_description": "Updated description"
  }'
```

### Example 9: Delete TC Template

```bash
curl -X POST "http://localhost/amt/api/tc-generation-api/delete/1" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 10: Get Subjects List

```bash
curl -X POST "http://localhost/amt/api/tc-generation-api/subjects" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

## Complete Workflow: TC Generation Process

### Step 1: List Available TC Templates

```bash
curl -X POST "http://localhost/amt/api/tc-generation-api/templates" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Response:**
```json
{
  "status": 1,
  "message": "TC certificate templates retrieved successfully",
  "total_records": 2,
  "data": [
    {
      "id": 1,
      "tc_name": "Standard TC",
      "school_name": "Amaravathi Junior College"
    }
  ]
}
```

### Step 2: Get TC Template Details

```bash
curl -X POST "http://localhost/amt/api/tc-generation-api/template/1" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Step 3: List Students for TC Generation

```bash
curl -X POST "http://localhost/amt/api/tc-generation-api/students" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "class_id": 10,
    "section_id": 1
  }'
```

**Response:**
```json
{
  "status": 1,
  "message": "Students for TC generation retrieved successfully",
  "total_records": 2,
  "data": [
    {
      "student_id": 135,
      "admission_no": "ADM001",
      "student_name": "John Doe",
      "class": "Class 10",
      "section": "A"
    }
  ]
}
```

### Step 4: Get Specific Student Details

```bash
curl -X POST "http://localhost/amt/api/tc-generation-api/student/135" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

## Complete Workflow: TC Template Management

### Step 1: Get Subjects List (for Language Selection)

```bash
curl -X POST "http://localhost/amt/api/tc-generation-api/subjects" \
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
  "total_records": 10,
  "data": [
    {
      "id": 1,
      "name": "English"
    },
    {
      "id": 2,
      "name": "Telugu"
    }
  ]
}
```

### Step 2: Create New TC Template

```bash
curl -X POST "http://localhost/amt/api/tc-generation-api/create" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "tc_name": "Standard TC",
    "school_name": "Amaravathi Junior College",
    "tc_head_tittle": "TRANSFER CERTIFICATE",
    "tc_description": "Standard transfer certificate template",
    "tc_address": "School Address",
    "tc_body": "Certificate body text",
    "tc_footer": "Certificate footer",
    "tc_conduct": "Good",
    "tc_mother_tongue": "Telugu",
    "firstlang_id": "1",
    "secondlang_id": "2",
    "tc_date_left": "Date format",
    "tc_nationality": "Indian",
    "tc_second_year_course": "Course details",
    "tc_eligible_university_course": "University course details",
    "tc_receipt_scholarship": "Scholarship details",
    "tc_receipt_concession": "Concession details",
    "tc_punishment_during_period": "Punishment details",
    "tc_optional_lang": "Optional language",
    "status": 1
  }'
```

**Response:**
```json
{
  "status": 1,
  "message": "TC template created successfully",
  "data": {
    "id": 7,
    "tc_name": "Standard TC"
  }
}
```

### Step 3: List All Templates (Management View)

```bash
curl -X POST "http://localhost/amt/api/tc-generation-api/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "status": 1
  }'
```

### Step 4: Update Template (if needed)

```bash
curl -X POST "http://localhost/amt/api/tc-generation-api/update/7" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "tc_name": "Updated TC Name",
    "tc_description": "Updated description"
  }'
```

### Step 5: Delete Template (if needed)

```bash
curl -X POST "http://localhost/amt/api/tc-generation-api/delete/7" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

## Use Cases

### 1. **TC Template Management**
   - List all available TC certificate templates (including inactive)
   - Create new TC certificate templates
   - Update existing TC templates
   - Delete TC templates
   - Get subjects list for language selection
   - Search and filter templates by name or status
   - Get detailed template information for TC generation
   - Select appropriate template for generating certificates

### 2. **Student Selection for TC**
   - Filter students by class and section
   - Get list of students eligible for TC generation
   - Retrieve detailed student information for TC

### 3. **TC Generation Workflow**
   - Select TC template (TC NAME)
   - Filter students by class and section
   - Generate TC certificates for selected students
   - Track TC generation process

### 4. **Reporting and Analytics**
   - Generate reports of students eligible for TC
   - Track TC generation by class/section
   - Monitor TC generation trends

---

## Database Schema

### tc_generation Table

The TC generation system uses the `tc_generation` table to store TC certificate templates with the following key fields:

- `id` (Primary Key) - Unique identifier for the template
- `tc_name` - Name of the TC certificate template
- `school_name` - School name
- `tc_head_tittle` - Certificate header title
- `tc_description` - Certificate description
- `tc_address` - School address
- `tc_body` - Certificate body text
- `tc_footer` - Certificate footer
- `tc_conduct` - Conduct information
- `tc_mother_tongue` - Mother tongue field
- `tc_first_lang` - First language ID
- `tc_second_lang` - Second language ID
- `logo` - Logo file path
- `enable_student_name` - Enable/disable student name field
- `enable_admission_date` - Enable/disable admission date field
- `enable_parents_name` - Enable/disable parents name field
- `enable_dob` - Enable/disable date of birth field
- `enable_mother_tongue` - Enable/disable mother tongue field
- `enable_date_tc` - Enable/disable TC date field
- `enable_caste` - Enable/disable caste field
- `status` - Template status (0=disabled, 1=enabled)

**Related Tables:**
- `students` - Student information
- `student_session` - Student class/section assignments
- `classes` - Class information
- `sections` - Section information
- `subjects` - Subject information (for languages)

---

## Best Practices

1. **Always select a valid TC template** before generating certificates
2. **Use filters** to efficiently retrieve specific students
3. **Verify student information** before generating TC
4. **Check template status** to ensure template is active
5. **Handle error responses** appropriately in your application
6. **Log all API calls** for audit and debugging purposes
7. **Use class and section filters** to narrow down student lists
8. **Verify student eligibility** (active status) before TC generation
9. **Maintain template consistency** across TC generations
10. **Use session filters** to track TC generation by academic year

---

## Integration Notes

### TC Generation Process
- The API provides data retrieval for TC generation
- Actual TC certificate generation should be handled through the main application
- Use template ID and student ID to generate TC certificates

### Template Selection
- Use the templates endpoint to get available TC templates
- Select appropriate template based on requirements
- Template ID is used when generating TC for students

### Student Filtering
- Filter students by class and section as shown in the UI
- Only active students are returned
- Use filters to get specific student groups for TC generation

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | 2025-11-13 | Initial release with template and student listing endpoints |

---

## Support

For issues or questions regarding this API, please contact the development team or refer to the main API documentation.

