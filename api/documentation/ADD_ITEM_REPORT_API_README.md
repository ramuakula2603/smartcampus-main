# Add Item Report API Documentation

## Overview

The Add Item Report API provides endpoints to retrieve information about items added to the inventory system within a specified date range, including quantities and purchase prices.

**Status:** ✅ Fully Working

**Base URL:** `http://localhost/amt/api`

## Key Features

- ✅ **Date Range Filtering** - Filter items added within specific date ranges
- ✅ **Purchase Information** - Includes quantities and purchase prices
- ✅ **Graceful Null Handling** - Empty requests return all records
- ✅ **Comprehensive Data** - Includes item details, categories, suppliers, and stores
- ✅ **Financial Summary** - Total quantities and purchase prices

## Authentication

Required headers:
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

## Endpoints

### 1. List Endpoint

**URL:** `POST /api/add-item-report/list`

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

**URL:** `POST /api/add-item-report/filter`

**Description:** Get add item report data.

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

2. **This Month:**
```json
{
  "search_type": "this_month"
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
  "message": "Add item report retrieved successfully",
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
    "total_items": 25,
    "total_quantity": 350,
    "total_purchase_price": "125000.00"
  },
  "total_records": 25,
  "data": [
    {
      "id": "1",
      "item_id": "5",
      "supplier_id": "2",
      "store_id": "1",
      "quantity": 20,
      "purchase_price": "15000.00",
      "date": "2025-10-05",
      "attachment": null,
      "description": "Bulk purchase",
      "name": "Laptop Dell Inspiron",
      "item_category_id": "1",
      "des": "15 inch laptop",
      "item_category": "Electronics",
      "item_supplier": "Tech Supplies Inc",
      "item_store": "Main Store"
    },
    {
      "id": "2",
      "item_id": "8",
      "supplier_id": "3",
      "store_id": "1",
      "quantity": 50,
      "purchase_price": "25000.00",
      "date": "2025-10-03",
      "attachment": null,
      "description": "Office supplies",
      "name": "Office Chair",
      "item_category_id": "2",
      "des": "Ergonomic office chair",
      "item_category": "Furniture",
      "item_supplier": "Office Furniture Co",
      "item_store": "Main Store"
    }
  ],
  "timestamp": "2025-10-09 12:34:56"
}
```

## Response Fields

### Summary Fields
- `total_items` - Total number of item additions
- `total_quantity` - Sum of all quantities added
- `total_purchase_price` - Sum of all purchase prices

### Data Fields (per item addition)
- `id` - Stock record ID
- `item_id` - Item ID
- `supplier_id` - Supplier ID
- `store_id` - Store ID
- `quantity` - Quantity added
- `purchase_price` - Purchase price (formatted to 2 decimals)
- `date` - Date item was added
- `attachment` - Attachment file (if any)
- `description` - Description/notes
- `name` - Item name
- `item_category_id` - Category ID
- `des` - Item description
- `item_category` - Category name
- `item_supplier` - Supplier name
- `item_store` - Store name

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
curl -X POST http://localhost/amt/api/add-item-report/filter \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"search_type": "this_month"}'
```

### Postman Example
1. Method: POST
2. URL: `http://localhost/amt/api/add-item-report/filter`
3. Headers:
   - `Content-Type: application/json`
   - `Client-Service: smartschool`
   - `Auth-Key: schoolAdmin@`
4. Body (raw JSON):
```json
{
  "search_type": "this_month"
}
```

### PHP Example
```php
$url = 'http://localhost/amt/api/add-item-report/filter';
$headers = array(
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
);
$data = array(
    'search_type' => 'period',
    'date_from' => '2025-01-01',
    'date_to' => '2025-03-31'
);

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
- `item_stock` - Item stock records (main table)
- `item` - Item master data
- `item_category` - Item categories
- `item_supplier` - Supplier information
- `item_store` - Store information

### Date Filtering
- Date filtering is applied to the `item_stock.date` field
- Format: YYYY-MM-DD
- Inclusive of both start and end dates

## Notes

- Empty request body `{}` returns all item additions for the current year
- Results are ordered by stock ID in descending order (newest first)
- Purchase prices are formatted to 2 decimal places
- All quantities are returned as integers
- The API shows individual stock additions, not aggregated by item

## Use Cases

1. **Track Recent Purchases** - Monitor items added in the last week/month
2. **Financial Analysis** - Calculate total spending on inventory
3. **Supplier Analysis** - Identify which suppliers provided items
4. **Audit Trail** - Review all items added within a specific period
5. **Budget Planning** - Analyze purchase patterns and costs

## Related APIs

- **Inventory Stock Report API** - Shows current stock levels
- **Issue Inventory Report API** - Shows items issued to staff

## Support

For issues or questions, please contact the development team.

---

**Last Updated:** 2025-10-09  
**API Version:** 1.0  
**Status:** Production Ready

