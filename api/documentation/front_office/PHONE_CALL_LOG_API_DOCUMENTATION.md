# Phone Call Log API Documentation

## Overview

The **Phone Call Log API** provides RESTful endpoints for managing phone call log records in the school management system. This API enables you to create, read, update, and delete phone call log entries, track incoming and outgoing calls, and manage call-related information such as caller details, call duration, descriptions, and follow-up dates.

**Base URL:** `http://localhost/amt/api`

**Authentication:** All endpoints require the following headers:
- `Client-Service: smartschool`
- `Auth-Key: schoolAdmin@`
- `Content-Type: application/json`

---

## ⚠️ Important Notes

**All endpoints use POST method** - Even for retrieving data, you must use POST (not GET).

**URL Format** - Make sure your URL is correct:
- ❌ Wrong: `hhttp://localhost/amt/api/generalcall_api/detail/1` (typo: "hhttp")
- ✅ Correct: `http://localhost/amt/api/generalcall_api/detail/1`
- Always use `http://` (not `https://` or `hhttp://`)

**URL Placeholders** - When you see `{id}` in the documentation, replace it with an actual number:
- ❌ Wrong: `http://localhost/amt/api/generalcall_api/detail/{id}`
- ✅ Correct: `http://localhost/amt/api/generalcall_api/detail/1`
- Example: For ID 1, use `/detail/1` not `/detail/{id}`

**Request Body** - Even if the endpoint doesn't require data, you must send an empty JSON object `{}` in the request body.

---

## Endpoints

### 1. List All Phone Call Logs

**Endpoint:** `POST /generalcall_api/list`

**Description:** Retrieves a list of all phone call log entries.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Request Body:**
```json
{}
```

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Phone call logs retrieved successfully",
  "total_records": 10,
  "data": [
    {
      "id": "1",
      "name": "John Doe",
      "contact": "1234567890",
      "date": "2025-11-07",
      "description": "Enquiry about admission",
      "call_duration": "10 minutes",
      "note": "Interested in Grade 5 admission",
      "follow_up_date": "2025-11-10",
      "created_at": "2025-11-07 10:30:00"
    },
    {
      "id": "2",
      "name": "Jane Smith",
      "contact": "9876543210",
      "date": "2025-11-08",
      "description": "Fee payment enquiry",
      "call_duration": "5 minutes",
      "note": "Will call back next week",
      "follow_up_date": "2025-11-15",
      "created_at": "2025-11-08 14:20:00"
    }
  ]
}
```

**Error Responses:**

**401 Unauthorized - Invalid Headers:**
```json
{
  "status": 0,
  "message": "Unauthorized access. Invalid headers.",
  "data": null
}
```

**405 Method Not Allowed:**
```json
{
  "status": 0,
  "message": "Method not allowed. Use POST method.",
  "data": null
}
```

**500 Internal Server Error:**
```json
{
  "status": 0,
  "message": "Internal server error occurred",
  "data": null
}
```

---

### 2. Get Specific Phone Call Log

**Endpoint:** `POST /generalcall_api/detail/{id}`

**Description:** Retrieves details of a specific phone call log entry by its ID.

**⚠️ Important:** 
- **Method must be POST** (not GET)
- Replace `{id}` with an actual call log ID number (e.g., use `1` instead of `{id}`)
- Example URL: `http://localhost/amt/api/generalcall_api/detail/1`

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**URL Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `id` | integer | Yes | ID of the phone call log | `1`, `2`, `3` |

