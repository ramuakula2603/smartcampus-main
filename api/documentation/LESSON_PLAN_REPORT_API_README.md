# Lesson Plan Report API Documentation

## Overview

The Lesson Plan Report API provides endpoints for retrieving lesson plan completion status reports by class, section, and subject group. This API returns detailed information about subject-wise completion percentages, complete and incomplete topic counts.

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

### 1. Filter Lesson Plan Report

**Endpoint:** `POST /api/lesson-plan-report/filter`

**Description:** Retrieves lesson plan completion status for all subjects in a subject group.

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
  "session_id": 18
}
```

**Response:**
```json
{
  "status": 1,
  "message": "Lesson plan report retrieved successfully",
  "filters_applied": {
    "class_id": 1,
    "section_id": 2,
    "subject_group_id": 3,
    "session_id": 18
  },
  "subject_group_info": {
    "id": "10",
    "name": "Science Group"
  },
  "total_subjects": 3,
  "subjects": [
    {
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
    {
      "subject_id": "6",
      "subject_name": "Physics",
      "subject_code": "PHY101",
      "label": "Physics (PHY101)",
      "complete_percentage": 60.0,
      "incomplete_percentage": 40.0,
      "complete_count": "12",
      "incomplete_count": "8",
      "total_topics": "20"
    }
  ],
  "timestamp": "2025-10-07 10:30:00"
}
```

---

### 2. List All Classes

**Endpoint:** `POST /api/lesson-plan-report/list`

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
  "note": "Use the filter endpoint with class_id, section_id, and subject_group_id to get lesson plan report",
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
| `session_id` | integer | No | Session ID (defaults to current session) | `18` |

### Parameter Notes

- **class_id, section_id, subject_group_id** are required for the filter endpoint
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
  "total_subjects": 0,
  "subjects": [ ],
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

### Subject Fields

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

---

## Usage Examples

### Example 1: Get All Classes

```bash
curl -X POST "http://localhost/amt/api/lesson-plan-report/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 2: Get Lesson Plan Report

```bash
curl -X POST "http://localhost/amt/api/lesson-plan-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "class_id": 1,
    "section_id": 2,
    "subject_group_id": 3
  }'
```

### Example 3: Get Report for Specific Session

```bash
curl -X POST "http://localhost/amt/api/lesson-plan-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "class_id": 1,
    "section_id": 2,
    "subject_group_id": 3,
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
| 404 | Not Found - Subject group or class not found |
| 500 | Internal Server Error - Server-side error |

### Error Examples

**Missing Required Parameters:**
```json
{
  "status": 0,
  "message": "Missing required parameters: class_id, section_id, and subject_group_id are required"
}
```

**Subject Group Not Found:**
```json
{
  "status": 0,
  "message": "No subject group found for the specified class, section, and subject group"
}
```

---

## Code Examples

### JavaScript (Fetch API)

```javascript
const getLessonPlanReport = async (classId, sectionId, subjectGroupId, sessionId = null) => {
  try {
    const response = await fetch('http://localhost/amt/api/lesson-plan-report/filter', {
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
        session_id: sessionId
      })
    });
    
    const data = await response.json();
    
    if (data.status === 1) {
      console.log('Lesson Plan Report:', data.subjects);
      console.log('Total Subjects:', data.total_subjects);
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
getLessonPlanReport(1, 2, 3);
```

### PHP (cURL)

```php
<?php
function getLessonPlanReport($classId, $sectionId, $subjectGroupId, $sessionId = null) {
    $url = 'http://localhost/amt/api/lesson-plan-report/filter';
    
    $data = array(
        'class_id' => $classId,
        'section_id' => $sectionId,
        'subject_group_id' => $subjectGroupId
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
$report = getLessonPlanReport(1, 2, 3);
print_r($report);
?>
```

### Python (Requests)

```python
import requests
import json

def get_lesson_plan_report(class_id, section_id, subject_group_id, session_id=None):
    url = 'http://localhost/amt/api/lesson-plan-report/filter'
    
    headers = {
        'Content-Type': 'application/json',
        'Client-Service': 'smartschool',
        'Auth-Key': 'schoolAdmin@'
    }
    
    data = {
        'class_id': class_id,
        'section_id': section_id,
        'subject_group_id': subject_group_id
    }
    if session_id is not None:
        data['session_id'] = session_id
    
    try:
        response = requests.post(url, headers=headers, json=data)
        
        if response.status_code == 200:
            result = response.json()
            if result['status'] == 1:
                print(f"Total Subjects: {result['total_subjects']}")
                return result
            else:
                print(f"Error: {result['message']}")
                return None
    except Exception as e:
        print(f"Request failed: {str(e)}")
        return None

# Usage
report = get_lesson_plan_report(1, 2, 3)
print(report)
```

---

## Notes

1. **Required Parameters:** class_id, section_id, and subject_group_id are mandatory for the filter endpoint.
2. **Session Handling:** If session_id is not provided, the API uses the current active session.
3. **Completion Calculation:** Percentages are calculated based on completed vs total topics.
4. **Subject Group:** The API returns all subjects within the specified subject group.
5. **Zero Topics:** Subjects with no topics will show 0% completion.

---

## Support

For issues or questions regarding this API, please contact the development team or refer to the main API documentation.

**Last Updated:** October 2025

