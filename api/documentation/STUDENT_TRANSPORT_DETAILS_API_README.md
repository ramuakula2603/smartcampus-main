# Student Transport Details API Documentation

## Overview

The Student Transport Details API provides endpoints to retrieve information about students assigned to transport routes, vehicles, and pickup points in the school management system.

**Status:** ✅ Fully Working

**Base URL:** `http://localhost/amt/api`

## Key Features

- ✅ **Student Transport Information** - Complete details of students using transport
- ✅ **Route & Vehicle Details** - Route titles, vehicle numbers, and driver information
- ✅ **Pickup Point Information** - Pickup locations, times, and distances
- ✅ **Multiple Filters** - Filter by class, section, route, pickup point, or vehicle
- ✅ **Graceful Null Handling** - Empty requests return all transport students
- ✅ **Comprehensive Summary** - Total students, routes, vehicles, and fees

## Authentication

Required headers:
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

## Endpoints

### 1. List Endpoint

**URL:** `POST /api/student-transport-details/list`

**Description:** Get filter options including classes, routes, and vehicles.

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
    "routes": [
      {
        "id": "1",
        "route_title": "Route A - Downtown"
      },
      {
        "id": "2",
        "route_title": "Route B - Suburbs"
      }
    ],
    "vehicles": [
      {
        "id": "1",
        "vehicle_no": "BUS-001",
        "vehicle_model": "Tata LP 1512",
        "driver_name": "John Doe"
      },
      {
        "id": "2",
        "vehicle_no": "BUS-002",
        "vehicle_model": "Ashok Leyland",
        "driver_name": "Jane Smith"
      }
    ]
  },
  "timestamp": "2025-10-09 12:34:56"
}
```

### 2. Filter Endpoint

**URL:** `POST /api/student-transport-details/filter`

**Description:** Get student transport details data.

**Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| class_id | integer | No | Filter by class ID |
| section_id | integer | No | Filter by section ID |
| transport_route_id | integer | No | Filter by transport route ID |
| pickup_point_id | integer | No | Filter by pickup point ID |
| vehicle_id | integer | No | Filter by vehicle ID |

**Request Examples:**

1. **Empty Request (All Transport Students):**
```json
{}
```

2. **Filter by Class:**
```json
{
  "class_id": 5
}
```

3. **Filter by Route:**
```json
{
  "transport_route_id": 2
}
```

4. **Filter by Vehicle:**
```json
{
  "vehicle_id": 1
}
```

5. **Multiple Filters:**
```json
{
  "class_id": 5,
  "section_id": 2,
  "transport_route_id": 1
}
```

**Response Example:**
```json
{
  "status": 1,
  "message": "Student transport details retrieved successfully",
  "filters_applied": {
    "class_id": 5,
    "section_id": null,
    "transport_route_id": 1,
    "pickup_point_id": null,
    "vehicle_id": null
  },
  "summary": {
    "total_students": 45,
    "total_routes": 2,
    "total_vehicles": 3,
    "total_transport_fees": "67500.00"
  },
  "total_records": 45,
  "data": [
    {
      "id": "123",
      "firstname": "John",
      "middlename": "",
      "lastname": "Doe",
      "admission_no": "ADM001",
      "father_name": "Robert Doe",
      "mother_name": "Mary Doe",
      "father_phone": "1234567890",
      "mother_phone": "0987654321",
      "mobileno": "1122334455",
      "class": "Class 5",
      "section": "A",
      "route_pickup_point_id": "5",
      "pickup_name": "Main Street Stop",
      "route_title": "Route A - Downtown",
      "fees": 1500.00,
      "destination_distance": 12.5,
      "pickup_time": "07:30:00",
      "vehicle_id": "1",
      "vehicle_no": "BUS-001",
      "vehicle_model": "Tata LP 1512",
      "driver_name": "John Driver",
      "driver_contact": "9876543210",
      "student_name": "John Doe",
      "class_section": "Class 5 - A"
    },
    {
      "id": "124",
      "firstname": "Jane",
      "middlename": "M",
      "lastname": "Smith",
      "admission_no": "ADM002",
      "father_name": "Michael Smith",
      "mother_name": "Sarah Smith",
      "father_phone": "2345678901",
      "mother_phone": "8765432109",
      "mobileno": "2233445566",
      "class": "Class 5",
      "section": "A",
      "route_pickup_point_id": "6",
      "pickup_name": "Park Avenue Stop",
      "route_title": "Route A - Downtown",
      "fees": 1500.00,
      "destination_distance": 10.0,
      "pickup_time": "07:45:00",
      "vehicle_id": "1",
      "vehicle_no": "BUS-001",
      "vehicle_model": "Tata LP 1512",
      "driver_name": "John Driver",
      "driver_contact": "9876543210",
      "student_name": "Jane M Smith",
      "class_section": "Class 5 - A"
    }
  ],
  "timestamp": "2025-10-09 12:34:56"
}
```

## Response Fields

### Summary Fields
- `total_students` - Total number of students using transport
- `total_routes` - Number of unique routes
- `total_vehicles` - Number of unique vehicles
- `total_transport_fees` - Sum of all transport fees

### Data Fields (per student)
- `id` - Student ID
- `firstname` - Student first name
- `middlename` - Student middle name
- `lastname` - Student last name
- `admission_no` - Student admission number
- `father_name` - Father's name
- `mother_name` - Mother's name
- `father_phone` - Father's phone number
- `mother_phone` - Mother's phone number
- `mobileno` - Student mobile number
- `class` - Class name
- `section` - Section name
- `route_pickup_point_id` - Pickup point ID
- `pickup_name` - Pickup point name
- `route_title` - Transport route title
- `fees` - Transport fees (float)
- `destination_distance` - Distance in km (float)
- `pickup_time` - Pickup time (HH:MM:SS)
- `vehicle_id` - Vehicle ID
- `vehicle_no` - Vehicle number
- `vehicle_model` - Vehicle model
- `driver_name` - Driver name
- `driver_contact` - Driver contact number
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
curl -X POST http://localhost/amt/api/student-transport-details/filter \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": 5}'
```

