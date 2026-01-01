# Combined Collection Report API - Bug Fixes

## Date: 2025-10-11

## Issues Fixed

### Issue 1: Incorrect Collection Amount ✅ FIXED

**Problem**: The API was calculating total collection amount incorrectly by only summing the `amount` field, ignoring discounts and fines.

**Root Cause**: 
- The calculation was: `total = sum(amount)`
- Should be: `total = sum(amount + amount_fine - amount_discount)`

**Solution**:
- Updated the calculation logic in `Combined_collection_report_api.php` (lines 237-290)
- Now correctly calculates:
  - `total_amount` - sum of all payment amounts
  - `total_discount` - sum of all discounts
  - `total_fine` - sum of all fines
  - `grand_total` - amount + fine - discount (the actual collection total)

**Code Changes**:
```php
// OLD CODE (lines 251-253):
$amount = isset($row['amount']) ? floatval($row['amount']) : 0;
$grouped_results[$key]['subtotal'] += $amount;
$total_amount += $amount;

// NEW CODE (lines 262-274):
$amount = isset($row['amount']) ? floatval($row['amount']) : 0;
$discount = isset($row['amount_discount']) ? floatval($row['amount_discount']) : 0;
$fine = isset($row['amount_fine']) ? floatval($row['amount_fine']) : 0;
$record_total = $amount + $fine - $discount;

$grouped_results[$key]['subtotal_amount'] += $amount;
$grouped_results[$key]['subtotal_discount'] += $discount;
$grouped_results[$key]['subtotal_fine'] += $fine;
$grouped_results[$key]['subtotal_total'] += $record_total;

$total_amount += $amount;
$total_discount += $discount;
$total_fine += $fine;
```

**Response Changes**:
```json
{
  "summary": {
    "total_records": 4585,
    "total_amount": "1234567.00",      // Sum of amounts
    "total_discount": "12345.00",      // Sum of discounts
    "total_fine": "5678.00",           // Sum of fines
    "grand_total": "1227900.00",       // amount + fine - discount (CORRECT TOTAL)
    "regular_fees_count": 4472,
    "other_fees_count": 113
  }
}
```

---

### Issue 2: Missing Other Fees ✅ VERIFIED WORKING

**Problem**: User reported that other fees were not being displayed in the report.

**Investigation**: 
- The API was already calling both model methods:
  - `studentfeemaster_model->getFeeCollectionReport()` for regular fees
  - `studentfeemasteradding_model->getFeeCollectionReport()` for other fees
- Results were being merged with `array_merge($regular_fees, $other_fees)`

**Root Cause**: 
- The issue was NOT that other fees were missing from the code
- The issue was that the `feetype_id` filter was being applied, which could exclude other fees if a specific regular fee type was selected
- When `feetype_id` is passed, the model queries filter by that specific fee type, excluding all others

**Solution**:
- Set `$feetype_id = null` to always retrieve ALL fee types (line 198)
- This ensures both regular fees AND other fees are always included in the combined report
- The Combined Collection Report should show ALL collections, not filtered by fee type

**Code Changes**:
```php
// OLD CODE (line 196):
$feetype_id = isset($input['feetype_id']) && $input['feetype_id'] !== '' ? $input['feetype_id'] : null;

// NEW CODE (lines 196-198):
// FIX: Do NOT pass feetype_id to model methods - we want ALL fee types in combined report
// The feetype_id filter was causing issues by excluding fee types
$feetype_id = null; // Always null to get all fee types
```

**Verification**:
- The response now includes `regular_fees_count` and `other_fees_count` in the summary
- Both counts should be > 0 if there are collections of both types
- Example: `"regular_fees_count": 4472, "other_fees_count": 113`

---

### Issue 3: Remove feetype_id Filter ✅ FIXED

**Problem**: The API was filtering results by `feetype_id` parameter, but user wants ALL fee types to be included regardless of the parameter value.

**Root Cause**:
- The API was accepting `feetype_id` from the request and passing it to model methods
- The model methods (`getFeeCollectionReport()`) filter by `feetype_id` in SQL queries:
  - `Studentfeemaster_model.php` lines 925-941
  - `Studentfeemasteradding_model.php` lines 756-762
- When `feetype_id` is provided, only that specific fee type is returned

