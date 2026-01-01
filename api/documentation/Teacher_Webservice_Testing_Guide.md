# Teacher Webservice API Testing Guide

## Overview

This guide provides comprehensive testing instructions for the Teacher Webservice API endpoints that handle menu items, permissions, and module access for teachers in the Smart School Management System.

## Prerequisites

1. **Database Setup**:
   - Ensure all teacher authentication tables are set up
   - Have at least one teacher with assigned role in the database
   - Verify permission tables are populated with data
   - Check that sidebar_menus and sidebar_sub_menus have data

2. **Authentication Setup**:
   - Complete teacher login to get authentication tokens
   - Ensure teacher has proper role assignments in staff_roles table
   - Verify role has permissions in roles_permissions table

## Testing Workflow

### Phase 1: Basic Webservice Testing

#### Test 1: Get Teacher Menu Items
**Endpoint:** `GET /teacher/menu`

**Test Steps:**
1. Login as a teacher to get authentication tokens
2. Send GET request to `/teacher/menu`
3. Verify response contains menu structure
4. Check that only accessible menus are returned
5. Validate submenu structure

**Expected Results:**
- Status 200 with menu hierarchy
- Only menus with permissions are included
- Submenus match teacher's access rights
- Role information is correctly displayed

#### Test 2: Get Teacher Permissions
**Endpoint:** `GET /teacher/permissions`

**Test Steps:**
1. Send GET request to `/teacher/permissions`
2. Verify all permission groups are returned
3. Check permission details (can_view, can_add, etc.)
4. Validate permission summary statistics

**Expected Results:**
- Complete permission structure returned
- Permissions match role assignments in database
- Summary statistics are accurate
- Super admin gets all permissions

#### Test 3: Get Accessible Modules
**Endpoint:** `GET /teacher/modules`

**Test Steps:**
1. Send GET request to `/teacher/modules`
2. Verify only accessible modules are returned
3. Check module information completeness
4. Validate module count

**Expected Results:**
- Only modules with granted permissions
- Accurate module information
- Correct total count

### Phase 2: Permission Management Testing

#### Test 4: Check Specific Permission
**Endpoint:** `POST /teacher/check-permission`

**Test Data:**
```json
{
    "category": "student_information",
    "permission": "view"
}
```

**Test Steps:**
1. Test with valid permission that teacher has
2. Test with permission teacher doesn't have
3. Test with invalid category/permission
4. Verify response accuracy

**Expected Results:**
- Accurate permission check results
- Proper error handling for invalid inputs
- Role information included in response

#### Test 5: Get Permission Groups
**Endpoint:** `GET /teacher/permission-groups`

**Test Steps:**
1. Send GET request to get all permission groups
2. Verify group information completeness
3. Check active vs total permission counts
4. Validate access level indicators

**Expected Results:**
- All relevant permission groups returned
- Accurate permission counts
- Correct access level determination

#### Test 6: Get Group Permissions Detail
**Endpoint:** `POST /teacher/group-permissions`

**Test Data:**
```json
{
    "group_code": "student_information"
}
```

**Test Steps:**
1. Test with valid group code
2. Test with invalid group code
3. Test with group teacher has no access to
4. Verify detailed permission information

**Expected Results:**
- Detailed permissions for accessible groups
- Proper error handling for invalid groups
- Complete permission breakdown

### Phase 3: Advanced Features Testing

#### Test 7: Bulk Permission Check
**Endpoint:** `POST /teacher/bulk-permission-check`

**Test Data:**
```json
{
    "permissions": [
        {
            "category": "student_information",
            "permission": "view",
            "identifier": "student_view"
        },
        {
            "category": "attendance",
            "permission": "add",
            "identifier": "attendance_add"
        }
    ]
}
```

**Test Steps:**
1. Test with multiple valid permissions
2. Test with mix of granted/denied permissions
3. Test with invalid permission format
4. Verify batch processing accuracy

**Expected Results:**
- All permissions checked accurately
- Proper handling of mixed results
- Identifier preservation in response

#### Test 8: Get Teacher Features
**Endpoint:** `GET /teacher/features`

**Test Steps:**
1. Send GET request to get feature access
2. Verify feature categorization
3. Check access determination logic
4. Validate granted permissions list

**Expected Results:**
- Comprehensive feature access information
- Accurate access determination
- Proper permission mapping

#### Test 9: Get Dashboard Summary
**Endpoint:** `GET /teacher/dashboard-summary`

**Test Steps:**
1. Send GET request for dashboard summary
2. Verify all summary statistics
3. Check quick stats accuracy
4. Validate percentage calculations

**Expected Results:**
- Complete dashboard summary
- Accurate statistics and percentages
- Proper quick access indicators

### Phase 4: Menu and Navigation Testing

#### Test 10: Get Sidebar Menu Structure
**Endpoint:** `GET /teacher/sidebar-menu`

**Test Steps:**
1. Send GET request for sidebar menu
2. Verify hierarchical structure
3. Check submenu organization
4. Validate menu counts

**Expected Results:**
- Properly structured sidebar menu
- Accurate hierarchy and counts
- Only accessible items included

