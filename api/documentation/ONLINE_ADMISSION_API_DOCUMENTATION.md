# Online Admission API Documentation

## Overview

The Online Admission API provides comprehensive functionality for managing online student admission records. This API allows you to list, filter, and retrieve detailed information about online admissions submitted through the school's online admission portal.

---

## Base URL
```
http://{domain}/api/
```

## Important URL Structure

**For Online Admission APIs, use the controller/method pattern:**
- List admissions: `http://{domain}/api/online-admission/list`
- Get single admission: `http://{domain}/api/online-admission/get/{id}`
- Filter admissions: `http://{domain}/api/online-admission/filter`

**Examples:**
- List all: `http://localhost/amt/api/online-admission/list`
- Get admission: `http://localhost/amt/api/online-admission/get/123`
- Filter: `http://localhost/amt/api/online-admission/filter`

## Authentication Headers

All API requests require the following headers:
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

---

## Endpoints

### 1. List Online Admissions

**Endpoint:** `POST /online-admission/list`
**Full URL:** `http://localhost/amt/api/online-admission/list`

**Description:** Retrieve a list of all online admission records with optional basic filtering.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### Request Body (Optional Parameters)
```json
{
  "class_id": 19,        // Optional: Filter by class ID
  "section_id": 47,      // Optional: Filter by section ID
  "status": "1",         // Optional: Filter by enrollment status (0=not enrolled, 1=enrolled)
  "search": "john"       // Optional: Search in name, reference no, phone, email
}
```

#### Success Response (HTTP 200)
```json
{
  "status": 1,
  "message": "Online admissions retrieved successfully",
  "filters_applied": {
    "class_id": 19,
    "section_id": 47
  },
  "total_records": 25,
  "data": [
    {
      "id": 123,
      "reference_no": "REF2024001",
      "admission_no": "ADM2024001",
      "admission_date": "2024-01-15",
      "full_name": "John Doe Smith",
      "firstname": "John",
      "middlename": "Doe",
      "lastname": "Smith",
      "dob": "2010-05-15",
      "gender": "Male",
      "email": "john.smith@example.com",
      "mobileno": "9876543210",
      "father_name": "Robert Smith",
      "father_phone": "9876543211",
      "mother_name": "Mary Smith",
      "mother_phone": "9876543212",
      "guardian_name": "Robert Smith",
      "guardian_phone": "9876543211",
      "current_address": "123 Main Street, City",
      "permanent_address": "123 Main Street, City",
      "class_info": {
        "class_id": 19,
        "class_name": "Class 10",
        "section_id": 47,
        "section_name": "Section A"
      },
      "category": "General",
      "house_name": "Red House",
      "blood_group": "O+",
      "religion": "Hindu",
      "cast": "General",
      "is_enroll": "0",
      "form_status": "1",
      "paid_status": "1",
      "created_at": "2024-01-15 10:30:00",
      "updated_at": "2024-01-15 10:30:00"
    }
  ]
}
```

---

### 2. Get Single Online Admission

**Endpoint:** `POST /online-admission/get/{id}`
**Full URL:** `http://localhost/amt/api/online-admission/get/123`

**Description:** Retrieve detailed information for a specific online admission record.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### URL Parameters
- `id` (required): Online admission record ID

#### Request Body
```json
{}
```

#### Success Response (HTTP 200)
```json
{
  "status": 1,
  "message": "Online admission record retrieved successfully",
  "data": {
    "id": 123,
    "reference_no": "REF2024001",
    "admission_no": "ADM2024001",
    "roll_no": "001",
    "admission_date": "2024-01-15",
    "full_name": "John Doe Smith",
    "firstname": "John",
    "middlename": "Doe",
    "lastname": "Smith",
    "dob": "2010-05-15",
    "gender": "Male",
    "email": "john.smith@example.com",
    "mobileno": "9876543210",
    "blood_group": "O+",
    "religion": "Hindu",
    "cast": "General",
    "rte": "No",
    "current_address": "123 Main Street, City",
    "permanent_address": "123 Main Street, City",
    "previous_school": "ABC School",
    "father_info": {
      "name": "Robert Smith",
      "phone": "9876543211",
      "occupation": "Engineer",
      "pic": "father_pic.jpg"
    },
    "mother_info": {
      "name": "Mary Smith",
      "phone": "9876543212",
      "occupation": "Teacher",
      "pic": "mother_pic.jpg"
    },
    "guardian_info": {
      "is": "father",
      "name": "Robert Smith",
      "relation": "Father",
      "phone": "9876543211",
      "email": "robert@example.com",
      "occupation": "Engineer",
      "address": "123 Main Street, City",
      "pic": "guardian_pic.jpg"
    },
    "class_info": {
      "class_section_id": 25,
      "class_id": 19,
      "class_name": "Class 10",
      "section_id": 47,
      "section_name": "Section A"
    },
    "category": "General",
    "house_info": {
      "house_id": 5,
      "house_name": "Red House"
    },
    "hostel_info": {
      "hostel_id": 2,
      "hostel_name": "Boys Hostel",
      "room_id": 15,
      "room_no": "101",
      "room_type_id": 3,
      "room_type": "Double Sharing"
    },
    "transport_info": {
      "route_id": 8,
      "route_title": "Route A",
      "vehicle_id": 12,
      "vehicle_no": "TN01AB1234",
      "driver_name": "Driver Name",
      "driver_contact": "9876543213"
    },
    "financial_info": {
      "bank_account_no": "1234567890",
      "bank_name": "ABC Bank",
      "ifsc_code": "ABCD0123456"
    },
    "documents": {
      "document": "admission_doc.pdf",
      "adhar_no": "123456789012",
      "samagra_id": "123456789"
    },
    "physical_info": {
      "height": "150",
      "weight": "45",
      "measurement_date": "2024-01-15"
    },
    "status_info": {
      "is_enroll": "0",
      "form_status": "1",
      "paid_status": "1"
    },
    "timestamps": {
      "created_at": "2024-01-15 10:30:00",
      "updated_at": "2024-01-15 10:30:00"
    },
    "additional_info": {
      "state": "Tamil Nadu",
      "city": "Chennai",
      "pincode": "600001",
      "note": "Additional notes"
    }
  }
}
```

