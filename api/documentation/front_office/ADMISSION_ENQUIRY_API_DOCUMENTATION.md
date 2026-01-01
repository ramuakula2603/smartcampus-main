# Admission Enquiry API Documentation

## Overview

The **Admission Enquiry API** provides RESTful endpoints for managing admission enquiries in the school management system. This API enables you to create, read, update, and delete admission enquiry records, as well as filter and search enquiries based on various criteria such as class, source, date range, and status.

**Base URL:** `http://localhost/amt/api`

**Authentication:** All endpoints require the following headers:
- `Client-Service: smartschool`
- `Auth-Key: schoolAdmin@`
- `Content-Type: application/json`

---

## ⚠️ Important Notes

**All endpoints use POST method** - Even for retrieving data, you must use POST (not GET).

**URL Placeholders** - When you see `{id}` in the documentation, replace it with an actual number:
- ❌ Wrong: `http://localhost/amt/api/admission-enquiry-api/get/{id}`
- ✅ Correct: `http://localhost/amt/api/admission-enquiry-api/get/1`

**Request Body** - Even if the endpoint doesn't require data, you must send an empty JSON object `{}` in the request body.

---

## Endpoints

### 1. List All Admission Enquiries

**Endpoint:** `POST /admission-enquiry-api/list`

**Description:** Retrieves a list of all admission enquiries with optional filtering and search capabilities. This endpoint supports multiple filter combinations and returns comprehensive enquiry data including follow-up information.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Request Body:**
```json
{
  "class_id": 4,
  "source": "Front Office",
  "enquiry_from_date": "2023-01-01",
  "enquiry_to_date": "2023-12-31",
  "status": "active",
  "search": "Aadi"
}
```

**Request Parameters:**
| Parameter | Type | Required | Description | Example Values |
|-----------|------|----------|-------------|----------------|
| `class_id` | integer | No | Filter by class ID. Must be a valid class ID from the classes table | `4`, `10`, `11` |
| `source` | string | No | Filter by enquiry source. Must match exactly with source values in database | `"Front Office"`, `"Advertisement"`, `"Google Ads"`, `"Admission Campaign"`, `"Website"`, `"Referral"` |
| `enquiry_from_date` | string (YYYY-MM-DD) | No | Filter enquiries from this date (inclusive). Date must be in YYYY-MM-DD format | `"2023-01-01"`, `"2025-11-10"` |
| `enquiry_to_date` | string (YYYY-MM-DD) | No | Filter enquiries up to this date (inclusive). Date must be in YYYY-MM-DD format | `"2023-12-31"`, `"2025-11-30"` |
| `status` | string | No | Filter by status. Default is "active" if not provided | `"active"`, `"inactive"`, `"converted"`, `"lost"` |
| `search` | string | No | General search query that searches across name, contact, and email fields (case-insensitive partial match) | `"Aadi"`, `"98067867866"`, `"aadi@gmail.com"` |

