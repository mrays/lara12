-- Sample Domain Registers Data for Testing
-- Run this SQL script directly in MySQL/MariaDB

-- Insert sample domain registers with various expiration scenarios
INSERT IGNORE INTO domain_registers (
    name, 
    login_link, 
    expired_date, 
    username, 
    password, 
    notes, 
    created_at, 
    updated_at
) VALUES 
-- Domain Register 1: Expiring in 15 days (Warning)
(
    'GoDaddy',
    'https://godaddy.com/login',
    DATE_ADD(CURDATE(), INTERVAL 15 DAY),
    'admin@godaddy.com',
    'godaddy123',
    'Main domain register for US clients, expiring soon',
    NOW(),
    NOW()
),

-- Domain Register 2: Expired 5 days ago (Expired)
(
    'Namecheap',
    'https://namecheap.com/login',
    DATE_SUB(CURDATE(), INTERVAL 5 DAY),
    'admin@namecheap.com',
    'namecheap456',
    'Budget-friendly domain register, currently expired',
    NOW(),
    NOW()
),

-- Domain Register 3: Safe for 6 months (Active)
(
    'Cloudflare',
    'https://dash.cloudflare.com/login',
    DATE_ADD(CURDATE(), INTERVAL 6 MONTH),
    'admin@cloudflare.com',
    'cloudflare789',
    'Modern DNS and domain management, safe for now',
    NOW(),
    NOW()
),

-- Domain Register 4: Critical - expiring in 3 days (Warning)
(
    'Google Domains',
    'https://domains.google.com/registrar',
    DATE_ADD(CURDATE(), INTERVAL 3 DAY),
    'admin@google.com',
    'google123',
    'Google domain register, critical expiration!',
    NOW(),
    NOW()
),

-- Domain Register 5: Safe for 4 months (Active)
(
    'Amazon Route 53',
    'https://console.aws.amazon.com/route53',
    DATE_ADD(CURDATE(), INTERVAL 4 MONTH),
    'admin@aws.com',
    'aws456',
    'AWS domain register, active and safe',
    NOW(),
    NOW()
),

-- Domain Register 6: All services expired (Expired)
(
    'Bluehost',
    'https://my.bluehost.com/hosting/login',
    DATE_SUB(CURDATE(), INTERVAL 15 DAY),
    'admin@bluehost.com',
    'bluehost789',
    'Hosting provider with domain services, long expired',
    NOW(),
    NOW()
),

-- Domain Register 7: Newly registered (Safe)
(
    'Shopify',
    'https://shopify.com/login',
    DATE_ADD(CURDATE(), INTERVAL 1 YEAR),
    'admin@shopify.com',
    'shopify123',
    'E-commerce platform domain register, very safe',
    NOW(),
    NOW()
),

-- Domain Register 8: Medium risk (Expiring Soon)
(
    'Network Solutions',
    'https://www.networksolutions.com/manage-it/index.jsp',
    DATE_ADD(CURDATE(), INTERVAL 1 MONTH),
    'admin@netsol.com',
    'netsol456',
    'Traditional domain register, expiring in 1 month',
    NOW(),
    NOW()
);

-- Show results
SELECT 'Sample Domain Registers Created Successfully!' as message;
SELECT 
    COUNT(*) as total_registers,
    SUM(CASE WHEN expired_date < CURDATE() THEN 1 ELSE 0 END) as expired_registers,
    SUM(CASE WHEN expired_date >= CURDATE() AND expired_date <= DATE_ADD(CURDATE(), INTERVAL 3 MONTH) THEN 1 ELSE 0 END) as expiring_soon,
    SUM(CASE WHEN expired_date > DATE_ADD(CURDATE(), INTERVAL 3 MONTH) THEN 1 ELSE 0 END) as safe_registers
FROM domain_registers;

-- Show the data
SELECT 'Sample Domain Registers Preview' as info;
SELECT 
    name,
    login_link,
    expired_date,
    username,
    CASE 
        WHEN expired_date < CURDATE() THEN 'Expired'
        WHEN expired_date <= DATE_ADD(CURDATE(), INTERVAL 3 MONTH) THEN 'Expiring Soon'
        ELSE 'Safe'
    END as expiration_status,
    notes
FROM domain_registers
ORDER BY expired_date;
