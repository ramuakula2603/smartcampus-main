# Online Exam Attend Report API Documentation

## Overview

The Online Exam Attend Report API provides endpoints for retrieving online exam attendance reports showing which students have attempted which online exams. This API returns detailed information about student participation, exam details, and attempt statistics.

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

### 1. Filter Online Exam Attend Report

**Endpoint:** `POST /api/online-exam-attend-report/filter`

**Description:** Retrieves online exam attendance report data with optional date range filtering.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Request Body:**
```json
{
  "from_date": "2025-01-01",
  "to_date": "2025-10-07"
}
```

**Response:**
```json
{
  "status": 1,
  "message": "Online exam attend report retrieved successfully",
  "filters_applied": {
    "from_date": "2025-01-01",
    "to_date": "2025-10-07"
  },
  "total_records": 50,
  "data": [
    {
      "student_session_id": "100",
      "student_id": "50",
      "admission_no": "2024001",
      "name": "John Doe",
      "firstname": "John",
      "middlename": "",
      "lastname": "Doe",
      "class_id": "10",
      "class": "Class 10",
      "section_id": "1",
      "section": "A",
      "exams": [
        {
          "exam_id": "10",
          "exam_name": "Mathematics Quiz",
          "attempt": "1",
          "exam_from": "2025-10-01 09:00:00",
          "exam_to": "2025-10-01 10:00:00",
          "duration": "60",
          "passing_percentage": "40",
          "is_active": "1",
          "publish_result": "1"
        },
        {
          "exam_id": "11",
          "exam_name": "Science Test",
          "attempt": "1",
          "exam_from": "2025-10-05 10:00:00",
          "exam_to": "2025-10-05 11:00:00",
          "duration": "60",
          "passing_percentage": "40",
          "is_active": "1",
          "publish_result": "1"
        }
      ],
      "total_exams_attempted": 2
    }
  ],
  "timestamp": "2025-10-07 10:30:00"
}
```

---

### 2. List Current Year's Attendance

**Endpoint:** `POST /api/online-exam-attend-report/list`

**Description:** Retrieves online exam attendance report for the current year.

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
  "message": "Online exam attend report retrieved successfully",
  "year": 2025,
  "total_records": 50,
  "data": [
    {
      "student_session_id": "100",
      "student_id": "50",
      "admission_no": "2024001",
      "name": "John Doe",
      "firstname": "John",
      "middlename": "",
      "lastname": "Doe",
      "class_id": "10",
      "class": "Class 10",
      "section_id": "1",
      "section": "A",
      "exams": [
        {
          "exam_id": "10",
          "exam_name": "Mathematics Quiz",
          "attempt": "1",
          "exam_from": "2025-10-01 09:00:00",
          "exam_to": "2025-10-01 10:00:00",
          "duration": "60",
          "passing_percentage": "40",
          "is_active": "1",
          "publish_result": "1"
        }
      ],
      "total_exams_attempted": 1
    }
  ],
  "timestamp": "2025-10-07 10:30:00"
}
```

---

## Filter Parameters

All filter parameters are **optional**. If no parameters are provided, the API returns current year's data.

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `from_date` | string | Start date for date range filter (YYYY-MM-DD) | `"2025-01-01"` |
| `to_date` | string | End date for date range filter (YYYY-MM-DD) | `"2025-10-07"` |

### Date Range Filtering

- If both `from_date` and `to_date` are provided, returns data between those dates
- If only `from_date` is provided, returns data from that date onwards
- If only `to_date` is provided, returns data up to that date
- If no dates are provided, defaults to current year (January 1 to December 31)

### Graceful Null/Empty Handling

- Empty parameters (`{}`) return current year's data
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

## Usage Examples

### Example 1: Get Current Year's Attendance

```bash
curl -X POST "http://localhost/amt/api/online-exam-attend-report/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 2: Get Attendance for Date Range

```bash
curl -X POST "http://localhost/amt/api/online-exam-attend-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "from_date": "2025-01-01",
    "to_date": "2025-10-07"
  }'
```

### Example 3: Get Attendance from Specific Date

```bash
curl -X POST "http://localhost/amt/api/online-exam-attend-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "from_date": "2025-09-01"
  }'
```

### Example 4: Get Attendance for Specific Month

```bash
curl -X POST "http://localhost/amt/api/online-exam-attend-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "from_date": "2025-10-01",
    "to_date": "2025-10-31"
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
const getOnlineExamAttendReport = async (fromDate, toDate) => {
  try {
    const response = await fetch('http://localhost/amt/api/online-exam-attend-report/filter', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Client-Service': 'smartschool',
        'Auth-Key': 'schoolAdmin@'
      },
      body: JSON.stringify({
        from_date: fromDate,
        to_date: toDate
      })
    });
    
    const data = await response.json();
    
    if (data.status === 1) {
      console.log('Online Exam Attend Report:', data.data);
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
getOnlineExamAttendReport('2025-01-01', '2025-10-07');
```

### PHP (cURL)

```php
<?php
function getOnlineExamAttendReport($fromDate = null, $toDate = null) {
    $url = 'http://localhost/amt/api/online-exam-attend-report/filter';
    
    $data = array();
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
$attendReport = getOnlineExamAttendReport('2025-01-01', '2025-10-07');
print_r($attendReport);
?>
```

### Python (Requests)

```python
import requests
import json

def get_online_exam_attend_report(from_date=None, to_date=None):
    url = 'http://localhost/amt/api/online-exam-attend-report/filter'
    
    headers = {
        'Content-Type': 'application/json',
        'Client-Service': 'smartschool',
        'Auth-Key': 'schoolAdmin@'
    }
    
    data = {}
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
attend_report = get_online_exam_attend_report('2025-01-01', '2025-10-07')
print(attend_report)
```

---

## Notes

1. **Date Format:** All dates should be in `YYYY-MM-DD` format.
2. **Current Year Default:** If no date parameters are provided, the API defaults to the current year.
3. **Student Details:** Each record includes complete student information with class and section.
4. **Exam Details:** Each exam includes name, attempt, duration, passing percentage, and status.
5. **Total Count:** The `total_exams_attempted` field shows how many exams each student has attempted.

---

## Support

For issues or questions regarding this API, please contact the development team or refer to the main API documentation.

**Last Updated:** October 2025