**Important Notes:**
- All parameters are optional. If no parameters are provided, all active enquiries will be returned.
- Multiple filters can be combined. All filters are applied with AND logic.
- The `search` parameter performs a partial match across name, contact, and email fields.
- Results are ordered by enquiry date in descending order (newest first).
- The endpoint returns follow-up information (last follow-up date, next follow-up date, response, and notes) for each enquiry.

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Admission enquiries retrieved successfully",
  "total_records": 6,
  "data": [
    {
      "id": 1,
      "name": "Aadi Shinde",
      "contact": "98067867866",
      "address": "MR 4 Road South Delhi",
      "reference": "Parent",
      "date": "2023-01-18",
      "description": "Admission Enquiry",
      "follow_up_date": "2023-01-20",
      "note": "Admission Enquiry",
      "source": "Front Office",
      "email": "aadi@gmail.com",
      "assigned": null,
      "class_id": 4,
      "no_of_child": "5",
      "status": "active",
      "created_by": 1,
      "created_at": "2023-08-12 00:06:11",
      "class_name": "Grade 4",
      "assigned_staff_name": null,
      "assigned_staff_surname": null,
      "assigned_staff_employee_id": null,
      "last_follow_up_date": "2023-01-20",
      "next_follow_up_date": "2023-01-20",
      "last_follow_up_response": "Interested in admission",
      "last_follow_up_note": "Follow up scheduled"
    },
    {
      "id": 2,
      "name": "Abhimnayu",
      "contact": "9125242523",
      "address": "",
      "reference": "",
      "date": "2022-11-01",
      "description": "",
      "follow_up_date": "2022-11-09",
      "note": "",
      "source": "Advertisement",
      "email": null,
      "assigned": null,
      "class_id": null,
      "no_of_child": null,
      "status": "active",
      "created_by": 1,
      "created_at": "2023-08-12 00:06:11",
      "class_name": null,
      "assigned_staff_name": null,
      "assigned_staff_surname": null,
      "assigned_staff_employee_id": null,
      "last_follow_up_date": "2022-11-09",
      "next_follow_up_date": "2022-11-09",
      "last_follow_up_response": null,
      "last_follow_up_note": null
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

## Detailed List Endpoint Examples

### Example 1: Get All Active Enquiries (No Filters)

**Request:**
```bash
curl -X POST "http://localhost/amt/api/admission-enquiry-api/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Response:**
```json
{
  "status": 1,
  "message": "Admission enquiries retrieved successfully",
  "total_records": 6,
  "data": [
    {
      "id": 1,
      "name": "Aadi Shinde",
      "contact": "98067867866",
      "source": "Front Office",
      "date": "2023-01-18",
      "status": "active",
      "class_name": "Grade 4",
      "last_follow_up_date": "2023-01-20",
      "next_follow_up_date": "2023-01-20"
    }
  ]
}
```

### Example 2: Filter by Class ID

**Request:**
```bash
curl -X POST "http://localhost/amt/api/admission-enquiry-api/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "class_id": 4
  }'
```

**Response:** Returns only enquiries for class ID 4.

### Example 3: Filter by Source

**Request:**
```bash
curl -X POST "http://localhost/amt/api/admission-enquiry-api/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "source": "Front Office"
  }'
```

**Response:** Returns only enquiries from "Front Office" source.

### Example 4: Filter by Date Range

**Request:**
```bash
curl -X POST "http://localhost/amt/api/admission-enquiry-api/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "enquiry_from_date": "2023-01-01",
    "enquiry_to_date": "2023-12-31"
  }'
```

**Response:** Returns enquiries between January 1, 2023 and December 31, 2023 (inclusive).

### Example 5: Filter by Status

**Request:**
```bash
curl -X POST "http://localhost/amt/api/admission-enquiry-api/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "status": "active"
  }'
```

**Response:** Returns only active enquiries.

### Example 6: Search by Name, Contact, or Email

**Request:**
```bash
curl -X POST "http://localhost/amt/api/admission-enquiry-api/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "search": "Aadi"
  }'
```

**Response:** Returns enquiries where name, contact, or email contains "Aadi" (case-insensitive).

### Example 7: Combined Filters (Class + Source + Date Range)

**Request:**
```bash
curl -X POST "http://localhost/amt/api/admission-enquiry-api/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "class_id": 4,
    "source": "Front Office",
    "enquiry_from_date": "2023-01-01",
    "enquiry_to_date": "2023-12-31",
    "status": "active"
  }'
```

**Response:** Returns active enquiries for class 4 from "Front Office" source within the specified date range.

### Example 8: Search with Other Filters

**Request:**
```bash
curl -X POST "http://localhost/amt/api/admission-enquiry-api/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "source": "Advertisement",
    "status": "active",
    "search": "9125242523"
  }'
```

**Response:** Returns active enquiries from "Advertisement" source where name, contact, or email contains "9125242523".

### Example 9: Get All Statuses (Not Just Active)

**Request:**
```bash
curl -X POST "http://localhost/amt/api/admission-enquiry-api/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "status": "converted"
  }'
```

**Response:** Returns only converted enquiries.

### Example 10: Empty Result Set

**Request:**
```bash
curl -X POST "http://localhost/amt/api/admission-enquiry-api/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "class_id": 999,
    "status": "active"
  }'
