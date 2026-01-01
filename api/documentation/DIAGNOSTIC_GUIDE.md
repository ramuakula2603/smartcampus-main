# Subjects API - Diagnostic Guide

## Current Status

✅ **Code Changes Verified:**
- Subject_model.php extends MY_Model (Line 6)
- Subjects_api.php loads customlib in model array (Lines 41-45)
- Routes configured correctly (Lines 190-195 in routes.php)
- Controller file exists and has correct class name

❌ **Issue:** 403 Forbidden error persists when testing in Postman

## What We Need From You

To diagnose this issue, please provide the following information:

### 1. Exact Postman Response

When you test the Subjects API endpoint in Postman, please provide:

**Request Details:**
- URL: `http://localhost/amt/api/subjects/list`
- Method: `POST`
- Headers:
  - `Content-Type: application/json`
  - `Client-Service: smartschool`
  - `Auth-Key: schoolAdmin@`
- Body: `{}`

**Response Details (from Postman):**
- [ ] HTTP Status Code (e.g., 403)
- [ ] Response Headers (click "Headers" tab in Postman response)
- [ ] Response Body (the actual error message)
- [ ] Screenshot of the full response

### 2. Comparison Test

Test the working Sections API and provide the same information:

**Request:**
- URL: `http://localhost/amt/api/sections/list`
- Method: `POST`
- Headers: Same as above
- Body: `{}`

**Response:**
- [ ] HTTP Status Code
- [ ] Response Headers
- [ ] Response Body
- [ ] Screenshot

### 3. Apache Error Log

Check the Apache error log for any errors related to the Subjects API:

**File Location:** `C:\xampp\apache\logs\error.log`

**Steps:**
1. Open the file in a text editor
2. Search for "subjects" or "403"
3. Look for any errors from the last few minutes
4. Copy and paste the relevant error messages

### 4. CodeIgniter Log

Check the CodeIgniter log for any errors:

**File Location:** `api/application/logs/log-2025-10-29.php`

**Steps:**
1. Open the file in a text editor
2. Look at the most recent entries
3. Search for "Subjects" or "ERROR"
4. Copy and paste any relevant error messages

### 5. Browser Developer Tools

Test the API directly from your browser:

**Steps:**
1. Open your browser
2. Press F12 to open Developer Tools
3. Go to the "Network" tab
4. Make a request to `http://localhost/amt/api/subjects/list` using Postman
5. In the Network tab, find the request to "subjects/list"
6. Click on it and provide:
   - [ ] Request Headers
   - [ ] Response Headers
   - [ ] Response Body
   - [ ] Screenshot

### 6. Test with cURL

Run this command in PowerShell and provide the output:

```powershell
$headers = @{
    'Content-Type' = 'application/json'
    'Client-Service' = 'smartschool'
    'Auth-Key' = 'schoolAdmin@'
}

$body = @{} | ConvertTo-Json

Invoke-WebRequest -Uri "http://localhost/amt/api/subjects/list" `
    -Method POST `
    -Headers $headers `
    -Body $body `
    -Verbose
```

### 7. Verify Apache is Running

Run this command and confirm Apache is running:

```powershell
Get-Service Apache2.4 | Select-Object Status
```

Expected output: `Status : Running`

### 8. Clear Caches

Before testing again, please:

1. **Clear Browser Cache:**
   - Press Ctrl+Shift+Delete
   - Select "All time"
   - Click "Clear data"

2. **Restart Apache:**
   - Open XAMPP Control Panel
   - Click "Stop" for Apache
   - Wait 5 seconds
   - Click "Start" for Apache
   - Wait for it to fully start

3. **Test again in Postman**

## What This Information Will Help Us Determine

- **Is the request reaching the controller?** (Check response body)
- **Is Apache blocking the request?** (Check Apache error log)
- **Is there a PHP error?** (Check CodeIgniter log)
- **Is the route being matched?** (Check if 404 override is triggered)
- **Is there a caching issue?** (Restart Apache and clear browser cache)

## Next Steps

Once you provide this information, we can:
1. Identify the exact source of the 403 error
2. Determine if it's an Apache, PHP, or CodeIgniter issue
3. Apply the appropriate fix
4. Verify the fix works

---

**Please provide as much of this information as possible. The more details you provide, the faster we can resolve this issue.**

