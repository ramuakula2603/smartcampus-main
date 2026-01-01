# Subjects API - Troubleshooting Guide

## Issue Fixed: 403 Forbidden Error

### Problem
When testing the Subjects API endpoints, you were receiving a **403 Forbidden** error with the message:
```
"You don't have permission to access this resource."
```

### Root Cause
The Subjects API controller was trying to load `customlib` as a **model** when it's actually a **library**. This caused a CodeIgniter error that resulted in the 403 response.

**Error in logs:**
```
ERROR - Error loading models: The model name you are loading is the name of a resource that is already being used: customlib
```

### Solution Applied
Fixed the constructor in `api/application/controllers/Subjects_api.php`:

**Before (INCORRECT):**
```php
$this->load->model(array(
    'subject_model',
    'setting_model',
    'customlib'  // ❌ WRONG - customlib is a library, not a model
));

$this->load->library(array('customlib'));
```

**After (CORRECT):**
```php
$this->load->model(array(
    'subject_model',
    'setting_model'  // ✅ CORRECT - only models
));

$this->load->library(array('customlib'));  // ✅ customlib loaded as library
```

---

## Testing the Fixed API

### Using Postman

1. **Set up the request:**
   - URL: `http://localhost/amt/api/subjects/list`
   - Method: **POST**
   - Headers:
     ```
     Content-Type: application/json
     Client-Service: smartschool
     Auth-Key: schoolAdmin@
     ```
   - Body: `{}`

2. **Expected Response (HTTP 200):**
   ```json
   {
     "status": 1,
     "message": "Subjects retrieved successfully",
     "total_records": 5,
     "data": [
       {
         "id": 1,
         "name": "Mathematics",
         "code": "MATH",
         "is_active": "yes",
         "created_at": "2024-01-15 10:30:00",
         "updated_at": "2024-01-15 10:30:00"
       }
     ]
   }
   ```

### Using cURL

```bash
curl -X POST "http://localhost/amt/api/subjects/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Using PHP

```php
<?php
$ch = curl_init('http://localhost/amt/api/subjects/list');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
));
curl_setopt($ch, CURLOPT_POSTFIELDS, '{}');

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

echo "HTTP Status: " . $http_code . "\n";
echo "Response: " . $response . "\n";
?>
```

---

## Common Issues and Solutions

### Issue 1: Still Getting 403 Forbidden

**Possible Causes:**
1. Browser cache not cleared
2. Apache not restarted
3. File not saved properly

**Solutions:**
1. Clear browser cache (Ctrl+Shift+Delete)
2. Restart Apache: `net stop Apache2.4` then `net start Apache2.4`
3. Verify file was saved: Check `api/application/controllers/Subjects_api.php` line 39-54

### Issue 2: 401 Unauthorized

**Cause:** Missing or incorrect authentication headers

**Solution:** Ensure headers are exactly:
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

### Issue 3: 405 Method Not Allowed

**Cause:** Using GET instead of POST

**Solution:** All endpoints require POST method, not GET

### Issue 4: 400 Bad Request

**Cause:** Invalid JSON or missing required fields

**Solution:** 
- Ensure JSON is valid
- For create/update, include "name" field
- Use proper JSON format

### Issue 5: 404 Not Found

**Cause:** Route not configured or controller not found

**Solution:**
1. Verify routes in `api/application/config/routes.php` (lines 190-195)
2. Verify controller file exists: `api/application/controllers/Subjects_api.php`
3. Verify controller class name is `Subjects_api`

---

## Verification Checklist

- [x] Fixed `customlib` loading issue in Subjects_api.php
- [x] Verified routes are configured in routes.php
- [x] Verified controller file exists
- [x] Verified subject_model is loaded correctly
- [x] Verified authentication headers are validated
- [x] Verified response format is consistent

---

## Files Modified

1. **api/application/controllers/Subjects_api.php**
   - Removed `customlib` from model loading
   - Kept `customlib` in library loading
   - Lines 39-54 updated

---

## Next Steps

1. **Clear browser cache** and refresh
2. **Restart Apache** if needed
3. **Test all endpoints** using Postman or cURL
4. **Verify database** has subjects table with data
5. **Check logs** at `api/application/logs/log-2025-10-29.php` for any errors

---

## Support

If you continue to experience issues:

1. Check the API logs: `api/application/logs/log-2025-10-29.php`
2. Verify the Sections API works: `http://localhost/amt/api/sections/list`
3. Compare Subjects_api.php with Sections_api.php
4. Ensure all required models are loaded correctly

---

## Status

✅ **ISSUE FIXED**

The 403 Forbidden error has been resolved. The Subjects API should now work correctly with all CRUD endpoints.

