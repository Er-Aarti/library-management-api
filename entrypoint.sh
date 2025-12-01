#!/bin/bash

# Wait for DB to be ready (optional if using external DB)
sleep 5

# Clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Run migrations
php artisan migrate --force
entrypoint.sh
# Run seeders
php artisan db:seed --class=BookSeeder
php artisan db:seed --class=BookBorrowingSeeder

# Run unit tests
# php artisan test

# Start Apache in foreground
# Fix Apache ServerName warning
echo "ServerName localhost" >> /etc/apache2/apache2.conf
apache2-foreground
