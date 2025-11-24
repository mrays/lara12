@echo off
echo Fixing helper functions autoload...

REM Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

REM Regenerate autoload files
composer dump-autoload

echo Helper functions should now be loaded properly.
echo Please test the forgot-password page again.
pause
