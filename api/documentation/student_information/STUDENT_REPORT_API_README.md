# Student Report API - README

## 📋 Table of Contents
- [Overview](#overview)
- [Quick Start](#quick-start)
- [Features](#features)
- [Documentation](#documentation)
- [Testing](#testing)
- [Examples](#examples)
- [Troubleshooting](#troubleshooting)

---

## 🎯 Overview

The **Student Report API** provides flexible endpoints for retrieving student report data with advanced filtering capabilities. It's designed to handle null/empty parameters gracefully, making it easy to use for various client applications.

### Key Highlights
- ✅ **Graceful null/empty handling** - Returns all records when no filters provided
- ✅ **Multi-select support** - Filter by multiple classes, sections, or categories
- ✅ **Session-aware** - Automatically uses current session or accepts custom session
- ✅ **RESTful design** - Follows REST API best practices
- ✅ **Consistent patterns** - Matches existing API structure (Disable Reason, Fee Master)

---

## 🚀 Quick Start

### 1. Basic Request (All Students)
```bash
curl -X POST "http://localhost/amt/api/student-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### 2. Filter by Class
```bash
curl -X POST "http://localhost/amt/api/student-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": 1}'
```

### 3. Multiple Filters
```bash
curl -X POST "http://localhost/amt/api/student-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": 1, "section_id": 2, "category_id": 3}'
```

---

## ✨ Features

### 1. Flexible Filtering
Filter students by:
- **Class** (single or multiple)
- **Section** (single or multiple)
- **Category** (single or multiple)
- **Session** (defaults to current session)

### 2. Graceful Handling
The API intelligently handles:
- Empty request bodies `{}`
- Null values `{"class_id": null}`
- Empty arrays `{"class_id": []}`
- Mixed filters `{"class_id": 1, "section_id": null}`

All of these return all active students for the session.

### 3. Multi-Select Support
Pass single values or arrays:
```json
// Single value
{"class_id": 1}

// Multiple values
{"class_id": [1, 2, 3]}
```

### 4. Comprehensive Response
Each student record includes:
- Basic info (name, admission no, roll no)
- Class and section details
- Category information
- Contact details
- Guardian information
- Addresses
- Identification numbers (Aadhar, Samagra)

---

## 📚 Documentation

### Complete Documentation
- **[Full API Documentation](STUDENT_REPORT_API_DOCUMENTATION.md)** - Complete reference with all details
- **[Quick Reference](STUDENT_REPORT_API_QUICK_REFERENCE.md)** - Quick lookup guide
- **[Implementation Summary](STUDENT_REPORT_API_IMPLEMENTATION_SUMMARY.md)** - Technical implementation details

### API Endpoints

#### 1. Filter Student Report
**URL:** `POST /api/student-report/filter`

**Parameters (all optional):**
- `class_id` - integer or array
- `section_id` - integer or array
- `category_id` - integer or array
- `session_id` - integer

#### 2. List All Students
**URL:** `POST /api/student-report/list`

**Parameters:** None (returns all active students)

---

## 🧪 Testing

### Interactive HTML Tester
Open `student_report_api_test.html` in your browser for an interactive testing interface with:
- Pre-configured test scenarios
- Custom request builder
- Real-time response display
- Color-coded success/error indicators

### Test Scenarios Included
1. ✅ All Students (no filters)
2. ✅ Single Class
3. ✅ Multiple Classes
4. ✅ Class & Section
5. ✅ With Category
6. ✅ Null Filters
7. ✅ List Endpoint
8. ✅ Complex Filter

### Manual Testing with cURL

**Test 1: All Students**
```bash
curl -X POST "http://localhost/amt/api/student-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Test 2: Filter by Multiple Classes**
```bash
curl -X POST "http://localhost/amt/api/student-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": [1, 2, 3]}'
```

**Test 3: Complex Filter**
```bash
curl -X POST "http://localhost/amt/api/student-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": [1, 2], "section_id": [1, 2], "category_id": 3}'
```

---

## 💡 Examples

### JavaScript/Fetch
```javascript
async function getStudentReport(filters = {}) {
  try {
    const response = await fetch('http://localhost/amt/api/student-report/filter', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Client-Service': 'smartschool',
        'Auth-Key': 'schoolAdmin@'
      },
      body: JSON.stringify(filters)
    });
    
    const data = await response.json();
    
    if (data.status === 1) {
      console.log(`Found ${data.total_records} students`);
      return data.data;
    } else {
      console.error('Error:', data.message);
      return [];
    }
  } catch (error) {
    console.error('Request failed:', error);
    return [];
  }
}

// Usage examples
getStudentReport(); // All students
getStudentReport({ class_id: 1 }); // Students in class 1
getStudentReport({ class_id: [1, 2], section_id: [1, 2] }); // Multiple filters
```

### PHP
```php
<?php
function getStudentReport($filters = []) {
    $url = 'http://localhost/amt/api/student-report/filter';
    
    $ch = curl_init($url);
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
$allStudents = getStudentReport();
$classStudents = getStudentReport(['class_id' => 1]);
$filteredStudents = getStudentReport([
    'class_id' => [1, 2],
    'section_id' => [1, 2]
]);
?>
```

### Python
```python
import requests
import json

def get_student_report(filters=None):
    url = 'http://localhost/amt/api/student-report/filter'
    headers = {
        'Content-Type': 'application/json',
        'Client-Service': 'smartschool',
        'Auth-Key': 'schoolAdmin@'
    }
    
    if filters is None:
        filters = {}
    
    response = requests.post(url, headers=headers, json=filters)
    return response.json()

# Usage
all_students = get_student_report()
class_students = get_student_report({'class_id': 1})
filtered_students = get_student_report({
    'class_id': [1, 2],
    'section_id': [1, 2]
})
```

---

## 🔧 Troubleshooting

### Common Issues

#### 1. 401 Unauthorized Error
**Problem:** Invalid authentication headers

**Solution:** Ensure you're sending the correct headers:
```
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### 2. 400 Bad Request
**Problem:** Wrong HTTP method

**Solution:** Use POST method, not GET:
```bash
curl -X POST "http://localhost/amt/api/student-report/filter" ...
```

#### 3. Empty Response
**Problem:** No students found or database connection issue

**Solution:**
- Check if students exist in the database
- Verify session_id is correct
- Check database connection
- Review application logs

#### 4. Invalid JSON Error
**Problem:** Malformed JSON in request body

**Solution:** Validate your JSON:
```json
// ✅ Valid
{"class_id": 1}

// ❌ Invalid (missing quotes)
{class_id: 1}
```

#### 5. 500 Internal Server Error
**Problem:** Server-side error

**Solution:**
- Check application logs at `api/application/logs/`
- Verify database connection
- Ensure all required models are loaded
- Check PHP error logs

### Debugging Tips

1. **Enable Error Logging**
   - Check `api/application/logs/` for detailed error messages

2. **Test with Simple Request**
   - Start with empty filters `{}`
   - Gradually add filters to isolate issues

3. **Verify Database**
   - Ensure tables exist: `students`, `student_session`, `classes`, `sections`, `categories`
   - Check if data exists in these tables

4. **Check Routes**
   - Verify routes are configured in `api/application/config/routes.php`

5. **Use Interactive Tester**
   - Open `student_report_api_test.html` for visual debugging

---

## 📞 Support

### Getting Help
1. Review the [Complete Documentation](STUDENT_REPORT_API_DOCUMENTATION.md)
2. Check the [Quick Reference](STUDENT_REPORT_API_QUICK_REFERENCE.md)
3. Use the [Interactive Tester](student_report_api_test.html)
4. Review application logs
5. Check existing API implementations (Disable Reason, Fee Master)

### Reporting Issues
When reporting issues, include:
- Request URL and method
- Request headers
- Request body
- Response received
- Error messages from logs
- Steps to reproduce

---

## 📝 Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | 2025-10-07 | Initial release |

---

## 🎓 Related APIs

This API follows the same patterns as:
- **Disable Reason API** - CRUD operations for disable reasons
- **Fee Master API** - Fee management operations
- **Online Admission API** - Admission filtering

All APIs share:
- Same authentication mechanism
- Consistent response format
- POST method for operations
- Comprehensive error handling

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

---

## 🎉 Success!

You're now ready to use the Student Report API! Start with the interactive tester or try the quick start examples above.

For detailed information, refer to the [Complete API Documentation](STUDENT_REPORT_API_DOCUMENTATION.md).

Happy coding! 🚀

