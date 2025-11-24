-- STEP BY STEP DATABASE UPGRADE
-- Execute each section one by one and verify results before proceeding

-- ========================================
-- STEP 1: CREATE BACKUPS (EXECUTE FIRST!)
-- ========================================

-- IMPORTANT: Run this first to backup your data
CREATE TABLE `clients_backup` AS SELECT * FROM `clients`;
CREATE TABLE `invoices_backup` AS SELECT * FROM `invoices`;  
CREATE TABLE `services_backup` AS SELECT * FROM `services`;

-- Verify backups created
SELECT 'Backup verification:' as info;
SELECT 'clients_backup' as table_name, COUNT(*) as records FROM clients_backup
UNION ALL
SELECT 'invoices_backup', COUNT(*) FROM invoices_backup
UNION ALL  
SELECT 'services_backup', COUNT(*) FROM services_backup;

-- ========================================
-- STEP 2: ENHANCE CLIENTS TABLE
-- ========================================

-- Add new columns to clients table
ALTER TABLE `clients` ADD COLUMN `address` TEXT NULL;
ALTER TABLE `clients` ADD COLUMN `company` VARCHAR(255) NULL;

-- Update sample data
UPDATE `clients` SET 
    `company` = 'Exputra Cloud Services',
    `address` = 'Jakarta, Indonesia'
WHERE `email` = 'admin@exputra.cloud';

-- Verify clients table
SELECT 'Enhanced clients table:' as info;
SELECT id, name, email, company, address, status FROM clients LIMIT 3;

-- ========================================
-- STEP 3: ENHANCE SERVICES TABLE  
-- ========================================

-- Add notes column
ALTER TABLE `services` ADD COLUMN `notes` TEXT NULL;

-- Verify services table
SELECT 'Enhanced services table:' as info;
SELECT id, client_id, product, domain, price, status FROM services LIMIT 3;

-- ========================================
-- STEP 4: CREATE NEW INVOICE TABLES
-- ========================================

-- Rename current invoices table to preserve data
ALTER TABLE `invoices` RENAME TO `invoices_old`;

-- Create new invoices table
CREATE TABLE `invoices` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `client_id` bigint(20) UNSIGNED NOT NULL,
    `service_id` bigint(20) UNSIGNED NULL,
    `number` varchar(255) NOT NULL,
    `title` varchar(255) NOT NULL,
    `description` text NULL,
    `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
    `tax_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
    `tax_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
    `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
    `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
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
    UNIQUE KEY `number` (`number`),
    KEY `client_id` (`client_id`),
    KEY `service_id` (`service_id`),
    KEY `status` (`status`),
    KEY `due_date` (`due_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create invoice items table
