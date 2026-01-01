# User Log API Documentation

## Overview

The User Log API provides access to user login logs showing login activities of students, parents, staff, and administrators in the school management system. This API allows you to retrieve user logs with various filtering options including role, class, date range, and IP address.

## Base URL

```
http://localhost/amt/api
```

## Authentication

All API requests require the following headers:

```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

## Endpoints

### 1. List Filter Options

Returns available filter options for user logs.

**Endpoint:** `POST /user-log/list`

**Request Body:** `{}` (empty)

**Response:**
```json
{
  "status": 1,
  "message": "Filter options retrieved successfully",
  "data": {
    "roles": ["student", "parent", "Super Admin", "Teacher", "Accountant", "Operator", "Receptionist"],
    "classes": [
      {
        "id": 10,
        "class": "JR-BIPC"
      },
      {
        "id": 20,
        "class": "EMCET"
      }
    ]
  },
  "timestamp": "2025-10-09 12:00:00"
}
```

### 2. Filter User Logs

Returns user log data based on filter criteria.

**Endpoint:** `POST /user-log/filter`

**Request Body (all parameters optional):**
```json
{
  "role": "student",
  "class_id": 10,
  "section_id": 15,
  "from_date": "2025-01-01",
  "to_date": "2025-12-31",
  "ip_address": "192.168.1.1",
  "user": "john",
  "limit": 100
}
```

**Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| role | string | No | Filter by user role (student, parent, Super Admin, etc.) |
| class_id | integer | No | Filter by class ID |
| section_id | integer | No | Filter by section ID |
| from_date | string | No | Start date (YYYY-MM-DD format) |
| to_date | string | No | End date (YYYY-MM-DD format) |
| ip_address | string | No | Filter by IP address (partial match supported) |
| user | string | No | Filter by username (partial match supported) |
| limit | integer | No | Maximum number of records to return (default: 100) |

**Response:**
```json
{
  "status": 1,
  "message": "User log data retrieved successfully",
  "filters_applied": {
    "role": "student",
    "class_id": 10,
    "section_id": null,
    "from_date": "2025-01-01",
    "to_date": "2025-12-31",
    "ip_address": null,
    "user": null,
    "limit": 100
  },
  "summary": {
    "total_logs": 250,
    "total_roles": 2,
    "total_users": 45,
    "role_distribution": {
      "Student": 200,
      "Parent": 50
    }
  },
  "total_records": 250,
  "data": [
    {
      "id": 3006,
      "user": "std1571",
      "role": "student",
      "class_section_id": 123,
      "ipaddress": "192.168.1.100",
      "user_agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/141.0.0.0",
      "login_datetime": "2024-08-17 11:17:11",
      "class_id": 10,
      "class_name": "JR-BIPC",
      "section_id": 15,
      "section_name": "08199-JR-BIPC-B1",
      "formatted_datetime": "2024-08-17 11:17:11",
      "formatted_date": "2024-08-17",
      "formatted_time": "11:17:11",
      "class_section": "JR-BIPC (08199-JR-BIPC-B1)",
      "role_formatted": "Student"
    }
  ],
  "timestamp": "2025-10-09 12:00:00"
}
```

## Error Responses

### Unauthorized Access (401)
```json
{
  "status": 0,
  "message": "Unauthorized access"
}
```

### Bad Request (400)
```json
{
  "status": 0,
  "message": "Bad request. Only POST method allowed."
}
```

### Internal Server Error (500)
```json
{
  "status": 0,
  "message": "Error retrieving user log data",
  "error": "Database connection failed"
}
```

## Usage Examples

### cURL

**Get Filter Options:**
```bash
curl -X POST http://localhost/amt/api/user-log/list \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Get All Recent User Logs:**
```bash
curl -X POST http://localhost/amt/api/user-log/filter \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Filter by Role (Students Only):**
```bash
curl -X POST http://localhost/amt/api/user-log/filter \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"role": "student"}'
```

**Filter by Class:**
```bash
curl -X POST http://localhost/amt/api/user-log/filter \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": 10}'
```

**Filter by Date Range:**
```bash
curl -X POST http://localhost/amt/api/user-log/filter \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"from_date": "2025-10-01", "to_date": "2025-10-09"}'
```

**Combined Filters:**
```bash
curl -X POST http://localhost/amt/api/user-log/filter \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "role": "student",
    "class_id": 10,
    "from_date": "2025-10-01",
    "to_date": "2025-10-09",
    "limit": 50
  }'
