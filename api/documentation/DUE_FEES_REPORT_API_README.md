# Due Fees Report API Documentation

## Overview

The Due Fees Report API provides endpoints to retrieve information about students with pending/due fees. This API allows filtering by class and section and returns comprehensive data about students, their due fees, fee breakdowns, and transport fees.

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

### 1. Filter Due Fees Report

**Endpoint:** `POST /api/due-fees-report/filter`

#### Request Body (All Optional)

```json
{
  "class_id": "1",
  "section_id": "2"
}
```

#### Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| class_id | string | No | Class ID to filter students |
| section_id | string | No | Section ID to filter students |

**Note:** Empty request `{}` returns all students with due fees across all classes and sections.

#### Response Example

```json
{
  "status": 1,
  "message": "Due fees report retrieved successfully",
  "filters_applied": {
    "class_id": "1",
    "section_id": "2",
    "date": "2025-10-07"
  },
  "total_records": 2,
  "data": [
    {
      "admission_no": "STU001",
      "class_id": "1",
      "section_id": "2",
      "student_id": "123",
      "roll_no": "1",
      "admission_date": "2024-04-01",
      "firstname": "John",
      "middlename": "",
      "lastname": "Doe",
      "father_name": "Robert Doe",
      "image": "student.jpg",
      "mobileno": "1234567890",
      "email": "john@example.com",
      "state": "California",
      "city": "Los Angeles",
      "pincode": "90001",
      "class": "Class 1",
      "section": "A",
      "fee_groups_feetype_ids": ["1", "2", "3"],
      "fees_list": [
        {
          "id": "1",
          "fee_session_group_id": "1",
          "student_session_id": "456",
          "amount": "1000.00",
          "is_system": "0",
          "fee_groups_id": "1",
          "session_id": "1",
          "name": "Tuition Fee",
          "fee_amount": "1000.00",
          "fee_groups_feetype_id": "1",
          "fine_type": "percentage",
          "due_date": "2025-09-30",
          "fine_percentage": "5.00",
          "fine_amount": "50.00",
          "student_fees_deposite_id": "0",
          "amount_detail": "0",
          "type": "Monthly",
          "code": "TF"
        }
      ],
      "transport_fees": [
        {
          "id": "1",
          "student_session_id": "456",
          "transport_feemaster_id": "1",
          "route_pickup_point_id": "1",
          "month": "October",
          "due_date": "2025-10-05",
          "fees": "500.00",
          "fine_amount": "25.00",
          "fine_type": "percentage",
          "fine_percentage": "5.00",
          "student_fees_deposite_id": "0",
          "amount_detail": "0"
        }
      ]
    }
  ],
  "timestamp": "2025-10-07 12:00:00"
}
```

#### Key Response Fields

| Field | Description |
|-------|-------------|
| admission_no | Student admission number |
| class_id | Class ID |
| section_id | Section ID |
| student_id | Student ID |
| roll_no | Student roll number |
| firstname, middlename, lastname | Student name |
| father_name | Father's name |
| mobileno | Contact number |
| email | Email address |
| class | Class name |
| section | Section name |
| fee_groups_feetype_ids | Array of fee type IDs with dues |
| fees_list | Array of fee details with amounts and due dates |
| transport_fees | Array of transport fee details (if applicable) |

---

### 2. List Filter Options

**Endpoint:** `POST /api/due-fees-report/list`

#### Response

```json
{
  "status": 1,
  "message": "Due fees filter options retrieved successfully",
  "classes": [
    {
      "id": "1",
      "class": "Class 1",
      "sections": "A,B,C"
    },
    {
      "id": "2",
      "class": "Class 2",
      "sections": "A,B"
    }
  ],
  "note": "Use the filter endpoint with class_id and section_id to get due fees report",
  "timestamp": "2025-10-07 12:00:00"
}
```

---

## Usage Examples

### Example 1: Get All Students with Due Fees (Empty Request)

```bash
curl -X POST "http://localhost/amt/api/due-fees-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 2: Filter by Class

```bash
curl -X POST "http://localhost/amt/api/due-fees-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "class_id": "1"
  }'
```

### Example 3: Filter by Class and Section

```bash
curl -X POST "http://localhost/amt/api/due-fees-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "class_id": "1",
    "section_id": "2"
  }'
```

### Example 4: Get Filter Options

```bash
curl -X POST "http://localhost/amt/api/due-fees-report/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 5: JavaScript (Fetch API)

```javascript
fetch('http://localhost/amt/api/due-fees-report/filter', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Client-Service': 'smartschool',
    'Auth-Key': 'schoolAdmin@'
  },
  body: JSON.stringify({
    class_id: '1',
    section_id: '2'
  })
})
.then(response => response.json())
.then(data => {
  console.log('Total Students with Due Fees:', data.total_records);
  data.data.forEach(student => {
    console.log(`${student.firstname} ${student.lastname} - ${student.class} ${student.section}`);
  });
});
```

### Example 6: PHP

```php
$ch = curl_init('http://localhost/amt/api/due-fees-report/filter');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'class_id' => '1'
]));
$response = curl_exec($ch);
curl_close($ch);
$data = json_decode($response, true);

// Calculate total due amount
$total_due = 0;
foreach ($data['data'] as $student) {
    foreach ($student['fees_list'] as $fee) {
        if ($fee['student_fees_deposite_id'] == '0') {
            $total_due += floatval($fee['fee_amount']);
        }
    }
}
echo "Total Due Amount: $" . $total_due;
```

### Example 7: Python

```python
import requests

url = 'http://localhost/amt/api/due-fees-report/filter'
headers = {
    'Content-Type': 'application/json',
    'Client-Service': 'smartschool',
    'Auth-Key': 'schoolAdmin@'
}
payload = {'class_id': '1', 'section_id': '2'}
response = requests.post(url, headers=headers, json=payload)
data = response.json()

# Print students with due fees
for student in data['data']:
    print(f"{student['firstname']} {student['lastname']} - Due Fees: {len(student['fees_list'])}")
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

1. **Empty Request:** Use `{}` to get all students with due fees
2. **Class Filtering:** Use `class_id` to filter by specific class
3. **Section Filtering:** Use both `class_id` and `section_id` for specific section
4. **Due Date Calculation:** API automatically calculates dues based on current date
5. **Transport Fees:** Check `transport_fees` array for transport-related dues

---

## Notes

- Due fees are calculated based on current date
- Only students with unpaid or partially paid fees are returned
- Includes both regular fees and transport fees
- `student_fees_deposite_id = "0"` indicates unpaid fee
- Fine amounts are calculated based on due date and fine type

---

## Related APIs

- Student Report API
- Fee Master API
- Class Report API

---

**API Version:** 1.0  
**Last Updated:** October 7, 2025  
**Status:** Production Ready

