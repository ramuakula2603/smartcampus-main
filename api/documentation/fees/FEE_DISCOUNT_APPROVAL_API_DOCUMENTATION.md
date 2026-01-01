# Fee Discount Approval API Documentation

## Overview

The **Fee Discount Approval API** provides RESTful endpoints for managing fee discount approval records in the school management system. This API enables you to list, view, approve, reject, and revert fee discount approvals for students. Fee discounts are applied to student fee payments and require approval before being processed.

**Base URL:** `http://localhost/amt/api`

**Authentication:** All endpoints require the following headers:
- `Client-Service: smartschool`
- `Auth-Key: schoolAdmin@`
- `Content-Type: application/json`

---

## ⚠️ Important Notes

**All endpoints use POST method** - Even for retrieving data, you must use POST (not GET).

**URL Format** - Make sure your URL is correct:
- ❌ Wrong: `hhttp://localhost/amt/api/fee-discount-approval-api/approve/15` (typo: "hhttp")
- ✅ Correct: `http://localhost/amt/api/fee-discount-approval-api/approve/15`
- Always use `http://` (not `https://` or `hhttp://`)

**URL Placeholders** - When you see `{id}` in the documentation, replace it with an actual number:
- ❌ Wrong: `http://localhost/amt/api/fee-discount-approval-api/get/{id}`
- ✅ Correct: `http://localhost/amt/api/fee-discount-approval-api/get/1`
- Example: For ID 15, use `/approve/15` not `/approve/{id}`

**Request Body** - Even if the endpoint doesn't require data, you must send an empty JSON object `{}` in the request body.

**Approval Status Values:**
- `0` or `"pending"` - Discount request is pending approval
- `1` or `"approved"` - Discount has been approved
- `2` or `"rejected"` - Discount has been rejected

---

## Endpoints

### 1. List All Fee Discount Approvals

**Endpoint:** `POST /fee-discount-approval-api/list`

**Description:** Retrieves a list of all fee discount approvals with optional filtering by class, section, session, and approval status.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Request Body:**
```json
{
  "class_id": [1, 2],
  "section_id": [1, 3],
  "session_id": 1,
  "approval_status": "pending"
}
```

**Request Parameters:**
| Parameter | Type | Required | Description | Example Values |
|-----------|------|----------|-------------|----------------|
| `class_id` | integer or array | No | Filter by class ID(s). Can be a single ID or array of IDs | `1`, `[1, 2, 3]` |
| `section_id` | integer or array | No | Filter by section ID(s). Can be a single ID or array of IDs | `1`, `[1, 3]` |
| `session_id` | integer or array | No | Filter by session ID(s). Can be a single ID or array of IDs | `1`, `[1, 2]` |
| `approval_status` | integer or string | No | Filter by approval status. Can be `0`/`"pending"`, `1`/`"approved"`, `2`/`"rejected"`, or array of these values | `"pending"`, `1`, `["pending", "approved"]` |

**Important Notes:**
- All parameters are optional. If no parameters are provided, all discount approvals will be returned.
- Multiple filters can be combined. All filters are applied with AND logic.
- Results are ordered by approval ID in descending order (newest first).

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Fee discount approvals retrieved successfully",
  "total_records": 2,
  "data": [
    {
      "approval_id": 5,
      "student_id": 123,
      "student_session_id": 456,
      "admission_no": "ADM001",
      "student_name": "John Doe",
      "father_name": "Robert Doe",
      "class": "Class 10",
      "class_id": 10,
      "section": "A",
      "section_id": 1,
      "date_of_birth": "2010-05-15",
      "gender": "Male",
      "category": "General",
      "mobile_number": "1234567890",
      "fee_group": "Annual Fees",
      "discount_amount": "500.00",
      "discount_note": "Merit scholarship discount",
      "approval_status": 0,
      "approval_status_text": "pending",
      "payment_id": null,
      "date": "2025-11-13",
      "created_at": "2025-11-13 10:30:00"
    },
    {
      "approval_id": 4,
      "student_id": 124,
      "student_session_id": 457,
      "admission_no": "ADM002",
      "student_name": "Jane Smith",
      "father_name": "Michael Smith",
      "class": "Class 9",
      "class_id": 9,
      "section": "B",
      "section_id": 2,
      "date_of_birth": "2011-08-20",
      "gender": "Female",
      "category": "OBC",
      "mobile_number": "9876543210",
      "fee_group": "Tuition Fees",
      "discount_amount": "1000.00",
      "discount_note": "Need-based discount",
      "approval_status": 1,
      "approval_status_text": "approved",
      "payment_id": "INV001/001",
      "date": "2025-11-12",
      "created_at": "2025-11-12 14:20:00"
    }
  ]
}
```

**Error Responses:**

**401 Unauthorized - Invalid Headers:**
```json
{
  "status": 0,
  "message": "Unauthorized access. Invalid headers.",
  "data": null
}
```

**405 Method Not Allowed:**
```json
{
  "status": 0,
  "message": "Method not allowed. Use POST method.",
  "data": null
}
```

**500 Internal Server Error:**
```json
{
  "status": 0,
  "message": "Internal server error occurred",
  "data": null
}
```

---

### 2. Get Specific Fee Discount Approval

**Endpoint:** `POST /fee-discount-approval-api/get/{id}`

**Description:** Retrieves details of a specific fee discount approval by its ID.

**⚠️ Important:** 
- **Method must be POST** (not GET)
- Replace `{id}` with an actual approval ID number (e.g., use `1` instead of `{id}`)
- Example URL: `http://localhost/amt/api/fee-discount-approval-api/get/5`

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**URL Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `id` | integer | Yes | ID of the fee discount approval | `5`, `10`, `25` |

