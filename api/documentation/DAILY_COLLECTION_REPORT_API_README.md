# Daily Collection Report API Documentation

## Overview

The Daily Collection Report API provides endpoints to retrieve daily fee collection data for a specified date range. This API shows the total amount collected each day, including both regular fees and other fees (additional fees), along with transaction counts and deposit IDs. It's designed for financial reporting and analysis of daily collection patterns.

**Key Features:**
- **Date Range Filtering:** Filter collections by custom date range (from date to date)
- **Dual Fee Tracking:** Separate tracking for regular fees and other fees (additional fees)
- **Daily Aggregation:** Collections are aggregated by date with amount totals and transaction counts
- **Default Behavior:** Returns current month's collections when no filters are provided
- **Fine Inclusion:** Includes fine amounts in the total collection

**Base URL:** `http://localhost/amt/api`

---

## Authentication

All endpoints require authentication headers:

```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

---

## Endpoints

### 1. Filter Daily Collection Report

**Endpoint:** `POST /api/daily-collection-report/filter`

**Description:** Retrieves daily collection data for a specified date range. Returns separate arrays for regular fees and other fees (additional fees), with daily totals, transaction counts, and deposit IDs.

#### Request Body (All Parameters Optional)

```json
{
  "date_from": "2025-01-01",
  "date_to": "2025-01-31"
}
```

#### Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| date_from | string | No | Start date for the report (YYYY-MM-DD format). Defaults to first day of current month |
| date_to | string | No | End date for the report (YYYY-MM-DD format). Defaults to last day of current month |

**Important Notes:**
- Empty request `{}` returns current month's collection (from 1st to last day of current month)
- Date format must be `YYYY-MM-DD` (e.g., "2025-01-15")
- Both dates are inclusive in the range
- The API initializes all dates in the range with zero values, then populates actual collection data
- Collections include both payment amount and fine amount

#### Response Format

**Success Response (200 OK):**

```json
{
  "status": 1,
  "message": "Daily collection report retrieved successfully",
  "filters_applied": {
    "date_from": "2025-01-01",
    "date_to": "2025-01-31"
  },
  "total_records": 31,
  "fees_data": [
    {
      "date": "2025-01-01",
      "amt": 15000.00,
      "count": 5,
      "student_fees_deposite_ids": [101, 102, 103, 104, 105]
    },
    {
      "date": "2025-01-02",
      "amt": 22500.00,
      "count": 8,
      "student_fees_deposite_ids": [106, 107, 108, 109, 110, 111, 112, 113]
    },
    {
      "date": "2025-01-03",
      "amt": 0,
      "count": 0,
      "student_fees_deposite_ids": []
    }
  ],
  "other_fees_data": [
    {
      "date": "2025-01-05",
      "amt": 5000.00,
      "count": 2,
      "student_fees_deposite_ids": [201, 202]
    },
    {
      "date": "2025-01-10",
      "amt": 3500.00,
      "count": 1,
      "student_fees_deposite_ids": [203]
    }
  ],
  "timestamp": "2025-10-10 10:00:00"
}
```

**Error Response (401 Unauthorized):**

```json
{
  "status": 0,
  "message": "Unauthorized access"
}
```

**Error Response (400 Bad Request):**

```json
{
  "status": 0,
  "message": "Bad request. Only POST method allowed."
}
```

**Error Response (500 Internal Server Error):**

```json
{
  "status": 0,
  "message": "Internal server error",
  "error": "Error details here"
}
```

---

### 2. List Filter Options

**Endpoint:** `POST /api/daily-collection-report/list`

**Description:** Retrieves suggested date ranges for filtering the daily collection report.

#### Request Body

```json
{}
```

No parameters required.

#### Response Format

**Success Response (200 OK):**

```json
{
  "status": 1,
  "message": "Daily collection filter options retrieved successfully",
  "date_ranges": [
    {
      "label": "This Month",
      "date_from": "2025-10-01",
      "date_to": "2025-10-31"
    },
    {
      "label": "Last Month",
      "date_from": "2025-09-01",
      "date_to": "2025-09-30"
    },
    {
      "label": "This Year",
      "date_from": "2025-01-01",
      "date_to": "2025-12-31"
    }
  ],
  "note": "Use the filter endpoint with date_from and date_to to get daily collection report",
  "timestamp": "2025-10-10 10:00:00"
}
```

---

## Usage Examples

### Example 1: Get Current Month's Collection (No Filters)

```bash
curl -X POST "http://localhost/amt/api/daily-collection-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Result:** Returns daily collection data for the current month (from 1st to last day).

