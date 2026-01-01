# Due Fees Remark Report API Documentation

## Overview

The Due Fees Remark Report API provides endpoints to retrieve balance fees report with remarks. This API shows students with due fees filtered by class, section, and session, including fee details, payment information, and remarks.

**Base URL:** `http://localhost/amt/api`

**API Version:** 1.0

**Last Updated:** October 9, 2025

**Key Features:**
- ✅ **Graceful Null Handling** - Returns all records when filters are not provided (instead of error message)
- ✅ **Session ID Support** - Filter by specific session or use current active session
- ✅ **Flexible Filtering** - Filter by class only, section only, or both
- ✅ **Comprehensive Summary** - Total students, amounts, payments, and balances

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

### 1. Filter Due Fees Remark Report

Retrieve students with due fees by class, section, and/or session with graceful null handling.

**Endpoint:** `POST /due-fees-remark-report/filter`

**Request Body Examples:**

**Example 1 - Get all due fees (empty request):**
```json
{}
```

**Example 2 - Get all due fees for specific session:**
```json
{
  "session_id": "25"
}
```

**Example 3 - Get due fees for specific class (all sections):**
```json
{
  "class_id": "1"
}
```

**Example 4 - Get due fees for specific class and section:**
```json
{
  "class_id": "1",
  "section_id": "2"
}
```

**Example 5 - Get due fees for specific class, section, and session:**
```json
{
  "class_id": "1",
  "section_id": "2",
  "session_id": "25"
}
```

**Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| class_id | string | No | Class ID to filter by. If not provided, returns all classes. |
| section_id | string | No | Section ID to filter by. If not provided, returns all sections. |
| session_id | string | No | Session ID to filter by. If not provided, uses current active session. |

**Behavior:**
- **Empty request `{}`**: Returns ALL due fees records for current session
- **Only `class_id`**: Returns all sections in that class for current session
- **Only `section_id`**: Returns that section across all classes for current session
- **Only `session_id`**: Returns all classes and sections for that session
- **Any combination**: Filters by all provided parameters
- **Null/empty values**: Treated the same as not providing the parameter

**Success Response:**

```json
{
  "status": 1,
  "message": "Due fees remark report retrieved successfully",
  "filters_applied": {
    "class_id": "1",
    "section_id": "2",
    "session_id": "21",
    "date": "2025-10-09"
  },
  "summary": {
    "total_students": 5,
    "total_amount": "50000.00",
    "total_paid": "30000.00",
    "total_balance": "20000.00"
  },
  "total_records": 5,
  "data": [
    {
      "student_id": "101",
      "admission_no": "ADM001",
      "firstname": "John",
      "middlename": "",
      "lastname": "Doe",
      "class": "Class 1",
      "section": "A",
      "guardian_phone": "9876543210",
      "remark": "Payment pending",
      "fees": [
        {
          "fee_group": "Tuition Fee",
          "fee_type": "Monthly Fee",
          "due_date": "2025-09-30",
          "amount": "5000.00",
          "paid": "3000.00",
          "balance": "2000.00"
        },
        {
          "fee_group": "Transport Fee",
          "fee_type": "Monthly Fee",
          "due_date": "2025-09-30",
          "amount": "1500.00",
          "paid": "0.00",
          "balance": "1500.00"
        }
      ],
      "total_amount": "6500.00",
      "total_paid": "3000.00",
      "total_balance": "3500.00"
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
| summary | object | Overall summary with totals |
| total_records | integer | Number of students with due fees |
| data | array | Array of student records |
| timestamp | string | Response timestamp |

**Student Record Fields:**

| Field | Type | Description |
|-------|------|-------------|
| student_id | string | Student ID |
| admission_no | string | Admission number |
| firstname | string | Student first name |
| middlename | string | Student middle name |
| lastname | string | Student last name |
| class | string | Class name |
| section | string | Section name |
| guardian_phone | string | Guardian phone number |
| remark | string | Remark/notes |
| fees | array | Array of fee details |
| total_amount | string | Total fee amount for student |
| total_paid | string | Total paid amount for student |
| total_balance | string | Total balance for student |

**Fee Detail Fields:**

| Field | Type | Description |
|-------|------|-------------|
| fee_group | string | Fee group name |
| fee_type | string | Fee type name |
| due_date | string | Fee due date (Y-m-d) |
| amount | string | Fee amount |
| paid | string | Paid amount |
| balance | string | Balance amount |

---

### 2. List Filter Options

Get available classes for filtering.

**Endpoint:** `POST /due-fees-remark-report/list`

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
    "classes": [
      {
        "id": "1",
        "class": "Class 1",
        "sections": [
          {
            "id": "1",
            "section": "A"
          },
          {
            "id": "2",
            "section": "B"
          }
        ]
      },
      {
        "id": "2",
        "class": "Class 2",
        "sections": [
          {
            "id": "3",
            "section": "A"
          }
        ]
      }
    ]
  },
  "timestamp": "2025-10-08 22:30:00"
}
```

---

## Graceful Null Handling

This API follows the graceful null handling pattern, treating empty filters the same as list endpoints by returning all available records instead of validation errors.

### Behavior Summary

| Request | Behavior |
|---------|----------|
| `{}` (empty) | Returns ALL due fees for current session |
| `{"class_id": null}` | Same as empty - returns all classes |
| `{"class_id": ""}` | Same as empty - returns all classes |
| `{"class_id": "1"}` | Returns all sections in class 1 |
| `{"class_id": "1", "section_id": "2"}` | Returns specific class and section |
| `{"session_id": "25"}` | Returns all classes/sections for session 25 |

