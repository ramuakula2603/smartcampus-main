# Fee Group-wise Collection Report API Documentation

## Overview
The Fee Group-wise Collection Report API provides endpoints to retrieve fee collection data grouped by fee groups with detailed student-level records. This API supports filtering by session, class, section, fee groups, and date range.

## Base URL
```
http://localhost/amt/api/
```

## Authentication
All endpoints require authentication headers:
- **Client-Service**: `smartschool`
- **Auth-Key**: `schoolAdmin@`

## Endpoints

### 1. Filter Endpoint - Get Fee Group-wise Collection Report

**Endpoint**: `/api/feegroupwise-collection-report/filter`  
**Method**: `POST`  
**Content-Type**: `application/json`

#### Request Headers
```
Client-Service: smartschool
Auth-Key: schoolAdmin@
Content-Type: application/json
```

#### Request Body Parameters
All parameters are optional. Empty parameters will return all available records.

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `session_id` | string | No | Session ID (defaults to current session if not provided) |
| `class_ids` | array | No | Array of class IDs to filter (empty array returns all classes) |
| `section_ids` | array | No | Array of section IDs to filter (empty array returns all sections) |
| `feegroup_ids` | array | No | Array of fee group IDs to filter (empty array returns all fee groups) |
| `from_date` | string | No | Start date in YYYY-MM-DD format (null returns all dates) |
| `to_date` | string | No | End date in YYYY-MM-DD format (null returns all dates) |

#### Example Request - With Filters
```json
{
  "session_id": "25",
  "class_ids": ["1", "2"],
  "section_ids": ["1", "2"],
  "feegroup_ids": ["1", "2"],
  "from_date": "2024-01-01",
  "to_date": "2024-12-31"
}
```

#### Example Request - Empty (Returns All Records)
```json
{}
```

or

```json
{
  "session_id": "",
  "class_ids": [],
  "section_ids": [],
  "feegroup_ids": [],
  "from_date": "",
  "to_date": ""
}
```

#### Success Response (200 OK)
```json
{
  "status": 1,
  "message": "Fee group-wise collection report retrieved successfully",
  "filters_applied": {
    "session_id": "25",
    "class_ids": ["1", "2"],
    "section_ids": ["1", "2"],
    "feegroup_ids": ["1", "2"],
    "from_date": "2024-01-01",
    "to_date": "2024-12-31"
  },
  "summary": {
    "total_fee_groups": 5,
    "total_amount": "500000.00",
    "total_collected": "350000.00",
    "total_balance": "150000.00",
    "collection_percentage": 70.00
  },
  "grid_data": [
    {
      "fee_group_id": "1",
      "fee_group_name": "Tuition Fee",
      "total_amount": "200000.00",
      "amount_collected": "150000.00",
      "balance_amount": "50000.00",
      "total_students": 50,
      "collection_percentage": 75.00
    },
    {
      "fee_group_id": "2",
      "fee_group_name": "Transport Fee",
      "total_amount": "100000.00",
      "amount_collected": "80000.00",
      "balance_amount": "20000.00",
      "total_students": 30,
      "collection_percentage": 80.00
    }
  ],
  "detailed_data": [
    {
      "student_id": "123",
      "admission_no": "STU001",
      "student_name": "John Doe",
      "father_name": "Robert Doe",
      "class_name": "Class 1",
      "section_name": "A",
      "fee_group_name": "Tuition Fee",
      "total_amount": "5000.00",
      "amount_collected": "3000.00",
      "balance_amount": "2000.00",
      "collection_percentage": 60.00,
      "payment_status": "Partial",
      "student_fees_master_id": "456",
      "fee_group_id": "1"
    },
    {
      "student_id": "124",
      "admission_no": "STU002",
      "student_name": "Jane Smith",
      "father_name": "Michael Smith",
      "class_name": "Class 1",
      "section_name": "A",
      "fee_group_name": "Tuition Fee",
      "total_amount": "5000.00",
      "amount_collected": "5000.00",
      "balance_amount": "0.00",
      "collection_percentage": 100.00,
      "payment_status": "Paid",
      "student_fees_master_id": "457",
      "fee_group_id": "1"
    }
  ],
  "total_fee_groups": 5,
  "total_detailed_records": 150,
  "timestamp": "2024-10-09 12:00:00"
}
```

#### Error Response (401 Unauthorized)
```json
{
  "status": 0,
  "message": "Unauthorized access"
}
```

#### Error Response (500 Internal Server Error)
```json
{
  "status": 0,
  "message": "Error retrieving fee group-wise collection report: [error details]"
}
```

---

### 2. List Endpoint - Get Filter Options

**Endpoint**: `/api/feegroupwise-collection-report/list`  
**Method**: `POST`  
**Content-Type**: `application/json`

