# Biometric Attendance Log Report API Documentation

## Overview

The Biometric Attendance Log Report API provides endpoints for retrieving biometric attendance log records with student details. This API returns detailed information about biometric attendance entries with pagination support for large datasets.

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

### 1. Filter Biometric Attendance Log Report

**Endpoint:** `POST /api/biometric-attlog-report/filter`

**Description:** Retrieves biometric attendance log records with optional filtering by date range, student, and pagination.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Request Body:**
```json
{
  "from_date": "2025-10-01",
  "to_date": "2025-10-07",
  "student_id": 50,
  "limit": 50,
  "offset": 0
}
```

**Response:**
```json
{
  "status": 1,
  "message": "Biometric attendance log report retrieved successfully",
  "filters_applied": {
    "from_date": "2025-10-01",
    "to_date": "2025-10-07",
    "student_id": [50],
    "limit": 50,
    "offset": 0
  },
  "total_records": 150,
  "returned_records": 50,
  "data": [
    {
      "id": "1000",
      "student_session_id": "100",
      "date": "2025-10-07",
      "attendence_type_id": "1",
      "remark": "",
      "biometric_attendence": "1",
      "biometric_device_data": "Device001",
      "name": "John Doe",
      "firstname": "John",
      "middlename": "",
      "lastname": "Doe",
      "roll_no": "1",
      "admission_no": "2024001",
      "class": "Class 10",
      "section": "A"
    }
  ],
  "timestamp": "2025-10-07 10:30:00"
}
```

---

### 2. List Recent Biometric Logs

**Endpoint:** `POST /api/biometric-attlog-report/list`

**Description:** Retrieves the most recent 100 biometric attendance log records.

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
  "message": "Biometric attendance log report retrieved successfully",
  "total_records": 500,
  "returned_records": 100,
  "data": [
    {
      "id": "1000",
      "student_session_id": "100",
      "date": "2025-10-07",
      "attendence_type_id": "1",
      "remark": "",
      "biometric_attendence": "1",
      "biometric_device_data": "Device001",
      "name": "John Doe",
      "firstname": "John",
      "middlename": "",
      "lastname": "Doe",
      "roll_no": "1",
      "admission_no": "2024001",
      "class": "Class 10",
      "section": "A"
    }
  ],
  "timestamp": "2025-10-07 10:30:00"
}
```

---

## Filter Parameters

All filter parameters are **optional**. If no parameters are provided, the API returns the most recent 100 records.

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `from_date` | string | Start date for date range filter (YYYY-MM-DD) | `"2025-10-01"` |
| `to_date` | string | End date for date range filter (YYYY-MM-DD) | `"2025-10-07"` |
| `student_id` | integer or array | Single or multiple student IDs | `50` or `[50, 51, 52]` |
| `limit` | integer | Number of records to return (default: 100) | `50` |
| `offset` | integer | Number of records to skip (default: 0) | `0` |

### Pagination

The API supports pagination for handling large datasets:

- **limit:** Number of records per page (default: 100)
- **offset:** Starting position (default: 0)

**Example for Page 2 with 50 records per page:**
```json
{
  "limit": 50,
  "offset": 50
}
```

### Multi-Select Support

The API supports multi-select for `student_id` parameter:

```json
{
  "student_id": [50, 51, 52, 53]
}
```

### Graceful Null/Empty Handling

- Empty parameters (`{}`) return the most recent 100 records
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
  "returned_records": 0,
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
| `id` | string | Attendance record ID |
| `student_session_id` | string | Student session ID |
| `date` | string | Attendance date |
| `attendence_type_id` | string | Attendance type ID (1=Present, 2=Excuse, 3=Late, 4=Absent, 6=Half Day) |
| `remark` | string | Attendance remark |
| `biometric_attendence` | string | Biometric flag (1=biometric, 0=manual) |
| `biometric_device_data` | string | Biometric device information |
| `name` | string | Student full name |
| `firstname` | string | Student first name |
| `middlename` | string | Student middle name |
| `lastname` | string | Student last name |
| `roll_no` | string | Student roll number |
| `admission_no` | string | Student admission number |
| `class` | string | Class name |
| `section` | string | Section name |

---

## Usage Examples

### Example 1: Get Recent Biometric Logs

```bash
curl -X POST "http://localhost/amt/api/biometric-attlog-report/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 2: Get Logs for Date Range

