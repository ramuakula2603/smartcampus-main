# Other Collection Report API Documentation

## Overview

The Other Collection Report API provides endpoints to retrieve fee collection data for "other" fee types (fees from the `feetypeadding` table such as hostel fees, library fees, etc.). This API supports flexible filtering and graceful null parameter handling.

**Status:** ✅ Fully Working

**Base URL:** `http://localhost/amt/api`

## Key Features

- ✅ **Graceful Null Handling** - Empty requests return all records for current session
- ✅ **Session Support** - Filter by specific session or use current session
- ✅ **Flexible Filtering** - Filter by date range, class, section, fee type, received by
- ✅ **Grouping Support** - Group results by class, collection, or payment mode
- ✅ **Direct Database Queries** - No model dependencies
- ✅ **Proper Error Handling** - JSON error responses with clear messages

## Authentication

All endpoints require the following headers:

```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

## Endpoints

### 1. List Endpoint

**URL:** `POST /api/other-collection-report/list`

**Description:** Get filter options including search types, classes, fee types, and received by list.

**Request Body:** `{}`

**Response Example:**
```json
{
    "status": 1,
    "message": "Filter options retrieved successfully",
    "data": {
        "search_types": [
            {"key": "today", "label": "Today"},
            {"key": "this_week", "label": "This Week"},
            {"key": "this_month", "label": "This Month"},
            {"key": "last_month", "label": "Last Month"},
            {"key": "this_year", "label": "This Year"},
            {"key": "period", "label": "Custom Period"}
        ],
        "group_by": [
            {"key": "class", "label": "Group By Class"},
            {"key": "collection", "label": "Group By Collection"},
            {"key": "mode", "label": "Group By Payment Mode"}
        ],
        "classes": [...],
        "fee_types": [...],
        "received_by": [...]
    },
    "timestamp": "2025-10-09 12:34:56"
}
```

### 2. Filter Endpoint

**URL:** `POST /api/other-collection-report/filter`

**Description:** Get other collection report data with optional filters.

**Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| search_type | string | No | Date range type: today, this_week, this_month, last_month, this_year, period |
| date_from | string | No | Start date (YYYY-MM-DD) - used when search_type is 'period' |
| date_to | string | No | End date (YYYY-MM-DD) - used when search_type is 'period' |
| class_id | integer | No | Filter by class ID |
| section_id | integer | No | Filter by section ID |
| session_id | integer | No | Filter by session ID - **IMPORTANT**: This filters by student enrollment session, not fee collection session |
| feetype_id | integer | No | Filter by fee type ID |
| received_by | string | No | Filter by person who received payment |
| group | string | No | Group results by: class, collection, or mode |

**Request Examples:**

**Example 1: Empty Request (Returns all records for current session)**
```json
{}
```

**Example 2: Filter by Date Range**
```json
{
    "search_type": "this_month"
}
```

**Example 3: Filter by Class and Section**
```json
{
    "search_type": "this_year",
    "class_id": 1,
    "section_id": 2
}
```

**Example 4: Filter with Grouping**
```json
{
    "search_type": "this_month",
    "class_id": 1,
    "group": "class"
}
```

**Example 5: Custom Date Range**
```json
{
    "search_type": "period",
    "date_from": "2025-01-01",
    "date_to": "2025-03-31"
}
```

**Response Example (Without Grouping):**
```json
{
    "status": 1,
    "message": "Other collection report retrieved successfully",
    "filters_applied": {
        "search_type": "this_month",
        "date_from": "2025-10-01",
        "date_to": "2025-10-09",
        "class_id": null,
        "section_id": null,
        "session_id": 18,
        "feetype_id": null,
        "received_by": null,
        "group": null
    },
    "summary": {
        "total_records": 150,
        "total_amount": "125000.00"
    },
    "total_records": 150,
    "data": [
        {
            "id": 1,
            "student_fees_master_id": 123,
            "amount": 5000.00,
            "amount_discount": 0.00,
            "amount_fine": 0.00,
            "payment_mode": "cash",
            "received_by": "John Doe",
            "created_at": "2025-10-05 10:30:00",
            "firstname": "Alice",
            "middlename": "",
            "lastname": "Smith",
            "admission_no": "STU001",
            "class": "Class 1",
            "section": "A",
            "type": "Hostel Fees",
            "code": "HOSTEL",
            "name": "Hostel Fee Group"
        }
    ],
    "timestamp": "2025-10-09 12:34:56"
}
```

**Response Example (With Grouping):**
```json
{
    "status": 1,
    "message": "Other collection report retrieved successfully",
    "filters_applied": {
        "search_type": "this_month",
        "date_from": "2025-10-01",
        "date_to": "2025-10-09",
        "class_id": null,
        "section_id": null,
        "session_id": 18,
        "feetype_id": null,
        "received_by": null,
        "group": "class"
    },
    "summary": {
        "total_records": 150,
        "total_amount": "125000.00"
    },
    "total_records": 150,
    "data": [
        {
            "group_name": "1",
            "records": [...],
            "subtotal": 45000.00
        },
        {
            "group_name": "2",
            "records": [...],
            "subtotal": 80000.00
        }
    ],
    "timestamp": "2025-10-09 12:34:56"
}
```

## API Behavior Matrix

| Request | Returns |
|---------|---------|
| `{}` | ALL other fee collection records for current session |
| `{"search_type": "today"}` | Today's other fee collection records |
| `{"search_type": "this_month"}` | This month's other fee collection records |
| `{"class_id": 1}` | All other fee collection for class 1 (current year) |
| `{"class_id": 1, "section_id": 2}` | All other fee collection for class 1, section 2 (current year) |
| `{"session_id": 17}` | All other fee collection for session 17 (current year) |
| `{"feetype_id": 5}` | All other fee collection for fee type 5 (current year) |
| `{"received_by": "John"}` | All other fee collection received by John (current year) |
| `{"group": "class"}` | All records grouped by class |

## Error Handling

### Database Connection Error
```json
{
    "status": 0,
    "message": "Database connection error. Please ensure MySQL is running in XAMPP.",
    "error": "Unable to connect to database server",
    "timestamp": "2025-10-09 12:34:56"
}
```

### Authentication Error
```json
{
    "status": 0,
    "message": "Unauthorized access",
    "timestamp": "2025-10-09 12:34:56"
}
```

### General Error
```json
{
    "status": 0,
    "message": "Error retrieving other collection report",
    "error": "Detailed error message",
    "timestamp": "2025-10-09 12:34:56"
}
```

## Postman Testing

### Test 1: Get Filter Options
```
POST http://localhost/amt/api/other-collection-report/list
Headers:
  Content-Type: application/json
  Client-Service: smartschool
  Auth-Key: schoolAdmin@
