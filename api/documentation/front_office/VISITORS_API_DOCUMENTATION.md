# Visitors API Documentation

## Overview

The **Visitors API** provides RESTful endpoints for managing visitor records in the school management system. This API enables you to create, read, update, and delete visitor records, track visitors meeting with staff or students, and filter visitors based on various criteria such as date range, meeting type, staff, or student.

**Base URL:** `http://localhost/amt/api`

**Authentication:** All endpoints require the following headers:
- `Client-Service: smartschool`
- `Auth-Key: schoolAdmin@`
- `Content-Type: application/json`

---

## ⚠️ Important Notes

**All endpoints use POST method** - Even for retrieving data, you must use POST (not GET).

**URL Placeholders** - When you see `{id}` in the documentation, replace it with an actual number:
- ❌ Wrong: `http://localhost/amt/api/visitors-api/get/{id}`
- ✅ Correct: `http://localhost/amt/api/visitors-api/get/1`

**Request Body** - Even if the endpoint doesn't require data, you must send an empty JSON object `{}` in the request body.

**Meeting Types** - Visitors can meet with either "staff" or "student". The required fields differ based on the meeting type:
- For "staff": `staff_id` is required
- For "student": `student_session_id` is required

---

## Endpoints

### 1. List All Visitors

**Endpoint:** `POST /visitors-api/list`

**Description:** Retrieves a list of all visitors with optional filtering and search capabilities. This endpoint supports multiple filter combinations and returns comprehensive visitor data including staff and student information.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Request Body:**
```json
{
  "staff_id": 5,
  "student_session_id": 10,
  "meeting_with": "staff",
  "date_from": "2025-01-01",
  "date_to": "2025-12-31",
  "search": "John"
}
```

**Request Parameters:**
| Parameter | Type | Required | Description | Example Values |
|-----------|------|----------|-------------|----------------|
| `staff_id` | integer | No | Filter by staff ID. Must be a valid staff ID | `5`, `10`, `15` |
| `student_session_id` | integer | No | Filter by student session ID. Must be a valid student session ID | `10`, `20`, `30` |
| `meeting_with` | string | No | Filter by meeting type. Must be "staff" or "student" | `"staff"`, `"student"` |
| `date_from` | string (YYYY-MM-DD) | No | Filter visitors from this date (inclusive) | `"2025-01-01"`, `"2025-11-10"` |
| `date_to` | string (YYYY-MM-DD) | No | Filter visitors up to this date (inclusive) | `"2025-12-31"`, `"2025-11-30"` |
| `search` | string | No | General search query that searches across name, contact, email, and ID proof fields (case-insensitive partial match) | `"John"`, `"9876543210"`, `"john@example.com"` |

