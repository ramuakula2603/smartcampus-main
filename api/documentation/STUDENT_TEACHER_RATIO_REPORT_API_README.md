# Student Teacher Ratio Report API - README

## 📋 Overview

The **Student Teacher Ratio Report API** provides flexible endpoints for retrieving student-teacher ratio statistics with advanced filtering capabilities. It returns aggregated counts of students (total, male, female) and teachers grouped by class and section, along with calculated ratios for analysis.

### Key Highlights
- ✅ **Comprehensive statistics** - Returns student counts, teacher counts, and calculated ratios
- ✅ **Flexible filtering** - Filter by class, section, and session
- ✅ **Graceful null/empty handling** - Returns all records when no filters provided
- ✅ **Multi-select support** - Filter by multiple classes or sections
- ✅ **Automatic ratio calculation** - Boys:girls ratio and student:teacher ratio
- ✅ **Summary statistics** - Overall totals and ratios across all filtered records
- ✅ **RESTful design** - Follows REST API best practices

---

## 🚀 Quick Start

### 1. Basic Request (All Classes and Sections)
```bash
curl -X POST "http://localhost/amt/api/student-teacher-ratio-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### 2. Filter by Class
```bash
curl -X POST "http://localhost/amt/api/student-teacher-ratio-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": 1}'
```

### 3. Filter by Multiple Classes
```bash
curl -X POST "http://localhost/amt/api/student-teacher-ratio-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": [1, 2, 3]}'
```

### 4. Filter by Class and Section
```bash
curl -X POST "http://localhost/amt/api/student-teacher-ratio-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": 1, "section_id": 2}'
```

---

## ✨ Features

### 1. Comprehensive Statistics
Each record includes:
- **Student Counts**: total_student, male, female
- **Teacher Count**: total_teacher (unique teachers assigned to class-section)
- **Class Information**: class_id, class, section_id, section
- **Calculated Ratios**: boys_girls_ratio, teacher_ratio (student:teacher)

### 2. Summary Statistics
Response includes overall summary with:
- **total_students** - Total students across all filtered records
- **total_boys** - Total male students
- **total_girls** - Total female students
- **total_teachers** - Total unique teachers
- **boys_girls_ratio** - Overall boys:girls ratio
- **student_teacher_ratio** - Overall student:teacher ratio

### 3. Flexible Filtering
Filter by:
- **Class** (single or multiple)
- **Section** (single or multiple)
- **Session** (defaults to current session)

### 4. Ratio Calculation
Ratios are calculated in format "1:X" where:
- **Boys:Girls Ratio** - Shows gender distribution (e.g., "1:0.8" means 1 boy for every 0.8 girls)
- **Student:Teacher Ratio** - Shows teaching load (e.g., "1:0.11" means 1 student for every 0.11 teachers, or ~9 students per teacher)

---

## 📚 API Endpoints

### 1. Filter Student Teacher Ratio Report
**URL:** `POST /api/student-teacher-ratio-report/filter`

**Parameters (all optional):**
- `class_id` - integer or array
- `section_id` - integer or array
- `session_id` - integer (defaults to current session)

### 2. List All Student Teacher Ratio Data
**URL:** `POST /api/student-teacher-ratio-report/list`

**Parameters:** None (returns all data for current session)

---

## 🧪 Testing

### Interactive HTML Tester
Open `student_teacher_ratio_report_api_test.html` in your browser for an interactive testing interface.

### Manual Testing with cURL

**Test 1: All Classes and Sections**
```bash
curl -X POST "http://localhost/amt/api/student-teacher-ratio-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Test 2: Single Class**
```bash
curl -X POST "http://localhost/amt/api/student-teacher-ratio-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": 1}'
```

**Test 3: Multiple Classes**
```bash
curl -X POST "http://localhost/amt/api/student-teacher-ratio-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": [1, 2, 3]}'
```

**Test 4: Class and Section**
```bash
curl -X POST "http://localhost/amt/api/student-teacher-ratio-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": 1, "section_id": 2}'
```

---

## 💡 Code Examples

### JavaScript
```javascript
async function getStudentTeacherRatioReport(filters = {}) {
  const response = await fetch('http://localhost/amt/api/student-teacher-ratio-report/filter', {
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
const data = await getStudentTeacherRatioReport({ class_id: [1, 2, 3] });
console.log('Summary:', data.summary);
console.log('Data:', data.data);
```

### PHP
```php
function getStudentTeacherRatioReport($filters = []) {
    $ch = curl_init('http://localhost/amt/api/student-teacher-ratio-report/filter');
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
$data = getStudentTeacherRatioReport(['class_id' => [1, 2, 3]]);
print_r($data['summary']);
```

### Python
```python
import requests
import json

def get_student_teacher_ratio_report(filters={}):
    url = 'http://localhost/amt/api/student-teacher-ratio-report/filter'
    headers = {
        'Content-Type': 'application/json',
        'Client-Service': 'smartschool',
        'Auth-Key': 'schoolAdmin@'
    }
    response = requests.post(url, headers=headers, json=filters)
    return response.json()

# Usage
data = get_student_teacher_ratio_report({'class_id': [1, 2, 3]})
print('Summary:', data['summary'])
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
- Check if students table has data
- Verify subject_timetable has teacher assignments
- Review application logs

#### 4. Zero Teachers
**Solution:**
- Ensure teachers are assigned in subject_timetable
- Verify staff.is_active = '1'
- Check session_id matches current session

---

## 📊 Response Example

```json
{
  "status": 1,
  "message": "Student teacher ratio report retrieved successfully",
  "filters_applied": {
    "class_id": [1],
    "section_id": null,
    "session_id": 18
  },
  "total_records": 2,
  "summary": {
    "total_students": 90,
    "total_boys": 50,
    "total_girls": 40,
    "total_teachers": 10,
    "boys_girls_ratio": "1:0.8",
    "student_teacher_ratio": "1:0.11"
  },
  "data": [
    {
      "total_student": "45",
      "male": "25",
      "female": "20",
      "class": "Class 1",
      "section": "A",
      "class_id": "1",
      "section_id": "1",
      "total_teacher": 5,
      "boys_girls_ratio": "1:0.8",
      "teacher_ratio": "1:0.11"
    },
    {
      "total_student": "45",
      "male": "25",
      "female": "20",
      "class": "Class 1",
      "section": "B",
      "class_id": "1",
      "section_id": "2",
      "total_teacher": 5,
      "boys_girls_ratio": "1:0.8",
      "teacher_ratio": "1:0.11"
    }
  ],
  "timestamp": "2025-10-07 10:30:45"
}
```

---

## 📝 Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | 2025-10-07 | Initial release |

---

## 🎓 Related APIs

- **Boys Girls Ratio Report API**
- **Student Report API**
- **Student Profile Report API**
- **Class Subject Report API**

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
- [ ] Teachers are assigned in subject_timetable

---

## 🎉 Success!

You're now ready to use the Student Teacher Ratio Report API!

Happy coding! 🚀

