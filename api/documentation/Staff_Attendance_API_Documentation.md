# Staff Attendance API Documentation

## Overview

The Staff Attendance API provides comprehensive attendance statistics and detailed records for staff members in the Smart School Management System. This API returns detailed attendance data including counts, dates, leave information, and summaries with flexible filtering options.

**🎉 NEW FEATURE (FIXED):** The API now automatically detects the actual date range of attendance data when no dates are specified, solving the issue where calling with just `staff_id` returned empty results.

## Base URL
```
http://{domain}/api/
```

## Endpoints

### Primary Endpoint (With Authentication)
**URL:** `/api/teacher/attendance-summary`
**Method:** POST
**Content-Type:** application/json

### Alternative Endpoint (Simplified - For Testing)
**URL:** `/api/attendance/summary`
**Method:** POST
**Content-Type:** application/json

### NEW: Simplified Staff Attendance Endpoint
**URL:** `/api/teacher/staff-attendance`
**Method:** POST
**Content-Type:** application/json
**Description:** New simplified endpoint that automatically detects date ranges and requires only staff_id parameter.

## Authentication

The primary endpoint (`/api/teacher/attendance-summary`) requires the following headers:
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

The alternative endpoint (`/api/attendance/summary`) only requires:
```
Content-Type: application/json
```

## Endpoint

### Staff Attendance Summary

**Endpoint:** `POST /teacher/attendance-summary`

**Full URL:** `http://localhost/amt/api/teacher/attendance-summary`

**Description:** Retrieve comprehensive attendance statistics for staff members with detailed date information, leave records, and summaries.

## Request Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `staff_id` | integer | No | Specific staff member ID. If not provided, returns data for all active staff |
| `from_date` | string | No | Start date in YYYY-MM-DD format. Defaults to current year start (YYYY-01-01) |
| `to_date` | string | No | End date in YYYY-MM-DD format. Defaults to current year end (YYYY-12-31) |

## Request Examples

### 1. Get Attendance for Specific Staff Member

```bash
curl -X POST "http://localhost/amt/api/teacher/attendance-summary" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "staff_id": 6,
    "from_date": "2024-01-01",
    "to_date": "2024-12-31"
  }'
```

### 2. Get Attendance for All Staff (Current Year)

```bash
curl -X POST "http://localhost/amt/api/teacher/attendance-summary" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### 3. Get Attendance for Specific Date Range (All Staff)

```bash
curl -X POST "http://localhost/amt/api/teacher/attendance-summary" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "from_date": "2024-06-01",
    "to_date": "2024-06-30"
  }'
