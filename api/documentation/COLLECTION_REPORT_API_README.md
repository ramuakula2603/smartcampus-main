# Collection Report API Documentation

## Overview
The Collection Report API provides endpoints to retrieve fee collection reports with comprehensive filtering options. This API supports graceful null/empty parameter handling, returning all records when parameters are not provided.

**Base URL:** `/api/collection-report`

**Authentication Required:** Yes
- Header: `Client-Service: smartschool`
- Header: `Auth-Key: schoolAdmin@`

**HTTP Method:** POST (for all endpoints)

---

## Endpoints

### 1. Filter Endpoint
**URL:** `/api/collection-report/filter`

**Purpose:** Retrieve fee collection report data with optional filters

**Request Body Parameters:**
| Parameter | Type | Required | Description | Alternative Names |
|-----------|------|----------|-------------|-------------------|
| search_type | string | No | Predefined date range (today, this_week, last_week, this_month, last_month, last_3_month, last_6_month, last_12_month, this_year, last_year, period). Use "all" or omit for all records. | - |
| date_from | string | No | Custom start date (YYYY-MM-DD format) | from_date |
| date_to | string | No | Custom end date (YYYY-MM-DD format) | to_date |
| feetype_id | string/int | No | Fee type ID to filter by (use 'transport_fees' for transport fees) | fee_type_id |
| received_by | string/int | No | Staff ID who received the payment | collect_by_id, collect_by |
| group | string | No | Group by option (class, collection, mode) | - |
| class_id | string/int | No | Class ID to filter by | - |
| section_id | string/int | No | Section ID to filter by | - |
| session_id | string/int | No | Academic session ID to filter by | sch_session_id |

**Graceful Null/Empty Handling:**
- Empty request `{}` returns current month's collection data
- `null`, empty string, or "all" for search_type are treated as "return ALL records"
- No validation errors for missing or empty parameters
- Alternative parameter names are supported for backward compatibility

**Example Requests:**

