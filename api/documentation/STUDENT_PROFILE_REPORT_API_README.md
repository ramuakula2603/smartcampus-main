# Student Profile Report API - README

## 📋 Overview

The **Student Profile Report API** provides flexible endpoints for retrieving comprehensive student profile information with advanced filtering capabilities. It returns extensive student data including personal details, academic information, hostel, transport, and login credentials.

### Key Highlights
- ✅ **Comprehensive data** - Returns 100+ fields per student
- ✅ **Graceful null/empty handling** - Returns all records when no filters provided
- ✅ **Multi-select support** - Filter by multiple classes or sections
- ✅ **Class & Section filtering** - Filter students by class and/or section
- ✅ **Session-aware** - Automatically uses current session or accepts custom session
- ✅ **RESTful design** - Follows REST API best practices
- ✅ **Consistent patterns** - Matches existing API structure

---

## 🚀 Quick Start

### 1. Basic Request (All Students)
```bash
curl -X POST "http://localhost/amt/api/student-profile-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### 2. Filter by Class
```bash
curl -X POST "http://localhost/amt/api/student-profile-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": 1}'
```

### 3. Filter by Section
```bash
curl -X POST "http://localhost/amt/api/student-profile-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"section_id": 2}'
```

### 4. Multiple Filters
```bash
curl -X POST "http://localhost/amt/api/student-profile-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": 1, "section_id": 2}'
```

---

## ✨ Features

### 1. Comprehensive Student Data
Each student profile includes:
- **Personal Info**: name, DOB, gender, blood group, religion, cast
- **Contact Info**: mobile, email, address (current & permanent)
- **Academic Info**: admission no, roll no, class, section, admission date
- **Family Info**: father, mother, guardian details with photos
- **Documents**: Adhar no, Samagra ID, bank details
- **Hostel Info**: hostel name, room no, room type
- **Transport Info**: vehicle no, route, driver details, transport fees
- **Physical Info**: height, weight, measurement date
- **School Info**: school house, category, RTE status
- **Login Info**: username, password
- **Status Info**: is_active, disable reason, disable note
- **Fees Info**: fees discount

### 2. Flexible Filtering
Filter students by:
- **Class** (single or multiple)
- **Section** (single or multiple)
- **Session** (defaults to current session)

### 3. Graceful Handling
The API intelligently handles:
- Empty request bodies `{}`
- Null values `{"class_id": null, "section_id": null}`
- Empty arrays `{"class_id": [], "section_id": []}`
- Mixed filters `{"class_id": 1, "section_id": null}`

All of these return all active students for the session.

### 4. Multi-Select Support
Pass single values or arrays:
```json
// Single values
{"class_id": 1, "section_id": 2}

// Multiple values
{"class_id": [1, 2, 3], "section_id": [1, 2]}
```

---

## 📚 API Endpoints

### 1. Filter Student Profile Report
**URL:** `POST /api/student-profile-report/filter`

**Parameters (all optional):**
- `class_id` - integer or array
- `section_id` - integer or array
- `session_id` - integer

### 2. List All Student Profiles
**URL:** `POST /api/student-profile-report/list`

**Parameters:** None (returns all active students)

---

## 🧪 Testing

### Interactive HTML Tester
Open `student_profile_report_api_test.html` in your browser for an interactive testing interface.

### Manual Testing with cURL

**Test 1: All Students**
```bash
curl -X POST "http://localhost/amt/api/student-profile-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Test 2: Filter by Multiple Classes**
```bash
curl -X POST "http://localhost/amt/api/student-profile-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": [1, 2, 3]}'
```

---

## 💡 Code Examples

### JavaScript
```javascript
async function getStudentProfileReport(filters = {}) {
  const response = await fetch('http://localhost/amt/api/student-profile-report/filter', {
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
const data = await getStudentProfileReport({ class_id: 1, section_id: 2 });
```

### PHP
```php
function getStudentProfileReport($filters = []) {
    $ch = curl_init('http://localhost/amt/api/student-profile-report/filter');
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
$data = getStudentProfileReport(['class_id' => 1, 'section_id' => 2]);
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
- Check if students exist in the database
- Verify students.is_active = 'yes'
- Check session_id is correct
- Review application logs

#### 4. 500 Internal Server Error
**Solution:**
- Check application logs at `api/application/logs/`
- Verify database connection
- Ensure all required models are loaded

---

## 📊 Response Fields

Each student profile includes 100+ fields organized by category:
- Personal information (15+ fields)
- Contact information (10+ fields)
- Academic information (10+ fields)
- Family information (15+ fields)
- Hostel information (5+ fields)
- Transport information (8+ fields)
- Physical information (3+ fields)
- School information (5+ fields)
- Login information (2 fields)
- Status information (5+ fields)
- Financial information (3+ fields)

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
- **Class Subject Report API**

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

---

## 🎉 Success!

You're now ready to use the Student Profile Report API!

Happy coding! 🚀