```

## Response Format

### Success Response (Single Staff Member)

**Status Code:** `200 OK`

```json
{
  "status": 1,
  "message": "Attendance summary retrieved successfully.",
  "data": {
    "staff_id": 6,
    "staff_info": {
      "id": "6",
      "name": "MAHA LAKSHMI",
      "surname": "SALLA",
      "employee_id": "200226",
      "email": "mahalakshmisalla70@gmail.com",
      "contact_no": "8328595488",
      "designation": "Accountant",
      "department_name": "Finance",
      "role_name": "Teacher"
    },
    "attendance_summary": {
      "Present": {
        "count": 45,
        "dates": [
          {
            "date": "2024-06-28",
            "remark": "",
            "recorded_at": "2024-06-28 09:15:00"
          },
          {
            "date": "2024-06-27",
            "remark": "On time",
            "recorded_at": "2024-06-27 09:00:00"
          }
        ]
      },
      "Absent": {
        "count": 3,
        "dates": [
          {
            "date": "2024-06-25",
            "remark": "Sick leave",
            "recorded_at": "2024-06-25 10:00:00"
          }
        ]
      },
      "Half Day": {
        "count": 2,
        "dates": [
          {
            "date": "2024-06-20",
            "remark": "Medical appointment",
            "recorded_at": "2024-06-20 13:00:00"
          }
        ]
      },
      "Late": {
        "count": 1,
        "dates": [
          {
            "date": "2024-06-15",
            "remark": "Traffic delay",
            "recorded_at": "2024-06-15 09:45:00"
          }
        ]
      },
      "Holiday": {
        "count": 5,
        "dates": [
          {
            "date": "2024-06-10",
            "remark": "National Holiday",
            "recorded_at": "2024-06-10 00:00:00"
          }
        ]
      }
    },
    "attendance_dates": [
      {
        "date": "2024-06-28",
        "type": "Present",
        "key_value": "<b class=\"text text-success\">P</b>",
        "remark": "",
        "recorded_at": "2024-06-28 09:15:00"
      },
      {
        "date": "2024-06-27",
        "type": "Present",
        "key_value": "<b class=\"text text-success\">P</b>",
        "remark": "On time",
        "recorded_at": "2024-06-27 09:00:00"
      }
    ],
    "leave_summary": {
      "leave_summary": {
        "Casual Leave": {
          "count": 2,
          "total_days": 5,
          "requests": [
            {
              "leave_from": "2024-06-01",
              "leave_to": "2024-06-03",
              "leave_days": 3,
              "employee_remark": "Family function",
              "admin_remark": "Approved",
              "applied_date": "2024-05-25"
            }
          ]
        },
        "Medical Leave": {
          "count": 1,
          "total_days": 2,
          "requests": [
            {
              "leave_from": "2024-06-15",
              "leave_to": "2024-06-16",
              "leave_days": 2,
              "employee_remark": "Medical treatment",
              "admin_remark": "Approved with medical certificate",
              "applied_date": "2024-06-10"
            }
          ]
        }
      },
      "leave_dates": [
        {
          "date": "2024-06-01",
          "leave_type": "Casual Leave",
          "remark": "Family function"
        },
        {
          "date": "2024-06-02",
          "leave_type": "Casual Leave",
          "remark": "Family function"
        },
        {
          "date": "2024-06-03",
          "leave_type": "Casual Leave",
          "remark": "Family function"
        }
      ]
    },
    "date_range": {
      "from_date": "2024-01-01",
      "to_date": "2024-12-31"
    }
  },
  "request_info": {
    "staff_id": 6,
    "from_date": "2024-01-01",
    "to_date": "2024-12-31",
    "generated_at": "2024-06-28 14:30:00"
  }
}
```

### Success Response (All Staff Members)

**Status Code:** `200 OK`

```json
{
  "status": 1,
  "message": "Attendance summary retrieved successfully.",
  "data": {
    "staff_attendance_data": [
      {
        "staff_id": 6,
        "staff_info": {
          "id": "6",
          "name": "MAHA LAKSHMI",
          "surname": "SALLA",
          "employee_id": "200226",
          "email": "mahalakshmisalla70@gmail.com",
          "contact_no": "8328595488",
          "designation": "Accountant",
          "department_name": "Finance",
          "role_name": "Teacher"
        },
        "attendance_summary": {
          "Present": {
            "count": 45,
            "dates": [...]
          },
          "Absent": {
            "count": 3,
            "dates": [...]
          }
        },
        "attendance_dates": [...],
        "leave_summary": {...}
      },
      {
        "staff_id": 7,
        "staff_info": {...},
        "attendance_summary": {...},
        "attendance_dates": [...],
        "leave_summary": {...}
      }
    ],
    "total_staff": 2,
    "date_range": {
      "from_date": "2024-01-01",
      "to_date": "2024-12-31"
    }
  },
  "request_info": {
    "staff_id": null,
    "from_date": "2024-01-01",
    "to_date": "2024-12-31",
    "generated_at": "2024-06-28 14:30:00"
  }
}
```

## Error Responses

### 400 Bad Request - Invalid JSON

```json
{
  "status": 400,
  "message": "Invalid JSON format in request body."
}
```

### 400 Bad Request - Invalid Staff ID

```json
{
  "status": 400,
  "message": "Invalid staff_id. Must be a positive integer."
}
```

### 400 Bad Request - Invalid Date Format

```json
{
  "status": 400,
  "message": "Invalid from_date format. Use YYYY-MM-DD format."
}
```

### 400 Bad Request - Invalid Date Range

```json
{
  "status": 400,
  "message": "from_date cannot be greater than to_date."
}
```

### 400 Bad Request - Staff Not Found

```json
{
  "status": 400,
  "message": "Staff member not found or no data available."
}
```

### 401 Unauthorized

```json
{
  "status": 401,
  "message": "Unauthorized. Please check Client-Service and Auth-Key headers."
}
```

### 500 Internal Server Error

```json
{
  "status": 500,
  "message": "Internal server error. Please try again later."
}
```

## Data Fields Explanation

### Attendance Summary Fields

- **Present**: Days when staff was present and on time
- **Absent**: Days when staff was absent without approved leave
- **Half Day**: Days when staff worked only half day
- **Late**: Days when staff arrived late but was present
- **Holiday**: Official holidays or school closure days

### Leave Summary Fields

- **Casual Leave**: Personal leave days taken by staff
- **Medical Leave**: Medical/sick leave days
- **Maternity Leave**: Maternity leave days (if applicable)
- **Other Leave Types**: Any additional leave categories configured in the system

### Date Information

Each attendance and leave record includes:
- **date**: The specific date (YYYY-MM-DD format)
- **type**: Type of attendance/leave
- **remark**: Additional notes or comments
- **recorded_at**: Timestamp when the record was created

## Usage Notes

1. **🎉 NEW: Auto Date Detection**: If no date range is specified, the API now automatically detects the actual date range of attendance data instead of defaulting to current year
2. **Staff Filtering**: Use `staff_id` parameter to get data for a specific staff member
3. **All Staff**: Omit `staff_id` parameter to get data for all active staff members
4. **Performance**: For large date ranges or many staff members, the API may take longer to respond
5. **Permissions**: Ensure the authenticated user has appropriate permissions to view attendance data
6. **Backward Compatibility**: All existing API calls with explicit date ranges continue to work as before

## Integration Examples

### 🎉 NEW: Simplified Usage (Auto Date Detection)

#### Get All Attendance for a Staff Member (Recommended)
```javascript
// NEW: Just provide staff_id - API automatically finds date range
const attendanceData = {
    staff_id: 6
};

