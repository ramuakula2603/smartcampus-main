# Student Attendance Type Report API Documentation

## Overview

The Student Attendance Type Report API provides endpoints for retrieving students who have specific attendance types (Present, Absent, Late, Excuse, Half Day, Holiday) within a given date range. This API corresponds to the **"Student Attendance Type Report"** page at:

**Web Page URL:** `http://localhost/amt/attendencereports/attendancereport`

**Key Features:**
- Filter students by specific attendance type (optional - returns all types if not specified)
- Multiple date range options (today, this_week, last_week, this_month, last_3_months, etc.)
- Custom date range support with period option
- Class and section filtering
- Returns detailed student information with attendance count
- Built-in attendance type reference endpoint

**Base URL:** `http://localhost/amt/api`

**Version:** 1.0.0

**Authentication Required:** Yes

---

## Table of Contents

1. [Authentication](#authentication)
2. [Attendance Types](#attendance-types)
3. [Endpoints](#endpoints)
4. [Search Type Options](#search-type-options)
5. [Request Parameters](#request-parameters)
6. [Response Format](#response-format)
7. [Usage Examples](#usage-examples)
8. [Error Handling](#error-handling)
9. [Code Examples](#code-examples)
10. [Testing](#testing)

---

## Authentication

All API requests require authentication headers:

- **Client-Service:** `smartschool`
- **Auth-Key:** `schoolAdmin@`

### Example Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

---

## Attendance Types

The system supports 6 different attendance types:

| ID | Type | Key | Description |
|----|------|-----|-------------|
| 1 | Present | P | Student was present |
| 2 | Excuse | E | Student was excused |
| 3 | Late | L | Student was late |
| 4 | Absent | A | Student was absent |
| 5 | Holiday | H | Holiday |
| 6 | Half Day | HD | Student attended half day |

---

## Endpoints

### 1. Filter Student Attendance Type Report

**Endpoint:** `POST /api/student-attendance-type-report/filter`

**Description:** Retrieves students with a specific attendance type within filters. If attendance_type is not provided, returns all attendance types. If class_id is not provided, returns all classes. If session_id is not provided, returns all sessions.

**Request Body (Empty - All Defaults):**
```json
{}
```
*Defaults: Returns all attendance types, all classes, all sections, all sessions for current week*

**Request Body (Minimum Required):**
```json
{
  "search_type": "this_month"
}
```

**Request Body (With All Filters):**
```json
{
  "attendance_type": 1,
  "class_id": 14,
  "section_id": 24,
  "search_type": "period",
  "date_from": "2025-08-01",
  "date_to": "2025-08-31",
  "session_id": 21
}
```

**Response (When attendance_type is provided):**
```json
{
  "status": true,
  "message": "Student attendance type report retrieved successfully",
  "filters_applied": {
    "search_type": "period",
    "attendance_type": 1,
    "attendance_type_name": "Present",
    "attendance_type_key": "P",
    "class_id": [14],
    "section_id": [24],
    "session_id": 21
  },
  "date_range": {
    "from": "2025-08-01",
    "to": "2025-08-31",
    "display": "01 Aug 2025 To 31 Aug 2025"
  },
  "total_records": 56,
  "data": [
    {
      "class_id": "14",
      "id": "1883",
      "class": "JR-MPC",
      "section_id": "24",
      "section": "08199-JR-MPC-LEO",
      "admission_no": "2025001",
      "roll_no": null,
      "admission_date": "2025-06-15",
      "firstname": "NEELAM",
      "middlename": null,
      "lastname": "GNANA DEEPIKA",
      "mobileno": "9876543210",
      "email": "neelam@example.com",
      "dob": "2010-03-15",
      "father_name": "Mr. Neelam",
      "gender": "Female",
      "session_id": "21",
      "total_type": "5"
    }
  ],
  "timestamp": "2025-10-14 09:15:00"
}
```

**Response (When attendance_type is NOT provided - returns all types):**
```json
{
  "status": true,
  "message": "Student attendance type report retrieved successfully",
  "filters_applied": {
    "search_type": "this_month",
    "class_id": [14],
    "section_id": null,
    "session_id": 21,
    "attendance_type": "all",
    "attendance_type_name": "All Types",
    "attendance_type_key": "ALL"
  },
  "date_range": {
    "from": "2025-10-01",
    "to": "2025-10-31",
    "display": "01 Oct 2025 To 31 Oct 2025"
  },
  "total_records": 150,
  "data": [
    {
      "class_id": "14",
      "id": "1883",
      "class": "JR-MPC",
      "section_id": "24",
      "section": "08199-JR-MPC-LEO",
      "admission_no": "2025001",
      "firstname": "NEELAM",
      "lastname": "GNANA DEEPIKA",
      "total_type": "8"
    }
  ],
  "timestamp": "2025-10-14 09:15:00"
}
```

---

### 2. List All Students with Attendance Type

**Endpoint:** `POST /api/student-attendance-type-report/list`

**Description:** Retrieves all students with a specific attendance type for default date range (this_week). If attendance_type is not provided, returns all attendance types.

**Request Body (Empty - uses all defaults):**
```json
{}
```
*Defaults: `search_type="this_week"`, `attendance_type="all"`, `session_id="all"` (returns data from ALL sessions)*

**Request Body (Optional attendance_type):**
```json
{
  "search_type": "this_week"
}
```

**Request Body (With attendance_type):**
```json
{
  "attendance_type": 4,
  "search_type": "this_week"
}
```

**Request Body (With custom session):**
```json
{
  "attendance_type": 4,
  "search_type": "this_month",
  "session_id": 20
}
```

**Response:**
```json
{
  "status": true,
  "message": "Student attendance type report retrieved successfully",
  "filters_applied": {
    "search_type": "this_week",
    "attendance_type": 4,
    "attendance_type_name": "Absent",
    "attendance_type_key": "A"
  },
  "date_range": {
    "from": "2025-10-13",
    "to": "2025-10-19",
    "display": "13 Oct 2025 To 19 Oct 2025"
  },
  "session_id": 21,
  "total_records": 0,
  "data": [],
  "timestamp": "2025-10-14 09:20:00"
}
```

---

### 3. Get Available Attendance Types

**Endpoint:** `POST /api/student-attendance-type-report/attendance-types`

**Description:** Returns list of all available attendance types.

**Request Body:**
```json
{}
```

**Response:**
```json
{
  "status": true,
  "message": "Attendance types retrieved successfully",
  "total": 6,
  "data": [
    {
      "id": 1,
      "type": "Present",
      "key": "P",
      "description": "Student was present"
    },
    {
      "id": 2,
      "type": "Excuse",
      "key": "E",
      "description": "Student was excused"
    },
    {
      "id": 3,
      "type": "Late",
      "key": "L",
      "description": "Student was late"
    },
    {
      "id": 4,
      "type": "Absent",
      "key": "A",
      "description": "Student was absent"
    },
    {
      "id": 5,
      "type": "Holiday",
      "key": "H",
      "description": "Holiday"
    },
    {
      "id": 6,
      "type": "Half Day",
      "key": "HD",
      "description": "Student attended half day"
    }
  ],
  "timestamp": "2025-10-14 09:25:00"
}
```

---

## Search Type Options

The API supports the following search_type values for date range filtering:

| Search Type | Description | Date Range |
|-------------|-------------|------------|
| `today` | Today's date | Current day only |
| `this_week` | Current week | Sunday to Saturday of current week |
| `last_week` | Previous week | Sunday to Saturday of previous week |
| `this_month` | Current month | 1st to last day of current month |
| `last_month` | Previous month | 1st to last day of previous month |
| `last_3_months` | Last 3 months | 3 months back from today |
| `last_6_months` | Last 6 months | 6 months back from today |
| `last_12_months` | Last 12 months | 12 months back from today |
| `this_year` | Current year | Jan 1 to Dec 31 of current year |
| `last_year` | Previous year | Jan 1 to Dec 31 of previous year |
| `period` | Custom date range | Requires `date_from` and `date_to` |

### Using Custom Period

When using `search_type: "period"`, you must provide both `date_from` and `date_to`:

```json
{
  "attendance_type": 1,
  "class_id": 14,
  "search_type": "period",
  "date_from": "2025-08-01",
  "date_to": "2025-08-31"
}
```

---

## Request Parameters

### Filter Endpoint Parameters

| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `attendance_type` | integer | **No** | Attendance type ID (1-6). If not provided, returns all types | `1` |
| `class_id` | integer or array | **No** | Single or multiple class IDs. If not provided, returns all classes | `14` or `[14, 15]` |
| `section_id` | integer or array | No | Single or multiple section IDs. If not provided, returns all sections | `24` or `[24, 25]` |
| `search_type` | string | No | Date range filter type (default: `this_week`) | `"this_month"` |
| `date_from` | string | Conditional | Start date (required if search_type = 'period') | `"2025-08-01"` |
| `date_to` | string | Conditional | End date (required if search_type = 'period') | `"2025-08-31"` |
| `session_id` | integer | No | Session ID. If not provided, returns data from **ALL sessions** | `21` |

### List Endpoint Parameters

| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `attendance_type` | integer | **No** | Attendance type ID (1-6). If not provided, returns all types | `4` |
| `search_type` | string | No | Date range filter type (default: `this_week`) | `"this_week"` |
| `session_id` | integer | No | Session ID. If not provided, returns data from **ALL sessions** | `20` |

**Default Behavior:** All parameters are optional. Empty payload `{}` returns all attendance types for current week from **ALL sessions**.

### Multi-Select Support

The API supports multi-select for `class_id` and `section_id` parameters:

```json
{
  "attendance_type": 1,
  "class_id": [14, 15, 16],
  "section_id": [24, 25],
  "search_type": "this_month"
}
```

---

## Response Format

### Success Response Structure

```json
{
  "status": true,
  "message": "Success message",
  "filters_applied": { },
  "date_range": { },
  "total_records": 0,
  "data": [ ],
  "timestamp": "YYYY-MM-DD HH:MM:SS"
}
```

### Error Response Structure

```json
{
  "status": false,
  "message": "Error message",
  "error": "Detailed error information",
  "data": null
}
```

---

## Response Fields

### Main Response Fields

| Field | Type | Description |
|-------|------|-------------|
| `status` | boolean | Request success status (true/false) |
| `message` | string | Response message |
| `filters_applied` | object | Applied filters details |
| `date_range` | object | Date range information |
| `total_records` | integer | Total number of students returned |
| `data` | array | Array of student records |
| `timestamp` | string | Response timestamp |

### Student Data Fields

| Field | Type | Description |
|-------|------|-------------|
| `class_id` | string | Class ID |
| `id` | string | Student ID |
| `class` | string | Class name |
| `section_id` | string | Section ID |
| `section` | string | Section name |
| `admission_no` | string | Student admission number |
| `roll_no` | string/null | Student roll number |
| `admission_date` | string | Admission date |
| `firstname` | string | Student first name |
| `middlename` | string/null | Student middle name |
| `lastname` | string | Student last name |
| `mobileno` | string | Mobile number |
| `email` | string | Email address |
| `dob` | string | Date of birth |
| `father_name` | string | Father's name |
| `gender` | string | Student gender |
| `session_id` | string | Session ID |
| `total_type` | string | Count of attendance type occurrences |

---

## Usage Examples

### Example 1: Get All Attendance Types with Empty Payload (All Defaults)

```bash
curl -X POST "http://localhost/amt/api/student-attendance-type-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```
*This returns all attendance types, all classes, all sessions for current week*

### Example 2: Get All Data for Specific Date Range

```bash
curl -X POST "http://localhost/amt/api/student-attendance-type-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "search_type": "period",
    "date_from": "2025-08-01",
    "date_to": "2025-08-31"
  }'
```

### Example 3: Get All Attendance Types for a Specific Class

```bash
curl -X POST "http://localhost/amt/api/student-attendance-type-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "class_id": 14,
    "search_type": "this_month"
  }'
```

### Example 4: Get All Present Students (All Classes, All Sessions)

```bash
curl -X POST "http://localhost/amt/api/student-attendance-type-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "attendance_type": 1,
    "search_type": "period",
    "date_from": "2025-08-01",
    "date_to": "2025-08-31"
  }'
```

### Example 5: Get Absent Students by Class (This Month)

```bash
curl -X POST "http://localhost/amt/api/student-attendance-type-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "attendance_type": 3,
    "class_id": 14,
    "section_id": 24,
    "search_type": "period",
    "date_from": "2025-08-01",
    "date_to": "2025-08-31"
  }'
```

### Example 6: Get Students with Excuse by Multiple Classes (Last Week)

```bash
curl -X POST "http://localhost/amt/api/student-attendance-type-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "attendance_type": 2,
    "class_id": [14, 15, 16],
    "search_type": "last_week"
  }'
```

### Example 7: Get Available Attendance Types

```bash
curl -X POST "http://localhost/amt/api/student-attendance-type-report/attendance-types" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

## Error Handling

### Common Error Codes

| Status Code | Description |
|-------------|-------------|
| 400 | Bad Request - Invalid or missing required parameters |
| 401 | Unauthorized - Invalid or missing authentication |
| 500 | Internal Server Error - Server-side error |

### Error Response Examples

#### Missing Required Parameter
```json
{
  "status": false,
  "message": "Attendance type is required"
}
```

#### Invalid Search Type
```json
{
  "status": false,
  "message": "date_from and date_to are required when search_type is \"period\""
}
```

#### Authentication Failed
```json
{
  "status": false,
  "message": "Unauthorized access"
}
```

---

## Code Examples

### JavaScript (Fetch API)

```javascript
const getStudentAttendanceTypeReport = async (attendanceType, classId, sectionId = null, searchType = 'this_week') => {
  try {
    const requestBody = {
      attendance_type: attendanceType,
      class_id: classId,
      search_type: searchType
    };

    if (sectionId) {
      requestBody.section_id = sectionId;
    }

    const response = await fetch('http://localhost/amt/api/student-attendance-type-report/filter', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Client-Service': 'smartschool',
        'Auth-Key': 'schoolAdmin@'
      },
      body: JSON.stringify(requestBody)
    });
    
    const data = await response.json();
    
    if (data.status) {
      console.log('Total Students:', data.total_records);
      console.log('Date Range:', data.date_range.display);
      console.log('Attendance Type:', data.filters_applied.attendance_type_name);
      console.log('Students:', data.data);
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

// Usage Examples
getStudentAttendanceTypeReport(1, 14, 24, 'this_month'); // Present students
getStudentAttendanceTypeReport(4, 14, null, 'last_week'); // Absent students
```

### PHP (cURL)

```php
<?php
function getStudentAttendanceTypeReport($attendanceType, $classId, $sectionId = null, $searchType = 'this_week', $dateFrom = null, $dateTo = null) {
    $url = 'http://localhost/amt/api/student-attendance-type-report/filter';
    
    $data = array(
        'attendance_type' => $attendanceType,
        'class_id' => $classId,
        'search_type' => $searchType
    );
    
    if ($sectionId !== null) {
        $data['section_id'] = $sectionId;
    }
    
    if ($searchType === 'period' && $dateFrom && $dateTo) {
        $data['date_from'] = $dateFrom;
        $data['date_to'] = $dateTo;
    }
    
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
        if ($result['status']) {
            echo "Total Students: " . $result['total_records'] . "\n";
            echo "Date Range: " . $result['date_range']['display'] . "\n";
            echo "Attendance Type: " . $result['filters_applied']['attendance_type_name'] . "\n";
            return $result['data'];
        }
    }
    
    return null;
}

// Usage Examples
$presentStudents = getStudentAttendanceTypeReport(1, 14, 24, 'this_month');
$absentStudents = getStudentAttendanceTypeReport(4, 14, null, 'last_week');
$lateStudents = getStudentAttendanceTypeReport(3, 14, 24, 'period', '2025-08-01', '2025-08-31');
?>
```

### Python (Requests)

```python
import requests
import json
from datetime import datetime

def get_student_attendance_type_report(attendance_type, class_id, section_id=None, 
                                        search_type='this_week', date_from=None, date_to=None):
    url = 'http://localhost/amt/api/student-attendance-type-report/filter'
    
    headers = {
        'Content-Type': 'application/json',
        'Client-Service': 'smartschool',
        'Auth-Key': 'schoolAdmin@'
    }
    
    data = {
        'attendance_type': attendance_type,
        'class_id': class_id,
        'search_type': search_type
    }
    
    if section_id is not None:
        data['section_id'] = section_id
    
    if search_type == 'period' and date_from and date_to:
        data['date_from'] = date_from
        data['date_to'] = date_to
    
    try:
        response = requests.post(url, headers=headers, json=data)
        
        if response.status_code == 200:
            result = response.json()
            if result['status']:
                print(f"Total Students: {result['total_records']}")
                print(f"Date Range: {result['date_range']['display']}")
                print(f"Attendance Type: {result['filters_applied']['attendance_type_name']}")
                return result['data']
            else:
                print(f"Error: {result['message']}")
                return None
    except Exception as e:
        print(f"Request failed: {str(e)}")
        return None

# Usage Examples
present_students = get_student_attendance_type_report(1, 14, 24, 'this_month')
absent_students = get_student_attendance_type_report(4, 14, None, 'last_week')
late_students = get_student_attendance_type_report(3, 14, 24, 'period', '2025-08-01', '2025-08-31')
```

### Android Kotlin

```kotlin
import kotlinx.coroutines.*
import okhttp3.*
import okhttp3.MediaType.Companion.toMediaType
import okhttp3.RequestBody.Companion.toRequestBody
import org.json.JSONArray
import org.json.JSONObject

class StudentAttendanceTypeReportAPI {
    private val client = OkHttpClient()
    private val baseUrl = "http://yourserver.com/amt/api"
    
    data class AttendanceTypeReportRequest(
        val attendanceType: Int,
        val classId: Int,
        val sectionId: Int? = null,
        val searchType: String = "this_week",
        val dateFrom: String? = null,
        val dateTo: String? = null
    )
    
    data class StudentData(
        val id: String,
        val admissionNo: String,
        val firstname: String,
        val middlename: String?,
        val lastname: String,
        val className: String,
        val section: String,
        val totalType: String
    )
    
    suspend fun getStudentAttendanceTypeReport(request: AttendanceTypeReportRequest): List<StudentData>? {
        return withContext(Dispatchers.IO) {
            try {
                val jsonObject = JSONObject().apply {
                    put("attendance_type", request.attendanceType)
                    put("class_id", request.classId)
                    request.sectionId?.let { put("section_id", it) }
                    put("search_type", request.searchType)
                    request.dateFrom?.let { put("date_from", it) }
                    request.dateTo?.let { put("date_to", it) }
                }
                
                val requestBody = jsonObject.toString()
                    .toRequestBody("application/json".toMediaType())
                
                val httpRequest = Request.Builder()
                    .url("$baseUrl/student-attendance-type-report/filter")
                    .post(requestBody)
                    .addHeader("Content-Type", "application/json")
                    .addHeader("Client-Service", "smartschool")
                    .addHeader("Auth-Key", "schoolAdmin@")
                    .build()
                
                val response = client.newCall(httpRequest).execute()
                val responseBody = response.body?.string()
                
                if (response.isSuccessful && responseBody != null) {
                    val jsonResponse = JSONObject(responseBody)
                    if (jsonResponse.getBoolean("status")) {
                        val dataArray = jsonResponse.getJSONArray("data")
                        val students = mutableListOf<StudentData>()
                        
                        for (i in 0 until dataArray.length()) {
                            val student = dataArray.getJSONObject(i)
                            students.add(
                                StudentData(
                                    id = student.getString("id"),
                                    admissionNo = student.getString("admission_no"),
                                    firstname = student.getString("firstname"),
                                    middlename = student.optString("middlename"),
                                    lastname = student.getString("lastname"),
                                    className = student.getString("class"),
                                    section = student.getString("section"),
                                    totalType = student.getString("total_type")
                                )
                            )
                        }
                        return@withContext students
                    }
                }
                null
            } catch (e: Exception) {
                e.printStackTrace()
                null
            }
        }
    }
}

// Usage Example
fun main() = runBlocking {
    val api = StudentAttendanceTypeReportAPI()
    
    // Get present students
    val presentStudents = api.getStudentAttendanceTypeReport(
        StudentAttendanceTypeReportAPI.AttendanceTypeReportRequest(
            attendanceType = 1,
            classId = 14,
            sectionId = 24,
            searchType = "this_month"
        )
    )
    
    presentStudents?.forEach { student ->
        println("${student.firstname} ${student.lastname} - ${student.className} (${student.section})")
        println("Present Days: ${student.totalType}")
    }
}
```

---

## Testing

### PowerShell Test Commands

```powershell
# Set up headers
$headers = @{
    "Content-Type" = "application/json"
    "Client-Service" = "smartschool"
    "Auth-Key" = "schoolAdmin@"
}

# Test 1: Get present students (this week) - all classes
$body1 = @{ attendance_type = 1 } | ConvertTo-Json
$response1 = Invoke-RestMethod -Uri "http://localhost/amt/api/student-attendance-type-report/list" -Method POST -Headers $headers -Body $body1
Write-Host "Present Students This Week: $($response1.total_records)"

# Test 2: Get absent students by class (this month)
$body2 = @{ attendance_type = 4; class_id = 14; search_type = "this_month" } | ConvertTo-Json
$response2 = Invoke-RestMethod -Uri "http://localhost/amt/api/student-attendance-type-report/filter" -Method POST -Headers $headers -Body $body2
Write-Host "Absent Students This Month (Class 14): $($response2.total_records)"

# Test 3: Get late students by class and section (custom period)
$body3 = @{ 
    attendance_type = 3
    class_id = 14
    section_id = 24
    search_type = "period"
    date_from = "2025-08-01"
    date_to = "2025-08-31"
} | ConvertTo-Json
$response3 = Invoke-RestMethod -Uri "http://localhost/amt/api/student-attendance-type-report/filter" -Method POST -Headers $headers -Body $body3
Write-Host "Late Students in August (Class 14, Section 24): $($response3.total_records)"

# Test 4: Get attendance types
$response4 = Invoke-RestMethod -Uri "http://localhost/amt/api/student-attendance-type-report/attendance-types" -Method POST -Headers $headers
Write-Host "Available Attendance Types: $($response4.total)"
$response4.data | Format-Table id, type, key, description
```

---

## Notes

1. **Date Format:** All dates should be in `YYYY-MM-DD` format.
2. **Session Handling:** If `session_id` is not provided, the API returns data from **ALL sessions**. You can filter to a specific session by providing `session_id` in the request.
3. **Class Handling:** If `class_id` is not provided, the API returns data from **ALL classes**. You can filter to specific classes by providing `class_id` in the request.
4. **Active Students Only:** The API returns data only for active students (`is_active = 'yes'`).
5. **Total Type Count:** The `total_type` field shows the count of attendance type occurrences for each student within the date range.
6. **Search Type Default:** If `search_type` is not provided, it defaults to `'this_week'`.
7. **Period Validation:** When using `search_type = 'period'`, both `date_from` and `date_to` are mandatory.
8. **Multi-Select:** Both `class_id` and `section_id` support single values or arrays for multiple selections.
9. **Optional Attendance Type:** If `attendance_type` is not provided, the API returns students with all attendance types. When provided, it filters by that specific type only.
10. **Empty Payload:** Both endpoints accept an empty payload `{}` which uses all defaults: `search_type="this_week"`, `attendance_type="all"`, `class_id="all"`, `session_id="all"` (returns data from all classes and all sessions).
11. **Fully Optional Filters:** All parameters are now optional. You can filter by any combination of attendance_type, class_id, section_id, session_id, or use none at all to get complete data.

---

## Differences from Other Attendance APIs

### vs. Attendance Report API (`attendance_report_api`)
- **Attendance Report API:** Returns all attendance records with total count
- **Student Attendance Type Report API:** Returns only students with specific attendance type

### vs. Class Attendance Report API (`class_attendance_report_api`)
- **Class Attendance Report API:** Returns monthly statistics with breakdown by type
- **Student Attendance Type Report API:** Returns students filtered by single attendance type

### vs. Daily Attendance Report API (`daily_attendance_report_api`)
- **Daily Attendance Report API:** Returns daily summary statistics by class
- **Student Attendance Type Report API:** Returns individual students with attendance type count

---

## API Routes Configuration

The following routes are configured in `api/application/config/routes.php`:

```php
// Student Attendance Type Report API Routes
$route['student-attendance-type-report/filter']['POST'] = 'student_attendance_type_report_api/filter';
$route['student-attendance-type-report/list']['POST'] = 'student_attendance_type_report_api/list';
$route['student-attendance-type-report/attendance-types']['POST'] = 'student_attendance_type_report_api/attendance_types';
```

---

## Support

For issues or questions regarding this API, please contact the development team or refer to the main API documentation.

**API Controller:** `api/application/controllers/Student_attendance_type_report_api.php`

**Model Method:** `api/application/models/Stuattendence_model.php::getStudentAttendanceTypeReport()`

**Last Updated:** October 2025

**Version:** 1.0.0
