# Monthly Staff Attendance Report API Documentation

## Overview

The Monthly Staff Attendance Report API provides comprehensive monthly attendance reports for staff members, mirroring the functionality of the web page at `http://localhost/amt/attendencereports/staffattendancereport`. This API returns detailed daily attendance records, attendance summaries, percentage calculations, and attendance status for each staff member.

**Base URL:** `http://localhost/amt/api`

**Version:** 1.0.0

**Authentication Required:** Yes

**Related Web Page:** `/attendencereports/staffattendancereport`

---

## Table of Contents

1. [Features](#features)
2. [Authentication](#authentication)
3. [Endpoints](#endpoints)
4. [Request Parameters](#request-parameters)
5. [Response Format](#response-format)
6. [Usage Examples](#usage-examples)
7. [Error Handling](#error-handling)
8. [Code Examples](#code-examples)
9. [Testing](#testing)

---

## Features

✅ **Monthly Attendance Reports** - Get complete attendance data for a specific month and year  
✅ **Role-Based Filtering** - Filter staff by role (Teacher, Admin, Accountant, etc.)  
✅ **Daily Attendance Records** - Detailed day-by-day attendance for each staff member  
✅ **Attendance Summaries** - Count of Present, Absent, Late, Half Day, and Holiday records  
✅ **Percentage Calculation** - Automatic attendance percentage calculation  
✅ **Status Classification** - Attendance status (Good/Low) based on percentage  
✅ **Comprehensive Staff Info** - Full staff details including employee ID, contact, email, and role  
✅ **Available Periods** - Get list of available months, years, and roles for filtering

---

## Authentication

All API requests require authentication headers:

```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

---

## Endpoints

### 1. Get Monthly Staff Attendance Report

**Endpoint:** `POST /api/monthly-staff-attendance/report`

**Description:** Retrieves comprehensive monthly attendance report for staff members with daily attendance records, summaries, and percentage calculations.

**Full URL:** `http://localhost/amt/api/monthly-staff-attendance/report`

#### Request Parameters

| Parameter | Type   | Required | Description | Example |
|-----------|--------|----------|-------------|---------|
| `role` | string | No | Staff role name to filter. Use "select" or omit for all roles | `"Teacher"`, `"Admin"`, `"Accountant"` |
| `month` | string | No | Full month name (defaults to current month) | `"October"`, `"January"` |
| `year` | integer | No | Year (defaults to current year) | `2024`, `2025` |

#### Request Example

```json
{
  "role": "Teacher",
  "month": "October",
  "year": 2024
}
```

#### Response Example

```json
{
  "status": 1,
  "message": "Monthly staff attendance report retrieved successfully",
  "filters_applied": {
    "role": "Teacher",
    "month": "October",
    "month_number": 10,
    "year": 2024
  },
  "attendance_types": [
    {
      "id": "1",
      "type": "Present",
      "key_value": "<b class='text text-success'>P</b>",
      "is_active": "yes"
    },
    {
      "id": "2",
      "type": "Late",
      "key_value": "<b class='text text-warning'>L</b>",
      "is_active": "yes"
    },
    {
      "id": "3",
      "type": "Absent",
      "key_value": "<b class='text text-danger'>A</b>",
      "is_active": "yes"
    },
    {
      "id": "4",
      "type": "Half Day",
      "key_value": "<b class='text text-info'>H</b>",
      "is_active": "yes"
    },
    {
      "id": "5",
      "type": "Holiday",
      "key_value": "<b class='text text-default'>HD</b>",
      "is_active": "yes"
    }
  ],
  "total_staff": 5,
  "total_days": 31,
  "dates": [
    "2024-10-01",
    "2024-10-02",
    "2024-10-03",
    "...",
    "2024-10-31"
  ],
  "data": [
    {
      "staff_id": "6",
      "staff_info": {
        "name": "MAHA LAKSHMI",
        "surname": "SALLA",
        "employee_id": "200226",
        "contact_no": "8328595488",
        "email": "mahalakshmisalla70@gmail.com",
        "role": "Teacher"
      },
      "daily_attendance": {
        "2024-10-01": {
          "date": "2024-10-01",
          "day_name": "Tuesday",
          "day_short": "Tue",
          "attendance_type": "Present",
          "attendance_key": "<b class='text text-success'>P</b>",
          "remark": ""
        },
        "2024-10-02": {
          "date": "2024-10-02",
          "day_name": "Wednesday",
          "day_short": "Wed",
          "attendance_type": "Present",
          "attendance_key": "<b class='text text-success'>P</b>",
          "remark": ""
        },
        "2024-10-03": {
          "date": "2024-10-03",
          "day_name": "Thursday",
          "day_short": "Thu",
          "attendance_type": "Absent",
          "attendance_key": "<b class='text text-danger'>A</b>",
          "remark": "Sick leave"
        }
      },
      "attendance_summary": {
        "Present": 22,
        "Late": 2,
        "Absent": 3,
        "Half Day": 1,
        "Holiday": 3
      },
      "attendance_percentage": 86.21,
      "attendance_percentage_display": 86,
      "attendance_status": "Good",
      "attendance_status_class": "success",
      "total_working_days": 29,
      "total_present_days": 25
    }
  ],
  "timestamp": "2024-10-13 14:30:00"
}
```

---

### 2. Get Available Periods

**Endpoint:** `POST /api/monthly-staff-attendance/available-periods`

**Description:** Returns list of available years, months, and staff roles for filtering.

**Full URL:** `http://localhost/amt/api/monthly-staff-attendance/available-periods`

#### Request Example

```json
{}
```

#### Response Example

```json
{
  "status": 1,
  "message": "Available periods retrieved successfully",
  "data": {
    "years": [
      {"year": "2025"},
      {"year": "2024"},
      {"year": "2023"}
    ],
    "months": [
      "January", "February", "March", "April", "May", "June",
      "July", "August", "September", "October", "November", "December"
    ],
    "roles": [
      {
        "id": "1",
        "name": "Admin",
        "type": "Admin"
      },
      {
        "id": "2",
        "name": "Teacher",
        "type": "Teacher"
      },
      {
        "id": "3",
        "name": "Accountant",
        "type": "Accountant"
      }
    ]
  },
  "timestamp": "2024-10-13 14:30:00"
}
```

---

## Response Fields

### Main Response Fields

| Field | Type | Description |
|-------|------|-------------|
| `status` | integer | 1 for success, 0 for error |
| `message` | string | Response message |
| `filters_applied` | object | Applied filter parameters |
| `attendance_types` | array | List of attendance types with display keys |
| `total_staff` | integer | Total number of staff members in the report |
| `total_days` | integer | Number of days in the selected month |
| `dates` | array | Array of all dates in the month (YYYY-MM-DD) |
| `data` | array | Array of staff attendance records |
| `timestamp` | string | Response generation timestamp |

### Staff Attendance Record Fields

| Field | Type | Description |
|-------|------|-------------|
| `staff_id` | string | Unique staff identifier |
| `staff_info` | object | Staff personal information |
| `daily_attendance` | object | Daily attendance records (keyed by date) |
| `attendance_summary` | object | Count of each attendance type |
| `attendance_percentage` | float | Attendance percentage (precise) |
| `attendance_percentage_display` | integer | Attendance percentage (rounded for display) |
| `attendance_status` | string | "Good", "Low", or "No Data" |
| `attendance_status_class` | string | CSS class: "success", "danger", or "default" |
| `total_working_days` | integer | Total working days (excluding holidays) |
| `total_present_days` | integer | Total days marked as present/late/half-day |

### Daily Attendance Record Fields

| Field | Type | Description |
|-------|------|-------------|
| `date` | string | Date in YYYY-MM-DD format |
| `day_name` | string | Full day name (e.g., "Monday") |
| `day_short` | string | Short day name (e.g., "Mon") |
| `attendance_type` | string | Attendance type (Present, Absent, Late, etc.) |
| `attendance_key` | string | HTML display key (e.g., P, A, L, H) |
| `remark` | string | Attendance remark or notes |

---

## Attendance Calculation

### Percentage Formula

```
Total Present Days = Present + Late + Half Day
Total Working Days = Present + Late + Absent + Half Day
Attendance Percentage = (Total Present Days / Total Working Days) × 100
```

### Status Classification

| Percentage | Status | Class |
|------------|--------|-------|
| < 75% | Low | danger (red) |
| ≥ 75% | Good | success (green) |
| No data | No Data | default (gray) |

---

## Usage Examples

### Example 1: Get Current Month Attendance for All Staff (Empty Payload)

```bash
curl -X POST "http://localhost/amt/api/monthly-staff-attendance/report" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 2: Get October 2024 Attendance for All Staff

```bash
curl -X POST "http://localhost/amt/api/monthly-staff-attendance/report" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "month": "October",
    "year": 2024
  }'
```

### Example 3: Get Teacher Attendance for September 2024

```bash
curl -X POST "http://localhost/amt/api/monthly-staff-attendance/report" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "role": "Teacher",
    "month": "September",
    "year": 2024
  }'
```

### Example 3: Get Available Periods

```bash
curl -X POST "http://localhost/amt/api/monthly-staff-attendance/available-periods" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 4: Get Current Month Attendance for Accountants

```bash
curl -X POST "http://localhost/amt/api/monthly-staff-attendance/report" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "role": "Accountant",
    "month": "October"
  }'
```

---

## Error Handling

### Common Error Responses

#### 400 Bad Request - Invalid Month

```json
{
  "status": 0,
  "message": "Invalid month name. Use full month name (e.g., \"January\", \"October\")."
}
```

#### 400 Bad Request - Invalid Year

```json
{
  "status": 0,
  "message": "Invalid year. Must be between 2000 and 2100."
}
```

#### 400 Bad Request - Invalid JSON

```json
{
  "status": 0,
  "message": "Invalid JSON format in request body."
}
```

#### 401 Unauthorized

```json
{
  "status": 0,
  "message": "Unauthorized access"
}
```

#### 500 Internal Server Error

```json
{
  "status": 0,
  "message": "Internal server error occurred",
  "error": "Detailed error message",
  "data": null
}
```

---

## Code Examples

### JavaScript (Fetch API)

```javascript
/**
 * Get Monthly Staff Attendance Report
 */
async function getMonthlyStaffAttendance(role, month, year) {
  try {
    const response = await fetch('http://localhost/amt/api/monthly-staff-attendance/report', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Client-Service': 'smartschool',
        'Auth-Key': 'schoolAdmin@'
      },
      body: JSON.stringify({
        role: role,
        month: month,
        year: year
      })
    });
    
    const data = await response.json();
    
    if (data.status === 1) {
      console.log('Monthly Report:', data.data);
      console.log('Total Staff:', data.total_staff);
      console.log('Total Days:', data.total_days);
      
      // Process each staff member
      data.data.forEach(staff => {
        console.log(`${staff.staff_info.name} ${staff.staff_info.surname}`);
        console.log(`  Employee ID: ${staff.staff_info.employee_id}`);
        console.log(`  Attendance: ${staff.attendance_percentage_display}%`);
        console.log(`  Status: ${staff.attendance_status}`);
        console.log(`  Present: ${staff.attendance_summary.Present}`);
        console.log(`  Absent: ${staff.attendance_summary.Absent}`);
        console.log(`  Late: ${staff.attendance_summary.Late}`);
      });
      
      return data;
    } else {
      console.error('Error:', data.message);
      return null;
    }
  } catch (error) {
    console.error('Request failed:', error);
    return null;
  }
}

// Usage
getMonthlyStaffAttendance('Teacher', 'October', 2024);
```

### JavaScript - Get Available Periods

```javascript
/**
 * Get Available Months, Years, and Roles
 */
async function getAvailablePeriods() {
  try {
    const response = await fetch('http://localhost/amt/api/monthly-staff-attendance/available-periods', {
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
      console.log('Available Years:', data.data.years);
      console.log('Available Months:', data.data.months);
      console.log('Available Roles:', data.data.roles);
      return data.data;
    } else {
      console.error('Error:', data.message);
      return null;
    }
  } catch (error) {
    console.error('Request failed:', error);
    return null;
  }
}

// Usage
getAvailablePeriods();
```

### PHP (cURL)

```php
<?php
/**
 * Get Monthly Staff Attendance Report
 * 
 * @param string $role Staff role (optional)
 * @param string $month Month name (required)
 * @param int $year Year (optional)
 * @return array|null
 */
function getMonthlyStaffAttendance($role = null, $month = null, $year = null) {
    $url = 'http://localhost/amt/api/monthly-staff-attendance/report';
    
    $data = array();
    if ($role !== null) $data['role'] = $role;
    if ($month !== null) $data['month'] = $month;
    if ($year !== null) $data['year'] = $year;
    
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
            echo "Total Staff: " . $result['total_staff'] . "\n";
            echo "Total Days: " . $result['total_days'] . "\n\n";
            
            foreach ($result['data'] as $staff) {
                echo $staff['staff_info']['name'] . " " . $staff['staff_info']['surname'] . "\n";
                echo "  Employee ID: " . $staff['staff_info']['employee_id'] . "\n";
                echo "  Attendance: " . $staff['attendance_percentage_display'] . "%\n";
                echo "  Status: " . $staff['attendance_status'] . "\n";
                echo "  Present: " . $staff['attendance_summary']['Present'] . "\n";
                echo "  Absent: " . $staff['attendance_summary']['Absent'] . "\n";
                echo "  Late: " . $staff['attendance_summary']['Late'] . "\n\n";
            }
            
            return $result['data'];
        }
    }
    
    return null;
}

// Usage
$report = getMonthlyStaffAttendance('Teacher', 'October', 2024);
?>
```

### Python (Requests)

```python
import requests
import json

def get_monthly_staff_attendance(role=None, month=None, year=None):
    """
    Get Monthly Staff Attendance Report
    
    Args:
        role (str): Staff role (optional)
        month (str): Month name (required)
        year (int): Year (optional)
    
    Returns:
        dict: Attendance report data or None
    """
    url = 'http://localhost/amt/api/monthly-staff-attendance/report'
    
    headers = {
        'Content-Type': 'application/json',
        'Client-Service': 'smartschool',
        'Auth-Key': 'schoolAdmin@'
    }
    
    data = {}
    if role is not None:
        data['role'] = role
    if month is not None:
        data['month'] = month
    if year is not None:
        data['year'] = year
    
    try:
        response = requests.post(url, headers=headers, json=data)
        
        if response.status_code == 200:
            result = response.json()
            if result['status'] == 1:
                print(f"Total Staff: {result['total_staff']}")
                print(f"Total Days: {result['total_days']}\n")
                
                for staff in result['data']:
                    print(f"{staff['staff_info']['name']} {staff['staff_info']['surname']}")
                    print(f"  Employee ID: {staff['staff_info']['employee_id']}")
                    print(f"  Attendance: {staff['attendance_percentage_display']}%")
                    print(f"  Status: {staff['attendance_status']}")
                    print(f"  Present: {staff['attendance_summary']['Present']}")
                    print(f"  Absent: {staff['attendance_summary']['Absent']}")
                    print(f"  Late: {staff['attendance_summary']['Late']}\n")
                
                return result['data']
            else:
                print(f"Error: {result['message']}")
                return None
    except Exception as e:
        print(f"Request failed: {str(e)}")
        return None

# Usage
report = get_monthly_staff_attendance('Teacher', 'October', 2024)
```

---

## Testing

### Quick Test with cURL

1. **Test Available Periods:**
```bash
curl -X POST "http://localhost/amt/api/monthly-staff-attendance/available-periods" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

2. **Test Monthly Report:**
```bash
curl -X POST "http://localhost/amt/api/monthly-staff-attendance/report" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"month": "October", "year": 2024}'
```

3. **Test with Role Filter:**
```bash
curl -X POST "http://localhost/amt/api/monthly-staff-attendance/report" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"role": "Teacher", "month": "October", "year": 2024}'
```

### Test HTML File

A test HTML file is available at `api/documentation/monthly_staff_attendance_report_api_test.html` for interactive API testing.

---

## Important Notes

1. **Empty Payload Support:** API can be called with `{}` - defaults to current month and year for all staff
2. **Month Format:** Use full month names (e.g., "January", "October", not "Jan" or "10")
3. **Default Values:** If month/year not specified, uses current month and year
4. **Year Range:** Year must be between 2000 and 2100
5. **Role Filter:** Use exact role names as they appear in the system (case-sensitive)
6. **Active Staff Only:** API returns data only for active staff members
7. **Attendance Percentage:** Calculated based on Present, Late, and Half Day vs. total working days
8. **Holiday Exclusion:** Holidays are not counted in percentage calculations
9. **Daily Attendance:** Each day shows attendance type with visual key for easy interpretation
10. **Performance:** Large date ranges may take longer to process

---

## Related APIs

- **Staff Attendance Report API** - `/api/staff-attendance-report/filter` (Date range filtering)
- **Staff Attendance API** - `/api/teacher/attendance-summary` (Individual staff attendance)
- **Staff Attendance Years API** - `/api/staff-attendance-years/list` (Available years)

---

## Support

For issues or questions regarding this API, please contact the development team or refer to the main API documentation index.

---

## Version History

- **v1.0.0** (October 2025) - Initial release
  - Monthly attendance report endpoint
  - Available periods endpoint
  - Role-based filtering
  - Attendance percentage calculations
  - Status classification

---

**Last Updated:** October 13, 2025  
**Maintained By:** SMS Development Team  
**API Status:** ✅ Active
