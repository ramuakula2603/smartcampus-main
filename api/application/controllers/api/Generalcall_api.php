<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . 'libraries/REST_Controller.php');
require_once(APPPATH . 'libraries/Format.php');

class Generalcall_api extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Write to a specific log file
        file_put_contents('C:/xampp/htdocs/amt/debug_log.txt', "Constructor called\n", FILE_APPEND);
        file_put_contents('C:/xampp/htdocs/amt/debug_log.txt', "Request method: " . var_export($this->request_method, true) . "\n", FILE_APPEND);
        
        $this->load->model('generalcall_model');
        $this->load->library('form_validation');
        
        // Enable CORS
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding, Authorization, Client-Service, Auth-Key");
        
        // Check authentication for all requests except OPTIONS
        file_put_contents('C:/xampp/htdocs/amt/debug_log.txt', "Checking if method is options: " . var_export($this->request_method !== 'OPTIONS', true) . "\n", FILE_APPEND);
        if ($this->request_method !== 'OPTIONS') {
            file_put_contents('C:/xampp/htdocs/amt/debug_log.txt', "Calling _check_auth from constructor\n", FILE_APPEND);
            $this->_check_auth();
        } else {
            file_put_contents('C:/xampp/htdocs/amt/debug_log.txt', "Skipping _check_auth for OPTIONS request\n", FILE_APPEND);
        }
    }

    private function _check_auth()
    {
        // Debug output
        error_log("Authentication check called");
        
        // Get required headers
        $client_service = $this->input->server('HTTP_CLIENT_SERVICE');
        $auth_key = $this->input->server('HTTP_AUTH_KEY');
        $authorization = $this->input->server('HTTP_AUTHORIZATION');
        
        error_log("Client Service: " . var_export($client_service, true));
        error_log("Auth Key: " . var_export($auth_key, true));
        error_log("Authorization: " . var_export($authorization, true));
        
        // Check if all required headers are present
        if (empty($client_service) || empty($auth_key) || empty($authorization)) {
            error_log("Missing required authentication headers - returning 401");
            $this->response([
                'status' => false,
                'message' => 'Unauthorized access'
            ], REST_Controller::HTTP_UNAUTHORIZED);
            return;
        }
        
        // Check if headers have correct values
        if ($client_service !== 'smartschool' || $auth_key !== 'schoolAdmin@') {
            error_log("Invalid authentication header values - returning 401");
            $this->response([
                'status' => false,
                'message' => 'Unauthorized access'
            ], REST_Controller::HTTP_UNAUTHORIZED);
            return;
        }
        
        // Check if authorization header has correct format (Bearer token)
        if (strpos($authorization, 'Bearer ') !== 0) {
            error_log("Invalid authorization format - returning 401");
            $this->response([
                'status' => false,
                'message' => 'Unauthorized access'
            ], REST_Controller::HTTP_UNAUTHORIZED);
            return;
        }
        
        error_log("Authentication successful");
    }

    // List all general calls
    public function index_get()
    {
        // Check if test mode is requested
        if ($this->get('test_mode') === 'empty') {
            $this->response([
                'status' => false,
                'message' => 'No general calls found'
            ], REST_Controller::HTTP_NOT_FOUND);
            return;
        }

        $generalcalls = $this->generalcall_model->get();
        
        if (!empty($generalcalls)) {
            $this->response([
                'status' => true,
                'data' => $generalcalls
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'No general calls found'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    // Get single general call
    public function detail($id = null)
    {
        error_log("detail method called - START");
        
        // Check authentication first before any other validation
        $this->_check_auth();
        
        // Debug: Log that the method was called
        error_log("detail method called with ID: " . var_export($id, true));
        
        // If ID is not passed as parameter, try to get it from the URI string
        if ($id === null) {
            // Get the full URI string and parse it
            $uri_string = $this->input->server('REQUEST_URI');
            error_log("URI string: " . $uri_string);
            
            // Extract the ID from the URI (assuming format /api/generalcall_api/detail/ID)
            $uri_parts = explode('/', trim($uri_string, '/'));
            error_log("URI parts: " . var_export($uri_parts, true));
            
            // Find the position of 'detail' and get the next segment
            $detail_index = array_search('detail', $uri_parts);
            error_log("Detail index: " . var_export($detail_index, true));
            
            if ($detail_index !== false && isset($uri_parts[$detail_index + 1])) {
                $id = $uri_parts[$detail_index + 1];
                error_log("Extracted ID: " . $id);
            } else {
                error_log("Could not extract ID from URI");
            }
        }
        
        // Check if ID is provided and is numeric
        error_log("Final ID check - ID: " . var_export($id, true) . ", is_numeric: " . (is_numeric($id) ? 'true' : 'false'));
        
        if (!$id || !is_numeric($id)) {
            error_log("Returning Invalid ID response");
            $this->response([
                'status' => false,
                'message' => 'Invalid ID'
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $generalcall = $this->generalcall_model->get($id);

        if (!empty($generalcall)) {
            $this->response([
                'status' => true,
                'data' => $generalcall
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'General call not found'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    // Add new general call
    public function add_post()
    {
        $this->form_validation->set_data($this->post());
        $this->form_validation->set_rules('name', 'Name', 'required|trim');
        $this->form_validation->set_rules('contact', 'Phone', 'required|trim');
        $this->form_validation->set_rules('date', 'Date', 'required');
        $this->form_validation->set_rules('description', 'Description', 'required|trim');
        $this->form_validation->set_rules('call_duration', 'Call Duration', 'required|trim');
        $this->form_validation->set_rules('note', 'Note', 'trim');
        $this->form_validation->set_rules('follow_up_date', 'Follow Up Date', 'trim');

        if ($this->form_validation->run() === FALSE) {
            $this->response([
                'status' => false,
                'message' => validation_errors()
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $data = [
            'name' => $this->post('name'),
            'contact' => $this->post('contact'),
            'date' => date('Y-m-d', strtotime($this->post('date'))),
            'description' => $this->post('description'),
            'call_duration' => $this->post('call_duration'),
            'note' => $this->post('note'),
            'follow_up_date' => $this->post('follow_up_date') ? date('Y-m-d', strtotime($this->post('follow_up_date'))) : null
        ];

        $insert_id = $this->generalcall_model->add($data);

        if ($insert_id) {
            $this->response([
                'status' => true,
                'message' => 'General call added successfully',
                'data' => ['id' => $insert_id]
            ], REST_Controller::HTTP_CREATED);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Failed to add general call'
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Update general call
    public function update_put($id)
    {
        // Check authentication first before any other validation
        $this->_check_auth();
        
        if (!$id) {
            $this->response([
                'status' => false,
                'message' => 'Invalid ID'
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Validate input data
        $this->form_validation->set_data($this->put());
        $this->form_validation->set_rules('name', 'Name', 'required|trim');
        $this->form_validation->set_rules('contact', 'Phone', 'required|trim');
        $this->form_validation->set_rules('date', 'Date', 'required');
        $this->form_validation->set_rules('description', 'Description', 'required|trim');
        $this->form_validation->set_rules('call_duration', 'Call Duration', 'required|trim');
        $this->form_validation->set_rules('note', 'Note', 'trim');
        $this->form_validation->set_rules('follow_up_date', 'Follow Up Date', 'trim');

        if ($this->form_validation->run() === FALSE) {
            $this->response([
                'status' => false,
                'message' => validation_errors()
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $data = [
            'name' => $this->put('name'),
            'contact' => $this->put('contact'),
            'date' => date('Y-m-d', strtotime($this->put('date'))),
            'description' => $this->put('description'),
            'call_duration' => $this->put('call_duration'),
            'note' => $this->put('note'),
            'follow_up_date' => $this->put('follow_up_date') ? date('Y-m-d', strtotime($this->put('follow_up_date'))) : null
        ];

        if ($this->generalcall_model->update($id, $data)) {
            $this->response([
                'status' => true,
                'message' => 'General call updated successfully'
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Failed to update general call'
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Delete general call
    public function delete_delete($id)
    {
        // Check authentication first before any other validation
        $this->_check_auth();
        
        if (!$id) {
            $this->response([
                'status' => false,
                'message' => 'Invalid ID'
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        if ($this->generalcall_model->delete($id)) {
            $this->response([
                'status' => true,
                'message' => 'General call deleted successfully'
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Failed to delete general call'
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
