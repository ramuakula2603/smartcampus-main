<?php
/**
 * Biometric Attendance Controller
 * 
 * Complete rewrite based on reference implementation: adms-server-ZKTeco/app/Http/Controllers/AttendanceController.php
 * This is a SIMPLIFIED version that matches the reference EXACTLY
 * 
 * Endpoints:
 * - GET  /iclock/cdata - Device handshake
 * - POST /iclock/cdata - Receive attendance data
 * 
 * Key Features:
 * - Direct database INSERT (no complex models)
 * - Minimal logging
 * - Simple error handling
 * - Matches reference protocol exactly
 */

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Biometric extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        // CRITICAL: Disable session for biometric device communication
        // ZKTeco devices don't handle cookies/sessions and may reject responses with Set-Cookie headers
        // This matches the reference Laravel implementation which has NO middleware
        $this->config->set_item('sess_driver', 'none');

        $this->load->database();
    }

    /**
     * Main entry point - routes GET/POST requests
     */
    public function index()
    {
        $request_method = $this->input->server('REQUEST_METHOD');

        if ($request_method === 'GET') {
            return $this->handshake();
        } elseif ($request_method === 'POST') {
            return $this->store();
        } else {
            // Clean response without extra headers
            $this->output->set_status_header(405);
            $this->output->set_content_type('text/plain', 'UTF-8');
            $this->cleanHeaders();
            $this->output->set_output('ERROR: Method not allowed');
            return;
        }
    }

    /**
     * Clean unnecessary headers that might interfere with device communication
     * ZKTeco devices expect minimal HTTP headers - just Date, Content-Type, Content-Length
     */
    private function cleanHeaders()
    {
        // Remove cache control headers
        header_remove('Expires');
        header_remove('Cache-Control');
        header_remove('Pragma');

        // Remove session cookie
        header_remove('Set-Cookie');

        // Remove framework headers
        header_remove('X-Powered-By');
    }

    /**
     * Device handshake endpoint
     * GET /iclock/cdata?SN={device_serial}&options=all
     *
     * EXACT MATCH with reference implementation
     * Returns clean response with minimal headers
     */
    private function handshake()
    {
        $device_sn = $this->input->get('SN');

        $response = "GET OPTION FROM: {$device_sn}\r\n" .
                   "Stamp=9999\r\n" .
                   "OpStamp=" . time() . "\r\n" .
                   "ErrorDelay=60\r\n" .
                   "Delay=30\r\n" .
                   "ResLogDay=18250\r\n" .
                   "ResLogDelCount=10000\r\n" .
                   "ResLogCount=50000\r\n" .
                   "TransTimes=00:00;14:05\r\n" .
                   "TransInterval=1\r\n" .
                   "TransFlag=1111000000\r\n" .
                   "Realtime=1\r\n" .
                   "Encrypt=0";

        // Set clean headers matching reference implementation
        $this->output->set_status_header(200);
        $this->output->set_content_type('text/plain', 'UTF-8');
        $this->cleanHeaders();
        $this->output->set_output($response);
        return;
    }

    /**
     * Receive attendance records from biometric devices
     * POST /iclock/cdata?SN={device_serial}&table=ATTLOG&Stamp={stamp}
     * 
     * EXACT MATCH with reference implementation
     * Reference: adms-server-ZKTeco/app/Http/Controllers/AttendanceController.php (lines 72-139)
     */
    private function store()
    {
        try {
            // Get raw POST body
            $raw_body = file_get_contents('php://input');

            // Validate we have data
            if (empty($raw_body)) {
                $this->output->set_status_header(200);
                $this->output->set_content_type('text/plain', 'UTF-8');
                $this->cleanHeaders();
                $this->output->set_output("OK: 0");
                return;
            }

            // Split by multiple line endings (EXACT match with reference)
            $arr = preg_split('/\\r\\n|\\r|,|\\n/', $raw_body);
            $tot = 0;

            // Get query parameters
            $device_sn = $this->input->get('SN');
            $table = $this->input->get('table');
            $stamp = $this->input->get('Stamp');

            // Ignore operation logs (EXACT match with reference)
            if ($table == "OPERLOG") {
                foreach ($arr as $rey) {
                    if (isset($rey)) {
                        $tot++;
                    }
                }
                $this->output->set_status_header(200);
                $this->output->set_content_type('text/plain', 'UTF-8');
                $this->cleanHeaders();
                $this->output->set_output("OK: " . $tot);
                return;
            }

            // Process attendance records - INSERT NEW RECORD FOR EACH PUNCH
            foreach ($arr as $rey) {
                if (empty($rey)) {
                    continue;
                }

                // Parse tab-separated values
                $data = explode("\t", $rey);

                // Validate data array has minimum required fields
                if (!isset($data[0]) || !isset($data[1]) || empty($data[0]) || empty($data[1])) {
                    continue; // Skip malformed records
                }

                $user_id = $data[0]; // This is the database ID (staff.id or students.id)
                $timestamp = $data[1];
                $date = date('Y-m-d', strtotime($timestamp));

                // Build biometric_device_data JSON
                $biometric_device_data = json_encode([
                    'sn' => $device_sn,
                    'table' => $table,
                    'stamp' => $stamp,
                    'timestamp' => $timestamp,
                    'status1' => isset($data[2]) && $data[2] !== '' ? (int)$data[2] : null,
                    'status2' => isset($data[3]) && $data[3] !== '' ? (int)$data[3] : null,
                    'status3' => isset($data[4]) && $data[4] !== '' ? (int)$data[4] : null,
                    'status4' => isset($data[5]) && $data[5] !== '' ? (int)$data[5] : null,
                    'status5' => isset($data[6]) && $data[6] !== '' ? (int)$data[6] : null,
                ]);

                // Check if user_id belongs to staff or student
                $user_type = $this->identifyUserType($user_id);

                if ($user_type === 'staff') {
                    // Insert into staff_attendance table
                    $this->insertStaffAttendance($user_id, $date, $timestamp, $biometric_device_data);
                    $tot++;
                } elseif ($user_type === 'student') {
                    // Insert into student_attendences table
                    $this->insertStudentAttendance($user_id, $date, $timestamp, $biometric_device_data);
                    $tot++;
                }
                // If user not found in either table, skip silently
            }

            // Return success response with clean headers
            $this->output->set_status_header(200);
            $this->output->set_content_type('text/plain', 'UTF-8');
            $this->cleanHeaders();
            $this->output->set_output("OK: " . $tot);
            return;

        } catch (Exception $e) {
            // No logging - return error response silently with clean headers
            $this->output->set_status_header(500);
            $this->output->set_content_type('text/plain', 'UTF-8');
            $this->cleanHeaders();
            $this->output->set_output("ERROR: 0\n");
            return;
        }
    }

    /**
     * Identify if user_id belongs to staff or student
     *
     * @param string $user_id The ID from biometric device
     * @return string 'staff', 'student', or null
     */
    private function identifyUserType($user_id)
    {
        // Check if ID exists in staff table
        $this->db->select('id');
        $this->db->from('staff');
        $this->db->where('id', $user_id);
        $this->db->where('is_active', 1);
        $staff = $this->db->get()->row();

        if ($staff) {
            return 'staff';
        }

        // Check if ID exists in students table
        $this->db->select('id');
        $this->db->from('students');
        $this->db->where('id', $user_id);
        $this->db->where('is_active', 'yes');
        $student = $this->db->get()->row();

        if ($student) {
            return 'student';
        }

        return null;
    }

    /**
     * Insert staff attendance record
     *
     * @param int $staff_id Staff ID
     * @param string $date Date in Y-m-d format
     * @param string $timestamp Full timestamp
     * @param string $biometric_device_data JSON device data
     */
    private function insertStaffAttendance($staff_id, $date, $timestamp, $biometric_device_data)
    {
        try {
            $attendanceData = [
                'date' => $date,
                'staff_id' => $staff_id,
                'staff_attendance_type_id' => 1,
                'biometric_attendence' => 1,
                'is_authorized_range' => 1,
                'biometric_device_data' => $biometric_device_data,
                'remark' => 'Auto-recorded from biometric device at ' . $timestamp,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s', strtotime($timestamp)),
                'updated_at' => date('Y-m-d', strtotime($timestamp)),
            ];

            $result = $this->db->insert('staff_attendance', $attendanceData);

            // Check if insert was successful
            // Note: We check the result of insert() rather than affected_rows()
            // because affected_rows() can be unreliable for INSERTs in some configurations
            if (!$result) {
                // Log error if logging is enabled
                log_message('error', 'Failed to insert staff attendance for staff_id: ' . $staff_id);
                log_message('error', 'Database error: ' . $this->db->error()['message']);
                return false;
            }

            return true;
        } catch (Exception $e) {
            // Log error if logging is enabled
            log_message('error', 'Exception in insertStaffAttendance: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Insert student attendance record
     *
     * @param int $student_id Student ID
     * @param string $date Date in Y-m-d format
     * @param string $timestamp Full timestamp
     * @param string $biometric_device_data JSON device data
     */
    private function insertStudentAttendance($student_id, $date, $timestamp, $biometric_device_data)
    {
        try {
            // Get active student_session_id for this student
            $this->db->select('student_session.id as student_session_id');
            $this->db->from('student_session');
            $this->db->join('sessions', 'sessions.id = student_session.session_id');
            $this->db->where('student_session.student_id', $student_id);
            $this->db->where('sessions.is_active', 'yes');
            $this->db->limit(1);
            $session = $this->db->get()->row();

            if (!$session) {
                // No active session found, skip
                log_message('debug', 'No active session found for student_id: ' . $student_id);
                return false;
            }

            $attendanceData = [
                'student_session_id' => $session->student_session_id,
                'biometric_attendence' => 1,
                'date' => $date,
                'attendence_type_id' => 1, // 1 = Present
                'remark' => 'Auto-recorded from biometric device at ' . $timestamp,
                'biometric_device_data' => $biometric_device_data,
                'is_authorized_range' => 1,
                'is_active' => 'yes',
                'created_at' => date('Y-m-d H:i:s', strtotime($timestamp)),
                'updated_at' => date('Y-m-d H:i:s', strtotime($timestamp)),
            ];

            $result = $this->db->insert('student_attendences', $attendanceData);

            // Check if insert was successful
            // Note: We check the result of insert() rather than affected_rows()
            // because affected_rows() can be unreliable for INSERTs in some configurations
            if (!$result) {
                log_message('error', 'Failed to insert student attendance for student_id: ' . $student_id);
                log_message('error', 'Database error: ' . $this->db->error()['message']);
                return false;
            }

            return true;
        } catch (Exception $e) {
            log_message('error', 'Exception in insertStudentAttendance: ' . $e->getMessage());
            return false;
        }
    }
}
