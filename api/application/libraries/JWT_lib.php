<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * JWT Library for Teacher Authentication
 * Simple JWT implementation for CodeIgniter
 */
class JWT_lib
{
    private $secret_key;
    private $algorithm;
    private $expiration_time;

    public function __construct()
    {
        $this->CI = &get_instance();
        
        // JWT Configuration - In production, store these in config files
        $this->secret_key = 'your-secret-key-change-this-in-production-2024';
        $this->algorithm = 'HS256';
        $this->expiration_time = 8760; // 365 days in hours
    }

    /**
     * Generate JWT Token
     */
    public function generate_token($payload)
    {
        $header = json_encode(['typ' => 'JWT', 'alg' => $this->algorithm]);
        
        // Add standard claims
        $payload['iat'] = time(); // Issued at
        $payload['exp'] = time() + ($this->expiration_time * 3600); // Expiration time
        $payload['iss'] = 'smartschool-api'; // Issuer
        
        $payload_encoded = json_encode($payload);
        
        $base64_header = $this->base64url_encode($header);
        $base64_payload = $this->base64url_encode($payload_encoded);
        
        $signature = hash_hmac('sha256', $base64_header . "." . $base64_payload, $this->secret_key, true);
        $base64_signature = $this->base64url_encode($signature);
        
        return $base64_header . "." . $base64_payload . "." . $base64_signature;
    }

    /**
     * Verify and Decode JWT Token
     */
    public function verify_token($token)
    {
        $token_parts = explode('.', $token);
        
        if (count($token_parts) !== 3) {
            return false;
        }
        
        list($header, $payload, $signature) = $token_parts;
        
        // Verify signature
        $expected_signature = hash_hmac('sha256', $header . "." . $payload, $this->secret_key, true);
        $expected_signature_encoded = $this->base64url_encode($expected_signature);
        
        if (!hash_equals($expected_signature_encoded, $signature)) {
            return false;
        }
        
        // Decode payload
        $payload_decoded = json_decode($this->base64url_decode($payload), true);
        
        if (!$payload_decoded) {
            return false;
        }
        
        // Check expiration
        if (isset($payload_decoded['exp']) && $payload_decoded['exp'] < time()) {
            return false;
        }
        
        return $payload_decoded;
    }

    /**
     * Refresh JWT Token
     */
    public function refresh_token($token)
    {
        $payload = $this->verify_token($token);
        
        if (!$payload) {
            return false;
        }
        
        // Remove old timestamps
        unset($payload['iat']);
        unset($payload['exp']);
        
        // Generate new token
        return $this->generate_token($payload);
    }

    /**
     * Extract payload without verification (for debugging)
     */
    public function decode_payload($token)
    {
        $token_parts = explode('.', $token);
        
        if (count($token_parts) !== 3) {
            return false;
        }
        
        $payload = $this->base64url_decode($token_parts[1]);
        return json_decode($payload, true);
    }

    /**
     * Base64 URL Encode
     */
    private function base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Base64 URL Decode
     */
    private function base64url_decode($data)
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }

    /**
     * Get token expiration time
     */
    public function get_expiration_time()
    {
        return $this->expiration_time;
    }

    /**
     * Set custom expiration time
     */
    public function set_expiration_time($hours)
    {
        $this->expiration_time = $hours;
    }

    /**
     * Validate token format
     */
    public function is_valid_format($token)
    {
        return count(explode('.', $token)) === 3;
    }

    /**
     * Get token remaining time in seconds
     */
    public function get_remaining_time($token)
    {
        $payload = $this->decode_payload($token);
        
        if (!$payload || !isset($payload['exp'])) {
            return 0;
        }
        
        $remaining = $payload['exp'] - time();
        return max(0, $remaining);
    }

    /**
     * Check if token is about to expire (within 1 hour)
     */
    public function is_token_expiring_soon($token)
    {
        $remaining = $this->get_remaining_time($token);
        return $remaining > 0 && $remaining < 3600; // Less than 1 hour
    }
}
