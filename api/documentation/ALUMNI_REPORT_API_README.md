# Alumni Report API Documentation

## Overview

The Alumni Report API provides endpoints to retrieve information about students who have passed out from the school (alumni students). This API allows filtering by class, section, pass-out session, category, and admission number.

**Status:** ✅ Fully Working

**Base URL:** `http://localhost/amt/api`

## Key Features

- ✅ **Alumni Student Information** - Complete details of students who have passed out
- ✅ **Pass-Out Session Tracking** - Track students by their pass-out year/session
- ✅ **Current Contact Information** - Alumni's current email, phone, occupation, and address
- ✅ **Multiple Filters** - Filter by class, section, session, category, or admission number
- ✅ **Graceful Null Handling** - Empty requests return all alumni students
- ✅ **Comprehensive Summary** - Total alumni, session distribution, and statistics

## Authentication

Required headers:
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

## Endpoints

### 1. List Endpoint

**URL:** `POST /api/alumni-report/list`

**Description:** Get filter options including classes, sessions (pass-out years), and categories.

**Request Body:** `{}`

**Response Example:**
```json
{
  "status": 1,
  "message": "Filter options retrieved successfully",
  "data": {
    "classes": [
      {
        "id": "1",
        "class": "Class 1"
      },
      {
        "id": "2",
        "class": "Class 2"
      }
    ],
    "sessions": [
      {
        "id": "25",
        "session": "2023-24"
      },
      {
        "id": "24",
        "session": "2022-23"
      }
    ],
    "categories": [
      {
        "id": "1",
        "category": "General"
      },
      {
        "id": "2",
        "category": "OBC"
      }
    ]
  },
  "timestamp": "2025-10-09 12:34:56"
}
```

### 2. Filter Endpoint

**URL:** `POST /api/alumni-report/filter`

**Description:** Get alumni student data.

**Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| class_id | integer | No | Filter by class ID |
| section_id | integer | No | Filter by section ID |
| session_id | integer | No | Filter by pass-out session ID |
| category_id | integer | No | Filter by category ID |
| admission_no | string | No | Search by admission number (partial match) |

**Request Examples:**

1. **Empty Request (All Alumni Students):**
```json
{}
```

2. **Filter by Pass-Out Session:**
```json
{
  "session_id": 25
}
```

3. **Filter by Class:**
```json
{
  "class_id": 12
}
```

4. **Filter by Class and Session:**
```json
{
  "class_id": 12,
  "session_id": 25
}
```

5. **Search by Admission Number:**
```json
{
  "admission_no": "ADM"
}
```

6. **Multiple Filters:**
```json
{
  "class_id": 12,
  "section_id": 2,
  "session_id": 25,
  "category_id": 1
}
```

**Response Example:**
```json
{
  "status": 1,
  "message": "Alumni report data retrieved successfully",
  "filters_applied": {
    "class_id": 12,
    "section_id": null,
    "session_id": 25,
    "category_id": null,
    "admission_no": null
  },
  "summary": {
    "total_alumni": 150,
    "total_classes": 1,
    "total_sessions": 1,
    "session_distribution": {
      "2023-24": 150
    }
  },
  "total_records": 150,
  "data": [
    {
      "id": "123",
      "admission_no": "ADM001",
      "roll_no": "1",
      "firstname": "John",
      "middlename": "",
      "lastname": "Doe",
      "father_name": "Robert Doe",
      "guardian_name": "Robert Doe",
      "guardian_phone": "1234567890",
      "mobileno": "9876543210",
      "email": "john.doe@school.com",
      "dob": "2005-05-15",
      "gender": "Male",
      "current_address": "123 Main Street",
      "permanent_address": "123 Main Street",
      "city": "New York",
      "state": "NY",
      "pincode": "10001",
      "religion": "Christian",
      "admission_date": "2018-04-01",
      "class_id": "12",
      "class": "Class 12",
      "section_id": "2",
      "section": "A",
      "category_id": "1",
      "category": "General",
      "session_id": "25",
      "session": "2023-24",
      "current_email": "john.doe@gmail.com",
      "current_phone": "9876543210",
      "occupation": "Software Engineer",
      "current_address_alumni": "456 Tech Park, Silicon Valley",
      "student_name": "John Doe",
      "class_section": "Class 12 - A",
      "pass_out_year": "2023-24"
    },
    {
      "id": "124",
      "admission_no": "ADM002",
      "roll_no": "2",
      "firstname": "Jane",
      "middlename": "M",
      "lastname": "Smith",
      "father_name": "Michael Smith",
      "guardian_name": "Michael Smith",
      "guardian_phone": "2345678901",
      "mobileno": "8765432109",
      "email": "jane.smith@school.com",
      "dob": "2005-08-20",
      "gender": "Female",
      "current_address": "789 Park Avenue",
      "permanent_address": "789 Park Avenue",
      "city": "Los Angeles",
      "state": "CA",
      "pincode": "90001",
      "religion": "Hindu",
      "admission_date": "2018-04-01",
      "class_id": "12",
      "class": "Class 12",
      "section_id": "2",
      "section": "A",
      "category_id": "2",
      "category": "OBC",
      "session_id": "25",
      "session": "2023-24",
      "current_email": "jane.smith@gmail.com",
      "current_phone": "8765432109",
      "occupation": "Doctor",
      "current_address_alumni": "123 Medical Center, LA",
      "student_name": "Jane M Smith",
      "class_section": "Class 12 - A",
      "pass_out_year": "2023-24"
    }
  ],
  "timestamp": "2025-10-09 12:34:56"
}
```

