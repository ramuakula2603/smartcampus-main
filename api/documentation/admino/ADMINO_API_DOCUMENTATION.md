# Admino API Documentation

## Overview
This API allows searching for students based on their admission number status and updating their admission numbers.

## Base URL
`http://localhost/amt/api/Admino_api`

## Authentication
All requests must include the following headers:
- `Client-Service`: `smartschool`
- `Auth-Key`: `schoolAdmin@`

## Endpoints

### 1. Search Students
**URL:** `/search`
**Method:** `POST`

**Request Body:**
```json
{
    "class_id": "1",
    "section_id": "1",
    "admi_status": "withadmissionno"
}
```
*Note: `admi_status` can be "withadmissionno" or "noadmissionno".*
```

**Response:**
```json
{
    "status": 200,
    "message": "Success",
    "data": [
        {
            "id": "1",
            "firstname": "John",
            "lastname": "Doe",
            ...
        }
    ]
}
```

### 2. Update Admission Number
**URL:** `/update_admission_no`
**Method:** `POST`

**Request Body:**
```json
{
    "student_id": "1",
    "admi_no": "ADM12345"
}
```

**Response:**
```json
{
    "status": 200,
    "message": "Admission Number Updated Successfully."
}
```

### 3. Search Student by Admission Number
**URL:** `/searching`
**Method:** `POST`

**Request Body:**
```json
{
    "admission_no": "1"
}
```

**Response (Success):**
```json
{
    "status": 200,
    "message": "Student found.",
    "data": {
        "studentDetails": {
            "id": "1",
            "firstname": "John",
            "lastname": "Doe",
            "admission_no": "1",
            ...
        },
        "student_session": [
            {
                "id": "1",
                "student_id": "1",
                "class_id": "1",
                "section_id": "1",
                "class": "Class 1",
                "section": "A",
                ...
            }
        ],
        "admission_no": "1"
    }
}
```

**Response (Not Found):**
```json
{
    "status": 404,
    "message": "Student not found."
}
```
