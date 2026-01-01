# Account Report API Documentation

## Overview

The **Account Report API** provides RESTful endpoints for generating comprehensive account reports in the school management system. This API enables you to generate account reports with opening/closing balances, view account transactions, and access account information.

**Base URL:** `http://localhost/amt/api`

**Authentication:** All endpoints require the following headers:
- `Client-Service: smartschool`
- `Auth-Key: schoolAdmin@`
- `Content-Type: application/json`

---

## ⚠️ Important Notes

**All endpoints use POST method** - Even for retrieving data, you must use POST (not GET).

**URL Format** - Make sure your URL is correct:
- ❌ Wrong: `hhttp://localhost/amt/api/account-report-api/generate` (typo: "hhttp")
- ✅ Correct: `http://localhost/amt/api/account-report-api/generate`
- Always use `http://` (not `https://` or `hhttp://`)

**Request Body** - Even if the endpoint doesn't require data, you must send an empty JSON object `{}` in the request body.

**Date Format** - All dates should be in `Y-m-d` format (e.g., `2024-01-15`).

**Financial Year** - Account reports are calculated based on the active financial year. Opening and closing balances are calculated from the financial year start date.

---

## Endpoints

### 1. Generate Account Report

**Endpoint:** `POST /account-report-api/generate`

**Description:** Generates a comprehensive account report with opening/closing balances, transactions, and daily breakdown for a specific account within a date range.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Request Body:**
```json
{
  "account_id": 4,
  "date_from": "2024-01-01",
  "date_to": "2024-12-31"
}
```

**Request Parameters:**
| Parameter | Type | Required | Description | Example Values |
|-----------|------|----------|-------------|----------------|
| `account_id` | integer | Yes | ID of the account | `4`, `5`, `10` |
| `date_from` | string (Y-m-d) | Yes | Start date for the report | `"2024-01-01"` |
| `date_to` | string (Y-m-d) | Yes | End date for the report | `"2024-12-31"` |

