# Online Exam Report API Documentation

## Overview

The Online Exam Report API provides endpoints for retrieving detailed online exam reports including exam information and student participation data. This API returns comprehensive information about specific online exams with student assignment details.

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

### 1. Filter Online Exam Report

**Endpoint:** `POST /api/online-exam-report/filter`

**Description:** Retrieves detailed online exam report data with optional filtering by exam, class, and section.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Request Body:**
```json
{
  "exam_id": 10,
  "class_id": 1,
  "section_id": 2
}
```

**Response:**
```json
{
  "status": 1,
  "message": "Online exam report retrieved successfully",
  "filters_applied": {
    "exam_id": 10,
    "class_id": [1],
    "section_id": [2]
  },
  "exam": {
    "id": "10",
    "exam": "Mathematics Quiz",
    "attempt": "1",
    "exam_from": "2025-10-01 09:00:00",
    "exam_to": "2025-10-01 10:00:00",
    "duration": "60",
    "passing_percentage": "40",
    "description": "Mid-term mathematics assessment",
    "session_id": "18",
    "is_active": "1",
    "is_rank_generated": "1",
    "publish_result": "1"
  },
  "total_students": 30,
  "students": [
    {
      "onlineexam_student_id": "150",
      "student_id": "50",
      "admission_no": "2024001",
      "firstname": "John",
      "lastname": "Doe",
      "roll_no": "1",
      "class": "Class 10",
      "section": "A",
      "is_attempted": "1",
      "rank": "1"
    },
    {
      "onlineexam_student_id": "151",
      "student_id": "51",
      "admission_no": "2024002",
      "firstname": "Jane",
      "lastname": "Smith",
      "roll_no": "2",
      "class": "Class 10",
      "section": "A",
      "is_attempted": "0",
      "rank": null
    }
  ],
  "timestamp": "2025-10-07 10:30:00"
}
```

---

### 2. List All Online Exams

**Endpoint:** `POST /api/online-exam-report/list`

**Description:** Retrieves all online exams with class list. Use this to get available exams before filtering.

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
  "message": "Online exam report data retrieved successfully",
  "exams": [
    {
      "id": "10",
      "exam": "Mathematics Quiz",
      "attempt": "1",
      "exam_from": "2025-10-01 09:00:00",
      "exam_to": "2025-10-01 10:00:00",
      "duration": "60",
      "passing_percentage": "40",
      "is_rank_generated": "1"
    },
    {
      "id": "11",
      "exam": "Science Test",
      "attempt": "1",
      "exam_from": "2025-10-05 10:00:00",
      "exam_to": "2025-10-05 11:00:00",
      "duration": "60",
      "passing_percentage": "40",
      "is_rank_generated": "0"
    }
  ],
  "classes": [
    {
      "id": "1",
      "class": "Class 10"
    },
    {
      "id": "2",
      "class": "Class 9"
    }
  ],
  "total_exams": 2,
  "note": "Use the filter endpoint with exam_id to get detailed report for a specific online exam",
  "timestamp": "2025-10-07 10:30:00"
}
```

---

## Filter Parameters

All filter parameters are **optional**. If no parameters are provided, the API returns available online exams.

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `exam_id` | integer | Online exam ID (required for detailed report) | `10` |
| `class_id` | integer or array | Single or multiple class IDs | `1` or `[1, 2, 3]` |
| `section_id` | integer or array | Single or multiple section IDs | `2` or `[1, 2]` |

### Multi-Select Support

The API supports multi-select for `class_id` and `section_id` parameters:

```json
{
  "exam_id": 10,
  "class_id": [1, 2, 3],
  "section_id": [1, 2]
}
```

### Graceful Null/Empty Handling

- Empty parameters (`{}`) return all available online exams
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
  "exam": { },
  "total_students": 0,
  "students": [ ],
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

### Exam Fields

| Field | Type | Description |
|-------|------|-------------|
| `id` | string | Online exam ID |
| `exam` | string | Exam name |
| `attempt` | string | Number of attempts allowed |
| `exam_from` | string | Exam start date and time |
| `exam_to` | string | Exam end date and time |
| `duration` | string | Exam duration in minutes |
| `passing_percentage` | string | Minimum passing percentage |
| `description` | string | Exam description |
| `session_id` | string | Session ID |
| `is_active` | string | Active status (1=active, 0=inactive) |
| `is_rank_generated` | string | Rank generation status (1=generated, 0=not generated) |
| `publish_result` | string | Result publication status (1=published, 0=not published) |

### Student Fields

| Field | Type | Description |
|-------|------|-------------|
| `onlineexam_student_id` | string | Online exam student assignment ID |
| `student_id` | string | Student ID |
| `admission_no` | string | Student admission number |
| `firstname` | string | Student first name |
| `lastname` | string | Student last name |
| `roll_no` | string | Student roll number |
| `class` | string | Class name |
| `section` | string | Section name |
| `is_attempted` | string | Attempt status (1=attempted, 0=not attempted) |
| `rank` | string | Student rank (null if not attempted or rank not generated) |

---

## Usage Examples

### Example 1: Get All Online Exams

```bash
curl -X POST "http://localhost/amt/api/online-exam-report/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 2: Get Report for Specific Exam

