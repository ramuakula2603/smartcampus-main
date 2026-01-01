# Complaint API Documentation

## Overview

The **Complaint API** provides RESTful endpoints for managing complaint records in the school management system. This API enables you to create, read, update, and delete complaint records, track complaints based on various criteria such as date range, complaint type, source, name, and contact number, and retrieve available complaint types and sources.

**Base URL:** `http://localhost/amt/api`

**Authentication:** All endpoints require the following headers:
- `Client-Service: smartschool`
- `Auth-Key: schoolAdmin@`
- `Content-Type: application/json`

---

## ⚠️ Important Notes

**All endpoints use POST method** - Even for retrieving data, you must use POST (not GET).

**URL Format** - Make sure your URL is correct:
- ❌ Wrong: `hhttp://localhost/amt/api/complaint-api/update/15` (typo: "hhttp")
- ✅ Correct: `http://localhost/amt/api/complaint-api/update/15`
- Always use `http://` (not `https://` or `hhttp://`)

**URL Placeholders** - When you see `{id}` in the documentation, replace it with an actual number:
- ❌ Wrong: `http://localhost/amt/api/complaint-api/get/{id}`
- ✅ Correct: `http://localhost/amt/api/complaint-api/get/1`
- Example: For ID 15, use `/update/15` not `/update/{id}`

**Request Body** - Even if the endpoint doesn't require data, you must send an empty JSON object `{}` in the request body.

**Document Attachments** - The `image` field is used to store document/file paths. File uploads should be handled separately and the file path should be provided in the `image` field.

---

## Endpoints

### 1. List All Complaints

**Endpoint:** `POST /complaint-api/list`

**Description:** Retrieves a list of all complaints with optional filtering and search capabilities. This endpoint supports multiple filter combinations and returns comprehensive complaint data.

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
  "complaint_type": "General",
  "source": "Phone",
  "name": "John Doe",
  "contact": "1234567890",
  "search": "General"
}
```

**Request Parameters:**
| Parameter | Type | Required | Description | Example Values |
|-----------|------|----------|-------------|----------------|
| `date_from` | string (YYYY-MM-DD) | No | Filter complaints from this date (inclusive) | `"2023-01-01"`, `"2025-11-10"` |
| `date_to` | string (YYYY-MM-DD) | No | Filter complaints up to this date (inclusive) | `"2023-12-31"`, `"2025-11-30"` |
| `complaint_type` | string | No | Filter by complaint type (partial match) | `"General"`, `"Fees"`, `"Sports"` |
| `source` | string | No | Filter by source (partial match) | `"Phone"`, `"Email"`, `"In Person"` |
| `name` | string | No | Filter by complainant name (partial match) | `"John Doe"`, `"Jane Smith"` |
| `contact` | string | No | Filter by contact number (partial match) | `"1234567890"`, `"9876543210"` |
| `search` | string | No | General search query that searches across complaint_type, source, name, contact, description, action_taken, assigned, and note fields (case-insensitive partial match) | `"General"`, `"John"` |

**Important Notes:**
- All parameters are optional. If no parameters are provided, all complaints will be returned.
- Multiple filters can be combined. All filters are applied with AND logic.
- The `search` parameter performs a partial match across multiple fields.
- Results are ordered by complaint ID in descending order (newest first).

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Complaints retrieved successfully",
  "total_records": 10,
  "data": [
    {
      "id": 22,
      "complaint_type": "General",
      "source": "Phone",
      "name": "John Doe",
      "contact": "1234567890",
      "date": "2025-09-03",
      "description": "Complaint description here",
      "action_taken": "Investigation initiated",
      "assigned": "Admin Staff",
      "note": "Follow up required",
      "image": "uploads/front_office/complaints/document.pdf",
      "created_at": "2025-09-03 10:30:00"
    },
    {
      "id": 21,
      "complaint_type": "Fees",
      "source": "Email",
      "name": "Jane Smith",
      "contact": "9876543210",
      "date": "2025-09-03",
      "description": "Fee related issue",
      "action_taken": "Resolved",
      "assigned": "Finance Team",
      "note": "Issue resolved",
      "image": null,
      "created_at": "2025-09-03 09:15:00"
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

### 2. Get Specific Complaint

**Endpoint:** `POST /complaint-api/get/{id}`

**Description:** Retrieves details of a specific complaint by its ID.

**⚠️ Important:** 
- **Method must be POST** (not GET)
- Replace `{id}` with an actual complaint ID number (e.g., use `1` instead of `{id}`)
- Example URL: `http://localhost/amt/api/complaint-api/get/22`

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**URL Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `id` | integer | Yes | ID of the complaint | `22`, `21`, `11` |

