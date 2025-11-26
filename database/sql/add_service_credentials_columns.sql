-- Add service credentials columns to services table
-- Run this query directly in MySQL

ALTER TABLE `services` 
ADD COLUMN `username` VARCHAR(255) NULL AFTER `notes`,
ADD COLUMN `password` VARCHAR(255) NULL AFTER `username`,
ADD COLUMN `server` VARCHAR(255) NULL AFTER `password`,
ADD COLUMN `login_url` VARCHAR(255) NULL AFTER `server`,
ADD COLUMN `description` TEXT NULL AFTER `login_url`,
ADD COLUMN `setup_fee` DECIMAL(10,2) NULL DEFAULT 0.00 AFTER `description`;
