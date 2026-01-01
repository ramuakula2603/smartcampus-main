# Student Book Issue Report API Documentation

## Overview

The Student Book Issue Report API provides endpoints to retrieve book issue information with filtering by date range and member type (student/teacher).

**Base URL:** `http://localhost/amt/api`

---

## Authentication

Required headers:
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

---

## Endpoints

### 1. Filter Student Book Issue Report

**Endpoint:** `POST /api/student-book-issue-report/filter`

#### Request Body (All Optional)

```json
{
  "search_type": "this_year",
  "from_date": "2025-01-01",
  "to_date": "2025-12-31",
  "member_type": "student"
}
```

#### Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| search_type | string | No | `today`, `this_week`, `this_month`, `this_year` |
| from_date | string | No | Start date (YYYY-MM-DD) |
| to_date | string | No | End date (YYYY-MM-DD) |
| member_type | string | No | `student`, `teacher`, or empty for all |

#### Response Example

```json
{
  "status": 1,
  "message": "Student book issue report retrieved successfully",
  "filters_applied": {
    "search_type": "this_year",
    "from_date": null,
    "to_date": null,
    "member_type": "student",
    "date_range_used": {
      "start_date": "2025-01-01",
      "end_date": "2025-12-31"
    }
  },
  "total_records": 1,
  "data": [
    {
      "id": "1",
      "issue_date": "2025-01-15",
      "duereturn_date": "2025-02-15",
      "return_date": null,
      "is_returned": "0",
      "book_title": "Introduction to Programming",
      "book_no": "BK001",
      "author": "John Doe",
      "members_id": "1",
      "library_card_no": "LIB001",
      "member_type": "student",
      "firstname": "Alice",
      "middlename": "",
      "lastname": "Smith",
      "admission": "STU001",
      "staff_name": null,
      "staff_surname": null,
      "employee_id": null
    }
  ],
  "timestamp": "2025-10-07 12:00:00"
}
```

#### Key Response Fields

| Field | Description |
|-------|-------------|
| duereturn_date | Due date for returning the book |
| is_returned | "0" = not returned, "1" = returned |
| member_type | "student" or "teacher" |
| admission | Student admission number (null for teachers) |
| employee_id | Teacher employee ID (null for students) |

---

### 2. List Filter Options

**Endpoint:** `POST /api/student-book-issue-report/list`

#### Response

```json
{
  "status": 1,
  "message": "Student book issue filter options retrieved successfully",
  "search_types": [
    {"value": "today", "label": "Today"},
    {"value": "this_week", "label": "This Week"},
    {"value": "this_month", "label": "This Month"},
    {"value": "this_year", "label": "This Year"}
  ],
  "member_types": [
    {"value": "", "label": "All"},
    {"value": "student", "label": "Student"},
    {"value": "teacher", "label": "Teacher"}
  ],
  "timestamp": "2025-10-07 12:00:00"
}
```

---

## Usage Examples

### Example 1: Get All Book Issues (Empty Request)

```bash
curl -X POST "http://localhost/amt/api/student-book-issue-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 2: Filter by Member Type (Students Only)

```bash
curl -X POST "http://localhost/amt/api/student-book-issue-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "member_type": "student"
  }'
```

### Example 3: Filter by Search Type and Member Type

```bash
curl -X POST "http://localhost/amt/api/student-book-issue-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "search_type": "this_month",
    "member_type": "teacher"
  }'
```

### Example 4: Filter by Custom Date Range

```bash
curl -X POST "http://localhost/amt/api/student-book-issue-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "from_date": "2025-01-01",
    "to_date": "2025-03-31",
    "member_type": "student"
  }'
```

### Example 5: JavaScript (Fetch API)

```javascript
fetch('http://localhost/amt/api/student-book-issue-report/filter', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Client-Service': 'smartschool',
    'Auth-Key': 'schoolAdmin@'
  },
  body: JSON.stringify({
    member_type: 'student',
    search_type: 'this_month'
  })
})
.then(response => response.json())
.then(data => console.log(data));
```

### Example 6: PHP

```php
$ch = curl_init('http://localhost/amt/api/student-book-issue-report/filter');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'member_type' => 'student',
    'search_type' => 'this_week'
]));
$response = curl_exec($ch);
curl_close($ch);
$data = json_decode($response, true);
```

### Example 7: Python

```python
import requests

url = 'http://localhost/amt/api/student-book-issue-report/filter'
headers = {
    'Content-Type': 'application/json',
    'Client-Service': 'smartschool',
    'Auth-Key': 'schoolAdmin@'
}
payload = {
    'member_type': 'student',
    'search_type': 'this_month'
}
response = requests.post(url, headers=headers, json=payload)
data = response.json()
```

---

## Error Handling

### Error Response Format

```json
{
  "status": 0,
  "message": "Error description"
}
```

### Common Errors

| HTTP Code | Message | Solution |
|-----------|---------|----------|
| 400 | Bad request. Only POST method allowed. | Use POST method |
| 401 | Unauthorized access | Check authentication headers |
| 500 | Internal server error | Check server logs |

---

## Best Practices

1. **Empty Request:** Use `{}` to get all book issues for current year
2. **Member Type Filtering:** Use `member_type` to filter by student/teacher
3. **Date Range Priority:** Custom date range overrides search_type
4. **Due Date Tracking:** Use `duereturn_date` to track due dates
5. **Return Status:** Check `is_returned` field for return status

---

## Notes

- Default date range: current year
- Includes both issued and returned books
- Supports filtering by member type
- Date format: YYYY-MM-DD
- Filtered by issue_date

---

## Related APIs

- Issue Return Report API
- Book Due Report API
- Book Inventory Report API

---

**API Version:** 1.0  
**Last Updated:** October 7, 2025  
**Status:** Production Ready

