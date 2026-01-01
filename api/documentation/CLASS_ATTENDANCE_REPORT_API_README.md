# Class Attendance Report API Documentation

## Overview

The Class Attendance Report API provides endpoints for retrieving class-wise attendance statistics with detailed breakdowns by attendance type. This API returns comprehensive attendance data grouped by class and section with summary statistics.

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

### 1. Filter Class Attendance Report

**Endpoint:** `POST /api/class-attendance-report/filter`

**Description:** Retrieves class-wise attendance statistics with optional filtering by class, section, and date range.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Request Body:**
```json
{
  "class_id": 1,
  "section_id": 2,
  "from_date": "2025-10-01",
  "to_date": "2025-10-07",
  "session_id": 18
}
```

**Response:**
```json
{
  "status": 1,
  "message": "Class attendance report retrieved successfully",
  "filters_applied": {
    "class_id": [1],
    "section_id": [2],
    "from_date": "2025-10-01",
    "to_date": "2025-10-07",
    "session_id": 18
  },
  "total_records": 12,
  "summary": {
    "total_classes": 12,
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
      "section_id": "2",
      "section_name": "A",
      "total_students": "45",
      "present_count": "38",
      "excuse_count": "2",
      "late_count": "1",
      "half_day_count": "1",
      "absent_count": "3",
      "total_present": "42",
      "present_percentage": "93.33%",
      "absent_percentage": "6.67%",
      "date_range": "2025-10-01 to 2025-10-07",
      "total_days": 7
    }
  ],
  "timestamp": "2025-10-07 10:30:00"
}
```

---

### 2. List All Class Attendance

**Endpoint:** `POST /api/class-attendance-report/list`

**Description:** Retrieves class-wise attendance statistics for all classes in the current session.

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
  "message": "Class attendance report retrieved successfully",
  "session_id": 18,
  "total_records": 12,
  "summary": {
    "total_classes": 12,
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
      "section_id": "2",
      "section_name": "A",
      "total_students": "45",
      "present_count": "38",
      "excuse_count": "2",
      "late_count": "1",
      "half_day_count": "1",
      "absent_count": "3",
      "total_present": "42",
      "present_percentage": "93.33%",
      "absent_percentage": "6.67%"
    }
  ],
  "timestamp": "2025-10-07 10:30:00"
}
```

---

## Filter Parameters

All filter parameters are **optional**. If no parameters are provided, the API returns all class attendance data for the current session.

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `class_id` | integer or array | Single or multiple class IDs | `1` or `[1, 2, 3]` |
| `section_id` | integer or array | Single or multiple section IDs | `2` or `[1, 2]` |
| `from_date` | string | Start date for date range filter (YYYY-MM-DD) | `"2025-10-01"` |
| `to_date` | string | End date for date range filter (YYYY-MM-DD) | `"2025-10-07"` |
| `session_id` | integer | Session ID (defaults to current session) | `18` |

### Multi-Select Support

The API supports multi-select for `class_id` and `section_id` parameters:

```json
{
  "class_id": [1, 2, 3],
  "section_id": [1, 2]
}
```

### Date Range Filtering

- If both `from_date` and `to_date` are provided, returns data between those dates
- If only `from_date` is provided, returns data from that date onwards
- If only `to_date` is provided, returns data up to that date
- If no dates are provided, returns all attendance records

### Graceful Null/Empty Handling

- Empty parameters (`{}`) return all class attendance data for current session
- `null` values are treated as "no filter"
- Empty arrays (`[]`) are treated as "no filter"

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

## Response Fields

### Data Fields

| Field | Type | Description |
|-------|------|-------------|
| `class_id` | string | Class ID |
| `class_name` | string | Class name |
| `section_id` | string | Section ID |
| `section_name` | string | Section name |
| `total_students` | string | Total number of students in class |
| `present_count` | string | Number of students marked present |
| `excuse_count` | string | Number of students excused |
| `late_count` | string | Number of students marked late |
| `half_day_count` | string | Number of students marked half day |
| `absent_count` | string | Number of students marked absent |
| `total_present` | string | Total present (present + excuse + late + half_day) |
| `present_percentage` | string | Percentage of students present |
| `absent_percentage` | string | Percentage of students absent |
| `date_range` | string | Date range for the report (if filtered) |
| `total_days` | integer | Number of days in the date range (if filtered) |

### Summary Fields

| Field | Type | Description |
|-------|------|-------------|
| `total_classes` | integer | Total number of classes |
| `total_students` | integer | Total number of students across all classes |
| `total_present` | integer | Total number of present students |
| `total_absent` | integer | Total number of absent students |
| `overall_present_percentage` | string | Overall present percentage |
| `overall_absent_percentage` | string | Overall absent percentage |

---

## Usage Examples

### Example 1: Get All Class Attendance

```bash
curl -X POST "http://localhost/amt/api/class-attendance-report/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 2: Filter by Class and Section

