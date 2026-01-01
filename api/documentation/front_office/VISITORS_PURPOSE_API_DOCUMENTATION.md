# Visitors Purpose API Documentation

## Overview

The **Visitors Purpose API** provides RESTful endpoints for managing visitor purpose records in the school management system. This API enables you to create, read, update, and delete visitor purpose records that are used in the front office module for categorizing visitor purposes.

**Base URL:** `http://localhost/amt/api`

**Authentication:** All endpoints require the following headers:
- `Client-Service: smartschool`
- `Auth-Key: schoolAdmin@`
- `Content-Type: application/json`

---

## ⚠️ Important Notes

**All endpoints use POST method** - Even for retrieving data, you must use POST (not GET).

**URL Format** - Make sure your URL is correct:
- ❌ Wrong: `hhttp://localhost/amt/api/visitors-purpose-api/update/15` (typo: "hhttp")
- ✅ Correct: `http://localhost/amt/api/visitors-purpose-api/update/15`
- Always use `http://` (not `https://` or `hhttp://`)

**URL Placeholders** - When you see `{id}` in the documentation, replace it with an actual number:
- ❌ Wrong: `http://localhost/amt/api/visitors-purpose-api/get/{id}`
- ✅ Correct: `http://localhost/amt/api/visitors-purpose-api/get/1`
- Example: For ID 15, use `/update/15` not `/update/{id}`

**Request Body** - Even if the endpoint doesn't require data, you must send an empty JSON object `{}` in the request body.

---

## Endpoints

### 1. List All Visitor Purposes

**Endpoint:** `POST /visitors-purpose-api/list`

**Description:** Retrieves a list of all visitor purposes with optional search capabilities. This endpoint supports searching across purpose name and description fields.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Request Body:**
```json
{
  "search": "Marketing"
}
```

**Request Parameters:**
| Parameter | Type | Required | Description | Example Values |
|-----------|------|----------|-------------|----------------|
| `search` | string | No | General search query that searches across visitors_purpose and description fields (case-insensitive partial match) | `"Marketing"`, `"Meeting"` |

