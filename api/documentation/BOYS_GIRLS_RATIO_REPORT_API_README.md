# Boys Girls Ratio Report API - README

## 📋 Overview

The **Boys Girls Ratio Report API** provides flexible endpoints for retrieving boys/girls ratio statistics with advanced filtering capabilities. It returns aggregated counts of male and female students grouped by class and section, along with calculated ratios.

### Key Highlights
- ✅ **Aggregated statistics** - Returns counts grouped by class and section
- ✅ **Ratio calculation** - Automatically calculates boys:girls ratio
- ✅ **Summary statistics** - Provides overall totals and ratios
- ✅ **Graceful null/empty handling** - Returns all records when no filters provided
- ✅ **Multi-select support** - Filter by multiple classes or sections
- ✅ **Session-aware** - Automatically uses current session or accepts custom session
- ✅ **RESTful design** - Follows REST API best practices

---

## 🚀 Quick Start

### 1. Basic Request (All Classes & Sections)
```bash
curl -X POST "http://localhost/amt/api/boys-girls-ratio-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

### 2. Filter by Class
```bash
curl -X POST "http://localhost/amt/api/boys-girls-ratio-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": 1}'
```

### 3. Filter by Multiple Classes
```bash
curl -X POST "http://localhost/amt/api/boys-girls-ratio-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": [1, 2, 3]}'
```

---

## ✨ Features

### 1. Aggregated Statistics
Each record includes:
- **total_student**: Total number of students in the class-section
- **male**: Number of male students
- **female**: Number of female students
- **class**: Class name
- **section**: Section name
- **class_id**: Class ID
- **section_id**: Section ID

### 2. Summary Statistics
The API response includes overall summary:
- **total_students**: Total across all filtered records
- **total_boys**: Total male students
- **total_girls**: Total female students
- **boys_girls_ratio**: Calculated ratio in format "1:X"

### 3. Ratio Calculation
The API automatically calculates ratios:
- Format: "1:X" where X is girls per boy
- Example: "1:0.8" means 1 boy for every 0.8 girls
- Handles edge cases: "0:0", "1:0", "0:1"

### 4. Flexible Filtering
Filter by:
- **Class** (single or multiple)
- **Section** (single or multiple)
- **Session** (defaults to current session)

### 5. Graceful Handling
The API intelligently handles:
- Empty request bodies `{}`
- Null values `{"class_id": null}`
- Empty arrays `{"class_id": []}`
- Mixed filters `{"class_id": 1, "section_id": null}`

---

## 📚 API Endpoints

### 1. Filter Boys Girls Ratio Report
**URL:** `POST /api/boys-girls-ratio-report/filter`

**Parameters (all optional):**
- `class_id` - integer or array
- `section_id` - integer or array
- `session_id` - integer

**Response Fields:**
- `status` - 1 for success, 0 for error
- `message` - Status message
- `filters_applied` - Echo of applied filters
- `total_records` - Number of class-section combinations
- `summary` - Overall statistics and ratio
- `data` - Array of records
- `timestamp` - Response timestamp

### 2. List All Boys Girls Ratio Data
**URL:** `POST /api/boys-girls-ratio-report/list`

**Parameters:** None (returns all data for current session)

---

## 🧪 Testing

### Interactive HTML Tester
Open `boys_girls_ratio_report_api_test.html` in your browser for an interactive testing interface.

### Manual Testing with cURL

**Test 1: All Data**
```bash
curl -X POST "http://localhost/amt/api/boys-girls-ratio-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}'
```

**Test 2: Specific Class and Section**
```bash
curl -X POST "http://localhost/amt/api/boys-girls-ratio-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": 1, "section_id": 1}'
```

**Test 3: Multiple Classes**
```bash
curl -X POST "http://localhost/amt/api/boys-girls-ratio-report/filter" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"class_id": [1, 2, 3]}'
```

---

## 💡 Code Examples

### JavaScript
```javascript
async function getBoysGirlsRatioReport(filters = {}) {
  const response = await fetch('http://localhost/amt/api/boys-girls-ratio-report/filter', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Client-Service': 'smartschool',
      'Auth-Key': 'schoolAdmin@'
    },
    body: JSON.stringify(filters)
  });
  return await response.json();
}