**Request Body:** `{}` (empty JSON object)

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Fee discount approval retrieved successfully",
  "data": {
    "approval_id": 5,
    "student_session_id": 456,
    "admission_no": "ADM001",
    "student_name": "John Doe",
    "father_name": "Robert Doe",
    "class": "Class 10",
    "section": "A",
    "date_of_birth": "2010-05-15",
    "gender": "Male",
    "mobile_number": "1234567890",
    "fee_group": "Annual Fees",
    "discount_amount": "500.00",
    "discount_note": "Merit scholarship discount",
    "approval_status": 0,
    "approval_status_text": "pending",
    "payment_id": null,
    "fee_groups_feetype_id": 10,
    "student_fees_master_id": 789,
    "date": "2025-11-13",
    "created_at": "2025-11-13 10:30:00"
  }
}
```

**Error Responses:**

**400 Bad Request - Invalid ID:**
```json
{
  "status": 0,
  "message": "Invalid or missing approval ID",
  "data": null
}
```

**404 Not Found:**
```json
{
  "status": 0,
  "message": "Fee discount approval not found",
  "data": null
}
```

---

### 3. Approve Fee Discount

**Endpoint:** `POST /fee-discount-approval-api/approve/{id}`

**Description:** Approves a fee discount approval. This updates the approval status to approved (1).

**⚠️ Important:** 
- **Method must be POST** (not PUT or PATCH)
- **URL must start with `http://`** (not `hhttp://` or `https://`)
- **Replace `{id}` with an actual approval ID number** (e.g., use `5` instead of `{id}`)
- **Correct Example URL:** `http://localhost/amt/api/fee-discount-approval-api/approve/5`
- **Note:** This API only updates the approval status. The full approval process may require creating fee deposits which should be handled through the main application or additional integration.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**URL Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `id` | integer | Yes | ID of the approval to approve | `5`, `10`, `25` |

**Request Body:** `{}` (empty JSON object)

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Fee discount approved successfully",
  "data": {
    "approval_id": 5,
    "approval_status": 1,
    "approval_status_text": "approved"
  }
}
```

**Error Responses:**

**400 Bad Request - Invalid ID:**
```json
{
  "status": 0,
  "message": "Invalid or missing approval ID",
  "data": null
}
```

**400 Bad Request - Already Approved:**
```json
{
  "status": 0,
  "message": "Discount approval is already approved",
  "data": null
}
```

**404 Not Found:**
```json
{
  "status": 0,
  "message": "Fee discount approval not found",
  "data": null
}
```

---

### 4. Reject Fee Discount

**Endpoint:** `POST /fee-discount-approval-api/reject/{id}`

**Description:** Rejects a fee discount approval. This updates the approval status to rejected (2).

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**URL Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `id` | integer | Yes | ID of the approval to reject | `5`, `10`, `25` |

**Request Body:** `{}` (empty JSON object)

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Fee discount rejected successfully",
  "data": {
    "approval_id": 5,
    "approval_status": 2,
    "approval_status_text": "rejected"
  }
}
```

**Error Responses:**

**400 Bad Request - Invalid ID:**
```json
{
  "status": 0,
  "message": "Invalid or missing approval ID",
  "data": null
}
```

**400 Bad Request - Already Rejected:**
```json
{
  "status": 0,
  "message": "Discount approval is already rejected",
  "data": null
}
```

**404 Not Found:**
```json
{
  "status": 0,
  "message": "Fee discount approval not found",
  "data": null
}
```

---

### 5. Revert Approved Fee Discount

**Endpoint:** `POST /fee-discount-approval-api/revert/{id}`

**Description:** Reverts an approved fee discount back to pending status and clears the payment ID. This is useful when an approved discount needs to be reviewed again.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**URL Parameters:**
| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `id` | integer | Yes | ID of the approval to revert | `5`, `10`, `25` |

**Request Body:** `{}` (empty JSON object)

**Success Response (200 OK):**
```json
{
  "status": 1,
  "message": "Fee discount approval reverted successfully",
  "data": {
    "approval_id": 5,
    "approval_status": 0,
    "approval_status_text": "pending",
    "payment_id": null
  }
}
```

**Error Responses:**

**400 Bad Request - Invalid ID:**
```json
{
  "status": 0,
  "message": "Invalid or missing approval ID",
  "data": null
}
```

**400 Bad Request - Already Pending:**
```json
{
  "status": 0,
  "message": "Discount approval is already in pending status",
  "data": null
}
```

