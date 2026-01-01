# Combined Collection Report API Documentation

## Overview

The Combined Collection Report API provides endpoints to retrieve combined fee collection data from both regular fees and other fees (including transport fees). This API merges data from both `student_fees_deposite` and `student_fees_depositeadding` tables.

**Status:** ✅ Fully Working
**Version:** 2.0 (Fee type filtering removed)
**Base URL:** `http://localhost/amt/api`

## Key Features

- ✅ **Combined Data** - Merges regular fees + other fees + transport fees
- ✅ **Graceful Null Handling** - Empty requests return all records
- ✅ **Session Support** - Filter by specific session or use current session
- ✅ **Flexible Filtering** - Filter by date range, class, section, received by
- ✅ **Grouping Support** - Group results by class, collection, or payment mode
- ✅ **Breakdown Summary** - Shows count of regular vs other fees
- ⚠️ **All Fee Types Returned** - API always returns ALL fee types (fee type filtering removed)

## Authentication

```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

## Endpoints

### 1. List Endpoint

**URL:** `POST /api/combined-collection-report/list`

**Description:** Get filter options including all fee types (regular + other + transport).

**Request Body:** `{}`

**Response:** Returns search types, group options, classes, combined fee types list, and received by list.

### 2. Filter Endpoint

**URL:** `POST /api/combined-collection-report/filter`

**Description:** Get combined collection report data with optional filters.

**Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| search_type | string | No | Date range: today, this_week, this_month, last_month, this_year, period |
| date_from | string | No | Start date (YYYY-MM-DD) |
| date_to | string | No | End date (YYYY-MM-DD) |
| class_id | integer | No | Filter by class ID |
| section_id | integer | No | Filter by section ID |
| session_id | integer | No | Filter by session ID (defaults to current) |
| received_by | string | No | Filter by person who received payment |
| group | string | No | Group by: class, collection, or mode |

**Important Note:** ⚠️ `feetype_id` parameter is **NOT supported**. The API always returns ALL fee types. If you need to filter by fee type, implement client-side filtering on the `type` or `code` field in the response.

**Request Examples:**

**Example 1: Empty Request**
```json
{}
```

**Example 2: This Month with Grouping**
```json
{
    "search_type": "this_month",
    "group": "class"
}
```

**Example 3: Filter by Class and Section**
```json
{
    "search_type": "this_year",
    "class_id": 5,
    "section_id": 3
}
```

**Response Example:**
```json
{
    "status": 1,
    "message": "Combined collection report retrieved successfully",
    "filters_applied": {
        "search_type": "this_month",
        "date_from": "2025-10-01",
        "date_to": "2025-10-09",
        "class_id": null,
        "section_id": null,
        "session_id": 18,
        "received_by": null,
        "group": null
    },
    "summary": {
        "total_records": 350,
        "total_amount": "450000.00",
        "regular_fees_count": 200,
        "other_fees_count": 150
    },
    "total_records": 350,
    "data": [
        {
            "id": 1,
            "amount": 5000.00,
            "payment_mode": "cash",
            "received_by": "John Doe",
            "created_at": "2025-10-05 10:30:00",
            "firstname": "Alice",
            "lastname": "Smith",
            "admission_no": "STU001",
            "class": "Class 1",
            "section": "A",
            "type": "Tuition Fees",
            "fee_source": "regular"
        },
        {
            "id": 2,
            "amount": 3000.00,
            "payment_mode": "online",
            "received_by": "Jane Smith",
            "created_at": "2025-10-06 14:20:00",
            "firstname": "Bob",
            "lastname": "Johnson",
            "admission_no": "STU002",
            "class": "Class 2",
            "section": "B",
            "type": "Hostel Fees",
            "fee_source": "other"
        }
    ],
    "timestamp": "2025-10-09 12:34:56"
}
```

## API Behavior Matrix

| Request | Returns |
|---------|---------|
| `{}` | ALL fee collection (regular + other) for current session with ALL fee types |
| `{"search_type": "today"}` | Today's combined fee collection with ALL fee types |
| `{"class_id": 1}` | All combined fees for class 1 (current year) with ALL fee types |
| `{"session_id": 21}` | All collection for session 21 with ALL fee types |
| `{"group": "class"}` | All records grouped by class with subtotals, ALL fee types |

**Note:** The API always returns ALL fee types. Fee type filtering is not supported.

## Error Handling

Same as Other Collection Report API - returns JSON error responses with status 0.

## Postman Testing

### Test 1: Get All Combined Records
```
POST http://localhost/amt/api/combined-collection-report/filter
Headers:
  Content-Type: application/json
  Client-Service: smartschool
  Auth-Key: schoolAdmin@
