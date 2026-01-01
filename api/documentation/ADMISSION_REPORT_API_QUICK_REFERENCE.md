# Admission Report API - Quick Reference

## 🚀 Quick Start

### Base URL
```
http://localhost/amt/api
```

### Authentication Headers
```
Client-Service: smartschool
Auth-Key: schoolAdmin@
Content-Type: application/json
```

---

## 📋 Endpoints

### 1. Filter Admission Report
**POST** `/admission-report/filter`

**Parameters (all optional):**
- `class_id` - integer or array
- `year` - integer or array (admission year)
- `session_id` - integer (defaults to current)

**Quick Example:**
```bash
curl -X POST "http://localhost/amt/api/admission-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": 1, "year": 2024}'
```

---

### 2. List All Admissions
**POST** `/admission-report/list`

**Parameters:** None

**Quick Example:**
```bash
curl -X POST "http://localhost/amt/api/admission-report/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

## 💡 Common Use Cases

### Get All Students
```json
{}
```

### Filter by Single Class
```json
{"class_id": 1}
```

### Filter by Multiple Classes
```json
{"class_id": [1, 2, 3]}
```

### Filter by Single Year
```json
{"year": 2024}
```

### Filter by Multiple Years
```json
{"year": [2023, 2024]}
```

### Filter by Class and Year
```json
{"class_id": 1, "year": 2024}
```

### Filter by Multiple Classes and Years
```json
{
  "class_id": [1, 2],
  "year": [2023, 2024]
}
```

### Filter with Custom Session
```json
{
  "class_id": 1,
  "year": 2024,
  "session_id": 18
}
```

---

## 📊 Response Format

### Success Response
```json
{
  "status": 1,
  "message": "Admission report retrieved successfully",
  "filters_applied": {
    "class_id": [1],
    "year": [2024],
    "session_id": 18
  },
  "total_records": 25,
  "data": [
    {
      "id": "123",
      "admission_no": "ADM001",
      "admission_date": "2024-04-15",
      "firstname": "John",
      "middlename": "Michael",
      "lastname": "Doe",
      "class_id": "1",
      "class": "Class 1",
      "section_id": "2",
      "section": "A",
      "session_id": "18",
      "session": "2024-2025",
      "mobileno": "9876543210",
      "guardian_name": "Robert Doe",
      "guardian_relation": "Father",
      "guardian_phone": "9876543210",
      "is_active": "yes"
    }
  ],
  "timestamp": "2025-10-07 10:30:45"
}
```

### Error Response
```json
{
  "status": 0,
  "message": "Error message"
}
```

---

## 🔑 Response Fields

| Field | Type | Description |
|-------|------|-------------|
| `id` | string | Student ID |
| `admission_no` | string | Admission number |
| `admission_date` | string | Admission date (YYYY-MM-DD) |
| `firstname` | string | First name |
| `middlename` | string | Middle name |
| `lastname` | string | Last name |
| `class_id` | string | Class ID |
| `class` | string | Class name |
| `section_id` | string | Section ID |
| `section` | string | Section name |
| `session_id` | string | Session ID |
| `session` | string | Session name |
| `mobileno` | string | Student mobile |
| `guardian_name` | string | Guardian name |
| `guardian_relation` | string | Guardian relation |
| `guardian_phone` | string | Guardian phone |
| `is_active` | string | Active status |

---

## ⚠️ Common Errors

### 400 Bad Request
**Cause:** Wrong HTTP method (not POST)
**Solution:** Use POST method

### 401 Unauthorized
**Cause:** Invalid authentication headers
**Solution:** Check `Client-Service` and `Auth-Key` headers

### 500 Internal Server Error
**Cause:** Server-side error
**Solution:** Check logs at `api/application/logs/`

---

## 🧪 Testing Checklist

- [ ] Test with no filters (should return all students)
- [ ] Test with single class filter
- [ ] Test with multiple classes (array)
- [ ] Test with single year filter
- [ ] Test with multiple years (array)
- [ ] Test with class and year filters
- [ ] Test with null values (should return all)
- [ ] Test with empty arrays (should return all)
- [ ] Test list endpoint
- [ ] Test with wrong HTTP method (should fail)
- [ ] Test with wrong auth headers (should fail)

---

## 💻 Code Examples

### JavaScript
```javascript
async function getAdmissionReport(filters = {}) {
  const response = await fetch('http://localhost/amt/api/admission-report/filter', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Client-Service': 'smartschool',
      'Auth-Key': 'schoolAdmin@'
    },
    body: JSON.stringify(filters)
  });
  return await response.json();
}

// Usage
const data = await getAdmissionReport({ class_id: 1, year: 2024 });
```

### PHP
```php
function getAdmissionReport($filters = []) {
    $ch = curl_init('http://localhost/amt/api/admission-report/filter');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($filters));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Client-Service: smartschool',
        'Auth-Key: schoolAdmin@'
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

// Usage
$data = getAdmissionReport(['class_id' => 1, 'year' => 2024]);
```

### Python
```python
import requests

def get_admission_report(filters=None):
    url = 'http://localhost/amt/api/admission-report/filter'
    headers = {
        'Content-Type': 'application/json',
        'Client-Service': 'smartschool',
        'Auth-Key': 'schoolAdmin@'
    }
    response = requests.post(url, headers=headers, json=filters or {})
    return response.json()

# Usage
data = get_admission_report({'class_id': 1, 'year': 2024})
```

---

## 🎯 Key Features

✅ **Graceful Null Handling** - Empty filters return all records
✅ **Multi-Select Support** - Pass arrays for multiple values
✅ **Year Filtering** - Filter by admission year(s)
✅ **Session-Aware** - Auto-uses current session
✅ **Consistent Format** - Same response structure across all endpoints
✅ **Comprehensive Data** - Includes admission date and all student information

---

## 📚 Related Documentation

- **Full Documentation:** `ADMISSION_REPORT_API_DOCUMENTATION.md`
- **Implementation Summary:** `ADMISSION_REPORT_API_IMPLEMENTATION_SUMMARY.md`
- **README:** `ADMISSION_REPORT_API_README.md`
- **Interactive Tester:** `admission_report_api_test.html`

---

## 🔗 Related APIs

- Student Report API
- Guardian Report API
- Disable Reason API
- Fee Master API

All follow the same authentication and response patterns.

