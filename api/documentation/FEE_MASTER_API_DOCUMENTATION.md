# Fee Master API Documentation

## Overview

The Fee Master API provides comprehensive CRUD (Create, Read, Update, Delete) functionality for managing fee master records in the school management system. Fee masters define the relationship between fee groups, fee types, amounts, due dates, and fine structures.

---

## Base URL
```
http://{domain}/api/
```

## Important URL Structure

**For Fee Master APIs, use the controller/method pattern:**
- List fee masters: `http://{domain}/api/fee-masters/list`
- Get single fee master: `http://{domain}/api/fee-masters/get`
- Create fee master: `http://{domain}/api/fee-masters/create`
- Update fee master: `http://{domain}/api/fee-masters/update`
- Delete fee master: `http://{domain}/api/fee-masters/delete`

**Examples:**
- List all: `http://localhost/amt/api/fee-masters/list`
- Get specific: `http://localhost/amt/api/fee-masters/get`
- Create: `http://localhost/amt/api/fee-masters/create`
- Update: `http://localhost/amt/api/fee-masters/update`
- Delete: `http://localhost/amt/api/fee-masters/delete`

## Authentication Headers

All API requests require the following headers:
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

---

## Endpoints

### 1. List Fee Masters

**Endpoint:** `POST /fee-masters/list`
**Full URL:** `http://localhost/amt/api/fee-masters/list`

**Description:** Retrieve a list of all fee master records with associated fee groups and types.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### Request Body
```json
{}
```

#### Success Response (HTTP 200)
```json
{
  "status": 1,
  "message": "Fee masters retrieved successfully",
  "total_records": 2,
  "data": [
    {
      "id": 1,
      "fee_groups_id": 1,
      "group_name": "Tuition Fees",
      "session_id": 1,
      "is_system": 0,
      "feetypes": [
        {
          "id": 1,
          "feetype_id": 1,
          "type": "Monthly Fee",
          "code": "MF001",
          "amount": "5000.00",
          "due_date": "2024-01-10",
          "fine_type": "percentage",
          "fine_percentage": "5.00",
          "fine_amount": "0.00"
        }
      ]
    }
  ],
  "timestamp": "2025-10-05 12:30:45"
}
```

#### cURL Example
```bash
curl -X POST "http://localhost/amt/api/fee-masters/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

### 2. Get Single Fee Master

**Endpoint:** `POST /fee-masters/get`
**Full URL:** `http://localhost/amt/api/fee-masters/get`

**Description:** Retrieve details of a specific fee master by ID.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### Request Body
```json
{
  "id": 1
}
```

#### Success Response (HTTP 200)
```json
{
  "status": 1,
  "message": "Fee master retrieved successfully",
  "data": {
    "id": 1,
    "fee_groups_id": 1,
    "fee_group_name": "Tuition Fees",
    "feetype_id": 1,
    "type": "Monthly Fee",
    "code": "MF001",
    "session_id": 1,
    "amount": "5000.00",
    "due_date": "2024-01-10",
    "fine_type": "percentage",
    "fine_percentage": "5.00",
    "fine_amount": "0.00",
    "is_active": "yes"
  },
  "timestamp": "2025-10-05 12:30:45"
}
```

#### Error Response (HTTP 404)
```json
{
  "status": 0,
  "message": "Fee master not found"
}
```

#### cURL Example
```bash
curl -X POST "http://localhost/amt/api/fee-masters/get" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "id": 1
  }'
```

---

### 3. Create Fee Master

**Endpoint:** `POST /fee-masters/create`
**Full URL:** `http://localhost/amt/api/fee-masters/create`

**Description:** Create a new fee master record.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### Request Body
```json
{
  "fee_groups_id": 1,
  "feetype_id": 2,
  "amount": 3000.00,
  "due_date": "2024-02-15",
  "fine_type": "fix",
  "fine_amount": 100.00
}
```

#### Success Response (HTTP 201)
```json
{
  "status": 1,
  "message": "Fee master created successfully",
  "data": {
    "fee_groups_id": 1,
    "feetype_id": 2,
    "session_id": 1,
    "due_date": "2024-02-15",
    "amount": 3000.00,
    "fine_type": "fix",
    "fine_percentage": 0,
    "fine_amount": 100.00,
    "is_active": "yes"
  },
  "timestamp": "2025-10-05 12:30:45"
}
```

#### Error Response (HTTP 400)
```json
{
  "status": 0,
  "message": "Fee group ID is required"
}
```

