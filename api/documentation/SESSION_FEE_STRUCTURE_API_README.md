# Session Fee Structure API Documentation

## Overview

The Session Fee Structure API provides endpoints to retrieve comprehensive session-wise fee structure data for the school management system. This API returns hierarchical data showing sessions with their associated classes, sections, fee groups, and fee types with amounts.

**Key Features:**
- **Hierarchical Structure:** Sessions → Classes → Sections, and Sessions → Fee Groups → Fee Types
- **Flexible Filtering:** Filter by session, class, section, fee group, or fee type
- **Default Behavior:** Returns all session-wise fee structures when no filters are provided
- **Nested Response:** Easy-to-consume nested JSON structure

**Base URL:** `http://localhost/amt/api`

---

## Authentication

All endpoints require authentication headers:

```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

---

## Endpoints

### 1. Filter Session Fee Structure

**Endpoint:** `POST /api/session-fee-structure/filter`

**Description:** Retrieves session-wise fee structure data with optional filtering. Returns a hierarchical structure showing sessions with classes/sections and fee groups with fee types.

#### Request Body (All Parameters Optional)

```json
{
  "session_id": 1,
  "class_id": 2,
  "section_id": 3,
  "fee_group_id": 4,
  "fee_type_id": 5
}
```

#### Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| session_id | integer | No | Filter by specific session ID |
| class_id | integer | No | Filter by specific class ID |
| section_id | integer | No | Filter by specific section ID |
| fee_group_id | integer | No | Filter by specific fee group ID |
| fee_type_id | integer | No | Filter by specific fee type ID |

**Important Notes:**
- Empty request `{}` returns all session-wise fee structures
- All parameters are optional and can be combined
- Filters are applied with AND logic (all specified filters must match)

#### Response Format

**Success Response (200 OK):**

```json
{
  "status": 1,
  "message": "Session fee structure retrieved successfully",
  "filters_applied": {
    "session_id": 21,
    "class_id": null,
    "section_id": null,
    "fee_group_id": null,
    "fee_type_id": null
  },
  "total_sessions": 1,
  "data": [
    {
      "session_id": "21",
      "session_name": "2025-26",
      "session_is_active": "yes",
      "classes": [
        {
          "class_id": "10",
          "class_name": "JR-BIPC",
          "class_is_active": "no",
          "sections": [
            {
              "section_id": "11",
              "section_name": "08199-JR-BIPC-B1",
              "section_is_active": "no"
            }
          ]
        },
        {
          "class_id": "11",
          "class_name": "JR-CEC",
          "class_is_active": "no",
          "sections": [
            {
              "section_id": "14",
              "section_name": "08199-JR-CEC-B1",
              "section_is_active": "no"
            },
            {
              "section_id": "15",
              "section_name": "08199-JR-CEC-BATCH1",
              "section_is_active": "no"
            }
          ]
        }
      ],
      "fee_groups": [
        {
          "fee_session_group_id": "148",
          "fee_group_id": "139",
          "fee_group_name": "2025-2026 -SR- 0NTC",
          "fee_group_description": "",
          "fee_group_is_system": "0",
          "fee_group_is_active": "no",
          "fee_types": [
            {
              "fee_groups_feetype_id": "387",
              "fee_type_id": "40",
              "fee_type_name": "ADMISSION FEE",
              "fee_type_code": "8",
              "fee_type_description": "ADMISSION FEE\r\n",
              "fee_type_is_system": "0",
              "fee_type_is_active": "no",
              "amount": "2500.00",
              "due_date": null,
              "fine_type": "none",
              "fine_percentage": "0.00",
              "fine_amount": "0.00"
            },
            {
              "fee_groups_feetype_id": "397",
              "fee_type_id": "73",
              "fee_type_name": "ON-TC",
              "fee_type_code": "39",
              "fee_type_description": "",
              "fee_type_is_system": "0",
              "fee_type_is_active": "no",
              "amount": "2500.00",
              "due_date": null,
              "fine_type": "none",
              "fine_percentage": "0.00",
              "fine_amount": "0.00"
            }
          ]
        },
        {
          "fee_session_group_id": "160",
          "fee_group_id": "147",
          "fee_group_name": "2025-2026 JR-BIPC(BOOKS FEE)",
          "fee_group_description": "",
          "fee_group_is_system": "0",
          "fee_group_is_active": "no",
          "fee_types": [
            {
              "fee_groups_feetype_id": "401",
              "fee_type_id": "37",
              "fee_type_name": "BOOKS FEE",
              "fee_type_code": "5",
              "fee_type_description": "BOOKS FEE\r\n",
              "fee_type_is_system": "0",
              "fee_type_is_active": "no",
              "amount": "1200.00",
              "due_date": null,
              "fine_type": "none",
              "fine_percentage": "0.00",
              "fine_amount": "0.00"
            }
          ]
        }
      ]
    }
  ],
  "timestamp": "2025-10-10 12:52:54"
}
```

**Error Response (401 Unauthorized):**

```json
{
  "status": 0,
  "message": "Unauthorized access"
}
```

**Error Response (400 Bad Request):**

```json
{
  "status": 0,
  "message": "Bad request. Only POST method allowed."
}
```

**Error Response (500 Internal Server Error):**

```json
{
  "status": 0,
  "message": "Internal server error",
  "error": "Error details here"
}
```

---

### 2. List Filter Options

**Endpoint:** `POST /api/session-fee-structure/list`

**Description:** Retrieves available filter options including all sessions, classes, fee groups, and fee types.

#### Request Body

```json
{}
```

No parameters required.

#### Response Format

**Success Response (200 OK):**

```json
{
  "status": 1,
  "message": "Session fee structure filter options retrieved successfully",
  "sessions": [
    {
      "id": "7",
      "session": "2016-17",
      "is_active": "no",
      "created_at": "2017-04-20 12:12:19",
      "updated_at": "0000-00-00",
      "active": "0"
    },
    {
      "id": "11",
      "session": "2017-18",
      "is_active": "no",
      "created_at": "2017-04-20 12:11:37",
      "updated_at": "0000-00-00",
      "active": "0"
    }
  ],
  "classes": [
    {
      "id": "10",
      "class": "JR-BIPC",
      "is_active": "no",
      "created_at": "2024-03-17 14:17:56",
      "updated_at": null
    },
    {
      "id": "11",
      "class": "JR-CEC",
      "is_active": "no",
      "created_at": "2024-03-17 14:25:05",
      "updated_at": null
    }
  ],
  "fee_groups": [
    {
      "id": "25",
      "name": "2020-202108199OTHERFEE",
      "is_system": "0",
      "description": "OTHERFEE",
      "is_active": "no",
      "created_at": "2024-04-06 16:01:55"
    },
    {
      "id": "26",
      "name": "2021-202208199-JR-MPC",
      "is_system": "0",
      "description": "JR-MPC",
      "is_active": "no",
      "created_at": "2023-12-09 22:39:25"
    }
  ],
  "fee_types": [
    {
      "id": "21",
      "is_system": "0",
      "feecategory_id": null,
      "type": "Topper Discount",
      "code": "discount123",
      "is_active": "no",
      "description": "",
      "created_at": "2023-08-11 17:13:43",
      "updated_at": null
    },
    {
      "id": "33",
      "is_system": "0",
      "feecategory_id": null,
      "type": "TUITION FEE",
      "code": "1",
      "is_active": "no",
      "description": "",
      "created_at": "2023-12-09 21:59:20",
      "updated_at": null
    }
  ],
  "note": "Use the filter endpoint with parameters to get session fee structure data",
  "timestamp": "2025-10-10 12:54:03"
}
```

---

## Usage Examples

### Example 1: Get All Session Fee Structures (No Filters)

```bash
curl -X POST "http://localhost/amt/api/session-fee-structure/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Result:** Returns all sessions with their classes, sections, fee groups, and fee types.

