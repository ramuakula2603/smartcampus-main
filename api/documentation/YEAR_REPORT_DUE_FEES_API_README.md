# Year Report Due Fees API Documentation

## Overview

The Year Report Due Fees API provides endpoints to retrieve information about students with pending/due fees for the entire year. This API is specifically designed for year-end reporting and uses December 31st of the current year as the cutoff date for determining due fees. It allows filtering by session, class, and section, and returns comprehensive data about students, their due fees, fee breakdowns, and transport fees.

**Key Difference from Regular Due Fees Report:**
- **Regular Due Fees Report:** Uses current date for due date comparison
- **Year Report Due Fees:** Uses December 31st of current year (end of year) for due date comparison

**Base URL:** `http://localhost/amt/api`

---

## Authentication

All endpoints require authentication headers:

```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

---

## Endpoints

### 1. Filter Year Report Due Fees

**Endpoint:** `POST /api/year-report-due-fees/filter`

**Description:** Retrieves students with due fees for the entire year (up to December 31st). Supports filtering by session, class, and section.

#### Request Body (All Parameters Optional)

```json
{
  "session_id": "21",
  "class_id": "1",
  "section_id": "2"
}
```

#### Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| session_id | string | No | Session ID to filter students by academic session |
| class_id | string | No | Class ID to filter students by class |
| section_id | string | No | Section ID to filter students by section |

**Important Notes:**
- Empty request `{}` returns all students with due fees across all sessions, classes, and sections
- All parameters are optional and can be used independently or in combination
- Empty strings are treated as null (graceful handling)
- Due date comparison uses December 31st of current year (`YYYY-12-31`)

#### Response Format

**Success Response (200 OK):**

```json
{
  "status": 1,
  "message": "Year report due fees retrieved successfully",
  "filters_applied": {
    "class_id": "1",
    "section_id": "2",
    "session_id": "21",
    "date": "2025-12-31"
  },
  "total_records": 2,
  "data": [
    {
      "admission_no": "STU001",
      "class_id": "1",
      "section_id": "2",
      "student_id": "123",
      "roll_no": "1",
      "admission_date": "2024-04-01",
      "firstname": "John",
      "middlename": "",
      "lastname": "Doe",
      "father_name": "Robert Doe",
      "image": "student.jpg",
      "mobileno": "1234567890",
      "email": "john@example.com",
      "state": "California",
      "city": "Los Angeles",
      "pincode": "90001",
      "class": "Class 1",
      "section": "A",
      "fee_groups_feetype_ids": ["1", "2", "3"],
      "fees_list": [
        {
          "id": "1",
          "fee_session_group_id": "1",
          "student_session_id": "456",
          "amount": "1000.00",
          "previous_amount": "1000.00",
          "is_system": "0",
          "student_fees_master_id": "789",
          "code": "TF",
          "type": "Monthly",
          "student_fees_deposite_id": "0",
          "amount_detail": "0",
          "fee_group_name": "Tuition Fee",
          "due_date": "2025-09-30",
          "fine_type": "percentage",
          "fine_percentage": "5.00",
          "fine_amount": "50.00"
        }
      ],
      "transport_fees": [
        {
          "id": "1",
          "student_transport_fee_id": "10",
          "amount": "500.00",
          "month": "April",
          "due_date": "2025-04-10"
        }
      ]
    }
  ],
  "timestamp": "2025-10-10 10:00:00"
}
```

**Error Response (401 Unauthorized):**

```json
{
  "status": 0,
  "message": "Unauthorized access"
}
```

**Error Response (400 Bad Request):**

```json
{
  "status": 0,
  "message": "Bad request. Only POST method allowed."
}
```

**Error Response (500 Internal Server Error):**

```json
{
  "status": 0,
  "message": "Internal server error",
  "error": "Error details here"
}
```

---

### 2. List Filter Options

**Endpoint:** `POST /api/year-report-due-fees/list`

**Description:** Retrieves available filter options (classes) for the year report due fees.

#### Request Body

```json
{}
```

No parameters required.

#### Response Format

**Success Response (200 OK):**

```json
{
  "status": 1,
  "message": "Year report due fees filter options retrieved successfully",
  "classes": [
    {
      "id": "1",
      "class": "Class 1",
      "sections": "A,B,C"
    },
    {
      "id": "2",
      "class": "Class 2",
      "sections": "A,B"
    }
  ],
  "note": "Use the filter endpoint with class_id, section_id, and session_id to get year report due fees",
  "timestamp": "2025-10-10 10:00:00"
}
```

---

## Usage Examples

### Example 1: Get All Students with Due Fees (No Filters)

```bash
curl -X POST "http://localhost/amt/api/year-report-due-fees/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Result:** Returns all students with due fees for the entire year across all sessions, classes, and sections.

