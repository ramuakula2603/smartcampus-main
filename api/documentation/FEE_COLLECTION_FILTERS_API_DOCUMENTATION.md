# Fee Collection Filters API Documentation

## Overview

The Fee Collection Filters API provides filter options for fee collection reports. This API returns all available filter options including sessions, classes, sections, fee groups, fee types, staff collectors, and grouping options. It supports hierarchical filtering where classes can be filtered by session and sections can be filtered by class.

---

## Base URL
```
http://{domain}/api/
```

## Important URL Structure

**For Fee Collection Filters API, use the controller/method pattern:**
- Get filter options: `http://{domain}/api/fee-collection-filters/get`

**Examples:**
- Get all filters: `http://localhost/amt/api/fee-collection-filters/get`

## Authentication Headers

All API requests require the following headers:
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

---

## Endpoint

### Get Fee Collection Filter Options

**Endpoint:** `POST /fee-collection-filters/get`
**Full URL:** `http://localhost/amt/api/fee-collection-filters/get`

**Description:** Retrieve all available filter options for fee collection reports. Supports hierarchical filtering based on session_id and class_id parameters.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

---

## Request Examples

### Example 1: Get All Filter Options (Empty Request)

**Request Body:**
```json
{}
```

**Description:** Returns all available filter options without any filtering. This is the default behavior when no parameters are provided.

**Success Response (HTTP 200):**
```json
{
  "status": 1,
  "message": "Filter options retrieved successfully",
  "data": {
    "sessions": [
      {
        "id": 21,
        "name": "2024-2025"
      },
      {
        "id": 20,
        "name": "2023-2024"
      }
    ],
    "classes": [
      {
        "id": 19,
        "name": "Class 1"
      },
      {
        "id": 20,
        "name": "Class 2"
      },
      {
        "id": 21,
        "name": "Class 3"
      }
    ],
    "sections": [
      {
        "id": 1,
        "name": "Section A"
      },
      {
        "id": 2,
        "name": "Section B"
      },
      {
        "id": 3,
        "name": "Section C"
      }
    ],
    "fee_groups": [
      {
        "id": 1,
        "name": "Tuition Fees"
      },
      {
        "id": 2,
        "name": "Transport Fees"
      }
    ],
    "fee_types": [
      {
        "id": 1,
        "name": "Monthly Fee",
        "code": "MF001"
      },
      {
        "id": 2,
        "name": "Admission Fee",
        "code": "AF001"
      }
    ],
    "collect_by": [
      {
        "id": 1,
        "name": "John Doe",
        "employee_id": "EMP001"
      },
      {
        "id": 2,
        "name": "Jane Smith",
        "employee_id": "EMP002"
      }
    ],
    "group_by_options": [
      "class",
      "collect",
      "mode"
    ]
  }
}
```

---

### Example 2: Filter Classes by Session

**Request Body:**
```json
{
  "session_id": 21
}
```

**Description:** Returns all filter options, but the `classes` array will only contain classes that have students enrolled in session 21.

**Success Response (HTTP 200):**
```json
{
  "status": 1,
  "message": "Filter options retrieved successfully",
  "data": {
    "sessions": [
      {
        "id": 21,
        "name": "2024-2025"
      },
      {
        "id": 20,
        "name": "2023-2024"
      }
    ],
    "classes": [
      {
        "id": 19,
        "name": "Class 1"
      },
      {
        "id": 20,
        "name": "Class 2"
      }
    ],
    "sections": [
      {
        "id": 1,
        "name": "Section A"
      },
      {
        "id": 2,
        "name": "Section B"
      }
    ],
    "fee_groups": [
      {
        "id": 1,
        "name": "Tuition Fees"
      }
    ],
    "fee_types": [
      {
        "id": 1,
        "name": "Monthly Fee",
        "code": "MF001"
      }
    ],
    "collect_by": [
      {
        "id": 1,
        "name": "John Doe",
        "employee_id": "EMP001"
      }
    ],
    "group_by_options": [
      "class",
      "collect",
      "mode"
    ]
  }
}
```

---

### Example 3: Filter Sections by Class

**Request Body:**
```json
{
  "session_id": 21,
  "class_id": 19
}
```

**Description:** Returns all filter options, but the `sections` array will only contain sections that belong to class 19.

**Success Response (HTTP 200):**
```json
{
  "status": 1,
  "message": "Filter options retrieved successfully",
  "data": {
    "sessions": [
      {
        "id": 21,
        "name": "2024-2025"
      }
    ],
    "classes": [
      {
        "id": 19,
        "name": "Class 1"
      }
    ],
    "sections": [
      {
        "id": 1,
        "name": "Section A"
      },
      {
        "id": 2,
        "name": "Section B"
      }
    ],
    "fee_groups": [
      {
        "id": 1,
        "name": "Tuition Fees"
      }
    ],
    "fee_types": [
      {
        "id": 1,
        "name": "Monthly Fee",
        "code": "MF001"
      }
    ],
    "collect_by": [
      {
        "id": 1,
        "name": "John Doe",
        "employee_id": "EMP001"
      }
    ],
    "group_by_options": [
      "class",
      "collect",
      "mode"
    ]
  }
}
```