#### cURL Example
```bash
curl -X POST "http://localhost/amt/api/fee-masters/create" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "fee_groups_id": 1,
    "feetype_id": 2,
    "amount": 3000.00,
    "due_date": "2024-02-15",
    "fine_type": "fix",
    "fine_amount": 100.00
  }'
```

---

### 4. Update Fee Master

**Endpoint:** `POST /fee-masters/update`
**Full URL:** `http://localhost/amt/api/fee-masters/update`

**Description:** Update an existing fee master record.

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
  "feetype_id": 2,
  "amount": 3500.00,
  "due_date": "2024-02-20",
  "fine_type": "percentage",
  "fine_percentage": 10.00
}
```

#### Success Response (HTTP 200)
```json
{
  "status": 1,
  "message": "Fee master updated successfully",
  "data": {
    "id": 1,
    "feetype_id": 2,
    "due_date": "2024-02-20",
    "amount": 3500.00,
    "fine_type": "percentage",
    "fine_percentage": 10.00,
    "fine_amount": 0
  },
  "timestamp": "2025-10-05 12:30:45"
}
```

#### Error Response (HTTP 404)
```json
{
  "status": 0,
  "message": "Fee master not found"
}
```

#### cURL Example
```bash
curl -X POST "http://localhost/amt/api/fee-masters/update" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "id": 1,
    "feetype_id": 2,
    "amount": 3500.00,
    "due_date": "2024-02-20",
    "fine_type": "percentage",
    "fine_percentage": 10.00
  }'
```

---

### 5. Delete Fee Master

**Endpoint:** `POST /fee-masters/delete`
**Full URL:** `http://localhost/amt/api/fee-masters/delete`

**Description:** Delete a fee master record by ID.

#### Request Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### Request Body
```json
{
  "id": 1
}
```

#### Success Response (HTTP 200)
```json
{
  "status": 1,
  "message": "Fee master deleted successfully",
  "timestamp": "2025-10-05 12:30:45"
}
```

#### Error Response (HTTP 404)
```json
{
  "status": 0,
  "message": "Fee master not found"
}
```

#### cURL Example
```bash
curl -X POST "http://localhost/amt/api/fee-masters/delete" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "id": 1
  }'
```

---

## Request Parameters

### Create Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| fee_groups_id | integer | Yes | Fee group ID |
| feetype_id | integer | Yes | Fee type ID |
| amount | decimal | Yes | Fee amount |
| due_date | string | No | Due date (YYYY-MM-DD format) |
| fine_type | string | No | Fine type: 'none', 'fix', 'percentage' |
| fine_amount | decimal | No | Fixed fine amount (when fine_type is 'fix') |
| fine_percentage | decimal | No | Fine percentage (when fine_type is 'percentage') |

### Update Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | integer | Yes | Fee master ID |
| feetype_id | integer | Yes | Fee type ID |
| amount | decimal | Yes | Fee amount |
| due_date | string | No | Due date (YYYY-MM-DD format) |
| fine_type | string | No | Fine type: 'none', 'fix', 'percentage' |
| fine_amount | decimal | No | Fixed fine amount |
| fine_percentage | decimal | No | Fine percentage |

### Get/Delete Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | integer | Yes | Fee master ID |

---

## Response Fields

| Field | Type | Description |
|-------|------|-------------|
| status | integer | 1 for success, 0 for failure |
| message | string | Human-readable message |
| data | object/array | Response data |
| timestamp | string | Server timestamp |
| total_records | integer | Total number of records (list endpoint) |

---

## Error Codes

| HTTP Code | Description |
|-----------|-------------|
| 200 | Success |
| 201 | Created successfully |
| 400 | Bad request (validation error) |
| 404 | Resource not found |
| 500 | Internal server error |

---

## Fine Types

| Type | Description |
|------|-------------|
| none | No fine applied |
| fix | Fixed fine amount |
| percentage | Percentage-based fine |

---

## Testing

Use the provided cURL examples to test each endpoint. Make sure to:

1. Replace `localhost/amt` with your actual domain
2. Ensure the authentication headers are correct
3. Verify fee groups and fee types exist before creating fee masters
4. Test different fine type scenarios

---

## Notes

- All endpoints use POST method as per application standards
- Authentication is required for all endpoints
- Fee masters link fee groups with fee types and define amounts
- Fine calculations depend on the fine_type setting
- Session ID is automatically set to current session
- Always validate fee group and fee type existence before creation