**Important Notes:**
- The `search` parameter is optional. If not provided, all purposes will be returned.
- The `search` parameter performs a partial match across purpose name and description fields.
- Results are ordered by purpose ID in ascending order.

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Visitor purposes retrieved successfully",
  "total_records": 6,
  "data": [
    {
      "id": 1,
      "visitors_purpose": "Marketing",
      "description": "Marketing related visits"
    },
    {
      "id": 2,
      "visitors_purpose": "Parent Teacher Meeting",
      "description": "Meetings with parents and teachers"
    },
    {
      "id": 3,
      "visitors_purpose": "Student Meeting",
      "description": "Meetings with students"
    },
    {
      "id": 4,
      "visitors_purpose": "Staff Meeting",
      "description": "Meetings with staff members"
    },
    {
      "id": 5,
      "visitors_purpose": "Principal Meeting",
      "description": "Meetings with principal"
    },
    {
      "id": 6,
      "visitors_purpose": "School Events",
      "description": "Visits related to school events"
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

### 2. Get Specific Visitor Purpose

**Endpoint:** `POST /visitors-purpose-api/get/{id}`

**Description:** Retrieves details of a specific visitor purpose by its ID.

**⚠️ Important:** 
- **Method must be POST** (not GET)
- Replace `{id}` with an actual purpose ID number (e.g., use `1` instead of `{id}`)
- Example URL: `http://localhost/amt/api/visitors-purpose-api/get/1`

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**URL Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `id` | integer | Yes | ID of the visitor purpose | `1`, `2`, `6` |

**Request Body:** `{}` (empty JSON object)

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Visitor purpose retrieved successfully",
  "data": {
    "id": 1,
    "visitors_purpose": "Marketing",
    "description": "Marketing related visits"
  }
}
```

**Error Responses:**

**400 Bad Request - Invalid ID:**
```json
{
  "status": 0,
  "message": "Invalid or missing purpose ID",
  "data": null
}
```

**404 Not Found:**
```json
{
  "status": 0,
  "message": "Visitor purpose not found",
  "data": null
}
```

---

### 3. Create New Visitor Purpose

**Endpoint:** `POST /visitors-purpose-api/create`

**Description:** Creates a new visitor purpose record.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Request Body:**
```json
{
  "visitors_purpose": "Admission Inquiry",
  "description": "Visits related to admission inquiries"
}
```

**Request Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `visitors_purpose` | string | Yes | Name of the visitor purpose | `"Admission Inquiry"`, `"Parent Meeting"` |
| `description` | string | No | Detailed description of the purpose | `"Visits related to admission inquiries"` |

**Success Response (201 Created):**
```json
{
  "status": 1,
  "message": "Visitor purpose created successfully",
  "data": {
    "id": 7,
    "visitors_purpose": "Admission Inquiry",
    "description": "Visits related to admission inquiries"
  }
}
```

**Error Responses:**

**400 Bad Request - Missing Required Field:**
```json
{
  "status": 0,
  "message": "Purpose is required",
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

### 4. Update Visitor Purpose

**Endpoint:** `POST /visitors-purpose-api/update/{id}`

**Description:** Updates an existing visitor purpose record.

**⚠️ Important:** 
- **Method must be POST** (not PUT or PATCH)
- **URL must start with `http://`** (not `hhttp://` or `https://`)
- **Replace `{id}` with an actual purpose ID number** (e.g., use `1` instead of `{id}`)
- **Correct Example URL:** `http://localhost/amt/api/visitors-purpose-api/update/1`
- You must have a valid purpose ID. Use the list endpoint first to get existing purpose IDs.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**URL Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `id` | integer | Yes | ID of the purpose to update | `1`, `2`, `6` |

**Request Body:**
```json
{
  "visitors_purpose": "Marketing & Sales",
  "description": "Marketing and sales related visits"
}
```

**Request Parameters:** (Same as Create endpoint)

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Visitor purpose updated successfully",
  "data": {
    "id": 1,
    "visitors_purpose": "Marketing & Sales",
    "description": "Marketing and sales related visits"
  }
}
```

**Error Responses:**

**400 Bad Request - Invalid ID:**
```json
{
  "status": 0,
  "message": "Invalid or missing purpose ID",
  "data": null
}
```

**400 Bad Request - Missing Required Field:**
```json
{
  "status": 0,
  "message": "Purpose is required",
  "data": null
}
```

**404 Not Found - Purpose doesn't exist:**
```json
{
  "status": 0,
  "message": "Visitor purpose not found",
  "data": null
}
```

---

### 5. Delete Visitor Purpose

**Endpoint:** `POST /visitors-purpose-api/delete/{id}`

**Description:** Deletes a visitor purpose record.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**URL Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `id` | integer | Yes | ID of the purpose to delete | `1`, `2`, `6` |

**Request Body:** `{}` (empty JSON object)

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Visitor purpose deleted successfully",
  "data": {
    "id": 7
  }
}
```

**Error Responses:**

**400 Bad Request - Invalid ID:**
```json
{
  "status": 0,
  "message": "Invalid or missing purpose ID",
  "data": null
}
```

**404 Not Found:**
```json
{
  "status": 0,
  "message": "Visitor purpose not found",
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
| 404 | Not Found | Resource not found (purpose) |
| 405 | Method Not Allowed | Wrong HTTP method used (must be POST) |
| 500 | Internal Server Error | Server error occurred |

---

## cURL Examples

### Example 1: List All Visitor Purposes

```bash
curl -X POST "http://localhost/amt/api/visitors-purpose-api/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 2: List with Search

```bash
curl -X POST "http://localhost/amt/api/visitors-purpose-api/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "search": "Meeting"
  }'
```

### Example 3: Get Specific Visitor Purpose

```bash
curl -X POST "http://localhost/amt/api/visitors-purpose-api/get/1" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 4: Create New Visitor Purpose

```bash
curl -X POST "http://localhost/amt/api/visitors-purpose-api/create" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "visitors_purpose": "Admission Inquiry",
    "description": "Visits related to admission inquiries"
  }'
