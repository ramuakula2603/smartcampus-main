# Postal Dispatch API Documentation

## Overview

The **Postal Dispatch API** provides RESTful endpoints for managing postal dispatch records in the school management system. This API enables you to create, read, update, and delete postal dispatch records, track outgoing mail and documents, and filter dispatches based on various criteria such as date range, recipient, sender, and reference number.

**Base URL:** `http://localhost/amt/api`

**Authentication:** All endpoints require the following headers:
- `Client-Service: smartschool`
- `Auth-Key: schoolAdmin@`
- `Content-Type: application/json`

---

## ⚠️ Important Notes

**All endpoints use POST method** - Even for retrieving data, you must use POST (not GET).

**URL Format** - Make sure your URL is correct:
- ❌ Wrong: `hhttp://localhost/amt/api/postal-dispatch-api/update/15` (typo: "hhttp")
- ✅ Correct: `http://localhost/amt/api/postal-dispatch-api/update/15`
- Always use `http://` (not `https://` or `hhttp://`)

**URL Placeholders** - When you see `{id}` in the documentation, replace it with an actual number:
- ❌ Wrong: `http://localhost/amt/api/postal-dispatch-api/get/{id}`
- ✅ Correct: `http://localhost/amt/api/postal-dispatch-api/get/1`
- Example: For ID 15, use `/update/15` not `/update/{id}`

**Request Body** - Even if the endpoint doesn't require data, you must send an empty JSON object `{}` in the request body.

**Document Attachments** - The `image` field is used to store document/file paths. File uploads should be handled separately and the file path should be provided in the `image` field.

---

## Endpoints

### 1. ~List All Postal Dispatches~

**Endpoint:** `POST /postal-dispatch-api/list`

**Description:** Retrieves a list of all postal dispatches with optional filtering and search capabilities. This endpoint supports multiple filter combinations and returns comprehensive dispatch data.

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
  "to_title": "Director",
  "from_title": "head office",
  "reference_no": "5462",
  "search": "Director"
}
```

**Request Parameters:**
| Parameter | Type | Required | Description | Example Values |
|-----------|------|----------|-------------|----------------|
| `date_from` | string (YYYY-MM-DD) | No | Filter dispatches from this date (inclusive) | `"2023-01-01"`, `"2025-11-10"` |
| `date_to` | string (YYYY-MM-DD) | No | Filter dispatches up to this date (inclusive) | `"2023-12-31"`, `"2025-11-30"` |
| `to_title` | string | No | Filter by recipient title (partial match) | `"Director"`, `"School Board"` |
| `from_title` | string | No | Filter by sender title (partial match) | `"head office"`, `"School"` |
| `reference_no` | string | No | Filter by reference number (partial match) | `"5462"`, `"51302"` |
| `search` | string | No | General search query that searches across to_title, from_title, reference_no, address, and note fields (case-insensitive partial match) | `"Director"`, `"5462"` |

**Important Notes:**
- All parameters are optional. If no parameters are provided, all dispatches will be returned.
- Multiple filters can be combined. All filters are applied with AND logic.
- The `search` parameter performs a partial match across multiple fields.
- Results are ordered by dispatch ID in descending order (newest first).

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Postal dispatches retrieved successfully",
  "total_records": 10,
  "data": [
    {
      "id": 15,
      "reference_no": "5462",
      "to_title": "Director, Higher Education",
      "address": "",
      "note": "",
      "from_title": "head office",
      "date": "2023-08-31",
      "image": null,
      "type": "dispatch",
      "created_at": "2023-08-11 16:49:00"
    },
    {
      "id": 14,
      "reference_no": "51302",
      "to_title": "School Board Office",
      "address": "",
      "note": "",
      "from_title": "School Board Office",
      "date": "2023-07-31",
      "image": null,
      "type": "dispatch",
      "created_at": "2023-08-11 16:49:00"
    },
    {
      "id": 13,
      "reference_no": "6312",
      "to_title": "Online Class",
      "address": "",
      "note": "",
      "from_title": "Online Class",
      "date": "2023-07-25",
      "image": null,
      "type": "dispatch",
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

### 2. Get Specific Postal Dispatch

**Endpoint:** `POST /postal-dispatch-api/get/{id}`

**Description:** Retrieves details of a specific postal dispatch by its ID.

**⚠️ Important:** 
- **Method must be POST** (not GET)
- Replace `{id}` with an actual dispatch ID number (e.g., use `1` instead of `{id}`)
- Example URL: `http://localhost/amt/api/postal-dispatch-api/get/15`

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**URL Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `id` | integer | Yes | ID of the postal dispatch | `15`, `14`, `13` |