**Important Notes:**
- Both `date_from` and `date_to` are required.
- `date_from` cannot be greater than `date_to`.
- Opening balance is calculated from the financial year start date to the day before `date_from`.
- Closing balance is calculated from the financial year start date to `date_to`.
- Daily breakdown includes opening/closing balances and transactions for each day in the date range.

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Account report generated successfully",
  "data": {
    "account": {
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
    "financial_year": {
      "year_id": 2024,
      "start_date": "2024-05-01",
      "end_date": "2025-05-01",
      "is_active": 1
    },
    "report_period": {
      "start_date": "2024-01-01",
      "end_date": "2024-12-31"
    },
    "opening_balance": 50000.00,
    "closing_balance": 125000.00,
    "opening_debit": 10000.00,
    "opening_credit": 60000.00,
    "closing_debit": 25000.00,
    "closing_credit": 150000.00,
    "transactions": [
      {
        "id": 1,
        "receiptid": "10338/1",
        "accountid": 4,
        "amount": 800,
        "date": "2024-05-07",
        "type": "fees",
        "is_active": "no",
        "description": "",
        "status": "credit",
        "created_at": "2024-05-07 16:29:13",
        "updated_at": null
      },
      {
        "id": 2,
        "receiptid": "10339/1",
        "accountid": 4,
        "amount": 2600,
        "date": "2024-05-08",
        "type": "fees",
        "is_active": "no",
        "description": "",
        "status": "credit",
        "created_at": "2024-05-08 13:44:20",
        "updated_at": null
      }
    ],
    "daily_data": [
      {
        "date": "2024-01-01",
        "opening_balance": 50000.00,
        "transactions": [
          {
            "id": 1,
            "receiptid": "10338/1",
            "accountid": 4,
            "amount": 800,
            "date": "2024-01-01",
            "type": "fees",
            "status": "credit"
          }
        ],
        "closing_balance": 50800.00
      },
      {
        "date": "2024-01-02",
        "opening_balance": 50800.00,
        "transactions": [],
        "closing_balance": 50800.00
      }
    ]
  }
}
```

**Error Responses:**

**400 Bad Request - Missing Required Fields:**
```json
{
  "status": 0,
  "message": "Invalid or missing account_id",
  "data": null
}
```

**400 Bad Request - Missing Dates:**
```json
{
  "status": 0,
  "message": "Both date_from and date_to are required",
  "data": null
}
```

**400 Bad Request - Invalid Date Format:**
```json
{
  "status": 0,
  "message": "Invalid date format. Use Y-m-d format (e.g., 2024-01-15)",
  "data": null
}
```

**400 Bad Request - Invalid Date Range:**
```json
{
  "status": 0,
  "message": "date_from cannot be greater than date_to",
  "data": null
}
```

**404 Not Found - Account Not Found:**
```json
{
  "status": 0,
  "message": "Account not found",
  "data": null
}
```

**404 Not Found - No Active Financial Year:**
```json
{
  "status": 0,
  "message": "No active financial year found",
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

### 2. Get Accounts List

**Endpoint:** `POST /account-report-api/accounts`

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

### 3. Get Account Transactions

**Endpoint:** `POST /account-report-api/transactions`

**Description:** Retrieves transactions for a specific account with optional date range and status filters.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Request Body:**
```json
{
  "account_id": 4,
  "date_from": "2024-01-01",
  "date_to": "2024-12-31",
  "status": "credit"
}
```

**Request Parameters:**
| Parameter | Type | Required | Description | Example Values |
|-----------|------|----------|-------------|----------------|
| `account_id` | integer | Yes | ID of the account | `4`, `5`, `10` |
| `date_from` | string (Y-m-d) | No | Start date for filtering | `"2024-01-01"` |
| `date_to` | string (Y-m-d) | No | End date for filtering | `"2024-12-31"` |
| `status` | string | No | Filter by transaction status (`credit` or `debit`) | `"credit"`, `"debit"` |

**Important Notes:**
- If `date_from` is provided, `date_to` must also be provided (and vice versa).
- If no date range is provided, all transactions for the account will be returned.
- If `status` is provided, only transactions with that status will be returned.
- Results are ordered by date (ASC) and ID (ASC).

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Account transactions retrieved successfully",
  "total_records": 10,
  "data": [
    {
      "id": 1,
      "receiptid": "10338/1",
      "accountid": 4,
      "amount": 800,
      "date": "2024-05-07",
      "type": "fees",
      "is_active": "no",
      "description": "",
      "status": "credit",
      "created_at": "2024-05-07 16:29:13",
      "updated_at": null
    },
    {
      "id": 2,
      "receiptid": "10339/1",
      "accountid": 4,
      "amount": 2600,
      "date": "2024-05-08",
      "type": "fees",
      "is_active": "no",
      "description": "",
      "status": "credit",
      "created_at": "2024-05-08 13:44:20",
      "updated_at": null
    }
  ]
}
```

**Error Responses:**

**400 Bad Request - Missing Account ID:**
```json
{
  "status": 0,
  "message": "Invalid or missing account_id",
  "data": null
}
```

**400 Bad Request - Incomplete Date Range:**
```json
{
  "status": 0,
  "message": "Both date_from and date_to are required when filtering by date",
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

### 4. Get Active Financial Year

**Endpoint:** `POST /account-report-api/active-financial-year`

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

## HTTP Status Codes

| Code | Meaning | Description |
|------|---------|-------------|
| 200 | OK | Request successful |
| 400 | Bad Request | Invalid input or missing required fields |
| 401 | Unauthorized | Invalid or missing authentication headers |
| 404 | Not Found | Resource not found (account, financial year) |
| 405 | Method Not Allowed | Wrong HTTP method used (must be POST) |
| 500 | Internal Server Error | Server error occurred |

---

## cURL Examples

### Example 1: Generate Account Report

```bash
curl -X POST "http://localhost/amt/api/account-report-api/generate" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "account_id": 4,
    "date_from": "2024-01-01",
    "date_to": "2024-12-31"
  }'
```

### Example 2: Get Accounts List

```bash
curl -X POST "http://localhost/amt/api/account-report-api/accounts" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 3: Get Account Transactions

```bash
curl -X POST "http://localhost/amt/api/account-report-api/transactions" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "account_id": 4,
    "date_from": "2024-01-01",
    "date_to": "2024-12-31"
  }'
```

### Example 4: Get Account Transactions (Credit Only)

```bash
curl -X POST "http://localhost/amt/api/account-report-api/transactions" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "account_id": 4,
    "date_from": "2024-01-01",
    "date_to": "2024-12-31",
    "status": "credit"
  }'
```

### Example 5: Get All Account Transactions (No Date Filter)

```bash
curl -X POST "http://localhost/amt/api/account-report-api/transactions" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "account_id": 4
  }'
```

### Example 6: Get Active Financial Year

```bash
curl -X POST "http://localhost/amt/api/account-report-api/active-financial-year" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

## Complete Workflow: Generate Account Report

### Step 1: Get Active Financial Year (Optional)

