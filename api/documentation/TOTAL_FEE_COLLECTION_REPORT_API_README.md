# Total Fee Collection Report API Documentation

## Overview

The Total Fee Collection Report API provides endpoints to retrieve total fee collection data with fee type breakdown. Similar to Combined Collection Report but with additional fee type totals in the summary.

**Status:** ✅ Fully Working

**Base URL:** `http://localhost/amt/api`

## Key Features

- ✅ **Combined Data** - Merges regular fees + other fees + transport fees
- ✅ **Fee Type Breakdown** - Summary includes totals by fee type
- ✅ **Graceful Null Handling** - Empty requests return all records
- ✅ **Session Support** - Filter by specific session or use current session
- ✅ **Flexible Filtering** - Filter by date range, class, section, fee type, received by
- ✅ **Grouping Support** - Group results by class, collection, or payment mode

## Authentication

```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

## Endpoints

### 1. List Endpoint

**URL:** `POST /api/total-fee-collection-report/list`

**Description:** Get filter options including all fee types (regular + other + transport).

**Request Body:** `{}`

### 2. Filter Endpoint

**URL:** `POST /api/total-fee-collection-report/filter`

**Description:** Get total fee collection report with fee type breakdown.

**Parameters:** Same as Combined Collection Report API

**Request Examples:**

**Example 1: Empty Request**
```json
{}
```

**Example 2: This Year with Class Filter**
```json
{
    "search_type": "this_year",
    "class_id": 1
}
```

**Response Example:**
```json
{
    "status": 1,
    "message": "Total fee collection report retrieved successfully",
    "filters_applied": {
        "search_type": "this_year",
        "date_from": "2025-01-01",
        "date_to": "2025-12-31",
        "class_id": 1,
        "section_id": null,
        "session_id": 18,
        "feetype_id": null,
        "received_by": null,
        "group": null
    },
    "summary": {
        "total_records": 350,
        "total_amount": "450000.00",
        "regular_fees_count": 200,
        "other_fees_count": 150,
        "fee_type_breakdown": [
            {
                "fee_type": "Tuition Fees",
                "count": 150,
                "total": 300000.00
            },
            {
                "fee_type": "Hostel Fees",
                "count": 100,
                "total": 100000.00
            },
            {
                "fee_type": "Library Fees",
                "count": 50,
                "total": 25000.00
            },
            {
                "fee_type": "Transport Fees",
                "count": 50,
                "total": 25000.00
            }
        ]
    },
    "total_records": 350,
    "data": [...],
    "timestamp": "2025-10-09 12:34:56"
}
```

**Response with Grouping:**
```json
{
    "status": 1,
    "message": "Total fee collection report retrieved successfully",
    "filters_applied": {
        "search_type": "this_month",
        "group": "class",
        ...
    },
    "summary": {
        "total_records": 350,
        "total_amount": "450000.00",
        "regular_fees_count": 200,
        "other_fees_count": 150,
        "fee_type_breakdown": [...]
    },
    "total_records": 350,
    "data": [
        {
            "group_name": "1",
            "records": [...],
            "subtotal": 150000.00
        },
        {
            "group_name": "2",
            "records": [...],
            "subtotal": 300000.00
        }
    ],
    "timestamp": "2025-10-09 12:34:56"
}
```

## API Behavior Matrix

| Request | Returns |
|---------|---------|
| `{}` | ALL fee collection with fee type breakdown |
| `{"search_type": "this_month"}` | This month's collection with breakdown |
| `{"class_id": 1}` | All fees for class 1 with breakdown |
| `{"group": "class"}` | Records grouped by class with breakdown |

## Postman Testing

### Test 1: Get Total Collection with Breakdown
```
POST http://localhost/amt/api/total-fee-collection-report/filter
Headers:
  Content-Type: application/json
  Client-Service: smartschool
  Auth-Key: schoolAdmin@
Body: {}
```

### Test 2: This Month with Fee Type Breakdown
```
POST http://localhost/amt/api/total-fee-collection-report/filter
Headers:
  Content-Type: application/json
  Client-Service: smartschool
  Auth-Key: schoolAdmin@
Body:
{
    "search_type": "this_month"
}
```

### Test 3: Filter by Class with Grouping
```
POST http://localhost/amt/api/total-fee-collection-report/filter
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

## Technical Details

- **Controller:** `api/application/controllers/Total_fee_collection_report_api.php`
- **Routes:** Defined in `api/application/config/routes.php`
- **Database Tables:** Same as Combined Collection Report
- **Query Method:** Two separate queries merged, with additional fee type aggregation
- **Date Field:** Uses `created_at` timestamp

## Key Differences from Combined Collection Report

1. **Fee Type Breakdown:** Summary includes detailed breakdown by fee type
2. **Aggregation:** Calculates count and total for each fee type
3. **Use Case:** Better for understanding distribution across fee types

## Notes

- All parameters are optional
- Empty request returns all records with fee type breakdown
- Fee type breakdown shows count and total for each fee type
- Useful for financial analysis and reporting
- Breakdown is calculated from actual data, not from fee type master

---

**Last Updated:** 2025-10-09  
**Version:** 1.0  
**Status:** ✅ Fully Working

