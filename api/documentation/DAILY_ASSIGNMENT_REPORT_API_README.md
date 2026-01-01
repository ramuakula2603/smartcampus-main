# Daily Assignment Report API Documentation

## Overview
The Daily Assignment Report API provides endpoints to retrieve student daily assignment information with flexible filtering options. This API allows you to fetch daily assignment data by class, section, subject group, subject, and date range.

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

### 1. Filter Daily Assignment Report
Retrieve daily assignment report data with optional filters.

**Endpoint:** `POST /api/daily-assignment-report/filter`

**Request Body Parameters (All Optional):**

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `class_id` | integer | Class ID | `10` |
| `section_id` | integer | Section ID | `5` |
| `subject_group_id` | integer | Subject Group ID | `3` |
| `subject_id` | integer | Subject ID | `15` |
| `search_type` | string | Predefined date range | `"this_year"`, `"this_month"`, `"this_week"`, `"today"` |
| `from_date` | string | Start date (YYYY-MM-DD) | `"2025-01-01"` |
| `to_date` | string | End date (YYYY-MM-DD) | `"2025-12-31"` |
| `session_id` | integer | Session ID | `21` |

**Important:** 
- Empty request body `{}` returns **ALL daily assignment data** for the current year
- All parameters are optional
- Custom date range (`from_date`, `to_date`) takes precedence over `search_type`
- If no date filter is provided, defaults to current year

**Response Format:**
```json
{
    "status": 1,
    "message": "Daily assignment report retrieved successfully",
    "filters_applied": {
        "class_id": 10,
        "section_id": 5,
        "subject_group_id": 3,
        "subject_id": 15,
        "search_type": "this_month",
        "from_date": null,
        "to_date": null,
        "session_id": 21
    },
    "total_records": 45,
    "data": [
        {
            "id": "123",
            "student_session_id": "456",
            "subject_group_subject_id": "15",
            "title": "Math Assignment 1",
            "description": "Complete exercises 1-10",
            "date": "2025-01-15",
            "attachment": "assignment.pdf",
            "marks": "10",
            "obtained_marks": "8",
            "evaluation_date": "2025-01-20",
            "remark": "Good work",
            "evaluated_by": "5",
            "name": "John",
            "surname": "Smith",
            "employee_id": "EMP001",
            "class": "JR-BIPC",
            "section": "A",
            "firstname": "Alice",
            "middlename": "",
            "lastname": "Johnson",
            "student_id": "789",
            "student_admission_no": "STU001",
            "subject_name": "Mathematics",
            "subject_code": "MATH101"
        }
    ],
    "timestamp": "2025-10-07 23:30:00"
}
```

**Response Fields:**

| Field | Type | Description |
|-------|------|-------------|
| `status` | integer | 1 for success, 0 for error |
| `message` | string | Response message |
| `filters_applied` | object | Applied filter parameters |
| `total_records` | integer | Number of records returned |
| `data` | array | Array of daily assignment records |
| `timestamp` | string | Response timestamp |

**Daily Assignment Record Fields:**

| Field | Description |
|-------|-------------|
| `id` | Assignment ID |
| `student_session_id` | Student session ID |
| `subject_group_subject_id` | Subject group subject ID |
| `title` | Assignment title |
| `description` | Assignment description |
| `date` | Assignment date |
| `attachment` | Attachment filename |
| `marks` | Total marks |
| `obtained_marks` | Marks obtained by student |
| `evaluation_date` | Date of evaluation |
| `remark` | Evaluation remark |
| `evaluated_by` | Staff ID who evaluated |
| `name` | Staff first name |
| `surname` | Staff last name |
| `employee_id` | Staff employee ID |
| `class` | Class name |
| `section` | Section name |
| `firstname` | Student first name |
| `middlename` | Student middle name |
| `lastname` | Student last name |
| `student_id` | Student ID |
| `student_admission_no` | Student admission number |
| `subject_name` | Subject name |
| `subject_code` | Subject code |

### 2. List Daily Assignment Filter Options
Retrieve available filter options (classes, search types).

**Endpoint:** `POST /api/daily-assignment-report/list`

**Request Body:** Empty `{}`

**Response Format:**
```json
{
    "status": 1,
    "message": "Daily assignment filter options retrieved successfully",
    "total_classes": 13,
    "classes": [
        {"id": "10", "class": "JR-BIPC"},
        {"id": "11", "class": "JR-CEC"}
    ],
    "search_types": [
        {"value": "today", "label": "Today"},
        {"value": "this_week", "label": "This Week"},
        {"value": "this_month", "label": "This Month"},
        {"value": "this_year", "label": "This Year"}
    ],
    "current_session_id": 21,
    "note": "Use the filter endpoint with class_id, section_id, subject_group_id, subject_id, or date range to get daily assignment report",
    "timestamp": "2025-10-07 23:30:00"
}
```

## Usage Examples

### Example 1: Get All Daily Assignments (Empty Request)
```bash
curl -X POST "http://localhost/amt/api/daily-assignment-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Returns:** All daily assignment records for the current year

### Example 2: Filter by Class and Section
```bash
curl -X POST "http://localhost/amt/api/daily-assignment-report/filter" \
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
curl -X POST "http://localhost/amt/api/daily-assignment-report/filter" \
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

### Example 4: Filter by Date Range
```bash
curl -X POST "http://localhost/amt/api/daily-assignment-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "from_date": "2025-01-01",
    "to_date": "2025-03-31"
  }'
```

### Example 5: Filter by Search Type
```bash
curl -X POST "http://localhost/amt/api/daily-assignment-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "search_type": "this_month"
  }'
```