**Important Notes:**
- All parameters are optional. If no parameters are provided, all visitors will be returned.
- Multiple filters can be combined. All filters are applied with AND logic.
- The `search` parameter performs a partial match across name, contact, email, and ID proof fields.
- Results are ordered by visitor ID in descending order (newest first).
- The endpoint returns related information including staff details, student details, class, and section information.

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Visitors retrieved successfully",
  "total_records": 3,
  "data": [
    {
      "id": 1,
      "staff_id": 5,
      "student_session_id": null,
      "source": null,
      "purpose": "Parent-Teacher Meeting",
      "name": "John Doe",
      "email": "john.doe@example.com",
      "contact": "9876543210",
      "id_proof": "A1234567",
      "no_of_people": 2,
      "date": "2025-11-10",
      "in_time": "10:00 AM",
      "out_time": "11:30 AM",
      "note": "Discussed student progress",
      "image": null,
      "meeting_with": "staff",
      "created_at": "2025-11-10 10:00:00",
      "class": null,
      "section": null,
      "staff_name": "Jane",
      "staff_surname": "Smith",
      "staff_employee_id": "EMP001",
      "class_id": null,
      "section_id": null,
      "students_id": null,
      "admission_no": null,
      "student_firstname": null,
      "student_middlename": null,
      "student_lastname": null
    },
    {
      "id": 2,
      "staff_id": null,
      "student_session_id": 10,
      "source": null,
      "purpose": "Guardian Visit",
      "name": "Mary Johnson",
      "email": "mary.j@example.com",
      "contact": "9876543211",
      "id_proof": "B2345678",
      "no_of_people": 1,
      "date": "2025-11-09",
      "in_time": "02:00 PM",
      "out_time": "02:45 PM",
      "note": "Regular check-in",
      "image": null,
      "meeting_with": "student",
      "created_at": "2025-11-09 14:00:00",
      "class": "Grade 10",
      "section": "A",
      "staff_name": null,
      "staff_surname": null,
      "staff_employee_id": null,
      "class_id": 10,
      "section_id": 5,
      "students_id": 25,
      "admission_no": "ADM001",
      "student_firstname": "Alice",
      "student_middlename": "",
      "student_lastname": "Johnson"
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

### 2. Get Specific Visitor

**Endpoint:** `POST /visitors-api/get/{id}`

**Description:** Retrieves details of a specific visitor by its ID.

**⚠️ Important:** 
- **Method must be POST** (not GET)
- Replace `{id}` with an actual visitor ID number (e.g., use `1` instead of `{id}`)
- Example URL: `http://localhost/amt/api/visitors-api/get/1`

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**URL Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `id` | integer | Yes | ID of the visitor | `1`, `2`, `5` |

**Request Body:** `{}` (empty JSON object)

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Visitor retrieved successfully",
  "data": {
    "id": 1,
    "staff_id": 5,
    "student_session_id": null,
    "source": null,
    "purpose": "Parent-Teacher Meeting",
    "name": "John Doe",
    "email": "john.doe@example.com",
    "contact": "9876543210",
    "id_proof": "A1234567",
    "no_of_people": 2,
    "date": "2025-11-10",
    "in_time": "10:00 AM",
    "out_time": "11:30 AM",
    "note": "Discussed student progress",
    "image": null,
    "meeting_with": "staff",
    "created_at": "2025-11-10 10:00:00"
  }
}
```

**Error Responses:**

**400 Bad Request - Invalid ID:**
```json
{
  "status": 0,
  "message": "Invalid or missing visitor ID",
  "data": null
}
```

**404 Not Found:**
```json
{
  "status": 0,
  "message": "Visitor not found",
  "data": null
}
```

---

### 3. Create New Visitor

**Endpoint:** `POST /visitors-api/create`

**Description:** Creates a new visitor record.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Request Body (Meeting with Staff):**
```json
{
  "meeting_with": "staff",
  "purpose": "Parent-Teacher Meeting",
  "name": "John Doe",
  "contact": "9876543210",
  "email": "john.doe@example.com",
  "id_proof": "A1234567",
  "no_of_people": 2,
  "date": "2025-11-10",
  "in_time": "10:00 AM",
  "out_time": "11:30 AM",
  "note": "Discussed student progress",
  "source": "Direct",
  "staff_id": 5,
  "image": null
}
```

**Request Body (Meeting with Student):**
```json
{
  "meeting_with": "student",
  "purpose": "Guardian Visit",
  "name": "Mary Johnson",
  "contact": "9876543211",
  "email": "mary.j@example.com",
  "id_proof": "B2345678",
  "no_of_people": 1,
  "date": "2025-11-10",
  "in_time": "02:00 PM",
  "out_time": "02:45 PM",
  "note": "Regular check-in",
  "source": "Direct",
  "student_session_id": 10,
  "image": null
}
```

**Request Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `meeting_with` | string | Yes | Type of meeting: "staff" or "student" | `"staff"`, `"student"` |
| `purpose` | string | Yes | Purpose of the visit | `"Parent-Teacher Meeting"`, `"Guardian Visit"` |
| `name` | string | Yes | Name of the visitor | `"John Doe"` |
| `contact` | string | Yes | Contact phone number | `"9876543210"` |
| `date` | string (YYYY-MM-DD) | Yes | Date of visit | `"2025-11-10"` |
| `staff_id` | integer | Yes* | ID of staff member (required if meeting_with is "staff") | `5`, `10` |
| `student_session_id` | integer | Yes* | ID of student session (required if meeting_with is "student") | `10`, `20` |
| `email` | string | No | Email address of the visitor | `"john@example.com"` |
| `id_proof` | string | No | ID proof number | `"A1234567"` |
| `no_of_people` | integer | No | Number of people (default: 1) | `1`, `2`, `3` |
| `in_time` | string | No | Time of entry | `"10:00 AM"`, `"14:30"` |
| `out_time` | string | No | Time of exit | `"11:30 AM"`, `"15:45"` |
| `note` | string | No | Additional notes | `"Discussed student progress"` |
| `source` | string | No | Source of visit | `"Direct"`, `"Appointment"` |
| `image` | string | No | Image file path or URL | `"uploads/visitors/image.jpg"` |

**Success Response (201 Created):**
```json
{
  "status": 1,
  "message": "Visitor created successfully",
  "data": {
    "id": 1,
    "meeting_with": "staff",
    "purpose": "Parent-Teacher Meeting",
    "name": "John Doe",
    "contact": "9876543210",
    "email": "john.doe@example.com",
    "id_proof": "A1234567",
    "no_of_people": 2,
    "date": "2025-11-10",
    "in_time": "10:00 AM",
    "out_time": "11:30 AM",
    "note": "Discussed student progress",
    "source": "Direct",
    "staff_id": 5,
    "student_session_id": null,
    "image": null,
    "created_at": "2025-11-10 10:00:00"
  }
}
```

**Error Responses:**

**400 Bad Request - Missing Required Field:**
```json
{
  "status": 0,
  "message": "Meeting with is required and must be either \"staff\" or \"student\"",
  "data": null
}
```

**400 Bad Request - Missing Staff ID:**
```json
{
  "status": 0,
  "message": "Staff ID is required when meeting_with is \"staff\"",
  "data": null
}
```

**400 Bad Request - Missing Student Session ID:**
```json
{
  "status": 0,
  "message": "Student session ID is required when meeting_with is \"student\"",
  "data": null
}
```

**404 Not Found - Staff doesn't exist:**
```json
{
  "status": 0,
  "message": "Staff not found",
  "data": null
}
```

---

### 4. Update Visitor

**Endpoint:** `POST /visitors-api/update/{id}`

**Description:** Updates an existing visitor record.

**⚠️ Important:** 
- **Method must be POST** (not PUT or PATCH)
- **Replace `{id}` with an actual visitor ID number** (e.g., use `1` instead of `{id}`)
- Example URL: `http://localhost/amt/api/visitors-api/update/1`
- You must have a valid visitor ID. Use the list endpoint first to get existing visitor IDs.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**URL Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `id` | integer | Yes | ID of the visitor to update | `1`, `2`, `5` |

**Request Body:**
```json
{
  "meeting_with": "staff",
  "purpose": "Parent-Teacher Meeting - Updated",
  "name": "John Doe",
  "contact": "9876543210",
  "email": "john.doe@example.com",
  "id_proof": "A1234567",
  "no_of_people": 2,
  "date": "2025-11-10",
  "in_time": "10:00 AM",
  "out_time": "12:00 PM",
  "note": "Extended meeting - discussed in detail",
  "source": "Direct",
  "staff_id": 5
}
```

**Request Parameters:** (Same as Create endpoint)

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Visitor updated successfully",
  "data": {
    "id": 1,
    "meeting_with": "staff",
    "purpose": "Parent-Teacher Meeting - Updated",
    "name": "John Doe",
    "contact": "9876543210",
    "email": "john.doe@example.com",
    "id_proof": "A1234567",
    "no_of_people": 2,
    "date": "2025-11-10",
    "in_time": "10:00 AM",
    "out_time": "12:00 PM",
    "note": "Extended meeting - discussed in detail",
    "source": "Direct",
    "staff_id": 5,
    "student_session_id": null,
    "image": null,
    "created_at": "2025-11-10 10:00:00"
  }
}
```

**Error Responses:**

**400 Bad Request - Invalid ID:**
```json
{
  "status": 0,
  "message": "Invalid or missing visitor ID",
  "data": null
}
```

**404 Not Found - Visitor doesn't exist:**
```json
{
  "status": 0,
  "message": "Visitor not found",
  "data": null
}
```

---

### 5. Delete Visitor

**Endpoint:** `POST /visitors-api/delete/{id}`

**Description:** Deletes a visitor record.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**URL Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `id` | integer | Yes | ID of the visitor to delete | `1`, `2`, `5` |

**Request Body:** `{}` (empty JSON object)

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Visitor deleted successfully",
  "data": {
    "id": 1
  }
}
```

