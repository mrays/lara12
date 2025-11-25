-- Basic domains table creation - copy and paste this directly

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

-- Add sample data
INSERT INTO domains (domain_name, client_id, server_id, domain_register_id, expired_date, status, notes) VALUES
('example.com', 1, 1, 1, '2025-12-15', 'active', 'Main client domain'),
('test-site.org', 2, 1, 2, '2025-12-01', 'active', 'Expiring soon'),
('old-business.net', 3, 2, 2, '2025-10-15', 'expired', 'Needs renewal'),
('new-startup.io', NULL, NULL, 3, '2026-11-25', 'pending', 'New domain'),
('suspended-site.co', 4, 2, 4, '2026-05-25', 'suspended', 'Payment issues'),
('custom-expired.info', 5, 3, 5, '2025-12-31', 'active', 'Manual date'),
('standalone-domain.com', NULL, NULL, NULL, '2026-07-25', 'active', 'No assignments'),
('test-domain.tech', NULL, NULL, NULL, NULL, 'pending', 'No expiration');

-- Verify table created
SELECT 'Domains table created and populated!' as result;
SELECT COUNT(*) as total_domains FROM domains;