### Example 6: Get Filter Options
```bash
curl -X POST "http://localhost/amt/api/daily-assignment-report/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 7: Combined Filters
```bash
curl -X POST "http://localhost/amt/api/daily-assignment-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "class_id": 10,
    "section_id": 5,
    "subject_group_id": 3,
    "search_type": "this_week"
  }'
```

## Code Examples

### JavaScript (Fetch API)
```javascript
// Get all daily assignments
async function getAllDailyAssignments() {
    const response = await fetch('http://localhost/amt/api/daily-assignment-report/filter', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Client-Service': 'smartschool',
            'Auth-Key': 'schoolAdmin@'
        },
        body: JSON.stringify({})
    });
    
    const data = await response.json();
    console.log('Daily Assignments:', data);
    return data;
}

// Filter by class and date range
async function getDailyAssignmentsByClassAndDate(classId, sectionId, fromDate, toDate) {
    const response = await fetch('http://localhost/amt/api/daily-assignment-report/filter', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Client-Service': 'smartschool',
            'Auth-Key': 'schoolAdmin@'
        },
        body: JSON.stringify({
            class_id: classId,
            section_id: sectionId,
            from_date: fromDate,
            to_date: toDate
        })
    });
    
    const data = await response.json();
    return data;
}

// Usage
getAllDailyAssignments();
getDailyAssignmentsByClassAndDate(10, 5, '2025-01-01', '2025-03-31');
```

### PHP (cURL)
```php
<?php
// Get all daily assignments
function getAllDailyAssignments() {
    $url = 'http://localhost/amt/api/daily-assignment-report/filter';
    
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

// Filter by search type
function getDailyAssignmentsBySearchType($searchType) {
    $url = 'http://localhost/amt/api/daily-assignment-report/filter';
    
    $data = [
        'search_type' => $searchType
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
$allAssignments = getAllDailyAssignments();
$thisMonthAssignments = getDailyAssignmentsBySearchType('this_month');
?>
```

### Python (Requests)
```python
import requests
import json

# API configuration
BASE_URL = 'http://localhost/amt/api'
HEADERS = {
    'Content-Type': 'application/json',
    'Client-Service': 'smartschool',
    'Auth-Key': 'schoolAdmin@'
}

# Get all daily assignments
def get_all_daily_assignments():
    url = f'{BASE_URL}/daily-assignment-report/filter'
    response = requests.post(url, headers=HEADERS, json={})
    return response.json()

# Filter by class and section
def get_daily_assignments_by_class(class_id, section_id):
    url = f'{BASE_URL}/daily-assignment-report/filter'
    data = {
        'class_id': class_id,
        'section_id': section_id
    }
    response = requests.post(url, headers=HEADERS, json=data)
    return response.json()

# Filter by date range
def get_daily_assignments_by_date_range(from_date, to_date):
    url = f'{BASE_URL}/daily-assignment-report/filter'
    data = {
        'from_date': from_date,
        'to_date': to_date
    }
    response = requests.post(url, headers=HEADERS, json=data)
    return response.json()

# Get filter options
def get_filter_options():
    url = f'{BASE_URL}/daily-assignment-report/list'
    response = requests.post(url, headers=HEADERS, json={})
    return response.json()

# Usage
if __name__ == '__main__':
    # Get all assignments
    all_assignments = get_all_daily_assignments()
    print(f"Total assignments: {all_assignments['total_records']}")

    # Filter by class
    class_assignments = get_daily_assignments_by_class(10, 5)
    print(f"Class assignments: {class_assignments['total_records']}")

    # Get filter options
    options = get_filter_options()
    print(f"Available classes: {options['total_classes']}")
```

## Search Type Options

The `search_type` parameter accepts the following predefined values:

| Value | Description | Date Range |
|-------|-------------|------------|
| `today` | Today's date | Current date only |
| `this_week` | Current week | Monday to Sunday of current week |
| `this_month` | Current month | 1st to last day of current month |
| `this_year` | Current year | January 1 to December 31 of current year |

## Error Handling

### Error Response Format
```json
{
    "status": 0,
    "message": "Error description",
    "error": "Detailed error message"
}
```

### Common Error Codes

| HTTP Status | Description |
|-------------|-------------|
| 400 | Bad Request - Invalid request method |
| 401 | Unauthorized - Invalid or missing authentication |
| 500 | Internal Server Error - Server-side error |

### Error Examples

**Unauthorized Access:**
```json
{
    "status": 0,
    "message": "Unauthorized access"
}
```

**Bad Request:**
```json
{
    "status": 0,
    "message": "Bad request. Only POST method allowed."
}
```

## Notes

1. **Empty Request Behavior:** Sending an empty request body `{}` to the filter endpoint returns all daily assignment data for the current year, not an error.

2. **Date Range Priority:** If both custom date range (`from_date`, `to_date`) and `search_type` are provided, the custom date range takes precedence.

3. **Default Date Filter:** If no date filter is provided, the API defaults to the current year.

4. **Session ID:** If no session_id is provided, the current session is used automatically.

5. **Date Format:** All dates should be in `YYYY-MM-DD` format.

6. **Evaluation Status:** The API returns both evaluated and unevaluated assignments.

## Best Practices

1. **Always include authentication headers** in every request
2. **Use predefined search types** for common date ranges
3. **Cache filter options** from the list endpoint to avoid repeated calls
4. **Handle empty data arrays** gracefully in your application
5. **Validate date formats** before sending requests
6. **Check the status field** in responses before processing data
7. **Combine filters** for more specific queries
8. **Log errors** for debugging and monitoring

## Support

For issues or questions regarding this API, please contact the system administrator or refer to the main API documentation.

---

**Version:** 1.0
**Last Updated:** October 7, 2025
**API Endpoint:** `/api/daily-assignment-report`

