-- =====================================================
-- Service Upgrade Requests Table Creation
-- =====================================================
-- File: create_service_upgrade_requests.sql
-- Description: SQL query untuk membuat tabel service_upgrade_requests
-- Date: 2025-01-25

-- Drop table jika sudah ada (optional)
-- DROP TABLE IF EXISTS service_upgrade_requests;

-- Buat tabel service_upgrade_requests
CREATE TABLE service_upgrade_requests (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    service_id BIGINT UNSIGNED NOT NULL,
    client_id BIGINT UNSIGNED NOT NULL,
    current_plan VARCHAR(255) NOT NULL,
    requested_plan VARCHAR(255) NOT NULL,
    current_price DECIMAL(10,2) NOT NULL,
    requested_price DECIMAL(10,2) NOT NULL,
    upgrade_reason ENUM('need_more_resources', 'additional_features', 'business_growth', 'performance_improvement', 'other') NOT NULL,
    additional_notes TEXT NULL,
    status ENUM('pending', 'approved', 'rejected', 'processing') NOT NULL DEFAULT 'pending',
    admin_notes TEXT NULL,
    processed_by BIGINT UNSIGNED NULL,
    processed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign Key Constraints
    CONSTRAINT fk_service_upgrade_requests_service_id 
        FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE,
    
    CONSTRAINT fk_service_upgrade_requests_client_id 
        FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
    
    CONSTRAINT fk_service_upgrade_requests_processed_by 
        FOREIGN KEY (processed_by) REFERENCES users(id) ON DELETE SET NULL,
    
    -- Indexes untuk performance
    INDEX idx_service_upgrade_requests_status_created (status, created_at),
    INDEX idx_service_upgrade_requests_client_service (client_id, service_id),
    INDEX idx_service_upgrade_requests_service_id (service_id),
    INDEX idx_service_upgrade_requests_client_id (client_id),
    INDEX idx_service_upgrade_requests_processed_by (processed_by),
    INDEX idx_service_upgrade_requests_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Sample Data (Optional - untuk testing)
-- =====================================================

-- Uncomment baris di bawah jika ingin insert sample data
/*
INSERT INTO service_upgrade_requests (
    service_id, 
    client_id, 
    current_plan, 
    requested_plan, 
    current_price, 
    requested_price, 
    upgrade_reason, 
    additional_notes, 
    status
) VALUES 
(1, 2, 'Basic Plan', 'Premium Plan', 100000.00, 250000.00, 'business_growth', 'Need more storage and bandwidth for growing business', 'pending'),
(2, 3, 'Standard Plan', 'Enterprise Plan', 200000.00, 500000.00, 'need_more_resources', 'Current plan is not sufficient for our needs', 'pending'),
(3, 4, 'Basic Plan', 'Standard Plan', 100000.00, 200000.00, 'additional_features', 'Need SSL certificate and advanced security features', 'approved');
*/

-- =====================================================
-- Verification Queries
-- =====================================================

-- Cek struktur tabel
-- DESCRIBE service_upgrade_requests;

-- Cek foreign key constraints
-- SELECT 
--     CONSTRAINT_NAME,
--     COLUMN_NAME,
--     REFERENCED_TABLE_NAME,
--     REFERENCED_COLUMN_NAME
-- FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
-- WHERE TABLE_NAME = 'service_upgrade_requests' 
-- AND REFERENCED_TABLE_NAME IS NOT NULL;

-- Cek indexes
-- SHOW INDEX FROM service_upgrade_requests;

-- =====================================================
-- Useful Queries untuk Management
-- =====================================================

-- Query untuk cek pending requests
-- SELECT 
--     sur.id,
--     sur.current_plan,
--     sur.requested_plan,
--     sur.current_price,
--     sur.requested_price,
--     u.name as client_name,
--     s.product as service_name,
--     sur.created_at
-- FROM service_upgrade_requests sur
-- JOIN users u ON sur.client_id = u.id
-- JOIN services s ON sur.service_id = s.id
-- WHERE sur.status = 'pending'
-- ORDER BY sur.created_at DESC;

-- Query untuk statistik status
-- SELECT 
--     status,
--     COUNT(*) as total,
--     AVG(requested_price - current_price) as avg_price_increase
-- FROM service_upgrade_requests 
-- GROUP BY status;

-- Query untuk cek duplicate pending requests
-- SELECT 
--     service_id,
--     client_id,
--     COUNT(*) as pending_count
-- FROM service_upgrade_requests 
-- WHERE status = 'pending'
-- GROUP BY service_id, client_id
-- HAVING COUNT(*) > 1;