**Request Body:** `{}` (empty JSON object)

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Phone call log retrieved successfully",
  "data": {
    "id": "1",
    "name": "John Doe",
    "contact": "1234567890",
    "date": "2025-11-07",
    "description": "Enquiry about admission",
    "call_duration": "10 minutes",
    "note": "Interested in Grade 5 admission",
    "follow_up_date": "2025-11-10",
    "created_at": "2025-11-07 10:30:00"
  }
}
```

**Error Responses:**

**400 Bad Request - Invalid ID:**
```json
{
  "status": 0,
  "message": "Invalid or missing phone call log ID",
  "data": null
}
```

**404 Not Found:**
```json
{
  "status": 0,
  "message": "Phone call log not found",
  "data": null
}
```

---

### 3. Create New Phone Call Log

**Endpoint:** `POST /generalcall_api/add`

**Description:** Creates a new phone call log entry.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Request Body:**
```json
{
  "name": "John Doe",
  "contact": "1234567890",
  "date": "2025-11-07",
  "description": "Enquiry about admission",
  "call_duration": "10 minutes",
  "note": "Interested in Grade 5 admission",
  "follow_up_date": "2025-11-10"
}
```

**Request Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `name` | string | Yes | Caller's name | `"John Doe"`, `"Jane Smith"` |
| `contact` | string | Yes | Contact number | `"1234567890"`, `"9876543210"` |
| `date` | string (YYYY-MM-DD) | Yes | Call date | `"2025-11-07"`, `"2025-11-10"` |
| `description` | string | Yes | Call description | `"Enquiry about admission"`, `"Fee payment enquiry"` |
| `call_duration` | string | Yes | Duration of the call | `"10 minutes"`, `"15 minutes"` |
| `note` | string | No | Additional notes | `"Interested in Grade 5 admission"` |
| `follow_up_date` | string (YYYY-MM-DD) | No | Follow-up date | `"2025-11-10"`, `"2025-11-15"` |

**Success Response (201 Created):**
```json
{
  "status": 1,
  "message": "Phone call log created successfully",
  "data": {
    "id": "1",
    "name": "John Doe",
    "contact": "1234567890",
    "date": "2025-11-07",
    "description": "Enquiry about admission",
    "call_duration": "10 minutes",
    "note": "Interested in Grade 5 admission",
    "follow_up_date": "2025-11-10",
    "created_at": "2025-11-07 10:30:00"
  }
}
```

**Error Responses:**

**400 Bad Request - Missing Required Field:**
```json
{
  "status": 0,
  "message": "Name is required",
  "data": null
}
```

**400 Bad Request - Invalid Date Format:**
```json
{
  "status": 0,
  "message": "Invalid date format. Use YYYY-MM-DD format",
  "data": null
}
```

---

### 4. Update Phone Call Log

**Endpoint:** `POST /generalcall_api/update/{id}`

**Description:** Updates an existing phone call log entry.

**⚠️ Important:** 
- **Method must be POST** (not PUT or PATCH)
- **URL must start with `http://`** (not `hhttp://` or `https://`)
- **Replace `{id}` with an actual call log ID number** (e.g., use `1` instead of `{id}`)
- **Correct Example URL:** `http://localhost/amt/api/generalcall_api/update/1`
- You must have a valid call log ID. Use the list endpoint first to get existing call log IDs.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**URL Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `id` | integer | Yes | ID of the phone call log to update | `1`, `2`, `3` |

**Request Body:**
```json
{
  "name": "John Doe",
  "contact": "1234567890",
  "date": "2025-11-07",
  "description": "Updated enquiry details",
  "call_duration": "15 minutes",
  "note": "Updated notes",
  "follow_up_date": "2025-11-12"
}
```

**Request Parameters:** (Same as Create endpoint)

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Phone call log updated successfully",
  "data": {
    "id": "1",
    "name": "John Doe",
    "contact": "1234567890",
    "date": "2025-11-07",
    "description": "Updated enquiry details",
    "call_duration": "15 minutes",
    "note": "Updated notes",
    "follow_up_date": "2025-11-12",
    "created_at": "2025-11-07 10:30:00"
  }
}
```

**Error Responses:**

**400 Bad Request - Invalid ID:**
```json
{
  "status": 0,
  "message": "Invalid or missing phone call log ID",
  "data": null
}
```

**404 Not Found - Call log doesn't exist:**
```json
{
  "status": 0,
  "message": "Phone call log not found",
  "data": null
}
```

---

### 5. Delete Phone Call Log

**Endpoint:** `POST /generalcall_api/delete/{id}`

**Description:** Deletes a phone call log entry.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**URL Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `id` | integer | Yes | ID of the phone call log to delete | `1`, `2`, `3` |

**Request Body:** `{}` (empty JSON object)

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Phone call log deleted successfully",
  "data": {
    "id": "1"
  }
}
```

**Error Responses:**

**400 Bad Request - Invalid ID:**
```json
{
  "status": 0,
  "message": "Invalid or missing phone call log ID",
  "data": null
}
```

**404 Not Found:**
```json
{
  "status": 0,
  "message": "Phone call log not found",
  "data": null
}
```

---

## HTTP Status Codes

| Code | Meaning | Description |
|------|---------|-------------|
| 200 | OK | Request successful (GET, LIST, UPDATE, DELETE operations) |
| 201 | Created | Resource created successfully (CREATE operation) |
| 400 | Bad Request | Invalid input or missing required fields |
| 401 | Unauthorized | Invalid or missing authentication headers |
| 404 | Not Found | Resource not found (call log) |
| 405 | Method Not Allowed | Wrong HTTP method used (must be POST) |
| 500 | Internal Server Error | Server error occurred |

---

## cURL Examples

### Example 1: List All Phone Call Logs

```bash
curl -X POST "http://localhost/amt/api/generalcall_api/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 2: Get Specific Phone Call Log

```bash
curl -X POST "http://localhost/amt/api/generalcall_api/detail/1" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 3: Create New Phone Call Log

