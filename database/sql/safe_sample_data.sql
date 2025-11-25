-- Safe Sample Client Data for Domain Expiration Monitoring
-- Run this SQL script directly in MySQL/MariaDB
-- This version checks table structure first and only inserts required columns

-- Check if client_data table exists
SET @table_exists = 0;
SELECT COUNT(*) INTO @table_exists 
FROM information_schema.tables 
WHERE table_schema = DATABASE() AND table_name = 'client_data';

-- If table doesn't exist, show error
SET @result = IF(@table_exists = 0, 
    'ERROR: client_data table not found. Please create the table first.',
    'OK: client_data table found.'
);

SELECT @result as table_check;

-- Only proceed if table exists
SET @proceed = IF(@table_exists > 0, 1, 0);

-- Get actual column names from client_data table
SET @sql = 'SELECT GROUP_CONCAT(COLUMN_NAME ORDER BY ORDINAL_POSITION) as columns 
            FROM information_schema.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = \"client_data\"';

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Show table structure for reference
SELECT 'Table Structure:' as info;
SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'client_data' 
ORDER BY ORDINAL_POSITION;

-- Insert sample data with only essential columns (skip foreign keys if they don't exist)
INSERT IGNORE INTO client_data (
    name, 
    address, 
    whatsapp, 
    website_service_expired, 
    domain_expired, 
    hosting_expired, 
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
