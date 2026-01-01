# Income Group Report API Documentation

## Overview

The Income Group Report API provides endpoints to retrieve income reports grouped by income heads with flexible filtering options. This API allows you to fetch income data for different time periods and filter by specific income heads.

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

### 1. Filter Income Group Report

Retrieves income records grouped by income heads with optional filters.

**Endpoint:** `POST /income-group-report/filter`

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
| `head` | integer | Income head ID to filter by | `1` |

**Graceful Null/Empty Handling:**
- Empty request `{}` returns all income for the current year
- Null or empty parameters are ignored
- No validation errors for missing parameters

**Request Examples:**

1. **Empty Request (Returns all income for current year):**
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

4. **Filter by Income Head:**
```json
{
  "search_type": "this_year",
  "head": "1"
}
```

**Success Response (HTTP 200):**

```json
{
  "status": 1,
  "message": "Income group report retrieved successfully",
  "filters_applied": {
    "search_type": null,
    "date_from": "2025-01-01",
    "date_to": "2025-12-31",
    "head": null
  },
  "date_range": {
    "start_date": "2025-01-01",
    "end_date": "2025-12-31",
    "label": "01/01/2025 to 31/12/2025"
  },
  "summary": {
    "total_records": 8,
    "total_amount": "57500.00",
    "income_heads": [
      {
        "head_id": "1",
        "income_category": "Fees Collection",
        "count": 5,
        "total": 45000
      },
      {
        "head_id": "2",
        "income_category": "Donations",
        "count": 3,
        "total": 12500
      }
    ]
  },
  "total_records": 8,
  "data": [
    {
      "id": "1",
      "name": "Student Fee Payment",
      "invoice_no": "INV001",
      "date": "2025-01-15",
      "amount": "5000.00",
      "income_category": "Fees Collection",
      "head_id": "1",
      "note": "Monthly fee payment",
      "documents": ""
    },
    {
      "id": "2",
      "name": "Library Fee",
      "invoice_no": "INV002",
      "date": "2025-01-20",
      "amount": "1500.00",
      "income_category": "Library",
      "head_id": "3",
      "note": "Annual library fee",
      "documents": ""
    }
  ],
  "timestamp": "2025-10-08 22:15:30"
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
| `summary.total_records` | integer | Total number of income records |
| `summary.total_amount` | string | Total income amount |
| `summary.income_heads` | array | Income grouped by heads |
| `total_records` | integer | Total number of records |
| `data` | array | Array of income records |
| `data[].id` | string | Income ID |
| `data[].name` | string | Income name/description |
| `data[].invoice_no` | string | Invoice number |
| `data[].date` | string | Income date (Y-m-d) |
| `data[].amount` | string | Income amount |
| `data[].income_category` | string | Income head category |
| `data[].head_id` | string | Income head ID |
| `data[].note` | string | Additional notes |
| `data[].documents` | string | Document path |
| `timestamp` | string | Response timestamp |

**Error Response (HTTP 200):**

```json
{
  "status": 0,
  "message": "Unauthorized access",
  "timestamp": "2025-10-08 22:15:30"
}
```

### 2. List Income Heads

Retrieves available income heads and search types for filtering.

**Endpoint:** `POST /income-group-report/list`

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
  "message": "Income heads retrieved successfully",
  "data": {
    "income_heads": [
      {
        "id": "1",
        "income_category": "Fees Collection",
        "description": "Student fee collection"
      },
      {
        "id": "2",
        "income_category": "Donations",
        "description": "Donations received"
      },
      {
        "id": "3",
        "income_category": "Library",
        "description": "Library fees"
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
  "timestamp": "2025-10-08 22:15:30"
}
```

## Usage Examples

### cURL Examples

**1. Get all income for current year:**
```bash
curl -X POST http://localhost/amt/api/income-group-report/filter \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**2. Get income for this month:**
```bash
curl -X POST http://localhost/amt/api/income-group-report/filter \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"search_type":"this_month"}'
```

**3. Get income for custom date range:**
```bash
curl -X POST http://localhost/amt/api/income-group-report/filter \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"date_from":"2025-01-01","date_to":"2025-12-31"}'
```

**4. Get income heads list:**
```bash
curl -X POST http://localhost/amt/api/income-group-report/list \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### PHP Example

```php
<?php
$url = 'http://localhost/amt/api/income-group-report/filter';
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
  "timestamp": "2025-10-08 22:15:30"
}
```

**Server Error:**
```json
{
  "status": 0,
  "message": "An error occurred while processing the request",
  "error": "Error details here",
  "timestamp": "2025-10-08 22:15:30"
}
```

## Notes

1. **Graceful Handling:** Empty or null parameters are handled gracefully - no validation errors
2. **Default Behavior:** Empty request returns all income for the current year
3. **Date Format:** All dates use Y-m-d format (e.g., 2025-01-01)
4. **Amount Format:** Amounts are returned as strings with 2 decimal places
5. **Grouping:** Income is automatically grouped by income heads in the summary
6. **Sorting:** Results are sorted by income head ID (descending) and date (descending)

## Status Codes

- `status: 1` - Success
- `status: 0` - Error (authentication, server error, etc.)

## Version

API Version: 1.0  
Last Updated: October 8, 2025

