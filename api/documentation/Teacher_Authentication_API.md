# Teacher Authentication API Documentation

## Overview

The Teacher Authentication API provides secure authentication and profile management for teachers in the Smart School Management System. It supports both traditional token-based authentication and modern JWT (JSON Web Token) authentication.

## Base URL
```
http://{domain}/api/
```

## Database Configuration

The API connects to the following database:
- **Database**: `digita90_testschool`
- **Username**: `digita90_digidineuser`
- **Password**: `Neelarani@@10`
- **Host**: `localhost`

## Test Credentials

For testing purposes, use these credentials:
- **Email**: `teacher@gmail.com`
- **Password**: `teacher`

## Authentication Headers

All API requests require the following headers:

```
Client-Service: smartschool
Auth-Key: schoolAdmin@
Content-Type: application/json
```

For authenticated endpoints, also include:
```
User-ID: {user_id}
Authorization: {token}
JWT-Token: {jwt_token} (optional, for JWT authentication)
```

## Postman Testing Guide

### Quick Setup for Postman

1. **Import Collection**: Copy the cURL commands below and import them into Postman
2. **Set Base URL**: Create an environment variable `{{base_url}}` = `http://localhost/amt/api`
3. **Test Credentials**: Use `teacher@gmail.com` / `teacher` for testing

## API Endpoints

### 1. Connectivity Test

**Endpoint:** `GET /teacher/test`

**Description:** Basic connectivity test to verify API is working.

**cURL Command:**
```bash
curl -X GET "{{base_url}}/teacher/test" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -H "Content-Type: application/json"
```

**Success Response (200):**
```json
{
    "status": 1,
    "message": "Teacher Auth Controller is working",
    "timestamp": "2025-08-22 06:29:15",
    "database_connected": true,
    "models_loaded": {
        "teacher_auth_model": true,
        "staff_model": true,
        "setting_model": true
    }
}
```

### 2. Simple Login (Token-based)

**Endpoint:** `POST /teacher/simple-login`

**Description:** Simple login without JWT, returns basic token.

**cURL Command:**
```bash
curl -X POST "{{base_url}}/teacher/simple-login" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "email=teacher@gmail.com&password=teacher"
```

**Success Response (200):**
```json
{
    "status": 1,
    "message": "Login successful",
    "staff_id": "31",
    "name": "teacher ",
    "email": "teacher@gmail.com"
}
```

**Error Response (401):**
```json
{
    "status": 0,
    "message": "Invalid email or password."
}
```

### 3. Full Login (JWT-enabled)

**Endpoint:** `POST /teacher/login`

**Description:** Complete login with JWT token and full user information.

**cURL Command:**
```bash
curl -X POST "{{base_url}}/teacher/login" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "teacher@gmail.com",
    "password": "teacher",
    "deviceToken": "optional_device_token"
  }'
```

**Success Response (200):**
```json
{
    "status": 1,
    "message": "Successfully logged in.",
    "id": "31",
    "token": "NppK+b2i0lBOQATtDfP6dBCsca2Lgz1w",
    "jwt_token": null,
    "role": "teacher",
    "record": {
        "id": "31",
        "staff_id": "31",
        "employee_id": "322",
        "role": "teacher",
        "email": "teacher@gmail.com",
        "contact_no": "",
        "username": "teacher",
        "name": "teacher",
        "surname": "",
        "designation": null,
        "department": null,
        "date_format": "d/m/Y",
        "currency_symbol": "₹",
        "currency_short_name": "68",
        "currency_id": "68",
        "timezone": "Asia/Kolkata",
        "sch_name": "MGM School",
        "language": {
            "lang_id": "4",
            "language": "English",
            "short_code": "en"
        },
        "is_rtl": "disabled",
        "theme": "white.jpg",
        "image": "",
        "start_week": "Thursday",
        "superadmin_restriction": "enabled"
    }
}
```

**Error Response (401):**
```json
{
    "status": 0,
    "message": "Invalid Email or Password"
}
```

### 4. Debug Login