---

### Example 2: Get Fee Structure for Specific Session

```bash
curl -X POST "http://localhost/amt/api/session-fee-structure/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "session_id": 1
  }'
```

**Result:** Returns fee structure data only for session ID 1.

---

### Example 3: Get Fee Structure for Specific Class

```bash
curl -X POST "http://localhost/amt/api/session-fee-structure/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "class_id": 2
  }'
```

**Result:** Returns fee structure data for all sessions that have class ID 2.

---

### Example 4: Get Fee Structure for Specific Fee Group

```bash
curl -X POST "http://localhost/amt/api/session-fee-structure/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "fee_group_id": 3
  }'
```

**Result:** Returns fee structure data filtered to show only fee group ID 3.

---

### Example 5: Combined Filters (Session + Class)

```bash
curl -X POST "http://localhost/amt/api/session-fee-structure/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "session_id": 1,
    "class_id": 2
  }'
```

**Result:** Returns fee structure data for session 1 and class 2 only.

---

### Example 6: Get Filter Options

```bash
curl -X POST "http://localhost/amt/api/session-fee-structure/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Result:** Returns all available sessions, classes, fee groups, and fee types for filtering.

---

## Filter Behavior

### Session ID Parameter (`session_id`)

- **When provided:** Returns data only for the specified session
- **When null/empty:** Returns data for all sessions
- **Example:** `"session_id": 1` returns only session 1 data

### Class ID Parameter (`class_id`)

- **When provided:** Returns data only for the specified class
- **When null/empty:** Returns data for all classes
- **Example:** `"class_id": 2` returns only class 2 data

### Section ID Parameter (`section_id`)

- **When provided:** Returns data only for the specified section
- **When null/empty:** Returns data for all sections
- **Example:** `"section_id": 3` returns only section 3 data

### Fee Group ID Parameter (`fee_group_id`)

- **When provided:** Returns data only for the specified fee group
- **When null/empty:** Returns data for all fee groups
- **Example:** `"fee_group_id": 4` returns only fee group 4 data

### Fee Type ID Parameter (`fee_type_id`)

- **When provided:** Returns data only for the specified fee type
- **When null/empty:** Returns data for all fee types
- **Example:** `"fee_type_id": 5` returns only fee type 5 data

### Combined Filters

- All filters use AND logic
- Example: `{"session_id": 1, "class_id": 2}` returns data that matches BOTH session 1 AND class 2
- More filters = more specific results

---

## Response Structure Explained

### Hierarchical Structure

The API returns data in a nested hierarchical structure:

```
Sessions
├── Session Details (id, name, is_active)
├── Classes
│   ├── Class Details (id, name, is_active)
│   └── Sections
│       └── Section Details (id, name, is_active)
└── Fee Groups
    ├── Fee Group Details (id, name, description, is_system, is_active)
    └── Fee Types
        └── Fee Type Details (id, name, code, amount, due_date, fine details)
