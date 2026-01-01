# Student Hostel Details API Documentation

## Overview

The Student Hostel Details API provides endpoints to retrieve information about students assigned to hostels, rooms, and room types in the school management system.

**Status:** ✅ Fully Working

**Base URL:** `http://localhost/amt/api`

## Key Features

- ✅ **Student Hostel Information** - Complete details of students residing in hostels
- ✅ **Hostel & Room Details** - Hostel names, room numbers, and room types
- ✅ **Cost Information** - Cost per bed for each room
- ✅ **Multiple Filters** - Filter by class, section, hostel, room type, or room number
- ✅ **Graceful Null Handling** - Empty requests return all hostel students
- ✅ **Comprehensive Summary** - Total students, hostels, rooms, and costs

## Authentication

Required headers:
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

## Endpoints

### 1. List Endpoint

**URL:** `POST /api/student-hostel-details/list`

**Description:** Get filter options including classes, hostels, and room types.

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
    "hostels": [
      {
        "id": "1",
        "hostel_name": "Boys Hostel",
        "type": "Boys",
        "address": "123 Main Street",
        "intake": "100"
      },
      {
        "id": "2",
        "hostel_name": "Girls Hostel",
        "type": "Girls",
        "address": "456 Park Avenue",
        "intake": "80"
      }
    ],
    "room_types": [
      {
        "id": "1",
        "room_type": "Single",
        "description": "Single occupancy room"
      },
      {
        "id": "2",
        "room_type": "Double",
        "description": "Double occupancy room"
      }
    ]
  },
  "timestamp": "2025-10-09 12:34:56"
}
```

### 2. Filter Endpoint

**URL:** `POST /api/student-hostel-details/filter`

**Description:** Get student hostel details data.

**Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| class_id | integer | No | Filter by class ID |
| section_id | integer | No | Filter by section ID |
| hostel_id | integer | No | Filter by hostel ID |
| hostel_name | string | No | Filter by hostel name |
| room_type_id | integer | No | Filter by room type ID |
| room_no | string | No | Filter by room number |

**Request Examples:**

1. **Empty Request (All Hostel Students):**
```json
{}
```

2. **Filter by Class:**
```json
{
  "class_id": 5
}
```

3. **Filter by Hostel:**
```json
{
  "hostel_id": 1
}
```

4. **Filter by Hostel Name:**
```json
{
  "hostel_name": "Boys Hostel"
}
```

5. **Filter by Room Type:**
```json
{
  "room_type_id": 2
}
```

6. **Multiple Filters:**
```json
{
  "class_id": 5,
  "section_id": 2,
  "hostel_id": 1,
  "room_type_id": 2
}
```

**Response Example:**
```json
{
  "status": 1,
  "message": "Student hostel details retrieved successfully",
  "filters_applied": {
    "class_id": 5,
    "section_id": null,
    "hostel_id": 1,
    "hostel_name": null,
    "room_type_id": null,
    "room_no": null
  },
  "summary": {
    "total_students": 35,
    "total_hostels": 1,
    "total_rooms": 12,
    "total_hostel_cost": "52500.00"
  },
  "total_records": 35,
  "data": [
    {
      "id": "123",
      "firstname": "John",
      "middlename": "",
      "lastname": "Doe",
      "admission_no": "ADM001",
      "mobileno": "1234567890",
      "guardian_phone": "0987654321",
      "hostel_room_id": "5",
      "class": "Class 5",
      "section": "A",
      "room_id": "5",
      "room_no": "101",
      "no_of_bed": 2,
      "cost_per_bed": 1500.00,
      "room_title": "Standard Room",
      "room_description": "Standard double occupancy room",
      "hostel_id": "1",
      "hostel_name": "Boys Hostel",
      "hostel_type": "Boys",
      "hostel_address": "123 Main Street",
      "room_type_id": "2",
      "room_type": "Double",
      "room_type_description": "Double occupancy room",
      "student_name": "John Doe",
      "class_section": "Class 5 - A"
    },
    {
      "id": "124",
      "firstname": "Jane",
      "middlename": "M",
      "lastname": "Smith",
      "admission_no": "ADM002",
      "mobileno": "2345678901",
      "guardian_phone": "8765432109",
      "hostel_room_id": "5",
      "class": "Class 5",
      "section": "A",
      "room_id": "5",
      "room_no": "101",
      "no_of_bed": 2,
      "cost_per_bed": 1500.00,
      "room_title": "Standard Room",
      "room_description": "Standard double occupancy room",
      "hostel_id": "1",
      "hostel_name": "Boys Hostel",
      "hostel_type": "Boys",
      "hostel_address": "123 Main Street",
      "room_type_id": "2",
      "room_type": "Double",
      "room_type_description": "Double occupancy room",
      "student_name": "Jane M Smith",
      "class_section": "Class 5 - A"
    }
  ],
  "timestamp": "2025-10-09 12:34:56"
}
```

## Response Fields

### Summary Fields
- `total_students` - Total number of students in hostels
- `total_hostels` - Number of unique hostels
- `total_rooms` - Number of unique rooms
- `total_hostel_cost` - Sum of all hostel costs

### Data Fields (per student)
- `id` - Student ID
- `firstname` - Student first name
- `middlename` - Student middle name
- `lastname` - Student last name
- `admission_no` - Student admission number
- `mobileno` - Student mobile number
- `guardian_phone` - Guardian phone number
- `hostel_room_id` - Hostel room ID
- `class` - Class name
- `section` - Section name
- `room_id` - Room ID
- `room_no` - Room number
- `no_of_bed` - Number of beds in room (integer)
- `cost_per_bed` - Cost per bed (float)
- `room_title` - Room title
- `room_description` - Room description
- `hostel_id` - Hostel ID
- `hostel_name` - Hostel name
- `hostel_type` - Hostel type (Boys/Girls/Mixed)
- `hostel_address` - Hostel address
- `room_type_id` - Room type ID
- `room_type` - Room type name
- `room_type_description` - Room type description
- `student_name` - Formatted full name
- `class_section` - Formatted class and section

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
curl -X POST http://localhost/amt/api/student-hostel-details/filter \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"hostel_id": 1}'
```

