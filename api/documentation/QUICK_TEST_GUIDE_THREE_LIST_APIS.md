# Quick Test Guide - Three List APIs

## Overview
This guide provides quick testing instructions for the three newly created list APIs.

---

## Prerequisites
- Server running at `http://localhost/amt/`
- Database with sample data in `income_head`, `expense_head`, and `roles` tables
- API authentication headers configured

---

## API Endpoints

### 1. Income Head List API
**URL:** `http://localhost/amt/api/income-head-list/list`

### 2. Expense Head List API
**URL:** `http://localhost/amt/api/expense-head-list/list`

### 3. Roles List API
**URL:** `http://localhost/amt/api/roles-list/list`

---

## Required Headers (All APIs)
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

---

## Quick Test Commands

### Using cURL

#### Test Income Head List API
```bash
curl -X POST "http://localhost/amt/api/income-head-list/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d "{}"
```

#### Test Expense Head List API
```bash
curl -X POST "http://localhost/amt/api/expense-head-list/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d "{}"
```

#### Test Roles List API
```bash
curl -X POST "http://localhost/amt/api/roles-list/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d "{}"
```

---

## Using Postman

### Setup
1. Create a new POST request
2. Set URL to one of the endpoints above
3. Go to **Headers** tab and add:
   - `Content-Type: application/json`
   - `Client-Service: smartschool`
   - `Auth-Key: schoolAdmin@`
4. Go to **Body** tab, select **raw** and **JSON**
5. Enter: `{}`
6. Click **Send**

### Expected Response Format
```json
{
    "status": 1,
    "message": "Records retrieved successfully",
    "total_records": 5,
    "data": [
        {
            "id": "1",
            "...": "..."
        }
    ],
    "timestamp": "2025-10-11 14:30:00"
}
```

---

## Using Browser Console (JavaScript)

### Test Income Head List API
```javascript
fetch('http://localhost/amt/api/income-head-list/list', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Client-Service': 'smartschool',
        'Auth-Key': 'schoolAdmin@'
    },
    body: JSON.stringify({})
})
.then(response => response.json())
.then(data => console.log('Income Heads:', data))
.catch(error => console.error('Error:', error));
```

### Test Expense Head List API
```javascript
fetch('http://localhost/amt/api/expense-head-list/list', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Client-Service': 'smartschool',
        'Auth-Key': 'schoolAdmin@'
    },
    body: JSON.stringify({})
})
.then(response => response.json())
.then(data => console.log('Expense Heads:', data))
.catch(error => console.error('Error:', error));
```

### Test Roles List API
```javascript
fetch('http://localhost/amt/api/roles-list/list', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Client-Service': 'smartschool',
        'Auth-Key': 'schoolAdmin@'
    },
    body: JSON.stringify({})
})
.then(response => response.json())
.then(data => console.log('Roles:', data))
.catch(error => console.error('Error:', error));
```

---

## Expected Success Response

### Status Code: 200 OK

### Response Body Structure:
```json
{
    "status": 1,
    "message": "Records retrieved successfully",
    "total_records": <number>,
    "data": [<array of records>],
    "timestamp": "<current timestamp>"
}
```

---

## Common Error Responses

### 401 Unauthorized
**Cause:** Invalid or missing authentication headers

**Response:**
```json
{
    "status": 0,
    "message": "Unauthorized access. Invalid headers.",
    "data": null
}
```

**Solution:** Check that headers are correct:
- `Client-Service: smartschool`
- `Auth-Key: schoolAdmin@`

---

### 405 Method Not Allowed
**Cause:** Using GET instead of POST

**Response:**
```json
{
    "status": 0,
    "message": "Method not allowed. Use POST method.",
    "data": null
}
```

**Solution:** Change request method to POST

---

### 500 Internal Server Error
**Cause:** Server-side error (database connection, model loading, etc.)

**Response:**
```json
{
    "status": 0,
    "message": "Internal server error occurred",
    "data": null
}
```

**Solution:** 
1. Check server logs
2. Verify database connection
3. Ensure models exist and are loaded correctly

---

## Verification Checklist

### For Each API:
- [ ] Returns status code 200
- [ ] Response has `status: 1`
- [ ] Response has appropriate success message
- [ ] Response has `total_records` field
- [ ] Response has `data` array
- [ ] Response has `timestamp` field
- [ ] Data array contains expected fields
- [ ] Empty request `{}` works without errors

---

## Sample Data Verification

### Income Head List API
Expected fields in each record:
- `id`
- `income_category`
- `description`
- `is_active`
- `is_deleted`
- `created_at`
- `updated_at`

### Expense Head List API
Expected fields in each record:
- `id`
- `exp_category`
- `description`
- `is_active`
- `is_deleted`
- `created_at`
- `updated_at`

### Roles List API
Expected fields in each record:
- `id`
- `name`
- `slug`
- `is_active`
- `is_system`
- `is_superadmin`
- `created_at`
- `updated_at`

---

## Troubleshooting

### No Data Returned
**Issue:** `data` array is empty `[]`

**Possible Causes:**
1. Database tables are empty
2. All records have `is_deleted = 'yes'`
3. Database connection issue

**Solution:**
1. Check database tables have records
2. Insert sample data if needed
3. Verify database connection in config

### Headers Not Working
**Issue:** Getting 401 Unauthorized

**Solution:**
1. Ensure headers are exactly:
   - `Client-Service: smartschool` (case-sensitive)
   - `Auth-Key: schoolAdmin@` (case-sensitive)
2. Check for extra spaces in header values
3. Verify headers are being sent in request

### Route Not Found
**Issue:** Getting 404 Not Found

**Solution:**
1. Verify routes are added to `api/application/config/routes.php`
2. Check URL spelling and format
3. Ensure `.htaccess` is configured correctly
4. Clear any route caching if applicable

---

## Quick Reference URLs

| API | URL |
|-----|-----|
| Income Head List | `http://localhost/amt/api/income-head-list/list` |
| Expense Head List | `http://localhost/amt/api/expense-head-list/list` |
| Roles List | `http://localhost/amt/api/roles-list/list` |

---

## Next Steps After Testing

1. ✅ Verify all three APIs return data successfully
2. ✅ Check response format matches documentation
3. ✅ Test error scenarios (wrong headers, wrong method)
4. ✅ Integrate with frontend application
5. ✅ Add to API documentation portal
6. ✅ Share endpoints with development team

---

**Happy Testing! 🚀**

