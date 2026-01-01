<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Generatepaper extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        // Only staff/admin with proper privilege should access this controller
        if (!$this->rbac->hasPrivilege('generate_paper', 'can_view')) {
            access_denied();
        }

        $this->load->model('Chatmessage_model');
    }

    public function index()
    {
        $this->session->set_userdata('top_menu', 'generate_paper');
        $this->session->set_userdata('sub_menu', 'admin/generatepaper');

        $data['title'] = 'Generate Paper';
        
        // Get school settings for PDF template
        $this->load->model('setting_model');
        $sch_setting = $this->setting_model->getSetting();
        $data['sch_setting'] = $sch_setting;

        // Get college logo
        $this->load->library('media_storage');
        $logo_url = '';
        $logo_path_base = FCPATH . 'uploads/school_content/logo/';
        if (is_dir($logo_path_base)) {
            $logo_files = glob($logo_path_base . '*.{png,jpg,jpeg}', GLOB_BRACE);
            if (!empty($logo_files)) {
                $logo_file = basename($logo_files[0]);
                $logo_url = $this->media_storage->getImageURL('uploads/school_content/logo/' . $logo_file);
            }
        }
        $data['logo_url'] = $logo_url;

        // Get Amaravathi student receipt header image (same as fee receipt)
        $receipt_header_url = '';
        if (method_exists($this->setting_model, 'get_receiptheader')) {
            $receipt_header = $this->setting_model->get_receiptheader();
            if (!empty($receipt_header)) {
                $receipt_header_url = $this->media_storage->getImageURL('/uploads/print_headerfooter/student_receipt/' . $receipt_header);
            }
        }
        $data['receipt_header_url'] = $receipt_header_url;

        $this->load->view('layout/header', $data);
        $this->load->view('admin/generatepaper', $data);
        $this->load->view('layout/footer', $data);
    }

    /**
     * Preview question paper as HTML template and allow HTML download
     */
    public function preview()
    {
        // Only allow POST requests
        if ($this->input->method() !== 'post') {
            show_error('Invalid request method', 405);
            return;
        }

        $paper_text = $this->input->post('paper_text');

        if (empty($paper_text)) {
            show_error('No question paper content provided', 400);
            return;
        }

        // Get school settings (same as index)
        $this->load->model('setting_model');
        $sch_setting = $this->setting_model->getSetting();

        // Get logo (same logic as index)
        $this->load->library('media_storage');
        $logo_url = '';
        $logo_path_base = FCPATH . 'uploads/school_content/logo/';
        if (is_dir($logo_path_base)) {
            $logo_files = glob($logo_path_base . '*.{png,jpg,jpeg}', GLOB_BRACE);
            if (!empty($logo_files)) {
                $logo_file = basename($logo_files[0]);
                $logo_url  = $this->media_storage->getImageURL('uploads/school_content/logo/' . $logo_file);
            }
        }

        // Get Amaravathi student receipt header image (same as fee receipt)
        $receipt_header_url = '';
        if (method_exists($this->setting_model, 'get_receiptheader')) {
            $receipt_header = $this->setting_model->get_receiptheader();
            if (!empty($receipt_header)) {
                $receipt_header_url = $this->media_storage->getImageURL('/uploads/print_headerfooter/student_receipt/' . $receipt_header);
            }
        }

        $data = array(
            'school_name'        => isset($sch_setting->name) ? $sch_setting->name : 'AMARAVATHI JUNIOR COLLEGE',
            'school_address'     => isset($sch_setting->address) ? $sch_setting->address : '',
            'logo_url'           => $logo_url,
            'receipt_header_url' => $receipt_header_url,
            // Pass raw text; view can convert or you can later switch to HTML
            'paper_text'         => $paper_text,
        );

        $this->load->view('admin/generatepaper/paper_view', $data);
    }

    /**
     * Save chat message via AJAX
     */
    public function save_message()
    {
        // Only allow POST requests
        if ($this->input->method() !== 'post') {
            $response = array(
                'status'  => 'error',
                'message' => 'Invalid request method'
            );
            echo json_encode($response);
            return;
        }

        // Get POST data
        $user_message    = $this->input->post('user_message');
        $conversation_id = $this->input->post('conversation_id');
        if (empty($conversation_id)) {
            $conversation_id = 'default';
        }

        // Validate input
        if (empty($user_message)) {
            $response = array(
                'status'  => 'error',
                'message' => 'User message is required'
            );
            echo json_encode($response);
            return;
        }

        // Call n8n webhook to get AI response (Production URL)
        $webhook_url = 'https://ai.alviongs.com/webhook/chat';

        $webhook_data = array(
            'message' => $user_message,
            'timestamp' => date('Y-m-d H:i:s'),
            'user' => 'admin'
        );

        // Initialize cURL
        $ch = curl_init($webhook_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($webhook_data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        // Execute request
        $webhook_response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        // Check for cURL errors
        if ($curl_error) {
            $response = array(
                'status'  => 'error',
                'message' => 'Failed to connect to AI agent: ' . $curl_error
            );
            echo json_encode($response);
            return;
        }

        // Check HTTP response code
        if ($http_code !== 200) {
            $response = array(
                'status'  => 'error',
                'message' => 'AI agent returned error code: ' . $http_code
            );
            echo json_encode($response);
            return;
        }

        // Parse webhook response
        $webhook_data_response = json_decode($webhook_response, true);

        // Extract AI reply from different possible response formats
        $ai_reply = '';
        if (is_array($webhook_data_response)) {
            if (isset($webhook_data_response['response'])) {
                $ai_reply = $webhook_data_response['response'];
            } elseif (isset($webhook_data_response['message'])) {
                $ai_reply = $webhook_data_response['message'];
            } elseif (isset($webhook_data_response['output'])) {
                $ai_reply = $webhook_data_response['output'];
            } elseif (isset($webhook_data_response['reply'])) {
                $ai_reply = $webhook_data_response['reply'];
            } else {
                $ai_reply = $webhook_response; // Use raw response if no known field
            }
        } else {
            $ai_reply = $webhook_response; // Use raw response if not JSON
        }

        // Validate AI reply
        if (empty($ai_reply)) {
            $ai_reply = 'AI agent returned empty response';
        }

        // Save to database
        try {
            $insert_id = $this->Chatmessage_model->save_message($user_message, $ai_reply, $conversation_id);

            if ($insert_id) {
                $response = array(
                    'status'          => 'success',
                    'message'         => 'Message saved successfully',
                    'ai_reply'        => $ai_reply,
                    'id'              => $insert_id,
                    'conversation_id' => $conversation_id,
                );
            } else {
                $response = array(
                    'status'  => 'error',
                    'message' => 'Failed to save message'
                );
            }
        } catch (Exception $e) {
            $response = array(
                'status'  => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            );
        }

        echo json_encode($response);
    }

    /**
     * Return list of conversations (for sidebar)
     */
    public function get_conversations()
    {
        try {
            $rows = $this->Chatmessage_model->get_conversations(100);

            $conversations = array();
            $counter = 1;
            foreach ($rows as $row) {
                $preview = isset($row['preview']) ? $row['preview'] : '';
                $title   = 'Conversation ' . $counter;
                $counter++;

                $conversations[] = array(
                    'conversation_id' => $row['conversation_id'],
                    'title'           => $title,
                    'preview'         => $preview,
                    'last_time'       => $row['last_time'],
                );
            }

            $response = array(
                'status'        => 'success',
                'conversations' => $conversations,
            );
        } catch (Exception $e) {
            $response = array(
                'status'  => 'error',
                'message' => 'Database error: ' . $e->getMessage(),
            );
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Return messages for a given conversation
     */
    public function get_conversation_messages()
    {
        $conversation_id = $this->input->get('conversation_id');
        if (empty($conversation_id)) {
            $conversation_id = 'default';
        }

        try {
            $rows = $this->Chatmessage_model->get_messages_by_conversation($conversation_id);

            $messages = array();
            foreach ($rows as $row) {
                $messages[] = array(
                    'id'           => isset($row['id']) ? (int) $row['id'] : 0,
                    'user_message' => isset($row['user_message']) ? $row['user_message'] : '',
                    'ai_reply'     => isset($row['ai_reply']) ? $row['ai_reply'] : '',
                    'created_at'   => isset($row['created_at']) ? $row['created_at'] : '',
                );
            }

            $response = array(
                'status'          => 'success',
                'conversation_id' => $conversation_id,
                'messages'        => $messages,
            );
        } catch (Exception $e) {
            $response = array(
                'status'  => 'error',
                'message' => 'Database error: ' . $e->getMessage(),
            );
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Handle file upload via AJAX
     */
    public function upload_file()
    {
        // Only allow POST requests
        if ($this->input->method() !== 'post') {
            $response = array(
                'status'  => 'error',
                'message' => 'Invalid request method'
            );
            echo json_encode($response);
            return;
        }

        // Check if file was uploaded
        if (empty($_FILES['file']['name'])) {
            $response = array(
                'status'  => 'error',
                'message' => 'No file uploaded'
            );
            echo json_encode($response);
            return;
        }

        // Create upload directory if it doesn't exist (mirror Homework::edit pattern)
        $uploaddir = './uploads/generatepaperpdf/';

        if (!is_dir($uploaddir)) {
            if (!mkdir($uploaddir, 0755, true)) {
                echo json_encode(array(
                    'status'  => 'error',
                    'message' => 'Upload failed: Unable to create upload directory'
                ));
                return;
            }

            // Security: prevent directory listing
            @file_put_contents($uploaddir . 'index.html', '');
        }

        // Build a safe unique filename preserving original name and extension with timestamp
        $original_name = isset($_FILES['file']['name']) ? $_FILES['file']['name'] : 'uploaded_file';
        $fileInfo = pathinfo($original_name);
        $extension = isset($fileInfo['extension']) && $fileInfo['extension'] !== '' ? '.' . $fileInfo['extension'] : '';
        $base_name = isset($fileInfo['filename']) ? $fileInfo['filename'] : 'uploaded_file';

        // Sanitize base name: allow letters, numbers, dash, underscore, dot; replace others with underscore
        $safe_base_name = preg_replace('/[^A-Za-z0-9\-_.]+/', '_', $base_name);

        // Append timestamp to keep it unique and traceable
        $timestamp = date('YmdHis');
        $stored_name = $safe_base_name . '_' . $timestamp . $extension;

        $destination = $uploaddir . $stored_name;

        if (move_uploaded_file($_FILES['file']['tmp_name'], $destination)) {
            $size_kb = file_exists($destination) ? round(filesize($destination) / 1024, 2) : 0;

            $response = array(
                'status'        => 'success',
                'message'       => 'File uploaded successfully',
                'file_name'     => $stored_name,
                'original_name' => $original_name,
                'file_size'     => $size_kb
            );
        } else {
            $response = array(
                'status'  => 'error',
                'message' => 'Upload failed: Unable to move uploaded file'
            );
        }

        echo json_encode($response);
    }

    /**
     * Generate PDF from selected messages - following Report.php pattern exactly
     */
    public function generate_pdf()
    {
        // Clear output buffers first
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        $is_embed = $this->input->post('embed') == 'true';
        
        try {
            // Get messages from POST data
            $messages = $this->input->post('messages');
            
            // Debug: Log received data
            error_log('PDF Generation - POST data: ' . print_r($_POST, true));
            error_log('PDF Generation - Raw messages: ' . print_r($messages, true));
            
            // Handle array format
            if (!is_array($messages)) {
                if (!empty($messages)) {
                    $messages = array($messages);
                } else {
                    $messages = array();
                }
            }
            
            // Filter out empty messages and clean them
            $messages = array_filter($messages, function($msg) {
                return !empty($msg) && trim($msg) !== '';
            });
            
            // Re-index array after filtering
            $messages = array_values($messages);
            
            // Debug: Log processed messages
            error_log('PDF Generation - Processed ' . count($messages) . ' messages');
            if (!empty($messages)) {
                error_log('PDF Generation - First message preview: ' . substr($messages[0], 0, 100));
            }
            
            // If no messages, return error
            if (empty($messages)) {
                if ($is_embed) {
                    header('Content-Type: application/json');
                    echo json_encode(array(
                        'status' => 'error',
                        'message' => 'No messages selected to generate PDF.'
                    ));
                    return;
                } else {
                    show_error('No messages selected to generate PDF.', 400);
                    return;
                }
            }

            // Get school settings (like TC generation does)
            $this->load->model('setting_model');
            $sch_setting = $this->setting_model->getSetting();
            
            // Get college logo using media_storage (like TC generation)
            $this->load->library('media_storage');
            $logo_url = '';
            
            // Try to get logo from school content
            $logo_path = FCPATH . 'uploads/school_content/logo/';
            if (is_dir($logo_path)) {
                $logo_files = glob($logo_path . '*.png');
                if (empty($logo_files)) {
                    $logo_files = glob($logo_path . '*.jpg');
                }
                if (empty($logo_files)) {
                    $logo_files = glob($logo_path . '*.jpeg');
                }
                if (!empty($logo_files)) {
                    $logo_file = basename($logo_files[0]);
                    $logo_url = $this->media_storage->getImageURL('uploads/school_content/logo/' . $logo_file);
                }
            }

            // Prepare data for view template (same pattern as Report.php and TC generation)
            $data = array(
                'messages' => $messages,
                'logo_url' => $logo_url,
                'sch_setting' => $sch_setting
            );
            
            // Load view template and get HTML (same pattern as Report.php line 33)
            $html = $this->load->view('admin/generatepaper/question_paper_pdf', $data, true);
            
            if (empty($html)) {
                throw new Exception('View template returned empty HTML');
            }

            // Return HTML for client-side PDF generation
            // This approach is more reliable than server-side PDF generation
            if ($is_embed) {
                // Clear output buffers
                while (ob_get_level()) {
                    ob_end_clean();
                }
                
                // Return HTML for client-side PDF generation
                header('Content-Type: application/json');
                echo json_encode(array(
                    'status' => 'success',
                    'format' => 'html',
                    'html' => base64_encode($html),
                    'filename' => 'question_paper_' . date('YmdHis') . '.pdf'
                ));
                exit;
            } else {
                // For direct access, show HTML
                echo $html;
            }
            
        } catch (Exception $e) {
            // Clear output buffers
            while (ob_get_level()) {
                ob_end_clean();
            }
            
            error_log('PDF Generation Exception: ' . $e->getMessage());
            error_log('PDF Generation File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            error_log('PDF Generation Stack: ' . $e->getTraceAsString());
            
            if ($is_embed) {
                // Disable CodeIgniter output
                $this->output->_display = false;
                
                // Clear all output
                while (ob_get_level()) {
                    ob_end_clean();
                }
                
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(200);
                $error_response = array(
                    'status' => 'error',
                    'message' => 'PDF generation failed: ' . $e->getMessage(),
                    'file' => basename($e->getFile()),
                    'line' => $e->getLine()
                );
                echo json_encode($error_response, JSON_UNESCAPED_UNICODE);
                exit;
            } else {
                show_error('PDF generation failed: ' . $e->getMessage(), 500);
            }
        } catch (Error $e) {
            // Clear output buffers
            while (ob_get_level()) {
                ob_end_clean();
            }
            
            error_log('PDF Generation Fatal Error: ' . $e->getMessage());
            error_log('PDF Generation File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            error_log('PDF Generation Stack: ' . $e->getTraceAsString());
            
            if ($is_embed) {
                // Disable CodeIgniter output
                $this->output->_display = false;
                
                // Clear all output
                while (ob_get_level()) {
                    ob_end_clean();
                }
                
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(200);
                $error_response = array(
                    'status' => 'error',
                    'message' => 'PDF generation failed: ' . $e->getMessage(),
                    'file' => basename($e->getFile()),
                    'line' => $e->getLine()
                );
                echo json_encode($error_response, JSON_UNESCAPED_UNICODE);
                exit;
            } else {
                show_error('PDF generation failed: ' . $e->getMessage(), 500);
            }
        } catch (Throwable $e) {
            // Clear output buffers
            while (ob_get_level()) {
                ob_end_clean();
            }
            
            error_log('PDF Generation Throwable: ' . $e->getMessage());
            error_log('PDF Generation File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            
            if ($is_embed) {
                // Disable CodeIgniter output
                $this->output->_display = false;
                
                // Clear all output
                while (ob_get_level()) {
                    ob_end_clean();
                }
                
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(200);
                $error_response = array(
                    'status' => 'error',
                    'message' => 'PDF generation failed: ' . $e->getMessage(),
                    'file' => basename($e->getFile()),
                    'line' => $e->getLine()
                );
                echo json_encode($error_response, JSON_UNESCAPED_UNICODE);
                exit;
            } else {
                show_error('PDF generation failed: ' . $e->getMessage(), 500);
            }
        }
    }
}

