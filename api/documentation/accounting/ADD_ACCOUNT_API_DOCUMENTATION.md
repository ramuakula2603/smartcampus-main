# Add Account API Documentation

## Overview

The **Add Account API** provides RESTful endpoints for managing accounts in the school management system. This API enables you to create, read, update, and delete accounts, as well as retrieve account categories, types, and roles for dropdown selections.

**Base URL:** `http://localhost/amt/api`

**Authentication:** All endpoints require the following headers:
- `Client-Service: smartschool`
- `Auth-Key: schoolAdmin@`
- `Content-Type: application/json`

---

## ⚠️ Important Notes

**All endpoints use POST method** - Even for retrieving data, you must use POST (not GET).

**URL Format** - Make sure your URL is correct:
- ❌ Wrong: `hhttp://localhost/amt/api/add-account-api/create` (typo: "hhttp")
- ✅ Correct: `http://localhost/amt/api/add-account-api/create`
- Always use `http://` (not `https://` or `hhttp://`)

**URL Placeholders** - When you see `{id}` in the documentation, replace it with an actual number:
- ❌ Wrong: `http://localhost/amt/api/add-account-api/get/{id}`
- ✅ Correct: `http://localhost/amt/api/add-account-api/get/1`
- Example: For ID 5, use `/get/5` not `/get/{id}`

**Request Body** - Even if the endpoint doesn't require data, you must send an empty JSON object `{}` in the request body.

**Payment Modes** - Payment modes are provided as an array of strings. Valid values are: `cash`, `cheque`, `dd`, `bank_transfer`, `upi`, `card`. At least one payment mode must be selected.

**Account Roles** - Account role must be one of: `both`, `debitor`, `creditor`.

---

## Endpoints

### 1. Get Account Categories

**Endpoint:** `POST /add-account-api/categories`

**Description:** Retrieves a list of all account categories for dropdown selection.

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
  "message": "Account categories retrieved successfully",
  "total_records": 5,
  "data": [
    {
      "id": 1,
      "name": "Assets",
      "is_system": 0
    },
    {
      "id": 2,
      "name": "Liabilities",
      "is_system": 0
    },
    {
      "id": 3,
      "name": "Payment",
      "is_system": 0
    }
  ]
}
```

---

### 2. Get Account Types

**Endpoint:** `POST /add-account-api/types`

**Description:** Retrieves a list of account types. Can be filtered by account category ID.

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
| `account_category_id` | integer | No | Filter account types by category ID | `1`, `2`, `3` |

**Note:** If `account_category_id` is provided, only account types associated with that category will be returned. Otherwise, all account types will be returned.

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Account types retrieved successfully",
  "total_records": 10,
  "data": [
    {
      "id": 4,
      "type": "Cash In Hand",
      "is_system": 0
    },
    {
      "id": 5,
      "type": "cash In Bank",
      "is_system": 0
    },
    {
      "id": 6,
      "type": "Other Asset",
      "is_system": 0
    }
  ]
}
```

**Success Response (With Category Filter):**
```json
{
  "status": 1,
  "message": "Account types retrieved successfully",
  "total_records": 3,
  "data": [
    {
      "id": 4,
      "name": "Cash In Hand"
    },
    {
      "id": 5,
      "name": "cash In Bank"
    }
  ]
}
```

---

### 3. Get Account Roles

**Endpoint:** `POST /add-account-api/roles`

