# Student Referral API Documentation

## Overview

The **Student Referral API** provides RESTful endpoints for managing student referral records in the school management system. This API enables you to create, read, update, and delete student referral records, tracking which staff members referred which students. Student referrals help track the source of student admissions and can be used for reporting and analytics.

**Base URL:** `http://localhost/amt/api`

**Authentication:** All endpoints require the following headers:
- `Client-Service: smartschool`
- `Auth-Key: schoolAdmin@`
- `Content-Type: application/json`

---

## ⚠️ Important Notes

**All endpoints use POST method** - Even for retrieving data, you must use POST (not GET).

**URL Format** - Make sure your URL is correct:
- ❌ Wrong: `hhttp://localhost/amt/api/student-referral-api/update/15` (typo: "hhttp")
- ✅ Correct: `http://localhost/amt/api/student-referral-api/update/15`
- Always use `http://` (not `https://` or `hhttp://`)

**URL Placeholders** - When you see `{id}` in the documentation, replace it with an actual number:
- ❌ Wrong: `http://localhost/amt/api/student-referral-api/get/{id}`
- ✅ Correct: `http://localhost/amt/api/student-referral-api/get/1`
- Example: For ID 15, use `/update/15` not `/update/{id}`

**Request Body** - Even if the endpoint doesn't require data, you must send an empty JSON object `{}` in the request body.

---

## Endpoints

### 1. List All Student Referrals

**Endpoint:** `POST /student-referral-api/list`

**Description:** Retrieves a list of all student referrals with optional filtering by class, section, reference ID (staff ID), and session.

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
  "section_id": [1, 3],
  "reference_id": 2,
  "session_id": 1
}
```

**Request Parameters:**
| Parameter | Type | Required | Description | Example Values |
|-----------|------|----------|-------------|----------------|
| `class_id` | integer or array | No | Filter by class ID(s). Can be a single ID or array of IDs | `1`, `[1, 2, 3]` |
| `section_id` | integer or array | No | Filter by section ID(s). Can be a single ID or array of IDs | `1`, `[1, 3]` |
| `reference_id` | integer or array | No | Filter by reference ID (staff ID) who referred the student. Can be a single ID or array of IDs | `2`, `[2, 6]` |
| `session_id` | integer or array | No | Filter by session ID(s). Can be a single ID or array of IDs | `1`, `[1, 2]` |

**Important Notes:**
- All parameters are optional. If no parameters are provided, all student referrals will be returned.
- Multiple filters can be combined. All filters are applied with AND logic.
- Results are ordered by referral ID in descending order (newest first).

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Student referrals retrieved successfully",
  "total_records": 2,
  "data": [
    {
      "referral_id": 5,
      "student_id": 139,
      "admission_no": "ADM001",
      "student_name": "John Doe",
      "father_name": "Robert Doe",
      "class": "Class 10",
      "class_id": 10,
      "section": "A",
      "section_id": 1,
      "date_of_birth": "2010-05-15",
      "phone": "1234567890",
      "reference_id": 2,
      "reference_by": "Staff Name",
      "reference_employee_id": "EMP001",
      "session_id": 18,
      "created_at": "2023-10-26 15:17:05"
    },
    {
      "referral_id": 4,
      "student_id": 138,
      "admission_no": "ADM002",
      "student_name": "Jane Smith",
      "father_name": "Michael Smith",
      "class": "Class 9",
      "class_id": 9,
      "section": "B",
      "section_id": 2,
      "date_of_birth": "2011-08-20",
      "phone": "9876543210",
      "reference_id": 6,
      "reference_by": "Another Staff",
      "reference_employee_id": "EMP002",
      "session_id": 18,
      "created_at": "2023-09-22 10:43:29"
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

### 2. Get Specific Student Referral

**Endpoint:** `POST /student-referral-api/get/{id}`

**Description:** Retrieves details of a specific student referral by its ID.

**⚠️ Important:** 
- **Method must be POST** (not GET)
- Replace `{id}` with an actual referral ID number (e.g., use `1` instead of `{id}`)
- Example URL: `http://localhost/amt/api/student-referral-api/get/5`

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**URL Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `id` | integer | Yes | ID of the student referral | `5`, `10`, `25` |