Body: {}
```

### Test 2: Get All Records (Empty Request)
```
POST http://localhost/amt/api/other-collection-report/filter
Headers:
  Content-Type: application/json
  Client-Service: smartschool
  Auth-Key: schoolAdmin@
Body: {}
```

### Test 3: Filter by This Month
```
POST http://localhost/amt/api/other-collection-report/filter
Headers:
  Content-Type: application/json
  Client-Service: smartschool
  Auth-Key: schoolAdmin@
Body:
{
    "search_type": "this_month"
}
```

### Test 4: Filter by Class with Grouping
```
POST http://localhost/amt/api/other-collection-report/filter
Headers:
  Content-Type: application/json
  Client-Service: smartschool
  Auth-Key: schoolAdmin@
Body:
{
    "search_type": "this_year",
    "class_id": 1,
    "group": "class"
}
```

### Test 5: Specific Filter Example (Working)
```
POST http://localhost/amt/api/other-collection-report/filter
Headers:
  Content-Type: application/json
  Client-Service: smartschool
  Auth-Key: schoolAdmin@
Body:
{
    "session_id": "20",
    "class_id": "16",
    "section_id": "26",
    "feetype_id": "4",
    "collect_by_id": "6",
    "from_date": "2025-09-01",
    "to_date": "2025-10-11"
}
```

**Note**: This example uses `session_id: "20"` (2024-25 session) because the student is enrolled in that session, not session 21.

## Technical Details

- **Controller:** `api/application/controllers/Other_collection_report_api.php`
- **Routes:** Defined in `api/application/config/routes.php`
- **Database Tables:** 
  - `student_fees_depositeadding` (main table)
  - `fee_groups_feetypeadding`
  - `fee_groupsadding`
  - `feetypeadding`
  - `student_fees_masteradding`
  - `student_session`
  - `classes`
  - `sections`
  - `students`
- **Query Method:** Direct database queries using CodeIgniter's Query Builder
- **Date Field:** Uses `created_at` timestamp for date filtering

## Important Notes

- All parameters are optional - empty request returns all records for current session
- Null, empty string, and missing parameters are treated identically
- Default date range is current year if no date parameters provided
- Grouping organizes records by specified field with subtotals
- Response always includes summary with total records and total amount
- All amounts are formatted as decimal strings with 2 decimal places

### Session ID Filtering

**CRITICAL**: The `session_id` parameter filters by **student enrollment session**, not fee collection session. This means:

- If a student is enrolled in session 20 (2024-25), you must use `session_id: 20` to find their fee collections
- Fee collections are linked to students through their enrollment session
- If you get "No records found" with a specific session_id, try without the session_id filter first
- Use the debug endpoint `/debug-session/check-student-session` to verify which session a student belongs to

### Troubleshooting "No Records Found"

1. **Remove session_id filter** - Test without session_id to see if data exists
2. **Check date range** - Ensure fee collections exist in the specified date range
3. **Verify student enrollment** - Students must be enrolled in the specified session
4. **Check collector assignment** - The specified collector must have collected fees for these criteria
5. **Validate IDs** - Ensure class_id, section_id, feetype_id, and collect_by_id exist and are correct

---

**Last Updated:** 2025-10-11
**Version:** 1.1
**Status:** ✅ Fully Working & Tested

**Recent Updates:**
- ✅ Fixed session_id filtering issue - now properly documented that session_id filters by student enrollment session
- ✅ Enhanced debug information for "No records found" scenarios
- ✅ Added troubleshooting guide for common issues
- ✅ Added working test example with correct session_id