```bash
curl -X POST "http://localhost/amt/api/generalcall_api/add" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "name": "John Doe",
    "contact": "1234567890",
    "date": "2025-11-07",
    "description": "Enquiry about admission",
    "call_duration": "10 minutes",
    "note": "Interested in Grade 5 admission",
    "follow_up_date": "2025-11-10"
  }'
```

### Example 4: Update Phone Call Log

```bash
curl -X POST "http://localhost/amt/api/generalcall_api/update/1" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "name": "John Doe",
    "contact": "1234567890",
    "date": "2025-11-07",
    "description": "Updated enquiry details",
    "call_duration": "15 minutes",
    "note": "Updated notes",
    "follow_up_date": "2025-11-12"
  }'
```

### Example 5: Delete Phone Call Log

```bash
curl -X POST "http://localhost/amt/api/generalcall_api/delete/1" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

## Complete Workflow: Managing Phone Call Logs

### Step 1: Create a New Phone Call Log

```bash
curl -X POST "http://localhost/amt/api/generalcall_api/add" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "name": "Jane Smith",
    "contact": "9876543210",
    "date": "2025-11-10",
    "description": "Fee payment enquiry",
    "call_duration": "5 minutes",
    "note": "Will call back next week",
    "follow_up_date": "2025-11-15"
  }'
```

**Response:**
```json
{
  "status": 1,
  "message": "Phone call log created successfully",
  "data": {
    "id": "2",
    "name": "Jane Smith",
    "contact": "9876543210",
    "date": "2025-11-10",
    "description": "Fee payment enquiry",
    "call_duration": "5 minutes"
  }
}
```

### Step 2: List All Call Logs

```bash
curl -X POST "http://localhost/amt/api/generalcall_api/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Step 3: Get Specific Call Log

```bash
curl -X POST "http://localhost/amt/api/generalcall_api/detail/2" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Step 4: Update the Call Log

```bash
curl -X POST "http://localhost/amt/api/generalcall_api/update/2" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "name": "Jane Smith",
    "contact": "9876543210",
    "date": "2025-11-10",
    "description": "Fee payment enquiry - Completed",
    "call_duration": "8 minutes",
    "note": "Payment processed successfully",
    "follow_up_date": "2025-11-15"
  }'
```

### Step 5: Delete the Call Log (if needed)

```bash
curl -X POST "http://localhost/amt/api/generalcall_api/delete/2" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

## Use Cases

### 1. **Call Tracking and Management**
   - Record all incoming and outgoing phone calls
   - Track caller information and contact details
   - Maintain call history for reference
   - Generate call reports

### 2. **Follow-up Management**
   - Schedule and track follow-up calls
   - Set reminders for important callbacks
   - Monitor pending follow-ups
   - Track follow-up completion

### 3. **Customer Service**
   - Log customer inquiries and complaints
   - Track call duration for performance metrics
   - Maintain detailed notes about conversations
   - Improve customer service quality

### 4. **Admission and Enrollment**
   - Record admission-related phone calls
   - Track prospective student inquiries
   - Schedule follow-up calls with parents
   - Maintain communication history

### 5. **Administrative Communication**
   - Log administrative phone calls
   - Track communication with vendors and suppliers
   - Maintain records of important conversations
   - Generate communication reports

### 6. **Compliance and Audit**
   - Maintain complete call records
   - Track all phone communications
   - Generate audit trails
   - Export call log data for reporting

---

## Database Schema

### general_call Table

```sql
CREATE TABLE `general_call` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `contact` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `description` text NOT NULL,
  `call_duration` varchar(255) NOT NULL,
  `note` text DEFAULT NULL,
  `follow_up_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

---

## Best Practices

1. **Always provide required fields** (name, contact, date, description, call_duration)
2. **Use proper date format (YYYY-MM-DD)** for all date fields
3. **Record accurate call duration** for performance tracking
4. **Add detailed notes** for important conversations
5. **Set follow-up dates** for calls requiring action
6. **Use consistent contact number format** for better searchability
7. **Update call logs** when follow-up actions are completed
8. **Handle error responses** appropriately in your application
9. **Log all API calls** for audit and debugging purposes
10. **Maintain complete call records** for compliance

---

## Integration with Other APIs

### Admission Enquiry Integration
- Link phone call logs with admission enquiries
- Track phone-based admission inquiries
- Maintain communication history for prospective students

### Visitor Management Integration
- Connect phone calls with visitor records
- Track pre-visit phone communications
- Maintain visitor contact history

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | 2025-11-10 | Initial release with create, read, update, delete, and list endpoints |

---

## Support

For issues or questions regarding this API, please contact the development team or refer to the main API documentation.