**Endpoint:** `POST /teacher/debug-login`

**Description:** Debug endpoint that provides detailed information about the request.

**cURL Command:**
```bash
curl -X POST "{{base_url}}/teacher/debug-login" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "teacher@gmail.com",
    "password": "teacher"
  }'
```

**Success Response (200):**
```json
{
    "status": 1,
    "message": "Debug information",
    "debug": {
        "all_headers": {
            "AUTHORIZATION": "",
            "AUTH-KEY": "schoolAdmin@",
            "CLIENT-SERVICE": "smartschool",
            "USER-AGENT": "curl/7.68.0",
            "HOST": "localhost"
        },
        "client_service": "smartschool",
        "auth_key": "schoolAdmin@",
        "expected_client_service": "smartschool",
        "expected_auth_key": "schoolAdmin@",
        "headers_valid": true,
        "post_data": {
            "email": "teacher@gmail.com",
            "password": "teacher"
        },
        "auth_check_result": true,
        "request_method": "POST",
        "content_type": "application/json",
        "login_attempt": {
            "email": "teacher@gmail.com",
            "password_length": 7,
            "login_result": {
                "status": 1,
                "message": "Successfully logged in.",
                "id": "31",
                "token": "generated_token_here",
                "jwt_token": null,
                "role": "teacher"
            }
        }
    }
}
```

## Postman Collection Setup

### Environment Variables
Create a Postman environment with these variables:
```
base_url: http://localhost/amt/api
client_service: smartschool
auth_key: schoolAdmin@
test_email: teacher@gmail.com
test_password: teacher
```

### Pre-request Scripts
For authenticated endpoints, add this pre-request script:
```javascript
// Set authentication headers
pm.request.headers.add({
    key: "Client-Service",
    value: pm.environment.get("client_service")
});
pm.request.headers.add({
    key: "Auth-Key",
    value: pm.environment.get("auth_key")
});
```

### Test Scripts
Add this test script to verify responses:
```javascript
pm.test("Status code is 200", function () {
    pm.response.to.have.status(200);
});

pm.test("Response has status field", function () {
    var jsonData = pm.response.json();
    pm.expect(jsonData).to.have.property('status');
});

// For login endpoints, save token
if (pm.response.json().token) {
    pm.environment.set("auth_token", pm.response.json().token);
    pm.environment.set("user_id", pm.response.json().id);
}
```

## Authenticated Endpoints

### 5. Teacher Logout

**Endpoint:** `POST /teacher/logout`

**Description:** Logout teacher and invalidate tokens.

**cURL Command:**
```bash
curl -X POST "{{base_url}}/teacher/logout" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -H "Content-Type: application/json" \
  -H "User-ID: {{user_id}}" \
  -H "Authorization: {{auth_token}}" \
  -d '{
    "deviceToken": "optional_device_token"
  }'
```

**Success Response (200):**
```json
{
    "status": 200,
    "message": "Successfully logged out."
}
```

### 6. Get Teacher Profile

**Endpoint:** `GET /teacher/profile`

**Description:** Retrieve authenticated teacher's profile information.

**cURL Command:**
```bash
curl -X GET "{{base_url}}/teacher/profile" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -H "Content-Type: application/json" \
  -H "User-ID: {{user_id}}" \
  -H "Authorization: {{auth_token}}"
```

**Success Response (200):**
```json
{
    "status": 1,
    "message": "Profile retrieved successfully.",
    "data": {
        "id": 31,
        "employee_id": "322",
        "name": "teacher",
        "surname": "",
        "father_name": null,
        "mother_name": null,
        "email": "teacher@gmail.com",
        "contact_no": "",
        "emergency_contact_no": null,
        "dob": null,
        "marital_status": null,
        "date_of_joining": null,
        "designation": null,
        "department": null,
        "qualification": null,
        "work_exp": null,
        "local_address": null,
        "permanent_address": null,
        "image": "",
        "gender": null,
        "account_title": null,
        "bank_account_no": null,
        "bank_name": null,
        "ifsc_code": null,
        "bank_branch": null,
        "payscale": null,
        "basic_salary": null,
        "epf_no": null,
        "contract_type": null,
        "work_shift": null,
        "work_location": null,
        "note": null,
        "is_active": 1
    }
}
```

