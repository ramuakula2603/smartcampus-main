<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Online Admission API Controller
 * 
 * This controller provides RESTful API endpoints for online admission management.
 * It handles listing, filtering, and retrieving online admission records.
 * 
 * @package    Student Management System
 * @subpackage API Controllers
 * @category   Webservices
 * @author     School Management System
 * @version    1.0.0
 */
class Online_admission_api extends CI_Controller
{
    /**
     * Constructor
     * 
     * Initializes the controller, loads required models, libraries, and helpers.
     * Sets up error handling and timezone configuration.
     */
    public function __construct()
    {
        parent::__construct();

        // Start output buffering if not already started
        if (!ob_get_level()) {
            ob_start();
        }

        // Set JSON content type early
        $this->output->set_content_type('application/json');

        // Load essential helpers
        $this->load->helper('json_output');

        // Load required models
        try {
            $this->load->model(array(
                'onlinestudent_model',
                'setting_model',
                'class_model',
                'section_model',
                'category_model'
            ));
        } catch (Exception $e) {
            log_message('error', 'Error loading models: ' . $e->getMessage());
        }

        // Load libraries
        try {
            $this->load->library(array('customlib'));
        } catch (Exception $e) {
            log_message('error', 'Error loading libraries: ' . $e->getMessage());
        }

        // Get school settings
        try {
            $this->sch_setting_detail = $this->setting_model->getSetting();
            
            // Set timezone
            if (isset($this->sch_setting_detail->timezone) && $this->sch_setting_detail->timezone != "") {
                date_default_timezone_set($this->sch_setting_detail->timezone);
            } else {
                date_default_timezone_set('UTC');
            }
        } catch (Exception $e) {
            log_message('error', 'Error loading school settings: ' . $e->getMessage());
            date_default_timezone_set('UTC');
        }
    }

