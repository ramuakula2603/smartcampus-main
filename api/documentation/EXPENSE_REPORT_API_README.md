# Expense Report API Documentation

## Overview

The Expense Report API provides endpoints to retrieve expense reports grouped by expense heads with flexible filtering options. This API allows you to fetch expense data for different time periods and filter by specific expense heads.

## Base URL

```
http://localhost/amt/api
```

## Authentication

All API requests require authentication headers:

```
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

## Endpoints

### 1. Filter Expense Report

Retrieves expense records grouped by expense heads with optional filters.

**Endpoint:** `POST /expense-report/filter`

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Request Body Parameters (all optional):**

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `search_type` | string | Predefined time period | `today`, `this_week`, `this_month`, `last_month`, `this_year`, `period` |
| `date_from` | string | Start date (Y-m-d format) | `2025-01-01` |
| `date_to` | string | End date (Y-m-d format) | `2025-12-31` |

**Graceful Null/Empty Handling:**
- Empty request `{}` returns all expenses for the current year
- Null or empty parameters are ignored
- No validation errors for missing parameters

**Request Examples:**

1. **Empty Request (Returns all expenses for current year):**
```json
{}
```

2. **Filter by Search Type:**
```json
{
  "search_type": "this_month"
}
```

3. **Filter by Custom Date Range:**
```json
{
  "date_from": "2025-01-01",
  "date_to": "2025-12-31"
}
```

**Success Response (HTTP 200):**

```json
{
  "status": 1,
  "message": "Expense report retrieved successfully",
  "filters_applied": {
    "search_type": null,
    "date_from": "2025-01-01",
    "date_to": "2025-12-31"
  },
  "date_range": {
    "start_date": "2025-01-01",
    "end_date": "2025-12-31",
    "label": "01/01/2025 to 31/12/2025"
  },
  "summary": {
    "total_records": 9,
    "total_amount": "21700.00",
    "expense_heads": [
      {
        "head_id": "1",
        "exp_category": "Utilities",
        "count": 4,
        "total": 8500
      },
      {
        "head_id": "2",
        "exp_category": "Maintenance",
        "count": 5,
        "total": 13200
      }
    ]
  },
  "total_records": 9,
  "data": [
    {
      "id": "1",
      "name": "Electricity Bill",
      "invoice_no": "EXP001",
      "date": "2025-01-15",
      "amount": "2500.00",
      "exp_category": "Utilities",
      "exp_head_id": "1",
      "note": "Monthly electricity bill",
      "documents": ""
    },
    {
      "id": "2",
      "name": "Building Repair",
      "invoice_no": "EXP002",
      "date": "2025-01-20",
      "amount": "5000.00",
      "exp_category": "Maintenance",
      "exp_head_id": "2",
      "note": "Roof repair",
      "documents": ""
    }
  ],
  "timestamp": "2025-10-08 23:00:00"
}
```

**Response Fields:**

| Field | Type | Description |
|-------|------|-------------|
| `status` | integer | 1 for success, 0 for error |
| `message` | string | Response message |
| `filters_applied` | object | Applied filter parameters |
| `date_range` | object | Date range information |
| `summary` | object | Summary statistics |
| `summary.total_records` | integer | Total number of expense records |
| `summary.total_amount` | string | Total expense amount |
| `summary.expense_heads` | array | Expenses grouped by heads |
| `total_records` | integer | Total number of records |
| `data` | array | Array of expense records |
| `data[].id` | string | Expense ID |
| `data[].name` | string | Expense name/description |
| `data[].invoice_no` | string | Invoice number |
| `data[].date` | string | Expense date (Y-m-d) |
| `data[].amount` | string | Expense amount |
| `data[].exp_category` | string | Expense head category |
| `data[].exp_head_id` | string | Expense head ID |
| `data[].note` | string | Additional notes |
| `data[].documents` | string | Document path |
| `timestamp` | string | Response timestamp |

**Error Response (HTTP 200):**

```json
{
  "status": 0,
  "message": "Unauthorized access",
  "timestamp": "2025-10-08 23:00:00"
}
```

### 2. List Expense Heads

Retrieves available expense heads and search types for filtering.

**Endpoint:** `POST /expense-report/list`

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Request Body:**
```json
{}
```

**Success Response (HTTP 200):**

```json
{
  "status": 1,
  "message": "Expense heads retrieved successfully",
  "data": {
    "expense_heads": [
      {
        "id": "1",
        "exp_category": "Utilities",
        "description": "Utility expenses"
      },
      {
        "id": "2",
        "exp_category": "Maintenance",
        "description": "Maintenance expenses"
      },
      {
        "id": "3",
        "exp_category": "Salaries",
        "description": "Staff salaries"
      }
    ],
    "search_types": [
      {
        "key": "today",
        "label": "Today"
      },
      {
        "key": "this_week",
        "label": "This Week"
      },
      {
        "key": "this_month",
        "label": "This Month"
      },
      {
        "key": "last_month",
        "label": "Last Month"
      },
      {
        "key": "this_year",
        "label": "This Year"
      },
      {
        "key": "period",
        "label": "Custom Period"
      }
    ]
  },
  "timestamp": "2025-10-08 23:00:00"
}
```

## Usage Examples

### cURL Examples

**1. Get all expenses for current year:**
```bash
curl -X POST http://localhost/amt/api/expense-report/filter \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**2. Get expenses for this month:**
```bash
curl -X POST http://localhost/amt/api/expense-report/filter \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"search_type":"this_month"}'
```

**3. Get expenses for custom date range:**
```bash
curl -X POST http://localhost/amt/api/expense-report/filter \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"date_from":"2025-01-01","date_to":"2025-12-31"}'
```

**4. Get expense heads list:**
```bash
curl -X POST http://localhost/amt/api/expense-report/list \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### PHP Example

```php
<?php
$url = 'http://localhost/amt/api/expense-report/filter';
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

$data = [
    'search_type' => 'this_month'
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$result = json_decode($response, true);
print_r($result);
?>
```

## Error Handling

The API returns JSON responses for all errors:

**Authentication Error:**
```json
{
  "status": 0,
  "message": "Unauthorized access",
  "timestamp": "2025-10-08 23:00:00"
}
```

**Server Error:**
```json
{
  "status": 0,
  "message": "An error occurred while processing the request",
  "error": "Error details here",
  "timestamp": "2025-10-08 23:00:00"
}
```

## Notes

1. **Graceful Handling:** Empty or null parameters are handled gracefully - no validation errors
2. **Default Behavior:** Empty request returns all expenses for the current year
3. **Date Format:** All dates use Y-m-d format (e.g., 2025-01-01)
4. **Amount Format:** Amounts are returned as strings with 2 decimal places
5. **Grouping:** Expenses are automatically grouped by expense heads in the summary
6. **Sorting:** Results are sorted by date (descending)

## Status Codes

- `status: 1` - Success
- `status: 0` - Error (authentication, server error, etc.)

## Version

API Version: 1.0  
Last Updated: October 8, 2025

