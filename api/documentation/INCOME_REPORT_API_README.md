# Income Report API Documentation

## Overview

The Income Report API provides endpoints to retrieve income records for a school management system. This API shows all income records without grouping (different from Income Group Report API which groups by income heads).

**Base URL:** `http://localhost/amt/api`

**API Version:** 1.0

**Last Updated:** October 8, 2025

---

## Authentication

All API requests require the following headers:

```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

---

## Endpoints

### 1. Filter Income Report

Retrieve income records with optional filters.

**Endpoint:** `POST /income-report/filter`

**Request Body:**

All parameters are optional. Empty request `{}` returns all income for the current year.

```json
{
  "search_type": "today|this_week|this_month|last_month|this_year|period",
  "date_from": "2025-01-01",
  "date_to": "2025-12-31"
}
```

**Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| search_type | string | No | Predefined date range (today, this_week, this_month, last_month, this_year, period) |
| date_from | string | No | Start date (Y-m-d format). Used when search_type is not provided |
| date_to | string | No | End date (Y-m-d format). Used when search_type is not provided |

**Response:**

```json
{
  "status": 1,
  "message": "Income report retrieved successfully",
  "filters_applied": {
    "search_type": "this_month",
    "date_from": "2025-10-01",
    "date_to": "2025-10-31"
  },
  "date_range": {
    "start_date": "2025-10-01",
    "end_date": "2025-10-31",
    "label": "October 2025"
  },
  "summary": {
    "total_records": 15,
    "total_amount": "125000.00"
  },
  "total_records": 15,
  "data": [
    {
      "id": "1",
      "name": "Admission Fee",
      "invoice_no": "INV001",
      "date": "2025-10-05",
      "amount": "5000.00",
      "income_head": "Admission Fees",
      "income_head_id": "1",
      "note": "New admission",
      "documents": ""
    },
    {
      "id": "2",
      "name": "Library Fee",
      "invoice_no": "INV002",
      "date": "2025-10-10",
      "amount": "1500.00",
      "income_head": "Library Fees",
      "income_head_id": "2",
      "note": "",
      "documents": ""
    }
  ],
  "timestamp": "2025-10-08 22:30:00"
}
```

**Response Fields:**

| Field | Type | Description |
|-------|------|-------------|
| status | integer | 1 for success, 0 for error |
| message | string | Response message |
| filters_applied | object | Filters that were applied |
| date_range | object | Date range details |
| summary | object | Summary with total records and amount |
| total_records | integer | Number of income records |
| data | array | Array of income records |
| timestamp | string | Response timestamp |

**Income Record Fields:**

| Field | Type | Description |
|-------|------|-------------|
| id | string | Income record ID |
| name | string | Income name/description |
| invoice_no | string | Invoice number |
| date | string | Income date (Y-m-d) |
| amount | string | Income amount |
| income_head | string | Income head category |
| income_head_id | string | Income head ID |
| note | string | Additional notes |
| documents | string | Attached documents |

---

### 2. List Filter Options

Get available filter options for income report.

**Endpoint:** `POST /income-report/list`

**Request Body:**

```json
{}
```

**Response:**

```json
{
  "status": 1,
  "message": "Filter options retrieved successfully",
  "data": {
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
  "timestamp": "2025-10-08 22:30:00"
}
```

---

## Usage Examples

### Example 1: Get All Income for Current Year (Empty Request)

**Request:**

```bash
curl -X POST http://localhost/amt/api/income-report/filter \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Response:**

Returns all income records for the current year (2025-01-01 to 2025-12-31).

---

### Example 2: Get Income for This Month

**Request:**

```bash
curl -X POST http://localhost/amt/api/income-report/filter \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"search_type": "this_month"}'
```

**Response:**

Returns income records for the current month.

---

### Example 3: Get Income for Custom Date Range

**Request:**

```bash
curl -X POST http://localhost/amt/api/income-report/filter \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "date_from": "2025-01-01",
    "date_to": "2025-03-31"
  }'
```

**Response:**

Returns income records from January 1, 2025 to March 31, 2025.

---

### Example 4: Get Filter Options

**Request:**

```bash
curl -X POST http://localhost/amt/api/income-report/list \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Response:**

Returns available search types for filtering.

---

## PHP Usage Example

```php
<?php

$url = 'http://localhost/amt/api/income-report/filter';
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

if ($result['status'] == 1) {
    echo "Total Income: " . $result['summary']['total_amount'] . "\n";
    echo "Total Records: " . $result['total_records'] . "\n";
    
    foreach ($result['data'] as $income) {
        echo $income['name'] . " - " . $income['amount'] . "\n";
    }
} else {
    echo "Error: " . $result['message'] . "\n";
}
?>
```

---

## Error Responses

### Unauthorized Access

```json
{
  "status": 0,
  "message": "Unauthorized access",
  "timestamp": "2025-10-08 22:30:00"
}
```

### Server Error

```json
{
  "status": 0,
  "message": "An error occurred while processing the request",
  "error": "Error details here",
  "timestamp": "2025-10-08 22:30:00"
}
```

---

## Notes

1. **Empty Request Handling:** Empty request `{}` returns all income for the current year (graceful handling)
2. **Date Format:** All dates use Y-m-d format (e.g., 2025-10-08)
3. **Amount Format:** All amounts are formatted with 2 decimal places
4. **Search Type Priority:** If both search_type and custom dates are provided, search_type takes priority
5. **Default Behavior:** If no filters provided, defaults to current year

---

## Difference from Income Group Report API

| Feature | Income Report API | Income Group Report API |
|---------|-------------------|------------------------|
| Purpose | Show all income records | Show income grouped by heads |
| Grouping | No grouping | Grouped by income head |
| Filters | search_type, date_from, date_to | search_type, date_from, date_to, head |
| Summary | Total amount only | Total + breakdown by head |
| Use Case | Detailed income list | Summary by category |

---

## Support

For issues or questions, please contact the development team.

**API Status:** ✅ Fully Working

**Last Tested:** October 9, 2025

**Last Updated:** October 9, 2025 - Fixed model loading issues and implemented direct database queries