---

### Example 2: Filter by Session Only

```bash
curl -X POST "http://localhost/amt/api/year-report-due-fees/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "session_id": "21"
  }'
```

**Result:** Returns students enrolled in session 21 with due fees for the entire year.

---

### Example 3: Filter by Session and Class

```bash
curl -X POST "http://localhost/amt/api/year-report-due-fees/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "session_id": "21",
    "class_id": "1"
  }'
```

**Result:** Returns students from session 21, class 1 with due fees for the entire year.

---

### Example 4: Filter by Session, Class, and Section

```bash
curl -X POST "http://localhost/amt/api/year-report-due-fees/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "session_id": "21",
    "class_id": "1",
    "section_id": "2"
  }'
```

**Result:** Returns students from session 21, class 1, section 2 with due fees for the entire year.

---

### Example 5: Get Filter Options

```bash
curl -X POST "http://localhost/amt/api/year-report-due-fees/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Result:** Returns available classes for filtering.

---

## Filter Behavior

### Session Filter (`session_id`)

- **When provided:** Returns only students enrolled in the specified session
- **When null/empty:** Returns students from all sessions
- **Example:** `"session_id": "21"` returns students enrolled in session 21

### Class Filter (`class_id`)

- **When provided:** Returns only students in the specified class
- **When null/empty:** Returns students from all classes
- **Example:** `"class_id": "1"` returns students in class 1
- **Can be combined with:** session_id, section_id

### Section Filter (`section_id`)

- **When provided:** Returns only students in the specified section
- **When null/empty:** Returns students from all sections
- **Example:** `"section_id": "2"` returns students in section 2
- **Can be combined with:** session_id, class_id

### Date Filter (Automatic)

- **Always uses:** December 31st of current year (`YYYY-12-31`)
- **Purpose:** Year-end reporting - shows all fees due by end of year
- **Comparison:** `fee_groups_feetype.due_date <= YYYY-12-31`
- **Cannot be changed:** Date is automatically set by the API

---

## Response Fields Explained

### Student Information Fields

| Field | Type | Description |
|-------|------|-------------|
| admission_no | string | Student's admission number |
| class_id | string | ID of the student's class |
| section_id | string | ID of the student's section |
| student_id | string | Unique student ID |
| roll_no | string | Student's roll number |
| admission_date | string | Date of admission (YYYY-MM-DD) |
| firstname | string | Student's first name |
| middlename | string | Student's middle name |
| lastname | string | Student's last name |
| father_name | string | Father's name |
| image | string | Student's photo filename |
| mobileno | string | Contact mobile number |
| email | string | Student's email address |
| state | string | State of residence |
| city | string | City of residence |
| pincode | string | Postal code |
| class | string | Class name (e.g., "Class 1") |
| section | string | Section name (e.g., "A") |

### Fee Information Fields

| Field | Type | Description |
|-------|------|-------------|
| fee_groups_feetype_ids | array | Array of fee type IDs with due amounts |
| fees_list | array | Detailed list of fee items |
| transport_fees | array | Transport fee details (if applicable) |

### Fee List Item Fields

| Field | Type | Description |
|-------|------|-------------|
| id | string | Fee groups feetype ID |
| fee_session_group_id | string | Fee session group ID |
| student_session_id | string | Student session ID |
| amount | string | Fee amount |
| previous_amount | string | Previous balance amount |
| is_system | string | System fee flag (0 or 1) |
| student_fees_master_id | string | Student fees master record ID |
| code | string | Fee type code |
| type | string | Fee type name |
| student_fees_deposite_id | string | Deposit record ID (0 if no payment) |
| amount_detail | string | JSON string of payment details |
| fee_group_name | string | Name of the fee group |
| due_date | string | Due date (YYYY-MM-DD) |
| fine_type | string | Type of fine (none/percentage/fixed) |
| fine_percentage | string | Fine percentage if applicable |
| fine_amount | string | Fine amount if applicable |

---

## Error Handling

### Common Errors and Solutions

#### 1. Unauthorized Access (401)

**Error:**
```json
{
  "status": 0,
  "message": "Unauthorized access"
}
```

**Cause:** Missing or incorrect authentication headers

**Solution:** Ensure you include the required headers:
```
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

