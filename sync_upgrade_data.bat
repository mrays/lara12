@echo off
echo ========================================
echo Sync Upgrade Request Data
echo ========================================

echo.
echo Step 1: Clear caches...
php artisan cache:clear
php artisan config:clear
composer dump-autoload

echo.
echo Step 2: Check current data...
php artisan sync:upgrade-data

echo.
echo Step 3: Create sample data for testing...
php artisan sync:upgrade-data --create-sample

echo.
echo Step 4: Test admin access...
echo.
echo Admin panel available at: /admin/upgrade-requests
echo.
echo Sample login credentials:
echo - Admin: admin@test.com / password
echo - Client: client@test.com / password
echo.

echo ========================================
echo Data sync completed!
echo ========================================
echo.
echo Next steps:
echo 1. Login as admin: /login
echo 2. Access: /admin/upgrade-requests
echo 3. You should see upgrade requests from clients
echo.
pause
