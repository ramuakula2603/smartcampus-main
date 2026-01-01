# Student Fee Payment Search API Documentation

## Overview

The Student Fee Payment Search API provides comprehensive search functionality for finding student fee payment records. This API supports searching by payment ID, invoice ID, and transport fee payments, mirroring the functionality of the `studentfee/searchpayment` page.

---

## Base URL
```
http://{domain}/api/
```

## Important URL Structure

**For Student Fee Payment Search APIs, use the controller/method pattern:**
- Search by payment ID: `http://{domain}/api/student-fee-payment-search/by-payment-id`
- Search by invoice ID: `http://{domain}/api/student-fee-payment-search/by-invoice-id`
- Search transport fee: `http://{domain}/api/student-fee-payment-search/transport-fee`
- Get receipt details: `http://{domain}/api/student-fee-payment-search/receipt`
- Validate payment ID: `http://{domain}/api/student-fee-payment-search/validate-payment-id`

**Examples:**
- Search by payment ID: `http://localhost/amt/api/student-fee-payment-search/by-payment-id`
- Search by invoice ID: `http://localhost/amt/api/student-fee-payment-search/by-invoice-id`
- Get receipt: `http://localhost/amt/api/student-fee-payment-search/receipt`

## Authentication Headers

All API requests require the following headers:
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

---

## Endpoints

### 1. Search by Payment ID

**Endpoint:** `POST /student-fee-payment-search/by-payment-id`
**Full URL:** `http://localhost/amt/api/student-fee-payment-search/by-payment-id`

**Description:** Search payment records using payment ID. Supports both invoice format (id/sub_id) and single payment ID.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### Request Body
```json
{
  "payment_id": "123/456"
}
```

#### Success Response (HTTP 200)
```json
{
  "status": 1,
  "message": "Payment details retrieved successfully",
  "payment_id": "123/456",
  "data": {
    "fee_payment": [
      {
        "id": 1,
        "invoice": "123/456",
        "student_session_id": 101,
        "admission_no": "ADM001",
        "firstname": "John",
        "lastname": "Doe",
        "father_name": "Robert Doe",
        "class": "Class 1",
        "section": "A",
        "fee_group_name": "Tuition Fees",
        "type": "Monthly Fee",
        "amount": "5000.00",
        "amount_detail": "5000.00",
        "amount_fine": "0.00",
        "amount_discount": "0.00",
        "date": "2024-01-15",
        "payment_mode": "Cash",
        "description": "Monthly tuition fee payment"
      }
    ]
  },
  "timestamp": "2025-10-05 12:30:45"
}
```

#### Error Response (HTTP 404)
```json
{
  "status": 0,
  "message": "No payment found with the provided payment ID",
  "payment_id": "123/456"
}
```

#### cURL Example
```bash
curl -X POST "http://localhost/amt/api/student-fee-payment-search/by-payment-id" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "payment_id": "123/456"
  }'
```

---

### 2. Search by Invoice ID

**Endpoint:** `POST /student-fee-payment-search/by-invoice-id`
**Full URL:** `http://localhost/amt/api/student-fee-payment-search/by-invoice-id`

**Description:** Search payment records using separate invoice ID and sub-invoice ID.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### Request Body
```json
{
  "invoice_id": "123",
  "sub_invoice_id": "456"
}
```

#### Success Response (HTTP 200)
```json
{
  "status": 1,
  "message": "Payment details retrieved successfully",
  "invoice_id": "123",
  "sub_invoice_id": "456",
  "data": [
    {
      "id": 1,
      "invoice": "123/456",
      "student_session_id": 101,
      "admission_no": "ADM001",
      "firstname": "John",
      "lastname": "Doe",
      "father_name": "Robert Doe",
      "class": "Class 1",
      "section": "A",
      "fee_group_name": "Tuition Fees",
      "type": "Monthly Fee",
      "amount": "5000.00",
      "amount_detail": "5000.00",
      "amount_fine": "0.00",
      "amount_discount": "0.00",
      "date": "2024-01-15",
      "payment_mode": "Cash",
      "description": "Monthly tuition fee payment"
    }
  ],
  "timestamp": "2025-10-05 12:30:45"
}
```

#### cURL Example
```bash
curl -X POST "http://localhost/amt/api/student-fee-payment-search/by-invoice-id" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "invoice_id": "123",
    "sub_invoice_id": "456"
  }'
```

---

### 3. Search Transport Fee

**Endpoint:** `POST /student-fee-payment-search/transport-fee`
**Full URL:** `http://localhost/amt/api/student-fee-payment-search/transport-fee`

**Description:** Search transport fee payment records by payment ID.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### Request Body
```json
{
  "payment_id": "789"
}
```

#### Success Response (HTTP 200)
```json
{
  "status": 1,
  "message": "Transport fee payment details retrieved successfully",
  "payment_id": "789",
  "data": [
    {
      "id": 789,
      "student_session_id": 102,
      "admission_no": "ADM002",
      "firstname": "Jane",
      "lastname": "Smith",
      "class": "Class 2",
      "section": "B",
      "route_title": "Route A - Main Street",
      "vehicle_no": "BUS001",
      "amount": "2000.00",
      "amount_fine": "0.00",
      "amount_discount": "0.00",
      "date": "2024-01-20",
      "payment_mode": "Online",
      "description": "Monthly transport fee"
    }
  ],
  "timestamp": "2025-10-05 12:30:45"
}
```

