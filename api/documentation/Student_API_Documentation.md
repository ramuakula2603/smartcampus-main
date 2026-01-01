# Student API Documentation

## Overview

The Student API provides comprehensive functionality for student authentication, profile management, academic operations, and various school-related services in the Smart School Management System. The API supports both student and parent access with role-based permissions.

## Base URL
```
http://{domain}/api/
```

## Important URL Structure

**For Student APIs, use the controller/method pattern:**
- Authentication: `http://{domain}/api/auth/{method}`
- Other endpoints: `http://{domain}/api/webservice/{method}`

**Examples:**
- Login: `http://localhost/amt/api/auth/login`
- Get Profile: `http://localhost/amt/api/webservice/getStudentProfile`
- Dashboard: `http://localhost/amt/api/webservice/dashboard`

## Database Configuration

The API connects to the following database:
- **Database**: `digita90_testschool`
- **Username**: `digita90_digidineuser`
- **Password**: `Neelarani@@10`
- **Host**: `localhost`

## Authentication Headers

All API requests require the following headers:
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

For authenticated endpoints, additional headers may be required:
```
User-ID: {user_id}
Authorization: {token}
```

## Student Authentication Endpoints

### 1. Student Login

**Endpoint:** `POST /auth/login`
**Full URL:** `http://localhost/amt/api/auth/login`

**Description:** Authenticate student or parent users.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Request Body:**
```json
{
  "username": "student_username",
  "password": "student_password",
  "deviceToken": "optional_device_token"
}
```

**Success Response (200):**
```json
{
  "status": 1,
  "message": "Successfully login.",
  "id": 123,
  "token": "authentication_token",
  "role": "student",
  "record": {
    "id": 123,
    "student_id": 456,
    "class_id": 5,
    "section_id": 2,
    "firstname": "John",
    "lastname": "Doe",
    "admission_no": "ADM001",
    "roll_no": "001",
    "email": "john.doe@example.com",
    "mobileno": "1234567890",
    "image": "uploads/student_images/student.jpg"
  }
}
```

**Error Response (401):**
```json
{
  "status": 0,
  "message": "Invalid Username or Password"
}
```

**cURL Example:**
```bash
curl -X POST "http://localhost/amt/api/auth/login" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "username": "student001",
    "password": "password123",
    "deviceToken": "device_token_here"
  }'
```

### 2. Student Logout

**Endpoint:** `POST /webservice/logout`
**Full URL:** `http://localhost/amt/api/webservice/logout`

**Description:** Logout student and invalidate session.

**Request Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
User-ID: {user_id}
Authorization: {token}
```

**Request Body:**
```json
{
  "user_id": 123
}
```

**Success Response (200):**
```json
{
  "status": 1,
  "message": "Successfully logout."
}
```

## Student Profile Endpoints

### 3. Get Student Profile

**Endpoint:** `POST /webservice/getStudentProfile`
**Full URL:** `http://localhost/amt/api/webservice/getStudentProfile`

**Description:** Retrieve comprehensive student profile information.

**Request Body:**
```json
{
  "student_id": 456,
  "user_type": "student"
}
```

**Success Response (200):**
```json
{
  "student_result": {
    "id": 456,
    "admission_no": "ADM001",
    "roll_no": "001",
    "firstname": "John",
    "middlename": "",
    "lastname": "Doe",
    "gender": "Male",
    "dob": "2005-01-15",
    "category": "General",
    "religion": "Christianity",
    "cast": "",
    "mobileno": "1234567890",
    "email": "john.doe@example.com",
    "admission_date": "2020-04-01",
    "blood_group": "O+",
    "height": "5.6",
    "weight": "60",
    "father_name": "Robert Doe",
    "father_phone": "9876543210",
    "father_occupation": "Engineer",
    "mother_name": "Jane Doe",
    "mother_phone": "9876543211",
    "mother_occupation": "Teacher",
    "guardian_name": "Robert Doe",
    "guardian_relation": "Father",
    "guardian_phone": "9876543210",
    "current_address": "123 Main St, City",
    "permanent_address": "123 Main St, City",
    "class": "Class 10",
    "section": "A",
    "session": "2023-24",
    "barcode": "/uploads/student_id_card/barcodes/ADM001.png",
    "qrcode": "/uploads/student_id_card/qrcode/ADM001.png",
    "behaviou_score": 85
  },
  "student_fields": {
    "admission_no": true,
    "roll_no": true,
    "firstname": true,
    "lastname": true,
    "gender": true,
    "dob": true,
    "category": true,
    "religion": true,
    "cast": true,
    "mobileno": true,
    "email": true,
    "current_address": true,
    "permanent_address": true,
    "father_name": true,
    "father_phone": true,
    "mother_name": true,
    "mother_phone": true
  },
  "custom_fields": {
    "emergency_contact": "9999999999",
    "medical_conditions": "None"
  }
}
```

