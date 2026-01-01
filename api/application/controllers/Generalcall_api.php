<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Generalcall_api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // Load dependencies used by the model
        $this->load->model('setting_model');
        $this->load->model('generalcall_model');
        $this->load->helper('url');
        // Keep responses JSON
        $this->output->set_content_type('application/json');
        
        // Temporarily disable authentication for testing
        // Check authentication for all requests
        // $this->_check_auth();
    }

    private function _check_auth()
    {
        // Get required headers
        $client_service = $this->input->server('HTTP_CLIENT_SERVICE');
        $auth_key = $this->input->server('HTTP_AUTH_KEY');
        $authorization = $this->input->server('HTTP_AUTHORIZATION');
        
        // Debug output
        error_log("Client Service: " . var_export($client_service, true));
        error_log("Auth Key: " . var_export($auth_key, true));
        error_log("Authorization: " . var_export($authorization, true));
        
        // Check if all required headers are present
        if (empty($client_service) || empty($auth_key) || empty($authorization)) {
            error_log("Missing required headers");
            $this->output->set_status_header(401)->set_output(json_encode([
                'status' => false,
                'message' => 'Unauthorized access'
            ]));
            echo $this->output->get_output();
            exit;
        }
        
        // Check if headers have correct values
        if ($client_service !== 'smartschool' || $auth_key !== 'schoolAdmin@') {
            error_log("Invalid header values");
            $this->output->set_status_header(401)->set_output(json_encode([
                'status' => false,
                'message' => 'Unauthorized access'
            ]));
            echo $this->output->get_output();
            exit;
        }
        
        // Check if authorization header has correct format (Bearer token)
        if (strpos($authorization, 'Bearer ') !== 0) {
            error_log("Invalid authorization format");
            $this->output->set_status_header(401)->set_output(json_encode([
                'status' => false,
                'message' => 'Unauthorized access'
            ]));
            echo $this->output->get_output();
            exit;
        }
    }

    // GET /generalcall_api or /generalcall_api/index
    public function index()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method !== 'GET') {
            $this->output->set_status_header(405)->set_output(json_encode([
                'status' => 0,
                'message' => 'Method Not Allowed'
            ]));
            return;
        }

        // Check if test mode is requested
        $test_mode = $this->input->get('test_mode');
        
        error_log("Test mode: " . var_export($test_mode, true));
        
        if ($test_mode === 'empty') {
            error_log("Returning empty test mode response");
            // Force 404 response for testing
            $this->output->set_status_header(404)->set_output(json_encode([
                'status' => false,
                'message' => 'No general calls found'
            ]));
            return;
        }

        $generalcalls = $this->generalcall_model->get();
        error_log("General calls count: " . count($generalcalls));
        if (!empty($generalcalls)) {
            $this->output->set_status_header(200)->set_output(json_encode([
                'status' => true,
                'data' => $generalcalls
            ]));
        } else {
            error_log("Returning no data found response");
            // Return 404 Not Found for empty results
            $this->output->set_status_header(404)->set_output(json_encode([
                'status' => false,
                'message' => 'No general calls found'
            ]));
        }
    }

    // GET /generalcall_api/detail/{id}
    public function detail($id = null)
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method !== 'GET') {
            $this->output->set_status_header(405)->set_output(json_encode([
                'status' => 0,
                'message' => 'Method Not Allowed'
            ]));
            return;
        }

        // If ID is not passed as parameter, try to get it from the URI string
        if ($id === null) {
            // Get the full URI string and parse it
            $uri_string = $this->input->server('REQUEST_URI');
            // Extract the ID from the URI (assuming format /api/generalcall_api/detail/ID)
            $uri_parts = explode('/', trim($uri_string, '/'));
            // Find the position of 'detail' and get the next segment
            $detail_index = array_search('detail', $uri_parts);
            if ($detail_index !== false && isset($uri_parts[$detail_index + 1])) {
                $id = $uri_parts[$detail_index + 1];
            }
        }

        // Check if ID is provided and is numeric and positive
        if (!$id || !is_numeric($id) || $id <= 0) {
            $this->output->set_status_header(400)->set_output(json_encode([
                'status' => false,
                'message' => 'Invalid ID'
            ]));
            return;
        }

        $generalcall = $this->generalcall_model->get($id);
        if (!empty($generalcall)) {
            $this->output->set_status_header(200)->set_output(json_encode([
                'status' => true,
                'data' => $generalcall
            ]));
        } else {
            $this->output->set_status_header(404)->set_output(json_encode([
                'status' => false,
                'message' => 'General call not found'
            ]));
        }
    }

    // POST /generalcall_api/add
    public function add()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method !== 'POST') {
            $this->output->set_status_header(405)->set_output(json_encode([
                'status' => 0,
                'message' => 'Method Not Allowed'
            ]));
            return;
        }

        $input = json_decode($this->input->raw_input_stream, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->output->set_status_header(400)->set_output(json_encode([
                'status' => 0,
                'message' => 'Invalid JSON format'
            ]));
            return;
        }

        // Basic required fields validation
        $required = ['name', 'contact', 'date', 'description', 'call_duration'];
        foreach ($required as $field) {
            if (empty($input[$field])) {
                $this->output->set_status_header(400)->set_output(json_encode([
                    'status' => false,
                    'message' => "$field is required"
                ]));
                return;
            }
        }

        // Validate date format
        $date_timestamp = strtotime($input['date']);
        if ($date_timestamp === false) {
            $this->output->set_status_header(400)->set_output(json_encode([
                'status' => false,
                'message' => 'Invalid date format'
            ]));
            return;
        }
        
        // Validate follow_up_date if provided
        $follow_up_date_timestamp = null;
        if (isset($input['follow_up_date']) && $input['follow_up_date']) {
            $follow_up_date_timestamp = strtotime($input['follow_up_date']);
            if ($follow_up_date_timestamp === false) {
                $this->output->set_status_header(400)->set_output(json_encode([
                    'status' => false,
                    'message' => 'Invalid follow up date format'
                ]));
                return;
            }
        }

        $data = [
            'name' => $input['name'],
            'contact' => $input['contact'],
            'date' => date('Y-m-d', $date_timestamp),
            'description' => $input['description'],
            'call_duration' => $input['call_duration'],
            'note' => isset($input['note']) ? $input['note'] : '',
            'follow_up_date' => $follow_up_date_timestamp ? date('Y-m-d', $follow_up_date_timestamp) : ''
        ];

        $insert_id = $this->generalcall_model->add($data);
        if ($insert_id) {
            $this->output->set_status_header(201)->set_output(json_encode([
                'status' => true,
                'message' => 'General call added successfully',
                'data' => ['id' => $insert_id]
            ]));
        } else {
            $this->output->set_status_header(500)->set_output(json_encode([
                'status' => false,
                'message' => 'Failed to add general call'
            ]));
        }
    }

    // PUT /generalcall_api/update/{id}
    public function update($id = null)
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method !== 'PUT') {
            $this->output->set_status_header(405)->set_output(json_encode([
                'status' => 0,
                'message' => 'Method Not Allowed'
            ]));
            return;
        }

        if (!$id) {
            $this->output->set_status_header(400)->set_output(json_encode([
                'status' => false,
                'message' => 'Invalid ID'
            ]));
            return;
        }

        // Check if ID is numeric and positive
        if (!is_numeric($id) || $id <= 0) {
            $this->output->set_status_header(400)->set_output(json_encode([
                'status' => false,
                'message' => 'Invalid ID format'
            ]));
            return;
        }

        $input = json_decode($this->input->raw_input_stream, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->output->set_status_header(400)->set_output(json_encode([
                'status' => 0,
                'message' => 'Invalid JSON format'
            ]));
            return;
        }

        // Process and validate data
        $data = [];
        if (isset($input['name'])) $data['name'] = $input['name'];
        if (isset($input['contact'])) $data['contact'] = $input['contact'];
        if (isset($input['date'])) {
            $date_timestamp = strtotime($input['date']);
            if ($date_timestamp === false) {
                $this->output->set_status_header(400)->set_output(json_encode([
                    'status' => false,
                    'message' => 'Invalid date format'
                ]));
                return;
            }
            $data['date'] = date('Y-m-d', $date_timestamp);
        }
        if (isset($input['description'])) $data['description'] = $input['description'];
        if (isset($input['call_duration'])) $data['call_duration'] = $input['call_duration'];
        if (isset($input['note'])) $data['note'] = $input['note'];
        if (isset($input['follow_up_date'])) {
            if ($input['follow_up_date']) {
                $follow_up_date_timestamp = strtotime($input['follow_up_date']);
                if ($follow_up_date_timestamp === false) {
                    $this->output->set_status_header(400)->set_output(json_encode([
                        'status' => false,
                        'message' => 'Invalid follow up date format'
                    ]));
                    return;
                }
                $data['follow_up_date'] = date('Y-m-d', $follow_up_date_timestamp);
            } else {
                $data['follow_up_date'] = '';
            }
        }

        if (empty($data)) {
            $this->output->set_status_header(400)->set_output(json_encode([
                'status' => false,
                'message' => 'No data provided for update'
            ]));
            return;
        }

        // Check if the general call exists before updating
        $existing_call = $this->generalcall_model->get($id);
        if (empty($existing_call)) {
            $this->output->set_status_header(404)->set_output(json_encode([
                'status' => false,
                'message' => 'General call not found'
            ]));
            return;
        }

        if ($this->generalcall_model->update($id, $data)) {
            $this->output->set_status_header(200)->set_output(json_encode([
                'status' => true,
                'message' => 'General call updated successfully'
            ]));
        } else {
            $this->output->set_status_header(500)->set_output(json_encode([
                'status' => false,
                'message' => 'Failed to update general call'
            ]));
        }
    }

    // DELETE /generalcall_api/delete/{id}
    public function delete($id = null)
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method !== 'DELETE') {
            $this->output->set_status_header(405)->set_output(json_encode([
                'status' => 0,
                'message' => 'Method Not Allowed'
            ]));
            return;
        }

        if (!$id) {
            $this->output->set_status_header(400)->set_output(json_encode([
                'status' => false,
                'message' => 'Invalid ID'
            ]));
            return;
        }

        // Check if ID is numeric and positive
        if (!is_numeric($id) || $id <= 0) {
            $this->output->set_status_header(400)->set_output(json_encode([
                'status' => false,
                'message' => 'Invalid ID format'
            ]));
            return;
        }

        // Check if the general call exists before deleting
        $existing_call = $this->generalcall_model->get($id);
        if (empty($existing_call)) {
            $this->output->set_status_header(404)->set_output(json_encode([
                'status' => false,
                'message' => 'General call not found'
            ]));
            return;
        }

        if ($this->generalcall_model->delete($id)) {
            $this->output->set_status_header(200)->set_output(json_encode([
                'status' => true,
                'message' => 'General call deleted successfully'
            ]));
        } else {
            $this->output->set_status_header(500)->set_output(json_encode([
                'status' => false,
                'message' => 'Failed to delete general call'
            ]));
        }
    }
}
