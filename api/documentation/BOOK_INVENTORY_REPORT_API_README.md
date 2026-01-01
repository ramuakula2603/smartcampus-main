# Book Inventory Report API Documentation

## Overview

The Book Inventory Report API provides endpoints to retrieve book inventory information including stock details, issued quantities, and available quantities.

**Base URL:** `http://localhost/amt/api`

---

## Authentication

Required headers:
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

---

## Endpoints

### 1. Filter Book Inventory Report

**Endpoint:** `POST /api/book-inventory-report/filter`

#### Request Body (All Optional)

```json
{
  "search_type": "this_year",
  "from_date": "2025-01-01",
  "to_date": "2025-12-31"
}
```

#### Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| search_type | string | No | `today`, `this_week`, `this_month`, `this_year` |
| from_date | string | No | Start date (YYYY-MM-DD) |
| to_date | string | No | End date (YYYY-MM-DD) |

#### Response Example

```json
{
  "status": 1,
  "message": "Book inventory report retrieved successfully",
  "filters_applied": {
    "search_type": "this_year",
    "from_date": null,
    "to_date": null,
    "date_range_used": {
      "start_date": "2025-01-01",
      "end_date": "2025-12-31"
    }
  },
  "total_records": 2,
  "data": [
    {
      "id": "1",
      "book_title": "Introduction to Programming",
      "book_no": "BK001",
      "isbn_no": "978-0-123456-78-9",
      "subject": "Computer Science",
      "author": "John Doe",
      "publisher": "Tech Publishers",
      "publish": "2024",
      "rack_no": "A1",
      "qty": "10",
      "perunitcost": "500.00",
      "postdate": "2025-01-01",
      "description": "Beginner programming book",
      "total_issue": "3",
      "available_qty": "7",
      "issued_qty": "3"
    },
    {
      "id": "2",
      "book_title": "Advanced Mathematics",
      "book_no": "BK002",
      "isbn_no": "978-0-987654-32-1",
      "subject": "Mathematics",
      "author": "Jane Wilson",
      "publisher": "Math Books Inc",
      "publish": "2024",
      "rack_no": "B2",
      "qty": "15",
      "perunitcost": "600.00",
      "postdate": "2025-01-15",
      "description": "Advanced math concepts",
      "total_issue": "0",
      "available_qty": "15",
      "issued_qty": "0"
    }
  ],
  "timestamp": "2025-10-07 12:00:00"
}
```

#### Key Response Fields

| Field | Description |
|-------|-------------|
| id | Book ID |
| book_title | Title of the book |
| book_no | Book number/code |
| isbn_no | ISBN number |
| subject | Book subject/category |
| author | Book author |
| publisher | Publisher name |
| publish | Publication year |
| rack_no | Rack/shelf number |
| qty | Total quantity in stock |
| perunitcost | Cost per unit |
| postdate | Date when book was added |
| description | Book description |
| total_issue | Number of books currently issued |
| available_qty | Available quantity (qty - total_issue) |
| issued_qty | Number of books issued (same as total_issue) |

**Note:** `available_qty` is automatically calculated as `qty - total_issue`.

---

### 2. List Filter Options

**Endpoint:** `POST /api/book-inventory-report/list`

#### Response

```json
{
  "status": 1,
  "message": "Book inventory filter options retrieved successfully",
  "search_types": [
    {"value": "today", "label": "Today"},
    {"value": "this_week", "label": "This Week"},
    {"value": "this_month", "label": "This Month"},
    {"value": "this_year", "label": "This Year"}
  ],
  "note": "Use the filter endpoint with search_type or custom date range (from_date, to_date) to get book inventory report",
  "timestamp": "2025-10-07 12:00:00"
}
```

---

## Usage Examples

### Example 1: Get All Book Inventory (Empty Request)

```bash
curl -X POST "http://localhost/amt/api/book-inventory-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 2: Filter by Search Type (This Year)

```bash
curl -X POST "http://localhost/amt/api/book-inventory-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "search_type": "this_year"
  }'