**Request Body:** `{}` (empty JSON object)

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Student referral retrieved successfully",
  "data": {
    "referral_id": 5,
    "student_id": 139,
    "admission_no": "ADM001",
    "student_name": "John Doe",
    "father_name": "Robert Doe",
    "class": "Class 10",
    "section": "A",
    "date_of_birth": "2010-05-15",
    "gender": "Male",
    "phone": "1234567890",
    "reference_id": 2,
    "reference_by": "Staff Name",
    "reference_employee_id": "EMP001",
    "session_id": 18,
    "created_at": "2023-10-26 15:17:05"
  }
}
```

**Error Responses:**

**400 Bad Request - Invalid ID:**
```json
{
  "status": 0,
  "message": "Invalid or missing referral ID",
  "data": null
}
```

**404 Not Found:**
```json
{
  "status": 0,
  "message": "Student referral not found",
  "data": null
}
```

---

### 3. Create New Student Referral

**Endpoint:** `POST /student-referral-api/create`

**Description:** Creates a new student referral record, linking a student to a staff member who referred them.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Request Body:**
```json
{
  "student_id": 140,
  "reference_id": 2,
  "session_id": 18
}
```

**Request Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `student_id` | integer | Yes | ID of the student being referred | `140`, `141` |
| `reference_id` | integer | Yes | ID of the staff member who referred the student | `2`, `6` |
| `session_id` | integer | No | Academic session ID. If not provided, may use current session | `18`, `19` |

**Success Response (201 Created):**
```json
{
  "status": 1,
  "message": "Student referral created successfully",
  "data": {
    "referral_id": 8,
    "student_id": 140,
    "reference_id": 2,
    "session_id": 18
  }
}
```

**Error Responses:**

**400 Bad Request - Missing Required Field:**
```json
{
  "status": 0,
  "message": "Student ID is required and must be a valid number",
  "data": null
}
```

**400 Bad Request - Missing Reference ID:**
```json
{
  "status": 0,
  "message": "Reference ID (Staff ID) is required and must be a valid number",
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

### 4. Update Student Referral

**Endpoint:** `POST /student-referral-api/update/{id}`

**Description:** Updates an existing student referral record.

**⚠️ Important:** 
- **Method must be POST** (not PUT or PATCH)
- **URL must start with `http://`** (not `hhttp://` or `https://`)
- **Replace `{id}` with an actual referral ID number** (e.g., use `5` instead of `{id}`)
- **Correct Example URL:** `http://localhost/amt/api/student-referral-api/update/5`
- You must have a valid referral ID. Use the list endpoint first to get existing referral IDs.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**URL Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `id` | integer | Yes | ID of the referral to update | `5`, `10`, `25` |

**Request Body:**
```json
{
  "student_id": 141,
  "reference_id": 6,
  "session_id": 18
}
```

**Request Parameters:** (All fields are optional - only provide fields you want to update)
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `student_id` | integer | No | ID of the student | `141`, `142` |
| `reference_id` | integer | No | ID of the staff member who referred the student | `6`, `2` |
| `session_id` | integer | No | Academic session ID | `18`, `19` |

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Student referral updated successfully",
  "data": {
    "referral_id": 5,
    "student_id": 141,
    "reference_id": 6,
    "session_id": 18
  }
}
```

**Error Responses:**

**400 Bad Request - Invalid ID:**
```json
{
  "status": 0,
  "message": "Invalid or missing referral ID",
  "data": null
}
```

**400 Bad Request - No Fields Provided:**
```json
{
  "status": 0,
  "message": "No valid fields provided for update",
  "data": null
}
```

**404 Not Found - Referral doesn't exist:**
```json
{
  "status": 0,
  "message": "Student referral not found",
  "data": null
}
```

---

### 5. Delete Student Referral

**Endpoint:** `POST /student-referral-api/delete/{id}`

**Description:** Deletes a student referral record.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**URL Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `id` | integer | Yes | ID of the referral to delete | `5`, `10`, `25` |

**Request Body:** `{}` (empty JSON object)

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Student referral deleted successfully",
  "data": {
    "referral_id": 5
  }
}
```

**Error Responses:**

**400 Bad Request - Invalid ID:**
```json
{
  "status": 0,
  "message": "Invalid or missing referral ID",
  "data": null
}
```

**404 Not Found:**
```json
{
  "status": 0,
  "message": "Student referral not found",
  "data": null
}
```

---

## HTTP Status Codes

| Code | Meaning | Description |
|------|---------|-------------|
| 200 | OK | Request successful (GET, LIST, UPDATE, DELETE operations) |
| 201 | Created | Resource created successfully (CREATE operation) |
| 400 | Bad Request | Invalid input or missing required fields |
| 401 | Unauthorized | Invalid or missing authentication headers |
| 404 | Not Found | Resource not found (referral) |
| 405 | Method Not Allowed | Wrong HTTP method used (must be POST) |
| 500 | Internal Server Error | Server error occurred |

---

