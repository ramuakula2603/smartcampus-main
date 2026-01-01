# Staff Report API Documentation

## Overview
The Staff Report API provides endpoints to retrieve comprehensive staff information with flexible filtering options. This API allows you to fetch staff data by role, designation, status, and date range.

## Base URL
```
http://localhost/amt/api
```

## Authentication
All API requests require authentication headers:

| Header | Value | Required |
|--------|-------|----------|
| `Client-Service` | `smartschool` | Yes |
| `Auth-Key` | `schoolAdmin@` | Yes |
| `Content-Type` | `application/json` | Yes |

## Endpoints

### 1. Filter Staff Report
Retrieve staff report data with optional filters.

**Endpoint:** `POST /api/staff-report/filter`

**Request Body Parameters (All Optional):**

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `role` | integer | Role ID | `1` |
| `designation` | integer | Designation ID | `2` |
| `staff_status` | string | Staff status | `"1"` (Active), `"2"` (Inactive), `"both"` |
| `search_type` | string | Predefined date range | `"this_year"`, `"this_month"`, `"this_week"`, `"today"` |
| `from_date` | string | Start date (YYYY-MM-DD) | `"2025-01-01"` |
| `to_date` | string | End date (YYYY-MM-DD) | `"2025-12-31"` |

**Important:** 
- Empty request body `{}` returns **ALL staff data**
- All parameters are optional
- Custom date range (`from_date` and `to_date`) takes precedence over `search_type`

**Response Format:**
```json
{
    "status": 1,
    "message": "Staff report retrieved successfully",
    "filters_applied": {
        "role": 1,
        "designation": 2,
        "staff_status": "1",
        "search_type": null,
        "from_date": null,
        "to_date": null
    },
    "total_records": 45,
    "data": [
        {
            "id": "123",
            "employee_id": "EMP001",
            "name": "John",
            "surname": "Doe",
            "father_name": "Robert Doe",
            "mother_name": "Jane Doe",
            "email": "john.doe@school.com",
            "gender": "Male",
            "dob": "1985-05-15",
            "date_of_joining": "2020-01-10",
            "phone": "1234567890",
            "emergency_contact_number": "9876543210",
            "marital_status": "Married",
            "current_address": "123 Main St",
            "permanent_address": "123 Main St",
            "qualification": "M.Ed",
            "work_exp": "5 years",
            "note": "Excellent teacher",
            "is_active": "1",
            "designation": "Senior Teacher",
            "department": "Mathematics",
            "user_type": "Teacher",
            "leaves": "1@12,2@10",
            "processed_leaves": [
                {
                    "leave_type_id": "1",
                    "leave_type": "Casual Leave",
                    "alloted_leave": "12"
                },
                {
                    "leave_type_id": "2",
                    "leave_type": "Sick Leave",
                    "alloted_leave": "10"
                }
            ]
        }
    ],
    "leave_types": {
        "1": "Casual Leave",
        "2": "Sick Leave",
        "3": "Maternity Leave"
    },
    "timestamp": "2025-10-07 22:45:00"
}
```

**Response Fields:**

| Field | Type | Description |
|-------|------|-------------|
| `status` | integer | 1 for success, 0 for error |
| `message` | string | Response message |
| `filters_applied` | object | Applied filter parameters |
| `total_records` | integer | Number of records returned |
| `data` | array | Array of staff records |
| `leave_types` | object | Map of leave type IDs to names |
| `timestamp` | string | Response timestamp |

**Staff Record Fields:**

| Field | Description |
|-------|-------------|
| `id` | Staff ID |
| `employee_id` | Employee ID |
| `name` | First name |
| `surname` | Last name |
| `father_name` | Father's name |
| `mother_name` | Mother's name |
| `email` | Email address |
| `gender` | Gender |
| `dob` | Date of birth |
| `date_of_joining` | Joining date |
| `phone` | Phone number |
| `emergency_contact_number` | Emergency contact |
| `marital_status` | Marital status |
| `current_address` | Current address |
| `permanent_address` | Permanent address |
| `qualification` | Educational qualification |
| `work_exp` | Work experience |
| `note` | Additional notes |
| `is_active` | Active status (1=Active, 0=Inactive) |
| `designation` | Job designation |
| `department` | Department name |
| `user_type` | Role name |
| `leaves` | Raw leave data (format: type_id@count) |
| `processed_leaves` | Processed leave information array |

### 2. List Staff Filter Options
Retrieve available filter options (roles, designations, leave types, status options).

**Endpoint:** `POST /api/staff-report/list`

**Request Body:** Empty `{}`

