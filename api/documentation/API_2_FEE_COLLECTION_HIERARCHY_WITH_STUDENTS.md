# API 2: Fee Collection Hierarchy with Students - GET-HIERARCHY

## 📋 Overview

This API endpoint returns complete hierarchical academic data including all enrolled students. It provides a nested structure showing Sessions → Classes → Sections → Students with full student details.

---

## 🔗 Endpoint Details

**URL:** `POST /api/fee-collection-filters/get-hierarchy`  
**Full URL:** `http://localhost/amt/api/fee-collection-filters/get-hierarchy`  
**Method:** POST  
**Content-Type:** application/json

---

## 🔐 Authentication Headers

**Required Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

---

## 📥 Request Body

All parameters are **optional**. Empty request body `{}` returns all available data.

```json
{
  "session_id": 21,      // Optional - Filter by specific session
  "class_id": 19,        // Optional - Filter by specific class
  "section_id": 1        // Optional - Filter by specific section
}
```

### Request Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| session_id | integer | No | Filter by specific session |
| class_id | integer | No | Filter by specific class |
| section_id | integer | No | Filter by specific section |

---

## 📤 Response Structure

### Success Response (HTTP 200)

```json
{
  "status": 1,
  "message": "Hierarchical academic data retrieved successfully",
  "filters_applied": {
    "session_id": 21,
    "class_id": null,
    "section_id": null
  },
  "statistics": {
    "total_sessions": 1,
    "total_classes": 3,
    "total_sections": 8,
    "total_students": 150
  },
  "data": [
    {
      "id": 21,
      "name": "2024-2025",
      "classes": [
        {
          "id": 19,
          "name": "Class 1",
          "sections": [
            {
              "id": 1,
              "name": "Section A",
              "students": [
                {
                  "id": 101,
                  "admission_no": "STU001",
                  "roll_no": "1",
                  "full_name": "John Michael Doe",
                  "firstname": "John",
                  "middlename": "Michael",
                  "lastname": "Doe",
                  "father_name": "Robert Doe",
                  "dob": "2015-05-15",
                  "gender": "Male",
                  "mobileno": "1234567890",
                  "email": "john.doe@example.com",
                  "guardian_name": "Robert Doe",
                  "guardian_phone": "1234567890",
                  "is_active": "yes"
                },
                {
                  "id": 102,
                  "admission_no": "STU002",
                  "roll_no": "2",
                  "full_name": "Jane Mary Smith",
                  "firstname": "Jane",
                  "middlename": "Mary",
                  "lastname": "Smith",
                  "father_name": "William Smith",
                  "dob": "2015-06-20",
                  "gender": "Female",
                  "mobileno": "0987654321",
                  "email": "jane.smith@example.com",
                  "guardian_name": "William Smith",
                  "guardian_phone": "0987654321",
                  "is_active": "yes"
                }
              ]
            },
            {
              "id": 2,
              "name": "Section B",
              "students": [
                // More students...
              ]
            }
          ]
        }
      ]
    }
  ],
  "timestamp": "2025-10-10 14:30:00"
}
```

### Response Data Structure

#### Root Level
- `status` (integer) - 1 for success, 0 for error
- `message` (string) - Response message
- `filters_applied` (object) - Shows which filters were applied
- `statistics` (object) - Summary counts
- `data` (array) - Hierarchical academic data
- `timestamp` (string) - Response generation time

#### Filters Applied Object
- `session_id` (integer|null) - Applied session filter
- `class_id` (integer|null) - Applied class filter
- `section_id` (integer|null) - Applied section filter

#### Statistics Object
- `total_sessions` (integer) - Number of sessions in response
- `total_classes` (integer) - Total number of classes
- `total_sections` (integer) - Total number of sections
- `total_students` (integer) - Total number of students

#### Sessions (Hierarchical Array)
Each session contains:
- `id` (integer) - Session ID
- `name` (string) - Session name (e.g., "2024-2025")
- `classes` (array) - Nested array of classes

#### Classes (Nested in Sessions)
Each class contains:
- `id` (integer) - Class ID
- `name` (string) - Class name (e.g., "Class 1")
- `sections` (array) - Nested array of sections

#### Sections (Nested in Classes)
Each section contains:
- `id` (integer) - Section ID
- `name` (string) - Section name (e.g., "Section A")
- `students` (array) - Nested array of students

#### Students (Nested in Sections)
Each student contains:
- `id` (integer) - Student ID
- `admission_no` (string) - Admission number
- `roll_no` (string) - Roll number
- `full_name` (string) - Complete name (firstname + middlename + lastname)
- `firstname` (string) - First name
- `middlename` (string) - Middle name
- `lastname` (string) - Last name
- `father_name` (string) - Father's name
- `dob` (date) - Date of birth (YYYY-MM-DD)
- `gender` (string) - Gender (Male/Female)
- `mobileno` (string) - Mobile number
- `email` (string) - Email address
- `guardian_name` (string) - Guardian name
- `guardian_phone` (string) - Guardian phone number
- `is_active` (string) - Active status ("yes"/"no")