**Description:** Retrieves a list of account roles for dropdown selection.

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
  "message": "Account roles retrieved successfully",
  "total_records": 3,
  "data": [
    {
      "value": "both",
      "label": "Both"
    },
    {
      "value": "debitor",
      "label": "Debitor"
    },
    {
      "value": "creditor",
      "label": "Creditor"
    }
  ]
}
```

---

### 4. Create Account

**Endpoint:** `POST /add-account-api/create`

**Description:** Creates a new account with the specified details.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Request Body:**
```json
{
  "name": "HDFC Bank",
  "code": "2",
  "account_category": 1,
  "account_type": 5,
  "account_role": "both",
  "payment_modes": ["cash", "cheque", "dd", "bank_transfer", "upi", "card"],
  "description": "HDFC Bank Account",
  "is_active": "yes"
}
```

**Request Parameters:**
| Parameter | Type | Required | Description | Example Values |
|-----------|------|----------|-------------|----------------|
| `name` | string | Yes | Account name (must be unique) | `"HDFC Bank"`, `"Cash"` |
| `code` | string | Yes | Account code (must be unique) | `"1"`, `"2"`, `"BANK001"` |
| `account_category` | integer | Yes | Account category ID | `1`, `2`, `3` |
| `account_type` | integer | Yes | Account type ID | `4`, `5`, `6` |
| `account_role` | string | Yes | Account role (`both`, `debitor`, or `creditor`) | `"both"`, `"debitor"`, `"creditor"` |
| `payment_modes` | array | Yes | Array of payment modes (at least one required) | `["cash", "cheque"]` |
| `description` | string | No | Account description | `"HDFC Bank Account"` |
| `is_active` | string | No | Active status (`yes` or `no`). Default: `yes` | `"yes"`, `"no"` |

**Payment Modes:**
- Valid values: `cash`, `cheque`, `dd`, `bank_transfer`, `upi`, `card`
- Must be provided as an array of strings
- At least one payment mode must be selected

**Success Response (201 Created):**
```json
{
  "status": 1,
  "message": "Account created successfully",
  "data": {
    "id": 63,
    "name": "HDFC Bank",
    "code": "2",
    "account_category": 1,
    "account_category_name": "Assets",
    "account_type": 5,
    "account_type_name": "cash In Bank",
    "account_role": "both",
    "is_active": "yes",
    "description": "HDFC Bank Account",
    "payment_modes": ["cash", "cheque", "dd", "bank_transfer", "upi", "card"],
    "cash": 1,
    "cheque": 1,
    "dd": 1,
    "bank_transfer": 1,
    "upi": 1,
    "card": 1,
    "created_at": "2024-11-14 19:30:00",
    "updated_at": null
  }
}
```

**Error Responses:**

**400 Bad Request - Missing Required Fields:**
```json
{
  "status": 0,
  "message": "Invalid or missing name",
  "data": null
}
```

**400 Bad Request - Duplicate Name:**
```json
{
  "status": 0,
  "message": "Account name already exists",
  "data": null
}
```

**400 Bad Request - Duplicate Code:**
```json
{
  "status": 0,
  "message": "Account code already exists",
  "data": null
}
```

**400 Bad Request - Invalid Payment Modes:**
```json
{
  "status": 0,
  "message": "Invalid payment mode: invalid_mode. Valid modes are: cash, cheque, dd, bank_transfer, upi, card",
  "data": null
}
```

**500 Internal Server Error:**
```json
{
  "status": 0,
  "message": "Failed to create account",
  "data": null
}
```

---

### 5. List Accounts

**Endpoint:** `POST /add-account-api/list`

**Description:** Retrieves a list of accounts with optional filters.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Request Body (Optional Filters):**
```json
{
  "account_category": 1,
  "account_type": 5,
  "account_role": "both",
  "is_active": "yes"
}
```

**Request Parameters:**
| Parameter | Type | Required | Description | Example Values |
|-----------|------|----------|-------------|----------------|
| `account_category` | integer | No | Filter by account category ID | `1`, `2`, `3` |
| `account_type` | integer | No | Filter by account type ID | `4`, `5`, `6` |
| `account_role` | string | No | Filter by account role | `"both"`, `"debitor"`, `"creditor"` |
| `is_active` | string | No | Filter by active status | `"yes"`, `"no"` |

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Accounts retrieved successfully",
  "total_records": 15,
  "data": [
    {
      "id": 4,
      "name": "Cash",
      "code": "1",
      "account_category": 3,
      "account_category_name": "Assets",
      "account_type": 4,
      "account_type_name": "Cash In Hand",
      "account_role": "both",
      "is_active": "no",
      "description": "Cash In",
      "payment_modes": ["cash"],
      "cash": 1,
      "cheque": 0,
      "dd": 0,
      "bank_transfer": 0,
      "upi": 0,
      "card": 0,
      "created_at": "2024-05-24 16:29:35",
      "updated_at": null
    },
    {
      "id": 5,
      "name": "HDFC Bank",
      "code": "2",
      "account_category": 3,
      "account_category_name": "Assets",
      "account_type": 5,
      "account_type_name": "cash In Bank",
      "account_role": "both",
      "is_active": "no",
      "description": "cash In",
      "payment_modes": ["cheque", "dd", "bank_transfer", "upi", "card"],
      "cash": 0,
      "cheque": 1,
      "dd": 1,
      "bank_transfer": 1,
      "upi": 1,
      "card": 1,
      "created_at": "2024-05-24 16:40:45",
      "updated_at": null
    }
  ]
}
```

