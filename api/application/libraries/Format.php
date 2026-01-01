<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Minimal Format helper used by REST_Controller::response
 * Supports JSON formatting needed by controllers in this project.
 */

if (!function_exists('format_json')) {
    function format_json($data)
    {
        if ($data === null) {
            return '';
        }
        return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}

if (!function_exists('format_xml')) {
    function format_xml($data)
    {
        // Simple XML conversion for arrays/objects (best-effort)
        $xml = new SimpleXMLElement('<response/>');
        $convert = function ($data, &$xml) use (&$convert) {
            foreach ($data as $key => $value) {
                if (is_array($value) || is_object($value)) {
                    $child = $xml->addChild(is_string($key) ? $key : 'item');
                    $convert((array)$value, $child);
                } else {
                    $xml->addChild(is_string($key) ? $key : 'item', htmlspecialchars((string)$value));
                }
            }
        };
        $convert((array)$data, $xml);
        return $xml->asXML();
    }
}
