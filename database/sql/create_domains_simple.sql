-- Create domains table for individual domain management (Simple Version)
-- Run this SQL script directly in MySQL/MariaDB

-- Create the domains table without foreign keys (safer approach)
CREATE TABLE IF NOT EXISTS domains (
    id INT AUTO_INCREMENT PRIMARY KEY,
    domain_name VARCHAR(255) NOT NULL UNIQUE,
    client_id INT NULL,
    server_id INT NULL,
    domain_register_id INT NULL,
    expired_date DATE NULL,
    status ENUM('active', 'expired', 'pending', 'suspended') DEFAULT 'active',
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_domain_name (domain_name),
    INDEX idx_client_id (client_id),
    INDEX idx_server_id (server_id),
    INDEX idx_domain_register_id (domain_register_id),
    INDEX idx_expired_date (expired_date),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Show table creation result
SELECT 'Domains table created successfully!' as message;

-- Insert sample domains for testing (only if tables exist)
SET @client_data_exists = (SELECT COUNT(*) FROM information_schema.tables 
                           WHERE table_schema = DATABASE() AND table_name = 'client_data');

SET @servers_exists = (SELECT COUNT(*) FROM information_schema.tables 
                       WHERE table_schema = DATABASE() AND table_name = 'servers');

SET @domain_registers_exists = (SELECT COUNT(*) FROM information_schema.tables 
                                 WHERE table_schema = DATABASE() AND table_name = 'domain_registers');

-- Insert sample data only if we have the necessary tables
INSERT IGNORE INTO domains (
    domain_name, 
    client_id, 
    server_id, 
    domain_register_id, 
    expired_date, 
    status, 
    notes
) VALUES 
-- Domain 1: Active with client and server (if tables exist)
(
    'example.com',
    IF(@client_data_exists > 0, 1, NULL),
    IF(@servers_exists > 0, 1, NULL),
    IF(@domain_registers_exists > 0, 1, NULL),
    DATE_ADD(CURDATE(), INTERVAL 2 MONTH),
    'active',
    'Main client domain, hosted on server 1'
),

-- Domain 2: Expiring soon
(
    'test-site.org',
    IF(@client_data_exists > 0, 2, NULL),
    IF(@servers_exists > 0, 1, NULL),
    IF(@domain_registers_exists > 0, 2, NULL),
    DATE_ADD(CURDATE(), INTERVAL 15 DAY),
    'active',
    'Client domain expiring in 15 days'
),

-- Domain 3: Expired
(
    'old-business.net',
    IF(@client_data_exists > 0, 3, NULL),
    IF(@servers_exists > 0, 2, NULL),
    IF(@domain_registers_exists > 0, 2, NULL),
    DATE_SUB(CURDATE(), INTERVAL 1 MONTH),
    'expired',
    'Domain expired, needs renewal'
),

-- Domain 4: Pending setup (no assignments)
(
    'new-startup.io',
    NULL,
    NULL,
    IF(@domain_registers_exists > 0, 3, NULL),
    DATE_ADD(CURDATE(), INTERVAL 1 YEAR),
    'pending',
    'New domain waiting for client assignment'
),

-- Domain 5: Suspended
(
    'suspended-site.co',
    IF(@client_data_exists > 0, 4, NULL),
    IF(@servers_exists > 0, 2, NULL),
    IF(@domain_registers_exists > 0, 4, NULL),
    DATE_ADD(CURDATE(), INTERVAL 6 MONTH),
    'suspended',
    'Suspended due to payment issues'
),

-- Domain 6: Manual expired date
(
    'custom-expired.info',
    IF(@client_data_exists > 0, 5, NULL),
    IF(@servers_exists > 0, 3, NULL),
    IF(@domain_registers_exists > 0, 5, NULL),
    '2025-12-31',
    'active',
    'Domain with manually set expiration date'
),

-- Domain 7: No assignments (standalone)
(
    'standalone-domain.com',
    NULL,
    NULL,
    NULL,
    DATE_ADD(CURDATE(), INTERVAL 8 MONTH),
    'active',
    'Domain without client/server/register assignments'
),

-- Domain 8: Testing domain
(
    'test-domain.tech',
    NULL,
    NULL,
    NULL,
    NULL,
    'pending',
    'Testing domain without expiration date'
);

-- Show results
SELECT 'Sample domains inserted successfully!' as message;

-- Show statistics
SELECT 
    COUNT(*) as total_domains,
    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_domains,
    SUM(CASE WHEN status = 'expired' THEN 1 ELSE 0 END) as expired_domains,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_domains,
    SUM(CASE WHEN status = 'suspended' THEN 1 ELSE 0 END) as suspended_domains,
    SUM(CASE WHEN expired_date IS NOT NULL THEN 1 ELSE 0 END) as domains_with_expiration,
    SUM(CASE WHEN client_id IS NOT NULL THEN 1 ELSE 0 END) as domains_with_client,
    SUM(CASE WHEN server_id IS NOT NULL THEN 1 ELSE 0 END) as domains_with_server,
    SUM(CASE WHEN domain_register_id IS NOT NULL THEN 1 ELSE 0 END) as domains_with_register
FROM domains;

-- Show the data
SELECT 'Sample Domains Preview' as info;
SELECT 
    d.domain_name,
    d.expired_date,
    d.status,
    d.notes,
    CASE 
        WHEN d.client_id IS NOT NULL THEN CONCAT('Client ID: ', d.client_id)
        ELSE 'No Client'
    END as client_info,
    CASE 
        WHEN d.server_id IS NOT NULL THEN CONCAT('Server ID: ', d.server_id)
        ELSE 'No Server'
    END as server_info,
    CASE 
        WHEN d.domain_register_id IS NOT NULL THEN CONCAT('Register ID: ', d.domain_register_id)
        ELSE 'No Register'
    END as register_info
FROM domains d
ORDER BY d.expired_date ASC, d.domain_name;
