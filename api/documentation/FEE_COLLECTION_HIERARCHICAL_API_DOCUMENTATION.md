# Fee Collection Hierarchical API Documentation

## Overview

This document describes the updated Fee Collection Filters API endpoints that provide hierarchical academic data structures. The API now supports two endpoints:

1. **`/fee-collection-filters/get`** - Returns hierarchical structure: Sessions → Classes → Sections (with fee-related filters)
2. **`/fee-collection-filters/get-hierarchy`** - Returns complete hierarchical structure: Sessions → Classes → Sections → Students

Both endpoints follow the school management system API conventions and handle empty request bodies gracefully by returning all available records.

---

## Base URL
```
http://{domain}/api/
```

## Authentication Headers

All API requests require the following headers:
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

---

## Endpoints

### 1. Get Fee Collection Filters (Hierarchical)

**Endpoint:** `POST /fee-collection-filters/get`  
**Full URL:** `http://localhost/amt/api/fee-collection-filters/get`

**Description:** Retrieve hierarchical filter options for fee collection reports. Returns sessions with nested classes and sections, along with fee groups, fee types, staff collectors, and grouping options.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### Request Body (All parameters optional)
```json
{
  "session_id": 21,
  "class_id": 19,
  "section_id": 1
}
```

**Note:** Empty request body `{}` returns all available data.

#### Success Response (HTTP 200)
```json
{
  "status": 1,
  "message": "Filter options retrieved successfully",
  "data": {
    "sessions": [
      {
        "id": 21,
        "name": "2024-2025",
        "classes": [
          {
            "id": 19,
            "name": "Class 1",
            "sections": [
              {
                "id": 1,
                "name": "Section A"
              },
              {
                "id": 2,
                "name": "Section B"
              }
            ]
          },
          {
            "id": 20,
            "name": "Class 2",
            "sections": [
              {
                "id": 3,
                "name": "Section A"
              }
            ]
          }
        ]
      },
      {
        "id": 20,
        "name": "2023-2024",
        "classes": [
          {
            "id": 19,
            "name": "Class 1",
            "sections": [
              {
                "id": 1,
                "name": "Section A"
              }
            ]
          }
        ]
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

### 2. Get Hierarchical Academic Data with Students

**Endpoint:** `POST /fee-collection-filters/get-hierarchy`  
**Full URL:** `http://localhost/amt/api/fee-collection-filters/get-hierarchy`

**Description:** Retrieve complete hierarchical academic data including all enrolled students. Returns sessions with nested classes, sections, and students.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### Request Body (All parameters optional)
```json
{
  "session_id": 21,
  "class_id": 19,
  "section_id": 1
}
```

**Note:** Empty request body `{}` returns all available data.

#### Success Response (HTTP 200)
```json
{
  "status": 1,
  "message": "Hierarchical academic data retrieved successfully",
  "filters_applied": {
    "session_id": 21,
    "class_id": 19,
    "section_id": 1
  },
  "statistics": {
    "total_sessions": 1,
    "total_classes": 1,
    "total_sections": 1,
    "total_students": 25
  },
  "data": [
    {
      "id": 21,
      "name": "2024-2025",
      "classes": [
        {
          "id": 19,
          "name": "Class 1",
          "sections": [
            {
              "id": 1,
              "name": "Section A",
              "students": [
                {
                  "id": 101,
                  "admission_no": "STU001",
                  "roll_no": "1",
                  "full_name": "John Michael Doe",
                  "firstname": "John",
                  "middlename": "Michael",
                  "lastname": "Doe",
                  "father_name": "Robert Doe",
                  "dob": "2015-05-15",
                  "gender": "Male",
                  "mobileno": "1234567890",
                  "email": "john.doe@example.com",
                  "guardian_name": "Robert Doe",
                  "guardian_phone": "1234567890",
                  "is_active": "yes"
                },
                {
                  "id": 102,
                  "admission_no": "STU002",
                  "roll_no": "2",
                  "full_name": "Jane Mary Smith",
                  "firstname": "Jane",
                  "middlename": "Mary",
                  "lastname": "Smith",
                  "father_name": "William Smith",
                  "dob": "2015-06-20",
                  "gender": "Female",
                  "mobileno": "0987654321",
                  "email": "jane.smith@example.com",
                  "guardian_name": "William Smith",
                  "guardian_phone": "0987654321",
                  "is_active": "yes"
                }
              ]
            }
          ]
        }
      ]
    }
  ],
  "timestamp": "2025-10-10 14:30:00"
}
```