**Response Format:**
```json
{
    "status": 1,
    "message": "Staff filter options retrieved successfully",
    "total_roles": 5,
    "roles": [
        {"id": "1", "name": "Teacher"},
        {"id": "2", "name": "Admin"},
        {"id": "3", "name": "Accountant"}
    ],
    "total_designations": 8,
    "designations": [
        {"id": "1", "designation": "Senior Teacher"},
        {"id": "2", "designation": "Junior Teacher"},
        {"id": "3", "designation": "Principal"}
    ],
    "total_leave_types": 3,
    "leave_types": [
        {"id": "1", "type": "Casual Leave", "is_active": "yes"},
        {"id": "2", "type": "Sick Leave", "is_active": "yes"},
        {"id": "3", "type": "Maternity Leave", "is_active": "yes"}
    ],
    "status_options": [
        {"value": "1", "label": "Active"},
        {"value": "2", "label": "Inactive"},
        {"value": "both", "label": "Both"}
    ],
    "note": "Use the filter endpoint with role, designation, staff_status, or date range to get staff report",
    "timestamp": "2025-10-07 22:45:00"
}
```

## Usage Examples

### Example 1: Get All Staff Data (Empty Request)
```bash
curl -X POST "http://localhost/amt/api/staff-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Returns:** All staff records

### Example 2: Filter by Role
```bash
curl -X POST "http://localhost/amt/api/staff-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "role": 1
  }'
```

### Example 3: Filter by Designation and Status
```bash
curl -X POST "http://localhost/amt/api/staff-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "designation": 2,
    "staff_status": "1"
  }'
```

### Example 4: Filter by Date Range (Joining Date)
```bash
curl -X POST "http://localhost/amt/api/staff-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "from_date": "2024-01-01",
    "to_date": "2024-12-31"
  }'
```

### Example 5: Filter by Predefined Date Range
```bash
curl -X POST "http://localhost/amt/api/staff-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "search_type": "this_year"
  }'
```

### Example 6: Get Filter Options
```bash
curl -X POST "http://localhost/amt/api/staff-report/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 7: Filter Active Teachers
```bash
curl -X POST "http://localhost/amt/api/staff-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "role": 1,
    "staff_status": "1"
  }'
```

## Code Examples

### JavaScript (Fetch API)
```javascript
// Get all staff data
async function getAllStaffData() {
    const response = await fetch('http://localhost/amt/api/staff-report/filter', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Client-Service': 'smartschool',
            'Auth-Key': 'schoolAdmin@'
        },
        body: JSON.stringify({})
    });
    
    const data = await response.json();
    console.log('Staff Data:', data);
    return data;
}

// Filter by role and status
async function getStaffByRole(roleId, status) {
    const response = await fetch('http://localhost/amt/api/staff-report/filter', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Client-Service': 'smartschool',
            'Auth-Key': 'schoolAdmin@'
        },
        body: JSON.stringify({
            role: roleId,
            staff_status: status
        })
    });
    
    const data = await response.json();
    return data;
}

// Get filter options
async function getFilterOptions() {
    const response = await fetch('http://localhost/amt/api/staff-report/list', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Client-Service': 'smartschool',
            'Auth-Key': 'schoolAdmin@'
        },
        body: JSON.stringify({})
    });
    
    const data = await response.json();
    return data;
}

// Usage
getAllStaffData();
getStaffByRole(1, '1'); // Get active teachers
getFilterOptions();
```

### PHP (cURL)
```php
<?php
// Get all staff data
function getAllStaffData() {
    $url = 'http://localhost/amt/api/staff-report/filter';
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Client-Service: smartschool',
        'Auth-Key: schoolAdmin@'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([]));
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

// Filter by designation
function getStaffByDesignation($designationId) {
    $url = 'http://localhost/amt/api/staff-report/filter';
    
    $data = [
        'designation' => $designationId,
        'staff_status' => '1'
    ];
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Client-Service: smartschool',
        'Auth-Key: schoolAdmin@'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

// Usage
$allStaff = getAllStaffData();
$seniorTeachers = getStaffByDesignation(1);
?>
```

