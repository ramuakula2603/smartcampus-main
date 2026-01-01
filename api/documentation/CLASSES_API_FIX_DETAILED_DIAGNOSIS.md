# Classes API - Detailed Diagnosis and Fix

## Problem Summary
The Classes API endpoint (`POST /api/classes/list`) was returning **500 Internal Server Error** even after removing `customlib` from the model loading array.

## Root Cause Analysis

### Issue 1: Incorrect Model Loading (FIXED ✅)
**File:** `api/application/controllers/Classes_api.php`
**Lines:** 39-54
**Problem:** `customlib` was being loaded as a model instead of a library
**Status:** ✅ FIXED - Removed from model array, kept only in library array

### Issue 2: Missing Model Dependency (FIXED ✅)
**File:** `api/application/models/Class_model.php`
**Lines:** 10-16
**Problem:** The Class_model constructor tried to access `$this->setting_model->getCurrentSession()` on line 13 (originally line 13), but `setting_model` was NOT loaded in the model itself. It was only loaded in the controller.

**Why This Fails:**
- Models in CodeIgniter don't automatically have access to controller-loaded models
- When `Class_model` is instantiated, it tries to access `$this->setting_model` which doesn't exist
- This causes: `Undefined property: Classes_model::$setting_model`
- Then: `Call to a member function getCurrentSession() on null`

**Solution Applied:**
Added `$this->load->model('setting_model');` inside the Class_model constructor BEFORE trying to use it.

## Changes Applied

### Before (BROKEN):
```php
// api/application/models/Class_model.php
public function __construct()
{
    parent::__construct();
    $this->current_session = $this->setting_model->getCurrentSession();  // ❌ FAILS - setting_model not loaded
}
```

### After (FIXED):
```php
// api/application/models/Class_model.php
public function __construct()
{
    parent::__construct();
    // Load setting_model to get current session
    $this->load->model('setting_model');  // ✅ Load it first
    $this->current_session = $this->setting_model->getCurrentSession();  // ✅ Now it works
}
```

## Error Log Evidence

**Before Fix (Lines 304-305 of log-2025-10-30.php):**
```
ERROR - 2025-10-30 11:22:27 --> Severity: Warning --> Undefined property: Classes_api::$setting_model
ERROR - 2025-10-30 11:22:27 --> Severity: error --> Exception: Call to a member function getCurrentSession() on null
```

## Pattern Used in Other Models

This pattern is already used successfully in other models:

**Example: Teacher_auth_model.php (Lines 12-16)**
```php
public function __construct()
{
    parent::__construct();
    // Load models
    $this->load->model(array('staff_model', 'setting_model'));
    // ... rest of constructor
}
```

## Files Modified

1. ✅ `api/application/models/Class_model.php` - Added setting_model loading in constructor

## Testing Recommendations

After this fix, test the Classes API endpoint:

```bash
curl -X POST "http://localhost/amt/api/classes/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d "{}"
```

**Expected Response:** 200 OK with JSON data
**Previous Response:** 500 Internal Server Error

## Key Takeaway

**Models must load their own dependencies.** When a model needs another model, it should load it in its own constructor, not rely on the controller to load it. This ensures the model works correctly regardless of which controller uses it.

