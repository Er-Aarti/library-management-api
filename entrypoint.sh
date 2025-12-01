#!/bin/bash

sleep 5

echo "ServerName localhost" >> /etc/apache2/apache2.conf

php artisan config:clear
php artisan route:clear
php artisan view:clear

php artisan key:generate
php artisan config:cache

php artisan migrate --force

php artisan db:seed --class=BookSeeder
php artisan db:seed --class=BookBorrowingSeeder

# Remove or comment this line - avoid recursion
# entrypoint.sh

# Don't run tests here in production (optional)
# php artisan test

apache2-foreground
