# Monthly Staff Attendance Report API - Quick Reference

**Created:** October 13, 2025  
**Related Page:** http://localhost/amt/attendencereports/staffattendancereport  
**Status:** ✅ Complete & Ready to Use

---

## 📋 Summary

This API provides comprehensive monthly staff attendance reports with the same functionality as the web page. It includes:

- ✅ **Monthly attendance reports** with daily breakdown
- ✅ **Role-based filtering** (Teacher, Admin, Accountant, etc.)
- ✅ **Attendance summaries** (Present, Absent, Late, Half Day, Holiday counts)
- ✅ **Percentage calculations** with status classification
- ✅ **Available periods endpoint** for dropdown population

---

## 🚀 Quick Start

### 1. Get Current Month Report (Empty Payload)

```bash
curl -X POST "http://localhost/amt/api/monthly-staff-attendance/report" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### 2. Get Monthly Report (Specific Month)

```bash
curl -X POST "http://localhost/amt/api/monthly-staff-attendance/report" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "role": "Teacher",
    "month": "October",
    "year": 2024
  }'
```

### 3. Get Available Periods

```bash
curl -X POST "http://localhost/amt/api/monthly-staff-attendance/available-periods" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

## 📡 Endpoints

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/monthly-staff-attendance/report` | POST | Get monthly attendance report |
| `/api/monthly-staff-attendance/available-periods` | POST | Get available years, months, and roles |

---

## 📥 Request Parameters

### Monthly Report Endpoint

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `role` | string | No | All roles | Staff role filter (e.g., "Teacher", "Admin") |
| `month` | string | No | Current month | Full month name (e.g., "October") |
| `year` | integer | No | Current year | Year (2000-2100) |

### Available Periods Endpoint

No parameters required - send empty JSON `{}`

---

## 📤 Response Structure

### Monthly Report Response

```json
{
  "status": 1,
  "message": "Monthly staff attendance report retrieved successfully",
  "filters_applied": {
    "role": "Teacher",
    "month": "October",
    "month_number": 10,
    "year": 2024
  },
  "attendance_types": [...],
  "total_staff": 5,
  "total_days": 31,
  "dates": ["2024-10-01", "2024-10-02", ...],
  "data": [
    {
      "staff_id": "6",
      "staff_info": {
        "name": "MAHA LAKSHMI",
        "surname": "SALLA",
        "employee_id": "200226",
        "contact_no": "8328595488",
        "email": "mahalakshmisalla70@gmail.com",
        "role": "Teacher"
      },
      "daily_attendance": {
        "2024-10-01": {
          "date": "2024-10-01",
          "day_name": "Tuesday",
          "day_short": "Tue",
          "attendance_type": "Present",
          "attendance_key": "<b class='text text-success'>P</b>",
          "remark": ""
        }
      },
      "attendance_summary": {
        "Present": 22,
        "Late": 2,
        "Absent": 3,
        "Half Day": 1,
        "Holiday": 3
      },
      "attendance_percentage": 86.21,
      "attendance_percentage_display": 86,
      "attendance_status": "Good",
      "attendance_status_class": "success",
      "total_working_days": 29,
      "total_present_days": 25
    }
  ],
  "timestamp": "2024-10-13 14:30:00"
}
```

### Available Periods Response

```json
{
  "status": 1,
  "message": "Available periods retrieved successfully",
  "data": {
    "years": [
      {"year": "2025"},
      {"year": "2024"}
    ],
    "months": [
      "January", "February", "March", "April", "May", "June",
      "July", "August", "September", "October", "November", "December"
    ],
    "roles": [
      {"id": "1", "name": "Admin", "type": "Admin"},
      {"id": "2", "name": "Teacher", "type": "Teacher"}
    ]
  },
  "timestamp": "2024-10-13 14:30:00"
}
```

---

## 💡 Key Features Explained

### Attendance Percentage Calculation

```
Total Present Days = Present + Late + Half Day
Total Working Days = Present + Late + Absent + Half Day
Attendance % = (Total Present Days / Total Working Days) × 100
```

### Status Classification

| Percentage | Status | Class |
|------------|--------|-------|
| < 75% | Low | danger (red) |
| ≥ 75% | Good | success (green) |
| No data | No Data | default (gray) |

---

## 🔍 Common Use Cases

### Use Case 1: Monthly Report for All Staff

```javascript
const data = {
  month: "October",
  year: 2024
};
```

### Use Case 2: Monthly Report for Teachers Only

```javascript
const data = {
  role: "Teacher",
  month: "October",
  year: 2024
};
```

### Use Case 3: Populate Dropdowns

```javascript
// First, get available periods
const periods = await getAvailablePeriods();

