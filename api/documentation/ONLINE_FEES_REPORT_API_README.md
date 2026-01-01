# Online Fees Report API Documentation

## Overview

The Online Fees Report API provides endpoints to retrieve online fee collection reports. This API shows fees paid through online payment gateways, including student details, payment information, and transaction details.

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

### 1. Filter Online Fees Report

Retrieve online fee collection records with optional filters.

**Endpoint:** `POST /online-fees-report/filter`

**Request Body:**

All parameters are optional. Empty request `{}` returns all online fees for the current year.

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
  "message": "Online fees report retrieved successfully",
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
    "total_records": 25,
    "total_amount": "125000.00"
  },
  "total_records": 25,
  "data": [
    {
      "id": "1",
      "student_id": "101",
      "admission_no": "ADM001",
      "student_name": "John Doe",
      "class": "Class 1",
      "section": "A",
      "fee_group": "Tuition Fee",
      "fee_type": "Monthly Fee",
      "fee_code": "TF001",
      "amount": "5000.00",
      "payment_date": "2025-10-05",
      "payment_mode": "Online"
    },
    {
      "id": "2",
      "student_id": "102",
      "admission_no": "ADM002",
      "student_name": "Jane Smith",
      "class": "Class 2",
      "section": "B",
      "fee_group": "Transport Fee",
      "fee_type": "Monthly Fee",
      "fee_code": "TRF001",
      "amount": "1500.00",
      "payment_date": "2025-10-10",
      "payment_mode": "PayPal"
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
| total_records | integer | Number of online fee records |
| data | array | Array of online fee records |
| timestamp | string | Response timestamp |

**Online Fee Record Fields:**

| Field | Type | Description |
|-------|------|-------------|
| id | string | Fee deposit record ID |
| student_id | string | Student ID |
| admission_no | string | Student admission number |
| student_name | string | Full student name |
| class | string | Class name |
| section | string | Section name |
| fee_group | string | Fee group name |
| fee_type | string | Fee type name |
| fee_code | string | Fee type code |
| amount | string | Payment amount |
| payment_date | string | Payment date |
| payment_mode | string | Payment mode/gateway |

---

### 2. List Filter Options

Get available filter options for online fees report.

**Endpoint:** `POST /online-fees-report/list`

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

### Example 1: Get All Online Fees for Current Year (Empty Request)

**Request:**

```bash
curl -X POST http://localhost/amt/api/online-fees-report/filter \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Response:**

Returns all online fee payments for the current year (2025-01-01 to 2025-12-31).

---

### Example 2: Get Online Fees for This Month

**Request:**

```bash
curl -X POST http://localhost/amt/api/online-fees-report/filter \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"search_type": "this_month"}'
```

**Response:**

Returns online fee payments for the current month.

---

### Example 3: Get Online Fees for Custom Date Range

**Request:**

```bash
curl -X POST http://localhost/amt/api/online-fees-report/filter \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "date_from": "2025-01-01",
    "date_to": "2025-03-31"
  }'
```

**Response:**

Returns online fee payments from January 1, 2025 to March 31, 2025.

---

### Example 4: Get Filter Options

**Request:**

```bash
curl -X POST http://localhost/amt/api/online-fees-report/list \
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

$url = 'http://localhost/amt/api/online-fees-report/filter';
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
    echo "Total Online Fees: " . $result['summary']['total_amount'] . "\n";
    echo "Total Records: " . $result['total_records'] . "\n\n";
    
    foreach ($result['data'] as $fee) {
        echo "Student: " . $fee['student_name'] . " (" . $fee['admission_no'] . ")\n";
        echo "Class: " . $fee['class'] . " - " . $fee['section'] . "\n";
        echo "Fee: " . $fee['fee_group'] . " - " . $fee['fee_type'] . "\n";
        echo "Amount: " . $fee['amount'] . "\n";
        echo "Payment Date: " . $fee['payment_date'] . "\n";
        echo "Payment Mode: " . $fee['payment_mode'] . "\n\n";
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

1. **Empty Request Handling:** Empty request `{}` returns all online fees for the current year (graceful handling)
2. **Date Format:** All dates use Y-m-d format (e.g., 2025-10-08)
3. **Amount Format:** All amounts are formatted with 2 decimal places
4. **Search Type Priority:** If both search_type and custom dates are provided, search_type takes priority
5. **Default Behavior:** If no filters provided, defaults to current year
6. **Payment Modes:** Includes various online payment gateways (PayPal, Stripe, Razorpay, etc.)

---

## Data Source

The API retrieves data from the `student_fees_deposite` table, which stores online fee payments. The data includes:

- Student information (from `students` table)
- Class and section details (from `classes` and `sections` tables)
- Fee group and type information (from `fee_groups` and `feetype` tables)
- Payment details parsed from `amount_detail` JSON field

---

## Business Logic

1. **Amount Calculation:**
   - Parses `amount_detail` JSON field
   - Sums up all payment amounts
   - Extracts payment date and mode from the first payment entry

2. **Student Name:**
   - Concatenates firstname, middlename (if exists), and lastname
   - Provides full student name in response

3. **Date Filtering:**
   - Filters based on payment date
   - Supports predefined date ranges and custom periods

---

## Use Cases

1. **Daily Collection Report:** Use `search_type: "today"` to get today's online collections
2. **Monthly Report:** Use `search_type: "this_month"` for monthly reconciliation
3. **Custom Period Report:** Use `date_from` and `date_to` for specific date ranges
4. **Payment Gateway Analysis:** Analyze payment modes to see which gateways are most used
5. **Student Payment History:** Filter by student to see their online payment history

---

## Support

For issues or questions, please contact the development team.

**API Status:** ✅ Fully Working

**Last Tested:** October 9, 2025

**Last Updated:** October 9, 2025 - Fixed model loading issues and implemented direct database queries

