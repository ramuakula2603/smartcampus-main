# Account Transaction Report API Documentation

## Overview

The **Account Transaction Report API** provides RESTful endpoints for managing and retrieving account transaction reports in the school management system. This API enables you to view account transactions, get transaction summaries, manage accounts, and handle financial years.

**Base URL:** `http://localhost/amt/api`

**Authentication:** All endpoints require the following headers:
- `Client-Service: smartschool`
- `Auth-Key: schoolAdmin@`
- `Content-Type: application/json`

---

## ⚠️ Important Notes

**All endpoints use POST method** - Even for retrieving data, you must use POST (not GET).

**URL Format** - Make sure your URL is correct:
- ❌ Wrong: `hhttp://localhost/amt/api/account-transaction-report-api/list` (typo: "hhttp")
- ✅ Correct: `http://localhost/amt/api/account-transaction-report-api/list`
- Always use `http://` (not `https://` or `hhttp://`)

**URL Placeholders** - When you see `{id}` in the documentation, replace it with an actual number:
- ❌ Wrong: `http://localhost/amt/api/account-transaction-report-api/get/{id}`
- ✅ Correct: `http://localhost/amt/api/account-transaction-report-api/get/1`
- Example: For ID 5, use `/get/5` not `/get/{id}`

**Request Body** - Even if the endpoint doesn't require data, you must send an empty JSON object `{}` in the request body.

**Date Format** - All dates should be in `Y-m-d` format (e.g., `2024-01-15`).

---

## Endpoints

### 1. List Account Transactions

**Endpoint:** `POST /account-transaction-report-api/list`

**Description:** Retrieves a list of account transactions with optional date range and filter parameters.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Request Body:**
```json
{
  "date_from": "2024-01-01",
  "date_to": "2024-12-31",
  "from_account_id": 1,
  "to_account_id": 2,
  "is_active": "yes"
}
```

**Request Parameters:**
| Parameter | Type | Required | Description | Example Values |
|-----------|------|----------|-------------|----------------|
| `date_from` | string (Y-m-d) | No | Start date for filtering transactions | `"2024-01-01"` |
| `date_to` | string (Y-m-d) | No | End date for filtering transactions | `"2024-12-31"` |
| `from_account_id` | integer | No | Filter by source account ID | `1`, `5` |
| `to_account_id` | integer | No | Filter by destination account ID | `2`, `10` |
| `is_active` | string | No | Filter by active status (`yes` or `no`) | `"yes"`, `"no"` |

**Important Notes:**
- If `date_from` is provided, `date_to` must also be provided (and vice versa).
- If no date range is provided, all transactions will be returned.
- Results are ordered by date (DESC) and ID (DESC).

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Account transactions retrieved successfully",
  "total_records": 10,
  "data": [
    {
      "id": 1,
      "from_account_id": 4,
      "from_account_name": "Cash Account",
      "from_account_number": "ACC001",
      "to_account_id": 5,
      "to_account_name": "Bank Account",
      "to_account_number": "ACC002",
      "amount": 53512,
      "date": "2024-05-03",
      "is_active": "no",
      "note": "old software account balancing",
      "created_at": "2024-05-24 16:29:35",
      "updated_at": null
    },
    {
      "id": 2,
      "from_account_id": 56,
      "from_account_name": "Petty Cash",
      "from_account_number": "ACC003",
      "to_account_id": 5,
      "to_account_name": "Bank Account",
      "to_account_number": "ACC002",
      "amount": 13462,
      "date": "2024-05-03",
      "is_active": "no",
      "note": "old software account balancing",
      "created_at": "2024-05-24 16:40:45",
      "updated_at": null
    }
  ]
}
```

**Error Responses:**

**400 Bad Request - Incomplete Date Range:**
```json
{
  "status": 0,
  "message": "Both date_from and date_to are required when filtering by date",
  "data": null
}
```

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

### 2. Get Transaction Summary

**Endpoint:** `POST /account-transaction-report-api/summary`

**Description:** Retrieves summary statistics for account transactions within a date range.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Request Body:**
```json
{
  "date_from": "2024-01-01",
  "date_to": "2024-12-31"
}
```

**Request Parameters:**
| Parameter | Type | Required | Description | Example Values |
|-----------|------|----------|-------------|----------------|
| `date_from` | string (Y-m-d) | No | Start date for summary calculation | `"2024-01-01"` |
| `date_to` | string (Y-m-d) | No | End date for summary calculation | `"2024-12-31"` |

**Important Notes:**
- If `date_from` is provided, `date_to` must also be provided (and vice versa).
- If no date range is provided, summary will be calculated for all transactions.

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Transaction summary retrieved successfully",
  "data": {
    "total_transactions": 76,
    "total_amount": 1250000.00
  }
}
```

**Error Responses:**