```

**Response:**
```json
{
  "status": 1,
  "message": "Admission enquiries retrieved successfully",
  "total_records": 0,
  "data": []
}
```

---

## Response Data Structure

Each enquiry object in the response array contains the following fields:

| Field | Type | Description |
|-------|------|-------------|
| `id` | integer | Unique identifier for the enquiry |
| `name` | string | Name of the enquirer |
| `contact` | string | Contact phone number |
| `address` | string | Address of the enquirer |
| `reference` | string | Reference source |
| `date` | string (YYYY-MM-DD) | Date of the enquiry |
| `description` | string | Description of the enquiry |
| `follow_up_date` | string (YYYY-MM-DD) | Next scheduled follow-up date |
| `note` | string | Additional notes |
| `source` | string | Source of the enquiry |
| `email` | string/null | Email address of the enquirer |
| `assigned` | integer/null | ID of assigned staff member |
| `class_id` | integer/null | ID of the class |
| `no_of_child` | string/null | Number of children |
| `status` | string | Status of the enquiry |
| `created_by` | integer | ID of user who created the enquiry |
| `created_at` | string (timestamp) | Creation timestamp |
| `class_name` | string/null | Name of the class (from classes table) |
| `assigned_staff_name` | string/null | First name of assigned staff |
| `assigned_staff_surname` | string/null | Surname of assigned staff |
| `assigned_staff_employee_id` | string/null | Employee ID of assigned staff |
| `last_follow_up_date` | string/null (YYYY-MM-DD) | Date of last follow-up |
| `next_follow_up_date` | string/null (YYYY-MM-DD) | Date of next scheduled follow-up |
| `last_follow_up_response` | string/null | Response from last follow-up |
| `last_follow_up_note` | string/null | Notes from last follow-up |

---

## Filtering Logic

### Date Range Filtering
- `enquiry_from_date`: Returns enquiries where `date >= enquiry_from_date` (inclusive)
- `enquiry_to_date`: Returns enquiries where `date <= enquiry_to_date` (inclusive)
- Both dates can be used together to create a date range
- Date format must be YYYY-MM-DD (e.g., "2023-01-01")

### Search Filtering
- The `search` parameter performs a case-insensitive partial match
- Searches across three fields: `name`, `contact`, and `email`
- Uses OR logic: returns records matching any of the three fields
- Example: `"search": "Aadi"` will match:
  - Name contains "Aadi"
  - Contact contains "Aadi"
  - Email contains "Aadi"

### Status Filtering
- Default status is "active" if not specified
- Common status values: "active", "inactive", "converted", "lost"
- Status must match exactly (case-sensitive)

### Combined Filters
- All filters use AND logic when combined
- Example: `{"class_id": 4, "source": "Front Office", "status": "active"}` returns enquiries that:
  - Have class_id = 4 AND
  - Have source = "Front Office" AND
  - Have status = "active"

---

## Common Use Cases

### 1. Get Today's Enquiries
```json
{
  "enquiry_from_date": "2025-11-10",
  "enquiry_to_date": "2025-11-10",
  "status": "active"
}
```

### 2. Get This Month's Enquiries
```json
{
  "enquiry_from_date": "2025-11-01",
  "enquiry_to_date": "2025-11-30",
  "status": "active"
}
```

### 3. Find Enquiry by Phone Number
```json
{
  "search": "98067867866"
}
```

### 4. Get Enquiries for Specific Class
```json
{
  "class_id": 10,
  "status": "active"
}
```

### 5. Get Enquiries from Specific Source
```json
{
  "source": "Google Ads",
  "status": "active"
}
```

### 6. Get Overdue Follow-ups (requires additional processing on client side)
```json
{
  "status": "active"
}
```
Then filter client-side where `next_follow_up_date < today's date`.

---

### 2. Get Specific Admission Enquiry

**Endpoint:** `POST /admission-enquiry-api/get/{id}`

**Description:** Retrieves details of a specific admission enquiry by its ID.