**cURL Example:**
```bash
curl -X POST "http://localhost/amt/api/webservice/getStudentProfile" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "student_id": 456,
    "user_type": "student"
  }'
```

### 4. Update Student Profile

**Endpoint:** `POST /editprofile`

**Description:** Update student profile information.

**Request Body:**
```json
{
  "student_id": 456,
  "firstname": "John",
  "lastname": "Doe",
  "email": "john.doe@example.com",
  "mobileno": "1234567890",
  "current_address": "123 Main St, City",
  "permanent_address": "123 Main St, City",
  "father_name": "Robert Doe",
  "father_phone": "9876543210",
  "mother_name": "Jane Doe",
  "mother_phone": "9876543211"
}
```

**Success Response (200):**
```json
{
  "status": "1",
  "msg": "Success"
}
```

## Academic Endpoints

### 5. Student Dashboard

**Endpoint:** `POST /dashboard`

**Description:** Get student dashboard with attendance, homework, and events.

**Request Body:**
```json
{
  "student_id": 456,
  "date_from": "2023-01-01",
  "date_to": "2023-12-31",
  "role": "student"
}
```

**Success Response (200):**
```json
{
  "attendence_type": 1,
  "class_id": 5,
  "section_id": 2,
  "student_attendence_percentage": 85,
  "student_homework_incomplete": 3,
  "student_incomplete_task": 2,
  "public_events": [
    {
      "id": 1,
      "title": "Annual Sports Day",
      "start_date": "2023-12-15",
      "end_date": "2023-12-15",
      "event_type": "public"
    }
  ]
}
```

### 6. Get Student Subjects

**Endpoint:** `POST /getstudentsubject`

**Description:** Retrieve subjects assigned to student's class and section.

**Request Body:**
```json
{
  "student_id": 456
}
```

**Success Response (200):**
```json
{
  "subjectlist": [
    {
      "id": 1,
      "name": "Mathematics",
      "code": "MATH",
      "type": "Theory"
    },
    {
      "id": 2,
      "name": "Physics",
      "code": "PHY",
      "type": "Theory"
    }
  ],
  "class_id": 5,
  "section_id": 2
}
```

### 7. Get Homework

**Endpoint:** `POST /getHomework`

**Description:** Retrieve homework assignments for student.

**Request Body:**
```json
{
  "student_id": 456,
  "homework_status": "incomplete",
  "subject_group_subject_id": 0
}
```

**Success Response (200):**
```json
{
  "homeworklist": [
    {
      "id": 1,
      "subject_name": "Mathematics",
      "homework_date": "2023-11-01",
      "submit_date": "2023-11-05",
      "description": "Solve exercises 1-10 from chapter 5",
      "document": "homework_doc.pdf",
      "status": "incomplete"
    }
  ]
}
```

### 8. Submit Homework

**Endpoint:** `POST /addaa`

**Description:** Submit homework assignment with optional file attachment.

**Request Body (multipart/form-data):**
```
student_id: 456
homework_id: 1
message: "Completed all exercises"
file: [homework_file.pdf]
```

**Success Response (200):**
```json
{
  "status": "1",
  "msg": "Success"
}
```

## Attendance Endpoints

### 9. Get Attendance Records

**Endpoint:** `POST /getAttendenceRecords`

**Description:** Retrieve student attendance records for a specific period.

**Request Body:**
```json
{
  "year": "2023",
  "month": "11",
  "student_id": 456,
  "date": "2023-11-15"
}
```

**Success Response (200):**
```json
{
  "attendence_type": 1,
  "data": [
    {
      "date": "2023-11-15",
      "attendence_type": "Present",
      "remark": ""
    }
  ]
}
```

## Examination Endpoints

### 10. Get Online Exams

**Endpoint:** `POST /getOnlineExam`

