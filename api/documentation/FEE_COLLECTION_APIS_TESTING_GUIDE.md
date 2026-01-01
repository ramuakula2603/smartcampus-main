# Fee Collection Hierarchical APIs - Testing Guide

## 🧪 Quick Testing Guide

This guide helps you test both fee collection hierarchical APIs.

---

## 🔧 Prerequisites

1. **Server Running:** Ensure your local server is running at `http://localhost/amt`
2. **Database:** Ensure database has sessions, classes, sections, and students data
3. **Headers:** Always include required authentication headers

---

## 📋 API Endpoints

### API 1: Fee Collection Filters (Hierarchical)
- **URL:** `POST http://localhost/amt/api/fee-collection-filters/get`
- **Returns:** Sessions → Classes → Sections (+ fee filters)
- **No Student Data**

### API 2: Complete Hierarchy with Students
- **URL:** `POST http://localhost/amt/api/fee-collection-filters/get-hierarchy`
- **Returns:** Sessions → Classes → Sections → Students
- **Includes Student Data**

---

## 🧪 Test Cases

### Test 1: API 1 - Get All Filters (Empty Body)

**Request:**
```bash
curl -X POST "http://localhost/amt/api/fee-collection-filters/get" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Expected Response:**
- HTTP 200 OK
- `status: 1`
- `data.sessions` array with nested classes and sections
- `data.fee_groups` array present
- `data.fee_types` array present
- `data.collect_by` array present
- `data.group_by_options` array present
- No student data

**Success Criteria:**
✅ Response received without errors  
✅ Hierarchical structure (sessions contain classes, classes contain sections)  
✅ Fee-related data included  
✅ No SQL errors  

---

### Test 2: API 1 - Filter by Session

**Request:**
```bash
curl -X POST "http://localhost/amt/api/fee-collection-filters/get" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "session_id": 25
  }'
```

**Expected Response:**
- HTTP 200 OK
- `status: 1`
- Only session with ID 25 in response
- Classes and sections for that session only

**Success Criteria:**
✅ Only specified session returned  
✅ Classes filtered to that session  
✅ Sections filtered to those classes  

---

### Test 3: API 1 - Filter by Session and Class

**Request:**
```bash
curl -X POST "http://localhost/amt/api/fee-collection-filters/get" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "session_id": 25,
    "class_id": 19
  }'
```

**Expected Response:**
- HTTP 200 OK
- Only specified session and class
- Sections for that class only

**Success Criteria:**
✅ Correct session and class returned  
✅ Sections filtered correctly  

---

### Test 4: API 2 - Get All Data with Students (Empty Body)

**Request:**
```bash
curl -X POST "http://localhost/amt/api/fee-collection-filters/get-hierarchy" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Expected Response:**
- HTTP 200 OK
- `status: 1`
- `filters_applied` object present
- `statistics` object with counts
- `data` array with hierarchical structure
- Each section contains `students` array
- `timestamp` present

**Success Criteria:**
✅ Response received without errors  
✅ Complete hierarchy with students  
✅ Statistics calculated correctly  
✅ Students have all required fields  
✅ No SQL errors  

---

### Test 5: API 2 - Get Students for Specific Session

**Request:**
```bash
curl -X POST "http://localhost/amt/api/fee-collection-filters/get-hierarchy" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "session_id": 25
  }'
```

**Expected Response:**
- HTTP 200 OK
- Only specified session
- All classes, sections, and students for that session
- Statistics match the filtered data

**Success Criteria:**
✅ Only specified session returned  
✅ All students for that session included  
✅ Statistics accurate  

---

### Test 6: API 2 - Get Students for Specific Section

**Request:**
```bash
curl -X POST "http://localhost/amt/api/fee-collection-filters/get-hierarchy" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "session_id": 25,
    "class_id": 19,
    "section_id": 1
  }'
```

**Expected Response:**
- HTTP 200 OK
- Only specified session, class, and section
- Students for that specific section only
- Statistics show 1 session, 1 class, 1 section, X students

**Success Criteria:**
✅ Correct filtering applied  
✅ Only students from specified section  
✅ Statistics accurate  

---

### Test 7: Invalid Headers (Both APIs)

**Request:**
```bash
curl -X POST "http://localhost/amt/api/fee-collection-filters/get" \
  -H "Content-Type: application/json" \
  -d '{}'
```

