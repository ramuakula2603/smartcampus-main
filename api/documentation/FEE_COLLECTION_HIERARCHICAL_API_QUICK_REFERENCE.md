# Fee Collection Hierarchical API - Quick Reference

## 🚀 Quick Start

### Base URL
```
http://localhost/amt/api/
```

### Required Headers
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

---

## 📋 Endpoints Summary

| Endpoint | Method | Purpose | Includes Students |
|----------|--------|---------|-------------------|
| `/fee-collection-filters/get` | POST | Hierarchical filters for fee collection | ❌ No |
| `/fee-collection-filters/get-hierarchy` | POST | Complete academic hierarchy | ✅ Yes |

---

## 🔗 Endpoint 1: Get Hierarchical Filters

**URL:** `POST /fee-collection-filters/get`

### Request
```json
{
  "session_id": 21,      // Optional
  "class_id": 19,        // Optional
  "section_id": 1        // Optional
}
```

### Response Structure
```
sessions (array)
├── id, name
└── classes (array)
    ├── id, name
    └── sections (array)
        └── id, name

+ fee_groups (array)
+ fee_types (array)
+ collect_by (array)
+ group_by_options (array)
```

### Example Response
```json
{
  "status": 1,
  "message": "Filter options retrieved successfully",
  "data": {
    "sessions": [
      {
        "id": 21,
        "name": "2024-2025",
        "classes": [
          {
            "id": 19,
            "name": "Class 1",
            "sections": [
              {"id": 1, "name": "Section A"},
              {"id": 2, "name": "Section B"}
            ]
          }
        ]
      }
    ],
    "fee_groups": [...],
    "fee_types": [...],
    "collect_by": [...],
    "group_by_options": ["class", "collect", "mode"]
  }
}
```

---

## 🔗 Endpoint 2: Get Hierarchical Data with Students

**URL:** `POST /fee-collection-filters/get-hierarchy`

### Request
```json
{
  "session_id": 21,      // Optional
  "class_id": 19,        // Optional
  "section_id": 1        // Optional
}
```

### Response Structure
```
data (array)
└── sessions
    ├── id, name
    └── classes (array)
        ├── id, name
        └── sections (array)
            ├── id, name
            └── students (array)
                ├── id, admission_no, roll_no
                ├── full_name, firstname, middlename, lastname
                ├── father_name, dob, gender
                ├── mobileno, email
                ├── guardian_name, guardian_phone
                └── is_active

+ filters_applied (object)
+ statistics (object)
+ timestamp (string)
```

### Example Response
```json
{
  "status": 1,
  "message": "Hierarchical academic data retrieved successfully",
  "filters_applied": {
    "session_id": 21,
    "class_id": null,
    "section_id": null
  },
  "statistics": {
    "total_sessions": 1,
    "total_classes": 3,
    "total_sections": 8,
    "total_students": 150
  },
  "data": [
    {
      "id": 21,
      "name": "2024-2025",
      "classes": [
        {
          "id": 19,
          "name": "Class 1",
          "sections": [
            {
              "id": 1,
              "name": "Section A",
              "students": [
                {
                  "id": 101,
                  "admission_no": "STU001",
                  "roll_no": "1",
                  "full_name": "John Michael Doe",
                  "firstname": "John",
                  "middlename": "Michael",
                  "lastname": "Doe",
                  "father_name": "Robert Doe",
                  "dob": "2015-05-15",
                  "gender": "Male",
                  "mobileno": "1234567890",
                  "email": "john.doe@example.com",
                  "guardian_name": "Robert Doe",
                  "guardian_phone": "1234567890",
                  "is_active": "yes"
                }
              ]
            }
          ]
        }
      ]
    }
  ],
  "timestamp": "2025-10-10 14:30:00"
}
```

---

## 🎯 Common Use Cases

### 1. Get All Data (No Filters)
```bash
curl -X POST "http://localhost/amt/api/fee-collection-filters/get" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### 2. Get Data for Specific Session
```bash
curl -X POST "http://localhost/amt/api/fee-collection-filters/get-hierarchy" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"session_id": 21}'
```

### 3. Get Students for Specific Class
```bash
curl -X POST "http://localhost/amt/api/fee-collection-filters/get-hierarchy" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"session_id": 21, "class_id": 19}'
```

### 4. Get Students for Specific Section
```bash
curl -X POST "http://localhost/amt/api/fee-collection-filters/get-hierarchy" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"session_id": 21, "class_id": 19, "section_id": 1}'
```

---

## 📊 Response Comparison

| Feature | `/get` | `/get-hierarchy` |
|---------|--------|------------------|
| Sessions | ✅ Hierarchical | ✅ Hierarchical |
| Classes | ✅ Nested | ✅ Nested |
| Sections | ✅ Nested | ✅ Nested |
| Students | ❌ Not included | ✅ Nested with full details |
| Fee Groups | ✅ Included | ❌ Not included |
| Fee Types | ✅ Included | ❌ Not included |
| Staff Collectors | ✅ Included | ❌ Not included |
| Statistics | ❌ Not included | ✅ Included |
| Timestamp | ❌ Not included | ✅ Included |

---

## ⚡ Key Features

### Both Endpoints
- ✅ POST method only
- ✅ Requires authentication headers
- ✅ Handles empty request body `{}`
- ✅ Returns hierarchical structure
- ✅ Supports optional filtering
- ✅ Only active students included

### Endpoint 1 (`/get`)
- 🎯 Optimized for fee collection filters
- 📋 Includes fee-related data
- ⚡ Faster (no student data)

### Endpoint 2 (`/get-hierarchy`)
- 🎯 Complete academic hierarchy
- 👥 Includes all student details
- 📊 Provides statistics
- 🕐 Includes timestamp

---

## 🔍 Filtering Logic

| Filter | Behavior |
|--------|----------|
| No filters (`{}`) | Returns all data |
| `session_id` only | Returns data for that session |
| `session_id` + `class_id` | Returns data for that class in session |
| All three filters | Returns data for specific section |

---

## ⚠️ Important Notes

1. **Breaking Change:** The `/get` endpoint now returns hierarchical structure (not flat arrays)
2. **Empty Body:** `{}` is valid and returns all data
3. **Active Students:** Only students with `is_active = 'yes'` are included
4. **System Records:** System fee groups and types are excluded
5. **Performance:** Use filters to reduce response size for large datasets

---

## 🐛 Error Responses

### 401 Unauthorized
```json
{
  "status": 0,
  "message": "Unauthorized access. Invalid headers.",
  "data": null
}
```

### 405 Method Not Allowed
```json
{
  "status": 0,
  "message": "Method not allowed. Use POST method.",
  "data": null
}
```

### 500 Internal Server Error
```json
{
  "status": 0,
  "message": "Internal server error occurred",
  "data": null
}
```

---

## 📚 Related Documentation

- Full Documentation: `FEE_COLLECTION_HIERARCHICAL_API_DOCUMENTATION.md`
- Original API: `FEE_COLLECTION_FILTERS_API_DOCUMENTATION.md`

---

**Last Updated:** October 10, 2025  
**Version:** 2.0 (Hierarchical Structure)

