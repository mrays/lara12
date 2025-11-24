-- Fix Billing Cycle Column Length Issue
-- Run this SQL to fix the column length problem

-- Check current column definition
DESCRIBE services;

-- Modify billing_cycle column to allow longer strings
ALTER TABLE services 
MODIFY COLUMN billing_cycle VARCHAR(50) NULL;

-- Verify the change
DESCRIBE services;

-- Optional: Check current data to see what's causing truncation
SELECT id, billing_cycle, CHAR_LENGTH(billing_cycle) as length 
FROM services 
WHERE billing_cycle IS NOT NULL 
ORDER BY length DESC;

-- Test update with the problematic value
-- UPDATE services SET billing_cycle = '1 Tahun' WHERE id = 1;
