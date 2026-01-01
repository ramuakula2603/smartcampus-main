# Other Collection Report API - Complete Guide

## 🎯 Overview

The Other Collection Report API retrieves other fee collection data (additional fees like library, sports, hostel fees, etc.). This API **matches the exact behavior** of the web interface at `http://localhost/amt/financereports/other_collection_report`.

**Status:** ✅ Fixed and Working  
**Base URL:** `http://localhost/amt/api`

---

## 🔑 Key Technical Details

### How It Works

1. **Uses Same Model as Web Interface**
   - Calls `studentfeemasteradding_model->getFeeCollectionReport()`
   - Same method used by web page

2. **Parses JSON amount_detail Field**
   - Each deposit record contains JSON with multiple payments
   - Model extracts individual payments from JSON
   - Returns payment records, not deposit records

3. **Filters from JSON, Not Table Columns**
   - `received_by` is in JSON, not a column
   - `date` is in JSON, not a column
   - `payment_mode` is in JSON, not a column
   - Model parses JSON to apply filters

4. **Returns Individual Payments**
   - One deposit can have multiple payments (sub-invoices)
   - Each payment is a separate record in response
   - Includes full student and fee details

---

## 📊 Database Structure

### Table: `student_fees_depositeadding`

```sql
CREATE TABLE `student_fees_depositeadding` (
  `id` int(11) NOT NULL,
  `student_fees_master_id` int(11) DEFAULT NULL,
  `fee_groups_feetype_id` int(11) DEFAULT NULL,
  `student_transport_fee_id` int(11) DEFAULT NULL,
  `amount_detail` text DEFAULT NULL,  -- ✅ JSON field with payments
  `is_active` varchar(10) NOT NULL DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
);
```

### JSON Structure in `amount_detail`

```json
{
  "1": {
    "amount": "5000.00",
    "date": "2025-10-10",
    "payment_mode": "Cash",
    "received_by": "123",
    "amount_discount": "0",
    "amount_fine": "0",
    "description": "Library Fee Payment",
    "inv_no": "1"
  },
  "2": {
    "amount": "3000.00",
    "date": "2025-10-11",
    "payment_mode": "Online",
    "received_by": "456",
    "amount_discount": "0",
    "amount_fine": "0",
    "description": "Sports Fee Payment",
    "inv_no": "2"
  }
}
```

**Key Points:**
- Each key ("1", "2") represents a sub-invoice
- `received_by` is a staff ID
- `date` is the actual payment date
- One deposit record can have multiple payments

---

## 🔌 API Endpoints

### 1. List Endpoint - Get Filter Options

**URL:** `POST /api/other-collection-report/list`

**Purpose:** Get available filter options (classes, fee types, collectors, etc.)

**Request:**
```json
{}
```

**Response:**
```json
{
    "status": 1,
    "message": "Filter options retrieved successfully",
    "data": {
        "search_types": [
            {"key": "today", "label": "Today"},
            {"key": "this_week", "label": "This Week"},
            {"key": "this_month", "label": "This Month"},
            {"key": "last_month", "label": "Last Month"},
            {"key": "this_year", "label": "This Year"},
            {"key": "period", "label": "Custom Period"}
        ],
        "group_by": [
            {"key": "class", "label": "Group By Class"},
            {"key": "collection", "label": "Group By Collection"},
            {"key": "mode", "label": "Group By Payment Mode"}
        ],
        "classes": [...],
        "fee_types": [
            {"id": "1", "type": "Library Fee"},
            {"id": "2", "type": "Sports Fee"}
        ],
        "received_by": [
            {"id": "123", "name": "John Doe (EMP001)"},
            {"id": "456", "name": "Jane Smith (EMP002)"}
        ]
    },
    "timestamp": "2025-10-10 21:30:00"
}
```

---

### 2. Filter Endpoint - Get Report Data

**URL:** `POST /api/other-collection-report/filter`

**Purpose:** Get other fee collection report with filters

#### Request Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| search_type | string | No | Predefined date range (today, this_week, this_month, last_month, this_year, period) |
| date_from | string | No | Start date (YYYY-MM-DD) - used when search_type is 'period' |
| date_to | string | No | End date (YYYY-MM-DD) - used when search_type is 'period' |
| class_id | integer/string | No | Filter by class ID |
| section_id | integer/string | No | Filter by section ID |
| session_id | integer/string | No | Filter by session ID (defaults to current session) |
| feetype_id | integer/string | No | Filter by fee type ID |
| received_by | integer/string | No | Filter by staff ID who collected the fee |
| group | string | No | Group results by: 'class', 'collection', or 'mode' |

**Note:** All parameters are optional. Empty request `{}` returns all records for current year.

---

## 📝 Request Examples

### Example 1: Get All Records (Current Year)

```bash
curl -X POST "http://localhost/amt/api/other-collection-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

### Example 2: Filter by Today

```bash
curl -X POST "http://localhost/amt/api/other-collection-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "search_type": "today"
  }'
```

---

### Example 3: Filter by Date Range

```bash
curl -X POST "http://localhost/amt/api/other-collection-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "search_type": "period",
    "date_from": "2025-01-01",
    "date_to": "2025-12-31"
  }'
```

---

### Example 4: Filter by Class and Section

```bash
curl -X POST "http://localhost/amt/api/other-collection-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "class_id": "19",
    "section_id": "36"
  }'
