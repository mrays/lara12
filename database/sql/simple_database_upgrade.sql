-- Simple Database Upgrade Script
-- This script safely upgrades your existing database to support the new invoice system
-- Execute this step by step for safety

-- ========================================
-- STEP 1: BACKUP EXISTING DATA
-- ========================================

-- Create backup tables (run this first!)
CREATE TABLE IF NOT EXISTS `clients_backup_20241124` AS SELECT * FROM `clients`;
CREATE TABLE IF NOT EXISTS `invoices_backup_20241124` AS SELECT * FROM `invoices`;
CREATE TABLE IF NOT EXISTS `services_backup_20241124` AS SELECT * FROM `services`;

-- ========================================
-- STEP 2: ENHANCE CLIENTS TABLE
-- ========================================

-- Add missing columns to clients table
ALTER TABLE `clients` 
ADD COLUMN IF NOT EXISTS `address` TEXT NULL AFTER `phone`,
ADD COLUMN IF NOT EXISTS `company` VARCHAR(255) NULL AFTER `address`;

-- Update status values to match new system
UPDATE `clients` SET `status` = 'Active' WHERE `status` = 'Active';
UPDATE `clients` SET `status` = 'Suspended' WHERE `status` = 'Suspended';
UPDATE `clients` SET `status` = 'Inactive' WHERE `status` = 'Cancelled';

-- ========================================
-- STEP 3: ENHANCE SERVICES TABLE
-- ========================================

-- Add notes column to services
ALTER TABLE `services` 
ADD COLUMN IF NOT EXISTS `notes` TEXT NULL AFTER `status`;

-- Update existing services to have proper status
UPDATE `services` SET `status` = 'Active' WHERE `status` = 'Active';
UPDATE `services` SET `status` = 'Suspended' WHERE `status` = 'Suspended';
UPDATE `services` SET `status` = 'Terminated' WHERE `status` = 'Cancelled';

-- ========================================
-- STEP 4: CREATE NEW INVOICE STRUCTURE
-- ========================================

-- Rename existing invoices table
RENAME TABLE `invoices` TO `invoices_old_structure`;

-- Create new invoices table with enhanced structure
CREATE TABLE `invoices` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `client_id` bigint(20) UNSIGNED NOT NULL,
    `service_id` bigint(20) UNSIGNED NULL,
    `number` varchar(255) NOT NULL,
    `title` varchar(255) NOT NULL,
    `description` text NULL,
    `subtotal` decimal(10,2) NOT NULL,
    `tax_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
    `tax_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
    `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
    `total_amount` decimal(10,2) NOT NULL,
    `status` enum('Draft', 'Sent', 'Paid', 'Overdue', 'Cancelled') NOT NULL DEFAULT 'Draft',
    `issue_date` date NOT NULL,
    `due_date` date NOT NULL,
    `paid_date` date NULL,
    `payment_method` varchar(255) NULL,
    `payment_reference` varchar(255) NULL,
    `notes` text NULL,
    `duitku_merchant_code` varchar(255) NULL,
    `duitku_reference` varchar(255) NULL,
    `duitku_payment_url` varchar(255) NULL,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `invoices_number_unique` (`number`),
    KEY `invoices_client_id_index` (`client_id`),
    KEY `invoices_service_id_index` (`service_id`),
    KEY `invoices_status_index` (`status`),
    KEY `invoices_due_date_index` (`due_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- STEP 5: CREATE INVOICE ITEMS TABLE
-- ========================================

