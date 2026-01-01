# Payroll Report API and Staff Report API - Testing Guide

## Overview
This document provides comprehensive testing instructions for the newly created Payroll Report API and Staff Report API.

**APIs Created:** 2  
**Total Endpoints:** 4  
**Status:** ✅ Ready for Testing  
**Date:** October 7, 2025

---

## 🎯 APIs Summary

### 1. Payroll Report API
- **Base URL:** `http://localhost/amt/api/payroll-report`
- **Endpoints:** `/filter`, `/list`
- **Purpose:** Retrieve staff payroll information with flexible filtering
- **Documentation:** `api/documentation/PAYROLL_REPORT_API_README.md`

### 2. Staff Report API
- **Base URL:** `http://localhost/amt/api/staff-report`
- **Endpoints:** `/filter`, `/list`
- **Purpose:** Retrieve comprehensive staff information with flexible filtering
- **Documentation:** `api/documentation/STAFF_REPORT_API_README.md`

---

## 📋 Pre-Testing Checklist

### Files Created/Modified:
- ✅ `api/application/controllers/Payroll_report_api.php` (260 lines)
- ✅ `api/application/controllers/Staff_report_api.php` (310 lines)
- ✅ `api/application/models/Staff_model.php` (Updated - added 2 methods)
- ✅ `api/application/models/Role_model.php` (Created - 40 lines)
- ✅ `api/application/models/Leavetypes_model.php` (Created - 50 lines)
- ✅ `api/application/config/routes.php` (Updated - added 4 routes)
- ✅ `api/documentation/PAYROLL_REPORT_API_README.md` (451 lines)
- ✅ `api/documentation/STAFF_REPORT_API_README.md` (589 lines)
- ✅ `api/documentation/REPORT_APIS_IMPLEMENTATION_SUMMARY.md` (Updated)

### Models Available:
- ✅ Payroll_model (already existed in API models)
- ✅ Staff_model (updated with staff_report and getStaffRole methods)
- ✅ Role_model (created)
- ✅ Leavetypes_model (created)
- ✅ Setting_model (already existed)
- ✅ Auth_model (already existed)

---

## 🧪 Test Cases

### Test Suite 1: Payroll Report API

#### Test 1.1: Get All Payroll Data (Empty Request)
**Purpose:** Verify that empty request returns all payroll data for current year

```bash
curl -X POST "http://localhost/amt/api/payroll-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Expected Result:**
- Status: 200 OK
- Response status: 1
- Message: "Payroll report retrieved successfully"
- Data array with all payroll records for current year
- filters_applied shows null values
- total_records > 0 (if payroll data exists)

#### Test 1.2: Filter by Month and Year
```bash
curl -X POST "http://localhost/amt/api/payroll-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "month": "January",
    "year": 2025
  }'
```

**Expected Result:**
- Returns payroll records for January 2025 only
- filters_applied shows month: "January", year: 2025

#### Test 1.3: Filter by Role
```bash
curl -X POST "http://localhost/amt/api/payroll-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "role": "Teacher",
    "year": 2025
  }'
```

**Expected Result:**
- Returns payroll records for Teachers only
- filters_applied shows role: "Teacher"

#### Test 1.4: Filter by Date Range
```bash
curl -X POST "http://localhost/amt/api/payroll-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "from_date": "2025-01-01",
    "to_date": "2025-03-31"
  }'
```

**Expected Result:**
- Returns payroll records between specified dates
- filters_applied shows from_date and to_date

#### Test 1.5: Get Filter Options
```bash
curl -X POST "http://localhost/amt/api/payroll-report/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Expected Result:**
- Returns available years, roles, and months
- total_years, total_roles fields present
- Arrays of years and roles

#### Test 1.6: Invalid Authentication
```bash
curl -X POST "http://localhost/amt/api/payroll-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: invalid" \
  -H "Auth-Key: invalid" \
  -d '{}'
```

**Expected Result:**
- Status: 401 Unauthorized
- Response status: 0
- Message: "Unauthorized access"

---

### Test Suite 2: Staff Report API

#### Test 2.1: Get All Staff Data (Empty Request)
**Purpose:** Verify that empty request returns all staff data

