# Rank Report API Documentation

## Overview

The Rank Report API provides endpoints for retrieving exam rank reports with student rankings based on exam performance. This API returns detailed information about student rankings, subject-wise results, exam details, and overall performance metrics.

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

### 1. Filter Rank Report

**Endpoint:** `POST /api/rank-report/filter`

**Description:** Retrieves rank report data with optional filtering by exam group, exam, class, section, and session.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Request Body:**
```json
{
  "exam_group_id": 1,
  "exam_id": 5,
  "class_id": 1,
  "section_id": 2,
  "session_id": 18
}
```

**Response:**
```json
{
  "status": 1,
  "message": "Rank report retrieved successfully",
  "filters_applied": {
    "exam_group_id": 1,
    "exam_id": 5,
    "class_id": [1],
    "section_id": [2],
    "session_id": 18
  },
  "exam_details": {
    "id": "5",
    "exam_group_id": "1",
    "exam_name": "Mid Term Exam",
    "exam_group_type": "1",
    "is_rank_generated": "1"
  },
  "exam_subjects": [
    {
      "id": "10",
      "subject_id": "1",
      "subject_name": "Mathematics",
      "subject_code": "MATH101",
      "full_marks": "100",
      "passing_marks": "40"
    }
  ],
  "total_records": 25,
  "data": [
    {
      "exam_group_class_batch_exam_student_id": "100",
      "student_id": "50",
      "admission_no": "2024001",
      "firstname": "John",
      "lastname": "Doe",
      "roll_no": "1",
      "class": "Class 10",
      "section": "A",
      "rank": "1",
      "total_marks": "450",
      "percentage": "90.00",
      "subject_results": [
        {
          "subject_id": "1",
          "subject_name": "Mathematics",
          "full_marks": "100",
          "passing_marks": "40",
          "get_marks": "95",
          "attendence": "present",
          "grade": "A+"
        }
      ]
    }
  ],
  "timestamp": "2025-10-07 10:30:00"
}
```

---

### 2. List All Exam Groups

**Endpoint:** `POST /api/rank-report/list`

**Description:** Retrieves all exam groups for the current session. Use this to get available exam groups before filtering.

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
  "message": "Rank report data retrieved successfully",
  "session_id": 18,
  "exam_groups": [
    {
      "id": "1",
      "name": "Term 1 Exams",
      "exam_group_type": "1",
      "description": "First term examinations"
    },
    {
      "id": "2",
      "name": "Term 2 Exams",
      "exam_group_type": "1",
      "description": "Second term examinations"
    }
  ],
  "total_records": 2,
  "note": "Use the filter endpoint with exam_id to get detailed rank report for a specific exam",
  "timestamp": "2025-10-07 10:30:00"
}
```

---

## Filter Parameters

All filter parameters are **optional**. If no parameters are provided, the API returns available exam groups.

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `exam_group_id` | integer | Exam group ID | `1` |
| `exam_id` | integer | Exam ID (required for detailed report) | `5` |
| `class_id` | integer or array | Single or multiple class IDs | `1` or `[1, 2, 3]` |
| `section_id` | integer or array | Single or multiple section IDs | `2` or `[1, 2]` |
| `session_id` | integer | Session ID (defaults to current session) | `18` |

### Multi-Select Support

The API supports multi-select for `class_id` and `section_id` parameters:

```json
{
  "exam_group_id": 1,
  "exam_id": 5,
  "class_id": [1, 2, 3],
  "section_id": [1, 2]
}
```

### Graceful Null/Empty Handling

- Empty parameters (`{}`) return all available exam groups
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
  "exam_details": { },
  "exam_subjects": [ ],
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

### Example 1: Get All Exam Groups

```bash
curl -X POST "http://localhost/amt/api/rank-report/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 2: Get Rank Report for Specific Exam

```bash
curl -X POST "http://localhost/amt/api/rank-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "exam_group_id": 1,
    "exam_id": 5,
    "class_id": 1,
    "section_id": 2
  }'
```

### Example 3: Get Rank Report for Multiple Classes

```bash
curl -X POST "http://localhost/amt/api/rank-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "exam_group_id": 1,
    "exam_id": 5,
    "class_id": [1, 2, 3],
    "section_id": [1, 2]
  }'
```

### Example 4: Get Rank Report for All Students in an Exam

```bash
curl -X POST "http://localhost/amt/api/rank-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "exam_group_id": 1,
    "exam_id": 5
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

### Error Response Examples

**401 Unauthorized:**
```json
{
  "status": 0,
  "message": "Unauthorized access"
}
```

**500 Internal Server Error:**
```json
{
  "status": 0,
  "message": "Internal server error occurred",
  "error": "Database connection failed",
  "data": null
}
```

---

## Code Examples

### JavaScript (Fetch API)

```javascript
const getRankReport = async (examGroupId, examId, classId, sectionId) => {
  try {
    const response = await fetch('http://localhost/amt/api/rank-report/filter', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Client-Service': 'smartschool',
        'Auth-Key': 'schoolAdmin@'
      },
      body: JSON.stringify({
        exam_group_id: examGroupId,
        exam_id: examId,
        class_id: classId,
        section_id: sectionId
      })
    });
    
    const data = await response.json();
    
    if (data.status === 1) {
      console.log('Rank Report:', data.data);
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
getRankReport(1, 5, 1, 2);
```

### PHP (cURL)

```php
<?php
function getRankReport($examGroupId, $examId, $classId, $sectionId) {
    $url = 'http://localhost/amt/api/rank-report/filter';
    
    $data = array(
        'exam_group_id' => $examGroupId,
        'exam_id' => $examId,
        'class_id' => $classId,
        'section_id' => $sectionId
    );
    
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
$rankReport = getRankReport(1, 5, 1, 2);
print_r($rankReport);
?>
```

### Python (Requests)

```python
import requests
import json

def get_rank_report(exam_group_id, exam_id, class_id, section_id):
    url = 'http://localhost/amt/api/rank-report/filter'
    
    headers = {
        'Content-Type': 'application/json',
        'Client-Service': 'smartschool',
        'Auth-Key': 'schoolAdmin@'
    }
    
    data = {
        'exam_group_id': exam_group_id,
        'exam_id': exam_id,
        'class_id': class_id,
        'section_id': section_id
    }
    
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
rank_report = get_rank_report(1, 5, 1, 2)
print(rank_report)
```

---

## Notes

1. **Exam ID Required:** To get detailed rank report with student data, `exam_id` parameter is required.
2. **Session Handling:** If `session_id` is not provided, the API uses the current active session.
3. **Multi-Select:** Both `class_id` and `section_id` support single values or arrays for filtering multiple classes/sections.
4. **Rank Generation:** The API returns rank data only if ranks have been generated for the exam (`is_rank_generated = 1`).
5. **Subject Results:** Each student record includes detailed subject-wise results with marks, attendance, and grades.

---

## Support

For issues or questions regarding this API, please contact the development team or refer to the main API documentation.

**Last Updated:** October 2025

