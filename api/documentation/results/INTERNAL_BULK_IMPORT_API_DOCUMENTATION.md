# Internal Bulk Import API Documentation

## Overview
The Internal Bulk Import API allows for bulk importing of internal exam results via CSV files. It provides endpoints to download a sample CSV template and to upload the filled CSV file for processing.

## Base URL
`http://localhost/amt/api/Internalbulkimport_api/`

*Note: Replace `localhost/amt` with your actual domain when deploying to production.*

## Authentication
All requests must include the following headers:
- `Client-Service`: `smartschool`
- `Auth-Key`: `schoolAdmin@`

## Endpoints

### 1. Download Sample CSV File
**Method:** `GET`  
**URL:** `download_sample_file`

**Description:**  
Downloads a sample CSV file with the required headers and dummy data to guide the user on the correct format for import.

**Headers:**
- `admission_no`: Student's Admission Number
- `subject_code`: Subject Code (e.g., 31, 41)
- `marks`: Marks obtained
- `is_absent`: 0 for Present, 1 for Absent

**Request Example:**
```bash
curl -X GET "http://localhost/amt/api/Internalbulkimport_api/download_sample_file" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  --output sample_import.csv
```

**Response:**
- Returns a `.csv` file download.

---

### 2. Import Internal Results
**Method:** `POST`  
**URL:** `import_internal_results`

**Description:**  
Imports internal results from a CSV file. It validates student admission numbers and subject codes before inserting or updating records.

**Request Parameters (Form-Data):**
- `file` (required): The CSV file to upload.
- `result_type_id` (required): ID of the Exam Type (e.g., Mid-1 ID).
- `session_id` (required): Academic Session ID.

**CSV Format Requirements:**
- **Column 1**: `admission_no` (Required)
- **Column 2**: `subject_code` (Required)
- **Column 3**: `marks` (Required)
- **Column 4**: `is_absent` (Optional, default 0)

**Request Example:**
```bash
curl -X POST "http://localhost/amt/api/Internalbulkimport_api/import_internal_results" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -F "file=@/path/to/your/results.csv" \
  -F "result_type_id=3" \
  -F "session_id=20"
```

**Response (Success):**
```json
{
    "status": 200,
    "message": "Import completed.",
    "data": {
        "total_processed": 50,
        "success_count": 48,
        "error_count": 2,
        "errors": [
            "Row 5: Student with Admission No '1005' not found.",
            "Row 12: Subject with Code '99' not found."
        ]
    }
}
```

**Response (Error - Missing File):**
```json
{
    "status": 400,
    "message": "CSV file is required."
}
```

**Response (Error - Invalid Format):**
```json
{
    "status": 400,
    "message": "Invalid CSV format. Required columns: admission_no, subject_code, marks."
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

---

## Integration Example (PHP)

```php
<?php
$base_url = 'http://localhost/amt/api/Internalbulkimport_api/';
$headers = array(
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
);

// 1. Download Sample File
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . 'download_sample_file');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$csv_content = curl_exec($ch);
curl_close($ch);
file_put_contents('sample_import.csv', $csv_content);
echo "Sample file downloaded.\n";

// 2. Import Results
$file_path = realpath('sample_import.csv'); // Use your actual file path
$post_fields = array(
    'file' => new CURLFile($file_path),
    'result_type_id' => '3',
    'session_id' => '20'
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . 'import_internal_results');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);
print_r($result);
?>
```

---

## Integration Example (JavaScript)

```javascript
const baseUrl = 'http://localhost/amt/api/Internalbulkimport_api/';
const headers = {
    'Client-Service': 'smartschool',
    'Auth-Key': 'schoolAdmin@'
};

// 1. Download Sample File
async function downloadSample() {
    const response = await fetch(baseUrl + 'download_sample_file', { headers });
    const blob = await response.blob();
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'sample_import.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

// 2. Import Results
async function importResults(fileInput) {
    const formData = new FormData();
    formData.append('file', fileInput.files[0]);
    formData.append('result_type_id', '3');
    formData.append('session_id', '20');

    try {
        const response = await fetch(baseUrl + 'import_internal_results', {
            method: 'POST',
            headers: headers,
            body: formData
        });
        const result = await response.json();
        console.log(result);
    } catch (error) {
        console.error('Error:', error);
    }
}
```

---

## Notes

1. **Authentication**: All endpoints require valid headers.
2. **File Format**: Only CSV files are supported.
3. **Data Validation**: The API validates `admission_no` against the `students` table and `subject_code` against the `resultsubjects` table.
4. **Upsert Logic**: If a result record already exists for the given student, exam, and subject, it will be **updated**. Otherwise, a new record will be **inserted**.