```bash
curl -X POST "http://localhost/amt/api/class-attendance-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "class_id": 1,
    "section_id": 2
  }'
```

### Example 3: Filter by Date Range

```bash
curl -X POST "http://localhost/amt/api/class-attendance-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "from_date": "2025-10-01",
    "to_date": "2025-10-07"
  }'
```

### Example 4: Filter by Multiple Classes and Date Range

```bash
curl -X POST "http://localhost/amt/api/class-attendance-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "class_id": [1, 2, 3],
    "section_id": [1, 2],
    "from_date": "2025-10-01",
    "to_date": "2025-10-07"
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
const getClassAttendanceReport = async (classId, sectionId, fromDate, toDate) => {
  try {
    const response = await fetch('http://localhost/amt/api/class-attendance-report/filter', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Client-Service': 'smartschool',
        'Auth-Key': 'schoolAdmin@'
      },
      body: JSON.stringify({
        class_id: classId,
        section_id: sectionId,
        from_date: fromDate,
        to_date: toDate
      })
    });
    
    const data = await response.json();
    
    if (data.status === 1) {
      console.log('Class Attendance Report:', data.data);
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
getClassAttendanceReport(1, 2, '2025-10-01', '2025-10-07');
```

### PHP (cURL)

```php
<?php
function getClassAttendanceReport($classId = null, $sectionId = null, $fromDate = null, $toDate = null) {
    $url = 'http://localhost/amt/api/class-attendance-report/filter';
    
    $data = array();
    if ($classId !== null) $data['class_id'] = $classId;
    if ($sectionId !== null) $data['section_id'] = $sectionId;
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
            return $result;
        }
    }
    
    return null;
}

// Usage
$classAttendanceReport = getClassAttendanceReport(1, 2, '2025-10-01', '2025-10-07');
print_r($classAttendanceReport);
?>
```

### Python (Requests)

```python
import requests
import json

def get_class_attendance_report(class_id=None, section_id=None, from_date=None, to_date=None):
    url = 'http://localhost/amt/api/class-attendance-report/filter'
    
    headers = {
        'Content-Type': 'application/json',
        'Client-Service': 'smartschool',
        'Auth-Key': 'schoolAdmin@'
    }
    
    data = {}
    if class_id is not None:
        data['class_id'] = class_id
    if section_id is not None:
        data['section_id'] = section_id
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
                return result
            else:
                print(f"Error: {result['message']}")
                return None
    except Exception as e:
        print(f"Request failed: {str(e)}")
        return None

# Usage
class_attendance_report = get_class_attendance_report(1, 2, '2025-10-01', '2025-10-07')
print(class_attendance_report)
```

---

## Notes

1. **Date Format:** All dates should be in `YYYY-MM-DD` format.
2. **Session Handling:** If `session_id` is not provided, the API uses the current active session.
3. **Grouped by Class:** Results are grouped by class and section for easy analysis.
4. **Percentage Calculation:** Present and absent percentages are automatically calculated.
5. **Summary Statistics:** The API provides overall summary statistics across all classes.
6. **Total Present:** Includes present, excuse, late, and half_day attendance types.

---

## Support

For issues or questions regarding this API, please contact the development team or refer to the main API documentation.

**Last Updated:** October 2025

