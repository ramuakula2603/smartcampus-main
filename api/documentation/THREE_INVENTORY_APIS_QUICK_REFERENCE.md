# Three Inventory Report APIs - Quick Reference Guide

## Quick Start

**Base URL:** `http://localhost/amt/api`

**Required Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

---

## 1. Inventory Stock Report API

### Get All Stock
```bash
POST /api/inventory-stock-report/filter
Body: {}
```

### Get This Month's Stock
```bash
POST /api/inventory-stock-report/filter
Body: {"search_type": "this_month"}
```

### Response Summary
```json
{
  "summary": {
    "total_items": 45,
    "total_stock_quantity": 1250,
    "total_available_quantity": 980,
    "total_issued_quantity": 270
  }
}
```

**Key Fields:**
- `available_quantity` - Items available for issue
- `total_quantity` - Total items in stock
- `total_issued` - Items currently issued

---

## 2. Add Item Report API

### Get All Added Items
```bash
POST /api/add-item-report/filter
Body: {}
```

### Get This Week's Additions
```bash
POST /api/add-item-report/filter
Body: {"search_type": "this_week"}
```

### Response Summary
```json
{
  "summary": {
    "total_items": 25,
    "total_quantity": 350,
    "total_purchase_price": "125000.00"
  }
}
```

**Key Fields:**
- `quantity` - Quantity added
- `purchase_price` - Purchase price
- `date` - Date added
- `item_supplier` - Supplier name

---

## 3. Issue Inventory Report API

### Get All Issues
```bash
POST /api/issue-inventory-report/filter
Body: {}
```

### Get Today's Issues
```bash
POST /api/issue-inventory-report/filter
Body: {"search_type": "today"}
```

### Response Summary
```json
{
  "summary": {
    "total_issues": 15,
    "total_quantity": 45,
    "total_returned": 8,
    "total_not_returned": 7
  }
}
```

**Key Fields:**
- `issue_to_info` - Staff who received item
- `issued_by_info` - Staff who issued item
- `return_status` - "Returned" or "Not Returned"
- `is_returned` - 0 or 1

---

## Search Types

| Type | Description | Example |
|------|-------------|---------|
| `today` | Today's records | `{"search_type": "today"}` |
| `this_week` | This week (Mon-Today) | `{"search_type": "this_week"}` |
| `this_month` | This month | `{"search_type": "this_month"}` |
| `last_month` | Last month | `{"search_type": "last_month"}` |
| `this_year` | This year | `{"search_type": "this_year"}` |
| `period` | Custom range | `{"search_type": "period", "date_from": "2025-01-01", "date_to": "2025-03-31"}` |

---

## Common Request Examples

### Empty Request (Default: This Year)
```json
{}
```

### This Month
```json
{
  "search_type": "this_month"
}
```

### Custom Date Range
```json
{
  "search_type": "period",
  "date_from": "2025-01-01",
  "date_to": "2025-03-31"
}
```

### Specific Dates
```json
{
  "date_from": "2025-10-01",
  "date_to": "2025-10-09"
}
```

---

## Response Structure

All APIs return the same structure:

```json
{
  "status": 1,
  "message": "Success message",
  "filters_applied": {
    "search_type": "this_month",
    "date_from": null,
    "date_to": null,
    "date_range_used": {
      "start_date": "2025-10-01",
      "end_date": "2025-10-09"
    }
  },
  "summary": {
    // API-specific summary fields
  },
  "total_records": 25,
  "data": [
    // Array of records
  ],
  "timestamp": "2025-10-09 12:34:56"
}
```

---

## Error Responses

### 401 Unauthorized
```json
{
  "status": 0,
  "message": "Unauthorized access"
}
```
**Fix:** Check headers (Client-Service and Auth-Key)

### 500 Database Error
```json
{
  "status": 0,
  "message": "Database connection error. Please ensure MySQL is running in XAMPP.",
  "error": "Connection failed"
}
```
**Fix:** Start MySQL in XAMPP

---

## cURL Examples

### Inventory Stock Report
```bash
curl -X POST http://localhost/amt/api/inventory-stock-report/filter \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Add Item Report
```bash
curl -X POST http://localhost/amt/api/add-item-report/filter \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"search_type": "this_month"}'
```

### Issue Inventory Report
```bash
curl -X POST http://localhost/amt/api/issue-inventory-report/filter \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"search_type": "this_week"}'
```

---

## Postman Quick Setup

1. **Create New Request**
   - Method: POST
   - URL: `http://localhost/amt/api/{endpoint}`

2. **Add Headers**
   - `Content-Type: application/json`
   - `Client-Service: smartschool`
   - `Auth-Key: schoolAdmin@`

3. **Add Body**
   - Type: raw
   - Format: JSON
   - Content: `{}`

4. **Send Request**

---

## PHP Quick Example

```php
function call_inventory_api($endpoint, $data = array()) {
    $url = 'http://localhost/amt/api/' . $endpoint;
    $headers = array(
        'Content-Type: application/json',
        'Client-Service: smartschool',
        'Auth-Key: schoolAdmin@'
    );
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

// Usage
$stock = call_inventory_api('inventory-stock-report/filter', array());
$additions = call_inventory_api('add-item-report/filter', array('search_type' => 'this_month'));
$issues = call_inventory_api('issue-inventory-report/filter', array('search_type' => 'this_week'));
```

---

## Comparison Table

| Feature | Stock Report | Add Item Report | Issue Report |
|---------|-------------|-----------------|--------------|
| **Main Focus** | Current stock levels | Items added | Items issued |
| **Date Field** | item_stock.date | item_stock.date | item_issue.issue_date |
| **Key Metric** | Available quantity | Purchase price | Return status |
| **Staff Info** | No | No | Yes |
| **Financial Data** | No | Yes | No |

---

## Use Case Matrix

| Use Case | API to Use | Parameters |
|----------|-----------|------------|
| Check current stock | Stock Report | `{}` |
| Track recent purchases | Add Item Report | `{"search_type": "this_month"}` |
| Find overdue items | Issue Report | `{}` (filter is_returned=0) |
| Monthly spending | Add Item Report | `{"search_type": "this_month"}` |
| Staff accountability | Issue Report | `{"search_type": "this_year"}` |
| Inventory audit | Stock Report | `{}` |

---

## Testing Checklist

- [ ] MySQL is running in XAMPP
- [ ] Headers are correct (Client-Service, Auth-Key)
- [ ] Request body is valid JSON
- [ ] Date format is YYYY-MM-DD
- [ ] Response status is 1 (success)
- [ ] Data array is not empty
- [ ] Summary fields have values

---

## Troubleshooting Quick Fixes

| Problem | Solution |
|---------|----------|
| 401 Error | Check headers exactly match |
| 500 Error | Start MySQL in XAMPP |
| Empty data | Check database has records |
| Wrong dates | Use YYYY-MM-DD format |
| HTML response | Check URL and route config |

---

## Related Documentation

- **Full Documentation:**
  - `INVENTORY_STOCK_REPORT_API_README.md`
  - `ADD_ITEM_REPORT_API_README.md`
  - `ISSUE_INVENTORY_REPORT_API_README.md`

- **Implementation Summary:**
  - `THREE_INVENTORY_APIS_IMPLEMENTATION_SUMMARY.md`

---

**Last Updated:** 2025-10-09  
**Quick Reference Version:** 1.0