---

## Request Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| session_id | integer | No | Filter by specific session |
| class_id | integer | No | Filter by specific class |
| section_id | integer | No | Filter by specific section |

**Note:** All parameters are optional. When no parameters are provided (empty request body `{}`), the API returns all available data.

---

## Response Structure

### Endpoint 1: `/fee-collection-filters/get`

#### Sessions Array (Hierarchical)
Each session object contains:
- `id` (integer) - Session ID
- `name` (string) - Session name
- `classes` (array) - Array of class objects

#### Class Object (Nested in Session)
- `id` (integer) - Class ID
- `name` (string) - Class name
- `sections` (array) - Array of section objects

#### Section Object (Nested in Class)
- `id` (integer) - Section ID
- `name` (string) - Section name

### Endpoint 2: `/fee-collection-filters/get-hierarchy`

Same structure as Endpoint 1, but sections also include:

#### Student Object (Nested in Section)
- `id` (integer) - Student ID
- `admission_no` (string) - Student admission number
- `roll_no` (string) - Student roll number
- `full_name` (string) - Complete student name
- `firstname` (string) - First name
- `middlename` (string) - Middle name
- `lastname` (string) - Last name
- `father_name` (string) - Father's name
- `dob` (date) - Date of birth
- `gender` (string) - Gender
- `mobileno` (string) - Mobile number
- `email` (string) - Email address
- `guardian_name` (string) - Guardian name
- `guardian_phone` (string) - Guardian phone
- `is_active` (string) - Active status ("yes"/"no")

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

### Example 1: Get All Hierarchical Filters (Empty Request)
```bash
curl -X POST "http://localhost/amt/api/fee-collection-filters/get" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 2: Filter by Session
```bash
curl -X POST "http://localhost/amt/api/fee-collection-filters/get" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "session_id": 21
  }'
```

### Example 3: Filter by Session and Class
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

### Example 4: Get All Hierarchical Data with Students
```bash
curl -X POST "http://localhost/amt/api/fee-collection-filters/get-hierarchy" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 5: Get Students for Specific Session
```bash
curl -X POST "http://localhost/amt/api/fee-collection-filters/get-hierarchy" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "session_id": 21
  }'
```

### Example 6: Get Students for Specific Section
```bash
curl -X POST "http://localhost/amt/api/fee-collection-filters/get-hierarchy" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "session_id": 21,
    "class_id": 19,
    "section_id": 1
  }'
```

---

## Database Tables Used

### Main Tables:
- `sessions` - Academic session information
- `classes` - Class information
- `sections` - Section information
- `students` - Student information
- `fee_groups` - Fee group definitions
- `feetype` - Fee type definitions
- `staff` - Staff member information

### Relationship Tables:
- `student_session` - Links students to sessions, classes, and sections
- `class_sections` - Links classes to sections

---

## Hierarchical Structure Logic

### Sessions
- Always returns all sessions (unless filtered by session_id)
- Ordered by ID in descending order (newest first)

### Classes (Nested in Sessions)
- Returns only classes that have students enrolled in the parent session
- Filtered from `student_session` table
- Ordered by ID in ascending order

### Sections (Nested in Classes)
- Returns only sections that have students enrolled in the parent session and class
- Filtered from `student_session` table
- Ordered by ID in ascending order

