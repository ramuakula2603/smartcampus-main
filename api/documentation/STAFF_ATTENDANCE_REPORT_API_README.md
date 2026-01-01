# Staff Attendance Report API Documentation

## Overview

The Staff Attendance Report API provides endpoints for retrieving staff attendance records with detailed staff information. This API returns comprehensive staff attendance data with filtering capabilities by staff role, date range, and attendance type.

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

### 1. Filter Staff Attendance Report

**Endpoint:** `POST /api/staff-attendance-report/filter`

**Description:** Retrieves detailed staff attendance records with optional filtering by staff role, date range, and attendance type.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Request Body:**
```json
{
  "role_id": 2,
  "from_date": "2025-10-01",
  "to_date": "2025-10-07",
  "attendance_type": "present"
}
```

**Response:**
```json
{
  "status": 1,
  "message": "Staff attendance report retrieved successfully",
  "filters_applied": {
    "role_id": [2],
    "from_date": "2025-10-01",
    "to_date": "2025-10-07",
    "attendance_type": "present"
  },
  "total_records": 50,
  "data": [
    {
      "id": "100",
      "staff_id": "50",
      "date": "2025-10-07",
      "staff_attendance_type_id": "1",
      "remark": "",
      "is_active": "yes",
      "name": "John Smith",
      "surname": "Smith",
      "employee_id": "EMP001",
      "department": "Mathematics",
      "designation": "Senior Teacher",
      "role_id": "2",
      "role": "Teacher",
      "attendance_type": "Present"
    }
  ],
  "timestamp": "2025-10-07 10:30:00"
}
```

---

### 2. List All Staff Attendance

**Endpoint:** `POST /api/staff-attendance-report/list`

**Description:** Retrieves all staff attendance records.

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
  "message": "Staff attendance report retrieved successfully",
  "total_records": 200,
  "data": [
    {
      "id": "100",
      "staff_id": "50",
      "date": "2025-10-07",
      "staff_attendance_type_id": "1",
      "remark": "",
      "is_active": "yes",
      "name": "John Smith",
      "surname": "Smith",
      "employee_id": "EMP001",
      "department": "Mathematics",
      "designation": "Senior Teacher",
      "role_id": "2",
      "role": "Teacher",
      "attendance_type": "Present"
    }
  ],
  "timestamp": "2025-10-07 10:30:00"
}
```

---

## Filter Parameters

All filter parameters are **optional**. If no parameters are provided, the API returns all staff attendance records.

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `role_id` | integer or array | Single or multiple staff role IDs | `2` or `[2, 3, 4]` |
| `from_date` | string | Start date for date range filter (YYYY-MM-DD) | `"2025-10-01"` |
| `to_date` | string | End date for date range filter (YYYY-MM-DD) | `"2025-10-07"` |
| `attendance_type` | string | Attendance type filter | `"present"`, `"absent"`, `"late"`, `"half_day"` |

### Multi-Select Support

The API supports multi-select for `role_id` parameter:

```json
{
  "role_id": [2, 3, 4]
}
```

### Attendance Type Values

| Value | Description |
|-------|-------------|
| `present` | Staff is present |
| `absent` | Staff is absent |
| `late` | Staff arrived late |
| `half_day` | Staff attended half day |

### Date Range Filtering

- If both `from_date` and `to_date` are provided, returns data between those dates
- If only `from_date` is provided, returns data from that date onwards
- If only `to_date` is provided, returns data up to that date
- If no dates are provided, returns all attendance records

### Graceful Null/Empty Handling

- Empty parameters (`{}`) return all staff attendance records
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
| `id` | string | Attendance record ID |
| `staff_id` | string | Staff ID |
| `date` | string | Attendance date |
| `staff_attendance_type_id` | string | Attendance type ID |
| `remark` | string | Attendance remark |
| `is_active` | string | Staff active status |
| `name` | string | Staff full name |
| `surname` | string | Staff surname |
| `employee_id` | string | Staff employee ID |
| `department` | string | Staff department |
| `designation` | string | Staff designation |
| `role_id` | string | Staff role ID |
| `role` | string | Staff role name |
| `attendance_type` | string | Attendance type name |

---

## Usage Examples

### Example 1: Get All Staff Attendance

```bash
curl -X POST "http://localhost/amt/api/staff-attendance-report/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 2: Filter by Role

