<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Biometric_log_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Log incoming device request
     * 
     * @param array $data Request data
     * @return int Insert ID
     */
    public function logDeviceRequest($data)
    {
        $log_data = array(
            'request_method'    => isset($data['request_method']) ? $data['request_method'] : null,
            'request_uri'       => isset($data['request_uri']) ? $data['request_uri'] : null,
            'query_string'      => isset($data['query_string']) ? json_encode($data['query_string']) : null,
            'raw_body'          => isset($data['raw_body']) ? $data['raw_body'] : null,
            'parsed_data'       => isset($data['parsed_data']) ? json_encode($data['parsed_data']) : null,
            'device_sn'         => isset($data['device_sn']) ? $data['device_sn'] : null,
            'ip_address'        => isset($data['ip_address']) ? $data['ip_address'] : null,
            'user_agent'        => isset($data['user_agent']) ? $data['user_agent'] : null,
            'processing_status' => isset($data['processing_status']) ? $data['processing_status'] : 'pending',
            'error_message'     => isset($data['error_message']) ? $data['error_message'] : null,
            'records_processed' => isset($data['records_processed']) ? $data['records_processed'] : 0,
        );

        $this->db->insert('biometric_device_logs', $log_data);
        return $this->db->insert_id();
    }

    /**
     * Update device log status
     * 
     * @param int $log_id Log ID
     * @param array $data Update data
     * @return bool
     */
    public function updateLogStatus($log_id, $data)
    {
        $update_data = array();
        
        if (isset($data['processing_status'])) {
            $update_data['processing_status'] = $data['processing_status'];
        }
        
        if (isset($data['error_message'])) {
            $update_data['error_message'] = $data['error_message'];
        }
        
        if (isset($data['records_processed'])) {
            $update_data['records_processed'] = $data['records_processed'];
        }

        if (!empty($update_data)) {
            $this->db->where('id', $log_id);
            return $this->db->update('biometric_device_logs', $update_data);
        }
        
        return false;
    }

    /**
     * Log raw attendance record
     * 
     * @param array $data Attendance data
     * @return int Insert ID
     */
    public function logRawAttendance($data)
    {
        $attendance_data = array(
            'device_log_id' => isset($data['device_log_id']) ? $data['device_log_id'] : null,
            'device_sn'     => isset($data['device_sn']) ? $data['device_sn'] : null,
            'table_type'    => isset($data['table_type']) ? $data['table_type'] : null,
            'stamp'         => isset($data['stamp']) ? $data['stamp'] : null,
            'employee_id'   => isset($data['employee_id']) ? $data['employee_id'] : null,
            'punch_time'    => isset($data['punch_time']) ? $data['punch_time'] : null,
            'status1'       => isset($data['status1']) ? $data['status1'] : null,
            'status2'       => isset($data['status2']) ? $data['status2'] : null,
            'status3'       => isset($data['status3']) ? $data['status3'] : null,
            'status4'       => isset($data['status4']) ? $data['status4'] : null,
            'status5'       => isset($data['status5']) ? $data['status5'] : null,
            'processed'     => isset($data['processed']) ? $data['processed'] : 0,
        );

        $this->db->insert('biometric_raw_attendance', $attendance_data);
        return $this->db->insert_id();
    }

    /**
     * Mark raw attendance as processed
     * 
     * @param int $raw_id Raw attendance ID
     * @return bool
     */
    public function markAsProcessed($raw_id)
    {
        $this->db->where('id', $raw_id);
        return $this->db->update('biometric_raw_attendance', array(
            'processed' => 1,
            'processed_at' => date('Y-m-d H:i:s')
        ));
    }

    /**
     * Get device logs with filters
     *
     * @param array $filters Filter parameters
     * @param int $limit Limit
     * @param int $offset Offset
     * @return array
     */
    public function getDeviceLogs($filters = array(), $limit = 100, $offset = 0)
    {
        $this->db->select('*');
        $this->db->from('biometric_device_logs');

        if (isset($filters['device_sn'])) {
            $this->db->where('device_sn', $filters['device_sn']);
        }

        if (isset($filters['processing_status'])) {
            $this->db->where('processing_status', $filters['processing_status']);
        }

        if (isset($filters['date_from'])) {
            $this->db->where('created_at >=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $this->db->where('created_at <=', $filters['date_to']);
        }

        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit, $offset);

        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Get device logs count with filters
     *
     * @param array $filters Filter parameters
     * @return int
     */
    public function getDeviceLogsCount($filters = array())
    {
        $this->db->from('biometric_device_logs');

        if (isset($filters['device_sn'])) {
            $this->db->where('device_sn', $filters['device_sn']);
        }

        if (isset($filters['processing_status'])) {
            $this->db->where('processing_status', $filters['processing_status']);
        }

        if (isset($filters['date_from'])) {
            $this->db->where('created_at >=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $this->db->where('created_at <=', $filters['date_to']);
        }

        return $this->db->count_all_results();
    }

    /**
     * Get raw attendance records
     *
     * @param array $filters Filter parameters
     * @param int $limit Limit
     * @param int $offset Offset
     * @return array
     */
    public function getRawAttendance($filters = array(), $limit = 100, $offset = 0)
    {
        $this->db->select('*');
        $this->db->from('biometric_raw_attendance');

        if (isset($filters['device_sn'])) {
            $this->db->where('device_sn', $filters['device_sn']);
        }

        if (isset($filters['employee_id'])) {
            $this->db->where('employee_id', $filters['employee_id']);
        }

        if (isset($filters['processed'])) {
            $this->db->where('processed', $filters['processed']);
        }

        if (isset($filters['date_from'])) {
            $this->db->where('punch_time >=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $this->db->where('punch_time <=', $filters['date_to']);
        }

        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit, $offset);

        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Get raw attendance count with filters
     *
     * @param array $filters Filter parameters
     * @return int
     */
    public function getRawAttendanceCount($filters = array())
    {
        $this->db->from('biometric_raw_attendance');

        if (isset($filters['device_sn'])) {
            $this->db->where('device_sn', $filters['device_sn']);
        }

        if (isset($filters['employee_id'])) {
            $this->db->where('employee_id', $filters['employee_id']);
        }

        if (isset($filters['processed'])) {
            $this->db->where('processed', $filters['processed']);
        }

        if (isset($filters['date_from'])) {
            $this->db->where('punch_time >=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $this->db->where('punch_time <=', $filters['date_to']);
        }

        return $this->db->count_all_results();
    }

    /**
     * Get statistics
     * 
     * @return array
     */
    public function getStatistics()
    {
        $stats = array();

        // Total logs
        $stats['total_logs'] = $this->db->count_all('biometric_device_logs');

        // Logs by status
        $this->db->select('processing_status, COUNT(*) as count');
        $this->db->from('biometric_device_logs');
        $this->db->group_by('processing_status');
        $query = $this->db->get();
        $stats['by_status'] = array();
        foreach ($query->result() as $row) {
            $stats['by_status'][$row->processing_status] = $row->count;
        }

        // Total raw attendance
        $stats['total_raw_attendance'] = $this->db->count_all('biometric_raw_attendance');

        // Processed vs unprocessed
        $this->db->where('processed', 1);
        $stats['processed_attendance'] = $this->db->count_all_results('biometric_raw_attendance');
        
        $this->db->where('processed', 0);
        $stats['unprocessed_attendance'] = $this->db->count_all_results('biometric_raw_attendance');

        // Today's logs
        $this->db->where('DATE(created_at)', date('Y-m-d'));
        $stats['today_logs'] = $this->db->count_all_results('biometric_device_logs');

        return $stats;
    }
}

