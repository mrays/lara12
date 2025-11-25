-- Sample Client Data for Domain Expiration Monitoring
-- Run this SQL script directly in MySQL/MariaDB to create sample data
-- Make sure you have domain_registers, servers, and users tables with existing data

-- Check and create sample data for domain registers if needed
INSERT IGNORE INTO domain_registers (name, login_link, expired_date, username, password, notes, created_at, updated_at) VALUES
('GoDaddy', 'https://godaddy.com/login', '2025-12-31', 'admin@godaddy.com', 'godaddy123', 'Main domain register for US clients', NOW(), NOW()),
('Namecheap', 'https://namecheap.com/login', '2025-06-30', 'admin@namecheap.com', 'namecheap456', 'Budget-friendly domain register', NOW(), NOW()),
('Cloudflare', 'https://dash.cloudflare.com/login', '2025-09-15', 'admin@cloudflare.com', 'cloudflare789', 'Modern DNS and domain management', NOW(), NOW());

-- Check and create sample data for servers if needed  
INSERT IGNORE INTO servers (name, ip_address, location, specs, username, password, notes, created_at, updated_at) VALUES
('Server US East', '192.168.1.10', 'New York', '8GB RAM, 4 CPU, 100GB SSD', 'root', 'server123', 'Primary US server', NOW(), NOW()),
('Server EU West', '192.168.1.20', 'London', '16GB RAM, 8 CPU, 200GB SSD', 'root', 'server456', 'European server', NOW(), NOW()),
('Server Asia', '192.168.1.30', 'Singapore', '12GB RAM, 6 CPU, 150GB SSD', 'root', 'server789', 'Asia Pacific server', NOW(), NOW());