---

### Example 2: Get Collection for Specific Month

```bash
curl -X POST "http://localhost/amt/api/daily-collection-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "date_from": "2025-01-01",
    "date_to": "2025-01-31"
  }'
```

**Result:** Returns daily collection data for January 2025.

---

### Example 3: Get Collection for Custom Date Range

```bash
curl -X POST "http://localhost/amt/api/daily-collection-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "date_from": "2025-01-15",
    "date_to": "2025-02-15"
  }'
```

**Result:** Returns daily collection data from January 15 to February 15, 2025.

---

### Example 4: Get Collection for Last Month

```bash
curl -X POST "http://localhost/amt/api/daily-collection-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "date_from": "2025-09-01",
    "date_to": "2025-09-30"
  }'
```

**Result:** Returns daily collection data for September 2025.

---

### Example 5: Get Collection for Entire Year

```bash
curl -X POST "http://localhost/amt/api/daily-collection-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "date_from": "2025-01-01",
    "date_to": "2025-12-31"
  }'
```

**Result:** Returns daily collection data for the entire year 2025.

---

### Example 6: Get Filter Options

```bash
curl -X POST "http://localhost/amt/api/daily-collection-report/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Result:** Returns suggested date ranges (This Month, Last Month, This Year).

---

## Filter Behavior

### Date From Parameter (`date_from`)

- **When provided:** Uses the specified start date for the report
- **When null/empty:** Defaults to the first day of the current month (`date('Y-m-01')`)
- **Format:** Must be in `YYYY-MM-DD` format
- **Example:** `"date_from": "2025-01-01"` starts the report from January 1, 2025
- **Inclusive:** The start date is included in the results

### Date To Parameter (`date_to`)

- **When provided:** Uses the specified end date for the report
- **When null/empty:** Defaults to the last day of the current month (`date('Y-m-t')`)
- **Format:** Must be in `YYYY-MM-DD` format
- **Example:** `"date_to": "2025-01-31"` ends the report on January 31, 2025
- **Inclusive:** The end date is included in the results

### Date Range Initialization

- The API initializes all dates in the specified range with zero values
- This ensures every date in the range appears in the response, even if there were no collections
- Dates with actual collections will have their amounts and counts updated
- Dates without collections will show `amt: 0`, `count: 0`, and empty `student_fees_deposite_ids` array

---

## Response Fields Explained

### Main Response Fields

| Field | Type | Description |
|-------|------|-------------|
| status | integer | Response status (1 = success, 0 = error) |
| message | string | Human-readable response message |
| filters_applied | object | Echo of the filters used in the request |
| total_records | integer | Total number of days in the date range |
| fees_data | array | Daily collection data for regular fees |
| other_fees_data | array | Daily collection data for other fees (additional fees) |
| timestamp | string | Server timestamp when response was generated |

### Fees Data Item Fields (Regular Fees)

| Field | Type | Description |
|-------|------|-------------|
| date | string | Date of collection (YYYY-MM-DD format) |
| amt | float | Total amount collected on this date (includes fines) |
| count | integer | Number of transactions/deposits on this date |
| student_fees_deposite_ids | array | Array of deposit IDs from `student_fees_deposite` table |

### Other Fees Data Item Fields (Additional Fees)

| Field | Type | Description |
|-------|------|-------------|
| date | string | Date of collection (YYYY-MM-DD format) |
| amt | float | Total amount collected on this date (includes fines) |
| count | integer | Number of transactions/deposits on this date |
| student_fees_deposite_ids | array | Array of deposit IDs from `student_fees_depositeadding` table |

---

## Data Processing Logic

### How Collections Are Calculated

The API processes collections in the following way:

1. **Retrieve All Fee Records:**
   - Regular fees from `student_fees_deposite` table via `getCurrentSessionStudentFeess()` model method
   - Other fees from `student_fees_depositeadding` table via `getOtherfeesCurrentSessionStudentFeess()` model method

2. **Parse Payment Details:**
   - Each deposit record contains an `amount_detail` field with JSON data
   - JSON contains array of payment transactions with dates, amounts, and fines
   - Example: `[{"date":"2025-01-15","amount":1000,"amount_fine":50,"amount_discount":0}]`

3. **Filter by Date Range:**
   - Only transactions within the specified date range are included
   - Date comparison uses Unix timestamps for accuracy

4. **Aggregate by Date:**
   - Transactions are grouped by date
   - Amounts are summed: `amount + amount_fine` for each transaction
   - Transaction counts are incremented
   - Deposit IDs are collected in an array

5. **Initialize Missing Dates:**
   - All dates in the range are initialized with zero values
   - This ensures complete date coverage in the response

### Amount Calculation Formula

```
Daily Total = Σ (payment_amount + fine_amount)
```

Where:
- `payment_amount` = Amount paid from `amount_detail` JSON
- `fine_amount` = Fine amount from `amount_detail` JSON
- Discounts are NOT deducted (they're tracked separately in the JSON)

---

## Error Handling

### Common Errors and Solutions

#### 1. Unauthorized Access (401)

**Error:**
```json
{
  "status": 0,
  "message": "Unauthorized access"
}
```

**Cause:** Missing or incorrect authentication headers

**Solution:** Ensure you include the required headers:
```
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