**⚠️ Important:** 
- **Method must be POST** (not GET)
- Replace `{id}` with an actual enquiry ID number (e.g., use `1` instead of `{id}`)
- Example URL: `http://localhost/amt/api/admission-enquiry-api/get/1`

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**URL Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `id` | integer | Yes | ID of the admission enquiry | `1`, `2`, `5` |

**Request Body:** `{}` (empty JSON object)

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Admission enquiry retrieved successfully",
  "data": {
    "id": 1,
    "name": "Aadi Shinde",
    "contact": "98067867866",
    "address": "MR 4 Road South Delhi",
    "reference": "Parent",
    "date": "2023-01-18",
    "description": "Admission Enquiry",
    "follow_up_date": "2023-01-20",
    "note": "Admission Enquiry",
    "source": "Front Office",
    "email": "aadi@gmail.com",
    "assigned": null,
    "class_id": 4,
    "no_of_child": "5",
    "status": "active",
    "created_by": 1,
    "created_at": "2023-08-12 00:06:11",
    "classname": "Grade 4",
    "staff_id": null,
    "staff_name": null,
    "staff_surname": null,
    "employee_id": null,
    "last_follow_up_date": "2023-01-20",
    "next_follow_up_date": "2023-01-20",
    "last_follow_up_response": "Interested in admission",
    "last_follow_up_note": "Follow up scheduled"
  }
}
```

**Error Responses:**

**400 Bad Request - Invalid ID:**
```json
{
  "status": 0,
  "message": "Invalid or missing admission enquiry ID",
  "data": null
}
```

**404 Not Found:**
```json
{
  "status": 0,
  "message": "Admission enquiry not found",
  "data": null
}
```

---

### 3. Create New Admission Enquiry

**Endpoint:** `POST /admission-enquiry-api/create`

**Description:** Creates a new admission enquiry record.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Request Body:**
```json
{
  "name": "New Student Enquiry",
  "contact": "9988776655",
  "address": "123 Main Street, City",
  "reference": "Parent",
  "date": "2025-11-10",
  "description": "Enquired about admission process for 11th grade",
  "follow_up_date": "2025-11-15",
  "note": "Interested in science stream",
  "source": "Website",
  "email": "newstudent@example.com",
  "assigned": 5,
  "class_id": 11,
  "no_of_child": "1",
  "status": "active",
  "created_by": 1
}
```

**Request Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `name` | string | Yes | Name of the enquirer |
| `contact` | string | Yes | Contact phone number |
| `address` | string | No | Address of the enquirer |
| `reference` | string | No | Reference source |
| `date` | string (YYYY-MM-DD) | Yes | Enquiry date |
| `description` | string | No | Description of the enquiry |
| `follow_up_date` | string (YYYY-MM-DD) | Yes | Next follow-up date |
| `note` | string | No | Additional notes |
| `source` | string | Yes | Source of enquiry (e.g., "Front Office", "Advertisement", "Google Ads", "Admission Campaign", "Website") |
| `email` | string | No | Email address of the enquirer |
| `assigned` | integer | No | ID of staff member assigned to handle the enquiry |
| `class_id` | integer | No | ID of the class for which admission is enquired |
| `no_of_child` | string | No | Number of children |
| `status` | string | No | Status of enquiry (default: "active") |
| `created_by` | integer | No | ID of user creating the enquiry (default: 1) |

**Success Response (201 Created):**
```json
{
  "status": 1,
  "message": "Admission enquiry created successfully",
  "data": {
    "id": 7,
    "name": "New Student Enquiry",
    "contact": "9988776655",
    "address": "123 Main Street, City",
    "reference": "Parent",
    "date": "2025-11-10",
    "description": "Enquired about admission process for 11th grade",
    "follow_up_date": "2025-11-15",
    "note": "Interested in science stream",
    "source": "Website",
    "email": "newstudent@example.com",
    "assigned": 5,
    "class_id": 11,
    "no_of_child": "1",
    "status": "active",
    "created_by": 1,
    "created_at": "2025-11-10 12:30:00",
    "classname": "Grade 11",
    "staff_id": 5,
    "staff_name": "John",
    "staff_surname": "Doe",
    "employee_id": "EMP001"
  }
}
```

**Error Responses:**

**400 Bad Request - Missing Required Field:**
```json
{
  "status": 0,
  "message": "Name is required",
  "data": null
}
```

**400 Bad Request - Invalid Date Format:**
```json
{
  "status": 0,
  "message": "Invalid date format. Use YYYY-MM-DD format",
  "data": null
}
```

**404 Not Found - Class doesn't exist:**
```json
{
  "status": 0,
  "message": "Class not found",
  "data": null
}
```

**404 Not Found - Staff doesn't exist:**
```json
{
  "status": 0,
  "message": "Assigned staff not found",
  "data": null
}
```

---

### 4. Update Admission Enquiry

**Endpoint:** `POST /admission-enquiry-api/update/{id}`

**Description:** Updates an existing admission enquiry record.

**⚠️ Important:** 
- **Method must be POST** (not PUT or PATCH)
- **Replace `{id}` with an actual enquiry ID number** (e.g., use `1` instead of `{id}`)
- Example URL: `http://localhost/amt/api/admission-enquiry-api/update/1`
- You must have a valid enquiry ID. Use the list endpoint first to get existing enquiry IDs.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**URL Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `id` | integer | Yes | ID of the admission enquiry to update | `1`, `2`, `5` |

