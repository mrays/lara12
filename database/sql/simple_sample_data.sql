-- Simple Sample Client Data for Domain Expiration Monitoring
-- Run this SQL script directly in MySQL/MariaDB
-- This version only uses client_data table with no dependencies

-- Insert sample client data (all foreign keys as NULL)
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
    NULL, -- server_id
    NULL, -- domain_register_id  
    NULL, -- user_id
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
    NULL,
    NULL,
    NULL,
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
    NULL,
    NULL,
    NULL,
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
    NULL,
    NULL,
    NULL,
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
    NULL,
    NULL,
    NULL,
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
    NULL,
    NULL,
    NULL,
    'expired',
    'Semua layanan expired, urgent!',
    NOW(),
    NOW()
);

-- Show results
SELECT 'Sample Data Created Successfully!' as message;
SELECT 
    COUNT(*) as total_clients,
    SUM(CASE WHEN domain_expired < CURDATE() THEN 1 ELSE 0 END) as expired_domains,
    SUM(CASE WHEN domain_expired >= CURDATE() AND domain_expired <= DATE_ADD(CURDATE(), INTERVAL 3 MONTH) THEN 1 ELSE 0 END) as expiring_soon,
    SUM(CASE WHEN domain_expired > DATE_ADD(CURDATE(), INTERVAL 3 MONTH) THEN 1 ELSE 0 END) as safe_domains
FROM client_data;

-- Show the data
SELECT 'Sample Client Data Preview' as info;
SELECT 
    name,
    domain_expired,
    website_service_expired,
    hosting_expired,
    status,
    CASE 
        WHEN domain_expired < CURDATE() THEN 'Expired'
        WHEN domain_expired <= DATE_ADD(CURDATE(), INTERVAL 3 MONTH) THEN 'Expiring Soon'
        ELSE 'Safe'
    END as expiration_status,
    whatsapp,
    notes
FROM client_data
ORDER BY domain_expired;
