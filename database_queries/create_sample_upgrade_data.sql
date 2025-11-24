-- =====================================================
-- Sample Data untuk Testing Upgrade System
-- =====================================================
-- File: create_sample_upgrade_data.sql
-- Description: Membuat sample data untuk test upgrade request system
-- Date: 2025-01-25

-- =====================================================
-- 1. Create Sample Users (Client & Admin)
-- =====================================================

-- Insert sample client user
INSERT IGNORE INTO users (id, name, email, email_verified_at, password, role, created_at, updated_at) 
VALUES (
    100, 
    'John Doe Client', 
    'client@test.com', 
    NOW(), 
    '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
    'client', 
    NOW(), 
    NOW()
);

-- Insert sample admin user
INSERT IGNORE INTO users (id, name, email, email_verified_at, password, role, created_at, updated_at) 
VALUES (
    101, 
    'Admin User', 
    'admin@test.com', 
    NOW(), 
    '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
    'admin', 
    NOW(), 
    NOW()
);

-- Insert another sample client
INSERT IGNORE INTO users (id, name, email, email_verified_at, password, role, created_at, updated_at) 
VALUES (
    102, 
    'Jane Smith', 
    'jane@test.com', 
    NOW(), 
    '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
    'client', 
    NOW(), 
    NOW()
);

-- =====================================================
-- 2. Create Sample Services
-- =====================================================

-- Service untuk John Doe
INSERT IGNORE INTO services (id, client_id, product, domain, price, billing_cycle, registration_date, due_date, status, notes, created_at, updated_at) 
VALUES (
    100,
    100,
    'Business Website Basic',
    'johndoe.com',
    150000.00,
    'monthly',
    '2024-01-01',
    '2025-02-01',
    'Active',
    'Basic business website package',
    NOW(),
    NOW()
);

-- Service untuk Jane Smith
INSERT IGNORE INTO services (id, client_id, product, domain, price, billing_cycle, registration_date, due_date, status, notes, created_at, updated_at) 
VALUES (
    101,
    102,
    'E-commerce Standard',
    'janestore.com',
    500000.00,
    'yearly',
    '2024-06-01',
    '2025-06-01',
    'Active',
    'Standard e-commerce package',
    NOW(),
    NOW()
);

-- Service lain untuk John Doe
INSERT IGNORE INTO services (id, client_id, product, domain, price, billing_cycle, registration_date, due_date, status, notes, created_at, updated_at) 
VALUES (
    102,
    100,
    'Hosting Shared',
    'johndoe-blog.com',
    75000.00,
    'monthly',
    '2024-03-01',
    '2025-03-01',
    'Active',
    'Shared hosting package',
    NOW(),
    NOW()
);

-- =====================================================
-- 3. Create Sample Upgrade Requests
-- =====================================================

-- Upgrade request dari John Doe (Pending)
INSERT IGNORE INTO service_upgrade_requests (
    id, service_id, client_id, current_plan, requested_plan, 
    current_price, requested_price, upgrade_reason, additional_notes, 
    status, created_at, updated_at
) VALUES (
    100,
    100,
    100,
    'Business Website Basic',
    'Business Website Premium',
    150000.00,
    300000.00,
    'business_growth',
    'Our business is growing rapidly and we need more storage space and bandwidth. Current plan is no longer sufficient for our traffic.',
    'pending',
    NOW(),
    NOW()
);

-- Upgrade request dari Jane Smith (Pending)
INSERT IGNORE INTO service_upgrade_requests (
    id, service_id, client_id, current_plan, requested_plan, 
    current_price, requested_price, upgrade_reason, additional_notes, 
    status, created_at, updated_at
) VALUES (
    101,
    101,
    102,
    'E-commerce Standard',
    'E-commerce Enterprise',
    500000.00,
    1000000.00,
    'additional_features',
    'Need advanced analytics, multi-currency support, and API integrations for our expanding international business.',
    'pending',
    NOW(),
    NOW()
);

-- Upgrade request dari John Doe untuk hosting (Approved - sample)
INSERT IGNORE INTO service_upgrade_requests (
    id, service_id, client_id, current_plan, requested_plan, 
    current_price, requested_price, upgrade_reason, additional_notes, 
    status, admin_notes, processed_by, processed_at, created_at, updated_at
) VALUES (
    102,
    102,
    100,
    'Hosting Shared',
    'Hosting VPS',
    75000.00,
    200000.00,
    'performance_improvement',
    'Website is getting slower due to increased traffic. Need dedicated resources.',
    'approved',
    'Request approved. VPS will be provisioned within 24 hours.',
    101,
    DATE_SUB(NOW(), INTERVAL 1 DAY),
    DATE_SUB(NOW(), INTERVAL 2 DAY),
    DATE_SUB(NOW(), INTERVAL 1 DAY)
);

-- Upgrade request yang ditolak (sample)
INSERT IGNORE INTO service_upgrade_requests (
    id, service_id, client_id, current_plan, requested_plan, 
    current_price, requested_price, upgrade_reason, additional_notes, 
    status, admin_notes, processed_by, processed_at, created_at, updated_at
) VALUES (
    103,
    100,
    100,
    'Business Website Basic',
    'Business Website Enterprise',
    150000.00,
    750000.00,
    'other',
    'Want to upgrade to the highest plan available.',
    'rejected',
    'Current usage does not justify Enterprise plan. Premium plan would be more suitable.',
    101,
    DATE_SUB(NOW(), INTERVAL 3 DAY),
    DATE_SUB(NOW(), INTERVAL 5 DAY),
    DATE_SUB(NOW(), INTERVAL 3 DAY)
);

-- =====================================================
-- Verification Queries
-- =====================================================

-- Cek sample users
-- SELECT id, name, email, role FROM users WHERE id >= 100;

-- Cek sample services
-- SELECT id, client_id, product, domain, price, billing_cycle, status FROM services WHERE id >= 100;

-- Cek sample upgrade requests
-- SELECT 
--     sur.id,
--     u.name as client_name,
--     s.product as service_name,
--     sur.current_plan,
--     sur.requested_plan,
--     sur.status,
--     sur.created_at
-- FROM service_upgrade_requests sur
-- JOIN users u ON sur.client_id = u.id
-- JOIN services s ON sur.service_id = s.id
-- WHERE sur.id >= 100
-- ORDER BY sur.created_at DESC;

-- =====================================================
-- Login Credentials untuk Testing
-- =====================================================

-- Admin Login:
-- Email: admin@test.com
-- Password: password

-- Client Login:
-- Email: client@test.com
-- Password: password

-- Client Login 2:
-- Email: jane@test.com
-- Password: password

-- =====================================================
-- URLs untuk Testing
-- =====================================================

-- Admin Panel: /admin/upgrade-requests
-- Client Panel: /client/services/100/manage (untuk John Doe)
-- Client Panel: /client/services/101/manage (untuk Jane Smith)
