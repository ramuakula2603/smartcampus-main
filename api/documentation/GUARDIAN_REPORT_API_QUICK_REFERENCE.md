# Guardian Report API - Quick Reference

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

### 1. Filter Guardian Report
**POST** `/guardian-report/filter`

**Parameters (all optional):**
- `class_id` - integer or array
- `section_id` - integer or array
- `session_id` - integer (defaults to current)

**Quick Example:**
```bash
curl -X POST "http://localhost/amt/api/guardian-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": 1}'
```

---

### 2. List All Guardians
**POST** `/guardian-report/list`

**Parameters:** None

**Quick Example:**
```bash
curl -X POST "http://localhost/amt/api/guardian-report/list" \
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

### Filter by Class and Section
```json
{"class_id": 1, "section_id": 2}
```

### Filter by Multiple Classes and Sections
```json
{
  "class_id": [1, 2],
  "section_id": [1, 2, 3]
}
```

### Filter with Custom Session
```json
{
  "class_id": 1,
  "session_id": 18
}
```

---

## 📊 Response Format

### Success Response
```json
{
  "status": 1,
  "message": "Guardian report retrieved successfully",
  "filters_applied": {
    "class_id": [1],
    "section_id": [2],
    "session_id": 18
  },
  "total_records": 25,
  "data": [
    {
      "id": "123",
      "admission_no": "ADM001",
      "firstname": "John",
      "middlename": "Michael",
      "lastname": "Doe",
      "class_id": "1",
      "class": "Class 1",
      "section_id": "2",
      "section": "A",
      "mobileno": "9876543210",
      "guardian_name": "Robert Doe",
      "guardian_relation": "Father",
      "guardian_phone": "9876543210",
      "father_name": "Robert Doe",
      "father_phone": "9876543210",
      "mother_name": "Mary Doe",
      "mother_phone": "9876543211",
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
| `firstname` | string | First name |
| `middlename` | string | Middle name |
| `lastname` | string | Last name |
| `class_id` | string | Class ID |
| `class` | string | Class name |
| `section_id` | string | Section ID |
| `section` | string | Section name |
| `mobileno` | string | Student mobile |
| `guardian_name` | string | Guardian name |
| `guardian_relation` | string | Guardian relation |
| `guardian_phone` | string | Guardian phone |
| `father_name` | string | Father name |
| `father_phone` | string | Father phone |
| `mother_name` | string | Mother name |
| `mother_phone` | string | Mother phone |
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
- [ ] Test with class and section filters
- [ ] Test with null values (should return all)
- [ ] Test with empty arrays (should return all)
- [ ] Test list endpoint
- [ ] Test with wrong HTTP method (should fail)
- [ ] Test with wrong auth headers (should fail)

---

## 💻 Code Examples

### JavaScript
```javascript
async function getGuardianReport(filters = {}) {
  const response = await fetch('http://localhost/amt/api/guardian-report/filter', {
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
const data = await getGuardianReport({ class_id: 1 });
```

### PHP
```php
function getGuardianReport($filters = []) {
    $ch = curl_init('http://localhost/amt/api/guardian-report/filter');
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
$data = getGuardianReport(['class_id' => 1]);
```

### Python
```python
import requests

def get_guardian_report(filters=None):
    url = 'http://localhost/amt/api/guardian-report/filter'
    headers = {
        'Content-Type': 'application/json',
        'Client-Service': 'smartschool',
        'Auth-Key': 'schoolAdmin@'
    }
    response = requests.post(url, headers=headers, json=filters or {})
    return response.json()

# Usage
data = get_guardian_report({'class_id': 1})
```

---

## 🎯 Key Features

✅ **Graceful Null Handling** - Empty filters return all records
✅ **Multi-Select Support** - Pass arrays for multiple values
✅ **Session-Aware** - Auto-uses current session
✅ **Consistent Format** - Same response structure across all endpoints
✅ **Comprehensive Data** - Includes all guardian information

---

## 📚 Related Documentation

- **Full Documentation:** `GUARDIAN_REPORT_API_DOCUMENTATION.md`
- **Implementation Summary:** `GUARDIAN_REPORT_API_IMPLEMENTATION_SUMMARY.md`
- **README:** `GUARDIAN_REPORT_API_README.md`
- **Interactive Tester:** `guardian_report_api_test.html`

---

## 🔗 Related APIs

- Student Report API
- Disable Reason API
- Fee Master API

All follow the same authentication and response patterns.