1. **Empty Request (Current Month's Collection):**
```json
{}
```

2. **Filter by Search Type:**
```json
{
    "search_type": "this_year"
}
```

3. **Filter by Custom Date Range:**
```json
{
    "date_from": "2025-01-01",
    "date_to": "2025-01-31"
}
```

4. **Filter by Class and Session:**
```json
{
    "class_id": "1",
    "session_id": "1",
    "search_type": "this_month"
}
```

5. **Filter by Fee Type:**
```json
{
    "feetype_id": "1",
    "search_type": "this_month"
}
```

6. **Filter by Collector:**
```json
{
    "received_by": "5",
    "search_type": "this_week"
}
```

7. **Combined Filters:**
```json
{
    "class_id": "1",
    "section_id": "1",
    "session_id": "1",
    "feetype_id": "1",
    "search_type": "this_month"
}
```

8. **Using Alternative Parameter Names:**
```json
{
    "session_id": "21",
    "class_id": "19",
    "section_id": "36",
    "fee_type_id": "33",
    "collect_by_id": "6",
    "search_type": "all",
    "from_date": "2025-09-01",
    "to_date": "2025-10-11"
}
```

9. **Using Period Search Type with Custom Dates:**
```json
{
    "search_type": "period",
    "date_from": "2025-09-01",
    "date_to": "2025-10-11",
    "class_id": "19",
    "section_id": "36"
}
```

**Success Response (200 OK):**
```json
{
    "status": 1,
    "message": "Collection report retrieved successfully",
    "filters_applied": {
        "search_type": "this_month",
        "date_from": "2025-10-01",
        "date_to": "2025-10-31",
        "feetype_id": null,
        "received_by": null,
        "group": null,
        "class_id": null,
        "section_id": null,
        "session_id": null
    },
    "total_records": 150,
    "data": [
        {
            "id": "123",
            "student_fees_master_id": "456",
            "fee_groups_feetype_id": "789",
            "admission_no": "ADM001",
            "firstname": "John",
            "middlename": "M",
            "lastname": "Doe",
            "class_id": "1",
            "class": "Class 1",
            "section": "A",
            "section_id": "1",
            "student_id": "100",
            "name": "Tuition Fee",
            "type": "Tuition Fee",
            "code": "TF001",
            "student_session_id": "200",
            "is_system": "1",
            "amount": "1000.00",
            "date": "2025-10-15",
            "amount_discount": "0.00",
            "amount_fine": "0.00",
            "description": "Monthly tuition fee",
            "payment_mode": "Cash",
            "inv_no": "INV-2025-001",
            "received_by": "5"
        }
    ],
    "timestamp": "2025-10-08 14:30:00"
}
```

**Error Response (401 Unauthorized):**
```json
{
    "status": 0,
    "message": "Unauthorized access"
}
```

**Error Response (500 Internal Server Error):**
```json
{
    "status": 0,
    "message": "Error retrieving collection report: [error details]"
}
```

---

### 2. List Endpoint
**URL:** `/api/collection-report/list`

**Purpose:** Retrieve filter options for the collection report

**Request Body:** Empty `{}` or no body required

**Success Response (200 OK):**
```json
{
    "status": 1,
    "message": "Filter options retrieved successfully",
    "data": {
        "classes": [
            {
                "id": "1",
                "class": "Class 1",
                "sections": [
                    {
                        "id": "1",
                        "section": "A"
                    }
                ]
            }
        ],
        "fee_types": [
            {
                "id": "1",
                "type": "Tuition Fee",
                "code": "TF001"
            },
            {
                "id": "transport_fees",
                "type": "Transport Fees"
            }
        ],
        "collect_by": {
            "5": "John Doe (EMP001)",
            "6": "Jane Smith (EMP002)"
        },
        "sessions": [
            {
                "id": "1",
                "session": "2024-2025"
            }
        ],
        "search_types": {
            "": "Select",
            "today": "Today",
            "this_week": "This Week",
            "this_month": "This Month",
            "last_month": "Last Month",
            "this_year": "This Year",
            "last_year": "Last Year",
            "period": "Period"
        },
        "group_by": {
            "": "Select",
            "class": "Class",
            "collection": "Collection"
        }
    },
    "timestamp": "2025-10-08 14:30:00"
}
```

---

## Implementation Details

### Controller
**File:** `api/application/controllers/Collection_report_api.php`

**Key Features:**
- Authentication check using `auth_model->check_auth_client()`
- Graceful null/empty parameter handling
- Date range processing (search_type or custom dates)
- Default to current month if no date parameters provided
- Comprehensive error handling with try-catch blocks

### Model Methods
**File:** `api/application/models/Studentfeemaster_model.php`

**Methods:**
1. `getFeeCollectionReport($start_date, $end_date, $feetype_id, $received_by, $group, $class_id, $section_id, $session_id)`
   - Retrieves fee collection data with filters
   - Supports both regular fees and transport fees
   - Processes amount_detail JSON to extract payment information
   - Filters by date range using helper methods

2. `get_feesreceived_by()`
   - Returns list of staff members who can collect fees
   - Formatted as: "Name (Employee ID)"

3. `findObjectById($array, $st_date, $ed_date)`
   - Helper method to filter payment records by date range
   - Parses amount_detail JSON

4. `findObjectByCollectId($array, $st_date, $ed_date, $receivedBy)`
   - Helper method to filter payment records by collector and date range
   - Supports both single value and array for multi-select

### Routes
**File:** `api/application/config/routes.php`

```php
$route['collection-report/filter']['POST'] = 'collection_report_api/filter';
$route['collection-report/list']['POST'] = 'collection_report_api/list';
```

---

## Testing

### Test Script
**File:** `test_collection_report.php`

**Test Cases:**
1. Empty request - Returns current month's collection
2. List endpoint - Returns filter options
3. Filter by search_type
4. Filter by custom date range
5. Filter by class_id
6. Filter by session_id
7. Filter by feetype_id
8. Combined filters
9. Empty string parameters treated as null

**Run Tests:**
```bash
php test_collection_report.php
```

---

## Usage Examples

### cURL Examples

1. **Get Current Month's Collection:**
```bash
curl -X POST "http://localhost/amt/api/collection-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d "{}"
```

2. **Get This Year's Collection:**
```bash
curl -X POST "http://localhost/amt/api/collection-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"search_type":"this_year"}'
```

3. **Get Collection for Specific Class:**
```bash
curl -X POST "http://localhost/amt/api/collection-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id":"1","search_type":"this_month"}'
```

4. **Get Filter Options:**
```bash
curl -X POST "http://localhost/amt/api/collection-report/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d "{}"
```

---

## Notes

1. **Date Range Processing:**
   - If `search_type` is provided (except "all" or "period"), it takes precedence over custom dates
   - If `search_type` is "all" or omitted, and custom dates are provided, custom dates are used
   - If `search_type` is "period", custom dates from `date_from`/`date_to` are required
   - If neither `search_type` nor custom dates are provided, defaults to current month
   - Custom dates require both `date_from`/`date_to` (or `from_date`/`to_date`)
   - Valid search_type values: today, this_week, last_week, this_month, last_month, last_3_month, last_6_month, last_12_month, this_year, last_year, period, all

2. **Transport Fees:**
   - Transport fees are included as a special fee type with ID 'transport_fees'
   - Only included if transport module is active
   - Queried separately and merged with regular fees

3. **Amount Detail Processing:**
   - Payment amounts are stored as JSON in `amount_detail` field
   - Each payment record includes: amount, date, discount, fine, payment_mode, inv_no, received_by
   - Date range filtering is applied to individual payment records

4. **Performance Considerations:**
   - Large date ranges may result in slower queries
   - Consider pagination for very large result sets
   - Index on date fields recommended for better performance

---

## API Version
**Version:** 1.0.0  
**Last Updated:** October 8, 2025  
**Status:** Production Ready