#### cURL Example
```bash
curl -X POST "http://localhost/amt/api/student-fee-payment-search/transport-fee" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "payment_id": "789"
  }'
```

---

### 4. Get Receipt Details

**Endpoint:** `POST /student-fee-payment-search/receipt`
**Full URL:** `http://localhost/amt/api/student-fee-payment-search/receipt`

**Description:** Get detailed receipt information for a payment ID.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### Request Body
```json
{
  "payment_id": "123/456"
}
```

#### Success Response (HTTP 200)
```json
{
  "status": 1,
  "message": "Receipt details retrieved successfully",
  "payment_id": "123/456",
  "data": {
    "type": "fee_payment",
    "payment_details": [
      {
        "id": 1,
        "invoice": "123/456",
        "student_session_id": 101,
        "admission_no": "ADM001",
        "firstname": "John",
        "lastname": "Doe",
        "father_name": "Robert Doe",
        "class": "Class 1",
        "section": "A",
        "fee_group_name": "Tuition Fees",
        "type": "Monthly Fee",
        "amount": "5000.00",
        "amount_detail": "5000.00",
        "amount_fine": "0.00",
        "amount_discount": "0.00",
        "date": "2024-01-15",
        "payment_mode": "Cash",
        "description": "Monthly tuition fee payment"
      }
    ],
    "invoice_id": "123",
    "sub_invoice_id": "456"
  },
  "timestamp": "2025-10-05 12:30:45"
}
```

#### cURL Example
```bash
curl -X POST "http://localhost/amt/api/student-fee-payment-search/receipt" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "payment_id": "123/456"
  }'
```

---

### 5. Validate Payment ID

**Endpoint:** `POST /student-fee-payment-search/validate-payment-id`
**Full URL:** `http://localhost/amt/api/student-fee-payment-search/validate-payment-id`

**Description:** Validate the format of a payment ID and parse its components.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### Request Body
```json
{
  "payment_id": "123/456"
}
```

#### Success Response (HTTP 200)
```json
{
  "status": 1,
  "message": "Payment ID validation completed",
  "data": {
    "payment_id": "123/456",
    "is_valid": true,
    "format": "invoice_format",
    "parts": {
      "invoice_id": "123",
      "sub_invoice_id": "456"
    }
  },
  "timestamp": "2025-10-05 12:30:45"
}
```

#### Success Response for Numeric ID (HTTP 200)
```json
{
  "status": 1,
  "message": "Payment ID validation completed",
  "data": {
    "payment_id": "789",
    "is_valid": true,
    "format": "numeric_id",
    "parts": {
      "payment_id": "789"
    }
  },
  "timestamp": "2025-10-05 12:30:45"
}
```

#### cURL Example
```bash
curl -X POST "http://localhost/amt/api/student-fee-payment-search/validate-payment-id" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "payment_id": "123/456"
  }'
```

---

## Request Parameters

### Search by Payment ID Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| payment_id | string | Yes | Payment ID (format: "id/sub_id" or single ID) |

### Search by Invoice ID Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| invoice_id | string | Yes | Invoice ID |
| sub_invoice_id | string | No | Sub-invoice ID |

### Transport Fee Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| payment_id | string | Yes | Transport fee payment ID |

### Receipt Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| payment_id | string | Yes | Payment ID for receipt |

### Validate Payment ID Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| payment_id | string | Yes | Payment ID to validate |

---

## Response Fields

| Field | Type | Description |
|-------|------|-------------|
| status | integer | 1 for success, 0 for failure |
| message | string | Human-readable message |
| data | object/array | Response data |
| timestamp | string | Server timestamp |
| payment_id | string | Searched payment ID |

---

## Payment ID Formats

| Format | Example | Description |
|--------|---------|-------------|
| Invoice Format | "123/456" | Standard fee payment format |
| Numeric ID | "789" | Transport fee or single payment ID |

---

## Error Codes

| HTTP Code | Description |
|-----------|-------------|
| 200 | Success |
| 400 | Bad request (validation error) |
| 404 | Payment not found |
| 500 | Internal server error |

---

## Testing

Use the provided cURL examples to test each endpoint. Make sure to:

1. Replace `localhost/amt` with your actual domain
2. Ensure the authentication headers are correct
3. Test with valid payment IDs from your database
4. Test both invoice format (123/456) and numeric format (789)
5. Verify transport fee payments separately

---

## Notes

- All endpoints use POST method as per application standards
- Authentication is required for all endpoints
- Payment ID format determines search method (invoice vs transport fee)
- Receipt endpoint provides formatted data suitable for printing
- Validation endpoint helps verify payment ID format before searching
- Transport fees and regular fees are stored in separate tables
- Always validate payment ID format before making search requests
