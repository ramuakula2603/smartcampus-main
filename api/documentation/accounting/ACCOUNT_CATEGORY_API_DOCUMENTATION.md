# Account Category API Documentation

## Overview

The **Account Category API** provides RESTful endpoints for managing account categories. This API enables you to create, read, update, and delete account categories, which are used to organize and classify different types of accounts in the accounting system.

**Base URL:** `http://localhost/amt/api`

**Authentication:** All endpoints require the following headers:
- `Client-Service: smartschool`
- `Auth-Key: schoolAdmin@`
- `Content-Type: application/json`

---

## ⚠️ Important Notes

**All endpoints use POST method** - Even for retrieving data, you must use POST (not GET).

**URL Format** - Make sure your URL is correct:
- ❌ Wrong: `hhttp://localhost/amt/api/account-category-api/create` (typo: "hhttp")
- ✅ Correct: `http://localhost/amt/api/account-category-api/create`
- Always use `http://` (not `https://` or `hhttp://`)

**URL Placeholders** - When you see `{id}` in the documentation, replace it with an actual number:
- ❌ Wrong: `http://localhost/amt/api/account-category-api/get/{id}`
- ✅ Correct: `http://localhost/amt/api/account-category-api/get/3`
- Example: For ID 5, use `/get/5` not `/get/{id}`

**Request Body** - Even if the endpoint doesn't require data, you must send an empty JSON object `{}` in the request body.

**Unique Names** - Each account category name must be unique. You cannot create duplicate category names.

**System Categories** - System categories (with `is_system = 1`) cannot be modified or deleted through the API. Only user-created categories (with `is_system = 0`) can be managed.

---

## Endpoints

### 1. List Account Categories

**Endpoint:** `POST /account-category-api/list`

**Description:** Retrieves a list of all account categories with optional filtering and search capabilities.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Request Body (Optional Filters):**
```json
{
  "is_active": "yes",
  "search": "Assets"
}
```

**Request Parameters:**
| Parameter | Type | Required | Description | Example Values |
|-----------|------|----------|-------------|----------------|
| `is_active` | string | No | Filter by active status | `"yes"`, `"no"` |
| `search` | string | No | Search in name and description | `"Assets"`, `"Payment"` |

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Account categories retrieved successfully",
  "total_records": 4,
  "data": [
    {
      "id": 3,
      "name": "Assets",
      "description": "Assets",
      "is_system": 0,
      "is_active": "no",
      "created_at": "2024-05-07 10:55:45"
    },
    {
      "id": 4,
      "name": "Payment",
      "description": "Payment",
      "is_system": 0,
      "is_active": "no",
      "created_at": "2024-05-07 10:55:59"
    },
    {
      "id": 5,
      "name": "Receipt",
      "description": "Receipt",
      "is_system": 0,
      "is_active": "no",
      "created_at": "2024-05-07 10:56:12"
    },
    {
      "id": 6,
      "name": "Receipt And Payment",
      "description": "Receipt And Payment",
      "is_system": 0,
      "is_active": "no",
      "created_at": "2024-05-07 10:56:44"
    }
  ]
}
```

**Example: List All Categories**
```json
{}
```

**Example: Filter by Active Status**
```json
{
  "is_active": "yes"
}
```

**Example: Search Categories**
```json
{
  "search": "Asset"
}
```

---

### 2. Get Specific Account Category

**Endpoint:** `POST /account-category-api/get/{id}`

**Description:** Retrieves detailed information about a specific account category by its ID.

**⚠️ Important:** 
- **Method must be POST** (not GET)
- Replace `{id}` with an actual category ID number (e.g., use `3` instead of `{id}`)
- Example URL: `http://localhost/amt/api/account-category-api/get/3`

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**URL Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `id` | integer | Yes | ID of the account category | `3`, `4`, `5` |

