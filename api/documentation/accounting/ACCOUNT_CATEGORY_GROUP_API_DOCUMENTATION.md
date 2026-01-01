 # Account Category Group API Documentation

## Overview

The **Account Category Group API** provides RESTful endpoints for managing relationships between account categories and account types. This API enables you to create, read, update, and delete category-type relationships, which are used to define which account types are available for each account category.

**Base URL:** `http://localhost/amt/api`

**Authentication:** All endpoints require the following headers:
- `Client-Service: smartschool`
- `Auth-Key: schoolAdmin@`
- `Content-Type: application/json`

---

## ⚠️ Important Notes

**All endpoints use POST method** - Even for retrieving data, you must use POST (not GET).

**URL Format** - Make sure your URL is correct:
- ❌ Wrong: `hhttp://localhost/amt/api/account-category-group-api/create` (typo: "hhttp")
- ✅ Correct: `http://localhost/amt/api/account-category-group-api/create`
- Always use `http://` (not `https://` or `hhttp://`)

**URL Placeholders** - When you see `{id}` in the documentation, replace it with an actual number:
- ❌ Wrong: `http://localhost/amt/api/account-category-group-api/get/{id}`
- ✅ Correct: `http://localhost/amt/api/account-category-group-api/get/1`
- Example: For ID 5, use `/get/5` not `/get/{id}`

**Request Body** - Even if the endpoint doesn't require data, you must send an empty JSON object `{}` in the request body.

**Unique Combinations** - Each combination of account category and account type must be unique. You cannot create duplicate relationships.

---

## Endpoints

### 1. List Category Groups (Grouped)

**Endpoint:** `POST /account-category-group-api/list`

**Description:** Retrieves a list of category groups grouped by account category. Each category includes its associated account types.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Request Body (Optional Filter):**
```json
{
  "account_category_id": 1
}
```

**Request Parameters:**
| Parameter | Type | Required | Description | Example Values |
|-----------|------|----------|-------------|----------------|
| `account_category_id` | integer | No | Filter by account category ID | `1`, `2`, `3` |

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Category groups retrieved successfully",
  "total_records": 3,
  "data": [
    {
      "id": 3,
      "category_name": "Assets",
      "is_system": 0,
      "account_types": [
        {
          "id": 2,
          "accountcategory_id": 3,
          "accounttype_id": 2,
          "is_active": "no",
          "created_at": "2024-05-07 11:42:53",
          "account_type_name": "Other Asset",
          "account_type_code": null
        },
        {
          "id": 3,
          "accountcategory_id": 3,
          "accounttype_id": 3,
          "is_active": "no",
          "created_at": "2024-05-07 11:43:02",
          "account_type_name": "Other Current Asset",
          "account_type_code": null
        },
        {
          "id": 4,
          "accountcategory_id": 3,
          "accounttype_id": 4,
          "is_active": "no",
          "created_at": "2024-05-07 11:43:15",
          "account_type_name": "Cash In Hand",
          "account_type_code": null
        }
      ]
    }
  ]
}
```

---

### 2. List Category Groups (Flat List)

**Endpoint:** `POST /account-category-group-api/list-flat`

**Description:** Retrieves a flat list of all category groups (not grouped by category). Useful for displaying in tables.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Request Body (Optional Filters):**
```json
{
  "account_category_id": 1,
  "account_type_id": 4,
  "is_active": "yes"
}
```

**Request Parameters:**
| Parameter | Type | Required | Description | Example Values |
|-----------|------|----------|-------------|----------------|
| `account_category_id` | integer | No | Filter by account category ID | `1`, `2`, `3` |
| `account_type_id` | integer | No | Filter by account type ID | `4`, `5`, `6` |
| `is_active` | string | No | Filter by active status | `"yes"`, `"no"` |

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Category groups retrieved successfully",
  "total_records": 9,
  "data": [
    {
      "id": 2,
      "accountcategory_id": 3,
      "accounttype_id": 2,
      "is_active": "no",
      "created_at": "2024-05-07 11:42:53",
      "account_category_name": "Assets",
      "account_type_name": "Other Asset",
      "account_type_code": null
    },
    {
      "id": 3,
      "accountcategory_id": 3,
      "accounttype_id": 3,
      "is_active": "no",
      "created_at": "2024-05-07 11:43:02",
      "account_category_name": "Assets",
      "account_type_name": "Other Current Asset",
      "account_type_code": null
    },
    {
      "id": 4,
      "accountcategory_id": 3,
      "accounttype_id": 4,
      "is_active": "no",
      "created_at": "2024-05-07 11:43:15",
      "account_category_name": "Assets",
      "account_type_name": "Cash In Hand",
      "account_type_code": null
    }
  ]
}
```

