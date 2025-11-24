-- =====================================================
-- Password Reset Tokens Table Creation
-- =====================================================
-- File: create_password_reset_tokens.sql
-- Description: SQL query untuk membuat tabel password_reset_tokens
-- Date: 2025-01-25

-- Drop table jika sudah ada (optional)
-- DROP TABLE IF EXISTS password_reset_tokens;

-- Buat tabel password_reset_tokens
CREATE TABLE password_reset_tokens (
    email VARCHAR(255) NOT NULL PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    
    -- Index untuk performance
    INDEX idx_password_reset_tokens_email (email),
    INDEX idx_password_reset_tokens_token (token),
    INDEX idx_password_reset_tokens_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Alternative: Create password_resets table (Laravel < 9)
-- =====================================================
-- Jika menggunakan Laravel versi lama, gunakan tabel ini:

-- CREATE TABLE password_resets (
--     email VARCHAR(255) NOT NULL,
--     token VARCHAR(255) NOT NULL,
--     created_at TIMESTAMP NULL DEFAULT NULL,
--     
--     INDEX idx_password_resets_email (email),
--     INDEX idx_password_resets_token (token)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Verification Queries
-- =====================================================

-- Cek struktur tabel
-- DESCRIBE password_reset_tokens;

-- Cek apakah tabel sudah ada
-- SHOW TABLES LIKE 'password_reset_tokens';

-- =====================================================
-- Cleanup Queries (untuk maintenance)
-- =====================================================

-- Hapus token yang sudah expired (lebih dari 1 jam)
-- DELETE FROM password_reset_tokens 
-- WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 HOUR);

-- Hapus semua token untuk email tertentu
-- DELETE FROM password_reset_tokens WHERE email = 'user@example.com';

-- Lihat semua active tokens
-- SELECT email, LEFT(token, 10) as token_preview, created_at 
-- FROM password_reset_tokens 
-- ORDER BY created_at DESC;
