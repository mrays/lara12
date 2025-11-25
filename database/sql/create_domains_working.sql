-- Create domains table for individual domain management (Working Version)
-- Run this SQL script directly in MySQL/MariaDB

-- Step 1: Create the domains table (basic version)
CREATE TABLE domains (
    id INT AUTO_INCREMENT PRIMARY KEY,
    domain_name VARCHAR(255) NOT NULL UNIQUE,
    client_id INT NULL,
    server_id INT NULL,
    domain_register_id INT NULL,
    expired_date DATE NULL,
    status ENUM('active', 'expired', 'pending', 'suspended') DEFAULT 'active',
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Step 2: Add indexes for better performance
CREATE INDEX idx_domain_name ON domains(domain_name);
CREATE INDEX idx_client_id ON domains(client_id);
CREATE INDEX idx_server_id ON domains(server_id);
CREATE INDEX idx_domain_register_id ON domains(domain_register_id);
CREATE INDEX idx_expired_date ON domains(expired_date);
CREATE INDEX idx_status ON domains(status);

-- Step 3: Show table creation result
SELECT 'Domains table created successfully!' as message;

-- Step 4: Check if table exists
SHOW TABLES LIKE 'domains';

-- Step 5: Show table structure
DESCRIBE domains;

-- Step 6: Insert sample domains
INSERT INTO domains (
    domain_name, 
    client_id, 
    server_id, 
    domain_register_id, 
    expired_date, 
    status, 
    notes
) VALUES 
('example.com', 1, 1, 1, DATE_ADD(CURDATE(), INTERVAL 2 MONTH), 'active', 'Main client domain, hosted on server 1'),
('test-site.org', 2, 1, 2, DATE_ADD(CURDATE(), INTERVAL 15 DAY), 'active', 'Client domain expiring in 15 days'),
('old-business.net', 3, 2, 2, DATE_SUB(CURDATE(), INTERVAL 1 MONTH), 'expired', 'Domain expired, needs renewal'),
('new-startup.io', NULL, NULL, 3, DATE_ADD(CURDATE(), INTERVAL 1 YEAR), 'pending', 'New domain waiting for client assignment'),
('suspended-site.co', 4, 2, 4, DATE_ADD(CURDATE(), INTERVAL 6 MONTH), 'suspended', 'Suspended due to payment issues'),
('custom-expired.info', 5, 3, 5, '2025-12-31', 'active', 'Domain with manually set expiration date'),
('standalone-domain.com', NULL, NULL, NULL, DATE_ADD(CURDATE(), INTERVAL 8 MONTH), 'active', 'Domain without client/server/register assignments'),
('test-domain.tech', NULL, NULL, NULL, NULL, 'pending', 'Testing domain without expiration date');

-- Step 7: Show results
SELECT 'Sample domains inserted successfully!' as message;

-- Step 8: Show statistics
SELECT 
    COUNT(*) as total_domains,
    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_domains,
    SUM(CASE WHEN status = 'expired' THEN 1 ELSE 0 END) as expired_domains,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_domains,
    SUM(CASE WHEN status = 'suspended' THEN 1 ELSE 0 END) as suspended_domains
FROM domains;

-- Step 9: Show the data
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
