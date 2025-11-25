-- ========================================
-- 1. Create Servers Table
-- ========================================
CREATE TABLE IF NOT EXISTS `servers` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `ip_address` varchar(255) NOT NULL,
    `username` varchar(255) NOT NULL,
    `password` text NOT NULL,
    `login_link` varchar(255) NOT NULL,
    `expired_date` date NOT NULL,
    `status` enum('active','expired','suspended') NOT NULL DEFAULT 'active',
    `notes` text DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `servers_status_expired_date_index` (`status`,`expired_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 2. Create Domain Registers Table
-- ========================================
CREATE TABLE IF NOT EXISTS `domain_registers` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `username` varchar(255) NOT NULL,
    `password` text NOT NULL,
    `login_link` varchar(255) NOT NULL,
    `expired_date` date NOT NULL,
    `status` enum('active','expired','suspended') NOT NULL DEFAULT 'active',
    `notes` text DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `domain_registers_status_expired_date_index` (`status`,`expired_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 3. Create Client Data Table
-- ========================================
CREATE TABLE IF NOT EXISTS `client_data` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `address` text NOT NULL,
    `whatsapp` varchar(20) NOT NULL,
    `website_service_expired` date NOT NULL,
    `domain_expired` date NOT NULL,
    `hosting_expired` date NOT NULL,
    `server_id` bigint(20) unsigned DEFAULT NULL,
    `domain_register_id` bigint(20) unsigned DEFAULT NULL,
    `user_id` bigint(20) unsigned DEFAULT NULL,
    `status` enum('active','expired','warning') NOT NULL DEFAULT 'active',
    `notes` text DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `client_data_status_website_service_expired_domain_expired_hosting_expired_index` (`status`,`website_service_expired`,`domain_expired`,`hosting_expired`),
    KEY `client_data_server_id_foreign` (`server_id`),
    KEY `client_data_domain_register_id_foreign` (`domain_register_id`),
    KEY `client_data_user_id_foreign` (`user_id`),
    CONSTRAINT `client_data_domain_register_id_foreign` FOREIGN KEY (`domain_register_id`) REFERENCES `domain_registers` (`id`) ON DELETE SET NULL,
    CONSTRAINT `client_data_server_id_foreign` FOREIGN KEY (`server_id`) REFERENCES `servers` (`id`) ON DELETE SET NULL,
    CONSTRAINT `client_data_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 4. Insert Sample Data (Optional)
-- ========================================

-- Sample Server
INSERT INTO `servers` (`name`, `ip_address`, `username`, `password`, `login_link`, `expired_date`, `status`, `notes`, `created_at`, `updated_at`) VALUES
('Server Hosting 1', '192.168.1.100', 'root', 'encrypted_password_here', 'https://server1.example.com:2083', '2026-12-31', 'active', 'Primary hosting server', NOW(), NOW()),
('Server Hosting 2', '192.168.1.101', 'admin', 'encrypted_password_here', 'https://server2.example.com:2083', '2025-06-30', 'active', 'Backup hosting server', NOW(), NOW());

-- Sample Domain Register
INSERT INTO `domain_registers` (`name`, `username`, `password`, `login_link`, `expired_date`, `status`, `notes`, `created_at`, `updated_at`) VALUES
('ResellerClub', 'reseller@example.com', 'encrypted_password_here', 'https://manage.resellerclub.com', '2025-12-31', 'active', 'Domain reseller account', NOW(), NOW()),
('GoDaddy', 'godaddy@example.com', 'encrypted_password_here', 'https://godaddy.com/login', '2026-06-30', 'active', 'Alternative domain register', NOW(), NOW());

-- Sample Client Data
INSERT INTO `client_data` (`name`, `address`, `whatsapp`, `website_service_expired`, `domain_expired`, `hosting_expired`, `server_id`, `domain_register_id`, `user_id`, `status`, `notes`, `created_at`, `updated_at`) VALUES
('PT. Contoh Company', 'Jl. Contoh No. 123, Jakarta', '+62812345678', '2025-12-31', '2025-12-31', '2025-12-31', 1, 1, NULL, 'active', 'Client pertama', NOW(), NOW()),
('CV. Test Company', 'Jl. Test No. 456, Surabaya', '+628987654321', '2025-06-30', '2025-06-30', '2025-06-30', 1, 2, NULL, 'active', 'Client kedua', NOW(), NOW());

-- ========================================
-- 5. Create Views for Easy Querying (Optional)
-- ========================================

-- View for Client with Server and Register Info
CREATE OR REPLACE VIEW `v_client_details` AS
SELECT 
    cd.id,
    cd.name as client_name,
    cd.address,
    cd.whatsapp,
    cd.website_service_expired,
    cd.domain_expired,
    cd.hosting_expired,
    cd.status,
    cd.notes,
    s.name as server_name,
    s.ip_address as server_ip,
    dr.name as register_name,
    u.name as user_name,
    u.email as user_email,
    CASE 
        WHEN cd.website_service_expired < CURDATE() OR cd.domain_expired < CURDATE() OR cd.hosting_expired < CURDATE() THEN 'expired'
        WHEN cd.website_service_expired <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) OR cd.domain_expired <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) OR cd.hosting_expired <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN 'expiring_soon'
        ELSE 'active'
    END as computed_status
FROM client_data cd
LEFT JOIN servers s ON cd.server_id = s.id
LEFT JOIN domain_registers dr ON cd.domain_register_id = dr.id
LEFT JOIN users u ON cd.user_id = u.id;

-- View for Server Statistics
CREATE OR REPLACE VIEW `v_server_stats` AS
SELECT 
    s.id,
    s.name,
    s.ip_address,
    s.status,
    s.expired_date,
    COUNT(cd.id) as client_count,
    SUM(CASE WHEN cd.website_service_expired < CURDATE() OR cd.domain_expired < CURDATE() OR cd.hosting_expired < CURDATE() THEN 1 ELSE 0 END) as expired_clients,
    SUM(CASE WHEN cd.website_service_expired <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) OR cd.domain_expired <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) OR cd.hosting_expired <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as expiring_clients
FROM servers s
LEFT JOIN client_data cd ON s.id = cd.server_id
GROUP BY s.id, s.name, s.ip_address, s.status, s.expired_date;

-- View for Domain Register Statistics
CREATE OR REPLACE VIEW `v_register_stats` AS
SELECT 
    dr.id,
    dr.name,
    dr.status,
    dr.expired_date,
    COUNT(cd.id) as client_count,
    SUM(CASE WHEN cd.website_service_expired < CURDATE() OR cd.domain_expired < CURDATE() OR cd.hosting_expired < CURDATE() THEN 1 ELSE 0 END) as expired_clients,
    SUM(CASE WHEN cd.website_service_expired <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) OR cd.domain_expired <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) OR cd.hosting_expired <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as expiring_clients
FROM domain_registers dr
LEFT JOIN client_data cd ON dr.id = cd.domain_register_id
GROUP BY dr.id, dr.name, dr.status, dr.expired_date;
