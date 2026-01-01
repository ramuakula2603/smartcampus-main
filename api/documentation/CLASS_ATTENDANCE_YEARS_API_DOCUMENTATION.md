# Class Attendance Years API Documentation

## Overview

The **Class Attendance Years API** provides endpoints to retrieve the list of available years with attendance records in the school management system. This API is designed to populate year dropdowns in the class attendance report interface.

**Base URL:** `http://localhost/amt/api`

**API Version:** 1.0.0  
**Created:** October 13, 2025  
**Status:** ✅ Production Ready

---

## Table of Contents

1. [Authentication](#authentication)
2. [Endpoints](#endpoints)
   - [List Available Years](#1-list-available-years)
   - [Get Years with Details](#2-get-years-with-details)
3. [Response Codes](#response-codes)
4. [Error Handling](#error-handling)
5. [Usage Examples](#usage-examples)
6. [Integration Guide](#integration-guide)
7. [Testing](#testing)

---

## Authentication

All API requests require authentication headers:

| Header | Value | Required | Description |
|--------|-------|----------|-------------|
| `Client-Service` | `smartschool` | Yes | Client identifier |
| `Auth-Key` | `schoolAdmin@` | Yes | Authentication key |
| `Content-Type` | `application/json` | Yes | Request content type |

---

## Endpoints

### 1. List Available Years

Retrieves a simple list of distinct years that have attendance records.

**Endpoint:** `POST /api/class-attendance-years/list`

#### Request

**Method:** `POST`  
**Content-Type:** `application/json`  
**Body:** Empty JSON object `{}`

```json
{}
```

#### Response

**Success Response (200 OK):**

```json
{
  "status": 1,
  "message": "Available attendance years retrieved successfully",
  "total_years": 3,
  "data": [
    {"year": "2025"},
    {"year": "2024"},
    {"year": "2023"}
  ],
  "timestamp": "2025-10-13 10:30:45"
}
```

**Response Fields:**

| Field | Type | Description |
|-------|------|-------------|
| `status` | Integer | 1 for success, 0 for failure |
| `message` | String | Response message |
| `total_years` | Integer | Total count of years available |
| `data` | Array | Array of year objects |
| `data[].year` | String | Year value (YYYY format) |
| `timestamp` | String | Server timestamp (Y-m-d H:i:s) |

---

### 2. Get Years with Details

Retrieves years with additional statistics including total records, earliest date, and latest date.

**Endpoint:** `POST /api/class-attendance-years/details`

#### Request

**Method:** `POST`  
**Content-Type:** `application/json`  
**Body:** Empty JSON object `{}`

```json
{}
```

#### Response

**Success Response (200 OK):**

```json
{
  "status": 1,
  "message": "Attendance years details retrieved successfully",
  "total_years": 3,
  "data": [
    {
      "year": "2025",
      "total_records": 15420,
      "earliest_date": "2025-01-01",
      "latest_date": "2025-10-13"
    },
    {
      "year": "2024",
      "total_records": 45680,
      "earliest_date": "2024-01-01",
      "latest_date": "2024-12-31"
    },
    {
      "year": "2023",
      "total_records": 44950,
      "earliest_date": "2023-01-01",
      "latest_date": "2023-12-31"
    }
  ],
  "timestamp": "2025-10-13 10:30:45"
}
```

**Response Fields:**

| Field | Type | Description |
|-------|------|-------------|
| `status` | Integer | 1 for success, 0 for failure |
| `message` | String | Response message |
| `total_years` | Integer | Total count of years available |
| `data` | Array | Array of year detail objects |
| `data[].year` | String | Year value (YYYY format) |
| `data[].total_records` | Integer | Total attendance records in this year |
| `data[].earliest_date` | String | Earliest attendance date (Y-m-d) |
| `data[].latest_date` | String | Latest attendance date (Y-m-d) |
| `timestamp` | String | Server timestamp (Y-m-d H:i:s) |

---

## Response Codes

| HTTP Code | Status | Description |
|-----------|--------|-------------|
| 200 | Success | Request completed successfully |
| 400 | Bad Request | Invalid request method (only POST allowed) |
| 401 | Unauthorized | Invalid or missing authentication headers |
| 500 | Internal Server Error | Server error occurred |

---

## Error Handling

### Common Error Responses

#### 1. Unauthorized Access (401)

```json
{
  "status": 0,
  "message": "Unauthorized access"
}
```

**Cause:** Invalid or missing authentication headers  
**Solution:** Verify `Client-Service` and `Auth-Key` headers

#### 2. Bad Request (400)

```json
{
  "status": 0,
  "message": "Bad request. Only POST method allowed."
}
```

**Cause:** Using GET or other HTTP methods  
**Solution:** Use POST method only

#### 3. Internal Server Error (500)

```json
{
  "status": 0,
  "message": "Internal server error occurred",
  "error": "Database connection failed",
  "data": []
}
```

**Cause:** Server-side error (database, configuration, etc.)  
**Solution:** Check server logs, verify database connectivity

---

## Usage Examples

### Example 1: cURL - List Years

```bash
curl -X POST http://localhost/amt/api/class-attendance-years/list \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 2: cURL - Get Years with Details

```bash
curl -X POST http://localhost/amt/api/class-attendance-years/details \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 3: JavaScript/Fetch

```javascript
async function getAttendanceYears() {
  try {
    const response = await fetch('http://localhost/amt/api/class-attendance-years/list', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Client-Service': 'smartschool',
        'Auth-Key': 'schoolAdmin@'
      },
      body: JSON.stringify({})
    });

    const data = await response.json();
    
    if (data.status === 1) {
      console.log('Available years:', data.data);
      // Populate dropdown
      const yearSelect = document.getElementById('year-select');
      data.data.forEach(item => {
        const option = document.createElement('option');
        option.value = item.year;
        option.textContent = item.year;
        yearSelect.appendChild(option);
      });
    } else {
      console.error('Error:', data.message);
    }
  } catch (error) {
    console.error('Network error:', error);
  }
}

// Call the function
getAttendanceYears();
```

### Example 4: PHP

```php
<?php
$url = 'http://localhost/amt/api/class-attendance-years/list';

$headers = array(
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array()));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$data = json_decode($response, true);

if ($http_code == 200 && $data['status'] == 1) {
    echo "Available years:\n";
    foreach ($data['data'] as $year) {
        echo "- {$year['year']}\n";
    }
} else {
    echo "Error: {$data['message']}\n";
}
?>
```

### Example 5: Python

```python
import requests
import json

url = 'http://localhost/amt/api/class-attendance-years/list'

headers = {
    'Content-Type': 'application/json',
    'Client-Service': 'smartschool',
    'Auth-Key': 'schoolAdmin@'
}

response = requests.post(url, headers=headers, json={})
data = response.json()

if response.status_code == 200 and data['status'] == 1:
    print(f"Available years: {data['total_years']}")
    for year in data['data']:
        print(f"- {year['year']}")
else:
    print(f"Error: {data['message']}")
```

---

## Integration Guide

### Step 1: Populate Year Dropdown

When loading the attendance report page, call the `/list` endpoint to populate the year dropdown:

```javascript
// On page load
document.addEventListener('DOMContentLoaded', function() {
    loadYearDropdown();
});

async function loadYearDropdown() {
    const response = await fetch('http://localhost/amt/api/class-attendance-years/list', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Client-Service': 'smartschool',
            'Auth-Key': 'schoolAdmin@'
        },
        body: JSON.stringify({})
    });

    const data = await response.json();
    
    if (data.status === 1) {
        const select = document.getElementById('year');
        data.data.forEach(item => {
            const option = new Option(item.year, item.year);
            select.add(option);
        });
        
        // Set current year as default
        const currentYear = new Date().getFullYear().toString();
        select.value = currentYear;
    }
}
```

### Step 2: Show Year Statistics (Optional)

Use the `/details` endpoint to show statistics:

```javascript
async function showYearStatistics() {
    const response = await fetch('http://localhost/amt/api/class-attendance-years/details', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Client-Service': 'smartschool',
            'Auth-Key': 'schoolAdmin@'
        },
        body: JSON.stringify({})
    });

    const data = await response.json();
    
    if (data.status === 1) {
        data.data.forEach(year => {
            console.log(`Year ${year.year}:`);
            console.log(`  Records: ${year.total_records}`);
            console.log(`  Date Range: ${year.earliest_date} to ${year.latest_date}`);
        });
    }
}
```

---

## Testing

### Test Script (PHP)

Create `test_class_attendance_years_api.php`:

```php
<?php
/**
 * Test Script for Class Attendance Years API
 */

$base_url = 'http://localhost/amt/api';
$headers = array(
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
);

function call_api($url, $headers) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array()));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return array(
        'http_code' => $http_code,
        'response' => json_decode($response, true)
    );
}

echo "=== CLASS ATTENDANCE YEARS API TESTS ===\n\n";

// Test 1: List Years
echo "Test 1: List Available Years\n";
$result = call_api("{$base_url}/class-attendance-years/list", $headers);
if ($result['http_code'] == 200 && $result['response']['status'] == 1) {
    echo "✓ SUCCESS\n";
    echo "  Total Years: {$result['response']['total_years']}\n";
    foreach ($result['response']['data'] as $year) {
        echo "  - {$year['year']}\n";
    }
} else {
    echo "✗ FAILED\n";
}
echo "\n";

// Test 2: Get Years with Details
echo "Test 2: Get Years with Details\n";
$result = call_api("{$base_url}/class-attendance-years/details", $headers);
if ($result['http_code'] == 200 && $result['response']['status'] == 1) {
    echo "✓ SUCCESS\n";
    echo "  Total Years: {$result['response']['total_years']}\n";
    foreach ($result['response']['data'] as $year) {
        echo "  Year {$year['year']}:\n";
        echo "    Records: {$year['total_records']}\n";
        echo "    Date Range: {$year['earliest_date']} to {$year['latest_date']}\n";
    }
} else {
    echo "✗ FAILED\n";
}
echo "\n";

echo "=== ALL TESTS COMPLETED ===\n";
?>
```

Run the test:
```bash
C:\xampp\php\php.exe test_class_attendance_years_api.php
```

---

## Use Cases

### 1. Year Dropdown Population
Load available years when the attendance report page loads to populate the year filter dropdown.

### 2. Date Range Validation
Use the earliest and latest dates to validate user-selected date ranges.

### 3. Data Availability Check
Check if attendance data exists for a specific year before allowing report generation.

### 4. Statistics Dashboard
Display year-wise attendance record counts in a dashboard or statistics panel.

---

## Related APIs

- **Class Attendance Report API** - `/api/class-attendance-report/filter`
- **Daily Attendance Report API** - `/api/daily-attendance-report/filter`
- **Staff Attendance Report API** - `/api/staff-attendance-report/filter`

---

## Technical Details

### Controller
- **File:** `api/application/controllers/Class_attendance_years_api.php`
- **Class:** `Class_attendance_years_api`
- **Extends:** `CI_Controller`

### Model
- **File:** `api/application/models/Stuattendence_model.php`
- **Methods:**
  - `getAttendanceYears()` - Get simple year list
  - `getAttendanceYearsWithDetails()` - Get years with statistics

### Routes
```php
// Class Attendance Years API Routes
$route['class-attendance-years/list']['POST'] = 'class_attendance_years_api/list';
$route['class-attendance-years/details']['POST'] = 'class_attendance_years_api/details';
```

### Database Tables
- `student_attendences` - Stores all student attendance records

---

## Best Practices

1. **Cache Years List**
   - Cache the years list on the client side to reduce API calls
   - Refresh cache when new attendance data is added

2. **Error Handling**
   - Always check the `status` field before processing data
   - Implement fallback UI if API fails

3. **Year Selection**
   - Default to current year for better UX
   - Show years in descending order (newest first)

4. **Performance**
   - The API uses indexed queries for fast performance
   - Results are ordered by year DESC

---

## Troubleshooting

### Issue: Empty Years List

**Symptom:**
```json
{
  "status": 1,
  "total_years": 0,
  "data": []
}
```

**Causes:**
- No attendance records in database
- All attendance dates are NULL

**Solution:**
- Verify attendance data exists in `student_attendences` table
- Check if `date` field is populated

### Issue: Unauthorized Error

**Symptom:**
```json
{
  "status": 0,
  "message": "Unauthorized access"
}
```

**Causes:**
- Missing or incorrect authentication headers

**Solution:**
- Verify headers: `Client-Service: smartschool` and `Auth-Key: schoolAdmin@`

---

## Changelog

### Version 1.0.0 (October 13, 2025)
- ✅ Initial release
- ✅ `/list` endpoint for simple year list
- ✅ `/details` endpoint for years with statistics
- ✅ Complete documentation
- ✅ Test scripts included

---

## Support

For issues or questions:
1. Check the troubleshooting section
2. Review server error logs
3. Verify database connectivity
4. Test with provided test scripts

---

**API Status:** ✅ Ready for Production  
**Last Updated:** October 13, 2025  
**Version:** 1.0.0
