# Postal Receive API Documentation

## Overview

The **Postal Receive API** provides RESTful endpoints for managing postal receive records in the school management system. This API enables you to create, read, update, and delete postal receive records, track incoming mail and documents, and filter receives based on various criteria such as date range, sender, recipient, and reference number.

**Base URL:** `http://localhost/amt/api`

**Authentication:** All endpoints require the following headers:
- `Client-Service: smartschool`
- `Auth-Key: schoolAdmin@`
- `Content-Type: application/json`

---

## ⚠️ Important Notes

**All endpoints use POST method** - Even for retrieving data, you must use POST (not GET).

**URL Format** - Make sure your URL is correct:
- ❌ Wrong: `hhttp://localhost/amt/api/postal-receive-api/update/15` (typo: "hhttp")
- ✅ Correct: `http://localhost/amt/api/postal-receive-api/update/15`
- Always use `http://` (not `https://` or `hhttp://`)

**URL Placeholders** - When you see `{id}` in the documentation, replace it with an actual number:
- ❌ Wrong: `http://localhost/amt/api/postal-receive-api/get/{id}`
- ✅ Correct: `http://localhost/amt/api/postal-receive-api/get/1`
- Example: For ID 15, use `/update/15` not `/update/{id}`

**Request Body** - Even if the endpoint doesn't require data, you must send an empty JSON object `{}` in the request body.

**Document Attachments** - The `image` field is used to store document/file paths. File uploads should be handled separately and the file path should be provided in the `image` field.

---

## Endpoints

### 1. List All Postal Receives

**Endpoint:** `POST /postal-receive-api/list`

**Description:** Retrieves a list of all postal receives with optional filtering and search capabilities. This endpoint supports multiple filter combinations and returns comprehensive receive data.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Request Body:**
```json
{
  "date_from": "2023-01-01",
  "date_to": "2023-12-31",
  "to_title": "Amaravathi School",
  "from_title": "Online Education",
  "reference_no": "1551",
  "search": "Online Education"
}
```

**Request Parameters:**
| Parameter | Type | Required | Description | Example Values |
|-----------|------|----------|-------------|----------------|
| `date_from` | string (YYYY-MM-DD) | No | Filter receives from this date (inclusive) | `"2023-01-01"`, `"2025-11-10"` |
| `date_to` | string (YYYY-MM-DD) | No | Filter receives up to this date (inclusive) | `"2023-12-31"`, `"2025-11-30"` |
| `to_title` | string | No | Filter by recipient title (partial match) | `"Amaravathi School"`, `"government school"` |
| `from_title` | string | No | Filter by sender title (partial match) | `"Online Education"`, `"CBSE Book Publication"` |
| `reference_no` | string | No | Filter by reference number (partial match) | `"1551"`, `"51561"` |
| `search` | string | No | General search query that searches across to_title, from_title, reference_no, address, and note fields (case-insensitive partial match) | `"Online Education"`, `"1551"` |

