# Due Fees Remark Report API - Quick Test Guide

## Quick Postman Tests

### Setup
1. Open Postman
2. Create a new POST request
3. Add these headers:
   - `Content-Type`: `application/json`
   - `Client-Service`: `smartschool`
   - `Auth-Key`: `schoolAdmin@`

---

## Test 1: Get All Due Fees (Empty Request)

**URL:** `http://localhost/amt/api/due-fees-remark-report/filter`

**Body (raw JSON):**
```json
{}
```

**Expected:** Returns ALL due fees for current session

---

## Test 2: Filter by Class Only

**URL:** `http://localhost/amt/api/due-fees-remark-report/filter`

**Body (raw JSON):**
```json
{
  "class_id": "1"
}
```

**Expected:** Returns all sections in class 1

---

## Test 3: Filter by Class and Section

**URL:** `http://localhost/amt/api/due-fees-remark-report/filter`

**Body (raw JSON):**
```json
{
  "class_id": "1",
  "section_id": "1"
}
```

**Expected:** Returns specific class and section

---

## Test 4: Filter by Session Only

**URL:** `http://localhost/amt/api/due-fees-remark-report/filter`

**Body (raw JSON):**
```json
{
  "session_id": "25"
}
```

**Expected:** Returns all classes/sections for session 25

---

## Test 5: Filter by All Parameters

**URL:** `http://localhost/amt/api/due-fees-remark-report/filter`

**Body (raw JSON):**
```json
{
  "class_id": "1",
  "section_id": "1",
  "session_id": "25"
}
```

**Expected:** Returns specific class/section for session 25

---

## Success Indicators

✅ HTTP Status: 200
✅ Response has `"status": 1`
✅ Response has `"message": "Due fees remark report retrieved successfully"`
✅ Response includes `filters_applied` with your parameters
✅ Response includes `summary` with totals
✅ Response includes `data` array with student records

---

## Common Issues

### Issue: Database Connection Error
**Solution:** Start MySQL in XAMPP Control Panel

### Issue: Unauthorized Access
**Solution:** Check that all three headers are present and correct

### Issue: Empty Data Array
**Solution:** This is normal if there are no due fees for the filters you specified

---

## Quick cURL Commands

### Test 1: Empty Request
```bash
curl -X POST http://localhost/amt/api/due-fees-remark-report/filter -H "Content-Type: application/json" -H "Client-Service: smartschool" -H "Auth-Key: schoolAdmin@" -d "{}"
```

### Test 2: Filter by Class
```bash
curl -X POST http://localhost/amt/api/due-fees-remark-report/filter -H "Content-Type: application/json" -H "Client-Service: smartschool" -H "Auth-Key: schoolAdmin@" -d "{\"class_id\":\"1\"}"
```

### Test 3: Filter by Session
```bash
curl -X POST http://localhost/amt/api/due-fees-remark-report/filter -H "Content-Type: application/json" -H "Client-Service: smartschool" -H "Auth-Key: schoolAdmin@" -d "{\"session_id\":\"25\"}"
```

---

## Automated Test Script

Run the comprehensive test script:

```bash
cd C:\xampp\htdocs\amt
C:\xampp\php\php.exe test_due_fees_graceful_handling.php
```

This will test all 8 scenarios automatically and show results.

---

**Last Updated:** October 9, 2025