---

#### 2. Bad Request (400)

**Error:**
```json
{
  "status": 0,
  "message": "Bad request. Only POST method allowed."
}
```

**Cause:** Using wrong HTTP method (GET, PUT, DELETE instead of POST)

**Solution:** Use POST method for all API calls

---

#### 3. Internal Server Error (500)

**Error:**
```json
{
  "status": 0,
  "message": "Internal server error",
  "error": "Error details"
}
```

**Cause:** Server-side error (database connection, query error, invalid date format, etc.)

**Solution:**
- Check server logs at `application/logs/log-{date}.php`
- Verify date format is `YYYY-MM-DD`
- Ensure database connection is working
- Verify all required models are loaded
- Contact system administrator if issue persists

---

#### 4. Invalid Date Format

**Symptom:** Empty response or incorrect data

**Cause:** Date parameters not in `YYYY-MM-DD` format

**Solution:**
- Always use `YYYY-MM-DD` format (e.g., "2025-01-15")
- Do NOT use formats like "01/15/2025" or "15-01-2025"
- Ensure dates are valid (e.g., not "2025-02-30")

---

#### 5. Empty Response Data

**Response:**
```json
{
  "status": 1,
  "message": "Daily collection report retrieved successfully",
  "total_records": 31,
  "fees_data": [
    {"date": "2025-01-01", "amt": 0, "count": 0, "student_fees_deposite_ids": []},
    ...
  ],
  "other_fees_data": []
}
```

**Possible Causes:**
1. No collections were made during the specified date range
2. All collections are in the "other fees" category (check `other_fees_data` array)
3. Date range is outside the current session
4. Collections exist but payment dates in JSON don't match the filter range

**Solution:**
- Verify collections exist in the database for the date range
- Check both `fees_data` and `other_fees_data` arrays
- Try a different date range
- Verify payment dates in `amount_detail` JSON match your filter

---

## Testing Instructions

### Step 1: Test Authentication

```bash
# Test with missing headers (should fail)
curl -X POST "http://localhost/amt/api/daily-collection-report/filter" \
  -H "Content-Type: application/json" \
  -d '{}'
```

**Expected:** 401 Unauthorized

---

### Step 2: Test Default Behavior (Current Month)

```bash
# Test with empty filter (should return current month)
curl -X POST "http://localhost/amt/api/daily-collection-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Expected:** 200 OK with current month's daily collection data

**Verify:**
- `filters_applied.date_from` is first day of current month
- `filters_applied.date_to` is last day of current month
- `total_records` equals number of days in current month
- `fees_data` array has entries for each day

---

### Step 3: Test Custom Date Range

```bash
# Test with specific date range
curl -X POST "http://localhost/amt/api/daily-collection-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "date_from": "2025-01-01",
    "date_to": "2025-01-31"
  }'
```

**Expected:** 200 OK with January 2025 daily collection data

**Verify:**
- `filters_applied.date_from` is "2025-01-01"
- `filters_applied.date_to` is "2025-01-31"
- `total_records` is 31 (days in January)
- `fees_data` array has 31 entries

---

### Step 4: Test Single Day Range

```bash
# Test with same start and end date
curl -X POST "http://localhost/amt/api/daily-collection-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "date_from": "2025-01-15",
    "date_to": "2025-01-15"
  }'
```

**Expected:** 200 OK with single day's collection data

**Verify:**
- `total_records` is 1
- `fees_data` array has 1 entry for 2025-01-15

---

### Step 5: Test List Endpoint

```bash
# Test list endpoint for filter options
curl -X POST "http://localhost/amt/api/daily-collection-report/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Expected:** 200 OK with suggested date ranges

**Verify:**
- `date_ranges` array has 3 entries
- Entries include "This Month", "Last Month", "This Year"
- Each entry has `label`, `date_from`, and `date_to` fields

---

### Step 6: Verify Data Accuracy

