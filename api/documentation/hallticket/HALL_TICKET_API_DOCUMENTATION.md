# Hall Ticket API Documentation

This documentation provides detailed information about the Hall Ticket API endpoints, including request/response formats, parameters, and examples.

## Base URL

All API endpoints are relative to:
```
http://localhost/amt/api/
```

## Authentication

All API requests require the following headers:
```
Client-Service: smartschool
Auth-Key: schoolAdmin@
Content-Type: application/json
```

## Endpoints

### Hall Ticket Templates

#### 1. List Hall Ticket Templates
**Endpoint:** `POST hall_ticket_api/list`

**Description:** Retrieves a list of hall ticket templates with optional filters.

**Request Body:**
```json
{
  "is_active": "yes" // Optional filter
}
```

**Response:**
```json
{
  "status": 1,
  "message": "Hall ticket templates retrieved successfully",
  "total_records": 2,
  "data": [
    {
      "id": "1",
      "halltickect_name": "Mid Term Exam",
      "schoolname": "ABC School",
      "address": "123 Main St",
      "email": "info@abcschool.edu",
      "phone": "123-456-7890",
      "toplefttext": "Roll No: [application_no]",
      "topmiddletext": "",
      "toprighttext": "",
      "bottomlefttext": "Invigilator Signature",
      "bottommiddletext": "",
      "bottomrighttext": "Principal Signature",
      "examheading": "Mid Term Examination 2023",
      "sessionid": "1",
      "is_active": "yes",
      "logo_path": ""
    }
  ]
}
```

#### 2. Get Hall Ticket Template
**Endpoint:** `POST hall_ticket_api/get/{id}`

**Description:** Retrieves detailed information about a specific hall ticket template by its ID.

**Response:**
```json
{
  "status": 1,
  "message": "Hall ticket template retrieved successfully",
  "data": {
    "id": "1",
    "halltickect_name": "Mid Term Exam",
    "schoolname": "ABC School",
    "address": "123 Main St",
    "email": "info@abcschool.edu",
    "phone": "123-456-7890",
    "toplefttext": "Roll No: [application_no]",
    "topmiddletext": "",
    "toprighttext": "",
    "bottomlefttext": "Invigilator Signature",
    "bottommiddletext": "",
    "bottomrighttext": "Principal Signature",
    "examheading": "Mid Term Examination 2023",
    "sessionid": "1",
    "is_active": "yes",
    "logo_path": ""
  }
}
```

#### 3. Create Hall Ticket Template
**Endpoint:** `POST hall_ticket_api/create`

**Description:** Creates a new hall ticket template.

**Request Body:**
```json
{
  "halltickect_name": "Final Exam",
  "schoolname": "ABC School",
  "address": "123 Main St",
  "email": "info@abcschool.edu",
  "phone": "123-456-7890",
  "toplefttext": "Roll No: [application_no]",
  "topmiddletext": "",
  "toprighttext": "",
  "bottomlefttext": "Invigilator Signature",
  "bottommiddletext": "",
  "bottomrighttext": "Principal Signature",
  "examheading": "Final Examination 2023",
  "sessionid": 1,
  "is_active": "yes"
}
```

**Response:**
```json
{
  "status": 1,
  "message": "Hall ticket template created successfully",
  "data": {
    "id": "3",
    "halltickect_name": "Final Exam",
    "schoolname": "ABC School",
    "address": "123 Main St",
    "email": "info@abcschool.edu",
    "phone": "123-456-7890",
    "toplefttext": "Roll No: [application_no]",
    "topmiddletext": "",
    "toprighttext": "",
    "bottomlefttext": "Invigilator Signature",
    "bottommiddletext": "",
    "bottomrighttext": "Principal Signature",
    "examheading": "Final Examination 2023",
    "sessionid": "1",
    "is_active": "yes",
    "logo_path": ""
  }
}
```

#### 4. Update Hall Ticket Template
**Endpoint:** `POST hall_ticket_api/update/{id}`

**Description:** Updates an existing hall ticket template.

**Request Body:**
```json
{
  "halltickect_name": "Updated Final Exam",
  "examheading": "Updated Final Examination 2023"
}
```