**400 Bad Request - Incomplete Date Range:**
```json
{
  "status": 0,
  "message": "Both date_from and date_to are required when filtering by date",
  "data": null
}
```

---

### 3. Get Accounts List

**Endpoint:** `POST /account-transaction-report-api/accounts`

**Description:** Retrieves a list of all accounts with their categories and types.

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
  "message": "Accounts retrieved successfully",
  "total_records": 25,
  "data": [
    {
      "id": 1,
      "account_name": "Cash Account",
      "account_number": "ACC001",
      "account_category": 1,
      "account_category_name": "Assets",
      "account_type": 1,
      "account_type": "Current Asset"
    },
    {
      "id": 2,
      "account_name": "Bank Account",
      "account_number": "ACC002",
      "account_category": 1,
      "account_category_name": "Assets",
      "account_type": 1,
      "account_type": "Current Asset"
    }
  ]
}
```

---

### 4. Get Financial Years List

**Endpoint:** `POST /account-transaction-report-api/financial-years`

**Description:** Retrieves a list of all financial years.

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
  "message": "Financial years retrieved successfully",
  "total_records": 2,
  "data": [
    {
      "year_id": 2025,
      "start_date": "2024-06-01",
      "end_date": "2025-06-01",
      "is_active": 0
    },
    {
      "year_id": 2024,
      "start_date": "2024-05-01",
      "end_date": "2025-05-01",
      "is_active": 1
    }
  ]
}
```

---

### 5. Get Active Financial Year

**Endpoint:** `POST /account-transaction-report-api/active-financial-year`

**Description:** Retrieves the currently active financial year.

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
  "message": "Active financial year retrieved successfully",
  "data": {
    "year_id": 2024,
    "start_date": "2024-05-01",
    "end_date": "2025-05-01",
    "is_active": 1
  }
}
```

**Error Responses:**

**404 Not Found:**
```json
{
  "status": 0,
  "message": "No active financial year found",
  "data": null
}
```

---

### 6. Get Specific Transaction

**Endpoint:** `POST /account-transaction-report-api/get/{id}`

**Description:** Retrieves detailed information about a specific transaction by its ID.

**⚠️ Important:** 
- **Method must be POST** (not GET)
- Replace `{id}` with an actual transaction ID number (e.g., use `1` instead of `{id}`)
- Example URL: `http://localhost/amt/api/account-transaction-report-api/get/1`

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
    "id": 1,
    "from_account_id": 4,
    "from_account_name": "Cash Account",
    "from_account_number": "ACC001",
    "to_account_id": 5,
    "to_account_name": "Bank Account",
    "to_account_number": "ACC002",
    "amount": 53512,
    "date": "2024-05-03",
    "is_active": "no",
    "note": "old software account balancing",
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

### 7. Delete Transaction

**Endpoint:** `POST /account-transaction-report-api/delete/{id}`

**Description:** Deletes a specific transaction by its ID.

**⚠️ Important:** 
- **Method must be POST** (not DELETE)
- Replace `{id}` with an actual transaction ID number (e.g., use `1` instead of `{id}`)
- Example URL: `http://localhost/amt/api/account-transaction-report-api/delete/1`

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
| 200 | OK | Request successful |
| 400 | Bad Request | Invalid input or missing required fields |
| 401 | Unauthorized | Invalid or missing authentication headers |
| 404 | Not Found | Resource not found (transaction, financial year) |
| 405 | Method Not Allowed | Wrong HTTP method used (must be POST) |
| 500 | Internal Server Error | Server error occurred |

---

## cURL Examples

### Example 1: List Transactions with Date Range

```bash
curl -X POST "http://localhost/amt/api/account-transaction-report-api/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "date_from": "2024-01-01",
    "date_to": "2024-12-31"
  }'
```

### Example 2: List All Transactions

```bash
curl -X POST "http://localhost/amt/api/account-transaction-report-api/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 3: List Transactions with Filters

```bash
curl -X POST "http://localhost/amt/api/account-transaction-report-api/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "date_from": "2024-01-01",
    "date_to": "2024-12-31",
    "from_account_id": 4,
    "is_active": "yes"
  }'
```

### Example 4: Get Transaction Summary

```bash
curl -X POST "http://localhost/amt/api/account-transaction-report-api/summary" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "date_from": "2024-01-01",
    "date_to": "2024-12-31"
  }'
```

### Example 5: Get Accounts List

```bash
curl -X POST "http://localhost/amt/api/account-transaction-report-api/accounts" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 6: Get Financial Years

```bash
curl -X POST "http://localhost/amt/api/account-transaction-report-api/financial-years" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 7: Get Active Financial Year

```bash
curl -X POST "http://localhost/amt/api/account-transaction-report-api/active-financial-year" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 8: Get Specific Transaction

```bash
curl -X POST "http://localhost/amt/api/account-transaction-report-api/get/1" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 9: Delete Transaction

```bash
curl -X POST "http://localhost/amt/api/account-transaction-report-api/delete/1" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

