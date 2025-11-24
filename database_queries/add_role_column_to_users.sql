-- =====================================================
-- Add Role Column to Users Table
-- =====================================================
-- File: add_role_column_to_users.sql
-- Description: Menambahkan kolom role ke tabel users untuk role-based access control
-- Date: 2025-01-25

-- Cek apakah kolom role sudah ada
-- SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
-- WHERE TABLE_NAME = 'users' AND COLUMN_NAME = 'role';

-- Tambahkan kolom role jika belum ada
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS role ENUM('admin', 'client', 'staff') NOT NULL DEFAULT 'client';

-- Tambahkan index untuk performance
ALTER TABLE users 
ADD INDEX IF NOT EXISTS idx_users_role (role);

-- =====================================================
-- Set Default Admin User (Optional)
-- =====================================================

-- Update user pertama menjadi admin (sesuaikan dengan ID yang sesuai)
-- UPDATE users SET role = 'admin' WHERE id = 1;

-- Atau update berdasarkan email
-- UPDATE users SET role = 'admin' WHERE email = 'admin@exputra.com';

-- =====================================================
-- Verification Queries
-- =====================================================

-- Cek struktur kolom role
-- DESCRIBE users;

-- Cek distribusi role
-- SELECT role, COUNT(*) as total FROM users GROUP BY role;

-- Cek admin users
-- SELECT id, name, email, role, created_at FROM users WHERE role = 'admin';

-- =====================================================
-- Sample Data untuk Testing (Optional)
-- =====================================================

-- Uncomment jika ingin membuat sample admin user
/*
INSERT INTO users (name, email, email_verified_at, password, role, created_at, updated_at) 
VALUES (
    'Admin User', 
    'admin@exputra.com', 
    NOW(), 
    '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
    'admin', 
    NOW(), 
    NOW()
);
*/

-- =====================================================
-- Useful Management Queries
-- =====================================================

-- Promote user to admin
-- UPDATE users SET role = 'admin' WHERE email = 'user@example.com';

-- Demote admin to client
-- UPDATE users SET role = 'client' WHERE id = 2;

-- List all admin users
-- SELECT id, name, email, role, created_at FROM users WHERE role = 'admin' ORDER BY created_at;

-- Count users by role
-- SELECT 
--     role,
--     COUNT(*) as total,
--     COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as last_30_days
-- FROM users 
-- GROUP BY role;
