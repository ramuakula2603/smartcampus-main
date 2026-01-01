# Parent Login Detail Report API - README

## 📋 Overview

The **Parent Login Detail Report API** provides flexible endpoints for retrieving parent login credential information with advanced filtering capabilities. It's designed to handle null/empty parameters gracefully, making it easy to use for various client applications.

### Key Highlights
- ✅ **Graceful null/empty handling** - Returns all records when no filters provided
- ✅ **Multi-select support** - Filter by multiple classes or sections
- ✅ **Class & Section filtering** - Filter students by class and/or section
- ✅ **Session-aware** - Automatically uses current session or accepts custom session
- ✅ **RESTful design** - Follows REST API best practices
- ✅ **Parent login credentials** - Includes parent username and password for each student
- ✅ **Consistent patterns** - Matches existing API structure

---

## 🚀 Quick Start

### 1. Basic Request (All Students)
```bash
curl -X POST "http://localhost/amt/api/parent-login-detail-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### 2. Filter by Class
```bash
curl -X POST "http://localhost/amt/api/parent-login-detail-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": 1}'
```

### 3. Filter by Section
```bash
curl -X POST "http://localhost/amt/api/parent-login-detail-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"section_id": 2}'
```

### 4. Multiple Filters
```bash
curl -X POST "http://localhost/amt/api/parent-login-detail-report/filter" \
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
- Contact info (mobile, email, guardian info)
- **Parent login credentials (username, password)**

---

## 📚 API Endpoints

### 1. Filter Parent Login Detail Report
**URL:** `POST /api/parent-login-detail-report/filter`

**Parameters (all optional):**
- `class_id` - integer or array
- `section_id` - integer or array
- `session_id` - integer

### 2. List All Parent Login Details
**URL:** `POST /api/parent-login-detail-report/list`

**Parameters:** None (returns all active students)

---

## 🧪 Testing

### Interactive HTML Tester
Open `parent_login_detail_report_api_test.html` in your browser for an interactive testing interface.

### Manual Testing with cURL

**Test 1: All Students**
```bash
curl -X POST "http://localhost/amt/api/parent-login-detail-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Test 2: Filter by Multiple Classes**
```bash
curl -X POST "http://localhost/amt/api/parent-login-detail-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": [1, 2, 3]}'
```

---

## 💡 Code Examples

### JavaScript
```javascript
async function getParentLoginDetailReport(filters = {}) {
  const response = await fetch('http://localhost/amt/api/parent-login-detail-report/filter', {
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
const data = await getParentLoginDetailReport({ class_id: 1, section_id: 2 });
```

### PHP
```php
function getParentLoginDetailReport($filters = []) {
    $ch = curl_init('http://localhost/amt/api/parent-login-detail-report/filter');
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
$data = getParentLoginDetailReport(['class_id' => 1, 'section_id' => 2]);
```

---

## 🔒 Security

### Important Security Considerations

⚠️ **This API returns sensitive parent login credentials (usernames and passwords).**

### Security Best Practices:

1. **Use HTTPS in Production**
   - Never transmit credentials over HTTP
   - Enable SSL/TLS on your server

2. **Implement Access Control**
   - Restrict API access to authorized users only
   - Consider role-based access control (RBAC)

3. **Log All Access**
   - Monitor who accesses parent credential data
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
- Check if students exist in the database
- Verify parent_id is set for students
- Check users table has records with role='parent'
- Review application logs

#### 4. 500 Internal Server Error
**Solution:**
- Check application logs at `api/application/logs/`
- Verify database connection
- Ensure all required models are loaded

---

## 📞 Support

### Getting Help
1. Review the Complete Documentation
2. Check the Quick Reference
3. Use the Interactive Tester
4. Review application logs
5. Check existing API implementations

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
- **Login Detail Report API** (Student credentials)
- **Disable Reason API**
- **Fee Master API**

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
- [ ] HTTPS is enabled (production)
- [ ] Access control is implemented (production)

---

## 🎉 Success!

You're now ready to use the Parent Login Detail Report API!

**Remember:** Handle parent login credentials securely and follow security best practices!

Happy coding! 🚀

