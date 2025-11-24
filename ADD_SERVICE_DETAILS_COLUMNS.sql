-- Add Service Details Columns to Sync Admin and Client Views
-- Run this SQL to add missing columns to services table

-- Check current table structure
DESCRIBE services;

-- Add missing columns for service details
ALTER TABLE services 
ADD COLUMN IF NOT EXISTS username VARCHAR(255) NULL AFTER billing_cycle,
ADD COLUMN IF NOT EXISTS password VARCHAR(255) NULL AFTER username,
ADD COLUMN IF NOT EXISTS server VARCHAR(255) NULL AFTER password,
ADD COLUMN IF NOT EXISTS login_url VARCHAR(500) NULL AFTER server,
ADD COLUMN IF NOT EXISTS description TEXT NULL AFTER login_url,
ADD COLUMN IF NOT EXISTS notes TEXT NULL AFTER description,
ADD COLUMN IF NOT EXISTS setup_fee DECIMAL(15,2) NULL DEFAULT 0 AFTER price;

-- Verify new table structure
DESCRIBE services;

-- Set default values for existing services (optional)
UPDATE services 
SET 
    username = 'admin',
    password = 'musang', 
    server = 'Default Server',
    login_url = 'https://example.com/login',
    description = 'Service description for client',
    notes = 'Premium hosting package',
    setup_fee = 0
WHERE username IS NULL;

-- Check updated data
SELECT id, product, username, password, server, login_url, setup_fee 
FROM services 
LIMIT 5;
