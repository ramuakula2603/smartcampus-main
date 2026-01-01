# Login Detail Report API - README

## 📋 Table of Contents
- [Overview](#overview)
- [Quick Start](#quick-start)
- [Features](#features)
- [Documentation](#documentation)
- [Testing](#testing)
- [Examples](#examples)
- [Troubleshooting](#troubleshooting)
- [Security](#security)

---

## 🎯 Overview

The **Login Detail Report API** provides flexible endpoints for retrieving student login credential information with advanced filtering capabilities. It's designed to handle null/empty parameters gracefully, making it easy to use for various client applications.

### Key Highlights
- ✅ **Graceful null/empty handling** - Returns all records when no filters provided
- ✅ **Multi-select support** - Filter by multiple classes or sections
- ✅ **Class & Section filtering** - Filter students by class and/or section
- ✅ **Session-aware** - Automatically uses current session or accepts custom session
- ✅ **RESTful design** - Follows REST API best practices
- ✅ **Login credentials** - Includes username and password for each student
- ✅ **Consistent patterns** - Matches existing API structure (Student Report, Guardian Report, Admission Report)

---

## 🚀 Quick Start

### 1. Basic Request (All Students)
```bash
curl -X POST "http://localhost/amt/api/login-detail-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### 2. Filter by Class
```bash
curl -X POST "http://localhost/amt/api/login-detail-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": 1}'
```

### 3. Filter by Section
```bash
curl -X POST "http://localhost/amt/api/login-detail-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"section_id": 2}'
```

### 4. Multiple Filters
```bash
curl -X POST "http://localhost/amt/api/login-detail-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": 1, "section_id": 2}'
```

---

## ✨ Features

### 1. Flexible Filtering
Filter students by:
- **Class** (single or multiple)
- **Section** (single or multiple)
- **Session** (defaults to current session)

### 2. Graceful Handling
The API intelligently handles:
- Empty request bodies `{}`
- Null values `{"class_id": null, "section_id": null}`
- Empty arrays `{"class_id": [], "section_id": []}`
- Mixed filters `{"class_id": 1, "section_id": null}`

All of these return all active students for the session.

### 3. Multi-Select Support
Pass single values or arrays:
```json
// Single values
{"class_id": 1, "section_id": 2}

// Multiple values
{"class_id": [1, 2, 3], "section_id": [1, 2]}
```

### 4. Comprehensive Response
Each student record includes:
- Basic info (name, admission no)
- Class and section details
- Session information
- Contact info (mobile, email)
- **Login credentials (username, password)**

---

## 📚 Documentation

### Complete Documentation
- **[Full API Documentation](LOGIN_DETAIL_REPORT_API_DOCUMENTATION.md)** - Complete reference with all details
- **[Quick Reference](LOGIN_DETAIL_REPORT_API_QUICK_REFERENCE.md)** - Quick lookup guide
- **[Implementation Summary](LOGIN_DETAIL_REPORT_API_IMPLEMENTATION_SUMMARY.md)** - Technical implementation details

### API Endpoints

#### 1. Filter Login Detail Report
**URL:** `POST /api/login-detail-report/filter`

**Parameters (all optional):**
- `class_id` - integer or array
- `section_id` - integer or array
- `session_id` - integer

#### 2. List All Login Details
**URL:** `POST /api/login-detail-report/list`

**Parameters:** None (returns all active students)

---

## 🧪 Testing

### Interactive HTML Tester
Open `login_detail_report_api_test.html` in your browser for an interactive testing interface with:
- Pre-configured test scenarios
- Custom request builder
- Real-time response display
- Color-coded success/error indicators

### Test Scenarios Included
1. ✅ All Students (no filters)
2. ✅ Single Class
3. ✅ Multiple Classes
4. ✅ Single Section
5. ✅ Multiple Sections
6. ✅ Class & Section
7. ✅ Multiple Classes & Sections
8. ✅ Null Filters
9. ✅ Empty Arrays
10. ✅ List Endpoint

### Manual Testing with cURL

**Test 1: All Students**
```bash
curl -X POST "http://localhost/amt/api/login-detail-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Test 2: Filter by Multiple Classes**
```bash
curl -X POST "http://localhost/amt/api/login-detail-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": [1, 2, 3]}'
```

**Test 3: Filter by Section**
```bash
curl -X POST "http://localhost/amt/api/login-detail-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"section_id": 2}'
```

**Test 4: Complex Filter**
```bash
curl -X POST "http://localhost/amt/api/login-detail-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": [1, 2], "section_id": [1, 2]}'
```

---

## 💡 Examples

### JavaScript/Fetch
```javascript
async function getLoginDetailReport(filters = {}) {
  try {
    const response = await fetch('http://localhost/amt/api/login-detail-report/filter', {
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
getLoginDetailReport(); // All students
getLoginDetailReport({ class_id: 1 }); // Students in class 1
getLoginDetailReport({ section_id: 2 }); // Students in section 2
getLoginDetailReport({ class_id: [1, 2], section_id: [1, 2] }); // Multiple filters
```

### PHP
```php
<?php
function getLoginDetailReport($filters = []) {
    $url = 'http://localhost/amt/api/login-detail-report/filter';
    
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
$allStudents = getLoginDetailReport();
$classStudents = getLoginDetailReport(['class_id' => 1]);
$sectionStudents = getLoginDetailReport(['section_id' => 2]);
$filteredStudents = getLoginDetailReport([
    'class_id' => [1, 2],
    'section_id' => [1, 2]
]);
?>
```

### Python
```python
import requests
import json

def get_login_detail_report(filters=None):
    url = 'http://localhost/amt/api/login-detail-report/filter'
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
all_students = get_login_detail_report()
class_students = get_login_detail_report({'class_id': 1})
section_students = get_login_detail_report({'section_id': 2})
filtered_students = get_login_detail_report({
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
curl -X POST "http://localhost/amt/api/login-detail-report/filter" ...
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
{"class_id": 1, "section_id": 2}

// ❌ Invalid (missing quotes)
{class_id: 1, section_id: 2}
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
   - Ensure tables exist: `students`, `student_session`, `classes`, `sections`, `sessions`, `users`
   - Check if data exists in these tables
   - Verify users table has records with role='student'

4. **Check Routes**
   - Verify routes are configured in `api/application/config/routes.php`

5. **Use Interactive Tester**
   - Open `login_detail_report_api_test.html` for visual debugging

---

## 🔒 Security

### Important Security Considerations

⚠️ **This API returns sensitive login credentials (usernames and passwords).**

### Security Best Practices:

1. **Use HTTPS in Production**
   - Never transmit credentials over HTTP
   - Enable SSL/TLS on your server

2. **Implement Access Control**
   - Restrict API access to authorized users only
   - Consider role-based access control (RBAC)

3. **Log All Access**
   - Monitor who accesses login credential data
   - Maintain audit trails for security compliance

4. **Consider Password Encryption**
   - Encrypt passwords in database
   - Use strong hashing algorithms (bcrypt, Argon2)

5. **Rate Limiting**
   - Implement rate limiting to prevent abuse
   - Limit requests per IP/user

6. **IP Whitelisting**
   - Consider restricting API access to specific IPs
   - Use firewall rules for additional security

7. **Regular Security Audits**
   - Review access logs regularly
   - Monitor for suspicious activity
   - Update security measures as needed

---

## 📞 Support

### Getting Help
1. Review the [Complete Documentation](LOGIN_DETAIL_REPORT_API_DOCUMENTATION.md)
2. Check the [Quick Reference](LOGIN_DETAIL_REPORT_API_QUICK_REFERENCE.md)
3. Use the [Interactive Tester](login_detail_report_api_test.html)
4. Review application logs
5. Check existing API implementations (Student Report, Guardian Report, Admission Report)

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
- **Admission Report API** - Admission information with year filtering
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
- [ ] HTTPS is enabled (production)
- [ ] Access control is implemented (production)

---

## 🎉 Success!

You're now ready to use the Login Detail Report API! Start with the interactive tester or try the quick start examples above.

For detailed information, refer to the [Complete API Documentation](LOGIN_DETAIL_REPORT_API_DOCUMENTATION.md).

**Remember:** Handle login credentials securely and follow security best practices!

Happy coding! 🚀