**Compare with Web Version:**

1. Open web version: `http://localhost/amt/financereports/reportdailycollection`
2. Select the same date range in the web form
3. Submit and view the results
4. Compare the daily totals with API response
5. Verify amounts match between web and API

**Expected:** API data should match web version exactly

---

## Technical Details

### Database Tables Used

**Regular Fees:**
- `student_fees_deposite` - Payment deposit records
- `student_fees_master` - Fee master records
- `fee_session_groups` - Fee group session mappings
- `fee_groups` - Fee group definitions
- `fee_groups_feetype` - Fee type definitions
- `student_session` - Student session records
- `students` - Student information
- `classes` - Class information
- `sections` - Section information

**Other Fees (Additional Fees):**
- `student_fees_depositeadding` - Additional fee payment deposits
- `student_fees_masteradding` - Additional fee master records
- `fee_session_groupsadding` - Additional fee group session mappings
- `fee_groupsadding` - Additional fee group definitions
- `fee_groups_feetypeadding` - Additional fee type definitions

### Model Methods Used

1. **`getCurrentSessionStudentFeess()`**
   - Location: `application/models/Studentfeemaster_model.php`
   - Purpose: Retrieves all regular fee deposits for current session
   - Returns: Array of fee deposit records with student and fee details

2. **`getOtherfeesCurrentSessionStudentFeess()`**
   - Location: `application/models/Studentfeemaster_model.php`
   - Purpose: Retrieves all additional fee deposits for current session
   - Returns: Array of additional fee deposit records

### Helper Functions Used

- **`isJSON($string)`** - Validates if a string is valid JSON
- Located in: `application/helpers/custom_helper.php`

### Controller Location

- **File:** `api/application/controllers/Daily_collection_report_api.php`
- **Class:** `Daily_collection_report_api`
- **Methods:** `filter()`, `list()`

---

## Key Differences from Web Version

| Feature | Web Version | API Version |
|---------|-------------|-------------|
| **Endpoint** | `/financereports/reportdailycollection` | `/api/daily-collection-report/filter` |
| **Method** | POST (form submission) | POST (JSON) |
| **Input Format** | Form data | JSON |
| **Date Format** | Configurable (based on locale) | Fixed `YYYY-MM-DD` |
| **Output Format** | HTML view | JSON |
| **Default Behavior** | Shows form, requires submission | Returns current month data |
| **Authentication** | Session-based | Header-based (API keys) |
| **Response** | HTML page with table | JSON with structured data |
| **Date Initialization** | Only in web version | Both web and API initialize dates |

---

## Best Practices

1. **Always include authentication headers** in every request
2. **Use YYYY-MM-DD date format** for all date parameters
3. **Start with empty filters** to see current month's data
4. **Check both fees_data and other_fees_data** arrays for complete picture
5. **Verify total_records** matches expected number of days
6. **Handle zero-amount days** gracefully in your application
7. **Use list endpoint** to get suggested date ranges
8. **Compare with web version** to verify data accuracy
9. **Log API responses** for debugging and audit purposes
10. **Consider date range size** - large ranges may take longer to process

---

## Use Cases

### 1. Daily Collection Dashboard

Display daily collection trends on a dashboard:

```javascript
// Fetch current month's collection
fetch('http://localhost/amt/api/daily-collection-report/filter', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Client-Service': 'smartschool',
    'Auth-Key': 'schoolAdmin@'
  },
  body: JSON.stringify({})
})
.then(response => response.json())
.then(data => {
  // Create chart with fees_data
  createCollectionChart(data.fees_data);
});
```

---

### 2. Monthly Collection Report

Generate monthly collection summary:

```javascript
// Fetch specific month
fetch('http://localhost/amt/api/daily-collection-report/filter', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Client-Service': 'smartschool',
    'Auth-Key': 'schoolAdmin@'
  },
  body: JSON.stringify({
    date_from: '2025-01-01',
    date_to: '2025-01-31'
  })
})
.then(response => response.json())
.then(data => {
  // Calculate monthly total
  const monthlyTotal = data.fees_data.reduce((sum, day) => sum + day.amt, 0);
  const otherFeesTotal = data.other_fees_data.reduce((sum, day) => sum + day.amt, 0);
  const grandTotal = monthlyTotal + otherFeesTotal;

  console.log('Regular Fees:', monthlyTotal);
  console.log('Other Fees:', otherFeesTotal);
  console.log('Grand Total:', grandTotal);
});
```

---

### 3. Collection Comparison

Compare collections between two periods:

