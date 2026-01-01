# Online Exams Report API Documentation

## Overview

The Online Exams Report API provides endpoints for retrieving online exams reports showing exam details, assigned students count, and question counts. This API returns comprehensive information about all online exams with their statistics.

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

### 1. Filter Online Exams Report

**Endpoint:** `POST /api/online-exams-report/filter`

**Description:** Retrieves online exams report data with optional date range filtering.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Request Body:**
```json
{
  "from_date": "2025-01-01",
  "to_date": "2025-10-07"
}
```

**Response:**
```json
{
  "status": 1,
  "message": "Online exams report retrieved successfully",
  "filters_applied": {
    "from_date": "2025-01-01",
    "to_date": "2025-10-07"
  },
  "total_records": 15,
  "filtered_records": 15,
  "data": [
    {
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
      "assign": "30",
      "questions": "20"
    },
    {
      "id": "11",
      "exam": "Science Test",
      "attempt": "1",
      "exam_from": "2025-10-05 10:00:00",
      "exam_to": "2025-10-05 11:00:00",
      "duration": "60",
      "passing_percentage": "40",
      "description": "Science chapter 1-5 test",
      "session_id": "18",
      "is_active": "1",
      "is_rank_generated": "0",
      "assign": "25",
      "questions": "15"
    }
  ],
  "timestamp": "2025-10-07 10:30:00"
}
```

---

### 2. List Current Year's Exams

**Endpoint:** `POST /api/online-exams-report/list`

**Description:** Retrieves online exams report for the current year.

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
  "message": "Online exams report retrieved successfully",
  "year": 2025,
  "total_records": 15,
  "filtered_records": 15,
  "data": [
    {
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
      "assign": "30",
      "questions": "20"
    }
  ],
  "timestamp": "2025-10-07 10:30:00"
}
```

---

## Filter Parameters

All filter parameters are **optional**. If no parameters are provided, the API returns current year's data.

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `from_date` | string | Start date for date range filter (YYYY-MM-DD) | `"2025-01-01"` |
| `to_date` | string | End date for date range filter (YYYY-MM-DD) | `"2025-10-07"` |

### Date Range Filtering

- If both `from_date` and `to_date` are provided, returns data between those dates
- If only `from_date` is provided, returns data from that date onwards
- If only `to_date` is provided, returns data up to that date
- If no dates are provided, defaults to current year (January 1 to December 31)

### Graceful Null/Empty Handling

- Empty parameters (`{}`) return current year's data
- `null` values are treated as "no filter"

---

## Response Format

### Success Response

```json
{
  "status": 1,
  "message": "Success message",
  "filters_applied": { },
  "total_records": 0,
  "filtered_records": 0,
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

## Response Fields

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
| `assign` | string | Number of students assigned to the exam |
| `questions` | string | Number of questions in the exam |

---

## Usage Examples

### Example 1: Get Current Year's Exams

```bash
curl -X POST "http://localhost/amt/api/online-exams-report/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 2: Get Exams for Date Range

```bash
curl -X POST "http://localhost/amt/api/online-exams-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "from_date": "2025-01-01",
    "to_date": "2025-10-07"
  }'
```

### Example 3: Get Exams from Specific Date

```bash
curl -X POST "http://localhost/amt/api/online-exams-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "from_date": "2025-09-01"
  }'
```

### Example 4: Get Exams for Specific Quarter

```bash
curl -X POST "http://localhost/amt/api/online-exams-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "from_date": "2025-07-01",
    "to_date": "2025-09-30"
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
const getOnlineExamsReport = async (fromDate, toDate) => {
  try {
    const response = await fetch('http://localhost/amt/api/online-exams-report/filter', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Client-Service': 'smartschool',
        'Auth-Key': 'schoolAdmin@'
      },
      body: JSON.stringify({
        from_date: fromDate,
        to_date: toDate
      })
    });
    
    const data = await response.json();
    
    if (data.status === 1) {
      console.log('Online Exams Report:', data.data);
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
getOnlineExamsReport('2025-01-01', '2025-10-07');
```

### PHP (cURL)

```php
<?php
function getOnlineExamsReport($fromDate = null, $toDate = null) {
    $url = 'http://localhost/amt/api/online-exams-report/filter';
    
    $data = array();
    if ($fromDate !== null) $data['from_date'] = $fromDate;
    if ($toDate !== null) $data['to_date'] = $toDate;
    
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
$examsReport = getOnlineExamsReport('2025-01-01', '2025-10-07');
print_r($examsReport);
?>
```

### Python (Requests)

```python
import requests
import json

def get_online_exams_report(from_date=None, to_date=None):
    url = 'http://localhost/amt/api/online-exams-report/filter'
    
    headers = {
        'Content-Type': 'application/json',
        'Client-Service': 'smartschool',
        'Auth-Key': 'schoolAdmin@'
    }
    
    data = {}
    if from_date is not None:
        data['from_date'] = from_date
    if to_date is not None:
        data['to_date'] = to_date
    
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
exams_report = get_online_exams_report('2025-01-01', '2025-10-07')
print(exams_report)
```

---

## Notes

1. **Date Format:** All dates should be in `YYYY-MM-DD` format.
2. **Current Year Default:** If no date parameters are provided, the API defaults to the current year.
3. **Assignment Count:** The `assign` field shows how many students are assigned to each exam.
4. **Question Count:** The `questions` field shows the total number of questions in each exam.
5. **Rank Status:** The `is_rank_generated` field indicates whether ranks have been calculated for the exam.

---

## Support

For issues or questions regarding this API, please contact the development team or refer to the main API documentation.

**Last Updated:** October 2025