**Request Body:**
```json
{
  "name": "Aadi Shinde Updated",
  "contact": "98067867866",
  "address": "MR 4 Road South Delhi",
  "reference": "Parent",
  "date": "2023-01-18",
  "description": "Admission Enquiry - Updated",
  "follow_up_date": "2023-02-01",
  "note": "Follow-up completed",
  "source": "Front Office",
  "email": "aadi@gmail.com",
  "assigned": 3,
  "class_id": 4,
  "no_of_child": "5"
}
```

**Request Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `name` | string | Yes | Name of the enquirer |
| `contact` | string | Yes | Contact phone number |
| `address` | string | No | Address of the enquirer |
| `reference` | string | No | Reference source |
| `date` | string (YYYY-MM-DD) | Yes | Enquiry date |
| `description` | string | No | Description of the enquiry |
| `follow_up_date` | string (YYYY-MM-DD) | Yes | Next follow-up date |
| `note` | string | No | Additional notes |
| `source` | string | Yes | Source of enquiry |
| `email` | string | No | Email address of the enquirer |
| `assigned` | integer | No | ID of staff member assigned to handle the enquiry |
| `class_id` | integer | No | ID of the class for which admission is enquired |
| `no_of_child` | string | No | Number of children |

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Admission enquiry updated successfully",
  "data": {
    "id": 1,
    "name": "Aadi Shinde Updated",
    "contact": "98067867866",
    "address": "MR 4 Road South Delhi",
    "reference": "Parent",
    "date": "2023-01-18",
    "description": "Admission Enquiry - Updated",
    "follow_up_date": "2023-02-01",
    "note": "Follow-up completed",
    "source": "Front Office",
    "email": "aadi@gmail.com",
    "assigned": 3,
    "class_id": 4,
    "no_of_child": "5",
    "status": "active",
    "created_by": 1,
    "created_at": "2023-08-12 00:06:11",
    "classname": "Grade 4",
    "staff_id": 3,
    "staff_name": "Jane",
    "staff_surname": "Smith",
    "employee_id": "EMP003"
  }
}
```

**Error Responses:**

**400 Bad Request - Invalid ID:**
```json
{
  "status": 0,
  "message": "Invalid or missing admission enquiry ID",
  "data": null
}
```

**404 Not Found - Enquiry doesn't exist:**
```json
{
  "status": 0,
  "message": "Admission enquiry not found",
  "data": null
}
```

**404 Not Found - Class doesn't exist:**
```json
{
  "status": 0,
  "message": "Class not found",
  "data": null
}
```

---

### 5. Delete Admission Enquiry

**Endpoint:** `POST /admission-enquiry-api/delete/{id}`

**Description:** Deletes an admission enquiry record.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**URL Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `id` | integer | Yes | ID of the admission enquiry to delete |

**Request Body:** `{}` (empty JSON object)

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Admission enquiry deleted successfully",
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
  "message": "Invalid or missing admission enquiry ID",
  "data": null
}
```