---

## 🧪 Testing Examples

### Example 1: Get All Data (No Filters)
```bash
curl -X POST "http://localhost/amt/api/fee-collection-filters/get-hierarchy" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 2: Get Students for Specific Session
```bash
curl -X POST "http://localhost/amt/api/fee-collection-filters/get-hierarchy" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "session_id": 21
  }'
```

### Example 3: Get Students for Specific Class
```bash
curl -X POST "http://localhost/amt/api/fee-collection-filters/get-hierarchy" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "session_id": 21,
    "class_id": 19
  }'
```

### Example 4: Get Students for Specific Section
```bash
curl -X POST "http://localhost/amt/api/fee-collection-filters/get-hierarchy" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "session_id": 21,
    "class_id": 19,
    "section_id": 1
  }'
```

---

## ❌ Error Responses

### 401 Unauthorized
**Cause:** Missing or invalid authentication headers

```json
{
  "status": 0,
  "message": "Unauthorized access. Invalid headers.",
  "data": null
}
```

### 405 Method Not Allowed
**Cause:** Using GET instead of POST

```json
{
  "status": 0,
  "message": "Method not allowed. Use POST method.",
  "data": null
}
```

### 500 Internal Server Error
**Cause:** Server-side error

```json
{
  "status": 0,
  "message": "Internal server error occurred",
  "data": null
}
```

---

## 🎯 Use Cases

1. **Student Management** - View complete academic structure with enrolled students
2. **Bulk Operations** - Perform operations on students by session/class/section
3. **Academic Reports** - Generate comprehensive reports with student details
4. **Data Export** - Export complete academic data with student information
5. **Dashboard Views** - Display hierarchical academic data with student counts
6. **Attendance Systems** - Get student lists for attendance marking
7. **Fee Collection** - Get student lists for fee collection by section
8. **Communication** - Send notifications to students in specific sections

---

## 📊 Filtering Behavior

| Filter Combination | Result |
|-------------------|--------|
| Empty `{}` | Returns all sessions with complete hierarchy and all students |
| `session_id` only | Returns that session with all its classes, sections, and students |
| `session_id` + `class_id` | Returns that class with all its sections and students |
| All three filters | Returns specific section with its students only |

---

## ⚡ Key Features

✅ **Complete Hierarchy** - Full nested structure with student data  
✅ **Student Details** - Comprehensive student information included  
✅ **Statistics** - Provides counts of sessions, classes, sections, students  
✅ **Flexible Filtering** - Filter at any level (session/class/section)  
✅ **Empty Body Support** - `{}` returns all data gracefully  
✅ **Active Students Only** - Only includes students with `is_active = 'yes'`  
✅ **Timestamp** - Includes response generation timestamp  

---

## 📝 Important Notes

1. **POST Method Only** - GET requests will return 405 error
2. **Headers Required** - Both `Client-Service` and `Auth-Key` headers are mandatory
3. **Empty Body Valid** - `{}` is a valid request that returns all data
4. **Complete Hierarchy** - Sessions → Classes → Sections → Students
5. **Active Students Only** - Only students with `is_active = 'yes'` are included
6. **Ordered Results** - Students ordered by admission number
7. **Full Student Data** - Includes all essential student information
8. **Performance** - May be slower with large datasets due to student data

---

## 🔄 Comparison with API 1

| Feature | API 1 (`/get`) | API 2 (`/get-hierarchy`) |
|---------|----------------|--------------------------|
| Student Data | ❌ Not included | ✅ Included with full details |
| Fee Groups | ✅ Included | ❌ Not included |
| Fee Types | ✅ Included | ❌ Not included |
| Staff Collectors | ✅ Included | ❌ Not included |
| Statistics | ❌ Not included | ✅ Included |
| Timestamp | ❌ Not included | ✅ Included |
| Performance | ⚡ Faster | 🐢 Slower (more data) |
| Use Case | Fee filters | Complete academic data |

---

## 💡 Performance Tips

1. **Use Filters** - Apply filters to reduce response size
2. **Cache Results** - Cache responses for frequently accessed data
3. **Specific Queries** - Use section_id filter for smallest dataset
4. **Off-Peak Hours** - Run large queries during off-peak hours
5. **Pagination** - Consider implementing pagination for very large datasets

---

## 📚 Related Documentation

- **API 1:** See `API_1_FEE_COLLECTION_FILTERS_GET.md` for filters without student data
- **Complete Guide:** See `FEE_COLLECTION_HIERARCHICAL_API_DOCUMENTATION.md`
- **Quick Reference:** See `FEE_COLLECTION_HIERARCHICAL_API_QUICK_REFERENCE.md`

---

**API Status:** ✅ Active  
**Last Updated:** October 10, 2025  
**Version:** 2.0 (Hierarchical Structure with Students)

