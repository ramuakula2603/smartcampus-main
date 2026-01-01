# Issue Return Report API Documentation

## Overview

The Issue Return Report API provides endpoints to retrieve book issue and return information from the library management system. This API allows filtering by date range and returns comprehensive data about book issues and returns for both students and teachers.

**Base URL:** `http://localhost/amt/api`

---

## Authentication

All endpoints require authentication headers:

```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

---

## Endpoints

### 1. Filter Issue Return Report

Retrieve book issue and return data with optional filtering.

**Endpoint:** `POST /api/issue-return-report/filter`

#### Request Body

All parameters are optional. Empty request `{}` returns all data for the current year.

```json
{
  "search_type": "this_year",
  "from_date": "2025-01-01",
  "to_date": "2025-12-31"
}
```

#### Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| search_type | string | No | Predefined date range: `today`, `this_week`, `this_month`, `this_year` |
| from_date | string | No | Start date in YYYY-MM-DD format |
| to_date | string | No | End date in YYYY-MM-DD format |

**Note:** Custom date range (`from_date`, `to_date`) takes precedence over `search_type`.

#### Response

```json
{
  "status": 1,
  "message": "Issue return report retrieved successfully",
  "filters_applied": {
    "search_type": "this_year",
    "from_date": null,
    "to_date": null,
    "date_range_used": {
      "start_date": "2025-01-01",
      "end_date": "2025-12-31"
    }
  },
  "total_records": 2,
  "data": [
    {
      "id": "1",
      "issue_date": "2025-01-15",
      "return_date": "2025-01-20",
      "is_returned": "1",
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
      "fname": null,
      "lname": null,
      "employee_id": null
    },
    {
      "id": "2",
      "issue_date": "2025-02-10",
      "return_date": null,
      "is_returned": "0",
      "book_title": "Advanced Mathematics",
      "book_no": "BK002",
      "author": "Jane Wilson",
      "members_id": "2",
      "library_card_no": "LIB002",
      "member_type": "teacher",
      "firstname": null,
      "middlename": null,
      "lastname": null,
      "admission": null,
      "fname": "Robert",
      "lname": "Johnson",
      "employee_id": "EMP001"
    }
  ],
  "timestamp": "2025-10-07 12:00:00"
}
```

#### Response Fields

| Field | Type | Description |
|-------|------|-------------|
| id | string | Book issue record ID |
| issue_date | string | Date when book was issued |
| return_date | string | Date when book was returned (null if not returned) |
| is_returned | string | Return status: "0" = not returned, "1" = returned |
| book_title | string | Title of the book |
| book_no | string | Book number/code |
| author | string | Book author |
| members_id | string | Library member ID |
| library_card_no | string | Library card number |
| member_type | string | Member type: "student" or "teacher" |
| firstname | string | Student first name (null for teachers) |
| middlename | string | Student middle name (null for teachers) |
| lastname | string | Student last name (null for teachers) |
| admission | string | Student admission number (null for teachers) |
| fname | string | Teacher first name (null for students) |
| lname | string | Teacher last name (null for students) |
| employee_id | string | Teacher employee ID (null for students) |

---

### 2. List Filter Options

Get available filter options for the issue return report.

**Endpoint:** `POST /api/issue-return-report/list`

#### Request Body

```json
{}
```

#### Response

```json
{
  "status": 1,
  "message": "Issue return filter options retrieved successfully",
  "search_types": [
    {
      "value": "today",
      "label": "Today"
    },
    {
      "value": "this_week",
      "label": "This Week"
    },
    {
      "value": "this_month",
      "label": "This Month"
    },
    {
      "value": "this_year",
      "label": "This Year"
    }
  ],
  "note": "Use the filter endpoint with search_type or custom date range (from_date, to_date) to get issue return report",
  "timestamp": "2025-10-07 12:00:00"
}
```

---

## Usage Examples

### Example 1: Get All Issue/Return Records (Empty Request)

**cURL:**
```bash
curl -X POST "http://localhost/amt/api/issue-return-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**JavaScript (Fetch API):**
```javascript
fetch('http://localhost/amt/api/issue-return-report/filter', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Client-Service': 'smartschool',
    'Auth-Key': 'schoolAdmin@'
  },
  body: JSON.stringify({})
})
.then(response => response.json())
.then(data => console.log(data));
```

**PHP:**
```php
$ch = curl_init('http://localhost/amt/api/issue-return-report/filter');
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
$data = json_decode($response, true);
```

**Python:**
```python
import requests

url = 'http://localhost/amt/api/issue-return-report/filter'
headers = {
    'Content-Type': 'application/json',
    'Client-Service': 'smartschool',
    'Auth-Key': 'schoolAdmin@'
}
response = requests.post(url, headers=headers, json={})
data = response.json()
```

---

### Example 2: Filter by Search Type (This Month)

**cURL:**
```bash
curl -X POST "http://localhost/amt/api/issue-return-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "search_type": "this_month"
  }'
```

---

### Example 3: Filter by Custom Date Range

**cURL:**
```bash
curl -X POST "http://localhost/amt/api/issue-return-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key": "schoolAdmin@" \
  -d '{
    "from_date": "2025-01-01",
    "to_date": "2025-03-31"
  }'
```

---

### Example 4: Get Filter Options

**cURL:**
```bash
curl -X POST "http://localhost/amt/api/issue-return-report/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
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

### Common Error Codes

| HTTP Code | Status | Message | Solution |
|-----------|--------|---------|----------|
| 400 | 0 | Bad request. Only POST method allowed. | Use POST method |
| 401 | 0 | Unauthorized access | Check authentication headers |
| 500 | 0 | Internal server error | Check server logs |

---

## Best Practices

1. **Empty Request for All Data:** Use empty request body `{}` to get all records for the current year
2. **Date Range Priority:** Custom date range takes precedence over search_type
3. **Member Type Identification:** Use `member_type` field to distinguish between students and teachers
4. **Return Status:** Check `is_returned` field: "0" = not returned, "1" = returned
5. **Null Handling:** Student fields are null for teachers, teacher fields are null for students

---

## Notes

- Default date range is current year if no filters provided
- Includes both issued and returned books
- Supports both student and teacher book issues
- Date format: YYYY-MM-DD
- All dates are filtered by issue_date

---

## Related APIs

- Student Book Issue Report API
- Book Due Report API
- Book Inventory Report API

---

## Support

For issues or questions, please contact the API development team.

**API Version:** 1.0  
**Last Updated:** October 7, 2025  
**Status:** Production Ready