## Complete Workflow: Account Transaction Report

### Step 1: Get Active Financial Year

```bash
curl -X POST "http://localhost/amt/api/account-transaction-report-api/active-financial-year" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Response:**
```json
{
  "status": 1,
  "message": "Active financial year retrieved successfully",
  "data": {
    "year_id": 2024,
    "start_date": "2024-05-01",
    "end_date": "2025-05-01",
    "is_active": 1
  }
}
```

### Step 2: Get Accounts List (Optional)

```bash
curl -X POST "http://localhost/amt/api/account-transaction-report-api/accounts" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Step 3: List Transactions by Date Range

```bash
curl -X POST "http://localhost/amt/api/account-transaction-report-api/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "date_from": "2024-01-01",
    "date_to": "2024-12-31"
  }'
```

**Response:**
```json
{
  "status": 1,
  "message": "Account transactions retrieved successfully",
  "total_records": 10,
  "data": [
    {
      "id": 1,
      "from_account_id": 4,
      "from_account_name": "Cash Account",
      "to_account_id": 5,
      "to_account_name": "Bank Account",
      "amount": 53512,
      "date": "2024-05-03"
    }
  ]
}
```

### Step 4: Get Transaction Summary

```bash
curl -X POST "http://localhost/amt/api/account-transaction-report-api/summary" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "date_from": "2024-01-01",
    "date_to": "2024-12-31"
  }'
```

**Response:**
```json
{
  "status": 1,
  "message": "Transaction summary retrieved successfully",
  "data": {
    "total_transactions": 76,
    "total_amount": 1250000.00
  }
}
```

### Step 5: Get Specific Transaction Details (if needed)

```bash
curl -X POST "http://localhost/amt/api/account-transaction-report-api/get/1" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

## Use Cases

### 1. **Account Transaction Reporting**
   - Generate transaction reports by date range
   - Filter transactions by source or destination account
   - View transaction details and notes
   - Export transaction data for accounting purposes

### 2. **Financial Analysis**
   - Get transaction summaries for specific periods
   - Calculate total transaction amounts
   - Track transaction counts
   - Analyze account movements

### 3. **Account Management**
   - List all available accounts
   - View account categories and types
   - Filter transactions by account

### 4. **Financial Year Management**
   - View all financial years
   - Get active financial year information
   - Track financial periods

### 5. **Transaction Management**
   - View individual transaction details
   - Delete incorrect transactions
   - Track transaction history

---

## Database Schema

### accounttranscations Table

The account transaction system uses the `accounttranscations` table to store transaction records with the following key fields:

- `id` (Primary Key) - Unique identifier for the transaction
- `fromaccountid` - Source account ID (foreign key to `addaccount.id`)
- `toaccountid` - Destination account ID (foreign key to `addaccount.id`)
- `amount` - Transaction amount
- `date` - Transaction date
- `is_active` - Active status (`yes` or `no`)
- `note` - Transaction notes/description
- `created_at` - Record creation timestamp
- `updated_at` - Record update date

**Related Tables:**
- `addaccount` - Account information
- `accounttype` - Account type information
- `accountcategory` - Account category information
- `financialyear` - Financial year information

### financialyear Table

- `year_id` (Primary Key) - Financial year identifier
- `start_date` - Financial year start date
- `end_date` - Financial year end date
- `is_active` - Active status (0=inactive, 1=active)

---

## Best Practices

1. **Always use date ranges** when querying transactions for better performance
2. **Validate date formats** before sending requests (use Y-m-d format)
3. **Use filters** to narrow down transaction lists
4. **Check active financial year** before generating reports
5. **Handle error responses** appropriately in your application
6. **Log all API calls** for audit and debugging purposes
7. **Use transaction summaries** for quick overviews instead of fetching all records
8. **Verify account IDs** before filtering by account
9. **Check transaction existence** before deletion
10. **Use appropriate date ranges** based on financial year periods

---

## Integration Notes

### Date Range Filtering
- Both `date_from` and `date_to` must be provided together if filtering by date
- Dates should be in `Y-m-d` format (e.g., `2024-01-15`)
- Date range is inclusive (transactions on both start and end dates are included)

### Account Filtering
- Use `from_account_id` to filter transactions from a specific account
- Use `to_account_id` to filter transactions to a specific account
- Both filters can be combined for more specific results

### Transaction Status
- Use `is_active` filter to get only active (`yes`) or inactive (`no`) transactions
- Leave empty to get all transactions regardless of status

### Financial Year Integration
- Get active financial year to determine default date ranges
- Use financial year dates for period-based reporting
- Track transactions within financial year boundaries

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | 2025-11-14 | Initial release with transaction listing, summary, accounts, and financial year endpoints |

---

## Support

For issues or questions regarding this API, please contact the development team or refer to the main API documentation.