---

### 6. Get Specific Account

**Endpoint:** `POST /add-account-api/get/{id}`

**Description:** Retrieves detailed information about a specific account by its ID.

**⚠️ Important:** 
- **Method must be POST** (not GET)
- Replace `{id}` with an actual account ID number (e.g., use `1` instead of `{id}`)
- Example URL: `http://localhost/amt/api/add-account-api/get/1`

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**URL Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `id` | integer | Yes | ID of the account | `1`, `2`, `5` |

**Request Body:** `{}` (empty JSON object)

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Account retrieved successfully",
  "data": {
    "id": 4,
    "name": "Cash",
    "code": "1",
    "account_category": 3,
    "account_category_name": "Assets",
    "account_type": 4,
    "account_type_name": "Cash In Hand",
    "account_role": "both",
    "is_active": "no",
    "description": "Cash In",
    "payment_modes": ["cash"],
    "cash": 1,
    "cheque": 0,
    "dd": 0,
    "bank_transfer": 0,
    "upi": 0,
    "card": 0,
    "created_at": "2024-05-24 16:29:35",
    "updated_at": null
  }
}
```

**Error Responses:**

**400 Bad Request - Invalid ID:**
```json
{
  "status": 0,
  "message": "Invalid or missing account ID",
  "data": null
}
```

**404 Not Found:**
```json
{
  "status": 0,
  "message": "Account not found",
  "data": null
}
```

---

### 7. Update Account

**Endpoint:** `POST /add-account-api/update/{id}`

**Description:** Updates an existing account. Only provided fields will be updated.

**⚠️ Important:** 
- **Method must be POST** (not PUT)
- Replace `{id}` with an actual account ID number (e.g., use `1` instead of `{id}`)
- Example URL: `http://localhost/amt/api/add-account-api/update/1`

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**URL Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `id` | integer | Yes | ID of the account to update | `1`, `2`, `5` |

**Request Body:**
```json
{
  "name": "Updated Account Name",
  "description": "Updated description",
  "payment_modes": ["cash", "cheque", "upi"],
  "is_active": "yes"
}
```

**Request Parameters (All Optional):**
| Parameter | Type | Required | Description | Example Values |
|-----------|------|----------|-------------|----------------|
| `name` | string | No | Update account name | `"Updated Name"` |
| `code` | string | No | Update account code | `"UPD001"` |
| `account_category` | integer | No | Update account category ID | `1`, `2`, `3` |
| `account_type` | integer | No | Update account type ID | `4`, `5`, `6` |
| `account_role` | string | No | Update account role | `"both"`, `"debitor"`, `"creditor"` |
| `payment_modes` | array | No | Update payment modes | `["cash", "cheque"]` |
| `description` | string | No | Update account description | `"Updated description"` |
| `is_active` | string | No | Update active status | `"yes"`, `"no"` |

**Important Notes:**
- Only provide fields you want to update
- If updating `name` or `code`, the new value must be unique (not already used by another account)
- If updating `payment_modes`, provide the complete array of desired payment modes

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Account updated successfully",
  "data": {
    "id": 4,
    "name": "Updated Account Name",
    "code": "1",
    "account_category": 3,
    "account_category_name": "Assets",
    "account_type": 4,
    "account_type_name": "Cash In Hand",
    "account_role": "both",
    "is_active": "yes",
    "description": "Updated description",
    "payment_modes": ["cash", "cheque", "upi"],
    "cash": 1,
    "cheque": 1,
    "dd": 0,
    "bank_transfer": 0,
    "upi": 1,
    "card": 0,
    "created_at": "2024-05-24 16:29:35",
    "updated_at": "2024-11-14 20:00:00"
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
  "message": "Account name already exists",
  "data": null
}
```

**404 Not Found:**
```json
{
  "status": 0,
  "message": "Account not found",
  "data": null
}
```

---

### 8. Delete Account

**Endpoint:** `POST /add-account-api/delete/{id}`

**Description:** Deletes an account.

**⚠️ Important:** 
- **Method must be POST** (not DELETE)
- Replace `{id}` with an actual account ID number (e.g., use `1` instead of `{id}`)
- Example URL: `            ++`

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**URL Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `id` | integer | Yes | ID of the account to delete | `1`, `2`, `5` |

**Request Body:** `{}` (empty JSON object)

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Account deleted successfully",
  "data": null
}
```