**Expected Response:**
- HTTP 401 Unauthorized
- `status: 0`
- `message: "Unauthorized access. Invalid headers."`

**Success Criteria:**
✅ Returns 401 error  
✅ Proper error message  

---

### Test 8: Wrong Method (Both APIs)

**Request:**
```bash
curl -X GET "http://localhost/amt/api/fee-collection-filters/get"
```

**Expected Response:**
- HTTP 405 Method Not Allowed
- `status: 0`
- `message: "Method not allowed. Use POST method."`

**Success Criteria:**
✅ Returns 405 error  
✅ Proper error message  

---

## 🔍 Verification Checklist

### For API 1 (`/get`)
- [ ] Returns hierarchical structure (nested)
- [ ] Sessions contain classes array
- [ ] Classes contain sections array
- [ ] Fee groups included
- [ ] Fee types included
- [ ] Collect by (staff) included
- [ ] Group by options included
- [ ] No student data present
- [ ] Filtering works correctly
- [ ] Empty body returns all data

### For API 2 (`/get-hierarchy`)
- [ ] Returns complete hierarchical structure
- [ ] Sessions contain classes array
- [ ] Classes contain sections array
- [ ] Sections contain students array
- [ ] Students have all required fields
- [ ] Statistics calculated correctly
- [ ] Filters applied shown correctly
- [ ] Timestamp present
- [ ] Only active students included
- [ ] Students ordered by admission_no
- [ ] Filtering works correctly
- [ ] Empty body returns all data

---

## 🐛 Common Issues and Solutions

### Issue 1: SQL Syntax Error (DISTINCT)
**Error:** `You have an error in your SQL syntax... near 'DISTINCT'`

**Solution:** ✅ Fixed - Now using `$this->db->distinct()` method instead of `SELECT DISTINCT`

### Issue 2: Empty Response
**Possible Causes:**
- No data in database
- Incorrect filter values
- Database connection issue

**Solution:** Check database has sessions, classes, sections, and students data

### Issue 3: 401 Unauthorized
**Cause:** Missing or incorrect headers

**Solution:** Ensure both headers are present:
```
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

### Issue 4: 405 Method Not Allowed
**Cause:** Using GET instead of POST

**Solution:** Always use POST method

---

## 📊 Expected Data Structure

### API 1 Response Structure
```
{
  status: 1,
  message: "...",
  data: {
    sessions: [
      {
        id: int,
        name: string,
        classes: [
          {
            id: int,
            name: string,
            sections: [
              {id: int, name: string}
            ]
          }
        ]
      }
    ],
    fee_groups: [...],
    fee_types: [...],
    collect_by: [...],
    group_by_options: [...]
  }
}
```

### API 2 Response Structure
```
{
  status: 1,
  message: "...",
  filters_applied: {...},
  statistics: {...},
  data: [
    {
      id: int,
      name: string,
      classes: [
        {
          id: int,
          name: string,
          sections: [
            {
              id: int,
              name: string,
              students: [
                {
                  id: int,
                  admission_no: string,
                  full_name: string,
                  ...
                }
              ]
            }
          ]
        }
      ]
    }
  ],
  timestamp: string
}
```

---

## ✅ Testing Completion Checklist

- [ ] Test 1: API 1 - Empty body ✅
- [ ] Test 2: API 1 - Filter by session ✅
- [ ] Test 3: API 1 - Filter by session and class ✅
- [ ] Test 4: API 2 - Empty body ✅
- [ ] Test 5: API 2 - Filter by session ✅
- [ ] Test 6: API 2 - Filter by section ✅
- [ ] Test 7: Invalid headers ✅
- [ ] Test 8: Wrong method ✅
- [ ] Verify hierarchical structure ✅
- [ ] Verify student data (API 2) ✅
- [ ] Verify statistics (API 2) ✅
- [ ] Verify filtering works ✅

---

## 📚 Documentation References

- **API 1 Documentation:** `API_1_FEE_COLLECTION_FILTERS_GET.md`
- **API 2 Documentation:** `API_2_FEE_COLLECTION_HIERARCHY_WITH_STUDENTS.md`
- **Quick Reference:** `FEE_COLLECTION_HIERARCHICAL_API_QUICK_REFERENCE.md`
- **Implementation Summary:** `FEE_COLLECTION_HIERARCHICAL_IMPLEMENTATION_SUMMARY.md`

---

**Last Updated:** October 10, 2025  
**Status:** ✅ Ready for Testing

