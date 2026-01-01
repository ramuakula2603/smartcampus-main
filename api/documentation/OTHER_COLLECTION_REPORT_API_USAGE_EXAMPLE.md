# Other Collection Report API - Usage Examples

## Understanding the Two Endpoints

### 🔍 Endpoint 1: `/list` - Get Filter Options (Initial Load)

**Purpose:** Get dropdown options for filters - just like loading the web page initially

**Request:**
```http
POST http://localhost/amt/api/other-collection-report/list
Headers:
  Client-Service: smartschool
  Auth-Key: schoolAdmin@
  Content-Type: application/json

Body: {}
```

**Response:**
```json
{
    "status": 1,
    "message": "Filter options retrieved successfully",
    "data": {
        "search_types": [...],
        "group_by": [...],
        "classes": [...],
        "fee_types": [...],
        "received_by": [...]
    }
}
```

**Note:** This endpoint does NOT return any report data - only filter options!

---

### 📊 Endpoint 2: `/filter` - Get Report Data (After Search)

**Purpose:** Get actual report data based on selected filters

#### Example 1: Today's Collection Report

**Request:**
```http
POST http://localhost/amt/api/other-collection-report/filter
Headers:
  Client-Service: smartschool
  Auth-Key: schoolAdmin@
  Content-Type: application/json

Body:
{
    "search_type": "today"
}
```

#### Example 2: This Month's Report for Specific Class

**Request:**
```json
{
    "search_type": "this_month",
    "class_id": "19",
    "group": "class"
}
```

#### Example 3: Custom Date Range with Fee Type

**Request:**
```json
{
    "date_from": "2024-01-01",
    "date_to": "2024-12-31",
    "feetype_id": "5",
    "received_by": "1"
}
```

#### Example 4: Empty Request (Returns Current Year Data)

**Request:**
```json
{}
```

**Response Structure:**
```json
{
    "status": 1,
    "message": "Other collection report retrieved successfully",
    "filters_applied": {
        "search_type": "today",
        "date_from": "2025-10-11",
        "date_to": "2025-10-11",
        ...
    },
    "summary": {
        "total_records": 15,
        "total_paid": "125000.00",
        "total_discount": "5000.00",
        "total_fine": "2000.00",
        "grand_total": "122000.00"
    },
    "data": [
        {
            "payment_id": "123/INV001",
            "date": "2025-10-11",
            "admission_no": "2024001",
            "student_name": "John Doe",
            "class": "SR-MPC (A)",
            "fee_type": "TUITION FEE",
            "collect_by": "Super Admin (9000)",
            "mode": "Cash",
            "paid": "10000.00",
            "note": "",
            "discount": "500.00",
            "fine": "100.00",
            "total": "9600.00"
        },
        ...
    ]
}
```

---

## 🔄 Complete Workflow

### Step 1: Load Filter Options
```javascript
// Call /list to get filter options
fetch('http://localhost/amt/api/other-collection-report/list', {
    method: 'POST',
    headers: {
        'Client-Service': 'smartschool',
        'Auth-Key': 'schoolAdmin@',
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({})
})
.then(response => response.json())
.then(data => {
    // Populate dropdowns with:
    // - data.data.search_types
    // - data.data.classes
    // - data.data.fee_types
    // - data.data.received_by
    // - data.data.group_by
});
```

### Step 2: User Selects Filters and Clicks Search
```javascript
// Call /filter with selected filters
fetch('http://localhost/amt/api/other-collection-report/filter', {
    method: 'POST',
    headers: {
        'Client-Service': 'smartschool',
        'Auth-Key': 'schoolAdmin@',
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({
        search_type: 'this_month',
        class_id: '19',
        feetype_id: '5',
        received_by: '1',
        group: 'class'
    })
})
.then(response => response.json())
.then(data => {
    // Display report data:
    // - data.data (array of payment records)
    // - data.summary (totals)
});
```

---

## ✅ Key Points

1. **`/list` endpoint:**
   - Returns ONLY filter options
   - NO report data
   - Call this once on page load

2. **`/filter` endpoint:**
   - Returns actual report data
   - Call this when user clicks "Search"
   - Can be called with empty body {} to get current year data

3. **This matches the web interface behavior:**
   - Web page initially shows "No record found"
   - After clicking "Search", it shows the report data

---

## 🎯 Your Current Response is CORRECT!

The response you received from `/list` is exactly what it should be:
- Filter options only
- No report data

To get report data, you need to call the `/filter` endpoint with your desired filters.

---

## 📝 Testing with Postman/Thunder Client

### Test 1: Get Filter Options
```
POST http://localhost/amt/api/other-collection-report/list
Headers:
  Client-Service: smartschool
  Auth-Key: schoolAdmin@
Body: {}
```

### Test 2: Get Today's Report
```
POST http://localhost/amt/api/other-collection-report/filter
Headers:
  Client-Service: smartschool
  Auth-Key: schoolAdmin@
Body:
{
    "search_type": "today"
}
```

### Test 3: Get This Month's Report
```
POST http://localhost/amt/api/other-collection-report/filter
Headers:
  Client-Service: smartschool
  Auth-Key: schoolAdmin@
Body:
{
    "search_type": "this_month"
}
```

---

## 🔗 Related Documentation

- **Complete API Guide:** `OTHER_COLLECTION_REPORT_API_COMPLETE_GUIDE.md`
- **Web Reference:** `http://localhost/amt/financereports/other_collection_report`