**Description:** Retrieve available online exams for student.

**Request Body:**
```json
{
  "student_id": 456,
  "exam_type": "open"
}
```

**Success Response (200):**
```json
{
  "onlineexam": [
    {
      "id": 1,
      "exam": "Mathematics Quiz",
      "description": "Chapter 5 Quiz",
      "exam_from": "2023-11-20 10:00:00",
      "exam_to": "2023-11-20 11:00:00",
      "duration": "01:00:00",
      "attempt": 1,
      "total_question": 20
    }
  ]
}
```

### 11. Get Exam Questions

**Endpoint:** `POST /getOnlineExamQuestion`

**Description:** Retrieve questions for a specific online exam.

**Request Body:**
```json
{
  "student_id": 456,
  "online_exam_id": 1
}
```

**Success Response (200):**
```json
{
  "exam_result_publish_status": 0,
  "exam_attempt_status": 0,
  "questions": [
    {
      "id": 1,
      "question": "What is 2 + 2?",
      "question_type": "single_choice",
      "opt_a": "3",
      "opt_b": "4",
      "opt_c": "5",
      "opt_d": "6"
    }
  ]
}
```

### 12. Submit Online Exam

**Endpoint:** `POST /saveOnlineExam`

**Description:** Submit answers for online exam.

**Request Body:**
```json
{
  "onlineexam_student_id": 123,
  "questions": [
    {
      "question_id": 1,
      "answer": "opt_b"
    }
  ]
}
```

**Success Response (200):**
```json
{
  "status": 1,
  "msg": "record inserted"
}
```

### 13. Get Exam Results

**Endpoint:** `POST /getOnlineExamResult`

**Description:** Retrieve results for completed online exam.

**Request Body:**
```json
{
  "onlineexam_student_id": 123,
  "exam_id": 1
}
```

**Success Response (200):**
```json
{
  "exam": {
    "exam": "Mathematics Quiz",
    "total_question": 20,
    "correct_ans": 18,
    "wrong_ans": 2,
    "not_attempted": 0,
    "exam_total_marks": 20,
    "exam_total_scored": 18,
    "score": 90,
    "rank": 5
  },
  "question_result": [
    {
      "question": "What is 2 + 2?",
      "answer": "4",
      "correct_answer": "4",
      "marks": 1
    }
  ]
}
```

## Fee Management Endpoints

### 14. Get Student Fees

**Endpoint:** `POST /fees`

**Description:** Retrieve fee structure and payment details for student.

**Request Body:**
```json
{
  "student_id": 456
}
```

**Success Response (200):**
```json
{
  "student_due_fee": [
    {
      "fee_groups_name": "Tuition Fee",
      "type": "Monthly",
      "due_date": "2023-11-30",
      "amount": 5000,
      "amount_discount": 500,
      "amount_fine": 0,
      "amount_paid": 4500,
      "balance": 0
    }
  ],
  "transport_fees": [
    {
      "route_title": "Route A",
      "vehicle_no": "ABC123",
      "pickup_point": "Main Gate",
      "fees": 1000,
      "amount_paid": 1000,
      "balance": 0
    }
  ]
}
```

### 15. Add Offline Payment

**Endpoint:** `POST /addofflinepayment`

**Description:** Submit offline payment details for verification.

**Request Body (multipart/form-data):**
```
payment_type: fees
payment_date: 2023-11-15
student_session_id: 789
fee_groups_feetype_id: 1
student_fees_master_id: 123
bank_account_transferred: Bank Account
amount: 5000
reference: TXN123456
file: [payment_receipt.jpg]
```

**Success Response (200):**
```json
{
  "status": "1",
  "msg": "Success"
}
```

## Leave Management Endpoints

### 16. Get Leave Applications

**Endpoint:** `POST /getApplyLeave`

**Description:** Retrieve student's leave applications.

**Request Body:**
```json
{
  "student_id": 456
}
```

**Success Response (200):**
```json
{
  "result_array": [
    {
      "id": 1,
      "from_date": "2023-11-20",
      "to_date": "2023-11-22",
      "apply_date": "2023-11-15",
      "reason": "Family function",
      "status": "pending",
      "approve_by": "",
      "approve_date": "",
      "docs": "leave_doc.pdf"
    }
  ]
}
```

### 17. Apply for Leave

**Endpoint:** `POST /addLeave`