---

### 3. Get Specific Category Group

**Endpoint:** `POST /account-category-group-api/get/{id}`

**Description:** Retrieves detailed information about a specific category group by its ID.

**⚠️ Important:** 
- **Method must be POST** (not GET)
- Replace `{id}` with an actual category group ID number (e.g., use `1` instead of `{id}`)
- Example URL: `http://localhost/amt/api/account-category-group-api/get/1`

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**URL Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `id` | integer | Yes | ID of the category group | `1`, `2`, `5` |

**Request Body:** `{}` (empty JSON object)

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Category group retrieved successfully",
  "data": {
    "id": 4,
    "account_category_id": 3,
    "account_category_name": "Assets",
    "account_type_id": 4,
    "account_type_name": "Cash In Hand",
    "account_type_code": null,
    "is_active": "no",
    "created_at": "2024-05-07 11:43:15"
  }
}
```

**Error Responses:**

**400 Bad Request - Invalid ID:**
```json
{
  "status": 0,
  "message": "Invalid or missing category group ID",
  "data": null
}
```

**404 Not Found:**
```json
{
  "status": 0,
  "message": "Category group not found",
  "data": null
}
```

---

### 4. Create Category Group

**Endpoint:** `POST /account-category-group-api/create`

**Description:** Creates a new relationship between an account category and an account type.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Request Body:**
```json
{
  "account_category_id": 3,
  "account_type_id": 4,
  "is_active": "yes"
}
```

**Request Parameters:**
| Parameter | Type | Required | Description | Example Values |
|-----------|------|----------|-------------|----------------|
| `account_category_id` | integer | Yes | Account category ID | `1`, `2`, `3` |
| `account_type_id` | integer | Yes | Account type ID | `4`, `5`, `6` |
| `is_active` | string | No | Active status (`yes` or `no`). Default: `yes` | `"yes"`, `"no"` |

**Important Notes:**
- The combination of `account_category_id` and `account_type_id` must be unique
- If the combination already exists, the API will return an error
- Both category and type must exist in the system

**Success Response (201 Created):**
```json
{
  "status": 1,
  "message": "Category group created successfully",
  "data": {
    "id": 51,
    "account_category_id": 3,
    "account_category_name": "Assets",
    "account_type_id": 4,
    "account_type_name": "Cash In Hand",
    "account_type_code": null,
    "is_active": "yes",
    "created_at": "2024-11-14 20:00:00"
  }
}
```

**Error Responses:**

**400 Bad Request - Missing Required Fields:**
```json
{
  "status": 0,
  "message": "Invalid or missing account_category_id",
  "data": null
}
```

**400 Bad Request - Duplicate Combination:**
```json
{
  "status": 0,
  "message": "This account category and account type combination already exists",
  "data": null
}
```

**500 Internal Server Error:**
```json
{
  "status": 0,
  "message": "Failed to create category group",
  "data": null
}
```

---

### 5. Update Category Group

**Endpoint:** `POST /account-category-group-api/update/{id}`

**Description:** Updates an existing category group. Only provided fields will be updated.

**⚠️ Important:** 
- **Method must be POST** (not PUT)
- Replace `{id}` with an actual category group ID number (e.g., use `1` instead of `{id}`)
- Example URL: `http://localhost/amt/api/account-category-group-api/update/1`

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**URL Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `id` | integer | Yes | ID of the category group to update | `1`, `2`, `5` |

