# Daily Attendance Report API Documentation

## Overview

The Daily Attendance Report API provides endpoints for retrieving daily attendance statistics grouped by class and section. This API returns detailed information about attendance types (present, absent, late, excuse, half_day) with summary statistics and percentages.

**Base URL:** `http://localhost/amt/api`

**Version:** 1.0.0

**Authentication Required:** Yes

---

## Table of Contents

1. [Authentication](#authentication)
2. [Endpoints](#endpoints)
3. [Filter Parameters](#filter-parameters)
4. [Response Format](#response-format)
5. [Usage Examples](#usage-examples)
6. [Error Handling](#error-handling)
7. [Code Examples](#code-examples)

---

## Authentication

All API requests require authentication headers:

- **Client-Service:** `smartschool`
- **Auth-Key:** `schoolAdmin@`

---

## Endpoints

### 1. Filter Daily Attendance Report

**Endpoint:** `POST /api/daily-attendance-report/filter`

**Description:** Retrieves daily attendance statistics with optional filtering by date, date range, and session.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Request Body:**
```json
{
  "date": "2025-10-07",
  "session_id": 18
}
```

**Response:**
```json
{
  "status": 1,
  "message": "Daily attendance report retrieved successfully",
  "filters_applied": {
    "date": "2025-10-07",
    "from_date": null,
    "to_date": null,
    "session_id": 18
  },
  "total_records": 12,
  "summary": {
    "total_students": 450,
    "total_present": 420,
    "total_absent": 30,
    "overall_present_percentage": "93.33%",
    "overall_absent_percentage": "6.67%"
  },
  "data": [
    {
      "class_id": "1",
      "class_name": "Class 10",
      "section_id": "1",
      "section_name": "A",
      "present": "38",
      "excuse": "2",
      "absent": "3",
      "late": "1",
      "half_day": "1",
      "total_student": "45",
      "total_present": "42",
      "present_percent": "93%",
      "absent_percent": "7%"
    }
  ],
  "timestamp": "2025-10-07 10:30:00"
}
```

---

### 2. List Today's Attendance

**Endpoint:** `POST /api/daily-attendance-report/list`

**Description:** Retrieves today's attendance statistics for the current session.

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

**Response:**
```json
{
  "status": 1,
  "message": "Daily attendance report retrieved successfully",
  "date": "2025-10-07",
  "session_id": 18,
  "total_records": 12,
  "summary": {
    "total_students": 450,
    "total_present": 420,
    "total_absent": 30,
    "overall_present_percentage": "93.33%",
    "overall_absent_percentage": "6.67%"
  },
  "data": [
    {
      "class_id": "1",
      "class_name": "Class 10",
      "section_id": "1",
      "section_name": "A",
      "present": "38",
      "excuse": "2",
      "absent": "3",
      "late": "1",
      "half_day": "1",
      "total_student": "45",
      "total_present": "42",
      "present_percent": "93%",
      "absent_percent": "7%"
    }
  ],
  "timestamp": "2025-10-07 10:30:00"
}
```

---

## Filter Parameters

All filter parameters are **optional**. If no parameters are provided, the API returns today's attendance.

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `date` | string | Specific date for attendance (YYYY-MM-DD) | `"2025-10-07"` |
| `from_date` | string | Start date for date range filter (YYYY-MM-DD) | `"2025-10-01"` |
| `to_date` | string | End date for date range filter (YYYY-MM-DD) | `"2025-10-07"` |
| `session_id` | integer | Session ID (defaults to current session) | `18` |

### Date Filtering Options

1. **Specific Date:** Use `date` parameter
2. **Date Range:** Use `from_date` and `to_date` parameters
3. **From Date Only:** Use `from_date` parameter
4. **To Date Only:** Use `to_date` parameter

**Note:** If `date` is provided, it takes precedence over `from_date` and `to_date`.

### Graceful Null/Empty Handling

- Empty parameters (`{}`) return today's attendance
- `null` values are treated as "no filter"

---

## Response Format

### Success Response

```json
{
  "status": 1,
  "message": "Success message",
  "filters_applied": { },
  "total_records": 0,
  "summary": { },
  "data": [ ],
  "timestamp": "YYYY-MM-DD HH:MM:SS"
}
```

### Error Response

```json
{
  "status": 0,
  "message": "Error message",
  "error": "Detailed error information",
  "data": null
}
```

---

## Attendance Types

| Type ID | Type Name | Description |
|---------|-----------|-------------|
| 1 | Present | Student is present |
| 2 | Excuse | Student is excused |
| 3 | Late | Student arrived late |
| 4 | Absent | Student is absent |
| 5 | Holiday | Holiday |
| 6 | Half Day | Student attended half day |

**Total Present Calculation:** Present + Excuse + Late + Half Day

---

## Usage Examples

### Example 1: Get Today's Attendance

```bash
curl -X POST "http://localhost/amt/api/daily-attendance-report/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 2: Get Attendance for Specific Date

```bash
curl -X POST "http://localhost/amt/api/daily-attendance-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "date": "2025-10-07"
  }'
```

### Example 3: Get Attendance for Date Range

```bash
curl -X POST "http://localhost/amt/api/daily-attendance-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "from_date": "2025-10-01",
    "to_date": "2025-10-07"
  }'
```

### Example 4: Get Attendance from Specific Date

```bash
curl -X POST "http://localhost/amt/api/daily-attendance-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "from_date": "2025-10-01"
  }'
```

---

## Error Handling

### Common Error Codes

| Status Code | Description |
|-------------|-------------|
| 400 | Bad Request - Invalid request method |
| 401 | Unauthorized - Invalid or missing authentication |
| 500 | Internal Server Error - Server-side error |

---

## Code Examples

### JavaScript (Fetch API)

```javascript
const getDailyAttendanceReport = async (date, fromDate, toDate) => {
  try {
    const response = await fetch('http://localhost/amt/api/daily-attendance-report/filter', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Client-Service': 'smartschool',
        'Auth-Key': 'schoolAdmin@'
      },
      body: JSON.stringify({
        date: date,
        from_date: fromDate,
        to_date: toDate
      })
    });
    
    const data = await response.json();
    
    if (data.status === 1) {
      console.log('Daily Attendance Report:', data.data);
      console.log('Summary:', data.summary);
      console.log('Total Records:', data.total_records);
      return data;
    } else {
      console.error('Error:', data.message);
      return null;
    }
  } catch (error) {
    console.error('Request failed:', error);
    return null;
  }
};

// Usage
getDailyAttendanceReport('2025-10-07', null, null);
```

### PHP (cURL)

```php
<?php
function getDailyAttendanceReport($date = null, $fromDate = null, $toDate = null) {
    $url = 'http://localhost/amt/api/daily-attendance-report/filter';
    
    $data = array();
    if ($date !== null) $data['date'] = $date;
    if ($fromDate !== null) $data['from_date'] = $fromDate;
    if ($toDate !== null) $data['to_date'] = $toDate;
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Client-Service: smartschool',
        'Auth-Key: schoolAdmin@'
    ));
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        $result = json_decode($response, true);
        if ($result['status'] === 1) {
            return $result['data'];
        }
    }
    
    return null;
}

