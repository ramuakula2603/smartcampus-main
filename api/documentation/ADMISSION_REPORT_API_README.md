# Admission Report API - README

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

The **Admission Report API** provides flexible endpoints for retrieving student admission information with advanced filtering capabilities. It's designed to handle null/empty parameters gracefully, making it easy to use for various client applications.

### Key Highlights
- ✅ **Graceful null/empty handling** - Returns all records when no filters provided
- ✅ **Multi-select support** - Filter by multiple classes or admission years
- ✅ **Year filtering** - Filter students by admission year
- ✅ **Session-aware** - Automatically uses current session or accepts custom session
- ✅ **RESTful design** - Follows REST API best practices
- ✅ **Consistent patterns** - Matches existing API structure (Student Report, Guardian Report)

---

## 🚀 Quick Start

### 1. Basic Request (All Students)
```bash
curl -X POST "http://localhost/amt/api/admission-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### 2. Filter by Class
```bash
curl -X POST "http://localhost/amt/api/admission-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": 1}'
```

### 3. Filter by Year
```bash
curl -X POST "http://localhost/amt/api/admission-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"year": 2024}'
```

### 4. Multiple Filters
```bash
curl -X POST "http://localhost/amt/api/admission-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": 1, "year": 2024}'
```

---

## ✨ Features

### 1. Flexible Filtering
Filter students by:
- **Class** (single or multiple)
- **Admission Year** (single or multiple)
- **Session** (defaults to current session)

### 2. Graceful Handling
The API intelligently handles:
- Empty request bodies `{}`
- Null values `{"class_id": null, "year": null}`
- Empty arrays `{"class_id": [], "year": []}`
- Mixed filters `{"class_id": 1, "year": null}`

All of these return all active students for the session.

### 3. Multi-Select Support
Pass single values or arrays:
```json
// Single values
{"class_id": 1, "year": 2024}

// Multiple values
{"class_id": [1, 2, 3], "year": [2023, 2024]}
```

### 4. Comprehensive Response
Each student record includes:
- Basic info (name, admission no, admission date)
- Class and section details
- Session information
- Student mobile number
- Guardian information (name, relation, phone)

---

## 📚 Documentation

### Complete Documentation
- **[Full API Documentation](ADMISSION_REPORT_API_DOCUMENTATION.md)** - Complete reference with all details
- **[Quick Reference](ADMISSION_REPORT_API_QUICK_REFERENCE.md)** - Quick lookup guide
- **[Implementation Summary](ADMISSION_REPORT_API_IMPLEMENTATION_SUMMARY.md)** - Technical implementation details

### API Endpoints

#### 1. Filter Admission Report
**URL:** `POST /api/admission-report/filter`

**Parameters (all optional):**
- `class_id` - integer or array
- `year` - integer or array (admission year)
- `session_id` - integer

#### 2. List All Admissions
**URL:** `POST /api/admission-report/list`

**Parameters:** None (returns all active students)

---

## 🧪 Testing

### Interactive HTML Tester
Open `admission_report_api_test.html` in your browser for an interactive testing interface with:
- Pre-configured test scenarios
- Custom request builder
- Real-time response display
- Color-coded success/error indicators

### Test Scenarios Included
1. ✅ All Students (no filters)
2. ✅ Single Class
3. ✅ Multiple Classes
4. ✅ Single Year
5. ✅ Multiple Years
6. ✅ Class & Year
7. ✅ Multiple Classes & Years
8. ✅ Null Filters
9. ✅ Empty Arrays
10. ✅ List Endpoint

### Manual Testing with cURL

**Test 1: All Students**
```bash
curl -X POST "http://localhost/amt/api/admission-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Test 2: Filter by Multiple Classes**
```bash
curl -X POST "http://localhost/amt/api/admission-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": [1, 2, 3]}'
```

**Test 3: Filter by Year**
```bash
curl -X POST "http://localhost/amt/api/admission-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"year": 2024}'
```

**Test 4: Complex Filter**
```bash
curl -X POST "http://localhost/amt/api/admission-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": [1, 2], "year": [2023, 2024]}'
```

---

## 💡 Examples

### JavaScript/Fetch
```javascript
async function getAdmissionReport(filters = {}) {
  try {
    const response = await fetch('http://localhost/amt/api/admission-report/filter', {
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
getAdmissionReport(); // All students
getAdmissionReport({ class_id: 1 }); // Students in class 1
getAdmissionReport({ year: 2024 }); // Students admitted in 2024
getAdmissionReport({ class_id: [1, 2], year: [2023, 2024] }); // Multiple filters
```

### PHP
```php
<?php
function getAdmissionReport($filters = []) {
    $url = 'http://localhost/amt/api/admission-report/filter';
    
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
$allStudents = getAdmissionReport();
$classStudents = getAdmissionReport(['class_id' => 1]);
$yearStudents = getAdmissionReport(['year' => 2024]);
$filteredStudents = getAdmissionReport([
    'class_id' => [1, 2],
    'year' => [2023, 2024]
]);
?>
```

### Python
```python
import requests
import json

def get_admission_report(filters=None):
    url = 'http://localhost/amt/api/admission-report/filter'
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
all_students = get_admission_report()
class_students = get_admission_report({'class_id': 1})
year_students = get_admission_report({'year': 2024})
filtered_students = get_admission_report({
    'class_id': [1, 2],
    'year': [2023, 2024]
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
curl -X POST "http://localhost/amt/api/admission-report/filter" ...
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
{"class_id": 1, "year": 2024}

// ❌ Invalid (missing quotes)
{class_id: 1, year: 2024}
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
   - Ensure tables exist: `students`, `student_session`, `classes`, `sections`, `sessions`
   - Check if data exists in these tables
   - Verify admission_date column has valid dates

4. **Check Routes**
   - Verify routes are configured in `api/application/config/routes.php`

5. **Use Interactive Tester**
   - Open `admission_report_api_test.html` for visual debugging

---

## 📞 Support

### Getting Help
1. Review the [Complete Documentation](ADMISSION_REPORT_API_DOCUMENTATION.md)
2. Check the [Quick Reference](ADMISSION_REPORT_API_QUICK_REFERENCE.md)
3. Use the [Interactive Tester](admission_report_api_test.html)
4. Review application logs
5. Check existing API implementations (Student Report, Guardian Report)

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
- **Student Report API** - Student information with additional filters
- **Guardian Report API** - Guardian information filtering
- **Disable Reason API** - CRUD operations for disable reasons
- **Fee Master API** - Fee management operations

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

You're now ready to use the Admission Report API! Start with the interactive tester or try the quick start examples above.

For detailed information, refer to the [Complete API Documentation](ADMISSION_REPORT_API_DOCUMENTATION.md).

Happy coding! 🚀

