# Teacher Authentication API Testing Guide

## Prerequisites

1. **Database Setup**:
   - Run the database migration script: `api/database_updates/teacher_auth_migration.sql`
   - Ensure you have at least one teacher record in the `staff` table with valid email and password
   - Verify the `users_authentication` table exists with the `staff_id` field

2. **Server Setup**:
   - Ensure your web server is running (Apache/Nginx)
   - PHP version 7.0+ recommended
   - CodeIgniter framework properly configured
   - Database connection configured in `api/application/config/database.php`

3. **Postman Setup**:
   - Import the collection: `api/postman/Teacher_Authentication_API.postman_collection.json`
   - Import the environment: `api/postman/Teacher_Authentication_Environment.postman_environment.json`
   - Update the `base_url` in the environment to match your server URL

## Testing Workflow

### Step 1: Environment Configuration

1. Open Postman and select the "Teacher Authentication Environment"
2. Update the `base_url` variable to your actual API URL:
   ```
   http://your-domain.com/api
   ```
   or for local development:
   ```
   http://localhost/your-project-folder/api
   ```

### Step 2: Database Preparation

Create a test teacher record in your database:

```sql
-- Insert a test teacher (adjust values as needed)
INSERT INTO staff (
    employee_id, name, surname, email, password, 
    contact_no, designation, department, is_active,
    date_of_joining, gender, lang_id, currency_id
) VALUES (
    'TEACH001', 'John', 'Doe', 'teacher@school.com', 'teacher123',
    '1234567890', 1, 1, 1,
    '2020-01-15', 'Male', 1, 1
);
```

### Step 3: Basic Authentication Testing

#### Test 1: Teacher Login
1. Open the "Teacher Login" request
2. Update the request body with valid credentials:
   ```json
   {
       "email": "teacher@school.com",
       "password": "teacher123",
       "deviceToken": "test_device_token"
   }
   ```
3. Send the request
4. **Expected Result**: Status 200 with authentication tokens
5. **Verification**: Check that environment variables are auto-populated

#### Test 2: Get Teacher Profile (Token Auth)
1. Open "Get Teacher Profile" request
2. Send the request (uses auto-populated tokens)
3. **Expected Result**: Status 200 with teacher profile data

#### Test 3: Get Teacher Profile (JWT Auth)
1. Open "Get Teacher Profile (JWT)" request
2. Send the request (uses JWT token)
3. **Expected Result**: Status 200 with teacher profile data

### Step 4: Profile Management Testing

#### Test 4: Update Teacher Profile
1. Open "Update Teacher Profile" request
2. Modify the request body with new information
3. Send the request
4. **Expected Result**: Status 200 with success message
5. **Verification**: Get profile again to confirm changes

#### Test 5: Change Password
1. Open "Change Password" request
2. Use current password and a new password
3. Send the request
4. **Expected Result**: Status 200 with success message
5. **Verification**: Try logging in with the new password

### Step 5: Dashboard and Data Testing

#### Test 6: Get Dashboard Data
1. Open "Get Dashboard Data" request
2. Send the request
3. **Expected Result**: Status 200 with dashboard information including assigned classes and subjects

### Step 6: JWT Token Management Testing

#### Test 7: Refresh JWT Token
1. Open "Refresh JWT Token" request
2. Send the request
3. **Expected Result**: Status 200 with new JWT token
4. **Verification**: Environment variable should be updated

#### Test 8: Validate JWT Token
1. Open "Validate JWT Token" request
2. Send the request
3. **Expected Result**: Status 200 with token validation information

### Step 7: Error Scenario Testing

#### Test 9: Invalid Login Credentials
1. Open "Login - Invalid Credentials" request
2. Send the request
3. **Expected Result**: Status 200 with error message (status: 0)

#### Test 10: Unauthorized Access
1. Open "Profile - Unauthorized Access" request
2. Send the request
3. **Expected Result**: Status 401 with unauthorized message

#### Test 11: Missing Headers
1. Open "Missing Required Headers" request
2. Send the request
3. **Expected Result**: Status 200 with unauthorized message

### Step 8: Logout Testing

#### Test 12: Teacher Logout
1. Open "Teacher Logout" request
2. Send the request
3. **Expected Result**: Status 200 with logout success message
4. **Verification**: Try accessing profile - should get unauthorized error

## Manual Testing Scenarios

### Scenario 1: Complete Authentication Flow
1. Login → Get Profile → Update Profile → Change Password → Logout
2. Verify each step works correctly
3. Confirm tokens are properly managed

### Scenario 2: JWT vs Token Authentication
1. Login to get both tokens
2. Test profile access with traditional token
3. Test profile access with JWT token
4. Compare response times and functionality

### Scenario 3: Token Expiration Handling
1. Login and get tokens
2. Wait for token expiration (or manually expire in database)
3. Try accessing protected endpoints
4. Verify proper error messages

### Scenario 4: Concurrent Sessions
1. Login from multiple devices/sessions
2. Verify each session works independently
3. Test logout from one session doesn't affect others

## Performance Testing

### Load Testing with Postman
1. Use Postman Runner for bulk testing
2. Test login endpoint with multiple iterations
3. Monitor response times and success rates
4. Test concurrent profile updates

### Recommended Test Data
```json
{
    "valid_teacher": {
        "email": "teacher@school.com",
        "password": "teacher123"
    },
    "invalid_teacher": {
        "email": "invalid@school.com",
        "password": "wrongpassword"
    },
    "update_data": {
        "name": "Updated Name",
        "contact_no": "9876543210",
        "local_address": "Updated Address"
    }
}
```

## Troubleshooting Common Issues

### Issue 1: "Unauthorized" Error
- **Cause**: Missing or invalid headers
- **Solution**: Check Client-Service and Auth-Key headers
- **Verification**: Compare with working requests

### Issue 2: Database Connection Error
- **Cause**: Database configuration issues
- **Solution**: Check database.php configuration
- **Verification**: Test database connection separately

### Issue 3: JWT Token Invalid
- **Cause**: Token expired or malformed
- **Solution**: Login again to get fresh token
- **Verification**: Use validate-token endpoint

### Issue 4: Profile Update Not Working
- **Cause**: Invalid field names or authentication issues
- **Solution**: Check field names match database schema
- **Verification**: Check database for actual updates

## Security Testing Checklist

- [ ] SQL Injection testing on login fields
- [ ] XSS testing on profile update fields
- [ ] Token manipulation testing
- [ ] Rate limiting verification
- [ ] HTTPS enforcement (production)
- [ ] Password strength validation
- [ ] Session timeout testing
- [ ] Concurrent session handling

## Automated Testing Script

Create a simple test script to verify all endpoints:

```bash
#!/bin/bash
# Basic API health check script
BASE_URL="http://localhost/your-project/api"

echo "Testing Teacher Authentication API..."

# Test login
curl -X POST "$BASE_URL/teacher/login" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -H "Content-Type: application/json" \
  -d '{"email":"teacher@school.com","password":"teacher123"}'

echo "API testing complete!"
```

## Reporting Issues

When reporting issues, include:
1. Request details (method, URL, headers, body)
2. Expected vs actual response
3. Environment details (PHP version, database version)
4. Error logs from server
5. Steps to reproduce

## Next Steps

After successful testing:
1. Deploy to staging environment
2. Conduct user acceptance testing
3. Performance optimization
4. Security audit
5. Production deployment
