@echo off
echo ========================================
echo Complete Upgrade System Setup
echo ========================================

echo.
echo This will setup the complete upgrade system with sample data
echo so you can see client requests in the admin panel.
echo.

echo Step 1: Clear all caches...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
composer dump-autoload

echo.
echo Step 2: Database setup required...
echo ========================================
echo Please run these SQL queries in your database:
echo.

echo A. Create required tables (if not exists):
echo    File: quick_fix_auth.sql
echo.

echo B. Create sample data for testing:
echo    File: database_queries/create_sample_upgrade_data.sql
echo.

echo ========================================
echo Manual SQL Setup:
echo ========================================
echo.
echo 1. Open phpMyAdmin or MySQL client
echo 2. Select your database
echo 3. Run queries from: quick_fix_auth.sql
echo 4. Run queries from: database_queries/create_sample_upgrade_data.sql
echo.

echo ========================================
echo After running SQL queries:
echo ========================================
echo.

echo Step 3: Test the system...
echo.
echo Login Credentials:
echo ==================
echo Admin Panel:
echo   Email: admin@test.com
echo   Password: password
echo   URL: /admin/upgrade-requests
echo.
echo Client Panel:
echo   Email: client@test.com  
echo   Password: password
echo   URL: /client/services/100/manage
echo.
echo   Email: jane@test.com
echo   Password: password  
echo   URL: /client/services/101/manage
echo.

echo Step 4: Testing Flow:
echo ===================
echo 1. Login as client (client@test.com)
echo 2. Go to /client/services/100/manage
echo 3. Click "Upgrade Layanan"
echo 4. Submit upgrade request
echo 5. Login as admin (admin@test.com)
echo 6. Go to /admin/upgrade-requests
echo 7. You should see the client's request
echo.

echo ========================================
echo Setup completed!
echo ========================================
echo.
echo Sample data includes:
echo - 2 Client users with services
echo - 1 Admin user
echo - 4 Sample upgrade requests (pending, approved, rejected)
echo.
echo The admin panel will show all client upgrade requests
echo with full client information and service details.
echo.
pause