**Request Body:** `{}` (empty JSON object)

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Complaint retrieved successfully",
  "data": {
    "id": 22,
    "complaint_type": "General",
    "source": "Phone",
    "name": "John Doe",
    "contact": "1234567890",
    "date": "2025-09-03",
    "description": "Complaint description here",
    "action_taken": "Investigation initiated",
    "assigned": "Admin Staff",
    "note": "Follow up required",
    "image": "uploads/front_office/complaints/document.pdf",
    "created_at": "2025-09-03 10:30:00"
  }
}
```

**Error Responses:**

**400 Bad Request - Invalid ID:**
```json
{
  "status": 0,
  "message": "Invalid or missing complaint ID",
  "data": null
}
```

**404 Not Found:**
```json
{
  "status": 0,
  "message": "Complaint not found",
  "data": null
}
```

---

### 3. Create New Complaint

**Endpoint:** `POST /complaint-api/create`

**Description:** Creates a new complaint record.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Request Body:**
```json
{
  "complaint_type": "General",
  "source": "Phone",
  "name": "John Doe",
  "contact": "1234567890",
  "date": "2025-11-13",
  "description": "Complaint description here",
  "action_taken": "Investigation initiated",
  "assigned": "Admin Staff",
  "note": "Follow up required",
  "image": "uploads/front_office/complaints/document.pdf"
}
```

**Request Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `name` | string | Yes | Name of the person making the complaint (Complain By) | `"John Doe"`, `"Jane Smith"` |
| `date` | string (YYYY-MM-DD) | Yes | Date of the complaint | `"2025-11-13"`, `"2025-09-03"` |
| `complaint_type` | string | No | Type of complaint | `"General"`, `"Fees"`, `"Sports"`, `"Transport"`, `"Hostel"`, `"Front Office"`, `"Study"`, `"Teacher"` |
| `source` | string | No | Source of the complaint | `"Phone"`, `"Email"`, `"In Person"`, `"Online"` |
| `contact` | string | No | Contact number of the complainant | `"1234567890"`, `"9876543210"` |
| `description` | string | No | Detailed description of the complaint | `"Complaint description here"` |
| `action_taken` | string | No | Action taken on the complaint | `"Investigation initiated"`, `"Resolved"` |
| `assigned` | string | No | Person or department assigned to handle the complaint | `"Admin Staff"`, `"Finance Team"` |
| `note` | string | No | Additional notes about the complaint | `"Follow up required"` |
| `image` | string | No | File path or URL of attached document | `"uploads/front_office/complaints/document.pdf"` |

**Success Response (201 Created):**
```json
{
  "status": 1,
  "message": "Complaint created successfully",
  "data": {
    "id": 23,
    "complaint_type": "General",
    "source": "Phone",
    "name": "John Doe",
    "contact": "1234567890",
    "date": "2025-11-13",
    "description": "Complaint description here",
    "action_taken": "Investigation initiated",
    "assigned": "Admin Staff",
    "note": "Follow up required",
    "image": "uploads/front_office/complaints/document.pdf",
    "created_at": "2025-11-13 10:30:00"
  }
}
```

**Error Responses:**

**400 Bad Request - Missing Required Field:**
```json
{
  "status": 0,
  "message": "Name (Complain By) is required",
  "data": null
}
```

**400 Bad Request - Missing Date:**
```json
{
  "status": 0,
  "message": "Date is required",
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

### 4. Update Complaint

**Endpoint:** `POST /complaint-api/update/{id}`

**Description:** Updates an existing complaint record.

**⚠️ Important:** 
- **Method must be POST** (not PUT or PATCH)
- **URL must start with `http://`** (not `hhttp://` or `https://`)
- **Replace `{id}` with an actual complaint ID number** (e.g., use `22` instead of `{id}`)
- **Correct Example URL:** `http://localhost/amt/api/complaint-api/update/22`
- You must have a valid complaint ID. Use the list endpoint first to get existing complaint IDs.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**URL Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `id` | integer | Yes | ID of the complaint to update | `22`, `21`, `11` |

**Request Body:**
```json
{
  "complaint_type": "General",
  "source": "Phone",
  "name": "John Doe",
  "contact": "1234567890",
  "date": "2025-11-13",
  "description": "Updated complaint description",
  "action_taken": "Resolved",
  "assigned": "Admin Staff",
  "note": "Issue resolved",
  "image": "uploads/front_office/complaints/document_updated.pdf"
}
```

**Request Parameters:** (Same as Create endpoint)

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Complaint updated successfully",
  "data": {
    "id": 22,
    "complaint_type": "General",
    "source": "Phone",
    "name": "John Doe",
    "contact": "1234567890",
    "date": "2025-11-13",
    "description": "Updated complaint description",
    "action_taken": "Resolved",
    "assigned": "Admin Staff",
    "note": "Issue resolved",
    "image": "uploads/front_office/complaints/document_updated.pdf",
    "created_at": "2025-09-03 10:30:00"
  }
}
```

**Error Responses:**

**400 Bad Request - Invalid ID:**
```json
{
  "status": 0,
  "message": "Invalid or missing complaint ID",
  "data": null
}
```

**404 Not Found - Complaint doesn't exist:**
```json
{
  "status": 0,
  "message": "Complaint not found",
  "data": null
}
```

---

### 5. Delete Complaint

**Endpoint:** `POST /complaint-api/delete/{id}`

**Description:** Deletes a complaint record.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**URL Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `id` | integer | Yes | ID of the complaint to delete | `22`, `21`, `11` |

**Request Body:** `{}` (empty JSON object)

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Complaint deleted successfully",
  "data": {
    "id": 22
  }
}
```