**Request Body:** `{}` (empty JSON object)

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Account category retrieved successfully",
  "data": {
    "id": 3,
    "name": "Assets",
    "description": "Assets",
    "is_system": 0,
    "is_active": "no",
    "created_at": "2024-05-07 10:55:45"
  }
}
```

**Error Responses:**

**400 Bad Request - Invalid ID:**
```json
{
  "status": 0,
  "message": "Invalid or missing category ID",
  "data": null
}
```

**404 Not Found:**
```json
{
  "status": 0,
  "message": "Account category not found",
  "data": null
}
```

---

### 3. Create Account Category

**Endpoint:** `POST /account-category-api/create`

**Description:** Creates a new account category.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Request Body:**
```json
{
  "name": "Expenses",
  "description": "All expense categories",
  "is_active": "yes"
}
```

**Request Parameters:**
| Parameter | Type | Required | Description | Example Values |
|-----------|------|----------|-------------|----------------|
| `name` | string | Yes | Category name (must be unique) | `"Expenses"`, `"Income"` |
| `description` | string | No | Category description | `"All expense categories"` |
| `is_active` | string | No | Active status (`yes` or `no`). Default: `yes` | `"yes"`, `"no"` |

**Important Notes:**
- The `name` field is required and must be unique
- Category names are case-sensitive
- System categories cannot be created through the API (they are created automatically)
- Empty or whitespace-only names are not allowed

**Success Response (201 Created):**
```json
{
  "status": 1,
  "message": "Account category created successfully",
  "data": {
    "id": 7,
    "name": "Expenses",
    "description": "All expense categories",
    "is_system": 0,
    "is_active": "yes",
    "created_at": "2024-11-15 12:30:00"
  }
}
```

**Error Responses:**

**400 Bad Request - Missing Name:**
```json
{
  "status": 0,
  "message": "Name is required and cannot be empty",
  "data": null
}
```

**400 Bad Request - Duplicate Name:**
```json
{
  "status": 0,
  "message": "Account category with this name already exists",
  "data": null
}
```

**500 Internal Server Error:**
```json
{
  "status": 0,
  "message": "Failed to create account category",
  "data": null
}
```

---

### 4. Update Account Category

**Endpoint:** `POST /account-category-api/update/{id}`

**Description:** Updates an existing account category. Only provided fields will be updated.

**⚠️ Important:** 
- **Method must be POST** (not PUT)
- Replace `{id}` with an actual category ID number (e.g., use `3` instead of `{id}`)
- Example URL: `http://localhost/amt/api/account-category-api/update/3`

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**URL Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `id` | integer | Yes | ID of the category to update | `3`, `4`, `5` |

**Request Body:**
```json
{
  "name": "Updated Assets",
  "description": "Updated description",
  "is_active": "yes"
}
```

**Request Parameters (All Optional):**
| Parameter | Type | Required | Description | Example Values |
|-----------|------|----------|-------------|----------------|
| `name` | string | No | Update category name (must be unique) | `"Updated Assets"` |
| `description` | string | No | Update category description | `"Updated description"` |
| `is_active` | string | No | Update active status | `"yes"`, `"no"` |

**Important Notes:**
- Only provide fields you want to update
- If updating `name`, the new name must be unique
- System categories cannot be updated through the API
- Empty or whitespace-only names are not allowed

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Account category updated successfully",
  "data": {
    "id": 3,
    "name": "Updated Assets",
    "description": "Updated description",
    "is_system": 0,
    "is_active": "yes",
    "created_at": "2024-05-07 10:55:45"
  }
}
```

**Error Responses:**

**400 Bad Request - No Data Provided:**
```json
{
  "status": 0,
  "message": "No data provided for update",
  "data": null
}
```

**400 Bad Request - Duplicate Name:**
```json
{
  "status": 0,
  "message": "Account category with this name already exists",
  "data": null
}
```

**404 Not Found:**
```json
{
  "status": 0,
  "message": "Account category not found",
  "data": null
}
```

---

### 5. Delete Account Category

**Endpoint:** `POST /account-category-api/delete/{id}`

**Description:** Deletes an account category.

**⚠️ Important:** 
- **Method must be POST** (not DELETE)
- Replace `{id}` with an actual category ID number (e.g., use `3` instead of `{id}`)
- Example URL: `http://localhost/amt/api/account-category-api/delete/3`

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**URL Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `id` | integer | Yes | ID of the category to delete | `3`, `4`, `5` |

**Request Body:** `{}` (empty JSON object)

**Important Notes:**
- System categories cannot be deleted through the API
- Only user-created categories (with `is_system = 0`) can be deleted
- Deleting a category may affect related records (e.g., account category groups)

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Account category deleted successfully",
  "data": null
}
```

**Error Responses:**

**400 Bad Request - Invalid ID:**
```json
{
  "status": 0,
  "message": "Invalid or missing category ID",
  "data": null
}
```

**404 Not Found:**
```json
{
  "status": 0,
  "message": "Account category not found",
  "data": null
}
```

**500 Internal Server Error:**
```json
{
  "status": 0,
  "message": "Failed to delete account category",
  "data": null
}
```

---

## HTTP Status Codes

| Code | Meaning | Description |
|------|---------|-------------|
| 200 | OK | Request successful (GET, LIST, UPDATE operations) |
| 201 | Created | Resource created successfully (CREATE operation) |
| 400 | Bad Request | Invalid input or missing required fields |
| 401 | Unauthorized | Invalid or missing authentication headers |
| 404 | Not Found | Resource not found (category) |
| 405 | Method Not Allowed | Wrong HTTP method used (must be POST) |
| 500 | Internal Server Error | Server error occurred |

---

## cURL Examples

### Example 1: List All Account Categories

```bash
curl -X POST "http://localhost/amt/api/account-category-api/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 2: List Active Categories Only

```bash
curl -X POST "http://localhost/amt/api/account-category-api/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "is_active": "yes"
  }'
```