fetch('http://localhost/amt/api/teacher/staff-attendance', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Client-Service': 'smartschool',
        'Auth-Key': 'schoolAdmin@'
    },
    body: JSON.stringify(attendanceData)
})
.then(response => response.json())
.then(data => {
    if (data.status === 1) {
        console.log('Staff Attendance:', data.data);
        console.log('Auto-detected Date Range:', data.data.date_range);
        console.log('Present Days:', data.data.attendance_summary.Present.count);
        console.log('Absent Days:', data.data.attendance_summary.Absent.count);
    } else {
        console.error('Error:', data.message);
    }
});
```

#### Get All Staff Attendance (Auto Date Range)
```javascript
// Get all staff with automatic date detection
fetch('http://localhost/amt/api/teacher/attendance-summary', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Client-Service': 'smartschool',
        'Auth-Key': 'schoolAdmin@'
    },
    body: JSON.stringify({}) // Empty body - auto detects date range
})
.then(response => response.json())
.then(data => {
    if (data.status === 1) {
        console.log('All Staff Attendance:', data.data);
        console.log('Total Staff:', data.data.total_staff);
        console.log('Date Range:', data.data.date_range);
    }
});
```

### Traditional Usage (Explicit Date Range)

```javascript
const attendanceData = {
    staff_id: 6,
    from_date: "2024-01-01",
    to_date: "2024-12-31"
};

fetch('http://localhost/amt/api/teacher/attendance-summary', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Client-Service': 'smartschool',
        'Auth-Key': 'schoolAdmin@'
    },
    body: JSON.stringify(attendanceData)
})
.then(response => response.json())
.then(data => {
    if (data.status === 1) {
        console.log('Attendance Summary:', data.data);
        // Process attendance data
        const summary = data.data.attendance_summary;
        console.log('Present Days:', summary.Present.count);
        console.log('Absent Days:', summary.Absent.count);
    } else {
        console.error('Error:', data.message);
    }
})
.catch(error => {
    console.error('Request failed:', error);
});
```

### PHP Examples

#### 🎉 NEW: Simplified PHP Usage (Auto Date Detection)
```php
<?php
// NEW: Get all attendance for a staff member (auto date range)
$url = 'http://localhost/amt/api/teacher/staff-attendance';
$data = array('staff_id' => 6); // Only staff_id needed!