-- Check and create sample client users if needed
INSERT IGNORE INTO users (name, email, password, role, created_at, updated_at) VALUES
('Client User 1', 'client1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'client', NOW(), NOW()),
('Client User 2', 'client2@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'client', NOW(), NOW()),
('Client User 3', 'client3@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'client', NOW(), NOW()),
('Client User 4', 'client4@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'client', NOW(), NOW()),
('Client User 5', 'client5@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'client', NOW(), NOW()),
('Client User 6', 'client6@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'client', NOW(), NOW());

-- Sample Client Data with various expiration scenarios
-- Get the first IDs from the reference tables
SET @domain_register_1 = (SELECT id FROM domain_registers LIMIT 1);
SET @domain_register_2 = (SELECT id FROM domain_registers ORDER BY id LIMIT 1 OFFSET 1);
SET @domain_register_3 = (SELECT id FROM domain_registers ORDER BY id LIMIT 1 OFFSET 2);

SET @server_1 = (SELECT id FROM servers LIMIT 1);
SET @server_2 = (SELECT id FROM servers ORDER BY id LIMIT 1 OFFSET 1);
SET @server_3 = (SELECT id FROM servers ORDER BY id LIMIT 1 OFFSET 2);

SET @client_user_1 = (SELECT id FROM users WHERE role = 'client' LIMIT 1);
SET @client_user_2 = (SELECT id FROM users WHERE role = 'client' ORDER BY id LIMIT 1 OFFSET 1);
SET @client_user_3 = (SELECT id FROM users WHERE role = 'client' ORDER BY id LIMIT 1 OFFSET 2);
SET @client_user_4 = (SELECT id FROM users WHERE role = 'client' ORDER BY id LIMIT 1 OFFSET 3);
SET @client_user_5 = (SELECT id FROM users WHERE role = 'client' ORDER BY id LIMIT 1 OFFSET 4);
SET @client_user_6 = (SELECT id FROM users WHERE role = 'client' ORDER BY id LIMIT 1 OFFSET 5);

-- Insert sample client data
INSERT IGNORE INTO client_data (
    name, 
    address, 
    whatsapp, 
    website_service_expired, 
    domain_expired, 
    hosting_expired, 
    server_id, 
    domain_register_id, 
    user_id, 
    status, 
    notes, 
    created_at, 
    updated_at
) VALUES 
-- Client 1: Expiring in 15 days (Warning)
(
    'PT. Teknologi Maju',
    'Jl. Sudirman No. 123, Jakarta Selatan',
    '+6281234567890',
    DATE_ADD(CURDATE(), INTERVAL 15 DAY),
    DATE_ADD(CURDATE(), INTERVAL 15 DAY),
    DATE_ADD(CURDATE(), INTERVAL 2 MONTH),
    @server_1,
    @domain_register_1,
    @client_user_1,
    'warning',
    'Client penting, perlu diperhatikan renewalnya',
    NOW(),
    NOW()
),

-- Client 2: Expired 5 days ago (Expired)
(
    'CV. Karya Digital',
    'Jl. Gatot Subroto No. 456, Jakarta Pusat',
    '+6282345678901',
    DATE_SUB(CURDATE(), INTERVAL 5 DAY),
    DATE_SUB(CURDATE(), INTERVAL 5 DAY),
    DATE_ADD(CURDATE(), INTERVAL 10 DAY),
    @server_2,
    @domain_register_2,
    @client_user_2,
    'expired',
    'Domain expired, perlu segera renewal',
    NOW(),
    NOW()
),

-- Client 3: Safe for 6 months (Active)
(
    'UD. Jaya Abadi',
    'Jl. Thamrin No. 789, Jakarta Barat',
    '+6283456789012',
    DATE_ADD(CURDATE(), INTERVAL 6 MONTH),
    DATE_ADD(CURDATE(), INTERVAL 6 MONTH),
    DATE_ADD(CURDATE(), INTERVAL 8 MONTH),
    @server_3,
    @domain_register_3,
    @client_user_3,
    'active',
    'Client regular, semua aman',
    NOW(),
    NOW()
),

-- Client 4: Critical - expiring in 3 days (Warning)
(
    'PT. Solusi Bisnis',
    'Jl. MH Thamrin No. 321, Jakarta',
    '+6284567890123',
    DATE_ADD(CURDATE(), INTERVAL 3 DAY),
    DATE_ADD(CURDATE(), INTERVAL 3 DAY),
    DATE_ADD(CURDATE(), INTERVAL 1 MONTH),
    @server_1,
    @domain_register_1,
    @client_user_4,
    'warning',
    'Critical: akan expired dalam 3 hari!',
    NOW(),
    NOW()
),

-- Client 5: Safe for 4 months (Active)
(
    'CV. Media Kreatif',
    'Jl. Kemang No. 567, Jakarta Selatan',
    '+6285678901234',
    DATE_ADD(CURDATE(), INTERVAL 4 MONTH),
    DATE_ADD(CURDATE(), INTERVAL 4 MONTH),
    DATE_ADD(CURDATE(), INTERVAL 3 MONTH),
    @server_2,
    @domain_register_2,
    @client_user_5,
    'active',
    'Client baru, semua layanan aktif',
    NOW(),
    NOW()
),

-- Client 6: All services expired (Expired)
(
    'PT. Inovasi Teknologi',
    'Jl. SCBD No. 890, Jakarta',
    '+6286789012345',
    DATE_SUB(CURDATE(), INTERVAL 15 DAY),
    DATE_SUB(CURDATE(), INTERVAL 15 DAY),
    DATE_SUB(CURDATE(), INTERVAL 10 DAY),
    @server_3,
    @domain_register_3,
    @client_user_6,
    'expired',
    'Semua layanan expired, urgent!',
    NOW(),
    NOW()
);

-- Show statistics after insertion
SELECT 'Sample Data Statistics' as info;
SELECT 
    COUNT(*) as total_clients,
    SUM(CASE WHEN domain_expired < CURDATE() THEN 1 ELSE 0 END) as expired_domains,
    SUM(CASE WHEN domain_expired >= CURDATE() AND domain_expired <= DATE_ADD(CURDATE(), INTERVAL 3 MONTH) THEN 1 ELSE 0 END) as expiring_soon,
    SUM(CASE WHEN domain_expired > DATE_ADD(CURDATE(), INTERVAL 3 MONTH) THEN 1 ELSE 0 END) as safe_domains
FROM client_data;

-- Show sample data
SELECT 'Sample Client Data' as info;
SELECT 
    c.name,
    c.domain_expired,
    c.website_service_expired,
    c.hosting_expired,
    c.status,
    CASE 
        WHEN c.domain_expired < CURDATE() THEN 'Expired'
        WHEN c.domain_expired <= DATE_ADD(CURDATE(), INTERVAL 3 MONTH) THEN 'Expiring Soon'
        ELSE 'Safe'
    END as expiration_status,
    dr.name as domain_register_name
FROM client_data c
LEFT JOIN domain_registers dr ON c.domain_register_id = dr.id
ORDER BY c.domain_expired;
