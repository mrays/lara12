-- Add profile fields to users table
-- Run this SQL script to add new profile fields

ALTER TABLE users 
ADD COLUMN whatsapp VARCHAR(20) NULL AFTER email,
ADD COLUMN address TEXT NULL AFTER whatsapp,
ADD COLUMN business_name VARCHAR(255) NULL AFTER address;

-- Update existing users with default values if needed
-- UPDATE users SET whatsapp = phone WHERE whatsapp IS NULL AND phone IS NOT NULL;

-- Show the updated table structure
DESCRIBE users;
