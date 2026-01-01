# Account Transaction API Documentation

## Overview

The **Account Transaction API** provides RESTful endpoints for managing account transactions in the school management system. This API enables you to create, read, update, and delete account transactions between debit and credit accounts.

**Base URL:** `http://localhost/amt/api`

**Authentication:** All endpoints require the following headers:
- `Client-Service: smartschool`
- `Auth-Key: schoolAdmin@`
- `Content-Type: application/json`

---

## ⚠️ Important Notes

**All endpoints use POST method** - Even for retrieving data, you must use POST (not GET).

**URL Format** - Make sure your URL is correct:
- ❌ Wrong: `hhttp://localhost/amt/api/account-transaction-api/create` (typo: "hhttp")
- ✅ Correct: `http://localhost/amt/api/account-transaction-api/create`
- Always use `http://` (not `https://` or `hhttp://`)

**URL Placeholders** - When you see `{id}` in the documentation, replace it with an actual number:
- ❌ Wrong: `http://localhost/amt/api/account-transaction-api/get/{id}`
- ✅ Correct: `http://localhost/amt/api/account-transaction-api/get/1`
- Example: For ID 5, use `/get/5` not `/get/{id}`

**Request Body** - Even if the endpoint doesn't require data, you must send an empty JSON object `{}` in the request body.

**Date Format** - All dates should be in `Y-m-d` format (e.g., `2024-01-15`).

**Transaction Logic** - When creating a transaction:
- A record is created in `accounttranscations` table
- Two records are automatically created in `accountreceipts` table (one for debit, one for credit)
- The debit account receives a debit entry
- The credit account receives a credit entry

---

## Endpoints

### 1. Get Debit Accounts

**Endpoint:** `POST /account-transaction-api/debit-accounts`

**Description:** Retrieves a list of all accounts that can be used as debit accounts (accounts that are not restricted to credit-only).

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
  "message": "Debit accounts retrieved successfully",
  "total_records": 15,
  "data": [
    {
      "id": 4,
      "name": "Cash",
      "code": "1",
      "account_category": 3,
      "account_category_name": "Assets",
      "account_type": 4,
      "account_type": "Current Asset",
      "account_role": "both",
      "is_active": "no",
      "description": "",
      "cash": 1,
      "cheque": 0,
      "dd": 0,
      "bank_transfer": 0,
      "upi": 0,
      "card": 0
    },
    {
      "id": 5,
      "name": "HDFC Bank",
      "code": "2",
      "account_category": 3,
      "account_category_name": "Assets",
      "account_type": 5,
      "account_type": "Bank Account",
      "account_role": "both",
      "is_active": "no",
      "description": "",
      "cash": 0,
      "cheque": 1,
      "dd": 1,
      "bank_transfer": 1,
      "upi": 1,
      "card": 1
    }
  ]
}
```

---

### 2. Get Credit Accounts

**Endpoint:** `POST /account-transaction-api/credit-accounts`

**Description:** Retrieves a list of all accounts that can be used as credit accounts (accounts that are not restricted to debit-only).

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
  "message": "Credit accounts retrieved successfully",
  "total_records": 20,
  "data": [
    {
      "id": 4,
      "name": "Cash",
      "code": "1",
      "account_category": 3,
      "account_category_name": "Assets",
      "account_type": 4,
      "account_type": "Current Asset",
      "account_role": "both",
      "is_active": "no",
      "description": "",
      "cash": 1,
      "cheque": 0,
      "dd": 0,
      "bank_transfer": 0,
      "upi": 0,
      "card": 0
    }
  ]
}
```

---

### 3. Create Account Transaction

**Endpoint:** `POST /account-transaction-api/create`

**Description:** Creates a new account transaction between a debit account and a credit account. This automatically creates entries in both `accounttranscations` and `accountreceipts` tables.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Request Body:**
```json
{
  "from_account_id": 4,
  "to_account_id": 5,
  "amount": 10000.00,
  "date": "2024-11-14",
  "note": "Transfer from Cash to Bank",
  "is_active": "yes"
}
```