```javascript
// Fetch this month and last month
Promise.all([
  fetch('http://localhost/amt/api/daily-collection-report/filter', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Client-Service': 'smartschool',
      'Auth-Key': 'schoolAdmin@'
    },
    body: JSON.stringify({
      date_from: '2025-10-01',
      date_to: '2025-10-31'
    })
  }).then(r => r.json()),

  fetch('http://localhost/amt/api/daily-collection-report/filter', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Client-Service': 'smartschool',
      'Auth-Key': 'schoolAdmin@'
    },
    body: JSON.stringify({
      date_from: '2025-09-01',
      date_to: '2025-09-30'
    })
  }).then(r => r.json())
])
.then(([thisMonth, lastMonth]) => {
  // Compare totals
  const thisMonthTotal = thisMonth.fees_data.reduce((sum, day) => sum + day.amt, 0);
  const lastMonthTotal = lastMonth.fees_data.reduce((sum, day) => sum + day.amt, 0);
  const difference = thisMonthTotal - lastMonthTotal;
  const percentChange = (difference / lastMonthTotal) * 100;

  console.log('This Month:', thisMonthTotal);
  console.log('Last Month:', lastMonthTotal);
  console.log('Change:', difference, `(${percentChange.toFixed(2)}%)`);
});
```

---

## Related APIs

- **Collection Report API:** `/api/collection-report/filter` - Detailed collection report with student information
- **Combined Collection Report API:** `/api/combined-collection-report/filter` - Combined regular and other fees
- **Total Fee Collection Report API:** `/api/total-fee-collection-report/filter` - Total collections summary
- **Fee Collection Columnwise Report API:** `/api/fee-collection-columnwise-report/filter` - Column-wise breakdown
- **Other Collection Report API:** `/api/other-collection-report/filter` - Other fees only

---

## Frequently Asked Questions (FAQ)

### Q1: What's the difference between fees_data and other_fees_data?

**A:**
- `fees_data` contains regular fees (tuition, admission, etc.) from `student_fees_deposite` table
- `other_fees_data` contains additional fees (late fees, special charges, etc.) from `student_fees_depositeadding` table
- Both are tracked separately in the database and returned as separate arrays

---

### Q2: Why do some dates show zero amount?

**A:** The API initializes all dates in the specified range with zero values. This ensures every date appears in the response, making it easier to create charts and reports. Dates with actual collections will have non-zero amounts.

---

### Q3: Are fines included in the collection amount?

**A:** Yes, the `amt` field includes both the payment amount and fine amount: `amt = amount + amount_fine`

---

### Q4: Can I get collection data for multiple months?

**A:** Yes, you can specify any date range. For example, to get 3 months of data, set `date_from` to the first day of the first month and `date_to` to the last day of the third month.

---

### Q5: What if I want only a specific day's collection?

**A:** Set both `date_from` and `date_to` to the same date. The API will return data for that single day.

---

### Q6: How do I calculate the total collection for a period?

**A:** Sum the `amt` values from both `fees_data` and `other_fees_data` arrays:
```javascript
const regularTotal = fees_data.reduce((sum, day) => sum + day.amt, 0);
const otherTotal = other_fees_data.reduce((sum, day) => sum + day.amt, 0);
const grandTotal = regularTotal + otherTotal;
```

---

### Q7: What's the purpose of student_fees_deposite_ids array?

**A:** This array contains the deposit record IDs for that day. You can use these IDs to fetch detailed information about individual transactions if needed.

---

### Q8: Does the API support pagination?

**A:** No, the API returns all days in the specified date range. However, you can control the amount of data by adjusting the date range.

---

### Q9: Can I filter by class or section?

**A:** No, this API only filters by date range. For class/section filtering, use the Collection Report API (`/api/collection-report/filter`).

---

### Q10: What happens if date_from is after date_to?

**A:** The API will return an empty or minimal dataset. Always ensure `date_from` is earlier than or equal to `date_to`.

---

## Support

For issues or questions:
1. Check server logs at `application/logs/log-{date}.php`
2. Review this documentation
3. Compare API response with web version at `http://localhost/amt/financereports/reportdailycollection`
4. Verify authentication headers are correct
5. Ensure date format is `YYYY-MM-DD`
6. Contact system administrator

---

## Changelog

### Version 1.0 (Current)
- Initial API implementation
- Support for date range filtering
- Separate tracking for regular and other fees
- Default behavior returns current month
- List endpoint for suggested date ranges

---

**Last Updated:** 2025-10-10
**API Version:** 1.0
**Status:** Active
**Web Version:** `http://localhost/amt/financereports/reportdailycollection`
