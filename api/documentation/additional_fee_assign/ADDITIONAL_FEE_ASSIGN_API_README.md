# Additional Fee Assignment API

## Overview

This API provides programmatic access to update additional fee assignments in the school management system. It replicates the functionality of the web interface's `updateadditionalfee` method but exposes it as a RESTful API endpoint.

## Purpose

The Additional Fee Assignment API was created to:
1. Provide programmatic access to additional fee management functionality
2. Allow external systems to update additional fee amounts
3. Maintain consistency with the existing web interface functionality

## API Endpoint

**Base URL:** `http://localhost/amt/api/`
**Endpoint:** `POST /additional-fee-assign/update`
**Full URL:** `http://localhost/amt/api/additional-fee-assign/update`

## Related Components

### Web Interface Equivalent
- **Controller:** `application/controllers/admin/Additionalfeeassigns.php`
- **Method:** `updateadditionalfee()`
- **Model:** `application/models/feesessiongroupadding_model.php`
- **Method:** `updateadditionalfee()`

### Database Table
- **Table:** `student_fees_amountadding`
- **Key Fields:**
  - `id` - Primary key
  - `amount` - Fee amount (decimal)
  - `fee_groups_feetype_id` - Reference to fee type
  - `student_session_id` - Reference to student

## Authentication

All requests require the following headers:
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

## Usage Example

```bash
curl -X POST "http://localhost/amt/api/additional-fee-assign/update" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "id": 1,
    "amount": 1500.00
  }'
```

## Response Format

### Success Response (HTTP 200)
```json
{
  "status": "success",
  "error": ""
}
```

### Error Response (HTTP 400)
```json
{
  "status": "fail",
  "error": {
    "feegroupid_feegroupid": "The Fee Master field is required.",
    "amount_amount": "The Amount field is required."
  }
}
```

## Testing

1. Ensure the API server is running
2. Verify authentication credentials are correct
3. Use the test script at `api/test_additional_fee_assign_api.php`
4. Replace test data with valid IDs from your database

## Notes

- This API only provides update functionality, not full CRUD operations
- The API follows the same validation rules as the web interface
- Error responses maintain compatibility with existing front-end error handling
- All monetary values are handled as decimal numbers with 2 decimal places