**Request Parameters:**
| Parameter | Type | Required | Description | Example Values |
|-----------|------|----------|-------------|----------------|
| `from_account_id` | integer | Yes | ID of the debit account (source account) | `4`, `5`, `10` |
| `to_account_id` | integer | Yes | ID of the credit account (destination account) | `4`, `5`, `10` |
| `amount` | float | Yes | Transaction amount (must be greater than 0) | `1000.00`, `5000.50` |
| `date` | string (Y-m-d) | Yes | Transaction date | `"2024-11-14"` |
| `note` | string | No | Transaction description/notes | `"Transfer from Cash to Bank"` |
| `is_active` | string | No | Active status (`yes` or `no`). Default: `yes` | `"yes"`, `"no"` |

**Important Notes:**
- `from_account_id` and `to_account_id` must be different accounts
- Both accounts must exist in the system
- Amount must be greater than 0
- Date must be in `Y-m-d` format
- Creating a transaction automatically creates debit and credit entries in `accountreceipts` table

**Success Response (201 Created):**
```json
{
  "status": 1,
  "message": "Account transaction created successfully",
  "data": {
    "id": 77,
    "from_account_id": 4,
    "from_account_name": "Cash",
    "from_account_number": "1",
    "to_account_id": 5,
    "to_account_name": "HDFC Bank",
    "to_account_number": "2",
    "amount": 10000.00,
    "date": "2024-11-14",
    "is_active": "yes",
    "note": "Transfer from Cash to Bank",
    "created_at": "2024-11-14 16:20:00",
    "updated_at": null
  }
}
```

**Error Responses:**

**400 Bad Request - Missing Required Fields:**
```json
{
  "status": 0,
  "message": "Invalid or missing from_account_id (debit account)",
  "data": null
}
```

**400 Bad Request - Invalid Amount:**
```json
{
  "status": 0,
  "message": "Invalid or missing amount. Amount must be greater than 0",
  "data": null
}
```

**400 Bad Request - Same Account:**
```json
{
  "status": 0,
  "message": "Debit account and credit account cannot be the same",
  "data": null
}
```

**404 Not Found - Account Not Found:**
```json
{
  "status": 0,
  "message": "Debit account not found",
  "data": null
}
```

**500 Internal Server Error:**
```json
{
  "status": 0,
  "message": "Failed to create account transaction",
  "data": null
}
```

---

### 4. List Account Transactions

**Endpoint:** `POST /account-transaction-api/list`

**Description:** Retrieves a list of account transactions with optional filters.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Request Body:**
```json
{
  "from_account_id": 4,
  "to_account_id": 5,
  "date_from": "2024-01-01",
  "date_to": "2024-12-31",
  "is_active": "yes"
}
```

**Request Parameters:**
| Parameter | Type | Required | Description | Example Values |
|-----------|------|----------|-------------|----------------|
| `from_account_id` | integer | No | Filter by debit account ID | `4`, `5`, `10` |
| `to_account_id` | integer | No | Filter by credit account ID | `4`, `5`, `10` |
| `date_from` | string (Y-m-d) | No | Filter by start date | `"2024-01-01"` |
| `date_to` | string (Y-m-d) | No | Filter by end date | `"2024-12-31"` |
| `is_active` | string | No | Filter by active status (`yes` or `no`) | `"yes"`, `"no"` |

