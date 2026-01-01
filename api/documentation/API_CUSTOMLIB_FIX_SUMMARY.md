# API Controllers - Customlib Loading Fix

## Issue Summary
Multiple API controllers were incorrectly loading `customlib` as a **model** instead of a **library**, causing 500 Internal Server Errors when the API endpoints were called.

### Root Cause
- `customlib` is a **library** (located in `api/application/libraries/`), NOT a model
- When loaded as a model via `$this->load->model('customlib')`, CodeIgniter fails to load it properly
- This caused dependent models (like `setting_model`) to fail initialization
- When models tried to use `$this->setting_model`, it was null, resulting in fatal errors

### Error Example
```
ERROR - Undefined property: Classes_api::$setting_model
ERROR - Call to a member function getCurrentSession() on null
```

## Files Fixed

### 1. **api/application/controllers/Classes_api.php**
- **Lines:** 39-54
- **Change:** Removed `'customlib'` from model loading array
- **Status:** ✅ FIXED

### 2. **api/application/controllers/Subjects_api.php**
- **Lines:** 39-54
- **Change:** Removed `'customlib'` from model loading array
- **Status:** ✅ FIXED

### 3. **api/application/controllers/Department_api.php**
- **Lines:** 39-54
- **Change:** Removed `'customlib'` from model loading array
- **Status:** ✅ FIXED

### 4. **api/application/controllers/Designation_api.php**
- **Lines:** 39-54
- **Change:** Removed `'customlib'` from model loading array
- **Status:** ✅ FIXED

### 5. **api/application/controllers/Income_api.php**
- **Lines:** 39-55
- **Change:** Removed `'customlib'` from model loading array
- **Status:** ✅ FIXED

### 6. **api/application/controllers/Income_search_api.php**
- **Lines:** 39-55
- **Change:** Removed `'customlib'` from model loading array
- **Status:** ✅ FIXED

### 7. **api/application/controllers/Income_head_api.php**
- **Lines:** 39-54
- **Change:** Removed `'customlib'` from model loading array
- **Status:** ✅ FIXED

### 8. **api/application/controllers/Online_admission_api.php**
- **Lines:** 40-58
- **Change:** Removed `'customlib'` from model loading array
- **Status:** ✅ FIXED

## Correct Pattern (After Fix)

All affected controllers now follow this correct pattern:

```php
// Load required models
try {
    $this->load->model(array(
        'model_name_1',
        'model_name_2',
        'setting_model'
        // NO customlib here!
    ));
} catch (Exception $e) {
    log_message('error', 'Error loading models: ' . $e->getMessage());
}

// Load libraries
try {
    $this->load->library(array('customlib'));  // customlib loaded as library
} catch (Exception $e) {
    log_message('error', 'Error loading libraries: ' . $e->getMessage());
}
```

## Testing Recommendations

After applying these fixes, test all affected API endpoints:

1. **Classes API:** `POST /api/classes/list`
2. **Subjects API:** `POST /api/subjects/list`
3. **Department API:** `POST /api/department/list`
4. **Designation API:** `POST /api/designation/list`
5. **Income API:** `POST /api/income/list`
6. **Income Head API:** `POST /api/incomehead/list`
7. **Online Admission API:** `POST /api/online-admission/list`

All endpoints should now return **200 OK** with proper JSON responses instead of **500 Internal Server Error**.

## Key Takeaway

**Always load `customlib` as a library, never as a model:**
- ✅ Correct: `$this->load->library(array('customlib'));`
- ❌ Wrong: `$this->load->model(array('customlib'));`

