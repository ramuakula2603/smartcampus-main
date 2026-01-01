# General Call API Test Cases

## Base URL
`http://localhost/amt`

## Common Headers for All Requests
```
Client-Service: smartschool
Auth-Key: schoolAdmin@
Authorization: Bearer {token}
```

## Test Cases

### 1. List All General Calls
**Endpoint:** `GET http://localhost/amt/api/generalcall_api`

#### Test Case 1.1: Successful Retrieval
- **URL:** `http://localhost/amt/api/generalcall_api`
- **Method:** `GET`
- **Expected Response:** 
  - Status: 200 OK
  ```json
  {
    "status": true,
    "data": [
      {
        "id": "1",
        "name": "John Doe",
        "contact": "1234567890",
        "date": "2025-11-07",
        "description": "Enquiry about admission",
        "call_duration": "10 minutes",
        "note": "Interested in Grade 5 admission",
        "follow_up_date": "2025-11-10"
      }
    ]
  }
  ```

#### Test Case 1.2: Empty Database
- **URL:** `http://localhost/amt/api/generalcall_api`
- **Method:** `GET`
- **Expected Response:**
  - Status: 404 Not Found
  ```json
  {
    "status": false,
    "message": "No general calls found"
  }
  ```

# General Call API Test Cases

## Base URL
`http://localhost/amt`

## Common Headers for All Requests
```
Client-Service: smartschool
Auth-Key: schoolAdmin@
Authorization: Bearer {token}
```

## Test Cases

### 1. List All General Calls
**Endpoint:** `GET http://localhost/amt/api/generalcall_api`

#### Test Case 1.1: Successful Retrieval
- **URL:** `http://localhost/amt/api/generalcall_api`
- **Method:** `GET`
- **Request Body:** None
- **Expected Response:** 
  - Status: 200 OK
  ```json
  {
    "status": true,
    "data": [
      {
        "id": "1",
        "name": "John Doe",
        "contact": "1234567890",
        "date": "2025-11-07",
        "description": "Enquiry about admission",
        "call_duration": "10 minutes",
        "note": "Interested in Grade 5 admission",
        "follow_up_date": "2025-11-10"
      }
    ]
  }
  ```


### 2. Get Single General Call
**Endpoint:** `GET http://localhost/amt/api/generalcall_api/detail/{id}`

#### Test Case 2.1: Valid ID
- **URL:** `http://localhost/amt/api/generalcall_api/detail/1`
- **Method:** `GET`
- **Request Body:** None
- **Expected Response:**
  - Status: 200 OK
  ```json
  {
    "status": true,
    "data": {
      "id": "1",
      "name": "John Doe",
      "contact": "1234567890",
      "date": "2025-11-07",
      "description": "Enquiry about admission",
      "call_duration": "10 minutes",
      "note": "Interested in Grade 5 admission",
      "follow_up_date": "2025-11-10"
    }
  }
  ```

#### Test Case 2.2: Invalid ID
- **URL:** `http://localhost/amt/api/generalcall_api/detail/999`
- **Method:** `GET`
- **Request Body:** None
- **Expected Response:**
  - Status: 404 Not Found
  ```json
  {
    "status": false,
    "message": "General call not found"
  }
  ```

#### Test Case 2.3: Malformed ID
- **URL:** `http://localhost/amt/api/generalcall_api/detail/abc`
- **Method:** `GET`
- **Request Body:** None
- **Expected Response:**
  - Status: 400 Bad Request
  ```json
  {
    "status": false,
    "message": "Invalid ID"
  }
  ```

### 3. Add New General Call
**Endpoint:** `POST http://localhost/amt/api/generalcall_api/add`

#### Test Case 3.1: Valid Creation
- **URL:** `http://localhost/amt/api/generalcall_api/add`
- **Method:** `POST`
- **Headers:** 
  ```
  Content-Type: application/json
  ```
- **Request Body:**
  ```json
  {
    "name": "John Doe",
    "contact": "1234567890",
    "date": "2025-11-07",
    "description": "Enquiry about admission",
    "call_duration": "10 minutes",
    "note": "Interested in Grade 5 admission",
    "follow_up_date": "2025-11-10"
  }
  ```
