# External Bulk Import API Documentation

## Overview
The External Bulk Import API allows for bulk importing of external exam results via CSV files. Unlike internal results, this API identifies students using their **Hall Ticket Number** (`hall_no`) and supports dynamic columns for subject marks.

## Base URL
`http://localhost/amt/api/Externalbulkimport_api/`

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
Downloads a sample CSV file with the required `hall_no` header and placeholder subject columns.

**Headers:**
- `hall_no`: Student's Hall Ticket Number (Required)
- `subject_code_X`: Placeholder for Subject Codes (Replace with actual Subject Codes or IDs)

**Request Example:**
```bash
curl -X GET "http://localhost/amt/api/Externalbulkimport_api/download_sample_file" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  --output sample_external_import.csv
```

**Response:**
- Returns a `.csv` file download.

---

### 2. Import External Results
**Method:** `POST`  
**URL:** `import_external_results`

**Description:**  
Imports external results from a CSV file. It validates student hall ticket numbers and maps dynamic CSV columns to subject marks.

**Request Parameters (Form-Data):**
- `file` (required): The CSV file to upload.
- `exam_id` (required): ID of the External Exam Type.
- `academic_id` (required): Academic Year ID.

**CSV Format Requirements:**
- **Required Column**: `hall_no` (Must match a valid student's hall ticket number)
- **Dynamic Columns**: Headers must match either the **Subject Code** (e.g., `MATHS-1A`) or **Subject ID** (e.g., `31`) configured for the selected Exam Type.
- **Values**: Marks obtained (or 'AB' for absent, though handled as string).

**Example CSV Content:**
```csv
hall_no,MATHS-1A,PHYSICS,CHEMISTRY
HT1001,85,90,75
HT1002,90,88,92
HT1003,AB,70,80
```

**Request Example:**
```bash
curl -X POST "http://localhost/amt/api/Externalbulkimport_api/import_external_results" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -F "file=@/path/to/your/external_results.csv" \
  -F "exam_id=5" \
  -F "academic_id=20"
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
            "Row 5: Student with Hall Ticket 'HT9999' not found.",
            "Row 12: Missing Hall Ticket Number."
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
    "message": "Invalid CSV format. \"hall_no\" column is required."
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
$base_url = 'http://localhost/amt/api/Externalbulkimport_api/';
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
file_put_contents('sample_external_import.csv', $csv_content);
echo "Sample file downloaded.\n";

// 2. Import Results
$file_path = realpath('sample_external_import.csv'); // Use your actual file path
$post_fields = array(
    'file' => new CURLFile($file_path),
    'exam_id' => '5',
    'academic_id' => '20'
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . 'import_external_results');
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
const baseUrl = 'http://localhost/amt/api/Externalbulkimport_api/';
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
    a.download = 'sample_external_import.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

// 2. Import Results
async function importResults(fileInput) {
    const formData = new FormData();
    formData.append('file', fileInput.files[0]);
    formData.append('exam_id', '5');
    formData.append('academic_id', '20');

    try {
        const response = await fetch(baseUrl + 'import_external_results', {
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
3. **Identification**: Students are identified by `hall_no` (Hall Ticket Number).
4. **Dynamic Columns**: The API automatically maps CSV columns to subjects. Ensure your CSV headers match the **Subject Codes** or **Subject IDs** exactly.
5. **Upsert Logic**: Existing marks for the same student, exam, and subject will be updated.