// Then populate your dropdowns
periods.data.years.forEach(year => {
  // Add to year dropdown
});

periods.data.months.forEach(month => {
  // Add to month dropdown
});

periods.data.roles.forEach(role => {
  // Add to role dropdown
});
```

---

## ⚡ Quick Integration

### JavaScript Example

```javascript
async function loadMonthlyReport(role, month, year) {
  const response = await fetch('http://localhost/amt/api/monthly-staff-attendance/report', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Client-Service': 'smartschool',
      'Auth-Key': 'schoolAdmin@'
    },
    body: JSON.stringify({ role, month, year })
  });
  
  const data = await response.json();
  
  if (data.status === 1) {
    // Success - display the report
    console.log(`Total Staff: ${data.total_staff}`);
    console.log(`Total Days: ${data.total_days}`);
    
    data.data.forEach(staff => {
      console.log(`${staff.staff_info.name}: ${staff.attendance_percentage_display}%`);
    });
  }
  
  return data;
}

// Usage
loadMonthlyReport('Teacher', 'October', 2024);
```

### PHP Example

```php
<?php
function getMonthlyReport($role, $month, $year) {
    $url = 'http://localhost/amt/api/monthly-staff-attendance/report';
    
    $data = array(
        'role' => $role,
        'month' => $month,
        'year' => $year
    );
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Client-Service: smartschool',
        'Auth-Key: schoolAdmin@'
    ));
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

$report = getMonthlyReport('Teacher', 'October', 2024);
?>
```

---

## 🧪 Testing

### Test with HTML Interface

Open: `http://localhost/amt/api/documentation/monthly_staff_attendance_report_api_test.html`

### Test with cURL

```bash
# Test 1: Get monthly report
curl -X POST "http://localhost/amt/api/monthly-staff-attendance/report" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"month": "October", "year": 2024}'

# Test 2: Get available periods
curl -X POST "http://localhost/amt/api/monthly-staff-attendance/available-periods" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

## 📁 Files Created

| File | Location | Purpose |
|------|----------|---------|
| `Monthly_staff_attendance_api.php` | `/api/application/controllers/` | API Controller |
| `MONTHLY_STAFF_ATTENDANCE_REPORT_API_README.md` | `/api/documentation/` | Full Documentation |
| `monthly_staff_attendance_report_api_test.html` | `/api/documentation/` | Test Interface |
| `MONTHLY_STAFF_ATTENDANCE_API_QUICK_REFERENCE.md` | `/api/documentation/` | This file |

---

## ⚠️ Important Notes

1. **Month Format:** Use full month names (e.g., "October", not "Oct" or "10")
2. **Year Range:** Must be between 2000 and 2100
3. **Role Names:** Case-sensitive, use exact role names from the system
4. **Active Staff Only:** API returns only active staff members
5. **Holiday Exclusion:** Holidays are not counted in percentage calculations

---

## 🔗 Related APIs

- **Staff Attendance Report API:** `/api/staff-attendance-report/filter` (Date range filtering)
- **Staff Attendance Summary API:** `/api/teacher/attendance-summary` (Individual staff)
- **Staff Attendance Years API:** `/api/staff-attendance-years/list` (Available years)

---

## 🆘 Common Errors

### Error: "Invalid month name"
**Solution:** Use full month names: "January", "February", etc. (not "Jan", "1", etc.)

### Error: "Invalid year"
**Solution:** Year must be between 2000 and 2100

### Error: "Unauthorized access"
**Solution:** Check your `Client-Service` and `Auth-Key` headers

---

## ✅ Implementation Checklist

- [x] Controller created (`Monthly_staff_attendance_api.php`)
- [x] Model methods verified (using existing `Staffattendancemodel`)
- [x] Full documentation created
- [x] Quick reference guide created
- [x] Test HTML interface created
- [x] JavaScript examples provided
- [x] PHP examples provided
- [x] Python examples provided
- [x] cURL examples provided
- [x] Error handling documented

---

## 📞 Support

For questions or issues:
- Review the full documentation: `MONTHLY_STAFF_ATTENDANCE_REPORT_API_README.md`
- Test using the HTML interface: `monthly_staff_attendance_report_api_test.html`
- Check existing similar APIs for patterns

---

**Status:** ✅ Production Ready  
**Last Updated:** October 13, 2025