## cURL Examples

### Example 1: List All Student Referrals

```bash
curl -X POST "http://localhost/amt/api/student-referral-api/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 2: List with Filters

```bash
curl -X POST "http://localhost/amt/api/student-referral-api/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "class_id": [1, 2],
    "section_id": 1,
    "reference_id": 2
  }'
```

### Example 3: Get Specific Student Referral

```bash
curl -X POST "http://localhost/amt/api/student-referral-api/get/5" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 4: Create New Student Referral

```bash
curl -X POST "http://localhost/amt/api/student-referral-api/create" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "student_id": 140,
    "reference_id": 2,
    "session_id": 18
  }'
```

### Example 5: Update Student Referral

```bash
curl -X POST "http://localhost/amt/api/student-referral-api/update/5" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "reference_id": 6,
    "session_id": 18
  }'
```

### Example 6: Delete Student Referral

```bash
curl -X POST "http://localhost/amt/api/student-referral-api/delete/5" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

## Complete Workflow: Managing Student Referrals

### Step 1: List All Student Referrals

```bash
curl -X POST "http://localhost/amt/api/student-referral-api/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Response:**
```json
{
  "status": 1,
  "message": "Student referrals retrieved successfully",
  "total_records": 3,
  "data": [
    {
      "referral_id": 5,
      "student_name": "John Doe",
      "reference_by": "Staff Name"
    }
  ]
}
```

### Step 2: Create a New Student Referral

```bash
curl -X POST "http://localhost/amt/api/student-referral-api/create" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "student_id": 140,
    "reference_id": 2,
    "session_id": 18
  }'
```

**Response:**
```json
{
  "status": 1,
  "message": "Student referral created successfully",
  "data": {
    "referral_id": 8,
    "student_id": 140,
    "reference_id": 2,
    "session_id": 18
  }
}
```

### Step 3: Get Specific Student Referral

```bash
curl -X POST "http://localhost/amt/api/student-referral-api/get/8" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Step 4: Update the Student Referral

```bash
curl -X POST "http://localhost/amt/api/student-referral-api/update/8" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "reference_id": 6
  }'
```

### Step 5: Delete the Student Referral (if needed)

```bash
curl -X POST "http://localhost/amt/api/student-referral-api/delete/8" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

## Use Cases

### 1. **Referral Tracking**
   - Track which staff members referred which students
   - Monitor referral performance by staff member
   - Generate referral reports for analytics

### 2. **Admission Source Analysis**
   - Analyze admission sources
   - Track successful referrals
   - Identify top referring staff members

### 3. **Reporting and Analytics**
   - Generate reports by class, section, or staff member
   - Track referrals by session
   - Monitor referral trends over time

### 4. **Staff Performance**
   - Evaluate staff referral performance
   - Reward top referring staff members
   - Track referral success rates

---

## Database Schema

### student_reference Table

```sql
CREATE TABLE `student_reference` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

**Table Fields:**
- `id` (int, Primary Key, Auto Increment) - Unique identifier for the referral
- `session_id` (int, Optional) - Academic session ID
- `student_id` (int, Required) - ID of the student being referred
- `staff_id` (int, Required) - ID of the staff member who referred the student
- `created_at` (timestamp) - Timestamp when the referral was created

**Related Tables:**
- `students` - Student information
- `staff` - Staff member information
- `student_session` - Student class/section assignments
- `classes` - Class information
- `sections` - Section information

---

## Best Practices

1. **Always provide student_id and reference_id** when creating referrals
2. **Use valid student and staff IDs** that exist in the database
3. **Use the list endpoint with filters** to efficiently retrieve specific referrals
4. **Check existing referrals** before creating duplicates
5. **Handle error responses** appropriately in your application
6. **Log all API calls** for audit and debugging purposes
7. **Update records** when referral information needs to change
8. **Avoid deleting referrals** that are needed for historical reporting
9. **Use session_id** to track referrals by academic year
10. **Maintain data integrity** by ensuring student and staff IDs are valid

---

## Integration with Other APIs

### Student API
- Student referrals are linked to students via `student_id`
- Use the Student API to get detailed student information
- Verify student exists before creating a referral

### Staff API
- Student referrals are linked to staff members via `reference_id` (staff_id)
- Use the Staff API to get detailed staff information
- Verify staff member exists before creating a referral

### Class and Section APIs
- Filter referrals by class and section
- Use class and section IDs for filtering and reporting

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | 2025-11-13 | Initial release with create, read, update, delete, and list endpoints |

---

## Support

For issues or questions regarding this API, please contact the development team or refer to the main API documentation.