---

#### 2. Bad Request (400)

**Error:**
```json
{
  "status": 0,
  "message": "Bad request. Only POST method allowed."
}
```

**Cause:** Using wrong HTTP method (GET, PUT, DELETE instead of POST)

**Solution:** Use POST method for all API calls

---

#### 3. Internal Server Error (500)

**Error:**
```json
{
  "status": 0,
  "message": "Internal server error",
  "error": "Error details"
}
```

**Cause:** Server-side error (database connection, query error, etc.)

**Solution:** 
- Check server logs at `application/logs/log-{date}.php`
- Verify database connection
- Ensure all required models are loaded
- Contact system administrator if issue persists

---

#### 4. No Records Returned

**Response:**
```json
{
  "status": 1,
  "message": "Year report due fees retrieved successfully",
  "total_records": 0,
  "data": []
}
```

**Possible Causes:**
1. No students have due fees for the specified filters
2. All fees are paid or not yet due by December 31st
3. No students enrolled in the specified session/class/section
4. Incorrect filter values (non-existent session_id, class_id, or section_id)

**Solution:**
- Verify students exist in the specified session/class/section
- Check that fees have due dates on or before December 31st
- Try with empty filters `{}` to see all students with due fees
- Verify filter IDs are correct

---

## Testing Instructions

### Step 1: Test Authentication

```bash
# Test with missing headers (should fail)
curl -X POST "http://localhost/amt/api/year-report-due-fees/filter" \
  -H "Content-Type: application/json" \
  -d '{}'
```

**Expected:** 401 Unauthorized

---

### Step 2: Test Empty Filter

```bash
# Test with empty filter (should return all students with due fees)
curl -X POST "http://localhost/amt/api/year-report-due-fees/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Expected:** 200 OK with list of students

---

### Step 3: Test Session Filter

```bash
# Test with session filter
curl -X POST "http://localhost/amt/api/year-report-due-fees/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "session_id": "21"
  }'
```

**Expected:** 200 OK with students from session 21

---

### Step 4: Test Combined Filters

```bash
# Test with multiple filters
curl -X POST "http://localhost/amt/api/year-report-due-fees/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "session_id": "21",
    "class_id": "1",
    "section_id": "2"
  }'
```

**Expected:** 200 OK with students matching all filters

---

### Step 5: Test List Endpoint

```bash
# Test list endpoint
curl -X POST "http://localhost/amt/api/year-report-due-fees/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Expected:** 200 OK with list of classes

---

## Key Differences from Regular Due Fees Report

| Feature | Regular Due Fees Report | Year Report Due Fees |
|---------|------------------------|---------------------|
| **Endpoint** | `/api/due-fees-report/filter` | `/api/year-report-due-fees/filter` |
| **Due Date** | Current date (`date('Y-m-d')`) | December 31st (`date('Y-12-31')`) |
| **Purpose** | Current due fees | Year-end reporting |
| **Use Case** | Daily/monthly fee tracking | Annual fee analysis |
| **Date Filter** | Dynamic (today's date) | Fixed (end of year) |

---

## Best Practices

1. **Always include authentication headers** in every request
2. **Use empty filters first** to see all available data
3. **Apply filters progressively** (session → class → section)
4. **Check total_records** before processing data array
5. **Handle empty responses gracefully** in your application
6. **Log API responses** for debugging purposes
7. **Use the list endpoint** to get valid filter options
8. **Compare with web version** at `http://localhost/amt/financereports/yearreportduefees`

---

## Related APIs

- **Due Fees Report API:** `/api/due-fees-report/filter` - Current due fees
- **Due Fees Remark Report API:** `/api/due-fees-remark-report/filter` - Due fees with remarks
- **Fee Collection Report API:** `/api/collection-report/filter` - Fee collections
- **Total Fee Collection Report API:** `/api/total-fee-collection-report/filter` - Total collections

---

## Support

For issues or questions:
1. Check server logs at `application/logs/log-{date}.php`
2. Review this documentation
3. Compare API response with web version
4. Contact system administrator

---

**Last Updated:** 2025-10-10  
**API Version:** 1.0  
**Status:** Active