**Error Responses:**

**400 Bad Request - Invalid ID:**
```json
{
  "status": 0,
  "message": "Invalid or missing visitor ID",
  "data": null
}
```

**404 Not Found:**
```json
{
  "status": 0,
  "message": "Visitor not found",
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
| 404 | Not Found | Resource not found (visitor, staff, or student session) |
| 405 | Method Not Allowed | Wrong HTTP method used (must be POST) |
| 500 | Internal Server Error | Server error occurred |

---

## cURL Examples

### Example 1: List All Visitors

```bash
curl -X POST "http://localhost/amt/api/visitors-api/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "meeting_with": "staff",
    "date_from": "2025-01-01",
    "date_to": "2025-12-31"
  }'
```

### Example 2: Get Specific Visitor

```bash
curl -X POST "http://localhost/amt/api/visitors-api/get/1" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 3: Create New Visitor (Meeting with Staff)

```bash
curl -X POST "http://localhost/amt/api/visitors-api/create" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "meeting_with": "staff",
    "purpose": "Parent-Teacher Meeting",
    "name": "John Doe",
    "contact": "9876543210",
    "email": "john.doe@example.com",
    "id_proof": "A1234567",
    "no_of_people": 2,
    "date": "2025-11-10",
    "in_time": "10:00 AM",
    "out_time": "11:30 AM",
    "note": "Discussed student progress",
    "source": "Direct",
    "staff_id": 5
  }'
```

