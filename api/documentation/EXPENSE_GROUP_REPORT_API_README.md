# Expense Group Report API Documentation

## Overview
The Expense Group Report API provides endpoints to retrieve expense data grouped by expense heads for specified date ranges. This API is part of the Finance Reports module and follows the established patterns for graceful null/empty parameter handling.

## Base Information
- **Base URL**: `http://localhost/amt/api`
- **API Version**: 1.0.0
- **Authentication**: Required (Client-Service and Auth-Key headers)
- **Response Format**: JSON
- **HTTP Method**: POST (for all endpoints)

## Authentication

All API requests require the following headers:

```
Client-Service: smartschool
Auth-Key: schoolAdmin@
Content-Type: application/json
```

### Authentication Example
```bash
curl -X POST http://localhost/amt/api/expense-group-report/filter \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

## Endpoints

### 1. Filter Endpoint
Retrieves expense group report data with optional filters.

**URL**: `/api/expense-group-report/filter`  
**Method**: `POST`  
**Authentication**: Required

#### Request Parameters

All parameters are optional. Empty request `{}` returns all expenses for the current year.

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `search_type` | string | No | Predefined date range (today, this_week, last_week, this_month, last_month, last_3_month, last_6_month, last_12_month, this_year, last_year) |
| `date_from` | string | No | Start date (Y-m-d format, e.g., "2025-01-01") |
| `date_to` | string | No | End date (Y-m-d format, e.g., "2025-12-31") |
| `head_id` | integer | No | Expense head ID to filter by specific expense category |

#### Graceful Null/Empty Handling

This API follows the established pattern for Finance Report APIs:

- **Empty Request** `{}`: Returns all expenses for current year (no validation error)
- **Null Parameters**: Treated same as empty request
- **Empty Strings**: Treated same as null
- **Partial Filters**: Only provided filters are applied

#### Request Examples

**Example 1: Empty Request (All Expenses for Current Year)**
```json
{}
```

**Example 2: Filter by Search Type**
```json
{
    "search_type": "this_month"
}
```

**Example 3: Filter by Custom Date Range**
```json
{
    "date_from": "2025-01-01",
    "date_to": "2025-12-31"
}
```

**Example 4: Filter by Expense Head**
```json
{
    "search_type": "this_year",
    "head_id": 1
}
```

**Example 5: Multiple Filters**
```json
{
    "date_from": "2025-01-01",
    "date_to": "2025-06-30",
    "head_id": 2
}
```

#### Response Format

**Success Response (HTTP 200)**
```json
{
    "status": 1,
    "message": "Expense group report retrieved successfully",
    "filters_applied": {
        "search_type": null,
        "date_from": "2025-01-01",
        "date_to": "2025-12-31",
        "head_id": null
    },
    "date_range": {
        "start_date": "2025-01-01",
        "end_date": "2025-12-31",
        "label": "Jan 1, 2025 to Dec 31, 2025"
    },
    "summary": {
        "total_expenses": 15,
        "total_amount": "125,500.00",
        "by_head": [
            {
                "head_id": "1",
                "exp_category": "Stationery Purchase",
                "expense_count": 5,
                "total_amount": 25000
            },
            {
                "head_id": "2",
                "exp_category": "Electricity Bill",
                "expense_count": 10,
                "total_amount": 100500
            }
        ]
    },
    "total_records": 15,
    "data": [
        {
            "id": "1",
            "date": "2025-01-15",
            "name": "Office Supplies",
            "invoice_no": "INV-001",
            "amount": "5000.00",
            "exp_category": "Stationery Purchase",
            "exp_head_id": "1",
            "total_amount": "5000.00",
            "note": "Monthly office supplies",
            "documents": null
        },
        {
            "id": "2",
            "date": "2025-01-20",
            "name": "Monthly Electricity",
            "invoice_no": "ELEC-001",
            "amount": "10000.00",
            "exp_category": "Electricity Bill",
            "exp_head_id": "2",
            "total_amount": "10000.00",
            "note": "January electricity bill",
            "documents": null
        }
    ],
    "timestamp": "2025-10-08 21:30:00"
}
```

**Error Response (HTTP 401 - Unauthorized)**
```json
{
    "status": 0,
    "message": "Unauthorized access"
}
```

**Error Response (HTTP 500 - Server Error)**
```json
{
    "status": 0,
    "message": "Error retrieving expense group report: [error details]"
}
```

#### Response Fields Description

| Field | Type | Description |
|-------|------|-------------|
| `status` | integer | 1 for success, 0 for error |
| `message` | string | Success or error message |
| `filters_applied` | object | Shows which filters were applied |
| `date_range` | object | Date range information with formatted label |
| `summary` | object | Summary statistics |
| `summary.total_expenses` | integer | Total number of expense records |
| `summary.total_amount` | string | Total amount (formatted with commas) |
| `summary.by_head` | array | Expenses grouped by expense head |
| `total_records` | integer | Number of expense records returned |
| `data` | array | Array of expense records |
| `data[].id` | string | Expense ID |
| `data[].date` | string | Expense date (Y-m-d format) |
| `data[].name` | string | Expense name/description |
| `data[].invoice_no` | string | Invoice number |
| `data[].amount` | string | Expense amount |
| `data[].exp_category` | string | Expense category name |
| `data[].exp_head_id` | string | Expense head ID |
| `data[].note` | string | Additional notes |
| `data[].documents` | string | Attached documents (if any) |
| `timestamp` | string | Response generation timestamp |

### 2. List Endpoint
Retrieves filter options for the expense group report.

**URL**: `/api/expense-group-report/list`  
**Method**: `POST`  
**Authentication**: Required

#### Request Parameters
None required. Send empty JSON object `{}`.

#### Request Example
```json
{}
```

#### Response Format

**Success Response (HTTP 200)**
```json
{
    "status": 1,
    "message": "Filter options retrieved successfully",
    "data": {
        "expense_heads": [
            {
                "id": "1",
                "exp_category": "Stationery Purchase",
                "description": "",
                "is_active": "yes",
                "is_deleted": "no",
                "created_at": "2023-08-24 07:10:42",
                "updated_at": null
            },
            {
                "id": "2",
                "exp_category": "Electricity Bill",
                "description": "",
                "is_active": "yes",
                "is_deleted": "no",
                "created_at": "2023-08-24 07:10:48",
                "updated_at": null
            }
        ],
        "search_types": {
            "": false,
            "today": false,
            "this_week": false,
            "last_week": false,
            "this_month": false,
            "last_month": false,
            "last_3_month": false,
            "last_6_month": false,
            "last_12_month": false,
            "this_year": false,
            "last_year": false,
            "period": false
        },
        "date_types": {
            "": false,
            "exam_from_date": false,
            "exam_to_date": false
        }
    },
    "timestamp": "2025-10-08 21:30:00"
}
```

## Usage Examples

### Example 1: Get All Expenses for Current Year
```bash
curl -X POST http://localhost/amt/api/expense-group-report/filter \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 2: Get This Month's Expenses
```bash
curl -X POST http://localhost/amt/api/expense-group-report/filter \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"search_type": "this_month"}'
```