```bash
curl -X POST "http://localhost/amt/api/staff-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Expected Result:**
- Status: 200 OK
- Response status: 1
- Message: "Staff report retrieved successfully"
- Data array with all staff records
- leave_types object present
- processed_leaves array for each staff member

#### Test 2.2: Filter by Role
```bash
curl -X POST "http://localhost/amt/api/staff-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "role": 1
  }'
```

**Expected Result:**
- Returns staff with specified role only
- filters_applied shows role: 1

#### Test 2.3: Filter by Designation and Status
```bash
curl -X POST "http://localhost/amt/api/staff-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "designation": 2,
    "staff_status": "1"
  }'
```

**Expected Result:**
- Returns active staff with specified designation
- filters_applied shows designation: 2, staff_status: "1"

#### Test 2.4: Filter by Date Range (Joining Date)
```bash
curl -X POST "http://localhost/amt/api/staff-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "from_date": "2024-01-01",
    "to_date": "2024-12-31"
  }'
```

**Expected Result:**
- Returns staff who joined between specified dates
- filters_applied shows from_date and to_date

#### Test 2.5: Filter by Search Type
```bash
curl -X POST "http://localhost/amt/api/staff-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "search_type": "this_year"
  }'
```

**Expected Result:**
- Returns staff who joined this year
- filters_applied shows search_type: "this_year"

#### Test 2.6: Get Filter Options
```bash
curl -X POST "http://localhost/amt/api/staff-report/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Expected Result:**
- Returns roles, designations, leave_types, status_options
- total_roles, total_designations, total_leave_types fields present

#### Test 2.7: Filter Inactive Staff
```bash
curl -X POST "http://localhost/amt/api/staff-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "staff_status": "2"
  }'
```

**Expected Result:**
- Returns inactive staff only
- filters_applied shows staff_status: "2"

---

## ✅ Validation Checklist

### Response Structure Validation:
- [ ] All responses include `status` field (1 for success, 0 for error)
- [ ] All responses include `message` field
- [ ] All responses include `timestamp` field
- [ ] Filter responses include `filters_applied` object
- [ ] Filter responses include `total_records` field
- [ ] Filter responses include `data` array
- [ ] List responses include appropriate count fields

### Data Validation:
- [ ] Payroll records include all required fields (employee_id, name, salary details)
- [ ] Staff records include all required fields (employee_id, name, designation, etc.)
- [ ] Leave information is properly processed in staff records
- [ ] Date fields are in correct format
- [ ] Numeric fields are properly formatted

### Error Handling:
- [ ] Invalid authentication returns 401
- [ ] Invalid request method returns 400
- [ ] Server errors return 500 with error message
- [ ] Empty data returns empty array, not error

### Graceful Null/Empty Handling:
- [ ] Empty request `{}` returns all data (not error)
- [ ] Null parameters are ignored
- [ ] Partial filters work correctly
- [ ] Combined filters work correctly

---

## 🐛 Common Issues and Solutions

### Issue 1: "Unable to locate the model"
**Solution:** Ensure all required models exist in `api/application/models/` folder

### Issue 2: "Unauthorized access"
**Solution:** Check authentication headers are correct:
- `Client-Service: smartschool`
- `Auth-Key: schoolAdmin@`

### Issue 3: Empty data array
**Solution:** This is normal if no records exist in database. Check database tables:
- `staff_payslip` for payroll data
- `staff` for staff data

### Issue 4: SQL errors
**Solution:** Check database connection and table structures

---

## 📊 Expected Response Examples

### Payroll Report Success Response:
```json
{
    "status": 1,
    "message": "Payroll report retrieved successfully",
    "filters_applied": {...},
    "total_records": 10,
    "data": [...],
    "timestamp": "2025-10-07 23:00:00"
}
```

### Staff Report Success Response:
```json
{
    "status": 1,
    "message": "Staff report retrieved successfully",
    "filters_applied": {...},
    "total_records": 25,
    "data": [...],
    "leave_types": {...},
    "timestamp": "2025-10-07 23:00:00"
}
```

---

## 🎉 Success Criteria

Both APIs are considered successfully implemented if:
1. ✅ All test cases pass
2. ✅ Empty request returns all data (not error)
3. ✅ All filters work correctly
4. ✅ Authentication is enforced
5. ✅ Response format is consistent
6. ✅ No PHP errors or warnings
7. ✅ Documentation is complete and accurate

---

**Testing Status:** Ready for Testing  
**Next Steps:** Run all test cases and verify results  
**Support:** Refer to individual API documentation for detailed information