```

### Important Notes

**Data Types:**
- All ID fields (session_id, class_id, section_id, fee_group_id, fee_type_id, etc.) are returned as **strings**, not integers
- Boolean-like fields (is_active, is_system) are returned as **strings** ("yes"/"no" or "0"/"1")
- Numeric fields (amount, fine_percentage, fine_amount) are returned as **strings** in decimal format (e.g., "2500.00")

**Null Values:**
- `due_date` can be `null` if no due date is set
- `fee_group_description` and `fee_type_description` can be empty strings (`""`)
- `updated_at` in list endpoint can be `null`
- `feecategory_id` in fee types can be `null`

**Fine Types:**
- `"none"` - No fine applicable
- `"percentage"` - Fine calculated as percentage of amount (check `fine_percentage`)
- `"fixed"` - Fixed fine amount (check `fine_amount`)

### Main Response Fields

| Field | Type | Description |
|-------|------|-------------|
| status | integer | Response status (1 = success, 0 = error) |
| message | string | Human-readable response message |
| filters_applied | object | Echo of the filters used in the request |
| total_sessions | integer | Total number of sessions in the response |
| data | array | Array of session objects with nested data |
| timestamp | string | Server timestamp when response was generated |

### Session Object Fields

| Field | Type | Description |
|-------|------|-------------|
| session_id | string | Unique session identifier |
| session_name | string | Session name (e.g., "2025-26") |
| session_is_active | string | Session active status ("yes" or "no") |
| classes | array | Array of class objects for this session |
| fee_groups | array | Array of fee group objects for this session |

### Class Object Fields

| Field | Type | Description |
|-------|------|-------------|
| class_id | string | Unique class identifier |
| class_name | string | Class name (e.g., "JR-BIPC", "JR-CEC") |
| class_is_active | string | Class active status ("yes" or "no") |
| sections | array | Array of section objects for this class |

### Section Object Fields

| Field | Type | Description |
|-------|------|-------------|
| section_id | string | Unique section identifier |
| section_name | string | Section name (e.g., "08199-JR-BIPC-B1") |
| section_is_active | string | Section active status ("yes" or "no") |

### Fee Group Object Fields

| Field | Type | Description |
|-------|------|-------------|
| fee_session_group_id | string | Unique fee session group identifier |
| fee_group_id | string | Unique fee group identifier |
| fee_group_name | string | Fee group name (e.g., "2025-2026 -SR- 0NTC") |
| fee_group_description | string | Fee group description (can be empty string) |
| fee_group_is_system | string | System fee group flag ("0" or "1") |
| fee_group_is_active | string | Fee group active status ("yes" or "no") |
| fee_types | array | Array of fee type objects for this fee group |

### Fee Type Object Fields

| Field | Type | Description |
|-------|------|-------------|
| fee_groups_feetype_id | string | Unique fee groups feetype identifier |
| fee_type_id | string | Unique fee type identifier |
| fee_type_name | string | Fee type name (e.g., "ADMISSION FEE", "BOOKS FEE") |
| fee_type_code | string | Fee type code (e.g., "8", "5") |
| fee_type_description | string | Fee type description (can be empty string) |
| fee_type_is_system | string | System fee type flag ("0" or "1") |
| fee_type_is_active | string | Fee type active status ("yes" or "no") |
| amount | string | Fee amount in decimal format (e.g., "2500.00") |
| due_date | string/null | Fee due date in YYYY-MM-DD format or null |
| fine_type | string | Fine type ("none", "percentage", "fixed") |
| fine_percentage | string | Fine percentage (e.g., "0.00", "2.00") |
| fine_amount | string | Fine amount in decimal format (e.g., "0.00") |

---

## Error Handling

### Common Errors and Solutions

#### 1. Unauthorized Access (401)

**Error:**
```json
{
  "status": 0,
  "message": "Unauthorized access"
}
```

**Cause:** Missing or incorrect authentication headers

**Solution:** Ensure you include the required headers:
```
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