    /**
     * List all online admissions
     * 
     * Retrieves a list of all online admission records with optional filtering.
     * Supports pagination and search functionality.
     * 
     * @return void Outputs JSON response
     */
    public function list()
    {
        try {
            // Validate request method
            if ($this->input->method() !== 'post') {
                json_output(405, array(
                    'status' => 0,
                    'message' => 'Method not allowed. Use POST method.',
                    'data' => null
                ));
                return;
            }

            // Validate required headers
            $client_service = $this->input->get_request_header('Client-Service', TRUE);
            $auth_key = $this->input->get_request_header('Auth-Key', TRUE);

            if ($client_service !== 'smartschool' || $auth_key !== 'schoolAdmin@') {
                json_output(401, array(
                    'status' => 0,
                    'message' => 'Unauthorized access. Invalid headers.',
                    'data' => null
                ));
                return;
            }

            // Get input parameters
            $input = json_decode($this->input->raw_input_stream, true);
            
            // Optional filters
            $class_id = isset($input['class_id']) ? (int)$input['class_id'] : null;
            $section_id = isset($input['section_id']) ? (int)$input['section_id'] : null;
            $status = isset($input['status']) ? $input['status'] : null;
            $search = isset($input['search']) ? $input['search'] : null;

            // Get online admissions list
            $admissions = $this->onlinestudent_model->get(null, null);

            // Apply filters if provided
            if (!empty($admissions)) {
                if ($class_id !== null) {
                    $admissions = array_filter($admissions, function($admission) use ($class_id) {
                        return $admission['class_id'] == $class_id;
                    });
                }

                if ($section_id !== null) {
                    $admissions = array_filter($admissions, function($admission) use ($section_id) {
                        return $admission['section_id'] == $section_id;
                    });
                }

                if ($status !== null) {
                    $admissions = array_filter($admissions, function($admission) use ($status) {
                        return $admission['is_enroll'] == $status;
                    });
                }

                if ($search !== null && !empty($search)) {
                    $admissions = array_filter($admissions, function($admission) use ($search) {
                        $searchFields = [
                            $admission['firstname'],
                            $admission['lastname'],
                            $admission['reference_no'],
                            $admission['father_name'],
                            $admission['mobileno'],
                            $admission['email']
                        ];
                        
                        foreach ($searchFields as $field) {
                            if (stripos($field, $search) !== false) {
                                return true;
                            }
                        }
                        return false;
                    });
                }
            }

            // Format response data
            $formatted_admissions = array();
            if (!empty($admissions)) {
                foreach ($admissions as $admission) {
                    $formatted_admissions[] = array(
                        'id' => $admission['id'],
                        'reference_no' => $admission['reference_no'],
                        'admission_no' => $admission['admission_no'],
                        'admission_date' => $admission['admission_date'],
                        'full_name' => trim($admission['firstname'] . ' ' . $admission['middlename'] . ' ' . $admission['lastname']),
                        'firstname' => $admission['firstname'],
                        'middlename' => $admission['middlename'],
                        'lastname' => $admission['lastname'],
                        'dob' => $admission['dob'],
                        'gender' => $admission['gender'],
                        'email' => $admission['email'],
                        'mobileno' => $admission['mobileno'],
                        'father_name' => $admission['father_name'],
                        'father_phone' => $admission['father_phone'],
                        'mother_name' => $admission['mother_name'],
                        'mother_phone' => $admission['mother_phone'],
                        'guardian_name' => $admission['guardian_name'],
                        'guardian_phone' => $admission['guardian_phone'],
                        'current_address' => $admission['current_address'],
                        'permanent_address' => $admission['permanent_address'],
                        'class_info' => array(
                            'class_id' => $admission['class_id'],
                            'class_name' => $admission['class'],
                            'section_id' => $admission['section_id'],
                            'section_name' => $admission['section']
                        ),
                        'category' => $admission['category'],
                        'house_name' => $admission['house_name'],
                        'blood_group' => $admission['blood_group'],
                        'religion' => $admission['religion'],
                        'cast' => $admission['cast'],
                        'is_enroll' => $admission['is_enroll'],
                        'form_status' => $admission['form_status'],
                        'paid_status' => $admission['paid_status'],
                        'created_at' => $admission['created_at'],
                        'updated_at' => $admission['updated_at']
                    );
                }
            }

            // Prepare filters applied info
            $filters_applied = array();
            if ($class_id !== null) $filters_applied['class_id'] = $class_id;
            if ($section_id !== null) $filters_applied['section_id'] = $section_id;
            if ($status !== null) $filters_applied['status'] = $status;
            if ($search !== null) $filters_applied['search'] = $search;

            json_output(200, array(
                'status' => 1,
                'message' => 'Online admissions retrieved successfully',
                'filters_applied' => $filters_applied,
                'total_records' => count($formatted_admissions),
                'data' => $formatted_admissions
            ));

        } catch (Exception $e) {
            log_message('error', 'Online Admission API List Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Get single online admission record
     * 
     * Retrieves detailed information for a specific online admission record.
     * 
     * @param int $id Online admission ID
     * @return void Outputs JSON response
     */
    public function get($id = null)
    {
        try {
            // Validate request method
            if ($this->input->method() !== 'post') {
                json_output(405, array(
                    'status' => 0,
                    'message' => 'Method not allowed. Use POST method.',
                    'data' => null
                ));
                return;
            }

            // Validate required headers
            $client_service = $this->input->get_request_header('Client-Service', TRUE);
            $auth_key = $this->input->get_request_header('Auth-Key', TRUE);

            if ($client_service !== 'smartschool' || $auth_key !== 'schoolAdmin@') {
                json_output(401, array(
                    'status' => 0,
                    'message' => 'Unauthorized access. Invalid headers.',
                    'data' => null
                ));
                return;
            }

            // Validate ID parameter
            if ($id === null || !is_numeric($id) || $id <= 0) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or missing admission ID',
                    'data' => null
                ));
                return;
            }

            // Get admission record
            $admission = $this->onlinestudent_model->get($id);

            if (empty($admission)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Online admission record not found',
                    'data' => null
                ));
                return;
            }

