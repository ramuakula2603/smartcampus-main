# Teacher Syllabus Status Report API Documentation

## Overview

The Teacher Syllabus Status Report API provides endpoints for retrieving teacher-wise syllabus completion status for a specific subject. This API returns detailed information about which teachers are teaching which topics, their completion status, and period-wise syllabus details.

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

### 1. Filter Teacher Syllabus Status Report

**Endpoint:** `POST /api/teacher-syllabus-status-report/filter`

**Description:** Retrieves teacher-wise syllabus completion status for a specific subject.

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
  "subject_group_id": 3,
  "subject_id": 5,
  "session_id": 18
}
```

**Response:**
```json
{
  "status": 1,
  "message": "Teacher syllabus status report retrieved successfully",
  "filters_applied": {
    "class_id": 1,
    "section_id": 2,
    "subject_group_id": 3,
    "subject_id": 5,
    "session_id": 18
  },
  "subject_group_info": {
    "id": "10",
    "name": "Science Group"
  },
  "subject_info": {
    "subject_id": "5",
    "subject_name": "Mathematics",
    "subject_code": "MATH101",
    "label": "Mathematics (MATH101)",
    "complete_percentage": 75.5,
    "incomplete_percentage": 24.5,
    "complete_count": "15",
    "incomplete_count": "5",
    "total_topics": "20"
  },
  "total_teachers": 2,
  "teachers_summary": [
    {
      "teacher_name": "John Smith (EMP001)",
      "total_periods": "12",
      "syllabus_details": [
        {
          "id": "100",
          "topic_id": "50",
          "lesson_id": "25",
          "created_for": "10",
          "created_by": "1",
          "created_at": "2025-10-01 10:00:00",
          "time_from": "09:00:00",
          "time_to": "10:00:00",
          "presentation": "",
          "attachment": "",
          "lacture_youtube_url": "",
          "lacture_video": "",
          "sub_topic": "Introduction to Algebra",
          "teaching_method": "Lecture",
          "general_objectives": "Understand basic algebra concepts",
          "previous_knowledge": "Basic arithmetic",
          "comprehensive_questions": "What is algebra?",
          "status": "1",
          "lesson_name": "Algebra Basics",
          "topic_name": "Introduction"
        }
      ]
    },
    {
      "teacher_name": "Jane Doe (EMP002)",
      "total_periods": "8",
      "syllabus_details": [
        {
          "id": "101",
          "topic_id": "51",
          "lesson_id": "26",
          "created_for": "11",
          "created_by": "1",
          "created_at": "2025-10-02 10:00:00",
          "time_from": "10:00:00",
          "time_to": "11:00:00",
          "presentation": "",
          "attachment": "",
          "lacture_youtube_url": "",
          "lacture_video": "",
          "sub_topic": "Advanced Algebra",
          "teaching_method": "Discussion",
          "general_objectives": "Master advanced concepts",
          "previous_knowledge": "Basic algebra",
          "comprehensive_questions": "Solve equations",
          "status": "1",
          "lesson_name": "Advanced Topics",
          "topic_name": "Equations"
        }
      ]
    }
  ],
  "timestamp": "2025-10-07 10:30:00"
}
```

---

### 2. List All Classes

**Endpoint:** `POST /api/teacher-syllabus-status-report/list`

**Description:** Retrieves all available classes. Use this to get class options before filtering.

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
  "message": "Classes retrieved successfully",
  "total_classes": 10,
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
  "note": "Use the filter endpoint with class_id, section_id, subject_group_id, and subject_id to get teacher syllabus status report",
  "timestamp": "2025-10-07 10:30:00"
}
```

---

## Filter Parameters

| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `class_id` | integer | **Yes** | Class ID | `1` |
| `section_id` | integer | **Yes** | Section ID | `2` |
| `subject_group_id` | integer | **Yes** | Subject group ID | `3` |
| `subject_id` | integer | **Yes** | Subject ID | `5` |
| `session_id` | integer | No | Session ID (defaults to current session) | `18` |

### Parameter Notes

- **class_id, section_id, subject_group_id, subject_id** are required for the filter endpoint
- **session_id** is optional and defaults to the current active session
- All parameters must be valid integers

---

## Response Format

### Success Response

```json
{
  "status": 1,
  "message": "Success message",
  "filters_applied": { },
  "subject_group_info": { },
  "subject_info": { },
  "total_teachers": 0,
  "teachers_summary": [ ],
  "timestamp": "YYYY-MM-DD HH:MM:SS"
}
```

### Error Response

```json
{
  "status": 0,
  "message": "Error message",
  "error": "Detailed error information"
}
```

---

## Response Fields

### Subject Info Fields

| Field | Type | Description |
|-------|------|-------------|
| `subject_id` | string | Subject ID |
| `subject_name` | string | Subject name |
| `subject_code` | string | Subject code |
| `label` | string | Subject display label (name with code) |
| `complete_percentage` | number | Percentage of completed topics |
| `incomplete_percentage` | number | Percentage of incomplete topics |
| `complete_count` | string | Number of completed topics |
| `incomplete_count` | string | Number of incomplete topics |
| `total_topics` | string | Total number of topics |

### Teacher Summary Fields

| Field | Type | Description |
|-------|------|-------------|
| `teacher_name` | string | Teacher name with employee ID |
| `total_periods` | string | Total number of periods taught |
| `syllabus_details` | array | Array of syllabus period details |