### Example 4: Create New Visitor (Meeting with Student)

```bash
curl -X POST "http://localhost/amt/api/visitors-api/create" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "meeting_with": "student",
    "purpose": "Guardian Visit",
    "name": "Mary Johnson",
    "contact": "9876543211",
    "email": "mary.j@example.com",
    "id_proof": "B2345678",
    "no_of_people": 1,
    "date": "2025-11-10",
    "in_time": "02:00 PM",
    "out_time": "02:45 PM",
    "note": "Regular check-in",
    "source": "Direct",
    "student_session_id": 10
  }'
```

### Example 5: Update Visitor

```bash
curl -X POST "http://localhost/amt/api/visitors-api/update/1" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "meeting_with": "staff",
    "purpose": "Parent-Teacher Meeting - Updated",
    "name": "John Doe",
    "contact": "9876543210",
    "email": "john.doe@example.com",
    "id_proof": "A1234567",
    "no_of_people": 2,
    "date": "2025-11-10",
    "in_time": "10:00 AM",
    "out_time": "12:00 PM",
    "note": "Extended meeting",
    "source": "Direct",
    "staff_id": 5
  }'
```

### Example 6: Delete Visitor

```bash
curl -X POST "http://localhost/amt/api/visitors-api/delete/1" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

## Complete Workflow: Managing Visitors

### Step 1: Create a New Visitor (Meeting with Staff)

```bash
curl -X POST "http://localhost/amt/api/visitors-api/create" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "meeting_with": "staff",
    "purpose": "Parent-Teacher Meeting",
    "name": "John Doe",
    "contact": "9876543210",
    "email": "john.doe@example.com",
    "id_proof": "A1234567",
    "no_of_people": 2,
    "date": "2025-11-10",
    "in_time": "10:00 AM",
    "out_time": "11:30 AM",
    "note": "Discussed student progress",
    "source": "Direct",
    "staff_id": 5
  }'
