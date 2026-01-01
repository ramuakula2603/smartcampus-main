# Online Admission Fee Report API Documentation

## Overview
The Online Admission Fee Report API provides endpoints to retrieve online admission fee collection data for specified date ranges. This API is part of the Finance Reports module and follows the established patterns for graceful null/empty parameter handling.

## Base Information
- **Base URL**: `http://localhost/amt/api`
- **API Version**: 1.0.0
- **Authentication**: Required (Client-Service and Auth-Key headers)
- **Response Format**: JSON
- **HTTP Method**: POST (for all endpoints)

## Authentication

All API requests require the following headers:

```
Client-Service: smartschool
Auth-Key: schoolAdmin@
Content-Type: application/json
```

### Authentication Example
```bash
curl -X POST http://localhost/amt/api/online-admission-report/filter \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

## Endpoints

### 1. Filter Endpoint
Retrieves online admission fee collection report data with optional filters.

**URL**: `/api/online-admission-report/filter`  
**Method**: `POST`  
**Authentication**: Required

#### Request Parameters

All parameters are optional. Empty request `{}` returns all online admission payments for the current year.

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `search_type` | string | No | Predefined date range (today, this_week, last_week, this_month, last_month, last_3_month, last_6_month, last_12_month, this_year, last_year) |
| `date_from` | string | No | Start date (Y-m-d format, e.g., "2025-01-01") |
| `date_to` | string | No | End date (Y-m-d format, e.g., "2025-12-31") |

#### Graceful Null/Empty Handling

This API follows the established pattern for Finance Report APIs:

- **Empty Request** `{}`: Returns all online admission payments for current year (no validation error)
- **Null Parameters**: Treated same as empty request
- **Empty Strings**: Treated same as null
- **Partial Filters**: Only provided filters are applied

#### Request Examples

**Example 1: Empty Request (All Admissions for Current Year)**
```json
{}
```

**Example 2: Filter by Search Type**
```json
{
    "search_type": "this_month"
}
```

**Example 3: Filter by Custom Date Range**
```json
{
    "date_from": "2025-01-01",
    "date_to": "2025-12-31"
}
```

#### Response Format

**Success Response (HTTP 200)**
```json
{
    "status": 1,
    "message": "Online admission report retrieved successfully",
    "filters_applied": {
        "search_type": null,
        "date_from": "2025-01-01",
        "date_to": "2025-12-31"
    },
    "date_range": {
        "start_date": "2025-01-01",
        "end_date": "2025-12-31",
        "label": "Jan 1, 2025 to Dec 31, 2025"
    },
    "summary": {
        "total_admissions": 25,
        "total_payments": 30,
        "total_amount": "450,000.00",
        "by_payment_mode": [
            {
                "total_admissions": "15",
                "total_amount": "300000.00",
                "payment_mode": "Cash",
                "payment_count": "18"
            },
            {
                "total_admissions": "10",
                "total_amount": "150000.00",
                "payment_mode": "Online",
                "payment_count": "12"
            }
        ],
        "by_class": [
            {
                "class": "Class 1",
                "section": "A",
                "admission_count": "10",
                "total_amount": "150000.00"
            },
            {
                "class": "Class 2",
                "section": "B",
                "admission_count": "15",
                "total_amount": "300000.00"
            }
        ]
    },
    "total_records": 30,
    "data": [
        {
            "id": "1",
            "reference_no": "OA2025001",
            "firstname": "John",
            "middlename": "",
            "lastname": "Doe",
            "mobileno": "1234567890",
            "email": "john@example.com",
            "class": "Class 1",
            "section": "A",
            "category": "General",
            "date": "2025-01-15",
            "paid_amount": "15000.00",
            "payment_mode": "Cash",
            "payment_id": "PAY001",
            "hostel_name": null,
            "room_type": null,
            "room_no": null,
            "route_title": null,
            "vehicle_no": null,
            "house_name": null,
            "online_admission_id": "1"
        }
    ],
    "timestamp": "2025-10-08 21:30:00"
}
```

**Error Response (HTTP 401 - Unauthorized)**
```json
{
    "status": 0,
    "message": "Unauthorized access"
}
```

**Error Response (HTTP 500 - Server Error)**
```json
{
    "status": 0,
    "message": "Error retrieving online admission report: [error details]"
}
```

#### Response Fields Description

| Field | Type | Description |
|-------|------|-------------|
| `status` | integer | 1 for success, 0 for error |
| `message` | string | Success or error message |
| `filters_applied` | object | Shows which filters were applied |
| `date_range` | object | Date range information with formatted label |
| `summary` | object | Summary statistics |
| `summary.total_admissions` | integer | Total number of unique admissions |
| `summary.total_payments` | integer | Total number of payment records |
| `summary.total_amount` | string | Total amount (formatted with commas) |
| `summary.by_payment_mode` | array | Payments grouped by payment mode |
| `summary.by_class` | array | Admissions grouped by class |
| `total_records` | integer | Number of payment records returned |
| `data` | array | Array of online admission payment records |
| `data[].id` | string | Payment record ID |
| `data[].reference_no` | string | Online admission reference number |
| `data[].firstname` | string | Student first name |
| `data[].middlename` | string | Student middle name |
| `data[].lastname` | string | Student last name |
| `data[].mobileno` | string | Contact mobile number |
| `data[].email` | string | Contact email |
| `data[].class` | string | Class name |
| `data[].section` | string | Section name |
| `data[].category` | string | Student category |
| `data[].date` | string | Payment date (Y-m-d format) |
| `data[].paid_amount` | string | Payment amount |
| `data[].payment_mode` | string | Payment mode (Cash, Online, etc.) |
| `data[].payment_id` | string | Payment transaction ID |
| `data[].hostel_name` | string | Hostel name (if applicable) |
| `data[].room_type` | string | Room type (if applicable) |
| `data[].room_no` | string | Room number (if applicable) |
| `data[].route_title` | string | Transport route (if applicable) |
| `data[].vehicle_no` | string | Vehicle number (if applicable) |
| `data[].house_name` | string | School house name (if applicable) |
| `data[].online_admission_id` | string | Online admission ID |
| `timestamp` | string | Response generation timestamp |

### 2. List Endpoint
Retrieves filter options for the online admission report.

**URL**: `/api/online-admission-report/list`  
**Method**: `POST`  
**Authentication**: Required

#### Request Parameters
None required. Send empty JSON object `{}`.

#### Request Example
```json
{}
```

#### Response Format

**Success Response (HTTP 200)**
```json
{
    "status": 1,
    "message": "Filter options retrieved successfully",
    "data": {
        "search_types": {
            "": false,
            "today": false,
            "this_week": false,
            "last_week": false,
            "last_month": false,
            "last_3_month": false,
            "last_6_month": false,
            "last_12_month": false,
            "this_year": false,
            "last_year": false,
            "period": false
        },
        "group_by": {
            "": false,
            "class": false,
            "collection": false,
            "mode": false
        }
    },
    "timestamp": "2025-10-08 21:30:00"
}
```

## Usage Examples

### Example 1: Get All Online Admissions for Current Year
```bash
curl -X POST http://localhost/amt/api/online-admission-report/filter \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 2: Get This Month's Online Admissions
```bash
curl -X POST http://localhost/amt/api/online-admission-report/filter \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"search_type": "this_month"}'
```