// Usage
const data = await getBoysGirlsRatioReport({ class_id: [1, 2] });
console.log('Total Students:', data.summary.total_students);
console.log('Boys:Girls Ratio:', data.summary.boys_girls_ratio);
```

### PHP
```php
function getBoysGirlsRatioReport($filters = []) {
    $ch = curl_init('http://localhost/amt/api/boys-girls-ratio-report/filter');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($filters));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Client-Service: smartschool',
        'Auth-Key: schoolAdmin@'
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

// Usage
$data = getBoysGirlsRatioReport(['class_id' => [1, 2]]);
echo "Total Students: " . $data['summary']['total_students'];
echo "Boys:Girls Ratio: " . $data['summary']['boys_girls_ratio'];
```

### Python
```python
import requests
import json

def get_boys_girls_ratio_report(filters={}):
    url = 'http://localhost/amt/api/boys-girls-ratio-report/filter'
    headers = {
        'Content-Type': 'application/json',
        'Client-Service': 'smartschool',
        'Auth-Key': 'schoolAdmin@'
    }
    response = requests.post(url, headers=headers, json=filters)
    return response.json()

# Usage
data = get_boys_girls_ratio_report({'class_id': [1, 2]})
print(f"Total Students: {data['summary']['total_students']}")
print(f"Boys:Girls Ratio: {data['summary']['boys_girls_ratio']}")
```

---

## 🔧 Troubleshooting

### Common Issues

#### 1. 401 Unauthorized Error
**Solution:** Ensure you're sending the correct headers:
```
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

#### 2. 400 Bad Request
**Solution:** Use POST method, not GET

#### 3. Empty Response
**Solution:**
- Check if students exist in the database
- Verify students.is_active = 'yes'
- Check session_id is correct
- Review application logs

#### 4. Incorrect Ratios
**Solution:**
- Verify gender field values are "Male" or "Female" (case-sensitive)
- Check for NULL or empty gender values
- Ensure students are properly assigned to classes/sections

---

## 📊 Response Example

```json
{
  "status": 1,
  "message": "Boys girls ratio report retrieved successfully",
  "filters_applied": {
    "class_id": [1, 2],
    "section_id": null,
    "session_id": 18
  },
  "total_records": 4,
  "summary": {
    "total_students": 180,
    "total_boys": 100,
    "total_girls": 80,
    "boys_girls_ratio": "1:0.8"
  },
  "data": [
    {
      "total_student": "45",
      "male": "25",
      "female": "20",
      "class": "Class 1",
      "section": "A",
      "class_id": "1",
      "section_id": "1"
    },
    {
      "total_student": "50",
      "male": "28",
      "female": "22",
      "class": "Class 1",
      "section": "B",
      "class_id": "1",
      "section_id": "2"
    }
  ],
  "timestamp": "2025-10-07 10:30:45"
}
```

---

## 📝 Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | 2025-10-07 | Initial release |

---

## 🎓 Related APIs

- **Student Report API**
- **Student Profile Report API**
- **Guardian Report API**
- **Admission Report API**
- **Login Detail Report API**
- **Parent Login Detail Report API**
- **Class Subject Report API**

---

## ✅ Quick Checklist

Before using the API, ensure:
- [ ] Server is running
- [ ] Database is accessible
- [ ] Authentication headers are correct
- [ ] Using POST method
- [ ] Request body is valid JSON
- [ ] Routes are configured
- [ ] Models are loaded correctly
- [ ] Gender field contains "Male" or "Female" values

---

## 🎉 Success!

You're now ready to use the Boys Girls Ratio Report API!

Happy coding! 🚀

