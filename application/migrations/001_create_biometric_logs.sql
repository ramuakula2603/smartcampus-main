-- Biometric Device Communication Logs Table
-- This table stores all incoming requests from biometric devices for debugging and audit purposes

CREATE TABLE IF NOT EXISTS `biometric_device_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `request_method` varchar(10) DEFAULT NULL COMMENT 'GET or POST',
  `request_uri` varchar(255) DEFAULT NULL COMMENT 'Request URI',
  `query_string` text DEFAULT NULL COMMENT 'URL query parameters as JSON',
  `raw_body` text DEFAULT NULL COMMENT 'Raw POST body data',
  `parsed_data` text DEFAULT NULL COMMENT 'Parsed data as JSON',
  `device_sn` varchar(100) DEFAULT NULL COMMENT 'Device serial number',
  `ip_address` varchar(50) DEFAULT NULL COMMENT 'Device IP address',
  `user_agent` varchar(255) DEFAULT NULL COMMENT 'Device user agent',
  `processing_status` enum('pending','success','error') DEFAULT 'pending' COMMENT 'Processing status',
  `error_message` text DEFAULT NULL COMMENT 'Error message if processing failed',
  `records_processed` int(11) DEFAULT 0 COMMENT 'Number of attendance records processed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_device_sn` (`device_sn`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_processing_status` (`processing_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Biometric device communication logs';

-- Biometric Raw Attendance Data Table
-- This table stores raw attendance data before processing
CREATE TABLE IF NOT EXISTS `biometric_raw_attendance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `device_log_id` int(11) DEFAULT NULL COMMENT 'Reference to biometric_device_logs',
  `device_sn` varchar(100) DEFAULT NULL COMMENT 'Device serial number',
  `table_type` varchar(50) DEFAULT NULL COMMENT 'ATTLOG or OPERLOG',
  `stamp` varchar(50) DEFAULT NULL COMMENT 'Device stamp',
  `employee_id` varchar(50) DEFAULT NULL COMMENT 'Employee/Student PIN',
  `punch_time` datetime DEFAULT NULL COMMENT 'Punch timestamp',
  `status1` int(11) DEFAULT NULL COMMENT 'Status field 1',
  `status2` int(11) DEFAULT NULL COMMENT 'Status field 2',
  `status3` int(11) DEFAULT NULL COMMENT 'Status field 3',
  `status4` int(11) DEFAULT NULL COMMENT 'Status field 4',
  `status5` int(11) DEFAULT NULL COMMENT 'Status field 5',
  `processed` tinyint(1) DEFAULT 0 COMMENT 'Whether this record has been processed',
  `processed_at` datetime DEFAULT NULL COMMENT 'When this record was processed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_device_log_id` (`device_log_id`),
  KEY `idx_device_sn` (`device_sn`),
  KEY `idx_employee_id` (`employee_id`),
  KEY `idx_processed` (`processed`),
  KEY `idx_punch_time` (`punch_time`),
  CONSTRAINT `fk_biometric_raw_device_log` FOREIGN KEY (`device_log_id`) REFERENCES `biometric_device_logs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Raw biometric attendance data';