### Syllabus Details Fields

| Field | Type | Description |
|-------|------|-------------|
| `id` | string | Syllabus ID |
| `topic_id` | string | Topic ID |
| `lesson_id` | string | Lesson ID |
| `created_for` | string | Staff ID (teacher) |
| `created_by` | string | Creator ID |
| `created_at` | string | Creation timestamp |
| `time_from` | string | Period start time |
| `time_to` | string | Period end time |
| `sub_topic` | string | Sub-topic name |
| `teaching_method` | string | Teaching method used |
| `general_objectives` | string | Learning objectives |
| `previous_knowledge` | string | Required prior knowledge |
| `comprehensive_questions` | string | Assessment questions |
| `status` | string | Completion status |
| `lesson_name` | string | Lesson name |
| `topic_name` | string | Topic name |

---

## Usage Examples

### Example 1: Get All Classes

```bash
curl -X POST "http://localhost/amt/api/teacher-syllabus-status-report/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 2: Get Teacher Syllabus Status Report

```bash
curl -X POST "http://localhost/amt/api/teacher-syllabus-status-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "class_id": 1,
    "section_id": 2,
    "subject_group_id": 3,
    "subject_id": 5
  }'
```

### Example 3: Get Report for Specific Session

```bash
curl -X POST "http://localhost/amt/api/teacher-syllabus-status-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "class_id": 1,
    "section_id": 2,
    "subject_group_id": 3,
    "subject_id": 5,
    "session_id": 18
  }'
```

---

## Error Handling

### Common Error Codes

| Status Code | Description |
|-------------|-------------|
| 400 | Bad Request - Invalid request method or missing required parameters |
| 401 | Unauthorized - Invalid or missing authentication |
| 404 | Not Found - Subject group, subject, or class not found |
| 500 | Internal Server Error - Server-side error |

### Error Examples

**Missing Required Parameters:**
```json
{
  "status": 0,
  "message": "Missing required parameters: class_id, section_id, subject_group_id, and subject_id are required"
}
```

**Subject Not Found:**
```json
{
  "status": 0,
  "message": "Subject not found"
}
```

---

## Code Examples

### JavaScript (Fetch API)

```javascript
const getTeacherSyllabusStatusReport = async (classId, sectionId, subjectGroupId, subjectId, sessionId = null) => {
  try {
    const response = await fetch('http://localhost/amt/api/teacher-syllabus-status-report/filter', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Client-Service': 'smartschool',
        'Auth-Key': 'schoolAdmin@'
      },
      body: JSON.stringify({
        class_id: classId,
        section_id: sectionId,
        subject_group_id: subjectGroupId,
        subject_id: subjectId,
        session_id: sessionId
      })
    });
    
    const data = await response.json();
    
    if (data.status === 1) {
      console.log('Subject Info:', data.subject_info);
      console.log('Total Teachers:', data.total_teachers);
      console.log('Teachers Summary:', data.teachers_summary);
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
getTeacherSyllabusStatusReport(1, 2, 3, 5);
```

### PHP (cURL)

```php
<?php
function getTeacherSyllabusStatusReport($classId, $sectionId, $subjectGroupId, $subjectId, $sessionId = null) {
    $url = 'http://localhost/amt/api/teacher-syllabus-status-report/filter';
    
    $data = array(
        'class_id' => $classId,
        'section_id' => $sectionId,
        'subject_group_id' => $subjectGroupId,
        'subject_id' => $subjectId
    );
    if ($sessionId !== null) $data['session_id'] = $sessionId;
    
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
$report = getTeacherSyllabusStatusReport(1, 2, 3, 5);
print_r($report);
?>
```

### Python (Requests)

```python
import requests
import json

def get_teacher_syllabus_status_report(class_id, section_id, subject_group_id, subject_id, session_id=None):
    url = 'http://localhost/amt/api/teacher-syllabus-status-report/filter'
    
    headers = {
        'Content-Type': 'application/json',
        'Client-Service': 'smartschool',
        'Auth-Key': 'schoolAdmin@'
    }
    
    data = {
        'class_id': class_id,
        'section_id': section_id,
        'subject_group_id': subject_group_id,
        'subject_id': subject_id
    }
    if session_id is not None:
        data['session_id'] = session_id
    
    try:
        response = requests.post(url, headers=headers, json=data)
        
        if response.status_code == 200:
            result = response.json()
            if result['status'] == 1:
                print(f"Total Teachers: {result['total_teachers']}")
                return result
            else:
                print(f"Error: {result['message']}")
                return None
    except Exception as e:
        print(f"Request failed: {str(e)}")
        return None

# Usage
report = get_teacher_syllabus_status_report(1, 2, 3, 5)
print(report)
```

---

## Notes

1. **Required Parameters:** class_id, section_id, subject_group_id, and subject_id are mandatory for the filter endpoint.
2. **Session Handling:** If session_id is not provided, the API uses the current active session.
3. **Teacher Details:** Each teacher's name includes their employee ID in parentheses.
4. **Syllabus Details:** Includes complete information about each period taught by the teacher.
5. **Period Count:** total_periods shows how many periods each teacher has taught for the subject.

---

## Support

For issues or questions regarding this API, please contact the development team or refer to the main API documentation.

**Last Updated:** October 2025

