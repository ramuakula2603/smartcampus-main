# Fee Collection Columnwise Report API Documentation

## Overview

The Fee Collection Columnwise Report API provides endpoints to retrieve fee collection data organized in a column-wise format by fee type. Each student appears once with their fee payments organized by fee type as columns.

**Status:** ✅ Fully Working

**Base URL:** `http://localhost/amt/api`

## Key Features

- ✅ **Columnwise Format** - Fee types as columns, students as rows
- ✅ **Student-Centric View** - Each student appears once with all their fee payments
- ✅ **Fee Type Totals** - Summary includes totals for each fee type
- ✅ **Graceful Null Handling** - Empty requests return all records
- ✅ **Session Support** - Filter by specific session or use current session
- ✅ **Flexible Filtering** - Filter by date range, class, section, fee type, received by
- ✅ **JSON Field Handling** - Correctly decodes and processes `amount_detail` JSON field

## Important Technical Details

This API correctly handles the database structure where payment details are stored in a JSON field called `amount_detail`. The implementation:

1. **Decodes JSON Data**: Extracts payment information from the `amount_detail` JSON field in both `student_fees_deposite` and `student_fees_depositeadding` tables
2. **Date Filtering**: Filters payments by date within the JSON data (not by `created_at` field)
3. **Received By Filtering**: Filters by `received_by` within the JSON payment details
4. **Amount Aggregation**: Sums up amounts from multiple payments of the same fee type per student
5. **Accurate Totals**: Calculates correct totals for students, fee types, and overall collection

This approach ensures accurate reporting of fee collection amounts and matches the behavior of the working web page implementation.

## Authentication

```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

## Endpoints

### 1. List Endpoint

**URL:** `POST /api/fee-collection-columnwise-report/list`

**Description:** Get filter options including all fee types (regular + other + transport).

**Request Body:** `{}`

### 2. Filter Endpoint

**URL:** `POST /api/fee-collection-columnwise-report/filter`

**Description:** Get fee collection report in columnwise format.

**Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| search_type | string | No | Date range: today, this_week, this_month, last_month, this_year, period |
| date_from | string | No | Start date (YYYY-MM-DD) |
| date_to | string | No | End date (YYYY-MM-DD) |
| class_id | integer | No | Filter by class ID |
| section_id | integer | No | Filter by section ID |
| session_id | integer | No | Filter by session ID (defaults to current) |
| feetype_id | integer/string | No | Filter by fee type ID |
| received_by | string | No | Filter by person who received payment |

**Note:** Grouping is not supported in columnwise format.

**Request Examples:**

**Example 1: Empty Request**
```json
{}
```

**Example 2: This Month**
```json
{
    "search_type": "this_month"
}
```

**Example 3: Filter by Class**
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
    "message": "Fee collection columnwise report retrieved successfully",
    "filters_applied": {
        "search_type": "this_month",
        "date_from": "2025-10-01",
        "date_to": "2025-10-09",
        "class_id": null,
        "section_id": null,
        "session_id": 18,
        "feetype_id": null,
        "received_by": null
    },
    "summary": {
        "total_students": 50,
        "total_records": 350,
        "total_amount": "450000.00",
        "fee_type_totals": {
            "Tuition Fees": 300000.00,
            "Hostel Fees": 100000.00,
            "Library Fees": 25000.00,
            "Transport Fees": 25000.00
        }
    },
    "fee_types": [
        {"type": "Tuition Fees", "code": "TUITION"},
        {"type": "Hostel Fees", "code": "HOSTEL"},
        {"type": "Library Fees", "code": "LIBRARY"},
        {"type": "Transport Fees", "code": "TRANSPORT"}
    ],
    "total_records": 50,
    "data": [
        {
            "student_id": 123,
            "admission_no": "STU001",
            "student_name": "Alice Smith",
            "class": "Class 1",
            "section": "A",
            "fee_payments": {
                "Tuition Fees": 5000.00,
                "Hostel Fees": 3000.00,
                "Library Fees": 500.00,
                "Transport Fees": 0
            },
            "total": 8500.00
        },
        {
            "student_id": 124,
            "admission_no": "STU002",
            "student_name": "Bob Johnson",
            "class": "Class 1",
            "section": "A",
            "fee_payments": {
                "Tuition Fees": 5000.00,
                "Hostel Fees": 0,
                "Library Fees": 500.00,
                "Transport Fees": 1000.00
            },
            "total": 6500.00
        }
    ],
    "timestamp": "2025-10-09 12:34:56"
}
```