**Important Notes:**
- All parameters are optional. If no parameters are provided, all receives will be returned.
- Multiple filters can be combined. All filters are applied with AND logic.
- The `search` parameter performs a partial match across multiple fields.
- Results are ordered by receive ID in descending order (newest first).

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Postal receives retrieved successfully",
  "total_records": 10,
  "data": [
    {
      "id": 15,
      "reference_no": "1551",
      "from_title": "Online Education",
      "address": "",
      "note": "",
      "to_title": "Online Education",
      "date": "2023-07-01",
      "image": null,
      "type": "receive",
      "created_at": "2023-08-11 16:49:00"
    },
    {
      "id": 14,
      "reference_no": "51561",
      "from_title": "Sports Camp",
      "address": "",
      "note": "",
      "to_title": "Sports Camp",
      "date": "2023-07-05",
      "image": null,
      "type": "receive",
      "created_at": "2023-08-11 16:49:00"
    },
    {
      "id": 13,
      "reference_no": "5152",
      "from_title": "Health Checkup",
      "address": "",
      "note": "",
      "to_title": "Health Checkup",
      "date": "2023-07-10",
      "image": null,
      "type": "receive",
      "created_at": "2023-08-11 16:49:00"
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

### 2. Get Specific Postal Receive

**Endpoint:** `POST /postal-receive-api/get/{id}`

**Description:** Retrieves details of a specific postal receive by its ID.

**⚠️ Important:** 
- **Method must be POST** (not GET)
- Replace `{id}` with an actual receive ID number (e.g., use `1` instead of `{id}`)
- Example URL: `http://localhost/amt/api/postal-receive-api/get/15`

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**URL Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `id` | integer | Yes | ID of the postal receive | `15`, `14`, `13` |

**Request Body:** `{}` (empty JSON object)

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Postal receive retrieved successfully",
  "data": {
    "id": 15,
    "reference_no": "1551",
    "from_title": "Online Education",
    "address": "",
    "note": "",
    "to_title": "Online Education",
    "date": "2023-07-01",
    "image": null,
    "type": "receive",
    "created_at": "2023-08-11 16:49:00"
  }
}
```

**Error Responses:**

**400 Bad Request - Invalid ID:**
```json
{
  "status": 0,
  "message": "Invalid or missing postal receive ID",
  "data": null
}
```

**404 Not Found:**
```json
{
  "status": 0,
  "message": "Postal receive not found",
  "data": null
}
```

---

### 3. Create New Postal Receive

**Endpoint:** `POST /postal-receive-api/create`

**Description:** Creates a new postal receive record.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Request Body:**
```json
{
  "from_title": "Online Education",
  "reference_no": "1551",
  "address": "123 Education Street, City",
  "note": "Important documents received",
  "to_title": "Amaravathi School",
  "date": "2025-11-12",
  "image": "uploads/front_office/dispatch_receive/document.pdf"
}
```

**Request Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `from_title` | string | Yes | Sender title/name | `"Online Education"`, `"CBSE Book Publication"` |
| `date` | string (YYYY-MM-DD) | Yes | Receive date | `"2025-11-12"`, `"2023-07-01"` |
| `reference_no` | string | No | Reference number for the receive | `"1551"`, `"51561"` |
| `address` | string | No | Sender address | `"123 Education Street, City"` |
| `note` | string | No | Additional notes | `"Important documents received"` |
| `to_title` | string | No | Recipient title/name (usually the school) | `"Amaravathi School"`, `"government school"` |
| `image` | string | No | File path or URL of attached document | `"uploads/front_office/dispatch_receive/document.pdf"` |

**Success Response (201 Created):**
```json
{
  "status": 1,
  "message": "Postal receive created successfully",
  "data": {
    "id": 16,
    "reference_no": "1551",
    "from_title": "Online Education",
    "address": "123 Education Street, City",
    "note": "Important documents received",
    "to_title": "Amaravathi School",
    "date": "2025-11-12",
    "image": "uploads/front_office/dispatch_receive/document.pdf",
    "type": "receive",
    "created_at": "2025-11-12 10:30:00"
  }
}
```

**Error Responses:**

**400 Bad Request - Missing Required Field:**
```json
{
  "status": 0,
  "message": "From Title is required",
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

### 4. Update Postal Receive

**Endpoint:** `POST /postal-receive-api/update/{id}`

**Description:** Updates an existing postal receive record.

**⚠️ Important:** 
- **Method must be POST** (not PUT or PATCH)
- **URL must start with `http://`** (not `hhttp://` or `https://`)
- **Replace `{id}` with an actual receive ID number** (e.g., use `15` instead of `{id}`)
- **Correct Example URL:** `http://localhost/amt/api/postal-receive-api/update/15`
- You must have a valid receive ID. Use the list endpoint first to get existing receive IDs.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**URL Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `id` | integer | Yes | ID of the postal receive to update | `15`, `14`, `13` |

**Request Body:**
```json
{
  "from_title": "Online Education - Updated",
  "reference_no": "1551",
  "address": "123 Education Street, City - Updated",
  "note": "Important documents received - Updated",
  "to_title": "Amaravathi School",
  "date": "2025-11-12",
  "image": "uploads/front_office/dispatch_receive/document_updated.pdf"
}
```

**Request Parameters:** (Same as Create endpoint)

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Postal receive updated successfully",
  "data": {
    "id": 15,
    "reference_no": "1551",
    "from_title": "Online Education - Updated",
    "address": "123 Education Street, City - Updated",
    "note": "Important documents received - Updated",
    "to_title": "Amaravathi School",
    "date": "2025-11-12",
    "image": "uploads/front_office/dispatch_receive/document_updated.pdf",
    "type": "receive",
    "created_at": "2023-08-11 16:49:00"
  }
}
```

**Error Responses:**

**400 Bad Request - Invalid ID:**
```json
{
  "status": 0,
  "message": "Invalid or missing postal receive ID",
  "data": null
}
```

**404 Not Found - Receive doesn't exist:**
```json
{
  "status": 0,
  "message": "Postal receive not found",
  "data": null
}
```

---

### 5. Delete Postal Receive

**Endpoint:** `POST /postal-receive-api/delete/{id}`

**Description:** Deletes a postal receive record.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**URL Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `id` | integer | Yes | ID of the postal receive to delete | `15`, `14`, `13` |

**Request Body:** `{}` (empty JSON object)

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Postal receive deleted successfully",
  "data": {
    "id": 15
  }
}
```

**Error Responses:**

**400 Bad Request - Invalid ID:**
```json
{
  "status": 0,
  "message": "Invalid or missing postal receive ID",
  "data": null
}
```

**404 Not Found:**
```json
{
  "status": 0,
  "message": "Postal receive not found",
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
| 404 | Not Found | Resource not found (receive) |
| 405 | Method Not Allowed | Wrong HTTP method used (must be POST) |
| 500 | Internal Server Error | Server error occurred |

---

## cURL Examples

### Example 1: List All Postal Receives

```bash
curl -X POST "http://localhost/amt/api/postal-receive-api/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "date_from": "2023-01-01",
    "date_to": "2023-12-31"
  }'