---

## Response Structure

### Filter Options Object

| Field | Type | Description |
|-------|------|-------------|
| sessions | array | List of academic sessions |
| classes | array | List of classes (filtered by session if session_id provided) |
| sections | array | List of sections (filtered by class if class_id provided) |
| fee_groups | array | List of fee groups |
| fee_types | array | List of fee types (always returns all fee types) |
| collect_by | array | List of staff members who can collect fees |
| group_by_options | array | Available grouping options for reports |

### Session Object

| Field | Type | Description |
|-------|------|-------------|
| id | integer | Session ID |
| name | string | Session name (e.g., "2024-2025") |

### Class Object

| Field | Type | Description |
|-------|------|-------------|
| id | integer | Class ID |
| name | string | Class name (e.g., "Class 1") |

### Section Object

| Field | Type | Description |
|-------|------|-------------|
| id | integer | Section ID |
| name | string | Section name (e.g., "Section A") |

### Fee Group Object

| Field | Type | Description |
|-------|------|-------------|
| id | integer | Fee group ID |
| name | string | Fee group name |

### Fee Type Object

| Field | Type | Description |
|-------|------|-------------|
| id | integer | Fee type ID |
| name | string | Fee type name |
| code | string | Fee type code |

### Collect By Object (Staff)

| Field | Type | Description |
|-------|------|-------------|
| id | integer | Staff ID |
| name | string | Staff full name |
| employee_id | string | Staff employee ID |

### Group By Options

Array of strings representing available grouping options:
- `"class"` - Group by class
- `"collect"` - Group by collector/staff
- `"mode"` - Group by payment mode

---

## Request Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| session_id | integer | No | Filter classes by specific session |
| class_id | integer | No | Filter sections by specific class |

**Note:** All parameters are optional. When no parameters are provided (empty request body `{}`), the API returns all available options.

---

## Error Responses

### Unauthorized Access (HTTP 401)
```json
{
  "status": 0,
  "message": "Unauthorized access. Invalid headers.",
  "data": null
}
```

### Method Not Allowed (HTTP 405)
```json
{
  "status": 0,
  "message": "Method not allowed. Use POST method.",
  "data": null
}
```

### Internal Server Error (HTTP 500)
```json
{
  "status": 0,
  "message": "Internal server error occurred",
  "data": null
}
```

---

## cURL Examples

### Example 1: Get All Filter Options
```bash
curl -X POST "http://localhost/amt/api/fee-collection-filters/get" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 2: Filter Classes by Session
```bash
curl -X POST "http://localhost/amt/api/fee-collection-filters/get" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "session_id": 21
  }'
```

### Example 3: Filter Sections by Class
```bash
curl -X POST "http://localhost/amt/api/fee-collection-filters/get" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "session_id": 21,
    "class_id": 19
  }'
```

---

## Database Tables Used

### Main Tables:
- `sessions` - Academic session information
- `classes` - Class information
- `sections` - Section information
- `fee_groups` - Fee group definitions
- `feetype` - Fee type definitions
- `staff` - Staff member information

### Relationship Tables:
- `student_session` - Links students to sessions, classes, and sections (used to get classes by session)
- `class_sections` - Links classes to sections (used to get sections by class)

---

## Hierarchical Filtering Logic

### 1. Sessions
- Always returns all sessions
- Ordered by ID in descending order (newest first)

### 2. Classes
- **Without session_id:** Returns all classes from the `classes` table
- **With session_id:** Returns only classes that have students enrolled in that session (from `student_session` table)
- **Validation:** session_id must be a positive integer (> 0), not null, not empty string

### 3. Sections
- **Without class_id:** Returns all sections from the `sections` table
- **With class_id:** Returns only sections that belong to that class (from `class_sections` table)
- **Validation:** class_id must be a positive integer (> 0), not null, not empty string

### 4. Fee Groups
- Always returns all non-system fee groups
- Excludes system fee groups (is_system = 1)

### 5. Fee Types
- Always returns all non-system fee types
- Excludes system fee types (is_system = 1)
- Returns all fee types regardless of session or other filters

### 6. Collect By (Staff)
- Always returns all active staff members
- Includes staff ID, full name, and employee ID

### 7. Group By Options
- Always returns the same three options: "class", "collect", "mode"

---

## Use Cases

1. **Initial Page Load:** Call with empty request body `{}` to populate all filter dropdowns
2. **Session Selection:** Call with `session_id` to get classes for that session
3. **Class Selection:** Call with `session_id` and `class_id` to get sections for that class
4. **Dynamic Filtering:** Use the hierarchical structure to create cascading dropdowns in the UI

---

## Notes

1. All endpoints use POST method as per application standards
2. Authentication headers are mandatory for all requests
3. Empty request body `{}` is valid and returns all available options
4. The API treats empty filters the same as list endpoints (returns all data)
5. Fee types are always returned in full regardless of filters
6. Staff collectors include only active staff members
7. System fee groups and fee types are excluded from results
8. Hierarchical relationships are maintained (session → class → section)

---

## Support

For API support and questions, contact the development team or refer to the main project documentation.

**API Status:** ✅ Fully Implemented

**Last Updated:** October 10, 2025

