# Type-wise Balance Report API Documentation

## Overview

The Type-wise Balance Report API provides endpoints to retrieve student fee balance information categorized by fee type. This API allows you to filter balance reports by session, fee type, fee group, class, and section, making it easy to track outstanding fees for specific fee types (like TUITION FEE, ADMISSION FEE, etc.).

**Base URL:** `http://localhost/amt/api`

**API Version:** 1.0

**Last Updated:** 2025-10-10

---

## Table of Contents

1. [Authentication](#authentication)
2. [Endpoints](#endpoints)
   - [Filter Endpoint](#1-filter-endpoint)
   - [List Endpoint](#2-list-endpoint)
3. [Request & Response Examples](#request--response-examples)
4. [Field Descriptions](#field-descriptions)
5. [Use Cases](#use-cases)
6. [Error Handling](#error-handling)
7. [Best Practices](#best-practices)
8. [FAQ](#faq)

---

## Authentication

All API requests require authentication headers:

```http
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Authentication Failure Response:**
```json
{
  "status": 0,
  "message": "Unauthorized access"
}
```
**HTTP Status Code:** 401

---

## Endpoints

### 1. Filter Endpoint

Retrieve type-wise balance report data with optional filters.

**Endpoint:** `POST /api/type-wise-balance-report/filter`

**Request Body:**
```json
{
  "session_id": "21",
  "feetype_ids": ["33"],
  "feegroup_ids": ["139", "147"],
  "class_id": "10",
  "section_id": "11"
}
```

**Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| session_id | string | **Yes** | Session ID to filter by |
| feetype_ids | array | No | Array of fee type IDs. Empty array `[]` returns all fee types |
| feegroup_ids | array | No | Array of fee group IDs to filter by |
| class_id | string | No | Class ID to filter by |
| section_id | string | No | Section ID to filter by |

**Important Notes:**
- `session_id` is **required**
- `feetype_ids` can be an empty array `[]` to return all fee types
- All other filters are optional
- All IDs are strings, not integers

**Success Response (HTTP 200):**
```json
{
  "status": 1,
  "message": "Type wise balance report retrieved successfully",
  "filters_applied": {
    "session_id": "21",
    "feetype_ids": ["33"],
    "feegroup_ids": null,
    "class_id": null,
    "section_id": null
  },
  "total_records": 1145,
  "data": [
    {
      "feegroupname": "2025-2026 SR MPC",
      "stfeemasid": "8895",
      "total": "22000.00",
      "fgtid": "379",
      "fine": "0.00",
      "type": "TUITION FEE",
      "section": "2025-26 SR SPARK",
      "class": "SR-MPC",
      "admission_no": "2025 SR-ONTC-53",
      "mobileno": "9949683860",
      "firstname": "MUTHAYA",
      "middlename": null,
      "lastname": "NAVANEETH",
      "total_amount": 0,
      "total_fine": 0,
      "total_discount": 0,
      "balance": "22000.00"
    }
  ],
  "timestamp": "2025-10-10 13:33:14"
}
```

**Error Response - Missing session_id (HTTP 400):**
```json
{
  "status": 0,
  "message": "session_id is required"
}
```

---

### 2. List Endpoint

Retrieve available filter options (sessions, fee types, fee groups, classes).

**Endpoint:** `POST /api/type-wise-balance-report/list`

**Request Body:**
```json
{}
```

**Success Response (HTTP 200):**
```json
{
  "status": 1,
  "message": "Type wise balance report filter options retrieved successfully",
  "sessions": [
    {
      "id": "21",
      "session": "2025-26",
      "is_active": "yes",
      "created_at": "2024-03-17 14:15:30",
      "updated_at": "0000-00-00"
    }
  ],
  "feegroups": [
    {
      "id": "139",
      "name": "2025-2026 -SR- 0NTC",
      "is_system": "0",
      "description": "",
      "is_active": "no",
      "created_at": "2024-04-06 16:01:55"
    }
  ],
  "feetypes": [
    {
      "id": "33",
      "is_system": "0",
      "feecategory_id": null,
      "type": "TUITION FEE",
      "code": "1",
      "is_active": "no",
      "description": "",
      "created_at": "2023-12-09 21:59:20",
      "updated_at": null
    }
  ],
  "classes": [
    {
      "id": "10",
      "class": "JR-BIPC",
      "is_active": "no",
      "created_at": "2024-03-17 14:17:56",
      "updated_at": null
    }
  ],
  "note": "Use the filter endpoint with session_id (required) and feetype_ids (required) to get type wise balance report",
  "timestamp": "2025-10-10 13:30:00"
}
```

---

## Field Descriptions

### Filter Response Data Fields

| Field | Type | Description | Example |
|-------|------|-------------|---------|
| feegroupname | string | Name of the fee group | "2025-2026 SR MPC" |
| stfeemasid | string | Student fee master ID | "8895" |
| total | string | Total fee amount (decimal format) | "22000.00" |
| fgtid | string | Fee groups feetype ID | "379" |
| fine | string | Fine amount (decimal format) | "0.00" |
| type | string | Fee type name | "TUITION FEE" |
| section | string | Section name | "2025-26 SR SPARK" |
| class | string | Class name | "SR-MPC" |
| admission_no | string | Student admission number | "2025 SR-ONTC-53" |
| mobileno | string | Student/parent mobile number | "9949683860" |
| firstname | string | Student first name | "MUTHAYA" |
| middlename | string/null | Student middle name (can be null) | null |
| lastname | string | Student last name | "NAVANEETH" |
| total_amount | integer | Total amount paid | 0 |
| total_fine | integer | Total fine paid | 0 |
| total_discount | integer | Total discount applied | 0 |
| balance | string/integer | Outstanding balance | "22000.00" or 16500 |

**Important Data Type Notes:**
- Most fields are **strings**, not integers
- `total`, `fine` are strings in decimal format (e.g., "22000.00")
- `total_amount`, `total_fine`, `total_discount` are **integers**
- `balance` can be either string or integer
- `middlename` can be `null`
- Parse string values before calculations: `parseFloat(record.total)`

### Balance Calculation

```javascript
balance = total - total_amount + total_fine - total_discount
```

**Example:**
- Total: 22000.00
- Paid: 0
- Fine: 0
- Discount: 0
- **Balance: 22000.00**

---

## Request & Response Examples

### Example 1: Get All Fee Types for Active Session

**Request:**
```bash
curl -X POST "http://localhost/amt/api/type-wise-balance-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "session_id": "21",
    "feetype_ids": []
  }'
```

**Response:**
```json
{
  "status": 1,
  "message": "Type wise balance report retrieved successfully",
  "filters_applied": {
    "session_id": "21",
    "feetype_ids": [],
    "feegroup_ids": null,
    "class_id": null,
    "section_id": null
  },
  "total_records": 6747,
  "data": [...],
  "timestamp": "2025-10-10 13:30:00"
}
```

### Example 2: Get TUITION FEE Balance for Specific Class

**Request:**
```bash
curl -X POST "http://localhost/amt/api/type-wise-balance-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "session_id": "21",
    "feetype_ids": ["33"],
    "class_id": "10"
  }'
```

**Response:**
```json
{
  "status": 1,
  "total_records": 42,
  "data": [...]
}
```

### Example 3: Get Multiple Fee Types

**Request:**
```json
{
  "session_id": "21",
  "feetype_ids": ["33", "40", "5"]
}
```

---

## Use Cases

### 1. Display Outstanding Tuition Fees

```javascript
fetch('http://localhost/amt/api/type-wise-balance-report/filter', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Client-Service': 'smartschool',
    'Auth-Key': 'schoolAdmin@'
  },
  body: JSON.stringify({
    session_id: '21',
    feetype_ids: ['33']  // TUITION FEE
  })
})
.then(response => response.json())
.then(data => {
  if (data.status === 1) {
    let totalBalance = 0;
    
    data.data.forEach(student => {
      const balance = parseFloat(student.total) - 
                     student.total_amount + 
                     student.total_fine - 
                     student.total_discount;
      totalBalance += balance;
    });
    
    console.log('Total Outstanding Tuition Fees: ₹' + totalBalance.toFixed(2));
  }
});
```

### 2. Generate Class-wise Fee Balance Report

```javascript
// Get balance for specific class
fetch('http://localhost/amt/api/type-wise-balance-report/filter', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Client-Service': 'smartschool',
    'Auth-Key': 'schoolAdmin@'
  },
  body: JSON.stringify({
    session_id: '21',
    feetype_ids: [],  // All fee types
    class_id: '10'    // JR-BIPC
  })
})
.then(response => response.json())
.then(data => {
  if (data.status === 1) {
    // Group by fee type
    const feeTypeBalances = {};

    data.data.forEach(student => {
      if (!feeTypeBalances[student.type]) {
        feeTypeBalances[student.type] = {
          total: 0,
          paid: 0,
          balance: 0,
          students: 0
        };
      }

      const balance = parseFloat(student.total) -
                     student.total_amount +
                     student.total_fine -
                     student.total_discount;

      feeTypeBalances[student.type].total += parseFloat(student.total);
      feeTypeBalances[student.type].paid += student.total_amount;
      feeTypeBalances[student.type].balance += balance;
      feeTypeBalances[student.type].students++;
    });

    console.log('Fee Type Balances:', feeTypeBalances);
  }
});
```

### 3. Export Balance Report to CSV

```javascript
async function exportBalanceReport(sessionId, feetypeIds) {
  const response = await fetch('http://localhost/amt/api/type-wise-balance-report/filter', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Client-Service': 'smartschool',
      'Auth-Key': 'schoolAdmin@'
    },
    body: JSON.stringify({
      session_id: sessionId,
      feetype_ids: feetypeIds
    })
  });

  const data = await response.json();

  if (data.status === 1) {
    // Create CSV header
    let csv = 'Admission No,Name,Class,Section,Fee Type,Fee Group,Total,Paid,Fine,Discount,Balance\n';

    // Add data rows
    data.data.forEach(student => {
      const name = `${student.firstname} ${student.middlename || ''} ${student.lastname}`.trim();
      const balance = parseFloat(student.total) -
                     student.total_amount +
                     student.total_fine -
                     student.total_discount;

      csv += `${student.admission_no},${name},${student.class},${student.section},`;
      csv += `${student.type},${student.feegroupname},${student.total},`;
      csv += `${student.total_amount},${student.total_fine},${student.total_discount},${balance}\n`;
    });

    // Download CSV
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'type_wise_balance_report.csv';
    a.click();
  }
}
```

---

## Error Handling

### Common Error Responses

#### 1. Missing Required Parameter (HTTP 400)
```json
{
  "status": 0,
  "message": "session_id is required"
}
```

#### 2. Unauthorized Access (HTTP 401)
```json
{
  "status": 0,
  "message": "Unauthorized access"
}
```

#### 3. Invalid Request Method (HTTP 400)
```json
{
  "status": 0,
  "message": "Bad request. Only POST method allowed."
}
```

#### 4. Internal Server Error (HTTP 500)
```json
{
  "status": 0,
  "message": "Internal server error",
  "error": "Detailed error message"
}
```

### Error Handling Example

```javascript
async function getBalanceReport(sessionId, feetypeIds) {
  try {
    const response = await fetch('http://localhost/amt/api/type-wise-balance-report/filter', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Client-Service': 'smartschool',
        'Auth-Key': 'schoolAdmin@'
      },
      body: JSON.stringify({
        session_id: sessionId,
        feetype_ids: feetypeIds
      })
    });

    const data = await response.json();

    if (data.status === 0) {
      // Handle API error
      console.error('API Error:', data.message);
      return null;
    }

    return data;

  } catch (error) {
    // Handle network error
    console.error('Network Error:', error);
    return null;
  }
}
```

---

## Best Practices

1. **Always include authentication headers** in every request
2. **Validate session_id** before making API calls
3. **Use empty feetype_ids array** to get all fee types
4. **Parse string values** before calculations (parseFloat, parseInt)
5. **Handle null values** for middlename and other optional fields
6. **Cache filter options** from list endpoint to reduce API calls
7. **Implement pagination** for large datasets on the frontend
8. **Calculate balance correctly** using the formula: `total - paid + fine - discount`
9. **Handle mixed data types** - some fields are strings, some are integers
10. **Use try-catch blocks** for error handling
11. **Validate filter parameters** before sending requests
12. **Display loading states** while fetching data

---

## FAQ

### Q1: Why is session_id required?

**A:** The type-wise balance report is session-specific. Each academic session has different fee structures and student enrollments, so session_id is mandatory to ensure accurate reporting.

### Q2: What happens if I pass an empty feetype_ids array?

**A:** Passing an empty array `[]` for feetype_ids will return balance data for **all fee types** in the specified session. This is useful for getting a complete overview.

### Q3: Can I filter by multiple classes?

**A:** Currently, the API accepts a single `class_id`. To get data for multiple classes, make separate API calls for each class or use empty class_id to get all classes and filter on the frontend.

### Q4: Why are some fields strings and others integers?

**A:** This is due to the database schema and model implementation. Fields like `total`, `fine`, and `balance` come from the database as strings in decimal format, while `total_amount`, `total_fine`, and `total_discount` are calculated as integers. Always parse values before calculations.

### Q5: How do I calculate the balance?

**A:** Use this formula:
```javascript
balance = parseFloat(total) - total_amount + total_fine - total_discount
```

### Q6: What if middlename is null?

**A:** The `middlename` field can be `null`. Handle it in your code:
```javascript
const fullName = `${student.firstname} ${student.middlename || ''} ${student.lastname}`.trim();
```

### Q7: Can I filter by multiple fee types?

**A:** Yes! Pass an array of fee type IDs:
```json
{
  "session_id": "21",
  "feetype_ids": ["33", "40", "5"]
}
```

### Q8: How do I get the list of available fee types?

**A:** Use the list endpoint:
```bash
curl -X POST "http://localhost/amt/api/type-wise-balance-report/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Q9: What's the difference between `total` and `balance`?

