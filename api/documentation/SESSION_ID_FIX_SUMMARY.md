# Session ID Default Behavior Fix

## Issue

When passing an empty payload `{}` to the Student Attendance Type Report API, the `session_id` was always using the default current session without allowing it to be overridden by the request.

### Before Fix:
```php
// In list() endpoint - HARDCODED
$session_id = $this->current_session;  // ❌ No way to override
```

The `list()` endpoint was not checking if `session_id` was provided in the request payload.

---

## Solution

Updated the `list()` endpoint to check for `session_id` in the request payload first, and only use the default current session if not provided.

### After Fix:
```php
// In list() endpoint - NOW CHECKS REQUEST FIRST
$session_id = isset($json_input['session_id']) ? $json_input['session_id'] : null;

// If session_id is not provided, use current session
if (empty($session_id)) {
    $session_id = $this->current_session;  // ✅ Falls back to default
}
```

---

## Changes Made

### 1. Controller Update
**File:** `api/application/controllers/Student_attendance_type_report_api.php`

**Line:** ~295 (in `list()` method)

**Change:**
```php
// OLD CODE:
$attendance_type = isset($json_input['attendance_type']) ? $json_input['attendance_type'] : null;
$search_type = isset($json_input['search_type']) ? $json_input['search_type'] : 'this_week';
$session_id = $this->current_session;  // ❌ Always default

// NEW CODE:
$attendance_type = isset($json_input['attendance_type']) ? $json_input['attendance_type'] : null;
$search_type = isset($json_input['search_type']) ? $json_input['search_type'] : 'this_week';
$session_id = isset($json_input['session_id']) ? $json_input['session_id'] : null;  // ✅ Check request

// If session_id is not provided, use current session
if (empty($session_id)) {
    $session_id = $this->current_session;
}
```

### 2. Documentation Updates
**File:** `api/documentation/STUDENT_ATTENDANCE_TYPE_REPORT_API_README.md`

**Updates Made:**
1. Added empty payload example with defaults explanation
2. Updated List Endpoint Parameters table to include `session_id`
3. Added default behavior note
4. Added Note #9 about empty payload behavior
5. Updated Note #2 to mention override capability

---

## API Behavior Now

### Default Behavior (Empty Payload)
```json
POST /api/student-attendance-type-report/list
{
}
```

**Response:**
- `session_id`: Uses current session (e.g., 21)
- `search_type`: Uses default "this_week"
- `attendance_type`: Uses default "all" (all types)

### Override Session (Custom Payload)
```json
POST /api/student-attendance-type-report/list
{
  "session_id": 20,
  "search_type": "this_month"
}
```

**Response:**
- `session_id`: Uses provided session (20)
- `search_type`: Uses provided "this_month"
- `attendance_type`: Uses default "all" (all types)

---

## Test Results

| Test Case | Payload | Expected Session | Actual Session | Status |
|-----------|---------|------------------|----------------|--------|
| Empty payload | `{}` | 21 (current) | 21 | ✅ PASS |
| Custom session | `{"session_id": 20}` | 20 (provided) | 20 | ✅ PASS |
| Filter default | `{"class_id": 14}` | 21 (current) | 21 | ✅ PASS |
| Filter custom | `{"class_id": 14, "session_id": 19}` | 19 (provided) | 19 | ✅ PASS |

### Full Test Output:
```
Test 1: Empty payload {}
 Session: 21 | Search: this_week | Type: All Types  ✓

Test 2: Custom session_id = 20
 Session: 20 | Search: this_month | Type: All Types  ✓

Test 3: Filter with class_id (default session)
 Session: 21 | Search: this_week | Type: Present  ✓

Test 4: Filter with custom session_id = 19
 Session: 19 | Search: last_month | Type: All Types  ✓
```

---

## Both Endpoints Comparison

### Filter Endpoint (`/filter`)
**Status:** ✅ Already had correct implementation
```php
// If session_id is not provided, use current session
if (empty($session_id)) {
    $session_id = $this->current_session;
}
```

### List Endpoint (`/list`)
**Status:** ✅ Fixed - Now matches filter endpoint behavior
```php
// If session_id is not provided, use current session
if (empty($session_id)) {
    $session_id = $this->current_session;
}
```

---

## API Parameters Summary

### List Endpoint - All Optional
| Parameter | Default Value | Can Override? |
|-----------|--------------|---------------|
| `attendance_type` | "all" (all types) | ✅ Yes |
| `search_type` | "this_week" | ✅ Yes |
| `session_id` | Current session | ✅ Yes |

### Filter Endpoint
| Parameter | Required | Default Value | Can Override? |
|-----------|----------|---------------|---------------|
| `class_id` | **Yes** | N/A | N/A |
| `attendance_type` | No | "all" (all types) | ✅ Yes |
| `search_type` | No | "this_week" | ✅ Yes |
| `section_id` | No | None | ✅ Yes |
| `session_id` | No | Current session | ✅ Yes |

---

## Benefits of This Fix

1. **Flexibility:** Users can now query data from different sessions
2. **Consistency:** Both endpoints now behave the same way
3. **Backward Compatible:** Empty payload still works with sensible defaults
4. **Control:** Applications can explicitly specify which session to query
5. **Historical Data:** Can retrieve attendance data from previous sessions

---

## Example Use Cases

### Use Case 1: Quick Current Week Report
```javascript
// Get all attendance for current week and current session
fetch('/api/student-attendance-type-report/list', {
  method: 'POST',
  body: JSON.stringify({})  // Empty = all defaults
});
```

### Use Case 2: Historical Comparison
```javascript
// Compare attendance between two sessions
const session2024 = await fetch('/api/student-attendance-type-report/list', {
  method: 'POST',
  body: JSON.stringify({ session_id: 20, search_type: 'this_year' })
});

const session2025 = await fetch('/api/student-attendance-type-report/list', {
  method: 'POST',
  body: JSON.stringify({ session_id: 21, search_type: 'this_year' })
});
```

### Use Case 3: Specific Class in Previous Session
```javascript
// Get class 14 data from session 19
fetch('/api/student-attendance-type-report/filter', {
  method: 'POST',
  body: JSON.stringify({
    class_id: 14,
    session_id: 19,
    search_type: 'last_month'
  })
});
```

---

## Updated Documentation

### New Example in README
```bash
# Example 1: Get All Attendance Types with Empty Payload (All Defaults)
curl -X POST "http://localhost/amt/api/student-attendance-type-report/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```
*This returns all attendance types for current week and current session*

### Updated Parameter Table
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `session_id` | integer | No | Session ID (defaults to current session if not provided) | `20` |

---

## Files Modified

1. **Controller:**
   - `api/application/controllers/Student_attendance_type_report_api.php`
   - Method: `list()` (line ~295)
   - Change: Added `session_id` parameter check

2. **Documentation:**
   - `api/documentation/STUDENT_ATTENDANCE_TYPE_REPORT_API_README.md`
   - Added: Empty payload example
   - Updated: List Endpoint Parameters table
   - Updated: Notes section (#2 and new #9)
   - Added: Example 1 for empty payload

3. **Summary Document:**
   - `api/documentation/SESSION_ID_FIX_SUMMARY.md` (NEW)

---

## Date: October 14, 2025

**Version:** 1.1.1

**Issue Type:** Bug Fix / Enhancement

**Status:** ✅ Fixed and Tested

**Backward Compatible:** ✅ Yes - Existing code continues to work