### Postman Example
1. Method: POST
2. URL: `http://localhost/amt/api/student-transport-details/filter`
3. Headers:
   - `Content-Type: application/json`
   - `Client-Service: smartschool`
   - `Auth-Key: schoolAdmin@`
4. Body (raw JSON):
```json
{
  "transport_route_id": 1
}
```

### PHP Example
```php
$url = 'http://localhost/amt/api/student-transport-details/filter';
$headers = array(
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
);
$data = array(
    'class_id' => 5,
    'transport_route_id' => 1
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
- `route_pickup_point` - Route and pickup point mapping
- `transport_route` - Transport routes
- `pickup_point` - Pickup points
- `vehicle_routes` - Vehicle and route mapping
- `vehicles` - Vehicle information

### Query Logic
The API joins multiple tables to get comprehensive transport information:
1. Students with active status
2. Current session data
3. Class and section details
4. Route and pickup point assignments
5. Vehicle assignments
6. Driver information

## Notes

- Empty request body `{}` returns all students using transport in the current session
- Only active students are included in results
- Results are ordered by class and section
- All filters are optional and can be combined
- Fees are returned as float values
- Distance is in kilometers

## Use Cases

1. **Route Management** - See all students on a specific route
2. **Vehicle Capacity** - Check students assigned to each vehicle
3. **Class Transport** - View transport details for a specific class
4. **Pickup Point Analysis** - Identify students at each pickup point
5. **Fee Collection** - Calculate total transport fees
6. **Parent Communication** - Get contact details for transport students
7. **Driver Assignment** - See which students are assigned to each driver

## Related APIs

- **Inventory Stock Report API** - Inventory management
- **Add Item Report API** - Item additions
- **Issue Inventory Report API** - Item issues

## Support

For issues or questions, please contact the development team.

---

**Last Updated:** 2025-10-09  
**API Version:** 1.0  
**Status:** Production Ready

