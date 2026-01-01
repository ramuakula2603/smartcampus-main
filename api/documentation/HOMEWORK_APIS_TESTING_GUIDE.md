# Homework Report APIs Testing Guide

## Overview
This guide provides comprehensive testing instructions for the three homework-related Report APIs:
1. **Daily Assignment Report API**
2. **Evaluation Report API**
3. **Homework Report API**

All three APIs have been created following the exact patterns and standards used in the existing 24 Report APIs.

---

## Prerequisites

### Required Headers
All API requests require these authentication headers:
```
Client-Service: smartschool
Auth-Key: schoolAdmin@
Content-Type: application/json
```

### Base URL
```
http://localhost/amt/api
```

---

## API 1: Daily Assignment Report API

### Endpoints
1. `POST /api/daily-assignment-report/filter` - Get daily assignment data
2. `POST /api/daily-assignment-report/list` - Get filter options

### Test 1: Get All Daily Assignments (Empty Request)
```bash
curl -X POST "http://localhost/amt/api/daily-assignment-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Expected Result:**
- Status: 200 OK
- Response contains all daily assignment records for current year
- `status: 1`
- `total_records` > 0 (if data exists)
- `data` array contains assignment records with student and staff information

### Test 2: Filter by Class and Section
```bash
curl -X POST "http://localhost/amt/api/daily-assignment-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "class_id": 10,
    "section_id": 5
  }'
```

### Test 3: Filter by Date Range
```bash
curl -X POST "http://localhost/amt/api/daily-assignment-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "from_date": "2025-01-01",
    "to_date": "2025-03-31"
  }'
```

### Test 4: Filter by Search Type
```bash
curl -X POST "http://localhost/amt/api/daily-assignment-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "search_type": "this_month"
  }'
```

**Search Type Options:**
- `today` - Today's assignments
- `this_week` - This week's assignments
- `this_month` - This month's assignments
- `this_year` - This year's assignments

### Test 5: Get Filter Options
```bash
curl -X POST "http://localhost/amt/api/daily-assignment-report/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Expected Result:**
- Returns available classes
- Returns search type options
- Returns current session ID

---

## API 2: Evaluation Report API

### Endpoints
1. `POST /api/evaluation-report/filter` - Get homework evaluation data
2. `POST /api/evaluation-report/list` - Get filter options

### Test 1: Get All Homework Evaluations (Empty Request)
```bash
curl -X POST "http://localhost/amt/api/evaluation-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Expected Result:**
- Status: 200 OK
- Response contains all homework records with evaluation statistics
- `status: 1`
- Each homework record includes `evaluation_report` object with:
  - `total_students`
  - `evaluated_count`
  - `submitted_count`
  - `pending_count`
  - `evaluated_percentage`
  - `submitted_percentage`
- `evaluation_summary` object provides quick access to statistics by homework ID

### Test 2: Filter by Class and Section
```bash
curl -X POST "http://localhost/amt/api/evaluation-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "class_id": 10,
    "section_id": 5
  }'
```

### Test 3: Filter by Subject
```bash
curl -X POST "http://localhost/amt/api/evaluation-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "class_id": 10,
    "section_id": 5,
    "subject_group_id": 3,
    "subject_id": 15
  }'
```

### Test 4: Get Filter Options
```bash
curl -X POST "http://localhost/amt/api/evaluation-report/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

## API 3: Homework Report API

### Endpoints
1. `POST /api/homework-report/filter` - Get homework data
2. `POST /api/homework-report/list` - Get filter options

### Test 1: Get All Homework (Empty Request)
```bash
curl -X POST "http://localhost/amt/api/homework-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Expected Result:**
- Status: 200 OK
- Response contains all homework records
- `status: 1`
- Each homework record includes:
  - Homework details (id, date, description, document)
  - Class and section information
  - Subject information
  - Staff information (name, surname, employee_id)
  - `student_count` - Total students in class/section
  - `assignments` - Number of submissions

### Test 2: Filter by Class and Section
```bash
curl -X POST "http://localhost/amt/api/homework-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "class_id": 10,
    "section_id": 5
  }'
```

### Test 3: Filter by Subject
```bash
curl -X POST "http://localhost/amt/api/homework-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "class_id": 10,
    "section_id": 5,
    "subject_group_id": 3,
    "subject_id": 15
  }'
```

### Test 4: Get Filter Options
```bash
curl -X POST "http://localhost/amt/api/homework-report/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

## Testing Checklist

### For Each API:

#### ✅ Basic Functionality
- [ ] Empty request `{}` returns all data (not just basic info)
- [ ] Filter by class_id works correctly
- [ ] Filter by section_id works correctly
- [ ] Filter by subject_group_id works correctly (where applicable)
- [ ] Filter by subject_id works correctly (where applicable)
- [ ] Combined filters work correctly
- [ ] List endpoint returns filter options

#### ✅ Response Format
- [ ] Response is valid JSON
- [ ] `status` field is present (1 for success)
- [ ] `message` field is present
- [ ] `filters_applied` object shows applied filters
- [ ] `total_records` count is accurate
- [ ] `data` array contains records
- [ ] `timestamp` is present

#### ✅ Error Handling
- [ ] Invalid authentication returns 401
- [ ] GET request returns 400 (only POST allowed)
- [ ] Server errors return 500 with error message

#### ✅ Data Integrity
- [ ] All expected fields are present in records
- [ ] No null pointer errors
- [ ] No PHP warnings or errors
- [ ] Data matches web page results

---

## Common Issues and Solutions

### Issue 1: Empty Response
**Problem:** API returns empty data array
**Solution:** Check if data exists in database for the given filters

### Issue 2: Authentication Error
**Problem:** 401 Unauthorized
**Solution:** Verify headers are correct:
- `Client-Service: smartschool`
- `Auth-Key: schoolAdmin@`

### Issue 3: Method Not Allowed
**Problem:** 400 Bad Request
**Solution:** Use POST method, not GET

### Issue 4: PHP Errors
**Problem:** PHP warnings or errors in response
**Solution:** Check error logs at `application/logs/`

---

## Success Criteria

All three APIs are considered successfully implemented when:

1. ✅ Empty request `{}` returns complete data (not just class list)
2. ✅ All filters work correctly (individually and combined)
3. ✅ Response format matches existing Report APIs
4. ✅ No PHP errors or warnings
5. ✅ Data matches web page results
6. ✅ Documentation is complete and accurate
7. ✅ All test cases pass

---

## Files Created

### Controllers
1. `api/application/controllers/Daily_assignment_report_api.php` (280 lines)
2. `api/application/controllers/Evaluation_report_api.php` (240 lines)
3. `api/application/controllers/Homework_report_api.php` (180 lines)

### Models
- `api/application/models/Homework_model.php` (Updated with 3 new methods)

### Documentation
1. `api/documentation/DAILY_ASSIGNMENT_REPORT_API_README.md` (489 lines)
2. `api/documentation/EVALUATION_REPORT_API_README.md` (300+ lines)
3. `api/documentation/HOMEWORK_REPORT_API_README.md` (300+ lines)
4. `api/documentation/HOMEWORK_APIS_TESTING_GUIDE.md` (This file)

### Routes
- `api/application/config/routes.php` (Updated with 6 new routes)

### Summary
- `api/documentation/REPORT_APIS_IMPLEMENTATION_SUMMARY.md` (Updated)

---

**Total APIs:** 27  
**Total Endpoints:** 54  
**Status:** ✅ Ready for Testing  
**Date:** October 7, 2025