**Important Notes:**
- If `date_from` is provided, `date_to` can also be provided for date range filtering
- Results are ordered by date (DESC) and ID (DESC)
- If no filters are provided, all transactions will be returned

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Account transactions retrieved successfully",
  "total_records": 10,
  "data": [
    {
      "id": 77,
      "from_account_id": 4,
      "from_account_name": "Cash",
      "from_account_number": "1",
      "to_account_id": 5,
      "to_account_name": "HDFC Bank",
      "to_account_number": "2",
      "amount": 10000.00,
      "date": "2024-11-14",
      "is_active": "yes",
      "note": "Transfer from Cash to Bank",
      "created_at": "2024-11-14 16:20:00",
      "updated_at": null
    },
    {
      "id": 76,
      "from_account_id": 5,
      "from_account_name": "HDFC Bank",
      "from_account_number": "2",
      "to_account_id": 12,
      "to_account_name": "AMT Branch Maintenance",
      "to_account_number": "8",
      "amount": 50000.00,
      "date": "2024-05-28",
      "is_active": "no",
      "note": "AMT PAINTING (SHOP) : 38650...",
      "created_at": "2024-05-28 10:10:00",
      "updated_at": null
    }
  ]
}
```

---

### 5. Get Specific Transaction

**Endpoint:** `POST /account-transaction-api/get/{id}`

**Description:** Retrieves detailed information about a specific account transaction by its ID.

**⚠️ Important:** 
- **Method must be POST** (not GET)
- Replace `{id}` with an actual transaction ID number (e.g., use `1` instead of `{id}`)
- Example URL: `http://localhost/amt/api/account-transaction-api/get/1`

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**URL Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `id` | integer | Yes | ID of the transaction | `1`, `2`, `5` |

**Request Body:** `{}` (empty JSON object)

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Transaction retrieved successfully",
  "data": {
    "id": 77,
    "from_account_id": 4,
    "from_account_name": "Cash",
    "from_account_number": "1",
    "to_account_id": 5,
    "to_account_name": "HDFC Bank",
    "to_account_number": "2",
    "amount": 10000.00,
    "date": "2024-11-14",
    "is_active": "yes",
    "note": "Transfer from Cash to Bank",
    "created_at": "2024-11-14 16:20:00",
    "updated_at": null
  }
}
```

**Error Responses:**

**400 Bad Request - Invalid ID:**
```json
{
  "status": 0,
  "message": "Invalid or missing transaction ID",
  "data": null
}
```

**404 Not Found:**
```json
{
  "status": 0,
  "message": "Transaction not found",
  "data": null
}
```

---

### 6. Update Transaction

**Endpoint:** `POST /account-transaction-api/update/{id}`

**Description:** Updates an existing account transaction. Only provided fields will be updated.

**⚠️ Important:** 
- **Method must be POST** (not PUT)
- Replace `{id}` with an actual transaction ID number (e.g., use `1` instead of `{id}`)
- Example URL: `http://localhost/amt/api/account-transaction-api/update/1`

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**URL Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `id` | integer | Yes | ID of the transaction to update | `1`, `2`, `5` |

**Request Body:**
```json
{
  "amount": 15000.00,
  "date": "2024-11-15",
  "note": "Updated transfer amount"
}
```

**Request Parameters (All Optional):**
| Parameter | Type | Required | Description | Example Values |
|-----------|------|----------|-------------|----------------|
| `from_account_id` | integer | No | Update debit account ID | `4`, `5`, `10` |
| `to_account_id` | integer | No | Update credit account ID | `4`, `5`, `10` |
| `amount` | float | No | Update transaction amount | `15000.00`, `5000.50` |
| `date` | string (Y-m-d) | No | Update transaction date | `"2024-11-15"` |
| `note` | string | No | Update transaction notes | `"Updated description"` |
| `is_active` | string | No | Update active status | `"yes"`, `"no"` |