### Example 3: Get Expenses for Specific Date Range
```bash
curl -X POST http://localhost/amt/api/expense-group-report/filter \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"date_from": "2025-01-01", "date_to": "2025-06-30"}'
```

### Example 4: Get Expenses for Specific Head
```bash
curl -X POST http://localhost/amt/api/expense-group-report/filter \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"search_type": "this_year", "head_id": 1}'
```

### Example 5: Get Filter Options
```bash
curl -X POST http://localhost/amt/api/expense-group-report/list \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

## Error Handling

### Common Error Scenarios

1. **Missing Authentication Headers**
   - HTTP Status: 401
   - Response: `{"status": 0, "message": "Unauthorized access"}`

2. **Invalid Date Format**
   - HTTP Status: 200 (gracefully handled)
   - Response: Returns data for current year

3. **Invalid Expense Head ID**
   - HTTP Status: 200
   - Response: Returns empty data array

4. **Server Error**
   - HTTP Status: 500
   - Response: `{"status": 0, "message": "Error retrieving expense group report: [details]"}`

## Notes

1. **Graceful Handling**: This API follows the established pattern where empty/null parameters return all records instead of validation errors.

2. **Date Range Priority**: If both `search_type` and custom dates are provided, `search_type` takes precedence.

3. **Default Behavior**: Empty request defaults to current year (Jan 1 to Dec 31 of current year).

4. **Performance**: For large datasets, consider using specific date ranges or expense head filters.

5. **JSON-Only Output**: API returns pure JSON without HTML errors (error display is disabled).

## Related APIs

- Collection Report API
- Total Student Academic Report API
- Student Academic Report API
- Report By Name API
- Online Admission Report API

## Version History

- **v1.0.0** (2025-10-08): Initial release with graceful null/empty handling

## Support

For issues or questions, please contact the development team.