### Key Points

1. **No Validation Errors**: The API never returns "Please select class and section" error anymore
2. **Default to Current Session**: If `session_id` is not provided, uses the current active session
3. **Flexible Filtering**: Any combination of parameters works together
4. **Consistent Behavior**: Null, empty string, and missing parameters are treated identically

---

## Usage Examples

### Example 1: Get All Due Fees (Empty Request - NEW BEHAVIOR)

**Request:**

```bash
curl -X POST http://localhost/amt/api/due-fees-remark-report/filter \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Response:**

```json
{
  "status": 1,
  "message": "Due fees remark report retrieved successfully",
  "filters_applied": {
    "class_id": null,
    "section_id": null,
    "session_id": "21",
    "date": "2025-10-09"
  },
  "summary": {
    "total_students": 294,
    "total_amount": "6973400.00",
    "total_paid": "5459600.00",
    "total_balance": "1513800.00"
  },
  "total_records": 294,
  "data": [
    {
      "student_id": "101",
      "admission_no": "ADM001",
      "firstname": "John",
      "middlename": "",
      "lastname": "Doe",
      "class": "Class 5",
      "section": "A",
      "guardian_phone": "9876543210",
      "remark": "",
      "fees": [...],
      "total_amount": "25000.00",
      "total_paid": "15000.00",
      "total_balance": "10000.00"
    }
    // ... more students
  ],
  "timestamp": "2025-10-09 12:34:56"
}
```

**Note:** This now returns ALL due fees records for the current session instead of an error message.

---

### Example 2: Get Due Fees for Specific Class Only

**Request:**

```bash
curl -X POST http://localhost/amt/api/due-fees-remark-report/filter \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "class_id": "1"
  }'
```

**Response:**

Returns all students with due fees in Class 1 (all sections) for current session.

---

### Example 3: Get Due Fees for Specific Class and Section

**Request:**

```bash
curl -X POST http://localhost/amt/api/due-fees-remark-report/filter \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "class_id": "1",
    "section_id": "2"
  }'
```

**Response:**

Returns students with due fees in Class 1, Section 2 for current session.

---

### Example 4: Get Due Fees for Specific Session

**Request:**

```bash
curl -X POST http://localhost/amt/api/due-fees-remark-report/filter \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "session_id": "25"
  }'
```

**Response:**

Returns all students with due fees for session 25 (all classes and sections).

---

### Example 5: Get Due Fees for Specific Class, Section, and Session

**Request:**

```bash
curl -X POST http://localhost/amt/api/due-fees-remark-report/filter \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "class_id": "1",
    "section_id": "2",
    "session_id": "25"
  }'
```

**Response:**

Returns students with due fees in Class 1, Section 2 for session 25.

---

### Example 6: Get Available Classes

**Request:**

```bash
curl -X POST http://localhost/amt/api/due-fees-remark-report/list \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Response:**

Returns all available classes with their sections.

---

## PHP Usage Example

```php
<?php

$url = 'http://localhost/amt/api/due-fees-remark-report/filter';
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

$data = [
    'class_id' => '1',
    'section_id' => '2'
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
    echo "Total Students with Due Fees: " . $result['summary']['total_students'] . "\n";
    echo "Total Balance: " . $result['summary']['total_balance'] . "\n\n";
    
    foreach ($result['data'] as $student) {
        echo "Student: " . $student['firstname'] . " " . $student['lastname'] . "\n";
        echo "Admission No: " . $student['admission_no'] . "\n";
        echo "Balance: " . $student['total_balance'] . "\n";
        echo "Guardian Phone: " . $student['guardian_phone'] . "\n";
        echo "Remark: " . $student['remark'] . "\n\n";
        
        foreach ($student['fees'] as $fee) {
            echo "  - " . $fee['fee_group'] . " (" . $fee['fee_type'] . ")\n";
            echo "    Amount: " . $fee['amount'] . ", Paid: " . $fee['paid'] . ", Balance: " . $fee['balance'] . "\n";
        }
        echo "\n";
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

1. **Empty Request Handling:** Empty request `{}` returns a message to select class and section (graceful handling)
2. **Date Format:** All dates use Y-m-d format (e.g., 2025-10-08)
3. **Amount Format:** All amounts are formatted with 2 decimal places
4. **Due Date Filter:** Only shows fees with due dates before the current date
5. **Fee Calculation:** Parses amount_detail JSON to calculate paid amounts and balance
6. **Grouping:** Results are grouped by student, with all their due fees listed

---

## Business Logic

1. **Due Fee Calculation:**
   - For system fees: Uses previous_balance_amount
   - For custom fees: Uses amount field
   - Paid amount is calculated from amount_detail JSON
   - Balance = Amount - Paid

2. **Student Grouping:**
   - All fees for a student are grouped together
   - Student totals are calculated across all their fees
   - Overall summary includes all students

3. **Remark Field:**
   - Shows any remarks/notes added for the student
   - Can be used for follow-up actions

---

## Support

For issues or questions, please contact the development team.

**API Status:** ✅ Fully Working

**Last Tested:** October 9, 2025

**Last Updated:** October 9, 2025 - Fixed model loading issues and implemented direct database queries

