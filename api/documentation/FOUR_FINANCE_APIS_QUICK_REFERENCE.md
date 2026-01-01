# Four Finance Report APIs - Quick Reference Guide

## 🚀 Quick Start

All APIs require these headers:
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

Base URL: `http://localhost/amt/api`

---

## 📊 API Endpoints Summary

### 1. Other Collection Report API
```
POST /api/other-collection-report/list       # Get filter options
POST /api/other-collection-report/filter     # Get report data
```
**Use Case:** View only "other" fee collections (hostel, library, etc.)

### 2. Combined Collection Report API
```
POST /api/combined-collection-report/list    # Get filter options
POST /api/combined-collection-report/filter  # Get report data
```
**Use Case:** View all fee collections (regular + other + transport)

### 3. Total Fee Collection Report API
```
POST /api/total-fee-collection-report/list   # Get filter options
POST /api/total-fee-collection-report/filter # Get report data
```
**Use Case:** View all fees with breakdown by fee type

### 4. Fee Collection Columnwise Report API
```
POST /api/fee-collection-columnwise-report/list   # Get filter options
POST /api/fee-collection-columnwise-report/filter # Get report data
```
**Use Case:** View student-wise fee collection (pivot table format)

---

## 🎯 Common Parameters

All filter endpoints accept these optional parameters:

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| search_type | string | Date range preset | "this_month" |
| date_from | string | Start date | "2025-01-01" |
| date_to | string | End date | "2025-03-31" |
| class_id | integer | Filter by class | 1 |
| section_id | integer | Filter by section | 2 |
| session_id | integer | Filter by session | 18 |
| feetype_id | integer | Filter by fee type | 5 |
| received_by | string | Filter by receiver | "John Doe" |
| group | string | Group results | "class" |

**Note:** All parameters are optional. Empty request `{}` returns all records.

---

## 📅 Search Type Options

| Value | Description |
|-------|-------------|
| today | Today's records |
| this_week | This week's records |
| this_month | This month's records |
| last_month | Last month's records |
| this_year | This year's records |
| period | Custom date range (use date_from and date_to) |

---

## 🔄 Grouping Options

| Value | Description |
|-------|-------------|
| class | Group by class |
| collection | Group by person who received payment |
| mode | Group by payment mode |

**Note:** Grouping not supported in Columnwise Report API.

---

## 💡 Quick Examples

### Get All Records (Empty Request)
```json
POST /api/other-collection-report/filter
Body: {}
```

### Get This Month's Records
```json
POST /api/combined-collection-report/filter
Body: {
    "search_type": "this_month"
}
```

### Get Records for Specific Class
```json
POST /api/total-fee-collection-report/filter
Body: {
    "search_type": "this_year",
    "class_id": 1
}
```

### Get Columnwise Report with Date Range
```json
POST /api/fee-collection-columnwise-report/filter
Body: {
    "search_type": "period",
    "date_from": "2025-01-01",
    "date_to": "2025-03-31",
    "class_id": 1
}
```

### Get Records with Grouping
```json
POST /api/other-collection-report/filter
Body: {
    "search_type": "this_month",
    "group": "class"
}
```

---

## 📈 Response Format

All APIs return this structure:

```json
{
    "status": 1,
    "message": "Success message",
    "filters_applied": {
        "search_type": "this_month",
        "date_from": "2025-10-01",
        "date_to": "2025-10-09",
        "class_id": null,
        "section_id": null,
        "session_id": 18,
        "feetype_id": null,
        "received_by": null,
        "group": null
    },
    "summary": {
        "total_records": 150,
        "total_amount": "125000.00"
    },
    "total_records": 150,
    "data": [...],
    "timestamp": "2025-10-09 12:34:56"
}
```

---

## 🎨 API Differences at a Glance

| Feature | Other | Combined | Total | Columnwise |
|---------|-------|----------|-------|------------|
| Data | Other fees only | All fees | All fees | All fees |
| Format | Transactions | Transactions | Transactions | Student-wise |
| Grouping | ✅ | ✅ | ✅ | ❌ |
| Fee Breakdown | ❌ | ❌ | ✅ | ✅ |
| Best For | Other fees | All fees | Analysis | Summary |

---

## 🧪 Testing

Run the test script:
```bash
cd C:\xampp\htdocs\amt
C:\xampp\php\php.exe test_four_finance_report_apis.php
```

Expected: 16/16 tests pass ✅

---

## 📚 Full Documentation

For detailed documentation, see:
- `api/documentation/OTHER_COLLECTION_REPORT_API_README.md`
- `api/documentation/COMBINED_COLLECTION_REPORT_API_README.md`
- `api/documentation/TOTAL_FEE_COLLECTION_REPORT_API_README.md`
- `api/documentation/FEE_COLLECTION_COLUMNWISE_REPORT_API_README.md`
- `api/documentation/FOUR_FINANCE_REPORT_APIS_IMPLEMENTATION_SUMMARY.md`

---

## ⚠️ Important Notes

1. **MySQL Must Be Running** - Ensure MySQL is started in XAMPP
2. **Empty Requests Are Valid** - `{}` returns all records for current session
3. **All Parameters Optional** - No required parameters except authentication headers
4. **Date Defaults** - If no date specified, defaults to current year
5. **Session Defaults** - If no session specified, uses current active session

---

## 🔍 Troubleshooting

### Database Connection Error
```json
{
    "status": 0,
    "message": "Database connection error. Please ensure MySQL is running in XAMPP."
}
```
**Solution:** Start MySQL in XAMPP Control Panel

### Unauthorized Access
```json
{
    "status": 0,
    "message": "Unauthorized access"
}
```
**Solution:** Check headers - ensure `Client-Service: smartschool` and `Auth-Key: schoolAdmin@`

### Empty Data Array
```json
{
    "status": 1,
    "data": []
}
```
**Solution:** No records found for the specified filters. Try broader date range or remove filters.

---

## 📞 Support

1. Check individual API documentation
2. Run test script to verify functionality
3. Verify MySQL is running
4. Check authentication headers
5. Review error messages in response

---

**Last Updated:** 2025-10-09  
**Version:** 1.0