### Students (Nested in Sections - only in get-hierarchy endpoint)
- Returns only active students (`is_active = 'yes'`)
- Includes complete student information
- Ordered by admission number in ascending order

---

## Key Differences Between Endpoints

| Feature | `/get` | `/get-hierarchy` |
|---------|--------|------------------|
| Structure | Sessions → Classes → Sections | Sessions → Classes → Sections → Students |
| Student Data | Not included | Included with full details |
| Fee Groups | Included | Not included |
| Fee Types | Included | Not included |
| Staff Collectors | Included | Not included |
| Group By Options | Included | Not included |
| Statistics | Not included | Included (counts) |
| Use Case | Fee collection filters | Complete academic hierarchy |

---

## Use Cases

### Endpoint 1: `/fee-collection-filters/get`
1. **Fee Collection Reports:** Get hierarchical filters for fee collection reports
2. **Dynamic Dropdowns:** Populate cascading dropdowns (Session → Class → Section)
3. **Fee Configuration:** Configure fee-related settings with proper hierarchy
4. **Report Filters:** Provide filter options for various fee reports

### Endpoint 2: `/fee-collection-filters/get-hierarchy`
1. **Student Management:** View complete academic structure with enrolled students
2. **Bulk Operations:** Perform operations on students by session/class/section
3. **Academic Reports:** Generate reports showing complete hierarchy
4. **Data Export:** Export complete academic data with student details
5. **Dashboard Views:** Display hierarchical academic data with student counts

---

## Filtering Behavior

### Empty Request Body `{}`
- Returns all sessions with their complete hierarchy
- No filtering applied
- Useful for initial page load or getting complete data

### With `session_id`
- Returns only the specified session
- Classes and sections filtered to that session
- Students (if included) filtered to that session

### With `session_id` and `class_id`
- Returns only the specified session and class
- Sections filtered to that class within the session
- Students (if included) filtered to that class and session

### With `session_id`, `class_id`, and `section_id`
- Returns only the specified session, class, and section
- Most specific filtering
- Students (if included) filtered to that exact section

---

## Performance Considerations

1. **Without Students:** The `/get` endpoint is faster as it doesn't fetch student data
2. **With Students:** The `/get-hierarchy` endpoint may take longer with large datasets
3. **Filtering:** Using filters reduces response size and improves performance
4. **Caching:** Consider caching responses for frequently accessed data
5. **Pagination:** For very large datasets, consider implementing pagination

---

## Migration Notes

### Breaking Changes
The `/fee-collection-filters/get` endpoint now returns a **hierarchical structure** instead of flat arrays.

**Old Response Structure (Flat):**
```json
{
  "sessions": [{"id": 21, "name": "2024-2025"}],
  "classes": [{"id": 19, "name": "Class 1"}],
  "sections": [{"id": 1, "name": "Section A"}]
}
```

**New Response Structure (Hierarchical):**
```json
{
  "sessions": [
    {
      "id": 21,
      "name": "2024-2025",
      "classes": [
        {
          "id": 19,
          "name": "Class 1",
          "sections": [
            {"id": 1, "name": "Section A"}
          ]
        }
      ]
    }
  ]
}
```

### Migration Steps
1. Update client code to handle nested structure
2. Traverse sessions → classes → sections hierarchy
3. Update UI components to work with nested data
4. Test all dependent features thoroughly

---

## Notes

1. All endpoints use POST method as per application standards
2. Authentication headers are mandatory for all requests
3. Empty request body `{}` is valid and returns all available data
4. The API treats empty filters the same as list endpoints (returns all data)
5. Only active students (`is_active = 'yes'`) are included in results
6. System fee groups and fee types are excluded from results
7. Hierarchical relationships are maintained (session → class → section → student)
8. All filtering is done at the database level for optimal performance

---

## Support

For API support and questions, contact the development team or refer to the main project documentation.

**API Status:** ✅ Fully Implemented

**Last Updated:** October 10, 2025

**Version:** 2.0 (Hierarchical Structure)