Body: {}
```

### Test 2: Filter by This Month with Grouping
```
POST http://localhost/amt/api/combined-collection-report/filter
Headers:
  Content-Type: application/json
  Client-Service: smartschool
  Auth-Key: schoolAdmin@
Body:
{
    "search_type": "this_month",
    "group": "collection"
}
```

### Test 3: Filter by Class and Section
```
POST http://localhost/amt/api/combined-collection-report/filter
Headers:
  Content-Type: application/json
  Client-Service: smartschool
  Auth-Key: schoolAdmin@
Body:
{
    "search_type": "this_year",
    "class_id": 1,
    "section_id": 2
}
```

## Technical Details

- **Controller:** `api/application/controllers/Combined_collection_report_api.php`
- **Routes:** Defined in `api/application/config/routes.php`
- **Database Tables:** 
  - Regular fees: `student_fees_deposite`, `fee_groups_feetype`, `fee_groups`, `feetype`, `student_fees_master`
  - Other fees: `student_fees_depositeadding`, `fee_groups_feetypeadding`, `fee_groupsadding`, `feetypeadding`, `student_fees_masteradding`
  - Common: `student_session`, `classes`, `sections`, `students`
- **Query Method:** Two separate queries merged using `array_merge()`
- **Date Field:** Uses `created_at` timestamp for both tables
- **Fee Source:** Each record includes `fee_source` field ('regular' or 'other')

## Key Differences from Other Collection Report

1. **Data Source:** Combines both regular and other fee tables
2. **Fee Types:** Includes all fee types (regular + other + transport)
3. **Summary:** Includes breakdown of regular_fees_count and other_fees_count
4. **Fee Source Field:** Each record tagged with source ('regular' or 'other')

## Notes

- All parameters are optional
- Empty request returns all combined records for current session
- Default date range is current year
- Transport fees are included in regular fees
- Response includes breakdown of regular vs other fees in summary
- Grouping works across both regular and other fees
- ⚠️ **Fee type filtering is NOT supported** - API always returns ALL fee types
- For fee type filtering, implement client-side filtering on the response data

## Client-Side Fee Type Filtering

If you need to filter by fee type, filter the response data on the client side:

```javascript
// Get all records from API
const response = await fetch('/api/combined-collection-report/filter', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Client-Service': 'smartschool',
    'Auth-Key': 'schoolAdmin@'
  },
  body: JSON.stringify({
    date_from: '2025-09-01',
    date_to: '2025-09-30'
  })
});

const data = await response.json();

// Filter by fee type on client side
const tuitionFees = data.data.filter(record => record.type === 'TUITION FEE');
const multipleFees = data.data.filter(record =>
  ['TUITION FEE', 'EXAM FEE'].includes(record.type)
);
```

---

## Version History

### Version 2.0 (2025-10-11)
- ❌ **Removed**: Fee type filtering (`feetype_id` parameter)
- ✅ **Simplified**: API now always returns ALL fee types
- ✅ **Updated**: Documentation to reflect changes

### Version 1.0 (2025-10-09)
- ✅ Initial release with all filters including fee type filtering

---

**Last Updated:** 2025-10-11
**Version:** 2.0
**Status:** ✅ Fully Working