```

### Example 5: Update Visitor Purpose

```bash
curl -X POST "http://localhost/amt/api/visitors-purpose-api/update/1" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "visitors_purpose": "Marketing & Sales",
    "description": "Marketing and sales related visits"
  }'
```

### Example 6: Delete Visitor Purpose

```bash
curl -X POST "http://localhost/amt/api/visitors-purpose-api/delete/7" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

## Complete Workflow: Managing Visitor Purposes

### Step 1: List All Visitor Purposes

```bash
curl -X POST "http://localhost/amt/api/visitors-purpose-api/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Response:**
```json
{
  "status": 1,
  "message": "Visitor purposes retrieved successfully",
  "total_records": 6,
  "data": [
    {"id": 1, "visitors_purpose": "Marketing", "description": "Marketing related visits"},
    {"id": 2, "visitors_purpose": "Parent Teacher Meeting", "description": "Meetings with parents and teachers"}
  ]
}
```

### Step 2: Create a New Visitor Purpose

```bash
curl -X POST "http://localhost/amt/api/visitors-purpose-api/create" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "visitors_purpose": "Admission Inquiry",
    "description": "Visits related to admission inquiries"
  }'
```

**Response:**
```json
{
  "status": 1,
  "message": "Visitor purpose created successfully",
  "data": {
    "id": 7,
    "visitors_purpose": "Admission Inquiry",
    "description": "Visits related to admission inquiries"
  }
}
```

### Step 3: Get Specific Visitor Purpose

```bash
curl -X POST "http://localhost/amt/api/visitors-purpose-api/get/7" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Step 4: Update the Visitor Purpose

```bash
curl -X POST "http://localhost/amt/api/visitors-purpose-api/update/7" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "visitors_purpose": "Admission Inquiry & Consultation",
    "description": "Visits related to admission inquiries and consultations"
  }'
```

### Step 5: Delete the Visitor Purpose (if needed)

```bash
curl -X POST "http://localhost/amt/api/visitors-purpose-api/delete/7" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

## Use Cases

### 1. **Purpose Management**
   - Create and manage visitor purpose categories
   - Track different types of visitor purposes
   - Maintain purpose descriptions for reference

### 2. **Visitor Book Integration**
   - Use purposes when recording visitor entries
   - Filter visitors by purpose type
   - Generate reports based on visitor purposes

### 3. **Reporting and Analytics**
   - Analyze visitor patterns by purpose
   - Generate purpose-based visitor reports
   - Track most common visitor purposes

### 4. **Front Office Setup**
   - Configure available visitor purposes
   - Customize purpose list for your institution
   - Maintain standardized purpose categories

---

## Database Schema

### visitors_purpose Table

```sql
CREATE TABLE `visitors_purpose` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `visitors_purpose` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

**Table Fields:**
- `id` (int, Primary Key, Auto Increment) - Unique identifier for the purpose
- `visitors_purpose` (varchar(255), Required) - Name of the visitor purpose
- `description` (text, Optional) - Detailed description of the purpose

---

## Best Practices

1. **Always provide visitors_purpose** as it is a required field
2. **Use descriptive purpose names** that clearly indicate the purpose type
3. **Add descriptions** to provide context for each purpose
4. **Use the list endpoint with search** to efficiently find specific purposes
5. **Check existing purposes** before creating duplicates
6. **Handle error responses** appropriately in your application
7. **Log all API calls** for audit and debugging purposes
8. **Update records** when purpose names or descriptions need to change
9. **Avoid deleting purposes** that are actively being used in visitor records
10. **Maintain consistent naming** conventions across all purposes

---

## Integration with Other APIs

### Visitor Book API
- Visitor purposes are used when creating visitor entries via the Visitor Book API
- The purpose ID or name should be provided when creating visitor records
- Use the list endpoint to get available purposes for dropdowns/select lists

### Front Office Setup
- This API is part of the front office setup module
- Purposes are configured in the admin panel and can be managed via this API
- Changes made via API will reflect in the admin panel and vice versa

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | 2025-11-13 | Initial release with create, read, update, delete, and list endpoints |

---

## Support

For issues or questions regarding this API, please contact the development team or refer to the main API documentation.