```

### Example 2: Get Specific Postal Receive

```bash
curl -X POST "http://localhost/amt/api/postal-receive-api/get/15" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 3: Create New Postal Receive

```bash
curl -X POST "http://localhost/amt/api/postal-receive-api/create" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "from_title": "Online Education",
    "reference_no": "1551",
    "address": "123 Education Street, City",
    "note": "Important documents received",
    "to_title": "Amaravathi School",
    "date": "2025-11-12",
    "image": "uploads/front_office/dispatch_receive/document.pdf"
  }'
```

### Example 4: Update Postal Receive

```bash
curl -X POST "http://localhost/amt/api/postal-receive-api/update/15" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "from_title": "Online Education - Updated",
    "reference_no": "1551",
    "address": "123 Education Street, City - Updated",
    "note": "Important documents received - Updated",
    "to_title": "Amaravathi School",
    "date": "2025-11-12",
    "image": "uploads/front_office/dispatch_receive/document_updated.pdf"
  }'
```

### Example 5: Delete Postal Receive

```bash
curl -X POST "http://localhost/amt/api/postal-receive-api/delete/15" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

## Complete Workflow: Managing Postal Receives

### Step 1: Create a New Postal Receive

```bash
curl -X POST "http://localhost/amt/api/postal-receive-api/create" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "from_title": "CBSE Book Publication",
    "reference_no": "5620",
    "address": "456 Publication Avenue",
    "note": "Textbooks received for new academic year",
    "to_title": "Amaravathi School",
    "date": "2025-11-12",
    "image": null
  }'
```

**Response:**
```json
{
  "status": 1,
  "message": "Postal receive created successfully",
  "data": {
    "id": 16,
    "from_title": "CBSE Book Publication",
    "reference_no": "5620",
    "date": "2025-11-12",
    "type": "receive"
  }
}
```

### Step 2: List All Receives

```bash
curl -X POST "http://localhost/amt/api/postal-receive-api/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Step 3: Get Specific Receive

```bash
curl -X POST "http://localhost/amt/api/postal-receive-api/get/16" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Step 4: Update the Receive

```bash
curl -X POST "http://localhost/amt/api/postal-receive-api/update/16" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "from_title": "CBSE Book Publication",
    "reference_no": "5620",
    "address": "456 Publication Avenue",
    "note": "Textbooks received and distributed",
    "to_title": "Amaravathi School",
    "date": "2025-11-12",
    "image": null
  }'
```

### Step 5: Delete the Receive (if needed)

```bash
curl -X POST "http://localhost/amt/api/postal-receive-api/delete/16" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

## Use Cases

### 1. **Incoming Mail Management**
   - Record all incoming postal receives
   - Track sender information and contact details
   - Maintain receive history for reference
   - Generate receive reports

### 2. **Document Tracking**
   - Track important documents received
   - Maintain reference numbers for audit purposes
   - Link documents to receives via image field
   - Monitor document receive dates

### 3. **Correspondence Management**
   - Record communications from external parties
   - Track sender and recipient information
   - Maintain address records
   - Store notes about receives

### 4. **Compliance and Audit**
   - Maintain complete receive records
   - Track all incoming communications
   - Generate audit trails
   - Export receive data for reporting

### 5. **Daily Receive Reports**
   - Generate daily receive lists
   - Filter by date range
   - Search by sender or reference number
   - Export receive data

### 6. **Reference Number Management**
   - Track unique reference numbers
   - Search receives by reference
   - Maintain reference number sequences
   - Link related receives

---

## Database Schema

### dispatch_receive Table

```sql
CREATE TABLE `dispatch_receive` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reference_no` varchar(255) DEFAULT NULL,
  `to_title` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `note` text DEFAULT NULL,
  `from_title` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `type` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

**Note:** The `type` field is set to `'receive'` for all postal receive records. The same table is used for both dispatch and receive records, differentiated by the `type` field.

---

## Best Practices

1. **Always provide from_title** as it is a required field
2. **Use proper date format (YYYY-MM-DD)** for all date fields
3. **Use unique reference numbers** for better tracking
4. **Use the list endpoint with filters** to efficiently retrieve specific receives
5. **Store document paths** in the image field after uploading files
6. **Use search functionality** to quickly find receives by sender, recipient, or reference number
7. **Maintain complete address information** for proper record keeping
8. **Handle error responses** appropriately in your application
9. **Log all API calls** for audit and debugging purposes
10. **Update records** when receive status changes

---

## Integration with Other APIs

### File Upload
- File uploads should be handled separately
- After successful upload, provide the file path in the `image` field
- File paths should be relative to the uploads directory

### Postal Dispatch Integration
- Link related dispatch and receive records
- Track correspondence chains
- Maintain complete communication history

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | 2025-11-12 | Initial release with create, read, update, delete, and list endpoints |

---

## Support

For issues or questions regarding this API, please contact the development team or refer to the main API documentation.