**Description:** Submit new leave application.

**Request Body (multipart/form-data):**
```
from_date: 2023-11-20
to_date: 2023-11-22
apply_date: 2023-11-15
student_id: 456
reason: Family function
message: Need to attend family wedding
file: [leave_document.pdf]
```

**Success Response (200):**
```json
{
  "status": "1",
  "msg": "Success"
}
```

## Library Endpoints

### 18. Get Library Books

**Endpoint:** `GET /getLibraryBooks`

**Description:** Retrieve available library books.

**Success Response (200):**
```json
{
  "books": [
    {
      "id": 1,
      "book_title": "Mathematics Textbook",
      "book_no": "BOOK001",
      "isbn_no": "978-0123456789",
      "author": "John Smith",
      "publisher": "Education Press",
      "available": "Yes"
    }
  ]
}
```

### 19. Get Issued Books

**Endpoint:** `POST /getLibraryBookIssued`

**Description:** Retrieve books issued to student.

**Request Body:**
```json
{
  "student_id": 456
}
```

**Success Response (200):**
```json
{
  "issued_books": [
    {
      "book_title": "Physics Textbook",
      "book_no": "BOOK002",
      "issue_date": "2023-11-01",
      "return_date": "2023-11-15",
      "status": "issued"
    }
  ]
}
```

## Transport Endpoints

### 20. Get Transport Routes

**Endpoint:** `POST /getTransportroute`

**Description:** Retrieve available transport routes.

**Request Body:**
```json
{
  "student_id": 456
}
```

**Success Response (200):**
```json
{
  "transport_routes": [
    {
      "id": 1,
      "route_title": "Route A",
      "vehicle_no": "ABC123",
      "driver_name": "Driver Name",
      "driver_contact": "9876543210",
      "pickup_points": [
        {
          "pickup_point": "Main Gate",
          "pickup_time": "07:30:00"
        }
      ]
    }
  ]
}
```

### 21. Get Vehicle Details

**Endpoint:** `POST /getTransportVehicleDetails`

**Description:** Get detailed information about assigned transport vehicle.

**Request Body:**
```json
{
  "student_id": 456
}
```

**Success Response (200):**
```json
{
  "vehicle_details": {
    "vehicle_no": "ABC123",
    "vehicle_model": "Bus Model",
    "manufacture_year": "2020",
    "driver_name": "Driver Name",
    "driver_contact": "9876543210",
    "driver_licence": "DL123456789",
    "route_title": "Route A",
    "pickup_point": "Main Gate"
  }
}
```

## Communication Endpoints

### 22. Get Notifications

**Endpoint:** `POST /getNotifications`

**Description:** Retrieve notifications for student.

**Request Body:**
```json
{
  "student_id": 456
}
```

**Success Response (200):**
```json
{
  "notifications": [
    {
      "id": 1,
      "title": "Exam Schedule Released",
      "message": "Mid-term exam schedule has been published",
      "date": "2023-11-15",
      "is_active": "yes"
    }
  ]
}
```

### 23. Search Users (Chat)

**Endpoint:** `POST /searchuser`

**Description:** Search for users to start chat conversation.

**Request Body:**
```json
{
  "search_text": "teacher",
  "user_type": "student"
}
```

**Success Response (200):**
```json
{
  "users": [
    {
      "id": 1,
      "name": "Math Teacher",
      "role": "teacher",
      "image": "uploads/staff_images/teacher.jpg"
    }
  ]
}
```

## Behavior Management Endpoints

### 24. Get Student Behavior Records

**Endpoint:** `POST /getstudentbehaviour`

**Description:** Retrieve student behavior incidents and scores.

**Request Body:**
```json
{
  "student_id": 456
}
```

**Success Response (200):**
```json
{
  "behaviour_settings": {
    "comment_option": "enabled"
  },
  "behaviour_score": 85,
  "assigned_incident": [
    {
      "id": 1,
      "incident_title": "Good Behavior",
      "incident_date": "2023-11-15",
      "point": 5,
      "comment_count": 2
    }
  ]
}
```

### 25. Get Incident Comments

**Endpoint:** `POST /getincidentcomments`

**Description:** Retrieve comments for a specific behavior incident.

**Request Body:**
```json
{
  "student_incident_id": 1
}
```

