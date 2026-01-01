# Admino Import API Documentation

## Overview
The Admino Import API allows for the bulk import of student admission numbers using a CSV file.

## Base URL
`http://your-domain/api/Adminoimport_api/`

## Authentication
All requests must include the following headers:
- `Client-Service`: `smartschool`
- `Auth-Key`: `schoolAdmin@`

## Endpoints

### 1. Import Student Admission Numbers
**Method:** `POST`
**URL:** `import`

**Description:**
Imports student admission numbers from a CSV file. The CSV file must contain `admi_no`, `admission_no`, and `session` columns.

**Request Body (Multipart/Form-Data):**
- `file`: The CSV file to upload.

**CSV Format:**
The CSV file should have the following headers:
- `admi_no`: The new admission number to assign.
- `admission_no`: The student's current application/admission number (used to identify the student).
- `session`: The academic session (e.g., "2025-26").

**Example CSV Content:**
```csv
admi_no,admission_no,session
ADM001,1001,2025-26
ADM002,1002,2025-26
```

**Response:**

**Success (200 OK):**
```json
{
    "status": 200,
    "message": "Import successful.",
    "records_imported": 2
}
```

**Error (400 Bad Request) - No Records:**
```json
{
    "status": 400,
    "message": "No records found in CSV file."
}
```

**Error (400 Bad Request) - Invalid File Type:**
```json
{
    "status": 400,
    "message": "Please upload CSV file only."
}
```

**Error (401 Unauthorized):**
```json
{
    "status": 401,
    "message": "Client Service or Auth Key is invalid."
}
```