---

### 3. Filter Online Admissions

**Endpoint:** `POST /online-admission/filter`
**Full URL:** `http://localhost/amt/api/online-admission/filter`

**Description:** Advanced filtering of online admission records with multiple criteria.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### Request Body (All Parameters Optional)
```json
{
  "class_id": 19,              // Filter by class ID
  "section_id": 47,            // Filter by section ID
  "category_id": 5,            // Filter by category ID
  "gender": "Male",            // Filter by gender (Male/Female)
  "is_enroll": "1",            // Filter by enrollment status (0/1)
  "form_status": "1",          // Filter by form status
  "paid_status": "1",          // Filter by payment status
  "date_from": "2024-01-01",   // Filter from date (YYYY-MM-DD)
  "date_to": "2024-12-31",     // Filter to date (YYYY-MM-DD)
  "search": "john"             // Search in multiple fields
}
```

#### Success Response (HTTP 200)
```json
{
  "status": 1,
  "message": "Online admissions filtered successfully",
  "filters_applied": {
    "class_id": 19,
    "gender": "Male",
    "is_enroll": "1"
  },
  "total_records": 15,
  "data": [
    {
      "id": 123,
      "reference_no": "REF2024001",
      "admission_no": "ADM2024001",
      "admission_date": "2024-01-15",
      "full_name": "John Doe Smith",
      "firstname": "John",
      "middlename": "Doe",
      "lastname": "Smith",
      "dob": "2010-05-15",
      "gender": "Male",
      "email": "john.smith@example.com",
      "mobileno": "9876543210",
      "father_name": "Robert Smith",
      "mother_name": "Mary Smith",
      "class_info": {
        "class_id": 19,
        "class_name": "Class 10",
        "section_id": 47,
        "section_name": "Section A"
      },
      "category": "General",
      "house_name": "Red House",
      "blood_group": "O+",
      "is_enroll": "1",
      "form_status": "1",
      "paid_status": "1",
      "created_at": "2024-01-15 10:30:00"
    }
  ]
}
```

---

## Error Responses

### 400 Bad Request
```json
{
  "status": 0,
  "message": "Invalid or missing admission ID",
  "data": null
}
```

### 401 Unauthorized
```json
{
  "status": 0,
  "message": "Unauthorized access. Invalid headers.",
  "data": null
}
```

### 404 Not Found
```json
{
  "status": 0,
  "message": "Online admission record not found",
  "data": null
}
```

### 405 Method Not Allowed
```json
{
  "status": 0,
  "message": "Method not allowed. Use POST method.",
  "data": null
}
```

### 500 Internal Server Error
```json
{
  "status": 0,
  "message": "Internal server error occurred",
  "data": null
}
```

---

## Usage Examples

### Example 1: Get All Online Admissions
```bash
curl -X POST "http://localhost/amt/api/online-admission/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 2: Filter by Class and Gender
```bash
curl -X POST "http://localhost/amt/api/online-admission/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "class_id": 19,
    "gender": "Male",
    "is_enroll": "0"
  }'
```

### Example 3: Get Specific Admission
```bash
curl -X POST "http://localhost/amt/api/online-admission/get/123" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

## Database Tables Used

- `online_admissions` - Main online admission records
- `class_sections` - Class and section relationships
- `classes` - Class information
- `sections` - Section information
- `categories` - Student categories
- `school_houses` - School house information
- `hostel` - Hostel information
- `hostel_rooms` - Hostel room details
- `vehicle_routes` - Transport route information
- `vehicles` - Vehicle details
- `transport_route` - Route information

---

## Notes

1. All endpoints require POST method
2. Authentication headers are mandatory for all requests
3. Date filters use YYYY-MM-DD format
4. Search functionality works across multiple fields
5. Enrollment status: 0 = Not Enrolled, 1 = Enrolled
6. All responses include status, message, and data fields
7. Error responses follow consistent format

---

## Support

For API support and questions, contact the development team or refer to the main project documentation.
