@echo off
REM Clear all Laravel caches

php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan optimize:clear

echo All caches cleared successfully!
pause
