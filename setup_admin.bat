@echo off
echo ========================================
echo Setting up Admin Access Control
echo ========================================

echo.
echo Step 1: Clearing caches...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

echo.
echo Step 2: Regenerating autoload...
composer dump-autoload

echo.
echo Step 3: Database setup required...
echo Please run this SQL query in your database:
echo.
echo ALTER TABLE users ADD COLUMN IF NOT EXISTS role ENUM('admin', 'client', 'staff') NOT NULL DEFAULT 'client';
echo ALTER TABLE users ADD INDEX IF NOT EXISTS idx_users_role (role);
echo.

echo Step 4: Create admin user...
echo You can create admin user with:
echo php artisan admin:create
echo.
echo Or manually update existing user:
echo UPDATE users SET role = 'admin' WHERE email = 'your-email@example.com';
echo.

echo ========================================
echo Setup completed!
echo ========================================
echo.
echo Next steps:
echo 1. Run the SQL queries above in your database
echo 2. Create admin user: php artisan admin:create
echo 3. Access admin panel: /admin/upgrade-requests
echo.
pause
