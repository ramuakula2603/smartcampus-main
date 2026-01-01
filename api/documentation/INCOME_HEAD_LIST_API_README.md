# Income Head List API Documentation

## Overview
The Income Head List API provides an endpoint to retrieve all income head records in the school management system. Income heads are categories used to classify different types of income.

**Base URL:** `/api/income-head-list`

**Authentication Required:** Yes
- Header: `Client-Service: smartschool`
- Header: `Auth-Key: schoolAdmin@`

**HTTP Method:** POST

---

## Endpoint

### List All Income Heads
**URL:** `/api/income-head-list/list`

**Purpose:** Retrieve all income head records

**Request Body Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| N/A | N/A | No | Empty request body `{}` is accepted |

**Graceful Null/Empty Handling:**
- Empty request `{}` returns all income head records
- No validation errors for empty request body
- Treats empty request the same as a list endpoint

**Example Requests:**

1. **Empty Request (All Income Heads):**
```json
{}
```

**Success Response (200 OK):**
```json
{
    "status": 1,
    "message": "Income heads retrieved successfully",
    "total_records": 5,
    "data": [
        {
            "id": "1",
            "income_category": "Fee Collection",
            "description": "Student fee collection income",
            "is_active": "yes",
            "is_deleted": "no",
            "created_at": "2025-01-15 10:30:00",
            "updated_at": "2025-01-15 10:30:00"
        },
        {
            "id": "2",
            "income_category": "Donation",
            "description": "Donations received",
            "is_active": "yes",
            "is_deleted": "no",
            "created_at": "2025-01-16 11:00:00",
            "updated_at": "2025-01-16 11:00:00"
        },
        {
            "id": "3",
            "income_category": "Transport Fee",
            "description": "Transport fee collection",
            "is_active": "yes",
            "is_deleted": "no",
            "created_at": "2025-01-17 09:15:00",
            "updated_at": "2025-01-17 09:15:00"
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
**File:** `api/application/controllers/Income_head_list_api.php`

**Key Features:**
- Authentication check via headers
- Graceful empty request body handling
- Returns all income head records
- Proper error handling and logging

### Model Methods
**File:** `api/application/models/Incomehead_model.php`

**Methods:**
1. `get($id = null)` - Get all income heads or specific income head by ID

### Routes
**File:** `api/application/config/routes.php`

```php
$route['income-head-list/list']['POST'] = 'income_head_list_api/list';
```

---

## Usage Examples

### cURL Example

**Get All Income Heads:**
```bash
curl -X POST "http://localhost/amt/api/income-head-list/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d "{}"
```

### JavaScript (Fetch API) Example

```javascript
fetch('http://localhost/amt/api/income-head-list/list', {
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
    console.log('Income Heads:', data);
})
.catch(error => {
    console.error('Error:', error);
});
```

### PHP Example

```php
<?php
$url = 'http://localhost/amt/api/income-head-list/list';
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
| total_records | integer | Total number of income head records returned |
| data | array | Array of income head objects |
| data[].id | string | Unique identifier for the income head |
| data[].income_category | string | Name/category of the income head |
| data[].description | string/null | Description of the income head |
| data[].is_active | string | Active status ('yes' or 'no') |
| data[].is_deleted | string | Deletion status ('yes' or 'no') |
| data[].created_at | string/null | Creation timestamp |
| data[].updated_at | string/null | Last update timestamp |
| timestamp | string | Server timestamp of the response |

---

## Notes

1. **Empty Request Handling:**
   - The API gracefully handles empty request body `{}`
   - No validation errors are thrown for empty requests
   - Returns all available income head records

2. **Active Records:**
   - The API returns all records regardless of `is_active` status
   - Client applications should filter by `is_active` if needed

3. **Deleted Records:**
   - Records with `is_deleted = 'yes'` may be included
   - Client applications should filter by `is_deleted` if needed

4. **Performance:**
   - Returns all income heads in a single request
   - Suitable for dropdown lists and filter options

---

## API Version
**Version:** 1.0.0  
**Last Updated:** October 11, 2025  
**Status:** Production Ready

