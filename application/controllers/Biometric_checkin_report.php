<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Biometric Check-in Report Controller
 * Displays daily check-in/check-out tracking for students and staff
 */
class Biometric_checkin_report extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('biometric_checkin_model');
        $this->load->model('class_model');
        $this->load->model('section_model');
        $this->load->model('setting_model');
    }

    /**
     * Main index page - shows check-in summary
     */
    public function index()
    {
        if (!$this->rbac->hasPrivilege('biometric_checkin_report', 'can_view')) {
            access_denied();
        }

        $data['title'] = 'Biometric Check-in Report';
        $data['classlist'] = $this->class_model->get();
        $data['date'] = date('Y-m-d');
        
        // Get statistics for today
        $data['statistics'] = $this->biometric_checkin_model->getCheckinStatistics($data['date']);

        $this->load->view('layout/header', $data);
        $this->load->view('biometric_checkin/index', $data);
        $this->load->view('layout/footer', $data);
    }

    /**
     * Get staff check-in report
     */
    public function staff_checkin()
    {
        if (!$this->rbac->hasPrivilege('biometric_checkin_report', 'can_view')) {
            access_denied();
        }

        $date = $this->input->post('date') ?: date('Y-m-d');
        
        $data['title'] = 'Staff Check-in Report';
        $data['date'] = $date;
        $data['staff_list'] = $this->biometric_checkin_model->getStaffCheckinSummary($date);
        $data['statistics'] = $this->biometric_checkin_model->getCheckinStatistics($date);

        $this->load->view('layout/header', $data);
        $this->load->view('biometric_checkin/staff_checkin', $data);
        $this->load->view('layout/footer', $data);
    }

    /**
     * Get student check-in report
     */
    public function student_checkin()
    {
        if (!$this->rbac->hasPrivilege('biometric_checkin_report', 'can_view')) {
            access_denied();
        }

        $date = $this->input->post('date') ?: date('Y-m-d');
        $class_id = $this->input->post('class_id');
        $section_id = $this->input->post('section_id');
        
        $data['title'] = 'Student Check-in Report';
        $data['date'] = $date;
        $data['class_id'] = $class_id;
        $data['section_id'] = $section_id;
        $data['classlist'] = $this->class_model->get();
        $data['student_list'] = $this->biometric_checkin_model->getStudentCheckinSummary($date, $class_id, $section_id);
        $data['statistics'] = $this->biometric_checkin_model->getCheckinStatistics($date);

        $this->load->view('layout/header', $data);
        $this->load->view('biometric_checkin/student_checkin', $data);
        $this->load->view('layout/footer', $data);
    }

    /**
     * AJAX: Get sections by class
     */
    public function getSectionByClass()
    {
        $class_id = $this->input->post('class_id');
        $sections = $this->section_model->getSectionByClass($class_id);
        echo json_encode($sections);
    }

    /**
     * AJAX: Get check-in details for a person
     */
    public function getCheckinDetails()
    {
        $id = $this->input->post('id');
        $type = $this->input->post('type'); // 'staff' or 'student'
        $date = $this->input->post('date') ?: date('Y-m-d');

        $details = $this->biometric_checkin_model->getPersonCheckinDetails($id, $type, $date);
        
        echo json_encode(array(
            'status' => 200,
            'data' => $details
        ));
    }

    /**
     * Export staff check-in report to Excel
     */
    public function export_staff_excel()
    {
        if (!$this->rbac->hasPrivilege('biometric_checkin_report', 'can_view')) {
            access_denied();
        }

        $date = $this->input->get('date') ?: date('Y-m-d');
        $staff_list = $this->biometric_checkin_model->getStaffCheckinSummary($date);
        $statistics = $this->biometric_checkin_model->getCheckinStatistics($date);

        // Generate filename
        $filename = 'Staff_Checkin_Report_' . $date . '.xls';

        // Set headers for Excel download
        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Build Excel HTML content
        echo $this->build_staff_excel_content($staff_list, $statistics, $date);
        exit;
    }

    /**
     * Build Excel HTML content for staff report
     */
    private function build_staff_excel_content($staff_list, $statistics, $date)
    {
        $html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        $html .= '<head><meta charset="utf-8"><meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
        $html .= '<style>';
        $html .= 'table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; }';
        $html .= 'th, td { border: 1px solid #333; padding: 8px; text-align: left; font-size: 11px; }';
        $html .= 'th { background-color: #4CAF50; color: white; font-weight: bold; text-align: center; }';
        $html .= '.header-info { margin-bottom: 20px; }';
        $html .= '.summary-box { background-color: #f0f0f0; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; }';
        $html .= '.text-center { text-align: center; }';
        $html .= '.text-success { color: green; font-weight: bold; }';
        $html .= '.text-danger { color: red; font-weight: bold; }';
        $html .= '.not-checked-in { background-color: #ffebee; }';
        $html .= '</style></head><body>';

        // Title
        $html .= '<div class="header-info">';
        $html .= '<h2 style="text-align: center; margin: 0;">Staff Check-in Report</h2>';
        $html .= '<h4 style="text-align: center; margin: 5px 0;">Date: ' . date('F d, Y', strtotime($date)) . '</h4>';
        $html .= '</div>';

        // Summary statistics
        $html .= '<div class="summary-box">';
        $html .= '<h3 style="margin: 0 0 10px 0;">Summary Statistics</h3>';
        $html .= '<table style="width: 50%; border: none;">';
        $html .= '<tr style="border: none;"><td style="border: none;"><strong>Total Staff:</strong></td><td style="border: none;">' . $statistics['staff']['total'] . '</td></tr>';
        $html .= '<tr style="border: none;"><td style="border: none;"><strong>Checked In:</strong></td><td style="border: none; color: green;">' . $statistics['staff']['checked_in'] . '</td></tr>';
        $html .= '<tr style="border: none;"><td style="border: none;"><strong>Not Checked In:</strong></td><td style="border: none; color: red;">' . $statistics['staff']['not_checked_in'] . '</td></tr>';
        $html .= '<tr style="border: none;"><td style="border: none;"><strong>Attendance Rate:</strong></td><td style="border: none; color: blue;">' . $statistics['staff']['percentage'] . '%</td></tr>';
        $html .= '</table>';
        $html .= '</div>';

        // Main data table
        $html .= '<table>';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th class="text-center">#</th>';
        $html .= '<th>Employee ID</th>';
        $html .= '<th>Name</th>';
        $html .= '<th>Role</th>';
        $html .= '<th>Department</th>';
        $html .= '<th class="text-center">Status</th>';
        $html .= '<th class="text-center">First Check-in</th>';
        $html .= '<th class="text-center">Last Check-in</th>';
        $html .= '<th class="text-center">First Check-out</th>';
        $html .= '<th class="text-center">Last Check-out</th>';
        $html .= '<th class="text-center">Total Punches</th>';
        $html .= '<th class="text-center">Check-in Count</th>';
        $html .= '<th class="text-center">Check-out Count</th>';
        $html .= '<th>Remark</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        $count = 1;
        foreach ($staff_list as $staff) {
            $row_class = !$staff['has_checked_in'] ? ' class="not-checked-in"' : '';
            $status_class = $staff['has_checked_in'] ? 'text-success' : 'text-danger';
            $status_text = $staff['has_checked_in'] ? $staff['status'] : 'Not Checked In';
            if ($staff['has_checked_in'] && $staff['has_checked_out']) {
                $status_text .= ' / Checked Out';
            }
            
            $html .= '<tr' . $row_class . '>';
            $html .= '<td class="text-center">' . $count++ . '</td>';
            $html .= '<td>' . htmlspecialchars($staff['employee_id']) . '</td>';
            $html .= '<td>' . htmlspecialchars($staff['name']) . '</td>';
            $html .= '<td>' . htmlspecialchars($staff['role']) . '</td>';
            $html .= '<td>' . htmlspecialchars($staff['department'] ?: '-') . '</td>';
            $html .= '<td class="text-center ' . $status_class . '">' . htmlspecialchars($status_text) . '</td>';
            $html .= '<td class="text-center">' . ($staff['first_checkin_time'] ?: '-') . '</td>';
            $html .= '<td class="text-center">' . ($staff['last_checkin_time'] ?: '-') . '</td>';
            $html .= '<td class="text-center" style="color: #ff6600;">' . (isset($staff['first_checkout_time']) ? $staff['first_checkout_time'] : '-') . '</td>';
            $html .= '<td class="text-center" style="color: #ff6600;">' . (isset($staff['last_checkout_time']) ? $staff['last_checkout_time'] : '-') . '</td>';
            $html .= '<td class="text-center">' . (isset($staff['total_punch_count']) ? $staff['total_punch_count'] : $staff['checkin_count']) . '</td>';
            $html .= '<td class="text-center">' . $staff['checkin_count'] . '</td>';
            $html .= '<td class="text-center">' . (isset($staff['checkout_count']) ? $staff['checkout_count'] : 0) . '</td>';
            $html .= '<td>' . htmlspecialchars(isset($staff['remark']) ? $staff['remark'] : '-') . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody>';
        $html .= '</table>';

        // Footer
        $html .= '<br><br>';
        $html .= '<p style="font-size: 10px; color: #666;">Generated on: ' . date('F d, Y h:i A') . '</p>';

        $html .= '</body></html>';

        return $html;
    }

    /**
     * Export student check-in report to Excel
     */
    public function export_student_excel()
    {
        if (!$this->rbac->hasPrivilege('biometric_checkin_report', 'can_view')) {
            access_denied();
        }

        $date = $this->input->get('date') ?: date('Y-m-d');
        $class_id = $this->input->get('class_id');
        $section_id = $this->input->get('section_id');
        
        $student_list = $this->biometric_checkin_model->getStudentCheckinSummary($date, $class_id, $section_id);
        $statistics = $this->biometric_checkin_model->getCheckinStatistics($date);

        // Generate filename
        $filename = 'Student_Checkin_Report_' . $date . '.xls';

        // Set headers for Excel download
        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Build Excel HTML content
        echo $this->build_student_excel_content($student_list, $statistics, $date, $class_id, $section_id);
        exit;
    }

    /**
     * Build Excel HTML content for student report
     */
    private function build_student_excel_content($student_list, $statistics, $date, $class_id = null, $section_id = null)
    {
        $html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        $html .= '<head><meta charset="utf-8"><meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
        $html .= '<style>';
        $html .= 'table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; }';
        $html .= 'th, td { border: 1px solid #333; padding: 8px; text-align: left; font-size: 11px; }';
        $html .= 'th { background-color: #2196F3; color: white; font-weight: bold; text-align: center; }';
        $html .= '.header-info { margin-bottom: 20px; }';
        $html .= '.summary-box { background-color: #f0f0f0; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; }';
        $html .= '.text-center { text-align: center; }';
        $html .= '.text-success { color: green; font-weight: bold; }';
        $html .= '.text-danger { color: red; font-weight: bold; }';
        $html .= '.not-checked-in { background-color: #ffebee; }';
        $html .= '</style></head><body>';

        // Title
        $html .= '<div class="header-info">';
        $html .= '<h2 style="text-align: center; margin: 0;">Student Check-in Report</h2>';
        $html .= '<h4 style="text-align: center; margin: 5px 0;">Date: ' . date('F d, Y', strtotime($date)) . '</h4>';
        if ($class_id || $section_id) {
            $html .= '<p style="text-align: center; margin: 5px 0;">Filtered by Class/Section</p>';
        }
        $html .= '</div>';

        // Summary statistics
        $html .= '<div class="summary-box">';
        $html .= '<h3 style="margin: 0 0 10px 0;">Summary Statistics</h3>';
        $html .= '<table style="width: 50%; border: none;">';
        $html .= '<tr style="border: none;"><td style="border: none;"><strong>Total Students:</strong></td><td style="border: none;">' . $statistics['students']['total'] . '</td></tr>';
        $html .= '<tr style="border: none;"><td style="border: none;"><strong>Checked In:</strong></td><td style="border: none; color: green;">' . $statistics['students']['checked_in'] . '</td></tr>';
        $html .= '<tr style="border: none;"><td style="border: none;"><strong>Not Checked In:</strong></td><td style="border: none; color: red;">' . $statistics['students']['not_checked_in'] . '</td></tr>';
        $html .= '<tr style="border: none;"><td style="border: none;"><strong>Attendance Rate:</strong></td><td style="border: none; color: blue;">' . $statistics['students']['percentage'] . '%</td></tr>';
        $html .= '</table>';
        $html .= '</div>';

        // Main data table
        $html .= '<table>';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th class="text-center">#</th>';
        $html .= '<th>Admission No</th>';
        $html .= '<th>Student Name</th>';
        $html .= '<th>Class</th>';
        $html .= '<th>Section</th>';
        $html .= '<th class="text-center">Status</th>';
        $html .= '<th class="text-center">First Check-in</th>';
        $html .= '<th class="text-center">Last Check-in</th>';
        $html .= '<th class="text-center">Check-in Count</th>';
        $html .= '<th>Remark</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        $count = 1;
        foreach ($student_list as $student) {
            $row_class = !$student['has_checked_in'] ? ' class="not-checked-in"' : '';
            $status_class = $student['has_checked_in'] ? 'text-success' : 'text-danger';
            $status_text = $student['has_checked_in'] ? $student['status'] : 'Not Checked In';
            
            $html .= '<tr' . $row_class . '>';
            $html .= '<td class="text-center">' . $count++ . '</td>';
            $html .= '<td>' . htmlspecialchars($student['admission_no']) . '</td>';
            $html .= '<td>' . htmlspecialchars($student['name']) . '</td>';
            $html .= '<td>' . htmlspecialchars($student['class']) . '</td>';
            $html .= '<td>' . htmlspecialchars($student['section']) . '</td>';
            $html .= '<td class="text-center ' . $status_class . '">' . htmlspecialchars($status_text) . '</td>';
            $html .= '<td class="text-center">' . ($student['first_checkin_time'] ?: '-') . '</td>';
            $html .= '<td class="text-center">' . ($student['last_checkin_time'] ?: '-') . '</td>';
            $html .= '<td class="text-center">' . $student['checkin_count'] . '</td>';
            $html .= '<td>' . htmlspecialchars(isset($student['remark']) ? $student['remark'] : '-') . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody>';
        $html .= '</table>';

        // Footer
        $html .= '<br><br>';
        $html .= '<p style="font-size: 10px; color: #666;">Generated on: ' . date('F d, Y h:i A') . '</p>';

        $html .= '</body></html>';

        return $html;
    }

    /**
     * Print staff check-in report
     */
    public function print_staff()
    {
        if (!$this->rbac->hasPrivilege('biometric_checkin_report', 'can_view')) {
            access_denied();
        }

        $date = $this->input->get('date') ?: date('Y-m-d');
        
        $data['date'] = $date;
        $data['staff_list'] = $this->biometric_checkin_model->getStaffCheckinSummary($date);
        $data['statistics'] = $this->biometric_checkin_model->getCheckinStatistics($date);
        $data['school'] = $this->setting_model->getSchoolDetail();

        $this->load->view('biometric_checkin/print_staff', $data);
    }

    /**
     * Print student check-in report
     */
    public function print_student()
    {
        if (!$this->rbac->hasPrivilege('biometric_checkin_report', 'can_view')) {
            access_denied();
        }

        $date = $this->input->get('date') ?: date('Y-m-d');
        $class_id = $this->input->get('class_id');
        $section_id = $this->input->get('section_id');
        
        $data['date'] = $date;
        $data['student_list'] = $this->biometric_checkin_model->getStudentCheckinSummary($date, $class_id, $section_id);
        $data['statistics'] = $this->biometric_checkin_model->getCheckinStatistics($date);
        $data['school'] = $this->setting_model->getSchoolDetail();

        $this->load->view('biometric_checkin/print_student', $data);
    }
}

