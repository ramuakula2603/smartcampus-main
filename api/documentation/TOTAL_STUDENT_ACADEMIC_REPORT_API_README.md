# Total Student Academic Report API Documentation

## Overview
The Total Student Academic Report API provides endpoints to retrieve comprehensive academic fee reports for all students with filtering options by class, section, and session.

**Base URL:** `/api/total-student-academic-report`

**Authentication Required:** Yes
- Header: `Client-Service: smartschool`
- Header: `Auth-Key: schoolAdmin@`

**HTTP Method:** POST (for all endpoints)

---

## Endpoints

### 1. Filter Endpoint
**URL:** `/api/total-student-academic-report/filter`

**Purpose:** Retrieve total student academic fee report with optional filters

**Request Body Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| class_id | string/int | No | Class ID to filter by |
| section_id | string/int | No | Section ID to filter by |
| session_id | string/int | No | Academic session ID to filter by |

**Graceful Null/Empty Handling:**
- Empty request `{}` returns all students from all classes and sessions
- `null` or empty string parameters are treated as "return ALL records" for that parameter

**Example Requests:**

1. **Empty Request (All Students):**
```json
{}
```

2. **Filter by Class:**
```json
{
    "class_id": "1"
}
```

3. **Filter by Class and Section:**
```json
{
    "class_id": "1",
    "section_id": "1"
}
```

4. **Filter by Session:**
```json
{
    "session_id": "1"
}
```

5. **Combined Filters:**
```json
{
    "class_id": "1",
    "section_id": "1",
    "session_id": "1"
}
```

**Success Response (200 OK):**
```json
{
    "status": 1,
    "message": "Total student academic report retrieved successfully",
    "filters_applied": {
        "class_id": "1",
        "section_id": "1",
        "session_id": "1"
    },
    "total_records": 50,
    "data": [
        {
            "name": "John M Doe",
            "class": "Class 1",
            "section": "A",
            "admission_no": "ADM001",
            "roll_no": "001",
            "father_name": "Mr. Doe",
            "total_fee": "10000.00",
            "deposit": "7000.00",
            "discount": "500.00",
            "fine": "100.00",
            "balance": "2600.00"
        }
    ],
    "timestamp": "2025-10-08 14:30:00"
}
```

---

### 2. List Endpoint
**URL:** `/api/total-student-academic-report/list`

**Purpose:** Retrieve filter options

**Request Body:** Empty `{}` or no body required

**Success Response (200 OK):**
```json
{
    "status": 1,
    "message": "Filter options retrieved successfully",
    "data": {
        "classes": [
            {
                "id": "1",
                "class": "Class 1",
                "sections": [
                    {
                        "id": "1",
                        "section": "A"
                    }
                ]
            }
        ],
        "sessions": [
            {
                "id": "1",
                "session": "2024-2025"
            }
        ]
    },
    "timestamp": "2025-10-08 14:30:00"
}
```

---

## Implementation Details

### Controller
**File:** `api/application/controllers/Total_student_academic_report_api.php`

**Key Features:**
- Authentication check
- Graceful null/empty parameter handling
- Calculates total fees, deposits, discounts, fines, and balance for each student
- Includes transport fees if module is active

### Model Methods
**File:** `api/application/models/Student_model.php`

**Methods:**
1. `totalsearchByClassSectionWithSession($session_id, $class_id, $section_id)` - Get students by filters
2. `gettotalStudents($session_id)` - Get all students

**File:** `api/application/models/Studentfeemaster_model.php`

**Methods:**
1. `getTransStudentFees($student_session_id)` - Get student fees including transport fees

### Routes
**File:** `api/application/config/routes.php`

```php
$route['total-student-academic-report/filter']['POST'] = 'total_student_academic_report_api/filter';
$route['total-student-academic-report/list']['POST'] = 'total_student_academic_report_api/list';
```

---

## Usage Examples

### cURL Examples

1. **Get All Students:**
```bash
curl -X POST "http://localhost/amt/api/total-student-academic-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d "{}"
```

2. **Get Students from Specific Class:**
```bash
curl -X POST "http://localhost/amt/api/total-student-academic-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id":"1"}'
```

3. **Get Filter Options:**
```bash
curl -X POST "http://localhost/amt/api/total-student-academic-report/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d "{}"
```

---

## Notes

1. **Fee Calculation:**
   - Total Fee: Sum of all fee amounts
   - Deposit: Sum of all payments made
   - Discount: Sum of all discounts applied
   - Fine: Sum of all fines
   - Balance: (Total Fee - Discount) - Deposit + Fine

2. **Transport Fees:**
   - Included automatically if transport module is active
   - Merged with regular academic fees

3. **Performance:**
   - Returns all students if no filter provided
   - Consider using class/section filters for large datasets

---

## API Version
**Version:** 1.0.0  
**Last Updated:** October 8, 2025  
**Status:** Production Ready