**Success Response (200):**
```json
{
  "messagelist": [
    {
      "id": 1,
      "comment": "Great improvement in behavior",
      "type": "teacher",
      "firstname": "John",
      "lastname": "Teacher",
      "created_date": "2023-11-15 10:30:00"
    }
  ]
}
```

### 26. Add Incident Comment

**Endpoint:** `POST /addincidentcomments`

**Description:** Add comment to behavior incident.

**Request Body:**
```json
{
  "student_id": 456,
  "student_incident_id": 1,
  "type": "student",
  "comment": "Thank you for the feedback"
}
```

**Success Response (200):**
```json
{
  "status": "1",
  "msg": "Success"
}
```

## System Configuration Endpoints

### 27. Get Module Status

**Endpoint:** `POST /getModuleStatus`

**Description:** Check which modules are enabled for student access.

**Request Body:**
```json
{
  "user": "student"
}
```

**Success Response (200):**
```json
{
  "modules": [
    {
      "name": "Class Timetable",
      "short_code": "class_timetable",
      "status": "1"
    },
    {
      "name": "Attendance",
      "short_code": "attendance",
      "status": "1"
    },
    {
      "name": "Examinations",
      "short_code": "examinations",
      "status": "1"
    }
  ]
}
```

### 28. Check Student Status

**Endpoint:** `POST /checkStudentStatus`

**Description:** Verify if student account is active and accessible.

**Request Body:**
```json
{
  "id": 456,
  "user_type": "student"
}
```

**Success Response (200):**
```json
{
  "response": {
    "status": "active",
    "message": "Student account is active"
  }
}
```

## Error Handling

### Common Error Responses

**400 Bad Request:**
```json
{
  "status": 400,
  "message": "Bad request."
}
```

**401 Unauthorized:**
```json
{
  "status": 0,
  "message": "Unauthorized. Please check Client-Service and Auth-Key headers."
}
```

**Validation Errors:**
```json
{
  "status": "0",
  "error": {
    "student_id": "Student ID is required",
    "from_date": "From date is required"
  }
}
```

## HTTP Status Codes

- **200 OK**: Request successful
- **400 Bad Request**: Invalid request format
- **401 Unauthorized**: Authentication failed
- **404 Not Found**: Endpoint not found
- **500 Internal Server Error**: Server error

## Rate Limiting

The API implements standard rate limiting to prevent abuse. Clients should implement appropriate retry logic with exponential backoff.

## Security Considerations

1. Always use HTTPS in production
2. Store authentication tokens securely
3. Implement proper session management
4. Validate all input data
5. Use prepared statements for database queries
6. Implement proper file upload validation

## Testing

Use the provided cURL examples to test endpoints. For comprehensive testing, consider using tools like Postman or automated testing frameworks.

## Additional Endpoints

### 29. Get Student Documents

**Endpoint:** `POST /getDocument`

**Description:** Retrieve documents uploaded for student.

**Request Body:**
```json
{
  "student_id": 456
}
```

**Success Response (200):**
```json
{
  "documents": [
    {
      "id": 1,
      "title": "Birth Certificate",
      "doc": "birth_cert.pdf",
      "created_at": "2023-11-01"
    }
  ]
}
```

### 30. Upload Document

**Endpoint:** `POST /uploadDocument`

**Description:** Upload document for student.

**Request Body (multipart/form-data):**
```
student_id: 456
title: Medical Certificate
description: Medical fitness certificate
file: [medical_cert.pdf]
```

**Success Response (200):**
```json
{
  "status": "1",
  "msg": "Success"
}
```

### 31. Get Class Timetable

**Endpoint:** `POST /getClassTimetable`

**Description:** Retrieve class timetable for student.

**Request Body:**
```json
{
  "student_id": 456
}
```

**Success Response (200):**
```json
{
  "timetable": [
    {
      "day": "Monday",
      "periods": [
        {
          "time_from": "09:00:00",
          "time_to": "09:45:00",
          "subject": "Mathematics",
          "teacher": "John Teacher",
          "room_no": "101"
        }
      ]
    }
  ]
}
```

### 32. Get Syllabus

**Endpoint:** `POST /getsyllabus`

**Description:** Retrieve syllabus information for student's subjects.

**Request Body:**
```json
{
  "student_id": 456
}
```