**Response:**
```json
{
  "status": 1,
  "message": "Hall ticket template updated successfully",
  "data": {
    "id": "3",
    "halltickect_name": "Updated Final Exam",
    "schoolname": "ABC School",
    "address": "123 Main St",
    "email": "info@abcschool.edu",
    "phone": "123-456-7890",
    "toplefttext": "Roll No: [application_no]",
    "topmiddletext": "",
    "toprighttext": "",
    "bottomlefttext": "Invigilator Signature",
    "bottommiddletext": "",
    "bottomrighttext": "Principal Signature",
    "examheading": "Updated Final Examination 2023",
    "sessionid": "1",
    "is_active": "yes",
    "logo_path": ""
  }
}
```

#### 5. Delete Hall Ticket Template
**Endpoint:** `POST hall_ticket_api/delete/{id}`

**Description:** Deletes a hall ticket template.

**Response:**
```json
{
  "status": 1,
  "message": "Hall ticket template deleted successfully",
  "data": null
}
```

### Subjects

#### 6. List Subjects
**Endpoint:** `POST hall_ticket_api/subjects`

**Description:** Retrieves a list of subjects for hall tickets.

**Response:**
```json
{
  "status": 1,
  "message": "Subjects retrieved successfully",
  "total_records": 3,
  "data": [
    {
      "id": "1",
      "name": "Mathematics",
      "subject_code": "MATH101"
    },
    {
      "id": "2",
      "name": "English",
      "subject_code": "ENG101"
    }
  ]
}
```

#### 7. Create Subject
**Endpoint:** `POST hall_ticket_api/create_subject`

**Description:** Creates a new subject for hall tickets.

**Request Body:**
```json
{
  "name": "Physics",
  "subject_code": "PHY101"
}
```

**Response:**
```json
{
  "status": 1,
  "message": "Subject created successfully",
  "data": {
    "id": "3",
    "name": "Physics",
    "subject_code": "PHY101"
  }
}
```

#### 8. Update Subject
**Endpoint:** `POST hall_ticket_api/update_subject/{id}`

**Description:** Updates an existing subject.

**Request Body:**
```json
{
  "name": "Advanced Physics",
  "subject_code": "PHY201"
}
```

**Response:**
```json
{
  "status": 1,
  "message": "Subject updated successfully",
  "data": {
    "id": "3",
    "name": "Advanced Physics",
    "subject_code": "PHY201"
  }
}
```

#### 9. Delete Subject
**Endpoint:** `POST hall_ticket_api/delete_subject/{id}`

**Description:** Deletes a subject.

**Response:**
```json
{
  "status": 1,
  "message": "Subject deleted successfully",
  "data": null
}
```

### Subject Groups

#### 10. List Subject Groups
**Endpoint:** `POST hall_ticket_api/subject_groups`

**Description:** Retrieves a list of subject groups for hall tickets.

**Response:**
```json
{
  "status": 1,
  "message": "Subject groups retrieved successfully",
  "total_records": 2,
  "data": [
    {
      "id": "1",
      "name": "Science Group"
    },
    {
      "id": "2",
      "name": "Commerce Group"
    }
  ]
}
```

#### 11. Create Subject Group
**Endpoint:** `POST hall_ticket_api/create_subject_group`

**Description:** Creates a new subject group for hall tickets.

**Request Body:**
```json
{
  "name": "Arts Group"
}
```

**Response:**
```json
{
  "status": 1,
  "message": "Subject group created successfully",
  "data": {
    "id": "3",
    "name": "Arts Group"
  }
}
```

#### 12. Update Subject Group
**Endpoint:** `POST hall_ticket_api/update_subject_group/{id}`

**Description:** Updates an existing subject group.

**Request Body:**
```json
{
  "name": "Humanities Group"
}
```

**Response:**
```json
{
  "status": 1,
  "message": "Subject group updated successfully",
  "data": {
    "id": "3",
    "name": "Humanities Group"
  }
}
```

#### 13. Delete Subject Group
**Endpoint:** `POST hall_ticket_api/delete_subject_group/{id}`

**Description:** Deletes a subject group.

