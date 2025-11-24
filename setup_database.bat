@echo off
echo ========================================
echo Database Setup for Laravel Auth System
echo ========================================

echo.
echo This script will guide you through setting up required database tables.
echo.

echo Step 1: Required SQL Queries
echo ========================================
echo.
echo Please run these SQL queries in your database (phpMyAdmin, MySQL Workbench, etc.):
echo.

echo 1. Password Reset Tokens Table:
echo ----------------------------------------
echo CREATE TABLE IF NOT EXISTS password_reset_tokens (
echo     email VARCHAR(255) NOT NULL PRIMARY KEY,
echo     token VARCHAR(255) NOT NULL,
echo     created_at TIMESTAMP NULL DEFAULT NULL,
echo     INDEX idx_password_reset_tokens_email (email)
echo ^) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
echo.

echo 2. Add Role Column to Users (if not exists):
echo ----------------------------------------
echo ALTER TABLE users ADD COLUMN IF NOT EXISTS role ENUM('admin', 'client', 'staff') NOT NULL DEFAULT 'client';
echo ALTER TABLE users ADD INDEX IF NOT EXISTS idx_users_role (role);
echo.

echo 3. Service Upgrade Requests Table:
echo ----------------------------------------
echo (See file: database_queries/create_service_upgrade_requests.sql)
echo.

echo ========================================
echo Quick Setup Options:
echo ========================================
echo.
echo Option A: Copy queries from files:
echo   - database_queries/create_password_reset_tokens.sql
echo   - database_queries/add_role_column_to_users.sql  
echo   - database_queries/create_service_upgrade_requests.sql
echo.
echo Option B: Run complete auth tables setup:
echo   - database_queries/create_auth_tables.sql
echo.

echo ========================================
echo After running SQL queries:
echo ========================================
echo.
echo 1. Clear Laravel caches:
php artisan cache:clear
php artisan config:clear
php artisan view:clear

echo.
echo 2. Create admin user:
echo    php artisan admin:create
echo.
echo 3. Test the system:
echo    - Login: /login
echo    - Forgot Password: /forgot-password  
echo    - Admin Panel: /admin/upgrade-requests
echo.

echo ========================================
echo Setup completed!
echo ========================================
echo.
echo If you encounter any issues, check:
echo - TROUBLESHOOTING.md
echo - Laravel logs: storage/logs/laravel.log
echo.
pause