**404 Not Found:**
```json
{
  "status": 0,
  "message": "Admission enquiry not found",
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
| 404 | Not Found | Resource not found (enquiry, class, or staff) |
| 405 | Method Not Allowed | Wrong HTTP method used (must be POST) |
| 500 | Internal Server Error | Server error occurred |

---

## cURL Examples

### Example 1: List All Admission Enquiries

```bash
curl -X POST "http://localhost/amt/api/admission-enquiry-api/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "class_id": 4,
    "source": "Front Office",
    "enquiry_from_date": "2023-01-01",
    "enquiry_to_date": "2023-12-31",
    "status": "active",
    "search": "Aadi"
  }'
```

### Example 2: Get Specific Admission Enquiry

```bash
curl -X POST "http://localhost/amt/api/admission-enquiry-api/get/1" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 3: Create New Admission Enquiry

```bash
curl -X POST "http://localhost/amt/api/admission-enquiry-api/create" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "name": "New Student Enquiry",
    "contact": "9988776655",
    "address": "123 Main Street, City",
    "reference": "Parent",
    "date": "2025-11-10",
    "description": "Enquired about admission process for 11th grade",
    "follow_up_date": "2025-11-15",
    "note": "Interested in science stream",
    "source": "Website",
    "email": "newstudent@example.com",
    "assigned": 5,
    "class_id": 11,
    "no_of_child": "1",
    "status": "active",
    "created_by": 1
  }'
```

### Example 4: Update Admission Enquiry

```bash
curl -X POST "http://localhost/amt/api/admission-enquiry-api/update/1" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "name": "Aadi Shinde Updated",
    "contact": "98067867866",
    "address": "MR 4 Road South Delhi",
    "reference": "Parent",
    "date": "2023-01-18",
    "description": "Admission Enquiry - Updated",
    "follow_up_date": "2023-02-01",
    "note": "Follow-up completed",
    "source": "Front Office",
    "email": "aadi@gmail.com",
    "assigned": 3,
    "class_id": 4,
    "no_of_child": "5"
  }'
```

### Example 5: Delete Admission Enquiry

```bash
curl -X POST "http://localhost/amt/api/admission-enquiry-api/delete/1" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

## Complete Workflow: Managing Admission Enquiries

This section demonstrates the complete workflow for managing admission enquiries.

### Step 1: Create a New Admission Enquiry

```bash
curl -X POST "http://localhost/amt/api/admission-enquiry-api/create" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "name": "John Doe",
    "contact": "9876543210",
    "address": "123 Education Street",
    "reference": "Parent",
    "date": "2025-11-10",
    "description": "Interested in admission for Grade 10",
    "follow_up_date": "2025-11-15",
    "note": "Call back after 5 PM",
    "source": "Website",
    "email": "johndoe@example.com",
    "assigned": 5,
    "class_id": 10,
    "no_of_child": "1",
    "status": "active"
  }'
```

**Response:**
```json
{
  "status": 1,
  "message": "Admission enquiry created successfully",
  "data": {
    "id": 8,
    "name": "John Doe",
    "contact": "9876543210",
    "date": "2025-11-10",
    "source": "Website",
    "class_id": 10,
    "status": "active"
  }
}
```

### Step 2: List All Enquiries with Filters

```bash
curl -X POST "http://localhost/amt/api/admission-enquiry-api/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "class_id": 10,
    "source": "Website",
    "status": "active",
    "enquiry_from_date": "2025-11-01",
    "enquiry_to_date": "2025-11-30"
  }'
```

### Step 3: Get Specific Enquiry Details

```bash
curl -X POST "http://localhost/amt/api/admission-enquiry-api/get/8" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Step 4: Update the Enquiry

