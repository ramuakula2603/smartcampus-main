# API Test Scenarios - Quick Reference

## All Tested Payload Combinations

### ✅ Filter Endpoint: `/api/student-attendance-type-report/filter`

#### 1. Empty Payload - Get Everything
```json
{}
```
**Result:** 1147 records | search_type="all" | Date: 2015-10-14 to 2035-10-14

---

#### 2. Only Search Type
```json
{"search_type": "this_week"}
```
**Result:** 0 records | Date: 2025-10-13 to 2025-10-19

---

#### 3. Only Attendance Type (Present)
```json
{"attendance_type": 1}
```
**Result:** 1141 records | search_type="all" | All Present students

---

#### 4. Only Class ID
```json
{"class_id": 14}
```
**Result:** 343 records | search_type="all" | All Class 14 students

---

#### 5. Only Session ID
```json
{"session_id": 21}
```
**Result:** 480 records | search_type="all" | All Session 21 students

---

#### 6. Custom Date Range Only
```json
{
  "search_type": "period",
  "date_from": "2025-08-01",
  "date_to": "2025-08-31"
}
```
**Result:** 316 records | August 2025 data

---

#### 7. Multiple Filters
```json
{
  "class_id": 14,
  "attendance_type": 1,
  "search_type": "period",
  "date_from": "2025-08-01",
  "date_to": "2025-08-31"
}
```
**Result:** 115 records | Class 14 + Present + August 2025

---

#### 8. All Filters (Maximum Filtering)
```json
{
  "class_id": 14,
  "section_id": 24,
  "session_id": 21,
  "attendance_type": 1,
  "search_type": "period",
  "date_from": "2025-08-01",
  "date_to": "2025-08-31"
}
```
**Result:** 56 records | Fully filtered result

---

### ✅ List Endpoint: `/api/student-attendance-type-report/list`

#### 9. Empty Payload
```json
{}
```
**Result:** 1147 records | search_type="all" | All historical data

---

#### 10. Specific Attendance Type (Absent)
```json
{"attendance_type": 4}
```
**Result:** 607 records | search_type="all" | All Absent students

---

## Parameter Values Reference

### Attendance Types:
- `1` = Present (1141 historical records)
- `2` = Excuse
- `3` = Late
- `4` = Absent (607 historical records)
- `5` = Holiday
- `6` = Half Day

### Search Types:
- `"today"` = Current day
- `"this_week"` = Current week (Oct 13-19, 2025)
- `"last_week"` = Previous week
- `"this_month"` = Current month
- `"last_month"` = Previous month
- `"last_3_months"` = Last 3 months
- `"last_6_months"` = Last 6 months
- `"last_12_months"` = Last 12 months
- `"this_year"` = Current year
- `"last_year"` = Previous year
- `"period"` = Custom date range (requires date_from & date_to)
- `null` or not provided = **"all"** (10 year range: 2015-2035)

### Class IDs (Example):
- `14` = JR-MPC (343 historical records)

### Session IDs (Example):
- `21` = 2025-2026 Academic Session (480 historical records)

---

## Response Format

### Standard Response Structure:
```json
{
  "status": true,
  "message": "Student attendance type report retrieved successfully",
  "filters_applied": {
    "search_type": "...",      // "all", "this_week", "period", etc.
    "class_id": ...,           // null, integer, or array
    "section_id": ...,         // null, integer, or array
    "session_id": ...,         // "all" or integer
    "attendance_type": ...,    // "all" or integer
    "attendance_type_name": "...", // "All Types", "Present", "Absent", etc.
    "attendance_type_key": "..."   // "ALL", "P", "A", etc.
  },
  "date_range": {
    "from": "YYYY-MM-DD",
    "to": "YYYY-MM-DD",
    "display": "DD MMM YYYY To DD MMM YYYY"
  },
  "total_records": 0,
  "data": [],
  "timestamp": "YYYY-MM-DD HH:MM:SS"
}
```

---

## PowerShell Test Commands

### Quick Test All Scenarios:
```powershell
$headers = @{
    "Content-Type"="application/json"
    "Client-Service"="smartschool"
    "Auth-Key"="schoolAdmin@"
}

# Test 1: Empty
$r1 = Invoke-RestMethod -Uri "http://localhost/amt/api/student-attendance-type-report/filter" -Method POST -Headers $headers -Body "{}"
Write-Host "Empty: $($r1.total_records) records"

# Test 2: This week
$r2 = Invoke-RestMethod -Uri "http://localhost/amt/api/student-attendance-type-report/filter" -Method POST -Headers $headers -Body '{"search_type":"this_week"}'
Write-Host "This Week: $($r2.total_records) records"

# Test 3: Present only
$r3 = Invoke-RestMethod -Uri "http://localhost/amt/api/student-attendance-type-report/filter" -Method POST -Headers $headers -Body '{"attendance_type":1}'
Write-Host "Present: $($r3.total_records) records"

# Test 4: Class 14
$r4 = Invoke-RestMethod -Uri "http://localhost/amt/api/student-attendance-type-report/filter" -Method POST -Headers $headers -Body '{"class_id":14}'
Write-Host "Class 14: $($r4.total_records) records"

# Test 5: Custom date
$r5 = Invoke-RestMethod -Uri "http://localhost/amt/api/student-attendance-type-report/filter" -Method POST -Headers $headers -Body '{"search_type":"period","date_from":"2025-08-01","date_to":"2025-08-31"}'
Write-Host "August 2025: $($r5.total_records) records"
```

---

## Expected Record Counts

| Scenario | Expected Records | Notes |
|----------|-----------------|-------|
| Empty `{}` | **1147** | All historical data |
| `search_type="this_week"` | **0** | Current week (no data) |
| `attendance_type=1` (Present) | **1141** | All Present records |
| `attendance_type=4` (Absent) | **607** | All Absent records |
| `class_id=14` | **343** | All Class 14 records |
| `session_id=21` | **480** | All Session 21 records |
| Aug 2025 range | **316** | Custom date range |
| Class 14 + Present + Aug | **115** | Multiple filters |
| All filters | **56** | Maximum filtering |

---

## Date: October 14, 2025
## Version: 1.4.0
## Status: ✅ All Tests Passed
