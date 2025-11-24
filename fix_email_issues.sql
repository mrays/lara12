-- =====================================================
-- Fix Email Issues - Exputra Cloud
-- =====================================================
-- File: fix_email_issues.sql
-- Description: SQL untuk memperbaiki masalah email forgot password
-- Usage: Jalankan di phpMyAdmin atau MySQL client
-- Date: 2025-01-25

-- 1. Create password_reset_tokens table if not exists
CREATE TABLE IF NOT EXISTS password_reset_tokens (
    email VARCHAR(255) NOT NULL PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    
    INDEX idx_password_reset_tokens_email (email),
    INDEX idx_password_reset_tokens_token (token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Clear any existing password reset tokens (optional)
-- DELETE FROM password_reset_tokens WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 HOUR);

-- 3. Check if table was created successfully
SELECT 
    TABLE_NAME,
    TABLE_ROWS,
    CREATE_TIME
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'password_reset_tokens';

-- 4. Show table structure
DESCRIBE password_reset_tokens;

-- 5. Test insert (will be deleted automatically)
INSERT INTO password_reset_tokens (email, token, created_at) 
VALUES ('test@example.com', 'test_token_123', NOW())
ON DUPLICATE KEY UPDATE token = VALUES(token), created_at = VALUES(created_at);

-- 6. Verify insert worked
SELECT * FROM password_reset_tokens WHERE email = 'test@example.com';

-- 7. Clean up test data
DELETE FROM password_reset_tokens WHERE email = 'test@example.com';

-- 8. Final verification
SELECT COUNT(*) as total_tokens FROM password_reset_tokens;

-- =====================================================
-- NOTES:
-- =====================================================
-- Setelah menjalankan SQL ini, pastikan juga:
-- 1. Update .env file dengan email credentials yang benar
-- 2. Set MAIL_MAILER=smtp (bukan log)
-- 3. Restart web server jika perlu
-- 4. Test forgot password functionality
-- =====================================================