**Request Body:**
```json
{
  "account_type_id": 5,
  "is_active": "yes"
}
```

**Request Parameters (All Optional):**
| Parameter | Type | Required | Description | Example Values |
|-----------|------|----------|-------------|----------------|
| `account_category_id` | integer | No | Update account category ID | `1`, `2`, `3` |
| `account_type_id` | integer | No | Update account type ID | `4`, `5`, `6` |
| `is_active` | string | No | Update active status | `"yes"`, `"no"` |

**Important Notes:**
- Only provide fields you want to update
- If updating `account_category_id` or `account_type_id`, the new combination must be unique
- The new combination cannot already exist (excluding the current record)

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Category group updated successfully",
  "data": {
    "id": 4,
    "account_category_id": 3,
    "account_category_name": "Assets",
    "account_type_id": 5,
    "account_type_name": "cash In Bank",
    "account_type_code": null,
    "is_active": "yes",
    "created_at": "2024-05-07 11:43:15"
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

**400 Bad Request - Duplicate Combination:**
```json
{
  "status": 0,
  "message": "This account category and account type combination already exists",
  "data": null
}
```

**404 Not Found:**
```json
{
  "status": 0,
  "message": "Category group not found",
  "data": null
}
```

---

### 6. Delete Category Group

**Endpoint:** `POST /account-category-group-api/delete/{id}`

**Description:** Deletes a category group relationship.

**⚠️ Important:** 
- **Method must be POST** (not DELETE)
- Replace `{id}` with an actual category group ID number (e.g., use `1` instead of `{id}`)
- Example URL: `http://localhost/amt/api/account-category-group-api/delete/1`

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**URL Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `id` | integer | Yes | ID of the category group to delete | `1`, `2`, `5` |

**Request Body:** `{}` (empty JSON object)

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Category group deleted successfully",
  "data": null
}
```

**Error Responses:**

**400 Bad Request - Invalid ID:**
```json
{
  "status": 0,
  "message": "Invalid or missing category group ID",
  "data": null
}
```

**404 Not Found:**
```json
{
  "status": 0,
  "message": "Category group not found",
  "data": null
}
```

**500 Internal Server Error:**
```json
{
  "status": 0,
  "message": "Failed to delete category group",
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
| 404 | Not Found | Resource not found (category group) |
| 405 | Method Not Allowed | Wrong HTTP method used (must be POST) |
| 500 | Internal Server Error | Server error occurred |

---

## cURL Examples

### Example 1: List Category Groups (Grouped)

```bash
curl -X POST "http://localhost/amt/api/account-category-group-api/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 2: List Category Groups (Filtered by Category)

```bash
curl -X POST "http://localhost/amt/api/account-category-group-api/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "account_category_id": 3
  }'
```

### Example 3: List Category Groups (Flat List)

```bash
curl -X POST "http://localhost/amt/api/account-category-group-api/list-flat" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 4: List Category Groups (Flat List with Filters)

```bash
curl -X POST "http://localhost/amt/api/account-category-group-api/list-flat" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "account_category_id": 3,
    "is_active": "yes"
  }'
```

### Example 5: Get Specific Category Group

```bash
curl -X POST "http://localhost/amt/api/account-category-group-api/get/4" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 6: Create Category Group

```bash
curl -X POST "http://localhost/amt/api/account-category-group-api/create" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "account_category_id": 3,
    "account_type_id": 4,
    "is_active": "yes"
  }'
```

### Example 7: Update Category Group

```bash
curl -X POST "http://localhost/amt/api/account-category-group-api/update/4" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "account_type_id": 5,
    "is_active": "yes"
  }'