```bash
curl -X POST "http://localhost/amt/api/staff-attendance-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "role_id": 2
  }'
```

### Example 3: Filter by Date Range

```bash
curl -X POST "http://localhost/amt/api/staff-attendance-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "from_date": "2025-10-01",
    "to_date": "2025-10-07"
  }'
```

### Example 4: Filter by Attendance Type

```bash
curl -X POST "http://localhost/amt/api/staff-attendance-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "attendance_type": "present",
    "from_date": "2025-10-01",
    "to_date": "2025-10-07"
  }'
```

### Example 5: Filter by Multiple Roles and Date Range

```bash
curl -X POST "http://localhost/amt/api/staff-attendance-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "role_id": [2, 3, 4],
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
const getStaffAttendanceReport = async (roleId, fromDate, toDate, attendanceType) => {
  try {
    const response = await fetch('http://localhost/amt/api/staff-attendance-report/filter', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Client-Service': 'smartschool',
        'Auth-Key': 'schoolAdmin@'
      },
      body: JSON.stringify({
        role_id: roleId,
        from_date: fromDate,
        to_date: toDate,
        attendance_type: attendanceType
      })
    });
    
    const data = await response.json();
    
    if (data.status === 1) {
      console.log('Staff Attendance Report:', data.data);
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
getStaffAttendanceReport(2, '2025-10-01', '2025-10-07', 'present');
```

### PHP (cURL)

```php
<?php
function getStaffAttendanceReport($roleId = null, $fromDate = null, $toDate = null, $attendanceType = null) {
    $url = 'http://localhost/amt/api/staff-attendance-report/filter';
    
    $data = array();
    if ($roleId !== null) $data['role_id'] = $roleId;
    if ($fromDate !== null) $data['from_date'] = $fromDate;
    if ($toDate !== null) $data['to_date'] = $toDate;
    if ($attendanceType !== null) $data['attendance_type'] = $attendanceType;
    
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
$staffAttendanceReport = getStaffAttendanceReport(2, '2025-10-01', '2025-10-07', 'present');
print_r($staffAttendanceReport);
?>
```

### Python (Requests)

```python
import requests
import json

def get_staff_attendance_report(role_id=None, from_date=None, to_date=None, attendance_type=None):
    url = 'http://localhost/amt/api/staff-attendance-report/filter'
    
    headers = {
        'Content-Type': 'application/json',
        'Client-Service': 'smartschool',
        'Auth-Key': 'schoolAdmin@'
    }
    
    data = {}
    if role_id is not None:
        data['role_id'] = role_id
    if from_date is not None:
        data['from_date'] = from_date
    if to_date is not None:
        data['to_date'] = to_date
    if attendance_type is not None:
        data['attendance_type'] = attendance_type
    
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
staff_attendance_report = get_staff_attendance_report(2, '2025-10-01', '2025-10-07', 'present')
print(staff_attendance_report)
```

---

## Notes

1. **Date Format:** All dates should be in `YYYY-MM-DD` format.
2. **Active Staff Only:** The API returns data only for active staff members (`is_active = 'yes'`).
3. **Role-Based Filtering:** Use `role_id` to filter by specific staff roles (e.g., Teacher, Admin, Accountant).
4. **Attendance Type:** The `attendance_type` parameter accepts lowercase values: `present`, `absent`, `late`, `half_day`.
5. **Complete Staff Info:** Each record includes comprehensive staff information including department and designation.

---

## Support

For issues or questions regarding this API, please contact the development team or refer to the main API documentation.

**Last Updated:** October 2025

