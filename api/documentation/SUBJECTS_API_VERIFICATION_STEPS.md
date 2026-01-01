# Subjects API - Verification Steps

## Issue Fixed

The Subjects API was returning **403 Forbidden** error. The root cause was that `Subject_model` was extending `CI_Model` instead of `MY_Model`.

**Fix Applied:** Changed `api/application/models/Subject_model.php` line 6 to extend `MY_Model`.

---

## Step-by-Step Verification

### Step 1: Verify the Fix Was Applied

**File:** `api/application/models/Subject_model.php`

**Check line 6:**
```php
class Subject_model extends MY_Model {
```

Should show `MY_Model`, NOT `CI_Model`.

✓ **Expected:** `extends MY_Model`
✗ **Wrong:** `extends CI_Model`

---

### Step 2: Clear Browser Cache

1. Open your browser
2. Press `Ctrl + Shift + Delete` (or `Cmd + Shift + Delete` on Mac)
3. Select "All time" or "Everything"
4. Check "Cookies and other site data" and "Cached images and files"
5. Click "Clear data"

---

### Step 3: Test the API in Postman

#### Test 1: List All Subjects

**Request:**
- **URL:** `http://localhost/amt/api/subjects/list`
- **Method:** `POST`
- **Headers:**
  ```
  Content-Type: application/json
  Client-Service: smartschool
  Auth-Key: schoolAdmin@
  ```
- **Body:**
  ```json
  {}
  ```

**Expected Response (HTTP 200):**
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

---

#### Test 2: Get Single Subject

**Request:**
- **URL:** `http://localhost/amt/api/subjects/get/1`
- **Method:** `POST`
- **Headers:** (same as above)
- **Body:** `{}`

**Expected Response (HTTP 200):**
```json
{
  "status": 1,
  "message": "Subject retrieved successfully",
  "data": {
    "id": 1,
    "name": "Mathematics",
    "code": "MATH",
    "is_active": "yes",
    "created_at": "2024-01-15 10:30:00",
    "updated_at": "2024-01-15 10:30:00"
  }
}
```

---

#### Test 3: Create New Subject

**Request:**
- **URL:** `http://localhost/amt/api/subjects/create`
- **Method:** `POST`
- **Headers:** (same as above)
- **Body:**
  ```json
  {
    "name": "Physics",
    "code": "PHY",
    "is_active": "yes"
  }
  ```

**Expected Response (HTTP 201):**
```json
{
  "status": 1,
  "message": "Subject created successfully",
  "data": {
    "id": 6,
    "name": "Physics",
    "code": "PHY",
    "is_active": "yes"
  }
}
```

---

#### Test 4: Update Subject

**Request:**
- **URL:** `http://localhost/amt/api/subjects/update/1`
- **Method:** `POST`
- **Headers:** (same as above)
- **Body:**
  ```json
  {
    "name": "Advanced Mathematics",
    "code": "MATH-ADV"
  }
  ```

**Expected Response (HTTP 200):**
```json
{
  "status": 1,
  "message": "Subject updated successfully",
  "data": {
    "id": 1,
    "name": "Advanced Mathematics",
    "code": "MATH-ADV",
    "is_active": "yes"
  }
}
```

---

#### Test 5: Delete Subject

**Request:**
- **URL:** `http://localhost/amt/api/subjects/delete/6`
- **Method:** `POST`
- **Headers:** (same as above)
- **Body:** `{}`

**Expected Response (HTTP 200):**
```json
{
  "status": 1,
  "message": "Subject deleted successfully",
  "data": null
}
```

---

## Troubleshooting

### Still Getting 403 Forbidden?

1. **Verify the fix was applied:**
   - Open `api/application/models/Subject_model.php`
   - Check line 6 shows `extends MY_Model`

2. **Clear browser cache:**
   - Ctrl + Shift + Delete
   - Clear all cache

3. **Restart Apache:**
   - Open XAMPP Control Panel
   - Stop Apache
   - Wait 2 seconds
   - Start Apache

4. **Check file permissions:**
   - Right-click `api/application/models/Subject_model.php`
   - Properties → Security
   - Ensure "Read" and "Read & Execute" are allowed

### Getting 401 Unauthorized?

- Verify headers are exactly:
  ```
  Content-Type: application/json
  Client-Service: smartschool
  Auth-Key: schoolAdmin@
  ```

### Getting 405 Method Not Allowed?

- Ensure you're using `POST` method, not `GET`

### Getting 404 Not Found?

- Verify the URL is correct: `http://localhost/amt/api/subjects/list`
- Check routes in `api/application/config/routes.php` (lines 190-195)

---

## Success Indicators

✓ All endpoints return HTTP 200 or 201 (not 403)
✓ Response includes `"status": 1` (success)
✓ Response includes appropriate `"message"`
✓ Response includes `"data"` with subject information
✓ All CRUD operations work correctly

---

## Next Steps

Once verified:
1. Test the Subjects page at `http://localhost/amt/admin/subject`
2. Verify the page can load and display subjects
3. Test creating, editing, and deleting subjects from the UI
4. Verify the API is being called correctly from the frontend

---

## Support

If you continue to experience issues:
1. Check the API logs: `api/application/logs/log-2025-10-29.php`
2. Check Apache error log: `C:\xampp\apache\logs\error.log`
3. Verify database connection is working
4. Ensure MySQL is running in XAMPP