CREATE TABLE `invoice_items` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `invoice_id` bigint(20) UNSIGNED NOT NULL,
    `description` varchar(255) NOT NULL,
    `quantity` int(11) NOT NULL DEFAULT 1,
    `unit_price` decimal(10,2) NOT NULL,
    `total_price` decimal(10,2) NOT NULL,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `invoice_items_invoice_id_index` (`invoice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- STEP 6: MIGRATE EXISTING INVOICE DATA
-- ========================================

-- Migrate old invoices to new structure
INSERT INTO `invoices` (
    `client_id`, 
    `service_id`,
    `number`, 
    `title`, 
    `subtotal`, 
    `total_amount`, 
    `status`, 
    `issue_date`,
    `due_date`,
    `paid_date`,
    `payment_reference`,
    `duitku_merchant_code`,
    `duitku_reference`,
    `created_at`,
    `updated_at`
)
SELECT 
    old.`client_id`,
    (SELECT s.id FROM services s WHERE s.client_id = old.client_id ORDER BY s.id LIMIT 1) as service_id,
    old.`invoice_no` as number,
    CONCAT('Invoice ', old.`invoice_no`) as title,
    old.`amount` as subtotal,
    old.`amount` as total_amount,
    CASE 
        WHEN old.`status` = 'Paid' THEN 'Paid'
        WHEN old.`status` = 'Unpaid' THEN 'Sent'
        WHEN old.`status` = 'Past Due' THEN 'Overdue'
        ELSE 'Draft'
    END as status,
    COALESCE(old.`due_date`, old.`created_at`, CURDATE()) as issue_date,
    COALESCE(old.`due_date`, DATE_ADD(CURDATE(), INTERVAL 30 DAY)) as due_date,
    CASE WHEN old.`status` = 'Paid' THEN old.`due_date` ELSE NULL END as paid_date,
    old.`reference` as payment_reference,
    old.`merchant_order_id` as duitku_merchant_code,
    old.`reference` as duitku_reference,
    old.`created_at`,
    old.`updated_at`
FROM `invoices_old_structure` old;

-- Create invoice items for each migrated invoice
INSERT INTO `invoice_items` (`invoice_id`, `description`, `quantity`, `unit_price`, `total_price`)
SELECT 
    i.`id` as invoice_id,
    CONCAT('Service: ', COALESCE(s.product, 'General Service')) as description,
    1 as quantity,
    i.`total_amount` as unit_price,
    i.`total_amount` as total_price
FROM `invoices` i
LEFT JOIN `services` s ON i.service_id = s.id;

-- ========================================
-- STEP 7: ADD FOREIGN KEY CONSTRAINTS
-- ========================================

-- Add foreign key constraints (do this after data migration)
ALTER TABLE `invoices` 
ADD CONSTRAINT `fk_invoices_client_new` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE;

ALTER TABLE `invoices` 
ADD CONSTRAINT `fk_invoices_service_new` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE SET NULL;

ALTER TABLE `invoice_items` 
ADD CONSTRAINT `fk_invoice_items_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE;

-- ========================================
-- STEP 8: UPDATE SAMPLE DATA
-- ========================================

-- Update existing clients with sample company info
UPDATE `clients` SET 
    `company` = 'Exputra Cloud Services',
    `address` = 'Jakarta, Indonesia'
WHERE `email` = 'admin@exputra.cloud' AND `company` IS NULL;

UPDATE `clients` SET 
    `company` = 'Example Corporation',
    `address` = 'New York, USA'
WHERE `email` = 'john@example.com' AND `company` IS NULL;

UPDATE `clients` SET 
    `company` = 'Customer Solutions',
    `address` = 'London, UK'  
WHERE `email` = 'jane@example.com' AND `company` IS NULL;

-- ========================================
-- STEP 9: CREATE USEFUL VIEWS
-- ========================================

-- Invoice summary view
CREATE OR REPLACE VIEW `v_invoice_summary` AS
SELECT 
    i.id,
    i.number,
    i.title,
    i.total_amount,
    i.status,
    i.issue_date,
    i.due_date,
    c.name as client_name,
    c.email as client_email,
    s.product as service_name
FROM invoices i
JOIN clients c ON i.client_id = c.id
LEFT JOIN services s ON i.service_id = s.id;

-- Client statistics view
CREATE OR REPLACE VIEW `v_client_stats` AS
SELECT 
    c.id,
    c.name,
    c.email,
    c.company,
    COUNT(i.id) as total_invoices,
    SUM(CASE WHEN i.status = 'Paid' THEN i.total_amount ELSE 0 END) as paid_amount,
    SUM(CASE WHEN i.status IN ('Sent', 'Overdue') THEN i.total_amount ELSE 0 END) as unpaid_amount,
    COUNT(CASE WHEN i.status = 'Overdue' THEN 1 END) as overdue_count
FROM clients c
LEFT JOIN invoices i ON c.id = i.client_id
GROUP BY c.id, c.name, c.email, c.company;

-- ========================================
-- STEP 10: VERIFICATION
-- ========================================

-- Show migration summary
SELECT 'MIGRATION SUMMARY' as info;

SELECT 
    'Original Invoices' as type, 
    COUNT(*) as count, 
    SUM(amount) as total_amount 
FROM invoices_old_structure
UNION ALL
SELECT 
    'New Invoices' as type, 
    COUNT(*) as count, 
    SUM(total_amount) as total_amount 
FROM invoices
UNION ALL
SELECT 
    'Invoice Items' as type, 
    COUNT(*) as count, 
    SUM(total_price) as total_amount 
FROM invoice_items;

-- Show status distribution
SELECT 'Invoice Status Distribution:' as info;
SELECT status, COUNT(*) as count FROM invoices GROUP BY status;

-- Show client summary
SELECT 'Client Summary:' as info;
SELECT name, total_invoices, paid_amount, unpaid_amount FROM v_client_stats;

-- ========================================
-- COMPLETION MESSAGE
-- ========================================

SELECT 'DATABASE UPGRADE COMPLETED SUCCESSFULLY!' as message;
SELECT 'Original data backed up in tables with _backup_20241124 suffix' as backup_info;
SELECT 'Old invoice structure preserved in invoices_old_structure table' as preservation_info;