**Success Response (200):**
```json
{
  "syllabus": [
    {
      "subject_name": "Mathematics",
      "subject_code": "MATH",
      "syllabus_status": [
        {
          "lesson": "Algebra",
          "topic": "Linear Equations",
          "status": "completed",
          "completion_date": "2023-11-01"
        }
      ]
    }
  ]
}
```

### 33. Get Live Classes

**Endpoint:** `POST /liveclasses`

**Description:** Retrieve scheduled live classes for student.

**Request Body:**
```json
{
  "student_id": 456
}
```

**Success Response (200):**
```json
{
  "live_classes": [
    {
      "id": 1,
      "title": "Mathematics Class",
      "date": "2023-11-20",
      "time_from": "10:00:00",
      "time_to": "11:00:00",
      "meeting_id": "123456789",
      "password": "class123",
      "host_video": 1,
      "participant_video": 0
    }
  ]
}
```

### 34. Get Video Tutorials

**Endpoint:** `POST /getVideoTutorial`

**Description:** Retrieve educational video tutorials.

**Request Body:**
```json
{
  "student_id": 456
}
```

**Success Response (200):**
```json
{
  "videos": [
    {
      "id": 1,
      "title": "Introduction to Algebra",
      "description": "Basic algebra concepts",
      "video_url": "uploads/videos/algebra_intro.mp4",
      "thumbnail": "uploads/thumbnails/algebra.jpg",
      "duration": "15:30",
      "subject": "Mathematics"
    }
  ]
}
```

### 35. Get CBSE Exam Results

**Endpoint:** `POST /cbseexamresult`

**Description:** Retrieve CBSE examination results for student.

**Request Body:**
```json
{
  "student_session_id": 789
}
```

**Success Response (200):**
```json
{
  "exams": [
    {
      "cbse_exam_id": 1,
      "exam_name": "Mid Term Examination",
      "exam_rank": 5,
      "subjects": [
        {
          "subject_name": "Mathematics",
          "subject_code": "MATH",
          "exam_assessments": {
            "1": {
              "cbse_exam_assessment_type_name": "Theory",
              "maximum_marks": 100,
              "marks": 85,
              "is_absent": 0
            }
          }
        }
      ]
    }
  ]
}
```

### 36. Get CBSE Exam Timetable

**Endpoint:** `POST /cbseexamtimetable`

**Description:** Retrieve CBSE examination timetable.

**Request Body:**
```json
{
  "student_session_id": 789
}
```

**Success Response (200):**
```json
{
  "result": [
    {
      "exam_name": "Final Examination",
      "subject_name": "Mathematics",
      "date_from": "2023-12-01",
      "time_from": "09:00:00",
      "time_to": "12:00:00",
      "room_no": "101"
    }
  ]
}
```

### 37. Get Student Timeline

**Endpoint:** `POST /getTimeline`

**Description:** Retrieve student timeline events and activities.

**Request Body:**
```json
{
  "student_id": 456
}
```

**Success Response (200):**
```json
{
  "timeline": [
    {
      "id": 1,
      "title": "Assignment Submitted",
      "date": "2023-11-15",
      "description": "Mathematics assignment submitted successfully",
      "status": "success",
      "visible_student": "yes"
    }
  ]
}
```

### 38. Get Hostel Information

**Endpoint:** `POST /getHostelList`

**Description:** Retrieve hostel information for student.

**Request Body:**
```json
{
  "student_id": 456
}
```

**Success Response (200):**
```json
{
  "hostel_details": {
    "hostel_name": "Boys Hostel A",
    "hostel_type": "Boys",
    "room_no": "201",
    "room_type": "Double Sharing",
    "intake": 2,
    "cost_per_bed": 5000,
    "description": "Well furnished rooms with all amenities"
  }
}
```

### 39. Get Downloads

**Endpoint:** `POST /getDownloadsLinks`

**Description:** Retrieve downloadable content links for student.

**Request Body:**
```json
{
  "student_id": 456
}
```

**Success Response (200):**
```json
{
  "downloads": [
    {
      "id": 1,
      "title": "Study Material - Mathematics",
      "description": "Chapter wise study material",
      "file": "uploads/downloads/math_study_material.pdf",
      "date": "2023-11-01",
      "is_active": "yes"
    }
  ]
}
```

### 40. Get Visitors Information

**Endpoint:** `POST /getVisitors`

**Description:** Retrieve visitor information for student (if applicable).

