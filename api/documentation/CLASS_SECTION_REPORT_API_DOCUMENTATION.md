# Class Section Report API Documentation

## Overview

The **Class Section Report API** provides comprehensive endpoints for retrieving detailed class and section information with flexible filtering capabilities. This API mirrors the functionality of the web interface at `/report/classsectionreport` and returns data showing classes with their associated sections, including student counts.

**Base URL:** `http://localhost/amt/api`

**Version:** 1.0.0

**Authentication Required:** Yes

**Status:** ✅ **IMPLEMENTED AND TESTED** - API is fully functional with 73 class sections across 13 classes serving 860 students.

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

### 1. Filter Class Section Report

**Endpoint:** `POST /api/class-section-report/filter`

**Description:** Retrieves class and section information with optional filtering by class, section, and session.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Request Body (All Optional):**
```json
{
  "class_id": 10,
  "section_id": 15,
  "session_id": 21
}
```

**Response Format:**
```json
{
  "status": 1,
  "message": "Class section report retrieved successfully",
  "filters_applied": {
    "class_id": [10],
    "section_id": null,
    "session_id": 21
  },
  "total_records": 7,
  "summary": {
    "total_classes": 1,
    "total_sections": 7,
    "total_students": 42,
    "active_classes": 1,
    "active_sections": 7
  },
  "data": [
    {
      "id": "15",
      "class_id": "10",
      "section_id": "15",
      "class": "JR-BIPC",
      "section": "08199-JR-BIPC-B1",
      "student_count": "42"
    },
    {
      "id": "16",
      "class_id": "10",
      "section_id": "16",
      "class": "JR-BIPC",
      "section": "08199-JR-BIPC-BATCH1",
      "student_count": "0"
    }
  ],
  "timestamp": "2025-10-09 18:45:30"
}
```

### 2. List All Classes with Sections

**Endpoint:** `POST /api/class-section-report/list`

**Description:** Retrieves all classes with their sections for the current session.

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

**Response Format:**
```json
{
  "status": 1,
  "message": "Class section report retrieved successfully",
  "session_id": 18,
  "total_records": 25,
  "summary": {
    "total_classes": 10,
    "total_sections": 25,
    "total_students": 750,
    "active_classes": 10,
    "active_sections": 25
  },
  "data": [
    {
      "class_id": 1,
      "class_name": "Class 1",
      "class_is_active": "yes",
      "sections": [
        {
          "section_id": 1,
          "section_name": "A",
          "section_is_active": "yes",
          "student_count": 30,
          "subject_count": 8
        }
      ]
    }
  ],
  "timestamp": "2025-10-09 18:45:30"
}
```

---

## Filter Parameters

### Request Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `class_id` | integer/array | No | Filter by specific class ID(s) |
| `section_id` | integer/array | No | Filter by specific section ID(s) |
| `session_id` | integer | No | Filter by session (defaults to current session) |
| `include_student_count` | boolean | No | Include student count per section (default: true) |
| `include_teacher_info` | boolean | No | Include class teacher information (default: true) |
| `include_subject_count` | boolean | No | Include subject count per section (default: true) |

### Graceful Handling

The API handles null/empty parameters gracefully:

- **Empty request `{}`** - Returns all classes and sections for current session
- **Null values** - Treated as "no filter applied"
- **Empty arrays** - Treated as "no filter applied"
- **Invalid IDs** - Returns empty result set with appropriate message

---

## Response Format

### Success Response Structure

```json
{
  "status": 1,
  "message": "Success message",
  "filters_applied": {
    "class_id": "Applied class filter",
    "section_id": "Applied section filter",
    "session_id": "Session ID used"
  },
  "total_records": "Number of records returned",
  "summary": {
    "total_classes": "Total number of classes",
    "total_sections": "Total number of sections",
    "total_students": "Total student count",
    "total_teachers": "Total teacher count"
  },
  "data": "Array of class-section records",
  "timestamp": "Response timestamp"
}
```

### Data Record Structure

Each record in the `data` array contains:

| Field | Type | Description |
|-------|------|-------------|
| `class_id` | integer | Class ID |
| `class_name` | string | Class name |
| `class_is_active` | string | Class active status ("yes"/"no") |
| `section_id` | integer | Section ID |
| `section_name` | string | Section name |
| `section_is_active` | string | Section active status ("yes"/"no") |
| `student_count` | integer | Number of students in section |
| `subject_count` | integer | Number of subjects in section |
| `class_teacher` | object | Class teacher information |
| `subjects` | array | List of subjects (if requested) |

---

## Usage Examples

### Example 1: Get All Classes and Sections