### 4. Update Teacher Profile

**Endpoint:** `PUT /teacher/profile/update`

**Description:** Update teacher's profile information.

**Headers Required:**
```
User-ID: {user_id}
Authorization: {token}
JWT-Token: {jwt_token} (alternative)
```

**Request Body:**
```json
{
    "name": "John",
    "surname": "Doe",
    "father_name": "Robert Doe",
    "mother_name": "Mary Doe",
    "contact_no": "1234567890",
    "emergency_contact_no": "0987654321",
    "local_address": "123 New Main St, City",
    "permanent_address": "456 New Home St, Town",
    "qualification": "M.Sc Mathematics, B.Ed",
    "work_exp": "6 years",
    "note": "Updated profile information",
    "account_title": "John Doe",
    "bank_account_no": "1234567890",
    "bank_name": "XYZ Bank",
    "ifsc_code": "XYZ123456",
    "bank_branch": "Central Branch"
}
```

**Success Response (200):**
```json
{
    "status": 1,
    "message": "Profile updated successfully."
}
```

### 5. Change Password

**Endpoint:** `PUT /teacher/change-password`

**Description:** Change teacher's password.

**Headers Required:**
```
User-ID: {user_id}
Authorization: {token}
JWT-Token: {jwt_token} (alternative)
```

**Request Body:**
```json
{
    "current_password": "old_password",
    "new_password": "new_secure_password"
}
```

**Success Response (200):**
```json
{
    "status": 1,
    "message": "Password changed successfully."
}
```

**Error Response:**
```json
{
    "status": 0,
    "message": "Current password is incorrect."
}
```

### 6. Get Dashboard Data

**Endpoint:** `GET /teacher/dashboard`

**Description:** Get teacher's dashboard information including assigned classes and subjects.

**Headers Required:**
```
User-ID: {user_id}
Authorization: {token}
JWT-Token: {jwt_token} (alternative)
```

**Success Response (200):**
```json
{
    "status": 1,
    "message": "Dashboard data retrieved successfully.",
    "data": {
        "teacher_info": {
            "name": "John Doe",
            "employee_id": "EMP001",
            "designation": "Mathematics Teacher",
            "department": "Science Department",
            "email": "teacher@school.com",
            "image": "teacher_photo.jpg"
        },
        "assigned_classes": [
            {
                "class": "10",
                "section": "A",
                "session_id": 1
            },
            {
                "class": "9",
                "section": "B",
                "session_id": 1
            }
        ],
        "assigned_subjects": [
            {
                "subject_name": "Mathematics",
                "subject_code": "MATH"
            },
            {
                "subject_name": "Physics",
                "subject_code": "PHY"
            }
        ],
        "total_classes": 2,
        "total_subjects": 2
    }
}
```

### 7. Refresh JWT Token

**Endpoint:** `POST /teacher/refresh-token`

**Description:** Refresh an existing JWT token to extend its validity.