**Request Body:** `{}` (empty JSON object)

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Postal dispatch retrieved successfully",
  "data": {
    "id": 15,
    "reference_no": "5462",
    "to_title": "Director, Higher Education",
    "address": "",
    "note": "",
    "from_title": "head office",
    "date": "2023-08-31",
    "image": null,
    "type": "dispatch",
    "created_at": "2023-08-11 16:49:00"
  }
}
```

**Error Responses:**

**400 Bad Request - Invalid ID:**
```json
{
  "status": 0,
  "message": "Invalid or missing postal dispatch ID",
  "data": null
}
```

**404 Not Found:**
```json
{
  "status": 0,
  "message": "Postal dispatch not found",
  "data": null
}
```

---

### 3. Create New Postal Dispatch

**Endpoint:** `POST /postal-dispatch-api/create`

**Description:** Creates a new postal dispatch record.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Request Body:**
```json
{
  "to_title": "Director, Higher Education",
  "reference_no": "5462",
  "address": "123 Education Street, City",
  "note": "Important documents",
  "from_title": "head office",
  "date": "2025-11-10",
  "image": "uploads/front_office/dispatch_receive/document.pdf"
}
```

**Request Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `to_title` | string | Yes | Recipient title/name | `"Director, Higher Education"`, `"School Board Office"` |
| `date` | string (YYYY-MM-DD) | Yes | Dispatch date | `"2025-11-10"`, `"2023-08-31"` |
| `reference_no` | string | No | Reference number for the dispatch | `"5462"`, `"51302"` |
| `address` | string | No | Recipient address | `"123 Education Street, City"` |
| `note` | string | No | Additional notes | `"Important documents"` |
| `from_title` | string | No | Sender title/name | `"head office"`, `"School Board Office"` |
| `image` | string | No | File path or URL of attached document | `"uploads/front_office/dispatch_receive/document.pdf"` |

**Success Response (201 Created):**
```json
{
  "status": 1,
  "message": "Postal dispatch created successfully",
  "data": {
    "id": 16,
    "reference_no": "5462",
    "to_title": "Director, Higher Education",
    "address": "123 Education Street, City",
    "note": "Important documents",
    "from_title": "head office",
    "date": "2025-11-10",
    "image": "uploads/front_office/dispatch_receive/document.pdf",
    "type": "dispatch",
    "created_at": "2025-11-10 10:30:00"
  }
}
```

**Error Responses:**

**400 Bad Request - Missing Required Field:**
```json
{
  "status": 0,
  "message": "To Title is required",
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

### 4. Update Postal Dispatch

**Endpoint:** `POST /postal-dispatch-api/update/{id}`

**Description:** Updates an existing postal dispatch record.

**⚠️ Important:** 
- **Method must be POST** (not PUT or PATCH)
- **URL must start with `http://`** (not `hhttp://` or `https://`)
- **Replace `{id}` with an actual dispatch ID number** (e.g., use `15` instead of `{id}`)
- **Correct Example URL:** `http://localhost/amt/api/postal-dispatch-api/update/15`
-
- You must have a valid dispatch ID. Use the list endpoint first to get existing dispatch IDs.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**URL Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `id` | integer | Yes | ID of the postal dispatch to update | `15`, `14`, `13` |

**Request Body:**
```json
{
  "to_title": "Director, Higher Education - Updated",
  "reference_no": "5462",
  "address": "123 Education Street, City - Updated",
  "note": "Important documents - Updated",
  "from_title": "head office",
  "date": "2025-11-10",
  "image": "uploads/front_office/dispatch_receive/document_updated.pdf"
}
```

**Request Parameters:** (Same as Create endpoint)

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Postal dispatch updated successfully",
  "data": {
    "id": 15,
    "reference_no": "5462",
    "to_title": "Director, Higher Education - Updated",
    "address": "123 Education Street, City - Updated",
    "note": "Important documents - Updated",
    "from_title": "head office",
    "date": "2025-11-10",
    "image": "uploads/front_office/dispatch_receive/document_updated.pdf",
    "type": "dispatch",
    "created_at": "2023-08-11 16:49:00"
  }
}
```

**Error Responses:**

**400 Bad Request - Invalid ID:**
```json
{
  "status": 0,
  "message": "Invalid or missing postal dispatch ID",
  "data": null
}
```

**404 Not Found - Dispatch doesn't exist:**
```json
{
  "status": 0,
  "message": "Postal dispatch not found",
  "data": null
}
```

---

### 5. Delete Postal Dispatch

**Endpoint:** `POST /postal-dispatch-api/delete/{id}`

**Description:** Deletes a postal dispatch record.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**URL Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `id` | integer | Yes | ID of the postal dispatch to delete | `15`, `14`, `13` |

**Request Body:** `{}` (empty JSON object)

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Postal dispatch deleted successfully",
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
  "message": "Invalid or missing postal dispatch ID",
  "data": null
}
```

**404 Not Found:**
```json
{
  "status": 0,
  "message": "Postal dispatch not found",
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
| 404 | Not Found | Resource not found (dispatch) |
| 405 | Method Not Allowed | Wrong HTTP method used (must be POST) |
| 500 | Internal Server Error | Server error occurred |

---

## cURL Examples

### Example 1: List All Postal Dispatches

```bash
curl -X POST "http://localhost/amt/api/postal-dispatch-api/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "date_from": "2023-01-01",
    "date_to": "2023-12-31"
  }'