**A:**
- `total` = Total fee amount assigned to the student
- `balance` = Outstanding amount after deducting payments and discounts, adding fines

### Q10: Can I filter by fee group?

**A:** Yes, use the `feegroup_ids` parameter:
```json
{
  "session_id": "21",
  "feetype_ids": ["33"],
  "feegroup_ids": ["139", "147"]
}
```

### Q11: Why are all IDs returned as strings?

**A:** This is the standard behavior of the CodeIgniter framework when fetching data from MySQL. All database IDs are returned as strings. Convert them to integers if needed:
```javascript
const sessionId = parseInt(session.id);
```

### Q12: How do I handle large datasets?

**A:** For large datasets (thousands of records), implement pagination on the frontend:
```javascript
const pageSize = 100;
const page = 1;
const startIndex = (page - 1) * pageSize;
const endIndex = startIndex + pageSize;
const pageData = data.data.slice(startIndex, endIndex);
```

### Q13: Can I filter by section without specifying class?

**A:** While technically possible, it's recommended to specify both class_id and section_id for accurate results, as sections are typically associated with specific classes.

---

## Quick Reference: Data Type Conversions

```javascript
// Example API response data
const student = data.data[0];

// Convert IDs to integers
const sessionId = parseInt(filters_applied.session_id);  // "21" → 21
const classId = parseInt(student.class_id);              // "10" → 10

// Convert amounts to floats
const total = parseFloat(student.total);                 // "22000.00" → 22000.00
const fine = parseFloat(student.fine);                   // "0.00" → 0.00

// Handle integers (already numbers)
const paid = student.total_amount;                       // 0 (already integer)
const totalFine = student.total_fine;                    // 0 (already integer)
const discount = student.total_discount;                 // 0 (already integer)

// Handle null values
const middlename = student.middlename || '';             // null → ''

// Calculate balance
const balance = parseFloat(student.total) -
               student.total_amount +
               student.total_fine -
               student.total_discount;
```

---

## Support

For technical support or questions about this API:
- **Email:** support@smartschool.com
- **Documentation:** `/api/documentation/TYPE_WISE_BALANCE_REPORT_API_README.md`
- **API Controller:** `/api/application/controllers/Type_wise_balance_report_api.php`

---

## Changelog

### Version 1.0 (2025-10-10)
- Initial API documentation
- Filter endpoint with session, fee type, fee group, class, and section filters
- List endpoint for filter options
- Comprehensive examples and use cases
- Data type documentation and conversion guide

---

**Last Updated:** 2025-10-10
**API Version:** 1.0
**Documentation Version:** 1.0