```

### Example 8: Delete Category Group

```bash
curl -X POST "http://localhost/amt/api/account-category-group-api/delete/4" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

## Complete Workflow: Create Category Group

### Step 1: List Category Groups (to see existing relationships)

```bash
curl -X POST "http://localhost/amt/api/account-category-group-api/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Response:**
```json
{
  "status": 1,
  "message": "Category groups retrieved successfully",
  "total_records": 3,
  "data": [
    {
      "id": 3,
      "category_name": "Assets",
      "account_types": [...]
    }
  ]
}
```

### Step 2: Create New Category Group

```bash
curl -X POST "http://localhost/amt/api/account-category-group-api/create" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "account_category_id": 3,
    "account_type_id": 4,
    "is_active": "yes"
  }'
```

**Response:**
```json
{
  "status": 1,
  "message": "Category group created successfully",
  "data": {
    "id": 51,
    "account_category_id": 3,
    "account_category_name": "Assets",
    "account_type_id": 4,
    "account_type_name": "Cash In Hand",
    "is_active": "yes"
  }
}
```

---

## Use Cases

### 1. **Category-Type Relationship Management**
   - Link account types to account categories
   - Define which account types are available for each category
   - Manage the relationship between categories and types

### 2. **Account Setup**
   - Configure account structure
   - Set up category-type mappings
   - Organize accounts by category and type

### 3. **Account Type Filtering**
   - Filter available account types based on selected category
   - Display relevant account types in dropdowns
   - Ensure proper account categorization

### 4. **Data Organization**
   - Group accounts by category
   - View all types associated with a category
   - Maintain data integrity through relationships

---

## Database Schema

### accountcategorygroup Table

The category group system uses the `accountcategorygroup` table to store relationships:

- `id` (Primary Key) - Unique identifier for the relationship
- `accountcategory_id` - Account category ID (foreign key to `accountcategory.id`)
- `accounttype_id` - Account type ID (foreign key to `accounttype.id`)
- `is_active` - Active status (`yes` or `no`)
- `created_at` - Record creation timestamp

**Related Tables:**
- `accountcategory` - Account category information
- `accounttype` - Account type information

**Unique Constraint:**
- The combination of `accountcategory_id` and `accounttype_id` must be unique

---

## Best Practices

1. **Unique Combinations** - Ensure each category-type combination is unique
2. **Validation** - Validate that both category and type exist before creating relationships
3. **Active Status** - Use `is_active` to enable/disable relationships without deleting them
4. **Grouped vs Flat** - Use grouped list for hierarchical views, flat list for tables
5. **Filtering** - Use filters to narrow down category groups when needed
6. **Updates** - Only update necessary fields to avoid unintended changes
7. **Deletion** - Delete relationships carefully as they may affect account setup
8. **Data Integrity** - Maintain proper relationships between categories and types
9. **Error Handling** - Check for duplicate combinations before creating
10. **List Views** - Use appropriate list endpoint based on display requirements

---

## Integration Notes

### Category-Type Relationships
- Each relationship links one account category to one account type
- Multiple types can be associated with one category
- One type can be associated with multiple categories
- The relationship is many-to-many through the junction table

### List Endpoints
- **Grouped List (`/list`)**: Returns categories with nested account types (hierarchical structure)
- **Flat List (`/list-flat`)**: Returns all relationships as a flat array (table structure)

### Validation
- The API validates that category-type combinations are unique
- Both category and type must exist in the system
- Duplicate combinations are rejected with appropriate error messages

### Active Status
- Use `is_active` to control whether a relationship is active
- Inactive relationships are still stored but can be filtered out
- Default value is `yes` when creating new relationships

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | 2025-11-14 | Initial release with CRUD operations for category groups |

---

## Support

For issues or questions regarding this API, please contact the development team or refer to the main API documentation.

