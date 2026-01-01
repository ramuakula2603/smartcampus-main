# Expense Head List API Documentation

## Overview
The Expense Head List API provides an endpoint to retrieve all expense head records in the school management system. Expense heads are categories used to classify different types of expenses.

**Base URL:** `/api/expense-head-list`

**Authentication Required:** Yes
- Header: `Client-Service: smartschool`
- Header: `Auth-Key: schoolAdmin@`

**HTTP Method:** POST

---

## Endpoint

### List All Expense Heads
**URL:** `/api/expense-head-list/list`

**Purpose:** Retrieve all expense head records

**Request Body Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| N/A | N/A | No | Empty request body `{}` is accepted |

**Graceful Null/Empty Handling:**
- Empty request `{}` returns all expense head records
- No validation errors for empty request body
- Treats empty request the same as a list endpoint

**Example Requests:**

1. **Empty Request (All Expense Heads):**
```json
{}
```

**Success Response (200 OK):**
```json
{
    "status": 1,
    "message": "Expense heads retrieved successfully",
    "total_records": 6,
    "data": [
        {
            "id": "1",
            "exp_category": "Stationery Purchase",
            "description": "Office and school stationery",
            "is_active": "yes",
            "is_deleted": "no",
            "created_at": "2025-01-15 10:30:00",
            "updated_at": "2025-01-15 10:30:00"
        },
        {
            "id": "2",
            "exp_category": "Electricity Bill",
            "description": "Monthly electricity expenses",
            "is_active": "yes",
            "is_deleted": "no",
            "created_at": "2025-01-16 11:00:00",
            "updated_at": "2025-01-16 11:00:00"
        },
        {
            "id": "3",
            "exp_category": "Telephone Bill",
            "description": "Phone and internet bills",
            "is_active": "yes",
            "is_deleted": "no",
            "created_at": "2025-01-17 09:15:00",
            "updated_at": "2025-01-17 09:15:00"
        },
        {
            "id": "4",
            "exp_category": "Maintenance",
            "description": "Building and equipment maintenance",
            "is_active": "yes",
            "is_deleted": "no",
            "created_at": "2025-01-18 14:20:00",
            "updated_at": "2025-01-18 14:20:00"
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
**File:** `api/application/controllers/Expense_head_list_api.php`

**Key Features:**
- Authentication check via headers
- Graceful empty request body handling
- Returns all expense head records
- Proper error handling and logging

### Model Methods
**File:** `api/application/models/Expensehead_model.php`

**Methods:**
1. `get($id = null)` - Get all expense heads or specific expense head by ID

### Routes
**File:** `api/application/config/routes.php`

```php
$route['expense-head-list/list']['POST'] = 'expense_head_list_api/list';
```

---

## Usage Examples

### cURL Example

**Get All Expense Heads:**
```bash
curl -X POST "http://localhost/amt/api/expense-head-list/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d "{}"
```

### JavaScript (Fetch API) Example

```javascript
fetch('http://localhost/amt/api/expense-head-list/list', {
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
    console.log('Expense Heads:', data);
})
.catch(error => {
    console.error('Error:', error);
});
```

### PHP Example

```php
<?php
$url = 'http://localhost/amt/api/expense-head-list/list';
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
| total_records | integer | Total number of expense head records returned |
| data | array | Array of expense head objects |
| data[].id | string | Unique identifier for the expense head |
| data[].exp_category | string | Name/category of the expense head |
| data[].description | string/null | Description of the expense head |
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
   - Returns all available expense head records

2. **Active Records:**
   - The API returns all records regardless of `is_active` status
   - Client applications should filter by `is_active` if needed

3. **Deleted Records:**
   - Records with `is_deleted = 'yes'` may be included
   - Client applications should filter by `is_deleted` if needed

4. **Performance:**
   - Returns all expense heads in a single request
   - Suitable for dropdown lists and filter options

5. **Common Expense Categories:**
   - Stationery Purchase
   - Electricity Bill
   - Telephone Bill
   - Maintenance
   - Miscellaneous
   - Flower/Decoration

---

## API Version
**Version:** 1.0.0  
**Last Updated:** October 11, 2025  
**Status:** Production Ready