$options = array(
    'http' => array(
        'header' => "Content-Type: application/json\r\n" .
                   "Client-Service: smartschool\r\n" .
                   "Auth-Key: schoolAdmin@\r\n",
        'method' => 'POST',
        'content' => json_encode($data)
    )
);

$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);
$response = json_decode($result, true);

if ($response['status'] == 1) {
    $attendanceData = $response['data'];
    echo "Staff: " . $attendanceData['staff_info']['name'] . " " . $attendanceData['staff_info']['surname'] . "\n";
    echo "Auto-detected Date Range: " . $attendanceData['date_range']['from_date'] . " to " . $attendanceData['date_range']['to_date'] . "\n";
    echo "Present Days: " . $attendanceData['attendance_summary']['Present']['count'] . "\n";
    echo "Absent Days: " . $attendanceData['attendance_summary']['Absent']['count'] . "\n";
    echo "Total Attendance Records: " . count($attendanceData['attendance_dates']) . "\n";
} else {
    echo "Error: " . $response['message'] . "\n";
}
?>
```

#### Traditional PHP Usage (Explicit Date Range)
```php
<?php
$url = 'http://localhost/amt/api/teacher/attendance-summary';
$data = array(
    'staff_id' => 6,
    'from_date' => '2024-01-01',
    'to_date' => '2024-12-31'
);

$options = array(
    'http' => array(
        'header' => "Content-Type: application/json\r\n" .
                   "Client-Service: smartschool\r\n" .
                   "Auth-Key: schoolAdmin@\r\n",
        'method' => 'POST',
        'content' => json_encode($data)
    )
);

$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);
$response = json_decode($result, true);

if ($response['status'] == 1) {
    $attendanceData = $response['data'];
    echo "Staff: " . $attendanceData['staff_info']['name'] . "\n";
    echo "Present Days: " . $attendanceData['attendance_summary']['Present']['count'] . "\n";
    echo "Absent Days: " . $attendanceData['attendance_summary']['Absent']['count'] . "\n";
} else {
    echo "Error: " . $response['message'] . "\n";
}
?>
```

### cURL Examples

#### 🎉 NEW: Get Staff Attendance (Auto Date Range)
```bash
# Just provide staff_id - API automatically finds date range
curl -X POST "http://localhost/amt/api/teacher/staff-attendance" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"staff_id": 6}'
```

#### Get All Staff Attendance (Auto Date Range)
```bash
# Empty body - API automatically detects date range
curl -X POST "http://localhost/amt/api/teacher/attendance-summary" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

#### Traditional Usage with Date Range
```bash
curl -X POST "http://localhost/amt/api/teacher/attendance-summary" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"staff_id": 6, "from_date": "2024-08-01", "to_date": "2024-08-31"}'
```

## Support

For API support and questions, contact the development team or refer to the main project documentation.

## Version History

- **v1.0** - Initial release with comprehensive attendance statistics and date information
- **v1.1** - 🎉 **MAJOR UPDATE**: Added automatic date range detection, fixed issue where API returned empty data when called with just staff_id, added new simplified endpoint `/api/teacher/staff-attendance`

## 🎯 ISSUE RESOLUTION SUMMARY

**Problem Solved:** The original issue where calling the API with just `{"staff_id": 6}` returned empty data has been completely resolved.

**Root Cause:** The API was defaulting to the current year (2025) when no dates were specified, but attendance data exists in 2024.

**Solution Implemented:**
1. **Enhanced existing endpoint** `/api/teacher/attendance-summary` with automatic date range detection
2. **Created new simplified endpoint** `/api/teacher/staff-attendance` specifically for easy staff queries
3. **Backward compatibility maintained** - all existing API calls continue to work

**Result:** Now when you call with just `{"staff_id": 6}`, the API automatically detects the actual attendance data range (e.g., 2024-05-20 to 2024-12-07) and returns complete attendance information including all present/absent dates.