```

### Example 3: Filter by Custom Date Range

```bash
curl -X POST "http://localhost/amt/api/book-inventory-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "from_date": "2024-01-01",
    "to_date": "2024-12-31"
  }'
```

### Example 4: Get Filter Options

```bash
curl -X POST "http://localhost/amt/api/book-inventory-report/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 5: JavaScript (Fetch API)

```javascript
fetch('http://localhost/amt/api/book-inventory-report/filter', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Client-Service': 'smartschool',
    'Auth-Key': 'schoolAdmin@'
  },
  body: JSON.stringify({
    search_type: 'this_year'
  })
})
.then(response => response.json())
.then(data => {
  console.log('Total Books:', data.total_records);
  data.data.forEach(book => {
    console.log(`${book.book_title}: ${book.available_qty}/${book.qty} available`);
  });
});
```

### Example 6: PHP

```php
$ch = curl_init('http://localhost/amt/api/book-inventory-report/filter');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'search_type' => 'this_year'
]));
$response = curl_exec($ch);
curl_close($ch);
$data = json_decode($response, true);

// Calculate total inventory value
$total_value = 0;
foreach ($data['data'] as $book) {
    $total_value += $book['qty'] * $book['perunitcost'];
}
echo "Total Inventory Value: $" . $total_value;
```

### Example 7: Python

```python
import requests

url = 'http://localhost/amt/api/book-inventory-report/filter'
headers = {
    'Content-Type': 'application/json',
    'Client-Service': 'smartschool',
    'Auth-Key': 'schoolAdmin@'
}
payload = {'search_type': 'this_year'}
response = requests.post(url, headers=headers, json=payload)
data = response.json()

# Find books with low stock
low_stock_books = [
    book for book in data['data'] 
    if int(book['available_qty']) < 5
]
print(f"Books with low stock: {len(low_stock_books)}")
```

---

## Error Handling

### Error Response Format

```json
{
  "status": 0,
  "message": "Error description"
}
```

### Common Errors

| HTTP Code | Message | Solution |
|-----------|---------|----------|
| 400 | Bad request. Only POST method allowed. | Use POST method |
| 401 | Unauthorized access | Check authentication headers |
| 500 | Internal server error | Check server logs |

---

## Best Practices

1. **Empty Request:** Use `{}` to get all book inventory for current year
2. **Stock Monitoring:** Use `available_qty` to track available books
3. **Inventory Value:** Calculate total value using `qty * perunitcost`
4. **Low Stock Alerts:** Monitor `available_qty` for low stock warnings
5. **Date Range Priority:** Custom date range overrides search_type

---

## Use Cases

### 1. Stock Management
```javascript
// Find books with low stock
const lowStockBooks = data.data.filter(book => 
  parseInt(book.available_qty) < 5
);
```

### 2. Inventory Valuation
```javascript
// Calculate total inventory value
const totalValue = data.data.reduce((sum, book) => 
  sum + (parseInt(book.qty) * parseFloat(book.perunitcost)), 0
);
```

### 3. Availability Check
```javascript
// Check if book is available
const isAvailable = (bookId) => {
  const book = data.data.find(b => b.id === bookId);
  return book && parseInt(book.available_qty) > 0;
};
```

### 4. Subject-wise Inventory
```javascript
// Group books by subject
const bySubject = data.data.reduce((acc, book) => {
  acc[book.subject] = (acc[book.subject] || 0) + parseInt(book.qty);
  return acc;
}, {});
```

---

## Notes

- Default date range: current year
- Date filtering is based on `postdate` (when book was added)
- `available_qty` is automatically calculated
- `total_issue` shows currently issued books (not returned)
- Date format: YYYY-MM-DD
- All quantity fields are strings (convert to int for calculations)

---

## Calculated Fields

The API automatically calculates:
- **available_qty:** `qty - total_issue`
- **issued_qty:** Same as `total_issue`

These fields help track book availability without additional calculations.

---

## Related APIs

- Issue Return Report API
- Student Book Issue Report API
- Book Due Report API

---

**API Version:** 1.0  
**Last Updated:** October 7, 2025  
**Status:** Production Ready

