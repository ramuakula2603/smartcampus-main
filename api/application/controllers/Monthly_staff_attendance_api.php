
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Monthly Staff Attendance Report API Controller
 * 
 * Provides comprehensive monthly attendance reports for staff members
 * with detailed daily attendance records, percentage calculations,
 * and attendance type summaries.
 * 
 * This API mirrors the functionality of the web page:
 * http://localhost/amt/attendencereports/staffattendancereport
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Staff Attendance Report APIs
 * @author     SMS Development Team
 * @version    1.0.0
 * @date       October 2025
 */
class Monthly_staff_attendance_api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Load required models
        $this->load->model('setting_model');
        $this->load->model('auth_model');
        $this->load->model('staffattendancemodel');
        $this->load->model('staff_model');
        
        // Load helpers
        $this->load->helper('url');
        $this->load->helper('security');
    }

    /**
     * Get Monthly Staff Attendance Report
     * 
     * This endpoint provides comprehensive monthly attendance report
     * similar to the staff attendance report page.
     * 
     * @method POST
     * @route  /api/monthly-staff-attendance/report
     * 
     * @param  string  $role       Optional. Staff role name to filter (e.g., "Teacher", "Admin")
     * @param  string  $month      Required. Month name (e.g., "January", "October")
     * @param  int     $year       Required. Year (e.g., 2024, 2025)
     * 
     * @return JSON Response with status, message, report data, and metadata
     */
    public function report()
    {
        try {
            // Validate request method
            if ($this->input->method() !== 'post') {
                $this->output
                    ->set_status_header(400)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 0,
                        'message' => 'Bad request. Only POST method allowed.'
                    ]));
                return;
            }

            // Check authentication
            if (!$this->auth_model->check_auth_client()) {
                $this->output
                    ->set_status_header(401)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 0,
                        'message' => 'Unauthorized access'
                    ]));
                return;
            }

            // Get JSON input
            $json_input = json_decode(file_get_contents('php://input'), true);
            
            if ($json_input === null) {
                $this->output
                    ->set_status_header(400)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 0,
                        'message' => 'Invalid JSON format in request body.'
                    ]));
                return;
            }

            // Extract parameters with defaults
            $role = isset($json_input['role']) ? $json_input['role'] : 'select';
            $month = isset($json_input['month']) ? $json_input['month'] : null; // null means all months
            $year = isset($json_input['year']) ? $json_input['year'] : null; // null means all years

            // Determine years to process
            $years_to_process = [];
            $is_all_years = false;
            
            if ($year === null) {
                // Get all available years from database
                $available_years = $this->staffattendancemodel->getStaffAttendanceYears();
                
                if (empty($available_years)) {
                    // No attendance records found, default to current year
                    $years_to_process = [date('Y')];
                } else {
                    // Extract year values from the result
                    foreach ($available_years as $year_record) {
                        $years_to_process[] = $year_record['year'];
                    }
                    $is_all_years = true;
                }
            } else {
                // Validate single year
                if (!is_numeric($year) || $year < 2000 || $year > 2100) {
                    $this->output
                        ->set_status_header(400)
                        ->set_content_type('application/json')
                        ->set_output(json_encode([
                            'status' => 0,
                            'message' => 'Invalid year. Must be between 2000 and 2100.'
                        ]));
                    return;
                }
                $years_to_process = [$year];
            }

            // Get attendance types
            $attendance_types = $this->staffattendancemodel->getStaffAttendanceType();

            // Determine if we're getting all months or a specific month
            if ($month === null) {
                // Get all months of the year
                $months_to_process = [
                    'January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December'
                ];
                $is_all_months = true;
            } else {
                // Process single month
                $months_to_process = [$month];
                $is_all_months = false;
                
                // Validate month name
                $month_number = date("m", strtotime($month));
                if (!$month_number) {
                    $this->output
                        ->set_status_header(400)
                        ->set_content_type('application/json')
                        ->set_output(json_encode([
                            'status' => 0,
                            'message' => 'Invalid month name. Use full month name (e.g., "January", "October").'
                        ]));
                    return;
                }
            }

            // Get staff list based on role
            $staff_list = $this->staff_model->getEmployee($role);

            if (empty($staff_list)) {
                $this->output
                    ->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 1,
                        'message' => 'No staff found for the selected role.',
                        'filters_applied' => [
                            'role' => $role,
                            'month' => $month,
                            'year' => $year,
                            'all_months' => $is_all_months,
                            'all_years' => $is_all_years
                        ],
                        'attendance_types' => $attendance_types,
                        'total_staff' => 0,
                        'data' => [],
                        'timestamp' => date('Y-m-d H:i:s')
                    ]));
                return;
            }

            // Process data for each year and month
            $all_years_data = [];
            
            foreach ($years_to_process as $current_year) {
                $all_months_data = [];
                
                foreach ($months_to_process as $current_month) {
                    // Convert month name to number
                    $month_number = date("m", strtotime($current_month));
                    
                    // Get number of days in the month
                    $num_of_days = cal_days_in_month(CAL_GREGORIAN, $month_number, $current_year);
                    
                    // Build daily attendance data for this month
                    $attendance_data = [];
                    $dates_array = [];

                    for ($day = 1; $day <= $num_of_days; $day++) {
                        $att_date = sprintf("%s-%s-%02d", $current_year, $month_number, $day);
                        $dates_array[] = $att_date;

                        // Get attendance for this date
                        $daily_attendance = $this->staffattendancemodel->searchAttendanceReport($role, $att_date);

                    foreach ($daily_attendance as $attendance) {
                        $staff_id = $attendance['id'];
                        
                        if (!isset($attendance_data[$staff_id])) {
                            // Initialize staff record
                            $attendance_data[$staff_id] = [
                                'staff_id' => $staff_id,
                                'staff_info' => [
                                    'name' => $attendance['name'],
                                    'surname' => $attendance['surname'],
                                    'employee_id' => $attendance['employee_id'],
                                    'contact_no' => $attendance['contact_no'],
                                    'email' => $attendance['email'],
                                    'role' => $attendance['user_type']
                                ],
                                'daily_attendance' => [],
                                'attendance_summary' => []
                            ];

                            // Initialize attendance summary counters
                            foreach ($attendance_types as $type) {
                                $attendance_data[$staff_id]['attendance_summary'][$type['type']] = 0;
                            }
                        }

                        // Add daily attendance record
                        $attendance_data[$staff_id]['daily_attendance'][$att_date] = [
                            'date' => $att_date,
                            'day_name' => date('l', strtotime($att_date)),
                            'day_short' => date('D', strtotime($att_date)),
                            'attendance_type' => $attendance['att_type'] ?? 'Not Marked',
                            'attendance_key' => $attendance['key'] ?? '-',
                            'remark' => $attendance['remark'] ?? ''
                        ];

                        // Update attendance summary
                        if (isset($attendance['att_type']) && !empty($attendance['att_type'])) {
                            $attendance_data[$staff_id]['attendance_summary'][$attendance['att_type']]++;
                        }
                    }
                }

                // Calculate attendance percentages
                foreach ($attendance_data as $staff_id => &$staff) {
                    $total_present = ($staff['attendance_summary']['Present'] ?? 0) +
                                    ($staff['attendance_summary']['Late'] ?? 0) +
                                    ($staff['attendance_summary']['Half Day'] ?? 0);

                    $total_days = ($staff['attendance_summary']['Present'] ?? 0) +
                                 ($staff['attendance_summary']['Late'] ?? 0) +
                                 ($staff['attendance_summary']['Absent'] ?? 0) +
                                 ($staff['attendance_summary']['Half Day'] ?? 0);

                    if ($total_days > 0) {
                        $percentage = ($total_present / $total_days) * 100;
                        $staff['attendance_percentage'] = round($percentage, 2);
                        $staff['attendance_percentage_display'] = round($percentage, 0);
                        
                        // Categorize attendance status
                        if ($percentage < 75) {
                            $staff['attendance_status'] = 'Low';
                            $staff['attendance_status_class'] = 'danger';
                        } else if ($percentage >= 75) {
                                $staff['attendance_status'] = 'Good';
                            $staff['attendance_status_class'] = 'success';
                        }
                    } else {
                        $staff['attendance_percentage'] = 0;
                        $staff['attendance_percentage_display'] = '-';
                        $staff['attendance_status'] = 'No Data';
                        $staff['attendance_status_class'] = 'default';
                    }

                    $staff['total_working_days'] = $total_days;
                    $staff['total_present_days'] = $total_present;
                }
                
                // Store this month's data
                if ($is_all_months) {
                    $all_months_data[$current_month] = [
                        'month' => $current_month,
                        'month_number' => (int)$month_number,
                        'total_days' => $num_of_days,
                        'dates' => $dates_array,
                        'total_staff' => count($attendance_data),
                        'staff_attendance' => array_values($attendance_data)
                    ];
                } else {
                    // For single month, keep the original structure
                    $all_months_data = [
                        'month' => $current_month,
                        'month_number' => (int)$month_number,
                        'total_days' => $num_of_days,
                        'dates' => $dates_array,
                        'total_staff' => count($attendance_data),
                        'staff_attendance' => array_values($attendance_data)
                    ];
                }
            }
            
            // Store this year's data
            if ($is_all_years) {
                $all_years_data[$current_year] = [
                    'year' => (int)$current_year,
                    'total_months' => count($all_months_data),
                    'months_data' => $all_months_data
                ];
            } else if ($is_all_months) {
                // Single year, all months
                $all_years_data = $all_months_data;
            } else {
                // Single year, single month
                $all_years_data = $all_months_data;
            }
        }

            // Prepare response based on scope
            if ($is_all_years) {
                // All years, all months
                $response = [
                    'status' => 1,
                    'message' => 'All years staff attendance report retrieved successfully',
                    'filters_applied' => [
                        'role' => $role,
                        'all_years' => true,
                        'all_months' => true
                    ],
                    'attendance_types' => $attendance_types,
                    'total_years' => count($all_years_data),
                    'years_data' => $all_years_data,
                    'timestamp' => date('Y-m-d H:i:s')
                ];
            } else if ($is_all_months) {
                // Single year, all months
                $response = [
                    'status' => 1,
                    'message' => 'Yearly staff attendance report retrieved successfully',
                    'filters_applied' => [
                        'role' => $role,
                        'year' => (int)$years_to_process[0],
                        'all_months' => true
                    ],
                    'attendance_types' => $attendance_types,
                    'total_months' => count($all_years_data),
                    'months_data' => $all_years_data,
                    'timestamp' => date('Y-m-d H:i:s')
                ];
            } else {
                // Single year, single month
                $response = [
                    'status' => 1,
                    'message' => 'Monthly staff attendance report retrieved successfully',
                    'filters_applied' => [
                        'role' => $role,
                        'month' => $month,
                        'month_number' => $all_years_data['month_number'],
                        'year' => (int)$years_to_process[0]
                    ],
                    'attendance_types' => $attendance_types,
                    'total_staff' => $all_years_data['total_staff'],
                    'total_days' => $all_years_data['total_days'],
                    'dates' => $all_years_data['dates'],
                    'data' => $all_years_data['staff_attendance'],
                    'timestamp' => date('Y-m-d H:i:s')
                ];
            }

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Monthly Staff Attendance API Error: ' . $e->getMessage());
            
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 0,
                    'message' => 'Internal server error occurred',
                    'error' => $e->getMessage(),
                    'data' => null
                ]));
        }
    }

    /**
     * Get Available Months and Years
     * 
     * Returns list of months and years that have attendance data
     * 
     * @method POST
     * @route  /api/monthly-staff-attendance/available-periods
     * 
     * @return JSON Response with available months and years
     */
    public function available_periods()
    {
        try {
            // Validate request method
            if ($this->input->method() !== 'post') {
                $this->output
                    ->set_status_header(400)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 0,
                        'message' => 'Bad request. Only POST method allowed.'
                    ]));
                return;
            }

            // Check authentication
            if (!$this->auth_model->check_auth_client()) {
                $this->output
                    ->set_status_header(401)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 0,
                        'message' => 'Unauthorized access'
                    ]));
                return;
            }

            // Get available years
            $years = $this->staffattendancemodel->attendanceYearCount();

            // Get month list
            $months = [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ];

            // Get staff roles
            $staff_roles = $this->staff_model->getStaffRole();

            $response = [
                'status' => 1,
                'message' => 'Available periods retrieved successfully',
                'data' => [
                    'years' => $years,
                    'months' => $months,
                    'roles' => $staff_roles
                ],
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Monthly Staff Attendance Available Periods API Error: ' . $e->getMessage());
            
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 0,
                    'message' => 'Internal server error occurred',
                    'error' => $e->getMessage(),
                    'data' => null
                ]));
        }
    }
}