```bash
curl -X POST "http://localhost/amt/api/admission-enquiry-api/update/8" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "name": "John Doe",
    "contact": "9876543210",
    "address": "123 Education Street",
    "reference": "Parent",
    "date": "2025-11-10",
    "description": "Interested in admission for Grade 10 - Follow up done",
    "follow_up_date": "2025-11-20",
    "note": "Parent visited school, application submitted",
    "source": "Website",
    "email": "johndoe@example.com",
    "assigned": 5,
    "class_id": 10,
    "no_of_child": "1"
  }'
```

### Step 5: Delete the Enquiry (if needed)

```bash
curl -X POST "http://localhost/amt/api/admission-enquiry-api/delete/8" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

## Use Cases

### 1. **Bulk Enquiry Management**
   - Create multiple enquiries from different sources
   - Filter enquiries by date range, class, or source
   - Assign enquiries to specific staff members
   - Track follow-up dates and responses

### 2. **Enquiry Tracking**
   - Monitor enquiry status (active, converted, lost)
   - Track follow-up dates and responses
   - Generate reports based on enquiry sources
   - Analyze conversion rates by source

### 3. **Staff Assignment**
   - Assign enquiries to specific staff members
   - Track which staff member is handling which enquiry
   - Monitor staff performance in handling enquiries

### 4. **Class-wise Enquiry Analysis**
   - Filter enquiries by class
   - Identify popular classes for admission
   - Plan admission strategies based on class-wise enquiries

### 5. **Source-based Marketing**
   - Track which sources generate the most enquiries
   - Analyze effectiveness of different marketing channels
   - Optimize marketing budget based on source performance

### 6. **Follow-up Management**
   - Schedule and track follow-up dates
   - Record follow-up responses and notes
   - Ensure timely follow-up on all enquiries

---

## Database Schema

### enquiry Table

```sql
CREATE TABLE `enquiry` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `reference` varchar(20) NOT NULL,
  `date` date NOT NULL,
  `description` varchar(500) NOT NULL,
  `follow_up_date` date NOT NULL,
  `note` text NOT NULL,
  `source` varchar(50) NOT NULL,
  `email` varchar(50) DEFAULT NULL,
  `assigned` int(11) DEFAULT NULL,
  `class_id` int(11) DEFAULT NULL,
  `no_of_child` varchar(11) DEFAULT NULL,
  `status` varchar(100) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `assigned` (`assigned`),
  KEY `class_id` (`class_id`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

### follow_up Table

```sql
CREATE TABLE `follow_up` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `enquiry_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `next_date` date NOT NULL,
  `response` text NOT NULL,
  `note` text NOT NULL,
  `followup_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `enquiry_id` (`enquiry_id`),
  KEY `followup_by` (`followup_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

### Key Relationships

```
classes (id) ← enquiry (class_id)
staff (id) ← enquiry (assigned)
staff (id) ← enquiry (created_by)
enquiry (id) ← follow_up (enquiry_id)
staff (id) ← follow_up (followup_by)
```

---

## Best Practices

1. **Always validate required fields** before creating or updating enquiries
2. **Use proper date format (YYYY-MM-DD)** for all date fields
3. **Validate class and staff IDs** before assigning them to enquiries
4. **Use the list endpoint with filters** to efficiently retrieve specific enquiries
5. **Track follow-up dates** to ensure timely follow-up on all enquiries
6. **Use status field** to track enquiry lifecycle (active, converted, lost)
7. **Handle error responses** appropriately in your application
8. **Log all API calls** for audit and debugging purposes
9. **Use search functionality** to quickly find enquiries by name, contact, or email
10. **Maintain data consistency** by validating foreign key relationships

---

## Integration with Other APIs

### Classes API
- **List Classes:** `POST /api/classes-api/list`
- **Get Class:** `POST /api/classes-api/get/{id}`

### Staff API
- **List Staff:** `POST /api/staff-api/list`
- **Get Staff:** `POST /api/staff-api/get/{id}`

### Source Management
- Sources are typically managed through the admin panel
- Common sources: "Front Office", "Advertisement", "Google Ads", "Admission Campaign", "Website", "Referral"

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | 2025-11-10 | Initial release with create, read, update, delete, and list endpoints |

---

## Support

For issues or questions regarding this API, please contact the development team or refer to the main API documentation.


