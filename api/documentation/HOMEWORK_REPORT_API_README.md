# Homework Report API Documentation

## Overview
The Homework Report API provides endpoints to retrieve comprehensive homework information with flexible filtering options. This API allows you to fetch homework data including student counts and submission statistics by class, section, subject group, and subject.

## Base URL
```
http://localhost/amt/api
```

## Authentication
All API requests require authentication headers:

| Header | Value | Required |
|--------|-------|----------|
| `Client-Service` | `smartschool` | Yes |
| `Auth-Key` | `schoolAdmin@` | Yes |
| `Content-Type` | `application/json` | Yes |

## Endpoints

### 1. Filter Homework Report
Retrieve homework report data with optional filters.

**Endpoint:** `POST /api/homework-report/filter`

**Request Body Parameters (All Optional):**

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `class_id` | integer | Class ID | `10` |
| `section_id` | integer | Section ID | `5` |
| `subject_group_id` | integer | Subject Group ID | `3` |
| `subject_id` | integer | Subject ID | `15` |
| `session_id` | integer | Session ID | `21` |

**Important:** 
- Empty request body `{}` returns **ALL homework data**
- All parameters are optional
- Returns homework with student counts and staff information

**Response Format:**
```json
{
    "status": 1,
    "message": "Homework report retrieved successfully",
    "filters_applied": {
        "class_id": 10,
        "section_id": 5,
        "subject_group_id": 3,
        "subject_id": 15,
        "session_id": 21
    },
    "total_records": 35,
    "data": [
        {
            "id": "123",
            "class_id": "10",
            "section_id": "5",
            "subject_group_subject_id": "15",
            "homework_date": "2025-01-15",
            "submit_date": "2025-01-20",
            "evaluation_date": "2025-01-22",
            "description": "Complete chapter 5 exercises",
            "document": "homework.pdf",
            "created_by": "5",
            "session_id": "21",
            "class": "JR-BIPC",
            "section": "A",
            "subject_id": "10",
            "subject_group_subject_id": "15",
            "subject_name": "Mathematics",
            "subject_code": "MATH101",
            "subject_groups_id": "3",
            "name": "Science Group",
            "assignments": "18",
            "staff_name": "John",
            "staff_surname": "Smith",
            "staff_employee_id": "EMP001",
            "student_count": "30"
        }
    ],
    "timestamp": "2025-10-07 23:50:00"
}
```

**Response Fields:**

| Field | Type | Description |
|-------|------|-------------|
| `status` | integer | 1 for success, 0 for error |
| `message` | string | Response message |
| `filters_applied` | object | Applied filter parameters |
| `total_records` | integer | Number of homework records returned |
| `data` | array | Array of homework records |
| `timestamp` | string | Response timestamp |

**Homework Record Fields:**

| Field | Description |
|-------|-------------|
| `id` | Homework ID |
| `class_id` | Class ID |
| `section_id` | Section ID |
| `subject_group_subject_id` | Subject group subject ID |
| `homework_date` | Date homework was assigned |
| `submit_date` | Submission deadline |
| `evaluation_date` | Date of evaluation |
| `description` | Homework description |
| `document` | Attached document filename |
| `created_by` | Staff ID who created homework |
| `session_id` | Session ID |
| `class` | Class name |
| `section` | Section name |
| `subject_name` | Subject name |
| `subject_code` | Subject code |
| `subject_groups_id` | Subject group ID |
| `name` | Subject group name |
| `assignments` | Number of submissions |
| `staff_name` | Staff first name |
| `staff_surname` | Staff last name |
| `staff_employee_id` | Staff employee ID |
| `student_count` | Total students in class/section |

### 2. List Homework Report Filter Options
Retrieve available filter options (classes).

**Endpoint:** `POST /api/homework-report/list`

**Request Body:** Empty `{}`

**Response Format:**
```json
{
    "status": 1,
    "message": "Homework report filter options retrieved successfully",
    "total_classes": 13,
    "classes": [
        {"id": "10", "class": "JR-BIPC"},
        {"id": "11", "class": "JR-CEC"}
    ],
    "current_session_id": 21,
    "note": "Use the filter endpoint with class_id, section_id, subject_group_id, and subject_id to get homework report",
    "timestamp": "2025-10-07 23:50:00"
}
```

## Usage Examples

### Example 1: Get All Homework (Empty Request)
```bash
curl -X POST "http://localhost/amt/api/homework-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 2: Filter by Class and Section
```bash
curl -X POST "http://localhost/amt/api/homework-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "class_id": 10,
    "section_id": 5
  }'
```

### Example 3: Filter by Subject
```bash
curl -X POST "http://localhost/amt/api/homework-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "class_id": 10,
    "section_id": 5,
    "subject_group_id": 3,
    "subject_id": 15
  }'
```

### Example 4: Get Filter Options
```bash
curl -X POST "http://localhost/amt/api/homework-report/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

## Code Examples

### JavaScript (Fetch API)
```javascript
async function getAllHomework() {
    const response = await fetch('http://localhost/amt/api/homework-report/filter', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Client-Service': 'smartschool',
            'Auth-Key': 'schoolAdmin@'
        },
        body: JSON.stringify({})
    });
    return await response.json();
}

async function getHomeworkByClass(classId, sectionId) {
    const response = await fetch('http://localhost/amt/api/homework-report/filter', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Client-Service': 'smartschool',
            'Auth-Key': 'schoolAdmin@'
        },
        body: JSON.stringify({
            class_id: classId,
            section_id: sectionId
        })
    });
    return await response.json();
}
```

### PHP (cURL)
```php
<?php
function getAllHomework() {
    $url = 'http://localhost/amt/api/homework-report/filter';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Client-Service: smartschool',
        'Auth-Key: schoolAdmin@'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([]));
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}
?>
```

### Python (Requests)
```python
import requests

BASE_URL = 'http://localhost/amt/api'
HEADERS = {
    'Content-Type': 'application/json',
    'Client-Service': 'smartschool',
    'Auth-Key': 'schoolAdmin@'
}

def get_all_homework():
    url = f'{BASE_URL}/homework-report/filter'
    response = requests.post(url, headers=HEADERS, json={})
    return response.json()

def get_homework_by_class(class_id, section_id):
    url = f'{BASE_URL}/homework-report/filter'
    data = {'class_id': class_id, 'section_id': section_id}
    response = requests.post(url, headers=HEADERS, json=data)
    return response.json()
```

## Best Practices

1. **Always include authentication headers**
2. **Cache filter options** from the list endpoint
3. **Use student_count field** for class size information
4. **Check assignments field** for submission count
5. **Handle empty data arrays** gracefully
6. **Combine filters** for specific queries

---

**Version:** 1.0  
**Last Updated:** October 7, 2025  
**API Endpoint:** `/api/homework-report`