### Postman Example
1. Method: POST
2. URL: `http://localhost/amt/api/student-hostel-details/filter`
3. Headers:
   - `Content-Type: application/json`
   - `Client-Service: smartschool`
   - `Auth-Key: schoolAdmin@`
4. Body (raw JSON):
```json
{
  "hostel_name": "Boys Hostel"
}
```

### PHP Example
```php
$url = 'http://localhost/amt/api/student-hostel-details/filter';
$headers = array(
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
);
$data = array(
    'class_id' => 5,
    'hostel_id' => 1
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
- `students` - Student master data
- `student_session` - Student session information
- `classes` - Class information
- `sections` - Section information
- `hostel_rooms` - Hostel room details
- `hostel` - Hostel information
- `room_types` - Room type information

### Query Logic
The API joins multiple tables to get comprehensive hostel information:
1. Students with active status
2. Current session data
3. Class and section details
4. Hostel room assignments
5. Hostel details
6. Room type information

## Notes

- Empty request body `{}` returns all students in hostels for the current session
- Only active students are included in results
- Results are ordered by class, section, and student name
- All filters are optional and can be combined
- Costs are returned as float values
- Number of beds is returned as integer

## Use Cases

1. **Hostel Management** - See all students in a specific hostel
2. **Room Occupancy** - Check students assigned to each room
3. **Class Hostel** - View hostel details for a specific class
4. **Room Type Analysis** - Identify students in different room types
5. **Cost Calculation** - Calculate total hostel costs
6. **Parent Communication** - Get contact details for hostel students
7. **Capacity Planning** - Monitor hostel and room occupancy

## Related APIs

- **Student Transport Details API** - Transport management
- **Inventory Stock Report API** - Inventory management
- **Add Item Report API** - Item additions
- **Issue Inventory Report API** - Item issues

## Support

For issues or questions, please contact the development team.

---

**Last Updated:** 2025-10-09  
**API Version:** 1.0  
**Status:** Production Ready

