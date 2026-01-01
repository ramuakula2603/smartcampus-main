# Additional Fee Assignment API Documentation

## Overview

The Additional Fee Assignment API provides functionality for managing additional fee assignments in the school management system. This API allows updating fee amounts for additional fees that are assigned to students.

---

## Base URL
```
http://{domain}/api/
```

## Important URL Structure

**For Additional Fee Assignment APIs, use the controller/method pattern:**
- Update additional fee amount: `http://{domain}/api/additional-fee-assign/update`

**Examples:**
- Update: `http://localhost/amt/api/additional-fee-assign/update`

## Authentication Headers

All API requests require the following headers:
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

---

## Endpoints

### 1. Update Additional Fee Amount

**Endpoint:** `POST /additional-fee-assign/update`
**Full URL:** `http://localhost/amt/api/additional-fee-assign/update`

**Description:** Update the amount for an additional fee assignment.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### Request Body
```json
{
    "id": 1,
    "amount": 1500.00
}
```

#### Success Response (HTTP 200)
```json
{
  "status": "success",
  "error": ""
}
```

#### Error Response (HTTP 400)
```json
{
  "status": "fail",
  "error": {
    "feegroupid_feegroupid": "The Fee Master field is required.",
    "amount_amount": "The Amount field is required."
  }
}
```

#### cURL Example
```bash
curl -X POST "http://localhost/amt/api/additional-fee-assign/update" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "id": 1,
    "amount": 1500.00
  }'
```

---

## Request Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | integer | Yes | Additional fee assignment ID |
| amount | decimal | Yes | New fee amount |

---

## Response Fields

| Field | Type | Description |
|-------|------|-------------|
| status | string | "success" or "fail" |
| error | object/string | Error messages if status is "fail", empty string if status is "success" |

---

## Error Codes

| HTTP Code | Description |
|-----------|-------------|
| 200 | Success |
| 400 | Bad request (validation error) |
| 500 | Internal server error |

---

## Testing

Use the provided cURL examples to test the endpoint. Make sure to:

1. Replace `localhost/amt` with your actual domain
2. Ensure the authentication headers are correct
3. Verify that the ID exists in the `student_fees_amountadding` table
4. Ensure the amount is a valid decimal value

### Test Cases

#### Test Case 1: Successful Update
This test case demonstrates a successful update of an additional fee amount.

**Request:**
```bash
curl -X POST "http://localhost/amt/api/additional-fee-assign/update" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "id": 1,
    "amount": 2500.00
  }'
```

**Expected Response (HTTP 200):**
```json
{
  "status": "success",
  "error": ""
}
```

#### Test Case 2: Missing Required Fields
This test case demonstrates the validation error when required fields are missing.

**Request:**
```bash
curl -X POST "http://localhost/amt/api/additional-fee-assign/update" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "id": 1
  }'
```

**Expected Response (HTTP 400):**
```json
{
  "status": "fail",
  "error": {
    "amount_amount": "The Amount field is required."
  }
}
```

#### Test Case 3: Invalid ID
This test case demonstrates the behavior when an invalid ID is provided.

**Request:**
```bash
curl -X POST "http://localhost/amt/api/additional-fee-assign/update" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "id": 999999,
    "amount": 1500.00
  }'
```

**Expected Response (HTTP 200):**
```json
{
  "status": "success",
  "error": ""
}
```
*Note: The API may return success even with an invalid ID, depending on implementation. Check database to verify if the record was actually updated.*

#### Test Case 4: Invalid Amount Format
This test case demonstrates the validation error when an invalid amount format is provided.

**Request:**
```bash
curl -X POST "http://localhost/amt/api/additional-fee-assign/update" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "id": 1,
    "amount": "invalid_amount"
  }'
```

**Expected Response (HTTP 400):**
```json
{
  "status": "fail",
  "error": {
    "amount_amount": "The Amount field must contain a valid decimal number."
  }
}
```

---

## Notes

- All endpoints use POST method as per application standards
- Authentication is required for all endpoints
- This API specifically manages additional fee assignments (the "adding" tables)
- The functionality mirrors the web interface's `updateadditionalfee` method
- Amounts are stored with up to 2 decimal places
