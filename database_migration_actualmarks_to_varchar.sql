-- Migration to change actualmarks column from INT to VARCHAR to support 'AB' (Absent) values
-- Run this SQL script in your database

-- Change actualmarks column in internalresulttable
ALTER TABLE `internalresulttable` 
MODIFY COLUMN `actualmarks` VARCHAR(10) DEFAULT NULL;

-- Change actualmarks column in publicresulttable
ALTER TABLE `publicresulttable` 
MODIFY COLUMN `actualmarks` VARCHAR(10) DEFAULT NULL;

-- Note: This migration preserves existing numeric data as VARCHAR can store numbers
-- After running this migration, the system will support both numeric marks and 'AB' for absent students