**Important Notes:**
- Only provide fields you want to update
- If `from_account_id` and `to_account_id` are both provided, they must be different
- If account IDs are updated, corresponding `accountreceipts` entries will also be updated
- If amount or date is updated, corresponding `accountreceipts` entries will also be updated

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Transaction updated successfully",
  "data": {
    "id": 77,
    "from_account_id": 4,
    "from_account_name": "Cash",
    "from_account_number": "1",
    "to_account_id": 5,
    "to_account_name": "HDFC Bank",
    "to_account_number": "2",
    "amount": 15000.00,
    "date": "2024-11-15",
    "is_active": "yes",
    "note": "Updated transfer amount",
    "created_at": "2024-11-14 16:20:00",
    "updated_at": "2024-11-15 10:30:00"
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

**400 Bad Request - Same Account:**
```json
{
  "status": 0,
  "message": "Debit account and credit account cannot be the same",
  "data": null
}
```

**404 Not Found:**
```json
{
  "status": 0,
  "message": "Transaction not found",
  "data": null
}
```

---

### 7. Delete Transaction

**Endpoint:** `POST /account-transaction-api/delete/{id}`

**Description:** Deletes an account transaction. This also deletes the corresponding entries in the `accountreceipts` table.

**⚠️ Important:** 
- **Method must be POST** (not DELETE)
- Replace `{id}` with an actual transaction ID number (e.g., use `1` instead of `{id}`)
- Example URL: `http://localhost/amt/api/account-transaction-api/delete/1`

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**URL Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `id` | integer | Yes | ID of the transaction to delete | `1`, `2`, `5` |

**Request Body:** `{}` (empty JSON object)

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Transaction deleted successfully",
  "data": null
}
```

**Error Responses:**

**400 Bad Request - Invalid ID:**
```json
{
  "status": 0,
  "message": "Invalid or missing transaction ID",
  "data": null
}
```

**404 Not Found:**
```json
{
  "status": 0,
  "message": "Transaction not found",
  "data": null
}
```

**500 Internal Server Error:**
```json
{
  "status": 0,
  "message": "Failed to delete transaction",
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
| 404 | Not Found | Resource not found (transaction, account) |
| 405 | Method Not Allowed | Wrong HTTP method used (must be POST) |
| 500 | Internal Server Error | Server error occurred |

---

## cURL Examples

### Example 1: Get Debit Accounts

```bash
curl -X POST "http://localhost/amt/api/account-transaction-api/debit-accounts" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 2: Get Credit Accounts

```bash
curl -X POST "http://localhost/amt/api/account-transaction-api/credit-accounts" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 3: Create Account Transaction

```bash
curl -X POST "http://localhost/amt/api/account-transaction-api/create" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "from_account_id": 4,
    "to_account_id": 5,
    "amount": 10000.00,
    "date": "2024-11-14",
    "note": "Transfer from Cash to Bank",
    "is_active": "yes"
  }'
```

### Example 4: List All Transactions

```bash
curl -X POST "http://localhost/amt/api/account-transaction-api/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 5: List Transactions with Filters

```bash
curl -X POST "http://localhost/amt/api/account-transaction-api/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "from_account_id": 4,
    "date_from": "2024-01-01",
    "date_to": "2024-12-31",
    "is_active": "yes"
  }'
```

### Example 6: Get Specific Transaction

```bash
curl -X POST "http://localhost/amt/api/account-transaction-api/get/77" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 7: Update Transaction

```bash
curl -X POST "http://localhost/amt/api/account-transaction-api/update/77" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "amount": 15000.00,
    "note": "Updated transfer amount"
  }'
```

### Example 8: Delete Transaction

```bash
curl -X POST "http://localhost/amt/api/account-transaction-api/delete/77" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

## Complete Workflow: Create Account Transaction

### Step 1: Get Debit Accounts

```bash
curl -X POST "http://localhost/amt/api/account-transaction-api/debit-accounts" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Response:**
```json
{
  "status": 1,
  "message": "Debit accounts retrieved successfully",
  "total_records": 15,
  "data": [
    {
      "id": 4,
      "name": "Cash",
      "code": "1"
    }
  ]
}
```

### Step 2: Get Credit Accounts

```bash
curl -X POST "http://localhost/amt/api/account-transaction-api/credit-accounts" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Response:**
```json
{
  "status": 1,
  "message": "Credit accounts retrieved successfully",
  "total_records": 20,
  "data": [
    {
      "id": 5,
      "name": "HDFC Bank",
      "code": "2"
    }
  ]
}
```

### Step 3: Create Transaction

```bash
curl -X POST "http://localhost/amt/api/account-transaction-api/create" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "from_account_id": 4,
    "to_account_id": 5,
    "amount": 10000.00,
    "date": "2024-11-14",
    "note": "Transfer from Cash to Bank"
  }'