**Error Responses:**

**400 Bad Request - Invalid ID:**
```json
{
  "status": 0,
  "message": "Invalid or missing complaint ID",
  "data": null
}
```

**404 Not Found:**
```json
{
  "status": 0,
  "message": "Complaint not found",
  "data": null
}
```

---

### 6. Get Complaint Types

**Endpoint:** `POST /complaint-api/types`

**Description:** Retrieves a list of all available complaint types that can be used when creating or updating complaints.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Request Body:** `{}` (empty JSON object)

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Complaint types retrieved successfully",
  "total_records": 8,
  "data": [
    {
      "id": 1,
      "complaint_type": "General"
    },
    {
      "id": 2,
      "complaint_type": "Fees"
    },
    {
      "id": 3,
      "complaint_type": "Sports"
    },
    {
      "id": 4,
      "complaint_type": "Transport"
    },
    {
      "id": 5,
      "complaint_type": "Hostel"
    },
    {
      "id": 6,
      "complaint_type": "Front Office"
    },
    {
      "id": 7,
      "complaint_type": "Study"
    },
    {
      "id": 8,
      "complaint_type": "Teacher"
    }
  ]
}
```

---

### 7. Get Complaint Sources

**Endpoint:** `POST /complaint-api/sources`

**Description:** Retrieves a list of all available complaint sources that can be used when creating or updating complaints.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Request Body:** `{}` (empty JSON object)

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Complaint sources retrieved successfully",
  "total_records": 4,
  "data": [
    {
      "id": 1,
      "source": "Phone"
    },
    {
      "id": 2,
      "source": "Email"
    },
    {
      "id": 3,
      "source": "In Person"
    },
    {
      "id": 4,
      "source": "Online"
    }
  ]
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
| 404 | Not Found | Resource not found (complaint) |
| 405 | Method Not Allowed | Wrong HTTP method used (must be POST) |
| 500 | Internal Server Error | Server error occurred |

---

## cURL Examples

### Example 1: List All Complaints

```bash
curl -X POST "http://localhost/amt/api/complaint-api/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "date_from": "2023-01-01",
    "date_to": "2023-12-31"
  }'
```

### Example 2: Get Specific Complaint

```bash
curl -X POST "http://localhost/amt/api/complaint-api/get/22" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 3: Create New Complaint

```bash
curl -X POST "http://localhost/amt/api/complaint-api/create" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "complaint_type": "General",
    "source": "Phone",
    "name": "John Doe",
    "contact": "1234567890",
    "date": "2025-11-13",
    "description": "Complaint description here",
    "action_taken": "Investigation initiated",
    "assigned": "Admin Staff",
    "note": "Follow up required",
    "image": "uploads/front_office/complaints/document.pdf"
  }'
```

### Example 4: Update Complaint

```bash
curl -X POST "http://localhost/amt/api/complaint-api/update/22" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "complaint_type": "General",
    "source": "Phone",
    "name": "John Doe",
    "contact": "1234567890",
    "date": "2025-11-13",
    "description": "Updated complaint description",
    "action_taken": "Resolved",
    "assigned": "Admin Staff",
    "note": "Issue resolved",
    "image": "uploads/front_office/complaints/document_updated.pdf"
  }'
```

### Example 5: Delete Complaint

```bash
curl -X POST "http://localhost/amt/api/complaint-api/delete/22" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 6: Get Complaint Types

```bash
curl -X POST "http://localhost/amt/api/complaint-api/types" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 7: Get Complaint Sources

```bash
curl -X POST "http://localhost/amt/api/complaint-api/sources" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

## Complete Workflow: Managing Complaints

### Step 1: Get Available Complaint Types and Sources

```bash
# Get complaint types
curl -X POST "http://localhost/amt/api/complaint-api/types" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'