**Request Body:**
```json
{
  "student_id": 456
}
```

**Success Response (200):**
```json
{
  "visitors": [
    {
      "id": 1,
      "name": "Parent Name",
      "phone": "9876543210",
      "purpose": "Parent-Teacher Meeting",
      "date": "2023-11-15",
      "in_time": "10:00:00",
      "out_time": "11:00:00"
    }
  ]
}
```

## Parent-Specific Endpoints

### 41. Get Parent's Students List

**Endpoint:** `POST /Parent_GetStudentsList`

**Description:** Retrieve list of children for parent user.

**Request Body:**
```json
{
  "parent_id": 123
}
```

**Success Response (200):**
```json
{
  "childs": [
    {
      "student_id": 456,
      "firstname": "John",
      "lastname": "Doe",
      "admission_no": "ADM001",
      "class": "Class 10",
      "section": "A",
      "roll_no": "001",
      "image": "uploads/student_images/john.jpg"
    }
  ]
}
```

## Currency and Language Settings

### 42. Get Student Currency

**Endpoint:** `POST /getStudentCurrency`

**Description:** Get currency settings for student.

**Request Body:**
```json
{
  "student_id": 456
}
```

**Success Response (200):**
```json
{
  "currency_symbol": "$",
  "currency_short_name": "USD",
  "base_price": 1.0
}
```

### 43. Update Student Currency

**Endpoint:** `POST /updatestudentcurrency`

**Description:** Update currency preference for student.

**Request Body:**
```json
{
  "student_id": 456,
  "currency_id": 2
}
```

**Success Response (200):**
```json
{
  "status": "1",
  "msg": "Success"
}
```

### 44. Get Student Language

**Endpoint:** `POST /getstudentcurrentlanguage`

**Description:** Get current language setting for student.

**Request Body:**
```json
{
  "student_id": 456
}
```

**Success Response (200):**
```json
{
  "language": "english",
  "language_id": 1
}
```

### 45. Update Student Language

**Endpoint:** `POST /updatestudentlanguage`

**Description:** Update language preference for student.

**Request Body:**
```json
{
  "student_id": 456,
  "language_id": 2
}
```

**Success Response (200):**
```json
{
  "status": "1",
  "msg": "Success"
}
```

## Complete Endpoint Summary

| Method | Endpoint | Full URL | Description | Authentication |
|--------|----------|----------|-------------|----------------|
| POST | `/auth/login` | `{base_url}/auth/login` | Student/Parent authentication | None |
| POST | `/webservice/logout` | `{base_url}/webservice/logout` | Logout user | Required |
| POST | `/webservice/getStudentProfile` | `{base_url}/webservice/getStudentProfile` | Get student profile | Required |
| POST | `/webservice/editprofile` | `{base_url}/webservice/editprofile` | Update student profile | Required |
| POST | `/webservice/dashboard` | `{base_url}/webservice/dashboard` | Get dashboard data | Required |
| POST | `/webservice/getstudentsubject` | `{base_url}/webservice/getstudentsubject` | Get student subjects | Required |
| POST | `/webservice/getHomework` | `{base_url}/webservice/getHomework` | Get homework assignments | Required |
| POST | `/webservice/addaa` | `{base_url}/webservice/addaa` | Submit homework | Required |
| POST | `/webservice/getAttendenceRecords` | `{base_url}/webservice/getAttendenceRecords` | Get attendance records | Required |
| POST | `/webservice/getOnlineExam` | `{base_url}/webservice/getOnlineExam` | Get online exams | Required |
| POST | `/webservice/getOnlineExamQuestion` | `{base_url}/webservice/getOnlineExamQuestion` | Get exam questions | Required |
| POST | `/webservice/saveOnlineExam` | `{base_url}/webservice/saveOnlineExam` | Submit exam answers | Required |
| POST | `/webservice/getOnlineExamResult` | `{base_url}/webservice/getOnlineExamResult` | Get exam results | Required |
| POST | `/webservice/fees` | `{base_url}/webservice/fees` | Get fee information | Required |
| POST | `/webservice/addofflinepayment` | `{base_url}/webservice/addofflinepayment` | Submit offline payment | Required |
| POST | `/webservice/getApplyLeave` | `{base_url}/webservice/getApplyLeave` | Get leave applications | Required |
| POST | `/webservice/addLeave` | `{base_url}/webservice/addLeave` | Apply for leave | Required |
| GET | `/webservice/getLibraryBooks` | `{base_url}/webservice/getLibraryBooks` | Get library books | Required |
| POST | `/webservice/getLibraryBookIssued` | `{base_url}/webservice/getLibraryBookIssued` | Get issued books | Required |
| POST | `/webservice/getTransportroute` | `{base_url}/webservice/getTransportroute` | Get transport routes | Required |
| POST | `/webservice/getTransportVehicleDetails` | `{base_url}/webservice/getTransportVehicleDetails` | Get vehicle details | Required |
| POST | `/webservice/getNotifications` | `{base_url}/webservice/getNotifications` | Get notifications | Required |
| POST | `/webservice/searchuser` | `{base_url}/webservice/searchuser` | Search users for chat | Required |
| POST | `/webservice/getstudentbehaviour` | `{base_url}/webservice/getstudentbehaviour` | Get behavior records | Required |
| POST | `/webservice/getincidentcomments` | `{base_url}/webservice/getincidentcomments` | Get incident comments | Required |
| POST | `/webservice/addincidentcomments` | `{base_url}/webservice/addincidentcomments` | Add incident comment | Required |
| POST | `/webservice/getModuleStatus` | `{base_url}/webservice/getModuleStatus` | Get module status | Required |
| POST | `/webservice/checkStudentStatus` | `{base_url}/webservice/checkStudentStatus` | Check student status | Required |

