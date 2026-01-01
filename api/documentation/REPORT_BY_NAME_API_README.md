# Report By Name API Documentation

## 🎉 **UPDATED - October 8, 2025**
**The API now returns complete detailed fee information including:**
- ✅ Fee groups with fee types
- ✅ Payment history with dates and payment modes
- ✅ Student discount information
- ✅ Transport fees
- ✅ Due dates and fine amounts

**See:** `REPORT_BY_NAME_DETAILED_FEE_FIX.md` for complete details of the update.

---

## Overview
The Report By Name API provides endpoints to search students by name and retrieve their comprehensive fee reports with detailed fee structure, payment history, and filtering options.

**Base URL:** `/api/report-by-name`

**Authentication Required:** Yes
- Header: `Client-Service: smartschool`
- Header: `Auth-Key: schoolAdmin@`

**HTTP Method:** POST (for all endpoints)

---

## Endpoints

### 1. Filter Endpoint
**URL:** `/api/report-by-name/filter`

**Purpose:** Search students by name and retrieve their fee reports

**Request Body Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| search_text | string | No | Text to search in firstname, middlename, lastname, or admission_no |
| class_id | string/int | No | Class ID to filter by |
| section_id | string/int | No | Section ID to filter by |
| session_id | string/int | No | Academic session ID to filter by |

**Graceful Null/Empty Handling:**
- Empty request `{}` returns first 100 students (limited for performance)
- `null` or empty string parameters are treated as "return ALL records" for that parameter
- Search is case-insensitive and uses LIKE matching

**Example Requests:**

1. **Empty Request (First 100 Students):**
```json
{}
```

2. **Search by Name:**
```json
{
    "search_text": "John"
}
```

3. **Search by Admission Number:**
```json
{
    "search_text": "ADM001"
}
```

4. **Search with Class Filter:**
```json
{
    "search_text": "John",
    "class_id": "1"
}
```

5. **Search with Multiple Filters:**
```json
{
    "search_text": "John",
    "class_id": "1",
    "section_id": "1",
    "session_id": "1"
}
```

6. **Filter by Class Only:**
```json
{
    "class_id": "1"
}
```

**Success Response (200 OK):**
```json
{
    "status": 1,
    "message": "Report by name retrieved successfully",
    "filters_applied": {
        "search_text": "John",
        "class_id": "1",
        "section_id": null,
        "session_id": null
    },
    "total_records": 5,
    "data": [
        {
            "student_id": "100",
            "admission_no": "ADM001",
            "firstname": "John",
            "middlename": "M",
            "lastname": "Doe",
            "full_name": "John M Doe",
            "class": "Class 1",
            "section": "A",
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
**URL:** `/api/report-by-name/list`

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
**File:** `api/application/controllers/Report_by_name_api.php`

**Key Features:**
- Authentication check
- Graceful null/empty parameter handling
- Case-insensitive name search
- Searches in firstname, middlename, lastname, and admission_no
- Calculates total fees, deposits, discounts, fines, and balance
- Limited to 100 records for performance when no filter provided

### Model Methods
**File:** `api/application/models/Student_model.php`

**Methods:**
1. `searchByName($search_text, $class_id, $section_id, $session_id)` - Search students by name
2. `searchByClassSection($class_id, $section_id, $session_id)` - Get students by class/section
3. `getAll($session_id)` - Get all students (limited to 100)

**File:** `api/application/models/Studentfeemaster_model.php`

**Methods:**
1. `getStudentFees($student_session_id)` - Get student fees

### Routes
**File:** `api/application/config/routes.php`

```php
$route['report-by-name/filter']['POST'] = 'report_by_name_api/filter';
$route['report-by-name/list']['POST'] = 'report_by_name_api/list';
```

---

## Usage Examples

### cURL Examples

1. **Search by Name:**
```bash
curl -X POST "http://localhost/amt/api/report-by-name/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"search_text":"John"}'
```

2. **Search by Admission Number:**
```bash
curl -X POST "http://localhost/amt/api/report-by-name/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"search_text":"ADM001"}'
```

3. **Search with Class Filter:**
```bash
curl -X POST "http://localhost/amt/api/report-by-name/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"search_text":"John","class_id":"1"}'
```

4. **Get All Students from Class:**
```bash
curl -X POST "http://localhost/amt/api/report-by-name/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id":"1"}'
```

5. **Get Filter Options:**
```bash
curl -X POST "http://localhost/amt/api/report-by-name/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d "{}"
```

---

## Notes

1. **Search Behavior:**
   - Searches in firstname, middlename, lastname, and admission_no fields
   - Uses LIKE matching (case-insensitive)
   - Partial matches are supported (e.g., "Joh" will match "John")

2. **Performance:**
   - Empty request limited to 100 records to prevent large result sets
   - Use filters to narrow down results for better performance
   - Consider adding pagination for large datasets

3. **Fee Calculation:**
   - Total Fee: Sum of all fee amounts
   - Deposit: Sum of all payments made
   - Discount: Sum of all discounts applied
   - Fine: Sum of all fines
   - Balance: (Total Fee - Discount) - Deposit + Fine

4. **Use Cases:**
   - Quick student lookup by name
   - Search by partial name
   - Find student by admission number
   - Get fee summary for searched students

---

## API Version
**Version:** 1.0.0  
**Last Updated:** October 8, 2025  
**Status:** Production Ready

