# Internal Result Report API Documentation

## Overview
This API provides access to the Internal Results Report, allowing retrieval of student results based on various filters such as session, exam type, class, section, and status.

## Base URL
`http://localhost/amt/api/`

## Authentication
All API requests require the following headers:
- `Client-Service`: `smartschool`
- `Auth-Key`: `schoolAdmin@`

---

## Endpoints

### 1. Get Filter Options
Retrieves all available options for filtering the report (sessions, exam types, classes, sections, statuses).

- **URL**: `/internal-result-report/filter-options`
- **Method**: `GET`
- **Success Response**:
  - **Code**: 200 OK
  - **Content**:
    ```json
    {
        "status": 200,
        "message": "Success",
        "data": {
            "sessions": [
                { "id": "1", "session": "2023-24" }
            ],
            "exam_types": [
                { "id": "1", "examtype": "Mid Term" }
            ],
            "classes": [
                { "id": "1", "class": "Class 1" }
            ],
            "sections": [
                { "id": "1", "section": "A" }
            ],
            "statuses": [
                { "id": "all", "name": "All" },
                { "id": "pass", "name": "Pass" },
                { "id": "fail", "name": "Fail" },
                { "id": "absent", "name": "Absent" }
            ]
        }
    }
    ```

### 2. Get Internal Result Report
Retrieves the internal result report based on provided filters.

- **URL**: `/internal-result-report/get-report`
- **Method**: `POST`
- **Body Parameters**:
  - `session_id` (optional): Array of Session IDs or single ID
  - `exam_type_id` (optional): Array of Exam Type IDs or single ID
  - `class_id` (optional): Array of Class IDs or single ID
  - `section_id` (optional): Array of Section IDs or single ID
  - `status` (optional): Filter by status ('all', 'pass', 'fail', 'absent')

- **Request Example**:
  ```json
  {
      "session_id": ["1"],
      "exam_type_id": ["1"],
      "class_id": ["1"],
      "section_id": ["1"],
      "status": "all"
  }
  ```

- **Success Response**:
  - **Code**: 200 OK
  - **Content**:
    ```json
    {
        "status": 200,
        "message": "Success",
        "data": {
            "report": [
                {
                    "student_id": "1",
                    "student_name": "John Doe",
                    "admission_no": "1001",
                    "class": "Class 1",
                    "section": "A",
                    "sessions": {
                        "1": {
                            "session_id": "1",
                            "session_name": "2023-24",
                            "exams": {
                                "1": {
                                    "exam_id": "1",
                                    "exam_name": "Mid Term",
                                    "subjects": [
                                        {
                                            "subject_name": "Mathematics",
                                            "subject_code": "MATH101",
                                            "minmarks": "35",
                                            "maxmarks": "100",
                                            "actualmarks": "85",
                                            "is_absent": false,
                                            "pass": true
                                        }
                                    ],
                                    "total_marks": 85,
                                    "total_max_marks": 100,
                                    "percentage": 85,
                                    "pass_status": true
                                }
                            }
                        }
                    }
                }
            ],
            "count": 1
        }
    }
    ```

---

## Integration Examples

### PHP (cURL)
```php
<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'http://localhost/amt/api/internal-result-report/get-report',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{
    "session_id": ["1"],
    "exam_type_id": ["1"],
    "class_id": ["1"],
    "section_id": ["1"],
    "status": "all"
}',
  CURLOPT_HTTPHEADER => array(
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@',
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
```

### JavaScript (Fetch)
```javascript
var myHeaders = new Headers();
myHeaders.append("Client-Service", "smartschool");
myHeaders.append("Auth-Key", "schoolAdmin@");
myHeaders.append("Content-Type", "application/json");

var raw = JSON.stringify({
  "session_id": ["1"],
  "exam_type_id": ["1"],
  "class_id": ["1"],
  "section_id": ["1"],
  "status": "all"
});

var requestOptions = {
  method: 'POST',
  headers: myHeaders,
  body: raw,
  redirect: 'follow'
};

fetch("http://localhost/amt/api/internal-result-report/get-report", requestOptions)
  .then(response => response.text())
  .then(result => console.log(result))
  .catch(error => console.log('error', error));
```