```

**Response:**
```json
{
  "status": 1,
  "message": "Visitor created successfully",
  "data": {
    "id": 1,
    "name": "John Doe",
    "meeting_with": "staff",
    "date": "2025-11-10"
  }
}
```

### Step 2: List All Visitors

```bash
curl -X POST "http://localhost/amt/api/visitors-api/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Step 3: Get Specific Visitor

```bash
curl -X POST "http://localhost/amt/api/visitors-api/get/1" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Step 4: Update Visitor (Record Out Time)

```bash
curl -X POST "http://localhost/amt/api/visitors-api/update/1" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "meeting_with": "staff",
    "purpose": "Parent-Teacher Meeting",
    "name": "John Doe",
    "contact": "9876543210",
    "email": "john.doe@example.com",
    "id_proof": "A1234567",
    "no_of_people": 2,
    "date": "2025-11-10",
    "in_time": "10:00 AM",
    "out_time": "12:00 PM",
    "note": "Extended meeting - completed",
    "source": "Direct",
    "staff_id": 5
  }'
```

### Step 5: Delete Visitor (if needed)

```bash
curl -X POST "http://localhost/amt/api/visitors-api/delete/1" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

## Use Cases

### 1. **Visitor Registration**
   - Register visitors meeting with staff members
   - Register visitors meeting with students
   - Track entry and exit times
   - Record ID proof information

### 2. **Visitor Tracking**
   - Monitor all visitors on a specific date
   - Filter visitors by staff member
   - Filter visitors by student
   - Track visitors who haven't checked out

### 3. **Security Management**
   - Maintain visitor records for security purposes
   - Track ID proof information
   - Monitor visitor patterns
   - Generate visitor reports

### 4. **Parent-Teacher Meetings**
   - Record parent visits to meet teachers
   - Track meeting purposes
   - Maintain meeting notes
   - Schedule follow-up visits

### 5. **Guardian Visits**
   - Record guardian visits to meet students
   - Track visit frequency
   - Maintain visit history
   - Monitor student-visitor relationships

### 6. **Daily Visitor Reports**
   - Generate daily visitor lists
   - Filter by date range
   - Export visitor data
   - Analyze visitor patterns

---

## Database Schema

### visitors_book Table

```sql
CREATE TABLE `visitors_book` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `staff_id` int(11) DEFAULT NULL,
  `student_session_id` int(11) DEFAULT NULL,
  `source` varchar(100) DEFAULT NULL,
  `purpose` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `contact` varchar(12) NOT NULL,
  `id_proof` varchar(50) NOT NULL,
  `no_of_people` int(11) NOT NULL,
  `date` date NOT NULL,
  `in_time` varchar(20) NOT NULL,
  `out_time` varchar(20) NOT NULL,
  `note` text NOT NULL,
  `image` varchar(100) DEFAULT NULL,
  `meeting_with` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `staff_id` (`staff_id`),
  KEY `student_session_id` (`student_session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

### Key Relationships

```
staff (id) ← visitors_book (staff_id)
student_session (id) ← visitors_book (student_session_id)
```

---

## Best Practices

1. **Always validate meeting type** before creating or updating visitors
2. **Use proper date format (YYYY-MM-DD)** for all date fields
3. **Validate staff and student session IDs** before assigning them to visitors
4. **Use the list endpoint with filters** to efficiently retrieve specific visitors
5. **Record in_time and out_time** for complete visitor tracking
6. **Maintain ID proof information** for security purposes
7. **Use search functionality** to quickly find visitors by name, contact, email, or ID proof
8. **Handle error responses** appropriately in your application
9. **Log all API calls** for audit and debugging purposes
10. **Update out_time** when visitors check out to maintain accurate records

---

## Integration with Other APIs

### Staff API
- **List Staff:** `POST /api/staff-api/list`
- **Get Staff:** `POST /api/staff-api/get/{id}`

### Student Session API
- **List Student Sessions:** Use student-related APIs to get student session IDs

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | 2025-11-10 | Initial release with create, read, update, delete, and list endpoints |

---

## Support

For issues or questions regarding this API, please contact the development team or refer to the main API documentation.