### Example 3: Get Online Admissions for Specific Date Range
```bash
curl -X POST http://localhost/amt/api/online-admission-report/filter \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"date_from": "2025-01-01", "date_to": "2025-06-30"}'
```

### Example 4: Get Filter Options
```bash
curl -X POST http://localhost/amt/api/online-admission-report/list \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

## Error Handling

### Common Error Scenarios

1. **Missing Authentication Headers**
   - HTTP Status: 401
   - Response: `{"status": 0, "message": "Unauthorized access"}`

2. **Invalid Date Format**
   - HTTP Status: 200 (gracefully handled)
   - Response: Returns data for current year

3. **Server Error**
   - HTTP Status: 500
   - Response: `{"status": 0, "message": "Error retrieving online admission report: [details]"}`

## Notes

1. **Graceful Handling**: This API follows the established pattern where empty/null parameters return all records instead of validation errors.

2. **Date Range Priority**: If both `search_type` and custom dates are provided, `search_type` takes precedence.

3. **Default Behavior**: Empty request defaults to current year (Jan 1 to Dec 31 of current year).

4. **Payment vs Admission Count**: Note that `total_payments` may be greater than `total_admissions` if students made multiple payments.

5. **JSON-Only Output**: API returns pure JSON without HTML errors (error display is disabled).

## Related APIs

- Collection Report API
- Total Student Academic Report API
- Student Academic Report API
- Report By Name API
- Expense Group Report API

## Version History

- **v1.0.0** (2025-10-08): Initial release with graceful null/empty handling

## Support

For issues or questions, please contact the development team.

