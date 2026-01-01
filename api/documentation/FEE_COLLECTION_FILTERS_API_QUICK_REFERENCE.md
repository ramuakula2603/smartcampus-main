# Fee Collection Filters API - Quick Reference

## 🎯 Overview

The Fee Collection Filters API provides filter options for fee collection reports with hierarchical filtering support.

---

## 📍 Endpoint

**URL:** `POST /api/fee-collection-filters/get`

**Full URL:** `http://localhost/amt/api/fee-collection-filters/get`

---

## 🔑 Authentication Headers

```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

---

## 📥 Request Examples

### 1. Get All Filter Options (Empty Request)
```json
{}
```

### 2. Filter Classes by Session
```json
{
  "session_id": 21
}
```

### 3. Filter Sections by Class
```json
{
  "session_id": 21,
  "class_id": 19
}
```

---

## 📤 Response Structure

```json
{
  "status": 1,
  "message": "Filter options retrieved successfully",
  "data": {
    "sessions": [
      {"id": 21, "name": "2024-2025"}
    ],
    "classes": [
      {"id": 19, "name": "Class 1"}
    ],
    "sections": [
      {"id": 1, "name": "Section A"}
    ],
    "fee_groups": [
      {"id": 1, "name": "Tuition Fees"}
    ],
    "fee_types": [
      {"id": 1, "name": "Monthly Fee", "code": "MF001"}
    ],
    "collect_by": [
      {"id": 1, "name": "John Doe", "employee_id": "EMP001"}
    ],
    "group_by_options": ["class", "collect", "mode"]
  }
}
```

---

## 🔄 Hierarchical Filtering Logic

| Filter | Behavior |
|--------|----------|
| **Sessions** | Always returns all sessions |
| **Classes** | Without `session_id`: All classes<br>With `session_id`: Classes for that session only |
| **Sections** | Without `class_id`: All sections<br>With `class_id`: Sections for that class only |
| **Fee Groups** | Always returns all non-system fee groups |
| **Fee Types** | Always returns all non-system fee types |
| **Collect By** | Always returns all active staff members |
| **Group By Options** | Always returns: ["class", "collect", "mode"] |

---

## 🧪 Testing

### cURL Test - Get All Options
```bash
curl -X POST "http://localhost/amt/api/fee-collection-filters/get" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### cURL Test - Filter by Session
```bash
curl -X POST "http://localhost/amt/api/fee-collection-filters/get" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"session_id": 21}'
```

### Test Script
Run the test script in your browser:
```
http://localhost/amt/test_fee_collection_filters_api.php
```

---

## 📁 Files Created

| File | Location | Purpose |
|------|----------|---------|
| Controller | `api/application/controllers/Fee_collection_filters_api.php` | Handles API requests |
| Model | `api/application/models/Fee_collection_filters_model.php` | Database queries |
| Documentation | `api/documentation/FEE_COLLECTION_FILTERS_API_DOCUMENTATION.md` | Full API docs |
| Quick Reference | `api/documentation/FEE_COLLECTION_FILTERS_API_QUICK_REFERENCE.md` | This file |
| Test Script | `test_fee_collection_filters_api.php` | API testing |

---

## ✅ Key Features

1. ✅ **POST Method Only** - Follows existing API patterns
2. ✅ **Smart Headers** - Uses `Client-Service: smartschool` and `Auth-Key: schoolAdmin@`
3. ✅ **Graceful Empty Handling** - Empty request `{}` returns all options
4. ✅ **Hierarchical Filtering** - Session → Class → Section relationships
5. ✅ **Consistent Structure** - Follows Disable Reason API and Fee Master API patterns
6. ✅ **Comprehensive Error Handling** - 401, 405, 500 error responses
7. ✅ **Well Documented** - Inline comments and external documentation

---

## 🗄️ Database Tables

- `sessions` - Academic sessions
- `classes` - Class information
- `sections` - Section information
- `fee_groups` - Fee group definitions
- `feetype` - Fee type definitions
- `staff` - Staff member information
- `student_session` - Session-class relationships
- `class_sections` - Class-section relationships

---

## 🎨 Use Cases

1. **Initial Page Load** - Call with `{}` to populate all dropdowns
2. **Session Selection** - Call with `session_id` to update class dropdown
3. **Class Selection** - Call with `session_id` and `class_id` to update section dropdown
4. **Report Generation** - Use returned filter options to build fee collection reports

---

## ⚠️ Important Notes

- All parameters are **optional**
- Empty request body `{}` is **valid and recommended** for initial load
- Fee types are **always returned in full** (not filtered by session)
- Staff collectors include **only active staff**
- System fee groups and types are **excluded**
- Classes filtered by session use `student_session` table
- Sections filtered by class use `class_sections` table

---

## 🔗 Related APIs

- **Fee Master API** - `api/documentation/FEE_MASTER_API_DOCUMENTATION.md`
- **Disable Reason API** - `api/documentation/DISABLE_REASON_API_DOCUMENTATION.md`
- **Session Fee Structure API** - `api/documentation/SESSION_FEE_STRUCTURE_API_README.md`

---

## 📞 Support

For issues or questions, contact the development team.

**API Status:** ✅ Fully Implemented

**Last Updated:** October 10, 2025