**Where `{base_url}` = `http://localhost/amt/api` (adjust domain as needed)**

## Troubleshooting 404 Errors

If you're getting "404 Page Not Found" errors in Postman, check the following:

### 1. Correct URL Structure
**❌ Wrong:**
```
http://localhost/amt/api/login
http://localhost/amt/api/getStudentProfile
```

**✅ Correct:**
```
http://localhost/amt/api/auth/login
http://localhost/amt/api/webservice/getStudentProfile
```

### 2. Required Headers
Make sure you include these headers in ALL requests:
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

### 3. Request Method
Most endpoints require **POST** method, not GET.

### 4. Server Configuration
Ensure your server has:
- PHP 7.0+ installed
- mod_rewrite enabled (for Apache)
- Proper .htaccess configuration

### 5. Test Basic Connectivity
First test if the API is accessible:
```bash
curl -X GET "http://localhost/amt/api/"
```

### 6. Common Postman Setup

**Step 1: Set Base URL**
- Create environment variable: `base_url = http://localhost/amt/api`

**Step 2: Set Headers**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Step 3: Test Login First**
```
POST {{base_url}}/auth/login
Body (raw JSON):
{
  "username": "your_student_username",
  "password": "your_password",
  "deviceToken": "test_token"
}
```

**Step 4: Test Profile (after successful login)**
```
POST {{base_url}}/webservice/getStudentProfile
Body (raw JSON):
{
  "student_id": 1,
  "user_type": "student"
}
```

### 7. Debug Steps

1. **Check if CodeIgniter is working:**
   - Visit: `http://localhost/amt/api/`
   - Should show CodeIgniter welcome page or custom response

2. **Check controller exists:**
   - Verify `api/application/controllers/Auth.php` exists
   - Verify `api/application/controllers/Webservice.php` exists

3. **Check method exists:**
   - Open the controller file
   - Verify the method name matches exactly (case-sensitive)

4. **Check database connection:**
   - Verify database credentials in `api/application/config/database.php`

### 8. Working Test Examples

**Login Test:**
```bash
curl -X POST "http://localhost/amt/api/auth/login" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "username": "student001",
    "password": "password123",
    "deviceToken": "test_device"
  }'
```

**Profile Test:**
```bash
curl -X POST "http://localhost/amt/api/webservice/getStudentProfile" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{
    "student_id": 1,
    "user_type": "student"
  }'
```

## Support

For API support and questions, contact the development team or refer to the main project documentation.

## Quick Reference

**Base URLs:**
- Authentication: `http://localhost/amt/api/auth/{method}`
- All other endpoints: `http://localhost/amt/api/webservice/{method}`

**Required Headers:**
```
Content-Type: application/json
Client-Service: smartschool
Auth-Key: schoolAdmin@
```

**Test Credentials (if available):**
- Username: `student001` or check your database
- Password: Check your database or ask admin
