# Roles List API Documentation

## Overview
The Roles List API provides an endpoint to retrieve all role records in the school management system. Roles define different user types and their permission levels within the system (e.g., Admin, Teacher, Accountant, Librarian, etc.).

**Base URL:** `/api/roles-list`

**Authentication Required:** Yes
- Header: `Client-Service: smartschool`
- Header: `Auth-Key: schoolAdmin@`

**HTTP Method:** POST

---

## Endpoint

### List All Roles
**URL:** `/api/roles-list/list`

**Purpose:** Retrieve all role records

**Request Body Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| N/A | N/A | No | Empty request body `{}` is accepted |

**Graceful Null/Empty Handling:**
- Empty request `{}` returns all role records
- No validation errors for empty request body
- Treats empty request the same as a list endpoint

**Example Requests:**

1. **Empty Request (All Roles):**
```json
{}
```

**Success Response (200 OK):**
```json
{
    "status": 1,
    "message": "Roles retrieved successfully",
    "total_records": 8,
    "data": [
        {
            "id": "1",
            "name": "Admin",
            "slug": "admin",
            "is_active": 1,
            "is_system": 1,
            "is_superadmin": 0,
            "created_at": "2025-01-10 08:00:00",
            "updated_at": "2025-01-10 08:00:00"
        },
        {
            "id": "2",
            "name": "Teacher",
            "slug": "teacher",
            "is_active": 1,
            "is_system": 1,
            "is_superadmin": 0,
            "created_at": "2025-01-10 08:00:00",
            "updated_at": "2025-01-10 08:00:00"
        },
        {
            "id": "3",
            "name": "Accountant",
            "slug": "accountant",
            "is_active": 1,
            "is_system": 1,
            "is_superadmin": 0,
            "created_at": "2025-01-10 08:00:00",
            "updated_at": "2025-01-10 08:00:00"
        },
        {
            "id": "4",
            "name": "Librarian",
            "slug": "librarian",
            "is_active": 1,
            "is_system": 1,
            "is_superadmin": 0,
            "created_at": "2025-01-10 08:00:00",
            "updated_at": "2025-01-10 08:00:00"
        },
        {
            "id": "7",
            "name": "Super Admin",
            "slug": "super_admin",
            "is_active": 1,
            "is_system": 1,
            "is_superadmin": 1,
            "created_at": "2025-01-10 08:00:00",
            "updated_at": "2025-01-10 08:00:00"
        }
    ],
    "timestamp": "2025-10-11 14:30:00"
}
```

**Error Response (401 Unauthorized):**
```json
{
    "status": 0,
    "message": "Unauthorized access. Invalid headers.",
    "data": null
}
```

**Error Response (405 Method Not Allowed):**
```json
{
    "status": 0,
    "message": "Method not allowed. Use POST method.",
    "data": null
}
```

**Error Response (500 Internal Server Error):**
```json
{
    "status": 0,
    "message": "Internal server error occurred",
    "data": null
}
```

---

## Implementation Details

### Controller
**File:** `api/application/controllers/Roles_list_api.php`

**Key Features:**
- Authentication check via headers
- Graceful empty request body handling
- Returns all role records
- Proper error handling and logging

### Model Methods
**File:** `api/application/models/Role_model.php`

**Methods:**
1. `get($id = null)` - Get all roles or specific role by ID

### Routes
**File:** `api/application/config/routes.php`

```php
$route['roles-list/list']['POST'] = 'roles_list_api/list';
```

---

## Usage Examples

### cURL Example

**Get All Roles:**
```bash
curl -X POST "http://localhost/amt/api/roles-list/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d "{}"
```

### JavaScript (Fetch API) Example

```javascript
fetch('http://localhost/amt/api/roles-list/list', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Client-Service': 'smartschool',
        'Auth-Key': 'schoolAdmin@'
    },
    body: JSON.stringify({})
})
.then(response => response.json())
.then(data => {
    console.log('Roles:', data);
})
.catch(error => {
    console.error('Error:', error);
});
```

### PHP Example

```php
<?php
$url = 'http://localhost/amt/api/roles-list/list';
$headers = array(
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array()));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
print_r($data);
?>
```

---

## Response Fields

| Field | Type | Description |
|-------|------|-------------|
| status | integer | 1 for success, 0 for failure |
| message | string | Descriptive message about the operation |
| total_records | integer | Total number of role records returned |
| data | array | Array of role objects |
| data[].id | string | Unique identifier for the role |
| data[].name | string | Name of the role |
| data[].slug | string/null | URL-friendly slug for the role |
| data[].is_active | integer | Active status (1 for active, 0 for inactive) |
| data[].is_system | integer | System role flag (1 for system role, 0 for custom) |
| data[].is_superadmin | integer | Super admin flag (1 for super admin, 0 for regular) |
| data[].created_at | string/null | Creation timestamp |
| data[].updated_at | string/null | Last update timestamp |
| timestamp | string | Server timestamp of the response |

---

## Notes

1. **Empty Request Handling:**
   - The API gracefully handles empty request body `{}`
   - No validation errors are thrown for empty requests
   - Returns all available role records

2. **Active Records:**
   - The API returns all records regardless of `is_active` status
   - Client applications should filter by `is_active` if needed

3. **System Roles:**
   - Roles with `is_system = 1` are built-in system roles
   - System roles typically cannot be deleted
   - Custom roles have `is_system = 0`

4. **Super Admin:**
   - Roles with `is_superadmin = 1` have full system access
   - Usually only one role (Super Admin) has this flag set

5. **Common Roles:**
   - Admin
   - Teacher
   - Accountant
   - Librarian
   - Receptionist
   - Super Admin

6. **Performance:**
   - Returns all roles in a single request
   - Suitable for dropdown lists and user assignment

7. **Use Cases:**
   - Staff role assignment
   - Permission management
   - User access control
   - Role-based filtering

---

## API Version
**Version:** 1.0.0  
**Last Updated:** October 11, 2025  
**Status:** Production Ready

