# Attendance Report API Documentation

## Overview

The Attendance Report API provides endpoints for retrieving detailed student attendance records with student information. This API returns comprehensive attendance data with filtering capabilities by class, section, and date range.

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

### 1. Filter Attendance Report

**Endpoint:** `POST /api/attendance-report/filter`

**Description:** Retrieves detailed student attendance records with optional filtering by class, section, and date range.

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
  "message": "Attendance report retrieved successfully",
  "filters_applied": {
    "class_id": [1],
    "section_id": [2],
    "from_date": "2025-10-01",
    "to_date": "2025-10-07",
    "session_id": 18
  },
  "total_records": 45,
  "data": [
    {
      "class_id": "1",
      "id": "50",
      "class": "Class 10",
      "section_id": "2",
      "section": "A",
      "admission_no": "2024001",
      "roll_no": "1",
      "firstname": "John",
      "middlename": "",
      "lastname": "Doe",
      "gender": "Male",
      "session_id": "18",
      "date": "2025-10-07",
      "total_attendance": "5"
    }
  ],
  "timestamp": "2025-10-07 10:30:00"
}
```

---

### 2. List All Attendance

**Endpoint:** `POST /api/attendance-report/list`

**Description:** Retrieves all student attendance records for the current session.

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
  "message": "Attendance report retrieved successfully",
  "session_id": 18,
  "total_records": 450,
  "data": [
    {
      "class_id": "1",
      "id": "50",
      "class": "Class 10",
      "section_id": "2",
      "section": "A",
      "admission_no": "2024001",
      "roll_no": "1",
      "firstname": "John",
      "middlename": "",
      "lastname": "Doe",
      "gender": "Male",
      "session_id": "18",
      "date": "2025-10-07",
      "total_attendance": "5"
    }
  ],
  "timestamp": "2025-10-07 10:30:00"
}
```

---

## Filter Parameters

All filter parameters are **optional**. If no parameters are provided, the API returns all attendance records for the current session.

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

- Empty parameters (`{}`) return all attendance records for current session
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

| Field | Type | Description |
|-------|------|-------------|
| `class_id` | string | Class ID |
| `id` | string | Student ID |
| `class` | string | Class name |
| `section_id` | string | Section ID |
| `section` | string | Section name |
| `admission_no` | string | Student admission number |
| `roll_no` | string | Student roll number |
| `firstname` | string | Student first name |
| `middlename` | string | Student middle name |
| `lastname` | string | Student last name |
| `gender` | string | Student gender |
| `session_id` | string | Session ID |
| `date` | string | Attendance date |
| `total_attendance` | string | Total attendance count |

---

## Usage Examples

### Example 1: Get All Attendance

```bash
curl -X POST "http://localhost/amt/api/attendance-report/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 2: Filter by Class and Section

```bash
curl -X POST "http://localhost/amt/api/attendance-report/filter" \
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
curl -X POST "http://localhost/amt/api/attendance-report/filter" \
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
curl -X POST "http://localhost/amt/api/attendance-report/filter" \
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
const getAttendanceReport = async (classId, sectionId, fromDate, toDate) => {
  try {
    const response = await fetch('http://localhost/amt/api/attendance-report/filter', {
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
      console.log('Attendance Report:', data.data);
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
getAttendanceReport(1, 2, '2025-10-01', '2025-10-07');
```

### PHP (cURL)

```php
<?php
function getAttendanceReport($classId = null, $sectionId = null, $fromDate = null, $toDate = null) {
    $url = 'http://localhost/amt/api/attendance-report/filter';
    
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
            return $result['data'];
        }
    }
    
    return null;
}

// Usage
$attendanceReport = getAttendanceReport(1, 2, '2025-10-01', '2025-10-07');
print_r($attendanceReport);
?>
```

### Python (Requests)

```python
import requests
import json

def get_attendance_report(class_id=None, section_id=None, from_date=None, to_date=None):
    url = 'http://localhost/amt/api/attendance-report/filter'
    
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
                return result['data']
            else:
                print(f"Error: {result['message']}")
                return None
    except Exception as e:
        print(f"Request failed: {str(e)}")
        return None

# Usage
attendance_report = get_attendance_report(1, 2, '2025-10-01', '2025-10-07')
print(attendance_report)
```

---

## Notes

1. **Date Format:** All dates should be in `YYYY-MM-DD` format.
2. **Session Handling:** If `session_id` is not provided, the API uses the current active session.
3. **Active Students Only:** The API returns data only for active students (`is_active = 'yes'`).
4. **Grouped by Student:** Results are grouped by student ID for easy analysis.
5. **Total Attendance:** The `total_attendance` field shows the count of attendance records for each student.

---

## Support

For issues or questions regarding this API, please contact the development team or refer to the main API documentation.

**Last Updated:** October 2025

