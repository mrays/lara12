-- Create domains table for individual domain management
-- Run this SQL script directly in MySQL/MariaDB

-- First, create the domains table without foreign keys
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

-- Add foreign keys only if referenced tables exist
-- Check if client_data table exists before adding FK
SET @client_table_exists = (SELECT COUNT(*) FROM information_schema.tables 
                           WHERE table_schema = DATABASE() AND table_name = 'client_data');

SET @client_fk_exists = (SELECT COUNT(*) FROM information_schema.table_constraints 
                        WHERE table_schema = DATABASE() AND table_name = 'domains' 
                        AND constraint_name = 'fk_domains_client_id');

SET @sql = IF(@client_table_exists = 1 AND @client_fk_exists = 0,
              'ALTER TABLE domains ADD CONSTRAINT fk_domains_client_id 
               FOREIGN KEY (client_id) REFERENCES client_data(id) ON DELETE SET NULL',
              'SELECT "Client FK skipped - table does not exist or FK already exists" as message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check if servers table exists before adding FK
SET @server_table_exists = (SELECT COUNT(*) FROM information_schema.tables 
                           WHERE table_schema = DATABASE() AND table_name = 'servers');

SET @server_fk_exists = (SELECT COUNT(*) FROM information_schema.table_constraints 
                        WHERE table_schema = DATABASE() AND table_name = 'domains' 
                        AND constraint_name = 'fk_domains_server_id');

SET @sql = IF(@server_table_exists = 1 AND @server_fk_exists = 0,
              'ALTER TABLE domains ADD CONSTRAINT fk_domains_server_id 
               FOREIGN KEY (server_id) REFERENCES servers(id) ON DELETE SET NULL',
              'SELECT "Server FK skipped - table does not exist or FK already exists" as message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check if domain_registers table exists before adding FK
SET @register_table_exists = (SELECT COUNT(*) FROM information_schema.tables 
                             WHERE table_schema = DATABASE() AND table_name = 'domain_registers');

SET @register_fk_exists = (SELECT COUNT(*) FROM information_schema.table_constraints 
                          WHERE table_schema = DATABASE() AND table_name = 'domains' 
                          AND constraint_name = 'fk_domains_domain_register_id');

SET @sql = IF(@register_table_exists = 1 AND @register_fk_exists = 0,
              'ALTER TABLE domains ADD CONSTRAINT fk_domains_domain_register_id 
               FOREIGN KEY (domain_register_id) REFERENCES domain_registers(id) ON DELETE SET NULL',
              'SELECT "Register FK skipped - table does not exist or FK already exists" as message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Insert sample domains for testing
INSERT IGNORE INTO domains (
    domain_name, 
    client_id, 
    server_id, 
    domain_register_id, 
    expired_date, 
    status, 
    notes
) VALUES 
-- Domain 1: Active with client and server
(
    'example.com',
    1,
    1,
    1,
    DATE_ADD(CURDATE(), INTERVAL 2 MONTH),
    'active',
    'Main client domain, hosted on server 1'
),

-- Domain 2: Expiring soon
(
    'test-site.org',
    2,
    1,
    2,
    DATE_ADD(CURDATE(), INTERVAL 15 DAY),
    'active',
    'Client domain expiring in 15 days'
),

-- Domain 3: Expired
(
    'old-business.net',
    3,
    2,
    2,
    DATE_SUB(CURDATE(), INTERVAL 1 MONTH),
    'expired',
    'Domain expired, needs renewal'
),

-- Domain 4: Pending setup
(
    'new-startup.io',
    NULL,
    NULL,
    3,
    DATE_ADD(CURDATE(), INTERVAL 1 YEAR),
    'pending',
    'New domain waiting for client assignment'
),

-- Domain 5: Suspended
(
    'suspended-site.co',
    4,
    2,
    4,
    DATE_ADD(CURDATE(), INTERVAL 6 MONTH),
    'suspended',
    'Suspended due to payment issues'
),

-- Domain 6: Manual expired date
(
    'custom-expired.info',
    5,
    3,
    5,
    '2025-12-31',
    'active',
    'Domain with manually set expiration date'
);

-- Show results
SELECT 'Domains table created successfully!' as message;
SELECT 
    COUNT(*) as total_domains,
    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_domains,
    SUM(CASE WHEN status = 'expired' THEN 1 ELSE 0 END) as expired_domains,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_domains,
    SUM(CASE WHEN status = 'suspended' THEN 1 ELSE 0 END) as suspended_domains
FROM domains;

-- Show the data
SELECT 'Sample Domains Preview' as info;
SELECT 
    d.domain_name,
    cd.name as client_name,
    s.name as server_name,
    dr.name as domain_register_name,
    d.expired_date,
    d.status,
    d.notes
FROM domains d
LEFT JOIN client_data cd ON d.client_id = cd.id
LEFT JOIN servers s ON d.server_id = s.id  
LEFT JOIN domain_registers dr ON d.domain_register_id = dr.id
ORDER BY d.expired_date;