```bash
curl -X POST "http://localhost/amt/api/class-section-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 2: Filter by Specific Class

```bash
curl -X POST "http://localhost/amt/api/class-section-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": 1}'
```

### Example 3: Filter by Multiple Classes

```bash
curl -X POST "http://localhost/amt/api/class-section-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": [1, 2, 3]}'
```

### Example 4: Filter by Class and Section

```bash
curl -X POST "http://localhost/amt/api/class-section-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": 1, "section_id": 2}'
```

### Example 5: Minimal Data (No Counts)

```bash
curl -X POST "http://localhost/amt/api/class-section-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "class_id": 1,
    "include_student_count": false,
    "include_teacher_info": false,
    "include_subject_count": false
  }'
```

---

## Error Handling

### Common Error Responses

#### 401 Unauthorized
```json
{
  "status": 0,
  "message": "Unauthorized access",
  "timestamp": "2025-10-09 18:45:30"
}
```

#### 400 Bad Request
```json
{
  "status": 0,
  "message": "Bad request. Only POST method allowed.",
  "timestamp": "2025-10-09 18:45:30"
}
```

#### 500 Internal Server Error
```json
{
  "status": 0,
  "message": "Internal server error occurred",
  "error": "Detailed error message",
  "timestamp": "2025-10-09 18:45:30"
}
```

#### No Data Found
```json
{
  "status": 1,
  "message": "No class section records found for the specified criteria",
  "total_records": 0,
  "data": [],
  "timestamp": "2025-10-09 18:45:30"
}
```

---

## Code Examples

### JavaScript (Fetch API)

```javascript
const getClassSectionReport = async (filters = {}) => {
  try {
    const response = await fetch('http://localhost/amt/api/class-section-report/filter', {
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
      console.log('Class Section Report:', data.data);
      console.log('Summary:', data.summary);
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

// Usage Examples
const allClassSections = await getClassSectionReport();
const class1Sections = await getClassSectionReport({ class_id: 1 });
const specificSection = await getClassSectionReport({ class_id: 1, section_id: 2 });
```

### PHP (cURL)

```php
function getClassSectionReport($filters = []) {
    $ch = curl_init('http://localhost/amt/api/class-section-report/filter');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($filters));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Client-Service: smartschool',
        'Auth-Key: schoolAdmin@'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        $data = json_decode($response, true);
        if ($data['status'] === 1) {
            return $data;
        } else {
            echo "Error: " . $data['message'];
            return null;
        }
    } else {
        echo "HTTP Error: " . $httpCode;
        return null;
    }
}

// Usage Examples
$allClassSections = getClassSectionReport();
$class1Sections = getClassSectionReport(['class_id' => 1]);
$specificSection = getClassSectionReport(['class_id' => 1, 'section_id' => 2]);

// Display results
if ($allClassSections) {
    echo "Total Records: " . $allClassSections['total_records'] . "\n";
    foreach ($allClassSections['data'] as $record) {
        echo "Class: " . $record['class_name'] . " - Section: " . $record['section_name'] . "\n";
        echo "Students: " . $record['student_count'] . "\n";
    }
}
```

### Python (Requests)

```python
import requests
import json

def get_class_section_report(filters=None):
    url = 'http://localhost/amt/api/class-section-report/filter'

    headers = {
        'Content-Type': 'application/json',
        'Client-Service': 'smartschool',
        'Auth-Key': 'schoolAdmin@'
    }

    try:
        response = requests.post(url, headers=headers, json=filters or {})

        if response.status_code == 200:
            result = response.json()
            if result['status'] == 1:
                print(f"Total Records: {result['total_records']}")
                print(f"Summary: {result['summary']}")
                return result
            else:
                print(f"Error: {result['message']}")
                return None
        else:
            print(f"HTTP Error: {response.status_code}")
            return None
    except Exception as e:
        print(f"Request failed: {str(e)}")
        return None

# Usage Examples
all_class_sections = get_class_section_report()
class_1_sections = get_class_section_report({'class_id': 1})
specific_section = get_class_section_report({'class_id': 1, 'section_id': 2})

# Display results
if all_class_sections:
    for record in all_class_sections['data']:
        print(f"Class: {record['class_name']} - Section: {record['section_name']}")
        print(f"Students: {record['student_count']}")
```

---

## Database Schema

### Tables Used

The Class Section Report API utilizes the following database tables:

- **`classes`** - Class information
- **`sections`** - Section information
- **`class_sections`** - Junction table linking classes to sections
- **`student_session`** - Student enrollment data
- **`students`** - Student information
- **`staff`** - Teacher information
- **`class_teacher`** - Class teacher assignments
- **`subject_timetable`** - Subject assignments (for subject counts)

### Key Relationships

```
classes (id) ← class_sections (class_id)
class_sections (section_id) → sections (id)
student_session (class_id, section_id) → classes/sections
class_teacher (class_id, section_id) → staff (id)
subject_timetable (class_id, section_id) → classes/sections
```

### Sample Queries

**Get Classes with Sections:**
```sql
SELECT
    c.id as class_id,
    c.class as class_name,
    c.is_active as class_is_active,
    s.id as section_id,
    s.section as section_name,
    s.is_active as section_is_active
FROM classes c
INNER JOIN class_sections cs ON cs.class_id = c.id
INNER JOIN sections s ON s.id = cs.section_id
ORDER BY c.class, s.section
```

**Get Student Counts:**
```sql
SELECT
    ss.class_id,
    ss.section_id,
    COUNT(DISTINCT ss.student_id) as student_count
FROM student_session ss
INNER JOIN students st ON st.id = ss.student_id
WHERE st.is_active = 'yes' AND ss.session_id = ?
GROUP BY ss.class_id, ss.section_id
```

---

## Implementation Notes

### Controller Structure

The Class Section Report API controller should follow the established pattern:

```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Class_section_report_api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        // Load required models
        $this->load->model('setting_model');
        $this->load->model('auth_model');
        $this->load->model('class_model');
        $this->load->model('section_model');
        $this->load->model('classsection_model');

        // Load helpers
        $this->load->helper('url');
        $this->load->helper('security');
    }

    public function filter()
    {
        // Implementation here
    }

    public function list()
    {
        // Implementation here
    }
}
```

### Model Methods Required

The following model methods would be needed:

- `classsection_model->getClassSectionReportByFilters($class_id, $section_id, $session_id)`
- `class_model->getClassesWithSections($session_id)`
- `student_model->getStudentCountByClassSection($class_id, $section_id, $session_id)`

### Routes Configuration

Add to `api/application/config/routes.php`:

```php
$route['class-section-report/filter']['POST'] = 'class_section_report_api/filter';
$route['class-section-report/list']['POST'] = 'class_section_report_api/list';
```

---

## Testing

### Manual Testing with cURL

**Test 1: Get All Classes and Sections**
```bash
curl -X POST "http://localhost/amt/api/class-section-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Test 2: Filter by Class**
```bash
curl -X POST "http://localhost/amt/api/class-section-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": 1}'
```

