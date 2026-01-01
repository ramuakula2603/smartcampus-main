# Hall Ticket API Documentation

## Overview
The Hall Ticket API allows searching for students based on their hall ticket status and managing hall ticket numbers.

## Base URL
`http://localhost/amt/api/Hallticket_api/`

*Note: Replace `localhost/amt` with your actual domain when deploying to production.*

## Authentication
All requests must include the following headers:
- `Client-Service`: `smartschool`
- `Auth-Key`: `schoolAdmin@`

## Endpoints

### 1. Search Students by Hall Ticket Status
**Method:** `POST`
**URL:** `search`

**Description:**
Retrieves a list of students filtered by class, section, and hall ticket status.

**Request Body:**
```json
{
    "class_id": "1",
    "section_id": "1",
    "hall_ticket_status": "nohallticket"
}
```
*Note: `hall_ticket_status` can be "withhallticket" or "nohallticket". `section_id` is optional.*

**Response (Success):**
```json
{
    "status": 200,
    "message": "Success",
    "data": [
        {
            "id": "1",
            "firstname": "John",
            "lastname": "Doe",
            "admission_no": "1001",
            "class": "Class 1",
            "section": "A",
            ...
        }
    ]
}
```

**Response (Error):**
```json
{
    "status": 400,
    "message": "Class ID is required."
}
```

---

### 2. Update Hall Ticket Number
**Method:** `POST`
**URL:** `update_hall_ticket`

**Description:**
Updates or creates a hall ticket number for a student.

**Request Body:**
```json
{
    "student_id": "1",
    "hall_ticket_no": "HT2025001"
}
```

**Response (Success):**
```json
{
    "status": 200,
    "message": "Hall Ticket Number Updated Successfully."
}
```

**Response (Error - Already Exists):**
```json
{
    "status": 400,
    "message": "Hall Ticket Number already exists."
}
```

**Response (Error - Student Not Found):**
```json
{
    "status": 404,
    "message": "Student admission record not found."
}
```

---

### 3. Get Hall Ticket Number
**Method:** `POST`
**URL:** `get_hall_ticket`

**Description:**
Retrieves the hall ticket number for a specific student.

**Request Body:**
```json
{
    "student_id": "1"
}
```

**Response (Success):**
```json
{
    "status": 200,
    "message": "Success",
    "hall_ticket_no": "HT2025001"
}
```

**Response (Error - Not Found):**
```json
{
    "status": 404,
    "message": "Hall Ticket Number not found."
}
```

**Response (Error - Student Not Found):**
```json
{
    "status": 404,
    "message": "Student admission record not found."
}
```

---

## Error Responses

### Authentication Error
```json
{
    "status": 401,
    "message": "Client Service or Auth Key is invalid."
}
```

### Bad Request
```json
{
    "status": 400,
    "message": "Bad request."
}
```
