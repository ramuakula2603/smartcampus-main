# Behavioral Module - Behaviour Setting API Documentation

## Overview

The Behavioral Module - Behaviour Setting API provides functionality for managing behaviour module settings in the school management system. This API allows retrieving and updating behaviour settings, specifically the comment options configuration.

---

## Base URL
```
http://{domain}/api/
```

## Important URL Structure

**For Behavioral Module - Behaviour Settings APIs, use the controller/method pattern:**
- Get behaviour settings: `http://{domain}/api/behaviour/setting/get`
- Update behaviour settings: `http://{domain}/api/behaviour/setting/update`

**Examples:**
- Get settings: `http://localhost/amt/api/behaviour/setting/get`
- Update settings: `http://localhost/amt/api/behaviour/setting/update`

## Authentication Headers

All API requests require the following headers:
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

---

## Endpoints

### 1. Get Behaviour Settings

**Endpoint:** `POST /behaviour/setting/get`
**Full URL:** `http://localhost/amt/api/behaviour/setting/get`

**Description:** Retrieve the current behaviour module settings.

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
    "data": {
        "id": "1",
        "comment_option": [
            "student",
            "parent"
        ]
    },
    "message": "Behaviour settings retrieved successfully"
}
```

#### Error Response (HTTP 404)
```json
{
    "status": 0,
    "message": "Behaviour settings not found"
}
```

#### Error Response (HTTP 500)
```json
{
    "status": 0,
    "message": "Internal server error: [error details]"
}
```

#### cURL Example
```bash
curl -X POST "http://localhost/amt/api/behaviour/setting/get" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

### 2. Update Behaviour Settings

**Endpoint:** `POST /behaviour/setting/update`
**Full URL:** `http://localhost/amt/api/behaviour/setting/update`

**Description:** Update the behaviour module settings.

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
    "comment_option": ["student", "parent"]
}
```

#### Success Response (HTTP 200)
```json
{
    "status": 1,
    "message": "Behaviour settings updated successfully"
}
```

#### Error Response (HTTP 400)
```json
{
    "status": 0,
    "message": "Settings ID is required"
}
```

#### Error Response (HTTP 500)
```json
{
    "status": 0,
    "message": "Failed to update behaviour settings"
}
```

#### cURL Example
```bash
curl -X POST "http://localhost/amt/api/behaviour/setting/update" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "id": 1,
    "comment_option": ["student", "parent"]
  }'
```

---

## Request Parameters

### Get Behaviour Settings
No parameters required.

### Update Behaviour Settings
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | integer | Yes | Settings record ID |
| comment_option | array | No | Array of comment options (student, parent) |

---

## Response Fields

| Field | Type | Description |
|-------|------|-------------|
| status | integer | 1 for success, 0 for failure |
| data | object/array | Response data |
| message | string | Response message |

---

## Error Codes

| HTTP Code | Description |
|-----------|-------------|
| 200 | Success |
| 400 | Bad request (validation error) |
| 404 | Not found |
| 500 | Internal server error |

---

## Testing

Use the provided cURL examples to test the endpoints. Make sure to:

1. Replace `localhost/amt` with your actual domain
2. Ensure the authentication headers are correct
3. Verify that the IDs exist in the respective tables
4. Check that you have proper permissions to access the data

### Test Cases

#### Test Case 1: Get Behaviour Settings
This test case demonstrates retrieving the current behaviour settings.

**Request:**
```bash
curl -X POST "http://localhost/amt/api/behaviour/setting/get" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Expected Response (HTTP 200):**
```json
{
    "status": 1,
    "data": {
        "id": "1",
        "comment_option": [
            "student",
            "parent"
        ]
    },
    "message": "Behaviour settings retrieved successfully"
}
```

#### Test Case 2: Update Behaviour Settings
This test case demonstrates updating the behaviour settings.

**Request:**
```bash
curl -X POST "http://localhost/amt/api/behaviour/setting/update" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "id": 1,
    "comment_option": ["student"]
  }'
```

**Expected Response (HTTP 200):**
```json
{
    "status": 1,
    "message": "Behaviour settings updated successfully"
}
```

#### Test Case 3: Missing Required Fields
This test case demonstrates the validation error when required fields are missing.

**Request:**
```bash
curl -X POST "http://localhost/amt/api/behaviour/setting/update" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Expected Response (HTTP 400):**
```json
{
    "status": 0,
    "message": "Settings ID is required"
}
```

---

## Notes

- All endpoints use POST method as per application standards
- Authentication is required for all endpoints
- This API specifically manages behaviour module settings
- The comment_option field controls which users can add comments to behaviour incidents
- Valid comment options are "student" and "parent"
- The functionality complements the existing student incidents API