```

---

### Example 5: Filter by Fee Type

```bash
curl -X POST "http://localhost/amt/api/other-collection-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "feetype_id": "5"
  }'
```

---

### Example 6: Filter by Collector (Received By)

```bash
curl -X POST "http://localhost/amt/api/other-collection-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "received_by": "123"
  }'
```

**Note:** `received_by` is a staff ID, not a name.

---

### Example 7: Group by Class

```bash
curl -X POST "http://localhost/amt/api/other-collection-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "group": "class"
  }'
```

---

### Example 8: Combined Filters with Grouping

```bash
curl -X POST "http://localhost/amt/api/other-collection-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "search_type": "this_month",
    "class_id": "19",
    "section_id": "36",
    "feetype_id": "5",
    "received_by": "123",
    "group": "class"
  }'
```

---

## 📤 Response Structure

### Without Grouping

```json
{
    "status": 1,
    "message": "Other collection report retrieved successfully",
    "filters_applied": {
        "search_type": "today",
        "date_from": "2025-10-10",
        "date_to": "2025-10-10",
        "class_id": null,
        "section_id": null,
        "session_id": 21,
        "feetype_id": null,
        "received_by": null,
        "group": null
    },
    "summary": {
        "total_records": 5,
        "total_amount": "15000.00"
    },
    "total_records": 5,
    "data": [
        {
            "id": "123",
            "student_fees_master_id": "456",
            "fee_groups_feetype_id": "789",
            "admission_no": "2025001",
            "firstname": "John",
            "middlename": "",
            "lastname": "Doe",
            "class_id": "19",
            "class": "Class 10",
            "section": "A",
            "section_id": "36",
            "student_id": "1038",
            "name": "Library Fee",
            "type": "Library Fee",
            "code": "LIB001",
            "student_session_id": "567",
            "is_system": "0",
            "amount": "5000.00",
            "date": "2025-10-10",
            "amount_discount": "0",
            "amount_fine": "0",
            "description": "Library Fee Payment",
            "payment_mode": "Cash",
            "inv_no": "1",
            "received_by": "123",
            "received_byname": {
                "name": "John Doe",
                "employee_id": "EMP001",
                "id": "123"
            }
        }
    ],
    "timestamp": "2025-10-10 21:30:00"
}
```

### With Grouping

```json
{
    "status": 1,
    "message": "Other collection report retrieved successfully",
    "filters_applied": {...},
    "summary": {
        "total_records": 10,
        "total_amount": "30000.00"
    },
    "total_records": 10,
    "data": [
        {
            "group_name": "19",
            "records": [
                {...},
                {...}
            ],
            "subtotal": 15000
        },
        {
            "group_name": "20",
            "records": [
                {...},
                {...}
            ],
            "subtotal": 15000
        }
    ],
    "timestamp": "2025-10-10 21:30:00"
}
```

---

## 🔍 How Data is Retrieved

### Step-by-Step Process

1. **Query Database**
   - Joins: `student_fees_depositeadding` → `fee_groups_feetypeadding` → `fee_groupsadding` → `feetypeadding` → `student_fees_masteradding` → `student_session` → `classes` → `sections` → `students`
   - Filters by: class_id, section_id, session_id, feetype_id (table columns)

2. **Parse JSON for Each Deposit**
   - Decode `amount_detail` JSON field
   - Extract individual payments (sub-invoices)

3. **Filter Payments by Date**
   - Check if payment date (from JSON) falls within date range
   - Only include payments within range

4. **Filter Payments by Collector** (if specified)
   - Check if `received_by` (from JSON) matches filter
   - Only include matching payments

5. **Build Response Records**
   - For each payment that passes filters:
     - Include deposit record fields (id, student info, fee info)
     - Include payment fields from JSON (amount, date, payment_mode, etc.)
     - Look up collector name from staff table

6. **Group Results** (if grouping specified)
   - Group by class_id, received_by, or payment_mode
   - Calculate subtotals for each group

---

## ✨ Key Differences from Simple Query

| Simple Database Query | This API (Matches Web) |
|-----------------------|------------------------|
| Returns deposit records | Returns individual payment records |
| One record per deposit | Multiple records per deposit (one per payment) |
| Cannot filter by date accurately | Filters by actual payment date from JSON |
| Cannot filter by received_by | Filters by collector from JSON |
| Shows created_at timestamp | Shows actual payment date |
| Missing payment details | Includes all payment details |

---

## 🎯 Use Cases

1. **Daily Collection Report** - Filter by today's date
2. **Monthly Collection Report** - Filter by this_month
3. **Collector-wise Report** - Filter by received_by and group by collection
4. **Class-wise Report** - Filter by class and group by class
5. **Fee Type Report** - Filter by feetype_id
6. **Custom Period Report** - Use date_from and date_to

---

## 📚 Related Files

- **Controller:** `api/application/controllers/Other_collection_report_api.php`
- **Model:** `application/models/Studentfeemasteradding_model.php`
- **Web Controller:** `application/controllers/Financereports.php` (line 767-876)
- **Web Page:** `http://localhost/amt/financereports/other_collection_report`

---

**Status:** ✅ Fixed - Now matches web interface behavior exactly  
**Date:** October 10, 2025

