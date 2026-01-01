# Student Admission API Documentation

## Overview

The Student Admission API provides RESTful endpoints for managing student admissions and records. This API mirrors the functionality of the web-based student admission form at `http://localhost/amt/student/create`.

**Base URL:** `http://localhost/amt/api/`

**Controller:** `Student_admission_api.php`

---

## Endpoints

### 1. Create Student Admission

Creates a new student admission with all related data including student session, parent account, and optional fee assignments.

**Endpoint:** `POST /student_admission_api/create`

**Content-Type:** `application/json`

#### Required Fields

| Field | Type | Description | Validation |
|-------|------|-------------|------------|
| `firstname` | string | Student's first name | Required, XSS clean |
| `gender` | string | Student's gender | Required, Values: Male/Female |
| `dob` | string | Date of birth | Required, Format: YYYY-MM-DD or DD-MM-YYYY |
| `class_id` | integer | Class ID | Required |
| `section_id` | integer | Section ID | Required |
| `reference_id` | integer | Reference staff ID | Required |

#### Conditional Required Fields

These fields are required based on school settings:

| Field | Type | Description | Condition |
|-------|------|-------------|-----------|
| `guardian_name` | string | Guardian's name | If `guardian_name` setting is enabled |
| `guardian_is` | string | Guardian relationship | If `guardian_name` setting is enabled |
| `guardian_phone` | string | Guardian's phone | If `guardian_phone` setting is enabled |
| `lastname` | string | Student's last name | If `lastname` setting is enabled |
| `mobileno` | string | Student's mobile number | If `mobile_no` setting is enabled, Must be 10 digits starting with 6-9 |
| `admission_no` | string | Admission number | If `adm_auto_insert` is disabled, Must be unique |
| `roll_no` | string | Roll number | If `roll_no` is enabled and `sroll_auto_insert` is disabled |

#### Optional Fields

| Field | Type | Description |
|-------|------|-------------|
| `middlename` | string | Student's middle name |
| `email` | string | Student's email (must be unique) |
| `admi_no` | string | Custom admission number (must be unique) |
| `rte` | string | RTE status |
| `state` | string | State |
| `city` | string | City |
| `pincode` | string | Pincode |
| `cast` | string | Caste |
| `religion` | string | Religion |
| `blood_group` | string | Blood group (O+, A+, B+, AB+, O-, A-, B-, AB-) |
| `house` | integer | School house ID |
| `category_id` | integer | Category ID |
| `current_address` | string | Current address |
| `permanent_address` | string | Permanent address |
| `previous_school` | string | Previous school name |
| `admission_date` | string | Admission date (Format: YYYY-MM-DD or DD-MM-YYYY) |
| `height` | string | Height |
| `weight` | string | Weight |
| `measure_date` | string | Measurement date |
| `father_name` | string | Father's name |
| `father_phone` | string | Father's phone |
| `father_occupation` | string | Father's occupation |
| `mother_name` | string | Mother's name |
| `mother_phone` | string | Mother's phone |
| `mother_occupation` | string | Mother's occupation |
| `guardian_relation` | string | Guardian relation |
| `guardian_email` | string | Guardian email (must be unique if no sibling) |
| `guardian_address` | string | Guardian address |
| `guardian_occupation` | string | Guardian occupation |
| `adhar_no` | string | Aadhaar number |
| `samagra_id` | string | Samagra ID |
| `bank_account_no` | string | Bank account number |
| `bank_name` | string | Bank name |
| `ifsc_code` | string | IFSC code |
| `note` | string | Additional notes |
| `sibling_id` | integer | Sibling student ID (if exists) |
| `fees_discount` | float | Fee discount amount |
| `fee_session_group_id` | integer | Fee session group ID |
| `hostel_room_id` | integer | Hostel room ID |
| `vehroute_id` | integer | Vehicle route ID |
| `route_pickup_point_id` | integer | Route pickup point ID |
| `transport_feemaster_id` | array | Array of transport fee master IDs |

#### Request Example

```json
{
  "firstname": "John",
  "lastname": "Doe",
  "middlename": "Michael",
  "gender": "Male",
  "dob": "2010-01-15",
  "class_id": 1,
  "section_id": 1,
  "reference_id": 5,
  "guardian_name": "Jane Doe",
  "guardian_is": "Mother",
  "guardian_phone": "9876543210",
  "guardian_email": "jane.doe@example.com",
  "guardian_relation": "Mother",
  "email": "john.doe@example.com",
  "mobileno": "9123456789",
  "blood_group": "O+",
  "religion": "Hindu",
  "cast": "General",
  "category_id": 1,
  "current_address": "123 Main Street, City",
  "permanent_address": "123 Main Street, City",
  "father_name": "Robert Doe",
  "father_phone": "9876543211",
  "father_occupation": "Engineer",
  "mother_name": "Jane Doe",
  "mother_phone": "9876543210",
  "mother_occupation": "Teacher",
  "admission_date": "2024-04-01",
  "admi_no": "ADM2024001",
  "note": "New admission for academic year 2024-25"
}
```

