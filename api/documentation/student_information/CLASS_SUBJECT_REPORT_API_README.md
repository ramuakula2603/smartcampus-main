# Class Subject Report API - README

## 📋 Overview

The **Class Subject Report API** provides flexible endpoints for retrieving class subject assignment information with advanced filtering capabilities. It returns detailed information about subjects assigned to classes and sections, including teacher assignments and timetable details.

### Key Highlights
- ✅ **Graceful null/empty handling** - Returns all records when no filters provided
- ✅ **Multi-select support** - Filter by multiple classes or sections
- ✅ **Class & Section filtering** - Filter subjects by class and/or section
- ✅ **Session-aware** - Automatically uses current session or accepts custom session
- ✅ **RESTful design** - Follows REST API best practices
- ✅ **Comprehensive data** - Includes subject, teacher, and timetable information
- ✅ **Consistent patterns** - Matches existing API structure

---

## 🚀 Quick Start

### 1. Basic Request (All Subjects)
```bash
curl -X POST "http://localhost/amt/api/class-subject-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### 2. Filter by Class
```bash
curl -X POST "http://localhost/amt/api/class-subject-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": 1}'
```

### 3. Filter by Section
```bash
curl -X POST "http://localhost/amt/api/class-subject-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"section_id": 2}'
```

### 4. Multiple Filters
```bash
curl -X POST "http://localhost/amt/api/class-subject-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": 1, "section_id": 2}'
```

---

## ✨ Features

### 1. Flexible Filtering
Filter subject assignments by:
- **Class** (single or multiple)
- **Section** (single or multiple)
- **Session** (defaults to current session)

### 2. Graceful Handling
The API intelligently handles:
- Empty request bodies `{}`
- Null values `{"class_id": null, "section_id": null}`
- Empty arrays `{"class_id": [], "section_id": []}`
- Mixed filters `{"class_id": 1, "section_id": null}`

All of these return all subject assignments for the session.

### 3. Multi-Select Support
Pass single values or arrays:
```json
// Single values
{"class_id": 1, "section_id": 2}

// Multiple values
{"class_id": [1, 2, 3], "section_id": [1, 2]}
```

### 4. Comprehensive Response
Each subject assignment record includes:
- **Subject information** (name, code, type)
- **Teacher details** (name, surname, employee_id)
- **Class and section** (class_name, section_name)
- **Timetable details** (day, time_from, time_to, room_no)
- **Session information**
- **Class teacher indicator**

---

## 📚 API Endpoints

### 1. Filter Class Subject Report
**URL:** `POST /api/class-subject-report/filter`

**Parameters (all optional):**
- `class_id` - integer or array
- `section_id` - integer or array
- `session_id` - integer

**Response Fields:**
- `timetable_id` - Subject timetable entry ID
- `subject_id` - Subject ID
- `subject_name` - Subject name
- `subject_code` - Subject code
- `subject_type` - Subject type
- `staff_id` - Teacher ID
- `staff_name` - Teacher first name
- `staff_surname` - Teacher surname
- `employee_id` - Teacher employee ID
- `class_id` - Class ID
- `class_name` - Class name
- `section_id` - Section ID
- `section_name` - Section name
- `day` - Day of week
- `time_from` - Start time
- `time_to` - End time
- `start_time` - Start time (alternative format)
- `end_time` - End time (alternative format)
- `room_no` - Room number
- `session_id` - Session ID
- `subject_group_id` - Subject group ID
- `subject_group_subject_id` - Subject group subject ID
- `class_teacher` - Class teacher staff ID (if applicable)

### 2. List All Class Subjects
**URL:** `POST /api/class-subject-report/list`

**Parameters:** None (returns all subject assignments)

---

## 🧪 Testing

### Interactive HTML Tester
Open `class_subject_report_api_test.html` in your browser for an interactive testing interface.

### Manual Testing with cURL

**Test 1: All Subject Assignments**
```bash
curl -X POST "http://localhost/amt/api/class-subject-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Test 2: Filter by Multiple Classes**
```bash
curl -X POST "http://localhost/amt/api/class-subject-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": [1, 2, 3]}'
```

**Test 3: Filter by Class and Section**
```bash
curl -X POST "http://localhost/amt/api/class-subject-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": 1, "section_id": 2}'
```

---

## 💡 Code Examples

### JavaScript
```javascript
async function getClassSubjectReport(filters = {}) {
  const response = await fetch('http://localhost/amt/api/class-subject-report/filter', {
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
const data = await getClassSubjectReport({ class_id: 1, section_id: 2 });
console.log('Subject assignments:', data.data);
```

### PHP
```php
function getClassSubjectReport($filters = []) {
    $ch = curl_init('http://localhost/amt/api/class-subject-report/filter');
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
$data = getClassSubjectReport(['class_id' => 1, 'section_id' => 2]);
print_r($data['data']);
```

### Python
```python
import requests
import json

def get_class_subject_report(filters=None):
    url = 'http://localhost/amt/api/class-subject-report/filter'
    headers = {
        'Content-Type': 'application/json',
        'Client-Service': 'smartschool',
        'Auth-Key': 'schoolAdmin@'
    }
    response = requests.post(url, headers=headers, json=filters or {})
    return response.json()

# Usage
data = get_class_subject_report({'class_id': 1, 'section_id': 2})
print('Subject assignments:', data['data'])
```

---

## 🔧 Troubleshooting

### Common Issues

#### 1. 401 Unauthorized Error
**Solution:** Ensure you're sending the correct headers:
```
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### 2. 400 Bad Request
**Solution:** Use POST method, not GET

#### 3. Empty Response
**Solution:**
- Check if subject_timetable table has data
- Verify staff.is_active = 1
- Check session_id is correct
- Review application logs

#### 4. 500 Internal Server Error
**Solution:**
- Check application logs at `api/application/logs/`
- Verify database connection
- Ensure all required models are loaded

---

## 📊 Database Schema

### Tables Used
- `subject_timetable` - Main table for subject assignments
- `subject_group_subjects` - Subject group relationships
- `subjects` - Subject information
- `staff` - Teacher information
- `classes` - Class information
- `sections` - Section information
- `class_teacher` - Class teacher assignments

### Key Relationships
- subject_timetable → subject_group_subjects (subject_group_subject_id)
- subject_group_subjects → subjects (subject_id)
- subject_timetable → staff (staff_id)
- subject_timetable → classes (class_id)
- subject_timetable → sections (section_id)
- class_teacher → staff (staff_id)

---

## 📝 Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | 2025-10-07 | Initial release |

---

## 🎓 Related APIs

- **Student Report API**
- **Guardian Report API**
- **Admission Report API**
- **Login Detail Report API**
- **Parent Login Detail Report API**

All APIs share the same authentication mechanism and response format.

---

## ✅ Quick Checklist

Before using the API, ensure:
- [ ] Server is running
- [ ] Database is accessible
- [ ] Authentication headers are correct
- [ ] Using POST method
- [ ] Request body is valid JSON
- [ ] Routes are configured
- [ ] Models are loaded correctly
- [ ] subject_timetable table has data

---

## 🎉 Success!

You're now ready to use the Class Subject Report API!

Happy coding! 🚀