**Error Responses:**

**400 Bad Request - Invalid ID:**
```json
{
  "status": 0,
  "message": "Invalid or missing account ID",
  "data": null
}
```

**404 Not Found:**
```json
{
  "status": 0,
  "message": "Account not found",
  "data": null
}
```

**500 Internal Server Error:**
```json
{
  "status": 0,
  "message": "Failed to delete account",
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
| 404 | Not Found | Resource not found (account, category, type) |
| 405 | Method Not Allowed | Wrong HTTP method used (must be POST) |
| 500 | Internal Server Error | Server error occurred |

---

## cURL Examples

### Example 1: Get Account Categories

```bash
curl -X POST "http://localhost/amt/api/add-account-api/categories" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 2: Get Account Types (All)

```bash
curl -X POST "http://localhost/amt/api/add-account-api/types" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 3: Get Account Types (By Category)

```bash
curl -X POST "http://localhost/amt/api/add-account-api/types" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "account_category_id": 1
  }'
```

### Example 4: Get Account Roles

```bash
curl -X POST "http://localhost/amt/api/add-account-api/roles" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 5: Create Account

```bash
curl -X POST "http://localhost/amt/api/add-account-api/create" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "name": "HDFC Bank",
    "code": "2",
    "account_category": 1,
    "account_type": 5,
    "account_role": "both",
    "payment_modes": ["cash", "cheque", "dd", "bank_transfer", "upi", "card"],
    "description": "HDFC Bank Account",
    "is_active": "yes"
  }'
```

### Example 6: List All Accounts

```bash
curl -X POST "http://localhost/amt/api/add-account-api/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 7: List Accounts with Filters

```bash
curl -X POST "http://localhost/amt/api/add-account-api/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "account_category": 1,
    "account_role": "both",
    "is_active": "yes"
  }'
```

### Example 8: Get Specific Account

```bash
curl -X POST "http://localhost/amt/api/add-account-api/get/4" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 9: Update Account

```bash
curl -X POST "http://localhost/amt/api/add-account-api/update/4" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "name": "Updated Account Name",
    "description": "Updated description",
    "payment_modes": ["cash", "cheque", "upi"],
    "is_active": "yes"
  }'
```

### Example 10: Delete Account

```bash
curl -X POST "http://localhost/amt/api/add-account-api/delete/4" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

## Complete Workflow: Create Account

### Step 1: Get Account Categories

```bash
curl -X POST "http://localhost/amt/api/add-account-api/categories" \
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
  "total_records": 5,
  "data": [
    {
      "id": 1,
      "name": "Assets"
    }
  ]
}
```

### Step 2: Get Account Types (Based on Selected Category)

```bash
curl -X POST "http://localhost/amt/api/add-account-api/types" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "account_category_id": 1
  }'
```

**Response:**
```json
{
  "status": 1,
  "message": "Account types retrieved successfully",
  "total_records": 3,
  "data": [
    {
      "id": 4,
      "name": "Cash In Hand"
    },
    {
      "id": 5,
      "name": "cash In Bank"
    }
  ]
}
```

### Step 3: Get Account Roles

```bash
curl -X POST "http://localhost/amt/api/add-account-api/roles" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Response:**
```json
{
  "status": 1,
  "message": "Account roles retrieved successfully",
  "total_records": 3,
  "data": [
    {
      "value": "both",
      "label": "Both"
    }
  ]
}
```

### Step 4: Create Account

```bash
curl -X POST "http://localhost/amt/api/add-account-api/create" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "name": "HDFC Bank",
    "code": "2",
    "account_category": 1,
    "account_type": 5,
    "account_role": "both",
    "payment_modes": ["cash", "cheque", "dd", "bank_transfer", "upi", "card"],
    "description": "HDFC Bank Account"
  }'
```