#### Success Response

**Status Code:** `201 Created`

```json
{
  "status": 1,
  "message": "Student created successfully",
  "data": {
    "student_id": 123,
    "admission_no": "ADM001",
    "roll_no": "R001",
    "student_session_id": 456,
    "student_username": "std123",
    "student_password": "abc123",
    "parent_username": "par123",
    "parent_password": "xyz789",
    "firstname": "John",
    "lastname": "Doe",
    "gender": "Male",
    "class_id": 1,
    "section_id": 1
  },
  "timestamp": "2024-10-04 10:30:45"
}
```

#### Error Responses

**Validation Error - Status Code:** `400 Bad Request`

```json
{
  "status": 0,
  "message": "Validation failed",
  "errors": {
    "firstname": "First Name is required",
    "email": "Email already exists",
    "mobileno": "Mobile Number must be exactly 10 characters"
  },
  "timestamp": "2024-10-04 10:30:45"
}
```

**Duplicate Admission Number - Status Code:** `400 Bad Request`

```json
{
  "status": 0,
  "message": "Admission number ADM001 already exists",
  "timestamp": "2024-10-04 10:30:45"
}
```

**Server Error - Status Code:** `500 Internal Server Error`

```json
{
  "status": 0,
  "message": "Exception occurred while creating student",
  "error": {
    "type": "Exception",
    "message": "Database connection failed",
    "file": "Student_webservice.php",
    "line": 123
  },
  "timestamp": "2024-10-04 10:30:45"
}
```

---

## Database Tables Affected

The student creation API interacts with the following database tables:

1. **students** - Main student information
2. **student_session** - Student's class/section assignment for current session
3. **student_reference** - Reference staff information
4. **student_admi** - Admission number tracking
5. **users** - Student and parent login credentials
6. **student_fee_master** - Fee assignments (if fee_session_group_id provided)
7. **student_transport_fees** - Transport fee assignments (if transport details provided)

---

## Business Logic

### Admission Number Generation

- If `adm_auto_insert` is enabled in school settings:
  - System automatically generates admission number using prefix and counter
  - Format: `{prefix}{counter}` (e.g., ADM001, ADM002)
  - Counter increments based on last student's admission number
- If `adm_auto_insert` is disabled:
  - Admission number must be provided in request
  - System validates uniqueness

### Roll Number Generation

- If `roll_no` setting is enabled and `sroll_auto_insert` is enabled:
  - System automatically generates roll number per class/section
  - Format: `{prefix}{counter}` (e.g., R001, R002)
  - Counter is class/section specific
- If auto-insert is disabled:
  - Roll number must be provided in request
  - System validates uniqueness within class/section

### Parent Account Creation

- If `sibling_id` is provided:
  - Student is linked to existing parent account
  - No new parent credentials generated
- If `sibling_id` is not provided:
  - New parent account is created
  - Parent username: `par{student_id}`
  - Random 6-character password generated

### Student Login Creation

- Student username: `std{student_id}`
- Random 6-character password generated
- Default language set from school settings

---

## Testing the API

### Using cURL

```bash
curl -X POST http://localhost/amt/api/student_admission_api/create \
  -H "Content-Type: application/json" \
  -d '{
    "firstname": "John",
    "lastname": "Doe",
    "gender": "Male",
    "dob": "2010-01-15",
    "class_id": 1,
    "section_id": 1,
    "reference_id": 5,
    "guardian_name": "Jane Doe",
    "guardian_phone": "9876543210",
    "guardian_email": "jane@example.com",
    "email": "john@example.com",
    "mobileno": "9123456789"
  }'
```

### Using Postman

1. Set method to `POST`
2. URL: `http://localhost/amt/api/student_admission_api/create`
3. Headers: `Content-Type: application/json`
4. Body: Select `raw` and `JSON`, paste the request JSON
5. Click `Send`

---

## Notes

1. **Date Format**: The API accepts dates in both `YYYY-MM-DD` and `DD-MM-YYYY` formats. Internally, dates are converted to `YYYY-MM-DD` for database storage.

2. **Mobile Number Validation**: If mobile number validation is enabled, it must be exactly 10 digits and start with 6, 7, 8, or 9.

3. **Email Uniqueness**: Both student email and guardian email must be unique across all students.

4. **Transaction Safety**: The entire student creation process is wrapped in a database transaction. If any step fails, all changes are rolled back.

5. **Default Images**: If no student image is uploaded, a default image is assigned based on gender (default_male.jpg or default_female.jpg).

6. **School Settings**: Many validation rules and required fields depend on school settings configured in the admin panel.

---

## Error Handling

The API implements comprehensive error handling:

- **Validation Errors**: Returns detailed field-level validation errors
- **Database Errors**: Catches and logs database connection/query errors
- **Transaction Errors**: Rolls back all changes if any operation fails
- **Exception Handling**: Catches and logs all exceptions with detailed error information

All errors are logged to the CodeIgniter error log for debugging purposes.