CREATE TABLE `invoice_items` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `invoice_id` bigint(20) UNSIGNED NOT NULL,
    `description` varchar(255) NOT NULL,
    `quantity` int(11) NOT NULL DEFAULT 1,
    `unit_price` decimal(10,2) NOT NULL DEFAULT 0.00,
    `total_price` decimal(10,2) NOT NULL DEFAULT 0.00,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `invoice_id` (`invoice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Verify new tables created
SHOW TABLES LIKE '%invoice%';

-- ========================================
-- STEP 5: MIGRATE DATA FROM OLD TO NEW
-- ========================================

-- Migrate invoices from old structure to new
INSERT INTO `invoices` (
    `client_id`,
    `service_id`, 
    `number`,
    `title`,
    `description`,
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
    (SELECT s.id FROM services s WHERE s.client_id = old.client_id LIMIT 1) as service_id,
    old.`invoice_no` as number,
    CONCAT('Invoice ', old.`invoice_no`) as title,
    'Migrated from legacy system' as description,
    old.`amount` as subtotal,
    old.`amount` as total_amount,
    CASE 
        WHEN old.`status` = 'Paid' THEN 'Paid'
        WHEN old.`status` = 'Unpaid' THEN 'Sent' 
        WHEN old.`status` = 'Past Due' THEN 'Overdue'
        ELSE 'Draft'
    END as status,
    COALESCE(old.`due_date`, old.`created_at`) as issue_date,
    COALESCE(old.`due_date`, DATE_ADD(old.`created_at`, INTERVAL 30 DAY)) as due_date,
    CASE WHEN old.`status` = 'Paid' THEN old.`due_date` ELSE NULL END as paid_date,
    old.`reference` as payment_reference,
    old.`merchant_order_id` as duitku_merchant_code,
    old.`reference` as duitku_reference,
    old.`created_at`,
    old.`updated_at`
FROM `invoices_old` old;

-- Create invoice items for migrated invoices
INSERT INTO `invoice_items` (`invoice_id`, `description`, `quantity`, `unit_price`, `total_price`)
SELECT 
    i.`id`,
    CONCAT('Service: ', COALESCE(s.product, 'General Service')) as description,
    1 as quantity,
    i.`total_amount` as unit_price,
    i.`total_amount` as total_price
FROM `invoices` i
LEFT JOIN `services` s ON i.service_id = s.id;

-- Verify migration
SELECT 'Migration verification:' as info;
SELECT 'Old invoices' as type, COUNT(*) as count FROM invoices_old
UNION ALL
SELECT 'New invoices' as type, COUNT(*) as count FROM invoices
UNION ALL
SELECT 'Invoice items' as type, COUNT(*) as count FROM invoice_items;

-- ========================================
-- STEP 6: ADD FOREIGN KEY CONSTRAINTS
-- ========================================

-- Add foreign key constraints
ALTER TABLE `invoices` 
ADD CONSTRAINT `fk_invoices_client` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE;

ALTER TABLE `invoices`
ADD CONSTRAINT `fk_invoices_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE SET NULL;

ALTER TABLE `invoice_items`
ADD CONSTRAINT `fk_invoice_items_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE;

-- ========================================
-- STEP 7: CREATE SAMPLE NEW INVOICES
-- ========================================

-- Insert some sample invoices with new structure
INSERT INTO `invoices` (
    `client_id`, `service_id`, `number`, `title`, `description`, 
    `subtotal`, `total_amount`, `status`, `issue_date`, `due_date`
) VALUES
(1, 1, 'INV-2024-003', 'Web Hosting Service - December 2024', 'Monthly hosting service', 1680000.00, 1680000.00, 'Sent', '2024-12-01', '2024-12-31'),
(2, 3, 'INV-2024-004', 'Starter Hosting - December 2024', 'Monthly starter hosting', 120000.00, 120000.00, 'Draft', '2024-12-01', '2024-12-31');

-- Add items for sample invoices
INSERT INTO `invoice_items` (`invoice_id`, `description`, `quantity`, `unit_price`, `total_price`)
SELECT 
    i.id,
    CONCAT(s.product, ' - ', i.title),
    1,
    i.total_amount,
    i.total_amount
FROM `invoices` i
JOIN `services` s ON i.service_id = s.id
WHERE i.number IN ('INV-2024-003', 'INV-2024-004');

-- ========================================
-- STEP 8: CREATE HELPFUL VIEWS
-- ========================================

-- Create invoice summary view
CREATE OR REPLACE VIEW `invoice_summary` AS
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

-- Create client stats view  
CREATE OR REPLACE VIEW `client_stats` AS
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
-- STEP 9: FINAL VERIFICATION
-- ========================================

-- Show final summary
SELECT '=== UPGRADE COMPLETED SUCCESSFULLY ===' as message;

SELECT 'Table Summary:' as info;
SELECT 'clients' as table_name, COUNT(*) as records FROM clients
UNION ALL
SELECT 'services', COUNT(*) FROM services  
UNION ALL
SELECT 'invoices', COUNT(*) FROM invoices
UNION ALL
SELECT 'invoice_items', COUNT(*) FROM invoice_items;

SELECT 'Invoice Status Distribution:' as info;
SELECT status, COUNT(*) as count, SUM(total_amount) as total_amount 
FROM invoices GROUP BY status;

SELECT 'Sample Invoice Data:' as info;
SELECT number, title, total_amount, status, client_name 
FROM invoice_summary LIMIT 5;

SELECT 'Client Statistics:' as info;  
SELECT name, total_invoices, paid_amount, unpaid_amount 
FROM client_stats LIMIT 5;

-- ========================================
-- CLEANUP NOTES
-- ========================================

/*
UPGRADE COMPLETED! 

BACKUP TABLES CREATED:
- clients_backup (original clients data)
- invoices_backup (original invoices data)  
- services_backup (original services data)
- invoices_old (renamed original invoices table)

NEW FEATURES:
✅ Enhanced clients table with company and address
✅ Enhanced services table with notes
✅ New comprehensive invoices table with tax, discounts, etc.
✅ Invoice items table for detailed line items
✅ Proper foreign key relationships
✅ Helpful views for reporting

WHAT TO DO NEXT:
1. Test your Laravel application
2. Update your models to match new structure  
3. Verify all data migrated correctly
4. After testing, you can drop backup tables if desired

IMPORTANT: Keep backup tables until you're sure everything works!
*/