```

### Example 2: Get Specific Postal Dispatch

```bash
curl -X POST "http://localhost/amt/api/postal-dispatch-api/get/15" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 3: Create New Postal Dispatch

```bash
curl -X POST "http://localhost/amt/api/postal-dispatch-api/create" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "to_title": "Director, Higher Education",
    "reference_no": "5462",
    "address": "123 Education Street, City",
    "note": "Important documents",
    "from_title": "head office",
    "date": "2025-11-10",
    "image": "uploads/front_office/dispatch_receive/document.pdf"
  }'
```

### Example 4: Update Postal Dispatch

```bash
curl -X POST "http://localhost/amt/api/postal-dispatch-api/update/15" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "to_title": "Director, Higher Education - Updated",
    "reference_no": "5462",
    "address": "123 Education Street, City - Updated",
    "note": "Important documents - Updated",
    "from_title": "head office",
    "date": "2025-11-10",
    "image": "uploads/front_office/dispatch_receive/document_updated.pdf"
  }'
```

### Example 5: Delete Postal Dispatch

```bash
curl -X POST "http://localhost/amt/api/postal-dispatch-api/delete/15" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

## Complete Workflow: Managing Postal Dispatches

### Step 1: Create a New Postal Dispatch

```bash
curl -X POST "http://localhost/amt/api/postal-dispatch-api/create" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "to_title": "School Board Office",
    "reference_no": "51302",
    "address": "456 Board Street",
    "note": "Quarterly report submission",
    "from_title": "School Board Office",
    "date": "2025-11-10",
    "image": null
  }'
```

**Response:**
```json
{
  "status": 1,
  "message": "Postal dispatch created successfully",
  "data": {
    "id": 16,
    "to_title": "School Board Office",
    "reference_no": "51302",
    "date": "2025-11-10",
    "type": "dispatch"
  }
}
```

### Step 2: List All Dispatches

```bash
curl -X POST "http://localhost/amt/api/postal-dispatch-api/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Step 3: Get Specific Dispatch

```bash
curl -X POST "http://localhost/amt/api/postal-dispatch-api/get/16" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Step 4: Update the Dispatch

```bash
curl -X POST "http://localhost/amt/api/postal-dispatch-api/update/16" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "to_title": "School Board Office",
    "reference_no": "51302",
    "address": "456 Board Street",
    "note": "Quarterly report submission - Dispatched",
    "from_title": "School Board Office",
    "date": "2025-11-10",
    "image": null
  }'
```

### Step 5: Delete the Dispatch (if needed)

```bash
curl -X POST "http://localhost/amt/api/postal-dispatch-api/delete/16" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

## Use Cases

### 1. **Outgoing Mail Management**
   - Record all outgoing postal dispatches
   - Track reference numbers for correspondence
   - Maintain dispatch history
   - Generate dispatch reports

### 2. **Document Tracking**
   - Track important documents sent out
   - Maintain reference numbers for audit purposes
   - Link documents to dispatches via image field
   - Monitor document dispatch dates

### 3. **Correspondence Management**
   - Record communications with external parties
   - Track recipient and sender information
   - Maintain address records
   - Store notes about dispatches

### 4. **Compliance and Audit**
   - Maintain complete dispatch records
   - Track all outgoing communications
   - Generate audit trails
   - Export dispatch data for reporting

### 5. **Daily Dispatch Reports**
   - Generate daily dispatch lists
   - Filter by date range
   - Search by recipient or reference number
   - Export dispatch data

### 6. **Reference Number Management**
   - Track unique reference numbers
   - Search dispatches by reference
   - Maintain reference number sequences
   - Link related dispatches

---

## Database Schema

### dispatch_receive Table

```sql
CREATE TABLE `dispatch_receive` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reference_no` varchar(255) DEFAULT NULL,
  `to_title` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `note` text DEFAULT NULL,
  `from_title` varchar(255) DEFAULT NULL,
  `date` date NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `type` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

**Note:** The `type` field is set to `'dispatch'` for all postal dispatch records. The same table is used for both dispatch and receive records, differentiated by the `type` field.

---

## Best Practices

1. **Always provide to_title** as it is a required field
2. **Use proper date format (YYYY-MM-DD)** for all date fields
3. **Use unique reference numbers** for better tracking
4. **Use the list endpoint with filters** to efficiently retrieve specific dispatches
5. **Store document paths** in the image field after uploading files
6. **Use search functionality** to quickly find dispatches by recipient, sender, or reference number
7. **Maintain complete address information** for proper record keeping
8. **Handle error responses** appropriately in your application
9. **Log all API calls** for audit and debugging purposes
10. **Update records** when dispatch status changes

---

## Integration with Other APIs

### File Upload
- File uploads should be handled separately
- After successful upload, provide the file path in the `image` field
- File paths should be relative to the uploads directory

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | 2025-11-10 | Initial release with create, read, update, delete, and list endpoints |

---

## Support

For issues or questions regarding this API, please contact the development team or refer to the main API documentation.