**Request Body:**
```json
{
    "jwt_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

**Success Response (200):**
```json
{
    "status": 1,
    "message": "Token refreshed successfully.",
    "jwt_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "expires_in": 31536000
}
```

**Error Response:**
```json
{
    "status": 0,
    "message": "Invalid or expired token. Please login again."
}
```

### 8. Validate JWT Token

**Endpoint:** `POST /teacher/validate-token`

**Description:** Validate a JWT token and get its information.

**Request Body:**
```json
{
    "jwt_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

**Success Response (200):**
```json
{
    "status": 1,
    "message": "Token is valid.",
    "payload": {
        "user_id": 123,
        "staff_id": 456,
        "email": "teacher@school.com",
        "role": "teacher",
        "employee_id": "EMP001",
        "name": "John Doe",
        "iat": 1640995200,
        "exp": 1672531200,
        "iss": "smartschool-api"
    },
    "remaining_time": 2592000,
    "expires_in_hours": 720.0,
    "is_expiring_soon": false
}
```

## Error Codes

| Code | Description |
|------|-------------|
| 200  | Success |
| 400  | Bad Request - Invalid parameters |
| 401  | Unauthorized - Invalid credentials or token |
| 403  | Forbidden - Access denied |
| 404  | Not Found - Resource not found |
| 429  | Too Many Requests - Rate limit exceeded |
| 500  | Internal Server Error |

## Common Error Response Format

```json
{
    "status": 0,
    "message": "Error description"
}
```

## Authentication Types

### 1. Traditional Token Authentication
- Use `User-ID` and `Authorization` headers
- Tokens expire after 8760 hours (1 year)
- Stored in database for validation

### 2. JWT Authentication
- Use `JWT-Token` header
- Self-contained tokens with embedded information
- Configurable expiration time
- No database lookup required for validation

## Security Features

1. **Password Hashing**: Passwords are securely hashed (implement proper hashing in production)
2. **Token Expiration**: Both token types have configurable expiration
3. **Rate Limiting**: API calls are rate-limited per teacher
4. **Role-Based Access**: Different permissions based on teacher roles
5. **Device Token Management**: Support for mobile device tokens
6. **Session Management**: Proper session handling and cleanup

## Rate Limiting

- Default: 100 requests per hour per teacher
- Exceeded requests return HTTP 429
- Configurable per endpoint

## Database Requirements

### Required Tables:
- `staff` - Teacher information
- `users_authentication` - Authentication tokens
- `staff_designation` - Teacher designations
- `department` - Departments
- `class_teacher` - Class assignments
- `teacher_subject` - Subject assignments

### Required Fields:
- `staff.app_key` - Mobile device token (add if missing)
- `users_authentication.staff_id` - Link to staff table

## Implementation Notes

1. **Production Security**:
   - Change JWT secret key
   - Implement proper password hashing (bcrypt/Argon2)
   - Use HTTPS for all API calls
   - Implement proper input validation

2. **Performance**:
   - Add database indexes for frequently queried fields
   - Implement caching for frequently accessed data
   - Use connection pooling for database connections

3. **Monitoring**:
   - Log all authentication attempts
   - Monitor failed login attempts
   - Track API usage patterns

## Teacher Webservice Endpoints

### 9. Get Teacher Menu Items

**Endpoint:** `GET /teacher/menu`

**Description:** Retrieve teacher-specific menu items based on role and permissions.

**Headers Required:**
```
User-ID: {user_id}
Authorization: {token}
JWT-Token: {jwt_token} (alternative)
```

**Success Response (200):**
```json
{
    "status": 1,
    "message": "Menu items retrieved successfully.",
    "data": {
        "role": {
            "id": 2,
            "name": "Teacher",
            "slug": "teacher",
            "is_superadmin": false
        },
        "menus": [
            {
                "id": 1,
                "menu": "Student Information",
                "icon": "fa fa-users",
                "activate_menu": "student_information",
                "lang_key": "student_information",
                "level": 1,
                "permission_group": "student_information",
                "submenus": [
                    {
                        "id": 1,
                        "menu": "Student Details",
                        "key": "student_details",
                        "lang_key": "student_details",
                        "url": "student/search",
                        "level": 1,
                        "permission_group": "student_information",
                        "activate_controller": "student",
                        "activate_methods": ["search", "view"]
                    }
                ]
            }
        ],
        "total_menus": 5
    }
}
```

### 10. Get Teacher Permissions

**Endpoint:** `GET /teacher/permissions`

**Description:** Retrieve all permissions assigned to the teacher based on their role.

**Success Response (200):**
```json
{
    "status": 1,
    "message": "Permissions retrieved successfully.",
    "data": {
        "role": {
            "id": 2,
            "name": "Teacher",
            "slug": "teacher",
            "is_superadmin": false
        },
        "permissions": {
            "student_information": {
                "group_id": 1,
                "group_name": "Student Information",
                "permissions": {
                    "student": {
                        "permission_id": 1,
                        "permission_name": "Student",
                        "can_view": true,
                        "can_add": false,
                        "can_edit": true,
                        "can_delete": false
                    }
                }
            }
        },
        "summary": {
            "total_permission_groups": 5,
            "total_permissions": 25,
            "active_permissions": 15
        }
    }
}
```

### 11. Get Accessible Modules

**Endpoint:** `GET /teacher/modules`

**Description:** Get list of modules/features accessible to the teacher.

**Success Response (200):**
```json
{
    "status": 1,
    "message": "Accessible modules retrieved successfully.",
    "data": {
        "role": {
            "id": 2,
            "name": "Teacher",
            "slug": "teacher",
            "is_superadmin": false
        },
        "modules": [
            {
                "group_id": 1,
                "group_name": "Student Information",
                "group_code": "student_information",
                "status": "active",
                "permissions_count": 5
            }
        ],
        "total_modules": 8
    }
}
```

### 12. Check Specific Permission

**Endpoint:** `POST /teacher/check-permission`

**Description:** Check if teacher has a specific permission.

**Request Body:**
```json
{
    "category": "student_information",
    "permission": "view"
}
```

**Success Response (200):**
```json
{
    "status": 1,
    "message": "Permission check completed.",
    "data": {
        "category": "student_information",
        "permission": "view",
        "has_permission": true,
        "role": {
            "id": 2,
            "name": "Teacher",
            "is_superadmin": false
        }
    }
}
```

### 13. Get Teacher Role Information

**Endpoint:** `GET /teacher/role`

**Description:** Get detailed role information for the authenticated teacher.

**Success Response (200):**
```json
{
    "status": 1,
    "message": "Role information retrieved successfully.",
    "data": {
        "role": {
            "id": 2,
            "name": "Teacher",
            "slug": "teacher",
            "is_superadmin": false
        },
        "staff_info": {
            "id": 456,
            "employee_id": "EMP001",
            "name": "John Doe",
            "designation": "Mathematics Teacher",
            "department": "Science Department"
        }
    }
}
```

### 14. Get System Settings

**Endpoint:** `GET /teacher/settings`

**Description:** Get system settings relevant to teachers.

**Success Response (200):**
```json
{
    "status": 1,
    "message": "System settings retrieved successfully.",
    "data": {
        "school_name": "Smart School",
        "school_code": "SS001",
        "session_id": 1,
        "currency_symbol": "$",
        "currency": "USD",
        "date_format": "d-m-Y",
        "time_format": "H:i",
        "timezone": "UTC",
        "language": "English",
        "is_rtl": "0",
        "theme": "default.jpg",
        "start_week": "Monday"
    }
}
```

## Troubleshooting

### Common Issues and Solutions

#### 1. "Unauthorized access" Error
**Problem:** Getting `{"status": 0, "message": "Unauthorized access."}`
**Solution:**
- Verify headers: `Client-Service: smartschool` and `Auth-Key: schoolAdmin@`
- Check header spelling and case sensitivity

#### 2. "Invalid Email or Password" Error
**Problem:** Getting `{"status": 0, "message": "Invalid Email or Password"}`
**Solutions:**
- Use correct test credentials: `teacher@gmail.com` / `teacher`
- Verify database connection is working (use `/teacher/test` endpoint)
- Check if staff record exists and is active in database

#### 3. Database Connection Issues
**Problem:** API returns database-related errors
**Solutions:**
- Verify database credentials in `api/application/config/database.php`
- Ensure MySQL service is running
- Check database name: `digita90_testschool`
- Test connection with `/teacher/test` endpoint

#### 4. Empty Response or 500 Error
**Problem:** No response or internal server error
**Solutions:**
- Check PHP error logs
- Verify CodeIgniter installation
- Ensure all required files are present
- Check file permissions

#### 5. Headers Not Being Sent
**Problem:** Headers not reaching the API
**Solutions:**
- Use proper header format in Postman
- For form data, use `application/x-www-form-urlencoded`
- For JSON data, use `application/json`
- Check server configuration for header handling

### Testing Checklist

Before testing, ensure:
- [ ] XAMPP/WAMP is running
- [ ] MySQL service is active
- [ ] Database `digita90_testschool` exists
- [ ] Staff record with email `teacher@gmail.com` exists
- [ ] API files are in correct directory structure
- [ ] Postman environment variables are set

### Quick Test Sequence

1. **Test Connectivity**: `GET /teacher/test`
2. **Test Simple Login**: `POST /teacher/simple-login` (form data)
3. **Test Full Login**: `POST /teacher/login` (JSON data)
4. **Test Debug Info**: `POST /teacher/debug-login`
5. **Test Profile** (after login): `GET /teacher/profile`

### Sample Postman Collection JSON

```json
{
    "info": {
        "name": "Teacher Authentication API",
        "description": "Complete API testing collection for teacher authentication"
    },
    "variable": [
        {
            "key": "base_url",
            "value": "http://localhost/amt/api"
        },
        {
            "key": "client_service",
            "value": "smartschool"
        },
        {
            "key": "auth_key",
            "value": "schoolAdmin@"
        },
        {
            "key": "test_email",
            "value": "teacher@gmail.com"
        },
        {
            "key": "test_password",
            "value": "teacher"
        }
    ],
    "item": [
        {
            "name": "1. Test Connectivity",
            "request": {
                "method": "GET",
                "header": [
                    {
                        "key": "Client-Service",
                        "value": "{{client_service}}"
                    },
                    {
                        "key": "Auth-Key",
                        "value": "{{auth_key}}"
                    }
                ],
                "url": {
                    "raw": "{{base_url}}/teacher/test",
                    "host": ["{{base_url}}"],
                    "path": ["teacher", "test"]
                }
            }
        },
        {
            "name": "2. Simple Login",
            "request": {
                "method": "POST",
                "header": [
                    {
                        "key": "Client-Service",
                        "value": "{{client_service}}"
                    },
                    {
                        "key": "Auth-Key",
                        "value": "{{auth_key}}"
                    }
                ],
                "body": {
                    "mode": "urlencoded",
                    "urlencoded": [
                        {
                            "key": "email",
                            "value": "{{test_email}}"
                        },
                        {
                            "key": "password",
                            "value": "{{test_password}}"
                        }
                    ]
                },
                "url": {
                    "raw": "{{base_url}}/teacher/simple-login",
                    "host": ["{{base_url}}"],
                    "path": ["teacher", "simple-login"]
                }
            }
        },
        {
            "name": "3. Full Login",
            "request": {
                "method": "POST",
                "header": [
                    {
                        "key": "Client-Service",
                        "value": "{{client_service}}"
                    },
                    {
                        "key": "Auth-Key",
                        "value": "{{auth_key}}"
                    },
                    {
                        "key": "Content-Type",
                        "value": "application/json"
                    }
                ],
                "body": {
                    "mode": "raw",
                    "raw": "{\n    \"email\": \"{{test_email}}\",\n    \"password\": \"{{test_password}}\"\n}"
                },
                "url": {
                    "raw": "{{base_url}}/teacher/login",
                    "host": ["{{base_url}}"],
                    "path": ["teacher", "login"]
                }
            },
            "event": [
                {
                    "listen": "test",
                    "script": {
                        "exec": [
                            "if (pm.response.json().token) {",
                            "    pm.environment.set('auth_token', pm.response.json().token);",
                            "    pm.environment.set('user_id', pm.response.json().id);",
                            "}"
                        ]
                    }
                }
            ]
        }
    ]
}
```

## API Testing Summary

The Teacher Authentication API provides comprehensive authentication and profile management capabilities. Use the provided cURL commands and Postman collection for thorough testing. All endpoints follow consistent patterns for headers, request/response formats, and error handling.

For production deployment:
1. Change database credentials
2. Update base URL
3. Implement proper SSL/TLS
4. Add rate limiting
5. Enable comprehensive logging
6. Implement proper error handling