// Usage
$attendanceReport = getDailyAttendanceReport('2025-10-07');
print_r($attendanceReport);
?>
```

### Python (Requests)

```python
import requests
import json

def get_daily_attendance_report(date=None, from_date=None, to_date=None):
    url = 'http://localhost/amt/api/daily-attendance-report/filter'
    
    headers = {
        'Content-Type': 'application/json',
        'Client-Service': 'smartschool',
        'Auth-Key': 'schoolAdmin@'
    }
    
    data = {}
    if date is not None:
        data['date'] = date
    if from_date is not None:
        data['from_date'] = from_date
    if to_date is not None:
        data['to_date'] = to_date
    
    try:
        response = requests.post(url, headers=headers, json=data)
        
        if response.status_code == 200:
            result = response.json()
            if result['status'] == 1:
                print(f"Total Records: {result['total_records']}")
                print(f"Summary: {result['summary']}")
                return result['data']
            else:
                print(f"Error: {result['message']}")
                return None
    except Exception as e:
        print(f"Request failed: {str(e)}")
        return None

# Usage
attendance_report = get_daily_attendance_report('2025-10-07')
print(attendance_report)
```

---

## Notes

1. **Date Format:** All dates should be in `YYYY-MM-DD` format.
2. **Session Handling:** If `session_id` is not provided, the API uses the current active session.
3. **Grouped Data:** Results are grouped by class and section for easy analysis.
4. **Percentage Calculation:** Present and absent percentages are automatically calculated.
5. **Summary Statistics:** The API provides overall summary statistics across all classes.

---

## Support

For issues or questions regarding this API, please contact the development team or refer to the main API documentation.

**Last Updated:** October 2025