#### Test 11: Get Navigation Breadcrumb
**Endpoint:** `POST /teacher/breadcrumb`

**Test Data:**
```json
{
    "controller": "student",
    "method": "search"
}
```

**Test Steps:**
1. Test with valid controller/method combination
2. Test with invalid combinations
3. Test with inaccessible controller
4. Verify breadcrumb generation

**Expected Results:**
- Accurate breadcrumb information
- Proper handling of invalid requests
- Complete navigation context

### Phase 5: Integration Testing

#### Test 12: Role-Based Access Control
**Test Scenarios:**
1. Teacher with limited permissions
2. Head teacher with extended permissions
3. Super admin with all permissions
4. Teacher with no role assigned

**Validation Points:**
- Menu items match role permissions
- Permission checks are consistent
- Module access aligns with role
- Error handling for invalid roles

#### Test 13: Authentication Integration
**Test Scenarios:**
1. Valid JWT token authentication
2. Traditional token authentication
3. Expired token handling
4. Invalid token handling

**Validation Points:**
- Both authentication methods work
- Proper error responses for invalid auth
- Token validation consistency
- Session management integration

### Phase 6: Error Handling Testing

#### Test 14: Invalid Request Testing
**Test Cases:**
1. Missing required headers
2. Invalid JSON in request body
3. Missing required parameters
4. Invalid parameter values

**Expected Results:**
- Proper HTTP status codes (400, 401, 403)
- Descriptive error messages
- Consistent error response format
- No system information leakage

#### Test 15: Database Error Handling
**Test Scenarios:**
1. Database connection issues
2. Missing permission data
3. Corrupted role assignments
4. Missing menu data

**Expected Results:**
- Graceful error handling
- Appropriate fallback responses
- No application crashes
- Proper logging of errors

## Performance Testing

### Load Testing
1. **Concurrent Requests**: Test with multiple simultaneous requests
2. **Large Data Sets**: Test with teachers having many permissions
3. **Complex Hierarchies**: Test with deep menu structures
4. **Bulk Operations**: Test bulk permission checks with large arrays

### Response Time Testing
- Menu retrieval: < 500ms
- Permission checks: < 200ms
- Dashboard summary: < 1000ms
- Bulk operations: < 2000ms

## Security Testing

### Authentication Security
1. Test with tampered tokens
2. Test with expired tokens
3. Test cross-teacher access attempts
4. Test privilege escalation attempts

### Data Security
1. Verify no unauthorized data exposure
2. Test SQL injection attempts
3. Test XSS in parameters
4. Verify proper input sanitization

## Automated Testing Script

```bash
#!/bin/bash
# Teacher Webservice API Test Script

BASE_URL="http://localhost/your-project/api"
USER_ID="123"
TOKEN="your_auth_token"

echo "Testing Teacher Webservice API..."

# Test 1: Get Menu
echo "1. Testing Get Menu..."
curl -X GET "$BASE_URL/teacher/menu" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -H "User-ID: $USER_ID" \
  -H "Authorization: $TOKEN"

# Test 2: Get Permissions
echo "2. Testing Get Permissions..."
curl -X GET "$BASE_URL/teacher/permissions" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -H "User-ID: $USER_ID" \
  -H "Authorization: $TOKEN"

# Test 3: Check Permission
echo "3. Testing Check Permission..."
curl -X POST "$BASE_URL/teacher/check-permission" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -H "Content-Type: application/json" \
  -H "User-ID: $USER_ID" \
  -H "Authorization: $TOKEN" \
  -d '{"category":"student_information","permission":"view"}'

echo "API testing complete!"
```

## Troubleshooting Common Issues

### Issue 1: Empty Menu Response
- **Cause**: Teacher has no role assigned or no permissions
- **Solution**: Check staff_roles and roles_permissions tables
- **Verification**: Verify role assignment in database

### Issue 2: Permission Check Always Returns False
- **Cause**: Incorrect permission category or permission name
- **Solution**: Check permission_category table for correct short_codes
- **Verification**: Compare with existing permission structure

### Issue 3: Authentication Errors
- **Cause**: Invalid or expired tokens
- **Solution**: Re-authenticate to get fresh tokens
- **Verification**: Check token validity and expiration

### Issue 4: Database Connection Errors
- **Cause**: Database configuration or connection issues
- **Solution**: Verify database settings and connectivity
- **Verification**: Test database connection separately

## Success Criteria

The Teacher Webservice API testing is considered successful when:

1. **Functionality**: All endpoints return expected data
2. **Security**: Proper authentication and authorization
3. **Performance**: Response times within acceptable limits
4. **Integration**: Seamless integration with teacher authentication
5. **Error Handling**: Graceful handling of all error scenarios
6. **Data Integrity**: Accurate permission and menu data
7. **Scalability**: Handles multiple concurrent requests
8. **Documentation**: All endpoints properly documented

## Next Steps

After successful testing:
1. Deploy to staging environment
2. Conduct user acceptance testing
3. Performance optimization if needed
4. Security audit and penetration testing
5. Production deployment
6. Monitor API usage and performance