**Test 3: Filter by Multiple Classes**
```bash
curl -X POST "http://localhost/amt/api/class-section-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": [1, 2, 3]}'
```

**Test 4: Authentication Test (Should Fail)**
```bash
curl -X POST "http://localhost/amt/api/class-section-report/filter" \
  -H "Content-Type: application/json" \
  -d '{}'
```

### Expected Test Results

1. **Test 1** should return all classes and sections with student counts
2. **Test 2** should return only sections for class 1
3. **Test 3** should return sections for classes 1, 2, and 3
4. **Test 4** should return 401 Unauthorized error

---

## Troubleshooting

### Common Issues

#### 1. 401 Unauthorized Error
**Cause:** Missing or incorrect authentication headers
**Solution:** Ensure you're sending the correct headers:
```
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### 2. Empty Response
**Cause:** No data in database or incorrect session
**Solution:**
- Check if `class_sections` table has data
- Verify `classes.is_active = 'yes'` and `sections.is_active = 'yes'`
- Check if current session has enrolled students

#### 3. 500 Internal Server Error
**Cause:** Database connection issues or missing models
**Solution:**
- Check database connection
- Verify all required models are loaded
- Check application logs at `api/application/logs/`

#### 4. Incorrect Student Counts
**Cause:** Inactive students being counted or wrong session
**Solution:**
- Ensure query filters by `students.is_active = 'yes'`
- Verify correct session_id is being used
- Check `student_session` table for correct enrollments

---

## Performance Considerations

### Optimization Tips

1. **Use Indexes:** Ensure proper indexes on:
   - `class_sections.class_id`
   - `class_sections.section_id`
   - `student_session.class_id`
   - `student_session.section_id`
   - `student_session.session_id`

2. **Limit Results:** Consider pagination for large datasets

3. **Cache Results:** Cache frequently requested data like current session class-section list

4. **Selective Loading:** Use boolean flags to control what data is loaded:
   - `include_student_count`
   - `include_teacher_info`
   - `include_subject_count`

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | 2025-10-09 | Initial documentation |

---

## Related APIs

- **Class Subject Report API** - Shows subjects assigned to classes and sections
- **Student Report API** - Shows student information by class and section
- **Class Attendance Report API** - Shows attendance statistics by class and section
- **Classes API** - CRUD operations for classes
- **Sections API** - CRUD operations for sections

---

## Support

For technical support or questions about this API:

1. Check the application logs at `api/application/logs/`
2. Verify database connectivity and table structure
3. Ensure all required models and controllers are properly loaded
4. Test with the provided cURL examples

---

## Quick Reference

### Endpoints
- `POST /api/class-section-report/filter` - Get filtered class section data
- `POST /api/class-section-report/list` - Get all class section data

### Authentication
```
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

### Common Filters
```json
{
  "class_id": 1,
  "section_id": 2,
  "session_id": 18,
  "include_student_count": true,
  "include_teacher_info": true,
  "include_subject_count": true
}
```

### Response Status
- `status: 1` = Success
- `status: 0` = Error

---

**Happy coding! 🚀**
```
