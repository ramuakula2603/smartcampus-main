# Issue Inventory Report API Documentation

## Overview

The Issue Inventory Report API provides endpoints to retrieve information about items issued to staff members within a specified date range, including issue details, return status, and staff information.

**Status:** ✅ Fully Working

**Base URL:** `http://localhost/amt/api`

## Key Features

- ✅ **Issue Tracking** - Track items issued to staff members
- ✅ **Return Status** - Shows whether items have been returned
- ✅ **Staff Information** - Includes details of who received and issued items
- ✅ **Date Range Filtering** - Filter by issue date ranges
- ✅ **Graceful Null Handling** - Empty requests return all records
- ✅ **Comprehensive Summary** - Total issues, returned, and not returned counts

## Authentication

Required headers:
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

## Endpoints

### 1. List Endpoint

**URL:** `POST /api/issue-inventory-report/list`

**Description:** Get filter options including search types.

**Request Body:** `{}`

**Response Example:**
```json
{
  "status": 1,
  "message": "Filter options retrieved successfully",
  "data": {
    "search_types": {
      "today": "Today",
      "this_week": "This Week",
      "this_month": "This Month",
      "last_month": "Last Month",
      "this_year": "This Year",
      "period": "Period"
    }
  },
  "timestamp": "2025-10-09 12:34:56"
}
```

### 2. Filter Endpoint

**URL:** `POST /api/issue-inventory-report/filter`

**Description:** Get issue inventory report data.

**Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| search_type | string | No | Date range: today, this_week, this_month, last_month, this_year, period |
| date_from | string | No | Start date (YYYY-MM-DD) |
| date_to | string | No | End date (YYYY-MM-DD) |

**Request Examples:**

1. **Empty Request (All Records):**
```json
{}
```

2. **This Week:**
```json
{
  "search_type": "this_week"
}
```

3. **Custom Date Range:**
```json
{
  "search_type": "period",
  "date_from": "2025-01-01",
  "date_to": "2025-03-31"
}
```

**Response Example:**
```json
{
  "status": 1,
  "message": "Issue inventory report retrieved successfully",
  "filters_applied": {
    "search_type": "this_week",
    "date_from": null,
    "date_to": null,
    "date_range_used": {
      "start_date": "2025-10-06",
      "end_date": "2025-10-09"
    }
  },
  "summary": {
    "total_issues": 15,
    "total_quantity": 45,
    "total_returned": 8,
    "total_not_returned": 7
  },
  "total_records": 15,
  "data": [
    {
      "id": "1",
      "item_id": "5",
      "issue_to": "12",
      "issue_by": "3",
      "issue_date": "2025-10-08",
      "return_date": "2025-10-09",
      "quantity": 2,
      "note": "For classroom use",
      "is_returned": 1,
      "item_name": "Laptop Dell Inspiron",
      "item_category_id": "1",
      "item_category": "Electronics",
      "employee_id": "EMP001",
      "staff_name": "John",
      "surname": "Doe",
      "issued_by_employee_id": "EMP005",
      "issued_by_name": "Jane",
      "issued_by_surname": "Smith",
      "role_name": "Teacher",
      "issue_to_info": {
        "employee_id": "EMP001",
        "name": "John Doe",
        "role": "Teacher"
      },
      "issued_by_info": {
        "employee_id": "EMP005",
        "name": "Jane Smith"
      },
      "return_status": "Returned"
    },
    {
      "id": "2",
      "item_id": "8",
      "issue_to": "15",
      "issue_by": "3",
      "issue_date": "2025-10-07",
      "return_date": null,
      "quantity": 5,
      "note": "Office supplies",
      "is_returned": 0,
      "item_name": "Office Chair",
      "item_category_id": "2",
      "item_category": "Furniture",
      "employee_id": "EMP002",
      "staff_name": "Mike",
      "surname": "Johnson",
      "issued_by_employee_id": "EMP005",
      "issued_by_name": "Jane",
      "issued_by_surname": "Smith",
      "role_name": "Administrator",
      "issue_to_info": {
        "employee_id": "EMP002",
        "name": "Mike Johnson",
        "role": "Administrator"
      },
      "issued_by_info": {
        "employee_id": "EMP005",
        "name": "Jane Smith"
      },
      "return_status": "Not Returned"
    }
  ],
  "timestamp": "2025-10-09 12:34:56"
}
```

## Response Fields

### Summary Fields
- `total_issues` - Total number of issue records
- `total_quantity` - Sum of all quantities issued
- `total_returned` - Count of items returned
- `total_not_returned` - Count of items not returned

### Data Fields (per issue)
- `id` - Issue record ID
- `item_id` - Item ID
- `issue_to` - Staff ID who received the item
- `issue_by` - Staff ID who issued the item
- `issue_date` - Date item was issued
- `return_date` - Date item was returned (null if not returned)
- `quantity` - Quantity issued
- `note` - Notes/description
- `is_returned` - Return status (0 = not returned, 1 = returned)
- `item_name` - Item name
- `item_category_id` - Category ID
- `item_category` - Category name
- `employee_id` - Employee ID of recipient
- `staff_name` - First name of recipient
- `surname` - Last name of recipient
- `issued_by_employee_id` - Employee ID of issuer
- `issued_by_name` - First name of issuer
- `issued_by_surname` - Last name of issuer
- `role_name` - Role of recipient
- `issue_to_info` - Formatted recipient information
- `issued_by_info` - Formatted issuer information
- `return_status` - "Returned" or "Not Returned"

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
curl -X POST http://localhost/amt/api/issue-inventory-report/filter \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"search_type": "this_week"}'
```

### Postman Example
1. Method: POST
2. URL: `http://localhost/amt/api/issue-inventory-report/filter`
3. Headers:
   - `Content-Type: application/json`
   - `Client-Service: smartschool`
   - `Auth-Key: schoolAdmin@`
4. Body (raw JSON):
```json
{
  "search_type": "this_week"
}
```

### PHP Example
```php
$url = 'http://localhost/amt/api/issue-inventory-report/filter';
$headers = array(
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
);
$data = array(
    'search_type' => 'period',
    'date_from' => '2025-01-01',
    'date_to' => '2025-03-31'
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
- `item_issue` - Item issue records (main table)
- `item` - Item master data
- `item_category` - Item categories
- `staff` - Staff information (for both recipient and issuer)
- `staff_roles` - Staff role assignments
- `roles` - Role definitions

### Date Filtering
- Date filtering is applied to the `item_issue.issue_date` field
- Format: YYYY-MM-DD
- Inclusive of both start and end dates

## Notes

- Empty request body `{}` returns all issue records for the current year
- Results are ordered by issue date in descending order (newest first)
- Return date is null if item has not been returned
- `is_returned` field: 0 = not returned, 1 = returned
- Staff information includes both recipient and issuer details
- Role information is included for the recipient

## Use Cases

1. **Track Outstanding Items** - Monitor items not yet returned
2. **Staff Accountability** - See which staff members have issued items
3. **Audit Trail** - Review all issues within a specific period
4. **Return Reminders** - Identify overdue items
5. **Usage Analysis** - Analyze item usage patterns by staff

## Related APIs

- **Inventory Stock Report API** - Shows current stock levels
- **Add Item Report API** - Shows items added to inventory

## Support

For issues or questions, please contact the development team.

---

**Last Updated:** 2025-10-09  
**API Version:** 1.0  
**Status:** Production Ready