### Python (Requests)
```python
import requests
import json

# API configuration
BASE_URL = 'http://localhost/amt/api'
HEADERS = {
    'Content-Type': 'application/json',
    'Client-Service': 'smartschool',
    'Auth-Key': 'schoolAdmin@'
}

# Get all staff data
def get_all_staff_data():
    url = f'{BASE_URL}/staff-report/filter'
    response = requests.post(url, headers=HEADERS, json={})
    return response.json()

# Filter by role
def get_staff_by_role(role_id, status='1'):
    url = f'{BASE_URL}/staff-report/filter'
    data = {
        'role': role_id,
        'staff_status': status
    }
    response = requests.post(url, headers=HEADERS, json=data)
    return response.json()

# Filter by date range
def get_staff_by_joining_date(from_date, to_date):
    url = f'{BASE_URL}/staff-report/filter'
    data = {
        'from_date': from_date,
        'to_date': to_date
    }
    response = requests.post(url, headers=HEADERS, json=data)
    return response.json()

# Filter by search type
def get_staff_by_search_type(search_type):
    url = f'{BASE_URL}/staff-report/filter'
    data = {
        'search_type': search_type
    }
    response = requests.post(url, headers=HEADERS, json=data)
    return response.json()

# Get filter options
def get_filter_options():
    url = f'{BASE_URL}/staff-report/list'
    response = requests.post(url, headers=HEADERS, json={})
    return response.json()

# Usage
if __name__ == '__main__':
    # Get all staff
    all_staff = get_all_staff_data()
    print(f"Total staff: {all_staff['total_records']}")

    # Get active teachers
    teachers = get_staff_by_role(1, '1')
    print(f"Active teachers: {teachers['total_records']}")

    # Get staff joined this year
    this_year_staff = get_staff_by_search_type('this_year')
    print(f"Staff joined this year: {this_year_staff['total_records']}")

    # Get filter options
    options = get_filter_options()
    print(f"Available roles: {len(options['roles'])}")
    print(f"Available designations: {len(options['designations'])}")
```

## Search Type Options

The `search_type` parameter accepts the following predefined values:

| Value | Description | Date Range |
|-------|-------------|------------|
| `today` | Today's date | Current date only |
| `this_week` | Current week | Monday to Sunday of current week |
| `this_month` | Current month | 1st to last day of current month |
| `this_year` | Current year | January 1 to December 31 of current year |

## Staff Status Values

| Value | Description |
|-------|-------------|
| `"1"` | Active staff only |
| `"2"` | Inactive staff only |
| `"both"` | Both active and inactive staff |

## Error Handling

### Error Response Format
```json
{
    "status": 0,
    "message": "Error description",
    "error": "Detailed error message"
}
```

### Common Error Codes

| HTTP Status | Description |
|-------------|-------------|
| 400 | Bad Request - Invalid request method |
| 401 | Unauthorized - Invalid or missing authentication |
| 500 | Internal Server Error - Server-side error |

### Error Examples

**Unauthorized Access:**
```json
{
    "status": 0,
    "message": "Unauthorized access"
}
```

**Bad Request:**
```json
{
    "status": 0,
    "message": "Bad request. Only POST method allowed."
}
```

## Notes

1. **Empty Request Behavior:** Sending an empty request body `{}` to the filter endpoint returns all staff data, not an error.

2. **Date Range Priority:** If both custom date range (`from_date`, `to_date`) and `search_type` are provided, the custom date range takes precedence.

3. **Leave Data:** The API returns both raw leave data (`leaves` field) and processed leave information (`processed_leaves` array) for convenience.

4. **Custom Fields:** If custom fields are defined for staff, they will be included in the response with their field names as keys.

5. **Role and Designation:** Both role and designation filters can be combined for more specific queries.

6. **Date Format:** All dates should be in `YYYY-MM-DD` format.

7. **Active Status:** By default, if no `staff_status` is provided, the API returns all staff regardless of status.

## Data Processing Tips

### Processing Leave Information
```javascript
// Example: Calculate total leave days for a staff member
function calculateTotalLeaves(staff) {
    if (!staff.processed_leaves) return 0;

    return staff.processed_leaves.reduce((total, leave) => {
        return total + parseInt(leave.alloted_leave);
    }, 0);
}

// Usage
const staff = staffData.data[0];
const totalLeaves = calculateTotalLeaves(staff);
console.log(`Total leave days: ${totalLeaves}`);
```

### Filtering by Multiple Criteria
```javascript
// Example: Get active teachers with specific designation
async function getActiveTeachersWithDesignation(roleId, designationId) {
    const response = await fetch('http://localhost/amt/api/staff-report/filter', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Client-Service': 'smartschool',
            'Auth-Key': 'schoolAdmin@'
        },
        body: JSON.stringify({
            role: roleId,
            designation: designationId,
            staff_status: '1'
        })
    });

    return await response.json();
}
```

## Best Practices

1. **Always include authentication headers** in every request
2. **Use predefined search types** for common date ranges
3. **Cache filter options** from the list endpoint to avoid repeated calls
4. **Handle empty data arrays** gracefully in your application
5. **Validate date formats** before sending requests
6. **Check the status field** in responses before processing data
7. **Process leave data** using the `processed_leaves` array for easier handling
8. **Combine filters** for more specific queries
9. **Log errors** for debugging and monitoring

## Support

For issues or questions regarding this API, please contact the system administrator or refer to the main API documentation.

---

**Version:** 1.0
**Last Updated:** October 7, 2025
**API Endpoint:** `/api/staff-report`

