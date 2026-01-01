<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Subjectgroup_api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('publicresultsubjectgroup_model');
        $this->load->helper('json_output');
    }

    private function validate_headers()
    {
        $client_service = $this->input->get_request_header('Client-Service', TRUE);
        $auth_key = $this->input->get_request_header('Auth-Key', TRUE);
        
        if ($client_service == 'smartschool' && $auth_key == 'schoolAdmin@') {
            return true;
        }
        return false;
    }

    /**
     * Get all result types (exam types)
     * 
     * @return void Outputs JSON response with list of result types
     */
    public function get_result_types()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'GET') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            if ($this->validate_headers()) {
                $result_types = $this->publicresultsubjectgroup_model->resulttype();
                json_output(200, array('status' => 200, 'message' => 'Success', 'data' => $result_types));
            } else {
                json_output(401, array('status' => 401, 'message' => 'Client Service or Auth Key is invalid.'));
            }
        }
    }

    /**
     * Get all subjects
     * 
     * @return void Outputs JSON response with list of subjects
     */
    public function get_subjects()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'GET') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            if ($this->validate_headers()) {
                $subjects = $this->publicresultsubjectgroup_model->subjects();
                json_output(200, array('status' => 200, 'message' => 'Success', 'data' => $subjects));
            } else {
                json_output(401, array('status' => 401, 'message' => 'Client Service or Auth Key is invalid.'));
            }
        }
    }

    /**
     * Get all subject groups
     * 
     * @return void Outputs JSON response with list of subject groups
     */
    public function get_subject_groups()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'GET') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            if ($this->validate_headers()) {
                $subject_groups = $this->publicresultsubjectgroup_model->getByID();
                json_output(200, array('status' => 200, 'message' => 'Success', 'data' => $subject_groups));
            } else {
                json_output(401, array('status' => 401, 'message' => 'Client Service or Auth Key is invalid.'));
            }
        }
    }

    /**
     * Get specific subject group by ID
     * 
     * @param int $id Subject group ID
     * @return void Outputs JSON response with subject group details
     */
    public function get_subject_group($id)
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'GET') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            if ($this->validate_headers()) {
                if (empty($id)) {
                    json_output(400, array('status' => 400, 'message' => 'Subject group ID is required.'));
                    return;
                }

                $subject_group = $this->publicresultsubjectgroup_model->getByID($id);
                
                if (empty($subject_group)) {
                    json_output(404, array('status' => 404, 'message' => 'Subject group not found.'));
                    return;
                }

                json_output(200, array('status' => 200, 'message' => 'Success', 'data' => $subject_group));
            } else {
                json_output(401, array('status' => 401, 'message' => 'Client Service or Auth Key is invalid.'));
            }
        }
    }

    /**
     * Create new subject group
     * 
     * @return void Outputs JSON response with creation status
     */
    public function create_subject_group()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            if ($this->validate_headers()) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                
                if (empty($params)) {
                    json_output(400, array('status' => 400, 'message' => 'Invalid JSON format or empty body.'));
                    return;
                }

                $result_type_id = isset($params['result_type_id']) ? $params['result_type_id'] : '';
                $subjects = isset($params['subjects']) ? $params['subjects'] : array();

                // Validate required fields
                if (empty($result_type_id)) {
                    json_output(400, array('status' => 400, 'message' => 'Result type ID is required.'));
                    return;
                }

                if (empty($subjects) || !is_array($subjects)) {
                    json_output(400, array('status' => 400, 'message' => 'At least one subject is required.'));
                    return;
                }

                // Prepare subject group data
                $subject_group_subject_Array = array();
                
                foreach ($subjects as $subject) {
                    if (!isset($subject['subject_id'])) {
                        json_output(400, array('status' => 400, 'message' => 'Subject ID is required for each subject.'));
                        return;
                    }

                    $subject_data = array(
                        'resultsubjects_id' => $result_type_id,
                        'subject_id' => $subject['subject_id'],
                        'session_id' => $this->setting_model->getCurrentSession(),
                        'minmarks' => isset($subject['minmarks']) ? $subject['minmarks'] : 0,
                        'maxmarks' => isset($subject['maxmarks']) ? $subject['maxmarks'] : 100
                    );

                    $subject_group_subject_Array[] = $subject_data;
                }

                // Insert the data
                $this->db->insert_batch('publicresultsubject_group_subjects', $subject_group_subject_Array);

                json_output(201, array(
                    'status' => 201,
                    'message' => 'Subject group created successfully.',
                    'data' => array(
                        'result_type_id' => $result_type_id,
                        'subjects_count' => count($subject_group_subject_Array)
                    )
                ));
            } else {
                json_output(401, array('status' => 401, 'message' => 'Client Service or Auth Key is invalid.'));
            }
        }
    }

    /**
     * Update subject group
     * 
     * @param int $id Subject group ID
     * @return void Outputs JSON response with update status
     */
    public function update_subject_group($id)
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'PUT' && $method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            if ($this->validate_headers()) {
                if (empty($id)) {
                    json_output(400, array('status' => 400, 'message' => 'Subject group ID is required.'));
                    return;
                }

                $params = json_decode(file_get_contents('php://input'), TRUE);
                
                if (empty($params)) {
                    json_output(400, array('status' => 400, 'message' => 'Invalid JSON format or empty body.'));
                    return;
                }

                $subjects = isset($params['subjects']) ? $params['subjects'] : array();

                if (empty($subjects) || !is_array($subjects)) {
                    json_output(400, array('status' => 400, 'message' => 'At least one subject is required.'));
                    return;
                }

                // Get existing subjects
                $existing_group = $this->publicresultsubjectgroup_model->getByID($id);
                if (empty($existing_group)) {
                    json_output(404, array('status' => 404, 'message' => 'Subject group not found.'));
                    return;
                }

                $old_subjects = array();
                if (!empty($existing_group[0]->group_subject)) {
                    foreach ($existing_group[0]->group_subject as $value) {
                        $old_subjects[] = $value->subject_id;
                    }
                }

                $new_subject_ids = array_column($subjects, 'subject_id');
                $delete_subjects = array_diff($old_subjects, $new_subject_ids);
                $add_subjects = array_diff($new_subject_ids, $old_subjects);

                // Add new subjects
                if (!empty($add_subjects)) {
                    $subject_group_subject_Array = array();
                    
                    foreach ($subjects as $subject) {
                        if (in_array($subject['subject_id'], $add_subjects)) {
                            $subject_data = array(
                                'resultsubjects_id' => $id,
                                'subject_id' => $subject['subject_id'],
                                'session_id' => $this->setting_model->getCurrentSession(),
                                'minmarks' => isset($subject['minmarks']) ? $subject['minmarks'] : 0,
                                'maxmarks' => isset($subject['maxmarks']) ? $subject['maxmarks'] : 100
                            );
                            $subject_group_subject_Array[] = $subject_data;
                        }
                    }

                    if (!empty($subject_group_subject_Array)) {
                        $this->db->insert_batch('publicresultsubject_group_subjects', $subject_group_subject_Array);
                    }
                }

                // Update marks for all subjects
                foreach ($subjects as $subject) {
                    $this->publicresultsubjectgroup_model->updatemarks(
                        $id,
                        $subject['subject_id'],
                        isset($subject['minmarks']) ? $subject['minmarks'] : 0,
                        isset($subject['maxmarks']) ? $subject['maxmarks'] : 100
                    );
                }

                // Delete removed subjects
                $class_array = array('id' => $id);
                $this->publicresultsubjectgroup_model->edit($class_array, $delete_subjects, $add_subjects);

                json_output(200, array(
                    'status' => 200,
                    'message' => 'Subject group updated successfully.',
                    'data' => array(
                        'id' => $id,
                        'subjects_added' => count($add_subjects),
                        'subjects_removed' => count($delete_subjects)
                    )
                ));
            } else {
                json_output(401, array('status' => 401, 'message' => 'Client Service or Auth Key is invalid.'));
            }
        }
    }

    /**
     * Delete subject group
     * 
     * @param int $id Subject group ID
     * @return void Outputs JSON response with deletion status
     */
    public function delete_subject_group($id)
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'DELETE' && $method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            if ($this->validate_headers()) {
                if (empty($id)) {
                    json_output(400, array('status' => 400, 'message' => 'Subject group ID is required.'));
                    return;
                }

                // Check if subject group exists
                $existing_group = $this->publicresultsubjectgroup_model->getByID($id);
                if (empty($existing_group)) {
                    json_output(404, array('status' => 404, 'message' => 'Subject group not found.'));
                    return;
                }

                // Delete the subject group
                $this->publicresultsubjectgroup_model->remove($id);

                json_output(200, array(
                    'status' => 200,
                    'message' => 'Subject group deleted successfully.',
                    'data' => array('id' => $id)
                ));
            } else {
                json_output(401, array('status' => 401, 'message' => 'Client Service or Auth Key is invalid.'));
            }
        }
    }
}
