<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . "third_party/omnipay/vendor/autoload.php";

use Mpdf\Mpdf;

class M_pdf
{
    public $pdf;

    public function __construct()
    {
        $CI = &get_instance();
        log_message('Debug', 'mPDF class is loaded.');
        
        // Initialize pdf property for compatibility with Report.php usage pattern
        // Use try-catch to handle any initialization errors
        try {
            $this->pdf = $this->load();
        } catch (Exception $e) {
            log_message('error', 'mPDF initialization error: ' . $e->getMessage());
            // Don't throw here, let it be handled when pdf is accessed
        }
    }

    public function load($param = null)
    {
        if ($param === null) {
            // Default settings for Report.php compatibility
            // Use dejavusans which is more reliable and comes with mPDF
            try {
                return new Mpdf([
                    'mode' => 'utf-8',
                    'default_font' => 'dejavusans',
                    'margin_left' => 2,
                    'margin_right' => 2,
                    'margin_top' => 2,
                    'margin_bottom' => 2,
                    'format' => 'Legal'
                ]);
            } catch (Exception $e) {
                // Fallback: try without specifying font
                return new Mpdf([
                    'mode' => 'utf-8',
                    'margin_left' => 2,
                    'margin_right' => 2,
                    'margin_top' => 2,
                    'margin_bottom' => 2,
                    'format' => 'Legal'
                ]);
            }
        } else {
            // Custom settings
            return new Mpdf($param);
        }
    }
}
