# Class Sections API Documentation

## Overview

The **Class Sections API** provides RESTful endpoints for managing the relationships between classes and sections through the `class_sections` junction table. This API enables you to link and unlink classes to sections, retrieve all class-section relationships, and get details about specific links.

**Base URL:** `http://localhost/amt/api`

**Authentication:** All endpoints require the following headers:
- `Client-Service: smartschool`
- `Auth-Key: schoolAdmin@`
- `Content-Type: application/json`

---

## Endpoints

### 1. Link Class to Section

**Endpoint:** `POST /class-sections/link`

**Description:** Creates a new link between an existing class and an existing section in the `class_sections` junction table.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Request Body:**
```json
{
  "class_id": 10,
  "section_id": 5,
  "is_active": "yes"
}
```

**Request Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `class_id` | integer | Yes | ID of the class to link (must exist in classes table) |
| `section_id` | integer | Yes | ID of the section to link (must exist in sections table) |
| `is_active` | string | No | Active status: "yes" or "no" (default: "yes") |

**Success Response (201 Created):**
```json
{
  "status": 1,
  "message": "Class-section link created successfully",
  "data": {
    "id": 45,
    "class_id": 10,
    "section_id": 5,
    "class": "Grade 10",
    "section": "A",
    "is_active": "yes",
    "created_at": "2025-11-01 10:30:00",
    "updated_at": null
  }
}
```

**Error Responses:**

**400 Bad Request - Missing/Invalid class_id:**
```json
{
  "status": 0,
  "message": "Class ID is required and must be a positive integer",
  "data": null
}
```

**400 Bad Request - Missing/Invalid section_id:**
```json
{
  "status": 0,
  "message": "Section ID is required and must be a positive integer",
  "data": null
}
```

**404 Not Found - Class doesn't exist:**
```json
{
  "status": 0,
  "message": "Class not found",
  "data": null
}
```

**404 Not Found - Section doesn't exist:**
```json
{
  "status": 0,
  "message": "Section not found",
  "data": null
}
```

**409 Conflict - Link already exists:**
```json
{
  "status": 0,
  "message": "This class-section link already exists",
  "data": null
}
```

---

### 2. Unlink Class from Section

**Endpoint:** `POST /class-sections/unlink/{id}`

**Description:** Removes a class-section link from the `class_sections` table.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**URL Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `id` | integer | Yes | ID of the class-section link to remove |

**Request Body:** `{}` (empty JSON object)

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Class-section link removed successfully",
  "data": {
    "id": 45,
    "class_id": 10,
    "section_id": 5
  }
}
```

**Error Responses:**

**400 Bad Request - Invalid ID:**
```json
{
  "status": 0,
  "message": "Invalid or missing class-section link ID",
  "data": null
}
```

**404 Not Found - Link doesn't exist:**
```json
{
  "status": 0,
  "message": "Class-section link not found",
  "data": null
}
```

---

### 3. List All Class-Section Links

**Endpoint:** `POST /class-sections/list`

**Description:** Retrieves all class-section links with complete details including class and section names.

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
  "message": "Class-section links retrieved successfully",
  "total_records": 3,
  "data": [
    {
      "id": 45,
      "class_id": 10,
      "section_id": 5,
      "class": "Grade 10",
      "section": "A",
      "is_active": "yes",
      "created_at": "2025-11-01 10:30:00",
      "updated_at": null
    },
    {
      "id": 46,
      "class_id": 10,
      "section_id": 6,
      "class": "Grade 10",
      "section": "B",
      "is_active": "yes",
      "created_at": "2025-11-01 10:31:00",
      "updated_at": null
    },
    {
      "id": 47,
      "class_id": 11,
      "section_id": 5,
      "class": "Grade 11",
      "section": "A",
      "is_active": "yes",
      "created_at": "2025-11-01 10:32:00",
      "updated_at": null
    }
  ]
}
```

---

### 4. Get Specific Class-Section Link

**Endpoint:** `POST /class-sections/get/{id}`

**Description:** Retrieves details of a specific class-section link.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**URL Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `id` | integer | Yes | ID of the class-section link |

**Request Body:** `{}` (empty JSON object)

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Class-section link retrieved successfully",
  "data": {
    "id": 45,
    "class_id": 10,
    "section_id": 5,
    "class": "Grade 10",
    "section": "A",
    "is_active": "yes",
    "created_at": "2025-11-01 10:30:00",
    "updated_at": null
  }
}
```

**Error Response (404 Not Found):**
```json
{
  "status": 0,
  "message": "Class-section link not found",
  "data": null
}
```

---

## HTTP Status Codes

| Code | Meaning | Description |
|------|---------|-------------|
| 200 | OK | Request successful (GET, LIST, UNLINK operations) |
| 201 | Created | Resource created successfully (LINK operation) |
| 400 | Bad Request | Invalid input or missing required fields |
| 401 | Unauthorized | Invalid or missing authentication headers |
| 404 | Not Found | Resource not found (class, section, or link) |
| 405 | Method Not Allowed | Wrong HTTP method used (must be POST) |
| 409 | Conflict | Resource already exists (duplicate link) |
| 500 | Internal Server Error | Server error occurred |

---

## cURL Examples

### Example 1: Link a Class to a Section

```bash
curl -X POST "http://localhost/amt/api/class-sections/link" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "class_id": 10,
    "section_id": 5,
    "is_active": "yes"
  }'
