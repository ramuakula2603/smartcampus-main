<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * REST_Controller
 *
 * Lightweight implementation of CodeIgniter Rest Server features required by
 * this project. It provides helpers for request parsing and response formatting.
 * This file is based on the public domain/ MIT implementations but trimmed to
 * include only the necessary functionality (JSON parsing/formatting and simple
 * response helpers).
 */
require_once APPPATH . 'libraries/Format.php';

class REST_Controller extends CI_Controller
{
    // HTTP status codes
    const HTTP_OK = 200;
    const HTTP_CREATED = 201;
    const HTTP_NO_CONTENT = 204;
    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_FORBIDDEN = 403;
    const HTTP_NOT_FOUND = 404;
    const HTTP_INTERNAL_SERVER_ERROR = 500;

    /**
     * Request method (GET, POST, PUT, DELETE)
     * @var string
     */
    protected $request_method;

    /**
     * Parsed request parameters (GET/POST/PUT/DELETE)
     * @var array
     */
    protected $request_params = [];

    public function __construct()
    {
        parent::__construct();

        // determine request method
        $this->request_method = $this->input->server('REQUEST_METHOD');

        // populate params depending on method
        $this->_parse_request();
    }

    protected function _parse_request()
    {
        $method = strtoupper($this->request_method);
        if ($method === 'GET') {
            $this->request_params = $this->input->get() ?: [];
        } elseif ($method === 'POST') {
            $post = $this->input->post();
            if (empty($post)) {
                $raw = trim($this->input->raw_input_stream);
                if (!empty($raw)) {
                    $decoded = json_decode($raw, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $this->request_params = $decoded;
                    } else {
                        parse_str($raw, $this->request_params);
                    }
                }
            } else {
                $this->request_params = $post;
            }
        } else {
            // PUT, DELETE, PATCH
            $raw = trim($this->input->raw_input_stream);
            if (!empty($raw)) {
                $decoded = json_decode($raw, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $this->request_params = $decoded;
                } else {
                    parse_str($raw, $this->request_params);
                }
            }
        }
    }

    // helpers to get input values similarly to original REST_Controller
    public function post($key = null)
    {
        if ($this->request_method !== 'POST') {
            return null;
        }
        if ($key === null) return $this->request_params;
        return isset($this->request_params[$key]) ? $this->request_params[$key] : null;
    }

    public function get($key = null)
    {
        if ($this->request_method !== 'GET') {
            // still return GET params if present
        }
        $params = $this->input->get() ?: [];
        if ($key === null) return $params;
        return isset($params[$key]) ? $params[$key] : null;
    }

    public function put($key = null)
    {
        if ($this->request_method !== 'PUT') {
            // allow access regardless
        }
        if ($key === null) return $this->request_params;
        return isset($this->request_params[$key]) ? $this->request_params[$key] : null;
    }

    public function delete($key = null)
    {
        if ($this->request_method !== 'DELETE') {
            // allow access regardless
        }
        if ($key === null) return $this->request_params;
        return isset($this->request_params[$key]) ? $this->request_params[$key] : null;
    }

    /**
     * Send response using Format library
     * @param mixed $data
     * @param int $http_code
     */
    public function response($data = null, $http_code = self::HTTP_OK)
    {
        // set status header
        if (function_exists('set_status_header')) {
            set_status_header($http_code);
        } else {
            http_response_code($http_code);
        }

        // default to JSON
        header('Content-Type: application/json; charset=utf-8');
        if (is_string($data)) {
            echo $data;
        } else {
            echo format_json($data);
        }
        exit;
    }
}