```

**Response:**
```json
{
  "status": 1,
  "message": "Account transaction created successfully",
  "data": {
    "id": 77,
    "from_account_id": 4,
    "from_account_name": "Cash",
    "to_account_id": 5,
    "to_account_name": "HDFC Bank",
    "amount": 10000.00,
    "date": "2024-11-14",
    "note": "Transfer from Cash to Bank"
  }
}
```

---

## Use Cases

### 1. **Account Transfer**
   - Transfer funds from one account to another
   - Record cash deposits to bank
   - Record bank withdrawals
   - Track inter-account movements

### 2. **Transaction Management**
   - Create new transactions
   - Update existing transactions
   - Delete incorrect transactions
   - View transaction history

### 3. **Account Reconciliation**
   - List transactions by account
   - Filter transactions by date range
   - Track all debit and credit entries
   - Verify account balances

### 4. **Financial Reporting**
   - Generate transaction reports
   - Filter by account, date, or status
   - Export transaction data
   - Audit transaction history

---

## Database Schema

### accounttranscations Table

The account transaction system uses the `accounttranscations` table to store transaction records:

- `id` (Primary Key) - Unique identifier for the transaction
- `fromaccountid` - Debit account ID (foreign key to `addaccount.id`)
- `toaccountid` - Credit account ID (foreign key to `addaccount.id`)
- `amount` - Transaction amount
- `date` - Transaction date
- `is_active` - Active status (`yes` or `no`)
- `note` - Transaction notes/description
- `created_at` - Record creation timestamp
- `updated_at` - Record update date

### accountreceipts Table

When a transaction is created, two entries are automatically created in the `accountreceipts` table:

- **Debit Entry:**
  - `receiptid` - Transaction ID (foreign key to `accounttranscations.id`)
  - `accountid` - Debit account ID
  - `amount` - Transaction amount
  - `date` - Transaction date
  - `type` - Credit account name
  - `description` - Transaction notes
  - `status` - `"debit"`

- **Credit Entry:**
  - `receiptid` - Transaction ID (foreign key to `accounttranscations.id`)
  - `accountid` - Credit account ID
  - `amount` - Transaction amount
  - `date` - Transaction date
  - `type` - Debit account name
  - `description` - Transaction notes
  - `status` - `"credit"`

**Related Tables:**
- `addaccount` - Account information
- `accounttype` - Account type information
- `accountcategory` - Account category information

---

## Best Practices

1. **Always validate account IDs** before creating transactions
2. **Ensure accounts are different** - debit and credit accounts must be different
3. **Use appropriate date formats** - always use `Y-m-d` format (e.g., `2024-11-14`)
4. **Validate amounts** - ensure amounts are greater than 0
5. **Use descriptive notes** - add meaningful descriptions for audit purposes
6. **Filter transactions** - use filters to narrow down transaction lists
7. **Handle errors appropriately** - check for 404 errors when accounts don't exist
8. **Update carefully** - only update necessary fields to avoid unintended changes
9. **Delete with caution** - deleting a transaction also deletes related receipt entries
10. **Track transaction history** - use list endpoint to view all transactions

---

## Integration Notes

### Account Roles
- **Debit Accounts**: Accounts with role `both` or `debitor` (not `creditor`)
- **Credit Accounts**: Accounts with role `both` or `creditor` (not `debitor`)
- Use `debit-accounts` and `credit-accounts` endpoints to get appropriate account lists

### Transaction Creation
- Creating a transaction automatically creates debit and credit entries in `accountreceipts`
- The transaction ID is used as `receiptid` in the receipt entries
- Account names are automatically populated in the receipt entries

### Transaction Updates
- Updating account IDs, amount, or date will automatically update corresponding receipt entries
- Only provided fields will be updated (partial updates are supported)
- Receipt entries are updated to maintain data consistency

### Transaction Deletion
- Deleting a transaction also deletes the corresponding debit and credit entries in `accountreceipts`
- This ensures data consistency across both tables
- Deletion is permanent and cannot be undone

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | 2025-11-14 | Initial release with CRUD operations for account transactions |

---

## Support

For issues or questions regarding this API, please contact the development team or refer to the main API documentation.