## Response Fields

### Summary Fields
- `total_alumni` - Total number of alumni students
- `total_classes` - Number of unique classes
- `total_sessions` - Number of unique pass-out sessions
- `session_distribution` - Alumni count grouped by pass-out session

### Data Fields (per alumni student)
- `id` - Student ID
- `admission_no` - Student admission number
- `roll_no` - Student roll number
- `firstname` - Student first name
- `middlename` - Student middle name
- `lastname` - Student last name
- `father_name` - Father's name
- `guardian_name` - Guardian's name
- `guardian_phone` - Guardian phone number
- `mobileno` - Student mobile number (at school)
- `email` - Student email (at school)
- `dob` - Date of birth
- `gender` - Gender (Male/Female)
- `current_address` - Current address (at school)
- `permanent_address` - Permanent address
- `city` - City
- `state` - State
- `pincode` - PIN code
- `religion` - Religion
- `admission_date` - Admission date
- `class_id` - Class ID
- `class` - Class name
- `section_id` - Section ID
- `section` - Section name
- `category_id` - Category ID
- `category` - Category name
- `session_id` - Pass-out session ID
- `session` - Pass-out session name
- `current_email` - Alumni's current email
- `current_phone` - Alumni's current phone
- `occupation` - Alumni's current occupation
- `current_address_alumni` - Alumni's current address
- `student_name` - Formatted full name
- `class_section` - Formatted class and section
- `pass_out_year` - Pass-out year/session

## Error Responses

### 401 Unauthorized
```json
{
  "status": 0,
  "message": "Unauthorized access"
}
```

### 500 Database Error
```json
{
  "status": 0,
  "message": "Database connection error. Please ensure MySQL is running in XAMPP.",
  "error": "Connection failed"
}
```

## Usage Examples

### cURL Example
```bash
curl -X POST http://localhost/amt/api/alumni-report/filter \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"session_id": 25}'
```

### Postman Example
1. Method: POST
2. URL: `http://localhost/amt/api/alumni-report/filter`
3. Headers:
   - `Content-Type: application/json`
   - `Client-Service: smartschool`
   - `Auth-Key: schoolAdmin@`
4. Body (raw JSON):
```json
{
  "class_id": 12,
  "session_id": 25
}
```

### PHP Example
```php
$url = 'http://localhost/amt/api/alumni-report/filter';
$headers = array(
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
);
$data = array(
    'class_id' => 12,
    'session_id' => 25
);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);
print_r($result);
```

## Technical Details

### Database Tables Used
- `alumni_students` - Alumni student records (current contact info, occupation)
- `students` - Student master data
- `student_session` - Student session information (includes is_alumni flag)
- `classes` - Class information
- `sections` - Section information
- `sessions` - Session/year information
- `categories` - Category information

### Query Logic
The API joins multiple tables to get comprehensive alumni information:
1. Alumni students with current contact information
2. Student master data
3. Student session data (with is_alumni = 1)
4. Class and section details
5. Pass-out session information
6. Category information

## Notes

- Empty request body `{}` returns all alumni students
- Only active students marked as alumni (is_alumni = 1) are included
- Results are ordered by admission number
- All filters are optional and can be combined
- Admission number search supports partial matching
- Session distribution shows alumni count by pass-out year

## Use Cases

1. **Alumni Directory** - Maintain a complete directory of all alumni
2. **Pass-Out Year Reports** - Generate reports by pass-out year
3. **Class Alumni** - View all alumni from a specific class
4. **Alumni Contact** - Get current contact information for alumni
5. **Occupation Analysis** - Track career paths of alumni
6. **Reunion Planning** - Identify alumni for specific sessions/classes
7. **Alumni Communication** - Send targeted communications to alumni groups

## Related APIs

- **Student Hostel Details API** - Hostel management
- **Student Transport Details API** - Transport management
- **Inventory Stock Report API** - Inventory management

## Support

For issues or questions, please contact the development team.

---

**Last Updated:** 2025-10-09  
**API Version:** 1.0  
**Status:** Production Ready