---

#### 2. Bad Request (400)

**Error:**
```json
{
  "status": 0,
  "message": "Bad request. Only POST method allowed."
}
```

**Cause:** Using wrong HTTP method (GET, PUT, DELETE instead of POST)

**Solution:** Use POST method for all API calls

---

#### 3. Internal Server Error (500)

**Error:**
```json
{
  "status": 0,
  "message": "Internal server error",
  "error": "Error details"
}
```

**Cause:** Server-side error (database connection, query error, etc.)

**Solution:**
- Check server logs at `application/logs/log-{date}.php`
- Verify database connection is working
- Verify all required models are loaded
- Contact system administrator if issue persists

---

## Testing Instructions

### Step 1: Test Authentication

```bash
# Test with missing headers (should fail)
curl -X POST "http://localhost/amt/api/session-fee-structure/filter" \
  -H "Content-Type: application/json" \
  -d '{}'
```

**Expected:** 401 Unauthorized

---

### Step 2: Test Default Behavior (All Data)

```bash
# Test with empty filter (should return all data)
curl -X POST "http://localhost/amt/api/session-fee-structure/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Expected:** 200 OK with all session fee structure data

**Verify:**
- `status` is 1
- `data` array contains session objects
- Each session has `classes` and `fee_groups` arrays
- Fee groups have `fee_types` arrays

---

### Step 3: Test Session Filter

```bash
# Test with session filter
curl -X POST "http://localhost/amt/api/session-fee-structure/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "session_id": 1
  }'