```

### Example 2: Unlink a Class from a Section

```bash
curl -X POST "http://localhost/amt/api/class-sections/unlink/45" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 3: List All Class-Section Links

```bash
curl -X POST "http://localhost/amt/api/class-sections/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 4: Get a Specific Class-Section Link

```bash
curl -X POST "http://localhost/amt/api/class-sections/get/45" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

## Complete Workflow: Creating Class, Section, and Linking Them

This section demonstrates the complete workflow for creating a new class, creating a new section, and linking them together.

### Step 1: Create a Class

```bash
curl -X POST "http://localhost/amt/api/classes/create" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "class": "Grade 12",
    "is_active": "yes"
  }'
```

**Response:**
```json
{
  "status": 1,
  "message": "Class created successfully",
  "data": {
    "id": 25,
    "class": "Grade 12",
    "is_active": "yes",
    "created_at": "2025-11-01 10:30:00"
  }
}
```

### Step 2: Create a Section

```bash
curl -X POST "http://localhost/amt/api/sections/create" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "section": "C",
    "is_active": "yes"
  }'
```

**Response:**
```json
{
  "status": 1,
  "message": "Section created successfully",
  "data": {
    "id": 50,
    "section": "C",
    "is_active": "yes",
    "created_at": "2025-11-01 10:30:00"
  }
}
```

### Step 3: Link the Class to the Section

```bash
curl -X POST "http://localhost/amt/api/class-sections/link" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "class_id": 25,
    "section_id": 50,
    "is_active": "yes"
  }'
```

**Response:**
```json
{
  "status": 1,
  "message": "Class-section link created successfully",
  "data": {
    "id": 100,
    "class_id": 25,
    "section_id": 50,
    "class": "Grade 12",
    "section": "C",
    "is_active": "yes",
    "created_at": "2025-11-01 10:30:00",
    "updated_at": null
  }
}
```

---

## Use Cases

### 1. **Bulk Class-Section Assignment**
   - Create multiple sections for a class
   - Link each section to the class using the API
   - Useful for setting up new academic sessions

### 2. **Class Restructuring**
   - Unlink sections from a class
   - Link them to a different class
   - Useful for reorganizing class structures

### 3. **Student Assignment Validation**
   - Verify class-section relationships before assigning students
   - Ensure valid class-section combinations exist

### 4. **Timetable Management**
   - Link classes to sections before creating timetables
   - Ensure all required class-section combinations are set up

### 5. **Teacher Assignment**
   - Verify class-section links exist before assigning teachers
   - Ensure teachers are assigned to valid class-section combinations

### 6. **Report Generation**
   - Retrieve all class-section links for reporting
   - Generate hierarchical reports of classes and their sections

---

## Database Schema

### class_sections Table

```sql
CREATE TABLE `class_sections` (
  `id` int(11) NOT NULL,
  `class_id` int(11) DEFAULT NULL,
  `section_id` int(11) DEFAULT NULL,
  `is_active` varchar(255) DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` date DEFAULT NULL
);
```

### Key Relationships

```
classes (id) ← class_sections (class_id)
class_sections (section_id) → sections (id)
```

---

## Best Practices

1. **Always validate class and section IDs** before attempting to link them
2. **Check for existing links** to avoid duplicate entries (API returns 409 Conflict)
3. **Use the list endpoint** to verify all class-section relationships
4. **Maintain data consistency** by unlinking before deleting classes or sections
5. **Use is_active flag** to deactivate links without deleting them
6. **Handle error responses** appropriately in your application
7. **Log all API calls** for audit and debugging purposes

---

## Integration with Other APIs

### Classes API
- **Create Class:** `POST /api/classes/create`
- **List Classes:** `POST /api/classes/list`
- **Get Class:** `POST /api/classes/get/{id}`

### Sections API
- **Create Section:** `POST /api/sections/create`
- **List Sections:** `POST /api/sections/list`
- **Get Section:** `POST /api/sections/get/{id}`

### Hierarchical Retrieval
- **Classes with Sections:** `POST /api/teacher/classes-with-sections`
- **Sessions with Classes and Sections:** `POST /api/teacher/sessions-with-classes-sections`

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | 2025-11-01 | Initial release with link, unlink, list, and get endpoints |

---

## Support

For issues or questions regarding this API, please contact the development team or refer to the main API documentation.