```

### Postman

1. **Method:** POST
2. **URL:** `http://localhost/amt/api/user-log/filter`
3. **Headers:**
   - `Content-Type: application/json`
   - `Client-Service: smartschool`
   - `Auth-Key: schoolAdmin@`
4. **Body (raw JSON):**
```json
{
  "role": "student",
  "from_date": "2025-10-01",
  "to_date": "2025-10-09"
}
```

### PHP

```php
<?php
$url = 'http://localhost/amt/api/user-log/filter';
$data = array(
    'role' => 'student',
    'class_id' => 10,
    'from_date' => '2025-10-01',
    'to_date' => '2025-10-09',
    'limit' => 50
);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
));

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$result = json_decode($response, true);
print_r($result);
?>
```

## Data Fields

### User Log Record

| Field | Type | Description |
|-------|------|-------------|
| id | integer | Unique log ID |
| user | string | Username of the logged-in user |
| role | string | User role (student, parent, Super Admin, etc.) |
| class_section_id | integer | Class section ID (for students) |
| ipaddress | string | IP address of the user |
| user_agent | string | Browser/user agent information |
| login_datetime | datetime | Login timestamp |
| class_id | integer | Class ID (0 if not applicable) |
| class_name | string | Class name (empty if not applicable) |
| section_id | integer | Section ID (0 if not applicable) |
| section_name | string | Section name (empty if not applicable) |
| formatted_datetime | string | Formatted login datetime (YYYY-MM-DD HH:MM:SS) |
| formatted_date | string | Formatted date (YYYY-MM-DD) |
| formatted_time | string | Formatted time (HH:MM:SS) |
| class_section | string | Combined class and section display |
| role_formatted | string | Capitalized role name |

## Notes

1. **Empty Request Body:** Sending an empty request body `{}` returns the most recent 100 user logs (default limit).

2. **Date Format:** All dates should be in `YYYY-MM-DD` format.

3. **IP Address Filtering:** Supports partial matching (e.g., "192.168" will match all IPs starting with that).

4. **User Filtering:** Supports partial matching on username (e.g., "john" will match "john123", "johnny", etc.).

5. **Limit:** Default limit is 100 records. You can adjust this using the `limit` parameter.

6. **Ordering:** Results are ordered by most recent first (descending by login_datetime).

7. **User Roles:** Common roles include:
   - student: Student users
   - parent: Parent users
   - Super Admin: System administrators
   - Teacher: Teaching staff
   - Accountant: Accounting staff
   - Operator: System operators
   - Receptionist: Reception staff
   - Admin: Administrative staff

8. **Class/Section:** Only applicable for student logins. Staff and parent logins will have empty class/section fields.

## Use Cases

### 1. Monitor Student Login Activity
```json
{
  "role": "student",
  "from_date": "2025-10-01",
  "to_date": "2025-10-09"
}
```

### 2. Track Logins from Specific Class
```json
{
  "role": "student",
  "class_id": 10,
  "section_id": 15
}
```

### 3. Identify Suspicious Login Attempts
```json
{
  "ip_address": "suspicious_ip",
  "from_date": "2025-10-01"
}
```

### 4. Generate Staff Login Report
```json
{
  "role": "Teacher",
  "from_date": "2025-10-01",
  "to_date": "2025-10-09"
}
```

## Testing

Run the test script to verify the API:

```bash
C:\xampp\php\php.exe test_user_log_api.php
```

## Support

For issues or questions, please contact the development team.

---

**Last Updated:** 2025-10-09  
**Version:** 1.0  
**Status:** Production Ready