```

**Expected:** 200 OK with data only for session 1

**Verify:**
- `filters_applied.session_id` is 1
- `data` array contains only session 1

---

### Step 4: Test List Endpoint

```bash
# Test list endpoint for filter options
curl -X POST "http://localhost/amt/api/session-fee-structure/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Expected:** 200 OK with filter options

**Verify:**
- `sessions` array has session objects
- `classes` array has class objects
- `fee_groups` array has fee group objects
- `fee_types` array has fee type objects

---

## Technical Details

### Database Tables Used

**Session and Class Tables:**
- `sessions` - Session information
- `student_session` - Student enrollment (links sessions to classes/sections)
- `classes` - Class information
- `sections` - Section information

**Fee Structure Tables:**
- `fee_session_groups` - Session-wise fee group mappings
- `fee_groups` - Fee group definitions
- `fee_groups_feetype` - Fee types within fee groups with amounts
- `feetype` - Fee type definitions

### Key Relationships

```
sessions (id) ← student_session (session_id)
student_session (class_id) → classes (id)
student_session (section_id) → sections (id)

sessions (id) ← fee_session_groups (session_id)
fee_session_groups (fee_groups_id) → fee_groups (id)
fee_session_groups (id) ← fee_groups_feetype (fee_session_group_id)
fee_groups_feetype (feetype_id) → feetype (id)
```

### Controller Location

- **File:** `api/application/controllers/Session_fee_structure_api.php`
- **Class:** `Session_fee_structure_api`
- **Methods:** `filter()`, `list()`, `get_session_fee_structure()`, `get_fee_groups_for_session()`, `get_fee_types_for_group()`

---

## Best Practices

1. **Always include authentication headers** in every request
2. **Start with empty filters** to see all available data
3. **Use list endpoint** to get available filter options
4. **Apply filters progressively** - start broad, then narrow down
5. **Handle nested structure** properly in your application
6. **Check is_active flags** to filter active/inactive records
7. **Use fee_type_code** for programmatic identification
8. **Consider fine_type** when calculating total fees
9. **Log API responses** for debugging and audit purposes
10. **Cache filter options** to reduce API calls
11. **Parse string values** - All IDs and numeric values are returned as strings, parse them as needed
12. **Handle null values** - Check for null before using fields like `due_date`, `updated_at`
13. **Handle empty strings** - Fields like `description` can be empty strings, provide defaults
14. **Type conversion** - Convert strings to appropriate types (parseInt, parseFloat) before calculations

---

## Use Cases

### 1. Display Fee Structure for Current Session

```javascript
// Fetch current session fee structure
fetch('http://localhost/amt/api/session-fee-structure/filter', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Client-Service': 'smartschool',
    'Auth-Key': 'schoolAdmin@'
  },
  body: JSON.stringify({
    session_id: 21  // Current session ID (2025-26)
  })
})
.then(response => response.json())
.then(data => {
  if (data.status === 1 && data.data.length > 0) {
    const session = data.data[0];
    console.log('Session:', session.session_name);
    console.log('Classes:', session.classes.length);
    console.log('Fee Groups:', session.fee_groups.length);

    // Display fee groups
    session.fee_groups.forEach(fg => {
      console.log(`\nFee Group: ${fg.fee_group_name}`);
      fg.fee_types.forEach(ft => {
        console.log(`  - ${ft.fee_type_name}: ₹${ft.amount}`);
      });
    });
  }
});
```