**404 Not Found:**
```json
{
  "status": 0,
  "message": "Fee discount approval not found",
  "data": null
}
```

---

## HTTP Status Codes

| Code | Meaning | Description |
|------|---------|-------------|
| 200 | OK | Request successful (GET, LIST, APPROVE, REJECT, REVERT operations) |
| 400 | Bad Request | Invalid input or missing required fields |
| 401 | Unauthorized | Invalid or missing authentication headers |
| 404 | Not Found | Resource not found (approval) |
| 405 | Method Not Allowed | Wrong HTTP method used (must be POST) |
| 500 | Internal Server Error | Server error occurred |

---

## cURL Examples

### Example 1: List All Fee Discount Approvals

```bash
curl -X POST "http://localhost/amt/api/fee-discount-approval-api/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 2: List with Filters

```bash
curl -X POST "http://localhost/amt/api/fee-discount-approval-api/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "class_id": [1, 2],
    "section_id": 1,
    "approval_status": "pending"
  }'
```

### Example 3: Get Specific Approval

```bash
curl -X POST "http://localhost/amt/api/fee-discount-approval-api/get/5" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 4: Approve Discount

```bash
curl -X POST "http://localhost/amt/api/fee-discount-approval-api/approve/5" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 5: Reject Discount

```bash
curl -X POST "http://localhost/amt/api/fee-discount-approval-api/reject/5" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Example 6: Revert Approved Discount

```bash
curl -X POST "http://localhost/amt/api/fee-discount-approval-api/revert/5" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

## Complete Workflow: Managing Fee Discount Approvals

### Step 1: List Pending Discount Approvals

```bash
curl -X POST "http://localhost/amt/api/fee-discount-approval-api/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "approval_status": "pending"
  }'
```

**Response:**
```json
{
  "status": 1,
  "message": "Fee discount approvals retrieved successfully",
  "total_records": 3,
  "data": [
    {
      "approval_id": 5,
      "student_name": "John Doe",
      "discount_amount": "500.00",
      "approval_status_text": "pending"
    }
  ]
}
```

### Step 2: Get Specific Approval Details

```bash
curl -X POST "http://localhost/amt/api/fee-discount-approval-api/get/5" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Step 3: Approve the Discount

```bash
curl -X POST "http://localhost/amt/api/fee-discount-approval-api/approve/5" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### Step 4: (Optional) Revert if Needed

```bash
curl -X POST "http://localhost/amt/api/fee-discount-approval-api/revert/5" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

---

## Use Cases

### 1. **Discount Approval Management**
   - Review pending discount requests
   - Approve or reject discount applications
   - Track approval status for all students

### 2. **Filtering and Reporting**
   - Filter discounts by class, section, or session
   - Generate reports based on approval status
   - Track discount approvals by date range

### 3. **Bulk Operations**
   - List all pending approvals for a specific class
   - Review discounts by approval status
   - Monitor approved discounts with payment IDs

### 4. **Discount Reversal**
   - Revert approved discounts if needed
   - Clear payment IDs for re-processing
   - Change approval status back to pending

---

## Database Schema

### fees_discount_approval Table

The fee discount approval system uses the `fees_discount_approval` table with the following key fields:

- `id` (Primary Key) - Unique identifier for the approval
- `student_session_id` - Reference to student session
- `fee_groups_feetype_id` - Reference to fee type
- `student_fees_master_id` - Reference to student fee master record
- `amount` - Discount amount
- `description` - Discount note/description
- `approval_status` - Status (0=pending, 1=approved, 2=rejected)
- `payment_id` - Payment/invoice ID (set when approved)
- `date` - Discount date
- `session_id` - Academic session ID
- `created_at` - Creation timestamp

**Related Tables:**
- `students` - Student information
- `student_session` - Student class/section assignments
- `classes` - Class information
- `sections` - Section information
- `fee_groups` - Fee group information
- `student_fees_master` - Student fee master records

---

## Best Practices

1. **Always validate approval IDs** before performing operations
2. **Check approval status** before approving/rejecting to avoid duplicate operations
3. **Use filters** to efficiently retrieve specific discount approvals
4. **Handle error responses** appropriately in your application
5. **Log all API calls** for audit and debugging purposes
6. **Verify student information** before approving discounts
7. **Use the list endpoint with filters** to find specific approvals
8. **Monitor payment IDs** for approved discounts
9. **Use revert functionality** carefully as it clears payment information
10. **Maintain consistent approval workflow** across all discount requests

---

## Integration Notes

### Approval Process
- The approve endpoint updates the approval status to approved (1)
- Full approval process may require creating fee deposits through the main application
- Payment IDs are typically set when discounts are fully processed

### Status Management
- Pending (0): Initial status for new discount requests
- Approved (1): Discount has been approved
- Rejected (2): Discount has been rejected

### Revert Functionality
- Reverting an approved discount sets status back to pending (0)
- Payment ID is cleared when reverting
- This allows re-processing of the discount if needed

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | 2025-11-13 | Initial release with list, get, approve, reject, and revert endpoints |

---

## Support

For issues or questions regarding this API, please contact the development team or refer to the main API documentation.