### Example 3: Search Categories

```bash
curl -X POST "http://localhost/amt/api/account-category-api/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "search": "Asset"
  }'
```

### Example 4: Get Specific Category

```bash
curl -X POST "http://localhost/amt/api/account-category-api/get/3" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 5: Create Account Category

```bash
curl -X POST "http://localhost/amt/api/account-category-api/create" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "name": "Expenses",
    "description": "All expense categories",
    "is_active": "yes"
  }'
```

### Example 6: Update Account Category

```bash
curl -X POST "http://localhost/amt/api/account-category-api/update/3" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "name": "Updated Assets",
    "description": "Updated description",
    "is_active": "yes"
  }'
```

### Example 7: Update Only Description

```bash
curl -X POST "http://localhost/amt/api/account-category-api/update/3" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "description": "Updated description only"
  }'
```

### Example 8: Delete Account Category

```bash
curl -X POST "http://localhost/amt/api/account-category-api/delete/3" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

## Complete Workflow: Create and Manage Account Category

### Step 1: List All Categories (to see existing categories)

```bash
curl -X POST "http://localhost/amt/api/account-category-api/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Response:**
```json
{
  "status": 1,
  "message": "Account categories retrieved successfully",
  "total_records": 4,
  "data": [
    {
      "id": 3,
      "name": "Assets",
      "description": "Assets",
      "is_system": 0,
      "is_active": "no"
    }
  ]
}
```

### Step 2: Create New Category

```bash
curl -X POST "http://localhost/amt/api/account-category-api/create" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "name": "Expenses",
    "description": "All expense categories",
    "is_active": "yes"
  }'
```

**Response:**
```json
{
  "status": 1,
  "message": "Account category created successfully",
  "data": {
    "id": 7,
    "name": "Expenses",
    "description": "All expense categories",
    "is_system": 0,
    "is_active": "yes"
  }
}
```

### Step 3: Get Created Category

```bash
curl -X POST "http://localhost/amt/api/account-category-api/get/7" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Step 4: Update Category

```bash
curl -X POST "http://localhost/amt/api/account-category-api/update/7" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "description": "Updated expense description"
  }'
```

---

## Use Cases

### 1. **Category Management**
   - Create new account categories
   - Update existing category information
   - Delete unused categories
   - Organize accounts by category

### 2. **Account Organization**
   - Classify accounts into categories
   - Group related accounts together
   - Maintain account structure

### 3. **Reporting and Filtering**
   - Filter accounts by category
   - Generate category-based reports
   - Analyze accounts by category

### 4. **Data Integrity**
   - Ensure unique category names
   - Maintain category relationships
   - Protect system categories

---

## Database Schema

### accountcategory Table

The category system uses the `accountcategory` table to store category information:

- `id` (Primary Key) - Unique identifier for the category
- `name` (VARCHAR 200) - Category name (must be unique)
- `description` (TEXT) - Category description
- `is_system` (INT 1) - System flag (0 = user-created, 1 = system)
- `is_active` (VARCHAR 10) - Active status (`yes` or `no`)
- `created_at` (TIMESTAMP) - Record creation timestamp

**Constraints:**
- `name` must be unique
- System categories (`is_system = 1`) cannot be modified or deleted
- Only user-created categories (`is_system = 0`) can be managed through the API

**Related Tables:**
- `accountcategorygroup` - Links categories to account types
- `addaccount` - Accounts that belong to categories

---

## Best Practices

1. **Unique Names** - Ensure each category name is unique and descriptive
2. **Descriptions** - Provide clear descriptions for better understanding
3. **Active Status** - Use `is_active` to enable/disable categories without deleting them
4. **Search** - Use search functionality to find categories quickly
5. **Updates** - Only update necessary fields to avoid unintended changes
6. **Deletion** - Delete categories carefully as they may affect related records
7. **System Categories** - Do not attempt to modify or delete system categories
8. **Validation** - Always validate category names before creating
9. **Naming Conventions** - Use consistent naming conventions for categories
10. **Data Integrity** - Maintain proper relationships between categories and accounts

---

## Integration Notes

### Category Management
- Categories are used to organize and classify accounts
- Each category can have multiple account types associated with it
- Categories help in reporting and filtering accounts

### System vs User Categories
- System categories are created automatically and cannot be modified
- User-created categories can be fully managed through the API
- The API automatically filters out system categories from operations

### Name Uniqueness
- Category names must be unique across the system
- The API validates name uniqueness before creating or updating
- Case-sensitive comparison is used for names

### Active Status
- Use `is_active` to control whether a category is active
- Inactive categories are still stored but can be filtered out
- Default value is `yes` when creating new categories

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | 2025-11-15 | Initial release with CRUD operations for account categories |

---

## Support

For issues or questions regarding this API, please contact the development team or refer to the main API documentation.