---

### 2. Build Fee Structure Dropdown

```javascript
// Fetch filter options
fetch('http://localhost/amt/api/session-fee-structure/list', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Client-Service': 'smartschool',
    'Auth-Key': 'schoolAdmin@'
  },
  body: JSON.stringify({})
})
.then(response => response.json())
.then(data => {
  // Populate dropdowns
  populateSessionDropdown(data.sessions);
  populateClassDropdown(data.classes);
  populateFeeGroupDropdown(data.fee_groups);
});
```

---

### 3. Calculate Total Fees for a Class

```javascript
// Fetch fee structure for specific class
fetch('http://localhost/amt/api/session-fee-structure/filter', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Client-Service': 'smartschool',
    'Auth-Key': 'schoolAdmin@'
  },
  body: JSON.stringify({
    session_id: 21,  // Session: 2025-26
    class_id: 10     // Class: JR-BIPC
  })
})
.then(response => response.json())
.then(data => {
  if (data.status === 1 && data.data.length > 0) {
    let totalFees = 0;
    let feeDetails = [];

    data.data[0].fee_groups.forEach(feeGroup => {
      feeGroup.fee_types.forEach(feeType => {
        const amount = parseFloat(feeType.amount);
        totalFees += amount;

        // Calculate fine if applicable
        let fine = 0;
        if (feeType.fine_type === 'percentage') {
          fine = amount * (parseFloat(feeType.fine_percentage) / 100);
        } else if (feeType.fine_type === 'fixed') {
          fine = parseFloat(feeType.fine_amount);
        }

        feeDetails.push({
          name: feeType.fee_type_name,
          amount: amount,
          fine: fine,
          total: amount + fine
        });
      });
    });

    console.log('Total Base Fees: ₹' + totalFees.toFixed(2));
    console.log('Fee Details:', feeDetails);
  }
});
```

---

## Related APIs

- **Collection Report API:** `/api/collection-report/filter` - Fee collection reports
- **Due Fees Report API:** `/api/due-fees-report/filter` - Due fees reports
- **Fee Master API:** `/api/fee-master/filter` - Fee master data
- **Classes API:** `/api/classes/list` - Class information
- **Sections API:** `/api/sections/list` - Section information

---

## Frequently Asked Questions (FAQ)

### Q1: What's the difference between fee_group_id and fee_session_group_id?

**A:**
- `fee_group_id` is the unique identifier for the fee group itself
- `fee_session_group_id` is the unique identifier for the fee group's association with a specific session
- One fee group can have multiple fee_session_group_ids (one per session)

---

### Q2: Why are some sessions showing empty classes or fee_groups arrays?

**A:** This can happen if:
- No students are enrolled in that session (empty classes)
- No fee groups are assigned to that session (empty fee_groups)
- Filters are excluding the data

---

### Q3: How do I get only active sessions/classes/fee groups?

**A:** Filter the response data based on the `is_active` field:
```javascript
const activeSessions = data.data.filter(s => s.session_is_active === 'yes');
```

---

### Q4: Can I filter by multiple sessions at once?

**A:** No, the current API supports filtering by a single session ID. To get multiple sessions, either:
- Make multiple API calls
- Use no session filter to get all sessions, then filter client-side

---

### Q5: What does is_system flag mean?

**A:** The `is_system` flag indicates whether a fee group or fee type is a system-defined (built-in) item ("1") or a custom user-defined item ("0"). Note: This is returned as a string, not an integer.

---

### Q6: How do I calculate fees with fines?

**A:** Check the `fine_type` field:
- If "none": No fine
- If "percentage": Fine = amount × (fine_percentage / 100)
- If "fixed": Fine = fine_amount