**Response:**
```json
{
  "status": 1,
  "message": "Subject group deleted successfully",
  "data": null
}
```

### Subject Combinations

#### 14. List Subject Combinations
**Endpoint:** `POST hall_ticket_api/subject_combinations`

**Description:** Retrieves a list of subject combinations for hall tickets.

**Request Body (Optional):**
```json
{
  "subjectgrp_id": 1 // Filter by subject group ID
}
```

**Response:**
```json
{
  "status": 1,
  "message": "Subject combinations retrieved successfully",
  "total_records": 2,
  "data": [
    {
      "id": "1",
      "subjectgrp_id": "1",
      "subject_id": "1",
      "date": "2023-12-15",
      "starttime": "09:00",
      "endtime": "12:00",
      "maxmark": "100",
      "minmark": "35",
      "is_active": "1",
      "subject_name": "Mathematics",
      "group_name": "Science Group"
    }
  ]
}
```

#### 15. Create Subject Combination
**Endpoint:** `POST hall_ticket_api/create_subject_combination`

**Description:** Creates a new subject combination for hall tickets.

**Request Body:**
```json
{
  "subjectgrp_id": 1,
  "subject_id": 2,
  "date": "2023-12-16",
  "starttime": "14:00",
  "endtime": "17:00",
  "maxmark": 100,
  "minmark": 35,
  "is_active": "1"
}
```

**Response:**
```json
{
  "status": 1,
  "message": "Subject combination created successfully",
  "data": {
    "id": "3",
    "subjectgrp_id": "1",
    "subject_id": "2",
    "date": "2023-12-16",
    "starttime": "14:00",
    "endtime": "17:00",
    "maxmark": "100",
    "minmark": "35",
    "is_active": "1",
    "subject_name": "English",
    "group_name": "Science Group"
  }
}
```

#### 16. Update Subject Combination
**Endpoint:** `POST hall_ticket_api/update_subject_combination/{id}`

**Description:** Updates an existing subject combination.

**Request Body:**
```json
{
  "date": "2023-12-17",
  "starttime": "10:00",
  "endtime": "13:00"
}
```

**Response:**
```json
{
  "status": 1,
  "message": "Subject combination updated successfully",
  "data": {
    "id": "3",
    "subjectgrp_id": "1",
    "subject_id": "2",
    "date": "2023-12-17",
    "starttime": "10:00",
    "endtime": "13:00",
    "maxmark": "100",
    "minmark": "35",
    "is_active": "1",
    "subject_name": "English",
    "group_name": "Science Group"
  }
}
```

#### 17. Delete Subject Combination
**Endpoint:** `POST hall_ticket_api/delete_subject_combination/{id}`

**Description:** Deletes a subject combination.

**Response:**
```json
{
  "status": 1,
  "message": "Subject combination deleted successfully",
  "data": null
}
```

### Hall Ticket Generation

#### 18. Generate Hall Tickets
**Endpoint:** `POST hall_ticket_api/generate`

**Description:** Generates hall tickets for students based on provided criteria.

**Request Body:**
```json
{
  "student_ids": [1, 2, 3],
  "template_id": 1,
  "subjectgrp_id": 1
}
```

**Response:**
```json
{
  "status": 1,
  "message": "Hall tickets generated successfully",
  "total_records": 3,
  "data": [
    {
      "student": {
        "id": "1",
        "admission_no": "STU001",
        "firstname": "John",
        "middlename": "",
        "lastname": "Doe",
        "dob": "2005-05-15",
        "gender": "Male",
        "mobileno": "9876543210",
        "class": "10th Grade",
        "section": "A"
      },
      "template": {
        "id": "1",
        "halltickect_name": "Mid Term Exam",
        "schoolname": "ABC School",
        "address": "123 Main St",
        "email": "info@abcschool.edu",
        "phone": "123-456-7890",
        "toplefttext": "Roll No: [application_no]",
        "topmiddletext": "",
        "toprighttext": "",
        "bottomlefttext": "Invigilator Signature",
        "bottommiddletext": "",
        "bottomrighttext": "Principal Signature",
        "examheading": "Mid Term Examination 2023",
        "sessionid": "1",
        "is_active": "yes",
        "logo_path": ""
      },
      "subjects": [
        {
          "id": "1",
          "subjectgrp_id": "1",
          "subject_id": "1",
          "date": "2023-12-15",
          "starttime": "09:00",
          "endtime": "12:00",
          "maxmark": "100",
          "minmark": "35",
          "is_active": "1",
          "subject_name": "Mathematics",
          "group_name": "Science Group"
        }
      ]
    }
  ]
}
```

