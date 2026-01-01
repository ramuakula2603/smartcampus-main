# Three Finance Report APIs - Postman Testing Guide

## Overview

This guide provides step-by-step instructions for testing the three finance report APIs in Postman.

---

## Prerequisites

1. **Postman** installed on your computer
2. **XAMPP** running with Apache and MySQL
3. **Base URL**: `http://localhost/amt/api`

---

## Common Headers (Required for All Requests)

All API requests require these headers:

| Header | Value |
|--------|-------|
| `Content-Type` | `application/json` |
| `Client-Service` | `smartschool` |
| `Auth-Key` | `schoolAdmin@` |

---

## 1. Income Report API Tests

### Test 1.1: Get Filter Options (List Endpoint)

**Method**: POST  
**URL**: `http://localhost/amt/api/income-report/list`  
**Headers**: (See common headers above)  
**Body** (raw JSON):
```json
{}
```

**Expected Response**:
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
    ]
  },
  "timestamp": "2025-10-09 ..."
}
```

### Test 1.2: Get All Income (Empty Request)

**Method**: POST  
**URL**: `http://localhost/amt/api/income-report/filter`  
**Headers**: (See common headers above)  
**Body** (raw JSON):
```json
{}
```

**Expected Response**:
```json
{
  "status": 1,
  "message": "Income report retrieved successfully",
  "filters_applied": {
    "search_type": null,
    "date_from": "2025-01-01",
    "date_to": "2025-12-31"
  },
  "date_range": {
    "start_date": "2025-01-01",
    "end_date": "2025-12-31",
    "label": "01/01/2025 to 31/12/2025"
  },
  "summary": {
    "total_records": 8,
    "total_amount": "57500.00"
  },
  "total_records": 8,
  "data": [...]
}
```

### Test 1.3: Get Income for This Month

**Method**: POST  
**URL**: `http://localhost/amt/api/income-report/filter`  
**Headers**: (See common headers above)  
**Body** (raw JSON):
```json
{
  "search_type": "this_month"
}
```

### Test 1.4: Get Income for Custom Date Range

**Method**: POST  
**URL**: `http://localhost/amt/api/income-report/filter`  
**Headers**: (See common headers above)  
**Body** (raw JSON):
```json
{
  "date_from": "2025-01-01",
  "date_to": "2025-12-31"
}
```

---

## 2. Due Fees Remark Report API Tests

### Test 2.1: Get Available Classes (List Endpoint)

**Method**: POST  
**URL**: `http://localhost/amt/api/due-fees-remark-report/list`  
**Headers**: (See common headers above)  
**Body** (raw JSON):
```json
{}
```

**Expected Response**:
```json
{
  "status": 1,
  "message": "Filter options retrieved successfully",
  "data": {
    "classes": [
      {
        "id": "1",
        "class": "Class 1",
        "sections": [...]
      }
    ]
  },
  "timestamp": "2025-10-09 ..."
}
```

### Test 2.2: Get Due Fees Without Filters (Empty Request)

**Method**: POST  
**URL**: `http://localhost/amt/api/due-fees-remark-report/filter`  
**Headers**: (See common headers above)  
**Body** (raw JSON):
```json
{}
```

**Expected Response**:
```json
{
  "status": 1,
  "message": "Please select class and section to view due fees report",
  "filters_applied": {
    "class_id": null,
    "section_id": null
  },
  "total_records": 0,
  "data": [],
  "timestamp": "2025-10-09 ..."
}
```

### Test 2.3: Get Due Fees for Specific Class and Section

**Method**: POST  
**URL**: `http://localhost/amt/api/due-fees-remark-report/filter`  
**Headers**: (See common headers above)  
**Body** (raw JSON):
```json
{
  "class_id": "1",
  "section_id": "1"
}
```

**Expected Response**:
```json
{
  "status": 1,
  "message": "Due fees remark report retrieved successfully",
  "filters_applied": {
    "class_id": "1",
    "section_id": "1",
    "date": "2025-10-09"
  },
  "summary": {
    "total_students": 5,
    "total_amount": "50000.00",
    "total_paid": "30000.00",
    "total_balance": "20000.00"
  },
  "total_records": 5,
  "data": [...]
}
```

