# API 1: Fee Collection Filters (Hierarchical) - GET

## 📋 Overview

This API endpoint returns hierarchical filter options for fee collection reports. It provides a nested structure showing Sessions → Classes → Sections, along with fee-related filter options.

---

## 🔗 Endpoint Details

**URL:** `POST /api/fee-collection-filters/get`  
**Full URL:** `http://localhost/amt/api/fee-collection-filters/get`  
**Method:** POST  
**Content-Type:** application/json

---

## 🔐 Authentication Headers

**Required Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

---

## 📥 Request Body

All parameters are **optional**. Empty request body `{}` returns all available data.

```json
{
  "session_id": 21,      // Optional - Filter by specific session
  "class_id": 19,        // Optional - Filter by specific class
  "section_id": 1        // Optional - Filter by specific section
}
```

### Request Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| session_id | integer | No | Filter classes by specific session |
| class_id | integer | No | Filter sections by specific class |
| section_id | integer | No | Filter to specific section |

---

## 📤 Response Structure

### Success Response (HTTP 200)

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

### Response Data Structure

#### Sessions (Hierarchical Array)
Each session contains:
- `id` (integer) - Session ID
- `name` (string) - Session name (e.g., "2024-2025")
- `classes` (array) - Nested array of classes

#### Classes (Nested in Sessions)
Each class contains:
- `id` (integer) - Class ID
- `name` (string) - Class name (e.g., "Class 1")
- `sections` (array) - Nested array of sections

#### Sections (Nested in Classes)
Each section contains:
- `id` (integer) - Section ID
- `name` (string) - Section name (e.g., "Section A")

#### Fee Groups (Flat Array)
- `id` (integer) - Fee group ID
- `name` (string) - Fee group name

#### Fee Types (Flat Array)
- `id` (integer) - Fee type ID
- `name` (string) - Fee type name
- `code` (string) - Fee type code

#### Collect By (Flat Array)
- `id` (integer) - Staff ID
- `name` (string) - Staff full name
- `employee_id` (string) - Employee ID

#### Group By Options (Array of Strings)
- `"class"` - Group by class
- `"collect"` - Group by collector
- `"mode"` - Group by payment mode

---

## 🧪 Testing Examples

### Example 1: Get All Data (No Filters)
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

### Example 4: Filter by Session, Class, and Section
```bash
curl -X POST "http://localhost/amt/api/fee-collection-filters/get" \
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

## ❌ Error Responses

### 401 Unauthorized
**Cause:** Missing or invalid authentication headers

```json
{
  "status": 0,
  "message": "Unauthorized access. Invalid headers.",
  "data": null
}
```

### 405 Method Not Allowed
**Cause:** Using GET instead of POST

```json
{
  "status": 0,
  "message": "Method not allowed. Use POST method.",
  "data": null
}
```

### 500 Internal Server Error
**Cause:** Server-side error

```json
{
  "status": 0,
  "message": "Internal server error occurred",
  "data": null
}
```

---

## 🎯 Use Cases

1. **Fee Collection Reports** - Get hierarchical filters for generating fee reports
2. **Dynamic Dropdowns** - Populate cascading dropdowns (Session → Class → Section)
3. **Fee Configuration** - Configure fee settings with proper academic hierarchy
4. **Report Filters** - Provide filter options for various fee-related reports
5. **Dashboard Filters** - Display hierarchical filters on fee collection dashboards

---

## 📊 Filtering Behavior

| Filter Combination | Result |
|-------------------|--------|
| Empty `{}` | Returns all sessions with complete hierarchy |
| `session_id` only | Returns only that session with its classes and sections |
| `session_id` + `class_id` | Returns that session and class with its sections |
| All three filters | Returns specific session, class, and section |

---

## ⚡ Key Features

✅ **Hierarchical Structure** - Nested data showing parent-child relationships  
✅ **Fee-Related Filters** - Includes fee groups, types, collectors, and grouping options  
✅ **Flexible Filtering** - Filter at session, class, or section level  
✅ **Empty Body Support** - `{}` returns all data gracefully  
✅ **Fast Performance** - No student data, optimized for filter dropdowns  

---

## 📝 Important Notes

1. **POST Method Only** - GET requests will return 405 error
2. **Headers Required** - Both `Client-Service` and `Auth-Key` headers are mandatory
3. **Empty Body Valid** - `{}` is a valid request that returns all data
4. **Hierarchical Structure** - Sessions contain classes, classes contain sections
5. **No Student Data** - This endpoint does NOT include student information
6. **System Records Excluded** - System fee groups and types are not included
7. **Active Staff Only** - Only active staff members are included in `collect_by`

---

## 🔄 Breaking Change Notice

**Previous Version:** Returned flat arrays for sessions, classes, and sections

**Current Version:** Returns hierarchical nested structure

**Migration Required:** Update client code to traverse nested structure

---

## 📚 Related Documentation

- **API 2:** See `API_2_FEE_COLLECTION_HIERARCHY_WITH_STUDENTS.md` for endpoint with student data
- **Complete Guide:** See `FEE_COLLECTION_HIERARCHICAL_API_DOCUMENTATION.md`
- **Quick Reference:** See `FEE_COLLECTION_HIERARCHICAL_API_QUICK_REFERENCE.md`

---

**API Status:** ✅ Active  
**Last Updated:** October 10, 2025  
**Version:** 2.0 (Hierarchical Structure)

