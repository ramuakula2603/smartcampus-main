# Inventory Stock Report API Documentation

## Overview

The Inventory Stock Report API provides endpoints to retrieve inventory stock information including available quantities, total quantities, and issued quantities for all items in the inventory system.

**Status:** ✅ Fully Working

**Base URL:** `http://localhost/amt/api`

## Key Features

- ✅ **Stock Information** - Shows available, total, and issued quantities
- ✅ **Date Range Filtering** - Filter by various date ranges
- ✅ **Graceful Null Handling** - Empty requests return all records
- ✅ **Comprehensive Data** - Includes item details, categories, suppliers, and stores
- ✅ **Accurate Calculations** - Correctly calculates available quantities

## Authentication

Required headers:
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

## Endpoints

### 1. List Endpoint

**URL:** `POST /api/inventory-stock-report/list`

**Description:** Get filter options including search types.

**Request Body:** `{}`

**Response Example:**
```json
{
  "status": 1,
  "message": "Filter options retrieved successfully",
  "data": {
    "search_types": {
      "today": "Today",
      "this_week": "This Week",
      "this_month": "This Month",
      "last_month": "Last Month",
      "this_year": "This Year",
      "period": "Period"
    }
  },
  "timestamp": "2025-10-09 12:34:56"
}
```

### 2. Filter Endpoint

**URL:** `POST /api/inventory-stock-report/filter`

**Description:** Get inventory stock report data.

**Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| search_type | string | No | Date range: today, this_week, this_month, last_month, this_year, period |
| date_from | string | No | Start date (YYYY-MM-DD) |
| date_to | string | No | End date (YYYY-MM-DD) |

**Request Examples:**

1. **Empty Request (All Records):**
```json
{}
```

2. **This Year:**
```json
{
  "search_type": "this_year"
}
```

3. **Custom Date Range:**
```json
{
  "search_type": "period",
  "date_from": "2025-01-01",
  "date_to": "2025-03-31"
}
```

**Response Example:**
```json
{
  "status": 1,
  "message": "Inventory stock report retrieved successfully",
  "filters_applied": {
    "search_type": "this_year",
    "date_from": null,
    "date_to": null,
    "date_range_used": {
      "start_date": "2025-01-01",
      "end_date": "2025-12-31"
    }
  },
  "summary": {
    "total_items": 45,
    "total_stock_quantity": 1250,
    "total_available_quantity": 980,
    "total_issued_quantity": 270
  },
  "total_records": 45,
  "data": [
    {
      "id": "1",
      "name": "Laptop Dell Inspiron",
      "item_category_id": "1",
      "description": "15 inch laptop",
      "item_category": "Electronics",
      "item_supplier": "Tech Supplies Inc",
      "item_store": "Main Store",
      "total_quantity": 50,
      "total_issued": 15,
      "total_not_returned": 15,
      "available_quantity": 35
    },
    {
      "id": "2",
      "name": "Office Chair",
      "item_category_id": "2",
      "description": "Ergonomic office chair",
      "item_category": "Furniture",
      "item_supplier": "Office Furniture Co",
      "item_store": "Main Store",
      "total_quantity": 100,
      "total_issued": 30,
      "total_not_returned": 30,
      "available_quantity": 70
    }
  ],
  "timestamp": "2025-10-09 12:34:56"
}
```

## Response Fields

### Summary Fields
- `total_items` - Total number of unique items
- `total_stock_quantity` - Sum of all item quantities in stock
- `total_available_quantity` - Sum of all available quantities
- `total_issued_quantity` - Sum of all issued quantities

### Data Fields (per item)
- `id` - Item ID
- `name` - Item name
- `item_category_id` - Category ID
- `description` - Item description
- `item_category` - Category name
- `item_supplier` - Supplier name
- `item_store` - Store name
- `total_quantity` - Total quantity in stock
- `total_issued` - Total quantity issued
- `total_not_returned` - Total quantity not returned
- `available_quantity` - Available quantity (calculated)

## Error Responses

### 401 Unauthorized
```json
{
  "status": 0,
  "message": "Unauthorized access"
}
```

### 500 Database Error
```json
{
  "status": 0,
  "message": "Database connection error. Please ensure MySQL is running in XAMPP.",
  "error": "Connection failed"
}
```

## Usage Examples

### cURL Example
```bash
curl -X POST http://localhost/amt/api/inventory-stock-report/filter \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Postman Example
1. Method: POST
2. URL: `http://localhost/amt/api/inventory-stock-report/filter`
3. Headers:
   - `Content-Type: application/json`
   - `Client-Service: smartschool`
   - `Auth-Key: schoolAdmin@`
4. Body (raw JSON): `{}`

### PHP Example
```php
$url = 'http://localhost/amt/api/inventory-stock-report/filter';
$headers = array(
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
);
$data = array('search_type' => 'this_month');

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);
print_r($result);
```

## Technical Details

### Database Tables Used
- `item_stock` - Item stock records
- `item` - Item master data
- `item_category` - Item categories
- `item_supplier` - Supplier information
- `item_store` - Store information
- `item_issue` - Item issue records

### Calculation Logic

**Available Quantity** = Total Stock - Issued (Not Returned)

The API uses a complex query to calculate:
1. `added_stock` - Sum of all quantities added to stock
2. `issued` - Sum of all quantities issued and not returned
3. `available_quantity` - Difference between added_stock and issued

## Notes

- Empty request body `{}` returns all inventory stock records
- Date filtering is applied to the `item_stock.date` field
- Available quantity is calculated dynamically for accuracy
- All quantities are returned as integers
- Negative available quantities are returned as 0

## Related APIs

- **Add Item Report API** - Shows items added to inventory
- **Issue Inventory Report API** - Shows items issued to staff

## Support

For issues or questions, please contact the development team.

---

**Last Updated:** 2025-10-09  
**API Version:** 1.0  
**Status:** Production Ready