## Error Responses

All error responses follow this format:
```json
{
  "status": 0,
  "message": "Error description",
  "data": null
}
```

Common HTTP status codes:
- 200: Success
- 201: Created
- 400: Bad Request
- 401: Unauthorized
- 404: Not Found
- 405: Method Not Allowed
- 500: Internal Server Error

## cURL Examples

### List Hall Ticket Templates
```bash
curl -X POST http://localhost/amt/api/hall_ticket_api/list \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -H "Content-Type: application/json" \
  -d '{}'
```

### Create Hall Ticket Template
```bash
curl -X POST http://localhost/amt/api/hall_ticket_api/create \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -H "Content-Type: application/json" \
  -d '{
    "halltickect_name": "Final Exam",
    "schoolname": "ABC School",
    "address": "123 Main St",
    "email": "info@abcschool.edu",
    "phone": "123-456-7890",
    "examheading": "Final Examination 2023"
  }'
```

### Generate Hall Tickets
```bash
curl -X POST http://localhost/amt/api/hall_ticket_api/generate \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -H "Content-Type: application/json" \
  -d '{
    "student_ids": [1, 2, 3],
    "template_id": 1,
    "subjectgrp_id": 1
  }'
```

## Web Interface Endpoints

### Hall Ticket Management

#### 19. View Hall Ticket Management Page
**Endpoint:** `GET /admin/hallticket`

**Description:** Displays the hall ticket management interface where administrators can view students and assign hall ticket numbers.

**Features:**
- RBAC privilege check for 'add_hallticno' with 'can_view' permission
- Sets menu context for navigation
- Displays class list for filtering
- Shows admission auto-insert setting

#### 20. Search Students for Hall Tickets
**Endpoint:** `POST /admin/hallticket/search`

**Description:** Searches for students based on class, section, and hall ticket status to display in the management interface.

**Form Parameters:**
- `class_id` (required): The class ID to filter students
- `section_id` (optional): The section ID to filter students
- `progress_id` (required): The hall ticket status filter ("withadmissionno" or "noadmissionno")

**Validation Rules:**
- `class_id` and `progress_id` are required fields
- Input is sanitized using `xss_clean`

**Database Operations:**
- Queries student data using `hallticketnostatusgetDatatableByClassSection` method
- Filters based on hall ticket status (1 for students with hall ticket numbers, 0 for students without)

#### 21. Add/Update Student Hall Ticket Number
**Endpoint:** `POST /admin/hallticket/addadmino`

**Description:** Adds or updates hall ticket numbers for students through AJAX requests.

**Form Parameters:**
- `studentid` (required): The student ID to assign the hall ticket number
- `admi_no` (required): The hall ticket number to assign

**Validation Rules:**
- `admi_no` is required and must be unique across all students
- Uses `check_student_admi_no_exists` callback to verify uniqueness
- Input is sanitized using `xss_clean`

**Success Response:**
```json
{
  "status": "success"
}
```

**Error Response:**
```json
{
  "status": "fail",
  "error": {
    "admi_no": "Error message"
  }
}
```

**Database Operations:**
- Checks if student already has a hall ticket record
- Updates existing record or creates new record in `student_hallticket` table
- Fields stored: `std_hallticket`, `hallticket_status`, `admi_no_id`

#### 22. Retrieve Student Hall Ticket Number
**Endpoint:** `POST /admin/hallticket/getadmino`

**Description:** Retrieves the hall ticket number for a specific student through AJAX requests.

**Form Parameters:**
- `studentid` (required): The student ID to retrieve the hall ticket number for

**Success Response:**
```json
{
  "status": "success",
  "admi_no": "HT2023001"
}
```