- **Expected Response:**
  - Status: 201 Created
  ```json
  {
    "status": true,
    "message": "General call added successfully",
    "data": {
      "id": "1"
    }
  }
  ```

#### Test Case 3.2: Missing Required Fields
- **URL:** `http://localhost/amt/api/generalcall_api/add`
- **Method:** `POST`
- **Request Body:** (missing required fields)
  ```json
  {
    "name": "John Doe"
  }
  ```
- **Expected Response:**
  - Status: 400 Bad Request
  ```json
  {
    "status": false,
    "message": "Validation errors message"
  }
  ```

#### Test Case 3.3: Invalid Date Format
- **URL:** `http://localhost/amt/api/generalcall_api/add`
- **Method:** `POST`
- **Request Body:** (invalid date)
  ```json
  {
    "name": "John Doe",
    "contact": "1234567890",
    "date": "invalid-date",
    "description": "Test",
    "call_duration": "10 minutes"
  }
  ```
- **Expected Response:**
  - Status: 400 Bad Request
  ```json
  {
    "status": false,
    "message": "Invalid date format"
  }
  ```

### 4. Update General Call
**Endpoint:** `PUT http://localhost/amt/api/generalcall_api/update/{id}`

#### Test Case 4.1: Valid Update
- **URL:** `http://localhost/amt/api/generalcall_api/update/1`
- **Method:** `PUT`
- **Headers:**
  ```
  Content-Type: application/json
  ```
- **Request Body:**
  ```json
  {
    "name": "John Doe Updated",
    "contact": "1234567890",
    "date": "2025-11-07",
    "description": "Updated description",
    "call_duration": "15 minutes",
    "note": "Updated notes",
    "follow_up_date": "2025-11-12"
  }
  ```
- **Expected Response:**
  - Status: 200 OK
  ```json
  {
    "status": true,
    "message": "General call updated successfully"
  }
  ```

#### Test Case 4.2: Invalid ID
- **URL:** `http://localhost/amt/api/generalcall_api/update/999`
- **Method:** `PUT`
- **Request Body:** (same as valid update)
  ```json
  {
    "name": "John Doe Updated",
    "contact": "1234567890",
    "date": "2025-11-07",
    "description": "Updated description",
    "call_duration": "15 minutes",
    "note": "Updated notes",
    "follow_up_date": "2025-11-12"
  }
  ```
- **Expected Response:**
  - Status: 404 Not Found
  ```json
  {
    "status": false,
    "message": "General call not found"
  }
  ```

#### Test Case 4.3: Invalid Data
- **URL:** `http://localhost/amt/api/generalcall_api/update/1`
- **Method:** `PUT`
- **Request Body:** (invalid data)
  ```json
  {
    "date": "invalid-date"
  }
  ```
- **Expected Response:**
  - Status: 400 Bad Request
  ```json
  {
    "status": false,
    "message": "Invalid data format"
  }
  ```

### 5. Delete General Call
**Endpoint:** `DELETE http://localhost/amt/api/generalcall_api/delete/{id}`

#### Test Case 5.1: Valid Deletion
- **URL:** `http://localhost/amt/api/generalcall_api/delete/1`
- **Method:** `DELETE`
- **Request Body:** None
- **Expected Response:**
  - Status: 200 OK
  ```json
  {
    "status": true,
    "message": "General call deleted successfully"
  }
  ```

#### Test Case 5.2: Non-existent ID
- **URL:** `http://localhost/amt/api/generalcall_api/delete/999`
- **Method:** `DELETE`
- **Request Body:** None
- **Expected Response:**
  - Status: 404 Not Found
  ```json
  {
    "status": false,
    "message": "General call not found"
  }
  ```

#### Test Case 5.3: Invalid ID Format
- **URL:** `http://localhost/amt/api/generalcall_api/delete/abc`
- **Method:** `DELETE`
- **Request Body:** None
- **Expected Response:**
  - Status: 400 Bad Request
  ```json
  {
    "status": false,
    "message": "Invalid ID format"
  }
  ```