**Example:**
```javascript
const feeType = {
  amount: "2500.00",
  fine_type: "percentage",
  fine_percentage: "2.00"
};

const baseAmount = parseFloat(feeType.amount); // 2500.00
const fineAmount = feeType.fine_type === "percentage"
  ? baseAmount * (parseFloat(feeType.fine_percentage) / 100)
  : parseFloat(feeType.fine_amount);
// fineAmount = 50.00

const totalAmount = baseAmount + fineAmount; // 2550.00
```

---

### Q7: Why is the response structure nested?

**A:** The nested structure reflects the natural hierarchy of the data:
- Sessions contain classes and fee groups
- Classes contain sections
- Fee groups contain fee types
This makes it easier to display hierarchical data in UI components.

---

### Q8: Can I get fee structure for a specific student?

**A:** This API provides general fee structure data. For student-specific fee data, use the Student Fee Search API or Due Fees Report API.

---

### Q9: How often should I call the list endpoint?

**A:** Call it once when your application loads or when you need to refresh filter options. Cache the results to avoid unnecessary API calls.

---

### Q10: What if a session has no fee groups?

**A:** The session will still appear in the response with an empty `fee_groups` array. This is normal for newly created sessions.

---

### Q11: Why are all IDs returned as strings instead of integers?

**A:** The API returns all database IDs as strings to maintain consistency and avoid type conversion issues. When using the data in your application, parse them as needed:
```javascript
const sessionId = parseInt(data.data[0].session_id); // Convert to integer
const amount = parseFloat(feeType.amount); // Convert to float
```

---

### Q12: How do I handle null values in the response?

**A:** Some fields can be `null` (e.g., `due_date`, `updated_at`, `feecategory_id`). Always check for null before using:
```javascript
const dueDate = feeType.due_date || 'No due date set';
const description = feeGroup.fee_group_description || 'No description';
```

---

### Q13: What's the difference between empty string and null?

**A:**
- **Empty string (`""`)**: Field exists but has no value (e.g., `fee_group_description: ""`)
- **Null (`null`)**: Field is not set or not applicable (e.g., `due_date: null`)

Both should be handled gracefully in your application.

---

## Quick Reference: Data Type Conversions

When working with the API response, remember to convert string values to appropriate types:

```javascript
// Example API response data
const session = data.data[0];
const feeType = session.fee_groups[0].fee_types[0];

// Convert IDs to integers
const sessionId = parseInt(session.session_id);        // "21" → 21
const classId = parseInt(session.classes[0].class_id); // "10" → 10

// Convert amounts to floats
const amount = parseFloat(feeType.amount);                    // "2500.00" → 2500.00
const finePercentage = parseFloat(feeType.fine_percentage);   // "2.00" → 2.00
const fineAmount = parseFloat(feeType.fine_amount);           // "0.00" → 0.00

// Handle boolean-like strings
const isActive = session.session_is_active === "yes";         // "yes" → true
const isSystem = feeType.fee_type_is_system === "1";          // "0" → false

// Handle null values
const dueDate = feeType.due_date || "No due date";            // null → "No due date"
const description = feeType.fee_type_description || "N/A";    // "" → "N/A"

// Calculate total with fine
let total = amount;
if (feeType.fine_type === "percentage") {
  total += amount * (finePercentage / 100);
} else if (feeType.fine_type === "fixed") {
  total += fineAmount;
}
```

---

## Support

For issues or questions:
1. Check server logs at `application/logs/log-{date}.php`
2. Review this documentation
3. Verify authentication headers are correct
4. Ensure database connection is working
5. Contact system administrator

---

## Changelog

### Version 1.0 (Current)
- Initial API implementation
- Support for session, class, section, fee group, and fee type filtering
- Hierarchical nested response structure
- List endpoint for filter options
- Graceful null/empty parameter handling

---

**Last Updated:** 2025-10-10
**API Version:** 1.0
**Status:** Active