**Error Response:**
```json
{
  "status": "fail",
  "error_message": "Hall Ticket Number not found."
}
```

**Database Operations:**
- Queries `student_hallticket` table for student's hall ticket number
- Uses `getadmi_no_id` and `gethallticket_no` methods to retrieve data

### Hall Ticket Import/Export

#### 23. Import Hall Ticket Numbers from CSV
**Endpoint:** `POST /admin/hallticket/hallticketimport/import`

**Description:** Imports hall ticket numbers for multiple students from a CSV file. This bulk import feature allows administrators to quickly assign hall ticket numbers to many students at once.

**Form Parameters:**
- `file` (required): CSV file containing hall ticket data

**CSV File Format:**
The CSV file must contain the following columns in order:
1. `hall_no` - The hall ticket number to assign
2. `admi_no` - The student's admission number (used to identify the student)
3. `session` - The session identifier

**Validation Rules:**
- User must have 'hallticketbulkimport' privilege with 'can_view' permission
- File must be a valid CSV format
- Admission number must exist in the system
- Hall ticket number is only assigned if student doesn't already have one

**Success Response:**
- Redirects back to the import page
- Sets a flash message with import statistics (total records found, successfully imported, failed imports)

**Error Response:**
- Redirects back to the import page
- Sets appropriate error message in flash data

**Database Operations:**
- Parses CSV file and validates each row
- Checks if admission number exists using `getadmi_no_id_for_hallticket` method
- Verifies student doesn't already have a hall ticket number using `check_admino_id` method
- Inserts new records into `student_hallticket` table with fields: `admi_no_id`, `std_hallticket`, `hallticket_status`

#### 24. Download Hall Ticket Import Template
**Endpoint:** `GET /admin/hallticket/hallticketimport/exportformat`

**Description:** Provides a downloadable CSV template file that administrators can use as a reference when preparing bulk imports of hall ticket numbers.

**Response:**
- Forces download of `import_student_hallticket_no.csv` template file
- File is located at `./backend/import/import_student_hallticket_no.csv`

**Template File Contents:**
The template contains sample data showing the required CSV format with columns:
- `hall_no` - Hall ticket number
- `admi_no` - Admission number
- `session` - Session identifier

## Implementation Details

### Database Tables Used

1. `halltickect_generation` - Stores hall ticket templates
2. `halltickectsubjects` - Stores subjects
3. `halltickectsubgrp` - Stores subject groups
4. `halltickectsubjectcombo` - Stores subject combinations with exam details

### Model Methods

The `Hall_ticket_model` provides the following methods for data access:
- Template management: `get_templates()`, `get_template()`, `create_template()`, `update_template()`, `delete_template()`
- Subject management: `get_subjects()`, `get_subject()`, `create_subject()`, `update_subject()`, `delete_subject()`
- Subject group management: `get_subject_groups()`, `get_subject_group()`, `create_subject_group()`, `update_subject_group()`, `delete_subject_group()`
- Subject combination management: `get_subject_combinations()`, `get_subject_combination()`, `create_subject_combination()`, `update_subject_combination()`, `delete_subject_combination()`
- Student data retrieval: `get_students_by_ids()`

The `halltickectgeneration_model` (used by web interface) provides additional methods:
- Subject management: `getsubjects()`, `subjectadd()`, `getsub()`, `subremove()`
- Subject group management: `getsubjectgrp()`, `subjectgrpadd()`, `getgrpsub()`, `subgrpremove()`
- Subject combination management: `get_subject_groups()`, `addsubcombo()`, `getsingle()`, `delcomgrp()`, `comboremove()`

### Controller Features

The `Hall_ticket_api` controller implements:
- Full CRUD operations for hall ticket templates, subjects, subject groups, and subject combinations
- Input validation for all endpoints
- Proper error handling with meaningful error messages
- JSON output formatting for all responses
- Authentication header validation
- Transaction management for database operations

The `Halltickectgeneration` controller (web interface) implements:
- Form handling for hall ticket subject, group, and combination management
- Input validation with unique constraint checking for subject codes
- Web interface for creating and managing hall ticket components
- Flash messaging for user feedback