---

## 3. Online Fees Report API Tests

### Test 3.1: Get Filter Options (List Endpoint)

**Method**: POST  
**URL**: `http://localhost/amt/api/online-fees-report/list`  
**Headers**: (See common headers above)  
**Body** (raw JSON):
```json
{}
```

**Expected Response**:
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
    ]
  },
  "timestamp": "2025-10-09 ..."
}
```

### Test 3.2: Get All Online Fees (Empty Request)

**Method**: POST  
**URL**: `http://localhost/amt/api/online-fees-report/filter`  
**Headers**: (See common headers above)  
**Body** (raw JSON):
```json
{}
```

**Expected Response**:
```json
{
  "status": 1,
  "message": "Online fees report retrieved successfully",
  "filters_applied": {
    "search_type": null,
    "date_from": "2025-01-01",
    "date_to": "2025-12-31"
  },
  "date_range": {
    "start_date": "2025-01-01",
    "end_date": "2025-12-31",
    "label": "01/01/2025 to 31/12/2025"
  },
  "summary": {
    "total_records": 4068,
    "total_amount": "10685950.00"
  },
  "total_records": 4068,
  "data": [...]
}
```

### Test 3.3: Get Online Fees for This Month

**Method**: POST  
**URL**: `http://localhost/amt/api/online-fees-report/filter`  
**Headers**: (See common headers above)  
**Body** (raw JSON):
```json
{
  "search_type": "this_month"
}
```

### Test 3.4: Get Online Fees for Custom Date Range

**Method**: POST  
**URL**: `http://localhost/amt/api/online-fees-report/filter`  
**Headers**: (See common headers above)  
**Body** (raw JSON):
```json
{
  "date_from": "2025-01-01",
  "date_to": "2025-03-31"
}
```

---

## Quick Setup in Postman

### Step 1: Create a New Collection

1. Open Postman
2. Click "New" → "Collection"
3. Name it "Finance Report APIs"

### Step 2: Set Collection Variables

1. Click on the collection
2. Go to "Variables" tab
3. Add these variables:
   - `base_url`: `http://localhost/amt/api`
   - `client_service`: `smartschool`
   - `auth_key`: `schoolAdmin@`

### Step 3: Set Collection Headers

1. Go to "Headers" tab in the collection
2. Add these headers:
   - `Content-Type`: `application/json`
   - `Client-Service`: `{{client_service}}`
   - `Auth-Key`: `{{auth_key}}`

### Step 4: Create Requests

For each test above:
1. Click "Add request" in the collection
2. Set method to POST
3. Set URL using variable: `{{base_url}}/income-report/filter`
4. Go to "Body" tab → Select "raw" → Select "JSON"
5. Paste the request body
6. Click "Send"

---

## Troubleshooting

### Issue: "Unauthorized access"
**Solution**: Check that headers are correctly set with proper values

### Issue: HTTP 500 Error
**Solution**: 
1. Check that XAMPP Apache and MySQL are running
2. Check PHP error logs in `C:\xampp\apache\logs\error.log`
3. Verify database connection settings

### Issue: Empty Response
**Solution**: 
1. Check that the database has data
2. Verify the date ranges in your request
3. Check that the current session is set correctly

---

## Success Indicators

✅ **Status Code**: 200 OK  
✅ **Response Status**: `"status": 1`  
✅ **Response Message**: Success message  
✅ **Data**: Array of records or appropriate response  
✅ **Timestamp**: Current timestamp  

---

## Additional Notes

1. All APIs support graceful null/empty parameter handling
2. Empty requests return default data (usually current year)
3. All dates use Y-m-d format (e.g., 2025-10-09)
4. All amounts are formatted with 2 decimal places
5. Response times should be under 2 seconds for most queries

---

## Support

For issues or questions, refer to the individual API documentation files:
- `INCOME_REPORT_API_README.md`
- `DUE_FEES_REMARK_REPORT_API_README.md`
- `ONLINE_FEES_REPORT_API_README.md`

**Last Updated**: October 9, 2025