# Get complaint sources
curl -X POST "http://localhost/amt/api/complaint-api/sources" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Response:**
```json
{
  "status": 1,
  "message": "Complaint types retrieved successfully",
  "data": [
    {"id": 1, "complaint_type": "General"},
    {"id": 2, "complaint_type": "Fees"}
  ]
}
```

### Step 2: Create a New Complaint

```bash
curl -X POST "http://localhost/amt/api/complaint-api/create" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "complaint_type": "General",
    "source": "Phone",
    "name": "John Doe",
    "contact": "1234567890",
    "date": "2025-11-13",
    "description": "Complaint description here",
    "action_taken": "Investigation initiated",
    "assigned": "Admin Staff",
    "note": "Follow up required",
    "image": null
  }'
```

**Response:**
```json
{
  "status": 1,
  "message": "Complaint created successfully",
  "data": {
    "id": 23,
    "complaint_type": "General",
    "source": "Phone",
    "name": "John Doe",
    "date": "2025-11-13"
  }
}
```

### Step 3: List All Complaints

```bash
curl -X POST "http://localhost/amt/api/complaint-api/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Step 4: Get Specific Complaint

```bash
curl -X POST "http://localhost/amt/api/complaint-api/get/23" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Step 5: Update the Complaint

```bash
curl -X POST "http://localhost/amt/api/complaint-api/update/23" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "complaint_type": "General",
    "source": "Phone",
    "name": "John Doe",
    "contact": "1234567890",
    "date": "2025-11-13",
    "description": "Complaint description here",
    "action_taken": "Resolved",
    "assigned": "Admin Staff",
    "note": "Issue resolved",
    "image": null
  }'
```

### Step 6: Delete the Complaint (if needed)

```bash
curl -X POST "http://localhost/amt/api/complaint-api/delete/23" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

## Use Cases

### 1. **Complaint Management**
   - Record all incoming complaints from various sources
   - Track complaint types and sources
   - Maintain complaint history
   - Generate complaint reports

### 2. **Complaint Tracking**
   - Track complaints by type (General, Fees, Sports, Transport, etc.)
   - Monitor complaints by source (Phone, Email, In Person, Online)
   - Track assigned staff/departments
   - Monitor action taken on complaints

### 3. **Document Management**
   - Attach documents to complaints via image field
   - Track document paths for complaint records
   - Maintain document history
   - Link documents to specific complaints

### 4. **Reporting and Analytics**
   - Generate complaint reports by date range
   - Filter complaints by type, source, or assigned staff
   - Search complaints across multiple fields
   - Export complaint data for analysis

### 5. **Daily Complaint Management**
   - Generate daily complaint lists
   - Filter by date range
   - Search by complainant name or contact
   - Track complaint resolution status

### 6. **Complaint Resolution Tracking**
   - Track action taken on each complaint
   - Monitor assigned staff/departments
   - Maintain notes and follow-up information
   - Track complaint status and resolution

---

## Database Schema

### complaint Table

```sql
CREATE TABLE `complaint` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `complaint_type` varchar(255) DEFAULT NULL,
  `source` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `contact` varchar(255) DEFAULT NULL,
  `date` date NOT NULL,
  `description` text DEFAULT NULL,
  `action_taken` varchar(255) DEFAULT NULL,
  `assigned` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

### complaint_type Table

```sql
CREATE TABLE `complaint_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `complaint_type` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

### source Table

```sql
CREATE TABLE `source` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `source` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

---

## Best Practices

1. **Always provide name and date** as they are required fields
2. **Use proper date format (YYYY-MM-DD)** for all date fields
3. **Use valid complaint types and sources** from the types and sources endpoints
4. **Use the list endpoint with filters** to efficiently retrieve specific complaints
5. **Store document paths** in the image field after uploading files
6. **Use search functionality** to quickly find complaints by name, contact, or description
7. **Maintain complete complaint information** for proper record keeping
8. **Handle error responses** appropriately in your application
9. **Log all API calls** for audit and debugging purposes
10. **Update records** when complaint status or action taken changes
11. **Use assigned field** to track responsibility for complaint resolution
12. **Maintain notes** for follow-up and resolution tracking

---

## Integration with Other APIs

### File Upload
- File uploads should be handled separately
- After successful upload, provide the file path in the `image` field
- File paths should be relative to the uploads directory: `uploads/front_office/complaints/`
- Supported file types depend on system configuration

### Complaint Types and Sources
- Use the `/complaint-api/types` endpoint to get available complaint types
- Use the `/complaint-api/sources` endpoint to get available sources
- These values should be used when creating or updating complaints

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | 2025-11-13 | Initial release with create, read, update, delete, list, types, and sources endpoints |

---

## Support

For issues or questions regarding this API, please contact the development team or refer to the main API documentation.

