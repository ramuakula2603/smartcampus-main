# Audit Log API Documentation

## Overview

The Audit Log API provides access to system audit trail logs showing actions performed by staff members in the school management system. This API allows you to retrieve audit logs with various filtering options including user, action type, platform, date range, and IP address.

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

Returns available filter options for audit logs.

**Endpoint:** `POST /audit-log/list`

**Request Body:** `{}` (empty)

**Response:**
```json
{
  "status": 1,
  "message": "Filter options retrieved successfully",
  "data": {
    "actions": ["Insert", "Update", "Delete"],
    "platforms": ["Windows 10", "Android", "Linux", "Mac OS X"],
    "users": [
      {
        "id": 35,
        "staff_name": "chinthala pavan kalyan (16443)"
      }
    ]
  },
  "timestamp": "2025-10-09 12:00:00"
}
```

### 2. Filter Audit Logs

Returns audit log data based on filter criteria.

**Endpoint:** `POST /audit-log/filter`

**Request Body (all parameters optional):**
```json
{
  "user_id": 35,
  "action": "Insert",
  "platform": "Windows 10",
  "from_date": "2025-01-01",
  "to_date": "2025-12-31",
  "ip_address": "192.168.1.1",
  "limit": 100
}
```

**Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| user_id | integer | No | Filter by staff user ID |
| action | string | No | Filter by action type (Insert, Update, Delete) |
| platform | string | No | Filter by platform (Windows 10, Android, etc.) |
| from_date | string | No | Start date (YYYY-MM-DD format) |
| to_date | string | No | End date (YYYY-MM-DD format) |
| ip_address | string | No | Filter by IP address (partial match supported) |
| limit | integer | No | Maximum number of records to return (default: 100) |

**Response:**
```json
{
  "status": 1,
  "message": "Audit log data retrieved successfully",
  "filters_applied": {
    "user_id": 35,
    "action": "Insert",
    "platform": "Windows 10",
    "from_date": "2025-01-01",
    "to_date": "2025-12-31",
    "ip_address": null,
    "limit": 100
  },
  "summary": {
    "total_logs": 150,
    "total_actions": 3,
    "total_users": 5,
    "action_distribution": {
      "Insert": 100,
      "Update": 35,
      "Delete": 15
    }
  },
  "total_records": 150,
  "data": [
    {
      "id": 65813,
      "message": "New Record inserted On disable reason id 4",
      "record_id": "4",
      "user_id": 35,
      "action": "Insert",
      "ip_address": "::1",
      "platform": "Windows 10",
      "agent": "Chrome",
      "time": "2025-10-06 21:51:19",
      "created_at": "2025-10-06",
      "staff_name": "chinthala pavan kalyan (16443)",
      "employee_id": "16443",
      "staff_first_name": "chinthala",
      "staff_last_name": "pavan kalyan",
      "formatted_time": "2025-10-06 21:51:19",
      "formatted_date": "2025-10-06",
      "formatted_time_only": "21:51:19"
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
  "message": "Error retrieving audit log data",
  "error": "Database connection failed"
}
```

## Usage Examples

### cURL

**Get Filter Options:**
```bash
curl -X POST http://localhost/amt/api/audit-log/list \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Get All Recent Audit Logs:**
```bash
curl -X POST http://localhost/amt/api/audit-log/filter \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Filter by Action:**
```bash
curl -X POST http://localhost/amt/api/audit-log/filter \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"action": "Insert"}'
```

**Filter by User:**
```bash
curl -X POST http://localhost/amt/api/audit-log/filter \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"user_id": 35}'
```

**Filter by Date Range:**
```bash
curl -X POST http://localhost/amt/api/audit-log/filter \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"from_date": "2025-10-01", "to_date": "2025-10-09"}'
```

**Combined Filters:**
```bash
curl -X POST http://localhost/amt/api/audit-log/filter \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "action": "Insert",
    "platform": "Windows 10",
    "from_date": "2025-10-01",
    "to_date": "2025-10-09",
    "limit": 50
  }'
```

### Postman

1. **Method:** POST
2. **URL:** `http://localhost/amt/api/audit-log/filter`
3. **Headers:**
   - `Content-Type: application/json`
   - `Client-Service: smartschool`
   - `Auth-Key: schoolAdmin@`
4. **Body (raw JSON):**
```json
{
  "action": "Insert",
  "from_date": "2025-10-01",
  "to_date": "2025-10-09"
}
```

### PHP

```php
<?php
$url = 'http://localhost/amt/api/audit-log/filter';
$data = array(
    'action' => 'Insert',
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

### Audit Log Record

| Field | Type | Description |
|-------|------|-------------|
| id | integer | Unique log ID |
| message | string | Audit log message describing the action |
| record_id | string | ID of the affected record |
| user_id | integer | Staff user ID who performed the action |
| action | string | Action type (Insert, Update, Delete) |
| ip_address | string | IP address of the user |
| platform | string | Operating system platform |
| agent | string | Browser/user agent |
| time | datetime | Timestamp of the action |
| created_at | date | Date the log was created |
| staff_name | string | Full name of staff member with employee ID |
| employee_id | string | Staff employee ID |
| staff_first_name | string | Staff first name |
| staff_last_name | string | Staff last name |
| formatted_time | string | Formatted timestamp (YYYY-MM-DD HH:MM:SS) |
| formatted_date | string | Formatted date (YYYY-MM-DD) |
| formatted_time_only | string | Formatted time only (HH:MM:SS) |

## Notes

1. **Empty Request Body:** Sending an empty request body `{}` returns the most recent 100 audit logs (default limit).

2. **Date Format:** All dates should be in `YYYY-MM-DD` format.

3. **IP Address Filtering:** Supports partial matching (e.g., "192.168" will match all IPs starting with that).

4. **Limit:** Default limit is 100 records. You can adjust this using the `limit` parameter.

5. **Ordering:** Results are ordered by most recent first (descending by time).

6. **Action Types:** Common action types include:
   - Insert: New record created
   - Update: Existing record modified
   - Delete: Record deleted

7. **Platform Types:** Common platforms include:
   - Windows 10
   - Android
   - Linux
   - Mac OS X

## Testing

Run the test script to verify the API:

```bash
C:\xampp\php\php.exe test_audit_log_api.php
```

## Support

For issues or questions, please contact the development team.

---

**Last Updated:** 2025-10-09  
**Version:** 1.0  
**Status:** Production Ready

