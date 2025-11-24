-- =====================================================
-- Quick Fix untuk Laravel Auth Tables
-- =====================================================
-- File: quick_fix_auth.sql
-- Description: Query cepat untuk fix error auth tables
-- Usage: Copy paste semua query ini ke phpMyAdmin atau MySQL
-- Date: 2025-01-25

-- 1. Password Reset Tokens Table (WAJIB untuk forgot password)
CREATE TABLE IF NOT EXISTS password_reset_tokens (
    email VARCHAR(255) NOT NULL PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    
    INDEX idx_password_reset_tokens_email (email),
    INDEX idx_password_reset_tokens_token (token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Add Role Column to Users (WAJIB untuk admin access)
ALTER TABLE users ADD COLUMN IF NOT EXISTS role ENUM('admin', 'client', 'staff') NOT NULL DEFAULT 'client';
ALTER TABLE users ADD INDEX IF NOT EXISTS idx_users_role (role);

-- 3. Service Upgrade Requests Table (untuk upgrade system)
CREATE TABLE IF NOT EXISTS service_upgrade_requests (
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
    
    CONSTRAINT fk_service_upgrade_requests_service_id 
        FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE,
    CONSTRAINT fk_service_upgrade_requests_client_id 
        FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_service_upgrade_requests_processed_by 
        FOREIGN KEY (processed_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_service_upgrade_requests_status_created (status, created_at),
    INDEX idx_service_upgrade_requests_client_service (client_id, service_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Sessions Table (optional, jika menggunakan database sessions)
CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(255) NOT NULL PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload LONGTEXT NOT NULL,
    last_activity INT NOT NULL,
    
    INDEX idx_sessions_user_id (user_id),
    INDEX idx_sessions_last_activity (last_activity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Verification Queries (uncomment untuk test)
-- =====================================================

-- Cek semua tabel sudah ada:
-- SHOW TABLES LIKE '%password_reset_tokens%';
-- SHOW TABLES LIKE '%service_upgrade_requests%';

-- Cek struktur users table:
-- DESCRIBE users;

-- Cek admin users:
-- SELECT id, name, email, role FROM users WHERE role = 'admin';

-- =====================================================
-- Create Admin User (uncomment dan sesuaikan)
-- =====================================================

-- Buat admin user baru:
-- INSERT INTO users (name, email, email_verified_at, password, role, created_at, updated_at) 
-- VALUES (
--     'Admin User', 
--     'admin@exputra.com', 
--     NOW(), 
--     '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
--     'admin', 
--     NOW(), 
--     NOW()
-- );

-- Atau promote user existing:
-- UPDATE users SET role = 'admin' WHERE email = 'your-email@example.com';