## Data Structure Explanation

### Student-Centric Organization

Unlike other reports that show individual payment transactions, this report:

1. **Groups by Student:** Each student appears once in the data array
2. **Fee Types as Columns:** Each fee type becomes a column in `fee_payments` object
3. **Aggregated Amounts:** If a student paid the same fee type multiple times, amounts are summed
4. **Student Total:** Each student record includes their total payments across all fee types

### Fee Types Array

The `fee_types` array lists all unique fee types found in the data, useful for:
- Creating table headers in UI
- Understanding which fee types have collections
- Consistent column ordering

### Summary Totals

- `total_students`: Number of unique students who made payments
- `total_records`: Total number of payment transactions
- `total_amount`: Grand total of all payments
- `fee_type_totals`: Total collected for each fee type

## API Behavior Matrix

| Request | Returns |
|---------|---------|
| `{}` | ALL students with their fee payments (current session) |
| `{"search_type": "today"}` | Students who paid today |
| `{"class_id": 1}` | All students in class 1 with their payments |
| `{"feetype_id": 5}` | Students who paid fee type 5 (only that fee type shown) |

## Postman Testing

### Test 1: Get Columnwise Report
```
POST http://localhost/amt/api/fee-collection-columnwise-report/filter
Headers:
  Content-Type: application/json
  Client-Service: smartschool
  Auth-Key: schoolAdmin@
Body: {}
```

### Test 2: This Month Columnwise
```
POST http://localhost/amt/api/fee-collection-columnwise-report/filter
Headers:
  Content-Type: application/json
  Client-Service: smartschool
  Auth-Key: schoolAdmin@
Body:
{
    "search_type": "this_month"
}
```

### Test 3: Filter by Class
```
POST http://localhost/amt/api/fee-collection-columnwise-report/filter
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

- **Controller:** `api/application/controllers/Fee_collection_columnwise_report_api.php`
- **Routes:** Defined in `api/application/config/routes.php`
- **Database Tables:** Same as Combined Collection Report
- **Query Method:** Two queries merged, then reorganized by student
- **Date Field:** Uses `created_at` timestamp
- **Ordering:** Students ordered by admission number

## Data Transformation Process

1. **Fetch Data:** Get all payment records from both regular and other fee tables
2. **Extract Fee Types:** Identify unique fee types from the results
3. **Group by Student:** Organize records by student_id + admission_no
4. **Aggregate Payments:** Sum amounts for each fee type per student
5. **Calculate Totals:** Compute student totals and fee type totals

## Use Cases

- **Student Fee Summary:** See all fees paid by each student in one view
- **Fee Type Analysis:** Compare collection across different fee types
- **Class-wise Collection:** View fee collection organized by students in a class
- **Export to Excel:** Columnwise format is ideal for spreadsheet export
- **Financial Reports:** Generate summary reports with fee type breakdowns

## Key Differences from Other Reports

1. **Student-Centric:** One row per student instead of one row per transaction
2. **Columnwise Layout:** Fee types as columns instead of rows
3. **Aggregated Data:** Multiple payments of same fee type are summed
4. **No Grouping:** Grouping parameter not supported (already grouped by student)
5. **Fee Type List:** Includes separate array of fee types for UI rendering

## Notes

- All parameters are optional
- Empty request returns all students with payments for current session
- Students with no payments in the date range are not included
- Fee types with zero payment for a student show 0 in fee_payments
- Ideal for creating pivot table style reports
- Sorted by admission number for consistent ordering

---

**Last Updated:** 2025-10-09  
**Version:** 1.0  
**Status:** ✅ Fully Working