```bash
curl -X POST "http://localhost/amt/api/account-report-api/active-financial-year" \
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
curl -X POST "http://localhost/amt/api/account-report-api/accounts" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Step 3: Generate Account Report

```bash
curl -X POST "http://localhost/amt/api/account-report-api/generate" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "account_id": 4,
    "date_from": "2024-01-01",
    "date_to": "2024-12-31"
  }'
```

**Response:**
```json
{
  "status": 1,
  "message": "Account report generated successfully",
  "data": {
    "account": {
      "id": 4,
      "name": "Cash",
      "code": "1"
    },
    "financial_year": {
      "year_id": 2024,
      "start_date": "2024-05-01",
      "end_date": "2025-05-01"
    },
    "opening_balance": 50000.00,
    "closing_balance": 125000.00,
    "transactions": [...],
    "daily_data": [...]
  }
}
```

---

## Use Cases

### 1. **Account Balance Reporting**
   - Generate comprehensive account reports for specific periods
   - View opening and closing balances
   - Track account movements over time
   - Export account data for accounting purposes

### 2. **Financial Analysis**
   - Analyze account transactions by date range
   - Filter transactions by status (credit/debit)
   - Calculate account balances based on financial year
   - Generate daily breakdowns for detailed analysis

### 3. **Account Management**
   - List all available accounts
   - View account details and categories
   - Track account transactions
   - Monitor account activity

### 4. **Financial Year Integration**
   - Get active financial year information
   - Calculate balances from financial year start
   - Track transactions within financial year periods

### 5. **Transaction Tracking**
   - View all transactions for an account
   - Filter transactions by date range
   - Filter transactions by status (credit/debit)
   - Get detailed transaction information

---

## Database Schema

### accountreceipts Table

The account report system uses the `accountreceipts` table to store account transaction records with the following key fields:

- `id` (Primary Key) - Unique identifier for the transaction
- `receiptid` - Receipt ID/Number
- `accountid` - Account ID (foreign key to `addaccount.id`)
- `amount` - Transaction amount
- `date` - Transaction date
- `type` - Transaction type (e.g., "fees")
- `status` - Transaction status (`credit` or `debit`)
- `is_active` - Active status (`yes` or `no`)
- `description` - Transaction description
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

## Balance Calculation Logic

### Opening Balance
- Calculated from financial year start date to the day before the report start date
- Formula: `Opening Balance = Opening Credit - Opening Debit`
- Opening Credit: Sum of all credit transactions from financial year start to day before start date
- Opening Debit: Sum of all debit transactions from financial year start to day before start date

### Closing Balance
- Calculated from financial year start date to the report end date
- Formula: `Closing Balance = Closing Credit - Closing Debit`
- Closing Credit: Sum of all credit transactions from financial year start to end date
- Closing Debit: Sum of all debit transactions from financial year start to end date

### Daily Balance
- For each day in the date range:
  - Opening Balance: Balance from financial year start to day before current date
  - Closing Balance: Balance from financial year start to current date
  - Transactions: All transactions for the current date

---

## Best Practices

1. **Always validate account_id** before generating reports
2. **Use appropriate date ranges** to avoid large datasets
3. **Check active financial year** before calculating balances
4. **Use daily breakdown** for detailed day-by-day analysis
5. **Filter transactions by status** when needed (credit/debit)
6. **Handle null balances** appropriately (default to 0)
7. **Validate date formats** before sending requests
8. **Use transaction summaries** for quick overviews
9. **Cache account lists** to reduce API calls
10. **Monitor financial year changes** that may affect balance calculations

---

## Integration Notes

### Date Range Filtering
- Both `date_from` and `date_to` must be provided together if filtering by date
- Dates should be in `Y-m-d` format (e.g., `2024-01-15`)
- Date range is inclusive (transactions on both start and end dates are included)
- `date_from` cannot be greater than `date_to`

### Account Filtering
- Use `account_id` to filter transactions for a specific account
- Account must exist in the system
- Account ID must be a valid integer

### Transaction Status
- Use `status` filter to get only credit (`credit`) or debit (`debit`) transactions
- Leave empty to get all transactions regardless of status
- Status values are case-sensitive

### Financial Year Integration
- Get active financial year to determine default date ranges
- Use financial year dates for period-based reporting
- Track transactions within financial year boundaries
- Opening/closing balances are calculated from financial year start date

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | 2025-11-14 | Initial release with account report generation, transactions listing, and financial year endpoints |

---

## Support

For issues or questions regarding this API, please contact the development team or refer to the main API documentation.