```bash
curl -X POST "http://localhost/amt/api/online-exam-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "exam_id": 10
  }'
```

### Example 3: Get Report for Specific Class

```bash
curl -X POST "http://localhost/amt/api/online-exam-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "exam_id": 10,
    "class_id": 1,
    "section_id": 2
  }'
```

### Example 4: Get Report for Multiple Classes

```bash
curl -X POST "http://localhost/amt/api/online-exam-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "exam_id": 10,
    "class_id": [1, 2, 3],
    "section_id": [1, 2]
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
const getOnlineExamReport = async (examId, classId, sectionId) => {
  try {
    const response = await fetch('http://localhost/amt/api/online-exam-report/filter', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Client-Service': 'smartschool',
        'Auth-Key': 'schoolAdmin@'
      },
      body: JSON.stringify({
        exam_id: examId,
        class_id: classId,
        section_id: sectionId
      })
    });
    
    const data = await response.json();
    
    if (data.status === 1) {
      console.log('Online Exam Report:', data.exam);
      console.log('Total Students:', data.total_students);
      console.log('Students:', data.students);
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
getOnlineExamReport(10, 1, 2);
```

### PHP (cURL)

```php
<?php
function getOnlineExamReport($examId, $classId = null, $sectionId = null) {
    $url = 'http://localhost/amt/api/online-exam-report/filter';
    
    $data = array('exam_id' => $examId);
    if ($classId !== null) $data['class_id'] = $classId;
    if ($sectionId !== null) $data['section_id'] = $sectionId;
    
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
$examReport = getOnlineExamReport(10, 1, 2);
print_r($examReport);
?>
```

### Python (Requests)

```python
import requests
import json

def get_online_exam_report(exam_id, class_id=None, section_id=None):
    url = 'http://localhost/amt/api/online-exam-report/filter'
    
    headers = {
        'Content-Type': 'application/json',
        'Client-Service': 'smartschool',
        'Auth-Key': 'schoolAdmin@'
    }
    
    data = {'exam_id': exam_id}
    if class_id is not None:
        data['class_id'] = class_id
    if section_id is not None:
        data['section_id'] = section_id
    
    try:
        response = requests.post(url, headers=headers, json=data)
        
        if response.status_code == 200:
            result = response.json()
            if result['status'] == 1:
                print(f"Total Students: {result['total_students']}")
                return result
            else:
                print(f"Error: {result['message']}")
                return None
    except Exception as e:
        print(f"Request failed: {str(e)}")
        return None

# Usage
exam_report = get_online_exam_report(10, 1, 2)
print(exam_report)
```

---

## Notes

1. **Exam ID Required:** To get detailed report with student data, `exam_id` parameter is required.
2. **Student List:** The API returns all students assigned to the exam, regardless of attempt status.
3. **Attempt Status:** The `is_attempted` field indicates whether each student has attempted the exam.
4. **Rank Information:** Rank is only available if the student has attempted the exam and ranks have been generated.
5. **Multi-Select:** Both `class_id` and `section_id` support single values or arrays for filtering multiple classes/sections.

---

## Support

For issues or questions regarding this API, please contact the development team or refer to the main API documentation.

**Last Updated:** October 2025

