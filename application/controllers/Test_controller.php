<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test_controller extends CI_Controller {
    
    public function test_fee_report() {
        // Test basic functionality
        $this->load->model('studentfeemaster_model');
        
        echo "Testing fee collection report...\n";
        
        // Test with minimal parameters
        try {
            $start_date = '2024-01-01';
            $end_date = '2024-12-31';
            
            $result = $this->studentfeemaster_model->getFeeCollectionReportColumnwise($start_date, $end_date);
            
            echo "Model method works! Result count: " . count($result) . "\n";
            
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
    }
}
