# Evaluation Report API Documentation

## Overview
The Evaluation Report API provides endpoints to retrieve homework evaluation status with flexible filtering options. This API allows you to fetch homework evaluation data including completion percentages by class, section, subject group, and subject.

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

### 1. Filter Evaluation Report
Retrieve homework evaluation report data with optional filters.

**Endpoint:** `POST /api/evaluation-report/filter`

**Request Body Parameters (All Optional):**

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `class_id` | integer | Class ID | `10` |
| `section_id` | integer | Section ID | `5` |
| `subject_group_id` | integer | Subject Group ID | `3` |
| `subject_id` | integer | Subject ID | `15` |
| `session_id` | integer | Session ID | `21` |

**Important:** 
- Empty request body `{}` returns **ALL homework evaluation data**
- All parameters are optional
- Returns evaluation percentages for each homework

**Response Format:**
```json
{
    "status": 1,
    "message": "Evaluation report retrieved successfully",
    "filters_applied": {
        "class_id": 10,
        "section_id": 5,
        "subject_group_id": 3,
        "subject_id": 15,
        "session_id": 21
    },
    "total_records": 25,
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
            "evaluation_report": {
                "total_students": 30,
                "evaluated_count": 25,
                "submitted_count": 28,
                "pending_count": 5,
                "evaluated_percentage": 83.33,
                "submitted_percentage": 93.33
            }
        }
    ],
    "evaluation_summary": {
        "123": {
            "total_students": 30,
            "evaluated_count": 25,
            "submitted_count": 28,
            "pending_count": 5,
            "evaluated_percentage": 83.33,
            "submitted_percentage": 93.33
        }
    },
    "timestamp": "2025-10-07 23:45:00"
}
```

**Response Fields:**

| Field | Type | Description |
|-------|------|-------------|
| `status` | integer | 1 for success, 0 for error |
| `message` | string | Response message |
| `filters_applied` | object | Applied filter parameters |
| `total_records` | integer | Number of homework records returned |
| `data` | array | Array of homework records with evaluation data |
| `evaluation_summary` | object | Map of homework IDs to evaluation statistics |
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
| `assignments` | Number of submissions |
| `evaluation_report` | Evaluation statistics object |

**Evaluation Report Fields:**

| Field | Description |
|-------|-------------|
| `total_students` | Total students in class/section |
| `evaluated_count` | Number of students evaluated |
| `submitted_count` | Number of students who submitted |
| `pending_count` | Number of students pending evaluation |
| `evaluated_percentage` | Percentage of students evaluated |
| `submitted_percentage` | Percentage of students who submitted |

### 2. List Evaluation Report Filter Options
Retrieve available filter options (classes).

**Endpoint:** `POST /api/evaluation-report/list`

**Request Body:** Empty `{}`

**Response Format:**
```json
{
    "status": 1,
    "message": "Evaluation report filter options retrieved successfully",
    "total_classes": 13,
    "classes": [
        {"id": "10", "class": "JR-BIPC"},
        {"id": "11", "class": "JR-CEC"}
    ],
    "current_session_id": 21,
    "note": "Use the filter endpoint with class_id, section_id, subject_group_id, and subject_id to get evaluation report",
    "timestamp": "2025-10-07 23:45:00"
}
```

## Usage Examples

### Example 1: Get All Homework Evaluations (Empty Request)
```bash
curl -X POST "http://localhost/amt/api/evaluation-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Returns:** All homework records with evaluation statistics

### Example 2: Filter by Class and Section
```bash
curl -X POST "http://localhost/amt/api/evaluation-report/filter" \
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
curl -X POST "http://localhost/amt/api/evaluation-report/filter" \
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
curl -X POST "http://localhost/amt/api/evaluation-report/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

## Code Examples

### JavaScript (Fetch API)
```javascript
// Get all homework evaluations
async function getAllEvaluations() {
    const response = await fetch('http://localhost/amt/api/evaluation-report/filter', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Client-Service': 'smartschool',
            'Auth-Key': 'schoolAdmin@'
        },
        body: JSON.stringify({})
    });
    
    const data = await response.json();
    console.log('Evaluations:', data);
    return data;
}

// Filter by class and subject
async function getEvaluationsByClassAndSubject(classId, sectionId, subjectGroupId, subjectId) {
    const response = await fetch('http://localhost/amt/api/evaluation-report/filter', {
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
            subject_id: subjectId
        })
    });
    
    const data = await response.json();
    return data;
}

// Usage
getAllEvaluations();
getEvaluationsByClassAndSubject(10, 5, 3, 15);
```

### PHP (cURL)
```php
<?php
// Get all homework evaluations
function getAllEvaluations() {
    $url = 'http://localhost/amt/api/evaluation-report/filter';
    
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

// Filter by class
function getEvaluationsByClass($classId, $sectionId) {
    $url = 'http://localhost/amt/api/evaluation-report/filter';
    
    $data = [
        'class_id' => $classId,
        'section_id' => $sectionId
    ];
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Client-Service: smartschool',
        'Auth-Key: schoolAdmin@'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

// Usage
$allEvaluations = getAllEvaluations();
$classEvaluations = getEvaluationsByClass(10, 5);
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

def get_all_evaluations():
    url = f'{BASE_URL}/evaluation-report/filter'
    response = requests.post(url, headers=HEADERS, json={})
    return response.json()

def get_evaluations_by_class(class_id, section_id):
    url = f'{BASE_URL}/evaluation-report/filter'
    data = {'class_id': class_id, 'section_id': section_id}
    response = requests.post(url, headers=HEADERS, json=data)
    return response.json()

# Usage
all_evaluations = get_all_evaluations()
print(f"Total homework: {all_evaluations['total_records']}")
```

## Best Practices

1. **Always include authentication headers** in every request
2. **Cache filter options** from the list endpoint
3. **Use evaluation_report field** for quick statistics
4. **Check both evaluated and submitted percentages** for complete picture
5. **Handle empty data arrays** gracefully
6. **Combine filters** for specific queries

---

**Version:** 1.0  
**Last Updated:** October 7, 2025  
**API Endpoint:** `/api/evaluation-report`