```bash
curl -X POST "http://localhost/amt/api/biometric-attlog-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "from_date": "2025-10-01",
    "to_date": "2025-10-07"
  }'
```

### Example 3: Get Logs for Specific Student

```bash
curl -X POST "http://localhost/amt/api/biometric-attlog-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "student_id": 50
  }'
```

### Example 4: Get Logs with Pagination

```bash
curl -X POST "http://localhost/amt/api/biometric-attlog-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "from_date": "2025-10-01",
    "to_date": "2025-10-07",
    "limit": 50,
    "offset": 0
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
const getBiometricAttlogReport = async (fromDate, toDate, studentId, limit = 100, offset = 0) => {
  try {
    const response = await fetch('http://localhost/amt/api/biometric-attlog-report/filter', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Client-Service': 'smartschool',
        'Auth-Key': 'schoolAdmin@'
      },
      body: JSON.stringify({
        from_date: fromDate,
        to_date: toDate,
        student_id: studentId,
        limit: limit,
        offset: offset
      })
    });
    
    const data = await response.json();
    
    if (data.status === 1) {
      console.log('Biometric Attendance Log:', data.data);
      console.log('Total Records:', data.total_records);
      console.log('Returned Records:', data.returned_records);
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
getBiometricAttlogReport('2025-10-01', '2025-10-07', null, 50, 0);
```

### PHP (cURL)

```php
<?php
function getBiometricAttlogReport($fromDate = null, $toDate = null, $studentId = null, $limit = 100, $offset = 0) {
    $url = 'http://localhost/amt/api/biometric-attlog-report/filter';
    
    $data = array(
        'limit' => $limit,
        'offset' => $offset
    );
    if ($fromDate !== null) $data['from_date'] = $fromDate;
    if ($toDate !== null) $data['to_date'] = $toDate;
    if ($studentId !== null) $data['student_id'] = $studentId;
    
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
$attlogReport = getBiometricAttlogReport('2025-10-01', '2025-10-07', null, 50, 0);
print_r($attlogReport);
?>
```

### Python (Requests)

```python
import requests
import json

def get_biometric_attlog_report(from_date=None, to_date=None, student_id=None, limit=100, offset=0):
    url = 'http://localhost/amt/api/biometric-attlog-report/filter'
    
    headers = {
        'Content-Type': 'application/json',
        'Client-Service': 'smartschool',
        'Auth-Key': 'schoolAdmin@'
    }
    
    data = {
        'limit': limit,
        'offset': offset
    }
    if from_date is not None:
        data['from_date'] = from_date
    if to_date is not None:
        data['to_date'] = to_date
    if student_id is not None:
        data['student_id'] = student_id
    
    try:
        response = requests.post(url, headers=headers, json=data)
        
        if response.status_code == 200:
            result = response.json()
            if result['status'] == 1:
                print(f"Total Records: {result['total_records']}")
                print(f"Returned Records: {result['returned_records']}")
                return result['data']
            else:
                print(f"Error: {result['message']}")
                return None
    except Exception as e:
        print(f"Request failed: {str(e)}")
        return None

# Usage
attlog_report = get_biometric_attlog_report('2025-10-01', '2025-10-07', None, 50, 0)
print(attlog_report)
```

---

## Notes

1. **Date Format:** All dates should be in `YYYY-MM-DD` format.
2. **Biometric Only:** This API returns only biometric attendance records (`biometric_attendence = 1`).
3. **Pagination:** Use `limit` and `offset` for handling large datasets efficiently.
4. **Multi-Select:** The `student_id` parameter supports both single values and arrays.
5. **Sorting:** Results are sorted by date (descending) and ID (descending) by default.

---

## Support

For issues or questions regarding this API, please contact the development team or refer to the main API documentation.

**Last Updated:** October 2025



