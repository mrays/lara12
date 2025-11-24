@echo off
echo ========================================
echo Debug Upgrade System
echo ========================================

echo.
echo Step 1: Clear all caches...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
composer dump-autoload

echo.
echo Step 2: Check routes...
php artisan route:list | findstr upgrade

echo.
echo Step 3: Check if tables exist...
echo Please run these queries in your database to verify:
echo.
echo -- Check if service_upgrade_requests table exists:
echo SHOW TABLES LIKE 'service_upgrade_requests';
echo.
echo -- Check if role column exists in users:
echo DESCRIBE users;
echo.
echo -- Check if password_reset_tokens exists:
echo SHOW TABLES LIKE 'password_reset_tokens';
echo.

echo Step 4: Create missing tables if needed...
echo If any tables are missing, run:
echo - quick_fix_auth.sql (contains all required tables)
echo.

echo Step 5: Test the system...
echo 1. Login to /client/services/1/manage
echo 2. Click "Upgrade Layanan"
echo 3. Select a plan and submit
echo 4. Check browser console for detailed errors
echo.

echo ========================================
echo Debug completed!
echo ========================================
echo.
echo If still having issues:
echo 1. Check Laravel logs: storage/logs/laravel.log
echo 2. Check browser console for JavaScript errors
echo 3. Verify database tables exist
echo 4. Ensure user has access to the service
echo.
pause
