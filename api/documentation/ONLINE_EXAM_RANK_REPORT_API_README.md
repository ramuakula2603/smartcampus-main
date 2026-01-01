# Online Exam Rank Report API Documentation

## Overview

The Online Exam Rank Report API provides endpoints for retrieving online exam rank reports with student rankings based on online exam performance. This API returns detailed information about student rankings, question-wise results, scores, and attempt status.

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

### 1. Filter Online Exam Rank Report

**Endpoint:** `POST /api/online-exam-rank-report/filter`

**Description:** Retrieves online exam rank report data with optional filtering by exam, class, and section.

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
  "message": "Online exam rank report retrieved successfully",
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
    "is_rank_generated": "1"
  },
  "total_records": 30,
  "data": [
    {
      "onlineexam_student_id": "150",
      "student_id": "50",
      "admission_no": "2024001",
      "firstname": "John",
      "lastname": "Doe",
      "roll_no": "1",
      "class": "Class 10",
      "section": "A",
      "rank": "1",
      "is_attempted": "1",
      "total_marks": "95",
      "percentage": "95.00",
      "questions_results": {
        "correct_answers": 19,
        "incorrect_answers": 1,
        "total_questions": 20,
        "marks_obtained": "95",
        "total_marks": "100"
      }
    }
  ],
  "timestamp": "2025-10-07 10:30:00"
}
```

---

### 2. List All Online Exams

**Endpoint:** `POST /api/online-exam-rank-report/list`

**Description:** Retrieves all online exams. Use this to get available exams before filtering.

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
  "message": "Online exam rank report data retrieved successfully",
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
      "is_rank_generated": "1"
    }
  ],
  "total_records": 2,
  "note": "Use the filter endpoint with exam_id to get detailed rank report for a specific online exam",
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

### Example 1: Get All Online Exams

```bash
curl -X POST "http://localhost/amt/api/online-exam-rank-report/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 2: Get Rank Report for Specific Online Exam

```bash
curl -X POST "http://localhost/amt/api/online-exam-rank-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "exam_id": 10
  }'
```

### Example 3: Get Rank Report for Specific Class

```bash
curl -X POST "http://localhost/amt/api/online-exam-rank-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "exam_id": 10,
    "class_id": 1,
    "section_id": 2
  }'
```

### Example 4: Get Rank Report for Multiple Classes

```bash
curl -X POST "http://localhost/amt/api/online-exam-rank-report/filter" \
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
const getOnlineExamRankReport = async (examId, classId, sectionId) => {
  try {
    const response = await fetch('http://localhost/amt/api/online-exam-rank-report/filter', {
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
      console.log('Online Exam Rank Report:', data.data);
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
getOnlineExamRankReport(10, 1, 2);
```

### PHP (cURL)

```php
<?php
function getOnlineExamRankReport($examId, $classId = null, $sectionId = null) {
    $url = 'http://localhost/amt/api/online-exam-rank-report/filter';
    
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
            return $result['data'];
        }
    }
    
    return null;
}

// Usage
$rankReport = getOnlineExamRankReport(10, 1, 2);
print_r($rankReport);
?>
```

### Python (Requests)

```python
import requests
import json

def get_online_exam_rank_report(exam_id, class_id=None, section_id=None):
    url = 'http://localhost/amt/api/online-exam-rank-report/filter'
    
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
                print(f"Total Records: {result['total_records']}")
                return result['data']
            else:
                print(f"Error: {result['message']}")
                return None
    except Exception as e:
        print(f"Request failed: {str(e)}")
        return None

# Usage
rank_report = get_online_exam_rank_report(10, 1, 2)
print(rank_report)
```

---

## Notes

1. **Exam ID Required:** To get detailed rank report with student data, `exam_id` parameter is required.
2. **Attempt Status:** The API returns data only for students who have attempted the exam (`is_attempted = 1`).
3. **Multi-Select:** Both `class_id` and `section_id` support single values or arrays for filtering multiple classes/sections.
4. **Rank Generation:** The API returns rank data only if ranks have been generated for the exam (`is_rank_generated = 1`).
5. **Question Results:** Each student record includes detailed question-wise results with correct/incorrect answers and marks obtained.

---

## Support

For issues or questions regarding this API, please contact the development team or refer to the main API documentation.

**Last Updated:** October 2025