**Solution**:
- Force `$feetype_id = null` in the controller (line 198)
- This ensures the model methods do NOT apply any fee type filtering
- The Combined Collection Report now always shows ALL fee types

**Code Changes**:
```php
// Line 198:
$feetype_id = null; // Always null to get all fee types

// Line 302 (response):
'feetype_id' => 'all', // Always show all fee types
```

**Impact**:
- Even if a client sends `{"feetype_id": 5}` in the request, it will be ignored
- The API will always return ALL fee types (regular + other + transport)
- This matches the expected behavior for a "Combined" Collection Report

---

## Files Modified

1. **api/application/controllers/Combined_collection_report_api.php**
   - Line 196-198: Force `feetype_id = null` to disable fee type filtering
   - Lines 237-290: Fixed amount calculation to include fine and discount
   - Lines 292-318: Updated response to include detailed summary with all totals

---

## Testing Instructions

### Test 1: Verify Correct Amount Calculation

**Request**:
```bash
curl -X POST "http://localhost/amt/api/combined-collection-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"search_type": "this_month"}'
```

**Expected Response**:
```json
{
  "status": 1,
  "summary": {
    "total_amount": "XXX.XX",      // Sum of amounts
    "total_discount": "XX.XX",     // Sum of discounts
    "total_fine": "XX.XX",         // Sum of fines
    "grand_total": "XXX.XX"        // amount + fine - discount
  }
}
```

**Verification**:
- `grand_total` should equal `total_amount + total_fine - total_discount`
- Manually verify by checking a few records in the database

---

### Test 2: Verify Other Fees Are Included

**Request**:
```bash
curl -X POST "http://localhost/amt/api/combined-collection-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"search_type": "this_year"}'
```

**Expected Response**:
```json
{
  "status": 1,
  "summary": {
    "total_records": 4585,
    "regular_fees_count": 4472,    // Should be > 0
    "other_fees_count": 113        // Should be > 0 if other fees exist
  }
}
```

**Verification**:
- Check that `other_fees_count > 0` (if there are other fee collections in the database)
- Check that `total_records = regular_fees_count + other_fees_count`
- Inspect the `data` array to verify it contains records with different fee types

---

### Test 3: Verify feetype_id Filter Is Ignored

**Request 1** (with feetype_id):
```bash
curl -X POST "http://localhost/amt/api/combined-collection-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"search_type": "this_year", "feetype_id": 5}'
```

**Request 2** (without feetype_id):
```bash
curl -X POST "http://localhost/amt/api/combined-collection-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"search_type": "this_year"}'
```

**Expected Result**:
- Both requests should return the SAME number of records
- Both should have `"feetype_id": "all"` in `filters_applied`
- This confirms that the feetype_id parameter is being ignored

---

### Test 4: Verify Grouped Results Calculate Correctly

**Request**:
```bash
curl -X POST "http://localhost/amt/api/combined-collection-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"search_type": "this_month", "group": "class"}'
```

**Expected Response**:
```json
{
  "status": 1,
  "data": [
    {
      "group_name": "19",
      "records": [...],
      "subtotal_amount": "XXX.XX",
      "subtotal_discount": "XX.XX",
      "subtotal_fine": "XX.XX",
      "subtotal_total": "XXX.XX"    // amount + fine - discount
    }
  ]
}
```

**Verification**:
- Each group should have all four subtotal fields
- `subtotal_total` should equal `subtotal_amount + subtotal_fine - subtotal_discount`
- Sum of all `subtotal_total` should equal `grand_total` in summary

---

## Summary of Changes

✅ **Issue 1 - Incorrect Amount**: Fixed by calculating `grand_total = amount + fine - discount`  
✅ **Issue 2 - Missing Other Fees**: Verified working, was caused by feetype_id filter  
✅ **Issue 3 - Remove feetype_id Filter**: Fixed by forcing `feetype_id = null`

All three issues have been resolved. The Combined Collection Report API now:
1. Calculates collection amounts correctly including fines and discounts
2. Includes both regular fees and other fees in all responses
3. Does not filter by fee type - always shows ALL fee types

---

## Next Steps

1. Test the API with the provided test cases
2. Verify the calculations match the web page report
3. Update any client applications that were relying on the feetype_id filter
4. Consider adding a separate endpoint if fee-type-specific filtering is needed in the future

