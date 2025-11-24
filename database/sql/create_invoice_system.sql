-- Invoice System Database Schema for MySQL/MariaDB
-- Created for Laravel Exputra Cloud Application

-- Create clients table
CREATE TABLE IF NOT EXISTS `clients` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL UNIQUE,
    `phone` varchar(255) NULL,
    `address` text NULL,
    `company` varchar(255) NULL,
    `status` enum('Active', 'Inactive', 'Suspended') NOT NULL DEFAULT 'Active',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `clients_email_index` (`email`),
    KEY `clients_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create services table
CREATE TABLE IF NOT EXISTS `services` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `client_id` bigint(20) UNSIGNED NOT NULL,
    `product` varchar(255) NOT NULL,
    `domain` varchar(255) NULL,
    `price` decimal(10,2) NOT NULL,
    `billing_cycle` enum('Monthly', 'Quarterly', 'Semi-Annually', 'Annually', 'Biennially') NOT NULL,
    `registration_date` date NOT NULL,
    `due_date` date NOT NULL,
    `ip` varchar(255) NULL,
    `status` enum('Active', 'Suspended', 'Terminated', 'Pending') NOT NULL DEFAULT 'Pending',
    `notes` text NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `services_client_id_foreign` (`client_id`),
    KEY `services_status_index` (`status`),
    KEY `services_due_date_index` (`due_date`),
    CONSTRAINT `services_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create invoices table
CREATE TABLE IF NOT EXISTS `invoices` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `client_id` bigint(20) UNSIGNED NOT NULL,
    `service_id` bigint(20) UNSIGNED NULL,
    `number` varchar(255) NOT NULL UNIQUE,
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
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `invoices_number_unique` (`number`),
    KEY `invoices_client_id_foreign` (`client_id`),
    KEY `invoices_service_id_foreign` (`service_id`),
    KEY `invoices_status_index` (`status`),
    KEY `invoices_due_date_index` (`due_date`),
    KEY `invoices_issue_date_index` (`issue_date`),
    CONSTRAINT `invoices_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
    CONSTRAINT `invoices_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create invoice_items table
CREATE TABLE IF NOT EXISTS `invoice_items` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `invoice_id` bigint(20) UNSIGNED NOT NULL,
    `description` varchar(255) NOT NULL,
    `quantity` int(11) NOT NULL DEFAULT 1,
    `unit_price` decimal(10,2) NOT NULL,
    `total_price` decimal(10,2) NOT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `invoice_items_invoice_id_foreign` (`invoice_id`),
    CONSTRAINT `invoice_items_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample data for testing
INSERT INTO `clients` (`name`, `email`, `phone`, `company`, `status`, `created_at`, `updated_at`) VALUES
('John Doe', 'john@example.com', '+1234567890', 'Tech Corp', 'Active', NOW(), NOW()),
('Jane Smith', 'jane@example.com', '+0987654321', 'Design Studio', 'Active', NOW(), NOW()),
('Bob Johnson', 'bob@example.com', '+1122334455', 'Marketing Inc', 'Active', NOW(), NOW());

-- Insert sample services
INSERT INTO `services` (`client_id`, `product`, `domain`, `price`, `billing_cycle`, `registration_date`, `due_date`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Web Hosting Premium', 'techcorp.com', 29.99, 'Monthly', '2024-01-01', '2024-12-01', 'Active', NOW(), NOW()),
(1, 'SSL Certificate', 'techcorp.com', 99.99, 'Annually', '2024-01-01', '2025-01-01', 'Active', NOW(), NOW()),
(2, 'VPS Server', 'designstudio.com', 79.99, 'Monthly', '2024-02-01', '2024-12-01', 'Active', NOW(), NOW()),
(3, 'Domain Registration', 'marketinginc.com', 15.99, 'Annually', '2024-03-01', '2025-03-01', 'Active', NOW(), NOW());

-- Insert sample invoices
INSERT INTO `invoices` (`client_id`, `service_id`, `number`, `title`, `description`, `subtotal`, `tax_rate`, `tax_amount`, `total_amount`, `status`, `issue_date`, `due_date`, `created_at`, `updated_at`) VALUES
(1, 1, 'INV-2024-001', 'Web Hosting Premium - November 2024', 'Monthly hosting service for techcorp.com', 29.99, 10.00, 3.00, 32.99, 'Sent', '2024-11-01', '2024-11-30', NOW(), NOW()),
(1, 2, 'INV-2024-002', 'SSL Certificate Renewal', 'Annual SSL certificate for techcorp.com', 99.99, 10.00, 10.00, 109.99, 'Paid', '2024-01-01', '2024-01-31', NOW(), NOW()),
(2, 3, 'INV-2024-003', 'VPS Server - November 2024', 'Monthly VPS hosting service', 79.99, 10.00, 8.00, 87.99, 'Overdue', '2024-11-01', '2024-11-15', NOW(), NOW()),
(3, 4, 'INV-2024-004', 'Domain Registration', 'Annual domain registration fee', 15.99, 0.00, 0.00, 15.99, 'Paid', '2024-03-01', '2024-03-31', NOW(), NOW());

-- Insert sample invoice items
INSERT INTO `invoice_items` (`invoice_id`, `description`, `quantity`, `unit_price`, `total_price`, `created_at`, `updated_at`) VALUES
(1, 'Web Hosting Premium Plan', 1, 29.99, 29.99, NOW(), NOW()),
(2, 'SSL Certificate - Wildcard', 1, 99.99, 99.99, NOW(), NOW()),
(3, 'VPS Server 4GB RAM', 1, 79.99, 79.99, NOW(), NOW()),
(4, 'Domain Registration .com', 1, 15.99, 15.99, NOW(), NOW());

-- Create indexes for better performance
CREATE INDEX idx_invoices_client_status ON invoices(client_id, status);
CREATE INDEX idx_services_client_status ON services(client_id, status);
CREATE INDEX idx_invoices_dates ON invoices(issue_date, due_date);

-- Create views for common queries
CREATE OR REPLACE VIEW invoice_summary AS
SELECT 
    i.id,
    i.number,
    i.title,
    i.total_amount,
    i.status,
    i.issue_date,
    i.due_date,
    i.paid_date,
    c.name as client_name,
    c.email as client_email,
    s.product as service_name,
    s.domain as service_domain
FROM invoices i
JOIN clients c ON i.client_id = c.id
LEFT JOIN services s ON i.service_id = s.id;

CREATE OR REPLACE VIEW client_invoice_stats AS
SELECT 
    c.id as client_id,
    c.name as client_name,
    c.email as client_email,
    COUNT(i.id) as total_invoices,
    SUM(CASE WHEN i.status = 'Paid' THEN i.total_amount ELSE 0 END) as paid_amount,
    SUM(CASE WHEN i.status IN ('Sent', 'Overdue') THEN i.total_amount ELSE 0 END) as outstanding_amount,
    COUNT(CASE WHEN i.status = 'Overdue' THEN 1 END) as overdue_count
FROM clients c
LEFT JOIN invoices i ON c.id = i.client_id
GROUP BY c.id, c.name, c.email;