            // Format response data
            $formatted_admission = array(
                'id' => $admission['id'],
                'reference_no' => $admission['reference_no'],
                'admission_no' => $admission['admission_no'],
                'roll_no' => $admission['roll_no'],
                'admission_date' => $admission['admission_date'],
                'full_name' => trim($admission['firstname'] . ' ' . $admission['middlename'] . ' ' . $admission['lastname']),
                'firstname' => $admission['firstname'],
                'middlename' => $admission['middlename'],
                'lastname' => $admission['lastname'],
                'dob' => $admission['dob'],
                'gender' => $admission['gender'],
                'email' => $admission['email'],
                'mobileno' => $admission['mobileno'],
                'blood_group' => $admission['blood_group'],
                'religion' => $admission['religion'],
                'cast' => $admission['cast'],
                'rte' => $admission['rte'],
                'current_address' => $admission['current_address'],
                'permanent_address' => $admission['permanent_address'],
                'previous_school' => $admission['previous_school'],
                'father_info' => array(
                    'name' => $admission['father_name'],
                    'phone' => $admission['father_phone'],
                    'occupation' => $admission['father_occupation'],
                    'pic' => $admission['father_pic']
                ),
                'mother_info' => array(
                    'name' => $admission['mother_name'],
                    'phone' => $admission['mother_phone'],
                    'occupation' => $admission['mother_occupation'],
                    'pic' => $admission['mother_pic']
                ),
                'guardian_info' => array(
                    'is' => $admission['guardian_is'],
                    'name' => $admission['guardian_name'],
                    'relation' => $admission['guardian_relation'],
                    'phone' => $admission['guardian_phone'],
                    'email' => $admission['guardian_email'],
                    'occupation' => $admission['guardian_occupation'],
                    'address' => $admission['guardian_address'],
                    'pic' => $admission['guardian_pic']
                ),
                'class_info' => array(
                    'class_section_id' => $admission['class_section_id'],
                    'class_id' => $admission['class_id'],
                    'class_name' => $admission['class'],
                    'section_id' => $admission['section_id'],
                    'section_name' => $admission['section']
                ),
                'category' => $admission['category'],
                'house_info' => array(
                    'house_id' => $admission['school_house_id'],
                    'house_name' => $admission['house_name']
                ),
                'hostel_info' => array(
                    'hostel_id' => $admission['hostel_id'],
                    'hostel_name' => $admission['hostel_name'],
                    'room_id' => $admission['hostel_room_id'],
                    'room_no' => $admission['room_no'],
                    'room_type_id' => $admission['room_type_id'],
                    'room_type' => $admission['room_type']
                ),
                'transport_info' => array(
                    'route_id' => $admission['route_id'],
                    'route_title' => $admission['route_title'],
                    'vehicle_id' => $admission['vehicle_id'],
                    'vehicle_no' => $admission['vehicle_no'],
                    'driver_name' => $admission['driver_name'],
                    'driver_contact' => $admission['driver_contact']
                ),
                'financial_info' => array(
                    'bank_account_no' => $admission['bank_account_no'],
                    'bank_name' => $admission['bank_name'],
                    'ifsc_code' => $admission['ifsc_code']
                ),
                'documents' => array(
                    'document' => $admission['document'],
                    'adhar_no' => $admission['adhar_no'],
                    'samagra_id' => $admission['samagra_id']
                ),
                'physical_info' => array(
                    'height' => $admission['height'],
                    'weight' => $admission['weight'],
                    'measurement_date' => $admission['measurement_date']
                ),
                'status_info' => array(
                    'is_enroll' => $admission['is_enroll'],
                    'form_status' => $admission['form_status'],
                    'paid_status' => $admission['paid_status']
                ),
                'timestamps' => array(
                    'created_at' => $admission['created_at'],
                    'updated_at' => $admission['updated_at']
                ),
                'additional_info' => array(
                    'state' => $admission['state'],
                    'city' => $admission['city'],
                    'pincode' => $admission['pincode'],
                    'note' => $admission['note']
                )
            );