**Response:**
```json
{
  "status": 1,
  "message": "Account created successfully",
  "data": {
    "id": 63,
    "name": "HDFC Bank",
    "code": "2",
    "account_category": 1,
    "account_category_name": "Assets",
    "account_type": 5,
    "account_type_name": "cash In Bank",
    "account_role": "both",
    "is_active": "yes",
    "description": "HDFC Bank Account",
    "payment_modes": ["cash", "cheque", "dd", "bank_transfer", "upi", "card"]
  }
}
```

---

## Use Cases

### 1. **Account Management**
   - Create new accounts (bank accounts, cash accounts, etc.)
   - Update account information
   - Delete accounts that are no longer needed
   - View account details and history

### 2. **Account Setup**
   - Set up account categories (Assets, Liabilities, Payment, etc.)
   - Configure account types based on categories
   - Define account roles (both, debitor, creditor)
   - Configure payment modes for each account

### 3. **Account Listing**
   - List all accounts
   - Filter accounts by category, type, role, or status
   - Search and view account details
   - Export account data

### 4. **Form Integration**
   - Populate dropdown lists (categories, types, roles)
   - Validate account names and codes
   - Handle payment mode selections
   - Submit account creation/update forms

---

## Database Schema

### addaccount Table

The account system uses the `addaccount` table to store account records:

- `id` (Primary Key) - Unique identifier for the account
- `is_system` - System account flag (0 = user-created, 1 = system)
- `name` - Account name (unique, required)
- `code` - Account code (unique, required)
- `account_category` - Account category ID (foreign key to `accountcategory.id`)
- `account_type` - Account type ID (foreign key to `accounttype.id`)
- `account_role` - Account role (`both`, `debitor`, or `creditor`)
- `is_active` - Active status (`yes` or `no`)
- `description` - Account description
- `cash` - Cash payment mode enabled (0 or 1)
- `cheque` - Cheque payment mode enabled (0 or 1)
- `dd` - DD payment mode enabled (0 or 1)
- `bank_transfer` - Bank transfer payment mode enabled (0 or 1)
- `upi` - UPI payment mode enabled (0 or 1)
- `card` - Card payment mode enabled (0 or 1)
- `created_at` - Record creation timestamp
- `updated_at` - Record update date

**Related Tables:**
- `accountcategory` - Account category information
- `accounttype` - Account type information
- `accountcategorygroup` - Links categories to types

---

## Best Practices

1. **Unique Names and Codes** - Ensure account names and codes are unique before creating
2. **Payment Modes** - Select appropriate payment modes based on account type
3. **Account Roles** - Choose the correct role based on account usage:
   - `both`: Account can be used for both debit and credit
   - `debitor`: Account can only be debited
   - `creditor`: Account can only be credited
4. **Category and Type** - Select appropriate category and type for proper account classification
5. **Active Status** - Use `is_active` to enable/disable accounts without deleting them
6. **Descriptions** - Add meaningful descriptions for better account management
7. **Validation** - Always validate account names and codes before submission
8. **Filtering** - Use filters when listing accounts to improve performance
9. **Updates** - Only update necessary fields to avoid unintended changes
10. **Deletion** - Delete accounts carefully as they may be referenced in transactions

---

## Integration Notes

### Payment Modes
- Payment modes are stored as separate boolean fields in the database
- When creating/updating, provide payment modes as an array: `["cash", "cheque", "dd", "bank_transfer", "upi", "card"]`
- At least one payment mode must be selected
- The API automatically converts the array to individual boolean fields

### Account Categories and Types
- Account types are linked to categories through the `accountcategorygroup` table
- When filtering types by category, only types associated with that category are returned
- If no category filter is provided, all account types are returned

### Account Roles
- `both`: Account can be used for both debit and credit transactions
- `debitor`: Account can only be used as a debit account (source)
- `creditor`: Account can only be used as a credit account (destination)

### System Accounts
- System accounts (`is_system = 1`) are not returned by the API
- Only user-created accounts (`is_system = 0`) can be managed through the API

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | 2025-11-14 | Initial release with CRUD operations for accounts |

---

## Support

For issues or questions regarding this API, please contact the development team or refer to the main API documentation.