#### Request Headers
```
Client-Service: smartschool
Auth-Key: schoolAdmin@
Content-Type: application/json
```

#### Request Body Parameters
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `session_id` | string | No | Session ID (defaults to current session if not provided) |

#### Example Request
```json
{
  "session_id": "25"
}
```

or empty:

```json
{}
```

#### Success Response (200 OK)
```json
{
  "status": 1,
  "message": "Filter options retrieved successfully",
  "current_session": "25",
  "fee_groups": [
    {
      "id": "1",
      "name": "Tuition Fee"
    },
    {
      "id": "2",
      "name": "Transport Fee"
    }
  ],
  "classes": [
    {
      "id": "1",
      "class": "Class 1"
    },
    {
      "id": "2",
      "class": "Class 2"
    }
  ],
  "sessions": [
    {
      "id": "25",
      "session": "2024-2025"
    }
  ],
  "timestamp": "2024-10-09 12:00:00"
}
```

---

## Response Field Descriptions

### Summary Object
- `total_fee_groups`: Total number of fee groups in the report
- `total_amount`: Total fee amount across all fee groups
- `total_collected`: Total amount collected across all fee groups
- `total_balance`: Total balance amount (total_amount - total_collected)
- `collection_percentage`: Overall collection percentage

### Grid Data (Fee Group Summary)
- `fee_group_id`: Unique identifier for the fee group
- `fee_group_name`: Name of the fee group
- `total_amount`: Total fee amount for this fee group
- `amount_collected`: Total amount collected for this fee group
- `balance_amount`: Balance amount (total_amount - amount_collected)
- `total_students`: Number of students in this fee group
- `collection_percentage`: Collection percentage for this fee group

### Detailed Data (Student-level Records)
- `student_id`: Unique identifier for the student
- `admission_no`: Student admission number
- `student_name`: Full name of the student
- `father_name`: Father's name
- `class_name`: Class name
- `section_name`: Section name
- `fee_group_name`: Name of the fee group
- `total_amount`: Total fee amount for this student
- `amount_collected`: Amount collected from this student
- `balance_amount`: Balance amount for this student
- `collection_percentage`: Collection percentage for this student
- `payment_status`: Payment status (Paid/Partial/Pending)
- `student_fees_master_id`: Reference ID for student fee master record
- `fee_group_id`: Reference ID for fee group

---

## Usage Examples

### cURL Example - Filter with Parameters
```bash
curl -X POST http://localhost/amt/api/feegroupwise-collection-report/filter \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -H "Content-Type: application/json" \
  -d '{
    "session_id": "25",
    "class_ids": ["1", "2"],
    "from_date": "2024-01-01",
    "to_date": "2024-12-31"
  }'
```

### cURL Example - Get All Records (Empty Filter)
```bash
curl -X POST http://localhost/amt/api/feegroupwise-collection-report/filter \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -H "Content-Type: application/json" \
  -d '{}'
```

### cURL Example - Get Filter Options
```bash
curl -X POST http://localhost/amt/api/feegroupwise-collection-report/list \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -H "Content-Type: application/json" \
  -d '{}'
```

---

## Notes

1. **Empty Parameters Handling**: The API gracefully handles empty or missing parameters by returning all available records, following the pattern of treating empty filters the same as list endpoints.

2. **Date Filtering**: When `from_date` and `to_date` are provided, the API filters payment records based on the payment date stored in the `amount_detail` JSON field.

3. **Amount Calculation**: 
   - `total_amount` is calculated from `student_fees_master.amount` (actual assigned amount per student)
   - `amount_collected` is calculated by parsing the `amount_detail` JSON field in deposit tables
   - All amounts are formatted to 2 decimal places

4. **Payment Status**:
   - `Paid`: balance_amount = 0 and amount_collected > 0
   - `Partial`: balance_amount > 0 and amount_collected > 0
   - `Pending`: amount_collected = 0

5. **Fee Types**: The API handles both regular fees and additional fees, combining them in the response.

---

## Bug Fixes Applied

### Issue 1: Negative Values in Collection Amounts
**Root Cause**: The model was calculating `total_amount` by summing `fee_groups_feetype.amount` (base fee type amounts) instead of `student_fees_master.amount` (actual assigned amounts per student).

**Fix**: Updated SQL queries in `getRegularFeesCollection()` and `getAdditionalFeesCollection()` methods to use `SUM(sfm.amount)` and `SUM(sfma.amount)` respectively, ensuring correct positive values.

### Issue 2: Missing "Detailed Fee Collection Records"
**Root Cause**: The API endpoint didn't exist. The functionality was only available in the web view.

**Fix**: Created new API controller `Feegroupwise_collection_report_api.php` with proper endpoints that return both summary (`grid_data`) and detailed student-level records (`detailed_data`).