            json_output(200, array(
                'status' => 1,
                'message' => 'Online admission record retrieved successfully',
                'data' => $formatted_admission
            ));

        } catch (Exception $e) {
            log_message('error', 'Online Admission API Get Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Filter online admissions with advanced criteria
     *
     * Provides advanced filtering capabilities for online admission records.
     * Supports multiple filter combinations and date range filtering.
     *
     * @return void Outputs JSON response
     */
    public function filter()
    {
        try {
            // Validate request method
            if ($this->input->method() !== 'post') {
                json_output(405, array(
                    'status' => 0,
                    'message' => 'Method not allowed. Use POST method.',
                    'data' => null
                ));
                return;
            }

            // Validate required headers
            $client_service = $this->input->get_request_header('Client-Service', TRUE);
            $auth_key = $this->input->get_request_header('Auth-Key', TRUE);

            if ($client_service !== 'smartschool' || $auth_key !== 'schoolAdmin@') {
                json_output(401, array(
                    'status' => 0,
                    'message' => 'Unauthorized access. Invalid headers.',
                    'data' => null
                ));
                return;
            }

            // Get input parameters
            $input = json_decode($this->input->raw_input_stream, true);

            // Filter parameters
            $filters = array(
                'class_id' => isset($input['class_id']) ? (int)$input['class_id'] : null,
                'section_id' => isset($input['section_id']) ? (int)$input['section_id'] : null,
                'category_id' => isset($input['category_id']) ? (int)$input['category_id'] : null,
                'gender' => isset($input['gender']) ? $input['gender'] : null,
                'is_enroll' => isset($input['is_enroll']) ? $input['is_enroll'] : null,
                'form_status' => isset($input['form_status']) ? $input['form_status'] : null,
                'paid_status' => isset($input['paid_status']) ? $input['paid_status'] : null,
                'date_from' => isset($input['date_from']) ? $input['date_from'] : null,
                'date_to' => isset($input['date_to']) ? $input['date_to'] : null,
                'search' => isset($input['search']) ? $input['search'] : null
            );

            // Get all online admissions
            $admissions = $this->onlinestudent_model->get(null, null);

            // Apply filters
            if (!empty($admissions)) {
                foreach ($filters as $key => $value) {
                    if ($value !== null && $value !== '') {
                        switch ($key) {
                            case 'class_id':
                                $admissions = array_filter($admissions, function($admission) use ($value) {
                                    return $admission['class_id'] == $value;
                                });
                                break;
                            case 'section_id':
                                $admissions = array_filter($admissions, function($admission) use ($value) {
                                    return $admission['section_id'] == $value;
                                });
                                break;
                            case 'category_id':
                                $admissions = array_filter($admissions, function($admission) use ($value) {
                                    return $admission['category_id'] == $value;
                                });
                                break;
                            case 'gender':
                                $admissions = array_filter($admissions, function($admission) use ($value) {
                                    return strtolower($admission['gender']) == strtolower($value);
                                });
                                break;
                            case 'is_enroll':
                                $admissions = array_filter($admissions, function($admission) use ($value) {
                                    return $admission['is_enroll'] == $value;
                                });
                                break;
                            case 'form_status':
                                $admissions = array_filter($admissions, function($admission) use ($value) {
                                    return $admission['form_status'] == $value;
                                });
                                break;
                            case 'paid_status':
                                $admissions = array_filter($admissions, function($admission) use ($value) {
                                    return $admission['paid_status'] == $value;
                                });
                                break;
                            case 'date_from':
                                $admissions = array_filter($admissions, function($admission) use ($value) {
                                    return strtotime($admission['created_at']) >= strtotime($value);
                                });
                                break;
                            case 'date_to':
                                $admissions = array_filter($admissions, function($admission) use ($value) {
                                    return strtotime($admission['created_at']) <= strtotime($value . ' 23:59:59');
                                });
                                break;
                            case 'search':
                                $admissions = array_filter($admissions, function($admission) use ($value) {
                                    $searchFields = [
                                        $admission['firstname'],
                                        $admission['lastname'],
                                        $admission['reference_no'],
                                        $admission['admission_no'],
                                        $admission['father_name'],
                                        $admission['mother_name'],
                                        $admission['mobileno'],
                                        $admission['email']
                                    ];

                                    foreach ($searchFields as $field) {
                                        if (stripos($field, $value) !== false) {
                                            return true;
                                        }
                                    }
                                    return false;
                                });
                                break;
                        }
                    }
                }
            }

            // Format response data
            $formatted_admissions = array();
            if (!empty($admissions)) {
                foreach ($admissions as $admission) {
                    $formatted_admissions[] = array(
                        'id' => $admission['id'],
                        'reference_no' => $admission['reference_no'],
                        'admission_no' => $admission['admission_no'],
                        'admission_date' => $admission['admission_date'],
                        'full_name' => trim($admission['firstname'] . ' ' . $admission['middlename'] . ' ' . $admission['lastname']),
                        'firstname' => $admission['firstname'],
                        'middlename' => $admission['middlename'],
                        'lastname' => $admission['lastname'],
                        'dob' => $admission['dob'],
                        'gender' => $admission['gender'],
                        'email' => $admission['email'],
                        'mobileno' => $admission['mobileno'],
                        'father_name' => $admission['father_name'],
                        'mother_name' => $admission['mother_name'],
                        'class_info' => array(
                            'class_id' => $admission['class_id'],
                            'class_name' => $admission['class'],
                            'section_id' => $admission['section_id'],
                            'section_name' => $admission['section']
                        ),
                        'category' => $admission['category'],
                        'house_name' => $admission['house_name'],
                        'blood_group' => $admission['blood_group'],
                        'is_enroll' => $admission['is_enroll'],
                        'form_status' => $admission['form_status'],
                        'paid_status' => $admission['paid_status'],
                        'created_at' => $admission['created_at']
                    );
                }
            }

            // Remove null filters for response
            $applied_filters = array_filter($filters, function($value) {
                return $value !== null && $value !== '';
            });

            json_output(200, array(
                'status' => 1,
                'message' => 'Online admissions filtered successfully',
                'filters_applied' => $applied_filters,
                'total_records' => count($formatted_admissions),
                'data' => $formatted_admissions
            ));

        } catch (Exception $e) {
            log_message('error', 'Online Admission API Filter Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }
}
