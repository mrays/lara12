-- Complete Fresh Database Setup for Exputra Cloud
-- This file creates a complete database from scratch with all tables and sample data
-- Use this for EMPTY/NEW database installations

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- ========================================
-- CREATE ALL TABLES
-- ========================================

-- Table structure for table `cache`
CREATE TABLE `cache` (
    `key` varchar(255) NOT NULL,
    `value` mediumtext NOT NULL,
    `expiration` int(11) NOT NULL,
    PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for table `cache_locks`
CREATE TABLE `cache_locks` (
    `key` varchar(255) NOT NULL,
    `owner` varchar(255) NOT NULL,
    `expiration` int(11) NOT NULL,
    PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for table `failed_jobs`
CREATE TABLE `failed_jobs` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `uuid` varchar(255) NOT NULL,
    `connection` text NOT NULL,
    `queue` text NOT NULL,
    `payload` longtext NOT NULL,
    `exception` longtext NOT NULL,
    `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for table `jobs`
CREATE TABLE `jobs` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `queue` varchar(255) NOT NULL,
    `payload` longtext NOT NULL,
    `attempts` tinyint(3) UNSIGNED NOT NULL,
    `reserved_at` int(10) UNSIGNED DEFAULT NULL,
    `available_at` int(10) UNSIGNED NOT NULL,
    `created_at` int(10) UNSIGNED NOT NULL,
    PRIMARY KEY (`id`),
    KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for table `job_batches`
CREATE TABLE `job_batches` (
    `id` varchar(255) NOT NULL,
    `name` varchar(255) NOT NULL,
    `total_jobs` int(11) NOT NULL,
    `pending_jobs` int(11) NOT NULL,
    `failed_jobs` int(11) NOT NULL,
    `failed_job_ids` longtext DEFAULT NULL,
    `options` mediumtext DEFAULT NULL,
    `cancelled_at` int(11) DEFAULT NULL,
    `created_at` int(11) NOT NULL,
    `finished_at` int(11) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for table `migrations`
CREATE TABLE `migrations` (
    `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `migration` varchar(255) NOT NULL,
    `batch` int(11) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for table `sessions`
CREATE TABLE `sessions` (
    `id` varchar(255) NOT NULL,
    `user_id` bigint(20) UNSIGNED DEFAULT NULL,
    `ip_address` varchar(45) DEFAULT NULL,
    `user_agent` text DEFAULT NULL,
    `payload` longtext NOT NULL,
    `last_activity` int(11) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `sessions_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- MAIN APPLICATION TABLES
-- ========================================

-- Table structure for table `users`
CREATE TABLE `users` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL,
    `email_verified_at` timestamp NULL DEFAULT NULL,
    `password` varchar(255) NOT NULL,
    `role` varchar(50) NOT NULL DEFAULT 'client',
    `remember_token` varchar(100) DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for table `clients`
CREATE TABLE `clients` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL,
    `phone` varchar(50) DEFAULT NULL,
    `address` text DEFAULT NULL,
    `company` varchar(255) DEFAULT NULL,
    `status` enum('Active','Inactive','Suspended') NOT NULL DEFAULT 'Active',
    `created_at` timestamp NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `clients_email_unique` (`email`),
    KEY `clients_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for table `services`
CREATE TABLE `services` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `client_id` bigint(20) UNSIGNED NOT NULL,
    `product` varchar(255) NOT NULL,
    `domain` varchar(255) DEFAULT NULL,
    `price` decimal(10,2) NOT NULL,
    `billing_cycle` enum('Monthly','Quarterly','Semi-Annually','Annually','Biennially') NOT NULL,
    `registration_date` date NOT NULL,
    `due_date` date NOT NULL,
    `ip` varchar(45) DEFAULT NULL,
    `status` enum('Active','Suspended','Terminated','Pending') NOT NULL DEFAULT 'Pending',
    `notes` text DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `services_client_id_foreign` (`client_id`),
    KEY `services_status_index` (`status`),
    KEY `services_due_date_index` (`due_date`),
    CONSTRAINT `services_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for table `invoices`
CREATE TABLE `invoices` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `client_id` bigint(20) UNSIGNED NOT NULL,
    `service_id` bigint(20) UNSIGNED DEFAULT NULL,
    `number` varchar(255) NOT NULL,
    `title` varchar(255) NOT NULL,
    `description` text DEFAULT NULL,
    `subtotal` decimal(10,2) NOT NULL,
    `tax_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
    `tax_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
    `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
    `total_amount` decimal(10,2) NOT NULL,
    `status` enum('Draft','Sent','Paid','Overdue','Cancelled') NOT NULL DEFAULT 'Draft',
    `issue_date` date NOT NULL,
    `due_date` date NOT NULL,
    `paid_date` date DEFAULT NULL,
    `payment_method` varchar(255) DEFAULT NULL,
    `payment_reference` varchar(255) DEFAULT NULL,
    `notes` text DEFAULT NULL,
    `duitku_merchant_code` varchar(255) DEFAULT NULL,
    `duitku_reference` varchar(255) DEFAULT NULL,
    `duitku_payment_url` varchar(255) DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
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

-- Table structure for table `invoice_items`
CREATE TABLE `invoice_items` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `invoice_id` bigint(20) UNSIGNED NOT NULL,
    `description` varchar(255) NOT NULL,
    `quantity` int(11) NOT NULL DEFAULT 1,
    `unit_price` decimal(10,2) NOT NULL,
    `total_price` decimal(10,2) NOT NULL,
    `created_at` timestamp NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `invoice_items_invoice_id_foreign` (`invoice_id`),
    CONSTRAINT `invoice_items_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- INSERT SAMPLE DATA
-- ========================================

-- Insert users
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'admin@exputra.cloud', '2025-11-24 06:00:00', '$2y$10$cbQfVIAtv7lIhWZo4pqK1.Tmeh7bwy0cPUaqz6vVN/0gw833t8P82', 'admin', '2025-11-24 06:00:00', '2025-11-24 06:00:00'),
(2, 'Client Demo', 'client@exputra.cloud', '2025-11-24 06:00:00', '$2y$10$oAQex3uYy/t1vJKEvjG9xuwKIm3oQO7IOx0r4KBDuJCoFnSUzwm3e', 'client', '2025-11-24 06:00:00', '2025-11-24 06:00:00'),
(3, 'John Doe', 'john@example.com', '2025-11-24 06:00:00', '$2y$10$oAQex3uYy/t1vJKEvjG9xuwKIm3oQO7IOx0r4KBDuJCoFnSUzwm3e', 'client', '2025-11-24 06:00:00', '2025-11-24 06:00:00'),
(4, 'Jane Smith', 'jane@example.com', '2025-11-24 06:00:00', '$2y$10$oAQex3uYy/t1vJKEvjG9xuwKIm3oQO7IOx0r4KBDuJCoFnSUzwm3e', 'client', '2025-11-24 06:00:00', '2025-11-24 06:00:00');

-- Insert clients
INSERT INTO `clients` (`id`, `name`, `email`, `phone`, `address`, `company`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'admin@exputra.cloud', '081234567890', 'Jakarta, Indonesia', 'Exputra Cloud Services', 'Active', '2025-11-24 08:00:00', '2025-11-24 08:00:00'),
(2, 'John Doe', 'john@example.com', '081111111111', 'New York, USA', 'Example Corporation', 'Active', '2025-11-24 08:00:00', '2025-11-24 08:00:00'),
(3, 'Jane Smith', 'jane@example.com', '082222222222', 'London, UK', 'Smith Solutions Ltd', 'Active', '2025-11-24 08:00:00', '2025-11-24 08:00:00'),
(4, 'Bob Johnson', 'bob@example.com', '083333333333', 'Sydney, Australia', 'Johnson Marketing Inc', 'Active', '2025-11-24 08:00:00', '2025-11-24 08:00:00');

-- Insert services
INSERT INTO `services` (`id`, `client_id`, `product`, `domain`, `price`, `billing_cycle`, `registration_date`, `due_date`, `ip`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 'Unlimited Hosting L', 'mahamultilogamindo.com', 1680000.00, 'Annually', '2025-11-10', '2026-01-09', '192.168.1.100', 'Active', 'Premium hosting package', '2025-11-24 08:00:00', '2025-11-24 08:00:00'),
(2, 1, 'Unlimited Hosting L', 'jasatukangbangunanterdekat.id', 1680000.00, 'Annually', '2025-09-17', '2026-09-17', '192.168.1.101', 'Active', 'Premium hosting package', '2025-11-24 08:00:00', '2025-11-24 08:00:00'),
(3, 2, 'Starter Hosting', 'johndoe.com', 120000.00, 'Monthly', '2024-10-01', '2024-12-01', '192.168.1.102', 'Active', 'Basic hosting package', '2025-11-24 08:00:00', '2025-11-24 08:00:00'),
(4, 3, 'Business Hosting', 'smithsolutions.com', 350000.00, 'Quarterly', '2024-09-01', '2024-12-01', '192.168.1.103', 'Active', 'Business hosting package', '2025-11-24 08:00:00', '2025-11-24 08:00:00'),
(5, 4, 'VPS Server', 'johnsonmarketing.com', 800000.00, 'Monthly', '2024-08-01', '2024-12-01', '192.168.1.104', 'Active', 'Virtual private server', '2025-11-24 08:00:00', '2025-11-24 08:00:00');

-- Insert invoices
INSERT INTO `invoices` (`id`, `client_id`, `service_id`, `number`, `title`, `description`, `subtotal`, `tax_rate`, `tax_amount`, `discount_amount`, `total_amount`, `status`, `issue_date`, `due_date`, `paid_date`, `payment_method`, `payment_reference`, `duitku_merchant_code`, `duitku_reference`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'INV-2025-001', 'Unlimited Hosting L - Annual Renewal', 'Annual hosting service for mahamultilogamindo.com', 1680000.00, 11.00, 184800.00, 0.00, 1864800.00, 'Sent', '2025-11-01', '2025-12-01', NULL, NULL, NULL, 'INV-1-1732435200', 'DS1690225IBHEVKK5Q4H3EEV', '2025-11-24 08:00:00', '2025-11-24 08:00:00'),
(2, 2, 3, 'INV-2025-002', 'Starter Hosting - November 2024', 'Monthly hosting service for johndoe.com', 120000.00, 11.00, 13200.00, 0.00, 133200.00, 'Paid', '2024-11-01', '2024-11-30', '2024-11-15', 'Bank Transfer', 'TRX123456789', NULL, NULL, '2025-11-24 08:00:00', '2025-11-24 08:00:00'),
(3, 3, 4, 'INV-2025-003', 'Business Hosting - Q4 2024', 'Quarterly hosting service for smithsolutions.com', 350000.00, 11.00, 38500.00, 17500.00, 371000.00, 'Overdue', '2024-10-01', '2024-11-15', NULL, NULL, NULL, NULL, NULL, '2025-11-24 08:00:00', '2025-11-24 08:00:00'),
(4, 4, 5, 'INV-2025-004', 'VPS Server - November 2024', 'Monthly VPS server service', 800000.00, 11.00, 88000.00, 0.00, 888000.00, 'Draft', '2024-11-01', '2024-12-01', NULL, NULL, NULL, NULL, NULL, '2025-11-24 08:00:00', '2025-11-24 08:00:00');

-- Insert invoice items
INSERT INTO `invoice_items` (`id`, `invoice_id`, `description`, `quantity`, `unit_price`, `total_price`, `created_at`, `updated_at`) VALUES
(1, 1, 'Unlimited Hosting L - Annual Plan', 1, 1680000.00, 1680000.00, '2025-11-24 08:00:00', '2025-11-24 08:00:00'),
(2, 2, 'Starter Hosting - Monthly Plan', 1, 120000.00, 120000.00, '2025-11-24 08:00:00', '2025-11-24 08:00:00'),
(3, 3, 'Business Hosting - Quarterly Plan', 1, 350000.00, 350000.00, '2025-11-24 08:00:00', '2025-11-24 08:00:00'),
(4, 4, 'VPS Server - Monthly Plan', 1, 800000.00, 800000.00, '2025-11-24 08:00:00', '2025-11-24 08:00:00'),
(5, 1, 'SSL Certificate', 1, 0.00, 0.00, '2025-11-24 08:00:00', '2025-11-24 08:00:00'),
(6, 3, 'Setup Fee Discount', 1, -17500.00, -17500.00, '2025-11-24 08:00:00', '2025-11-24 08:00:00');

-- ========================================
-- CREATE PERFORMANCE INDEXES
-- ========================================

-- Additional indexes for better performance
CREATE INDEX idx_invoices_client_status ON invoices(client_id, status);
CREATE INDEX idx_services_client_status ON services(client_id, status);
CREATE INDEX idx_invoices_dates ON invoices(issue_date, due_date);

-- ========================================
-- CREATE USEFUL VIEWS
-- ========================================

-- Invoice summary view
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
    c.company as client_company,
    s.product as service_name,
    s.domain as service_domain
FROM invoices i
JOIN clients c ON i.client_id = c.id
LEFT JOIN services s ON i.service_id = s.id;

-- Client statistics view
CREATE OR REPLACE VIEW client_invoice_stats AS
SELECT 
    c.id as client_id,
    c.name as client_name,
    c.email as client_email,
    c.company as client_company,
    c.status as client_status,
    COUNT(i.id) as total_invoices,
    SUM(CASE WHEN i.status = 'Paid' THEN i.total_amount ELSE 0 END) as paid_amount,
    SUM(CASE WHEN i.status IN ('Sent', 'Overdue') THEN i.total_amount ELSE 0 END) as outstanding_amount,
    COUNT(CASE WHEN i.status = 'Overdue' THEN 1 END) as overdue_count,
    COUNT(CASE WHEN i.status IN ('Draft', 'Sent', 'Overdue') THEN 1 END) as unpaid_count
FROM clients c
LEFT JOIN invoices i ON c.id = i.client_id
GROUP BY c.id, c.name, c.email, c.company, c.status;

-- Service statistics view
CREATE OR REPLACE VIEW service_stats AS
SELECT 
    s.id as service_id,
    s.product,
    s.domain,
    s.price,
    s.billing_cycle,
    s.status,
    c.name as client_name,
    COUNT(i.id) as invoice_count,
    SUM(i.total_amount) as total_billed,
    MAX(i.due_date) as last_invoice_date
FROM services s
JOIN clients c ON s.client_id = c.id
LEFT JOIN invoices i ON s.id = i.service_id
GROUP BY s.id, s.product, s.domain, s.price, s.billing_cycle, s.status, c.name;

-- ========================================
-- SET AUTO_INCREMENT VALUES
-- ========================================

ALTER TABLE `clients` AUTO_INCREMENT = 5;
ALTER TABLE `services` AUTO_INCREMENT = 6;
ALTER TABLE `invoices` AUTO_INCREMENT = 5;
ALTER TABLE `invoice_items` AUTO_INCREMENT = 7;
ALTER TABLE `users` AUTO_INCREMENT = 5;

-- ========================================
-- FINAL VERIFICATION
-- ========================================

-- Show database summary
SELECT 'DATABASE SETUP COMPLETED SUCCESSFULLY!' as message;

SELECT 'Table Summary:' as info;
SELECT 'users' as table_name, COUNT(*) as records FROM users
UNION ALL
SELECT 'clients', COUNT(*) FROM clients
UNION ALL
SELECT 'services', COUNT(*) FROM services
UNION ALL
SELECT 'invoices', COUNT(*) FROM invoices
UNION ALL
SELECT 'invoice_items', COUNT(*) FROM invoice_items;

SELECT 'Invoice Status Distribution:' as info;
SELECT status, COUNT(*) as count, SUM(total_amount) as total_amount 
FROM invoices GROUP BY status;

SELECT 'Client Statistics:' as info;
SELECT client_name, total_invoices, paid_amount, outstanding_amount, overdue_count 
FROM client_invoice_stats;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- ========================================
-- SETUP COMPLETED!
-- ========================================

/*
🎉 FRESH DATABASE SETUP COMPLETED!

WHAT WAS CREATED:
✅ All Laravel framework tables (cache, jobs, sessions, etc.)
✅ Users table with sample admin and client accounts
✅ Clients table with enhanced structure (company, address)
✅ Services table with proper enums and relationships
✅ Invoices table with comprehensive features (tax, discounts, etc.)
✅ Invoice items table for detailed line items
✅ Performance indexes for optimal queries
✅ Useful views for reporting and analytics

SAMPLE DATA INCLUDED:
👤 4 Users (1 admin, 3 clients)
🏢 4 Clients with company information
🔧 5 Services (hosting, VPS, etc.)
📄 4 Invoices with different statuses
📋 6 Invoice items with detailed descriptions

LOGIN CREDENTIALS:
Admin: admin@exputra.cloud / password
Client: client@exputra.cloud / password

NEXT STEPS:
1. Import this file to your empty database
2. Update your .env file with database credentials
3. Run: php artisan config:clear
4. Test login with provided credentials
5. Explore the beautiful Sneat dashboard!

FEATURES READY:
✅ Dynamic invoice system
✅ Client and admin dashboards
✅ Payment tracking
✅ Overdue detection
✅ Tax calculations
✅ Reporting views
✅ Beautiful Sneat UI

Happy coding! 🚀
*/
